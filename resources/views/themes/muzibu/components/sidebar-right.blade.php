{{-- RIGHT SIDEBAR - v6: Tab System with Dynamic Header --}}
<div class="h-full" x-data="{ songsTab: 'new' }">

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
                        <span x-text="$store.sidebar.previewTracks.length"></span> {{ trans('muzibu::front.general.song') }}
                    </p>
                </div>
            </div>

            {{-- Loading State --}}
            <template x-if="$store.sidebar.previewLoading">
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-muzibu-coral text-xl mb-2"></i>
                        <p class="text-xs text-gray-500">{{ trans('muzibu::front.general.loading') }}</p>
                    </div>
                </div>
            </template>

            {{-- Track List with Thumbnails --}}
            <template x-if="!$store.sidebar.previewLoading && $store.sidebar.hasPreviewTracks">
                <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent p-2 space-y-0.5">
                    <template x-for="(track, index) in $store.sidebar.previewTracks" :key="track.id">
                        <div class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer group transition-all"
                             :class="track.isPlaying ? 'bg-muzibu-coral/10 border border-muzibu-coral/20' : ''"
                             @click="(($store.sidebar.previewMode && $store.sidebar.previewInfo) ? $store.player.setPlayContext({
                                 type: $store.sidebar.previewInfo.type || 'playlist',
                                 id: $store.sidebar.previewInfo.id,
                                 name: $store.sidebar.previewInfo.title,
                                 offset: index
                             }) : null), $dispatch('play-song', { songId: track.id })">

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
                            <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-gradient-to-br from-muzibu-coral to-orange-600">
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
                        <p class="text-xs">{{ trans('muzibu::front.messages.song_not_found') }}</p>
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
                        <span x-text="$store.sidebar.tracks.length"></span> {{ trans('muzibu::front.general.song') }}
                    </p>
                </div>
            </div>

            {{-- Track List with Thumbnails --}}
            <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent p-2 space-y-0.5">
                <template x-for="(track, index) in $store.sidebar.tracks" :key="track.id">
                    <div class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer group transition-all"
                         :class="track.isPlaying ? 'bg-muzibu-coral/10 border border-muzibu-coral/20' : ''"
                         @click="(($store.sidebar.isDetailPage && $store.sidebar.entityInfo) ? $store.player.setPlayContext({
                             type: $store.sidebar.pageType,
                             id: $store.sidebar.entityInfo.id,
                             name: $store.sidebar.entityInfo.title,
                             offset: index
                         }) : null), $dispatch('play-song', { songId: track.id })">

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
                        <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-gradient-to-br from-muzibu-coral to-orange-600">
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

    {{-- HOMEPAGE: Tab System with Songs (v6 Design) --}}
    <template x-if="!$store.sidebar.isDetailPage && !$store.sidebar.previewMode">
        <div class="h-full flex flex-col bg-gradient-to-b from-white/[0.03] to-transparent rounded-2xl overflow-hidden">

            {{-- Premium Header with Rounded Top --}}
            <div class="relative overflow-hidden rounded-t-2xl transition-all duration-300"
                 :class="{
                     'bg-gradient-to-br from-orange-500/30 via-red-500/20 to-transparent': songsTab === 'popular',
                     'bg-gradient-to-br from-green-500/30 via-emerald-500/20 to-transparent': songsTab === 'new',
                     'bg-gradient-to-br from-blue-500/30 via-indigo-500/20 to-transparent': songsTab === 'trend'
                 }">
                <div class="px-3 py-1">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <template x-if="songsTab === 'popular'">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-fire text-orange-400"></i>
                                {{ trans('muzibu::front.general.popular_songs') }}
                            </span>
                        </template>
                        <template x-if="songsTab === 'new'">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-star text-green-400"></i>
                                {{ trans('muzibu::front.general.new_songs') }}
                            </span>
                        </template>
                        <template x-if="songsTab === 'trend'">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-chart-line text-blue-400"></i>
                                {{ trans('muzibu::front.general.trending_songs') }}
                            </span>
                        </template>
                    </h3>
                    <p class="text-xs text-gray-400 mt-1"
                       x-text="songsTab === 'popular' ? (window.muzibuPlayerConfig?.frontLang?.sidebar?.most_played || 'Most Played') : (songsTab === 'new' ? (window.muzibuPlayerConfig?.frontLang?.sidebar?.newly_added || 'Newly Added') : (window.muzibuPlayerConfig?.frontLang?.sidebar?.trending || 'Trending'))"></p>
                </div>
            </div>

            {{-- Tab Headers (3 tabs) - Pill Style: Yeni → Trend → Popüler --}}
            <div class="flex gap-1 px-3 py-2 bg-black/20">
                <button @click="songsTab = 'new'"
                        class="flex-1 py-2 px-3 text-xs font-medium transition-all rounded-full"
                        :class="songsTab === 'new' ? 'bg-green-500/20 text-green-400 font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5'">
                    <i class="fas fa-star mr-1"></i>{{ trans('muzibu::front.sidebar.new') }}
                </button>
                <button @click="songsTab = 'trend'"
                        class="flex-1 py-2 px-3 text-xs font-medium transition-all rounded-full"
                        :class="songsTab === 'trend' ? 'bg-blue-500/20 text-blue-400 font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5'">
                    <i class="fas fa-chart-line mr-1"></i>{{ trans('muzibu::front.sidebar.trend') }}
                </button>
                <button @click="songsTab = 'popular'"
                        class="flex-1 py-2 px-3 text-xs font-medium transition-all rounded-full"
                        :class="songsTab === 'popular' ? 'bg-orange-500/20 text-orange-400 font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5'">
                    <i class="fas fa-fire mr-1"></i>{{ trans('muzibu::front.sidebar.popular') }}
                </button>
            </div>

            {{-- Song Lists --}}
            <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent p-2 space-y-0.5">

                {{-- POPULAR SONGS TAB --}}
                <template x-if="songsTab === 'popular'">
                    <div class="space-y-0.5">
                        @if(isset($popularSongs) && count($popularSongs) > 0)
                            @foreach($popularSongs->take(15) as $index => $song)
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
                            @endforeach
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-music text-2xl mb-2"></i>
                                <p class="text-xs">{{ trans('muzibu::front.general.no_songs') }}</p>
                            </div>
                        @endif
                    </div>
                </template>

                {{-- NEW SONGS TAB --}}
                <template x-if="songsTab === 'new'">
                    <div class="space-y-0.5">
                        @if(isset($newSongs) && count($newSongs) > 0)
                            @foreach($newSongs->take(15) as $index => $song)
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
                            @endforeach
                        @elseif(isset($popularSongs) && count($popularSongs) > 10)
                            {{-- Fallback: Use slice of popularSongs if newSongs not available --}}
                            @foreach($popularSongs->slice(10)->take(15) as $index => $song)
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
                            @endforeach
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-star text-2xl mb-2"></i>
                                <p class="text-xs">{{ trans('muzibu::front.messages.no_songs_found') }}</p>
                            </div>
                        @endif
                    </div>
                </template>

                {{-- TREND SONGS TAB --}}
                <template x-if="songsTab === 'trend'">
                    <div class="space-y-0.5">
                        @if(isset($trendSongs) && count($trendSongs) > 0)
                            @foreach($trendSongs->take(15) as $index => $song)
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
                            @endforeach
                        @elseif(isset($popularSongs) && count($popularSongs) > 0)
                            {{-- Fallback: Use popularSongs shuffled if trendSongs not available --}}
                            @foreach($popularSongs->shuffle()->take(15) as $index => $song)
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
                            @endforeach
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-chart-line text-2xl mb-2"></i>
                                <p class="text-xs">{{ trans('muzibu::front.messages.no_songs_found') }}</p>
                            </div>
                        @endif
                    </div>
                </template>

            </div>
        </div>
    </template>

    {{-- FALLBACK: Loading State --}}
    <template x-if="$store.sidebar.isDetailPage && !$store.sidebar.hasTracks">
        <div class="h-full flex items-center justify-center">
            <div class="text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-muzibu-coral text-2xl mb-3"></i>
                <p class="text-xs">{{ trans('muzibu::front.sidebar.song_loading') }}</p>
            </div>
        </div>
    </template>
</div>
