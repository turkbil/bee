@extends('themes.muzibu.layouts.app')

@section('content')
{{-- Hero Section - Full Width Background Image (Spotify Mobile Style) --}}
<div class="relative overflow-hidden">
    @php
        $heroMedia = $genre->getFirstMedia('hero');
        $heroUrl = $heroMedia ? thumb($heroMedia, 1200, 800, ['scale' => 1]) : null;
    @endphp
    {{-- Full Width Background Image --}}
    @if($heroUrl)
        <div class="relative w-full aspect-[4/3] sm:aspect-[16/9] md:aspect-[21/9]">
            <img src="{{ $heroUrl }}"
                 alt="{{ $genre->getTranslation('title', app()->getLocale()) }}"
                 class="w-full h-full object-cover">
            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>

            {{-- Action Buttons - Top Right --}}
            <div class="absolute top-4 right-4 flex items-center gap-3">
                <x-common.favorite-button :model="$genre" size="lg" />
                {{-- Play Button --}}
                <button
                    @click="$dispatch('play-all-songs', { genreId: {{ $genre->genre_id }} })"
                    class="w-14 h-14 bg-muzibu-coral hover:scale-105 active:scale-95 rounded-full flex items-center justify-center shadow-xl transition-all">
                    <i class="fas fa-play text-white text-xl ml-1"></i>
                </button>
            </div>

            {{-- Content - Bottom Left --}}
            <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
                <p class="text-xs font-bold text-muzibu-coral uppercase tracking-widest mb-1">Tür</p>
                <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-black text-white mb-2 leading-tight drop-shadow-lg">
                    {{ $genre->getTranslation('title', app()->getLocale()) }}
                </h1>
                @if($genre->description)
                    <p class="text-sm text-white/80 mb-2 line-clamp-2 max-w-2xl">
                        {{ clean_html($genre->getTranslation('description', app()->getLocale())) }}
                    </p>
                @endif
                <p class="text-sm text-white/70">{{ $playlists->count() }} playlist</p>
            </div>
        </div>
    @else
        {{-- Fallback if no hero --}}
        <div class="relative w-full aspect-[4/3] sm:aspect-[16/9] bg-gradient-to-br from-green-900 to-slate-900">
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-8xl">🎸</span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>

            {{-- Action Buttons --}}
            <div class="absolute top-4 right-4 flex items-center gap-3">
                <x-common.favorite-button :model="$genre" size="lg" />
                <button
                    @click="$dispatch('play-all-songs', { genreId: {{ $genre->genre_id }} })"
                    class="w-14 h-14 bg-muzibu-coral rounded-full flex items-center justify-center shadow-xl">
                    <i class="fas fa-play text-white text-xl ml-1"></i>
                </button>
            </div>

            {{-- Content --}}
            <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
                <p class="text-xs font-bold text-muzibu-coral uppercase tracking-widest mb-1">Tür</p>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white mb-2">
                    {{ $genre->getTranslation('title', app()->getLocale()) }}
                </h1>
                <p class="text-sm text-white/70">{{ $playlists->count() }} playlist</p>
            </div>
        </div>
    @endif
</div>

{{-- Playlists Section --}}
<div class="px-4 sm:px-6 pt-6">
    @if($playlists && $playlists->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
            @foreach($playlists as $playlist)
                <x-muzibu.playlist-card :playlist="$playlist" :preview="true" />
            @endforeach
        </div>
    @else
        <div class="text-center py-16 sm:py-20">
            <div class="mb-6">
                <i class="fas fa-music text-gray-600 text-5xl sm:text-6xl"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">Bu türde henüz playlist yok</h3>
            <p class="text-sm sm:text-base text-gray-400">Yakında yeni playlistler eklenecek</p>
        </div>
    @endif
</div>
@endsection
