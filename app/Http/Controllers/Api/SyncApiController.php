<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlatBerat;
use App\Models\MonitoringAlatBerat;
use App\Models\Pengaliran;
use App\Models\Pks;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SyncApiController extends Controller
{
    /**
     * Helper to authenticate user from Bearer token or session
     */
    private function getAuthenticatedUser(Request $request): ?User
    {
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = str_replace('Bearer ', '', $authHeader);
            $decoded = base64_decode($token, true);
            if ($decoded !== false) {
                $parts = explode(':', $decoded);
                if (count($parts) >= 2) {
                    $user = User::with('pks')->find($parts[0]);
                    if ($user && $user->username === $parts[1]) {
                        return $user;
                    }
                }
            }
        }

        return Auth::user() ?? Auth::guard('web')->user();
    }

    private function numericSum($items, string $field): float|int
    {
        return $items->sum(function ($item) use ($field) {
            $value = $item->{$field} ?? 0;
            if (is_numeric($value)) {
                return $value + 0;
            }
            $sanitized = preg_replace('/[^0-9.-]/', '', (string) $value);
            return is_numeric($sanitized) ? $sanitized + 0 : 0;
        });
    }

    private function getGrafikData(?int $idPks, array $params): array
    {
        $periode    = $params['periode'] ?? 'harian';
        $tahun      = $params['tahun'] ?? date('Y');
        $bulan      = $params['bulan'] ?? date('m');
        $jenis      = $params['jenis'] ?? 'flat_bed';
        $nilai      = $params['nilai'] ?? null;
        $tglMulai   = $params['tgl_mulai'] ?? null;
        $tglSelesai = $params['tgl_selesai'] ?? null;

        $pilihanQuery = Pengaliran::query();
        if ($idPks) {
            $pilihanQuery->where('id_pks', $idPks);
        }

        if ($jenis == 'blok') {
            $pilihan = (clone $pilihanQuery)
                ->whereNotNull('blok')
                ->where('blok', '!=', '')
                ->distinct()
                ->orderBy('blok')
                ->pluck('blok');
        } elseif ($jenis == 'no_bak') {
            $pilihan = (clone $pilihanQuery)
                ->whereNotNull('no_bak')
                ->where('no_bak', '!=', '')
                ->distinct()
                ->orderBy('no_bak')
                ->pluck('no_bak');
        } else {
            $pilihan = (clone $pilihanQuery)
                ->whereNotNull('flat_bed')
                ->where('flat_bed', '!=', '')
                ->distinct()
                ->orderBy('flat_bed')
                ->pluck('flat_bed');
        }

        $baseQuery = Pengaliran::query();
        if ($idPks) {
            $baseQuery->where('id_pks', $idPks);
        }

        if ($nilai !== null && $nilai !== '' && $nilai !== 'Semua') {
            if ($jenis == 'blok') {
                $baseQuery->where('blok', $nilai);
            } elseif ($jenis == 'no_bak') {
                $baseQuery->where('no_bak', $nilai);
            } else {
                $baseQuery->where('flat_bed', $nilai);
            }
        }

        $labels = [];
        $volumeDialirkan = [];
        $volumeDihasilkan = [];

        if ($periode === 'custom') {
            if (!$tglMulai) $tglMulai = Carbon::now()->startOfMonth()->toDateString();
            if (!$tglSelesai) $tglSelesai = Carbon::now()->toDateString();

            $start = Carbon::parse($tglMulai)->startOfDay();
            $end   = Carbon::parse($tglSelesai)->endOfDay();

            $curr = $start->copy();
            while ($curr->lte($end)) {
                $dateStr = $curr->toDateString();
                $labels[] = $curr->format('d/m/Y');

                $q = (clone $baseQuery)->whereDate('tanggal', $dateStr);
                $volumeDialirkan[]  = round((float)$this->numericSum($q->get(), 'vol_limbah_dialirkan'), 2);
                $volumeDihasilkan[] = round((float)$this->numericSum($q->get(), 'vol_limbah_dihasilkan'), 2);

                $curr->addDay();
            }
        } elseif ($periode === 'semua') {
            $records = (clone $baseQuery)
                ->selectRaw('YEAR(tanggal) as yr, MONTH(tanggal) as mo')
                ->whereNotNull('tanggal')
                ->groupBy(DB::raw('YEAR(tanggal)'), DB::raw('MONTH(tanggal)'))
                ->orderBy(DB::raw('YEAR(tanggal)'))
                ->orderBy(DB::raw('MONTH(tanggal)'))
                ->get();

            $namaBulanShort = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
            ];

            if ($records->isEmpty()) {
                $labels[] = date('M Y');
                $volumeDialirkan[]  = 0;
                $volumeDihasilkan[] = 0;
            } else {
                foreach ($records as $rec) {
                    $yr = $rec->yr;
                    $mo = $rec->mo;
                    $labels[] = ($namaBulanShort[(int)$mo] ?? $mo) . ' ' . $yr;
                    $q = (clone $baseQuery)->whereYear('tanggal', $yr)->whereMonth('tanggal', $mo);
                    $volumeDialirkan[]  = round((float)$this->numericSum($q->get(), 'vol_limbah_dialirkan'), 2);
                    $volumeDihasilkan[] = round((float)$this->numericSum($q->get(), 'vol_limbah_dihasilkan'), 2);
                }
            }
        } elseif ($periode === 'tahunan') {
            $yearQuery = Pengaliran::query();
            if ($idPks) {
                $yearQuery->where('id_pks', $idPks);
            }
            $years = $yearQuery->whereNotNull('tanggal')
                ->selectRaw('DISTINCT YEAR(tanggal) as yr')
                ->pluck('yr')
                ->map(fn($y) => (int)$y)
                ->filter()
                ->toArray();

            if (empty($years)) {
                $years = [(int)date('Y') - 1, (int)date('Y')];
            } else {
                sort($years);
            }

            $minYear = min($years);
            $maxYear = max($years);
            if ($minYear == $maxYear) {
                $minYear = $maxYear - 1;
            }

            $allYears = range($minYear, $maxYear);

            foreach ($allYears as $yr) {
                $labels[] = (string)$yr;
                $q = (clone $baseQuery)->whereYear('tanggal', $yr);
                $volumeDialirkan[]  = round((float)$this->numericSum($q->get(), 'vol_limbah_dialirkan'), 2);
                $volumeDihasilkan[] = round((float)$this->numericSum($q->get(), 'vol_limbah_dihasilkan'), 2);
            }
        } elseif ($periode === 'bulanan') {
            $namaBulan = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
            ];
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = $namaBulan[$m];
                $q = (clone $baseQuery)->whereYear('tanggal', $tahun)->whereMonth('tanggal', $m);
                $volumeDialirkan[]  = round((float)$this->numericSum($q->get(), 'vol_limbah_dialirkan'), 2);
                $volumeDihasilkan[] = round((float)$this->numericSum($q->get(), 'vol_limbah_dihasilkan'), 2);
            }
        } elseif ($periode === 'mingguan') {
            $jumlahHari = Carbon::create($tahun, $bulan)->daysInMonth;
            $weeks = [
                'Minggu 1 (1-7)'   => [1, 7],
                'Minggu 2 (8-14)'  => [8, 14],
                'Minggu 3 (15-21)' => [15, 21],
                'Minggu 4 (22-28)' => [22, 28],
            ];
            if ($jumlahHari > 28) {
                $weeks['Minggu 5 (29-'.$jumlahHari.')'] = [29, $jumlahHari];
            }

            foreach ($weeks as $wName => [$dStart, $dEnd]) {
                $labels[] = $wName;
                $q = (clone $baseQuery)->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->whereRaw('DAY(tanggal) BETWEEN ? AND ?', [$dStart, $dEnd]);
                $volumeDialirkan[]  = round((float)$this->numericSum($q->get(), 'vol_limbah_dialirkan'), 2);
                $volumeDihasilkan[] = round((float)$this->numericSum($q->get(), 'vol_limbah_dihasilkan'), 2);
            }
        } else {
            // Harian
            $jumlahHari = Carbon::create($tahun, $bulan)->daysInMonth;
            for ($i = 1; $i <= $jumlahHari; $i++) {
                $labels[] = (string)$i;
                $q = (clone $baseQuery)->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->whereDay('tanggal', $i);
                $volumeDialirkan[]  = round((float)$this->numericSum($q->get(), 'vol_limbah_dialirkan'), 2);
                $volumeDihasilkan[] = round((float)$this->numericSum($q->get(), 'vol_limbah_dihasilkan'), 2);
            }
        }

        return [
            'labels'           => $labels,
            'labelHari'        => $labels,
            'volumeDialirkan'  => $volumeDialirkan,
            'volumeGrafik'     => $volumeDialirkan,
            'volumeDihasilkan' => $volumeDihasilkan,
            'pilihan'          => $pilihan,
        ];
    }

    /**
     * Pull Master Data & Recent Records (Delta Sync) with Timeframe Filtering
     */
    public function pull(Request $request)
    {
        try {
            $user = $this->getAuthenticatedUser($request);

            $idPks = $request->input('id_pks');
            if (!$idPks && $user && ($user->isUnit() || $user->isFieldUser() || $user->id_pks)) {
                $idPks = $user->id_pks;
            }

            if (!$idPks) {
                $idPks = Pks::value('id_pks') ?? 1;
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
            } elseif ($request->has('tgl_mulai') && $request->has('tgl_selesai')) {
                $reportsQuery->whereBetween('tanggal', [$request->input('tgl_mulai'), $request->input('tgl_selesai')]);
            } elseif ($request->has('tahun') && $request->has('bulan')) {
                $reportsQuery->whereYear('tanggal', $request->input('tahun'))->whereMonth('tanggal', $request->input('bulan'));
            } else {
                $reportsQuery->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->limit(30);
            }
            $reportsList = $reportsQuery->get();

            // 5. Compute Grafik Volume Data based on filters
            $grafikData = $this->getGrafikData($idPks ? (int)$idPks : null, [
                'periode'     => $request->input('periode', 'harian'),
                'tahun'       => $request->input('tahun', date('Y')),
                'bulan'       => $request->input('bulan', date('m')),
                'jenis'       => $request->input('jenis', 'flat_bed'),
                'nilai'       => $request->input('nilai'),
                'tgl_mulai'   => $request->input('tgl_mulai'),
                'tgl_selesai' => $request->input('tgl_selesai'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pull data master & filter berhasil',
                'server_time' => Carbon::now('Asia/Jakarta')->toIso8601String(),
                'data' => array_merge([
                    'pks' => $pksList,
                    'alat_berat' => $alatList,
                    'operators' => $usersList,
                    'recent_reports' => $reportsList,
                    'grafik_volume' => $grafikData,
                ], $grafikData)
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
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Silakan login terlebih dahulu.',
            ], 401);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.uuid' => 'required|string',
            'items.*.alat_berat_id' => 'required',
            'items.*.tanggal' => 'required',
            'items.*.kegiatan' => 'required',
        ]);

        $defaultPksId = $user->id_pks;
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
                $idPks = ($user->isUnit() || $user->isFieldUser()) && $user->id_pks ? $user->id_pks : ($item['id_pks'] ?? $defaultPksId);

                // Enforce authenticated user identity: non-admin users cannot spoof other operator names
                $operatorName = ($user->isAdmin() && !empty($item['operator'])) ? $item['operator'] : $user->username;

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
                        'operator' => $operatorName,
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

            // Kirim notifikasi WhatsApp ke Asisten Unit PKS untuk laporan yang baru tersinkronkan
            try {
                $waService = app(\App\Services\SidobeWaService::class);
                $syncedLogs = MonitoringAlatBerat::with(['pks', 'alatBerat'])->whereIn('uuid', $syncedUuids)->get();
                foreach ($syncedLogs as $sLog) {
                    $waService->sendOperatorInputNotification($sLog);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('[Sidobe WA Sync Notification Error] ' . $e->getMessage());
            }

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

    /**
     * Check if operator / unit has inputted work logs today (PWA Notification API)
     */
    public function checkTodayInput(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $todayFormatted = $now->translatedFormat('l, d F Y');
        $currentHour = (int) $now->format('H');

        $query = MonitoringAlatBerat::with('alatBerat')
            ->whereDate('tanggal', $today);

        if ($user->id_pks) {
            $query->where('id_pks', $user->id_pks);
        }

        if ($user->isFieldUser() && $user->isOperator()) {
            $query->where('operator', $user->username);
        }

        $todayLogs = $query->orderBy('id', 'desc')->get();
        $totalToday = $todayLogs->count();
        $hasInputToday = $totalToday > 0;

        $uncompletedCount = $todayLogs->filter(function ($item) {
            return empty($item->foto_sesudah) || empty($item->hm_akhir);
        })->count();

        $latestLog = $todayLogs->first();

        return response()->json([
            'success' => true,
            'has_input_today' => $hasInputToday,
            'total_today' => $totalToday,
            'uncompleted_shifts' => $uncompletedCount,
            'completed_shifts' => $totalToday - $uncompletedCount,
            'today_date' => $today,
            'today_formatted' => $todayFormatted,
            'current_hour' => $currentHour,
            'operator_name' => $user->username,
            'pks_name' => $user->pks ? $user->pks->nama : 'PKS Unit',
            'latest_log' => $latestLog ? [
                'id' => $latestLog->id,
                'alat' => $latestLog->alatBerat ? $latestLog->alatBerat->kode_alat : 'Unit',
                'kegiatan' => $latestLog->kegiatan,
                'created_at' => $latestLog->created_at ? $latestLog->created_at->format('H:i') : null,
                'is_completed' => $latestLog->isCompleted(),
            ] : null,
            'message' => $hasInputToday
                ? ($uncompletedCount > 0
                    ? "Terdapat {$uncompletedCount} shift yang belum diselesaikan hari ini."
                    : "Laporan kerja hari ini telah lengkap ({$totalToday} laporan).")
                : "Anda belum menginput data laporan kerja operasional hari ini ({$todayFormatted})."
        ]);
    }
}
