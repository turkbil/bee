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

        {{-- Header Section --}}
        <section class="relative py-16 md:py-20 overflow-hidden">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                <div class="text-center max-w-3xl mx-auto">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                        @if($selectedCategory)
                            {{ $selectedCategory->getTranslated('title') }}
                        @else
                            {{ $moduleTitle ?? __('shop::front.all_products') }}
                        @endif
                    </h1>
                    @if($selectedCategory && $selectedCategory->getTranslated('description'))
                        <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                            {!! Str::limit(strip_tags($selectedCategory->getTranslated('description')), 200) !!}
                        </p>
                    @endif
                </div>
            </div>
        </section>

        {{-- Categories Filter (Root Categories) --}}
        @if($categories->count() > 0)
        <section class="py-8 border-b border-gray-200 dark:border-white/10">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-hide">
                    {{-- All Products --}}
                    <a href="{{ route('shop.index') }}"
                       class="flex-shrink-0 px-6 py-3 rounded-xl font-semibold transition-all {{ !$selectedCategory ? 'bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white/70 dark:bg-white/5 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-white/10 hover:border-blue-300 dark:hover:border-blue-400' }}">
                        <i class="fa-light fa-grid-2 mr-2"></i>
                        {{ __('shop::front.all_products') }}
                    </a>

                    {{-- Root Categories --}}
                    @foreach($categories as $category)
                        @php
                            $categorySlug = $category->getTranslated('slug');
                            $isActive = $selectedCategory && $selectedCategory->category_id === $category->category_id;
                        @endphp
                        <a href="{{ route('shop.index', ['category' => $categorySlug]) }}"
                           class="flex-shrink-0 px-6 py-3 rounded-xl font-semibold transition-all {{ $isActive ? 'bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white/70 dark:bg-white/5 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-white/10 hover:border-blue-300 dark:hover:border-blue-400' }}">
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

                    {{-- Pagination --}}
                    @if($products->hasPages())
                        <div class="mt-12">
                            <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md rounded-2xl border border-gray-200 dark:border-white/10 p-6">
                                {{ $products->links() }}
                            </div>
                        </div>
                    @endif
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
    </div>

    <script>
        function shopIndexPage() {
            return {
                loaded: false,
                page: 1,
                loading: false,
                hasMore: {{ $products->hasMorePages() ? 'true' : 'false' }},

                init() {
                    this.$nextTick(() => {
                        this.loaded = true;
                    });
                },

                // Infinite scroll için hazır - opsiyonel olarak eklenebilir
                loadMore() {
                    if (this.loading || !this.hasMore) return;

                    this.loading = true;
                    this.page++;

                    // AJAX ile daha fazla ürün yükle
                    // Bu kısım opsiyonel - şimdilik pagination kullanıyoruz
                }
            }
        }
    </script>
@endsection
