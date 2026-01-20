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

/**
 * Certbot ile SSL sertifikası al/yenile
 * Plesk lisansı gerektirmez
 */
class RenewSSLCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 180;
    public $backoff = 60;

    // Config
    protected string $certName = 'tuufi-all';
    protected string $sslEmail = 'ssl@tuufi.com';
    protected string $webroot = '/var/www/vhosts/default/htdocs';
    protected string $pleskCertPath = '/usr/local/psa/var/certificates/scffm1s7qbch4jnfprJ4Ox';

    // Central domain
    protected string $centralDomain;

    public function __construct()
    {
        // 15 saniye bekle (web server reload için)
        $this->delay = now()->addSeconds(15);

        // Central domain handle() içinde alınacak
        $this->centralDomain = '';
    }

    /**
     * Central domain'i al (central tenant'ın ilk domain'i)
     */
    protected function getCentralDomain(): string
    {
        if (empty($this->centralDomain)) {
            $centralTenant = \App\Models\Tenant::where('central', true)->first();
            if ($centralTenant) {
                $domain = $centralTenant->domains()
                    ->where('domain', 'not like', 'www.%')
                    ->first();
                $this->centralDomain = $domain?->domain ?? 'tuufi.com';
            } else {
                $this->centralDomain = 'tuufi.com';
            }
        }
        return $this->centralDomain;
    }

    public function handle(): void
    {
        Log::channel('system')->info("🔐 SSL sertifikası yenileniyor (certbot)");

        try {
            // Tüm domain'leri al
            $domains = $this->getAllDomains();

            if (empty($domains)) {
                Log::channel('system')->warning("⚠️ SSL: Domain bulunamadı");
                return;
            }

            // Domain parametrelerini oluştur
            $domainParams = '';
            foreach ($domains as $domain) {
                $domainParams .= " -d {$domain}";
            }

            // Certbot komutu
            $command = "sudo certbot certonly --webroot " .
                "-w {$this->webroot} " .
                "{$domainParams} " .
                "--cert-name {$this->certName} " .
                "--non-interactive " .
                "--agree-tos " .
                "--email {$this->sslEmail} " .
                "--expand " .  // Mevcut sertifikayı genişlet
                "2>&1";

            Log::channel('system')->debug("🔐 Certbot komutu çalıştırılıyor", [
                'domains' => $domains,
            ]);

            $result = Process::timeout(180)->run($command);

            if ($result->successful() || str_contains($result->output(), 'Successfully received certificate')) {
                // Sertifikayı Plesk formatına dönüştür ve kopyala
                $this->copyToPlesk();

                // Nginx'i reload et
                Process::run('sudo systemctl reload nginx');

                Log::channel('system')->info("✅ SSL sertifikası yenilendi", [
                    'domains' => count($domains),
                ]);
            } else {
                $output = $result->output() . $result->errorOutput();

                // Rate limit kontrolü
                if (str_contains($output, 'too many') || str_contains($output, 'rate limit')) {
                    Log::channel('system')->warning("⚠️ SSL: Rate limit - sonra tekrar denenecek");
                }
                // Zaten güncel
                elseif (str_contains($output, 'not yet due for renewal')) {
                    Log::channel('system')->info("ℹ️ SSL: Sertifika zaten güncel");
                }
                else {
                    Log::channel('system')->error("❌ SSL sertifikası hatası", [
                        'output' => substr($output, 0, 500),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::channel('system')->error("❌ SSL exception", [
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Tüm domain'leri al (www dahil)
     */
    protected function getAllDomains(): array
    {
        // Central domain ile başla
        $centralDomain = $this->getCentralDomain();
        $domains = [
            $centralDomain,
            "www.{$centralDomain}",
        ];

        // Tenant domain'lerini al
        $tenantDomains = DB::connection('mysql')->table('domains')
            ->where('tenant_id', '!=', 1)
            ->where('domain', 'not like', 'www.%')
            ->pluck('domain')
            ->toArray();

        // Her domain için www versiyonunu da ekle
        foreach ($tenantDomains as $domain) {
            // Redirect yapılan domain'leri atla (ixtif.com.tr gibi)
            if ($this->isRedirectDomain($domain)) {
                continue;
            }

            $domains[] = $domain;
            $domains[] = "www.{$domain}";
        }

        return array_unique($domains);
    }

    /**
     * SSL sertifikasından hariç tutulacak domain mi?
     * - Redirect yapılan domain'ler (301 redirect varsa SSL gerekmez)
     * - Cloudflare arkasındaki domain'ler (kendi SSL'leri var)
     * - Bu sunucuda barındırılmayan domain'ler
     */
    protected function isRedirectDomain(string $domain): bool
    {
        // Hariç tutulacak domain'ler
        $excludedDomains = [
            // Redirect yapılan
            'ixtif.com.tr',

            // Cloudflare arkasında / farklı sunucuda
            'muzibu.com.tr',
        ];

        return in_array($domain, $excludedDomains);
    }

    /**
     * Let's Encrypt sertifikasını Plesk formatına dönüştür ve kopyala
     */
    protected function copyToPlesk(): void
    {
        $certPath = "/etc/letsencrypt/live/{$this->certName}";

        // fullchain.pem + privkey.pem -> combined format (Plesk için)
        // sudo bash -c ile pipe kullanımı, rm -f ile interaktif prompt engellenir
        $command = "sudo bash -c 'cat {$certPath}/fullchain.pem {$certPath}/privkey.pem > {$this->pleskCertPath}' && " .
            "sudo chmod 600 {$this->pleskCertPath}";

        Process::run($command);

        Log::channel('system')->info("✅ SSL sertifikası Plesk'e kopyalandı");
    }
}
