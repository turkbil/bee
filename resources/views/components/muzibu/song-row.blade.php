@props(['song', 'index' => 0, 'showAlbum' => false])

{{-- Muzibu Song Row Component --}}
{{-- Usage: <x-muzibu.song-row :song="$song" :index="$index" :show-album="true" /> --}}
{{-- Features: Album name, infinite queue data, playing indicator, favorite always visible for favorited songs --}}

<div class="group flex items-center gap-2 sm:gap-4 px-2 sm:px-4 py-2 sm:py-3 rounded-lg hover:bg-white/5 transition-all cursor-pointer"
     {{-- Infinite Queue Data Attributes --}}
     data-song-id="{{ $song->id }}"
     data-album-id="{{ $song->album_id ?? '' }}"
     data-genre-id="{{ $song->genre_id ?? ($song->album->genre_id ?? '') }}"
     data-context-type="song"
     {{-- Playing State (JS will add this class when playing) --}}
     x-bind:class="$store.player.currentSong?.id === {{ $song->id }} ? 'bg-muzibu-coral/10 border-l-4 border-muzibu-coral' : ''"
     x-on:click="$store.player.playSong({{ $song->id }}, {{ $song->album_id ?? 'null' }}, {{ $song->genre_id ?? ($song->album->genre_id ?? 'null') }})">

    {{-- Index / Playing Indicator --}}
    <span class="w-6 sm:w-8 text-center text-xs sm:text-sm flex-shrink-0">
        {{-- Playing: Animated Waveform Icon --}}
        <span x-show="$store.player.currentSong?.id === {{ $song->id }} && $store.player.isPlaying"
              x-cloak
              class="text-muzibu-coral">
            <i class="fas fa-waveform-lines animate-pulse"></i>
        </span>
        {{-- Paused: Play Icon --}}
        <span x-show="$store.player.currentSong?.id === {{ $song->id }} && !$store.player.isPlaying"
              x-cloak
              class="text-muzibu-coral">
            <i class="fas fa-play"></i>
        </span>
        {{-- Not Playing: Index Number --}}
        <span x-show="$store.player.currentSong?.id !== {{ $song->id }}"
              class="text-gray-400">
            {{ $index + 1 }}
        </span>
    </span>

    {{-- Album Cover (Optional - if showAlbum is true) --}}
    @if($showAlbum && $song->album && $song->album->coverMedia)
        <img src="{{ thumb($song->album->coverMedia, 48, 48) }}"
             alt="{{ $song->album->getTranslation('title', app()->getLocale()) }}"
             class="w-10 h-10 sm:w-12 sm:h-12 rounded shadow-md flex-shrink-0"
             loading="lazy">
    @endif

    {{-- Song Info --}}
    <div class="flex-1 min-w-0">
        {{-- Song Name --}}
        <h3 class="text-white font-medium text-sm sm:text-base truncate"
            x-bind:class="$store.player.currentSong?.id === {{ $song->id }} ? 'text-muzibu-coral font-bold' : ''">
            {{ $song->getTranslation('title', app()->getLocale()) }}
        </h3>

        {{-- Album Name (NEW - Always show if song has album) --}}
        @if($song->album)
            <p class="text-xs sm:text-sm text-gray-400 truncate mt-0.5">
                {{ $song->album->getTranslation('title', app()->getLocale()) }}
            </p>
        @endif
    </div>

    {{-- Duration --}}
    <span class="text-xs sm:text-sm text-gray-400 hidden sm:block flex-shrink-0">
        {{ gmdate('i:s', $song->duration ?? 0) }}
    </span>

    {{-- Actions (Hover for unfavorited, Always visible for favorited) --}}
    <div class="flex items-center gap-2 flex-shrink-0 transition-opacity"
         x-bind:class="$store.player.currentSong?.id === {{ $song->id }} ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'"
         @click.stop.prevent>

        {{-- Favorite Button --}}
        @php
            $isFavorited = auth()->check() && method_exists($song, 'isFavoritedBy') && $song->isFavoritedBy(auth()->id());
        @endphp

        <button class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center text-gray-400 hover:text-muzibu-coral transition-colors hover:scale-110 {{ $isFavorited ? '!opacity-100' : '' }}"
                x-on:click.stop.prevent="$store.favorites.toggle('song', {{ $song->id }})"
                x-bind:class="$store.favorites.isFavorite('song', {{ $song->id }}) ? 'text-muzibu-coral' : ''">
            <i class="text-sm sm:text-base"
               x-bind:class="$store.favorites.isFavorite('song', {{ $song->id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
        </button>

        {{-- 3-Dot Menu Button --}}
        <x-muzibu.song-actions-menu :song="$song" />
    </div>
</div>
