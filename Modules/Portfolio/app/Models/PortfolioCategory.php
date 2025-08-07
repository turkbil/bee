<?php
// Modules/Portfolio/App/Models/PortfolioCategory.php
namespace Modules\Portfolio\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use App\Traits\HasSeo;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortfolioCategory extends BaseModel 
{
    use Sluggable, SoftDeletes, HasTranslations, HasSeo;

    protected $primaryKey = 'portfolio_category_id';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'title' => 'array',
        'slug' => 'array',
        'body' => 'array',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug', 'body'];


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
    
    /**
     * SEO için title fallback - JSON title alanından string döndür
     */
    protected function getSeoFallbackTitle(): ?string
    {
        if (isset($this->title) && is_array($this->title)) {
            // Önce mevcut dil, sonra Türkçe, sonra ilk değeri döndür
            $locale = app()->getLocale() ?? 'tr';
            return $this->title[$locale] ?? $this->title['tr'] ?? reset($this->title);
        }
        
        return $this->title ?? null;
    }
    
    /**
     * SEO için description fallback - JSON body alanından string döndür
     */
    protected function getSeoFallbackDescription(): ?string
    {
        if (isset($this->body) && is_array($this->body)) {
            $locale = app()->getLocale() ?? 'tr';
            $body = $this->body[$locale] ?? $this->body['tr'] ?? reset($this->body);
            return $body ? strip_tags($body) : null;
        }
        
        return isset($this->body) ? strip_tags($this->body) : null;
    }
}