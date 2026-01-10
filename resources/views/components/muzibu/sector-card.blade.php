@props(['sector', 'preview' => false, 'compact' => false, 'index' => 0])

{{-- Muzibu Sector Card Component --}}
{{-- Usage: <x-muzibu.sector-card :sector="$sector" :compact="true" /> --}}
{{-- STANDARD PATTERN: Same layout as playlist/album/genre cards --}}
{{-- Preview: Desktop (≥1024px) = sidebar preview, Mobile (<1024px) = detail page --}}

<a href="/sectors/{{ $sector->getTranslation('slug', app()->getLocale()) }}"
   @if($preview)
   @click="if (window.innerWidth >= 768) {
       $event.preventDefault();
       $store.sidebar.showPreview('sector', {{ $sector->sector_id }}, {
           type: 'Sector',
           id: {{ $sector->sector_id }},
           title: '{{ addslashes($sector->getTranslation('title', app()->getLocale())) }}',
           slug: '{{ $sector->getTranslation('slug', app()->getLocale()) }}',
           cover: '{{ $sector->iconMedia ? thumb($sector->iconMedia, 300, 300, ['scale' => 1]) : '' }}',
           is_favorite: {{ is_favorited('sector', $sector->sector_id) ? 'true' : 'false' }}
       });
   }"
   @mouseenter="$store.sidebar.prefetch('sector', {{ $sector->sector_id }})"
   @endif
   data-sector-id="{{ $sector->sector_id }}"
   data-context-type="sector"
   class="group rounded-lg transition-all duration-300 relative overflow-hidden border-2 border-muzibu-gray @if($compact) flex-shrink-0 w-[190px] p-3 bg-transparent hover:bg-white/10 @else bg-muzibu-gray hover:bg-spotify-black px-4 pt-4 @endif">

    {{-- Hover Shimmer/Buz Efekti --}}
    <div class="absolute inset-0 overflow-hidden rounded-lg pointer-events-none">
        <div class="absolute -inset-full bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 -translate-x-full group-hover:animate-shimmer-sweep"></div>
    </div>

    <div class="relative @if($compact) mb-3 @else mb-4 @endif">
        {{-- Sector Icon/Cover --}}
        @php $heroMedia = $sector->getFirstMedia('hero'); @endphp
        @if($heroMedia)
            <img src="{{ thumb($heroMedia, 300, 300, ['scale' => 1]) }}"
                 alt="{{ $sector->getTranslation('title', app()->getLocale()) }}"
                 class="w-full aspect-square object-cover rounded-lg shadow-lg"
                 loading="{{ $index < 4 ? 'eager' : 'lazy' }}">
        @else
            <div class="w-full aspect-square bg-gradient-to-br from-muzibu-coral to-orange-600 rounded-lg flex items-center justify-center shadow-lg">
                <span class="text-5xl">🎭</span>
            </div>
        @endif

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
                title: '{{ addslashes($sector->getTranslation('title', app()->getLocale())) }}',
                cover_url: '{{ $sector->iconMedia ? thumb($sector->iconMedia, 300, 300, ['scale' => 1]) : '' }}',
                is_favorite: {{ is_favorited('sector', $sector->sector_id) ? 'true' : 'false' }}
            })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                <i class="fas fa-ellipsis-v text-sm"></i>
            </button>
        </div>
    </div>

    {{-- Text Area (Fixed Height - ALWAYS 48px / 3rem) --}}
    <div class="h-12 overflow-hidden pb-4">
        <h3 class="font-semibold text-white text-sm leading-6 line-clamp-1">
            {{ $sector->getTranslation('title', app()->getLocale()) }}
        </h3>
        <p class="text-xs text-gray-400 leading-6 line-clamp-1">
            @if(isset($sector->description) && !empty($sector->description))
                {{ $sector->getTranslation('description', app()->getLocale()) ?? '&nbsp;' }}
            @else
                &nbsp;
            @endif
        </p>
    </div>
</a>
