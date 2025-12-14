{{-- RIGHT SIDEBAR - Dynamic Content based on page type --}}
<div class="muzibu-right-sidebar space-y-6 p-4" x-data>

    {{-- PREVIEW MODE: Track List (when hovering on list item) --}}
    <template x-if="!$store.sidebar.isDetailPage && $store.sidebar.previewMode">
        <div class="animate-fade-in">
            {{-- Preview Header --}}
            <div class="mb-4" x-show="$store.sidebar.previewInfo">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-lg overflow-hidden shadow-lg flex-shrink-0 bg-gradient-to-br from-blue-500 to-purple-600">
                        <template x-if="$store.sidebar.previewInfo?.cover">
                            <img :src="$store.sidebar.previewInfo.cover" :alt="$store.sidebar.previewInfo?.title" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!$store.sidebar.previewInfo?.cover">
                            <div class="w-full h-full flex items-center justify-center text-xl">🎵</div>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold text-blue-400 uppercase tracking-wider" x-text="$store.sidebar.previewInfo?.type || 'Önizleme'"></p>
                        <h3 class="text-sm font-bold text-white truncate" x-text="$store.sidebar.previewInfo?.title"></h3>
                        <p class="text-xs text-gray-400" x-text="$store.sidebar.previewTracks.length + ' şarkı'"></p>
                    </div>
                </div>
            </div>

            {{-- Loading State --}}
            <template x-if="$store.sidebar.previewLoading">
                <div class="flex items-center justify-center py-8">
                    <i class="fas fa-spinner fa-spin text-gray-500"></i>
                </div>
            </template>

            {{-- Preview Track List --}}
            <template x-if="!$store.sidebar.previewLoading && $store.sidebar.hasPreviewTracks">
                <div class="space-y-0.5 max-h-[calc(100vh-280px)] overflow-y-auto scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent">
                    <template x-for="(track, index) in $store.sidebar.previewTracks" :key="track.id">
                        <div
                            class="flex items-center gap-2 p-2 rounded-lg hover:bg-white/5 cursor-pointer group transition-all"
                            @click="$dispatch('play-song', { songId: track.id })">
                            <div class="w-5 text-center flex-shrink-0">
                                <span class="text-xs text-gray-500 group-hover:hidden" x-text="index + 1"></span>
                                <i class="fas fa-play text-[10px] text-white hidden group-hover:inline"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-white truncate group-hover:text-blue-400 transition-colors" x-text="track.title"></p>
                                <p class="text-[10px] text-gray-500 truncate" x-text="track.artist"></p>
                            </div>
                            <div class="text-[10px] text-gray-500 flex-shrink-0" x-text="track.duration"></div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="!$store.sidebar.previewLoading && !$store.sidebar.hasPreviewTracks">
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-music text-lg mb-2"></i>
                    <p class="text-xs">Şarkı bulunamadı</p>
                </div>
            </template>
        </div>
    </template>

    {{-- DETAIL PAGE: Track List (when on detail page) --}}
    <template x-if="$store.sidebar.isDetailPage && $store.sidebar.hasTracks">
        <div class="animate-fade-in">
            {{-- Entity Header --}}
            <div class="mb-4" x-show="$store.sidebar.entityInfo">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-lg overflow-hidden shadow-lg flex-shrink-0 bg-gradient-to-br from-muzibu-coral to-purple-600">
                        <template x-if="$store.sidebar.entityInfo?.cover">
                            <img :src="$store.sidebar.entityInfo.cover" :alt="$store.sidebar.entityInfo?.title" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!$store.sidebar.entityInfo?.cover">
                            <div class="w-full h-full flex items-center justify-center text-xl">🎵</div>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold text-muzibu-coral uppercase tracking-wider" x-text="$store.sidebar.entityInfo?.type || 'Playlist'"></p>
                        <h3 class="text-sm font-bold text-white truncate" x-text="$store.sidebar.entityInfo?.title"></h3>
                        <p class="text-xs text-gray-400" x-text="$store.sidebar.tracks.length + ' şarkı'"></p>
                    </div>
                </div>
            </div>

            {{-- Compact Track List --}}
            <div class="space-y-0.5 max-h-[calc(100vh-280px)] overflow-y-auto scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent">
                <template x-for="(track, index) in $store.sidebar.tracks" :key="track.id">
                    <div
                        class="flex items-center gap-2 p-2 rounded-lg hover:bg-white/5 cursor-pointer group transition-all"
                        @click="$dispatch('play-song', { songId: track.id })">
                        {{-- Track Number --}}
                        <div class="w-5 text-center flex-shrink-0">
                            <span class="text-xs text-gray-500 group-hover:hidden" x-text="index + 1"></span>
                            <i class="fas fa-play text-[10px] text-white hidden group-hover:inline"></i>
                        </div>

                        {{-- Track Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-white truncate group-hover:text-muzibu-coral transition-colors" x-text="track.title"></p>
                            <p class="text-[10px] text-gray-500 truncate" x-text="track.artist"></p>
                        </div>

                        {{-- Duration --}}
                        <div class="text-[10px] text-gray-500 flex-shrink-0" x-text="track.duration"></div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- HOMEPAGE: Featured Content (default - when not in preview or detail mode) --}}
    <template x-if="!$store.sidebar.isDetailPage && !$store.sidebar.previewMode">
        <div class="space-y-8">
            {{-- Section 1: Sizin İçin --}}
            <div>
                <div class="mb-4">
                    <h3 class="text-sm font-bold bg-gradient-to-r from-white via-zinc-100 to-muzibu-text-gray bg-clip-text text-transparent uppercase tracking-wider">
                        Sizin İçin
                    </h3>
                </div>

                {{-- Featured Playlists --}}
                @if(isset($featuredPlaylists) && $featuredPlaylists->count() > 0)
                <div class="space-y-0.5">
                    @foreach($featuredPlaylists->take(5) as $playlist)
                    <a href="/playlists/{{ $playlist->getTranslation('slug', app()->getLocale()) }}"
                       wire:navigate
                       class="flex items-center gap-3 p-2 hover:bg-white/5 rounded-lg cursor-pointer transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 flex-shrink-0 overflow-hidden relative">
                            @if($playlist->coverMedia)
                                <img src="{{ thumb($playlist->coverMedia, 40, 40, ['scale' => 1]) }}" alt="{{ getLocaleTitle($playlist->title, 'Playlist') }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-base">🎵</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-semibold text-white truncate group-hover:text-muzibu-coral transition-colors">
                                {{ getLocaleTitle($playlist->title, 'Playlist') }}
                            </h4>
                            <p class="text-[10px] text-gray-500 truncate">
                                {{ $playlist->songs()->count() }} şarkı
                            </p>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-music text-lg mb-1"></i>
                    <p class="text-xs">Henüz playlist yok</p>
                </div>
                @endif
            </div>

            {{-- Section 2: Popüler Şarkılar --}}
            @if(isset($popularSongs) && count($popularSongs) > 0)
            <div>
                <div class="mb-4">
                    <h3 class="text-sm font-bold bg-gradient-to-r from-muzibu-coral via-orange-400 to-yellow-400 bg-clip-text text-transparent uppercase tracking-wider">
                        Popüler
                    </h3>
                </div>
                <div class="space-y-0.5">
                    @foreach($popularSongs as $index => $song)
                    <div class="flex items-center gap-2 p-2 hover:bg-white/5 rounded-lg cursor-pointer transition-all group"
                         @click="$dispatch('play-song', { songId: {{ $song->song_id }} })">
                        <div class="w-5 h-5 rounded bg-gradient-to-br from-orange-500 to-red-600 flex-shrink-0 flex items-center justify-center text-white font-bold text-[10px]">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-semibold text-white truncate group-hover:text-muzibu-coral transition-colors">
                                {{ $song->getTranslation('title', app()->getLocale()) }}
                            </h4>
                            <p class="text-[10px] text-gray-500 truncate">
                                {{ $song->artist ? $song->artist->getTranslation('title', app()->getLocale()) : '' }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Section 3: Yeni Eklenenler --}}
            @if(isset($recentAlbums) && count($recentAlbums) > 0)
            <div>
                <div class="mb-4">
                    <h3 class="text-sm font-bold bg-gradient-to-r from-green-400 via-emerald-400 to-teal-400 bg-clip-text text-transparent uppercase tracking-wider">
                        Yeni
                    </h3>
                </div>
                <div class="space-y-0.5">
                    @foreach($recentAlbums as $album)
                    <a href="/albums/{{ $album->getTranslation('slug', app()->getLocale()) }}"
                       wire:navigate
                       class="flex items-center gap-3 p-2 hover:bg-white/5 rounded-lg cursor-pointer transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-500 to-teal-600 flex-shrink-0 overflow-hidden">
                            @if($album->coverMedia)
                                <img src="{{ thumb($album->coverMedia, 40, 40, ['scale' => 1]) }}" alt="{{ getLocaleTitle($album->title, 'Album') }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-base">💿</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-semibold text-white truncate group-hover:text-muzibu-coral transition-colors">
                                {{ getLocaleTitle($album->title, 'Album') }}
                            </h4>
                            <p class="text-[10px] text-gray-500 truncate">
                                {{ $album->artist ? $album->artist->getTranslation('title', app()->getLocale()) : '' }}
                            </p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Section 4: Türler --}}
            @if(isset($genres) && count($genres) > 0)
            <div>
                <div class="mb-4">
                    <h3 class="text-sm font-bold bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-400 bg-clip-text text-transparent uppercase tracking-wider">
                        Türler
                    </h3>
                </div>
                <div class="grid grid-cols-2 gap-1.5">
                    @foreach($genres->take(6) as $genre)
                    <a href="/genres/{{ $genre->getTranslation('slug', app()->getLocale()) }}"
                       wire:navigate
                       class="bg-gradient-to-br from-blue-600/10 to-purple-600/10 hover:from-blue-600/20 hover:to-purple-600/20 border border-white/5 rounded-lg p-2.5 text-center transition-all group">
                        <div class="text-base mb-0.5">🎸</div>
                        <div class="text-[10px] font-semibold text-white group-hover:text-muzibu-coral transition-colors truncate">
                            {{ getLocaleTitle($genre->title, 'Genre') }}
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </template>

    {{-- FALLBACK: Empty State (no tracks, not homepage) --}}
    <template x-if="$store.sidebar.isDetailPage && !$store.sidebar.hasTracks">
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-music text-2xl mb-2"></i>
            <p class="text-xs">Şarkı yükleniyor...</p>
        </div>
    </template>
</div>
