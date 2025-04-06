<?php

namespace Modules\Page\App\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Page\App\Models\Page;
use App\Services\ThemeService;
use Illuminate\Support\Facades\View;

class PageFrontController extends Controller
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

        $viewPath = $this->themeService->getThemeViewPath('index', 'page');
        
        // Hata ayıklama
        if (!View::exists($viewPath)) {
            \Log::warning("View bulunamadı: {$viewPath}. Varsayılan olarak themes.blank.index kullanılıyor.");
            $viewPath = 'themes.blank.index';
        }
        
        return view($viewPath, compact('pages'));
    }

    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $viewPath = $this->themeService->getThemeViewPath('show', 'page');
        
        // Hata ayıklama
        if (!View::exists($viewPath)) {
            \Log::warning("View bulunamadı: {$viewPath}. Varsayılan olarak themes.blank.show kullanılıyor.");
            $viewPath = 'themes.blank.show';
        }
        
        return view($viewPath, compact('page'));
    }
}