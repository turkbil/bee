<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
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
        ]);

        // Şifre sıfırlama bağlantısı gönder
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
    }

}
