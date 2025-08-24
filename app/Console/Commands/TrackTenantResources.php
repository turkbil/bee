<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\TenantManagement\App\Services\RealTimeResourceTracker;

class TrackTenantResources extends Command
{
    protected $signature = 'tenant:track-resources {--tenant-id= : Specific tenant ID to track}';
    protected $description = 'Track real resource usage for tenants';

    private $tracker;

    public function __construct(RealTimeResourceTracker $tracker)
    {
        parent::__construct();
        $this->tracker = $tracker;
    }

    public function handle()
    {
        $this->info('Starting real tenant resource tracking...');
        
        $tenantId = $this->option('tenant-id');
        
        if ($tenantId) {
            // Specific tenant tracking
            $result = $this->tracker->trackRealResourceUsage($tenantId);
            if ($result) {
                $this->info("Resource tracking completed for tenant {$tenantId}");
            } else {
                $this->error("Failed to track resources for tenant {$tenantId}");
            }
        } else {
            // All tenants tracking
            $this->tracker->trackAllTenants();
            $this->info('Resource tracking completed for all active tenants');
        }
        
        return Command::SUCCESS;
    }
}