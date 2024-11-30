<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Mevcut tenant bilgisi alınır
        $tenant = tenancy()->tenant;
        $siteId = $tenant->tenant_id;

        // Verileri doğrula
        $request->validate([
            'token' => ['required'],
            'email' => [
                'required',
                'email',
                // Email ve tenant_id kombinasyonunu kontrol et
                function ($attribute, $value, $fail) use ($siteId) {
                    $exists = \App\Models\User::where('email', $value)
                        ->where('tenant_id', $siteId)
                        ->exists();

                    if (! $exists) {
                        $fail('Bu e-posta adresi bu alias ile ilişkili bir kullanıcıya ait değil.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Şifre sıfırlama işlemini gerçekleştir
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))
                    ->withErrors(['email' => [__($status)]]);
    }
}
