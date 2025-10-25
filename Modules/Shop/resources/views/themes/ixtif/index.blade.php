@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <div class="relative" x-data="shopIndexPage()" x-init="init()">
        {{-- Gradient Background --}}
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-800 dark:via-slate-900 dark:to-slate-800 -z-10"></div>

        {{-- Header Section - Glassmorphism Subheader --}}
        @if($selectedCategory)
            {{-- Category Header with Glassmorphism Design --}}
            <section class="relative py-12 overflow-hidden">
                <div class="w-full">
                    <div class="container mx-auto px-4 max-w-7xl">
                        <div class="bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-3xl p-8 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.3)]">
                            <div class="grid lg:grid-cols-[1fr_400px] gap-8 items-stretch">
                                {{-- Left: Icon, Title & Breadcrumb --}}
                                <div class="flex flex-col justify-between">
                                    <div class="flex items-center gap-6">
                                        @if($selectedCategory->icon_class)
                                            <div class="w-24 h-24 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-xl flex-shrink-0">
                                                <i class="{{ $selectedCategory->icon_class }} text-5xl text-white"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h1 class="text-4xl md:text-5xl font-bold text-white mb-3">
                                                {{ $selectedCategory->getTranslated('title') }}
                                            </h1>
                                            {{-- Breadcrumb --}}
                                            <div class="flex items-center gap-2 text-sm text-white/90 flex-wrap">
                                                <a href="{{ route('shop.index') }}" class="hover:text-white transition flex items-center gap-1.5">
                                                    <i class="fa-solid fa-home text-xs"></i>
                                                    <span>Ana Sayfa</span>
                                                </a>
                                                <i class="fa-solid fa-chevron-right text-xs opacity-60"></i>
                                                <a href="{{ route('shop.index') }}" class="hover:text-white transition">Ürünler</a>
                                                <i class="fa-solid fa-chevron-right text-xs opacity-60"></i>
                                                <span class="font-semibold">{{ $selectedCategory->getTranslated('title') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Right: Search & Sort --}}
                                <div class="flex flex-col justify-center space-y-3">
                                    {{-- Search --}}
                                    <div class="relative">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <input type="search"
                                                   name="search"
                                                   value="{{ request('search') }}"
                                                   placeholder="Ara..."
                                                   class="w-full pl-10 pr-4 py-3 bg-white/10 backdrop-blur-sm rounded-xl text-white placeholder-white/60 text-sm
                                                          border border-white/20 focus:bg-white/20 focus:border-white/40 transition-all">
                                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-white/60"></i>
                                        </form>
                                    </div>

                                    {{-- Sort + View --}}
                                    <div class="flex items-center gap-2">
                                        {{-- Sort --}}
                                        <select class="flex-1 px-4 py-3 bg-white/10 backdrop-blur-sm rounded-xl text-white text-sm font-medium
                                                       border border-white/20 focus:bg-white/20 focus:border-white/40 transition-all cursor-pointer"
                                                onchange="window.location.href = '{{ url()->current() }}?sort=' + this.value + '{{ request('search') ? '&search=' . request('search') : '' }}'">
                                            <option class="text-gray-900 bg-white" value="" {{ !request('sort') ? 'selected' : '' }}>Editörün Seçimi</option>
                                            <option class="text-gray-900 bg-white" value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>A'dan Z'ye</option>
                                            <option class="text-gray-900 bg-white" value="z-a" {{ request('sort') == 'z-a' ? 'selected' : '' }}>Z'den A'ya</option>
                                        </select>

                                        {{-- View Toggle - Single Button with Animation --}}
                                        <button x-data="{ view: 'grid' }"
                                                @click="view = view === 'grid' ? 'list' : 'grid'"
                                                class="relative px-3 py-3 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20
                                                       hover:bg-white/20 active:scale-95 transition-all duration-300 overflow-hidden">
                                            <i class="fa-solid fa-grip text-white absolute inset-0 flex items-center justify-center transition-all duration-500"
                                               :class="view === 'grid' ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 -rotate-180 scale-50'"></i>
                                            <i class="fa-solid fa-list text-white absolute inset-0 flex items-center justify-center transition-all duration-500"
                                               :class="view === 'list' ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 rotate-180 scale-50'"></i>
                                            <i class="fa-solid fa-grip opacity-0"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @else
            {{-- Default Header for Shop Home / Search Results --}}
            <section class="relative py-16 md:py-20 overflow-hidden">
                <div class="container mx-auto px-4 sm:px-4 md:px-0">
                    <div class="text-center max-w-3xl mx-auto">
                        @if(request('search'))
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                                "{{ request('search') }}" Arama Sonuçları
                            </h1>
                        @else
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                                Ürünlerimiz
                            </h1>
                            <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                                Endüstriyel ekipmanlar ve çözümler
                            </p>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        {{-- Search Bar with Autocomplete --}}
        <section class="py-12 border-b border-gray-200 dark:border-white/10">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                <div class="max-w-5xl mx-auto">
                    <div x-data="{
                        query: '{{ request('search') }}',
                        selectedCategory: '{{ request('category') }}',
                        categoryDropdownOpen: false,
                        keywords: [],
                        products: [],
                        total: 0,
                        isOpen: false,
                        loading: false,
                        error: null,
                        get hasResults() {
                            return this.keywords.length > 0 || this.products.length > 0;
                        },
                        async search() {
                            const trimmed = this.query.trim();
                            if (trimmed.length < 2) {
                                this.keywords = [];
                                this.products = [];
                                this.total = 0;
                                this.isOpen = false;
                                return;
                            }
                            this.loading = true;
                            this.error = null;
                            try {
                                let url = `/api/search/suggestions?q=${encodeURIComponent(trimmed)}`;
                                // Kategori varsa API'ye ekle
                                if (this.selectedCategory) {
                                    url += `&category=${encodeURIComponent(this.selectedCategory)}`;
                                }
                                const response = await fetch(url, {
                                    headers: { 'Accept': 'application/json' }
                                });
                                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                                const data = await response.json();
                                if (data.success && data.data) {
                                    this.keywords = data.data.keywords || [];
                                    this.products = data.data.products || [];
                                    this.total = data.data.total || 0;
                                    this.isOpen = this.hasResults;
                                } else {
                                    this.keywords = [];
                                    this.products = [];
                                    this.total = 0;
                                }
                            } catch (e) {
                                console.error('Search error:', e);
                                this.error = 'Arama başarısız';
                            }
                            this.loading = false;
                        },
                        goToSearch() {
                            if (this.query.trim().length >= 1) {
                                // Kategori varsa kategori sayfasında ara, yoksa ana shop'ta ara
                                if (this.selectedCategory) {
                                    window.location.href = '/shop/kategori/' + encodeURIComponent(this.selectedCategory) + '?search=' + encodeURIComponent(this.query);
                                } else {
                                    window.location.href = '{{ route('shop.index') }}?search=' + encodeURIComponent(this.query);
                                }
                            }
                        },
                        selectKeyword(keyword) {
                            if (!keyword?.text) return;
                            window.location.href = '/search?q=' + encodeURIComponent(keyword.text);
                        },
                        selectProduct(product) {
                            if (product?.url) window.location.href = product.url;
                        }
                    }" @click.away="isOpen = false" class="relative">
                        <form action="{{ route('shop.index') }}" method="GET" @submit.prevent="goToSearch()">
                            <div class="relative group">
                                {{-- Search Icon (Dynamic) --}}
                                <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-all duration-300 group-focus-within:scale-110">
                                    @if(isset($category) && $category->icon_class)
                                        <i class="{{ $category->icon_class }} text-2xl"></i>
                                    @else
                                        <i class="fa-light fa-magnifying-glass text-2xl"></i>
                                    @endif
                                </div>

                                {{-- Search Input (Dynamic Placeholder) --}}
                                <input type="search"
                                       x-model="query"
                                       @input.debounce.300ms="search()"
                                       @focus="if(query.trim().length >= 2) isOpen = hasResults"
                                       placeholder="@if(isset($category)){{ $category->getTranslated('title') }} ürünlerinde ara...@else{{ __('shop::front.search_products') }}@endif"
                                       class="w-full pl-20 pr-48 py-6 rounded-2xl border-2 border-gray-200 dark:border-white/20
                                              bg-gradient-to-r from-white via-gray-50 to-white dark:from-slate-800 dark:via-slate-900 dark:to-slate-800
                                              shadow-[0_8px_30px_rgb(0,0,0,0.06)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.4)]
                                              focus:border-blue-500 dark:focus:border-blue-400
                                              focus:ring-4 focus:ring-blue-100/50 dark:focus:ring-blue-900/30
                                              focus:shadow-[0_8px_40px_rgb(59,130,246,0.15)]
                                              transition-all duration-300
                                              text-gray-900 dark:text-white
                                              placeholder-gray-400 dark:placeholder-gray-500
                                              text-lg font-medium
                                              backdrop-blur-sm"
                                       autocomplete="off">

                                {{-- Submit Button --}}
                                <button type="submit"
                                        :disabled="loading"
                                        class="absolute right-3 top-1/2 -translate-y-1/2
                                               px-10 py-4
                                               bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600
                                               text-white rounded-xl font-bold text-base
                                               shadow-[0_4px_20px_rgba(59,130,246,0.3)]
                                               hover:shadow-[0_8px_30px_rgba(59,130,246,0.4)]
                                               hover:scale-105
                                               active:scale-95
                                               transition-all duration-300
                                               flex items-center gap-2
                                               disabled:opacity-70 disabled:cursor-not-allowed">
                                    <i class="fa-solid fa-spinner fa-spin" x-show="loading" x-cloak></i>
                                    <span x-show="!loading">Ara</span>
                                    <i class="fa-light fa-arrow-right" x-show="!loading"></i>
                                </button>
                            </div>
                        </form>

                        {{-- Autocomplete Dropdown --}}
                        <div x-show="isOpen"
                             x-transition
                             class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 shadow-2xl rounded-xl z-50 border border-gray-200 dark:border-gray-700 overflow-hidden"
                             x-cloak>
                            <div class="max-h-[28rem] overflow-y-auto">
                                <div class="grid gap-4 px-4 py-4 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
                                    {{-- Keywords Section --}}
                                    <div x-show="keywords.length > 0" class="space-y-2 border border-gray-200 dark:border-gray-700 rounded-lg p-4 lg:p-5 bg-gray-50 dark:bg-gray-900/40">
                                        <div class="flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                            <span><i class="fa-solid fa-fire text-orange-500 mr-1"></i> Popüler Aramalar</span>
                                            <span class="text-[10px] text-gray-400 dark:text-gray-500" x-text="`${keywords.length}`"></span>
                                        </div>
                                        <div class="space-y-1">
                                            <template x-for="(keyword, index) in keywords" :key="'k-'+index">
                                                <a href="#"
                                                   @click.prevent="selectKeyword(keyword)"
                                                   class="flex items-center justify-between gap-3 px-3 py-2 rounded-md transition group hover:bg-white dark:hover:bg-gray-800">
                                                    <div class="flex items-center gap-3">
                                                        <span class="w-7 h-7 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-gray-400 dark:text-gray-500 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                                                        </span>
                                                        <span class="font-medium text-sm text-gray-900 dark:text-white" x-text="keyword.text"></span>
                                                    </div>
                                                    <span x-show="keyword.count" class="text-xs text-gray-400 dark:text-gray-500" x-text="`${keyword.count} sonuç`"></span>
                                                </a>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Products Section --}}
                                    <div x-show="products.length > 0" class="space-y-3">
                                        <div class="flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                            <span><i class="fa-solid fa-box text-blue-500 mr-1"></i> Ürünler</span>
                                            <span x-show="total > 0" class="text-[11px] font-medium text-gray-400 dark:text-gray-500" x-text="`${products.length} / ${total}`"></span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <template x-for="(product, index) in products" :key="'p-'+index">
                                                <a href="#"
                                                   @click.prevent="selectProduct(product)"
                                                   class="flex gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 transition group hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md">
                                                    <div class="w-16 h-16 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden flex-shrink-0">
                                                        <template x-if="product.image">
                                                            <img :src="product.image" :alt="product.title" class="w-full h-full object-cover">
                                                        </template>
                                                        <template x-if="!product.image">
                                                            <i class="fa-solid fa-cube text-gray-400 dark:text-gray-500 text-xl"></i>
                                                        </template>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-medium text-sm text-gray-900 dark:text-white leading-snug line-clamp-2" x-html="product.highlighted_title || product.title"></div>
                                                        <p x-show="product.highlighted_description" class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2" x-html="product.highlighted_description"></p>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                            <span x-text="product.type_label"></span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- View All Results --}}
                            <a :href="`/search?q=${encodeURIComponent(query)}`"
                               x-show="total > 0"
                               class="block p-4 text-center text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-semibold transition border-t border-gray-200 dark:border-gray-700">
                                <i class="fa-solid fa-arrow-right mr-2"></i>
                                <span x-text="`Tüm ${total} sonucu gör`"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Categories Filter --}}
        @if($categories->count() > 0)
        <section class="py-8 border-b border-gray-200 dark:border-white/10">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-hide">
                    {{-- All Categories (Modal Trigger) --}}
                    <button @click="$refs.categoryModal.classList.remove('hidden')"
                       class="flex-shrink-0 px-6 py-3 rounded-xl font-semibold transition-all {{ !$selectedCategory ? 'bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white/70 dark:bg-white/5 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-white/10 hover:border-blue-300 dark:hover:border-blue-400' }}">
                        <i class="fa-light fa-grid-2 mr-2"></i>
                        Tüm Kategoriler
                    </button>

                    {{-- Root Categories + Subcategories --}}
                    @foreach($categories as $category)
                        @php
                            $categorySlug = $category->getTranslated('slug');
                            $isActive = $selectedCategory && $selectedCategory->category_id === $category->category_id;
                            $subcategories = \Modules\Shop\App\Models\ShopCategory::where('parent_id', $category->category_id)
                                ->active()
                                ->orderBy('sort_order')
                                ->get();
                        @endphp

                        {{-- Root Category --}}
                        <a href="/shop/kategori/{{ $categorySlug }}"
                           class="flex-shrink-0 px-6 py-3 rounded-xl font-bold text-base transition-all {{ $isActive ? 'bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white/70 dark:bg-white/5 text-gray-700 dark:text-gray-300 border-2 border-gray-200 dark:border-white/10 hover:border-blue-300 dark:hover:border-blue-400' }}">
                            @if($category->icon_class)
                                <i class="{{ $category->icon_class }} mr-2"></i>
                            @endif
                            {{ $category->getTranslated('title') }}
                        </a>

                        {{-- Subcategories (inline after parent) --}}
                        @if($subcategories->count() > 0)
                            @foreach($subcategories as $subcategory)
                                @php
                                    $subcategorySlug = $subcategory->getTranslated('slug');
                                    $isSubActive = $selectedCategory && $selectedCategory->category_id === $subcategory->category_id;
                                @endphp
                                <a href="{{ url('/shop/kategori/' . $subcategorySlug) }}"
                                   class="flex-shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $isSubActive ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-300 dark:border-blue-600' : 'bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-white/10 hover:border-blue-200 dark:hover:border-blue-400 hover:text-blue-600 dark:hover:text-blue-400' }}">
                                    @if($subcategory->icon_class)
                                        <i class="{{ $subcategory->icon_class }} mr-1 text-xs"></i>
                                    @else
                                        <i class="fa-light fa-angle-right mr-1 text-xs opacity-50"></i>
                                    @endif
                                    {{ $subcategory->getTranslated('title') }}
                                </a>
                            @endforeach
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- Subcategories (if category is selected and has children) --}}
        @if($selectedCategory && $selectedCategory->children->count() > 0)
        <section class="py-12">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                    <i class="fa-light fa-folder-tree mr-2 text-blue-600 dark:text-blue-400"></i>
                    {{ __('shop::front.subcategories') }}
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($selectedCategory->children->sortBy('sort_order') as $subcategory)
                        @php
                            $subcategorySlug = $subcategory->getTranslated('slug');
                        @endphp
                        <a href="{{ url('/shop/category/' . $subcategorySlug) }}"
                           class="group bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl p-6 hover:bg-white/90 dark:hover:bg-white/10 hover:shadow-xl hover:border-blue-300 dark:hover:border-white/20 transition-all">
                            <div class="flex flex-col items-center text-center">
                                @if($subcategory->icon_class)
                                    <i class="{{ $subcategory->icon_class }} text-4xl text-blue-400 dark:text-blue-300 mb-3 group-hover:scale-110 transition-transform"></i>
                                @else
                                    <i class="fa-light fa-folder text-4xl text-blue-400 dark:text-blue-300 mb-3 group-hover:scale-110 transition-transform"></i>
                                @endif
                                <h3 class="font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $subcategory->getTranslated('title') }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- Products Section --}}
        <section class="py-12">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                @if($products->count() > 0)
                    {{-- Products Grid - iXtif Design --}}
                    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" x-ref="productsGrid">
                        @foreach($products as $product)
                            @include('shop::themes.ixtif.partials.product-card', ['product' => $product])
                        @endforeach
                    </div>

                    {{-- Infinite Scroll Loading Indicator --}}
                    <div x-show="loading" class="flex justify-center items-center py-12" x-cloak>
                        <div class="flex flex-col items-center gap-4">
                            <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 dark:border-gray-700 border-t-blue-600"></div>
                            <p class="text-gray-600 dark:text-gray-400 font-medium">Daha fazla ürün yükleniyor...</p>
                        </div>
                    </div>

                    {{-- End Message --}}
                    <div x-show="!hasMore && loaded" class="flex justify-center items-center py-12" x-cloak>
                        <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md rounded-2xl border border-gray-200 dark:border-white/10 px-8 py-6">
                            <p class="text-gray-600 dark:text-gray-400 font-medium text-center">
                                <i class="fa-light fa-check-circle text-green-600 dark:text-green-400 mr-2"></i>
                                Tüm ürünler yüklendi
                            </p>
                        </div>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-20">
                        <div class="inline-block">
                            <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                                <i class="fa-light fa-box-open text-4xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                                {{ __('shop::front.no_products_found') }}
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                                {{ __('shop::front.no_products_description') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- Category Filter Modal --}}
        <div x-ref="categoryModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="$refs.categoryModal.classList.add('hidden')"></div>

            {{-- Modal Panel --}}
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-6xl bg-white dark:bg-slate-800 rounded-3xl shadow-2xl overflow-hidden transform transition-all">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 px-8 py-6 relative overflow-hidden">
                        <div class="absolute inset-0 bg-white/10"></div>
                        <div class="relative flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                                    <i class="fa-light fa-filter"></i>
                                    Tüm Kategoriler
                                </h2>
                                <p class="text-white/80 text-sm mt-1">Hızlı kategori filtreleme</p>
                            </div>
                            <button @click="$refs.categoryModal.classList.add('hidden')"
                                    class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center text-white transition-all">
                                <i class="fa-light fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-8 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($categories as $rootCategory)
                                <div class="bg-gray-50 dark:bg-slate-700/50 rounded-2xl p-6 border border-gray-200 dark:border-white/10">
                                    {{-- Root Category --}}
                                    <a href="/shop/kategori/{{ $rootCategory->getTranslated('slug') }}"
                                       class="group flex items-center gap-3 mb-4 hover:bg-white dark:hover:bg-slate-700 rounded-xl p-3 -m-3 transition-all">
                                        @if($rootCategory->icon_class)
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                                <i class="{{ $rootCategory->icon_class }} text-white text-xl"></i>
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ $rootCategory->getTranslated('title') }}
                                            </h3>
                                        </div>
                                        <i class="fa-light fa-arrow-right text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"></i>
                                    </a>

                                    {{-- Subcategories --}}
                                    @php
                                        $subcategories = \Modules\Shop\App\Models\ShopCategory::where('parent_id', $rootCategory->category_id)
                                            ->active()
                                            ->orderBy('sort_order')
                                            ->get();
                                    @endphp
                                    @if($subcategories->count() > 0)
                                        <div class="space-y-1 pl-4 border-l-2 border-gray-200 dark:border-white/10">
                                            @foreach($subcategories as $subCategory)
                                                <a href="{{ url('/shop/kategori/' . $subCategory->getTranslated('slug')) }}"
                                                   class="flex items-center gap-2 py-2 px-3 rounded-lg hover:bg-white dark:hover:bg-slate-700 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-all group">
                                                    @if($subCategory->icon_class)
                                                        <i class="{{ $subCategory->icon_class }} text-xs opacity-60 group-hover:opacity-100"></i>
                                                    @else
                                                        <i class="fa-light fa-angle-right text-xs opacity-60 group-hover:opacity-100"></i>
                                                    @endif
                                                    <span class="flex-1 font-medium">{{ $subCategory->getTranslated('title') }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 dark:bg-slate-700/50 px-8 py-4 border-t border-gray-200 dark:border-white/10">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('shop.index') }}"
                               class="text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">
                                <i class="fa-light fa-grid-2 mr-2"></i>
                                Tüm Ürünleri Göster
                            </a>
                            <button @click="$refs.categoryModal.classList.add('hidden')"
                                    class="px-6 py-2 bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-300 dark:hover:bg-slate-500 font-semibold transition-all">
                                Kapat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function shopIndexPage() {
            return {
                loaded: false,
                page: {{ $products->currentPage() }},
                loading: false,
                hasMore: {{ $products->hasMorePages() ? 'true' : 'false' }},
                lastPage: {{ $products->lastPage() }},

                init() {
                    this.$nextTick(() => {
                        this.loaded = true;
                        this.setupInfiniteScroll();
                    });
                },

                setupInfiniteScroll() {
                    const options = {
                        root: null,
                        rootMargin: '200px',
                        threshold: 0.1
                    };

                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting && this.hasMore && !this.loading) {
                                this.loadMore();
                            }
                        });
                    }, options);

                    // Observer'ı grid'e bağla
                    const grid = this.$refs.productsGrid;
                    if (grid) {
                        observer.observe(grid);
                    }

                    // Scroll event fallback
                    window.addEventListener('scroll', () => {
                        const scrollPosition = window.innerHeight + window.scrollY;
                        const threshold = document.documentElement.scrollHeight - 400;

                        if (scrollPosition >= threshold && this.hasMore && !this.loading) {
                            this.loadMore();
                        }
                    });
                },

                async loadMore() {
                    if (this.loading || !this.hasMore) return;

                    this.loading = true;
                    this.page++;

                    try {
                        // URL parametrelerini koru (category, search vb.)
                        const url = new URL(window.location.href);
                        url.searchParams.set('page', this.page);

                        const response = await fetch(url.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) throw new Error('Network response was not ok');

                        const html = await response.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        // Yeni ürünleri grid'e ekle
                        const newProducts = doc.querySelectorAll('[x-ref="productsGrid"] > div');
                        const grid = this.$refs.productsGrid;

                        newProducts.forEach(product => {
                            grid.appendChild(product.cloneNode(true));
                        });

                        // Daha sayfa var mı kontrol et
                        if (this.page >= this.lastPage) {
                            this.hasMore = false;
                        }

                    } catch (error) {
                        console.error('Error loading more products:', error);
                        this.page--; // Hata durumunda sayfa numarasını geri al
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
@endsection
