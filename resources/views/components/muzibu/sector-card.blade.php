@props(['sector', 'preview' => false])

{{-- Muzibu Sector Card Component --}}
{{-- Usage: <x-muzibu.sector-card :sector="$sector" /> --}}
{{-- STANDARD PATTERN: Same layout as playlist/album/song/genre cards --}}

<a @if($preview)
       href="javascript:void(0)"
       @click="$store.sidebar.showPreview('sector', {{ $sector->sector_id }}, {
           type: 'Sector',
           id: {{ $sector->sector_id }},
           title: '{{ addslashes($sector->title['tr'] ?? $sector->title['en'] ?? 'Sector') }}',
           is_favorite: {{ auth()->check() && method_exists($sector, 'isFavoritedBy') && $sector->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
       })"
   @else
       href="/sectors/{{ $sector->getTranslation('slug', app()->getLocale()) }}"
   @endif
   data-sector-id="{{ $sector->sector_id }}"
   data-context-type="sector"
   x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'sector', {
       id: {{ $sector->sector_id }},
       title: '{{ addslashes($sector->title['tr'] ?? $sector->title['en'] ?? 'Sector') }}',
       is_favorite: {{ auth()->check() && method_exists($sector, 'isFavoritedBy') && $sector->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
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
           }, 'sector', {
               id: {{ $sector->sector_id }},
               title: '{{ addslashes($sector->title['tr'] ?? $sector->title['en'] ?? 'Sector') }}',
               is_favorite: {{ auth()->check() && method_exists($sector, 'isFavoritedBy') && $sector->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
           });
       }, 500);
   "
   x-on:touchend="clearTimeout(touchTimer)"
   x-on:touchmove="
       const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                    Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
       if (moved) clearTimeout(touchTimer);
   "
   class="group bg-muzibu-gray hover:bg-gray-700 rounded-lg px-4 pt-4 transition-all duration-300">

    <div class="relative mb-4">
        {{-- Sector Icon/Cover --}}
        @if($sector->media_id && $sector->iconMedia)
            <img src="{{ thumb($sector->iconMedia, 300, 300, ['scale' => 1]) }}"
                 alt="{{ $sector->title['tr'] ?? $sector->title['en'] ?? 'Sector' }}"
                 class="w-full aspect-square object-cover rounded-lg shadow-lg"
                 loading="lazy">
        @else
            <div class="w-full aspect-square bg-gradient-to-br from-pink-500 to-rose-600 rounded-lg flex items-center justify-center shadow-lg">
                <span class="text-5xl">🎭</span>
            </div>
        @endif

        {{-- Play Button - Spotify Style Bottom Right --}}
        <button x-on:click.stop.prevent="
            $store.player.setPlayContext({
                type: 'sector',
                id: {{ $sector->sector_id }},
                name: '{{ addslashes($sector->title['tr'] ?? $sector->title['en'] ?? 'Sector') }}'
            });
            playSector({{ $sector->sector_id }});
        " class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-xl hover:scale-110 hover:bg-green-500">
            <i class="fas fa-play ml-1"></i>
        </button>

        {{-- Favorite + Menu Buttons (Top-right, hover only) --}}
        <div class="absolute top-2 right-2 z-10 flex gap-2 opacity-0 group-hover:opacity-100 transition-all" x-on:click.stop.prevent>
            {{-- Favorite Button --}}
            <button x-on:click.stop.prevent="$store.favorites.toggle('sector', {{ $sector->sector_id }})"
                    class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all"
                    x-bind:class="$store.favorites.isFavorite('sector', {{ $sector->sector_id }}) ? 'text-muzibu-coral' : ''">
                <i class="text-sm"
                   x-bind:class="$store.favorites.isFavorite('sector', {{ $sector->sector_id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
            </button>

            {{-- 3-Dot Menu Button --}}
            <button x-on:click.stop.prevent="$store.contextMenu.openContextMenu($event, 'sector', {
                id: {{ $sector->sector_id }},
                title: '{{ addslashes($sector->title['tr'] ?? $sector->title['en'] ?? 'Sector') }}',
                is_favorite: {{ auth()->check() && method_exists($sector, 'isFavoritedBy') && $sector->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
            })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                <i class="fas fa-ellipsis-v text-sm"></i>
            </button>
        </div>
    </div>

    {{-- Text Area (Fixed Height - ALWAYS 48px / 3rem) --}}
    <div class="h-12 overflow-hidden pb-4">
        <h3 class="font-semibold text-white text-sm leading-6 line-clamp-1">
            {{ $sector->title['tr'] ?? $sector->title['en'] ?? 'Sector' }}
        </h3>
        <p class="text-xs text-gray-400 leading-6 line-clamp-1">
            @if(isset($sector->description) && !empty($sector->description))
                {{ is_array($sector->description) ? ($sector->description['tr'] ?? $sector->description['en'] ?? '&nbsp;') : $sector->description }}
            @else
                &nbsp;
            @endif
        </p>
    </div>
</a>
