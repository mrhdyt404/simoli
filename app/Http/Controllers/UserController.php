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

        return view('pengguna.index', compact('users', 'totalUsers', 'totalAdmin', 'totalUnit'));
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
        $validated = $request->validate([
            'id_pks' => [
                'required',
                Rule::unique('user', 'id_pks')->where(function($q) use ($request) {
                    return $q->where('level_akses', 'unit');
                })
            ],
            'username' => [
                'required',
                Rule::unique('user', 'username')
            ],
            'password' => 'required',
            'level_akses' => [
                'required',
                Rule::in(['admin', 'unit', 'operator'])
            ],
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
        $validated = $request->validate([
            'id_pks' => [
                'required',
                Rule::unique('user', 'id_pks')->ignore($user->ID, 'ID')->where(function($q) use ($request) {
                    return $q->where('level_akses', 'unit');
                })
            ],
            'username' => [
                'required',
                Rule::unique('user', 'username')->ignore($user->ID, 'ID')
            ],
            'password' => 'required',
            'level_akses' => [
                'required',
                Rule::in(['admin', 'unit', 'operator'])
            ],
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