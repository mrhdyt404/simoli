<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IotApiAuth
{
    /**
     * Handle an incoming request for IoT API endpoints.
     * Enforces Authentication (User Token / API Key) and Authorization (PKS level access).
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authenticatedUser = null;
        $isApiKeyAuth = false;

        $configuredKey = config('mqtt.api_key') ?: env('IOT_API_KEY', 'simoli_iot_secret_galuh_2026_key');

        // 1. Check Web Session Auth (if accessed from internal web/blade dashboard with cookie)
        if (auth()->check()) {
            $authenticatedUser = auth()->user();
        }

        // 2. Check Custom Header: X-API-KEY / X-IOT-KEY / Authorization: ApiKey <key>
        $apiKey = $request->header('X-API-KEY') 
            ?? $request->header('X-IOT-KEY') 
            ?? $request->header('x-api-key')
            ?? $request->query('api_key');

        if ($apiKey && hash_equals($configuredKey, (string) $apiKey)) {
            $isApiKeyAuth = true;
        }

        // 3. Check Authorization Header (Bearer Token)
        $authHeader = $request->header('Authorization');
        if (!$isApiKeyAuth && $authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = trim(substr($authHeader, 7));

            // Check if Bearer matches the IoT API Key directly
            if (hash_equals($configuredKey, $token)) {
                $isApiKeyAuth = true;
            } else {
                // Check User base64 Token (ID:username)
                $decoded = base64_decode($token, true);
                if ($decoded !== false) {
                    $parts = explode(':', $decoded);
                    if (count($parts) >= 2) {
                        $user = User::with('pks')->find($parts[0]);
                        if ($user && $user->username === $parts[1]) {
                            $authenticatedUser = $user;
                        }
                    }
                }
            }
        }

        // --- AUTHENTICATION CHECK ---
        if (!$authenticatedUser && !$isApiKeyAuth) {
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'Unauthenticated: Akses ditolak. Anda belum terautentikasi. Silakan sertakan Authorization Bearer Token atau X-API-KEY yang valid.',
            ], 401);
        }

        // --- AUTHORIZATION CHECK ---
        if ($authenticatedUser) {
            $requestedPksId = $request->input('id_pks');

            // If user is not admin and requests a specific PKS outside their assigned unit
            if (!$authenticatedUser->isAdmin() && $authenticatedUser->id_pks && $requestedPksId) {
                if ((int) $requestedPksId !== (int) $authenticatedUser->id_pks) {
                    return response()->json([
                        'status' => 'error',
                        'code' => 403,
                        'message' => "Unauthorized / Forbidden: Akun Anda ({$authenticatedUser->username}) tidak memiliki hak otorisasi untuk mengakses data PKS ID {$requestedPksId}.",
                    ], 403);
                }
            }

            // Bind authenticated user to request attributes
            $request->attributes->set('auth_user', $authenticatedUser);
        }

        return $next($request);
    }
}
