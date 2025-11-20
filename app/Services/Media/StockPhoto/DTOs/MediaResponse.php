<?php

namespace App\Services\Media\StockPhoto\DTOs;

/**
 * Media Response DTO
 *
 * Provider'dan dönen görsel bilgisini taşır
 */
class MediaResponse
{
    public function __construct(
        public readonly string $url,                // Görsel URL'i (download için)
        public readonly int $width,                 // Genişlik
        public readonly int $height,                // Yükseklik
        public readonly string $provider,           // Provider adı (pexels, unsplash, pixabay, dalle)
        public readonly ?string $photographer = null, // Fotoğrafçı adı (attribution için)
        public readonly ?string $photographerUrl = null, // Fotoğrafçı profil URL'i
        public readonly ?string $providerUrl = null,     // Provider kaynak URL'i (attribution)
        public readonly int|string|null $providerId = null,    // Provider'daki ID (int veya string, örn: Pexels=int, Unsplash=string)
        public readonly ?string $altText = null,    // Alt text (SEO için)
        public readonly array $metadata = []        // Ek bilgiler (color, tags, cost vs)
    ) {
    }

    /**
     * Array'den DTO oluştur
     */
    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'],
            width: $data['width'],
            height: $data['height'],
            provider: $data['provider'],
            photographer: $data['photographer'] ?? null,
            photographerUrl: $data['photographer_url'] ?? null,
            providerUrl: $data['provider_url'] ?? null,
            providerId: $data['provider_id'] ?? null,
            altText: $data['alt_text'] ?? null,
            metadata: $data['metadata'] ?? []
        );
    }

    /**
     * DTO'yu array'e çevir
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'width' => $this->width,
            'height' => $this->height,
            'provider' => $this->provider,
            'photographer' => $this->photographer,
            'photographer_url' => $this->photographerUrl,
            'provider_url' => $this->providerUrl,
            'provider_id' => $this->providerId,
            'alt_text' => $this->altText,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Attribution string oluştur (stock photo için)
     *
     * @return string|null
     */
    public function getAttributionText(): ?string
    {
        if (!$this->photographer) {
            return null;
        }

        $text = "Photo by {$this->photographer}";

        if ($this->providerUrl) {
            $text .= " on {$this->provider}";
        }

        return $text;
    }

    /**
     * Attribution HTML oluştur (stock photo için)
     *
     * @return string|null
     */
    public function getAttributionHtml(): ?string
    {
        if (!$this->photographer) {
            return null;
        }

        $html = 'Photo by ';

        if ($this->photographerUrl) {
            $html .= "<a href=\"{$this->photographerUrl}\" target=\"_blank\" rel=\"noopener\">{$this->photographer}</a>";
        } else {
            $html .= $this->photographer;
        }

        if ($this->providerUrl) {
            $html .= " on <a href=\"{$this->providerUrl}\" target=\"_blank\" rel=\"noopener\">{$this->provider}</a>";
        }

        return $html;
    }

    /**
     * Maliyet bilgisi al (metadata'dan)
     */
    public function getCost(): float
    {
        return $this->metadata['cost'] ?? 0.0;
    }

    /**
     * Ücretsiz mi?
     */
    public function isFree(): bool
    {
        return $this->getCost() === 0.0;
    }
}
