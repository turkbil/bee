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

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('index', 'portfolio');
            return view($viewPath, compact('items'));
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('portfolio::front.index', compact('items'));
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
            // Sadece aktif dilde slug ara
            $item = Portfolio::with('category')
                ->where('is_active', true)
                ->whereJsonContains("slug->{$currentLocale}", $slug)
                ->first();
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
            // Sadece aktif dilde slug ara
            $category = PortfolioCategory::where('is_active', true)
                ->whereJsonContains("slug->{$currentLocale}", $slug)
                ->first();
                
            if (!$category) {
                Log::warning("Category not found", [
                    'slug' => $slug,
                    'locale' => $currentLocale
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
}