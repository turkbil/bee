<div class="px-6 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Favorilerim</h1>
        <p class="text-gray-400">Beğendiğin şarkılar, albümler ve playlistler</p>
    </div>

    {{-- Modern Filter Tabs --}}
    <div class="mb-8">
        <nav class="flex gap-4 overflow-x-auto scrollbar-hide pb-2" x-data="{ activeTab: '{{ $type }}' }">
            <a href="/muzibu/favorites?type=all"
               class="flex-shrink-0 px-6 py-3 rounded-full font-semibold text-base transition-all duration-200"
               :class="activeTab === 'all' ? 'bg-muzibu-coral text-white shadow-lg shadow-muzibu-coral/30' : 'bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white'">
                <i class="fas fa-heart mr-2"></i>
                Tümü
            </a>
            <a href="/muzibu/favorites?type=songs"
               class="flex-shrink-0 px-6 py-3 rounded-full font-semibold text-base transition-all duration-200"
               :class="activeTab === 'songs' ? 'bg-muzibu-coral text-white shadow-lg shadow-muzibu-coral/30' : 'bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white'">
                <i class="fas fa-music mr-2"></i>
                Şarkılar
            </a>
            <a href="/muzibu/favorites?type=albums"
               class="flex-shrink-0 px-6 py-3 rounded-full font-semibold text-base transition-all duration-200"
               :class="activeTab === 'albums' ? 'bg-muzibu-coral text-white shadow-lg shadow-muzibu-coral/30' : 'bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white'">
                <i class="fas fa-record-vinyl mr-2"></i>
                Albümler
            </a>
            <a href="/muzibu/favorites?type=playlists"
               class="flex-shrink-0 px-6 py-3 rounded-full font-semibold text-base transition-all duration-200"
               :class="activeTab === 'playlists' ? 'bg-muzibu-coral text-white shadow-lg shadow-muzibu-coral/30' : 'bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white'">
                <i class="fas fa-list-music mr-2"></i>
                Playlistler
            </a>
        </nav>
    </div>

    @if($favorites->count() > 0)
        {{-- Favorites Grid - Modern Layout --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7 gap-4 mb-8">
            @foreach($favorites as $favorite)
                @php
                    $item = $favorite->favoritable;
                @endphp

                @if($item)
                    @if($item instanceof \Modules\Muzibu\App\Models\Song)
                        {{-- Song Card --}}
                        <div class="group relative">
                            <a href="{{ $item->getUrl() }}"
                              
                               class="block p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-white/10">
                                <div class="relative mb-3">
                                    <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl">
                                        @if($item->album && $item->album->getCoverUrl())
                                            <img src="{{ $item->album->getCoverUrl(200, 200) }}"
                                                 alt="{{ $item->getTranslation('title', app()->getLocale()) }}"
                                                 class="w-full h-full object-cover"
                                                 loading="lazy">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-muzibu-coral to-purple-600 flex items-center justify-center text-4xl">
                                                🎵
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <h3 class="font-semibold text-white truncate mb-1 text-sm">
                                    {{ $item->getTranslation('title', app()->getLocale()) }}
                                </h3>
                                @if($item->album && $item->album->artist)
                                    <p class="text-xs text-muzibu-text-gray truncate">
                                        {{ $item->album->artist->getTranslation('title', app()->getLocale()) }}
                                    </p>
                                @endif
                            </a>
                            {{-- Play button OUTSIDE <a> tag --}}
                            <button class="absolute w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-20 pointer-events-auto"
                                    style="bottom: calc(3rem + 0.75rem + 0.5rem); right: calc(0.75rem + 0.5rem);"
                                    @click="playSong({{ $item->song_id }})">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>

                    @elseif($item instanceof \Modules\Muzibu\App\Models\Album)
                        {{-- Album Card --}}
                        <div class="group relative">
                            <a href="/albums/{{ $item->getTranslation('slug', app()->getLocale()) }}"
                              
                               class="block p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-white/10">
                                <div class="relative mb-3">
                                    <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl">
                                        @if($item->getCoverUrl())
                                            <img src="{{ $item->getCoverUrl(200, 200) }}"
                                                 alt="{{ $item->getTranslation('title', app()->getLocale()) }}"
                                                 class="w-full h-full object-cover"
                                                 loading="lazy">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-4xl">
                                                💿
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <h3 class="font-semibold text-white truncate mb-1 text-sm">
                                    {{ $item->getTranslation('title', app()->getLocale()) }}
                                </h3>
                                @if($item->artist)
                                    <p class="text-xs text-muzibu-text-gray truncate">
                                        {{ $item->artist->getTranslation('title', app()->getLocale()) }}
                                    </p>
                                @endif
                            </a>
                            {{-- Play button OUTSIDE <a> tag --}}
                            <button class="absolute w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-20 pointer-events-auto"
                                    style="bottom: calc(3rem + 0.75rem + 0.5rem); right: calc(0.75rem + 0.5rem);"
                                    @click="playAlbum({{ $item->album_id }})">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>

                    @elseif($item instanceof \Modules\Muzibu\App\Models\Playlist)
                        {{-- Playlist Card --}}
                        <div class="group relative">
                            <a href="/playlists/{{ $item->getTranslation('slug', app()->getLocale()) }}"
                              
                               class="block p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-white/10">
                                <div class="relative mb-3">
                                    <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl">
                                        @if($item->getCoverUrl())
                                            <img src="{{ $item->getCoverUrl(200, 200) }}"
                                                 alt="{{ $item->getTranslation('title', app()->getLocale()) }}"
                                                 class="w-full h-full object-cover"
                                                 loading="lazy">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-green-500 to-blue-600 flex items-center justify-center text-4xl">
                                                🎵
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <h3 class="font-semibold text-white truncate mb-1 text-sm">
                                    {{ $item->getTranslation('title', app()->getLocale()) }}
                                </h3>
                                @if($item->description)
                                    <p class="text-xs text-muzibu-text-gray truncate">
                                        {{ Str::limit($item->getTranslation('description', app()->getLocale()), 40) }}
                                    </p>
                                @endif
                            </a>
                            {{-- Play button OUTSIDE <a> tag --}}
                            <button class="absolute w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-20 pointer-events-auto"
                                    style="bottom: calc(3rem + 0.75rem + 0.5rem); right: calc(0.75rem + 0.5rem);"
                                    @click="playPlaylist({{ $item->playlist_id }})">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>
                    @endif
                @endif
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($favorites->hasPages())
            <div class="mt-8">
                {{ $favorites->links() }}
            </div>
        @endif

    @else
        {{-- Empty State --}}
        <div class="text-center py-16">
            <div class="mb-6">
                <i class="fas fa-heart text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">
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
            <p class="text-gray-400 mb-6">Beğendiğin içerikleri favorilere ekleyerek kolayca ulaşabilirsin</p>
            <a href="/"
               class="inline-flex items-center px-8 py-4 bg-muzibu-coral text-white font-bold rounded-full hover:bg-opacity-90 hover:scale-105 transition-all duration-200 shadow-xl">
                <i class="fas fa-home mr-2"></i>
                Ana Sayfaya Dön
            </a>
        </div>
    @endif
</div>

{{-- scrollbar-hide CSS moved to tenant-1001.css --}}
