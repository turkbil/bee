<?php

namespace App\Services\ConversationNodes\Common;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Link Generator Node
 *
 * Converts custom link format to actual URLs
 * Example: [LINK:shop:product:transpalet-manuel] → /shop/product/transpalet-manuel
 */
class LinkGeneratorNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        // Get AI response from context (should be set by previous AI node)
        $aiResponse = $conversation->context_data['last_ai_response'] ?? '';

        if (empty($aiResponse)) {
            $this->log('warning', 'No AI response found in context', [
                'conversation_id' => $conversation->id,
            ]);

            $nextNode = $this->getConfig('next_node');
            return $this->success(null, [], $nextNode);
        }

        // Convert custom links to URLs
        $processedResponse = $this->processLinks($aiResponse);

        // Update context with processed response
        $conversation->addToContext('processed_ai_response', $processedResponse);

        $this->log('info', 'Links processed', [
            'conversation_id' => $conversation->id,
            'links_found' => substr_count($aiResponse, '[LINK:'),
        ]);

        $nextNode = $this->getConfig('next_node');

        return $this->success(
            null,
            ['processed_response' => $processedResponse],
            $nextNode
        );
    }

    protected function processLinks(string $content): string
    {
        // Pattern: [LINK:module:type:identifier]
        // Examples:
        // [LINK:shop:product:transpalet-2-ton] → /shop/product/transpalet-2-ton
        // [LINK:shop:category:forklift] → /shop/category/forklift
        // [LINK:page:iletisim] → /page/iletisim

        $pattern = '/\[LINK:([a-z]+):([a-z]+):([a-z0-9\-]+)\]/i';

        return preg_replace_callback($pattern, function ($matches) {
            [$fullMatch, $module, $type, $identifier] = $matches;

            $baseUrl = $this->getConfig('base_url', config('app.url'));

            // Build URL based on module and type
            $url = match ($module) {
                'shop' => $this->buildShopUrl($type, $identifier),
                'page' => $this->buildPageUrl($identifier),
                default => '#',
            };

            // Return as HTML link
            $linkText = ucfirst(str_replace('-', ' ', $identifier));
            return sprintf('<a href="%s" target="_blank" class="text-primary hover:underline">%s</a>', $url, $linkText);
        }, $content);
    }

    protected function buildShopUrl(string $type, string $identifier): string
    {
        return match ($type) {
            'product' => "/shop/product/{$identifier}",
            'category' => "/shop/category/{$identifier}",
            'brand' => "/shop/brand/{$identifier}",
            default => "/shop/{$identifier}",
        };
    }

    protected function buildPageUrl(string $identifier): string
    {
        return "/page/{$identifier}";
    }

    public function validate(): bool
    {
        return true;
    }

    public static function getType(): string
    {
        return 'link_generator';
    }

    public static function getName(): string
    {
        return 'Link Oluştur';
    }

    public static function getDescription(): string
    {
        return 'Custom link formatını gerçek URL\'lere çevirir';
    }

    public static function getConfigSchema(): array
    {
        return [
            'base_url' => [
                'type' => 'text',
                'label' => 'Base URL',
                'placeholder' => 'https://ixtif.com',
                'help' => 'Site ana URL\'i (boş bırakılırsa config\'ten alınır)',
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
            ['id' => 'input_1', 'label' => 'Tetikleyici'],
        ];
    }

    public static function getOutputs(): array
    {
        return [
            ['id' => 'output_1', 'label' => 'Linkler Oluşturuldu'],
        ];
    }

    public static function getCategory(): string
    {
        return 'output';
    }

    public static function getIcon(): string
    {
        return 'ti ti-link';
    }
}
