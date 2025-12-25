<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

/**
 * Google Shopping Feed Controller
 * Google Merchant Center için RSS formatında ürün feed'i oluşturur
 * Route: /productfeed, /googlemerchant
 */
class GoogleShoppingFeedController extends Controller
{
    /**
     * Google Shopping Feed XML'sini oluştur ve döndür
     *
     * @return Response XML feed (application/xml)
     */
    public function index(): Response
    {
        // Tenant ayarlarından bilgileri al
        $companyName = setting('company_name') ?? setting('site_name') ?? 'İxtif';
        $siteUrl = url('/');
        $description = setting('site_description') ?? $companyName . ' - Google Shopping Feed';

        // LogoService'den tema logosunu al (header/footer'dan)
        $logoService = app(\App\Services\LogoService::class);
        $fallbackLogo = $logoService->getSchemaLogoUrl();

        // Tenant'ın varsayılan para birimini kontrol et
        $defaultCurrency = 'USD'; // Varsayılan
        $shopSettings = DB::connection('central')->table('shop_settings')->where('key', 'currency_primary')->first();
        if ($shopSettings) {
            $currencyData = json_decode($shopSettings->value, true);
            $defaultCurrency = $currencyData['code'] ?? 'USD';
        }

        // XML RSS yapısını başlat
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
        $xml .= '<channel>';
        $xml .= '<title>' . htmlspecialchars($companyName, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</title>';
        $xml .= '<link>' . htmlspecialchars($siteUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</link>';
        $xml .= '<description>' . htmlspecialchars($description, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</description>';

        try {
            // Veritabanından ürünleri al (Sadece var olan sütunları kullan)
            // NOT: migration'da olmayan sütunlar referans verme (ör. price_on_request, is_active)

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
                    'sp.current_stock',  // ✅ Stok miktarı için eklendi
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
                $slug = is_array($slugData) ? ($slugData['tr'] ?? $slugData['en'] ?? 'urun') : $product->slug;

                // JSON formatında depolanan açıklamayı çöz
                $descData = json_decode($product->short_description ?? '{}', true);
                $description = is_array($descData) ?
                    ($descData['tr'] ?? $descData['en'] ?? '') :
                    ($product->short_description ?? '');

                // HTML etiketlerini kaldır ve boşlukları temizle
                $description = strip_tags($description);
                $description = preg_replace('/\s+/', ' ', $description);
                $description = trim($description);

                // Google'ın limit'i (5000 karakter) kadar sınırla
                $description = mb_substr($description, 0, 5000);

                // Açıklama yoksa başlığı kullan
                if (empty($description)) {
                    $description = strip_tags($title);
                }

                // Ürün sayfasının URL'sini oluştur
                $productUrl = url('/shop/' . $slug);

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

                // Marka bilgisini al (ürünün marka'sı varsa kullan, yoksa şirket adı kullan)
                $brandName = $companyName;
                if ($product->brand_title) {
                    $brandData = json_decode($product->brand_title, true);
                    $brandName = is_array($brandData) ?
                        ($brandData['tr'] ?? $brandData['en'] ?? $companyName) :
                        $product->brand_title;
                }

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

                // Google Shopping formatında XML item öğesini oluştur
                $xml .= '<item>';
                $xml .= '<g:id>' . htmlspecialchars((string)$product->product_id, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:id>';
                $xml .= '<g:title>' . htmlspecialchars(substr($title, 0, 500), ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:title>';
                $xml .= '<g:description>' . htmlspecialchars($description, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:description>';
                $xml .= '<g:link>' . htmlspecialchars($productUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:link>';

                // Görselleri ekle (ilk = primary, geriye kalanlar = additional)
                // 🔄 WebP'leri PNG'ye dönüştür (Thumbmaker ile)
                foreach ($imageUrls as $imageUrl) {
                    // Google desteklenen formatlar: JPG, JPEG, PNG, GIF, BMP, TIF (WebP DEĞIL!)
                    // WebP dosyalarını Thumbmaker aracılığıyla PNG'ye dönüştür
                    if (preg_match('/\.webp$/i', $imageUrl)) {
                        // WebP URL'i PNG'ye dönüştür: /thumbmaker?src=...&w=500&f=png
                        $imageUrl = url('/thumbmaker?src=' . urlencode($imageUrl) . '&w=500&f=png');
                    }
                    $xml .= '<g:image_link>' . htmlspecialchars($imageUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:image_link>';
                }

                $xml .= '<g:price>' . $price . ' ' . $currency . '</g:price>';
                $xml .= '<g:availability>in stock</g:availability>';

                // ✅ Stok miktarı ekle (minimum 1) - 25.12.2025
                // Google Shopping için opsiyonel ama önerilen field
                // Gerçek stok 0 olsa bile minimum 1 göster
                $stockQuantity = isset($product->current_stock) && $product->current_stock > 0
                    ? (int)$product->current_stock
                    : 1;
                $xml .= '<g:quantity>' . $stockQuantity . '</g:quantity>';

                $xml .= '<g:condition>' . htmlspecialchars($product->condition ?? 'new', ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:condition>';
                $xml .= '<g:brand>' . htmlspecialchars($brandName, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:brand>';

                // ✅ Google Product Category - 25.12.2025
                // 1167 = Business & Industrial > Material Handling > Forklifts
                // https://support.google.com/merchants/answer/6324436
                $xml .= '<g:google_product_category>1167</g:google_product_category>';

                // ✅ Product Type (Kendi kategori yapısı) - 25.12.2025
                // Kampanya filtreleme için kullanılır
                // TODO: Kategori sistemi varsa buraya eklenecek
                // Şimdilik genel kategori olarak "Material Handling Equipment" kullanıyoruz
                $xml .= '<g:product_type>Material Handling Equipment &gt; Forklifts</g:product_type>';
                $xml .= '</item>';
            }
        } catch (\Exception $e) {
            // Hata oluşursa XML'e yorum olarak ekle ve server log'a kaydet
            $xml .= '<!-- Hata: ' . htmlspecialchars($e->getMessage(), ENT_XML1 | ENT_QUOTES, 'UTF-8') . ' -->';
            \Log::error('GoogleShoppingFeed Hatası', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }

        // XML'i kapat
        $xml .= '</channel>';
        $xml .= '</rss>';

        // XML feed'i application/xml Content-Type ile döndür
        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
