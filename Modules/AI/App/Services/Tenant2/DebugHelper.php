<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Tenant2;

use Illuminate\Support\Facades\Log;

/**
 * Tenant 2 (ixtif.com) Debug Helper
 *
 * Tenant 2'ye özel debug ve log fonksiyonları.
 * Global dosyalardan taşındı - tenant-aware yapı için.
 *
 * @package Modules\AI\App\Services\Tenant2
 * @version 1.0
 */
class DebugHelper
{
    /**
     * AI Context'teki ixtif ürünlerini logla
     *
     * @param array $aiContext AI context array
     * @return void
     */
    public static function logIxtifProducts(array $aiContext): void
    {
        if (empty($aiContext['context']['modules']['shop']['all_products'])) {
            return;
        }

        // İlk 5 ürünü logla, özellikle "ixtif" içerenleri
        $productsToLog = array_slice($aiContext['context']['modules']['shop']['all_products'], 0, 5);
        $ixtifProducts = [];

        foreach ($productsToLog as $product) {
            $title = is_array($product['title']) ? json_encode($product['title']) : $product['title'];
            if (stripos($title, 'ixtif') !== false || stripos($title, 'İXTİF') !== false) {
                $ixtifProducts[] = [
                    'title' => $title,
                    'url' => $product['url'] ?? 'N/A',
                    'slug_starts_with_i' => str_starts_with(basename($product['url'] ?? ''), 'i'),
                ];
            }
        }

        if (!empty($ixtifProducts)) {
            Log::info('🔍 AI Context - iXTİF Products Check', [
                'count' => count($ixtifProducts),
                'products' => $ixtifProducts,
            ]);
        }
    }

    /**
     * AI yanıtındaki ixtif referanslarını logla
     *
     * @param string $aiResponseText AI yanıt metni
     * @param string $stage 'before' veya 'after' post-processing
     * @return void
     */
    public static function logIxtifReferences(string $aiResponseText, string $stage = 'before'): void
    {
        $emoji = $stage === 'before' ? '🤖' : '✅';
        $label = $stage === 'before' ? 'BEFORE' : 'AFTER';

        Log::info("{$emoji} AI Response {$label} post-processing", [
            'response_preview' => mb_substr($aiResponseText, 0, 500),
            'contains_ixtif' => str_contains($aiResponseText, 'ixtif'),
            'contains_xtif' => str_contains($aiResponseText, 'xtif'),
        ]);
    }

    /**
     * iXTİF prompt örnekleri
     *
     * @return array
     */
    public static function getPromptExamples(): array
    {
        return [
            'correct' => [
                '**Litef EPT15** [LINK:shop:litef-ept15]',
                '**İXTİF CPD15** [LINK:shop:ixtif-cpd15tvl]',
            ],
            'wrong' => [
                '[Litef EPT15](https://ixtif.com/shop/...) ← Markdown YASAK!',
                '<a href="...">Litef EPT15</a> ← HTML link YASAK!',
                '**[Litef EPT15](url)** ← Bu format YASAK!',
            ],
        ];
    }

    /**
     * iXTİF search layer identifier
     *
     * @return string
     */
    public static function getSearchLayerName(): string
    {
        return 'ixtif_price_query';
    }
}
