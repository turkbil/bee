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
                $breadcrumbs[] = ['label' => $category->parent->getTranslated('title'), 'url' => route('shop.category', $category->parent->getTranslated('slug'))];
            }
            $breadcrumbs[] = ['label' => $category->getTranslated('title')];
        @endphp

        @subheader([
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

                <!-- View Toggle - HOMEPAGE STİLİ -->
                <button @click="toggleView()"
                        class="relative w-14 h-14 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 group overflow-hidden">
                    <!-- Grid Icon -->
                    <div class="absolute inset-0 flex items-center justify-center transition-all duration-500"
                         :class="view === \'grid\' ? \'opacity-100 rotate-0 scale-100\' : \'opacity-0 -rotate-90 scale-50\'">
                        <i class="fa-solid fa-grid-2 text-xl text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"></i>
                    </div>
                    <!-- List Icon -->
                    <div class="absolute inset-0 flex items-center justify-center transition-all duration-500"
                         :class="view === \'list\' ? \'opacity-100 rotate-0 scale-100\' : \'opacity-0 rotate-90 scale-50\'">
                        <i class="fa-solid fa-list text-xl text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"></i>
                    </div>
                    <!-- Hover Gradient Ring -->
                    <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                         style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(168, 85, 247, 0.1), rgba(236, 72, 153, 0.1));"></div>
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

                        <a href="{{ route('shop.category', $catSlug) }}"
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

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 md:gap-6">
                    @foreach($subcategories as $subcategory)
                        @php
                            $subcategorySlug = $subcategory->getTranslated('slug');
                            $subcategoryProductCount = $subcategory->products()->active()->published()->count();
                        @endphp
                        <a href="{{ route('shop.category', $subcategorySlug) }}"
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
                    {{-- Products Grid/List - iXtif Design (Ultra hızlı transition) --}}
                    {{-- Grid: 2 → 3 → 4 sütun (responsive) | List: 1 → 2 sütun (responsive) --}}

                    {{-- Grid Mode Container --}}
                    <div x-show="view === 'grid'"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 md:gap-6"
                         x-ref="productsGrid">
                        @foreach($products as $product)
                            <x-ixtif.product-card :product="$product" layout="vertical" :showAddToCart="true" />
                        @endforeach
                    </div>

                    {{-- List Mode Container --}}
                    <div x-show="view === 'list'"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="grid grid-cols-1 lg:grid-cols-2 gap-6"
                         style="display: none;">
                        @foreach($products as $product)
                            <x-ixtif.product-card :product="$product" layout="horizontal" :showAddToCart="true" />
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
                page: {{ $products->currentPage() }},
                loading: false,
                hasMore: {{ $products->hasMorePages() ? 'true' : 'false' }},
                lastPage: {{ $products->lastPage() }},
                view: null,

                // Cookie helper functions
                getCookie(name) {
                    const value = `; ${document.cookie}`;
                    const parts = value.split(`; ${name}=`);
                    if (parts.length === 2) return parts.pop().split(';').shift();
                    return null;
                },

                setCookie(name, value, days = 365) {
                    const expires = new Date(Date.now() + days * 864e5).toUTCString();
                    document.cookie = `${name}=${value}; expires=${expires}; path=/; SameSite=Lax`;
                },

                getDefaultView() {
                    return window.innerWidth < 1024 ? 'list' : 'grid';
                },

                init() {
                    // Priority: Cookie > localStorage > Responsive Default
                    const cookieValue = this.getCookie('viewMode');
                    const localValue = localStorage.getItem('viewMode');

                    if (cookieValue) {
                        this.view = cookieValue;
                        localStorage.setItem('viewMode', cookieValue);
                    } else if (localValue) {
                        this.view = localValue;
                        this.setCookie('viewMode', localValue);
                    } else {
                        this.view = this.getDefaultView();
                    }

                    // Anasayfa gibi hızlı - direkt infinite scroll setup
                    this.setupInfiniteScroll();
                },

                toggleView() {
                    this.view = this.view === 'grid' ? 'list' : 'grid';
                    localStorage.setItem('viewMode', this.view);
                    this.setCookie('viewMode', this.view);
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

    {{-- AI Chat Context - Kategori Sayfası Bilgisi --}}
    <script>
        // Alpine yüklenene kadar bekle
        document.addEventListener('alpine:init', () => {
            // AI Chat store'a bu kategori bilgisini gönder
            if (typeof Alpine !== 'undefined' && Alpine.store('aiChat')) {
                Alpine.store('aiChat').updateContext({
                    category_id: {{ $category->id }},
                    product_id: null, // Kategori sayfası, tek ürün yok
                    page_slug: '{{ $category->getTranslated('slug', app()->getLocale()) }}',
                });

                console.log('✅ AI Chat Context Updated (Category):', {
                    category_id: {{ $category->id }},
                    category_title: @json($category->getTranslated('title', app()->getLocale())),
                });
            }
        });

        // Eğer Alpine zaten yüklüyse direkt güncelle
        if (typeof Alpine !== 'undefined' && Alpine.store('aiChat')) {
            Alpine.store('aiChat').updateContext({
                category_id: {{ $category->id }},
                product_id: null,
                page_slug: '{{ $category->getTranslated('slug', app()->getLocale()) }}',
            });
        }
    </script>

@endsection
