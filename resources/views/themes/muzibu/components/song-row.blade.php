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
     data-song-id="{{ $songId }}"
     x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', {
        id: {{ $songId }},
        title: '{{ addslashes($song->title) }}',
        artist: '{{ addslashes($artistName) }}',
        album_id: {{ $song->album_id ?? 'null' }},
        album_slug: '{{ $song->album?->slug ?? '' }}',
        artist_id: {{ $song->artist?->artist_id ?? ($song->album?->artist?->artist_id ?? 'null') }},
        artist_slug: '{{ $song->artist?->slug ?? ($song->album?->artist?->slug ?? '') }}',
        is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }}
    })"
     x-data="{
        touchTimer: null,
        touchStartPos: { x: 0, y: 0 }
    }"
     x-on:touchstart="
        touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY };
        touchTimer = setTimeout(() => {
            if (navigator.vibrate) navigator.vibrate(50);
            $store.contextMenu.openContextMenu({
                clientX: $event.touches[0].clientX,
                clientY: $event.touches[0].clientY
            }, 'song', {
                id: {{ $songId }},
                title: '{{ addslashes($song->title) }}',
                artist: '{{ addslashes($artistName) }}',
                album_id: {{ $song->album_id ?? 'null' }},
                album_slug: '{{ $song->album?->slug ?? '' }}',
                artist_id: {{ $song->artist?->artist_id ?? ($song->album?->artist?->artist_id ?? 'null') }},
                artist_slug: '{{ $song->artist?->slug ?? ($song->album?->artist?->slug ?? '') }}',
                is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }}
            });
        }, 500);
    "
     x-on:touchend="clearTimeout(touchTimer)"
     x-on:touchmove="
        const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                     Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
        if (moved) clearTimeout(touchTimer);
    ">

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
            <button @click.stop="$store.contextMenu.openContextMenu($event, 'song', {
                        id: {{ $songId }},
                        title: '{{ addslashes($song->title) }}',
                        artist: '{{ addslashes($artistName) }}',
                        album_id: {{ $song->album_id ?? 'null' }},
                        album_slug: '{{ $song->album?->slug ?? '' }}',
                        artist_id: {{ $song->artist?->artist_id ?? ($song->album?->artist?->artist_id ?? 'null') }},
                        artist_slug: '{{ $song->artist?->slug ?? ($song->album?->artist?->slug ?? '') }}',
                        is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }}
                    })"
                    class="p-2 transition rounded-full hover:bg-white/10 text-gray-400 hover:text-white">
                <i class="fas fa-ellipsis-v text-sm"></i>
            </button>
        </div>
    @endif
</div>
