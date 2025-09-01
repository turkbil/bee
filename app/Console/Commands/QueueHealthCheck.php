<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\QueueHealthService;

class QueueHealthCheck extends Command
{
    protected $signature = 'queue:health-check';
    protected $description = 'Automatic queue health check and restoration system';

    public function handle()
    {
        $this->info('🚀 Starting automatic queue health check...');
        
        $results = QueueHealthService::checkAndFixQueueHealth();
        
        $this->line('📊 QUEUE HEALTH REPORT:');
        $this->line('Status: ' . $results['queue_workers_status']);
        $this->line('Health Score: ' . $results['health_score'] . '/100');
        $this->line('Failed Jobs Cleared: ' . $results['failed_jobs_cleared']);
        
        if (!empty($results['actions_taken'])) {
            $this->line('🔧 Actions Taken:');
            foreach ($results['actions_taken'] as $action) {
                $this->line('  • ' . $action);
            }
        }
        
        if ($results['health_score'] >= 80) {
            $this->info('✅ Queue system is healthy!');
        } elseif ($results['health_score'] >= 50) {
            $this->warn('⚠️ Queue system has minor issues (fixed automatically)');
        } else {
            $this->error('❌ Queue system has critical issues - manual intervention may be needed');
        }
        
        return 0;
    }
}