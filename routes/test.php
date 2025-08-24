<?php

use Modules\SeoManagement\App\Services\SeoAIService;
use Modules\SeoManagement\App\Http\Controllers\Admin\SeoAIController;
use Modules\Page\App\Models\Page;
use Illuminate\Http\Request;

// SEO AI Controller Test Route
Route::post('/test-seo-controller', function (Request $request) {
    try {
        $seoController = app(SeoAIController::class);
        
        $testRequest = Request::create('/test', 'POST', [
            'feature_slug' => 'seo-comprehensive-audit',
            'form_content' => [
                'page_title' => 'Test Başlık - Hakkımızda',
                'page_description' => 'Test açıklama',
                'page_content' => 'Test içerik hakkımızda sayfası',
                'page_url' => 'https://laravel.test/test',
                'target_keywords' => 'test, seo'
            ]
        ]);
        
        $response = $seoController->analyze($testRequest);
        
        return response()->json([
            'controller_test' => 'success',
            'response_status' => $response->getStatusCode(),
            'response_data' => $response->getData(true)
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'controller_test' => 'failed',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// SEO AI Test Route
Route::get('/test-seo-ai', function () {
    try {
        // İlk sayfayı al
        $page = Page::first();
        if (!$page) {
            return response()->json([
                'error' => 'Test için sayfa bulunamadı',
                'message' => 'Önce bir sayfa oluşturun'
            ], 404);
        }

        $seoAIService = app(SeoAIService::class);
        
        // Test verisi hazırla
        $formContent = [
            'page_title' => $page->getSeoTitle() ?? 'Test Başlık',
            'page_description' => $page->getSeoDescription() ?? 'Test açıklama',
            'page_content' => strip_tags($page->body['tr'] ?? $page->body['en'] ?? 'Test içerik'),
            'page_url' => url('/'),
            'target_keywords' => 'seo, optimizasyon, web sitesi'
        ];

        // SEO analizi yap
        $result = $seoAIService->analyzeSEO('seo-comprehensive-audit', $formContent);

        return response()->json([
            'test_status' => 'completed',
            'page_id' => $page->id,
            'form_content' => $formContent,
            'ai_result' => $result,
            'timestamp' => now()->toISOString()
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'error' => 'Test sırasında hata oluştu',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});