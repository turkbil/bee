@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('title', 'Popüler Aramalar')

@section('module_content')
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            {{-- Sayfa Başlığı --}}
            <header class="mb-8 md:mb-12">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white leading-tight mb-4">
                    Popüler Aramalar
                </h1>
                <div class="h-1 w-20 bg-gradient-to-r from-blue-600 to-blue-400 dark:from-blue-500 dark:to-blue-300 rounded-full"></div>
            </header>

            @php
                $popularSearches = \Modules\Search\App\Models\SearchQuery::getPopularSearches(50, 90);
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($popularSearches as $search)
                    <a href="{{ route('search.show', ['query' => $search->query]) }}"
                       class="bg-white dark:bg-gray-800 px-4 py-3 rounded-lg shadow hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $search->query }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $search->search_count }} arama</div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
