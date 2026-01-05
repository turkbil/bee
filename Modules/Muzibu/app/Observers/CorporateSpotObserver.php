<?php

namespace Modules\Muzibu\App\Observers;

use Modules\Muzibu\App\Models\CorporateSpot;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CorporateSpot Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Activity logging ve cache temizleme işlemlerini otomatikleştirir.
 * Spot değişikliklerinde parent account'un spot_settings_version'ını artırır.
 */
class CorporateSpotObserver
{
    /**
     * İzlenecek field'ler (değişirse version artırılır)
     */
    protected array $trackedFields = [
        'title',
        'starts_at',
        'ends_at',
        'is_enabled',
        'is_archived',
        'position',
    ];
    /**
     * Handle the CorporateSpot "created" event.
     */
    public function created(CorporateSpot $spot): void
    {
        $this->clearSpotCaches($spot->corporate_account_id);

        // ✅ YENİ: Version artır (yeni anons eklendi)
        $this->incrementVersion($spot);

        if (function_exists('log_activity')) {
            log_activity($spot, 'oluşturuldu');
        }

        Log::info('Muzibu CorporateSpot created successfully', [
            'id' => $spot->id,
            'title' => $spot->title,
            'corporate_account_id' => $spot->corporate_account_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the CorporateSpot "updating" event.
     */
    public function updating(CorporateSpot $spot): void
    {
        $dirty = $spot->getDirty();

        Log::info('Muzibu CorporateSpot updating', [
            'id' => $spot->id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the CorporateSpot "updated" event.
     */
    public function updated(CorporateSpot $spot): void
    {
        $this->clearSpotCaches($spot->corporate_account_id, $spot->id);

        // ✅ YENİ: İzlenen field'lerden biri değiştiyse version artır
        $changes = $spot->getChanges();
        foreach ($this->trackedFields as $field) {
            if (array_key_exists($field, $changes)) {
                $this->incrementVersion($spot);
                break; // Bir field yeter
            }
        }

        if (function_exists('log_activity')) {
            unset($changes['updated_at']);

            if (!empty($changes)) {
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $spot->getOriginal('title');
                }

                log_activity($spot, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldTitle);
            }
        }

        Log::info('Muzibu CorporateSpot updated successfully', [
            'id' => $spot->id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the CorporateSpot "saving" event.
     */
    public function saving(CorporateSpot $spot): void
    {
        // Title validation
        if (!empty($spot->title)) {
            $minLength = 2;
            $maxLength = 191;

            if (strlen($spot->title) < $minLength) {
                throw new \Exception("Anons adı en az {$minLength} karakter olmalıdır");
            }

            if (strlen($spot->title) > $maxLength) {
                $spot->title = mb_substr($spot->title, 0, $maxLength);

                Log::warning('Muzibu CorporateSpot title auto-trimmed', [
                    'id' => $spot->id,
                    'original_length' => strlen($spot->title),
                    'trimmed_length' => $maxLength
                ]);
            }
        }
    }

    /**
     * Handle the CorporateSpot "saved" event.
     */
    public function saved(CorporateSpot $spot): void
    {
        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Handle the CorporateSpot "deleting" event.
     */
    public function deleting(CorporateSpot $spot): bool
    {
        Log::info('Muzibu CorporateSpot deleting', [
            'id' => $spot->id,
            'title' => $spot->title,
            'corporate_account_id' => $spot->corporate_account_id,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the CorporateSpot "deleted" event.
     */
    public function deleted(CorporateSpot $spot): void
    {
        $this->clearSpotCaches($spot->corporate_account_id, $spot->id);

        // ✅ YENİ: Version artır (anons silindi)
        $this->incrementVersion($spot);

        if (function_exists('log_activity')) {
            log_activity($spot, 'silindi', null, $spot->title);
        }

        Log::info('Muzibu CorporateSpot deleted successfully', [
            'id' => $spot->id,
            'title' => $spot->title,
            'corporate_account_id' => $spot->corporate_account_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Clear corporate spot related caches
     */
    private function clearSpotCaches(?int $corporateAccountId = null, ?int $spotId = null): void
    {
        Cache::forget('muzibu_corporate_spots_list');

        if ($corporateAccountId) {
            Cache::forget("muzibu_corporate_spots_{$corporateAccountId}");
            Cache::forget("muzibu_corporate_spots_active_{$corporateAccountId}");
        }

        if ($spotId) {
            Cache::forget("muzibu_corporate_spot_detail_{$spotId}");
        }

        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['muzibu_corporate_spots', 'muzibu', 'corporate'])->flush();
        }

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Parent account'un spot_settings_version'ını artır
     * Bu method spot değişikliklerinde (create/update/delete) çağrılır
     */
    protected function incrementVersion(CorporateSpot $spot): void
    {
        try {
            $corporateAccountId = $spot->corporate_account_id;

            if (!$corporateAccountId) {
                return;
            }

            // Version'ı artır (atomic operation)
            DB::table('muzibu_corporate_accounts')
                ->where('id', $corporateAccountId)
                ->update([
                    'spot_settings_version' => DB::raw('spot_settings_version + 1'),
                    'updated_at' => now(),
                ]);

            Log::info('🎙️ SpotObserver: Version incremented', [
                'corporate_account_id' => $corporateAccountId,
                'spot_id' => $spot->id,
                'spot_title' => $spot->title,
            ]);

        } catch (\Exception $e) {
            Log::error('🎙️ SpotObserver: Failed to increment version', [
                'error' => $e->getMessage(),
                'spot_id' => $spot->id ?? null,
            ]);
        }
    }
}
