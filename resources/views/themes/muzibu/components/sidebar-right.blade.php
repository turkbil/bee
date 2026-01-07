{{-- RIGHT SIDEBAR - v6: Tab System with Dynamic Header --}}

{{-- My Playlist Preview Hover Styles --}}
<style>
    /* Default state: duration visible, remove button hidden */
    .playlist-item .duration-text {
        display: inline !important;
    }
    .playlist-item .remove-btn {
        display: none !important;
    }

    /* Hover state: duration hidden, remove button visible */
    .playlist-item:hover .duration-text {
        display: none !important;
    }
    .playlist-item:hover .remove-btn {
        display: flex !important;
    }

    /* Sortable styles for playlist reorder */
    .playlist-item {
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
    }

    /* Ghost - bırakılacak yeri gösteren placeholder */
    .playlist-item.sortable-ghost {
        opacity: 0.4 !important;
        background: rgba(255, 107, 107, 0.1) !important;
        border: 2px dashed rgba(255, 107, 107, 0.4) !important;
        border-radius: 0.75rem !important;
    }
    .playlist-item.sortable-ghost * {
        opacity: 1 !important;
        visibility: visible !important;
    }

    /* Sürüklenen item */
    .playlist-item.sortable-drag {
        opacity: 1 !important;
        background: rgba(30, 30, 35, 0.98) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
        border-radius: 0.75rem !important;
        z-index: 9999 !important;
    }

    /* Seçilen item */
    .playlist-item.sortable-chosen {
        background: rgba(255, 107, 107, 0.1) !important;
    }
</style>

<div class="h-full" x-data="{ songsTab: 'new' }">

    {{-- PREVIEW MODE: Premium Card Design (when clicking on list item - WORKS EVERYWHERE) --}}
    <template x-if="$store.sidebar?.previewMode">
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
                    {{-- Playlist/Album: Show duration, Genre/Sector: Show nothing --}}
                    <template x-if="$store.sidebar.previewInfo?.type === 'Playlist' || $store.sidebar.previewInfo?.type === 'Album'">
                        <p class="text-xs text-white/60 mt-1" x-text="$store.sidebar.previewTotalDuration"></p>
                    </template>
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

            {{-- My-Playlist Preview (Queue Style with Reorder/Delete) - ONLY FOR USER'S OWN PLAYLISTS --}}
            <template x-if="!$store.sidebar.previewLoading && $store.sidebar.hasPreviewTracks && $store.sidebar.previewInfo?.type === 'Playlist' && $store.sidebar.previewInfo?.is_mine === true">
                <div class="flex-1 overflow-y-auto bg-slate-900/50"
                     x-ref="myPlaylistPreviewList"
                     x-data="{ sortableReady: false }"
                     x-effect="
                        if ($store.sidebar.previewTracks?.length > 0 && $refs.myPlaylistPreviewList && typeof Sortable !== 'undefined') {
                            $nextTick(() => {
                                if ($refs.myPlaylistPreviewList._sortable) $refs.myPlaylistPreviewList._sortable.destroy();
                                $refs.myPlaylistPreviewList._sortable = new Sortable($refs.myPlaylistPreviewList, {
                                    animation: 200,
                                    handle: '.playlist-drag-handle',
                                    ghostClass: 'sortable-ghost',
                                    chosenClass: 'sortable-chosen',
                                    dragClass: 'sortable-drag',
                                    forceFallback: true,
                                    onEnd: async (evt) => {
                                        if (evt.oldIndex === evt.newIndex) return;

                                        const tracks = [...$store.sidebar.previewTracks];
                                        const movedTrack = tracks[evt.oldIndex];
                                        tracks.splice(evt.oldIndex, 1);
                                        tracks.splice(evt.newIndex, 0, movedTrack);

                                        $store.sidebar.previewTracks = tracks;

                                        const songPositions = tracks.map((t, i) => ({ song_id: t.id, position: i }));
                                        try {
                                            const resp = await fetch(`/api/muzibu/playlists/${$store.sidebar.previewInfo.id}/reorder`, {
                                                method: 'PUT',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content
                                                },
                                                body: JSON.stringify({ song_positions: songPositions })
                                            });
                                            const data = await resp.json();
                                            if (data.success) {
                                                Alpine.store('toast').show('Sıralama kaydedildi', 'success');
                                            }
                                        } catch (e) {
                                            console.error('Reorder error:', e);
                                        }
                                    }
                                });
                            });
                        }
                     ">
                    <template x-for="(track, index) in $store.sidebar.previewTracks" :key="'pl-' + track.id + '-' + index">
                        <div class="group flex items-center gap-2.5 px-3 py-2 hover:bg-white/5 cursor-pointer transition-all playlist-item"
                             @click="$dispatch('play-song', { songId: track.id })">

                            {{-- Thumbnail with Play Overlay --}}
                            <div class="relative w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-gradient-to-br from-muzibu-coral to-orange-600">
                                <template x-if="track.cover">
                                    <img :src="track.cover" :alt="track.title" class="w-full h-full object-cover" loading="lazy">
                                </template>
                                <template x-if="!track.cover">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-music text-white/30 text-xs"></i>
                                    </div>
                                </template>
                                {{-- Play Overlay --}}
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                    <i class="fas fa-play text-white text-xs"></i>
                                </div>
                            </div>

                            {{-- Song Info --}}
                            <div class="flex-1 min-w-0">
                                <h4 class="text-white text-sm font-medium truncate group-hover:text-muzibu-coral transition-colors"
                                    x-text="track.title"></h4>
                                <p class="text-gray-400 text-xs truncate" x-text="track.artist"></p>
                            </div>

                            {{-- Duration / Remove Button (Same Position - Toggle on Hover) --}}
                            <div class="w-10 h-6 flex items-center justify-center flex-shrink-0 relative">
                                {{-- Duration (Default State - hides on hover via CSS) --}}
                                <span class="duration-text text-xs text-gray-600" x-show="track.duration" x-text="track.duration"></span>
                                {{-- Remove Button (Hover State - shows on hover via CSS) --}}
                                <button
                                    @click.stop="$store.confirmModal.show({
                                        title: 'Şarkıyı Çıkar',
                                        message: 'Bu şarkıyı playlist\'ten çıkarmak istediğinizden emin misiniz?',
                                        confirmText: 'Çıkar',
                                        cancelText: 'Vazgeç',
                                        type: 'danger',
                                        onConfirm: async () => {
                                            const resp = await fetch(`/api/muzibu/playlists/${$store.sidebar.previewInfo.id}/remove-song/${track.id}`, {
                                                method: 'DELETE',
                                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content }
                                            });
                                            const data = await resp.json();
                                            if(data.success) {
                                                Alpine.store('toast').show('Şarkı çıkarıldı', 'success');
                                                $store.sidebar.refreshPreview();
                                            }
                                        }
                                    })"
                                    class="remove-btn absolute inset-0 w-full h-full flex items-center justify-center rounded-full text-gray-400 hover:text-red-400 hover:bg-red-500/10 transition-all"
                                    style="display: none;"
                                    title="Çıkar"
                                >
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>

                            {{-- Drag Handle (Always Visible) --}}
                            <div class="playlist-drag-handle w-6 h-6 flex items-center justify-center rounded-full text-gray-500 cursor-grab active:cursor-grabbing hover:bg-white/10 hover:text-white transition-all flex-shrink-0" title="Sürükle">
                                <i class="fas fa-grip-vertical text-xs"></i>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Normal Playlist Preview (Not Mine - Album Style) --}}
            <template x-if="!$store.sidebar.previewLoading && $store.sidebar.hasPreviewTracks && $store.sidebar.previewInfo?.type === 'Playlist' && $store.sidebar.previewInfo?.is_mine !== true">
                <div class="flex-1 overflow-y-auto bg-slate-900/50"
                     x-ref="previewScrollContainer"
                     @scroll="
                        if ($store.sidebar.canLoadMorePreviewTracks) {
                            const container = $refs.previewScrollContainer;
                            const scrolledToBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 200;
                            if (scrolledToBottom) {
                                $store.sidebar.loadMorePreviewTracks();
                            }
                        }
                     ">
                    <template x-for="(track, index) in $store.sidebar.previewTracks" :key="track.id">
                        <div class="group flex items-center gap-3 px-4 py-2.5 hover:bg-white/5 cursor-pointer transition-all"
                             @click="$dispatch('play-song', { songId: track.id })"
                             x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', { id: track.id, title: track.title, artist: track.artist, album_id: track.album_id, album_slug: track.album_slug, album_title: track.album_title, album_cover: track.album_cover, is_favorite: track.is_favorite || false })">

                            {{-- Thumbnail with Play Overlay --}}
                            <div class="relative w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-gradient-to-br from-muzibu-coral to-orange-600">
                                <template x-if="track.cover">
                                    <img :src="track.cover" :alt="track.title" class="w-full h-full object-cover" loading="lazy">
                                </template>
                                <template x-if="!track.cover">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-music text-white/50 text-xs"></i>
                                    </div>
                                </template>
                                {{-- Play Overlay on Hover --}}
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                    <i class="fas fa-play text-white text-sm"></i>
                                </div>
                            </div>

                            {{-- Song Info --}}
                            <div class="flex-1 min-w-0">
                                <h4 class="text-white text-sm font-medium truncate group-hover:text-muzibu-coral transition-colors"
                                    x-text="track.title"></h4>
                                <p class="text-gray-400 text-xs truncate" x-text="track.artist"></p>
                            </div>

                            {{-- Actions (Favorite + Time/3-Dot) - Fixed Width --}}
                            <div class="flex items-center gap-1 flex-shrink-0">
                                {{-- Favorite Button (Always Same Position) --}}
                                <button @click.stop="$store.favorites.toggle('song', track.id)"
                                        class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-white/10 transition-all"
                                        :class="$store.favorites.isFavorite('song', track.id) ? 'text-muzibu-coral' : 'text-gray-400 hover:text-white'"
                                        title="Favorilere Ekle/Çıkar">
                                    <i :class="$store.favorites.isFavorite('song', track.id) ? 'fas fa-heart' : 'far fa-heart'"
                                       class="text-xs"></i>
                                </button>

                                {{-- Duration / 3-Dot Menu Container (Fixed 60px width) --}}
                                <div class="w-[60px] flex items-center justify-end">
                                    {{-- Duration (hide on hover) --}}
                                    <span class="text-xs text-gray-600 group-hover:hidden" x-show="track.duration" x-text="track.duration"></span>

                                    {{-- 3-Dot Menu (show on hover) --}}
                                    <button @click.stop="$store.contextMenu.openContextMenu($event, 'song', {
                                            id: track.id,
                                            title: track.title,
                                            artist: track.artist,
                                            album_id: track.album_id,
                                            album_slug: track.album_slug,
                                            album_title: track.album_title,
                                            album_cover: track.album_cover,
                                            is_favorite: track.is_favorite || false
                                        })"
                                        class="w-7 h-7 hidden group-hover:flex items-center justify-center rounded-full hover:bg-white/10 text-gray-400 hover:text-white transition-all"
                                        title="Daha Fazla">
                                        <i class="fas fa-ellipsis-v text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Album/Genre/Sector Preview (Original Design) --}}
            <template x-if="!$store.sidebar.previewLoading && $store.sidebar.hasPreviewTracks && $store.sidebar.previewInfo?.type !== 'Playlist'">
                <div class="flex-1 overflow-y-auto bg-slate-900/50"
                     x-ref="previewScrollContainer"
                     @scroll="
                        if ($store.sidebar.canLoadMorePreviewTracks) {
                            const container = $refs.previewScrollContainer;
                            const scrolledToBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 200;
                            if (scrolledToBottom) {
                                $store.sidebar.loadMorePreviewTracks();
                            }
                        }
                     ">
                    <template x-for="(track, index) in $store.sidebar.previewTracks" :key="track.id">
                        <div class="group flex items-center gap-3 px-4 py-2.5 hover:bg-white/5 cursor-pointer transition-all"
                             @click="$dispatch('play-song', { songId: track.id })"
                             x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', { id: track.id, title: track.title, artist: track.artist, album_id: track.album_id, album_slug: track.album_slug, album_title: track.album_title, album_cover: track.album_cover, is_favorite: track.is_favorite || false })">

                            {{-- Thumbnail with Play Overlay --}}
                            <div class="relative w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-gradient-to-br from-muzibu-coral to-orange-600">
                                <template x-if="track.cover">
                                    <img :src="track.cover" :alt="track.title" class="w-full h-full object-cover" loading="lazy">
                                </template>
                                <template x-if="!track.cover">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-music text-white/50 text-xs"></i>
                                    </div>
                                </template>
                                {{-- Play Overlay on Hover --}}
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                    <i class="fas fa-play text-white text-sm"></i>
                                </div>
                            </div>

                            {{-- Song Info --}}
                            <div class="flex-1 min-w-0">
                                <h4 class="text-white text-sm font-medium truncate group-hover:text-muzibu-coral transition-colors"
                                    x-text="track.title"></h4>
                                <p class="text-gray-400 text-xs truncate" x-text="track.artist"></p>
                            </div>

                            {{-- Actions (Favorite + Time/3-Dot) - Fixed Width --}}
                            <div class="flex items-center gap-1 flex-shrink-0">
                                {{-- Favorite Button (Always Same Position) --}}
                                <button @click.stop="$store.favorites.toggle('song', track.id)"
                                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/10 transition-all"
                                        x-bind:class="$store.favorites.isFavorite('song', track.id) ? 'text-muzibu-coral opacity-100' : 'text-gray-400 opacity-0 group-hover:opacity-100'">
                                    <i class="text-sm"
                                       x-bind:class="$store.favorites.isFavorite('song', track.id) ? 'fas fa-heart' : 'far fa-heart'"></i>
                                </button>

                                {{-- Duration / 3-Dot Menu (Same Width, Toggle on Hover) --}}
                                <div class="w-8 h-8 flex items-center justify-center">
                                    {{-- Duration (Default State) --}}
                                    <div class="text-gray-500 text-xs group-hover:hidden" x-text="track.duration"></div>

                                    {{-- 3-Dot Menu Button (Hover State) --}}
                                    <button @click.stop="$store.contextMenu.openContextMenu($event, 'song', { id: track.id, title: track.title, artist: track.artist, album_id: track.album_id, album_slug: track.album_slug, album_title: track.album_title, album_cover: track.album_cover, is_favorite: track.is_favorite || false })"
                                            class="hidden group-hover:flex items-center justify-center w-full h-full rounded-full text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
                                        <i class="fas fa-ellipsis-v text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Load More Button (for infinite scroll up to 100) --}}
                    <template x-if="$store.sidebar.canLoadMorePreviewTracks">
                        <div class="px-4 py-4 border-t border-gray-800">
                            <button @click="$store.sidebar.loadMorePreviewTracks()"
                                    class="w-full py-2.5 bg-muzibu-gray hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-chevron-down text-xs"></i>
                                <span x-text="`Daha Fazla Yükle (${$store.sidebar.previewDisplayCount}/${Math.min(100, $store.sidebar.previewTotalCount)})`"></span>
                            </button>
                        </div>
                    </template>

                    {{-- Pagination (for 100+) --}}
                    <template x-if="$store.sidebar.showPreviewPagination && $store.sidebar.previewDisplayCount >= 100">
                        <div class="px-4 py-4 border-t border-gray-800">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs text-gray-400" x-text="`Toplam ${$store.sidebar.previewTotalCount} şarkı`"></span>
                                <span class="text-xs text-gray-400" x-text="`Sayfa ${$store.sidebar.previewCurrentPage} / ${$store.sidebar.previewTotalPages}`"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                {{-- Previous Button --}}
                                <button @click="$store.sidebar.loadPreviewPage($store.sidebar.previewCurrentPage - 1)"
                                        :disabled="$store.sidebar.previewCurrentPage === 1"
                                        class="px-3 py-2 bg-muzibu-gray hover:bg-gray-700 text-white text-sm rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </button>

                                {{-- Page Numbers --}}
                                <div class="flex-1 flex items-center justify-center gap-1">
                                    <template x-for="page in $store.sidebar.previewTotalPages" :key="page">
                                        <button @click="$store.sidebar.loadPreviewPage(page)"
                                                x-show="page === 1 || page === $store.sidebar.previewTotalPages || Math.abs(page - $store.sidebar.previewCurrentPage) <= 1"
                                                class="w-8 h-8 rounded-lg text-xs font-medium transition-colors"
                                                :class="page === $store.sidebar.previewCurrentPage ? 'bg-muzibu-coral text-white' : 'bg-muzibu-gray hover:bg-gray-700 text-gray-400'"
                                                x-text="page"></button>
                                    </template>
                                </div>

                                {{-- Next Button --}}
                                <button @click="$store.sidebar.loadPreviewPage($store.sidebar.previewCurrentPage + 1)"
                                        :disabled="$store.sidebar.previewCurrentPage === $store.sidebar.previewTotalPages"
                                        class="px-3 py-2 bg-muzibu-gray hover:bg-gray-700 text-white text-sm rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-chevron-right text-xs"></i>
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

    {{-- DETAIL PAGE: Premium Card Design (only when NOT in preview mode) --}}
    <template x-if="$store.sidebar?.isDetailPage && !$store.sidebar?.previewMode && $store.sidebar?.hasTracks">
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
                    <div class="group flex items-center gap-3 px-2 py-2.5 hover:bg-white/5 cursor-pointer transition-all"
                         @click="(($store.sidebar.isDetailPage && $store.sidebar.entityInfo) ? $store.player.setPlayContext({
                             type: $store.sidebar.pageType,
                             id: $store.sidebar.entityInfo.id,
                             name: $store.sidebar.entityInfo.title,
                             offset: index
                         }) : null), $dispatch('play-song', { songId: track.id })"
                         x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', { id: track.id, title: track.title, artist: track.artist, album_id: track.album_id, album_slug: track.album_slug, album_title: track.album_title, album_cover: track.album_cover, is_favorite: track.is_favorite || false })">

                        {{-- Thumbnail with Play Overlay --}}
                        <div class="relative w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-gradient-to-br from-muzibu-coral to-orange-600">
                            <template x-if="track.cover">
                                <img :src="track.cover" :alt="track.title" class="w-full h-full object-cover" loading="lazy">
                            </template>
                            <template x-if="!track.cover">
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-music text-white/50 text-xs"></i>
                                </div>
                            </template>
                            {{-- Play Overlay on Hover --}}
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                <i class="fas fa-play text-white text-sm"></i>
                            </div>
                        </div>

                        {{-- Song Info --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="text-white text-sm font-medium truncate group-hover:text-muzibu-coral transition-colors"
                                x-text="track.title"></h4>
                            <p class="text-gray-400 text-xs truncate" x-text="track.artist"></p>
                        </div>

                        {{-- Actions (Favorite + Time/3-Dot) - Fixed Width --}}
                        <div class="flex items-center gap-1 flex-shrink-0">
                            {{-- Favorite Button (Always Same Position) --}}
                            <button @click.stop="$store.favorites.toggle('song', track.id)"
                                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/10 transition-all"
                                    x-bind:class="$store.favorites.isFavorite('song', track.id) ? 'text-muzibu-coral opacity-100' : 'text-gray-400 opacity-0 group-hover:opacity-100'">
                                <i class="text-sm"
                                   x-bind:class="$store.favorites.isFavorite('song', track.id) ? 'fas fa-heart' : 'far fa-heart'"></i>
                            </button>

                            {{-- Duration / 3-Dot Menu (Same Width, Toggle on Hover) --}}
                            <div class="w-8 h-8 flex items-center justify-center">
                                {{-- Duration (Default State) --}}
                                <div class="text-gray-500 text-xs group-hover:hidden" x-text="track.duration"></div>

                                {{-- 3-Dot Menu Button (Hover State) --}}
                                <button @click.stop="$store.contextMenu.openContextMenu($event, 'song', { id: track.id, title: track.title, artist: track.artist, album_id: track.album_id, album_slug: track.album_slug, album_title: track.album_title, album_cover: track.album_cover, is_favorite: track.is_favorite || false })"
                                        class="hidden group-hover:flex items-center justify-center w-full h-full rounded-full text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
                                    <i class="fas fa-ellipsis-v text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- HOMEPAGE: Tab System with Songs (v6 Design - when NOT in preview/detail) --}}
    {{-- Default visible, hidden when preview or detail page --}}
    <div x-show="!$store.sidebar?.previewMode && !$store.sidebar?.isDetailPage"
         x-transition:enter="transition ease-out duration-200"
         x-transition:leave="transition ease-in duration-150"
         class="h-full flex flex-col">

            {{-- Premium Header with Tabs --}}
            <div class="relative rounded-t-lg overflow-hidden">
                {{-- Gradient Background Layer (Fixed Height, Behind Content) --}}
                <div class="absolute top-0 left-0 right-0 h-[120px] rounded-t-lg pointer-events-none overflow-hidden">
                    {{-- Color Layer (Dynamic based on active tab) --}}
                    <div class="absolute top-0 left-0 right-0 bottom-0 bg-gradient-to-b from-muzibu-coral/40 via-muzibu-coral/20 to-transparent"
                         x-show="songsTab === 'new'"></div>
                    <div class="absolute top-0 left-0 right-0 bottom-0 bg-gradient-to-b from-orange-500/40 via-orange-500/20 to-transparent"
                         x-show="songsTab === 'popular'"></div>
                    {{-- Dark Overlay (Bottom fade to #121212) --}}
                    <div class="absolute top-0 left-0 right-0 bottom-0 bg-gradient-to-b from-transparent via-black/30 to-[#121212]"></div>
                </div>

                {{-- Content (Above Gradient) --}}
                <div class="relative z-10 pt-3 pb-2">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 px-3">
                        <template x-if="songsTab === 'popular'">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-fire text-orange-400"></i>
                                {{ trans('muzibu::front.general.popular_songs') }}
                            </span>
                        </template>
                        <template x-if="songsTab === 'new'">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-star text-muzibu-coral"></i>
                                {{ trans('muzibu::front.general.new_songs') }}
                            </span>
                        </template>
                    </h3>
                    <p class="text-xs text-gray-400 mt-1 px-3">
                        <template x-if="songsTab === 'new'">
                            <span>{{ trans('muzibu::front.sidebar.newly_added') }}</span>
                        </template>
                        <template x-if="songsTab === 'popular'">
                            <span>{{ trans('muzibu::front.sidebar.most_played') }}</span>
                        </template>
                    </p>

                    {{-- Tab Headers (2 tabs) - Pill Style: Yeni → Popüler --}}
                    <div class="flex gap-2 pt-3 px-3">
                        <button @click="songsTab = 'new'"
                                class="flex-1 py-2 px-3 text-xs font-medium transition-all rounded-full"
                                :class="songsTab === 'new' ? 'bg-muzibu-coral/20 text-muzibu-coral font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5'">
                            <i class="fas fa-star mr-1"></i>{{ trans('muzibu::front.sidebar.new') }}
                        </button>
                        <button @click="songsTab = 'popular'"
                                class="flex-1 py-2 px-3 text-xs font-medium transition-all rounded-full"
                                :class="songsTab === 'popular' ? 'bg-orange-500/20 text-orange-400 font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5'">
                            <i class="fas fa-fire mr-1"></i>{{ trans('muzibu::front.sidebar.popular') }}
                        </button>
                    </div>
                </div>
            </div>

            {{-- Song Lists --}}
            <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent px-2 pt-1 pb-2 space-y-0.5">

                {{-- POPULAR SONGS TAB --}}
                <template x-if="songsTab === 'popular'">
                    <div>
                        @if(isset($popularSongs) && count($popularSongs) > 0)
                            @foreach($popularSongs->take(15) as $index => $song)
                                <x-muzibu.song-simple-row :song="$song" :index="$index" />
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
                    <div>
                        @if(isset($newSongs) && count($newSongs) > 0)
                            @foreach($newSongs->take(15) as $index => $song)
                                <x-muzibu.song-simple-row :song="$song" :index="$index" />
                            @endforeach
                        @elseif(isset($popularSongs) && count($popularSongs) > 10)
                            {{-- Fallback: Use slice of popularSongs if newSongs not available --}}
                            @foreach($popularSongs->slice(10)->take(15) as $index => $song)
                                <x-muzibu.song-simple-row :song="$song" :index="$index" />
                            @endforeach
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-star text-2xl mb-2"></i>
                                <p class="text-xs">{{ trans('muzibu::front.messages.no_songs_found') }}</p>
                            </div>
                        @endif
                    </div>
                </template>

            </div>
    </div>

    {{-- FALLBACK: Loading State (detail page without tracks, not in preview) --}}
    <template x-if="$store.sidebar?.isDetailPage && !$store.sidebar?.previewMode && !$store.sidebar?.hasTracks">
        <div class="h-full flex items-center justify-center">
            <div class="text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-muzibu-coral text-2xl mb-3"></i>
                <p class="text-xs">{{ trans('muzibu::front.sidebar.song_loading') }}</p>
            </div>
        </div>
    </template>
</div>
