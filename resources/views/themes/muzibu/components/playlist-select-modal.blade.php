{{-- 🎵 Playlist Select Modal Component - Global, SPA-safe --}}
{{-- $store.playlistModal kullanır (contextMenu'dan bağımsız) --}}
<template x-teleport="body">
    <div x-show="$store.playlistModal.open"
         x-cloak
         @keydown.escape.window="$store.playlistModal.hide()"
         class="fixed inset-0 z-[9999]">

        {{-- Backdrop --}}
        <div x-show="$store.playlistModal.open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="$store.playlistModal.hide()"
             class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        {{-- Desktop: Centered Modal --}}
        <div class="hidden md:flex items-center justify-center h-full p-4">
            <div x-show="$store.playlistModal.open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.stop
                 class="relative w-full max-w-sm bg-zinc-900 rounded-2xl border border-white/10 shadow-2xl overflow-hidden flex flex-col max-h-[80vh]">

                {{-- Header with Content Info --}}
                <div class="p-4 border-b border-white/10 bg-gradient-to-r from-muzibu-coral/20 to-transparent flex-shrink-0">
                    <div class="flex items-center gap-3">
                        {{-- Cover --}}
                        <div class="w-12 h-12 rounded-lg flex-shrink-0 overflow-hidden"
                             :class="$store.playlistModal.contentData?.cover_url ? '' : 'bg-gradient-to-br from-pink-500 to-purple-600'">
                            <template x-if="$store.playlistModal.contentData?.cover_url">
                                <img :src="$store.playlistModal.contentData.cover_url" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!$store.playlistModal.contentData?.cover_url">
                                <div class="w-full h-full flex items-center justify-center">
                                    <i :class="$store.playlistModal.contentType === 'album' ? 'fas fa-compact-disc' : 'fas fa-music'" class="text-white/60"></i>
                                </div>
                            </template>
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-white truncate text-sm" x-text="$store.playlistModal.contentData?.title || ($store.playlistModal.contentType === 'album' ? 'Albüm' : 'Şarkı')"></p>
                            <p class="text-xs text-zinc-400 truncate" x-text="$store.playlistModal.contentData?.artist || ''"></p>
                            <p x-show="$store.playlistModal.contentType === 'album'" class="text-[10px] text-orange-400 mt-0.5">
                                <i class="fas fa-info-circle mr-1"></i>Tüm şarkılar eklenecek
                            </p>
                        </div>
                        {{-- Close --}}
                        <button @click="$store.playlistModal.hide()"
                                class="w-8 h-8 flex items-center justify-center text-zinc-500 hover:text-white rounded-full hover:bg-white/10 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                {{-- Search (only if 5+ playlists) --}}
                <template x-if="$store.playlistModal.userPlaylists && $store.playlistModal.userPlaylists.length >= 5">
                    <div class="p-3 border-b border-white/5 flex-shrink-0">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-sm"></i>
                            <input type="text"
                                   x-model="$store.playlistModal.searchQuery"
                                   placeholder="Playlist ara..."
                                   class="w-full pl-10 pr-4 py-2.5 bg-zinc-800 border border-zinc-700 rounded-xl text-white text-sm placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-muzibu-coral/50 focus:border-transparent">
                        </div>
                    </div>
                </template>

                {{-- Create New Playlist --}}
                <div class="p-3 border-b border-white/5 flex-shrink-0">
                    <button @click="$store.playlistModal.createNewPlaylist()"
                            class="w-full flex items-center gap-3 p-3 bg-white/5 hover:bg-white/10 rounded-xl transition group">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <span class="font-medium text-white">Yeni Playlist Oluştur</span>
                        <i class="fas fa-chevron-right text-zinc-600 ml-auto group-hover:text-white transition"></i>
                    </button>
                </div>

                {{-- Loading State --}}
                <template x-if="$store.playlistModal.loading">
                    <div class="flex-1 flex items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-2 border-white/20 border-t-muzibu-coral"></div>
                    </div>
                </template>

                {{-- Playlist List --}}
                <template x-if="!$store.playlistModal.loading">
                    <div class="flex-1 overflow-y-auto scrollbar-thin">
                        <p class="px-4 py-2 text-xs text-zinc-500 uppercase tracking-wider">Playlistlerim</p>

                        <template x-if="!$store.playlistModal.userPlaylists || $store.playlistModal.userPlaylists.length === 0">
                            <div class="px-4 pb-4 text-center">
                                <p class="text-zinc-500 text-sm py-4">Henüz playlist'iniz yok</p>
                            </div>
                        </template>

                        <div class="px-3 pb-3 space-y-1">
                            <template x-for="playlist in $store.playlistModal.filteredPlaylists" :key="playlist.playlist_id">
                                <button @click="$store.playlistModal.toggleSelection(playlist.playlist_id)"
                                        class="w-full flex items-center gap-3 p-2.5 rounded-xl transition group"
                                        :class="{
                                            'bg-green-500/10 border border-green-500/30': $store.playlistModal.isInPlaylist(playlist.playlist_id),
                                            'bg-blue-500/10 border border-blue-500/30': $store.playlistModal.isSelected(playlist.playlist_id) && !$store.playlistModal.isInPlaylist(playlist.playlist_id),
                                            'hover:bg-white/5': !$store.playlistModal.isInPlaylist(playlist.playlist_id) && !$store.playlistModal.isSelected(playlist.playlist_id)
                                        }"
                                        :disabled="$store.playlistModal.isInPlaylist(playlist.playlist_id)">
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
                                               'text-green-400': $store.playlistModal.isInPlaylist(playlist.playlist_id),
                                               'text-blue-400': $store.playlistModal.isSelected(playlist.playlist_id) && !$store.playlistModal.isInPlaylist(playlist.playlist_id),
                                               'text-zinc-500': !$store.playlistModal.isInPlaylist(playlist.playlist_id) && !$store.playlistModal.isSelected(playlist.playlist_id)
                                           }">
                                            <span x-show="$store.playlistModal.isInPlaylist(playlist.playlist_id)">Bu playlist'te mevcut ✓</span>
                                            <span x-show="$store.playlistModal.isSelected(playlist.playlist_id) && !$store.playlistModal.isInPlaylist(playlist.playlist_id)">Seçildi</span>
                                            <span x-show="!$store.playlistModal.isInPlaylist(playlist.playlist_id) && !$store.playlistModal.isSelected(playlist.playlist_id)" x-text="(playlist.song_count || 0) + ' şarkı'"></span>
                                        </p>
                                    </div>

                                    {{-- Checkbox --}}
                                    <div class="w-6 h-6 rounded flex items-center justify-center transition"
                                         :class="{
                                             'bg-green-500': $store.playlistModal.isInPlaylist(playlist.playlist_id),
                                             'bg-blue-500': $store.playlistModal.isSelected(playlist.playlist_id) && !$store.playlistModal.isInPlaylist(playlist.playlist_id),
                                             'border-2 border-zinc-600 group-hover:border-zinc-400': !$store.playlistModal.isInPlaylist(playlist.playlist_id) && !$store.playlistModal.isSelected(playlist.playlist_id)
                                         }">
                                        <i x-show="$store.playlistModal.isInPlaylist(playlist.playlist_id) || $store.playlistModal.isSelected(playlist.playlist_id)" class="fas fa-check text-white text-xs"></i>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Footer with Add Button --}}
                <div class="p-3 border-t border-white/10 flex-shrink-0 bg-zinc-900/80 backdrop-blur">
                    <button @click="$store.playlistModal.addToSelected()"
                            :disabled="!$store.playlistModal.hasSelection || $store.playlistModal.adding"
                            class="w-full py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2"
                            :class="$store.playlistModal.hasSelection && !$store.playlistModal.adding ? 'bg-muzibu-coral hover:bg-red-500 text-white' : 'bg-zinc-800 text-zinc-500 cursor-not-allowed'">
                        <template x-if="$store.playlistModal.adding">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-white/20 border-t-white"></div>
                        </template>
                        <template x-if="!$store.playlistModal.adding">
                            <i class="fas fa-plus"></i>
                        </template>
                        <span x-text="$store.playlistModal.adding ? 'Ekleniyor...' : ($store.playlistModal.hasSelection ? $store.playlistModal.selectedPlaylists.length + ' Playlist\'e Ekle' : 'Playlist Seçin')"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile: Bottom Sheet --}}
        <div class="md:hidden absolute inset-x-0 bottom-0"
             x-show="$store.playlistModal.open"
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

                {{-- Content Info --}}
                <div class="px-4 pb-3 flex items-center gap-3 flex-shrink-0">
                    <div class="w-14 h-14 rounded-xl flex-shrink-0 overflow-hidden"
                         :class="$store.playlistModal.contentData?.cover_url ? '' : 'bg-gradient-to-br from-pink-500 to-purple-600'">
                        <template x-if="$store.playlistModal.contentData?.cover_url">
                            <img :src="$store.playlistModal.contentData.cover_url" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!$store.playlistModal.contentData?.cover_url">
                            <div class="w-full h-full flex items-center justify-center">
                                <i :class="$store.playlistModal.contentType === 'album' ? 'fas fa-compact-disc' : 'fas fa-music'" class="text-white/60 text-xl"></i>
                            </div>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-white truncate" x-text="$store.playlistModal.contentData?.title || ($store.playlistModal.contentType === 'album' ? 'Albüm' : 'Şarkı')"></p>
                        <p class="text-sm text-zinc-400 truncate" x-text="$store.playlistModal.contentData?.artist || ''"></p>
                        <p x-show="$store.playlistModal.contentType === 'album'" class="text-xs text-orange-400 mt-0.5">
                            <i class="fas fa-info-circle mr-1"></i>Tüm şarkılar eklenecek
                        </p>
                    </div>
                </div>

                {{-- Search (only if 5+ playlists) --}}
                <template x-if="$store.playlistModal.userPlaylists && $store.playlistModal.userPlaylists.length >= 5">
                    <div class="px-4 pb-3 flex-shrink-0">
                        <div class="relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-500"></i>
                            <input type="text"
                                   x-model="$store.playlistModal.searchQuery"
                                   placeholder="Playlist ara..."
                                   class="w-full pl-12 pr-4 py-3 bg-zinc-800 border border-zinc-700 rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-muzibu-coral/50">
                        </div>
                    </div>
                </template>

                {{-- Create New --}}
                <div class="px-4 pb-2 flex-shrink-0">
                    <button @click="$store.playlistModal.createNewPlaylist()"
                            class="w-full flex items-center gap-3 p-3 bg-green-500/10 border border-green-500/30 rounded-xl">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <span class="font-medium text-green-400">Yeni Playlist Oluştur</span>
                    </button>
                </div>

                {{-- Loading State --}}
                <template x-if="$store.playlistModal.loading">
                    <div class="flex-1 flex items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-2 border-white/20 border-t-muzibu-coral"></div>
                    </div>
                </template>

                {{-- Playlists --}}
                <template x-if="!$store.playlistModal.loading">
                    <div class="px-4 overflow-y-auto flex-1 pb-2">
                        <p class="text-xs text-zinc-500 mb-2 uppercase tracking-wider">Playlistlerim</p>

                        <template x-if="!$store.playlistModal.userPlaylists || $store.playlistModal.userPlaylists.length === 0">
                            <div class="text-center py-6">
                                <p class="text-zinc-500 text-sm">Henüz playlist'iniz yok</p>
                            </div>
                        </template>

                        <div class="space-y-1">
                            <template x-for="playlist in $store.playlistModal.filteredPlaylists" :key="playlist.playlist_id">
                                <button @click="$store.playlistModal.toggleSelection(playlist.playlist_id)"
                                        class="w-full flex items-center gap-3 p-3 rounded-xl transition"
                                        :class="{
                                            'bg-green-500/10 border border-green-500/30': $store.playlistModal.isInPlaylist(playlist.playlist_id),
                                            'bg-blue-500/10 border border-blue-500/30': $store.playlistModal.isSelected(playlist.playlist_id) && !$store.playlistModal.isInPlaylist(playlist.playlist_id),
                                            'hover:bg-white/5': !$store.playlistModal.isInPlaylist(playlist.playlist_id) && !$store.playlistModal.isSelected(playlist.playlist_id)
                                        }"
                                        :disabled="$store.playlistModal.isInPlaylist(playlist.playlist_id)">
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
                                               'text-green-400': $store.playlistModal.isInPlaylist(playlist.playlist_id),
                                               'text-blue-400': $store.playlistModal.isSelected(playlist.playlist_id) && !$store.playlistModal.isInPlaylist(playlist.playlist_id),
                                               'text-zinc-500': !$store.playlistModal.isInPlaylist(playlist.playlist_id) && !$store.playlistModal.isSelected(playlist.playlist_id)
                                           }">
                                            <span x-show="$store.playlistModal.isInPlaylist(playlist.playlist_id)">Bu playlist'te mevcut ✓</span>
                                            <span x-show="$store.playlistModal.isSelected(playlist.playlist_id) && !$store.playlistModal.isInPlaylist(playlist.playlist_id)">Seçildi</span>
                                            <span x-show="!$store.playlistModal.isInPlaylist(playlist.playlist_id) && !$store.playlistModal.isSelected(playlist.playlist_id)" x-text="(playlist.song_count || 0) + ' şarkı'"></span>
                                        </p>
                                    </div>

                                    {{-- Checkbox --}}
                                    <div class="w-7 h-7 rounded flex items-center justify-center transition"
                                         :class="{
                                             'bg-green-500': $store.playlistModal.isInPlaylist(playlist.playlist_id),
                                             'bg-blue-500': $store.playlistModal.isSelected(playlist.playlist_id) && !$store.playlistModal.isInPlaylist(playlist.playlist_id),
                                             'border-2 border-zinc-600': !$store.playlistModal.isInPlaylist(playlist.playlist_id) && !$store.playlistModal.isSelected(playlist.playlist_id)
                                         }">
                                        <i x-show="$store.playlistModal.isInPlaylist(playlist.playlist_id) || $store.playlistModal.isSelected(playlist.playlist_id)" class="fas fa-check text-white text-sm"></i>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Footer with Add Button --}}
                <div class="px-4 py-4 border-t border-white/10 flex-shrink-0 bg-zinc-900/80 backdrop-blur safe-area-bottom">
                    <button @click="$store.playlistModal.addToSelected()"
                            :disabled="!$store.playlistModal.hasSelection || $store.playlistModal.adding"
                            class="w-full py-4 rounded-xl font-bold transition flex items-center justify-center gap-2 text-lg"
                            :class="$store.playlistModal.hasSelection && !$store.playlistModal.adding ? 'bg-muzibu-coral hover:bg-red-500 text-white' : 'bg-zinc-800 text-zinc-500 cursor-not-allowed'">
                        <template x-if="$store.playlistModal.adding">
                            <div class="animate-spin rounded-full h-5 w-5 border-2 border-white/20 border-t-white"></div>
                        </template>
                        <template x-if="!$store.playlistModal.adding">
                            <i class="fas fa-plus"></i>
                        </template>
                        <span x-text="$store.playlistModal.adding ? 'Ekleniyor...' : ($store.playlistModal.hasSelection ? $store.playlistModal.selectedPlaylists.length + ' Playlist\'e Ekle' : 'Playlist Seçin')"></span>
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
