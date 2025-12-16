<?php
/**
 * Google Shopping Feed Endpoint
 * Google Merchant Center için ürün feed'i oluşturur
 * Her domain için otomatik tenant algılaması yapılır
 */

header('Content-Type: application/xml; charset=utf-8');

require '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Domain'i HTTP_HOST'tan al ve domains tablosundan kontrol et
$requestDomain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? null;
if (!$requestDomain) {
    header('Content-Type: text/plain; charset=utf-8');
    die('Hata: Domain bilgisi alınamadı');
}

// Domains tablosundan tenant'ı bul
$tenantDomain = \Stancl\Tenancy\Database\Models\Domain::where('domain', $requestDomain)->first();
if (!$tenantDomain || !$tenantDomain->tenant) {
    header('Content-Type: text/plain; charset=utf-8');
    die('Hata: Domain "' . htmlspecialchars($requestDomain) . '" bulunamadı');
}

// Tenant'ı aktifleştir
tenancy()->initialize($tenantDomain->tenant);

// Doğru domain'i domains tablosundan al (verified)
$domain = $tenantDomain->domain;
$baseUrl = 'https://' . $domain;

// Tenant'ın varsayılan para birimini kontrol et (TRY olarak gösterilecek mi?)
$defaultCurrency = 'USD'; // Varsayılan
$shopSettings = DB::connection('central')->table('shop_settings')->where('key', 'currency_primary')->first();
if ($shopSettings) {
    $currencyData = json_decode($shopSettings->value, true);
    $defaultCurrency = $currencyData['code'] ?? 'USD';
}

// Tenant ayarlarından şirket bilgilerini al
$companyName = setting('company_name') ?? setting('site_name') ?? 'Şirket';
$siteUrl = $baseUrl;
$description = setting('site_description') ?? $companyName . ' - Google Shopping Feed';

// LogoService'den tema logosunu al (header/footer'dan)
$logoService = app(\App\Services\LogoService::class);
$fallbackLogo = $logoService->getSchemaLogoUrl();

// XML feed yapısını başlat
$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
$xml .= '<channel>';
$xml .= '<title>' . htmlspecialchars($companyName, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</title>';
$xml .= '<link>' . htmlspecialchars($siteUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</link>';
$xml .= '<description>' . htmlspecialchars($description, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</description>';

// Veritabanından ürünleri al (aktif ve fiyatlandırılmış)
// NOT: Sadece var olan sütunları kullan, migration'da olmayan sütunlar ekleme!
try {
    // Döviz kurlarını al (para birimi dönüştürmesi gerekirse)
    $exchangeRates = [];
    if ($defaultCurrency !== 'USD') {
        $currencies = DB::table('shop_currencies')->get(['code', 'exchange_rate']);
        foreach ($currencies as $curr) {
            $exchangeRates[$curr->code] = (float)$curr->exchange_rate;
        }
    }

    $products = DB::table('shop_products as sp')
        ->leftJoin('shop_brands as sb', 'sp.brand_id', '=', 'sb.brand_id')
        ->select(
            'sp.product_id',
            'sp.title',
            'sp.slug',
            'sp.short_description',
            'sp.base_price',
            'sp.currency',
            'sp.condition',
            'sp.parent_product_id',
            'sb.title as brand_title'
        )
        ->whereNull('sp.deleted_at')  // Soft-deleted ürünleri hariç tut
        // Tüm ürünleri al (fiyatlı ve fiyatsız)
        ->get();

    foreach ($products as $product) {
        // JSON formatında depolanan ürün başlığını Türkçe/İngilizce olarak çöz
        $titleData = json_decode($product->title, true);
        $title = is_array($titleData) ? ($titleData['tr'] ?? $titleData['en'] ?? 'Ürün') : $product->title;

        // Fiyat boşsa başlığa (Fiyat Talep Et) ekle
        $hasPriceOnRequest = !$product->base_price || $product->base_price <= 0;
        if ($hasPriceOnRequest) {
            $title .= ' (Fiyat Talep Et)';
        }

        // JSON formatında depolanan URL slug'ını çöz
        $slugData = json_decode($product->slug, true);
        $slug = is_array($slugData) ? ($slugData['tr'] ?? $slugData['en'] ?? $product->product_id) : $product->slug;

        // Marka başlığını JSON'dan çöz
        $brandData = json_decode($product->brand_title ?? '{}', true);
        $brand = is_array($brandData) ? ($brandData['tr'] ?? $brandData['en'] ?? $companyName) : ($product->brand_title ?? $companyName);

        // Kısa açıklamayı JSON'dan çöz
        $descData = json_decode($product->short_description ?? '{}', true);
        $desc = '';
        if (is_array($descData)) {
            $desc = $descData['tr'] ?? $descData['en'] ?? '';
        } else {
            $desc = $product->short_description ?? '';
        }

        // HTML etiketlerini kaldır ve boşlukları temizle
        $desc = strip_tags($desc);
        $desc = preg_replace('/\s+/', ' ', $desc);
        $desc = trim($desc);

        // Google'ın limit'i (5000 karakter) kadar sınırla
        $desc = mb_substr($desc, 0, 5000);

        // Açıklama yoksa başlığı kullan
        if (empty($desc)) {
            $desc = strip_tags($title);
        }

        // Ürün URL'sini oluştur
        $productUrl = $baseUrl . '/shop/' . $slug;

        // Fiyat ve para birimini formatla
        $price = (float)$product->base_price;
        $currency = $product->currency ?? 'TRY';

        // Fiyat boşsa 1 TRY koy (Fiyat Talep Et ürünler için)
        if (!$price || $price <= 0) {
            $price = 1;
            $currency = 'TRY';
        } else {
            // Tenant'ın varsayılan para birimine dönüştür (gerekirse)
            if ($defaultCurrency !== 'USD' && $currency !== $defaultCurrency && isset($exchangeRates[$currency])) {
                $price = $price * $exchangeRates[$currency];
                $currency = $defaultCurrency;
            }
        }

        $price = number_format($price, 2, '.', '');

        // Ürün görselleri (hero + gallery) var mı kontrol et ve ekle
        // Medya koleksiyonları: 'hero' (ana görsel) + 'gallery' (galeri görselleri - max 20)
        $imageUrls = [];
        try {
            $productModel = \Modules\Shop\App\Models\ShopProduct::find($product->product_id);

            // 1. Kendi medyasını kontrol et (hero + gallery)
            if ($productModel && $productModel->hasMedia('hero')) {
                $heroUrl = $productModel->getFirstMediaUrl('hero');
                if ($heroUrl) {
                    $imageUrls[] = $heroUrl;
                }
            }

            if ($productModel && $productModel->hasMedia('gallery')) {
                $galleryMedia = $productModel->getMedia('gallery');
                foreach ($galleryMedia as $media) {
                    $galleryUrl = $media->getFullUrl();
                    if ($galleryUrl && count($imageUrls) < 10) {
                        $imageUrls[] = $galleryUrl;
                    }
                }
            }

            // 2. Eğer kendi medyası yoksa ve VARIANT ise: Ana ürünün medyasını kullan
            if (empty($imageUrls) && $product->parent_product_id) {
                $parentProduct = \Modules\Shop\App\Models\ShopProduct::find($product->parent_product_id);

                if ($parentProduct && $parentProduct->hasMedia('hero')) {
                    $heroUrl = $parentProduct->getFirstMediaUrl('hero');
                    if ($heroUrl) {
                        $imageUrls[] = $heroUrl;
                    }
                }

                if ($parentProduct && $parentProduct->hasMedia('gallery')) {
                    $galleryMedia = $parentProduct->getMedia('gallery');
                    foreach ($galleryMedia as $media) {
                        $galleryUrl = $media->getFullUrl();
                        if ($galleryUrl && count($imageUrls) < 10) {
                            $imageUrls[] = $galleryUrl;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Medya alınmazsa boş array
            $imageUrls = [];
        }

        // 3. Fallback logo: Görsel hala yoksa tema logosunu kullan
        if (empty($imageUrls) && $fallbackLogo) {
            $imageUrls[] = $fallbackLogo;
        }

        // Google Shopping XML item'ını oluştur
        $xml .= '<item>';
        $xml .= '<g:id>' . htmlspecialchars($product->product_id, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:id>';
        $xml .= '<g:title>' . htmlspecialchars(substr($title, 0, 500), ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:title>';
        $xml .= '<g:description>' . htmlspecialchars($desc, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:description>';
        $xml .= '<g:link>' . htmlspecialchars($productUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:link>';

        // Görselleri ekle (ilk = primary, geriye kalanlar = additional)
        // 🔄 WebP'leri PNG'ye dönüştür (Thumbmaker ile)
        foreach ($imageUrls as $imageUrl) {
            // Google desteklenen formatlar: JPG, JPEG, PNG, GIF, BMP, TIF (WebP DEĞIL!)
            // WebP dosyalarını Thumbmaker aracılığıyla PNG'ye dönüştür
            if (preg_match('/\.webp$/i', $imageUrl)) {
                // WebP URL'i PNG'ye dönüştür: /thumbmaker?src=...&w=500&f=png
                // Doğru domain kullan (ixtif.com, tuufi.com değil!)
                $imageUrl = $baseUrl . '/thumbmaker?src=' . urlencode($imageUrl) . '&w=500&f=png';
            }
            $xml .= '<g:image_link>' . htmlspecialchars($imageUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:image_link>';
        }

        $xml .= '<g:price>' . $price . ' ' . $currency . '</g:price>';
        $xml .= '<g:availability>in stock</g:availability>';
        $xml .= '<g:condition>' . htmlspecialchars($product->condition ?? 'new', ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:condition>';
        $xml .= '<g:brand>' . htmlspecialchars($brand, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:brand>';
        $xml .= '</item>';
    }
} catch (Exception $e) {
    // Hata oluşursa XML'e yorum olarak ekle (Google tarafında sorun bildirimi yapılabilir)
    $xml .= '<!-- Hata: ' . htmlspecialchars($e->getMessage(), ENT_XML1) . ' -->';
}

$xml .= '</channel>';
$xml .= '</rss>';

echo $xml;
