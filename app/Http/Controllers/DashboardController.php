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
            return $this->adminDashboard($tanggal);
        }

        return $this->unitDashboard($user, $tanggal);
    }

    public function pilihanFilter(Request $request)
    {
        $user = Auth::user();

        $jenis = $request->jenis;

        if ($jenis == 'blok') {

            $pilihan = Pengaliran::where('id_pks', $user->id_pks)
                ->distinct()
                ->orderBy('blok')
                ->pluck('blok');

        } else {

            $pilihan = Pengaliran::where('id_pks', $user->id_pks)
                ->distinct()
                ->orderBy('flat_bed')
                ->pluck('flat_bed');

        }

        return response()->json($pilihan);
    }

    /**
     * Dashboard untuk Admin: monitoring semua PKS
     */
    private function adminDashboard(string $tanggal)
    {
        $pksList = Pks::orderBy('nama')->get();

        $selectedDate = Carbon::parse($tanggal);
        $selectedYear = $selectedDate->year;

        // Status monitoring per PKS
        $monitoringData = [];
        foreach ($pksList as $pks) {
            $hasPengaliran = Pengaliran::where('id_pks', $pks->id_pks)
                ->whereDate('tanggal', $tanggal)
                ->exists();

            $hasPemeliharaan = Pemeliharaan::where('id_pks', $pks->id_pks)
                ->whereDate('tanggal', $tanggal)
                ->exists();

            $monitoringData[] = [
                'pks' => $pks,
                'pks_id' => $pks->id_pks,
                'pengaliran' => $hasPengaliran,
                'pemeliharaan' => $hasPemeliharaan,
                'prioritas' =>
                    ($hasPengaliran && $hasPemeliharaan)
                    ? 3
                    : (($hasPengaliran || $hasPemeliharaan)
                        ? 2
                        : 1),
            ];
        }

        // Summary counts
        $totalPks = count($monitoringData);
        $monitoringData = collect($monitoringData)
            ->sortBy('prioritas')
            ->values()
            ->toArray();
        $sudahPengaliran = collect($monitoringData)->where('pengaliran', true)->count();
        $sudahPemeliharaan = collect($monitoringData)->where('pemeliharaan', true)->count();
        $sudahLengkap = collect($monitoringData)->filter(fn($d) => $d['pengaliran'] && $d['pemeliharaan'])->count();
        $sebagian = collect($monitoringData)->filter(fn($d) => ($d['pengaliran'] || $d['pemeliharaan']) && !($d['pengaliran'] && $d['pemeliharaan']))->count();
        $belumAda = $totalPks - $sudahLengkap - $sebagian;

        // ============ STATISTIK PENGALIRAN (bulan ini) ============
        $startOfMonth = Carbon::parse($tanggal)->startOfMonth();
        $endOfMonth = Carbon::parse($tanggal)->endOfMonth();

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

        return view('dashboard.admin', compact(
            'tanggal',
            'monitoringData',
            'totalPks',
            'sudahPengaliran',
            'sudahPemeliharaan',
            'sudahLengkap',
            'sebagian',
            'belumAda',
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
            'trendData'
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

        $tahun = request('tahun', date('Y'));
        $bulan = request('bulan', date('m'));
        $jenis = request('jenis', 'flat_bed'); // flat_bed atau blok
        $nilai = request('nilai');

        $tahunDb = Pengaliran::where('id_pks', $user->id_pks)
            ->selectRaw('YEAR(tanggal) as yr')
            ->distinct()
            ->pluck('yr')
            ->toArray();

        $tahunList = array_unique(array_merge([date('Y'), (int)date('Y') - 1], array_map('intval', $tahunDb)));
        rsort($tahunList);

        if ($jenis == 'blok') {
            $pilihan = Pengaliran::where('id_pks', $user->id_pks)
                ->distinct()
                ->orderBy('blok')
                ->pluck('blok');
        } else {
            $pilihan = Pengaliran::where('id_pks', $user->id_pks)
                ->distinct()
                ->orderBy('flat_bed')
                ->pluck('flat_bed');
        }

        if (!$nilai && $pilihan->count()) {
            $nilai = $pilihan->first();
        }

        $query = Pengaliran::where('id_pks', $user->id_pks)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);

        if ($nilai) {
            if ($jenis == 'blok') {
                $query->where('blok', $nilai);
            } else {
                $query->where('flat_bed', $nilai);
            }
        }

        $jumlahHari = Carbon::create($tahun, $bulan)->daysInMonth;

        $labelHari = [];
        $volumeGrafik = [];

        for ($i = 1; $i <= $jumlahHari; $i++) {

            $labelHari[] = $i;

            $volumeGrafik[] = (clone $query)
                ->whereDay('tanggal', $i)
                ->sum('vol_limbah_dialirkan');
        }


        $statAlatBerat = [
            'total_unit' => AlatBerat::where('id_pks', $user->id_pks)->count(),
            'ready' => AlatBerat::where('id_pks', $user->id_pks)->where('status', 'Operational')->count(),
            'maintenance' => AlatBerat::where('id_pks', $user->id_pks)->where('status', 'Maintenance')->count(),
            'breakdown' => AlatBerat::where('id_pks', $user->id_pks)->where('status', 'Breakdown')->count(),
            'rolling' => AlatBerat::where('id_pks', $user->id_pks)->where('status', 'Rolling')->count(),
            'total_hm' => MonitoringAlatBerat::where('id_pks', $user->id_pks)->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->sum('total_hm'),
            'total_bbm' => MonitoringAlatBerat::where('id_pks', $user->id_pks)->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->sum('bbm_liter'),
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
            'tahun',
            'tahunList',
            'bulan',
            'jenis',
            'nilai',
            'pilihan',
            'rencanaBulanan'
        ));
    }

    public function grafikVolume(Request $request)
    {
        $user = Auth::user();

        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $jenis = $request->jenis ?? 'flat_bed';
        $nilai = $request->nilai;

        if ($jenis == 'blok') {

            $pilihan = Pengaliran::where('id_pks', $user->id_pks)
                ->distinct()
                ->orderBy('blok')
                ->pluck('blok');

        } else {

            $pilihan = Pengaliran::where('id_pks', $user->id_pks)
                ->distinct()
                ->orderBy('flat_bed')
                ->pluck('flat_bed');

        }

        $query = Pengaliran::where('id_pks', $user->id_pks)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);

        if (!empty($nilai)) {

            if ($jenis == 'blok') {
                $query->where('blok', $nilai);
            } else {
                $query->where('flat_bed', $nilai);
            }

        }

        $jumlahHari = Carbon::create($tahun, $bulan)->daysInMonth;

        $labelHari = [];
        $volumeGrafik = [];

        for ($i = 1; $i <= $jumlahHari; $i++) {

            $labelHari[] = $i;

            $volumeGrafik[] = (clone $query)
                ->whereDay('tanggal', $i)
                ->sum('vol_limbah_dialirkan');

        }

        return response()->json([
            'labelHari' => $labelHari,
            'volumeGrafik' => $volumeGrafik,
            'pilihan' => $pilihan
        ]);
    }

}
