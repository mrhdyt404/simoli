<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::with('pks')->where('username', $request->username)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Username tidak ditemukan.',
            ], 404);
        }

        if ($user->password !== $request->password) {
            return response()->json([
                'success' => false,
                'message' => 'Password yang Anda masukkan salah.',
            ], 401);
        }

        $token = base64_encode($user->ID . ':' . $user->username);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->ID,
                'username' => $user->username,
                'level_akses' => $user->level_akses,
                'id_pks' => $user->id_pks,
                'nama_pks' => $user->pks ? $user->pks->nama : null,
            ]
        ]);
    }

    public function user(Request $request)
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $decoded = base64_decode($token);
        $parts = explode(':', $decoded);

        if (count($parts) < 2) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
        }

        $userId = $parts[0];
        $user = User::with('pks')->find($userId);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->ID,
                'username' => $user->username,
                'level_akses' => $user->level_akses,
                'id_pks' => $user->id_pks,
                'nama_pks' => $user->pks ? $user->pks->nama : null,
            ]
        ]);
    }
}
