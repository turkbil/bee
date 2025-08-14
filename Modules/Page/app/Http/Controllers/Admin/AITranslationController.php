<?php

declare(strict_types=1);

namespace Modules\Page\App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\AI\App\Services\UniversalInputAIService;
use Modules\Page\App\Models\Page;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Facades\Queue;
use Modules\Page\App\Jobs\TranslatePageJob;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

/**
 * 🌍 AI Translation Controller for Page Module
 * 
 * Bu controller Page modülündeki AI çeviri işlemlerini yönetir:
 * - Token tahmini hesaplama
 * - Çeviri işlemini başlatma  
 * - İlerleme durumunu takip etme
 */
class AITranslationController extends Controller
{
    public function __construct(
        private readonly UniversalInputAIService $aiService
    ) {}

    /**
     * Token kullanım tahminini hesapla
     */
    public function estimateTokens(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mode' => 'required|in:single,bulk',
            'page_id' => 'required_if:mode,single|integer|exists:pages,page_id',
            'selected_pages' => 'required_if:mode,bulk|array',
            'selected_pages.*' => 'integer|exists:pages,page_id',
            'source_language' => 'required|string|max:5',
            'target_languages' => 'required|array|min:1',
            'target_languages.*' => 'string|max:5',
            'quality' => 'in:fast,balanced,premium'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $mode = $request->input('mode');
        $quality = $request->input('quality', 'balanced');
        $sourceLanguage = $request->input('source_language');
        $targetLanguages = $request->input('target_languages');

        // Sayfa(ları) al
        if ($mode === 'single') {
            $pages = Page::where('page_id', $request->input('page_id'))->get();
        } else {
            $pages = Page::whereIn('page_id', $request->input('selected_pages'))->get();
        }

        if ($pages->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Sayfa bulunamadı'
            ], 404);
        }

        // Token hesaplama
        $totalTokens = 0;
        $totalCharacters = 0;

        foreach ($pages as $page) {
            $translations = $page->getTranslated('title', $sourceLanguage) ?? '';
            $translations .= ' ' . ($page->getTranslated('body', $sourceLanguage) ?? '');
            $translations .= ' ' . ($page->getTranslated('excerpt', $sourceLanguage) ?? '');
            
            $characterCount = mb_strlen(strip_tags($translations));
            $totalCharacters += $characterCount;
            
            // Dil sayısı × karakter sayısına göre token tahmini
            foreach ($targetLanguages as $targetLang) {
                if ($targetLang !== $sourceLanguage) {
                    $baseTokens = ceil($characterCount / 4); // Yaklaşık 4 karakter = 1 token
                    
                    // Kalite çarpanı
                    $qualityMultiplier = match($quality) {
                        'fast' => 1.0,
                        'balanced' => 1.5,
                        'premium' => 2.0,
                        default => 1.5
                    };
                    
                    $totalTokens += (int)($baseTokens * $qualityMultiplier);
                }
            }
        }

        return response()->json([
            'success' => true,
            'estimated_tokens' => $totalTokens,
            'pages_count' => $pages->count(),
            'target_languages_count' => count($targetLanguages),
            'character_count' => $totalCharacters,
            'quality' => $quality
        ]);
    }

    /**
     * Çeviri işlemini başlat
     */
    public function start(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'source_language' => 'required|string|max:5',
            'target_languages' => 'required|array|min:1',
            'target_languages.*' => 'string|max:5',
            'translation_quality' => 'in:fast,balanced,premium',
            'preserve_seo' => 'boolean',
            'preserve_formatting' => 'boolean',
            'cultural_adaptation' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Çeviri işlem ID'si oluştur
        $operationId = 'page_translation_' . Str::uuid();
        
        $sourceLanguage = $request->input('source_language');
        $targetLanguages = $request->input('target_languages');
        $quality = $request->input('translation_quality', 'balanced');
        
        $options = [
            'preserve_seo' => $request->boolean('preserve_seo', true),
            'preserve_formatting' => $request->boolean('preserve_formatting', true),
            'cultural_adaptation' => $request->boolean('cultural_adaptation', false),
        ];

        // Modal'dan gelen mode'u kontrol et (JavaScript'te belirleniyor)
        $mode = session('ai_translation_mode', 'single');
        
        if ($mode === 'bulk') {
            $pageIds = session('ai_translation_selected_pages', []);
        } else {
            $pageIds = [session('ai_translation_current_page', request()->input('page_id'))];
        }

        if (empty($pageIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Çevrilecek sayfa bulunamadı'
            ], 400);
        }

        // Cache'de işlem durumunu başlat
        Cache::put("translation_progress_{$operationId}", [
            'status' => 'started',
            'total' => count($pageIds) * count($targetLanguages),
            'processed' => 0,
            'success_count' => 0,
            'failed_count' => 0,
            'tokens_used' => 0,
            'created_at' => now()
        ], 3600); // 1 saat

        // Queue job'unu dispatch et
        TranslatePageJob::dispatch(
            $pageIds,
            $sourceLanguage,
            $targetLanguages,
            $quality,
            $options,
            $operationId
        );

        return response()->json([
            'success' => true,
            'operation_id' => $operationId,
            'message' => 'Çeviri işlemi başlatıldı',
            'pages_count' => count($pageIds),
            'target_languages' => $targetLanguages
        ]);
    }

    /**
     * İşlem ilerlemesini takip et
     */
    public function progress(string $operationId): JsonResponse
    {
        $progress = Cache::get("translation_progress_{$operationId}");

        if (!$progress) {
            return response()->json([
                'success' => false,
                'message' => 'İşlem bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $progress['status'],
            'total' => $progress['total'],
            'processed' => $progress['processed'],
            'success_count' => $progress['success_count'],
            'failed_count' => $progress['failed_count'],
            'tokens_used' => $progress['tokens_used'],
            'percentage' => $progress['total'] > 0 ? round(($progress['processed'] / $progress['total']) * 100) : 0
        ]);
    }
}