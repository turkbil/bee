<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Blog\App\Jobs\GenerateBlogFromDraftJob;
use Modules\Blog\App\Jobs\GenerateDraftsJob;
use Modules\Blog\App\Models\BlogAIDraft;
use Illuminate\Support\Facades\Log;

/**
 * Her saat başı otomatik blog üretimi
 *
 * Cron job ile çalıştırılır: php artisan generate:hourly-blog
 * Laravel Scheduler ile her saat başı otomatik çalışır
 */
class GenerateHourlyBlog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:hourly-blog
                            {--force : Force generation even if overlapping}
                            {--test : Test mode - show what would happen without executing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate 1 blog post from draft pool every hour automatically';

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
        $this->info("🚀 Blog Cron Started at {$startTime->format('Y-m-d H:i:s')}");

        try {
            // 📊 Step 1: Count available drafts
            $availableDrafts = BlogAIDraft::where('is_generated', false)
                ->count();

            $this->info("📋 Available drafts: {$availableDrafts}");

            Log::channel('daily')->info('🤖 BLOG CRON: Started', [
                'available_drafts' => $availableDrafts,
                'timestamp' => $startTime,
            ]);

            // 🔍 Step 2: Check if we need to generate drafts first
            if ($availableDrafts === 0) {
                $this->warn('⚠️  No drafts available! Generating drafts first...');

                if (!$this->option('test')) {
                    // Generate drafts first
                    GenerateDraftsJob::dispatch(self::DRAFT_REGENERATION_COUNT)
                        ->onQueue('blog-ai');

                    $this->info('✅ Draft generation job dispatched (100 drafts)');
                    $this->warn('⏸️  Waiting for drafts to be generated. Blog generation will happen in next cron run.');

                    Log::channel('daily')->warning('🤖 BLOG CRON: No drafts - generating first', [
                        'draft_count' => self::DRAFT_REGENERATION_COUNT,
                    ]);

                    return self::SUCCESS;
                }

                $this->comment('[TEST MODE] Would dispatch GenerateDraftsJob');
                return self::SUCCESS;
            }

            // 🎯 Step 3: Select random draft
            $selectedDraft = BlogAIDraft::where('is_generated', false)
                ->inRandomOrder()
                ->lockForUpdate() // Database lock to prevent race conditions
                ->first();

            if (!$selectedDraft) {
                $this->error('❌ Could not select a draft (race condition or all drafts used)');
                Log::channel('daily')->error('🤖 BLOG CRON: Failed to select draft');
                return self::FAILURE;
            }

            $this->info("🎲 Selected draft: [{$selectedDraft->draft_id}] {$selectedDraft->topic_keyword}");

            // 📝 Step 4: Dispatch blog generation job
            if (!$this->option('test')) {
                GenerateBlogFromDraftJob::dispatch($selectedDraft->draft_id)
                    ->onQueue('blog-ai');

                $this->info('✅ Blog generation job dispatched');

                Log::channel('daily')->info('🤖 BLOG CRON: Blog job dispatched', [
                    'draft_id' => $selectedDraft->draft_id,
                    'topic' => $selectedDraft->topic_keyword,
                    'remaining_drafts' => $availableDrafts - 1,
                ]);
            } else {
                $this->comment('[TEST MODE] Would dispatch GenerateBlogFromDraftJob for draft #' . $selectedDraft->draft_id);
            }

            // 🔄 Step 5: Check if we need to regenerate drafts
            $remainingAfterUse = $availableDrafts - 1;

            if ($remainingAfterUse <= self::MINIMUM_DRAFT_THRESHOLD) {
                $this->warn("⚠️  Draft pool is low ({$remainingAfterUse} remaining). Triggering regeneration...");

                if (!$this->option('test')) {
                    GenerateDraftsJob::dispatch(self::DRAFT_REGENERATION_COUNT)
                        ->onQueue('blog-ai');

                    $this->info('✅ Draft regeneration job dispatched (100 new drafts)');

                    Log::channel('daily')->warning('🤖 BLOG CRON: Low draft pool - regenerating', [
                        'remaining_drafts' => $remainingAfterUse,
                        'generating_count' => self::DRAFT_REGENERATION_COUNT,
                    ]);
                } else {
                    $this->comment('[TEST MODE] Would dispatch GenerateDraftsJob (pool low)');
                }
            } else {
                $this->info("✅ Draft pool healthy ({$remainingAfterUse} remaining). No regeneration needed.");
            }

            // 📊 Step 6: Summary
            $duration = $startTime->diffInSeconds(now());
            $this->newLine();
            $this->info("✅ Blog Cron Completed in {$duration} seconds");
            $this->info("📊 Summary:");
            $this->line("   - Draft used: #{$selectedDraft->draft_id} - {$selectedDraft->topic_keyword}");
            $this->line("   - Remaining drafts: {$remainingAfterUse}");
            $this->line("   - Queue jobs: " . ($remainingAfterUse <= self::MINIMUM_DRAFT_THRESHOLD ? '2 (blog + drafts)' : '1 (blog only)'));

            Log::channel('daily')->info('🤖 BLOG CRON: Completed successfully', [
                'duration_seconds' => $duration,
                'draft_used' => $selectedDraft->draft_id,
                'remaining_drafts' => $remainingAfterUse,
            ]);

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());

            Log::channel('daily')->error('🤖 BLOG CRON: Exception occurred', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
