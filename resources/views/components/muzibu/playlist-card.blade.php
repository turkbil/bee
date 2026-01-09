@props(['playlist', 'preview' => true, 'compact' => false, 'index' => 0])

{{-- ⚠️⚠️⚠️ UYARI: PREVIEW MODU HER ZAMAN TRUE OLMALI! ⚠️⚠️⚠️ --}}
{{--
    🚨 KRİTİK: Bu component MUTLAKA :preview="true" ile çağrılmalı!

    ✅ DOĞRU KULLANIM:
    <x-muzibu.playlist-card :playlist="$playlist" :preview="true" />

    ❌ YANLIŞ KULLANIM:
    <x-muzibu.playlist-card :playlist="$playlist" />  ← Bu YANLIŞ! Preview=false olur!

    📋 Neden Önemli?
    - Desktop'ta sidebar preview açılması için preview=true gerekli
    - Preview=false ise her tıklama yeni sayfaya gider (eskden davranış)
    - Varsayılan değer: true (değiştirilirse tüm sistem bozulur)

    🔍 Kontrol:
    Console'da "[PLAYLIST-CARD]" yazısını ara
    Preview=false kullanımları loglanır
--}}
{{-- Muzibu Playlist Card Component --}}

<a @if($preview)
       href="/playlists/{{ $playlist->getTranslation('slug', app()->getLocale()) }}"
       @click="if (window.innerWidth >= 768) { $event.preventDefault(); $store.sidebar.showPreview('playlist', {{ $playlist->id }}, {
           type: 'Playlist',
           id: {{ $playlist->id }},
           title: '{{ addslashes($playlist->getTranslation('title', app()->getLocale())) }}',
           cover: '{{ $playlist->getFirstMedia('hero') ? thumb($playlist->getFirstMedia('hero'), 300, 300, ['scale' => 1]) : '' }}',
           is_favorite: {{ is_favorited('playlist', $playlist->playlist_id ?? $playlist->id) ? 'true' : 'false' }}
       }); }"
   @else
       href="/playlists/{{ $playlist->getTranslation('slug', app()->getLocale()) }}"
   @endif
   data-playlist-id="{{ $playlist->id }}"
   data-context-type="playlist"
   x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'playlist', {
       id: {{ $playlist->id }},
       title: '{{ addslashes($playlist->getTranslation('title', app()->getLocale())) }}',
       is_favorite: {{ is_favorited('playlist', $playlist->playlist_id ?? $playlist->id) ? 'true' : 'false' }},
       is_mine: {{ $playlist->user_id && auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }}
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
           }, 'playlist', {
               id: {{ $playlist->id }},
               title: '{{ addslashes($playlist->getTranslation('title', app()->getLocale())) }}',
               is_favorite: {{ is_favorited('playlist', $playlist->playlist_id ?? $playlist->id) ? 'true' : 'false' }},
               is_mine: {{ $playlist->user_id && auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }}
           });
       }, 500);
   "
   x-on:touchend="clearTimeout(touchTimer)"
   x-on:touchmove="
       const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                    Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
       if (moved) clearTimeout(touchTimer);
   "
   class="group bg-muzibu-gray hover:bg-spotify-black rounded-lg transition-all duration-300 relative overflow-hidden border-2 border-muzibu-gray @if($compact) flex-shrink-0 w-[190px] px-3 pt-3 @else px-4 pt-4 @endif"
   x-bind:class="$store.player.currentContext?.type === 'playlist' && $store.player.currentContext?.id === {{ $playlist->id }} ? 'border-muzibu-coral/60' : ''">

    {{-- Hover Shimmer/Buz Efekti --}}
    <div class="absolute inset-0 overflow-hidden rounded-lg pointer-events-none">
        <div class="absolute -inset-full bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 -translate-x-full group-hover:animate-shimmer-sweep"></div>
    </div>

    <div class="relative @if($compact) mb-2 @else mb-4 @endif">
        @php $heroMedia = $playlist->getFirstMedia('hero'); @endphp
        @if($heroMedia)
            <img src="{{ thumb($heroMedia, 300, 300, ['scale' => 1]) }}"
                 alt="{{ $playlist->getTranslation('title', app()->getLocale()) }}"
                 class="w-full aspect-square object-cover rounded-lg shadow-lg"
                 loading="{{ $index < 4 ? 'eager' : 'lazy' }}">
        @else
            <div class="w-full aspect-square bg-gradient-to-br from-muzibu-coral to-orange-600 rounded-lg flex items-center justify-center shadow-lg">
                <span class="text-5xl">🎵</span>
            </div>
        @endif

        {{-- Playing Badge --}}
        <span class="absolute top-2 left-2 z-10 bg-gradient-to-r from-muzibu-coral to-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg hidden"
              x-show="$store.player.currentContext?.type === 'playlist' && $store.player.currentContext?.id === {{ $playlist->id }}"
              x-transition>
            🎵 Çalıyor
        </span>

        {{-- Play Button --}}
        <button x-on:click.stop.prevent="window.playContent('playlist', {{ $playlist->id }})"
                class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-xl hover:scale-110 hover:bg-green-500">
            <i class="fas fa-play ml-1"></i>
        </button>

        {{-- Favorite + Menu Buttons (Sağ Üst) - HOVER'DA GÖRÜNÜR --}}
        <div class="absolute top-2 right-2 z-10 flex gap-2 opacity-0 group-hover:opacity-100 transition-all" x-on:click.stop.prevent>
            {{-- Favorite Button --}}
            <button x-on:click.stop.prevent="$store.favorites.toggle('playlist', {{ $playlist->id }})"
                    class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all"
                    x-bind:class="$store.favorites.isFavorite('playlist', {{ $playlist->id }}) ? 'text-muzibu-coral' : ''">
                <i class="text-sm"
                   x-bind:class="$store.favorites.isFavorite('playlist', {{ $playlist->id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
            </button>

            {{-- 3-Dot Menu Button --}}
            <button x-on:click.stop.prevent="$store.contextMenu.openContextMenu($event, 'playlist', {
                id: {{ $playlist->id }},
                title: '{{ addslashes($playlist->getTranslation('title', app()->getLocale())) }}',
                is_favorite: {{ is_favorited('playlist', $playlist->playlist_id ?? $playlist->id) ? 'true' : 'false' }},
                is_mine: {{ $playlist->user_id && auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }}
            })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                <i class="fas fa-ellipsis-v text-sm"></i>
            </button>
        </div>
    </div>

    {{-- Text Area (Fixed Height - Always 2 rows) --}}
    <div class="h-12 overflow-hidden pb-4">
        <h3 class="font-semibold text-white text-sm leading-6 line-clamp-1">
            {{ $playlist->getTranslation('title', app()->getLocale()) }}
        </h3>
        <p class="text-xs text-gray-400 leading-6 line-clamp-1">
            {{ $playlist->getTurkishFormattedDuration() }}
        </p>
    </div>
</a>
