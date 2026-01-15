<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     *
     * Bu route auth middleware olmadan çalışır (signed URL yeterli)
     * Kullanıcı giriş yapmamış olsa bile email doğrulaması yapılabilir
     */
    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        // Kullanıcıyı ID ile bul
        $user = User::findOrFail($id);

        // Hash kontrolü (signed URL doğrulaması middleware'de yapılıyor)
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        // Email zaten doğrulanmışsa
        if ($user->hasVerifiedEmail()) {
            // Kullanıcı giriş yapmamışsa login yap
            if (!Auth::check()) {
                Auth::login($user);
            }

            return $this->redirectAfterVerification($user, $request);
        }

        // Email'i doğrula
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Kullanıcıyı otomatik login yap
        if (!Auth::check()) {
            Auth::login($user);

            // Session regenerate
            $request->session()->regenerate();

            \Log::info('🔐 EMAIL VERIFIED: User auto-logged in', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        return $this->redirectAfterVerification($user, $request);
    }

    /**
     * Redirect user after email verification based on tenant
     */
    protected function redirectAfterVerification(User $user, Request $request): RedirectResponse
    {
        // Cookie domain ayarı
        $cookieDomain = config('session.domain');

        // Redirect response oluştur
        $redirectUrl = '/';
        if (tenant() && tenant()->id === 1001) {
            // Muzibu için ana sayfaya yönlendir
            $redirectUrl = '/?verified=1';
        } else {
            // Diğer tenant'lar için dashboard
            $redirectUrl = route('dashboard', absolute: false).'?verified=1';
        }

        $response = redirect()->to($redirectUrl)
            ->with('status', 'Email adresiniz başarıyla doğrulandı!');

        // CSRF token cookie ekle (subdomain'lerde de geçerli)
        return $response->cookie('XSRF-TOKEN', csrf_token(), 60, '/', $cookieDomain, true, false, false, 'lax');
    }
}
