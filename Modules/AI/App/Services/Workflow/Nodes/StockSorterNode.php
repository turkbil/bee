<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

use Illuminate\Support\Facades\Log;

class StockSorterNode extends BaseNode
{
    /**
     * 🎯 Profesyonel Ürün Sıralama Sistemi
     *
     * Sıralama Kriterleri (Önem Sırasına Göre):
     * 1. 🥇 Vitrin Ürünleri: Homepage'de gösterilen ürünler önce (homepage = 1)
     * 2. 🥈 Stok Durumu: Stokta olan ürünler önce (current_stock > 0)
     * 3. 🥉 Kategori Sırası: Sort order (sort_order ASC)
     * 4. 💰 Fiyat: Fiyatlı ürünler önce, sonra en ucuzdan pahalıya (base_price ASC)
     */
    public function execute(array $context): array
    {
        $products = collect($context['products'] ?? []);

        $excludeOutOfStock = $this->getConfig('exclude_out_of_stock', false);
        $highStockThreshold = $this->getConfig('high_stock_threshold', 10);

        // 1. Stok dışı ürünleri filtrele (config'e göre)
        if ($excludeOutOfStock && $products->isNotEmpty()) {
            $originalCount = $products->count();
            $products = $products->filter(fn($p) => isset($p->current_stock) && $p->current_stock > 0);
            Log::info('📊 StockSorterNode: Stok filtresi', [
                'original' => $originalCount,
                'after_filter' => $products->count(),
                'excluded' => $originalCount - $products->count()
            ]);
        }

        // 2. Profesyonel sıralama uygula
        // Sıralama: Homepage > Stok > Kategori Sorting > Fiyat (en ucuz)
        if ($products->isNotEmpty()) {
            $products = $products->sort(function($a, $b) {
                // 🥇 1. ÖNCE: Vitrin Ürünleri (Homepage = 1 olanlar önce)
                $aHomepage = $a->homepage ?? 0;
                $bHomepage = $b->homepage ?? 0;

                if ($aHomepage !== $bHomepage) {
                    return $bHomepage <=> $aHomepage; // 1 önce, 0 sonra
                }

                // 🥈 2. SONRA: Stok Durumu (Stokta olan önce)
                $aInStock = ($a->current_stock ?? 0) > 0;
                $bInStock = ($b->current_stock ?? 0) > 0;

                if ($aInStock !== $bInStock) {
                    return $bInStock <=> $aInStock; // Stokta olan önce
                }

                // 🥉 3. Kategori İçi Sıra (Sort Order)
                $aSortOrder = $a->sort_order ?? 9999;
                $bSortOrder = $b->sort_order ?? 9999;

                if ($aSortOrder !== $bSortOrder) {
                    return $aSortOrder <=> $bSortOrder; // Küçük sayı önce
                }

                // 💰 4. Fiyat (En ucuz önce - fiyatlı ürünler için)
                $aPrice = $a->base_price ?? 0;
                $bPrice = $b->base_price ?? 0;

                // İkisi de fiyatlı → en ucuz önce
                if ($aPrice > 0 && $bPrice > 0) {
                    return $aPrice <=> $bPrice;
                }

                // Biri fiyatlı biri değil → fiyatlı önce
                if ($aPrice > 0 && $bPrice == 0) {
                    return -1;
                }
                if ($aPrice == 0 && $bPrice > 0) {
                    return 1;
                }

                return 0; // Eşit
            })->values(); // Re-index array
        }

        $highStockCount = $products->filter(fn($p) => isset($p->current_stock) && $p->current_stock >= $highStockThreshold)->count();
        $inStockCount = $products->filter(fn($p) => isset($p->current_stock) && $p->current_stock > 0)->count();
        $withPriceCount = $products->filter(fn($p) => !($p->price_on_request ?? true))->count();

        Log::info('📊 StockSorterNode: Profesyonel sıralama uygulandı', [
            'total' => $products->count(),
            'with_price' => $withPriceCount,
            'in_stock' => $inStockCount,
            'high_stock' => $highStockCount,
            'sorting_applied' => '6-tier professional sorting'
        ]);

        // Return only new keys (FlowExecutor will merge with context)
        return [
            'products' => $products,
            'high_stock_count' => $highStockCount,
            'in_stock_count' => $inStockCount,
            'with_price_count' => $withPriceCount
        ];
    }
}
