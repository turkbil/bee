<?php

namespace App\Services\ConversationNodes\Common;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;
use Illuminate\Support\Facades\Http;

/**
 * Webhook Node
 *
 * Sends HTTP request to external webhook URL
 * Useful for integrations with external services
 */
class WebhookNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        $url = $this->getConfig('url');
        $method = $this->getConfig('method', 'POST');
        $headers = $this->getConfig('headers', []);
        $timeout = $this->getConfig('timeout', 10);

        if (empty($url)) {
            return $this->failure('Webhook URL is required');
        }

        // Prepare payload
        $payload = [
            'conversation_id' => $conversation->id,
            'tenant_id' => $conversation->tenant_id,
            'session_id' => $conversation->session_id,
            'user_message' => $userMessage,
            'context' => $conversation->context_data,
            'timestamp' => now()->toIso8601String(),
        ];

        // Merge with custom payload
        $customPayload = $this->getConfig('payload', []);
        $payload = array_merge($payload, $customPayload);

        try {
            // Send HTTP request
            $response = Http::timeout($timeout)
                ->withHeaders($headers)
                ->$method($url, $payload);

            $statusCode = $response->status();
            $responseBody = $response->body();

            $this->log('info', 'Webhook request sent', [
                'conversation_id' => $conversation->id,
                'url' => $url,
                'method' => $method,
                'status_code' => $statusCode,
                'response_length' => strlen($responseBody),
            ]);

            // Check if successful
            if ($response->successful()) {
                return $this->success(
                    null,
                    [
                        'webhook_success' => true,
                        'status_code' => $statusCode,
                        'response' => $responseBody,
                    ],
                    $this->getConfig('success_node')
                );
            } else {
                return $this->success(
                    null,
                    [
                        'webhook_success' => false,
                        'status_code' => $statusCode,
                        'error' => $responseBody,
                    ],
                    $this->getConfig('error_node')
                );
            }

        } catch (\Exception $e) {
            $this->log('error', 'Webhook request failed', [
                'conversation_id' => $conversation->id,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return $this->success(
                null,
                [
                    'webhook_success' => false,
                    'error' => $e->getMessage(),
                ],
                $this->getConfig('error_node')
            );
        }
    }

    public function validate(): bool
    {
        $url = $this->getConfig('url');
        return !empty($url) && filter_var($url, FILTER_VALIDATE_URL);
    }

    public static function getType(): string
    {
        return 'webhook';
    }

    public static function getName(): string
    {
        return 'Webhook';
    }

    public static function getDescription(): string
    {
        return 'Harici bir servise HTTP isteği gönder';
    }

    public static function getConfigSchema(): array
    {
        return [
            'url' => [
                'type' => 'text',
                'label' => 'Webhook URL',
                'placeholder' => 'https://example.com/webhook',
                'required' => true,
            ],
            'method' => [
                'type' => 'select',
                'label' => 'HTTP Method',
                'options' => [
                    'POST' => 'POST',
                    'GET' => 'GET',
                    'PUT' => 'PUT',
                    'PATCH' => 'PATCH',
                ],
                'default' => 'POST',
            ],
            'headers' => [
                'type' => 'json',
                'label' => 'Headers',
                'help' => 'JSON formatında header\'lar',
            ],
            'payload' => [
                'type' => 'json',
                'label' => 'Ekstra Payload',
                'help' => 'Otomatik payload\'a eklenecek özel alanlar',
            ],
            'timeout' => [
                'type' => 'number',
                'label' => 'Timeout (saniye)',
                'min' => 1,
                'max' => 30,
                'default' => 10,
            ],
            'success_node' => [
                'type' => 'node_select',
                'label' => 'Başarılı İse',
            ],
            'error_node' => [
                'type' => 'node_select',
                'label' => 'Hata İse',
            ],
        ];
    }

    public static function getInputs(): array
    {
        return [
            ['id' => 'input_1', 'label' => 'Tetikleyici'],
        ];
    }

    public static function getOutputs(): array
    {
        return [
            ['id' => 'output_success', 'label' => 'Başarılı'],
            ['id' => 'output_error', 'label' => 'Hata'],
        ];
    }

    public static function getCategory(): string
    {
        return 'integration';
    }

    public static function getIcon(): string
    {
        return 'ti ti-webhook';
    }
}
