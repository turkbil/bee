<?php

declare(strict_types=1);

namespace Modules\SeoManagement\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\HasTranslations;

class SeoSetting extends Model
{
    use HasTranslations;
    
    protected $translatable = ['titles', 'descriptions', 'keywords', 'og_titles', 'og_descriptions', 'focus_keywords'];
    
    protected $fillable = [
        'seoable_type', 'seoable_id', // Polymorphic relationship fields
        'meta_title', 'meta_description', 'meta_keywords',
        'titles', 'descriptions', 'keywords', 'canonical_url',
        'author', 'publisher', 'copyright', // Yeni temel meta alanları
        'og_titles', 'og_descriptions', 'og_image', 'og_type', 'og_locale', 'og_site_name',
        'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image', 'twitter_site', 'twitter_creator',
        'robots_meta', 'schema_markup', 'focus_keywords',
        'additional_keywords', 'seo_score', 'seo_analysis', 'last_analyzed',
        'hreflang_urls', 'content_length', 'keyword_density', 'readability_score',
        'page_speed_images', 'last_crawled', 'ai_suggestions', 'auto_optimize',
        'status', 'priority_score', 'available_languages', 'default_language', 'language_fallbacks'
    ];

    protected $casts = [
        'titles' => 'array',
        'descriptions' => 'array', 
        'keywords' => 'array',
        'focus_keywords' => 'array',
        'og_titles' => 'array',
        'og_descriptions' => 'array',
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
     * Get default robots meta values - Google SEO best practices
     */
    public function getDefaultRobotsMeta(): array
    {
        return [
            'index' => true,          // ✅ Aktif - Arama motorlarında görünsün
            'follow' => true,         // ✅ Aktif - Linkleri takip etsin
            'archive' => false,       // ❌ Pasif - Web arşivlemesi genel olarak gerekli değil
            'snippet' => true,        // ✅ Aktif - Arama sonuçlarında özet göstersin
            'imageindex' => true      // ✅ Aktif - Resimleri indekslesin
        ];
    }

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

    public function getFocusKeywords(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->focus_keywords) {
            return null;
        }

        return $this->getTranslated('focus_keywords', $locale);
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


    public function getOgTitle(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->og_titles) {
            return $this->getTitle($locale);
        }

        return $this->getTranslated('og_titles', $locale) ?? $this->getTitle($locale);
    }

    public function getOgDescription(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->og_descriptions) {
            return $this->getDescription($locale);
        }

        return $this->getTranslated('og_descriptions', $locale) ?? $this->getDescription($locale);
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
        return $query->where('priority_score', '>=', 8);
    }

    public function scopeByScore($query, string $operator = '>=', int $score = 80)
    {
        return $query->where('seo_score', $operator, $score);
    }

    /**
     * Update SEO data for a specific language
     */
    public function updateLanguageData(string $locale, array $data): void
    {
        // Title güncelle
        if (isset($data['title'])) {
            $titles = $this->titles ?? [];
            $titles[$locale] = $data['title'];
            $this->titles = $titles;
        }

        // Description güncelle  
        if (isset($data['description'])) {
            $descriptions = $this->descriptions ?? [];
            $descriptions[$locale] = $data['description'];
            $this->descriptions = $descriptions;
        }

        // Keywords güncelle
        if (isset($data['keywords'])) {
            $keywords = $this->keywords ?? [];
            // String'i array'e çevir
            if (is_string($data['keywords'])) {
                $keywordArray = array_filter(array_map('trim', explode(',', $data['keywords'])));
                $keywords[$locale] = $keywordArray;
            } else {
                $keywords[$locale] = $data['keywords'];
            }
            $this->keywords = $keywords;
        }

        
        // Focus keywords güncelle - titles/descriptions ile aynı pattern
        if (isset($data['focus_keywords'])) {
            \Log::info('🔥 DERIN DEBUG - SeoSetting focus_keywords girdi', [
                'locale' => $locale,
                'input_type' => gettype($data['focus_keywords']),
                'input_value' => $data['focus_keywords'],
                'input_is_array' => is_array($data['focus_keywords']),
                'current_focus_keywords' => $this->focus_keywords ?? 'NULL'
            ]);
            
            $focusKeywords = $this->focus_keywords ?? [];
            if (is_array($data['focus_keywords'])) {
                // Eğer focus_keywords direkt array ise (tüm diller)
                $this->focus_keywords = $data['focus_keywords'];
                \Log::info('🔥 FOCUS KEYWORDS - Array olarak kaydedildi', [
                    'saved_data' => $this->focus_keywords
                ]);
            } else {
                // Eğer tek dil için string ise
                $focusKeywords[$locale] = $data['focus_keywords'];
                $this->focus_keywords = $focusKeywords;
                \Log::info('🔥 FOCUS KEYWORDS - String olarak locale bazlı kaydedildi', [
                    'locale' => $locale,
                    'input_string' => $data['focus_keywords'],
                    'saved_data' => $this->focus_keywords
                ]);
            }
        }

        // OG Title güncelle - çoklu dil JSON (focus_keywords pattern)
        if (isset($data['og_title'])) {
            $ogTitles = $this->og_titles ?? [];
            $ogTitles[$locale] = $data['og_title'];
            $this->og_titles = $ogTitles;
        }

        // OG Description güncelle - çoklu dil JSON
        if (isset($data['og_description'])) {
            $ogDescriptions = $this->og_descriptions ?? [];
            $ogDescriptions[$locale] = $data['og_description'];
            $this->og_descriptions = $ogDescriptions;
        }

        // Diğer alanları güncelle - tek alan (dil bağımsız)
        $directFields = [
            'canonical_url', 'priority_score',
            'og_image', 'og_type', 
            'twitter_title', 'twitter_description', 'twitter_image', 'twitter_card'
        ];
        foreach ($directFields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }

        // Robots Meta güncelle - JSON format
        $robotsFields = ['robots_index', 'robots_follow', 'robots_archive', 'robots_snippet'];
        $robotsData = [];
        foreach ($robotsFields as $field) {
            if (isset($data[$field])) {
                $key = str_replace('robots_', '', $field);
                $robotsData[$key] = (bool)$data[$field];
            }
        }
        
        if (!empty($robotsData)) {
            $currentRobots = $this->robots_meta ?? [];
            $this->robots_meta = array_merge($currentRobots, $robotsData);
        }

        // Değişiklikleri kaydet
        $this->save();

        \Log::info('✅ SeoSetting updateLanguageData çalıştı', [
            'locale' => $locale,
            'has_title' => isset($data['title']),
            'has_description' => isset($data['description']), 
            'has_keywords' => isset($data['keywords']),
            'has_focus_keywords' => isset($data['focus_keywords']),
            'focus_keywords_value' => $data['focus_keywords'] ?? 'YOK'
        ]);
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