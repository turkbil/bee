@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <div class="relative" x-data="shopIndexPage()" x-init="init()">
        {{-- Header Section - Glassmorphism Subheader (Design 7 - Exact Copy) --}}
        @if($selectedCategory)
            <section class="py-12">
                <!-- FULL WIDTH Container -->
                <div class="w-full">
                    <!-- Inner Container max-w-7xl -->
                    <div class="container mx-auto px-4 max-w-7xl">
                        <!-- Main Card with Bottom Shadow -->
                        <div class="bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-3xl p-8 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.3)]">
                            <div class="grid lg:grid-cols-[1fr_400px] gap-8 items-stretch">
                                <!-- Left: Title & Breadcrumb -->
                                <div class="flex flex-col justify-between">
                                    <div class="flex items-center gap-6 mb-6">
                                        @if($selectedCategory->icon_class)
                                            <div class="w-24 h-24 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-xl">
                                                <i class="{{ $selectedCategory->icon_class }} text-5xl text-white"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h1 class="text-4xl md:text-5xl font-bold text-white mb-3">{{ $selectedCategory->getTranslated('title') }}</h1>
                                            <!-- Breadcrumb -->
                                            <div class="flex items-center gap-2 text-sm text-white/90">
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

                                <!-- Right: Search & Sort - No Card -->
                                <div class="flex flex-col justify-center space-y-3">
                                    <!-- Search - ÜSTTE -->
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

                                    <!-- Sort + View - ALTTA -->
                                    <div class="flex items-center gap-2">
                                        <!-- Sort -->
                                        <select class="flex-1 px-4 py-3 bg-white/10 backdrop-blur-sm rounded-xl text-white text-sm font-medium
                                                       border border-white/20 focus:bg-white/20 focus:border-white/40 transition-all cursor-pointer"
                                                onchange="window.location.href = '{{ url()->current() }}?sort=' + this.value + '{{ request('search') ? '&search=' . request('search') : '' }}'">
                                            <option class="text-gray-900 bg-white" value="" {{ !request('sort') ? 'selected' : '' }}>Editörün Seçimi</option>
                                            <option class="text-gray-900 bg-white" value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>A'dan Z'ye</option>
                                            <option class="text-gray-900 bg-white" value="z-a" {{ request('sort') == 'z-a' ? 'selected' : '' }}>Z'den A'ya</option>
                                        </select>

                                        <!-- View Toggle - TEK BUTON ANIMASYONLU -->
                                        <button @click="toggleView()"
                                                class="relative px-3 py-3 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20
                                                       hover:bg-white/20 active:scale-95 transition-all duration-300 overflow-hidden">
                                            <!-- Grid Icon -->
                                            <i class="fa-solid fa-grip text-white absolute inset-0 flex items-center justify-center transition-all duration-500"
                                               :class="view === 'grid' ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 -rotate-180 scale-50'"></i>
                                            <!-- List Icon -->
                                            <i class="fa-solid fa-list text-white absolute inset-0 flex items-center justify-center transition-all duration-500"
                                               :class="view === 'list' ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 rotate-180 scale-50'"></i>
                                            <!-- Placeholder for size -->
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
            {{-- Glassmorphism Subheader for Shop Home --}}
            <section class="bg-white/70 dark:bg-white/5 backdrop-blur-md border-y border-white/20 dark:border-white/10">
                <div class="container mx-auto py-6">
                    <div class="grid lg:grid-cols-[1fr_auto] gap-8 items-stretch">
                        <!-- Left: Title & Breadcrumb -->
                        <div class="flex flex-col justify-between">
                            <div class="flex items-center gap-6">
                                <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-xl">
                                    <i class="fa-solid fa-store text-5xl text-white"></i>
                                </div>
                                <div>
                                    @if(request('search'))
                                        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-3">"{{ request('search') }}" Arama Sonuçları</h1>
                                    @else
                                        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-3">Tüm Ürünler</h1>
                                    @endif
                                    <!-- Breadcrumb -->
                                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                        <a href="{{ route('shop.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition flex items-center gap-1.5">
                                            <i class="fa-solid fa-home text-xs"></i>
                                            <span>Ana Sayfa</span>
                                        </a>
                                        <i class="fa-solid fa-chevron-right text-xs opacity-60"></i>
                                        <span class="font-semibold text-gray-900 dark:text-white">Ürünler</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: View Toggle Only (No Sort - Default sorting) -->
                        <div class="flex flex-col justify-end">
                            <div class="flex items-center justify-end">
                                <!-- View Toggle -->
                                <button @click="toggleView()"
                                        class="relative w-12 h-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                                               hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-blue-400 dark:hover:border-blue-500
                                               active:scale-95 transition-all duration-300 flex items-center justify-center">
                                    <!-- Grid Icon -->
                                    <i class="fa-solid fa-grip text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400
                                              absolute transition-all duration-300 text-2xl"
                                       :class="view === 'grid' ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"></i>
                                    <!-- List Icon -->
                                    <i class="fa-solid fa-list text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400
                                              absolute transition-all duration-300 text-2xl"
                                       :class="view === 'list' ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif


        {{-- Categories Filter --}}
        @if($categories->count() > 0)
        <section class="py-8 border-b border-gray-200 dark:border-white/10">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-hide">
                    {{-- All Categories (Modal Trigger) --}}
                    <button @click="$refs.categoryModal.classList.remove('hidden')"
                       class="flex-shrink-0 px-6 py-3 rounded-xl font-semibold transition-all {{ !$selectedCategory ? 'bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white/50 dark:bg-white/5 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-white/10 hover:border-blue-300 dark:hover:border-blue-400' }}">
                        <i class="fa-light fa-grid-2 mr-2"></i>
                        Tüm Kategoriler
                    </button>

                    {{-- Root Categories Only --}}
                    @foreach($categories as $category)
                        @php
                            $categorySlug = $category->getTranslated('slug');
                            $isActive = $selectedCategory &&
                                       ($selectedCategory->category_id === $category->category_id ||
                                        ($selectedCategory->parent && $selectedCategory->parent->category_id === $category->category_id));
                        @endphp

                        {{-- Root Category --}}
                        <a href="/shop/kategori/{{ $categorySlug }}"
                           class="flex-shrink-0 px-6 py-3 rounded-xl font-bold text-base transition-all {{ $isActive ? 'bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white/50 dark:bg-white/5 text-gray-700 dark:text-gray-300 border-2 border-gray-200 dark:border-white/10 hover:border-blue-300 dark:hover:border-blue-400' }}">
                            @if($category->icon_class)
                                <i class="{{ $category->icon_class }} mr-2"></i>
                            @endif
                            {{ $category->getTranslated('title') }}
                        </a>
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
                           class="group bg-white/60 dark:bg-white/5 backdrop-blur-sm border border-gray-200 dark:border-white/10 rounded-2xl p-6 hover:bg-white/80 dark:hover:bg-white/10 hover:shadow-xl hover:border-blue-300 dark:hover:border-white/20 transition-all">
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
                    {{-- Products Grid/List - iXtif Design --}}
                    {{-- Grid: 2 → 3 → 4 sütun (responsive) | List: 1 → 2 sütun (responsive) --}}
                    <div class="grid gap-6 transition-all duration-300"
                         :class="view === 'grid' ? 'grid-cols-2 lg:grid-cols-3 xl:grid-cols-4' : 'grid-cols-1 lg:grid-cols-2'"
                         x-ref="productsGrid">
                        @foreach($products as $product)
                            {{-- Grid Mode Card --}}
                            <div x-show="view === 'grid'" x-cloak>
                                @include('shop::themes.ixtif.partials.product-card', ['product' => $product, 'viewMode' => 'grid'])
                            </div>
                            {{-- List Mode Card --}}
                            <div x-show="view === 'list'" x-cloak>
                                @include('shop::themes.ixtif.partials.product-card', ['product' => $product, 'viewMode' => 'list'])
                            </div>
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
                        <div class="bg-white/50 dark:bg-white/5 backdrop-blur-sm rounded-2xl border border-gray-200 dark:border-white/10 px-8 py-6">
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
                view: localStorage.getItem('shopViewMode') || 'grid', // Persist view preference

                init() {
                    this.$nextTick(() => {
                        this.loaded = true;
                        this.setupInfiniteScroll();
                    });
                },

                toggleView() {
                    this.view = this.view === 'grid' ? 'list' : 'grid';
                    localStorage.setItem('shopViewMode', this.view); // Save preference
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
