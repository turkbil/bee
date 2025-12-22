@props([
    'song',
    'index' => 0
])

{{-- SONG LIST ITEM - Unified Component for Blade Loops --}}
{{-- Usage: <x-muzibu.song-list-item :song="$song" :index="$index" /> --}}

<div class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer group transition-all"
     @click="$dispatch('play-song', { songId: {{ $song->song_id }} })">

    {{-- Track Thumbnail with Play Overlay --}}
    @php $coverUrl = $song->getCoverUrl(40, 40); @endphp
    <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-gradient-to-br from-muzibu-coral to-orange-600 relative">
        @if($coverUrl)
            <img src="{{ $coverUrl }}" alt="{{ $song->getTranslation('title', app()->getLocale()) }}" class="w-full h-full object-cover" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-music text-gray-600 text-xs"></i>
            </div>
        @endif
        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
            <i class="fas fa-play text-white text-xs"></i>
        </div>
    </div>

    {{-- Track Info --}}
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-white truncate group-hover:text-muzibu-coral transition-colors">
            {{ $song->getTranslation('title', app()->getLocale()) }}
        </p>
        <p class="text-xs text-gray-500 truncate">
            {{ $song->artist ? $song->artist->getTranslation('title', app()->getLocale()) : '' }}
        </p>
    </div>

    {{-- Duration (hide on hover) --}}
    <div class="text-xs text-gray-600 flex-shrink-0 group-hover:hidden">
        {{ $song->duration ? gmdate('i:s', $song->duration) : '' }}
    </div>

    {{-- Actions (show on hover) --}}
    <div class="hidden group-hover:flex items-center gap-1 flex-shrink-0">
        <button @click.stop="$dispatch('toggle-favorite', { type: 'song', id: {{ $song->song_id }} })"
                class="w-6 h-6 flex items-center justify-center rounded-full hover:bg-white/10 text-gray-400 hover:text-muzibu-coral transition-colors">
            <i class="far fa-heart text-xs"></i>
        </button>
        <button @click.stop="Alpine.store('contextMenu').openContextMenu($event, 'song', { id: {{ $song->song_id }}, title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}' })"
                class="w-6 h-6 flex items-center justify-center rounded-full text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
            <i class="fas fa-ellipsis-v text-xs"></i>
        </button>
    </div>
</div>
