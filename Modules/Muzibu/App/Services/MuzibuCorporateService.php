<?php

namespace Modules\Muzibu\App\Services;

use App\Models\User;
use Modules\Muzibu\App\Models\MuzibuCorporateAccount;

/**
 * Corporate Account Service - Only for Muzibu
 * Handles all corporate membership logic
 */
class MuzibuCorporateService
{
    /**
     * Create corporate account for user
     */
    public function createAccount(User $user, ?string $companyName = null, int $maxMembers = 5): MuzibuCorporateAccount
    {
        return MuzibuCorporateAccount::create([
            'user_id' => $user->id,
            'corporate_code' => MuzibuCorporateAccount::generateCode(),
            'company_name' => $companyName,
            'max_members' => $maxMembers,
            'is_active' => true,
        ]);
    }

    /**
     * Get user's corporate account (owned)
     */
    public function getOwnedAccount(User $user): ?MuzibuCorporateAccount
    {
        return MuzibuCorporateAccount::where('user_id', $user->id)->first();
    }

    /**
     * Get corporate account user belongs to
     */
    public function getMemberAccount(User $user): ?MuzibuCorporateAccount
    {
        if (!$user->corporate_account_id) {
            return null;
        }
        return MuzibuCorporateAccount::find($user->corporate_account_id);
    }

    /**
     * Check if user is corporate owner
     */
    public function isOwner(User $user): bool
    {
        return MuzibuCorporateAccount::where('user_id', $user->id)->exists();
    }

    /**
     * Check if user is corporate member
     */
    public function isMember(User $user): bool
    {
        return $user->corporate_account_id !== null;
    }

    /**
     * Join corporate with invite code
     */
    public function joinWithCode(User $user, string $code): bool
    {
        $account = MuzibuCorporateAccount::where('corporate_code', $code)
            ->where('is_active', true)
            ->first();

        if (!$account || !$account->canAddMember()) {
            return false;
        }

        $user->update(['corporate_account_id' => $account->id]);
        return true;
    }

    /**
     * Remove user from corporate
     */
    public function leave(User $user): bool
    {
        if (!$user->corporate_account_id) {
            return false;
        }

        $user->update(['corporate_account_id' => null]);
        return true;
    }

    /**
     * Remove member from corporate (by owner)
     */
    public function removeMember(MuzibuCorporateAccount $account, User $member): bool
    {
        if ($member->corporate_account_id !== $account->id) {
            return false;
        }

        $member->update(['corporate_account_id' => null]);
        return true;
    }

    /**
     * Get effective subscription for user
     * If member of corporate, returns owner's subscription
     */
    public function getEffectiveSubscription(User $user)
    {
        if ($this->isMember($user)) {
            $account = $this->getMemberAccount($user);
            if ($account && $account->owner) {
                return $account->owner->subscription ?? null;
            }
        }
        return $user->subscription ?? null;
    }

    /**
     * Get all members of corporate account
     */
    public function getMembers(MuzibuCorporateAccount $account)
    {
        return User::where('corporate_account_id', $account->id)->get();
    }

    /**
     * Regenerate invite code
     */
    public function regenerateCode(MuzibuCorporateAccount $account): string
    {
        $newCode = MuzibuCorporateAccount::generateCode();
        $account->update(['corporate_code' => $newCode]);
        return $newCode;
    }

    /**
     * Deactivate corporate account
     */
    public function deactivate(MuzibuCorporateAccount $account): bool
    {
        // Remove all members first
        User::where('corporate_account_id', $account->id)
            ->update(['corporate_account_id' => null]);

        $account->update(['is_active' => false]);
        return true;
    }
}
