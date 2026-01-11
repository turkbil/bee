<?php

namespace Modules\Announcement\App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Announcement\App\Models\Announcement;

interface AnnouncementRepositoryInterface
{
    public function findById(int $id): ?Announcement;

    public function findByIdWithSeo(int $id): ?Announcement;

    public function findBySlug(string $slug, string $locale = 'tr'): ?Announcement;

    public function getActive(): Collection;

    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function search(string $term, array $locales = []): Collection;

    public function create(array $data): Announcement;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function toggleActive(int $id): bool;

    public function bulkDelete(array $ids): int;

    public function bulkToggleActive(array $ids): int;

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool;

    public function clearCache(): void;
}
