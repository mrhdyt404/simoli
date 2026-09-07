<?php

namespace App\Http\Controllers;

use App\Models\PerizinanLa;
use App\Models\Pks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PerizinanLaController extends Controller
{
    /**
     * Tampilan Utama Arsip Surat Keputusan (SK) Izin Land Application
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PerizinanLa::with('pks');

        // Jika bukan admin, hanya lihat arsip milik PKS-nya sendiri
        if (!$user->isAdmin()) {
            $query->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $query->where('id_pks', $request->id_pks);
        }

        if ($request->filled('status')) {
            $query->where('status_izin', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_sk', 'like', "%{$search}%")
                  ->orWhere('instansi_penerbit', 'like', "%{$search}%")
                  ->orWhere('tentang', 'like', "%{$search}%")
                  ->orWhereHas('pks', function($qp) use ($search) {
                      $qp->where('nama', 'like', "%{$search}%")
                         ->orWhere('kode', 'like', "%{$search}%")
                         ->orWhere('akro', 'like', "%{$search}%");
                  });
            });
        }

        $perizinanList = $query->orderBy('tanggal_terbit', 'desc')->paginate(12)->withQueryString();

        // KPI Ringkasan Arsip
        $totalArsip = PerizinanLa::count();
        $arsipAktif = PerizinanLa::where('status_izin', 'Aktif')->count();
        $arsipPerpanjangan = PerizinanLa::where('status_izin', 'Proses Perpanjangan')->count();
        $arsipKedaluwarsa = PerizinanLa::where('status_izin', 'Kedaluwarsa')->count();

        $daftarPks = Pks::whereNotIn('id_pks', [13, 14, 15])->get();

        return view('perizinan_la.index', compact(
            'perizinanList',
            'totalArsip',
            'arsipAktif',
            'arsipPerpanjangan',
            'arsipKedaluwarsa',
            'daftarPks'
        ));
    }

    /**
     * Form Unggah Arsip SK Baru
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            $daftarPks = Pks::where('id_pks', $user->id_pks)->get();
        } else {
            $daftarPks = Pks::whereNotIn('id_pks', [13, 14, 15])->get();
        }

        return view('perizinan_la.create', compact('daftarPks', 'user'));
    }

    /**
     * Simpan Unggahan Dokumen SK
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'id_pks' => 'required|exists:pks,id_pks',
            'nomor_sk' => 'required|string|max:150',
            'tentang' => 'nullable|string|max:255',
            'instansi_penerbit' => 'required|string|max:150',
            'debit_maksimal_harian' => 'required|numeric|min:1',
            'tanggal_terbit' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_terbit',
            'file_sk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'keterangan' => 'nullable|string',
        ], [
            'debit_maksimal_harian.required' => 'Wajib memasukkan batas maksimal debit pengaliran harian yang tertera pada SK izin.',
            'debit_maksimal_harian.numeric' => 'Batas debit maksimal harian harus berupa angka.',
            'debit_maksimal_harian.min' => 'Batas debit maksimal harian minimal 1 m³/hari.',
            'file_sk.required' => 'Wajib mengunggah berkas salinan SK (PDF / Scan).',
            'file_sk.mimes' => 'Format berkas harus PDF, JPG, atau PNG.',
            'file_sk.max' => 'Ukuran berkas maksimal 20 MB.',
        ]);

        $data = [
            'id_pks' => $user->isAdmin() ? $request->id_pks : $user->id_pks,
            'nomor_sk' => $request->nomor_sk,
            'tentang' => $request->tentang ?: 'Izin Pembuangan Air Limbah Pada Tanah (Land Application)',
            'instansi_penerbit' => $request->instansi_penerbit,
            'debit_maksimal_harian' => $request->debit_maksimal_harian,
            'tanggal_terbit' => $request->tanggal_terbit,
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'status_izin' => $request->status_izin ?? 'Aktif',
            'keterangan' => $request->keterangan,
        ];

        // Hitung masa berlaku otomatis jika ada tanggal
        if ($request->filled('tanggal_terbit') && $request->filled('tanggal_berakhir')) {
            $tglAwal = Carbon::parse($request->tanggal_terbit);
            $tglAkhir = Carbon::parse($request->tanggal_berakhir);
            $data['masa_berlaku_tahun'] = max(1, round($tglAwal->diffInYears($tglAkhir)));
        }

        if ($request->hasFile('file_sk')) {
            $file = $request->file('file_sk');
            $filename = 'SK_LA_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/perizinan_la'), $filename);
            $data['file_sk'] = $filename;
        }

        PerizinanLa::create($data);

        return redirect()->route('perizinan-la.index')
            ->with('success', 'Arsip Dokumen SK Perizinan LA berhasil diunggah dan disimpan.');
    }

    /**
     * Lihat Detail & Salinan Dokumen SK
     */
    public function show(string $id)
    {
        $perizinan = PerizinanLa::with('pks')->findOrFail($id);
        $user = Auth::user();

        if (!$user->isAdmin() && $perizinan->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses ke berkas arsip PKS ini.');
        }

        return view('perizinan_la.show', compact('perizinan'));
    }

    /**
     * Form Edit Metadata & Berkas Arsip SK
     */
    public function edit(string $id)
    {
        $perizinan = PerizinanLa::findOrFail($id);
        $user = Auth::user();

        if (!$user->isAdmin() && $perizinan->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit arsip izin unit PKS lain.');
        }

        if (!$user->isAdmin()) {
            $daftarPks = Pks::where('id_pks', $user->id_pks)->get();
        } else {
            $daftarPks = Pks::whereNotIn('id_pks', [13, 14, 15])->get();
        }

        return view('perizinan_la.edit', compact('perizinan', 'daftarPks', 'user'));
    }

    /**
     * Update Metadata & Berkas Arsip SK
     */
    public function update(Request $request, string $id)
    {
        $perizinan = PerizinanLa::findOrFail($id);
        $user = Auth::user();

        if (!$user->isAdmin() && $perizinan->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki hak untuk memperbarui arsip izin unit PKS lain.');
        }

        $request->validate([
            'id_pks' => 'required|exists:pks,id_pks',
            'nomor_sk' => 'required|string|max:150',
            'tentang' => 'nullable|string|max:255',
            'instansi_penerbit' => 'required|string|max:150',
            'debit_maksimal_harian' => 'required|numeric|min:1',
            'tanggal_terbit' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_terbit',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'keterangan' => 'nullable|string',
        ], [
            'debit_maksimal_harian.required' => 'Wajib memasukkan batas maksimal debit pengaliran harian yang tertera pada SK izin.',
            'debit_maksimal_harian.numeric' => 'Batas debit maksimal harian harus berupa angka.',
            'debit_maksimal_harian.min' => 'Batas debit maksimal harian minimal 1 m³/hari.',
        ]);

        $data = [
            'id_pks' => $user->isAdmin() ? $request->id_pks : $user->id_pks,
            'nomor_sk' => $request->nomor_sk,
            'tentang' => $request->tentang ?: $perizinan->tentang,
            'instansi_penerbit' => $request->instansi_penerbit,
            'debit_maksimal_harian' => $request->debit_maksimal_harian,
            'tanggal_terbit' => $request->tanggal_terbit,
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'status_izin' => $request->status_izin ?? $perizinan->status_izin,
            'keterangan' => $request->keterangan,
        ];

        if ($request->filled('tanggal_terbit') && $request->filled('tanggal_berakhir')) {
            $tglAwal = Carbon::parse($request->tanggal_terbit);
            $tglAkhir = Carbon::parse($request->tanggal_berakhir);
            $data['masa_berlaku_tahun'] = max(1, round($tglAwal->diffInYears($tglAkhir)));
        }

        if ($request->hasFile('file_sk')) {
            if ($perizinan->file_sk && file_exists(public_path('uploads/perizinan_la/' . $perizinan->file_sk))) {
                @unlink(public_path('uploads/perizinan_la/' . $perizinan->file_sk));
            }
            $file = $request->file('file_sk');
            $filename = 'SK_LA_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/perizinan_la'), $filename);
            $data['file_sk'] = $filename;
        }

        $perizinan->update($data);

        return redirect()->route('perizinan-la.show', $perizinan->id)
            ->with('success', 'Arsip Dokumen SK Perizinan LA berhasil diperbarui.');
    }

    /**
     * Hapus Arsip Dokumen SK
     */
    public function destroy(string $id)
    {
        $perizinan = PerizinanLa::findOrFail($id);
        $user = Auth::user();

        if (!$user->isAdmin() && $perizinan->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus arsip unit PKS lain.');
        }

        if ($perizinan->file_sk && file_exists(public_path('uploads/perizinan_la/' . $perizinan->file_sk))) {
            @unlink(public_path('uploads/perizinan_la/' . $perizinan->file_sk));
        }

        $perizinan->delete();

        return redirect()->route('perizinan-la.index')
            ->with('success', 'Arsip Dokumen SK Perizinan LA berhasil dihapus.');
    }
}
