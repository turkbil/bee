<?php

declare(strict_types=1);

namespace Modules\Service\App\Console;

use Illuminate\Console\Command;
use Modules\Service\App\Models\Service;
use Modules\Service\App\Services\ServiceService;
use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Warm Service Cache Command
 *
 * Pre-loads frequently accessed services into cache for better performance.
 * Supports multi-tenant architecture with isolated cache warming.
 *
 * @package Modules\Service\App\Console
 *
 * @method void info(string $message)
 * @method void warn(string $message)
 * @method void error(string $message)
 * @method void newLine(int $count = 1)
 * @method array option(string $key = null)
 * @method mixed table(array $headers, array $rows)
 */
class WarmServiceCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:warm-cache
        {--tenant=* : Specific tenant IDs to warm cache for}
        {--services=10 : Number of services to warm per tenant}
        {--force : Force cache refresh even if already cached}
        {--urls : Also warm frontend URL caches}
        {--silent : Suppress output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm service caches for better performance';

    /**
     * Progress bar instance
     */
    private $progressBar;

    /**
     * Statistics tracking
     */
    private array $stats = [
        'services_cached' => 0,
        'urls_warmed' => 0,
        'errors' => 0,
        'skipped' => 0,
        'time_taken' => 0,
    ];

    /**
     * Execute the console command.
     */
    public function handle(ServiceService $serviceService, TenantCacheService $cacheService): int
    {
        $startTime = microtime(true);

        if (!$this->option('silent')) {
            $this->info('🔥 Starting Service Cache Warming...');
            $this->newLine();
        }

        try {
            // Determine which tenants to process
            $tenantIds = $this->getTenantIds();

            if (empty($tenantIds)) {
                $this->warn('No tenants found to process.');
                return Command::FAILURE;
            }

            // Process each tenant
            foreach ($tenantIds as $tenantId) {
                $this->processTenant($tenantId, $serviceService, $cacheService);
            }

            // Display summary
            $this->stats['time_taken'] = round(microtime(true) - $startTime, 2);
            $this->displaySummary();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Cache warming failed: ' . $e->getMessage());
            Log::error('Service cache warming failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Get tenant IDs to process
     */
    private function getTenantIds(): array
    {
        $tenantOption = $this->option('tenant');

        if (!empty($tenantOption)) {
            return array_map('intval', $tenantOption);
        }

        // Get all tenant IDs from database
        if (class_exists('\App\Models\Tenant')) {
            return DB::table('tenants')->pluck('id')->toArray();
        }

        // Default to current tenant or central
        return [1];
    }

    /**
     * Process cache warming for a specific tenant
     */
    private function processTenant(int $tenantId, ServiceService $serviceService, TenantCacheService $cacheService): void
    {
        if (!$this->option('silent')) {
            $this->info("📦 Processing Tenant #{$tenantId}");
        }

        // Initialize tenant context
        if (class_exists('\App\Helpers\TenantHelpers')) {
            \App\Helpers\TenantHelpers::setTenantContext($tenantId);
        }

        // Get services to warm
        $limit = (int) $this->option('services');
        $services = $this->getPagesToWarm($limit);

        if ($services->isEmpty()) {
            if (!$this->option('silent')) {
                $this->warn("  No services found for tenant #{$tenantId}");
            }
            return;
        }

        // Create progress bar
        if (!$this->option('silent')) {
            $this->progressBar = $this->output->createProgressBar($services->count());
            $this->progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        }

        // Warm cache for each service
        foreach ($services as $service) {
            $this->warmPageCache($service, $serviceService, $cacheService);

            if (!$this->option('silent')) {
                $this->progressBar->advance();
            }
        }

        if (!$this->option('silent')) {
            $this->progressBar->finish();
            $this->newLine(2);
        }
    }

    /**
     * Get services that should be warmed
     */
    private function getPagesToWarm(int $limit): \Illuminate\Database\Eloquent\Collection
    {
        $query = Service::query()
            ->where('is_active', true)
            ->with(config('service.performance.eager_loading', []));

        // Prioritize important services
        $query->orderByRaw('
            CASE
                WHEN slug LIKE \'%about%\' THEN 1
                WHEN slug LIKE \'%contact%\' THEN 2
                ELSE 3
            END
        ')
            ->orderBy('updated_at', 'desc')
            ->limit($limit);

        return $query->get();
    }

    /**
     * Warm cache for a specific service
     */
    private function warmPageCache(Service $service, ServiceService $serviceService, TenantCacheService $cacheService): void
    {
        try {
            $force = $this->option('force');

            // Set progress message
            if (!$this->option('silent') && $this->progressBar) {
                $title = is_array($service->title) ? ($service->title['tr'] ?? 'Untitled') : 'Untitled';
                $this->progressBar->setMessage("Caching: {$title}");
            }

            // Cache key for this service
            $cacheKey = "service_detail_{$service->service_id}";

            // Check if already cached
            if (!$force && $cacheService->has(TenantCacheService::PREFIX_SERVICE, $cacheKey)) {
                $this->stats['skipped']++;
                return;
            }

            // Cache service data
            $ttl = config('service.cache.ttl.detail', 7200);
            $cacheService->remember(
                TenantCacheService::PREFIX_SERVICE,
                $cacheKey,
                $ttl,
                fn() => $service->load('seoSetting')->toArray()
            );

            $this->stats['services_cached']++;

            // Cache different language versions
            if (is_array($service->slug)) {
                foreach ($service->slug as $locale => $slug) {
                    $localeCacheKey = "service_slug_{$locale}_{$slug}";
                    $cacheService->remember(
                        TenantCacheService::PREFIX_SERVICE,
                        $localeCacheKey,
                        $ttl,
                        fn() => $service->service_id
                    );
                }
            }

            // Warm frontend URLs if requested
            if ($this->option('urls')) {
                $this->warmPageUrls($service);
            }

            // Cache SEO data separately (if SEO trait is available)
            if (method_exists($service, 'hasSeoSettings') && $service->hasSeoSettings()) {
                Cache::put(
                    "universal_seo_service_{$service->service_id}",
                    method_exists($service, 'getSeoData') ? $service->getSeoData() : [],
                    $ttl
                );
            }
        } catch (\Exception $e) {
            $this->stats['errors']++;

            Log::warning('Failed to warm cache for service', [
                'service_id' => $service->service_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Warm frontend URLs by making HTTP requests
     */
    private function warmPageUrls(Service $service): void
    {
        if (!is_array($service->slug)) {
            return;
        }

        foreach ($service->slug as $locale => $slug) {
            try {
                $url = route('service.show', ['slug' => $slug, 'locale' => $locale]);

                // Make async HTTP request to warm the URL
                Http::async()
                    ->timeout(5)
                    ->withHeaders(['X-Cache-Warm' => 'true'])
                    ->get($url);

                $this->stats['urls_warmed']++;
            } catch (\Exception $e) {
                // Silent fail for URL warming
                Log::debug('URL warming failed', [
                    'url' => $url ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Display summary statistics
     */
    private function displaySummary(): void
    {
        if ($this->option('silent')) {
            return;
        }

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 Cache Warming Summary');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $this->table(
            ['Metric', 'Value'],
            [
                ['Pages Cached', $this->stats['services_cached']],
                ['URLs Warmed', $this->stats['urls_warmed']],
                ['Skipped (Already Cached)', $this->stats['skipped']],
                ['Errors', $this->stats['errors']],
                ['Time Taken', $this->stats['time_taken'] . ' seconds'],
                ['Cache Hit Rate', $this->calculateCacheHitRate() . '%'],
            ]
        );

        if ($this->stats['errors'] > 0) {
            $this->warn("⚠️  {$this->stats['errors']} errors occurred during cache warming.");
            $this->info('Check logs for details: storage/logs/laravel.log');
        } else {
            $this->info('✅ Cache warming completed successfully!');
        }
    }

    /**
     * Calculate cache hit rate percentage
     */
    private function calculateCacheHitRate(): float
    {
        $total = $this->stats['services_cached'] + $this->stats['skipped'];

        if ($total === 0) {
            return 0;
        }

        return round(($this->stats['skipped'] / $total) * 100, 2);
    }

    /**
     * SCHEDULING GUIDE
     * ================
     * Add this to your App\Console\Kernel schedule() method:
     *
     * // Warm cache every hour during business hours
     * $schedule->command('service:warm-cache --services=20')
     *     ->hourly()
     *     ->between('08:00', '20:00')
     *     ->withoutOverlapping()
     *     ->runInBackground();
     *
     * // Full cache warm daily at night
     * $schedule->command('service:warm-cache --services=50 --urls')
     *     ->dailyAt('03:00')
     *     ->withoutOverlapping()
     *     ->runInBackground();
     */
}
