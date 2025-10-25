<div class="w-full rounded-2xl overflow-hidden border border-gray-300 dark:border-gray-700 shadow-lg">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6">

        {{-- ========================================== --}}
        {{-- MOBİL GÖRÜNÜM (lg altı) --}}
        {{-- ========================================== --}}
        <div class="lg:hidden space-y-3">
            {{-- Tüm Kategoriler (Yedek Parça dahil) --}}
            @foreach($mainCategories as $category)
                @php
                    $categoryTitle = is_array($category->title) ? $category->title['tr'] : $category->title;
                    $categorySlug = is_array($category->slug) ? $category->slug['tr'] : $category->slug;
                @endphp

                <a href="/shop/kategori/{{ $categorySlug }}"
                   class="flex items-center gap-3 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-xl p-4 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200">

                    {{-- Icon --}}
                    <div class="w-12 h-12 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center flex-shrink-0 border border-white/30 dark:border-white/20">
                        @if($category->icon_class)
                            <i class="{{ $category->icon_class }} text-gray-700 dark:text-white text-lg"></i>
                        @else
                            <i class="fa-solid fa-box text-gray-700 dark:text-white text-lg"></i>
                        @endif
                    </div>

                    {{-- Kategori Adı --}}
                    <div class="flex-1 min-w-0">
                        <h5 class="font-bold text-gray-900 dark:text-white text-base">
                            {{ $categoryTitle }}
                        </h5>
                    </div>

                    {{-- Chevron --}}
                    <div class="text-gray-500 dark:text-gray-400 flex-shrink-0">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </div>
                </a>
            @endforeach

            {{-- Hizmetler - Mobil --}}
            <div class="pt-2 space-y-2">
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide px-2">
                    Hizmetler
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-lg px-3 py-2 text-center hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 cursor-pointer">
                        <i class="fa-solid fa-badge-check text-gray-700 dark:text-white text-sm mb-1"></i>
                        <p class="text-xs font-bold text-gray-900 dark:text-white">Sıfır Ürün</p>
                    </div>
                    <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-lg px-3 py-2 text-center hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 cursor-pointer">
                        <i class="fa-solid fa-recycle text-gray-700 dark:text-white text-sm mb-1"></i>
                        <p class="text-xs font-bold text-gray-900 dark:text-white">İkinci El</p>
                    </div>
                    <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-lg px-3 py-2 text-center hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 cursor-pointer">
                        <i class="fa-solid fa-calendar-days text-gray-700 dark:text-white text-sm mb-1"></i>
                        <p class="text-xs font-bold text-gray-900 dark:text-white">Kiralama</p>
                    </div>
                    <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-lg px-3 py-2 text-center hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 cursor-pointer">
                        <i class="fa-solid fa-screwdriver-wrench text-gray-700 dark:text-white text-sm mb-1"></i>
                        <p class="text-xs font-bold text-gray-900 dark:text-white">Teknik Servis</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- DESKTOP GÖRÜNÜM (lg+) --}}
        {{-- ========================================== --}}
        <div class="hidden lg:grid grid-cols-12 gap-8">

            {{-- ========================================== --}}
            {{-- SOL: DİĞER 6 KATEGORİ + HİZMETLER (col-span-7) --}}
            {{-- ========================================== --}}
            <div class="col-span-12 lg:col-span-7 space-y-5">
                {{-- Kategoriler Grid - Yenilendi! --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($mainCategories as $index => $category)
                        @if($category->category_id != 7) {{-- Yedek Parça hariç --}}
                            @php
                                $config = $categoryConfigs[$category->category_id] ?? $categoryConfigs[1];
                                $categoryTitle = is_array($category->title) ? $category->title['tr'] : $category->title;
                                $categorySlug = is_array($category->slug) ? $category->slug['tr'] : $category->slug;
                            @endphp

                            <a href="/shop/kategori/{{ $categorySlug }}"
                               class="group relative bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-2xl p-6 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 overflow-hidden">

                                <div class="relative z-10 flex items-center gap-4">
                                    {{-- Icon Container --}}
                                    <div class="relative">
                                        <div class="w-20 h-20 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30 dark:border-white/20">
                                            @if($category->icon_class)
                                                <i class="{{ $category->icon_class }} text-gray-700 dark:text-white text-3xl"></i>
                                            @else
                                                <i class="fa-solid fa-box text-gray-700 dark:text-white text-3xl"></i>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Kategori Bilgisi --}}
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-black text-gray-900 dark:text-white text-2xl">
                                            {{ $categoryTitle }}
                                        </h5>
                                    </div>

                                    {{-- Arrow --}}
                                    <div class="text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white group-hover:translate-x-1 transition-all duration-300">
                                        <i class="fa-solid fa-arrow-right text-xl"></i>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>

                {{-- Hizmetler - Horizontal Badges --}}
                <div class="flex flex-wrap gap-3">
                    {{-- Sıfır Ürün --}}
                    <div class="flex-1 min-w-[140px] bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-xl px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center flex-shrink-0 border border-white/30 dark:border-white/20">
                                <i class="fa-solid fa-badge-check text-gray-700 dark:text-white text-lg"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Sıfır Ürün</p>
                        </div>
                    </div>

                    {{-- İkinci El --}}
                    <div class="flex-1 min-w-[140px] bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-xl px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center flex-shrink-0 border border-white/30 dark:border-white/20">
                                <i class="fa-solid fa-recycle text-gray-700 dark:text-white text-lg"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">İkinci El</p>
                        </div>
                    </div>

                    {{-- Kiralama --}}
                    <div class="flex-1 min-w-[140px] bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-xl px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center flex-shrink-0 border border-white/30 dark:border-white/20">
                                <i class="fa-solid fa-calendar-days text-gray-700 dark:text-white text-lg"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Kiralama</p>
                        </div>
                    </div>

                    {{-- Teknik Servis --}}
                    <div class="flex-1 min-w-[140px] bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-xl px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center flex-shrink-0 border border-white/30 dark:border-white/20">
                                <i class="fa-solid fa-screwdriver-wrench text-gray-700 dark:text-white text-lg"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Teknik Servis</p>
                        </div>
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

                <div class="bg-gradient-to-br from-gray-600 to-gray-700 dark:from-gray-700 dark:to-gray-800 rounded-2xl p-6 text-white relative overflow-hidden h-full">
                    <div class="relative z-10">
                        {{-- Header --}}
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center">
                                <i class="{{ $yedekParcaIcon }} text-white text-4xl"></i>
                            </div>
                            <div>
                                <h3 class="text-3xl font-black">{{ $yedekParcaTitle }}</h3>
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
