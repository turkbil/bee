{{-- 🎯 Sidebar Data - Track list for right sidebar --}}
<script>
document.addEventListener('alpine:init', () => {
    // Wait for store to be ready
    setTimeout(() => {
        if (window.Alpine && window.Alpine.store('sidebar')) {
            window.Alpine.store('sidebar').setContent(
                'playlist',
                @json($songs->map(function($song) {
                    return [
                        'id' => $song->song_id,
                        'title' => $song->getTranslation('title', app()->getLocale()),
                        'artist' => $song->artist ? $song->artist->getTranslation('title', app()->getLocale()) : '',
                        'duration' => gmdate('i:s', $song->duration ?? 0)
                    ];
                })),
                {
                    type: 'Playlist',
                    title: @json($playlist->getTranslation('title', app()->getLocale())),
                    cover: @json($playlist->media_id && $playlist->coverMedia ? thumb($playlist->coverMedia, 100, 100, ['scale' => 1]) : null),
                    id: {{ $playlist->playlist_id }}
                }
            );
        }
    }, 100);
});

// Also set immediately if Alpine is already initialized
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').setContent(
        'playlist',
        @json($songs->map(function($song) {
            return [
                'id' => $song->song_id,
                'title' => $song->getTranslation('title', app()->getLocale()),
                'artist' => $song->artist ? $song->artist->getTranslation('title', app()->getLocale()) : '',
                'duration' => gmdate('i:s', $song->duration ?? 0)
            ];
        })),
        {
            type: 'Playlist',
            title: @json($playlist->getTranslation('title', app()->getLocale())),
            cover: @json($playlist->getFirstMedia('cover') ? thumb($playlist->getFirstMedia('cover'), 100, 100, ['scale' => 1]) : null),
            id: {{ $playlist->playlist_id }}
        }
    );
}
</script>

{{-- Hero Section with Gradient Background --}}
<div class="relative mb-8">
    {{-- Dynamic Gradient Background --}}
    <div class="absolute inset-0 bg-gradient-to-b from-purple-900/40 via-transparent to-transparent h-96 -z-10"></div>

    <div class="px-4 sm:px-6 py-8 sm:py-12">
        {{-- Playlist Header - Modern Hero Style --}}
        <div class="flex flex-col sm:flex-row items-center sm:items-end gap-6 sm:gap-8 mb-8 animate-slide-up">
            {{-- Cover with Shadow --}}
            <div class="relative flex-shrink-0 group">
                @if($playlist->media_id && $playlist->coverMedia)
                    <img src="{{ thumb($playlist->coverMedia, 300, 300, ['scale' => 1]) }}"
                         alt="{{ $playlist->getTranslation('title', app()->getLocale()) }}"
                         class="w-48 h-48 sm:w-56 sm:h-56 md:w-64 md:h-64 object-cover rounded-xl shadow-2xl shadow-black/50">
                @else
                    <div class="w-48 h-48 sm:w-56 sm:h-56 md:w-64 md:h-64 bg-gradient-to-br from-muzibu-coral to-purple-600 rounded-xl flex items-center justify-center text-5xl sm:text-6xl md:text-7xl shadow-2xl shadow-black/50">
                        🎵
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 w-full sm:min-w-0 text-center sm:text-left pb-0 sm:pb-4">
                <p class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider mb-3 sm:mb-4">Playlist</p>
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black text-white mb-4 sm:mb-6 leading-tight">
                    {{ $playlist->getTranslation('title', app()->getLocale()) }}
                </h1>

                @if($playlist->description)
                    <p class="text-sm sm:text-base md:text-lg text-gray-300 mb-4 sm:mb-6 line-clamp-2 opacity-90">
                        {{ $playlist->getTranslation('description', app()->getLocale()) }}
                    </p>
                @endif

                <div class="flex items-center justify-center sm:justify-start gap-2 text-sm sm:text-base text-white">
                    <span class="font-bold">Muzibu</span>
                    <span class="text-gray-400">•</span>
                    <span class="font-semibold">{{ $songs->count() }} şarkı</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Content Section --}}
<div class="px-4 sm:px-6">
    {{-- Actions - Larger Buttons --}}
    <div class="flex items-center gap-6 mb-8 sm:mb-10">
        <button
            @click="$dispatch('play-all-songs', { playlistId: {{ $playlist->playlist_id }} })"
            class="w-14 h-14 sm:w-16 sm:h-16 bg-muzibu-coral hover:scale-105 active:scale-100 rounded-full flex items-center justify-center shadow-xl hover:shadow-2xl transition-all duration-200">
            <i class="fas fa-play text-white text-xl sm:text-2xl ml-1"></i>
        </button>

        <div x-on:click.stop>
            @auth
            <button
                x-data="{
                    favorited: {{ auth()->check() && method_exists($playlist, 'isFavoritedBy') && $playlist->isFavoritedBy(auth()->id()) ? 'true' : 'false' }},
                    count: {{ method_exists($playlist, 'favoritesCount') ? $playlist->favoritesCount() : 0 }},
                    loading: false,
                    toggle() {
                        if (this.loading) return;
                        this.loading = true;
                        fetch('/api/favorites/toggle', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                model_class: '{{ addslashes(get_class($playlist)) }}',
                                model_id: {{ $playlist->id }}
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.favorited = data.data.is_favorited;
                                this.count = data.data.favorites_count;
                            }
                        })
                        .catch(error => console.error('Favorite error:', error))
                        .finally(() => this.loading = false);
                    }
                }"
                x-on:click="toggle()"
                class="flex items-center gap-2 cursor-pointer hover:scale-110 transition-transform duration-200"
            >
                <i x-bind:class="favorited ? 'fas fa-heart text-red-500' : 'far fa-heart text-gray-400'" class="text-2xl transition-colors"></i>
                <span class="text-sm font-medium text-gray-400" x-text="count + ' favori'"></span>
            </button>
            @else
            <a href="{{ route('login') }}" wire:navigate class="flex items-center gap-2 text-gray-400 hover:text-white cursor-pointer">
                <i class="far fa-heart text-2xl"></i>
                <span class="text-sm font-medium">{{ method_exists($playlist, 'favoritesCount') ? $playlist->favoritesCount() : 0 }} favori</span>
            </a>
            @endauth
        </div>

        <button class="text-gray-400 hover:text-white transition-colors" title="Daha fazla">
            <i class="fas fa-ellipsis-h text-2xl"></i>
        </button>
    </div>

    {{-- Songs List - Modern Table Style --}}
    @if($songs && $songs->count() > 0)
        {{-- Table Header - Desktop Only --}}
        <div class="hidden md:grid grid-cols-[40px_50px_6fr_4fr_100px_60px] gap-4 px-4 py-2 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-white/5">
            <div class="text-center">#</div>
            <div></div>
            <div>Başlık</div>
            <div>Albüm</div>
            <div class="text-right">Süre</div>
            <div></div>
        </div>

        <div class="space-y-0">
            @foreach($songs as $index => $song)
                <div
                    x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', {
                        id: {{ $song->song_id }},
                        title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
                        artist: '{{ $song->artist ? addslashes($song->artist->getTranslation('title', app()->getLocale())) : '' }}',
                        album_id: {{ $song->album ? $song->album->id : 'null' }},
                        is_favorite: {{ auth()->check() && method_exists($song, 'isFavoritedBy') && $song->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
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
                            }, 'song', {
                                id: {{ $song->song_id }},
                                title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
                                artist: '{{ $song->artist ? addslashes($song->artist->getTranslation('title', app()->getLocale())) : '' }}',
                                album_id: {{ $song->album ? $song->album->id : 'null' }},
                                is_favorite: {{ auth()->check() && method_exists($song, 'isFavoritedBy') && $song->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
                            });
                        }, 500);
                    "
                    x-on:touchend="clearTimeout(touchTimer)"
                    x-on:touchmove="
                        const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                                     Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
                        if (moved) clearTimeout(touchTimer);
                    "
                    class="group grid grid-cols-[40px_50px_1fr_60px] md:grid-cols-[40px_50px_6fr_4fr_100px_60px] gap-2 sm:gap-4 px-2 sm:px-4 py-2 sm:py-3 rounded-lg hover:bg-white/10 transition-all cursor-pointer items-center"
                    @click="$dispatch('play-song', { songId: {{ $song->song_id }} })">
                    {{-- Number/Play Icon --}}
                    <div class="text-center flex-shrink-0">
                        <span class="text-gray-400 text-xs sm:text-sm group-hover:hidden">{{ $index + 1 }}</span>
                        <i class="fas fa-play text-sm hidden group-hover:inline-block text-white"></i>
                    </div>

                    {{-- Song Cover Image --}}
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded overflow-hidden shadow-lg flex-shrink-0">
                        <x-muzibu.lazy-image
                            :src="$song->getCoverUrl(80, 80)"
                            :alt="$song->getTranslation('title', app()->getLocale())"
                            wrapper-class="w-full h-full"
                            class="w-full h-full object-cover"
                        />
                    </div>

                    {{-- Title & Artist --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-white font-medium text-sm sm:text-base truncate group-hover:text-muzibu-coral transition-colors">
                            {{ $song->getTranslation('title', app()->getLocale()) }}
                        </h3>
                        @if($song->artist)
                            <p class="text-xs sm:text-sm text-gray-400 truncate">
                                {{ $song->artist->getTranslation('title', app()->getLocale()) }}
                            </p>
                        @endif
                    </div>

                    {{-- Album - Desktop Only --}}
                    <div class="hidden md:block truncate" @click.stop>
                        @if($song->album)
                            <a href="/albums/{{ $song->album->getTranslation('slug', app()->getLocale()) }}" wire:navigate
                               
                               class="text-sm text-gray-400 hover:text-white hover:underline transition-colors">
                                {{ $song->album->getTranslation('title', app()->getLocale()) }}
                            </a>
                        @endif
                    </div>

                    {{-- Duration - Desktop Only --}}
                    <div class="hidden md:block text-right text-sm text-gray-400 flex-shrink-0">
                        {{ gmdate('i:s', $song->duration ?? 0) }}
                    </div>

                    {{-- Actions Menu --}}
                    <div @click.stop class="flex-shrink-0 text-right">
                        <x-muzibu.song-actions-menu :song="$song" />
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 sm:py-20">
            <div class="mb-6">
                <i class="fas fa-music text-gray-600 text-5xl sm:text-6xl"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">Bu playlist'te henüz şarkı yok</h3>
            <p class="text-sm sm:text-base text-gray-400">Şarkı ekleyerek playlist'ini zenginleştir</p>
        </div>
    @endif
</div>
