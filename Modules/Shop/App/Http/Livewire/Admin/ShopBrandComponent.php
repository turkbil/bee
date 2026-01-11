<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Url};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\Shop\App\Http\Livewire\Traits\{InlineEditTitle, WithBulkActions};
use Modules\Shop\App\Models\ShopBrand;
use Modules\Shop\App\Services\ShopBrandService;

#[Layout('admin.layout')]
class ShopBrandComponent extends Component
{
    use WithPagination;
    use WithBulkActions;
    use InlineEditTitle;

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $perPage = null;

    #[Url]
    public string $sortField = 'brand_id';

    #[Url]
    public string $sortDirection = 'asc';

    public array $availableLanguages = [];

    protected ShopBrandService $brandService;

    public function boot(ShopBrandService $brandService): void
    {
        $this->brandService = $brandService;
        $this->perPage ??= (int) config('modules.pagination.admin_per_page', 15);
        $this->availableLanguages = TenantLanguage::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
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

    protected function getModelClass(): string
    {
        return ShopBrand::class;
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

    public function toggleActive(int $brandId): void
    {
        $result = $this->brandService->toggleBrandStatus($brandId);

        $this->dispatch('toast', [
            'title' => $result['success'] ? __('admin.success') : __('admin.error'),
            'message' => $result['message'],
            'type' => $result['type'] ?? ($result['success'] ? 'success' : 'error'),
        ]);
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

        $deleted = $this->brandService->bulkDeleteBrands(array_map('intval', $this->selectedItems));

        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => trans_choice('shop::admin.brands_deleted', $deleted, ['count' => $deleted]),
            'type' => 'success',
        ]);

        $this->refreshSelectedItems();
        $this->resetPage();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $filters = [
            'search' => $this->search,
            'is_active' => null,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'locales' => $this->availableLanguages,
        ];

        $brands = $this->brandService->getPaginatedBrands($filters, (int) $this->perPage);

        return view('shop::admin.livewire.brand-component', [
            'brands' => $brands,
            'siteLanguages' => $this->availableLanguages,
        ]);
    }
}
