<?php

namespace App\Services\ConversationNodes\Shop;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Currency Converter Node
 *
 * Converts prices between TL/USD/EUR
 * Uses tenant-specific exchange rates from shop settings
 */
class CurrencyConverterNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        try {
            // Get products from context (from previous product search)
            $products = $conversation->context_data['searched_products'] ?? [];

            if (empty($products)) {
                $this->log('warning', 'No products in context for currency conversion', [
                    'conversation_id' => $conversation->id,
                ]);

                $nextNode = $this->getConfig('next_node');
                return $this->success(null, [], $nextNode);
            }

            // Detect requested currency
            $targetCurrency = $this->detectCurrency($userMessage);

            // Get exchange rates from tenant settings
            $rates = $this->getExchangeRates();

            // Convert prices
            $convertedProducts = $this->convertProductPrices($products, $targetCurrency, $rates);

            // Store in context
            $conversation->addToContext('converted_products', $convertedProducts);
            $conversation->addToContext('target_currency', $targetCurrency);

            $this->log('info', 'Currency conversion completed', [
                'conversation_id' => $conversation->id,
                'target_currency' => $targetCurrency,
                'product_count' => count($convertedProducts),
            ]);

            $nextNode = $this->getConfig('next_node');

            return $this->success(
                null,
                [
                    'products' => $convertedProducts,
                    'target_currency' => $targetCurrency,
                    'exchange_rates' => $rates,
                ],
                $nextNode
            );

        } catch (\Exception $e) {
            $this->log('error', 'Currency conversion failed', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);

            return $this->failure('Currency conversion failed: ' . $e->getMessage());
        }
    }

    protected function detectCurrency(string $message): string
    {
        $message = mb_strtolower($message);

        if (preg_match('/(dolar|usd|\$)/i', $message)) {
            return 'USD';
        }

        if (preg_match('/(euro|eur|€)/i', $message)) {
            return 'EUR';
        }

        return 'TL'; // Default
    }

    protected function getExchangeRates(): array
    {
        // Get rates from shop settings (tenant-specific)
        try {
            return [
                'USD' => (float) settings()->get('shop.exchange_rate_usd', 30.0),
                'EUR' => (float) settings()->get('shop.exchange_rate_eur', 32.0),
            ];
        } catch (\Exception $e) {
            // Fallback rates
            return [
                'USD' => 30.0,
                'EUR' => 32.0,
            ];
        }
    }

    protected function convertProductPrices(array $products, string $targetCurrency, array $rates): array
    {
        if ($targetCurrency === 'TL') {
            return $products; // No conversion needed
        }

        $rate = $rates[$targetCurrency] ?? 1;

        return array_map(function($product) use ($targetCurrency, $rate) {
            $convertedPrice = $product['base_price'] / $rate;

            $product['converted_price'] = $convertedPrice;
            $product['converted_price_formatted'] = number_format($convertedPrice, 2, ',', '.') . ' ' . $targetCurrency;
            $product['original_price'] = $product['base_price'];
            $product['original_currency'] = 'TL';
            $product['target_currency'] = $targetCurrency;
            $product['exchange_rate'] = $rate;

            return $product;
        }, $products);
    }

    public function validate(): bool
    {
        return true;
    }

    public static function getType(): string
    {
        return 'currency_converter';
    }

    public static function getName(): string
    {
        return 'Döviz Çevirici';
    }

    public static function getDescription(): string
    {
        return 'Fiyatları TL/USD/EUR arasında çevirir (tenant kuru)';
    }

    public static function getConfigSchema(): array
    {
        return [
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
            ['id' => 'output_1', 'label' => 'Çevrildi'],
        ];
    }

    public static function getCategory(): string
    {
        return 'shop';
    }

    public static function getIcon(): string
    {
        return 'ti ti-currency-dollar';
    }
}
