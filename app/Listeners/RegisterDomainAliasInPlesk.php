<?php

namespace App\Listeners;

use Stancl\Tenancy\Events\DomainCreated;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;
use App\Jobs\ReissueLetsEncryptCertificate;

class RegisterDomainAliasInPlesk
{
    /**
     * Ana domain - tüm alias'lar bu domain'e bağlanır
     */
    protected string $parentDomain = 'tuufi.com';

    public function handle(DomainCreated $event): void
    {
        $domain = $event->domain;
        $domainName = $domain->domain;

        // Ana domain ise skip
        if ($domainName === $this->parentDomain) {
            return;
        }

        // www. ile başlayan domain'leri atla - Plesk ana domain alias'ı oluşturduğunda www otomatik eklenir
        if (str_starts_with($domainName, 'www.')) {
            Log::channel('system')->info("⏭️ www subdomain atlandı (Plesk otomatik ekler): {$domainName}", [
                'tenant_id' => $domain->tenant_id,
            ]);
            return;
        }

        Log::channel('system')->info("📋 Plesk domain alias ekleniyor: {$domainName} → {$this->parentDomain}", [
            'tenant_id' => $domain->tenant_id,
        ]);

        $aliasCreated = false;

        try {
            // Domain alias zaten var mı kontrol et
            $checkResult = Process::timeout(10)->run(
                "sudo /usr/sbin/plesk bin domalias --info {$domainName} 2>&1"
            );

            if ($checkResult->successful() && !str_contains($checkResult->output(), 'not found')) {
                Log::channel('system')->info("ℹ️ Domain alias zaten mevcut, SSL yenilenecek: {$domainName}", [
                    'tenant_id' => $domain->tenant_id,
                ]);
                $aliasCreated = true; // Alias var, SSL yenilemesi yapılacak
            } else {
                // Domain alias oluştur
                // SEO redirect kapalı (her domain kendi içeriğini gösterecek)
                $createResult = Process::timeout(30)->run(
                    "sudo /usr/sbin/plesk bin domalias --create {$domainName} -domain {$this->parentDomain} -web true -mail true -dns true -seo-redirect false"
                );

                if ($createResult->successful()) {
                    Log::channel('system')->info("✅ Plesk domain alias oluşturuldu: {$domainName}", [
                        'tenant_id' => $domain->tenant_id,
                        'parent_domain' => $this->parentDomain,
                    ]);
                    $aliasCreated = true;
                } else {
                    Log::channel('system')->error("❌ Plesk domain alias hatası: {$domainName}", [
                        'tenant_id' => $domain->tenant_id,
                        'error' => substr($createResult->errorOutput(), 0, 300),
                        'output' => substr($createResult->output(), 0, 300),
                    ]);
                }
            }

            // SSL sertifikasını yenile (alias oluşturulduysa veya zaten varsa)
            if ($aliasCreated) {
                // Senkron çalıştır - tenant oluşturma sırasında SSL hemen yenilensin
                Log::channel('system')->info("🔐 SSL yenileme başlatılıyor: {$domainName}");
                try {
                    (new ReissueLetsEncryptCertificate())->handle();
                    Log::channel('system')->info("✅ SSL yenileme tamamlandı: {$domainName}");
                } catch (\Exception $sslException) {
                    // SSL hatası tenant oluşturmayı engellemesin
                    Log::channel('system')->warning("⚠️ SSL yenileme hatası (tenant oluşturma devam ediyor): {$domainName}", [
                        'error' => $sslException->getMessage(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::channel('system')->error("❌ Plesk domain alias exception: {$domainName}", [
                'tenant_id' => $domain->tenant_id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
