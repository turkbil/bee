<?php

namespace App\Services\Media\StockPhoto\DTOs;

/**
 * Media Request DTO
 *
 * Görsel arama kriterlerini taşır
 */
class MediaRequest
{
    public function __construct(
        public readonly string $query,              // Arama terimi (örn: "industrial forklift", "music studio")
        public readonly string $orientation = 'landscape', // 'landscape', 'portrait', 'square'
        public readonly ?int $width = null,         // İstenen genişlik (opsiyonel)
        public readonly ?int $height = null,        // İstenen yükseklik (opsiyonel)
        public readonly string $locale = 'en',      // Dil (tr, en, de vs)
        public readonly ?string $color = null,      // Renk filtresi (opsiyonel, hex kod)
        public readonly int $perPage = 15,          // Kaç sonuç (random seçim için)
        public readonly array $metadata = []        // Ek bilgiler (context: blog/product/page, tenant_id, category vs)
    ) {
    }

    /**
     * Array'den DTO oluştur
     */
    public static function fromArray(array $data): self
    {
        return new self(
            query: $data['query'] ?? '',
            orientation: $data['orientation'] ?? 'landscape',
            width: $data['width'] ?? null,
            height: $data['height'] ?? null,
            locale: $data['locale'] ?? 'en',
            color: $data['color'] ?? null,
            perPage: $data['per_page'] ?? 15,
            metadata: $data['metadata'] ?? []
        );
    }

    /**
     * DTO'yu array'e çevir
     */
    public function toArray(): array
    {
        return [
            'query' => $this->query,
            'orientation' => $this->orientation,
            'width' => $this->width,
            'height' => $this->height,
            'locale' => $this->locale,
            'color' => $this->color,
            'per_page' => $this->perPage,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Context bilgisi al (blog, product, page)
     */
    public function getContext(): ?string
    {
        return $this->metadata['context'] ?? null;
    }

    /**
     * Tenant ID al
     */
    public function getTenantId(): ?int
    {
        return $this->metadata['tenant_id'] ?? null;
    }
}
