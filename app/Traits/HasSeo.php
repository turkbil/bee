<?php

namespace App\Traits;

use App\Models\SeoSetting;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeo
{
    /**
     * Polymorphic relationship to SEO settings
     */
    public function seoSetting(): MorphOne
    {
        return $this->morphOne(SeoSetting::class, 'seoable');
    }

    /**
     * Get or create SEO settings for this model
     */
    public function getOrCreateSeoSetting(): SeoSetting
    {
        if (!$this->seoSetting) {
            $this->seoSetting()->create([
                'default_language' => app()->getLocale(),
                'available_languages' => ['tr', 'en'],
                'status' => 'active',
                'priority' => 'medium'
            ]);
            
            // Refresh relationship
            $this->load('seoSetting');
        }

        return $this->seoSetting;
    }

    /**
     * Get SEO title for current or specified locale
     */
    public function getSeoTitle(?string $locale = null): ?string
    {
        if (!$this->seoSetting) {
            return $this->getSeoFallbackTitle();
        }

        return $this->seoSetting->getTitle($locale) ?? $this->getSeoFallbackTitle();
    }

    /**
     * Get SEO description for current or specified locale
     */
    public function getSeoDescription(?string $locale = null): ?string
    {
        if (!$this->seoSetting) {
            return $this->getSeoFallbackDescription();
        }

        return $this->seoSetting->getDescription($locale) ?? $this->getSeoFallbackDescription();
    }

    /**
     * Get SEO keywords for current or specified locale
     */
    public function getSeoKeywords(?string $locale = null): array
    {
        if (!$this->seoSetting) {
            return $this->getSeoFallbackKeywords();
        }

        $keywords = $this->seoSetting->getKeywords($locale);
        
        return !empty($keywords) ? $keywords : $this->getSeoFallbackKeywords();
    }

    /**
     * Get canonical URL for SEO
     */
    public function getSeoCanonicalUrl(): ?string
    {
        if ($this->seoSetting && $this->seoSetting->canonical_url) {
            return $this->seoSetting->canonical_url;
        }

        return $this->getSeoFallbackCanonicalUrl();
    }

    /**
     * Get Open Graph data
     */
    public function getOpenGraphData(?string $locale = null): array
    {
        $seo = $this->seoSetting;
        
        return [
            'title' => $seo?->og_title ?? $this->getSeoTitle($locale),
            'description' => $seo?->og_description ?? $this->getSeoDescription($locale),
            'image' => $seo?->og_image ?? $this->getSeoFallbackImage(),
            'type' => $seo?->og_type ?? 'website',
            'url' => $this->getSeoCanonicalUrl()
        ];
    }

    /**
     * Get Twitter Card data
     */
    public function getTwitterCardData(?string $locale = null): array
    {
        $seo = $this->seoSetting;
        
        return [
            'card' => $seo?->twitter_card ?? 'summary',
            'title' => $seo?->twitter_title ?? $this->getSeoTitle($locale),
            'description' => $seo?->twitter_description ?? $this->getSeoDescription($locale),
            'image' => $seo?->twitter_image ?? $this->getSeoFallbackImage()
        ];
    }

    /**
     * Get robots meta directives
     */
    public function getRobotsMetaString(): string
    {
        if (!$this->seoSetting) {
            return 'index, follow';
        }

        return $this->seoSetting->getRobotsMetaString();
    }

    /**
     * Get schema markup data
     */
    public function getSchemaMarkup(): ?array
    {
        if (!$this->seoSetting || !$this->seoSetting->schema_markup) {
            return $this->getSeoFallbackSchemaMarkup();
        }

        return $this->seoSetting->schema_markup;
    }

    /**
     * Get hreflang URLs
     */
    public function getHreflangUrls(): array
    {
        if (!$this->seoSetting) {
            return [];
        }

        return $this->seoSetting->getHreflangUrls();
    }

    /**
     * Update SEO data for specific language
     */
    public function updateSeoForLanguage(string $locale, array $data): void
    {
        $seoSetting = $this->getOrCreateSeoSetting();
        $seoSetting->updateLanguageData($locale, $data);
    }

    /**
     * Update supported languages for SEO
     */
    public function updateSeoLanguages(array $languages): void
    {
        $seoSetting = $this->getOrCreateSeoSetting();
        $seoSetting->updateLanguageSupport($languages);
    }

    /**
     * Check if SEO is optimized
     */
    public function isSeoOptimized(): bool
    {
        if (!$this->seoSetting) {
            return false;
        }

        return $this->seoSetting->isOptimized();
    }

    /**
     * Get SEO score
     */
    public function getSeoScore(): int
    {
        if (!$this->seoSetting) {
            return 0;
        }

        return $this->seoSetting->seo_score;
    }

    /**
     * Check if needs SEO analysis
     */
    public function needsSeoAnalysis(): bool
    {
        if (!$this->seoSetting) {
            return true;
        }

        return $this->seoSetting->needsAnalysis();
    }

    /**
     * Generate complete meta tags HTML
     */
    public function getMetaTagsHtml(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $html = [];

        // Basic meta tags
        if ($title = $this->getSeoTitle($locale)) {
            $html[] = '<title>' . e($title) . '</title>';
            $html[] = '<meta name="title" content="' . e($title) . '">';
        }

        if ($description = $this->getSeoDescription($locale)) {
            $html[] = '<meta name="description" content="' . e($description) . '">';
        }

        $keywords = $this->getSeoKeywords($locale);
        if (!empty($keywords)) {
            $html[] = '<meta name="keywords" content="' . e(implode(', ', $keywords)) . '">';
        }

        // Robots meta
        $html[] = '<meta name="robots" content="' . $this->getRobotsMetaString() . '">';

        // Canonical URL
        if ($canonical = $this->getSeoCanonicalUrl()) {
            $html[] = '<link rel="canonical" href="' . e($canonical) . '">';
        }

        // Open Graph
        $og = $this->getOpenGraphData($locale);
        foreach ($og as $property => $content) {
            if ($content) {
                $html[] = '<meta property="og:' . $property . '" content="' . e($content) . '">';
            }
        }

        // Twitter Card
        $twitter = $this->getTwitterCardData($locale);
        foreach ($twitter as $name => $content) {
            if ($content) {
                $html[] = '<meta name="twitter:' . $name . '" content="' . e($content) . '">';
            }
        }

        // Hreflang
        $hreflang = $this->getHreflangUrls();
        foreach ($hreflang as $lang => $url) {
            $html[] = '<link rel="alternate" hreflang="' . e($lang) . '" href="' . e($url) . '">';
        }

        // Schema markup
        if ($schema = $this->getSchemaMarkup()) {
            $html[] = '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
        }

        return implode("\n", $html);
    }

    /**
     * Auto-update SEO content based on model data
     */
    public function autoUpdateSeo(?string $locale = null): void
    {
        $locale = $locale ?? app()->getLocale();
        $seoSetting = $this->getOrCreateSeoSetting();

        // Only auto-update if no manual SEO data exists
        if (!$seoSetting->getTitle($locale)) {
            $this->updateSeoForLanguage($locale, [
                'title' => $this->getSeoFallbackTitle(),
                'description' => $this->getSeoFallbackDescription(),
                'keywords' => $this->getSeoFallbackKeywords()
            ]);
        }
    }

    /**
     * Boot trait
     */
    public static function bootHasSeo(): void
    {
        static::created(function ($model) {
            // Auto-create SEO settings for new models
            $model->autoUpdateSeo();
        });

        static::updated(function ($model) {
            // Auto-update SEO if enabled
            if ($model->seoSetting && $model->seoSetting->auto_optimize) {
                $model->autoUpdateSeo();
            }
        });
    }

    // Abstract methods that implementing models should define

    /**
     * Get fallback title when no SEO title is set
     */
    protected function getSeoFallbackTitle(): ?string
    {
        // Default implementation - models can override
        if (isset($this->title)) {
            return $this->title;
        }
        
        if (isset($this->name)) {
            return $this->name;
        }

        return null;
    }

    /**
     * Get fallback description when no SEO description is set
     */
    protected function getSeoFallbackDescription(): ?string
    {
        // Default implementation - models can override
        if (isset($this->description)) {
            return str_limit(strip_tags($this->description), 160);
        }
        
        if (isset($this->content)) {
            return str_limit(strip_tags($this->content), 160);
        }

        return null;
    }

    /**
     * Get fallback keywords when no SEO keywords are set
     */
    protected function getSeoFallbackKeywords(): array
    {
        // Default implementation - models can override
        return [];
    }

    /**
     * Get fallback canonical URL
     */
    protected function getSeoFallbackCanonicalUrl(): ?string
    {
        // Default implementation - models can override
        if (method_exists($this, 'getUrl')) {
            return $this->getUrl();
        }

        return null;
    }

    /**
     * Get fallback image for social sharing
     */
    protected function getSeoFallbackImage(): ?string
    {
        // Default implementation - models can override
        if (isset($this->image)) {
            return $this->image;
        }
        
        if (isset($this->featured_image)) {
            return $this->featured_image;
        }

        return null;
    }

    /**
     * Get fallback schema markup
     */
    protected function getSeoFallbackSchemaMarkup(): ?array
    {
        // Default implementation - models can override
        return null;
    }
}