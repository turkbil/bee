@extends('themes.muzibu.layouts.app')

@section('title', 'Muzibu - Ana Sayfa')

@section('content')
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
            @foreach($featuredPlaylists->take(8) as $playlist)
            <div class="playlist-card group flex items-center gap-3 bg-white/5 hover:bg-white/10 rounded transition-all cursor-pointer overflow-hidden h-16"
                 data-playlist-id="{{ $playlist->playlist_id }}"
                 data-playlist-title="{{ getLocaleTitle($playlist->title, 'Playlist') }}"
                 data-is-favorite="{{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'playlist', $playlist->playlist_id) ? '1' : '0' }}"
                 data-is-mine="{{ auth()->check() && $playlist->user_id == auth()->user()->id ? '1' : '0' }}">
                <div class="w-16 h-16 flex-shrink-0">
                    @if($playlist->coverMedia)
                        <img src="{{ thumb($playlist->coverMedia, 64, 64, ['scale' => 1]) }}" alt="{{ getLocaleTitle($playlist->title, 'Playlist') }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-muzibu-coral to-pink-600 flex items-center justify-center text-xl">🎵</div>
                    @endif
                </div>
                <div class="flex-1 min-w-0 pr-3">
                    <h3 class="font-semibold text-white text-sm truncate">
                        {{ getLocaleTitle($playlist->title, 'Playlist') }}
                    </h3>
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
        @foreach($featuredPlaylists as $playlist)
        <div class="playlist-card group relative flex-shrink-0 w-[190px]"
             data-playlist-id="{{ $playlist->playlist_id }}"
             data-playlist-title="{{ getLocaleTitle($playlist->title, 'Playlist') }}"
             data-is-favorite="{{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'playlist', $playlist->playlist_id) ? '1' : '0' }}"
             data-is-mine="{{ auth()->check() && $playlist->user_id == auth()->user()->id ? '1' : '0' }}">
            <a class="block p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-white/10"
               wire:navigate
               href="/playlists/{{ $playlist->getTranslation('slug', app()->getLocale()) }}">
                <div class="relative mb-3">
                    <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl" style="background: linear-gradient(135deg, #{{ sprintf('%06X', mt_rand(0, 0xFFFFFF)) }} 0%, #{{ sprintf('%06X', mt_rand(0, 0xFFFFFF)) }} 100%);">
                        @if($playlist->coverMedia)
                            <img src="{{ thumb($playlist->coverMedia, 200, 200, ['scale' => 1]) }}" alt="{{ getLocaleTitle($playlist->title, 'Playlist') }}" loading="lazy" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-4xl">🎵</div>
                        @endif
                    </div>
                </div>
                <h3 class="font-semibold text-white truncate mb-1 text-sm">
                    {{ getLocaleTitle($playlist->title, 'Playlist') }}
                </h3>
                <p class="text-xs text-muzibu-text-gray truncate">{{ $playlist->songs()->count() }} şarkı</p>
            </a>
            {{-- Play button OUTSIDE <a> tag, positioned over photo bottom-right --}}
            <button class="absolute w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-20 pointer-events-auto"
                    style="bottom: calc(3rem + 0.75rem + 0.5rem); right: calc(0.75rem + 0.5rem);"
                    @click="playPlaylist({{ $playlist->playlist_id }})">
                <i class="fas fa-play text-black ml-0.5"></i>
            </button>
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
        @foreach($newReleases as $album)
        <div class="album-card group relative flex-shrink-0 w-[190px]"
             data-album-id="{{ $album->album_id }}"
             data-album-title="{{ getLocaleTitle($album->title, 'Album') }}"
             data-album-artist="{{ $album->artist ? (is_array($album->artist->title) ? ($album->artist->title['tr'] ?? $album->artist->title['en'] ?? 'Artist') : $album->artist->title) : 'Sanatçı' }}"
             data-is-favorite="{{ \Modules\Favorite\App\Models\Favorite::check(auth()->id(), 'album', $album->album_id) ? '1' : '0' }}">
            <a class="block p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-white/10"
               wire:navigate
               href="/albums/{{ $album->getTranslation('slug', app()->getLocale()) }}">
                <div class="relative mb-3">
                    <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl" style="background: linear-gradient(135deg, #{{ sprintf('%06X', mt_rand(0, 0xFFFFFF)) }} 0%, #{{ sprintf('%06X', mt_rand(0, 0xFFFFFF)) }} 100%);">
                        @if($album->coverMedia)
                            <img src="{{ thumb($album->coverMedia, 200, 200, ['scale' => 1]) }}" alt="{{ getLocaleTitle($album->title, 'Album') }}" loading="lazy" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-4xl">🎸</div>
                        @endif
                    </div>
                </div>
                <h3 class="font-semibold text-white truncate mb-1 text-sm">
                    {{ getLocaleTitle($album->title, 'Album') }}
                </h3>
                <p class="text-xs text-muzibu-text-gray truncate">
                    {{ $album->artist ? (is_array($album->artist->title) ? ($album->artist->title['tr'] ?? $album->artist->title['en'] ?? 'Artist') : $album->artist->title) : 'Sanatçı' }}
                </p>
            </a>
            {{-- Play button OUTSIDE <a> tag, positioned over photo bottom-right --}}
            <button class="absolute w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-20 pointer-events-auto"
                    style="bottom: calc(3rem + 0.75rem + 0.5rem); right: calc(0.75rem + 0.5rem);"
                    @click="playAlbum({{ $album->album_id }})">
                <i class="fas fa-play text-black ml-0.5"></i>
            </button>
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
            <div class="group flex items-center gap-3 px-3 py-2 rounded transition-all bg-transparent hover:bg-white/10 cursor-pointer" @click="playSong({{ $song->song_id }})">
                {{-- Play Button Overlay --}}
                <div class="relative">
                    <div class="w-14 h-14 rounded overflow-hidden flex-shrink-0">
                        @if($song->album && $song->album->coverMedia)
                            <img src="{{ thumb($song->album->coverMedia, 56, 56, ['scale' => 1]) }}" alt="{{ getLocaleTitle($song->title, 'Song') }}" class="w-full h-full object-cover">
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
                <div class="flex items-center gap-3">
                    {{-- Favorite Button --}}
                    <button
                        @click.stop="toggleFavorite({{ $song->song_id }})"
                        class="transition-colors text-muzibu-text-gray hover:text-muzibu-coral"
                        :class="{ 'text-muzibu-coral': favorites.includes({{ $song->song_id }}) }"
                    >
                        <i class="fas fa-heart" :class="{ 'fas': favorites.includes({{ $song->song_id }}), 'far': !favorites.includes({{ $song->song_id }}) }"></i>
                    </button>

                    {{-- Duration --}}
                    <div class="text-xs text-muzibu-text-gray w-10 text-right">
                        @if($song->duration)
                            {{ gmdate('i:s', $song->duration) }}
                        @endif
                    </div>
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
            <div class="group flex items-center gap-3 px-3 py-2 rounded transition-all bg-transparent hover:bg-white/10 cursor-pointer" @click="playSong({{ $song->song_id }})">
                {{-- Play Button Overlay --}}
                <div class="relative">
                    <div class="w-14 h-14 rounded overflow-hidden flex-shrink-0">
                        @if($song->album && $song->album->coverMedia)
                            <img src="{{ thumb($song->album->coverMedia, 56, 56, ['scale' => 1]) }}" alt="{{ getLocaleTitle($song->title, 'Song') }}" class="w-full h-full object-cover">
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
                <div class="flex items-center gap-3">
                    {{-- Favorite Button --}}
                    <button
                        @click.stop="toggleFavorite({{ $song->song_id }})"
                        class="transition-colors text-muzibu-text-gray hover:text-muzibu-coral"
                        :class="{ 'text-muzibu-coral': favorites.includes({{ $song->song_id }}) }"
                    >
                        <i class="fas fa-heart" :class="{ 'fas': favorites.includes({{ $song->song_id }}), 'far': !favorites.includes({{ $song->song_id }}) }"></i>
                    </button>

                    {{-- Duration --}}
                    <div class="text-xs text-muzibu-text-gray w-10 text-right">
                        @if($song->duration)
                            {{ gmdate('i:s', $song->duration) }}
                        @endif
                    </div>
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
        <div class="group relative flex-shrink-0 w-[190px]">
            <a class="block p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-white/10"
               wire:navigate
               href="/genres/{{ $genre->getTranslation('slug', app()->getLocale()) }}">
                <div class="relative mb-3">
                    <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl"
                         style="background: linear-gradient(135deg, #{{ sprintf('%06X', mt_rand(0, 0xFFFFFF)) }} 0%, #{{ sprintf('%06X', mt_rand(0, 0xFFFFFF)) }} 100%);">
                        <div class="absolute inset-0 flex items-center justify-center text-6xl opacity-30">
                            🎵
                        </div>
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-center justify-center">
                            <i class="fas fa-play text-white text-3xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </div>
                    </div>
                </div>
                <h3 class="font-semibold text-white truncate mb-1 text-sm">
                    {{ getLocaleTitle($genre->title, 'Genre') }}
                </h3>
                <p class="text-xs text-muzibu-text-gray truncate">{{ $genre->songs()->count() }} şarkı</p>
            </a>
            {{-- Play button OUTSIDE <a> tag, positioned over photo bottom-right --}}
            <button class="absolute w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-20 pointer-events-auto"
                    style="bottom: calc(3rem + 0.75rem + 0.5rem); right: calc(0.75rem + 0.5rem);"
                    @click="playGenre({{ $genre->genre_id }})">
                <i class="fas fa-play text-black ml-0.5"></i>
            </button>
        </div>
        @endforeach
    </div>
</div>
@endif


</div>
@endsection
