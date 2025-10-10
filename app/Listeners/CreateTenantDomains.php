<?php

namespace App\Listeners;

use Stancl\Tenancy\Events\DomainCreated;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Support\Facades\Log;

class CreateTenantDomains
{
    /**
     * Domain oluşturulduğunda otomatik olarak www.domain ekle
     *
     * Örnek: ixtif.com.tr oluşturulduğunda → www.ixtif.com.tr otomatik eklenir
     */
    public function handle(DomainCreated $event): void
    {
        $createdDomain = $event->domain;
        $tenant = $createdDomain->tenant;

        // Eğer central tenant ise domain ekleme
        if ($tenant && $tenant->central) {
            return;
        }

        // Oluşturulan domain'i al
        $domainName = $createdDomain->domain;

        // Eğer zaten www. ile başlıyorsa, atla
        if (str_starts_with($domainName, 'www.')) {
            return;
        }

        // www. domain'i oluştur
        $wwwDomain = 'www.' . $domainName;

        // www domain zaten var mı kontrol et
        $wwwExists = Domain::where('domain', $wwwDomain)
            ->where('tenant_id', $tenant->id)
            ->exists();

        if ($wwwExists) {
            Log::info("WWW domain zaten mevcut: {$wwwDomain} → Tenant {$tenant->id}");
            return;
        }

        try {
            // www. ile domain ekle
            $domain = Domain::create([
                'domain' => $wwwDomain,
                'tenant_id' => $tenant->id,
            ]);

            Log::info("WWW domain eklendi: {$wwwDomain} → Tenant {$tenant->id}");

        } catch (\Exception $e) {
            Log::error("WWW domain ekleme hatası (Tenant {$tenant->id}): " . $e->getMessage());
        }
    }
}
