<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlatBerat;
use App\Models\MonitoringAlatBerat;
use App\Models\Pks;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SyncApiController extends Controller
{
    /**
     * Helper to authenticate user from Bearer token
     */
    private function getAuthenticatedUser(Request $request): ?User
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $decoded = base64_decode($token);
        $parts = explode(':', $decoded);

        if (count($parts) >= 2) {
            return User::with('pks')->find($parts[0]);
        }

        return null;
    }

    /**
     * Pull Master Data & Recent Records (Delta Sync)
     */
    public function pull(Request $request)
    {
        try {
            $user = $this->getAuthenticatedUser($request);
            $idPks = $request->input('id_pks');

            if ($user && $user->isUnit()) {
                $idPks = $user->id_pks;
            }

            $lastSync = $request->input('last_sync');

            // 1. Master PKS
            $pksQuery = Pks::select('id_pks', 'kode', 'nama', 'akro');
            $pksList = $pksQuery->get();

            // 2. Master Alat Berat
            $alatQuery = AlatBerat::query();
            if ($idPks) {
                $alatQuery->where('id_pks', $idPks);
            }
            if ($lastSync) {
                $alatQuery->where('updated_at', '>=', $lastSync);
            }
            $alatList = $alatQuery->orderBy('kode_alat')->get();

            // 3. Master Operators / Mandor
            $userQuery = User::select('ID as id', 'username', 'level_akses', 'id_pks')
                ->whereIn('level_akses', ['operator', 'mandor', 'unit']);
            if ($idPks) {
                $userQuery->where('id_pks', $idPks);
            }
            $usersList = $userQuery->get();

            // 4. Recent Monitoring Records
            $reportsQuery = MonitoringAlatBerat::with(['alatBerat', 'pks']);
            if ($idPks) {
                $reportsQuery->where('id_pks', $idPks);
            }
            if ($lastSync) {
                $reportsQuery->where('updated_at', '>=', $lastSync);
            } else {
                $reportsQuery->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->limit(30);
            }
            $reportsList = $reportsQuery->get();

            return response()->json([
                'success' => true,
                'message' => 'Pull data master berhasil',
                'server_time' => Carbon::now('Asia/Jakarta')->toIso8601String(),
                'data' => [
                    'pks' => $pksList,
                    'alat_berat' => $alatList,
                    'operators' => $usersList,
                    'recent_reports' => $reportsList,
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Push Batch Offline Transactions from Client
     */
    public function push(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.uuid' => 'required|string',
            'items.*.alat_berat_id' => 'required',
            'items.*.tanggal' => 'required',
            'items.*.operator' => 'required',
            'items.*.kegiatan' => 'required',
        ]);

        $user = $this->getAuthenticatedUser($request);
        $defaultPksId = $user ? $user->id_pks : null;
        $deviceId = $request->header('X-Device-ID') ?? $request->input('device_id', 'unknown-device');

        $uploadDir = public_path('gallery');
        if (!File::exists($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }

        $syncedUuids = [];
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($request->input('items') as $index => $item) {
                $uuid = $item['uuid'] ?? (string) Str::uuid();
                $idPks = $item['id_pks'] ?? $defaultPksId;

                // Handle Photo Sebelum (Base64 or existing filename)
                $fotoSebelumName = $item['foto_sebelum'] ?? null;
                if (!empty($item['foto_sebelum_base64']) && str_starts_with($item['foto_sebelum_base64'], 'data:image')) {
                    $fotoSebelumName = $this->saveBase64Image($item['foto_sebelum_base64'], 'sebelum', $uploadDir);
                }

                // Handle Photo Sesudah (Base64 or existing filename)
                $fotoSesudahName = $item['foto_sesudah'] ?? null;
                if (!empty($item['foto_sesudah_base64']) && str_starts_with($item['foto_sesudah_base64'], 'data:image')) {
                    $fotoSesudahName = $this->saveBase64Image($item['foto_sesudah_base64'], 'sesudah', $uploadDir);
                }

                // Bed calculation
                $flatBed = (int) ($item['flat_bed'] ?? 0);
                $longBed = (int) ($item['long_bed'] ?? 0);
                $jumlahBed = $flatBed + $longBed;
                if ($jumlahBed === 0 && !empty($item['jumlah_bed'])) {
                    $jumlahBed = (int) $item['jumlah_bed'];
                }

                // Coordinates
                $latAwal = $item['latitude_awal'] ?? $item['latitude'] ?? null;
                $longAwal = $item['longitude_awal'] ?? $item['longitude'] ?? null;
                $latAkhir = $item['latitude_akhir'] ?? null;
                $longAkhir = $item['longitude_akhir'] ?? null;

                // HM formatting
                $hmAwal = !empty($item['hm_awal']) ? $this->formatTimestamp($item['hm_awal'], $item['tanggal']) : null;
                $hmAkhir = !empty($item['hm_akhir']) ? $this->formatTimestamp($item['hm_akhir'], $item['tanggal']) : null;

                $totalHm = (float) ($item['total_hm'] ?? 0);
                if ($totalHm <= 0 && $hmAwal && $hmAkhir) {
                    $cStart = Carbon::parse($hmAwal);
                    $cEnd = Carbon::parse($hmAkhir);
                    if ($cEnd->lessThan($cStart)) $cEnd->addDay();
                    $totalHm = round(abs($cStart->diffInMinutes($cEnd)) / 60, 2);
                }

                $record = MonitoringAlatBerat::updateOrCreate(
                    ['uuid' => $uuid],
                    [
                        'id_pks' => $idPks,
                        'alat_berat_id' => $item['alat_berat_id'],
                        'tanggal' => $item['tanggal'],
                        'operator' => $item['operator'],
                        'kegiatan' => $item['kegiatan'],
                        'lokasi_blok' => $item['lokasi_blok'] ?? null,
                        'no_bak' => $item['no_bak'] ?? null,
                        'flat_bed' => $flatBed,
                        'long_bed' => $longBed,
                        'jumlah_bed' => $jumlahBed,
                        'latitude' => $latAwal,
                        'longitude' => $longAwal,
                        'latitude_awal' => $latAwal,
                        'longitude_awal' => $longAwal,
                        'latitude_akhir' => $latAkhir,
                        'longitude_akhir' => $longAkhir,
                        'hm_awal' => $hmAwal,
                        'hm_akhir' => $hmAkhir,
                        'total_hm' => $totalHm,
                        'bbm_liter' => (float) ($item['bbm_liter'] ?? 0),
                        'kondisi_alat' => $item['kondisi_alat'] ?? 'Baik',
                        'foto' => $fotoSebelumName ?? $fotoSesudahName,
                        'foto_sebelum' => $fotoSebelumName,
                        'foto_sesudah' => $fotoSesudahName,
                        'catatan' => $item['catatan'] ?? null,
                        'sync_version' => ($item['sync_version'] ?? 0) + 1,
                        'client_created_at' => !empty($item['client_created_at']) ? Carbon::parse($item['client_created_at']) : Carbon::now('Asia/Jakarta'),
                        'synced_at' => Carbon::now('Asia/Jakarta'),
                        'device_id' => $item['device_id'] ?? $deviceId,
                    ]
                );

                $syncedUuids[] = $uuid;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($syncedUuids) . ' data berhasil disinkronkan ke server.',
                'synced_uuids' => $syncedUuids,
                'synced_count' => count($syncedUuids),
                'server_time' => Carbon::now('Asia/Jakarta')->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Sinkronisasi gagal: ' . $e->getMessage(),
                'synced_uuids' => $syncedUuids,
            ], 500);
        }
    }

    /**
     * Save base64 image to disk and return file name
     */
    private function saveBase64Image(string $base64String, string $prefix, string $destinationDir): string
    {
        $imageParts = explode(';base64,', $base64String);
        $imageTypeAux = explode('image/', $imageParts[0]);
        $imageType = count($imageTypeAux) > 1 ? $imageTypeAux[1] : 'jpeg';
        if ($imageType === 'jpg') $imageType = 'jpeg';

        $imageBase64 = base64_decode($imageParts[1]);
        $fileName = $prefix . '_' . time() . '_' . rand(1000, 9999) . '.' . ($imageType === 'jpeg' ? 'jpg' : $imageType);
        $filePath = $destinationDir . '/' . $fileName;

        File::put($filePath, $imageBase64);

        return $fileName;
    }

    /**
     * Format timestamp helper
     */
    private function formatTimestamp($val, $tanggal): ?string
    {
        if (empty($val)) return null;

        // If time format HH:MM
        if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', trim($val))) {
            $time = trim($val);
            if (strlen($time) === 5) $time .= ':00';
            return $tanggal . ' ' . $time;
        }

        try {
            return Carbon::parse($val)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}
