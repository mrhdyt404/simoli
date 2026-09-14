<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Pks;

class UserController extends Controller
{
    private function requireAdmin(): void
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403, 'Hanya admin yang dapat mengakses data pengguna.');
        }
    }

    public function index(Request $request)
    {
        $this->requireAdmin();

        $query = User::with('pks');

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($builder) use ($keyword) {

                $builder->where('username', 'like', "%{$keyword}%")
                    ->orWhereHas('pks', function ($q) use ($keyword) {

                        $q->where('NAMA', 'like', "%{$keyword}%")
                            ->orWhere('KODE', 'like', "%{$keyword}%")
                            ->orWhere('AKRO', 'like', "%{$keyword}%");

                    });

            });
        }

        if ($request->filled('level_akses')) {
            $query->where('level_akses', $request->level_akses);
        }

        $users = $query
            ->join('pks', 'user.id_pks', '=', 'pks.id_pks')
            ->select('user.*')
            ->orderBy('user.level_akses')
            ->orderBy('pks.NAMA')
            ->paginate(15)
            ->withQueryString();

        $totalUsers = User::count();
        $totalAdmin = User::where('level_akses', 'admin')->count();
        $totalUnit = User::where('level_akses', 'unit')->count();
        $totalMandor = User::where('level_akses', 'mandor')->count();
        $totalOperator = User::where('level_akses', 'operator')->count();

        $pksList = Pks::orderBy('id_pks')->get();

        return view('pengguna.index', compact('users', 'totalUsers', 'totalAdmin', 'totalUnit', 'totalMandor', 'totalOperator', 'pksList'));
    }

    public function updatePksAsisten(Request $request, $id)
    {
        $this->requireAdmin();

        $pks = Pks::findOrFail($id);

        $validated = $request->validate([
            'asisten' => 'nullable|string|max:100',
            'wa_asisten' => 'nullable|string|max:30',
        ]);

        $pks->update([
            'asisten' => $validated['asisten'] ?? null,
            'wa_asisten' => $validated['wa_asisten'] ?? null,
        ]);

        return redirect()->back()->with('success', "Kontak Asisten & Nomor WhatsApp untuk {$pks->nama} berhasil diperbarui.");
    }

    public function testPksWa(Request $request, $id, \App\Services\SidobeWaService $waService)
    {
        $this->requireAdmin();

        $pks = Pks::findOrFail($id);

        if (empty($pks->wa_asisten)) {
            return redirect()->back()->with('error', "Nomor WhatsApp Asisten untuk {$pks->nama} belum diisi.");
        }

        $now = now()->format('d-m-Y H:i:s');
        $namaAsisten = $pks->asisten ?: 'Bapak/Ibu Asisten';
        $message = "🔔 *TES NOTIFIKASI SIMOLI — PTPN IV*\n"
            . "-------------------------------------------\n"
            . "Halo {$namaAsisten},\n"
            . "Ini adalah pesan verifikasi koneksi gateway Sidobe WhatsApp untuk Unit *{$pks->nama}*.\n\n"
            . "✅ *Status Gateway:* Terhubung & Aktif\n"
            . "⏰ *Waktu:* {$now} WIB\n\n"
            . "_Sistem SIMOLI siap mengirimkan notifikasi monitoring alat berat & pengingat harian ke nomor ini._";

        $result = $waService->sendMessage($pks->wa_asisten, $message);

        if ($result['success']) {
            return redirect()->back()->with('success', "Pesan tes WhatsApp berhasil dikirim ke {$pks->wa_asisten} ({$pks->nama}).");
        } else {
            $errDetail = $result['message'] ?? ($result['error'] ?? 'Respon API gagal');
            return redirect()->back()->with('error', "Gagal mengirim pesan tes WA ke {$pks->wa_asisten}: {$errDetail}");
        }
    }

    public function create()
    {
        $this->requireAdmin();

        $pks = Pks::orderBy('nama')->get();

        return view('pengguna.create', compact('pks'));
        
    }
    

    public function store(Request $request)
    {
        $this->requireAdmin();

        $idPksRules = ['required'];
        if ($request->level_akses === 'unit') {
            $idPksRules[] = Rule::unique('user', 'id_pks')->where(function($q) {
                return $q->where('level_akses', 'unit');
            });
        }

        $validated = $request->validate([
            'id_pks' => $idPksRules,
            'username' => [
                'required',
                Rule::unique('user', 'username')
            ],
            'password' => 'required',
            'level_akses' => [
                'required',
                Rule::in(['admin', 'unit', 'mandor', 'operator'])
            ],
        ], [
            'id_pks.required' => 'PKS Unit wajib dipilih.',
            'id_pks.unique' => 'PKS ini sudah memiliki akun Unit. Hanya boleh ada 1 akun Unit per PKS.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan, silakan gunakan username lain.',
            'password.required' => 'Password wajib diisi.',
            'level_akses.required' => 'Level Akses wajib dipilih.',
        ]);

        User::create($validated);

        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $this->requireAdmin();

        $user = User::findOrFail($id);
        $pks = Pks::orderBy('NAMA')->get();

        return view('pengguna.edit', compact('user', 'pks'));
    }

    public function update(Request $request, $id)
    {
        $this->requireAdmin();
        $user = User::findOrFail($id);

        $idPksRules = ['required'];
        if ($request->level_akses === 'unit') {
            $idPksRules[] = Rule::unique('user', 'id_pks')->ignore($user->ID, 'ID')->where(function($q) {
                return $q->where('level_akses', 'unit');
            });
        }

        $validated = $request->validate([
            'id_pks' => $idPksRules,
            'username' => [
                'required',
                Rule::unique('user', 'username')->ignore($user->ID, 'ID')
            ],
            'password' => 'required',
            'level_akses' => [
                'required',
                Rule::in(['admin', 'unit', 'mandor', 'operator'])
            ],
        ], [
            'id_pks.required' => 'PKS Unit wajib dipilih.',
            'id_pks.unique' => 'PKS ini sudah memiliki akun Unit. Hanya boleh ada 1 akun Unit per PKS.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan, silakan gunakan username lain.',
            'password.required' => 'Password wajib diisi.',
            'level_akses.required' => 'Level Akses wajib dipilih.',
        ]);

        $user->update($validated);

        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->requireAdmin();

        $user = User::findOrFail($id);

        if (Auth::id() === $user->ID) {
            return redirect()->route('pengguna.index')->with('error', 'Akun yang sedang digunakan tidak bisa dihapus.');
        }

        $user->delete();

        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil dihapus.');
    }
}