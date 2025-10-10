<?php

namespace Modules\Shop\App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Shop\App\Models\ShopCategory;

interface ShopCategoryRepositoryInterface
{
    public function findById(int $id): ?ShopCategory;

    public function findByIdWithSeo(int $id): ?ShopCategory;

    public function findBySlug(string $slug, string $locale = 'tr'): ?ShopCategory;

    public function getActive(): Collection;

    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function search(string $term, array $locales = []): Collection;

    public function create(array $data): ShopCategory;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function toggleActive(int $id): bool;

    public function bulkDelete(array $ids): int;

    public function bulkToggleActive(array $ids): int;

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool;

    public function clearCache(): void;
}
