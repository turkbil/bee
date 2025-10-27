<?php

namespace App\Livewire\Theme;

use Livewire\Component;
use Modules\Shop\app\Models\ShopProduct;
use Modules\Shop\app\Models\ShopCategory;

class MegaMenuV3 extends Component
{
    public $categoryId;

    public function mount($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function render()
    {
        // Ana kategoriyi çek
        $category = ShopCategory::where('category_id', $this->categoryId)
            ->where('is_active', 1)
            ->first();

        // Öne çıkan ürün: sort_order'a göre ilk ürün (sadece ana ürünler, varyant değil)
        $featuredProduct = ShopProduct::where('category_id', $this->categoryId)
            ->where('is_active', 1)
            ->whereNull('parent_product_id')
            ->orderBy('sort_order', 'asc')
            ->first();

        // Diğer ürünler: sort_order'a göre sıralı, 5 ürün (sadece ana ürünler, featured hariç)
        $otherProducts = ShopProduct::where('category_id', $this->categoryId)
            ->where('is_active', 1)
            ->whereNull('parent_product_id')
            ->where('product_id', '!=', $featuredProduct ? $featuredProduct->product_id : 0)
            ->orderBy('sort_order', 'asc')
            ->take(5)
            ->get();

        // Kategori özelleştirmeleri
        $categoryConfig = $this->getCategoryConfig($this->categoryId);

        return view('livewire.theme.mega-menu-v3', [
            'category' => $category,
            'featuredProduct' => $featuredProduct,
            'otherProducts' => $otherProducts,
            'config' => $categoryConfig,
        ]);
    }

    private function getCategoryConfig($categoryId)
    {
        $configs = [
            1 => [ // Forklift
                'gradient' => 'from-orange-600 via-red-600 to-pink-700',
                'title_suffix' => 'Çözümleri',
                'description' => 'Ağır yük taşıma ve depolama operasyonları için profesyonel forklift sistemleri',
            ],
            2 => [ // Transpalet
                'gradient' => 'from-blue-600 via-indigo-600 to-purple-700',
                'title_suffix' => 'Ekipmanları',
                'description' => 'Yük taşıma ve paletleme işlemleri için ergonomik transpalet çözümleri',
            ],
            3 => [ // İstif Makinesi
                'gradient' => 'from-green-600 via-emerald-600 to-teal-700',
                'title_suffix' => 'Sistemleri',
                'description' => 'Yüksek raflama ve istif operasyonları için güvenli istif makineleri',
            ],
        ];

        $config = $configs[$categoryId] ?? $configs[1];

        // Database'den kategori ikonunu al
        $category = ShopCategory::where('category_id', $categoryId)
            ->where('is_active', 1)
            ->first();

        $config['icon'] = $category && $category->icon_class
            ? $category->icon_class
            : 'fa-solid fa-box';

        return $config;
    }
}
