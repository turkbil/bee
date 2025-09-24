<?php

use Illuminate\Support\Facades\Route;
use Modules\AI\App\Http\Controllers\Admin\ModelCreditRateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Frontend routes burada olacak

// ✨ PHASE 3: Model Credit Rate Management API (Moved from api.php for CSRF support)
Route::prefix('api/ai/admin')->name('ai.admin.')->middleware(['auth:web'])->group(function () {
    
    // 🏷️ Provider & Model Selection APIs
    Route::get('/providers-models', [ModelCreditRateController::class, 'getProvidersWithModels'])
        ->name('providers.models');
    
    Route::get('/provider/{providerId}/models', [ModelCreditRateController::class, 'getProviderModels'])
        ->name('provider.models');
    
    // 💰 Credit Calculation APIs
    Route::post('/calculate-cost', [ModelCreditRateController::class, 'calculateCreditCost'])
        ->name('calculate.cost');
    
    Route::post('/compare-models', [ModelCreditRateController::class, 'compareModels'])
        ->name('compare.models');
    
    // 🏢 Tenant Configuration APIs
    Route::get('/tenant-config/{tenantId?}', [ModelCreditRateController::class, 'getTenantConfiguration'])
        ->name('tenant.config');
    
    // 🚀 Enhanced HTML Translation Progress API
    Route::get('/translation/progress/{translationId}', function ($translationId) {
        $aiService = app(\Modules\AI\App\Services\AIService::class);
        return response()->json($aiService->getTranslationProgress($translationId));
    })->name('translation.progress');
    
    Route::get('/translation/result/{translationId}', function ($translationId) {
        $aiService = app(\Modules\AI\App\Services\AIService::class);
        return response()->json($aiService->getFinalTranslationResult($translationId));
    })->name('translation.result');
    
    // 🚀 Global AI Content Generation API (Pattern: AI Translation)
    Route::prefix('content')->name('content.')->group(function () {

        // Generate content
        Route::post('/generate', function (\Illuminate\Http\Request $request) {
            $contentService = app(\Modules\AI\app\Services\Content\AIContentGeneratorService::class);

            $result = $contentService->generateContentAsync([
                'prompt' => $request->input('prompt'),
                'target_field' => $request->input('target_field', 'body'),
                'replace_existing' => $request->boolean('replace_existing', true),
                'module' => $request->input('module', 'page'),
                'component' => $request->input('component')
            ]);

            return response()->json($result);
        })->name('generate');

        // Check job status
        Route::get('/status/{jobId}', function ($jobId) {
            $contentService = app(\Modules\AI\app\Services\Content\AIContentGeneratorService::class);
            return response()->json($contentService->getJobStatus($jobId));
        })->name('status');

        // Get job result
        Route::get('/result/{jobId}', function ($jobId) {
            $contentService = app(\Modules\AI\app\Services\Content\AIContentGeneratorService::class);
            $data = $contentService->getJobResult($jobId);
            // Fallback: progress cache veya session-map üzerinden içerik eşlemesi
            $content = $data['data']['content'] ?? $data['content'] ?? null;
            if (empty($content)) {
                $progress = \Illuminate\Support\Facades\Cache::get("ai_content_progress_{$jobId}");
                if (is_array($progress) && !empty($progress['content'])) {
                    return response()->json([
                        'success' => true,
                        'data' => [ 'content' => $progress['content'] ]
                    ]);
                }
                $sessionId = \Illuminate\Support\Facades\Cache::get("ai_content_job_map_{$jobId}");
                if ($sessionId) {
                    $result = \Illuminate\Support\Facades\Cache::get("ai_content_result_{$sessionId}");
                    if (is_array($result) && !empty($result['content'])) {
                        return response()->json([
                            'success' => true,
                            'data' => [ 'content' => $result['content'] ]
                        ]);
                    }
                }
            }
            return response()->json($data);
        })->name('result');
    });

    // 🚀 Enhanced HTML Translation Start API
    Route::post('/enhanced-translation', function (\Illuminate\Http\Request $request) {
        $aiService = app(\Modules\AI\App\Services\AIService::class);

        $result = $aiService->translateHtmlAsync(
            $request->entity_type,
            $request->entity_id,
            $request->field,
            $request->html_content,
            $request->source_language,
            $request->target_language,
            $request->options ?? []
        );
        
        return response()->json($result);
    })->name('enhanced.translation');
    
    // 🚀 Get Current Content API
    Route::post('/get-content', function (\Illuminate\Http\Request $request) {
        try {
            $entityType = $request->entity_type;
            $entityId = $request->entity_id;
            $field = $request->field ?? 'body';
            
            switch ($entityType) {
                case 'page':
                    $entity = \Modules\Page\App\Models\Page::find($entityId);
                    break;
                case 'portfolio':
                    $entity = \Modules\Portfolio\App\Models\Portfolio::find($entityId);
                    break;
                case 'announcement':
                    $entity = \Modules\Announcement\App\Models\Announcement::find($entityId);
                    break;
                default:
                    throw new \Exception('Unsupported entity type');
            }
            
            if (!$entity) {
                throw new \Exception('Entity not found');
            }
            
            $sourceLanguage = app()->getLocale();
            $content = $entity->getTranslated($field, $sourceLanguage);
            
            return response()->json([
                'success' => true,
                'content' => $content,
                'source_language' => $sourceLanguage
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    })->name('get.content');
        
});

// 🚀 GLOBAL AI CONTENT GENERATION - Direct Routes
Route::prefix('admin/ai')->middleware(['auth', 'web'])->group(function () {

    // 🆕 File analysis route - ASYNC VERSION
    Route::post('/analyze-files', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'files' => 'required|array|max:5',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png,webp|max:102400', // 100MB
            'analysis_type' => 'required|in:layout_preserve,content_extract'
        ]);

        try {
            // Async analiz için unique ID oluştur
            $analysisId = \Illuminate\Support\Str::uuid()->toString();

            // Session ID'yi al veya oluştur
            $sessionId = session()->getId() ?? \Illuminate\Support\Str::uuid()->toString();

            // Dosyaları base64 olarak hazırla (Job için serialize edilebilir)
            $files = [];
            foreach ($request->file('files') as $file) {
                $files[] = [
                    'content' => base64_encode(file_get_contents($file->getPathname())),
                    'name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'extension' => $file->getClientOriginalExtension()
                ];
            }

            // Async Job'u başlat - sistemi bloklamaz!
            \Modules\AI\App\Jobs\FileAnalysisJob::dispatch(
                $files,
                $request->input('analysis_type', 'content_extract'),
                $analysisId,
                $sessionId
            )->onQueue('ai-file-analysis'); // High priority queue

            // Hemen cevap dön (sistem bloklanmaz)
            return response()->json([
                'success' => true,
                'analysis_id' => $analysisId,
                'message' => 'Dosya analizi başlatıldı',
                'status_url' => route('ai.files.analyze.status', $analysisId)
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('❌ File analysis dispatch error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Dosya analizi başlatılamadı'
            ], 500);
        }
    })->name('ai.files.analyze');

    // 🆕 File analysis status check route
    Route::get('/analyze-files/status/{analysisId}', function ($analysisId) {
        try {
            $status = \Illuminate\Support\Facades\Cache::get("file_analysis_{$analysisId}", [
                'status' => 'pending',
                'progress' => 0,
                'message' => 'İşlem bekliyor...'
            ]);

            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    })->name('ai.files.analyze.status');

    // Generate content async - Enhanced with file support
    Route::post('/generate-content-async', function (\Illuminate\Http\Request $request) {
        try {
            $contentService = app(\Modules\AI\app\Services\Content\AIContentGeneratorService::class);

            // Start async job with enhanced parameters
            $jobId = $contentService->startAsyncGeneration([
                'prompt' => $request->input('prompt'),
                'target_field' => $request->input('target_field', 'body'),
                'replace_existing' => $request->boolean('replace_existing', true),
                'module' => $request->input('module', 'page'),
                'component' => $request->input('component'),
                // 🆕 File analysis results
                'file_analysis' => $request->input('file_analysis'),
                'conversion_type' => $request->input('conversion_type', 'content_extract')
            ]);

            return response()->json([
                'success' => true,
                'job_id' => $jobId
            ]);
        } catch (\Throwable $e) {
            \Log::error('❌ generate-content-async error: ' . $e->getMessage(), [
                'file' => $e->getFile(), 'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    })->name('ai.content.generate.async');

    // Check job progress
    Route::get('/job-progress/{jobId}', function ($jobId) {
        try {
            $progress = \Illuminate\Support\Facades\Cache::get("ai_content_progress_{$jobId}", [
                'progress' => 0,
                'message' => 'İşlem bekliyor...',
                'status' => 'pending',
                'content' => null
            ]);
            if (!is_array($progress) || (empty($progress) && $progress !== 0)) {
                $progress = \Illuminate\Support\Facades\Cache::store('file')->get("ai_content_progress_{$jobId}", [
                    'progress' => 0,
                    'message' => 'İşlem bekliyor...',
                    'status' => 'pending',
                    'content' => null
                ]);
            }

            // Eğer completed görünüyor ama content boş ise, job ↔ session eşlemesi ile içeriği bulmayı dene
            if (($progress['status'] ?? '') === 'completed' && empty($progress['content'])) {
                $sessionId = \Illuminate\Support\Facades\Cache::get("ai_content_job_map_{$jobId}")
                    ?? \Illuminate\Support\Facades\Cache::store('file')->get("ai_content_job_map_{$jobId}");
                if ($sessionId) {
                    // 1) Sonuç cache'inden dene
                    $resultKey = "ai_content_result_{$sessionId}";
                    $result = \Illuminate\Support\Facades\Cache::get($resultKey)
                        ?? \Illuminate\Support\Facades\Cache::store('file')->get($resultKey);
                    if (is_array($result) && !empty($result['content'])) {
                        $progress['content'] = $result['content'];
                    }
                    // 2) Job cache'inden dene
                    if (empty($progress['content'])) {
                        $jobCache = \Illuminate\Support\Facades\Cache::get("ai_content_job:{$jobId}");
                        if (is_array($jobCache) && !empty($jobCache['content'])) {
                            $progress['content'] = $jobCache['content'];
                        }
                    }
                } else {
                    // Eşleşme yoksa doğrudan job cache'i dene
                    $jobCache = \Illuminate\Support\Facades\Cache::get("ai_content_job:{$jobId}")
                        ?? \Illuminate\Support\Facades\Cache::store('file')->get("ai_content_job:{$jobId}");
                    if (is_array($jobCache) && !empty($jobCache['content'])) {
                        $progress['content'] = $jobCache['content'];
                    }
                }
            }

            return response()->json($progress);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    })->name('ai.content.progress');
});

// 🔍 SEO ANALYSIS ROUTES
Route::prefix('admin/seo/ai')->middleware(['auth', 'web'])->group(function () {
    Route::post('/analyze', [\Modules\AI\App\Http\Controllers\Admin\SeoAnalysisController::class, 'analyze'])
        ->name('seo.analyze');
    
    // 🎯 SEO RECOMMENDATIONS ROUTE
    Route::post('/recommendations', [\Modules\AI\App\Http\Controllers\Admin\SeoAnalysisController::class, 'generateRecommendations'])
        ->name('seo.recommendations');
});
