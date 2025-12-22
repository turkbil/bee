<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Tema-aware register view
        $theme = app(\App\Services\ThemeService::class)->getActiveTheme();
        $themeName = $theme ? $theme->name : 'simple';
        $viewPath = "themes.{$themeName}.auth.register";

        // Fallback: Tema yoksa veya view yoksa default auth.register kullan
        if (!view()->exists($viewPath)) {
            $viewPath = 'auth.register';
        }

        return view($viewPath);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // ✅ Email doğrulaması için login yap (ama verified middleware ile bloklanacak)
        Auth::login($user);

        // Kayıt log'u
        activity()
            ->causedBy($user)
            ->inLog('User')
            ->withProperties(['baslik' => $user->name, 'modul' => 'User'])
            ->tap(function ($activity) {
                $activity->event = 'kayıt oldu';
            })
            ->log("\"{$user->name}\" kayıt oldu");

        // Trial subscription başlat (auth_subscription açıksa ve trial plan varsa)
        // NOT: Trial subscription email doğrulandıktan sonra aktif olacak
        if (setting('auth_subscription')) {
            $subscriptionService = app(\Modules\Subscription\App\Services\SubscriptionService::class);
            $subscriptionService->createTrialForUser($user);
        }

        // Email doğrulama sayfasına yönlendir
        return redirect()->route('verification.notice');
    }
}
