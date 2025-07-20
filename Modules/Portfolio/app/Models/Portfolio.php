<?php

namespace Modules\Portfolio\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use App\Traits\HasSeo;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Portfolio extends BaseModel implements HasMedia
{
    use Sluggable, SoftDeletes, InteractsWithMedia, HasTranslations, HasSeo;

    protected $primaryKey = 'portfolio_id';

    protected $fillable = [
        'portfolio_category_id',
        'title',
        'slug',
        'body',
        'image',
        'css',
        'js',
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
        $keywords = [];
        
        // Extract from title
        $title = $this->getSeoFallbackTitle();
        if ($title) {
            $words = array_filter(explode(' ', strtolower($title)), function($word) {
                return strlen($word) > 3;
            });
            $keywords = array_merge($keywords, array_slice($words, 0, 3));
        }
        
        // Add category name if available
        if ($this->category) {
            $categoryName = $this->category->getTranslated('name', app()->getLocale());
            if ($categoryName) {
                $keywords[] = strtolower($categoryName);
            }
        }
        
        return array_unique($keywords);
    }

    /**
     * Get fallback canonical URL
     */
    protected function getSeoFallbackCanonicalUrl(): ?string
    {
        $slug = $this->getTranslated('slug', app()->getLocale()) ?? $this->slug;
        
        if ($slug) {
            return url('/portfolio/' . ltrim($slug, '/'));
        }
        
        return null;
    }

    /**
     * Get fallback image for social sharing
     */
    protected function getSeoFallbackImage(): ?string
    {
        // First try direct image field
        if ($this->image) {
            return asset($this->image);
        }
        
        // Try media library
        $media = $this->getFirstMedia('images');
        if ($media) {
            return $media->getUrl();
        }
        
        // Extract from content
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
            '@type' => 'CreativeWork',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
            'image' => $this->getSeoFallbackImage(),
            'dateCreated' => $this->created_at?->toISOString(),
            'dateModified' => $this->updated_at?->toISOString(),
            'creator' => [
                '@type' => 'Organization',
                'name' => config('app.name')
            ]
        ];
    }

    public function sluggable(): array
    {
        return [
            // JSON slug alanları manuel olarak yönetiliyor
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class, 'portfolio_category_id', 'portfolio_category_id');
    }

    protected static function booted()
    {
        static::saving(function ($portfolio) {
            if ($portfolio->portfolio_category_id) {
                $category = PortfolioCategory::find($portfolio->portfolio_category_id);
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
             ->singleFile()
             ->useDisk('public');
    }
    
}