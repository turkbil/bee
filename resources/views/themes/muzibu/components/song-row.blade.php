@props([
    'song',
    'index' => null,
    'showAlbum' => true,
    'showDuration' => true,
    'showActions' => true
])

@php
    // Şarkının kendi görseli öncelikli, yoksa albüm görseli (quality=90)
    $coverUrl = $song->getCoverUrl(80, 80) ?? '/images/default-cover.png';
    $artistName = $song->album->artist->title ?? __('muzibu::front.dashboard.unknown_artist');
    $albumTitle = $song->album->title ?? '';
    $duration = $song->duration ? gmdate('i:s', $song->duration) : '';
    $songId = $song->song_id ?? $song->id;
@endphp

<div class="group flex items-center gap-4 p-3 rounded-lg hover:bg-white/5 transition cursor-pointer"
     @click="playSong({{ $songId }})"
     data-song-id="{{ $songId }}">

    {{-- Index/Play --}}
    @if($index !== null)
        <div class="w-8 text-center flex-shrink-0">
            <span class="text-gray-500 group-hover:hidden">{{ $index + 1 }}</span>
            <i class="fas fa-play text-white hidden group-hover:inline"></i>
        </div>
    @endif

    {{-- Cover --}}
    <div class="relative w-12 h-12 rounded-lg overflow-hidden flex-shrink-0">
        <img src="{{ $coverUrl }}" alt="{{ $song->title }}" class="w-full h-full object-cover" loading="lazy">
        @if($index === null)
            <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                <i class="fas fa-play text-white text-sm"></i>
            </div>
        @endif
    </div>

    {{-- Title & Artist --}}
    <div class="flex-1 min-w-0">
        <p class="text-white font-medium truncate">{{ $song->title }}</p>
        <p class="text-gray-400 text-sm truncate">{{ $artistName }}</p>
    </div>

    {{-- Album --}}
    @if($showAlbum && $albumTitle)
        <div class="hidden md:block flex-1 min-w-0">
            <a href="/albums/{{ $song->album->slug ?? $song->album_id }}"
               class="text-gray-400 text-sm hover:text-white hover:underline truncate block"
               @click.stop data-spa>
                {{ $albumTitle }}
            </a>
        </div>
    @endif

    {{-- Duration --}}
    @if($showDuration && $duration)
        <span class="text-gray-500 text-sm w-12 text-right hidden sm:block">{{ $duration }}</span>
    @endif

    {{-- Actions --}}
    @if($showActions)
        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
            <button @click.stop="$store.favorites.toggle('song', {{ $songId }})"
                    class="p-2 transition rounded-full hover:bg-white/10"
                    x-bind:class="$store.favorites.isFavorite('song', {{ $songId }}) ? 'text-muzibu-coral' : 'text-gray-400'"
                    x-bind:title="$store.favorites.isFavorite('song', {{ $songId }}) ? 'Favorilerden çıkar' : 'Favorilere ekle'">
                <i x-bind:class="$store.favorites.isFavorite('song', {{ $songId }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
            </button>
            <x-muzibu.song-actions-menu :song="$song" :showPlay="false" />
        </div>
    @endif
</div>
