<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * AI PRIORITY ENGINE - Merkezi Prompt Sıralama Sistemi
 * 
 * Bu engine tüm AI çağrılarında kullanılır:
 * - AIService (Feature prompts)
 * - AI Helpers (Helper functions) 
 * - Prowess (Content generation)
 * - Conversations (Chat system)
 * 
 * WEIGHT-BASED SCORING SYSTEM:
 * Final Score = Base Category Weight × Priority Multiplier + Position Bonus
 */
class AIPriorityEngine
{
    /**
     * Base category weights - Kategori temel ağırlıkları (OPTIMIZE EDİLMİŞ)
     */
    public const BASE_WEIGHTS = [
        'system_common'      => 10000,  // Ortak özellikler (markdown yasağı, türkçe vb)
        'system_hidden'      => 9000,   // Gizli sistem (güvenlik, sınırlar)
        'feature_definition' => 8000,   // Quick prompt (feature ne yapacak) ⬆️ YUKARI
        'expert_knowledge'   => 7000,   // Expert prompts (nasıl yapacak) ⬆️ YUKARI
        'tenant_identity'    => 6000,   // Tenant profili (şirket kimliği) ⬇️ AŞAĞI
        'secret_knowledge'   => 5000,   // Gizli bilgi tabanı (pasif) ⬆️ YUKARI
        'brand_context'      => 4500,   // Marka detayları (feature-aware) ⬇️ AŞAĞI
        'response_format'    => 4000,   // Response template (nasıl görünecek)
        'conditional_info'   => 2000,   // Şartlı yanıtlar (on-demand)
    ];

    /**
     * Priority multipliers - İçerik öncelik çarpanları
     */
    public const PRIORITY_MULTIPLIERS = [
        1 => 1.5,   // Critical: %50 boost
        2 => 1.2,   // Important: %20 boost  
        3 => 1.0,   // Normal: No change
        4 => 0.6,   // Optional: %40 penalty
        5 => 0.3,   // Rarely used: %70 penalty
    ];

    /**
     * Context type thresholds - Hangi context type'da hangi weight'ler dahil (GÜNCEL)
     */
    const CONTEXT_THRESHOLDS = [
        'minimal'   => 8000,   // Sadece system + feature definition
        'essential' => 6000,   // + expert knowledge + tenant identity  
        'normal'    => 4000,   // + secret knowledge + brand context + templates
        'detailed'  => 2000,   // Her şey dahil
        'complete'  => 0,      // Hiçbir şeyi filtreleme
    ];

    /**
     * 🎯 MAIN METHOD: Build complete AI system prompt
     * 
     * @param array $components - Prompt components with priorities
     * @param array $options - Context options (type, feature_name, etc.)
     * @return string - Final ordered system prompt
     */
    public static function buildSystemPrompt(array $components, array $options = []): string
    {
        // Context type belirle
        $contextType = $options['context_type'] ?? 'normal';
        $threshold = self::CONTEXT_THRESHOLDS[$contextType] ?? self::CONTEXT_THRESHOLDS['normal'];
        
        // Feature-based threshold adjustment
        $threshold = self::adjustThresholdByFeature($threshold, $options);
        
        // Component'leri score'la ve sırala
        $scoredComponents = self::scoreComponents($components);
        
        // Threshold'a göre filtrele
        $filteredComponents = array_filter($scoredComponents, function($component) use ($threshold) {
            return $component['final_score'] >= $threshold;
        });
        
        // Score'a göre sırala (yüksekten düşüğe)
        usort($filteredComponents, function($a, $b) {
            return $b['final_score'] <=> $a['final_score'];
        });
        
        // Final prompt'u birleştir
        $promptParts = [];
        foreach ($filteredComponents as $component) {
            if (!empty($component['content'])) {
                $promptParts[] = $component['content'];
            }
        }
        
        // Cache ve log
        self::logPromptBuild($filteredComponents, $options);
        
        return implode("\n\n---\n\n", $promptParts);
    }

    /**
     * Component'leri score'la
     */
    private static function scoreComponents(array $components): array
    {
        $scoredComponents = [];
        
        foreach ($components as $component) {
            $category = $component['category'] ?? 'conditional_info';
            $priority = $component['priority'] ?? 3;
            $content = $component['content'] ?? '';
            $position = $component['position'] ?? 0;
            
            // Base weight al
            $baseWeight = self::BASE_WEIGHTS[$category] ?? 1000;
            
            // Priority multiplier uygula
            $multiplier = self::PRIORITY_MULTIPLIERS[$priority] ?? 1.0;
            
            // Position bonus (expert prompts için)
            $positionBonus = 0;
            if ($category === 'expert_knowledge' && $position > 0) {
                $positionBonus = 100 - ($position * 5); // 1=95, 2=90, 3=85...
            }
            
            // Final score hesapla
            $finalScore = intval($baseWeight * $multiplier) + $positionBonus;
            
            $scoredComponents[] = [
                'category' => $category,
                'priority' => $priority,
                'position' => $position,
                'content' => $content,
                'base_weight' => $baseWeight,
                'multiplier' => $multiplier,
                'position_bonus' => $positionBonus,
                'final_score' => $finalScore,
                'name' => $component['name'] ?? $category,
            ];
        }
        
        return $scoredComponents;
    }

    /**
     * Feature'a göre threshold ayarla
     */
    private static function adjustThresholdByFeature(int $threshold, array $options): int
    {
        $featureName = $options['feature_name'] ?? '';
        
        // Lokasyon önemli olan feature'lar için detailed context
        if (str_contains($featureName, 'local') || 
            str_contains($featureName, 'maps') || 
            str_contains($featureName, 'address') ||
            str_contains($featureName, 'location')) {
            return self::CONTEXT_THRESHOLDS['detailed'];
        }
        
        // Hızlı content için minimal context
        if (str_contains($featureName, 'quick') || 
            str_contains($featureName, 'instant') || 
            str_contains($featureName, 'fast') ||
            str_contains($featureName, 'brief')) {
            return self::CONTEXT_THRESHOLDS['minimal'];
        }
        
        // SEO ve uzman işler için essential context
        if (str_contains($featureName, 'seo') || 
            str_contains($featureName, 'expert') || 
            str_contains($featureName, 'professional') ||
            str_contains($featureName, 'analysis')) {
            return self::CONTEXT_THRESHOLDS['essential'];
        }
        
        return $threshold;
    }

    /**
     * Prompt build process'ini logla + gerçek tenant analytics'e kaydet
     */
    private static function logPromptBuild(array $components, array $options): void
    {
        try {
            $startTime = $options['start_time'] ?? microtime(true);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            // Component analysis hazırla
            $usedPrompts = [];
            $filteredPrompts = [];
            $threshold = $options['threshold'] ?? 4000;
            
            foreach ($components as $comp) {
                $promptData = [
                    'name' => $comp['name'],
                    'category' => $comp['category'],
                    'priority' => $comp['priority'],
                    'base_weight' => $comp['base_weight'],
                    'final_score' => $comp['final_score'],
                    'content_length' => strlen($comp['content']),
                    'multiplier' => $comp['multiplier']
                ];
                
                if ($comp['final_score'] >= $threshold) {
                    $usedPrompts[] = $promptData;
                } else {
                    $filteredPrompts[] = array_merge($promptData, [
                        'filter_reason' => 'below_threshold',
                        'threshold' => $threshold
                    ]);
                }
            }
            
            // Scoring summary
            $scores = array_column($usedPrompts, 'final_score');
            $scoringSummary = [
                'highest_score' => !empty($scores) ? max($scores) : 0,
                'lowest_used_score' => !empty($scores) ? min($scores) : 0,
                'average_score' => !empty($scores) ? round(array_sum($scores) / count($scores)) : 0,
                'total_content_length' => array_sum(array_column($usedPrompts, 'content_length'))
            ];
            
            // Analytics data hazırla (migration schema ile uyumlu)
            $analyticsData = [
                'tenant_id' => tenant('id') ?? 'default',
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'feature_slug' => $options['feature_name'] ?? 'unknown',
                'request_type' => $options['request_type'] ?? 'chat',
                'context_type' => $options['context_type'] ?? 'normal',
                'threshold_used' => $threshold,
                'total_available_prompts' => count($components),
                'actually_used_prompts' => count($usedPrompts),
                'filtered_prompts' => count($filteredPrompts),
                'highest_score' => $scoringSummary['highest_score'],
                'lowest_used_score' => $scoringSummary['lowest_used_score'],
                'execution_time_ms' => intval($executionTime),
                'response_length' => null, // Will be filled by caller
                'token_usage' => null, // Will be filled by caller
                'input_preview' => $options['input_preview'] ?? null,
                'response_preview' => $options['response_preview'] ?? null,
                'prompts_analysis' => [
                    'used_prompts' => $usedPrompts,
                    'filtered_prompts' => $filteredPrompts,
                    'scoring_summary' => $scoringSummary
                ],
                'scoring_summary' => $scoringSummary,
                'ai_model' => config('ai.default_model', 'deepseek-chat'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'has_error' => false,
                'error_message' => null,
                'created_at' => now()
            ];
            
            // Database'e async kaydet (queue ile)
            if (config('ai.debug_logging_enabled', true)) {
                dispatch(new \Modules\AI\App\Jobs\LogAIDebugData($analyticsData));
            }
            
            // Log için summary
            Log::info('🎯 AIPriorityEngine: System prompt built', [
                'tenant_id' => $analyticsData['tenant_id'],
                'feature' => $analyticsData['feature_slug'],
                'context_type' => $analyticsData['context_type'],
                'used_prompts' => $analyticsData['actually_used_prompts'],
                'total_prompts' => $analyticsData['total_available_prompts'],
                'execution_time_ms' => $analyticsData['execution_time_ms'],
                'highest_score' => $analyticsData['highest_score']
            ]);
            
        } catch (\Exception $e) {
            Log::warning('AIPriorityEngine logging failed: ' . $e->getMessage());
        }
    }

    /**
     * 🔧 HELPER: Standard AI components oluştur
     * AIService, AIHelper, Prowess için ortak component'ler
     */
    public static function getStandardComponents(): array
    {
        $components = [];
        
        // 1. Ortak özellikler (System Common)
        $commonPrompt = \Modules\AI\App\Models\Prompt::getCommon();
        if ($commonPrompt) {
            $components[] = [
                'category' => 'system_common',
                'priority' => 1,
                'content' => $commonPrompt->content,
                'name' => 'Ortak Özellikler'
            ];
        }
        
        // 2. Gizli sistem (System Hidden)
        $hiddenSystemPrompt = \Modules\AI\App\Models\Prompt::getHiddenSystem();
        if ($hiddenSystemPrompt) {
            $components[] = [
                'category' => 'system_hidden',
                'priority' => 1,
                'content' => $hiddenSystemPrompt->content,
                'name' => 'Gizli Sistem'
            ];
        }
        
        // 3. Tenant Identity
        $tenantContext = self::getTenantIdentityContext();
        if ($tenantContext) {
            $components[] = [
                'category' => 'tenant_identity',
                'priority' => 1,
                'content' => $tenantContext,
                'name' => 'Tenant Kimliği'
            ];
        }
        
        // 4. Secret Knowledge
        $secretKnowledge = \Modules\AI\App\Models\Prompt::getSecretKnowledge();
        if ($secretKnowledge) {
            $components[] = [
                'category' => 'secret_knowledge',
                'priority' => 3,
                'content' => $secretKnowledge->content,
                'name' => 'Gizli Bilgi Tabanı'
            ];
        }
        
        // 5. Conditional Responses
        $conditionalResponses = \Modules\AI\App\Models\Prompt::getConditional();
        if ($conditionalResponses) {
            $components[] = [
                'category' => 'conditional_info',
                'priority' => 4,
                'content' => $conditionalResponses->content,
                'name' => 'Şartlı Yanıtlar'
            ];
        }
        
        return $components;
    }

    /**
     * 🎯 HELPER: Brand context components oluştur
     */
    public static function getBrandComponents(array $options = []): array
    {
        $components = [];
        
        try {
            $tenantId = resolve_tenant_id(false);
            \Log::info('🔍 AIPriorityEngine.getBrandComponents Debug', [
                'tenant_id' => $tenantId,
                'has_tenant' => !empty($tenantId)
            ]);
            
            if (!$tenantId) {
                \Log::warning('❌ getBrandComponents: No tenant ID');
                return $components;
            }
            
            $profile = \Modules\AI\App\Models\AITenantProfile::where('tenant_id', $tenantId)->first();
            \Log::info('🔍 Profile Check', [
                'profile_exists' => !empty($profile),
                'has_business_name' => !empty($profile?->business_name),
                'has_industry' => !empty($profile?->industry),
                'profile_completed_at' => $profile?->profile_completed_at ?? 'not_set'
            ]);
            
            if (!$profile) {
                \Log::warning('❌ getBrandComponents: Profile not found');
                return $components;
            }
            
            // AI Tenant Profile JSON yapısında minimum brand bilgileri kontrolü
            $companyInfo = $profile->company_info ?? [];
            $sectorDetails = $profile->sector_details ?? [];
            
            $hasBrandInfo = !empty($companyInfo['brand_name']) || 
                           !empty($companyInfo['main_service']) || 
                           !empty($sectorDetails);
                           
            if (!$hasBrandInfo) {
                \Log::warning('❌ getBrandComponents: Profile exists but has no brand info', [
                    'company_info_keys' => array_keys($companyInfo),
                    'sector_details_keys' => is_array($sectorDetails) ? array_keys($sectorDetails) : 'not_array',
                    'profile_completed' => $profile->is_completed ?? false
                ]);
                return $components;
            }
            
            // Context type'a göre priority level belirle
            $contextType = $options['context_type'] ?? 'normal';
            $maxPriority = match($contextType) {
                'minimal' => 1,
                'essential' => 2,
                'normal' => 3,
                'detailed' => 4,
                'complete' => 4,
                default => 3
            };
            
            // Eski tablo yapısı için brand context oluştur
            $brandContext = self::buildLegacyBrandContext($profile, $maxPriority);
            if ($brandContext) {
                $components[] = [
                    'category' => 'brand_context',
                    'priority' => 1,
                    'content' => "## 🎯 MARKA KİMLİĞİ\n" . $brandContext,
                    'name' => 'Marka Kimliği'
                ];
            }
            
        } catch (\Exception $e) {
            Log::warning('Brand components build failed', ['error' => $e->getMessage()]);
        }
        
        return $components;
    }

    /**
     * 🔧 HELPER: Feature-specific components oluştur
     */
    public static function getFeatureComponents($feature): array
    {
        $components = [];
        
        // Quick Prompt
        if ($feature->hasQuickPrompt()) {
            $components[] = [
                'category' => 'feature_definition',
                'priority' => 1,
                'content' => "=== GÖREV TANIMI ===\n" . $feature->quick_prompt,
                'name' => 'Quick Prompt'
            ];
        }
        
        // Expert Prompts (priority sırasına göre)
        $expertPrompts = $feature->prompts()
            ->wherePivot('is_active', true)
            ->where('prompt_type', 'feature')
            ->orderBy('ai_feature_prompts.priority', 'asc')
            ->get();
            
        foreach ($expertPrompts as $index => $prompt) {
            $role = $prompt->pivot->role ?? 'primary';
            $expertPriority = $prompt->pivot->priority ?? ($index + 1);
            
            $components[] = [
                'category' => 'expert_knowledge',
                'priority' => min($expertPriority, 4), // Max 4 priority
                'position' => $expertPriority,
                'content' => "=== UZMAN BİLGİSİ ({$role}) ===\n" . $prompt->content,
                'name' => "Expert Prompt #{$expertPriority}"
            ];
        }
        
        // Response Template
        if ($feature->hasResponseTemplate()) {
            $components[] = [
                'category' => 'response_format',
                'priority' => 2,
                'content' => "=== YANIT FORMATI ===\n" . $feature->getFormattedTemplate(),
                'name' => 'Response Template'
            ];
        }
        
        return $components;
    }

    /**
     * Helper: Basic tenant identity context
     */
    private static function getTenantIdentityContext(): ?string
    {
        try {
            $tenant = tenant();
            if (!$tenant) {
                return null;
            }
            
            return "TENANT CONTEXT: {$tenant->name} (ID: {$tenant->id})";
            
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * AI Tenant Profile için brand context builder - YENİ JSON yapısı
     */
    private static function buildLegacyBrandContext($profile, int $maxPriority): ?string
    {
        $context = [];
        
        // Yeni AI-friendly data formatını kullan
        $profileData = $profile->getAIFriendlyDataSorted($maxPriority);
        
        if (empty($profileData)) {
            return null;
        }
        
        // Priority'ye göre sıralanmış verileri context'e ekle
        foreach ($profileData as $item) {
            $fieldData = $item['data'];
            $fieldKey = $item['key'];
            $priority = $item['priority'];
            $multiplier = $item['multiplier'];
            
            // Sadece önemli alanları context'e ekle
            if ($priority <= $maxPriority) {
                $fieldName = $fieldData['question'] ?? ucfirst(str_replace('_', ' ', $fieldKey));
                
                if (isset($fieldData['value'])) {
                    // Tek değer (string)
                    $value = $fieldData['value'];
                    $displayValue = $fieldData['label'] ?? $value;
                    
                    // Array değeri string'e çevir
                    if (is_array($value)) {
                        $displayValue = implode(', ', array_filter($value));
                    } elseif (is_array($displayValue)) {
                        $displayValue = implode(', ', array_filter($displayValue));
                    }
                    
                    if (!empty($displayValue)) {
                        $context[] = "**{$fieldName}**: {$displayValue}";
                    }
                } elseif (isset($fieldData['values'])) {
                    // Çoklu değer (array)
                    $values = $fieldData['values'];
                    $displayValues = $fieldData['labels'] ?? $values;
                    
                    // Array handling
                    if (is_array($displayValues)) {
                        $cleanValues = array_filter($displayValues);
                        if (!empty($cleanValues)) {
                            $context[] = "**{$fieldName}**: " . implode(', ', $cleanValues);
                        }
                    } elseif (!empty($displayValues)) {
                        $context[] = "**{$fieldName}**: {$displayValues}";
                    }
                }
            }
        }
        
        if (empty($context)) {
            return null;
        }
        
        $finalContext = implode("\n", $context);
        $finalContext .= "\n\n*Bu marka bilgilerine uygun, tutarlı ve kişiselleştirilmiş yanıtlar üret.*";
        
        return $finalContext;
    }

    /**
     * 🎯 QUICK ACCESS: Tüm AI çağrıları için tek entry point
     */
    public static function buildCompletePrompt(array $options = []): string
    {
        $components = [];
        
        // Standard components ekle
        $components = array_merge($components, self::getStandardComponents());
        
        // Brand components ekle
        $components = array_merge($components, self::getBrandComponents($options));
        
        // Feature varsa feature components ekle
        if (isset($options['feature'])) {
            $components = array_merge($components, self::getFeatureComponents($options['feature']));
        }
        
        // Custom components varsa ekle
        if (isset($options['custom_components'])) {
            $components = array_merge($components, $options['custom_components']);
        }
        
        return self::buildSystemPrompt($components, $options);
    }
}