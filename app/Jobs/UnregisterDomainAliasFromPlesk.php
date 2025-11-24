<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class UnregisterDomainAliasFromPlesk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 30;

    public function __construct(
        public string $domainName,
        public $tenantId = null
    ) {}

    public function handle(): void
    {
        // Ana domain silme
        if ($this->domainName === 'tuufi.com') {
            return;
        }

        Log::channel('system')->info("📋 Plesk domain alias siliniyor: {$this->domainName}", [
            'tenant_id' => $this->tenantId,
        ]);

        try {
            $deleteResult = Process::timeout(30)->run(
                "sudo /usr/sbin/plesk bin domalias --delete {$this->domainName}"
            );

            if ($deleteResult->successful()) {
                Log::channel('system')->info("✅ Plesk domain alias silindi: {$this->domainName}", [
                    'tenant_id' => $this->tenantId,
                ]);
            } else {
                // Zaten yoksa hata verme
                if (str_contains($deleteResult->output(), 'not found') ||
                    str_contains($deleteResult->errorOutput(), 'not found')) {
                    Log::channel('system')->warning("⚠️ Domain alias zaten yok: {$this->domainName}", [
                        'tenant_id' => $this->tenantId,
                    ]);
                } else {
                    Log::channel('system')->error("❌ Plesk domain alias silme hatası: {$this->domainName}", [
                        'tenant_id' => $this->tenantId,
                        'error' => substr($deleteResult->errorOutput(), 0, 200),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::channel('system')->error("❌ Plesk domain alias silme exception: {$this->domainName}", [
                'tenant_id' => $this->tenantId,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
