<?php

namespace App\Http\Controllers;

use App\Models\AlatBerat;
use App\Models\Pks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlatBeratController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = AlatBerat::with('pks');

        if ($user->isUnit()) {
            $query->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $query->where('id_pks', $request->id_pks);
        }

        if ($request->filled('jenis_alat')) {
            $query->where('jenis_alat', $request->jenis_alat);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_alat', 'like', "%{$search}%")
                  ->orWhere('nama_alat', 'like', "%{$search}%")
                  ->orWhere('merk_tipe', 'like', "%{$search}%");
            });
        }

        $alatBerat = $query->orderBy('kode_alat', 'asc')->paginate(15)->withQueryString();
        $pksList = Pks::orderBy('nama')->get();

        return view('alat_berat.index', compact('alatBerat', 'pksList'));
    }

    public function create()
    {
        $user = Auth::user();
        $pksList = Pks::orderBy('nama')->get();

        return view('alat_berat.create', compact('pksList', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'id_pks' => $user->isAdmin() ? 'required|exists:pks,id_pks' : 'nullable',
            'kode_alat' => 'required|string|max:30|unique:alat_berat,kode_alat',
            'nama_alat' => 'required|string|max:100',
            'jenis_alat' => 'required|in:Excavator,Wheel Loader,Bulldozer,Dump Truck,Compactor,Lainnya',
            'merk_tipe' => 'nullable|string|max:100',
            'tahun_pengadaan' => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
            'status' => 'required|in:Operational,Maintenance,Breakdown,Standby,Rolling',
            'keterangan' => 'nullable|string',
        ]);

        $idPks = $user->isUnit() ? $user->id_pks : $request->id_pks;

        AlatBerat::create([
            'id_pks' => $idPks,
            'kode_alat' => strtoupper($request->kode_alat),
            'nama_alat' => $request->nama_alat,
            'jenis_alat' => $request->jenis_alat,
            'merk_tipe' => $request->merk_tipe,
            'tahun_pengadaan' => $request->tahun_pengadaan,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('alat-berat.index')->with('success', 'Data alat berat berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $alatBerat = AlatBerat::findOrFail($id);

        if ($user->isUnit() && $alatBerat->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data alat berat ini.');
        }

        $pksList = Pks::orderBy('nama')->get();

        return view('alat_berat.edit', compact('alatBerat', 'pksList', 'user'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $alatBerat = AlatBerat::findOrFail($id);

        if ($user->isUnit() && $alatBerat->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data alat berat ini.');
        }

        $request->validate([
            'id_pks' => $user->isAdmin() ? 'required|exists:pks,id_pks' : 'nullable',
            'kode_alat' => 'required|string|max:30|unique:alat_berat,kode_alat,' . $id,
            'nama_alat' => 'required|string|max:100',
            'jenis_alat' => 'required|in:Excavator,Wheel Loader,Bulldozer,Dump Truck,Compactor,Lainnya',
            'merk_tipe' => 'nullable|string|max:100',
            'tahun_pengadaan' => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
            'status' => 'required|in:Operational,Maintenance,Breakdown,Standby,Rolling',
            'keterangan' => 'nullable|string',
        ]);

        $idPks = $user->isUnit() ? $user->id_pks : $request->id_pks;

        $alatBerat->update([
            'id_pks' => $idPks,
            'kode_alat' => strtoupper($request->kode_alat),
            'nama_alat' => $request->nama_alat,
            'jenis_alat' => $request->jenis_alat,
            'merk_tipe' => $request->merk_tipe,
            'tahun_pengadaan' => $request->tahun_pengadaan,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('alat-berat.index')->with('success', 'Data alat berat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $alatBerat = AlatBerat::findOrFail($id);

        if ($user->isUnit() && $alatBerat->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data alat berat ini.');
        }

        $alatBerat->delete();

        return redirect()->route('alat-berat.index')->with('success', 'Data alat berat berhasil dihapus.');
    }
}
