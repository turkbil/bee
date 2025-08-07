<?php
namespace Modules\Portfolio\App\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use App\Services\ThemeService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Services\ModuleSlugService;
use App\Traits\HasModuleAccessControl;
use App\Models\ModuleTenantSetting;

class PortfolioController extends Controller
{
    use HasModuleAccessControl;
    
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
        
        // 🔒 MODÜL ERİŞİM KONTROLÜ
        $this->checkModuleAccess('Portfolio');
    }

    public function index()
    {
        $items = Portfolio::with(['category', 'media'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        // Modül title'ını al
        $moduleTitle = $this->getModuleTitle('Portfolio');

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('index', 'portfolio');
            return view($viewPath, compact('items', 'moduleTitle'));
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('portfolio::front.index', compact('items', 'moduleTitle'));
        }
    }

    public function show($slug)
    {
        // Aktif dili al
        $currentLocale = app()->getLocale();
        
        // Eğer sayısal ise direkt ID ile ara
        if (is_numeric($slug)) {
            $item = Portfolio::with('category')
                ->where('portfolio_id', $slug)
                ->where('is_active', true)
                ->first();
        } else {
            // Önce aktif dilde slug ara
            $item = Portfolio::with('category')
                ->where('is_active', true)
                ->whereJsonContains("slug->{$currentLocale}", $slug)
                ->first();
                
            // Bulunamazsa tüm dillerde ara (fallback)
            if (!$item) {
                $availableLanguages = \App\Services\TenantLanguageProvider::getActiveLanguageCodes();
                
                foreach ($availableLanguages as $langCode) {
                    $item = Portfolio::with('category')
                        ->where('is_active', true)
                        ->whereJsonContains("slug->{$langCode}", $slug)
                        ->first();
                        
                    if ($item) {
                        Log::info("Portfolio found with fallback language", [
                            'slug' => $slug,
                            'requested_locale' => $currentLocale,
                            'found_locale' => $langCode
                        ]);
                        break;
                    }
                }
            }
        }
        
        if (!$item) {
            Log::warning("Portfolio not found", [
                'slug' => $slug,
                'locale' => $currentLocale
            ]);
            abort(404, "Portfolio not found");
        }
        
        // Canonical URL kontrolü - doğru slug kullanılıyor mu?
        $expectedSlug = $item->getTranslated('slug', $currentLocale);
        if (!is_numeric($slug) && $slug !== $expectedSlug) {
            Log::info("Redirecting to canonical slug", [
                'requested' => $slug,
                'canonical' => $expectedSlug,
                'locale' => $currentLocale
            ]);
            // Yanlış slug ile erişim, doğru URL'e redirect
            return redirect()->to($this->generatePortfolioUrl($item, $currentLocale));
        }


        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('show', 'portfolio');
            return view($viewPath, compact('item'));
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('portfolio::front.show', compact('item'));
        }
    }
    
    public function category($slug)
    {
        // Aktif dili al
        $currentLocale = app()->getLocale();
        
        // Eğer sayısal ise direkt ID ile ara
        if (is_numeric($slug)) {
            $category = PortfolioCategory::where('portfolio_category_id', $slug)
                ->where('is_active', true)
                ->firstOrFail();
        } else {
            // Önce aktif dilde slug ara
            $category = PortfolioCategory::where('is_active', true)
                ->whereJsonContains("slug->{$currentLocale}", $slug)
                ->first();
                
            // Bulunamazsa tüm dillerde ara (fallback)
            if (!$category) {
                $availableLanguages = \App\Services\TenantLanguageProvider::getActiveLanguageCodes();
                
                foreach ($availableLanguages as $langCode) {
                    $category = PortfolioCategory::where('is_active', true)
                        ->whereJsonContains("slug->{$langCode}", $slug)
                        ->first();
                        
                    if ($category) {
                        Log::info("Category found with fallback language", [
                            'slug' => $slug,
                            'requested_locale' => $currentLocale,
                            'found_locale' => $langCode
                        ]);
                        break;
                    }
                }
            }
                
            if (!$category) {
                Log::warning("Category not found in any language", [
                    'slug' => $slug,
                    'requested_locale' => $currentLocale,
                    'checked_languages' => \App\Services\TenantLanguageProvider::getActiveLanguageCodes()
                ]);
                abort(404, "Category not found");
            }
        }
            
        $items = Portfolio::with(['category', 'media'])
            ->where('portfolio_category_id', $category->portfolio_category_id)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('category', 'portfolio');
            return view($viewPath, compact('category', 'items'));
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('portfolio::front.category', compact('category', 'items'));
        }
    }
    
    /**
     * Portfolio için locale-aware URL oluştur
     */
    protected function generatePortfolioUrl(Portfolio $portfolio, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $portfolio->getTranslated('slug', $locale);
        
        // Modül slug'ını al (tenant tarafından özelleştirilebilir)
        $moduleSlug = ModuleSlugService::getSlug('Portfolio', 'show');
        
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