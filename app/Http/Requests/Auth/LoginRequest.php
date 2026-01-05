<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\User;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 🔍 LOGIN DEBUG: Login denemesini logla
        \Log::info('🔐 LOGIN ATTEMPT', [
            'email' => $this->email,
            'tenant_id' => tenant()->id ?? null,
            'ip' => $this->ip(),
        ]);

        // 🔐 MD5 LEGACY SUPPORT: SADECE Tenant 1001 (Muzibu) için - Eski siteden gelen kullanıcılar
        $user = User::where('email', $this->email)->first();

        // Önce standart bcrypt ile dene
        $attemptSuccess = Auth::attempt($this->only('email', 'password'), $this->boolean('remember'));

        // ⚠️ SADECE TENANT 1001 (Muzibu) için MD5 desteği
        if (!$attemptSuccess && $user && tenant() && tenant()->id === 1001) {
            // MD5 hash kontrolü (Muzibu eski sistem)
            // MD5 hash: 32 karakter hex string
            if (strlen($user->password) === 32 && ctype_xdigit($user->password)) {
                // MD5 hash ile kontrol et
                if (md5($this->password) === $user->password) {
                    // ✅ MD5 şifre match etti!
                    // Şimdi bcrypt'e güncelle ve login yap
                    $user->password = \Hash::make($this->password);
                    $user->save();

                    \Log::info('🔐 TENANT 1001: MD5 → BCrypt Migration', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'tenant_id' => tenant()->id,
                    ]);

                    // Manuel login
                    Auth::login($user, $this->boolean('remember'));
                    $attemptSuccess = true;
                }
            }
        }

        // Hala başarısız ise hata fırlat
        if (!$attemptSuccess) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Kullanıcı pasif ise giriş yapmasına izin verme
        if ($user && !$user->is_active) {
            Auth::logout();

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Bu hesap pasif durumda. Lütfen yönetici ile iletişime geçin.',
            ]);
        }

        // Email doğrulaması zorunlu ise ve doğrulanmamışsa giriş yapmasına izin verme
        if ($user && setting('auth_registration_email_verify') && !$user->hasVerifiedEmail()) {
            Auth::logout();

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'E-posta adresinizi doğrulamanız gerekmektedir. Lütfen e-posta kutunuzu kontrol edin.',
            ]);
        }

        // Son giriş zamanını güncelle
        if ($user) {
            $user->update([
                'last_login_at' => Carbon::now()
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}