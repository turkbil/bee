<?php

declare(strict_types=1);

namespace Modules\Portfolio\App\Console;

use Illuminate\Console\Command;
use Modules\Portfolio\App\Models\Page;
use Modules\Portfolio\App\Services\PageService;
use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Warm Portfolio Cache Command
 *
 * Pre-loads frequently accessed pages into cache for better performance.
 * Supports multi-tenant architecture with isolated cache warming.
 *
 * @package Modules\Portfolio\App\Console
 */
class WarmPortfolioCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page:warm-cache
        {--tenant=* : Specific tenant IDs to warm cache for}
        {--pages=10 : Number of pages to warm per tenant}
        {--force : Force cache refresh even if already cached}
        {--urls : Also warm frontend URL caches}
        {--quiet : Suppress output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm page caches for better performance';

    /**
     * Progress bar instance
     */
    private $progressBar;

    /**
     * Statistics tracking
     */
    private array $stats = [
        'pages_cached' => 0,
        'urls_warmed' => 0,
        'errors' => 0,
        'skipped' => 0,
        'time_taken' => 0,
    ];

    /**
     * Execute the console command.
     */
    public function handle(PageService $portfolioervice, TenantCacheService $cacheService): int
    {
        $startTime = microtime(true);

        if (!$this->option('quiet')) {
            $this->info('🔥 Starting Portfolio Cache Warming...');
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
                $this->processTenant($tenantId, $portfolioervice, $cacheService);
            }

            // Display summary
            $this->stats['time_taken'] = round(microtime(true) - $startTime, 2);
            $this->displaySummary();

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Cache warming failed: ' . $e->getMessage());
            Log::error('Portfolio cache warming failed', [
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
            return \App\Models\Tenant::pluck('id')->toArray();
        }

        // Default to current tenant or central
        return [1];
    }

    /**
     * Process cache warming for a specific tenant
     */
    private function processTenant(int $tenantId, PageService $portfolioervice, TenantCacheService $cacheService): void
    {
        if (!$this->option('quiet')) {
            $this->info("📦 Processing Tenant #{$tenantId}");
        }

        // Initialize tenant context
        if (class_exists('\App\Helpers\TenantHelpers')) {
            \App\Helpers\TenantHelpers::setTenantContext($tenantId);
        }

        // Get pages to warm
        $limit = (int) $this->option('portfolios');
        $portfolios = $this->getPagesToWarm($limit);

        if ($portfolios->isEmpty()) {
            if (!$this->option('quiet')) {
                $this->warn("  No pages found for tenant #{$tenantId}");
            }
            return;
        }

        // Create progress bar
        if (!$this->option('quiet')) {
            $this->progressBar = $this->output->createProgressBar($portfolios->count());
            $this->progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        }

        // Warm cache for each page
        foreach ($portfolios as $portfolio {
            $this->warmPageCache($portfolio $portfolioervice, $cacheService);

            if (!$this->option('quiet')) {
                $this->progressBar->advance();
            }
        }

        if (!$this->option('quiet')) {
            $this->progressBar->finish();
            $this->newLine(2);
        }
    }

    /**
     * Get pages that should be warmed
     */
    private function getPagesToWarm(int $limit): \Illuminate\Database\Eloquent\Collection
    {
        $query = Page::query()
            ->where('is_active', true)
            ->with(config('page.performance.eager_loading', []));

        // Prioritize important pages
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
     * Warm cache for a specific page
     */
    private function warmPageCache(Portfolio $portfolio PageService $portfolioervice, TenantCacheService $cacheService): void
    {
        try {
            $force = $this->option('force');

            // Set progress message
            if (!$this->option('quiet') && $this->progressBar) {
                $title = is_array($portfolio>title) ? ($portfolio>title['tr'] ?? 'Untitled') : 'Untitled';
                $this->progressBar->setMessage("Caching: {$title}");
            }

            // Cache key for this page
            $cacheKey = "page_detail_{$portfolio>portfolio_id}";

            // Check if already cached
            if (!$force && $cacheService->has(TenantCacheService::PREFIX_PAGES, $cacheKey)) {
                $this->stats['skipped']++;
                return;
            }

            // Cache page data
            $ttl = config('page.cache.ttl.detail', 7200);
            $cacheService->remember(
                TenantCacheService::PREFIX_PAGES,
                $cacheKey,
                $ttl,
                fn() => $portfolio>load('seoSetting')->toArray()
            );

            $this->stats['pages_cached']++;

            // Cache different language versions
            if (is_array($portfolio>slug)) {
                foreach ($portfolio>slug as $locale => $slug) {
                    $localeCacheKey = "page_slug_{$locale}_{$slug}";
                    $cacheService->remember(
                        TenantCacheService::PREFIX_PAGES,
                        $localeCacheKey,
                        $ttl,
                        fn() => $portfolio>portfolio_id
                    );
                }
            }

            // Warm frontend URLs if requested
            if ($this->option('urls')) {
                $this->warmPageUrls($portfolio;
            }

            // Cache SEO data separately
            if ($portfolio>hasSeoSettings()) {
                Cache::put(
                    "universal_seo_page_{$portfolio>portfolio_id}",
                    $portfolio>getSeoData(),
                    $ttl
                );
            }

        } catch (\Exception $e) {
            $this->stats['errors']++;

            Log::warning('Failed to warm cache for page', [
                'portfolio_id' => $portfolio>portfolio_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Warm frontend URLs by making HTTP requests
     */
    private function warmPageUrls(Portfolio $portfolio: void
    {
        if (!is_array($portfolio>slug)) {
            return;
        }

        foreach ($portfolio>slug as $locale => $slug) {
            try {
                $url = route('page.show', ['slug' => $slug, 'locale' => $locale]);

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
        if ($this->option('quiet')) {
            return;
        }

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 Cache Warming Summary');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $this->table(
            ['Metric', 'Value'],
            [
                ['Pages Cached', $this->stats['pages_cached']],
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
        $total = $this->stats['pages_cached'] + $this->stats['skipped'];

        if ($total === 0) {
            return 0;
        }

        return round(($this->stats['skipped'] / $total) * 100, 2);
    }

    /**
     * Schedule the command to run periodically
     *
     * Add this to your App\Console\Kernel schedule() method:
     * $schedule->command('page:warm-cache')->hourly();
     */
    public static function schedule(\Illuminate\Console\Scheduling\Schedule $schedule): void
    {
        // Warm cache every hour during business hours
        $schedule->command('page:warm-cache --pages=20')
            ->hourly()
            ->between('08:00', '20:00')
            ->withoutOverlapping()
            ->runInBackground();

        // Full cache warm daily at night
        $schedule->command('page:warm-cache --pages=50 --urls')
            ->dailyAt('03:00')
            ->withoutOverlapping()
            ->runInBackground();
    }
}