<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Tenancy;

class CacheController extends Controller
{
    public function clearCache(Request $request)
    {
        try {
            $clearedItems = [];
            
            // Tenant olup olmadığını kontrol et
            if (app(Tenancy::class)->initialized) {
                $tenant = tenant();
                
                // Tenant cache'ini temizle
                Cache::flush();
                $clearedItems[] = 'Laravel Cache';
                
                // Tenant Redis cache'ini temizle
                $redisPrefix = "tenant:{$tenant->id}:*";
                $redis = Redis::connection();
                $keys = $redis->keys($redisPrefix);
                if (!empty($keys)) {
                    $redis->del($keys);
                    $clearedItems[] = 'Redis Cache (' . count($keys) . ' key)';
                }
                
                // View cache temizleme
                Artisan::call('view:clear');
                $clearedItems[] = 'View Cache';

                // Compiled views silme
                $viewPath = storage_path('framework/views');
                if (is_dir($viewPath)) {
                    array_map('unlink', glob("$viewPath/*.php"));
                    $clearedItems[] = 'Compiled Views';
                }
                
                // Route cache temizleme
                Artisan::call('route:clear');
                $clearedItems[] = 'Route Cache';
                
                // Config cache temizleme
                Artisan::call('config:clear');
                $clearedItems[] = 'Config Cache';

                // OPCache temizleme
                if (function_exists('opcache_reset')) {
                    opcache_reset();
                    $clearedItems[] = 'OPCache';
                }

                // ResponseCache temizleme
                if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
                    \Spatie\ResponseCache\Facades\ResponseCache::clear();
                    $clearedItems[] = 'Response Cache';
                }
                
                // Cache temizleme log'u
                activity()
                    ->causedBy(auth()->user())
                    ->inLog('System')
                    ->withProperties(['baslik' => 'Tenant Cache', 'modul' => 'System', 'tenant' => $tenant->id])
                    ->tap(function ($activity) {
                        $activity->event = 'cache temizlendi';
                    })
                    ->log("Tenant cache temizlendi");
                
                return response()->json([
                    'success' => true,
                    'message' => 'Tenant cache başarıyla temizlendi',
                    'cleared' => $clearedItems,
                    'tenant' => $tenant->id
                ]);
                
            } else {
                // Central domain - sadece kendi cache'ini temizle
                Cache::flush();
                $clearedItems[] = 'Laravel Cache';
                
                // Central Redis cache temizleme
                $redis = Redis::connection();
                $keys = $redis->keys('central:*');
                if (!empty($keys)) {
                    $redis->del($keys);
                    $clearedItems[] = 'Central Redis Cache (' . count($keys) . ' key)';
                }
                
                // System cache'leri
                Artisan::call('view:clear');
                $clearedItems[] = 'View Cache';

                // Compiled views silme
                $viewPath = storage_path('framework/views');
                if (is_dir($viewPath)) {
                    array_map('unlink', glob("$viewPath/*.php"));
                    $clearedItems[] = 'Compiled Views';
                }

                Artisan::call('route:clear');
                $clearedItems[] = 'Route Cache';
                
                Artisan::call('config:clear');
                $clearedItems[] = 'Config Cache';

                // OPCache temizleme
                if (function_exists('opcache_reset')) {
                    opcache_reset();
                    $clearedItems[] = 'OPCache';
                }

                // ResponseCache temizleme
                if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
                    \Spatie\ResponseCache\Facades\ResponseCache::clear();
                    $clearedItems[] = 'Response Cache';
                }
                
                // Central cache temizleme log'u
                activity()
                    ->causedBy(auth()->user())
                    ->inLog('System')
                    ->withProperties(['baslik' => 'Central Cache', 'modul' => 'System'])
                    ->tap(function ($activity) {
                        $activity->event = 'cache temizlendi';
                    })
                    ->log("Central cache temizlendi");
                
                return response()->json([
                    'success' => true,
                    'message' => 'Central domain cache temizlendi',
                    'cleared' => $clearedItems
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Cache clear error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Cache temizleme sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function clearAllCache(Request $request)
    {
        try {
            // Bu endpoint sadece central domain'de çalışır
            if (app(Tenancy::class)->initialized) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu işlem sadece central domain\'de kullanılabilir'
                ], 403);
            }
            
            $clearedItems = [];
            $tenantsCleared = 0;
            
            // Tüm Redis cache'ini temizle
            $redis = Redis::connection();
            $allKeys = $redis->keys('*');
            if (!empty($allKeys)) {
                $redis->flushall();
                $clearedItems[] = 'Tüm Redis Cache (' . count($allKeys) . ' key)';
            }
            
            // Laravel cache temizle
            Cache::flush();
            $clearedItems[] = 'Laravel Cache';
            
            // System cache'leri temizle
            Artisan::call('view:clear');
            $clearedItems[] = 'View Cache';
            
            Artisan::call('route:clear');
            $clearedItems[] = 'Route Cache';
            
            Artisan::call('config:clear');
            $clearedItems[] = 'Config Cache';
            
            Artisan::call('cache:clear');
            $clearedItems[] = 'Application Cache';
            
            // TÜM ResponseCache temizle (tenant + central)
            if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
                \Spatie\ResponseCache\Facades\ResponseCache::clear();
                $clearedItems[] = 'Response Cache (Tüm)';
            }
            
            // Opcache temizle (eğer varsa)
            if (function_exists('opcache_reset')) {
                opcache_reset();
                $clearedItems[] = 'OPCache';
            }
            
            // Tüm cache temizleme log'u
            activity()
                ->causedBy(auth()->user())
                ->inLog('System')
                ->withProperties(['baslik' => 'Tüm Sistem Cache', 'modul' => 'System'])
                ->tap(function ($activity) {
                    $activity->event = 'tüm cache temizlendi';
                })
                ->log("Tüm sistem cache temizlendi");
            
            return response()->json([
                'success' => true,
                'message' => 'Tüm sistem cache\'i temizlendi',
                'cleared' => $clearedItems
            ]);
            
        } catch (\Exception $e) {
            Log::error('Cache clear all error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Tüm cache temizleme sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}