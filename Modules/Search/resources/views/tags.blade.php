@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('title', 'Tüm Aramalar - Popüler Arama Kelimeleri')

@section('module_content')
<div class="min-h-screen py-16">
    <div class="container mx-auto px-4 sm:px-4 md:px-0">

        {{-- Header - Minimalist --}}
        <div class="text-center mb-16 max-w-3xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 text-gray-900 dark:text-white">
                Popüler Aramalar
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                @if(isset($searchTags) && $searchTags->count() > 0)
                    {{ $searchTags->count() }} farklı arama bulundu
                @else
                    Henüz arama yapılmamış
                @endif
            </p>
        </div>

        {{-- Main Tag Cloud - Clean & Minimal --}}
        <div class="max-w-5xl mx-auto mb-16">
            <div class="flex flex-wrap justify-center gap-3">
                @forelse($searchTags ?? [] as $tag)
                    @if(!empty($tag->query) && trim($tag->query) !== '' && !str_contains($tag->query, '{'))
                    <a href="{{ route('search.show', ['query' => trim($tag->query)]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2
                              bg-gray-100 dark:bg-gray-800
                              border-2 border-gray-200 dark:border-gray-700
                              text-gray-700 dark:text-gray-300
                              rounded-lg
                              hover:border-indigo-500 dark:hover:border-indigo-400
                              hover:text-indigo-600 dark:hover:text-indigo-400
                              hover:bg-indigo-50 dark:hover:bg-indigo-900/20
                              transition-all duration-200
                              font-medium"
                       style="font-size: {{ min(1.2, max(0.875, $tag->font_size * 0.25)) }}rem;">

                        @if($tag->is_popular)
                            <i class="fa-solid fa-star text-yellow-500 text-xs"></i>
                        @endif

                        <span>{{ $tag->query }}</span>
                    </a>
                    @endif
                @empty
                    <div class="text-center py-16 w-full">
                        <i class="fa-solid fa-magnifying-glass text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-xl text-gray-500 dark:text-gray-400">
                            Henüz arama yapılmamış
                        </p>
                    </div>
                @endforelse
            </div>
        </div>


        {{-- Sidebar Sections - Horizontal on Mobile --}}
        <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Popular Searches --}}
            @if(isset($popularSearches) && $popularSearches->count() > 0)
                <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                    <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-gray-900 dark:text-white">
                        <i class="fa-solid fa-star text-yellow-500"></i>
                        Popüler Aramalar
                    </h3>
                    <div class="space-y-2">
                        @foreach($popularSearches->take(5) as $search)
                            @if(!empty($search->query) && trim($search->query) !== '' && !str_contains($search->query, '{'))
                            <a href="{{ route('search.show', ['query' => trim($search->query)]) }}"
                               class="block px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700
                                      hover:border-yellow-400 dark:hover:border-yellow-500
                                      rounded-lg transition-all text-gray-700 dark:text-gray-300
                                      hover:bg-yellow-50 dark:hover:bg-yellow-900/10
                                      font-medium">
                                {{ $search->query }}
                            </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Recent Searches --}}
            @if(isset($recentSearches) && $recentSearches->count() > 0)
                <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                    <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-gray-900 dark:text-white">
                        <i class="fa-solid fa-clock text-blue-500"></i>
                        Son Aramalar
                    </h3>
                    <div class="space-y-2">
                        @foreach($recentSearches->take(5) as $search)
                            @if(!empty($search->query) && trim($search->query) !== '' && !str_contains($search->query, '{'))
                            <a href="{{ route('search.show', ['query' => trim($search->query)]) }}"
                               class="block px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700
                                      hover:border-blue-400 dark:hover:border-blue-500
                                      rounded-lg transition-all text-sm text-gray-600 dark:text-gray-400
                                      hover:bg-blue-50 dark:hover:bg-blue-900/10">
                                {{ $search->query }}
                                <span class="text-xs text-gray-400 dark:text-gray-500 ml-2">
                                    {{ \Carbon\Carbon::parse($search->last_searched)->diffForHumans() }}
                                </span>
                            </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
