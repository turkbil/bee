<?php

namespace Modules\Muzibu\App\Observers;

use Modules\Muzibu\App\Models\Sector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * Sector Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class SectorObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the Sector "creating" event.
     */
    public function creating(Sector $sector): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($sector->slug) && !empty($sector->title)) {
            $slugs = [];
            foreach ($sector->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $sector->slug = $slugs;
            }
        }

        // Varsayılan değerleri ayarla
        if (!isset($sector->is_active)) {
            $sector->is_active = true;
        }

        Log::info('Muzibu Sector creating', [
            'title' => $sector->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Sector "created" event.
     */
    public function created(Sector $sector): void
    {
        $this->clearSectorCaches();

        if (function_exists('log_activity')) {
            log_activity($sector, 'oluşturuldu');
        }

        Log::info('Muzibu Sector created successfully', [
            'sector_id' => $sector->sector_id,
            'title' => $sector->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Sector "updating" event.
     */
    public function updating(Sector $sector): void
    {
        $dirty = $sector->getDirty();

        if (isset($dirty['slug']) && is_array($dirty['slug'])) {
            foreach ($dirty['slug'] as $locale => $slug) {
                if ($this->isSlugTaken($slug, $locale, $sector->sector_id)) {
                    $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $sector->sector_id);
                }
            }
            $sector->slug = $dirty['slug'];
        }

        Log::info('Muzibu Sector updating', [
            'sector_id' => $sector->sector_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Sector "updated" event.
     */
    public function updated(Sector $sector): void
    {
        $this->clearSectorCaches($sector->sector_id);

        if (function_exists('log_activity')) {
            $changes = $sector->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $sector->getOriginal('title');
                }

                log_activity($sector, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldTitle);
            }
        }

        Log::info('Muzibu Sector updated successfully', [
            'sector_id' => $sector->sector_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Sector "saving" event.
     */
    public function saving(Sector $sector): void
    {
        if (is_array($sector->title)) {
            foreach ($sector->title as $locale => $title) {
                $minLength = 2;
                $maxLength = 191;

                if (!empty($title)) {
                    if (strlen($title) < $minLength) {
                        throw new \Exception("Sektör adı en az {$minLength} karakter olmalıdır ({$locale})");
                    }

                    if (strlen($title) > $maxLength) {
                        $sector->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Muzibu Sector title auto-trimmed', [
                            'sector_id' => $sector->sector_id,
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
     * Handle the Sector "saved" event.
     */
    public function saved(Sector $sector): void
    {
        Cache::forget("universal_seo_muzibu_sector_{$sector->sector_id}");

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Handle the Sector "deleting" event.
     */
    public function deleting(Sector $sector): bool
    {
        Log::info('Muzibu Sector deleting', [
            'sector_id' => $sector->sector_id,
            'title' => $sector->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Sector "deleted" event.
     */
    public function deleted(Sector $sector): void
    {
        $this->clearSectorCaches($sector->sector_id);

        // Sector-Radio ilişkilerini temizle
        $sector->radios()->detach();

        if (function_exists('log_activity')) {
            log_activity($sector, 'silindi', null, $sector->title);
        }

        Log::info('Muzibu Sector deleted successfully', [
            'sector_id' => $sector->sector_id,
            'title' => $sector->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Sector "restoring" event.
     */
    public function restoring(Sector $sector): void
    {
        Log::info('Muzibu Sector restoring', [
            'sector_id' => $sector->sector_id,
            'title' => $sector->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Sector "restored" event.
     */
    public function restored(Sector $sector): void
    {
        $this->clearSectorCaches();

        if (function_exists('log_activity')) {
            log_activity($sector, 'geri yüklendi');
        }

        Log::info('Muzibu Sector restored successfully', [
            'sector_id' => $sector->sector_id,
            'title' => $sector->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Sector "forceDeleting" event.
     */
    public function forceDeleting(Sector $sector): bool
    {
        Log::warning('Muzibu Sector force deleting', [
            'sector_id' => $sector->sector_id,
            'title' => $sector->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Sector "forceDeleted" event.
     */
    public function forceDeleted(Sector $sector): void
    {
        $this->clearSectorCaches($sector->sector_id);

        if (function_exists('log_activity')) {
            log_activity($sector, 'kalıcı silindi', null, $sector->title);
        }

        Log::warning('Muzibu Sector force deleted', [
            'sector_id' => $sector->sector_id,
            'title' => $sector->title,
            'user_id' => auth()->id()
        ]);
    }

    private function clearSectorCaches(?int $sectorId = null): void
    {
        $this->cacheService->flushByPrefix('muzibu_sectors');

        Cache::forget('muzibu_sectors_list');
        Cache::forget('muzibu_sectors_active');

        if ($sectorId) {
            Cache::forget("muzibu_sector_detail_{$sectorId}");
            Cache::forget("universal_seo_muzibu_sector_{$sectorId}");
        }

        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['muzibu_sectors', 'muzibu', 'content'])->flush();
        }

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    private function isSlugTaken(string $slug, string $locale, ?int $excludeId = null): bool
    {
        $query = Sector::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('sector_id', '!=', $excludeId);
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
