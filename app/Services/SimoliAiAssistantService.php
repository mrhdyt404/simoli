<?php

namespace App\Services;

use App\Models\Pks;
use App\Models\Pengaliran;
use App\Models\Pemeliharaan;
use App\Models\AlatBerat;
use App\Models\MonitoringAlatBerat;
use App\Models\Rencana;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SimoliAiAssistantService
{
    protected SidobeWaService $waService;
    protected ?string $geminiApiKey;
    protected string $geminiModel;

    public static array $bulanMap = [
        'januari' => 1, 'jan' => 1, 'january' => 1,
        'februari' => 2, 'feb' => 2, 'february' => 2,
        'maret' => 3, 'mar' => 3, 'march' => 3,
        'april' => 4, 'apr' => 4,
        'mei' => 5, 'may' => 5,
        'juni' => 6, 'jun' => 6, 'june' => 6,
        'juli' => 7, 'jul' => 7, 'july' => 7,
        'agustus' => 8, 'agu' => 8, 'ags' => 8, 'aug' => 8, 'august' => 8,
        'september' => 9, 'sep' => 9, 'sept' => 9,
        'oktober' => 10, 'okt' => 10, 'oct' => 10, 'october' => 10,
        'november' => 11, 'nov' => 11,
        'desember' => 12, 'des' => 12, 'dec' => 12, 'december' => 12,
    ];

    public function __construct(SidobeWaService $waService)
    {
        $this->waService = $waService;
        $this->geminiApiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
        $this->geminiModel = config('services.gemini.model', env('GEMINI_MODEL', 'gemini-1.5-flash'));
    }

    /**
     * Helper Penjumlahan Numerik Aman (menghindari unsupported operand type jika string tersimpan)
     */
    public static function numericSum($items, string $field): float|int
    {
        return $items->sum(function ($item) use ($field) {
            $value = is_array($item) ? ($item[$field] ?? 0) : ($item->{$field} ?? 0);

            if (is_numeric($value)) {
                return $value + 0;
            }

            $sanitized = preg_replace('/[^0-9.-]/', '', (string) $value);

            return is_numeric($sanitized) ? $sanitized + 0 : 0;
        });
    }

    /**
     * Dapatkan ringkasan status input harian untuk Pengaliran, Pemeliharaan, dan Alat Berat
     */
    public function getDailyAudit(?string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $pksList = Pks::whereNotIn('akro', ['TEP', 'DTM', 'DBR'])->orderBy('nama')->get();

        $auditData = [];
        $missingPengaliran = [];
        $missingPemeliharaan = [];
        $missingAlatBerat = [];
        $fullyCompleted = [];
        $partiallyCompleted = [];
        $unsubmitted = [];

        foreach ($pksList as $pks) {
            $hasPengaliran = Pengaliran::where('id_pks', $pks->id_pks)
                ->whereDate('tanggal', $targetDate)
                ->exists();

            $hasPemeliharaan = Pemeliharaan::where('id_pks', $pks->id_pks)
                ->whereDate('tanggal', $targetDate)
                ->exists();

            $hasAlatBerat = MonitoringAlatBerat::where('id_pks', $pks->id_pks)
                ->whereDate('tanggal', $targetDate)
                ->exists();

            $missingItems = [];
            if (!$hasPengaliran) $missingItems[] = 'Pengaliran LA';
            if (!$hasPemeliharaan) $missingItems[] = 'Pemeliharaan Kolam/Bed';
            if (!$hasAlatBerat) $missingItems[] = 'Operasional Alat Berat';

            $item = [
                'id_pks' => $pks->id_pks,
                'nama' => $pks->nama,
                'akro' => $pks->akro ?: $pks->nama,
                'asisten' => $pks->asisten ?: 'Belum diset',
                'wa_asisten' => $pks->wa_asisten ?: null,
                'pengaliran' => $hasPengaliran,
                'pemeliharaan' => $hasPemeliharaan,
                'alat_berat' => $hasAlatBerat,
                'missing_items' => $missingItems,
                'is_complete' => empty($missingItems),
            ];

            $auditData[] = $item;

            if (!$hasPengaliran) $missingPengaliran[] = $item;
            if (!$hasPemeliharaan) $missingPemeliharaan[] = $item;
            if (!$hasAlatBerat) $missingAlatBerat[] = $item;

            if (empty($missingItems)) {
                $fullyCompleted[] = $item;
            } elseif (count($missingItems) === 3) {
                $unsubmitted[] = $item;
            } else {
                $partiallyCompleted[] = $item;
            }
        }

        return [
            'tanggal' => $targetDate,
            'tanggal_formatted' => Carbon::parse($targetDate)->locale('id')->translatedFormat('l, d F Y'),
            'total_pks' => count($pksList),
            'count_lengkap' => count($fullyCompleted),
            'count_sebagian' => count($partiallyCompleted),
            'count_belum_ada' => count($unsubmitted),
            'missing_pengaliran_count' => count($missingPengaliran),
            'missing_pemeliharaan_count' => count($missingPemeliharaan),
            'missing_alat_berat_count' => count($missingAlatBerat),
            'missing_pengaliran' => $missingPengaliran,
            'missing_pemeliharaan' => $missingPemeliharaan,
            'missing_alat_berat' => $missingAlatBerat,
            'fully_completed' => $fullyCompleted,
            'partially_completed' => $partiallyCompleted,
            'unsubmitted' => $unsubmitted,
            'all_pks' => $auditData,
        ];
    }

    /**
     * Dapatkan ringkasan metrik statistik operasional bulan berjalan
     */
    public function getMonthlyStatistics(?string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date) : Carbon::now('Asia/Jakarta');
        $startOfMonth = $targetDate->copy()->startOfMonth();
        $endOfMonth = $targetDate->copy()->endOfMonth();

        return $this->getScopedData([
            'type' => 'month',
            'start_date' => $startOfMonth,
            'end_date' => $endOfMonth,
            'label' => 'Bulan ' . $targetDate->locale('id')->translatedFormat('F Y'),
            'is_custom' => false,
        ]);
    }

    /**
     * Helper Parser untuk Mendeteksi Unit PKS dari Prompt Pertanyaan User
     */
    public function parsePksFromQuery(string $query): ?Pks
    {
        $q = strtolower(trim($query));
        $allPks = Pks::whereNotIn('akro', ['TEP', 'DTM', 'DBR'])->get();

        foreach ($allPks as $pks) {
            $namaClean = strtolower(trim($pks->nama));
            $akroClean = strtolower(trim($pks->akro));

            // Check acronym (with word boundaries)
            if (preg_match('/\b' . preg_quote($akroClean, '/') . '\b/i', $q)) {
                return $pks;
            }

            // Check full name
            if (preg_match('/\b' . preg_quote($namaClean, '/') . '\b/i', $q)) {
                return $pks;
            }

            // Check without "pks" prefix
            $withoutPks = str_replace('pks ', '', $namaClean);
            if ($withoutPks !== $namaClean && preg_match('/\b' . preg_quote($withoutPks, '/') . '\b/i', $q)) {
                return $pks;
            }

            // Check without "sei " prefix if distinctive
            $withoutSei = str_replace('sei ', '', $namaClean);
            if ($withoutSei !== $namaClean && strlen($withoutSei) >= 4 && preg_match('/\b' . preg_quote($withoutSei, '/') . '\b/i', $q)) {
                return $pks;
            }
        }

        return null;
    }

    /**
     * Helper Parser untuk Mendeteksi Rentang Tanggal / Periode dari Prompt Pertanyaan User
     */
    public function parseDateRangeFromQuery(string $query, ?string $fallbackDate = null): array
    {
        $now = $fallbackDate ? Carbon::parse($fallbackDate) : Carbon::now('Asia/Jakarta');
        $q = strtolower(trim($query));

        // 1. Relative: Kemarin
        if (preg_match('/\bkemarin\b/i', $q)) {
            $d = $now->copy()->subDay();
            return [
                'type' => 'single',
                'start_date' => $d->copy()->startOfDay(),
                'end_date' => $d->copy()->endOfDay(),
                'label' => $d->locale('id')->translatedFormat('l, d F Y') . ' (Kemarin)',
                'is_custom' => true,
            ];
        }

        // 2. Relative: Hari ini
        if (preg_match('/\bhari ini\b/i', $q)) {
            return [
                'type' => 'single',
                'start_date' => $now->copy()->startOfDay(),
                'end_date' => $now->copy()->endOfDay(),
                'label' => $now->locale('id')->translatedFormat('l, d F Y') . ' (Hari Ini)',
                'is_custom' => false,
            ];
        }

        // 3. Relative: Minggu lalu
        if (preg_match('/\bminggu lalu\b/i', $q)) {
            $start = $now->copy()->subWeek()->startOfWeek();
            $end = $now->copy()->subWeek()->endOfWeek();
            return [
                'type' => 'range',
                'start_date' => $start->startOfDay(),
                'end_date' => $end->endOfDay(),
                'label' => 'Minggu Lalu (' . $start->format('d/m/Y') . ' – ' . $end->format('d/m/Y') . ')',
                'is_custom' => true,
            ];
        }

        // 4. Relative: Minggu ini
        if (preg_match('/\bminggu ini\b/i', $q)) {
            $start = $now->copy()->startOfWeek();
            $end = $now->copy()->endOfWeek();
            return [
                'type' => 'range',
                'start_date' => $start->startOfDay(),
                'end_date' => $end->endOfDay(),
                'label' => 'Minggu Ini (' . $start->format('d/m/Y') . ' – ' . $end->format('d/m/Y') . ')',
                'is_custom' => true,
            ];
        }

        // 5. Relative: Bulan lalu
        if (preg_match('/\bbulan lalu\b/i', $q)) {
            $lastMonth = $now->copy()->subMonth();
            return [
                'type' => 'month',
                'start_date' => $lastMonth->copy()->startOfMonth(),
                'end_date' => $lastMonth->copy()->endOfMonth(),
                'label' => 'Bulan ' . $lastMonth->locale('id')->translatedFormat('F Y'),
                'is_custom' => true,
            ];
        }

        // 6. Relative: Bulan ini
        if (preg_match('/\bbulan ini\b/i', $q)) {
            return [
                'type' => 'month',
                'start_date' => $now->copy()->startOfMonth(),
                'end_date' => $now->copy()->endOfMonth(),
                'label' => 'Bulan ' . $now->locale('id')->translatedFormat('F Y'),
                'is_custom' => false,
            ];
        }

        // 7. Date Range: dd/mm/yyyy - dd/mm/yyyy
        if (preg_match('/(?:dari\s+)?(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})\s*(?:s\/?d|sampai|hingga|\-|sd|ke)\s*(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/i', $q, $m)) {
            $start = Carbon::createFromDate((int)$m[3], (int)$m[2], (int)$m[1])->startOfDay();
            $end = Carbon::createFromDate((int)$m[6], (int)$m[5], (int)$m[4])->endOfDay();
            return [
                'type' => 'range',
                'start_date' => $start,
                'end_date' => $end,
                'label' => $start->locale('id')->translatedFormat('d F Y') . ' – ' . $end->locale('id')->translatedFormat('d F Y'),
                'is_custom' => true,
            ];
        }

        // 8. Date Range: d1 bulan1 y1 s/d d2 bulan2 y2 (e.g., 1 januari 2026 sampai 15 januari 2026)
        if (preg_match('/(?:dari\s+)?(\d{1,2})\s+([a-zA-Z]+)(?:\s+(\d{4}))?\s*(?:s\/?d|sampai|hingga|\-|sd|ke)\s*(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4})/i', $q, $m)) {
            $m1 = self::$bulanMap[strtolower($m[2])] ?? null;
            $m2 = self::$bulanMap[strtolower($m[5])] ?? null;
            if ($m1 && $m2) {
                $y2 = (int)$m[6];
                $y1 = !empty($m[3]) ? (int)$m[3] : $y2;
                $start = Carbon::createFromDate($y1, $m1, (int)$m[1])->startOfDay();
                $end = Carbon::createFromDate($y2, $m2, (int)$m[4])->endOfDay();
                return [
                    'type' => 'range',
                    'start_date' => $start,
                    'end_date' => $end,
                    'label' => $start->locale('id')->translatedFormat('d F Y') . ' – ' . $end->locale('id')->translatedFormat('d F Y'),
                    'is_custom' => true,
                ];
            }
        }

        // 9. Date Range: d1 s/d d2 bulan yyyy (e.g., 1 s/d 15 januari 2026, 1-15 jan 2026)
        if (preg_match('/(?:dari\s+)?(\d{1,2})\s*(?:s\/?d|sampai|hingga|\-|sd|ke)\s*(\d{1,2})\s+([a-zA-Z]+)(?:\s+(\d{4}))?/i', $q, $m)) {
            $monthNum = self::$bulanMap[strtolower($m[3])] ?? null;
            if ($monthNum) {
                $year = !empty($m[4]) ? (int)$m[4] : (int)$now->year;
                $start = Carbon::createFromDate($year, $monthNum, (int)$m[1])->startOfDay();
                $end = Carbon::createFromDate($year, $monthNum, (int)$m[2])->endOfDay();
                return [
                    'type' => 'range',
                    'start_date' => $start,
                    'end_date' => $end,
                    'label' => sprintf("%02d – %02d %s %d", (int)$m[1], (int)$m[2], Carbon::createFromDate($year, $monthNum, 1)->locale('id')->translatedFormat('F'), $year),
                    'is_custom' => true,
                ];
            }
        }

        // 10. Single Date: dd/mm/yyyy or dd-mm-yyyy
        if (preg_match('/(?:tanggal|tgl)?\s*(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/i', $q, $m)) {
            $d = Carbon::createFromDate((int)$m[3], (int)$m[2], (int)$m[1]);
            return [
                'type' => 'single',
                'start_date' => $d->copy()->startOfDay(),
                'end_date' => $d->copy()->endOfDay(),
                'label' => $d->locale('id')->translatedFormat('l, d F Y'),
                'is_custom' => true,
            ];
        }

        // 11. Single Date: 12 januari 2026 or tanggal 12 januari 2026
        if (preg_match('/(?:tanggal|tgl)?\s*(\d{1,2})\s+([a-zA-Z]+)(?:\s+(\d{4}))?/i', $q, $m)) {
            $monthNum = self::$bulanMap[strtolower($m[2])] ?? null;
            if ($monthNum) {
                $year = !empty($m[3]) ? (int)$m[3] : (int)$now->year;
                $d = Carbon::createFromDate($year, $monthNum, (int)$m[1]);
                return [
                    'type' => 'single',
                    'start_date' => $d->copy()->startOfDay(),
                    'end_date' => $d->copy()->endOfDay(),
                    'label' => $d->locale('id')->translatedFormat('l, d F Y'),
                    'is_custom' => true,
                ];
            }
        }

        // 12. Month & Year: bulan januari 2026 or januari 2026
        if (preg_match('/(?:bulan|bln)?\s*([a-zA-Z]+)\s+(\d{4})\b/i', $q, $m)) {
            $monthNum = self::$bulanMap[strtolower($m[1])] ?? null;
            if ($monthNum) {
                $year = (int)$m[2];
                $d = Carbon::createFromDate($year, $monthNum, 1);
                return [
                    'type' => 'month',
                    'start_date' => $d->copy()->startOfMonth(),
                    'end_date' => $d->copy()->endOfMonth(),
                    'label' => 'Bulan ' . $d->locale('id')->translatedFormat('F Y'),
                    'is_custom' => true,
                ];
            }
        }

        // 13. Month only: bulan januari
        if (preg_match('/(?:bulan|bln)\s+([a-zA-Z]+)\b/i', $q, $m)) {
            $monthNum = self::$bulanMap[strtolower($m[1])] ?? null;
            if ($monthNum) {
                $d = Carbon::createFromDate((int)$now->year, $monthNum, 1);
                return [
                    'type' => 'month',
                    'start_date' => $d->copy()->startOfMonth(),
                    'end_date' => $d->copy()->endOfMonth(),
                    'label' => 'Bulan ' . $d->locale('id')->translatedFormat('F Y'),
                    'is_custom' => true,
                ];
            }
        }

        // 14. Year only: tahun 2025 or sepanjang 2025
        if (preg_match('/(?:tahun|thn|sepanjang)\s+(\d{4})\b/i', $q, $m) || preg_match('/\b(20\d{2})\b/', $q, $m)) {
            $year = (int)$m[1];
            $d = Carbon::createFromDate($year, 1, 1);
            return [
                'type' => 'year',
                'start_date' => $d->copy()->startOfYear(),
                'end_date' => $d->copy()->endOfYear(),
                'label' => 'Tahun ' . $year,
                'is_custom' => true,
            ];
        }

        // Default: Fallback to current month
        return [
            'type' => 'default',
            'start_date' => $now->copy()->startOfMonth(),
            'end_date' => $now->copy()->endOfMonth(),
            'label' => 'Bulan ' . $now->locale('id')->translatedFormat('F Y'),
            'is_custom' => false,
        ];
    }

    /**
     * Dapatkan Data Statistik Lengkap dari Database Sesuai Rentang Waktu dan/atau Unit PKS
     */
    public function getScopedData(array $dateRange, ?Pks $targetPks = null): array
    {
        $startDate = $dateRange['start_date'];
        $endDate = $dateRange['end_date'];

        // Base Query Pengaliran
        $pengaliranQ = Pengaliran::with('pks')
            ->whereBetween('tanggal', [$startDate->format('Y-m-d 00:00:00'), $endDate->format('Y-m-d 23:59:59')]);

        if ($targetPks) {
            $pengaliranQ->where('id_pks', $targetPks->id_pks);
        }

        $pengaliranRecords = $pengaliranQ->orderBy('tanggal', 'asc')->get();
        $totalVolDihasilkan = self::numericSum($pengaliranRecords, 'vol_limbah_dihasilkan');
        $totalVolDialirkan = self::numericSum($pengaliranRecords, 'vol_limbah_dialirkan');
        $totalFlatBedPengaliran = self::numericSum($pengaliranRecords, 'flat_bed');
        $totalLuasArea = self::numericSum($pengaliranRecords, 'luas_area');
        $countPengaliranRecords = $pengaliranRecords->count();

        // Distinct Block & Bak
        $bloks = $pengaliranRecords->pluck('blok')->filter(fn($v) => $v && $v !== '-')->unique()->values()->all();
        $baks = $pengaliranRecords->pluck('no_bak')->filter(fn($v) => $v && $v !== '-')->unique()->values()->all();

        // Base Query Pemeliharaan
        $pemeliharaanQ = Pemeliharaan::with('pks')
            ->whereBetween('tanggal', [$startDate->format('Y-m-d 00:00:00'), $endDate->format('Y-m-d 23:59:59')]);

        if ($targetPks) {
            $pemeliharaanQ->where('id_pks', $targetPks->id_pks);
        }

        $pemeliharaanRecords = $pemeliharaanQ->orderBy('tanggal', 'asc')->get();
        $totalFlatBedPemeliharaan = self::numericSum($pemeliharaanRecords, 'flat_bed');
        $totalLongBedPemeliharaan = self::numericSum($pemeliharaanRecords, 'long_bed');
        $totalHk = self::numericSum($pemeliharaanRecords, 'jumlah_hk');
        $totalMekanis = $pemeliharaanRecords->where('jenis_pemeliharaan', '1')->count();
        $totalManual = $pemeliharaanRecords->where('jenis_pemeliharaan', '2')->count();

        // Base Query Monitoring Alat Berat
        $monitoringAbQ = MonitoringAlatBerat::with('pks', 'alatBerat')
            ->whereBetween('tanggal', [$startDate->format('Y-m-d 00:00:00'), $endDate->format('Y-m-d 23:59:59')]);

        if ($targetPks) {
            $monitoringAbQ->where('id_pks', $targetPks->id_pks);
        }

        $monitoringAbRecords = $monitoringAbQ->orderBy('tanggal', 'asc')->get();
        $totalHm = self::numericSum($monitoringAbRecords, 'total_hm');
        $totalBbm = self::numericSum($monitoringAbRecords, 'bbm_liter');
        $countLogAb = $monitoringAbRecords->count();

        // Status Master Alat Berat
        $totalUnitAlat = AlatBerat::count();
        $unitReady = AlatBerat::where('status', 'Operational')->count();
        $unitMaintenance = AlatBerat::where('status', 'Maintenance')->count();
        $unitBreakdown = AlatBerat::where('status', 'Breakdown')->count();
        $unitRolling = AlatBerat::where('status', 'Rolling')->count();

        // Per-PKS Breakdown jika Multi PKS
        $pksBreakdown = [];
        if (!$targetPks) {
            $allOperationalPks = Pks::whereNotIn('akro', ['TEP', 'DTM', 'DBR'])->orderBy('nama')->get();
            foreach ($allOperationalPks as $p) {
                $pksPeng = $pengaliranRecords->where('id_pks', $p->id_pks);
                $pksPem = $pemeliharaanRecords->where('id_pks', $p->id_pks);
                $pksAb = $monitoringAbRecords->where('id_pks', $p->id_pks);

                $pksBreakdown[] = [
                    'id_pks' => $p->id_pks,
                    'nama' => $p->nama,
                    'akro' => $p->akro,
                    'vol_dihasilkan' => self::numericSum($pksPeng, 'vol_limbah_dihasilkan'),
                    'vol_dialirkan' => self::numericSum($pksPeng, 'vol_limbah_dialirkan'),
                    'flat_bed_pengaliran' => self::numericSum($pksPeng, 'flat_bed'),
                    'luas_area' => self::numericSum($pksPeng, 'luas_area'),
                    'count_pengaliran' => $pksPeng->count(),
                    'flat_bed_pemeliharaan' => self::numericSum($pksPem, 'flat_bed'),
                    'long_bed_pemeliharaan' => self::numericSum($pksPem, 'long_bed'),
                    'jumlah_hk' => self::numericSum($pksPem, 'jumlah_hk'),
                    'total_hm' => self::numericSum($pksAb, 'total_hm'),
                    'total_bbm' => self::numericSum($pksAb, 'bbm_liter'),
                ];
            }
        }

        return [
            'periode_label' => $dateRange['label'],
            'date_range' => $dateRange,
            'target_pks' => $targetPks ? [
                'id_pks' => $targetPks->id_pks,
                'nama' => $targetPks->nama,
                'akro' => $targetPks->akro,
                'asisten' => $targetPks->asisten,
            ] : null,
            'pengaliran' => [
                'records' => $countPengaliranRecords,
                'vol_dihasilkan' => $totalVolDihasilkan,
                'vol_dialirkan' => $totalVolDialirkan,
                'flat_bed' => $totalFlatBedPengaliran,
                'luas_area' => $totalLuasArea,
                'bloks' => empty($bloks) ? '-' : implode(', ', $bloks),
                'baks' => empty($baks) ? '-' : implode(', ', $baks),
                'items' => $pengaliranRecords,
            ],
            'pemeliharaan' => [
                'records' => $pemeliharaanRecords->count(),
                'flat_bed' => $totalFlatBedPemeliharaan,
                'long_bed' => $totalLongBedPemeliharaan,
                'jumlah_hk' => $totalHk,
                'mekanis' => $totalMekanis,
                'manual' => $totalManual,
                'items' => $pemeliharaanRecords,
            ],
            'alat_berat' => [
                'total_unit' => $totalUnitAlat,
                'operational' => $unitReady,
                'maintenance' => $unitMaintenance,
                'breakdown' => $unitBreakdown,
                'rolling' => $unitRolling,
                'total_hm' => $totalHm,
                'total_bbm' => $totalBbm,
                'total_log' => $countLogAb,
                'items' => $monitoringAbRecords,
            ],
            'pks_breakdown' => $pksBreakdown,
        ];
    }

    /**
     * Memproses Pertanyaan User ke AI Assistant
     */
    public function answerQuery(string $query, ?string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now('Asia/Jakarta')->format('Y-m-d');
        
        // 1. Ekstrak Unit PKS dan Rentang Waktu dari prompt pertanyaan
        $targetPks = $this->parsePksFromQuery($query);
        $dateRange = $this->parseDateRangeFromQuery($query, $targetDate);

        // 2. Dapatkan data audit kepatuhan dan scoped statistik database
        $audit = $this->getDailyAudit($dateRange['type'] === 'single' ? $dateRange['start_date']->format('Y-m-d') : $targetDate);
        $scopedData = $this->getScopedData($dateRange, $targetPks);

        // 3. Coba gunakan Gemini API jika key tersedia
        if (!empty($this->geminiApiKey)) {
            try {
                $geminiResponse = $this->callGeminiApi($query, $audit, $scopedData, $targetPks, $dateRange);
                if (!empty($geminiResponse)) {
                    $badges = $this->buildBadgesForScopedData($scopedData, $targetPks);
                    $actions = $this->buildActionsForScopedData($scopedData, $targetPks);

                    return [
                        'success' => true,
                        'source' => 'gemini',
                        'answer' => $geminiResponse,
                        'badges' => $badges,
                        'suggested_actions' => $actions,
                        'audit' => $audit,
                        'stats' => $scopedData,
                        'target_pks' => $targetPks ? $targetPks->nama : null,
                        'date_range' => $dateRange['label'],
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning("[SIMOLI AI] Gemini API call failed, falling back to heuristic engine: " . $e->getMessage());
            }
        }

        // 4. Gunakan Intelligent Heuristic & NLP SIMOLI Engine
        $localAnswer = $this->generateIntelligentLocalAnswer($query, $audit, $scopedData, $targetPks, $dateRange);

        return [
            'success' => true,
            'source' => 'simoli_engine',
            'answer' => $localAnswer['text'],
            'badges' => $localAnswer['badges'] ?? [],
            'suggested_actions' => $localAnswer['actions'] ?? [],
            'audit' => $audit,
            'stats' => $scopedData,
            'target_pks' => $targetPks ? $targetPks->nama : null,
            'date_range' => $dateRange['label'],
        ];
    }

    /**
     * Panggil Google Gemini API dengan Konteks Data Sesuai Rentang Waktu dan Unit PKS
     */
    protected function callGeminiApi(string $query, array $audit, array $scopedData, ?Pks $targetPks, array $dateRange): ?string
    {
        $pksContext = $targetPks 
            ? "PKS SPESIFIK: {$targetPks->nama} ({$targetPks->akro}) - Asisten: " . ($targetPks->asisten ?: '-')
            : "LINGKUP UNIT: Seluruh 12 Unit PKS PTPN IV Regional III";

        $p = $scopedData['pengaliran'];
        $m = $scopedData['pemeliharaan'];
        $ab = $scopedData['alat_berat'];

        $systemContext = "Anda adalah SIMOLI AI Assistant untuk Tim Admin TEP (Bagian Teknik & Pengolahan) PTPN IV Regional III.\n"
            . "Tugas Anda adalah memberikan jawaban dan rekapitulasi data yang 100% AKURAT, LENGKAP, dan RELEVAN dengan rentang waktu serta unit PKS yang ditanyakan.\n\n"
            . "=== PARAMETER FILTER PERTANYAAN ===\n"
            . "- {$pksContext}\n"
            . "- PERIODE DIMINTA: {$scopedData['periode_label']} ({$dateRange['start_date']->format('Y-m-d')} s/d {$dateRange['end_date']->format('Y-m-d')})\n\n"
            . "=== DATA DATABASE REAL-TIME SESUAI PERIODE & PKS DI ATAS ===\n"
            . "1. PENGALIRAN LAND APLIKASI (LA):\n"
            . "   - Total Entri Transaksi: {$p['records']}\n"
            . "   - Vol. Limbah Dihasilkan (PKS): " . number_format($p['vol_dihasilkan'], 2) . " m³\n"
            . "   - Vol. Limbah Dialirkan (LA): " . number_format($p['vol_dialirkan'], 2) . " m³\n"
            . "   - Aplikasi Flat Bed: " . number_format($p['flat_bed']) . " Bed\n"
            . "   - Luas Area Aplikasi: " . number_format($p['luas_area'], 2) . " Ha\n"
            . "   - Lokasi Blok: {$p['bloks']}\n"
            . "   - Bak Distribusi: {$p['baks']}\n\n"
            . "2. PEMELIHARAAN KOLAM IPAL & BED:\n"
            . "   - Total Kegiatan: {$m['records']}\n"
            . "   - Flat Bed Dirawat: " . number_format($m['flat_bed']) . " Bed\n"
            . "   - Long Bed Dirawat: " . number_format($m['long_bed']) . " Bed\n"
            . "   - Total Tenaga Kerja (HK): " . number_format($m['jumlah_hk']) . " HK\n"
            . "   - Metode Mekanis (Alat Berat): {$m['mekanis']} Kegiatan | Manual: {$m['manual']} Kegiatan\n\n"
            . "3. OPERASIONAL ALAT BERAT:\n"
            . "   - Total Jam Kerja (HM): " . number_format($ab['total_hm'], 2) . " Jam\n"
            . "   - Total Konsumsi BBM: " . number_format($ab['total_bbm'], 2) . " Liter\n"
            . "   - Status Populasi Unit: Ready {$ab['operational']}, Breakdown {$ab['breakdown']}, Maint {$ab['maintenance']}, Rolling {$ab['rolling']} dari total {$ab['total_unit']} Unit\n\n"
            . "=== STATUS AUDIT KEPATUHAN HARIAN (TANGGAL {$audit['tanggal_formatted']}) ===\n"
            . "- Lengkap: {$audit['count_lengkap']} PKS | Sebagian: {$audit['count_sebagian']} PKS | Belum Input: {$audit['count_belum_ada']} PKS\n"
            . "- PKS Belum Input Pengaliran: " . ($audit['missing_pengaliran_count'] === 0 ? 'Nihil (Semua Sudah)' : implode(', ', array_map(fn($x) => $x['akro'], $audit['missing_pengaliran']))) . "\n\n"
            . "INSTRUKSI JAWABAN:\n"
            . "- Jawablah spesifik sesuai unit dan periode yang diminta di atas.\n"
            . "- Gunakan format Markdown rapi dengan emoji, poin-poin tegas, angka format Indonesia (titik pemisah ribuan, koma pemisah desimal).\n"
            . "- Sertakan volume limbah dihasilkan, volume dialirkan, bed, dan luas area jika ditanya pengaliran.\n"
            . "- Jika tidak ada data transaksi pada rentang tersebut, nyatakan dengan jelas bahwa data belum tercatat untuk periode tersebut.";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->geminiModel}:generateContent?key=" . $this->geminiApiKey;

        $response = Http::timeout(20)->post($url, [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemContext . "\n\nPertanyaan User: " . $query]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 1200,
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        return null;
    }

    /**
     * Engine NLP & Analisis Heuristik Internal SIMOLI Sesuai Rentang Waktu & Unit PKS
     */
    protected function generateIntelligentLocalAnswer(
        string $rawQuery,
        array $audit,
        array $scopedData,
        ?Pks $targetPks,
        array $dateRange
    ): array {
        $q = strtolower(trim($rawQuery));
        $actions = [];
        $badges = $this->buildBadgesForScopedData($scopedData, $targetPks);

        $p = $scopedData['pengaliran'];
        $m = $scopedData['pemeliharaan'];
        $ab = $scopedData['alat_berat'];
        $periodeLabel = $scopedData['periode_label'];

        // 1. Cek Unit / PKS yang Belum Input (Audit Kepatuhan)
        if (
            str_contains($q, 'belum input') ||
            str_contains($q, 'belum isi') ||
            str_contains($q, 'belum ada input') ||
            str_contains($q, 'pemberitahuan') ||
            str_contains($q, 'peringatan') ||
            str_contains($q, 'alert') ||
            str_contains($q, 'notifikasi') ||
            str_contains($q, 'kepatuhan') ||
            str_contains($q, 'siapa saja') ||
            str_contains($q, 'pks mana')
        ) {
            $text = "### 🚨 **Laporan Kepatuhan Input Harian SIMOLI**\n";
            $text .= "📅 **Tanggal Pantauan:** {$audit['tanggal_formatted']}\n\n";

            $text .= "📊 **Ringkasan Status 12 PKS:**\n";
            $text .= "- ✅ **Lengkap (3 Modul):** {$audit['count_lengkap']} Unit PKS\n";
            $text .= "- ⚠️ **Sebagian Input:** {$audit['count_sebagian']} Unit PKS\n";
            $text .= "- ❌ **Belum Ada Input Sama Sekali:** {$audit['count_belum_ada']} Unit PKS\n\n";

            $text .= "---\n";

            // Rincian Pengaliran
            $text .= "#### 💧 **1. Pengaliran Land Aplikasi (LA)**\n";
            if ($audit['missing_pengaliran_count'] === 0) {
                $text .= "✅ *Semua 12 Unit PKS telah menginput data pengaliran untuk tanggal ini.*\n\n";
            } else {
                $text .= "⚠️ **{$audit['missing_pengaliran_count']} PKS Belum Input Pengaliran:**\n";
                foreach ($audit['missing_pengaliran'] as $idx => $pItem) {
                    $text .= ($idx + 1) . ". **{$pItem['nama']} ({$pItem['akro']})** — Asisten: {$pItem['asisten']}\n";
                }
                $text .= "\n";
            }

            // Rincian Pemeliharaan
            $text .= "#### 🛠️ **2. Pemeliharaan Kolam IPAL & Bed**\n";
            if ($audit['missing_pemeliharaan_count'] === 0) {
                $text .= "✅ *Semua 12 Unit PKS telah menginput data pemeliharaan untuk tanggal ini.*\n\n";
            } else {
                $text .= "⚠️ **{$audit['missing_pemeliharaan_count']} PKS Belum Input Pemeliharaan:**\n";
                foreach ($audit['missing_pemeliharaan'] as $idx => $pItem) {
                    $text .= ($idx + 1) . ". **{$pItem['nama']} ({$pItem['akro']})** — Asisten: {$pItem['asisten']}\n";
                }
                $text .= "\n";
            }

            // Rincian Alat Berat
            $text .= "#### 🚜 **3. Operasional Alat Berat**\n";
            if ($audit['missing_alat_berat_count'] === 0) {
                $text .= "✅ *Semua 12 Unit PKS telah menginput laporan kerja alat berat untuk tanggal ini.*\n\n";
            } else {
                $text .= "⚠️ **{$audit['missing_alat_berat_count']} PKS Belum Input Alat Berat:**\n";
                foreach ($audit['missing_alat_berat'] as $idx => $pItem) {
                    $text .= ($idx + 1) . ". **{$pItem['nama']} ({$pItem['akro']})** — Asisten: {$pItem['asisten']}\n";
                }
                $text .= "\n";
            }

            $text .= "💡 *Rekomendasi:* Anda dapat mengirimkan pengingat instan melalui WhatsApp ke seluruh Asisten PKS yang belum melakukan input.";

            $actions[] = [
                'label' => '📲 Kirim WhatsApp Pengingat ke Semua PKS Tertunda',
                'action_type' => 'trigger_reminder_all',
            ];

            return [
                'text' => $text,
                'actions' => $actions,
                'badges' => [
                    'Belum Pengaliran: ' . $audit['missing_pengaliran_count'],
                    'Belum Pemeliharaan: ' . $audit['missing_pemeliharaan_count'],
                    'Belum Alat Berat: ' . $audit['missing_alat_berat_count'],
                ],
            ];
        }

        // 2. KASUS SPESIFIK UNIT PKS (misal: "rekap pengaliran pks tanah putih tanggal 12 januari 2026")
        if ($targetPks) {
            $pksName = $targetPks->nama;
            $pksAkro = $targetPks->akro;
            $asisten = $targetPks->asisten ?: 'Belum diset';

            // Jika secara spesifik menanyakan Pemeliharaan
            if (str_contains($q, 'pemeliharaan') || str_contains($q, 'kolam') || str_contains($q, 'rawat')) {
                $text = "### 🛠️ **Laporan Pemeliharaan — PKS {$pksName} ({$pksAkro})**\n";
                $text .= "📅 **Periode:** {$periodeLabel}\n";
                $text .= "👤 **Asisten:** {$asisten}\n\n";

                if ($m['records'] === 0) {
                    $text .= "ℹ️ *Belum ada catatan transaksi pemeliharaan untuk PKS {$pksName} pada periode {$periodeLabel}.*\n";
                } else {
                    $text .= "📊 **Realisasi Pemeliharaan Fisik:**\n";
                    $text .= "- 🟫 **Aplikasi Flat Bed Dirawat:** " . number_format($m['flat_bed'], 0, ',', '.') . " Bed\n";
                    $text .= "- 🟩 **Aplikasi Long Bed Dirawat:** " . number_format($m['long_bed'], 0, ',', '.') . " Bed\n";
                    $text .= "- 👷 **Penggunaan Tenaga Kerja:** " . number_format($m['jumlah_hk'], 0, ',', '.') . " HK\n";
                    $text .= "- 🚜 **Kegiatan Mekanis:** {$m['mekanis']} | 🧤 **Manual:** {$m['manual']}\n";
                    $text .= "- 📋 **Total Log Tercatat:** {$m['records']} Transaksi\n";
                }

                return [
                    'text' => $text,
                    'badges' => $badges,
                    'actions' => $this->buildActionsForScopedData($scopedData, $targetPks),
                ];
            }

            // Jika secara spesifik menanyakan Alat Berat
            if (str_contains($q, 'alat berat') || str_contains($q, 'excavator') || str_contains($q, 'bbm') || str_contains($q, 'hm')) {
                $text = "### 🚜 **Laporan Operasional Alat Berat — PKS {$pksName} ({$pksAkro})**\n";
                $text .= "📅 **Periode:** {$periodeLabel}\n";
                $text .= "👤 **Asisten:** {$asisten}\n\n";

                if ($ab['total_log'] === 0) {
                    $text .= "ℹ️ *Belum ada catatan log alat berat untuk PKS {$pksName} pada periode {$periodeLabel}.*\n";
                } else {
                    $text .= "📊 **Kinerja Jam Kerja & BBM:**\n";
                    $text .= "- 🕒 **Total Jam Kerja (HM):** " . number_format($ab['total_hm'], 2, ',', '.') . " Jam\n";
                    $text .= "- ⛽ **Total Konsumsi BBM:** " . number_format($ab['total_bbm'], 2, ',', '.') . " Liter\n";
                    $text .= "- 📝 **Total Log Shift:** " . number_format($ab['total_log']) . " Laporan\n";
                }

                return [
                    'text' => $text,
                    'badges' => $badges,
                    'actions' => $this->buildActionsForScopedData($scopedData, $targetPks),
                ];
            }

            // Default untuk PKS spesifik: LAPORAN PENGALIRAN / LENGKAP
            $text = "### 💧 **Laporan Pengaliran Limbah — PKS {$pksName} ({$pksAkro})**\n";
            $text .= "📅 **Periode:** {$periodeLabel}\n";
            $text .= "👤 **Asisten:** {$asisten}\n\n";

            if ($p['records'] === 0) {
                $text .= "ℹ️ *Belum ada catatan pengaliran limbah untuk PKS {$pksName} pada periode {$periodeLabel}.*\n\n";
                $text .= "💡 *Catatan:* Silakan pastikan operator atau asisten unit telah menginput transaksi pengaliran pada tanggal tersebut.";
            } else {
                $text .= "📊 **Akumulasi Kinerja Pengaliran:**\n";
                $text .= "- 🧪 **Limbah Dihasilkan (PKS):** " . number_format($p['vol_dihasilkan'], 2, ',', '.') . " m³\n";
                $text .= "- 🌊 **Limbah Dialirkan (LA):** " . number_format($p['vol_dialirkan'], 2, ',', '.') . " m³\n";
                $persenDialirkan = $p['vol_dihasilkan'] > 0 ? round(($p['vol_dialirkan'] / $p['vol_dihasilkan']) * 100, 1) : 0;
                $text .= "- 📈 **Rasio Pengaliran Limbah:** **{$persenDialirkan}%**\n";
                $text .= "- 🌱 **Aplikasi Flat Bed:** " . number_format($p['flat_bed'], 0, ',', '.') . " Bed\n";
                $text .= "- 🗺️ **Luas Area Aplikasi:** " . number_format($p['luas_area'], 2, ',', '.') . " Hektar\n";
                $text .= "- 🧱 **Lokasi Blok:** {$p['bloks']}\n";
                $text .= "- 🚰 **Bak Distribusi:** {$p['baks']}\n";
                $text .= "- 📋 **Total Entri Data:** {$p['records']} Transaksi\n\n";

                // Jika single day atau rentang <= 7 hari, tampilkan rincian baris jika ada
                if ($p['items']->count() > 0 && ($dateRange['type'] === 'single' || $p['items']->count() <= 10)) {
                    $text .= "📝 **Rincian Transaksi:**\n";
                    $text .= "| No | Tanggal | Jam | Blok | Bak | Dihasilkan | Dialirkan | Flat Bed |\n";
                    $text .= "| :-: | :-: | :-: | :-: | :-: | -: | -: | -: |\n";
                    foreach ($p['items']->take(10) as $idx => $item) {
                        $tgl = Carbon::parse($item->tanggal)->format('d/m/Y');
                        $jam = $item->jam_mulai ? ($item->jam_mulai . '-' . $item->jam_selesai) : '-';
                        $volH = number_format($item->vol_limbah_dihasilkan, 0, ',', '.');
                        $volA = number_format($item->vol_limbah_dialirkan, 0, ',', '.');
                        $bed = number_format($item->flat_bed, 0, ',', '.');
                        $text .= "| " . ($idx + 1) . " | {$tgl} | {$jam} | {$item->blok} | {$item->no_bak} | {$volH} m³ | {$volA} m³ | {$bed} |\n";
                    }
                    $text .= "\n";
                }
            }

            return [
                'text' => $text,
                'badges' => $badges,
                'actions' => $this->buildActionsForScopedData($scopedData, $targetPks),
            ];
        }

        // 3. Pertanyaan Seputar Alat Berat (Multi / All PKS)
        if (
            str_contains($q, 'alat berat') ||
            str_contains($q, 'excavator') ||
            str_contains($q, 'traktor') ||
            str_contains($q, 'breakdown') ||
            str_contains($q, 'maintenance') ||
            str_contains($q, 'bbm') ||
            str_contains($q, 'hm') ||
            str_contains($q, 'jam kerja')
        ) {
            $text = "### 🚜 **Laporan Operasional Alat Berat SIMOLI**\n";
            $text .= "📅 **Periode:** {$periodeLabel}\n\n";

            $text .= "📦 **Ketersediaan Unit Alat Berat:**\n";
            $text .= "| Status | Jumlah Unit | Persentase |\n";
            $text .= "| :--- | :---: | :---: |\n";
            $totalUnit = max(1, $ab['total_unit']);
            $text .= "| 🟢 **Operational (Ready)** | **{$ab['operational']}** Unit | " . round(($ab['operational'] / $totalUnit) * 100) . "% |\n";
            $text .= "| 🟡 **Maintenance (Perawatan)** | **{$ab['maintenance']}** Unit | " . round(($ab['maintenance'] / $totalUnit) * 100) . "% |\n";
            $text .= "| 🔴 **Breakdown (Rusak)** | **{$ab['breakdown']}** Unit | " . round(($ab['breakdown'] / $totalUnit) * 100) . "% |\n";
            $text .= "| 🔵 **Rolling (Mutasi)** | **{$ab['rolling']}** Unit | " . round(($ab['rolling'] / $totalUnit) * 100) . "% |\n";
            $text .= "| **Total Populasi Unit** | **{$ab['total_unit']}** Unit | 100% |\n\n";

            $text .= "⏱️ **Kinerja Jam Kerja & Bahan Bakar ({$periodeLabel}):**\n";
            $text .= "- 🕒 **Total Jam Kerja (HM):** " . number_format($ab['total_hm'], 2, ',', '.') . " Jam\n";
            $text .= "- ⛽ **Total Konsumsi BBM:** " . number_format($ab['total_bbm'], 2, ',', '.') . " Liter\n";
            $text .= "- 📝 **Total Log Shift Masuk:** " . number_format($ab['total_log']) . " Laporan\n\n";

            if ($ab['breakdown'] > 0) {
                $text .= "⚠️ **Perhatian TEP:** Terdapat **{$ab['breakdown']} unit** alat berat berstatus *Breakdown* yang memerlukan koordinasi perbaikan segera agar tidak menghambat aplikasi limbah di lapangan.";
            }

            return [
                'text' => $text,
                'badges' => [
                    "Ready: {$ab['operational']}",
                    "Breakdown: {$ab['breakdown']}",
                    "Total HM: " . number_format($ab['total_hm'], 1) . " Jam",
                ],
                'actions' => $this->buildActionsForScopedData($scopedData, null),
            ];
        }

        // 4. Pertanyaan Seputar Pemeliharaan (Multi / All PKS)
        if (
            str_contains($q, 'pemeliharaan') ||
            str_contains($q, 'rawat') ||
            str_contains($q, 'kolam') ||
            str_contains($q, 'long bed') ||
            str_contains($q, 'hk') ||
            str_contains($q, 'tenaga kerja')
        ) {
            $text = "### 🛠️ **Laporan Pemeliharaan Kolam IPAL & Bed**\n";
            $text .= "📅 **Periode:** {$periodeLabel}\n\n";

            $text .= "📊 **Realisasi Pemeliharaan Fisik:**\n";
            $text .= "- 🟫 **Flat Bed Dibersihkan/Dirawat:** " . number_format($m['flat_bed'], 0, ',', '.') . " Bed\n";
            $text .= "- 🟩 **Long Bed Dirawat:** " . number_format($m['long_bed'], 0, ',', '.') . " Bed\n";
            $text .= "- 👷 **Penggunaan Tenaga Kerja (HK):** " . number_format($m['jumlah_hk'], 0, ',', '.') . " HK\n";
            $text .= "- 🚜 **Metode Mekanis (Alat Berat):** " . number_format($m['mekanis']) . " Kegiatan\n";
            $text .= "- 🧤 **Metode Manual:** " . number_format($m['manual']) . " Kegiatan\n";
            $text .= "- 📋 **Total Catatan:** " . number_format($m['records']) . " Transaksi\n\n";

            return [
                'text' => $text,
                'badges' => [
                    'Flat Bed: ' . number_format($m['flat_bed']) . ' Bed',
                    'Long Bed: ' . number_format($m['long_bed']) . ' Bed',
                    'Tenaga: ' . number_format($m['jumlah_hk']) . ' HK',
                ],
                'actions' => $this->buildActionsForScopedData($scopedData, null),
            ];
        }

        // 5. Default / Rekap Pengaliran (Multi / All PKS)
        $text = "### 💧 **Laporan Pengaliran Limbah Land Application (LA)**\n";
        $text .= "📅 **Periode:** {$periodeLabel}\n";
        $text .= "🏢 **Cakupan:** 12 Unit Mill PKS\n\n";

        $text .= "📊 **Akumulasi Kinerja Pengaliran:**\n";
        $text .= "- 🧪 **Limbah Dihasilkan (PKS):** " . number_format($p['vol_dihasilkan'], 2, ',', '.') . " m³\n";
        $text .= "- 🌊 **Limbah Dialirkan (LA):** " . number_format($p['vol_dialirkan'], 2, ',', '.') . " m³\n";
        $persenDialirkan = $p['vol_dihasilkan'] > 0 ? round(($p['vol_dialirkan'] / $p['vol_dihasilkan']) * 100, 1) : 0;
        $text .= "- 📈 **Rasio Pengaliran Limbah:** **{$persenDialirkan}%**\n";
        $text .= "- 🌱 **Aplikasi Flat Bed:** " . number_format($p['flat_bed'], 0, ',', '.') . " Bed\n";
        $text .= "- 🗺️ **Luas Area Aplikasi:** " . number_format($p['luas_area'], 2, ',', '.') . " Hektar\n";
        $text .= "- 📋 **Total Entri Data:** " . number_format($p['records']) . " Transaksi\n\n";

        // Tabel Breakdown per PKS jika ada data
        if (!empty($scopedData['pks_breakdown']) && $p['records'] > 0) {
            $text .= "🏢 **Rekap Singkat per PKS:**\n";
            $text .= "| PKS | Vol Dihasilkan | Vol Dialirkan | Flat Bed | Luas |\n";
            $text .= "| :--- | -: | -: | -: | -: |\n";
            foreach (array_slice($scopedData['pks_breakdown'], 0, 12) as $row) {
                if ($row['vol_dialirkan'] > 0 || $row['vol_dihasilkan'] > 0 || $row['flat_bed_pengaliran'] > 0) {
                    $vh = number_format($row['vol_dihasilkan'], 0, ',', '.');
                    $va = number_format($row['vol_dialirkan'], 0, ',', '.');
                    $fb = number_format($row['flat_bed_pengaliran'], 0, ',', '.');
                    $la = number_format($row['luas_area'], 1, ',', '.');
                    $text .= "| **{$row['akro']}** | {$vh} m³ | {$va} m³ | {$fb} Bed | {$la} Ha |\n";
                }
            }
            $text .= "\n";
        }

        return [
            'text' => $text,
            'badges' => $badges,
            'actions' => $this->buildActionsForScopedData($scopedData, null),
        ];
    }

    /**
     * Helper Membuat Badges Stat Ringkas
     */
    protected function buildBadgesForScopedData(array $scopedData, ?Pks $targetPks): array
    {
        $p = $scopedData['pengaliran'];
        $badges = [];

        if ($p['vol_dihasilkan'] > 0) {
            $badges[] = 'Vol Dihasilkan: ' . number_format($p['vol_dihasilkan'], 0, ',', '.') . ' m³';
        }
        if ($p['vol_dialirkan'] > 0) {
            $badges[] = 'Vol Dialirkan: ' . number_format($p['vol_dialirkan'], 0, ',', '.') . ' m³';
        }
        if ($p['flat_bed'] > 0) {
            $badges[] = 'Aplikasi: ' . number_format($p['flat_bed'], 0, ',', '.') . ' Bed';
        }
        if ($p['luas_area'] > 0) {
            $badges[] = 'Luas: ' . number_format($p['luas_area'], 1, ',', '.') . ' Ha';
        }

        if (empty($badges)) {
            $badges[] = 'Periode: ' . $scopedData['periode_label'];
            if ($targetPks) {
                $badges[] = 'PKS: ' . $targetPks->akro;
            }
        }

        return $badges;
    }

    /**
     * Helper Membuat Tombol Action Rekomendasi
     */
    protected function buildActionsForScopedData(array $scopedData, ?Pks $targetPks): array
    {
        $actions = [];

        if ($targetPks) {
            $actions[] = [
                'label' => "💧 Lihat Detail Pengaliran PKS {$targetPks->akro}",
                'query' => "rekap data pengaliran pks {$targetPks->nama} bulan ini",
            ];
            $actions[] = [
                'label' => "🛠️ Cek Pemeliharaan PKS {$targetPks->akro}",
                'query' => "rekap pemeliharaan pks {$targetPks->nama} bulan ini",
            ];
        } else {
            $actions[] = [
                'label' => '🚨 Cek PKS Belum Input Hari Ini',
                'query' => 'pks mana saja yang belum input data hari ini?',
            ];
            $actions[] = [
                'label' => '🚜 Cek Kesiapan Alat Berat',
                'query' => 'bagaimana status ketersediaan alat berat?',
            ];
        }

        return $actions;
    }

    /**
     * Kirim Pengingat WhatsApp ke Asisten PKS untuk Laporan yang Belum Diinput
     */
    public function sendReminderForPks(int $idPks, ?string $date = null): array
    {
        $pks = Pks::find($idPks);
        if (!$pks) {
            return ['success' => false, 'message' => 'Data PKS tidak ditemukan.'];
        }

        if (empty($pks->wa_asisten)) {
            return [
                'success' => false,
                'message' => "Nomor WhatsApp Asisten untuk PKS {$pks->nama} ({$pks->akro}) belum diatur di menu Data Pengguna/PKS.",
            ];
        }

        $targetDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $tglFormatted = Carbon::parse($targetDate)->locale('id')->translatedFormat('l, d F Y');

        $hasPengaliran = Pengaliran::where('id_pks', $pks->id_pks)->whereDate('tanggal', $targetDate)->exists();
        $hasPemeliharaan = Pemeliharaan::where('id_pks', $pks->id_pks)->whereDate('tanggal', $targetDate)->exists();
        $hasAlatBerat = MonitoringAlatBerat::where('id_pks', $pks->id_pks)->whereDate('tanggal', $targetDate)->exists();

        $missingList = [];
        if (!$hasPengaliran) $missingList[] = "• *Laporan Pengaliran Land Aplikasi (LA)*";
        if (!$hasPemeliharaan) $missingList[] = "• *Laporan Pemeliharaan Kolam IPAL & Bed*";
        if (!$hasAlatBerat) $missingList[] = "• *Laporan Operasional Alat Berat & Aplikasi*";

        if (empty($missingList)) {
            return [
                'success' => true,
                'message' => "PKS {$pks->nama} sudah lengkap menginput seluruh data untuk tanggal {$tglFormatted}.",
            ];
        }

        $asistenName = !empty($pks->asisten) ? $pks->asisten : 'Asisten ' . $pks->nama;
        $missingText = implode("\n", $missingList);

        $message = "⚠️ *SIMOLI PTPN IV - PENGINGAT INPUT LAPORAN HARIAN*\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━\n"
            . "Yth. Bapak/Ibu *{$asistenName}*\n"
            . "Asisten Unit PKS *{$pks->nama} ({$pks->akro})*\n\n"
            . "Pemberitahuan dari *Admin TEP SIMOLI* bahwa hingga saat ini laporan berikut *BELUM DIINPUT* untuk tanggal *{$tglFormatted}*:\n\n"
            . "{$missingText}\n\n"
            . "Mohon kesediaannya untuk segera mengarahkan tim operator dan petugas lapangan agar menginput data harian melalui aplikasi *SIMOLI* sebelum batas waktu operasional hari ini.\n\n"
            . "🌐 Akses SIMOLI: https://simoli.ptpn4.co.id\n"
            . "Terima kasih atas kerja sama dan dedikasinya.\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━\n"
            . "_SIMOLI - Bagian Teknik & Pengolahan (TEP) PTPN IV_";

        $res = $this->waService->sendMessage($pks->wa_asisten, $message);

        return [
            'success' => $res['success'],
            'message' => $res['success']
                ? "Pengingat WhatsApp berhasil dikirim ke Asisten {$pks->nama} ({$pks->wa_asisten})."
                : "Gagal mengirim ke {$pks->nama}: " . ($res['message'] ?? 'Error gateway'),
            'pks' => $pks->nama,
            'phone' => $pks->wa_asisten,
        ];
    }

    /**
     * Kirim Pengingat WhatsApp ke Semua PKS yang Belum Input Hari Ini
     */
    public function sendBatchReminderToAllMissing(?string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $audit = $this->getDailyAudit($targetDate);

        $sentCount = 0;
        $failedCount = 0;
        $skippedNoPhoneCount = 0;
        $details = [];

        // Gabungkan semua PKS yang memiliki minimal 1 modul belum diinput
        $allPending = array_merge($audit['partially_completed'], $audit['unsubmitted']);
        $uniquePending = [];
        foreach ($allPending as $p) {
            $uniquePending[$p['id_pks']] = $p;
        }

        if (empty($uniquePending)) {
            return [
                'success' => true,
                'message' => 'Luar biasa! Seluruh 12 Unit PKS telah menginput data lengkap hari ini. Tidak ada notifikasi yang perlu dikirim.',
                'sent_count' => 0,
                'failed_count' => 0,
                'details' => [],
            ];
        }

        foreach ($uniquePending as $item) {
            $pks = Pks::find($item['id_pks']);
            if (!$pks || empty($pks->wa_asisten)) {
                $skippedNoPhoneCount++;
                $details[] = [
                    'pks' => $item['nama'],
                    'status' => 'skipped',
                    'message' => 'Nomor WhatsApp Asisten belum diatur',
                ];
                continue;
            }

            $res = $this->sendReminderForPks($pks->id_pks, $targetDate);
            if ($res['success']) {
                $sentCount++;
                $details[] = [
                    'pks' => $item['nama'],
                    'status' => 'sent',
                    'phone' => $pks->wa_asisten,
                    'message' => 'Terkirim',
                ];
            } else {
                $failedCount++;
                $details[] = [
                    'pks' => $item['nama'],
                    'status' => 'failed',
                    'phone' => $pks->wa_asisten,
                    'message' => $res['message'],
                ];
            }
        }

        return [
            'success' => ($sentCount > 0 || $failedCount === 0),
            'message' => "Proses pengiriman selesai: {$sentCount} berhasil dikirim, {$failedCount} gagal, {$skippedNoPhoneCount} dilewati (nomor WA belum diisi).",
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'skipped_count' => $skippedNoPhoneCount,
            'details' => $details,
        ];
    }
}
