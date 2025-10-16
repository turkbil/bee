<?php

namespace App\Services\AI;

use Modules\Shop\App\Models\ShopProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Intelligent Product Search Service
 *
 * 3-Layer search system:
 * 1. Exact Match (SKU, model, title exact match)
 * 2. Fuzzy Search (Levenshtein Distance for typos)
 * 3. Phonetic Search (Turkish number-to-digit conversion)
 *
 * Handles all user types: polite, rude, urgent, confused
 */
class ProductSearchService
{
    protected int $tenantId;
    protected string $locale;

    public function __construct()
    {
        // ✅ FIX: Get tenant_id from central tenants table via tenancy helper
        $this->tenantId = tenant('id');

        // ⚠️ CRITICAL: tenant() NULL ise exception fırlat - API route tenant middleware eksik!
        if (!$this->tenantId) {
            \Log::error('🚨 ProductSearchService: Tenant context missing!', [
                'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)
            ]);
            throw new \Exception('Tenant context is required for ProductSearchService. Ensure InitializeTenancy middleware is applied to the route.');
        }

        // ✅ FIX: Get locale from tenant's tenant_languages table (is_default = 1)
        try {
            $defaultLanguage = \Modules\LanguageManagement\App\Models\TenantLanguage::where('is_default', 1)->first();
            $this->locale = $defaultLanguage ? $defaultLanguage->code : 'tr';

            \Log::info('🔧 ProductSearchService initialized', [
                'tenant_id' => $this->tenantId,
                'locale' => $this->locale,
                'default_language' => $defaultLanguage ? $defaultLanguage->name : 'Turkish (fallback)'
            ]);
        } catch (\Exception $e) {
            \Log::error('🚨 ProductSearchService: Failed to get default language', [
                'tenant_id' => $this->tenantId,
                'error' => $e->getMessage()
            ]);

            // Fallback to 'tr' if language table query fails
            $this->locale = 'tr';
        }
    }

    /**
     * Main search entry point
     */
    public function searchProducts(string $userMessage, array $options = []): array
    {
        // Normalize message (remove aggression, urgency markers)
        $normalizedMessage = $this->normalizeUserMessage($userMessage);

        $cacheKey = "smart_search:{$this->tenantId}:" . md5($normalizedMessage);

        return Cache::remember($cacheKey, 300, function() use ($normalizedMessage, $options) {
            // 🆕 STEP 0: Detect category first (HIGHEST PRIORITY!)
            $detectedCategory = $this->detectCategory($normalizedMessage);

            // Extract keywords from user message
            $keywords = $this->extractKeywords($normalizedMessage);

            Log::info('🔍 Smart Product Search Started', [
                'tenant_id' => $this->tenantId,
                'normalized_message' => substr($normalizedMessage, 0, 100),
                'keywords' => $keywords,
                'detected_category' => $detectedCategory ? $detectedCategory['category_name'] : 'none'
            ]);

            // 🆕 CATEGORY-BASED SEARCH (If category detected)
            if ($detectedCategory) {
                Log::info('🔍 Attempting category search', [
                    'category_id' => $detectedCategory['category_id'],
                    'category_name' => $detectedCategory['category_name']
                ]);

                $results = $this->searchByCategory($detectedCategory['category_id'], $keywords);

                Log::info('📊 Category search results', [
                    'results_count' => count($results),
                    'is_empty' => empty($results)
                ]);

                if (!empty($results)) {
                    Log::info('✅ Category Search found products', [
                        'category' => $detectedCategory['category_name'],
                        'count' => count($results)
                    ]);
                    return $this->formatResults($results, 'category', $detectedCategory);
                }
            }

            // Try Layer 1: Exact Match
            $results = $this->exactMatch($keywords, $detectedCategory);
            if (!empty($results)) {
                Log::info('✅ Layer 1 (Exact Match) found products', [
                    'count' => count($results)
                ]);
                return $this->formatResults($results, 'exact', $detectedCategory);
            }

            // Try Layer 2: Fuzzy Search (DISABLED - causes array-to-string conversion issues)
            // $results = $this->fuzzySearch($keywords, $detectedCategory);
            // if (!empty($results)) {
            //     Log::info('✅ Layer 2 (Fuzzy Search) found products', [
            //         'count' => count($results)
            //     ]);
            //     return $this->formatResults($results, 'fuzzy', $detectedCategory);
            // }

            // Try Layer 3: Phonetic Search
            $results = $this->phoneticSearch($keywords);
            if (!empty($results)) {
                Log::info('✅ Layer 3 (Phonetic Search) found products', [
                    'count' => count($results)
                ]);
                return $this->formatResults($results, 'phonetic', $detectedCategory);
            }

            Log::warning('❌ No products found in any layer', [
                'category_detected' => $detectedCategory ? 'yes' : 'no'
            ]);
            return [];
        });
    }

    /**
     * Normalize user message (handle rude, urgent, confused users)
     */
    protected function normalizeUserMessage(string $message): string
    {
        // Convert to lowercase
        $normalized = mb_strtolower($message);

        // Remove urgency markers
        $urgencyMarkers = ['acil', 'hemen', 'şimdi', 'çabuk', 'hızlı', 'ivedi', '!!!', '!!!!'];
        $normalized = str_replace($urgencyMarkers, '', $normalized);

        // Remove rudeness (keep professional regardless)
        $rudeWords = ['lan', 'yav', 'be', 'ya', 'amaan'];
        $normalized = str_replace($rudeWords, '', $normalized);

        // Remove excessive punctuation
        $normalized = preg_replace('/[!?]{2,}/', '', $normalized);

        // Remove emojis (optional, but helps with search)
        $normalized = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $normalized);

        return trim($normalized);
    }

    /**
     * Extract searchable keywords from user message
     */
    protected function extractKeywords(string $message): array
    {
        // Remove common stop words (Turkish)
        $stopWords = [
            'ürün', 'ürününüz', 'ürününüzün', 'link', 'linkini', 'istiyorum',
            'isterim', 'lütfen', 'hakkında', 'bilgi', 've', 'ile', 'için',
            'bir', 'bu', 'şu', 'o', 'ne', 'nedir', 'nasıl', 'kaç', 'hangi',
            'var', 'mı', 'mi', 'mu', 'mü', 'acaba', 'bana', 'bence',
            'verin', 'gösterin', 'atın', 'yollayın', 'gönder', 'söyle',
            'alo', 'merhaba', 'selam', 'iyi', 'günler', 'akşamlar', 'arıyorum',
            'lazım', 'gerekiyor', 'alacağım', 'almak'
        ];

        $message = str_replace($stopWords, ' ', $message);

        // Extract potential model numbers and technical terms
        preg_match_all('/[a-zA-Z0-9\-]+/', $message, $matches);
        $keywords = $matches[0] ?? [];

        // Filter out single characters and very short words
        $keywords = array_filter($keywords, fn($k) => strlen($k) >= 2);

        // Add normalized versions (remove dashes, spaces, underscores)
        $normalized = [];
        foreach ($keywords as $keyword) {
            $clean = str_replace(['-', ' ', '_'], '', $keyword);
            if (strlen($clean) >= 2) {
                $normalized[] = $clean;
            }
        }

        // Also extract capacity/weight numbers (important for forklifts)
        preg_match_all('/(\d+)\s*(ton|kg|kilo|kilogram)?/', $message, $capacityMatches);
        if (!empty($capacityMatches[1])) {
            foreach ($capacityMatches[1] as $idx => $number) {
                $unit = $capacityMatches[2][$idx] ?? '';
                // Convert ton to kg
                if (stripos($unit, 'ton') !== false) {
                    $keywords[] = ($number * 1000) . 'kg';
                } else {
                    $keywords[] = $number . 'kg';
                }
            }
        }

        return array_values(array_unique(array_merge($keywords, $normalized)));
    }

    /**
     * 🆕 NEW: Detect category from user message
     */
    public function detectCategory(string $message): ?array
    {
        $lowerMessage = mb_strtolower($message);

        // Category keywords mapping (Turkish)
        $categoryKeywords = [
            'transpalet' => ['transpalet', 'trans palet', 'palet taşıma', 'el arabası'],
            'forklift' => ['forklift', 'fork lift', 'forklit', 'çatal istif', 'istifleme makinesi'],
            'reach-truck' => ['reach truck', 'reach', 'reachtruck', 'dar koridor', 'uzun kaldırma'],
            'istif-makinesi' => ['istif makinesi', 'istif', 'stacker', 'yüksek kaldırma'],
            'platform' => ['platform', 'yükseltici platform', 'makaslı platform'],
            'aksesuarlar' => ['aksesuar', 'yedek parça', 'palet', 'tekerlek']
        ];

        // Check each category
        foreach ($categoryKeywords as $categorySlug => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($lowerMessage, $keyword) !== false) {
                    // Get category from database
                    $category = \Modules\Shop\App\Models\ShopCategory::where('slug', 'LIKE', '%' . $categorySlug . '%')
                        ->orWhere('title', 'LIKE', '%' . $keyword . '%')
                        ->where('is_active', true)
                        ->first();

                    if ($category) {
                        // Get title in current locale (handle JSON title)
                        $title = is_array($category->title)
                            ? ($category->title[$this->locale] ?? $category->title['tr'] ?? 'Unknown')
                            : $category->title;

                        return [
                            'category_id' => $category->category_id,
                            'category_name' => $title,
                            'category_slug' => $category->slug,
                            'keyword_matched' => $keyword
                        ];
                    }
                }
            }
        }

        return null;
    }

    /**
     * 🆕 NEW: Category-based search (Priority!)
     */
    protected function searchByCategory(int $categoryId, array $keywords = []): array
    {
        // First get products WITHOUT keyword filtering
        $baseQuery = ShopProduct::where('is_active', true)
            ->where('category_id', $categoryId);

        $totalInCategory = $baseQuery->count();
        Log::info('🔎 searchByCategory debug', [
            'category_id' => $categoryId,
            'total_products_in_category' => $totalInCategory,
            'keywords' => $keywords
        ]);

        // Now apply full filters
        $query = ShopProduct::where('is_active', true)
            ->where('category_id', $categoryId)
            ->select([
                'product_id', 'sku', 'title', 'slug', 'short_description',
                'category_id', 'base_price', 'price_on_request'
            ])
            ->with('category:category_id,title,slug');

        // If keywords provided, filter further (skip JSON for now)
        // SKIP keyword filtering for category search - just return all products from category
        // if (!empty($keywords)) {
        //     $query->where(function($q) use ($keywords) {
        //         foreach ($keywords as $keyword) {
        //             $q->orWhere('sku', 'LIKE', '%' . $keyword . '%')
        //               ->orWhere('title', 'LIKE', '%' . $keyword . '%');
        //         }
        //     });
        // }

        $results = $query->limit(10)->get()->toArray();
        Log::info('🔎 searchByCategory results', [
            'results_count' => count($results)
        ]);

        return $results;
    }

    /**
     * LAYER 1: Exact Match Search
     */
    protected function exactMatch(array $keywords, ?array $detectedCategory = null): array
    {
        if (empty($keywords)) return [];

        $query = ShopProduct::where('is_active', true)
            ->select([
                'product_id', 'sku', 'title', 'slug', 'short_description',
                'category_id', 'base_price', 'price_on_request', 'technical_specs'
            ])
            ->with('category:category_id,title,slug');

        // If category detected, filter by category
        if ($detectedCategory) {
            $query->where('category_id', $detectedCategory['category_id']);
        }

        // Search in SKU, title, or custom fields
        $query->where(function($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                $q->orWhere('sku', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('title', 'LIKE', '%' . $keyword . '%')
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(technical_specs, '$.model')) LIKE ?", ['%' . $keyword . '%'])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(technical_specs, '$.capacity')) LIKE ?", ['%' . $keyword . '%']);
            }
        });

        return $query->limit(10)->get()->toArray();
    }

    /**
     * LAYER 2: Fuzzy Search (Levenshtein Distance)
     */
    protected function fuzzySearch(array $keywords, ?array $detectedCategory = null): array
    {
        if (empty($keywords)) return [];

        // Get all active products (cached for performance)
        $cacheKey = "all_products_cache:{$this->tenantId}";
        if ($detectedCategory) {
            $cacheKey .= ":{$detectedCategory['category_id']}";
        }

        $products = Cache::remember($cacheKey, 600, function() use ($detectedCategory) {
            $query = ShopProduct::where('is_active', true)
                ->select([
                    'product_id', 'sku', 'title', 'slug', 'short_description',
                    'category_id', 'base_price', 'price_on_request', 'technical_specs'
                ])
                ->with('category:category_id,title,slug');

            // Filter by category if detected
            if ($detectedCategory) {
                $query->where('category_id', $detectedCategory['category_id']);
            }

            return $query->get();
        });

        $results = [];
        $threshold = 2; // Max allowed Levenshtein distance

        foreach ($products as $product) {
            // Build searchable text from SKU + title + short_description (skip complex JSON)
            $searchableText = $this->normalizeText(
                $product->sku . ' ' .
                $product->title . ' ' .
                ($product->short_description ?? '')
            );

            foreach ($keywords as $keyword) {
                $normalizedKeyword = $this->normalizeText($keyword);

                // Skip very short keywords for fuzzy search
                if (strlen($normalizedKeyword) < 3) continue;

                // Calculate Levenshtein distance
                $distance = levenshtein($normalizedKeyword, $searchableText);

                // Also check if keyword is substring (partial match)
                $isSubstring = strpos($searchableText, $normalizedKeyword) !== false;

                // Check similarity ratio (more flexible than pure distance)
                similar_text($normalizedKeyword, $searchableText, $percent);

                if ($distance <= $threshold || $isSubstring || $percent > 60) {
                    $results[] = $product;
                    break; // Found a match, no need to check other keywords
                }
            }

            if (count($results) >= 10) break; // Limit results
        }

        return array_map(fn($p) => $p->toArray(), $results);
    }

    /**
     * LAYER 3: Phonetic Search (Turkish number-to-digit)
     */
    protected function phoneticSearch(array $keywords): array
    {
        if (empty($keywords)) return [];

        // Convert Turkish number words to digits
        $turkishNumbers = [
            'sıfır' => '0', 'sifir' => '0',
            'bir' => '1',
            'iki' => '2',
            'üç' => '3', 'uc' => '3',
            'dört' => '4', 'dort' => '4',
            'beş' => '5', 'bes' => '5',
            'altı' => '6', 'alti' => '6',
            'yedi' => '7',
            'sekiz' => '8',
            'dokuz' => '9',
            // Letter phonetics
            'ef' => 'f', 'fe' => 'f',
            'ge' => 'g', 'je' => 'g',
            'ha' => 'h', 'he' => 'h',
            'ka' => 'k', 'ke' => 'k',
            'el' => 'l', 'le' => 'l',
            'em' => 'm', 'me' => 'm',
            'en' => 'n', 'ne' => 'n',
            'pe' => 'p', 'pi' => 'p',
            're' => 'r', 'ra' => 'r',
            'es' => 's', 'se' => 's',
            'te' => 't', 'ti' => 't',
            've' => 'v', 'vi' => 'v',
            'ze' => 'z', 'zi' => 'z',
        ];

        $convertedKeywords = [];
        foreach ($keywords as $keyword) {
            $converted = str_replace(
                array_keys($turkishNumbers),
                array_values($turkishNumbers),
                mb_strtolower($keyword)
            );

            // Only add if conversion actually changed something
            if ($converted !== mb_strtolower($keyword)) {
                $convertedKeywords[] = $converted;
            }
        }

        // Add original keywords too (in case phonetic didn't match)
        $convertedKeywords = array_merge($convertedKeywords, $keywords);

        // Now do exact match with converted keywords
        return $this->exactMatch($convertedKeywords);
    }

    /**
     * Normalize text for comparison (remove spaces, dashes, lowercase)
     */
    protected function normalizeText(string $text): string
    {
        return str_replace([' ', '-', '_', '.', ','], '', mb_strtolower($text));
    }

    /**
     * Format results with search metadata
     */
    protected function formatResults(array $products, string $searchLayer, ?array $detectedCategory = null): array
    {
        $result = [
            'products' => $products,
            'count' => count($products),
            'search_layer' => $searchLayer,
            'tenant_id' => $this->tenantId,
            'timestamp' => now()->toIso8601String()
        ];

        // Add category info if detected
        if ($detectedCategory) {
            $result['detected_category'] = $detectedCategory;
        }

        return $result;
    }

    /**
     * Detect user sentiment/urgency for context
     */
    public function detectUserSentiment(string $message): array
    {
        $sentiment = [
            'is_urgent' => false,
            'is_rude' => false,
            'is_polite' => false,
            'is_confused' => false,
            'tone' => 'neutral'
        ];

        $lowerMessage = mb_strtolower($message);

        // Urgency detection
        $urgencyWords = ['acil', 'hemen', 'şimdi', 'çabuk', 'ivedi', 'asap'];
        foreach ($urgencyWords as $word) {
            if (strpos($lowerMessage, $word) !== false) {
                $sentiment['is_urgent'] = true;
                $sentiment['tone'] = 'urgent';
                break;
            }
        }

        // Politeness detection
        $politeWords = ['lütfen', 'rica ederim', 'mümkünse', 'zahmet', 'teşekkür'];
        foreach ($politeWords as $word) {
            if (strpos($lowerMessage, $word) !== false) {
                $sentiment['is_polite'] = true;
                if ($sentiment['tone'] === 'neutral') {
                    $sentiment['tone'] = 'polite';
                }
                break;
            }
        }

        // Rudeness detection (keep professional regardless)
        $rudeWords = ['lan', 'yav', 'be', 'ya'];
        foreach ($rudeWords as $word) {
            if (strpos($lowerMessage, $word) !== false) {
                $sentiment['is_rude'] = true;
                $sentiment['tone'] = 'rude';
                break;
            }
        }

        // Confusion detection
        $confusionWords = ['bilmiyorum', 'emin değilim', 'sanırım', 'galiba', 'acaba'];
        foreach ($confusionWords as $word) {
            if (strpos($lowerMessage, $word) !== false) {
                $sentiment['is_confused'] = true;
                if ($sentiment['tone'] === 'neutral') {
                    $sentiment['tone'] = 'confused';
                }
                break;
            }
        }

        return $sentiment;
    }
}
