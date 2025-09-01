<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\AI\App\Jobs\TranslatePageJob;
use App\Services\TenantQueueService;

class TranslationController extends Controller
{
    /**
     * 🚀 BACKGROUND PAGE TRANSLATION
     * Çeviri işlemini queue'ya atar, user'a anında dönüş yapar
     */
    public function translatePageAsync(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'page_id' => 'required|integer|exists:pages,page_id',
                'source_language' => 'required|string|size:2',
                'target_language' => 'required|string|size:2'
            ]);

            $pageId = $request->get('page_id');
            $sourceLanguage = $request->get('source_language');
            $targetLanguage = $request->get('target_language');
            $userId = auth()->id();

            Log::info('🌍 Background translation requested with tenant isolation', [
                'page_id' => $pageId,
                'source' => $sourceLanguage,
                'target' => $targetLanguage,
                'user_id' => $userId,
                'tenant_id' => tenant()?->id,
                'tenant_info' => $tenantInfo
            ]);

            // Job'u queue'ya dispatch et
            // 🎆 DYNAMIC TENANT-AWARE DISPATCH - Prevents system-wide crashes
            $connection = TenantQueueService::getQueueConnection();
            $tenantInfo = TenantQueueService::getTenantInfo();
            
            $job = TranslatePageJob::dispatch(
                $pageId,
                $sourceLanguage,
                $targetLanguage,
                $userId
            )->onConnection($connection);

            return response()->json([
                'success' => true,
                'message' => 'Çeviri işlemi ANINDA başlatıldı. Background\'da devam ediyor.',
                'data' => [
                    'page_id' => $pageId,
                    'source_language' => $sourceLanguage,
                    'target_language' => $targetLanguage,
                    'status' => 'queued',
                    'estimated_time' => '2-5 dakika',
                    'job_dispatched' => true
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veriler',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Background translation dispatch failed', [
                'page_id' => $request->get('page_id'),
                'source' => $request->get('source_language'),
                'target' => $request->get('target_language'),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Çeviri işlemi başlatılamadı',
                'error' => config('app.debug') ? $e->getMessage() : 'Sistem hatası'
            ], 500);
        }
    }

    /**
     * 📊 TRANSLATION STATUS CHECK
     * Job durumunu kontrol et
     */
    public function checkTranslationStatus(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'page_id' => 'required|integer',
                'target_language' => 'required|string|size:2'
            ]);

            // Session'dan mesajları kontrol et
            $successMessage = session('translation_success');
            $errorMessage = session('translation_error');

            if ($successMessage) {
                return response()->json([
                    'success' => true,
                    'status' => 'completed',
                    'message' => 'Çeviri başarıyla tamamlandı!',
                    'data' => $successMessage
                ]);
            }

            if ($errorMessage) {
                return response()->json([
                    'success' => false,
                    'status' => 'failed',
                    'message' => 'Çeviri işlemi başarısız!',
                    'error' => $errorMessage
                ]);
            }

            // Default: İşlem devam ediyor
            return response()->json([
                'success' => true,
                'status' => 'processing',
                'message' => 'Çeviri işlemi devam ediyor...',
                'data' => [
                    'page_id' => $request->get('page_id'),
                    'target_language' => $request->get('target_language'),
                    'estimated_remaining_time' => '1-3 dakika'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Translation status check failed', [
                'page_id' => $request->get('page_id'),
                'target_language' => $request->get('target_language'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'status' => 'unknown',
                'message' => 'Durum kontrol edilemedi',
                'error' => config('app.debug') ? $e->getMessage() : 'Sistem hatası'
            ], 500);
        }
    }

    /**
     * ⚡ INSTANT TRANSLATION (Small content only)
     * Küçük içerikler için anında çeviri
     */
    public function translateInstant(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'content' => 'required|string|max:1000', // Max 1000 karakter
                'source_language' => 'required|string|size:2',
                'target_language' => 'required|string|size:2',
                'content_type' => 'sometimes|in:text,html'
            ]);

            $content = $request->get('content');
            $sourceLanguage = $request->get('source_language');
            $targetLanguage = $request->get('target_language');
            $contentType = $request->get('content_type', 'text');

            // AI Service kullanarak anında çevir
            $aiService = app(\Modules\AI\app\Services\AIService::class);
            
            $translatedContent = $aiService->translateText(
                $content,
                $sourceLanguage,
                $targetLanguage,
                [
                    'context' => 'instant_translation',
                    'preserve_html' => $contentType === 'html',
                    'max_length' => 1000
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'İçerik başarıyla çevrildi',
                'data' => [
                    'original_content' => $content,
                    'translated_content' => $translatedContent,
                    'source_language' => $sourceLanguage,
                    'target_language' => $targetLanguage,
                    'content_type' => $contentType,
                    'character_count' => strlen($content),
                    'translation_type' => 'instant'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Instant translation failed', [
                'content_length' => strlen($request->get('content', '')),
                'source' => $request->get('source_language'),
                'target' => $request->get('target_language'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Çeviri başarısız',
                'error' => config('app.debug') ? $e->getMessage() : 'Sistem hatası'
            ], 500);
        }
    }
}