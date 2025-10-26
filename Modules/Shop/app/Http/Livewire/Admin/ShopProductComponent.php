<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use App\Traits\HasUniversalTranslation;
use Livewire\Attributes\{Layout, Url};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\Shop\App\DataTransferObjects\ShopOperationResult;
use Modules\Shop\App\Http\Livewire\Traits\{InlineEditTitle, WithBulkActions};
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Services\ShopProductService;

#[Layout('admin.layout')]
class ShopProductComponent extends Component
{
    use WithPagination;
    use WithBulkActions;
    use InlineEditTitle;
    use HasUniversalTranslation;

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $perPage = null;

    #[Url]
    public string $sortField = 'product_id';

    #[Url]
    public string $sortDirection = 'desc';

    #[Url]
    public ?int $selectedCategory = null;

    public array $availableSiteLanguages = [];

    public array $categories = [];

    // Inline price editing
    public ?int $editingPriceId = null;
    public ?string $newPrice = null;
    public ?string $newCurrency = null;
    public bool $newPriceOnRequest = false;

    protected ShopProductService $productService;

    protected $listeners = [
        'refreshPageData' => 'refreshPageData',
    ];

    public function boot(ShopProductService $productService): void
    {
        $this->productService = $productService;
        $this->perPage ??= (int) config('modules.pagination.admin_per_page', 15);
        $this->availableSiteLanguages = TenantLanguage::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();

        // Load all categories hierarchically for dropdown
        $this->categories = $this->buildHierarchicalCategories();
    }

    protected function getModelClass(): string
    {
        return ShopProduct::class;
    }

    /**
     * Build hierarchical category list with indentation
     */
    protected function buildHierarchicalCategories(): array
    {
        $categories = \Modules\Shop\App\Models\ShopCategory::query()
            ->orderBy('sort_order')
            ->orderBy('category_id')
            ->get();

        $hierarchical = [];
        $this->buildTree($categories, $hierarchical, null, 0);

        return $hierarchical;
    }

    /**
     * Recursively build category tree
     */
    protected function buildTree($categories, &$result, $parentId, $level): void
    {
        $locale = app()->getLocale();

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $prefix = str_repeat('-', $level);
                if ($prefix) {
                    $prefix .= ' ';
                }

                $title = $category->getTranslated('title', $locale)
                    ?? $category->title['tr']
                    ?? $category->title[array_key_first($category->title)]
                    ?? 'Untitled';

                $result[] = [
                    'category_id' => $category->category_id,
                    'title' => $prefix . $title,
                    'level' => $level,
                    'parent_id' => $category->parent_id,
                ];

                // Recursively add children
                $this->buildTree($categories, $result, $category->category_id, $level + 1);
            }
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->perPage = max(1, (int) $this->perPage);
        $this->resetPage();
    }

    public function updatedSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function clearCategoryFilter(): void
    {
        $this->selectedCategory = null;
        $this->resetPage();
    }

    public function updateSortOrder(array $orderedIds): void
    {
        try {
            foreach ($orderedIds as $index => $productId) {
                ShopProduct::where('product_id', $productId)->update([
                    'sort_order' => $index + 1
                ]);
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('shop::admin.sort_order_updated'),
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function refreshPageData(): void
    {
        $this->productService->clearCache();
        $this->resetPage();
    }

    public function toggleActive(int $productId): void
    {
        $result = $this->productService->toggleProductStatus($productId);
        $this->emitResultToast($result);
    }

    public function toggleHomepage(int $productId): void
    {
        try {
            $product = ShopProduct::findOrFail($productId);
            $product->show_on_homepage = !$product->show_on_homepage;
            $product->save();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => $product->show_on_homepage
                    ? __('shop::admin.product_added_to_homepage')
                    : __('shop::admin.product_removed_from_homepage'),
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function startEditingPrice(int $productId, ?string $currentPrice, ?string $currentCurrency, bool $priceOnRequest = false): void
    {
        $this->editingPriceId = $productId;
        $this->newPrice = $currentPrice;
        $this->newCurrency = $currentCurrency ?? 'TRY';
        $this->newPriceOnRequest = $priceOnRequest;
    }

    public function updatePriceInline(): void
    {
        if (! $this->editingPriceId) {
            return;
        }

        try {
            $product = ShopProduct::findOrFail($this->editingPriceId);

            // Update price_on_request
            $product->price_on_request = $this->newPriceOnRequest;

            if (! $this->newPriceOnRequest) {
                // Validate price only if not price_on_request
                $price = $this->newPrice ? (float) str_replace(',', '.', $this->newPrice) : null;

                if ($price !== null && $price < 0) {
                    $this->dispatch('toast', [
                        'title' => __('admin.error'),
                        'message' => __('shop::admin.invalid_price'),
                        'type' => 'error',
                    ]);
                    return;
                }

                $product->base_price = $price;
                $product->currency = $this->newCurrency ?? 'TRY';
            } else {
                // If price_on_request, clear price
                $product->base_price = null;
            }

            $product->save();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('shop::admin.price_updated'),
                'type' => 'success',
            ]);

            $this->editingPriceId = null;
            $this->newPrice = null;
            $this->newCurrency = null;
            $this->newPriceOnRequest = false;
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function cancelPriceEdit(): void
    {
        $this->editingPriceId = null;
        $this->newPrice = null;
        $this->newCurrency = null;
        $this->newPriceOnRequest = false;
    }

    public function bulkDeleteSelected(): void
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('toast', [
                'title' => __('admin.warning'),
                'message' => __('shop::admin.select_records_first'),
                'type' => 'warning',
            ]);

            return;
        }

        $deleted = $this->productService->bulkDeleteProducts(array_map('intval', $this->selectedItems));

        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => $result->message,
            'type' => 'success',
        ]);

        $this->refreshSelectedItems();
        $this->resetPage();
    }

    public function bulkToggleSelected(bool $status): void
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('toast', [
                'title' => __('admin.warning'),
                'message' => __('shop::admin.select_records_first'),
                'type' => 'warning',
            ]);

            return;
        }

        $affected = $this->productService->bulkToggleProductStatus(array_map('intval', $this->selectedItems));

        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => $result->message,
            'type' => $status ? 'success' : 'info',
        ]);

        $this->refreshSelectedItems();
        $this->resetPage();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $filters = [
            'search' => $this->search,
            'locales' => $this->availableSiteLanguages,
            'sortField' => $this->selectedCategory ? 'sort_order' : $this->sortField,
            'sortDirection' => $this->selectedCategory ? 'asc' : $this->sortDirection,
            'currentLocale' => app()->getLocale(),
        ];

        // Add category filter if selected
        if ($this->selectedCategory) {
            $filters['category_id'] = $this->selectedCategory;
        }

        $products = $this->productService->getPaginatedProducts($filters, (int) $this->perPage);

        return view('shop::admin.livewire.product-component', [
            'products' => $products,
            'siteLanguages' => $this->availableSiteLanguages,
            'currentSiteLocale' => app()->getLocale(),
            'categories' => $this->categories,
        ]);
    }

    private function emitResultToast(ShopOperationResult $result): void
    {
        $this->dispatch('toast', [
            'title' => $result->success ? __('admin.success') : __('admin.' . $result->type),
            'message' => $result->message,
            'type' => $result->type,
        ]);

        if ($result->success) {
            $this->resetPage();
        }
    }
}
