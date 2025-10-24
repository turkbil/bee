<div class="w-full bg-white dark:bg-gray-800 rounded-2xl overflow-hidden">
    <div class="bg-gradient-to-br from-gray-50 to-slate-100 dark:from-gray-800 dark:to-gray-900 rounded-xl p-6">
        <div class="grid grid-cols-12 gap-6">

            {{-- ========================================== --}}
            {{-- SOL: DİĞER 6 KATEGORİ + HİZMETLER (col-span-7) --}}
            {{-- ========================================== --}}
            <div class="col-span-7 space-y-4">
                {{-- Kategoriler Grid --}}
                <div class="grid grid-cols-2 gap-4">
                    @foreach($mainCategories as $category)
                        @if($category->category_id != 7) {{-- Yedek Parça hariç --}}
                            @php
                                $config = $categoryConfigs[$category->category_id] ?? $categoryConfigs[1];
                                $categoryTitle = is_array($category->title) ? $category->title['tr'] : $category->title;
                                $categorySlug = is_array($category->slug) ? $category->slug['tr'] : $category->slug;
                            @endphp

                            <a href="/shop/kategori/{{ $categorySlug }}"
                               class="bg-white dark:bg-gray-800 rounded-xl p-4 transition-all border-2 border-gray-200 dark:border-gray-700 {{ $config['hoverBorder'] }} group">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br {{ $config['gradient'] }} rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                        @if($category->icon_class)
                                            <i class="{{ $category->icon_class }} text-white text-xl"></i>
                                        @else
                                            <i class="fa-solid fa-box text-white text-xl"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-black text-gray-900 dark:text-white {{ $config['hoverText'] }} transition-colors">{{ $categoryTitle }}</h4>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>

                {{-- Hizmetler Bölümü --}}
                <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-indigo-900/20 dark:via-purple-900/20 dark:to-pink-900/20 rounded-2xl p-6 border-2 border-indigo-200 dark:border-indigo-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-briefcase text-white text-lg"></i>
                            </div>
                            <span>Hizmetlerimiz</span>
                        </h3>
                        <a href="/shop" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 flex items-center gap-1">
                            Tümü
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-4 gap-3">
                        {{-- Sıfır Ürün --}}
                        <a href="/shop?durum=sifir"
                           class="group relative bg-white dark:bg-gray-800 rounded-xl p-4 transition-all border-2 border-green-200 dark:border-green-700 hover:border-green-400 dark:hover:border-green-500 hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl opacity-0 group-hover:opacity-10 transition-opacity"></div>
                            <div class="relative text-center">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-badge-check text-white text-xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">Sıfır Ürün</p>
                            </div>
                        </a>

                        {{-- İkinci El --}}
                        <a href="/shop?durum=ikinci-el"
                           class="group relative bg-white dark:bg-gray-800 rounded-xl p-4 transition-all border-2 border-blue-200 dark:border-blue-700 hover:border-blue-400 dark:hover:border-blue-500 hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl opacity-0 group-hover:opacity-10 transition-opacity"></div>
                            <div class="relative text-center">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-recycle text-white text-xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">İkinci El</p>
                            </div>
                        </a>

                        {{-- Kiralama --}}
                        <a href="/shop?hizmet=kiralama"
                           class="group relative bg-white dark:bg-gray-800 rounded-xl p-4 transition-all border-2 border-purple-200 dark:border-purple-700 hover:border-purple-400 dark:hover:border-purple-500 hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl opacity-0 group-hover:opacity-10 transition-opacity"></div>
                            <div class="relative text-center">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-calendar-days text-white text-xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">Kiralama</p>
                            </div>
                        </a>

                        {{-- Teknik Servis --}}
                        <a href="/shop?hizmet=servis"
                           class="group relative bg-white dark:bg-gray-800 rounded-xl p-4 transition-all border-2 border-orange-200 dark:border-orange-700 hover:border-orange-400 dark:hover:border-orange-500 hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl opacity-0 group-hover:opacity-10 transition-opacity"></div>
                            <div class="relative text-center">
                                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-screwdriver-wrench text-white text-xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">Teknik Servis</p>
                            </div>
                        </a>
                    </div>
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

                <div class="bg-gradient-to-br from-red-500 to-rose-600 dark:from-red-600 dark:to-rose-700 rounded-2xl p-6 text-white relative overflow-hidden h-full">
                    {{-- Animated Background --}}
                    <div class="absolute inset-0 opacity-20">
                        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxjaXJjbGUgZmlsbD0iI2ZmZiIgY3g9IjIwIiBjeT0iMjAiIHI9IjMiLz48L2c+PC9zdmc+')] animate-pulse"></div>
                    </div>

                    <div class="relative z-10">
                        {{-- Header --}}
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center float-animation">
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
                                   class="bg-white/20 backdrop-blur-md rounded-lg p-3 hover:bg-white/30 transition-all cursor-pointer border border-white/20">
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
