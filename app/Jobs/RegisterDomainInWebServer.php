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
 * Domain eklendiğinde nginx ve apache config'e otomatik ekle
 * Plesk lisansı gerektirmez - doğrudan config dosyalarını düzenler
 * open_basedir sorununu aşmak için shell komutları kullanır
 */
class RegisterDomainInWebServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 60;

    protected string $domain;
    protected ?int $tenantId;

    // Config dosya yolları
    protected string $nginxConfig = '/etc/nginx/plesk.conf.d/vhosts/tuufi.com.conf';
    protected string $apacheConfig = '/var/www/vhosts/system/tuufi.com/conf/httpd.conf';

    // Central domain - tüm tenant'lar bu domain üzerinden çalışır
    protected string $centralDomain;

    public function __construct(string $domain, ?int $tenantId = null)
    {
        $this->domain = $domain;
        $this->tenantId = $tenantId;

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

    /**
     * Config dosyasını oku (sudo cat ile - open_basedir bypass)
     */
    protected function readConfig(string $path): ?string
    {
        $result = Process::run("sudo cat {$path}");
        if ($result->successful()) {
            return $result->output();
        }
        Log::channel('system')->error("❌ Config okunamadı: {$path}");
        return null;
    }

    /**
     * Config dosyasına yaz (sudo tee ile - open_basedir bypass)
     */
    protected function writeConfig(string $path, string $content): bool
    {
        // Geçici dosyaya yaz, sonra sudo ile kopyala
        $tempFile = '/tmp/webserver_config_' . md5($path) . '.tmp';
        file_put_contents($tempFile, $content);

        $result = Process::run("sudo cp {$tempFile} {$path} && rm -f {$tempFile}");
        return $result->successful();
    }

    public function handle(): void
    {
        // www. ile başlayan domain'leri atla (ana domain eklenince otomatik eklenir)
        if (str_starts_with($this->domain, 'www.')) {
            Log::channel('system')->info("⏭️ www domain atlandı: {$this->domain}");
            return;
        }

        // Central domain ise atla
        if ($this->domain === $this->getCentralDomain()) {
            return;
        }

        Log::channel('system')->info("🌐 Domain web server'a ekleniyor: {$this->domain}", [
            'tenant_id' => $this->tenantId,
        ]);

        $nginxSuccess = $this->addToNginx();
        $apacheSuccess = $this->addToApache();

        if ($nginxSuccess && $apacheSuccess) {
            $this->reloadServices();
            Log::channel('system')->info("✅ Domain web server'a eklendi: {$this->domain}");
        }
    }

    /**
     * Nginx config'e domain ekle
     * Dinamik olarak son eklenen domain'den sonra ekler
     */
    protected function addToNginx(): bool
    {
        try {
            // Config'i oku (sudo ile)
            $config = $this->readConfig($this->nginxConfig);
            if (!$config) {
                return false;
            }

            // Domain zaten var mı kontrol et
            if (str_contains($config, "server_name {$this->domain};")) {
                Log::channel('system')->info("ℹ️ Domain nginx'te zaten mevcut: {$this->domain}");
                return true;
            }

            // Dinamik olarak son eklenen tenant domain'ini bul
            $lastDomain = $this->getLastAddedDomain();

            if ($lastDomain) {
                // Son domain'den sonra ekle
                $escapedDomain = preg_quote($lastDomain, '/');
                $pattern = "/(server_name www\\.{$escapedDomain};)/";

                $newConfig = preg_replace(
                    $pattern,
                    "$1\n\tserver_name {$this->domain};\n\tserver_name www.{$this->domain};",
                    $config
                );

                if ($newConfig !== $config) {
                    if ($this->writeConfig($this->nginxConfig, $newConfig)) {
                        Log::channel('system')->info("✅ Nginx: {$this->domain} eklendi (www.{$lastDomain} sonrasına)");
                    } else {
                        return $this->addToNginxBeforeSSL($config);
                    }
                } else {
                    // Fallback: ssl_certificate satırından önce ekle
                    return $this->addToNginxBeforeSSL($config);
                }
            } else {
                // Fallback: ssl_certificate satırından önce ekle
                return $this->addToNginxBeforeSSL($config);
            }

            // Config test
            $testResult = Process::run('sudo nginx -t 2>&1');
            if (!$testResult->successful()) {
                Log::channel('system')->error("❌ Nginx config test hatası", [
                    'error' => $testResult->output(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::channel('system')->error("❌ Nginx config exception", [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Son eklenen tenant domain'ini bul (www olmayan)
     */
    protected function getLastAddedDomain(): ?string
    {
        // Mevcut domain hariç, en son eklenen tenant domain'ini bul
        $domain = DB::connection('mysql')->table('domains')
            ->where('tenant_id', '!=', 1) // Central hariç
            ->where('domain', 'not like', 'www.%')
            ->where('domain', '!=', $this->domain)
            ->orderBy('id', 'desc')
            ->value('domain');

        return $domain;
    }

    /**
     * Fallback: ssl_certificate satırından önce ekle
     */
    protected function addToNginxBeforeSSL(string $config): bool
    {
        // ssl_certificate satırından önce ekle (HTTPS server bloğu için)
        $pattern = '/(\n)(\s*ssl_certificate\s)/';
        $replacement = "$1\tserver_name {$this->domain};\n\tserver_name www.{$this->domain};\n$2";

        $newConfig = preg_replace($pattern, $replacement, $config, 2); // İlk 2 eşleşme (HTTP ve HTTPS)

        if ($newConfig !== $config) {
            if ($this->writeConfig($this->nginxConfig, $newConfig)) {
                Log::channel('system')->info("✅ Nginx: {$this->domain} eklendi (ssl_certificate öncesine)");
                return true;
            }
        }

        // Son çare: client_max_body_size öncesine ekle
        $pattern = '/(\n)(\s*client_max_body_size\s)/';
        $newConfig = preg_replace($pattern, $replacement, $config, 2);

        if ($newConfig !== $config) {
            if ($this->writeConfig($this->nginxConfig, $newConfig)) {
                Log::channel('system')->info("✅ Nginx: {$this->domain} eklendi (client_max_body_size öncesine)");
                return true;
            }
        }

        Log::channel('system')->error("❌ Nginx: Domain eklenemedi - uygun konum bulunamadı");
        return false;
    }

    /**
     * Apache config'e domain ekle
     * Dinamik olarak son eklenen domain'den sonra ekler
     */
    protected function addToApache(): bool
    {
        try {
            // Config'i oku (sudo ile)
            $config = $this->readConfig($this->apacheConfig);
            if (!$config) {
                return false;
            }

            // Domain zaten var mı kontrol et
            if (str_contains($config, "ServerAlias \"{$this->domain}\"")) {
                Log::channel('system')->info("ℹ️ Domain apache'de zaten mevcut: {$this->domain}");
                return true;
            }

            // Dinamik olarak son eklenen tenant domain'ini bul
            $lastDomain = $this->getLastAddedDomain();

            if ($lastDomain) {
                // Son domain'den sonra ekle
                $result = Process::run(
                    "sudo sed -i '/ServerAlias \"www.{$lastDomain}\"/a\\\\t\\tServerAlias \"{$this->domain}\"\\n\\t\\tServerAlias \"www.{$this->domain}\"' {$this->apacheConfig}"
                );

                if ($result->successful()) {
                    Log::channel('system')->info("✅ Apache: {$this->domain} eklendi (www.{$lastDomain} sonrasına)");
                } else {
                    // Fallback: UseCanonicalName satırından önce ekle
                    return $this->addToApacheBeforeCanonical();
                }
            } else {
                // Fallback: UseCanonicalName satırından önce ekle
                return $this->addToApacheBeforeCanonical();
            }

            // Config test
            $testResult = Process::run('sudo apachectl configtest 2>&1');
            if (!str_contains($testResult->output(), 'Syntax OK')) {
                Log::channel('system')->error("❌ Apache config test hatası", [
                    'error' => $testResult->output(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::channel('system')->error("❌ Apache config exception", [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Fallback: UseCanonicalName satırından önce ekle
     */
    protected function addToApacheBeforeCanonical(): bool
    {
        $result = Process::run(
            "sudo sed -i '/UseCanonicalName Off/i\\\\t\\tServerAlias \"{$this->domain}\"\\n\\t\\tServerAlias \"www.{$this->domain}\"' {$this->apacheConfig}"
        );

        if ($result->successful()) {
            Log::channel('system')->info("✅ Apache: {$this->domain} eklendi (UseCanonicalName öncesine)");

            // Config test
            $testResult = Process::run('sudo apachectl configtest 2>&1');
            return str_contains($testResult->output(), 'Syntax OK');
        }

        Log::channel('system')->error("❌ Apache: Domain eklenemedi");
        return false;
    }

    /**
     * Nginx ve Apache'yi reload et
     */
    protected function reloadServices(): void
    {
        Process::run('sudo systemctl reload nginx');
        Process::run('sudo systemctl reload httpd');

        Log::channel('system')->info("🔄 Web server'lar reload edildi");
    }
}
