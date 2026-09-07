<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengaliran;
use App\Models\Pks;
use App\Services\IotPengaliranService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class IotPengaliranApiController extends Controller
{
    protected IotPengaliranService $iotService;

    public function __construct(IotPengaliranService $iotService)
    {
        $this->iotService = $iotService;
    }

    /**
     * POST /api/iot/pengaliran
     * Store pengaliran data from IoT device/gateway.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vol_limbah_dialirkan' => 'nullable|numeric|min:0',
            'volume' => 'nullable|numeric|min:0',
            'vol' => 'nullable|numeric|min:0',
            'flow_volume' => 'nullable|numeric|min:0',
            'debit' => 'nullable|numeric|min:0',
            'vol_limbah_dihasilkan' => 'nullable|numeric|min:0',
            'no_bak' => 'nullable|string|max:50',
            'blok' => 'nullable|string|max:50',
            'flat_bed' => 'nullable|numeric|min:0',
            'luas_area' => 'nullable|numeric|min:0',
            'id_pks' => 'nullable|integer',
            'pks_code' => 'nullable|string|max:50',
            'device_id' => 'nullable|string|max:100',
            'tanggal' => 'nullable|date',
            'jam_mulai' => 'nullable|string',
            'jam_selesai' => 'nullable|string',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi data IoT gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $payload = $request->all();
            $topic = $request->input('topic', 'api/http/iot/pengaliran');
            $forcedPks = $request->input('id_pks');

            // Default to Sei Galuh (id 7) if not given
            $pengaliran = $this->iotService->processAndSave($payload, $topic, $forcedPks ? (int)$forcedPks : null);

            // Load relations
            $pengaliran->load('pks');

            return response()->json([
                'status' => 'success',
                'message' => 'Data pengaliran IoT berhasil disimpan ke database SIMOLI',
                'data' => [
                    'id_pengaliran' => $pengaliran->id_pengaliran,
                    'pks' => [
                        'id_pks' => $pengaliran->pks?->id_pks,
                        'nama' => $pengaliran->pks?->nama,
                        'kode' => $pengaliran->pks?->kode,
                        'akro' => $pengaliran->pks?->akro,
                    ],
                    'tanggal' => $pengaliran->tanggal?->format('Y-m-d'),
                    'jam_mulai' => $pengaliran->jam_mulai,
                    'jam_selesai' => $pengaliran->jam_selesai,
                    'no_bak' => $pengaliran->no_bak,
                    'blok' => $pengaliran->blok,
                    'flat_bed' => $pengaliran->flat_bed,
                    'vol_limbah_dihasilkan' => $pengaliran->vol_limbah_dihasilkan,
                    'vol_limbah_dialirkan' => $pengaliran->vol_limbah_dialirkan,
                    'luas_area' => $pengaliran->luas_area,
                    'rotasi' => $pengaliran->rotasi,
                    'keterangan' => $pengaliran->keterangan,
                    'tags' => $pengaliran->tags,
                    'created_at' => $pengaliran->created_at?->toIso8601String(),
                ],
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data IoT: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/iot/pengaliran/latest
     * Retrieve the most recent IoT pengaliran record (default: PKS Sei Galuh id 7).
     */
    public function latest(Request $request): JsonResponse
    {
        $authUser = $request->attributes->get('auth_user');

        $idPks = $request->input('id_pks');
        if (!$idPks && $authUser && !$authUser->isAdmin() && $authUser->id_pks) {
            $idPks = $authUser->id_pks;
        }
        if (!$idPks) {
            $idPks = config('mqtt.default_pks_id', 7);
        }

        $record = Pengaliran::with('pks')
            ->where('id_pks', $idPks)
            ->orderBy('id_pengaliran', 'desc')
            ->first();

        if (!$record) {
            return response()->json([
                'status' => 'error',
                'message' => 'Belum ada data pengaliran untuk PKS ini',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data pengaliran IoT terbaru berhasil diambil',
            'data' => [
                'id_pengaliran' => $record->id_pengaliran,
                'pks' => [
                    'id_pks' => $record->pks?->id_pks,
                    'nama' => $record->pks?->nama,
                    'kode' => $record->pks?->kode,
                    'akro' => $record->pks?->akro,
                ],
                'tanggal' => $record->tanggal?->format('Y-m-d'),
                'jam_mulai' => $record->jam_mulai,
                'jam_selesai' => $record->jam_selesai,
                'no_bak' => $record->no_bak,
                'blok' => $record->blok,
                'flat_bed' => $record->flat_bed,
                'vol_limbah_dihasilkan' => $record->vol_limbah_dihasilkan,
                'vol_limbah_dialirkan' => $record->vol_limbah_dialirkan,
                'luas_area' => $record->luas_area,
                'rotasi' => $record->rotasi,
                'keterangan' => $record->keterangan,
                'tags' => $record->tags,
                'created_at' => $record->created_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * GET /api/iot/pengaliran
     * List historical IoT pengaliran records.
     */
    public function index(Request $request): JsonResponse
    {
        $authUser = $request->attributes->get('auth_user');

        $idPks = $request->input('id_pks');
        if (!$idPks && $authUser && !$authUser->isAdmin() && $authUser->id_pks) {
            $idPks = $authUser->id_pks;
        }
        if (!$idPks) {
            $idPks = config('mqtt.default_pks_id', 7);
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $limit = (int) $request->input('limit', 20);

        $query = Pengaliran::with('pks')
            ->where('id_pks', $idPks)
            ->orderBy('id_pengaliran', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }

        $records = $query->paginate($limit);

        return response()->json([
            'status' => 'success',
            'data' => $records->items(),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
                'last_page' => $records->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/iot/pengaliran/publish
     * Publish test or custom message to MQTT broker.
     */
    public function publish(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'topic' => 'nullable|string',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter topic dan message diperlukan',
                'errors' => $validator->errors(),
            ], 422);
        }

        $topic = $request->input('topic', 'simoli/seigaluh/pengaliran');
        $message = $request->input('message');

        $published = $this->iotService->publishMqtt($topic, $message);

        if ($published) {
            return response()->json([
                'status' => 'success',
                'message' => "Pesan berhasil dipublikasikan ke topic MQTT [{$topic}]",
                'topic' => $topic,
                'payload' => $message,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mempublikasikan pesan ke MQTT Broker',
        ], 500);
    }

    /**
     * GET /api/iot/status
     * Check MQTT Broker & Database health.
     */
    public function status(): JsonResponse
    {
        $host = config('mqtt.host') ?: env('MQTT_HOST', '127.0.0.1');
        $port = (int) (config('mqtt.port') ?: env('MQTT_PORT', 1883));
        $username = config('mqtt.username') ?: env('MQTT_AUTH_USERNAME', 'simoli');
        $password = config('mqtt.password') ?: env('MQTT_AUTH_PASSWORD', 'Simoli404$!');
        $version = config('mqtt.mqtt_version') ?: MqttClient::MQTT_3_1;

        $mqttConnected = false;
        $mqttError = null;

        try {
            $settings = (new ConnectionSettings)
                ->setUsername($username)
                ->setPassword($password)
                ->setKeepAliveInterval(5)
                ->setConnectTimeout(3);

            $mqtt = new MqttClient($host, $port, 'simoli_health_' . uniqid(), $version);
            $mqtt->connect($settings, true);
            $mqttConnected = true;
            $mqtt->disconnect(false);
        } catch (\Throwable $e) {
            $mqttConnected = false;
            $mqttError = $e->getMessage();
        }

        $pksSeiGaluh = Pks::find(7);
        $latestRecord = Pengaliran::where('id_pks', 7)->latest('id_pengaliran')->first();

        return response()->json([
            'status' => 'success',
            'system' => 'SIMOLI IoT Pengaliran Engine',
            'timestamp' => Carbon::now('Asia/Jakarta')->toIso8601String(),
            'mqtt_broker' => [
                'status' => $mqttConnected ? 'ONLINE' : 'OFFLINE',
                'host' => $host,
                'port' => $port,
                'username' => $username,
                'error' => $mqttError,
                'topics' => config('mqtt.topics'),
            ],
            'database' => [
                'status' => 'CONNECTED',
                'target_pks' => [
                    'id_pks' => $pksSeiGaluh?->id_pks,
                    'nama' => $pksSeiGaluh?->nama,
                    'kode' => $pksSeiGaluh?->kode,
                ],
                'latest_pengaliran' => $latestRecord ? [
                    'id' => $latestRecord->id_pengaliran,
                    'tanggal' => $latestRecord->tanggal?->format('Y-m-d'),
                    'vol_dialirkan' => $latestRecord->vol_limbah_dialirkan,
                    'created_at' => $latestRecord->created_at?->toIso8601String(),
                ] : null,
            ],
        ]);
    }
}
