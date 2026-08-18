<?php

namespace App\Http\Controllers;

use App\Models\Rencana;
use App\Models\Pks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RencanaController extends Controller
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
        $query = Rencana::with('pks');

        // Unit user: hanya lihat data milik PKS-nya
        if ($user->isUnit()) {
            $query->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $query->where('id_pks', $request->id_pks);
        }

        // Filter by tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $rencana = $query->orderBy('tahun', 'desc')
            ->orderBy('id_pks')
            ->paginate(15)
            ->withQueryString();

        $pksList = Pks::orderBy('NAMA')
            ->get();

        // Daftar tahun untuk filter (5 tahun sebelum dan 5 tahun sesudah tahun sekarang)
        $tahunSekarang = date('Y');
        $years = collect(range($tahunSekarang - 5, $tahunSekarang + 5))
            ->sortDesc()
            ->values();

        // Summary stats (filtered)
        $statsQuery = Rencana::query();
        if ($user->isUnit()) {
            $statsQuery->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $statsQuery->where('id_pks', $request->id_pks);
        }
        if ($request->filled('tahun')) {
            $statsQuery->where('tahun', $request->tahun);
        }

        $totalRecords = $statsQuery->count();
        $totalFlatBed = $this->numericSum((clone $statsQuery)->get(), 'flat_bed');
        $totalLongBed = $this->numericSum((clone $statsQuery)->get(), 'long_bed');

        // Count unique PKS in results
        $totalPks = (clone $statsQuery)->distinct('id_pks')->count('id_pks');

        return view('rencana.index', compact(
            'rencana',
            'pksList',
            'years',
            'totalRecords',
            'totalFlatBed',
            'totalLongBed',
            'totalPks'
        ));
    }

    public function report(Request $request)
    {
        $user = Auth::user();

        $pksList = Pks::orderBy('NAMA')
            ->get();

        $tahun = $request->input('tahun', date('Y'));

        $query = Rencana::with('pks')
            ->where('tahun', $tahun);

        if ($user->isUnit()) {
            $query->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $query->where('id_pks', $request->id_pks);
        }

        $rencanaData = $query->orderBy('id_pks')->get();

        $rencanaByPks = $rencanaData->groupBy(function ($item) {
            return $item->pks ? $item->pks->AKRO : 'N/A';
        });

        $summary = [
            'count' => $rencanaData->count(),
            'flat_bed' => $this->numericSum($rencanaData, 'flat_bed'),
            'long_bed' => $this->numericSum($rencanaData, 'long_bed'),
            'total_bed' => $this->numericSum($rencanaData, 'flat_bed') + $this->numericSum($rencanaData, 'long_bed'),
        ];

        $tahunSekarang = date('Y');
        $years = collect(range($tahunSekarang - 5, $tahunSekarang + 5))
            ->sortDesc()
            ->values();

        return view('report.report-rencana', compact(
            'pksList',
            'tahun',
            'rencanaData',
            'rencanaByPks',
            'summary',
            'years'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $pksList = Pks::orderBy('NAMA')
            ->get();

        return view('rencana.create', compact('pksList', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pks' => 'required|integer',
            'flat_bed' => 'required|numeric|min:0',
            'long_bed' => 'required|numeric|min:0',
            'tahun' => 'required|integer|min:2020|max:2099',
        ]);

        // Unit user: otomatis set id_pks
        $user = Auth::user();
        if ($user->isUnit()) {
            $validated['id_pks'] = $user->id_pks;
        }

        Rencana::create($validated);

        return redirect()->route('rencana.index')
            ->with('success', 'Data rencana berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $rencana = Rencana::findOrFail($id);

        if ($user->isUnit() && $rencana->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $pksList = Pks::orderBy('NAMA')
            ->get();

        return view('rencana.edit', compact('rencana', 'pksList', 'user'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $rencana = Rencana::findOrFail($id);

        if ($user->isUnit() && $rencana->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $validated = $request->validate([
            'id_pks' => 'required|integer',
            'flat_bed' => 'required|numeric|min:0',
            'long_bed' => 'required|numeric|min:0',
            'tahun' => 'required|integer|min:2020|max:2099',
        ]);

        // Unit user: otomatis set id_pks
        if ($user->isUnit()) {
            $validated['id_pks'] = $user->id_pks;
        }

        $rencana->update($validated);

        return redirect()->route('rencana.index')
            ->with('success', 'Data rencana berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = Auth::user();

        // Hanya admin yang boleh menghapus data
        if ($user->isUnit()) {
            abort(403, 'Unit tidak memiliki akses untuk menghapus data.');
        }

        $rencana = Rencana::findOrFail($id);
        $rencana->delete();

        return redirect()->route('rencana.index')
            ->with('success', 'Data rencana berhasil dihapus!');
    }
}
