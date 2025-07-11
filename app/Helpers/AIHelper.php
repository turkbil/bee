<?php

use App\Facades\AI;
use Modules\AI\App\Models\AIFeature;

/**
 * AI Helper Functions - MERKEZİ REPOSITORY SİSTEMİ
 * 
 * TÜM AI YANITLARI AIResponseRepository ÜZERİNDEN YÖNETİLİR:
 * - Tek dosyada tüm yanıt formatları
 * - Tutarlı error handling
 * - Merkezi caching sistemi
 * - Unified token management
 * - Kolay maintenance
 * 
 * Bu dosya composer.json autoload files bölümüne eklenerek
 * tüm projede global fonksiyonlar olarak kullanılabilir.
 */

// ============================================================================
// CORE AI HELPER FUNCTIONS - REPOSITORY PATTERN
// ============================================================================

if (!function_exists('ai_get_settings')) {
    /**
     * Merkezi AI ayarlarını döndürür (global cache'li)
     * Tüm helper'lar ve servisler bu fonksiyonu kullanmalı
     */
    function ai_get_settings(): ?\Modules\AI\App\Models\Setting
    {
        static $settings = null;
        
        if ($settings === null) {
            try {
                // AI ayarları sadece central domain'de - tenancy bypass
                $previousTenantId = tenancy()->tenant?->id ?? null;
                
                // Tenancy'yi bypass et
                tenancy()->end();
                
                $settings = \Cache::remember('ai_global_settings', 3600, function () {
                    return \Modules\AI\App\Models\Setting::first();
                });
                
                // Eski tenant context'i geri yükle
                if ($previousTenantId && class_exists('\App\Models\Tenant')) {
                    $tenant = \App\Models\Tenant::find($previousTenantId);
                    if ($tenant) {
                        tenancy()->initialize($tenant);
                    }
                }
            } catch (\Exception $e) {
                // Fallback - direkt database (tenancy bypass)
                try {
                    tenancy()->end();
                    $settings = \Modules\AI\App\Models\Setting::first();
                } catch (\Exception $e2) {
                    $settings = false; // Cache false değer (tekrar denemeyi önler)
                }
            }
        }
        
        return $settings === false ? null : $settings;
    }
}

if (!function_exists('ai_get_api_key')) {
    /**
     * Merkezi API anahtarı döndürür
     * Tüm AI servisleri bu fonksiyonu kullanmalı
     */
    function ai_get_api_key(): ?string
    {
        $settings = ai_get_settings();
        
        if (!$settings || empty($settings->api_key)) {
            return null;
        }
        
        return $settings->api_key;
    }
}

if (!function_exists('ai_get_model')) {
    /**
     * Merkezi AI model döndürür
     */
    function ai_get_model(): string
    {
        $settings = ai_get_settings();
        return $settings?->model ?? 'deepseek-chat';
    }
}

if (!function_exists('ai_is_enabled')) {
    /**
     * AI sisteminin aktif olup olmadığını kontrol eder
     */
    function ai_is_enabled(): bool
    {
        $settings = ai_get_settings();
        return $settings?->enabled ?? false;
    }
}

if (!function_exists('ai_get_repository')) {
    /**
     * Merkezi AI Response Repository'yi döndürür
     */
    function ai_get_repository(): \Modules\AI\App\Services\AIResponseRepository
    {
        return app(\Modules\AI\App\Services\AIResponseRepository::class);
    }
}

if (!function_exists('ai')) {
    /**
     * AI facade'ine kısa yoldan erişim
     * 
     * @return \App\Services\AI\AIServiceManager
     */
    function ai()
    {
        return app(\App\Services\AI\AIServiceManager::class);
    }
}

if (!function_exists('ai_for_module')) {
    /**
     * Belirtilen modül için AI builder döndür
     * 
     * @param string $module
     * @return \App\Facades\ModuleAIBuilder
     */
    function ai_for_module(string $module)
    {
        return AI::forModule($module);
    }
}

if (!function_exists('ai_execute_feature_template')) {
    /**
     * YENİ TEMPLATE SİSTEMİ - Feature'ı yeni template sistemi ile çalıştır
     * 
     * @param string $featureSlug Feature slug'ı
     * @param string $userMessage Kullanıcı mesajı
     * @param array $userInput Kullanıcı girdileri (content, language, vb.)
     * @param bool $stream Streaming yanıt mı?
     * @return string|null|\Closure
     */
    function ai_execute_feature_template(string $featureSlug, string $userMessage, array $userInput = [], bool $stream = false)
    {
        try {
            // Token kontrolü ÖNCE yap
            $tenantId = tenant('id') ?: 'default';
            $estimatedTokens = max(10, (int)(strlen($userMessage) / 4));
            
            if (!ai_can_use_tokens($estimatedTokens, $tenantId)) {
                $tokenPackagesUrl = route('admin.ai.token-packages.index');
                return "❌ **Yetersiz AI Token Bakiyesi**\n\n🛒 Token satın almak için: [{$tokenPackagesUrl}]({$tokenPackagesUrl})\n\n💰 Gerekli token: {$estimatedTokens}\n📊 Mevcut bakiye: " . ai_get_token_balance($tenantId);
            }
            
            // Feature'ı bul
            $feature = AIFeature::where('slug', $featureSlug)->where('status', 'active')->first();
            
            if (!$feature) {
                return "Belirtilen AI özelliği bulunamadı: {$featureSlug}";
            }
            
            // AI Service instance'ını al
            $aiService = app(\Modules\AI\App\Services\AIService::class);
            
            // Yeni template sistemi ile çalıştır
            $result = $aiService->askFeature($feature, $userMessage, $userInput, $stream);
            
            // Token kullanımını kaydet (AIService zaten kaydediyor ama helper tarafında da log)
            if ($result && is_string($result) && !str_contains($result, 'Yetersiz AI Token')) {
                $actualTokens = max(10, (int)((strlen($userMessage) + strlen($result)) / 4));
                // Token usage tracked
            }
            
            return $result;
            
        } catch (\Exception $e) {
            return "AI özelliği çalıştırılırken bir hata oluştu. Lütfen tekrar deneyin.";
        }
    }
}

// YENİ FEATURE HELPER FUNCTIONS - TEMPLATE SİSTEMİ İLE

if (!function_exists('ai_linkedin_thought_leader')) {
    function ai_linkedin_thought_leader(string $expertiseArea, array $options = []): array
    {
        return ai_execute_feature('linkedin-thought-leader', [
            'expertise_area' => $expertiseArea,
            'industry' => $options['industry'] ?? 'general',
            'topic' => $options['topic'] ?? 'industry_insights',
            'experience' => $options['experience'] ?? 'experienced',
            'audience' => $options['audience'] ?? 'professionals',
            'content_type' => $options['content_type'] ?? 'insight'
        ]);
    }
}

if (!function_exists('ai_customer_service_wizard')) {
    function ai_customer_service_wizard(string $customerIssue, array $options = []): array
    {
        return ai_execute_feature('customer-service-wizard', [
            'customer_issue' => $customerIssue,
            'issue_type' => $options['issue_type'] ?? 'general',
            'urgency' => $options['urgency'] ?? 'medium',
            'customer_mood' => $options['customer_mood'] ?? 'neutral',
            'complexity' => $options['complexity'] ?? 'medium',
            'channel' => $options['channel'] ?? 'email'
        ]);
    }
}

if (!function_exists('ai_job_posting_creator')) {
    function ai_job_posting_creator(string $positionDetails, array $options = []): array
    {
        return ai_execute_feature('job-posting-creator', [
            'position_details' => $positionDetails,
            'company_size' => $options['company_size'] ?? 'medium',
            'industry' => $options['industry'] ?? 'technology',
            'remote' => $options['remote'] ?? false,
            'experience' => $options['experience'] ?? '2+ years',
            'salary_range' => $options['salary_range'] ?? 'competitive',
            'team_size' => $options['team_size'] ?? 'small'
        ]);
    }
}

if (!function_exists('ai_whatsapp_business_message')) {
    function ai_whatsapp_business_message(string $messagePurpose, array $options = []): array
    {
        return ai_execute_feature('whatsapp-business-pro', [
            'message_purpose' => $messagePurpose,
            'business_type' => $options['business_type'] ?? 'general',
            'tone' => $options['tone'] ?? 'friendly',
            'include_media' => $options['include_media'] ?? false,
            'automation_level' => $options['automation_level'] ?? 'medium',
            'target_action' => $options['target_action'] ?? 'engagement'
        ]);
    }
}

if (!function_exists('ai_faq_generator')) {
    function ai_faq_generator(string $businessDescription, array $options = []): array
    {
        return ai_execute_feature('faq-generator', [
            'business_description' => $businessDescription,
            'categories' => $options['categories'] ?? ['general', 'support'],
            'business_type' => $options['business_type'] ?? 'service',
            'target_audience' => $options['target_audience'] ?? 'customers',
            'complexity_level' => $options['complexity_level'] ?? 'beginner',
            'tone' => $options['tone'] ?? 'helpful'
        ]);
    }
}

if (!function_exists('ai_meta_tag_optimizer')) {
    function ai_meta_tag_optimizer(string $pageContent, array $options = []): array
    {
        return ai_execute_feature('meta-tag-optimizer', [
            'page_content' => $pageContent,
            'keywords' => $options['keywords'] ?? [],
            'location' => $options['location'] ?? null,
            'page_type' => $options['page_type'] ?? 'general',
            'brand' => $options['brand'] ?? '',
            'competition' => $options['competition'] ?? 'medium'
        ]);
    }
}

if (!function_exists('ai_schema_markup_generator')) {
    function ai_schema_markup_generator(string $contentDescription, array $options = []): array
    {
        return ai_execute_feature('schema-markup-generator', [
            'content_description' => $contentDescription,
            'type' => $options['type'] ?? 'Article',
            'location' => $options['location'] ?? null,
            'has_reviews' => $options['has_reviews'] ?? false,
            'has_offers' => $options['has_offers'] ?? false,
            'custom_fields' => $options['custom_fields'] ?? []
        ]);
    }
}

if (!function_exists('ai_brand_story_creator')) {
    function ai_brand_story_creator(string $brandContext, array $options = []): array
    {
        // Marka hikayesi feature'ını slug ile dinamik olarak bul
        try {
            // Önce slug ile dene
            $feature = \Modules\AI\App\Models\AIFeature::where('slug', 'brand-story-creator')
                ->where('status', 'active')
                ->first();
                
            // Eğer slug ile bulunamazsa, name ile dene
            if (!$feature) {
                $feature = \Modules\AI\App\Models\AIFeature::where('name', 'LIKE', '%brand%story%')
                    ->orWhere('name', 'LIKE', '%marka%hikaye%')
                    ->where('status', 'active')
                    ->first();
            }
            
            // Eğer hala bulunamazsa, category ile dene
            if (!$feature) {
                $feature = \Modules\AI\App\Models\AIFeature::where('category', 'content-creation')
                    ->where('name', 'LIKE', '%story%')
                    ->where('status', 'active')
                    ->first();
            }
            
            if (!$feature) {
                return [
                    'success' => false,
                    'error' => 'Marka hikayesi özelliği bulunamadı. Lütfen feature\'ı aktif hale getirin.',
                    'tokens_used' => 0
                ];
            }

            // Token kontrolü
            $estimatedTokens = $feature->token_cost['estimated'] ?? 100;
            $tenantId = resolve_tenant_id() ?: 'default';
            
            if (!ai_can_use_tokens($estimatedTokens, $tenantId)) {
                return [
                    'success' => false,
                    'error' => 'Yeterli token bakiyeniz yok. Gerekli: ' . $estimatedTokens . ' token',
                    'tokens_needed' => $estimatedTokens,
                    'tokens_available' => ai_get_token_balance($tenantId)
                ];
            }

            // Input için marka context'i hazırla
            $userInput = [
                'brand_context' => $brandContext,
                'industry' => $options['industry'] ?? 'general',
                'stage' => $options['stage'] ?? 'growth',
                'mission' => $options['mission'] ?? 'customer_focused',
                'values' => $options['values'] ?? 'quality',
                'audience' => $options['audience'] ?? 'general',
                'unique_factor' => $options['unique_factor'] ?? 'innovation'
            ];

            // Feature'ın buildFinalPrompt metodunu kullan
            $finalPrompt = $feature->buildFinalPrompt([], $userInput);

            // Marka-aware AI service ile çalıştır
            $aiService = app(\Modules\AI\App\Services\AIService::class);
            $result = $aiService->askFeature($feature, $brandContext, [
                'context' => 'brand-story-creator',
                'options' => $options
            ]);

            // Token kullanımını kaydet
            ai_use_tokens($estimatedTokens, 'AI', 'brand-story-creator', $tenantId, ['brand_context' => $brandContext]);

            // Feature kullanım sayısını artır
            $feature->incrementUsage();

            return [
                'success' => true,
                'response' => $result,
                'tokens_used' => $estimatedTokens,
                'feature' => $feature->name,
                'message' => $feature->success_messages['default'] ?? 'Marka hikayeniz başarıyla oluşturuldu'
            ];

        } catch (\Exception $e) {
            \Log::error('ai_brand_story_creator error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => 'Marka hikayesi oluşturulurken hata: ' . $e->getMessage(),
                'tokens_used' => 0
            ];
        }
    }
}

if (!function_exists('ai_api_documentation')) {
    function ai_api_documentation(string $apiDescription, array $options = []): array
    {
        return ai_execute_feature('api-documentation-pro', [
            'api_description' => $apiDescription,
            'type' => $options['type'] ?? 'REST',
            'auth' => $options['auth'] ?? 'API_KEY',
            'version' => $options['version'] ?? 'v1',
            'complexity' => $options['complexity'] ?? 'medium',
            'target_audience' => $options['target_audience'] ?? 'developers'
        ]);
    }
}

if (!function_exists('ai_legal_compliance_docs')) {
    function ai_legal_compliance_docs(string $platform, array $options = []): array
    {
        return ai_execute_feature('kvkk-gdpr-expert', [
            'platform' => $platform,
            'type' => $options['type'] ?? 'website',
            'data_types' => $options['data_types'] ?? ['personal', 'usage'],
            'jurisdiction' => $options['jurisdiction'] ?? 'TR',
            'data_processing' => $options['data_processing'] ?? 'controller',
            'international_transfer' => $options['international_transfer'] ?? false
        ]);
    }
}

if (!function_exists('ai_sales_page_master')) {
    function ai_sales_page_master(string $product, array $options = []): array
    {
        return ai_execute_feature('sales-page-master', [
            'product' => $product,
            'price' => $options['price'] ?? 'contact',
            'audience' => $options['audience'] ?? 'general',
            'industry' => $options['industry'] ?? 'general',
            'competition' => $options['competition'] ?? 'medium',
            'urgency_level' => $options['urgency_level'] ?? 'medium'
        ]);
    }
}

if (!function_exists('ai_local_seo_strategy')) {
    function ai_local_seo_strategy(string $business, array $options = []): array
    {
        return ai_execute_feature('local-seo-domination', [
            'business' => $business,
            'services' => $options['services'] ?? [],
            'area' => $options['area'] ?? 'city_center',
            'competitors' => $options['competitors'] ?? [],
            'budget' => $options['budget'] ?? 'medium',
            'goals' => $options['goals'] ?? 'visibility'
        ]);
    }
}

if (!function_exists('ai_tiktok_viral_content')) {
    function ai_tiktok_viral_content(string $concept, array $options = []): array
    {
        return ai_execute_feature('tiktok-viral-factory', [
            'concept' => $concept,
            'style' => $options['style'] ?? 'general',
            'duration' => $options['duration'] ?? '30sec',
            'target_age' => $options['target_age'] ?? '18-24',
            'aesthetic' => $options['aesthetic'] ?? 'modern',
            'trending_topic' => $options['trending_topic'] ?? null
        ]);
    }
}

if (!function_exists('ai_press_release_expert')) {
    function ai_press_release_expert(string $topic, array $options = []): array
    {
        return ai_execute_feature('press-release-expert', [
            'topic' => $topic,
            'company' => $options['company'] ?? '',
            'industry' => $options['industry'] ?? 'general',
            'target_media' => $options['target_media'] ?? 'national',
            'urgency' => $options['urgency'] ?? 'standard',
            'contact_person' => $options['contact_person'] ?? ''
        ]);
    }
}

if (!function_exists('ai_execute_feature')) {
    /**
     * YENİ MERKEZI REPOSITORY SİSTEMİ - Feature'ı repository ile çalıştır
     * 
     * @param string $featureSlug Feature slug'ı
     * @param array $userInput Kullanıcı girdileri
     * @param array $conditions Çalıştırma şartları
     * @param array $options Ek seçenekler
     * @return array
     */
    function ai_execute_feature(string $featureSlug, array $userInput = [], array $conditions = [], array $options = []): array
    {
        try {
            // Token kontrolü için feature'ı önceden alalım
            $feature = AIFeature::where('slug', $featureSlug)->where('status', 'active')->first();
            if (!$feature) {
                return [
                    'success' => false,
                    'error' => "AI özelliği bulunamadı: {$featureSlug}",
                    'tokens_used' => 0
                ];
            }

            // Token kontrolü
            $estimatedTokens = $feature->token_cost['estimated'] ?? 100;
            if (isset($options['word_count'])) {
                $estimatedTokens += intval($options['word_count'] * 0.3);
            }
            
            $tenantId = tenant('id') ?: 'default';
            
            if (!ai_can_use_tokens($estimatedTokens, $tenantId)) {
                return [
                    'success' => false,
                    'error' => 'Yeterli token bakiyeniz yok. Gerekli: ' . $estimatedTokens . ' token',
                    'tokens_needed' => $estimatedTokens,
                    'tokens_available' => ai_get_token_balance($tenantId)
                ];
            }

            // Input validation
            if ($feature->input_validation) {
                $validation = ai_validate_feature_input($featureSlug, $userInput);
                if (!$validation['valid']) {
                    return ['success' => false, 'error' => $validation['message']];
                }
            }

            // ✅ YENİ MERKEZI REPOSITORY SİSTEMİ - Ana güç burada!
            $repository = ai_get_repository();
            $result = $repository->executeRequest('helper_function', [
                'helper_name' => 'ai_execute_feature',
                'feature_slug' => $featureSlug,
                'user_input' => $userInput,
                'conditions' => $conditions,
                'options' => $options
            ]);

            if (!$result['success']) {
                return [
                    'success' => false,
                    'error' => $result['error'],
                    'tokens_used' => 0
                ];
            }

            // Token kullanımını kaydet
            ai_use_tokens($estimatedTokens, $tenantId, [
                'source' => 'ai_helper',
                'helper_name' => 'ai_execute_feature',
                'feature_slug' => $featureSlug
            ]);

            // Feature kullanım sayısını artır
            $feature->incrementUsage();

            // Word buffer yapılandırması ekle
            $wordBufferConfig = [
                'enabled' => true,
                'delay_between_words' => 170,
                'animation_duration' => 4300,
                'container_selector' => '.ai-helper-response',
                'helper_name' => 'ai_execute_feature',
                'feature_name' => $feature->name
            ];

            return [
                'success' => true,
                'response' => $result['response'],
                'formatted_response' => $result['formatted_response'],
                'feature' => $result['feature'],
                'tokens_used' => $estimatedTokens,
                'helper_name' => $result['helper_name'],
                'word_buffer_enabled' => true,
                'word_buffer_config' => $wordBufferConfig
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Feature çalıştırma hatası: ' . $e->getMessage(),
                'tokens_used' => 0
            ];
        }
    }
}

// ============================================================================
// İÇERİK OLUŞTURMA ÖZELLİKLERİ
// ============================================================================

if (!function_exists('ai_seo_content_generation')) {
    /**
     * SEO İçerik Üretimi - Google'da 1. sayfada yer alacak içerikler
     */
    function ai_seo_content_generation(string $topic, string $target_keyword, array $options = []): array
    {
        return ai_execute_feature('seo-content-generation', [
            'content' => $topic,
            'target_keyword' => $target_keyword,
            'language' => $options['language'] ?? 'Turkish',
            'level' => $options['level'] ?? 'intermediate',
            'type' => 'seo_content',
            'audience' => $options['target_audience'] ?? 'general',
            'word_count' => $options['length'] === 'long' ? '2000' : '1000',
            'tone' => $options['tone'] ?? 'professional'
        ], [
            'language' => [$options['language'] ?? 'tr'],
            'content_type' => ['seo_content', 'blog'],
            'user_level' => ['beginner', 'intermediate', 'advanced']
        ], $options);
    }
}

if (!function_exists('ai_blog_writing_pro')) {
    /**
     * Blog Yazısı Pro - Viral olacak blog yazıları
     */
    function ai_blog_writing_pro(string $topic, array $options = []): array
    {
        return ai_execute_feature('blog-writing-pro', [
            'content' => $topic,
            'language' => $options['language'] ?? 'Turkish',
            'level' => $options['level'] ?? 'intermediate',
            'type' => 'blog_post',
            'audience' => $options['target_audience'] ?? 'general',
            'word_count' => $options['word_count'] ?? '1200',
            'tone' => $options['tone'] ?? 'educational'
        ], [
            'language' => [$options['language'] ?? 'tr'],
            'content_type' => ['blog', 'article'],
            'user_level' => ['beginner', 'intermediate', 'advanced'],
            'input_length' => ['min' => 5, 'max' => 1000]
        ], $options);
    }
}

if (!function_exists('ai_content_creation')) {
    /**
     * İçerik Oluşturma - Genel içerik üretimi
     */
    function ai_content_creation(string $topic, string $type = 'blog', array $options = []): array
    {
        return ai_execute_feature('content-creation', [
            'content' => $topic,
            'type' => $type,
            'language' => $options['language'] ?? 'Turkish',
            'tone' => $options['tone'] ?? 'professional',
            'word_count' => $options['word_count'] ?? '500'
        ], [
            'content_type' => [$type],
            'language' => [$options['language'] ?? 'tr']
        ], $options);
    }
}

if (!function_exists('ai_template_content')) {
    /**
     * Şablondan İçerik - Hazır şablonlar kullanarak içerik üretimi
     */
    function ai_template_content(string $template_type, array $data = []): array
    {
        return ai_execute_feature('template-content', [
            'template_type' => $template_type,
            'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'language' => $data['language'] ?? 'Turkish'
        ], [
            'content_type' => ['template', $template_type]
        ]);
    }
}

if (!function_exists('ai_headline_alternatives')) {
    /**
     * Başlık Alternatifleri - Çekici başlık seçenekleri
     */
    function ai_headline_alternatives(string $topic, int $count = 5, string $style = 'seo'): array
    {
        return ai_execute_feature('headline-alternatives', [
            'content' => $topic,
            'count' => $count,
            'style' => $style,
            'type' => 'headline'
        ], [
            'content_type' => ['headline', $style]
        ]);
    }
}

if (!function_exists('ai_content_summary')) {
    /**
     * İçerik Özeti - Uzun metinlerin özetini çıkarır
     */
    function ai_content_summary(string $content, string $length = 'medium'): array
    {
        return ai_execute_feature('content-summary', [
            'content' => $content,
            'summary_length' => $length,
            'type' => 'summary'
        ], [
            'input_length' => ['min' => 100],
            'content_type' => ['summary']
        ]);
    }
}

if (!function_exists('ai_faq_generation')) {
    /**
     * SSS Oluşturma - İçerik bazlı SSS listesi
     */
    function ai_faq_generation(string $topic, int $count = 10): array
    {
        return ai_execute_feature('faq-generation', [
            'content' => $topic,
            'count' => $count,
            'type' => 'faq'
        ], [
            'content_type' => ['faq']
        ]);
    }
}

if (!function_exists('ai_call_to_action')) {
    /**
     * Eylem Çağrısı - CTA metinleri oluşturur
     */
    function ai_call_to_action(string $action, string $context = '', int $count = 3): array
    {
        return ai_execute_feature('call-to-action', [
            'action' => $action,
            'context' => $context,
            'count' => $count,
            'type' => 'cta'
        ], [
            'content_type' => ['cta', 'marketing']
        ]);
    }
}

// ============================================================================
// SEO ÖZELLİKLERİ
// ============================================================================

if (!function_exists('ai_seo_analysis')) {
    /**
     * SEO Analizi - İçerik SEO performansını analiz eder
     */
    function ai_seo_analysis(string $content, string $target_keyword = ''): array
    {
        return ai_execute_feature('seo-analysis', [
            'content' => $content,
            'target_keyword' => $target_keyword,
            'type' => 'analysis'
        ], [
            'content_type' => ['seo', 'analysis'],
            'input_length' => ['min' => 50]
        ]);
    }
}

if (!function_exists('ai_keyword_extraction')) {
    /**
     * Anahtar Kelime Çıkarma - İçerikten SEO anahtar kelimeleri
     */
    function ai_keyword_extraction(string $content, int $count = 10): array
    {
        return ai_execute_feature('keyword-extraction', [
            'content' => $content,
            'count' => $count,
            'type' => 'keyword_extraction'
        ], [
            'content_type' => ['seo', 'keywords'],
            'input_length' => ['min' => 100]
        ]);
    }
}

if (!function_exists('ai_seo_optimization')) {
    /**
     * SEO Optimizasyonu - İçeriği SEO dostu hale getirir
     */
    function ai_seo_optimization(string $content, string $target_keyword, array $options = []): array
    {
        return ai_execute_feature('seo-optimization', [
            'content' => $content,
            'target_keyword' => $target_keyword,
            'type' => 'optimization',
            'options' => json_encode($options, JSON_UNESCAPED_UNICODE)
        ], [
            'content_type' => ['seo', 'optimization'],
            'input_length' => ['min' => 100]
        ], $options);
    }
}

if (!function_exists('ai_meta_tag_generation')) {
    /**
     * Meta Etiket Oluşturma - SEO meta etiketleri
     */
    function ai_meta_tag_generation(string $content, string $page_title = ''): array
    {
        return ai_execute_feature('meta-tag-generation', [
            'content' => $content,
            'page_title' => $page_title,
            'type' => 'meta_tags'
        ], [
            'content_type' => ['seo', 'meta'],
            'input_length' => ['min' => 50]
        ]);
    }
}

// ============================================================================
// ANALİZ ÖZELLİKLERİ
// ============================================================================

if (!function_exists('ai_readability_analysis')) {
    /**
     * Okunabilirlik Analizi - Metin okunabilirlik skorunu değerlendirir
     */
    function ai_readability_analysis(string $content): array
    {
        return ai_execute_feature('readability-analysis', [
            'content' => $content,
            'type' => 'readability'
        ], [
            'content_type' => ['analysis', 'readability'],
            'input_length' => ['min' => 100]
        ]);
    }
}

if (!function_exists('ai_tone_analysis')) {
    /**
     * Ton Analizi - Yazı tonunu analiz eder
     */
    function ai_tone_analysis(string $content): array
    {
        return ai_execute_feature('tone-analysis', [
            'content' => $content,
            'type' => 'tone_analysis'
        ], [
            'content_type' => ['analysis', 'tone'],
            'input_length' => ['min' => 50]
        ]);
    }
}

if (!function_exists('ai_improvement_suggestions')) {
    /**
     * İyileştirme Önerileri - İçerik geliştirme önerileri
     */
    function ai_improvement_suggestions(string $content): array
    {
        return ai_execute_feature('improvement-suggestions', [
            'content' => $content,
            'type' => 'improvement'
        ], [
            'content_type' => ['analysis', 'improvement'],
            'input_length' => ['min' => 100]
        ]);
    }
}

// ============================================================================
// SOSYAL MEDYA ÖZELLİKLERİ
// ============================================================================

if (!function_exists('ai_twitter_viral_content')) {
    /**
     * Twitter Viral İçerik - RT'lenecek, beğenilecek tweet'ler
     */
    function ai_twitter_viral_content(string $topic, array $options = []): array
    {
        $type = $options['type'] ?? 'single';
        return ai_execute_feature('twitter-viral-content', [
            'content' => $topic,
            'type' => $type,
            'tweet_count' => $options['tweet_count'] ?? 1,
            'style' => $options['style'] ?? 'professional'
        ], [
            'content_type' => ['social', 'twitter', $type],
            'platform' => ['twitter']
        ], $options);
    }
}

if (!function_exists('ai_social_media_posts')) {
    /**
     * Sosyal Medya Postları - Platform özel içerikler
     */
    function ai_social_media_posts(string $content, array $platforms = ['facebook', 'twitter', 'linkedin']): array
    {
        return ai_execute_feature('social-media-posts', [
            'content' => $content,
            'platforms' => implode(', ', $platforms),
            'type' => 'social_posts'
        ], [
            'content_type' => ['social', 'posts'],
            'platform' => $platforms
        ]);
    }
}

if (!function_exists('ai_instagram_content')) {
    /**
     * Instagram İçeriği - Instagram özel postlar
     */
    function ai_instagram_content(string $topic, string $type = 'post', array $options = []): array
    {
        return ai_execute_feature('instagram-content', [
            'content' => $topic,
            'post_type' => $type,
            'style' => $options['style'] ?? 'trendy',
            'hashtag_count' => $options['hashtag_count'] ?? 30
        ], [
            'content_type' => ['social', 'instagram', $type],
            'platform' => ['instagram']
        ], $options);
    }
}

if (!function_exists('ai_linkedin_professional')) {
    /**
     * LinkedIn Profesyonel - İş dünyası odaklı içerikler
     */
    function ai_linkedin_professional(string $topic, string $type = 'post', array $options = []): array
    {
        return ai_execute_feature('linkedin-professional', [
            'content' => $topic,
            'post_type' => $type,
            'tone' => $options['tone'] ?? 'professional',
            'target_audience' => $options['target_audience'] ?? 'professionals'
        ], [
            'content_type' => ['social', 'linkedin', $type],
            'platform' => ['linkedin'],
            'user_level' => ['professional', 'business']
        ], $options);
    }
}

// ============================================================================
// ÇEVİRİ VE DİL ÖZELLİKLERİ
// ============================================================================

if (!function_exists('ai_content_translation')) {
    /**
     * İçerik Çevirisi - Çoklu dil desteği
     */
    function ai_content_translation(string $content, string $target_language, string $source_language = 'auto'): array
    {
        return ai_execute_feature('content-translation', [
            'content' => $content,
            'target_language' => $target_language,
            'source_language' => $source_language,
            'type' => 'translation'
        ], [
            'content_type' => ['translation'],
            'language' => [$source_language, $target_language],
            'input_length' => ['min' => 10]
        ]);
    }
}

if (!function_exists('ai_content_rewriting')) {
    /**
     * İçerik Yeniden Yazma - Farklı stilde yeniden düzenler
     */
    function ai_content_rewriting(string $content, string $style = 'professional', array $options = []): array
    {
        return ai_execute_feature('content-rewriting', [
            'content' => $content,
            'style' => $style,
            'type' => 'rewriting',
            'options' => json_encode($options, JSON_UNESCAPED_UNICODE)
        ], [
            'content_type' => ['rewriting', $style],
            'input_length' => ['min' => 50]
        ], $options);
    }
}

// ============================================================================
// YARATICI ÖZELLİKLER
// ============================================================================

if (!function_exists('ai_creative_writing')) {
    /**
     * Yaratıcı Yazım - Hikaye, şiir, yaratıcı metinler
     */
    function ai_creative_writing(string $prompt, string $type = 'story', array $options = []): array
    {
        return ai_execute_feature('creative-writing', [
            'content' => $prompt,
            'creative_type' => $type,
            'style' => $options['style'] ?? 'narrative',
            'tone' => $options['tone'] ?? 'engaging',
            'word_count' => $options['word_count'] ?? '500'
        ], [
            'content_type' => ['creative', $type],
            'user_level' => ['creative', 'advanced']
        ], $options);
    }
}

if (!function_exists('ai_brainstorming')) {
    /**
     * Beyin Fırtınası - Yaratıcı fikir üretimi
     */
    function ai_brainstorming(string $topic, string $focus = 'general', int $ideas_count = 10): array
    {
        return ai_execute_feature('brainstorming', [
            'content' => $topic,
            'focus' => $focus,
            'ideas_count' => $ideas_count,
            'type' => 'brainstorming'
        ], [
            'content_type' => ['creative', 'brainstorming', $focus]
        ]);
    }
}

if (!function_exists('ai_related_topic_suggestions')) {
    /**
     * İlgili Konu Önerileri - İçerikle ilgili konular
     */
    function ai_related_topic_suggestions(string $main_topic, int $count = 10): array
    {
        return ai_execute_feature('related-topic-suggestions', [
            'content' => $main_topic,
            'count' => $count,
            'type' => 'related_topics'
        ], [
            'content_type' => ['suggestions', 'topics']
        ]);
    }
}

// ============================================================================
// TİCARİ ÖZELLİKLER
// ============================================================================

if (!function_exists('ai_email_marketing')) {
    /**
     * E-posta Pazarlama - Pazarlama e-postaları
     */
    function ai_email_marketing(string $topic, string $type = 'newsletter', array $options = []): array
    {
        return ai_execute_feature('email-marketing', [
            'content' => $topic,
            'email_type' => $type,
            'tone' => $options['tone'] ?? 'friendly',
            'target_audience' => $options['target_audience'] ?? 'subscribers',
            'cta' => $options['cta'] ?? 'subscribe'
        ], [
            'content_type' => ['marketing', 'email', $type],
            'user_level' => ['business', 'marketing']
        ], $options);
    }
}

if (!function_exists('ai_product_descriptions')) {
    /**
     * Ürün Açıklamaları - E-ticaret ürün detayları
     */
    function ai_product_descriptions(string $product_name, array $features = [], array $options = []): array
    {
        return ai_execute_feature('product-descriptions', [
            'product_name' => $product_name,
            'features' => implode(', ', $features),
            'style' => $options['style'] ?? 'persuasive',
            'target_audience' => $options['target_audience'] ?? 'customers',
            'type' => 'product_description'
        ], [
            'content_type' => ['ecommerce', 'product', 'description'],
            'user_level' => ['business', 'ecommerce']
        ], $options);
    }
}

if (!function_exists('ai_press_release')) {
    /**
     * Basın Bülteni - Profesyonel basın duyuruları
     */
    function ai_press_release(string $topic, array $details = []): array
    {
        return ai_execute_feature('press-release', [
            'content' => $topic,
            'details' => json_encode($details, JSON_UNESCAPED_UNICODE),
            'tone' => 'professional',
            'type' => 'press_release'
        ], [
            'content_type' => ['business', 'press', 'formal'],
            'user_level' => ['business', 'professional']
        ]);
    }
}

// ============================================================================
// YARDIMCI FONKSİYONLAR
// ============================================================================

if (!function_exists('ai_validate_feature_input')) {
    /**
     * Feature input validation
     */
    function ai_validate_feature_input(string $featureSlug, array $input): array
    {
        try {
            $feature = AIFeature::where('slug', $featureSlug)->first();
            if (!$feature || !$feature->input_validation) {
                return ['valid' => true]; // No validation rules
            }

            $rules = is_string($feature->input_validation) 
                ? json_decode($feature->input_validation, true) 
                : $feature->input_validation;

            $validator = \Illuminate\Support\Facades\Validator::make($input, $rules);
            
            if ($validator->fails()) {
                return [
                    'valid' => false,
                    'message' => $validator->errors()->first()
                ];
            }

            return ['valid' => true];

        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'Validation hatası: ' . $e->getMessage()
            ];
        }
    }
}

if (!function_exists('ai_get_feature_info')) {
    /**
     * Feature hakkında bilgi al
     */
    function ai_get_feature_info(string $featureSlug): array
    {
        try {
            $feature = AIFeature::where('slug', $featureSlug)->first();
            if (!$feature) {
                return ['found' => false];
            }

            return [
                'found' => true,
                'name' => $feature->name,
                'description' => $feature->description,
                'category' => $feature->getCategoryName(),
                'complexity' => $feature->getComplexityName(),
                'prompt_power' => $feature->getPromptPowerScore(),
                'has_custom_prompt' => $feature->hasCustomPrompt(),
                'has_advanced_prompts' => $feature->hasAdvancedPromptSystem(),
                'usage_count' => $feature->usage_count,
                'avg_rating' => $feature->avg_rating,
                'badges' => $feature->getBadges()
            ];

        } catch (\Exception $e) {
            return ['found' => false, 'error' => $e->getMessage()];
        }
    }
}

if (!function_exists('ai_quick_request')) {
    /**
     * Hızlı AI isteği (ham mesaj gönderimi) 
     */
    function ai_quick_request(string $message, ?string $tenantId = null, ?string $moduleContext = null): array
    {
        $tenantId = $tenantId ?: tenant('id') ?: 'default';
        
        $messages = [
            ['role' => 'user', 'content' => $message]
        ];
        
        return ai()->sendRequest($messages, $tenantId, $moduleContext);
    }
}

// ============================================================================
// LEGACY SUPPORT (ESKİ SİSTEM DESTEĞİ)
// ============================================================================

// Eski fonksiyon isimleri için alias'lar
if (!function_exists('ai_feature_seo_content_generation')) {
    function ai_feature_seo_content_generation(string $topic, string $target_keyword, array $options = []): array {
        return ai_seo_content_generation($topic, $target_keyword, $options);
    }
}

if (!function_exists('ai_feature_blog_writing_pro')) {
    function ai_feature_blog_writing_pro(string $topic, array $options = []): array {
        return ai_blog_writing_pro($topic, $options);
    }
}

if (!function_exists('ai_feature_twitter_viral_content')) {
    function ai_feature_twitter_viral_content(string $topic, array $options = []): array {
        return ai_twitter_viral_content($topic, $options);
    }
}

if (!function_exists('ai_content_create')) {
    function ai_content_create(string $topic, string $type = 'blog', array $options = []): string {
        $result = ai_content_creation($topic, $type, $options);
        return $result['success'] ? $result['content'] : 'Hata: ' . $result['error'];
    }
}

if (!function_exists('ai_content_from_template')) {
    function ai_content_from_template(string $template_type, array $data = []): string {
        $result = ai_template_content($template_type, $data);
        return $result['success'] ? $result['content'] : 'Hata: ' . $result['error'];
    }
}

// Module shortcuts
if (!function_exists('ai_page')) {
    function ai_page() { return AI::page(); }
}

if (!function_exists('ai_portfolio')) {
    function ai_portfolio() { return AI::portfolio(); }
}

if (!function_exists('ai_studio')) {
    function ai_studio() { return AI::studio(); }
}

if (!function_exists('ai_announcement')) {
    function ai_announcement() { return AI::announcement(); }
}

// Basit helper'lar
if (!function_exists('ai_page_headlines')) {
    function ai_page_headlines(string $title, string $contentType = 'blog_post', int $count = 5): array {
        return ai_headline_alternatives($title, $count, $contentType);
    }
}

if (!function_exists('ai_page_summary')) {
    function ai_page_summary(string $content, string $length = 'short'): array {
        return ai_content_summary($content, $length);
    }
}

if (!function_exists('ai_page_faq')) {
    function ai_page_faq(string $content, int $questionCount = 5): array {
        return ai_faq_generation($content, $questionCount);
    }
}

if (!function_exists('ai_page_keywords')) {
    function ai_page_keywords(string $content, int $keywordCount = 10): array {
        return ai_keyword_extraction($content, $keywordCount);
    }
}

if (!function_exists('ai_page_cta')) {
    function ai_page_cta(string $content, string $goal = 'conversion', int $ctaCount = 3): array {
        return ai_call_to_action($goal, $content, $ctaCount);
    }
}

// ============================================================================
// WHATSAPP BUSINESS ÖZELLİKLERİ
// ============================================================================

if (!function_exists('ai_whatsapp_business_message')) {
    /**
     * WhatsApp Business Pro - Profesyonel WhatsApp mesajları
     */
    function ai_whatsapp_business_message(string $messageType, array $options = []): array
    {
        return ai_execute_feature('whatsapp-business-pro', [
            'content' => $messageType,
            'business_type' => $options['business_type'] ?? 'general',
            'tone' => $options['tone'] ?? 'friendly',
            'language' => $options['language'] ?? 'Turkish',
            'include_emojis' => $options['include_emojis'] ?? true,
            'max_length' => $options['max_length'] ?? 160,
            'call_to_action' => $options['call_to_action'] ?? '',
            'contact_info' => $options['contact_info'] ?? false,
            'type' => 'whatsapp_message'
        ], [
            'content_type' => ['whatsapp', 'business_message'],
            'platform' => ['whatsapp'],
            'message_length' => ['min' => 10, 'max' => 4096]
        ], $options);
    }
}

if (!function_exists('ai_linkedin_professional_content')) {
    /**
     * LinkedIn Thought Leader - Profesyonel LinkedIn içerikleri
     */
    function ai_linkedin_professional_content(string $topic, array $options = []): array
    {
        return ai_execute_feature('linkedin-thought-leader', [
            'content' => $topic,
            'post_type' => $options['post_type'] ?? 'thought_leadership',
            'industry' => $options['industry'] ?? '',
            'business_size' => $options['business_size'] ?? 'medium',
            'target_audience' => $options['target_audience'] ?? 'professionals',
            'tone' => $options['tone'] ?? 'professional',
            'include_hashtags' => $options['include_hashtags'] ?? true,
            'call_to_action' => $options['call_to_action'] ?? 'comment',
            'language' => $options['language'] ?? 'Turkish',
            'type' => 'linkedin_post'
        ], [
            'content_type' => ['linkedin', 'professional_post'],
            'platform' => ['linkedin'],
            'post_length' => ['min' => 100, 'max' => 3000]
        ], $options);
    }
}

if (!function_exists('ai_tiktok_viral_content')) {
    /**
     * TikTok Viral Factory - Viral TikTok içerik konseptleri
     */
    function ai_tiktok_viral_content(string $concept, array $options = []): array
    {
        return ai_execute_feature('tiktok-viral-factory', [
            'content' => $concept,
            'style' => $options['style'] ?? 'lifestyle',
            'duration' => $options['duration'] ?? '30sec',
            'target_age' => $options['target_age'] ?? '18-24',
            'aesthetic' => $options['aesthetic'] ?? 'colorful',
            'trending_topic' => $options['trending_topic'] ?? '',
            'include_challenge' => $options['include_challenge'] ?? false,
            'language' => $options['language'] ?? 'Turkish',
            'type' => 'tiktok_concept'
        ], [
            'content_type' => ['tiktok', 'video_concept'],
            'platform' => ['tiktok'],
            'video_length' => ['min' => 15, 'max' => 60]
        ], $options);
    }
}

if (!function_exists('ai_instagram_growth_content')) {
    /**
     * Instagram Büyüme Paketi - Instagram büyüme odaklı içerikler
     */
    function ai_instagram_growth_content(string $topic, array $options = []): array
    {
        return ai_execute_feature('instagram-growth-pack', [
            'content' => $topic,
            'type' => $options['type'] ?? 'post',
            'audience' => $options['audience'] ?? 'general',
            'industry' => $options['industry'] ?? '',
            'goal' => $options['goal'] ?? 'engagement',
            'aesthetic' => $options['aesthetic'] ?? 'modern',
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['instagram', 'growth_content'],
            'platform' => ['instagram'],
            'engagement_type' => ['likes', 'comments', 'shares', 'followers']
        ], $options);
    }
}

if (!function_exists('ai_youtube_seo_content')) {
    /**
     * YouTube SEO Master - YouTube SEO optimizeli video içerikleri
     */
    function ai_youtube_seo_content(string $topic, array $options = []): array
    {
        return ai_execute_feature('youtube-seo-master', [
            'content' => $topic,
            'category' => $options['category'] ?? 'general',
            'duration' => $options['duration'] ?? '10-15min',
            'target_audience' => $options['target_audience'] ?? 'general',
            'skill_level' => $options['skill_level'] ?? 'intermediate',
            'competition' => $options['competition'] ?? 'medium',
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['youtube', 'seo_content'],
            'platform' => ['youtube'],
            'video_optimization' => ['title', 'description', 'tags', 'thumbnail']
        ], $options);
    }
}

if (!function_exists('ai_email_campaign_wizard')) {
    /**
     * Email Kampanya Sihirbazı - Email marketing kampanyaları
     */
    function ai_email_campaign_wizard(string $campaign_goal, array $options = []): array
    {
        return ai_execute_feature('email-campaign-wizard', [
            'content' => $campaign_goal,
            'type' => $options['type'] ?? 'newsletter',
            'audience' => $options['audience'] ?? 'subscribers',
            'industry' => $options['industry'] ?? 'general',
            'urgency' => $options['urgency'] ?? 'medium',
            'series_length' => $options['series_length'] ?? 1,
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['email', 'marketing_campaign'],
            'campaign_metrics' => ['open_rate', 'click_rate', 'conversion'],
            'email_length' => ['min' => 100, 'max' => 2000]
        ], $options);
    }
}

if (!function_exists('ai_product_description_pro')) {
    /**
     * Ürün Açıklaması Pro - E-ticaret ürün açıklamaları
     */
    function ai_product_description_pro(string $product, array $options = []): array
    {
        return ai_execute_feature('product-description-pro', [
            'content' => $product,
            'category' => $options['category'] ?? 'general',
            'price_range' => $options['price_range'] ?? 'mid',
            'target_audience' => $options['target_audience'] ?? 'customers',
            'platform' => $options['platform'] ?? 'website',
            'tone' => $options['tone'] ?? 'persuasive',
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['ecommerce', 'product_description'],
            'sales_focus' => ['features', 'benefits', 'urgency', 'social_proof'],
            'description_length' => ['min' => 150, 'max' => 800]
        ], $options);
    }
}

if (!function_exists('ai_blog_content_pro')) {
    /**
     * Blog Yazısı Pro - Profesyonel blog içerikleri
     */
    function ai_blog_content_pro(string $topic, array $options = []): array
    {
        return ai_execute_feature('blog-writing-pro', [
            'content' => $topic,
            'audience' => $options['audience'] ?? 'general',
            'tone' => $options['tone'] ?? 'educational',
            'length' => $options['length'] ?? 'medium',
            'include_seo' => $options['include_seo'] ?? true,
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['blog', 'article'],
            'engagement_factors' => ['storytelling', 'actionable_tips', 'examples'],
            'word_count' => ['min' => 800, 'max' => 3000]
        ], $options);
    }
}

if (!function_exists('ai_local_seo_strategy')) {
    /**
     * Yerel SEO Hakimiyeti - Yerel arama optimizasyonu
     */
    function ai_local_seo_strategy(string $business, array $options = []): array
    {
        return ai_execute_feature('local-seo-domination', [
            'content' => $business,
            'services' => implode(', ', $options['services'] ?? []),
            'area' => $options['area'] ?? '',
            'competitors' => implode(', ', $options['competitors'] ?? []),
            'budget' => $options['budget'] ?? 'medium',
            'goals' => $options['goals'] ?? 'top_rankings',
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['local_seo', 'business_optimization'],
            'local_factors' => ['gmb', 'citations', 'reviews', 'local_content'],
            'competition_level' => ['low', 'medium', 'high']
        ], $options);
    }
}

if (!function_exists('ai_sales_page_master')) {
    /**
     * Satış Sayfası Ustası - Dönüşüm odaklı satış sayfaları
     */
    function ai_sales_page_master(string $product, array $options = []): array
    {
        return ai_execute_feature('sales-page-master', [
            'content' => $product,
            'price' => $options['price'] ?? '',
            'audience' => $options['audience'] ?? 'general',
            'industry' => $options['industry'] ?? 'general',
            'competition' => $options['competition'] ?? 'medium',
            'urgency_level' => $options['urgency_level'] ?? 'medium',
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['sales_page', 'conversion_copy'],
            'conversion_elements' => ['headlines', 'social_proof', 'urgency', 'objections'],
            'page_length' => ['min' => 2000, 'max' => 5000]
        ], $options);
    }
}

if (!function_exists('ai_legal_compliance_docs')) {
    /**
     * KVKK & GDPR Uzmanı - Yasal uyum dökümanları
     */
    function ai_legal_compliance_docs(string $platform, array $options = []): array
    {
        return ai_execute_feature('kvkk-gdpr-expert', [
            'content' => $platform,
            'type' => $options['type'] ?? 'website',
            'data_types' => implode(', ', $options['data_types'] ?? []),
            'jurisdiction' => $options['jurisdiction'] ?? 'Turkey',
            'data_processing' => $options['data_processing'] ?? 'controller',
            'international_transfer' => $options['international_transfer'] ?? false,
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['legal', 'compliance_documents'],
            'document_types' => ['privacy_policy', 'terms_conditions', 'cookie_policy'],
            'compliance_standards' => ['KVKK', 'GDPR', 'CCPA']
        ], $options);
    }
}

if (!function_exists('ai_api_documentation')) {
    /**
     * API Dokümantasyon Pro - Kapsamlı API dökümanları
     */
    function ai_api_documentation(string $api_description, array $options = []): array
    {
        return ai_execute_feature('api-documentation-pro', [
            'content' => $api_description,
            'type' => $options['type'] ?? 'REST',
            'auth' => $options['auth'] ?? 'API_KEY',
            'version' => $options['version'] ?? 'v1',
            'complexity' => $options['complexity'] ?? 'medium',
            'target_audience' => $options['target_audience'] ?? 'developers',
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['api', 'technical_documentation'],
            'documentation_sections' => ['authentication', 'endpoints', 'examples', 'errors'],
            'formats' => ['REST', 'GraphQL', 'WebSocket']
        ], $options);
    }
}


if (!function_exists('ai_schema_markup_generator')) {
    /**
     * Schema Markup Generator - SEO strukturlanmış veriler
     */
    function ai_schema_markup_generator(string $content_description, array $options = []): array
    {
        return ai_execute_feature('schema-markup-generator', [
            'content' => $content_description,
            'type' => $options['type'] ?? 'Article',
            'location' => $options['location'] ?? '',
            'has_reviews' => $options['has_reviews'] ?? false,
            'has_offers' => $options['has_offers'] ?? false,
            'custom_fields' => implode(', ', $options['custom_fields'] ?? []),
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['schema', 'structured_data'],
            'seo_benefits' => ['rich_snippets', 'search_visibility', 'ctr_improvement'],
            'schema_types' => ['Product', 'LocalBusiness', 'Article', 'FAQ']
        ], $options);
    }
}

if (!function_exists('ai_meta_tag_optimizer')) {
    /**
     * Meta Tag Optimizer - SEO meta etiket optimizasyonu
     */
    function ai_meta_tag_optimizer(string $page_content, array $options = []): array
    {
        return ai_execute_feature('meta-tag-optimizer', [
            'content' => $page_content,
            'keywords' => implode(', ', $options['keywords'] ?? []),
            'location' => $options['location'] ?? '',
            'page_type' => $options['page_type'] ?? 'general',
            'brand' => $options['brand'] ?? '',
            'competition' => $options['competition'] ?? 'medium',
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['meta_tags', 'seo_optimization'],
            'optimization_factors' => ['title_length', 'description_length', 'keyword_density'],
            'metrics' => ['ctr_potential', 'seo_score']
        ], $options);
    }
}

if (!function_exists('ai_faq_generator')) {
    /**
     * FAQ & SSS Üretici - Kapsamlı SSS sayfaları
     */
    function ai_faq_generator(string $business_description, array $options = []): array
    {
        return ai_execute_feature('faq-generator', [
            'content' => $business_description,
            'categories' => implode(', ', $options['categories'] ?? []),
            'business_type' => $options['business_type'] ?? 'general',
            'target_audience' => $options['target_audience'] ?? 'customers',
            'complexity_level' => $options['complexity_level'] ?? 'medium',
            'tone' => $options['tone'] ?? 'helpful',
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['faq', 'customer_support'],
            'categories' => ['general', 'product', 'billing', 'technical', 'shipping'],
            'benefits' => ['customer_satisfaction', 'support_reduction', 'seo_content']
        ], $options);
    }
}

if (!function_exists('ai_whatsapp_business_message')) {
    /**
     * WhatsApp Business Pro - İş mesajları
     */
    function ai_whatsapp_business_message(string $message_purpose, array $options = []): array
    {
        return ai_execute_feature('whatsapp-business-pro', [
            'content' => $message_purpose,
            'business_type' => $options['business_type'] ?? 'general',
            'tone' => $options['tone'] ?? 'friendly',
            'include_media' => $options['include_media'] ?? false,
            'automation_level' => $options['automation_level'] ?? 'basic',
            'target_action' => $options['target_action'] ?? 'engagement',
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['whatsapp', 'business_messaging'],
            'message_types' => ['welcome', 'order_update', 'appointment', 'promotional'],
            'features' => ['automation', 'personalization', 'media_support']
        ], $options);
    }
}

if (!function_exists('ai_linkedin_thought_leader')) {
    /**
     * LinkedIn Thought Leader - Professional thought leadership içeriği
     */
    function ai_linkedin_thought_leader(string $expertise_topic, array $options = []): array
    {
        return ai_execute_feature('linkedin-thought-leader', [
            'content' => $expertise_topic,
            'industry' => $options['industry'] ?? 'general',
            'experience_level' => $options['experience_level'] ?? 'senior',
            'role' => $options['role'] ?? 'professional',
            'content_type' => $options['content_type'] ?? 'insight',
            'target_audience' => $options['target_audience'] ?? 'professionals',
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['linkedin', 'thought_leadership'],
            'professional_elements' => ['expertise', 'insights', 'networking', 'authority'],
            'engagement_factors' => ['discussion', 'shares', 'connections']
        ], $options);
    }
}

if (!function_exists('ai_case_study_writer')) {
    /**
     * Vaka Analizi Yazarı - Müşteri başarı hikayeleri
     */
    function ai_case_study_writer(string $project_description, array $options = []): array
    {
        return ai_execute_feature('case-study-writer', [
            'content' => $project_description,
            'client' => $options['client'] ?? 'anonymous',
            'industry' => $options['industry'] ?? 'general',
            'timeline' => $options['timeline'] ?? '6-12 months',
            'improvement' => $options['improvement'] ?? '',
            'challenge' => $options['challenge'] ?? '',
            'solution' => $options['solution'] ?? '',
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['case_study', 'success_story'],
            'business_elements' => ['problem', 'solution', 'results', 'roi'],
            'credibility_factors' => ['metrics', 'testimonials', 'proof']
        ], $options);
    }
}

if (!function_exists('ai_podcast_script_master')) {
    /**
     * Podcast Senaryo Ustası - Podcast episode script
     */
    function ai_podcast_script_master(string $topic, array $options = []): array
    {
        return ai_execute_feature('podcast-script-master', [
            'content' => $topic,
            'format' => $options['format'] ?? 'interview',
            'duration' => $options['duration'] ?? '30min',
            'audience' => $options['audience'] ?? 'general',
            'tone' => $options['tone'] ?? 'conversational',
            'expertise_level' => $options['expertise_level'] ?? 'intermediate',
            'guest_info' => $options['guest_info'] ?? '',
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['podcast', 'audio_script'],
            'episode_elements' => ['intro', 'segments', 'interview_questions', 'outro'],
            'engagement_features' => ['storytelling', 'interaction', 'takeaways']
        ], $options);
    }
}

if (!function_exists('ai_landing_page_architect')) {
    /**
     * Landing Page Mimarı - Conversion-optimized landing pages
     */
    function ai_landing_page_architect(string $campaign_description, array $options = []): array
    {
        return ai_execute_feature('landing-page-architect', [
            'content' => $campaign_description,
            'offer' => $options['offer'] ?? 'lead_magnet',
            'target_audience' => $options['target_audience'] ?? 'prospects',
            'industry' => $options['industry'] ?? 'general',
            'urgency' => $options['urgency'] ?? 'medium',
            'conversion_goal' => $options['conversion_goal'] ?? 'signup',
            'competitor' => implode(', ', $options['competitor'] ?? []),
            'language' => $options['language'] ?? 'Turkish'
        ], [
            'content_type' => ['landing_page', 'conversion_copy'],
            'conversion_elements' => ['headline', 'value_prop', 'social_proof', 'cta'],
            'optimization_factors' => ['mobile', 'speed', 'ux', 'psychology']
        ], $options);
    }
}