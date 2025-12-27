@props(['album', 'preview' => true, 'compact' => false])

{{-- ⚠️⚠️⚠️ UYARI: PREVIEW MODU HER ZAMAN TRUE OLMALI! ⚠️⚠️⚠️ --}}
{{--
    🚨 KRİTİK: Bu component MUTLAKA :preview="true" ile çağrılmalı!

    ✅ DOĞRU KULLANIM:
    <x-muzibu.album-card :album="$album" :preview="true" />

    ❌ YANLIŞ KULLANIM:
    <x-muzibu.album-card :album="$album" />  ← Bu YANLIŞ! Preview=false olur!

    📋 Neden Önemli?
    - Desktop'ta sidebar preview açılması için preview=true gerekli
    - Preview=false ise her tıklama yeni sayfaya gider
    - Varsayılan değer: true (değiştirilirse tüm sistem bozulur)

    🔍 Kontrol:
    Console'da "[ALBUM-CARD]" yazısını ara
    Preview=false kullanımları loglanır
--}}
{{-- Muzibu Album Card Component --}}
{{-- Features: Infinite queue data attributes, playing badge, context menu --}}

<a @if($preview)
       href="/albums/{{ $album->getTranslation('slug', app()->getLocale()) }}"
       @click="if (window.innerWidth >= 768) { $event.preventDefault(); $store.sidebar.showPreview('album', {{ $album->id }}, {
           type: 'Album',
           id: {{ $album->id }},
           title: '{{ addslashes($album->getTranslation('title', app()->getLocale())) }}',
           artist: '{{ $album->artist ? addslashes($album->artist->getTranslation('title', app()->getLocale())) : '' }}',
           cover: '{{ $album->coverMedia ? thumb($album->coverMedia, 300, 300, ['scale' => 1]) : '' }}',
           is_favorite: {{ is_favorited('album', $album->album_id ?? $album->id) ? 'true' : 'false' }}
       }); }"
   @else
       href="/albums/{{ $album->getTranslation('slug', app()->getLocale()) }}"
   @endif
   {{-- Infinite Queue Data Attributes --}}
   data-album-id="{{ $album->id }}"
   data-genre-id="{{ $album->genre_id ?? '' }}"
   data-context-type="album"
   {{-- Context Menu (Desktop: Right Click) --}}
   x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'album', {
       id: {{ $album->id }},
       title: '{{ addslashes($album->getTranslation('title', app()->getLocale())) }}',
       artist: '{{ $album->artist ? addslashes($album->artist->getTranslation('title', app()->getLocale())) : '' }}',
       is_favorite: {{ is_favorited('album', $album->album_id ?? $album->id) ? 'true' : 'false' }}
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
           }, 'album', {
               id: {{ $album->id }},
               title: '{{ addslashes($album->getTranslation('title', app()->getLocale())) }}',
               artist: '{{ $album->artist ? addslashes($album->artist->getTranslation('title', app()->getLocale())) : '' }}',
               is_favorite: {{ is_favorited('album', $album->album_id ?? $album->id) ? 'true' : 'false' }}
           });
       }, 500);
   "
   x-on:touchend="clearTimeout(touchTimer)"
   x-on:touchmove="
       const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                    Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
       if (moved) clearTimeout(touchTimer);
   "
   class="group bg-muzibu-gray hover:bg-gray-700 rounded-lg transition-all duration-300 @if($compact) flex-shrink-0 w-[190px] px-3 pt-3 @else px-4 pt-4 @endif"
   {{-- Active Album State (JS will add this class when playing) --}}
   x-bind:class="$store.player.currentContext?.type === 'album' && $store.player.currentContext?.id === {{ $album->id }} ? 'ring-2 ring-muzibu-coral' : ''">

    <div class="relative @if($compact) mb-2 @else mb-4 @endif">
        {{-- Album Cover --}}
        @if($album->media_id && $album->coverMedia)
            <img src="{{ thumb($album->coverMedia, 300, 300, ['scale' => 1]) }}"
                 alt="{{ $album->getTranslation('title', app()->getLocale()) }}"
                 class="w-full aspect-square object-cover rounded-lg shadow-lg"
                 loading="lazy">
        @else
            <div class="w-full aspect-square bg-gradient-to-br from-muzibu-coral to-orange-600 rounded-lg flex items-center justify-center shadow-lg">
                <span class="text-5xl">💿</span>
            </div>
        @endif

        {{-- Playing Badge (JS controlled) --}}
        <span class="absolute top-2 left-2 z-10 bg-gradient-to-r from-muzibu-coral to-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg hidden"
              x-show="$store.player.currentContext?.type === 'album' && $store.player.currentContext?.id === {{ $album->id }}"
              x-transition>
            🎵 Çalıyor
        </span>

        {{-- Play Button - Spotify Style Bottom Right (Hover) --}}
        <button x-on:click.stop.prevent="window.playContent('album', {{ $album->id }})"
                class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-xl hover:scale-110 hover:bg-green-500">
            <i class="fas fa-play ml-1"></i>
        </button>

        {{-- Favorite + Menu Buttons (Sağ Üst) - HOVER'DA GÖRÜNÜR --}}
        <div class="absolute top-2 right-2 z-10 flex gap-2 opacity-0 group-hover:opacity-100 transition-all" x-on:click.stop.prevent>
            {{-- Favorite Button --}}
            <button x-on:click.stop.prevent="$store.favorites.toggle('album', {{ $album->id }})"
                    class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all"
                    x-bind:class="$store.favorites.isFavorite('album', {{ $album->id }}) ? 'text-muzibu-coral' : ''">
                <i class="text-sm"
                   x-bind:class="$store.favorites.isFavorite('album', {{ $album->id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
            </button>

            {{-- 3-Dot Menu Button --}}
            <button x-on:click.stop.prevent="$store.contextMenu.openContextMenu($event, 'album', {
                id: {{ $album->id }},
                title: '{{ addslashes($album->getTranslation('title', app()->getLocale())) }}',
                artist: '{{ $album->artist ? addslashes($album->artist->getTranslation('title', app()->getLocale())) : '' }}',
                is_favorite: {{ is_favorited('album', $album->album_id ?? $album->id) ? 'true' : 'false' }}
            })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                <i class="fas fa-ellipsis-v text-sm"></i>
            </button>
        </div>
    </div>

    {{-- Text Area (Fixed Height - Always 2 rows) --}}
    <div class="h-12 overflow-hidden pb-4">
        <h3 class="font-semibold text-white text-sm leading-6 line-clamp-1">
            {{ $album->getTranslation('title', app()->getLocale()) }}
        </h3>
        <p class="text-xs text-gray-400 leading-6 line-clamp-1">
            @if($album->artist)
                {{ $album->artist->getTranslation('title', app()->getLocale()) }}
            @else
                &nbsp;
            @endif
        </p>
    </div>
</a>
