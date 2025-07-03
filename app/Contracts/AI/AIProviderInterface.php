<?php

namespace App\Contracts\AI;

interface AIProviderInterface
{
    /**
     * Provider adını döndür
     */
    public function getName(): string;

    /**
     * AI isteği gönder
     */
    public function sendRequest(array $messages, array $options = []): array;

    /**
     * Stream isteği gönder
     */
    public function sendStreamRequest(array $messages, array $options = []): \Generator;

    /**
     * Token sayısını hesapla
     */
    public function calculateTokens(string $text): int;

    /**
     * Provider'ın aktif olup olmadığını kontrol et
     */
    public function isActive(): bool;

    /**
     * Provider konfigürasyonunu doğrula
     */
    public function validateConfiguration(): bool;

    /**
     * Provider'ı test et
     */
    public function testConnection(): array;
}