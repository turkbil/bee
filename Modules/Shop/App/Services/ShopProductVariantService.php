<?php

declare(strict_types=1);

namespace Modules\Shop\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Shop\App\Contracts\ShopProductVariantRepositoryInterface;
use Modules\Shop\App\Models\ShopProductVariant;
use Throwable;

class ShopProductVariantService
{
    public function __construct(
        private readonly ShopProductVariantRepositoryInterface $repository
    ) {}

    public function getPaginatedVariants(int $productId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPaginatedByProduct($productId, $filters, $perPage);
    }

    public function getVariants(int $productId, array $filters = []): Collection
    {
        return $this->repository->getByProduct($productId, $filters);
    }

    public function getActiveVariants(int $productId): Collection
    {
        return $this->repository->getActiveByProduct($productId);
    }

    public function findVariant(int $variantId): ?ShopProductVariant
    {
        return $this->repository->findById($variantId);
    }

    public function getDefaultVariant(int $productId): ?ShopProductVariant
    {
        return $this->repository->getDefaultVariant($productId);
    }

    public function createVariant(array $data): array
    {
        try {
            $variant = $this->repository->create($data);

            Log::info('Shop product variant created', [
                'variant_id' => $variant->variant_id,
                'product_id' => $variant->product_id,
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => true,
                'type' => 'success',
                'message' => __('shop::admin.variant_created_successfully'),
                'data' => $variant,
            ];
        } catch (Throwable $e) {
            Log::error('Shop product variant creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => false,
                'type' => 'error',
                'message' => __('shop::admin.variant_create_failed'),
                'error' => $e->getMessage(),
            ];
        }
    }

    public function updateVariant(int $variantId, array $data): array
    {
        try {
            $variant = $this->repository->findById($variantId);

            if (!$variant) {
                return [
                    'success' => false,
                    'type' => 'error',
                    'message' => __('shop::admin.variant_not_found'),
                ];
            }

            $updated = $this->repository->update($variantId, $data);

            if ($updated) {
                Log::info('Shop product variant updated', [
                    'variant_id' => $variantId,
                    'product_id' => $variant->product_id,
                    'user_id' => auth()->id(),
                ]);

                return [
                    'success' => true,
                    'type' => 'success',
                    'message' => __('shop::admin.variant_updated_successfully'),
                    'data' => $variant->refresh(),
                ];
            }

            return [
                'success' => false,
                'type' => 'error',
                'message' => __('shop::admin.variant_update_failed'),
            ];
        } catch (Throwable $e) {
            Log::error('Shop product variant update failed', [
                'variant_id' => $variantId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => false,
                'type' => 'error',
                'message' => __('shop::admin.variant_update_failed'),
                'error' => $e->getMessage(),
            ];
        }
    }

    public function deleteVariant(int $variantId): array
    {
        try {
            $variant = $this->repository->findById($variantId);

            if (!$variant) {
                return [
                    'success' => false,
                    'type' => 'error',
                    'message' => __('shop::admin.variant_not_found'),
                ];
            }

            $deleted = $this->repository->delete($variantId);

            if ($deleted) {
                Log::info('Shop product variant deleted', [
                    'variant_id' => $variantId,
                    'product_id' => $variant->product_id,
                    'user_id' => auth()->id(),
                ]);

                return [
                    'success' => true,
                    'type' => 'success',
                    'message' => __('shop::admin.variant_deleted_successfully'),
                ];
            }

            return [
                'success' => false,
                'type' => 'error',
                'message' => __('shop::admin.variant_delete_failed'),
            ];
        } catch (Throwable $e) {
            Log::error('Shop product variant deletion failed', [
                'variant_id' => $variantId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => false,
                'type' => 'error',
                'message' => __('shop::admin.variant_delete_failed'),
                'error' => $e->getMessage(),
            ];
        }
    }

    public function toggleVariantStatus(int $variantId): array
    {
        try {
            $variant = $this->repository->findById($variantId);

            if (!$variant) {
                return [
                    'success' => false,
                    'type' => 'error',
                    'message' => __('shop::admin.variant_not_found'),
                ];
            }

            $result = $this->repository->toggleActive($variantId);

            if ($result) {
                $variant->refresh();

                Log::info('Shop product variant status toggled', [
                    'variant_id' => $variantId,
                    'product_id' => $variant->product_id,
                    'new_status' => $variant->is_active,
                    'user_id' => auth()->id(),
                ]);

                return [
                    'success' => true,
                    'type' => 'success',
                    'message' => __('admin.status_updated'),
                    'data' => $variant,
                    'meta' => [
                        'new_status' => $variant->is_active,
                    ],
                ];
            }

            return [
                'success' => false,
                'type' => 'error',
                'message' => __('admin.operation_failed'),
            ];
        } catch (Throwable $e) {
            Log::error('Shop product variant status toggle failed', [
                'variant_id' => $variantId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => false,
                'type' => 'error',
                'message' => __('admin.operation_failed'),
                'error' => $e->getMessage(),
            ];
        }
    }

    public function bulkDeleteVariants(array $variantIds): array
    {
        try {
            if (empty($variantIds)) {
                return [
                    'success' => false,
                    'type' => 'warning',
                    'message' => __('admin.select_records_first'),
                ];
            }

            $deleted = $this->repository->bulkDelete($variantIds);

            if ($deleted > 0) {
                Log::info('Shop product variants bulk deleted', [
                    'variant_ids' => $variantIds,
                    'deleted_count' => $deleted,
                    'user_id' => auth()->id(),
                ]);

                return [
                    'success' => true,
                    'type' => 'success',
                    'message' => trans_choice('shop::admin.variants_deleted', $deleted, ['count' => $deleted]),
                    'meta' => ['deleted' => $deleted],
                ];
            }

            return [
                'success' => false,
                'type' => 'error',
                'message' => __('shop::admin.variant_delete_failed'),
            ];
        } catch (Throwable $e) {
            Log::error('Shop product variants bulk delete failed', [
                'variant_ids' => $variantIds,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => false,
                'type' => 'error',
                'message' => __('shop::admin.variant_delete_failed'),
                'error' => $e->getMessage(),
            ];
        }
    }

    public function setDefaultVariant(int $productId, int $variantId): array
    {
        $result = $this->repository->setDefaultVariant($productId, $variantId);

        if ($result) {
            Log::info('Shop product default variant set', [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => true,
                'type' => 'success',
                'message' => __('shop::admin.variant_marked_as_default'),
            ];
        }

        return [
            'success' => false,
            'type' => 'error',
            'message' => __('shop::admin.variant_not_found'),
        ];
    }

    public function updateVariantOrder(int $productId, array $orderedItems): array
    {
        try {
            $this->repository->updateSortOrders($productId, $orderedItems);

            return [
                'success' => true,
                'type' => 'success',
                'message' => __('shop::admin.variant_order_updated'),
            ];
        } catch (Throwable $e) {
            Log::error('Shop product variant order update failed', [
                'product_id' => $productId,
                'ordered_items' => $orderedItems,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => false,
                'type' => 'error',
                'message' => __('admin.operation_failed'),
                'error' => $e->getMessage(),
            ];
        }
    }
}

