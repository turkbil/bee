<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AI\AutoSeoFillService;
use Illuminate\Support\Facades\Log;

/**
 * Frontend Auto SEO Fill Middleware
 *
 * Premium tenant'lar için otomatik SEO üretimi
 * Ziyaretçi bir detay sayfası açtığında, SEO boşsa otomatik üretir
 *
 * Çalışma Mantığı:
 * 1. Route'dan model type ve ID tespit edilir
 * 2. Premium tenant kontrolü yapılır
 * 3. SEO boş mu kontrol edilir
 * 4. Boşsa AI'dan üretilir ve kaydedilir
 * 5. Response döndürülür
 *
 * @author Claude Code
 * @version 1.0.0
 */
class FrontendAutoSeoFillMiddleware
{
    protected $autoSeoFillService;

    public function __construct(AutoSeoFillService $autoSeoFillService)
    {
        $this->autoSeoFillService = $autoSeoFillService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $modelType = null): Response
    {
        // Önce response'u al (sayfa hemen render edilsin, kullanıcı beklemez)
        $response = $next($request);

        // Sadece GET istekleri için çalış
        if (!$request->isMethod('GET')) {
            return $response;
        }

        // Sadece başarılı response'lar için çalış
        if (!$response->isSuccessful()) {
            return $response;
        }

        try {
            // Model type belirtilmemişse route'dan tespit et
            if (!$modelType) {
                $modelType = $this->detectModelTypeFromRoute($request);
                if (!$modelType) {
                    return $response;
                }
            }

            // Model ID'yi route parametrelerinden al
            $modelId = $this->getModelIdFromRoute($request, $modelType);
            if (!$modelId) {
                return $response;
            }

            // Tenant kontrolü
            $tenant = tenant();
            if (!$tenant || !$tenant->isPremium()) {
                return $response;
            }

            // Model class'ını belirle
            $modelClass = $this->getModelClass($modelType);
            if (!$modelClass || !class_exists($modelClass)) {
                return $response;
            }

            // Model'i bul
            $model = $modelClass::find($modelId);
            if (!$model) {
                return $response;
            }

            // Mevcut dil
            $locale = app()->getLocale();

            // SEO dolu mu kontrol et
            if (!$this->autoSeoFillService->shouldAutoFill($model, $locale)) {
                return $response;
            }

            // SEO üretimini background job'a gönder (sayfa hızını etkilemez)
            Log::info('🎯 Premium Tenant Auto SEO Fill job dispatch ediliyor', [
                'tenant' => $tenant->id,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'locale' => $locale
            ]);

            // Background job'a gönder
            \App\Jobs\AutoFillSeoDataJob::dispatch(
                $modelClass,
                $modelId,
                $locale,
                $tenant->id
            )->onQueue('default');

            Log::info('✅ Premium Tenant Auto SEO Fill job kuyruğa eklendi', [
                'model_type' => $modelType,
                'model_id' => $modelId
            ]);

        } catch (\Exception $e) {
            // Hata durumunda log at ama response'u etkileme
            Log::error('❌ Premium Tenant Auto SEO Fill hatası', [
                'error' => $e->getMessage(),
                'model_type' => $modelType ?? 'unknown',
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }

        return $response;
    }

    /**
     * Route'dan model ID'yi al
     */
    protected function getModelIdFromRoute(Request $request, string $modelType): ?int
    {
        $path = $request->path();
        $slug = null;

        // Path'den slug'ı çıkar
        if ($modelType === 'shop_product') {
            // shop/slug-here
            $slug = preg_replace('#^shop/(.+)$#', '$1', $path);
        } elseif ($modelType === 'shop_category') {
            // shop/category/slug-here veya shop/brand/slug-here
            $slug = preg_replace('#^shop/(category|brand)/(.+)$#', '$2', $path);
        } elseif (in_array($modelType, ['blog', 'portfolio', 'announcement'])) {
            // tr/blog/slug-here veya blog/slug-here
            $parts = explode('/', $path);
            $slug = end($parts);
        }

        if (!$slug || $slug === $path) {
            // Route parametrelerinden dene
            $routeParams = $request->route()->parameters();
            $slug = $routeParams['slug'] ?? null;
        }

        if (!$slug) {
            return null;
        }

        // Slug'dan ID bul
        $modelClass = $this->getModelClass($modelType);
        if ($modelClass && class_exists($modelClass)) {
            $locale = app()->getLocale();

            $model = $modelClass::where('slug->' . $locale, $slug)->first();

            if (!$model) {
                // Locale olmadan dene (fallback)
                $model = $modelClass::where('slug', $slug)->first();
            }

            return $model?->id ?? $model?->getKey();
        }

        return null;
    }

    /**
     * Model type'a göre model class'ını döndür
     */
    protected function getModelClass(string $modelType): ?string
    {
        return match($modelType) {
            'page' => \Modules\Page\App\Models\Page::class,
            'blog' => \Modules\Blog\App\Models\Blog::class,
            'portfolio' => \Modules\Portfolio\App\Models\Portfolio::class,
            'announcement' => \Modules\Announcement\App\Models\Announcement::class,
            'shop_product' => \Modules\Shop\App\Models\ShopProduct::class,
            'shop_category' => \Modules\Shop\App\Models\ShopProductCategory::class,
            default => null
        };
    }

    /**
     * Route'dan model type'ı tespit et
     * Controller namespace'den ya da route name'den
     */
    protected function detectModelTypeFromRoute(Request $request): ?string
    {
        $route = $request->route();
        if (!$route) {
            return null;
        }

        // URL PATH'den tespit et (en güvenilir yöntem)
        $path = $request->path();

        if (str_starts_with($path, 'shop/category/')) {
            return 'shop_category';
        }
        if (str_starts_with($path, 'shop/brand/')) {
            return 'shop_category'; // Brand da category gibi işle
        }
        if (str_starts_with($path, 'shop/') && $path !== 'shop' && $path !== 'shop/') {
            return 'shop_product'; // /shop/{slug} -> product detail
        }
        if (str_contains($path, '/blog/') || preg_match('#^[^/]+/blog/#', $path)) {
            return 'blog';
        }
        if (str_contains($path, '/portfolio/') || preg_match('#^[^/]+/portfolio/#', $path)) {
            return 'portfolio';
        }
        if (str_contains($path, '/announcement/') || preg_match('#^[^/]+/announcement/#', $path)) {
            return 'announcement';
        }

        // Controller'dan tespit et (fallback)
        $action = $route->getAction();
        $controller = $action['controller'] ?? '';

        if ($controller) {
            if (str_contains($controller, 'PageController')) return 'page';
            if (str_contains($controller, 'BlogController')) return 'blog';
            if (str_contains($controller, 'PortfolioController')) return 'portfolio';
            if (str_contains($controller, 'AnnouncementController')) return 'announcement';
            if (str_contains($controller, 'ShopProductController') || str_contains($controller, 'ShopController')) return 'shop_product';
            if (str_contains($controller, 'ShopCategoryController') || str_contains($controller, 'CategoryController')) return 'shop_category';
        }

        // Route name'den tespit et (fallback)
        $name = $route->getName();

        if ($name) {
            if (str_contains($name, 'page.')) return 'page';
            if (str_contains($name, 'blog.')) return 'blog';
            if (str_contains($name, 'portfolio.')) return 'portfolio';
            if (str_contains($name, 'announcement.')) return 'announcement';
            if (str_contains($name, 'shop.show') || str_contains($name, 'shop.product')) return 'shop_product';
            if (str_contains($name, 'shop.category')) return 'shop_category';
        }

        return null;
    }
}
