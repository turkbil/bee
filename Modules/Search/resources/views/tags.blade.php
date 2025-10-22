@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('title', 'Tüm Aramalar - Popüler Arama Kelimeleri')

@section('module_content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-pink-50 dark:from-slate-900 dark:via-purple-900 dark:to-slate-900 py-12">
    <div class="container mx-auto px-4">
        {{-- Header --}}
        <div class="text-center mb-12 animate-fade-in">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-black mb-4 bg-clip-text text-transparent bg-gradient-to-r from-purple-600 via-pink-600 to-red-600">
                🔍 Tüm Aramalar
            </h1>
            <p class="text-lg md:text-xl lg:text-2xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                @if(isset($searchTags) && $searchTags->count() > 0)
                    {{ $searchTags->count() }} farklı arama bulundu. İstediğiniz ürünü bulmak için tıklayın!
                @else
                    Arama kelimeleri yükleniyor...
                @endif
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Main Tag Cloud --}}
            <div class="lg:col-span-3">
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-2xl p-6 md:p-12 border border-gray-200 dark:border-gray-700">
                    <div class="flex flex-wrap justify-center gap-4 items-center" style="line-height: 3;">
                        @forelse($searchTags ?? [] as $tag)
                            <a href="{{ route('search.show', ['query' => $tag->query]) }}"
                               class="inline-block px-4 md:px-6 py-2 md:py-3
                                      bg-gradient-to-r {{ $tag->color }}
                                      text-white font-bold rounded-full
                                      hover:scale-110 hover:shadow-2xl
                                      transition-all duration-300
                                      hover:-rotate-2
                                      relative group overflow-hidden"
                               style="font-size: {{ $tag->font_size * 0.8 }}rem;">

                                {{-- Hover Glow Effect --}}
                                <span class="absolute inset-0 bg-white/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-full"></span>

                                {{-- Text --}}
                                <span class="relative z-10 flex items-center gap-2">
                                    @if($tag->is_popular)
                                        <i class="fa-solid fa-star text-yellow-300"></i>
                                    @endif
                                    {{ $tag->query }}
                                    <span class="text-xs opacity-75">({{ $tag->search_count }})</span>
                                </span>

                                {{-- Sparkle Animation --}}
                                <span class="absolute -top-1 -right-1 w-3 h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-ping"></span>
                            </a>
                        @empty
                            <div class="text-center py-12 w-full">
                                <div class="text-6xl mb-4">🔍</div>
                                <p class="text-2xl font-bold text-gray-400 dark:text-gray-500 mb-2">
                                    Henüz arama yapılmamış
                                </p>
                                <p class="text-gray-500 dark:text-gray-400">
                                    İlk arayan siz olun!
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Fun Stats --}}
                @if(isset($searchTags) && $searchTags->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                        @php
                            $totalSearches = $searchTags->sum('search_count');
                            $avgSearches = $searchTags->count() > 0 ? round($totalSearches / $searchTags->count(), 1) : 0;
                            $mostSearched = $searchTags->sortByDesc('search_count')->first();
                        @endphp

                        <div class="bg-gradient-to-br from-blue-500 to-cyan-500 text-white rounded-2xl p-6 shadow-lg">
                            <div class="text-3xl font-black">{{ number_format($totalSearches) }}</div>
                            <div class="text-sm opacity-90">Toplam Arama</div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-500 to-pink-500 text-white rounded-2xl p-6 shadow-lg">
                            <div class="text-3xl font-black">{{ number_format($searchTags->count()) }}</div>
                            <div class="text-sm opacity-90">Farklı Kelime</div>
                        </div>

                        <div class="bg-gradient-to-br from-green-500 to-emerald-500 text-white rounded-2xl p-6 shadow-lg">
                            <div class="text-3xl font-black">{{ $avgSearches }}</div>
                            <div class="text-sm opacity-90">Ortalama Arama</div>
                        </div>

                        <div class="bg-gradient-to-br from-orange-500 to-red-500 text-white rounded-2xl p-6 shadow-lg">
                            <div class="text-3xl font-black">🔥</div>
                            <div class="text-sm opacity-90 truncate">{{ $mostSearched->query ?? 'N/A' }}</div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Popular Searches --}}
                @if(isset($popularSearches) && $popularSearches->count() > 0)
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-2xl font-black mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-star text-yellow-500"></i>
                            Popüler
                        </h3>
                        <div class="space-y-2">
                            @foreach($popularSearches as $search)
                                <a href="{{ route('search.show', ['query' => $search->query]) }}"
                                   class="block px-4 py-3 bg-gradient-to-r from-yellow-100 to-orange-100 dark:from-yellow-900/30 dark:to-orange-900/30
                                          hover:from-yellow-200 hover:to-orange-200 dark:hover:from-yellow-800/40 dark:hover:to-orange-800/40
                                          rounded-xl transition-all font-semibold text-gray-800 dark:text-gray-200
                                          hover:scale-105 hover:shadow-lg">
                                    <i class="fa-solid fa-fire text-orange-500 mr-2"></i>
                                    {{ $search->query }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Recent Searches --}}
                @if(isset($recentSearches) && $recentSearches->count() > 0)
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-2xl font-black mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-clock text-blue-500"></i>
                            Son Aramalar
                        </h3>
                        <div class="space-y-2">
                            @foreach($recentSearches->take(10) as $search)
                                <a href="{{ route('search.show', ['query' => $search->query]) }}"
                                   class="block px-4 py-2 bg-gray-100 dark:bg-slate-700/50
                                          hover:bg-gray-200 dark:hover:bg-slate-600
                                          rounded-lg transition-all text-sm text-gray-700 dark:text-gray-300
                                          hover:translate-x-1">
                                    {{ $search->query }}
                                    <span class="text-xs text-gray-400 ml-2">
                                        {{ \Carbon\Carbon::parse($search->last_searched)->diffForHumans() }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- CTA Card --}}
                <div class="bg-gradient-to-br from-purple-600 to-pink-600 text-white rounded-3xl shadow-xl p-6">
                    <div class="text-4xl mb-3">🎯</div>
                    <h3 class="text-2xl font-black mb-2">Bulamadınız mı?</h3>
                    <p class="text-sm opacity-90 mb-4">
                        Aradığınız ürünü bulamadıysanız bize ulaşın!
                    </p>
                    <a href="{{ url('/contact') }}"
                       class="block w-full text-center px-6 py-3 bg-white text-purple-600 font-bold rounded-xl hover:bg-gray-100 transition-all hover:scale-105">
                        İletişime Geç
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}
</style>
@endsection
