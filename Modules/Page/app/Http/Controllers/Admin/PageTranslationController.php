<?php

declare(strict_types=1);

namespace Modules\Page\App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Page\App\Models\Page;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\AI\App\Services\AIService;
use Illuminate\Support\Str;

/**
 * 🌍 Page Translation Controller - Simplified Version
 * 
 * Bu controller Page modülündeki çeviri işlemlerini yönetir.
 * Sadece is_visible=true olan dillere çeviri yapar.
 */
class PageTranslationController extends Controller
{
    public function __construct(
        private readonly AIService $aiService
    ) {}

    /**
     * Tekli veya toplu çeviri başlat
     */
    public function translateMulti(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'source_language' => 'required|string|max:5',
                'target_languages' => 'required|array|min:1',
                'target_languages.*' => 'string|max:5',
                'selected_items' => 'required|array|min:1',
                'selected_items.*' => 'integer|exists:pages,page_id',
                'include_seo' => 'boolean'
            ]);

            $sourceLanguage = $request->input('source_language');
            $targetLanguages = $request->input('target_languages');
            $selectedItems = $request->input('selected_items');
            $includeSeo = $request->boolean('include_seo', false);

            // Sadece görünür dilleri filtrele
            $visibleLanguages = TenantLanguage::where('is_visible', true)
                ->whereIn('language_code', $targetLanguages)
                ->pluck('language_code')
                ->toArray();

            if (empty($visibleLanguages)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seçilen diller arasında görünür dil bulunamadı'
                ], 400);
            }

            $operationId = 'page_trans_' . substr(uniqid(), 0, 13);
            $results = [];
            $totalTranslations = 0;
            $successCount = 0;
            $failedCount = 0;

            // Cache'de başlangıç durumu
            Cache::put("translation_progress_{$operationId}", [
                'status' => 'initializing',
                'progress' => 8,
                'message' => '⚡ Çeviri motoru başlatılıyor, lütfen bekleyin...',
                'operation_id' => $operationId
            ], 300);

            foreach ($selectedItems as $pageId) {
                $page = Page::find($pageId);
                
                if (!$page) {
                    $failedCount++;
                    continue;
                }

                // Her dil için çeviri yap
                foreach ($visibleLanguages as $targetLang) {
                    if ($targetLang === $sourceLanguage) {
                        continue; // Kaynak dili atla
                    }

                    $totalTranslations++;
                    
                    try {
                        // Kaynak içeriği al
                        $sourceTitle = $page->getTranslated('title', $sourceLanguage) ?? '';
                        $sourceBody = $page->getTranslated('body', $sourceLanguage) ?? '';
                        $sourceExcerpt = $page->getTranslated('excerpt', $sourceLanguage) ?? '';

                        if (empty(trim($sourceTitle . $sourceBody))) {
                            Log::info("Empty content for page {$pageId} in {$sourceLanguage}");
                            continue;
                        }

                        // Çeviri yap
                        $translatedData = $this->translateContent(
                            $sourceTitle,
                            $sourceBody,
                            $sourceExcerpt,
                            $sourceLanguage,
                            $targetLang
                        );

                        if ($translatedData) {
                            // Mevcut JSON verileri al
                            $titles = is_string($page->title) ? json_decode($page->title, true) : $page->title;
                            $bodies = is_string($page->body) ? json_decode($page->body, true) : $page->body;
                            $excerpts = is_string($page->excerpt) ? json_decode($page->excerpt, true) : $page->excerpt;
                            $slugs = is_string($page->slug) ? json_decode($page->slug, true) : $page->slug;

                            // Yeni çevirileri ekle
                            $titles[$targetLang] = $translatedData['title'];
                            $bodies[$targetLang] = $translatedData['body'];
                            $excerpts[$targetLang] = $translatedData['excerpt'] ?? '';
                            $slugs[$targetLang] = Str::slug($translatedData['title']);

                            // Güncelle
                            $page->update([
                                'title' => $titles,
                                'body' => $bodies,
                                'excerpt' => $excerpts,
                                'slug' => $slugs
                            ]);

                            $successCount++;
                            
                            Log::info("✅ Page {$pageId} translated to {$targetLang}");
                        } else {
                            $failedCount++;
                            Log::error("❌ Translation failed for page {$pageId} to {$targetLang}");
                        }

                    } catch (\Exception $e) {
                        $failedCount++;
                        Log::error("Translation error: " . $e->getMessage());
                    }

                    // Progress güncelle
                    $progress = $totalTranslations > 0 
                        ? round((($successCount + $failedCount) / $totalTranslations) * 100) 
                        : 0;
                    
                    Cache::put("translation_progress_{$operationId}", [
                        'status' => 'processing',
                        'progress' => $progress,
                        'message' => "📝 Çeviriler işleniyor... ({$successCount} başarılı, {$failedCount} başarısız)",
                        'operation_id' => $operationId,
                        'success_count' => $successCount,
                        'failed_count' => $failedCount
                    ], 300);
                }
            }

            // Final durum
            Cache::put("translation_progress_{$operationId}", [
                'status' => 'completed',
                'progress' => 100,
                'message' => "✅ Çeviri tamamlandı! {$successCount} başarılı, {$failedCount} başarısız",
                'operation_id' => $operationId,
                'success_count' => $successCount,
                'failed_count' => $failedCount
            ], 300);

            return response()->json([
                'success' => true,
                'operation_id' => $operationId,
                'message' => 'Çeviri işlemi tamamlandı',
                'results' => [
                    'total' => $totalTranslations,
                    'success' => $successCount,
                    'failed' => $failedCount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Translation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Çeviri işlemi sırasında hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * İçeriği çevir
     */
    private function translateContent(
        string $title,
        string $body,
        string $excerpt,
        string $sourceLang,
        string $targetLang
    ): ?array {
        try {
            // Çeviri prompt'u oluştur
            $prompt = "Sen profesyonel bir çevirmensin. Aşağıdaki metni {$sourceLang} dilinden {$targetLang} diline çevir.

CONTENT TO TRANSLATE:
Title: {$title}
Body: {$body}
Excerpt: {$excerpt}

RULES:
1. Preserve HTML tags and formatting
2. Keep the same structure
3. Translate naturally for the target language
4. Return response in this JSON format:
{
    \"title\": \"translated title\",
    \"body\": \"translated body\",
    \"excerpt\": \"translated excerpt\"
}

IMPORTANT: Return ONLY the JSON object, no additional text.";

            // AI Service ile çeviri yap
            $response = $this->aiService->processRequest(
                prompt: $prompt,
                maxTokens: 2000,
                temperature: 0.3,
                metadata: [
                    'source' => 'page_translation',
                    'source_lang' => $sourceLang,
                    'target_lang' => $targetLang
                ]
            );

            Log::info("🔍 AI Translation Response", [
                'source_lang' => $sourceLang,
                'target_lang' => $targetLang,
                'response_keys' => array_keys($response),
                'success' => $response['success'] ?? false
            ]);

            if ($response['success'] ?? false) {
                // Response'dan içeriği al
                $content = $response['data']['content'] ?? 
                          $response['content'] ?? 
                          $response['response'] ?? 
                          $response['choices'][0]['message']['content'] ?? '';

                // JSON parse et
                if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
                    $jsonData = json_decode($matches[0], true);
                    if ($jsonData && isset($jsonData['title'])) {
                        return $jsonData;
                    }
                }

                // Fallback: Düz metin olarak parse et
                Log::warning("Could not parse JSON, using fallback", ['content' => substr($content, 0, 200)]);
                return [
                    'title' => $title, // Fallback olarak orijinal başlık
                    'body' => $content ?: $body,
                    'excerpt' => $excerpt
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error("Translation content error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Çeviri durumunu kontrol et
     */
    public function checkProgress(Request $request): JsonResponse
    {
        $operationId = $request->input('operation_id');
        
        if (!$operationId) {
            return response()->json([
                'success' => false,
                'message' => 'Operation ID gerekli'
            ], 400);
        }

        $progress = Cache::get("translation_progress_{$operationId}");

        if (!$progress) {
            return response()->json([
                'success' => false,
                'status' => 'unknown',
                'message' => 'İşlem bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $progress['status'] ?? 'unknown',
            'progress' => $progress['progress'] ?? 0,
            'message' => $progress['message'] ?? '',
            'operation_id' => $operationId,
            'success_count' => $progress['success_count'] ?? 0,
            'failed_count' => $progress['failed_count'] ?? 0
        ]);
    }
}