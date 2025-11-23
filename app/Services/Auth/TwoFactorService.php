<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class TwoFactorService
{
    /**
     * Send 2FA code to user
     */
    public function sendCode(User $user): bool
    {
        $code = $this->generateCode();
        $phone = $user->getTwoFactorPhone();

        if (!$phone) {
            return false;
        }

        // Store code in cache
        $expiryMinutes = (int) setting('auth_security_2fa_expiry', 5);
        Cache::put(
            $this->getCacheKey($user),
            $code,
            now()->addMinutes($expiryMinutes)
        );

        // Send SMS using tenant's SMS service
        // This would integrate with the tenant's SMS provider
        return $this->sendSms($phone, $code);
    }

    /**
     * Verify 2FA code
     */
    public function verifyCode(User $user, string $code): bool
    {
        $storedCode = Cache::get($this->getCacheKey($user));

        if ($storedCode && $storedCode === $code) {
            Cache::forget($this->getCacheKey($user));
            return true;
        }

        return false;
    }

    /**
     * Enable 2FA for user
     */
    public function enable(User $user, ?string $phone = null): bool
    {
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_phone' => $phone,
        ]);

        return true;
    }

    /**
     * Disable 2FA for user
     */
    public function disable(User $user): bool
    {
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_phone' => null,
        ]);

        return true;
    }

    /**
     * Generate random 6-digit code
     */
    protected function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get cache key for user
     */
    protected function getCacheKey(User $user): string
    {
        return "2fa_code_{$user->id}";
    }

    /**
     * Send SMS (placeholder - integrate with SMS provider)
     */
    protected function sendSms(string $phone, string $code): bool
    {
        // TODO: Integrate with tenant's SMS provider
        // Example: NetGSM, Iletimerkezi, etc.

        // For now, log the code (development only)
        logger()->info("2FA Code for {$phone}: {$code}");

        return true;
    }
}
