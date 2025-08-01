<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface UrlBuilderInterface
{
    /**
     * Model için URL oluştur
     */
    public function buildUrlForModel(Model $model, string $action = 'show', ?string $locale = null): string;

    /**
     * Module için URL oluştur
     */
    public function buildUrlForModule(string $module, string $action = 'index', ?array $params = null, ?string $locale = null): string;

    /**
     * Path için URL oluştur
     */
    public function buildUrlForPath(string $path, ?string $locale = null): string;

    /**
     * Mevcut URL için alternate linkler oluştur
     */
    public function generateAlternateLinks(?Model $model = null, string $action = 'show'): array;

    /**
     * URL'den route bilgisi çıkar
     */
    public function parseUrl(string $url): array;

    /**
     * Cache'i temizle
     */
    public function clearCache(?string $locale = null): void;

    /**
     * Performance metrikleri
     */
    public function getPerformanceMetrics(): array;
}