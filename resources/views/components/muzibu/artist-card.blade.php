@props(['artist', 'preview' => false])

{{--
╔═══════════════════════════════════════════════════════════════════════════╗
║ MUZIBU COMPONENT: Artist Card                                             ║
╠═══════════════════════════════════════════════════════════════════════════╣
║ Açıklama: Sanatçı kartı (circular profile layout)                         ║
║           Spotify-style round profile images with hover effects            ║
║                                                                            ║
║ Props:                                                                     ║
║   - artist: Model - Artist modeli (zorunlu)                               ║
║   - preview: Boolean - Sidebar preview modu (varsayılan: false)           ║
║                                                                            ║
║ Kullanım:                                                                  ║
║   <x-muzibu.artist-card :artist="$artist" />                              ║
║   <x-muzibu.artist-card :artist="$artist" :preview="true" />              ║
║                                                                            ║
║ Özellikler:                                                                ║
║   ✓ Circular profile image (rounded-full)                                ║
║   ✓ Gradient fallback (blue → purple) if no photo                        ║
║   ✓ Spotify-style play button (bottom-right, hover)                      ║
║   ✓ Favorite & menu buttons (top-right, hover)                           ║
║   ✓ Context menu (right-click + long-press)                               ║
║   ✓ Touch feedback (500ms vibration)                                      ║
║   ✓ "Sanatçı" label (centered below name)                                 ║
║                                                                            ║
║ Dependencies:                                                              ║
║   - Alpine.js: $store.player, $store.favorites, $store.contextMenu,       ║
║                 $store.sidebar                                             ║
║   - Helpers: thumb(), addslashes()                                         ║
║   - Auth: auth()->check(), isFavoritedBy()                                ║
║   - Relations: artist.photoMedia                                          ║
╚═══════════════════════════════════════════════════════════════════════════╝
--}}

<a @if($preview)
       href="javascript:void(0)"
       @click="$store.sidebar.showPreview('artist', {{ $artist->id }}, {
           type: 'Artist',
           id: {{ $artist->id }},
           title: '{{ addslashes($artist->getTranslation('title', app()->getLocale())) }}',
           cover: '{{ $artist->photoMedia ? thumb($artist->photoMedia, 300, 300, ['scale' => 1]) : '' }}',
           is_favorite: {{ auth()->check() && method_exists($artist, 'isFavoritedBy') && $artist->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
       })"
   @else
       href="/artists/{{ $artist->getTranslation('slug', app()->getLocale()) }}"
   @endif
   data-artist-id="{{ $artist->id }}"
   data-context-type="artist"
   x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'artist', {
       id: {{ $artist->id }},
       title: '{{ addslashes($artist->getTranslation('title', app()->getLocale())) }}',
       is_favorite: {{ auth()->check() && method_exists($artist, 'isFavoritedBy') && $artist->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
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
               id: {{ $artist->id }},
               title: '{{ addslashes($artist->getTranslation('title', app()->getLocale())) }}',
               is_favorite: {{ auth()->check() && method_exists($artist, 'isFavoritedBy') && $artist->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
           });
       }, 500);
   "
   x-on:touchend="clearTimeout(touchTimer)"
   x-on:touchmove="
       const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                    Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
       if (moved) clearTimeout(touchTimer);
   "
   class="group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-4 transition-all duration-300">

    <div class="relative mb-4">
        {{-- Artist Photo (Circular) --}}
        @if($artist->photoMedia)
            <img src="{{ thumb($artist->photoMedia, 300, 300, ['scale' => 1]) }}"
                 alt="{{ $artist->getTranslation('title', app()->getLocale()) }}"
                 class="w-full aspect-square object-cover rounded-full shadow-lg"
                 loading="lazy">
        @else
            <div class="w-full aspect-square bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                <i class="fas fa-user text-white text-5xl opacity-50"></i>
            </div>
        @endif

        {{-- Play Button - Spotify Style Bottom Right (Hover) --}}
        <button x-on:click.stop.prevent="$store.player.playArtist({{ $artist->id }})"
                class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-xl hover:scale-110 hover:bg-green-500">
            <i class="fas fa-play ml-1"></i>
        </button>

        {{-- Favorite + Menu Buttons (Cover Sağ Üst) - HOVER'DA GÖRÜNÜR --}}
        <div class="absolute top-2 right-2 z-10 flex gap-2 opacity-0 group-hover:opacity-100 transition-all" x-on:click.stop.prevent>
            {{-- Favorite Button --}}
            <button x-on:click.stop.prevent="$store.favorites.toggle('artist', {{ $artist->id }})"
                    class="w-8 h-8 bg-black/70 hover:bg-black/90 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all hover:scale-110"
                    x-bind:class="$store.favorites.isFavorite('artist', {{ $artist->id }}) ? 'text-muzibu-coral' : ''">
                <i class="text-sm"
                   x-bind:class="$store.favorites.isFavorite('artist', {{ $artist->id }}) ? 'fas fa-heart' : 'far fa-heart hover:text-muzibu-coral'"></i>
            </button>

            {{-- 3-Dot Menu Button --}}
            <button x-on:click.stop.prevent="$store.contextMenu.openContextMenu($event, 'artist', {
                id: {{ $artist->id }},
                title: '{{ addslashes($artist->getTranslation('title', app()->getLocale())) }}',
                is_favorite: {{ auth()->check() && method_exists($artist, 'isFavoritedBy') && $artist->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
            })" class="w-8 h-8 bg-black/70 hover:bg-black/90 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all hover:scale-110">
                <i class="fas fa-ellipsis-v text-sm"></i>
            </button>
        </div>
    </div>

    {{-- Artist Name --}}
    <h3 class="font-semibold text-white mb-1 truncate text-center">
        {{ $artist->getTranslation('title', app()->getLocale()) }}
    </h3>

    {{-- Artist Type Label --}}
    <p class="text-sm text-gray-400 truncate text-center">
        Sanatçı
    </p>
</a>
