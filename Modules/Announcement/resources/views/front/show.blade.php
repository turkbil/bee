@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@push('head')
{{-- Schema.org için sayfa bilgileri --}}
{!! \App\Services\SEOService::getPageSchema($item) !!}
@endpush

@section('module_content')
<div class="relative" x-data="featured()" x-init="init()">
    <!-- Gradient Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 -z-10"></div>

    @php
        $currentLocale = app()->getLocale();
        $title = $item->getTranslated('title', $currentLocale);
        $body = $item->getTranslated('body', $currentLocale);
    @endphp

    <!-- Homeannouncement Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none dark:prose-invert mb-12
                  prose-headings:font-bold prose-headings:text-gray-900 dark:prose-headings:text-white
                  prose-p:text-gray-600 dark:prose-p:text-gray-300 prose-p:leading-relaxed
                  prose-a:text-transparent prose-a:bg-gradient-to-r prose-a:from-blue-600 prose-a:to-purple-600 prose-a:bg-clip-text hover:prose-a:from-blue-700 hover:prose-a:to-purple-700
                  prose-strong:text-gray-900 dark:prose-strong:text-white
                  prose-blockquote:border-l-4 prose-blockquote:border-blue-500 prose-blockquote:bg-blue-50 dark:prose-blockquote:bg-blue-900/20 prose-blockquote:italic
                  prose-code:text-purple-600 dark:prose-code:text-purple-400 prose-code:bg-purple-50 dark:prose-code:bg-purple-900/20 prose-code:px-1 prose-code:py-0.5 prose-code:rounded
                  prose-pre:bg-gray-900 prose-pre:shadow-xl
                  prose-img:rounded-xl prose-img:shadow-lg">
            @parsewidgets($body ?? '')
        </div>

        </div>
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
</div>

<script>
function featured() {
    return {
        loaded: false,

        init() {
            this.$nextTick(() => {
                this.loaded = true;
            });
        }
    }
}
</script>
@else
<div class="bg-white dark:bg-gray-900">

    <!-- Header -->
    <div class="border-b border-gray-100 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="max-w-full">
                <h1 class="text-4xl font-semibold text-gray-900 dark:text-white mb-4">
                    {{ $item->getTranslated('title', app()->getLocale()) }}
                </h1>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="prose prose-lg max-w-none dark:prose-invert
                   prose-headings:text-gray-900 dark:prose-headings:text-white
                   prose-p:text-gray-600 dark:prose-p:text-gray-300
                   prose-a:text-blue-600 dark:prose-a:text-blue-400
                   prose-strong:text-gray-900 dark:prose-strong:text-white
                   prose-img:rounded-lg">
            @parsewidgets($item->getTranslated('body', app()->getLocale()) ?? '')
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
    </div>
</div>
@endif
@endsection
