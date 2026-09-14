<?php

namespace App\Http\Controllers;

use App\Models\ArsipPetaLa;
use App\Models\Pks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemetaanLaController extends Controller
{
    /**
     * Tampilan Utama Arsip Dokumen Peta Land Application
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $daftarPks = Pks::whereNotIn('id_pks', [13, 14, 15])->orderBy('NAMA')->get();

        // Tentukan PKS yang sedang aktif / dipilih
        if (!$user->isAdmin()) {
            $selectedPksId = $user->id_pks;
        } else {
            $selectedPksId = $request->get('id_pks', $daftarPks->first()?->id_pks ?? 1);
        }

        $pksAktif = Pks::find($selectedPksId) ?? $daftarPks->first();

        // Ambil Peta Terkini (Prioritaskan tahun_peta paling baru, lalu created_at terbaru)
        $petaTerbaru = null;
        if ($selectedPksId) {
            $petaTerbaru = ArsipPetaLa::where('id_pks', $selectedPksId)
                ->orderBy('tahun_peta', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();
        }

        // Query untuk daftar / galeri seluruh arsip peta
        $query = ArsipPetaLa::with('pks');

        if (!$user->isAdmin()) {
            $query->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $query->where('id_pks', $request->id_pks);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_peta', $request->kategori);
        }

        if ($request->filled('format')) {
            $fmt = strtolower($request->format);
            if ($fmt === 'pdf') {
                $query->where(function($q) {
                    $q->where('tipe_file', 'pdf')
                      ->orWhere('file_peta', 'like', '%.pdf');
                });
            } elseif ($fmt === 'image') {
                $query->where(function($q) {
                    $q->whereIn('tipe_file', ['jpg', 'jpeg', 'png', 'webp', 'svg'])
                      ->orWhere('file_peta', 'like', '%.jpg')
                      ->orWhere('file_peta', 'like', '%.jpeg')
                      ->orWhere('file_peta', 'like', '%.png')
                      ->orWhere('file_peta', 'like', '%.webp');
                });
            } elseif ($fmt === 'spasial') {
                $query->where(function($q) {
                    $q->whereNotIn('tipe_file', ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'svg'])
                      ->where('file_peta', 'not like', '%.pdf')
                      ->where('file_peta', 'not like', '%.jpg')
                      ->where('file_peta', 'not like', '%.jpeg')
                      ->where('file_peta', 'not like', '%.png')
                      ->where('file_peta', 'not like', '%.webp');
                });
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_peta', 'like', "%{$search}%")
                  ->orWhere('kategori_peta', 'like', "%{$search}%")
                  ->orWhere('tahun_peta', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('pks', function($qp) use ($search) {
                      $qp->where('nama', 'like', "%{$search}%")
                         ->orWhere('kode', 'like', "%{$search}%")
                         ->orWhere('akro', 'like', "%{$search}%");
                  });
            });
        }

        $petaList = $query->orderBy('tahun_peta', 'desc')->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // KPI Ringkasan
        $kpiQuery = ArsipPetaLa::query();
        if (!$user->isAdmin()) {
            $kpiQuery->where('id_pks', $user->id_pks);
        }

        $totalPeta = (clone $kpiQuery)->count();
        $totalGambar = (clone $kpiQuery)->where(function($q) {
            $q->whereIn('tipe_file', ['jpg', 'jpeg', 'png', 'webp', 'svg'])
              ->orWhere('file_peta', 'like', '%.jpg')
              ->orWhere('file_peta', 'like', '%.jpeg')
              ->orWhere('file_peta', 'like', '%.png')
              ->orWhere('file_peta', 'like', '%.webp');
        })->count();
        $totalPdf = (clone $kpiQuery)->where(function($q) {
            $q->where('tipe_file', 'pdf')
              ->orWhere('file_peta', 'like', '%.pdf');
        })->count();
        $totalSpasial = (clone $kpiQuery)->where(function($q) {
            $q->whereNotIn('tipe_file', ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'svg'])
              ->where('file_peta', 'not like', '%.pdf')
              ->where('file_peta', 'not like', '%.jpg')
              ->where('file_peta', 'not like', '%.jpeg')
              ->where('file_peta', 'not like', '%.png')
              ->where('file_peta', 'not like', '%.webp');
        })->count();

        $kategoriList = [
            'Peta Lokasi Land Application',
            'Peta Layout IPAL / Kolam',
            'Peta Sebaran Sumur Pantau',
            'Peta Blok & Afdeling',
            'Peta Teknis & Saluran LA',
            'Lainnya'
        ];

        return view('pemetaan_la.index', compact(
            'petaList',
            'petaTerbaru',
            'pksAktif',
            'selectedPksId',
            'totalPeta',
            'totalGambar',
            'totalPdf',
            'totalSpasial',
            'kategoriList',
            'daftarPks'
        ));
    }

    /**
     * Form Unggah Berkas Peta Baru
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            $daftarPks = Pks::where('id_pks', $user->id_pks)->get();
        } else {
            $daftarPks = Pks::whereNotIn('id_pks', [13, 14, 15])->get();
        }

        $kategoriList = [
            'Peta Lokasi Land Application',
            'Peta Layout IPAL / Kolam',
            'Peta Sebaran Sumur Pantau',
            'Peta Blok & Afdeling',
            'Peta Teknis & Saluran LA',
            'Lainnya'
        ];

        return view('pemetaan_la.create', compact('daftarPks', 'kategoriList', 'user'));
    }

    /**
     * Simpan Berkas Peta yang Diunggah
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'id_pks' => 'required|exists:pks,id_pks',
            'nama_peta' => 'required|string|max:200',
            'kategori_peta' => 'required|string|max:100',
            'tahun_peta' => 'nullable|string|max:10',
            'file_peta' => 'required|file|mimes:pdf,jpg,jpeg,png,webp,svg,zip,geojson,json|max:25600',
            'keterangan' => 'nullable|string',
        ], [
            'file_peta.required' => 'Wajib mengunggah berkas dokumen peta (PDF / Gambar / Zip / GeoJSON).',
            'file_peta.mimes' => 'Format berkas harus PDF, JPG, PNG, WEBP, ZIP, atau GeoJSON.',
            'file_peta.max' => 'Ukuran berkas peta maksimal 25 MB.',
        ]);

        $idPks = $user->isAdmin() ? $request->id_pks : $user->id_pks;

        $file = $request->file('file_peta');
        $ext = $file->getClientOriginalExtension();
        $filename = 'PETA_LA_' . time() . '_' . uniqid() . '.' . $ext;
        
        $destinationPath = public_path('uploads/peta_la');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        
        $fileSize = $file->getSize();
        $file->move($destinationPath, $filename);

        ArsipPetaLa::create([
            'id_pks' => $idPks,
            'nama_peta' => $request->nama_peta,
            'kategori_peta' => $request->kategori_peta,
            'tahun_peta' => $request->tahun_peta ?: date('Y'),
            'file_peta' => $filename,
            'tipe_file' => strtolower($ext),
            'ukuran_file' => $fileSize,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('pemetaan-la.index')
            ->with('success', 'Berkas Arsip Peta Land Application berhasil diunggah.');
    }

    /**
     * Lihat Detail & Pratinjau Dokumen Peta
     */
    public function show(string $id)
    {
        $peta = ArsipPetaLa::with('pks')->findOrFail($id);
        $user = Auth::user();

        if (!$user->isAdmin() && $peta->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki hak untuk mengakses berkas peta unit PKS lain.');
        }

        return view('pemetaan_la.show', compact('peta'));
    }

    /**
     * Form Edit Metadata & Berkas Peta
     */
    public function edit(string $id)
    {
        $peta = ArsipPetaLa::findOrFail($id);
        $user = Auth::user();

        if (!$user->isAdmin() && $peta->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit arsip peta unit PKS lain.');
        }

        if (!$user->isAdmin() && $peta->is_locked) {
            return redirect()->route('pemetaan-la.show', $peta->id)
                ->with('error', 'Dokumen arsip peta ini telah dikunci oleh Administrator Regional dan tidak dapat diedit.');
        }

        if (!$user->isAdmin()) {
            $daftarPks = Pks::where('id_pks', $user->id_pks)->get();
        } else {
            $daftarPks = Pks::whereNotIn('id_pks', [13, 14, 15])->get();
        }

        $kategoriList = [
            'Peta Lokasi Land Application',
            'Peta Layout IPAL / Kolam',
            'Peta Sebaran Sumur Pantau',
            'Peta Blok & Afdeling',
            'Peta Teknis & Saluran LA',
            'Lainnya'
        ];

        return view('pemetaan_la.edit', compact('peta', 'daftarPks', 'kategoriList', 'user'));
    }

    /**
     * Update Metadata & Berkas Peta
     */
    public function update(Request $request, string $id)
    {
        $peta = ArsipPetaLa::findOrFail($id);
        $user = Auth::user();

        if (!$user->isAdmin() && $peta->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki hak untuk memperbarui arsip peta unit PKS lain.');
        }

        if (!$user->isAdmin() && $peta->is_locked) {
            return redirect()->route('pemetaan-la.show', $peta->id)
                ->with('error', 'Dokumen arsip peta ini telah dikunci oleh Administrator Regional dan tidak dapat diedit.');
        }

        $request->validate([
            'id_pks' => 'required|exists:pks,id_pks',
            'nama_peta' => 'required|string|max:200',
            'kategori_peta' => 'required|string|max:100',
            'tahun_peta' => 'nullable|string|max:10',
            'file_peta' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,svg,zip,geojson,json|max:25600',
            'keterangan' => 'nullable|string',
        ]);

        $idPks = $user->isAdmin() ? $request->id_pks : $user->id_pks;

        $data = [
            'id_pks' => $idPks,
            'nama_peta' => $request->nama_peta,
            'kategori_peta' => $request->kategori_peta,
            'tahun_peta' => $request->tahun_peta ?: $peta->tahun_peta,
            'keterangan' => $request->keterangan,
        ];

        if ($request->hasFile('file_peta')) {
            if ($peta->file_peta && file_exists(public_path('uploads/peta_la/' . $peta->file_peta))) {
                @unlink(public_path('uploads/peta_la/' . $peta->file_peta));
            }
            $file = $request->file('file_peta');
            $ext = $file->getClientOriginalExtension();
            $filename = 'PETA_LA_' . time() . '_' . uniqid() . '.' . $ext;
            
            $destinationPath = public_path('uploads/peta_la');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $fileSize = $file->getSize();
            $file->move($destinationPath, $filename);

            $data['file_peta'] = $filename;
            $data['tipe_file'] = strtolower($ext);
            $data['ukuran_file'] = $fileSize;
        }

        $peta->update($data);

        return redirect()->route('pemetaan-la.show', $peta->id)
            ->with('success', 'Arsip Dokumen Peta Land Application berhasil diperbarui.');
    }

    /**
     * Hapus Berkas Peta (Hanya Admin)
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'Akses ditolak. Unit PKS tidak diperbolehkan menghapus data arsip peta.');
        }

        $peta = ArsipPetaLa::findOrFail($id);

        if ($peta->file_peta && file_exists(public_path('uploads/peta_la/' . $peta->file_peta))) {
            @unlink(public_path('uploads/peta_la/' . $peta->file_peta));
        }

        $peta->delete();

        return redirect()->route('pemetaan-la.index')
            ->with('success', 'Arsip Berkas Peta Land Application berhasil dihapus.');
    }

    /**
     * Kunci / Buka Kunci Edit Data Arsip Peta (Admin Only)
     */
    public function toggleLock(Request $request, string $id)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'Hanya Administrator Regional yang berhak mengunci atau membuka kunci data arsip.');
        }

        $peta = ArsipPetaLa::findOrFail($id);
        $peta->is_locked = !$peta->is_locked;
        $peta->save();

        $msg = $peta->is_locked
            ? 'Arsip Peta "' . $peta->nama_peta . '" berhasil DIKUNCI. Unit PKS tidak dapat mengedit arsip ini.'
            : 'Kunci Arsip Peta "' . $peta->nama_peta . '" berhasil DIBUKA. Unit PKS kini dapat mengedit.';

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Kunci / Buka Kunci Semua Data Arsip Peta LA (Admin Only - Semua Unit / Unit Tertentu)
     */
    public function bulkLock(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'Hanya Administrator Regional yang berhak mengunci atau membuka kunci data arsip.');
        }

        $action = $request->input('action', 'lock'); // 'lock' or 'unlock'
        $isLocked = ($action === 'lock');
        $idPks = $request->input('id_pks');

        $query = ArsipPetaLa::query();
        if ($idPks && $idPks !== 'all') {
            $query->where('id_pks', $idPks);
            $pks = Pks::find($idPks);
            $targetName = $pks ? 'Unit ' . $pks->nama : 'Unit Terpilih';
        } else {
            $targetName = 'SEMUA UNIT PKS';
        }

        $totalUpdated = $query->update(['is_locked' => $isLocked]);

        $msg = $isLocked
            ? "Berhasil! Seluruh data arsip peta Land Application ({$totalUpdated} berkas) untuk {$targetName} telah DIKUNCI. Pengguna Unit tidak dapat mengedit."
            : "Berhasil! Seluruh data arsip peta Land Application ({$totalUpdated} berkas) untuk {$targetName} telah DIBUKA KUNCI. Pengguna Unit kini dapat mengedit.";

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Kunci / Buka Kunci Semua Arsip Peta & Izin LA Sekaligus untuk Semua Unit
     */
    public function bulkLockAll(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'Hanya Administrator Regional yang berhak mengunci atau membuka kunci data arsip.');
        }

        $action = $request->input('action', 'lock');
        $isLocked = ($action === 'lock');

        $totalPeta = ArsipPetaLa::query()->update(['is_locked' => $isLocked]);
        $totalIzin = \App\Models\PerizinanLa::query()->update(['is_locked' => $isLocked]);

        $statusText = $isLocked ? 'DIKUNCI' : 'DIBUKA KUNCI';
        $msg = "Berhasil! Seluruh data Arsip Peta ({$totalPeta} berkas) dan Arsip SK Izin LA ({$totalIzin} dokumen) untuk SEMUA UNIT telah {$statusText}.";

        return redirect()->back()->with('success', $msg);
    }
}
