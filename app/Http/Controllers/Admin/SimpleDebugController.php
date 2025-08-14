<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SimpleDebugController extends Controller
{
    /**
     * Debug ana sayfa
     */
    public function index()
    {
        return view('admin.debug.simple');
    }

    /**
     * Gerçek zamanlı log stream
     */
    public function streamLogs(Request $request)
    {
        return response()->stream(function () {
            $logFile = storage_path('logs/laravel.log');
            $lastSize = 0;
            
            // İlk log içeriğini gönder
            if (file_exists($logFile)) {
                $content = file_get_contents($logFile);
                $lastSize = strlen($content);
                
                // Son 100 satırı al
                $lines = explode("\n", $content);
                $recentLines = array_slice($lines, -100);
                
                echo "data: " . json_encode([
                    'type' => 'initial',
                    'content' => implode("\n", $recentLines),
                    'timestamp' => now()->format('H:i:s')
                ]) . "\n\n";
                
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }
            
            // Canlı takip
            while (true) {
                if (connection_aborted()) {
                    break;
                }
                
                if (file_exists($logFile)) {
                    $currentSize = filesize($logFile);
                    
                    if ($currentSize > $lastSize) {
                        // Yeni içerik var
                        $handle = fopen($logFile, 'r');
                        fseek($handle, $lastSize);
                        $newContent = fread($handle, $currentSize - $lastSize);
                        fclose($handle);
                        
                        if (!empty(trim($newContent))) {
                            echo "data: " . json_encode([
                                'type' => 'new',
                                'content' => $newContent,
                                'timestamp' => now()->format('H:i:s')
                            ]) . "\n\n";
                            
                            if (ob_get_level()) {
                                ob_flush();
                            }
                            flush();
                        }
                        
                        $lastSize = $currentSize;
                    }
                }
                
                sleep(1); // 1 saniye bekle
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no'
        ]);
    }

    /**
     * Log'ları temizle
     */
    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Log dosyası temizlendi',
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Sistem durumu
     */
    public function systemStatus()
    {
        try {
            // Tenant bilgisi
            $tenant = tenant();
            $tenantInfo = $tenant ? [
                'id' => $tenant->id,
                'domain' => $tenant->getFirstDomain()
            ] : 'Central domain';

            // Dil sistemi
            $languages = \Modules\LanguageManagement\app\Models\TenantLanguage::where('is_active', true)->get(['code', 'name']);

            // AI Provider
            $aiProvider = \Modules\AI\app\Models\AIProvider::where('is_default', true)->first();

            return response()->json([
                'tenant' => $tenantInfo,
                'languages' => $languages,
                'ai_provider' => $aiProvider ? [
                    'name' => $aiProvider->name,
                    'model' => $aiProvider->default_model,
                    'status' => $aiProvider->is_active ? 'active' : 'inactive'
                ] : 'No provider',
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 500);
        }
    }

    /**
     * Çeviri testi
     */
    public function testTranslation(Request $request)
    {
        try {
            $text = $request->input('text', 'Test metni');
            $from = $request->input('from', 'tr');
            $to = $request->input('to', 'en');

            $aiService = app(\Modules\AI\app\Services\AIService::class);
            
            \Log::info('🧪 MANUAL TRANSLATION TEST STARTED', [
                'text' => $text,
                'from' => $from,
                'to' => $to,
                'user_id' => auth()->id(),
                'timestamp' => now()->toISOString()
            ]);

            $result = $aiService->translateText($text, $from, $to);

            \Log::info('🧪 MANUAL TRANSLATION TEST COMPLETED', [
                'original' => $text,
                'translated' => $result,
                'success' => !empty($result),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'original' => $text,
                'translated' => $result,
                'from' => $from,
                'to' => $to,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            \Log::error('🧪 MANUAL TRANSLATION TEST FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 500);
        }
    }
}