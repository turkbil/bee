<?php

namespace App\Listeners;

use Stancl\Tenancy\Events\DatabaseMigrated;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class RegisterTenantDatabaseToPlesk
{
    public function handle(DatabaseMigrated $event): void
    {
        $tenant = $event->tenant;
        $databaseName = $tenant->tenancy_db_name ?? null;
        $domain = env('APP_DOMAIN', 'tuufi.com');

        if (!$databaseName) {
            Log::warning("Tenant has no database name, skipping Plesk register");
            return;
        }

        // Sadece production/server ortamında çalıştır
        if (!file_exists('/usr/local/psa/bin/database')) {
            Log::info("Plesk binary not found, skipping database register for: {$databaseName}");
            return;
        }

        try {
            $result = Process::timeout(30)->run("plesk bin database --register {$databaseName} -domain {$domain} -type mysql");

            if ($result->successful()) {
                Log::info("✅ Database registered to Plesk: {$databaseName} → {$domain}");
            } else {
                Log::warning("⚠️ Failed to register database to Plesk: {$databaseName}", [
                    'output' => $result->output(),
                    'error' => $result->errorOutput(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("❌ Exception while registering database to Plesk: {$databaseName}", [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
