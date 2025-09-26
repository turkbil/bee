<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\HasTranslations;

class SeoSetting extends Model
{
    use HasTranslations;
    
    public $timestamps = false; // Timestamps devre dışı - Page tablosunda zaten var
    
    protected $translatable = ['titles', 'descriptions', 'og_titles', 'og_descriptions'];
    
    protected $fillable = [
        'seoable_type', 'seoable_id', // Polymorphic relationship fields
        'titles', 'descriptions', 'canonical_url',
        'author', // Sadece blog yazarları için - varsayılan null
        'og_titles', 'og_descriptions', 'og_image', 'og_type',
        // Twitter Cards - OG verilerinden otomatik üretiliyor
        'robots_meta',
        'seo_score', 'seo_analysis', 'last_analyzed',
        'content_length', 'keyword_density', 'readability_score',
        'page_speed_insights', 'last_crawled', 'ai_suggestions',
        'analysis_results', // AI analysis results
        'status', 'priority_score'
    ];

    protected $casts = [
        'titles' => 'array',
        'descriptions' => 'array',
        'og_titles' => 'array',
        'og_descriptions' => 'array',
        'robots_meta' => 'array',
        'seo_analysis' => 'array',
        'readability_score' => 'array',
        'page_speed_insights' => 'array',
        'ai_suggestions' => 'array',
        'analysis_results' => 'array', // AI analysis results casting
        'last_analyzed' => 'datetime',
        'last_crawled' => 'datetime'
    ];

    protected $attributes = [
        'og_type' => 'website',
        'seo_score' => 0,
        'status' => 'active',
        'priority_score' => 5
    ];

    /**
     * Get default robots meta values - 2025 Google SEO best practices
     */
    public function getDefaultRobotsMeta(): array
    {
        return [
            // Temel direktifler (her zaman)
            'index' => true,          // ✅ Arama motorlarında indeksle
            'follow' => true,         // ✅ Linkleri takip et
            
            // Snippet kontrolü (2025 güncel)
            'max-snippet' => -1,     // ✅ Sınırsız snippet uzunluğu
            'max-image-preview' => 'large',  // ✅ Büyük resim önizleme
            'max-video-preview' => -1, // ✅ Sınırsız video önizleme
            
            // Arşivleme kontrolü
            'noarchive' => false,     // ✅ Cache'e izin ver (performans için)
            'noimageindex' => false,  // ✅ Resimleri indeksle
            'notranslate' => false,   // ✅ Çeviriye izin ver
            
            // Gelişmiş 2025 özellikleri
            'indexifembedded' => true, // ✅ Embedded content'i indeksle
            'noydir' => true,         // ✅ DMOZ açıklamasını kullanma
            'noodp' => true           // ✅ ODP açıklamasını kullanma
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
            return null;
        }

        return $this->getTranslated('titles', $locale);
    }

    public function getDescription(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!$this->descriptions) {
            return null;
        }

        return $this->getTranslated('descriptions', $locale);
    }

    // Keywords metodları kaldırıldı - AI tarafından doldurulacak, manuel gerek yok

    public function getRobotsMeta(): array
    {
        return array_merge($this->getDefaultRobotsMeta(), $this->robots_meta ?? []);
    }

    public function getRobotsMetaString(): string
    {
        $robots = $this->getRobotsMeta();
        $directives = [];

        foreach ($robots as $directive => $value) {
            if ($value === true) {
                $directives[] = $directive;
            } elseif ($value === false) {
                // Negatif direktifler
                if (in_array($directive, ['index', 'follow', 'archive', 'imageindex', 'translate'])) {
                    $directives[] = 'no' . $directive;
                } else {
                    // Zaten negatif olanlar (noarchive, noimageindex vs.)
                    $directives[] = $directive;
                }
            } elseif (is_string($value) || is_numeric($value)) {
                // max-snippet: 160, max-image-preview: large gibi değerler
                $directives[] = $directive . ':' . $value;
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

        // Keywords ve Focus Keywords kaldırıldı - AI tarafından doldurulacak

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
            'og_image', 'og_type'
            // Twitter alanları kaldırıldı - OG'den otomatik üretiliyor
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

        // \Log::info('✅ SeoSetting updateLanguageData çalıştı', [
        //     'locale' => $locale,
        //     'has_title' => isset($data['title']),
        //     'has_description' => isset($data['description']), 
        //     'has_keywords' => isset($data['keywords']),
        //     'has_focus_keywords' => isset($data['focus_keywords']),
        //     'focus_keywords_value' => $data['focus_keywords'] ?? 'YOK'
        // ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Model temizliği sonrası available_languages kaldırıldı
        });

        static::updated(function ($model) {
            cache()->forget("seo_settings_{$model->seoable_type}_{$model->seoable_id}");
        });
    }
}