<?php

namespace App\Services;

use App\Models\Pengaliran;
use App\Models\Pks;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class IotPengaliranService
{
    /**
     * Default PKS ID (Sei Galuh = 7)
     */
    protected int $defaultPksId;

    public function __construct()
    {
        $this->defaultPksId = (int) config('mqtt.default_pks_id', 7);
    }

    /**
     * Process and save IoT payload (from HTTP request or MQTT subscriber).
     *
     * @param array|string $payload
     * @param string|null $topic
     * @param int|null $forcedPksId
     * @return Pengaliran
     */
    public function processAndSave(array|string $payload, ?string $topic = null, ?int $forcedPksId = null): Pengaliran
    {
        // Parse payload if JSON string
        if (is_string($payload)) {
            $decoded = json_decode($payload, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $data = $decoded;
            } else {
                // If it's a simple raw text/number payload
                $data = [
                    'raw_message' => $payload,
                    'vol_limbah_dialirkan' => is_numeric($payload) ? (float)$payload : null,
                ];
            }
        } else {
            $data = $payload;
        }

        // Determine PKS ID
        $idPks = $forcedPksId ?? $this->resolvePksId($data, $topic);

        // Normalize & map fields
        $attributes = $this->mapPayloadToAttributes($data, $idPks, $topic);

        // Save to Database
        $pengaliran = Pengaliran::create($attributes);

        Log::info('IoT Pengaliran data saved successfully', [
            'id_pengaliran' => $pengaliran->id_pengaliran,
            'id_pks' => $pengaliran->id_pks,
            'vol_dialirkan' => $pengaliran->vol_limbah_dialirkan,
            'topic' => $topic,
        ]);

        return $pengaliran;
    }

    /**
     * Resolve PKS ID from data or MQTT topic.
     */
    public function resolvePksId(array $data, ?string $topic = null): int
    {
        // 1. Direct ID in data
        if (!empty($data['id_pks']) && is_numeric($data['id_pks'])) {
            return (int) $data['id_pks'];
        }
        if (!empty($data['pks_id']) && is_numeric($data['pks_id'])) {
            return (int) $data['pks_id'];
        }

        // 2. Search by code / name / akronim in data
        $identifier = $data['kode_pks'] ?? $data['pks_code'] ?? $data['pks'] ?? $data['akro'] ?? null;
        if (!empty($identifier) && is_string($identifier)) {
            $found = Pks::where('kode', $identifier)
                ->orWhere('akro', strtoupper($identifier))
                ->orWhere('nama', 'LIKE', '%' . $identifier . '%')
                ->first();
            if ($found) {
                return (int) $found->id_pks;
            }
        }

        // 3. Resolve from MQTT topic name (e.g. simoli/seigaluh/pengaliran or simoli/SGH/pengaliran)
        if (!empty($topic)) {
            $lowerTopic = strtolower($topic);
            if (str_contains($lowerTopic, 'seigaluh') || str_contains($lowerTopic, 'sei_galuh') || str_contains($lowerTopic, 'sgh')) {
                return 7; // Sei Galuh
            }
            if (str_contains($lowerTopic, 'seigaro') || str_contains($lowerTopic, 'sgo')) {
                return 3;
            }
            if (str_contains($lowerTopic, 'tanahputih') || str_contains($lowerTopic, 'tpu')) {
                return 1;
            }
            if (str_contains($lowerTopic, 'tanjungmedan') || str_contains($lowerTopic, 'tme')) {
                return 2;
            }
            if (str_contains($lowerTopic, 'seipagar') || str_contains($lowerTopic, 'spa')) {
                return 4;
            }
            if (str_contains($lowerTopic, 'seibuatan') || str_contains($lowerTopic, 'sbt')) {
                return 5;
            }
            if (str_contains($lowerTopic, 'lubukdalam') || str_contains($lowerTopic, 'lda')) {
                return 6;
            }
            if (str_contains($lowerTopic, 'tandun') || str_contains($lowerTopic, 'tan')) {
                return 8;
            }
            if (str_contains($lowerTopic, 'terantam') || str_contains($lowerTopic, 'ter')) {
                return 9;
            }
            if (str_contains($lowerTopic, 'seitapung') || str_contains($lowerTopic, 'sta')) {
                return 10;
            }
            if (str_contains($lowerTopic, 'seirokan') || str_contains($lowerTopic, 'sro')) {
                return 11;
            }
            if (str_contains($lowerTopic, 'seiintan') || str_contains($lowerTopic, 'sin')) {
                return 12;
            }
        }

        // Default to PKS Sei Galuh (id 7)
        return $this->defaultPksId;
    }

    /**
     * Map IoT incoming keys to database columns in 'pengaliran'.
     */
    protected function mapPayloadToAttributes(array $data, int $idPks, ?string $topic = null): array
    {
        $now = Carbon::now('Asia/Jakarta');

        // Date resolution
        $tanggal = null;
        if (!empty($data['tanggal'])) {
            $tanggal = Carbon::parse($data['tanggal'])->format('Y-m-d');
        } elseif (!empty($data['timestamp']) || !empty($data['time']) || !empty($data['datetime'])) {
            $ts = $data['timestamp'] ?? $data['time'] ?? $data['datetime'];
            $tanggal = Carbon::parse($ts)->setTimezone('Asia/Jakarta')->format('Y-m-d');
        } else {
            $tanggal = $now->format('Y-m-d');
        }

        // Time resolution
        $jamMulai = $data['jam_mulai'] ?? $data['start_time'] ?? null;
        $jamSelesai = $data['jam_selesai'] ?? $data['end_time'] ?? null;

        if (!$jamMulai) {
            $jamMulai = $now->copy()->subHours(1)->format('H:i:s');
        }
        if (!$jamSelesai) {
            $jamSelesai = $now->format('H:i:s');
        }

        // Volume dialirkan (sensor flow value)
        $volDialirkan = $data['vol_limbah_dialirkan'] 
            ?? $data['volume_dialirkan'] 
            ?? $data['vol_dialirkan'] 
            ?? $data['volume'] 
            ?? $data['vol'] 
            ?? $data['flow_volume'] 
            ?? $data['total_volume'] 
            ?? $data['total_flow'] 
            ?? $data['debit'] 
            ?? 0;

        // Volume dihasilkan (limbah masuk / produced)
        $volDihasilkan = $data['vol_limbah_dihasilkan'] 
            ?? $data['volume_dihasilkan'] 
            ?? $data['vol_dihasilkan'] 
            ?? $data['produced_volume'] 
            ?? null;

        if ($volDihasilkan === null) {
            // Default reasonable estimate or match dialirkan
            $volDihasilkan = round((float)$volDialirkan * 0.95);
        }

        // Bak, Blok, Bed, Luas
        $noBak = (string) ($data['no_bak'] ?? $data['bak'] ?? $data['bak_no'] ?? $data['tank'] ?? '1');
        $blok = (string) ($data['blok'] ?? $data['block'] ?? $data['lokasi_blok'] ?? 'A18');
        $flatBed = (int) ($data['flat_bed'] ?? $data['flatbed'] ?? $data['bed'] ?? 420);
        $luasArea = (float) ($data['luas_area'] ?? $data['luas'] ?? $data['area'] ?? 7.0);
        $rotasi = (string) ($data['rotasi'] ?? $data['rotation'] ?? '1');

        // Tags & Description
        $device = $data['device_id'] ?? $data['device'] ?? $data['sensor_id'] ?? 'SGH_IOT';
        $tags = $data['tags'] ?? ('IOT_' . strtoupper(substr(str_replace([' ', '-'], '_', $device), 0, 15)));
        $tags = substr($tags, 0, 25);

        $extraDetails = [];
        if (!empty($data['device_id'])) {
            $extraDetails[] = "Device: {$data['device_id']}";
        }
        if ($topic) {
            $extraDetails[] = "MQTT Topic: {$topic}";
        }

        $keterangan = $data['keterangan'] 
            ?? $data['notes'] 
            ?? $data['description'] 
            ?? 'Pengaliran IoT Otomatis PKS Sei Galuh';

        if (!empty($extraDetails)) {
            $keterangan .= ' [' . implode(', ', $extraDetails) . ']';
        }

        return [
            'tanggal' => $tanggal,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'no_bak' => substr($noBak, 0, 25),
            'blok' => substr($blok, 0, 20),
            'flat_bed' => (int) $flatBed,
            'vol_limbah_dihasilkan' => (int) round((float) $volDihasilkan),
            'vol_limbah_dialirkan' => (int) round((float) $volDialirkan),
            'luas_area' => (int) round((float) $luasArea),
            'rotasi' => substr($rotasi, 0, 25),
            'keterangan' => $keterangan,
            'id_pks' => $idPks,
            'tags' => $tags,
            'foto' => $data['foto'] ?? null,
        ];
    }

    /**
     * Publish a message to MQTT broker.
     */
    public function publishMqtt(string $topic, array|string $payload, int $qualityOfService = 0, bool $retain = false): bool
    {
        $message = is_array($payload) ? json_encode($payload) : $payload;

        try {
            $host = config('mqtt.host') ?: env('MQTT_HOST', '127.0.0.1');
            $port = (int) (config('mqtt.port') ?: env('MQTT_PORT', 1883));
            $username = config('mqtt.username') ?: env('MQTT_AUTH_USERNAME', 'simoli');
            $password = config('mqtt.password') ?: env('MQTT_AUTH_PASSWORD', 'Simoli404$!');
            $version = config('mqtt.mqtt_version') ?: MqttClient::MQTT_3_1;
            $clientId = 'simoli_pub_' . uniqid();

            $settings = (new ConnectionSettings)
                ->setUsername($username)
                ->setPassword($password)
                ->setKeepAliveInterval((int) config('mqtt.keep_alive', 60))
                ->setConnectTimeout((int) config('mqtt.timeout', 10));

            $mqtt = new MqttClient($host, $port, $clientId, $version);
            $mqtt->connect($settings, true);
            $mqtt->publish($topic, $message, $qualityOfService, $retain);
            $mqtt->disconnect(false);

            Log::info("MQTT Published to [{$topic}]: {$message}");
            return true;
        } catch (\Throwable $e) {
            Log::error("MQTT Publish Error: " . $e->getMessage(), ['exception' => $e]);
            return false;
        }
    }

    /**
     * Get latest IoT records for PKS.
     */
    public function getLatestRecords(int $idPks = 7, int $limit = 20)
    {
        return Pengaliran::with('pks')
            ->where('id_pks', $idPks)
            ->where(function ($q) {
                $q->where('tags', 'LIKE', '%IOT%')
                  ->orWhere('keterangan', 'LIKE', '%IoT%')
                  ->orWhereNotNull('id_pengaliran');
            })
            ->orderBy('id_pengaliran', 'desc')
            ->limit($limit)
            ->get();
    }
}
