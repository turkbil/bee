<?php

namespace App\Services\ConversationNodes\Common;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;
use Modules\Shop\App\Models\ShopCurrency;

/**
 * Context Builder Node
 *
 * Builds AI context from conversation data, tenant directives, and custom data
 * Prepares comprehensive context for AI response generation
 */
class ContextBuilderNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        $includeDirectives = $this->getConfig('include_tenant_directives', true);
        $includeHistory = $this->getConfig('include_conversation_history', true);
        $includeContext = $this->getConfig('include_conversation_context', true);

        $contextData = [];

        // 1. Tenant Directives
        if ($includeDirectives) {
            $directives = \App\Models\AITenantDirective::getAllForTenant($conversation->tenant_id);
            $contextData['tenant_directives'] = $directives;
        }

        // 2. Conversation History
        if ($includeHistory) {
            $historyLimit = $this->getConfig('history_limit', 10);
            $history = $this->getConversationHistory($conversation, $historyLimit);
            $contextData['conversation_history'] = $history;
        }

        // 3. Conversation Context Data
        if ($includeContext && !empty($conversation->context_data)) {
            $contextData['conversation_context'] = $conversation->context_data;
        }

        // 4. Brand/Tenant Info
        $contextData['tenant_info'] = [
            'tenant_id' => $conversation->tenant_id,
            'locale' => app()->getLocale(),
        ];

        // 5. Contact Information (from settings)
        $contextData['contact'] = $this->getContactInformation();

        // 6. AI Settings (from settings)
        $contextData['ai_settings'] = $this->getAISettings();

        // 7. Format product prices if products exist in context
        if (!empty($contextData['conversation_context']['products'])) {
            $contextData['conversation_context']['products'] = $this->formatProductPrices(
                $contextData['conversation_context']['products']
            );
        }

        // Get next node
        $nextNode = $this->getConfig('next_node');

        $this->log('info', 'Context builder node executed', [
            'conversation_id' => $conversation->id,
            'context_keys' => array_keys($contextData),
            'history_count' => count($contextData['conversation_history'] ?? []),
            'has_contact' => !empty($contextData['contact']),
            'has_ai_settings' => !empty($contextData['ai_settings']),
            'products_count' => count($contextData['conversation_context']['products'] ?? []),
        ]);

        return $this->success(
            null, // Context builder doesn't generate prompt, just prepares data
            $contextData,
            $nextNode
        );
    }

    protected function getConversationHistory(AIConversation $conversation, int $limit): array
    {
        try {
            return $conversation->messages()
                ->orderBy('created_at', 'asc')
                ->limit($limit)
                ->get()
                ->map(fn($msg) => [
                    'role' => $msg->role,
                    'content' => $msg->content,
                    'created_at' => $msg->created_at->toIso8601String(),
                ])
                ->toArray();
        } catch (\Exception $e) {
            $this->log('warning', 'Failed to load conversation history', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get contact information from settings
     */
    protected function getContactInformation(): array
    {
        try {
            $whatsapp = settings('contact_whatsapp_1');
            $phone = settings('contact_phone_1');
            $email = settings('contact_email_1');

            return [
                'whatsapp' => $whatsapp,
                'whatsapp_link' => $whatsapp ? $this->generateWhatsAppLink($whatsapp) : null,
                'phone' => $phone,
                'email' => $email,
            ];
        } catch (\Exception $e) {
            $this->log('warning', 'Failed to load contact information', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get AI settings from settings
     */
    protected function getAISettings(): array
    {
        try {
            return [
                'assistant_name' => settings('ai_assistant_name', 'AI Asistan'),
                'response_tone' => settings('ai_response_tone', 'friendly'),
                'use_emojis' => settings('ai_use_emojis', 'moderate'),
                'response_length' => settings('ai_response_length', 'medium'),
                'sales_approach' => settings('ai_sales_approach', 'consultative'),
            ];
        } catch (\Exception $e) {
            $this->log('warning', 'Failed to load AI settings', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Generate WhatsApp link from phone number
     */
    protected function generateWhatsAppLink(string $phoneNumber): string
    {
        // Format: 0534 515 26 26 → 905345152626
        $clean = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Başında 0 varsa 90 ile değiştir
        if (substr($clean, 0, 1) === '0') {
            $clean = '90' . substr($clean, 1);
        }

        return "https://wa.me/{$clean}";
    }

    /**
     * Format product prices with currency information
     */
    protected function formatProductPrices(array $products): array
    {
        // Collect unique currency codes
        $currencyCodes = array_unique(array_column($products, 'currency'));

        // Fetch all currencies in one query (performance optimization)
        $currencies = ShopCurrency::whereIn('code', $currencyCodes)
            ->get()
            ->keyBy('code');

        // Format each product
        return array_map(function($product) use ($currencies) {
            // Skip if already has formatted_price
            if (isset($product['formatted_price'])) {
                return $product;
            }

            // Format price if currency exists
            if (isset($product['base_price']) && isset($product['currency']) && isset($currencies[$product['currency']])) {
                $currency = $currencies[$product['currency']];
                $product['currency_symbol'] = $currency->symbol;
                $product['currency_format'] = $currency->format;
                $product['decimal_places'] = $currency->decimal_places;
                $product['formatted_price'] = $this->formatPrice($product['base_price'], $currency);
            }

            return $product;
        }, $products);
    }

    /**
     * Format price with currency
     */
    protected function formatPrice(float $price, $currency): string
    {
        $formatted = number_format(
            $price,
            $currency->decimal_places ?? 0,
            ',',
            '.'
        );

        if ($currency->format === 'symbol_before') {
            return $currency->symbol . $formatted;
        }

        return $formatted . ' ' . $currency->symbol;
    }

    public function validate(): bool
    {
        return true; // Context builder has no required config
    }

    public static function getType(): string
    {
        return 'context_builder';
    }

    public static function getName(): string
    {
        return 'Context Hazırla';
    }

    public static function getDescription(): string
    {
        return 'AI için kapsamlı context bilgisi hazırlar';
    }

    public static function getConfigSchema(): array
    {
        return [
            'include_tenant_directives' => [
                'type' => 'boolean',
                'label' => 'Tenant Direktiflerini Ekle',
                'default' => true,
                'help' => 'Tenant\'a özel talimatları context\'e ekle',
            ],
            'include_conversation_history' => [
                'type' => 'boolean',
                'label' => 'Konuşma Geçmişini Ekle',
                'default' => true,
                'help' => 'Önceki mesajları context\'e ekle',
            ],
            'history_limit' => [
                'type' => 'number',
                'label' => 'Geçmiş Limiti',
                'min' => 1,
                'max' => 50,
                'default' => 10,
                'help' => 'Kaç mesaj yüklensin',
                'depends_on' => 'include_conversation_history',
            ],
            'include_conversation_context' => [
                'type' => 'boolean',
                'label' => 'Conversation Context Ekle',
                'default' => true,
                'help' => 'Conversation state\'teki context_data\'yı ekle',
            ],
            'next_node' => [
                'type' => 'node_select',
                'label' => 'Sonraki Node',
                'required' => false,
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
            ['id' => 'output_1', 'label' => 'Context Hazır'],
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
