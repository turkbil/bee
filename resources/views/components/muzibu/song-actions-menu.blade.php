@props(['song'])

{{-- 3-dot menu for song actions --}}
<div x-data="{ open: false }" @click.outside="open = false" class="relative">
    <button @click="open = !open" class="text-muzibu-text-gray hover:text-white transition-colors p-2">
        <i class="fas fa-ellipsis-h"></i>
    </button>

    {{-- Dropdown Menu --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.stop
        class="absolute right-0 mt-2 w-56 bg-zinc-800 rounded-lg shadow-xl border border-white/10 py-2 z-50"
    >
        {{-- Add to Queue --}}
        <button @click="addToQueue({{ $song->song_id }}); open = false" class="w-full px-4 py-2.5 text-left hover:bg-white/10 transition-colors flex items-center gap-3 text-white">
            <i class="fas fa-plus-circle w-5"></i>
            <span>Sıraya Ekle</span>
        </button>

        {{-- Add to Favorites --}}
        <button @click="toggleFavorite({{ $song->song_id }}, 'song'); open = false" class="w-full px-4 py-2.5 text-left hover:bg-white/10 transition-colors flex items-center gap-3 text-white">
            <i class="far fa-heart w-5"></i>
            <span>Favorilere Ekle</span>
        </button>

        <div class="border-t border-white/10 my-2"></div>

        {{-- Go to Album --}}
        @if($song->album)
        <a href="{{ route('muzibu.album.show', $song->album->getTranslation('slug', app()->getLocale())) }}" class="w-full px-4 py-2.5 text-left hover:bg-white/10 transition-colors flex items-center gap-3 text-white">
            <i class="fas fa-compact-disc w-5"></i>
            <span>Albüme Git</span>
        </a>
        @endif

        {{-- Go to Artist --}}
        @if($song->album && $song->album->artist)
        <a href="{{ route('muzibu.artist.show', $song->album->artist->getTranslation('slug', app()->getLocale())) }}" class="w-full px-4 py-2.5 text-left hover:bg-white/10 transition-colors flex items-center gap-3 text-white">
            <i class="fas fa-user-music w-5"></i>
            <span>Sanatçıya Git</span>
        </a>
        @endif
    </div>
</div>
