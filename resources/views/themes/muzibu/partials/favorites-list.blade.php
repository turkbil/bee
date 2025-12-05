<div class="px-4 sm:px-6 py-6 sm:py-8">
    {{-- Header --}}
    <div class="mb-8 sm:mb-10 animate-slide-up">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-2 sm:mb-3">Favorilerim</h1>
        <p class="text-sm sm:text-base text-gray-400">Beğendiğin şarkılar, albümler ve playlistler</p>
    </div>

    {{-- Modern Filter Tabs --}}
    <div class="mb-8 sm:mb-10">
        <nav class="flex gap-2 sm:gap-4 overflow-x-auto scrollbar-hide pb-2" x-data="{ activeTab: '{{ $type }}' }">
            <a href="/favorites?type=all" wire:navigate
               
               class="flex-shrink-0 px-4 sm:px-6 py-2 sm:py-3 rounded-full font-semibold text-sm sm:text-base transition-all duration-200"
               :class="activeTab === 'all' ? 'bg-muzibu-coral text-white shadow-lg shadow-muzibu-coral/30' : 'bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white'">
                <i class="fas fa-heart mr-2"></i>
                Tümü
            </a>
            <a href="/favorites?type=songs" wire:navigate
               
               class="flex-shrink-0 px-4 sm:px-6 py-2 sm:py-3 rounded-full font-semibold text-sm sm:text-base transition-all duration-200"
               :class="activeTab === 'songs' ? 'bg-muzibu-coral text-white shadow-lg shadow-muzibu-coral/30' : 'bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white'">
                <i class="fas fa-music mr-2"></i>
                Şarkılar
            </a>
            <a href="/favorites?type=albums" wire:navigate
               
               class="flex-shrink-0 px-4 sm:px-6 py-2 sm:py-3 rounded-full font-semibold text-sm sm:text-base transition-all duration-200"
               :class="activeTab === 'albums' ? 'bg-muzibu-coral text-white shadow-lg shadow-muzibu-coral/30' : 'bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white'">
                <i class="fas fa-compact-disc mr-2"></i>
                Albümler
            </a>
            <a href="/favorites?type=playlists" wire:navigate
               
               class="flex-shrink-0 px-4 sm:px-6 py-2 sm:py-3 rounded-full font-semibold text-sm sm:text-base transition-all duration-200"
               :class="activeTab === 'playlists' ? 'bg-muzibu-coral text-white shadow-lg shadow-muzibu-coral/30' : 'bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white'">
                <i class="fas fa-list-music mr-2"></i>
                Playlistler
            </a>
        </nav>
    </div>

    @if($favorites->count() > 0)
        {{-- Favorites Grid - Modern Layout --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-6 mb-8 animate-slide-up" style="animation-delay: 100ms">
            @foreach($favorites as $favorite)
                @php
                    $item = $favorite->favoritable;
                @endphp

                @if($item)
                    <div class="group bg-muzibu-gray hover:bg-gray-700 rounded-xl p-4 transition-all duration-300 cursor-pointer hover:shadow-2xl hover:shadow-muzibu-coral/20">
                        @if($item instanceof \Modules\Muzibu\App\Models\Song)
                            <!-- Song Card -->
                            <div @click="$dispatch('play-song', { songId: {{ $item->song_id }} })">
                                <div class="relative mb-4">
                                    @if($item->album && $item->album->getFirstMedia('album_cover'))
                                        <img src="{{ thumb($item->album->getFirstMedia('album_cover'), 300, 300, ['scale' => 1]) }}"
                                             alt="{{ $item->getTranslation('title', app()->getLocale()) }}"
                                             class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                             loading="lazy">
                                    @else
                                        <div class="w-full aspect-square bg-gradient-to-br from-muzibu-coral to-purple-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-music text-white text-4xl opacity-50"></i>
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                                        <button class="opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:scale-110">
                                            <i class="fas fa-play ml-1"></i>
                                        </button>
                                    </div>

                                    <div class="absolute top-2 right-2" x-on:click.stop>
                                        @auth
                                        <button
                                            x-data="{
                                                favorited: {{ auth()->check() && method_exists($item, 'isFavoritedBy') && $item->isFavoritedBy(auth()->id()) ? 'true' : 'false' }},
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
                                                            model_class: '{{ addslashes(get_class($item)) }}',
                                                            model_id: {{ $item->id }}
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
                                        <a href="{{ route('login') }}" wire:navigate class="w-8 h-8 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center hover:scale-110 transition-transform">
                                            <i class="far fa-heart text-white text-sm"></i>
                                        </a>
                                        @endauth
                                    </div>
                                </div>

                                <h3 class="font-semibold text-white mb-1 truncate">
                                    {{ $item->getTranslation('title', app()->getLocale()) }}
                                </h3>

                                @if($item->album && $item->album->artist)
                                    <p class="text-sm text-gray-400 truncate">
                                        {{ $item->album->artist->getTranslation('title', app()->getLocale()) }}
                                    </p>
                                @endif
                            </div>

                        @elseif($item instanceof \Modules\Muzibu\App\Models\Album)
                            <!-- Album Card -->
                            <a href="/albums/{{ $item->getTranslation('slug', app()->getLocale()) }}" wire:navigate
                               >
                                <div class="relative mb-4">
                                    @if($item->getFirstMedia('album_cover'))
                                        <img src="{{ thumb($item->getFirstMedia('album_cover'), 300, 300, ['scale' => 1]) }}"
                                             alt="{{ $item->getTranslation('title', app()->getLocale()) }}"
                                             class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                             loading="lazy">
                                    @else
                                        <div class="w-full aspect-square bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-compact-disc text-white text-4xl opacity-50"></i>
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                                        <button class="opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:scale-110">
                                            <i class="fas fa-play ml-1"></i>
                                        </button>
                                    </div>

                                    <div class="absolute top-2 right-2" x-on:click.stop>
                                        @auth
                                        <button
                                            x-data="{
                                                favorited: {{ auth()->check() && method_exists($item, 'isFavoritedBy') && $item->isFavoritedBy(auth()->id()) ? 'true' : 'false' }},
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
                                                            model_class: '{{ addslashes(get_class($item)) }}',
                                                            model_id: {{ $item->id }}
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
                                        <a href="{{ route('login') }}" wire:navigate class="w-8 h-8 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center hover:scale-110 transition-transform">
                                            <i class="far fa-heart text-white text-sm"></i>
                                        </a>
                                        @endauth
                                    </div>
                                </div>

                                <h3 class="font-semibold text-white mb-1 truncate">
                                    {{ $item->getTranslation('title', app()->getLocale()) }}
                                </h3>

                                @if($item->artist)
                                    <p class="text-sm text-gray-400 truncate">
                                        {{ $item->artist->getTranslation('title', app()->getLocale()) }}
                                    </p>
                                @endif
                            </a>

                        @elseif($item instanceof \Modules\Muzibu\App\Models\Playlist)
                            <!-- Playlist Card -->
                            <a href="/playlists/{{ $item->getTranslation('slug', app()->getLocale()) }}" wire:navigate
                               >
                                <div class="relative mb-4">
                                    @if($item->getFirstMedia('cover'))
                                        <img src="{{ thumb($item->getFirstMedia('cover'), 300, 300, ['scale' => 1]) }}"
                                             alt="{{ $item->getTranslation('title', app()->getLocale()) }}"
                                             class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                             loading="lazy">
                                    @else
                                        <div class="w-full aspect-square bg-gradient-to-br from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-list-music text-white text-4xl opacity-50"></i>
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                                        <button class="opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:scale-110">
                                            <i class="fas fa-play ml-1"></i>
                                        </button>
                                    </div>

                                    <div class="absolute top-2 right-2" x-on:click.stop>
                                        @auth
                                        <button
                                            x-data="{
                                                favorited: {{ auth()->check() && method_exists($item, 'isFavoritedBy') && $item->isFavoritedBy(auth()->id()) ? 'true' : 'false' }},
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
                                                            model_class: '{{ addslashes(get_class($item)) }}',
                                                            model_id: {{ $item->id }}
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
                                        <a href="{{ route('login') }}" wire:navigate class="w-8 h-8 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center hover:scale-110 transition-transform">
                                            <i class="far fa-heart text-white text-sm"></i>
                                        </a>
                                        @endauth
                                    </div>
                                </div>

                                <h3 class="font-semibold text-white mb-1 truncate">
                                    {{ $item->getTranslation('title', app()->getLocale()) }}
                                </h3>

                                @if($item->description)
                                    <p class="text-sm text-gray-400 truncate">
                                        {{ $item->getTranslation('description', app()->getLocale()) }}
                                    </p>
                                @endif
                            </a>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Pagination -->
        @if($favorites->hasPages())
            <div class="mt-8">
                {{ $favorites->links() }}
            </div>
        @endif

    @else
        {{-- Modern Empty State --}}
        <div class="text-center py-16 sm:py-24">
            <div class="mb-6 sm:mb-8 animate-bounce">
                <i class="fas fa-heart text-gray-600 text-6xl sm:text-7xl md:text-8xl"></i>
            </div>
            <h3 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4">
                @if($type === 'all')
                    Henüz favori eklemedin
                @elseif($type === 'songs')
                    Henüz favori şarkın yok
                @elseif($type === 'albums')
                    Henüz favori albümün yok
                @else
                    Henüz favori playlistin yok
                @endif
            </h3>
            <p class="text-sm sm:text-base md:text-lg text-gray-400 mb-6 sm:mb-8 max-w-md mx-auto">
                Beğendiğin içerikleri favorilere ekleyerek kolayca ulaşabilirsin
            </p>
            <a href="/" wire:navigate
               
               class="inline-flex items-center px-6 sm:px-8 py-3 sm:py-4 bg-muzibu-coral text-white font-bold rounded-full hover:bg-opacity-90 hover:scale-105 transition-all duration-200 shadow-xl hover:shadow-2xl text-sm sm:text-base">
                <i class="fas fa-home mr-2"></i>
                Ana Sayfaya Dön
            </a>
        </div>
    @endif
</div>
