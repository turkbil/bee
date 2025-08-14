<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\Page\App\Models\Page;
use Modules\AI\App\Services\AIService;
use App\Services\TenantLanguageProvider;

class TranslationDebugController extends Controller
{
    /**
     * Debug sayfası
     */
    public function index()
    {
        // Debug verilerini topla
        $debugData = $this->collectDebugData();
        
        return view('admin.debug.translation', compact('debugData'));
    }

    /**
     * Debug verilerini topla
     */
    private function collectDebugData(): array
    {
        $tenant = tenant();
        
        return [
            'tenant_info' => [
                'id' => $tenant?->id,
                'name' => $tenant?->name ?? 'Unknown',
                'default_locale' => $tenant?->tenant_default_locale ?? 'tr',
                'current_domain' => request()->getHost(),
                'tenancy_initialized' => app(\Stancl\Tenancy\Tenancy::class)->initialized,
            ],
            'language_system' => $this->getLanguageSystemInfo(),
            'session_data' => $this->getSessionData(),
            'cache_data' => $this->getCacheData(),
            'ai_system' => $this->getAISystemInfo(),
            'page_system' => $this->getPageSystemInfo(),
            'error_logs' => $this->getRecentErrorLogs(),
        ];
    }

    /**
     * Dil sistemi bilgileri
     */
    private function getLanguageSystemInfo(): array
    {
        try {
            $languages = TenantLanguage::all();
            $activeLanguages = $languages->where('is_active', true);
            $defaultLanguage = TenantLanguageProvider::getDefaultLanguageCode();
            
            return [
                'total_languages' => $languages->count(),
                'active_languages' => $activeLanguages->pluck('code')->toArray(),
                'inactive_languages' => $languages->where('is_active', false)->pluck('code')->toArray(),
                'hidden_languages' => $languages->where('is_visible', false)->pluck('code')->toArray(),
                'default_language' => $defaultLanguage,
                'language_details' => $languages->map(function($lang) {
                    return [
                        'id' => $lang->id,
                        'code' => $lang->code,
                        'name' => $lang->name,
                        'native_name' => $lang->native_name,
                        'is_active' => $lang->is_active,
                        'is_visible' => $lang->is_visible,
                        'is_default' => $lang->is_default ?? false,
                        'sort_order' => $lang->sort_order,
                    ];
                })->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Language system error: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ];
        }
    }

    /**
     * Session verileri
     */
    private function getSessionData(): array
    {
        return [
            'site_locale' => session('site_locale'),
            'admin_locale' => session('admin_locale'),
            'site_default_language' => session('site_default_language'),
            'js_current_language' => session('js_current_language'),
            'page_manage_language' => session('page_manage_language'),
            'page_continue_mode' => session('page_continue_mode'),
            'js_saved_language' => session('js_saved_language'),
            'all_session_keys' => array_keys(session()->all()),
        ];
    }

    /**
     * Cache verileri
     */
    private function getCacheData(): array
    {
        try {
            $tenantId = tenant('id');
            $cacheKeys = [
                "tenant_{$tenantId}_languages",
                "tenant_{$tenantId}_active_languages",
                "tenant_{$tenantId}_default_language",
                "ai_provider_status",
                "ai_translation_cache",
            ];

            $cacheData = [];
            foreach ($cacheKeys as $key) {
                $cacheData[$key] = [
                    'exists' => Cache::has($key),
                    'value' => Cache::get($key, 'NOT_FOUND'),
                    'ttl' => Cache::get($key . '_ttl', 'UNKNOWN'),
                ];
            }

            return $cacheData;
        } catch (\Exception $e) {
            return [
                'error' => 'Cache system error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * AI sistemi bilgileri
     */
    private function getAISystemInfo(): array
    {
        try {
            $aiService = app(AIService::class);
            
            // AI provider durumunu kontrol et
            $providerStatus = [
                'available' => true,
                'provider_name' => 'Unknown',
                'model' => 'Unknown',
                'error' => null,
            ];

            try {
                // Basit bir test çevirisi yap
                $testTranslation = $aiService->translateText(
                    'Hello World',
                    'en',
                    'tr',
                    ['context' => 'test']
                );
                
                $providerStatus['test_translation'] = $testTranslation;
                $providerStatus['test_successful'] = !empty($testTranslation) && $testTranslation !== 'Hello World';
            } catch (\Exception $e) {
                $providerStatus['available'] = false;
                $providerStatus['error'] = $e->getMessage();
                $providerStatus['test_successful'] = false;
            }

            return [
                'provider_status' => $providerStatus,
                'translation_features' => [
                    'multi_language_support' => true,
                    'html_preservation' => true,
                    'context_aware' => true,
                    'bulk_translation' => true,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'AI system error: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ];
        }
    }

    /**
     * Page sistemi bilgileri
     */
    private function getPageSystemInfo(): array
    {
        try {
            $totalPages = Page::count();
            $activePages = Page::where('is_active', true)->count();
            
            // JSON alanlarını kontrol et
            $samplePage = Page::first();
            $jsonFieldsInfo = [];
            
            if ($samplePage) {
                $jsonFieldsInfo = [
                    'title_structure' => [
                        'raw' => $samplePage->getAttributes()['title'] ?? null,
                        'type' => gettype($samplePage->getAttributes()['title'] ?? null),
                        'is_json' => is_string($samplePage->getAttributes()['title']) && 
                                   json_decode($samplePage->getAttributes()['title'], true) !== null,
                    ],
                    'body_structure' => [
                        'raw' => substr($samplePage->getAttributes()['body'] ?? '', 0, 100) . '...',
                        'type' => gettype($samplePage->getAttributes()['body'] ?? null),
                        'is_json' => is_string($samplePage->getAttributes()['body']) && 
                                   json_decode($samplePage->getAttributes()['body'], true) !== null,
                    ],
                    'slug_structure' => [
                        'raw' => $samplePage->getAttributes()['slug'] ?? null,
                        'type' => gettype($samplePage->getAttributes()['slug'] ?? null),
                        'is_json' => is_string($samplePage->getAttributes()['slug']) && 
                                   json_decode($samplePage->getAttributes()['slug'], true) !== null,
                    ],
                ];
            }

            return [
                'statistics' => [
                    'total_pages' => $totalPages,
                    'active_pages' => $activePages,
                    'inactive_pages' => $totalPages - $activePages,
                ],
                'json_fields' => $jsonFieldsInfo,
                'traits' => [
                    'HasTranslations' => class_uses(Page::class)['App\Traits\HasTranslations'] ?? false,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Page system error: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ];
        }
    }

    /**
     * Son hata loglarını al
     */
    private function getRecentErrorLogs(): array
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (!file_exists($logFile)) {
                return ['error' => 'Log file not found'];
            }

            $logs = [];
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $lines = array_reverse($lines); // Son logları ilk sıraya al

            $count = 0;
            $maxLogs = 50; // Son 50 log kaydı

            foreach ($lines as $line) {
                if ($count >= $maxLogs) break;

                // Translation ile ilgili logları filtrele
                if (stripos($line, 'translation') !== false || 
                    stripos($line, 'language') !== false ||
                    stripos($line, 'AI ') !== false ||
                    stripos($line, 'çeviri') !== false ||
                    stripos($line, 'ERROR') !== false) {
                    
                    $logs[] = [
                        'timestamp' => $this->extractTimestamp($line),
                        'level' => $this->extractLogLevel($line),
                        'message' => $line,
                        'is_translation_related' => $this->isTranslationRelated($line),
                    ];
                    $count++;
                }
            }

            return [
                'recent_logs' => $logs,
                'log_file_size' => filesize($logFile),
                'log_file_path' => $logFile,
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Log reading error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Log satırından timestamp çıkar
     */
    private function extractTimestamp(string $line): ?string
    {
        if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Log seviyesini çıkar
     */
    private function extractLogLevel(string $line): string
    {
        if (stripos($line, 'ERROR') !== false) return 'ERROR';
        if (stripos($line, 'WARNING') !== false) return 'WARNING';
        if (stripos($line, 'INFO') !== false) return 'INFO';
        if (stripos($line, 'DEBUG') !== false) return 'DEBUG';
        return 'UNKNOWN';
    }

    /**
     * Çeviri ile ilgili mi kontrol et
     */
    private function isTranslationRelated(string $line): bool
    {
        $keywords = [
            'translation', 'translate', 'çeviri', 'language', 'dil',
            'AI Translation', 'translateText', 'switchLanguage',
            'multiLangInputs', 'currentLanguage', 'SEO'
        ];

        foreach ($keywords as $keyword) {
            if (stripos($line, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Canlı test çevirisi yap
     */
    public function testTranslation(Request $request)
    {
        $sourceText = $request->input('text', 'Test metni');
        $sourceLang = $request->input('from', 'tr');
        $targetLang = $request->input('to', 'en');

        Log::info('🧪 TRANSLATION DEBUG TEST BAŞLADI', [
            'source_text' => $sourceText,
            'source_lang' => $sourceLang,
            'target_lang' => $targetLang,
            'timestamp' => now(),
            'user_id' => auth()->id(),
            'tenant_id' => tenant('id'),
        ]);

        try {
            $aiService = app(AIService::class);
            
            $startTime = microtime(true);
            $translatedText = $aiService->translateText(
                $sourceText,
                $sourceLang,
                $targetLang,
                [
                    'context' => 'debug_test',
                    'preserve_html' => false,
                ]
            );
            $endTime = microtime(true);
            $duration = ($endTime - $startTime) * 1000; // ms

            Log::info('✅ TRANSLATION DEBUG TEST BAŞARILI', [
                'translated_text' => $translatedText,
                'duration_ms' => round($duration, 2),
                'success' => true,
            ]);

            return response()->json([
                'success' => true,
                'original' => $sourceText,
                'translated' => $translatedText,
                'duration_ms' => round($duration, 2),
                'from_lang' => $sourceLang,
                'to_lang' => $targetLang,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ TRANSLATION DEBUG TEST HATASI', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'original' => $sourceText,
                'from_lang' => $sourceLang,
                'to_lang' => $targetLang,
            ], 500);
        }
    }

    /**
     * Log dosyasını temizle
     */
    public function clearLogs()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
                
                Log::info('🗑️ LOG DOSYASI TEMİZLENDİ', [
                    'user_id' => auth()->id(),
                    'timestamp' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Log dosyası temizlendi',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cache temizle
     */
    public function clearCache()
    {
        try {
            $tenantId = tenant('id');
            $cacheKeys = [
                "tenant_{$tenantId}_languages",
                "tenant_{$tenantId}_active_languages", 
                "tenant_{$tenantId}_default_language",
                "ai_provider_status",
                "ai_translation_cache",
            ];

            $clearedCount = 0;
            foreach ($cacheKeys as $key) {
                if (Cache::has($key)) {
                    Cache::forget($key);
                    $clearedCount++;
                }
            }

            // Genel cache temizliği
            Cache::flush();

            Log::info('🗑️ CACHE TEMİZLENDİ', [
                'cleared_keys' => $clearedCount,
                'user_id' => auth()->id(),
                'timestamp' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Cache temizlendi ({$clearedCount} anahtar)",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Real-time log akışı
     */
    public function streamLogs()
    {
        return response()->stream(function () {
            $logFile = storage_path('logs/laravel.log');
            
            if (!file_exists($logFile)) {
                echo "data: " . json_encode(['error' => 'Log file not found']) . "\n\n";
                return;
            }

            $lastSize = 0;
            $handle = fopen($logFile, 'r');

            while (true) {
                clearstatcache();
                $currentSize = filesize($logFile);

                if ($currentSize > $lastSize) {
                    fseek($handle, $lastSize);
                    $newContent = fread($handle, $currentSize - $lastSize);
                    
                    $lines = explode("\n", $newContent);
                    foreach ($lines as $line) {
                        if (!empty(trim($line)) && $this->isTranslationRelated($line)) {
                            echo "data: " . json_encode([
                                'timestamp' => now()->toISOString(),
                                'message' => $line,
                                'level' => $this->extractLogLevel($line),
                            ]) . "\n\n";
                            ob_flush();
                            flush();
                        }
                    }
                    
                    $lastSize = $currentSize;
                }

                sleep(1); // 1 saniye bekle
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }
}