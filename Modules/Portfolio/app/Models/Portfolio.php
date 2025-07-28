<?php

declare(strict_types=1);

namespace Modules\Portfolio\App\Models;

use App\Models\BaseModel;
use App\Models\SeoSetting;
use App\Traits\HasTranslations;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Portfolio extends BaseModel implements HasMedia
{
    use Sluggable, SoftDeletes, InteractsWithMedia, HasTranslations;

    protected $primaryKey = 'portfolio_id';

    protected $fillable = [
        'portfolio_category_id',
        'title',
        'slug',
        'body',
        'image',
        'client',
        'date',
        'url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'title' => 'array',
        'slug' => 'array',
        'body' => 'array',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug', 'body'];

    /**
     * Sluggable configuration (manuel yönetim)
     */
    public function sluggable(): array
    {
        return [
            // JSON slug alanları manuel olarak yönetiliyor
        ];
    }

    /**
     * SEO Setting Relationship (Global SEO System)
     */
    public function seoSetting(): MorphOne
    {
        return $this->morphOne(SeoSetting::class, 'seoable');
    }

    /**
     * Update SEO for specific language
     */
    public function updateSeoForLanguage(string $language, array $seoData): void
    {
        // SEO verisi boş mu kontrol et
        $hasAnyData = false;
        foreach (['seo_title', 'seo_description', 'seo_keywords'] as $field) {
            if (!empty($seoData[$field])) {
                $hasAnyData = true;
                break;
            }
        }
        if (!empty($seoData['canonical_url'])) {
            $hasAnyData = true;
        }
        
        // Eğer hiç SEO verisi yoksa işlem yapma
        if (!$hasAnyData) {
            return;
        }
        
        // Mevcut SEO kaydını al veya yeni oluştur
        $seoSetting = $this->seoSetting;
        if (!$seoSetting) {
            $seoSetting = new SeoSetting();
            $seoSetting->seoable()->associate($this);
        }
        
        // Update titles
        $titles = $seoSetting->titles ?? [];
        if (!empty($seoData['seo_title'])) {
            $titles[$language] = $seoData['seo_title'];
        }
        $seoSetting->titles = $titles;
        
        // Update descriptions  
        $descriptions = $seoSetting->descriptions ?? [];
        if (!empty($seoData['seo_description'])) {
            $descriptions[$language] = $seoData['seo_description'];
        }
        $seoSetting->descriptions = $descriptions;
        
        // Update keywords (convert string to array)
        $keywords = $seoSetting->keywords ?? [];
        if (!empty($seoData['seo_keywords'])) {
            if (is_string($seoData['seo_keywords'])) {
                $keywordArray = array_filter(array_map('trim', explode(',', $seoData['seo_keywords'])));
                $keywords[$language] = $keywordArray;
            } else {
                $keywords[$language] = $seoData['seo_keywords'];
            }
        }
        $seoSetting->keywords = $keywords;
        
        // Update canonical URL
        if (!empty($seoData['canonical_url'])) {
            $seoSetting->canonical_url = $seoData['canonical_url'];
        }
        
        $seoSetting->save();
    }

    /**
     * Portfolio Category Relationship
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class, 'portfolio_category_id', 'portfolio_category_id');
    }

    /**
     * Media Collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
             ->singleFile()
             ->useDisk('public');
             
        $this->addMediaCollection('gallery')
             ->useDisk('public');
    }
    
    /**
     * Model Events
     */
    protected static function booted(): void
    {
        static::saving(function ($portfolio) {
            // Portfolio kategorisi kontrolü
            if ($portfolio->portfolio_category_id) {
                $category = PortfolioCategory::find($portfolio->portfolio_category_id);
                // Category ile ilgili işlemler burada yapılabilir
            }
        });
    }
}