<?php

namespace Modules\Announcement\App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Announcement\App\Models\Announcement;

interface AnnouncementRepositoryInterface
{
    /**
     * ID ile duyuru bul
     */
    public function findById(int $id, array $with = []): ?Announcement;

    /**
     * Duyuru arama
     */
    public function search(array $filters = []): Collection;

    /**
     * Sayfalanmış duyuru listesi
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Duyuru oluştur
     */
    public function create(array $data): Announcement;

    /**
     * Duyuru güncelle 
     */
    public function update(int $id, array $data): Announcement;

    /**
     * Duyuru sil
     */
    public function delete(int $id): bool;

    /**
     * Aktif duyuruları getir
     */
    public function getActive(): Collection;

    /**
     * Cache temizle
     */
    public function clearCache(int $id = null): void;

    /**
     * Duyuru SEO verilerini güncelle
     */
    public function updateSeo(int $id, array $seoData): Announcement;

    /**
     * Son duyuruları getir
     */
    public function getRecent(int $limit = 10): Collection;

    /**
     * Slug ile duyuru bul
     */
    public function findBySlug(string $slug): ?Announcement;

    /**
     * Popüler duyuruları getir
     */
    public function getPopular(int $limit = 10): Collection;
}