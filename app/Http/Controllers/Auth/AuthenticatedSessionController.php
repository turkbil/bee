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

            // Tenant bilgisi alınıyor
            $tenant = tenancy()->tenant;
            $siteId = $tenant ? $tenant->tenant_id : null;

            if ($siteId) {
                // Oturum başlatıldıktan sonra `tenant_id` ve `user_id` ekle
                $sessionId = session()->getId();

                DB::table('sessions')
                    ->where('id', $sessionId)
                    ->update([
                        'tenant_id' => $siteId,
                        'user_id'   => $user->id, // `user_id`'yi ekliyoruz
                    ]);

                Log::info('Session updated with tenant_id and user_id', [
                    'session_id' => $sessionId,
                    'tenant_id'  => $siteId,
                    'user_id'    => $user->id,
                ]);
            } else {
                Log::warning('Tenant or tenant_id not found for session update.');
            }

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
