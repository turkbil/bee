@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        @php
            $currentLocale = app()->getLocale();
            $title = $item->getTranslated('title', $currentLocale) ?? 'Hoş Geldiniz';
            $body = $item->getTranslated('body', $currentLocale);
        @endphp

        <div class="mb-8">
            <h1 class="text-5xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mb-6">
                {{ $title }}
            </h1>
        </div>

        @if($body)
        <div class="prose prose-lg max-w-none dark:prose-invert mx-auto">
            @parsewidgets($body)
        </div>
        @endif
    </div>
</div>

@if(isset($item->js))
<script>{!! $item->js !!}</script>
@endif

@if(isset($item->css))
<style>{!! $item->css !!}</style>
@endif
@endsection
