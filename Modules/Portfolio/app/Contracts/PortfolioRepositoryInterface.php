<?php

namespace Modules\Portfolio\App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Portfolio\App\Models\Portfolio;

interface PortfolioRepositoryInterface
{
    /**
     * ID ile portfolio bul
     */
    public function findById(int $id, array $with = []): ?Portfolio;

    /**
     * Portfolio arama
     */
    public function search(array $filters = []): Collection;

    /**
     * Sayfalanmış portfolio listesi
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Portfolio oluştur
     */
    public function create(array $data): Portfolio;

    /**
     * Portfolio güncelle 
     */
    public function update(int $id, array $data): Portfolio;

    /**
     * Portfolio sil
     */
    public function delete(int $id): bool;

    /**
     * Aktif portfolioları getir
     */
    public function getActive(): Collection;

    /**
     * Kategoriye göre portfolioları getir
     */
    public function getByCategory(int $categoryId): Collection;

    /**
     * Cache temizle
     */
    public function clearCache(int $id = null): void;

    /**
     * Portfolio SEO verilerini güncelle
     */
    public function updateSeo(int $id, array $seoData): Portfolio;

    /**
     * Son portfolioları getir
     */
    public function getRecent(int $limit = 10): Collection;
}