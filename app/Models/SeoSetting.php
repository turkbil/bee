<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Services\SeoLanguageManager;
use App\Services\AI\SeoAnalysisService;

class SeoSetting extends Model
{
    protected $fillable = [
        'meta_title', 'meta_description', 'meta_keywords',
        'titles', 'descriptions', 'keywords',
        'og_title', 'og_description', 'og_image', 'og_type',
        'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image',
        'canonical_url', 'robots_meta', 'schema_markup', 'focus_keyword', 'focus_keywords',
        'additional_keywords', 'seo_score', 'seo_analysis', 'last_analyzed',
        'hreflang_urls', 'content_length', 'keyword_density', 'readability_score',
        'page_speed_insights', 'last_crawled', 'ai_suggestions', 'auto_optimize',
        'status', 'priority', 'available_languages', 'default_language', 'language_fallbacks'
    ];

    protected $casts = [
        'titles' => 'array',
        'descriptions' => 'array', 
        'keywords' => 'array',
        'focus_keywords' => 'array',
        'og_title' => 'array',
        'og_description' => 'array',
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
        'priority' => 'medium',
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
            return $this->meta_title;
        }

        return SeoLanguageManager::getSafeValue($this->titles, $locale, $this->default_language)
            ?? $this->meta_title;
    }

    /**
     * Multi-language description getter with fallback
     */
    public function getDescription(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->descriptions) {
            return $this->meta_description;
        }

        return SeoLanguageManager::getSafeValue($this->descriptions, $locale, $this->default_language)
            ?? $this->meta_description;
    }

    /**
     * Multi-language keywords getter with fallback
     */
    public function getKeywords(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->keywords) {
            return $this->meta_keywords ? explode(',', $this->meta_keywords) : [];
        }

        $keywords = SeoLanguageManager::getSafeValue($this->keywords, $locale, $this->default_language);
        
        return is_array($keywords) ? $keywords : 
            ($keywords ? explode(',', $keywords) : []);
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
        return $query->whereIn('priority', ['high', 'critical']);
    }

    public function scopeByScore($query, string $operator = '>=', int $score = 80)
    {
        return $query->where('seo_score', $operator, $score);
    }

    /**
     * Boot model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Set default language support
            if (!$model->available_languages) {
                $model->available_languages = ['tr', 'en'];
            }
        });

        static::updated(function ($model) {
            // Clear cache when SEO settings are updated
            cache()->forget("seo_settings_{$model->seoable_type}_{$model->seoable_id}");
        });
    }
}