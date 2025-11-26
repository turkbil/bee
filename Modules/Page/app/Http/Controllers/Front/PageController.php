<?php
namespace Modules\Page\App\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Page\App\Models\Page;
use App\Services\ThemeService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Spatie\ResponseCache\Facades\ResponseCache;
use App\Services\ModuleSlugService;
use App\Traits\HasModuleAccessControl;
use App\Models\ModuleTenantSetting;
use App\Services\SeoMetaTagService;

class PageController extends Controller
{
    use HasModuleAccessControl;
    
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;

        // 🔒 MODÜL ERİŞİM KONTROLÜ
        $this->checkModuleAccess('Page');
    }

    /**
     * Ana sayfa için is_homepage = 1 olan sayfayı getirir
     */
    public function homepage(SeoMetaTagService $seoService)
    {
        // Aktif ve ana sayfa olarak işaretli sayfayı al
        $page = Page::where('is_homepage', true)
            ->where('is_active', true)
            ->first();

        // Homepage bulunamazsa basit hata mesajı göster
        if (!$page) {
            return response()->view('errors.no-homepage', [], 503);
        }

        // SEO meta tags'i ayarla
        view()->share('currentModel', $page);

        // Homepage products'ları çek (homepage_sort_order'a göre sıralı)
        $homepageProductsQuery = \Modules\Shop\App\Models\ShopProduct::where('show_on_homepage', true)
            ->where('is_active', true)
            ->with(['category', 'brand', 'media'])
            ->orderByRaw('COALESCE(homepage_sort_order, 999999) ASC')
            ->orderBy('product_id', 'desc')
            ->get();

        // N+1 sorunu çözümü: Tüm currency'leri tek sorguda çek
        $currencyIds = $homepageProductsQuery->pluck('currency_id')->unique()->filter();
        $currencies = \Modules\Shop\App\Models\ShopCurrency::whereIn('currency_id', $currencyIds)->get()->keyBy('currency_id');

        $homepageProducts = $homepageProductsQuery->map(function ($product) use ($currencies) {
                // Currency field (string) ve currency() relation çakışıyor
                // Önceden yüklenmiş currencies collection'dan al (N+1 çözümü)
                $currencyRelation = $product->currency_id ? ($currencies[$product->currency_id] ?? null) : null;
                $currencyCode = $product->getAttribute('currency') ?? 'TRY';

                // TRY conversion için exchange rate hesapla
                $exchangeRate = $currencyRelation ? $currencyRelation->exchange_rate : 1;
                $tryPrice = ($currencyCode !== 'TRY' && $exchangeRate > 0)
                    ? number_format($product->base_price * $exchangeRate, 0, ',', '.')
                    : null;

                // Old price (compare_at_price) - Otomatik hesaplama
                $compareAtPrice = $product->compare_at_price;

                // ✨ OTOMATIK İNDİRİM SİSTEMİ
                // Eğer compare_at_price yoksa veya base_price'dan küçükse, otomatik hesapla
                $autoDiscountPercentage = null;
                if (!$compareAtPrice || $compareAtPrice <= $product->base_price) {
                    // Hedef indirim yüzdesi (badge için - SABİT: %5, %10, %15, %20)
                    $autoDiscountPercentage = (($product->product_id % 4) * 5 + 5);

                    // Eski fiyatı hesapla (ters formül: old = new / (1 - discount))
                    $compareAtPrice = $product->base_price / (1 - ($autoDiscountPercentage / 100));
                }

                // Format compare price
                $formattedComparePrice = null;
                if ($compareAtPrice && $compareAtPrice > $product->base_price) {
                    $formattedComparePrice = $currencyRelation
                        ? $currencyRelation->formatPrice($compareAtPrice)
                        : number_format($compareAtPrice, 0, ',', '.') . ' ₺';
                }

                return [
                    'id' => $product->product_id,
                    'title' => $product->getTranslated('title', app()->getLocale()),
                    'description' => strip_tags($product->getTranslated('short_description', app()->getLocale()) ?? ''),
                    'url' => \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($product),
                    'price' => $product->base_price,
                    'currency' => $currencyCode,
                    'currency_symbol' => $currencyRelation ? $currencyRelation->symbol : '₺',
                    'formatted_price' => $currencyRelation ? $currencyRelation->formatPrice($product->base_price) : number_format($product->base_price, 0, ',', '.') . ' ₺',
                    'image' => $product->hasMedia('featured_image') ? thumb($product->getFirstMedia('featured_image'), 400, 400, ['quality' => 85, 'scale' => 0, 'format' => 'webp']) : null,
                    'category' => $product->category ? $product->category->getTranslated('title', app()->getLocale()) : null,
                    'category_icon' => $product->category->icon_class ?? 'fa-light fa-box',
                    'featured' => $product->is_featured ?? false,
                    'bestseller' => $product->is_bestseller ?? false,
                    'badges' => $product->badges ?? [], // Badge sistemi
                    'exchange_rate' => $exchangeRate,
                    'try_price' => $tryPrice,
                    'compare_at_price' => $compareAtPrice,
                    'formatted_compare_price' => $formattedComparePrice,
                    'auto_discount_percentage' => $autoDiscountPercentage, // SABİT: 5, 10, 15, 20
                ];
            });

        try {
            // ThemeService ile homepage view'ını al
            $viewPath = $this->themeService->getThemeViewPath('homepage', 'page');

            return view($viewPath, [
                'item' => $page,
                'is_homepage' => true,
                'homepageProducts' => $homepageProducts
            ]);
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());

            // Fallback homepage view'a yönlendir
            return view('page::themes.ixtif.homepage', [
                'item' => $page,
                'is_homepage' => true,
                'homepageProducts' => $homepageProducts
            ]);
        }

    }

    public function index()
    {
        $items = Page::where('is_active', true)
            ->where('is_homepage', false)
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        // Modül title'ını al
        $moduleTitle = $this->getModuleTitle('Page');

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('index', 'page');
            return view($viewPath, compact('items', 'moduleTitle'));
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('page::front.index', compact('items', 'moduleTitle'));
        }
    }

    public function clearCache()
    {
        try {
            // ⚠️ SAFE CACHE CLEAR - config:clear KALDIRILDI (sistem çöker!)
            // View ve Response cache temizle (kullanıcı değişikliklerini görmek için yeterli)

            Artisan::call('view:clear');

            // Response Cache temizle
            if (class_exists('Spatie\ResponseCache\Facades\ResponseCache')) {
                ResponseCache::clear();
            }

            return response()->json([
                'success' => true,
                'message' => 'Cache başarıyla temizlendi'
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
              ->header('Pragma', 'no-cache')
              ->header('Expires', '0');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache temizleme hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($slug, $is_homepage_context = false, SeoMetaTagService $seoService = null)
    {
        // Debug log ekle
        Log::info('🔍 PageController::show called', [
            'slug' => $slug,
            'request_url' => request()->fullUrl(),
            'app_locale' => app()->getLocale(),
            'is_homepage_context' => $is_homepage_context
        ]);
        
        // Aktif dili al
        $currentLocale = app()->getLocale();
        
        // SADECE aktif dilde slug ara - locale-aware
        $item = Page::where('is_active', true)
            ->whereJsonContains("slug->{$currentLocale}", $slug)
            ->first();
            
        // Bulunamazsa 404
        if (!$item) {
            // Mevcut dilde bulunamadı, tüm dillerde ara (fallback)
            $allLocales = \App\Services\TenantLanguageProvider::getActiveLanguageCodes();
            
            foreach ($allLocales as $locale) {
                if ($locale === $currentLocale) {
                    continue; // Zaten aradık
                }
                
                $item = Page::where('is_active', true)
                    ->whereJsonContains("slug->{$locale}", $slug)
                    ->first();
                    
                if ($item) {
                    // Farklı dilde bulundu, ama kullanıcının seçtiği dilde göster (fallback content ile)
                    // Redirect etmek yerine mevcut locale'de göster
                    break; // Döngüden çık ve sayfayı göster
                }
            }
            
            // Döngü bittikten sonra hala bulunamadıysa 404
            if (!$item) {
                Log::warning("Page not found in any language", [
                    'slug' => $slug,
                    'searched_locales' => $allLocales
                ]);
                abort(404, "Page not found for slug '{$slug}'");
            }
        }
        
        // Canonical URL kontrolü - doğru slug kullanılıyor mu?
        $expectedSlug = $item->getTranslated('slug', $currentLocale);
        Log::info('🔍 Canonical URL check', [
            'slug' => $slug,
            'expectedSlug' => $expectedSlug,
            'currentLocale' => $currentLocale,
            'will_redirect' => $slug !== $expectedSlug
        ]);
        
        if ($slug !== $expectedSlug) {
            $redirectUrl = $this->generatePageUrl($item, $currentLocale);
            Log::info('🔄 Canonical redirect', [
                'from' => request()->fullUrl(),
                'to' => $redirectUrl
            ]);
            // Yanlış slug ile erişim, doğru URL'e redirect
            return redirect()->to($redirectUrl);
        }

        // Eğer bu sayfa veritabanında ana sayfa olarak işaretlenmişse ($item->is_homepage == true)
        // VE bu 'show' metodu, ana sayfa route'u (`homepage()` metodu) tarafından çağrılmadıysa
        // (yani $is_homepage_context == false ise, bu doğrudan slug ile erişim demektir),
        // REDIRECT YERINE CANONICAL URL KULLAN (Google SEO için)
        if ($item->is_homepage && !$is_homepage_context) {
            // Canonical URL'i homepage olarak belirt
            $canonicalUrl = route('home');
            Log::info('🔗 Setting canonical URL for homepage slug access', [
                'slug' => $slug,
                'canonical_url' => $canonicalUrl
            ]);
            // Canonical URL'i view'a paylaş
            view()->share('customCanonicalUrl', $canonicalUrl);
        }

        // SEO meta tags için model'i global olarak paylaş
        view()->share('currentModel', $item);

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('show', 'page');
            // View'a $item ve $is_homepage_context değişkenlerini gönderiyoruz.
            // View içinde $is_homepage_context değişkeni $is_homepage olarak erişilebilir olacak şekilde ayarlıyoruz.
            return view($viewPath, ['item' => $item, 'is_homepage' => $is_homepage_context]);
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('page::front.show', ['item' => $item, 'is_homepage' => $is_homepage_context]);
        }
    }
    
    /**
     * Sayfa için locale-aware URL oluştur
     */
    protected function generatePageUrl(Page $page, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $page->getTranslated('slug', $locale);
        
        // Modül slug'ını al (tenant tarafından özelleştirilebilir)
        $moduleSlug = ModuleSlugService::getSlug('Page', 'show');
        
        // Varsayılan dil kontrolü
        $defaultLocale = get_tenant_default_locale();
        
        if ($locale === $defaultLocale) {
            // Varsayılan dil için prefix yok
            return url("/{$moduleSlug}/{$slug}");
        }
        
        // Diğer diller için prefix ekle
        return url("/{$locale}/{$moduleSlug}/{$slug}");
    }

    /**
     * Modül title'ını al - settings tablosundan varsa onu, yoksa fallback
     */
    private function getModuleTitle(string $moduleName): string
    {
        $currentLocale = app()->getLocale();
        
        // ModuleTenantSetting'den title al
        $setting = ModuleTenantSetting::where('module_name', $moduleName)->first();
        
        if ($setting && $setting->title && isset($setting->title[$currentLocale])) {
            return $setting->title[$currentLocale];
        }
        
        // Fallback - ModuleSlugService'den default display name
        return ModuleSlugService::getDefaultModuleName($moduleName, $currentLocale);
    }
}