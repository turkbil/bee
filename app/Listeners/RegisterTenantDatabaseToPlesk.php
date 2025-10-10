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
            Log::channel('system')->warning("⚠️ Tenant has no database name, skipping Plesk register");
            return;
        }

        // 1️⃣ Storage klasörlerini oluştur
        $this->createTenantStorage($tenant);

        // Tenant'ın kendi domain'ini al (her tenant bağımsız site)
        $tenantDomain = $tenant->domains()->first()?->domain;

        // Tenant domain yoksa ana domain'i kullan
        $domain = $tenantDomain ?? 'tuufi.com';

        Log::channel('system')->info("📋 Plesk DB kaydı başlatılıyor: {$databaseName} → {$domain}", [
            'tenant_id' => $tenant->id,
        ]);

        try {
            // Plesk DB komutu ile domain ID'sini al (3 deneme)
            $domainResult = null;
            $maxAttempts = 3;

            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                $domainResult = Process::timeout(10)->run("plesk db \"SELECT id FROM domains WHERE name = '{$domain}' LIMIT 1\"");

                if ($domainResult->successful()) {
                    if ($attempt > 1) {
                        Log::channel('system')->info("✅ Plesk domain sorgusu başarılı (deneme {$attempt}/{$maxAttempts})");
                    }
                    break;
                }

                if ($attempt < $maxAttempts) {
                    Log::channel('system')->warning("⚠️ Plesk domain sorgusu başarısız, tekrar deneniyor... ({$attempt}/{$maxAttempts})");
                    sleep(2); // 2 saniye bekle
                }
            }

            if (!$domainResult->successful()) {
                Log::channel('system')->error("❌ Plesk domain bulunamadı: {$domain} ({$maxAttempts} deneme sonrası)", [
                    'tenant_id' => $tenant->id,
                    'exit_code' => $domainResult->exitCode(),
                    'error' => substr($domainResult->errorOutput(), 0, 200),
                    'output' => substr($domainResult->output(), 0, 200),
                ]);
                return;
            }

            // Domain ID'yi parse et (MySQL table format: +----+ \n | id | \n +----+ \n |  1 | \n +----+)
            $output = $domainResult->output();
            $lines = array_filter(explode("\n", $output));
            $domainId = null;

            foreach ($lines as $line) {
                // | 1 | formatındaki satırları kontrol et
                if (preg_match('/\|\s*(\d+)\s*\|/', $line, $matches)) {
                    $domainId = (int) $matches[1];
                    break;
                }
            }

            if (!$domainId) {
                Log::channel('system')->error("❌ Plesk domain ID alınamadı: {$domain}");
                return;
            }

            // Database zaten kayıtlı mı kontrol et
            $checkResult = Process::timeout(10)->run("plesk db \"SELECT COUNT(*) FROM data_bases WHERE name = '{$databaseName}'\"");
            if ($checkResult->successful()) {
                $lines = array_filter(explode("\n", $checkResult->output()));
                foreach ($lines as $line) {
                    // | 1 | formatındaki satırları kontrol et
                    if (preg_match('/\|\s*(\d+)\s*\|/', $line, $matches)) {
                        if ((int)$matches[1] > 0) {
                            Log::channel('system')->warning("⚠️ DB zaten Plesk'te kayıtlı: {$databaseName}", [
                                'tenant_id' => $tenant->id,
                            ]);
                            return;
                        }
                    }
                }
            }

            // DB server ID'sini al
            $serverResult = Process::timeout(10)->run("plesk db \"SELECT id FROM DatabaseServers WHERE type = 'mysql' AND host = 'localhost' LIMIT 1\"");
            $dbServerId = 1; // Default
            if ($serverResult->successful()) {
                $lines = array_filter(explode("\n", $serverResult->output()));
                foreach ($lines as $line) {
                    // | 1 | formatındaki satırları kontrol et
                    if (preg_match('/\|\s*(\d+)\s*\|/', $line, $matches)) {
                        $dbServerId = (int) $matches[1];
                        break;
                    }
                }
            }

            // Plesk veritabanına kaydet
            $insertSql = "INSERT INTO data_bases (name, type, dom_id, db_server_id) VALUES ('{$databaseName}', 'mysql', {$domainId}, {$dbServerId})";
            $insertResult = Process::timeout(10)->run("plesk db \"{$insertSql}\"");

            if ($insertResult->successful()) {
                Log::channel('system')->info("✅ Plesk DB kaydı tamamlandı: {$databaseName} → {$domain}", [
                    'tenant_id' => $tenant->id,
                    'dom_id' => $domainId,
                ]);
            } else {
                Log::channel('system')->error("❌ Plesk DB kayıt hatası: {$databaseName}", [
                    'tenant_id' => $tenant->id,
                    'error' => $insertResult->errorOutput(),
                ]);
            }
        } catch (\Exception $e) {
            Log::channel('system')->warning("⚠️ Plesk DB kaydı başarısız: {$databaseName}", [
                'tenant_id' => $tenant->id,
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

        // CENTRAL context'teyiz, base storage path + tenant{id}
        $baseStoragePath = base_path('storage');
        $storagePath = "{$baseStoragePath}/tenant{$tenantId}";

        // Ana klasör yoksa oluştur
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0775, true);
            Log::channel('system')->info("✅ Tenant storage oluşturuldu", [
                'tenant_id' => $tenantId,
                'path' => $storagePath,
            ]);
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
        // CENTRAL context - manual path
        $baseStoragePath = base_path('storage');
        $targetPath = "{$baseStoragePath}/tenant{$tenantId}/app/public";
        $linkPath = public_path("storage/tenant{$tenantId}");

        // Symlink zaten varsa güncelle
        if (is_link($linkPath)) {
            unlink($linkPath);
        }

        // Symlink oluştur
        if (is_dir($targetPath)) {
            symlink($targetPath, $linkPath);
            Log::channel('system')->info("✅ Public symlink oluşturuldu", [
                'tenant_id' => $tenantId,
                'link' => $linkPath,
                'target' => $targetPath,
            ]);
        }
    }
}
