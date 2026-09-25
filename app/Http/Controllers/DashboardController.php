<?php

namespace App\Http\Controllers;

use App\Models\Pengaliran;
use App\Models\Pemeliharaan;
use App\Models\Pks;
use App\Models\Rencana;
use App\Models\AlatBerat;
use App\Models\MonitoringAlatBerat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
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

    public function index(Request $request)
    {
        $user = Auth::user();
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        if ($user->isOperator()) {
            return redirect()->route('operator.index');
        }

        if ($user->isAdmin()) {
            return $this->adminDashboard($tanggal, $request);
        }

        return $this->unitDashboard($user, $tanggal);
    }

    public function pilihanFilter(Request $request)
    {
        $user  = Auth::user();
        $idPks = $request->input('id_pks', ($user && !$user->isAdmin()) ? $user->id_pks : null);
        if ($idPks === '' || $idPks === 'Semua' || $idPks === 'null') {
            $idPks = null;
        }
        $jenis = $request->input('jenis', 'flat_bed');

        $pilihan = $this->getPilihanFilterList($idPks, $jenis);

        return response()->json([
            'success'     => true,
            'message'     => 'Data pilihan filter berhasil diambil',
            'server_time' => Carbon::now('Asia/Jakarta')->toIso8601String(),
            'data'        => $pilihan,
        ]);
    }

    private function getPilihanFilterList(?int $idPks, string $jenis): array
    {
        $qPengaliran = Pengaliran::query();
        $qPemeliharaan = Pemeliharaan::query();

        if ($idPks) {
            $qPengaliran->where('id_pks', $idPks);
            $qPemeliharaan->where('id_pks', $idPks);
        }

        $collected = [];

        if ($jenis === 'blok') {
            $pPengaliran = (clone $qPengaliran)
                ->whereNotNull('blok')
                ->where('blok', '!=', '')
                ->where('blok', '!=', '-')
                ->where('blok', '!=', '0')
                ->distinct()
                ->pluck('blok')
                ->toArray();

            $pPemeliharaan = (clone $qPemeliharaan)
                ->whereNotNull('blok')
                ->where('blok', '!=', '')
                ->where('blok', '!=', '-')
                ->where('blok', '!=', '0')
                ->distinct()
                ->pluck('blok')
                ->toArray();

            $allRaw = array_unique(array_merge($pPengaliran, $pPemeliharaan));
            foreach ($allRaw as $raw) {
                $rawTrim = trim((string) $raw);
                if ($rawTrim === '' || $rawTrim === '-') continue;
                if (!in_array($rawTrim, $collected)) {
                    $collected[] = $rawTrim;
                }
                // Split multi-block values (e.g. C26/D22) to allow exact single-block filtering
                $parts = preg_split('/[\/,\s]+/', $rawTrim, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if ($p !== '' && $p !== '-' && !in_array($p, $collected)) {
                        $collected[] = $p;
                    }
                }
            }
        } elseif ($jenis === 'no_bak') {
            $pPengaliran = (clone $qPengaliran)
                ->whereNotNull('no_bak')
                ->where('no_bak', '!=', '')
                ->where('no_bak', '!=', '-')
                ->where('no_bak', '!=', '0')
                ->distinct()
                ->pluck('no_bak')
                ->toArray();

            $pPemeliharaan = (clone $qPemeliharaan)
                ->whereNotNull('no_bak')
                ->where('no_bak', '!=', '')
                ->where('no_bak', '!=', '-')
                ->where('no_bak', '!=', '0')
                ->distinct()
                ->pluck('no_bak')
                ->toArray();

            $allRaw = array_unique(array_merge($pPengaliran, $pPemeliharaan));
            foreach ($allRaw as $raw) {
                $rawTrim = trim((string) $raw);
                if ($rawTrim === '' || $rawTrim === '-') continue;
                if (!in_array($rawTrim, $collected)) {
                    $collected[] = $rawTrim;
                }
                $parts = preg_split('/[\/,\s]+/', $rawTrim, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if ($p !== '' && $p !== '-' && !in_array($p, $collected)) {
                        $collected[] = $p;
                    }
                }
            }
        } else {
            $pPengaliran = (clone $qPengaliran)
                ->whereNotNull('flat_bed')
                ->where('flat_bed', '!=', '')
                ->where('flat_bed', '!=', '-')
                ->where('flat_bed', '!=', '0')
                ->where('flat_bed', '!=', 0)
                ->distinct()
                ->pluck('flat_bed')
                ->toArray();

            $pPemeliharaan = (clone $qPemeliharaan)
                ->whereNotNull('flat_bed')
                ->where('flat_bed', '!=', '')
                ->where('flat_bed', '!=', '-')
                ->where('flat_bed', '!=', '0')
                ->where('flat_bed', '!=', 0)
                ->distinct()
                ->pluck('flat_bed')
                ->toArray();

            $collected = array_unique(array_merge($pPengaliran, $pPemeliharaan));
        }

        natsort($collected);
        return array_values($collected);
    }

    /**
     * Dashboard untuk Admin: monitoring semua PKS
     */
    private function adminDashboard(string $tanggal, ?Request $request = null)
    {
        $user = Auth::user();
        $pksList = Pks::whereNotIn('akro', ['TEP', 'DTM', 'DBR'])->orderBy('nama')->get();

        $selectedDate = Carbon::parse($tanggal);
        $selectedYear = $selectedDate->year;
        $selectedMonth = $selectedDate->format('m');

        // Status monitoring per PKS (Pengaliran, Pemeliharaan, dan Alat Berat)
        $monitoringData = [];
        $missingPengaliranPks = [];
        $missingPemeliharaanPks = [];
        $missingAlatBeratPks = [];

        foreach ($pksList as $pks) {
            $hasPengaliran = Pengaliran::where('id_pks', $pks->id_pks)
                ->whereDate('tanggal', $tanggal)
                ->exists();

            $hasPemeliharaan = Pemeliharaan::where('id_pks', $pks->id_pks)
                ->whereDate('tanggal', $tanggal)
                ->exists();

            $hasAlatBerat = MonitoringAlatBerat::where('id_pks', $pks->id_pks)
                ->whereDate('tanggal', $tanggal)
                ->exists();

            $countDone = ($hasPengaliran ? 1 : 0) + ($hasPemeliharaan ? 1 : 0) + ($hasAlatBerat ? 1 : 0);

            $monitoringData[] = [
                'pks' => $pks,
                'pks_id' => $pks->id_pks,
                'pengaliran' => $hasPengaliran,
                'pemeliharaan' => $hasPemeliharaan,
                'alat_berat' => $hasAlatBerat,
                'count_done' => $countDone,
                'is_complete' => ($countDone === 3),
                'prioritas' =>
                    ($countDone === 3)
                    ? 3
                    : (($countDone > 0)
                        ? 2
                        : 1),
            ];

            if (!$hasPengaliran) $missingPengaliranPks[] = $pks;
            if (!$hasPemeliharaan) $missingPemeliharaanPks[] = $pks;
            if (!$hasAlatBerat) $missingAlatBeratPks[] = $pks;
        }

        // Summary counts
        $totalPks = count($monitoringData);
        $monitoringData = collect($monitoringData)
            ->sortBy('prioritas')
            ->values()
            ->toArray();
        $sudahPengaliran = collect($monitoringData)->where('pengaliran', true)->count();
        $sudahPemeliharaan = collect($monitoringData)->where('pemeliharaan', true)->count();
        $sudahAlatBerat = collect($monitoringData)->where('alat_berat', true)->count();
        $sudahLengkap = collect($monitoringData)->filter(fn($d) => $d['pengaliran'] && $d['pemeliharaan'] && $d['alat_berat'])->count();
        $sebagian = collect($monitoringData)->filter(fn($d) => ($d['pengaliran'] || $d['pemeliharaan'] || $d['alat_berat']) && !($d['pengaliran'] && $d['pemeliharaan'] && $d['alat_berat']))->count();
        $belumAda = $totalPks - $sudahLengkap - $sebagian;

        $missingPengaliranCount = count($missingPengaliranPks);
        $missingPemeliharaanCount = count($missingPemeliharaanPks);
        $missingAlatBeratCount = count($missingAlatBeratPks);

        // ============ STATISTIK PENGALIRAN (bulan ini) ============
        $startOfMonth = $selectedDate->copy()->startOfMonth();
        $endOfMonth = $selectedDate->copy()->endOfMonth();

        $pengaliranBulanIni = Pengaliran::whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
        $statPengaliran = [
            'total_records' => (clone $pengaliranBulanIni)->count(),
            'vol_dihasilkan' => $this->numericSum((clone $pengaliranBulanIni)->get(), 'vol_limbah_dihasilkan'),
            'vol_dialirkan' => $this->numericSum((clone $pengaliranBulanIni)->get(), 'vol_limbah_dialirkan'),
            'total_flat_bed' => $this->numericSum((clone $pengaliranBulanIni)->get(), 'flat_bed'),
            'total_luas_area' => $this->numericSum((clone $pengaliranBulanIni)->get(), 'luas_area'),
        ];

        // ============ STATISTIK PEMELIHARAAN (bulan ini) ============
        $pemeliharaanBulanIni = Pemeliharaan::whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
        $statPemeliharaan = [
            'total_records' => (clone $pemeliharaanBulanIni)->count(),
            'total_flat_bed' => $this->numericSum((clone $pemeliharaanBulanIni)->get(), 'flat_bed'),
            'total_long_bed' => $this->numericSum((clone $pemeliharaanBulanIni)->get(), 'long_bed'),
            'total_hk' => $this->numericSum((clone $pemeliharaanBulanIni)->get(), 'jumlah_hk'),
            'total_mekanis' => (clone $pemeliharaanBulanIni)->where('jenis_pemeliharaan', '1')->count(),
            'total_manual' => (clone $pemeliharaanBulanIni)->where('jenis_pemeliharaan', '2')->count(),
        ];

        // ============ STATISTIK PER PKS (bulan ini) ============
        $statsPerPks = [];
        foreach ($pksList as $pks) {
            $pq = Pengaliran::where('id_pks', $pks->id_pks)->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
            $mq = Pemeliharaan::where('id_pks', $pks->id_pks)->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
            $statsPerPks[] = [
                'pks' => $pks,
                'akro' => $pks->akro ?? $pks->nama,
                'vol_dialirkan' => $this->numericSum((clone $pq)->get(), 'vol_limbah_dialirkan'),
                'flat_bed_p' => $this->numericSum((clone $pq)->get(), 'flat_bed'),
                'luas_area' => $this->numericSum((clone $pq)->get(), 'luas_area'),
                'flat_bed_m' => $this->numericSum((clone $mq)->get(), 'flat_bed'),
                'long_bed' => $this->numericSum((clone $mq)->get(), 'long_bed'),
                'hk' => $this->numericSum((clone $mq)->get(), 'jumlah_hk'),
            ];
        }

        // Aktivitas Pengaliran terbaru (hari ini)
        $recentPengaliran = Pengaliran::with('pks')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Aktivitas Pemeliharaan terbaru (hari ini)
        $recentPemeliharaan = Pemeliharaan::with('pks')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ============ STATISTIK RENCANA (tahun terpilih) ============
        $rencanaTahunIni = Rencana::with('pks')
            ->where('tahun', $selectedYear);

        $monitoringRencana = [];
        $rencanaMap = [];
        foreach ($pksList as $pks) {
            $hasRencana = (clone $rencanaTahunIni)
                ->where('id_pks', $pks->id_pks)
                ->exists();

            $monitoringRencana[] = [
                'pks' => $pks,
                'rencana' => $hasRencana,
            ];

            $rencanaMap[$pks->id_pks] = $hasRencana;
        }

        $statRencana = [
            'total_records' => (clone $rencanaTahunIni)->count(),
            'total_flat_bed' => $this->numericSum((clone $rencanaTahunIni)->get(), 'flat_bed'),
            'total_long_bed' => $this->numericSum((clone $rencanaTahunIni)->get(), 'long_bed'),
            'total_pks' => (clone $rencanaTahunIni)->distinct('id_pks')->count('id_pks'),
        ];

        $recentRencana = (clone $rencanaTahunIni)
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        $rencanaByPks = (clone $rencanaTahunIni)
            ->orderBy('id_pks')
            ->get()
            ->groupBy(function ($item) {
                return $item->pks ? $item->pks->akro : 'N/A';
            });

        // Trend 7 hari terakhir
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $selectedDate->copy()->subDays($i);
            $trendData[] = [
                'label' => $date->translatedFormat('d M'),
                'pengaliran' => Pengaliran::whereDate('tanggal', $date)->distinct('id_pks')->count('id_pks'),
                'pemeliharaan' => Pemeliharaan::whereDate('tanggal', $date)->distinct('id_pks')->count('id_pks'),
            ];
        }

        $statAlatBerat = [
            'total_unit' => AlatBerat::count(),
            'ready' => AlatBerat::where('status', 'Operational')->count(),
            'maintenance' => AlatBerat::where('status', 'Maintenance')->count(),
            'breakdown' => AlatBerat::where('status', 'Breakdown')->count(),
            'rolling' => AlatBerat::where('status', 'Rolling')->count(),
            'total_hm' => MonitoringAlatBerat::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->sum('total_hm'),
            'total_bbm' => MonitoringAlatBerat::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->sum('bbm_liter'),
        ];

        // Filter Grafik Data (Default: 'semua' / Keseluruhan data, disinkronkan dengan tanggal terpilih jika ada)
        $periode = $request ? $request->input('periode', 'semua') : 'semua';
        $tahun = $request ? $request->input('tahun', (string)$selectedYear) : (string)$selectedYear;
        $bulan = $request ? $request->input('bulan', $selectedMonth) : $selectedMonth;
        $jenis = $request ? $request->input('jenis', 'flat_bed') : 'flat_bed';
        $nilai = $request ? $request->input('nilai') : null;
        $idPksFilter = $request ? $request->input('id_pks') : null;
        $tglMulai = $request ? $request->input('tgl_mulai', $startOfMonth->toDateString()) : $startOfMonth->toDateString();
        $tglSelesai = $request ? $request->input('tgl_selesai', $selectedDate->toDateString()) : $selectedDate->toDateString();

        $tahunPengaliran = Pengaliran::selectRaw('YEAR(tanggal) as yr')
            ->whereNotNull('tanggal')
            ->distinct()
            ->pluck('yr')
            ->toArray();

        $tahunPemeliharaan = Pemeliharaan::selectRaw('YEAR(tanggal) as yr')
            ->whereNotNull('tanggal')
            ->distinct()
            ->pluck('yr')
            ->toArray();

        $tahunDb = array_unique(array_merge($tahunPengaliran, $tahunPemeliharaan));
        $tahunList = array_unique(array_merge([date('Y'), (int)date('Y') - 1, $selectedYear], array_map('intval', $tahunDb)));
        rsort($tahunList);

        $grafikRes = $this->getGrafikData($user, [
            'id_pks'      => $idPksFilter,
            'periode'     => $periode,
            'tahun'       => $tahun,
            'bulan'       => $bulan,
            'jenis'       => $jenis,
            'nilai'       => $nilai,
            'tgl_mulai'   => $tglMulai,
            'tgl_selesai' => $tglSelesai,
        ]);

        return view('dashboard.admin', compact(
            'tanggal',
            'monitoringData',
            'totalPks',
            'sudahPengaliran',
            'sudahPemeliharaan',
            'sudahAlatBerat',
            'sudahLengkap',
            'sebagian',
            'belumAda',
            'missingPengaliranCount',
            'missingPemeliharaanCount',
            'missingAlatBeratCount',
            'missingPengaliranPks',
            'missingPemeliharaanPks',
            'missingAlatBeratPks',
            'statPengaliran',
            'statPemeliharaan',
            'statRencana',
            'statAlatBerat',
            'monitoringRencana',
            'rencanaMap',
            'statsPerPks',
            'recentPengaliran',
            'recentPemeliharaan',
            'recentRencana',
            'rencanaByPks',
            'trendData',
            'pksList',
            'tahunList',
            'periode',
            'tahun',
            'bulan',
            'jenis',
            'nilai',
            'idPksFilter',
            'tglMulai',
            'tglSelesai',
            'grafikRes'
        ));
    }

    /**
     * Dashboard untuk Unit: status PKS sendiri
     */
    private function unitDashboard($user, string $tanggal)
    {
        // Status hari ini
        $hasPengaliran = Pengaliran::where('id_pks', $user->id_pks)
            ->whereDate('tanggal', $tanggal)
            ->exists();

        $hasPemeliharaan = Pemeliharaan::where('id_pks', $user->id_pks)
            ->whereDate('tanggal', $tanggal)
            ->exists();


        // ===============================
        // STATUS KESEHATAN SIMOLI
        // ===============================

        if ($hasPengaliran && $hasPemeliharaan) {
            $simoliStatus = 'NORMAL';
            $simoliColor = 'success';
            $simoliIcon = 'check-circle';
            $simoliMessage = 'Seluruh monitoring hari ini telah selesai.';

        } elseif ($hasPengaliran || $hasPemeliharaan) {
            $simoliStatus = 'PERHATIAN';
            $simoliColor = 'warning';
            $simoliIcon = 'alert-triangle';
            $simoliMessage = 'Masih terdapat monitoring yang belum lengkap.';

        } else {
            $simoliStatus = 'KRITIS';
            $simoliColor = 'danger';
            $simoliIcon = 'x-circle';
            $simoliMessage = 'Belum ada data monitoring hari ini.';
        }

        // Count entries today
        $countPengaliran = Pengaliran::where('id_pks', $user->id_pks)
            ->whereDate('tanggal', $tanggal)
            ->count();

        $countPemeliharaan = Pemeliharaan::where('id_pks', $user->id_pks)
            ->whereDate('tanggal', $tanggal)
            ->count();

        // Recent submissions
        $recentPengaliran = Pengaliran::where('id_pks', $user->id_pks)
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();

        $recentPemeliharaan = Pemeliharaan::where('id_pks', $user->id_pks)
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();

        // Statistik bulan ini
        $startOfMonth = Carbon::parse($tanggal)->startOfMonth();
        $endOfMonth = Carbon::parse($tanggal)->endOfMonth();

        $monthlyPengaliran = Pengaliran::where('id_pks', $user->id_pks)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->distinct('tanggal')
            ->count('tanggal');

        $monthlyPemeliharaan = Pemeliharaan::where('id_pks', $user->id_pks)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->distinct('tanggal')
            ->count('tanggal');

        $daysInMonth = $endOfMonth->day;

        // ============ STATISTIK PENGALIRAN (bulan ini) ============
        $pqMonth = Pengaliran::where('id_pks', $user->id_pks)->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
        $statPengaliran = [
            'total_records' => (clone $pqMonth)->count(),
            'vol_dihasilkan' => $this->numericSum((clone $pqMonth)->get(), 'vol_limbah_dihasilkan'),
            'vol_dialirkan' => $this->numericSum((clone $pqMonth)->get(), 'vol_limbah_dialirkan'),
            'total_flat_bed' => $this->numericSum((clone $pqMonth)->get(), 'flat_bed'),
            'total_luas_area' => $this->numericSum((clone $pqMonth)->get(), 'luas_area'),
        ];

        // ============ STATISTIK PEMELIHARAAN (bulan ini) ============
        $mqMonth = Pemeliharaan::where('id_pks', $user->id_pks)->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
        $statPemeliharaan = [
            'total_records' => (clone $mqMonth)->count(),
            'total_flat_bed' => $this->numericSum((clone $mqMonth)->get(), 'flat_bed'),
            'total_long_bed' => $this->numericSum((clone $mqMonth)->get(), 'long_bed'),
            'total_hk' => $this->numericSum((clone $mqMonth)->get(), 'jumlah_hk'),
            'total_mekanis' => (clone $mqMonth)->where('jenis_pemeliharaan', '1')->count(),
            'total_manual' => (clone $mqMonth)->where('jenis_pemeliharaan', '2')->count(),
        ];

        // ===============================
        // GRAFIK PER BULAN
        // ===============================

        $grafikPengaliran = Pengaliran::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
            ->where('id_pks', $user->id_pks)
            ->whereYear('tanggal', date('Y'))
            ->groupBy(DB::raw('MONTH(tanggal)'))
            ->pluck('total', 'bulan')
            ->toArray();

        $grafikPemeliharaan = Pemeliharaan::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
            ->where('id_pks', $user->id_pks)
            ->whereYear('tanggal', date('Y'))
            ->groupBy(DB::raw('MONTH(tanggal)'))
            ->pluck('total', 'bulan')
            ->toArray();

        $grafikRencana = Rencana::selectRaw('tahun, COUNT(*) as total')
            ->where('id_pks', $user->id_pks)
            ->where('tahun', date('Y'))
            ->selectRaw('1 as bulan')
            ->groupBy('tahun')
            ->pluck('total', 'bulan')
            ->toArray();

        $pengaliranBulanan = [];
        $pemeliharaanBulanan = [];
        $rencanaBulanan = [];

        for ($i = 1; $i <= 12; $i++) {
            $pengaliranBulanan[] = $grafikPengaliran[$i] ?? 0;
            $pemeliharaanBulanan[] = $grafikPemeliharaan[$i] ?? 0;
            $rencanaBulanan[] = ($i == date('n'))
                ? ($grafikRencana[1] ?? 0)
                : 0;
        }

        $tahun     = request('tahun', date('Y'));
        $bulan     = request('bulan', date('m'));
        $jenis     = request('jenis', 'flat_bed');
        $nilai     = request('nilai');
        $periode   = request('periode', 'harian');
        $tglMulai  = request('tgl_mulai', date('Y-m-01'));
        $tglSelesai = request('tgl_selesai', date('Y-m-d'));

        $tahunDb = Pengaliran::where('id_pks', $user->id_pks)
            ->selectRaw('YEAR(tanggal) as yr')
            ->distinct()
            ->pluck('yr')
            ->toArray();

        $tahunList = array_unique(array_merge([date('Y'), (int)date('Y') - 1], array_map('intval', $tahunDb)));
        rsort($tahunList);

        $grafikRes = $this->getGrafikData($user, [
            'id_pks'     => $user->id_pks,
            'periode'    => $periode,
            'tahun'      => $tahun,
            'bulan'      => $bulan,
            'jenis'      => $jenis,
            'nilai'      => $nilai,
            'tgl_mulai'  => $tglMulai,
            'tgl_selesai' => $tglSelesai,
        ]);

        $labelHari        = $grafikRes['labels'];
        $volumeGrafik     = $grafikRes['volumeDialirkan'];
        $volumeDihasilkan = $grafikRes['volumeDihasilkan'];
        $pilihan          = $grafikRes['pilihan'];

        $statAlatBerat = [
            'total_unit'  => AlatBerat::where('id_pks', $user->id_pks)->count(),
            'ready'       => AlatBerat::where('id_pks', $user->id_pks)->where('status', 'Operational')->count(),
            'maintenance' => AlatBerat::where('id_pks', $user->id_pks)->where('status', 'Maintenance')->count(),
            'breakdown'   => AlatBerat::where('id_pks', $user->id_pks)->where('status', 'Breakdown')->count(),
            'rolling'     => AlatBerat::where('id_pks', $user->id_pks)->where('status', 'Rolling')->count(),
            'total_hm'    => MonitoringAlatBerat::where('id_pks', $user->id_pks)->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->sum('total_hm'),
            'total_bbm'   => MonitoringAlatBerat::where('id_pks', $user->id_pks)->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->sum('bbm_liter'),
        ];

        return view('dashboard.unit', compact(
            'tanggal',
            'hasPengaliran',
            'hasPemeliharaan',
            'simoliStatus',
            'simoliColor',
            'simoliIcon',
            'simoliMessage',
            'countPengaliran',
            'countPemeliharaan',
            'recentPengaliran',
            'recentPemeliharaan',
            'monthlyPengaliran',
            'monthlyPemeliharaan',
            'daysInMonth',
            'statPengaliran',
            'statPemeliharaan',
            'statAlatBerat',
            'pengaliranBulanan',
            'pemeliharaanBulanan',
            'labelHari',
            'volumeGrafik',
            'volumeDihasilkan',
            'tahun',
            'tahunList',
            'bulan',
            'jenis',
            'nilai',
            'pilihan',
            'rencanaBulanan',
            'periode',
            'tglMulai',
            'tglSelesai'
        ));
    }

    private function getGrafikData($user, array $params): array
    {
        $idPks      = $params['id_pks'] ?? (($user && !$user->isAdmin()) ? $user->id_pks : null);
        if ($idPks === '' || $idPks === 'Semua' || $idPks === 'null' || $idPks === null) {
            $idPks = null;
        }

        $periode    = $params['periode'] ?? 'semua';
        $tahun      = (int) ($params['tahun'] ?? date('Y'));
        $bulan      = (int) ($params['bulan'] ?? date('m'));
        $jenis      = $params['jenis'] ?? 'flat_bed';
        $nilai      = $params['nilai'] ?? null;
        $tglMulai   = $params['tgl_mulai'] ?? null;
        $tglSelesai = $params['tgl_selesai'] ?? null;

        $pilihan = $this->getPilihanFilterList($idPks, $jenis);

        $baseQuery = Pengaliran::query();
        $pemeliharaanBaseQuery = Pemeliharaan::query();

        // Setup query for PKS breakdown
        $pksStatsPengaliran = Pengaliran::query();
        $pksStatsPemeliharaan = Pemeliharaan::query();

        if ($idPks) {
            $baseQuery->where('id_pks', $idPks);
            $pemeliharaanBaseQuery->where('id_pks', $idPks);
            $pksStatsPengaliran->where('id_pks', $idPks);
            $pksStatsPemeliharaan->where('id_pks', $idPks);
        }

        if ($nilai !== null && $nilai !== '' && $nilai !== 'Semua' && $nilai !== '0' && $nilai !== 0 && $nilai !== '-') {
            if ($jenis === 'blok') {
                $applyBlok = function($q) use ($nilai) {
                    $q->where(function($sub) use ($nilai) {
                        $sub->where('blok', $nilai)
                            ->orWhere('blok', 'LIKE', "{$nilai}/%")
                            ->orWhere('blok', 'LIKE', "%/{$nilai}")
                            ->orWhere('blok', 'LIKE', "%/{$nilai}/%")
                            ->orWhere('blok', 'LIKE', "%{$nilai}%");
                    });
                };
                $applyBlok($baseQuery);
                $applyBlok($pemeliharaanBaseQuery);
                $applyBlok($pksStatsPengaliran);
                $applyBlok($pksStatsPemeliharaan);
            } elseif ($jenis === 'no_bak') {
                $applyBak = function($q) use ($nilai) {
                    $q->where(function($sub) use ($nilai) {
                        $sub->where('no_bak', $nilai)
                            ->orWhere('no_bak', 'LIKE', "{$nilai}/%")
                            ->orWhere('no_bak', 'LIKE', "%/{$nilai}")
                            ->orWhere('no_bak', 'LIKE', "%/{$nilai}/%")
                            ->orWhere('no_bak', 'LIKE', "%{$nilai}%");
                    });
                };
                $applyBak($baseQuery);
                $applyBak($pemeliharaanBaseQuery);
                $applyBak($pksStatsPengaliran);
                $applyBak($pksStatsPemeliharaan);
            } else {
                $baseQuery->where('flat_bed', $nilai);
                $pemeliharaanBaseQuery->where('flat_bed', $nilai);
                $pksStatsPengaliran->where('flat_bed', $nilai);
                $pksStatsPemeliharaan->where('flat_bed', $nilai);
            }
        }

        $labels = [];
        $volumeDialirkan = [];
        $volumeDihasilkan = [];
        $trendPengaliran = [];
        $trendPemeliharaan = [];

        $namaBulanShort = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        if ($periode === 'custom') {
            if (!$tglMulai) $tglMulai = Carbon::now()->startOfMonth()->toDateString();
            if (!$tglSelesai) $tglSelesai = Carbon::now()->toDateString();

            $start = Carbon::parse($tglMulai)->startOfDay();
            $end   = Carbon::parse($tglSelesai)->endOfDay();

            $pksStatsPengaliran->whereBetween('tanggal', [$start, $end]);
            $pksStatsPemeliharaan->whereBetween('tanggal', [$start, $end]);

            $pMap = (clone $baseQuery)->whereBetween('tanggal', [$start, $end])
                ->selectRaw('DATE(tanggal) as tgl, SUM(vol_limbah_dialirkan) as dialirkan, SUM(vol_limbah_dihasilkan) as dihasilkan, COUNT(*) as cnt')
                ->groupBy(DB::raw('DATE(tanggal)'))
                ->get()
                ->keyBy('tgl');

            $mMap = (clone $pemeliharaanBaseQuery)->whereBetween('tanggal', [$start, $end])
                ->selectRaw('DATE(tanggal) as tgl, COUNT(*) as cnt')
                ->groupBy(DB::raw('DATE(tanggal)'))
                ->get()
                ->keyBy('tgl');

            $curr = $start->copy();
            while ($curr->lte($end)) {
                $dateStr = $curr->toDateString();
                $labels[] = $curr->format('d/m/Y');

                $rowP = $pMap->get($dateStr);
                $volumeDialirkan[]  = $rowP ? round((float)$rowP->dialirkan, 2) : 0;
                $volumeDihasilkan[] = $rowP ? round((float)$rowP->dihasilkan, 2) : 0;
                $trendPengaliran[]  = $rowP ? (int)$rowP->cnt : 0;

                $rowM = $mMap->get($dateStr);
                $trendPemeliharaan[] = $rowM ? (int)$rowM->cnt : 0;

                $curr->addDay();
            }
        } elseif ($periode === 'semua') {
            $records = (clone $baseQuery)
                ->selectRaw('YEAR(tanggal) as yr, MONTH(tanggal) as mo, SUM(vol_limbah_dialirkan) as dialirkan, SUM(vol_limbah_dihasilkan) as dihasilkan, COUNT(*) as cnt')
                ->whereNotNull('tanggal')
                ->groupBy(DB::raw('YEAR(tanggal)'), DB::raw('MONTH(tanggal)'))
                ->orderBy(DB::raw('YEAR(tanggal)'))
                ->orderBy(DB::raw('MONTH(tanggal)'))
                ->get()
                ->keyBy(fn($r) => $r->yr . '-' . $r->mo);

            $mMap = (clone $pemeliharaanBaseQuery)
                ->selectRaw('YEAR(tanggal) as yr, MONTH(tanggal) as mo, COUNT(*) as cnt')
                ->whereNotNull('tanggal')
                ->groupBy(DB::raw('YEAR(tanggal)'), DB::raw('MONTH(tanggal)'))
                ->orderBy(DB::raw('YEAR(tanggal)'))
                ->orderBy(DB::raw('MONTH(tanggal)'))
                ->get()
                ->keyBy(fn($r) => $r->yr . '-' . $r->mo);

            $allKeys = array_unique(array_merge($records->keys()->toArray(), $mMap->keys()->toArray()));

            if (empty($allKeys)) {
                for ($m = 1; $m <= 12; $m++) {
                    $labels[] = ($namaBulanShort[$m] ?? $m) . ' ' . date('Y');
                    $volumeDialirkan[]  = 0;
                    $volumeDihasilkan[] = 0;
                    $trendPengaliran[]  = 0;
                    $trendPemeliharaan[] = 0;
                }
            } else {
                usort($allKeys, function($a, $b) {
                    [$yA, $mA] = explode('-', $a);
                    [$yB, $mB] = explode('-', $b);
                    return ($yA != $yB) ? ($yA <=> $yB) : ($mA <=> $mB);
                });

                foreach ($allKeys as $key) {
                    [$yr, $mo] = explode('-', $key);
                    $labels[] = ($namaBulanShort[(int)$mo] ?? $mo) . ' ' . $yr;

                    $rowP = $records->get($key);
                    $volumeDialirkan[]  = $rowP ? round((float)$rowP->dialirkan, 2) : 0;
                    $volumeDihasilkan[] = $rowP ? round((float)$rowP->dihasilkan, 2) : 0;
                    $trendPengaliran[]  = $rowP ? (int)$rowP->cnt : 0;

                    $rowM = $mMap->get($key);
                    $trendPemeliharaan[] = $rowM ? (int)$rowM->cnt : 0;
                }
            }
        } elseif ($periode === 'tahunan') {
            $records = (clone $baseQuery)
                ->selectRaw('YEAR(tanggal) as yr, SUM(vol_limbah_dialirkan) as dialirkan, SUM(vol_limbah_dihasilkan) as dihasilkan, COUNT(*) as cnt')
                ->whereNotNull('tanggal')
                ->groupBy(DB::raw('YEAR(tanggal)'))
                ->orderBy(DB::raw('YEAR(tanggal)'))
                ->get()
                ->keyBy('yr');

            $mMap = (clone $pemeliharaanBaseQuery)
                ->selectRaw('YEAR(tanggal) as yr, COUNT(*) as cnt')
                ->whereNotNull('tanggal')
                ->groupBy(DB::raw('YEAR(tanggal)'))
                ->get()
                ->keyBy('yr');

            $yearsP = (clone $baseQuery)->whereNotNull('tanggal')
                ->selectRaw('DISTINCT YEAR(tanggal) as yr')
                ->pluck('yr')->map(fn($y) => (int)$y)->toArray();

            $yearsM = (clone $pemeliharaanBaseQuery)->whereNotNull('tanggal')
                ->selectRaw('DISTINCT YEAR(tanggal) as yr')
                ->pluck('yr')->map(fn($y) => (int)$y)->toArray();

            $years = array_unique(array_filter(array_merge($yearsP, $yearsM)));

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

            $pksStatsPengaliran->whereBetween('tanggal', ["{$minYear}-01-01", "{$maxYear}-12-31"]);
            $pksStatsPemeliharaan->whereBetween('tanggal', ["{$minYear}-01-01", "{$maxYear}-12-31"]);

            $allYears = range($minYear, $maxYear);

            foreach ($allYears as $yr) {
                $labels[] = (string)$yr;
                $rowP = $records->get($yr);
                $volumeDialirkan[]  = $rowP ? round((float)$rowP->dialirkan, 2) : 0;
                $volumeDihasilkan[] = $rowP ? round((float)$rowP->dihasilkan, 2) : 0;
                $trendPengaliran[]  = $rowP ? (int)$rowP->cnt : 0;

                $rowM = $mMap->get($yr);
                $trendPemeliharaan[] = $rowM ? (int)$rowM->cnt : 0;
            }
        } elseif ($periode === 'bulanan') {
            $pksStatsPengaliran->whereYear('tanggal', $tahun);
            $pksStatsPemeliharaan->whereYear('tanggal', $tahun);

            $pMap = (clone $baseQuery)->whereYear('tanggal', $tahun)
                ->selectRaw('MONTH(tanggal) as mo, SUM(vol_limbah_dialirkan) as dialirkan, SUM(vol_limbah_dihasilkan) as dihasilkan, COUNT(*) as cnt')
                ->groupBy(DB::raw('MONTH(tanggal)'))
                ->get()
                ->keyBy('mo');

            $mMap = (clone $pemeliharaanBaseQuery)->whereYear('tanggal', $tahun)
                ->selectRaw('MONTH(tanggal) as mo, COUNT(*) as cnt')
                ->groupBy(DB::raw('MONTH(tanggal)'))
                ->get()
                ->keyBy('mo');

            for ($m = 1; $m <= 12; $m++) {
                $labels[] = $namaBulanShort[$m];
                $rowP = $pMap->get($m);
                $volumeDialirkan[]  = $rowP ? round((float)$rowP->dialirkan, 2) : 0;
                $volumeDihasilkan[] = $rowP ? round((float)$rowP->dihasilkan, 2) : 0;
                $trendPengaliran[]  = $rowP ? (int)$rowP->cnt : 0;

                $rowM = $mMap->get($m);
                $trendPemeliharaan[] = $rowM ? (int)$rowM->cnt : 0;
            }
        } elseif ($periode === 'mingguan') {
            $pksStatsPengaliran->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
            $pksStatsPemeliharaan->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);

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

            $pMap = (clone $baseQuery)->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)
                ->selectRaw('DAY(tanggal) as d, SUM(vol_limbah_dialirkan) as dialirkan, SUM(vol_limbah_dihasilkan) as dihasilkan, COUNT(*) as cnt')
                ->groupBy(DB::raw('DAY(tanggal)'))
                ->get()
                ->keyBy('d');

            $mMap = (clone $pemeliharaanBaseQuery)->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)
                ->selectRaw('DAY(tanggal) as d, COUNT(*) as cnt')
                ->groupBy(DB::raw('DAY(tanggal)'))
                ->get()
                ->keyBy('d');

            foreach ($weeks as $wName => [$dStart, $dEnd]) {
                $labels[] = $wName;
                $wDialirkan = 0;
                $wDihasilkan = 0;
                $wTrendP = 0;
                $wTrendM = 0;

                for ($d = $dStart; $d <= $dEnd; $d++) {
                    if ($rowP = $pMap->get($d)) {
                        $wDialirkan += (float)$rowP->dialirkan;
                        $wDihasilkan += (float)$rowP->dihasilkan;
                        $wTrendP += (int)$rowP->cnt;
                    }
                    if ($rowM = $mMap->get($d)) {
                        $wTrendM += (int)$rowM->cnt;
                    }
                }

                $volumeDialirkan[]  = round($wDialirkan, 2);
                $volumeDihasilkan[] = round($wDihasilkan, 2);
                $trendPengaliran[]  = $wTrendP;
                $trendPemeliharaan[] = $wTrendM;
            }
        } else {
            // Harian (Default)
            $pksStatsPengaliran->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
            $pksStatsPemeliharaan->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);

            $pMap = (clone $baseQuery)->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)
                ->selectRaw('DAY(tanggal) as d, SUM(vol_limbah_dialirkan) as dialirkan, SUM(vol_limbah_dihasilkan) as dihasilkan, COUNT(*) as cnt')
                ->groupBy(DB::raw('DAY(tanggal)'))
                ->get()
                ->keyBy('d');

            $mMap = (clone $pemeliharaanBaseQuery)->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)
                ->selectRaw('DAY(tanggal) as d, COUNT(*) as cnt')
                ->groupBy(DB::raw('DAY(tanggal)'))
                ->get()
                ->keyBy('d');

            $jumlahHari = Carbon::create($tahun, $bulan)->daysInMonth;
            for ($i = 1; $i <= $jumlahHari; $i++) {
                $labels[] = (string)$i;
                $rowP = $pMap->get($i);
                $volumeDialirkan[]  = $rowP ? round((float)$rowP->dialirkan, 2) : 0;
                $volumeDihasilkan[] = $rowP ? round((float)($rowP->dihasilkan ?? 0), 2) : 0;
                $trendPengaliran[]  = $rowP ? (int)$rowP->cnt : 0;

                $rowM = $mMap->get($i);
                $trendPemeliharaan[] = $rowM ? (int)$rowM->cnt : 0;
            }
        }

        // Perbandingan PKS (jika filter PKS dipilih, tampilkan PKS tsb; jika tidak dipilih/Semua, tampilkan semua 12 PKS)
        $pksQuery = Pks::query();
        if ($idPks) {
            $pksQuery->where('id_pks', $idPks);
        } else {
            $pksQuery->whereNotIn('akro', ['TEP', 'DTM', 'DBR']);
        }
        $pksList = $pksQuery->orderBy('nama')->get();

        $pksNames = [];
        $pksVols = [];
        $pksDihasilkan = [];
        $pksFlatBed = [];
        $pksLongBed = [];
        $pksHk = [];

        $pksPengaliranAgg = (clone $pksStatsPengaliran)
            ->selectRaw('id_pks, SUM(vol_limbah_dialirkan) as dialirkan, SUM(vol_limbah_dihasilkan) as dihasilkan')
            ->groupBy('id_pks')
            ->get()
            ->keyBy('id_pks');

        $pksPemeliharaanAgg = (clone $pksStatsPemeliharaan)
            ->selectRaw('id_pks, SUM(flat_bed) as fb, SUM(long_bed) as lb, SUM(jumlah_hk) as hk')
            ->groupBy('id_pks')
            ->get()
            ->keyBy('id_pks');

        foreach ($pksList as $p) {
            $pAkro = $p->akro ?? $p->nama;
            $pksNames[] = $pAkro;

            $rowP = $pksPengaliranAgg->get($p->id_pks);
            $pksVols[]        = $rowP ? round((float)$rowP->dialirkan, 2) : 0;
            $pksDihasilkan[]   = $rowP ? round((float)$rowP->dihasilkan, 2) : 0;

            $rowM = $pksPemeliharaanAgg->get($p->id_pks);
            $pksFlatBed[]      = $rowM ? round((float)$rowM->fb, 2) : 0;
            $pksLongBed[]      = $rowM ? round((float)$rowM->lb, 2) : 0;
            $pksHk[]           = $rowM ? round((float)$rowM->hk, 2) : 0;
        }

        $totalDialirkan  = round(array_sum($volumeDialirkan), 2);
        $totalDihasilkan = round(array_sum($volumeDihasilkan), 2);
        $efficiency      = $totalDihasilkan > 0 ? round(($totalDialirkan / $totalDihasilkan) * 100, 1) : 0;
        $totalFlatBed    = round(array_sum($pksFlatBed), 0);
        $totalLongBed    = round(array_sum($pksLongBed), 0);
        $totalHk         = round(array_sum($pksHk), 0);

        return [
            'labels'             => $labels,
            'labelHari'          => $labels,
            'volumeDialirkan'    => $volumeDialirkan,
            'volumeGrafik'       => $volumeDialirkan,
            'volumeDihasilkan'   => $volumeDihasilkan,
            'pilihan'            => $pilihan,
            'totalDialirkan'     => $totalDialirkan,
            'totalDihasilkan'    => $totalDihasilkan,
            'efficiency'         => $efficiency,
            'totalFlatBed'       => $totalFlatBed,
            'totalLongBed'       => $totalLongBed,
            'totalHk'            => $totalHk,
            'pksNames'           => $pksNames,
            'pksVols'            => $pksVols,
            'pksDihasilkan'      => $pksDihasilkan,
            'pksFlatBed'         => $pksFlatBed,
            'pksLongBed'         => $pksLongBed,
            'pksHk'              => $pksHk,
            'trendPengaliran'    => $trendPengaliran,
            'trendPemeliharaan'  => $trendPemeliharaan,
        ];
    }

    public function grafikVolume(Request $request)
    {
        $user = Auth::user();

        $idPks = $request->input('id_pks');
        if ($user && !$user->isAdmin()) {
            $idPks = $user->id_pks;
        }

        $grafikRes = $this->getGrafikData($user, [
            'id_pks'      => $idPks,
            'periode'     => $request->input('periode', 'semua'),
            'tahun'       => $request->input('tahun', date('Y')),
            'bulan'       => $request->input('bulan', date('m')),
            'jenis'       => $request->input('jenis', 'flat_bed'),
            'nilai'       => $request->input('nilai'),
            'tgl_mulai'   => $request->input('tgl_mulai'),
            'tgl_selesai' => $request->input('tgl_selesai'),
        ]);

        return response()->json([
            'success'          => true,
            'message'          => 'Data grafik volume berhasil diambil',
            'server_time'      => Carbon::now('Asia/Jakarta')->toIso8601String(),
            'categories'       => $grafikRes['labels'] ?? [],
            'labels'           => $grafikRes['labels'] ?? [],
            'dialirkan'        => $grafikRes['volumeDialirkan'] ?? [],
            'dihasilkan'       => $grafikRes['volumeDihasilkan'] ?? [],
            'volumeDialirkan'  => $grafikRes['volumeDialirkan'] ?? [],
            'volumeDihasilkan' => $grafikRes['volumeDihasilkan'] ?? [],
            'data'             => $grafikRes,
        ]);
    }
}
