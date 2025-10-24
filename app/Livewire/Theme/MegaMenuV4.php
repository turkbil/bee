<?php

namespace App\Livewire\Theme;

use Livewire\Component;
use Modules\Shop\app\Models\ShopCategory;

class MegaMenuV4 extends Component
{
    public function render()
    {
        // Ana kategoriler (parent_id = NULL, id 1-7)
        $mainCategories = ShopCategory::whereNull('parent_id')
            ->where('is_active', 1)
            ->whereIn('category_id', [1, 2, 3, 4, 5, 6, 7]) // Forklift, Transpalet, İstif, Order Picker, Otonom, Reach, Yedek Parça
            ->orderBy('sort_order', 'asc')
            ->get();

        // Yedek Parça alt kategorileri (parent_id = 7)
        $yedekParcaSubcategories = ShopCategory::where('parent_id', 7)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'asc')
            ->get();

        // Her kategori için icon ve gradient config
        $categoryConfigs = $this->getCategoryConfigs();

        return view('livewire.theme.mega-menu-v4', [
            'mainCategories' => $mainCategories,
            'yedekParcaSubcategories' => $yedekParcaSubcategories,
            'categoryConfigs' => $categoryConfigs,
        ]);
    }

    private function getCategoryConfigs()
    {
        return [
            1 => [ // Forklift
                'gradient' => 'from-orange-500 to-red-600',
                'hoverBorder' => 'hover:border-orange-400',
                'hoverText' => 'hover:text-orange-600',
                'hoverBg' => 'hover:bg-orange-100',
            ],
            2 => [ // Transpalet
                'gradient' => 'from-blue-500 to-indigo-600',
                'hoverBorder' => 'hover:border-blue-400',
                'hoverText' => 'hover:text-blue-600',
                'hoverBg' => 'hover:bg-blue-100',
            ],
            3 => [ // İstif Makinesi
                'gradient' => 'from-green-500 to-emerald-600',
                'hoverBorder' => 'hover:border-green-400',
                'hoverText' => 'hover:text-green-600',
                'hoverBg' => 'hover:bg-green-100',
            ],
            4 => [ // Order Picker
                'gradient' => 'from-purple-500 to-pink-600',
                'hoverBorder' => 'hover:border-purple-400',
                'hoverText' => 'hover:text-purple-600',
                'hoverBg' => 'hover:bg-purple-100',
            ],
            5 => [ // Otonom Sistemler
                'gradient' => 'from-cyan-500 to-blue-600',
                'hoverBorder' => 'hover:border-cyan-400',
                'hoverText' => 'hover:text-cyan-600',
                'hoverBg' => 'hover:bg-cyan-100',
            ],
            6 => [ // Reach Truck
                'gradient' => 'from-amber-500 to-orange-600',
                'hoverBorder' => 'hover:border-amber-400',
                'hoverText' => 'hover:text-amber-600',
                'hoverBg' => 'hover:bg-amber-100',
            ],
            7 => [ // Yedek Parça
                'gradient' => 'from-red-500 to-rose-600',
                'hoverBorder' => 'hover:border-red-400',
                'hoverText' => 'hover:text-red-600',
                'hoverBg' => 'hover:bg-red-100',
            ],
        ];
    }
}
