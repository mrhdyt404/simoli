<?php

namespace App\Http\Controllers;

use App\Models\AlatBerat;
use App\Models\MonitoringAlatBerat;
use App\Models\Pks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class MonitoringAlatBeratController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = MonitoringAlatBerat::with(['pks', 'alatBerat']);

        if ($user->isUnit()) {
            $query->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $query->where('id_pks', $request->id_pks);
        }

        if ($request->filled('alat_berat_id')) {
            $query->where('alat_berat_id', $request->alat_berat_id);
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        if ($request->filled('kondisi_alat')) {
            $query->where('kondisi_alat', $request->kondisi_alat);
        }

        $logs = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Summary statistics
        $statsQuery = MonitoringAlatBerat::query();
        if ($user->isUnit()) {
            $statsQuery->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $statsQuery->where('id_pks', $request->id_pks);
        }
        if ($request->filled('bulan')) {
            $statsQuery->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $statsQuery->whereYear('tanggal', $request->tahun);
        }

        $totalHm = $statsQuery->sum('total_hm');
        $totalBbm = $statsQuery->sum('bbm_liter');
        $totalFlatBed = $statsQuery->sum('flat_bed');
        $totalLongBed = $statsQuery->sum('long_bed');
        $totalBed = $totalFlatBed + $totalLongBed;
        if ($totalBed === 0) {
            $totalBed = $statsQuery->sum('jumlah_bed');
        }
        $totalKegiatan = $statsQuery->count();

        $pksList = Pks::orderBy('nama')->get();

        $alatBeratQuery = AlatBerat::query();
        if ($user->isUnit()) {
            $alatBeratQuery->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $alatBeratQuery->where('id_pks', $request->id_pks);
        }
        $alatBeratList = $alatBeratQuery->orderBy('kode_alat')->get();

        return view('monitoring_alat_berat.index', compact(
            'logs', 'pksList', 'alatBeratList', 'totalHm', 'totalBbm', 'totalFlatBed', 'totalLongBed', 'totalBed', 'totalKegiatan'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            abort(403, 'Admin hanya memiliki akses untuk melihat data monitoring alat berat.');
        }

        $pksList = Pks::orderBy('nama')->get();

        $alatBeratQuery = AlatBerat::query();
        if ($user->isUnit()) {
            $alatBeratQuery->where('id_pks', $user->id_pks);
        }
        $alatBeratQuery->whereIn('status', ['Operational', 'Standby']);
        $alatBeratList = $alatBeratQuery->orderBy('kode_alat')->get();

        return view('monitoring_alat_berat.create', compact('pksList', 'alatBeratList', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            abort(403, 'Admin hanya memiliki akses untuk melihat data monitoring alat berat.');
        }

        // Prioritaskan input manual hm_awal (jika diisi), baru foto_sebelum
        if ($request->filled('hm_awal')) {
            $hmAwalTs = MonitoringAlatBerat::parseTimestamp($request->hm_awal, $request->tanggal);
        } elseif ($request->hasFile('foto_sebelum')) {
            $hmAwalTs = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        } else {
            $hmAwalTs = null;
        }

        // Prioritaskan input manual hm_akhir (jika diisi), baru foto_sesudah
        if ($request->filled('hm_akhir')) {
            $hmAkhirTs = MonitoringAlatBerat::parseTimestamp($request->hm_akhir, $request->tanggal);
        } elseif ($request->hasFile('foto_sesudah')) {
            $hmAkhirTs = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        } else {
            $hmAkhirTs = null;
        }

        $totalHm = 0.0;
        $valAwalStr = str_replace(',', '.', trim((string)$request->hm_awal));
        $valAkhirStr = str_replace(',', '.', trim((string)$request->hm_akhir));

        if ($request->filled('hm_awal') && $request->filled('hm_akhir') && is_numeric($valAwalStr) && is_numeric($valAkhirStr)) {
            $totalHm = round(max(0, (float)$valAkhirStr - (float)$valAwalStr), 2);
        } elseif ($hmAwalTs && $hmAkhirTs) {
            $cStart = \Carbon\Carbon::parse($hmAwalTs);
            $cEnd = \Carbon\Carbon::parse($hmAkhirTs);
            if ($cEnd->lessThan($cStart)) {
                $cEnd->addDay();
            }
            $diffMinutes = abs($cStart->diffInMinutes($cEnd));
            $totalHm = round($diffMinutes / 60, 2);
        }

        $request->validate([
            'id_pks' => $user->isAdmin() ? 'required|exists:pks,id_pks' : 'nullable',
            'alat_berat_id' => [
                'required',
                Rule::exists('alat_berat', 'id')->where(function ($q) {
                    $q->whereIn('status', ['Operational', 'Standby']);
                }),
            ],
            'tanggal' => 'required|date',
            'operator' => 'required|string|max:100',
            'kegiatan' => 'required|string|max:150',
            'lokasi_blok' => 'nullable|string|max:100',
            'flat_bed' => 'nullable|integer|min:0',
            'long_bed' => 'nullable|integer|min:0',
            'jumlah_bed' => 'nullable|integer|min:0',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'hm_awal' => 'nullable',
            'hm_akhir' => 'nullable',
            'bbm_liter' => 'nullable|numeric|min:0',
            'kondisi_alat' => 'required|in:Normal,Perlu Perbaikan,Breakdown',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'foto_sebelum' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'foto_sesudah' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'catatan' => 'nullable|string',
        ]);

        $idPks = $user->isUnit() ? $user->id_pks : $request->id_pks;

        $filename = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('gallery'), $filename);
        }

        $filenameSebelum = null;
        if ($request->hasFile('foto_sebelum')) {
            $file = $request->file('foto_sebelum');
            $filenameSebelum = time() . '_sebelum_' . $file->getClientOriginalName();
            $file->move(public_path('gallery'), $filenameSebelum);
        }

        $filenameSesudah = null;
        if ($request->hasFile('foto_sesudah')) {
            $file = $request->file('foto_sesudah');
            $filenameSesudah = time() . '_sesudah_' . $file->getClientOriginalName();
            $file->move(public_path('gallery'), $filenameSesudah);
        }

        $flatBed = $request->flat_bed ?? 0;
        $longBed = $request->long_bed ?? 0;
        $jumlahBed = ($flatBed + $longBed) > 0 ? ($flatBed + $longBed) : ($request->jumlah_bed ?? 0);

        MonitoringAlatBerat::create([
            'id_pks' => $idPks,
            'alat_berat_id' => $request->alat_berat_id,
            'tanggal' => $request->tanggal,
            'operator' => $request->operator,
            'kegiatan' => $request->kegiatan,
            'lokasi_blok' => $request->lokasi_blok,
            'flat_bed' => $flatBed,
            'long_bed' => $longBed,
            'jumlah_bed' => $jumlahBed,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'hm_awal' => $hmAwalTs,
            'hm_akhir' => $hmAkhirTs,
            'total_hm' => $totalHm,
            'bbm_liter' => $request->bbm_liter ?? 0,
            'kondisi_alat' => $request->kondisi_alat,
            'foto' => $filename ?? $filenameSebelum,
            'foto_sebelum' => $filenameSebelum,
            'foto_sesudah' => $filenameSesudah,
            'catatan' => $request->catatan,
        ]);

        // Auto update status alat berat jika status breakdown/perlu perbaikan
        $alatBerat = AlatBerat::find($request->alat_berat_id);
        if ($alatBerat) {
            if ($request->kondisi_alat === 'Breakdown') {
                $alatBerat->update(['status' => 'Breakdown']);
            } elseif ($request->kondisi_alat === 'Perlu Perbaikan') {
                $alatBerat->update(['status' => 'Maintenance']);
            }
        }

        return redirect()->route('monitoring-alat-berat.index')->with('success', 'Log monitoring alat berat berhasil disimpan.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $log = MonitoringAlatBerat::with(['pks', 'alatBerat'])->findOrFail($id);

        if ($user->isUnit() && $log->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        return view('monitoring_alat_berat.show', compact('log'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            abort(403, 'Admin hanya memiliki akses untuk melihat data monitoring alat berat.');
        }

        $log = MonitoringAlatBerat::with(['pks', 'alatBerat'])->findOrFail($id);

        if ($user->isUnit() && $log->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah log ini.');
        }

        $pksList = Pks::orderBy('nama')->get();

        $alatBeratQuery = AlatBerat::query();
        if ($user->isUnit()) {
            $alatBeratQuery->where('id_pks', $user->id_pks);
        }
        $alatBeratQuery->where(function($q) use ($log) {
            $q->whereIn('status', ['Operational', 'Standby']);
            if ($log->alat_berat_id) {
                $q->orWhere('id', $log->alat_berat_id);
            }
        });
        $alatBeratList = $alatBeratQuery->orderBy('kode_alat')->get();

        return view('monitoring_alat_berat.edit', compact('log', 'pksList', 'alatBeratList', 'user'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            abort(403, 'Admin hanya memiliki akses untuk melihat data monitoring alat berat.');
        }

        $log = MonitoringAlatBerat::findOrFail($id);

        if ($user->isUnit() && $log->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah log ini.');
        }

        // HM Awal terkunci (tidak boleh diubah) jika sudah terisi sebelumnya atau foto_sebelum sudah ada untuk mencegah rekayasa data
        if (!empty($log->hm_awal)) {
            $hmAwalTs = $log->hm_awal;
        } elseif ($request->filled('hm_awal')) {
            $hmAwalTs = MonitoringAlatBerat::parseTimestamp($request->hm_awal, $request->tanggal);
        } elseif ($request->hasFile('foto_sebelum')) {
            $hmAwalTs = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        } else {
            $hmAwalTs = null;
        }

        // HM Akhir terkunci (tidak boleh diubah) jika sudah terisi sebelumnya atau foto_sesudah sudah ada untuk mencegah rekayasa data
        if (!empty($log->hm_akhir)) {
            $hmAkhirTs = $log->hm_akhir;
        } elseif ($request->filled('hm_akhir')) {
            $hmAkhirTs = MonitoringAlatBerat::parseTimestamp($request->hm_akhir, $request->tanggal);
        } elseif ($request->hasFile('foto_sesudah')) {
            $hmAkhirTs = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        } else {
            $hmAkhirTs = null;
        }

        $totalHm = $log->total_hm;
        $valAwalStr = str_replace(',', '.', trim((string)$request->hm_awal));
        $valAkhirStr = str_replace(',', '.', trim((string)$request->hm_akhir));

        if ($request->filled('hm_awal') && $request->filled('hm_akhir') && is_numeric($valAwalStr) && is_numeric($valAkhirStr)) {
            $totalHm = round(max(0, (float)$valAkhirStr - (float)$valAwalStr), 2);
        } elseif ($hmAwalTs && $hmAkhirTs) {
            $cStart = \Carbon\Carbon::parse($hmAwalTs);
            $cEnd = \Carbon\Carbon::parse($hmAkhirTs);
            if ($cEnd->lessThan($cStart)) {
                $cEnd->addDay();
            }
            $diffMinutes = abs($cStart->diffInMinutes($cEnd));
            $totalHm = round($diffMinutes / 60, 2);
        }

        $request->validate([
            'id_pks' => $user->isAdmin() ? 'required|exists:pks,id_pks' : 'nullable',
            'alat_berat_id' => [
                'required',
                Rule::exists('alat_berat', 'id')->where(function ($q) use ($log) {
                    $q->whereIn('status', ['Operational', 'Standby']);
                    if ($log->alat_berat_id) {
                        $q->orWhere('id', $log->alat_berat_id);
                    }
                }),
            ],
            'tanggal' => 'required|date',
            'operator' => 'required|string|max:100',
            'kegiatan' => 'required|string|max:150',
            'lokasi_blok' => 'nullable|string|max:100',
            'flat_bed' => 'nullable|integer|min:0',
            'long_bed' => 'nullable|integer|min:0',
            'jumlah_bed' => 'nullable|integer|min:0',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'hm_awal' => 'nullable',
            'hm_akhir' => 'nullable',
            'bbm_liter' => 'nullable|numeric|min:0',
            'kondisi_alat' => 'required|in:Normal,Perlu Perbaikan,Breakdown',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'foto_sebelum' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'foto_sesudah' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'catatan' => 'nullable|string',
        ]);

        $idPks = $user->isUnit() ? $user->id_pks : $request->id_pks;

        $filename = $log->foto;
        if ($request->hasFile('foto')) {
            if ($log->foto && File::exists(public_path('gallery/' . $log->foto))) {
                File::delete(public_path('gallery/' . $log->foto));
            }
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('gallery'), $filename);
        }

        $filenameSebelum = $log->foto_sebelum;
        if ($request->hasFile('foto_sebelum')) {
            if ($log->foto_sebelum && File::exists(public_path('gallery/' . $log->foto_sebelum))) {
                File::delete(public_path('gallery/' . $log->foto_sebelum));
            }
            $file = $request->file('foto_sebelum');
            $filenameSebelum = time() . '_sebelum_' . $file->getClientOriginalName();
            $file->move(public_path('gallery'), $filenameSebelum);
        }

        $filenameSesudah = $log->foto_sesudah;
        if ($request->hasFile('foto_sesudah')) {
            if ($log->foto_sesudah && File::exists(public_path('gallery/' . $log->foto_sesudah))) {
                File::delete(public_path('gallery/' . $log->foto_sesudah));
            }
            $file = $request->file('foto_sesudah');
            $filenameSesudah = time() . '_sesudah_' . $file->getClientOriginalName();
            $file->move(public_path('gallery'), $filenameSesudah);
        }

        $flatBed = $request->flat_bed ?? 0;
        $longBed = $request->long_bed ?? 0;
        $jumlahBed = ($flatBed + $longBed) > 0 ? ($flatBed + $longBed) : ($request->jumlah_bed ?? 0);

        $log->update([
            'id_pks' => $idPks,
            'alat_berat_id' => $request->alat_berat_id,
            'tanggal' => $request->tanggal,
            'operator' => $request->operator,
            'kegiatan' => $request->kegiatan,
            'lokasi_blok' => $request->lokasi_blok,
            'flat_bed' => $flatBed,
            'long_bed' => $longBed,
            'jumlah_bed' => $jumlahBed,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'hm_awal' => $hmAwalTs,
            'hm_akhir' => $hmAkhirTs,
            'total_hm' => $totalHm,
            'bbm_liter' => $request->bbm_liter ?? 0,
            'kondisi_alat' => $request->kondisi_alat,
            'foto' => $filename ?? $filenameSebelum ?? $log->foto,
            'foto_sebelum' => $filenameSebelum,
            'foto_sesudah' => $filenameSesudah,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('monitoring-alat-berat.index')->with('success', 'Log monitoring alat berat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            abort(403, 'Admin hanya memiliki akses untuk melihat data monitoring alat berat.');
        }

        $log = MonitoringAlatBerat::findOrFail($id);

        if ($user->isUnit() && $log->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus log ini.');
        }

        if ($log->foto && File::exists(public_path('gallery/' . $log->foto))) {
            File::delete(public_path('gallery/' . $log->foto));
        }
        if ($log->foto_sebelum && File::exists(public_path('gallery/' . $log->foto_sebelum))) {
            File::delete(public_path('gallery/' . $log->foto_sebelum));
        }
        if ($log->foto_sesudah && File::exists(public_path('gallery/' . $log->foto_sesudah))) {
            File::delete(public_path('gallery/' . $log->foto_sesudah));
        }

        $log->delete();

        return redirect()->route('monitoring-alat-berat.index')->with('success', 'Log monitoring alat berat berhasil dihapus.');
    }
}
