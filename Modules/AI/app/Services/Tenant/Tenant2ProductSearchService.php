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

    /**
     * WORKFLOW V2: Search method for ProductSearchNode
     *
     * Uses HybridSearchService for proper sorting:
     * 1. Homepage products first
     * 2. Category sort_order
     * 3. Stock > 0
     * 4. Price > 0
     *
     * @param string $userMessage User's search query
     * @param int $limit Result limit
     * @param int|null $categoryId Optional category filter
     * @return array ['products' => Collection, 'products_found' => int, 'detected_category' => int|null]
     */
    public function search(string $userMessage, int $limit = 50, ?int $categoryId = null): array
    {
        // Detect category (optional - for filtering)
        $detectedCategory = $categoryId ?? $this->detectCategoryId($userMessage);

        // 🚨 Fiyat tabanlı sorgu mu? (en ucuz, ucuz bir şey, ekonomik)
        $isPriceQuery = $this->isPriceBasedQuery($userMessage);

        // 🚨 Model araması mı? (F4 201, EPL153, vb.)
        $extractedModel = $this->extractModelNumber($userMessage);

        Log::info('🏢 Tenant2: Product search (HybridSearch)', [
            'user_message' => mb_substr($userMessage, 0, 100),
            'detected_category' => $detectedCategory,
            'is_price_query' => $isPriceQuery,
            'extracted_model' => $extractedModel,
            'limit' => $limit
        ]);

        // Pass user message directly to HybridSearch
        // Meilisearch handles typo tolerance automatically (transpalet, trans palet, transpalat, etc.)
        $hybridResults = $this->hybridSearch->search($userMessage, $detectedCategory, $limit);

        if (empty($hybridResults)) {
            return [
                'products' => collect(),
                'products_found' => 0,
                'detected_category' => $detectedCategory
            ];
        }

        // 🚨 YEDEK PARÇA FİLTRESİ - Kullanıcı özellikle istemedikçe yedek parça gösterme!
        $isSparePartRequest = $this->isSparePartRequest($userMessage);

        // Yedek parça olarak kabul edilen ürün adları/kategorileri
        $sparePartKeywords = [
            // Elektronik parçalar
            'devirdaim', 'şamandıra', 'sensör', 'kablo', 'konvektör', 'converter',
            'geri ikaz', 'ikaz', 'korna', 'lamba', 'far', 'sinyal', 'anahtar',
            'şalter', 'kontaktör', 'röle', 'sigorta', 'soket', 'akü soketi',
            'voltaj', '12v', '24v', '12/24v', '48v', 'volt',

            // Mekanik parçalar
            'çatal', 'rulman', 'tekerlek', 'direksiyon', 'silindir', 'piston',
            'pompa', 'filtre', 'balata', 'fren', 'conta', 'kayış', 'zincir',
            'mil', 'yatak', 'kaplin', 'dişli', 'aks', 'şaft', 'burç', 'burc',
            'dingil', 'askı', 'makas', 'amortisör', 'rotil', 'rot',
            'menteşe', 'mentese', 'kaput', 'kapı', 'kilit', 'mandal',

            // Yapısal parçalar
            'sabitleme', 'levha', 'kızak', 'side shift', 'mast', 'çerçeve',
            'kapak', 'muhafaza', 'koruma', 'panjur', 'cam', 'ayna',
            'braket', 'bağlantı elemanı', 'civata', 'somun', 'pul',

            // Hidrolik parçalar
            'hidrolik', 'valf', 'hortum', 'keçe', 'segman', 'karter', 'tank',
            'manifold', 'bağlantı', 'nipel', 'rekor', 'o-ring', 'oring',

            // Ayar ve kalibrasyon parçaları
            'ayar', 'teflon', 'asansör ayar', 'kalibrasyon', 'spacer', 'shim',

            // Marka bazlı yedek parçalar (genellikle parça olarak satılır)
            'tcm', 'toyota parça', 'linde parça', 'hyster parça',

            // Genel kategoriler
            'motor yedek', 'yedek parça', 'spare', 'aksesuar', 'parça',
            'tamir', 'onarım', 'servis', 'bakım kiti'
        ];

        // Convert hybrid results to collection
        $products = collect();
        foreach ($hybridResults as $result) {
            $productData = $result['product'];
            $product = ShopProduct::find($productData['product_id']);
            if ($product) {
                // Yedek parça filtresi uygula
                if (!$isSparePartRequest) {
                    $productTitle = mb_strtolower($product->title['tr'] ?? '');
                    $isSpare = false;

                    foreach ($sparePartKeywords as $keyword) {
                        if (str_contains($productTitle, $keyword)) {
                            $isSpare = true;
                            break;
                        }
                    }

                    // Yedek parça ise atla
                    if ($isSpare) {
                        Log::debug('🚫 Yedek parça filtrelendi', ['product' => $product->title['tr']]);
                        continue;
                    }
                }

                $products->push($product);
            }
        }

        // 🚨 POST-PROCESSING: Model eşleştirme ve fiyat sıralaması

        // 1. Model araması varsa, exact match'leri öne al
        if ($extractedModel && $products->isNotEmpty()) {
            $products = $products->sortByDesc(function ($product) use ($extractedModel) {
                $title = mb_strtolower($product->title['tr'] ?? '');
                $model = mb_strtolower($extractedModel);

                // Exact match en yüksek skor
                if (str_contains($title, $model)) {
                    // "F4 201" içeriyorsa 100 puan
                    return 100;
                }
                // Kısmi eşleşme (sadece "F4" içeriyorsa ama "F4 201" aramışsa)
                $baseModel = preg_replace('/\s*\d+$/', '', $model); // "F4 201" -> "F4"
                if ($baseModel !== $model && str_contains($title, $baseModel)) {
                    return 50;
                }
                return 0;
            })->values();

            Log::info('🎯 Model match sorting applied', [
                'model' => $extractedModel,
                'first_product' => $products->first()?->title['tr'] ?? null
            ]);
        }

        // 2. Fiyat tabanlı sorgu ise, fiyata göre sırala
        if ($isPriceQuery && $products->isNotEmpty()) {
            // "En pahalı" sorgusu mu?
            $isMostExpensive = str_contains(mb_strtolower($userMessage), 'en pahalı');

            if ($isMostExpensive) {
                // En pahalı önce (DESC)
                $products = $products->sortByDesc(function ($product) {
                    if (!$product->base_price || $product->base_price <= 0) {
                        return 0; // Fiyatsız ürünleri en sona at
                    }
                    return $product->base_price;
                })->values();

                Log::info('💰 Price sorting applied (most expensive first)', [
                    'first_product' => $products->first()?->title['tr'] ?? null,
                    'first_price' => $products->first()?->base_price ?? 0
                ]);
            } else {
                // En ucuz önce (ASC)
                $products = $products->sortBy(function ($product) {
                    // Fiyatsız ürünleri en sona at
                    if (!$product->base_price || $product->base_price <= 0) {
                        return PHP_INT_MAX;
                    }
                    return $product->base_price;
                })->values();

                Log::info('💰 Price sorting applied (cheapest first)', [
                    'first_product' => $products->first()?->title['tr'] ?? null,
                    'first_price' => $products->first()?->base_price ?? 0
                ]);
            }
        }

        Log::info('✅ Tenant2: Products found (HybridSearch)', [
            'count' => $products->count(),
            'category_filtered' => $detectedCategory ? 'YES' : 'NO',
            'is_price_query' => $isPriceQuery,
            'first_product' => $products->first()?->title['tr'] ?? null
        ]);

        return [
            'products' => $products,
            'products_found' => $products->count(),
            'detected_category' => $detectedCategory
        ];
    }

    /**
     * Fiyat tabanlı sorgu mu kontrol et
     * "en ucuz", "ucuz bir şey", "ekonomik", "uygun fiyatlı" vb.
     */
    protected function isPriceBasedQuery(string $message): bool
    {
        $message = mb_strtolower($message);

        $priceKeywords = [
            'en ucuz',
            'ucuz bir',
            'ucuz ürün',
            'ekonomik',
            'uygun fiyat',
            'bütçe',
            'hesaplı',
            'en uygun',
            'fiyat listesi',
            'en pahalı', // Bu da fiyat sorgusu, ama DESC sıralama gerekir
        ];

        foreach ($priceKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Model numarası çıkar
     * "F4 201 fiyatı" -> "F4 201"
     * "EPL153 var mı" -> "EPL153"
     */
    protected function extractModelNumber(string $message): ?string
    {
        $message = mb_strtolower($message);

        // Model patterns - iXTİF specific
        // F4, F4 201, EPL153, EFL352, CPD15, etc.
        $patterns = [
            '/\b(f4\s*\d+)\b/i',           // F4 201, F4 301
            '/\b(f4)\b/i',                  // F4
            '/\b(epl\s*\d+)\b/i',           // EPL153, EPL 153
            '/\b(efl\s*\d+)\b/i',           // EFL352, EFL 352
            '/\b(cpd\s*\d+)\b/i',           // CPD15, CPD 15
            '/\b(ept\s*\d+)\b/i',           // EPT20
            '/\b(efx\s*\d+)\b/i',           // EFX5
            '/\b(tdl\s*\d+)\b/i',           // TDL
            '/\b(wpl\s*\d+)\b/i',           // WPL
            '/\b(rpl\s*\d+)\b/i',           // RPL
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                // Boşlukları temizle
                return trim(preg_replace('/\s+/', ' ', $matches[1]));
            }
        }

        return null;
    }

    /**
     * Extract keywords from user message (TENANT 2 specific)
     */
    protected function extractKeywords(string $message): array
    {
        $keywords = [];
        $message = mb_strtolower($message);

        // Product type keywords - TENANT 2 (iXtif) specific
        $productTypes = [
            'transpalet', 'trans palet', 'palet jack',  // Transpalet synonyms
            'forklift', 'fork lift', 'portif',  // Forklift synonyms
            'istif', 'istif makinesi', 'stacker',
            'akülü', 'elektrikli', 'manuel', 'palet', 'platform',
            'li-ion', 'lityum', 'agm', 'jel akü',  // Battery types
            'kaldırıcı', 'yük', 'depo', 'lojistik', 'taşıyıcı',
            'makine', 'makina', 'ekipman', 'araç',
            'order picker', 'reach truck', 'otonom',
            'cpd', 'ept', 'epl', 'efl', 'tdl', 'wpl', 'rpl'  // Model prefixes
        ];

        foreach ($productTypes as $type) {
            if (str_contains($message, $type)) {
                $keywords[] = $type;
            }
        }

        // Extract capacity/tonnage (e.g., "1.5 ton", "2 ton")
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*ton/i', $message, $matches)) {
            $keywords[] = $matches[1] . ' ton';
        }

        return array_unique($keywords);
    }

    /**
     * Detect category ID from user message (TENANT 2 specific - iXtif categories)
     */
    protected function detectCategoryId(string $message): ?int
    {
        $message = mb_strtolower($message);

        // Category mapping - TENANT 2 (iXtif) specific
        $categoryMap = [
            'forklift' => 1,
            'fork lift' => 1,
            'portif' => 1,
            'transpalet' => 2,
            'trans palet' => 2,
            'palet jack' => 2,
            'palet' => 2,
            'istif' => 3,
            'istif makinesi' => 3,
            'stacker' => 3,
            'order picker' => 4,
            'sipariş toplama' => 4,
            'otonom' => 5,
            'reach truck' => 6,
        ];

        // Find category by keyword (longest match first)
        $detectedCategory = null;
        $longestMatch = 0;

        foreach ($categoryMap as $keyword => $categoryId) {
            if (str_contains($message, $keyword) && strlen($keyword) > $longestMatch) {
                $detectedCategory = $categoryId;
                $longestMatch = strlen($keyword);
            }
        }

        if ($detectedCategory) {
            Log::info('🎯 Tenant2: Category detected', [
                'keyword_match' => array_search($detectedCategory, $categoryMap),
                'category_id' => $detectedCategory
            ]);
        }

        return $detectedCategory;
    }

    // Ana kategori keyword'leri (priority HIGH)
    protected array $mainCategoryKeywords = [
        'forklift' => ['forklift', 'forklifts', 'akülü forklift', 'elektrikli forklift'],
        'transpalet' => ['transpalet', 'pallet truck', 'transpalet modeli', 'transpalet çeşitleri'],
        'istif-makinesi' => ['istif makinesi', 'istif', 'stacker', 'yük istif'],
        'reach-truck' => ['reach truck', 'reach', 'dar koridor', 'yüksek raf'],
        'siparis-toplama' => ['sipariş toplama', 'order picker', 'picking', 'komisyonlama'],
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
        // Settings'den iletişim bilgilerini al (TENANT-AWARE, hardcode YOK)
        $contactInfo = \App\Helpers\AISettingsHelper::getContactInfo();
        $phone = $contactInfo['phone'] ?? '';
        $whatsapp = $contactInfo['whatsapp'] ?? '';
        $email = $contactInfo['email'] ?? '';

        // WhatsApp clean format
        $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
        if (substr($cleanWhatsapp, 0, 1) === '0') {
            $cleanWhatsapp = '90' . substr($cleanWhatsapp, 1);
        }

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
> \"Detaylı fiyat teklifi için [{$phone}](tel:{$phone}) numaralı telefonu arayabilir veya [WhatsApp](https://wa.me/{$cleanWhatsapp}) üzerinden ulaşabilirsiniz.\"

**❌ ASLA YAPMA:**
- \"Bu ürünün fiyatı yok\"
- \"Fiyat belirsiz\"
- \"0 TL\"

**✅ DOĞRU ÖRNEK:**
```
[İXTİF CPD18FVL - Forklift](URL)
- 1.8 ton kapasite
- **Fiyat:** Müşteri temsilcilerimizle iletişime geçerek detaylı fiyat teklifi alabilirsiniz.
- **İletişim:** [{$phone}](tel:{$phone}) | [WhatsApp](https://wa.me/{$cleanWhatsapp})
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
\"Sipariş ve teslimat bilgisi için [{$phone}](tel:{$phone}) veya [WhatsApp](https://wa.me/{$cleanWhatsapp}) ile ulaşabilirsiniz.\"
```

**✅ DOĞRU ÖRNEK:**
```
[İXTİF EFL181 - Forklift](URL)
- 1.8 ton kapasite, Li-Ion batarya
- **Fiyat:** \$3,450 USD
- **Tedarik:** [{$phone}](tel:{$phone}) | [WhatsApp](https://wa.me/{$cleanWhatsapp})
```

### 3. HER İKİ DURUM VARSA (Fiyatsız + Stoksuz)
```
\"Fiyat ve tedarik süresi bilgisi için müşteri temsilcilerimizle iletişime geçebilirsiniz.\"
\"Detaylı bilgi için [{$phone}](tel:{$phone}) veya [WhatsApp](https://wa.me/{$cleanWhatsapp}) ile ulaşın.\"
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
                'keywords' => ['forklift', 'fork lift', 'portif', 'akülü forklift', 'elektrikli forklift',
                               'en pahalı forklift', 'en ucuz forklift', 'ucuz forklift', 'pahalı forklift']
            ],
            'transpalet' => [
                'id' => 2,
                'name' => 'Transpalet',
                'keywords' => ['transpalet', 'trans palet', 'palet jack', 'pallet truck', 'transpalet modeli', 'transpalet çeşitleri',
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
