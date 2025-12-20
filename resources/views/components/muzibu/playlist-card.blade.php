@props(['playlist'])

{{-- Muzibu Playlist Card Component --}}
{{-- Usage: <x-muzibu.playlist-card :playlist="$playlist" /> --}}

<a href="/playlists/{{ $playlist->getTranslation('slug', app()->getLocale()) }}"
   data-playlist-id="{{ $playlist->id }}"
   data-context-type="playlist"
   x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'playlist', {
       id: {{ $playlist->id }},
       title: '{{ addslashes($playlist->getTranslation('title', app()->getLocale())) }}',
       is_favorite: {{ auth()->check() && method_exists($playlist, 'isFavoritedBy') && $playlist->isFavoritedBy(auth()->id()) ? 'true' : 'false' }},
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
               is_favorite: {{ auth()->check() && method_exists($playlist, 'isFavoritedBy') && $playlist->isFavoritedBy(auth()->id()) ? 'true' : 'false' }},
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
   class="group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-4 transition-all duration-300"
   x-bind:class="$store.player.currentContext?.type === 'playlist' && $store.player.currentContext?.id === {{ $playlist->id }} ? 'ring-2 ring-muzibu-coral' : ''">

    <div class="relative mb-4">
        @if($playlist->media_id && $playlist->coverMedia)
            <img src="{{ thumb($playlist->coverMedia, 300, 300, ['scale' => 1]) }}"
                 alt="{{ $playlist->getTranslation('title', app()->getLocale()) }}"
                 class="w-full aspect-square object-cover rounded-lg shadow-lg"
                 loading="lazy">
        @else
            <div class="w-full aspect-square bg-gradient-to-br from-muzibu-coral to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                <span class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl">🎵</span>
            </div>
        @endif

        {{-- Playing Badge --}}
        <span class="absolute top-2 left-2 z-10 bg-gradient-to-r from-muzibu-coral to-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg hidden"
              x-show="$store.player.currentContext?.type === 'playlist' && $store.player.currentContext?.id === {{ $playlist->id }}"
              x-transition>
            🎵 Çalıyor
        </span>

        {{-- Play Button --}}
        <button x-on:click.stop.prevent="$store.player.playPlaylist({{ $playlist->id }})"
                class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-xl hover:scale-110 hover:bg-green-500">
            <i class="fas fa-play ml-1"></i>
        </button>

        {{-- Favorite + Menu Buttons (Cover Sağ Üst) - HOVER'DA GÖRÜNÜR --}}
        <div class="absolute top-2 right-2 z-10 flex gap-2 opacity-0 group-hover:opacity-100 transition-all" x-on:click.stop.prevent>
            {{-- Favorite Button --}}
            <button x-on:click.stop="$store.favorites.toggle('playlist', {{ $playlist->id }})"
                    class="w-8 h-8 bg-black/70 hover:bg-black/90 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all hover:scale-110"
                    x-bind:class="$store.favorites.isFavorite('playlist', {{ $playlist->id }}) ? 'text-muzibu-coral' : ''">
                <i class="text-sm"
                   x-bind:class="$store.favorites.isFavorite('playlist', {{ $playlist->id }}) ? 'fas fa-heart' : 'far fa-heart hover:text-muzibu-coral'"></i>
            </button>

            {{-- 3-Dot Menu Button --}}
            <button x-on:click="$store.contextMenu.openContextMenu($event, 'playlist', {
                id: {{ $playlist->id }},
                title: '{{ addslashes($playlist->getTranslation('title', app()->getLocale())) }}',
                is_favorite: {{ auth()->check() && method_exists($playlist, 'isFavoritedBy') && $playlist->isFavoritedBy(auth()->id()) ? 'true' : 'false' }},
                is_mine: {{ $playlist->user_id && auth()->check() && $playlist->user_id == auth()->id() ? 'true' : 'false' }}
            })" class="w-8 h-8 bg-black/70 hover:bg-black/90 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all hover:scale-110">
                <i class="fas fa-ellipsis-v text-sm"></i>
            </button>
        </div>
    </div>

    <h3 class="font-semibold text-white mb-1 truncate">
        {{ $playlist->getTranslation('title', app()->getLocale()) }}
    </h3>

    <p class="text-sm text-gray-400 truncate">
        {{ $playlist->songs_count ?? 0 }} Şarkı
    </p>
</a>
