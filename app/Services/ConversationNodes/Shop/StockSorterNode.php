<?php

namespace App\Services\ConversationNodes\Shop;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Stock Sorter Node
 *
 * Sorts products by stock priority:
 * 1. Featured products (homepage)
 * 2. High stock (> threshold)
 * 3. Normal stock
 * 4. Low stock (optional: exclude out of stock)
 */
class StockSorterNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        try {
            // Get products from context
            $products = $conversation->context_data['searched_products'] ?? [];

            if (empty($products)) {
                $this->log('warning', 'No products to sort', [
                    'conversation_id' => $conversation->id,
                ]);

                $nextNode = $this->getConfig('next_node');
                return $this->success(null, [], $nextNode);
            }

            // Get config
            $highStockThreshold = $this->getConfig('high_stock_threshold', 10);
            $excludeOutOfStock = $this->getConfig('exclude_out_of_stock', false);

            // Sort products
            $sortedProducts = $this->sortByStock($products, $highStockThreshold, $excludeOutOfStock);

            // Store in context
            $conversation->addToContext('sorted_products', $sortedProducts);

            $this->log('info', 'Products sorted by stock', [
                'conversation_id' => $conversation->id,
                'original_count' => count($products),
                'sorted_count' => count($sortedProducts),
            ]);

            $nextNode = $this->getConfig('next_node');

            return $this->success(
                null,
                ['products' => $sortedProducts],
                $nextNode
            );

        } catch (\Exception $e) {
            $this->log('error', 'Stock sorting failed', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);

            return $this->failure('Stock sorting failed: ' . $e->getMessage());
        }
    }

    protected function sortByStock(array $products, int $highStockThreshold, bool $excludeOutOfStock): array
    {
        // Filter out of stock if needed
        if ($excludeOutOfStock) {
            $products = array_filter($products, function($product) {
                return ($product['stock'] ?? 0) > 0;
            });
        }

        // Sort by priority
        usort($products, function($a, $b) use ($highStockThreshold) {
            // 1. Featured first
            if (($a['is_featured'] ?? false) && !($b['is_featured'] ?? false)) return -1;
            if (!($a['is_featured'] ?? false) && ($b['is_featured'] ?? false)) return 1;

            // 2. High stock next
            $aHighStock = ($a['stock'] ?? 0) > $highStockThreshold;
            $bHighStock = ($b['stock'] ?? 0) > $highStockThreshold;
            if ($aHighStock && !$bHighStock) return -1;
            if (!$aHighStock && $bHighStock) return 1;

            // 3. Stock amount
            return ($b['stock'] ?? 0) - ($a['stock'] ?? 0);
        });

        return array_values($products);
    }

    public function validate(): bool
    {
        return true;
    }

    public static function getType(): string
    {
        return 'stock_sorter';
    }

    public static function getName(): string
    {
        return 'Stok Sırala';
    }

    public static function getDescription(): string
    {
        return 'Ürünleri stok durumuna göre sıralar (Featured → Yüksek Stok → Normal)';
    }

    public static function getConfigSchema(): array
    {
        return [
            'high_stock_threshold' => [
                'type' => 'number',
                'label' => 'Yüksek Stok Eşiği',
                'min' => 1,
                'max' => 100,
                'default' => 10,
                'help' => 'Bu sayının üstündeki stoklar "yüksek" sayılır',
            ],
            'exclude_out_of_stock' => [
                'type' => 'boolean',
                'label' => 'Stokta Olmayanları Hariç Tut',
                'default' => false,
                'help' => 'Stok 0 olan ürünleri listeden çıkar',
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
            ['id' => 'output_1', 'label' => 'Sıralandı'],
        ];
    }

    public static function getCategory(): string
    {
        return 'shop';
    }

    public static function getIcon(): string
    {
        return 'ti ti-sort-ascending-numbers';
    }
}
