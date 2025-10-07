<?php

namespace Modules\AI\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\SeoManagement\App\Services\SeoAIService;
use Modules\SeoManagement\App\Services\SeoRecommendationsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
                'page_id' => 'nullable|integer',
                'model_id' => 'nullable|integer',
                'model_type' => 'nullable|string',
                'model_class' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            $modelId = $request->input('model_id', $request->input('page_id'));
            $formContent = $request->input('form_content', []);
            $language = $request->input('language', TenantLanguageProvider::getDefaultLanguageCode());
            $modelType = $this->determineModelType($request, $formContent);
            $modelClass = $this->determineModelClass($request, $formContent, $modelType);

            // Force yeniden oluşturma kontrolü - query parametresi varsa cache'i atla
            $forceRegenerate = $request->input('force_regenerate', false) || $request->query('force_regenerate', false);

            // Sayfa ID varsa ve force regenerate değilse, önce kaydedilmiş önerileri kontrol et
            if ($modelId && !$forceRegenerate) {
                $existingRecommendations = $this->getExistingRecommendations($modelId, $language, $modelType, $modelClass);
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
                    'page_id' => $modelId,
                    'model_id' => $modelId,
                    'model_type' => $modelType,
                    'model_class' => $modelClass
                ]
            );

            // Başarılı yanıt - UTF-8 FIX
            if ($recommendationsResult['success']) {
                // UTF-8 karakterleri temizle
                $cleanRecommendations = $this->sanitizeUtf8($recommendationsResult['recommendations'] ?? []);

                // Sayfa ID varsa sonuçları kaydet
                if ($modelId) {
                    $this->saveSeoRecommendations($modelId, $cleanRecommendations, $language, $modelType, $modelClass);
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
    private function getExistingRecommendations(int $modelId, string $language, ?string $modelType = null, ?string $modelClass = null): ?array
    {
        try {
            $model = $this->resolveSeoModel($modelId, $modelType, $modelClass);

            if (!$model || !$model->seoSetting) {
                return null;
            }

            $aiSuggestions = $model->seoSetting->ai_suggestions;

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
                'model_id' => $modelId,
                'language' => $language,
                'model_type' => $modelType,
                'model_class' => $modelClass,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * AI SEO ÖNERİLERİNİ VERİTABANINA KAYDET
     */
    private function saveSeoRecommendations(int $modelId, array $recommendations, string $language, ?string $modelType = null, ?string $modelClass = null): void
    {
        try {
            $model = $this->resolveSeoModel($modelId, $modelType, $modelClass);

            if (!$model) {
                Log::warning('Model not found for SEO recommendations save', [
                    'model_id' => $modelId,
                    'model_type' => $modelType,
                    'model_class' => $modelClass
                ]);
                return;
            }

            $seoSetting = $this->ensureSeoSettingExists($model);

            // Mevcut ai_suggestions'ı al
            $aiSuggestions = $seoSetting->ai_suggestions ?? [];

            // Bu dil için önerileri kaydet
            $aiSuggestions[$language] = [
                'recommendations' => $recommendations,
                'generated_at' => now()->toISOString(),
                'total_count' => count($recommendations)
            ];

            // Güncelle
            $seoSetting->update([
                'ai_suggestions' => $aiSuggestions
            ]);

            Log::info('SEO Recommendations Saved Successfully', [
                'model_type' => get_class($model),
                'model_id' => $modelId,
                'language' => $language,
                'recommendations_count' => count($recommendations),
                'ai_suggestions_saved' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save SEO recommendations', [
                'model_id' => $modelId,
                'language' => $language,
                'model_type' => $modelType,
                'model_class' => $modelClass,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function determineModelType(Request $request, array $formContent): ?string
    {
        $candidates = [
            $request->input('model_type'),
            $request->input('module_name'),
            $request->input('module'),
            $request->input('context_module'),
            data_get($formContent, 'model_type'),
            data_get($formContent, 'module'),
            data_get($formContent, 'module_name'),
            data_get($formContent, 'seoable_type'),
            data_get($formContent, 'context_module'),
            data_get($formContent, 'seoDataCache.model_type'),
            data_get($formContent, 'seoDataCache.context_module'),
            data_get($formContent, 'current_url'),
        ];

        return $this->normalizeModelTypeFromCandidates($candidates);
    }

    private function normalizeModelTypeFromCandidates(array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (!is_string($candidate) || trim($candidate) === '') {
                continue;
            }

            $normalized = $this->normalizeModelTypeString($candidate);
            if ($normalized) {
                return $normalized;
            }
        }

        return null;
    }

    private function normalizeModelTypeString(string $value): ?string
    {
        $source = trim($value);

        if (filter_var($source, FILTER_VALIDATE_URL)) {
            $path = parse_url($source, PHP_URL_PATH) ?: '';
            $source = $path ?: $source;
        }

        $source = strtolower($source);

        $map = [
            'announcement' => 'announcement',
            'announcements' => 'announcement',
            'modules\announcement\app\models\announcement' => 'announcement',
            'portfolio' => 'portfolio',
            'modules\portfolio\app\models\portfolio' => 'portfolio',
            'page' => 'page',
            'modules\page\app\models\page' => 'page',
        ];

        foreach ($map as $needle => $mapped) {
            if (str_contains($source, $needle)) {
                return $mapped;
            }
        }

        if (str_starts_with($source, 'modules\\')) {
            $parts = explode('\\', $source);
            if (isset($parts[1]) && $parts[1] !== '') {
                return Str::slug($parts[1]);
            }
        }

        if (str_contains($source, '_')) {
            return Str::slug(str_replace('_', ' ', $source));
        }

        return null;
    }

    private function resolveSeoModel(int $modelId, ?string $modelType = null, ?string $modelClass = null)
    {
        $candidateClasses = [];

        if ($modelClass && is_string($modelClass)) {
            $candidateClasses[] = $modelClass;
        }

        if ($modelType && class_exists($modelType)) {
            $candidateClasses[] = $modelType;
        }

        $modelClassMap = [
            'page' => \Modules\Page\App\Models\Page::class,
            'announcement' => \Modules\Announcement\App\Models\Announcement::class,
            'portfolio' => \Modules\Portfolio\App\Models\Portfolio::class,
        ];

        if ($modelType && isset($modelClassMap[$modelType])) {
            $candidateClasses[] = $modelClassMap[$modelType];
        }

        // Guess class from module slug (Modules\Foo\App\Models\Foo)
        foreach ($this->buildModelClassGuesses($modelType) as $guess) {
            $candidateClasses[] = $guess;
        }

        // Remove duplicates
        $candidateClasses = array_values(array_unique(array_filter($candidateClasses)));

        foreach ($candidateClasses as $candidateClass) {
            if (!is_string($candidateClass) || !class_exists($candidateClass)) {
                continue;
            }

            $model = $candidateClass::with('seoSetting')->find($modelId);
            if ($model) {
                return $model;
            }
        }

        return null;
    }

    private function determineModelClass(Request $request, array $formContent, ?string $modelType = null): ?string
    {
        $candidates = [
            $request->input('model_class'),
            $request->input('seoable_type'),
            data_get($formContent, 'model_class'),
            data_get($formContent, 'seoable_type'),
            data_get($formContent, 'modelClass'),
        ];

        foreach ($candidates as $candidate) {
            $normalized = $this->normalizeClassName($candidate);
            if ($normalized && class_exists($normalized)) {
                return $normalized;
            }
        }

        foreach ($this->buildModelClassGuesses($modelType) as $guess) {
            if (class_exists($guess)) {
                return $guess;
            }
        }

        return null;
    }

    private function normalizeClassName($candidate): ?string
    {
        if (!is_string($candidate) || trim($candidate) === '') {
            return null;
        }

        $normalized = str_replace(['/', '\\'], '\\', trim($candidate));
        $normalized = ltrim($normalized, '\\');

        return $normalized !== '' ? $normalized : null;
    }

    private function buildModelClassGuesses(?string $modelType): array
    {
        if (!$modelType) {
            return [];
        }

        $slug = Str::slug(str_replace('\\', ' ', $modelType));
        if ($slug === '') {
            return [];
        }

        $studly = Str::studly(str_replace('-', ' ', $slug));
        $studly = str_replace(' ', '', $studly);

        $guesses = [];

        if ($studly !== '') {
            $guesses[] = "Modules\\{$studly}\\App\\Models\\{$studly}";

            $singular = Str::singular($studly);
            if ($singular && $singular !== $studly) {
                $guesses[] = "Modules\\{$studly}\\App\\Models\\{$singular}";
            }

            $guesses[] = "Modules\\{$studly}\\App\\Models\\{$studly}Item";
        }

        return array_unique($guesses);
    }

    private function ensureSeoSettingExists($model)
    {
        if (method_exists($model, 'getOrCreateSeoSetting')) {
            return $model->getOrCreateSeoSetting();
        }

        if ($model->seoSetting) {
            return $model->seoSetting;
        }

        $seoSetting = $model->seoSetting()->create([
            'titles' => [],
            'descriptions' => [],
            'keywords' => [],
        ]);

        $model->setRelation('seoSetting', $seoSetting);

        return $seoSetting;
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
