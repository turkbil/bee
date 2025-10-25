<div class="w-full rounded-2xl overflow-hidden border border-gray-300 dark:border-gray-700 shadow-lg">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6">
        <div class="grid grid-cols-12 gap-6">

            {{-- ========================================== --}}
            {{-- SOL: DİĞER 6 KATEGORİ + HİZMETLER (col-span-7) --}}
            {{-- ========================================== --}}
            <div class="col-span-12 lg:col-span-7 space-y-4">
                {{-- Kategoriler Grid --}}
                <div class="grid grid-cols-2 gap-3">
                    @foreach($mainCategories as $index => $category)
                        @if($category->category_id != 7) {{-- Yedek Parça hariç --}}
                            @php
                                $config = $categoryConfigs[$category->category_id] ?? $categoryConfigs[1];
                                $categoryTitle = is_array($category->title) ? $category->title['tr'] : $category->title;
                                $categorySlug = is_array($category->slug) ? $category->slug['tr'] : $category->slug;

                                // Renk paleti (MegaMenuV3'teki gibi)
                                $colors = [
                                    ['from' => 'blue', 'to' => 'cyan'],
                                    ['from' => 'green', 'to' => 'emerald'],
                                    ['from' => 'purple', 'to' => 'pink'],
                                    ['from' => 'orange', 'to' => 'red'],
                                    ['from' => 'pink', 'to' => 'rose'],
                                    ['from' => 'indigo', 'to' => 'purple'],
                                ];
                                $colorIndex = $index % count($colors);
                                $color = $colors[$colorIndex];
                            @endphp

                            <a href="/shop/kategori/{{ $categorySlug }}"
                               class="block bg-gray-50 dark:bg-gray-700 rounded-xl p-5 hover:bg-{{ $color['from'] }}-50 dark:hover:bg-{{ $color['from'] }}-900/20 transition-all duration-200 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 bg-gradient-to-br from-{{ $color['from'] }}-500 to-{{ $color['to'] }}-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                        @if($category->icon_class)
                                            <i class="{{ $category->icon_class }} text-white text-2xl"></i>
                                        @else
                                            <i class="fa-solid fa-box text-white text-2xl"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-bold text-gray-800 dark:text-gray-200 text-base truncate">
                                            {{ $categoryTitle }}
                                        </h5>
                                    </div>
                                    <div class="text-{{ $color['from'] }}-600 dark:text-{{ $color['from'] }}-400 flex-shrink-0">
                                        <i class="fa-solid fa-chevron-right text-sm"></i>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>

                {{-- Hizmetler - Info Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    {{-- Sıfır Ürün --}}
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-4 text-center border border-green-200 dark:border-green-700">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg mx-auto mb-2 flex items-center justify-center">
                            <i class="fa-solid fa-badge-check text-white text-xl"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200">Sıfır Ürün</p>
                    </div>

                    {{-- İkinci El --}}
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-4 text-center border border-blue-200 dark:border-blue-700">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg mx-auto mb-2 flex items-center justify-center">
                            <i class="fa-solid fa-recycle text-white text-xl"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200">İkinci El</p>
                    </div>

                    {{-- Kiralama --}}
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 text-center border border-purple-200 dark:border-purple-700">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg mx-auto mb-2 flex items-center justify-center">
                            <i class="fa-solid fa-calendar-days text-white text-xl"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200">Kiralama</p>
                    </div>

                    {{-- Teknik Servis --}}
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl p-4 text-center border border-orange-200 dark:border-orange-700">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg mx-auto mb-2 flex items-center justify-center">
                            <i class="fa-solid fa-screwdriver-wrench text-white text-xl"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200">Teknik Servis</p>
                    </div>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- SAĞ: YEDEK PARÇA FEATURED (col-span-5) --}}
            {{-- ========================================== --}}
            <div class="col-span-12 lg:col-span-5">
                @php
                    $yedekParcaCategory = $mainCategories->firstWhere('category_id', 7);
                    $yedekParcaTitle = $yedekParcaCategory ? (is_array($yedekParcaCategory->title) ? $yedekParcaCategory->title['tr'] : $yedekParcaCategory->title) : 'Yedek Parça';
                    $yedekParcaIcon = $yedekParcaCategory && $yedekParcaCategory->icon_class ? $yedekParcaCategory->icon_class : 'fa-solid fa-wrench';
                @endphp

                <div class="bg-gradient-to-br from-red-500 to-rose-600 dark:from-red-600 dark:to-rose-700 rounded-2xl p-6 text-white relative overflow-hidden h-full">
                    <div class="relative z-10">
                        {{-- Header --}}
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center">
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
</div>
