<?php

namespace App\Http\Controllers;

use App\Models\Pemeliharaan;
use App\Models\Pks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemeliharaanController extends Controller
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
        $query = Pemeliharaan::with('pks');

        // Unit user: hanya lihat data milik PKS-nya
        if ($user->isUnit()) {
            $query->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
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

        // Filter by jenis pemeliharaan
        if ($request->filled('jenis')) {
            $query->where('jenis_pemeliharaan', $request->jenis);
        }

        $pemeliharaan = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();

        $pksList = Pks::orderBy('NAMA')
            ->get();

        // Summary stats - apply same filters
        $statsQuery = Pemeliharaan::query();
        if ($user->isUnit()) {
            $statsQuery->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $statsQuery->where('id_pks', $request->id_pks);
        }
        if ($request->filled('dari_tanggal')) {
            $statsQuery->whereDate('tanggal', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $statsQuery->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }
        if ($request->filled('bulan')) {
            $statsQuery->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $statsQuery->whereYear('tanggal', $request->tahun);
        }
        if ($request->filled('jenis')) {
            $statsQuery->where('jenis_pemeliharaan', $request->jenis);
        }

        $totalRecords = $statsQuery->count();
        $totalFlatBed = $this->numericSum((clone $statsQuery)->get(), 'flat_bed');
        $totalLongBed = $this->numericSum((clone $statsQuery)->get(), 'long_bed');
        $totalHK = $this->numericSum((clone $statsQuery)->get(), 'jumlah_hk');
        $totalMekanis = (clone $statsQuery)->where('jenis_pemeliharaan', '1')->count();
        $totalManual = (clone $statsQuery)->where('jenis_pemeliharaan', '2')->count();

        return view('pemeliharaan.index', compact(
            'pemeliharaan',
            'pksList',
            'totalRecords',
            'totalFlatBed',
            'totalLongBed',
            'totalHK',
            'totalMekanis',
            'totalManual'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $pksList = Pks::orderBy('NAMA')
            ->get();

        return view('pemeliharaan.create', compact('pksList', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'tanggal' => 'required|date',
                'blok' => 'required|string|max:50',
                'no_bak' => 'required|string|max:50',
                'flat_bed' => 'required|numeric|min:0',
                'long_bed' => 'required|numeric|min:0',
                'jumlah_hk' => 'required|string|max:50',
                'keterangan' => 'nullable|string|max:1000',
                'id_pks' => 'required|integer',
                'jenis_pemeliharaan' => 'nullable|string|in:1,2',
                'sebelum' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'sesudah' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ],
            [
                // Foto Sebelum
                'sebelum.image' => 'Foto sebelum harus berupa file gambar.',
                'sebelum.mimes' => 'Foto sebelum harus berformat JPG, JPEG, atau PNG.',
                'sebelum.max' => 'Ukuran foto sebelum maksimal 2 MB.',

                // Foto Sesudah
                'sesudah.image' => 'Foto sesudah harus berupa file gambar.',
                'sesudah.mimes' => 'Foto sesudah harus berformat JPG, JPEG, atau PNG.',
                'sesudah.max' => 'Ukuran foto sesudah maksimal 2 MB.',
            ],
            [
                'sebelum' => 'Foto Sebelum',
                'sesudah' => 'Foto Sesudah',
            ]
        );

        // Handle sebelum photo
        if ($request->hasFile('sebelum')) {
            $file = $request->file('sebelum');
            $filename = md5(time() . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('gallery'), $filename);
            $validated['sebelum'] = $filename;
        }

        // Handle sesudah photo
        if ($request->hasFile('sesudah')) {
            $file = $request->file('sesudah');
            $filename = md5(time() . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('gallery'), $filename);
            $validated['sesudah'] = $filename;
        }

        // Unit user: otomatis set id_pks ke ID user sendiri
        $user = Auth::user();
        if ($user->isUnit()) {
            $validated['id_pks'] = $user->id_pks;
        }

        // Generate tags from sebelum filename
        $validated['tags'] = $validated['sebelum'] ?? '';

        Pemeliharaan::create($validated);

        return redirect()->route('pemeliharaan.index')
            ->with('success', 'Data pemeliharaan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $pemeliharaan = Pemeliharaan::findOrFail($id);

        if ($user->isUnit() && $pemeliharaan->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $pksList = Pks::orderBy('NAMA')
            ->get();

        return view('pemeliharaan.edit', compact('pemeliharaan', 'pksList', 'user'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $pemeliharaan = Pemeliharaan::findOrFail($id);

        if ($user->isUnit() && $pemeliharaan->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $validated = $request->validate(
            [
                'tanggal' => 'required|date',
                'blok' => 'required|string|max:50',
                'no_bak' => 'required|string|max:50',
                'flat_bed' => 'required|numeric|min:0',
                'long_bed' => 'required|numeric|min:0',
                'jumlah_hk' => 'required|string|max:50',
                'keterangan' => 'nullable|string|max:1000',
                'id_pks' => 'required|integer',
                'jenis_pemeliharaan' => 'nullable|string|in:1,2',
                'sebelum' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'sesudah' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ],
            [
                // Foto Sebelum
                'sebelum.image' => 'Foto sebelum harus berupa file gambar.',
                'sebelum.mimes' => 'Foto sebelum harus berformat JPG, JPEG, atau PNG.',
                'sebelum.max' => 'Ukuran foto sebelum maksimal 2 MB.',

                // Foto Sesudah
                'sesudah.image' => 'Foto sesudah harus berupa file gambar.',
                'sesudah.mimes' => 'Foto sesudah harus berformat JPG, JPEG, atau PNG.',
                'sesudah.max' => 'Ukuran foto sesudah maksimal 2 MB.',
            ],
            [
                'sebelum' => 'Foto Sebelum',
                'sesudah' => 'Foto Sesudah',
            ]
        );

        // Handle sebelum photo
        if ($request->hasFile('sebelum')) {
            $file = $request->file('sebelum');
            $filename = md5(time() . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('gallery'), $filename);
            $validated['sebelum'] = $filename;
        }

        // Handle sesudah photo
        if ($request->hasFile('sesudah')) {
            $file = $request->file('sesudah');
            $filename = md5(time() . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('gallery'), $filename);
            $validated['sesudah'] = $filename;
        }

        // Unit user: otomatis set id_pks
        if ($user->isUnit()) {
            $validated['id_pks'] = $user->id_pks;
        }

        $pemeliharaan->update($validated);

        return redirect()->route('pemeliharaan.index')
            ->with('success', 'Data pemeliharaan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = Auth::user();

        // Hanya admin yang boleh menghapus data
        if ($user->isUnit()) {
            abort(403, 'Unit tidak memiliki akses untuk menghapus data.');
        }

        $pemeliharaan = Pemeliharaan::findOrFail($id);
        $pemeliharaan->delete();

        return redirect()->route('pemeliharaan.index')
            ->with('success', 'Data pemeliharaan berhasil dihapus!');
    }

    public function show($id)
    {
        $data = Pemeliharaan::with('pks')->findOrFail($id);

        return view('pemeliharaan.show', compact('data'));
    }
}
