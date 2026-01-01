<?php

namespace Modules\Blog\App\Observers;

use Modules\Blog\App\Models\Blog;

class BlogObserver
{
    /**
     * Handle the Blog "saving" event.
     */
    public function saving(Blog $blog): void
    {
        // Excerpt otomatik oluştur (eğer boşsa)
        if ($blog->isDirty('body') && empty($blog->excerpt)) {
            $this->generateExcerpt($blog);
        }
    }

    /**
     * Handle the Blog "created" event.
     */
    public function created(Blog $blog): void
    {
        // Activity log
        if (function_exists('log_activity')) {
            log_activity($blog, 'oluşturuldu');
        }
    }

    /**
     * Handle the Blog "updated" event.
     */
    public function updated(Blog $blog): void
    {
        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $blog->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                // Eski başlığı al (title değiştiyse)
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $blog->getOriginal('title');
                }

                log_activity($blog, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldTitle);
            }
        }
    }

    /**
     * Handle the Blog "saved" event.
     */
    public function saved(Blog $blog): void
    {
        // Blog kaydedildikten sonra cache'i temizle
        if ($blog->wasChanged(['title', 'body', 'is_active', 'published_at'])) {
            $this->clearRelatedCaches($blog);
        }
    }

    /**
     * Handle the Blog "deleted" event.
     */
    public function deleted(Blog $blog): void
    {
        // Blog silindiğinde cache'leri temizle
        $this->clearRelatedCaches($blog);

        // Activity log - silinen kaydın başlığını sakla
        if (function_exists('log_activity')) {
            log_activity($blog, 'silindi', null, $blog->title);
        }
    }

    /**
     * Handle the Blog "restoring" event.
     */
    public function restoring(Blog $blog): void
    {
        \Log::info('Blog restoring', [
            'blog_id' => $blog->blog_id,
            'title' => $blog->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Blog "restored" event.
     */
    public function restored(Blog $blog): void
    {
        $this->clearRelatedCaches($blog);

        if (function_exists('log_activity')) {
            log_activity($blog, 'geri yüklendi');
        }

        \Log::info('Blog restored successfully', [
            'blog_id' => $blog->blog_id,
            'title' => $blog->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Blog "forceDeleting" event.
     */
    public function forceDeleting(Blog $blog): bool
    {
        \Log::warning('Blog force deleting', [
            'blog_id' => $blog->blog_id,
            'title' => $blog->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Blog "forceDeleted" event.
     */
    public function forceDeleted(Blog $blog): void
    {
        $this->clearRelatedCaches($blog);

        if (function_exists('log_activity')) {
            log_activity($blog, 'kalıcı silindi', null, $blog->title);
        }

        \Log::warning('Blog force deleted', [
            'blog_id' => $blog->blog_id,
            'title' => $blog->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Excerpt otomatik oluştur
     */
    private function generateExcerpt(Blog $blog): void
    {
        $excerpts = [];
        $languages = get_tenant_languages();

        foreach ($languages as $locale => $language) {
            $content = $blog->getTranslated('body', $locale);
            if ($content) {
                $plainText = strip_tags($content);
                $excerpt = \Illuminate\Support\Str::limit($plainText, 160);
                $excerpts[$locale] = $excerpt;
            }
        }

        if (!empty($excerpts)) {
            $blog->excerpt = $excerpts;
        }
    }

    /**
     * İlgili cache'leri temizle
     */
    private function clearRelatedCaches(Blog $blog): void
    {
        try {
            // Blog listesi cache'lerini temizle
            \Cache::tags(['blog'])->flush();

            // SEO cache'lerini temizle
            \Cache::forget("seo_meta_blog_{$blog->blog_id}");

            // Schema cache'lerini temizle
            \Cache::forget("schema_blog_{$blog->blog_id}");

            // Blog category cache'lerini temizle
            if ($blog->blog_category_id) {
                \Cache::tags(['blog_category_' . $blog->blog_category_id])->flush();
            }

            // Sitemap cache'ini temizle
            \Cache::forget('sitemap_blog');

            // Sitemap XML cache temizle (yeni blog içeriği için)
            $tenantId = tenant()?->id ?? 'central';
            \Cache::forget("sitemap_xml_{$tenantId}");

            // RSS feed cache'ini temizle
            \Cache::forget('rss_blog');

        } catch (\Exception $e) {
            \Log::warning('Blog cache temizleme hatası: ' . $e->getMessage());
        }
    }
}
