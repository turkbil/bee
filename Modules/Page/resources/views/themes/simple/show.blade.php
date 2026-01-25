@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
@if(isset($is_homepage) && $is_homepage)
    @php
        $currentLocale = app()->getLocale();
        $body = $item->getTranslated('body', $currentLocale);
    @endphp

    <div class="page-content">
        @parsewidgets($body ?? '')
    </div>

    @if(isset($item->js))<script>{!! $item->js !!}</script>@endif
    @if(isset($item->css))<style>{!! $item->css !!}</style>@endif

@else
    @php
        $currentLocale = app()->getLocale();
        $title = $item->getTranslated('title', $currentLocale);
        $body = $item->getTranslated('body', $currentLocale);

        $moduleSlugService = app(\App\Services\ModuleSlugService::class);
        $indexSlug = $moduleSlugService->getMultiLangSlug('Page', 'index', $currentLocale);
        $defaultLocale = get_tenant_default_locale();
        $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
        $pageIndexUrl = $localePrefix . '/' . $indexSlug;

        $breadcrumbsArray = [
            ['label' => __('page::front.general.home'), 'url' => url('/')],
            ['label' => __('page::front.general.pages'), 'url' => url($pageIndexUrl)],
            ['label' => $title]
        ];
    @endphp

    {{-- SUBHEADER (Service ile aynı) --}}
    <section class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto py-4">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2 overflow-x-auto whitespace-nowrap scrollbar-hide">
                @foreach($breadcrumbsArray as $index => $crumb)
                    @if(isset($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition">{{ $crumb['label'] }}</a>
                        @if($index < count($breadcrumbsArray) - 1)<span class="mx-2">/</span>@endif
                    @else
                        <span class="text-gray-900 dark:text-white font-medium">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold font-heading text-gray-900 dark:text-white">{{ $title }}</h1>
        </div>
    </section>

    {{-- CONTENT --}}
    <section class="bg-white dark:bg-gray-900 py-10 md:py-16">
        <div class="container mx-auto">
            <div class="page-content prose prose-base max-w-none font-body dark:prose-invert prose-a:no-underline prose-p:leading-relaxed prose-li:leading-relaxed">
                @parsewidgets($body ?? '')
            </div>
        </div>
    </section>

    @if(isset($item->js))<script>{!! $item->js !!}</script>@endif
    @if(isset($item->css))<style>{!! $item->css !!}</style>@endif
@endif
@endsection
