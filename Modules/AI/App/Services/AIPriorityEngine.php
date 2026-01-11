<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\AI\App\Services\ResponseTemplateEngine;

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
        'minimal'       => 8000,   // Sadece system + feature definition
        'essential'     => 6000,   // + expert knowledge + tenant identity  
        'normal'        => 4000,   // + secret knowledge + brand context + templates
        'detailed'      => 2000,   // Her şey dahil
        'complete'      => 0,      // Hiçbir şeyi filtreleme
    ];

    /**
     * 🔥 V2 FEATURE: Feature-specific priority mapping
     * Her feature türü için özelleştirilmiş category weight'leri
     */
    public const FEATURE_SPECIFIC_WEIGHTS = [
        // SEO odaklı feature'lar - Brand context düşük, teknik bilgi yüksek
        'seo' => [
            'expert_knowledge'   => 8500,  // SEO teknik bilgileri kritik
            'brand_context'      => 3000,  // Marka detayları daha az önemli
            'response_format'    => 5000,  // Yapılandırılmış sonuç önemli
        ],
        
        // Blog/Content odaklı feature'lar - Brand context yüksek, yaratıcılık önemli
        'blog' => [
            'brand_context'      => 6500,  // Marka sesi çok önemli
            'expert_knowledge'   => 6000,  // Yazım teknikleri önemli
            'secret_knowledge'   => 5500,  // Yaratıcı içgörüler değerli
        ],
        
        // Çeviri feature'ları - Formatı koru, basit context
        'translation' => [
            'response_format'    => 7000,  // Orijinal format korunmalı
            'brand_context'      => 2000,  // Marka sesi az önemli
            'expert_knowledge'   => 6500,  // Dil teknikleri önemli
        ],
        
        // Analiz feature'ları - Teknik detay odaklı
        'analysis' => [
            'expert_knowledge'   => 8000,  // Analiz teknikleri kritik
            'response_format'    => 6000,  // Yapılandırılmış rapor önemli
            'brand_context'      => 3500,  // Az marka etkisi
        ],
    ];

    /**
     * 🔥 V2 FEATURE: Provider-specific cost multipliers
     * Her provider için farklı kredi maliyeti hesabı
     */
    public const PROVIDER_MULTIPLIERS = [
        'openai-gpt-4' => [
            'cost_multiplier' => 2.5,    // Pahalı ama kaliteli
            'quality_score' => 95,       // En yüksek kalite
            'speed_score' => 85,         // Orta hız
        ],
        'openai-gpt-3.5' => [
            'cost_multiplier' => 1.0,    // Standart fiyat
            'quality_score' => 80,       // İyi kalite
            'speed_score' => 95,         // Çok hızlı
        ],
        'claude-3' => [
            'cost_multiplier' => 2.0,    // Orta-yüksek fiyat
            'quality_score' => 90,       // Çok iyi kalite
            'speed_score' => 80,         // İyi hız
        ],
        'gemini-pro' => [
            'cost_multiplier' => 1.5,    // Orta fiyat
            'quality_score' => 75,       // Kabul edilebilir kalite
            'speed_score' => 90,         // Çok hızlı
        ],
    ];

    /**
     * 🔥 V2 FEATURE: Brand context intelligent usage patterns
     * Feature türüne göre brand context kullanım stratejisi
     */
    public const BRAND_USAGE_PATTERNS = [
        'high_brand' => ['blog', 'content', 'marketing', 'social', 'creative'],
        'medium_brand' => ['analysis', 'translation', 'summary', 'rewrite'],
        'low_brand' => ['seo', 'technical', 'code', 'data', 'calculation'],
        'no_brand' => ['math', 'conversion', 'format', 'validation'],
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
        
        // V2 Feature: Component'leri score'la ve sırala - options ile
        $scoredComponents = self::scoreComponents($components, $options);
        
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
     * 🔥 V2 ENHANCED: Component'leri score'la - Feature-specific weights ile
     */
    private static function scoreComponents(array $components, array $options = []): array
    {
        $scoredComponents = [];
        
        // V2 Feature: Feature türünü detect et
        $featureName = $options['feature_name'] ?? '';
        $featureType = self::detectFeatureType($featureName);
        
        foreach ($components as $component) {
            $category = $component['category'] ?? 'conditional_info';
            $priority = $component['priority'] ?? 3;
            $content = $component['content'] ?? '';
            $position = $component['position'] ?? 0;
            
            // V2 Feature: Feature-specific weight uygula
            $baseWeight = self::getFeatureAwareWeight($category, $featureType);
            
            // Priority multiplier uygula
            $multiplier = self::PRIORITY_MULTIPLIERS[$priority] ?? 1.0;
            
            // Position bonus (expert prompts için)
            $positionBonus = 0;
            if ($category === 'expert_knowledge' && $position > 0) {
                $positionBonus = 100 - ($position * 5); // 1=95, 2=90, 3=85...
            }
            
            // V2 Feature: Brand context intelligent usage
            $brandBonus = 0;
            if ($category === 'brand_context') {
                $brandBonus = self::calculateBrandBonus($featureType);
            }
            
            // Final score hesapla
            $finalScore = intval($baseWeight * $multiplier) + $positionBonus + $brandBonus;
            
            $scoredComponents[] = [
                'category' => $category,
                'priority' => $priority,
                'position' => $position,
                'content' => $content,
                'base_weight' => $baseWeight,
                'multiplier' => $multiplier,
                'position_bonus' => $positionBonus,
                'brand_bonus' => $brandBonus,
                'final_score' => $finalScore,
                'name' => $component['name'] ?? $category,
                'feature_type' => $featureType, // V2 Debug bilgisi
            ];
        }
        
        return $scoredComponents;
    }

    /**
     * 🔥 V2 FEATURE: Feature türünü otomatik detect et
     */
    private static function detectFeatureType(string $featureName): string
    {
        $featureName = strtolower($featureName);
        
        // SEO keywords
        if (str_contains($featureName, 'seo') || 
            str_contains($featureName, 'meta') || 
            str_contains($featureName, 'anahtar') ||
            str_contains($featureName, 'optimizasyon')) {
            return 'seo';
        }
        
        // Blog/Content keywords  
        if (str_contains($featureName, 'blog') || 
            str_contains($featureName, 'makale') || 
            str_contains($featureName, 'icerik') ||
            str_contains($featureName, 'yazi') ||
            str_contains($featureName, 'content')) {
            return 'blog';
        }
        
        // Translation keywords
        if (str_contains($featureName, 'cevir') || 
            str_contains($featureName, 'translate') || 
            str_contains($featureName, 'dil')) {
            return 'translation';
        }
        
        // Analysis keywords
        if (str_contains($featureName, 'analiz') || 
            str_contains($featureName, 'rapor') || 
            str_contains($featureName, 'degerlend') ||
            str_contains($featureName, 'analysis')) {
            return 'analysis';
        }
        
        // Default
        return 'general';
    }

    /**
     * 🔥 V2 FEATURE: Feature türüne göre weight hesapla
     */
    private static function getFeatureAwareWeight(string $category, string $featureType): int
    {
        // Feature-specific weight varsa onu kullan
        if (isset(self::FEATURE_SPECIFIC_WEIGHTS[$featureType][$category])) {
            return self::FEATURE_SPECIFIC_WEIGHTS[$featureType][$category];
        }
        
        // Yoksa base weight kullan
        return self::BASE_WEIGHTS[$category] ?? 1000;
    }

    /**
     * 🔥 V2 FEATURE: Brand context için intelligent bonus hesapla
     */
    private static function calculateBrandBonus(string $featureType): int
    {
        // Feature türüne göre brand importance level belirle
        foreach (self::BRAND_USAGE_PATTERNS as $level => $patterns) {
            if (in_array($featureType, $patterns)) {
                return match($level) {
                    'high_brand' => 1000,    // Blog, content için yüksek bonus
                    'medium_brand' => 500,   // Analysis için orta bonus
                    'low_brand' => -500,     // SEO için penalty
                    'no_brand' => -1000,     // Technical için büyük penalty
                    default => 0
                };
            }
        }
        
        return 0; // Default: bonus yok
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
                'ai_model' => self::getCurrentProviderModel(),
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
        
        // 1. Ortak özellikler (System Common) + Basit Yanıt Formatı
        $commonPrompt = \Modules\AI\App\Models\Prompt::getCommon();
        $commonContent = $commonPrompt ? $commonPrompt->content : '';
        
        // Basit yanıt formatı kuralları ekle
        $simpleResponseRules = "🚨 YANIT FORMATI - KRİTİK KURALLAR:\n" .
            "- Sadece direkten yanıt yaz, başlık ekleme\n" . 
            "- Hiç markdown başlık kullanma (# ## ### yasak)\n" . 
            "- 'İşte yanıtınız:', 'Bu sorunun yanıtı:', 'Aşağıdaki gibi yapabilirsiniz:' gibi giriş cümleleri YASAK\n" .
            "- Uzun yanıtlarda MUTLAKA paragraf ayırımı yap (çift satır sonu ile)\n" .
            "- Her ana fikri ayrı paragrafa yaz - okunurluğu artır\n" .
            "- Direk konuya gir, konuyu açıkla, bitir\n" .
            "- Örnek: 'Bu makaleyi SEO için optimize etmek için...\n\nAnahtar kelimeler önemlidir çünkü...\n\nSonuç olarak...' (DOĞRU)\n" .
            "- Örnek: '# SEO Optimizasyonu\n\nİşte makalenizi optimize etme yolları...' (YANLIŞ)\n\n";
            
        $components[] = [
            'category' => 'system_common',
            'priority' => 1,
            'content' => $simpleResponseRules . ($commonContent ? "---\n\n" . $commonContent : ''),
            'name' => 'Ortak Özellikler + Basit Format'
        ];
        
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
            
            // SEO ve teknik feature'lar için brand context'i devre dışı bırak
            $featureSlug = $options['feature_slug'] ?? '';
            $isContentFeature = in_array($featureSlug, [
                'seo-analiz', 'meta-etiket-olustur', 'anahtar-kelime-analiz',
                'icerik-optimizasyon', 'makale-yaz', 'blog-post-yaz',
                'metin-duzelt', 'gramer-kontrol', 'metin-ozetle',
                'cevirmen', 'dil-cevirisi', 'ingilizce-turkce'
            ]);
            
            // Eğer içerik/SEO feature ise brand context'i dahil etme
            if ($isContentFeature) {
                \Log::info('🎯 Brand context disabled for content feature', [
                    'feature_slug' => $featureSlug,
                    'reason' => 'content_feature_detected'
                ]);
                return $components;
            }
            
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
    public static function getFeatureComponents($feature, array $options = []): array
    {
        $components = [];
        
        // Quick Prompt
        if ($feature->hasQuickPrompt()) {
            $components[] = [
                'category' => 'feature_definition',
                'priority' => 1,
                'content' => "=== GÖREV TANIMI ===\n" . $feature->quick_prompt,
                'name' => 'Quick Prompt',
                'type' => 'quick_prompt'
            ];
        }
        
        // Expert Prompts (yeni ai_feature_prompt_relations tablosundan) - FIXED: Dual foreign key sistem
        try {
            // İki tip prompt var: ai_prompts (universal) ve ai_feature_prompts (expert)
            $expertPromptRelations = \DB::table('ai_feature_prompt_relations as rel')
                // AI_FEATURE_PROMPTS tablosundan expert prompt'ları çek
                ->leftJoin('ai_feature_prompts as afp', 'rel.feature_prompt_id', '=', 'afp.id')
                // AI_PROMPTS tablosundan universal prompt'ları da çek (yazım tonu, uzunluk vs.)
                ->leftJoin('ai_prompts as p', 'rel.prompt_id', '=', 'p.prompt_id')
                ->where('rel.feature_id', $feature->id)
                ->where('rel.is_active', true)
                ->where(function($query) {
                    // Expert prompt VEYA universal prompt aktif olmalı
                    $query->where(function($q1) {
                        $q1->whereNotNull('afp.id')->where('afp.is_active', true);
                    })->orWhere(function($q2) {
                        $q2->whereNotNull('p.prompt_id')->where('p.is_active', true);
                    });
                })
                ->orderBy('rel.priority', 'asc')
                ->select(
                    \DB::raw('COALESCE(afp.expert_prompt, p.content) as content'),
                    \DB::raw('COALESCE(afp.name, p.name) as name'),
                    'rel.priority', 
                    'rel.role',
                    \DB::raw('CASE WHEN afp.id IS NOT NULL THEN "expert" ELSE "universal" END as prompt_source')
                )
                ->get();
                
            foreach ($expertPromptRelations as $index => $promptRel) {
                $role = $promptRel->role ?? 'primary';
                $expertPriority = $promptRel->priority ?? ($index + 1);
                $promptSource = $promptRel->prompt_source ?? 'expert';
                
                // Source'a göre farklı category ve formatting
                if ($promptSource === 'universal') {
                    // Universal prompt (yazım tonu, uzunluk vs.)
                    $components[] = [
                        'category' => 'system_common', // Universal prompt'lar system_common kategorisinde
                        'priority' => min($expertPriority, 3), 
                        'position' => $expertPriority,
                        'content' => "=== UNIVERSAL RULE ({$promptRel->name}) ===\n" . $promptRel->content,
                        'name' => "Universal: {$promptRel->name}",
                        'type' => 'universal_prompt'
                    ];
                } else {
                    // Expert prompt
                    $components[] = [
                        'category' => 'expert_knowledge',
                        'priority' => min($expertPriority, 4), // Max 4 priority
                        'position' => $expertPriority,
                        'content' => "=== UZMAN BİLGİSİ ({$role}) ===\n" . $promptRel->content,
                        'name' => "Expert Prompt #{$expertPriority} ({$promptRel->name})",
                        'type' => 'expert_prompt'
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Expert prompts loading failed', [
                'feature_id' => $feature->id,
                'error' => $e->getMessage()
            ]);
        }
        
        // 🔥 NEW: OPTIONS-BASED UNIVERSAL PROMPTS
        // Kullanıcı seçimlerine göre dinamik universal prompt'ları ekle
        self::addOptionsBasedUniversalPrompts($components, $options ?? []);
        
        // 🎨 V2 ENHANCED: Response Template with ResponseTemplateEngine
        if ($feature->hasResponseTemplate()) {
            // Geleneksel template format
            $traditionalTemplate = "=== YANIT FORMATI ===\n" . $feature->getFormattedTemplate();
            
            // V2 ResponseTemplateEngine entegrasyonu
            try {
                $templateEngine = new ResponseTemplateEngine();
                $enhancedTemplate = $templateEngine->buildTemplateAwarePrompt($feature, $options ?? []);
                
                // Enhanced template varsa onu kullan, yoksa traditional'ı kullan
                $finalTemplate = !empty($enhancedTemplate) ? $enhancedTemplate : $traditionalTemplate;
                
            } catch (\Exception $e) {
                Log::warning('ResponseTemplateEngine integration failed, fallback to traditional', [
                    'feature_slug' => $feature->slug,
                    'error' => $e->getMessage()
                ]);
                $finalTemplate = $traditionalTemplate;
            }
            
            $components[] = [
                'category' => 'response_format',
                'priority' => 2,
                'content' => $finalTemplate,
                'name' => 'Response Template (V2 Enhanced)',
                'type' => 'response_template'
            ];
        } else {
            // Feature'da response template yoksa, basic anti-monotony rules ekle
            $basicAntiMonotony = ResponseTemplateEngine::getQuickAntiMonotonyPrompt($feature->slug);
            
            $components[] = [
                'category' => 'response_format',
                'priority' => 3, // Biraz daha düşük priority
                'content' => $basicAntiMonotony,
                'name' => 'Anti-Monotony Rules (V2)'
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
            $components = array_merge($components, self::getFeatureComponents($options['feature'], $options));
        }
        
        
        // Custom components varsa ekle
        if (isset($options['custom_components'])) {
            $components = array_merge($components, $options['custom_components']);
        }
        
        return self::buildSystemPrompt($components, $options);
    }

    /**
     * 🔥 NEW: OPTIONS-BASED UNIVERSAL PROMPTS
     * Kullanıcı seçimlerine göre dinamik universal prompt'ları ekle
     */
    private static function addOptionsBasedUniversalPrompts(array &$components, array $options): void
    {
        try {
            // Yazım tonu seçimi varsa ekle
            if (isset($options['writing_tone']) && !empty($options['writing_tone'])) {
                $writingTonePrompt = \DB::table('ai_prompts')
                    ->where('prompt_type', 'writing_tone')
                    ->where('slug', $options['writing_tone'])
                    ->where('is_active', true)
                    ->first();
                    
                if ($writingTonePrompt) {
                    $components[] = [
                        'category' => 'system_common',
                        'priority' => 1, // Yüksek priority - çok önemli
                        'content' => "=== YAZIM TONU ===\n" . $writingTonePrompt->content,
                        'name' => "Yazım Tonu: {$writingTonePrompt->name}",
                        'type' => 'writing_tone_selection'
                    ];
                    
                    \Log::info('🎯 Writing tone prompt added', [
                        'tone' => $options['writing_tone'],
                        'prompt_name' => $writingTonePrompt->name
                    ]);
                }
            }
            
            // İçerik uzunluğu seçimi varsa ekle
            if (isset($options['content_length']) && !empty($options['content_length'])) {
                $contentLengthPrompt = \DB::table('ai_prompts')
                    ->where('prompt_type', 'content_length')
                    ->where('slug', $options['content_length'])
                    ->where('is_active', true)
                    ->first();
                    
                if ($contentLengthPrompt) {
                    $components[] = [
                        'category' => 'system_common',
                        'priority' => 1, // Yüksek priority - çok önemli
                        'content' => "=== İÇERİK UZUNLUĞU ===\n" . $contentLengthPrompt->content,
                        'name' => "İçerik Uzunluğu: {$contentLengthPrompt->name}",
                        'type' => 'content_length_selection'
                    ];
                    
                    \Log::info('🎯 Content length prompt added', [
                        'length' => $options['content_length'],
                        'prompt_name' => $contentLengthPrompt->name
                    ]);
                }
            }
            
            // Gelecekte başka universal prompt türleri için genişletilebilir:
            // - format_type (json, markdown, html vs.)
            // - complexity_level (basic, intermediate, advanced)
            // - audience_type (beginner, expert, general)
            
        } catch (\Exception $e) {
            \Log::warning('Options-based universal prompts loading failed', [
                'error' => $e->getMessage(),
                'options' => array_keys($options)
            ]);
        }
    }

    /**
     * Şu anda aktif olan provider'ın model bilgisini al
     */
    public static function getCurrentProviderModel(): string
    {
        try {
            $defaultProvider = \Modules\AI\App\Models\AIProvider::getDefault();
            if ($defaultProvider) {
                return $defaultProvider->name . '/' . $defaultProvider->default_model;
            }
            
            // Fallback: İlk aktif provider'ı al
            $activeProvider = \Modules\AI\App\Models\AIProvider::getActive()->first();
            if ($activeProvider) {
                return $activeProvider->name . '/' . $activeProvider->default_model;
            }
            
            return 'unknown/unknown';
        } catch (\Exception $e) {
            return 'unknown/error';
        }
    }

}