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

        // Filter by Kesesuaian Izin
        if ($request->filled('kesesuaian_izin')) {
            $query->where('kesesuaian_izin', $request->kesesuaian_izin);
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
        if ($request->filled('kesesuaian_izin')) {
            $statsQuery->where('kesesuaian_izin', $request->kesesuaian_izin);
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

    public function getPksProgress($idPks): object
    {
        $pks = Pks::find($idPks);
        $akro = strtoupper($pks->akro ?? '');
        $tahun = (int) date('Y');
        $bulan = (int) date('n');

        $rencana = \App\Models\Rencana::where('id_pks', $idPks)->where('tahun', $tahun)->first();
        if ($rencana && ($rencana->flat_bed > 0 || $rencana->long_bed > 0)) {
            $totalBed = $rencana->flat_bed ?: ($rencana->flat_bed + $rencana->long_bed);
        } else {
            $totalBed = $this->defaultTotalBeds[$akro] ?? 0;
        }

        $startOfMonth = \Carbon\Carbon::now('Asia/Jakarta')->startOfMonth();
        $endOfMonth = \Carbon\Carbon::now('Asia/Jakarta')->endOfMonth();
        $totalDaysInMonth = $endOfMonth->day;
        $todayDay = (int) date('j');

        if ($todayDay <= 7) {
            $startOfWeek = \Carbon\Carbon::now('Asia/Jakarta')->startOfMonth();
            $endOfWeek = \Carbon\Carbon::createFromDate($tahun, $bulan, min(7, $totalDaysInMonth))->endOfDay();
        } elseif ($todayDay <= 14) {
            $startOfWeek = \Carbon\Carbon::createFromDate($tahun, $bulan, 8)->startOfDay();
            $endOfWeek = \Carbon\Carbon::createFromDate($tahun, $bulan, min(14, $totalDaysInMonth))->endOfDay();
        } elseif ($todayDay <= 21) {
            $startOfWeek = \Carbon\Carbon::createFromDate($tahun, $bulan, 15)->startOfDay();
            $endOfWeek = \Carbon\Carbon::createFromDate($tahun, $bulan, min(21, $totalDaysInMonth))->endOfDay();
        } elseif ($todayDay <= 28) {
            $startOfWeek = \Carbon\Carbon::createFromDate($tahun, $bulan, 22)->startOfDay();
            $endOfWeek = \Carbon\Carbon::createFromDate($tahun, $bulan, min(28, $totalDaysInMonth))->endOfDay();
        } else {
            $startOfWeek = \Carbon\Carbon::createFromDate($tahun, $bulan, 29)->startOfDay();
            $endOfWeek = \Carbon\Carbon::createFromDate($tahun, $bulan, $totalDaysInMonth)->endOfDay();
        }

        $monthRecords = Pengaliran::where('id_pks', $idPks)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $weekRecords = $monthRecords->filter(fn($item) => \Carbon\Carbon::parse($item->tanggal)->betweenIncluded($startOfWeek, $endOfWeek));

        $formatList = function($items, $field) {
            $collected = [];
            foreach ($items as $item) {
                $val = trim((string)($item->{$field} ?? ''));
                if ($val !== '' && $val !== '-') {
                    $parts = preg_split('/[\/,\s]+/', $val, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($parts as $p) {
                        $p = trim($p);
                        if ($p !== '' && $p !== '-' && !in_array($p, $collected)) $collected[] = $p;
                    }
                }
            }
            return empty($collected) ? '-' : implode(', ', $collected);
        };

        return (object) [
            'pks' => $pks,
            'akro' => $akro,
            'nama' => $pks->nama ?? 'Unit PKS',
            'total_bed' => $totalBed,
            'minggu_ini' => (object) [
                'bed_dialirkan' => (int) $this->numericSum($weekRecords, 'flat_bed'),
                'blok' => $formatList($weekRecords, 'blok'),
                'bak' => $formatList($weekRecords, 'no_bak'),
            ],
            'sd_bulan_ini' => (object) [
                'bed_dialirkan' => (int) $this->numericSum($monthRecords, 'flat_bed'),
                'blok' => $formatList($monthRecords, 'blok'),
                'bak' => $formatList($monthRecords, 'no_bak'),
            ],
            'keterangan' => 'Pengaliran Limbah lancar',
        ];
    }

    /**
     * Map master peta blok LA berizin per PKS untuk kebutuhan frontend validasi
     */
    public function getPksBlokMap(): array
    {
        return Pks::with(['petaBlokLa' => function($q) {
            $q->where('status_aktif', true)->orderBy('nama_blok');
        }, 'perizinanLa'])->get()->mapWithKeys(function($pks) {
            $izin = $pks->perizinanLa;
            return [$pks->id_pks => [
                'nomor_sk' => $izin ? $izin->nomor_sk : null,
                'status_izin' => $izin ? $izin->status_izin : 'Belum Ada SK',
                'bloks' => $pks->petaBlokLa->map(function($b) {
                    return [
                        'nama_blok' => $b->nama_blok,
                        'clean_blok' => strtoupper(preg_replace('/[^A-Z0-9]/', '', $b->nama_blok)),
                        'afdeling' => $b->afdeling,
                        'no_bak_awal' => $b->no_bak_awal,
                        'no_bak_akhir' => $b->no_bak_akhir,
                        'jumlah_bak' => $b->jumlah_bak,
                        'luas_ha' => $b->luas_ha,
                        'flat_bed' => $b->jumlah_flat_bed,
                    ];
                })->values()->all(),
            ]];
        })->toArray();
    }

    /**
     * Evaluasi apakah blok dan bak berada dalam izin Land Application PKS
     */
    public function evaluasiKesesuaianIzin($idPks, $inputBlok, $inputBak): array
    {
        $idPks = (int) $idPks;
        $activeBloks = \App\Models\PetaBlokLa::where('id_pks', $idPks)
            ->where('status_aktif', true)
            ->get();

        // Jika PKS belum memiliki konfigurasi master peta blok, default sesuai izin
        if ($activeBloks->isEmpty()) {
            return [
                'sesuai' => true,
                'pesan' => 'PKS belum memiliki master blok LA resmi di sistem.',
                'matched_bloks' => [],
            ];
        }

        // 1. Ekstrak nama blok input (pisahkan koma, slash, spasi, kata 'dan', '&')
        $rawBlokTokens = preg_split('/[\/,\s;&+]+|(?:\bdan\b)/i', (string) $inputBlok, -1, PREG_SPLIT_NO_EMPTY);
        $cleanBlokInputList = [];
        foreach ($rawBlokTokens as $token) {
            $cleaned = strtoupper(preg_replace('/^(BLOK|BLOCK|AFD\.?|AFDELING)\s*/i', '', trim($token)));
            $cleaned = preg_replace('/[^A-Z0-9]/', '', $cleaned);
            if ($cleaned !== '' && !in_array($cleaned, $cleanBlokInputList)) {
                $cleanBlokInputList[] = $cleaned;
            }
        }

        if (empty($cleanBlokInputList)) {
            return [
                'sesuai' => false,
                'pesan' => 'Nama blok pengaliran tidak terisi atau format tidak dikenali.',
                'matched_bloks' => [],
            ];
        }

        // 2. Ekstrak nomor bak
        preg_match_all('/\d+/', (string) $inputBak, $bakMatches);
        $inputBakNumbers = array_map('intval', $bakMatches[0] ?? []);

        // 3. Cocokkan blok input dengan peta_blok_la
        $matchedBlokModels = [];
        $unmatchedBloks = [];

        foreach ($cleanBlokInputList as $inputBlokItem) {
            $found = null;
            foreach ($activeBloks as $blokModel) {
                $dbNamaBlok = strtoupper(preg_replace('/[^A-Z0-9]/', '', $blokModel->nama_blok));
                if ($dbNamaBlok === $inputBlokItem) {
                    $found = $blokModel;
                    break;
                }
            }

            if ($found) {
                $matchedBlokModels[] = $found;
            } else {
                $unmatchedBloks[] = $inputBlokItem;
            }
        }

        // Jika ada blok yang tidak terdaftar
        if (!empty($unmatchedBloks)) {
            return [
                'sesuai' => false,
                'pesan' => 'Blok [' . implode(', ', $unmatchedBloks) . '] tidak tercantum dalam Surat Izin / Peta Land Application.',
                'matched_bloks' => $matchedBlokModels,
                'unmatched_bloks' => $unmatchedBloks,
            ];
        }

        // 4. Validasi nomor bak terhadap rentang no_bak_awal & no_bak_akhir
        if (!empty($inputBakNumbers)) {
            $invalidBaks = [];
            foreach ($inputBakNumbers as $bakNo) {
                $bakValid = false;
                foreach ($matchedBlokModels as $bModel) {
                    $minBak = $bModel->no_bak_awal !== null ? (int) $bModel->no_bak_awal : null;
                    $maxBak = $bModel->no_bak_akhir !== null ? (int) $bModel->no_bak_akhir : null;

                    if ($minBak !== null && $maxBak !== null) {
                        if ($bakNo >= min($minBak, $maxBak) && $bakNo <= max($minBak, $maxBak)) {
                            $bakValid = true;
                            break;
                        }
                    } elseif ($minBak !== null) {
                        if ($bakNo == $minBak) {
                            $bakValid = true;
                            break;
                        }
                    } else {
                        $bakValid = true;
                        break;
                    }
                }

                if (!$bakValid) {
                    $invalidBaks[] = $bakNo;
                }
            }

            if (!empty($invalidBaks)) {
                return [
                    'sesuai' => false,
                    'pesan' => 'Bak No. [' . implode(', ', $invalidBaks) . '] berada di luar rentang bak resmi blok yang dipilih.',
                    'matched_bloks' => $matchedBlokModels,
                    'invalid_baks' => $invalidBaks,
                ];
            }
        }

        return [
            'sesuai' => true,
            'pesan' => 'Sesuai dengan Surat Izin & Peta Land Application',
            'matched_bloks' => $matchedBlokModels,
        ];
    }

    public function create()
    {
        $user = Auth::user();
        $pksList = Pks::orderBy('NAMA')->get();
        $defaultPksId = $user->id_pks ?: ($pksList->first() ? $pksList->first()->id_pks : 1);
        $pksProgress = $this->getPksProgress($defaultPksId);
        $pksBlokMap = $this->getPksBlokMap();

        return view('pengaliran.create', compact('pksList', 'user', 'pksProgress', 'pksBlokMap'));
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
                'alasan_tidak_sesuai_izin' => 'nullable|string',
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

        // Pengecekan kesesuaian izin Land Application
        $evaluasi = $this->evaluasiKesesuaianIzin($validated['id_pks'], $validated['blok'], $validated['no_bak']);
        if (!$evaluasi['sesuai']) {
            $alasan = trim((string) $request->input('alasan_tidak_sesuai_izin', ''));
            if ($alasan === '') {
                return back()->withErrors([
                    'alasan_tidak_sesuai_izin' => '⚠️ ' . $evaluasi['pesan'] . ' Anda wajib memberikan alasan kenapa pengaliran dilakukan di luar surat izin Land Application.'
                ])->withInput();
            }
            $validated['kesesuaian_izin'] = 'Di Luar Izin';
            $validated['alasan_tidak_sesuai_izin'] = $alasan;
        } else {
            $validated['kesesuaian_izin'] = 'Sesuai Izin';
            $validated['alasan_tidak_sesuai_izin'] = null;
        }

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('gallery'), $filename);
            $validated['foto'] = $filename;
        }

        Pengaliran::create($validated);

        return redirect()->route('pengaliran.index')
            ->with('success', 'Data pengaliran berhasil ditambahkan! (Status: ' . $validated['kesesuaian_izin'] . ')');
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
        $pksProgress = $this->getPksProgress($pengaliran->id_pks);
        $pksBlokMap = $this->getPksBlokMap();

        return view('pengaliran.edit', compact('pengaliran', 'pksList', 'user', 'pksProgress', 'pksBlokMap'));
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
                'alasan_tidak_sesuai_izin' => 'nullable|string',
            ],
            [
                'foto.image' => 'File yang diunggah harus berupa gambar.',
                'foto.mimes' => 'Foto dokumentasi harus berformat JPG, JPEG, atau PNG.',
                'foto.max' => 'Ukuran foto maksimal 2 MB.',
            ]
        );

        // Unit user: otomatis set id_pks ke ID user sendiri
        if ($user->isUnit()) {
            $validated['id_pks'] = $user->id_pks;
        }

        // Pengecekan kesesuaian izin Land Application
        $evaluasi = $this->evaluasiKesesuaianIzin($validated['id_pks'], $validated['blok'], $validated['no_bak']);
        if (!$evaluasi['sesuai']) {
            $alasan = trim((string) $request->input('alasan_tidak_sesuai_izin', ''));
            if ($alasan === '') {
                return back()->withErrors([
                    'alasan_tidak_sesuai_izin' => '⚠️ ' . $evaluasi['pesan'] . ' Anda wajib memberikan alasan kenapa pengaliran dilakukan di luar surat izin Land Application.'
                ])->withInput();
            }
            $validated['kesesuaian_izin'] = 'Di Luar Izin';
            $validated['alasan_tidak_sesuai_izin'] = $alasan;
        } else {
            $validated['kesesuaian_izin'] = 'Sesuai Izin';
            $validated['alasan_tidak_sesuai_izin'] = null;
        }

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('gallery'), $filename);
            $validated['foto'] = $filename;
        }

        $pengaliran->update($validated);

        return redirect()->route('pengaliran.index')
            ->with('success', 'Data pengaliran berhasil diperbarui! (Status: ' . $validated['kesesuaian_izin'] . ')');
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
