<?php
namespace Modules\Page\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use App\Traits\HasSeo;
use Cviebrock\EloquentSluggable\Sluggable;

class Page extends BaseModel
{
    use Sluggable, HasTranslations, HasSeo;

    protected $primaryKey = 'page_id';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'css',
        'js',
        'is_active',
        'is_homepage',
    ];

    protected $casts = [
        'is_homepage' => 'boolean',
        'title' => 'array',
        'slug' => 'array',
        'body' => 'array',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug', 'body'];

    /**
     * Sluggable Ayarları - JSON çoklu dil desteği için devre dışı
     * Artık HasTranslations trait'inde generateSlugForLocale() kullanılacak
     */
    public function sluggable(): array
    {
        return [
            // JSON column çalışmadığı için devre dışı
            // 'slug' => [
            //     'source' => 'title',
            //     'unique' => true,
            //     'onUpdate' => false,
            // ],
        ];
    }

    /**
     * Aktif sayfaları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Ana sayfayı getir
     */
    public function scopeHomepage($query)
    {
        return $query->where('is_homepage', true);
    }

    /**
     * HasSeo trait fallback implementations
     */
    
    /**
     * Get fallback title for SEO
     */
    protected function getSeoFallbackTitle(): ?string
    {
        return $this->getTranslated('title', app()->getLocale()) ?? $this->title;
    }

    /**
     * Get fallback description for SEO
     */
    protected function getSeoFallbackDescription(): ?string
    {
        $content = $this->getTranslated('body', app()->getLocale()) ?? $this->body;
        
        if (is_string($content)) {
            return \Illuminate\Support\Str::limit(strip_tags($content), 160);
        }
        
        return null;
    }

    /**
     * Get fallback keywords for SEO
     */
    protected function getSeoFallbackKeywords(): array
    {
        $title = $this->getSeoFallbackTitle();
        
        if ($title) {
            // Extract meaningful words from title
            $words = array_filter(explode(' ', strtolower($title)), function($word) {
                return strlen($word) > 3; // Only words longer than 3 chars
            });
            
            return array_slice($words, 0, 5); // Max 5 keywords
        }
        
        return [];
    }

    /**
     * Get fallback canonical URL
     */
    protected function getSeoFallbackCanonicalUrl(): ?string
    {
        $slug = $this->getTranslated('slug', app()->getLocale()) ?? $this->slug;
        
        if ($slug) {
            return url('/' . ltrim($slug, '/'));
        }
        
        return null;
    }

    /**
     * Get fallback image for social sharing
     */
    protected function getSeoFallbackImage(): ?string
    {
        // Check if page has any images in content
        $content = $this->getTranslated('body', app()->getLocale()) ?? $this->body;
        
        if (is_string($content) && preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Get fallback schema markup
     */
    protected function getSeoFallbackSchemaMarkup(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => config('app.name'),
                'url' => url('/')
            ]
        ];
    }
    
    /**
     * Get or create SEO setting for this page
     */
    public function getOrCreateSeoSetting()
    {
        if (!$this->seoSetting) {
            $this->seoSetting()->create([
                'titles' => [],
                'descriptions' => [],
                'keywords' => [],
                'focus_keywords' => [],
                'og_title' => [],
                'og_description' => [],
                'robots_meta' => [
                    'index' => true,
                    'follow' => true,
                    'archive' => true
                ],
                'status' => 'active',
                'default_language' => 'tr'
            ]);
            
            // Refresh relationship
            $this->load('seoSetting');
        }
        
        return $this->seoSetting;
    }
    
}