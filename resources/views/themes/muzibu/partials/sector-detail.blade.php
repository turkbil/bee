{{-- Hero Section with Gradient Background --}}
<div class="relative mb-8">
    {{-- Dynamic Gradient Background --}}
    <div class="absolute inset-0 bg-gradient-to-b from-pink-900/40 via-transparent to-transparent h-96 -z-10"></div>

    <div class="px-4 sm:px-6 py-8 sm:py-12">
        {{-- Sector Header - Modern Hero Style --}}
        <div class="flex flex-col sm:flex-row items-center sm:items-end gap-6 sm:gap-8 mb-8">
            {{-- Sector Cover with Shadow --}}
            <div class="relative flex-shrink-0 group">
                @if($sector->getCoverUrl())
                    <img src="{{ $sector->getCoverUrl(300, 300) }}"
                         alt="{{ $sector->getTranslation('title', app()->getLocale()) }}"
                         class="w-48 h-48 sm:w-56 sm:h-56 md:w-64 md:h-64 object-cover rounded-xl shadow-2xl shadow-black/50">
                @else
                    <div class="w-48 h-48 sm:w-56 sm:h-56 md:w-64 md:h-64 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center text-5xl sm:text-6xl md:text-7xl shadow-2xl shadow-black/50">
                        🎭
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 w-full sm:min-w-0 text-center sm:text-left pb-0 sm:pb-4">
                <p class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider mb-3 sm:mb-4">Kategori</p>
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black text-white mb-4 sm:mb-6 leading-tight">
                    {{ $sector->getTranslation('title', app()->getLocale()) }}
                </h1>

                @if($sector->description)
                    <p class="text-sm sm:text-base md:text-lg text-gray-300 mb-4 sm:mb-6 line-clamp-2 opacity-90">
                        {{ $sector->getTranslation('description', app()->getLocale()) }}
                    </p>
                @endif

                <div class="flex items-center justify-center sm:justify-start gap-2 text-sm sm:text-base text-white">
                    <span class="font-bold">Muzibu</span>
                    <span class="text-gray-400">•</span>
                    <span class="font-semibold">{{ $playlists->count() }} playlist</span>
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
            @click="$dispatch('play-all-playlists', { sectorId: {{ $sector->sector_id }} })"
            class="w-14 h-14 sm:w-16 sm:h-16 bg-muzibu-coral hover:scale-105 active:scale-100 rounded-full flex items-center justify-center shadow-xl hover:shadow-2xl transition-all duration-200">
            <i class="fas fa-play text-white text-xl sm:text-2xl ml-1"></i>
        </button>

        <div x-on:click.stop>
            @auth
            <button
                x-data="{
                    favorited: {{ is_favorited('sector', $sector->id) ? 'true' : 'false' }},
                    count: {{ method_exists($sector, 'favoritesCount') ? $sector->favoritesCount() : 0 }},
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
                                model_class: '{{ addslashes(get_class($sector)) }}',
                                model_id: {{ $sector->id }}
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
                <span class="text-sm font-medium">{{ method_exists($sector, 'favoritesCount') ? $sector->favoritesCount() : 0 }} favori</span>
            </a>
            @endauth
        </div>

        <button class="text-gray-400 hover:text-white transition-colors" title="Daha fazla">
            <i class="fas fa-ellipsis-h text-2xl"></i>
        </button>
    </div>

    {{-- RADYOLAR BÖLÜMÜ (Üstte) --}}
    @if(isset($radios) && $radios->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <i class="fas fa-radio text-red-500"></i>
                Canlı Radyolar
                <span class="bg-red-600 text-white text-xs px-2 py-1 rounded-full animate-pulse">CANLI</span>
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                @foreach($radios as $radio)
                    <x-muzibu.radio-card :radio="$radio" />
                @endforeach
            </div>
        </div>
    @endif

    {{-- PLAYLİSTLER BÖLÜMÜ (Altta) --}}
    @if($playlists && $playlists->count() > 0)
        <div>
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <i class="fas fa-list text-muzibu-coral"></i>
                Playlistler
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                @foreach($playlists as $playlist)
                    <x-muzibu.playlist-card :playlist="$playlist" :preview="true" />
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-400">Bu kategoride henüz playlist yok</p>
        </div>
    @endif
</div>
