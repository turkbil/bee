<?php

namespace App\Services\Auth;

use App\Models\User;
use Modules\Muzibu\App\Models\MuzibuCorporateAccount;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * Corporate Service - Yeni parent_id mimarisi
 * Üyeler de muzibu_corporate_accounts tablosunda tutulur
 * users tablosu universal kalır
 */
class CorporateService
{
    /**
     * Kullanıcı için kurum hesabı oluştur
     */
    public function createAccount(User $user, ?string $companyName = null): MuzibuCorporateAccount
    {
        return MuzibuCorporateAccount::create([
            'user_id' => $user->id,
            'parent_id' => null, // Kurum sahibi
            'corporate_code' => MuzibuCorporateAccount::generateCode(),
            'company_name' => $companyName,
            'is_active' => true,
        ]);
    }

    /**
     * Kuruma üye ekle (yeni user oluştur + corporate_accounts kaydı)
     */
    public function createMember(MuzibuCorporateAccount $account, array $data): User
    {
        // User oluştur
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'] ?? Str::random(12)),
            'is_active' => true,
            'is_approved' => true,
        ]);

        // Corporate accounts'a üye kaydı oluştur
        MuzibuCorporateAccount::create([
            'user_id' => $user->id,
            'parent_id' => $account->id, // Kuruma bağla
            'corporate_code' => null,
            'company_name' => null,
            'is_active' => true,
        ]);

        return $user;
    }

    /**
     * Kullanıcının sahip olduğu kurumu getir
     */
    public function getOwnedAccount(User $user): ?MuzibuCorporateAccount
    {
        return MuzibuCorporateAccount::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->first();
    }

    /**
     * Kullanıcının üye olduğu kurumu getir
     */
    public function getMemberAccount(User $user): ?MuzibuCorporateAccount
    {
        $record = MuzibuCorporateAccount::where('user_id', $user->id)
            ->whereNotNull('parent_id')
            ->first();

        return $record ? $record->parent : null;
    }

    /**
     * Kullanıcı kurum sahibi mi?
     */
    public function isOwner(User $user): bool
    {
        return MuzibuCorporateAccount::isUserOwner($user->id);
    }

    /**
     * Kullanıcı kurumsal üye mi?
     */
    public function isMember(User $user): bool
    {
        return MuzibuCorporateAccount::isUserMember($user->id);
    }

    /**
     * Davet maili gönder
     */
    public function sendInvite(User $owner, string $email): bool
    {
        $account = $this->getOwnedAccount($owner);

        if (!$account || !$account->is_active) {
            return false;
        }

        // TODO: Mail gönder
        // Mail::to($email)->send(new CorporateInviteMail($account));

        return true;
    }

    /**
     * Üyeyi kurumdan çıkar
     */
    public function removeMember(MuzibuCorporateAccount $account, User $member): bool
    {
        $memberRecord = MuzibuCorporateAccount::where('user_id', $member->id)
            ->where('parent_id', $account->id)
            ->first();

        if (!$memberRecord) {
            return false;
        }

        $memberRecord->delete();
        return true;
    }

    /**
     * Kurumun tüm üyelerini getir
     */
    public function getMembers(MuzibuCorporateAccount $account): \Illuminate\Database\Eloquent\Collection
    {
        $memberRecords = $account->members;
        $userIds = $memberRecords->pluck('user_id');

        return User::whereIn('id', $userIds)->get();
    }

    /**
     * Davet koduyla kuruma katıl
     */
    public function joinWithCode(User $user, string $code): bool
    {
        // Zaten bir kuruma bağlı mı?
        if (MuzibuCorporateAccount::findByUser($user->id)) {
            return false;
        }

        $account = MuzibuCorporateAccount::where('corporate_code', $code)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->first();

        if (!$account) {
            return false;
        }

        // Üye kaydı oluştur
        MuzibuCorporateAccount::create([
            'user_id' => $user->id,
            'parent_id' => $account->id,
            'corporate_code' => null,
            'company_name' => null,
            'is_active' => true,
        ]);

        return true;
    }

    /**
     * Kurumsal kodla kayıt ol
     */
    public function registerWithCode(string $code, array $data): User
    {
        $account = MuzibuCorporateAccount::where('corporate_code', $code)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->first();

        if (!$account) {
            throw new \Exception('Geçersiz kurumsal kod');
        }

        return $this->createMember($account, $data);
    }

    /**
     * Kurumdan ayrıl
     */
    public function leave(User $user): bool
    {
        $record = MuzibuCorporateAccount::where('user_id', $user->id)
            ->whereNotNull('parent_id')
            ->first();

        if (!$record) {
            return false;
        }

        $record->delete();
        return true;
    }

    /**
     * Kullanıcının efektif aboneliğini getir
     * Üyeyse kurum sahibinin aboneliğini kullanır
     */
    public function getEffectiveSubscription(User $user)
    {
        if ($this->isMember($user)) {
            $corporate = MuzibuCorporateAccount::getCorporateForUser($user->id);
            if ($corporate && $corporate->owner) {
                return $corporate->owner->subscription ?? null;
            }
        }
        return $user->subscription ?? null;
    }

    /**
     * Davet kodunu yenile
     */
    public function regenerateCode(MuzibuCorporateAccount $account): string
    {
        $newCode = MuzibuCorporateAccount::generateCode();
        $account->update(['corporate_code' => $newCode]);
        return $newCode;
    }

    /**
     * Kurum hesabını deaktive et
     */
    public function deactivate(MuzibuCorporateAccount $account): bool
    {
        // Tüm üyeleri sil
        MuzibuCorporateAccount::where('parent_id', $account->id)->delete();

        $account->update(['is_active' => false]);
        return true;
    }

    /**
     * Kurumsal özellikler aktif mi?
     */
    public function isEnabled(): bool
    {
        return (bool) setting('corporate_enabled', false);
    }

    /**
     * Kullanıcının fatura adresini getir (cart_addresses'ten)
     * Üyeyse kurum sahibinin adresini döndürür
     */
    public function getBillingAddress(User $user)
    {
        $corporate = MuzibuCorporateAccount::getCorporateForUser($user->id);

        if (!$corporate) {
            // Kurumsal değilse kendi adresini döndür
            return $user->addresses()
                ->where('address_type', 'billing')
                ->orWhere('is_default_billing', true)
                ->first();
        }

        // Kurum sahibinin fatura adresini döndür
        return $corporate->owner->addresses()
            ->where('address_type', 'billing')
            ->orWhere('is_default_billing', true)
            ->first();
    }
}
