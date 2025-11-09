<?php

namespace Modules\Muzibu\App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Muzibu\App\Models\Muzibu;

interface MuzibuRepositoryInterface
{
    public function findById(int $id): ?Muzibu;

    public function findByIdWithSeo(int $id): ?Muzibu;

    public function findBySlug(string $slug, string $locale = 'tr'): ?Muzibu;

    public function getActive(): Collection;

    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function search(string $term, array $locales = []): Collection;

    public function create(array $data): Muzibu;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function toggleActive(int $id): bool;

    public function bulkDelete(array $ids): int;

    public function bulkToggleActive(array $ids): int;

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool;

    public function clearCache(): void;
}
