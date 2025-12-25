<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectAfterVerification($request);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->redirectAfterVerification($request);
    }

    /**
     * Redirect user after email verification based on tenant
     */
    protected function redirectAfterVerification(EmailVerificationRequest $request): RedirectResponse
    {
        // Muzibu için ana sayfaya yönlendir
        if (tenant() && tenant()->id === 1001) {
            return redirect()->to('/?verified=1')->with('status', 'Email adresiniz başarıyla doğrulandı!');
        }

        // Diğer tenant'lar için dashboard
        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
