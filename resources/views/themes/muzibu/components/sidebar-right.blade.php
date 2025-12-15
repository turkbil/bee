{{-- RIGHT SIDEBAR - Premium Card Design (Draft 1) --}}
<div class="h-full" x-data>

    {{-- PREVIEW MODE: Premium Card Design (when clicking on list item) --}}
    <template x-if="!$store.sidebar.isDetailPage && $store.sidebar.previewMode">
        <div class="animate-fade-in h-full flex flex-col">
            {{-- Large Cover Header with Gradient --}}
            <div class="relative h-56 flex-shrink-0 overflow-hidden rounded-t-xl">
                {{-- Cover Image --}}
                <template x-if="$store.sidebar.previewInfo?.cover">
                    <img :src="$store.sidebar.previewInfo.cover"
                         :alt="$store.sidebar.previewInfo?.title"
                         class="w-full h-full object-cover">
                </template>
                <template x-if="!$store.sidebar.previewInfo?.cover">
                    <div class="w-full h-full bg-gradient-to-br from-muzibu-coral via-purple-600 to-blue-600 flex items-center justify-center">
                        <i class="fas fa-music text-white/30 text-5xl"></i>
                    </div>
                </template>

                {{-- Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>

                {{-- Action Buttons --}}
                <div class="absolute top-3 right-3 flex items-center gap-2">
                    {{-- Favorite Button --}}
                    <button @click="$dispatch('toggle-favorite-entity', { type: $store.sidebar.previewInfo?.type?.toLowerCase(), id: $store.sidebar.previewInfo?.id })"
                            class="w-10 h-10 bg-black/40 hover:bg-black/60 backdrop-blur-sm rounded-full flex items-center justify-center transition-all hover:scale-110"
                            :class="$store.sidebar.previewInfo?.is_favorite ? 'text-muzibu-coral' : 'text-white'">
                        <i :class="$store.sidebar.previewInfo?.is_favorite ? 'fas fa-heart' : 'far fa-heart'" class="text-sm"></i>
                    </button>
                    {{-- Play All Button --}}
                    <button @click="$dispatch('play-all-preview')"
                            class="w-11 h-11 bg-muzibu-coral hover:bg-green-500 rounded-full flex items-center justify-center shadow-xl hover:scale-110 transition-all duration-200"
                            style="box-shadow: 0 0 20px rgba(255,90,95,0.4)">
                        <i class="fas fa-play text-white text-sm ml-0.5"></i>
                    </button>
                </div>

                {{-- Entity Info at Bottom --}}
                <div class="absolute bottom-0 left-0 right-0 p-3">
                    <p class="text-[10px] font-bold text-muzibu-coral uppercase tracking-wider"
                       x-text="$store.sidebar.previewInfo?.type || 'Playlist'"></p>
                    <h3 class="text-lg font-bold text-white truncate leading-tight mt-0.5"
                        x-text="$store.sidebar.previewInfo?.title"></h3>
                    <p class="text-xs text-white/60 mt-1">
                        <span x-text="$store.sidebar.previewTracks.length"></span> sarki
                    </p>
                </div>
            </div>

            {{-- Loading State --}}
            <template x-if="$store.sidebar.previewLoading">
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-muzibu-coral text-xl mb-2"></i>
                        <p class="text-xs text-gray-500">Yukleniyor...</p>
                    </div>
                </div>
            </template>

            {{-- Track List with Thumbnails --}}
            <template x-if="!$store.sidebar.previewLoading && $store.sidebar.hasPreviewTracks">
                <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent p-2 space-y-0.5">
                    <template x-for="(track, index) in $store.sidebar.previewTracks" :key="track.id">
                        <div class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer group transition-all"
                             :class="track.isPlaying ? 'bg-muzibu-coral/10 border border-muzibu-coral/20' : ''"
                             @click="
                                // 🎯 PREVIEW MODE: Set context from preview info
                                if ($store.sidebar.previewMode && $store.sidebar.previewInfo) {
                                    $store.player.setPlayContext({
                                        type: $store.sidebar.previewInfo.type || 'playlist',
                                        id: $store.sidebar.previewInfo.id,
                                        name: $store.sidebar.previewInfo.title,
                                        offset: index
                                    });
                                }
                                $dispatch('play-song', { songId: track.id })
                             ">

                            {{-- Track Number / Play Icon --}}
                            <div class="w-5 text-center flex-shrink-0">
                                <template x-if="track.isPlaying">
                                    <i class="fas fa-volume-up text-muzibu-coral text-xs animate-pulse"></i>
                                </template>
                                <template x-if="!track.isPlaying">
                                    <span class="text-xs text-gray-500 group-hover:hidden" x-text="index + 1"></span>
                                </template>
                                <i class="fas fa-play text-muzibu-coral text-[10px] hidden group-hover:inline" x-show="!track.isPlaying"></i>
                            </div>

                            {{-- Track Thumbnail --}}
                            <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-gradient-to-br from-gray-700 to-gray-800">
                                <template x-if="track.cover">
                                    <img :src="track.cover" :alt="track.title" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!track.cover">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-music text-gray-600 text-xs"></i>
                                    </div>
                                </template>
                            </div>

                            {{-- Track Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate transition-colors"
                                   :class="track.isPlaying ? 'text-muzibu-coral' : 'text-white group-hover:text-muzibu-coral'"
                                   x-text="track.title"></p>
                                <p class="text-xs text-gray-500 truncate" x-text="track.artist"></p>
                            </div>

                            {{-- Duration (hide on hover) --}}
                            <div class="text-xs text-gray-600 flex-shrink-0 group-hover:hidden" x-text="track.duration"></div>

                            {{-- Actions (show on hover) --}}
                            <div class="hidden group-hover:flex items-center gap-1 flex-shrink-0">
                                <button @click.stop="$dispatch('toggle-favorite', { type: 'song', id: track.id })"
                                        class="w-6 h-6 flex items-center justify-center rounded-full hover:bg-white/10 transition-colors"
                                        :class="track.is_favorite ? 'text-muzibu-coral' : 'text-gray-400 hover:text-muzibu-coral'">
                                    <i :class="track.is_favorite ? 'fas fa-heart' : 'far fa-heart'" class="text-xs"></i>
                                </button>
                                <button @click.stop="Alpine.store('contextMenu').openContextMenu($event, 'song', { id: track.id, title: track.title })"
                                        class="w-6 h-6 flex items-center justify-center rounded-full text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
                                    <i class="fas fa-ellipsis-v text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="!$store.sidebar.previewLoading && !$store.sidebar.hasPreviewTracks">
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center text-gray-500">
                        <i class="fas fa-music text-2xl mb-2"></i>
                        <p class="text-xs">Sarki bulunamadi</p>
                    </div>
                </div>
            </template>
        </div>
    </template>

    {{-- DETAIL PAGE: Premium Card Design --}}
    <template x-if="$store.sidebar.isDetailPage && $store.sidebar.hasTracks">
        <div class="animate-fade-in h-full flex flex-col">
            {{-- Large Cover Header with Gradient --}}
            <div class="relative h-56 flex-shrink-0 overflow-hidden rounded-t-xl">
                {{-- Cover Image --}}
                <template x-if="$store.sidebar.entityInfo?.cover">
                    <img :src="$store.sidebar.entityInfo.cover"
                         :alt="$store.sidebar.entityInfo?.title"
                         class="w-full h-full object-cover">
                </template>
                <template x-if="!$store.sidebar.entityInfo?.cover">
                    <div class="w-full h-full bg-gradient-to-br from-muzibu-coral via-purple-600 to-blue-600 flex items-center justify-center">
                        <i class="fas fa-music text-white/30 text-5xl"></i>
                    </div>
                </template>

                {{-- Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>

                {{-- Action Buttons --}}
                <div class="absolute top-3 right-3 flex items-center gap-2">
                    {{-- Favorite Button --}}
                    <button @click="$dispatch('toggle-favorite-entity', { type: $store.sidebar.entityInfo?.type?.toLowerCase(), id: $store.sidebar.entityInfo?.id })"
                            class="w-10 h-10 bg-black/40 hover:bg-black/60 backdrop-blur-sm rounded-full flex items-center justify-center transition-all hover:scale-110"
                            :class="$store.sidebar.entityInfo?.is_favorite ? 'text-muzibu-coral' : 'text-white'">
                        <i :class="$store.sidebar.entityInfo?.is_favorite ? 'fas fa-heart' : 'far fa-heart'" class="text-sm"></i>
                    </button>
                    {{-- Play All Button --}}
                    <button @click="$dispatch('play-all-entity')"
                            class="w-11 h-11 bg-muzibu-coral hover:bg-green-500 rounded-full flex items-center justify-center shadow-xl hover:scale-110 transition-all duration-200"
                            style="box-shadow: 0 0 20px rgba(255,90,95,0.4)">
                        <i class="fas fa-play text-white text-sm ml-0.5"></i>
                    </button>
                </div>

                {{-- Entity Info at Bottom --}}
                <div class="absolute bottom-0 left-0 right-0 p-3">
                    <p class="text-[10px] font-bold text-muzibu-coral uppercase tracking-wider"
                       x-text="$store.sidebar.entityInfo?.type || 'Playlist'"></p>
                    <h3 class="text-lg font-bold text-white truncate leading-tight mt-0.5"
                        x-text="$store.sidebar.entityInfo?.title"></h3>
                    <p class="text-xs text-white/60 mt-1">
                        <span x-text="$store.sidebar.tracks.length"></span> sarki
                    </p>
                </div>
            </div>

            {{-- Track List with Thumbnails --}}
            <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent p-2 space-y-0.5">
                <template x-for="(track, index) in $store.sidebar.tracks" :key="track.id">
                    <div class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer group transition-all"
                         :class="track.isPlaying ? 'bg-muzibu-coral/10 border border-muzibu-coral/20' : ''"
                         @click="
                            // 🎯 DETAIL PAGE: Set context from sidebar entity info
                            if ($store.sidebar.isDetailPage && $store.sidebar.entityInfo) {
                                $store.player.setPlayContext({
                                    type: $store.sidebar.pageType,
                                    id: $store.sidebar.entityInfo.id,
                                    name: $store.sidebar.entityInfo.title,
                                    offset: index
                                });
                            }
                            $dispatch('play-song', { songId: track.id })
                         ">

                        {{-- Track Number / Play Icon --}}
                        <div class="w-5 text-center flex-shrink-0">
                            <template x-if="track.isPlaying">
                                <i class="fas fa-volume-up text-muzibu-coral text-xs animate-pulse"></i>
                            </template>
                            <template x-if="!track.isPlaying">
                                <span class="text-xs text-gray-500 group-hover:hidden" x-text="index + 1"></span>
                            </template>
                            <i class="fas fa-play text-muzibu-coral text-[10px] hidden group-hover:inline" x-show="!track.isPlaying"></i>
                        </div>

                        {{-- Track Thumbnail --}}
                        <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-gradient-to-br from-gray-700 to-gray-800">
                            <template x-if="track.cover">
                                <img :src="track.cover" :alt="track.title" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!track.cover">
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-music text-gray-600 text-xs"></i>
                                </div>
                            </template>
                        </div>

                        {{-- Track Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate transition-colors"
                               :class="track.isPlaying ? 'text-muzibu-coral' : 'text-white group-hover:text-muzibu-coral'"
                               x-text="track.title"></p>
                            <p class="text-xs text-gray-500 truncate" x-text="track.artist"></p>
                        </div>

                        {{-- Duration (hide on hover) --}}
                        <div class="text-xs text-gray-600 flex-shrink-0 group-hover:hidden" x-text="track.duration"></div>

                        {{-- Actions (show on hover) --}}
                        <div class="hidden group-hover:flex items-center gap-1 flex-shrink-0">
                            <button @click.stop="$dispatch('toggle-favorite', { type: 'song', id: track.id })"
                                    class="w-6 h-6 flex items-center justify-center rounded-full hover:bg-white/10 transition-colors"
                                    :class="track.is_favorite ? 'text-muzibu-coral' : 'text-gray-400 hover:text-muzibu-coral'">
                                <i :class="track.is_favorite ? 'fas fa-heart' : 'far fa-heart'" class="text-xs"></i>
                            </button>
                            <button @click.stop="Alpine.store('contextMenu').openContextMenu($event, 'song', { id: track.id, title: track.title })"
                                    class="w-6 h-6 flex items-center justify-center rounded-full text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
                                <i class="fas fa-ellipsis-v text-xs"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- HOMEPAGE: Featured Content (default - when not in preview or detail mode) --}}
    <template x-if="!$store.sidebar.isDetailPage && !$store.sidebar.previewMode">
        <div class="p-3 space-y-5 h-full overflow-y-auto scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent">

            {{-- Section 1: Sizin Icin --}}
            <div>
                <div class="mb-3">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-1 h-4 bg-gradient-to-b from-purple-500 to-pink-500 rounded-full"></span>
                        Sizin Icin
                    </h3>
                </div>

                @if(isset($featuredPlaylists) && $featuredPlaylists->count() > 0)
                <div class="space-y-1">
                    @foreach($featuredPlaylists->take(5) as $playlist)
                    <a href="/playlists/{{ $playlist->getTranslation('slug', app()->getLocale()) }}"
                       wire:navigate
                       class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 flex-shrink-0 overflow-hidden shadow-lg">
                            @if($playlist->coverMedia)
                                <img src="{{ thumb($playlist->coverMedia, 40, 40, ['scale' => 1]) }}" alt="{{ getLocaleTitle($playlist->title, 'Playlist') }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-base">🎵</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-white truncate group-hover:text-muzibu-coral transition-colors">
                                {{ getLocaleTitle($playlist->title, 'Playlist') }}
                            </h4>
                            <p class="text-xs text-gray-500 truncate">
                                {{ $playlist->songs()->count() }} sarki
                            </p>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-music text-lg mb-1"></i>
                    <p class="text-xs">Henuz playlist yok</p>
                </div>
                @endif
            </div>

            {{-- Section 2: Populer Sarkilar --}}
            @if(isset($popularSongs) && count($popularSongs) > 0)
            <div>
                <div class="mb-3">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-1 h-4 bg-gradient-to-b from-orange-500 to-red-500 rounded-full"></span>
                        Populer
                    </h3>
                </div>
                <div class="space-y-1">
                    @foreach($popularSongs as $index => $song)
                    <div class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer transition-all group"
                         @click="
                            // 🎯 HOMEPAGE: Set 'popular' context for homepage popular songs
                            $store.player.setPlayContext({
                                type: 'popular',
                                id: null,
                                name: 'Popüler Şarkılar',
                                offset: {{ $index }}
                            });
                            $dispatch('play-song', { songId: {{ $song->song_id }} })
                         ">
                        {{-- Rank Badge --}}
                        <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-orange-500 to-red-600 flex-shrink-0 flex items-center justify-center text-white font-bold text-xs shadow-lg">
                            {{ $index + 1 }}
                        </div>
                        {{-- Song Info --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-white truncate group-hover:text-muzibu-coral transition-colors">
                                {{ $song->getTranslation('title', app()->getLocale()) }}
                            </h4>
                            <p class="text-xs text-gray-500 truncate">
                                {{ $song->artist ? $song->artist->getTranslation('title', app()->getLocale()) : '' }}
                            </p>
                        </div>
                        {{-- Play Icon on Hover --}}
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-play text-muzibu-coral text-xs"></i>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Section 3: Yeni Eklenenler --}}
            @if(isset($recentAlbums) && count($recentAlbums) > 0)
            <div>
                <div class="mb-3">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-1 h-4 bg-gradient-to-b from-green-500 to-teal-500 rounded-full"></span>
                        Yeni
                    </h3>
                </div>
                <div class="space-y-1">
                    @foreach($recentAlbums as $album)
                    <a href="/albums/{{ $album->getTranslation('slug', app()->getLocale()) }}"
                       wire:navigate
                       class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-500 to-teal-600 flex-shrink-0 overflow-hidden shadow-lg">
                            @if($album->coverMedia)
                                <img src="{{ thumb($album->coverMedia, 40, 40, ['scale' => 1]) }}" alt="{{ getLocaleTitle($album->title, 'Album') }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-base">💿</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-white truncate group-hover:text-muzibu-coral transition-colors">
                                {{ getLocaleTitle($album->title, 'Album') }}
                            </h4>
                            <p class="text-xs text-gray-500 truncate">
                                {{ $album->artist ? $album->artist->getTranslation('title', app()->getLocale()) : '' }}
                            </p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Section 4: Turler --}}
            @if(isset($genres) && count($genres) > 0)
            <div>
                <div class="mb-3">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-1 h-4 bg-gradient-to-b from-blue-500 to-purple-500 rounded-full"></span>
                        Turler
                    </h3>
                </div>
                <div class="grid grid-cols-2 gap-1.5">
                    @foreach($genres->take(6) as $genre)
                    <a href="/genres/{{ $genre->getTranslation('slug', app()->getLocale()) }}"
                       wire:navigate
                       class="bg-gradient-to-br from-white/5 to-white/0 hover:from-white/10 hover:to-white/5 border border-white/5 rounded-xl p-2.5 text-center transition-all group">
                        <div class="text-base mb-1">🎸</div>
                        <div class="text-xs font-medium text-white group-hover:text-muzibu-coral transition-colors truncate">
                            {{ getLocaleTitle($genre->title, 'Genre') }}
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </template>

    {{-- FALLBACK: Loading State --}}
    <template x-if="$store.sidebar.isDetailPage && !$store.sidebar.hasTracks">
        <div class="h-full flex items-center justify-center">
            <div class="text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-muzibu-coral text-2xl mb-3"></i>
                <p class="text-xs">Sarki yukleniyor...</p>
            </div>
        </div>
    </template>
</div>
