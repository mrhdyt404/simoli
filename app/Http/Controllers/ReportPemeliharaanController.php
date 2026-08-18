<?php

namespace App\Http\Controllers;

use App\Models\Pemeliharaan;
use App\Models\Pks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportPemeliharaanController extends Controller
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

        $pksList = Pks::orderBy('nama')->get();

        // Default filters
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = Pemeliharaan::with('pks')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($user->isUnit()) {
            $query->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $query->where('id_pks', $request->id_pks);
        }

        // Filter jenis
        if ($request->filled('jenis')) {
            $query->where('jenis_pemeliharaan', $request->jenis);
        }

        $data = $query->orderBy('tanggal', 'asc')->orderBy('id_pks')->get();

        // Group by PKS
        $dataByPks = $data->groupBy(function ($item) {
            return $item->pks ? $item->pks->nama : 'N/A';
        });

        // Summary
        $summary = [
            'count' => $data->count(),
            'flat_bed' => $this->numericSum($data, 'flat_bed'),
            'long_bed' => $this->numericSum($data, 'long_bed'),
            'jumlah_hk' => $this->numericSum($data, 'jumlah_hk'),
        ];

        // Available years
        $years = Pemeliharaan::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($years->isEmpty()) {
            $years = collect([date('Y')]);
        }

        return view('report.report-pemeliharaan', compact(
            'pksList',
            'bulan',
            'tahun',
            'data',
            'dataByPks',
            'summary',
            'years'
        ));
    }
}
