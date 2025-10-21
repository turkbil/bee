<?php

declare(strict_types=1);

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIInputGroup extends Model
{
    protected $connection = 'mysql'; // Central DB for all tenants
    protected $table = 'ai_input_groups';
    
    protected $fillable = [
        'feature_id',
        'name',
        'slug',
        'description',
        'is_collapsible',
        'is_expanded',
        'sort_order'
    ];
    
    protected $casts = [
        'is_collapsible' => 'boolean',
        'is_expanded' => 'boolean'
    ];
    
    /**
     * Feature relationship
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(AIFeature::class, 'feature_id');
    }
    
    /**
     * Inputs in this group
     */
    public function inputs(): HasMany
    {
        return $this->hasMany(AIFeatureInput::class, 'group_id')->orderBy('sort_order');
    }
    
    /**
     * Scope for ordered groups
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
    
    /**
     * Scope for collapsible groups
     */
    public function scopeCollapsible($query)
    {
        return $query->where('is_collapsible', true);
    }
    
    /**
     * Scope for expanded groups
     */
    public function scopeExpanded($query)
    {
        return $query->where('is_expanded', true);
    }
    
    /**
     * Check if group has inputs
     */
    public function hasInputs(): bool
    {
        return $this->inputs()->count() > 0;
    }
    
    /**
     * Get icon class based on slug
     */
    public function getIconClass(): string
    {
        return match($this->slug) {
            'blog-ayarlari' => 'ti ti-edit',
            'seo-ayarlari' => 'ti ti-search',
            'platform-ayarlari' => 'ti ti-brand-twitter',
            'icerik-stili' => 'ti ti-palette',
            'email-tipi' => 'ti ti-mail',
            'kisisellestime' => 'ti ti-user',
            default => 'ti ti-folder'
        };
    }
    
    /**
     * Check if group should be collapsed by default
     */
    public function shouldBeCollapsed(): bool
    {
        return $this->is_collapsible && !$this->is_expanded;
    }
}