<?php

namespace App\Http\Controllers;

use App\Models\PerizinanLa;
use App\Models\PerizinanSumurPantau;
use App\Models\Pks;
use App\Models\Pengaliran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PerizinanLaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PerizinanLa::with(['pks', 'sumurPantau']);

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
                         ->orWhere('kode', 'like', "%{$search}%");
                  });
            });
        }

        $perizinanList = $query->orderBy('tanggal_terbit', 'desc')->paginate(10)->withQueryString();

        // Summary KPI Counts
        $totalIzin = PerizinanLa::count();
        $totalPks = Pks::whereNotIn('id_pks', [13, 14, 15])->count();
        $izinAktif = PerizinanLa::where('status_izin', 'Aktif')->count();
        $izinPerpanjangan = PerizinanLa::where('status_izin', 'Proses Perpanjangan')->count();
        $izinKedaluwarsa = PerizinanLa::where('status_izin', 'Kedaluwarsa')->count();

        // Kepatuhan Debit Pengaliran Terakhir (Compliance Check)
        // Periksa apakah ada pengaliran dalam 30 hari terakhir yang melebihi debit harian izin
        $complianceAlerts = [];
        $recentOverDebits = Pengaliran::with('pks')
            ->where('tanggal', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('vol_limbah_dialirkan')
            ->where('vol_limbah_dialirkan', '>', 0)
            ->get()
            ->filter(function($item) {
                $pksIzin = PerizinanLa::where('id_pks', $item->id_pks)->first();
                if ($pksIzin && $item->vol_limbah_dialirkan > $pksIzin->debit_maksimal_harian) {
                    $item->debit_izin = $pksIzin->debit_maksimal_harian;
                    $item->kelebihan = $item->vol_limbah_dialirkan - $pksIzin->debit_maksimal_harian;
                    return true;
                }
                return false;
            });

        $daftarPks = Pks::whereNotIn('id_pks', [13, 14, 15])->get();

        return view('perizinan_la.index', compact(
            'perizinanList',
            'totalIzin',
            'totalPks',
            'izinAktif',
            'izinPerpanjangan',
            'izinKedaluwarsa',
            'recentOverDebits',
            'daftarPks'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            $daftarPks = Pks::where('id_pks', $user->id_pks)->get();
        } else {
            $daftarPks = Pks::whereNotIn('id_pks', [13, 14, 15])->get();
        }

        return view('perizinan_la.create', compact('daftarPks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_pks' => 'required|exists:pks,id_pks',
            'nomor_sk' => 'required|string|max:100',
            'instansi_penerbit' => 'required|string|max:150',
            'tanggal_terbit' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_terbit',
            'bod_maksimal' => 'required|numeric',
            'ph_min' => 'required|numeric',
            'ph_max' => 'required|numeric',
            'debit_maksimal_harian' => 'required|numeric',
            'luas_areal_izin' => 'required|numeric',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'file_peta' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->except(['file_sk', 'file_peta', 'sumur_nama', 'sumur_jenis', 'sumur_blok', 'sumur_lat', 'sumur_long', 'sumur_text']);

        if ($request->hasFile('file_sk')) {
            $file = $request->file('file_sk');
            $filename = 'SK_LA_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/perizinan_la'), $filename);
            $data['file_sk'] = $filename;
        }

        if ($request->hasFile('file_peta')) {
            $file = $request->file('file_peta');
            $filename = 'PETA_LA_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/perizinan_la'), $filename);
            $data['file_peta'] = $filename;
        }

        $perizinan = PerizinanLa::create($data);

        // Simpan titik sumur pantau jika diisi
        if ($request->has('sumur_nama') && is_array($request->sumur_nama)) {
            foreach ($request->sumur_nama as $index => $namaSumur) {
                if (!empty($namaSumur)) {
                    PerizinanSumurPantau::create([
                        'perizinan_la_id' => $perizinan->id,
                        'nama_sumur' => $namaSumur,
                        'jenis_sumur' => $request->sumur_jenis[$index] ?? 'Sumur Pantau Aplikasi',
                        'lokasi_blok' => $request->sumur_blok[$index] ?? null,
                        'latitude' => $request->sumur_lat[$index] ?? null,
                        'longitude' => $request->sumur_long[$index] ?? null,
                        'koordinat_text' => $request->sumur_text[$index] ?? null,
                        'frekuensi_pantau' => '6 bulan sekali',
                        'parameter_pantau' => 'BOD, DO, pH, NO3, NH3-N, Logam Berat',
                    ]);
                }
            }
        }

        return redirect()->route('perizinan-la.index')
            ->with('success', 'Data Perizinan Land Application berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $perizinan = PerizinanLa::with(['pks.petaBlokLa', 'sumurPantau'])->findOrFail($id);

        // Riwayat pengaliran terbaru untuk PKS ini
        $recentPengaliran = Pengaliran::where('id_pks', $perizinan->id_pks)
            ->orderBy('tanggal', 'desc')
            ->limit(10)
            ->get();

        return view('perizinan_la.show', compact('perizinan', 'recentPengaliran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $perizinan = PerizinanLa::with('sumurPantau')->findOrFail($id);
        $user = Auth::user();

        if (!$user->isAdmin()) {
            $daftarPks = Pks::where('id_pks', $user->id_pks)->get();
        } else {
            $daftarPks = Pks::whereNotIn('id_pks', [13, 14, 15])->get();
        }

        return view('perizinan_la.edit', compact('perizinan', 'daftarPks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $perizinan = PerizinanLa::findOrFail($id);

        $request->validate([
            'id_pks' => 'required|exists:pks,id_pks',
            'nomor_sk' => 'required|string|max:100',
            'instansi_penerbit' => 'required|string|max:150',
            'tanggal_terbit' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_terbit',
            'bod_maksimal' => 'required|numeric',
            'ph_min' => 'required|numeric',
            'ph_max' => 'required|numeric',
            'debit_maksimal_harian' => 'required|numeric',
            'luas_areal_izin' => 'required|numeric',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'file_peta' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->except(['file_sk', 'file_peta', 'sumur_id', 'sumur_nama', 'sumur_jenis', 'sumur_blok', 'sumur_lat', 'sumur_long', 'sumur_text']);

        if ($request->hasFile('file_sk')) {
            if ($perizinan->file_sk && file_exists(public_path('uploads/perizinan_la/' . $perizinan->file_sk))) {
                @unlink(public_path('uploads/perizinan_la/' . $perizinan->file_sk));
            }
            $file = $request->file('file_sk');
            $filename = 'SK_LA_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/perizinan_la'), $filename);
            $data['file_sk'] = $filename;
        }

        if ($request->hasFile('file_peta')) {
            if ($perizinan->file_peta && file_exists(public_path('uploads/perizinan_la/' . $perizinan->file_peta))) {
                @unlink(public_path('uploads/perizinan_la/' . $perizinan->file_peta));
            }
            $file = $request->file('file_peta');
            $filename = 'PETA_LA_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/perizinan_la'), $filename);
            $data['file_peta'] = $filename;
        }

        $perizinan->update($data);

        // Update / create sumur pantau
        if ($request->has('sumur_nama') && is_array($request->sumur_nama)) {
            // Hapus titik sumur lama lalu insert ulang data yang dikirim
            PerizinanSumurPantau::where('perizinan_la_id', $perizinan->id)->delete();

            foreach ($request->sumur_nama as $index => $namaSumur) {
                if (!empty($namaSumur)) {
                    PerizinanSumurPantau::create([
                        'perizinan_la_id' => $perizinan->id,
                        'nama_sumur' => $namaSumur,
                        'jenis_sumur' => $request->sumur_jenis[$index] ?? 'Sumur Pantau Aplikasi',
                        'lokasi_blok' => $request->sumur_blok[$index] ?? null,
                        'latitude' => $request->sumur_lat[$index] ?? null,
                        'longitude' => $request->sumur_long[$index] ?? null,
                        'koordinat_text' => $request->sumur_text[$index] ?? null,
                        'frekuensi_pantau' => '6 bulan sekali',
                        'parameter_pantau' => 'BOD, DO, pH, NO3, NH3-N, Logam Berat',
                    ]);
                }
            }
        }

        return redirect()->route('perizinan-la.show', $perizinan->id)
            ->with('success', 'Data Perizinan Land Application berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $perizinan = PerizinanLa::findOrFail($id);

        if ($perizinan->file_sk && file_exists(public_path('uploads/perizinan_la/' . $perizinan->file_sk))) {
            @unlink(public_path('uploads/perizinan_la/' . $perizinan->file_sk));
        }

        if ($perizinan->file_peta && file_exists(public_path('uploads/perizinan_la/' . $perizinan->file_peta))) {
            @unlink(public_path('uploads/perizinan_la/' . $perizinan->file_peta));
        }

        $perizinan->delete();

        return redirect()->route('perizinan-la.index')
            ->with('success', 'Data Perizinan Land Application berhasil dihapus.');
    }
}
