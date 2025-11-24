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
            Log::channel('system')->warning("⚠️ Tenant has no database name, skipping Plesk unregister");
            return;
        }

        try {
            // Plesk DB komutu ile sil (3 deneme)
            $deleteSql = "DELETE FROM data_bases WHERE name = '{$databaseName}'";
            $deleteResult = null;
            $maxAttempts = 3;

            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                $deleteResult = Process::timeout(10)->run("sudo /usr/sbin/plesk db \"{$deleteSql}\"");

                if ($deleteResult->successful()) {
                    if ($attempt > 1) {
                        Log::channel('system')->info("✅ Plesk DB silme başarılı (deneme {$attempt}/{$maxAttempts})");
                    }
                    Log::channel('system')->info("✅ Plesk database kaydı silindi: {$databaseName}", [
                        'tenant_id' => $this->tenant->id,
                    ]);
                    return;
                }

                if ($attempt < $maxAttempts) {
                    Log::channel('system')->warning("⚠️ Plesk DB silme başarısız, tekrar deneniyor... ({$attempt}/{$maxAttempts})");
                    sleep(2); // 2 saniye bekle
                }
            }

            // Tüm denemeler başarısız
            Log::channel('system')->warning("⚠️ Plesk DB silme hatası: {$databaseName} ({$maxAttempts} deneme sonrası)", [
                'tenant_id' => $this->tenant->id,
                'error' => substr($deleteResult->errorOutput(), 0, 200),
            ]);
        } catch (\Exception $e) {
            Log::channel('system')->warning("⚠️ Plesk DB silme başarısız: {$databaseName}", [
                'tenant_id' => $this->tenant->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
