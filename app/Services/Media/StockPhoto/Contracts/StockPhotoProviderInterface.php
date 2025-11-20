<?php

namespace App\Services\Media\StockPhoto\Contracts;

use App\Services\Media\StockPhoto\DTOs\MediaRequest;
use App\Services\Media\StockPhoto\DTOs\MediaResponse;

/**
 * Stock Photo Provider Interface
 *
 * Tüm görsel sağlayıcıları (Pexels, Unsplash, Pixabay, DALL-E) bu interface'i implement eder
 */
interface StockPhotoProviderInterface
{
    /**
     * Provider adı
     *
     * @return string (örn: 'pexels', 'unsplash', 'pixabay', 'dalle')
     */
    public function getName(): string;

    /**
     * Provider aktif mi?
     *
     * @return bool
     */
    public function isAvailable(): bool;

    /**
     * Görsel ara ve indir
     *
     * @param MediaRequest $request Arama kriterleri
     * @return MediaResponse Görsel bilgisi
     * @throws \App\Services\Media\StockPhoto\Exceptions\MediaNotFoundException
     * @throws \App\Services\Media\StockPhoto\Exceptions\ProviderNotAvailableException
     */
    public function fetch(MediaRequest $request): MediaResponse;

    /**
     * API limiti kontrol et (opsiyonel)
     *
     * @return array{remaining: int, limit: int, reset_at: ?string}
     */
    public function getRateLimit(): array;
}
