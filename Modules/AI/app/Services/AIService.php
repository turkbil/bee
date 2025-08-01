<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Services\DeepSeekService;
use Modules\AI\App\Services\ConversationService;
use Modules\AI\App\Services\PromptService;
use Modules\AI\App\Services\AIPriorityEngine;
use Modules\AI\App\Services\AIProviderManager;
use App\Helpers\TenantHelpers;
use App\Services\AITokenService;
use Illuminate\Support\Facades\Cache;

class AIService
{
    protected $deepSeekService;
    protected $conversationService;
    protected $promptService;
    protected $aiTokenService;
    protected $providerManager;
    protected $currentProvider;
    protected $currentService;

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
        ?AITokenService $aiTokenService = null
    ) {
        // Provider Manager'ı yükle
        $this->providerManager = new AIProviderManager();
        
        // Varsayılan provider'ı al
        try {
            $providerData = $this->providerManager->getProviderServiceWithFailover();
            $this->currentProvider = $providerData['provider'];
            $this->currentService = $providerData['service'];
        } catch (\Exception $e) {
            \Log::warning('AI Provider yüklenemedi, DeepSeek fallback kullanılıyor: ' . $e->getMessage());
            $this->deepSeekService = $deepSeekService ?? new DeepSeekService();
            $this->currentService = $this->deepSeekService;
        }
        
        // Diğer servisleri oluştur
        $this->promptService = $promptService ?? new PromptService();
        $this->aiTokenService = $aiTokenService ?? new AITokenService();
        
        // ConversationService en son oluşturulmalı çünkü diğer servislere bağımlı
        $this->conversationService = $conversationService ?? 
            new ConversationService($this->currentService, $this->aiTokenService);
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
        // Modern token sistemi kontrolü
        $tenant = tenant();
        if ($tenant) {
            $tokensNeeded = $this->aiTokenService->estimateTokenCost('chat_message', ['message' => $prompt]);
            
            if (!$this->aiTokenService->canUseTokens($tenant, $tokensNeeded)) {
                return "Üzgünüm, yetersiz AI token bakiyeniz var veya aylık limitinize ulaştınız.";
            }
        } else {
            \Log::warning('Tenant bulunmadı, AI isteği için basit token kontrolü yapılıyor');
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

        // Build full system prompt with TENANT CONTEXT
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
            \Log::error('🚨 Prompt is not string!', [
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

        // Token kullanımını kaydet
        if ($tenant && isset($response['tokens_used'])) {
            $this->aiTokenService->useTokens($tenant, $response['tokens_used'], 'chat_message');
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
        // Modern token sistemi kontrolü
        $tenant = tenant();
        if ($tenant) {
            $tokensNeeded = $this->aiTokenService->estimateTokenCost('chat_message', ['message' => $prompt]);
            
            if (!$this->aiTokenService->canUseTokens($tenant, $tokensNeeded)) {
                return "Üzgünüm, yetersiz AI token bakiyeniz var veya aylık limitinize ulaştınız.";
            }
        } else {
            // Central admin için basit kontrol (tenant yoksa)
            // limitService yerine basit bir kontrol yap
            \Log::warning('Tenant bulunmadı, AI isteği için basit token kontrolü yapılıyor');
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

        // Build full system prompt with TENANT CONTEXT
        $systemPrompt = $this->buildFullSystemPrompt($customPrompt, $options);

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
                    'tenant_id' => $tenant->id,
                    'description' => 'AI Chat: ' . substr($prompt, 0, 50) . '...',
                    'source' => 'ai_service_ask'
                ]);
            } else {
                // Legacy limit sistemi kaldırıldı - sadece log
                \Log::info('AI yanıt başarılı (legacy mode)', [
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
        
        // Modern token sistemi kontrolü
        $tenant = tenant();
        if ($tenant) {
            $tokensNeeded = $this->aiTokenService->estimateTokenCost('feature_test', [
                'feature' => $feature->name,
                'input' => $userInput
            ]);
            
            if (!$this->aiTokenService->canUseTokens($tenant, $tokensNeeded)) {
                return "Üzgünüm, yetersiz AI token bakiyeniz var veya aylık limitinize ulaştınız.";
            }
        }

        // Yeni template sistemi: Quick + Expert + Response Template
        if ($feature->hasQuickPrompt() || $feature->hasResponseTemplate()) {
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
            
            // YENİ MERKEZİ KREDİ DÜŞME SİSTEMİ - FEATURE
            if ($tenant) {
                // API response'undan token bilgilerini al
                $tokenData = is_array($apiResponse) ? $apiResponse : [];
                
                // Provider adını belirle
                $providerName = $this->currentProvider ? $this->currentProvider->name : 'unknown';
                
                // Merkezi kredi düşme sistemi
                ai_use_calculated_credits($tokenData, $providerName, [
                    'usage_type' => 'feature_test',
                    'tenant_id' => $tenant->id,
                    'feature_slug' => $feature->slug,
                    'feature_id' => $feature->id,
                    'feature_name' => $feature->name,
                    'description' => 'AI Feature: ' . $feature->name,
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
            \Log::warning('Debug logging failed', [
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
                'tenant_id' => $tenant->id,
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
            \Log::warning('Conversation kaydı oluşturulamadı: ' . $e->getMessage());
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
     * Gizli promptları birleştirerek tam sistem promptunu oluşturur - YENİ PRIORITY ENGINE
     *
     * @param string $userPrompt Kullanıcının seçtiği prompt
     * @param array $options Context options
     * @return string Tam sistem promptu
     */
    public function buildFullSystemPrompt($userPrompt = '', array $options = [])
    {
        // Custom user prompt varsa component olarak ekle
        $customComponents = [];
        if (!empty($userPrompt)) {
            $customComponents[] = [
                'category' => 'feature_definition',
                'priority' => 1,
                'content' => $userPrompt,
                'name' => 'User Custom Prompt'
            ];
        }
        
        $options['custom_components'] = $customComponents;
        
        // AIPriorityEngine ile complete prompt oluştur
        return AIPriorityEngine::buildCompletePrompt($options);
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

        $profile = \Modules\AI\App\Models\AITenantProfile::where('tenant_id', $tenant->id)->first();
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
            \Log::error('Optimize tenant context error', [
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
            \Log::error('getTenantBrandContext error', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId ?? null
            ]);
            
            return null;
        }
    }

    /**
     * Get current AI provider and model name
     * 
     * @return string
     */
    public function getCurrentProviderModel(): string
    {
        try {
            // Get current provider settings
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
            if (!$settings || !$settings->providers) {
                return 'deepseek/deepseek-chat'; // fallback
            }

            $activeProvider = $settings->active_provider ?? 'deepseek';
            $providers = $settings->providers;

            if (!isset($providers[$activeProvider])) {
                return 'deepseek/deepseek-chat'; // fallback
            }

            $provider = $providers[$activeProvider];
            $model = $provider['model'] ?? 'unknown';

            return $activeProvider . '/' . $model;
            
        } catch (\Exception $e) {
            \Log::error('getCurrentProviderModel error', [
                'error' => $e->getMessage()
            ]);
            
            return 'deepseek/deepseek-chat'; // fallback
        }
    }
}