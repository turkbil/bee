<div class="px-4 sm:px-6 py-6 sm:py-8">
    {{-- Header --}}
    <div class="mb-8 sm:mb-10 animate-slide-up">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-2 sm:mb-3">Playlistlerim</h1>
        <p class="text-sm sm:text-base text-gray-400">Oluşturduğun müzik koleksiyonları</p>
    </div>

    {{-- Playlists Grid - Modern Layout --}}
    @if($playlists && $playlists->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-6 animate-slide-up" style="animation-delay: 100ms">
            @foreach($playlists as $playlist)
                <a href="/playlists/{{ $playlist->getTranslation('slug', app()->getLocale()) }}"
                   @click.prevent="$store.router.navigateTo('/playlists/{{ $playlist->getTranslation('slug', app()->getLocale()) }}')"
                   class="group bg-muzibu-gray hover:bg-gray-700 rounded-xl p-4 transition-all duration-300 hover:shadow-2xl hover:shadow-muzibu-coral/20">
                    <div class="relative mb-4">
                        @if($playlist->getFirstMedia('cover'))
                            <img src="{{ thumb($playlist->getFirstMedia('cover'), 300, 300, ['scale' => 1]) }}"
                                 alt="{{ $playlist->getTranslation('title', app()->getLocale()) }}"
                                 class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                 loading="lazy">
                        @else
                            <div class="w-full aspect-square bg-gradient-to-br from-muzibu-coral to-purple-600 rounded-lg flex items-center justify-center text-4xl shadow-lg">
                                🎵
                            </div>
                        @endif

                        {{-- Play Button Overlay --}}
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                            <button class="opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:scale-110">
                                <i class="fas fa-play ml-1"></i>
                            </button>
                        </div>

                        {{-- Favorite Button --}}
                        <div class="absolute top-2 right-2" x-on:click.stop>
                            @auth
                            <button
                                x-data="{
                                    favorited: {{ auth()->check() && method_exists($playlist, 'isFavoritedBy') && $playlist->isFavoritedBy(auth()->id()) ? 'true' : 'false' }},
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
                                            }
                                        })
                                        .catch(error => console.error('Favorite error:', error))
                                        .finally(() => this.loading = false);
                                    }
                                }"
                                x-on:click="toggle()"
                                class="w-8 h-8 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center hover:scale-110 transition-transform"
                            >
                                <i x-bind:class="favorited ? 'fas fa-heart text-red-500' : 'far fa-heart text-white'" class="text-sm"></i>
                            </button>
                            @else
                            <a href="{{ route('login') }}" class="w-8 h-8 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center hover:scale-110 transition-transform">
                                <i class="far fa-heart text-white text-sm"></i>
                            </a>
                            @endauth
                        </div>
                    </div>

                    <h3 class="font-semibold text-white mb-1 truncate">
                        {{ $playlist->getTranslation('title', app()->getLocale()) }}
                    </h3>

                    @if($playlist->description)
                        <p class="text-sm text-gray-400 truncate">
                            {{ $playlist->getTranslation('description', app()->getLocale()) }}
                        </p>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($playlists->hasPages())
            <div class="mt-8">
                {{ $playlists->links() }}
            </div>
        @endif
    @else
        {{-- Modern Empty State --}}
        <div class="text-center py-16 sm:py-24">
            <div class="mb-6 sm:mb-8 animate-pulse">
                <i class="fas fa-list-music text-gray-600 text-6xl sm:text-7xl md:text-8xl"></i>
            </div>
            <h3 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4">
                Henüz playlist oluşturmadın
            </h3>
            <p class="text-sm sm:text-base md:text-lg text-gray-400 mb-6 sm:mb-8 max-w-md mx-auto">
                Kendi müzik koleksiyonlarını oluşturmaya başla ve sevdiğin şarkıları bir araya getir
            </p>
            <button
               @click="$dispatch('open-create-playlist-modal')"
               class="inline-flex items-center px-6 sm:px-8 py-3 sm:py-4 bg-muzibu-coral text-white font-bold rounded-full hover:bg-opacity-90 hover:scale-105 transition-all duration-200 shadow-xl hover:shadow-2xl text-sm sm:text-base">
                <i class="fas fa-plus mr-2"></i>
                Playlist Oluştur
            </button>
        </div>
    @endif
</div>
