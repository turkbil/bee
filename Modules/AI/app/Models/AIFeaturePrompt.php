<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIFeaturePrompt extends Model
{
    protected $table = 'ai_feature_prompts';

    protected $fillable = [
        'feature_id',
        'prompt_id',
        'role',
        'priority',
        'is_active',
        'conditions',
        'notes'
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer'
    ];

    /**
     * İlişki: AI Feature
     */
    public function aiFeature(): BelongsTo
    {
        return $this->belongsTo(AIFeature::class, 'feature_id');
    }

    /**
     * İlişki: AI Prompt
     */
    public function aiPrompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class, 'prompt_id');
    }

    /**
     * Prompt role'ünü Türkçe al
     */
    public function getRoleName(): string
    {
        $roles = [
            'primary' => 'Ana Prompt',
            'secondary' => 'İkincil Prompt',
            'hidden' => 'Gizli Sistem',
            'conditional' => 'Şartlı Prompt',
            'formatting' => 'Format Düzenleme',
            'validation' => 'Doğrulama'
        ];

        return $roles[$this->role] ?? 'Bilinmeyen';
    }

    /**
     * Aktif mi kontrol et
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->aiFeature->status === 'active';
    }

    /**
     * Şartları kontrol et
     */
    public function checkConditions(array $context = []): bool
    {
        if (!$this->conditions || empty($this->conditions)) {
            return true; // Şart yoksa her zaman çalış
        }

        // Burada şart kontrolü yapılabilir
        // Örnek: ['user_type' => 'pro', 'category' => 'technical']
        foreach ($this->conditions as $key => $value) {
            if (!isset($context[$key]) || $context[$key] !== $value) {
                return false;
            }
        }

        return true;
    }
}