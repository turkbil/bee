@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 't-4';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    @include('blog::themes.t-4.partials.show-content', ['item' => $item])
@endsection
