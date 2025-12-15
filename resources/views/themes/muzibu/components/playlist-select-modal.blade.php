{{-- Playlist Select Modal Component - Çoklu Seçim + Checkbox --}}
<template x-teleport="body">
    <div x-show="$store.contextMenu.playlistModal.open"
         x-cloak
         @keydown.escape.window="$store.contextMenu.playlistModal.open = false"
         class="fixed inset-0 z-[9999]"
         x-data="{
            selectedPlaylists: [],
            isInPlaylist(playlistId) {
                return $store.contextMenu.songExistsInPlaylists?.includes(playlistId) || false;
            },
            isSelected(playlistId) {
                return this.selectedPlaylists.includes(playlistId);
            },
            togglePlaylist(playlistId) {
                if (this.isInPlaylist(playlistId)) return; // Zaten varsa toggle yapma
                const idx = this.selectedPlaylists.indexOf(playlistId);
                if (idx > -1) {
                    this.selectedPlaylists.splice(idx, 1);
                } else {
                    this.selectedPlaylists.push(playlistId);
                }
            },
            async addToSelected() {
                if (this.selectedPlaylists.length === 0) return;
                await $store.contextMenu.addToMultiplePlaylists(this.selectedPlaylists);
                this.selectedPlaylists = [];
            },
            get hasSelection() {
                return this.selectedPlaylists.length > 0;
            }
         }"
         x-init="$watch('$store.contextMenu.playlistModal.open', (open) => { if(!open) selectedPlaylists = []; })">

        {{-- Backdrop --}}
        <div x-show="$store.contextMenu.playlistModal.open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="$store.contextMenu.playlistModal.open = false"
             class="absolute inset-0 bg-transparent backdrop-blur-sm"></div>

        {{-- Desktop: Centered Modal --}}
        <div class="hidden md:flex items-center justify-center h-full p-4">
            <div x-show="$store.contextMenu.playlistModal.open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.stop
                 class="relative w-full max-w-sm bg-zinc-900 rounded-2xl border border-white/10 shadow-2xl overflow-hidden flex flex-col max-h-[80vh]">

                {{-- Header with Song Info --}}
                <div class="p-4 border-b border-white/10 bg-gradient-to-r from-muzibu-coral/20 to-transparent flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg flex-shrink-0 overflow-hidden"
                             :class="$store.contextMenu.data?.cover_url ? '' : 'bg-gradient-to-br from-pink-500 to-purple-600'">
                            <template x-if="$store.contextMenu.data?.cover_url">
                                <img :src="$store.contextMenu.data.cover_url" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!$store.contextMenu.data?.cover_url">
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-music text-white/60"></i>
                                </div>
                            </template>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-white truncate text-sm" x-text="$store.contextMenu.data?.title || 'Şarkı'"></p>
                            <p class="text-xs text-zinc-400 truncate" x-text="$store.contextMenu.data?.artist || ''"></p>
                        </div>
                        <button @click="$store.contextMenu.playlistModal.open = false"
                                class="w-8 h-8 flex items-center justify-center text-zinc-500 hover:text-white rounded-full hover:bg-white/10 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                {{-- Search (only if 5+ playlists) --}}
                <template x-if="$store.contextMenu.userPlaylists && $store.contextMenu.userPlaylists.length >= 5">
                    <div class="p-3 border-b border-white/5 flex-shrink-0">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-sm"></i>
                            <input type="text"
                                   x-model="$store.contextMenu.playlistSearchQuery"
                                   placeholder="Playlist ara..."
                                   class="w-full pl-10 pr-4 py-2.5 bg-zinc-800 border border-zinc-700 rounded-xl text-white text-sm placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-muzibu-coral/50 focus:border-transparent">
                        </div>
                    </div>
                </template>

                {{-- Create New Playlist --}}
                <div class="p-3 border-b border-white/5 flex-shrink-0">
                    <button @click="$store.contextMenu.playlistModal.open = false; $dispatch('open-create-playlist-modal')"
                            class="w-full flex items-center gap-3 p-3 bg-white/5 hover:bg-white/10 rounded-xl transition group">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <span class="font-medium text-white">Yeni Playlist Oluştur</span>
                        <i class="fas fa-chevron-right text-zinc-600 ml-auto group-hover:text-white transition"></i>
                    </button>
                </div>

                {{-- Playlist List --}}
                <div class="flex-1 overflow-y-auto scrollbar-thin">
                    <p class="px-4 py-2 text-xs text-zinc-500 uppercase tracking-wider">Playlistlerim</p>

                    <template x-if="!$store.contextMenu.userPlaylists || $store.contextMenu.userPlaylists.length === 0">
                        <div class="px-4 pb-4 text-center">
                            <p class="text-zinc-500 text-sm py-4">Henüz playlist'iniz yok</p>
                        </div>
                    </template>

                    <div class="px-3 pb-3 space-y-1">
                        <template x-for="playlist in $store.contextMenu.filteredPlaylists" :key="playlist.playlist_id">
                            <button @click="togglePlaylist(playlist.playlist_id)"
                                    class="w-full flex items-center gap-3 p-2.5 rounded-xl transition group"
                                    :class="{
                                        'bg-green-500/10 border border-green-500/30': isInPlaylist(playlist.playlist_id),
                                        'bg-blue-500/10 border border-blue-500/30': isSelected(playlist.playlist_id) && !isInPlaylist(playlist.playlist_id),
                                        'hover:bg-white/5': !isInPlaylist(playlist.playlist_id) && !isSelected(playlist.playlist_id)
                                    }"
                                    :disabled="isInPlaylist(playlist.playlist_id)">
                                {{-- Playlist Cover --}}
                                <div class="w-10 h-10 rounded-lg flex-shrink-0 overflow-hidden"
                                     :class="playlist.cover_url ? '' : 'bg-gradient-to-br from-purple-500 to-pink-600'">
                                    <template x-if="playlist.cover_url">
                                        <img :src="playlist.cover_url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!playlist.cover_url">
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-list text-white/60 text-sm"></i>
                                        </div>
                                    </template>
                                </div>

                                {{-- Playlist Info --}}
                                <div class="flex-1 text-left min-w-0">
                                    <p class="font-medium text-white truncate text-sm" x-text="playlist.title"></p>
                                    <p class="text-xs"
                                       :class="{
                                           'text-green-400': isInPlaylist(playlist.playlist_id),
                                           'text-blue-400': isSelected(playlist.playlist_id) && !isInPlaylist(playlist.playlist_id),
                                           'text-zinc-500': !isInPlaylist(playlist.playlist_id) && !isSelected(playlist.playlist_id)
                                       }">
                                        <span x-show="isInPlaylist(playlist.playlist_id)">Bu playlist'te mevcut ✓</span>
                                        <span x-show="isSelected(playlist.playlist_id) && !isInPlaylist(playlist.playlist_id)">Seçildi</span>
                                        <span x-show="!isInPlaylist(playlist.playlist_id) && !isSelected(playlist.playlist_id)" x-text="(playlist.song_count || 0) + ' şarkı'"></span>
                                    </p>
                                </div>

                                {{-- Checkbox --}}
                                <div class="w-6 h-6 rounded flex items-center justify-center transition"
                                     :class="{
                                         'bg-green-500': isInPlaylist(playlist.playlist_id),
                                         'bg-blue-500': isSelected(playlist.playlist_id) && !isInPlaylist(playlist.playlist_id),
                                         'border-2 border-zinc-600 group-hover:border-zinc-400': !isInPlaylist(playlist.playlist_id) && !isSelected(playlist.playlist_id)
                                     }">
                                    <i x-show="isInPlaylist(playlist.playlist_id) || isSelected(playlist.playlist_id)" class="fas fa-check text-white text-xs"></i>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Footer with Add Button --}}
                <div class="p-3 border-t border-white/10 flex-shrink-0 bg-zinc-900/80 backdrop-blur">
                    <button @click="addToSelected()"
                            :disabled="!hasSelection"
                            class="w-full py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2"
                            :class="hasSelection ? 'bg-muzibu-coral hover:bg-red-500 text-white' : 'bg-zinc-800 text-zinc-500 cursor-not-allowed'">
                        <i class="fas fa-plus"></i>
                        <span x-text="hasSelection ? selectedPlaylists.length + ' Playlist\'e Ekle' : 'Playlist Seçin'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile: Bottom Sheet --}}
        <div class="md:hidden absolute inset-x-0 bottom-0"
             x-show="$store.contextMenu.playlistModal.open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             @click.stop>

            <div class="bg-zinc-900 rounded-t-3xl border-t border-white/10 max-h-[85vh] flex flex-col">
                {{-- Handle --}}
                <div class="flex justify-center py-3 flex-shrink-0">
                    <div class="w-10 h-1 bg-zinc-700 rounded-full"></div>
                </div>

                {{-- Song Info --}}
                <div class="px-4 pb-3 flex items-center gap-3 flex-shrink-0">
                    <div class="w-14 h-14 rounded-xl flex-shrink-0 overflow-hidden"
                         :class="$store.contextMenu.data?.cover_url ? '' : 'bg-gradient-to-br from-pink-500 to-purple-600'">
                        <template x-if="$store.contextMenu.data?.cover_url">
                            <img :src="$store.contextMenu.data.cover_url" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!$store.contextMenu.data?.cover_url">
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-music text-white/60 text-xl"></i>
                            </div>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-white truncate" x-text="$store.contextMenu.data?.title || 'Şarkı'"></p>
                        <p class="text-sm text-zinc-400 truncate" x-text="$store.contextMenu.data?.artist || ''"></p>
                    </div>
                </div>

                {{-- Search (only if 5+ playlists) --}}
                <template x-if="$store.contextMenu.userPlaylists && $store.contextMenu.userPlaylists.length >= 5">
                    <div class="px-4 pb-3 flex-shrink-0">
                        <div class="relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-500"></i>
                            <input type="text"
                                   x-model="$store.contextMenu.playlistSearchQuery"
                                   placeholder="Playlist ara..."
                                   class="w-full pl-12 pr-4 py-3 bg-zinc-800 border border-zinc-700 rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-muzibu-coral/50">
                        </div>
                    </div>
                </template>

                {{-- Create New --}}
                <div class="px-4 pb-2 flex-shrink-0">
                    <button @click="$store.contextMenu.playlistModal.open = false; $dispatch('open-create-playlist-modal')"
                            class="w-full flex items-center gap-3 p-3 bg-green-500/10 border border-green-500/30 rounded-xl">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <span class="font-medium text-green-400">Yeni Playlist Oluştur</span>
                    </button>
                </div>

                {{-- Playlists --}}
                <div class="px-4 overflow-y-auto flex-1 pb-2">
                    <p class="text-xs text-zinc-500 mb-2 uppercase tracking-wider">Playlistlerim</p>

                    <template x-if="!$store.contextMenu.userPlaylists || $store.contextMenu.userPlaylists.length === 0">
                        <div class="text-center py-6">
                            <p class="text-zinc-500 text-sm">Henüz playlist'iniz yok</p>
                        </div>
                    </template>

                    <div class="space-y-1">
                        <template x-for="playlist in $store.contextMenu.filteredPlaylists" :key="playlist.playlist_id">
                            <button @click="togglePlaylist(playlist.playlist_id)"
                                    class="w-full flex items-center gap-3 p-3 rounded-xl transition"
                                    :class="{
                                        'bg-green-500/10 border border-green-500/30': isInPlaylist(playlist.playlist_id),
                                        'bg-blue-500/10 border border-blue-500/30': isSelected(playlist.playlist_id) && !isInPlaylist(playlist.playlist_id),
                                        'hover:bg-white/5': !isInPlaylist(playlist.playlist_id) && !isSelected(playlist.playlist_id)
                                    }"
                                    :disabled="isInPlaylist(playlist.playlist_id)">
                                {{-- Playlist Cover --}}
                                <div class="w-12 h-12 rounded-lg flex-shrink-0 overflow-hidden"
                                     :class="playlist.cover_url ? '' : 'bg-gradient-to-br from-purple-500 to-pink-600'">
                                    <template x-if="playlist.cover_url">
                                        <img :src="playlist.cover_url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!playlist.cover_url">
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-list text-white/60"></i>
                                        </div>
                                    </template>
                                </div>

                                {{-- Playlist Info --}}
                                <div class="flex-1 text-left min-w-0">
                                    <p class="font-medium text-white truncate" x-text="playlist.title"></p>
                                    <p class="text-xs"
                                       :class="{
                                           'text-green-400': isInPlaylist(playlist.playlist_id),
                                           'text-blue-400': isSelected(playlist.playlist_id) && !isInPlaylist(playlist.playlist_id),
                                           'text-zinc-500': !isInPlaylist(playlist.playlist_id) && !isSelected(playlist.playlist_id)
                                       }">
                                        <span x-show="isInPlaylist(playlist.playlist_id)">Bu playlist'te mevcut ✓</span>
                                        <span x-show="isSelected(playlist.playlist_id) && !isInPlaylist(playlist.playlist_id)">Seçildi</span>
                                        <span x-show="!isInPlaylist(playlist.playlist_id) && !isSelected(playlist.playlist_id)" x-text="(playlist.song_count || 0) + ' şarkı'"></span>
                                    </p>
                                </div>

                                {{-- Checkbox --}}
                                <div class="w-7 h-7 rounded flex items-center justify-center transition"
                                     :class="{
                                         'bg-green-500': isInPlaylist(playlist.playlist_id),
                                         'bg-blue-500': isSelected(playlist.playlist_id) && !isInPlaylist(playlist.playlist_id),
                                         'border-2 border-zinc-600': !isInPlaylist(playlist.playlist_id) && !isSelected(playlist.playlist_id)
                                     }">
                                    <i x-show="isInPlaylist(playlist.playlist_id) || isSelected(playlist.playlist_id)" class="fas fa-check text-white text-sm"></i>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Footer with Add Button --}}
                <div class="px-4 py-4 border-t border-white/10 flex-shrink-0 bg-zinc-900/80 backdrop-blur safe-area-bottom">
                    <button @click="addToSelected()"
                            :disabled="!hasSelection"
                            class="w-full py-4 rounded-xl font-bold transition flex items-center justify-center gap-2 text-lg"
                            :class="hasSelection ? 'bg-muzibu-coral hover:bg-red-500 text-white' : 'bg-zinc-800 text-zinc-500 cursor-not-allowed'">
                        <i class="fas fa-plus"></i>
                        <span x-text="hasSelection ? selectedPlaylists.length + ' Playlist\'e Ekle' : 'Playlist Seçin'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Scrollbar styling */
.scrollbar-thin::-webkit-scrollbar { width: 4px; }
.scrollbar-thin::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 2px; }
.scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 2px; }
.scrollbar-thin::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }

/* Safe area for mobile bottom */
.safe-area-bottom { padding-bottom: max(1rem, env(safe-area-inset-bottom)); }
</style>
