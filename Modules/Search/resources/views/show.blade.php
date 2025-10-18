@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('title', $pageTitle)

@section('module_content')
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <h1 class="text-3xl font-bold mb-6">{{ $pageTitle }}</h1>
            <p class="text-gray-600 dark:text-gray-400">
                Arama sonuçları: <strong>{{ $query }}</strong>
            </p>
            <p class="mt-4 text-sm text-gray-500">
                (Livewire search-results component geçici olarak devre dışı)
            </p>
        </div>
    </div>
@endsection
