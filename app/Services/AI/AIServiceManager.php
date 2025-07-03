<?php

namespace App\Services\AI;

use App\Contracts\AI\AIProviderInterface;
use App\Contracts\AI\TokenManagerInterface;
use App\Contracts\AI\AIIntegrationInterface;
use Illuminate\Support\Facades\Log;
use Exception;

class AIServiceManager
{
    protected AIProviderInterface $provider;
    protected TokenManagerInterface $tokenManager;
    protected array $integrations = [];

    public function __construct(
        AIProviderInterface $provider,
        TokenManagerInterface $tokenManager
    ) {
        $this->provider = $provider;
        $this->tokenManager = $tokenManager;
    }

    /**
     * AI isteği gönder (token kontrolü ile)
     */
    public function sendRequest(
        array $messages, 
        string $tenantId, 
        string $moduleContext = null,
        array $options = []
    ): array {
        try {
            // Token miktarını hesapla
            $estimatedTokens = $this->estimateTokensForMessages($messages);
            
            // Token kontrolü
            if (!$this->tokenManager->canUseTokens($tenantId, $estimatedTokens)) {
                throw new Exception('Yetersiz token bakiyesi');
            }

            // AI isteği gönder
            $response = $this->provider->sendRequest($messages, $options);

            // Gerçek token kullanımını kaydet
            $actualTokens = $response['usage']['total_tokens'] ?? $estimatedTokens;
            $this->tokenManager->recordTokenUsage(
                $tenantId, 
                $actualTokens, 
                'ai_request', 
                $moduleContext,
                [
                    'provider' => $this->provider->getName(),
                    'model' => $options['model'] ?? 'default',
                    'estimated_tokens' => $estimatedTokens,
                    'actual_tokens' => $actualTokens
                ]
            );

            return [
                'success' => true,
                'data' => $response,
                'tokens_used' => $actualTokens,
                'remaining_tokens' => $this->tokenManager->getRemainingTokens($tenantId)
            ];

        } catch (Exception $e) {
            Log::error('AI request failed', [
                'tenant_id' => $tenantId,
                'module_context' => $moduleContext,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'tokens_used' => 0
            ];
        }
    }

    /**
     * Stream AI isteği gönder
     */
    public function sendStreamRequest(
        array $messages, 
        string $tenantId, 
        string $moduleContext = null,
        array $options = []
    ): \Generator {
        try {
            // Token kontrolü
            $estimatedTokens = $this->estimateTokensForMessages($messages);
            if (!$this->tokenManager->canUseTokens($tenantId, $estimatedTokens)) {
                throw new Exception('Yetersiz token bakiyesi');
            }

            $totalTokens = 0;
            foreach ($this->provider->sendStreamRequest($messages, $options) as $chunk) {
                if (isset($chunk['usage']['total_tokens'])) {
                    $totalTokens = $chunk['usage']['total_tokens'];
                }
                yield $chunk;
            }

            // Stream tamamlandıktan sonra token kullanımını kaydet
            $this->tokenManager->recordTokenUsage(
                $tenantId, 
                $totalTokens ?: $estimatedTokens, 
                'ai_stream_request', 
                $moduleContext,
                [
                    'provider' => $this->provider->getName(),
                    'estimated_tokens' => $estimatedTokens,
                    'actual_tokens' => $totalTokens
                ]
            );

        } catch (Exception $e) {
            Log::error('AI stream request failed', [
                'tenant_id' => $tenantId,
                'module_context' => $moduleContext,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Modül entegrasyonu kaydet
     */
    public function registerIntegration(string $module, AIIntegrationInterface $integration): void
    {
        $this->integrations[$module] = $integration;
    }

    /**
     * Modül entegrasyonunu getir
     */
    public function getIntegration(string $module): ?AIIntegrationInterface
    {
        return $this->integrations[$module] ?? null;
    }

    /**
     * Modül için AI action çalıştır
     */
    public function executeModuleAction(
        string $module, 
        string $action, 
        array $parameters, 
        string $tenantId
    ): array {
        try {
            $integration = $this->getIntegration($module);
            if (!$integration) {
                throw new Exception("Modül entegrasyonu bulunamadı: {$module}");
            }

            // Token miktarını hesapla
            $estimatedTokens = $integration->estimateTokens($action, $parameters);
            
            // Token kontrolü
            if (!$this->tokenManager->canUseTokens($tenantId, $estimatedTokens)) {
                throw new Exception('Yetersiz token bakiyesi');
            }

            // Action'ı çalıştır
            $result = $integration->executeAction($action, $parameters);
            
            // Token kullanımını kaydet
            $actualTokens = $result['tokens_used'] ?? $estimatedTokens;
            $this->tokenManager->recordTokenUsage(
                $tenantId, 
                $actualTokens, 
                "module_action_{$action}", 
                $module,
                [
                    'action' => $action,
                    'parameters' => $parameters,
                    'estimated_tokens' => $estimatedTokens,
                    'actual_tokens' => $actualTokens
                ]
            );

            return [
                'success' => true,
                'data' => $result,
                'tokens_used' => $actualTokens,
                'remaining_tokens' => $this->tokenManager->getRemainingTokens($tenantId)
            ];

        } catch (Exception $e) {
            Log::error('Module AI action failed', [
                'module' => $module,
                'action' => $action,
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'tokens_used' => 0
            ];
        }
    }

    /**
     * Mesajlar için token miktarını tahmin et
     */
    protected function estimateTokensForMessages(array $messages): int
    {
        $totalText = '';
        foreach ($messages as $message) {
            $totalText .= $message['content'] ?? '';
        }
        
        return $this->provider->calculateTokens($totalText);
    }

    /**
     * Token durumu getir
     */
    public function getTokenStatus(string $tenantId): array
    {
        return [
            'remaining_tokens' => $this->tokenManager->getRemainingTokens($tenantId),
            'total_tokens' => $this->tokenManager->getMonthlyLimit($tenantId),
            'daily_usage' => $this->tokenManager->getDailyUsage($tenantId),
            'monthly_usage' => $this->tokenManager->getMonthlyUsage($tenantId),
            'provider' => $this->provider->getName(),
            'provider_active' => $this->provider->isActive()
        ];
    }

    /**
     * Kayıtlı entegrasyonları listele
     */
    public function getRegisteredIntegrations(): array
    {
        $integrations = [];
        foreach ($this->integrations as $module => $integration) {
            $integrations[$module] = [
                'name' => $integration->getName(),
                'actions' => $integration->getSupportedActions(),
                'active' => $integration->isActive(),
                'status' => $integration->isActive() ? 'active' : 'inactive'
            ];
        }
        return $integrations;
    }
}