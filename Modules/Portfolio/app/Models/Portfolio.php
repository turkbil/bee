<?php
namespace Modules\Portfolio\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use App\Traits\HasSeo;
use App\Contracts\TranslatableEntity;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Portfolio extends BaseModel implements TranslatableEntity
{
    use Sluggable, HasTranslations, HasSeo, HasFactory;

    protected $primaryKey = 'portfolio_id';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'css',
        'js',
        'category_id',
        'is_active',
    ];

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'body' => 'array',
        'category_id' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug', 'body'];

    /**
     * ID accessor - portfolio_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->portfolio_id;
    }

    /**
     * Sluggable Ayarları - JSON çoklu dil desteği için devre dışı
     */
    public function sluggable(): array
    {
        return [];
    }

    /**
     * Aktif portfolioları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Category ilişkisi
     */
    public function category()
    {
        return $this->belongsTo(PortfolioCategory::class, 'category_id', 'category_id');
    }

    /**
     * HasSeo trait fallback implementations
     */

    protected function getSeoFallbackTitle(): ?string
    {
        return $this->getTranslated('title', app()->getLocale()) ?? $this->title;
    }

    protected function getSeoFallbackDescription(): ?string
    {
        $content = $this->getTranslated('body', app()->getLocale()) ?? $this->body;

        if (is_string($content)) {
            return \Illuminate\Support\Str::limit(strip_tags($content), 160);
        }

        return null;
    }

    protected function getSeoFallbackKeywords(): array
    {
        $title = $this->getSeoFallbackTitle();

        if ($title) {
            $words = array_filter(explode(' ', strtolower($title)), function($word) {
                return strlen($word) > 3;
            });

            return array_slice($words, 0, 5);
        }

        return [];
    }

    protected function getSeoFallbackCanonicalUrl(): ?string
    {
        $slug = $this->getTranslated('slug', app()->getLocale()) ?? $this->slug;

        if ($slug) {
            return url('/portfolio/' . ltrim($slug, '/'));
        }

        return null;
    }

    protected function getSeoFallbackImage(): ?string
    {
        $content = $this->getTranslated('body', app()->getLocale()) ?? $this->body;

        if (is_string($content) && preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function getSeoFallbackSchemaMarkup(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
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
     * Get or create SEO setting
     */
    public function getOrCreateSeoSetting()
    {
        if (!$this->seoSetting) {
            $this->seoSetting()->create([
                'titles' => [],
                'descriptions' => [],
                'og_titles' => [],
                'og_descriptions' => [],
                'robots_meta' => [
                    'index' => true,
                    'follow' => true,
                    'archive' => true
                ],
                'status' => 'active'
            ]);

            $this->load('seoSetting');
        }

        return $this->seoSetting;
    }

    /**
     * TranslatableEntity interface implementation
     */
    public function getTranslatableFields(): array
    {
        return [
            'title' => 'text',
            'body' => 'html',
            'slug' => 'auto'
        ];
    }

    public function hasSeoSettings(): bool
    {
        return true;
    }

    public function afterTranslation(string $targetLanguage, array $translatedData): void
    {
        \Log::info("Portfolio çevirisi tamamlandı", [
            'portfolio_id' => $this->portfolio_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData)
        ]);
    }

    public function getPrimaryKeyName(): string
    {
        return 'portfolio_id';
    }

    protected static function newFactory()
    {
        return \Modules\Portfolio\Database\Factories\PortfolioFactory::new();
    }
}
