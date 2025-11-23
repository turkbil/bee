<?php

namespace Modules\AI\App\Contracts;

/**
 * Interface for Tenant-specific Prompt Services
 *
 * Her tenant kendi PromptService'ini implement eder.
 * Yeni tenant eklendiğinde sadece TenantXPromptService.php oluşturmak yeterli.
 */
interface TenantPromptServiceInterface
{
    /**
     * Tenant'a özel prompt kurallarını oluşturur
     *
     * @return array<string> Prompt satırları
     */
    public function buildPrompt(): array;

    /**
     * Tenant'ın özel kurallarını döndürür (AIResponseNode için)
     * Bu kurallar system prompt'a eklenir
     *
     * @return string
     */
    public function getSpecialRules(): string;

    /**
     * Tenant için "ürün bulunamadı" mesajını döndürür
     *
     * @return string
     */
    public function getNoProductMessage(): string;

    /**
     * Tenant'ın iletişim bilgilerini döndürür
     *
     * @return array{phone?: string, whatsapp?: string, email?: string}
     */
    public function getContactInfo(): array;

    /**
     * Tenant'ın sektörünü döndürür
     *
     * @return string (örn: 'industrial', 'music', 'ecommerce')
     */
    public function getSector(): string;
}
