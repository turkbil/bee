<?php

namespace App\Services\AI\Integration;

use App\Contracts\AI\AIIntegrationInterface;
use App\Services\AI\AIServiceManager;
use Illuminate\Support\Facades\Log;
use Exception;

abstract class BaseModuleAIIntegration implements AIIntegrationInterface
{
    protected AIServiceManager $aiServiceManager;
    protected array $configuration = [];
    protected array $supportedActions = [];

    public function __construct(AIServiceManager $aiServiceManager)
    {
        $this->aiServiceManager = $aiServiceManager;
        $this->initializeConfiguration();
        $this->registerActions();
    }

    /**
     * Modül konfigürasyonunu başlat
     */
    abstract protected function initializeConfiguration(): void;

    /**
     * Desteklenen action'ları kaydet
     */
    abstract protected function registerActions(): void;

    /**
     * Entegrasyon adını döndür
     */
    abstract public function getName(): string;

    public function getSupportedActions(): array
    {
        return array_keys($this->supportedActions);
    }

    public function executeAction(string $action, array $parameters = []): array
    {
        try {
            // Action'ın desteklenip desteklenmediğini kontrol et
            if (!isset($this->supportedActions[$action])) {
                throw new Exception("Desteklenmeyen action: {$action}");
            }

            // Parametreleri doğrula
            if (!$this->validateActionParameters($action, $parameters)) {
                throw new Exception("Geçersiz parametreler: {$action}");
            }

            // Action'ı çalıştır
            $actionConfig = $this->supportedActions[$action];
            $method = $actionConfig['method'] ?? null;

            if (!$method || !method_exists($this, $method)) {
                throw new Exception("Action metodu bulunamadı: {$action}");
            }

            Log::info('AI action çalıştırılıyor', [
                'module' => $this->getName(),
                'action' => $action,
                'method' => $method,
                'parameters' => array_keys($parameters)
            ]);

            // Metodu çalıştır
            $result = $this->$method($parameters);

            return [
                'success' => true,
                'data' => $result,
                'action' => $action,
                'module' => $this->getName(),
                'tokens_used' => $result['tokens_used'] ?? 0
            ];

        } catch (Exception $e) {
            Log::error('AI action hatası', [
                'module' => $this->getName(),
                'action' => $action,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'action' => $action,
                'module' => $this->getName(),
                'tokens_used' => 0
            ];
        }
    }

    public function estimateTokens(string $action, array $parameters = []): int
    {
        try {
            if (!isset($this->supportedActions[$action])) {
                return 0;
            }

            $actionConfig = $this->supportedActions[$action];
            $estimateMethod = $actionConfig['estimate_method'] ?? 'defaultEstimateTokens';

            if (method_exists($this, $estimateMethod)) {
                return $this->$estimateMethod($parameters);
            }

            return $this->defaultEstimateTokens($parameters);

        } catch (Exception $e) {
            Log::error('Token tahmini hatası', [
                'module' => $this->getName(),
                'action' => $action,
                'error' => $e->getMessage()
            ]);

            return 100; // Varsayılan token miktarı
        }
    }

    public function validateActionParameters(string $action, array $parameters): bool
    {
        try {
            if (!isset($this->supportedActions[$action])) {
                return false;
            }

            $actionConfig = $this->supportedActions[$action];
            $requiredParams = $actionConfig['required_params'] ?? [];

            // Gerekli parametrelerin varlığını kontrol et
            foreach ($requiredParams as $param) {
                if (!isset($parameters[$param])) {
                    Log::warning('Eksik parametre', [
                        'module' => $this->getName(),
                        'action' => $action,
                        'missing_param' => $param
                    ]);
                    return false;
                }
            }

            // Özel validasyon metodu varsa çalıştır
            $validateMethod = $actionConfig['validate_method'] ?? null;
            if ($validateMethod && method_exists($this, $validateMethod)) {
                return $this->$validateMethod($parameters);
            }

            return true;

        } catch (Exception $e) {
            Log::error('Parametre validasyon hatası', [
                'module' => $this->getName(),
                'action' => $action,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    public function isActive(): bool
    {
        return $this->configuration['active'] ?? true;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * AI isteği gönder
     */
    protected function sendAIRequest(array $messages, string $tenantId, array $options = []): array
    {
        return $this->aiServiceManager->sendRequest(
            $messages, 
            $tenantId, 
            $this->getName(),
            $options
        );
    }

    /**
     * Varsayılan token tahmini
     */
    protected function defaultEstimateTokens(array $parameters): int
    {
        $textLength = 0;
        
        foreach ($parameters as $value) {
            if (is_string($value)) {
                $textLength += strlen($value);
            } elseif (is_array($value)) {
                $textLength += strlen(json_encode($value));
            }
        }

        // Basit token hesaplama: 4 karakter = 1 token
        return (int) ceil($textLength / 4) + 50; // +50 sistem mesajı için
    }

    /**
     * Prompt şablonu oluştur
     */
    protected function buildPrompt(string $template, array $variables = []): string
    {
        $prompt = $template;
        
        foreach ($variables as $key => $value) {
            $prompt = str_replace("{{$key}}", $value, $prompt);
        }
        
        return $prompt;
    }

    /**
     * Modül specific token hesaplama
     */
    protected function calculateModuleTokens(string $content): int
    {
        return (int) ceil(strlen($content) / 4);
    }

    /**
     * Action konfigürasyonu kaydet
     */
    protected function addAction(string $name, array $config): void
    {
        $this->supportedActions[$name] = array_merge([
            'method' => null,
            'required_params' => [],
            'estimate_method' => 'defaultEstimateTokens',
            'validate_method' => null,
            'description' => '',
            'category' => 'general'
        ], $config);
    }

    /**
     * Tenant ID'yi context'ten al
     */
    protected function getTenantId(): string
    {
        // Multi-tenancy context'inden tenant ID'yi al
        return tenant('id') ?: 'default';
    }

    /**
     * Yanıtı formatla
     */
    protected function formatResponse(array $data, int $tokensUsed = 0): array
    {
        return [
            'content' => $data['content'] ?? '',
            'metadata' => $data['metadata'] ?? [],
            'tokens_used' => $tokensUsed,
            'timestamp' => now()->toISOString(),
            'module' => $this->getName()
        ];
    }
}