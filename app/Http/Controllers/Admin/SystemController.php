<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemController extends Controller
{
    /**
     * Queue worker durumunu kontrol et - OPTIMIZE EDİLMİŞ VERSİYON
     * JavaScript otomatik cleanup sistemi için
     */
    public function queueStatus(Request $request)
    {
        try {
            // CACHE İLE OPTİMİZE - 30 saniye cache
            $cacheKey = 'system_queue_status';
            $cachedResult = Cache::get($cacheKey);
            
            if ($cachedResult) {
                return response()->json($cachedResult);
            }
            // 1. Queue connection kontrolü
            $queueConnection = config('queue.default');
            $queueActive = false;
            $workerRunning = false;
            
            // 2. Basit process kontrolü - DB query yok!
            $processCheck = shell_exec('ps aux | grep "queue:work" | grep -v grep');
            if (!empty($processCheck)) {
                $workerRunning = true;
                $queueActive = true;
            }
            
            // 3. Horizon kontrolü de ekle
            $horizonCheck = shell_exec('ps aux | grep "horizon" | grep -v grep');
            if (!empty($horizonCheck)) {
                $workerRunning = true;
                $queueActive = true;
            }
            
            // 4. Response hazırla
            $response = [
                'success' => true,
                'queue_connection' => $queueConnection,
                'queue_active' => $queueActive,
                'worker_running' => $workerRunning,
                'status' => $workerRunning ? 'active' : 'inactive',
                'message' => $workerRunning 
                    ? 'Queue worker aktif ve çalışıyor' 
                    : 'Queue worker bulunamadı veya aktif değil',
                'timestamp' => now()->toISOString(),
                'recommendations' => []
            ];
            
            // 5. Öneriler ekle
            if (!$workerRunning) {
                $response['recommendations'] = [
                    'php artisan queue:work komutunu çalıştırın',
                    'Supervisor ile queue worker\'ı otomatik başlatın',
                    'Laravel Horizon kullanmayı değerlendirin'
                ];
            }
            
            // Cache'e kaydet
            Cache::put($cacheKey, $response, 30); // 30 saniye cache
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('Queue status check error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'queue_active' => false,
                'worker_running' => false,
                'status' => 'error',
                'message' => 'Queue durumu kontrol edilemedi',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }
    
    /**
     * System health check - genel sistem durumu
     */
    public function healthCheck(Request $request)
    {
        try {
            $health = [
                'success' => true,
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'checks' => []
            ];
            
            // Database bağlantısı
            try {
                \DB::connection()->getPdo();
                $health['checks']['database'] = ['status' => 'ok', 'message' => 'Database bağlantısı aktif'];
            } catch (\Exception $e) {
                $health['checks']['database'] = ['status' => 'error', 'message' => 'Database bağlantısı başarısız'];
                $health['status'] = 'degraded';
            }
            
            // Cache sistemi
            try {
                Cache::put('health_check_test', 'ok', 10);
                $cacheResult = Cache::get('health_check_test');
                Cache::forget('health_check_test');
                
                $health['checks']['cache'] = $cacheResult === 'ok' 
                    ? ['status' => 'ok', 'message' => 'Cache sistemi çalışıyor']
                    : ['status' => 'error', 'message' => 'Cache sistemi yanıt vermiyor'];
                    
            } catch (\Exception $e) {
                $health['checks']['cache'] = ['status' => 'error', 'message' => 'Cache sistemi hatası: ' . $e->getMessage()];
                $health['status'] = 'degraded';
            }
            
            // Queue durumu da ekle
            $queueStatus = $this->queueStatus($request);
            $queueData = $queueStatus->getData(true);
            $health['checks']['queue'] = [
                'status' => $queueData['worker_running'] ? 'ok' : 'warning',
                'message' => $queueData['message']
            ];
            
            return response()->json($health);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Health check başarısız',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }
}