<?php

namespace Modules\AI\App\Services\Tenant;

use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Http\Controllers\Front\ShopController;
use Illuminate\Support\Facades\Log;
use App\Services\AI\HybridSearchService;

/**
 * Tenant 2 & 3 (iXTİF) - Ürün Arama Servisi
 *
 * Tenant: ixtif.com (ID: 2) ve ixtif.com.tr (ID: 3)
 *
 * ⚠️ TENANT-SPECIFIC: Bu kategoriler sadece Tenant 2 için geçerli!
 *    Global ProductSearchService tüm 10000 tenant için kullanılır.
 *    Bu servis sadece iXtif (endüstriyel ekipman) kategorilerini içerir.
 *
 * Çalışma Mantığı:
 * 1. Kullanıcı mesajından keyword çıkar (forklift, transpalet, reach truck, vb.)
 * 2. Ana kategorilere ZOOM yap (yedek parça HARİÇ)
 * 3. İlgili ürünleri DB'den anlık çek
 * 4. Yedek parça sadece talep edilirse ara
 *
 * @package Modules\AI\App\Services\Tenant
 * @version 2.0
 */
class Tenant2ProductSearchService
{
    protected string $locale;
    protected HybridSearchService $hybridSearch;

    public function __construct(HybridSearchService $hybridSearch)
    {
        $this->locale = app()->getLocale();
        $this->hybridSearch = $hybridSearch;
    }

    // Ana kategori keyword'leri (priority HIGH)
    protected array $mainCategoryKeywords = [
        'forklift' => ['forklift', 'forklifts', 'akülü forklift', 'elektrikli forklift'],
        'transpalet' => ['transpalet', 'pallet truck', 'transpalet modeli', 'transpalet çeşitleri'],
        'istif-makinesi' => ['istif makinesi', 'istif', 'stacker', 'yük istif'],
        'reach-truck' => ['reach truck', 'reach', 'dar koridor', 'yüksek raf'],
        'siparis-toplama' => ['sipariş toplama', 'order picker', 'picking', 'komis yonlama'],
        'otonom' => ['otonom', 'autonomous', 'agv', 'otomatik', 'robot'],
    ];

    // Yedek parça keyword'leri (priority LOW - sadece talep edilirse)
    protected array $sparePartKeywords = [
        'yedek parça',
        'parça',
        'spare part',
        'aksesuar',
        'tekerlek',
        'piston',
        'silindir',
        'motor',
        'pompa',
        'filtre',
        'balata',
        'fren',
        'rulman',
    ];

    /**
     * İXTİF ÖZEL PROMPT'LARI
     *
     * Bu prompt'lar config/ai-tenant-rules.php yerine buradan alınacak
     * Böylece tüm iXtif özelleştirmeleri tek dosyada toplanır
     *
     * @return array
     */
    public function getCustomPrompts(): array
    {
        return [
            'product_recommendation' => "
## 🎯 İXTİF ÖZEL KURAL: ÜRÜN ÖNCELİKLENDİRME

**ANA ÜRÜNLER ÖNCELİKLİ:**
Kullanıcı genel bir talep belirtirse (örn: 'ürün arıyorum', 'ne var'), önce ANA ÜRÜNLERİ öner:
- Forklift
- Transpalet
- İstif Makinesi
- Reach Truck
- Sipariş Toplama Araçları
- Otonom Sistemler

**YEDEK PARÇA EN SON:**
Yedek parça ürünlerini SADECE şu durumlarda göster:
1. Kullanıcı açıkça 'yedek parça', 'parça', 'aksesuar' dedi
2. Kullanıcı spesifik parça adı söyledi (tekerlek, piston, motor vs.)
3. Ana ürün önerileri gösterildi, kullanıcı daha fazla detay istedi

**ÖRNEK DİYALOG:**
Kullanıcı: 'ürünleriniz neler?'
AI: 'Ana ürün kategorilerimiz: [Forklift], [Transpalet], [İstif Makinesi]... Hangi kategoride ürün arıyorsunuz?'

Kullanıcı: 'yedek parça arıyorum'
AI: 'Hangi ürün için yedek parça arıyorsunuz? [Forklift], [Transpalet] vs. için çok çeşitli parçalarımız var.'
",

            'concrete_product_links' => "
## 🔗 İXTİF ÖZEL KURAL: SOMUT ÜRÜN LİNKLERİ ZORUNLU

**🚨 KRİTİK: Kullanıcı ürün sorduğunda MUTLAKA somut ürünleri Markdown link ile listele!**

**❌ ASLA YAPMA:**
- Sadece genel bilgi verme
- 'Tüm Ürünler' linkini tek başına verme
- 'Modellerimiz var' deyip link verme

**✅ MUTLAKA YAP:**
- EN AZ 3 SOMUT ÜRÜN linki ver
- Her ürün için BAĞLAM BİLGİLERİ'ndeki tam URL'yi AYNEN KOPYALA
- Markdown format kullan: `- [Ürün Adı](URL) - Kısa açıklama`

**ÖRNEK DOĞRU YANIT:**
```
Harika! Transpalet modellerimiz:

- [İXTİF CPD15TVL - 1.5-2 Ton Li-Ion Forklift](https://ixtif.com/shop/ixtif-cpd15tvl-15-20-ton-li-ion-forklift) - Kompakt ve güçlü
- [İXTİF EFL181 - 1.8 Ton 48V Li-Ion Forklift](https://ixtif.com/shop/ixtif-efl181-18-ton-48v-li-ion-denge-agirlikli-forklift) - Denge ağırlıklı
- [İXTİF CPD18FVL - 1.8 Ton Li-Ion Forklift](https://ixtif.com/shop/ixtif-cpd18fvl-18-ton-li-ion-forklift) - Yüksek verimlilik

Hangi özellikler sizin için önemli?
```

**❌ ÖRNEK YANLIŞ YANIT:**
```
Transpalet modellerimiz mevcut. Tüm Ürünler sayfasına bakabilirsiniz.
```
",

            'dynamic_search_behavior' => "
## 🔍 İXTİF ÖZEL KURAL: DİNAMİK ÜRÜN ARAMA

**ÇALIŞMA MANTIĞI:**
Kullanıcı mesajını analiz et ve ilgili ürünleri BAĞLAM BİLGİLERİ'nden ara:

**ADIM 1:** Kullanıcı hangi kategoriyi arıyor?
- Forklift → Forklift ürünlerine odaklan
- Transpalet → Transpalet ürünlerine odaklan
- Reach truck → Reach truck ürünlerine odaklan
- İstif makinesi → İstif makinelerine odaklan
- Sipariş toplama → Sipariş toplama ürünlerine odaklan
- Otonom → Otonom sistemlere odaklan

**ADIM 2:** İlgili ürünleri Markdown link ile listele
**ADIM 3:** Kullanıcıya netleştirme sorusu sor

**ÖZEL DURUM - REACH TRUCK:**
Kullanıcı 'reach truck' derse:
1. BAĞLAM BİLGİLERİ'nde reach truck ara
2. Varsa mutlaka linkle göster
3. Yoksa: 'Reach truck modellerimiz için lütfen iletişime geçin' de

**ÖZEL DURUM - YEDEK PARÇA:**
Kullanıcı 'yedek parça' demediği sürece yedek parça önerme!
",

            'price_and_stock_policy' => "
## 💰 İXTİF ÖZEL KURAL: FİYAT VE STOK DURUMU POLİTİKASI

**🚨 KRİTİK KURALLAR - MUTLAKA UYULMALI:**

### 1. FİYATSIZ ÜRÜNLER (base_price = 0 veya price_on_request = true)
**Ürün gösterilir, ancak fiyat yerine şu mesaj verilir:**
> \"Fiyat bilgisi için lütfen müşteri temsilcilerimizle iletişime geçin.\"
> \"Detaylı fiyat teklifi için 0216 755 3 555 numaralı telefonu arayabilir veya iletişim bilgilerinizi bırakabilirsiniz.\"

**❌ ASLA YAPMA:**
- \"Bu ürünün fiyatı yok\"
- \"Fiyat belirsiz\"
- \"0 TL\"

**✅ DOĞRU ÖRNEK:**
```
[İXTİF CPD18FVL - Forklift](URL)
- 1.8 ton kapasite
- **Fiyat:** Müşteri temsilcilerimizle iletişime geçerek detaylı fiyat teklifi alabilirsiniz.
- **İletişim:** 0216 755 3 555
```

### 2. STOKTA OLMAYAN ÜRÜNLER (current_stock = 0)
**Ürün gösterilir, \"stokta yok\" DENİLMEZ!**

**❌ ASLA YAPMA:**
- \"Bu ürün stokta yok\"
- \"Stok tükendi\"
- \"Temin edilemez\"

**✅ DOĞRU MESAJ:**
```
\"Tedarik süresi ve stok bilgisi için lütfen müşteri hizmetlerimizle iletişime geçin.\"
\"Sipariş ve teslimat bilgisi için numaranızı bırakabilir veya 0216 755 3 555'i arayabilirsiniz.\"
```

**✅ DOĞRU ÖRNEK:**
```
[İXTİF EFL181 - Forklift](URL)
- 1.8 ton kapasite, Li-Ion batarya
- **Fiyat:** $3,450 USD
- **Tedarik:** Sipariş ve teslimat süresi için 0216 755 3 555'i arayabilirsiniz.
```

### 3. HER İKİ DURUM VARSA (Fiyatsız + Stoksuz)
```
\"Fiyat ve tedarik süresi bilgisi için müşteri temsilcilerimizle iletişime geçebilirsiniz.\"
\"Detaylı bilgi için 0216 755 3 555'i arayın veya iletişim bilgilerinizi bırakın.\"
```

**SONUÇ:** Tüm ürünler gösterilir, hiçbir ürün gizlenmez. AI, fiyat/stok eksikliğini nazikçe temsilci yönlendirmesi ile kapatır.
",
        ];
    }

    /**
     * Kullanıcı mesajına göre dinamik ürün araması yap
     *
     * 🔄 UPDATED: Global ProductSearchService ile aynı logic kullanır
     *             Sadece kategori detection tenant-specific
     *
     * @param string $userMessage Kullanıcı mesajı
     * @return array Bulunan ürünler + metadata
     */
    public function searchProducts(string $userMessage): array
    {
        $startTime = microtime(true);

        // 1. TENANT-SPECIFIC: Kategori detection
        $detectedCategory = $this->detectCategoryTenant2($userMessage);

        Log::info('🏢 Tenant2ProductSearchService - Category detection', [
            'user_message' => mb_substr($userMessage, 0, 100),
            'detected_category' => $detectedCategory ? $detectedCategory['category_name'] : 'none'
        ]);

        // 2. HYBRID SEARCH (Global ile aynı - Meilisearch + Vector)
        $hybridResults = $this->hybridSearch->search(
            $userMessage,
            $detectedCategory['category_id'] ?? null,
            100  // TÜM ürünleri getir, AI filtreleyecek
        );

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        if (!empty($hybridResults)) {
            Log::info('✅ Tenant2 Hybrid Search SUCCESS', [
                'results_count' => count($hybridResults),
                'top_product' => $hybridResults[0]['product']['title'] ?? null,
                'execution_time_ms' => $executionTime
            ]);

            // Add category labels to products (for ContextBuilderNode)
            $categoryMap = [
                1 => '[FORKLIFT] ',
                2 => '[TRANSPALET] ',
                3 => '[İSTİF MAKİNESİ] ',
                4 => '[SİPARİŞ TOPLAMA] ',
                5 => '[OTONOM] ',
                6 => '[REACH TRUCK] ',
            ];

            $products = array_column($hybridResults, 'product');
            foreach ($products as &$product) {
                $categoryId = $product['category_id'] ?? null;
                $product['_category_label'] = $categoryId && isset($categoryMap[$categoryId])
                    ? $categoryMap[$categoryId]
                    : '';
            }

            return [
                'products' => $products,
                'search_layer' => 'hybrid',
                'detected_category' => $detectedCategory,
                'total_found' => count($hybridResults),
                'execution_time_ms' => $executionTime
            ];
        }

        Log::warning('❌ No products found (Tenant2)', [
            'category_detected' => $detectedCategory ? 'yes' : 'no',
            'query' => substr($userMessage, 0, 100)
        ]);

        return [];
    }

    /**
     * Tenant 2 kategori detection (endüstriyel ekipman)
     *
     * @param string $message
     * @return array|null ['category_id' => int, 'category_name' => string]
     */
    protected function detectCategoryTenant2(string $message): ?array
    {
        $lowerMessage = mb_strtolower($message);

        // Tenant 2 kategori mapping (endüstriyel ekipman)
        $categoryMap = [
            'forklift' => [
                'id' => 1,
                'name' => 'Forklift',
                'keywords' => ['forklift', 'akülü forklift', 'elektrikli forklift',
                               'en pahalı forklift', 'en ucuz forklift', 'ucuz forklift', 'pahalı forklift']
            ],
            'transpalet' => [
                'id' => 2,
                'name' => 'Transpalet',
                'keywords' => ['transpalet', 'pallet truck', 'transpalet modeli', 'transpalet çeşitleri',
                               'en pahalı transpalet', 'en ucuz transpalet', 'ucuz transpalet', 'pahalı transpalet']
            ],
            'istif-makinesi' => [
                'id' => 3,
                'name' => 'İstif Makinesi',
                'keywords' => ['istif makinesi', 'istif', 'stacker']
            ],
            'siparis-toplama' => [
                'id' => 4,
                'name' => 'Sipariş Toplama',
                'keywords' => ['sipariş toplama', 'order picker', 'picking']
            ],
            'otonom' => [
                'id' => 5,
                'name' => 'Otonom Sistemler',
                'keywords' => ['otonom', 'autonomous', 'agv', 'otomatik', 'robot']
            ],
            'reach-truck' => [
                'id' => 6,
                'name' => 'Reach Truck',
                'keywords' => ['reach truck', 'reach', 'dar koridor']
            ],
        ];

        foreach ($categoryMap as $slug => $data) {
            foreach ($data['keywords'] as $keyword) {
                if (stripos($lowerMessage, $keyword) !== false) {
                    return [
                        'category_id' => $data['id'],
                        'category_name' => $data['name']
                    ];
                }
            }
        }

        return null; // Kategori bulunamadı
    }

    /**
     * Kullanıcı mesajından kategori keyword'lerini çıkar (DEPRECATED - eskisi)
     *
     * @param string $message
     * @return array Tespit edilen kategori slug'ları
     */
    protected function extractCategoryKeywords(string $message): array
    {
        $messageLower = mb_strtolower($message);
        $detected = [];

        foreach ($this->mainCategoryKeywords as $categorySlug => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($messageLower, mb_strtolower($keyword))) {
                    $detected[] = $categorySlug;
                    break; // Bu kategori bulundu, bir sonrakine geç
                }
            }
        }

        return array_unique($detected);
    }

    /**
     * Yedek parça talebi mi kontrol et
     *
     * @param string $message
     * @return bool
     */
    protected function isSparePartRequest(string $message): bool
    {
        $messageLower = mb_strtolower($message);

        foreach ($this->sparePartKeywords as $keyword) {
            if (str_contains($messageLower, mb_strtolower($keyword))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Belirli kategorilerdeki ürünleri ara (ZOOM)
     *
     * @param array $categorySlugs
     * @param int $limit
     * @return array
     */
    protected function searchByCategories(array $categorySlugs, int $limit = 20): array
    {
        // Kategorileri bul
        $categories = ShopCategory::where('is_active', true)
            ->where(function ($query) use ($categorySlugs) {
                foreach ($categorySlugs as $slug) {
                    $query->orWhere('slug->tr', 'like', "%{$slug}%")
                          ->orWhere('slug->en', 'like', "%{$slug}%");
                }
            })
            ->pluck('category_id');

        if ($categories->isEmpty()) {
            Log::warning('⚠️ No categories found for slugs', ['slugs' => $categorySlugs]);
            return [];
        }

        // O kategorilerdeki aktif ürünleri çek
        $products = ShopProduct::where('is_active', true)
            ->whereIn('category_id', $categories)
            ->with('category:category_id,title,slug')
            ->select([
                'product_id',
                'sku',
                'title',
                'slug',
                'short_description',
                'category_id',
                'base_price',
            ])
            ->take($limit)
            ->get();

        return $products->map(fn($p) => $this->formatProduct($p))->toArray();
    }

    /**
     * Yedek parça ara
     *
     * @param string $userMessage
     * @param int $limit
     * @return array
     */
    protected function searchSpareParts(string $userMessage, int $limit = 15): array
    {
        // Yedek parça kategorilerini bul
        $sparePartCategories = ShopCategory::where('is_active', true)
            ->where(function ($query) {
                $query->where('slug->tr', 'like', '%yedek-parca%')
                      ->orWhere('slug->tr', 'like', '%spare-parts%')
                      ->orWhere('slug->tr', 'like', '%parca%')
                      ->orWhere('slug->tr', 'like', '%aksesuar%')
                      ->orWhere('slug->en', 'like', '%spare%')
                      ->orWhere('slug->en', 'like', '%accessory%');
            })
            ->pluck('category_id');

        if ($sparePartCategories->isEmpty()) {
            Log::warning('⚠️ No spare part categories found');
            return [];
        }

        // Yedek parça ürünleri
        $products = ShopProduct::where('is_active', true)
            ->whereIn('category_id', $sparePartCategories)
            ->with('category:category_id,title,slug')
            ->select([
                'product_id',
                'sku',
                'title',
                'slug',
                'short_description',
                'category_id',
                'base_price',
            ])
            ->take($limit)
            ->get();

        return $products->map(fn($p) => $this->formatProduct($p))->toArray();
    }

    /**
     * Ana ürünleri ara (yedek parça HARİÇ)
     *
     * @param int $limit
     * @return array
     */
    protected function searchMainProducts(int $limit = 30): array
    {
        // Yedek parça kategorilerini bul (EXCLUDE)
        $sparePartCategories = ShopCategory::where('is_active', true)
            ->where(function ($query) {
                $query->where('slug->tr', 'like', '%yedek-parca%')
                      ->orWhere('slug->tr', 'like', '%spare-parts%')
                      ->orWhere('slug->tr', 'like', '%parca%')
                      ->orWhere('slug->tr', 'like', '%aksesuar%')
                      ->orWhere('slug->en', 'like', '%spare%')
                      ->orWhere('slug->en', 'like', '%accessory%');
            })
            ->pluck('category_id');

        // Ana ürünler (yedek parça HARİÇ)
        $query = ShopProduct::where('is_active', true)
            ->with('category:category_id,title,slug')
            ->select([
                'product_id',
                'sku',
                'title',
                'slug',
                'short_description',
                'category_id',
                'base_price',
            ]);

        if ($sparePartCategories->isNotEmpty()) {
            $query->whereNotIn('category_id', $sparePartCategories);
        }

        $products = $query->take($limit)->get();

        return $products->map(fn($p) => $this->formatProduct($p))->toArray();
    }

    /**
     * Ürünü AI context formatında hazırla
     *
     * @param ShopProduct $product
     * @return array
     */
    protected function formatProduct(ShopProduct $product): array
    {
        return [
            'id' => $product->product_id,
            'sku' => $product->sku,
            'title' => $this->translate($product->title),
            'short_description' => $this->translate($product->short_description),
            'category' => $product->category ? $this->translate($product->category->title) : null,
            'price' => $this->formatPrice($product),
            'url' => $this->getProductUrl($product),
        ];
    }

    /**
     * Product URL oluştur
     */
    protected function getProductUrl(ShopProduct $product): string
    {
        try {
            return ShopController::resolveProductUrl($product, $this->locale);
        } catch (\Exception $e) {
            $slug = ltrim($this->translate($product->slug), '/');
            return url('/shop/' . $slug);
        }
    }

    /**
     * Fiyat formatla
     */
    protected function formatPrice(ShopProduct $product): array
    {
        if ($product->base_price) {
            return [
                'available' => true,
                'amount' => $product->base_price,
                'formatted' => number_format($product->base_price, 2, ',', '.') . ' ' . ($product->currency ?? 'TRY'),
            ];
        }

        return [
            'available' => false,
            'on_request' => false,
        ];
    }

    /**
     * JSON multi-language çeviri
     */
    protected function translate($data): string
    {
        if (is_string($data)) {
            return $data;
        }

        if (is_array($data)) {
            $defaultLocale = get_tenant_default_locale();
            return $data[$this->locale] ?? $data[$defaultLocale] ?? $data['en'] ?? reset($data) ?? '';
        }

        return '';
    }

    /**
     * Detect user sentiment from message
     * (Copied from ProductSearchService for compatibility)
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

        // Rudeness detection
        $rudeWords = ['hemen', 'çabuk ol', 'acele et', 'yavaş'];
        foreach ($rudeWords as $word) {
            if (strpos($lowerMessage, $word) !== false && !$sentiment['is_polite']) {
                $sentiment['is_rude'] = true;
                $sentiment['tone'] = 'rude';
                break;
            }
        }

        // Confusion detection
        $confusionWords = ['anlamadım', 'ne demek', 'hangisi', 'fark nedir', 'bilmiyorum'];
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
