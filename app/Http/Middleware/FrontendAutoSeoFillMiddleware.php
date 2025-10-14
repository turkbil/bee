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
        // Önce response'u al (sayfa render edilsin)
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
                    return $response; // Tespit edilemedi, devam et
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

            // SEO üret ve kaydet (arka planda)
            Log::info('🎯 Frontend Auto SEO Fill: Üretim başlatılıyor', [
                'model_type' => $modelType,
                'model_id' => $modelId,
                'locale' => $locale,
                'tenant' => $tenant->id
            ]);

            $seoData = $this->autoSeoFillService->autoFillSeoData($model, $locale);

            if ($seoData) {
                $this->autoSeoFillService->saveSeoData($model, $seoData, $locale);

                Log::info('✅ Frontend Auto SEO Fill: Başarıyla üretildi', [
                    'model_type' => $modelType,
                    'model_id' => $modelId
                ]);
            }

        } catch (\Exception $e) {
            // Hata durumunda log at ama response'u etkileme
            Log::error('❌ Frontend Auto SEO Fill: Hata', [
                'error' => $e->getMessage(),
                'model_type' => $modelType,
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $response;
    }

    /**
     * Route'dan model ID'yi al
     */
    protected function getModelIdFromRoute(Request $request, string $modelType): ?int
    {
        $routeParams = $request->route()->parameters();

        // Model type'a göre parametre adını belirle
        $paramName = match($modelType) {
            'page' => 'slug',  // Page slug kullanıyor
            'blog' => 'slug',  // Blog slug kullanıyor
            'portfolio' => 'slug',  // Portfolio slug kullanıyor
            'announcement' => 'id',
            'shop_product' => 'slug',
            'shop_category' => 'slug',
            default => 'id'
        };

        $value = $routeParams[$paramName] ?? null;

        // Slug ise, model'den ID'yi bul
        if (in_array($paramName, ['slug']) && $value) {
            $modelClass = $this->getModelClass($modelType);
            if ($modelClass && class_exists($modelClass)) {
                $model = $modelClass::where('slug->' . app()->getLocale(), $value)->first();
                return $model?->id ?? $model?->getKey();
            }
        }

        return is_numeric($value) ? (int) $value : null;
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

        // Controller'dan tespit et
        $action = $route->getAction();
        $controller = $action['controller'] ?? '';

        if (str_contains($controller, 'PageController')) {
            return 'page';
        }
        if (str_contains($controller, 'BlogController')) {
            return 'blog';
        }
        if (str_contains($controller, 'PortfolioController')) {
            return 'portfolio';
        }
        if (str_contains($controller, 'AnnouncementController')) {
            return 'announcement';
        }
        if (str_contains($controller, 'ShopProductController')) {
            return 'shop_product';
        }
        if (str_contains($controller, 'ShopCategoryController') || str_contains($controller, 'CategoryController')) {
            return 'shop_category';
        }

        // Route name'den tespit et
        $name = $route->getName();
        if ($name) {
            if (str_contains($name, 'page.')) return 'page';
            if (str_contains($name, 'blog.')) return 'blog';
            if (str_contains($name, 'portfolio.')) return 'portfolio';
            if (str_contains($name, 'announcement.')) return 'announcement';
            if (str_contains($name, 'shop.product')) return 'shop_product';
            if (str_contains($name, 'shop.category')) return 'shop_category';
        }

        return null;
    }
}
