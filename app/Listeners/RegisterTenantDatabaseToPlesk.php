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

        if (!$databaseName) {
            Log::warning("Tenant has no database name, skipping Plesk register");
            return;
        }

        // 1️⃣ Storage klasörlerini oluştur
        $this->createTenantStorage($tenant);

        // Tenant'ın kendi domain'ini al (her tenant bağımsız site)
        $tenantDomain = $tenant->domains()->first()?->domain;

        if (!$tenantDomain) {
            Log::warning("Tenant has no domain, skipping Plesk register: {$databaseName}");
            return;
        }

        // Tenant'ın kendi domain'i kullan
        $domain = $tenantDomain;

        // Sadece production/server ortamında çalıştır
        // open_basedir kısıtlaması nedeniyle which komutu ile kontrol edelim
        $pleskCheck = Process::timeout(5)->run('which plesk');
        if (!$pleskCheck->successful()) {
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

    /**
     * Tenant için storage klasörlerini oluştur (yoksa)
     */
    private function createTenantStorage($tenant): void
    {
        $tenantId = $tenant->id;
        $storagePath = storage_path("tenant{$tenantId}");

        // Ana klasör yoksa oluştur
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0775, true);
            Log::info("✅ Created storage for tenant: {$storagePath}");
        }

        // Alt klasörleri oluştur
        $directories = [
            'framework/cache',
            'framework/sessions',
            'framework/views',
            'app/public',
            'logs',
        ];

        foreach ($directories as $dir) {
            $fullPath = "{$storagePath}/{$dir}";
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0775, true);
            }
        }

        // Public symlink oluştur
        $this->createPublicSymlink($tenantId);
    }

    /**
     * Tenant için public storage symlink oluştur
     */
    private function createPublicSymlink($tenantId): void
    {
        $targetPath = storage_path("tenant{$tenantId}/app/public");
        $linkPath = public_path("storage/tenant{$tenantId}");

        // Symlink zaten varsa güncelle
        if (is_link($linkPath)) {
            unlink($linkPath);
        }

        // Symlink oluştur
        if (is_dir($targetPath)) {
            symlink($targetPath, $linkPath);
            Log::info("✅ Created public symlink for tenant{$tenantId}");
        }
    }
}
