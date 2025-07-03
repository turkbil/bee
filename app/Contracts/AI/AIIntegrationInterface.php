<?php

namespace App\Contracts\AI;

interface AIIntegrationInterface
{
    /**
     * Entegrasyon adını döndür
     */
    public function getName(): string;

    /**
     * Desteklenen action'ları döndür
     */
    public function getSupportedActions(): array;

    /**
     * Belirtilen action'ı çalıştır
     */
    public function executeAction(string $action, array $parameters = []): array;

    /**
     * Action'ın gerekli token miktarını hesapla
     */
    public function estimateTokens(string $action, array $parameters = []): int;

    /**
     * Entegrasyonun aktif olup olmadığını kontrol et
     */
    public function isActive(): bool;

    /**
     * Action parametrelerini doğrula
     */
    public function validateActionParameters(string $action, array $parameters): bool;

    /**
     * Entegrasyon konfigürasyonunu getir
     */
    public function getConfiguration(): array;
}