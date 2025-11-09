<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

use Illuminate\Support\Facades\Log;

class ContextBuilderNode extends BaseNode
{
    public function execute(array $context): array
    {
        // Ensure $products is always a Collection (handle both array and Collection)
        $products = collect($context['products'] ?? []);

        Log::info('🏗️ ContextBuilderNode: Input', [
            'has_products' => isset($context['products']),
            'products_count' => $products->count(),
            'products_found' => $context['products_found'] ?? 'NULL'
        ]);

        // Get USD exchange rate from shop_currencies
        $usdRate = \DB::table('shop_currencies')
            ->where('code', 'USD')
            ->where('is_active', 1)
            ->value('exchange_rate') ?? 42.0; // Fallback to 42 if not found

        // Build markdown context for AI
        $productContext = "## 📦 Mevcut Ürünler:\n\n";

        foreach ($products as $product) {
            // Handle both Model and array
            if (is_array($product)) {
                $title = $product['title']['tr'] ?? $product['title']['en'] ?? 'Ürün';
                $basePrice = $product['base_price'] ?? 0;
                $currency = $product['currency'] ?? 'TRY';
                $stock = $product['current_stock'] ?? 0;
                $categoryId = $product['category_id'] ?? null;

                // slug can be string or array (JSON)
                $slugData = $product['slug'] ?? '';
                if (is_array($slugData)) {
                    $slug = $slugData['tr'] ?? $slugData['en'] ?? '';
                } elseif (is_string($slugData)) {
                    // JSON string parse
                    $decoded = json_decode($slugData, true);
                    $slug = is_array($decoded) ? ($decoded['tr'] ?? $decoded['en'] ?? $slugData) : $slugData;
                } else {
                    $slug = '';
                }
                $slug = trim($slug, '"');
            } else {
                $title = $product->getTranslated('title', 'tr');
                $basePrice = $product->base_price ?? 0;
                $currency = $product->currency ?? 'TRY';
                $stock = $product->current_stock ?? 0;
                $slug = $product->getTranslated('slug', 'tr') ?? '';
                $categoryId = $product->category_id ?? null;
            }

            // Get category label (from product data if provided by tenant-specific service)
            $categoryLabel = '';
            if (is_array($product) && isset($product['_category_label'])) {
                $categoryLabel = $product['_category_label'];
            } elseif (!is_array($product) && isset($product->_category_label)) {
                $categoryLabel = $product->_category_label;
            }

            // Currency conversion: USD -> TRY
            if (strtoupper($currency) === 'USD') {
                $priceInTRY = $basePrice * $usdRate;
                $price = number_format($priceInTRY, 0, ',', '.');
                $currencySymbol = 'TL';
                $originalPrice = '$' . number_format($basePrice, 0, ',', '.');
            } else {
                $price = number_format($basePrice, 0, ',', '.');
                $currencySymbol = 'TL';
                $originalPrice = null;
            }

            // ✅ BAŞLIK TEMİZLEME: Sayı formatı düzelt (2. Ton → 2 Ton)
            // Database'de "İXTİF EPT20-20ETC - 2. Ton..." gibi başlıklar var
            // Türkçe'de sayılarda nokta kullanılmaz: "2 ton" doğru, "2. ton" yanlış
            $title = preg_replace('/(\d+)\.\s+(Ton|ton)/u', '$1 $2', $title);

            // ✅ TEMİZ SUNUM - İkon yok, hardcode yok, stok bilgisi yok
            $productContext .= "### {$title}\n";

            // Fiyat kontrolü - fiyatsız ürünler için özel mesaj
            if ($basePrice > 0) {
                // Fiyatlı ürün
                if ($originalPrice) {
                    $productContext .= "- **{$price} {$currencySymbol}** ≈ {$originalPrice}\n";
                } else {
                    $productContext .= "- **{$price} {$currencySymbol}**\n";
                }
            } else {
                // Fiyatsız ürün - iletişim bilgilerini göster
                $productContext .= "- 📞 **Fiyat için iletişime geçin**\n";
            }

            // ✅ STOK BİLGİSİ KALDIRILDI
            // ✅ ASLA stok durumu verme (kullanıcı talebi)
            // ✅ AI sadece mevcut ürünleri önerecek (stok olan ürünler zaten öncelikli)

            // Tıklanabilir link
            if ($slug) {
                $productContext .= "- [Ürünü İncele](/shop/{$slug})\n";
            }
            $productContext .= "\n";
        }
        
        Log::info('🏗️ ContextBuilderNode: Output', [
            'context_length' => strlen($productContext),
            'products_count' => $products->count()
        ]);

        // Return only new keys (FlowExecutor will merge with context)
        // IMPORTANT: Also return products_found to preserve it for AIResponseNode
        return [
            'product_context' => $productContext,
            'products_found' => $products->count()  // Preserve for AI check
        ];
    }
}
