<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();

            // Kullanıcıyı al
            $user = Auth::user();

            // Oturum başlatıldıktan sonra `user_id` ekle
            $sessionId = session()->getId();

            DB::table('sessions')
            ->where('id', session()->getId())
            ->update([
                'last_activity' => now()->timestamp,
            ]);
        
        
            Log::info('Session updated with user_id', [
                'session_id' => $sessionId,
                'user_id'    => $user->id,
            ]);

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        } catch (\Exception $e) {
            Log::error('Authentication failed', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors([
                'email' => 'Giriş başarısız. Lütfen bilgilerinizi kontrol edin.',
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
