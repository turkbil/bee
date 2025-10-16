@php
    // Shop kategorilerini çek - Parent kategoriler (parent_id = null)
    try {
        $shopCategories = \Modules\Shop\app\Models\ShopCategory::with(['media', 'children'])
            ->whereNull('parent_id')
            ->where('is_active', 1)
            ->orderBy('order_column')
            ->take(6)
            ->get();
    } catch (\Exception $e) {
        $shopCategories = collect();
    }

    // Renkler
    $categoryColors = [
        ['bg' => 'blue', 'icon' => 'blue'],
        ['bg' => 'green', 'icon' => 'green'],
        ['bg' => 'purple', 'icon' => 'purple'],
        ['bg' => 'orange', 'icon' => 'orange'],
        ['bg' => 'red', 'icon' => 'red'],
        ['bg' => 'pink', 'icon' => 'pink'],
    ];
@endphp

<div class="grid grid-cols-12 gap-8">
    {{-- Category Sidebar --}}
    <div class="col-span-12 lg:col-span-3 border-r border-gray-100 dark:border-gray-700 pr-6">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-layer-group text-blue-600 dark:text-blue-400"></i>
            Kategoriler
        </h3>
        <div class="space-y-1">
            @if($shopCategories->isNotEmpty())
                @foreach($shopCategories as $index => $category)
                    @php
                        $color = $categoryColors[$index % count($categoryColors)];
                        $categoryKey = 'cat_' . $category->id;
                    @endphp
                    <button @mouseenter="activeCategory = '{{ $categoryKey }}'"
                            :class="activeCategory === '{{ $categoryKey }}' ? 'bg-gradient-to-r from-{{ $color['bg'] }}-50 to-{{ $color['bg'] }}-50 text-{{ $color['bg'] }}-600 dark:from-{{ $color['bg'] }}-900/30 dark:to-{{ $color['bg'] }}-900/30 dark:text-{{ $color['bg'] }}-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                            class="w-full text-left px-4 py-3 rounded-xl transition-all duration-200 flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-lg bg-{{ $color['bg'] }}-100 dark:bg-{{ $color['bg'] }}-900/50 flex items-center justify-center group-hover:scale-110 transition-transform">
                            @if($category->icon)
                                <i class="{{ $category->icon }} text-{{ $color['icon'] }}-600 dark:text-{{ $color['icon'] }}-400 text-lg"></i>
                            @else
                                <i class="fa-solid fa-boxes-stacked text-{{ $color['icon'] }}-600 dark:text-{{ $color['icon'] }}-400 text-lg"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <span class="font-semibold block">{{ $category->name }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-500">{{ $category->products_count ?? 0 }} ürün</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-sm opacity-0 group-hover:opacity-100 transition"></i>
                    </button>
                @endforeach
            @else
                {{-- Fallback kategoriler --}}
                <button @mouseenter="activeCategory = 'forklift'"
                        :class="activeCategory === 'forklift' ? 'bg-gradient-to-r from-blue-50 to-purple-50 text-blue-600 dark:from-blue-900/30 dark:to-purple-900/30 dark:text-blue-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                        class="w-full text-left px-4 py-3 rounded-xl transition-all duration-200 flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-warehouse text-blue-600 dark:text-blue-400 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <span class="font-semibold block">Forklift</span>
                        <span class="text-xs text-gray-500">Tüm modeller</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-sm opacity-0 group-hover:opacity-100 transition"></i>
                </button>

                <button @mouseenter="activeCategory = 'transpalet'"
                        :class="activeCategory === 'transpalet' ? 'bg-gradient-to-r from-green-50 to-emerald-50 text-green-600 dark:from-green-900/30 dark:to-emerald-900/30 dark:text-green-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                        class="w-full text-left px-4 py-3 rounded-xl transition-all duration-200 flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/50 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-dolly text-green-600 dark:text-green-400 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <span class="font-semibold block">Transpalet</span>
                        <span class="text-xs text-gray-500">El & Akülü</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-sm opacity-0 group-hover:opacity-100 transition"></i>
                </button>
            @endif
        </div>

        {{-- View All Categories Button --}}
        <a href="{{ route('shop.index') }}" class="mt-4 w-full bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition-all flex items-center justify-center">
            <i class="fa-solid fa-grid-2 mr-2"></i>
            Tüm Kategoriler
        </a>
    </div>

    {{-- Products Display Area --}}
    <div class="col-span-12 lg:col-span-9">
        {{-- Grid overlay system for tab contents --}}
        <div style="display: grid;">
            @if($shopCategories->isNotEmpty())
                @foreach($shopCategories as $index => $category)
                    @php
                        $categoryKey = 'cat_' . $category->id;
                        $color = $categoryColors[$index % count($categoryColors)];

                        // Alt kategorileri veya ürünleri al
                        $subCategories = $category->children->take(4);
                        if ($subCategories->isEmpty()) {
                            $subCategories = \Modules\Shop\app\Models\ShopCategory::where('parent_id', $category->id)
                                ->where('is_active', 1)
                                ->orderBy('order_column')
                                ->take(4)
                                ->get();
                        }
                    @endphp

                    <div x-show="activeCategory === '{{ $categoryKey }}'"
                         x-transition:leave="transition-opacity ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         style="grid-area: 1/1; display: none;"
                         :style="activeCategory === '{{ $categoryKey }}' ? 'display: block; grid-area: 1/1;' : 'display: none;'"
                         class="bg-white dark:bg-gray-800 rounded-2xl p-6"
                         x-cloak>
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $category->name }}</h4>
                        <a href="{{ route('shop.category', $category->slug) }}" class="text-{{ $color['bg'] }}-600 dark:text-{{ $color['bg'] }}-400 hover:text-{{ $color['bg'] }}-700 dark:hover:text-{{ $color['bg'] }}-300 font-semibold flex items-center gap-2">
                            Tümünü Gör
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @if($subCategories->isNotEmpty())
                            @foreach($subCategories as $subIndex => $subCategory)
                                @php
                                    $subColor = $categoryColors[($index + $subIndex) % count($categoryColors)];
                                @endphp
                                <a href="{{ route('shop.category', $subCategory->slug) }}" class="group">
                                    <div class="bg-gradient-to-br from-{{ $subColor['bg'] }}-50 to-{{ $subColor['bg'] }}-100 dark:from-{{ $subColor['bg'] }}-900/30 dark:to-{{ $subColor['bg'] }}-900/20 rounded-2xl p-8 group-hover:shadow-xl transition-all duration-300 h-48 flex items-center justify-center mb-4 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-br from-{{ $subColor['bg'] }}-500 to-{{ $subColor['bg'] }}-600 opacity-0 group-hover:opacity-10 transition-opacity"></div>
                                        @if($subCategory->getFirstMediaUrl('category_image'))
                                            <img src="{{ $subCategory->getFirstMediaUrl('category_image') }}" alt="{{ $subCategory->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-all duration-300">
                                        @else
                                            <i class="fa-solid {{ $subCategory->icon ?? 'fa-box' }} text-7xl text-{{ $subColor['icon'] }}-400 dark:text-{{ $subColor['icon'] }}-500 group-hover:text-{{ $subColor['icon'] }}-600 dark:group-hover:text-{{ $subColor['icon'] }}-400 group-hover:scale-110 transition-all duration-300"></i>
                                        @endif
                                    </div>
                                    <h5 class="font-bold text-gray-800 dark:text-gray-200 group-hover:text-{{ $subColor['bg'] }}-600 dark:group-hover:text-{{ $subColor['bg'] }}-400 transition text-lg mb-1">{{ $subCategory->name }}</h5>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $subCategory->products_count ?? 0 }}+ ürün</p>
                                    <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-tag"></i>
                                        <span>Özel fiyatlar</span>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            {{-- Kategori ürünlerini göster --}}
                            @php
                                $categoryProducts = \Modules\Shop\app\Models\ShopProduct::where('category_id', $category->id)
                                    ->where('is_active', 1)
                                    ->orderBy('order_column')
                                    ->take(4)
                                    ->get();
                            @endphp
                            @foreach($categoryProducts as $product)
                                <a href="{{ route('shop.show', $product->slug) }}" class="group">
                                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-8 group-hover:shadow-xl transition-all duration-300 h-48 flex items-center justify-center mb-4">
                                        @if($product->getFirstMediaUrl('product_image'))
                                            <img src="{{ $product->getFirstMediaUrl('product_image') }}" alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-all">
                                        @else
                                            <i class="fa-solid fa-box text-7xl text-gray-400 dark:text-gray-500"></i>
                                        @endif
                                    </div>
                                    <h5 class="font-bold text-gray-800 dark:text-gray-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">{{ $product->name }}</h5>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->price ? number_format($product->price, 2) . ' ₺' : 'Fiyat sorunuz' }}</p>
                                </a>
                            @endforeach
                        @endif
                    </div>

                    {{-- Featured Banner --}}
                    @if($category->description)
                        <div class="mt-6 bg-gradient-to-r from-{{ $color['bg'] }}-500 to-{{ $color['bg'] }}-600 dark:from-{{ $color['bg'] }}-600 dark:to-{{ $color['bg'] }}-700 rounded-2xl p-6 text-white flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex-1">
                                <h5 class="text-xl font-bold mb-1">{{ $category->name }} - Özel Kampanya!</h5>
                                <p class="text-{{ $color['bg'] }}-100 dark:text-{{ $color['bg'] }}-200">{{ Str::limit($category->description, 100) }}</p>
                            </div>
                            <a href="{{ route('shop.category', $category->slug) }}" class="bg-white text-{{ $color['bg'] }}-600 dark:text-{{ $color['bg'] }}-700 px-6 py-3 rounded-xl font-bold hover:shadow-xl transition whitespace-nowrap">
                                Kampanyayı Gör
                            </a>
                        </div>
                    @endif
                </div>
                @endforeach
            @else
                {{-- Fallback content --}}
                <div x-show="activeCategory === 'forklift'"
                     x-transition:leave="transition-opacity ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     style="grid-area: 1/1; display: none;"
                     :style="activeCategory === 'forklift' ? 'display: block; grid-area: 1/1;' : 'display: none;'"
                     class="bg-white dark:bg-gray-800 rounded-2xl p-6"
                     x-cloak>
                    <h4 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">Forklift Ürünleri</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="text-center p-8 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <i class="fa-solid fa-warehouse text-6xl text-blue-500 dark:text-blue-400 mb-4"></i>
                            <h5 class="font-bold text-gray-800 dark:text-gray-200">Forklift Kategorileri</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Yakında eklenecek</p>
                        </div>
                    </div>
                </div>

                <div x-show="activeCategory === 'transpalet'"
                     x-transition:leave="transition-opacity ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     style="grid-area: 1/1; display: none;"
                     :style="activeCategory === 'transpalet' ? 'display: block; grid-area: 1/1;' : 'display: none;'"
                     class="bg-white dark:bg-gray-800 rounded-2xl p-6"
                     x-cloak>
                    <h4 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">Transpalet Ürünleri</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="text-center p-8 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <i class="fa-solid fa-dolly text-6xl text-green-500 dark:text-green-400 mb-4"></i>
                            <h5 class="font-bold text-gray-800 dark:text-gray-200">Transpalet Kategorileri</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Yakında eklenecek</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
