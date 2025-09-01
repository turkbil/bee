<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TranslationStatusController extends Controller
{
    /**
     * 🔄 TRANSLATION PROGRESS POLLING - ALTERNATIF SİSTEM
     * JavaScript health check problemi için alternatif çözüm
     */
    public function checkProgress(Request $request): JsonResponse
    {
        $sessionId = $request->get('session_id');
        
        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'progress' => 0,
                'status' => 'error',
                'message' => 'Session ID required'
            ]);
        }

        try {
            // Cache'den progress kontrol et
            $progress = Cache::get("translation_progress_{$sessionId}", 0);
            $status = Cache::get("translation_status_{$sessionId}", 'processing');
            $result = Cache::get("translation_result_{$sessionId}", null);
            
            // Jobs tablosunda bu session için job var mı
            $activeJobs = DB::table('jobs')
                ->where('payload', 'LIKE', "%{$sessionId}%")
                ->count();
                
            // Completed job kontrol
            $isCompleted = $status === 'completed' || $result !== null;
            
            // Progress logic
            if ($isCompleted) {
                $finalProgress = 100;
                $finalStatus = 'completed';
                $message = '🎉 Çeviri tamamlandı!';
                
                // Cache'i temizle
                Cache::forget("translation_progress_{$sessionId}");
                Cache::forget("translation_status_{$sessionId}");
                Cache::forget("translation_result_{$sessionId}");
                
            } elseif ($activeJobs > 0) {
                $finalProgress = min($progress + 5, 95); // Artış simülasyonu  
                $finalStatus = 'processing';
                $message = '🔥 Elite AI sistemi çalışıyor... (' . $finalProgress . '%)';
                
                // Progress güncelle
                Cache::put("translation_progress_{$sessionId}", $finalProgress, 300);
                
            } else {
                // Job yok, progress takılmış olabilir
                if ($progress >= 75) {
                    $finalProgress = 100;
                    $finalStatus = 'completed';
                    $message = '✅ Çeviri tamamlandı (timeout)';
                } else {
                    $finalProgress = $progress;
                    $finalStatus = 'processing';
                    $message = '⏳ İşlem devam ediyor...';
                }
            }

            return response()->json([
                'success' => true,
                'progress' => $finalProgress,
                'status' => $finalStatus,
                'message' => $message,
                'active_jobs' => $activeJobs,
                'session_id' => $sessionId,
                'result' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Translation progress check failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'progress' => 0,
                'status' => 'error',
                'message' => 'Status kontrol hatası'
            ]);
        }
    }

    /**
     * 🎯 TRANSLATION PROGRESS UPDATE - Job'lardan çağrılır
     */
    public function updateProgress(Request $request): JsonResponse
    {
        $sessionId = $request->get('session_id');
        $progress = $request->get('progress', 0);
        $status = $request->get('status', 'processing');
        $result = $request->get('result', null);
        
        if (!$sessionId) {
            return response()->json(['success' => false]);
        }

        try {
            // Cache'e yaz
            Cache::put("translation_progress_{$sessionId}", $progress, 300);
            Cache::put("translation_status_{$sessionId}", $status, 300);
            
            if ($result) {
                Cache::put("translation_result_{$sessionId}", $result, 300);
            }

            Log::info('Translation progress updated', [
                'session_id' => $sessionId,
                'progress' => $progress,
                'status' => $status
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Translation progress update failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false]);
        }
    }
}