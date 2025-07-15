<?php

namespace Modules\AI\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIProfileQuestion extends Model
{
    protected $table = 'ai_profile_questions';
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // AI tabloları her zaman central database'de
        $this->setConnection('mysql');
    }

    protected $fillable = [
        'sector_code',
        'step',
        'section',
        'question_key',
        'question_text',
        'help_text',
        'input_type',
        'options',
        'validation_rules',
        'depends_on',
        'show_if',
        'is_required',
        'is_active',
        'sort_order',
        'priority',
        'ai_weight',
        'category'
    ];

    protected $casts = [
        'validation_rules' => 'array',
        'show_if' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'ai_weight' => 'integer'
    ];

    /**
     * İlişkili sektör
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(AIProfileSector::class, 'sector_code', 'code');
    }

    /**
     * Belirli bir adım için soruları getir
     */
    public static function getByStep(int $step, ?string $sectorCode = null)
    {
        $query = static::where('is_active', true)
                       ->where('step', $step);
        
        // Step 4 için hem company_info hem founder_info section sorularını getir
        if ($step === 4) {
            $query->where(function($q) {
                $q->where('section', 'company_info')
                  ->orWhere('section', 'founder_info')
                  ->orWhereNull('section');
            });
        }
        
        if ($sectorCode) {
            // Hem genel hem de sektöre özel soruları getir
            $query->where(function($q) use ($sectorCode) {
                $q->whereNull('sector_code')
                  ->orWhere('sector_code', $sectorCode);
            });
            
            // Sektöre özel sorular önce, sonra genel sorular (sort_order'a göre)
            return $query->orderByRaw('CASE WHEN sector_code IS NOT NULL THEN 0 ELSE 1 END')
                        ->orderBy('sort_order')
                        ->get();
        } else {
            // Sadece genel soruları getir
            $query->whereNull('sector_code');
            return $query->orderBy('sort_order')->get();
        }
    }

    /**
     * Checkbox ile aktif edilen bölüm soruları
     */
    public static function getOptionalSectionQuestions(string $section, ?string $sectorCode = null, ?int $step = null)
    {
        $query = static::where('is_active', true)
                       ->where('section', $section);
        
        if ($step) {
            $query->where('step', $step);
        }
        
        if ($sectorCode) {
            $query->where(function($q) use ($sectorCode) {
                $q->whereNull('sector_code')
                  ->orWhere('sector_code', $sectorCode);
            });
        }
        
        return $query->orderBy('sort_order')->get();
    }

    /**
     * Validation kurallarını Laravel formatına çevir
     */
    public function getLaravelValidationRules(): string
    {
        if (!$this->validation_rules) {
            return $this->is_required ? 'required' : 'nullable';
        }
        
        // Validation rules array olmalı, eğer string ise JSON decode et
        $rules = $this->validation_rules;
        if (is_string($rules)) {
            $rules = json_decode($rules, true) ?? [];
        }
        
        // Array olmadığı durumda güvenli fallback
        if (!is_array($rules)) {
            $rules = [];
        }
        
        if ($this->is_required && !in_array('required', $rules)) {
            array_unshift($rules, 'required');
        }
        
        return implode('|', $rules);
    }


    /**
     * Options attribute accessor - JSON decode edilmiş array döndürür
     */
    public function getOptionsAttribute($value)
    {
        if (!$value) {
            return [];
        }
        
        // If already an array, return as is
        if (is_array($value)) {
            return $value;
        }
        
        // First JSON decode - escaped string'i decode et
        $firstDecode = json_decode($value, true);
        
        // If first decode returns string, try second decode
        if (is_string($firstDecode)) {
            $secondDecode = json_decode($firstDecode, true);
            if (is_array($secondDecode)) {
                return $secondDecode;
            }
        }
        
        // If first decode returns array, return it
        if (is_array($firstDecode)) {
            return $firstDecode;
        }
        
        return [];
    }

    /**
     * Select/checkbox seçeneklerini formatla
     */
    public function getFormattedOptions(): array
    {
        $options = $this->options;
        if (!is_array($options)) {
            return [];
        }
        
        $formatted = [];
        foreach ($options as $option) {
            if (is_string($option)) {
                $formatted[$option] = $option;
            } elseif (is_array($option) && isset($option['value'], $option['label'])) {
                $formatted[$option['value']] = $option['label'];
            }
        }
        
        return $formatted;
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
     * CATEGORY LABELS
     */
    const CATEGORY_LABELS = [
        'company' => 'Firma Bilgileri',
        'sector' => 'Sektör Bilgileri', 
        'ai' => 'AI Davranış Kuralları',
        'founder' => 'Kurucu Bilgileri'
    ];

    /**
     * Get final AI weight (weight × multiplier)
     */
    public function getFinalAIWeight(): float
    {
        $multiplier = self::PRIORITY_MULTIPLIERS[$this->priority] ?? 1.0;
        return $this->ai_weight * $multiplier;
    }

    /**
     * Get category label in Turkish
     */
    public function getCategoryLabel(): string
    {
        return self::CATEGORY_LABELS[$this->category] ?? $this->category;
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
        return $query->where('category', $category);
    }

    /**
     * Scope: Ordered by AI weight (for context generation)
     */
    public function scopeByAIWeight($query)
    {
        return $query->orderByRaw('(ai_weight * CASE 
            WHEN priority = 1 THEN 1.5
            WHEN priority = 2 THEN 1.2
            WHEN priority = 3 THEN 1.0
            WHEN priority = 4 THEN 0.6
            WHEN priority = 5 THEN 0.3
            ELSE 1.0 END) DESC');
    }

    /**
     * Static: Get fields for AI context generation
     */
    public static function getForAIContext(int $maxPriority = 3): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('is_active', true)
                  ->byPriority($maxPriority)
                  ->byAIWeight()
                  ->get();
    }
}