<?php

declare(strict_types=1);

namespace Modules\SeoManagement\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\HasTranslations;

class SeoSetting extends Model
{
    use HasTranslations;
    
    protected $translatable = ['titles', 'descriptions', 'keywords', 'og_title', 'og_description', 'focus_keywords'];
    
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

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTitle(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->titles) {
            return $this->meta_title;
        }

        return $this->getTranslated('titles', $locale) ?? $this->meta_title;
    }

    public function getDescription(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->descriptions) {
            return $this->meta_description;
        }

        return $this->getTranslated('descriptions', $locale) ?? $this->meta_description;
    }

    public function getKeywords(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->keywords) {
            return $this->meta_keywords ? explode(',', $this->meta_keywords) : [];
        }

        $keywords = $this->getTranslated('keywords', $locale);
        
        return is_array($keywords) ? $keywords : 
            ($keywords ? explode(',', $keywords) : []);
    }

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

    public function getCanonicalUrl(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (is_string($this->canonical_url)) {
            return $this->canonical_url;
        }
        
        if (is_array($this->canonical_url)) {
            return $this->canonical_url[$locale] ?? null;
        }
        
        return null;
    }

    public function getOgTitle(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->og_title) {
            return $this->getTitle($locale);
        }

        return $this->getTranslated('og_title', $locale) ?? $this->getTitle($locale);
    }

    public function getOgDescription(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->og_description) {
            return $this->getDescription($locale);
        }

        return $this->getTranslated('og_description', $locale) ?? $this->getDescription($locale);
    }

    public function isOptimized(): bool
    {
        return $this->seo_score >= 80;
    }

    public function needsAnalysis(): bool
    {
        if (!$this->last_analyzed) {
            return true;
        }

        return $this->last_analyzed->diffInHours(now()) >= 24;
    }

    public function getScoreColor(): string
    {
        if ($this->seo_score >= 80) return 'text-success';
        if ($this->seo_score >= 60) return 'text-warning';
        return 'text-danger';
    }

    public function getScoreBadge(): string
    {
        if ($this->seo_score >= 80) return 'bg-success';
        if ($this->seo_score >= 60) return 'bg-warning';
        return 'bg-danger';
    }

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->available_languages) {
                $model->available_languages = ['tr', 'en', 'ar'];
            }
        });

        static::updated(function ($model) {
            cache()->forget("seo_settings_{$model->seoable_type}_{$model->seoable_id}");
        });
    }
}