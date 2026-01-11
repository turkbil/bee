<?php

namespace Modules\AI\App\Console;

use Illuminate\Console\Command;
use Modules\AI\App\Services\Cache\TenantAwareCacheService;

/**
 * Cache Warmup Command
 *
 * Popüler sorguları pre-cache'le
 * Cron: Her 4 dakikada çalıştır
 */
class CacheWarmupCommand extends Command
{
    protected $signature = 'ai:cache:warmup {tenant_id?}';
    protected $description = 'Warm up cache for popular queries';

    public function handle(TenantAwareCacheService $cacheService)
    {
        $tenantId = $this->argument('tenant_id') ?? tenant('id');

        $this->info("🔥 Warming cache for tenant: {$tenantId}");

        // Popüler sorgular (tenant-specific olabilir)
        $queries = [
            'product_search' => [
                ['query' => 'transpalet', 'category' => 'forklift'],
                ['query' => 'forklift', 'category' => 'forklift'],
                ['query' => 'istif makinesi', 'category' => 'stacker']
            ]
        ];

        $cacheService->warmup($queries);

        $this->info('✅ Cache warmed successfully!');
    }
}
