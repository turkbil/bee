<?php

namespace App\Services\AI\TenantSpecific;

use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Http\Controllers\Front\ShopController;
use Illuminate\Support\Facades\Log;

/**
 * İXTİF ÖZEL DİNAMİK ÜRÜN ARAMA SERVİSİ
 *
 * Tenant: ixtif.com (ID: 2) ve ixtif.com.tr (ID: 3)
 *
 * Çalışma Mantığı:
 * 1. Kullanıcı mesajından keyword çıkar (forklift, transpalet, reach truck, vb.)
 * 2. Ana kategorilere ZOOM yap (yedek parça HARİÇ)
 * 3. İlgili ürünleri DB'den anlık çek
 * 4. Yedek parça sadece talep edilirse ara
 */
class IxtifProductSearchService
{
    protected string $locale;

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

    public function __construct()
    {
        $this->locale = app()->getLocale();
    }

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
        ];
    }

    /**
     * Kullanıcı mesajına göre dinamik ürün araması yap
     *
     * @param string $userMessage Kullanıcı mesajı
     * @return array Bulunan ürünler + metadata
     */
    public function searchProducts(string $userMessage): array
    {
        $startTime = microtime(true);

        // 1. Keyword extraction
        $detectedCategories = $this->extractCategoryKeywords($userMessage);
        $isSparePartRequest = $this->isSparePartRequest($userMessage);

        Log::info('🔍 IxtifProductSearchService - Keyword extraction', [
            'user_message' => mb_substr($userMessage, 0, 100),
            'detected_categories' => $detectedCategories,
            'is_spare_part' => $isSparePartRequest,
        ]);

        // 2. Ürün arama stratejisi
        $products = [];

        if (!empty($detectedCategories)) {
            // ZOOM: Kullanıcı spesifik kategori belirtti → O kategorilere odaklan
            $products = $this->searchByCategories($detectedCategories, $limit = 20);
            $searchType = 'category_zoom';
        } elseif ($isSparePartRequest) {
            // SPARE PARTS ONLY: Kullanıcı yedek parça istiyor
            $products = $this->searchSpareParts($userMessage, $limit = 15);
            $searchType = 'spare_parts';
        } else {
            // GENERAL: Kategori belirtilmedi → Ana ürünleri göster (yedek parça HARİÇ)
            $products = $this->searchMainProducts($limit = 30);
            $searchType = 'general';
        }

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        Log::info('✅ IxtifProductSearchService - Search completed', [
            'search_type' => $searchType,
            'products_found' => count($products),
            'execution_time_ms' => $executionTime,
        ]);

        return [
            'products' => $products,
            'search_type' => $searchType,
            'detected_categories' => $detectedCategories,
            'is_spare_part_request' => $isSparePartRequest,
            'execution_time_ms' => $executionTime,
            'total_found' => count($products),
        ];
    }

    /**
     * Kullanıcı mesajından kategori keyword'lerini çıkar
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
                'price_on_request',
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
                'price_on_request',
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
                'price_on_request',
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
        if ($product->price_on_request) {
            return [
                'available' => false,
                'on_request' => true,
                'message' => 'Fiyat sorunuz için lütfen iletişime geçin',
            ];
        }

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
}
