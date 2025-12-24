@props([
    'song',
    'showPlay' => true
])

@php
    $songId = $song->song_id ?? $song->id;
@endphp

<div x-data="{ open: false }" class="relative" @click.outside="open = false">
    <button @click.stop="open = !open"
            class="p-2 text-gray-400 hover:text-white transition rounded-full hover:bg-white/10">
        <i class="fas fa-ellipsis-h"></i>
    </button>

    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-48 bg-slate-800 border border-white/10 rounded-xl shadow-xl z-50 py-2">

        @if($showPlay)
            <button @click="playSong({{ $songId }}); open = false"
                    class="w-full px-4 py-2 text-left text-sm text-white hover:bg-white/10 transition flex items-center gap-3">
                <i class="fas fa-play text-green-400 w-4"></i>
                {{ __('muzibu::front.player.play') }}
            </button>
        @endif

        <button @click="addToQueue({{ $songId }}); open = false"
                class="w-full px-4 py-2 text-left text-sm text-white hover:bg-white/10 transition flex items-center gap-3">
            <i class="fas fa-list text-blue-400 w-4"></i>
            {{ __('muzibu::front.player.queue') }}
        </button>

        <button @click="toggleFavorite({{ $songId }}); open = false"
                class="w-full px-4 py-2 text-left text-sm text-white hover:bg-white/10 transition flex items-center gap-3">
            <i class="far fa-heart text-red-400 w-4"></i>
            {{ __('muzibu::front.player.add_to_favorites') }}
        </button>

        <button @click="$dispatch('open-playlist-modal', { songId: {{ $songId }} }); open = false"
                class="w-full px-4 py-2 text-left text-sm text-white hover:bg-white/10 transition flex items-center gap-3">
            <i class="fas fa-plus text-purple-400 w-4"></i>
            {{ __('muzibu::front.player.add_to_playlist') }}
        </button>

        <div class="border-t border-white/10 my-2"></div>

        <button @click="shareSong({{ $songId }}); open = false"
                class="w-full px-4 py-2 text-left text-sm text-white hover:bg-white/10 transition flex items-center gap-3">
            <i class="fas fa-share-alt text-gray-400 w-4"></i>
            {{ __('muzibu::front.player.share') }}
        </button>
    </div>
</div>
