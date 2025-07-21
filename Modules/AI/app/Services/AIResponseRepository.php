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
        return [
            'formatted_text' => "🎯 **{$feature->name} Sonucu**\n\n" . $response,
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
     * SEO Analiz response formatter
     */
    private function formatSEOAnalysisResponse(string $response, string $helperName): array
    {
        return [
            'formatted_text' => "🔍 **SEO Analiz Sonucu**\n\n" . $response,
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
}