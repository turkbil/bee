<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ReissueLetsEncryptCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 120;
    public $backoff = 30;

    protected string $parentDomain = 'tuufi.com';
    protected string $sslEmail = 'ssl@tuufi.com';

    public function __construct()
    {
        // Queue'da 10 saniye bekle (DNS propagation için)
        $this->delay = now()->addSeconds(10);
    }

    public function handle(): void
    {
        Log::channel('system')->info("🔐 SSL sertifikası yenileniyor: {$this->parentDomain}");

        try {
            // Tüm domain alias'ları al
            $aliases = $this->getAllDomainAliases();

            if (empty($aliases)) {
                Log::channel('system')->warning("⚠️ SSL yenileme: Domain alias bulunamadı");
                return;
            }

            // Domain listesini oluştur
            $domainParams = "-d {$this->parentDomain} -d www.{$this->parentDomain}";
            foreach ($aliases as $alias) {
                $domainParams .= " -d {$alias}";
                // www subdomain'i de ekle (Plesk otomatik oluşturuyor)
                if (!str_starts_with($alias, 'www.')) {
                    $domainParams .= " -d www.{$alias}";
                }
            }

            // Let's Encrypt sertifikası yenile
            $command = "sudo /usr/sbin/plesk bin extension --exec letsencrypt cli.php {$domainParams} -m {$this->sslEmail}";

            Log::channel('system')->debug("🔐 SSL komutu: {$command}");

            $result = Process::timeout(120)->run($command);

            if ($result->successful()) {
                Log::channel('system')->info("✅ SSL sertifikası yenilendi", [
                    'domains' => $aliases,
                ]);
            } else {
                // Hata durumunda detaylı log
                $error = $result->errorOutput() ?: $result->output();

                // Rate limit veya başka ACME hatası olabilir
                if (str_contains($error, 'rateLimited') || str_contains($error, 'too many')) {
                    Log::channel('system')->warning("⚠️ SSL yenileme: Rate limit - daha sonra tekrar denenecek", [
                        'error' => substr($error, 0, 500),
                    ]);
                } else {
                    Log::channel('system')->error("❌ SSL sertifikası yenileme hatası", [
                        'error' => substr($error, 0, 500),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::channel('system')->error("❌ SSL sertifikası yenileme exception", [
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Laravel domains tablosundan tüm domain'leri al
     */
    protected function getAllDomainAliases(): array
    {
        // Laravel tenant domains tablosundan al
        $domains = DB::table('domains')
            ->whereNotIn('domain', [$this->parentDomain, "www.{$this->parentDomain}"])
            ->pluck('domain')
            ->toArray();

        // www olmayan domain'leri filtrele (sadece ana domain'ler)
        $aliases = [];
        foreach ($domains as $domain) {
            if (!str_starts_with($domain, 'www.')) {
                $aliases[] = $domain;
            }
        }

        return array_unique($aliases);
    }
}
