@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <div class="relative" x-data="shopCategoryPage()" x-init="init()">
        {{-- Gradient Background --}}
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-800 dark:via-slate-900 dark:to-slate-800 -z-10"></div>

        {{-- Category Header Section --}}
        <section class="relative py-16 md:py-24 overflow-hidden">
            {{-- Background Decorations --}}
            <div class="absolute inset-0">
                <div class="absolute top-0 left-0 w-72 h-72 bg-blue-400/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-400/10 rounded-full blur-3xl"></div>
            </div>

            <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
                {{-- Breadcrumb --}}
                <nav class="mb-8">
                    <ol class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <li>
                            <a href="{{ route('shop.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">
                                <i class="fa-light fa-home mr-1"></i>
                                {{ __('shop::front.all_products') }}
                            </a>
                        </li>
                        @if($category->parent)
                            <li><i class="fa-light fa-chevron-right text-xs"></i></li>
                            <li>
                                <a href="{{ url('/shop/category/' . $category->parent->getTranslated('slug')) }}"
                                   class="hover:text-blue-600 dark:hover:text-blue-400 transition">
                                    {{ $category->parent->getTranslated('title') }}
                                </a>
                            </li>
                        @endif
                        <li><i class="fa-light fa-chevron-right text-xs"></i></li>
                        <li class="text-gray-900 dark:text-white font-semibold">
                            {{ $category->getTranslated('title') }}
                        </li>
                    </ol>
                </nav>

                {{-- Category Hero --}}
                <div class="max-w-4xl">
                    <div class="flex items-center gap-4 mb-6">
                        @if($category->icon_class)
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <i class="{{ $category->icon_class }} text-3xl text-white"></i>
                            </div>
                        @endif
                        <div>
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white">
                                {{ $category->getTranslated('title') }}
                                @if($subcategories->count() === 0 && $products->total() > 0)
                                    <span class="text-3xl md:text-4xl lg:text-5xl font-normal text-gray-600 dark:text-gray-400">
                                        - {{ __('shop::front.products') }}
                                    </span>
                                @endif
                            </h1>
                        </div>
                    </div>

                    @if($category->getTranslated('description'))
                        <div class="prose dark:prose-invert max-w-none text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                            {!! $category->getTranslated('description') !!}
                        </div>
                    @endif

                    {{-- Stats --}}
                    <div class="mt-8 flex items-center gap-6 text-sm">
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                            <i class="fa-light fa-box text-blue-600 dark:text-blue-400"></i>
                            <span class="font-semibold">{{ $products->total() }}</span>
                            <span>{{ __('shop::front.products') }}</span>
                        </div>
                        @if($subcategories->count() > 0)
                            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                <i class="fa-light fa-folder-tree text-purple-600 dark:text-purple-400"></i>
                                <span class="font-semibold">{{ $subcategories->count() }}</span>
                                <span>{{ __('shop::front.subcategories') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Subcategories Section --}}
        @if($subcategories->count() > 0)
        <section class="py-12 border-y border-gray-200 dark:border-white/10 bg-white/50 dark:bg-white/5">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                    <i class="fa-light fa-folder-tree text-blue-600 dark:text-blue-400"></i>
                    {{ __('shop::front.browse_subcategories') }}
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach($subcategories as $subcategory)
                        @php
                            $subcategorySlug = $subcategory->getTranslated('slug');
                            $subcategoryProductCount = $subcategory->products()->active()->published()->count();
                        @endphp
                        <a href="{{ url('/shop/category/' . $subcategorySlug) }}"
                           class="group bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl p-6 hover:bg-white/90 dark:hover:bg-white/10 hover:shadow-xl hover:border-blue-300 dark:hover:border-white/20 transition-all">
                            <div class="flex flex-col items-center text-center">
                                @if($subcategory->icon_class)
                                    <i class="{{ $subcategory->icon_class }} text-4xl text-blue-400 dark:text-blue-300 mb-3 group-hover:scale-110 transition-transform"></i>
                                @else
                                    <i class="fa-light fa-folder text-4xl text-blue-400 dark:text-blue-300 mb-3 group-hover:scale-110 transition-transform"></i>
                                @endif
                                <h3 class="font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-2">
                                    {{ $subcategory->getTranslated('title') }}
                                </h3>
                                @if($subcategoryProductCount > 0)
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $subcategoryProductCount }} {{ __('shop::front.products') }}
                                    </span>
                                @endif
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
                    {{-- Section Header --}}
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            <i class="fa-light fa-grid-2 mr-2 text-blue-600 dark:text-blue-400"></i>
                            {{ __('shop::front.products_in_category') }}
                        </h2>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('shop::front.showing') }}
                            <span class="font-semibold">{{ $products->count() }}</span>
                            {{ __('shop::front.of') }}
                            <span class="font-semibold">{{ $products->total() }}</span>
                            {{ __('shop::front.products') }}
                        </div>
                    </div>

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
