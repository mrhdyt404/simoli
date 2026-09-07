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
        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));
        $minggu = $request->input('minggu', 'all');

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Available years
        $years = Pengaliran::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($years->isEmpty()) {
            $years = collect([date('Y')]);
        }
        if (!$years->contains(date('Y'))) {
            $years->prepend(date('Y'));
        }

        // PKS list for filter dropdown
        $allPks = Pks::orderBy('id_pks')->get();

        // Standard official sequence matching the report layout
        $pksOrder = ['TPU', 'TME', 'SGO', 'SPA', 'SGH', 'SBT', 'LDA', 'TAN', 'TER', 'STA', 'SRO', 'SIN'];

        // Target PKS list for table
        $pksQuery = Pks::query();
        if ($user->isUnit()) {
            $pksQuery->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $pksQuery->where('id_pks', $request->id_pks);
        }
        $pksList = $pksQuery->get()->sortBy(function ($pks) use ($pksOrder) {
            $idx = array_search(strtoupper($pks->akro ?? ''), $pksOrder);
            return $idx === false ? 999 : $idx;
        })->values();

        // Month start & end dates
        $dateStartMonth = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $dateEndMonth = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        $totalDaysInMonth = $dateEndMonth->day;

        $isCurrentMonth = ($tahun == (int) date('Y') && $bulan == (int) date('n'));
        $todayDay = (int) date('j');

        $tglAwalParam = $request->input('tgl_awal');
        $tglAkhirParam = $request->input('tgl_akhir');

        // Resolve active week if $minggu is 'custom' or date range is provided
        if ($minggu === 'custom' || ($request->filled('tgl_awal') && $request->filled('tgl_akhir'))) {
            $activeWeek = 'custom';
            $weekStart = Carbon::parse($tglAwalParam)->startOfDay();
            $weekEnd = Carbon::parse($tglAkhirParam)->endOfDay();
            
            $tglAwal = $weekStart->format('Y-m-d');
            $tglAkhir = $weekEnd->format('Y-m-d');
            
            $weekLabel = "Periode (" . $weekStart->format('d') . " " . $namaBulan[(int)$weekStart->format('n')] . " - " . $weekEnd->format('d') . " " . $namaBulan[(int)$weekEnd->format('n')] . " " . $weekEnd->format('Y') . ")";
        } elseif ($minggu === 'all' || empty($minggu)) {
            if ($isCurrentMonth) {
                // Determine current week by today's date
                if ($todayDay <= 7) $activeWeek = '1';
                elseif ($todayDay <= 14) $activeWeek = '2';
                elseif ($todayDay <= 21) $activeWeek = '3';
                elseif ($todayDay <= 28) $activeWeek = '4';
                else $activeWeek = '5';
            } else {
                // For past months, find the week containing the latest transaction in that month
                $latestPengaliran = Pengaliran::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->orderBy('tanggal', 'desc')->first();
                $latestMonitoring = \App\Models\MonitoringAlatBerat::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->orderBy('tanggal', 'desc')->first();
                
                $latestTgl = null;
                if ($latestPengaliran && $latestPengaliran->tanggal) {
                    $latestTgl = Carbon::parse($latestPengaliran->tanggal);
                }
                if ($latestMonitoring && $latestMonitoring->tanggal) {
                    $cMon = Carbon::parse($latestMonitoring->tanggal);
                    if (!$latestTgl || $cMon->greaterThan($latestTgl)) {
                        $latestTgl = $cMon;
                    }
                }

                if ($latestTgl) {
                    $day = $latestTgl->day;
                    if ($day <= 7) $activeWeek = '1';
                    elseif ($day <= 14) $activeWeek = '2';
                    elseif ($day <= 21) $activeWeek = '3';
                    elseif ($day <= 28) $activeWeek = '4';
                    else $activeWeek = '5';
                } else {
                    $activeWeek = '4';
                }
            }
        } else {
            $activeWeek = (string) $minggu;
        }

        // Calculate week range for presets 1..5
        if ($activeWeek === '1') {
            $weekStart = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
            $weekEnd = Carbon::createFromDate($tahun, $bulan, min(7, $totalDaysInMonth))->endOfDay();
            $weekLabel = "Minggu 1 (01 - " . sprintf("%02d", min(7, $totalDaysInMonth)) . " " . $namaBulan[$bulan] . " " . $tahun . ")";
            $tglAwal = $weekStart->format('Y-m-d');
            $tglAkhir = $weekEnd->format('Y-m-d');
        } elseif ($activeWeek === '2') {
            $weekStart = Carbon::createFromDate($tahun, $bulan, 8)->startOfDay();
            $weekEnd = Carbon::createFromDate($tahun, $bulan, min(14, $totalDaysInMonth))->endOfDay();
            $weekLabel = "Minggu 2 (08 - " . sprintf("%02d", min(14, $totalDaysInMonth)) . " " . $namaBulan[$bulan] . " " . $tahun . ")";
            $tglAwal = $weekStart->format('Y-m-d');
            $tglAkhir = $weekEnd->format('Y-m-d');
        } elseif ($activeWeek === '3') {
            $weekStart = Carbon::createFromDate($tahun, $bulan, 15)->startOfDay();
            $weekEnd = Carbon::createFromDate($tahun, $bulan, min(21, $totalDaysInMonth))->endOfDay();
            $weekLabel = "Minggu 3 (15 - " . sprintf("%02d", min(21, $totalDaysInMonth)) . " " . $namaBulan[$bulan] . " " . $tahun . ")";
            $tglAwal = $weekStart->format('Y-m-d');
            $tglAkhir = $weekEnd->format('Y-m-d');
        } elseif ($activeWeek === '4') {
            $weekStart = Carbon::createFromDate($tahun, $bulan, 22)->startOfDay();
            $weekEnd = Carbon::createFromDate($tahun, $bulan, min(28, $totalDaysInMonth))->endOfDay();
            $weekLabel = "Minggu 4 (22 - " . sprintf("%02d", min(28, $totalDaysInMonth)) . " " . $namaBulan[$bulan] . " " . $tahun . ")";
            $tglAwal = $weekStart->format('Y-m-d');
            $tglAkhir = $weekEnd->format('Y-m-d');
        } elseif ($activeWeek === '5') {
            $weekStart = Carbon::createFromDate($tahun, $bulan, 29)->startOfDay();
            $weekEnd = Carbon::createFromDate($tahun, $bulan, $totalDaysInMonth)->endOfDay();
            $weekLabel = "Minggu 5 (29 - " . sprintf("%02d", $totalDaysInMonth) . " " . $namaBulan[$bulan] . " " . $tahun . ")";
            $tglAwal = $weekStart->format('Y-m-d');
            $tglAkhir = $weekEnd->format('Y-m-d');
        }

        // Header Periode label (matching the blue pill in the image, e.g. "01 JULI 2026 – 30 JULI 2026")
        $periodeLabel = sprintf(
            "%02d %s %d – %02d %s %d",
            1,
            strtoupper($namaBulan[$bulan]),
            $tahun,
            $totalDaysInMonth,
            strtoupper($namaBulan[$bulan]),
            $tahun
        );

        // Fetch all month records from Pengaliran
        $pengaliranQuery = Pengaliran::with('pks')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);

        // Fetch all month records from MonitoringAlatBerat
        $monitoringQuery = \App\Models\MonitoringAlatBerat::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);

        if ($user->isUnit()) {
            $pengaliranQuery->where('id_pks', $user->id_pks);
            $monitoringQuery->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $pengaliranQuery->where('id_pks', $request->id_pks);
            $monitoringQuery->where('id_pks', $request->id_pks);
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

        foreach ($pksList as $pks) {
            $akro = strtoupper($pks->akro ?? '');
            // Skip district or office if not specifically filtering for it
            if (in_array($akro, ['TEP', 'DTM', 'DBR']) && !$request->filled('id_pks')) {
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

        // Detailed logs grouped by PKS
        $dataByPks = $allPengaliranRecords->groupBy(function ($item) {
            return $item->pks ? $item->pks->nama : 'N/A';
        });

        $data = $allPengaliranRecords;
        $allMonthRecords = $allPengaliranRecords;

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

