<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Services\DeepSeekService;
use Modules\AI\App\Services\ConversationService;
use Modules\AI\App\Services\LimitService;
use Modules\AI\App\Services\PromptService;
use Modules\AI\App\Models\Setting;
use App\Helpers\TenantHelpers;
use Illuminate\Support\Facades\Cache;

class AIService
{
    protected $deepSeekService;
    protected $conversationService;
    protected $limitService;
    protected $promptService;
    protected $tenantId;

    /**
     * Constructor
     *
     * @param DeepSeekService|null $deepSeekService
     * @param ConversationService|null $conversationService
     * @param LimitService|null $limitService
     * @param PromptService|null $promptService
     */
    public function __construct(
        ?DeepSeekService $deepSeekService = null,
        ?ConversationService $conversationService = null,
        ?LimitService $limitService = null,
        ?PromptService $promptService = null
    ) {
        $this->tenantId = tenant_id();
        
        // Tenant'a özgü DeepSeek servisini yükle
        // tenantId null olabileceği için doğrudan gönderiyoruz
        $this->deepSeekService = $deepSeekService ?? DeepSeekService::forTenant($this->tenantId);
        
        // Diğer servisleri oluştur
        // tenantId null olabileceğini hesaba katıyoruz
        $this->limitService = $limitService ?? new LimitService($this->tenantId);
        $this->promptService = $promptService ?? new PromptService($this->tenantId);
        
        // ConversationService en son oluşturulmalı çünkü diğer servislere bağımlı
        $this->conversationService = $conversationService ?? 
            new ConversationService($this->deepSeekService, $this->limitService);
    }

    /**
     * AI'ya doğrudan soru sor (konuşma oluşturmadan)
     *
     * @param string $prompt
     * @param array $options
     * @return string|null
     */
    public function ask(string $prompt, array $options = []): ?string
    {
        // Limit kontrolü yap
        if (!$this->limitService->checkLimits()) {
            return "Üzgünüm, kullanım limitinize ulaştınız.";
        }

        // Sistem promptunu ayarla
        $context = $options['context'] ?? null;
        $systemPrompt = null;
        
        if (isset($options['prompt_id'])) {
            $prompt = TenantHelpers::central(function () use ($options) {
                return \Modules\AI\App\Models\Prompt::find($options['prompt_id']);
            });
            
            if ($prompt) {
                $systemPrompt = $prompt->content;
            }
        } elseif ($context) {
            $defaultPrompt = $this->promptService->getDefaultPrompt();
            if ($defaultPrompt) {
                $systemPrompt = $defaultPrompt->content . "\n\nKONTEKST:\n" . $context;
            } else {
                $systemPrompt = "Aşağıdaki konteksti kullanarak yanıtla:\n\n" . $context;
            }
        } else {
            $defaultPrompt = $this->promptService->getDefaultPrompt();
            if ($defaultPrompt) {
                $systemPrompt = $defaultPrompt->content;
            }
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
            'content' => $prompt
        ];

        // AI'dan yanıt al
        $response = $this->deepSeekService->ask($messages);
        
        if ($response) {
            // Token sayısını tahmin et ve kullanım limitini güncelle
            $tokens = $this->deepSeekService->estimateTokens([
                ['role' => 'user', 'content' => $prompt],
                ['role' => 'assistant', 'content' => $response]
            ]);
            
            $this->limitService->incrementUsage($tokens);
        }

        return $response;
    }

    /**
     * Tenant ayarlarını getir
     *
     * @return Setting|null
     */
    public function getSettings(): ?Setting
    {
        // Tenant ID yoksa null döndür
        if ($this->tenantId === null) {
            return null;
        }
        
        $cacheKey = "ai_settings_tenant_{$this->tenantId}";
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            return TenantHelpers::central(function () {
                return Setting::where('tenant_id', $this->tenantId)->first();
            });
        });
    }

    /**
     * Tenant ayarlarını güncelle
     *
     * @param array $data
     * @return Setting|null
     */
    public function updateSettings(array $data): ?Setting
    {
        // Tenant ID yoksa null döndür
        if ($this->tenantId === null) {
            return null;
        }
        
        $settings = TenantHelpers::central(function () use ($data) {
            $settings = Setting::where('tenant_id', $this->tenantId)->first();
            
            if (!$settings) {
                return Setting::create(array_merge($data, ['tenant_id' => $this->tenantId]));
            }
            
            $settings->update($data);
            return $settings;
        });
        
        // Önbelleği temizle
        Cache::forget("ai_settings_tenant_{$this->tenantId}");
        
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
     * LimitService getter
     */
    public function limits()
    {
        return $this->limitService;
    }

    /**
     * PromptService getter
     */
    public function prompts()
    {
        return $this->promptService;
    }
}