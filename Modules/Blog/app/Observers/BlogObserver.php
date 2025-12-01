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
