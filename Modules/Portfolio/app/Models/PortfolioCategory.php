<?php
// Modules/Portfolio/App/Models/PortfolioCategory.php
namespace Modules\Portfolio\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use App\Traits\HasSeo;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortfolioCategory extends BaseModel 
{
    use Sluggable, SoftDeletes, HasTranslations, HasSeo;

    protected $primaryKey = 'portfolio_category_id';

    protected $fillable = [
        'parent_id',
        'title',
        'slug',
        'body',
        'order',
        'is_active',
    ];

    protected $casts = [
        'parent_id' => 'integer',
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
     * Ana kategori ilişkisi
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id', 'portfolio_category_id');
    }

    /**
     * Alt kategoriler ilişkisi
     */
    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id', 'portfolio_category_id')
                    ->where('is_active', true)
                    ->orderBy('order', 'asc');
    }

    /**
     * Tüm alt kategoriler (aktif/pasif fark etmez)
     */
    public function allChildren(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id', 'portfolio_category_id')
                    ->orderBy('order', 'asc');
    }

    /**
     * Sadece ana kategorileri getir (parent_id null olanlar)
     */
    public static function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Belirli bir kategorinin alt kategorilerini getir
     */
    public static function scopeChildrenOf($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    /**
     * Kategori ağacını getir (hierarchical)
     */
    public static function getTree($onlyActive = true)
    {
        $query = static::query()
            ->with(['children' => function($q) use ($onlyActive) {
                if ($onlyActive) {
                    $q->where('is_active', true);
                }
                $q->orderBy('order', 'asc');
            }])
            ->whereNull('parent_id')
            ->orderBy('order', 'asc');
            
        if ($onlyActive) {
            $query->where('is_active', true);
        }
        
        return $query->get();
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