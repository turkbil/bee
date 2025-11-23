<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CorporateService
{
    /**
     * Create a sub-user for corporate account
     */
    public function createSubUser(User $parent, array $data): User
    {
        if (!$parent->isCorporate()) {
            throw new \Exception('Bu hesap kurumsal hesap değil');
        }

        // Check max users limit
        $maxUsers = (int) setting('corporate_max_users', 0);
        if ($maxUsers > 0) {
            $currentCount = $parent->subUsers()->count();
            if ($currentCount >= $maxUsers) {
                throw new \Exception("Maksimum alt kullanıcı limitine ({$maxUsers}) ulaşıldı");
            }
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'] ?? Str::random(12)),
            'parent_user_id' => $parent->id,
            'is_active' => true,
            'is_approved' => true,
        ]);

        return $user;
    }

    /**
     * Send invite to email
     */
    public function sendInvite(User $parent, string $email): bool
    {
        if (!$parent->isCorporate()) {
            return false;
        }

        // Generate or get corporate code
        if (!$parent->corporate_code) {
            $parent->update([
                'corporate_code' => $this->generateCorporateCode($parent),
            ]);
        }

        // TODO: Send email with corporate code
        // Mail::to($email)->send(new CorporateInviteMail($parent));

        return true;
    }

    /**
     * Remove sub-user
     */
    public function removeSubUser(User $parent, User $subUser): bool
    {
        if ($subUser->parent_user_id !== $parent->id) {
            return false;
        }

        $subUser->update([
            'parent_user_id' => null,
            'is_active' => false,
        ]);

        return true;
    }

    /**
     * Get all sub-users for corporate account
     */
    public function getSubUsers(User $parent): \Illuminate\Database\Eloquent\Collection
    {
        return $parent->subUsers()->get();
    }

    /**
     * Register user with corporate code
     */
    public function registerWithCode(string $code, array $data): User
    {
        $parent = User::where('corporate_code', $code)
            ->where('is_corporate', true)
            ->first();

        if (!$parent) {
            throw new \Exception('Geçersiz kurumsal kod');
        }

        return $this->createSubUser($parent, $data);
    }

    /**
     * Generate unique corporate code
     */
    protected function generateCorporateCode(User $user): string
    {
        $prefix = Str::upper(Str::substr(Str::slug($user->name), 0, 6));
        $suffix = Str::upper(Str::random(6));

        return "{$prefix}-{$suffix}";
    }

    /**
     * Check if corporate features are enabled
     */
    public function isEnabled(): bool
    {
        return (bool) setting('corporate_enabled', false);
    }
}
