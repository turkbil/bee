<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

use Illuminate\Support\Facades\Log;

class ContextBuilderNode extends BaseNode
{
    public function execute(array $context): array
    {
        $products = $context['products'] ?? collect();
        
        // Build markdown context for AI
        $productContext = "## 📦 Mevcut Ürünler:\n\n";
        
        foreach ($products as $product) {
            // Handle both Model and array
            if (is_array($product)) {
                $title = $product['title']['tr'] ?? $product['title']['en'] ?? 'Ürün';
                $price = number_format($product['base_price'] ?? 0, 2, ',', '.');
                $stock = $product['current_stock'] ?? 0;
                // slug can be string or array (JSON)
                $slugData = $product['slug'] ?? '';
                $slug = is_array($slugData) ? ($slugData['tr'] ?? $slugData['en'] ?? '') : $slugData;
                $slug = trim($slug, '"');
            } else {
                $title = $product->getTranslated('title', 'tr');
                $price = number_format($product->base_price ?? 0, 2, ',', '.');
                $stock = $product->current_stock ?? 0;
                $slug = is_string($product->slug) ? trim($product->slug, '"') : '';
            }

            // Satış odaklı sunum
            $productContext .= "### 🔥 {$title}\n";

            // Fiyat sunumu - cazip göster
            $priceNum = floatval(str_replace(['.', ','], ['', '.'], $price));
            if ($priceNum < 2000) {
                $productContext .= "- 💰 **{$price} TL** (KDV dahil) - En ekonomik!\n";
            } elseif ($priceNum < 5000) {
                $productContext .= "- 💰 **{$price} TL** (KDV dahil) - Uygun fiyat!\n";
            } else {
                $productContext .= "- 💰 **{$price} TL** (KDV dahil) - Premium kalite!\n";
            }

            // Stok durumu - aciliyet yarat
            if ($stock <= 5 && $stock > 0) {
                $productContext .= "- ⚠️ **SON {$stock} ADET!** Acele edin!\n";
            } elseif ($stock <= 20) {
                $productContext .= "- 📦 Stokta {$stock} adet (Hızla tükeniyor)\n";
            } elseif ($stock > 20) {
                $productContext .= "- ✅ Stokta hazır, hemen teslim!\n";
            }

            // Satış odaklı özellikler
            $titleLower = mb_strtolower($title);
            if (str_contains($titleLower, 'li-ion') || str_contains($titleLower, 'lithium')) {
                $productContext .= "- 🔋 Li-Ion: Hafif ve uzun ömürlü\n";
            }
            if (str_contains($titleLower, 'elektrikli')) {
                $productContext .= "- ⚡ Elektrikli: Güçlü performans\n";
            }
            if (str_contains($titleLower, 'manuel')) {
                $productContext .= "- 💪 Manuel: Bakım gerektirmez\n";
            }

            // Tıklanabilir link
            if ($slug) {
                $productContext .= "- 👉 [**Hemen İncele**](/shop/product/{$slug})\n";
            }
            $productContext .= "\n";
        }
        
        $context['product_context'] = $productContext;
        
        Log::info('🏗️ ContextBuilderNode', [
            'context_length' => strlen($productContext)
        ]);
        
        return $context;
    }
}
