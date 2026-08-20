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

    public function create()
    {
        $user = Auth::user();
        $pksList = Pks::orderBy('NAMA')->get();
        $defaultPksId = $user->id_pks ?: ($pksList->first() ? $pksList->first()->id_pks : 1);
        $pksProgress = $this->getPksProgress($defaultPksId);

        return view('pengaliran.create', compact('pksList', 'user', 'pksProgress'));
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
        $pksProgress = $this->getPksProgress($pengaliran->id_pks);

        return view('pengaliran.edit', compact('pengaliran', 'pksList', 'user', 'pksProgress'));
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
