<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class RedisTestController extends Controller
{
    public function test()
    {
        $results = [];

        // 1. Redis Connection Test
        try {
            $redis = Redis::connection();
            $results['redis_connection'] = 'SUCCESS';
            $results['redis_ping'] = $redis->ping();
        } catch (\Exception $e) {
            $results['redis_connection'] = 'FAILED: ' . $e->getMessage();
        }

        // 2. Cache Test
        try {
            Cache::put('test_key', 'test_value', 60);
            $value = Cache::get('test_key');
            $results['cache_test'] = $value === 'test_value' ? 'SUCCESS' : 'FAILED';
            Cache::forget('test_key');
        } catch (\Exception $e) {
            $results['cache_test'] = 'FAILED: ' . $e->getMessage();
        }

        // 3. Redis Keys Count
        try {
            $redis = Redis::connection();
            $allKeys = $redis->keys('*');
            $results['total_redis_keys'] = count($allKeys);
            $results['sample_keys'] = array_slice($allKeys, 0, 10);
        } catch (\Exception $e) {
            $results['redis_keys_error'] = $e->getMessage();
        }

        // 4. Response Cache Test
        try {
            $testKey = 'responsecache:test_' . time();
            $redis->set($testKey, 'test_response', 'EX', 60);
            $testValue = $redis->get($testKey);
            $results['response_cache_test'] = $testValue === 'test_response' ? 'SUCCESS' : 'FAILED';
            $redis->del($testKey);
        } catch (\Exception $e) {
            $results['response_cache_test'] = 'FAILED: ' . $e->getMessage();
        }

        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
    }
}