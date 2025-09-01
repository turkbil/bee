<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * 🚀 GERÇEK ZAMANLI TRANSLATION PROGRESS CONTROLLER
 * 
 * Laravel log'undan gerçek çeviri durumunu takip eder:
 * - Real-time progress tracking
 * - Log-based completion detection
 * - Session-based progress monitoring
 */
class TranslationProgressController extends Controller
{
    /**
     * Gerçek zamanlı progress tracking endpoint
     */
    public function checkProgress(Request $request)
    {
        $sessionId = $request->input('sessionId');
        $lastLogPosition = (int) $request->input('lastLogPosition', 0);
        
        if (empty($sessionId)) {
            return response()->json([
                'found' => false,
                'percentage' => 0,
                'message' => 'Session ID required',
                'completed' => false
            ]);
        }
        
        try {
            // Laravel log dosyasını oku
            $logPath = storage_path('logs/laravel.log');
            
            if (!File::exists($logPath)) {
                return response()->json([
                    'found' => false,
                    'percentage' => 0,
                    'message' => 'Log file not found',
                    'completed' => false
                ]);
            }
            
            // Log dosyasından session ile ilgili verileri çek
            $progressData = $this->parseLogForProgress($logPath, $sessionId, $lastLogPosition);
            
            return response()->json($progressData);
            
        } catch (\Exception $e) {
            Log::error('Translation progress check failed: ' . $e->getMessage());
            
            return response()->json([
                'found' => false,
                'percentage' => 0,
                'message' => 'Progress check failed',
                'completed' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Log dosyasından progress verilerini parse et
     */
    private function parseLogForProgress(string $logPath, string $sessionId, int $lastPosition): array
    {
        $handle = fopen($logPath, 'r');
        
        if (!$handle) {
            return [
                'found' => false,
                'percentage' => 0,
                'message' => 'Cannot read log file',
                'completed' => false,
                'logPosition' => $lastPosition
            ];
        }
        
        // Son pozisyondan devam et
        if ($lastPosition > 0) {
            fseek($handle, $lastPosition);
        }
        
        $currentPosition = ftell($handle);
        $progressData = [
            'found' => false,
            'percentage' => 0,
            'message' => '',
            'completed' => false,
            'success' => 0,
            'failed' => 0,
            'logPosition' => $currentPosition
        ];
        
        $latestProgress = 0;
        $latestMessage = '';
        $isCompleted = false;
        $successCount = 0;
        $failedCount = 0;
        
        // Log satırlarını oku
        while (($line = fgets($handle)) !== false) {
            $currentPosition = ftell($handle);
            
            // Bu session'a ait mi kontrol et
            if (strpos($line, $sessionId) === false) {
                continue;
            }
            
            // TRANSLATEPAGEJOB başlangıç log'u
            if (strpos($line, 'TRANSLATEPAGEJOB HANDLE() BAŞLADI!') !== false) {
                $latestProgress = max($latestProgress, 30);
                $latestMessage = '🔥 AI çeviri job\'u başladı...';
                $progressData['found'] = true;
            }
            
            // Translation başarılı - "Page 1 translated to ar successfully" pattern'i
            if (strpos($line, 'translated to') !== false && strpos($line, 'successfully') !== false) {
                $successCount++;
                $latestProgress = max($latestProgress, 60 + ($successCount * 15));
                $latestMessage = "✅ Çeviri başarılı ($successCount tamamlandı)";
                $progressData['found'] = true;
                
                // Tek sayfa çevirisi tamamlandıysa completion işaretle
                if ($successCount >= 1) {
                    $isCompleted = true;
                    $latestProgress = 100;
                    $latestMessage = '🎉 Çeviri işlemi tamamlandı!';
                }
            }
            
            // Translation başarısız
            if (strpos($line, 'Translation failed for') !== false) {
                $failedCount++;
                $latestMessage = "⚠️ Bazı çeviriler başarısız ($failedCount hata)";
                $progressData['found'] = true;
            }
            
            // İşlem tamamlandı
            if (strpos($line, 'Translation job completed') !== false) {
                $isCompleted = true;
                $latestProgress = 100;
                $latestMessage = '🎉 Çeviri işlemi tamamlandı!';
                
                // Success/failed sayıları parse et
                if (preg_match('/Success: (\d+), Failed: (\d+)/', $line, $matches)) {
                    $successCount = (int) $matches[1];
                    $failedCount = (int) $matches[2];
                }
                
                $progressData['found'] = true;
                break;
            }
            
            // TranslationCompletedEvent
            if (strpos($line, 'TranslationCompletedEvent') !== false) {
                $isCompleted = true;
                $latestProgress = 100;
                $latestMessage = '🎉 Çeviri event\'i gönderildi!';
                $progressData['found'] = true;
                break;
            }
        }
        
        fclose($handle);
        
        // Sonuç güncelle
        if ($progressData['found']) {
            $progressData['percentage'] = $latestProgress;
            $progressData['message'] = $latestMessage ?: '⚡ AI sistemi çalışıyor...';
            $progressData['completed'] = $isCompleted;
            $progressData['success'] = $successCount;
            $progressData['failed'] = $failedCount;
            $progressData['logPosition'] = $currentPosition;
        }
        
        return $progressData;
    }
    
    /**
     * Laravel log temizleme endpoint (admin only)
     */
    public function clearLog(Request $request)
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            
            if (File::exists($logPath)) {
                // Log dosyasını boşalt
                file_put_contents($logPath, '');
                
                Log::info('🧹 Laravel log cleared by admin', [
                    'user_id' => auth()->id(),
                    'timestamp' => now()
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Log dosyası temizlendi'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Log dosyası bulunamadı'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Log temizleme başarısız: ' . $e->getMessage()
            ]);
        }
    }
}