@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';

    $initialItems = $initialData['items'] ?? [];
    $initialTotal = $initialData['total'] ?? 0;
    $initialResponse = $initialData['response_time'] ?? 0;
    $initialPage = $initialData['page'] ?? 1;
    $initialPerPage = $initialData['per_page'] ?? 20;
    $prefetched = $initialTotal > 0;
@endphp

@extends('themes.' . $themeName . '.layouts.app')

@section('title', $pageTitle)

@push('styles')
    <style>
        [x-cloak] { display: none !important; }

        /* Fallback: Hide Alpine.js controlled elements until Alpine loads */
        .alpine-container:not([x-init]) {
            min-height: 200px;
        }
    </style>
@endpush

@push('scripts')
<script>
    // Debug Alpine.js loading
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔍 Search Page Debug:');
        console.log('  Alpine available:', typeof window.Alpine !== 'undefined');
        console.log('  Livewire available:', typeof window.Livewire !== 'undefined');

        setTimeout(() => {
            const searchContainer = document.querySelector('[x-data*="query"]');
            console.log('  Search container found:', !!searchContainer);
            console.log('  Alpine initialized:', searchContainer?.__x !== undefined);
        }, 1000);
    });
</script>
@endpush

@section('module_content')
    <div x-data="{
            query: @js($query),
            results: @js($initialItems->map(function($item) {
                // Remove HTML from highlighted fields for Alpine.js
                $item['highlighted_title'] = strip_tags($item['highlighted_title'] ?? $item['title']);
                $item['highlighted_description'] = strip_tags($item['highlighted_description'] ?? '');
                return $item;
            })->values()->toArray()),
            total: {{ $initialTotal }},
            responseTime: {{ $initialResponse }},
            loading: {{ $prefetched ? 'false' : 'true' }},
            loadingMore: false,
            page: {{ $initialPage }},
            perPage: {{ $initialPerPage }},
            lastPage: {{ $initialData['last_page'] ?? 1 }},
            activeTab: 'all',
            prefetched: {{ $prefetched ? 'true' : 'false' }},
            debounceTimer: null,
            maxAutoLoadPages: 50,
            autoLoadedPages: 1,
            async trackClick(itemId, itemType, position) {
                try {
                    // Convert type string to model class name
                    const typeMap = {
                        'products': 'Modules\\Shop\\App\\Models\\ShopProduct',
                        'categories': 'Modules\\Shop\\App\\Models\\ShopCategory',
                        'brands': 'Modules\\Shop\\App\\Models\\ShopBrand'
                    };

                    const modelType = typeMap[itemType] || itemType;

                    await fetch('/api/search/track-click', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.content || ''
                        },
                        body: JSON.stringify({
                            query: this.query,
                            result_id: itemId,
                            result_type: modelType,
                            position: position,
                            opened_in_new_tab: false
                        })
                    });
                } catch (error) {
                    console.warn('Click tracking failed:', error);
                }
            },
            scrollToResults() {
                // Smooth scroll to results section when new search is performed
                const resultsSection = document.querySelector('.search-results-container');
                if (resultsSection) {
                    resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            },
            async fetchResults(reset = false) {
                const trimmedQuery = this.query.trim();

                if (reset) {
                    this.page = 1;
                    this.autoLoadedPages = 1;
                }

                if (trimmedQuery.length &lt; 2) {
                    this.loading = false;
                    this.results = [];
                    this.total = 0;
                    this.responseTime = 0;
                    this.lastPage = 1;
                    return;
                }

                this.loading = true;

                try {
                    const params = new URLSearchParams({
                        q: trimmedQuery,
                        type: this.activeTab,
                        per_page: this.perPage,
                        page: this.page
                    });

                    const response = await fetch(`/api/search?${params.toString()}`);
                    const data = await response.json();

                    if (data.success && data.data) {
                        const items = data.data.items || [];
                        this.results = items;
                        this.total = data.data.total || 0;
                        this.responseTime = data.data.response_time || 0;

                        if (data.data.pagination) {
                            this.page = data.data.pagination.current_page || this.page;
                            this.lastPage = data.data.pagination.last_page || this.totalPages;
                        } else {
                            this.lastPage = this.totalPages;
                        }
                    } else {
                        this.results = [];
                        this.total = 0;
                        this.responseTime = 0;
                        this.lastPage = 1;
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    this.results = [];
                    this.total = 0;
                    this.responseTime = 0;
                    this.lastPage = 1;
                } finally {
                    this.loading = false;
                    this.prefetched = false;

                    // Scroll to results on new search
                    if (reset) {
                        this.$nextTick(() => {
                            this.scrollToResults();
                        });
                    }
                }
            },
            startSearch() {
                this.prefetched = false;
                this.fetchResults(true);
            },
            debouncedSearch() {
                // Clear existing timer
                if (this.debounceTimer) {
                    clearTimeout(this.debounceTimer);
                }
                // Set new timer
                this.debounceTimer = setTimeout(() => {
                    this.startSearch();
                }, 500);
            },
            async loadMore() {
                if (!this.canLoadMore || this.loadingMore) {
                    return;
                }

                this.loadingMore = true;
                const nextPage = this.page + 1;

                try {
                    const params = new URLSearchParams({
                        q: this.query.trim(),
                        type: this.activeTab,
                        per_page: this.perPage,
                        page: nextPage
                    });

                    const response = await fetch(`/api/search?${params.toString()}`);
                    const data = await response.json();

                    if (data.success && data.data) {
                        const items = data.data.items || [];

                        // Append new results (don't replace)
                        this.results = [...this.results, ...items];
                        this.page = nextPage;
                        this.autoLoadedPages++;

                        if (data.data.pagination) {
                            this.lastPage = data.data.pagination.last_page || this.totalPages;
                        }
                    }
                } catch (error) {
                    console.error('Load more error:', error);
                } finally {
                    this.loadingMore = false;
                }
            },
            get canLoadMore() {
                return this.page &lt; this.lastPage &&
                       this.autoLoadedPages &lt; this.maxAutoLoadPages &&
                       !this.loading &&
                       !this.loadingMore;
            },
            get totalPages() {
                return Math.max(1, Math.ceil(this.total / this.perPage));
            },
            removeFallback() {
                const fallback = document.querySelector('.js-search-fallback');
                if (fallback) {
                    fallback.remove();
                }
            }
        }"
        x-init="removeFallback(); if (!prefetched) { fetchResults(true); }">
        <div class="container mx-auto px-4 sm:px-4 md:px-0 py-8 md:py-12">
            {{-- Header --}}
            <div class="mb-6 space-y-4">
                <div>
                    <h1 class="text-3xl font-bold mb-2 text-gray-900 dark:text-white">
                        <span x-text="query">{{ $query }}</span> - Arama Sonuçları
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 space-x-2">
                        @if($initialTotal > 0)
                            {{-- Initial results exist - show them immediately --}}
                            <span x-show="!loading && total &gt; 0">
                                <strong x-text="total">{{ $initialTotal }}</strong> sonuç bulundu
                                <span x-show="responseTime" class="text-xs text-gray-400 dark:text-gray-500">
                                    (<span x-text="responseTime">{{ $initialResponse }}</span> ms)
                                </span>
                            </span>
                            <span x-show="!loading && total === 0" style="display: none;">
                                Sonuç bulunamadı
                            </span>
                        @else
                            {{-- No initial results --}}
                            <span x-show="!loading && total &gt; 0" style="display: none;">
                                <strong x-text="total"></strong> sonuç bulundu
                                <span x-show="responseTime" class="text-xs text-gray-400 dark:text-gray-500">
                                    (<span x-text="responseTime"></span> ms)
                                </span>
                            </span>
                            <span x-show="!loading && total === 0">
                                Sonuç bulunamadı
                            </span>
                        @endif
                        <span x-show="loading" style="display: none;">
                            Aranıyor...
                        </span>
                    </p>
                </div>

                <div class="w-full">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="search"
                               x-model.trim="query"
                               @input="debouncedSearch()"
                               @keydown.enter.prevent="startSearch()"
                               placeholder="Yeni bir arama yapın..."
                               class="flex-1 px-5 py-3 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 text-gray-800 dark:text-white transition" />
                        <button @click="startSearch()"
                                :disabled="query.trim().length &lt; 2"
                                class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold shadow hover:from-blue-700 hover:to-purple-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Ara
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2"
                       x-show="query.trim().length &lt; 2"
                       x-cloak>
                        Arama sonuçlarının listelenmesi için en az 2 karakter girmeniz gerekir.
                    </p>
                </div>
            </div>

            {{-- Loading --}}
            <div x-show="loading" class="text-center py-12" x-cloak>
                <i class="fa-solid fa-spinner fa-spin text-4xl text-blue-600 dark:text-blue-400"></i>
                <p class="mt-4 text-gray-600 dark:text-gray-400">Arama yapılıyor...</p>
            </div>

            {{-- Results --}}
            <div x-show="!loading && results.length &gt; 0" class="search-results-container" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <template x-for="(item, index) in results" :key="`${item.id}-${index}`">
                        <a :href="item.url"
                           @click="trackClick(item.id, item.type, index)"
                           class="block bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition p-4 md:p-6 border border-gray-200 dark:border-gray-700 group">
                            <div class="flex flex-row gap-3 md:gap-5">
                                <template x-if="item.image">
                                    <div class="w-16 sm:w-20 md:w-28 flex-shrink-0">
                                        <img :src="item.image"
                                             :alt="item.title"
                                             class="w-full aspect-square object-cover rounded-lg md:rounded-xl bg-gray-100 dark:bg-gray-800">
                                    </div>
                                </template>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm md:text-lg font-semibold text-gray-900 dark:text-white mb-1 md:mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition line-clamp-2"
                                        x-html="item.highlighted_title"></h3>

                                    <div class="flex flex-wrap items-center gap-1 md:gap-2 mb-1 md:mb-2">
                                        <span class="inline-flex items-center gap-1 px-2 md:px-3 py-0.5 md:py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300"
                                              x-text="item.type_label"></span>
                                    </div>

                                    <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 line-clamp-2 hidden sm:block"
                                       x-html="item.highlighted_description"></p>

                                    <div x-show="item.price" class="mt-2 md:mt-3">
                                        <span class="text-sm md:text-lg font-bold text-green-600 dark:text-green-400" x-text="item.price"></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>

                {{-- Loading More Indicator --}}
                <div x-show="loadingMore" class="text-center py-8" x-cloak>
                    <i class="fa-solid fa-spinner fa-spin text-3xl text-blue-600 dark:text-blue-400"></i>
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Daha fazla sonuç yükleniyor...</p>
                </div>

                {{-- Infinite Scroll Sentinel (Invisible trigger for loading more) --}}
                <div x-show="canLoadMore && !loadingMore"
                     x-intersect.threshold.20="loadMore()"
                     class="h-20"
                     x-cloak></div>

                {{-- End of Results Message --}}
                <div x-show="!canLoadMore && page >= lastPage && !loadingMore" class="mt-12 text-center" x-cloak>
                    <div class="inline-flex items-center gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                        <i class="fa-solid fa-check-circle text-2xl text-green-600 dark:text-green-400"></i>
                        <div class="text-left">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Tüm sonuçlar gösterildi</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                <span x-text="total"></span> sonuçtan <span x-text="results.length"></span> tanesi listelendi
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- No Results --}}
            <div x-show="!loading && results.length === 0" class="text-center py-12" x-cloak>
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

    @if($prefetched)
        <div class="container mx-auto px-4 sm:px-4 md:px-0 py-8 md:py-12 js-search-fallback">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($initialItems as $item)
                    <a href="{{ $item['url'] }}"
                       class="block bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 md:p-6 border border-gray-200 dark:border-gray-700">
                        <div class="flex flex-row gap-3 md:gap-5">
                            @if(!empty($item['image']))
                                <div class="w-16 sm:w-20 md:w-28 flex-shrink-0">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full aspect-square object-cover rounded-lg md:rounded-xl bg-gray-100 dark:bg-gray-800">
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm md:text-lg font-semibold text-gray-900 dark:text-white mb-1 md:mb-2 line-clamp-2">{!! $item['highlighted_title'] !!}</h3>
                                <div class="flex flex-wrap items-center gap-1 md:gap-2 mb-1 md:mb-2">
                                    <span class="inline-flex items-center gap-1 px-2 md:px-3 py-0.5 md:py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">{{ $item['type_label'] }}</span>
                                </div>
                                @if(!empty($item['highlighted_description']))
                                    <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 line-clamp-2 hidden sm:block">{!! $item['highlighted_description'] !!}</p>
                                @endif
                                @if(!empty($item['price']))
                                    <div class="mt-2 md:mt-3">
                                        <span class="text-sm md:text-lg font-bold text-green-600 dark:text-green-400">{{ $item['price'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($initialTotal > count($initialItems))
                <div class="mt-12 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ count($initialItems) }} / {{ $initialTotal }} sonuç görüntüleniyor. Daha fazla sonuç için tarayıcınızda JavaScript'i etkinleştirin.
                </div>
            @endif
            @if(($initialData['last_page'] ?? 1) > 1)
                <nav class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4" aria-label="Arama sayfalama">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300">
                            <i class="fa-solid fa-arrow-left"></i> Önceki
                        </span>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300">
                            Sonraki <i class="fa-solid fa-arrow-right"></i>
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span>Sayfa {{ $initialData['page'] ?? 1 }} / {{ $initialData['last_page'] ?? 1 }}</span>
                        <div class="hidden sm:flex items-center gap-1">
                            @php
                                $window = 3;
                                $start = max(1, ($initialData['page'] ?? 1) - $window);
                                $end = min($initialData['last_page'] ?? 1, ($initialData['page'] ?? 1) + $window);
                            @endphp
                            @for($i = $start; $i <= $end; $i++)
                                <span class="min-w-[2.5rem] h-10 rounded-lg border text-sm font-semibold transition {{ ($initialData['page'] ?? 1) === $i ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300' }}">
                                    {{ $i }}
                                </span>
                            @endfor
                        </div>
                    </div>
                </nav>
            @endif
        </div>
    @elseif(mb_strlen($query) >= 2)
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 js-search-fallback">
            <div class="text-center py-12">
                <i class="fa-solid fa-magnifying-glass text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Sonuç Bulunamadı</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    "{{ $query }}" için sonuç bulunamadı.
                </p>
                <a href="/" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Ana Sayfaya Dön
                </a>
            </div>
        </div>
    @endif
@endsection
