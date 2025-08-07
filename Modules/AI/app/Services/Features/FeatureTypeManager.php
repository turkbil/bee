<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Features;

use Illuminate\Support\Facades\Cache;
use Modules\AI\App\Models\AIFeature;

/**
 * 🎯 Feature Type Management System
 * 
 * Özellikler:
 * - 4 farklı feature type (quick, standard, advanced, enterprise)
 * - Type-based resource allocation
 * - Performance optimization
 * - Usage tracking per type
 * - Dynamic type upgrading/downgrading
 */
readonly class FeatureTypeManager
{
    private const CACHE_PREFIX = 'feature_types';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * 🏷️ Feature Type Definitions
     */
    private const FEATURE_TYPES = [
        'quick' => [
            'name' => 'Quick',
            'description' => 'Hızlı ve basit AI işlemleri',
            'max_tokens' => 1000,
            'max_context_length' => 2000,
            'response_time_target' => 2000, // ms
            'cache_ttl' => 600, // 10 minutes
            'priority' => 4,
            'template_complexity' => 'simple',
            'features' => [
                'basic_prompts' => true,
                'simple_templates' => true,
                'context_aware' => false,
                'advanced_formatting' => false,
                'custom_instructions' => false
            ]
        ],
        
        'standard' => [
            'name' => 'Standard',
            'description' => 'Genel kullanım için optimized AI features',
            'max_tokens' => 3000,
            'max_context_length' => 5000,
            'response_time_target' => 5000, // ms
            'cache_ttl' => 1800, // 30 minutes
            'priority' => 3,
            'template_complexity' => 'moderate',
            'features' => [
                'basic_prompts' => true,
                'simple_templates' => true,
                'context_aware' => true,
                'advanced_formatting' => true,
                'custom_instructions' => false
            ]
        ],
        
        'advanced' => [
            'name' => 'Advanced',
            'description' => 'Karmaşık AI işlemleri ve detaylı analizler',
            'max_tokens' => 8000,
            'max_context_length' => 12000,
            'response_time_target' => 10000, // ms
            'cache_ttl' => 3600, // 1 hour
            'priority' => 2,
            'template_complexity' => 'advanced',
            'features' => [
                'basic_prompts' => true,
                'simple_templates' => true,
                'context_aware' => true,
                'advanced_formatting' => true,
                'custom_instructions' => true,
                'multi_step_processing' => true,
                'advanced_templates' => true
            ]
        ],
        
        'enterprise' => [
            'name' => 'Enterprise',
            'description' => 'Kurumsal seviye AI işlemleri ve özelleştirmeler',
            'max_tokens' => 20000,
            'max_context_length' => 30000,
            'response_time_target' => 15000, // ms
            'cache_ttl' => 7200, // 2 hours
            'priority' => 1,
            'template_complexity' => 'enterprise',
            'features' => [
                'basic_prompts' => true,
                'simple_templates' => true,
                'context_aware' => true,
                'advanced_formatting' => true,
                'custom_instructions' => true,
                'multi_step_processing' => true,
                'advanced_templates' => true,
                'workflow_integration' => true,
                'custom_model_selection' => true,
                'detailed_analytics' => true
            ]
        ]
    ];

    /**
     * 🎯 Feature'ın type'ını belirle
     *
     * @param AIFeature $feature Feature instance
     * @param array $context Request context
     * @return string Feature type
     */
    public function determineFeatureType(AIFeature $feature, array $context = []): string
    {
        // Explicit type varsa onu kullan
        if (!empty($feature->type) && $this->isValidType($feature->type)) {
            return $feature->type;
        }

        // Context-based type detection
        $detectedType = $this->detectTypeFromContext($feature, $context);
        if ($detectedType) {
            return $detectedType;
        }

        // Feature complexity'ye göre auto-detection
        return $this->detectTypeFromComplexity($feature);
    }

    /**
     * 🔍 Context'ten type tespit et
     *
     * @param AIFeature $feature Feature instance
     * @param array $context Context data
     * @return string|null Detected type
     */
    private function detectTypeFromContext(AIFeature $feature, array $context): ?string
    {
        // User input uzunluğu
        $userInput = $context['user_input'] ?? '';
        $inputLength = strlen($userInput);

        // Kısa input = quick type
        if ($inputLength < 100) {
            return 'quick';
        }

        // Uzun input = advanced/enterprise
        if ($inputLength > 1000) {
            return 'advanced';
        }

        // Token requirement
        $estimatedTokens = $context['estimated_tokens'] ?? 0;
        if ($estimatedTokens < 500) {
            return 'quick';
        }
        if ($estimatedTokens > 5000) {
            return 'enterprise';
        }

        // Response time requirement
        $requiredSpeed = $context['required_speed'] ?? 'normal';
        if ($requiredSpeed === 'fast') {
            return 'quick';
        }
        if ($requiredSpeed === 'detailed') {
            return 'advanced';
        }

        return null;
    }

    /**
     * 🧠 Feature complexity'den type tespit et
     *
     * @param AIFeature $feature Feature instance
     * @return string Detected type
     */
    private function detectTypeFromComplexity(AIFeature $feature): string
    {
        $complexityScore = 0;

        // Quick prompt varlığı
        if ($feature->hasQuickPrompt()) {
            $complexityScore += 1;
        }

        // Expert prompt varlığı ve sayısı
        $expertPrompts = $feature->getExpertPrompts();
        $complexityScore += count($expertPrompts) * 2;

        // Response template complexity
        if ($feature->hasResponseTemplate()) {
            $template = $feature->getResponseTemplateData();
            if (isset($template['sections']) && count($template['sections']) > 3) {
                $complexityScore += 2;
            }
            if (isset($template['conditions']) && !empty($template['conditions'])) {
                $complexityScore += 3;
            }
        }

        // Feature name/slug based hints
        $featureName = strtolower($feature->name ?? $feature->slug ?? '');
        
        if (str_contains($featureName, 'quick') || str_contains($featureName, 'simple')) {
            return 'quick';
        }
        
        if (str_contains($featureName, 'advanced') || str_contains($featureName, 'complex')) {
            return 'advanced';
        }
        
        if (str_contains($featureName, 'enterprise') || str_contains($featureName, 'premium')) {
            return 'enterprise';
        }

        // Score-based mapping
        return match (true) {
            $complexityScore <= 2 => 'quick',
            $complexityScore <= 5 => 'standard',
            $complexityScore <= 10 => 'advanced',
            default => 'enterprise'
        };
    }

    /**
     * ⚙️ Type configuration'ı al
     *
     * @param string $type Feature type
     * @return array Type configuration
     */
    public function getTypeConfiguration(string $type): array
    {
        if (!$this->isValidType($type)) {
            $type = 'standard'; // fallback
        }

        return self::FEATURE_TYPES[$type];
    }

    /**
     * 🎛️ Type için processing options oluştur
     *
     * @param string $type Feature type
     * @param array $baseOptions Base options
     * @return array Enhanced options
     */
    public function buildProcessingOptions(string $type, array $baseOptions = []): array
    {
        $typeConfig = $this->getTypeConfiguration($type);
        
        return array_merge($baseOptions, [
            'feature_type' => $type,
            'max_tokens' => $typeConfig['max_tokens'],
            'max_context_length' => $typeConfig['max_context_length'],
            'response_time_target' => $typeConfig['response_time_target'],
            'cache_ttl' => $typeConfig['cache_ttl'],
            'priority' => $typeConfig['priority'],
            'template_complexity' => $typeConfig['template_complexity'],
            'type_features' => $typeConfig['features']
        ]);
    }

    /**
     * 🏃‍♂️ Performance constraints uygula
     *
     * @param string $type Feature type
     * @param array $processingData Processing data
     * @return array Constrained data
     */
    public function applyPerformanceConstraints(string $type, array $processingData): array
    {
        $typeConfig = $this->getTypeConfiguration($type);
        $constrained = $processingData;

        // Token limit enforcement
        if (isset($constrained['max_tokens']) && $constrained['max_tokens'] > $typeConfig['max_tokens']) {
            $constrained['max_tokens'] = $typeConfig['max_tokens'];
        }

        // Context length limit
        if (isset($constrained['context']) && strlen($constrained['context']) > $typeConfig['max_context_length']) {
            $constrained['context'] = substr($constrained['context'], 0, $typeConfig['max_context_length']);
            $constrained['context_truncated'] = true;
        }

        // Priority assignment
        $constrained['processing_priority'] = $typeConfig['priority'];

        // Cache strategy
        $constrained['cache_strategy'] = [
            'enabled' => true,
            'ttl' => $typeConfig['cache_ttl'],
            'key_prefix' => "ft_{$type}_"
        ];

        return $constrained;
    }

    /**
     * 📊 Type usage istatistikleri
     *
     * @param string|null $type Specific type or all
     * @return array Usage statistics
     */
    public function getUsageStatistics(?string $type = null): array
    {
        $cacheKey = self::CACHE_PREFIX . ':usage_stats';
        
        $stats = Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return $this->calculateUsageStatistics();
        });

        if ($type && isset($stats[$type])) {
            return [$type => $stats[$type]];
        }

        return $stats;
    }

    /**
     * 🔄 Feature type'ı upgrade/downgrade et
     *
     * @param AIFeature $feature Feature to update
     * @param string $newType New type
     * @param string $reason Reason for change
     * @return bool Success status
     */
    public function changeFeatureType(AIFeature $feature, string $newType, string $reason = ''): bool
    {
        if (!$this->isValidType($newType)) {
            return false;
        }

        $oldType = $feature->type ?? $this->determineFeatureType($feature);
        
        // Type change log
        \Log::info('Feature type changed', [
            'feature_id' => $feature->id,
            'feature_name' => $feature->name,
            'old_type' => $oldType,
            'new_type' => $newType,
            'reason' => $reason
        ]);

        // Update feature
        $feature->update(['type' => $newType]);

        // Clear related caches
        $this->clearFeatureTypeCache($feature->id);

        return true;
    }

    /**
     * 🔮 Feature type önerisi al
     *
     * @param AIFeature $feature Feature instance
     * @param array $usageData Usage data
     * @return array Recommendation
     */
    public function recommendType(AIFeature $feature, array $usageData = []): array
    {
        $currentType = $this->determineFeatureType($feature);
        $currentConfig = $this->getTypeConfiguration($currentType);
        
        $recommendation = [
            'current_type' => $currentType,
            'recommended_type' => $currentType,
            'confidence' => 100,
            'reasons' => [],
            'benefits' => []
        ];

        // Usage pattern analysis
        $avgResponseTime = $usageData['avg_response_time'] ?? 0;
        $avgTokenUsage = $usageData['avg_token_usage'] ?? 0;
        $errorRate = $usageData['error_rate'] ?? 0;
        $usageFrequency = $usageData['usage_frequency'] ?? 0;

        // Performance-based recommendations
        if ($avgResponseTime > $currentConfig['response_time_target'] * 1.5) {
            if ($currentType !== 'quick') {
                $recommendation['recommended_type'] = 'quick';
                $recommendation['reasons'][] = 'Yavaş yanıt süresi';
                $recommendation['benefits'][] = 'Daha hızlı işlem';
                $recommendation['confidence'] = 80;
            }
        }

        // Resource usage analysis
        if ($avgTokenUsage > $currentConfig['max_tokens'] * 0.8) {
            $nextType = $this->getNextHigherType($currentType);
            if ($nextType) {
                $recommendation['recommended_type'] = $nextType;
                $recommendation['reasons'][] = 'Yüksek token kullanımı';
                $recommendation['benefits'][] = 'Daha yüksek token limiti';
                $recommendation['confidence'] = 90;
            }
        }

        // Error rate analysis
        if ($errorRate > 0.1) { // %10'dan fazla hata
            if ($currentType !== 'standard') {
                $recommendation['recommended_type'] = 'standard';
                $recommendation['reasons'][] = 'Yüksek hata oranı';
                $recommendation['benefits'][] = 'Daha kararlı işlem';
                $recommendation['confidence'] = 75;
            }
        }

        return $recommendation;
    }

    /**
     * ✅ Type geçerli mi kontrol et
     *
     * @param string $type Type name
     * @return bool Is valid
     */
    public function isValidType(string $type): bool
    {
        return isset(self::FEATURE_TYPES[$type]);
    }

    /**
     * 📋 Tüm type'ları listele
     *
     * @return array All types
     */
    public function getAllTypes(): array
    {
        return array_keys(self::FEATURE_TYPES);
    }

    /**
     * ⬆️ Bir sonraki yüksek type'ı al
     *
     * @param string $currentType Current type
     * @return string|null Next higher type
     */
    private function getNextHigherType(string $currentType): ?string
    {
        $hierarchy = ['quick', 'standard', 'advanced', 'enterprise'];
        $currentIndex = array_search($currentType, $hierarchy);
        
        if ($currentIndex !== false && $currentIndex < count($hierarchy) - 1) {
            return $hierarchy[$currentIndex + 1];
        }

        return null;
    }

    /**
     * ⬇️ Bir sonraki düşük type'ı al
     *
     * @param string $currentType Current type
     * @return string|null Next lower type
     */
    private function getNextLowerType(string $currentType): ?string
    {
        $hierarchy = ['quick', 'standard', 'advanced', 'enterprise'];
        $currentIndex = array_search($currentType, $hierarchy);
        
        if ($currentIndex !== false && $currentIndex > 0) {
            return $hierarchy[$currentIndex - 1];
        }

        return null;
    }

    /**
     * 📊 Usage istatistiklerini hesapla
     *
     * @return array Statistics
     */
    private function calculateUsageStatistics(): array
    {
        try {
            // Bu normalde veritabanından gelecek
            // Şimdilik mock data
            return [
                'quick' => [
                    'total_usage' => 1250,
                    'avg_response_time' => 1200,
                    'avg_token_usage' => 450,
                    'error_rate' => 0.02,
                    'satisfaction_score' => 4.2
                ],
                'standard' => [
                    'total_usage' => 3400,
                    'avg_response_time' => 3800,
                    'avg_token_usage' => 1800,
                    'error_rate' => 0.01,
                    'satisfaction_score' => 4.5
                ],
                'advanced' => [
                    'total_usage' => 890,
                    'avg_response_time' => 8200,
                    'avg_token_usage' => 5500,
                    'error_rate' => 0.03,
                    'satisfaction_score' => 4.7
                ],
                'enterprise' => [
                    'total_usage' => 156,
                    'avg_response_time' => 12000,
                    'avg_token_usage' => 12000,
                    'error_rate' => 0.01,
                    'satisfaction_score' => 4.9
                ]
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 🗑️ Feature type cache'ini temizle
     *
     * @param int|null $featureId Specific feature or all
     */
    private function clearFeatureTypeCache(?int $featureId = null): void
    {
        if ($featureId) {
            Cache::forget(self::CACHE_PREFIX . ":feature_{$featureId}");
        } else {
            // Clear all feature type caches
            Cache::forget(self::CACHE_PREFIX . ':usage_stats');
        }
    }
}