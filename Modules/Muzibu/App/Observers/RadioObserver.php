<?php

namespace Modules\Muzibu\App\Observers;

use Modules\Muzibu\App\Models\Radio;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * Radio Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class RadioObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the Radio "creating" event.
     */
    public function creating(Radio $radio): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($radio->slug) && !empty($radio->title)) {
            $slugs = [];
            foreach ($radio->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $radio->slug = $slugs;
            }
        }

        // Varsayılan değerleri ayarla
        if (!isset($radio->is_active)) {
            $radio->is_active = true;
        }

        Log::info('Muzibu Radio creating', [
            'title' => $radio->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Radio "created" event.
     */
    public function created(Radio $radio): void
    {
        $this->clearRadioCaches();

        if (function_exists('log_activity')) {
            log_activity($radio, 'oluşturuldu');
        }

        Log::info('Muzibu Radio created successfully', [
            'radio_id' => $radio->radio_id,
            'title' => $radio->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Radio "updating" event.
     */
    public function updating(Radio $radio): void
    {
        $dirty = $radio->getDirty();

        if (isset($dirty['slug']) && is_array($dirty['slug'])) {
            foreach ($dirty['slug'] as $locale => $slug) {
                if ($this->isSlugTaken($slug, $locale, $radio->radio_id)) {
                    $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $radio->radio_id);
                }
            }
            $radio->slug = $dirty['slug'];
        }

        Log::info('Muzibu Radio updating', [
            'radio_id' => $radio->radio_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Radio "updated" event.
     */
    public function updated(Radio $radio): void
    {
        $this->clearRadioCaches($radio->radio_id);

        if (function_exists('log_activity')) {
            $changes = $radio->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $radio->getOriginal('title');
                }

                log_activity($radio, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldTitle);
            }
        }

        Log::info('Muzibu Radio updated successfully', [
            'radio_id' => $radio->radio_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Radio "saving" event.
     */
    public function saving(Radio $radio): void
    {
        if (is_array($radio->title)) {
            foreach ($radio->title as $locale => $title) {
                $minLength = 2;
                $maxLength = 191;

                if (!empty($title)) {
                    if (strlen($title) < $minLength) {
                        throw new \Exception("Radyo adı en az {$minLength} karakter olmalıdır ({$locale})");
                    }

                    if (strlen($title) > $maxLength) {
                        $radio->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Muzibu Radio title auto-trimmed', [
                            'radio_id' => $radio->radio_id,
                            'locale' => $locale,
                            'original_length' => strlen($title),
                            'trimmed_length' => $maxLength
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Handle the Radio "saved" event.
     */
    public function saved(Radio $radio): void
    {
        Cache::forget("universal_seo_muzibu_radio_{$radio->radio_id}");

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Handle the Radio "deleting" event.
     */
    public function deleting(Radio $radio): bool
    {
        Log::info('Muzibu Radio deleting', [
            'radio_id' => $radio->radio_id,
            'title' => $radio->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Radio "deleted" event.
     */
    public function deleted(Radio $radio): void
    {
        $this->clearRadioCaches($radio->radio_id);

        // Radio-Sector ilişkilerini temizle
        $radio->sectors()->detach();

        if ($radio->seoSetting) {
            $radio->seoSetting->delete();
        }

        if (function_exists('log_activity')) {
            log_activity($radio, 'silindi', null, $radio->title);
        }

        Log::info('Muzibu Radio deleted successfully', [
            'radio_id' => $radio->radio_id,
            'title' => $radio->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Radio "restoring" event.
     */
    public function restoring(Radio $radio): void
    {
        Log::info('Muzibu Radio restoring', [
            'radio_id' => $radio->radio_id,
            'title' => $radio->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Radio "restored" event.
     */
    public function restored(Radio $radio): void
    {
        $this->clearRadioCaches();

        if (function_exists('log_activity')) {
            log_activity($radio, 'geri yüklendi');
        }

        Log::info('Muzibu Radio restored successfully', [
            'radio_id' => $radio->radio_id,
            'title' => $radio->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Radio "forceDeleting" event.
     */
    public function forceDeleting(Radio $radio): bool
    {
        Log::warning('Muzibu Radio force deleting', [
            'radio_id' => $radio->radio_id,
            'title' => $radio->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Radio "forceDeleted" event.
     */
    public function forceDeleted(Radio $radio): void
    {
        $this->clearRadioCaches($radio->radio_id);

        if (function_exists('log_activity')) {
            log_activity($radio, 'kalıcı silindi', null, $radio->title);
        }

        Log::warning('Muzibu Radio force deleted', [
            'radio_id' => $radio->radio_id,
            'title' => $radio->title,
            'user_id' => auth()->id()
        ]);
    }

    private function clearRadioCaches(?int $radioId = null): void
    {
        $this->cacheService->flushByPrefix('muzibu_radios');

        Cache::forget('muzibu_radios_list');
        Cache::forget('muzibu_radios_active');

        if ($radioId) {
            Cache::forget("muzibu_radio_detail_{$radioId}");
            Cache::forget("universal_seo_muzibu_radio_{$radioId}");
        }

        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['muzibu_radios', 'muzibu', 'content'])->flush();
        }

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    private function isSlugTaken(string $slug, string $locale, ?int $excludeId = null): bool
    {
        $query = Radio::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('radio_id', '!=', $excludeId);
        }

        return $query->exists();
    }

    private function generateUniqueSlug(string $baseSlug, string $locale, ?int $excludeId = null): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while ($this->isSlugTaken($slug, $locale, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
