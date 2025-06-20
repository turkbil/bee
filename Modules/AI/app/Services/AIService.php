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
        // DeepSeek servisini yükle
        $this->deepSeekService = $deepSeekService ?? new DeepSeekService();
        
        // Diğer servisleri oluştur
        $this->limitService = $limitService ?? new LimitService();
        $this->promptService = $promptService ?? new PromptService();
        
        // ConversationService en son oluşturulmalı çünkü diğer servislere bağımlı
        $this->conversationService = $conversationService ?? 
            new ConversationService($this->deepSeekService, $this->limitService);
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
        // Limit kontrolü yap
        if (!$this->limitService->checkLimits()) {
            return "Üzgünüm, kullanım limitinize ulaştınız.";
        }

        // Sistem promptunu ayarla
        $context = $options['context'] ?? null;
        $systemPrompt = null;
        
        if (isset($options['prompt_id'])) {
            $prompt = \Modules\AI\App\Models\Prompt::find($options['prompt_id']);
            
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
        $response = $this->deepSeekService->ask($messages, $stream);
        
        if ($response && !$stream) {
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
        
        if (isset($data['api_key'])) {
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