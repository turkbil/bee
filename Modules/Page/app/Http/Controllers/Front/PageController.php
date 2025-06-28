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
        $isAuthenticated = auth()->check();
        $userId = $isAuthenticated ? auth()->id() : 'guest';
        
        \Log::info('🏠 HOMEPAGE CONTROLLER', [
            'app_locale' => app()->getLocale(),
            'session_site_locale' => session('site_locale'),
            'is_authenticated' => $isAuthenticated,
            'user_id' => $userId
        ]);
        
        // Aktif ve ana sayfa olarak işaretli sayfayı al
        $page = Page::where('is_homepage', true)
            ->where('is_active', true)
            ->firstOrFail();
            
        \Log::info('📄 PAGE CONTENT DEBUG', [
            'page_id' => $page->page_id,
            'title_raw' => $page->getRawOriginal('title'),
            'title_translated' => $page->getTranslated('title', app()->getLocale()),
            'available_locales' => $page->title ? array_keys($page->title) : 'none'
        ]);

        
        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('show', 'page');
            return view($viewPath, ['item' => $page, 'is_homepage' => true]);
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('page::front.show', ['item' => $page, 'is_homepage' => true]);
        }
    }

    public function index()
    {
        $items = Page::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('index', 'page');
            return view($viewPath, compact('items'));
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('page::front.index', compact('items'));
        }
    }

    public function show($slug, $is_homepage_context = false)
    {
        // JSON slug arama - tüm dillerde ara
        $item = Page::where('is_active', true)
            ->where(function($query) use ($slug) {
                $query->whereJsonContains('slug->tr', $slug)
                      ->orWhereJsonContains('slug->en', $slug)
                      ->orWhereJsonContains('slug->ar', $slug);
            })
            ->firstOrFail();

        // Eğer bu sayfa veritabanında ana sayfa olarak işaretlenmişse ($item->is_homepage == true)
        // VE bu 'show' metodu, ana sayfa route'u (`homepage()` metodu) tarafından çağrılmadıysa
        // (yani $is_homepage_context == false ise, bu doğrudan slug ile erişim demektir),
        // o zaman ana sayfa route'una yönlendir.
        if ($item->is_homepage && !$is_homepage_context) {
            // Yönlendirme yapıldığını loglayalım.
            Log::info("Page '{$slug}' is a designated homepage and accessed via its slug. Redirecting to the main homepage route.");
            // 'homepage' isimli bir route olduğunu ve PageController@homepage metoduna işaret ettiğini varsayıyoruz.
            return redirect()->route('home');
        }


        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('show', 'page');
            // View'a $item ve $is_homepage_context değişkenlerini gönderiyoruz.
            // View içinde $is_homepage_context değişkeni $is_homepage olarak erişilebilir olacak şekilde ayarlıyoruz.
            return view($viewPath, ['item' => $item, 'is_homepage' => $is_homepage_context]);
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('page::front.show', ['item' => $item, 'is_homepage' => $is_homepage_context]);
        }
    }
}