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

    /**
     * Menampilkan data rencana
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        /*
         * INNER JOIN:
         * rencana.id_pks = pks.id_pks
         *
         * Mengambil pks.NAMA sebagai nama_pks
         */
        $query = Rencana::query()
            ->join(
                'pks',
                'rencana.id_pks',
                '=',
                'pks.id_pks'
            )
            ->select(
                'rencana.*',
                'pks.NAMA as nama_pks'
            );

        // Unit hanya dapat melihat PKS miliknya
        if ($user->isUnit()) {
            $query->where(
                'rencana.id_pks',
                $user->id_pks
            );
        }

        // Filter PKS
        elseif ($request->filled('id_pks')) {
            $query->where(
                'rencana.id_pks',
                $request->id_pks
            );
        }

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->where(
                'rencana.tahun',
                $request->tahun
            );
        }

        $rencana = $query
            ->orderBy('rencana.tahun', 'desc')
            ->orderBy('rencana.id_pks')
            ->paginate(15)
            ->withQueryString();

        /*
         * List PKS untuk dropdown filter
         */
        $pksList = Pks::orderBy('NAMA')->get();

        /*
         * Daftar tahun
         */
        $tahunSekarang = date('Y');

        $years = collect(
            range(
                $tahunSekarang - 5,
                $tahunSekarang + 5
            )
        )
            ->sortDesc()
            ->values();

        /*
         * Summary
         */
        $statsQuery = Rencana::query();

        if ($user->isUnit()) {
            $statsQuery->where(
                'id_pks',
                $user->id_pks
            );
        } elseif ($request->filled('id_pks')) {
            $statsQuery->where(
                'id_pks',
                $request->id_pks
            );
        }

        if ($request->filled('tahun')) {
            $statsQuery->where(
                'tahun',
                $request->tahun
            );
        }

        $totalRecords = $statsQuery->count();

        $totalFlatBed = $this->numericSum(
            (clone $statsQuery)->get(),
            'flat_bed'
        );

        $totalLongBed = $this->numericSum(
            (clone $statsQuery)->get(),
            'long_bed'
        );

        $totalPks = (clone $statsQuery)
            ->distinct('id_pks')
            ->count('id_pks');

        return view(
            'rencana.index',
            compact(
                'rencana',
                'pksList',
                'years',
                'totalRecords',
                'totalFlatBed',
                'totalLongBed',
                'totalPks'
            )
        );
    }

    /**
     * Report rencana
     */
    public function report(Request $request)
    {
        $user = Auth::user();

        $pksList = Pks::orderBy('NAMA')->get();

        $tahun = $request->input(
            'tahun',
            date('Y')
        );

        /*
         * INNER JOIN PKS
         *
         * rencana.id_pks = pks.id_pks
         */
        $query = Rencana::query()
            ->join(
                'pks',
                'rencana.id_pks',
                '=',
                'pks.id_pks'
            )
            ->select(
                'rencana.*',
                'pks.NAMA as nama_pks',
                'pks.AKRO as akro'
            )
            ->where(
                'rencana.tahun',
                $tahun
            );

        // Unit hanya melihat PKS miliknya
        if ($user->isUnit()) {
            $query->where(
                'rencana.id_pks',
                $user->id_pks
            );
        }

        // Filter PKS
        elseif ($request->filled('id_pks')) {
            $query->where(
                'rencana.id_pks',
                $request->id_pks
            );
        }

        $rencanaData = $query
            ->orderBy('rencana.id_pks')
            ->get();

        /*
         * Group berdasarkan AKRO
         */
        $rencanaByPks = $rencanaData->groupBy(function ($item) {
            return $item->akro ?? 'N/A';
        });

        /*
         * Summary
         */
        $flatBed = $this->numericSum(
            $rencanaData,
            'flat_bed'
        );

        $longBed = $this->numericSum(
            $rencanaData,
            'long_bed'
        );

        $summary = [
            'count' => $rencanaData->count(),
            'flat_bed' => $flatBed,
            'long_bed' => $longBed,
            'total_bed' => $flatBed + $longBed,
        ];

        /*
         * Tahun
         */
        $tahunSekarang = date('Y');

        $years = collect(
            range(
                $tahunSekarang - 5,
                $tahunSekarang + 5
            )
        )
            ->sortDesc()
            ->values();

        return view(
            'report.report-rencana',
            compact(
                'pksList',
                'tahun',
                'rencanaData',
                'rencanaByPks',
                'summary',
                'years'
            )
        );
    }

    /**
     * Form tambah
     */
    public function create()
    {
        $user = Auth::user();

        $pksList = Pks::orderBy('NAMA')->get();

        return view(
            'rencana.create',
            compact(
                'pksList',
                'user'
            )
        );
    }

    /**
     * Simpan data
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pks' => 'required|integer',
            'flat_bed' => 'required|numeric|min:0',
            'long_bed' => 'required|numeric|min:0',
            'tahun' => 'required|integer|min:2020|max:2099',
        ]);

        $user = Auth::user();

        // Unit otomatis menggunakan PKS miliknya
        if ($user->isUnit()) {
            $validated['id_pks'] = $user->id_pks;
        }

        Rencana::create($validated);

        return redirect()
            ->route('rencana.index')
            ->with(
                'success',
                'Data rencana berhasil ditambahkan!'
            );
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $user = Auth::user();

        $rencana = Rencana::findOrFail($id);

        if (
            $user->isUnit() &&
            $rencana->id_pks != $user->id_pks
        ) {
            abort(
                403,
                'Anda tidak memiliki akses ke data ini.'
            );
        }

        $pksList = Pks::orderBy('NAMA')->get();

        return view(
            'rencana.edit',
            compact(
                'rencana',
                'pksList',
                'user'
            )
        );
    }

    /**
     * Update data
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $rencana = Rencana::findOrFail($id);

        if (
            $user->isUnit() &&
            $rencana->id_pks != $user->id_pks
        ) {
            abort(
                403,
                'Anda tidak memiliki akses ke data ini.'
            );
        }

        $validated = $request->validate([
            'id_pks' => 'required|integer',
            'flat_bed' => 'required|numeric|min:0',
            'long_bed' => 'required|numeric|min:0',
            'tahun' => 'required|integer|min:2020|max:2099',
        ]);

        // Unit tidak boleh mengganti PKS
        if ($user->isUnit()) {
            $validated['id_pks'] = $user->id_pks;
        }

        $rencana->update($validated);

        return redirect()
            ->route('rencana.index')
            ->with(
                'success',
                'Data rencana berhasil diperbarui!'
            );
    }

    /**
     * Hapus data
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $rencana = Rencana::findOrFail($id);

        if ($user->isUnit() && $rencana->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data unit lain.');
        }

        $rencana->delete();

        return redirect()
            ->route('rencana.index')
            ->with(
                'success',
                'Data rencana berhasil dihapus!'
            );
    }
}

