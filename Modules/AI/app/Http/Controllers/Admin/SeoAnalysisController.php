<?php

namespace Modules\AI\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\SeoManagement\App\Services\SeoAIService;
use Modules\SeoManagement\App\Services\SeoRecommendationsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * SEO ANALYSIS CONTROLLER
 * AI-powered SEO analysis için endpoint
 */
class SeoAnalysisController extends Controller
{
    private SeoAIService $seoAIService;
    private SeoRecommendationsService $seoRecommendationsService;

    public function __construct(SeoAIService $seoAIService, SeoRecommendationsService $seoRecommendationsService)
    {
        $this->seoAIService = $seoAIService;
        $this->seoRecommendationsService = $seoRecommendationsService;
    }

    /**
     * AI SEO ANALİZİ ENDPOINT
     */
    public function analyze(Request $request): JsonResponse
    {
        try {
            Log::info('SEO AI Analysis Request Received', [
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            // Request validation
            $validator = Validator::make($request->all(), [
                'feature_slug' => 'required|string',
                'form_content' => 'required|array',
                'page_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // SEO analizi çalıştır
            $analysisResult = $this->seoAIService->analyzeSEO(
                $request->input('feature_slug', 'seo-comprehensive-audit'),
                $request->input('form_content', []),
                [
                    'user_id' => auth()->id(),
                    'page_id' => $request->input('page_id')
                ]
            );

            // Başarılı yanıt
            if ($analysisResult['success']) {
                // Sayfa ID varsa sonuçları kaydet
                if ($request->has('page_id') && $request->page_id) {
                    $this->saveSeoAnalysisResults($request->page_id, $analysisResult);
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'overall_score' => $analysisResult['metrics']['overall_score'] ?? 0,
                        'detailed_scores' => $analysisResult['detailed_scores'] ?? [],
                        'strengths' => $analysisResult['strengths'] ?? [],
                        'improvements' => $analysisResult['improvements'] ?? [],
                        'action_items' => $analysisResult['action_items'] ?? [],
                        'health_status' => $analysisResult['metrics']['health_status'] ?? 'Bilinmiyor'
                    ],
                    'message' => 'SEO analizi başarıyla tamamlandı'
                ]);
            }

            // Hata durumu
            return response()->json([
                'success' => false,
                'error' => $analysisResult['error'] ?? 'Analiz sırasında bir hata oluştu',
                'details' => $analysisResult
            ], 500);

        } catch (\Exception $e) {
            Log::error('SEO Analysis Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Sunucu hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI SEO ÖNERİLERİ ENDPOINT
     */
    public function generateRecommendations(Request $request): JsonResponse
    {
        try {
            Log::info('SEO AI Recommendations Request Received', [
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            // Request validation
            $validator = Validator::make($request->all(), [
                'feature_slug' => 'required|string',
                'form_content' => 'required|array',
                'language' => 'required|string',
                'page_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // SEO önerilerini üret
            $recommendationsResult = $this->seoRecommendationsService->generateSeoRecommendations(
                $request->input('feature_slug', 'seo-smart-recommendations'),
                $request->input('form_content', []),
                $request->input('language', 'tr'),
                [
                    'user_id' => auth()->id(),
                    'page_id' => $request->input('page_id')
                ]
            );

            // Başarılı yanıt
            if ($recommendationsResult['success']) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'recommendations' => $recommendationsResult['recommendations'] ?? [],
                        'total_count' => count($recommendationsResult['recommendations'] ?? []),
                        'language' => $request->input('language', 'tr'),
                        'generated_at' => now()->toISOString()
                    ],
                    'message' => 'SEO önerileri başarıyla oluşturuldu'
                ]);
            }

            // Hata durumu
            return response()->json([
                'success' => false,
                'error' => $recommendationsResult['error'] ?? 'Öneri üretimi sırasında bir hata oluştu',
                'details' => $recommendationsResult
            ], 500);

        } catch (\Exception $e) {
            Log::error('SEO Recommendations Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Sunucu hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * SEO ANALİZ SONUÇLARINI VERİTABANINA KAYDET
     */
    private function saveSeoAnalysisResults(int $pageId, array $analysisResult): void
    {
        try {
            // Page modelini SeoSetting ile birlikte bul
            $page = \Modules\Page\App\Models\Page::with('seoSetting')->find($pageId);
            
            if (!$page) {
                Log::warning('Page not found for SEO analysis save', ['page_id' => $pageId]);
                return;
            }

            // SeoSetting yoksa oluştur
            if (!$page->seoSetting) {
                $page->seoSetting()->create([
                    'titles' => [],
                    'descriptions' => [],
                    'keywords' => []
                ]);
                $page->load('seoSetting');
            }

            // Analiz sonuçlarını kaydet
            $page->seoSetting->update([
                'analysis_results' => $analysisResult,
                'analysis_date' => now(),
                'overall_score' => $analysisResult['metrics']['overall_score'] ?? 0,
                'strengths' => $analysisResult['strengths'] ?? [],
                'improvements' => $analysisResult['improvements'] ?? [],
                'action_items' => $analysisResult['action_items'] ?? []
            ]);

            Log::info('SEO Analysis Results Saved Successfully', [
                'page_id' => $pageId,
                'overall_score' => $analysisResult['metrics']['overall_score'] ?? 0
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save SEO analysis results', [
                'page_id' => $pageId,
                'error' => $e->getMessage()
            ]);
        }
    }
}