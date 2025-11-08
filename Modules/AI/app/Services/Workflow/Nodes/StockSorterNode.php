<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

use Illuminate\Support\Facades\Log;

class StockSorterNode extends BaseNode
{
    public function execute(array $context): array
    {
        $products = collect($context['products'] ?? []);

        $excludeOutOfStock = $this->getConfig('exclude_out_of_stock', true);
        $highStockThreshold = $this->getConfig('high_stock_threshold', 10);

        if ($excludeOutOfStock && $products->isNotEmpty()) {
            $products = $products->filter(fn($p) => isset($p->current_stock) && $p->current_stock > 0);
        }

        // Sort by stock level (if current_stock attribute exists)
        if ($products->isNotEmpty() && isset($products->first()->current_stock)) {
            $products = $products->sortByDesc('current_stock');
        }

        $highStockCount = $products->filter(fn($p) => isset($p->current_stock) && $p->current_stock >= $highStockThreshold)->count();

        Log::info('📊 StockSorterNode', [
            'total' => $products->count(),
            'high_stock' => $highStockCount
        ]);

        // Return only new keys (FlowExecutor will merge with context)
        return [
            'products' => $products,
            'high_stock_count' => $highStockCount
        ];
    }
}
