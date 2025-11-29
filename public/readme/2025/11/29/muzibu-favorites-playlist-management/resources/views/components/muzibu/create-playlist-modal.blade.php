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
                <h3 class="text-xl font-bold text-white">Yeni Playlist Oluştur</h3>
                <button @click="$wire.open = false" class="text-spotify-text-gray hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Form -->
            <div x-data="{
                title: '',
                description: '',
                loading: false,
                async createPlaylist() {
                    if (!this.title.trim()) {
                        alert('Playlist adı gerekli!');
                        return;
                    }

                    this.loading = true;
                    try {
                        const response = await fetch('/api/muzibu/playlists/quick-create', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer ' + localStorage.getItem('auth_token')',
                            },
                            body: JSON.stringify({
                                title: { tr: this.title },
                                slug: { tr: this.title.toLowerCase().replace(/\\s+/g, '-') },
                                description: this.description ? { tr: this.description } : null,
                                song_ids: [{{ $songId }}]
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            alert('✅ Playlist oluşturuldu!');
                            this.title = '';
                            this.description = '';
                            $wire.open = false;
                        } else {
                            alert('❌ ' + data.message);
                        }
                    } catch (error) {
                        alert('❌ Bir hata oluştu');
                    }
                    this.loading = false;
                }
            }">
                <form @submit.prevent="createPlaylist">
                    <!-- Title -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-spotify-text-gray mb-2">
                            Playlist Adı *
                        </label>
                        <input 
                            x-model="title"
                            type="text" 
                            class="w-full px-4 py-3 bg-spotify-dark border border-spotify-gray-light rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-spotify-green"
                            placeholder="Örn: Çalışırken Dinlenecekler"
                            required>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-spotify-text-gray mb-2">
                            Açıklama (Opsiyonel)
                        </label>
                        <textarea 
                            x-model="description"
                            rows="3"
                            class="w-full px-4 py-3 bg-spotify-dark border border-spotify-gray-light rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-spotify-green"
                            placeholder="Playlist hakkında kısa bir açıklama..."></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                :disabled="loading"
                                class="flex-1 bg-spotify-green hover:bg-spotify-green/90 text-black font-bold py-3 px-6 rounded-full transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!loading">Oluştur</span>
                            <span x-show="loading">
                                <i class="fas fa-spinner fa-spin"></i> Oluşturuluyor...
                            </span>
                        </button>
                        <button type="button"
                                @click="$wire.open = false"
                                class="px-6 py-3 border border-spotify-gray-light rounded-full text-white hover:bg-spotify-gray-light transition-colors">
                            İptal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
