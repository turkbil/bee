<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Models\AITenantProfile;
use Modules\AI\App\Models\AIProfileQuestion;
use Modules\AI\App\Services\SmartFieldCalculator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * SMART PROFILE BUILDER - CONTEXT-AWARE PROFILE GENERATION
 * 
 * Bu servis AI profilleri akıllıca oluşturur ve yönetir:
 * - Dynamic scoring calculation
 * - Context-aware profile building
 * - Smart recommendations
 * - Real-time analytics
 */
class SmartProfileBuilder
{
    protected SmartFieldCalculator $calculator;

    public function __construct(SmartFieldCalculator $calculator = null)
    {
        $this->calculator = $calculator ?? new SmartFieldCalculator();
    }

    /**
     * Build smart profile from user responses
     *
     * @param int $tenantId
     * @param array $userResponses Raw user responses
     * @param string $context Context for scoring
     * @return array Smart profile data
     */
    public function buildSmartProfile(int $tenantId, array $userResponses, string $context = 'normal'): array
    {
        Log::info('🚀 Smart Profile Builder başlatıldı', [
            'tenant_id' => $tenantId,
            'response_count' => count($userResponses),
            'context' => $context
        ]);

        $startTime = microtime(true);

        // 1. Get field definitions with priority data
        $fieldDefinitions = $this->getFieldDefinitions();
        
        // 2. Calculate smart scores for each field
        $fieldScores = $this->calculateAllFieldScores($fieldDefinitions, $userResponses, $context);
        
        // 3. Build advanced JSON structure
        $smartProfileData = $this->buildAdvancedProfileStructure($userResponses, $fieldScores, $context);
        
        // 4. Calculate completeness and quality
        $completeness = $this->calculator->calculateProfileCompleteness($fieldScores, $context);
        
        // 5. Generate smart recommendations
        $recommendations = $this->calculator->generateRecommendations($fieldScores, $context);
        
        // 6. Build final smart profile
        $smartProfile = [
            // Original data (legacy compatibility)
            'legacy_data' => $this->buildLegacyData($userResponses),
            
            // Smart scoring system
            'smart_field_scores' => $fieldScores,
            'field_calculation_metadata' => [
                'calculation_context' => $context,
                'calculated_at' => now(),
                'execution_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                'field_count' => count($fieldScores),
                'calculator_version' => '2.0'
            ],
            
            // Quality metrics
            'profile_completeness_score' => $completeness['completeness_percentage'],
            'profile_quality_grade' => $completeness['quality_grade'],
            'missing_critical_fields' => $completeness['missing_critical_fields'],
            
            // Context tracking
            'last_calculation_context' => $context,
            'scores_calculated_at' => now(),
            'context_performance' => [
                $context => [
                    'score' => $completeness['current_score'],
                    'max_possible' => $completeness['total_possible_score'],
                    'percentage' => $completeness['completeness_percentage'],
                    'calculated_at' => now()
                ]
            ],
            
            // Smart recommendations
            'ai_recommendations' => $recommendations,
            'field_quality_analysis' => $this->analyzeFieldQuality($fieldScores),
            
            // Profile metadata
            'profile_version' => $this->calculateProfileVersion($tenantId),
            'auto_optimization_enabled' => true,
            'is_completed' => $completeness['completeness_percentage'] >= 70,
            'is_active' => true
        ];

        Log::info('✅ Smart Profile Builder tamamlandı', [
            'tenant_id' => $tenantId,
            'completeness' => $completeness['completeness_percentage'] . '%',
            'quality_grade' => $completeness['quality_grade'],
            'execution_time_ms' => $smartProfile['field_calculation_metadata']['execution_time_ms']
        ]);

        return $smartProfile;
    }

    /**
     * Get field definitions with priority data
     */
    private function getFieldDefinitions(): array
    {
        return Cache::remember('smart_field_definitions', 3600, function () {
            $questions = AIProfileQuestion::where('is_active', true)
                ->orderBy('ai_priority', 'asc')
                ->get();

            $definitions = [];
            foreach ($questions as $question) {
                $definitions[$question->question_key] = [
                    'field_key' => $question->question_key,
                    'field_name' => $question->question_text,
                    'field_description' => $question->help_text,
                    'base_priority' => $question->ai_priority ?? 3,
                    'base_ai_weight' => $question->ai_weight ?? 50,
                    'field_category' => $question->category ?? 'company',
                    'input_type' => $question->input_type,
                    'is_required' => $question->is_required ?? false,
                    'validation_rules' => $question->validation_rules ?? [],
                    'options' => $question->options ?? []
                ];
            }

            return $definitions;
        });
    }

    /**
     * Calculate scores for all fields
     */
    private function calculateAllFieldScores(array $fieldDefinitions, array $userResponses, string $context): array
    {
        $fieldScores = [];

        foreach ($userResponses as $fieldKey => $fieldValue) {
            if (!isset($fieldDefinitions[$fieldKey])) {
                // Skip unknown fields
                continue;
            }

            $fieldData = $fieldDefinitions[$fieldKey];
            $calculation = $this->calculator->calculateFieldScore(
                $fieldData,
                $fieldValue,
                $context,
                $userResponses
            );

            $fieldScores[$fieldKey] = $calculation;
        }

        return $fieldScores;
    }

    /**
     * Build advanced JSON profile structure
     */
    private function buildAdvancedProfileStructure(array $userResponses, array $fieldScores, string $context): array
    {
        $advancedStructure = [];

        foreach ($userResponses as $fieldKey => $fieldValue) {
            $scoreData = $fieldScores[$fieldKey] ?? null;
            
            $advancedStructure[$fieldKey] = [
                'value' => $fieldValue,
                'processed_value' => $this->processFieldValue($fieldValue, $fieldKey),
                'score_data' => $scoreData,
                'context_relevance' => $this->calculateContextRelevance($fieldKey, $context),
                'last_updated' => now(),
                'quality_score' => $scoreData ? 
                    $scoreData['calculation_metadata']['quality_assessment'] ?? 0.0 : 0.0
            ];
        }

        return $advancedStructure;
    }

    /**
     * Process field value (normalize, clean, etc.)
     */
    private function processFieldValue($value, string $fieldKey)
    {
        if (is_string($value)) {
            return trim($value);
        }

        if (is_array($value)) {
            // For checkbox/multi-select, extract selected keys
            $selected = array_keys(array_filter($value, function($v) {
                return $v === true || $v === 'true' || $v === 1;
            }));
            return $selected;
        }

        return $value;
    }

    /**
     * Calculate context relevance for field
     */
    private function calculateContextRelevance(string $fieldKey, string $context): float
    {
        $relevanceMap = [
            'minimal' => [
                'brand_name' => 1.0,
                'brand_character' => 1.0,
                'writing_style' => 1.0,
                'city' => 0.3,
                'founder_story' => 0.2
            ],
            'normal' => [
                'brand_name' => 1.0,
                'brand_character' => 1.0,
                'writing_style' => 1.0,
                'city' => 0.6,
                'founder_story' => 0.7
            ],
            'detailed' => [
                'brand_name' => 1.0,
                'brand_character' => 1.0,
                'writing_style' => 1.0,
                'city' => 0.8,
                'founder_story' => 1.0
            ],
            'local_business' => [
                'brand_name' => 1.0,
                'city' => 1.0,
                'founder_story' => 0.9,
                'target_customers' => 1.0
            ],
            'seo_focused' => [
                'brand_name' => 1.0,
                'writing_style' => 1.0,
                'brand_character' => 1.0,
                'city' => 0.4
            ]
        ];

        return $relevanceMap[$context][$fieldKey] ?? 0.8;
    }

    /**
     * Build legacy data for backward compatibility
     */
    private function buildLegacyData(array $userResponses): array
    {
        // Convert new flat structure to old nested JSON
        $legacy = [
            'company_info' => [],
            'sector_details' => [],
            'founder_info' => [],
            'ai_behavior_rules' => [],
            'additional_info' => []
        ];

        // Map fields to legacy structure
        $fieldMapping = [
            'brand_name' => ['company_info', 'brand_name'],
            'city' => ['company_info', 'city'],
            'main_service' => ['company_info', 'main_service'],
            'sector_selection' => ['sector_details', 'sector'],
            'market_position' => ['sector_details', 'market_position'],
            'founder_name' => ['founder_info', 'founder_name'],
            'founder_position' => ['founder_info', 'founder_role'],
            'brand_character' => ['ai_behavior_rules', 'brand_voice'],
            'writing_style' => ['ai_behavior_rules', 'writing_tone']
        ];

        foreach ($userResponses as $fieldKey => $fieldValue) {
            if (isset($fieldMapping[$fieldKey])) {
                [$section, $key] = $fieldMapping[$fieldKey];
                $legacy[$section][$key] = $fieldValue;
            }
        }

        return $legacy;
    }

    /**
     * Analyze field quality
     */
    private function analyzeFieldQuality(array $fieldScores): array
    {
        $analysis = [
            'excellent_fields' => [],
            'good_fields' => [],
            'poor_fields' => [],
            'missing_fields' => [],
            'overall_quality' => 0.0
        ];

        $totalQuality = 0;
        $fieldCount = 0;

        foreach ($fieldScores as $fieldKey => $scoreData) {
            $quality = $scoreData['calculation_metadata']['quality_assessment'] ?? 0.0;
            $totalQuality += $quality;
            $fieldCount++;

            if ($quality >= 0.9) {
                $analysis['excellent_fields'][] = $fieldKey;
            } elseif ($quality >= 0.7) {
                $analysis['good_fields'][] = $fieldKey;
            } else {
                $analysis['poor_fields'][] = $fieldKey;
            }
        }

        // Check for missing critical fields
        $criticalFields = ['brand_name', 'brand_character', 'writing_style', 'sector_selection'];
        $presentFields = array_keys($fieldScores);
        $analysis['missing_fields'] = array_diff($criticalFields, $presentFields);

        $analysis['overall_quality'] = $fieldCount > 0 ? round($totalQuality / $fieldCount, 2) : 0.0;

        return $analysis;
    }

    /**
     * Calculate profile version (incremental)
     */
    private function calculateProfileVersion(int $tenantId): int
    {
        $profile = AITenantProfile::where('tenant_id', $tenantId)->first();
        return $profile ? ($profile->profile_version ?? 0) + 1 : 1;
    }

    /**
     * Update or create smart profile in database
     */
    public function saveSmartProfile(int $tenantId, array $smartProfileData): AITenantProfile
    {
        $existingProfile = AITenantProfile::where('tenant_id', $tenantId)->first();
        
        if ($existingProfile) {
            // Update existing profile
            $existingProfile->update($smartProfileData);
            $existingProfile->increment('ai_interactions_count');
            $existingProfile->last_ai_interaction_at = now();
            $existingProfile->save();
            
            return $existingProfile;
        } else {
            // Create new profile
            return AITenantProfile::create(array_merge($smartProfileData, [
                'tenant_id' => $tenantId,
                'ai_interactions_count' => 1,
                'last_ai_interaction_at' => now()
            ]));
        }
    }

    /**
     * Get optimized AI context for different scenarios
     */
    public function getOptimizedAIContext(int $tenantId, string $context = 'normal', int $maxPriority = 3): ?string
    {
        $profile = AITenantProfile::where('tenant_id', $tenantId)->first();
        
        if (!$profile || empty($profile->smart_field_scores)) {
            return null;
        }

        $fieldScores = $profile->smart_field_scores;
        $contextThreshold = $this->getContextThreshold($context);
        
        // Filter and sort fields by score
        $relevantFields = array_filter($fieldScores, function($scoreData) use ($contextThreshold, $maxPriority) {
            $score = $scoreData['final_score'] ?? 0;
            $priority = $scoreData['calculation_metadata']['base_priority'] ?? 5;
            
            return $score >= $contextThreshold && $priority <= $maxPriority;
        });

        // Sort by score (highest first)
        uasort($relevantFields, function($a, $b) {
            return ($b['final_score'] ?? 0) <=> ($a['final_score'] ?? 0);
        });

        // Build context string
        $contextParts = [];
        foreach ($relevantFields as $fieldKey => $scoreData) {
            $value = $profile->{$fieldKey} ?? null;
            if (!empty($value)) {
                $contextParts[] = $this->formatFieldForContext($fieldKey, $value, $scoreData);
            }
        }

        return empty($contextParts) ? null : implode("\n", $contextParts);
    }

    /**
     * Get context threshold for filtering
     */
    private function getContextThreshold(string $context): int
    {
        return match($context) {
            'minimal' => 8000,
            'essential' => 6000,
            'normal' => 4000,
            'detailed' => 2000,
            'complete' => 0,
            default => 4000
        };
    }

    /**
     * Format field for AI context
     */
    private function formatFieldForContext(string $fieldKey, $value, array $scoreData): string
    {
        $fieldNames = [
            'brand_name' => 'Marka',
            'city' => 'Şehir',
            'sector_selection' => 'Sektör',
            'brand_character' => 'Marka Karakteri',
            'writing_style' => 'Yazım Stili',
            'founder_name' => 'Kurucu',
            'main_service' => 'Ana Hizmet'
        ];

        $displayName = $fieldNames[$fieldKey] ?? ucfirst(str_replace('_', ' ', $fieldKey));
        $displayValue = is_array($value) ? implode(', ', $value) : (string) $value;
        
        return "**{$displayName}**: {$displayValue}";
    }
}