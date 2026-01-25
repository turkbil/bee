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
        $indexSlug = $moduleSlugService->getMultiLangSlug('Portfolio', 'index', $currentLocale);
        $showSlug = \App\Services\ModuleSlugService::getSlug('Portfolio', 'show');
        $defaultLocale = get_tenant_default_locale();
        $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';

        $breadcrumbsArray = [
            ['label' => __('portfolio::front.general.home'), 'url' => url('/')],
            ['label' => $moduleTitle ?? __('portfolio::front.general.portfolios')]
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
            <h1 class="text-2xl md:text-3xl font-bold font-heading text-gray-900 dark:text-white">{{ $moduleTitle ?? __('portfolio::front.general.portfolios') }}</h1>
        </div>
    </section>

    {{-- CONTENT --}}
    <section class="bg-white dark:bg-gray-900 py-10 md:py-16">
        <div class="container mx-auto">
            @if($items->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6 md:gap-8">
                    @foreach($items as $item)
                        @php
                            $slugData = $item->getRawOriginal('slug');
                            if (is_string($slugData)) {
                                $slugData = json_decode($slugData, true) ?: [];
                            }
                            $slug = is_array($slugData) && isset($slugData[$currentLocale]) && !empty($slugData[$currentLocale])
                                ? $slugData[$currentLocale]
                                : (is_array($slugData) ? ($slugData['tr'] ?? reset($slugData)) : $slugData);
                            $slug = $slug ?: $item->portfolio_id;

                            $title = $item->getTranslated('title') ?? ($item->title ?? 'Başlıksız');
                            $portfolioImage = $item->getFirstMedia('hero') ?: $item->getFirstMedia('featured_image');
                            $dynamicUrl = $localePrefix . '/' . $showSlug . '/' . $slug;
                        @endphp

                        <a href="{{ url($dynamicUrl) }}"
                           class="group relative aspect-square rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300">
                            @if($portfolioImage)
                                <img src="{{ $portfolioImage->hasGeneratedConversion('medium') ? $portfolioImage->getUrl('medium') : $portfolioImage->getUrl() }}"
                                     alt="{{ $title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                    <i class="fa-solid fa-images text-white/20 text-5xl"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-4 md:p-6">
                                <h2 class="text-xl md:text-2xl font-bold text-white font-heading drop-shadow-lg">{{ $title }}</h2>
                            </div>
                        </a>
                    @endforeach
                </div>
                @php unset($item); @endphp

                {{-- Pagination --}}
                @if($items->hasPages())
                    <div class="mt-12">
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                            {{ $items->links() }}
                        </div>
                    </div>
                @endif
            @else
                {{-- Empty State --}}
                <div class="text-center py-16">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-images text-gray-400 dark:text-gray-500 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 font-heading">{{ __('portfolio::front.general.no_portfolios_found') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-body">Yakında portfolyo çalışmalarımız burada listelenecek.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
