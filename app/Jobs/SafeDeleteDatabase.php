<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Events\DatabaseDeleted;
use Stancl\Tenancy\Events\DeletingDatabase;

/**
 * Safe Database Deletion Job
 *
 * Bu job, tenant database'ini silmeden önce varlığını kontrol eder.
 * Eğer database yoksa hata vermeden devam eder.
 */
class SafeDeleteDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected TenantWithDatabase $tenant;

    public function __construct(TenantWithDatabase $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle(): void
    {
        event(new DeletingDatabase($this->tenant));

        $databaseName = $this->tenant->database()->getName();

        try {
            // Database'in var olup olmadığını kontrol et
            $databaseExists = $this->checkDatabaseExists($databaseName);

            if ($databaseExists) {
                // Database varsa sil
                $this->tenant->database()->manager()->deleteDatabase($this->tenant);
                Log::channel('system')->info("✅ Database silindi: {$databaseName}", [
                    'tenant_id' => $this->tenant->getTenantKey(),
                ]);
            } else {
                // Database yoksa sadece log'a yaz
                Log::channel('system')->warning("⚠️ Database zaten yok: {$databaseName}", [
                    'tenant_id' => $this->tenant->getTenantKey(),
                ]);
            }

            event(new DatabaseDeleted($this->tenant));
        } catch (\Exception $e) {
            // Herhangi bir hata olursa log'a yaz ama işlemi durdurma
            Log::channel('system')->error("❌ Database silme hatası: {$databaseName}", [
                'tenant_id' => $this->tenant->getTenantKey(),
                'error' => $e->getMessage(),
            ]);

            // Event'i yine de tetikle ki diğer işlemler devam edebilsin
            event(new DatabaseDeleted($this->tenant));
        }
    }

    /**
     * Database'in var olup olmadığını kontrol et
     */
    protected function checkDatabaseExists(string $databaseName): bool
    {
        try {
            $result = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
            return !empty($result);
        } catch (\Exception $e) {
            Log::channel('system')->error("Database varlık kontrolü başarısız: {$databaseName}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
