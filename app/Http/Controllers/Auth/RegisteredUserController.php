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
        $viewPath = "themes.{$theme}.auth.register";

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

        return redirect(route('dashboard', absolute: false));
    }
}
