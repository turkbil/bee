<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\FeatureType;

use Modules\AI\App\Models\AIFeature;
use Illuminate\Support\Facades\Cache;

/**
 * 🎯 FEATURE TYPE MANAGER
 * 4 farklı feature type sistemi ile akıllı feature yönetimi
 */
readonly class FeatureTypeManager
{
    private const CACHE_PREFIX = 'feature_type:';
    private const CACHE_TTL = 1800; // 30 minutes
    
    // 4 Feature Type Definition
    public const FEATURE_TYPES = [
        'quick' => [
            'name' => 'Quick Action',
            'description' => 'Hızlı, tek amaçlı işlemler için',
            'max_context_length' => 1000,
            'response_time_target' => 2, // seconds
            'complexity_level' => 'simple',
            'cache_duration' => 300, // 5 minutes
            'default_template' => 'quick_response'
        ],
        'standard' => [
            'name' => 'Standard Feature',
            'description' => 'Orta karmaşıklıktaki genel işlemler',
            'max_context_length' => 3000,
            'response_time_target' => 5, // seconds
            'complexity_level' => 'medium',
            'cache_duration' => 900, // 15 minutes
            'default_template' => 'standard_response'
        ],
        'advanced' => [
            'name' => 'Advanced Analysis',
            'description' => 'Karmaşık analiz ve işleme gerektiren özellikler',
            'max_context_length' => 8000,
            'response_time_target' => 10, // seconds
            'complexity_level' => 'complex',
            'cache_duration' => 1800, // 30 minutes
            'default_template' => 'advanced_response'
        ],
        'enterprise' => [
            'name' => 'Enterprise Solution',
            'description' => 'Kurumsal seviye, çoklu adım işlemler',
            'max_context_length' => 15000,
            'response_time_target' => 20, // seconds
            'complexity_level' => 'enterprise',
            'cache_duration' => 3600, // 1 hour
            'default_template' => 'enterprise_response'
        ]
    ];
    
    /**
     * Feature type'ın konfigürasyonunu al
     */
    public function getTypeConfig(string $featureType): array
    {
        return self::FEATURE_TYPES[$featureType] ?? self::FEATURE_TYPES['standard'];
    }
    
    /**
     * Feature'ın type'ını otomatik belirle
     */
    public function determineFeatureType(AIFeature $feature): string
    {
        $cacheKey = self::CACHE_PREFIX . "auto_type:{$feature->id}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($feature) {
            return $this->analyzeFeatureComplexity($feature);
        });
    }
    
    /**
     * Feature complexity analysis ile type belirleme
     */
    private function analyzeFeatureComplexity(AIFeature $feature): string
    {
        $complexityScore = 0;
        
        // 1. Quick Prompt Length Analysis
        if ($feature->quick_prompt) {
            $promptLength = strlen($feature->quick_prompt);
            if ($promptLength > 500) $complexityScore += 2;
            elseif ($promptLength > 200) $complexityScore += 1;
        }
        
        // 2. Expert Prompt Complexity
        if ($feature->expert_prompt_id) {
            $expertPrompt = \Modules\AI\App\Models\Prompt::find($feature->expert_prompt_id);
            if ($expertPrompt) {
                $expertLength = strlen($expertPrompt->content);
                if ($expertLength > 2000) $complexityScore += 3;
                elseif ($expertLength > 800) $complexityScore += 2;
                elseif ($expertLength > 300) $complexityScore += 1;
            }
        }
        
        // 3. Response Template Complexity
        if ($feature->response_template) {
            try {
                $template = json_decode($feature->response_template, true);
                if ($template) {
                    // Multiple sections = higher complexity
                    if (isset($template['sections']) && is_array($template['sections'])) {
                        $sectionCount = count($template['sections']);
                        if ($sectionCount > 5) $complexityScore += 3;
                        elseif ($sectionCount > 2) $complexityScore += 2;
                        elseif ($sectionCount > 1) $complexityScore += 1;
                    }
                    
                    // JSON output = higher complexity
                    if (isset($template['json_output']) && $template['json_output']) {
                        $complexityScore += 2;
                    }
                    
                    // Scoring system = higher complexity
                    if (isset($template['scoring']) && $template['scoring']) {
                        $complexityScore += 1;
                    }
                }
            } catch (\Exception $e) {
                // Invalid JSON, assume simple
            }
        }
        
        // 4. Feature Name/Description Analysis
        $featureName = strtolower($feature->name);
        $quickKeywords = ['quick', 'fast', 'instant', 'simple', 'basic'];
        $advancedKeywords = ['advanced', 'complex', 'analysis', 'detailed', 'comprehensive'];
        $enterpriseKeywords = ['enterprise', 'professional', 'business', 'complete', 'solution'];
        
        foreach ($quickKeywords as $keyword) {
            if (str_contains($featureName, $keyword)) {
                $complexityScore -= 1; // Reduce complexity
                break;
            }
        }
        
        foreach ($advancedKeywords as $keyword) {
            if (str_contains($featureName, $keyword)) {
                $complexityScore += 2;
                break;
            }
        }
        
        foreach ($enterpriseKeywords as $keyword) {
            if (str_contains($featureName, $keyword)) {
                $complexityScore += 3;
                break;
            }
        }
        
        // Score'a göre type belirle
        if ($complexityScore <= 1) return 'quick';
        if ($complexityScore <= 4) return 'standard';
        if ($complexityScore <= 8) return 'advanced';
        return 'enterprise';
    }
    
    /**
     * Feature type'a göre context optimizasyonu
     */
    public function optimizeContextForType(string $featureType, string $context): string
    {
        $config = $this->getTypeConfig($featureType);
        $maxLength = $config['max_context_length'];
        
        if (strlen($context) <= $maxLength) {
            return $context;
        }
        
        // Type'a göre farklı kısaltma stratejileri
        return match($featureType) {
            'quick' => $this->optimizeForQuick($context, $maxLength),
            'standard' => $this->optimizeForStandard($context, $maxLength),
            'advanced' => $this->optimizeForAdvanced($context, $maxLength),
            'enterprise' => $this->optimizeForEnterprise($context, $maxLength),
            default => substr($context, 0, $maxLength)
        };
    }
    
    /**
     * Quick type için context optimizasyonu
     */
    private function optimizeForQuick(string $context, int $maxLength): string
    {
        // Quick işlemler için sadece temel bilgileri koru
        $lines = explode("\n", $context);
        $priorityLines = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Öncelikli bilgileri koru
            if (str_contains($line, '🎯') || str_contains($line, 'MARKA') || str_contains($line, 'Şirket:')) {
                $priorityLines[] = $line;
            }
        }
        
        $optimized = implode("\n", $priorityLines);
        return strlen($optimized) <= $maxLength ? $optimized : substr($optimized, 0, $maxLength);
    }
    
    /**
     * Standard type için context optimizasyonu
     */
    private function optimizeForStandard(string $context, int $maxLength): string
    {
        // Standard işlemler için özet koruma
        $paragraphs = explode("\n\n", $context);
        $optimized = '';
        
        foreach ($paragraphs as $paragraph) {
            if (strlen($optimized . $paragraph) <= $maxLength) {
                $optimized .= $paragraph . "\n\n";
            } else {
                // Bu paragrafı kısalt
                $remaining = $maxLength - strlen($optimized);
                if ($remaining > 100) {
                    $optimized .= substr($paragraph, 0, $remaining - 20) . '...';
                }
                break;
            }
        }
        
        return trim($optimized);
    }
    
    /**
     * Advanced type için context optimizasyonu
     */
    private function optimizeForAdvanced(string $context, int $maxLength): string
    {
        // Advanced işlemler için akıllı özetleme
        if (strlen($context) <= $maxLength) {
            return $context;
        }
        
        // Önemli kısımları koru, detayları kısalt
        $sections = preg_split('/(?=🎯|📋|🏢|👤)/', $context);
        $optimized = '';
        
        foreach ($sections as $section) {
            $section = trim($section);
            if (empty($section)) continue;
            
            if (strlen($optimized . $section) <= $maxLength) {
                $optimized .= $section . "\n\n";
            } else {
                // Bu section'ı özetle
                $lines = explode("\n", $section);
                $summary = $lines[0] . "\n"; // Header'ı koru
                $summary .= "• " . substr(implode(' ', array_slice($lines, 1)), 0, 200) . "...\n";
                
                if (strlen($optimized . $summary) <= $maxLength) {
                    $optimized .= $summary . "\n";
                }
                break;
            }
        }
        
        return trim($optimized);
    }
    
    /**
     * Enterprise type için context optimizasyonu
     */
    private function optimizeForEnterprise(string $context, int $maxLength): string
    {
        // Enterprise işlemler için mümkün olduğunca koruma
        if (strlen($context) <= $maxLength) {
            return $context;
        }
        
        // Sadece gerçekten gerekirse kısalt
        $important = $this->extractImportantSections($context);
        return strlen($important) <= $maxLength ? $important : substr($context, 0, $maxLength);
    }
    
    /**
     * Context'ten önemli section'ları çıkar
     */
    private function extractImportantSections(string $context): string
    {
        $importantMarkers = ['🎯', '📋', '🏢', '👤', 'MARKA', 'ŞİRKET', 'CONTEXT'];
        $lines = explode("\n", $context);
        $important = [];
        
        foreach ($lines as $line) {
            foreach ($importantMarkers as $marker) {
                if (str_contains($line, $marker)) {
                    $important[] = $line;
                    break;
                }
            }
        }
        
        return implode("\n", $important);
    }
    
    /**
     * Type'a göre response post-processing
     */
    public function processResponseForType(string $featureType, string $response): string
    {
        $config = $this->getTypeConfig($featureType);
        
        return match($featureType) {
            'quick' => $this->processQuickResponse($response),
            'standard' => $this->processStandardResponse($response),
            'advanced' => $this->processAdvancedResponse($response),
            'enterprise' => $this->processEnterpriseResponse($response),
            default => $response
        };
    }
    
    /**
     * Quick response processing
     */
    private function processQuickResponse(string $response): string
    {
        // Quick responses should be concise
        $lines = explode("\n", $response);
        $processed = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Remove excessive explanations for quick responses
            if (str_contains($line, 'açıklama:') || str_contains($line, 'detaylı')) {
                continue;
            }
            
            $processed[] = $line;
            
            // Limit quick responses to essential info only
            if (count($processed) >= 10) break;
        }
        
        return implode("\n", $processed);
    }
    
    /**
     * Standard response processing
     */
    private function processStandardResponse(string $response): string
    {
        // Standard responses - balanced format
        return $response; // No special processing needed for standard
    }
    
    /**
     * Advanced response processing
     */
    private function processAdvancedResponse(string $response): string
    {
        // Advanced responses - add analysis structure
        if (!str_contains($response, '## ')) {
            // Add section headers if missing
            $paragraphs = explode("\n\n", $response);
            if (count($paragraphs) >= 3) {
                $structured = "## Analiz Sonucu\n\n" . $paragraphs[0] . "\n\n";
                $structured .= "## Detaylı İnceleme\n\n" . implode("\n\n", array_slice($paragraphs, 1));
                return $structured;
            }
        }
        
        return $response;
    }
    
    /**
     * Enterprise response processing
     */
    private function processEnterpriseResponse(string $response): string
    {
        // Enterprise responses - add executive summary
        if (!str_contains($response, 'Yönetici Özeti')) {
            $lines = explode("\n", $response);
            $firstParagraph = [];
            
            foreach ($lines as $line) {
                if (empty(trim($line)) && !empty($firstParagraph)) break;
                if (!empty(trim($line))) $firstParagraph[] = $line;
            }
            
            if (!empty($firstParagraph)) {
                $summary = "## 🎯 Yönetici Özeti\n\n";
                $summary .= implode("\n", array_slice($firstParagraph, 0, 3)) . "\n\n";
                $summary .= "## 📋 Detaylı Rapor\n\n" . $response;
                return $summary;
            }
        }
        
        return $response;
    }
    
    /**
     * Feature type istatistikleri
     */
    public function getTypeStatistics(): array
    {
        $cacheKey = self::CACHE_PREFIX . 'statistics';
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $stats = [];
            
            foreach (array_keys(self::FEATURE_TYPES) as $type) {
                $count = AIFeature::where('type', $type)->count();
                $avgUsage = AIFeature::where('type', $type)->avg('usage_count') ?: 0;
                
                $stats[$type] = [
                    'count' => $count,
                    'avg_usage' => round($avgUsage, 2),
                    'config' => self::FEATURE_TYPES[$type]
                ];
            }
            
            return $stats;
        });
    }
    
    /**
     * Type performance metrics
     */
    public function getPerformanceMetrics(string $featureType): array
    {
        $config = $this->getTypeConfig($featureType);
        
        // Mock performance data - gerçek implementasyonda metrics collection gerekir
        return [
            'target_response_time' => $config['response_time_target'],
            'actual_avg_response_time' => $config['response_time_target'] * 0.8, // %80 efficiency
            'cache_hit_rate' => match($featureType) {
                'quick' => 95.2,
                'standard' => 87.4,
                'advanced' => 72.1,
                'enterprise' => 58.9,
                default => 80.0
            },
            'context_optimization_rate' => match($featureType) {
                'quick' => 85.0, // %85 context reduction
                'standard' => 45.0, // %45 context reduction
                'advanced' => 25.0, // %25 context reduction  
                'enterprise' => 10.0, // %10 context reduction
                default => 40.0
            }
        ];
    }
    
    /**
     * Cache temizleme
     */
    public function clearTypeCache(string $featureType = null): void
    {
        if ($featureType) {
            Cache::forget(self::CACHE_PREFIX . "auto_type:*");
        } else {
            Cache::forget(self::CACHE_PREFIX . 'statistics');
        }
    }
}