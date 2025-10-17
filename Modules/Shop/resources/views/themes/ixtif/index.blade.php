@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <div class="relative" x-data="shopsList()" x-init="init()">

        <!-- Gradient Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 -z-10"></div>

        <!-- Header -->
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10 dark:from-blue-600/20 dark:to-purple-600/20"></div>
            <div class="relative py-12">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-3xl">
                        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">
                            {{ $moduleTitle ?? __('shop::front.general.shops') }}
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-20">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                @if ($products->count() > 0)

                    <!-- Grid: 2 kolon (her satırda 2 ürün) -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        @foreach ($products as $item)
                            @php
                                $currentLocale = app()->getLocale();
                                $slugData = $item->getRawOriginal('slug');

                                if (is_string($slugData)) {
                                    $slugData = json_decode($slugData, true) ?: [];
                                }

                                if (is_array($slugData) && isset($slugData[$currentLocale]) && !empty($slugData[$currentLocale])) {
                                    $slug = $slugData[$currentLocale];
                                } else {
                                    $slug = is_array($slugData) ? ($slugData['tr'] ?? reset($slugData)) : $slugData;
                                }
                                $slug = $slug ?: $item->shop_id;

                                $title = $item->getTranslated('title') ?? ($item->getRawOriginal('title') ?? ($item->title ?? 'Başlıksız'));

                                $showSlug = \App\Services\ModuleSlugService::getSlug('Shop', 'show');
                                $defaultLocale = get_tenant_default_locale();
                                if ($currentLocale === $defaultLocale) {
                                    $dynamicUrl = '/' . $showSlug . '/' . $slug;
                                } else {
                                    $dynamicUrl = '/' . $currentLocale . '/' . $showSlug . '/' . $slug;
                                }

                                $metadesc = $item->getTranslated('metadesc') ?? ($item->getRawOriginal('metadesc') ?? ($item->metadesc ?? null));
                                $body = $item->getTranslated('body') ?? ($item->getRawOriginal('body') ?? ($item->body ?? null));
                                $description = $metadesc ?? (strip_tags($body) ?? null);

                                $variants = $item->childProducts ?? collect();
                                $featuredImage = $item->getFirstMedia('featured_image');
                            @endphp

                            <!-- Ürün Kartı - Glassmorphism -->
                            <article class="bg-white/70 dark:bg-white/5 backdrop-blur-md rounded-2xl shadow-lg hover:shadow-2xl border border-white/30 dark:border-white/10 transition-all overflow-hidden">

                                <!-- İçerik: 2 kolon (Sol: Ana Ürün, Sağ: Varyantlar) -->
                                <div class="grid grid-cols-1 md:grid-cols-2">

                                    <!-- SOL: ANA ÜRÜN -->
                                    <a href="{{ $dynamicUrl }}" class="block bg-white/20 dark:bg-white/5 p-6 hover:bg-white/30 dark:hover:bg-white/10 transition-colors">

                                        @if ($featuredImage)
                                            <div class="aspect-square mb-4 rounded-xl overflow-hidden bg-white/50 dark:bg-white/10 shadow-md backdrop-blur-sm">
                                                <img src="{{ $featuredImage->hasGeneratedConversion('thumb') ? $featuredImage->getUrl('thumb') : $featuredImage->getUrl() }}"
                                                    alt="{{ $title }}"
                                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                                            </div>
                                        @endif

                                        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                            {{ $title }}
                                        </h2>

                                        @if ($description)
                                            <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-3">
                                                {{ Str::limit($description, 120) }}
                                            </p>
                                        @endif
                                    </a>

                                    <!-- SAĞ: VARYANTLAR -->
                                    <div class="bg-white/20 dark:bg-white/5 p-6">
                                        @if ($variants->count() > 0)
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-white/20 dark:border-white/10">
                                                <i class="fa-solid fa-layer-group text-blue-600 dark:text-blue-400 mr-2"></i>
                                                Varyantlar <span class="text-gray-600 dark:text-gray-400 font-normal">({{ $variants->count() }})</span>
                                            </div>
                                            <ul class="space-y-2 max-h-[280px] overflow-y-auto">
                                                @foreach ($variants as $variant)
                                                    @php
                                                        $variantTitle = $variant->getTranslated('title', $currentLocale) ?? $variant->sku;
                                                        $variantUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($variant, $currentLocale);
                                                    @endphp
                                                    <li>
                                                        <a href="{{ $variantUrl }}"
                                                            class="group flex items-start gap-2 text-sm text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-white/30 dark:hover:bg-white/10 p-2 rounded-lg transition-all">
                                                            <i class="fa-solid fa-angle-right text-xs mt-1 group-hover:translate-x-1 transition-transform"></i>
                                                            <span class="flex-1">{{ $variantTitle }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="flex flex-col items-center justify-center h-full text-center text-gray-500 dark:text-gray-400 py-12">
                                                <i class="fa-solid fa-inbox text-3xl mb-2 opacity-50"></i>
                                                <p class="text-sm">Varyant bulunmuyor</p>
                                            </div>
                                        @endif
                                    </div>

                                </div>

                            </article>
                        @endforeach
                        @php
                            unset($item);
                        @endphp
                    </div>

                    <!-- Pagination -->
                    @if ($products->hasPages())
                        <div class="mt-20">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
                                {{ $products->links() }}
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-20" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                        <div x-show="show" x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100" class="inline-block">
                            <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                                <svg class="h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Henüz sayfa yok</h3>
                            <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                                {{ __('shop::front.general.no_shops_found') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function shopsList() {
            return {
                loaded: false,
                hover: null,
                prefetchedUrls: new Set(),

                init() {
                    this.$nextTick(() => {
                        this.loaded = true;
                    });
                },

                prefetch(url) {
                    if (this.prefetchedUrls.has(url)) return;

                    const link = document.createElement('link');
                    link.rel = 'prefetch';
                    link.href = url;
                    document.head.appendChild(link);
                    this.prefetchedUrls.add(url);
                },

                navigate(url) {
                    document.body.style.cursor = 'wait';
                    window.location.href = url;
                }
            }
        }
    </script>
@endsection
