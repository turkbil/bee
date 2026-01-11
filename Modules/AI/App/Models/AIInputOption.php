<?php

declare(strict_types=1);

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIInputOption extends Model
{
    protected $connection = 'mysql'; // Central DB for all tenants
    protected $table = 'ai_input_options';
    
    protected $fillable = [
        'input_id',
        'label',
        'value',
        'prompt_value',
        'sort_order',
        'is_default',
        'conditions'
    ];
    
    protected $casts = [
        'conditions' => 'array',
        'is_default' => 'boolean'
    ];
    
    /**
     * Input relationship
     */
    public function input(): BelongsTo
    {
        return $this->belongsTo(AIFeatureInput::class, 'input_id');
    }
    
    /**
     * Scope for ordered options
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
    
    /**
     * Scope for default options
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
    
    /**
     * Check if option has conditions
     */
    public function hasConditions(): bool
    {
        return !empty($this->conditions);
    }
    
    /**
     * Check if option meets conditions based on user inputs
     */
    public function meetsConditions(array $userInputs): bool
    {
        if (!$this->hasConditions()) {
            return true;
        }
        
        foreach ($this->conditions as $key => $expectedValue) {
            if (!isset($userInputs[$key]) || $userInputs[$key] !== $expectedValue) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get the prompt value or fallback to label
     */
    public function getPromptText(): string
    {
        return $this->prompt_value ?? $this->label;
    }
}