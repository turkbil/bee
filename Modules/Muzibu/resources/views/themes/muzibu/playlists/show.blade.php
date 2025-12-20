@extends('themes.muzibu.layouts.app')

@section('content')
<!-- Playlist Header -->
<section class="relative h-80 mb-8 bg-gradient-to-b from-purple-900 via-purple-800 to-transparent">
    <div class="container mx-auto px-8 h-full flex items-end pb-8">
        @if($playlist->coverMedia)
            <img src="{{ thumb($playlist->coverMedia, 232, 232) }}"
                 alt="{{ $playlist->getTranslation('title', app()->getLocale()) }}"
                 class="w-58 h-58 rounded-lg shadow-2xl mr-6">
        @else
            <div class="w-58 h-58 bg-gradient-to-br from-green-500 to-blue-600 rounded-lg flex items-center justify-center shadow-2xl mr-6">
                <span class="text-6xl">🎵</span>
            </div>
        @endif
        <div class="flex-1">
            <p class="text-sm font-semibold text-white mb-2">PLAYLIST</p>
            <h1 class="text-6xl font-black mb-4 text-white drop-shadow-2xl">
                {{ $playlist->title['tr'] ?? $playlist->title['en'] ?? 'Playlist' }}
            </h1>
            @if($playlist->description)
                <p class="text-lg text-white/90 mb-4">
                    {{ $playlist->description['tr'] ?? $playlist->description['en'] ?? '' }}
                </p>
            @endif
            <p class="text-sm text-white/70">{{ $songs->count() }} şarkı</p>
        </div>
    </div>
</section>

<!-- Player Controls -->
<section class="px-8 mb-8">
    <div class="flex items-center gap-6">
        <button @click="playPlaylist({{ $playlist->playlist_id }})"
                class="w-14 h-14 bg-spotify-green hover:bg-spotify-green-light rounded-full flex items-center justify-center hover:scale-105 transition-all shadow-lg">
            <i class="fas fa-play text-black text-xl ml-0.5"></i>
        </button>
        <button @click="toggleFavorite('playlist', {{ $playlist->playlist_id }})"
                class="text-gray-400 hover:text-white transition-all text-3xl">
            <i :class="isFavorite('playlist', {{ $playlist->playlist_id }}) ? 'fas fa-heart text-spotify-green' : 'far fa-heart'"></i>
        </button>
    </div>
</section>

<!-- Songs List -->
<section class="px-8 pb-12">
    <div class="space-y-1">
        @foreach($songs as $index => $song)
            <x-muzibu.song-row :song="$song" :index="$index" :show-album="true" />
        @endforeach
    </div>
</section>
@endsection
