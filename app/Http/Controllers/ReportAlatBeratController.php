<?php

namespace App\Http\Controllers;

use App\Models\AlatBerat;
use App\Models\MonitoringAlatBerat;
use App\Models\Pks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportAlatBeratController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $pksList = ($user && $user->isUnit()) 
            ? Pks::where('id_pks', $user->id_pks)->get() 
            : Pks::orderBy('nama')->get();

        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $dariTanggal = $request->input('dari_tanggal');
        $sampaiTanggal = $request->input('sampai_tanggal');

        $query = MonitoringAlatBerat::with(['pks', 'alatBerat']);

        if ($request->filled('dari_tanggal') || $request->filled('sampai_tanggal')) {
            if ($request->filled('dari_tanggal')) {
                $query->whereDate('tanggal', '>=', $dariTanggal);
            }
            if ($request->filled('sampai_tanggal')) {
                $query->whereDate('tanggal', '<=', $sampaiTanggal);
            }
        } else {
            $query->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun);
        }

        if ($user->isUnit()) {
            $query->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $query->where('id_pks', $request->id_pks);
        }

        if ($request->filled('alat_berat_id')) {
            $query->where('alat_berat_id', $request->alat_berat_id);
        }

        if ($request->filled('kondisi_alat')) {
            $query->where('kondisi_alat', $request->kondisi_alat);
        }

        $data = $query->orderBy('tanggal', 'asc')->orderBy('id_pks')->get();

        // Group by PKS
        $dataByPks = $data->groupBy(function ($item) {
            return $item->pks ? $item->pks->nama : 'N/A';
        });

        // Summary
        $summary = [
            'total_kegiatan' => $data->count(),
            'total_hm' => $data->sum('total_hm'),
            'total_bbm' => $data->sum('bbm_liter'),
            'total_flat_bed' => $data->sum('flat_bed'),
            'total_long_bed' => $data->sum('long_bed'),
            'total_bed' => $data->sum('flat_bed') + $data->sum('long_bed') > 0 ? ($data->sum('flat_bed') + $data->sum('long_bed')) : $data->sum('jumlah_bed'),
            'normal_count' => $data->where('kondisi_alat', 'Normal')->count(),
            'perbaikan_count' => $data->where('kondisi_alat', 'Perlu Perbaikan')->count(),
            'breakdown_count' => $data->where('kondisi_alat', 'Breakdown')->count(),
        ];

        // Available years
        $years = MonitoringAlatBerat::selectRaw('YEAR(tanggal) as tahun')
            ->whereNotNull('tanggal')
            ->whereRaw('YEAR(tanggal) >= 2000')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($years->isEmpty()) {
            $years = collect([date('Y')]);
        }

        $alatBeratList = AlatBerat::orderBy('kode_alat')->get();

        return view('report.alat_berat', compact(
            'pksList',
            'alatBeratList',
            'bulan',
            'tahun',
            'dariTanggal',
            'sampaiTanggal',
            'data',
            'dataByPks',
            'summary',
            'years'
        ));
    }
}
