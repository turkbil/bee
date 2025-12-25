<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckApproval
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Check if user is approved
        if (!$user->isApproved()) {
            Auth::logout();
            $request->session()->invalidate();
            // regenerateToken() KALDIRILDI - invalidate() zaten session'ı siliyor, gereksiz token yenileme

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Hesabınız henüz onaylanmamış. Lütfen admin onayını bekleyin.',
                    'error' => 'account_not_approved',
                ], 403);
            }

            return redirect()
                ->route('login')
                ->with('error', 'Hesabınız henüz onaylanmamış. Lütfen admin onayını bekleyin.');
        }

        // Check if account is locked
        if ($user->isLocked()) {
            Auth::logout();
            $request->session()->invalidate();
            // regenerateToken() KALDIRILDI - invalidate() zaten session'ı siliyor, gereksiz token yenileme

            $lockedUntil = $user->locked_until->format('H:i');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Hesabınız geçici olarak kilitlendi. Saat {$lockedUntil}'e kadar bekleyin.",
                    'error' => 'account_locked',
                    'locked_until' => $user->locked_until,
                ], 403);
            }

            return redirect()
                ->route('login')
                ->with('error', "Hesabınız geçici olarak kilitlendi. Saat {$lockedUntil}'e kadar bekleyin.");
        }

        return $next($request);
    }
}
