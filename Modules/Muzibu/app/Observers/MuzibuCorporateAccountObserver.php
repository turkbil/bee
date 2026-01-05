<?php

namespace Modules\Muzibu\App\Observers;

use Modules\Muzibu\App\Models\MuzibuCorporateAccount;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * MuzibuCorporateAccount Model Observer
 *
 * Kurumsal hesap lifecycle event'lerini yönetir.
 * Activity logging ve cache temizleme işlemlerini otomatikleştirir.
 *
 * Log edilecek işlemler:
 * - Kurumsal hesap oluşturma (ana şube)
 * - Kurumsal hesaba katılma (alt şube)
 * - Kurumsal hesaptan ayrılma
 * - Hesap güncelleme
 * - Hesap silme
 */
class MuzibuCorporateAccountObserver
{
    /**
     * Handle the MuzibuCorporateAccount "created" event.
     * Kurumsal hesap oluşturulduğunda veya kullanıcı katıldığında
     */
    public function created(MuzibuCorporateAccount $account): void
    {
        $this->clearCorporateCaches($account->user_id);

        if (function_exists('log_activity')) {
            // Ana şube mi yoksa alt şube (katılım) mı?
            if ($account->parent_id === null) {
                // Ana şube oluşturuldu
                log_activity($account, 'kurumsal hesap oluşturdu', [
                    'company_name' => $account->company_name,
                    'corporate_code' => $account->corporate_code,
                    'type' => 'ana_sube'
                ]);
            } else {
                // Kullanıcı kurumsal hesaba katıldı
                $parentAccount = $account->parent;
                log_activity($account, 'kurumsal hesaba katıldı', [
                    'company_name' => $parentAccount?->company_name,
                    'corporate_code' => $parentAccount?->corporate_code,
                    'parent_id' => $account->parent_id,
                    'type' => 'katilim'
                ]);
            }
        }

        Log::info('Muzibu CorporateAccount created', [
            'id' => $account->id,
            'user_id' => $account->user_id,
            'company_name' => $account->company_name,
            'parent_id' => $account->parent_id,
            'is_parent' => $account->parent_id === null,
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Handle the MuzibuCorporateAccount "updating" event.
     */
    public function updating(MuzibuCorporateAccount $account): void
    {
        $dirty = $account->getDirty();

        Log::info('Muzibu CorporateAccount updating', [
            'id' => $account->id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the MuzibuCorporateAccount "updated" event.
     */
    public function updated(MuzibuCorporateAccount $account): void
    {
        $this->clearCorporateCaches($account->user_id);

        if (function_exists('log_activity')) {
            $changes = $account->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                $oldCompanyName = null;
                if (isset($changes['company_name'])) {
                    $oldCompanyName = $account->getOriginal('company_name');
                }

                // Önemli değişiklikleri logla
                $changedFields = array_keys($changes);
                $description = 'güncellendi';

                if (in_array('is_active', $changedFields)) {
                    $description = $account->is_active ? 'aktif edildi' : 'pasif edildi';
                } elseif (in_array('corporate_code', $changedFields)) {
                    $description = 'kurumsal kodu değiştirildi';
                } elseif (in_array('company_name', $changedFields)) {
                    $description = 'şirket adı değiştirildi';
                } elseif (in_array('branch_name', $changedFields)) {
                    $description = 'şube adı değiştirildi';
                }

                log_activity($account, $description, [
                    'changed_fields' => $changedFields,
                    'old_company_name' => $oldCompanyName
                ], $oldCompanyName);
            }
        }

        Log::info('Muzibu CorporateAccount updated', [
            'id' => $account->id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the MuzibuCorporateAccount "deleting" event.
     * Kurumsal hesaptan ayrılma veya hesap silme
     */
    public function deleting(MuzibuCorporateAccount $account): bool
    {
        // Ayrılma öncesi bilgileri sakla (deleted event'te kullanmak için)
        $account->_deletion_info = [
            'company_name' => $account->company_name,
            'corporate_code' => $account->corporate_code,
            'parent_id' => $account->parent_id,
            'parent_company_name' => $account->parent?->company_name,
            'is_parent' => $account->parent_id === null,
            'children_count' => $account->children()->count()
        ];

        Log::info('Muzibu CorporateAccount deleting', [
            'id' => $account->id,
            'user_id' => $account->user_id,
            'company_name' => $account->company_name,
            'parent_id' => $account->parent_id,
            'deleted_by' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the MuzibuCorporateAccount "deleted" event.
     */
    public function deleted(MuzibuCorporateAccount $account): void
    {
        $this->clearCorporateCaches($account->user_id);

        if (function_exists('log_activity')) {
            $info = $account->_deletion_info ?? [];

            if (($info['parent_id'] ?? null) !== null) {
                // Alt şube ayrıldı
                log_activity($account, 'kurumsal hesaptan ayrıldı', [
                    'company_name' => $info['parent_company_name'] ?? 'Bilinmiyor',
                    'corporate_code' => $info['corporate_code'] ?? null,
                    'type' => 'ayrilma'
                ], $info['parent_company_name'] ?? null);
            } else {
                // Ana şube silindi (fesih)
                log_activity($account, 'kurumsal hesabı feshetti', [
                    'company_name' => $info['company_name'] ?? 'Bilinmiyor',
                    'corporate_code' => $info['corporate_code'] ?? null,
                    'children_count' => $info['children_count'] ?? 0,
                    'type' => 'fesih'
                ], $info['company_name'] ?? null);
            }
        }

        Log::info('Muzibu CorporateAccount deleted', [
            'id' => $account->id,
            'user_id' => $account->user_id,
            'company_name' => $account->company_name ?? 'N/A',
            'deleted_by' => auth()->id()
        ]);
    }

    /**
     * Clear corporate account related caches
     */
    private function clearCorporateCaches(?int $userId = null): void
    {
        Cache::forget('muzibu_corporate_accounts_list');
        Cache::forget('muzibu_corporate_stats');

        if ($userId) {
            Cache::forget("user_{$userId}_corporate_account");
            Cache::forget("user_{$userId}_corporate_status");
        }

        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['muzibu_corporate', 'muzibu', 'corporate'])->flush();
        }

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }
}
