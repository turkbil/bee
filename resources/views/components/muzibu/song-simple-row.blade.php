@props([
    'song',
    'index' => 0,
    'contextData' => []
])

{{-- SIMPLE SONG ROW - Thumbnail + Favorite + 3-Dot Menu Design --}}
{{-- Usage: <x-muzibu.song-simple-row :song="$song" :index="$index" /> --}}
{{-- Features: Thumbnail, Play overlay, Favorite, 3-Dot menu, Context menu --}}

<div class="group flex items-center gap-3 px-2 py-2.5 hover:bg-white/5 cursor-pointer transition-all"
     @click="$dispatch('play-song', { songId: {{ $song->song_id }} })"
     x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', {
         id: {{ $song->song_id }},
         title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
         artist: '{{ $song->artist ? addslashes($song->artist->getTranslation('title', app()->getLocale())) : '' }}',
         album_id: {{ isset($contextData['album_id']) ? $contextData['album_id'] : ($song->album_id ?? 'null') }},
         is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }}
     })"
     x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
     x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'song', { id: {{ $song->song_id }}, title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}', artist: '{{ $song->artist ? addslashes($song->artist->getTranslation('title', app()->getLocale())) : '' }}', album_id: {{ isset($contextData['album_id']) ? $contextData['album_id'] : ($song->album_id ?? 'null') }}, is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }} }); }, 500);"
     x-on:touchend="clearTimeout(touchTimer)"
     x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer)">

    {{-- Thumbnail with Play Overlay --}}
    @php $coverUrl = $song->getCoverUrl(120, 120); @endphp
    <div class="relative w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-gradient-to-br from-muzibu-coral to-orange-600">
        @if($coverUrl)
            <img src="{{ $coverUrl }}" alt="{{ $song->getTranslation('title', app()->getLocale()) }}" class="w-full h-full object-cover" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-music text-white/50 text-xs"></i>
            </div>
        @endif
        {{-- Play Overlay on Hover --}}
        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
            <i class="fas fa-play text-white text-sm"></i>
        </div>
    </div>

    {{-- Song Info --}}
    <div class="flex-1 min-w-0 overflow-hidden cursor-pointer">
        <h4 class="text-white text-sm font-medium truncate whitespace-nowrap group-hover:text-muzibu-coral transition-colors">
            {{ $song->getTranslation('title', app()->getLocale()) }}
        </h4>
        <p class="text-gray-400 text-xs truncate whitespace-nowrap">
            {{ $song->artist ? $song->artist->getTranslation('title', app()->getLocale()) : '' }}
        </p>
    </div>

    {{-- Actions (Favorite + Time/3-Dot) - Fixed Width --}}
    <div class="flex items-center gap-1 flex-shrink-0">
        {{-- Favorite Button (Always Same Position) --}}
        <button @click.stop="$store.favorites.toggle('song', {{ $song->song_id }})"
                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/10 transition-all"
                x-bind:class="$store.favorites.isFavorite('song', {{ $song->song_id }}) ? 'text-muzibu-coral opacity-100' : 'text-gray-400 opacity-0 group-hover:opacity-100'">
            <i class="text-sm"
               x-bind:class="$store.favorites.isFavorite('song', {{ $song->song_id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
        </button>

        {{-- Duration / 3-Dot Menu (Same Width, Toggle on Hover) --}}
        <div class="w-8 h-8 flex items-center justify-center">
            {{-- Duration (Default State) --}}
            <div class="text-gray-500 text-xs group-hover:hidden">
                {{ $song->duration ? gmdate('i:s', $song->duration) : '--:--' }}
            </div>

            {{-- 3-Dot Menu Button (Hover State) --}}
            <button @click.stop="$store.contextMenu.openContextMenu($event, 'song', {
                        id: {{ $song->song_id }},
                        title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
                        artist: '{{ $song->artist ? addslashes($song->artist->getTranslation('title', app()->getLocale())) : '' }}',
                        album_id: {{ isset($contextData['album_id']) ? $contextData['album_id'] : ($song->album_id ?? 'null') }},
                        is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }}
                    })"
                    class="hidden group-hover:flex items-center justify-center w-full h-full rounded-full text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
                <i class="fas fa-ellipsis-v text-sm"></i>
            </button>
        </div>
    </div>
</div>
