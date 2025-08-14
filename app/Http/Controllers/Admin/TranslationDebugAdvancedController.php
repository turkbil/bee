<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\Page\App\Models\Page;
use Modules\AI\App\Services\AIService;

class TranslationDebugAdvancedController extends Controller
{
    /**
     * Page çeviri sistemini gerçek zamanlı test et
     */
    public function testPageTranslation(Request $request)
    {
        $pageId = $request->input('page_id');
        $sourceLanguage = $request->input('source_language', 'tr');
        $targetLanguages = $request->input('target_languages', ['en']);
        $testMode = $request->input('test_mode', 'simulate'); // simulate veya real

        Log::info('🧪 ADVANCED PAGE TRANSLATION TEST BAŞLADI', [
            'page_id' => $pageId,
            'source_language' => $sourceLanguage,
            'target_languages' => $targetLanguages,
            'test_mode' => $testMode,
            'timestamp' => now(),
        ]);

        try {
            // 1. Page modelini yükle
            $page = Page::findOrFail($pageId);
            
            // 2. Aktif dilleri kontrol et
            $activeLanguages = TenantLanguage::where('is_active', true)
                ->pluck('code')
                ->toArray();

            // 3. Kaynak dil verilerini al
            $sourceData = [
                'title' => $page->getTranslated('title', $sourceLanguage),
                'body' => $page->getTranslated('body', $sourceLanguage),
                'slug' => $page->getTranslated('slug', $sourceLanguage),
            ];

            Log::info('📊 KAYNAK VERİLER ALINDI', [
                'source_data' => [
                    'title_length' => strlen($sourceData['title'] ?? ''),
                    'body_length' => strlen($sourceData['body'] ?? ''),
                    'slug_length' => strlen($sourceData['slug'] ?? ''),
                    'title_preview' => substr($sourceData['title'] ?? '', 0, 50),
                    'has_title' => !empty($sourceData['title']),
                    'has_body' => !empty($sourceData['body']),
                    'has_slug' => !empty($sourceData['slug']),
                ]
            ]);

            // 4. Her hedef dil için test
            $results = [];
            $aiService = app(AIService::class);

            foreach ($targetLanguages as $targetLang) {
                if (!in_array($targetLang, $activeLanguages)) {
                    $results[$targetLang] = [
                        'success' => false,
                        'error' => 'Target language not active',
                        'details' => "Language {$targetLang} is not in active languages: " . implode(', ', $activeLanguages)
                    ];
                    continue;
                }

                if ($targetLang === $sourceLanguage) {
                    $results[$targetLang] = [
                        'success' => false,
                        'error' => 'Source and target language are the same'
                    ];
                    continue;
                }

                Log::info("🔄 ÇEVİRİ TEST EDİLİYOR: {$sourceLanguage} -> {$targetLang}");

                $languageResult = [
                    'success' => false,
                    'translations' => [],
                    'errors' => [],
                    'timing' => [],
                ];

                // Title çevirisi test
                if (!empty($sourceData['title'])) {
                    $startTime = microtime(true);
                    try {
                        $translatedTitle = $aiService->translateText(
                            $sourceData['title'],
                            $sourceLanguage,
                            $targetLang,
                            ['context' => 'title', 'max_length' => 255]
                        );
                        $endTime = microtime(true);

                        $languageResult['translations']['title'] = [
                            'original' => $sourceData['title'],
                            'translated' => $translatedTitle,
                            'success' => !empty($translatedTitle) && $translatedTitle !== $sourceData['title'],
                            'duration_ms' => round(($endTime - $startTime) * 1000, 2),
                        ];

                        Log::info("✅ TITLE ÇEVİRİSİ BAŞARILI: {$targetLang}", [
                            'original' => $sourceData['title'],
                            'translated' => $translatedTitle,
                            'duration_ms' => $languageResult['translations']['title']['duration_ms'],
                        ]);

                    } catch (\Exception $e) {
                        $languageResult['errors']['title'] = $e->getMessage();
                        Log::error("❌ TITLE ÇEVİRİSİ HATASI: {$targetLang}", [
                            'error' => $e->getMessage(),
                            'original_text' => $sourceData['title'],
                        ]);
                    }
                }

                // Body çevirisi test (sadece ilk 500 karakter)
                if (!empty($sourceData['body'])) {
                    $bodyPreview = substr(strip_tags($sourceData['body']), 0, 500);
                    if (!empty($bodyPreview)) {
                        $startTime = microtime(true);
                        try {
                            $translatedBody = $aiService->translateText(
                                $bodyPreview,
                                $sourceLanguage,
                                $targetLang,
                                ['context' => 'html_content', 'preserve_html' => false]
                            );
                            $endTime = microtime(true);

                            $languageResult['translations']['body'] = [
                                'original' => $bodyPreview,
                                'translated' => $translatedBody,
                                'success' => !empty($translatedBody) && $translatedBody !== $bodyPreview,
                                'duration_ms' => round(($endTime - $startTime) * 1000, 2),
                            ];

                            Log::info("✅ BODY ÇEVİRİSİ BAŞARILI: {$targetLang}", [
                                'original_length' => strlen($bodyPreview),
                                'translated_length' => strlen($translatedBody),
                                'duration_ms' => $languageResult['translations']['body']['duration_ms'],
                            ]);

                        } catch (\Exception $e) {
                            $languageResult['errors']['body'] = $e->getMessage();
                            Log::error("❌ BODY ÇEVİRİSİ HATASI: {$targetLang}", [
                                'error' => $e->getMessage(),
                                'original_length' => strlen($bodyPreview),
                            ]);
                        }
                    }
                }

                // Sonuç değerlendirme
                $languageResult['success'] = count($languageResult['translations']) > 0 && 
                                           count($languageResult['errors']) === 0;

                $results[$targetLang] = $languageResult;
            }

            // 5. Test modu: Gerçek çeviri uygula
            if ($testMode === 'real') {
                Log::info('🚀 GERÇEK ÇEVİRİ MODU AKTIF - VERİLER KAYDEDILECEK');

                foreach ($results as $targetLang => $result) {
                    if ($result['success']) {
                        try {
                            // Mevcut hedef dil verilerini al
                            $currentTargetData = [
                                'title' => $page->getTranslated('title', $targetLang),
                                'body' => $page->getTranslated('body', $targetLang),
                                'slug' => $page->getTranslated('slug', $targetLang),
                            ];

                            // JSON verilerini güncelle
                            $titleJson = json_decode($page->title, true) ?: [];
                            $bodyJson = json_decode($page->body, true) ?: [];
                            $slugJson = json_decode($page->slug, true) ?: [];

                            // Yeni çevirileri ekle
                            if (isset($result['translations']['title'])) {
                                $titleJson[$targetLang] = $result['translations']['title']['translated'];
                            }

                            if (isset($result['translations']['body'])) {
                                // Body için tam çeviri yap (sadece preview değil)
                                $fullTranslatedBody = $aiService->translateText(
                                    $sourceData['body'],
                                    $sourceLanguage,
                                    $targetLang,
                                    ['context' => 'html_content', 'preserve_html' => true]
                                );
                                $bodyJson[$targetLang] = $fullTranslatedBody;
                            }

                            // Slug oluştur
                            if (isset($result['translations']['title'])) {
                                $slugJson[$targetLang] = \Str::slug($result['translations']['title']['translated']);
                            }

                            // Veritabanına kaydet
                            $page->update([
                                'title' => json_encode($titleJson),
                                'body' => json_encode($bodyJson),
                                'slug' => json_encode($slugJson),
                            ]);

                            Log::info("✅ VERİLER KAYDEDILDI: {$targetLang}", [
                                'title_updated' => isset($result['translations']['title']),
                                'body_updated' => isset($result['translations']['body']),
                                'slug_updated' => isset($result['translations']['title']),
                            ]);

                        } catch (\Exception $e) {
                            Log::error("❌ VERİ KAYDETME HATASI: {$targetLang}", [
                                'error' => $e->getMessage(),
                            ]);
                            $results[$targetLang]['save_error'] = $e->getMessage();
                        }
                    }
                }
            }

            // 6. Genel özet
            $summary = [
                'total_targets' => count($targetLanguages),
                'successful' => count(array_filter($results, fn($r) => $r['success'])),
                'failed' => count(array_filter($results, fn($r) => !$r['success'])),
                'ai_service_available' => true,
                'active_languages' => $activeLanguages,
            ];

            Log::info('🏁 PAGE TRANSLATION TEST TAMAMLANDI', [
                'summary' => $summary,
                'test_mode' => $testMode,
            ]);

            return response()->json([
                'success' => true,
                'summary' => $summary,
                'results' => $results,
                'source_data' => $sourceData,
                'page_info' => [
                    'id' => $page->page_id,
                    'title' => $page->getTranslated('title', $sourceLanguage),
                ],
                'test_mode' => $testMode,
            ]);

        } catch (\Exception $e) {
            Log::error('🚨 PAGE TRANSLATION TEST HATASI', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Page'in mevcut çoklu dil verilerini analiz et
     */
    public function analyzePageData(Request $request)
    {
        $pageId = $request->input('page_id');

        try {
            $page = Page::findOrFail($pageId);
            $activeLanguages = TenantLanguage::where('is_active', true)->pluck('code')->toArray();

            $analysis = [
                'page_info' => [
                    'id' => $page->page_id,
                    'created_at' => $page->created_at,
                    'updated_at' => $page->updated_at,
                    'is_active' => $page->is_active,
                ],
                'raw_data' => [
                    'title' => $page->getAttributes()['title'],
                    'body' => substr($page->getAttributes()['body'] ?? '', 0, 200) . '...',
                    'slug' => $page->getAttributes()['slug'],
                ],
                'parsed_data' => [],
                'analysis' => [
                    'has_multi_lang_title' => false,
                    'has_multi_lang_body' => false,
                    'has_multi_lang_slug' => false,
                    'languages_with_title' => [],
                    'languages_with_body' => [],
                    'languages_with_slug' => [],
                    'empty_languages' => [],
                    'active_languages' => $activeLanguages,
                ],
            ];

            // Her aktif dil için veri analizi
            foreach ($activeLanguages as $lang) {
                $titleData = $page->getTranslated('title', $lang);
                $bodyData = $page->getTranslated('body', $lang);
                $slugData = $page->getTranslated('slug', $lang);

                $analysis['parsed_data'][$lang] = [
                    'title' => [
                        'value' => $titleData,
                        'length' => strlen($titleData ?? ''),
                        'has_content' => !empty($titleData),
                    ],
                    'body' => [
                        'value' => substr($bodyData ?? '', 0, 100) . '...',
                        'length' => strlen($bodyData ?? ''),
                        'has_content' => !empty($bodyData),
                    ],
                    'slug' => [
                        'value' => $slugData,
                        'length' => strlen($slugData ?? ''),
                        'has_content' => !empty($slugData),
                    ],
                ];

                // İstatistikleri güncelle
                if (!empty($titleData)) {
                    $analysis['analysis']['languages_with_title'][] = $lang;
                }
                if (!empty($bodyData)) {
                    $analysis['analysis']['languages_with_body'][] = $lang;
                }
                if (!empty($slugData)) {
                    $analysis['analysis']['languages_with_slug'][] = $lang;
                }

                // Boş dil kontrolü
                if (empty($titleData) && empty($bodyData) && empty($slugData)) {
                    $analysis['analysis']['empty_languages'][] = $lang;
                }
            }

            // Multi-lang kontrolleri
            $analysis['analysis']['has_multi_lang_title'] = count($analysis['analysis']['languages_with_title']) > 1;
            $analysis['analysis']['has_multi_lang_body'] = count($analysis['analysis']['languages_with_body']) > 1;
            $analysis['analysis']['has_multi_lang_slug'] = count($analysis['analysis']['languages_with_slug']) > 1;

            Log::info('📊 PAGE DATA ANALYSIS TAMAMLANDI', [
                'page_id' => $pageId,
                'languages_analyzed' => count($activeLanguages),
                'has_multi_lang_content' => $analysis['analysis']['has_multi_lang_title'] || $analysis['analysis']['has_multi_lang_body'],
            ]);

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ PAGE DATA ANALYSIS HATASI', [
                'page_id' => $pageId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Çeviri sistemini tamamen debug et
     */
    public function fullSystemDebug()
    {
        try {
            $debug = [
                'timestamp' => now()->toISOString(),
                'system_status' => [],
                'language_system' => [],
                'ai_system' => [],
                'page_system' => [],
                'test_results' => [],
            ];

            // 1. Sistem durumu
            $debug['system_status'] = [
                'tenant_id' => tenant('id'),
                'tenant_name' => tenant('name'),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'memory_usage' => memory_get_usage(true),
                'memory_peak' => memory_get_peak_usage(true),
            ];

            // 2. Dil sistemi
            $activeLanguages = TenantLanguage::where('is_active', true)->get();
            $debug['language_system'] = [
                'total_languages' => TenantLanguage::count(),
                'active_count' => $activeLanguages->count(),
                'active_languages' => $activeLanguages->pluck('code')->toArray(),
                'default_language' => \App\Services\TenantLanguageProvider::getDefaultLanguageCode(),
                'language_details' => $activeLanguages->map(function($lang) {
                    return [
                        'code' => $lang->code,
                        'name' => $lang->name,
                        'native_name' => $lang->native_name,
                        'is_default' => $lang->is_default ?? false,
                        'sort_order' => $lang->sort_order,
                    ];
                }),
            ];

            // 3. AI sistemi
            try {
                $aiService = app(AIService::class);
                $testTranslation = $aiService->translateText('Hello', 'en', 'tr', ['context' => 'test']);
                
                $debug['ai_system'] = [
                    'service_available' => true,
                    'test_translation' => [
                        'input' => 'Hello',
                        'output' => $testTranslation,
                        'success' => !empty($testTranslation) && $testTranslation !== 'Hello',
                    ],
                ];
            } catch (\Exception $e) {
                $debug['ai_system'] = [
                    'service_available' => false,
                    'error' => $e->getMessage(),
                ];
            }

            // 4. Page sistemi
            $totalPages = Page::count();
            $samplePage = Page::first();
            
            $debug['page_system'] = [
                'total_pages' => $totalPages,
                'has_sample' => !is_null($samplePage),
                'sample_analysis' => null,
            ];

            if ($samplePage) {
                $debug['page_system']['sample_analysis'] = [
                    'id' => $samplePage->page_id,
                    'title_type' => gettype($samplePage->getAttributes()['title']),
                    'title_is_json' => is_string($samplePage->getAttributes()['title']) && 
                                     json_decode($samplePage->getAttributes()['title'], true) !== null,
                    'has_translations_trait' => in_array('App\Traits\HasTranslations', class_uses($samplePage)),
                ];

                // Çoklu dil test
                $multiLangTest = [];
                foreach ($debug['language_system']['active_languages'] as $lang) {
                    $multiLangTest[$lang] = [
                        'title' => $samplePage->getTranslated('title', $lang),
                        'has_title' => !empty($samplePage->getTranslated('title', $lang)),
                    ];
                }
                $debug['page_system']['sample_analysis']['multi_lang_test'] = $multiLangTest;
            }

            // 5. Test sonuçları
            if ($samplePage && $debug['ai_system']['service_available']) {
                try {
                    $testRequest = new Request([
                        'page_id' => $samplePage->page_id,
                        'source_language' => 'tr',
                        'target_languages' => ['en'],
                        'test_mode' => 'simulate',
                    ]);

                    $testResponse = $this->testPageTranslation($testRequest);
                    $testData = json_decode($testResponse->getContent(), true);
                    
                    $debug['test_results'] = [
                        'full_translation_test' => $testData['success'] ?? false,
                        'test_summary' => $testData['summary'] ?? null,
                    ];
                } catch (\Exception $e) {
                    $debug['test_results'] = [
                        'full_translation_test' => false,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            Log::info('🔍 FULL SYSTEM DEBUG TAMAMLANDI', [
                'systems_checked' => 5,
                'ai_available' => $debug['ai_system']['service_available'],
                'languages_count' => $debug['language_system']['active_count'],
                'pages_count' => $debug['page_system']['total_pages'],
            ]);

            return response()->json([
                'success' => true,
                'debug' => $debug,
            ]);

        } catch (\Exception $e) {
            Log::error('🚨 FULL SYSTEM DEBUG HATASI', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }
}