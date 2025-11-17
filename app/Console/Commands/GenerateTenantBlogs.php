<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Modules\Blog\App\Jobs\GenerateBlogFromDraftJob;
use Modules\Blog\App\Jobs\GenerateDraftsJob;
use Modules\Blog\App\Models\BlogAIDraft;
use Illuminate\Support\Facades\Log;

/**
 * Tenant-Aware Blog AI Cron Command
 *
 * Her saat başı çalışır, tüm tenant'ları tarar:
 * 1. Tenant'ın blog AI sistemi aktif mi? (blog_ai_enabled)
 * 2. Günlük blog sayısı kaç? (blog_ai_daily_count)
 * 3. Bu saatte blog üretilmeli mi? (calculateActiveHours)
 * 4. Draft pool'da yeterli draft var mı?
 * 5. Varsa blog üret, yoksa draft üret
 *
 * Cron: Her saat başı (0. dakika)
 * Laravel Scheduler: hourly()
 */
class GenerateTenantBlogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:tenant-blogs
                            {--tenant-id= : Specific tenant ID to process (optional)}
                            {--test : Test mode - show what would happen without executing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate blog posts for all enabled tenants based on their schedule';

    /**
     * Minimum draft count to maintain in the pool
     */
    const MINIMUM_DRAFT_THRESHOLD = 10;

    /**
     * Number of drafts to generate when pool is low
     */
    const DRAFT_REGENERATION_COUNT = 100;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();
        $currentHour = (int) $startTime->format('H');

        $this->info("🚀 Tenant Blog Cron Started at {$startTime->format('Y-m-d H:i:s')}");
        $this->info("⏰ Current Hour: {$currentHour}");
        $this->newLine();

        Log::channel('daily')->info('🤖 TENANT BLOG CRON: Started', [
            'timestamp' => $startTime,
            'current_hour' => $currentHour,
        ]);

        // Get tenants to process
        $tenants = $this->getTenantsToProcess();

        if ($tenants->isEmpty()) {
            $this->warn('⚠️  No tenants found to process');
            return self::SUCCESS;
        }

        $this->info("📊 Found {$tenants->count()} tenant(s) to check");
        $this->newLine();

        $processed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($tenants as $tenant) {
            try {
                $result = $this->processTenant($tenant, $currentHour);

                if ($result === 'processed') {
                    $processed++;
                } elseif ($result === 'skipped') {
                    $skipped++;
                }

            } catch (\Exception $e) {
                $errors++;
                $this->error("❌ Error processing tenant {$tenant->id}: " . $e->getMessage());

                Log::channel('daily')->error('🤖 TENANT BLOG CRON: Tenant error', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Summary
        $duration = $startTime->diffInSeconds(now());
        $this->newLine();
        $this->info("✅ Tenant Blog Cron Completed in {$duration} seconds");
        $this->info("📊 Summary:");
        $this->line("   - Processed: {$processed}");
        $this->line("   - Skipped: {$skipped}");
        $this->line("   - Errors: {$errors}");

        Log::channel('daily')->info('🤖 TENANT BLOG CRON: Completed', [
            'duration_seconds' => $duration,
            'processed' => $processed,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);

        return self::SUCCESS;
    }

    /**
     * Get tenants to process
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getTenantsToProcess()
    {
        // Eğer specific tenant ID verilmişse sadece onu işle
        if ($tenantId = $this->option('tenant-id')) {
            return Tenant::where('id', $tenantId)->get();
        }

        // Tüm tenant'ları getir
        return Tenant::all();
    }

    /**
     * Process a single tenant
     *
     * @param Tenant $tenant
     * @param int $currentHour
     * @return string 'processed' | 'skipped' | 'error'
     */
    protected function processTenant(Tenant $tenant, int $currentHour): string
    {
        $this->line("🏢 Tenant #{$tenant->id}: Checking...");

        // Tenant context başlat
        tenancy()->initialize($tenant);

        // 1️⃣ Blog AI enabled mi?
        $enabled = getTenantSetting('blog_ai_enabled', '0');
        $enabled = ($enabled === '1' || $enabled === 1 || $enabled === true);

        if (!$enabled) {
            $this->line("   ⏭️  Skipped - Blog AI disabled");
            tenancy()->end();
            return 'skipped';
        }

        // 2️⃣ Günlük blog sayısı
        $dailyCount = (int) getTenantSetting('blog_ai_daily_count', 4);

        // 3️⃣ Active hours hesapla
        $activeHours = calculateActiveHours($dailyCount);

        // 4️⃣ Bu saatte blog üretilmeli mi?
        if (!in_array($currentHour, $activeHours)) {
            $this->line("   ⏭️  Skipped - Not active hour (Active: " . implode(', ', $activeHours) . ")");
            tenancy()->end();
            return 'skipped';
        }

        // 5️⃣ Draft count kontrol
        $availableDrafts = BlogAIDraft::where('is_generated', false)->count();

        $this->info("   ✅ Active! Daily: {$dailyCount}, Drafts: {$availableDrafts}");

        // 6️⃣ Draft yoksa önce draft üret
        if ($availableDrafts === 0) {
            $this->warn("   ⚠️  No drafts available! Generating drafts first...");

            if (!$this->option('test')) {
                GenerateDraftsJob::dispatch(self::DRAFT_REGENERATION_COUNT)
                    ->onQueue('blog-ai');

                $this->info("   ✅ Draft generation job dispatched (100 drafts)");
                $this->warn("   ⏸️  Waiting for drafts. Blog generation will happen in next cron run.");

                Log::channel('daily')->warning('🤖 TENANT BLOG CRON: No drafts - generating first', [
                    'tenant_id' => $tenant->id,
                    'draft_count' => self::DRAFT_REGENERATION_COUNT,
                ]);
            } else {
                $this->comment("   [TEST MODE] Would dispatch GenerateDraftsJob");
            }

            tenancy()->end();
            return 'processed';
        }

        // 7️⃣ Random draft seç ve blog üret
        $selectedDraft = BlogAIDraft::where('is_generated', false)
            ->inRandomOrder()
            ->lockForUpdate()
            ->first();

        if (!$selectedDraft) {
            $this->error("   ❌ Could not select a draft (race condition or all drafts used)");
            tenancy()->end();
            return 'error';
        }

        $this->info("   🎲 Selected draft: [{$selectedDraft->draft_id}] {$selectedDraft->topic_keyword}");

        // 8️⃣ Auto-publish ayarını al
        $autoPublish = getTenantSetting('blog_ai_auto_publish', '1');
        $autoPublish = ($autoPublish === '1' || $autoPublish === 1 || $autoPublish === true);

        // 9️⃣ Blog generation job dispatch
        if (!$this->option('test')) {
            GenerateBlogFromDraftJob::dispatch($selectedDraft->draft_id, $autoPublish)
                ->onQueue('blog-ai');

            $publishStatus = $autoPublish ? 'published' : 'draft';
            $this->info("   ✅ Blog generation job dispatched (will be {$publishStatus})");

            Log::channel('daily')->info('🤖 TENANT BLOG CRON: Blog job dispatched', [
                'tenant_id' => $tenant->id,
                'draft_id' => $selectedDraft->draft_id,
                'topic' => $selectedDraft->topic_keyword,
                'auto_publish' => $autoPublish,
                'remaining_drafts' => $availableDrafts - 1,
            ]);
        } else {
            $this->comment("   [TEST MODE] Would dispatch GenerateBlogFromDraftJob for draft #{$selectedDraft->draft_id}");
        }

        // 🔄 Draft pool düşük mü kontrol et
        $remainingAfterUse = $availableDrafts - 1;

        if ($remainingAfterUse <= self::MINIMUM_DRAFT_THRESHOLD) {
            $this->warn("   ⚠️  Draft pool is low ({$remainingAfterUse} remaining). Triggering regeneration...");

            if (!$this->option('test')) {
                GenerateDraftsJob::dispatch(self::DRAFT_REGENERATION_COUNT)
                    ->onQueue('blog-ai');

                $this->info("   ✅ Draft regeneration job dispatched (100 new drafts)");

                Log::channel('daily')->warning('🤖 TENANT BLOG CRON: Low draft pool - regenerating', [
                    'tenant_id' => $tenant->id,
                    'remaining_drafts' => $remainingAfterUse,
                    'generating_count' => self::DRAFT_REGENERATION_COUNT,
                ]);
            } else {
                $this->comment("   [TEST MODE] Would dispatch GenerateDraftsJob (pool low)");
            }
        } else {
            $this->info("   ✅ Draft pool healthy ({$remainingAfterUse} remaining)");
        }

        tenancy()->end();
        return 'processed';
    }
}
