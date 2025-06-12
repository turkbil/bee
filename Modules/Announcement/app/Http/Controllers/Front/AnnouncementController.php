<?php
namespace Modules\Announcement\App\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Announcement\App\Models\Announcement;
use App\Services\ThemeService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    public function index()
    {
        $items = Announcement::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('index', 'announcement');
            return view($viewPath, compact('items'));
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('announcement::front.index', compact('items'));
        }
    }

    public function show($slug)
    {
        $item = Announcement::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();


        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('show', 'announcement');
            return view($viewPath, compact('item'));
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('announcement::front.show', compact('item'));
        }
    }
}