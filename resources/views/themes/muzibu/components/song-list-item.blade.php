@props([
    'song',
    'index' => null,
    'showAlbum' => true,
    'showDuration' => true,
    'compact' => false
])

@php
    $coverUrl = $song->getCoverUrl(120, 120) ?? '/images/default-cover.png';
    $artistName = $song->album->artist->title ?? __('muzibu::front.dashboard.unknown_artist');
    $albumTitle = $song->album->title ?? '';
    $duration = $song->duration ? gmdate('i:s', $song->duration) : '';
@endphp

<div class="group flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 transition cursor-pointer {{ $compact ? 'py-1' : 'py-2' }}"
     @click="playSong({{ $song->song_id }})"
     data-song-id="{{ $song->song_id }}">

    {{-- Index/Play Icon --}}
    @if($index !== null)
        <div class="w-6 text-center flex-shrink-0">
            <span class="text-gray-500 text-sm group-hover:hidden">{{ $index + 1 }}</span>
            <i class="fas fa-play text-white text-xs hidden group-hover:inline"></i>
        </div>
    @endif

    {{-- Cover --}}
    <div class="relative w-14 h-14 rounded overflow-hidden flex-shrink-0">
        <img src="{{ $coverUrl }}" alt="{{ $song->title }}" class="w-full h-full object-cover" loading="lazy">
        @if($index === null)
            <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                <i class="fas fa-play text-white text-xs"></i>
            </div>
        @endif
    </div>

    {{-- Info --}}
    <div class="flex-1 min-w-0">
        <p class="text-white text-sm font-medium truncate">{{ $song->title }}</p>
        <p class="text-gray-400 text-xs truncate">
            {{ $artistName }}
            @if($showAlbum && $albumTitle)
                <span class="text-gray-600 mx-1">•</span>
                {{ $albumTitle }}
            @endif
        </p>
    </div>

    {{-- Duration --}}
    @if($showDuration && $duration)
        <span class="text-gray-500 text-xs hidden sm:block">{{ $duration }}</span>
    @endif

    {{-- Actions --}}
    <div class="opacity-0 group-hover:opacity-100 transition flex items-center gap-1">
        <button @click.stop="$store.favorites.toggle('song', {{ $song->song_id }})"
                class="p-1.5 hover:text-red-400 transition"
                x-bind:class="$store.favorites.isFavorite('song', {{ $song->song_id }}) ? 'text-muzibu-coral' : 'text-gray-400'"
                x-bind:title="$store.favorites.isFavorite('song', {{ $song->song_id }}) ? 'Favorilerden çıkar' : 'Favorilere ekle'">
            <i class="text-sm"
               x-bind:class="$store.favorites.isFavorite('song', {{ $song->song_id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
        </button>
        <button @click.stop="addToQueue({{ $song->song_id }})"
                class="p-1.5 text-gray-400 hover:text-white transition" title="{{ __('muzibu::front.player.queue') }}">
            <i class="fas fa-list text-sm"></i>
        </button>
    </div>
</div>
