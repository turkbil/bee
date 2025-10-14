@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@push('head')
{!! \App\Services\SEOService::getPageSchema($item) !!}
@endpush

@section('module_content')
    @include('blog::themes.{{ $activeThemeName }}.partials.show-content', ['item' => $item])
@endsection
