@props(['artist', 'compact' => false])

{{-- Muzibu Artist Card Component --}}
{{-- Usage: <x-muzibu.artist-card :artist="$artist" /> --}}
{{-- STANDARD PATTERN: Same layout as playlist/album/genre cards --}}

<a href="/artists/{{ $artist->getTranslation('slug', app()->getLocale()) }}"
   data-spa
   data-artist-id="{{ $artist->artist_id }}"
   data-context-type="artist"
   x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'artist', {
       id: {{ $artist->artist_id }},
       title: '{{ addslashes($artist->getTranslation('title', app()->getLocale())) }}',
       cover_url: '{{ $artist->photoMedia ? thumb($artist->photoMedia, 300, 300, ['scale' => 1]) : '' }}',
       is_favorite: {{ is_favorited('artist', $artist->artist_id) ? 'true' : 'false' }}
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
           }, 'artist', {
               id: {{ $artist->artist_id }},
               title: '{{ addslashes($artist->getTranslation('title', app()->getLocale())) }}',
               cover_url: '{{ $artist->photoMedia ? thumb($artist->photoMedia, 300, 300, ['scale' => 1]) : '' }}',
               is_favorite: {{ is_favorited('artist', $artist->artist_id) ? 'true' : 'false' }}
           });
       }, 500);
   "
   x-on:touchend="clearTimeout(touchTimer)"
   x-on:touchmove="
       const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                    Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
       if (moved) clearTimeout(touchTimer);
   "
   class="group bg-muzibu-gray hover:bg-spotify-black rounded-lg px-4 pt-4 transition-all duration-300 relative overflow-hidden border-2 border-muzibu-gray">

    {{-- Hover Shimmer/Buz Efekti --}}
    <div class="absolute inset-0 overflow-hidden rounded-lg pointer-events-none">
        <div class="absolute -inset-full bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 -translate-x-full group-hover:animate-shimmer-sweep"></div>
    </div>

    <div class="relative mb-4">
        {{-- Artist Photo (Rounded for artist style) --}}
        @php $heroMedia = $artist->getFirstMedia('hero'); @endphp
        @if($heroMedia)
            <img src="{{ thumb($heroMedia, 300, 300, ['scale' => 1]) }}"
                 alt="{{ $artist->getTranslation('title', app()->getLocale()) }}"
                 class="w-full aspect-square object-cover rounded-full shadow-lg"
                 loading="lazy">
        @else
            <div class="w-full aspect-square bg-gradient-to-br from-purple-600 to-pink-600 rounded-full flex items-center justify-center shadow-lg">
                <span class="text-5xl">🎤</span>
            </div>
        @endif

        {{-- Play Button - Spotify Style Bottom Right --}}
        <button x-on:click.stop.prevent="window.playContent('artist', {{ $artist->artist_id }})"
                class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-xl hover:scale-110 hover:bg-green-500">
            <i class="fas fa-play ml-1"></i>
        </button>

        {{-- Favorite + Menu Buttons (Top-right, hover only) --}}
        <div class="absolute top-2 right-2 z-10 flex gap-2 opacity-0 group-hover:opacity-100 transition-all" x-on:click.stop.prevent>
            {{-- Favorite Button --}}
            <button x-on:click.stop.prevent="$store.favorites.toggle('artist', {{ $artist->artist_id }})"
                    class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all"
                    x-bind:class="$store.favorites.isFavorite('artist', {{ $artist->artist_id }}) ? 'text-muzibu-coral' : ''">
                <i class="text-sm"
                   x-bind:class="$store.favorites.isFavorite('artist', {{ $artist->artist_id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
            </button>

            {{-- 3-Dot Menu Button --}}
            <button x-on:click.stop.prevent="$store.contextMenu.openContextMenu($event, 'artist', {
                id: {{ $artist->artist_id }},
                title: '{{ addslashes($artist->getTranslation('title', app()->getLocale())) }}',
                cover_url: '{{ $artist->photoMedia ? thumb($artist->photoMedia, 300, 300, ['scale' => 1]) : '' }}',
                is_favorite: {{ is_favorited('artist', $artist->artist_id) ? 'true' : 'false' }}
            })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                <i class="fas fa-ellipsis-v text-sm"></i>
            </button>
        </div>
    </div>

    {{-- Text Area (Fixed Height - ALWAYS 48px / 3rem) --}}
    <div class="h-12 overflow-hidden pb-4">
        <h3 class="font-semibold text-white text-sm leading-6 line-clamp-1 text-center">
            {{ $artist->getTranslation('title', app()->getLocale()) }}
        </h3>
        <p class="text-xs text-gray-400 leading-6 line-clamp-1 text-center">
            Sanatci
        </p>
    </div>
</a>
