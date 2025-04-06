<?php

namespace Modules\Page\App\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Page\App\Models\Page;
use App\Services\ThemeService;
use Illuminate\Support\Facades\View;

class PageController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    public function index()
    {
        $pages = Page::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Debug için
        \Log::info("Loading pages index view");
        
        $viewPath = $this->themeService->getThemeViewPath('index', 'page');
        
        // Özel durumları kontrol et
        if (!View::exists($viewPath)) {
            \Log::warning("View not found: {$viewPath}, falling back to themes.blank.index");
            $viewPath = 'themes.blank.index';
            
            // Fallback da yoksa
            if (!View::exists($viewPath)) {
                \Log::error("Fallback view not found: {$viewPath}");
                abort(500, "Template not found");
            }
        }
        
        return view($viewPath, compact('pages'));
    }

    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Debug için
        \Log::info("Loading page show view for: {$slug}");
        
        $viewPath = $this->themeService->getThemeViewPath('show', 'page');
        
        // Özel durumları kontrol et
        if (!View::exists($viewPath)) {
            \Log::warning("View not found: {$viewPath}, falling back to themes.blank.show");
            $viewPath = 'themes.blank.show';
            
            // Fallback da yoksa
            if (!View::exists($viewPath)) {
                \Log::error("Fallback view not found: {$viewPath}");
                abort(500, "Template not found");
            }
        }
        
        return view($viewPath, compact('page'));
    }
}