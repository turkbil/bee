<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SeoWidgetService;
use App\Services\SeoCacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SeoController extends Controller
{
    /**
     * Modül bağımsız SEO verileri getir
     */
    public function getSeoData(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'model_type' => 'required|string',
                'model_id' => 'required|integer',
                'language' => 'required|string|max:2'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $modelType = $request->input('model_type');
            $modelId = $request->input('model_id');
            $language = $request->input('language');

            // Model'i dinamik olarak bul
            $model = $this->findModel($modelType, $modelId);
            
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => 'Model bulunamadı'
                ], 404);
            }

            // SEO verilerini hazırla
            $response = SeoWidgetService::prepareApiResponse($model, $language);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('SEO veri getirme hatası: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sunucu hatası oluştu'
            ], 500);
        }
    }

    /**
     * Modül bağımsız SEO verileri kaydet
     */
    public function saveSeoData(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'model_type' => 'required|string',
                'model_id' => 'required|integer',
                'language' => 'required|string|max:2',
                'seo_title' => 'required|string|max:60',
                'seo_description' => 'required|string|max:160',
                'seo_keywords' => 'nullable|string|max:500',
                'canonical_url' => 'nullable|url|max:255',
                'og_title' => 'nullable|string|max:60',
                'og_description' => 'nullable|string|max:160',
                'og_image' => 'nullable|url|max:500',
                'focus_keyword' => 'nullable|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $modelType = $request->input('model_type');
            $modelId = $request->input('model_id');
            $language = $request->input('language');
            
            // Model'i dinamik olarak bul
            $model = $this->findModel($modelType, $modelId);
            
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => 'Model bulunamadı'
                ], 404);
            }

            // SEO verilerini kaydet
            $seoData = $request->only([
                'seo_title', 'seo_description', 'seo_keywords', 'canonical_url',
                'og_title', 'og_description', 'og_image', 'focus_keyword'
            ]);

            $saved = SeoWidgetService::saveSeoData($model, $seoData, $language);

            if ($saved) {
                return response()->json([
                    'success' => true,
                    'message' => 'SEO verileri başarıyla kaydedildi'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'SEO verileri kaydedilemedi'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('SEO veri kaydetme hatası: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sunucu hatası oluştu'
            ], 500);
        }
    }

    /**
     * Slug benzersizlik kontrolü
     */
    public function checkSlug(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'slug' => 'required|string|max:255',
                'module' => 'required|string',
                'exclude_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $slug = $request->input('slug');
            $module = $request->input('module');
            $excludeId = $request->input('exclude_id');

            // Modül class'ını belirle
            $moduleClass = $this->getModuleClass($module);
            
            if (!$moduleClass) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz modül'
                ], 400);
            }

            $unique = SeoWidgetService::checkSlugUniqueness($slug, $moduleClass, $excludeId);

            return response()->json([
                'unique' => $unique,
                'message' => $unique ? 'Slug kullanılabilir' : 'Bu slug zaten kullanılıyor'
            ]);

        } catch (\Exception $e) {
            Log::error('Slug kontrolü hatası: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);

            return response()->json([
                'unique' => false,
                'message' => 'Slug kontrolü yapılamadı'
            ], 500);
        }
    }

    /**
     * SEO skorunu hesapla
     */
    public function calculateScore(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'model_type' => 'required|string',
                'model_id' => 'required|integer',
                'language' => 'required|string|max:2'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $modelType = $request->input('model_type');
            $modelId = $request->input('model_id');
            $language = $request->input('language');

            // Model'i dinamik olarak bul
            $model = $this->findModel($modelType, $modelId);
            
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => 'Model bulunamadı'
                ], 404);
            }

            $seoScore = SeoWidgetService::calculateSeoScore($model, $language);

            return response()->json([
                'success' => true,
                'seoScore' => $seoScore
            ]);

        } catch (\Exception $e) {
            Log::error('SEO skor hesaplama hatası: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'SEO skoru hesaplanamadı'
            ], 500);
        }
    }

    /**
     * SEO önerisi oluştur
     */
    public function generateSuggestion(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'model_type' => 'required|string',
                'model_id' => 'required|integer',
                'language' => 'required|string|max:2'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $modelType = $request->input('model_type');
            $modelId = $request->input('model_id');
            $language = $request->input('language');

            // Model'i dinamik olarak bul
            $model = $this->findModel($modelType, $modelId);
            
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => 'Model bulunamadı'
                ], 404);
            }

            $suggestions = SeoWidgetService::generateAutoSeoSuggestion($model, $language);

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);

        } catch (\Exception $e) {
            Log::error('SEO öneri oluşturma hatası: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'SEO önerisi oluşturulamadı'
            ], 500);
        }
    }

    /**
     * SEO cache temizle
     */
    public function clearCache(Request $request): JsonResponse
    {
        try {
            $type = $request->input('type', 'all'); // all, model, language

            switch ($type) {
                case 'model':
                    $validator = Validator::make($request->all(), [
                        'model_type' => 'required|string',
                        'model_id' => 'required|integer'
                    ]);

                    if ($validator->fails()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Validation hatası',
                            'errors' => $validator->errors()
                        ], 422);
                    }

                    $model = $this->findModel($request->input('model_type'), $request->input('model_id'));
                    if ($model) {
                        SeoCacheService::forgetModelCache($model);
                        return response()->json([
                            'success' => true,
                            'message' => 'Model cache temizlendi'
                        ]);
                    }
                    break;

                case 'language':
                    $validator = Validator::make($request->all(), [
                        'language' => 'required|string|max:2'
                    ]);

                    if ($validator->fails()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Validation hatası',
                            'errors' => $validator->errors()
                        ], 422);
                    }

                    SeoCacheService::forgetLanguageCache($request->input('language'));
                    return response()->json([
                        'success' => true,
                        'message' => 'Dil cache temizlendi'
                    ]);

                case 'all':
                default:
                    SeoCacheService::flush();
                    return response()->json([
                        'success' => true,
                        'message' => 'Tüm SEO cache temizlendi'
                    ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Cache temizlenemedi'
            ], 400);

        } catch (\Exception $e) {
            Log::error('SEO cache temizleme hatası: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Cache temizlenemedi'
            ], 500);
        }
    }

    /**
     * SEO cache istatistikleri
     */
    public function cacheStats(): JsonResponse
    {
        try {
            $stats = SeoCacheService::getStats();
            $health = SeoCacheService::healthCheck();

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'health' => $health
            ]);

        } catch (\Exception $e) {
            Log::error('SEO cache stats hatası: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cache stats alınamadı'
            ], 500);
        }
    }

    /**
     * Model'i dinamik olarak bul
     */
    private function findModel(string $modelType, int $modelId)
    {
        try {
            // Modül mapping'i
            $modelMapping = [
                'Page' => \Modules\Page\App\Models\Page::class,
                'Portfolio' => \Modules\Portfolio\App\Models\Portfolio::class,
                'PortfolioCategory' => \Modules\Portfolio\App\Models\PortfolioCategory::class,
                'Announcement' => \Modules\Announcement\App\Models\Announcement::class,
                // Yeni modüller buraya eklenebilir
            ];

            if (!isset($modelMapping[$modelType])) {
                return null;
            }

            $modelClass = $modelMapping[$modelType];
            
            if (!class_exists($modelClass)) {
                return null;
            }

            return $modelClass::find($modelId);

        } catch (\Exception $e) {
            Log::error('Model bulma hatası: ' . $e->getMessage(), [
                'model_type' => $modelType,
                'model_id' => $modelId
            ]);
            return null;
        }
    }

    /**
     * Modül class'ını getir
     */
    private function getModuleClass(string $module): ?string
    {
        $moduleMapping = [
            'Page' => \Modules\Page\App\Models\Page::class,
            'Portfolio' => \Modules\Portfolio\App\Models\Portfolio::class,
            'PortfolioCategory' => \Modules\Portfolio\App\Models\PortfolioCategory::class,
            'Announcement' => \Modules\Announcement\App\Models\Announcement::class,
            // Yeni modüller buraya eklenebilir
        ];

        return $moduleMapping[$module] ?? null;
    }
}