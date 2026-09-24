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

        // Official PKS sequence: TPU, TME, SPA, SGO, SBT, LDA, SGH, TAN, TER, STA, SRO, SIN
        $pksOrder = ['TPU', 'TME', 'SPA', 'SGO', 'SBT', 'LDA', 'SGH', 'TAN', 'TER', 'STA', 'SRO', 'SIN'];

        $pksQuery = Pks::query();
        if ($user->isUnit()) {
            $pksQuery->where('id_pks', $user->id_pks);
        }
        $pksList = $pksQuery->get()->sortBy(function ($pks) use ($pksOrder) {
            $idx = array_search(strtoupper($pks->akro ?? ''), $pksOrder);
            return $idx === false ? 999 : $idx;
        })->values();

        // Default filters
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = Pemeliharaan::with('pks');

        // Filter PKS
        if ($user->isUnit()) {
            $query->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $query->where('id_pks', $request->id_pks);
        }

        // Date range vs Month/Year filtering
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }

        if (!$request->filled('dari_tanggal') && !$request->filled('sampai_tanggal')) {
            if ($bulan !== 'all' && !empty($bulan)) {
                $query->whereMonth('tanggal', $bulan);
            }
            if ($tahun !== 'all' && !empty($tahun)) {
                $query->whereYear('tanggal', $tahun);
            }
        }

        // Filter jenis
        if ($request->filled('jenis')) {
            $query->where('jenis_pemeliharaan', $request->jenis);
        }

        // Search filter (Blok / No Bak / Keterangan)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('blok', 'like', "%{$search}%")
                  ->orWhere('no_bak', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('tanggal', 'asc')->orderBy('id_pks')->get();

        // Group by PKS and sort groups by official PKS sequence
        $dataByPks = $data->groupBy(function ($item) {
            return $item->pks ? $item->pks->nama : 'N/A';
        })->sortBy(function ($items, $pksName) use ($pksOrder) {
            $firstItem = $items->first();
            $akro = strtoupper($firstItem && $firstItem->pks ? ($firstItem->pks->akro ?? '') : '');
            $idx = array_search($akro, $pksOrder);
            return $idx === false ? 999 : $idx;
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
