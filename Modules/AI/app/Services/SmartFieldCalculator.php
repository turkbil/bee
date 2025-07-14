<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

/**
 * SMART FIELD CALCULATOR - ADVANCED DYNAMIC SCORING
 * 
 * Bu servis AI profil field'larını akıllıca hesaplar:
 * - Dynamic priority calculation
 * - Context-aware scoring
 * - Smart weight adjustment
 * - Real-time score updates
 */
class SmartFieldCalculator
{
    /**
     * Dynamic priority multipliers - Context-aware
     */
    public const PRIORITY_MULTIPLIERS = [
        1 => 1.5,   // Critical: %50 boost
        2 => 1.2,   // Important: %20 boost
        3 => 1.0,   // Normal: No change
        4 => 0.6,   // Optional: %40 penalty
        5 => 0.3,   // Rarely: %70 penalty
    ];

    /**
     * Context-based weight adjustments
     */
    public const CONTEXT_ADJUSTMENTS = [
        'minimal' => [
            'company' => 1.2,    // Company info more important in minimal
            'founder' => 0.5,    // Founder info less important
            'ai' => 1.0,
            'sector' => 0.8
        ],
        'normal' => [
            'company' => 1.0,
            'founder' => 1.0,
            'ai' => 1.0,
            'sector' => 1.0
        ],
        'detailed' => [
            'company' => 0.9,
            'founder' => 1.3,    // More founder details in detailed
            'ai' => 1.1,
            'sector' => 1.2
        ],
        'local_business' => [
            'company' => 1.5,    // City very important for local
            'founder' => 1.1,
            'ai' => 0.9,
            'sector' => 1.2
        ],
        'seo_focused' => [
            'company' => 1.3,    // Brand name critical for SEO
            'founder' => 0.7,
            'ai' => 1.4,         // AI behavior important for content
            'sector' => 1.2
        ]
    ];

    /**
     * Field-specific boost rules
     */
    public const FIELD_BOOST_RULES = [
        'brand_name' => [
            'always_critical' => true,
            'min_score' => 10000,
            'context_independent' => true
        ],
        'city' => [
            'context_dependent' => true,
            'boost_contexts' => ['local_business', 'detailed'],
            'penalty_contexts' => ['minimal', 'seo_focused']
        ],
        'founder_name' => [
            'conditional_boost' => [
                'if_field' => 'share_founder_info',
                'if_value' => ['yes_full', 'yes_selective'],
                'boost_multiplier' => 1.4
            ]
        ],
        'writing_style' => [
            'ai_behavior_critical' => true,
            'min_score' => 8000
        ]
    ];

    /**
     * Calculate dynamic field score
     *
     * @param array $fieldData Field definition
     * @param mixed $fieldValue User's answer
     * @param string $context Current context
     * @param array $allFieldValues All user answers (for conditional logic)
     * @return array Calculation result
     */
    public function calculateFieldScore(
        array $fieldData, 
        $fieldValue, 
        string $context = 'normal',
        array $allFieldValues = []
    ): array {
        $startTime = microtime(true);
        
        // 1. Base score calculation
        $baseScore = $this->calculateBaseScore($fieldData, $fieldValue);
        
        // 2. Apply priority multiplier
        $priorityScore = $this->applyPriorityMultiplier($baseScore, $fieldData['base_priority'] ?? 3);
        
        // 3. Apply context adjustments
        $contextScore = $this->applyContextAdjustments(
            $priorityScore, 
            $fieldData['field_category'] ?? 'company', 
            $context
        );
        
        // 4. Apply field-specific boosts/penalties
        $finalScore = $this->applyFieldSpecificRules(
            $contextScore,
            $fieldData['field_key'] ?? '',
            $fieldValue,
            $allFieldValues,
            $context
        );
        
        // 5. Calculate metadata
        $metadata = $this->buildCalculationMetadata($fieldData, $baseScore, $finalScore, $context);
        
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        
        return [
            'final_score' => intval($finalScore),
            'base_score' => intval($baseScore),
            'priority_score' => intval($priorityScore),
            'context_score' => intval($contextScore),
            'calculation_metadata' => $metadata,
            'execution_time_ms' => $executionTime,
            'calculated_at' => now(),
            'context_used' => $context
        ];
    }

    /**
     * Calculate base score from field weight and value quality
     */
    private function calculateBaseScore(array $fieldData, $fieldValue): float
    {
        $baseWeight = $fieldData['base_ai_weight'] ?? 50;
        $valueQuality = $this->assessValueQuality($fieldValue, $fieldData);
        
        return $baseWeight * $valueQuality;
    }

    /**
     * Assess quality of user's answer
     */
    private function assessValueQuality($value, array $fieldData): float
    {
        if (empty($value)) {
            return 0.0;
        }

        $inputType = $fieldData['input_type'] ?? 'text';
        
        switch ($inputType) {
            case 'text':
            case 'textarea':
                return $this->assessTextQuality($value);
                
            case 'select':
                return 1.0; // Full score for valid selection
                
            case 'checkbox':
            case 'multi_select':
                return $this->assessMultiValueQuality($value);
                
            case 'radio':
                return 1.0; // Full score for valid selection
                
            default:
                return 0.8; // Default quality
        }
    }

    /**
     * Assess text answer quality
     */
    private function assessTextQuality($text): float
    {
        if (!is_string($text)) {
            return 0.5;
        }

        $length = strlen(trim($text));
        
        if ($length === 0) return 0.0;
        if ($length < 5) return 0.4;      // Very short
        if ($length < 15) return 0.7;     // Short
        if ($length < 50) return 0.9;     // Good
        if ($length < 200) return 1.0;    // Excellent
        
        return 0.95; // Very long (might be too verbose)
    }

    /**
     * Assess multi-value answer quality
     */
    private function assessMultiValueQuality($values): float
    {
        if (!is_array($values)) {
            return is_string($values) ? 1.0 : 0.5;
        }

        $count = count(array_filter($values));
        
        if ($count === 0) return 0.0;
        if ($count === 1) return 0.8;     // Single selection
        if ($count <= 3) return 1.0;      // Good variety
        if ($count <= 5) return 0.95;     // Comprehensive
        
        return 0.8; // Too many selections (might be scattered)
    }

    /**
     * Apply priority multiplier
     */
    private function applyPriorityMultiplier(float $score, int $priority): float
    {
        $multiplier = self::PRIORITY_MULTIPLIERS[$priority] ?? 1.0;
        return $score * $multiplier;
    }

    /**
     * Apply context-based adjustments
     */
    private function applyContextAdjustments(float $score, string $category, string $context): float
    {
        $adjustments = self::CONTEXT_ADJUSTMENTS[$context] ?? self::CONTEXT_ADJUSTMENTS['normal'];
        $categoryMultiplier = $adjustments[$category] ?? 1.0;
        
        return $score * $categoryMultiplier;
    }

    /**
     * Apply field-specific boost/penalty rules
     */
    private function applyFieldSpecificRules(
        float $score,
        string $fieldKey,
        $fieldValue,
        array $allValues,
        string $context
    ): float {
        $rules = self::FIELD_BOOST_RULES[$fieldKey] ?? [];
        
        if (empty($rules)) {
            return $score;
        }

        // Always critical fields
        if (!empty($rules['always_critical'])) {
            $minScore = $rules['min_score'] ?? 10000;
            $score = max($score, $minScore);
        }

        // Context-dependent boost/penalty
        if (!empty($rules['context_dependent'])) {
            if (!empty($rules['boost_contexts']) && in_array($context, $rules['boost_contexts'])) {
                $score *= 1.5; // Boost for relevant contexts
            }
            if (!empty($rules['penalty_contexts']) && in_array($context, $rules['penalty_contexts'])) {
                $score *= 0.6; // Penalty for irrelevant contexts
            }
        }

        // Conditional boost based on other fields
        if (!empty($rules['conditional_boost'])) {
            $condition = $rules['conditional_boost'];
            $dependentField = $condition['if_field'];
            $expectedValues = (array) $condition['if_value'];
            $dependentValue = $allValues[$dependentField] ?? null;
            
            if (in_array($dependentValue, $expectedValues)) {
                $boostMultiplier = $condition['boost_multiplier'] ?? 1.2;
                $score *= $boostMultiplier;
            }
        }

        return $score;
    }

    /**
     * Build detailed calculation metadata
     */
    private function buildCalculationMetadata(array $fieldData, float $baseScore, float $finalScore, string $context): array
    {
        return [
            'field_key' => $fieldData['field_key'] ?? '',
            'field_category' => $fieldData['field_category'] ?? '',
            'base_priority' => $fieldData['base_priority'] ?? 3,
            'base_ai_weight' => $fieldData['base_ai_weight'] ?? 50,
            'score_progression' => [
                'base' => intval($baseScore),
                'after_priority' => intval($baseScore * (self::PRIORITY_MULTIPLIERS[$fieldData['base_priority'] ?? 3] ?? 1.0)),
                'final' => intval($finalScore)
            ],
            'applied_multipliers' => [
                'priority' => self::PRIORITY_MULTIPLIERS[$fieldData['base_priority'] ?? 3] ?? 1.0,
                'context' => $context,
                'category_adjustment' => self::CONTEXT_ADJUSTMENTS[$context][$fieldData['field_category'] ?? 'company'] ?? 1.0
            ],
            'calculation_context' => $context,
            'quality_assessment' => round($baseScore / ($fieldData['base_ai_weight'] ?? 50), 2)
        ];
    }

    /**
     * Calculate profile completeness score
     */
    public function calculateProfileCompleteness(array $allFieldScores, string $context = 'normal'): array
    {
        if (empty($allFieldScores)) {
            return [
                'completeness_percentage' => 0.0,
                'total_possible_score' => 0,
                'current_score' => 0,
                'missing_critical_fields' => 0,
                'quality_grade' => 'F'
            ];
        }

        $totalScore = array_sum(array_column($allFieldScores, 'final_score'));
        $maxPossibleScore = $this->calculateMaxPossibleScore($context);
        $completeness = ($totalScore / $maxPossibleScore) * 100;
        
        return [
            'completeness_percentage' => round(min($completeness, 100), 1),
            'total_possible_score' => $maxPossibleScore,
            'current_score' => $totalScore,
            'missing_critical_fields' => $this->countMissingCriticalFields($allFieldScores),
            'quality_grade' => $this->calculateQualityGrade($completeness),
            'context_used' => $context
        ];
    }

    /**
     * Calculate maximum possible score for context
     */
    private function calculateMaxPossibleScore(string $context): int
    {
        // This would be calculated based on all available fields
        // For now, return a reasonable estimate
        return match($context) {
            'minimal' => 50000,
            'normal' => 100000,
            'detailed' => 150000,
            'local_business' => 120000,
            'seo_focused' => 130000,
            default => 100000
        };
    }

    /**
     * Count missing critical fields
     */
    private function countMissingCriticalFields(array $fieldScores): int
    {
        $criticalFields = ['brand_name', 'brand_character', 'writing_style', 'sector_selection'];
        $presentFields = array_keys($fieldScores);
        
        return count(array_diff($criticalFields, $presentFields));
    }

    /**
     * Calculate quality grade
     */
    private function calculateQualityGrade(float $completeness): string
    {
        return match(true) {
            $completeness >= 90 => 'A+',
            $completeness >= 85 => 'A',
            $completeness >= 80 => 'A-',
            $completeness >= 75 => 'B+',
            $completeness >= 70 => 'B',
            $completeness >= 65 => 'B-',
            $completeness >= 60 => 'C+',
            $completeness >= 55 => 'C',
            $completeness >= 50 => 'C-',
            $completeness >= 40 => 'D',
            default => 'F'
        };
    }

    /**
     * Generate smart recommendations
     */
    public function generateRecommendations(array $fieldScores, string $context): array
    {
        $recommendations = [];
        
        // Check for missing critical fields
        $missingCritical = $this->countMissingCriticalFields($fieldScores);
        if ($missingCritical > 0) {
            $recommendations[] = [
                'type' => 'critical',
                'message' => "Complete {$missingCritical} critical fields for better AI performance",
                'action' => 'complete_critical_fields'
            ];
        }

        // Check for low-quality answers
        $lowQualityFields = array_filter($fieldScores, function($score) {
            $metadata = $score['calculation_metadata'] ?? [];
            return ($metadata['quality_assessment'] ?? 1.0) < 0.5;
        });

        if (count($lowQualityFields) > 0) {
            $recommendations[] = [
                'type' => 'quality',
                'message' => 'Improve ' . count($lowQualityFields) . ' field answers for better results',
                'action' => 'improve_field_quality',
                'fields' => array_keys($lowQualityFields)
            ];
        }

        return $recommendations;
    }
}