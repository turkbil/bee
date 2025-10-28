<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * AI Response Validator
 *
 * AI'nın ürettiği response'larda yanlış product title/slug kullanımını tespit eder ve düzeltir.
 *
 * Sorun: GPT-4o-mini modeli bazen prompt'tan gelen product bilgilerini değiştiriyor:
 * - "3.0 Ton" → "3. Ton" (title kısaltması)
 * - "1200 lb" → "120 lb" (slug hallüsinasyonu)
 *
 * Çözüm: AI response'u parse edip, Meilisearch'ten doğru verileri alarak düzeltir.
 * NOT: Context'e ihtiyaç duymaz, tüm validasyon Meilisearch üzerinden yapılır!
 */
class AIResponseValidator
{
    protected $meilisearchUrl;
    protected $meilisearchKey;

    public function __construct()
    {
        $this->meilisearchUrl = env('MEILISEARCH_HOST', 'http://127.0.0.1:7700');
        $this->meilisearchKey = env('MEILISEARCH_KEY', '');
    }

    /**
     * Validate ve düzelt AI response
     *
     * @param string $aiResponse AI'nın ürettiği markdown response
     * @return array ['response' => string, 'corrections' => array, 'has_errors' => bool]
     */
    public function validateAndFix(string $aiResponse): array
    {
        $corrections = [];
        $hasErrors = false;
        $fixedResponse = $aiResponse;

        // Product links validation (Meilisearch'ten)
        $result = $this->validateProductLinks($fixedResponse);
        $fixedResponse = $result['response'];
        $corrections = array_merge($corrections, $result['corrections']);
        $hasErrors = $hasErrors || $result['has_errors'];

        // Category links validation (Database'den)
        $result = $this->validateCategoryLinks($fixedResponse);
        $fixedResponse = $result['response'];
        $corrections = array_merge($corrections, $result['corrections']);
        $hasErrors = $hasErrors || $result['has_errors'];

        // Brand links validation (Database'den)
        $result = $this->validateBrandLinks($fixedResponse);
        $fixedResponse = $result['response'];
        $corrections = array_merge($corrections, $result['corrections']);
        $hasErrors = $hasErrors || $result['has_errors'];

        // Log corrections if any
        if (!empty($corrections)) {
            Log::warning('🔧 AI Response Auto-Corrected', [
                'corrections_count' => count($corrections),
                'corrections' => $corrections,
                'has_errors' => $hasErrors
            ]);
        }

        return [
            'response' => $fixedResponse,
            'corrections' => $corrections,
            'has_errors' => $hasErrors
        ];
    }

    /**
     * Validate product links
     * Format: **Product Title** [LINK:shop:product-slug]
     */
    protected function validateProductLinks(string $response): array
    {
        $corrections = [];
        $hasErrors = false;

        // Find all product links in response
        // Pattern: **Any Text** [LINK:shop:any-slug]
        $pattern = '/\*\*([^\*]+)\*\*\s*\[LINK:shop:([^\]]+)\]/';

        $fixedResponse = preg_replace_callback($pattern, function($matches) use (&$corrections, &$hasErrors) {
            $aiTitle = trim($matches[1]);
            $aiSlug = trim($matches[2]);

            // Search Meilisearch for this slug
            $correctData = $this->searchProductBySlug($aiSlug);

            if ($correctData) {
                $correctTitle = $correctData['title'];
                $correctSlug = $correctData['slug'];

                // Check for discrepancies
                $titleMismatch = ($aiTitle !== $correctTitle);
                $slugMismatch = ($aiSlug !== $correctSlug);

                if ($titleMismatch || $slugMismatch) {
                    $hasErrors = true;
                    $corrections[] = [
                        'type' => 'product',
                        'ai_title' => $aiTitle,
                        'correct_title' => $correctTitle,
                        'ai_slug' => $aiSlug,
                        'correct_slug' => $correctSlug,
                        'title_mismatch' => $titleMismatch,
                        'slug_mismatch' => $slugMismatch
                    ];

                    // Return corrected version
                    return "**{$correctTitle}** [LINK:shop:{$correctSlug}]";
                }
            } else {
                // Slug not found - possible hallucination
                // Try fuzzy matching by removing/changing numbers
                $fuzzyMatch = $this->searchProductByFuzzySlug($aiSlug);

                if ($fuzzyMatch) {
                    $hasErrors = true;
                    $corrections[] = [
                        'type' => 'product',
                        'ai_title' => $aiTitle,
                        'correct_title' => $fuzzyMatch['title'],
                        'ai_slug' => $aiSlug,
                        'correct_slug' => $fuzzyMatch['slug'],
                        'fuzzy_match' => true,
                        'reason' => 'Slug hallucination detected (e.g., 120 vs 1200)'
                    ];

                    return "**{$fuzzyMatch['title']}** [LINK:shop:{$fuzzyMatch['slug']}]";
                } else {
                    // No match found - log but keep original
                    Log::warning('🔍 Product slug not found in Meilisearch', [
                        'ai_slug' => $aiSlug,
                        'ai_title' => $aiTitle
                    ]);
                }
            }

            // Return original if no match
            return $matches[0];
        }, $response);

        return [
            'response' => $fixedResponse,
            'corrections' => $corrections,
            'has_errors' => $hasErrors
        ];
    }

    /**
     * Search product by exact slug in Meilisearch
     */
    protected function searchProductBySlug(string $slug): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->meilisearchKey,
                'Content-Type' => 'application/json'
            ])->post($this->meilisearchUrl . '/indexes/shop_products/search', [
                'q' => '',
                'filter' => 'slug = "' . $slug . '"',
                'limit' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['hits']) && count($data['hits']) > 0) {
                    $hit = $data['hits'][0];

                    // Extract title and slug (handle multi-language)
                    $title = is_array($hit['title'])
                        ? ($hit['title']['tr'] ?? $hit['title']['en'] ?? reset($hit['title']))
                        : $hit['title'];

                    $hitSlug = is_array($hit['slug'])
                        ? ($hit['slug']['tr'] ?? $hit['slug']['en'] ?? reset($hit['slug']))
                        : $hit['slug'];

                    return [
                        'title' => $title,
                        'slug' => $hitSlug,
                        'product_id' => $hit['product_id'] ?? null
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Meilisearch search error', ['error' => $e->getMessage(), 'slug' => $slug]);
        }

        return null;
    }

    /**
     * Search product by fuzzy slug matching (for hallucinated slugs)
     * Example: AI writes "120-lb" but correct is "1200-lb"
     */
    protected function searchProductByFuzzySlug(string $aiSlug): ?array
    {
        try {
            // Strategy 1: Remove numbers and search by pattern
            $slugPattern = preg_replace('/-?\d+/', '', $aiSlug);

            // Strategy 2: Try common number hallucinations (120 → 1200, 30 → 300, etc.)
            $numberVariations = $this->generateNumberVariations($aiSlug);

            // Try each variation
            foreach ($numberVariations as $variation) {
                $result = $this->searchProductBySlug($variation);
                if ($result) {
                    return $result;
                }
            }

            // Strategy 3: Search by text query (less precise but catches close matches)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->meilisearchKey,
                'Content-Type' => 'application/json'
            ])->post($this->meilisearchUrl . '/indexes/shop_products/search', [
                'q' => str_replace('-', ' ', $aiSlug),
                'limit' => 5
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['hits']) && count($data['hits']) > 0) {
                    // Return the closest match based on Levenshtein distance
                    $bestMatch = null;
                    $bestDistance = PHP_INT_MAX;

                    foreach ($data['hits'] as $hit) {
                        $hitSlug = is_array($hit['slug'])
                            ? ($hit['slug']['tr'] ?? $hit['slug']['en'] ?? reset($hit['slug']))
                            : $hit['slug'];

                        $distance = levenshtein(strtolower($aiSlug), strtolower($hitSlug));

                        if ($distance < $bestDistance && $distance <= 5) {
                            $bestDistance = $distance;

                            $title = is_array($hit['title'])
                                ? ($hit['title']['tr'] ?? $hit['title']['en'] ?? reset($hit['title']))
                                : $hit['title'];

                            $bestMatch = [
                                'title' => $title,
                                'slug' => $hitSlug,
                                'product_id' => $hit['product_id'] ?? null,
                                'distance' => $distance
                            ];
                        }
                    }

                    return $bestMatch;
                }
            }
        } catch (\Exception $e) {
            Log::error('Fuzzy slug search error', ['error' => $e->getMessage(), 'ai_slug' => $aiSlug]);
        }

        return null;
    }

    /**
     * Generate number variations for fuzzy matching
     * Example: "120" → ["1200", "12", "1200", "0120"]
     */
    protected function generateNumberVariations(string $slug): array
    {
        $variations = [];

        // Extract numbers from slug
        preg_match_all('/-?(\d+)-?/', $slug, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $number) {
                // Add/remove zeros
                $variations[] = str_replace($number, $number . '0', $slug);     // 120 → 1200
                $variations[] = str_replace($number, $number . '00', $slug);    // 12 → 1200
                $variations[] = str_replace($number, '0' . $number, $slug);     // 120 → 0120

                // Remove trailing zero
                if (strlen($number) > 1 && substr($number, -1) === '0') {
                    $variations[] = str_replace($number, rtrim($number, '0'), $slug); // 1200 → 120
                }

                // Common multipliers (for ton/kg conversions)
                if (preg_match('/\d+/', $number)) {
                    $num = (int)$number;
                    $variations[] = str_replace($number, ($num * 10), $slug);   // 3 → 30
                    $variations[] = str_replace($number, ($num / 10), $slug);   // 30 → 3
                }
            }
        }

        return array_unique($variations);
    }

    /**
     * Validate category links
     * Format: **Category Name** [LINK:category:category-slug]
     * NOTE: Currently disabled - category validation not critical
     */
    protected function validateCategoryLinks(string $response): array
    {
        // TODO: Implement category validation via database if needed
        // For now, categories are less critical than products
        return [
            'response' => $response,
            'corrections' => [],
            'has_errors' => false
        ];
    }

    /**
     * Validate brand links
     * Format: **Brand Name** [LINK:brand:brand-slug]
     * NOTE: Currently disabled - brand validation not critical
     */
    protected function validateBrandLinks(string $response): array
    {
        // TODO: Implement brand validation via database if needed
        // For now, brands are less critical than products
        return [
            'response' => $response,
            'corrections' => [],
            'has_errors' => false
        ];
    }
}
