{{-- 🎯 Sidebar Data - Track list for right sidebar --}}
<script>
document.addEventListener('alpine:init', () => {
    setTimeout(() => {
        if (window.Alpine && window.Alpine.store('sidebar')) {
            window.Alpine.store('sidebar').setContent(
                'genre',
                @json($songs->map(function($song) {
                    return [
                        'id' => $song->song_id,
                        'title' => $song->getTranslation('title', app()->getLocale()),
                        'artist' => $song->artist ? $song->artist->getTranslation('title', app()->getLocale()) : '',
                        'duration' => gmdate('i:s', $song->duration ?? 0)
                    ];
                })),
                {
                    type: 'Tür',
                    title: @json($genre->getTranslation('title', app()->getLocale())),
                    cover: @json($genre->media_id && $genre->iconMedia ? thumb($genre->iconMedia, 100, 100, ['scale' => 1]) : null),
                    id: {{ $genre->genre_id }}
                }
            );
        }
    }, 100);
});

if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').setContent(
        'genre',
        @json($songs->map(function($song) {
            return [
                'id' => $song->song_id,
                'title' => $song->getTranslation('title', app()->getLocale()),
                'artist' => $song->artist ? $song->artist->getTranslation('title', app()->getLocale()) : '',
                'duration' => gmdate('i:s', $song->duration ?? 0)
            ];
        })),
        {
            type: 'Tür',
            title: @json($genre->getTranslation('title', app()->getLocale())),
            cover: @json($genre->getFirstMedia('cover') ? thumb($genre->getFirstMedia('cover'), 100, 100, ['scale' => 1]) : null),
            id: {{ $genre->genre_id }}
        }
    );
}
</script>

{{-- Hero Section with Gradient Background --}}
<div class="relative mb-8">
    {{-- Dynamic Gradient Background --}}
    <div class="absolute inset-0 bg-gradient-to-b from-green-900/40 via-transparent to-transparent h-96 -z-10"></div>

    <div class="px-4 sm:px-6 py-8 sm:py-12">
        {{-- Genre Header - Modern Hero Style --}}
        <div class="flex flex-col sm:flex-row items-center sm:items-end gap-6 sm:gap-8 mb-8">
            {{-- Genre Cover with Shadow --}}
            <div class="relative flex-shrink-0 group">
                @if($genre->media_id && $genre->iconMedia)
                    <img src="{{ thumb($genre->iconMedia, 300, 300, ['scale' => 1]) }}"
                         alt="{{ $genre->getTranslation('title', app()->getLocale()) }}"
                         class="w-48 h-48 sm:w-56 sm:h-56 md:w-64 md:h-64 object-cover rounded-xl shadow-2xl shadow-black/50">
                @else
                    <div class="w-48 h-48 sm:w-56 sm:h-56 md:w-64 md:h-64 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl flex items-center justify-center text-5xl sm:text-6xl md:text-7xl shadow-2xl shadow-black/50">
                        🎸
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 w-full sm:min-w-0 text-center sm:text-left pb-0 sm:pb-4">
                <p class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider mb-3 sm:mb-4">Tür</p>
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black text-white mb-4 sm:mb-6 leading-tight">
                    {{ $genre->getTranslation('title', app()->getLocale()) }}
                </h1>

                @if($genre->description)
                    <p class="text-sm sm:text-base md:text-lg text-gray-300 mb-4 sm:mb-6 line-clamp-2 opacity-90">
                        {{ $genre->getTranslation('description', app()->getLocale()) }}
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
            @click="$dispatch('play-all-songs', { genreId: {{ $genre->genre_id }} })"
            class="w-14 h-14 sm:w-16 sm:h-16 bg-muzibu-coral hover:scale-105 active:scale-100 rounded-full flex items-center justify-center shadow-xl hover:shadow-2xl transition-all duration-200">
            <i class="fas fa-play text-white text-xl sm:text-2xl ml-1"></i>
        </button>

        <div x-on:click.stop>
            @auth
            <button
                x-data="{
                    favorited: {{ is_favorited('genre', $genre->id) ? 'true' : 'false' }},
                    count: {{ method_exists($genre, 'favoritesCount') ? $genre->favoritesCount() : 0 }},
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
                                model_class: '{{ addslashes(get_class($genre)) }}',
                                model_id: {{ $genre->id }}
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
            <a href="{{ route('login') }}" class="flex items-center gap-2 text-gray-400 hover:text-white cursor-pointer">
                <i class="far fa-heart text-2xl"></i>
                <span class="text-sm font-medium">{{ method_exists($genre, 'favoritesCount') ? $genre->favoritesCount() : 0 }} favori</span>
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
            <div>Sanatçı</div>
            <div class="text-right">Süre</div>
            <div></div>
        </div>

        <div class="space-y-0">
            @foreach($songs as $index => $song)
                <x-muzibu.song-detail-row :song="$song" :index="$index" :show-album="true" />
            @endforeach
        </div>
    @else
        <div class="text-center py-16 sm:py-20">
            <div class="mb-6">
                <i class="fas fa-music text-gray-600 text-5xl sm:text-6xl"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">Bu türde henüz şarkı yok</h3>
            <p class="text-sm sm:text-base text-gray-400">Bu türe ait şarkılar eklendiğinde burada görünecek</p>
        </div>
    @endif
</div>
