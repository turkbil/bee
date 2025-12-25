@extends('themes.muzibu.layouts.app')

@section('content')
{{-- Playlist Edit Page Data --}}
<script>
window.playlistEditData = {
    playlistId: {{ $playlist->playlist_id }},
    title: {!! json_encode($playlist->title) !!},
    description: {!! json_encode($playlist->description ?? '') !!},
    isPublic: {{ $playlist->is_public ? 'true' : 'false' }},
    songs: {!! json_encode($playlist->songs->map(function($song) {
        return [
            'song_id' => $song->song_id,
            'title' => $song->title,
            'artist' => $song->album && $song->album->artist ? $song->album->artist->title : '',
            'cover' => $song->getCoverUrl(100, 100) ?? '',
            'position' => $song->pivot->position ?? 0
        ];
    })->values()->toArray()) !!}
};
</script>

<div class="px-6 py-8 max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Playlist Düzenle</h1>
            <p class="text-gray-400">{{ $playlist->title }}</p>
        </div>
        <a href="{{ route('muzibu.my-playlists') }}"
           class="px-6 py-3 bg-gray-700 text-white font-semibold rounded-full hover:bg-gray-600 transition-all">
            <i class="fas fa-arrow-left mr-2"></i>
            Geri Dön
        </a>
    </div>

    <div x-data="playlistEditor()" x-init="initEditor()">
        <div class="grid md:grid-cols-2 gap-8">
            {{-- Playlist Bilgileri --}}
            <div class="bg-muzibu-gray rounded-lg p-6">
                <h2 class="text-2xl font-bold text-white mb-6">Playlist Bilgileri</h2>

                <form @submit.prevent="savePlaylist">
                    {{-- Title --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Playlist Adı *
                        </label>
                        <input
                            type="text"
                            x-model="title"
                            required
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-muzibu-coral focus:border-transparent"
                        >
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Açıklama
                        </label>
                        <textarea
                            x-model="description"
                            rows="3"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-muzibu-coral focus:border-transparent resize-none"
                        ></textarea>
                    </div>

                    {{-- Public/Private Toggle --}}
                    <div class="mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" x-model="isPublic" class="sr-only">
                            <div :class="isPublic ? 'bg-muzibu-coral' : 'bg-gray-600'" class="relative w-14 h-7 rounded-full transition-colors">
                                <div :class="isPublic ? 'translate-x-7' : 'translate-x-1'" class="absolute top-1 left-0 w-5 h-5 bg-white rounded-full transition-transform"></div>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-300">
                                <span x-show="isPublic">Herkese Açık</span>
                                <span x-show="!isPublic">Gizli</span>
                            </span>
                        </label>
                    </div>

                    {{-- Save Button --}}
                    <button
                        type="submit"
                        :disabled="saving || !title.trim()"
                        :class="saving || !title.trim() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-opacity-90'"
                        class="w-full px-6 py-3 bg-muzibu-coral text-white font-semibold rounded-full transition-all flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-save" x-show="!saving"></i>
                        <i class="fas fa-spinner fa-spin" x-show="saving" x-cloak></i>
                        <span x-text="saving ? 'Kaydediliyor...' : 'Değişiklikleri Kaydet'"></span>
                    </button>
                </form>
            </div>

            {{-- Şarkı Listesi --}}
            <div class="bg-muzibu-gray rounded-lg p-6">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center justify-between">
                    <span>Şarkılar</span>
                    <span class="text-sm text-gray-400" x-text="songs.length + ' şarkı'"></span>
                </h2>

                <div x-show="songs.length === 0" class="text-center py-8 text-gray-400">
                    <i class="fas fa-music text-4xl mb-3"></i>
                    <p>Henüz şarkı eklemediniz</p>
                </div>

                <div x-show="songs.length > 0" x-ref="songList" class="space-y-2">
                    <template x-for="(song, index) in songs" :key="song.song_id">
                        <div class="flex items-center gap-3 p-3 bg-white/5 rounded-lg hover:bg-white/10 transition-all cursor-move group">
                            {{-- Drag Handle --}}
                            <div class="text-gray-400">
                                <i class="fas fa-grip-vertical"></i>
                            </div>

                            {{-- Cover --}}
                            <div class="w-12 h-12 bg-gradient-to-br from-muzibu-coral to-purple-600 rounded flex-shrink-0 overflow-hidden">
                                <img x-show="song.cover" :src="song.cover" :alt="song.title" class="w-full h-full object-cover">
                            </div>

                            {{-- Song Info --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="text-white font-medium truncate" x-text="song.title"></h3>
                                <p class="text-sm text-gray-400 truncate" x-text="song.artist"></p>
                            </div>

                            {{-- Remove Button --}}
                            <button
                                @click="removeSong(song.song_id)"
                                class="opacity-0 group-hover:opacity-100 text-red-500 hover:text-red-400 transition-all"
                                title="Şarkıyı Çıkar"
                            >
                                <i class="fas fa-times-circle text-xl"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <div x-show="songs.length > 0" class="mt-4 text-sm text-gray-400 text-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Şarkıları sürükleyerek sıralayabilirsiniz
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Sortable.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

{{-- Playlist Editor Component --}}
<script>
function playlistEditor() {
    return {
        playlistId: window.playlistEditData.playlistId,
        title: window.playlistEditData.title,
        description: window.playlistEditData.description,
        isPublic: window.playlistEditData.isPublic,
        songs: window.playlistEditData.songs,
        saving: false,

        initEditor() {
            // Initialize Sortable.js for drag & drop
            if (this.$refs.songList) {
                new Sortable(this.$refs.songList, {
                    animation: 150,
                    ghostClass: 'opacity-50',
                    onEnd: (evt) => {
                        const movedItem = this.songs[evt.oldIndex];
                        this.songs.splice(evt.oldIndex, 1);
                        this.songs.splice(evt.newIndex, 0, movedItem);
                        this.saveSongOrder();
                    }
                });
            }
        },

        async savePlaylist() {
            this.saving = true;

            try {
                const response = await fetch(`/api/muzibu/playlists/${this.playlistId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                    },
                    body: JSON.stringify({
                        title: this.title,
                        description: this.description,
                        is_public: this.isPublic
                    })
                });

                const data = await response.json();

                if (data.success) {
                    Alpine.store('toast').show('Playlist güncellendi', 'success');
                    setTimeout(() => window.location.href = '{{ route('muzibu.my-playlists') }}', 1000);
                } else {
                    Alpine.store('toast').show(data.message || 'Hata oluştu', 'error');
                    this.saving = false;
                }
            } catch (err) {
                console.error('Playlist save error:', err);
                Alpine.store('toast').show('Bağlantı hatası', 'error');
                this.saving = false;
            }
        },

        async removeSong(songId) {
            if (!confirm('Bu şarkıyı playlist\'ten çıkarmak istediğinize emin misiniz?')) return;

            try {
                const response = await fetch(`/api/muzibu/playlists/${this.playlistId}/remove-song/${songId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.songs = this.songs.filter(s => s.song_id !== songId);
                    Alpine.store('toast').show('Şarkı çıkarıldı', 'success');
                } else {
                    Alpine.store('toast').show(data.message || 'Hata oluştu', 'error');
                }
            } catch (err) {
                console.error('Song remove error:', err);
                Alpine.store('toast').show('Bağlantı hatası', 'error');
            }
        },

        async saveSongOrder() {
            const songPositions = this.songs.map((song, index) => ({
                song_id: song.song_id,
                position: index + 1
            }));

            try {
                const response = await fetch(`/api/muzibu/playlists/${this.playlistId}/reorder`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                    },
                    body: JSON.stringify({ song_positions: songPositions })
                });

                const data = await response.json();

                if (data.success) {
                    Alpine.store('toast').show('Sıralama kaydedildi', 'success');
                } else {
                    Alpine.store('toast').show(data.message || 'Hata oluştu', 'error');
                }
            } catch (err) {
                console.error('Reorder error:', err);
                Alpine.store('toast').show('Bağlantı hatası', 'error');
            }
        }
    };
}
</script>
@endsection
