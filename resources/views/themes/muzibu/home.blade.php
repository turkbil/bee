@extends('themes.muzibu.layouts.app')

@section('title', 'Muzibu - Ana Sayfa')

@section('content')
<div class="px-4 py-6 sm:px-6 sm:py-8">

{{-- Quick Access Cards (Spotify Style - 2 rows) --}}
@if($featuredPlaylists && $featuredPlaylists->count() > 0)
<div class="mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">
        @foreach($featuredPlaylists->take(8) as $playlist)
        <div class="playlist-card group flex items-center gap-3 bg-white/5 hover:bg-spotify-black rounded transition-all cursor-pointer overflow-hidden h-16 relative"
           x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
           x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'playlist', {
               id: {{ $playlist->playlist_id }},
               title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}',
               is_favorite: {{ is_favorited('playlist', $playlist->playlist_id) ? 'true' : 'false' }},
               is_mine: {{ $playlist->user_id && auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }}
           })"
           x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'playlist', { id: {{ $playlist->playlist_id }}, title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}', is_favorite: {{ is_favorited('playlist', $playlist->playlist_id) ? 'true' : 'false' }}, is_mine: {{ $playlist->user_id && auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }} }); }, 500);"
           x-on:touchend="clearTimeout(touchTimer)"
           x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer);"
           @click="$store.sidebar.showPreview('playlist', {{ $playlist->playlist_id }}, {
               type: 'Playlist',
               id: {{ $playlist->playlist_id }},
               title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}',
               cover: '{{ $playlist->getCoverUrl(300, 300) ?? '' }}',
               is_favorite: {{ is_favorited('playlist', $playlist->playlist_id) ? 'true' : 'false' }}
           })">
            <div class="w-16 h-16 flex-shrink-0">
                @if($playlist->getCoverUrl())
                    <img src="{{ thumb($playlist->getCoverUrl(), 64, 64, ['scale' => 1]) }}" alt="{{ getLocaleTitle($playlist->title, 'Playlist') }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-muzibu-coral to-pink-600 flex items-center justify-center text-xl">🎵</div>
                @endif
            </div>
            <div class="flex-1 min-w-0 pr-10">
                <h3 class="font-semibold text-white text-sm truncate">
                    {{ getLocaleTitle($playlist->title, 'Playlist') }}
                </h3>
            </div>
            {{-- 3-Dot Menu Button (Sağ) - HER ZAMAN GÖRÜNÜR --}}
            <div class="absolute right-2 top-1/2 -translate-y-1/2 z-10" @click.stop>
                <button @click="$store.contextMenu.openContextMenu($event, 'playlist', {
                    id: {{ $playlist->playlist_id }},
                    title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}',
                    is_favorite: {{ is_favorited('playlist', $playlist->playlist_id) ? 'true' : 'false' }},
                    is_mine: {{ $playlist->user_id && auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }}
                })" class="w-7 h-7 bg-black/40 hover:bg-black/60 rounded-full flex items-center justify-center text-white/70 hover:text-white transition-all">
                    <i class="fas fa-ellipsis-v text-xs"></i>
                </button>
            </div>
        </div>
        @endforeach
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
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
            <i class="fas fa-list-music text-muzibu-coral"></i>
            Öne Çıkan Listeler
        </h2>
        <a href="/playlists" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/70 hover:text-white transition-all" title="Tümünü Gör">
            <i class="fas fa-chevron-right text-sm"></i>
        </a>
    </div>

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
        @foreach($featuredPlaylists as $playlist)
        <div class="playlist-card group flex-shrink-0 w-[190px] p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-spotify-black relative overflow-hidden"
           x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
           x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'playlist', {
               id: {{ $playlist->playlist_id }},
               title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}',
               is_favorite: {{ is_favorited('playlist', $playlist->playlist_id) ? 'true' : 'false' }},
               is_mine: {{ $playlist->user_id && auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }}
           })"
           x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'playlist', { id: {{ $playlist->playlist_id }}, title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}', is_favorite: {{ is_favorited('playlist', $playlist->playlist_id) ? 'true' : 'false' }}, is_mine: {{ $playlist->user_id && auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }} }); }, 500);"
           x-on:touchend="clearTimeout(touchTimer)"
           x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer);"
           @click="$store.sidebar.showPreview('playlist', {{ $playlist->playlist_id }}, {
               type: 'Playlist',
               id: {{ $playlist->playlist_id }},
               title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}',
               cover: '{{ $playlist->getCoverUrl(300, 300) ?? '' }}',
               is_favorite: {{ is_favorited('playlist', $playlist->playlist_id) ? 'true' : 'false' }}
           })">
            {{-- Hover Shimmer/Buz Efekti --}}
            <div class="absolute inset-0 overflow-hidden rounded-lg pointer-events-none">
                <div class="absolute -inset-full bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 -translate-x-full group-hover:animate-shimmer-sweep"></div>
            </div>

            <div class="relative mb-3">
                <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl">
                    @if($playlist->getCoverUrl())
                        <img src="{{ thumb($playlist->getCoverUrl(), 200, 200, ['scale' => 1]) }}" alt="{{ getLocaleTitle($playlist->title, 'Playlist') }}" loading="lazy" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-muzibu-coral to-pink-600 flex items-center justify-center text-4xl">🎵</div>
                    @endif
                </div>
                {{-- Play button on hover --}}
                <button type="button" class="absolute bottom-2 right-2 w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-10"
                     @click.stop="window.playPlaylist ? window.playPlaylist({{ $playlist->playlist_id }}) : $store.player.playPlaylist({{ $playlist->playlist_id }})">
                    <i class="fas fa-play text-black ml-0.5"></i>
                </button>
                {{-- 3-Dot Menu Button (Sağ Üst) - HER ZAMAN GÖRÜNÜR --}}
                <div class="absolute top-2 right-2 z-10" @click.stop>
                    <button @click="$store.contextMenu.openContextMenu($event, 'playlist', {
                        id: {{ $playlist->playlist_id }},
                        title: '{{ addslashes(getLocaleTitle($playlist->title, 'Playlist')) }}',
                        is_favorite: {{ is_favorited('playlist', $playlist->playlist_id) ? 'true' : 'false' }},
                        is_mine: {{ $playlist->user_id && auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }}
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

{{-- scrollbar-hide CSS moved to tenant-1001.css --}}
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
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
            <i class="fas fa-record-vinyl text-muzibu-coral"></i>
            Yeni Çıkanlar
        </h2>
        <a href="/albums" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/70 hover:text-white transition-all" title="Tümünü Gör">
            <i class="fas fa-chevron-right text-sm"></i>
        </a>
    </div>

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
        @foreach($newReleases as $album)
        <div class="album-card group flex-shrink-0 w-[190px] p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-spotify-black relative overflow-hidden"
           x-data="{
               touchTimer: null,
               touchStartPos: { x: 0, y: 0 }
           }"
           x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'album', {
               id: {{ $album->album_id }},
               title: '{{ addslashes(getLocaleTitle($album->title, 'Album')) }}',
               artist: '{{ $album->artist ? addslashes(is_array($album->artist->title) ? ($album->artist->title['tr'] ?? $album->artist->title['en'] ?? 'Artist') : $album->artist->title) : 'Sanatçı' }}',
               is_favorite: {{ is_favorited('album', $album->album_id) ? 'true' : 'false' }}
           })"
           x-on:touchstart="
               touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY };
               touchTimer = setTimeout(() => {
                   if (navigator.vibrate) navigator.vibrate(50);
                   $store.contextMenu.openContextMenu({
                       clientX: $event.touches[0].clientX,
                       clientY: $event.touches[0].clientY
                   }, 'album', {
                       id: {{ $album->album_id }},
                       title: '{{ addslashes(getLocaleTitle($album->title, 'Album')) }}',
                       artist: '{{ $album->artist ? addslashes(is_array($album->artist->title) ? ($album->artist->title['tr'] ?? $album->artist->title['en'] ?? 'Artist') : $album->artist->title) : 'Sanatçı' }}',
                       is_favorite: {{ is_favorited('album', $album->album_id) ? 'true' : 'false' }}
                   });
               }, 500);
           "
           x-on:touchend="clearTimeout(touchTimer)"
           x-on:touchmove="
               const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                            Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
               if (moved) clearTimeout(touchTimer);
           "
           @click="$store.sidebar.showPreview('album', {{ $album->album_id }}, {
               type: 'Album',
               id: {{ $album->album_id }},
               title: '{{ addslashes(getLocaleTitle($album->title, 'Album')) }}',
               artist: '{{ $album->artist ? addslashes(is_array($album->artist->title) ? ($album->artist->title['tr'] ?? $album->artist->title['en'] ?? 'Artist') : $album->artist->title) : 'Sanatçı' }}',
               cover: '{{ $album->getCoverUrl(300, 300) ?? '' }}',
               is_favorite: {{ is_favorited('album', $album->album_id) ? 'true' : 'false' }}
           })">
            {{-- Hover Shimmer/Buz Efekti --}}
            <div class="absolute inset-0 overflow-hidden rounded-lg pointer-events-none">
                <div class="absolute -inset-full bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 -translate-x-full group-hover:animate-shimmer-sweep"></div>
            </div>

            <div class="relative mb-3">
                <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl" class="bg-gradient-to-br from-muzibu-coral to-purple-600">
                    @if($album->getCoverUrl())
                        <img src="{{ thumb($album->getCoverUrl(), 200, 200, ['scale' => 1]) }}" alt="{{ getLocaleTitle($album->title, 'Album') }}" loading="lazy" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-4xl">🎸</div>
                    @endif
                </div>
                {{-- Play button on hover --}}
                <button type="button" class="absolute bottom-2 right-2 w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-10"
                     @click.stop="window.playAlbum ? window.playAlbum({{ $album->album_id }}) : $store.player.playAlbum({{ $album->album_id }})">
                    <i class="fas fa-play text-black ml-0.5"></i>
                </button>
                {{-- 3-Dot Menu Button (Sağ Üst) - HER ZAMAN GÖRÜNÜR --}}
                <div class="absolute top-2 right-2 z-10" @click.stop>
                    <button @click="$store.contextMenu.openContextMenu($event, 'album', {
                        id: {{ $album->album_id }},
                        title: '{{ addslashes(getLocaleTitle($album->title, 'Album')) }}',
                        artist: '{{ $album->artist ? addslashes(is_array($album->artist->title) ? ($album->artist->title['tr'] ?? $album->artist->title['en'] ?? 'Artist') : $album->artist->title) : 'Sanatçı' }}',
                        is_favorite: {{ is_favorited('album', $album->album_id) ? 'true' : 'false' }}
                    })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                        <i class="fas fa-ellipsis-v text-sm"></i>
                    </button>
                </div>
            </div>
            <h3 class="font-semibold text-white truncate mb-1 text-sm">
                {{ getLocaleTitle($album->title, 'Album') }}
            </h3>
            <p class="text-xs text-muzibu-text-gray truncate">
                {{ $album->artist ? (is_array($album->artist->title) ? ($album->artist->title['tr'] ?? $album->artist->title['en'] ?? 'Artist') : $album->artist->title) : json_encode('Sanatçı') }}
            </p>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- SONGS GRID - Popüler + Yeni Şarkılar --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-6">
    {{-- POPULAR SONGS --}}
    @if($popularSongs && $popularSongs->count() > 0)
    <div>
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-fire text-muzibu-coral"></i>
                Popüler Şarkılar
            </h2>
            <a href="/songs" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/70 hover:text-white transition-all" title="Tümünü Gör">
                <i class="fas fa-chevron-right text-sm"></i>
            </a>
        </div>

        <div class="grid grid-cols-1">
            @foreach($popularSongs->take(5) as $index => $song)
            <div class="group flex items-center gap-3 px-3 py-2 rounded transition-all bg-transparent hover:bg-white/10 cursor-pointer"
                 x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
                 x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', {
                     id: {{ $song->song_id }},
                     title: '{{ addslashes(getLocaleTitle($song->title, 'Song')) }}',
                     artist: '{{ $song->album && $song->album->artist ? addslashes(is_array($song->album->artist->title) ? ($song->album->artist->title['tr'] ?? $song->album->artist->title['en'] ?? 'Artist') : $song->album->artist->title) : 'Sanatçı' }}',
                     album_id: {{ $song->album ? $song->album->album_id : 'null' }},
                     is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }}
                 })"
                 x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'song', { id: {{ $song->song_id }}, title: '{{ addslashes(getLocaleTitle($song->title, 'Song')) }}', is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }} }); }, 500);"
                 x-on:touchend="clearTimeout(touchTimer)"
                 x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer);"
                 @mouseenter="preloadSongOnHover({{ $song->song_id }})"
                 @click="window.playSong ? window.playSong({{ $song->song_id }}) : $store.player.playSong({{ $song->song_id }})">
                {{-- Play Button Overlay --}}
                <div class="relative">
                    <div class="w-14 h-14 rounded overflow-hidden flex-shrink-0">
                        @if($song->album && $song->album->media_id)
                            <img src="{{ thumb($song->album->media_id, 56, 56, ['scale' => 1]) }}" alt="{{ getLocaleTitle($song->title, 'Song') }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-muzibu-coral to-pink-600 flex items-center justify-center text-xl">
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
                    {{-- 3-Dot Menu - HER ZAMAN GÖRÜNÜR --}}
                    <button @click.stop="$store.contextMenu.openContextMenu($event, 'song', {
                        id: {{ $song->song_id }},
                        title: '{{ addslashes(getLocaleTitle($song->title, 'Song')) }}',
                        artist: '{{ $song->album && $song->album->artist ? addslashes(is_array($song->album->artist->title) ? ($song->album->artist->title['tr'] ?? $song->album->artist->title['en'] ?? 'Artist') : $song->album->artist->title) : 'Sanatçı' }}',
                        album_id: {{ $song->album ? $song->album->album_id : 'null' }},
                        is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }}
                    })" class="w-8 h-8 rounded-full flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 transition-all">
                        <i class="fas fa-ellipsis-v text-sm"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- NEW SONGS --}}
    @if($popularSongs && $popularSongs->count() > 5)
    <div>
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-star text-muzibu-coral"></i>
                Yeni Şarkılar
            </h2>
        </div>

        <div class="grid grid-cols-1">
            @foreach($popularSongs->slice(5)->take(5) as $index => $song)
            <div class="group flex items-center gap-3 px-3 py-2 rounded transition-all bg-transparent hover:bg-white/10 cursor-pointer"
                 x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
                 x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', {
                     id: {{ $song->song_id }},
                     title: '{{ addslashes(getLocaleTitle($song->title, 'Song')) }}',
                     artist: '{{ $song->album && $song->album->artist ? addslashes(is_array($song->album->artist->title) ? ($song->album->artist->title['tr'] ?? $song->album->artist->title['en'] ?? 'Artist') : $song->album->artist->title) : 'Sanatçı' }}',
                     album_id: {{ $song->album ? $song->album->album_id : 'null' }},
                     is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }}
                 })"
                 x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'song', { id: {{ $song->song_id }}, title: '{{ addslashes(getLocaleTitle($song->title, 'Song')) }}', is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }} }); }, 500);"
                 x-on:touchend="clearTimeout(touchTimer)"
                 x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer);"
                 @mouseenter="preloadSongOnHover({{ $song->song_id }})"
                 @click="window.playSong ? window.playSong({{ $song->song_id }}) : $store.player.playSong({{ $song->song_id }})">
                {{-- Play Button Overlay --}}
                <div class="relative">
                    <div class="w-14 h-14 rounded overflow-hidden flex-shrink-0">
                        @if($song->album && $song->album->media_id)
                            <img src="{{ thumb($song->album->media_id, 56, 56, ['scale' => 1]) }}" alt="{{ getLocaleTitle($song->title, 'Song') }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-muzibu-coral to-pink-600 flex items-center justify-center text-xl">
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
                    {{-- 3-Dot Menu - HER ZAMAN GÖRÜNÜR --}}
                    <button @click.stop="$store.contextMenu.openContextMenu($event, 'song', {
                        id: {{ $song->song_id }},
                        title: '{{ addslashes(getLocaleTitle($song->title, 'Song')) }}',
                        artist: '{{ $song->album && $song->album->artist ? addslashes(is_array($song->album->artist->title) ? ($song->album->artist->title['tr'] ?? $song->album->artist->title['en'] ?? 'Artist') : $song->album->artist->title) : 'Sanatçı' }}',
                        album_id: {{ $song->album ? $song->album->album_id : 'null' }},
                        is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }}
                    })" class="w-8 h-8 rounded-full flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 transition-all">
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
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
            <i class="fas fa-folder text-muzibu-coral"></i>
            Kategoriler
        </h2>
        <a href="/genres" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/70 hover:text-white transition-all" title="Tümünü Gör">
            <i class="fas fa-chevron-right text-sm"></i>
        </a>
    </div>

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
        <div class="genre-card group flex-shrink-0 w-[190px] p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-spotify-black relative overflow-hidden"
           x-data="{
               touchTimer: null,
               touchStartPos: { x: 0, y: 0 }
           }"
           x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'genre', {
               id: {{ $genre->genre_id }},
               title: '{{ addslashes(getLocaleTitle($genre->title, 'Genre')) }}',
               slug: '{{ $genre->getTranslation('slug', app()->getLocale()) }}',
               is_favorite: {{ is_favorited('genre', $genre->genre_id) ? 'true' : 'false' }}
           })"
           x-on:touchstart="
               touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY };
               touchTimer = setTimeout(() => {
                   if (navigator.vibrate) navigator.vibrate(50);
                   $store.contextMenu.openContextMenu({
                       clientX: $event.touches[0].clientX,
                       clientY: $event.touches[0].clientY
                   }, 'genre', {
                       id: {{ $genre->genre_id }},
                       title: '{{ addslashes(getLocaleTitle($genre->title, 'Genre')) }}',
                       slug: '{{ $genre->getTranslation('slug', app()->getLocale()) }}',
                       is_favorite: {{ is_favorited('genre', $genre->genre_id) ? 'true' : 'false' }}
                   });
               }, 500);
           "
           x-on:touchend="clearTimeout(touchTimer)"
           x-on:touchmove="
               const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                            Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
               if (moved) clearTimeout(touchTimer);
           "
           @click="$store.sidebar.showPreview('genre', {{ $genre->genre_id }}, {
               type: 'Genre',
               id: {{ $genre->genre_id }},
               title: '{{ addslashes(getLocaleTitle($genre->title, 'Genre')) }}',
               slug: '{{ $genre->getTranslation('slug', app()->getLocale()) }}',
               cover: '{{ $genre->getIconUrl(300, 300) ?? '' }}',
               is_favorite: {{ is_favorited('genre', $genre->genre_id) ? 'true' : 'false' }}
           })">
            {{-- Hover Shimmer/Buz Efekti --}}
            <div class="absolute inset-0 overflow-hidden rounded-lg pointer-events-none">
                <div class="absolute -inset-full bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 -translate-x-full group-hover:animate-shimmer-sweep"></div>
            </div>

            <div class="relative mb-3">
                <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl"
                     class="bg-gradient-to-br from-muzibu-coral to-purple-600">
                    <div class="absolute inset-0 flex items-center justify-center text-6xl opacity-30">
                        🎵
                    </div>
                </div>
                {{-- Play button on hover --}}
                <button type="button" class="absolute bottom-2 right-2 w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-10"
                     @click.stop="playGenre({{ $genre->genre_id }})">
                    <i class="fas fa-play text-black ml-0.5"></i>
                </button>
                {{-- 3-Dot Menu Button (Sağ Üst) - HER ZAMAN GÖRÜNÜR --}}
                <div class="absolute top-2 right-2 z-10" @click.stop>
                    <button @click="$store.contextMenu.openContextMenu($event, 'genre', {
                        id: {{ $genre->genre_id }},
                        title: '{{ addslashes(getLocaleTitle($genre->title, 'Genre')) }}',
                        slug: '{{ $genre->getTranslation('slug', app()->getLocale()) }}',
                        is_favorite: {{ is_favorited('genre', $genre->genre_id) ? 'true' : 'false' }}
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

{{-- Öne Çıkan Radyolar (Horizontal Scroll - Spotify Style) --}}
@if($radios && $radios->count() > 0)
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
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
            <i class="fas fa-broadcast-tower text-muzibu-coral"></i>
            Öne Çıkan Radyolar
        </h2>
        <a href="/radios" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/70 hover:text-white transition-all" title="Tümünü Gör">
            <i class="fas fa-chevron-right text-sm"></i>
        </a>
    </div>

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
        @foreach($radios as $radio)
        <div class="radio-card group flex-shrink-0 w-[190px] p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-spotify-black relative overflow-hidden"
           x-data="{
               touchTimer: null,
               touchStartPos: { x: 0, y: 0 }
           }"
           x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'radio', {
               id: {{ $radio->radio_id }},
               title: '{{ addslashes($radio->getTranslation('title', app()->getLocale())) }}',
               is_favorite: {{ is_favorited('radio', $radio->radio_id) ? 'true' : 'false' }}
           })"
           x-on:touchstart="
               touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY };
               touchTimer = setTimeout(() => {
                   if (navigator.vibrate) navigator.vibrate(50);
                   $store.contextMenu.openContextMenu({
                       clientX: $event.touches[0].clientX,
                       clientY: $event.touches[0].clientY
                   }, 'radio', {
                       id: {{ $radio->radio_id }},
                       title: '{{ addslashes($radio->getTranslation('title', app()->getLocale())) }}',
                       is_favorite: {{ is_favorited('radio', $radio->radio_id) ? 'true' : 'false' }}
                   });
               }, 500);
           "
           x-on:touchend="clearTimeout(touchTimer)"
           x-on:touchmove="
               const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                            Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
               if (moved) clearTimeout(touchTimer);
           "
           @click="window.playContent('radio', {{ $radio->radio_id }})">
            {{-- Hover Shimmer/Buz Efekti --}}
            <div class="absolute inset-0 overflow-hidden rounded-lg pointer-events-none">
                <div class="absolute -inset-full bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 -translate-x-full group-hover:animate-shimmer-sweep"></div>
            </div>

            <div class="relative mb-3">
                <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl">
                    @if($radio->getCoverUrl())
                        <img src="{{ $radio->getCoverUrl(200, 200) }}" alt="{{ $radio->getTranslation('title', app()->getLocale()) }}" loading="lazy" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-4xl">
                            <i class="fas fa-radio text-white opacity-80"></i>
                        </div>
                    @endif
                </div>
                {{-- Play button on hover --}}
                <button type="button" class="absolute bottom-2 right-2 w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-10"
                     @click.stop="window.playContent('radio', {{ $radio->radio_id }})">
                    <i class="fas fa-play text-black ml-0.5"></i>
                </button>
                {{-- 3-Dot Menu Button (Sağ Üst) - HER ZAMAN GÖRÜNÜR --}}
                <div class="absolute top-2 right-2 z-10" @click.stop>
                    <button @click="$store.contextMenu.openContextMenu($event, 'radio', {
                        id: {{ $radio->radio_id }},
                        title: '{{ addslashes($radio->getTranslation('title', app()->getLocale())) }}',
                        is_favorite: {{ is_favorited('radio', $radio->radio_id) ? 'true' : 'false' }}
                    })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                        <i class="fas fa-ellipsis-v text-sm"></i>
                    </button>
                </div>
            </div>
            <h3 class="font-semibold text-white truncate mb-1 text-sm">
                {{ $radio->getTranslation('title', app()->getLocale()) }}
            </h3>
            <p class="text-xs text-muzibu-text-gray truncate">📻 Radyo</p>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Sektörler (Horizontal Scroll - Spotify Style) --}}
@if($sectors && $sectors->count() > 0)
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
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
            <i class="fas fa-building text-muzibu-coral"></i>
            Sektörler
        </h2>
        <a href="/sectors" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/70 hover:text-white transition-all" title="Tümünü Gör">
            <i class="fas fa-chevron-right text-sm"></i>
        </a>
    </div>

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
        @foreach($sectors as $sector)
        <div class="sector-card group flex-shrink-0 w-[190px] p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-spotify-black relative overflow-hidden"
           x-data="{
               touchTimer: null,
               touchStartPos: { x: 0, y: 0 }
           }"
           x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'sector', {
               id: {{ $sector->sector_id }},
               title: '{{ addslashes($sector->getTranslation('title', app()->getLocale())) }}',
               is_favorite: {{ is_favorited('sector', $sector->sector_id) ? 'true' : 'false' }}
           })"
           x-on:touchstart="
               touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY };
               touchTimer = setTimeout(() => {
                   if (navigator.vibrate) navigator.vibrate(50);
                   $store.contextMenu.openContextMenu({
                       clientX: $event.touches[0].clientX,
                       clientY: $event.touches[0].clientY
                   }, 'sector', {
                       id: {{ $sector->sector_id }},
                       title: '{{ addslashes($sector->getTranslation('title', app()->getLocale())) }}',
                       is_favorite: {{ is_favorited('sector', $sector->sector_id) ? 'true' : 'false' }}
                   });
               }, 500);
           "
           x-on:touchend="clearTimeout(touchTimer)"
           x-on:touchmove="
               const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                            Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
               if (moved) clearTimeout(touchTimer);
           "
           @click="$store.sidebar.showPreview('sector', {{ $sector->sector_id }}, {
               type: 'Sector',
               id: {{ $sector->sector_id }},
               title: '{{ addslashes($sector->getTranslation('title', app()->getLocale())) }}',
               slug: '{{ $sector->getTranslation('slug', app()->getLocale()) }}',
               cover: '{{ $sector->getCoverUrl(300, 300) ?? '' }}',
               is_favorite: {{ is_favorited('sector', $sector->sector_id) ? 'true' : 'false' }}
           })">
            {{-- Hover Shimmer/Buz Efekti --}}
            <div class="absolute inset-0 overflow-hidden rounded-lg pointer-events-none">
                <div class="absolute -inset-full bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 -translate-x-full group-hover:animate-shimmer-sweep"></div>
            </div>

            <div class="relative mb-3">
                <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl">
                    @if($sector->getCoverUrl())
                        <img src="{{ $sector->getCoverUrl(200, 200) }}" alt="{{ $sector->getTranslation('title', app()->getLocale()) }}" loading="lazy" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-4xl">
                            🎭
                        </div>
                    @endif
                </div>
                {{-- Play button on hover --}}
                <button type="button" class="absolute bottom-2 right-2 w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-10"
                     @click.stop="window.playContent('sector', {{ $sector->sector_id }})">
                    <i class="fas fa-play text-black ml-0.5"></i>
                </button>
                {{-- 3-Dot Menu Button (Sağ Üst) - HER ZAMAN GÖRÜNÜR --}}
                <div class="absolute top-2 right-2 z-10" @click.stop>
                    <button @click="$store.contextMenu.openContextMenu($event, 'sector', {
                        id: {{ $sector->sector_id }},
                        title: '{{ addslashes($sector->getTranslation('title', app()->getLocale())) }}',
                        is_favorite: {{ is_favorited('sector', $sector->sector_id) ? 'true' : 'false' }}
                    })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                        <i class="fas fa-ellipsis-v text-sm"></i>
                    </button>
                </div>
            </div>
            <h3 class="font-semibold text-white truncate mb-1 text-sm">
                {{ $sector->getTranslation('title', app()->getLocale()) }}
            </h3>
            <p class="text-xs text-muzibu-text-gray truncate">🎭 Sektör</p>
        </div>
        @endforeach
    </div>
</div>
@endif

</div>
@endsection
