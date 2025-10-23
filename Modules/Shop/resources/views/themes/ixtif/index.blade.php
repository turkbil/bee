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
                    @if($selectedCategory)
                        {{-- Kategori seçili --}}
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                            {{ $selectedCategory->getTranslated('title') }} {{ __('shop::front.products') }}
                        </h1>
                        @if($selectedCategory->getTranslated('description'))
                            <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                                {!! Str::limit(strip_tags($selectedCategory->getTranslated('description')), 200) !!}
                            </p>
                        @endif
                    @elseif(request('search'))
                        {{-- Arama yapıldı --}}
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                            "{{ request('search') }}" {{ __('shop::front.search_results') }}
                        </h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300">
                            {{ $products->total() }} {{ __('shop::front.products_found') }}
                        </p>
                    @else
                        {{-- Ana sayfa --}}
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                            {{ __('shop::front.hero_title') }}
                        </h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                            {{ __('shop::front.hero_subtitle') }}
                        </p>
                    @endif
                </div>
            </div>
        </section>

        {{-- Search Bar + Category Filter --}}
        <section class="py-8 border-b border-gray-200 dark:border-white/10 bg-white/30 dark:bg-white/5">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                <div class="max-w-4xl mx-auto">
                    <form action="{{ route('shop.index') }}" method="GET" class="relative">
                        {{-- Search Input --}}
                        <div class="relative group">
                            <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-colors">
                                <i class="fa-light fa-search text-xl"></i>
                            </div>
                            <input type="search"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="{{ __('shop::front.search_products') }}"
                                   class="w-full pl-14 pr-40 py-5 rounded-2xl border-2 border-gray-200 dark:border-white/10 bg-white dark:bg-slate-800 shadow-lg focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/30 transition-all text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 text-lg font-medium">

                            {{-- Submit Button --}}
                            <button type="submit"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 px-8 py-3 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white rounded-xl font-semibold hover:shadow-xl hover:scale-105 transition-all">
                                <i class="fa-light fa-arrow-right mr-2"></i>
                                Ara
                            </button>
                        </div>

                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                    </form>

                    {{-- Quick Actions --}}
                    <div class="mt-6 flex items-center justify-center gap-4">
                        <button @click="$refs.categoryModal.classList.remove('hidden')"
                                class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white rounded-xl font-bold text-base hover:shadow-2xl hover:scale-105 transition-all">
                            <i class="fa-light fa-grid-2 text-xl"></i>
                            <span>Tüm Kategoriler</span>
                            <i class="fa-light fa-chevron-right text-sm ml-2"></i>
                        </button>
                        <div class="inline-flex items-center gap-2 px-6 py-3 bg-white/70 dark:bg-white/10 backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-xl text-gray-700 dark:text-gray-300 font-semibold">
                            <i class="fa-light fa-sparkles text-purple-600 dark:text-purple-400"></i>
                            <span>Geniş Ürün Yelpazesi</span>
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
                        <a href="{{ route('shop.index', ['category' => $categorySlug]) }}"
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
                                <a href="{{ url('/shop/category/' . $subcategorySlug) }}"
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
                                    <a href="{{ route('shop.index', ['category' => $rootCategory->getTranslated('slug')]) }}"
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
                                            @php
                                                $rootProductCount = $rootCategory->products()->active()->published()->count();
                                            @endphp
                                            @if($rootProductCount > 0)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $rootProductCount }} ürün</span>
                                            @endif
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
                                                @php
                                                    $subProductCount = $subCategory->products()->active()->published()->count();
                                                @endphp
                                                <a href="{{ url('/shop/category/' . $subCategory->getTranslated('slug')) }}"
                                                   class="flex items-center gap-2 py-2 px-3 rounded-lg hover:bg-white dark:hover:bg-slate-700 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-all group">
                                                    @if($subCategory->icon_class)
                                                        <i class="{{ $subCategory->icon_class }} text-xs opacity-60 group-hover:opacity-100"></i>
                                                    @else
                                                        <i class="fa-light fa-angle-right text-xs opacity-60 group-hover:opacity-100"></i>
                                                    @endif
                                                    <span class="flex-1 font-medium">{{ $subCategory->getTranslated('title') }}</span>
                                                    @if($subProductCount > 0)
                                                        <span class="text-xs text-gray-400">({{ $subProductCount }})</span>
                                                    @endif
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
