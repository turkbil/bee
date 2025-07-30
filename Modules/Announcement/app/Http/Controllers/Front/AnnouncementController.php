<?php
namespace Modules\Announcement\App\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Announcement\App\Models\Announcement;
use App\Services\ThemeService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Services\ModuleSlugService;
use App\Traits\HasModuleAccessControl;

class AnnouncementController extends Controller
{
    use HasModuleAccessControl;
    
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
        
        // 🔒 MODÜL ERİŞİM KONTROLÜ
        $this->checkModuleAccess('Announcement');
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
        // Aktif dili al
        $currentLocale = app()->getLocale();
        
        // Eğer sayısal ise direkt ID ile ara
        if (is_numeric($slug)) {
            $item = Announcement::where('announcement_id', $slug)
                ->where('is_active', true)
                ->first();
        } else {
            // SADECE aktif dilde slug ara - locale-aware
            $item = Announcement::where('is_active', true)
                ->whereJsonContains("slug->{$currentLocale}", $slug)
                ->first();
        }
        
        if (!$item) {
            // Mevcut dilde bulunamadı, tüm dillerde ara (fallback)
            $allLocales = array_column(available_tenant_languages(), 'code');
            
            foreach ($allLocales as $locale) {
                if ($locale === $currentLocale) {
                    continue; // Zaten aradık
                }
                
                $item = Announcement::where('is_active', true)
                    ->whereJsonContains("slug->{$locale}", $slug)
                    ->first();
                    
                if ($item) {
                    // Farklı dilde bulundu, doğru URL'e redirect et
                    $correctUrl = $this->generateAnnouncementUrl($item, $locale);
                    return redirect()->to($correctUrl, 301); // 301 = Permanent redirect
                }
            }
            
            // Hiçbir dilde bulunamadı
            Log::warning("Announcement not found in any language", [
                'slug' => $slug,
                'searched_locales' => $allLocales
            ]);
            abort(404, "Announcement not found for slug '{$slug}'");
        }
        
        // Canonical URL kontrolü - doğru slug kullanılıyor mu?
        $expectedSlug = $item->getTranslated('slug', $currentLocale);
        if (!is_numeric($slug) && $slug !== $expectedSlug) {
            // Yanlış slug ile erişim, doğru URL'e redirect
            return redirect()->to($this->generateAnnouncementUrl($item, $currentLocale));
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
    
    /**
     * Announcement için locale-aware URL oluştur
     */
    protected function generateAnnouncementUrl(Announcement $announcement, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $announcement->getTranslated('slug', $locale);
        
        // Modül slug'ını al (tenant tarafından özelleştirilebilir)
        $moduleSlug = ModuleSlugService::getSlug('Announcement', 'show');
        
        // Varsayılan dil kontrolü
        $defaultLocale = get_tenant_default_locale();
        
        if ($locale === $defaultLocale) {
            // Varsayılan dil için prefix yok
            return url("/{$moduleSlug}/{$slug}");
        }
        
        // Diğer diller için prefix ekle
        return url("/{$locale}/{$moduleSlug}/{$slug}");
    }
}