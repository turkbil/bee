<?php
namespace Modules\Portfolio\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use App\Traits\HasSeo;
use App\Contracts\TranslatableEntity;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PortfolioCategory extends BaseModel implements TranslatableEntity
{
    use Sluggable, HasTranslations, HasSeo, HasFactory;

    protected $primaryKey = 'category_id';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'name' => 'array',
        'slug' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['name', 'slug', 'description'];

    /**
     * ID accessor - category_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->category_id;
    }

    /**
     * Sluggable Ayarları - JSON çoklu dil desteği için devre dışı
     */
    public function sluggable(): array
    {
        return [];
    }

    /**
     * Aktif kategorileri getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Portfolios ilişkisi
     */
    public function portfolios()
    {
        return $this->hasMany(Portfolio::class, 'category_id', 'category_id');
    }

    /**
     * HasSeo trait fallback implementations
     */

    protected function getSeoFallbackTitle(): ?string
    {
        return $this->getTranslated('name', app()->getLocale()) ?? $this->name;
    }

    protected function getSeoFallbackDescription(): ?string
    {
        $description = $this->getTranslated('description', app()->getLocale()) ?? $this->description;

        if (is_string($description)) {
            return \Illuminate\Support\Str::limit(strip_tags($description), 160);
        }

        return null;
    }

    protected function getSeoFallbackKeywords(): array
    {
        $name = $this->getSeoFallbackTitle();

        if ($name) {
            $words = array_filter(explode(' ', strtolower($name)), function($word) {
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
            return url('/portfolio/category/' . ltrim($slug, '/'));
        }

        return null;
    }

    protected function getSeoFallbackImage(): ?string
    {
        return null;
    }

    protected function getSeoFallbackSchemaMarkup(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
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
            'name' => 'text',
            'description' => 'text',
            'slug' => 'auto'
        ];
    }

    public function hasSeoSettings(): bool
    {
        return true;
    }

    public function afterTranslation(string $targetLanguage, array $translatedData): void
    {
        \Log::info("Portfolio Category çevirisi tamamlandı", [
            'category_id' => $this->category_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData)
        ]);
    }

    public function getPrimaryKeyName(): string
    {
        return 'category_id';
    }

    protected static function newFactory()
    {
        return \Modules\Portfolio\Database\Factories\PortfolioCategoryFactory::new();
    }
}
