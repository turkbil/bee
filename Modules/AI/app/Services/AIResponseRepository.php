<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\AIPriorityEngine;
use Modules\AI\App\Models\AIFeature;
use App\Helpers\TenantHelpers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * AI RESPONSE REPOSITORY - Merkezi AI Yanıt Yönetim Sistemi
 * 
 * TÜM AI YANITLARI BU DOSYADA YÖNETİLİR:
 * - Admin panel AI yanıtları
 * - Helper fonksiyon yanıtları  
 * - Prowess test yanıtları
 * - Conversation yanıtları
 * - Feature test yanıtları
 * 
 * AVANTAJLARI:
 * - Tek yerden tüm yanıtları düzenleyebiliriz
 * - Tutarlı format ve kalite
 * - Kolay maintain
 * - Central error handling
 * - Unified caching
 */
class AIResponseRepository
{
    private AIService $aiService;
    private AIPriorityEngine $priorityEngine;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
        $this->priorityEngine = new AIPriorityEngine();
    }

    /**
     * =======================================================================
     * MERKEZI AI YANIT FONKSİYONU - TÜM İSTEKLER BURAYA GELİR
     * =======================================================================
     */
    public function executeRequest(string $type, array $params): array
    {
        try {
            // Request'i log'la
            Log::info("AIResponseRepository: {$type} isteği", [
                'type' => $type,
                'tenant_id' => tenant('id'),
                'params_keys' => array_keys($params)
            ]);

            // İsteğe göre doğru metodu çağır
            return match($type) {
                'admin_chat' => $this->handleAdminChat($params),
                'feature_test' => $this->handleFeatureTest($params),
                'prowess_test' => $this->handleProwessTest($params),
                'conversation' => $this->handleConversation($params),
                'helper_function' => $this->handleHelperFunction($params),
                'bulk_test' => $this->handleBulkTest($params),
                default => $this->handleGenericRequest($params)
            };

        } catch (\Exception $e) {
            Log::error("AIResponseRepository Error: {$type}", [
                'error' => $e->getMessage(),
                'params' => $params
            ]);

            return [
                'success' => false,
                'error' => 'AI yanıt sisteminde hata oluştu: ' . $e->getMessage(),
                'type' => $type
            ];
        }
    }

    /**
     * =======================================================================
     * ADMIN PANEL AI CHAT YANITLARI
     * =======================================================================
     */
    private function handleAdminChat(array $params): array
    {
        $userMessage = $params['message'] ?? '';
        $customPrompt = $params['custom_prompt'] ?? '';

        if (empty($userMessage)) {
            return [
                'success' => false,
                'error' => 'Mesaj boş olamaz'
            ];
        }

        // Mesaja göre context type belirleme
        $contextType = $this->determineContextType($userMessage);

        // AI Service ile prompt oluştur ve yanıt al
        $response = $this->aiService->ask($userMessage, [
            'custom_prompt' => $customPrompt,
            'context_type' => $contextType,
            'source' => 'admin_panel'
        ]);

        return [
            'success' => true,
            'response' => $response,
            'type' => 'admin_chat',
            'token_used' => true,
            'formatted_response' => $this->formatAdminResponse($response)
        ];
    }

    /**
     * =======================================================================
     * FEATURE TEST YANITLARI
     * =======================================================================
     */
    private function handleFeatureTest(array $params): array
    {
        $featureSlug = $params['feature_slug'] ?? '';
        $inputText = $params['input_text'] ?? '';
        $userInput = $params['user_input'] ?? [];

        if (empty($featureSlug)) {
            return [
                'success' => false,
                'error' => 'Feature slug gerekli'
            ];
        }

        // Feature'ı bul
        $feature = AIFeature::where('slug', $featureSlug)
                          ->where('status', 'active')
                          ->first();

        if (!$feature) {
            return [
                'success' => false,
                'error' => "Feature bulunamadı: {$featureSlug}"
            ];
        }

        // AI Service ile feature test et
        $response = $this->aiService->askFeature($feature, $inputText, [
            'user_input' => $userInput,
            'context_type' => 'feature_test',
            'source' => 'feature_testing'
        ]);

        return [
            'success' => true,
            'response' => $response,
            'feature' => $feature->toArray(),
            'type' => 'feature_test',
            'token_used' => true,
            'formatted_response' => $this->formatFeatureResponse($response, $feature)
        ];
    }

    /**
     * =======================================================================
     * PROWESS TEST YANITLARI
     * =======================================================================
     */
    private function handleProwessTest(array $params): array
    {
        $featureId = $params['feature_id'] ?? null;
        $inputText = $params['input_text'] ?? '';

        if (!$featureId) {
            return [
                'success' => false,
                'error' => 'Feature ID gerekli'
            ];
        }

        $feature = AIFeature::find($featureId);
        if (!$feature) {
            return [
                'success' => false,
                'error' => "Feature bulunamadı: {$featureId}"
            ];
        }

        // Prowess için özel context
        $response = $this->aiService->askFeature($feature, $inputText, [
            'context_type' => 'prowess_showcase',
            'source' => 'prowess_page',
            'enhanced_quality' => true // Prowess için kalite artırılsın
        ]);

        return [
            'success' => true,
            'response' => $response,
            'feature' => $feature->toArray(),
            'type' => 'prowess_test',
            'token_used' => true,
            'formatted_response' => $this->formatProwessResponse($response, $feature)
        ];
    }

    /**
     * =======================================================================
     * CONVERSATION YANITLARI
     * =======================================================================
     */
    private function handleConversation(array $params): array
    {
        $message = $params['message'] ?? '';
        $conversationId = $params['conversation_id'] ?? null;

        if (empty($message)) {
            return [
                'success' => false,
                'error' => 'Mesaj boş olamaz'
            ];
        }

        $response = $this->aiService->ask($message, [
            'conversation_id' => $conversationId,
            'context_type' => 'conversation',
            'source' => 'chat_interface'
        ]);

        return [
            'success' => true,
            'response' => $response,
            'type' => 'conversation',
            'token_used' => true,
            'conversation_id' => $conversationId,
            'formatted_response' => $this->formatConversationResponse($response)
        ];
    }

    /**
     * =======================================================================
     * HELPER FUNCTION YANITLARI
     * =======================================================================
     */
    private function handleHelperFunction(array $params): array
    {
        $helperName = $params['helper_name'] ?? '';
        $featureSlug = $params['feature_slug'] ?? '';
        $userInput = $params['user_input'] ?? [];
        $conditions = $params['conditions'] ?? [];

        if (empty($featureSlug)) {
            return [
                'success' => false,
                'error' => 'Feature slug gerekli'
            ];
        }

        $feature = AIFeature::where('slug', $featureSlug)->first();
        if (!$feature) {
            return [
                'success' => false,
                'error' => "Helper için feature bulunamadı: {$featureSlug}"
            ];
        }

        // Feature-specific handling with separate functions
        return match($featureSlug) {
            'hizli-seo-analizi' => $this->handleSEOAnalysisFeature($feature, $userInput, $helperName, $conditions),
            'ai-asistan-sohbet' => $this->handleAIChatFeature($feature, $userInput, $helperName, $conditions),
            default => $this->handleGenericFeature($feature, $userInput, $helperName, $conditions)
        };
    }

    /**
     * =======================================================================
     * SEO ANALİZ FEATURE - AYRI FONKSİYON
     * =======================================================================
     */
    private function handleSEOAnalysisFeature($feature, array $userInput, string $helperName, array $conditions): array
    {
        try {
            // SEO analizi için özel user message
            $userMessage = $this->buildSEOAnalysisMessage($userInput);
            
            Log::info("SEO Analysis Feature - Processing", [
                'feature_slug' => $feature->slug,
                'user_input_keys' => array_keys($userInput),
                'helper_name' => $helperName
            ]);
            
            $response = $this->aiService->askFeature($feature, $userMessage, [
                'user_input' => $userInput,
                'conditions' => $conditions,
                'context_type' => 'seo_analysis',
                'source' => 'seo_helper',
                'helper_name' => $helperName,
                'feature_type' => 'seo_analysis'
            ]);

            return [
                'success' => true,
                'response' => $response,
                'feature' => $feature->toArray(),
                'type' => 'seo_analysis',
                'helper_name' => $helperName,
                'token_used' => true,
                'formatted_response' => $this->formatSEOAnalysisResponse($response, $helperName)
            ];
            
        } catch (\Exception $e) {
            Log::error("SEO Analysis Feature Error", [
                'error' => $e->getMessage(),
                'feature_slug' => $feature->slug
            ]);
            
            return [
                'success' => false,
                'error' => 'SEO analiz hatası: ' . $e->getMessage(),
                'type' => 'seo_analysis'
            ];
        }
    }

    /**
     * =======================================================================
     * AI CHAT FEATURE - AYRI FONKSİYON
     * =======================================================================
     */
    private function handleAIChatFeature($feature, array $userInput, string $helperName, array $conditions): array
    {
        try {
            // AI chat için özel user message
            $userMessage = $this->buildAIChatMessage($userInput);
            
            Log::info("AI Chat Feature - Processing", [
                'feature_slug' => $feature->slug,
                'user_input_keys' => array_keys($userInput),
                'helper_name' => $helperName
            ]);
            
            $response = $this->aiService->askFeature($feature, $userMessage, [
                'user_input' => $userInput,
                'conditions' => $conditions,
                'context_type' => 'ai_chat_test',
                'source' => 'chat_helper',
                'helper_name' => $helperName,
                'feature_type' => 'ai_chat'
            ]);

            return [
                'success' => true,
                'response' => $response,
                'feature' => $feature->toArray(),
                'type' => 'ai_chat_test',
                'helper_name' => $helperName,
                'token_used' => true,
                'formatted_response' => $this->formatAIChatResponse($response, $helperName)
            ];
            
        } catch (\Exception $e) {
            Log::error("AI Chat Feature Error", [
                'error' => $e->getMessage(),
                'feature_slug' => $feature->slug
            ]);
            
            return [
                'success' => false,
                'error' => 'AI chat test hatası: ' . $e->getMessage(),
                'type' => 'ai_chat_test'
            ];
        }
    }

    /**
     * =======================================================================
     * GENERİK FEATURE - DİĞER TÜM FEATURE'LAR
     * =======================================================================
     */
    private function handleGenericFeature($feature, array $userInput, string $helperName, array $conditions): array
    {
        try {
            // Genel feature'lar için user message
            $userMessage = $this->buildGenericFeatureMessage($userInput, $feature);
            
            $response = $this->aiService->askFeature($feature, $userMessage, [
                'user_input' => $userInput,
                'conditions' => $conditions,
                'context_type' => 'helper_function',
                'source' => 'ai_helper',
                'helper_name' => $helperName,
                'feature_type' => 'generic'
            ]);

            return [
                'success' => true,
                'response' => $response,
                'feature' => $feature->toArray(),
                'type' => 'helper_function',
                'helper_name' => $helperName,
                'token_used' => true,
                'formatted_response' => $this->formatHelperResponse($response, $helperName)
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Feature işlem hatası: ' . $e->getMessage(),
                'type' => 'helper_function'
            ];
        }
    }

    /**
     * =======================================================================
     * BULK TEST YANITLARI (Çoklu Feature Test)
     * =======================================================================
     */
    private function handleBulkTest(array $params): array
    {
        $features = $params['features'] ?? [];
        $inputText = $params['input_text'] ?? '';

        if (empty($features)) {
            return [
                'success' => false,
                'error' => 'Test edilecek feature listesi boş'
            ];
        }

        $results = [];
        foreach ($features as $featureSlug) {
            $result = $this->handleFeatureTest([
                'feature_slug' => $featureSlug,
                'input_text' => $inputText
            ]);
            $results[$featureSlug] = $result;
        }

        return [
            'success' => true,
            'type' => 'bulk_test',
            'results' => $results,
            'total_features' => count($features),
            'successful_tests' => count(array_filter($results, fn($r) => $r['success']))
        ];
    }

    /**
     * =======================================================================
     * GENERİK İSTEK İŞLEME
     * =======================================================================
     */
    private function handleGenericRequest(array $params): array
    {
        $message = $params['message'] ?? '';
        
        if (empty($message)) {
            return [
                'success' => false,
                'error' => 'Mesaj boş olamaz'
            ];
        }

        $response = $this->aiService->ask($message, [
            'context_type' => 'generic',
            'source' => 'ai_repository'
        ]);

        return [
            'success' => true,
            'response' => $response,
            'type' => 'generic',
            'token_used' => true
        ];
    }

    /**
     * =======================================================================
     * YANIT FORMATLAMA FONKSİYONLARI - WORD BUFFER SİSTEMİ İLE
     * =======================================================================
     */
    private function formatAdminResponse(string $response): array
    {
        return [
            'formatted_text' => "🤖 **AI Asistan Yanıtı**\n\n" . $response,
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 180,
                'animation_duration' => 4500,
                'container_selector' => '.ai-response-container'
            ]
        ];
    }

    private function formatFeatureResponse(string $response, AIFeature $feature): array
    {
        // Feature'a göre özel template seç
        $htmlTemplate = match($feature->slug) {
            'seo-puan-analizi', 'hizli-seo-analizi' => $this->buildSEOScoreHTML($response, $feature),
            'icerik-optimizasyonu', 'icerik-genisletme' => $this->buildContentOptimizationHTML($response, $feature),
            'anahtar-kelime-analizi' => $this->buildKeywordAnalysisHTML($response, $feature),
            'baslik-uretici', 'meta-aciklama-uretici' => $this->buildMetaGeneratorHTML($response, $feature),
            'rekabet-analizi' => $this->buildCompetitionAnalysisHTML($response, $feature),
            default => $this->buildGenericFeatureHTML($response, $feature)
        };
        
        return [
            'formatted_text' => $response,
            'formatted_html' => $htmlTemplate,
            'template_type' => $feature->slug,
            'feature_name' => $feature->name,
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 160,
                'animation_duration' => 4200,
                'container_selector' => '.feature-response-container',
                'feature_name' => $feature->name
            ]
        ];
    }

    private function formatProwessResponse(?string $response, AIFeature $feature): array
    {
        // Null response durumunu handle et
        if ($response === null) {
            $response = "⚠️ AI yanıt alamadı. Lütfen tekrar deneyin veya farklı bir provider kullanın.";
        }
        
        return [
            'formatted_text' => "⭐ **{$feature->name} Prowess Showcase**\n\n" . $response,
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 200,
                'animation_duration' => 5000,
                'container_selector' => '.prowess-response-container',
                'feature_name' => $feature->name,
                'showcase_mode' => true
            ]
        ];
    }

    private function formatConversationResponse(string $response): array
    {
        return [
            'formatted_text' => "💬 " . $response,
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 150,
                'animation_duration' => 4000,
                'container_selector' => '.conversation-response-container'
            ]
        ];
    }

    private function formatHelperResponse(string $response, string $helperName): array
    {
        return [
            'formatted_text' => "🔧 **{$helperName} Helper Sonucu**\n\n" . $response,
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 170,
                'animation_duration' => 4300,
                'container_selector' => '.helper-response-container',
                'helper_name' => $helperName
            ]
        ];
    }

    /**
     * =======================================================================
     * FEATURE-SPECIFIC RESPONSE FORMATTERS - AYRI FONKSİYONLAR
     * =======================================================================
     */
    
    /**
     * SEO Analiz response formatter - MODERN HTML TEMPLATE
     */
    private function formatSEOAnalysisResponse(string $response, string $helperName): array
    {
        // Plain text'i parse et ve SEO skorunu çıkar
        $seoScore = $this->extractSEOScore($response);
        $analysisItems = $this->extractAnalysisItems($response);
        $recommendations = $this->extractRecommendations($response);
        $technicalDetails = $this->extractTechnicalDetails($response);
        
        // Modern HTML template oluştur
        $htmlTemplate = $this->buildSEOAnalysisHTML($seoScore, $analysisItems, $recommendations, $technicalDetails);
        
        return [
            'formatted_text' => $response, // Original plain text
            'formatted_html' => $htmlTemplate, // Modern HTML version
            'seo_score' => $seoScore,
            'template_type' => 'seo_analysis',
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 180,
                'animation_duration' => 5000,
                'container_selector' => '.seo-analysis-response-container',
                'feature_type' => 'seo_analysis',
                'helper_name' => $helperName
            ]
        ];
    }
    
    /**
     * AI Chat Test response formatter
     */
    private function formatAIChatResponse(string $response, string $helperName): array
    {
        return [
            'formatted_text' => "🤖 **AI Test Sonucu**\n\n" . $response,
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 150,
                'animation_duration' => 3500,
                'container_selector' => '.ai-chat-test-response-container',
                'feature_type' => 'ai_chat_test',
                'helper_name' => $helperName
            ]
        ];
    }

    /**
     * Word buffer ile yanıt formatla (tüm AI yanıtları için universal)
     */
    public function formatWithWordBuffer(string $response, string $type, array $meta = []): array
    {
        // Type'a göre özel konfigürasyon
        $configs = [
            'admin_chat' => [
                'delay_between_words' => 180,
                'animation_duration' => 4500,
                'container_selector' => '.ai-admin-response'
            ],
            'feature_test' => [
                'delay_between_words' => 160,
                'animation_duration' => 4200,
                'container_selector' => '.ai-feature-response'
            ],
            'prowess_test' => [
                'delay_between_words' => 200,
                'animation_duration' => 5000,
                'container_selector' => '.ai-prowess-response',
                'showcase_mode' => true
            ],
            'conversation' => [
                'delay_between_words' => 150,
                'animation_duration' => 4000,
                'container_selector' => '.ai-conversation-response'
            ],
            'helper_function' => [
                'delay_between_words' => 170,
                'animation_duration' => 4300,
                'container_selector' => '.ai-helper-response'
            ]
        ];

        $config = $configs[$type] ?? $configs['admin_chat'];
        
        return [
            'response' => $response,
            'word_buffer_enabled' => true,
            'word_buffer_config' => array_merge($config, $meta)
        ];
    }

    /**
     * =======================================================================
     * UTILITY METHODS
     * =======================================================================
     */
    public function getAvailableTypes(): array
    {
        return [
            'admin_chat' => 'Admin Panel AI Chat',
            'feature_test' => 'Feature Test',
            'prowess_test' => 'Prowess Showcase Test', 
            'conversation' => 'AI Conversation',
            'helper_function' => 'AI Helper Function',
            'bulk_test' => 'Bulk Feature Test',
            'generic' => 'Generic AI Request'
        ];
    }

    public function getTypeDescription(string $type): string
    {
        return $this->getAvailableTypes()[$type] ?? 'Bilinmeyen tip';
    }

    /**
     * Mesaja göre context type belirleme - AI ile hızlı analiz
     */
    private function determineContextType(string $message): string
    {
        // Cache key oluştur
        $cacheKey = 'ai_context_type_' . md5($message);
        
        // Cache'den kontrol et
        if ($cached = cache()->get($cacheKey)) {
            return $cached;
        }
        
        // Hızlı AI analizi (sadece context type belirleme)
        try {
            $prompt = "Bu mesaj hangi context type gerektirir? Sadece tek kelime yanıt ver: minimal, essential, normal, detailed\n\nMesaj: \"$message\"";
            
            // Çok basit ve hızlı AI çağrısı
            $response = $this->aiService->ask($prompt, [
                'context_type' => 'minimal', // Recursive loop önleme
                'source' => 'context_analyzer',
                'max_tokens' => 5 // Sadece tek kelime
            ]);
            
            // Response'u temizle
            $contextType = strtolower(trim($response));
            
            // Valid context type kontrolü
            $validTypes = ['minimal', 'essential', 'normal', 'detailed'];
            if (!in_array($contextType, $validTypes)) {
                $contextType = 'essential'; // Default fallback
            }
            
            // 5 dakika cache
            cache()->put($cacheKey, $contextType, 300);
            
            return $contextType;
            
        } catch (\Exception $e) {
            // Hata durumunda fallback
            return 'essential';
        }
    }

    /**
     * =======================================================================
     * FEATURE-SPECIFIC MESSAGE BUILDERS - AYRI FONKSİYONLAR
     * =======================================================================
     */
    
    /**
     * SEO Analiz için özel message builder
     */
    private function buildSEOAnalysisMessage(array $userInput): string
    {
        $title = $userInput['title'] ?? '';
        $content = $userInput['content'] ?? '';
        $metaDesc = $userInput['meta_description'] ?? '';
        $language = $userInput['language'] ?? 'tr';
        
        return "🔍 SEO ANALİZ İSTEĞİ

Aşağıdaki web sayfası içeriğini profesyonel SEO kriterlerine göre analiz et:

📝 SAYFA BAŞLIĞI: {$title}

📄 İÇERİK METNİ:
{$content}

📋 META AÇIKLAMA: {$metaDesc}

🌐 DİL: {$language}

📊 ANALIZ TALEP EDİLEN KONULAR:
- SEO puanı (0-100)
- Kritik sorunlar ve eksiklikler
- Anahtar kelime optimizasyonu önerileri
- Teknik SEO iyileştirmeleri
- İçerik kalitesi değerlendirmesi
- Kullanıcı deneyimi önerileri

Lütfen detaylı ve uygulanabilir SEO analizi yap.";
    }
    
    /**
     * AI Chat Test için özel message builder
     */
    private function buildAIChatMessage(array $userInput): string
    {
        $testMessage = $userInput['test_message'] ?? 'AI bağlantı testi';
        $pageId = $userInput['page_id'] ?? '';
        $language = $userInput['language'] ?? 'tr';
        
        return "🤖 AI BAĞLANTI TEST İSTEĞİ

Bu bir AI asistan bağlantı testidir. Lütfen yanıt vererek sistemin çalıştığını onayla.

💬 TEST MESAJI: {$testMessage}

📄 SAYFA ID: {$pageId}

🌐 DİL: {$language}

✅ BEKLENEN YANIT:
- Kısa ve net bir onay mesajı
- Sistemin çalıştığına dair bilgi
- Test başarısı konfirmasyonu

Lütfen AI sisteminin aktif olduğunu doğrula.";
    }
    
    /**
     * Genel feature'lar için message builder
     */
    private function buildGenericFeatureMessage(array $userInput, $feature): string
    {
        $message = "🔧 {$feature->name} İSTEĞİ\n\nAşağıdaki verilerle işlem yap:\n\n";
        
        foreach ($userInput as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            $message .= "• " . strtoupper($key) . ": " . $value . "\n";
        }
        
        return $message;
    }

    /**
     * Legacy method - backward compatibility için
     */
    private function buildUserMessageFromInput(array $userInput, $feature): string
    {
        return match($feature->slug) {
            'hizli-seo-analizi' => $this->buildSEOAnalysisMessage($userInput),
            'ai-asistan-sohbet' => $this->buildAIChatMessage($userInput),
            default => $this->buildGenericFeatureMessage($userInput, $feature)
        };
    }

    /**
     * =======================================================================
     * MODERN HTML TEMPLATE BUILDERS - ŞİK GÖRÜNÜM SİSTEMİ
     * =======================================================================
     */

    /**
     * SEO Score HTML Template - Circular Score + Analysis
     */
    private function buildSEOScoreHTML(string $response, AIFeature $feature): string
    {
        $score = $this->extractSEOScore($response);
        $issues = $this->extractSEOIssues($response);
        $recommendations = $this->extractRecommendations($response);
        
        $scoreColor = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
        $scoreIcon = $score >= 80 ? 'fas fa-check-circle' : ($score >= 60 ? 'fas fa-exclamation-triangle' : 'fas fa-times-circle');
        
        return '
        <div class="ai-response-template seo-score-template">
            <div class="row">
                <!-- Hero Score Section - Solda Büyük Circular -->
                <div class="col-lg-4 col-md-6">
                    <div class="hero-score-card">
                        <div class="circular-score circular-score-' . $scoreColor . '">
                            <div class="score-inner">
                                <div class="score-number">' . $score . '</div>
                                <div class="score-label">SEO Skoru</div>
                            </div>
                        </div>
                        <div class="score-status">
                            <i class="' . $scoreIcon . ' text-' . $scoreColor . '"></i>
                            <span class="status-text">' . $this->getSEOStatusText($score) . '</span>
                        </div>
                    </div>
                </div>
                
                <!-- Analysis Section - Sağda Expandable List -->
                <div class="col-lg-8 col-md-6">
                    <div class="analysis-section">
                        <h5><i class="fas fa-chart-line me-2"></i>Analiz Sonuçları</h5>
                        <div class="analysis-items">
                            ' . $this->buildAnalysisItems($issues) . '
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recommendations Section - Full Width -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="recommendations-section">
                        <h5><i class="fas fa-lightbulb me-2"></i>Önerilerim</h5>
                        <div class="recommendation-cards">
                            ' . $this->buildRecommendationCards($recommendations) . '
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Technical Details - Collapsible -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="technical-details">
                        <div class="card">
                            <div class="card-header cursor-pointer" data-bs-toggle="collapse" data-bs-target="#technicalDetails">
                                <i class="fas fa-cog me-2"></i>Teknik Detaylar
                                <i class="fas fa-chevron-down float-end"></i>
                            </div>
                            <div id="technicalDetails" class="collapse">
                                <div class="card-body">
                                    <div class="technical-content">
                                        ' . $this->parseResponseContent($response) . '
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

    /**
     * Generic Feature HTML Template
     */
    private function buildGenericFeatureHTML(string $response, $feature): string
    {
        $mainPoints = $this->extractMainPoints($response);
        $details = $this->extractDetails($response);
        
        return '
        <div class="ai-response-template generic-feature-template">
            <div class="feature-header">
                <div class="feature-icon">
                    ' . $feature->emoji . '
                </div>
                <div class="feature-title">
                    <h4>' . $feature->name . '</h4>
                    <p class="text-muted">' . $feature->description . '</p>
                </div>
            </div>
            
            <div class="feature-content">
                <div class="main-points">
                    ' . $this->buildPointsList($mainPoints) . '
                </div>
                
                <div class="feature-details mt-3">
                    <div class="details-content">
                        ' . $this->parseResponseContent($response) . '
                    </div>
                </div>
            </div>
        </div>';
    }

    /**
     * Content Optimization HTML Template
     */
    private function buildContentOptimizationHTML(string $response, AIFeature $feature): string
    {
        $improvements = $this->extractImprovements($response);
        $beforeAfter = $this->extractBeforeAfter($response);
        
        return '
        <div class="ai-response-template content-optimization-template">
            <div class="optimization-header">
                <h4><i class="fas fa-magic me-2"></i>İçerik Optimizasyonu</h4>
            </div>
            
            <div class="improvement-cards">
                ' . $this->buildImprovementCards($improvements) . '
            </div>
            
            <div class="before-after-section mt-4">
                ' . $this->buildBeforeAfterSection($beforeAfter) . '
            </div>
            
            <div class="optimization-summary mt-3">
                <div class="summary-content">
                    ' . $this->parseResponseContent($response) . '
                </div>
            </div>
        </div>';
    }

    /**
     * =======================================================================
     * HTML COMPONENT BUILDERS - Helper Methods
     * =======================================================================
     */

    private function buildAnalysisItems(array $items): string
    {
        $html = '';
        foreach ($items as $item) {
            $status = $item['status'] ?? 'info';
            $icon = $this->getStatusIcon($status);
            $html .= '
            <div class="analysis-item analysis-item-' . $status . '">
                <div class="item-header">
                    <i class="' . $icon . ' me-2"></i>
                    <span class="item-label">' . $item['label'] . '</span>
                    <span class="badge badge-' . $status . ' ms-auto">' . ucfirst($status) . '</span>
                </div>
                <div class="item-detail">' . $item['detail'] . '</div>
            </div>';
        }
        return $html;
    }

    private function buildRecommendationCards(array $recommendations): string
    {
        $html = '';
        foreach ($recommendations as $rec) {
            $priority = $rec['priority'] ?? 'medium';
            $priorityClass = $priority === 'high' ? 'danger' : ($priority === 'medium' ? 'warning' : 'info');
            $html .= '
            <div class="recommendation-card">
                <div class="card border-' . $priorityClass . '">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-arrow-up me-2 text-' . $priorityClass . '"></i>
                            ' . $rec['title'] . '
                        </h6>
                        <p class="card-text">' . $rec['action'] . '</p>
                        <span class="badge bg-' . $priorityClass . '">' . strtoupper($priority) . ' ÖNCELİK</span>
                    </div>
                </div>
            </div>';
        }
        return $html;
    }

    /**
     * =======================================================================
     * CONTENT PARSING HELPERS - Plain Text'ten Veri Çıkarma
     * =======================================================================
     */

    private function extractSEOScore(string $response): int
    {
        // Regex ile SEO skorunu bul (85/100, 85%, 85 gibi formatlar)
        if (preg_match('/\b(\d{1,3})\s*[\/\%]?\s*(?:100|puan|skor)/i', $response, $matches)) {
            return intval($matches[1]);
        }
        
        // Default score
        return 75;
    }

    private function extractSEOIssues(string $response): array
    {
        $issues = [];
        
        // Common SEO issue patterns
        $patterns = [
            '/başlık.*?(eksik|kısa|uzun|problem)/i' => ['label' => 'Başlık Optimizasyonu', 'status' => 'warning'],
            '/meta.*?(eksik|kısa|uzun|problem)/i' => ['label' => 'Meta Açıklama', 'status' => 'warning'], 
            '/anahtar.*?(eksik|yok|problem)/i' => ['label' => 'Anahtar Kelime', 'status' => 'danger'],
            '/içerik.*?(kısa|yetersiz|problem)/i' => ['label' => 'İçerik Kalitesi', 'status' => 'warning'],
            '/link.*?(eksik|yok|problem)/i' => ['label' => 'İç Bağlantılar', 'status' => 'info']
        ];
        
        foreach ($patterns as $pattern => $config) {
            if (preg_match($pattern, $response, $matches)) {
                $issues[] = [
                    'label' => $config['label'],
                    'status' => $config['status'], 
                    'detail' => $matches[0]
                ];
            }
        }
        
        // Default issues if none found
        if (empty($issues)) {
            $issues = [
                ['label' => 'Genel Analiz', 'status' => 'info', 'detail' => 'SEO analizi tamamlandı'],
                ['label' => 'Öneriler', 'status' => 'success', 'detail' => 'İyileştirme önerileri hazır']
            ];
        }
        
        return $issues;
    }

    private function extractRecommendations(string $response): array
    {
        $recommendations = [];
        
        // Look for numbered recommendations or bullet points
        if (preg_match_all('/(?:^\d+\.|\*|\-)\s*(.+?)$/m', $response, $matches)) {
            foreach ($matches[1] as $index => $rec) {
                $recommendations[] = [
                    'title' => 'Öneri ' . ($index + 1),
                    'action' => trim($rec),
                    'priority' => $index < 2 ? 'high' : 'medium'
                ];
            }
        }
        
        // Default recommendations if none found
        if (empty($recommendations)) {
            $recommendations = [
                ['title' => 'İçerik İyileştir', 'action' => 'Analiz sonuçlarına göre içeriği optimize edin', 'priority' => 'high'],
                ['title' => 'SEO Teknik', 'action' => 'Teknik SEO iyileştirmelerini uygulayın', 'priority' => 'medium']
            ];
        }
        
        return $recommendations;
    }

    private function extractMainPoints(string $response): array
    {
        $points = [];
        
        // Extract bullet points or numbered lists
        if (preg_match_all('/(?:^\d+\.|\*|\-)\s*(.+?)$/m', $response, $matches)) {
            $points = $matches[1];
        }
        
        return array_slice($points, 0, 5); // İlk 5 point
    }

    private function parseResponseContent(string $response): string
    {
        // Basic formatting for better readability
        $content = nl2br(htmlspecialchars($response));
        
        // Make headers bold
        $content = preg_replace('/^(#+)\s*(.+?)$/m', '<strong>$2</strong>', $content);
        
        // Make bullet points styled
        $content = preg_replace('/^\*\s*(.+?)$/m', '<li>$1</li>', $content);
        $content = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $content);
        
        return $content;
    }

    /**
     * =======================================================================
     * UTILITY HELPERS
     * =======================================================================
     */

    private function getSEOStatusText(int $score): string
    {
        if ($score >= 90) return 'Mükemmel';
        if ($score >= 80) return 'Çok İyi';
        if ($score >= 60) return 'İyi';
        if ($score >= 40) return 'Geliştirilmeli';
        return 'Kötü';
    }

    private function getStatusIcon(string $status): string
    {
        return match($status) {
            'success' => 'fas fa-check-circle text-success',
            'warning' => 'fas fa-exclamation-triangle text-warning', 
            'danger' => 'fas fa-times-circle text-danger',
            default => 'fas fa-info-circle text-info'
        };
    }

    private function buildPointsList(array $points): string
    {
        $html = '<ul class="styled-points">';
        foreach ($points as $point) {
            $html .= '<li><i class="fas fa-check text-success me-2"></i>' . htmlspecialchars($point) . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    // Content parsing methods for modern templates
    private function extractSEOScore(string $response): ?int {
        // SEO skor paternleri
        $patterns = [
            '/(?:seo|score|skor).*?(\d{1,2})(?:\/100|\s*\%|\s*puan)/i',
            '/(\d{1,2})\s*\/\s*100/i',
            '/skorunuz.*?(\d{1,2})/i',
            '/genel.*?skor.*?(\d{1,2})/i',
            '/overall.*?score.*?(\d{1,2})/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $response, $matches)) {
                $score = (int) $matches[1];
                if ($score >= 0 && $score <= 100) {
                    return $score;
                }
            }
        }
        
        // Varsayılan skor
        return 75;
    }
    
    private function extractAnalysisItems(string $response): array { 
        // Yanıttan analiz noktalarını çıkar
        $items = [];
        $lines = explode("\n", $response);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^[\-\*\•]\s*(.+)$/i', $line, $matches)) {
                $text = trim($matches[1]);
                $status = 'info';
                
                // Durum tespiti
                if (preg_match('/(iyi|good|excellent|mükemmel|başarılı)/i', $text)) {
                    $status = 'success';
                } elseif (preg_match('/(kötü|bad|poor|eksik|yetersiz|problem)/i', $text)) {
                    $status = 'danger';
                } elseif (preg_match('/(orta|geliştir|improve|optimize)/i', $text)) {
                    $status = 'warning';
                }
                
                $items[] = [
                    'label' => substr($text, 0, 50) . (strlen($text) > 50 ? '...' : ''),
                    'detail' => $text,
                    'status' => $status
                ];
                
                if (count($items) >= 5) break; // Max 5 item
            }
        }
        
        return $items ?: [
            ['label' => 'Analiz Tamamlandı', 'detail' => 'SEO analizi başarıyla gerçekleştirildi', 'status' => 'success']
        ];
    }
    
    private function extractRecommendations(string $response): array {
        // Öneri paternlerini bul
        $recommendations = [];
        $lines = explode("\n", $response);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^[\d\.\-\*\•]\s*(.+)$/i', $line, $matches)) {
                $text = trim($matches[1]);
                
                // Öneri kelimelerini ara
                if (preg_match('/(öneri|suggest|recommend|improve|geliştir|optimize)/i', $text)) {
                    $priority = 'medium';
                    
                    if (preg_match('/(acil|urgent|kritik|important|önemli)/i', $text)) {
                        $priority = 'high';
                    } elseif (preg_match('/(minor|küçük|basit|simple)/i', $text)) {
                        $priority = 'low';
                    }
                    
                    $recommendations[] = [
                        'title' => substr($text, 0, 60) . (strlen($text) > 60 ? '...' : ''),
                        'action' => $text,
                        'priority' => $priority
                    ];
                    
                    if (count($recommendations) >= 4) break; // Max 4 öneri
                }
            }
        }
        
        return $recommendations;
    }
    
    private function extractTechnicalDetails(string $response): string {
        // Teknik detayları çıkar (response'un son kısmı genellikle)
        $sentences = preg_split('/[.!?]+/', $response);
        $technicalSentences = [];
        
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (preg_match('/(tag|meta|html|css|javascript|kod|technical|teknik)/i', $sentence) && strlen($sentence) > 30) {
                $technicalSentences[] = $sentence;
            }
        }
        
        return implode('. ', array_slice($technicalSentences, 0, 3)) . '.';
    }
    private function buildSEOAnalysisHTML($score, $items, $recs, $details): string { 
        // SEO Score değeri varsa modern template, yoksa basit template
        if ($score && is_numeric($score)) {
            return $this->buildModernSEOTemplate($score, $items, $recs, $details);
        }
        
        // Fallback: Generic template
        $feature = (object)[
            'name' => 'SEO Analizi',
            'description' => 'Detaylı SEO performans analizi',
            'emoji' => '📊'
        ];
        return $this->buildGenericFeatureHTML('', $feature); 
    }
    
    private function buildModernSEOTemplate($score, $items, $recommendations, $details): string {
        $scoreClass = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
        $scoreIcon = $score >= 80 ? 'fas fa-check-circle' : ($score >= 60 ? 'fas fa-exclamation-triangle' : 'fas fa-times-circle');
        
        return '
        <div class="ai-response-template seo-score-template">
            <div class="row">
                <div class="col-md-4">
                    <div class="hero-score-card">
                        <div class="circular-score circular-score-' . $scoreClass . '" style="--score-percentage: ' . $score . '%;">
                            <div class="score-inner">
                                <div class="score-number">' . $score . '</div>
                                <div class="score-label">SEO Skoru</div>
                            </div>
                        </div>
                        <div class="score-status text-' . $scoreClass . '">
                            <i class="' . $scoreIcon . '"></i>
                            <span>' . ($score >= 80 ? 'Mükemmel' : ($score >= 60 ? 'İyileştirilebilir' : 'Geliştirilmeli')) . '</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="analysis-section">
                        <h5><i class="fas fa-chart-bar me-2"></i>Analiz Sonuçları</h5>
                        ' . $this->buildAnalysisItems($items) . '
                    </div>
                </div>
            </div>
            
            ' . ($recommendations ? '
            <div class="recommendations-section">
                <h5><i class="fas fa-lightbulb me-2"></i>Önerilerim</h5>
                <div class="recommendation-cards">
                    ' . $this->buildRecommendationCards($recommendations) . '
                </div>
            </div>
            ' : '') . '
            
            ' . ($details ? '
            <div class="technical-details">
                <div class="card">
                    <div class="card-header cursor-pointer" data-bs-toggle="collapse" data-bs-target="#technicalDetails">
                        <i class="fas fa-cog me-2"></i>Teknik Detaylar
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </div>
                    <div class="collapse" id="technicalDetails">
                        <div class="card-body">
                            <div class="technical-content">' . nl2br(e($details)) . '</div>
                        </div>
                    </div>
                </div>
            </div>
            ' : '') . '
        </div>';
    }
    
    private function buildAnalysisItems($items): string {
        if (!$items || !is_array($items)) return '';
        
        $html = '';
        foreach ($items as $item) {
            $status = $item['status'] ?? 'info';
            $html .= '
            <div class="analysis-item analysis-item-' . $status . '">
                <div class="item-header">
                    <span class="item-label">' . ($item['label'] ?? '') . '</span>
                    <span class="badge badge-' . $status . '">' . strtoupper($status) . '</span>
                </div>
                <div class="item-detail">' . ($item['detail'] ?? '') . '</div>
            </div>';
        }
        return $html;
    }
    
    private function buildRecommendationCards($recommendations): string {
        if (!$recommendations || !is_array($recommendations)) return '';
        
        $html = '';
        foreach ($recommendations as $rec) {
            $priority = $rec['priority'] ?? 'medium';
            $priorityColor = $priority === 'high' ? 'danger' : ($priority === 'medium' ? 'warning' : 'info');
            
            $html .= '
            <div class="recommendation-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">' . ($rec['title'] ?? '') . '</h6>
                        <p class="card-text">' . ($rec['action'] ?? '') . '</p>
                        <span class="badge bg-' . $priorityColor . '">Öncelik: ' . ucfirst($priority) . '</span>
                    </div>
                </div>
            </div>';
        }
        return $html;
    }
    private function buildKeywordAnalysisHTML(string $response, AIFeature $feature): string { return $this->buildGenericFeatureHTML($response, $feature); }
    private function buildMetaGeneratorHTML(string $response, AIFeature $feature): string { return $this->buildGenericFeatureHTML($response, $feature); }
    private function buildCompetitionAnalysisHTML(string $response, AIFeature $feature): string { return $this->buildGenericFeatureHTML($response, $feature); }
    private function extractImprovements(string $response): array { return []; }
    private function extractBeforeAfter(string $response): array { return []; }
    private function extractDetails(string $response): array { return []; }
    private function buildImprovementCards(array $improvements): string { return ''; }
    private function buildBeforeAfterSection(array $beforeAfter): string { return ''; }
}