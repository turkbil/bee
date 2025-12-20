@props(['sector', 'preview' => false])

{{-- Muzibu Sector Card Component --}}
{{-- Usage: <x-muzibu.sector-card :sector="$sector" /> --}}

<a @if($preview)
       href="javascript:void(0)"
       @click="$store.sidebar.showPreview('sector', {{ $sector->sector_id }}, {
           type: 'Sector',
           id: {{ $sector->sector_id }},
           title: '{{ addslashes($sector->title['tr'] ?? $sector->title['en'] ?? 'Sector') }}',
           is_favorite: {{ auth()->check() && method_exists($sector, 'isFavoritedBy') && $sector->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
       })"
   @else
       href="/sectors/{{ $sector->sector_id }}"
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
   class="group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-6 transition-all duration-300 relative">

    {{-- Favorite + Menu Buttons (Sağ Üst) - HOVER'DA GÖRÜNÜR --}}
    <div class="absolute top-2 right-2 z-10 flex gap-2 opacity-0 group-hover:opacity-100 transition-all" x-on:click.stop.prevent>
        {{-- Favorite Button --}}
        <button x-on:click.stop.prevent="$store.favorites.toggle('sector', {{ $sector->sector_id }})"
                class="w-8 h-8 bg-black/70 hover:bg-black/90 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all hover:scale-110"
                x-bind:class="$store.favorites.isFavorite('sector', {{ $sector->sector_id }}) ? 'text-muzibu-coral' : ''">
            <i class="text-sm"
               x-bind:class="$store.favorites.isFavorite('sector', {{ $sector->sector_id }}) ? 'fas fa-heart' : 'far fa-heart hover:text-muzibu-coral'"></i>
        </button>

        {{-- 3-Dot Menu Button --}}
        <button x-on:click.stop.prevent="$store.contextMenu.openContextMenu($event, 'sector', {
            id: {{ $sector->sector_id }},
            title: '{{ addslashes($sector->title['tr'] ?? $sector->title['en'] ?? 'Sector') }}',
            is_favorite: {{ auth()->check() && method_exists($sector, 'isFavoritedBy') && $sector->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
        })" class="w-8 h-8 bg-black/70 hover:bg-black/90 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all hover:scale-110">
            <i class="fas fa-ellipsis-v text-sm"></i>
        </button>
    </div>

    {{-- Icon Container (Square - same as album/playlist cards) --}}
    <div class="relative mb-4">
        <div class="w-full aspect-square bg-gradient-to-br from-muzibu-coral to-orange-600 rounded-lg flex items-center justify-center shadow-lg">
            <i class="fas fa-building text-white text-5xl sm:text-6xl opacity-90"></i>
        </div>
    </div>

    {{-- Text Area (Fixed Height - ALWAYS same height) --}}
    <div class="h-[60px] flex flex-col justify-start">
        <h3 class="text-white font-bold text-base mb-1 truncate line-clamp-1">
            {{ $sector->title['tr'] ?? $sector->title['en'] ?? 'Sector' }}
        </h3>
        <p class="text-gray-400 text-sm truncate line-clamp-1">
            @if(isset($sector->description) && !empty($sector->description))
                {{ is_array($sector->description) ? ($sector->description['tr'] ?? $sector->description['en'] ?? '') : $sector->description }}
            @else
                {{ $sector->playlist_count ?? 0 }} playlist
            @endif
        </p>
    </div>
</a>
