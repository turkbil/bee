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

                <a href="{{ href('Shop', 'category', $categorySlug) }}"
                   class="flex items-center gap-3 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-xl p-4 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200">

                    {{-- Icon --}}
                    <div class="w-12 h-12 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-white/20">
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
                    <a href="{{ href('Shop', 'index') }}" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-lg px-3 py-2 text-center hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 block">
                        <i class="fa-solid fa-badge-check text-gray-700 dark:text-white text-sm mb-1"></i>
                        <p class="text-xs font-bold text-gray-900 dark:text-white">Sıfır Ürün</p>
                    </a>
                    <a href="{{ href('Shop', 'index') }}" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-lg px-3 py-2 text-center hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 block">
                        <i class="fa-solid fa-recycle text-gray-700 dark:text-white text-sm mb-1"></i>
                        <p class="text-xs font-bold text-gray-900 dark:text-white">İkinci El</p>
                    </a>
                    <a href="{{ href('Page', 'show', 'iletisim') }}" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-lg px-3 py-2 text-center hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 block">
                        <i class="fa-solid fa-calendar-days text-gray-700 dark:text-white text-sm mb-1"></i>
                        <p class="text-xs font-bold text-gray-900 dark:text-white">Kiralama</p>
                    </a>
                    <a href="{{ href('Page', 'show', 'iletisim') }}" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-lg px-3 py-2 text-center hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 block">
                        <i class="fa-solid fa-screwdriver-wrench text-gray-700 dark:text-white text-sm mb-1"></i>
                        <p class="text-xs font-bold text-gray-900 dark:text-white">Teknik Servis</p>
                    </a>
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

                            <a href="{{ href('Shop', 'category', $categorySlug) }}"
                               class="group relative bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-2xl p-6 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 overflow-hidden">

                                <div class="relative z-10 flex items-center gap-4">
                                    {{-- Icon Container --}}
                                    <div class="relative">
                                        <div class="w-20 h-20 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-gray-200 dark:border-white/20">
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

                {{-- Hizmetler - Responsive Grid --}}
                <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
                    {{-- Sıfır Ürün --}}
                    <a href="{{ href('Shop', 'index') }}" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-xl px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 block">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-white/20">
                                <i class="fa-solid fa-badge-check text-gray-700 dark:text-white text-lg"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Sıfır Ürün</p>
                        </div>
                    </a>

                    {{-- İkinci El --}}
                    <a href="{{ href('Shop', 'index') }}" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-xl px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 block">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-white/20">
                                <i class="fa-solid fa-recycle text-gray-700 dark:text-white text-lg"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">İkinci El</p>
                        </div>
                    </a>

                    {{-- Kiralama --}}
                    <a href="{{ href('Page', 'show', 'iletisim') }}" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-xl px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 block">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-white/20">
                                <i class="fa-solid fa-calendar-days text-gray-700 dark:text-white text-lg"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Kiralama</p>
                        </div>
                    </a>

                    {{-- Teknik Servis --}}
                    <a href="{{ href('Page', 'show', 'iletisim') }}" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-xl px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 block">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-white/20">
                                <i class="fa-solid fa-screwdriver-wrench text-gray-700 dark:text-white text-lg"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Teknik Servis</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- SAĞ: YEDEK PARÇA LİST (col-span-5) --}}
            {{-- ========================================== --}}
            <div class="col-span-12 lg:col-span-5">
                @php
                    $yedekParcaCategory = $mainCategories->firstWhere('category_id', 7);
                    $yedekParcaTitle = $yedekParcaCategory ? (is_array($yedekParcaCategory->title) ? $yedekParcaCategory->title['tr'] : $yedekParcaCategory->title) : 'Yedek Parça';
                    $yedekParcaIcon = $yedekParcaCategory && $yedekParcaCategory->icon_class ? $yedekParcaCategory->icon_class : 'fa-solid fa-wrench';
                @endphp

                <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-2xl p-6">
                    {{-- Header --}}
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-200 dark:border-white/10">
                        <div class="w-12 h-12 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center border border-gray-200 dark:border-white/20">
                            <i class="{{ $yedekParcaIcon }} text-gray-700 dark:text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ $yedekParcaTitle }}</h3>
                    </div>

                    {{-- Subcategories 2-Column List --}}
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($yedekParcaSubcategories as $subcategory)
                            @php
                                $subTitle = is_array($subcategory->title) ? $subcategory->title['tr'] : $subcategory->title;
                                $subSlug = is_array($subcategory->slug) ? $subcategory->slug['tr'] : $subcategory->slug;
                            @endphp
                            <a href="{{ href('Shop', 'category', $subSlug) }}"
                               class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors py-1.5 px-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                <i class="fa-solid fa-chevron-right text-xs text-gray-400 dark:text-gray-500"></i>
                                <span class="text-sm font-medium">{{ $subTitle }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
