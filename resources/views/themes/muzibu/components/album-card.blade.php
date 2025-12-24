@props([
    'album',
    'showArtist' => true,
    'size' => 'normal' // normal, small, large
])

@php
    $cover = $album->coverMedia ?? null;
    $coverUrl = $cover ? thumb($cover, 300, 300) : '/images/default-album.png';
    $artistName = $album->artist->title ?? __('muzibu::front.dashboard.unknown_artist');
    $albumUrl = '/albums/' . ($album->slug ?? $album->album_id);
    $songsCount = $album->songs_count ?? $album->songs()->where('is_active', 1)->count();

    $sizeClasses = [
        'small' => 'w-32 sm:w-36',
        'normal' => 'w-40 sm:w-44',
        'large' => 'w-48 sm:w-56'
    ];
    $cardSize = $sizeClasses[$size] ?? $sizeClasses['normal'];
@endphp

<div class="group flex-shrink-0 {{ $cardSize }} snap-start">
    <a href="{{ $albumUrl }}" class="block" data-spa>
        {{-- Cover --}}
        <div class="relative aspect-square rounded-xl overflow-hidden mb-3 bg-white/5 shadow-lg">
            <img src="{{ $coverUrl }}" alt="{{ $album->title }}"
                 class="w-full h-full object-cover transition group-hover:scale-105"
                 loading="lazy">

            {{-- Play Button Overlay --}}
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                <button @click.prevent="playAlbum({{ $album->album_id ?? $album->id }})"
                        class="w-12 h-12 bg-green-500 hover:bg-green-400 rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition">
                    <i class="fas fa-play text-black text-lg ml-0.5"></i>
                </button>
            </div>

            {{-- Year Badge --}}
            @if($album->release_year)
                <div class="absolute top-2 left-2 px-2 py-1 bg-black/70 rounded text-xs text-white">
                    {{ $album->release_year }}
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="px-1">
            <h3 class="text-white font-medium text-sm truncate group-hover:text-green-400 transition">
                {{ $album->title }}
            </h3>
            @if($showArtist)
                <p class="text-gray-400 text-xs mt-1 truncate">{{ $artistName }}</p>
            @endif
        </div>
    </a>
</div>
