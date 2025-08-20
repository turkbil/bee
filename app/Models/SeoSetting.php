<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Log;
use App\Services\SeoLanguageManager;
use App\Services\AI\SeoAnalysisService;
use App\Traits\HasTranslations;

class SeoSetting extends Model
{
    use HasTranslations;
    
    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['titles', 'descriptions', 'keywords'];
    
    protected $fillable = [
        'seoable_type', 'seoable_id',
        'titles', 'descriptions', 'keywords',
        'og_titles', 'og_descriptions', 'og_image', 'og_type',
        'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image',
        'canonical_url', 'robots_meta', 'schema_markup', 'focus_keyword', 'focus_keywords',
        'additional_keywords', 'seo_score', 'seo_analysis', 'last_analyzed',
        'hreflang_urls', 'content_length', 'keyword_density', 'readability_score',
        'page_speed_insights', 'last_crawled', 'ai_suggestions', 'auto_optimize',
        'status', 'priority_score', 'available_languages', 'default_language', 'language_fallbacks'
    ];

    protected $casts = [
        'titles' => 'array',
        'descriptions' => 'array', 
        'keywords' => 'array',
        'focus_keywords' => 'array',
        'og_titles' => 'array',
        'og_descriptions' => 'array',
        'og_title' => 'array',
        'og_description' => 'array',
        'canonical_url' => 'array',
        'robots_meta' => 'array',
        'schema_markup' => 'array',
        'additional_keywords' => 'array',
        'seo_analysis' => 'array',
        'hreflang_urls' => 'array',
        'readability_score' => 'array',
        'page_speed_insights' => 'array',
        'ai_suggestions' => 'array',
        'available_languages' => 'array',
        'language_fallbacks' => 'array',
        'last_analyzed' => 'datetime',
        'last_crawled' => 'datetime',
        'auto_optimize' => 'boolean'
    ];

    protected $attributes = [
        'og_type' => 'website',
        'twitter_card' => 'summary',
        'seo_score' => 0,
        'status' => 'active',
        'priority_score' => 5,
        'default_language' => 'tr',
        'auto_optimize' => false
    ];

    /**
     * Polymorphic relationship - herhangi bir modele bağlanabilir
     */
    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Multi-language title getter with fallback
     */
    public function getTitle(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->titles) {
            return null;
        }

        return SeoLanguageManager::getSafeValue($this->titles, $locale, $this->default_language);
    }

    /**
     * Check if direct title exists for specific locale (no fallback)
     */
    public function hasDirectTitle(?string $locale = null): bool
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->titles || !is_array($this->titles)) {
            return false;
        }

        return isset($this->titles[$locale]) && !empty($this->titles[$locale]);
    }

    /**
     * Check if direct description exists for specific locale (no fallback)
     */
    public function hasDirectDescription(?string $locale = null): bool
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->descriptions || !is_array($this->descriptions)) {
            return false;
        }

        return isset($this->descriptions[$locale]) && !empty($this->descriptions[$locale]);
    }

    /**
     * Multi-language description getter with fallback
     */
    public function getDescription(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->descriptions) {
            return null;
        }

        return SeoLanguageManager::getSafeValue($this->descriptions, $locale, $this->default_language);
    }

    /**
     * Multi-language keywords getter with fallback
     */
    public function getKeywords(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->keywords) {
            return [];
        }

        $keywords = SeoLanguageManager::getSafeValue($this->keywords, $locale, $this->default_language);
        
        return is_array($keywords) ? $keywords : 
            ($keywords ? explode(',', $keywords) : []);
    }

    /**
     * Multi-language focus keywords getter with fallback
     */
    public function getFocusKeywords(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->focus_keywords) {
            return null;
        }

        return SeoLanguageManager::getSafeValue($this->focus_keywords, $locale, $this->default_language);
    }

    /**
     * Safe robots meta getter
     */
    public function getRobotsMeta(): array
    {
        return array_merge([
            'index' => true,
            'follow' => true,
            'archive' => true,
            'snippet' => true,
            'imageindex' => true
        ], $this->robots_meta ?? []);
    }

    /**
     * Generate robots meta tag string
     */
    public function getRobotsMetaString(): string
    {
        $robots = $this->getRobotsMeta();
        $directives = [];

        foreach ($robots as $directive => $value) {
            if ($value === true) {
                $directives[] = $directive;
            } elseif ($value === false && in_array($directive, ['index', 'follow'])) {
                $directives[] = 'no' . $directive;
            }
        }

        return implode(', ', $directives);
    }

    /**
     * Multi-language canonical URL getter with fallback
     */
    public function getCanonicalUrl(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        // Eğer canonical URL tek değer ise direkt döndür
        if (is_string($this->canonical_url)) {
            return $this->canonical_url;
        }
        
        // Array ise dil bazında döndür
        if (is_array($this->canonical_url)) {
            return SeoLanguageManager::getSafeValue($this->canonical_url, $locale, $this->default_language);
        }
        
        return null;
    }

    /**
     * Multi-language OG title getter with fallback
     */
    public function getOgTitle(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->og_titles) {
            return $this->getTitle($locale);
        }

        return SeoLanguageManager::getSafeValue($this->og_titles, $locale, $this->default_language)
            ?? $this->getTitle($locale);
    }

    /**
     * Multi-language OG description getter with fallback
     */
    public function getOgDescription(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->og_descriptions) {
            return $this->getDescription($locale);
        }

        return SeoLanguageManager::getSafeValue($this->og_descriptions, $locale, $this->default_language)
            ?? $this->getDescription($locale);
    }

    /**
     * Multi-language OG image getter with fallback
     */
    public function getOgImage(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (is_string($this->og_image)) {
            return $this->og_image;
        }
        
        if (is_array($this->og_image)) {
            return SeoLanguageManager::getSafeValue($this->og_image, $locale, $this->default_language);
        }
        
        return null;
    }

    /**
     * Multi-language robots getter with fallback
     */
    public function getRobots(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (is_string($this->robots_meta)) {
            return $this->robots_meta;
        }
        
        if (is_array($this->robots_meta)) {
            $robotsValue = SeoLanguageManager::getSafeValue($this->robots_meta, $locale, $this->default_language);
            return $robotsValue ?? 'index,follow';
        }
        
        return 'index,follow';
    }

    /**
     * Get hreflang URLs for current locale
     */
    public function getHreflangUrls(): array
    {
        if (!$this->hreflang_urls) {
            return [];
        }

        return array_filter($this->hreflang_urls, function($url) {
            return !empty($url) && filter_var($url, FILTER_VALIDATE_URL);
        });
    }

    /**
     * Check if SEO is optimized (score >= 80)
     */
    public function isOptimized(): bool
    {
        return $this->seo_score >= 80;
    }

    /**
     * Check if needs analysis (older than 24 hours or never analyzed)
     */
    public function needsAnalysis(): bool
    {
        if (!$this->last_analyzed) {
            return true;
        }

        return $this->last_analyzed->diffInHours(now()) >= 24;
    }

    /**
     * Get SEO score color class for UI
     */
    public function getScoreColor(): string
    {
        if ($this->seo_score >= 80) return 'text-success';
        if ($this->seo_score >= 60) return 'text-warning';
        return 'text-danger';
    }

    /**
     * Get SEO score badge class for UI
     */
    public function getScoreBadge(): string
    {
        if ($this->seo_score >= 80) return 'bg-success';
        if ($this->seo_score >= 60) return 'bg-warning';
        return 'bg-danger';
    }

    /**
     * Update language support
     */
    public function updateLanguageSupport(array $languages): void
    {
        $this->available_languages = $languages;
        
        // Clean up language data for removed languages
        if ($this->titles) {
            $this->titles = array_intersect_key($this->titles, array_flip($languages));
        }
        
        if ($this->descriptions) {
            $this->descriptions = array_intersect_key($this->descriptions, array_flip($languages));
        }
        
        if ($this->keywords) {
            $this->keywords = array_intersect_key($this->keywords, array_flip($languages));
        }
        
        if ($this->canonical_url) {
            $this->canonical_url = array_intersect_key($this->canonical_url, array_flip($languages));
        }
        
        if ($this->hreflang_urls) {
            $this->hreflang_urls = array_intersect_key($this->hreflang_urls, array_flip($languages));
        }

        $this->save();
    }

    /**
     * Bulk update SEO data for specific language
     */
    public function updateLanguageData(string $locale, array $data): void
    {
        if (isset($data['title'])) {
            $titles = $this->titles ?? [];
            $titles[$locale] = $data['title'];
            $this->titles = $titles;
        }

        if (isset($data['description'])) {
            $descriptions = $this->descriptions ?? [];
            $descriptions[$locale] = $data['description'];
            $this->descriptions = $descriptions;
        }

        if (isset($data['keywords'])) {
            $keywords = $this->keywords ?? [];
            $keywords[$locale] = is_array($data['keywords']) ? $data['keywords'] : explode(',', $data['keywords']);
            $this->keywords = $keywords;
        }

        if (isset($data['focus_keywords'])) {
            $focusKeywords = $this->focus_keywords ?? [];
            $focusKeywords[$locale] = $data['focus_keywords'];
            $this->focus_keywords = $focusKeywords;
        }

        if (isset($data['canonical_url'])) {
            $this->canonical_url = $data['canonical_url'];
        }

        if (isset($data['hreflang_url'])) {
            $hreflang = $this->hreflang_urls ?? [];
            $hreflang[$locale] = $data['hreflang_url'];
            $this->hreflang_urls = $hreflang;
        }

        $this->save();
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeNeedsAnalysis($query)
    {
        return $query->where(function($q) {
            $q->whereNull('last_analyzed')
              ->orWhere('last_analyzed', '<', now()->subDay());
        });
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority_score', '>=', 8);
    }

    public function scopeByScore($query, string $operator = '>=', int $score = 80)
    {
        return $query->where('seo_score', $operator, $score);
    }

    /**
     * Multi-language setter methods for PageSeoRepository
     */
    public function setTitle(string $locale, ?string $value): void
    {
        $titles = $this->titles ?? [];
        if ($value !== null && $value !== '') {
            $titles[$locale] = SeoLanguageManager::sanitizeString($value);
        } else {
            unset($titles[$locale]);
        }
        $this->titles = $titles;
    }

    public function setDescription(string $locale, ?string $value): void
    {
        $descriptions = $this->descriptions ?? [];
        if ($value !== null && $value !== '') {
            $descriptions[$locale] = SeoLanguageManager::sanitizeString($value);
        } else {
            unset($descriptions[$locale]);
        }
        $this->descriptions = $descriptions;
    }

    public function setKeywords(string $locale, $value): void
    {
        $keywords = $this->keywords ?? [];
        if ($value !== null && $value !== '') {
            if (is_string($value)) {
                $keywordArray = array_map('trim', explode(',', $value));
                $keywords[$locale] = SeoLanguageManager::sanitizeArray($keywordArray);
            } elseif (is_array($value)) {
                $keywords[$locale] = SeoLanguageManager::sanitizeArray($value);
            }
        } else {
            unset($keywords[$locale]);
        }
        $this->keywords = $keywords;
    }

    public function setCanonicalUrl(string $locale, ?string $value): void
    {
        $canonicalUrls = $this->canonical_url ?? [];
        if ($value !== null && $value !== '') {
            $canonicalUrls[$locale] = SeoLanguageManager::sanitizeString($value);
        } else {
            unset($canonicalUrls[$locale]);
        }
        $this->canonical_url = $canonicalUrls;
    }

    public function setRobots(string $locale, ?string $value): void
    {
        if ($value !== null && $value !== '') {
            $this->robots_meta = SeoLanguageManager::sanitizeString($value);
        }
    }

    public function setOgTitle(string $locale, ?string $value): void
    {
        $ogTitles = $this->og_titles ?? [];
        if ($value !== null && $value !== '') {
            $ogTitles[$locale] = SeoLanguageManager::sanitizeString($value);
        } else {
            unset($ogTitles[$locale]);
        }
        $this->og_titles = $ogTitles;
    }

    public function setOgDescription(string $locale, ?string $value): void
    {
        $ogDescriptions = $this->og_descriptions ?? [];
        if ($value !== null && $value !== '') {
            $ogDescriptions[$locale] = SeoLanguageManager::sanitizeString($value);
        } else {
            unset($ogDescriptions[$locale]);
        }
        $this->og_descriptions = $ogDescriptions;
    }

    public function setOgImage(string $locale, ?string $value): void
    {
        if ($value !== null && $value !== '') {
            // OG Image tek değer olarak saklanabilir
            $this->og_image = SeoLanguageManager::sanitizeString($value);
        }
    }

    /**
     * Boot model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Set default language support based on available languages or fallback
            if (!$model->available_languages) {
                // Try to get from context or default to multilingual support
                $model->available_languages = ['tr', 'en', 'ar'];
            }
        });

        static::updated(function ($model) {
            // Clear cache when SEO settings are updated
            cache()->forget("seo_settings_{$model->seoable_type}_{$model->seoable_id}");
        });
    }
}