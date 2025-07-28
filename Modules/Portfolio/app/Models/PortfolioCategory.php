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
}