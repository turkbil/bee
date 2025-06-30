<?php
// Modules/Portfolio/App/Models/PortfolioCategory.php
namespace Modules\Portfolio\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortfolioCategory extends BaseModel 
{
    use Sluggable, SoftDeletes, HasTranslations;

    protected $primaryKey = 'portfolio_category_id';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'order',
        'metakey',
        'metadesc',
        'seo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'title' => 'array',
        'slug' => 'array',
        'body' => 'array',
        'metakey' => 'array',
        'metadesc' => 'array',
        'seo' => 'array',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug', 'body', 'metakey', 'metadesc'];

    /**
     * SEO alanını belirli dil için getir
     */
    public function getSeoField(string $field, ?string $locale = null, $default = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->seo[$locale][$field] ?? $this->seo[$field] ?? $default;
    }

    /**
     * SEO title getir (fallback sistemi ile)
     */
    public function getSeoTitle(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        // SEO title varsa onu döndür
        $seoTitle = $this->getSeoField('title', $locale);
        if ($seoTitle) {
            return $seoTitle;
        }
        
        // Yoksa normal title'ı döndür
        return $this->title[$locale] ?? $this->title['tr'] ?? '';
    }

    /**
     * SEO description getir (fallback sistemi ile)
     */
    public function getSeoDescription(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        // SEO description varsa onu döndür
        $seoDesc = $this->getSeoField('description', $locale);
        if ($seoDesc) {
            return $seoDesc;
        }
        
        // Yoksa metadesc'i döndür
        $metaDesc = $this->metadesc[$locale] ?? $this->metadesc['tr'] ?? '';
        if ($metaDesc) {
            return $metaDesc;
        }
        
        // Yoksa body'den kısa açıklama oluştur
        $body = $this->body[$locale] ?? $this->body['tr'] ?? '';
        return \Illuminate\Support\Str::limit(strip_tags($body), 155, '');
    }

    /**
     * SEO keywords getir (fallback sistemi ile)
     */
    public function getSeoKeywords(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        // SEO keywords varsa onu döndür
        $seoKeywords = $this->getSeoField('keywords', $locale);
        if ($seoKeywords) {
            return $seoKeywords;
        }
        
        // Yoksa metakey'i döndür
        $metaKey = $this->metakey[$locale] ?? $this->metakey['tr'] ?? '';
        return is_array($metaKey) ? implode(', ', $metaKey) : $metaKey;
    }

    /**
     * SEO verilerini güncelle
     */
    public function updateSeoData(string $field, $value, ?string $locale = null): self
    {
        $locale = $locale ?? app()->getLocale();
        $seo = $this->seo ?? [];
        
        if (!isset($seo[$locale])) {
            $seo[$locale] = [];
        }
        
        $seo[$locale][$field] = $value;
        $this->seo = $seo;
        
        return $this;
    }

    /**
     * Sluggable Ayarları - JSON slug alanları için devre dışı
     */
    public function sluggable(): array
    {
        return [
            // JSON slug alanları manuel olarak yönetiliyor
        ];
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class, 'portfolio_category_id', 'portfolio_category_id');
    }
}