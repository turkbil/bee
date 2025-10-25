<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\Shop\App\Models\ShopProduct;

#[Layout('admin.layout')]
class HomepageProductsComponent extends Component
{
    public array $products = [];
    public array $sortOrders = [];

    public function mount(): void
    {
        $this->loadProducts();
    }

    public function loadProducts(): void
    {
        $locale = app()->getLocale();

        $this->products = ShopProduct::query()
            ->where('show_on_homepage', true)
            ->with(['category', 'brand'])
            ->orderBy('homepage_sort_order', 'asc')
            ->orderBy('product_id', 'desc')
            ->get()
            ->map(function ($product) use ($locale) {
                return [
                    'product_id' => $product->product_id,
                    'title' => $product->getTranslated('title', $locale) ?? ($product->title[$locale] ?? 'Başlık yok'),
                    'sku' => $product->sku,
                    'category_name' => $product->category
                        ? ($product->category->getTranslated('title', $locale) ?? ($product->category->title[$locale] ?? '-'))
                        : '-',
                    'homepage_sort_order' => $product->homepage_sort_order,
                ];
            })
            ->toArray();

        // Initialize sort orders
        foreach ($this->products as $product) {
            $this->sortOrders[$product['product_id']] = $product['homepage_sort_order'] ?? '';
        }
    }

    public function saveSortOrders(): void
    {
        try {
            foreach ($this->sortOrders as $productId => $sortOrder) {
                ShopProduct::where('product_id', $productId)
                    ->update([
                        'homepage_sort_order' => $sortOrder === '' ? null : (int) $sortOrder,
                    ]);
            }

            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Sıralama başarıyla kaydedildi!',
            ]);

            $this->loadProducts();
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Hata: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('shop::admin.livewire.homepage-products-component');
    }
}
