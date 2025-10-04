<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Services\DeepSeekService;
use Modules\AI\App\Services\ConversationService;
use Modules\AI\App\Services\PromptService;
use Modules\AI\App\Services\AIPriorityEngine;
use Modules\AI\App\Services\AIProviderManager;
use Modules\AI\App\Services\ModelBasedCreditService;
use Modules\AI\App\Services\SilentFallbackService;
use Modules\AI\App\Services\Context\ContextEngine;
use Modules\AI\App\Services\ConversationTracker;
use App\Helpers\TenantHelpers;
use App\Services\AITokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $deepSeekService;
    protected $conversationService;
    protected $promptService;
    protected $aiTokenService;
    protected $providerManager;
    protected $silentFallbackService;
    protected $currentProvider;
    protected $currentService;
    protected $contextEngine;
    /**
     * Constructor
     *
     * @param DeepSeekService|null $deepSeekService
     * @param ConversationService|null $conversationService
     * @param PromptService|null $promptService
     */
    public function __construct(
        ?DeepSeekService $deepSeekService = null,
        ?ConversationService $conversationService = null,
        ?PromptService $promptService = null,
        ?AITokenService $aiTokenService = null,
        ?ContextEngine $contextEngine = null
    ) {
        // Provider Manager'ı yükle
        $this->providerManager = new AIProviderManager();
        
        // Silent Fallback Service'i yükle
        $this->silentFallbackService = new SilentFallbackService(
            app(ModelBasedCreditService::class),
            $this->providerManager
        );
        
        // Varsayılan provider'ı al - Silent Fallback aktif
        try {
            $providerData = $this->providerManager->getProviderServiceWithoutFailover();
            $this->currentProvider = $providerData['provider'];
            $this->currentService = $providerData['service'];
            
            Log::info('🔥 AI Provider loaded successfully', [
                'provider' => $this->currentProvider->name,
                'model' => $this->currentProvider->default_model
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ AI Provider loading failed - Attempting Silent Fallback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Silent Fallback dene
            $fallbackResult = $this->silentFallbackService->attemptSilentFallback(
                'unknown', // Original provider bilinmiyor
                'unknown', // Original model bilinmiyor
                'Initial provider selection failed',
                [],
                $e->getMessage()
            );
            
            if ($fallbackResult) {
                $this->currentProvider = $fallbackResult['provider'];
                $this->currentService = $fallbackResult['service'];

                Log::info('✅ Silent Fallback activated during initialization', [
                    'fallback_provider' => $this->currentProvider->name,
                    'fallback_model' => $fallbackResult['model']
                ]);
            } else {
                // Silent fail - AI features disabled but system continues to boot
                Log::warning('⚠️ AI Provider not configured - AI features disabled', [
                    'error' => $e->getMessage()
                ]);

                $this->currentProvider = null;
                $this->currentService = null;
            }
        }
        
        // Diğer servisleri oluştur
        $this->promptService = $promptService ?? new PromptService();
        $this->aiTokenService = $aiTokenService ?? new AITokenService();
        $this->contextEngine = $contextEngine ?? app(ContextEngine::class);

        // ConversationService en son oluşturulmalı çünkü diğer servislere bağımlı
        // Eğer provider yoksa ConversationService de null olacak
        if ($this->currentService) {
            $this->conversationService = $conversationService ??
                new ConversationService($this->currentService, $this->aiTokenService);
        } else {
            $this->conversationService = null;
        }
    }

    /**
     * AI'ya doğrudan soru sor (STREAMING)
     *
     * @param string $prompt
     * @param array $options
     * @param callable|null $streamCallback
     * @return string|null
     */
    public function askStream(string $prompt, array $options = [], ?callable $streamCallback = null)
    {
        // AI Provider kontrolü - yoksa hata döndür
        if (!$this->currentService || !$this->currentProvider) {
            Log::warning('AI Provider not configured - askStream() called but provider unavailable');
            if ($streamCallback) {
                $streamCallback('AI provider not configured. Please configure an AI provider first.');
            }
            return null;
        }

        // Modern token sistemi kontrolü
        $tenant = tenant();
        if ($tenant) {
            $tokensNeeded = $this->aiTokenService->estimateTokenCost('chat_message', ['message' => $prompt]);
            
            if (!$this->aiTokenService->canUseTokens($tenant, $tokensNeeded)) {
                return "Üzgünüm, yetersiz AI token bakiyeniz var veya aylık limitinize ulaştınız.";
            }
        } else {
            Log::warning('Tenant bulunmadı, AI isteği için basit token kontrolü yapılıyor');
        }

        // YENİ PRIORITY ENGINE SİSTEMİ - Tenant context ile
        $customPrompt = '';
        if (isset($options['prompt_id'])) {
            $promptModel = \Modules\AI\App\Models\Prompt::find($options['prompt_id']);
            if ($promptModel) {
                $customPrompt = $promptModel->content;
            }
        } elseif (isset($options['custom_prompt'])) {
            $customPrompt = $options['custom_prompt'];
        } elseif (isset($options['context'])) {
            $customPrompt = $options['context'];
        }

        // Build full system prompt with TENANT CONTEXT + USER INPUT
        $options['user_input'] = $prompt; // Uzunluk algılama için
        $systemPrompt = $this->buildFullSystemPrompt($customPrompt, $options);

        // Mesajları formatla
        $messages = [];
        
        if ($systemPrompt) {
            $messages[] = [
                'role' => 'system',
                'content' => $systemPrompt
            ];
        }
        
        // Conversation history'yi ekle (eğer varsa)
        if (isset($options['conversation_history']) && is_array($options['conversation_history'])) {
            foreach ($options['conversation_history'] as $historyMessage) {
                if (isset($historyMessage['role']) && isset($historyMessage['content'])) {
                    $messages[] = [
                        'role' => $historyMessage['role'],
                        'content' => $historyMessage['content']
                    ];
                }
            }
        }
        
        // DEBUG: Prompt tipini kontrol et
        if (!is_string($prompt)) {
            Log::error('🚨 Prompt is not string!', [
                'prompt_type' => gettype($prompt),
                'prompt_content' => $prompt,
                'is_array' => is_array($prompt),
                'is_object' => is_object($prompt)
            ]);
            
            // Eğer array ise, muhtemelen yanlış parametre sırası
            if (is_array($prompt)) {
                $prompt = 'Debug: Array received as prompt';
            } else {
                $prompt = 'Debug: Non-string prompt received';
            }
        }
        
        $messages[] = [
            'role' => 'user',
            'content' => $prompt
        ];

        // ✨ STREAMING RESPONSE - Dinamik provider kullanımı
        $startTime = microtime(true);
        $response = $this->currentService->generateCompletionStream($messages, $streamCallback);
        
        // Provider performansını güncelle
        if ($this->currentProvider) {
            $responseTime = (microtime(true) - $startTime) * 1000;
            $this->providerManager->updateProviderPerformance($this->currentProvider->name, $responseTime);
        }

        // Model bazlı kredi düşümü (YENİ SISTEM)
        if ($tenant && isset($response['usage'])) {
            $currentModel = $effectiveTenant?->default_ai_model ?? $this->currentProvider->default_model;
            $inputTokens = $response['usage']['prompt_tokens'] ?? $response['usage']['input_tokens'] ?? 0;
            $outputTokens = $response['usage']['completion_tokens'] ?? $response['usage']['output_tokens'] ?? 0;
            
            $usedCredits = ai_use_credits_with_model(
                $effectiveTenant?->id ?? 1,
                $this->currentProvider->id,
                $currentModel,
                $inputTokens,
                $outputTokens,
                'chat_message'
            );
        }

        return $response['response'] ?? null;
    }

    /**
     * AI'ya doğrudan soru sor (konuşma oluşturmadan)
     *
     * @param string $prompt
     * @param array $options
     * @param bool $stream
     * @return string|null|\Closure
     */
    public function ask(string $prompt, array $options = [], bool $stream = false)
    {
        // AI Provider kontrolü - yoksa hata döndür
        if (!$this->currentService || !$this->currentProvider) {
            Log::warning('AI Provider not configured - ask() called but provider unavailable');
            return [
                'success' => false,
                'error' => 'AI provider not configured. Please configure an AI provider first.',
                'content' => null
            ];
        }

        // Modern token sistemi kontrolü - TEST İÇİN GEÇİCİ OLARAK DEVRE DIŞI
        $tenant = tenant(); // tenant değişkeni hala gerekli
        /*
        if ($tenant) {
            $tokensNeeded = $this->aiTokenService->estimateTokenCost('chat_message', ['message' => $prompt]);
            
            if (!$this->aiTokenService->canUseTokens($tenant, $tokensNeeded)) {
                return "Üzgünüm, yetersiz AI token bakiyeniz var veya aylık limitinize ulaştınız.";
            }
        } else {
            // Central admin için basit kontrol (tenant yoksa)
            // limitService yerine basit bir kontrol yap
            Log::warning('Tenant bulunmadı, AI isteği için basit token kontrolü yapılıyor');
        }
        */

        // YENİ PRIORITY ENGINE SİSTEMİ - Tenant context ile
        $customPrompt = '';
        if (isset($options['prompt_id'])) {
            $promptModel = \Modules\AI\App\Models\Prompt::find($options['prompt_id']);
            if ($promptModel) {
                $customPrompt = $promptModel->content;
            }
        } elseif (isset($options['custom_prompt'])) {
            $customPrompt = $options['custom_prompt'];
        } elseif (isset($options['context'])) {
            $customPrompt = $options['context'];
        }

        // Build full system prompt with TENANT CONTEXT + USER INPUT
        $options['user_input'] = $prompt; // Uzunluk algılama için
        $systemPrompt = $this->buildFullSystemPrompt($customPrompt, $options);

        // Mesajları formatla
        $messages = [];
        
        if ($systemPrompt) {
            $messages[] = [
                'role' => 'system',
                'content' => $systemPrompt
            ];
        }
        
        // 🧠 CONVERSATION HISTORY - Hafıza sistemi
        if (isset($options['conversation_history']) && is_array($options['conversation_history'])) {
            foreach ($options['conversation_history'] as $historyMessage) {
                if (isset($historyMessage['role']) && isset($historyMessage['content']) 
                    && !empty(trim($historyMessage['content']))) {
                    $messages[] = [
                        'role' => $historyMessage['role'],
                        'content' => $historyMessage['content']
                    ];
                }
            }
            
            Log::info('🧠 AIService: Conversation history eklendi', [
                'history_count' => count($options['conversation_history']),
                'total_messages_to_api' => count($messages) + 1 // +1 for user message
            ]);
        }
        
        $messages[] = [
            'role' => 'user',
            'content' => $prompt
        ];

        // AI'dan yanıt al - Dinamik provider kullanımı
        $startTime = microtime(true);
        $apiResponse = $this->currentService->ask($messages, $stream);
        
        // Provider performansını güncelle
        if ($this->currentProvider) {
            $responseTime = (microtime(true) - $startTime) * 1000;
            $this->providerManager->updateProviderPerformance($this->currentProvider->name, $responseTime);
        }
        
        // API response'u parse et (string veya array olabilir)
        $response = is_array($apiResponse) ? ($apiResponse['response'] ?? $apiResponse) : $apiResponse;
        
        // YENİ: POST-PROCESSING - Yanıtı düzelt
        if ($response && !$stream) {
            $response = $this->enforceStructure($response, $options);
        }
        
        if ($response && !$stream) {
            // YENİ MERKEZİ KREDİ DÜŞME SİSTEMİ
            if ($tenant) {
                // API response'undan token bilgilerini al
                $tokenData = is_array($apiResponse) ? $apiResponse : [];
                
                // Provider adını belirle  
                $providerName = $this->currentProvider ? $this->currentProvider->name : 'unknown';
                
                // Merkezi kredi düşme sistemi
                ai_use_calculated_credits($tokenData, $providerName, [
                    'usage_type' => 'chat',
                    'tenant_id' => $effectiveTenant?->id ?? 1,
                    'description' => 'AI Chat: ' . substr($prompt, 0, 50) . '...',
                    'source' => 'ai_service_ask'
                ]);
            } else {
                // Legacy limit sistemi kaldırıldı - sadece log
                Log::info('AI yanıt başarılı (legacy mode)', [
                    'response_length' => strlen($response),
                    'tenant' => 'none'
                ]);
            }
        }

        return $response;
    }

    /**
     * AI Feature ile akıllı soru sor - YENİ MARKA-AWARE SİSTEM
     *
     * @param \Modules\AI\App\Models\AIFeature $feature
     * @param string $userInput
     * @param array $options
     * @return string|null
     */
    public function askFeature($feature, string $userInput, array $options = [])
    {
        $startTime = microtime(true);
        $tenantId = tenant('id') ?? 'default';
        
        // Feature string ise model olarak yükle
        if (is_string($feature)) {
            $featureSlug = $feature;
            $feature = \Modules\AI\App\Models\AIFeature::where('slug', $featureSlug)->first();
            if (!$feature) {
                return "Feature bulunamadı: {$featureSlug}";
            }
        }
        
        
        // Model bazlı kredi kontrolü (YENİ SISTEM)
        $tenant = tenant();
        if ($tenant) {
            // Model seçimi
            $currentModel = $effectiveTenant?->default_ai_model ?? $this->currentProvider->default_model;
            
            // Token tahmini
            $estimatedInputTokens = strlen($userInput) / 4;
            $estimatedOutputTokens = 1000; // Feature'lar için ortalama output
            
            // Model bazlı kredi hesaplama
            $requiredCredits = ai_calculate_model_credits(
                $this->currentProvider->id,
                $currentModel,
                $estimatedInputTokens,
                $estimatedOutputTokens
            );
            
            // Kredi kontrolü
            if ($requiredCredits && $tenant->credits < $requiredCredits) {
                return "Üzgünüm, yetersiz krediniz var. Gerekli: {$requiredCredits}, Mevcut: {$tenant->credits}";
            }
        }

        // Yeni template sistemi: Quick + Expert + Response Template
        if ($feature->hasQuickPrompt() || $feature->hasResponseTemplate()) {
            $options['user_input'] = $userInput; // Uzunluk algılama için features
            $systemPrompt = $this->buildFeatureSystemPrompt($feature, $options);
            
            // Debug: Prompt analizi için hangi prompt'ların kullanıldığını kaydet
            $promptAnalysis = $this->analyzeUsedPrompts($feature, $options);
        } else {
            // Legacy sistem: Basit custom prompt
            $systemPrompt = $feature->custom_prompt ?: "Sen yardımcı bir AI asistanısın.";
            $promptAnalysis = [
                ['prompt_name' => 'Custom Prompt', 'prompt_type' => 'legacy', 'priority' => 3]
            ];
        }

        // Mesajları formatla
        $messages = [];
        
        if ($systemPrompt) {
            $messages[] = [
                'role' => 'system',
                'content' => $systemPrompt
            ];
        }
        
        $messages[] = [
            'role' => 'user',
            'content' => $userInput
        ];

        // AI'dan yanıt al - Dinamik provider kullanımı
        $featureStartTime = microtime(true);
        $apiResponse = $this->currentService->ask($messages, false);
        
        // Provider performansını güncelle
        if ($this->currentProvider) {
            $responseTime = (microtime(true) - $featureStartTime) * 1000;
            $this->providerManager->updateProviderPerformance($this->currentProvider->name, $responseTime);
        }
        
        // API response'u parse et (string veya array olabilir)
        $response = is_array($apiResponse) ? ($apiResponse['response'] ?? $apiResponse) : $apiResponse;
        
        // Debug log kaldırıldı - AI çalışmasını etkiliyordu
        
        if ($response) {
            // Feature kullanım istatistiklerini güncelle
            $feature->incrementUsage();
            
            // YENİ MERKEZİ KREDİ DÜŞME SİSTEMİ - FEATURE (TENANT OLMADAN DA ÇALIŞIR)
            // Tenant yoksa da kredi düşümü yapalım (admin mode için)
            $effectiveTenant = $tenant;
            if (!$effectiveTenant && auth()->check()) {
                // Auth user'ın tenant'ını kullan
                $user = auth()->user();
                if ($user && $user->tenant_id) {
                    $effectiveTenant = \App\Models\Tenant::find($user->tenant_id);
                }
            }
            
            if ($effectiveTenant || !tenant('id')) { // Tenant var VEYA central admin mode
                Log::info('🔧 Kredi düşürme bloku çalışıyor', [
                    'effective_tenant' => $effectiveTenant ? $effectiveTenant->id : null,
                    'original_tenant' => $tenant ? $effectiveTenant?->id ?? 1 : null,
                    'tenant_function' => tenant('id'),
                    'auth_check' => auth()->check()
                ]);
                Log::info('🔧 Kredi düşürme bloku çalışıyor', [
                    'effective_tenant' => $effectiveTenant ? $effectiveTenant->id : null,
                    'original_tenant' => $tenant ? $effectiveTenant?->id ?? 1 : null,
                    'tenant_function' => tenant('id'),
                    'auth_check' => auth()->check()
                ]);
                // API response'undan token bilgilerini al
                $tokenData = is_array($apiResponse) ? $apiResponse : [];
                
                // Provider ve model bilgileri
                $providerName = $this->currentProvider ? $this->currentProvider->name : 'unknown';
                $providerID = $this->currentProvider ? $this->currentProvider->id : 1;
                $currentModel = $effectiveTenant?->default_ai_model ?? $this->currentProvider->default_model ?? 'unknown';
                
                // Token bilgilerini parse et
                $inputTokens = $tokenData['input_tokens'] ?? $tokenData['usage']['prompt_tokens'] ?? 0;
                $outputTokens = $tokenData['output_tokens'] ?? $tokenData['usage']['completion_tokens'] ?? 0;
                $totalTokens = $tokenData['total_tokens'] ?? $tokenData['usage']['total_tokens'] ?? ($inputTokens + $outputTokens);
                
                // Eğer token bilgisi yoksa tahmini hesapla
                if ($totalTokens == 0) {
                    $inputTokens = (int) ceil(strlen($userInput) / 4);
                    $outputTokens = (int) ceil(strlen($response) / 4);
                    $totalTokens = $inputTokens + $outputTokens;
                }
                
                // Model bazlı kredi kullanım sistemi
                $creditService = app(ModelBasedCreditService::class);
                $usedCredits = $creditService->deductCredits(
                    $effectiveTenant ?: (object)["id" => 1, "ai_credits" => 999999],
                    $providerID,
                    $currentModel,
                    $inputTokens,
                    $outputTokens,
                    'ai_feature',
                    $feature->id
                );
                
                Log::info('🎯 AI Feature kredi düşümü', [
                    'tenant_id' => $effectiveTenant?->id ?? 1,
                    'feature_slug' => $feature->slug,
                    'provider' => $providerName,
                    'model' => $currentModel,
                    'input_tokens' => $inputTokens,
                    'output_tokens' => $outputTokens,
                    'total_tokens' => $totalTokens,
                    'credits_used' => $usedCredits,
                    'source' => 'ai_service_ask_feature'
                ]);
            }
            
            // Conversation tracking
            $this->createConversationRecord($userInput, $response, 'feature_test', [
                'feature_id' => $feature->id,
                'feature_name' => $feature->name,
                'source' => 'feature_test'
            ]);
            
            // Debug Dashboard Logging
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            $this->logDebugInfo([
                'tenant_id' => $tenantId,
                'feature_slug' => $feature->slug ?? 'unknown',
                'request_type' => 'feature_request',
                'context_type' => $options['context_type'] ?? 'normal',
                'input_preview' => substr($userInput, 0, 200),
                'execution_time_ms' => $executionTime,
                'has_error' => false,
                'error_message' => null,
                'actually_used_prompts' => count($promptAnalysis ?? []),
                'total_available_prompts' => count($promptAnalysis ?? []),
                'prompts_analysis' => $promptAnalysis ?? [],
                'response_preview' => substr($response, 0, 300),
                'tokens_estimated' => $actualTokens ?? 0
            ]);
        }

        return $response;
    }

    /**
     * Process AI Feature - Helper function support
     * 
     * @param \Modules\AI\App\Models\AIFeature $feature
     * @param string $input
     * @param array $options
     * @return array
     */
    public function processFeature($feature, string $input, array $options = []): array
    {
        try {
            // Use askFeature method which already handles everything
            $response = $this->askFeature($feature, $input, $options);
            
            return [
                'success' => true,
                'response' => $response,
                'feature_id' => $feature->id,
                'feature_name' => $feature->name
            ];
        } catch (\Exception $e) {
            Log::error('ProcessFeature Error', [
                'feature_id' => $feature->id,
                'feature_slug' => $feature->slug,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'response' => 'AI işlemi başarısız: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Feature için sistem promptu oluştur - YENİ PRIORITY ENGINE
     */
    private function buildFeatureSystemPrompt($feature, array $options = []): string
    {
        // Feature bilgilerini options'a ekle
        $options['feature'] = $feature;
        $options['feature_name'] = $feature->slug ?? $feature->name ?? '';
        
        // AIPriorityEngine ile complete prompt oluştur
        return AIPriorityEngine::buildCompletePrompt($options);
    }

    /**
     * Kullanılan prompt'ları analiz et
     */
    private function analyzeUsedPrompts($feature, array $options = []): array
    {
        try {
            // Feature için options'ı hazırla
            $options['feature'] = $feature;
            $options['feature_name'] = $feature->slug ?? $feature->name ?? '';
            
            // AIPriorityEngine'den tüm bileşenleri al
            $components = [];
            
            // Standard components
            $components = array_merge($components, AIPriorityEngine::getStandardComponents());
            
            // Brand components
            $components = array_merge($components, AIPriorityEngine::getBrandComponents($options));
            
            // Feature components
            $components = array_merge($components, AIPriorityEngine::getFeatureComponents($feature));
            
            // Component'leri score'la
            $scoredComponents = [];
            foreach ($components as $component) {
                $category = $component['category'] ?? 'conditional_info';
                $priority = $component['priority'] ?? 3;
                $baseWeight = AIPriorityEngine::BASE_WEIGHTS[$category] ?? 1000;
                $multiplier = AIPriorityEngine::PRIORITY_MULTIPLIERS[$priority] ?? 1.0;
                $finalScore = intval($baseWeight * $multiplier);
                
                $scoredComponents[] = [
                    'prompt_name' => $component['name'] ?? 'Unknown',
                    'prompt_type' => $category,
                    'priority' => $priority,
                    'score' => $finalScore,
                    'base_weight' => $baseWeight,
                    'multiplier' => $multiplier
                ];
            }
            
            return $scoredComponents;
        } catch (\Exception $e) {
            // Fallback: basit analiz
            return [
                [
                    'prompt_name' => $feature->name ?? 'Feature',
                    'prompt_type' => 'feature',
                    'priority' => 2,
                    'score' => 100,
                    'base_weight' => 8000,
                    'multiplier' => 1.2
                ]
            ];
        }
    }

    /**
     * Debug dashboard için log bilgisi kaydet
     */
    private function logDebugInfo(array $data): void
    {
        try {
            // Migration schema ile uyumlu data hazırla
            $insertData = [
                'tenant_id' => $data['tenant_id'] ?? 'default',
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'feature_slug' => $data['feature_slug'] ?? 'unknown',
                'request_type' => $data['request_type'] ?? 'feature_request',
                'context_type' => $data['context_type'] ?? 'normal',
                'threshold_used' => 4000, // Default normal threshold
                'total_available_prompts' => $data['total_available_prompts'] ?? 0,
                'actually_used_prompts' => $data['actually_used_prompts'] ?? 0,
                'filtered_prompts' => max(0, ($data['total_available_prompts'] ?? 0) - ($data['actually_used_prompts'] ?? 0)),
                'highest_score' => 0, // Will be calculated from prompts_analysis
                'lowest_used_score' => 0, // Will be calculated from prompts_analysis
                'execution_time_ms' => intval($data['execution_time_ms'] ?? 0),
                'response_length' => isset($data['response_preview']) ? strlen($data['response_preview']) : null,
                'token_usage' => $data['tokens_estimated'] ?? 0,
                'input_preview' => $this->sanitizeForMySQL($data['input_preview'] ?? null),
                'response_preview' => $this->sanitizeForMySQL($data['response_preview'] ?? null),
                'prompts_analysis' => json_encode($data['prompts_analysis'] ?? []),
                'scoring_summary' => json_encode($this->calculateScoringSummary($data['prompts_analysis'] ?? [])),
                'ai_model' => $this->currentProvider ? ($this->currentProvider->name . '/' . $this->currentProvider->default_model) : 'unknown',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'has_error' => $data['has_error'] ?? false,
                'error_message' => $data['error_message'],
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Score'ları hesapla
            if (!empty($data['prompts_analysis'])) {
                $scores = array_column($data['prompts_analysis'], 'score');
                $insertData['highest_score'] = !empty($scores) ? max($scores) : 0;
                $insertData['lowest_used_score'] = !empty($scores) ? min($scores) : 0;
            }
            
            // ai_tenant_debug_logs tablosuna kaydet
            \DB::table('ai_tenant_debug_logs')->insert($insertData);
        } catch (\Exception $e) {
            // Debug logging hatası varsa log'a yaz ama işlemi durdurmma
            Log::warning('Debug logging failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }
    
    /**
     * MySQL charset problemi için text sanitize et
     */
    private function sanitizeForMySQL(?string $text): ?string
    {
        if (!$text) {
            return null;
        }
        
        // Limit text length to prevent huge debug logs
        $text = substr($text, 0, 2000);
        
        // Replace problematic UTF-8 characters for MySQL
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        
        // Remove any remaining problematic characters
        $text = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $text);
        
        return $text;
    }
    
    /**
     * Scoring summary hesapla
     */
    private function calculateScoringSummary(array $promptsAnalysis): array
    {
        if (empty($promptsAnalysis)) {
            return ['highest_score' => 0, 'lowest_used_score' => 0, 'average_score' => 0, 'total_content_length' => 0];
        }
        
        $scores = array_column($promptsAnalysis, 'score');
        return [
            'highest_score' => !empty($scores) ? max($scores) : 0,
            'lowest_used_score' => !empty($scores) ? min($scores) : 0,
            'average_score' => !empty($scores) ? round(array_sum($scores) / count($scores)) : 0,
            'total_content_length' => 0 // Content length calculation if needed
        ];
    }

    /**
     * Conversation kaydı oluştur
     */
    public function createConversationRecord(string $userMessage, string $aiResponse, string $type = 'chat', array $metadata = [])
    {
        try {
            $tenant = tenant();
            if (!$tenant) {
                return;
            }

            // Conversation oluştur
            $conversation = \Modules\AI\App\Models\Conversation::create([
                'tenant_id' => $effectiveTenant?->id ?? 1,
                'user_id' => auth()->id(),
                'title' => 'AI ' . ucfirst($type) . ': ' . substr($userMessage, 0, 50) . '...',
                'type' => $type,
                'metadata' => $metadata
            ]);

            // User message
            \Modules\AI\App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'content' => $userMessage,
                'role' => 'user',
                'token_count' => strlen($userMessage) / 4 // Tahmini
            ]);

            // AI response - model bilgisiyle
            \Modules\AI\App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'content' => $aiResponse,
                'role' => 'assistant',
                'token_count' => strlen($aiResponse) / 4, // Tahmini
                'model_used' => $this->getCurrentProviderModel() // Model bilgisini ekle
            ]);

        } catch (\Exception $e) {
            Log::warning('Conversation kaydı oluşturulamadı: ' . $e->getMessage());
        }
    }

    /**
     * Ayarları getir
     *
     * @return Setting|null
     */
    public function getSettings(): ?Setting
    {
        $cacheKey = "ai_settings";
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            // Config tabanlı ayarlar döndür
            return (object) [
                'enabled' => config('ai.enabled', true),
                'debug' => config('ai.debug', false),
                'cache_duration' => config('ai.cache_duration', 60),
                'default_language' => config('ai.integrations.page.supported_languages.0', 'tr'),
                'response_format' => 'markdown',
                'rate_limiting' => config('ai.security.enable_rate_limiting', true),
                'content_filtering' => config('ai.security.enable_content_filter', true),
            ];
        });
    }

    /**
     * Ayarları güncelle
     *
     * @param array $data
     * @return Setting|null
     */
    public function updateSettings(array $data): ?Setting
    {
        // Config tabanlı ayarlar
        $settings = (object) [
            'enabled' => config('ai.enabled', true),
            'debug' => config('ai.debug', false),
            'cache_duration' => config('ai.cache_duration', 60),
            'default_language' => config('ai.integrations.page.supported_languages.0', 'tr'),
            'response_format' => 'markdown',
            'rate_limiting' => config('ai.security.enable_rate_limiting', true),
            'content_filtering' => config('ai.security.enable_content_filter', true),
        ];
        
        if (!$settings) {
            $settings = new Setting();
        }
        
        // API anahtarı sadece data'da varsa ve dolu ise güncelle
        if (isset($data['api_key']) && !empty($data['api_key'])) {
            $settings->api_key = $data['api_key'];
        }
        
        if (isset($data['model'])) {
            $settings->model = $data['model'];
        }
        
        if (isset($data['max_tokens'])) {
            $settings->max_tokens = $data['max_tokens'];
        }
        
        if (isset($data['temperature'])) {
            $settings->temperature = $data['temperature'];
        }
        
        if (isset($data['enabled'])) {
            $settings->enabled = $data['enabled'];
        }
        
        $settings->save();
        
        // AI ayar güncelleme log'u
        if (function_exists('log_activity')) {
            log_activity($settings, 'güncellendi');
        }
        
        // Önbelleği temizle
        Cache::forget("ai_settings");
        
        return $settings;
    }

    /**
     * ConversationService getter
     */
    public function conversations()
    {
        return $this->conversationService;
    }

    /**
     * Token Service getter (limit service yerine)
     */
    public function limits()
    {
        return $this->aiTokenService;
    }

    /**
     * PromptService getter
     */
    public function prompts()
    {
        return $this->promptService;
    }
    
    /**
     * 🧠 YENİ CONTEXT ENGINE SİSTEMİ: Modern context management ile sistem promptu
     *
     * @param string $userPrompt Kullanıcının seçtiği prompt
     * @param array $options Context options
     * @return string Tam sistem promptu
     */
    public function buildFullSystemPrompt($userPrompt = '', array $options = [])
    {
        $parts = [];
        
        // MODE TESPİTİ (İlk sırada - kurallar için gerekli)
        $mode = $options['mode'] ?? $this->detectMode($options);
        
        // 1. MODE-AWARE UZUNLUK ve YAPI KURALLARI
        if ($mode === 'chat') {
            // CHAT MODU: Samimi, kısa, doğal yanıtlar
            $parts[] = "🗣️ CHAT MODU: Samimi, dostça ve doğal konuş. Sohbet tarzında yanıt ver.";
            $parts[] = "📝 HTML KULLANIMI: HTML tagları kullanabilirsin ama işlenmiş çıktı olarak ver.";
            $parts[] = "⚠️ ÖNEMLİ: HTML kodlarını HAM METIN olarak verme! Örnek: '<p>metin</p>' değil, doğrudan 'metin' ver.";
            $parts[] = "📏 UZUNLUK: Soruya göre esnek - kısa sorular için kısa yanıt, detay gereken konular için daha uzun.";
            $parts[] = "🎯 ÖNEMLI: Blog yazısı değil, doğal sohbet yap. Samimi ve arkadaşça ol.";
        } else {
            // FEATURE/BLOG MODU: Profesyonel, detaylı içerik
            if (isset($options['user_input'])) {
                $length = $this->detectLengthRequirement($options['user_input']);
                $parts[] = "⚠️ ZORUNLU UZUNLUK: Bu yanıt MİNİMUM {$length['min']} kelime, MAKSİMUM {$length['max']} kelime olmalıdır.";
            }
            $parts[] = "🚨 ZORUNLU PARAGRAF KURALI: İçerik MİNİMUM 4 paragraf olmalı! Tek paragraf yazma! Her paragraf 3-6 cümle. Paragraflar arasında boş satır bırak.";
            $parts[] = "⚠️ HTML YASAK: Hiçbir HTML kodu kullanma! Sadece düz metin olarak yaz. Örnek: '<p>metin</p>' değil, sadece 'metin' yaz.";
        }
        
        // 2. DİNAMİK DİL KURALI - Tenant'ın varsayılan dilini kullan
        $defaultLanguage = $this->getTenantDefaultLanguage();
        $parts[] = "🌐 DİL KURALI: Yanıtı '{$defaultLanguage['name']}' ({$defaultLanguage['code']}) dilinde ver. Çeviri istenmediği sürece bu dili kullan.";
        
        // 3. VERİTABANI PROMPT KURALLARI - Hidden System + Common
        $databasePrompts = $this->getSystemPrompts($mode);
        if (!empty($databasePrompts)) {
            $parts[] = "📋 SİSTEM KURALLARI:";
            foreach ($databasePrompts as $prompt) {
                $parts[] = "• " . $prompt['name'] . ": " . $prompt['content'];
            }
        } else {
            // SİSTEM KURALLARI YÜKLENEMEZ İSE AI ÇALIŞMAZ
            throw new \Exception('AI sistem kuralları yüklenemedi. Lütfen sistem yöneticisine başvurun.');
        }
        
        // 3. CHAT vs FEATURE MODU CONTEXT AYRIMI
        if ($mode === 'chat') {
            // CHAT MODU: KULLANICI BİLGİSİ ve AI KİMLİK AYIRIMI  
            if ($user = auth()->user()) {
                // ÖNCELİK: AI kimlik tanımı (Tenant'tan alınacak)
                if ($tenant = tenant()) {
                    $profile = \Modules\AI\App\Models\AITenantProfile::where('tenant_id', $effectiveTenant?->id ?? 1)->first();
                    if ($profile && $profile->company_info && isset($profile->company_info['brand_name'])) {
                        $parts[] = "🤖 SEN KİMSİN: Sen {$profile->company_info['brand_name']} şirketinin yapay zeka modelisin.";
                        
                        // Kurucu bilgisi varsa ekle (ama sen o değilsin!)
                        if (isset($profile->company_info['founder'])) {
                            $parts[] = "👨‍💼 ŞİRKET KURUCUSU: {$profile->company_info['founder']} (ama sen o değilsin, sen AI modelisin!)";
                        }
                        
                        // Debug log
                        Log::info('🤖 AI Identity Context Created', [
                            'brand_name' => $profile->company_info['brand_name'],
                            'founder_exists' => isset($profile->company_info['founder']),
                            'founder' => $profile->company_info['founder'] ?? 'YOK'
                        ]);
                    }
                }
                
                $userInfo = "Konuştuğun kişi: {$user->name}";
                if ($user->email) {
                    $userInfo .= " (Email: {$user->email})";
                }
                
                // Role/Yetki bilgilerini ekle
                try {
                    $userRoles = $user->getRoleNames()->toArray();
                    if (!empty($userRoles)) {
                        $roleText = implode(', ', $userRoles);
                        $userInfo .= " (Rol: {$roleText})";
                        
                        // Admin/yönetici kontrolü
                        if (in_array('admin', $userRoles) || in_array('administrator', $userRoles) || $user->hasRole('admin')) {
                            $userInfo .= " - Bu kullanıcı SİSTEM YÖNETİCİSİ'dir";
                        } elseif (in_array('editor', $userRoles) || $user->hasRole('editor')) {
                            $userInfo .= " - Bu kullanıcı EDİTÖR yetkisine sahip";
                        } elseif (in_array('moderator', $userRoles) || $user->hasRole('moderator')) {
                            $userInfo .= " - Bu kullanıcı MODERATÖR yetkisine sahip";
                        }
                    }
                } catch (\Exception $e) {
                    // Role kontrolü başarısız olursa sessizce devam et
                    Log::warning('AI Chat: Role bilgisi alınamadı', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                }
                
                // Üyelik tarihi ve süresi bilgilerini ekle
                try {
                    if ($user->created_at) {
                        $memberSince = $user->created_at;
                        $daysSinceMember = $memberSince->diffInDays(now());
                        $memberDate = $memberSince->format('d.m.Y');
                        
                        $userInfo .= " (Üyelik: {$memberDate} tarihinden beri - {$daysSinceMember} gündür üye)";
                        
                        // Üyelik süresine göre özel notlar
                        if ($daysSinceMember < 7) {
                            $userInfo .= " - YENİ ÜYE";
                        } elseif ($daysSinceMember < 30) {
                            $userInfo .= " - GENÇ ÜYE";
                        } elseif ($daysSinceMember < 365) {
                            $userInfo .= " - DENEYİMLİ ÜYE";
                        } else {
                            $userInfo .= " - ESKİ ÜYE (Veteran)";
                        }
                        
                        // Özel tarihlerde kutlama
                        if ($daysSinceMember > 0 && $daysSinceMember % 365 == 0) {
                            $years = intval($daysSinceMember / 365);
                            $userInfo .= " 🎉 {$years}. yıl kutlu olsun!";
                        }
                    }
                    
                    // Son aktivite bilgisi (eğer varsa)
                    if (isset($user->last_login_at) && $user->last_login_at) {
                        try {
                            // String'i Carbon'a çevir (eğer string ise)
                            $lastLogin = is_string($user->last_login_at) 
                                ? \Carbon\Carbon::parse($user->last_login_at) 
                                : $user->last_login_at;
                                
                            $hoursAgo = $lastLogin->diffInHours(now());
                            
                            if ($hoursAgo < 1) {
                                $userInfo .= " (Son giriş: Bu saatte)";
                            } elseif ($hoursAgo < 24) {
                                $userInfo .= " (Son giriş: {$hoursAgo} saat önce)";
                            } else {
                                $daysAgo = intval($hoursAgo / 24);
                                $userInfo .= " (Son giriş: {$daysAgo} gün önce)";
                            }
                        } catch (\Exception $loginErr) {
                            // Son giriş tarihi parse edilemezse sessizce atla
                            Log::warning('AI Chat: Son giriş tarihi parse edilemedi', [
                                'user_id' => $user->id, 
                                'last_login_at' => $user->last_login_at,
                                'error' => $loginErr->getMessage()
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // Tarih bilgisi hatası varsa sessizce devam et
                    Log::warning('AI Chat: Tarih bilgisi alınamadı', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                }
                
                if (isset($user->company) && $user->company) {
                    $userInfo .= " (Şirket: {$user->company})";
                }
                $userInfo .= ". Kişisel, samimi ve dostça ol.";
                $parts[] = "👤 CHAT KULLANICISI: " . $userInfo;
                $parts[] = "🚫 KRİTİK SORU ANALİZ SİSTEMİ:";
                $parts[] = "🙋 'BEN KİMİM?' sorusu → Kullanıcı hakkında bilgi ver: {$user->name}";
                $parts[] = "🤖 'SEN KİMSİN?' sorusu → KENDİN HAKKINDA: Sen yapay zeka modelisin, kullanıcı değil!";
                $parts[] = "🏢 'BİZ KİMİZ?' sorusu → Şirket/marka bilgilerini kullan";
                $parts[] = "⚠️ KRİTİK: 'Sen kimsin' = AI kimliği, 'Ben kimim' = Kullanıcı kimliği!";
                $parts[] = "🔑 YETKİ BİLGİSİ: Kullanıcının rol ve yetki durumunu da belirt (admin/editor/user vs.)";
                
                // Şirket bilgilerini her zaman hazır tut (dinamik kullanım için)
                try {
                    $brandContext = $this->getTenantBrandContext();
                    if ($brandContext) {
                        $parts[] = "🏢 ŞİRKET/MARKA BİLGİLERİ (şirket odaklı sorularda kullan):";
                        $parts[] = $brandContext;
                        $parts[] = "💡 KULLANIM: Soruyu analiz et ve uygun context'i seç - kullanıcı mı şirket mi soruyor?";
                    }
                } catch (\Exception $e) {
                    // Şirket bilgisi alınamazsa sessizce devam et
                    Log::warning('AI Chat: Şirket bilgisi alınamadı', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                }
            }
        } else {
            // FEATURE MODU: Context Engine kullan
            try {
                // ContextEngine'den mode'a uygun context oluştur
                $contextPrompt = $this->contextEngine->buildContextForMode($mode, $options);
                if (!empty($contextPrompt)) {
                    $parts[] = $contextPrompt;
                }
            } catch (\Exception $e) {
                Log::warning('ContextEngine hatası, fallback sisteme geçiliyor', [
                    'error' => $e->getMessage(),
                    'mode' => $mode,
                    'trace' => $e->getTraceAsString()
                ]);
                
                // FALLBACK: Eski sistem
                $brandContext = $this->getTenantBrandContext();
                if ($brandContext) {
                    $parts[] = "🏢 FEATURE MODU: Aşağıdaki şirket için çalış.\n" . $brandContext;
                }
            }
        }
        
        // 5. PRI͏ORITY ENGINE İLE ESKI SİSTEM ENTEGRASYONU
        if (!empty($userPrompt)) {
            $customComponents = [[
                'category' => 'feature_definition',
                'priority' => 1,
                'content' => $userPrompt,
                'name' => 'User Custom Prompt'
            ]];
            $options['custom_components'] = $customComponents;
            
            // Priority engine'den gelen prompt'u ekle
            $priorityPrompt = AIPriorityEngine::buildCompletePrompt($options);
            if ($priorityPrompt && trim($priorityPrompt) !== trim($userPrompt)) {
                $parts[] = $priorityPrompt;
            } else {
                $parts[] = $userPrompt;
            }
        }
        
        // 6. SON UYARI (Sadece feature modunda)
        if ($mode !== 'chat') {
            $parts[] = "📝 SON UYARI: UZUNLUK ve PARAGRAF kurallarına kesinlikle uy. 'Kısa yanıt' vermek yasak!";
            $parts[] = "🔥 ÖRNEK PARAGRAF YAPISI:";
            $parts[] = "Paragraf 1: Konuya giriş (3-6 cümle)";
            $parts[] = "";
            $parts[] = "Paragraf 2: Detaylar (3-6 cümle)"; 
            $parts[] = "";
            $parts[] = "Paragraf 3: Örnekler (3-6 cümle)";
            $parts[] = "";
            $parts[] = "Paragraf 4: Sonuç (3-6 cümle)";
            $parts[] = "🚨 UNUTMA: Her paragraf arasında BOŞ SATIR bırak!";
        }
        
        return implode("\n\n", $parts);
    }
    
    /**
     * Veritabanından sistem prompt'larını getir
     * 
     * @param string $mode
     * @return array
     */
    private function getSystemPrompts(string $mode = 'chat'): array
    {
        try {
            // Veritabanından prompt'ları çek - language ve tenant_id kolonu yok, basit sorgu
            $prompts = \DB::table('ai_prompts')
                ->where('is_active', true)
                ->whereIn('prompt_type', ['hidden_system', 'common'])
                ->orderBy('priority', 'asc')
                ->orderBy('ai_weight', 'desc')
                ->get();
            
            // Array'e dönüştür
            $result = [];
            foreach ($prompts as $prompt) {
                $result[] = [
                    'name' => $prompt->name,
                    'content' => $prompt->content,
                    'type' => $prompt->prompt_type,
                    'category' => $prompt->prompt_category,
                    'priority' => $prompt->priority,
                    'weight' => $prompt->ai_weight
                ];
            }
            
            Log::info('🔥 Database prompts loaded successfully', [
                'mode' => $mode,
                'prompts_count' => count($result),
                'prompt_names' => array_column($result, 'name')
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('❌ Database prompts loading failed', [
                'error' => $e->getMessage(),
                'mode' => $mode,
                'trace' => $e->getTraceAsString()
            ]);
            
            // FALLBACK YOK - Exception fırlat
            throw new \Exception('AI sistem kuralları veritabanından yüklenemedi: ' . $e->getMessage());
        }
    }
    
    /**
     * 🔍 UZUNLUK ALGıLAMA MOTORü
     * Kullanıcı girdisinden istenen uzunluğu akıllıca tespit eder
     */
    private function detectLengthRequirement($prompt): array
    {
        $prompt_lower = mb_strtolower($prompt);
        
        // 1. Sayısal değer var mı? (en kesin)
        if (preg_match('/(\d+)\s*(kelime|word)/i', $prompt, $matches)) {
            $target = (int)$matches[1];
            return ['min' => (int)($target * 0.8), 'max' => (int)($target * 1.2)];
        }
        
        // 2. Anahtar kelime bazlı algılama
        $keywords = [
            // ÖZEL DURUMLAR (İlk kontrol edilir)
            'çok uzun' => ['min' => 1500, 'max' => 2500],
            'çok kısa' => ['min' => 100, 'max' => 200],
            
            // UZUNLUK KELİMELERİ
            'uzun' => ['min' => 1000, 'max' => 1500],  // KRİTİK: "uzun" için 1000+ kelime
            'kısa' => ['min' => 200, 'max' => 400],
            'normal' => ['min' => 400, 'max' => 600],
            'detaylı' => ['min' => 800, 'max' => 1200],
            'kapsamlı' => ['min' => 1000, 'max' => 1500],
            'geniş' => ['min' => 800, 'max' => 1200],
            
            // İÇERİK TİPLERİ
            'makale' => ['min' => 800, 'max' => 1200],
            'blog' => ['min' => 600, 'max' => 1000],
            'özet' => ['min' => 200, 'max' => 400],
            
            // KISA İÇERİKLER (Son kontrol edilir)
            'tweet' => ['min' => 20, 'max' => 50],
            
            // BAŞLIK KELİMESİ KALDIRILDI - Yanıltıcı!
            // 'başlık' => ['min' => 5, 'max' => 15], // KALDIRILDI
        ];
        
        // Kelime araması
        foreach ($keywords as $keyword => $range) {
            if (str_contains($prompt_lower, $keyword)) {
                return $range;
            }
        }
        
        // 3. Context bazlı tahmin
        if (str_contains($prompt_lower, 'yaz') || str_contains($prompt_lower, 'oluştur')) {
            return ['min' => 600, 'max' => 800]; // Yazma talepleri için orta uzunluk
        }
        
        // 4. Default (konservatif)
        return ['min' => 400, 'max' => 600];
    }
    
    /**
     * 🎯 RESPONSE QUALITY KONTROL VE YAPIYI ZORLAMA
     * AI provider yanıtını kalite kontrolünden geçirir ve düzenler
     */
    private function enforceStructure($content, $requirements = []): string
    {
        // 🔍 1. İLK KALİTE KONTROL
        if (empty($content) || !is_string($content)) {
            Log::warning('🚨 AI Response Quality Issue: Empty or invalid content', [
                'content_type' => gettype($content),
                'content_length' => is_string($content) ? strlen($content) : 0
            ]);
            return 'AI yanıtı alınamadı. Lütfen tekrar deneyiniz.';
        }
        
        // 🧹 2. HTML TAG TEMİZLEME
        $originalContent = $content;
        $content = $this->cleanHtmlTags($content);
        
        if ($originalContent !== $content) {
            Log::info('🧹 HTML Tags cleaned from AI response', [
                'original_length' => strlen($originalContent),
                'cleaned_length' => strlen($content)
            ]);
        }
        
        // 🚫 3. YASAK KELİME KONTROL
        $content = $this->removeProhibitedPhrases($content);
        
        // 🔍 4. Mode tespiti
        $mode = $requirements['mode'] ?? $this->detectMode($requirements);
        $isChatMode = ($mode === 'chat');
        
        // Chat modunda sadece çok uzun tek paragrafları böl
        if ($isChatMode) {
            $content = trim($content);
            
            // Zaten paragrafları varsa dokunma - düz metin olarak döndür
            $existingParagraphs = preg_split('/\n\s*\n/', $content);
            if (count($existingParagraphs) >= 2) {
                // Zaten paragraflanmış - düz metin olarak döndür
                return $content;
            }
            
            // Tek paragraf ve çok uzunsa böl (500+ karakter)
            if (strlen($content) > 500) {
                $sentences = preg_split('/(?<=[.!?])\s+/', $content);
                $sentences = array_filter(array_map('trim', $sentences));
                
                if (count($sentences) >= 3) {
                    // Cümleleri 2-3 paragrafa böl
                    $perParagraph = ceil(count($sentences) / 2);
                    $paragraphs = [];
                    
                    for ($i = 0; $i < 2; $i++) {
                        $start = $i * $perParagraph;
                        $chunk = array_slice($sentences, $start, $perParagraph);
                        if (!empty($chunk)) {
                            $paragraphs[] = implode(' ', $chunk);
                        }
                    }
                    
                    // Düz metin formatında döndür
                    return implode("\n\n", $paragraphs);
                }
            }
            
            // Kısa metinler için düz metin olarak döndür
            return $content;
        }
        
        // İçeriği temizle
        $content = trim($content);
        
        // Paragrafları ayır
        $paragraphs = preg_split('/\n\s*\n/', $content);
        $paragraphs = array_filter(array_map('trim', $paragraphs));
        
        // Eğer tek paragraf ise, cümlelere böl ve 4 parça yap
        if (count($paragraphs) < 4) {
            // Cümleleri ayır
            $sentences = preg_split('/(?<=[.!?])\s+/', $content);
            $sentences = array_filter(array_map('trim', $sentences));
            
            if (count($sentences) >= 4) {
                // Cümleleri 4 paragrafa böl
                $perParagraph = ceil(count($sentences) / 4);
                $newParagraphs = [];
                
                for ($i = 0; $i < 4; $i++) {
                    $start = $i * $perParagraph;
                    $chunk = array_slice($sentences, $start, $perParagraph);
                    if (!empty($chunk)) {
                        $newParagraphs[] = implode(' ', $chunk);
                    }
                }
                
                $paragraphs = $newParagraphs;
            }
        }
        
        // Başlık ekle (user input'tan çıkar)
        $title = '';
        if (isset($requirements['user_input'])) {
            $userInput = $requirements['user_input'];
            // "hakkında", "için", "ile ilgili" gibi ifadeleri temizle
            $cleanTitle = preg_replace('/(hakkında|için|ile ilgili|konusunda)\s+(uzun\s*)?(yazı|makale|blog|içerik)\s*(yaz|oluştur|hazırla)/i', '', $userInput);
            $cleanTitle = preg_replace('/\s+(uzun\s*)?(yazı|makale|blog|içerik)\s*(yaz|oluştur|hazırla)/i', '', $cleanTitle);
            $title = trim(ucfirst($cleanTitle));
            
            if (empty($title)) {
                $title = 'Konu Başlığı';
            }
        }
        
        // Sonucu düz metin formatında birleştir (Frontend HTML'e çevirecek)
        $result = '';
        if ($title) {
            $result .= "{$title}\n\n";
        }
        
        // Paragrafları newline'lar ile birleştir 
        $result .= implode("\n\n", $paragraphs);
        
        return trim($result);
    }
    
    /**
     * 🔄 CHAT vs FEATURE MODU TESPİTİ
     * Context'ten modu otomatik tespit eder
     */
    private function detectMode(array $options = []): string
    {
        // Explicit mode varsa onu kullan
        if (isset($options['mode'])) {
            return $options['mode'];
        }
        
        // Feature objesi varsa feature modu
        if (isset($options['feature']) || isset($options['feature_name'])) {
            return 'feature';
        }
        
        // 🎯 YENİ: USER INPUT'A GÖRE FEATURE MODU TESPİTİ
        // skip_mode_override parametresi varsa mode override'ı atla
        if (!isset($options['skip_mode_override']) || !$options['skip_mode_override']) {
            // Eğer kullanıcı uzun içerik istiyorsa, chat panelinde bile feature modu çalışsın
            if (isset($options['user_input'])) {
                $userInput = mb_strtolower($options['user_input']);

                // İçerik üretim anahtar kelimeleri
                $featureKeywords = [
                    'uzun', 'makale', 'blog', 'yazı', 'içerik', 'text', 'content',
                    'detaylı', 'kapsamlı', 'geniş', 'profesyonel',
                    'yaz', 'oluştur', 'hazırla', 'üret', 'generate',
                    'başlık', 'paragraf', 'liste', 'madde',
                    'seo', 'optimizasyon', 'anahtar kelime',
                    'rapor', 'analiz', 'özet', 'sunum'
                ];

                // Kelime kontrolü
                foreach ($featureKeywords as $keyword) {
                    if (str_contains($userInput, $keyword)) {
                        // DEBUG: Feature mode override
                        Log::info('🎯 Mode Override: Chat→Feature', [
                            'user_input' => substr($options['user_input'], 0, 100),
                            'trigger_keyword' => $keyword,
                            'original_mode' => 'chat',
                            'new_mode' => 'feature'
                        ]);

                        return 'feature';
                    }
                }
            }
        } else {
            Log::info('⏭️ Mode override skipped due to skip_mode_override flag');
        }
        
        // URL bazlı tespit
        $currentUrl = request()->url();
        if (str_contains($currentUrl, '/chat') || str_contains($currentUrl, 'chat-panel') || str_contains($currentUrl, '/ask')) {
            return 'chat';
        }
        
        // Route bazlı tespit (admin chat route'ları)
        $routeName = request()->route() ? request()->route()->getName() : '';
        if (str_contains($routeName, 'chat') || str_contains($routeName, 'ask')) {
            return 'chat';
        }
        
        // Request path kontrolü
        $path = request()->path();
        if (str_contains($path, 'chat') || str_contains($path, 'ask') || str_contains($path, 'send-message')) {
            return 'chat';
        }
        
        // DEBUG: Mode tespit (gerektiğinde aç)
        // Log::info('🔍 Mode Detection Debug', [
        //     'url' => $currentUrl,
        //     'route_name' => $routeName, 
        //     'path' => $path,
        //     'detected_mode' => 'feature'
        // ]);
        
        // Default: feature modu (business odaklı)
        return 'feature';
    }
    
    /**
     * Tenant profil context'ini al (genel şirket bilgileri)
     */
    public function getTenantProfileContext(): ?string
    {
        $tenant = tenant();
        if (!$tenant) {
            return null;
        }

        $profile = \Modules\AI\App\Models\AITenantProfile::where('tenant_id', $effectiveTenant?->id ?? 1)->first();
        if (!$profile || !$profile->data) {
            return null;
        }

        $data = $profile->data;
        $context = "TENANT PROFILE CONTEXT:\n\n";
        
        // Temel şirket bilgileri
        if (!empty($data['company_name'])) {
            $context .= "Şirket: " . $data['company_name'] . "\n";
        }
        
        if (!empty($data['sector'])) {
            $context .= "Sektör: " . $data['sector'] . "\n";
        }
        
        if (!empty($data['target_audience'])) {
            $context .= "Hedef Kitle: " . $data['target_audience'] . "\n";
        }

        return $context;
    }
    
    /**
     * Marka tanıma context'ini al (brand_story HARİÇ tüm profil alanları)
     */
    /**
     * 🚀 YENİ OPTIMIZE TENANT CONTEXT - Priority sistemi ile
     */
    private function getOptimizedTenantContext(array $options = []): ?string
    {
        try {
            $tenantId = resolve_tenant_id();
            if (!$tenantId) {
                return null;
            }
            
            $profile = \Modules\AI\App\Models\AITenantProfile::where('tenant_id', $tenantId)->first();
            if (!$profile || !$profile->is_completed) {
                return null;
            }
            
            // Priority seviyesi belirle
            $contextType = $options['context_type'] ?? 'normal';
            $maxPriority = match($contextType) {
                'minimal' => 1,      // Sadece marka kimliği
                'essential' => 2,    // Marka kimliği + iş stratejisi  
                'normal' => 3,       // Standart (çoğu durum)
                'detailed' => 4,     // Tüm detaylar (sadece özel durumlar)
                default => 3
            };
            
            // Feature bazlı priority ayarlaması
            if (isset($options['feature_name'])) {
                $feature = $options['feature_name'];
                
                // Lokasyon önemli olan feature'lar
                if (str_contains($feature, 'local') || str_contains($feature, 'maps') || str_contains($feature, 'address')) {
                    $maxPriority = 4; // Lokasyon bilgisi için detaylı context
                }
                
                // Hızlı content için minimal
                if (str_contains($feature, 'quick') || str_contains($feature, 'instant') || str_contains($feature, 'fast')) {
                    $maxPriority = 2; // Hızlı content için temel bilgiler
                }
            }
            
            return $profile->getOptimizedAIContext($maxPriority);
            
        } catch (\Exception $e) {
            Log::error('Optimize tenant context error', [
                'error' => $e->getMessage(),
                'options' => $options
            ]);
            
            // Fallback: Eski sistem
            return $this->getTenantBrandContext();
        }
    }

    public function getTenantBrandContext(): ?string
    {
        try {
            // Yeni helper ile hızlı tenant ID çözümleme
            $tenantId = resolve_tenant_id(false); // Fallback yapma, null dönsün
            if (!$tenantId) {
                return null;
            }

            // AI Tenant Profile'ı al
            $profile = \Modules\AI\App\Models\AITenantProfile::where('tenant_id', $tenantId)->first();
            if (!$profile || !$profile->is_completed) {
                return null;
            }

            // YENİ SUMMARY SİSTEMİ - Hazırlanmış profil özeti
            $profileSummary = $profile->getAIProfileSummary();
            
            if (empty($profileSummary)) {
                return null;
            }

            // Marka context header'ı ekle
            $context = "# 🎯 MARKA TANIMA CONTEXT\n";
            $context .= "*Tüm AI davranışları bu marka profiline uygun olmalı. Bu bilgiler doğrultunda yanıt ver.*\n\n";
            $context .= $profileSummary;
            $context .= "\n\n---\n";
            $context .= "*Bu profil bilgileri doğrultunda marka kimliğine uygun, tutarlı ve özelleştirilmiş yanıtlar üret.*\n";

            return $context;
            
        } catch (\Exception $e) {
            Log::error('getTenantBrandContext error', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId ?? null
            ]);
            
            return null;
        }
    }

    /**
     * 🧹 HTML TAG TEMİZLEME
     * AI response'undan HTML tag'leri temizler
     */
    private function cleanHtmlTags(string $content): string
    {
        // Yaygın HTML tag'leri kaldır
        $htmlTags = [
            '/<p[^>]*>/i', '</p>',
            '/<br[^>]*>/i', 
            '/<div[^>]*>/i', '</div>',
            '/<span[^>]*>/i', '</span>',
            '/<strong[^>]*>/i', '</strong>',
            '/<b[^>]*>/i', '</b>',
            '/<em[^>]*>/i', '</em>',
            '/<i[^>]*>/i', '</i>',
            '/<h[1-6][^>]*>/i', '/<\/h[1-6]>/i',
            '/<ul[^>]*>/i', '</ul>',
            '/<ol[^>]*>/i', '</ol>',
            '/<li[^>]*>/i', '</li>',
            '/<a[^>]*>/i', '</a>',
        ];
        
        // Tag'leri kaldır
        $cleaned = preg_replace($htmlTags, '', $content);
        
        // HTML entity'leri decode et
        $cleaned = html_entity_decode($cleaned, ENT_QUOTES, 'UTF-8');
        
        // Fazla boşlukları temizle
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        
        return trim($cleaned);
    }
    
    /**
     * 🚫 YASAK KELİME TEMİZLEME
     * AI response'undan yasak ifadeleri kaldırır
     */
    private function removeProhibitedPhrases(string $content): string
    {
        $prohibitedPhrases = [
            // Yardım reddi ifadeleri
            '/Bu konuda yardımcı olamam/i',
            '/Bu konuda yardım edemem/i', 
            '/Size yardımcı olamam/i',
            '/Yardımcı olmakta zorlanıyorum/i',
            '/Bu alanda uzman değilim/i',
            '/Kesin bir bilgi veremem/i',
            
            // Bilgi eksikliği ifadeleri
            '/Daha fazla bilgi vermeniz gerekiyor/i',
            '/Hangi konuda/i',
            '/Ne hakkında/i',
            '/Lütfen daha spesifik olun/i',
            '/Daha detaylı açıklar mısınız/i',
            
            // Özür ifadeleri (başta)
            '/^Üzgünüm[,.]?\s*/i',
            '/^Maalesef[,.]?\s*/i',
            '/^Kusura bakmayın[,.]?\s*/i',
        ];
        
        foreach ($prohibitedPhrases as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }
        
        // Fazla boşlukları temizle
        $content = preg_replace('/\s+/', ' ', $content);
        
        return trim($content);
    }
    
    /**
     * 📊 RESPONSE KALİTE RAPORU
     * İşlenmiş response'un kalite metriklerini döndürür
     */
    private function generateQualityReport(string $content, array $requirements = []): array
    {
        $report = [
            'word_count' => str_word_count($content),
            'paragraph_count' => count(preg_split('/\n\s*\n/', trim($content))),
            'sentence_count' => preg_match_all('/[.!?]+/', $content),
            'has_html_tags' => preg_match('/<[^>]+>/', $content) > 0,
            'has_prohibited_phrases' => false,
            'quality_score' => 0
        ];
        
        // Yasak ifade kontrolü
        $prohibitedPhrases = [
            'yardımcı olamam', 'yardım edemem', 'hangi konuda',
            'daha fazla bilgi', 'üzgünüm', 'maalesef'
        ];
        
        foreach ($prohibitedPhrases as $phrase) {
            if (stripos($content, $phrase) !== false) {
                $report['has_prohibited_phrases'] = true;
                break;
            }
        }
        
        // Kalite skoru hesaplama (0-100)
        $score = 100;
        
        // HTML var ise -20
        if ($report['has_html_tags']) $score -= 20;
        
        // Yasak ifade var ise -30
        if ($report['has_prohibited_phrases']) $score -= 30;
        
        // Paragraf sayısı kontrolü (feature modunda)
        if (isset($requirements['mode']) && $requirements['mode'] !== 'chat') {
            if ($report['paragraph_count'] < 4) $score -= 25;
        }
        
        // Uzunluk kontrolü
        if (isset($requirements['user_input'])) {
            $lengthReq = $this->detectLengthRequirement($requirements['user_input']);
            if ($report['word_count'] < $lengthReq['min']) {
                $score -= 15;
            }
        }
        
        $report['quality_score'] = max(0, $score);
        
        return $report;
    }

    /**
     * Get current AI provider and model name
     * 
     * @return string
     */
    public function getCurrentProviderModel(): string
    {
        try {
            // Database'den default provider'ı al
            $defaultProvider = \Modules\AI\App\Models\AIProvider::getDefault();
            if ($defaultProvider) {
                return $defaultProvider->name . '/' . $defaultProvider->default_model;
            }
            
            return 'openai/gpt-4o';
            
        } catch (\Exception $e) {
            Log::error('getCurrentProviderModel error', [
                'error' => $e->getMessage()
            ]);
            
            return 'deepseek/deepseek-chat'; // fallback
        }
    }

    /**
     * Tenant'ın varsayılan dilini dinamik olarak al
     * 
     * @return array
     */
    private function getTenantDefaultLanguage(): array
    {
        try {
            $tenant = tenant();
            if (!$tenant) {
                // Tenant yoksa Türkçe default
                return ['code' => 'tr', 'name' => 'Türkçe'];
            }

            // 1. Tenants tablosundan varsayılan dil kodunu al
            $defaultCode = $tenant->tenant_default_locale ?? 'tr';
            
            // 2. TenantLanguage tablosundan bu dil koduna ait bilgileri al
            $language = \Modules\LanguageManagement\app\Models\TenantLanguage::where('code', $defaultCode)
                ->where('is_active', true)
                ->first();
            
            if ($language) {
                return [
                    'code' => $language->code,
                    'name' => $language->name,
                    'native_name' => $language->native_name ?? $language->name
                ];
            }
            
            // 3. Eğer tenant_languages'ta bulunamadıysa, fallback sistem
            $fallbacks = [
                'tr' => ['code' => 'tr', 'name' => 'Türkçe'],
                'en' => ['code' => 'en', 'name' => 'English'],
                'de' => ['code' => 'de', 'name' => 'Deutsch'],
                'fr' => ['code' => 'fr', 'name' => 'Français'],
                'es' => ['code' => 'es', 'name' => 'Español']
            ];
            
            return $fallbacks[$defaultCode] ?? $fallbacks['tr'];
            
        } catch (\Exception $e) {
            Log::warning('getTenantDefaultLanguage error', [
                'error' => $e->getMessage(),
                'tenant_id' => tenant('id') ?? 'none'
            ]);
            
            // Hata durumunda Türkçe default
            return ['code' => 'tr', 'name' => 'Türkçe'];
        }
    }

    /**
     * Universal Input System ile AI request processing
     * Phase 9 Integration - Added for Universal Input System support
     */
    public function processRequest(
        string $prompt,
        ?int $maxTokens = null,
        ?float $temperature = null,
        ?string $model = null,
        ?string $systemPrompt = null,
        array $metadata = []
    ): array {
        try {
            // Default değerler
            $maxTokens = $maxTokens ?? 2000;
            $temperature = $temperature ?? 0.7;
            
            // Provider kontrolü
            if (!$this->currentService) {
                throw new \Exception('AI Provider service not available');
            }
            
            // Model kontrolü - tenant'dan al ya da provider default'u kullan
            $tenant = tenant();
            $finalModel = $model ?? ($tenant ? $effectiveTenant?->default_ai_model : null) ?? $this->currentProvider->default_model;
            
            // Model bazlı kredi kontrolü (YENİ)
            if ($tenant) {
                // Estimate input tokens
                $estimatedInputTokens = strlen($prompt) / 4; // Rough estimation: 4 chars per token
                $estimatedOutputTokens = $maxTokens * 0.5; // Conservative estimation
                
                // Model bazlı kredi hesaplama
                $requiredCredits = ai_calculate_model_credits(
                    $this->currentProvider->id,
                    $finalModel,
                    $estimatedInputTokens,
                    $estimatedOutputTokens
                );
                
                // Kredi kontrolü
                if ($requiredCredits && $tenant->credits < $requiredCredits) {
                    return [
                        'success' => false,
                        'error' => 'insufficient_credits',
                        'message' => "Yetersiz kredi. Gerekli: {$requiredCredits}, Mevcut: {$tenant->credits}",
                        'required_credits' => $requiredCredits,
                        'available_credits' => $tenant->credits
                    ];
                }
            }
            
            // AI service çağrısı
            $startTime = microtime(true);
            
            // Provider'a göre service çağrısı
            if (method_exists($this->currentService, 'generateCompletion')) {
                $response = $this->currentService->generateCompletion($prompt, [
                    'max_tokens' => $maxTokens,
                    'temperature' => $temperature,
                    'model' => $model ?? $this->currentProvider->default_model
                ]);
            } else {
                // Claude ve diğer provider'lar için messages formatı
                $messages = [];
                
                // System prompt varsa ayrı olarak ekle
                if ($systemPrompt) {
                    $messages[] = [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ];
                    $userPrompt = $prompt;
                } else {
                    $userPrompt = $prompt;
                }
                
                // User message ekle
                $messages[] = [
                    'role' => 'user',
                    'content' => $userPrompt
                ];
                
                // ask metodu messages array bekliyor
                $response = $this->currentService->ask($messages, false);
            }
            
            $processingTime = microtime(true) - $startTime;
            
            // Debug: Response yapısını logla
            Log::info('🔍 AI Service Response Structure', [
                'response_type' => gettype($response),
                'has_choices' => isset($response['choices']),
                'has_response' => isset($response['response']),
                'has_content' => isset($response['content']),
                'response_keys' => is_array($response) ? array_keys($response) : 'NOT_ARRAY',
                'provider' => $this->currentProvider->name,
                'model' => $model ?? $this->currentProvider->default_model
            ]);
            
            // Model bazlı kredi düşümü (YENİ SISTEM)
            if ($tenant && isset($response['usage'])) {
                $inputTokens = $response['usage']['prompt_tokens'] ?? $response['usage']['input_tokens'] ?? 0;
                $outputTokens = $response['usage']['completion_tokens'] ?? $response['usage']['output_tokens'] ?? 0;
                
                // Model bazlı kredi hesapla ve düş
                $usedCredits = ai_use_credits_with_model(
                    $inputTokens,
                    $outputTokens,
                    $this->currentProvider->name,
                    $finalModel,
                    [
                        'tenant_id' => $effectiveTenant?->id ?? 1,
                        'provider_id' => $this->currentProvider->id,
                        'source' => $metadata['source'] ?? 'ai_feature',
                        'feature_id' => $metadata['feature_id'] ?? null,
                        'user_id' => auth()->id() ?? 1
                    ]
                );
                
                Log::info('🔥 Model-based credit deduction', [
                    'tenant_id' => $effectiveTenant?->id ?? 1,
                    'provider' => $this->currentProvider->name,
                    'model' => $finalModel,
                    'input_tokens' => $inputTokens,
                    'output_tokens' => $outputTokens,
                    'credits_used' => $usedCredits,
                    'remaining_credits' => $tenant->fresh()->credits
                ]);
                
                // 📊 CONVERSATION TRACKER - claude_ai.md TAM UYUM
                try {
                    $responseContent = '';
                    if (isset($response['choices'][0]['message']['content'])) {
                        $responseContent = $response['choices'][0]['message']['content'];
                    } elseif (isset($response['response'])) {
                        $responseContent = $response['response'];
                    } elseif (isset($response['content'])) {
                        $responseContent = $response['content'];
                    }
                    
                    ConversationTracker::saveConversation(
                        $prompt,
                        $responseContent,
                        $metadata['source'] ?? 'ai_feature',
                        [
                            'provider' => $this->currentProvider->name,
                            'model' => $finalModel,
                            'input_tokens' => $inputTokens,
                            'output_tokens' => $outputTokens,
                            'total_tokens' => $inputTokens + $outputTokens,
                            'credits_used' => $usedCredits,
                            'system_prompt' => $systemPrompt,
                            'metadata' => $metadata
                        ]
                    );
                    
                    Log::info('📊 Conversation kaydedildi', [
                        'feature' => $metadata['source'] ?? 'ai_feature',
                        'tokens' => $inputTokens + $outputTokens,
                        'credits' => $usedCredits
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('❌ Conversation kayıt hatası', [
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Response içeriğini al - daha güvenli parsing
            $content = '';
            if (isset($response['choices'][0]['message']['content'])) {
                $content = $response['choices'][0]['message']['content'];
                Log::info('✅ Content from choices[0].message.content');
            } elseif (isset($response['response'])) {
                $content = $response['response'];
                Log::info('✅ Content from response key');
            } elseif (isset($response['content'])) {
                $content = $response['content'];
                Log::info('✅ Content from content key');
            } elseif (is_string($response)) {
                $content = $response;
                Log::info('✅ Response is string directly');
            } else {
                Log::error('❌ Could not parse AI response', [
                    'response_structure' => $response
                ]);
            }
            
            return [
                'success' => true,
                'data' => [
                    'content' => $content,
                    'raw_response' => $response
                ],
                'tokens_used' => $response['usage']['total_tokens'] ?? 0,
                'model' => $model ?? $this->currentProvider->default_model,
                'processing_time' => $processingTime,
                'request_id' => $metadata['request_id'] ?? uniqid('ai_', true),
                'metadata' => $metadata
            ];
            
        } catch (\Exception $e) {
            Log::error('AIService processRequest error', [
                'error' => $e->getMessage(),
                'prompt_length' => strlen($prompt),
                'metadata' => $metadata,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Silent Fallback dene
            Log::info('🔇 Attempting Silent Fallback after processRequest error');
            
            $fallbackResult = $this->silentFallbackService->attemptSilentFallback(
                $this->currentProvider ? $this->currentProvider->name : 'unknown',
                $model ?? ($this->currentProvider ? $this->currentProvider->default_model : 'unknown'),
                $prompt,
                [
                    'max_tokens' => $maxTokens,
                    'temperature' => $temperature,
                    'system_prompt' => $systemPrompt,
                    'metadata' => $metadata
                ],
                $e->getMessage()
            );
            
            if ($fallbackResult) {
                Log::info('✅ Silent Fallback SUCCESS in processRequest', [
                    'fallback_provider' => $fallbackResult['provider']->name,
                    'fallback_model' => $fallbackResult['model']
                ]);
                
                // Fallback provider ile tekrar dene
                try {
                    $this->currentProvider = $fallbackResult['provider'];
                    $this->currentService = $fallbackResult['service'];
                    
                    // Fallback ile recursive çağrı YAP - tek sefer
                    return $this->processRequest($prompt, $maxTokens, $temperature, $fallbackResult['model'], $systemPrompt, $metadata);
                    
                } catch (\Exception $fallbackException) {
                    Log::error('🔇 Silent Fallback also failed', [
                        'fallback_error' => $fallbackException->getMessage()
                    ]);
                }
            }
            
            // Silent Fallback dene
            Log::info('🔇 Attempting Silent Fallback after processRequest error');
            
            $fallbackResult = $this->silentFallbackService->attemptSilentFallback(
                $this->currentProvider ? $this->currentProvider->name : 'unknown',
                $model ?? ($this->currentProvider ? $this->currentProvider->default_model : 'unknown'),
                $prompt,
                [
                    'max_tokens' => $maxTokens,
                    'temperature' => $temperature,
                    'system_prompt' => $systemPrompt,
                    'metadata' => $metadata
                ],
                $e->getMessage()
            );
            
            if ($fallbackResult) {
                Log::info('✅ Silent Fallback SUCCESS in processRequest', [
                    'fallback_provider' => $fallbackResult['provider']->name,
                    'fallback_model' => $fallbackResult['model']
                ]);
                
                // Fallback provider ile tekrar dene
                try {
                    $this->currentProvider = $fallbackResult['provider'];
                    $this->currentService = $fallbackResult['service'];
                    
                    // Fallback ile recursive çağrı YAP - tek sefer
                    return $this->processRequest($prompt, $maxTokens, $temperature, $fallbackResult['model'], $systemPrompt, $metadata);
                    
                } catch (\Exception $fallbackException) {
                    Log::error('🔇 Silent Fallback also failed', [
                        'fallback_error' => $fallbackException->getMessage()
                    ]);
                }
            }
            
            return [
                'success' => false,
                'error' => 'processing_error',
                'message' => 'AI processing failed: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? $e->getMessage() : null
            ];
        }
    }

    /**
     * Text translation using AI - with Smart HTML detection
     */
    public function translateText(string $text, string $fromLang, string $toLang, array $options = []): string
    {
        Log::info('🌐 translateText BAŞLADI', [
            'from' => $fromLang,
            'to' => $toLang,
            'text_length' => strlen($text),
            'text_preview' => substr($text, 0, 100),
            'options' => $options
        ]);

        if (empty(trim($text))) {
            Log::warning('⚠️ Boş text, çeviri yapılmadı');
            return '';
        }

        // 🧠 SMART HTML DETECTION - Büyük HTML içerikleri için
        if ($this->shouldUseSmartHtmlTranslation($text, $options)) {
            Log::info('🧠 Smart HTML Translation kullanılıyor', [
                'text_length' => strlen($text),
                'html_tag_count' => substr_count($text, '<')
            ]);

            try {
                $smartHtmlService = app(SmartHtmlTranslationService::class);
                return $smartHtmlService->translateHtmlContent($text, $fromLang, $toLang);
            } catch (\Exception $e) {
                Log::error('❌ Smart HTML Translation hatası, normal sisteme fallback', [
                    'error' => $e->getMessage()
                ]);
                // Fallback: Normal translation devam etsin
            }
        }

        // 🚀 STREAMING TRANSLATION - Çok büyük HTML içerikleri için
        if ($this->shouldUseStreamingTranslation($text, $options)) {
            Log::info('🚀 Streaming Translation kullanılıyor', [
                'text_length' => strlen($text),
                'session_id' => $options['session_id'] ?? 'auto_generated'
            ]);

            try {
                return $this->handleStreamingTranslation($text, $fromLang, $toLang, $options);
            } catch (\Exception $e) {
                Log::error('❌ Streaming Translation hatası, normal sisteme fallback', [
                    'error' => $e->getMessage()
                ]);
                // Fallback: Normal translation devam etsin
            }
        }

        if ($fromLang === $toLang) {
            Log::info('⚠️ Aynı dil, çeviri yapılmadı');
            return $text;
        }

        $context = $options['context'] ?? 'general';
        $maxLength = $options['max_length'] ?? null;
        $preserveHtml = $options['preserve_html'] ?? false;

        // 🔍 CHUNKING DEBUG - CRITICAL INVESTIGATION
        Log::info('🔍 CHUNKING DEBUG - Parameters check', [
            'text_length' => strlen($text),
            'preserve_html_raw' => $options['preserve_html'] ?? 'NOT_SET',
            'preserve_html_bool' => $preserveHtml,
            'condition_text_length' => strlen($text) > 500,
            'condition_preserve_html' => $preserveHtml,
            'condition_both' => $preserveHtml && strlen($text) > 500,
            'options_keys' => array_keys($options),
            'options_full' => $options
        ]);

        // 🔥 HTML İÇERİK CHUNK ÇEVİRİ SİSTEMİ - HER ZAMAN AKTIF
        if ($preserveHtml && strlen($text) > 500) {
            Log::info('🚨 Uzun HTML içerik tespit edildi, chunk çeviri yapılacak', [
                'text_length' => strlen($text),
                'from_lang' => $fromLang,
                'to_lang' => $toLang
            ]);
            // 🚀 SÜPER HIZLI BULK TRANSLATION SİSTEMİ
            $fastTranslator = new \Modules\AI\App\Services\FastHtmlTranslationService($this);
            return $fastTranslator->translateHtmlContentFast($text, $fromLang, $toLang, $context, $options);
        }

        // 🔥 ULTRA ASSERTIVE PROMPT SİSTEMİ - Zero refusal tolerance
        $prompt = \Modules\AI\App\Services\UltraAssertiveTranslationPrompt::buildPrompt($text, $fromLang, $toLang, $context, $preserveHtml);
        
        Log::info('📝 Translation prompt hazırlandı', [
            'prompt_length' => strlen($prompt),
            'from_lang' => $fromLang,
            'to_lang' => $toLang,
            'context' => $context
        ]);

        try {
            // 📊 CONVERSATION BAŞLAT - claude_ai.md sistemi
            $conversationData = [
                'tenant_id' => TenantHelpers::getTenantId(),
                'user_id' => auth()->id(),
                'session_id' => 'translation_' . uniqid(),
                'title' => "Translation: {$fromLang} → {$toLang}",
                'type' => 'translation',
                'feature_name' => 'ai_translate',
                'is_demo' => false,
                'prompt_id' => 1,
                'metadata' => [
                    'source' => 'translation_system',
                    'text_length' => strlen($text),
                    'estimated_tokens' => ceil(strlen($text) / 4) // Rough estimate
                ]
            ];

            $response = $this->processRequest(
                $prompt, 
                4000, // maxTokens - ARTTIRILDI: 2000 → 4000
                0.3,  // temperature - Lower for more consistent translations
                null, // model - use default
                null, // systemPrompt
                $conversationData // claude_ai.md uyumlu metadata
            );

            Log::info('🔍 Translation response received', [
                'success' => $response['success'],
                'has_content' => isset($response['data']['content']),
                'content_length' => isset($response['data']['content']) ? strlen($response['data']['content']) : 0,
                'content_preview' => isset($response['data']['content']) ? substr($response['data']['content'], 0, 100) : 'NO CONTENT'
            ]);

            if ($response['success']) {
                $translatedText = $response['data']['content'];
                
                if (empty(trim($translatedText))) {
                    Log::error('❌ Çeviri boş geldi!', [
                        'response' => $response,
                        'original_text' => substr($text, 0, 200)
                    ]);
                    return $text; // Fallback to original
                }
                
                // ❌ HTML TIRKANA İŞARETLERİNİ TEMİZLE - NURULLAH'IN TALEBİ
                // AI HTML içeriği farklı formatlarla sarıyor, hepsini temizle
                $originalText = $translatedText;
                
                // Pattern 1: ```html\n content \n```
                $translatedText = preg_replace('/^```html\s*\n?/', '', $translatedText);
                $translatedText = preg_replace('/\n?\s*```$/', '', $translatedText);
                
                // Pattern 2: ```\n content \n```  
                $translatedText = preg_replace('/^```\s*\n?/', '', $translatedText);
                $translatedText = preg_replace('/\n?\s*```$/', '', $translatedText);
                
                // Pattern 3: ``` content ```
                $translatedText = preg_replace('/^```\s*/', '', $translatedText);
                $translatedText = preg_replace('/\s*```$/', '', $translatedText);
                
                $translatedText = trim($translatedText);
                
                Log::info('🧹 HTML tırnak temizliği yapıldı', [
                    'before_length' => strlen($response['data']['content']),
                    'after_length' => strlen($translatedText),
                    'cleaned' => $response['data']['content'] !== $translatedText
                ]);
                
                // Apply max length if specified
                if ($maxLength && mb_strlen($translatedText) > $maxLength) {
                    $translatedText = mb_substr($translatedText, 0, $maxLength - 3) . '...';
                }

                Log::info('✅ Çeviri BAŞARILI', [
                    'from' => $fromLang,
                    'to' => $toLang,
                    'original_length' => strlen($text),
                    'translated_length' => strlen($translatedText),
                    'translated_preview' => substr($translatedText, 0, 100)
                ]);

                // 📊 CONVERSATION KAYIT SİSTEMİ - claude_ai.md uyumlu
                try {
                    \DB::table('ai_conversations')->insert([
                        'tenant_id' => TenantHelpers::getTenantId(),
                        'user_id' => $this->getSafeUserId(),
                        'session_id' => 'translation_' . uniqid(),
                        'title' => "Translation: {$fromLang} → {$toLang}",
                        'type' => 'translation',
                        'feature_name' => 'ai_translate',
                        'is_demo' => false,
                        'prompt_id' => 1,
                        'total_tokens_used' => $response['tokens_used'] ?? 0,
                        'metadata' => json_encode([
                            'input_data' => [
                                'text' => substr($text, 0, 500), // İlk 500 karakter
                                'from_language' => $fromLang,
                                'to_language' => $toLang,
                                'context' => $context,
                                'preserve_html' => $preserveHtml
                            ],
                            'output_data' => [
                                'translated_text' => substr($translatedText, 0, 500),
                                'original_length' => strlen($text),
                                'translated_length' => strlen($translatedText)
                            ],
                            'provider_used' => $this->currentProvider->name ?? 'unknown',
                            'model_used' => $response['model'] ?? 'unknown',
                            'processing_time' => $response['processing_time'] ?? 0
                        ]),
                        'status' => 'completed',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    Log::info('📊 Conversation kaydedildi - claude_ai.md sistemi', [
                        'type' => 'translation',
                        'tenant_id' => TenantHelpers::getTenantId(),
                        'tokens' => $response['tokens_used'] ?? 0
                    ]);
                } catch (\Exception $e) {
                    Log::error('❌ Conversation kayıt hatası', [
                        'error' => $e->getMessage()
                    ]);
                    // Hata olsa bile çeviri çalışmaya devam etsin
                }

                return $translatedText;
            } else {
                Log::error('❌ Translation response not successful', [
                    'response' => $response
                ]);
                throw new \Exception($response['message']);
            }

        } catch (\Exception $e) {
            Log::error('Translation failed', [
                'from' => $fromLang,
                'to' => $toLang,
                'text_length' => strlen($text),
                'error' => $e->getMessage()
            ]);

            // Fallback: return original text
            return $text;
        }
    }

    /**
     * Build translation prompt
     */
    /**
     * Build translation prompt
     */
    private function buildTranslationPrompt(string $text, string $fromLang, string $toLang, string $context, bool $preserveHtml): string
    {
        // Geliştirilmiş dil isimleri - native yazımları dahil
        $languageNames = [
            'tr' => 'Türkçe (Turkish)',
            'en' => 'English', 
            'de' => 'Deutsch (German)',
            'fr' => 'Français (French)',
            'es' => 'Español (Spanish)',
            'it' => 'Italiano (Italian)',
            'ar' => 'العربية (Arabic)',
            'da' => 'Dansk (Danish)',
            'bn' => 'বাংলা (Bengali)',
            'sq' => 'Shqip (Albanian)',
            'zh' => '中文 (Chinese)',
            'ja' => '日本語 (Japanese)',
            'ko' => '한국어 (Korean)',
            'ru' => 'Русский (Russian)',
            'pt' => 'Português (Portuguese)',
            'nl' => 'Nederlands (Dutch)',
            'sv' => 'Svenska (Swedish)',
            'no' => 'Norsk (Norwegian)',
            'fi' => 'Suomi (Finnish)',
            'pl' => 'Polski (Polish)',
            'cs' => 'Čeština (Czech)',
            'hu' => 'Magyar (Hungarian)',
            'ro' => 'Română (Romanian)',
            'he' => 'עברית (Hebrew)',
            'hi' => 'हिन्दी (Hindi)',
            'th' => 'ไทย (Thai)',
            'vi' => 'Tiếng Việt (Vietnamese)',
            'id' => 'Bahasa Indonesia (Indonesian)',
            'fa' => 'فارسی (Persian)',
            'ur' => 'اردو (Urdu)',
            'el' => 'Ελληνικά (Greek)',
            'bg' => 'Български (Bulgarian)',
            'hr' => 'Hrvatski (Croatian)',
            'sr' => 'Српски (Serbian)',
            'sl' => 'Slovenščina (Slovenian)',
            'sk' => 'Slovenčina (Slovak)',
            'uk' => 'Українська (Ukrainian)',
            'et' => 'Eesti (Estonian)',
            'lv' => 'Latviešu (Latvian)',
            'lt' => 'Lietuvių (Lithuanian)',
            'ms' => 'Bahasa Melayu (Malay)',
        ];

        $fromLanguageName = $languageNames[$fromLang] ?? strtoupper($fromLang) . ' Language';
        $toLanguageName = $languageNames[$toLang] ?? strtoupper($toLang) . ' Language';

        $contextInstructions = match($context) {
            'title' => 'Bu bir başlık metnidir. Kısa, net ve SEO dostu olmalıdır.',
            'seo_title' => 'Bu bir SEO başlığıdır. 60 karakter sınırında, anahtar kelime içermeli ve tıklanabilir olmalıdır.',
            'seo_description' => 'Bu bir SEO açıklamasıdır. 160 karakter sınırında, çekici ve bilgilendirici olmalıdır.',
            'seo_keywords' => 'Bunlar SEO anahtar kelimeleridir. Virgülle ayrılmış şekilde çevir.',
            'html_content' => 'Bu HTML içeriğidir. HTML etiketlerini koruyarak sadece metin kısmını çevir.',
            default => 'Bu genel bir metindir. Doğal ve akıcı bir şekilde çevir.'
        };

        $htmlInstructions = $preserveHtml ? "\n- HTML etiketlerini aynen koru, sadece metin içeriğini çevir" : "";

        // 🚀 DİNAMİK DİL KISITLAMA SİSTEMİ - Hedef dile göre ayarlanır
        $restrictedLanguages = collect(['en', 'es', 'fr', 'de', 'bg', 'tr'])
            ->reject(fn($lang) => $lang === $toLang)
            ->map(fn($lang) => $languageNames[$lang] ?? strtoupper($lang))
            ->join(', ');

        $languageRestriction = "
- FORBIDDEN LANGUAGES: {$restrictedLanguages}
- REQUIRED OUTPUT: Pure {$toLanguageName} ({$toLang}) ONLY
- PENALTY: If you output in forbidden languages, the translation FAILS";

        return "Sen profesyonel bir çevirmensin. Aşağıdaki metni {$fromLanguageName} dilinden {$toLanguageName} diline çevir.

CONTEXT: {$contextInstructions}

ÇEVİRİ KURALLARI:
- Doğal ve akıcı bir çeviri yap
- Kültürel bağlamı koru
- Teknik terimleri doğru çevir{$htmlInstructions}
- Sadece çeviriyi döndür, başka açıklama ekleme{$languageRestriction}

ÇEVİRİLECEK METİN:
{$text}";
    }

    /**
     * 🔥 UZUN HTML İÇERİK ÇEVİRİ SİSTEMİ - TOKEN LİMİT AŞIMI ENGELLEYİCİ
     * Uzun HTML içeriği parçalara böler ve sadece text kısımlarını çevirir
     */
    private function translateLongHtmlContent(string $html, string $fromLang, string $toLang, string $context): string
    {
        Log::info('🔧 Uzun HTML chunk çeviri başlıyor', [
            'html_length' => strlen($html),
            'from_lang' => $fromLang,
            'to_lang' => $toLang
        ]);

        try {
            // HTML'deki tüm text nodeları bul ve çevir
            $dom = new \DOMDocument('1.0', 'UTF-8');
            
            // HTML parse hatalarını bastır
            $originalErrorSetting = libxml_use_internal_errors(true);
            
            // UTF-8 desteği için meta tag ekle
            $htmlWithMeta = '<meta charset="UTF-8">' . $html;
            $dom->loadHTML($htmlWithMeta, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            
            $xpath = new \DOMXPath($dom);
            
            // Sadece text nodeları bul (element içinde olmayan)
            $textNodes = $xpath->query('//text()[normalize-space()]');
            
            $translatedTexts = [];
            $originalTexts = [];
            
            foreach ($textNodes as $textNode) {
                $originalText = trim($textNode->nodeValue);
                
                // Boş veya çok kısa metinleri atla
                if (strlen($originalText) < 3) {
                    continue;
                }
                
                // Sadece sayı veya sembol olan metinleri atla
                if (preg_match('/^[\d\s\-\.\,\+\*\/\=\(\)]+$/', $originalText)) {
                    continue;
                }
                
                $originalTexts[] = $originalText;
                
                // Her text node'u ayrı ayrı çevir
                $translatedText = $this->translateText(
                    $originalText,
                    $fromLang,
                    $toLang,
                    [
                        'context' => 'html_text_node',
                        'preserve_html' => false
                    ]
                );
                
                $translatedTexts[] = $translatedText;
                $textNode->nodeValue = $translatedText;
            }
            
            // HTML'i geri çıkar (meta tag'ı çıkar)
            $translatedHtml = $dom->saveHTML();
            $translatedHtml = preg_replace('/<meta charset="UTF-8">/', '', $translatedHtml);
            
            // libxml hata ayarını geri yükle
            libxml_use_internal_errors($originalErrorSetting);
            
            Log::info('✅ HTML chunk çeviri tamamlandı', [
                'original_length' => strlen($html),
                'translated_length' => strlen($translatedHtml),
                'text_nodes_translated' => count($translatedTexts)
            ]);
            
            return trim($translatedHtml);
            
        } catch (\Exception $e) {
            Log::error('❌ HTML chunk çeviri hatası', [
                'error' => $e->getMessage(),
                'html_length' => strlen($html)
            ]);
            
            // Fallback: Normal çeviri yap (kesilse bile)
            return $this->translateText($html, $fromLang, $toLang, ['context' => $context, 'preserve_html' => true]);
        }
    }

    /**
     * Smart HTML Translation kullanılıp kullanılmayacağını belirler
     */
    private function shouldUseSmartHtmlTranslation(string $text, array $options = []): bool
    {
        // HTML içerik kontrolü
        $hasHtmlTags = substr_count($text, '<') > 5; // En az 5 HTML tag
        
        // Boyut kontrolü (5KB üzeri)
        $isLargeContent = strlen($text) > 5120;
        
        // HTML oranı kontrolü
        $textOnly = strip_tags($text);
        $htmlRatio = (strlen($text) - strlen($textOnly)) / strlen($text);
        $hasHighHtmlRatio = $htmlRatio > 0.3; // %30'dan fazla HTML
        
        // Context kontrolü - body alanları için özellikle aktif
        $isBodyContent = isset($options['context']) && 
                        (strpos($options['context'], 'body') !== false || 
                         strpos($options['context'], 'content') !== false);
        
        return $hasHtmlTags && ($isLargeContent || $hasHighHtmlRatio || $isBodyContent);
    }

    /**
     * Streaming Translation kullanılıp kullanılmayacağını belirler
     */
    private function shouldUseStreamingTranslation(string $text, array $options = []): bool
    {
        // Çok büyük içerikler için streaming (15KB üzeri)
        $isVeryLargeContent = strlen($text) > 15360;
        
        // Session ID var mı (modal'dan gelen istekler)
        $hasSessionId = isset($options['session_id']);
        
        // Chunk context'i var mı
        $isChunkContext = isset($options['context']) && 
                         strpos($options['context'], 'chunk') !== false;
        
        return $isVeryLargeContent && ($hasSessionId || $isChunkContext);
    }

    /**
     * Streaming translation'ı handle eder
     */
    private function handleStreamingTranslation(
        string $text, 
        string $fromLang, 
        string $toLang, 
        array $options
    ): string {
        $sessionId = $options['session_id'] ?? 'auto_' . uniqid();
        
        Log::info('🚀 Starting streaming translation', [
            'session_id' => $sessionId,
            'text_length' => strlen($text),
            'from' => $fromLang,
            'to' => $toLang
        ]);

        try {
            $streamingEngine = app(StreamingTranslationEngine::class);
            
            $result = $streamingEngine->startStreamingTranslation(
                $text, 
                $fromLang, 
                $toLang, 
                $sessionId, 
                $options
            );
            
            if ($result['success']) {
                // Streaming başarıyla başladı, placeholder döndür
                return "<!-- STREAMING_TRANSLATION_PLACEHOLDER:{$sessionId} -->";
            } else {
                throw new \Exception('Streaming translation başlatılamadı: ' . $result['error']);
            }
            
        } catch (\Exception $e) {
            Log::error('❌ Streaming translation handle error', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
            
            // Fallback to normal translation
            throw $e;
        }
    }

    /**
     * Smart HTML Translation kullanılıp kullanılmayacağını belirler
     */

    /**
     * 🌐 HTML içerikli metinleri çevirir
     * Ultra Assertive Translation sistemi ile
     */
    public function translateHtml(
        string $html, 
        string $fromLang, 
        string $toLang, 
        array $options = []
    ): string {
        // HTML çevirisi için özel context ayarla
        $options['context'] = $options['context'] ?? 'html_content';
        $options['preserve_html'] = true;
        
        return $this->translateText($html, $fromLang, $toLang, $options);
    }

    /**
     * 🔒 SAFE USER ID DETECTION - Multi-tenant uyumlu
     * Queue/Job context'inde auth()->id() null döner, bu metod her durumda geçerli user_id verir
     */
    private function getSafeUserId(): int
    {
        try {
            // Web context - normal auth check
            if (function_exists('auth') && auth()->guard('web')->check()) {
                $userId = auth()->guard('web')->id();
                if ($userId && is_numeric($userId) && $userId > 0) {
                    \Log::debug('🔍 AIService: Web auth user_id found', ['user_id' => $userId]);
                    return (int) $userId;
                }
            }
            
            // CLI/Queue/Artisan context - tenant'daki ilk admin user'ı al
            try {
                $tenantId = TenantHelpers::getTenantId();
                
                // Tenant'daki active admin user'ı bul (Spatie roles ile)
                try {
                    $adminUser = \DB::table('users')
                        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->where('model_has_roles.model_type', 'App\\Models\\User')
                        ->whereIn('roles.name', ['admin', 'super-admin'])
                        ->where('users.is_active', true)
                        ->select('users.*')
                        ->orderBy('users.id', 'asc')
                        ->first();
                    
                    if ($adminUser) {
                        \Log::debug('🔍 AIService: Admin user found for tenant', [
                            'tenant_id' => $tenantId,
                            'user_id' => $adminUser->id,
                            'user_name' => $adminUser->name ?? 'unknown'
                        ]);
                        return (int) $adminUser->id;
                    }
                } catch (\Exception $e) {
                    \Log::debug('🔍 AIService: Spatie role lookup failed, trying direct user lookup', [
                        'error' => $e->getMessage()
                    ]);
                }
                
                // Admin user yoksa ilk active user'ı al
                $firstUser = \DB::table('users')
                    ->where('is_active', true)
                    ->orderBy('id', 'asc')
                    ->first();
                    
                if ($firstUser) {
                    \Log::debug('🔍 AIService: First active user found', [
                        'tenant_id' => $tenantId,
                        'user_id' => $firstUser->id,
                        'user_name' => $firstUser->name ?? 'unknown'
                    ]);
                    return (int) $firstUser->id;
                }
                
            } catch (\Exception $e) {
                \Log::warning('🔍 AIService: Tenant user lookup failed', ['error' => $e->getMessage()]);
            }
            
            // Son çare: System user (ID=1)
            \Log::info('🔍 AIService: Using fallback system user_id = 1 (CLI/Queue/Background context)');
            return 1; // GUARANTEED valid user ID
            
        } catch (\Exception $e) {
            \Log::warning('🔍 AIService: Exception in getSafeUserId, using fallback', ['error' => $e->getMessage()]);
            return 1; // GUARANTEED valid user ID  
        }
    }
}
