<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class UnregisterDatabaseFromPlesk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 30;

    public function __construct(
        public $tenant
    ) {}

    public function handle(): void
    {
        $databaseName = $this->tenant->tenancy_db_name ?? null;

        if (!$databaseName) {
            Log::warning("Tenant has no database name, skipping Plesk unregister");
            return;
        }

        // Sadece production/server ortamında çalıştır
        if (!file_exists('/usr/local/psa/bin/database')) {
            Log::info("Plesk binary not found, skipping database unregister for: {$databaseName}");
            return;
        }

        try {
            $result = Process::timeout(30)->run("plesk bin database --remove {$databaseName}");

            if ($result->successful()) {
                Log::info("Database unregistered from Plesk: {$databaseName}");
            } else {
                Log::warning("Failed to unregister database from Plesk: {$databaseName}", [
                    'output' => $result->output(),
                    'error' => $result->errorOutput(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Exception while unregistering database from Plesk: {$databaseName}", [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
