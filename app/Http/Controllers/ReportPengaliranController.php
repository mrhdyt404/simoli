<?php

namespace App\Http\Controllers;

use App\Models\Pengaliran;
use App\Models\Pks;
use App\Models\Rencana;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportPengaliranController extends Controller
{
    /**
     * Default baseline capacity (Total Bed) for the 12 PKS mills
     */
    private array $defaultTotalBeds = [
        'TPU' => 15112,
        'TME' => 6768,
        'SGO' => 3382,
        'SPA' => 7490,
        'SGH' => 3460,
        'SBT' => 15370,
        'LDA' => 6985,
        'TAN' => 10344,
        'TER' => 5691,
        'STA' => 8370,
        'SRO' => 6297,
        'SIN' => 4657,
    ];

    /**
     * Color scheme per PKS for icons
     */
    private array $pksColors = [
        'TPU' => '#1e40af', // Blue
        'TME' => '#65a30d', // Lime
        'SGO' => '#7c3aed', // Purple
        'SPA' => '#0284c7', // Sky
        'SGH' => '#d97706', // Amber
        'SBT' => '#dc2626', // Red
        'LDA' => '#4f46e5', // Indigo
        'TAN' => '#0d9488', // Teal
        'TER' => '#ea580c', // Orange
        'STA' => '#15803d', // Green
        'SRO' => '#16a34a', // Emerald
        'SIN' => '#9333ea', // Violet
    ];

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

    /**
     * Format a collection of records into distinct comma-separated string for block or bak
     */
    private function formatDistinctList($items, string $field): string
    {
        $collected = [];
        foreach ($items as $item) {
            $val = trim((string) ($item->{$field} ?? ''));
            if ($val !== '' && $val !== '-') {
                // Split by comma, slash, or space if multiple values recorded in one row
                $parts = preg_split('/[\/,\s]+/', $val, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if ($p !== '' && $p !== '-' && !in_array($p, $collected)) {
                        $collected[] = $p;
                    }
                }
            }
        }

        return empty($collected) ? '-' : implode(', ', $collected);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = (int) $request->input('tahun', date('Y'));

        // Default bulan: 'all' (Semua Bulan) kecuali jika dipilih bulan spesifik (1..12)
        $bulanInput = $request->input('bulan');
        if ($request->has('bulan') && $bulanInput !== 'all' && $bulanInput !== '' && is_numeric($bulanInput)) {
            $bulan = (int) $bulanInput;
        } else {
            $bulan = 'all';
        }

        $minggu = $request->input('minggu', 'all');

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Available years
        $years = Pengaliran::selectRaw('YEAR(tanggal) as tahun')
            ->whereNotNull('tanggal')
            ->whereRaw('YEAR(tanggal) >= 2000')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($years->isEmpty()) {
            $years = collect([date('Y')]);
        }
        if (!$years->contains(date('Y'))) {
            $years->prepend(date('Y'));
        }

        // Standard official sequence matching the report layout
        $pksOrder = ['TPU', 'TME', 'SGO', 'SPA', 'SGH', 'SBT', 'LDA', 'TAN', 'TER', 'STA', 'SRO', 'SIN'];

        // All operational PKS list
        $allOperationalPks = Pks::whereNotIn('akro', ['TEP', 'DTM', 'DBR'])
            ->get()
            ->sortBy(function ($pks) use ($pksOrder) {
                $idx = array_search(strtoupper($pks->akro ?? ''), $pksOrder);
                return $idx === false ? 999 : $idx;
            })->values();

        // PKS list for dropdown filter in view
        $allPks = ($user && $user->isUnit()) 
            ? Pks::where('id_pks', $user->id_pks)->get() 
            : $allOperationalPks;

        // Target PKS list to display in Report based on role & filter
        if ($user && $user->isUnit()) {
            // Unit user: HANYA tampilkan data sesuai unitnya sendiri
            $targetPksList = $allOperationalPks->where('id_pks', $user->id_pks)->values();
            if ($targetPksList->isEmpty()) {
                $targetPksList = Pks::where('id_pks', $user->id_pks)->get();
            }
        } elseif ($request->filled('id_pks')) {
            // Admin memilih PKS tertentu: tampilkan hanya PKS yang dipilih
            $targetPksList = $allOperationalPks->where('id_pks', (int) $request->id_pks)->values();
            if ($targetPksList->isEmpty()) {
                $targetPksList = Pks::where('id_pks', (int) $request->id_pks)->get();
            }
        } else {
            // Admin memilih "Semua PKS": tampilkan seluruh 12 PKS
            $targetPksList = $allOperationalPks;
        }

        $pksList = $targetPksList;
        $targetPksIds = $targetPksList->pluck('id_pks')->toArray();

        // Custom date range params (support both tgl_awal/tgl_akhir and dari_tanggal/sampai_tanggal)
        $tglAwalParam = $request->input('tgl_awal', $request->input('dari_tanggal'));
        $tglAkhirParam = $request->input('tgl_akhir', $request->input('sampai_tanggal'));

        // Handle Date Range & Periods
        if ($minggu === 'custom' || (!empty($tglAwalParam) && !empty($tglAkhirParam))) {
            $activeWeek = 'custom';
            $weekStart = Carbon::parse($tglAwalParam)->startOfDay();
            $weekEnd = Carbon::parse($tglAkhirParam)->endOfDay();
            
            $tglAwal = $weekStart->format('Y-m-d');
            $tglAkhir = $weekEnd->format('Y-m-d');
            
            $weekLabel = "Periode (" . $weekStart->format('d/m/Y') . " - " . $weekEnd->format('d/m/Y') . ")";
            $periodeLabel = sprintf(
                "%02d %s %d – %02d %s %d",
                $weekStart->day,
                strtoupper($namaBulan[(int)$weekStart->month]),
                $weekStart->year,
                $weekEnd->day,
                strtoupper($namaBulan[(int)$weekEnd->month]),
                $weekEnd->year
            );
        } elseif ($bulan === 'all') {
            // Mode Semua Bulan (Default)
            $activeWeek = (string) ($minggu ?: 'all');
            $isCurrentYear = ($tahun == (int) date('Y'));
            $currentMonth = (int) date('n');
            $todayDay = (int) date('j');
            
            // Reference month for week calculations
            $refMonth = $isCurrentYear ? $currentMonth : 12;
            $refTotalDays = Carbon::createFromDate($tahun, $refMonth, 1)->endOfMonth()->day;

            if ($activeWeek === '1') {
                $weekStart = Carbon::createFromDate($tahun, $refMonth, 1)->startOfDay();
                $weekEnd = Carbon::createFromDate($tahun, $refMonth, min(7, $refTotalDays))->endOfDay();
                $weekLabel = "Minggu 1 (" . sprintf("%02d", 1) . "-" . sprintf("%02d", min(7, $refTotalDays)) . " " . $namaBulan[$refMonth] . " " . $tahun . ")";
            } elseif ($activeWeek === '2') {
                $weekStart = Carbon::createFromDate($tahun, $refMonth, 8)->startOfDay();
                $weekEnd = Carbon::createFromDate($tahun, $refMonth, min(14, $refTotalDays))->endOfDay();
                $weekLabel = "Minggu 2 (08-" . sprintf("%02d", min(14, $refTotalDays)) . " " . $namaBulan[$refMonth] . " " . $tahun . ")";
            } elseif ($activeWeek === '3') {
                $weekStart = Carbon::createFromDate($tahun, $refMonth, 15)->startOfDay();
                $weekEnd = Carbon::createFromDate($tahun, $refMonth, min(21, $refTotalDays))->endOfDay();
                $weekLabel = "Minggu 3 (15-" . sprintf("%02d", min(21, $refTotalDays)) . " " . $namaBulan[$refMonth] . " " . $tahun . ")";
            } elseif ($activeWeek === '4') {
                $weekStart = Carbon::createFromDate($tahun, $refMonth, 22)->startOfDay();
                $weekEnd = Carbon::createFromDate($tahun, $refMonth, min(28, $refTotalDays))->endOfDay();
                $weekLabel = "Minggu 4 (22-" . sprintf("%02d", min(28, $refTotalDays)) . " " . $namaBulan[$refMonth] . " " . $tahun . ")";
            } elseif ($activeWeek === '5') {
                $weekStart = Carbon::createFromDate($tahun, $refMonth, 29)->startOfDay();
                $weekEnd = Carbon::createFromDate($tahun, $refMonth, $refTotalDays)->endOfDay();
                $weekLabel = "Minggu 5 (29-" . sprintf("%02d", $refTotalDays) . " " . $namaBulan[$refMonth] . " " . $tahun . ")";
            } else {
                // Minggu Aktif (Otomatis)
                if ($isCurrentYear) {
                    $wDay = $todayDay;
                    if ($wDay <= 7) $wNum = 1;
                    elseif ($wDay <= 14) $wNum = 2;
                    elseif ($wDay <= 21) $wNum = 3;
                    elseif ($wDay <= 28) $wNum = 4;
                    else $wNum = 5;
                    
                    $wStartDay = ($wNum - 1) * 7 + 1;
                    $wEndDay = ($wNum === 5) ? $refTotalDays : min($wNum * 7, $refTotalDays);
                    $weekStart = Carbon::createFromDate($tahun, $refMonth, $wStartDay)->startOfDay();
                    $weekEnd = Carbon::createFromDate($tahun, $refMonth, $wEndDay)->endOfDay();
                    $weekLabel = "Minggu $wNum (" . sprintf("%02d", $wStartDay) . "-" . sprintf("%02d", $wEndDay) . " " . $namaBulan[$refMonth] . " " . $tahun . ")";
                } else {
                    $weekStart = Carbon::createFromDate($tahun, 1, 1)->startOfDay();
                    $weekEnd = Carbon::createFromDate($tahun, 12, 31)->endOfDay();
                    $weekLabel = "Akumulasi Tahun $tahun";
                }
            }

            $tglAwal = $weekStart->format('Y-m-d');
            $tglAkhir = $weekEnd->format('Y-m-d');
            $periodeLabel = "01 JANUARI $tahun – 31 DESEMBER $tahun";

        } else {
            // Mode Bulan Spesifik (1..12)
            $dateStartMonth = Carbon::createFromDate($tahun, (int)$bulan, 1)->startOfMonth();
            $dateEndMonth = Carbon::createFromDate($tahun, (int)$bulan, 1)->endOfMonth();
            $totalDaysInMonth = $dateEndMonth->day;
            $isCurrentMonth = ($tahun == (int) date('Y') && (int)$bulan == (int) date('n'));
            $todayDay = (int) date('j');

            $activeWeek = (string) $minggu;
            if ($activeWeek === 'all' || empty($activeWeek)) {
                if ($isCurrentMonth) {
                    if ($todayDay <= 7) $activeWeek = '1';
                    elseif ($todayDay <= 14) $activeWeek = '2';
                    elseif ($todayDay <= 21) $activeWeek = '3';
                    elseif ($todayDay <= 28) $activeWeek = '4';
                    else $activeWeek = '5';
                } else {
                    $activeWeek = '4';
                }
            }

            if ($activeWeek === '1') {
                $weekStart = Carbon::createFromDate($tahun, (int)$bulan, 1)->startOfDay();
                $weekEnd = Carbon::createFromDate($tahun, (int)$bulan, min(7, $totalDaysInMonth))->endOfDay();
                $weekLabel = "Minggu 1 (01 - " . sprintf("%02d", min(7, $totalDaysInMonth)) . " " . $namaBulan[(int)$bulan] . " " . $tahun . ")";
            } elseif ($activeWeek === '2') {
                $weekStart = Carbon::createFromDate($tahun, (int)$bulan, 8)->startOfDay();
                $weekEnd = Carbon::createFromDate($tahun, (int)$bulan, min(14, $totalDaysInMonth))->endOfDay();
                $weekLabel = "Minggu 2 (08 - " . sprintf("%02d", min(14, $totalDaysInMonth)) . " " . $namaBulan[(int)$bulan] . " " . $tahun . ")";
            } elseif ($activeWeek === '3') {
                $weekStart = Carbon::createFromDate($tahun, (int)$bulan, 15)->startOfDay();
                $weekEnd = Carbon::createFromDate($tahun, (int)$bulan, min(21, $totalDaysInMonth))->endOfDay();
                $weekLabel = "Minggu 3 (15 - " . sprintf("%02d", min(21, $totalDaysInMonth)) . " " . $namaBulan[(int)$bulan] . " " . $tahun . ")";
            } elseif ($activeWeek === '4') {
                $weekStart = Carbon::createFromDate($tahun, (int)$bulan, 22)->startOfDay();
                $weekEnd = Carbon::createFromDate($tahun, (int)$bulan, min(28, $totalDaysInMonth))->endOfDay();
                $weekLabel = "Minggu 4 (22 - " . sprintf("%02d", min(28, $totalDaysInMonth)) . " " . $namaBulan[(int)$bulan] . " " . $tahun . ")";
            } elseif ($activeWeek === '5') {
                $weekStart = Carbon::createFromDate($tahun, (int)$bulan, 29)->startOfDay();
                $weekEnd = Carbon::createFromDate($tahun, (int)$bulan, $totalDaysInMonth)->endOfDay();
                $weekLabel = "Minggu 5 (29 - " . sprintf("%02d", $totalDaysInMonth) . " " . $namaBulan[(int)$bulan] . " " . $tahun . ")";
            }

            $tglAwal = $weekStart->format('Y-m-d');
            $tglAkhir = $weekEnd->format('Y-m-d');
            $periodeLabel = sprintf(
                "%02d %s %d – %02d %s %d",
                1,
                strtoupper($namaBulan[(int)$bulan]),
                $tahun,
                $totalDaysInMonth,
                strtoupper($namaBulan[(int)$bulan]),
                $tahun
            );
        }

        // Fetch records from Pengaliran for the target PKS
        $pengaliranQuery = Pengaliran::with('pks')
            ->whereYear('tanggal', $tahun)
            ->whereIn('id_pks', $targetPksIds);

        // Fetch records from MonitoringAlatBerat for the target PKS
        $monitoringQuery = \App\Models\MonitoringAlatBerat::whereYear('tanggal', $tahun)
            ->whereIn('id_pks', $targetPksIds);

        if ($bulan !== 'all' && is_numeric($bulan)) {
            $pengaliranQuery->whereMonth('tanggal', (int) $bulan);
            $monitoringQuery->whereMonth('tanggal', (int) $bulan);
        }

        $allPengaliranRecords = $pengaliranQuery->orderBy('tanggal', 'asc')->get();
        $allMonitoringRecords = $monitoringQuery->orderBy('tanggal', 'asc')->get();

        // Fetch Rencana for this year to get configured total bed
        $rencanaList = Rencana::where('tahun', $tahun)->get()->keyBy('id_pks');

        // Combined distinct list formatter
        $formatCombinedList = function($pRecords, $pField, $mRecords, $mField) {
            $collected = [];
            foreach ($pRecords as $item) {
                $val = trim((string) ($item->{$pField} ?? ''));
                if ($val !== '' && $val !== '-') {
                    $parts = preg_split('/[\/,\s]+/', $val, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($parts as $p) {
                        $p = trim($p);
                        if ($p !== '' && $p !== '-' && !in_array($p, $collected)) $collected[] = $p;
                    }
                }
            }
            foreach ($mRecords as $item) {
                $val = trim((string) ($item->{$mField} ?? ''));
                if ($val !== '' && $val !== '-') {
                    $parts = preg_split('/[\/,\s]+/', $val, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($parts as $p) {
                        $p = trim($p);
                        if ($p !== '' && $p !== '-' && !in_array($p, $collected)) $collected[] = $p;
                    }
                }
            }
            return empty($collected) ? '-' : implode(', ', $collected);
        };

        // Build Recap rows matching the table format in the image
        $rekapPengaliran = [];
        $no = 1;

        foreach ($targetPksList as $pks) {
            $akro = strtoupper($pks->akro ?? '');
            // Skip district or office if showing all PKS
            if (in_array($akro, ['TEP', 'DTM', 'DBR']) && count($targetPksList) > 1) {
                continue;
            }

            // Total Bed determination
            $rencana = $rencanaList->get($pks->id_pks);
            if ($rencana && ($rencana->flat_bed > 0 || $rencana->long_bed > 0)) {
                $totalBed = $rencana->flat_bed ?: ($rencana->flat_bed + $rencana->long_bed);
            } else {
                $totalBed = $this->defaultTotalBeds[$akro] ?? 0;
            }

            // Filter records for this PKS for Month
            $pksPengaliranMonth = $allPengaliranRecords->where('id_pks', $pks->id_pks);
            $pksMonitoringMonth = $allMonitoringRecords->where('id_pks', $pks->id_pks);

            // Filter records for this PKS for Week
            $pksPengaliranWeek = $pksPengaliranMonth->filter(function ($item) use ($weekStart, $weekEnd) {
                $tgl = Carbon::parse($item->tanggal);
                return $tgl->betweenIncluded($weekStart, $weekEnd);
            });
            $pksMonitoringWeek = $pksMonitoringMonth->filter(function ($item) use ($weekStart, $weekEnd) {
                $tgl = Carbon::parse($item->tanggal);
                return $tgl->betweenIncluded($weekStart, $weekEnd);
            });

            // Progress Minggu Ini
            $bedMingguIni = (int) $this->numericSum($pksPengaliranWeek, 'flat_bed') + (int) $this->numericSum($pksMonitoringWeek, 'flat_bed');
            $blokMingguIni = $formatCombinedList($pksPengaliranWeek, 'blok', $pksMonitoringWeek, 'lokasi_blok');
            $bakMingguIni = $formatCombinedList($pksPengaliranWeek, 'no_bak', $pksMonitoringWeek, 'no_bak');

            // Progress S.d Bulan Ini
            $bedSdBulanIni = (int) $this->numericSum($pksPengaliranMonth, 'flat_bed') + (int) $this->numericSum($pksMonitoringMonth, 'flat_bed');
            $blokSdBulanIni = $formatCombinedList($pksPengaliranMonth, 'blok', $pksMonitoringMonth, 'lokasi_blok');
            $bakSdBulanIni = $formatCombinedList($pksPengaliranMonth, 'no_bak', $pksMonitoringMonth, 'no_bak');

            // Status & Keterangan matching the visual style in the image
            $latestPengaliran = $pksPengaliranMonth->last();
            $latestMonitoring = $pksMonitoringMonth->last();
            $customKeterangan = '';
            if ($latestPengaliran && trim((string)$latestPengaliran->keterangan) !== '') {
                $customKeterangan = trim($latestPengaliran->keterangan);
            } elseif ($latestMonitoring && trim((string)$latestMonitoring->catatan) !== '') {
                $customKeterangan = trim($latestMonitoring->catatan);
            }

            if ($customKeterangan !== '' && $customKeterangan !== '-') {
                $keteranganText = $customKeterangan;
                $statusType = ($no % 2 === 0) ? 'water' : 'leaf';
            } else {
                if ($no % 2 === 0) {
                    $keteranganText = 'Pengaliran Limbah dari kolam IPAL ke LA berjalan lancar';
                    $statusType = 'water';
                } else {
                    $keteranganText = 'Pengaliran Limbah lancar';
                    $statusType = 'leaf';
                }
            }

            $rekapPengaliran[] = (object) [
                'no' => $no++,
                'id_pks' => $pks->id_pks,
                'pks' => $pks,
                'akro' => $akro,
                'nama' => $pks->nama,
                'color' => $this->pksColors[$akro] ?? '#0284c7',
                'total_bed' => $totalBed,
                'minggu_ini' => (object) [
                    'bed_dialirkan' => $bedMingguIni,
                    'blok' => $blokMingguIni,
                    'bak' => $bakMingguIni,
                ],
                'sd_bulan_ini' => (object) [
                    'bed_dialirkan' => $bedSdBulanIni,
                    'blok' => $blokSdBulanIni,
                    'bak' => $bakSdBulanIni,
                ],
                'keterangan' => $keteranganText,
                'status_type' => $statusType,
            ];
        }

        // Summary totals
        $summary = [
            'count' => $allPengaliranRecords->count() + $allMonitoringRecords->count(),
            'vol_dihasilkan' => $this->numericSum($allPengaliranRecords, 'vol_limbah_dihasilkan'),
            'vol_dialirkan' => $this->numericSum($allPengaliranRecords, 'vol_limbah_dialirkan'),
            'flat_bed' => $this->numericSum($allPengaliranRecords, 'flat_bed') + $this->numericSum($allMonitoringRecords, 'flat_bed'),
            'luas_area' => $this->numericSum($allPengaliranRecords, 'luas_area'),
            'total_bed_all' => collect($rekapPengaliran)->sum('total_bed'),
            'bed_minggu_ini_all' => collect($rekapPengaliran)->sum('minggu_ini.bed_dialirkan'),
            'bed_sd_bulan_all' => collect($rekapPengaliran)->sum('sd_bulan_ini.bed_dialirkan'),
        ];

        // Detailed logs grouped by PKS (filterable by id_pks in tab detail)
        $detailRecords = $allPengaliranRecords;
        if ($request->filled('id_pks')) {
            $detailRecords = $allPengaliranRecords->where('id_pks', (int) $request->id_pks);
        }

        $dataByPks = $detailRecords->groupBy(function ($item) {
            return $item->pks ? $item->pks->nama : 'N/A';
        });

        $data = $detailRecords;
        $allMonthRecords = $detailRecords;

        return view('report.report-pengaliran', compact(
            'allPks',
            'pksList',
            'bulan',
            'tahun',
            'minggu',
            'tglAwal',
            'tglAkhir',
            'activeWeek',
            'weekLabel',
            'periodeLabel',
            'rekapPengaliran',
            'data',
            'allMonthRecords',
            'allPengaliranRecords',
            'allMonitoringRecords',
            'dataByPks',
            'summary',
            'years',
            'namaBulan'
        ));
    }
}

