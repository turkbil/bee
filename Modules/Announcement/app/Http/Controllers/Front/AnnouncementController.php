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
        // Eğer sayısal ise direkt ID ile ara
        if (is_numeric($slug)) {
            $item = Announcement::where('announcement_id', $slug)
                ->where('is_active', true)
                ->first();
        } else {
            // String slug ise slug alanında ara (JSON field için)
            $item = Announcement::where('is_active', true)
                ->where(function($query) use ($slug) {
                    // Çoklu dil desteği ile slug arama
                    $query->whereRaw('JSON_CONTAINS(slug, ?)', [json_encode($slug)])
                          ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(slug, "$.tr")) = ?', [$slug])
                          ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(slug, "$.en")) = ?', [$slug]);
                })
                ->first();
        }
        
        if (!$item) {
            abort(404);
        }


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