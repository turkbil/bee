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

class PageController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    /**
     * Ana sayfa için is_homepage = 1 olan sayfayı getirir
     */
    public function homepage()
    {
        $isAuthenticated = auth()->check();
        $userId = $isAuthenticated ? auth()->id() : 'guest';
        
        \Log::info('🏠 HOMEPAGE CONTROLLER', [
            'app_locale' => app()->getLocale(),
            'session_tenant_locale' => session('tenant_locale'),
            'is_authenticated' => $isAuthenticated,
            'user_id' => $userId
        ]);
        
        // Aktif ve ana sayfa olarak işaretli sayfayı al
        $page = Page::where('is_homepage', true)
            ->where('is_active', true)
            ->firstOrFail();
            
        \Log::info('📄 PAGE CONTENT DEBUG', [
            'page_id' => $page->page_id,
            'title_translated' => $page->getTranslated('title', app()->getLocale()),
            'available_locales' => $page->title ? array_keys($page->title) : 'none'
        ]);

        
        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('show', 'page');
            return view($viewPath, ['item' => $page, 'is_homepage' => true]);
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('page::front.show', ['item' => $page, 'is_homepage' => true]);
        }
        
    }

    public function index()
    {
        $items = Page::where('is_active', true)
            ->where('is_homepage', false)
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('index', 'page');
            return view($viewPath, compact('items'));
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('page::front.index', compact('items'));
        }
    }

    public function clearCache()
    {
        try {
            // Tüm cache türlerini temizle
            Cache::flush();
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
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

    public function show($slug, $is_homepage_context = false)
    {
        // Aktif dili al
        $currentLocale = app()->getLocale();
        
        // SADECE aktif dilde slug ara - locale-aware
        $item = Page::where('is_active', true)
            ->whereJsonContains("slug->{$currentLocale}", $slug)
            ->first();
            
        // Bulunamazsa 404
        if (!$item) {
            // Mevcut dilde bulunamadı, tüm dillerde ara (fallback)
            $allLocales = array_column(available_tenant_languages(), 'code');
            
            foreach ($allLocales as $locale) {
                if ($locale === $currentLocale) {
                    continue; // Zaten aradık
                }
                
                $item = Page::where('is_active', true)
                    ->whereJsonContains("slug->{$locale}", $slug)
                    ->first();
                    
                if ($item) {
                    // Farklı dilde bulundu, doğru URL'e redirect et
                    Log::info("Page found in different locale, redirecting", [
                        'slug' => $slug,
                        'found_in' => $locale,
                        'requested_in' => $currentLocale
                    ]);
                    
                    // Doğru dil ve slug ile URL oluştur
                    $correctUrl = $this->generatePageUrl($item, $locale);
                    return redirect()->to($correctUrl, 301); // 301 = Permanent redirect
                }
            }
            
            // Hiçbir dilde bulunamadı
            Log::warning("Page not found in any language", [
                'slug' => $slug,
                'searched_locales' => $allLocales
            ]);
            abort(404, "Page not found for slug '{$slug}'");
        }
        
        // Canonical URL kontrolü - doğru slug kullanılıyor mu?
        $expectedSlug = $item->getTranslated('slug', $currentLocale);
        if ($slug !== $expectedSlug) {
            Log::info("Redirecting to canonical slug", [
                'requested' => $slug,
                'canonical' => $expectedSlug,
                'locale' => $currentLocale
            ]);
            // Yanlış slug ile erişim, doğru URL'e redirect
            return redirect()->to($this->generatePageUrl($item, $currentLocale));
        }

        // Eğer bu sayfa veritabanında ana sayfa olarak işaretlenmişse ($item->is_homepage == true)
        // VE bu 'show' metodu, ana sayfa route'u (`homepage()` metodu) tarafından çağrılmadıysa
        // (yani $is_homepage_context == false ise, bu doğrudan slug ile erişim demektir),
        // o zaman ana sayfa route'una yönlendir.
        if ($item->is_homepage && !$is_homepage_context) {
            // Yönlendirme yapıldığını loglayalım.
            Log::info("Page '{$slug}' is a designated homepage and accessed via its slug. Redirecting to the main homepage route.");
            // 'homepage' isimli bir route olduğunu ve PageController@homepage metoduna işaret ettiğini varsayıyoruz.
            return redirect()->route('home');
        }


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
}