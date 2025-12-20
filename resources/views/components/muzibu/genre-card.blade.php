@props(['genre', 'preview' => false])

{{-- Muzibu Genre Card Component --}}
{{-- Usage: <x-muzibu.genre-card :genre="$genre" /> --}}

@php
    $colors = ['bg-blue-600', 'bg-purple-600', 'bg-pink-600', 'bg-orange-600', 'bg-green-600', 'bg-red-600', 'bg-indigo-600', 'bg-teal-600'];
    $color = $colors[array_rand($colors)];
@endphp

<a @if($preview)
       href="javascript:void(0)"
       @click="$store.sidebar.showPreview('genre', {{ $genre->genre_id }}, {
           type: 'Genre',
           id: {{ $genre->genre_id }},
           title: '{{ addslashes($genre->title['tr'] ?? $genre->title['en'] ?? 'Genre') }}',
           slug: '{{ $genre->getTranslation('slug', app()->getLocale()) }}',
           cover: '{{ $genre->iconMedia ? thumb($genre->iconMedia, 300, 300, ['scale' => 1]) : '' }}',
           is_favorite: {{ auth()->check() && method_exists($genre, 'isFavoritedBy') && $genre->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
       })"
   @else
       href="/genres/{{ $genre->genre_id }}"
   @endif
   data-genre-id="{{ $genre->genre_id }}"
   data-context-type="genre"
   x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'genre', {
       id: {{ $genre->genre_id }},
       title: '{{ addslashes($genre->title['tr'] ?? $genre->title['en'] ?? 'Genre') }}',
       is_favorite: {{ auth()->check() && method_exists($genre, 'isFavoritedBy') && $genre->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
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
           }, 'genre', {
               id: {{ $genre->genre_id }},
               title: '{{ addslashes($genre->title['tr'] ?? $genre->title['en'] ?? 'Genre') }}',
               is_favorite: {{ auth()->check() && method_exists($genre, 'isFavoritedBy') && $genre->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
           });
       }, 500);
   "
   x-on:touchend="clearTimeout(touchTimer)"
   x-on:touchmove="
       const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                    Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
       if (moved) clearTimeout(touchTimer);
   "
   class="relative h-32 rounded-lg {{ $color }} overflow-hidden group hover:scale-105 transition-all shadow-lg">

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-all"></div>

    {{-- Favorite + Menu Buttons (Sağ Üst) - HOVER'DA GÖRÜNÜR --}}
    <div class="absolute top-2 right-2 z-20 flex gap-2 opacity-0 group-hover:opacity-100 transition-all" x-on:click.stop.prevent>
        {{-- Favorite Button --}}
        <button x-on:click.stop.prevent="$store.favorites.toggle('genre', {{ $genre->genre_id }})"
                class="w-8 h-8 bg-black/70 hover:bg-black/90 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all hover:scale-110"
                x-bind:class="$store.favorites.isFavorite('genre', {{ $genre->genre_id }}) ? 'text-muzibu-coral' : ''">
            <i class="text-sm"
               x-bind:class="$store.favorites.isFavorite('genre', {{ $genre->genre_id }}) ? 'fas fa-heart' : 'far fa-heart hover:text-muzibu-coral'"></i>
        </button>

        {{-- 3-Dot Menu Button --}}
        <button x-on:click.stop.prevent="$store.contextMenu.openContextMenu($event, 'genre', {
            id: {{ $genre->genre_id }},
            title: '{{ addslashes($genre->title['tr'] ?? $genre->title['en'] ?? 'Genre') }}',
            is_favorite: {{ auth()->check() && method_exists($genre, 'isFavoritedBy') && $genre->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
        })" class="w-8 h-8 bg-black/70 hover:bg-black/90 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all hover:scale-110">
            <i class="fas fa-ellipsis-v text-sm"></i>
        </button>
    </div>

    {{-- Content (Fixed Height Layout) --}}
    <div class="relative z-10 p-4 h-full flex flex-col">
        <div class="flex-1"></div>
        <div class="h-[60px] flex flex-col justify-start">
            <h3 class="text-white font-bold text-base mb-1 truncate line-clamp-1">
                {{ $genre->title['tr'] ?? $genre->title['en'] ?? 'Genre' }}
            </h3>
            <p class="text-white/80 text-sm truncate line-clamp-1">
                {{ $genre->song_count ?? 0 }} şarkı
            </p>
        </div>
    </div>
</a>
