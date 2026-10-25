<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    /**
     * Handle an incoming request for protected SIMOLI API endpoints.
     * Enforces authentication via Bearer Token (base64 ID:username) or Web Session.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = null;

        // 1. Check Bearer Token in Authorization header
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = trim(substr($authHeader, 7));
            $decoded = base64_decode($token, true);
            if ($decoded !== false) {
                $parts = explode(':', $decoded);
                if (count($parts) >= 2) {
                    $foundUser = User::with('pks')->find($parts[0]);
                    if ($foundUser && $foundUser->username === $parts[1]) {
                        $user = $foundUser;
                    }
                }
            }
        }

        // 2. Fallback to Web Session Auth (if accessed from internal web/browser with active session)
        if (!$user) {
            $user = Auth::user() ?? Auth::guard('web')->user();
        }

        // 3. Fallback to query parameter token (for test/debugging tools)
        if (!$user && $request->has('token')) {
            $decoded = base64_decode($request->query('token'), true);
            if ($decoded !== false) {
                $parts = explode(':', $decoded);
                if (count($parts) >= 2) {
                    $foundUser = User::with('pks')->find($parts[0]);
                    if ($foundUser && $foundUser->username === $parts[1]) {
                        $user = $foundUser;
                    }
                }
            }
        }

        // 4. Reject if not authenticated
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Silakan sertakan token otentikasi yang valid atau login terlebih dahulu.',
            ], 401);
        }

        // 5. Set authenticated user into Laravel Auth and Request instance
        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
