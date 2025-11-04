<?php

namespace App\Services\ConversationNodes\Common;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Collect Data Node
 *
 * Extracts and validates data from user message
 * Stores data in conversation context
 */
class CollectDataNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        $dataType = $this->getConfig('data_type', 'phone');
        $contextKey = $this->getConfig('context_key', $dataType);
        $required = $this->getConfig('required', true);
        $validationRegex = $this->getConfig('validation_regex');

        // Extract data based on type
        $extractedData = $this->extractData($dataType, $userMessage, $validationRegex);

        // If required and not found
        if ($required && empty($extractedData)) {
            $retryPrompt = $this->getConfig('retry_prompt', "Lütfen geçerli bir {$dataType} giriniz.");

            return $this->success(
                $retryPrompt,
                ['data_extracted' => false, 'retry' => true],
                $this->getConfig('retry_node')
            );
        }

        // Save to context
        if (!empty($extractedData)) {
            $conversation->addToContext($contextKey, $extractedData);
        }

        $this->log('info', 'Data collected', [
            'conversation_id' => $conversation->id,
            'data_type' => $dataType,
            'context_key' => $contextKey,
            'extracted' => !empty($extractedData),
        ]);

        return $this->success(
            null,
            [
                'data_extracted' => !empty($extractedData),
                $contextKey => $extractedData,
            ],
            $this->getConfig('next_node')
        );
    }

    protected function extractData(string $dataType, string $message, ?string $customRegex = null): ?string
    {
        // Use custom regex if provided
        if ($customRegex) {
            preg_match($customRegex, $message, $matches);
            return $matches[1] ?? $matches[0] ?? null;
        }

        // Built-in data type extractors
        return match ($dataType) {
            'phone' => $this->extractPhone($message),
            'email' => $this->extractEmail($message),
            'number' => $this->extractNumber($message),
            'text' => trim($message), // Any text
            default => null,
        };
    }

    protected function extractPhone(string $message): ?string
    {
        // Turkish phone number patterns
        $patterns = [
            '/(\+90|0)?[\s\(\)\-]*([5][0-9]{2})[\s\(\)\-]*([0-9]{3})[\s\(\)\-]*([0-9]{2})[\s\(\)\-]*([0-9]{2})/',
            '/([5][0-9]{9})/', // 10 digit mobile
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                // Clean and format
                $phone = preg_replace('/[^0-9]/', '', $matches[0]);
                return $phone;
            }
        }

        return null;
    }

    protected function extractEmail(string $message): ?string
    {
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $message, $matches)) {
            return $matches[0];
        }
        return null;
    }

    protected function extractNumber(string $message): ?string
    {
        if (preg_match('/\d+/', $message, $matches)) {
            return $matches[0];
        }
        return null;
    }

    public function validate(): bool
    {
        return !empty($this->getConfig('data_type'));
    }

    public static function getType(): string
    {
        return 'collect_data';
    }

    public static function getName(): string
    {
        return 'Veri Topla';
    }

    public static function getDescription(): string
    {
        return 'Kullanıcıdan veri al (telefon, email vb.)';
    }

    public static function getConfigSchema(): array
    {
        return [
            'data_type' => [
                'type' => 'select',
                'label' => 'Veri Türü',
                'options' => [
                    'phone' => 'Telefon',
                    'email' => 'Email',
                    'number' => 'Sayı',
                    'text' => 'Metin',
                ],
                'required' => true,
            ],
            'context_key' => [
                'type' => 'text',
                'label' => 'Context Anahtarı',
                'help' => 'Verinin saklanacağı anahtar (örn: customer_phone)',
            ],
            'required' => [
                'type' => 'boolean',
                'label' => 'Zorunlu',
                'default' => true,
            ],
            'validation_regex' => [
                'type' => 'text',
                'label' => 'Özel Regex',
                'help' => 'Özel doğrulama regex\'i (opsiyonel)',
            ],
            'retry_prompt' => [
                'type' => 'text',
                'label' => 'Tekrar Dene Mesajı',
                'default' => 'Lütfen geçerli bir değer giriniz.',
            ],
            'retry_node' => [
                'type' => 'node_select',
                'label' => 'Tekrar Dene Node',
            ],
            'next_node' => [
                'type' => 'node_select',
                'label' => 'Sonraki Node',
            ],
        ];
    }

    public static function getInputs(): array
    {
        return [
            ['id' => 'input_1', 'label' => 'Giriş'],
        ];
    }

    public static function getOutputs(): array
    {
        return [
            ['id' => 'output_success', 'label' => 'Başarılı'],
            ['id' => 'output_retry', 'label' => 'Tekrar Dene'],
        ];
    }

    public static function getCategory(): string
    {
        return 'data';
    }

    public static function getIcon(): string
    {
        return 'ti ti-database';
    }
}
