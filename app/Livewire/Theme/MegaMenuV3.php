<?php

namespace App\Livewire\Theme;

use Livewire\Component;
use Modules\Shop\app\Models\ShopProduct;
use Modules\Shop\app\Models\ShopCategory;

class MegaMenuV3 extends Component
{
    public $categoryId;
    public $search = '';

    public function mount($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function render()
    {
        // Ana kategoriyi çek
        $category = ShopCategory::where('id', $this->categoryId)
            ->where('is_active', 1)
            ->first();

        // Alt kategorileri çek
        $subCategories = collect();
        if ($category) {
            $subCategories = ShopCategory::where('parent_id', $category->id)
                ->where('is_active', 1)
                ->orderBy('order_column')
                ->take(8)
                ->get();
        }

        // Ürünleri çek (search varsa filtrele)
        $productsQuery = ShopProduct::where('category_id', $this->categoryId)
            ->where('is_active', 1);

        if (!empty($this->search)) {
            $productsQuery->where(function($q) {
                $q->where('title->tr', 'like', '%' . $this->search . '%')
                  ->orWhere('title->en', 'like', '%' . $this->search . '%')
                  ->orWhere('short_description->tr', 'like', '%' . $this->search . '%');
            });
        }

        $products = $productsQuery->latest()->take(5)->get();

        // Eğer search sonucu yoksa, en yeni 5 ürünü göster
        if ($products->isEmpty() && empty($this->search)) {
            $products = ShopProduct::where('category_id', $this->categoryId)
                ->where('is_active', 1)
                ->latest()
                ->take(5)
                ->get();
        }

        return view('livewire.theme.mega-menu-v3', [
            'category' => $category,
            'subCategories' => $subCategories,
            'products' => $products,
        ]);
    }
}
