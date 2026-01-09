@props(['genre', 'index' => 0])

{{--
╔═══════════════════════════════════════════════════════════════════════════╗
║ MUZIBU COMPONENT: Genre Quick Card                                        ║
╠═══════════════════════════════════════════════════════════════════════════╣
║ Açıklama: Ana sayfa Quick Access için yatay tür kartı                     ║
║           Spotify tarzı 2 satırlık horizontal scroll grid için tasarlandı ║
║                                                                            ║
║ Props:                                                                     ║
║   - genre: Model - Genre modeli (zorunlu)                                 ║
║   - index: Integer - Sıra numarası (varsayılan: 0)                        ║
║                                                                            ║
║ Kullanım:                                                                  ║
║   <x-muzibu.genre-quick-card :genre="$genre" :index="0" />               ║
║                                                                            ║
║ Özellikler:                                                                ║
║   ✓ Horizontal layout (64x64 cover + title)                              ║
║   ✓ Sidebar preview integration                                           ║
║   ✓ Context menu (right-click + long-press)                               ║
║   ✓ Touch feedback (500ms vibration)                                      ║
║                                                                            ║
║ Dependencies:                                                              ║
║   - Alpine.js: $store.sidebar, $store.contextMenu                         ║
║   - Helpers: thumb(), addslashes()                                         ║
╚═══════════════════════════════════════════════════════════════════════════╝
--}}

@php
    $genreId = $genre->id ?? $genre->genre_id;
    $genreTitle = $genre->getTranslation('title', app()->getLocale());
    $genreSlug = $genre->getTranslation('slug', app()->getLocale());
    // iconMedia() veya getFirstMediaUrl('hero') kullan
    $coverUrl = $genre->iconMedia ? thumb($genre->iconMedia, 64, 64, ['scale' => 1]) : ($genre->getFirstMediaUrl('hero') ? thumb($genre->getFirstMediaUrl('hero'), 64, 64, ['scale' => 1]) : null);
    $coverUrlLarge = $genre->iconMedia ? thumb($genre->iconMedia, 300, 300, ['scale' => 1]) : ($genre->getFirstMediaUrl('hero') ?: '');
@endphp

<a href="/genres/{{ $genreSlug }}"
   data-spa
   class="genre-card group flex items-center gap-3 bg-white/5 hover:bg-white/10 rounded transition-all cursor-pointer overflow-hidden h-16 relative"
     data-genre-id="{{ $genreId }}"
     data-context-type="genre"
     x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
     x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'genre', {
         id: {{ $genreId }},
         title: '{{ addslashes($genreTitle) }}'
     })"
     x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'genre', { id: {{ $genreId }}, title: '{{ addslashes($genreTitle) }}' }); }, 500);"
     x-on:touchend="clearTimeout(touchTimer)"
     x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer);">

    {{-- Cover Image --}}
    <div class="w-16 h-16 flex-shrink-0 bg-gradient-to-br from-muzibu-coral to-orange-600">
        @if($coverUrl)
            <img src="{{ $coverUrl }}"
                 alt="{{ $genreTitle }}"
                 loading="{{ $index < 6 ? 'eager' : 'lazy' }}"
                 class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-xl text-white/90">
                <i class="fas fa-music"></i>
            </div>
        @endif
    </div>

    {{-- Title --}}
    <div class="flex-1 min-w-0 pr-4">
        <h3 class="font-semibold text-white text-sm truncate">
            {{ $genreTitle }}
        </h3>
    </div>

    {{-- Play Button - HOVER'DA GÖRÜNÜR --}}
    <div class="absolute right-2 top-1/2 -translate-y-1/2 z-10 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
        <button @click.stop="$dispatch('play-genre', { genreId: {{ $genreId }} })"
                class="w-8 h-8 bg-muzibu-coral hover:bg-green-500 rounded-full flex items-center justify-center text-white shadow-lg hover:scale-110 transition-all">
            <i class="fas fa-play text-xs ml-0.5"></i>
        </button>
    </div>
</a>
