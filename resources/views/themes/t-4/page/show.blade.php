{{-- t-4 Unimad Theme - Page Show (Generic - DB'den body çeker) --}}
@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 't-4';

    $currentLocale = app()->getLocale();
    $title = $item->getTranslated('title', $currentLocale);
    $body = $item->getTranslated('body', $currentLocale);

    // Breadcrumbs
    $breadcrumbsArray = [
        ['label' => 'Ana Sayfa', 'url' => url('/')],
        ['label' => $title]
    ];
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('content')
    {{-- MINIMAL SUBHEADER --}}
    <section class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 pt-20">
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

    {{-- PAGE CONTENT (DB'den) --}}
    <div class="page-content">
        @parsewidgets($body ?? '')
    </div>

    {{-- Custom JS/CSS --}}
    @if(isset($item->js))<script>{!! $item->js !!}</script>@endif
    @if(isset($item->css))<style>{!! $item->css !!}</style>@endif
@endsection
