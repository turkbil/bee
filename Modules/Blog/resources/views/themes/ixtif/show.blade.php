@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@push('head')
{!! \App\Services\SEOService::getAllSchemas($item) !!}
@endpush

@section('module_content')
    @php
        // Theme fallback: try active theme, then simple
        $partialView = 'blog::themes.' . $themeName . '.partials.show-content';
        if (!view()->exists($partialView)) {
            $partialView = 'blog::themes.simple.partials.show-content';
        }
    @endphp
    @include($partialView, ['item' => $item])
@endsection
