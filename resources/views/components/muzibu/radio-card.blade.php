@props(['radio', 'preview' => false, 'compact' => false])

{{-- Muzibu Radio Card Component --}}
{{-- Usage: <x-muzibu.radio-card :radio="$radio" :compact="true" /> --}}
{{-- Note: Radios play directly - preview parameter is accepted but not used (for consistency) --}}

<div class="radio-card group rounded-lg transition-all duration-300 cursor-pointer @if($compact) flex-shrink-0 w-[190px] p-3 bg-transparent hover:bg-white/10 @else bg-muzibu-gray hover:bg-gray-700 px-4 pt-4 @endif"
     @click.stop.prevent="window.playContent('radio', {{ $radio->radio_id }})"
     data-radio-id="{{ $radio->radio_id }}"
     data-genre-id="{{ $radio->genre_id ?? '' }}"
     data-radio-title="{{ $radio->getTranslation('title', app()->getLocale()) }}"
     data-is-favorite="{{ is_favorited('radio', $radio->radio_id) ? '1' : '0' }}"
     x-data="{
         touchTimer: null,
         touchStartPos: { x: 0, y: 0 }
     }"
     x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'radio', {
         id: {{ $radio->radio_id }},
         title: '{{ addslashes($radio->getTranslation('title', app()->getLocale())) }}',
         is_favorite: {{ is_favorited('radio', $radio->radio_id) ? 'true' : 'false' }}
     })"
     x-on:touchstart="
         touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY };
         touchTimer = setTimeout(() => {
             if (navigator.vibrate) navigator.vibrate(50);
             $store.contextMenu.openContextMenu({
                 clientX: $event.touches[0].clientX,
                 clientY: $event.touches[0].clientY
             }, 'radio', {
                 id: {{ $radio->radio_id }},
                 title: '{{ addslashes($radio->getTranslation('title', app()->getLocale())) }}',
                 is_favorite: {{ is_favorited('radio', $radio->radio_id) ? 'true' : 'false' }}
             });
         }, 500);
     "
     x-on:touchend="clearTimeout(touchTimer)"
     x-on:touchmove="
         const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                      Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
         if (moved) clearTimeout(touchTimer);
     "
     x-bind:class="$store.player.currentContext?.type === 'radio' && $store.player.currentContext?.id === {{ $radio->radio_id }} ? 'ring-2 ring-muzibu-coral animate-pulse' : ''">

    {{-- Radio Logo/Icon --}}
    <div class="relative @if($compact) mb-3 @else mb-4 @endif">
        @if($radio->media_id && $radio->logoMedia)
            <img src="{{ thumb($radio->logoMedia, 300, 300, ['scale' => 1]) }}"
                 alt="{{ $radio->getTranslation('title', app()->getLocale()) }}"
                 class="w-full aspect-square object-cover rounded-lg shadow-lg"
                 loading="lazy">
        @else
            <div class="w-full aspect-square bg-gradient-to-br from-muzibu-coral to-orange-600 rounded-lg flex items-center justify-center shadow-lg">
                <i class="fas fa-radio text-white text-5xl opacity-90"></i>
            </div>
        @endif

        {{-- Playing Badge (JS controlled) --}}
        <span class="absolute top-2 left-2 z-10 bg-gradient-to-r from-muzibu-coral to-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg hidden"
              x-show="$store.player.currentContext?.type === 'radio' && $store.player.currentContext?.id === {{ $radio->radio_id }}"
              x-transition>
            📻 Çalıyor
        </span>

        {{-- Large Play Button Overlay (Center) - Radios have no detail page --}}
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 rounded-lg flex items-center justify-center">
            <button x-on:click.stop.prevent="window.playContent('radio', {{ $radio->radio_id }})"
                    class="opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300 bg-muzibu-coral hover:bg-opacity-90 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-2xl hover:scale-110">
                <i class="fas fa-play text-2xl ml-1"></i>
            </button>
        </div>

        {{-- Favorite + Menu Buttons (Sağ Üst) - HOVER'DA GÖRÜNÜR --}}
        <div class="absolute top-2 right-2 z-20 flex gap-2 opacity-0 group-hover:opacity-100 transition-all" x-on:click.stop.prevent>
            {{-- Favorite Button --}}
            <button @click.stop.prevent="$store.favorites.toggle('radio', {{ $radio->radio_id }})"
                    class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all"
                    x-bind:class="$store.favorites.isFavorite('radio', {{ $radio->radio_id }}) ? 'text-muzibu-coral' : ''">
                <i class="text-sm"
                   x-bind:class="$store.favorites.isFavorite('radio', {{ $radio->radio_id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
            </button>

            {{-- 3-Dot Menu Button --}}
            <button @click.stop.prevent="$store.contextMenu.openContextMenu($event, 'radio', {
                    id: {{ $radio->radio_id }},
                    title: '{{ addslashes($radio->getTranslation('title', app()->getLocale())) }}',
                    is_favorite: {{ is_favorited('radio', $radio->radio_id) ? 'true' : 'false' }}
                })"
                    class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                <i class="fas fa-ellipsis-v text-sm"></i>
            </button>
        </div>
    </div>

    {{-- Text Area (Fixed Height - Always 2 rows) --}}
    <div class="h-12 overflow-hidden pb-4">
        <h3 class="text-sm font-semibold text-white leading-6 line-clamp-1">
            {{ $radio->getTranslation('title', app()->getLocale()) }}
        </h3>
        <p class="text-xs text-gray-400 leading-6 line-clamp-1">
            @if($radio->description)
                {{ $radio->getTranslation('description', app()->getLocale()) }}
            @else
                &nbsp;
            @endif
        </p>
    </div>
</div>
