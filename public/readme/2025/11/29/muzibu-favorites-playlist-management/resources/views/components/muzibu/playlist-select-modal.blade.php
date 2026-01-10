@props(['songId' => null])

<div x-show="$wire.open" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Backdrop -->
        <div @click="$wire.open = false" 
             class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal -->
        <div class="relative bg-spotify-gray rounded-xl shadow-2xl max-w-md w-full p-6 border border-spotify-gray-light">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white">Playlist Seç</h3>
                <button @click="$wire.open = false" class="text-spotify-text-gray hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Playlist List -->
            <div x-data="{ 
                playlists: [],
                loading: true,
                async loadPlaylists() {
                    this.loading = true;
                    try {
                        const response = await fetch('/api/muzibu/playlists/my-playlists', {
                            headers: {
                                'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                            }
                        });
                        const data = await response.json();
                        this.playlists = data.data || [];
                    } catch (error) {
                        console.error('Playlist yükleme hatası:', error);
                    }
                    this.loading = false;
                },
                async addToPlaylist(playlistId) {
                    try {
                        const response = await fetch(`/api/muzibu/playlists/${playlistId}/add-song`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                            },
                            body: JSON.stringify({ song_id: {{ $songId }} })
                        });
                        const data = await response.json();
                        if (data.success) {
                            alert('✅ Şarkı playlist'e eklendi!');
                            $wire.open = false;
                        } else {
                            alert('❌ ' + data.message);
                        }
                    } catch (error) {
                        alert('❌ Bir hata oluştu');
                    }
                }
            }" 
            x-init="loadPlaylists()">
                <!-- Loading -->
                <div x-show="loading" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-spotify-green"></i>
                </div>

                <!-- Empty State -->
                <div x-show="!loading && playlists.length === 0" class="text-center py-8">
                    <i class="fas fa-music text-4xl text-spotify-text-gray mb-4"></i>
                    <p class="text-spotify-text-gray">Henüz playlist oluşturmadınız</p>
                </div>

                <!-- Playlist List -->
                <div x-show="!loading && playlists.length > 0" class="space-y-2 max-h-96 overflow-y-auto">
                    <template x-for="playlist in playlists" :key="playlist.playlist_id">
                        <button @click="addToPlaylist(playlist.playlist_id)"
                                class="w-full text-left px-4 py-3 bg-spotify-dark hover:bg-spotify-gray-light rounded-lg transition-colors flex items-center justify-between group">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-list-music text-spotify-green"></i>
                                <div>
                                    <div class="text-white font-medium" x-text="playlist.title.tr || playlist.title"></div>
                                    <div class="text-sm text-spotify-text-gray" x-text="`${playlist.songs_count} şarkı`"></div>
                                </div>
                            </div>
                            <i class="fas fa-plus text-spotify-text-gray group-hover:text-spotify-green"></i>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
