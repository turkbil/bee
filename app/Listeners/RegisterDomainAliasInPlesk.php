<?php

namespace App\Listeners;

use Stancl\Tenancy\Events\DomainCreated;
use Illuminate\Support\Facades\Log;
use App\Jobs\RegisterDomainInWebServer;
use App\Jobs\RenewSSLCertificate;

/**
 * Domain oluşturulduğunda:
 * 1. Nginx ve Apache config'e ekle
 * 2. SSL sertifikasını yenile
 *
 * NOT: Plesk lisansı gerektirmez - doğrudan config dosyalarını düzenler
 */
class RegisterDomainAliasInPlesk
{
    /**
     * Central domain - dinamik olarak alınır
     */
    protected string $centralDomain;

    public function __construct()
    {
        // Central domain'i handle() içinde alacağız, constructor'da DB sorgusu yapmıyoruz
        $this->centralDomain = '';
    }

    /**
     * Central domain'i al (central tenant'ın ilk domain'i)
     */
    protected function getCentralDomain(): string
    {
        if (empty($this->centralDomain)) {
            // Central tenant'ı bul
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

    public function handle(DomainCreated $event): void
    {
        $domain = $event->domain;
        $domainName = $domain->domain;
        $tenantId = $domain->tenant_id;

        // Central domain ise skip
        if ($domainName === $this->getCentralDomain()) {
            return;
        }

        // www. ile başlayan domain'leri atla - ana domain eklenince otomatik eklenir
        if (str_starts_with($domainName, 'www.')) {
            Log::channel('system')->info("⏭️ www subdomain atlandı: {$domainName}", [
                'tenant_id' => $tenantId,
            ]);
            return;
        }

        Log::channel('system')->info("📋 Domain kaydediliyor: {$domainName}", [
            'tenant_id' => $tenantId,
        ]);

        // 1. Nginx ve Apache'ye ekle (senkron)
        try {
            RegisterDomainInWebServer::dispatchSync($domainName, $tenantId);
        } catch (\Exception $e) {
            Log::channel('system')->error("❌ Web server kaydı hatası: {$domainName}", [
                'error' => $e->getMessage(),
            ]);
        }

        // 2. SSL sertifikasını yenile (queue'da çalışsın - 15sn delay ile)
        try {
            RenewSSLCertificate::dispatch();
            Log::channel('system')->info("🔐 SSL yenileme queue'ya eklendi: {$domainName}");
        } catch (\Exception $e) {
            Log::channel('system')->warning("⚠️ SSL yenileme hatası (tenant oluşturma devam ediyor): {$domainName}", [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
