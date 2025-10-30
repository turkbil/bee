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
    protected ?int $tenantId = null;
    protected ?string $locale = null;
    protected HybridSearchService $hybridSearch;

    /**
     * ⚙️ CATEGORY-SPECIFIC PARAMETER MAPPINGS
     * Her kategori için hangi parametrelerin önemli olduğunu tanımlar
     */
    protected const CATEGORY_PARAMETERS = [
        1 => [ // Forklift
            'name' => 'Forklift',
            'parameters' => [
                'capacity' => ['2 ton', '1.5 ton', '2.5 ton', '3 ton'],
                'lift_height' => ['3000mm', '3m', '4500mm', '4.5m', '6000mm', '6m'],
                'mast_type' => ['duplex', 'triplex', 'standart', 'serbest'],
                'motor_type' => ['elektrik', 'dizel', 'LPG', 'Li-Ion'],
                'tire_type' => ['havalı', 'dolgu', 'superelastik'],
                'fork_length' => ['1070mm', '1150mm', '1220mm']
            ]
        ],
        2 => [ // Transpalet
            'name' => 'Transpalet',
            'parameters' => [
                'capacity' => ['1.5 ton', '2 ton', '2.5 ton'],
                'fork_length' => ['900mm', '1150mm', '1220mm'],
                'fork_width' => ['540mm', '560mm', '685mm'],
                'battery_type' => ['Li-Ion', 'AGM', 'kurşun-asit'],
                'usage_area' => ['soğuk depo', 'gıda', 'paslanmaz'],
                'operator_type' => ['yürüyen', 'sürücülü', 'platform']
            ]
        ],
        3 => [ // İstif Makinesi
            'name' => 'İstif Makinesi',
            'parameters' => [
                'capacity' => ['1 ton', '1.2 ton', '1.5 ton', '2 ton'],
                'lift_height' => ['1600mm', '3000mm', '3.5m', '4.5m'],
                'operator_type' => ['yürüyen', 'sürücülü'],
                'battery_type' => ['Li-Ion', 'AGM'],
                'fork_length' => ['1150mm', '1220mm']
            ]
        ],
        4 => [ // Sipariş Toplama
            'name' => 'Sipariş Toplama',
            'parameters' => [
                'capacity' => ['200kg', '300kg', '500kg'],
                'platform_height' => ['2m', '3m', '4m', '6m'],
                'battery_type' => ['Li-Ion', 'AGM', 'kurşun-asit']
            ]
        ],
        5 => [ // Otonom
            'name' => 'Otonom Sistemler',
            'parameters' => [
                'capacity' => ['1.5 ton', '2 ton'],
                'navigation' => ['AGV', 'AMR', 'lazer', 'SLAM'],
                'automation_level' => ['tam otonom', 'yarı otonom']
            ]
        ],
        6 => [ // Reach Truck
            'name' => 'Reach Truck',
            'parameters' => [
                'capacity' => ['1.5 ton', '2 ton'],
                'lift_height' => ['6m', '9m', '12m'],
                'corridor_width' => ['dar koridor', '2.5m', '2.7m'],
                'cabin_type' => ['açık', 'kapalı']
            ]
        ]
    ];

    public function __construct(HybridSearchService $hybridSearch)
    {
        $this->hybridSearch = $hybridSearch;
        // Tenant context will be initialized lazily on first use
    }

    /**
     * Ensure tenant context is initialized
     * @throws \Exception if tenant context is not available
     */
    protected function ensureTenantContext(): void
    {
        if ($this->tenantId !== null) {
            return; // Already initialized
        }

        // Get tenant_id from tenancy helper
        $this->tenantId = tenant('id');

        if ($this->tenantId === null) {
            throw new \Exception('Tenant context is required. Ensure InitializeTenancy middleware is applied to the route.');
        }

        // Get locale from tenant's default language
        $defaultLanguage = \Modules\LanguageManagement\App\Models\TenantLanguage::where('is_default', 1)->first();
        $this->locale = $defaultLanguage ? $defaultLanguage->code : 'tr';

        \Log::info('🔧 ProductSearchService initialized', [
            'tenant_id' => $this->tenantId,
            'locale' => $this->locale,
            'default_language' => $defaultLanguage ? $defaultLanguage->name : 'Turkish (default)'
        ]);
    }

    /**
     * Main search entry point
     */
    public function searchProducts(string $userMessage, array $options = []): array
    {
        // Ensure tenant context is initialized
        $this->ensureTenantContext();

        // Normalize message (basic cleanup only)
        $normalizedMessage = $this->normalizeUserMessage($userMessage);

        $cacheKey = "smart_search:{$this->tenantId}:" . md5($normalizedMessage);

        return Cache::remember($cacheKey, 300, function() use ($normalizedMessage, $userMessage, $options) {
            // STEP 1: Detect category (for Meilisearch filtering)
            $detectedCategory = $this->detectCategory($normalizedMessage);

            Log::info('🔍 Smart Product Search Started', [
                'tenant_id' => $this->tenantId,
                'user_query' => substr($userMessage, 0, 100),
                'detected_category' => $detectedCategory ? $detectedCategory['category_name'] : 'none'
            ]);

            // STEP 2: HYBRID SEARCH (Meilisearch 70% + Vector 30%)
            // ✅ Meilisearch handles: typo tolerance, fuzzy matching, tokenization, stopwords
            // ✅ No keyword extraction needed - pass user query directly!
            $hybridResults = $this->hybridSearch->search(
                $normalizedMessage,
                $detectedCategory['category_id'] ?? null,
                10
            );

            if (!empty($hybridResults)) {
                Log::info('✅ Hybrid Search SUCCESS', [
                    'results_count' => count($hybridResults),
                    'top_product' => $hybridResults[0]['product']['title'] ?? null,
                    'search_layer' => 'hybrid'
                ]);

                return $this->formatResults(
                    array_column($hybridResults, 'product'),
                    'hybrid',
                    $detectedCategory
                );
            }

            // No fallbacks - if Meilisearch can't find it, it doesn't exist
            Log::warning('❌ No products found', [
                'category_detected' => $detectedCategory ? 'yes' : 'no',
                'query' => substr($normalizedMessage, 0, 100)
            ]);

            return [];
        });
    }

    /**
     * Normalize user message
     *
     * ✅ BASITLEŞTIRILDI: Sadece temel temizlik
     * ⚠️ Meilisearch zaten typo tolerance, fuzzy matching, stopword handling yapıyor
     *    Bu yüzden gereksiz preprocessing KALDIRILIYOR
     */
    protected function normalizeUserMessage(string $message): string
    {
        // Sadece trim ve fazla boşlukları temizle
        return trim(preg_replace('/\s+/', ' ', $message));
    }

    /**
     * Extract searchable keywords from user message
     * 🆕 ENHANCED: Koruma listesi + teknik terimler + ölçü/yükseklik extraction
     */
    protected function extractKeywords(string $message): array
    {
        // 🔒 PROTECTED TERMS: Bu terimleri asla stopword olarak silme!
        $protectedTerms = [
            'AGM', 'Li-Ion', 'lithium', 'LPG', 'dizel', 'elektrik',
            // ⚠️ KRİTİK: Voltage/batarya terimleri
            '48V', '48v', '24V', '24v', '12V', '12v', '36V', '36v', '80V', '80v',
            'volt', 'voltaj', 'batarya', 'akü', 'battery',
            // ⚠️ KRİTİK: "soğuk" kelimesinin tüm varyasyonları (typo tolerance)
            'soğuk', 'soguk', 'souk', 'depo', 'hava', 'soğuk depo', 'soguk depo',
            'soğuk hava', 'soguk hava', 'cold storage', 'freezer', 'dondurucu',
            'paslanmaz', 'stainless', 'gıda', 'gida', 'food', 'hijyenik', 'hijyen',
            'duplex', 'triplex', 'standart', 'serbest',
            'havalı', 'dolgu', 'superelastik',
            'otonom', 'AGV', 'AMR', 'SLAM',
            'reach', 'dar koridor'
        ];

        // Lowercase ve protected terms'i geçici işaretle
        $originalMessage = $message;
        $lowerMessage = mb_strtolower($message);

        // Protected terms'i placeholder ile değiştir (stopword'den korumak için)
        $protectedMap = [];
        foreach ($protectedTerms as $idx => $term) {
            $placeholder = "__PROTECTED_{$idx}__";
            $lowerMessage = str_ireplace($term, $placeholder, $lowerMessage);
            $protectedMap[$placeholder] = $term;
        }

        // ❌ Stopwords - ama daha az agresif
        $stopWords = [
            'ürün', 'ürününüz', 'link', 'linkini', 'istiyorum',
            'isterim', 'lütfen', 'hakkında', 'bilgi', 've', 'ile', 'için',
            'bir', 'bu', 'şu', 'o', 'ne', 'nedir', 'nasıl', 'kaç', 'hangi',
            'var', 'mı', 'mi', 'mu', 'mü', 'acaba', 'bana', 'bence',
            'verin', 'gösterin', 'atın', 'yollayın', 'gönder', 'söyle',
            'alo', 'merhaba', 'selam', 'iyi', 'günler', 'akşamlar', 'arıyorum'
            // ✅ REMOVED: 'lazım', 'gerekiyor' - bu kelimeler cümlenin parçası olabilir
        ];

        $lowerMessage = str_replace($stopWords, ' ', $lowerMessage);

        // Protected terms'i geri yükle
        foreach ($protectedMap as $placeholder => $term) {
            $lowerMessage = str_replace($placeholder, $term, $lowerMessage);
        }

        // Extract potential model numbers and technical terms
        preg_match_all('/[a-zA-Z0-9\-]+/', $lowerMessage, $matches);
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

        // 🆕 1. CAPACITY/WEIGHT EXTRACTION
        // ⚠️ KRİTİK: 1 ton = 1000 kg, 200 kg = 0.2 ton (2 ton DEĞİL!)
        preg_match_all('/(\d+\.?\d*)\s*(ton|kg|kilo|kilogram)/i', $originalMessage, $capacityMatches);
        if (!empty($capacityMatches[1])) {
            foreach ($capacityMatches[1] as $idx => $number) {
                $unit = $capacityMatches[2][$idx] ?? '';
                $numberValue = floatval($number);

                // ✅ TON → KG dönüşümü
                if (stripos($unit, 'ton') !== false) {
                    $keywords[] = ($numberValue * 1000) . 'kg';  // 2 ton → 2000kg
                    $keywords[] = $numberValue . 'ton';          // Ayrıca ton'u da ekle
                }
                // ✅ KG → Direkt ekle (dönüşüm YOK!)
                else {
                    $keywords[] = $numberValue . 'kg';           // 200 kg → 200kg (2 ton DEĞİL!)

                    // 🆕 Eğer 1000'den büyükse ton karşılığını da ekle
                    if ($numberValue >= 1000) {
                        $tonValue = $numberValue / 1000;
                        $keywords[] = $tonValue . 'ton';         // 2000 kg → 2 ton
                    }
                }
            }
        }

        // 🆕 2. HEIGHT/LENGTH EXTRACTION (metre, mm, cm)
        // "4.5 metre" → "4500mm"
        preg_match_all('/(\d+\.?\d*)\s*(metre|meter|m(?!\w))/i', $originalMessage, $heightMatches);
        if (!empty($heightMatches[1])) {
            foreach ($heightMatches[1] as $height) {
                $mm = floatval($height) * 1000;
                $keywords[] = $mm . 'mm';
                $keywords[] = $mm;
            }
        }

        // "1220 mm" veya "1220mm" → "1220mm"
        preg_match_all('/(\d{3,5})\s*mm/i', $originalMessage, $mmMatches);
        if (!empty($mmMatches[1])) {
            foreach ($mmMatches[1] as $mmValue) {
                $keywords[] = $mmValue . 'mm';
                $keywords[] = $mmValue;
            }
        }

        // 🆕 3. PROTECTED TERMS'i kesinlikle ekle
        foreach ($protectedTerms as $term) {
            if (stripos($originalMessage, $term) !== false) {
                $keywords[] = mb_strtolower($term);
            }
        }

        return array_values(array_unique(array_merge($keywords, $normalized)));
    }

    /**
     * 🆕 CATEGORY-SPECIFIC PARAMETER EXTRACTION
     * Kategori ID'ye göre özel parametreleri extract eder
     *
     * @param int $categoryId Kategori ID (1=Forklift, 2=Transpalet, vs.)
     * @param array $keywords Extract edilmiş keyword'ler
     * @param string $originalMessage Orijinal mesaj (fazladan kontrol için)
     * @return array ['capacity' => '2000kg', 'battery_type' => 'AGM', ...]
     */
    protected function extractCategoryParameters(int $categoryId, array $keywords, string $originalMessage): array
    {
        $extracted = [];
        $lowerMessage = mb_strtolower($originalMessage);

        // Kategori yoksa boş dön
        if (!isset(self::CATEGORY_PARAMETERS[$categoryId])) {
            return $extracted;
        }

        $categoryParams = self::CATEGORY_PARAMETERS[$categoryId]['parameters'];

        // Her parametre tipini kontrol et
        foreach ($categoryParams as $paramType => $expectedValues) {
            // Capacity için özel işlem (zaten kg'ye çevrilmiş)
            if ($paramType === 'capacity') {
                foreach ($keywords as $keyword) {
                    if (preg_match('/(\d+)kg/', $keyword, $matches)) {
                        $extracted['capacity'] = $keyword;
                        break;
                    }
                }
            }

            // Lift height / platform height
            if (in_array($paramType, ['lift_height', 'platform_height'])) {
                foreach ($keywords as $keyword) {
                    if (preg_match('/(\d+)mm/', $keyword, $matches)) {
                        $extracted[$paramType] = $keyword;
                        break;
                    }
                }
            }

            // Fork dimensions
            if (in_array($paramType, ['fork_length', 'fork_width'])) {
                foreach ($keywords as $keyword) {
                    if (preg_match('/(\d{3,4})mm/', $keyword)) {
                        $extracted[$paramType] = $keyword;
                        break;
                    }
                }
            }

            // Battery type (AGM, Li-Ion, kurşun-asit)
            if ($paramType === 'battery_type') {
                foreach (['agm', 'li-ion', 'lithium', 'kurşun-asit', 'kurşun', 'asit'] as $batteryTerm) {
                    if (stripos($lowerMessage, $batteryTerm) !== false) {
                        if (stripos($batteryTerm, 'agm') !== false) {
                            $extracted['battery_type'] = 'AGM';
                        } elseif (stripos($batteryTerm, 'li-ion') !== false || stripos($batteryTerm, 'lithium') !== false) {
                            $extracted['battery_type'] = 'Li-Ion';
                        } elseif (stripos($batteryTerm, 'kurşun') !== false) {
                            $extracted['battery_type'] = 'kurşun-asit';
                        }
                        break;
                    }
                }
            }

            // Usage area (soğuk depo, gıda, paslanmaz)
            if ($paramType === 'usage_area') {
                // ⚠️ KRİTİK: Typo tolerance - "soguk" ve "soğuk" her ikisini de kontrol et!
                if (stripos($lowerMessage, 'soğuk') !== false ||
                    stripos($lowerMessage, 'soguk') !== false ||
                    stripos($lowerMessage, 'souk') !== false ||
                    stripos($lowerMessage, 'cold') !== false) {
                    $extracted['usage_area'] = 'soğuk depo';
                }
                if (stripos($lowerMessage, 'gıda') !== false ||
                    stripos($lowerMessage, 'gida') !== false ||
                    stripos($lowerMessage, 'food') !== false) {
                    $extracted['usage_area'] = 'gıda';
                }
                if (stripos($lowerMessage, 'paslanmaz') !== false || stripos($lowerMessage, 'stainless') !== false) {
                    $extracted['usage_area'] = 'paslanmaz';
                }
            }

            // Mast type (duplex, triplex, standart, serbest)
            if ($paramType === 'mast_type') {
                foreach (['duplex', 'triplex', 'standart', 'serbest', 'free'] as $mastTerm) {
                    if (stripos($lowerMessage, $mastTerm) !== false) {
                        $extracted['mast_type'] = $mastTerm;
                        break;
                    }
                }
            }

            // Motor type
            if ($paramType === 'motor_type') {
                foreach (['elektrik', 'electric', 'dizel', 'diesel', 'lpg'] as $motorTerm) {
                    if (stripos($lowerMessage, $motorTerm) !== false) {
                        $extracted['motor_type'] = $motorTerm;
                        break;
                    }
                }
            }

            // Corridor width (dar koridor)
            if ($paramType === 'corridor_width') {
                if (stripos($lowerMessage, 'dar koridor') !== false || stripos($lowerMessage, 'dar') !== false) {
                    $extracted['corridor_width'] = 'dar koridor';
                }
            }
        }

        Log::info('🔍 Category Parameters Extracted', [
            'category_id' => $categoryId,
            'category_name' => self::CATEGORY_PARAMETERS[$categoryId]['name'] ?? 'Unknown',
            'extracted_params' => $extracted,
            'keywords_used' => $keywords
        ]);

        return $extracted;
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
     * 💡 YENİ YAKLAŞIM: Manuel filtreleme YAPMA! AI'a kategorideki TÜM ürünleri gönder!
     * AI semantic matching yapacak - "soguk" → "soğuk depo" bağlantısını kendisi kuracak
     */
    protected function searchByCategory(int $categoryId, array $keywords = [], array $extractedParams = []): array
    {
        Log::info('🔎 searchByCategory - AI-DRIVEN APPROACH', [
            'category_id' => $categoryId,
            'keywords' => $keywords,
            'extracted_params' => $extractedParams,
            'approach' => 'Send ALL products to AI, let AI do semantic matching!'
        ]);

        // 💡 YENİ YAKLAŞIM: Kategorideki TÜM ürünleri AI'a gönder (ilk 50)
        // AI'ın kendi semantic matching'i var!
        // "soguk" yazsa bile "soğuk depo" slug'unu bulabilir!
        $query = ShopProduct::where('is_active', true)
            ->where('category_id', $categoryId)
            ->select([
                'product_id', 'sku', 'title', 'slug', 'short_description',
                'category_id', 'base_price', 'technical_specs'
            ])
            ->with('category:category_id,title,slug');

        // 💡 YENİ YAKLAŞIM: FİLTRELEME YAPMA!
        // Kategorideki TÜM ürünleri AL, AI'a GÖNDER!
        // AI semantic matching yapacak - manuel typo matching gerekmez!

        // SADECE capacity için basit sıralama yap (opsiyonel)
        if (!empty($extractedParams['capacity'])) {
            $query->orderByRaw('CAST(SUBSTRING_INDEX(title, " ", 1) AS UNSIGNED) DESC');
        }

        $results = $query->limit(300)->get()->toArray(); // ← 300 ürün! AI TÜM kategoriyi görecek!

        Log::info('🤖 AI-DRIVEN SEARCH - ALL products sent to AI!', [
            'category_id' => $categoryId,
            'total_sent_to_AI' => count($results),
            'user_request_params' => $extractedParams,
            'note' => 'AI sees ALL category products - will do semantic matching!'
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
                'category_id', 'base_price', 'technical_specs'
            ])
            ->with('category:category_id,title,slug');

        // If category detected, filter by category
        if ($detectedCategory) {
            $query->where('category_id', $detectedCategory['category_id']);
        }

        // Search in SKU, title, or capacity
        $query->where(function($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                // Handle capacity keywords (e.g., "2000kg" → search for "2 ton" or "2.0 ton")
                if (preg_match('/(\d+)kg/', $keyword, $matches)) {
                    $kgValue = $matches[1];
                    $tonValue = $kgValue / 1000;

                    $q->orWhere('title', 'LIKE', '% ' . $tonValue . ' %ton%')
                      ->orWhere('title', 'LIKE', '% ' . $tonValue . ' %Ton%')
                      ->orWhere('title', 'LIKE', '%' . number_format($tonValue, 1) . ' %ton%')
                      ->orWhere('title', 'LIKE', '%' . number_format($tonValue, 1) . ' %Ton%');
                } else {
                    // Regular keyword search in SKU and title
                    $q->orWhere('sku', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('title', 'LIKE', '%' . $keyword . '%');
                }
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
