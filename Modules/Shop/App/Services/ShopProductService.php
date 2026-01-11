<?php

declare(strict_types=1);

namespace Modules\Shop\App\Services;

use App\Contracts\GlobalSeoRepositoryInterface;
use App\Services\BaseService;
use App\Services\GlobalTabService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Shop\App\Contracts\ShopProductRepositoryInterface;
use Modules\Shop\App\DataTransferObjects\{BulkOperationResult, ShopOperationResult};
use Modules\Shop\App\Exceptions\{ShopCreationException, ShopNotFoundException, ShopProtectionException};
use Modules\Shop\App\Models\ShopProduct;
use Throwable;

readonly class ShopProductService extends BaseService
{
    public function __construct(
        private ShopProductRepositoryInterface $productRepository,
        private GlobalSeoRepositoryInterface $seoRepository
    ) {}

    public function getProduct(int $id): ShopProduct
    {
        return $this->productRepository->findById($id)
            ?? throw ShopNotFoundException::withId($id);
    }

    public function getProductBySlug(string $slug, string $locale = 'tr'): ShopProduct
    {
        return $this->productRepository->findBySlug($slug, $locale)
            ?? throw ShopNotFoundException::withSlug($slug, $locale);
    }

    public function getActiveProducts(): Collection
    {
        return $this->productRepository->getActive();
    }

    public function getPaginatedProducts(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->productRepository->getPaginated($filters, $perPage);
    }

    public function searchProducts(string $term, array $locales = []): Collection
    {
        return $this->productRepository->search($term, $locales);
    }

    public function createProduct(array $data): ShopOperationResult
    {
        try {
            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title']);
            }

            if (isset($data['seo']) && is_array($data['seo'])) {
                $data['seo'] = $this->prepareSeoData($data['seo']);
            }

            $product = $this->productRepository->create($data);

            Log::info('Shop product created', [
                'product_id' => $product->product_id,
                'title' => $product->title,
                'user_id' => auth()->id()
            ]);

            return ShopOperationResult::success(
                message: __('shop::admin.product_created_successfully'),
                data: $product
            );
        } catch (Throwable $e) {
            Log::error('Shop product creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            throw ShopCreationException::withDatabaseError($e->getMessage());
        }
    }

    public function updateProduct(int $id, array $data): ShopOperationResult
    {
        try {
            $product = $this->productRepository->findById($id)
                ?? throw ShopNotFoundException::withId($id);

            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title'], $product->slug ?? []);
            }

            if (isset($data['seo']) && is_array($data['seo'])) {
                $data['seo'] = $this->prepareSeoData($data['seo'], $product->seo ?? []);
            }

            $this->productRepository->update($id, $data);

            Log::info('Shop product updated', [
                'product_id' => $id,
                'title' => $data['title'] ?? 'unchanged',
                'user_id' => auth()->id()
            ]);

            return ShopOperationResult::success(
                message: __('shop::admin.product_updated_successfully'),
                data: $product->refresh()
            );
        } catch (ShopNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Shop product update failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return ShopOperationResult::error(
                message: __('shop::admin.update_failed'),
                type: 'error'
            );
        }
    }

    public function deleteProduct(int $id): ShopOperationResult
    {
        try {
            $product = $this->productRepository->findById($id)
                ?? throw ShopNotFoundException::withId($id);

            $this->productRepository->delete($id);

            Log::info('Shop product deleted', [
                'product_id' => $id,
                'title' => $product->title,
                'user_id' => auth()->id()
            ]);

            return ShopOperationResult::success(
                message: __('shop::admin.product_deleted_successfully')
            );
        } catch (ShopNotFoundException | ShopProtectionException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Shop product deletion failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return ShopOperationResult::error(
                message: __('shop::admin.deletion_failed'),
                type: 'error'
            );
        }
    }

    public function toggleProductStatus(int $id): ShopOperationResult
    {
        try {
            $product = $this->productRepository->findById($id)
                ?? throw ShopNotFoundException::withId($id);

            $this->productRepository->toggleActive($id);
            $product->refresh();

            Log::info('Shop product status toggled', [
                'product_id' => $id,
                'new_status' => $product->is_active,
                'user_id' => auth()->id()
            ]);

            return ShopOperationResult::success(
                message: __($product->is_active ? 'admin.activated' : 'admin.deactivated'),
                data: $product,
                meta: ['new_status' => $product->is_active]
            );
        } catch (ShopNotFoundException $e) {
            return ShopOperationResult::error(
                message: __('shop::admin.product_not_found'),
                type: 'error'
            );
        } catch (ShopProtectionException $e) {
            return ShopOperationResult::warning(
                message: $e->getMessage()
            );
        } catch (Throwable $e) {
            Log::error('Shop product status toggle failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return ShopOperationResult::error(
                message: __('admin.operation_failed'),
                type: 'error'
            );
        }
    }

    public function bulkDeleteProducts(array $ids): BulkOperationResult
    {
        try {
            if (empty($ids)) {
                return BulkOperationResult::failure(
                    message: __('shop::admin.select_records_first')
                );
            }

            $deletedCount = $this->productRepository->bulkDelete($ids);

            Log::info('Shop product bulk delete', [
                'deleted_count' => $deletedCount,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::success(
                message: trans_choice('shop::admin.products_deleted', $deletedCount, ['count' => $deletedCount]),
                affectedCount: $deletedCount
            );
        } catch (Throwable $e) {
            Log::error('Shop product bulk delete failed', [
                'error' => $e->getMessage(),
                'ids' => $ids,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::failure(
                message: __('admin.bulk_operation_failed'),
                errors: [$e->getMessage()]
            );
        }
    }

    public function bulkToggleProductStatus(array $ids): BulkOperationResult
    {
        try {
            $affectedCount = $this->productRepository->bulkToggleActive($ids);

            Log::info('Shop product bulk status toggle', [
                'affected_count' => $affectedCount,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::success(
                message: trans_choice('shop::admin.products_status_changed', $affectedCount, ['count' => $affectedCount]),
                affectedCount: $affectedCount
            );
        } catch (Throwable $e) {
            Log::error('Shop product bulk status toggle failed', [
                'error' => $e->getMessage(),
                'ids' => $ids,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::failure(
                message: __('admin.bulk_operation_failed'),
                errors: [$e->getMessage()]
            );
        }
    }

    public function prepareProductForForm(int $id, string $language): array
    {
        $product = $this->productRepository->findByIdWithSeo($id);

        if (!$product) {
            return $this->getEmptyFormData($language);
        }

        $seoData = $this->seoRepository->getSeoData($product, $language);
        $allData = array_merge($product->toArray(), $seoData);
        $tabCompletion = GlobalTabService::getTabCompletionStatus($allData, 'shop');

        return [
            'product' => $product,
            'seoData' => $seoData,
            'tabCompletion' => $tabCompletion,
            'tabConfig' => GlobalTabService::getJavaScriptConfig('shop'),
            'seoLimits' => $this->seoRepository->getFieldLimits('shop'),
        ];
    }

    public function getEmptyFormData(string $language): array
    {
        $emptyData = [
            'title' => '',
            'short_description' => '',
            'body' => '',
            'slug' => '',
            'seo_title' => '',
            'seo_description' => '',
            'canonical_url' => '',
        ];

        return [
            'product' => null,
            'seoData' => $emptyData,
            'tabCompletion' => GlobalTabService::getTabCompletionStatus($emptyData, 'shop'),
            'tabConfig' => GlobalTabService::getJavaScriptConfig('shop'),
            'seoLimits' => $this->seoRepository->getFieldLimits('shop'),
        ];
    }

    public function getValidationRules(array $availableLanguages): array
    {
        $rules = [
            'inputs.is_active' => 'boolean',
            'inputs.category_id' => 'required|integer|exists:shop_categories,category_id',
            'inputs.brand_id' => 'nullable|integer|exists:shop_brands,brand_id',
            'inputs.sku' => 'required|string|max:191',
            'inputs.product_type' => 'required|string|in:physical,digital,service,membership,bundle',
            'inputs.condition' => 'required|string|in:new,used,refurbished',
            'inputs.base_price' => 'nullable|numeric|min:0',
            'inputs.compare_at_price' => 'nullable|numeric|min:0',
        ];

        $defaultLocale = get_tenant_default_locale();

        foreach ($availableLanguages as $lang) {
            $rules["multiLangInputs.{$lang}.title"] = $lang === $defaultLocale ? 'required|min:3|max:255' : 'nullable|min:3|max:255';
            $rules["multiLangInputs.{$lang}.slug"] = 'nullable|string|max:255';
            $rules["multiLangInputs.{$lang}.short_description"] = 'nullable|string';
            $rules["multiLangInputs.{$lang}.body"] = 'nullable|string';
        }

        $seoRules = $this->seoRepository->getValidationRules('shop');

        return array_merge($rules, $seoRules);
    }

    public function calculateSeoScore(array $seoData): array
    {
        return $this->seoRepository->calculateSeoScore($seoData, 'shop');
    }

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        $result = $this->productRepository->updateSeoField($id, $locale, $field, $value);

        if ($result) {
            Log::info('Shop product SEO field updated', [
                'product_id' => $id,
                'locale' => $locale,
                'field' => $field,
                'user_id' => auth()->id()
            ]);
        }

        return $result;
    }

    public function clearCache(): void
    {
        $this->productRepository->clearCache();

        Log::info('Shop product cache cleared', [
            'user_id' => auth()->id()
        ]);
    }

    protected function generateSlugsFromTitles(array $titles, array $existingSlugs = []): array
    {
        $slugs = $existingSlugs;

        foreach ($titles as $locale => $title) {
            if (!empty($title) && empty($slugs[$locale])) {
                $slugs[$locale] = \Str::slug($title);
            }
        }

        return $slugs;
    }

    protected function prepareSeoData(array $seoData, array $existingSeo = []): array
    {
        $prepared = $existingSeo;

        foreach ($seoData as $locale => $data) {
            if (is_array($data)) {
                $cleanData = array_filter(
                    $data,
                    static fn($value) => $value !== null && $value !== '' && $value !== []
                );

                if (!empty($cleanData)) {
                    $prepared[$locale] = array_merge($prepared[$locale] ?? [], $cleanData);
                }
            }
        }

        return $prepared;
    }

    /**
     * 🎨 AI Image Generation: Ürün için AI görsel oluştur
     *
     * @param int $productId Ürün ID
     * @return ShopOperationResult
     */
    public function generateAIImage(int $productId): ShopOperationResult
    {
        try {
            $product = $this->productRepository->findById($productId)
                ?? throw ShopNotFoundException::withId($productId);

            // AI Image Generation Service
            $imageService = app(\Modules\AI\App\Services\AIImageGenerationService::class);

            // Ürün başlığını ve kategorisini al
            $productName = $product->getTranslated('title', app()->getLocale());
            $categoryName = $product->category
                ? $product->category->getTranslated('title', app()->getLocale())
                : null;

            // AI ile görsel oluştur
            $mediaItem = $imageService->generateForProduct($productName, $categoryName);

            // Görseli ürüne ekle
            $product->addMedia($mediaItem->getFirstMedia('library')->getPath())
                ->preservingOriginal()
                ->toMediaCollection('images');

            Log::info('Shop product AI image generated', [
                'product_id' => $productId,
                'media_id' => $mediaItem->id,
                'user_id' => auth()->id()
            ]);

            return ShopOperationResult::success(
                message: __('AI image generated successfully'),
                data: $mediaItem
            );

        } catch (\Modules\AI\App\Exceptions\AICreditException $e) {
            Log::warning('Shop product AI image generation failed: insufficient credits', [
                'product_id' => $productId,
                'user_id' => auth()->id()
            ]);

            return ShopOperationResult::failure(
                message: __('Insufficient AI credits'),
                type: 'warning'
            );

        } catch (Throwable $e) {
            Log::error('Shop product AI image generation failed', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return ShopOperationResult::failure(
                message: __('AI image generation failed: ' . $e->getMessage()),
                type: 'error'
            );
        }
    }
}
