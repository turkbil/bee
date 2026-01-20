@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    @php
        $currentLocale = app()->getLocale();
        $moduleSlugService = app(\App\Services\ModuleSlugService::class);
        $indexSlug = $moduleSlugService->getMultiLangSlug('Shop', 'index', $currentLocale);
        $showSlug = \App\Services\ModuleSlugService::getSlug('Shop', 'show');
        $defaultLocale = get_tenant_default_locale();
        $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';

        $breadcrumbsArray = [
            ['label' => __('shop::front.general.home'), 'url' => url('/')],
            ['label' => $moduleTitle ?? __('shop::front.general.shops')]
        ];
    @endphp

    {{-- MINIMAL SUBHEADER --}}
    <section class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto py-4">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2 overflow-x-auto whitespace-nowrap scrollbar-hide">
                @foreach($breadcrumbsArray as $index => $crumb)
                    @if(isset($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition">{{ $crumb['label'] }}</a>
                        @if($index < count($breadcrumbsArray) - 1)
                            <span class="mx-2">/</span>
                        @endif
                    @else
                        <span class="text-gray-900 dark:text-white font-medium">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold font-heading text-gray-900 dark:text-white">{{ $moduleTitle ?? __('shop::front.general.shops') }}</h1>
        </div>
    </section>

    {{-- CONTENT --}}
    <section class="bg-white dark:bg-gray-900 py-10 md:py-16">
        <div class="container mx-auto">
            @if($products->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
                    @foreach($products as $item)
                        @php
                            $slugData = $item->getRawOriginal('slug');
                            if (is_string($slugData)) {
                                $slugData = json_decode($slugData, true) ?: [];
                            }
                            $slug = is_array($slugData) && isset($slugData[$currentLocale]) && !empty($slugData[$currentLocale])
                                ? $slugData[$currentLocale]
                                : (is_array($slugData) ? ($slugData['tr'] ?? reset($slugData)) : $slugData);
                            $slug = $slug ?: $item->shop_id;

                            $title = $item->getTranslated('title') ?? ($item->title ?? 'Başlıksız');
                            $dynamicUrl = $localePrefix . '/' . $showSlug . '/' . $slug;

                            $metadesc = $item->getTranslated('metadesc') ?? null;
                            $body = $item->getTranslated('body') ?? null;
                            $description = $metadesc ?? \Illuminate\Support\Str::limit(strip_tags($body ?? ''), 160);

                            $variants = $item->childProducts ?? collect();
                            $featuredImage = getFirstMediaWithFallback($item);
                        @endphp

                        <article class="bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2">
                                {{-- Sol: Ana Ürün --}}
                                <a href="{{ url($dynamicUrl) }}" class="block bg-gray-50 dark:bg-gray-900 p-6 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors border-r border-gray-200 dark:border-gray-700">
                                    @if($featuredImage)
                                        <div class="aspect-square mb-4 rounded-lg overflow-hidden bg-white dark:bg-gray-800 shadow-sm">
                                            <img src="{{ $featuredImage->hasGeneratedConversion('thumb') ? $featuredImage->getUrl('thumb') : $featuredImage->getUrl() }}"
                                                 alt="{{ $title }}"
                                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                                        </div>
                                    @endif

                                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                        {{ $title }}
                                    </h2>

                                    @if($description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3">
                                            {{ \Illuminate\Support\Str::limit($description, 120) }}
                                        </p>
                                    @endif
                                </a>

                                {{-- Sağ: Varyantlar --}}
                                <div class="bg-white dark:bg-gray-800 p-6">
                                    @if($variants->count() > 0)
                                        <div class="text-sm font-bold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                                            <i class="fa-solid fa-layer-group text-primary-600 dark:text-primary-400 mr-2"></i>
                                            Varyantlar <span class="text-gray-500 dark:text-gray-400 font-normal">({{ $variants->count() }})</span>
                                        </div>
                                        <ul class="space-y-2 max-h-[280px] overflow-y-auto">
                                            @foreach($variants as $variant)
                                                @php
                                                    $variantTitle = $variant->getTranslated('title', $currentLocale) ?? $variant->sku;
                                                    $variantUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($variant, $currentLocale);
                                                @endphp
                                                <li>
                                                    <a href="{{ $variantUrl }}"
                                                       class="group flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-gray-700 p-2 rounded-lg transition-all">
                                                        <i class="fa-solid fa-angle-right text-xs mt-1 group-hover:translate-x-1 transition-transform"></i>
                                                        <span class="flex-1">{{ $variantTitle }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="flex flex-col items-center justify-center h-full text-center text-gray-400 dark:text-gray-500 py-12">
                                            <i class="fa-solid fa-inbox text-3xl mb-2 opacity-50"></i>
                                            <p class="text-sm">Varyant bulunmuyor</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                @php unset($item); @endphp

                {{-- Pagination --}}
                @if($products->hasPages())
                    <div class="mt-12">
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                            {{ $products->links() }}
                        </div>
                    </div>
                @endif
            @else
                {{-- Empty State --}}
                <div class="text-center py-16">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-shopping-bag text-gray-400 dark:text-gray-500 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 font-heading">{{ __('shop::front.general.no_shops_found') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-body">Yakında ürünlerimiz burada listelenecek.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
