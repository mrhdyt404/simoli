<?php

namespace App\Console\Commands;

use App\Services\IotPengaliranService;
use Illuminate\Console\Command;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
use Throwable;

class MqttSubscribePengaliranCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:subscribe-pengaliran 
                            {--topic= : Custom topic to subscribe to}
                            {--host= : Override MQTT broker host}
                            {--port= : Override MQTT broker port}
                            {--username= : Override MQTT username}
                            {--password= : Override MQTT password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to MQTT broker topics and persist incoming IoT pengaliran data to database';

    protected IotPengaliranService $iotService;

    public function __construct(IotPengaliranService $iotService)
    {
        parent::__construct();
        $this->iotService = $iotService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $host = $this->option('host') ?: (config('mqtt.host') ?: env('MQTT_HOST', '127.0.0.1'));
        $port = (int) ($this->option('port') ?: (config('mqtt.port') ?: env('MQTT_PORT', 1883)));
        $username = $this->option('username') ?: (config('mqtt.username') ?: env('MQTT_AUTH_USERNAME', 'simoli'));
        $password = $this->option('password') ?: (config('mqtt.password') ?: env('MQTT_AUTH_PASSWORD', 'Simoli404$!'));
        $version = config('mqtt.mqtt_version') ?: MqttClient::MQTT_3_1;
        $clientId = 'simoli_sub_' . getmypid() . '_' . substr(md5(uniqid()), 0, 6);

        $customTopic = $this->option('topic');
        $topics = $customTopic ? [$customTopic] : [
            config('mqtt.topics.sei_galuh', 'simoli/seigaluh/pengaliran'),
            config('mqtt.topics.pengaliran', 'simoli/+/pengaliran'),
            config('mqtt.topics.test', 'simoli/test'),
        ];

        // Remove duplicates
        $topics = array_unique($topics);

        $this->info("==================================================");
        $this->info(" SIMOLI - MQTT IoT Pengaliran Subscriber Daemon");
        $this->info(" Target Unit: PKS SEI GALUH & Unit Lainnya");
        $this->info(" Broker Host: {$host}:{$port}");
        $this->info(" User: {$username}");
        $this->info(" Client ID: {$clientId}");
        $this->info(" Subscribed Topics: " . implode(', ', $topics));
        $this->info("==================================================");
        $this->info("Menghubungkan ke MQTT Broker Mosquitto...");

        while (true) {
            try {
                $settings = (new ConnectionSettings)
                    ->setKeepAliveInterval((int) config('mqtt.keep_alive', 60))
                    ->setConnectTimeout((int) config('mqtt.timeout', 10))
                    ->setUseTls(false)
                    ->setTlsVerifyPeer(false);

                if (!empty($username)) {
                    $settings = $settings->setUsername($username)->setPassword($password);
                }

                $mqtt = new MqttClient($host, $port, $clientId, $version);
                $mqtt->connect($settings, true);

                $this->info("[" . date('Y-m-d H:i:s') . "] Terhubung ke MQTT Broker! Menunggu data IoT...");

                foreach ($topics as $topic) {
                    $mqtt->subscribe($topic, function (string $topic, string $message) {
                        $this->line("");
                        $this->info("[" . date('Y-m-d H:i:s') . "] [INCOMING MQTT] Topic: <fg=cyan>{$topic}</>");
                        $this->line("Payload: <fg=yellow>{$message}</>");

                        try {
                            $pengaliran = $this->iotService->processAndSave($message, $topic);

                            $this->info("==> <fg=green;options=bold>BERHASIL DISIMPAN KE DATABASE!</>");
                            $this->line("    ID Pengaliran: {$pengaliran->id_pengaliran}");
                            $this->line("    PKS: {$pengaliran->pks?->nama} (ID: {$pengaliran->id_pks})");
                            $this->line("    Tanggal: " . ($pengaliran->tanggal?->format('Y-m-d') ?? '-'));
                            $this->line("    Volume Dialirkan: {$pengaliran->vol_limbah_dialirkan} m³");
                            $this->line("    Volume Dihasilkan: {$pengaliran->vol_limbah_dihasilkan} m³");
                            $this->line("    Bak / Blok / Bed: {$pengaliran->no_bak} / {$pengaliran->blok} / {$pengaliran->flat_bed}");
                            $this->line("    Tags: {$pengaliran->tags}");
                        } catch (Throwable $e) {
                            $this->error("==> [ERROR] Gagal memproses & menyimpan payload: " . $e->getMessage());
                        }
                    }, 0);
                }

                // Loop listening for messages
                $mqtt->loop(true);

            } catch (Throwable $e) {
                $this->error("[" . date('Y-m-d H:i:s') . "] MQTT Connection Error: " . $e->getMessage());
                $this->warn("Mencoba menghubungkan kembali dalam 5 detik...");
                sleep(5);
            }
        }
    }
}
