<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ResetMonthlyAITokenUsage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// AI Token scheduled tasks
Schedule::command('ai:reset-monthly-usage')
    ->monthlyOn(1, '02:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/ai-token-reset.log'));

// TenantManagement scheduled tasks
Schedule::command('tenant:track-resources')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/tenant-resource-tracking.log'));

// Auto-scaling check - every 2 minutes for high frequency monitoring
Schedule::call(function () {
    $autoScalingService = app(\Modules\TenantManagement\App\Services\AutoScalingService::class);
    $actions = $autoScalingService->checkAndScale();
    
    if (!empty($actions)) {
        foreach ($actions as $tenantId => $tenantActions) {
            foreach ($tenantActions as $action) {
                $autoScalingService->executeScalingAction($action);
            }
        }
    }
})->everyTwoMinutes()
    ->name('tenant-auto-scaling')
    ->withoutOverlapping();

// Real-time metrics collection - every minute
Schedule::call(function () {
    $realTimeAutoScalingService = app(\Modules\TenantManagement\App\Services\RealTimeAutoScalingService::class);
    $realTimeAutoScalingService->collectAndStoreMetrics();
})->everyMinute()
    ->name('tenant-metrics-collection')
    ->withoutOverlapping();
