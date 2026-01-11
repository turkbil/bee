<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Templates;

/**
 * 📄 Template Repository Interface
 * 
 * Template data storage ve retrieval için interface
 */
interface TemplateRepository
{
    /**
     * Template'i isimle bul
     *
     * @param string $name Template name
     * @return array|null Template data
     */
    public function findByName(string $name): ?array;

    /**
     * Feature için template'leri al
     *
     * @param string $featureName Feature name
     * @return array Template list
     */
    public function findByFeature(string $featureName): array;

    /**
     * Template kaydet
     *
     * @param array $templateData Template data
     * @return bool Success status
     */
    public function save(array $templateData): bool;

    /**
     * Template sil
     *
     * @param string $name Template name
     * @return bool Success status
     */
    public function delete(string $name): bool;

    /**
     * Tüm template'leri listele
     *
     * @return array Template list
     */
    public function all(): array;
}