<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prompt extends Model
{
    use HasFactory;

    protected $table = 'ai_prompts';
    protected $connection = 'mysql';

    protected $fillable = [
        'name',
        'content',
        'is_default',
        'is_system',
        'is_common',
        'is_active',
        'prompt_type',
        'priority',
        'ai_weight',
        'prompt_category',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_system' => 'boolean',
        'is_common' => 'boolean',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'ai_weight' => 'integer',
    ];

    /**
     * Varsayılan prompt'u getir
     *
     * @return self|null
     */
    public static function getDefault()
    {
        return self::where('is_default', true)->where('is_active', true)->first();
    }

    /**
     * Ortak özellikler promptunu getir
     *
     * @return self|null
     */
    public static function getCommon()
    {
        return self::where('is_common', true)->where('is_active', true)->first();
    }

    /**
     * Sistem promptlarını getir
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getSystemPrompts()
    {
        return self::where('is_system', true)->where('is_active', true)->get();
    }

    /**
     * Tip bazında prompt getir (sistem promptları için)
     *
     * @param string $type
     * @return self|null
     */
    public static function getByType($type)
    {
        return self::where('prompt_type', $type)
                   ->where('is_system', true)
                   ->where('is_active', true)
                   ->first();
    }

    /**
     * Gizli sistem promptu getir
     */
    public static function getHiddenSystem()
    {
        return self::getByType('hidden_system');
    }

    /**
     * Gizli bilgi tabanını getir
     */
    public static function getSecretKnowledge()
    {
        return self::getByType('secret_knowledge');
    }

    /**
     * Şartlı yanıtları getir
     */
    public static function getConditional()
    {
        return self::getByType('conditional');
    }

    /**
     * PRIORITY MULTIPLIERS (AIPriorityEngine uyumlu)
     */
    const PRIORITY_MULTIPLIERS = [
        1 => 1.5,   // Critical: %50 boost
        2 => 1.2,   // Important: %20 boost  
        3 => 1.0,   // Normal: No change
        4 => 0.6,   // Optional: %40 penalty
        5 => 0.3,   // Rarely used: %70 penalty
    ];

    /**
     * PROMPT CATEGORY TO BASE WEIGHT MAPPING (AIPriorityEngine)
     */
    const CATEGORY_BASE_WEIGHTS = [
        'system_common'      => 10000,  // Ortak özellikler (en yüksek)
        'system_hidden'      => 9000,   // Gizli sistem kuralları
        'feature_definition' => 8000,   // Quick prompts (feature tanımı)
        'expert_knowledge'   => 7000,   // Expert prompts (nasıl yapacak)
        'tenant_identity'    => 6000,   // Tenant profil context
        'secret_knowledge'   => 5000,   // Gizli bilgi tabanı
        'brand_context'      => 4500,   // Marka detayları
        'response_format'    => 4000,   // Response template'lar
        'conditional_info'   => 2000,   // Şartlı yanıtlar (en düşük)
    ];

    /**
     * Get final AI weight (base_weight × priority_multiplier × ai_weight)
     */
    public function getFinalAIWeight(): float
    {
        $baseWeight = self::CATEGORY_BASE_WEIGHTS[$this->prompt_category] ?? 1000;
        $multiplier = self::PRIORITY_MULTIPLIERS[$this->priority] ?? 1.0;
        $weightRatio = $this->ai_weight / 100; // Normalize to 0-1
        
        return $baseWeight * $multiplier * $weightRatio;
    }

    /**
     * Get category label
     */
    public function getCategoryLabel(): string
    {
        $labels = [
            'system_common' => 'Ortak Sistem',
            'system_hidden' => 'Gizli Sistem',
            'feature_definition' => 'Feature Tanımı',
            'expert_knowledge' => 'Uzman Bilgisi',
            'tenant_identity' => 'Tenant Kimliği',
            'secret_knowledge' => 'Gizli Bilgi',
            'brand_context' => 'Marka Context',
            'response_format' => 'Yanıt Formatı',
            'conditional_info' => 'Şartlı Bilgi'
        ];
        
        return $labels[$this->prompt_category] ?? $this->prompt_category;
    }

    /**
     * Get priority label
     */
    public function getPriorityLabel(): string
    {
        $labels = [
            1 => 'Kritik',
            2 => 'Önemli', 
            3 => 'Normal',
            4 => 'Opsiyonel',
            5 => 'Nadir'
        ];
        
        return $labels[$this->priority] ?? "Priority {$this->priority}";
    }

    /**
     * Scope: By priority level
     */
    public function scopeByPriority($query, int $maxPriority)
    {
        return $query->where('priority', '<=', $maxPriority);
    }

    /**
     * Scope: By category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('prompt_category', $category);
    }

    /**
     * Scope: Ordered by final AI weight
     */
    public function scopeByFinalWeight($query)
    {
        return $query->selectRaw('
            ai_prompts.*,
            (CASE prompt_category
                WHEN "system_common" THEN 10000
                WHEN "system_hidden" THEN 9000
                WHEN "feature_definition" THEN 8000
                WHEN "expert_knowledge" THEN 7000
                WHEN "tenant_identity" THEN 6000
                WHEN "secret_knowledge" THEN 5000
                WHEN "brand_context" THEN 4500
                WHEN "response_format" THEN 4000
                WHEN "conditional_info" THEN 2000
                ELSE 1000
            END) * 
            (CASE priority
                WHEN 1 THEN 1.5
                WHEN 2 THEN 1.2
                WHEN 3 THEN 1.0
                WHEN 4 THEN 0.6
                WHEN 5 THEN 0.3
                ELSE 1.0
            END) * 
            (ai_weight / 100) as final_weight
        ')->orderBy('final_weight', 'DESC');
    }

    /**
     * Static: Get prompts for AI context generation
     */
    public static function getForAIContext(int $maxPriority = 3): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('is_active', true)
                  ->byPriority($maxPriority)
                  ->byFinalWeight()
                  ->get();
    }

    /**
     * Konuşma ilişkisi
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Prompt'a bağlı AI feature'lar (many-to-many)
     */
    public function features()
    {
        return $this->belongsToMany(AIFeature::class, 'ai_feature_prompts', 'ai_prompt_id', 'ai_feature_id')
            ->withPivot(['prompt_role', 'priority', 'is_required', 'is_active', 'conditions', 'parameters', 'notes'])
            ->withTimestamps();
    }

    /**
     * Feature prompt pivot kayıtları
     */
    public function featurePrompts()
    {
        return $this->hasMany(AIFeaturePrompt::class, 'ai_prompt_id');
    }

    /**
     * Prompt silinebilir mi kontrol et
     */
    public function canBeDeleted(): bool
    {
        // Sistem promptları sadece düzenlenebilir, silinemez
        if ($this->is_system || $this->is_common || $this->is_default) {
            return false;
        }
        
        // Herhangi bir feature'a bağlıysa silinemez
        return $this->features()->count() === 0;
    }

    /**
     * Prompt türünü Türkçe al
     */
    public function getTypeName(): string
    {
        $types = [
            'standard' => 'Standart',
            'common' => 'Ortak Özellikler',
            'hidden_system' => 'Gizli Sistem',
            'secret_knowledge' => 'Gizli Bilgi',
            'conditional' => 'Şartlı Yanıt'
        ];

        return $types[$this->prompt_type] ?? 'Bilinmeyen';
    }
}