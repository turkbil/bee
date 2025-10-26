@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <div class="relative" x-data="shopCategoryPage()" x-init="init()">
        {{-- Glass Subheader Component --}}
        @php
            $breadcrumbs = [
                ['label' => 'Ana Sayfa', 'url' => route('shop.index'), 'icon' => 'fa-home'],
                ['label' => 'Ürünler', 'url' => route('shop.index')]
            ];
            if($category->parent) {
                $breadcrumbs[] = ['label' => $category->parent->getTranslated('title'), 'url' => url('/shop/kategori/' . $category->parent->getTranslated('slug'))];
            }
            $breadcrumbs[] = ['label' => $category->getTranslated('title')];
        @endphp

        @include('themes.ixtif.layouts.partials.glass-subheader', [
            'title' => $category->getTranslated('title'),
            'icon' => $category->icon_class ?? 'fa-solid fa-box',
            'breadcrumbs' => $breadcrumbs,
            'rightSlot' => '<div class="flex items-center gap-2">
                <!-- Sort -->
                <select class="flex-1 px-4 py-3 bg-white dark:bg-gray-800 rounded-xl text-gray-900 dark:text-white text-sm font-medium
                               border border-gray-200 dark:border-gray-700 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 transition-all cursor-pointer"
                        onchange="window.location.href = \'' . url()->current() . '?sort=\' + this.value + \'' . (request('search') ? '&search=' . request('search') : '') . '\'">
                    <option value="" ' . (!request('sort') ? 'selected' : '') . '>Varsayılan Sıralama</option>
                    <option value="a-z" ' . (request('sort') == 'a-z' ? 'selected' : '') . '>A\'dan Z\'ye</option>
                    <option value="z-a" ' . (request('sort') == 'z-a' ? 'selected' : '') . '>Z\'den A\'ya</option>
                </select>

                <!-- View Toggle -->
                <button @click="toggleView()"
                        class="relative w-12 h-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                               hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-blue-400 dark:hover:border-blue-500
                               active:scale-95 transition-all duration-300 flex items-center justify-center">
                    <!-- Grid Icon (default visible) -->
                    <i class="fa-solid fa-grip text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400
                              absolute transition-all duration-300 text-2xl"
                       :class="view === \'grid\' ? \'opacity-100 scale-100\' : \'opacity-0 scale-75\'"></i>
                    <!-- List Icon (default hidden) -->
                    <i class="fa-solid fa-list text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400
                              absolute transition-all duration-300 text-2xl opacity-0 scale-75"
                       :class="view === \'list\' ? \'opacity-100 scale-100\' : \'opacity-0 scale-75\'"></i>
                </button>
            </div>'
        ])

        {{-- Categories Filter - Only Root Categories --}}
        @php
            $allCategories = \Modules\Shop\App\Models\ShopCategory::query()
                ->whereNull('parent_id')
                ->active()
                ->orderBy('sort_order', 'asc')
                ->get();
        @endphp
        @if($allCategories->count() > 0)
        <section class="py-8 border-b border-gray-200 dark:border-white/10">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-hide">
                    {{-- All Products Link --}}
                    <a href="{{ route('shop.index') }}"
                       class="flex-shrink-0 px-6 py-3 rounded-xl font-semibold transition-all bg-white/50 dark:bg-white/5 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-white/10 hover:border-blue-300 dark:hover:border-blue-400">
                        <i class="fa-light fa-grid-2 mr-2"></i>
                        Tüm Ürünler
                    </a>

                    {{-- Root Categories --}}
                    @foreach($allCategories as $cat)
                        @php
                            $catSlug = $cat->getTranslated('slug');
                            $isActive = $category->category_id === $cat->category_id ||
                                       ($category->parent && $category->parent->category_id === $cat->category_id);
                        @endphp

                        <a href="/shop/kategori/{{ $catSlug }}"
                           class="flex-shrink-0 px-6 py-3 rounded-xl font-bold text-base transition-all {{ $isActive ? 'bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white/50 dark:bg-white/5 text-gray-700 dark:text-gray-300 border-2 border-gray-200 dark:border-white/10 hover:border-blue-300 dark:hover:border-blue-400' }}">
                            @if($cat->icon_class)
                                <i class="{{ $cat->icon_class }} mr-2"></i>
                            @endif
                            {{ $cat->getTranslated('title') }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- Subcategories Section --}}
        @if($subcategories->count() > 0)
        <section class="py-12">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                    <i class="fa-light fa-folder-tree text-blue-600 dark:text-blue-400"></i>
                    {{ __('shop::front.browse_subcategories') }}
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-8">
                    @foreach($subcategories as $subcategory)
                        @php
                            $subcategorySlug = $subcategory->getTranslated('slug');
                            $subcategoryProductCount = $subcategory->products()->active()->published()->count();
                        @endphp
                        <a href="{{ url('/shop/kategori/' . $subcategorySlug) }}"
                           class="group bg-white/60 dark:bg-white/5 backdrop-blur-sm border border-gray-200 dark:border-white/10 rounded-2xl p-6 hover:bg-white/80 dark:hover:bg-white/10 hover:shadow-xl hover:border-blue-300 dark:hover:border-white/20 transition-all">
                            <div class="flex flex-col items-center justify-center text-center h-full min-h-[120px]">
                                @if($subcategory->icon_class)
                                    <i class="{{ $subcategory->icon_class }} text-5xl text-blue-500 dark:text-blue-400 mb-3 group-hover:scale-110 transition-transform"></i>
                                @else
                                    <i class="fa-light fa-folder text-5xl text-blue-500 dark:text-blue-400 mb-3 group-hover:scale-110 transition-transform"></i>
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
        <section class="py-16">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                @if($products->count() > 0)
                    {{-- Loading Spinner (Initial Page Load) --}}
                    <div x-show="!loaded" class="flex justify-center items-center py-32">
                        <div class="flex flex-col items-center gap-4">
                            <i class="fa-solid fa-spinner loading-spin text-5xl text-blue-600"></i>
                            <p class="text-gray-600 dark:text-gray-400 font-medium">Yükleniyor...</p>
                        </div>
                    </div>

                    {{-- Products Grid/List - iXtif Design --}}
                    {{-- Grid: 2 → 3 → 4 sütun (responsive) | List: 1 → 2 sütun (responsive) --}}
                    <div x-show="loaded"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="grid gap-8 transition-all duration-300"
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
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-20">
                        <div class="inline-block">
                            <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                                <i class="fa-light fa-box-open text-4xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                                {{ __('shop::front.no_products_in_category') }}
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto mb-6">
                                {{ __('shop::front.no_products_category_description') }}
                            </p>
                            @if($subcategories->count() > 0)
                                <a href="#subcategories"
                                   class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg hover:scale-105 transition-all">
                                    <i class="fa-light fa-folder-tree"></i>
                                    {{ __('shop::front.browse_subcategories') }}
                                </a>
                            @else
                                <a href="{{ route('shop.index') }}"
                                   class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg hover:scale-105 transition-all">
                                    <i class="fa-light fa-arrow-left"></i>
                                    {{ __('shop::front.back_to_all_products') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <script>
        function shopCategoryPage() {
            return {
                loaded: false,
                page: {{ $products->currentPage() }},
                loading: false,
                hasMore: {{ $products->hasMorePages() ? 'true' : 'false' }},
                lastPage: {{ $products->lastPage() }},
                view: localStorage.getItem('shopViewMode') || 'grid', // Persist view preference

                init() {
                    // Minimum 500ms göster (çok hızlı yüklense bile skeleton görünsün)
                    setTimeout(() => {
                        this.loaded = true;
                        this.setupInfiniteScroll();
                    }, 500);
                },

                toggleView() {
                    this.view = this.view === 'grid' ? 'list' : 'grid';
                    localStorage.setItem('shopViewMode', this.view); // Save preference
                },

                setupInfiniteScroll() {
                    const options = {
                        root: null,
                        rootMargin: '800px', // Sayfa sonundan 800px önce tetiklenir
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
                        const threshold = document.documentElement.scrollHeight - 1200; // Sayfa sonundan 1200px önce tetiklenir

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
                        // Mevcut URL'i al ve sayfa parametresini güncelle
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
