<?php

declare(strict_types=1);

namespace Modules\Shop\App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Shop\App\Models\ShopProductVariant;

interface ShopProductVariantRepositoryInterface
{
    public function findById(int $id): ?ShopProductVariant;

    public function getByProduct(int $productId, array $filters = []): Collection;

    public function getPaginatedByProduct(int $productId, array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function getActiveByProduct(int $productId): Collection;

    public function getDefaultVariant(int $productId): ?ShopProductVariant;

    public function create(array $data): ShopProductVariant;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function bulkDelete(array $ids): int;

    public function toggleActive(int $id): bool;

    public function setDefaultVariant(int $productId, int $variantId): bool;

    public function updateSortOrders(int $productId, array $orderedItems): void;

    public function clearCache(?int $productId = null): void;
}

