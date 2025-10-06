<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Cviebrock\EloquentSluggable\Sluggable;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class AIFeatureCategory extends Model
{
    protected $connection = 'central';
    use Sluggable, CentralConnection;

    protected $table = 'ai_feature_categories';
    protected $primaryKey = 'ai_feature_category_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'order',
        'icon',
        'is_active',
        'parent_id',
        'has_subcategories',
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
            ],
        ];
    }

    public function aiFeatures(): HasMany
    {
        return $this->hasMany(AIFeature::class, 'ai_feature_category_id', 'ai_feature_category_id');
    }
    
    /**
     * Üst kategori
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(AIFeatureCategory::class, 'parent_id', 'ai_feature_category_id');
    }
    
    /**
     * Alt kategoriler
     */
    public function children(): HasMany
    {
        return $this->hasMany(AIFeatureCategory::class, 'parent_id', 'ai_feature_category_id')
            ->withCount('aiFeatures')
            ->orderBy('order');
    }
    
    /**
     * Tüm alt kategorileri ve bu kategorinin tüm AI feature'larını getirir
     */
    public function allAIFeatures()
    {
        $featureIds = $this->aiFeatures->pluck('ai_feature_id')->toArray();
        
        // Alt kategorilerin feature'larını da ekle
        foreach ($this->children as $child) {
            $childFeatureIds = $child->aiFeatures->pluck('ai_feature_id')->toArray();
            $featureIds = array_merge($featureIds, $childFeatureIds);
        }
        
        return AIFeature::whereIn('ai_feature_id', $featureIds)->get();
    }
}