<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Services\DeepSeekService;
use Modules\AI\App\Services\ConversationService;
use Modules\AI\App\Services\PromptService;
use Modules\AI\App\Services\AIPriorityEngine;
use Modules\AI\App\Models\Setting;
use App\Helpers\TenantHelpers;
use App\Services\AITokenService;
use Illuminate\Support\Facades\Cache;

class AIService
{
    protected $deepSeekService;
    protected $conversationService;
    protected $promptService;
    protected $aiTokenService;

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
        // DeepSeek servisini yükle
        $this->deepSeekService = $deepSeekService ?? new DeepSeekService();
        
        // Diğer servisleri oluştur
        $this->promptService = $promptService ?? new PromptService();
        $this->aiTokenService = $aiTokenService ?? new AITokenService();
        
        // ConversationService en son oluşturulmalı çünkü diğer servislere bağımlı
        $this->conversationService = $conversationService ?? 
            new ConversationService($this->deepSeekService, $this->aiTokenService);
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
            $prompt = \Modules\AI\App\Models\Prompt::find($options['prompt_id']);
            if ($prompt) {
                $customPrompt = $prompt->content;
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

        // AI'dan yanıt al
        $response = $this->deepSeekService->ask($messages, $stream);
        
        if ($response && !$stream) {
            // Token kullanımını kaydet
            if ($tenant) {
                // Modern token sistemi
                $actualTokens = $this->deepSeekService->estimateTokens([
                    ['role' => 'user', 'content' => $prompt],
                    ['role' => 'assistant', 'content' => $response]
                ]);
                
                $this->aiTokenService->useTokens(
                    $tenant, 
                    $actualTokens, 
                    'chat',
                    'AI Chat: ' . substr($prompt, 0, 50) . '...'
                );
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
        } else {
            // Legacy sistem: Basit custom prompt
            $systemPrompt = $feature->custom_prompt ?: "Sen yardımcı bir AI asistanısın.";
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

        // AI'dan yanıt al
        $response = $this->deepSeekService->ask($messages, false);
        
        if ($response) {
            // Feature kullanım istatistiklerini güncelle
            $feature->incrementUsage();
            
            // Token kullanımını kaydet
            if ($tenant) {
                $actualTokens = $this->deepSeekService->estimateTokens([
                    ['role' => 'user', 'content' => $userInput],
                    ['role' => 'assistant', 'content' => $response]
                ]);
                
                $this->aiTokenService->useTokens(
                    $tenant, 
                    $actualTokens, 
                    'feature_test',
                    'AI Feature: ' . $feature->name
                );
            }
            
            // Conversation tracking
            $this->createConversationRecord($userInput, $response, 'feature_test', [
                'feature_id' => $feature->id,
                'feature_name' => $feature->name,
                'source' => 'feature_test'
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

            // AI response
            \Modules\AI\App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'content' => $aiResponse,
                'role' => 'assistant',
                'token_count' => strlen($aiResponse) / 4 // Tahmini
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
            return Setting::first();
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
        $settings = Setting::first();
        
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
}