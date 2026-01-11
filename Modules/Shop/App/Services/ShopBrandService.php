<?php

declare(strict_types=1);

namespace Modules\Shop\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Shop\App\Contracts\ShopBrandRepositoryInterface;
use Modules\Shop\App\Models\ShopBrand;

class ShopBrandService
{
    public function __construct(
        private readonly ShopBrandRepositoryInterface $repository
    ) {}

    public function getPaginatedBrands(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    public function findBrand(int $id): ?ShopBrand
    {
        return $this->repository->findById($id);
    }

    public function findBySlug(string $slug, string $locale = 'tr'): ?ShopBrand
    {
        return $this->repository->findBySlug($slug, $locale);
    }

    public function getActiveBrands(): Collection
    {
        return $this->repository->getActive();
    }

    public function getFeaturedBrands(): Collection
    {
        return $this->repository->getFeatured();
    }

    public function createBrand(array $data): array
    {
        try {
            $brand = $this->repository->create($data);

            Log::info('Shop brand created', [
                'brand_id' => $brand->brand_id,
                'title' => $brand->title,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'message' => __('shop::admin.brand_created'),
                'data' => $brand
            ];
        } catch (\Exception $e) {
            Log::error('Shop brand creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => false,
                'message' => __('shop::admin.brand_create_failed'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function updateBrand(int $id, array $data): bool
    {
        $result = $this->repository->update($id, $data);

        if ($result) {
            Log::info('Shop brand updated', [
                'brand_id' => $id,
                'user_id' => auth()->id()
            ]);
        }

        return $result;
    }

    public function deleteBrand(int $id): array
    {
        try {
            $brand = $this->repository->findById($id);

            if (!$brand) {
                return [
                    'success' => false,
                    'message' => __('shop::admin.brand_not_found')
                ];
            }

            if ($brand->products()->count() > 0) {
                Log::warning('Cannot delete brand with products', [
                    'brand_id' => $id,
                    'product_count' => $brand->products()->count()
                ]);

                return [
                    'success' => false,
                    'message' => __('shop::admin.brand_has_products')
                ];
            }

            $deleted = $this->repository->delete($id);

            if ($deleted) {
                Log::info('Shop brand deleted', [
                    'brand_id' => $id,
                    'user_id' => auth()->id()
                ]);

                return [
                    'success' => true,
                    'message' => __('shop::admin.brand_deleted')
                ];
            }

            return [
                'success' => false,
                'message' => __('shop::admin.brand_delete_failed')
            ];
        } catch (\Exception $e) {
            Log::error('Shop brand deletion failed', [
                'brand_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return [
                'success' => false,
                'message' => __('shop::admin.brand_delete_failed'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function toggleBrandStatus(int $id): array
    {
        try {
            $brand = $this->repository->findById($id);

            if (!$brand) {
                return [
                    'success' => false,
                    'type' => 'error',
                    'message' => __('shop::admin.brand_not_found')
                ];
            }

            $newStatus = !$brand->is_active;
            $result = $this->repository->toggleActive($id);

            if ($result) {
                Log::info('Shop brand status toggled', [
                    'brand_id' => $id,
                    'new_status' => $newStatus,
                    'user_id' => auth()->id()
                ]);

                return [
                    'success' => true,
                    'type' => 'success',
                    'message' => __('shop::admin.status_updated'),
                    'data' => $brand,
                    'meta' => ['new_status' => $newStatus]
                ];
            }

            return [
                'success' => false,
                'type' => 'error',
                'message' => __('shop::admin.status_update_failed')
            ];
        } catch (\Exception $e) {
            Log::error('Shop brand status toggle error', [
                'brand_id' => $id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'type' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function bulkDeleteBrands(array $ids): int
    {
        return $this->repository->bulkDelete($ids);
    }

    public function bulkToggleActive(array $ids): int
    {
        return $this->repository->bulkToggleActive($ids);
    }

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        return $this->repository->updateSeoField($id, $locale, $field, $value);
    }

    public function clearCache(): void
    {
        $this->repository->clearCache();
    }
}
