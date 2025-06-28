<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Services\ModuleSlugService;
use Modules\LanguageManagement\app\Services\UrlPrefixService;

class DynamicRouteService
{
    public function handleDynamicRoute($slug1, $slug2 = null)
    {
        
        // Page modülü kontrolü
        $pageIndexSlug = ModuleSlugService::getSlug('Page', 'index');
        $pageShowSlug = ModuleSlugService::getSlug('Page', 'show');
        
        if ($slug1 === $pageIndexSlug && !$slug2) {
            // Page index
            return app(\Modules\Page\App\Http\Controllers\Front\PageController::class)->index();
        }
        
        if ($slug1 === $pageShowSlug && $slug2) {
            // Page show - slug2'yi kullan
            Log::info("DynamicRouteService: Page show called with slug: {$slug2}");
            return app(\Modules\Page\App\Http\Controllers\Front\PageController::class)->show($slug2);
        }
        
        // Direkt slug eşleştirmesi kaldırıldı - artık sadece /page/xxx formatı destekleniyor
        
        // Portfolio modülü kontrolü
        $portfolioIndexSlug = ModuleSlugService::getSlug('Portfolio', 'index');
        $portfolioShowSlug = ModuleSlugService::getSlug('Portfolio', 'show');
        $portfolioCategorySlug = ModuleSlugService::getSlug('Portfolio', 'category');
        
        if ($slug1 === $portfolioIndexSlug && !$slug2) {
            // Portfolio index
            return app(\Modules\Portfolio\App\Http\Controllers\Front\PortfolioController::class)->index();
        }
        
        if ($slug1 === $portfolioShowSlug && $slug2) {
            // Portfolio show
            return app(\Modules\Portfolio\App\Http\Controllers\Front\PortfolioController::class)->show($slug2);
        }
        
        if ($slug1 === $portfolioCategorySlug && $slug2) {
            // Portfolio category
            return app(\Modules\Portfolio\App\Http\Controllers\Front\PortfolioController::class)->category($slug2);
        }
        
        // Announcement modülü kontrolü
        $announcementIndexSlug = ModuleSlugService::getSlug('Announcement', 'index');
        $announcementShowSlug = ModuleSlugService::getSlug('Announcement', 'show');
        
        if ($slug1 === $announcementIndexSlug && !$slug2) {
            // Announcement index
            return app(\Modules\Announcement\App\Http\Controllers\Front\AnnouncementController::class)->index();
        }
        
        if ($slug1 === $announcementShowSlug && $slug2) {
            // Announcement show
            return app(\Modules\Announcement\App\Http\Controllers\Front\AnnouncementController::class)->show($slug2);
        }
        
        // Hiçbiri eşleşmezse 404
        abort(404);
    }

    public static function registerModuleRoutes()
    {
        Log::info('DynamicRouteService: Starting to register module routes');
        
        // Page modülü route'ları
        self::registerPageRoutes();
        
        // Portfolio modülü route'ları  
        self::registerPortfolioRoutes();
        
        // Announcement modülü route'ları
        self::registerAnnouncementRoutes();
        
        Log::info('DynamicRouteService: Finished registering module routes');
    }
    
    protected static function registerPageRoutes()
    {
        $indexSlug = ModuleSlugService::getSlug('Page', 'index');
        $showSlug = ModuleSlugService::getSlug('Page', 'show');
        
        Log::info('DynamicRouteService: Registering Page routes', [
            'index_slug' => $indexSlug,
            'show_slug' => $showSlug
        ]);
        
        // Use UrlPrefixService if available
        if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
            UrlPrefixService::registerLocaleRoutes(function () use ($indexSlug, $showSlug) {
                Route::get('/', [\Modules\Page\App\Http\Controllers\Front\PageController::class, 'homepage'])->name('home');
                Route::get('/' . $indexSlug, [\Modules\Page\App\Http\Controllers\Front\PageController::class, 'index'])->name('pages.index');
                Route::get('/' . $showSlug . '/{slug}', [\Modules\Page\App\Http\Controllers\Front\PageController::class, 'show'])->name('pages.show');
            });
        } else {
            // Fallback to regular routes
            Route::middleware(['web'])->group(function () use ($indexSlug, $showSlug) {
                Route::get('/', [\Modules\Page\App\Http\Controllers\Front\PageController::class, 'homepage'])->name('home');
                Route::get('/' . $indexSlug, [\Modules\Page\App\Http\Controllers\Front\PageController::class, 'index'])->name('pages.index');
                Route::get('/' . $showSlug . '/{slug}', [\Modules\Page\App\Http\Controllers\Front\PageController::class, 'show'])->name('pages.show');
            });
        }
    }
    
    protected static function registerPortfolioRoutes()
    {
        $indexSlug = ModuleSlugService::getSlug('Portfolio', 'index');
        $showSlug = ModuleSlugService::getSlug('Portfolio', 'show');
        $categorySlug = ModuleSlugService::getSlug('Portfolio', 'category');
        
        Log::info('DynamicRouteService: Registering Portfolio routes', [
            'index_slug' => $indexSlug,
            'show_slug' => $showSlug,
            'category_slug' => $categorySlug
        ]);
        
        // Use UrlPrefixService if available
        if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
            UrlPrefixService::registerLocaleRoutes(function () use ($indexSlug, $showSlug, $categorySlug) {
                Route::get('/' . $indexSlug, [\Modules\Portfolio\App\Http\Controllers\Front\PortfolioController::class, 'index'])->name('portfolios.index');
                Route::get('/' . $showSlug . '/{slug}', [\Modules\Portfolio\App\Http\Controllers\Front\PortfolioController::class, 'show'])->name('portfolios.show');
                Route::get('/' . $categorySlug . '/{slug}', [\Modules\Portfolio\App\Http\Controllers\Front\PortfolioController::class, 'category'])->name('portfolios.category');
            });
        } else {
            // Fallback to regular routes
            Route::middleware(['web'])->group(function () use ($indexSlug, $showSlug, $categorySlug) {
                Route::get('/' . $indexSlug, [\Modules\Portfolio\App\Http\Controllers\Front\PortfolioController::class, 'index'])->name('portfolios.index');
                Route::get('/' . $showSlug . '/{slug}', [\Modules\Portfolio\App\Http\Controllers\Front\PortfolioController::class, 'show'])->name('portfolios.show');
                Route::get('/' . $categorySlug . '/{slug}', [\Modules\Portfolio\App\Http\Controllers\Front\PortfolioController::class, 'category'])->name('portfolios.category');
            });
        }
    }
    
    protected static function registerAnnouncementRoutes()
    {
        $indexSlug = ModuleSlugService::getSlug('Announcement', 'index');
        $showSlug = ModuleSlugService::getSlug('Announcement', 'show');
        
        Log::info('DynamicRouteService: Registering Announcement routes', [
            'index_slug' => $indexSlug,
            'show_slug' => $showSlug
        ]);
        
        // Use UrlPrefixService if available
        if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
            UrlPrefixService::registerLocaleRoutes(function () use ($indexSlug, $showSlug) {
                Route::get('/' . $indexSlug, [\Modules\Announcement\App\Http\Controllers\Front\AnnouncementController::class, 'index'])->name('announcements.index');
                Route::get('/' . $showSlug . '/{slug}', [\Modules\Announcement\App\Http\Controllers\Front\AnnouncementController::class, 'show'])->name('announcements.show');
            });
        } else {
            // Fallback to regular routes
            Route::middleware(['web'])->group(function () use ($indexSlug, $showSlug) {
                Route::get('/' . $indexSlug, [\Modules\Announcement\App\Http\Controllers\Front\AnnouncementController::class, 'index'])->name('announcements.index');
                Route::get('/' . $showSlug . '/{slug}', [\Modules\Announcement\App\Http\Controllers\Front\AnnouncementController::class, 'show'])->name('announcements.show');
            });
        }
    }
}