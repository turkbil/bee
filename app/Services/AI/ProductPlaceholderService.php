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
     * Get placeholder conversation for a product (CACHE-ONLY)
     *
     * ⚡ NEW STRATEGY (Cache-Only):
     * - Cache HIT: Return immediately (~50ms)
     * - Cache MISS: Return fallback (NO generation, queue handles it)
     *
     * GENERATION FLOW:
     * 1. User visits product page → ShopController dispatches queue job
     * 2. Queue processes → AI generates → Saves to DB
     * 3. Next user → Cache HIT → Real conversation shown
     *
     * @param string $productId
     * @param bool $forceRegenerate (ignored, kept for BC)
     * @return array
     */
    public function getPlaceholder(string $productId, bool $forceRegenerate = false): array
    {
        // Check cache (database) - READONLY
        $cached = ProductChatPlaceholder::getByProductId($productId);

        if ($cached) {
            // ✅ CACHE HIT - Return real conversation
            Log::info('✅ Placeholder from cache (readonly)', [
                'product_id' => $productId,
                'age' => $cached->generated_at?->diffForHumans()
            ]);

            return [
                'success' => true,
                'conversation' => $cached->conversation_json,
                'from_cache' => true,
                'generated_at' => $cached->generated_at,
            ];
        }

        // ❌ CACHE MISS - Return fallback (NO generation here!)
        // Queue job handles generation (dispatched in ShopController)
        Log::info('⚡ Cache miss - returning fallback (queue will generate)', [
            'product_id' => $productId
        ]);

        return [
            'success' => true,
            'conversation' => $this->getFallbackPlaceholder(),
            'from_cache' => false,
            'is_fallback' => true,
        ];
    }

    /**
     * Force generate placeholder (used by background command)
     *
     * @param string $productId
     * @return array
     */
    public function forceGenerate(string $productId): array
    {
        try {
            $conversation = $this->generatePlaceholder($productId);

            // Save to cache
            ProductChatPlaceholder::updateOrCreatePlaceholder($productId, $conversation);

            return [
                'success' => true,
                'conversation' => $conversation,
                'generated_at' => now(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate placeholder in background (non-blocking)
     *
     * SAFE APPROACH: Uses shell_exec + nohup instead of Jobs to avoid Horizon crashes
     *
     * @param string $productId
     * @return void
     */
    protected function generateInBackground(string $productId): void
    {
        try {
            $basePath = base_path();
            $command = "nohup php {$basePath}/artisan app:generate-placeholder {$productId} > /dev/null 2>&1 &";

            shell_exec($command);

            Log::info('🔄 Background generation triggered', [
                'product_id' => $productId,
                'command' => $command
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Background generation failed to start', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            // Don't throw - user already got fallback
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
        $prompt = $this->buildPrompt($title, $shortDesc, $longDesc, $specs, $product->primary_specs);

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
     * @param array|null $primarySpecs
     * @return string
     */
    protected function buildPrompt(string $title, string $shortDesc, string $longDesc, string $specs, ?array $primarySpecs = null): string
    {
        // Dinamik örnek oluştur - ürünün kendi primary_specs'inden
        $exampleQuestions = $this->generateExampleQuestions($primarySpecs, $specs);

        return <<<PROMPT
Aşağıdaki ürün için 4 soru-cevap çifti üret. SADECE JSON array döndür.

ÜRÜN: {$title}
ÖZELLİKLER: {$specs}

FORMAT (ZORUNLU):
[
  {"role":"user","text":"SORU 1"},
  {"role":"assistant","text":"Merhaba! CEVAP 1"},
  {"role":"user","text":"SORU 2"},
  {"role":"assistant","text":"CEVAP 2"},
  {"role":"user","text":"SORU 3"},
  {"role":"assistant","text":"CEVAP 3"},
  {"role":"user","text":"SORU 4"},
  {"role":"assistant","text":"CEVAP 4"}
]

KURALLAR:
1. ❌ YASAK: Fiyat, kargo, garanti
2. ❌ YASAK: "Farklı seçenekler var", "Benimle konuşun" gibi genel laflar
3. ✅ ZORUNLU: Yukarıdaki ÖZELLİKLER listesinden gerçek değerleri kullanarak sor
4. ✅ ZORUNLU: Her soru bir özellik hakkında olmalı ve cevapda GERÇEK DEĞER söyle
5. ✅ İLK cevap "Merhaba!" ile başlar, diğerleri başlamaz
6. ✅ Soru MAX 10 kelime, cevap MAX 25 kelime
7. Türkçe

{$exampleQuestions}

ŞİMDİ SADECE JSON ARRAY DÖNDÜR (açıklama yapma):
PROMPT;
    }

    /**
     * Generate example questions based on product's actual primary_specs
     *
     * @param array|null $primarySpecs
     * @param string $specsFormatted
     * @return string
     */
    protected function generateExampleQuestions(?array $primarySpecs, string $specsFormatted): string
    {
        if (empty($primarySpecs) || !is_array($primarySpecs)) {
            // Fallback: Genel örnek
            return <<<EXAMPLE
DOĞRU ÖRNEK (BU ÜRÜNDEKİ GİBİ YAP):
[
  {"role":"user","text":"Özellik 1 nedir?"},
  {"role":"assistant","text":"Merhaba! Değer 1."},
  {"role":"user","text":"Özellik 2?"},
  {"role":"assistant","text":"Değer 2."}
]
EXAMPLE;
        }

        // İlk 4 primary_specs'i al
        $selectedSpecs = array_slice($primarySpecs, 0, 4);

        $examples = [];
        $isFirst = true;

        foreach ($selectedSpecs as $spec) {
            if (!isset($spec['label']) || !isset($spec['value'])) {
                continue;
            }

            $label = $spec['label'];
            $value = $spec['value'];

            // İlk soru için "Merhaba!" ekle
            if ($isFirst) {
                $examples[] = "  {\"role\":\"user\",\"text\":\"{$label} nedir?\"},";
                $examples[] = "  {\"role\":\"assistant\",\"text\":\"Merhaba! {$value}.\"},";
                $isFirst = false;
            } else {
                $examples[] = "  {\"role\":\"user\",\"text\":\"{$label}?\"},";
                $examples[] = "  {\"role\":\"assistant\",\"text\":\"{$value}.\"},";
            }
        }

        // Son virgülü kaldır
        if (!empty($examples)) {
            $lastKey = count($examples) - 1;
            $examples[$lastKey] = rtrim($examples[$lastKey], ',');
        }

        $exampleJson = "[\n" . implode("\n", $examples) . "\n]";

        return <<<EXAMPLE
BU ÜRÜN İÇİN DOĞRU ÖRNEK (AYNEN BUNUN GİBİ YAP):
{$exampleJson}
EXAMPLE;
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

        // Get context arrays instead of non-existent ::get() method
        $companyContext = \App\Helpers\AISettingsHelper::getCompanyContext();
        $personalityContext = \App\Helpers\AISettingsHelper::getPersonality();

        $companyServices = $companyContext['services'] ?? 'ürün ve hizmetlerimiz';
        $personalityRole = $personalityContext['role'] ?? 'yardımcı asistan';

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
