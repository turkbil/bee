<?php

namespace Modules\Announcement\App\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Announcement\App\Models\Announcement;
use App\Services\ThemeService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Spatie\ResponseCache\Facades\ResponseCache;
use App\Services\ModuleSlugService;
use App\Traits\HasModuleAccessControl;
use App\Models\ModuleTenantSetting;
use App\Services\SeoMetaTagService;

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

    /**
     * Ana sayfa desteği yok - Announcement modülünde homeannouncement özelliği kaldırıldı
     */
    public function homeannouncement(SeoMetaTagService $seoService)
    {
        // Ana sayfa özelliği Announcement modülünde desteklenmiyor
        abort(404);
    }

    public function index()
    {
        $items = Announcement::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        // Modül title'ını al
        $moduleTitle = $this->getModuleTitle('Announcement');

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('index', 'announcement');
            return view($viewPath, compact('items', 'moduleTitle'));
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());

            // Fallback view'a yönlendir
            return view('announcement::front.index', compact('items', 'moduleTitle'));
        }
    }

    public function clearCache()
    {
        try {
            // Tüm cache türlerini temizle
            Cache::flush();
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            // Response Cache temizle
            if (class_exists('Spatie\ResponseCache\Facades\ResponseCache')) {
                ResponseCache::clear();
            }

            return response()->json([
                'success' => true,
                'message' => 'Cache başarıyla temizlendi'
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache temizleme hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($slug, SeoMetaTagService $seoService = null)
    {
        // Debug log - sadece verbose logs aktifse
        if (config('announcement.debug.verbose_logs', false)) {
            Log::info('🔍 AnnouncementController::show called', [
                'slug' => $slug,
                'request_url' => request()->fullUrl(),
                'app_locale' => app()->getLocale()
            ]);
        }

        // Aktif dili al
        $currentLocale = app()->getLocale();

        // SADECE aktif dilde slug ara - locale-aware
        $item = Announcement::where('is_active', true)
            ->whereJsonContains("slug->{$currentLocale}", $slug)
            ->first();

        // Bulunamazsa 404
        if (!$item) {
            // Mevcut dilde bulunamadı, tüm dillerde ara (fallback)
            $allLocales = \App\Services\TenantLanguageProvider::getActiveLanguageCodes();

            foreach ($allLocales as $locale) {
                if ($locale === $currentLocale) {
                    continue; // Zaten aradık
                }

                $item = Announcement::where('is_active', true)
                    ->whereJsonContains("slug->{$locale}", $slug)
                    ->first();

                if ($item) {
                    // Farklı dilde bulundu, ama kullanıcının seçtiği dilde göster (fallback content ile)
                    // Redirect etmek yerine mevcut locale'de göster
                    break; // Döngüden çık ve sayfayı göster
                }
            }

            // Döngü bittikten sonra hala bulunamadıysa 404
            if (!$item) {
                Log::warning("Announcement not found in any language", [
                    'slug' => $slug,
                    'searched_locales' => $allLocales
                ]);
                abort(404, "Announcement not found for slug '{$slug}'");
            }
        }

        // Canonical URL kontrolü - doğru slug kullanılıyor mu?
        $expectedSlug = $item->getTranslated('slug', $currentLocale);

        if (config('announcement.debug.verbose_logs', false)) {
            Log::info('🔍 Canonical URL check', [
                'slug' => $slug,
                'expectedSlug' => $expectedSlug,
                'currentLocale' => $currentLocale,
                'will_redirect' => $slug !== $expectedSlug
            ]);
        }

        if ($slug !== $expectedSlug) {
            $redirectUrl = $this->generatePageUrl($item, $currentLocale);

            if (config('announcement.debug.verbose_logs', false)) {
                Log::info('🔄 Canonical redirect', [
                    'from' => request()->fullUrl(),
                    'to' => $redirectUrl
                ]);
            }

            // Yanlış slug ile erişim, doğru URL'e redirect
            return redirect()->to($redirectUrl);
        }

        // SEO meta tags için model'i global olarak paylaş
        view()->share('currentModel', $item);

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('show', 'announcement');
            return view($viewPath, ['item' => $item]);
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());

            // Fallback view'a yönlendir
            return view('announcement::front.show', ['item' => $item]);
        }
    }

    /**
     * Sayfa için locale-aware URL oluştur
     */
    protected function generatePageUrl(Announcement $announcement, ?string $locale = null): string
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

    /**
     * Modül title'ını al - settings tablosundan varsa onu, yoksa fallback
     */
    private function getModuleTitle(string $moduleName): string
    {
        $currentLocale = app()->getLocale();

        // ModuleTenantSetting'den title al
        $setting = ModuleTenantSetting::where('module_name', $moduleName)->first();

        if ($setting && $setting->title && isset($setting->title[$currentLocale])) {
            return $setting->title[$currentLocale];
        }

        // Fallback - ModuleSlugService'den default display name
        return ModuleSlugService::getDefaultModuleName($moduleName, $currentLocale);
    }
}
