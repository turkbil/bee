@props([
    'playlist',
    'preview' => false, // Desktop: sidebar preview, Mobile: detail page
    'size' => 'normal' // normal, small, large
])

@php
    $coverUrl = $playlist->getCoverUrl(300, 300) ?? '/images/default-playlist.png';
    $songsCount = $playlist->songs_count ?? $playlist->songs()->count();
    $playlistUrl = '/playlists/' . ($playlist->slug ?? $playlist->playlist_id);
    $playlistId = $playlist->playlist_id ?? $playlist->id;
    $isOwner = auth()->check() && $playlist->user_id === auth()->id();
    $isFavorite = is_favorited('playlist', $playlist->playlist_id);

    $sizeClasses = [
        'small' => 'w-32 sm:w-36',
        'normal' => 'w-40 sm:w-44',
        'large' => 'w-48 sm:w-56'
    ];
    $cardSize = $sizeClasses[$size] ?? $sizeClasses['normal'];
@endphp

<div class="group flex-shrink-0 {{ $cardSize }} snap-start"
     x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'playlist', {
         id: {{ $playlistId }},
         title: '{{ addslashes($playlist->title) }}',
         slug: '{{ $playlist->slug ?? '' }}',
         cover_url: '{{ $coverUrl }}',
         is_owner: {{ $isOwner ? 'true' : 'false' }},
         is_mine: {{ $isOwner ? 'true' : 'false' }},
         is_favorite: {{ $isFavorite ? 'true' : 'false' }}
     })"
     x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
     x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'playlist', { id: {{ $playlistId }}, title: '{{ addslashes($playlist->title) }}', slug: '{{ $playlist->slug ?? '' }}', cover_url: '{{ $coverUrl }}', is_owner: {{ $isOwner ? 'true' : 'false' }}, is_mine: {{ $isOwner ? 'true' : 'false' }}, is_favorite: {{ $isFavorite ? 'true' : 'false' }} }); }, 500);"
     x-on:touchend="clearTimeout(touchTimer)"
     x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer)">

    {{-- Link with Preview/Mobile behavior --}}
    <a href="{{ $playlistUrl }}"
       @if($preview)
       @click="if (window.innerWidth >= 768) {
           $event.preventDefault();
           $store.sidebar.showPreview('playlist', {{ $playlistId }}, {
               type: 'Playlist',
               id: {{ $playlistId }},
               title: '{{ addslashes($playlist->title) }}',
               slug: '{{ $playlist->slug ?? '' }}',
               cover: '{{ $coverUrl }}',
               is_favorite: {{ $isFavorite ? 'true' : 'false' }}
           });
       }"
       @mouseenter="$store.sidebar.prefetch('playlist', {{ $playlistId }})"
       @endif
       class="block" data-spa>

        {{-- Cover --}}
        <div class="relative aspect-square rounded-xl overflow-hidden mb-3 bg-white/5">
            <img src="{{ $coverUrl }}" alt="{{ $playlist->title }}"
                 class="w-full h-full object-cover transition group-hover:scale-105"
                 loading="lazy">

            {{-- Play Button Overlay --}}
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                <button @click.prevent.stop="playPlaylist({{ $playlistId }})"
                        class="w-12 h-12 bg-green-500 hover:bg-green-400 rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition">
                    <i class="fas fa-play text-black text-lg ml-0.5"></i>
                </button>
            </div>

            {{-- Songs Count Badge --}}
            @if($songsCount > 0)
                <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/70 rounded text-xs text-white">
                    {{ $songsCount }} {{ __('muzibu::front.general.songs') }}
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="px-1">
            <h3 class="text-white font-medium text-sm truncate group-hover:text-green-400 transition">
                {{ $playlist->title }}
            </h3>
            @if($playlist->description && !$preview)
                <p class="text-gray-400 text-xs mt-1 line-clamp-2">{{ $playlist->description }}</p>
            @endif
        </div>
    </a>
</div>
