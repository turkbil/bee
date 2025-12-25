@props(['song', 'index' => 0, 'showAlbum' => false])

{{-- Muzibu Song Row Component --}}
{{-- Usage: <x-muzibu.song-row :song="$song" :index="$index" :show-album="true" /> --}}
{{-- Features: Album name, infinite queue data, playing indicator, favorite always visible for favorited songs --}}

<div class="group flex items-center gap-2 sm:gap-4 px-2 sm:px-4 py-2 sm:py-3 rounded-lg hover:bg-white/5 transition-all cursor-pointer"
     {{-- Infinite Queue Data Attributes --}}
     data-song-id="{{ $song->id }}"
     data-album-id="{{ $song->album_id ?? '' }}"
     data-genre-id="{{ $song->genre_id ?? ($song->album->genre_id ?? '') }}"
     data-context-type="song"
     {{-- Playing State (JS will add this class when playing) --}}
     x-bind:class="$store.player.currentSong?.id === {{ $song->id }} ? 'bg-muzibu-coral/10 border-l-4 border-muzibu-coral' : ''"
     x-on:click="window.playSong ? window.playSong({{ $song->id }}, {{ $song->album_id ?? 'null' }}, {{ $song->genre_id ?? ($song->album->genre_id ?? 'null') }}) : $store.player.playSong({{ $song->id }}, {{ $song->album_id ?? 'null' }}, {{ $song->genre_id ?? ($song->album->genre_id ?? 'null') }})"
     x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', {
        id: {{ $song->id }},
        title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
        artist: '{{ $song->album && $song->album->artist ? addslashes($song->album->artist->getTranslation('title', app()->getLocale())) : '' }}',
        cover_url: '{{ $song->getCoverUrl(300, 300) ?? '' }}',
        album_id: {{ $song->album_id ?? 'null' }},
        album_slug: '{{ $song->album?->slug ?? '' }}',
        is_favorite: {{ auth()->check() && method_exists($song, 'isFavoritedBy') && $song->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
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
                id: {{ $song->id }},
                title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
                artist: '{{ $song->album && $song->album->artist ? addslashes($song->album->artist->getTranslation('title', app()->getLocale())) : '' }}',
                cover_url: '{{ $song->getCoverUrl(300, 300) ?? '' }}',
                album_id: {{ $song->album_id ?? 'null' }},
                album_slug: '{{ $song->album?->slug ?? '' }}',
                is_favorite: {{ auth()->check() && method_exists($song, 'isFavoritedBy') && $song->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
            });
        }, 500);
    "
     x-on:touchend="clearTimeout(touchTimer)"
     x-on:touchmove="
        const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                     Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
        if (moved) clearTimeout(touchTimer);
    ">

    {{-- Index / Playing Indicator --}}
    <span class="w-6 sm:w-8 text-center text-xs sm:text-sm flex-shrink-0">
        {{-- Playing: Animated Waveform Icon --}}
        <span x-show="$store.player.currentSong?.id === {{ $song->id }} && $store.player.isPlaying"
              x-cloak
              class="text-muzibu-coral">
            <i class="fas fa-waveform-lines animate-pulse"></i>
        </span>
        {{-- Paused: Play Icon --}}
        <span x-show="$store.player.currentSong?.id === {{ $song->id }} && !$store.player.isPlaying"
              x-cloak
              class="text-muzibu-coral">
            <i class="fas fa-play"></i>
        </span>
        {{-- Not Playing: Index Number --}}
        <span x-show="$store.player.currentSong?.id !== {{ $song->id }}"
              class="text-gray-400">
            {{ $index + 1 }}
        </span>
    </span>

    {{-- Song/Album Cover (Optional - if showAlbum is true) --}}
    @if($showAlbum)
        @php $coverUrl = $song->getCoverUrl(48, 48); @endphp
        @if($coverUrl)
            <img src="{{ $coverUrl }}"
                 alt="{{ $song->getTranslation('title', app()->getLocale()) }}"
                 class="w-10 h-10 sm:w-12 sm:h-12 rounded shadow-md flex-shrink-0"
                 loading="lazy">
        @else
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded shadow-md flex-shrink-0 bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center">
                <i class="fas fa-music text-gray-600 text-xs"></i>
            </div>
        @endif
    @endif

    {{-- Song Info --}}
    <div class="flex-1 min-w-0">
        {{-- Song Name --}}
        <h3 class="text-white font-medium text-sm sm:text-base truncate"
            x-bind:class="$store.player.currentSong?.id === {{ $song->id }} ? 'text-muzibu-coral font-bold' : ''">
            {{ $song->getTranslation('title', app()->getLocale()) }}
        </h3>

        {{-- Album Name (NEW - Always show if song has album) --}}
        @if($song->album)
            <p class="text-xs sm:text-sm text-gray-400 truncate mt-0.5">
                {{ $song->album->getTranslation('title', app()->getLocale()) }}
            </p>
        @endif
    </div>

    {{-- Duration --}}
    <span class="text-xs sm:text-sm text-gray-400 hidden sm:block flex-shrink-0">
        {{ gmdate('i:s', $song->duration ?? 0) }}
    </span>

    {{-- Actions (Hover for unfavorited, Always visible for favorited) --}}
    <div class="flex items-center gap-2 flex-shrink-0 transition-opacity"
         x-bind:class="$store.player.currentSong?.id === {{ $song->id }} ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'"
         @click.stop.prevent>

        {{-- Favorite Button --}}
        @php
            $isFavorited = auth()->check() && method_exists($song, 'isFavoritedBy') && $song->isFavoritedBy(auth()->id());
        @endphp

        <button class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center text-gray-400 hover:text-muzibu-coral transition-colors hover:scale-110 {{ $isFavorited ? '!opacity-100' : '' }}"
                x-on:click.stop.prevent="$store.favorites.toggle('song', {{ $song->id }})"
                x-bind:class="$store.favorites.isFavorite('song', {{ $song->id }}) ? 'text-muzibu-coral' : ''">
            <i class="text-sm sm:text-base"
               x-bind:class="$store.favorites.isFavorite('song', {{ $song->id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
        </button>

        {{-- 3-Dot Menu Button (Context Menu Trigger) --}}
        <button @click.stop="$store.contextMenu.openContextMenu($event, 'song', {
                    id: {{ $song->id }},
                    title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
                    artist: '{{ $song->album && $song->album->artist ? addslashes($song->album->artist->getTranslation('title', app()->getLocale())) : '' }}',
                    cover_url: '{{ $song->getCoverUrl(300, 300) ?? '' }}',
                    album_id: {{ $song->album_id ?? 'null' }},
                    album_slug: '{{ $song->album?->slug ?? '' }}',
                    is_favorite: {{ auth()->check() && method_exists($song, 'isFavoritedBy') && $song->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
                })"
                class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
            <i class="fas fa-ellipsis-v text-sm"></i>
        </button>
    </div>
</div>
