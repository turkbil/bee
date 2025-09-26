<?php

namespace Modules\SeoManagement\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\SeoManagement\App\Services\SeoAIService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * SEO AI Controller - AI-SEO ENTEGRASYON V2.0
 * 
 * SEO modülü için özel AI controller.
 * SeoAIService kullanarak temiz ayrım sağlar.
 * 
 * Desteklenen Features:
 * - seo-content-type-optimizer
 * - seo-social-media-optimizer  
 * - seo-priority-calculator
 * - seo-comprehensive-audit
 */
class SeoAIController extends Controller
{
    public function __construct(
        private SeoAIService $seoAIService
    ) {}

    /**
     * SEO AI analizi gerçekleştir
     */
    public function analyze(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'feature_slug' => 'required|string',
            'form_content' => 'required|array',
            'language' => 'required|string'
        ]);

        try {
            Log::info('SEO AI Controller Request', [
                'feature_slug' => $validated['feature_slug'],
                'form_content_keys' => array_keys($validated['form_content']),
                'language' => $validated['language'],
                'user_id' => Auth::id()
            ]);

            // Enterprise SEO Analysis
            $result = $this->seoAIService->analyzeSEO(
                featureSlug: $validated['feature_slug'],
                formContent: $validated['form_content'],
                language: $validated['language'],
                options: [
                    'user_id' => Auth::id(),
                    'tenant_context' => true
                ]
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'SEO analizi başarısız'
                ], 500);
            }

            // Enterprise response format - UTF-8 temizliği ile
            $cleanResult = $this->cleanUtf8Recursively($result);
            
            return response()->json([
                'success' => true,
                'data' => $cleanResult,
                'detailed_scores' => $cleanResult['detailed_scores'] ?? null,
                'metrics' => $cleanResult['metrics'] ?? null,
                'feature_used' => $validated['feature_slug'],
                'form_fields_analyzed' => count($validated['form_content']),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('SEO AI Controller Error', [
                'feature_slug' => $validated['feature_slug'] ?? 'unknown',
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'SEO AI analizi sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * SEO içeriği AI ile oluştur
     */
    public function generateSeo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'form_content' => 'required|array',
            'language' => 'required|string'
        ]);

        try {
            Log::info('SEO AI Generate Request', [
                'form_content_keys' => array_keys($validated['form_content']),
                'language' => $validated['language'],
                'user_id' => Auth::id()
            ]);

            // AI ile SEO içeriği oluştur
            $result = $this->seoAIService->generateSeoContent(
                formContent: $validated['form_content'],
                language: $validated['language'],
                options: [
                    'user_id' => Auth::id(),
                    'tenant_context' => true
                ]
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'SEO içeriği oluşturulamadı'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'language' => $validated['language'],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('SEO AI Generate Error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'SEO içeriği oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * SEO önerilerini AI'dan al
     */
    public function getSuggestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'form_content' => 'required|array',
            'language' => 'required|string'
        ]);

        try {
            Log::info('SEO AI Suggestions Request', [
                'form_content_keys' => array_keys($validated['form_content']),
                'language' => $validated['language'],
                'user_id' => Auth::id()
            ]);

            // AI ile SEO önerileri al
            $result = $this->seoAIService->getSeoSuggestions(
                formContent: $validated['form_content'],
                language: $validated['language'],
                options: [
                    'user_id' => Auth::id(),
                    'tenant_context' => true
                ]
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'SEO içeriği oluşturulamadı'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'language' => $validated['language'],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('SEO AI Suggestions Error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'SEO önerileri alınırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * SEO analiz sonuçlarını veritabanına kaydet
     */
    public function saveSeoData(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'model_type' => 'required|string|in:page,portfolio,announcement',
            'model_id' => 'required|integer',
            'field' => 'required|string|in:meta_title,meta_description,og_title,og_description,keywords,content_type,priority_score',
            'value' => 'required|string',
            'language' => 'string|default:tr'
        ]);

        try {
            Log::info('SEO AI Save Data Request', [
                'model_type' => $validated['model_type'],
                'model_id' => $validated['model_id'],
                'field' => $validated['field'],
                'language' => $validated['language'] ?? 'tr',
                'user_id' => Auth::id()
            ]);

            // SeoAIService ile kaydetme işlemi
            $result = $this->seoAIService->saveSeoData(
                modelType: $validated['model_type'],
                modelId: $validated['model_id'],
                field: $validated['field'],
                value: $validated['value'],
                language: $validated['language'] ?? 'tr',
                userId: Auth::id()
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'Veri kaydedilemedi'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'SEO verisi başarıyla kaydedildi',
                'data' => $result['data'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('SEO AI Save Data Error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Veri kaydedilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * SEO analiz geçmişini getir
     */
    public function getAnalysisHistory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
            'language' => 'string'
        ]);

        try {
            $result = $this->seoAIService->getAnalysisHistory(
                modelType: $validated['model_type'],
                modelId: $validated['model_id'],
                language: $validated['language'] ?? null
            );

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('SEO Analysis History Controller Error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Geçmiş analizler yüklenirken hata oluştu'
            ], 500);
        }
    }

    /**
     * UTF-8 karakterleri recursive olarak temizle
     */
    private function cleanUtf8Recursively($data)
    {
        if (is_string($data)) {
            // Geçersiz UTF-8 karakterleri temizle
            return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        } elseif (is_array($data)) {
            return array_map([$this, 'cleanUtf8Recursively'], $data);
        } elseif (is_object($data)) {
            $cleanData = new \stdClass();
            foreach ($data as $key => $value) {
                $cleanData->{$key} = $this->cleanUtf8Recursively($value);
            }
            return $cleanData;
        }
        
        return $data;
    }
}