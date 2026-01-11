<?php

namespace Modules\Announcement\App\Observers;

use Modules\Announcement\App\Models\Announcement;
use Modules\Announcement\App\Exceptions\AnnouncementValidationException;
use Modules\Announcement\App\Exceptions\AnnouncementProtectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * Announcement Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class AnnouncementObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the Announcement "creating" event.
     * Yeni kayıt oluşturulmadan önce çalışır
     */
    public function creating(Announcement $announcement): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($announcement->slug) && !empty($announcement->title)) {
            $slugs = [];
            foreach ($announcement->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $announcement->slug = $slugs;
            }
        }

        // Varsayılan değerleri config'den al
        $defaults = config('announcement.defaults', []);
        foreach ($defaults as $field => $value) {
            if (!isset($announcement->$field)) {
                $announcement->$field = $value;
            }
        }


        Log::info('Announcement creating', [
            'title' => $announcement->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Announcement "created" event.
     * Kayıt oluşturulduktan sonra çalışır
     */
    public function created(Announcement $announcement): void
    {
        // Cache temizle
        $this->clearAnnouncementCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($announcement, 'oluşturuldu');
        }

        Log::info('Announcement created successfully', [
            'announcement_id' => $announcement->announcement_id,
            'title' => $announcement->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Announcement "updating" event.
     * Güncelleme yapılmadan önce çalışır
     */
    public function updating(Announcement $announcement): void
    {
        // Değişen alanları tespit et
        $dirty = $announcement->getDirty();



        // Slug değişiklik kontrolü - benzersizlik
        if (isset($dirty['slug'])) {
            // Slug'ın array olup olmadığını kontrol et
            if (is_array($dirty['slug'])) {
                foreach ($dirty['slug'] as $locale => $slug) {
                    if ($this->isSlugTaken($slug, $locale, $announcement->announcement_id)) {
                        // Slug'a otomatik sayı ekle
                        $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $announcement->announcement_id);
                    }
                }
                $announcement->slug = $dirty['slug'];
            }
        }

        Log::info('Announcement updating', [
            'announcement_id' => $announcement->announcement_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Announcement "updated" event.
     * Güncelleme yapıldıktan sonra çalışır
     */
    public function updated(Announcement $announcement): void
    {
        // Cache temizle
        $this->clearAnnouncementCaches($announcement->announcement_id);

        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $announcement->getChanges();
            unset($changes['updated_at']); // updated_at'i loglamaya gerek yok

            if (!empty($changes)) {
                // Eski başlığı al (title değiştiyse)
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $announcement->getOriginal('title');
                }

                log_activity($announcement, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldTitle);
            }
        }

        Log::info('Announcement updated successfully', [
            'announcement_id' => $announcement->announcement_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Announcement "saving" event.
     * Create veya Update'ten önce çalışır (her ikisinde de)
     */
    public function saving(Announcement $announcement): void
    {
        // Title ve slug validasyon
        if (is_array($announcement->title)) {
            foreach ($announcement->title as $locale => $title) {
                $minLength = config('announcement.validation.title.min', 3);
                $maxLength = config('announcement.validation.title.max', 191);

                if (!empty($title)) {
                    // Minimum length check
                    if (strlen($title) < $minLength) {
                        throw AnnouncementValidationException::titleTooShort($locale, $minLength);
                    }

                    // Maximum length check - auto trim instead of throwing exception
                    if (strlen($title) > $maxLength) {
                        // AI translation bazen uzun gelebilir, otomatik kısalt
                        $announcement->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Announcement title auto-trimmed', [
                            'announcement_id' => $announcement->announcement_id,
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
     * Handle the Announcement "saved" event.
     * Create veya Update'ten sonra çalışır (her ikisinde de)
     */
    public function saved(Announcement $announcement): void
    {
        // Universal SEO cache temizle
        Cache::forget("universal_seo_announcement_{$announcement->announcement_id}");

        // Response cache temizle
        if (function_exists('responsecache')) {
            responsecache()->forget(route('announcement.show', $announcement->slug));
        }
    }

    /**
     * Handle the Announcement "deleting" event.
     * Silme işleminden önce çalışır
     */
    public function deleting(Announcement $announcement): bool
    {
        // Reserved slug kontrolü
        $reservedSlugs = config('announcement.slug.reserved_slugs', []);
        if (is_array($announcement->slug)) {
            foreach ($announcement->slug as $locale => $slug) {
                if (in_array($slug, $reservedSlugs)) {
                    throw AnnouncementProtectionException::protectedSlug($slug);
                }
            }
        }

        Log::info('Announcement deleting', [
            'announcement_id' => $announcement->announcement_id,
            'title' => $announcement->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Announcement "deleted" event.
     * Silme işleminden sonra çalışır
     */
    public function deleted(Announcement $announcement): void
    {
        // Spatie Media Library - Görselleri temizle
        $announcement->clearMediaCollection('featured_image');
        $announcement->clearMediaCollection('gallery');

        // Cache temizle
        $this->clearAnnouncementCaches($announcement->announcement_id);

        // SEO ayarlarını da sil
        if ($announcement->seoSetting) {
            $announcement->seoSetting->delete();
        }

        // Activity log - silinen kaydın başlığını sakla
        if (function_exists('log_activity')) {
            log_activity($announcement, 'silindi', null, $announcement->title);
        }

        Log::info('Announcement deleted successfully', [
            'announcement_id' => $announcement->announcement_id,
            'title' => $announcement->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Announcement "restoring" event.
     * Soft delete'ten geri dönüşte çalışır
     */
    public function restoring(Announcement $announcement): void
    {
        Log::info('Announcement restoring', [
            'announcement_id' => $announcement->announcement_id,
            'title' => $announcement->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Announcement "restored" event.
     * Soft delete'ten geri döndükten sonra çalışır
     */
    public function restored(Announcement $announcement): void
    {
        // Cache temizle
        $this->clearAnnouncementCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($announcement, 'geri yüklendi');
        }

        Log::info('Announcement restored successfully', [
            'announcement_id' => $announcement->announcement_id,
            'title' => $announcement->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Announcement "forceDeleting" event.
     * Kalıcı silme işleminden önce çalışır
     */
    public function forceDeleting(Announcement $announcement): bool
    {
        Log::warning('Announcement force deleting', [
            'announcement_id' => $announcement->announcement_id,
            'title' => $announcement->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Announcement "forceDeleted" event.
     * Kalıcı silme işleminden sonra çalışır
     */
    public function forceDeleted(Announcement $announcement): void
    {
        // Spatie Media Library - Görselleri temizle
        $announcement->clearMediaCollection('featured_image');
        $announcement->clearMediaCollection('gallery');

        // Tüm cache'leri temizle
        $this->clearAnnouncementCaches($announcement->announcement_id);

        // Activity log - kalıcı silme
        if (function_exists('log_activity')) {
            log_activity($announcement, 'kalıcı silindi', null, $announcement->title);
        }

        Log::warning('Announcement force deleted', [
            'announcement_id' => $announcement->announcement_id,
            'title' => $announcement->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Announcement cache'lerini temizle
     */
    private function clearAnnouncementCaches(?int $announcementId = null): void
    {
        // TenantCacheService ile prefix bazlı temizleme
        $this->cacheService->flushByPrefix('announcements');

        // Spesifik cache key'leri temizle
        Cache::forget('announcements_list');
        Cache::forget('announcements_menu_cache');
        Cache::forget('announcements_sitemap_cache');

        if ($announcementId) {
            Cache::forget("announcement_detail_{$announcementId}");
            Cache::forget("universal_seo_announcement_{$announcementId}");
        }

        // Tag bazlı cache temizleme
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['announcements', 'content'])->flush();
        }

        // Response cache temizle
        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Slug'ın benzersiz olup olmadığını kontrol et
     */
    private function isSlugTaken(string $slug, string $locale, ?int $excludeId = null): bool
    {
        $query = Announcement::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('announcement_id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Benzersiz slug oluştur
     */
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
