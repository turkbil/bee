<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;

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
        $request->authenticate();

        $request->session()->regenerate();
        
        // Son giriş zamanını güncelle
        $user = Auth::user();
        if ($user) {
            $user->last_login_at = Carbon::now();
            $user->save();
            
            // Giriş log'u
            activity()
                ->causedBy($user)
                ->inLog('User')
                ->withProperties(['baslik' => $user->name, 'modul' => 'User'])
                ->tap(function ($activity) {
                    $activity->event = 'giriş yaptı';
                })
                ->log("\"{$user->name}\" giriş yaptı");
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        
        // Çıkış log'u
        if ($user) {
            activity()
                ->causedBy($user)
                ->inLog('User')
                ->withProperties(['baslik' => $user->name, 'modul' => 'User'])
                ->tap(function ($activity) {
                    $activity->event = 'çıkış yaptı';
                })
                ->log("\"{$user->name}\" çıkış yaptı");
        }

        return redirect('/');
    }
}