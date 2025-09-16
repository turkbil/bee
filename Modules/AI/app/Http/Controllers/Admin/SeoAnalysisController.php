<?php

namespace Modules\AI\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\SeoManagement\App\Services\SeoAIService;
use Modules\SeoManagement\App\Services\SeoRecommendationsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\TenantLanguageProvider;

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

            // Dinamik dil validasyonu
            $availableLanguages = TenantLanguageProvider::getActiveLanguageCodes();

            // Request validation
            $validator = Validator::make($request->all(), [
                'feature_slug' => 'required|string',
                'form_content' => 'required|array',
                'language' => 'required|string|in:' . implode(',', $availableLanguages),
                'page_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            $pageId = $request->input('page_id');
            $language = $request->input('language', TenantLanguageProvider::getDefaultLanguageCode());

            // Force yeniden oluşturma kontrolü - query parametresi varsa cache'i atla
            $forceRegenerate = $request->input('force_regenerate', false) || $request->query('force_regenerate', false);

            // Sayfa ID varsa ve force regenerate değilse, önce kaydedilmiş önerileri kontrol et
            if ($pageId && !$forceRegenerate) {
                $existingRecommendations = $this->getExistingRecommendations($pageId, $language);
                if ($existingRecommendations) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'recommendations' => $existingRecommendations['recommendations'],
                            'total_count' => count($existingRecommendations['recommendations']),
                            'language' => $language,
                            'generated_at' => $existingRecommendations['generated_at'] ?? now()->toISOString(),
                            'from_cache' => true
                        ],
                        'message' => 'Kaydedilmiş SEO önerileri yüklendi'
                    ], 200, [], JSON_UNESCAPED_UNICODE);
                }
            }

            // SEO önerilerini üret
            $recommendationsResult = $this->seoRecommendationsService->generateSeoRecommendations(
                $request->input('feature_slug', 'seo-smart-recommendations'),
                $request->input('form_content', []),
                $language,
                [
                    'user_id' => auth()->id(),
                    'page_id' => $pageId
                ]
            );

            // Başarılı yanıt - UTF-8 FIX
            if ($recommendationsResult['success']) {
                // UTF-8 karakterleri temizle
                $cleanRecommendations = $this->sanitizeUtf8($recommendationsResult['recommendations'] ?? []);

                // Sayfa ID varsa sonuçları kaydet
                if ($pageId) {
                    $this->saveSeoRecommendations($pageId, $cleanRecommendations, $language);
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'recommendations' => $cleanRecommendations,
                        'total_count' => count($cleanRecommendations),
                        'language' => $language,
                        'generated_at' => now()->toISOString(),
                        'from_cache' => false
                    ],
                    'message' => 'SEO önerileri başarıyla oluşturuldu'
                ], 200, [], JSON_UNESCAPED_UNICODE);
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

    /**
     * MEVCUT AI SEO ÖNERİLERİNİ KONTROL ET
     */
    private function getExistingRecommendations(int $pageId, string $language): ?array
    {
        try {
            $page = \Modules\Page\App\Models\Page::with('seoSetting')->find($pageId);

            if (!$page || !$page->seoSetting) {
                return null;
            }

            $aiSuggestions = $page->seoSetting->ai_suggestions;

            if (!$aiSuggestions || !isset($aiSuggestions[$language])) {
                return null;
            }

            $langRecommendations = $aiSuggestions[$language];

            // Son 24 saat içinde oluşturulmuş mu kontrol et
            if (isset($langRecommendations['generated_at'])) {
                $generatedAt = \Carbon\Carbon::parse($langRecommendations['generated_at']);
                if ($generatedAt->diffInHours(now()) > 24) {
                    return null; // 24 saatten eski ise yeniden üret
                }
            }

            return $langRecommendations;

        } catch (\Exception $e) {
            Log::error('Failed to get existing recommendations', [
                'page_id' => $pageId,
                'language' => $language,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * AI SEO ÖNERİLERİNİ VERİTABANINA KAYDET
     */
    private function saveSeoRecommendations(int $pageId, array $recommendations, string $language): void
    {
        try {
            // Page modelini SeoSetting ile birlikte bul
            $page = \Modules\Page\App\Models\Page::with('seoSetting')->find($pageId);

            if (!$page) {
                Log::warning('Page not found for SEO recommendations save', ['page_id' => $pageId]);
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

            // Mevcut ai_suggestions'ı al
            $aiSuggestions = $page->seoSetting->ai_suggestions ?? [];

            // Bu dil için önerileri kaydet
            $aiSuggestions[$language] = [
                'recommendations' => $recommendations,
                'generated_at' => now()->toISOString(),
                'total_count' => count($recommendations)
            ];

            // Güncelle
            $page->seoSetting->update([
                'ai_suggestions' => $aiSuggestions
            ]);

            Log::info('SEO Recommendations Saved Successfully', [
                'page_id' => $pageId,
                'language' => $language,
                'recommendations_count' => count($recommendations)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save SEO recommendations', [
                'page_id' => $pageId,
                'language' => $language,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * DİNAMİK DİL LİSTESİ ENDPOINT - tenant_languages tablosundan
     */
    public function getAvailableLanguages(Request $request): JsonResponse
    {
        try {
            // tenant_languages tablosundan aktif ve görünür dilleri al
            $allLanguages = TenantLanguageProvider::getActiveLanguages()
                ->where('is_visible', true)
                ->map(function ($lang) {
                    return [
                        'code' => $lang->code,
                        'name' => $lang->native_name ?? $lang->name,
                        'direction' => $lang->direction ?? 'ltr',
                        'is_default' => $lang->is_default ?? false,
                        'is_main' => $lang->is_main_language ?? false,
                        'sort_order' => $lang->sort_order ?? 999,
                        'flag_emoji' => $lang->flag_emoji ?? null
                    ];
                });

            // Varsayılan dili bul
            $defaultLanguage = $allLanguages->where('is_default', true)->first()
                            ?? $allLanguages->sortBy('sort_order')->first()
                            ?? ['code' => 'tr', 'name' => 'Türkçe'];

            // Varsayılan dili başa al, geri kalanları sort_order'a göre sırala
            $sortedLanguages = $allLanguages->sortBy([
                ['is_default', 'desc'],  // Varsayılan önce
                ['is_main_language', 'desc'],  // Ana dil ikinci
                ['sort_order', 'asc']    // Sort order üçüncü
            ])->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'languages' => $sortedLanguages,
                    'default_language' => $defaultLanguage['code'],
                    'total_count' => $sortedLanguages->count(),
                    'main_language' => $allLanguages->where('is_main_language', true)->first()['code'] ?? $defaultLanguage['code']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get available languages from tenant_languages', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'tenant_languages tablosundan dil listesi alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * UTF-8 karakterleri temizle ve JSON uyumlu hale getir
     */
    private function sanitizeUtf8($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeUtf8'], $data);
        }

        if (is_string($data)) {
            // UTF-8 encoding'i koru - sadece null karakterleri temizle
            $data = str_replace("\x00", '', $data);
            // Geçerli UTF-8 kontrolü
            if (!mb_check_encoding($data, 'UTF-8')) {
                $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            }
            return $data;
        }

        return $data;
    }
}