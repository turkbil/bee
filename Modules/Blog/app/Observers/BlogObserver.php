<?php

namespace Modules\Blog\App\Observers;

use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Exceptions\BlogValidationException;
use Modules\Blog\App\Exceptions\BlogProtectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TenantCacheService;

/**
 * Blog Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Cache temizleme, slug oluşturma ve validasyon işlemlerini otomatikleştirir.
 */
class BlogObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    /**
     * Handle the Blog "creating" event.
     * Yeni kayıt oluşturulmadan önce çalışır
     */
    public function creating(Blog $blog): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($blog->slug) && !empty($blog->title)) {
            $slugs = [];
            foreach ($blog->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }
            if (!empty($slugs)) {
                $blog->slug = $slugs;
            }
        }

        // Varsayılan değerleri config'den al
        $defaults = config('blog.defaults', []);
        foreach ($defaults as $field => $value) {
            if (!isset($blog->$field)) {
                $blog->$field = $value;
            }
        }


        Log::info('Blog creating', [
            'title' => $blog->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Blog "created" event.
     * Kayıt oluşturulduktan sonra çalışır
     */
    public function created(Blog $blog): void
    {
        // Cache temizle
        $this->clearBlogCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($blog, 'oluşturuldu');
        }

        Log::info('Blog created successfully', [
            'blog_id' => $blog->blog_id,
            'title' => $blog->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Blog "updating" event.
     * Güncelleme yapılmadan önce çalışır
     */
    public function updating(Blog $blog): void
    {
        // Değişen alanları tespit et
        $dirty = $blog->getDirty();



        // Slug değişiklik kontrolü - benzersizlik
        if (isset($dirty['slug'])) {
            // Slug'ın array olup olmadığını kontrol et
            if (is_array($dirty['slug'])) {
                foreach ($dirty['slug'] as $locale => $slug) {
                    if ($this->isSlugTaken($slug, $locale, $blog->blog_id)) {
                        // Slug'a otomatik sayı ekle
                        $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $blog->blog_id);
                    }
                }
                $blog->slug = $dirty['slug'];
            }
        }

        Log::info('Blog updating', [
            'blog_id' => $blog->blog_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Blog "updated" event.
     * Güncelleme yapıldıktan sonra çalışır
     */
    public function updated(Blog $blog): void
    {
        // Cache temizle
        $this->clearBlogCaches($blog->blog_id);

        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $blog->getChanges();
            unset($changes['updated_at']); // updated_at'i loglamaya gerek yok

            if (!empty($changes)) {
                log_activity($blog, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ]);
            }
        }

        Log::info('Blog updated successfully', [
            'blog_id' => $blog->blog_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Blog "saving" event.
     * Create veya Update'ten önce çalışır (her ikisinde de)
     */
    public function saving(Blog $blog): void
    {
        // Title ve slug validasyon
        if (is_array($blog->title)) {
            foreach ($blog->title as $locale => $title) {
                $minLength = config('blog.validation.title.min', 3);
                $maxLength = config('blog.validation.title.max', 191);

                if (!empty($title)) {
                    // Minimum length check
                    if (strlen($title) < $minLength) {
                        throw BlogValidationException::titleTooShort($locale, $minLength);
                    }

                    // Maximum length check - auto trim instead of throwing exception
                    if (strlen($title) > $maxLength) {
                        // AI translation bazen uzun gelebilir, otomatik kısalt
                        $blog->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('Blog title auto-trimmed', [
                            'blog_id' => $blog->blog_id,
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
     * Handle the Blog "saved" event.
     * Create veya Update'ten sonra çalışır (her ikisinde de)
     */
    public function saved(Blog $blog): void
    {
        // Universal SEO cache temizle
        Cache::forget("universal_seo_blog_{$blog->blog_id}");

        // Response cache temizle
        if (function_exists('responsecache')) {
            responsecache()->forget(route('blog.show', $blog->slug));
        }
    }

    /**
     * Handle the Blog "deleting" event.
     * Silme işleminden önce çalışır
     */
    public function deleting(Blog $blog): bool
    {
        // Reserved slug kontrolü
        $reservedSlugs = config('blog.slug.reserved_slugs', []);
        if (is_array($blog->slug)) {
            foreach ($blog->slug as $locale => $slug) {
                if (in_array($slug, $reservedSlugs)) {
                    throw BlogProtectionException::protectedSlug($slug);
                }
            }
        }

        Log::info('Blog deleting', [
            'blog_id' => $blog->blog_id,
            'title' => $blog->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Blog "deleted" event.
     * Silme işleminden sonra çalışır
     */
    public function deleted(Blog $blog): void
    {
        // Spatie Media Library - Görselleri temizle
        $blog->clearMediaCollection('featured_image');
        $blog->clearMediaCollection('gallery');

        // Cache temizle
        $this->clearBlogCaches($blog->blog_id);

        // SEO ayarlarını da sil
        if ($blog->seoSetting) {
            $blog->seoSetting->delete();
        }

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($blog, 'silindi');
        }

        Log::info('Blog deleted successfully', [
            'blog_id' => $blog->blog_id,
            'title' => $blog->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Blog "restoring" event.
     * Soft delete'ten geri dönüşte çalışır
     */
    public function restoring(Blog $blog): void
    {
        Log::info('Blog restoring', [
            'blog_id' => $blog->blog_id,
            'title' => $blog->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Blog "restored" event.
     * Soft delete'ten geri döndükten sonra çalışır
     */
    public function restored(Blog $blog): void
    {
        // Cache temizle
        $this->clearBlogCaches();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($blog, 'geri yüklendi');
        }

        Log::info('Blog restored successfully', [
            'blog_id' => $blog->blog_id,
            'title' => $blog->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Blog "forceDeleting" event.
     * Kalıcı silme işleminden önce çalışır
     */
    public function forceDeleting(Blog $blog): bool
    {
        Log::warning('Blog force deleting', [
            'blog_id' => $blog->blog_id,
            'title' => $blog->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Blog "forceDeleted" event.
     * Kalıcı silme işleminden sonra çalışır
     */
    public function forceDeleted(Blog $blog): void
    {
        // Spatie Media Library - Görselleri temizle
        $blog->clearMediaCollection('featured_image');
        $blog->clearMediaCollection('gallery');

        // Tüm cache'leri temizle
        $this->clearBlogCaches($blog->blog_id);

        Log::warning('Blog force deleted', [
            'blog_id' => $blog->blog_id,
            'title' => $blog->title,
            'media_cleaned' => true,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Blog cache'lerini temizle
     */
    private function clearBlogCaches(?int $blogId = null): void
    {
        // TenantCacheService ile prefix bazlı temizleme
        $this->cacheService->flushByPrefix('blogs');

        // Spesifik cache key'leri temizle
        Cache::forget('blogs_list');
        Cache::forget('blogs_menu_cache');
        Cache::forget('blogs_sitemap_cache');

        if ($blogId) {
            Cache::forget("blog_detail_{$blogId}");
            Cache::forget("universal_seo_blog_{$blogId}");
        }

        // Tag bazlı cache temizleme
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['blogs', 'content'])->flush();
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
        $query = Blog::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);

        if ($excludeId) {
            $query->where('blog_id', '!=', $excludeId);
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
