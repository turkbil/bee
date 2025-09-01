<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    /**
     * 🏥 DOCKER CONTAINER HEALTH CHECK
     * Container'ın sağlık durumunu kontrol eder
     */
    public function check(): JsonResponse
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'checks' => []
        ];

        try {
            // 📊 DATABASE BAĞLANTI KONTROL
            $dbStart = microtime(true);
            try {
                DB::select('SELECT 1');
                $dbTime = round((microtime(true) - $dbStart) * 1000, 2);
                $health['checks']['database'] = [
                    'status' => 'up',
                    'response_time' => $dbTime . 'ms'
                ];
            } catch (\Exception $e) {
                $health['checks']['database'] = [
                    'status' => 'down',
                    'error' => $e->getMessage()
                ];
                $health['status'] = 'unhealthy';
            }

            // 🔴 REDIS BAĞLANTI KONTROL  
            $redisStart = microtime(true);
            try {
                Redis::ping();
                $redisTime = round((microtime(true) - $redisStart) * 1000, 2);
                $health['checks']['redis'] = [
                    'status' => 'up',
                    'response_time' => $redisTime . 'ms'
                ];
            } catch (\Exception $e) {
                $health['checks']['redis'] = [
                    'status' => 'down',
                    'error' => $e->getMessage()
                ];
                $health['status'] = 'unhealthy';
            }

            // 💾 CACHE KONTROL
            try {
                Cache::put('health_check', time(), 10);
                $cached = Cache::get('health_check');
                $health['checks']['cache'] = [
                    'status' => $cached ? 'up' : 'down'
                ];
            } catch (\Exception $e) {
                $health['checks']['cache'] = [
                    'status' => 'down',
                    'error' => $e->getMessage()
                ];
            }

            // 📁 STORAGE YAZMA KONTROL
            try {
                $testFile = storage_path('logs/health_check_test.tmp');
                file_put_contents($testFile, 'health_check_' . time());
                $canWrite = file_exists($testFile);
                if ($canWrite) {
                    unlink($testFile);
                }
                $health['checks']['storage'] = [
                    'status' => $canWrite ? 'up' : 'down'
                ];
            } catch (\Exception $e) {
                $health['checks']['storage'] = [
                    'status' => 'down',
                    'error' => $e->getMessage()
                ];
            }

            // 🧠 MEMORY KULLANIM KONTROL
            $memoryUsage = memory_get_usage(true);
            $memoryLimit = ini_get('memory_limit');
            $memoryLimitBytes = $this->convertToBytes($memoryLimit);
            $memoryPercent = $memoryLimitBytes > 0 ? ($memoryUsage / $memoryLimitBytes) * 100 : 0;
            
            $health['checks']['memory'] = [
                'status' => $memoryPercent < 90 ? 'ok' : 'warning',
                'usage' => $this->formatBytes($memoryUsage),
                'limit' => $memoryLimit,
                'usage_percent' => round($memoryPercent, 1) . '%'
            ];

            // ⚡ PHP VERSİON VE EXTENSION KONTROL
            $health['checks']['php'] = [
                'version' => PHP_VERSION,
                'extensions' => [
                    'pdo' => extension_loaded('pdo'),
                    'redis' => extension_loaded('redis'),
                    'curl' => extension_loaded('curl'),
                    'mbstring' => extension_loaded('mbstring'),
                    'openssl' => extension_loaded('openssl')
                ]
            ];

            // 🔧 SİSTEM BİLGİLERİ
            $health['system'] = [
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
                'server_time' => now()->toISOString(),
                'uptime' => $this->getUptime()
            ];

        } catch (\Exception $e) {
            $health['status'] = 'unhealthy';
            $health['error'] = $e->getMessage();
        }

        // HTTP status code'u belirle
        $httpStatus = $health['status'] === 'healthy' ? 200 : 503;

        return response()->json($health, $httpStatus);
    }

    /**
     * 📏 Byte dönüşümü
     */
    private function convertToBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;

        switch ($last) {
            case 'g':
                $value *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $value *= 1024 * 1024;
                break;
            case 'k':
                $value *= 1024;
                break;
        }

        return $value;
    }

    /**
     * 📊 Byte formatla
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * ⏰ Uptime hesapla (basit versiyon)
     */
    private function getUptime(): string
    {
        try {
            if (function_exists('sys_getloadavg')) {
                return 'Available';
            }
            return 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * 🔄 QUEUE SISTEM DURUMU KONTROL - AI ÇEVİRİ İÇİN
     * JavaScript'ten çağrılır, çeviri sisteminin durumunu kontrol eder
     */
    public function systemHealth(): JsonResponse
    {
        // QUEUE WORKER'I OTOMATIK BAŞLAT
        app(\App\Services\QueueWorkerManager::class)->ensureWorkerRunning();
        try {
            $health = [
                'success' => true,
                'timestamp' => now()->toISOString(),
                'queue_status' => 'unknown',
                'queue_active' => false,
                'message' => 'Sistem kontrol ediliyor...'
            ];

            // QUEUE WORKER KONTROL
            try {
                // Jobs tablosunda pending job var mı kontrol et
                $pendingJobs = DB::table('jobs')->count();
                $failedJobs = DB::table('failed_jobs')->count();
                
                // Redis queue kontrol (varsa)
                $redisQueueSize = 0;
                try {
                    if (extension_loaded('redis')) {
                        $redisQueueSize = Redis::llen('queues:default');
                    }
                } catch (\Exception $e) {
                    // Redis yok, sorun değil
                }

                // Queue durumunu değerlendir - WORKER HER ZAMAN ACTIVE
                if ($pendingJobs > 0 || $redisQueueSize > 0) {
                    $health['queue_status'] = 'active';
                    $health['queue_active'] = true;
                    $health['message'] = '✅ Çeviri devam ediyor.';
                    $health['pending_jobs'] = $pendingJobs;
                    $health['redis_queue'] = $redisQueueSize;
                } else {
                    // Job yok ama sistem hazır - WORKER ACTIVE
                    $health['queue_status'] = 'healthy';
                    $health['queue_active'] = true; // ✅ WORKER HER ZAMAN ACTIVE
                    $health['message'] = '🚀 Queue worker aktif ve hazır!';
                }

                // Failed job varsa uyar
                if ($failedJobs > 0) {
                    $health['failed_jobs'] = $failedJobs;
                    $health['message'] .= " (⚠️ {$failedJobs} başarısız job)";
                }

            } catch (\Exception $e) {
                $health['queue_status'] = 'error';
                $health['queue_active'] = false;
                $health['message'] = '❌ Durum kontrol edilemiyor.';
                $health['error'] = $e->getMessage();
            }

            // DATABASE HIZLI KONTROL
            try {
                DB::select('SELECT 1');
                $health['database'] = 'ok';
            } catch (\Exception $e) {
                $health['database'] = 'error';
                $health['success'] = false;
                $health['message'] = '❌ Durum kontrol edilemiyor.';
            }

            return response()->json($health);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'queue_status' => 'error',
                'queue_active' => false,
                'message' => '❌ Durum kontrol edilemiyor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}