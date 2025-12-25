@props(['song', 'index' => 0, 'showAlbum' => false, 'contextData' => []])

{{-- Muzibu Song Detail Row Component --}}
{{-- Usage: <x-muzibu.song-detail-row :song="$song" :index="$index" :show-album="true" :context-data="['album_id' => $album->id]" /> --}}
{{-- Features: Grid layout, cover image, album link, context menu, touch events --}}

<div
    x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', {
        id: {{ $song->song_id }},
        title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
        artist: '{{ $song->artist ? addslashes($song->artist->getTranslation('title', app()->getLocale())) : '' }}',
        album_id: {{ isset($contextData['album_id']) ? $contextData['album_id'] : ($song->album ? $song->album->id : 'null') }},
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
                id: {{ $song->song_id }},
                title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
                artist: '{{ $song->artist ? addslashes($song->artist->getTranslation('title', app()->getLocale())) : '' }}',
                album_id: {{ isset($contextData['album_id']) ? $contextData['album_id'] : ($song->album ? $song->album->id : 'null') }},
                is_favorite: {{ auth()->check() && method_exists($song, 'isFavoritedBy') && $song->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
            });
        }, 500);
    "
    x-on:touchend="clearTimeout(touchTimer)"
    x-on:touchmove="
        const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                     Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
        if (moved) clearTimeout(touchTimer);
    "
    class="group grid {{ $showAlbum ? 'grid-cols-[40px_50px_1fr_60px] md:grid-cols-[40px_50px_6fr_4fr_100px_60px]' : 'grid-cols-[40px_50px_1fr_60px] md:grid-cols-[40px_50px_1fr_100px_60px]' }} gap-2 sm:gap-4 px-2 sm:px-4 py-2 sm:py-3 rounded-lg hover:bg-white/10 transition-all cursor-pointer items-center"
    @click="$dispatch('play-song', { songId: {{ $song->song_id }} })">

    {{-- Number/Play Icon --}}
    <div class="text-center flex-shrink-0">
        <span class="text-gray-400 text-xs sm:text-sm group-hover:hidden">{{ $index + 1 }}</span>
        <button
            @click="$dispatch('play-song', { songId: {{ $song->song_id }} })"
            class="hidden group-hover:inline-block text-white hover:scale-110 transition-transform"
        >
            <i class="fas fa-play text-sm"></i>
        </button>
    </div>

    {{-- Song Cover Image --}}
    <div class="w-12 h-12 rounded overflow-hidden shadow-lg flex-shrink-0">
        <x-muzibu.lazy-image
            :src="$song->getCoverUrl(120, 120)"
            :alt="$song->getTranslation('title', app()->getLocale())"
            wrapper-class="relative w-full h-full"
            class="w-full h-full object-cover"
        />
    </div>

    {{-- Title & Artist --}}
    <div class="flex-1 min-w-0">
        <h3 class="text-white font-medium text-sm sm:text-base truncate group-hover:text-muzibu-coral transition-colors">
            {{ $song->getTranslation('title', app()->getLocale()) }}
        </h3>
        @if($song->artist && !$showAlbum)
            <p class="text-xs sm:text-sm text-gray-400 truncate md:hidden">
                {{ $song->artist->getTranslation('title', app()->getLocale()) }}
            </p>
        @elseif($song->artist)
            <p class="text-xs sm:text-sm text-gray-400 truncate">
                {{ $song->artist->getTranslation('title', app()->getLocale()) }}
            </p>
        @endif
    </div>

    {{-- Album - Desktop Only (if showAlbum is true) --}}
    @if($showAlbum)
        <div class="hidden md:block truncate" @click.stop>
            @if($song->album)
                <a href="/albums/{{ $song->album->getTranslation('slug', app()->getLocale()) }}"

                   class="text-sm text-gray-400 hover:text-white hover:underline transition-colors">
                    {{ $song->album->getTranslation('title', app()->getLocale()) }}
                </a>
            @endif
        </div>
    @endif

    {{-- Duration - Desktop Only --}}
    <div class="hidden md:block text-right text-sm text-gray-400 flex-shrink-0">
        {{ gmdate('i:s', $song->duration ?? 0) }}
    </div>

    {{-- Actions: Favorite + Menu --}}
    <div @click.stop class="flex items-center gap-1 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
        {{-- Favorite Button --}}
        @php
            $isFavorited = auth()->check() && method_exists($song, 'isFavoritedBy') && $song->isFavoritedBy(auth()->id());
        @endphp

        <button class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-muzibu-coral transition-colors hover:scale-110 rounded-full hover:bg-white/10 {{ $isFavorited ? '!opacity-100' : '' }}"
                x-on:click.stop.prevent="$store.favorites.toggle('song', {{ $song->song_id }})"
                x-bind:class="$store.favorites.isFavorite('song', {{ $song->song_id }}) ? 'text-muzibu-coral' : ''">
            <i class="text-sm"
               x-bind:class="$store.favorites.isFavorite('song', {{ $song->song_id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
        </button>

        {{-- 3-Dot Menu Button (Context Menu Trigger) --}}
        <button @click.stop="$store.contextMenu.openContextMenu($event, 'song', {
                    id: {{ $song->song_id }},
                    title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
                    artist: '{{ $song->artist ? addslashes($song->artist->getTranslation('title', app()->getLocale())) : '' }}',
                    album_id: {{ isset($contextData['album_id']) ? $contextData['album_id'] : ($song->album ? $song->album->id : 'null') }},
                    is_favorite: {{ auth()->check() && method_exists($song, 'isFavoritedBy') && $song->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
                })"
                class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
            <i class="fas fa-ellipsis-v text-sm"></i>
        </button>
    </div>
</div>
<\!-- TEST: MODULES VERSION -->
