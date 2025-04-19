<?php
namespace Modules\Announcement\App\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Announcement\App\Models\Announcement;
use App\Services\ThemeService;
use Illuminate\Support\Facades\View;

class AnnouncementController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    public function index()
    {
        $announcements = Announcement::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('index', 'announcement');
            return view($viewPath, compact('announcements'));
        } catch (\Exception $e) {
            // Hatayı logla
            \Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('announcement::front.index', compact('announcements'));
        }
    }

    public function show($slug)
    {
        $announcement = Announcement::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Duyuru görüntüleme sayısını arttır
        views($announcement)->record();

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('show', 'announcement');
            return view($viewPath, compact('announcement'));
        } catch (\Exception $e) {
            // Hatayı logla
            \Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('announcement::front.show', compact('announcement'));
        }
    }
}