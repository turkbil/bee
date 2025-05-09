<?php
namespace Modules\Page\App\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Page\App\Models\Page;
use App\Services\ThemeService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

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
        // Aktif ve ana sayfa olarak işaretli sayfayı al
        $page = Page::where('is_homepage', true)
            ->where('is_active', true)
            ->firstOrFail();

        // Standard gösterim mantığını kullan
        return $this->show($page->slug);
    }

    public function index()
    {
        $pages = Page::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('index', 'page');
            return view($viewPath, compact('pages'));
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('page::front.index', compact('pages'));
        }
    }

    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Sayfa görüntüleme sayısını arttır
        views($page)->record();

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('show', 'page');
            return view($viewPath, compact('page'));
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('page::front.show', compact('page'));
        }
    }
}