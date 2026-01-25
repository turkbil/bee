@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@push('head')
{{-- Schema.org --}}
{!! \App\Services\SEOService::getPageSchema($item) !!}
@endpush

@section('module_content')
    @php
        $currentLocale = app()->getLocale();
        $title = $item->getTranslated('title', $currentLocale);
        $body = $item->getTranslated('body', $currentLocale);

        $moduleSlugService = app(\App\Services\ModuleSlugService::class);
        $indexSlug = $moduleSlugService->getMultiLangSlug('Service', 'index', $currentLocale);
        $defaultLocale = get_tenant_default_locale();
        $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';
        $serviceIndexUrl = $localePrefix . '/' . $indexSlug;

        // Breadcrumbs
        $breadcrumbsArray = [
            ['label' => __('service::front.general.home'), 'url' => url('/'), 'icon' => 'fa-home'],
            ['label' => __('service::front.general.services'), 'url' => url($serviceIndexUrl)],
            ['label' => $title]
        ];
    @endphp

    {{-- Subheader --}}
    @subheader([
        'title' => $title,
        'icon' => 'fa-solid fa-briefcase',
        'breadcrumbs' => $breadcrumbsArray
    ])

    <div class="bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            {{-- Content --}}
            <div class="prose prose-lg max-w-none dark:prose-invert
                       prose-headings:font-bold prose-headings:text-gray-900 dark:prose-headings:text-white
                       prose-p:text-gray-600 dark:prose-p:text-gray-300 prose-p:leading-relaxed
                       prose-a:text-blue-600 dark:prose-a:text-blue-400 hover:prose-a:underline
                       prose-strong:text-gray-900 dark:prose-strong:text-white
                       prose-blockquote:border-l-4 prose-blockquote:border-blue-500 prose-blockquote:bg-blue-50 dark:prose-blockquote:bg-blue-900/20 prose-blockquote:italic
                       prose-code:text-blue-600 dark:prose-code:text-blue-400 prose-code:bg-blue-50 dark:prose-code:bg-blue-900/20 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded
                       prose-pre:bg-gray-900 prose-pre:shadow-xl
                       prose-img:rounded-xl prose-img:shadow-lg">
                @parsewidgets($body ?? '')
            </div>

            @if(isset($item->js))
            <script>
                {!! $item->js !!}
            </script>
            @endif

            @if(isset($item->css))
            <style>
                {!! $item->css !!}
            </style>
            @endif

            {{-- Back Button --}}
            <footer class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ url($serviceIndexUrl) }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>{{ __('service::front.general.all_services') }}</span>
                </a>
            </footer>
        </div>
    </div>
@endsection
