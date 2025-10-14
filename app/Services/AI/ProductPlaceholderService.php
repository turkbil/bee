<?php

namespace App\Services\AI;

use App\Models\ProductChatPlaceholder;
use Modules\Shop\App\Models\ShopProduct;
use Illuminate\Support\Facades\Log;
use App\Services\AI\CentralAIService;

/**
 * Product Placeholder Service
 *
 * Generates AI-powered placeholder conversations for product chat widgets
 * Caches results in database to avoid repeated AI calls
 */
class ProductPlaceholderService
{
    protected $aiService;

    public function __construct(CentralAIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Get or generate placeholder conversation for a product
     *
     * @param string $productId
     * @param bool $forceRegenerate
     * @return array
     */
    public function getPlaceholder(string $productId, bool $forceRegenerate = false): array
    {
        // 1. Check cache (database)
        if (!$forceRegenerate) {
            $cached = ProductChatPlaceholder::getByProductId($productId);

            if ($cached) {
                Log::info('✅ Product placeholder loaded from cache', [
                    'product_id' => $productId,
                    'generated_at' => $cached->generated_at
                ]);

                return [
                    'success' => true,
                    'conversation' => $cached->conversation_json,
                    'from_cache' => true,
                    'generated_at' => $cached->generated_at,
                ];
            }
        }

        // 2. Generate new placeholder
        try {
            $conversation = $this->generatePlaceholder($productId);

            // 3. Save to cache
            ProductChatPlaceholder::updateOrCreatePlaceholder($productId, $conversation);

            Log::info('✅ Product placeholder generated and cached', [
                'product_id' => $productId,
                'conversation_count' => count($conversation)
            ]);

            return [
                'success' => true,
                'conversation' => $conversation,
                'from_cache' => false,
                'generated_at' => now(),
            ];
        } catch (\Exception $e) {
            Log::error('❌ Product placeholder generation failed', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);

            // Return generic fallback placeholder
            return [
                'success' => false,
                'conversation' => $this->getFallbackPlaceholder(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate placeholder conversation using AI
     *
     * @param string $productId
     * @return array
     */
    protected function generatePlaceholder(string $productId): array
    {
        // Get product data
        $product = ShopProduct::where('product_id', $productId)->first();

        if (!$product) {
            throw new \Exception("Product not found: {$productId}");
        }

        // Get current locale
        $locale = app()->getLocale();

        // Extract product info
        $title = $product->getTranslated('title', $locale) ?? $product->sku;
        $shortDesc = $product->getTranslated('short_description', $locale) ?? '';
        $longDesc = $product->getTranslated('body', $locale) ?? '';

        // Primary specs
        $specs = $this->formatProductSpecs($product);

        // Build AI prompt
        $prompt = $this->buildPrompt($title, $shortDesc, $longDesc, $specs);

        // Call AI - Force OpenAI GPT-4o for reliable JSON responses
        // Note: GPT-5 uses reasoning tokens which don't leave room for content
        $aiResponse = $this->aiService->executeRequest($prompt, [
            'usage_type' => 'product_placeholder',
            'feature_slug' => 'shop',
            'reference_id' => $productId,
            'max_tokens' => 500, // Sufficient for 3 short Q&A pairs
            'temperature' => 0.8, // More creative for diverse questions
            'force_provider' => 'openai', // Use OpenAI (will get GPT-4o)
            'force_model' => 'gpt-4o-mini', // Force GPT-4o-mini for cost-effective JSON generation
        ]);

        if (!$aiResponse['success']) {
            throw new \Exception('AI request failed: ' . ($aiResponse['error'] ?? 'Unknown error'));
        }

        // Parse AI response - extract content from response array
        $responseContent = $aiResponse['response']['content'] ?? $aiResponse['response'];
        $conversation = $this->parseAIResponse($responseContent);

        return $conversation;
    }

    /**
     * Build AI prompt for placeholder generation
     *
     * @param string $title
     * @param string $shortDesc
     * @param string $longDesc
     * @param string $specs
     * @return string
     */
    protected function buildPrompt(string $title, string $shortDesc, string $longDesc, string $specs): string
    {
        return <<<PROMPT
Aşağıdaki ürün için 3 soru-cevap çifti üret. SADECE JSON array döndür.

ÜRÜN: {$title}
ÖZELLİKLER: {$specs}

FORMAT (ZORUNLU):
[
  {"role":"user","text":"SORU 1"},
  {"role":"assistant","text":"Merhaba! CEVAP 1"},
  {"role":"user","text":"SORU 2"},
  {"role":"assistant","text":"CEVAP 2"},
  {"role":"user","text":"SORU 3"},
  {"role":"assistant","text":"CEVAP 3"}
]

KURALLAR:
1. ❌ YASAK: Fiyat, kargo, garanti
2. ❌ YASAK: "Farklı seçenekler var", "Benimle konuşun" gibi genel laflar
3. ✅ ZORUNLU: Yukarıdaki ÖZELLİKLER'den gerçek rakamlar söyle
4. ✅ İLK cevap "Merhaba!" ile başlar, diğerleri başlamaz
5. ✅ Soru MAX 10 kelime, cevap MAX 25 kelime
6. Türkçe

DOĞRU ÖRNEK:
[
  {"role":"user","text":"Kapasite nedir?"},
  {"role":"assistant","text":"Merhaba! 2 ton yük kapasitesi."},
  {"role":"user","text":"Maksimum hız?"},
  {"role":"assistant","text":"12 km/s hız."}
]

ŞİMDİ SADECE JSON ARRAY DÖNDÜR (açıklama yapma):
PROMPT;
    }

    /**
     * Format product specs for prompt
     *
     * @param ShopProduct $product
     * @return string
     */
    protected function formatProductSpecs(ShopProduct $product): string
    {
        $specs = [];

        // Primary specs
        if (is_array($product->primary_specs)) {
            foreach ($product->primary_specs as $spec) {
                if (isset($spec['label']) && isset($spec['value'])) {
                    $specs[] = "{$spec['label']}: {$spec['value']}";
                }
            }
        }

        // Technical specs (flat version)
        if (is_array($product->technical_specs)) {
            foreach ($product->technical_specs as $key => $value) {
                if (is_string($value) || is_numeric($value)) {
                    $specs[] = "{$key}: {$value}";
                }
            }
        }

        return implode(', ', array_slice($specs, 0, 10)); // Limit to 10 specs
    }

    /**
     * Parse AI response to extract conversation array
     *
     * @param string|array $response
     * @return array
     */
    protected function parseAIResponse($response): array
    {
        // If response is already array (from some AI providers)
        if (is_array($response)) {
            return $response;
        }

        // Extract JSON from response string
        $responseText = is_string($response) ? $response : json_encode($response);

        // Remove code block markers if present (```json ... ```)
        $responseText = preg_replace('/```json\s*/i', '', $responseText);
        $responseText = preg_replace('/```\s*$/', '', $responseText);
        $responseText = trim($responseText);

        // Try to find JSON array in response
        preg_match('/\[[\s\S]*\]/U', $responseText, $matches);

        if (empty($matches)) {
            // Log for debugging
            Log::error('JSON parsing failed - response preview:', [
                'response_length' => strlen($responseText),
                'response_start' => substr($responseText, 0, 200)
            ]);
            throw new \Exception('No valid JSON array found in AI response');
        }

        $jsonString = $matches[0];
        $conversation = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON decode failed:', [
                'error' => json_last_error_msg(),
                'json_preview' => substr($jsonString, 0, 200)
            ]);
            throw new \Exception('Invalid JSON in AI response: ' . json_last_error_msg());
        }

        // Validate structure
        if (!is_array($conversation) || count($conversation) < 2) {
            throw new \Exception('Invalid conversation structure');
        }

        // Ensure all items have role and text
        foreach ($conversation as $item) {
            if (!isset($item['role']) || !isset($item['text'])) {
                throw new \Exception('Invalid conversation item structure');
            }
        }

        return $conversation;
    }

    /**
     * Get fallback placeholder when AI fails
     * Uses tenant AI settings to personalize messages
     *
     * @return array
     */
    protected function getFallbackPlaceholder(): array
    {
        // Get tenant-specific AI settings
        $assistantName = \App\Helpers\AISettingsHelper::getAssistantName() ?? 'Asistan';
        $companyServices = \App\Helpers\AISettingsHelper::get('ai_company_main_services') ?? 'ürünlerimiz';
        $personalityRole = \App\Helpers\AISettingsHelper::get('ai_personality_role') ?? 'yardımcı asistan';

        // Truncate long texts for placeholder
        if (strlen($companyServices) > 50) {
            $companyServices = 'ürün ve hizmetlerimiz';
        }

        return [
            ['role' => 'user', 'text' => 'Bu ürün ne işe yarar?'],
            ['role' => 'assistant', 'text' => "Merhaba! Ben {$assistantName}, {$personalityRole} olarak size bu ürün hakkında detaylı bilgi verebilirim."],
            ['role' => 'user', 'text' => 'Hangi özellikleri var?'],
            ['role' => 'assistant', 'text' => "{$companyServices} kapsamında bu ürünün teknik özellikleri ve avantajları hakkında soru sorabilirsiniz!"],
            ['role' => 'user', 'text' => 'Nasıl yardımcı olabilirsiniz?'],
            ['role' => 'assistant', 'text' => 'Ürün özellikleri, kullanım alanları ve size en uygun çözümü bulmak için buradan yazabilirsiniz!'],
        ];
    }

    /**
     * Clear cached placeholder for a product
     *
     * @param string $productId
     * @return bool
     */
    public function clearCache(string $productId): bool
    {
        $placeholder = ProductChatPlaceholder::getByProductId($productId);

        if ($placeholder) {
            $placeholder->delete();

            Log::info('🗑️ Product placeholder cache cleared', [
                'product_id' => $productId
            ]);

            return true;
        }

        return false;
    }
}
