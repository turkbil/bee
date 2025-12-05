{{-- Playlist Select Modal Component --}}
<div x-show="$store.contextMenu.playlistModal.open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div x-on:click="$store.contextMenu.playlistModal.open = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    <div class="relative bg-gray-800 rounded-lg shadow-2xl border border-white/20 max-w-2xl w-full p-6">
        {{-- Content Info Header --}}
        <div class="mb-6 pb-4 border-b border-white/20">
            <div class="flex items-center gap-3">
                <div :class="{
                    'bg-gradient-to-br from-pink-500 to-purple-600': $store.contextMenu.type === 'song',
                    'bg-gradient-to-br from-blue-500 to-cyan-600': $store.contextMenu.type === 'album'
                }" class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i :class="{
                        'fa-music': $store.contextMenu.type === 'song',
                        'fa-compact-disc': $store.contextMenu.type === 'album'
                    }" class="fas text-white text-xl opacity-80"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-white truncate" x-text="$store.contextMenu.data?.title"></h3>
                    <p class="text-sm text-gray-400 truncate" x-text="$store.contextMenu.data?.artist"></p>
                </div>
            </div>
        </div>

        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-list text-orange-500"></i>
            Hangi Playliste Eklemek İstersiniz?
        </h3>

        {{-- Create New --}}
        <button x-on:click="$store.contextMenu.createNewPlaylist()"
                class="w-full mb-4 px-4 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors font-semibold flex items-center justify-center gap-2">
            <i class="fas fa-plus"></i>
            Yeni Playlist Oluştur
        </button>

        {{-- Playlists (List View) --}}
        <div class="max-h-96 overflow-y-auto bg-gray-900 rounded-lg">
            <template x-for="playlist in $store.contextMenu.userPlaylists" :key="playlist.id">
                <button x-on:click="$store.contextMenu.addToPlaylist(playlist)"
                        class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-700 transition-colors text-left border-b border-white/5 last:border-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-list text-white text-sm opacity-80"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-white font-semibold truncate" x-text="playlist.title"></h4>
                        <p class="text-xs text-gray-400" x-text="playlist.song_count + ' şarkı'"></p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-500 text-xs"></i>
                </button>
            </template>
        </div>

        <button x-on:click="$store.contextMenu.playlistModal.open = false"
                class="mt-6 w-full px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
            Kapat
        </button>
    </div>
</div>
