<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RedisResilienceService;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class TestRedisResilience extends Command
{
    protected $signature = 'redis:test-resilience';
    protected $description = 'Test Redis resilience system';

    public function handle()
    {
        $this->info('🔧 Testing Redis Resilience System...');
        
        // 1. Basic Health Check
        $this->info('1️⃣ Running health check...');
        $health = RedisResilienceService::healthCheck();
        $this->table(['Connection', 'Status'], 
            array_map(fn($conn, $status) => [$conn, $status], 
                array_keys($health['connections']), 
                array_values($health['connections']))
        );
        
        // 2. Test Connection Resilience
        $this->info('2️⃣ Testing connection with resilience...');
        try {
            $result = RedisResilienceService::executeWithReconnect(function () {
                return Redis::ping();
            });
            $this->info('✅ Redis connection test: ' . $result);
        } catch (\Exception $e) {
            $this->error('❌ Redis connection failed: ' . $e->getMessage());
        }
        
        // 3. Test Queue Operations
        $this->info('3️⃣ Testing queue operations...');
        try {
            $testJob = new \Modules\Page\App\Jobs\TranslatePageJob(
                [1], 'tr', ['en'], 'test-session', true
            );
            
            dispatch($testJob->onQueue('tenant_isolated'));
            $this->info('✅ Test job dispatched successfully');
        } catch (\Exception $e) {
            $this->error('❌ Queue test failed: ' . $e->getMessage());
        }
        
        // 4. Test Force Reconnect
        $this->info('4️⃣ Testing force reconnect...');
        try {
            RedisResilienceService::forceReconnect();
            $this->info('✅ Force reconnect successful');
        } catch (\Exception $e) {
            $this->error('❌ Force reconnect failed: ' . $e->getMessage());
        }
        
        // 5. Final Health Check
        $this->info('5️⃣ Final health check...');
        $finalHealth = RedisResilienceService::healthCheck();
        $this->info('Redis Status: ' . $finalHealth['redis_status']);
        
        $this->info('🎉 Redis resilience test completed!');
        
        return 0;
    }
}