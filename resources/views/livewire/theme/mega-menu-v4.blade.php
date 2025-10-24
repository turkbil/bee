<div class="w-full bg-white shadow-2xl rounded-2xl overflow-hidden">
    <div class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-6">
        <div class="grid grid-cols-12 gap-6">

            {{-- ========================================== --}}
            {{-- SOL: DİĞER 6 KATEGORİ (col-span-7) --}}
            {{-- ========================================== --}}
            <div class="col-span-7">
                <div class="grid grid-cols-2 gap-4 h-full">

                    @foreach($mainCategories as $category)
                        @if($category->category_id != 7) {{-- Yedek Parça hariç --}}
                            @php
                                $config = $categoryConfigs[$category->category_id] ?? $categoryConfigs[1];
                                $categoryTitle = is_array($category->title) ? $category->title['tr'] : $category->title;
                                $categorySlug = is_array($category->slug) ? $category->slug['tr'] : $category->slug;
                            @endphp

                            <a href="/shop/kategori/{{ $categorySlug }}"
                               class="bg-white rounded-xl p-4 shadow-lg hover:shadow-xl transition-all border-2 border-transparent {{ $config['hoverBorder'] }} group">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-12 h-12 bg-gradient-to-br {{ $config['gradient'] }} rounded-lg flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                                        @if($category->icon_class)
                                            <i class="{{ $category->icon_class }} text-white text-xl"></i>
                                        @else
                                            <i class="fa-solid fa-box text-white text-xl"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-black text-gray-900 {{ $config['hoverText'] }} transition-colors">{{ $categoryTitle }}</h4>
                                    </div>
                                </div>

                                @php
                                    // Alt kategorileri veya ürünleri çek (ilk 3 tanesi)
                                    $subcategories = \Modules\Shop\app\Models\ShopCategory::where('parent_id', $category->category_id)
                                        ->where('is_active', 1)
                                        ->orderBy('sort_order', 'asc')
                                        ->take(3)
                                        ->get();

                                    // Eğer alt kategori yoksa, ürünleri göster
                                    if($subcategories->isEmpty()) {
                                        $products = \Modules\Shop\app\Models\ShopProduct::where('category_id', $category->category_id)
                                            ->where('is_active', 1)
                                            ->whereNull('parent_product_id')
                                            ->orderBy('sort_order', 'asc')
                                            ->take(3)
                                            ->get();
                                    }
                                @endphp

                                <ul class="space-y-1.5 text-xs">
                                    @if(!$subcategories->isEmpty())
                                        @foreach($subcategories as $subcat)
                                            @php
                                                $subTitle = is_array($subcat->title) ? $subcat->title['tr'] : $subcat->title;
                                            @endphp
                                            <li class="text-gray-700 {{ $config['hoverText'] }} cursor-pointer">• {{ $subTitle }}</li>
                                        @endforeach
                                    @elseif(isset($products) && !$products->isEmpty())
                                        @foreach($products as $product)
                                            @php
                                                $productTitle = is_array($product->title) ? $product->title['tr'] : $product->title;
                                            @endphp
                                            <li class="text-gray-700 {{ $config['hoverText'] }} cursor-pointer">• {{ $productTitle }}</li>
                                        @endforeach
                                    @endif
                                </ul>
                            </a>
                        @endif
                    @endforeach

                </div>
            </div>

            {{-- ========================================== --}}
            {{-- SAĞ: YEDEK PARÇA FEATURED (col-span-5) --}}
            {{-- ========================================== --}}
            <div class="col-span-5">
                @php
                    $yedekParcaCategory = $mainCategories->firstWhere('category_id', 7);
                    $yedekParcaTitle = $yedekParcaCategory ? (is_array($yedekParcaCategory->title) ? $yedekParcaCategory->title['tr'] : $yedekParcaCategory->title) : 'Yedek Parça';
                    $yedekParcaIcon = $yedekParcaCategory && $yedekParcaCategory->icon_class ? $yedekParcaCategory->icon_class : 'fa-solid fa-wrench';
                @endphp

                <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl p-6 text-white shadow-2xl relative overflow-hidden h-full">
                    {{-- Animated Background --}}
                    <div class="absolute inset-0 opacity-20">
                        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxjaXJjbGUgZmlsbD0iI2ZmZiIgY3g9IjIwIiBjeT0iMjAiIHI9IjMiLz48L2c+PC9zdmc+')] animate-pulse"></div>
                    </div>

                    <div class="relative z-10">
                        {{-- Header --}}
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-2xl float-animation">
                                <i class="{{ $yedekParcaIcon }} text-white text-4xl"></i>
                            </div>
                            <div>
                                <h3 class="text-3xl font-black mb-1">{{ $yedekParcaTitle }}</h3>
                                <div class="text-white/90 text-sm font-semibold">{{ $yedekParcaSubcategories->count() }} Ana Kategori</div>
                            </div>
                        </div>

                        {{-- Subcategories Grid --}}
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($yedekParcaSubcategories as $subcategory)
                                @php
                                    $subTitle = is_array($subcategory->title) ? $subcategory->title['tr'] : $subcategory->title;
                                    $subSlug = is_array($subcategory->slug) ? $subcategory->slug['tr'] : $subcategory->slug;
                                @endphp
                                <a href="/shop/kategori/{{ $subSlug }}"
                                   class="bg-white/20 backdrop-blur-md rounded-lg p-3 hover:bg-white/30 transition-all cursor-pointer">
                                    <div class="font-bold text-sm mb-1">{{ $subTitle }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    .float-animation {
        animation: float 3s ease-in-out infinite;
    }
    </style>
</div>
