<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIFeaturePromptRelation extends Model
{
    protected $connection = 'central';
    protected $table = 'ai_feature_prompt_relations';
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
    }

    protected $fillable = [
        'feature_id',
        'prompt_id',
        'feature_prompt_id',
        'priority',
        'role',
        'is_active',
        'conditions',
        'notes',
        'category_context',
        'feature_type_filter',
        'business_rules'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'conditions' => 'array',
        'category_context' => 'array',
        'business_rules' => 'array',
        'priority' => 'integer'
    ];

    /**
     * İlgili feature
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(AIFeature::class, 'feature_id');
    }

    /**
     * İlgili prompt
     */
    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class, 'prompt_id');
    }

    /**
     * İlgili feature prompt (eğer varsa)
     */
    public function featurePrompt(): BelongsTo
    {
        return $this->belongsTo(AIFeaturePrompt::class, 'feature_prompt_id');
    }
}