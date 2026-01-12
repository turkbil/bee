@extends('themes.muzibu.layouts.app')

@section('content')
{{-- Hero Section - Full Width Background Image (Spotify Mobile Style) --}}
<div class="relative overflow-hidden">
    {{-- Full Width Background Image --}}
    @if($playlist->getCoverUrl())
        <div class="relative w-full aspect-[4/3] sm:aspect-[16/9] md:aspect-[21/9]">
            <img src="{{ $playlist->getCoverUrl(1200, 800) }}"
                 alt="{{ $playlist->getTranslation('title', app()->getLocale()) }}"
                 class="w-full h-full object-cover">
            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>

            {{-- Action Buttons - Top Right --}}
            <div class="absolute top-4 right-4 flex items-center gap-3">
                <x-common.favorite-button :model="$playlist" size="lg" />
                {{-- Play Button --}}
                <button
                    @click="$dispatch('play-all-songs', { playlistId: {{ $playlist->playlist_id }} })"
                    class="w-14 h-14 bg-muzibu-coral hover:scale-105 active:scale-95 rounded-full flex items-center justify-center shadow-xl transition-all">
                    <i class="fas fa-play text-white text-xl ml-1"></i>
                </button>
            </div>

            {{-- Content - Bottom Left --}}
            <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
                <p class="text-xs font-bold text-muzibu-coral uppercase tracking-widest mb-1">Playlist</p>
                <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-black text-white mb-2 leading-tight drop-shadow-lg">
                    {{ $playlist->getTranslation('title', app()->getLocale()) }}
                </h1>
                @if($playlist->description)
                    <p class="text-sm text-white/80 mb-2 line-clamp-2 max-w-2xl">
                        {{ clean_html($playlist->getTranslation('description', app()->getLocale())) }}
                    </p>
                @endif
                <p class="text-sm text-white/70">{{ $songs->total() }} şarkı</p>
            </div>
        </div>
    @else
        {{-- Fallback if no cover --}}
        <div class="relative w-full aspect-[4/3] sm:aspect-[16/9] bg-gradient-to-br from-purple-900 to-slate-900">
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-8xl">🎵</span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>

            {{-- Action Buttons --}}
            <div class="absolute top-4 right-4 flex items-center gap-3">
                <x-common.favorite-button :model="$playlist" size="lg" />
                <button
                    @click="$dispatch('play-all-songs', { playlistId: {{ $playlist->playlist_id }} })"
                    class="w-14 h-14 bg-muzibu-coral rounded-full flex items-center justify-center shadow-xl">
                    <i class="fas fa-play text-white text-xl ml-1"></i>
                </button>
            </div>

            {{-- Content --}}
            <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
                <p class="text-xs font-bold text-muzibu-coral uppercase tracking-widest mb-1">Playlist</p>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white mb-2">
                    {{ $playlist->getTranslation('title', app()->getLocale()) }}
                </h1>
                <p class="text-sm text-white/70">{{ $songs->total() }} şarkı</p>
            </div>
        </div>
    @endif
</div>

{{-- Songs List Section --}}
<div class="px-4 sm:px-6 pt-6">
    {{-- Songs List - Modern Table Style --}}
    @if($songs && $songs->count() > 0)
        {{-- Table Header - Desktop Only --}}
        <div class="hidden md:grid grid-cols-[40px_50px_6fr_4fr_100px_60px] gap-4 px-4 py-2 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-white/5">
            <div class="text-center">#</div>
            <div></div>
            <div>Başlık</div>
            <div>Albüm</div>
            <div class="text-right">Süre</div>
            <div></div>
        </div>

        <div class="space-y-0">
            @foreach($songs as $index => $song)
                @php
                    $realIndex = ($songs->currentPage() - 1) * $songs->perPage() + $index;
                @endphp
                <x-muzibu.song-detail-row :song="$song" :index="$realIndex" :show-album="true" />
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($songs->hasPages())
            <div class="mt-8">
                {{ $songs->links('themes.muzibu.partials.pagination') }}
            </div>
        @endif
    @else
        <div class="text-center py-16 sm:py-20">
            <div class="mb-6">
                <i class="fas fa-music text-gray-600 text-5xl sm:text-6xl"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">Bu playlist'te henüz şarkı yok</h3>
            <p class="text-sm sm:text-base text-gray-400">Şarkı ekleyerek playlist'ini zenginleştir</p>
        </div>
    @endif
</div>
@endsection
