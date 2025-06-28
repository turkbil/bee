<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;

class CacheDebugController extends Controller
{
    public function index(Request $request)
    {
        $data = [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'auth_status' => [
                'is_authenticated' => Auth::check(),
                'user_id' => Auth::id(),
                'user_name' => Auth::user()?->name,
                'user_email' => Auth::user()?->email,
            ],
            'locale_info' => [
                'app_locale' => app()->getLocale(),
                'session_site_locale' => session('site_locale'),
                'all_sessions' => session()->all(),
            ],
            'cache_info' => $this->getCacheInfo($request),
            'request_info' => [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        ];

        return view('debug.cache-test', compact('data'));
    }

    private function getCacheInfo(Request $request)
    {
        try {
            $redis = Redis::connection();
            
            // AuthAwareHasher'dan hash oluştur
            $hasher = app(\App\Services\AuthAwareHasher::class);
            $currentHash = $hasher->getHashFor($request);
            
            // Cache key'lerini ara
            $allKeys = $redis->keys('*');
            $guestKeys = $redis->keys('*_guest_*');
            $authKeys = $redis->keys('*_auth_*');
            $responseKeys = $redis->keys('responsecache*');
            
            return [
                'current_hash' => $currentHash,
                'total_keys' => count($allKeys),
                'guest_keys_count' => count($guestKeys),
                'auth_keys_count' => count($authKeys),
                'response_cache_keys' => count($responseKeys),
                'sample_guest_keys' => array_slice($guestKeys, 0, 5),
                'sample_auth_keys' => array_slice($authKeys, 0, 5),
                'sample_response_keys' => array_slice($responseKeys, 0, 5),
                'current_page_cached' => $this->isCurrentPageCached($currentHash, $redis),
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'redis_available' => false
            ];
        }
    }

    private function isCurrentPageCached($hash, $redis)
    {
        try {
            $cacheKey = "responsecache:{$hash}";
            return $redis->exists($cacheKey) ? 'YES' : 'NO';
        } catch (\Exception $e) {
            return 'ERROR: ' . $e->getMessage();
        }
    }

    public function clearCache(Request $request)
    {
        $type = $request->get('type', 'all');
        $redis = Redis::connection();
        $cleared = 0;

        try {
            switch ($type) {
                case 'guest':
                    $keys = $redis->keys('*_guest_*');
                    break;
                case 'auth':
                    $keys = $redis->keys('*_auth_*');
                    break;
                case 'response':
                    $keys = $redis->keys('responsecache*');
                    break;
                case 'all':
                default:
                    $keys = $redis->keys('*');
                    break;
            }

            foreach ($keys as $key) {
                $redis->del($key);
                $cleared++;
            }

            return redirect()->route('cache.debug')->with('success', "✅ {$cleared} adet {$type} cache temizlendi!");

        } catch (\Exception $e) {
            return redirect()->route('cache.debug')->with('error', "❌ Cache temizleme hatası: " . $e->getMessage());
        }
    }
}