@props(['playlist', 'index' => 0])

{{--
╔═══════════════════════════════════════════════════════════════════════════╗
║ MUZIBU COMPONENT: Playlist Quick Card                                     ║
╠═══════════════════════════════════════════════════════════════════════════╣
║ Açıklama: Ana sayfa Quick Access için yatay playlist kartı                ║
║           Spotify tarzı 2 satırlık horizontal scroll grid için tasarlandı ║
║                                                                            ║
║ Props:                                                                     ║
║   - playlist: Model - Playlist modeli (zorunlu)                           ║
║   - index: Integer - Sıra numarası (varsayılan: 0)                        ║
║                                                                            ║
║ Kullanım:                                                                  ║
║   <x-muzibu.playlist-quick-card :playlist="$playlist" :index="0" />       ║
║                                                                            ║
║ Özellikler:                                                                ║
║   ✓ Horizontal layout (64x64 cover + title)                              ║
║   ✓ Hover'da favorite & menu butonları                                    ║
║   ✓ Sidebar preview integration                                           ║
║   ✓ Context menu (right-click + long-press)                               ║
║   ✓ Touch feedback (500ms vibration)                                      ║
║                                                                            ║
║ Dependencies:                                                              ║
║   - Alpine.js: $store.sidebar, $store.favorites, $store.contextMenu       ║
║   - Helpers: thumb(), addslashes()                                         ║
║   - Auth: auth()->check(), isFavoritedBy()                                ║
╚═══════════════════════════════════════════════════════════════════════════╝
--}}

@php
    $isFavorite = auth()->check() && method_exists($playlist, 'isFavoritedBy') && $playlist->isFavoritedBy(auth()->id());
    $isMine = auth()->check() && isset($playlist->user_id) && $playlist->user_id == auth()->id();
@endphp

<div class="playlist-card group flex items-center gap-3 bg-white/5 hover:bg-white/10 rounded transition-all cursor-pointer overflow-hidden h-16 relative"
     data-playlist-id="{{ $playlist->id ?? $playlist->playlist_id }}"
     data-context-type="playlist"
     x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
     x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'playlist', {
         id: {{ $playlist->id ?? $playlist->playlist_id }},
         title: '{{ addslashes($playlist->getTranslation('title', app()->getLocale())) }}',
         is_favorite: {{ $isFavorite ? 'true' : 'false' }},
         is_mine: {{ $isMine ? 'true' : 'false' }}
     })"
     x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'playlist', { id: {{ $playlist->id ?? $playlist->playlist_id }}, title: '{{ addslashes($playlist->getTranslation('title', app()->getLocale())) }}', is_favorite: {{ $isFavorite ? 'true' : 'false' }}, is_mine: {{ $isMine ? 'true' : 'false' }} }); }, 500);"
     x-on:touchend="clearTimeout(touchTimer)"
     x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer);"
     @click="$store.sidebar.showPreview('playlist', {{ $playlist->id ?? $playlist->playlist_id }}, {
         type: 'Playlist',
         id: {{ $playlist->id ?? $playlist->playlist_id }},
         title: '{{ addslashes($playlist->getTranslation('title', app()->getLocale())) }}',
         cover: '{{ $playlist->coverMedia ? thumb($playlist->coverMedia, 300, 300, ['scale' => 1]) : '' }}',
         is_favorite: {{ $isFavorite ? 'true' : 'false' }}
     })">

    {{-- Cover Image --}}
    <div class="w-16 h-16 flex-shrink-0 bg-gradient-to-br from-muzibu-coral to-pink-600">
        @if($playlist->coverMedia)
            <img src="{{ thumb($playlist->coverMedia, 64, 64, ['scale' => 1]) }}"
                 alt="{{ $playlist->getTranslation('title', app()->getLocale()) }}"
                 loading="{{ $index < 6 ? 'eager' : 'lazy' }}"
                 class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-xl text-white/90">🎵</div>
        @endif
    </div>

    {{-- Title --}}
    <div class="flex-1 min-w-0 pr-20">
        <h3 class="font-semibold text-white text-sm truncate">
            {{ $playlist->getTranslation('title', app()->getLocale()) }}
        </h3>
    </div>

    {{-- Favorite + Menu Buttons - HOVER'DA GÖRÜNÜR --}}
    <div class="absolute right-2 top-1/2 -translate-y-1/2 z-10 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
        {{-- Favorite Button --}}
        <button @click.stop="$store.favorites.toggle('playlist', {{ $playlist->id ?? $playlist->playlist_id }})"
                class="w-7 h-7 bg-black/40 hover:bg-black/60 rounded-full flex items-center justify-center text-white/70 hover:text-white transition-all"
                x-bind:class="$store.favorites.isFavorite('playlist', {{ $playlist->id ?? $playlist->playlist_id }}) ? 'text-muzibu-coral' : ''">
            <i class="text-xs"
               x-bind:class="$store.favorites.isFavorite('playlist', {{ $playlist->id ?? $playlist->playlist_id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
        </button>

        {{-- 3-Dot Menu Button --}}
        <button @click.stop="$store.contextMenu.openContextMenu($event, 'playlist', {
            id: {{ $playlist->id ?? $playlist->playlist_id }},
            title: '{{ addslashes($playlist->getTranslation('title', app()->getLocale())) }}',
            is_favorite: {{ $isFavorite ? 'true' : 'false' }},
            is_mine: {{ $isMine ? 'true' : 'false' }}
        })" class="w-7 h-7 bg-black/40 hover:bg-black/60 rounded-full flex items-center justify-center text-white/70 hover:text-white transition-all">
            <i class="fas fa-ellipsis-v text-xs"></i>
        </button>
    </div>
</div>
