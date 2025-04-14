<?php
// Modules/Portfolio/App/Models/PortfolioCategory.php
namespace Modules\Portfolio\App\Models;

use App\Models\BaseModel;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortfolioCategory extends BaseModel 
{
    use Sluggable, SoftDeletes;

    protected $primaryKey = 'portfolio_category_id';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'order',
        'metakey',
        'metadesc',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Sluggable Ayarları
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'onUpdate' => true,
                'unique' => true,
                'includeTrashed' => true,
            ],
        ];
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class, 'portfolio_category_id', 'portfolio_category_id');
    }
}