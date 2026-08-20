<?php

namespace App\Http\Controllers;

use App\Models\AlatBerat;
use App\Models\MonitoringAlatBerat;
use App\Models\Pks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class OperatorMonitoringController extends Controller
{
    /**
     * Mobile Home / Dashboard for Field Operator
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        // Query Equipment for Operator's PKS
        $alatBeratQuery = AlatBerat::query();
        if ($user->id_pks) {
            $alatBeratQuery->where('id_pks', $user->id_pks);
        }
        $alatBeratList = $alatBeratQuery->orderBy('kode_alat')->get();

        // Query Work Logs for Operator's PKS
        $logQuery = MonitoringAlatBerat::with(['pks', 'alatBerat']);
        if ($user->id_pks) {
            $logQuery->where('id_pks', $user->id_pks);
        }

        if ($request->filled('tanggal')) {
            $logQuery->whereDate('tanggal', $request->tanggal);
        } else {
            // Default to today or recent 15
            $logQuery->whereDate('tanggal', '>=', Carbon::now()->subDays(7)->toDateString());
        }

        if ($request->filled('alat_berat_id')) {
            $logQuery->where('alat_berat_id', $request->alat_berat_id);
        }

        $logs = $logQuery->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(10)->withQueryString();

        // Filtered statistics matching current query period
        $periodQuery = MonitoringAlatBerat::query();
        if ($user->id_pks) {
            $periodQuery->where('id_pks', $user->id_pks);
        }
        if ($request->filled('tanggal')) {
            $periodQuery->whereDate('tanggal', $request->tanggal);
        } else {
            $periodQuery->whereDate('tanggal', '>=', Carbon::now()->subDays(7)->toDateString());
        }
        if ($request->filled('alat_berat_id')) {
            $periodQuery->where('alat_berat_id', $request->alat_berat_id);
        }
        $periodLogs = $periodQuery->get();

        $stats = [
            'total_laporan_today' => $periodLogs->count(),
            'total_hm_today' => round($periodLogs->sum('total_hm'), 2),
            'total_bbm_today' => round($periodLogs->sum('bbm_liter'), 2),
            'total_bed_today' => $periodLogs->sum('jumlah_bed'),
            'ready_units' => $alatBeratList->where('status', 'Operational')->count(),
            'standby_units' => $alatBeratList->where('status', 'Standby')->count(),
        ];

        return view('operator.index', compact('user', 'logs', 'alatBeratList', 'stats', 'tanggal'));
    }

    /**
     * Helper to format HM display
     */
    public static function formatHmDisplay($totalHm, $hmAwal = null, $hmAkhir = null): string
    {
        $val = (float) $totalHm;
        if ($val <= 0) {
            if (!empty($hmAwal) && empty($hmAkhir)) {
                return 'Proses';
            }
            return '00:00 Jam';
        }

        return MonitoringAlatBerat::formatHm($val, true);
    }

    /**
     * Default baseline capacity (Total Bed) for the 12 PKS mills
     */
    private array $defaultTotalBeds = [
        'TPU' => 15112,
        'TME' => 6768,
        'SGO' => 3382,
        'SPA' => 7490,
        'SGH' => 3460,
        'SBT' => 15370,
        'LDA' => 6985,
        'TAN' => 10344,
        'TER' => 5691,
        'STA' => 8370,
        'SRO' => 6297,
        'SIN' => 4657,
    ];

    /**
     * Helper to get PKS progress for Land Application matching the report table format
     */
    public function getPksProgress($idPks): object
    {
        $pks = Pks::find($idPks);
        $akro = strtoupper($pks->akro ?? '');
        $tahun = (int) date('Y');
        $bulan = (int) date('n');

        // Total Bed determination
        $rencana = \App\Models\Rencana::where('id_pks', $idPks)->where('tahun', $tahun)->first();
        if ($rencana && ($rencana->flat_bed > 0 || $rencana->long_bed > 0)) {
            $totalBed = $rencana->flat_bed ?: ($rencana->flat_bed + $rencana->long_bed);
        } else {
            $totalBed = $this->defaultTotalBeds[$akro] ?? 0;
        }

        // Date ranges
        $startOfMonth = Carbon::now('Asia/Jakarta')->startOfMonth();
        $endOfMonth = Carbon::now('Asia/Jakarta')->endOfMonth();
        $startOfWeek = Carbon::now('Asia/Jakarta')->startOfWeek();
        $endOfWeek = Carbon::now('Asia/Jakarta')->endOfWeek();

        // Month records from Pengaliran & Monitoring
        $pengaliranMonth = \App\Models\Pengaliran::where('id_pks', $idPks)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $monitoringMonth = MonitoringAlatBerat::where('id_pks', $idPks)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        // Week records
        $pengaliranWeek = $pengaliranMonth->filter(fn($item) => Carbon::parse($item->tanggal)->betweenIncluded($startOfWeek, $endOfWeek));
        $monitoringWeek = $monitoringMonth->filter(fn($item) => Carbon::parse($item->tanggal)->betweenIncluded($startOfWeek, $endOfWeek));

        $formatList = function($items1, $field1, $items2 = null, $field2 = null) {
            $collected = [];
            foreach ($items1 as $item) {
                $val = trim((string)($item->{$field1} ?? ''));
                if ($val !== '' && $val !== '-') {
                    $parts = preg_split('/[\/,\s]+/', $val, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($parts as $p) {
                        $p = trim($p);
                        if ($p !== '' && $p !== '-' && !in_array($p, $collected)) $collected[] = $p;
                    }
                }
            }
            if ($items2 && $field2) {
                foreach ($items2 as $item) {
                    $val = trim((string)($item->{$field2} ?? ''));
                    if ($val !== '' && $val !== '-') {
                        $parts = preg_split('/[\/,\s]+/', $val, -1, PREG_SPLIT_NO_EMPTY);
                        foreach ($parts as $p) {
                            $p = trim($p);
                            if ($p !== '' && $p !== '-' && !in_array($p, $collected)) $collected[] = $p;
                        }
                    }
                }
            }
            return empty($collected) ? '-' : implode(', ', $collected);
        };

        $bedMinggu = (int)$pengaliranWeek->sum('flat_bed') + (int)$monitoringWeek->sum('flat_bed');
        $blokMinggu = $formatList($pengaliranWeek, 'blok', $monitoringWeek, 'lokasi_blok');
        $bakMinggu = $formatList($pengaliranWeek, 'no_bak', $monitoringWeek, 'no_bak');

        $bedBulan = (int)$pengaliranMonth->sum('flat_bed') + (int)$monitoringMonth->sum('flat_bed');
        $blokBulan = $formatList($pengaliranMonth, 'blok', $monitoringMonth, 'lokasi_blok');
        $bakBulan = $formatList($pengaliranMonth, 'no_bak', $monitoringMonth, 'no_bak');

        return (object) [
            'pks' => $pks,
            'akro' => $akro,
            'nama' => $pks->nama ?? 'Unit PKS',
            'total_bed' => $totalBed,
            'minggu_ini' => (object) [
                'bed_dialirkan' => $bedMinggu,
                'blok' => $blokMinggu,
                'bak' => $bakMinggu,
            ],
            'sd_bulan_ini' => (object) [
                'bed_dialirkan' => $bedBulan,
                'blok' => $blokBulan,
                'bak' => $bakBulan,
            ],
            'keterangan' => 'Pengaliran Limbah lancar',
        ];
    }

    /**
     * Show form to create new heavy equipment work report
     */
    public function create()
    {
        $user = Auth::user();

        // Get equipment filtered to Operational & Standby status
        $alatBeratQuery = AlatBerat::query();
        if ($user->id_pks) {
            $alatBeratQuery->where('id_pks', $user->id_pks);
        }
        $alatBeratList = $alatBeratQuery->whereIn('status', ['Operational', 'Standby'])
            ->orderBy('kode_alat')
            ->get();

        $pks = $user->pks ?? Pks::find($user->id_pks);
        $pksProgress = $this->getPksProgress($user->id_pks);

        return view('operator.create', compact('user', 'alatBeratList', 'pks', 'pksProgress'));
    }

    /**
     * Store new work report
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
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
            'no_bak' => 'nullable|string|max:100',
            'flat_bed' => 'nullable|integer|min:0',
            'long_bed' => 'nullable|integer|min:0',
            'jumlah_bed' => 'nullable|integer|min:0',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'latitude_awal' => 'nullable|numeric|between:-90,90',
            'longitude_awal' => 'nullable|numeric|between:-180,180',
            'latitude_akhir' => 'nullable|numeric|between:-90,90',
            'longitude_akhir' => 'nullable|numeric|between:-180,180',
            'hm_awal' => 'nullable',
            'hm_akhir' => 'nullable',
            'bbm_liter' => 'nullable|numeric|min:0',
            'kondisi_alat' => 'required|in:Normal,Perlu Perbaikan,Breakdown',
            'catatan' => 'nullable|string',
            'foto_sebelum' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'foto_sesudah' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // Jika operator mencoba menyelesaikan shift saat pembuatan (mengisi data akhir shift)
        if ($request->filled('hm_akhir') || $request->hasFile('foto_sesudah')) {
            $customErrors = [];
            if (!$request->filled('lokasi_blok')) {
                $customErrors['lokasi_blok'] = 'Lokasi / Blok Pekerjaan wajib diisi untuk menyelesaikan shift.';
            }
            if (!$request->filled('bbm_liter') && $request->bbm_liter !== '0') {
                $customErrors['bbm_liter'] = 'Pengisian BBM (Liter) wajib diisi untuk menyelesaikan shift.';
            }
            if (!$request->filled('hm_awal') && !$request->hasFile('foto_sebelum')) {
                $customErrors['hm_awal'] = 'HM / Jam Awal Kerja wajib diisi untuk menyelesaikan shift.';
            }
            if (!$request->hasFile('foto_sebelum')) {
                $customErrors['foto_sebelum'] = 'Foto Sebelum Kerja wajib diunggah untuk menyelesaikan shift.';
            }
            if (!$request->hasFile('foto_sesudah')) {
                $customErrors['foto_sesudah'] = 'Foto Sesudah Kerja wajib diunggah untuk menyelesaikan shift.';
            }
            if (!empty($customErrors)) {
                return redirect()->back()->withErrors($customErrors)->withInput();
            }
        }

        $idPks = $user->id_pks;
        $uploadDir = public_path('gallery');
        if (!File::exists($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }

        $fotoSebelumName = null;
        $fotoSebelumTs = null;
        if ($request->hasFile('foto_sebelum')) {
            $file = $request->file('foto_sebelum');
            $fotoSebelumName = 'sebelum_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $fotoSebelumName);
            $fotoSebelumTs = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        }

        $fotoSesudahName = null;
        $fotoSesudahTs = null;
        if ($request->hasFile('foto_sesudah')) {
            $file = $request->file('foto_sesudah');
            $fotoSesudahName = 'sesudah_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $fotoSesudahName);
            $fotoSesudahTs = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        }

        // Calculate Bed total
        $flatBed = (int) $request->input('flat_bed', 0);
        $longBed = (int) $request->input('long_bed', 0);
        $jumlahBed = $flatBed + $longBed;
        if ($jumlahBed === 0 && $request->filled('jumlah_bed')) {
            $jumlahBed = (int) $request->jumlah_bed;
        }

        // Prioritaskan input manual hm_awal (jika diisi), baru foto_sebelum
        if ($request->filled('hm_awal')) {
            $hmAwalTs = MonitoringAlatBerat::parseTimestamp($request->hm_awal, $request->tanggal);
        } elseif ($request->hasFile('foto_sebelum')) {
            $hmAwalTs = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        } else {
            $hmAwalTs = null;
        }

        // Prioritaskan input manual hm_akhir (jika diisi), baru foto_sesudah
        if ($request->filled('hm_akhir')) {
            $hmAkhirTs = MonitoringAlatBerat::parseTimestamp($request->hm_akhir, $request->tanggal);
        } elseif ($request->hasFile('foto_sesudah')) {
            $hmAkhirTs = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        } else {
            $hmAkhirTs = null;
        }

        $totalHm = 0.0;
        $valAwalStr = str_replace(',', '.', trim((string)$request->hm_awal));
        $valAkhirStr = str_replace(',', '.', trim((string)$request->hm_akhir));

        if ($request->filled('hm_awal') && $request->filled('hm_akhir') && is_numeric($valAwalStr) && is_numeric($valAkhirStr)) {
            $totalHm = round(max(0, (float)$valAkhirStr - (float)$valAwalStr), 2);
        } elseif ($hmAwalTs && $hmAkhirTs) {
            $cStart = Carbon::parse($hmAwalTs);
            $cEnd = Carbon::parse($hmAkhirTs);
            if ($cEnd->lessThan($cStart)) {
                $cEnd->addDay();
            }
            $diffMinutes = abs($cStart->diffInMinutes($cEnd));
            $totalHm = round($diffMinutes / 60, 2);
        }

        $latAwal = $request->latitude_awal ?? $request->latitude;
        $longAwal = $request->longitude_awal ?? $request->longitude;
        $latAkhir = $request->latitude_akhir;
        $longAkhir = $request->longitude_akhir;

        $log = MonitoringAlatBerat::create([
            'id_pks' => $idPks,
            'alat_berat_id' => $request->alat_berat_id,
            'tanggal' => $request->tanggal,
            'operator' => $request->operator,
            'kegiatan' => $request->kegiatan,
            'lokasi_blok' => $request->lokasi_blok,
            'no_bak' => $request->no_bak,
            'flat_bed' => $flatBed,
            'long_bed' => $longBed,
            'jumlah_bed' => $jumlahBed,
            'latitude' => $latAwal,
            'longitude' => $longAwal,
            'latitude_awal' => $latAwal,
            'longitude_awal' => $longAwal,
            'latitude_akhir' => $latAkhir,
            'longitude_akhir' => $longAkhir,
            'hm_awal' => $hmAwalTs,
            'hm_akhir' => $hmAkhirTs,
            'total_hm' => $totalHm,
            'bbm_liter' => $request->bbm_liter ?? 0,
            'kondisi_alat' => $request->kondisi_alat,
            'catatan' => $request->catatan,
            'foto_sebelum' => $fotoSebelumName,
            'foto_sesudah' => $fotoSesudahName,
        ]);

        // Auto update status alat berat if breakdown or needs maintenance
        $alatBerat = AlatBerat::find($request->alat_berat_id);
        if ($alatBerat) {
            if ($request->kondisi_alat === 'Breakdown') {
                $alatBerat->update(['status' => 'Breakdown']);
            } elseif ($request->kondisi_alat === 'Perlu Perbaikan') {
                $alatBerat->update(['status' => 'Maintenance']);
            }
        }

        return redirect()->route('operator.index')->with('success', 'Laporan kerja alat berat berhasil disimpan!');
    }

    /**
     * Display report detail
     */
    public function show($id)
    {
        $user = Auth::user();
        $log = MonitoringAlatBerat::with(['pks', 'alatBerat'])->findOrFail($id);

        if ($user->id_pks && $log->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        return view('operator.show', compact('log', 'user'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $user = Auth::user();
        $log = MonitoringAlatBerat::with(['pks', 'alatBerat'])->findOrFail($id);

        if ($user->id_pks && $log->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah laporan ini.');
        }

        if ($log->isCompleted()) {
            return redirect()->route('operator.show', $log->id)
                ->with('warning', 'Laporan pekerjaan ini telah diselesaikan dan tidak dapat diedit kembali.');
        }

        $alatBeratQuery = AlatBerat::query();
        if ($user->id_pks) {
            $alatBeratQuery->where('id_pks', $user->id_pks);
        }
        $alatBeratQuery->where(function($q) use ($log) {
            $q->whereIn('status', ['Operational', 'Standby']);
            if ($log->alat_berat_id) {
                $q->orWhere('id', $log->alat_berat_id);
            }
        });
        $alatBeratList = $alatBeratQuery->orderBy('kode_alat')->get();

        $pksProgress = $this->getPksProgress($log->id_pks ?? $user->id_pks);

        return view('operator.edit', compact('log', 'alatBeratList', 'user', 'pksProgress'));
    }

    /**
     * Update report
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $log = MonitoringAlatBerat::findOrFail($id);

        if ($user->id_pks && $log->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah laporan ini.');
        }

        if ($log->isCompleted()) {
            return redirect()->route('operator.show', $log->id)
                ->with('warning', 'Laporan pekerjaan ini telah diselesaikan dan tidak dapat diperbarui kembali.');
        }

        $request->validate([
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
            'lokasi_blok' => 'required|string|max:100',
            'no_bak' => 'nullable|string|max:100',
            'flat_bed' => 'nullable|integer|min:0',
            'long_bed' => 'nullable|integer|min:0',
            'jumlah_bed' => 'nullable|integer|min:0',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'latitude_awal' => 'nullable|numeric|between:-90,90',
            'longitude_awal' => 'nullable|numeric|between:-180,180',
            'latitude_akhir' => 'nullable|numeric|between:-90,90',
            'longitude_akhir' => 'nullable|numeric|between:-180,180',
            'hm_awal' => 'nullable',
            'hm_akhir' => 'nullable',
            'bbm_liter' => 'required|numeric|min:0',
            'kondisi_alat' => 'required|in:Normal,Perlu Perbaikan,Breakdown',
            'catatan' => 'nullable|string',
            'foto_sebelum' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'foto_sesudah' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'alat_berat_id.required' => 'Unit Alat Berat wajib dipilih.',
            'tanggal.required' => 'Tanggal laporan wajib diisi.',
            'operator.required' => 'Nama Operator wajib diisi.',
            'kegiatan.required' => 'Jenis Kegiatan / Pekerjaan wajib diisi.',
            'lokasi_blok.required' => 'Lokasi / Blok Pekerjaan wajib diisi untuk menyelesaikan shift.',
            'bbm_liter.required' => 'Pengisian BBM (Liter) wajib diisi (masukkan 0 jika tidak ada pengisian).',
            'kondisi_alat.required' => 'Kondisi Alat Berat wajib dipilih.',
        ]);

        // Verifikasi kelengkapan seluruh data shift sebelum diselesaikan
        $customErrors = [];
        $hasHmAwal = $request->filled('hm_awal') || !empty($log->hm_awal) || $request->hasFile('foto_sebelum');
        if (!$hasHmAwal) {
            $customErrors['hm_awal'] = 'HM / Jam Awal Kerja wajib diisi untuk menyelesaikan shift.';
        }

        $hasHmAkhir = $request->filled('hm_akhir') || !empty($log->hm_akhir) || $request->hasFile('foto_sesudah');
        if (!$hasHmAkhir) {
            $customErrors['hm_akhir'] = 'HM / Jam Akhir Kerja wajib diisi untuk menyelesaikan shift.';
        }

        $hasFotoSebelum = !empty($log->foto_sebelum) || $request->hasFile('foto_sebelum');
        if (!$hasFotoSebelum) {
            $customErrors['foto_sebelum'] = 'Foto Sebelum Kerja wajib diunggah untuk menyelesaikan shift.';
        }

        $hasFotoSesudah = !empty($log->foto_sesudah) || $request->hasFile('foto_sesudah');
        if (!$hasFotoSesudah) {
            $customErrors['foto_sesudah'] = 'Foto Sesudah Kerja wajib diunggah untuk menyelesaikan shift.';
        }

        if (!empty($customErrors)) {
            return redirect()->back()->withErrors($customErrors)->withInput();
        }

        $uploadDir = public_path('gallery');

        $fotoSebelumName = $log->foto_sebelum;
        $fotoSebelumTs = null;
        if (!empty($log->foto_sebelum)) {
            // Foto Sebelum telah terisi -> dikunci untuk mencegah manipulasi data
            $fotoSebelumName = $log->foto_sebelum;
        } elseif ($request->hasFile('foto_sebelum')) {
            $file = $request->file('foto_sebelum');
            $fotoSebelumName = 'sebelum_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $fotoSebelumName);
            $fotoSebelumTs = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        }

        $fotoSesudahName = $log->foto_sesudah;
        $fotoSesudahTs = null;
        if ($request->hasFile('foto_sesudah')) {
            if ($log->foto_sesudah && File::exists($uploadDir . '/' . $log->foto_sesudah)) {
                File::delete($uploadDir . '/' . $log->foto_sesudah);
            }
            $file = $request->file('foto_sesudah');
            $fotoSesudahName = 'sesudah_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $fotoSesudahName);
            $fotoSesudahTs = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        }

        // Bed calculation
        $flatBed = (int) $request->input('flat_bed', 0);
        $longBed = (int) $request->input('long_bed', 0);
        $jumlahBed = $flatBed + $longBed;
        if ($jumlahBed === 0 && $request->filled('jumlah_bed')) {
            $jumlahBed = (int) $request->jumlah_bed;
        }

        // HM calculation - prioritaskan input manual
        if ($request->filled('hm_awal')) {
            $hmAwalTs = MonitoringAlatBerat::parseTimestamp($request->hm_awal, $request->tanggal);
        } elseif ($request->hasFile('foto_sebelum')) {
            $hmAwalTs = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        } else {
            $hmAwalTs = $log->hm_awal;
        }

        if ($request->filled('hm_akhir')) {
            $hmAkhirTs = MonitoringAlatBerat::parseTimestamp($request->hm_akhir, $request->tanggal);
        } elseif ($request->hasFile('foto_sesudah')) {
            $hmAkhirTs = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        } else {
            $hmAkhirTs = $log->hm_akhir;
        }

        $totalHm = $log->total_hm;
        $valAwalStr = str_replace(',', '.', trim((string)$request->hm_awal));
        $valAkhirStr = str_replace(',', '.', trim((string)$request->hm_akhir));

        if ($request->filled('hm_awal') && $request->filled('hm_akhir') && is_numeric($valAwalStr) && is_numeric($valAkhirStr)) {
            $totalHm = round(max(0, (float)$valAkhirStr - (float)$valAwalStr), 2);
        } elseif ($hmAwalTs && $hmAkhirTs) {
            $cStart = Carbon::parse($hmAwalTs);
            $cEnd = Carbon::parse($hmAkhirTs);
            if ($cEnd->lessThan($cStart)) {
                $cEnd->addDay();
            }
            $diffMinutes = abs($cStart->diffInMinutes($cEnd));
            $totalHm = round($diffMinutes / 60, 2);
        }

        $latAwal = $request->latitude_awal ?? $log->latitude_awal ?? $request->latitude ?? $log->latitude;
        $longAwal = $request->longitude_awal ?? $log->longitude_awal ?? $request->longitude ?? $log->longitude;
        $latAkhir = $request->latitude_akhir ?? $log->latitude_akhir;
        $longAkhir = $request->longitude_akhir ?? $log->longitude_akhir;

        $log->update([
            'alat_berat_id' => $request->alat_berat_id,
            'tanggal' => $request->tanggal,
            'operator' => $request->operator,
            'kegiatan' => $request->kegiatan,
            'lokasi_blok' => $request->lokasi_blok,
            'no_bak' => $request->no_bak,
            'flat_bed' => $flatBed,
            'long_bed' => $longBed,
            'jumlah_bed' => $jumlahBed,
            'latitude' => $latAwal,
            'longitude' => $longAwal,
            'latitude_awal' => $latAwal,
            'longitude_awal' => $longAwal,
            'latitude_akhir' => $latAkhir,
            'longitude_akhir' => $longAkhir,
            'hm_awal' => $hmAwalTs,
            'hm_akhir' => $hmAkhirTs,
            'total_hm' => $totalHm,
            'bbm_liter' => $request->bbm_liter ?? $log->bbm_liter,
            'kondisi_alat' => $request->kondisi_alat,
            'catatan' => $request->catatan,
            'foto_sebelum' => $fotoSebelumName,
            'foto_sesudah' => $fotoSesudahName,
        ]);

        $alatBerat = AlatBerat::find($request->alat_berat_id);
        if ($alatBerat) {
            if ($request->kondisi_alat === 'Breakdown') {
                $alatBerat->update(['status' => 'Breakdown']);
            } elseif ($request->kondisi_alat === 'Perlu Perbaikan') {
                $alatBerat->update(['status' => 'Maintenance']);
            }
        }

        return redirect()->route('operator.index')->with('success', 'Laporan kerja berhasil diperbarui!');
    }

    /**
     * Heavy Equipment Management Index for Mandor / Unit
     */
    public function alatBeratIndex(Request $request)
    {
        $user = Auth::user();

        if (!$user->canManageAlatBerat()) {
            return redirect()->route('operator.index')->with('error', 'Akses Ditolak: Operator tidak memiliki hak akses untuk mengelola master & status alat berat. Fitur ini khusus untuk Mandor.');
        }

        $query = AlatBerat::query();

        if ($user->id_pks) {
            $query->where('id_pks', $user->id_pks);
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

        $alatBeratList = $query->orderBy('kode_alat')->get();

        return view('operator.alat_berat', compact('user', 'alatBeratList'));
    }

    /**
     * Store new Heavy Equipment
     */
    public function alatBeratStore(Request $request)
    {
        $user = Auth::user();

        if (!$user->canManageAlatBerat()) {
            return redirect()->route('operator.index')->with('error', 'Akses Ditolak: Operator tidak memiliki hak akses untuk menambahkan alat berat.');
        }

        $request->validate([
            'kode_alat' => 'required|string|max:30|unique:alat_berat,kode_alat',
            'nama_alat' => 'required|string|max:100',
            'jenis_alat' => 'required|in:Excavator,Wheel Loader,Bulldozer,Dump Truck,Compactor,Lainnya',
            'merk_tipe' => 'nullable|string|max:100',
            'tahun_pengadaan' => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
            'status' => 'required|in:Operational,Maintenance,Breakdown,Standby,Rolling',
            'keterangan' => 'nullable|string',
        ]);

        AlatBerat::create([
            'id_pks' => $user->id_pks,
            'kode_alat' => strtoupper($request->kode_alat),
            'nama_alat' => $request->nama_alat,
            'jenis_alat' => $request->jenis_alat,
            'merk_tipe' => $request->merk_tipe,
            'tahun_pengadaan' => $request->tahun_pengadaan,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('operator.alat-berat.index')->with('success', 'Unit alat berat baru berhasil ditambahkan!');
    }

    /**
     * Update Heavy Equipment details and status
     */
    public function alatBeratUpdate(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->canManageAlatBerat()) {
            return redirect()->route('operator.index')->with('error', 'Akses Ditolak: Operator tidak memiliki hak akses untuk mengubah data alat berat.');
        }

        $alatBerat = AlatBerat::findOrFail($id);

        if ($user->id_pks && $alatBerat->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah unit alat berat ini.');
        }

        $request->validate([
            'kode_alat' => 'required|string|max:30|unique:alat_berat,kode_alat,' . $id,
            'nama_alat' => 'required|string|max:100',
            'jenis_alat' => 'required|in:Excavator,Wheel Loader,Bulldozer,Dump Truck,Compactor,Lainnya',
            'merk_tipe' => 'nullable|string|max:100',
            'tahun_pengadaan' => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
            'status' => 'required|in:Operational,Maintenance,Breakdown,Standby,Rolling',
            'keterangan' => 'nullable|string',
        ]);

        $alatBerat->update([
            'kode_alat' => strtoupper($request->kode_alat),
            'nama_alat' => $request->nama_alat,
            'jenis_alat' => $request->jenis_alat,
            'merk_tipe' => $request->merk_tipe,
            'tahun_pengadaan' => $request->tahun_pengadaan,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('operator.alat-berat.index')->with('success', 'Data unit alat berat berhasil diperbarui!');
    }

    /**
     * Quick status update
     */
    public function alatBeratUpdateStatus(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->canManageAlatBerat()) {
            return redirect()->back()->with('error', 'Akses Ditolak: Operator tidak memiliki hak akses untuk mengubah status alat berat.');
        }

        $alatBerat = AlatBerat::findOrFail($id);

        if ($user->id_pks && $alatBerat->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status unit alat berat ini.');
        }

        $request->validate([
            'status' => 'required|in:Operational,Maintenance,Breakdown,Standby,Rolling',
        ]);

        $alatBerat->update(['status' => $request->status]);

        return redirect()->back()->with('success', "Status unit {$alatBerat->kode_alat} berhasil diubah menjadi {$request->status}!");
    }

    /**
     * Delete Heavy Equipment
     */
    public function alatBeratDestroy($id)
    {
        $user = Auth::user();

        if (!$user->canManageAlatBerat()) {
            return redirect()->route('operator.index')->with('error', 'Akses Ditolak: Operator tidak memiliki hak akses untuk menghapus alat berat.');
        }

        $alatBerat = AlatBerat::findOrFail($id);

        if ($user->id_pks && $alatBerat->id_pks != $user->id_pks) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus unit alat berat ini.');
        }

        $alatBerat->delete();

        return redirect()->route('operator.alat-berat.index')->with('success', 'Unit alat berat berhasil dihapus!');
    }
}
