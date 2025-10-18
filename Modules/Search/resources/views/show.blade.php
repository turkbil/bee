@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('title', $pageTitle)

@section('module_content')
    <div class="min-h-screen" x-data="{
        query: '{{ addslashes($query) }}',
        results: [],
        total: 0,
        loading: true,
        activeTab: 'all',
        async loadResults() {
            this.loading = true;
            try {
                const response = await fetch(`/api/search?q=${encodeURIComponent(this.query)}&type=${this.activeTab}`);
                const data = await response.json();

                if (data.success) {
                    this.results = data.data.items || [];
                    this.total = data.data.total || 0;
                }
            } catch (e) {
                console.error('Search error:', e);
            }
            this.loading = false;
        }
    }" x-init="loadResults()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold mb-2 text-gray-900 dark:text-white">
                    <span x-text="query"></span> - Arama Sonuçları
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    <span x-show="!loading && total > 0">
                        <strong x-text="total"></strong> sonuç bulundu
                    </span>
                    <span x-show="!loading && total === 0">
                        Sonuç bulunamadı
                    </span>
                    <span x-show="loading">
                        Aranıyor...
                    </span>
                </p>
            </div>

            {{-- Loading --}}
            <div x-show="loading" class="text-center py-12">
                <i class="fa-solid fa-spinner fa-spin text-4xl text-blue-600 dark:text-blue-400"></i>
                <p class="mt-4 text-gray-600 dark:text-gray-400">Arama yapılıyor...</p>
            </div>

            {{-- Results --}}
            <div x-show="!loading && results.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="(item, index) in results" :key="index">
                    <a :href="item.url"
                       class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition p-6 border border-gray-200 dark:border-gray-700 group">
                        {{-- Image --}}
                        <div x-show="item.image" class="mb-4">
                            <img :src="item.image"
                                 :alt="item.title"
                                 class="w-full h-48 object-cover rounded-lg">
                        </div>

                        {{-- Title --}}
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition"
                            x-html="item.highlighted_title"></h3>

                        {{-- Type Badge --}}
                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 mb-2"
                              x-text="item.type_label"></span>

                        {{-- Description --}}
                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2"
                           x-html="item.highlighted_description"></p>

                        {{-- Price --}}
                        <div x-show="item.price" class="mt-3">
                            <span class="text-lg font-bold text-green-600 dark:text-green-400" x-text="item.price"></span>
                        </div>
                    </a>
                </template>
            </div>

            {{-- No Results --}}
            <div x-show="!loading && results.length === 0" class="text-center py-12">
                <i class="fa-solid fa-magnifying-glass text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Sonuç Bulunamadı</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    "<span x-text="query"></span>" için sonuç bulunamadı.
                </p>
                <a href="/" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Ana Sayfaya Dön
                </a>
            </div>
        </div>
    </div>
@endsection
