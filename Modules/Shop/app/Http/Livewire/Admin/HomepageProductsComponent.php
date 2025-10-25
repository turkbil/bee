<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\Shop\App\Models\ShopProduct;

#[Layout('admin.layout')]
class HomepageProductsComponent extends Component
{
    public $products;
    public string $currentSiteLocale;

    public function mount(): void
    {
        $this->currentSiteLocale = app()->getLocale();
        $this->loadProducts();
    }

    public function loadProducts(): void
    {
        $this->products = ShopProduct::query()
            ->where('show_on_homepage', true)
            ->with(['category', 'brand'])
            ->orderByRaw('COALESCE(homepage_sort_order, 999999) ASC')
            ->orderBy('product_id', 'desc')
            ->get();
    }

    /**
     * Drag-drop sıralama güncelleme
     */
    public function updateSortOrder(array $orderedIds): void
    {
        try {
            foreach ($orderedIds as $index => $productId) {
                ShopProduct::where('product_id', $productId)->update([
                    'homepage_sort_order' => $index + 1
                ]);
            }

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Anasayfa sıralaması güncellendi!',
                'type' => 'success',
            ]);

            $this->loadProducts();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Sıralama güncellenemedi: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function render()
    {
        return view('shop::admin.livewire.homepage-products-component');
    }
}
