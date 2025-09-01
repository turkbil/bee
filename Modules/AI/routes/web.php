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

// 🔍 SEO ANALYSIS ROUTES
Route::prefix('admin/seo/ai')->middleware(['auth', 'web'])->group(function () {
    Route::post('/analyze', [\Modules\AI\App\Http\Controllers\Admin\SeoAnalysisController::class, 'analyze'])
        ->name('seo.analyze');
    
    // 🎯 SEO RECOMMENDATIONS ROUTE
    Route::post('/recommendations', [\Modules\AI\App\Http\Controllers\Admin\SeoAnalysisController::class, 'generateRecommendations'])
        ->name('seo.recommendations');
});