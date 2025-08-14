<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Admin\Translation;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\AI\App\Services\OpenAIService;
use Modules\AI\App\Services\AICreditService;
use Modules\AI\App\Services\Translation\CentralizedTranslationService;
use Modules\AI\App\Jobs\TranslateContentJob;

/**
 * Global AI Translation Controller
 * Handles AI-powered translations for all modules
 */
class GlobalTranslationController extends Controller
{
    public function __construct(
        private readonly OpenAIService $aiService,
        private readonly AICreditService $creditService,
        private readonly CentralizedTranslationService $translationService
    ) {}

    /**
     * Estimate tokens for translation
     */
    public function estimateTokens(Request $request): JsonResponse
    {
        try {
            $items = $request->input('items', []);
            $targetLanguages = $request->input('target_languages', []);
            $sourceLanguage = $request->input('source_language', 'tr');
            $includeSeo = $request->input('include_seo', false);
            $module = $this->detectModule($request);
            
            // CentralizedTranslationService kullanarak token tahmini yap
            $estimationConfig = [
                'items' => $items,
                'source_language' => $sourceLanguage,
                'target_languages' => $targetLanguages,
                'module' => $module,
                'include_seo' => $includeSeo,
                'user_id' => auth()->id(),
                'estimate_only' => true // Sadece tahmin yapmak için flag
            ];
            
            $estimation = $this->translationService->estimateTokensAndCost($estimationConfig);
            
            // Check credit balance
            $currentBalance = $this->creditService->getCurrentBalance(auth()->id());
            
            Log::info('🎯 Translation Credit Check', [
                'user_id' => auth()->id(),
                'estimated_tokens' => $estimation['total_tokens'],
                'estimated_cost' => $estimation['estimated_cost'],
                'current_balance' => $currentBalance,
                'sufficient_credit' => $currentBalance >= $estimation['estimated_cost'],
                'include_seo' => $includeSeo
            ]);
            
            return response()->json([
                'success' => true,
                'total_tokens' => $estimation['total_tokens'],
                'estimated_cost' => $estimation['estimated_cost'],
                'current_balance' => $currentBalance,
                'sufficient_credit' => $currentBalance >= $estimation['estimated_cost'],
                'item_count' => count($items),
                'language_count' => count($targetLanguages),
                'character_count' => $estimation['total_characters'],
                'estimated_time' => $this->estimateTime($estimation['total_tokens'])
            ]);
            
        } catch (\Exception $e) {
            Log::error('Global AI Translation Token Estimation Error', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Token tahmini yapılamadı'
            ], 500);
        }
    }

    /**
     * Start translation process for any module - PARÇALI ÇEVİRİ SİSTEMİ
     */
    public function startTranslation(Request $request): JsonResponse
    {
        try {
            $items = $request->input('items', []);
            $targetLanguages = $request->input('target_languages', []);
            $sourceLanguage = $request->input('source_language', 'tr');
            $quality = $request->input('quality', 'balanced');
            $includeSeo = $request->input('include_seo', false);
            $module = $this->detectModule($request);
            
            // TEK DİL Mİ YOKSA TÜM DİLLER Mİ?
            $singleLanguage = $request->input('single_language', null);
            
            if ($singleLanguage) {
                // TEK BİR DİL İÇİN ÇEVİRİ
                $targetLanguages = [$singleLanguage];
                Log::info('🌐 Single language translation', [
                    'language' => $singleLanguage,
                    'items_count' => count($items)
                ]);
            }

            // Kredi kontrolü - işlem başlamadan önce
            $currentBalance = $this->creditService->getCurrentBalance(auth()->id());
            Log::info('💳 Translation Credit Pre-Check', [
                'user_id' => auth()->id(),
                'current_balance' => $currentBalance,
                'items_count' => count($items),
                'target_languages' => $targetLanguages,
                'module' => $module,
                'include_seo' => $includeSeo
            ]);
            
            $operationId = $module . '_trans_' . uniqid();
            
            // ANINDA RESPONSE DÖNDÜR - ARKADA ÇALIŞACAK
            Log::info('🚀 Translation job başlatılıyor', [
                'operation_id' => $operationId,
                'items_count' => count($items),
                'languages' => count($targetLanguages)
            ]);
            
            // USER ID'yi kaydet (auth() background job'da çalışmaz)
            $userId = auth()->id();
            
            // Translation config hazırla
            $translationConfig = [
                'items' => $items,
                'source_language' => $sourceLanguage,
                'target_languages' => $targetLanguages,
                'quality' => $quality,
                'module' => $module,
                'include_seo' => $includeSeo,
                'user_id' => $userId
            ];
            
            // Queue kullan - ASLA SYNC ÇALIŞTIRMA
            Log::info('📦 Dispatching translation job to queue', [
                'operation_id' => $operationId,
                'single_language' => $singleLanguage,
                'items_count' => count($items),
                'languages_count' => count($targetLanguages)
            ]);
            
            // Başlangıç durumu
            cache()->put("translation_progress_{$operationId}", [
                'status' => 'starting',
                'progress' => 15,
                'message' => '🚀 Çeviri sistemi hazırlanıyor, yakında başlayacak...',
                'current_language' => $singleLanguage ?? 'all',
                'operation_id' => $operationId
            ], 600);
            
            // Job'ı dispatch et
            TranslateContentJob::dispatch($translationConfig, $operationId)
                ->onQueue('translations');
            
            // RESPONSE DÖNDÜR
            return response()->json([
                'success' => true,
                'operation_id' => $operationId,
                'message' => $singleLanguage 
                    ? "'{$singleLanguage}' dili için çeviri başlatıldı." 
                    : 'Çeviri işlemi başlatıldı.',
                'status' => 'started',
                'language' => $singleLanguage,
                'estimated_time' => $this->estimateTime(count($items) * count($targetLanguages) * 100)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Global AI Translation Start Error', [
                'module' => $module ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Çeviri işlemi başlatılamadı',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get translation progress
     */
    public function getProgress(string $operationId): JsonResponse
    {
        // Cache'den progress bilgisini al
        $progress = cache()->get("translation_progress_{$operationId}");
        
        // Only log important status changes, not every poll
        if (!$progress || in_array($progress['status'] ?? 'unknown', ['failed', 'completed'])) {
            Log::info('📊 Progress check', [
                'operation_id' => $operationId,
                'cache_exists' => $progress !== null,
                'status' => $progress['status'] ?? 'unknown'
            ]);
        }
        
        if (!$progress) {
            // Cache yoksa işlem henüz başlamamış veya silmiş
            return response()->json([
                'success' => true,
                'status' => 'initializing',
                'progress' => 8,
                'message' => '⚡ Çeviri motoru başlatılıyor, lütfen bekleyin...',
                'operation_id' => $operationId
            ]);
        }
        
        // Detaylı progress bilgisini döndür
        return response()->json(array_merge([
            'success' => true
        ], $progress));
    }


    /**
     * Detect module from request
     */
    private function detectModule(Request $request): string
    {
        // Check various possible sources for module name
        $module = $request->input('module') 
            ?? $request->header('X-Module')
            ?? $request->route('module')
            ?? 'page'; // Default to page if not specified
        
        return strtolower($module);
    }

    /**
     * Estimate time based on tokens
     */
    private function estimateTime(int|float $tokens): string
    {
        if ($tokens < 1000) return '30 saniye';
        if ($tokens < 5000) return '1-2 dakika';
        if ($tokens < 10000) return '2-5 dakika';
        if ($tokens < 20000) return '5-10 dakika';
        return '10+ dakika';
    }

    /**
     * Clean markdown formatting from AI response
     */
    private function cleanMarkdownFormatting(string $text): string
    {
        // Remove markdown code blocks
        $text = preg_replace('/^```[a-zA-Z]*\s*\n/m', '', $text);
        $text = preg_replace('/\n```\s*$/m', '', $text);
        $text = preg_replace('/```[a-zA-Z]*\s*/', '', $text);
        $text = preg_replace('/```/', '', $text);
        
        // Remove extra newlines created by code block removal
        $text = preg_replace('/^\s*\n+/', '', $text);
        $text = preg_replace('/\n+\s*$/', '', $text);
        
        // Remove any remaining markdown artifacts
        $text = preg_replace('/^\s*\\\s*/', '', $text);
        $text = preg_replace('/\\\s*$/', '', $text);
        
        return $text;
    }
    
    /**
     * Get available languages for translation
     */
    public function getAvailableLanguages(Request $request): JsonResponse
    {
        try {
            $languages = TenantLanguage::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'code'])
                ->toArray();
                
            return response()->json([
                'success' => true,
                'data' => [
                    'languages' => $languages
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch available languages', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch languages',
                'data' => [
                    'languages' => []
                ]
            ], 500);
        }
    }
}