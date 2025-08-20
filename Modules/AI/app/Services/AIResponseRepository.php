<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\AIPriorityEngine;
use Modules\AI\App\Services\SmartResponseFormatter;
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
    private SmartResponseFormatter $formatter;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
        $this->priorityEngine = new AIPriorityEngine();
        $this->formatter = new SmartResponseFormatter();
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
                'widget_feature' => $this->handleWidgetFeature($params),
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
            'formatted_response' => $this->formatFeatureResponse($response, $feature, 'feature_test')
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
        $universalInputs = $params['universal_inputs'] ?? [];

        // TENANT CONTEXT'İ ZORLA AYARLA - KRİTİK FİX
        if (!tenant('id') && auth()->check()) {
            $user = auth()->user();
            if ($user->tenant_id) {
                $tenant = \App\Models\Tenant::find($user->tenant_id);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                    Log::info('🔧 Prowess Test: Tenant context ayarlandı', [
                        'tenant_id' => $tenant->id,
                        'user_id' => $user->id
                    ]);
                }
            }
        }

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

        Log::info('Prowess Test Universal Inputs', [
            'feature_id' => $featureId,
            'feature_name' => $feature->name,
            'universal_inputs' => $universalInputs,
            'input_text_length' => strlen($inputText)
        ]);

        // Universal Input System verilerini işle
        $askOptions = [
            'context_type' => 'prowess_showcase',
            'source' => 'prowess_page',
            'enhanced_quality' => true
        ];

        // Universal inputs'ları options'a ekle
        if (!empty($universalInputs)) {
            $askOptions['universal_inputs'] = $universalInputs;
            
            // Content length mapping (1-5 slider → prompt değeri)
            if (isset($universalInputs['content_length'])) {
                $lengthMapping = [
                    1 => 90011, // Çok Kısa İçerik
                    2 => 90012, // Kısa İçerik  
                    3 => 90013, // Normal İçerik
                    4 => 90014, // Uzun İçerik
                    5 => 90015  // Çok Detaylı İçerik
                ];
                $askOptions['content_length_prompt_id'] = $lengthMapping[$universalInputs['content_length']] ?? 90013;
            }

            // Writing tone prompt ID
            if (isset($universalInputs['writing_tone'])) {
                $askOptions['writing_tone_prompt_id'] = $universalInputs['writing_tone'];
            }

            // Target audience
            if (isset($universalInputs['target_audience'])) {
                $askOptions['target_audience'] = $universalInputs['target_audience'];
            }

            // Company profile usage
            if (isset($universalInputs['use_company_profile']) && $universalInputs['use_company_profile']) {
                $askOptions['use_company_profile'] = true;
                Log::info('Company profile enabled for prowess test', ['feature_id' => $featureId]);
            }
        }

        // Prowess için özel context
        $response = $this->aiService->askFeature($feature, $inputText, $askOptions);

        $formattedResponse = $this->formatProwessResponse($response, $feature);
        
        // Safety check for array handling
        $responseText = '';
        if (is_array($formattedResponse) && isset($formattedResponse['formatted_text'])) {
            $responseText = $formattedResponse['formatted_text'];
        } elseif (is_string($formattedResponse)) {
            $responseText = $formattedResponse;
        } else {
            $responseText = $response; // Fallback to original response
            Log::warning("formatProwessResponse unexpected return type", [
                'feature_id' => $feature->id, 
                'response_type' => gettype($formattedResponse)
            ]);
        }
        
        return [
            'success' => true,
            'response' => $responseText,
            'feature' => $feature->toArray(),
            'type' => 'prowess_test',
            'token_used' => true,
            'tokens_used' => $formattedResponse['tokens_used'] ?? 0,
            'total_tokens' => $formattedResponse['total_tokens'] ?? 0,
            'formatted_response' => $responseText,
            'word_buffer_config' => $formattedResponse['word_buffer_config'] ?? null
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

            // Feature'a özel formatting
            $formattedResponse = $this->formatFeatureResponse($response, $feature, $helperName);
            
            return [
                'success' => true,
                'response' => $response,
                'feature' => $feature->toArray(),
                'type' => 'helper_function',
                'helper_name' => $helperName,
                'token_used' => true,
                'formatted_response' => $formattedResponse
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


    private function formatProwessResponse(?string $response, AIFeature $feature): array
    {
        // Null response durumunu handle et
        if ($response === null) {
            $response = "⚠️ AI yanıt alamadı. Lütfen tekrar deneyin veya farklı bir provider kullanın.";
        }
        
        // Feature'ın response template'ini kontrol et
        $responseTemplate = null;
        if ($feature->response_template) {
            try {
                // response_template zaten array ise decode etme
                if (is_array($feature->response_template)) {
                    $responseTemplate = $feature->response_template;
                } else {
                    // response_template zaten array ise decode etme
                if (is_array($feature->response_template)) {
                    $responseTemplate = $feature->response_template;
                } else {
                    // response_template zaten array ise decode etme
                if (is_array($feature->response_template)) {
                    $responseTemplate = $feature->response_template;
                } else {
                    $responseTemplate = json_decode($feature->response_template, true);
                }
                }
                }
            } catch (\Exception $e) {
                Log::warning("Feature response template parse hatası: {$feature->slug}", ['error' => $e->getMessage()]);
            }
        }
        
        // 🎨 SMART RESPONSE FORMATTER - Monoton 1-2-3 formatını kır
        try {
            // Orijinal input'u da göndermek için context'ten al
            $originalInput = $feature->quick_prompt ?? 'AI İsteği';
            
            // Smart formatter uygula
            $smartFormattedResponse = $this->formatter->format($originalInput, $response, $feature);
            
            Log::info('🎨 Smart Response Formatter applied', [
                'feature' => $feature->slug,
                'original_length' => strlen($response),
                'formatted_length' => strlen($smartFormattedResponse)
            ]);
            
            $formattedResponse = $smartFormattedResponse;
            
        } catch (\Exception $e) {
            Log::warning('Smart Response Formatter failed, using fallback', [
                'error' => $e->getMessage(),
                'feature' => $feature->slug
            ]);
            
            // Fallback: Eski sistem
            if ($this->isHtmlResponse($response)) {
                $formattedResponse = $response;
            } elseif ($responseTemplate && is_array($responseTemplate)) {
                $formattedResponse = $this->applyResponseTemplate($response, $responseTemplate, $feature);
            } else {
                $formattedResponse = $this->wrapResponseInCard($response, $feature->name);
            }
        }
        
        return [
            'formatted_text' => $formattedResponse,
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
    
    /**
     * Response template'ini uygula - structured formatting
     */
    private function applyResponseTemplate(string $response, array $template, AIFeature $feature): string
    {
        // Modern Tabler.io card layout
        $html = "<div class='card ai-feature-response ai-feature-{$feature->slug} mb-3'>";
        
        // Card header with feature name and icon
        $html .= "<div class='card-header'>";
        $html .= "<div class='d-flex align-items-center'>";
        $html .= "<div class='me-3'><i class='fas fa-robot text-primary fs-2'></i></div>";
        $html .= "<div>";
        $html .= "<h3 class='card-title mb-1'>{$feature->name}</h3>";
        $html .= "<p class='text-muted mb-0'>AI Analiz Sonuçları</p>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        
        // Card body
        $html .= "<div class='card-body'>";
        
        // Format belirleme
        $format = $template['format'] ?? 'text';
        
        if ($format === 'modern_html' && isset($template['sections'])) {
            // Modern HTML template sistemi - CARD SİSTEMİ
            return $this->applyModernCardTemplate($response, $template, $feature, $html);
        } elseif ($format === 'structured' && isset($template['sections'])) {
            $sections = $template['sections'];
            $parsedResponse = $this->tryParseResponse($response);
            
            if (is_array($parsedResponse)) {
                // Accordion layout for sections
                $accordionId = "accordion-{$feature->slug}";
                $html .= "<div class='accordion' id='{$accordionId}'>";
                
                $sectionIndex = 0;
                foreach ($sections as $section) {
                    if (isset($parsedResponse[$section])) {
                        $sectionTitle = $this->getSectionTitle($section);
                        $sectionIcon = $this->getSectionIcon($section);
                        $collapseId = "collapse-{$feature->slug}-{$sectionIndex}";
                        
                        $html .= "<div class='accordion-item'>";
                        $html .= "<h2 class='accordion-header' id='heading-{$sectionIndex}'>";
                        $html .= "<button class='accordion-button" . ($sectionIndex !== 0 ? ' collapsed' : '') . "' type='button' data-bs-toggle='collapse' data-bs-target='#{$collapseId}' aria-expanded='" . ($sectionIndex === 0 ? 'true' : 'false') . "' aria-controls='{$collapseId}'>";
                        $html .= "<i class='{$sectionIcon} me-2'></i> {$sectionTitle}";
                        $html .= "</button>";
                        $html .= "</h2>";
                        $html .= "<div id='{$collapseId}' class='accordion-collapse collapse" . ($sectionIndex === 0 ? ' show' : '') . "' aria-labelledby='heading-{$sectionIndex}' data-bs-parent='#{$accordionId}'>";
                        $html .= "<div class='accordion-body'>";
                        $html .= $this->formatSectionContent($parsedResponse[$section]);
                        $html .= "</div>";
                        $html .= "</div>";
                        $html .= "</div>";
                        
                        $sectionIndex++;
                    }
                }
                $html .= "</div>";
                
                // Score/confidence badge
                if ($template['show_confidence'] ?? false) {
                    if (isset($parsedResponse['score']) || isset($parsedResponse['confidence'])) {
                        $score = $parsedResponse['score'] ?? $parsedResponse['confidence'] ?? 'N/A';
                        $badgeClass = $this->getScoreBadgeClass($score);
                        
                        $html .= "<div class='mt-3 d-flex justify-content-center'>";
                        $html .= "<span class='badge {$badgeClass} badge-lg'>";
                        $html .= "<i class='fas fa-target me-1'></i> Güven Skoru: {$score}";
                        $html .= "</span>";
                        $html .= "</div>";
                    }
                }
            } else {
                // Simple response in alert format
                $html .= "<div class='alert alert-info mb-0'>";
                $html .= "<div class='d-flex'>";
                $html .= "<div class='me-2'><i class='fas fa-info-circle'></i></div>";
                $html .= "<div>{$response}</div>";
                $html .= "</div>";
                $html .= "</div>";
            }
        } else {
            // Simple format in card
            $html .= "<div class='alert alert-primary mb-0'>";
            $html .= "<div class='d-flex'>";
            $html .= "<div class='me-2'><i class='fas fa-magic'></i></div>";
            $html .= "<div>{$response}</div>";
            $html .= "</div>";
            $html .= "</div>";
        }
        
        $html .= "</div>"; // card-body
        $html .= "</div>"; // card
        
        return $html;
    }
    
    /**
     * Section title'ı güzelleştir
     */
    private function getSectionTitle(string $section): string
    {
        $titles = [
            'analysis' => 'Detaylı Analiz',
            'recommendations' => 'Öneriler',
            'score' => 'Puanlama',
            'keywords' => 'Anahtar Kelimeler',
            'content' => 'İçerik Analizi',
            'technical' => 'Teknik İnceleme',
            'performance' => 'Performans',
            'seo' => 'SEO Durumu'
        ];
        
        return $titles[$section] ?? ucfirst(str_replace(['_', '-'], ' ', $section));
    }
    
    /**
     * Section icon'unu belirle
     */
    private function getSectionIcon(string $section): string
    {
        $icons = [
            'analysis' => 'fas fa-search',
            'recommendations' => 'fas fa-lightbulb',
            'score' => 'fas fa-star',
            'keywords' => 'fas fa-key',
            'content' => 'fas fa-file-alt',
            'technical' => 'fas fa-cog',
            'performance' => 'fas fa-tachometer-alt',
            'seo' => 'fas fa-chart-line'
        ];
        
        return $icons[$section] ?? 'fas fa-info-circle';
    }
    
    /**
     * Score badge class'ını belirle
     */
    private function getScoreBadgeClass(string $score): string
    {
        if (is_numeric($score)) {
            $numScore = (float) $score;
            if ($numScore >= 80) return 'bg-success';
            if ($numScore >= 60) return 'bg-warning';
            if ($numScore >= 40) return 'bg-orange';
            return 'bg-danger';
        }
        
        return 'bg-primary';
    }
    
    /**
     * Modern HTML template sistemi uygula
     */
    private function applyModernTemplate(string $response, array $template, AIFeature $feature, string $baseHtml): string
    {
        $parsedResponse = $this->tryParseResponse($response);
        $sections = $template['sections'];
        $layout = $template['layout'] ?? 'card_accordion';
        
        // Layout'a göre render et
        if ($layout === 'card_accordion') {
            return $this->renderAccordionLayout($parsedResponse, $sections, $template, $feature, $baseHtml);
        } elseif ($layout === 'card_tabs') {
            return $this->renderTabsLayout($parsedResponse, $sections, $template, $feature, $baseHtml);
        }
        
        // Fallback to accordion
        return $this->renderAccordionLayout($parsedResponse, $sections, $template, $feature, $baseHtml);
    }
    
    /**
     * Accordion layout render
     */
    private function renderAccordionLayout($parsedResponse, $sections, $template, $feature, $baseHtml): string
    {
        $html = $baseHtml;
        
        if (is_array($parsedResponse) && is_array($sections)) {
            $accordionId = "modern-accordion-{$feature->slug}";
            $html .= "<div class='accordion accordion-flush' id='{$accordionId}'>";
            
            $sectionIndex = 0;
            foreach ($sections as $sectionKey => $sectionConfig) {
                if (isset($parsedResponse[$sectionKey])) {
                    $collapseId = "modern-collapse-{$feature->slug}-{$sectionIndex}";
                    $isFirstSection = $sectionIndex === 0;
                    
                    $html .= "<div class='accordion-item'>";
                    $html .= "<h2 class='accordion-header' id='modern-heading-{$sectionIndex}'>";
                    $html .= "<button class='accordion-button" . (!$isFirstSection ? ' collapsed' : '') . "' type='button' data-bs-toggle='collapse' data-bs-target='#{$collapseId}' aria-expanded='" . ($isFirstSection ? 'true' : 'false') . "' aria-controls='{$collapseId}'>";
                    $html .= "<div class='d-flex align-items-center'>";
                    $html .= "<i class='{$sectionConfig['icon']} me-2 text-primary'></i>";
                    $html .= "<span class='fw-semibold'>{$sectionConfig['title']}</span>";
                    $html .= "</div>";
                    $html .= "</button>";
                    $html .= "</h2>";
                    
                    $html .= "<div id='{$collapseId}' class='accordion-collapse collapse" . ($isFirstSection ? ' show' : '') . "' aria-labelledby='modern-heading-{$sectionIndex}' data-bs-parent='#{$accordionId}'>";
                    $html .= "<div class='accordion-body'>";
                    $html .= $this->renderSectionContent($parsedResponse[$sectionKey], $sectionConfig);
                    $html .= "</div>";
                    $html .= "</div>";
                    $html .= "</div>";
                    
                    $sectionIndex++;
                }
            }
            $html .= "</div>";
            
            // Confidence score
            if ($template['show_confidence'] ?? false) {
                $html .= $this->renderConfidenceScore($parsedResponse, $template);
            }
        }
        
        $html .= "</div>"; // card-body
        $html .= "</div>"; // card
        
        return $html;
    }
    
    /**
     * Tabs layout render
     */
    private function renderTabsLayout($parsedResponse, $sections, $template, $feature, $baseHtml): string
    {
        $html = $baseHtml;
        
        if (is_array($parsedResponse) && is_array($sections)) {
            $tabsId = "modern-tabs-{$feature->slug}";
            
            // Tab navigation
            $html .= "<nav>";
            $html .= "<div class='nav nav-tabs' id='{$tabsId}-nav' role='tablist'>";
            
            $sectionIndex = 0;
            foreach ($sections as $sectionKey => $sectionConfig) {
                if (isset($parsedResponse[$sectionKey])) {
                    $isActive = $sectionIndex === 0;
                    $tabId = "{$tabsId}-{$sectionKey}-tab";
                    $panelId = "{$tabsId}-{$sectionKey}";
                    
                    $html .= "<button class='nav-link" . ($isActive ? ' active' : '') . "' id='{$tabId}' data-bs-toggle='tab' data-bs-target='#{$panelId}' type='button' role='tab' aria-controls='{$panelId}' aria-selected='" . ($isActive ? 'true' : 'false') . "'>";
                    $html .= "<i class='{$sectionConfig['icon']} me-2'></i>";
                    $html .= $sectionConfig['title'];
                    $html .= "</button>";
                    
                    $sectionIndex++;
                }
            }
            
            $html .= "</div>";
            $html .= "</nav>";
            
            // Tab content
            $html .= "<div class='tab-content' id='{$tabsId}-content'>";
            
            $sectionIndex = 0;
            foreach ($sections as $sectionKey => $sectionConfig) {
                if (isset($parsedResponse[$sectionKey])) {
                    $isActive = $sectionIndex === 0;
                    $panelId = "{$tabsId}-{$sectionKey}";
                    
                    $html .= "<div class='tab-pane fade" . ($isActive ? ' show active' : '') . "' id='{$panelId}' role='tabpanel' aria-labelledby='{$panelId}-tab'>";
                    $html .= "<div class='py-3'>";
                    $html .= $this->renderSectionContent($parsedResponse[$sectionKey], $sectionConfig);
                    $html .= "</div>";
                    $html .= "</div>";
                    
                    $sectionIndex++;
                }
            }
            
            $html .= "</div>";
            
            // Confidence score
            if ($template['show_confidence'] ?? false) {
                $html .= $this->renderConfidenceScore($parsedResponse, $template);
            }
        }
        
        $html .= "</div>"; // card-body
        $html .= "</div>"; // card
        
        return $html;
    }
    
    /**
     * Section content'i type'ına göre render et - MODERN SİSTEM
     */
    private function renderSectionContent($content, $sectionConfig): string
    {
        $type = $sectionConfig['type'] ?? 'html_content';
        
        return match($type) {
            'badge_score' => $this->renderModernScoreCard($content),
            'list_group' => $this->renderModernAnalysisItems($content),
            'key_value_table' => $this->renderModernKeyValueTable($content),
            'alert_info' => $this->renderModernAlert($content),
            default => $this->renderModernContent($content)
        };
    }
    
    /**
     * List group render
     */
    private function renderListGroup($content): string
    {
        if (is_array($content)) {
            $html = "<div class='list-group list-group-flush'>";
            foreach ($content as $item) {
                $html .= "<div class='list-group-item border-0 px-0 py-2'>";
                $html .= "<div class='d-flex align-items-center'>";
                $html .= "<span class='status status-success me-3'></span>";
                $html .= "<span>" . (is_string($item) ? $item : json_encode($item)) . "</span>";
                $html .= "</div>";
                $html .= "</div>";
            }
            $html .= "</div>";
            return $html;
        }
        
        return $this->formatSectionContent($content);
    }
    
    /**
     * Responsive table render
     */
    private function renderResponsiveTable($content): string
    {
        if (is_array($content)) {
            $html = "<div class='table-responsive'>";
            $html .= "<table class='table table-sm table-striped'>";
            $html .= "<tbody>";
            
            foreach ($content as $key => $value) {
                $html .= "<tr>";
                $html .= "<td class='fw-semibold text-muted'>" . ucfirst(str_replace(['_', '-'], ' ', $key)) . "</td>";
                $html .= "<td>" . (is_string($value) ? $value : json_encode($value)) . "</td>";
                $html .= "</tr>";
            }
            
            $html .= "</tbody>";
            $html .= "</table>";
            $html .= "</div>";
            return $html;
        }
        
        return $this->formatSectionContent($content);
    }
    
    /**
     * Key-value table render
     */
    private function renderKeyValueTable($content): string
    {
        return $this->renderResponsiveTable($content);
    }
    
    /**
     * Badge score render
     */
    private function renderBadgeScore($content): string
    {
        if (is_numeric($content)) {
            $badgeClass = $this->getScoreBadgeClass($content);
            return "<div class='text-center'><span class='badge {$badgeClass} badge-lg fs-5 px-3 py-2'><i class='fas fa-star me-2'></i>{$content}</span></div>";
        }
        
        return $this->formatSectionContent($content);
    }
    
    /**
     * Alert info render
     */
    private function renderAlertInfo($content): string
    {
        $html = "<div class='alert alert-info d-flex align-items-start'>";
        $html .= "<div class='me-3'><i class='fas fa-info-circle'></i></div>";
        $html .= "<div class='flex-fill'>" . $this->formatSectionContent($content) . "</div>";
        $html .= "</div>";
        return $html;
    }
    
    /**
     * Confidence score render
     */
    private function renderConfidenceScore($parsedResponse, $template): string
    {
        if (isset($parsedResponse['score']) || isset($parsedResponse['confidence'])) {
            $score = $parsedResponse['score'] ?? $parsedResponse['confidence'] ?? 'N/A';
            $badgeClass = $this->getScoreBadgeClass($score);
            
            $html = "<div class='mt-4 text-center'>";
            $html .= "<div class='d-inline-flex align-items-center bg-light rounded-pill px-3 py-2'>";
            $html .= "<i class='fas fa-target text-muted me-2'></i>";
            $html .= "<span class='text-muted me-2'>Güven Skoru:</span>";
            $html .= "<span class='badge {$badgeClass}'>{$score}</span>";
            $html .= "</div>";
            $html .= "</div>";
            return $html;
        }
        
        return '';
    }
    
    /**
     * Response'ı JSON parse etmeye çalış
     */
    private function tryParseResponse(string $response): mixed
    {
        // JSON parse dene
        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        
        // JSON değilse string olarak döndür
        return $response;
    }
    
    /**
     * Section content'ini formatla
     */
    private function formatSectionContent($content): string
    {
        if (is_array($content)) {
            if (isset($content[0]) && is_string($content[0])) {
                // Array of strings - Modern list group format
                $html = "<div class='list-group list-group-flush'>";
                foreach ($content as $item) {
                    $html .= "<div class='list-group-item d-flex align-items-center px-0 py-2'>";
                    $html .= "<span class='status status-green me-3'></span>";
                    $html .= "<span>{$item}</span>";
                    $html .= "</div>";
                }
                $html .= "</div>";
                return $html;
            } else {
                // Associative array - Modern table format
                $html = "<div class='table-responsive'>";
                $html .= "<table class='table table-sm'>";
                $html .= "<tbody>";
                
                foreach ($content as $key => $value) {
                    $formattedValue = is_array($value) ? $this->formatSectionContent($value) : (string) $value;
                    $html .= "<tr>";
                    $html .= "<td class='w-50 text-muted'><strong>" . ucfirst(str_replace(['_', '-'], ' ', $key)) . "</strong></td>";
                    $html .= "<td>{$formattedValue}</td>";
                    $html .= "</tr>";
                }
                
                $html .= "</tbody>";
                $html .= "</table>";
                $html .= "</div>";
                return $html;
            }
        }
        
        // String content with paragraph formatting
        $contentStr = (string) $content;
        
        // Check if it's a score or numeric value
        if (is_numeric($contentStr)) {
            $score = (float) $contentStr;
            $badgeClass = $this->getScoreBadgeClass($contentStr);
            return "<span class='badge {$badgeClass} fs-6'>{$contentStr}</span>";
        }
        
        // Regular text with proper paragraphs
        $paragraphs = explode("\n\n", $contentStr);
        $html = "";
        
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (!empty($paragraph)) {
                // Check if it's a list item
                if (strpos($paragraph, "- ") === 0 || strpos($paragraph, "• ") === 0) {
                    $html .= "<div class='d-flex align-items-start mb-2'>";
                    $html .= "<span class='status status-blue me-2 mt-1'></span>";
                    $html .= "<span>" . substr($paragraph, 2) . "</span>";
                    $html .= "</div>";
                } else {
                    $html .= "<p class='mb-2'>{$paragraph}</p>";
                }
            }
        }
        
        return $html ?: $contentStr;
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

    /**
     * Feature'a özel response formatting - TEMPLATE SİSTEMİ İLE
     */
    private function formatFeatureResponse(string $response, AIFeature $feature, string $helperName): array
    {
        // Öncelikle feature'ın response_template'ini kontrol et
        $responseTemplate = null;
        if ($feature->response_template) {
            try {
                // response_template zaten array ise decode etme
                if (is_array($feature->response_template)) {
                    $responseTemplate = $feature->response_template;
                } else {
                    // response_template zaten array ise decode etme
                if (is_array($feature->response_template)) {
                    $responseTemplate = $feature->response_template;
                } else {
                    $responseTemplate = json_decode($feature->response_template, true);
                }
                }
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($responseTemplate)) {
                    // Template varsa onu kullan
                    $formattedHtml = $this->applyResponseTemplate($response, $responseTemplate, $feature);
                    
                    return [
                        'formatted_text' => $formattedHtml,
                        'raw_response' => $response,
                        'template_type' => $responseTemplate['format'] ?? 'templated',
                        'feature_name' => $feature->name,
                        'word_buffer_config' => [
                            'enabled' => true,
                            'delay_between_words' => 60,
                            'animation_duration' => 2000,
                            'container_selector' => '.ai-response-container',
                            'feature_type' => $feature->slug,
                            'helper_name' => $helperName
                        ]
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Feature response template parse hatası: {$feature->slug}", ['error' => $e->getMessage()]);
            }
        }
        
        // Template yoksa veya hatalıysa legacy handler'ları kullan
        return match($feature->slug) {
            'cevirmen', 'coklu-dil-cevirisi' => $this->formatTranslationResponse($response, $feature, $helperName),
            'baslik-uretici', 'meta-aciklama-uretici' => $this->formatContentGenerationResponse($response, $feature, $helperName),
            'schema-markup-uretici', 'schema-markup-onerileri' => $this->formatSchemaMarkupResponse($response, $feature, $helperName),
            default => $this->formatGenericResponse($response, $feature, $helperName)
        };
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
     * SEO Analiz response formatter - MODERN TABLER.IO TEMPLATE
     */
    private function formatSEOAnalysisResponse(string $response, AIFeature $feature, string $helperName): array
    {
        // AI'dan gelen JSON response'unu parse et
        $parsedData = $this->parseAIResponse($response);
        
        // Modern Tabler.io HTML template oluştur
        $htmlTemplate = $this->buildSEODashboard($parsedData, $feature);
        
        return [
            'formatted_text' => $htmlTemplate, // Modern HTML version
            'raw_response' => $response, // Original AI response
            'template_type' => 'seo_dashboard',
            'feature_name' => $feature->name,
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 50,
                'animation_duration' => 2000,
                'container_selector' => '.ai-response-container',
                'feature_type' => 'seo_analysis',
                'helper_name' => $helperName
            ]
        ];
    }
    
    /**
     * Modern SEO Dashboard HTML Template - TABLER.IO
     */
    private function buildSEODashboard(array $data, AIFeature $feature): string
    {
        $html = "<div class='ai-feature-response seo-dashboard'>";
        
        // Header
        $html .= "<div class='row mb-4'>";
        $html .= "<div class='col-12'>";
        $html .= "<div class='card border-0 bg-primary text-white'>";
        $html .= "<div class='card-body d-flex align-items-center'>";
        $html .= "<div class='me-3'><i class='{$feature->icon} fa-2x'></i></div>";
        $html .= "<div>";
        $html .= "<h3 class='mb-1 text-white'>{$feature->name}</h3>";
        $html .= "<p class='mb-0 opacity-75'>AI Analiz Sonuçları</p>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        
        // Hero Score (Eğer var ise)
        if (isset($data['hero_score'])) {
            $score = $data['hero_score'];
            $html .= "<div class='row mb-4'>";
            $html .= "<div class='col-12'>";
            $html .= "<div class='card'>";
            $html .= "<div class='card-body text-center'>";
            $html .= "<div class='display-1 fw-bold text-{$score['status']} mb-2'>{$score['value']}</div>";
            $html .= "<h4 class='text-muted'>{$score['label']}</h4>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
        }
        
        // Analysis Items
        if (isset($data['analysis']['items']) && is_array($data['analysis']['items'])) {
            $html .= "<div class='row mb-4'>";
            $html .= "<div class='col-12'>";
            $html .= "<div class='card'>";
            $html .= "<div class='card-header'>";
            $html .= "<h4 class='card-title'><i class='fas fa-chart-line me-2'></i>Analiz Sonuçları</h4>";
            $html .= "</div>";
            $html .= "<div class='card-body'>";
            $html .= "<div class='list-group list-group-flush'>";
            
            foreach ($data['analysis']['items'] as $item) {
                $iconClass = $item['status'] === 'success' ? 'text-success fas fa-check-circle' : 
                           ($item['status'] === 'warning' ? 'text-warning fas fa-exclamation-triangle' : 'text-danger fas fa-times-circle');
                           
                $html .= "<div class='list-group-item'>";
                $html .= "<div class='d-flex align-items-center'>";
                $html .= "<div class='me-3'><i class='{$iconClass}'></i></div>";
                $html .= "<div class='flex-fill'>";
                $html .= "<div class='fw-semibold'>{$item['label']}</div>";
                $html .= "<div class='text-muted'>{$item['detail']}</div>";
                $html .= "</div>";
                $html .= "</div>";
                $html .= "</div>";
            }
            
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
        }
        
        // Recommendations
        if (isset($data['recommendations']['cards']) && is_array($data['recommendations']['cards'])) {
            $html .= "<div class='row mb-4'>";
            $html .= "<div class='col-12'>";
            $html .= "<div class='card'>";
            $html .= "<div class='card-header'>";
            $html .= "<h4 class='card-title'><i class='fas fa-lightbulb me-2'></i>Önerilerim</h4>";
            $html .= "</div>";
            $html .= "<div class='card-body'>";
            $html .= "<div class='row'>";
            
            foreach ($data['recommendations']['cards'] as $index => $rec) {
                $priorityColor = $rec['priority'] === 'high' ? 'danger' : 
                               ($rec['priority'] === 'medium' ? 'warning' : 'success');
                               
                $html .= "<div class='col-md-6 mb-3'>";
                $html .= "<div class='card border-{$priorityColor}'>";
                $html .= "<div class='card-header bg-{$priorityColor}-lt'>";
                $html .= "<h5 class='card-title mb-0'>{$rec['title']}</h5>";
                $html .= "</div>";
                $html .= "<div class='card-body'>";
                $html .= "<p class='mb-0'>{$rec['action']}</p>";
                $html .= "</div>";
                $html .= "</div>";
                $html .= "</div>";
            }
            
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
        }
        
        // Technical Details
        if (isset($data['technical_details']['content'])) {
            $html .= "<div class='row mb-4'>";
            $html .= "<div class='col-12'>";
            $html .= "<div class='card'>";
            $html .= "<div class='card-header'>";
            $html .= "<h4 class='card-title'><i class='fas fa-cog me-2'></i>Teknik Detaylar</h4>";
            $html .= "</div>";
            $html .= "<div class='card-body'>";
            $html .= "<p>{$data['technical_details']['content']}</p>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
        }
        
        $html .= "</div>";
        return $html;
    }
    
    /**
     * AI Response JSON Parser - SADECE GERÇEK AI SONUÇLARI
     */
    private function parseAIResponse(string $response): array
    {
        // Önce JSON olarak parse etmeye çalış
        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        
        // JSON değilse text response'u parse etmeye çalış
        return $this->parseTextResponse($response);
    }
    
    /**
     * Text response'u parse et ve structure'a çevir
     */
    private function parseTextResponse(string $response): array
    {
        // Text içinden score, analiz, öneri gibi bölümleri çıkar
        $data = [];
        
        // Score extraction
        if (preg_match('/(\d+\/\d+|\d+%)/i', $response, $matches)) {
            $data['hero_score'] = [
                'value' => $matches[1],
                'label' => 'AI Analiz Skoru',
                'status' => 'primary'
            ];
        }
        
        // Text'i paragraflar halinde böl
        $paragraphs = explode("\n\n", $response);
        $analysisItems = [];
        $recommendations = [];
        
        foreach ($paragraphs as $paragraph) {
            if (!empty(trim($paragraph))) {
                // Öneri içeren paragrafları tespit et
                if (preg_match('/(öneri|tavsiye|yapmanız gereken|eklemelisiniz)/i', $paragraph)) {
                    $recommendations[] = [
                        'title' => 'AI Önerisi',
                        'action' => trim($paragraph),
                        'priority' => 'medium'
                    ];
                } else {
                    // Diğer paragrafları analiz olarak değerlendir
                    $analysisItems[] = [
                        'label' => 'Analiz',
                        'status' => 'info',
                        'detail' => trim($paragraph)
                    ];
                }
            }
        }
        
        if (!empty($analysisItems)) {
            $data['analysis'] = [
                'title' => 'AI Analiz Sonuçları',
                'items' => $analysisItems
            ];
        }
        
        if (!empty($recommendations)) {
            $data['recommendations'] = [
                'title' => 'AI Önerileri',
                'cards' => $recommendations
            ];
        }
        
        $data['technical_details'] = [
            'content' => 'AI tarafından otomatik oluşturulan analiz sonuçları.'
        ];
        
        return $data;
    }
    
    /**
     * Generic Feature Response Formatter - MODERN TABLER.IO
     */
    private function formatGenericResponse(string $response, AIFeature $feature, string $helperName): array
    {
        // Simple modern card layout
        $html = "<div class='ai-feature-response generic-response'>";
        $html .= "<div class='card'>";
        $html .= "<div class='card-header bg-primary-lt'>";
        $html .= "<div class='d-flex align-items-center'>";
        $html .= "<div class='me-3'><i class='{$feature->icon} text-primary'></i></div>";
        $html .= "<h4 class='card-title mb-0'>{$feature->name}</h4>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='card-body'>";
        $html .= "<div class='prose'>";
        $html .= nl2br(htmlspecialchars($response));
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";

        return [
            'formatted_text' => $html,
            'raw_response' => $response,
            'template_type' => 'generic',
            'feature_name' => $feature->name,
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 80,
                'animation_duration' => 2500,
                'container_selector' => '.ai-response-container',
                'feature_type' => 'generic',
                'helper_name' => $helperName
            ]
        ];
    }

    /**
     * Translation Response Formatter - MODERN TABLER.IO
     */
    private function formatTranslationResponse(string $response, AIFeature $feature, string $helperName): array
    {
        $html = "<div class='ai-feature-response translation-response'>";
        $html .= "<div class='card'>";
        $html .= "<div class='card-header bg-success-lt'>";
        $html .= "<div class='d-flex align-items-center'>";
        $html .= "<div class='me-3'><i class='fas fa-language text-success fa-lg'></i></div>";
        $html .= "<h4 class='card-title mb-0'>{$feature->name} Sonucu</h4>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='card-body'>";
        $html .= "<div class='translation-result p-3 bg-light rounded'>";
        $html .= nl2br(htmlspecialchars($response));
        $html .= "</div>";
        $html .= "<div class='mt-3 d-flex gap-2'>";
        $html .= "<button class='btn btn-outline-primary btn-sm' onclick='navigator.clipboard.writeText(this.closest(\".card-body\").querySelector(\".translation-result\").innerText)'>";
        $html .= "<i class='fas fa-copy me-1'></i>Kopyala</button>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";

        return [
            'formatted_text' => $html,
            'raw_response' => $response,
            'template_type' => 'translation',
            'feature_name' => $feature->name,
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 60,
                'animation_duration' => 2000,
                'container_selector' => '.ai-response-container',
                'feature_type' => 'translation',
                'helper_name' => $helperName
            ]
        ];
    }

    /**
     * Content Generation Response Formatter - MODERN TABLER.IO
     */
    private function formatContentGenerationResponse(string $response, AIFeature $feature, string $helperName): array
    {
        $html = "<div class='ai-feature-response content-generation-response'>";
        $html .= "<div class='card'>";
        $html .= "<div class='card-header bg-info-lt'>";
        $html .= "<div class='d-flex align-items-center justify-content-between'>";
        $html .= "<div class='d-flex align-items-center'>";
        $html .= "<div class='me-3'><i class='fas fa-pen-fancy text-info fa-lg'></i></div>";
        $html .= "<h4 class='card-title mb-0'>{$feature->name}</h4>";
        $html .= "</div>";
        $html .= "<div class='badge bg-info-lt text-info'>Üretildi</div>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='card-body'>";
        $html .= "<div class='generated-content p-3 border rounded bg-white'>";
        $html .= nl2br(htmlspecialchars($response));
        $html .= "</div>";
        $html .= "<div class='mt-3 d-flex gap-2'>";
        $html .= "<button class='btn btn-primary btn-sm' onclick='navigator.clipboard.writeText(this.closest(\".card-body\").querySelector(\".generated-content\").innerText)'>";
        $html .= "<i class='fas fa-copy me-1'></i>Kopyala</button>";
        $html .= "<button class='btn btn-outline-secondary btn-sm' onclick='this.closest(\".card-body\").querySelector(\".generated-content\").contentEditable=\"true\"; this.style.display=\"none\"; this.nextElementSibling.style.display=\"inline-block\"'>";
        $html .= "<i class='fas fa-edit me-1'></i>Düzenle</button>";
        $html .= "<button class='btn btn-success btn-sm' style='display:none' onclick='this.closest(\".card-body\").querySelector(\".generated-content\").contentEditable=\"false\"; this.style.display=\"none\"; this.previousElementSibling.style.display=\"inline-block\"'>";
        $html .= "<i class='fas fa-check me-1'></i>Tamam</button>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";

        return [
            'formatted_text' => $html,
            'raw_response' => $response,
            'template_type' => 'content_generation',
            'feature_name' => $feature->name,
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 70,
                'animation_duration' => 3000,
                'container_selector' => '.ai-response-container',
                'feature_type' => 'content_generation',
                'helper_name' => $helperName
            ]
        ];
    }

    /**
     * Schema Markup Response Formatter - MODERN TABLER.IO
     */
    private function formatSchemaMarkupResponse(string $response, AIFeature $feature, string $helperName): array
    {
        $html = "<div class='ai-feature-response schema-markup-response'>";
        $html .= "<div class='card'>";
        $html .= "<div class='card-header bg-warning-lt'>";
        $html .= "<div class='d-flex align-items-center'>";
        $html .= "<div class='me-3'><i class='fas fa-code text-warning fa-lg'></i></div>";
        $html .= "<h4 class='card-title mb-0'>{$feature->name}</h4>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='card-body'>";
        $html .= "<div class='alert alert-info'>";
        $html .= "<i class='fas fa-info-circle me-2'></i>";
        $html .= "Aşağıdaki Schema Markup kodunu sitenizin head bölümüne ekleyin.";
        $html .= "</div>";
        $html .= "<div class='schema-code'>";
        $html .= "<pre class='bg-dark text-light p-3 rounded'><code>";
        $html .= htmlspecialchars($response);
        $html .= "</code></pre>";
        $html .= "</div>";
        $html .= "<div class='mt-3 d-flex gap-2'>";
        $html .= "<button class='btn btn-primary btn-sm' onclick='navigator.clipboard.writeText(this.closest(\".card-body\").querySelector(\"code\").innerText)'>";
        $html .= "<i class='fas fa-copy me-1'></i>Kodu Kopyala</button>";
        $html .= "<a href='https://search.google.com/test/rich-results' target='_blank' class='btn btn-outline-success btn-sm'>";
        $html .= "<i class='fas fa-external-link-alt me-1'></i>Google'da Test Et</a>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";

        return [
            'formatted_text' => $html,
            'raw_response' => $response,
            'template_type' => 'schema_markup',
            'feature_name' => $feature->name,
            'word_buffer_config' => [
                'enabled' => true,
                'delay_between_words' => 30,
                'animation_duration' => 1500,
                'container_selector' => '.ai-response-container',
                'feature_type' => 'schema_markup',
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
     * MODERN HTML RENDER METHODS - YENİ TASARIM SİSTEMİ
     * =======================================================================
     */
    
    /**
     * Modern Score Card - Hero score güzel tasarım
     */
    private function renderModernScoreCard($content): string
    {
        if (is_array($content)) {
            $value = $content['value'] ?? '0';
            $label = $content['label'] ?? 'Skor';
            $status = $content['status'] ?? 'primary';
            
            $scoreValue = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            $statusClass = match($status) {
                'warning' => 'warning',
                'danger' => 'danger',
                'success' => 'success',
                default => 'primary'
            };
            
            $html = "<div class='text-center py-4'>";
            $html .= "<div class='display-3 fw-bold text-{$statusClass} mb-2'>{$scoreValue}</div>";
            $html .= "<h5 class='text-muted mb-0'>{$label}</h5>";
            $html .= "<div class='progress mt-3' style='height: 8px;'>";
            $html .= "<div class='progress-bar bg-{$statusClass}' style='width: {$scoreValue}%'></div>";
            $html .= "</div>";
            $html .= "</div>";
            
            return $html;
        }
        
        return $this->renderModernContent($content);
    }
    
    /**
     * Modern Analysis Items - Analiz sonuçları card'lar halinde
     */
    private function renderModernAnalysisItems($content): string
    {
        // JSON string ise decode et
        if (is_string($content) && str_starts_with(trim($content), '[')) {
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $content = $decoded;
            }
        }
        
        if (is_array($content)) {
            $html = "<div class='row g-3'>";
            
            foreach ($content as $item) {
                if (is_array($item) && isset($item['label'])) {
                    $status = $item['status'] ?? 'info';
                    $detail = $item['detail'] ?? $item['label'];
                    
                    $statusIcon = match($status) {
                        'success' => 'fas fa-check-circle text-success',
                        'warning' => 'fas fa-exclamation-triangle text-warning',
                        'danger' => 'fas fa-times-circle text-danger',
                        default => 'fas fa-info-circle text-info'
                    };
                    
                    $statusBg = match($status) {
                        'success' => 'bg-success-lt',
                        'warning' => 'bg-warning-lt',
                        'danger' => 'bg-danger-lt',
                        default => 'bg-info-lt'
                    };
                    
                    $html .= "<div class='col-md-6'>";
                    $html .= "<div class='card {$statusBg} h-100'>";
                    $html .= "<div class='card-body'>";
                    $html .= "<div class='d-flex align-items-start'>";
                    $html .= "<div class='me-3'><i class='{$statusIcon} fs-4'></i></div>";
                    $html .= "<div>";
                    $html .= "<h6 class='card-title mb-2'>{$item['label']}</h6>";
                    $html .= "<p class='card-text text-muted mb-0'>{$detail}</p>";
                    $html .= "</div>";
                    $html .= "</div>";
                    $html .= "</div>";
                    $html .= "</div>";
                    $html .= "</div>";
                }
            }
            
            $html .= "</div>";
            return $html;
        }
        
        return $this->renderModernContent($content);
    }
    
    /**
     * Modern Key-Value Table - Temiz tablo tasarımı
     */
    private function renderModernKeyValueTable($content): string
    {
        if (is_array($content)) {
            $html = "<div class='table-responsive'>";
            $html .= "<table class='table table-borderless'>";
            
            foreach ($content as $key => $value) {
                $displayValue = is_array($value) ? json_encode($value) : (string) $value;
                $html .= "<tr>";
                $html .= "<td class='fw-semibold text-muted w-40'>" . ucfirst(str_replace(['_', '-'], ' ', $key)) . "</td>";
                $html .= "<td class='text-dark'>{$displayValue}</td>";
                $html .= "</tr>";
            }
            
            $html .= "</table>";
            $html .= "</div>";
            return $html;
        }
        
        return $this->renderModernContent($content);
    }
    
    /**
     * Modern Alert - Vurgulu bilgi kutusu
     */
    private function renderModernAlert($content): string
    {
        $text = is_array($content) ? json_encode($content) : (string) $content;
        
        $html = "<div class='alert alert-info d-flex align-items-start'>";
        $html .= "<div class='me-3'><i class='fas fa-lightbulb text-info fs-4'></i></div>";
        $html .= "<div class='flex-fill'>";
        $html .= "<h6 class='alert-heading mb-2'>Analiz Özeti</h6>";
        $html .= "<p class='mb-0'>{$text}</p>";
        $html .= "</div>";
        $html .= "</div>";
        
        return $html;
    }
    
    /**
     * Modern Content - Genel içerik renderer
     */
    private function renderModernContent($content): string
    {
        // JSON string ise güzel formatla
        if (is_string($content) && (str_starts_with(trim($content), '[') || str_starts_with(trim($content), '{'))) {
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $this->renderJsonAsCards($decoded);
            }
        }
        
        // Array ise güzel formatla
        if (is_array($content)) {
            return $this->renderJsonAsCards($content);
        }
        
        // String content'i paragraf halinde render et
        $text = (string) $content;
        $paragraphs = explode("\n\n", $text);
        $html = "";
        
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (!empty($paragraph)) {
                $html .= "<p class='mb-3'>{$paragraph}</p>";
            }
        }
        
        return $html ?: "<p class='text-muted'>İçerik bulunamadı</p>";
    }
    
    /**
     * JSON içeriği modern card'lar halinde render et
     */
    private function renderJsonAsCards($data): string
    {
        if (!is_array($data)) {
            return "<p class='text-muted'>Geçersiz veri formatı</p>";
        }
        
        $html = "<div class='row g-3'>";
        
        foreach ($data as $index => $item) {
            if (is_array($item)) {
                $title = $item['title'] ?? $item['label'] ?? $item['name'] ?? "Öğe " . ($index + 1);
                $description = $item['action'] ?? $item['detail'] ?? $item['description'] ?? '';
                $priority = $item['priority'] ?? 'medium';
                
                $priorityColor = match($priority) {
                    'high' => 'danger',
                    'medium' => 'warning',
                    'low' => 'success',
                    default => 'info'
                };
                
                $html .= "<div class='col-md-6'>";
                $html .= "<div class='card border-{$priorityColor} h-100'>";
                $html .= "<div class='card-body'>";
                $html .= "<h6 class='card-title text-{$priorityColor}'>{$title}</h6>";
                if ($description) {
                    $html .= "<p class='card-text text-muted'>{$description}</p>";
                }
                if (isset($item['priority'])) {
                    $html .= "<span class='badge bg-{$priorityColor}-lt text-{$priorityColor}'>" . ucfirst($priority) . " Öncelik</span>";
                }
                $html .= "</div>";
                $html .= "</div>";
                $html .= "</div>";
            }
        }
        
        $html .= "</div>";
        return $html;
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
    private function buildKeywordAnalysisHTML(string $response, AIFeature $feature): string { return $this->buildGenericFeatureHTML($response, $feature); }
    private function buildMetaGeneratorHTML(string $response, AIFeature $feature): string { return $this->buildGenericFeatureHTML($response, $feature); }
    private function buildCompetitionAnalysisHTML(string $response, AIFeature $feature): string { return $this->buildGenericFeatureHTML($response, $feature); }
    private function extractImprovements(string $response): array { return []; }

    /**
     * Apply modern card template (YENİ SİSTEM - accordion yerine)
     */
    private function applyModernCardTemplate($response, $template, $feature, $html): string
    {
        $sections = $template['sections'] ?? [];
        $parsedResponse = $this->tryParseResponse($response);
        
        if (!is_array($parsedResponse) || empty($sections)) {
            // Fallback to simple card
            $html .= '<div class="alert alert-info">';
            $html .= '<div class="d-flex"><i class="fas fa-info-circle me-2"></i>';
            $html .= '<div>' . nl2br(e($response)) . '</div></div></div>';
            return $html;
        }
        
        // Modern card grid layout
        $html .= '<div class="row g-3">';
        
        foreach ($sections as $sectionKey => $sectionConfig) {
            if (!isset($parsedResponse[$sectionKey])) {
                continue;
            }
            
            $content = $parsedResponse[$sectionKey];
            $title = $sectionConfig['title'] ?? $this->getSectionTitle($sectionKey);
            $icon = $sectionConfig['icon'] ?? $this->getSectionIcon($sectionKey);
            $type = $sectionConfig['type'] ?? 'list_group';
            
            // Card boyutu belirle
            $colClass = 'col-12';
            if (count($sections) > 1) {
                $colClass = 'col-12 col-lg-6';
                if ($type === 'badge_score') {
                    $colClass = 'col-12 col-md-4'; // Score için daha küçük
                }
            }
            
            $html .= '<div class="' . $colClass . ' mb-4">';
            $html .= '<div class="card h-100 shadow-sm">';
            
            // Card header with gradient
            $html .= '<div class="card-header bg-primary-subtle border-0">';
            $html .= '<div class="d-flex align-items-center">';
            $html .= '<i class="' . $icon . ' me-2 text-primary fs-5"></i>';
            $html .= '<h6 class="card-title mb-0 fw-bold">' . e($title) . '</h6>';
            $html .= '</div></div>';
            
            // Card body
            $html .= '<div class="card-body">';
            
            // Content based on type
            switch ($type) {
                case 'badge_score':
                    $html .= $this->renderModernScoreCard($content);
                    break;
                    
                case 'list_group':
                    $html .= $this->renderModernAnalysisItems($content);
                    break;
                    
                case 'key_value_table':
                    $html .= $this->renderModernKeyValueTable($content);
                    break;
                    
                case 'alert_info':
                    $html .= $this->renderModernAlert($content);
                    break;
                    
                default:
                    $html .= $this->renderModernContent($content);
                    break;
            }
            
            $html .= '</div>'; // card-body
            $html .= '</div>'; // card
            $html .= '</div>'; // col
        }
        
        $html .= '</div>'; // row
        
        // Confidence score ayrı olarak göster
        if (($template['show_confidence'] ?? false) && (isset($parsedResponse['score']) || isset($parsedResponse['confidence']))) {
            $score = $parsedResponse['score'] ?? $parsedResponse['confidence'] ?? 'N/A';
            $badgeClass = $this->getScoreBadgeClass($score);
            
            $html .= '<div class="mt-3 text-center">';
            $html .= '<span class="badge ' . $badgeClass . ' badge-lg px-3 py-2">';
            $html .= '<i class="fas fa-target me-1"></i> Güven Skoru: ' . e($score);
            $html .= '</span>';
            $html .= '</div>';
        }
        
        return $html;
    }
    
    private function extractBeforeAfter(string $response): array { return []; }
    private function extractDetails(string $response): array { return []; }
    private function buildImprovementCards(array $improvements): string { return ''; }
    private function buildBeforeAfterSection(array $beforeAfter): string { return ''; }
    
    /**
     * AI response'unun HTML formatında olup olmadığını kontrol et
     */
    private function isHtmlResponse(string $response): bool
    {
        // HTML tag'leri var mı kontrol et
        $htmlTags = ['<div', '<card', '<span', '<ul', '<li', '<h1', '<h2', '<h3', '<h4', '<h5', '<h6', '<p', '<i class="fas'];
        
        foreach ($htmlTags as $tag) {
            if (stripos($response, $tag) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Plain text response'u basit card ile wrap et
     */
    private function wrapResponseInCard(string $response, string $featureName): string
    {
        return '
        <div class="card">
            <div class="card-header bg-primary-subtle">
                <h6><i class="fas fa-robot me-2"></i>' . e($featureName) . '</h6>
            </div>
            <div class="card-body">
                <div class="ai-response-content">
                    ' . nl2br(e($response)) . '
                </div>
            </div>
        </div>';
    }

    /**
     * Widget Feature Request Handler
     * AI Widget sisteminden gelen istekleri işler
     */
    private function handleWidgetFeature(array $params): array
    {
        $featureSlug = $params['feature_slug'] ?? '';
        $context = $params['context'] ?? 'page';
        $entityId = $params['entity_id'] ?? null;
        $entityType = $params['entity_type'] ?? 'page';
        $data = $params['data'] ?? [];
        $userId = $params['user_id'] ?? null;

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

        try {
            // AI Service ile işlem yap - Feature object geçmek gerekiyor
            $result = $this->aiService->askFeature(
                $feature,
                implode(' ', array_filter([
                    "Context: {$context}",
                    "Entity ID: {$entityId}",
                    "Entity Type: {$entityType}",
                    "Data: " . json_encode($data)
                ]))
            );

            // AIService.askFeature() string yanıt döndürüyor, array değil
            if (is_string($result) && !empty($result)) {
                return [
                    'success' => true,
                    'response' => $result,
                    'formatted_response' => $this->formatWidgetResponse($result, $feature),
                    'tokens_used' => 0, // Token bilgisi ayrıca alınabilir
                    'suggestions' => $this->extractSuggestions($result, $context)
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'AI Feature yanıt alınamadı'
                ];
            }

        } catch (\Exception $e) {
            Log::error('AI Widget Feature Error', [
                'feature_slug' => $featureSlug,
                'context' => $context,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'AI Widget Feature işleminde hata: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format AI Widget Response for HTML display
     */
    private function formatWidgetResponse(string $response, $feature = null): string
    {
        // Basic HTML formatting
        $formatted = nl2br(e($response));
        
        // Feature-specific formatting can be added here
        if ($feature && $feature->response_template) {
            // Apply feature-specific template formatting if needed
            $template = json_decode($feature->response_template, true);
            if ($template && isset($template['format'])) {
                // Custom formatting based on template
                switch ($template['format']) {
                    case 'seo_analysis':
                        return $this->formatSEOAnalysis($response);
                    case 'keyword_research':
                        return $this->formatKeywordResearch($response);
                    default:
                        return $formatted;
                }
            }
        }
        
        return $formatted;
    }

    /**
     * Extract actionable suggestions from AI response
     */
    private function extractSuggestions(string $response, string $context = 'page'): array
    {
        $suggestions = [];
        
        // Simple pattern matching for suggestions
        // This can be enhanced based on AI response patterns
        
        if (strpos($response, 'başlık') !== false || strpos($response, 'title') !== false) {
            $suggestions['title'] = 'Başlık önerileri mevcut';
        }
        
        if (strpos($response, 'meta') !== false || strpos($response, 'description') !== false) {
            $suggestions['meta'] = 'Meta açıklama önerileri mevcut';
        }
        
        if (strpos($response, 'anahtar kelime') !== false || strpos($response, 'keyword') !== false) {
            $suggestions['keywords'] = 'Anahtar kelime önerileri mevcut';
        }
        
        return $suggestions;
    }
}