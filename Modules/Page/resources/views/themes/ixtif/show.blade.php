@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
@if(isset($is_homepage) && $is_homepage)
    @include('page::themes.ixtif.homepage')
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

        // Check if this is full-screen widget page (contact page)
        $isFullScreenWidget = str_contains($body ?? '', '[[file:67]]');
    @endphp

    @if($isFullScreenWidget)
        {{-- Full Screen Widget Layout (No Container, No Padding) --}}
        @php
            // İki aşamalı render: Önce widget parse, sonra Blade render
            $parsedBody = parse_widget_shortcodes($body ?? '');
        @endphp
        {!! Blade::render($parsedBody, [], true) !!}

        @if(isset($item->js))
        <script>{!! $item->js !!}</script>
        @endif

        @if(isset($item->css))
        <style>{!! $item->css !!}</style>
        @endif
    @else
        {{-- Standard Page Layout --}}
        <div class="min-h-screen">
            {{-- Glassmorphism Subheader --}}
            <section class="bg-gray-50/95 dark:bg-gray-800/95 backdrop-blur-md border-y border-gray-200 dark:border-gray-700">
                <div class="container mx-auto py-6">
                    <div class="grid lg:grid-cols-[1fr_auto] gap-8 items-stretch">
                        <!-- Left: Title & Breadcrumb -->
                        <div class="flex flex-col justify-between">
                            <div class="flex items-center gap-6">
                                <i class="fa-solid fa-file-lines text-6xl text-blue-600 dark:text-blue-400 drop-shadow-lg"></i>
                                <div>
                                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-3">
                                        {{ $title }}
                                    </h1>
                                    <!-- Breadcrumb -->
                                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                        <a href="/" class="no-underline hover:text-blue-600 dark:hover:text-blue-400 transition flex items-center gap-1.5">
                                            <i class="fa-solid fa-home text-xs"></i>
                                            <span>Ana Sayfa</span>
                                        </a>
                                        <i class="fa-solid fa-chevron-right text-xs opacity-50"></i>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $title }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="container mx-auto py-12">
                <article class="prose prose-xl max-w-none dark:prose-invert
                              prose-headings:text-gray-900 dark:prose-headings:text-white
                              prose-p:text-gray-700 dark:prose-p:text-gray-300
                              prose-a:text-blue-600 dark:prose-a:text-blue-400 hover:prose-a:text-blue-700 dark:hover:prose-a:text-blue-300
                              prose-strong:text-gray-900 dark:prose-strong:text-white
                              prose-blockquote:border-l-blue-500 prose-blockquote:bg-blue-50/50 dark:prose-blockquote:bg-blue-900/10
                              prose-code:text-blue-600 dark:prose-code:text-blue-400 prose-code:bg-blue-50 dark:prose-code:bg-blue-900/20
                              prose-pre:bg-gray-900 dark:prose-pre:bg-gray-800
                              prose-img:rounded-lg">
                    @php
                        // İki aşamalı render: Önce widget parse, sonra Blade render
                        $parsedBody = parse_widget_shortcodes($body ?? '');
                    @endphp
                    {!! Blade::render($parsedBody, [], true) !!}
                </article>

                @if(isset($item->js))
                <script>{!! $item->js !!}</script>
                @endif

                @if(isset($item->css))
                <style>{!! $item->css !!}</style>
                @endif
            </div>
        </div>
    @endif
@endif
@endsection