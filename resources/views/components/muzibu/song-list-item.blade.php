@props([
    'song',
    'index' => 0
])

{{-- SONG LIST ITEM - Unified Component for Blade Loops --}}
{{-- Usage: <x-muzibu.song-list-item :song="$song" :index="$index" /> --}}

<div class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer group transition-all"
     @click="$dispatch('play-song', { songId: {{ $song->song_id }} })"
     x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', {
        id: {{ $song->song_id }},
        title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
        artist: '{{ $song->artist ? addslashes($song->artist->getTranslation('title', app()->getLocale())) : '' }}',
        cover_url: '{{ $song->getCoverUrl(300, 300) ?? '' }}',
        album_id: {{ $song->album_id ?? 'null' }},
        album_slug: '{{ $song->album?->slug ?? '' }}',
        is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }}
    })"
     x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
     x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'song', { id: {{ $song->song_id }}, title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}', artist: '{{ $song->artist ? addslashes($song->artist->getTranslation('title', app()->getLocale())) : '' }}', cover_url: '{{ $song->getCoverUrl(300, 300) ?? '' }}', album_id: {{ $song->album_id ?? 'null' }}, album_slug: '{{ $song->album?->slug ?? '' }}', is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }} }); }, 500);"
     x-on:touchend="clearTimeout(touchTimer)"
     x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer)">

    {{-- Track Thumbnail with Play Overlay --}}
    @php $coverUrl = $song->getCoverUrl(120, 120); @endphp
    <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-gradient-to-br from-muzibu-coral to-orange-600 relative">
        @if($coverUrl)
            <img src="{{ $coverUrl }}" alt="{{ $song->getTranslation('title', app()->getLocale()) }}" class="w-full h-full object-cover" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-music text-gray-600 text-xs"></i>
            </div>
        @endif
        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
            <i class="fas fa-play text-white text-xs"></i>
        </div>
    </div>

    {{-- Track Info --}}
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-white truncate group-hover:text-muzibu-coral transition-colors">
            {{ $song->getTranslation('title', app()->getLocale()) }}
        </p>
        <p class="text-xs text-gray-500 truncate">
            {{ $song->artist ? $song->artist->getTranslation('title', app()->getLocale()) : '' }}
        </p>
    </div>

    {{-- Duration (hide on hover) --}}
    <div class="text-xs text-gray-600 flex-shrink-0 group-hover:hidden">
        {{ $song->duration ? gmdate('i:s', $song->duration) : '' }}
    </div>

    {{-- Actions (show on hover) --}}
    <div class="hidden group-hover:flex items-center gap-1 flex-shrink-0">
        <button @click.stop="$store.favorites.toggle('song', {{ $song->song_id }})"
                class="w-6 h-6 flex items-center justify-center rounded-full hover:bg-white/10 transition-colors"
                x-bind:class="$store.favorites.isFavorite('song', {{ $song->song_id }}) ? 'text-muzibu-coral' : 'text-gray-400'">
            <i class="text-xs"
               x-bind:class="$store.favorites.isFavorite('song', {{ $song->song_id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
        </button>
        <button @click.stop="Alpine.store('contextMenu').openContextMenu($event, 'song', { id: {{ $song->song_id }}, title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}' })"
                class="w-6 h-6 flex items-center justify-center rounded-full text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
            <i class="fas fa-ellipsis-v text-xs"></i>
        </button>
    </div>
</div>
