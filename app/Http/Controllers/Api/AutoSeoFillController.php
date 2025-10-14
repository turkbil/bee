<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AI\AutoSeoFillService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * AUTO SEO FILL API CONTROLLER
 * Premium tenant'lar için otomatik SEO doldurma endpoint'i
 *
 * Route: POST /api/auto-seo-fill
 * Middleware: tenant, throttle:1,1 (1dk'da 1 request)
 */
class AutoSeoFillController extends Controller
{
    private AutoSeoFillService $autoSeoFillService;

    public function __construct(AutoSeoFillService $autoSeoFillService)
    {
        $this->autoSeoFillService = $autoSeoFillService;
    }

    /**
     * Sayfa için auto SEO fill tetikle
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fill(Request $request): JsonResponse
    {
        try {
            // Validate
            $validated = $request->validate([
                'model_type' => 'required|string|in:page,portfolio,announcement,blog',
                'model_id' => 'required|integer',
                'locale' => 'required|string|max:5'
            ]);

            Log::info('🚀 Auto SEO Fill API: İstek alındı', [
                'tenant_id' => tenant()?->id,
                'model_type' => $validated['model_type'],
                'model_id' => $validated['model_id'],
                'locale' => $validated['locale']
            ]);

            // Tenant kontrolü
            $tenant = tenant();
            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tenant bulunamadı'
                ], 404);
            }

            // Premium kontrolü
            if (!$tenant->isPremium()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Bu özellik sadece premium tenant\'lar için geçerlidir'
                ], 403);
            }

            // Model class belirleme
            $modelClass = $this->getModelClass($validated['model_type']);
            if (!$modelClass) {
                return response()->json([
                    'success' => false,
                    'error' => 'Geçersiz model tipi'
                ], 400);
            }

            // Model'i bul
            $model = $modelClass::find($validated['model_id']);
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'error' => 'Model bulunamadı'
                ], 404);
            }

            // Auto fill gerekli mi?
            if (!$this->autoSeoFillService->shouldAutoFill($model, $validated['locale'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'SEO verileri zaten dolu, otomatik doldurma gerekmiyor',
                    'skipped' => true
                ]);
            }

            // SEO verilerini AI ile doldur
            $seoData = $this->autoSeoFillService->autoFillSeoData($model, $validated['locale']);
            if (!$seoData) {
                return response()->json([
                    'success' => false,
                    'error' => 'SEO verileri oluşturulamadı'
                ], 500);
            }

            // Kaydet
            $saved = $this->autoSeoFillService->saveSeoData($model, $seoData, $validated['locale']);
            if (!$saved) {
                return response()->json([
                    'success' => false,
                    'error' => 'SEO verileri kaydedilemedi'
                ], 500);
            }

            Log::info('✅ Auto SEO Fill API: Başarılı', [
                'tenant_id' => $tenant->id,
                'model_type' => $validated['model_type'],
                'model_id' => $validated['model_id'],
                'locale' => $validated['locale'],
                'seo_data' => $seoData
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SEO verileri otomatik olarak dolduruldu',
                'data' => $seoData
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation hatası',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Auto SEO Fill API: Hata', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toplu auto fill (tüm model'ler için)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkFill(Request $request): JsonResponse
    {
        try {
            // Validate
            $validated = $request->validate([
                'model_type' => 'required|string|in:page,portfolio,announcement,blog',
                'locale' => 'required|string|max:5'
            ]);

            // Tenant kontrolü
            $tenant = tenant();
            if (!$tenant || !$tenant->isPremium()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Premium tenant gerekli'
                ], 403);
            }

            // Model class
            $modelClass = $this->getModelClass($validated['model_type']);
            if (!$modelClass) {
                return response()->json([
                    'success' => false,
                    'error' => 'Geçersiz model tipi'
                ], 400);
            }

            Log::info('🚀 Bulk Auto SEO Fill: Başlıyor', [
                'tenant_id' => $tenant->id,
                'model_type' => $validated['model_type'],
                'locale' => $validated['locale']
            ]);

            // Toplu fill
            $result = $this->autoSeoFillService->bulkAutoFill($modelClass, $validated['locale']);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('❌ Bulk Auto SEO Fill: Hata', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Toplu işlem hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Model type'ına göre model class döndür
     */
    private function getModelClass(string $modelType): ?string
    {
        $modelMap = [
            'page' => \Modules\Page\App\Models\Page::class,
            'portfolio' => \Modules\Portfolio\App\Models\Portfolio::class,
            'announcement' => \Modules\Announcement\App\Models\Announcement::class,
            'blog' => \Modules\Blog\App\Models\Blog::class,
        ];

        return $modelMap[$modelType] ?? null;
    }
}
