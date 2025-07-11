<?php

namespace Modules\AI\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIProfileQuestion extends Model
{
    protected $table = 'ai_profile_questions';

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
        'sort_order'
    ];

    protected $casts = [
        'validation_rules' => 'array',
        'show_if' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean'
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
        
        // Step 4 için sadece company_info section sorularını getir (founder_info hariç)
        if ($step === 4) {
            $query->where(function($q) {
                $q->where('section', 'company_info')
                  ->orWhereNull('section');
            });
        }
        
        if ($sectorCode) {
            // Hem genel hem de sektöre özel soruları getir
            $query->where(function($q) use ($sectorCode) {
                $q->whereNull('sector_code')
                  ->orWhere('sector_code', $sectorCode);
            });
        } else {
            // Sadece genel soruları getir
            $query->whereNull('sector_code');
        }
        
        return $query->orderBy('sort_order')->get();
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
        
        $rules = $this->validation_rules;
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
}