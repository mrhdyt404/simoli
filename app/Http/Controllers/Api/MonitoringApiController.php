<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlatBerat;
use App\Models\MonitoringAlatBerat;
use App\Models\Pemeliharaan;
use App\Models\Pengaliran;
use App\Models\Pks;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitoringApiController extends Controller
{
    private function getUserFromToken(Request $request)
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $decoded = base64_decode($token, true);
        if ($decoded === false) return null;

        $parts = explode(':', $decoded);

        if (count($parts) < 2) return null;

        $user = User::with('pks')->find($parts[0]);
        if ($user && $user->username === $parts[1]) {
            return $user;
        }

        return null;
    }

    public function dashboardStats(Request $request)
    {
        try {
            $user = $this->getUserFromToken($request);
            $idPks = $request->input('id_pks');

            if ($user && $user->isUnit()) {
                $idPks = $user->id_pks;
            }

            // Monitoring Alat Berat stats
            $alatQuery = MonitoringAlatBerat::query();
            if ($idPks) $alatQuery->where('id_pks', $idPks);

            $totalHm = $alatQuery->sum('total_hm') ?? 0;
            $totalBbm = $alatQuery->sum('bbm_liter') ?? 0;
            $totalKegiatanAlat = $alatQuery->count();

            // Pengaliran stats (column in db is vol_limbah_dialirkan)
            $pengaliranQuery = Pengaliran::query();
            if ($idPks) $pengaliranQuery->where('id_pks', $idPks);
            $totalVolumePengaliran = $pengaliranQuery->sum('vol_limbah_dialirkan') ?? 0;
            $totalPengaliranCount = $pengaliranQuery->count();

            // Pemeliharaan stats
            $pemeliharaanQuery = Pemeliharaan::query();
            if ($idPks) $pemeliharaanQuery->where('id_pks', $idPks);
            $totalBedPemeliharaan = $pemeliharaanQuery->count();

            // Overdue bed pemeliharaan (>90 days without maintenance)
            $limitDate = Carbon::now('Asia/Jakarta')->subDays(90);
            $overdueCount = Pemeliharaan::where('tanggal', '<', $limitDate)
                ->when($idPks, fn($q) => $q->where('id_pks', $idPks))
                ->count();

            // Condition stats for Alat Berat
            $kondisiStats = [
                'baik' => MonitoringAlatBerat::when($idPks, fn($q) => $q->where('id_pks', $idPks))->where('kondisi_alat', 'Baik')->count(),
                'rusak' => MonitoringAlatBerat::when($idPks, fn($q) => $q->where('id_pks', $idPks))->where('kondisi_alat', 'Rusak')->count(),
                'perbaikan' => MonitoringAlatBerat::when($idPks, fn($q) => $q->where('id_pks', $idPks))->where('kondisi_alat', 'Perbaikan')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'total_hm' => round((float) $totalHm, 2),
                    'total_hm_formatted' => MonitoringAlatBerat::formatHm($totalHm, true),
                    'total_bbm_liter' => round((float) $totalBbm, 2),
                    'total_kegiatan_alat' => $totalKegiatanAlat,
                    'total_flat_bed_alat' => (int) ($alatQuery->sum('flat_bed') ?? 0),
                    'total_long_bed_alat' => (int) ($alatQuery->sum('long_bed') ?? 0),
                    'total_bed_alat' => (int) (($alatQuery->sum('flat_bed') + $alatQuery->sum('long_bed')) > 0 ? ($alatQuery->sum('flat_bed') + $alatQuery->sum('long_bed')) : ($alatQuery->sum('jumlah_bed') ?? 0)),
                    'total_volume_pengaliran' => round((float) $totalVolumePengaliran, 2),
                    'total_pengaliran_count' => $totalPengaliranCount,
                    'total_bed_pemeliharaan' => $totalBedPemeliharaan,
                    'overdue_pemeliharaan_count' => $overdueCount,
                    'kondisi_alat_stats' => $kondisiStats,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik dashboard: ' . $e->getMessage(),
                'data' => [
                    'total_hm' => 0,
                    'total_hm_formatted' => '00:00 Jam',
                    'total_bbm_liter' => 0,
                    'total_kegiatan_alat' => 0,
                    'total_volume_pengaliran' => 0,
                    'total_pengaliran_count' => 0,
                    'total_bed_pemeliharaan' => 0,
                    'overdue_pemeliharaan_count' => 0,
                    'kondisi_alat_stats' => ['baik' => 0, 'rusak' => 0, 'perbaikan' => 0],
                ]
            ], 200);
        }
    }

    public function masterData(Request $request)
    {
        $user = $this->getUserFromToken($request);
        $idPks = $user && $user->isUnit() ? $user->id_pks : $request->input('id_pks');

        $pksList = Pks::orderBy('nama')->get(['id_pks', 'nama']);

        $alatBeratQuery = AlatBerat::query()->whereIn('status', ['Operational', 'Standby']);
        if ($idPks) {
            $alatBeratQuery->where(function ($q) use ($idPks) {
                $q->where('id_pks', $idPks)
                  ->orWhereNull('id_pks')
                  ->orWhere('id_pks', 0);
            });
        }
        $alatBeratList = $alatBeratQuery->orderBy('kode_alat')->get(['id', 'id_pks', 'kode_alat', 'jenis_alat', 'nama_alat']);

        // Fallback: If no alat berat found for specific unit, get all master equipment with Operational/Standby status
        if ($alatBeratList->isEmpty()) {
            $alatBeratList = AlatBerat::whereIn('status', ['Operational', 'Standby'])->orderBy('kode_alat')->get(['id', 'id_pks', 'kode_alat', 'jenis_alat', 'nama_alat']);
        }

        return response()->json([
            'success' => true,
            'pks' => $pksList,
            'alat_berat' => $alatBeratList
        ]);
    }

    public function monitoringAlatBeratList(Request $request)
    {
        $user = $this->getUserFromToken($request);
        $query = MonitoringAlatBerat::with(['pks', 'alatBerat']);

        if ($user && $user->isUnit()) {
            $query->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $query->where('id_pks', $request->id_pks);
        }

        if ($request->filled('alat_berat_id')) {
            $query->where('alat_berat_id', $request->alat_berat_id);
        }

        if ($request->filled('kondisi_alat')) {
            $query->where('kondisi_alat', $request->kondisi_alat);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('operator', 'like', "%{$search}%")
                  ->orWhere('kegiatan', 'like', "%{$search}%")
                  ->orWhere('lokasi_blok', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(20);

        $items = collect($logs->items())->map(function ($log) {
            return [
                'id' => $log->id,
                'id_pks' => $log->id_pks,
                'nama_pks' => $log->pks ? $log->pks->nama : null,
                'alat_berat_id' => $log->alat_berat_id,
                'kode_alat' => $log->alatBerat ? $log->alatBerat->kode_alat : 'ALAT',
                'jenis_alat' => $log->alatBerat ? ($log->alatBerat->jenis_alat ?? $log->alatBerat->nama_alat) : 'Alat Berat',
                'tanggal' => $log->tanggal ? $log->tanggal->format('Y-m-d') : null,
                'operator' => $log->operator,
                'kegiatan' => $log->kegiatan,
                'lokasi_blok' => $log->lokasi_blok,
                'flat_bed' => (int) ($log->flat_bed ?? 0),
                'long_bed' => (int) ($log->long_bed ?? 0),
                'jumlah_bed' => (int) ($log->jumlah_bed ?? 0),
                'latitude' => $log->latitude,
                'longitude' => $log->longitude,
                'maps_url' => $log->google_maps_url,
                'hm_awal' => $log->hm_awal_formatted,
                'hm_akhir' => $log->hm_akhir_formatted,
                'total_hm' => $log->total_hm,
                'total_hm_formatted' => $log->total_hm_formatted,
                'bbm_liter' => $log->bbm_liter,
                'kondisi_alat' => $log->kondisi_alat,
                'foto' => $log->foto ? asset($log->foto) : null,
                'foto_sebelum' => $log->foto_sebelum ? asset($log->foto_sebelum) : null,
                'foto_sesudah' => $log->foto_sesudah ? asset($log->foto_sesudah) : null,
                'catatan' => $log->catatan,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'current_page' => $logs->currentPage(),
            'last_page' => $logs->lastPage(),
            'total' => $logs->total()
        ]);
    }

    public function monitoringAlatBeratDetail($id)
    {
        $log = MonitoringAlatBerat::with(['pks', 'alatBerat'])->find($id);

        if (!$log) {
            return response()->json(['success' => false, 'message' => 'Data log tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $log->id,
                'id_pks' => $log->id_pks,
                'nama_pks' => $log->pks ? $log->pks->nama : null,
                'alat_berat_id' => $log->alat_berat_id,
                'kode_alat' => $log->alatBerat ? $log->alatBerat->kode_alat : 'ALAT',
                'jenis_alat' => $log->alatBerat ? ($log->alatBerat->jenis_alat ?? $log->alatBerat->nama_alat) : 'Alat Berat',
                'tanggal' => $log->tanggal ? $log->tanggal->format('Y-m-d') : null,
                'operator' => $log->operator,
                'kegiatan' => $log->kegiatan,
                'lokasi_blok' => $log->lokasi_blok,
                'flat_bed' => (int) ($log->flat_bed ?? 0),
                'long_bed' => (int) ($log->long_bed ?? 0),
                'jumlah_bed' => (int) ($log->jumlah_bed ?? 0),
                'latitude' => $log->latitude,
                'longitude' => $log->longitude,
                'maps_url' => $log->google_maps_url,
                'hm_awal' => $log->hm_awal_formatted,
                'hm_akhir' => $log->hm_akhir_formatted,
                'total_hm' => $log->total_hm,
                'total_hm_formatted' => $log->total_hm_formatted,
                'bbm_liter' => $log->bbm_liter,
                'kondisi_alat' => $log->kondisi_alat,
                'foto' => $log->foto ? asset($log->foto) : null,
                'foto_sebelum' => $log->foto_sebelum ? asset($log->foto_sebelum) : null,
                'foto_sesudah' => $log->foto_sesudah ? asset($log->foto_sesudah) : null,
                'catatan' => $log->catatan,
            ]
        ]);
    }

    public function storeMonitoringAlatBerat(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'alat_berat_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('alat_berat', 'id')->where(function ($q) {
                    $q->whereIn('status', ['Operational', 'Standby']);
                }),
            ],
            'tanggal' => 'required|date',
            'operator' => 'required|string',
            'kegiatan' => 'required|string',
            'lokasi_blok' => 'required|string',
            'kondisi_alat' => 'required|string',
        ]);

        $log = new MonitoringAlatBerat();
        $log->id_pks = $user->isUnit() ? $user->id_pks : ($request->id_pks ?? $user->id_pks ?? 1);
        $log->alat_berat_id = $request->alat_berat_id;
        $log->tanggal = $request->tanggal;
        $log->operator = $request->operator;
        $log->kegiatan = $request->kegiatan;
        $log->lokasi_blok = $request->lokasi_blok;
        $log->flat_bed = (int) ($request->flat_bed ?? 0);
        $log->long_bed = (int) ($request->long_bed ?? 0);
        $log->jumlah_bed = ($log->flat_bed + $log->long_bed) > 0 ? ($log->flat_bed + $log->long_bed) : (int) ($request->jumlah_bed ?? 0);
        $log->latitude = $request->latitude;
        $log->longitude = $request->longitude;

        // HM Awal terkunci (tidak boleh diubah) jika sudah terisi sebelumnya untuk mencegah rekayasa
        if (empty($log->hm_awal)) {
            if ($request->filled('hm_awal')) {
                $log->hm_awal = MonitoringAlatBerat::parseTimestamp($request->hm_awal, $request->tanggal);
            }
        }
        // HM Akhir terkunci (tidak boleh diubah) jika sudah terisi sebelumnya untuk mencegah rekayasa
        if (empty($log->hm_akhir)) {
            if ($request->filled('hm_akhir')) {
                $log->hm_akhir = MonitoringAlatBerat::parseTimestamp($request->hm_akhir, $request->tanggal);
            }
        }

        if ($request->filled('total_hm')) {
            $log->total_hm = $request->total_hm;
        } elseif ($log->hm_awal && $log->hm_akhir) {
            $start = Carbon::parse($log->hm_awal);
            $end = Carbon::parse($log->hm_akhir);
            if ($end->lessThan($start)) {
                $end->addDay();
            }
            $diffMins = abs($start->diffInMinutes($end));
            $log->total_hm = round($diffMins / 60, 2);
        }

        $log->bbm_liter = $request->bbm_liter ?? 0;
        $log->kondisi_alat = $request->kondisi_alat;
        $log->catatan = $request->catatan;

        // File upload photo before & after
        $uploadDir = 'uploads/monitoring_alat_berat';
        if (!file_exists(public_path($uploadDir))) {
            mkdir(public_path($uploadDir), 0777, true);
        }

        if ($request->hasFile('foto_sebelum')) {
            $file = $request->file('foto_sebelum');
            $filename = 'sebelum_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($uploadDir), $filename);
            $log->foto_sebelum = $uploadDir . '/' . $filename;
            $log->foto = $log->foto_sebelum;
        }

        if ($request->hasFile('foto_sesudah')) {
            $file = $request->file('foto_sesudah');
            $filename = 'sesudah_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($uploadDir), $filename);
            $log->foto_sesudah = $uploadDir . '/' . $filename;
        }

        $log->save();

        return response()->json([
            'success' => true,
            'message' => 'Log Monitoring Alat Berat berhasil disimpan',
            'id' => $log->id
        ], 201);
    }

    public function pengaliranList(Request $request)
    {
        $user = $this->getUserFromToken($request);
        $query = Pengaliran::with('pks');

        if ($user && $user->isUnit()) {
            $query->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $query->where('id_pks', $request->id_pks);
        }

        $logs = $query->orderBy('tanggal', 'desc')->paginate(20);

        $items = collect($logs->items())->map(function ($item) {
            return [
                'id' => $item->id_pengaliran,
                'id_pks' => $item->id_pks,
                'nama_pks' => $item->pks ? $item->pks->nama : null,
                'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal)->format('Y-m-d') : null,
                'lokasi_blok' => $item->blok,
                'bed_id' => $item->no_bak ?? '-',
                'volume_m3' => (float) ($item->vol_limbah_dialirkan ?? 0),
                'debit' => $item->jam_mulai && $item->jam_selesai ? "{$item->jam_mulai} - {$item->jam_selesai}" : '-',
                'keterangan' => $item->keterangan ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'total' => $logs->total()
        ]);
    }

    public function pemeliharaanList(Request $request)
    {
        $user = $this->getUserFromToken($request);
        $query = Pemeliharaan::with('pks');

        if ($user && $user->isUnit()) {
            $query->where('id_pks', $user->id_pks);
        } elseif ($request->filled('id_pks')) {
            $query->where('id_pks', $request->id_pks);
        }

        $logs = $query->orderBy('tanggal', 'desc')->paginate(20);

        $items = collect($logs->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'id_pks' => $item->id_pks,
                'nama_pks' => $item->pks ? $item->pks->nama : null,
                'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal)->format('Y-m-d') : null,
                'lokasi_blok' => $item->blok,
                'bed_id' => $item->no_bak ?? '-',
                'jenis_kegiatan' => $item->jenis_label ?? 'Pemeliharaan',
                'status_bed' => "Flat: {$item->flat_bed} / Long: {$item->long_bed}",
                'foto_sebelum' => $item->sebelum && $item->sebelum !== '-' ? asset('uploads/pemeliharaan/' . $item->sebelum) : null,
                'foto_sesudah' => $item->sesudah && $item->sesudah !== '-' ? asset('uploads/pemeliharaan/' . $item->sesudah) : null,
                'keterangan' => $item->keterangan ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'total' => $logs->total()
        ]);
    }
}
