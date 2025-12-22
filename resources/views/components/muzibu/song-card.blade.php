@props(['song', 'preview' => false])

{{--
╔═══════════════════════════════════════════════════════════════════════════╗
║ MUZIBU COMPONENT: Song Card                                               ║
╠═══════════════════════════════════════════════════════════════════════════╣
║ Açıklama: Grid görünüm için şarkı kartı                                   ║
║           Favoriler ve arama sonuçları sayfalarında kullanılır             ║
║                                                                            ║
║ Props:                                                                     ║
║   - song: Model - Song modeli (zorunlu)                                   ║
║   - preview: Boolean - Sidebar preview modu (varsayılan: false)           ║
║                                                                            ║
║ Kullanım:                                                                  ║
║   <x-muzibu.song-card :song="$song" />                                    ║
║   <x-muzibu.song-card :song="$song" :preview="true" />                    ║
║                                                                            ║
║ Özellikler:                                                                ║
║   ✓ Album cover veya gradient background                                 ║
║   ✓ Spotify-style play button (bottom-right, hover)                      ║
║   ✓ Favorite & menu buttons (top-right, hover)                           ║
║   ✓ Context menu (right-click + long-press)                               ║
║   ✓ Current song ring highlight (2px coral)                               ║
║   ✓ Click to play/sidebar preview                                         ║
║                                                                            ║
║ Dependencies:                                                              ║
║   - Alpine.js: $store.player, $store.favorites, $store.contextMenu        ║
║   - Helpers: thumb(), addslashes()                                         ║
║   - Relations: song.album.coverMedia, song.artists                        ║
╚═══════════════════════════════════════════════════════════════════════════╝
--}}

@php
    $isFavorite = auth()->check() && method_exists($song, 'isFavoritedBy') && $song->isFavoritedBy(auth()->id());
    $coverUrl = $song->getCoverUrl(300, 300);
@endphp

<div @if($preview)
       @click="$store.sidebar.showPreview('song', {{ $song->id }}, {
           type: 'Song',
           id: {{ $song->id }},
           title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
           artist: '{{ $song->album && $song->album->artist ? addslashes($song->album->artist->getTranslation('title', app()->getLocale())) : '' }}',
           cover: '{{ $coverUrl ?? '' }}',
           is_favorite: {{ $isFavorite ? 'true' : 'false' }}
       })"
     @else
       @click="$store.player.playSong({{ $song->id }})"
     @endif
     {{-- Infinite Queue Data Attributes --}}
     data-song-id="{{ $song->id }}"
     data-album-id="{{ $song->album_id ?? '' }}"
     data-genre-id="{{ $song->genre_id ?? '' }}"
     data-context-type="song"
     {{-- Context Menu (Desktop: Right Click) --}}
     x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', {
         id: {{ $song->id }},
         title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
         artist: '{{ $song->album && $song->album->artist ? addslashes($song->album->artist->getTranslation('title', app()->getLocale())) : '' }}',
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
                 id: {{ $song->id }},
                 title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
                 artist: '{{ $song->album && $song->album->artist ? addslashes($song->album->artist->getTranslation('title', app()->getLocale())) : '' }}',
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
     class="group bg-muzibu-gray hover:bg-gray-700 rounded-lg px-4 pt-4 transition-all duration-300"
     {{-- Active Song State (JS will add this class when playing) --}}
     x-bind:class="$store.player.currentSong?.id === {{ $song->id }} ? 'ring-2 ring-muzibu-coral' : ''">

    <div class="relative mb-4">
        {{-- Song Cover (Song's own cover or Album Cover) --}}
        @if($coverUrl)
            <img src="{{ $coverUrl }}"
                 alt="{{ $song->getTranslation('title', app()->getLocale()) }}"
                 class="w-full aspect-square object-cover rounded-lg shadow-lg"
                 loading="lazy">
        @else
            <div class="w-full aspect-square bg-gradient-to-br from-muzibu-coral to-orange-600 rounded-lg flex items-center justify-center shadow-lg">
                <i class="fas fa-music text-white text-5xl opacity-50"></i>
            </div>
        @endif

        {{-- Playing Badge (JS controlled) --}}
        <span class="absolute top-2 left-2 z-10 bg-gradient-to-r from-muzibu-coral to-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg hidden"
              x-show="$store.player.currentSong?.id === {{ $song->id }}"
              x-transition>
            🎵 Çalıyor
        </span>

        {{-- Play Button - Spotify Style Bottom Right (Hover) --}}
        <button x-on:click.stop.prevent="$store.player.playSong({{ $song->id }})"
                class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-xl hover:scale-110 hover:bg-green-500">
            <i class="fas fa-play ml-1"></i>
        </button>

        {{-- Favorite + Menu Buttons (Sağ Üst) - HOVER'DA GÖRÜNÜR --}}
        <div class="absolute top-2 right-2 z-10 flex gap-2 opacity-0 group-hover:opacity-100 transition-all" x-on:click.stop.prevent>
            {{-- Favorite Button --}}
            <button x-on:click.stop.prevent="$store.favorites.toggle('song', {{ $song->id }})"
                    class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all"
                    x-bind:class="$store.favorites.isFavorite('song', {{ $song->id }}) ? 'text-muzibu-coral' : ''">
                <i class="text-sm"
                   x-bind:class="$store.favorites.isFavorite('song', {{ $song->id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
            </button>

            {{-- 3-Dot Menu Button --}}
            <button x-on:click.stop.prevent="$store.contextMenu.openContextMenu($event, 'song', {
                id: {{ $song->id }},
                title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
                artist: '{{ $song->album && $song->album->artist ? addslashes($song->album->artist->getTranslation('title', app()->getLocale())) : '' }}',
                is_favorite: {{ $isFavorite ? 'true' : 'false' }}
            })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                <i class="fas fa-ellipsis-v text-sm"></i>
            </button>
        </div>
    </div>

    {{-- Text Area (Fixed Height - Always 2 rows) --}}
    <div class="h-12 overflow-hidden pb-4">
        <h3 class="font-semibold text-white text-sm leading-6 line-clamp-1">
            {{ $song->getTranslation('title', app()->getLocale()) }}
        </h3>
        <p class="text-xs text-gray-400 leading-6 line-clamp-1">
            @if($song->album && $song->album->artist)
                {{ $song->album->artist->getTranslation('title', app()->getLocale()) }}
            @else
                &nbsp;
            @endif
        </p>
    </div>
</div>
