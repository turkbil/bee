<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogCacheService;

class CleanupExpiredSessionsCommand extends Command
{
    protected $signature = 'auth:cleanup-sessions';
    protected $description = 'Clean up expired sessions and old activity logs';

    public function handle(): int
    {
        $this->info('Cleaning up expired sessions...');

        // Get session lifetime from settings (in minutes)
        $lifetime = (int) setting('auth_session_lifetime', 525600);
        $expiryTime = now()->subMinutes($lifetime)->timestamp;

        // Delete expired sessions
        $deletedSessions = DB::table('sessions')
            ->where('last_activity', '<', $expiryTime)
            ->delete();

        $this->line("Deleted {$deletedSessions} expired sessions");

        // Archive old activity logs (older than 1 year)
        $oneYearAgo = now()->subYear();

        $deletedLogs = DB::table('activity_log')
            ->where('created_at', '<', $oneYearAgo)
            ->delete();

        $this->line("Deleted {$deletedLogs} old activity logs");

        // Clear activity log cache after cleanup
        if ($deletedLogs > 0) {
            ActivityLogCacheService::clearCache();
            $this->line("Activity log cache cleared");
        }

        $this->info('Cleanup completed');

        return Command::SUCCESS;
    }
}
