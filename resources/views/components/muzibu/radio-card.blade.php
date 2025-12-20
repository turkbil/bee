@props(['radio'])

{{-- Muzibu Radio Card Component --}}
{{-- Usage: <x-muzibu.radio-card :radio="$radio" /> --}}
{{-- Note: Radios play directly, no preview mode --}}

<div class="radio-card group bg-muzibu-gray hover:bg-gray-700 rounded-xl sm:rounded-2xl p-4 sm:p-6 transition-all duration-300 cursor-pointer hover:shadow-2xl hover:shadow-muzibu-coral/20"
     data-radio-id="{{ $radio->radio_id }}"
     data-genre-id="{{ $radio->genre_id ?? '' }}"
     data-context-type="radio"
     data-radio-title="{{ $radio->getTranslation('title', app()->getLocale()) }}"
     data-is-favorite="{{ auth()->check() && $radio->isFavoritedBy(auth()->user()) ? '1' : '0' }}"
     x-data="{
         touchTimer: null,
         touchStartPos: { x: 0, y: 0 }
     }"
     x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'radio', {
         id: {{ $radio->radio_id }},
         title: '{{ addslashes($radio->getTranslation('title', app()->getLocale())) }}',
         is_favorite: {{ auth()->check() && $radio->isFavoritedBy(auth()->user()) ? 'true' : 'false' }}
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
                 is_favorite: {{ auth()->check() && $radio->isFavoritedBy(auth()->user()) ? 'true' : 'false' }}
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
    <div class="relative mb-4 sm:mb-6">
        @if($radio->media_id && $radio->logoMedia)
            <div class="w-full aspect-square bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl sm:rounded-2xl flex items-center justify-center p-4 sm:p-6 shadow-lg overflow-hidden">
                <img src="{{ thumb($radio->logoMedia, 300, 300, ['scale' => 1]) }}"
                     alt="{{ $radio->getTranslation('title', app()->getLocale()) }}"
                     class="w-full h-full object-contain"
                     loading="lazy">
            </div>
        @else
            <div class="w-full aspect-square bg-gradient-to-br from-red-500 via-pink-500 to-purple-600 rounded-xl sm:rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-radio text-white text-5xl sm:text-6xl opacity-90"></i>
            </div>
        @endif

        {{-- Infinite Badge (Always Visible) --}}
        <span class="absolute top-2 left-2 z-10 bg-gradient-to-r from-blue-500 to-purple-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
            ♾️ Sonsuz
        </span>

        {{-- Playing Badge (JS controlled) --}}
        <span class="absolute top-2 right-2 z-10 bg-gradient-to-r from-muzibu-coral to-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg animate-pulse hidden"
              x-show="$store.player.currentContext?.type === 'radio' && $store.player.currentContext?.id === {{ $radio->radio_id }}"
              x-transition>
            📻 Çalıyor
        </span>

        {{-- Large Play Button Overlay --}}
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 rounded-xl sm:rounded-2xl flex items-center justify-center">
            <button
                @click.stop.prevent="$store.player.playRadio({{ $radio->radio_id }})"
                class="opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-110 transition-all duration-300 bg-muzibu-coral hover:bg-opacity-90 text-white rounded-full w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center shadow-2xl hover:scale-125 hover:shadow-muzibu-coral/50"
            >
                <i class="fas fa-play text-2xl sm:text-3xl ml-1"></i>
            </button>
        </div>

        {{-- Favorite + Menu Buttons (Cover Sağ Üst) - HOVER'DA GÖRÜNÜR --}}
        <div class="absolute top-2 right-2 z-20 flex gap-2 opacity-0 group-hover:opacity-100 transition-all" @click.stop.prevent>
            {{-- Favorite Button --}}
            <button @click.stop.prevent="$store.favorites.toggle('radio', {{ $radio->radio_id }})"
                    class="w-8 h-8 bg-black/70 hover:bg-black/90 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all hover:scale-110"
                    x-bind:class="$store.favorites.isFavorite('radio', {{ $radio->radio_id }}) ? 'text-muzibu-coral' : ''">
                <i class="text-sm"
                   x-bind:class="$store.favorites.isFavorite('radio', {{ $radio->radio_id }}) ? 'fas fa-heart' : 'far fa-heart hover:text-muzibu-coral'"></i>
            </button>

            {{-- 3-Dot Menu Button --}}
            <button @click.stop.prevent="$store.contextMenu.openContextMenu($event, 'radio', {
                    id: {{ $radio->radio_id }},
                    title: '{{ addslashes($radio->getTranslation('title', app()->getLocale())) }}',
                    is_favorite: {{ auth()->check() && $radio->isFavoritedBy(auth()->user()) ? 'true' : 'false' }}
                })"
                    class="w-8 h-8 bg-black/70 hover:bg-black/90 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all hover:scale-110">
                <i class="fas fa-ellipsis-v text-sm"></i>
            </button>
        </div>
    </div>

    {{-- Radio Title --}}
    <h3 class="text-lg sm:text-xl font-bold text-white mb-1 truncate text-center">
        {{ $radio->getTranslation('title', app()->getLocale()) }}
    </h3>

    {{-- Radio Description --}}
    @if($radio->description)
        <p class="text-xs sm:text-sm text-gray-400 truncate text-center">
            {{ $radio->getTranslation('description', app()->getLocale()) }}
        </p>
    @endif
</div>
