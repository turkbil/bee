<?php
namespace Modules\Portfolio\App\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use App\Services\ThemeService;
use Illuminate\Support\Facades\View;

class PortfolioController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    public function index()
    {
        $portfolios = Portfolio::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('index', 'portfolio');
            return view($viewPath, compact('portfolios'));
        } catch (\Exception $e) {
            // Hatayı logla
            \Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('portfolio::front.index', compact('portfolios'));
        }
    }

    public function show($slug)
    {
        $portfolio = Portfolio::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('show', 'portfolio');
            return view($viewPath, compact('portfolio'));
        } catch (\Exception $e) {
            // Hatayı logla
            \Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('portfolio::front.show', compact('portfolio'));
        }
    }
    
    public function category($slug)
    {
        $category = PortfolioCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
            
        $portfolios = Portfolio::where('portfolio_category_id', $category->portfolio_category_id)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('category', 'portfolio');
            return view($viewPath, compact('category', 'portfolios'));
        } catch (\Exception $e) {
            // Hatayı logla
            \Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('portfolio::front.category', compact('category', 'portfolios'));
        }
    }
}