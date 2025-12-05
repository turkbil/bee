<div class="px-6 py-8">
    {{-- Header --}}
    <div class="mb-8 animate-slide-up">
        <h1 class="text-4xl font-bold text-white mb-2">Albümler</h1>
        <p class="text-gray-400">En yeni ve popüler albümler</p>
    </div>

    {{-- Albums Grid --}}
    @if($albums && $albums->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 animate-slide-up" style="animation-delay: 100ms">
            @foreach($albums as $album)
                <a href="/albums/{{ $album->getTranslation('slug', app()->getLocale()) }}"
                   @click.prevent="navigateTo('/albums/{{ $album->getTranslation('slug', app()->getLocale()) }}')"
                   class="group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-4 transition-all duration-300">
                    <div class="relative mb-4">
                        @if($album->getFirstMedia('album_cover'))
                            <img src="{{ thumb($album->getFirstMedia('album_cover'), 300, 300, ['scale' => 1]) }}"
                                 alt="{{ $album->getTranslation('title', app()->getLocale()) }}"
                                 class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                 loading="lazy">
                        @else
                            <div class="w-full aspect-square bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-4xl shadow-lg">
                                💿
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
                                    favorited: {{ auth()->check() && method_exists($album, 'isFavoritedBy') && $album->isFavoritedBy(auth()->id()) ? 'true' : 'false' }},
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
                                                model_class: '{{ addslashes(get_class($album)) }}',
                                                model_id: {{ $album->id }}
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
                        {{ $album->getTranslation('title', app()->getLocale()) }}
                    </h3>

                    @if($album->artist)
                        <p class="text-sm text-gray-400 truncate">
                            {{ $album->artist->getTranslation('title', app()->getLocale()) }}
                        </p>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($albums->hasPages())
            <div class="mt-8">
                {{ $albums->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-20">
            <div class="mb-6">
                <i class="fas fa-compact-disc text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Henüz albüm yok</h3>
            <p class="text-gray-400">Yakında yeni albümler eklenecek</p>
        </div>
    @endif
</div>
