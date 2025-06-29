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
        // Eğer sayısal ise direkt ID ile ara
        if (is_numeric($slug)) {
            $item = Portfolio::with('category')
                ->where('portfolio_id', $slug)
                ->where('is_active', true)
                ->first();
        } else {
            // String slug ise slug alanında ara
            $item = Portfolio::with('category')
                ->where('is_active', true)
                ->where(function($query) use ($slug) {
                    // Basit slug araması
                    $query->where('slug', 'LIKE', '%"' . $slug . '"%');
                })
                ->first();
        }
        
        if (!$item) {
            abort(404);
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
        // Eğer sayısal ise direkt ID ile ara
        if (is_numeric($slug)) {
            $category = PortfolioCategory::where('portfolio_category_id', $slug)
                ->where('is_active', true)
                ->firstOrFail();
        } else {
            // String slug ise slug alanında ara
            $category = PortfolioCategory::where('is_active', true)
                ->where(function($query) use ($slug) {
                    // Basit slug araması
                    $query->where('slug', 'LIKE', '%"' . $slug . '"%');
                })
                ->firstOrFail();
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
}