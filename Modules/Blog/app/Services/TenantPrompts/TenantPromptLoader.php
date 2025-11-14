<?php

namespace Modules\Blog\App\Services\TenantPrompts;

use Illuminate\Support\Facades\File;

/**
 * Tenant-Specific Prompt Loader
 *
 * Tenant ID'ye göre özel prompt'ları yükler
 * Tenant-specific dosya yoksa DefaultPrompts kullanır (fallback)
 */
class TenantPromptLoader
{
    protected DefaultPrompts $promptProvider;

    public function __construct()
    {
        $this->loadTenantPrompts();
    }

    /**
     * Tenant ID'ye göre uygun prompt sınıfını yükle
     */
    protected function loadTenantPrompts(): void
    {
        $tenantId = tenant('id');

        if (!$tenantId) {
            // Tenant context yok, default kullan
            $this->promptProvider = new DefaultPrompts();
            return;
        }

        // Tenant-specific prompt dosyası var mı?
        $tenantClass = "Modules\\Blog\\App\\Services\\TenantPrompts\\Tenants\\Tenant{$tenantId}Prompts";
        $tenantFile = __DIR__ . "/Tenants/Tenant{$tenantId}Prompts.php";

        if (File::exists($tenantFile) && class_exists($tenantClass)) {
            // Tenant özel prompt kullan
            $this->promptProvider = new $tenantClass();
        } else {
            // Default fallback
            $this->promptProvider = new DefaultPrompts();
        }
    }

    /**
     * Draft üretimi için prompt al
     */
    public function getDraftPrompt(): string
    {
        return $this->promptProvider->getDraftPrompt();
    }

    /**
     * Blog içeriği yazımı için prompt al
     */
    public function getBlogContentPrompt(): string
    {
        return $this->promptProvider->getBlogContentPrompt();
    }

    /**
     * Tenant context bilgilerini al
     */
    public function getTenantContext(): array
    {
        return $this->promptProvider->getContext();
    }

    /**
     * Hangi prompt provider kullanıldığını öğren (debug için)
     */
    public function getProviderClass(): string
    {
        return get_class($this->promptProvider);
    }
}
