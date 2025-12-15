@extends('themes.muzibu.layouts.app')

@section('title', 'Muzibu - Ana Sayfa')

@section('content')
{{-- 🎯 Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
document.addEventListener('alpine:init', () => {
    setTimeout(() => {
        if (window.Alpine && window.Alpine.store('sidebar')) {
            window.Alpine.store('sidebar').reset();
        }
    }, 100);
});
</script>

<div class="px-6 py-8">

{{-- Quick Access Cards (Spotify Style - 2 rows, Horizontal Scroll) --}}
@if($featuredPlaylists && $featuredPlaylists->count() > 0)
<div class="mb-6 relative group/quickaccess" x-data="{
    scrollContainer: null,
    scrollInterval: null,
    startAutoScroll(direction) {
        this.scrollInterval = setInterval(() => {
            this.scrollContainer.scrollBy({ left: direction === 'right' ? 20 : -20 });
        }, 50);
    },
    stopAutoScroll() {
        if (this.scrollInterval) {
            clearInterval(this.scrollInterval);
            this.scrollInterval = null;
        }
    }
}" x-init="scrollContainer = $refs.scrollContainer">

    {{-- Left Arrow --}}
    <button
        @click="scrollContainer.scrollBy({ left: -400, behavior: 'smooth' })"
        @mouseenter="startAutoScroll('left')"
        @mouseleave="stopAutoScroll()"
        class="absolute left-[-12px] top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/90 hover:bg-black rounded-full flex items-center justify-center text-white opacity-0 group-hover/quickaccess:opacity-100 transition-opacity shadow-xl"
    >
        <i class="fas fa-chevron-left"></i>
    </button>

    {{-- Right Arrow --}}
    <button
        @click="scrollContainer.scrollBy({ left: 400, behavior: 'smooth' })"
        @mouseenter="startAutoScroll('right')"
        @mouseleave="stopAutoScroll()"
        class="absolute right-[-12px] top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/90 hover:bg-black rounded-full flex items-center justify-center text-white opacity-0 group-hover/quickaccess:opacity-100 transition-opacity shadow-xl"
    >
        <i class="fas fa-chevron-right"></i>
    </button>

    {{-- Scrollable Container with 2 rows --}}
    <div x-ref="scrollContainer" class="overflow-x-auto scrollbar-hide scroll-smooth pb-4">
        <div class="grid grid-rows-2 grid-flow-col auto-cols-[minmax(280px,1fr)] gap-2">
            @foreach($featuredPlaylists->take(8) as $index => $playlist)
            <div class="playlist-card group flex items-center gap-3 bg-white/5 hover:bg-white/10 rounded transition-all cursor-pointer overflow-hidden h-16 relative"
               x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
               x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'playlist', {
                   id: {{ $playlist->playlist_id }},
                   title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}',
                   is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'playlist', $playlist->playlist_id) ? 'true' : 'false' }},
                   is_mine: {{ auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }}
               })"
               x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'playlist', { id: {{ $playlist->playlist_id }}, title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}', is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'playlist', $playlist->playlist_id) ? 'true' : 'false' }}, is_mine: {{ auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }} }); }, 500);"
               x-on:touchend="clearTimeout(touchTimer)"
               x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer);"
               @click="$store.sidebar.showPreview('playlist', {{ $playlist->playlist_id }}, {
                   type: 'Playlist',
                   id: {{ $playlist->playlist_id }},
                   title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}',
                   cover: '{{ $playlist->coverMedia ? thumb($playlist->coverMedia, 300, 300, ['scale' => 1]) : '' }}',
                   is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'playlist', $playlist->playlist_id) ? 'true' : 'false' }}
               })">
                <div class="w-16 h-16 flex-shrink-0 bg-gradient-to-br from-muzibu-coral to-pink-600">
                    @if($playlist->coverMedia)
                        <img src="{{ thumb($playlist->coverMedia, 64, 64, ['scale' => 1]) }}" alt="{{ getLocaleTitle($playlist->title, 'Playlist') }}" loading="{{ $index < 6 ? 'eager' : 'lazy' }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-xl text-white/90">🎵</div>
                    @endif
                </div>
                <div class="flex-1 min-w-0 pr-10">
                    <h3 class="font-semibold text-white text-sm truncate">
                        {{ getLocaleTitle($playlist->title, 'Playlist') }}
                    </h3>
                </div>
                {{-- 3-Dot Menu Button - HOVER'DA GÖRÜNÜR --}}
                <div class="absolute right-2 top-1/2 -translate-y-1/2 z-10 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
                    <button @click="$store.contextMenu.openContextMenu($event, 'playlist', {
                        id: {{ $playlist->playlist_id }},
                        title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}',
                        is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'playlist', $playlist->playlist_id) ? 'true' : 'false' }},
                        is_mine: {{ auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }}
                    })" class="w-7 h-7 bg-black/40 hover:bg-black/60 rounded-full flex items-center justify-center text-white/70 hover:text-white transition-all">
                        <i class="fas fa-ellipsis-v text-xs"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Featured Playlists (Spotify Style) --}}
@if($featuredPlaylists && $featuredPlaylists->count() > 0)
<div class="mb-4 relative group/scroll" x-data="{
    scrollContainer: null,
    scrollInterval: null,
    startAutoScroll(direction) {
        this.scrollInterval = setInterval(() => {
            this.scrollContainer.scrollBy({ left: direction === 'right' ? 20 : -20 });
        }, 50);
    },
    stopAutoScroll() {
        if (this.scrollInterval) {
            clearInterval(this.scrollInterval);
            this.scrollInterval = null;
        }
    }
}" x-init="scrollContainer = $refs.scrollContainer">
    <h2 class="text-2xl font-bold text-white mb-2">Öne Çıkan Listeler</h2>

    {{-- Left Arrow --}}
    <button
        @click="scrollContainer.scrollBy({ left: -400, behavior: 'smooth' })"
        @mouseenter="startAutoScroll('left')"
        @mouseleave="stopAutoScroll()"
        class="absolute left-[-12px] top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/90 hover:bg-black rounded-full flex items-center justify-center text-white opacity-0 group-hover/scroll:opacity-100 transition-opacity shadow-xl"
    >
        <i class="fas fa-chevron-left"></i>
    </button>

    {{-- Right Arrow --}}
    <button
        @click="scrollContainer.scrollBy({ left: 400, behavior: 'smooth' })"
        @mouseenter="startAutoScroll('right')"
        @mouseleave="stopAutoScroll()"
        class="absolute right-[-12px] top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/90 hover:bg-black rounded-full flex items-center justify-center text-white opacity-0 group-hover/scroll:opacity-100 transition-opacity shadow-xl"
    >
        <i class="fas fa-chevron-right"></i>
    </button>

    <div x-ref="scrollContainer" class="flex gap-2 overflow-x-auto scrollbar-hide scroll-smooth pb-4">
        @foreach($featuredPlaylists as $index => $playlist)
        <div class="playlist-card group relative flex-shrink-0 w-[190px] p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-white/10"
             x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
             x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'playlist', {
                 id: {{ $playlist->playlist_id }},
                 title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}',
                 is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'playlist', $playlist->playlist_id) ? 'true' : 'false' }},
                 is_mine: {{ auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }}
             })"
             x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'playlist', { id: {{ $playlist->playlist_id }}, title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}', is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'playlist', $playlist->playlist_id) ? 'true' : 'false' }}, is_mine: {{ auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }} }); }, 500);"
             x-on:touchend="clearTimeout(touchTimer)"
             x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer);"
             @click="$store.sidebar.showPreview('playlist', {{ $playlist->playlist_id }}, {
                 type: 'Playlist',
                 id: {{ $playlist->playlist_id }},
                 title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}',
                 cover: '{{ $playlist->coverMedia ? thumb($playlist->coverMedia, 300, 300, ['scale' => 1]) : '' }}',
                 is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'playlist', $playlist->playlist_id) ? 'true' : 'false' }}
             })">
            <div class="relative mb-3">
                <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl bg-gradient-to-br from-muzibu-coral to-purple-600">
                    @if($playlist->coverMedia)
                        <img src="{{ thumb($playlist->coverMedia, 200, 200, ['scale' => 1]) }}" alt="{{ getLocaleTitle($playlist->title, 'Playlist') }}" loading="{{ $index < 5 ? 'eager' : 'lazy' }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-4xl text-white/90">🎵</div>
                    @endif
                </div>
                {{-- Play button --}}
                <button class="absolute bottom-2 right-2 w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-10"
                        @click.stop="playPlaylist({{ $playlist->playlist_id }})">
                    <i class="fas fa-play text-black ml-0.5"></i>
                </button>
                {{-- 3-Dot Menu Button - HOVER'DA GÖRÜNÜR --}}
                <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
                    <button @click="$store.contextMenu.openContextMenu($event, 'playlist', {
                        id: {{ $playlist->playlist_id }},
                        title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}',
                        is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'playlist', $playlist->playlist_id) ? 'true' : 'false' }},
                        is_mine: {{ auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }}
                    })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                        <i class="fas fa-ellipsis-v text-sm"></i>
                    </button>
                </div>
            </div>
            <h3 class="font-semibold text-white truncate mb-1 text-sm">
                {{ getLocaleTitle($playlist->title, 'Playlist') }}
            </h3>
            <p class="text-xs text-muzibu-text-gray truncate">{{ $playlist->songs()->count() }} şarkı</p>
        </div>
        @endforeach
    </div>
</div>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endif

{{-- New Releases (Horizontal Scroll - Spotify Style) --}}
@if($newReleases && $newReleases->count() > 0)
<div class="mb-6 relative group/scroll" x-data="{
    scrollContainer: null,
    scrollInterval: null,
    startAutoScroll(direction) {
        this.scrollInterval = setInterval(() => {
            this.scrollContainer.scrollBy({ left: direction === 'right' ? 20 : -20 });
        }, 50);
    },
    stopAutoScroll() {
        if (this.scrollInterval) {
            clearInterval(this.scrollInterval);
            this.scrollInterval = null;
        }
    }
}" x-init="scrollContainer = $refs.scrollContainer">
    <h2 class="text-2xl font-bold text-white mb-2">Yeni Çıkanlar</h2>

    {{-- Left Arrow --}}
    <button
        @click="scrollContainer.scrollBy({ left: -400, behavior: 'smooth' })"
        @mouseenter="startAutoScroll('left')"
        @mouseleave="stopAutoScroll()"
        class="absolute left-[-12px] top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/90 hover:bg-black rounded-full flex items-center justify-center text-white opacity-0 group-hover/scroll:opacity-100 transition-opacity shadow-xl"
    >
        <i class="fas fa-chevron-left"></i>
    </button>

    {{-- Right Arrow --}}
    <button
        @click="scrollContainer.scrollBy({ left: 400, behavior: 'smooth' })"
        @mouseenter="startAutoScroll('right')"
        @mouseleave="stopAutoScroll()"
        class="absolute right-[-12px] top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/90 hover:bg-black rounded-full flex items-center justify-center text-white opacity-0 group-hover/scroll:opacity-100 transition-opacity shadow-xl"
    >
        <i class="fas fa-chevron-right"></i>
    </button>

    <div x-ref="scrollContainer" class="flex gap-2 overflow-x-auto scrollbar-hide scroll-smooth pb-4">
        @foreach($newReleases as $index => $album)
        <div class="album-card group relative flex-shrink-0 w-[190px] p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-white/10"
             x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
             x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'album', {
                 id: {{ $album->album_id }},
                 title: '{{ addslashes(getLocaleTitle($album->title, 'Album')) }}',
                 artist: '{{ $album->artist ? addslashes(is_array($album->artist->title) ? ($album->artist->title['tr'] ?? $album->artist->title['en'] ?? 'Artist') : $album->artist->title) : 'Sanatçı' }}',
                 is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'album', $album->album_id) ? 'true' : 'false' }}
             })"
             x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'album', { id: {{ $album->album_id }}, title: '{{ addslashes(getLocaleTitle($album->title, 'Album')) }}', artist: '{{ $album->artist ? addslashes(is_array($album->artist->title) ? ($album->artist->title['tr'] ?? $album->artist->title['en'] ?? 'Artist') : $album->artist->title) : 'Sanatçı' }}', is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'album', $album->album_id) ? 'true' : 'false' }} }); }, 500);"
             x-on:touchend="clearTimeout(touchTimer)"
             x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer);"
             @click="$store.sidebar.showPreview('album', {{ $album->album_id }}, {
                 type: 'Album',
                 id: {{ $album->album_id }},
                 title: '{{ addslashes(getLocaleTitle($album->title, 'Album')) }}',
                 artist: '{{ $album->artist ? addslashes(is_array($album->artist->title) ? ($album->artist->title['tr'] ?? $album->artist->title['en'] ?? 'Artist') : $album->artist->title) : 'Sanatçı' }}',
                 cover: '{{ $album->coverMedia ? thumb($album->coverMedia, 300, 300, ['scale' => 1]) : '' }}',
                 is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'album', $album->album_id) ? 'true' : 'false' }}
             })">
            <div class="relative mb-3">
                <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl bg-gradient-to-br from-blue-500 to-purple-600">
                    @if($album->coverMedia)
                        <img src="{{ thumb($album->coverMedia, 200, 200, ['scale' => 1]) }}" alt="{{ getLocaleTitle($album->title, 'Album') }}" loading="{{ $index < 5 ? 'eager' : 'lazy' }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-4xl text-white/90">💿</div>
                    @endif
                </div>
                {{-- Play button --}}
                <button class="absolute bottom-2 right-2 w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-10"
                        @click.stop="playAlbum({{ $album->album_id }})">
                    <i class="fas fa-play text-black ml-0.5"></i>
                </button>
                {{-- 3-Dot Menu Button - HOVER'DA GÖRÜNÜR --}}
                <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
                    <button @click="$store.contextMenu.openContextMenu($event, 'album', {
                        id: {{ $album->album_id }},
                        title: '{{ addslashes(getLocaleTitle($album->title, 'Album')) }}',
                        artist: '{{ $album->artist ? addslashes(is_array($album->artist->title) ? ($album->artist->title['tr'] ?? $album->artist->title['en'] ?? 'Artist') : $album->artist->title) : 'Sanatçı' }}',
                        is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'album', $album->album_id) ? 'true' : 'false' }}
                    })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                        <i class="fas fa-ellipsis-v text-sm"></i>
                    </button>
                </div>
            </div>
            <h3 class="font-semibold text-white truncate mb-1 text-sm">
                {{ getLocaleTitle($album->title, 'Album') }}
            </h3>
            <p class="text-xs text-muzibu-text-gray truncate">
                {{ $album->artist ? (is_array($album->artist->title) ? ($album->artist->title['tr'] ?? $album->artist->title['en'] ?? 'Artist') : $album->artist->title) : 'Sanatçı' }}
            </p>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- SONGS GRID - Popüler + Yeni Şarkılar --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- POPULAR SONGS --}}
    @if($popularSongs && $popularSongs->count() > 0)
    <div>
        <h2 class="text-2xl font-bold text-white mb-2">Popüler Şarkılar</h2>

        <div class="grid grid-cols-1">
            @foreach($popularSongs->take(10) as $index => $song)
            <div class="group flex items-center gap-3 px-3 py-2 rounded transition-all bg-transparent hover:bg-white/10 cursor-pointer"
                 x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
                 x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', {
                     id: {{ $song->song_id }},
                     title: '{{ addslashes(getLocaleTitle($song->title, 'Song')) }}',
                     artist: '{{ $song->album && $song->album->artist ? addslashes(is_array($song->album->artist->title) ? ($song->album->artist->title['tr'] ?? $song->album->artist->title['en'] ?? 'Artist') : $song->album->artist->title) : 'Sanatçı' }}',
                     album_id: {{ $song->album ? $song->album->album_id : 'null' }},
                     is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'song', $song->song_id) ? 'true' : 'false' }}
                 })"
                 x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'song', { id: {{ $song->song_id }}, title: '{{ addslashes(getLocaleTitle($song->title, 'Song')) }}', is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'song', $song->song_id) ? 'true' : 'false' }} }); }, 500);"
                 x-on:touchend="clearTimeout(touchTimer)"
                 x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer);"
                 @click="playSong({{ $song->song_id }})">
                {{-- Play Button Overlay --}}
                <div class="relative">
                    <div class="w-14 h-14 rounded overflow-hidden flex-shrink-0 bg-gradient-to-br from-muzibu-coral to-pink-600">
                        @if($song->album && $song->album->coverMedia)
                            <img src="{{ thumb($song->album->coverMedia, 56, 56, ['scale' => 1]) }}" alt="{{ getLocaleTitle($song->title, 'Song') }}" loading="lazy" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xl text-white/90">
                                🎵
                            </div>
                        @endif
                    </div>
                    <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded">
                        <i class="fas fa-play text-white text-sm"></i>
                    </div>
                </div>

                {{-- Song Info --}}
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-white truncate group-hover:text-muzibu-coral transition-colors">
                        {{ getLocaleTitle($song->title, 'Song') }}
                    </div>
                    <div class="text-xs text-muzibu-text-gray truncate">
                        {{ $song->album && $song->album->artist ? (is_array($song->album->artist->title) ? ($song->album->artist->title['tr'] ?? $song->album->artist->title['en'] ?? 'Artist') : $song->album->artist->title) : 'Sanatçı' }}
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Duration --}}
                    <div class="text-xs text-muzibu-text-gray w-10 text-right">
                        @if($song->duration)
                            {{ gmdate('i:s', $song->duration) }}
                        @endif
                    </div>
                    {{-- 3-Dot Menu - HOVER'DA GÖRÜNÜR --}}
                    <button @click.stop="$store.contextMenu.openContextMenu($event, 'song', {
                        id: {{ $song->song_id }},
                        title: '{{ addslashes(getLocaleTitle($song->title, 'Song')) }}',
                        artist: '{{ $song->album && $song->album->artist ? addslashes(is_array($song->album->artist->title) ? ($song->album->artist->title['tr'] ?? $song->album->artist->title['en'] ?? 'Artist') : $song->album->artist->title) : 'Sanatçı' }}',
                        album_id: {{ $song->album ? $song->album->album_id : 'null' }},
                        is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'song', $song->song_id) ? 'true' : 'false' }}
                    })" class="w-8 h-8 rounded-full flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 transition-all opacity-0 group-hover:opacity-100">
                        <i class="fas fa-ellipsis-v text-sm"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- NEW SONGS --}}
    @if($popularSongs && $popularSongs->count() > 10)
    <div>
        <h2 class="text-2xl font-bold text-white mb-2">Yeni Şarkılar</h2>

        <div class="grid grid-cols-1">
            @foreach($popularSongs->slice(10)->take(10) as $index => $song)
            <div class="group flex items-center gap-3 px-3 py-2 rounded transition-all bg-transparent hover:bg-white/10 cursor-pointer"
                 x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
                 x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', {
                     id: {{ $song->song_id }},
                     title: '{{ addslashes(getLocaleTitle($song->title, 'Song')) }}',
                     artist: '{{ $song->album && $song->album->artist ? addslashes(is_array($song->album->artist->title) ? ($song->album->artist->title['tr'] ?? $song->album->artist->title['en'] ?? 'Artist') : $song->album->artist->title) : 'Sanatçı' }}',
                     album_id: {{ $song->album ? $song->album->album_id : 'null' }},
                     is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'song', $song->song_id) ? 'true' : 'false' }}
                 })"
                 x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'song', { id: {{ $song->song_id }}, title: '{{ addslashes(getLocaleTitle($song->title, 'Song')) }}', is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'song', $song->song_id) ? 'true' : 'false' }} }); }, 500);"
                 x-on:touchend="clearTimeout(touchTimer)"
                 x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer);"
                 @click="playSong({{ $song->song_id }})">
                {{-- Play Button Overlay --}}
                <div class="relative">
                    <div class="w-14 h-14 rounded overflow-hidden flex-shrink-0 bg-gradient-to-br from-muzibu-coral to-pink-600">
                        @if($song->album && $song->album->coverMedia)
                            <img src="{{ thumb($song->album->coverMedia, 56, 56, ['scale' => 1]) }}" alt="{{ getLocaleTitle($song->title, 'Song') }}" loading="lazy" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xl text-white/90">
                                🎵
                            </div>
                        @endif
                    </div>
                    <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded">
                        <i class="fas fa-play text-white text-sm"></i>
                    </div>
                </div>

                {{-- Song Info --}}
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-white truncate group-hover:text-muzibu-coral transition-colors">
                        {{ getLocaleTitle($song->title, 'Song') }}
                    </div>
                    <div class="text-xs text-muzibu-text-gray truncate">
                        {{ $song->album && $song->album->artist ? (is_array($song->album->artist->title) ? ($song->album->artist->title['tr'] ?? $song->album->artist->title['en'] ?? 'Artist') : $song->album->artist->title) : 'Sanatçı' }}
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Duration --}}
                    <div class="text-xs text-muzibu-text-gray w-10 text-right">
                        @if($song->duration)
                            {{ gmdate('i:s', $song->duration) }}
                        @endif
                    </div>
                    {{-- 3-Dot Menu - HOVER'DA GÖRÜNÜR --}}
                    <button @click.stop="$store.contextMenu.openContextMenu($event, 'song', {
                        id: {{ $song->song_id }},
                        title: '{{ addslashes(getLocaleTitle($song->title, 'Song')) }}',
                        artist: '{{ $song->album && $song->album->artist ? addslashes(is_array($song->album->artist->title) ? ($song->album->artist->title['tr'] ?? $song->album->artist->title['en'] ?? 'Artist') : $song->album->artist->title) : 'Sanatçı' }}',
                        album_id: {{ $song->album ? $song->album->album_id : 'null' }},
                        is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'song', $song->song_id) ? 'true' : 'false' }}
                    })" class="w-8 h-8 rounded-full flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 transition-all opacity-0 group-hover:opacity-100">
                        <i class="fas fa-ellipsis-v text-sm"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Genres (Horizontal Scroll - Spotify Style) --}}
@if($genres && $genres->count() > 0)
<div class="mb-3 relative group/scroll" x-data="{
    scrollContainer: null,
    scrollInterval: null,
    startAutoScroll(direction) {
        this.scrollInterval = setInterval(() => {
            this.scrollContainer.scrollBy({ left: direction === 'right' ? 20 : -20 });
        }, 50);
    },
    stopAutoScroll() {
        if (this.scrollInterval) {
            clearInterval(this.scrollInterval);
            this.scrollInterval = null;
        }
    }
}" x-init="scrollContainer = $refs.scrollContainer">
    <h2 class="text-2xl font-bold text-white mb-2">Kategoriler</h2>

    {{-- Left Arrow --}}
    <button
        @click="scrollContainer.scrollBy({ left: -400, behavior: 'smooth' })"
        @mouseenter="startAutoScroll('left')"
        @mouseleave="stopAutoScroll()"
        class="absolute left-[-12px] top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/90 hover:bg-black rounded-full flex items-center justify-center text-white opacity-0 group-hover/scroll:opacity-100 transition-opacity shadow-xl"
    >
        <i class="fas fa-chevron-left"></i>
    </button>

    {{-- Right Arrow --}}
    <button
        @click="scrollContainer.scrollBy({ left: 400, behavior: 'smooth' })"
        @mouseenter="startAutoScroll('right')"
        @mouseleave="stopAutoScroll()"
        class="absolute right-[-12px] top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/90 hover:bg-black rounded-full flex items-center justify-center text-white opacity-0 group-hover/scroll:opacity-100 transition-opacity shadow-xl"
    >
        <i class="fas fa-chevron-right"></i>
    </button>

    <div x-ref="scrollContainer" class="flex gap-2 overflow-x-auto scrollbar-hide scroll-smooth pb-4">
        @foreach($genres as $genre)
        <div class="group relative flex-shrink-0 w-[190px] p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-white/10"
             x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
             x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'genre', {
                 id: {{ $genre->genre_id }},
                 title: '{{ addslashes(getLocaleTitle($genre->title, 'Genre')) }}',
                 slug: '{{ $genre->getTranslation('slug', app()->getLocale()) }}',
                 is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'genre', $genre->genre_id) ? 'true' : 'false' }}
             })"
             x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'genre', { id: {{ $genre->genre_id }}, title: '{{ addslashes(getLocaleTitle($genre->title, 'Genre')) }}', slug: '{{ $genre->getTranslation('slug', app()->getLocale()) }}', is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'genre', $genre->genre_id) ? 'true' : 'false' }} }); }, 500);"
             x-on:touchend="clearTimeout(touchTimer)"
             x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer);"
             @click="$store.sidebar.showPreview('genre', {{ $genre->genre_id }}, {
                 type: 'Genre',
                 id: {{ $genre->genre_id }},
                 title: '{{ addslashes(getLocaleTitle($genre->title, 'Genre')) }}',
                 slug: '{{ $genre->getTranslation('slug', app()->getLocale()) }}',
                 cover: '{{ $genre->iconMedia ? thumb($genre->iconMedia, 300, 300, ['scale' => 1]) : '' }}',
                 is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'genre', $genre->genre_id) ? 'true' : 'false' }}
             })">
            <div class="relative mb-3">
                <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl bg-gradient-to-br from-green-500 to-blue-600">
                    @if($genre->iconMedia)
                        <img src="{{ thumb($genre->iconMedia, 200, 200, ['scale' => 1]) }}" alt="{{ getLocaleTitle($genre->title, 'Genre') }}" loading="lazy" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-6xl text-white/90">
                            🎵
                        </div>
                    @endif
                </div>
                {{-- Play button --}}
                <button class="absolute bottom-2 right-2 w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-10"
                        @click.stop="playGenre({{ $genre->genre_id }})">
                    <i class="fas fa-play text-black ml-0.5"></i>
                </button>
                {{-- 3-Dot Menu Button - HOVER'DA GÖRÜNÜR --}}
                <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
                    <button @click="$store.contextMenu.openContextMenu($event, 'genre', {
                        id: {{ $genre->genre_id }},
                        title: '{{ addslashes(getLocaleTitle($genre->title, 'Genre')) }}',
                        slug: '{{ $genre->getTranslation('slug', app()->getLocale()) }}',
                        is_favorite: {{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'genre', $genre->genre_id) ? 'true' : 'false' }}
                    })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                        <i class="fas fa-ellipsis-v text-sm"></i>
                    </button>
                </div>
            </div>
            <h3 class="font-semibold text-white truncate mb-1 text-sm">
                {{ getLocaleTitle($genre->title, 'Genre') }}
            </h3>
            <p class="text-xs text-muzibu-text-gray truncate">{{ $genre->songs()->count() }} şarkı</p>
        </div>
        @endforeach
    </div>
</div>
@endif


</div>
@endsection
