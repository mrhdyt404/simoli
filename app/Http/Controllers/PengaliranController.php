<?php

namespace App\Http\Controllers;

use App\Models\Pengaliran;
use App\Models\Pks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaliranController extends Controller
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
        $query = Pengaliran::with('pks');

        // Unit user: hanya lihat data milik PKS-nya sendiri
        if ($user->isUnit()) {
            $query->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            // Admin: bisa filter by PKS
            $query->where('id_pks', $request->id_pks);
        }

        // Filter by date range
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }

        // Filter by bulan & tahun
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $pengaliran = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();

        $pksList = Pks::orderBy('NAMA')->get();

        // Summary stats (juga filter sesuai hak akses)
        $statsQuery = Pengaliran::query();
        if ($user->isUnit()) {
            $statsQuery->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $statsQuery->where('id_pks', $request->id_pks);
        }

        $totalRecords = $statsQuery->count();
        $totalVolDihasilkan = $this->numericSum((clone $statsQuery)->get(), 'vol_limbah_dihasilkan');
        $totalVolDialirkan = $this->numericSum((clone $statsQuery)->get(), 'vol_limbah_dialirkan');
        $totalFlatBed = $this->numericSum((clone $statsQuery)->get(), 'flat_bed');
        $totalLuasArea = $this->numericSum((clone $statsQuery)->get(), 'luas_area');

        return view('pengaliran.index', compact(
            'pengaliran',
            'pksList',
            'totalRecords',
            'totalVolDihasilkan',
            'totalVolDialirkan',
            'totalFlatBed',
            'totalLuasArea'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $pksList = Pks::orderBy('NAMA')
            ->get();

        return view('pengaliran.create', compact('pksList', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'tanggal' => 'required|date',
                'jam_mulai' => 'required',
                'jam_selesai' => 'required',
                'no_bak' => 'required|string',
                'blok' => 'required|string',
                'flat_bed' => 'required|integer',
                'vol_limbah_dihasilkan' => 'required|integer',
                'vol_limbah_dialirkan' => 'required|integer',
                'luas_area' => 'required|integer',
                'rotasi' => 'nullable|string',
                'keterangan' => 'nullable|string',
                'id_pks' => 'required|integer',
                'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ],
            [
                'foto.image' => 'File yang diunggah harus berupa gambar.',
                'foto.mimes' => 'Foto dokumentasi harus berformat JPG, JPEG, atau PNG.',
                'foto.max' => 'Ukuran foto maksimal 2 MB.',
            ]
        );

        // Unit user: otomatis set id_pks ke ID user sendiri
        $user = Auth::user();
        if ($user->isUnit()) {
            $validated['id_pks'] = $user->id_pks;
        }

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('gallery'), $filename);
            $validated['foto'] = $filename;
        }

        Pengaliran::create($validated);

        return redirect()->route('pengaliran.index')
            ->with('success', 'Data pengaliran berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $pengaliran = Pengaliran::findOrFail($id);

        // Unit user hanya bisa edit data miliknya
        if ($user->isUnit() && $pengaliran->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $pksList = Pks::orderBy('NAMA')->get();

        return view('pengaliran.edit', compact('pengaliran', 'pksList', 'user'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $pengaliran = Pengaliran::findOrFail($id);

        // Unit user hanya bisa update data miliknya
        if ($user->isUnit() && $pengaliran->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $validated = $request->validate(
            [
                'tanggal' => 'required|date',
                'jam_mulai' => 'required',
                'jam_selesai' => 'required',
                'no_bak' => 'required|string',
                'blok' => 'required|string',
                'flat_bed' => 'required|integer',
                'vol_limbah_dihasilkan' => 'required|integer',
                'vol_limbah_dialirkan' => 'required|integer',
                'luas_area' => 'required|integer',
                'rotasi' => 'nullable|string',
                'keterangan' => 'nullable|string',
                'id_pks' => 'required|integer',
                'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ],
            [
                'foto.image' => 'File yang diunggah harus berupa gambar.',
                'foto.mimes' => 'Foto dokumentasi harus berformat JPG, JPEG, atau PNG.',
                'foto.max' => 'Ukuran foto maksimal 2 MB.',
            ]
        );

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('gallery'), $filename);
            $validated['foto'] = $filename;
        }

        // Unit user: otomatis set id_pks ke ID user sendiri
        if ($user->isUnit()) {
            $validated['id_pks'] = $user->id_pks;
        }

        $pengaliran->update($validated);

        return redirect()->route('pengaliran.index')
            ->with('success', 'Data pengaliran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = Auth::user();

        // Hanya admin yang boleh menghapus data
        if ($user->isUnit()) {
            abort(403, 'Unit tidak memiliki akses untuk menghapus data.');
        }

        $pengaliran = Pengaliran::findOrFail($id);
        $pengaliran->delete();

        return redirect()->route('pengaliran.index')
            ->with('success', 'Data pengaliran berhasil dihapus!');
    }

    public function show($id)
    {
        $data = Pengaliran::with('pks')->findOrFail($id);
        return view('pengaliran.show', compact('data'));
    }
}
