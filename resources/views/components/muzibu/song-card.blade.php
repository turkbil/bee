@props(['song', 'preview' => true, 'compact' => false, 'list' => false])

{{-- ⚠️⚠️⚠️ UYARI: PREVIEW MODU HER ZAMAN TRUE OLMALI! ⚠️⚠️⚠️ --}}
{{--
    🚨 KRİTİK: Bu component MUTLAKA :preview="true" ile çağrılmalı!

    ✅ DOĞRU KULLANIM:
    <x-muzibu.song-card :song="$song" :preview="true" />           -- Card view
    <x-muzibu.song-card :song="$song" :preview="false" :list="true" /> -- List view

    Props:
    - song: Model - Şarkı modeli (zorunlu)
    - preview: Boolean - Desktop sidebar preview (varsayılan: true)
    - compact: Boolean - Compact card mode (varsayılan: false)
    - list: Boolean - Yatay liste görünümü (varsayılan: false)
--}}
{{-- Muzibu Song Card Component --}}

@php
    $songId = $song->song_id ?? $song->id;
    $albumCover = null;
    $artistName = null;

    if ($song->album) {
        $albumHero = $song->album->getFirstMedia('hero');
        $albumCover = $albumHero ? thumb($albumHero, 300, 300, ['scale' => 1]) : null;
        $artistName = $song->album->artist ? $song->album->artist->getTranslation('title', app()->getLocale()) : null;
    }
    $isFavorite = is_favorited('song', $songId);
@endphp

@if($list)
{{-- LIST VIEW --}}
<div class="group flex items-center gap-3 px-3 py-2 rounded transition-all bg-transparent hover:bg-white/10 cursor-pointer"
     data-song-id="{{ $songId }}"
     data-context-type="song"
     x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
     x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', {
         id: {{ $songId }},
         title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
         artist: '{{ $artistName ? addslashes($artistName) : '' }}',
         album_id: {{ $song->album_id ?? 'null' }},
         is_favorite: {{ $isFavorite ? 'true' : 'false' }}
     })"
     x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'song', { id: {{ $songId }}, title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}', is_favorite: {{ $isFavorite ? 'true' : 'false' }} }); }, 500);"
     x-on:touchend="clearTimeout(touchTimer)"
     x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer);"
     @mouseenter="if (typeof preloadSongOnHover === 'function') preloadSongOnHover({{ $songId }})"
     @click="window.playSong ? window.playSong({{ $songId }}) : $store.player.playSong({{ $songId }})">

    {{-- Play Button Overlay --}}
    <div class="relative">
        <div class="w-14 h-14 rounded overflow-hidden flex-shrink-0">
            @if($albumCover)
                <img src="{{ thumb($song->album->getFirstMedia('hero'), 56, 56, ['scale' => 1]) }}"
                     alt="{{ $song->getTranslation('title', app()->getLocale()) }}"
                     class="w-full h-full object-cover">
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
        <div class="text-sm font-semibold text-white truncate group-hover:text-muzibu-coral transition-colors"
             x-bind:class="$store.player.currentSong?.id === {{ $songId }} ? 'text-muzibu-coral' : ''">
            {{ $song->getTranslation('title', app()->getLocale()) }}
        </div>
        <div class="text-xs text-muzibu-text-gray truncate">
            {{ $artistName ?? 'Sanatçı' }}
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
        {{-- 3-Dot Menu --}}
        <button @click.stop="$store.contextMenu.openContextMenu($event, 'song', {
            id: {{ $songId }},
            title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
            artist: '{{ $artistName ? addslashes($artistName) : '' }}',
            album_id: {{ $song->album_id ?? 'null' }},
            is_favorite: {{ $isFavorite ? 'true' : 'false' }}
        })" class="w-8 h-8 rounded-full flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 transition-all">
            <i class="fas fa-ellipsis-v text-sm"></i>
        </button>
    </div>
</div>

@else
{{-- CARD VIEW (Default) --}}
<a @if($preview)
       href="{{ $song->getUrl() }}"
       @click="if (window.innerWidth >= 768) { $event.preventDefault(); $store.sidebar.showPreview('song', {{ $songId }}, {
           type: 'Song',
           id: {{ $songId }},
           title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
           artist: '{{ $artistName ? addslashes($artistName) : '' }}',
           cover: '{{ $albumCover ?? '' }}',
           is_favorite: {{ $isFavorite ? 'true' : 'false' }}
       }); }"
   @else
       href="{{ $song->getUrl() }}"
   @endif
   data-song-id="{{ $songId }}"
   data-context-type="song"
   {{-- Context Menu (Desktop: Right Click) --}}
   x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', {
       id: {{ $songId }},
       title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
       artist: '{{ $artistName ? addslashes($artistName) : '' }}',
       album_id: {{ $song->album_id ?? 'null' }},
       is_favorite: {{ $isFavorite ? 'true' : 'false' }}
   })"
   {{-- Context Menu (Mobile: Long Press) --}}
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
               title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
               artist: '{{ $artistName ? addslashes($artistName) : '' }}',
               album_id: {{ $song->album_id ?? 'null' }},
               is_favorite: {{ $isFavorite ? 'true' : 'false' }}
           });
       }, 500);
   "
   x-on:touchend="clearTimeout(touchTimer)"
   x-on:touchmove="
       const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                    Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
       if (moved) clearTimeout(touchTimer);
   "
   class="group bg-muzibu-gray md:hover:bg-spotify-black rounded-lg transition-all duration-300 relative overflow-hidden border-2 border-muzibu-gray touch-manipulation @if($compact) flex-shrink-0 w-[190px] px-3 pt-3 @else px-4 pt-4 @endif"
   x-bind:class="$store.player.currentSong?.id === {{ $songId }} ? 'border-muzibu-coral/60' : ''">

    {{-- Hover Shimmer/Buz Efekti - Sadece Desktop --}}
    <div class="absolute inset-0 overflow-hidden rounded-lg pointer-events-none hidden md:block">
        <div class="absolute -inset-full bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 -translate-x-full group-hover:animate-shimmer-sweep"></div>
    </div>

    <div class="relative @if($compact) mb-2 @else mb-4 @endif">
        {{-- Song Cover (Album artwork) --}}
        @if($albumCover)
            <img src="{{ $albumCover }}"
                 alt="{{ $song->getTranslation('title', app()->getLocale()) }}"
                 class="w-full aspect-square object-cover rounded-lg shadow-lg"
                 loading="lazy">
        @else
            <div class="w-full aspect-square bg-gradient-to-br from-muzibu-coral to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                <span class="text-5xl">🎵</span>
            </div>
        @endif

        {{-- Playing Badge --}}
        <span class="absolute top-2 left-2 z-10 bg-gradient-to-r from-muzibu-coral to-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg hidden"
              x-show="$store.player.currentSong?.id === {{ $songId }}"
              x-transition>
            🎵 Çalıyor
        </span>

        {{-- Play Button - Sadece Desktop Hover --}}
        <button x-on:click.stop.prevent="window.playContent('song', {{ $songId }})"
                class="absolute bottom-2 right-2 opacity-0 md:group-hover:opacity-100 transform translate-y-2 md:group-hover:translate-y-0 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-xl hover:scale-110 hover:bg-green-500 pointer-events-none md:pointer-events-auto">
            <i class="fas fa-play ml-1"></i>
        </button>

        {{-- Favorite + Menu Buttons - Sadece Desktop Hover --}}
        <div class="absolute top-2 right-2 z-10 flex gap-2 opacity-0 md:group-hover:opacity-100 transition-all pointer-events-none md:pointer-events-auto" x-on:click.stop.prevent>
            {{-- Favorite Button --}}
            <button x-on:click.stop.prevent="$store.favorites.toggle('song', {{ $songId }})"
                    class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all"
                    x-bind:class="$store.favorites.isFavorite('song', {{ $songId }}) ? 'text-muzibu-coral' : ''">
                <i class="text-sm"
                   x-bind:class="$store.favorites.isFavorite('song', {{ $songId }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
            </button>

            {{-- 3-Dot Menu Button --}}
            <button x-on:click.stop.prevent="$store.contextMenu.openContextMenu($event, 'song', {
                id: {{ $songId }},
                title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
                artist: '{{ $artistName ? addslashes($artistName) : '' }}',
                album_id: {{ $song->album_id ?? 'null' }},
                is_favorite: {{ $isFavorite ? 'true' : 'false' }}
            })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                <i class="fas fa-ellipsis-v text-sm"></i>
            </button>
        </div>
    </div>

    {{-- Text Area --}}
    <div class="h-12 overflow-hidden pb-4">
        <h3 class="font-semibold text-white text-sm leading-6 line-clamp-1">
            {{ $song->getTranslation('title', app()->getLocale()) }}
        </h3>
        <p class="text-xs text-gray-400 leading-6 line-clamp-1">
            @if($artistName)
                {{ $artistName }}
            @else
                &nbsp;
            @endif
        </p>
    </div>
</a>
@endif
