<?php
namespace Modules\Portfolio\App\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use App\Services\ThemeService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class PortfolioController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
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
        $item = Portfolio::with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();


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
        $category = PortfolioCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
            
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
}