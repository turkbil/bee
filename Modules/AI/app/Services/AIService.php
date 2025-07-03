<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Services\DeepSeekService;
use Modules\AI\App\Services\ConversationService;
use Modules\AI\App\Services\PromptService;
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
     * Gizli promptları birleştirerek tam sistem promptunu oluşturur
     *
     * @param string $userPrompt Kullanıcının seçtiği prompt
     * @return string Tam sistem promptu
     */
    public function buildFullSystemPrompt($userPrompt = '')
    {
        $systemPrompts = [];
        
        // 1. Gizli sistem promptu (her zaman önce)
        $hiddenSystemPrompt = \Modules\AI\App\Models\Prompt::getHiddenSystem();
        if ($hiddenSystemPrompt) {
            $systemPrompts[] = $hiddenSystemPrompt->content;
        }
        
        // 2. Kullanıcı tarafından seçilen prompt
        if (!empty($userPrompt)) {
            $systemPrompts[] = $userPrompt;
        }
        
        // 3. Gizli bilgi tabanı (AI bilir ama bahsetmez)
        $secretKnowledge = \Modules\AI\App\Models\Prompt::getSecretKnowledge();
        if ($secretKnowledge) {
            $systemPrompts[] = "GIZLI BILGI TABANI (Kendiliğinden bahsetme, sadece gerektiğinde kullan):\n" . $secretKnowledge->content;
        }
        
        // 4. Şartlı yanıtlar (sadece sorulunca anlatılır)
        $conditionalResponses = \Modules\AI\App\Models\Prompt::getConditional();
        if ($conditionalResponses) {
            $systemPrompts[] = "ŞARTLI BILGILER (Sadece kullanıcı sorduğunda anlatılır):\n" . $conditionalResponses->content;
        }
        
        return implode("\n\n---\n\n", array_filter($systemPrompts));
    }
}