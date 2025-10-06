<?php

declare(strict_types=1);

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIFeatureInput extends Model
{
    protected $connection = 'central';
    protected $table = 'ai_feature_inputs';
    
    protected $fillable = [
        'feature_id',
        'name',
        'slug', 
        'type',
        'placeholder',
        'help_text',
        'is_primary',
        'group_id',
        'sort_order',
        'is_required',
        'validation_rules',
        'default_value',
        'prompt_placeholder',
        'config',
        'conditional_logic',
        'dynamic_data_source_id'
    ];
    
    protected $casts = [
        'validation_rules' => 'array',
        'config' => 'array', 
        'conditional_logic' => 'array',
        'is_primary' => 'boolean',
        'is_required' => 'boolean'
    ];
    
    /**
     * AI Feature relationship
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(AIFeature::class, 'feature_id');
    }
    
    /**
     * Input options relationship
     */
    public function options(): HasMany
    {
        return $this->hasMany(AIInputOption::class, 'input_id')->orderBy('sort_order');
    }
    
    /**
     * Dynamic data source relationship
     */
    public function dynamicDataSource(): BelongsTo
    {
        return $this->belongsTo(AIDynamicDataSource::class, 'dynamic_data_source_id');
    }
    
    /**
     * Alias for dynamicDataSource (for compatibility)
     */
    public function dynamicSource(): BelongsTo
    {
        return $this->dynamicDataSource();
    }
    
    /**
     * Input group relationship
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(AIInputGroup::class, 'group_id');
    }
    
    /**
     * Scope for ordered inputs
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
    
    /**
     * Scope for primary input
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
    
    /**
     * Scope for required inputs
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }
    
    /**
     * Check if input has conditional logic
     */
    public function hasConditionalLogic(): bool
    {
        return !empty($this->conditional_logic);
    }
    
    /**
     * Get formatted validation rules for frontend
     */
    public function getFormattedValidationRules(): array
    {
        return $this->validation_rules ?? [];
    }
    
    /**
     * Get config value with default fallback
     */
    public function getConfigValue(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }
}