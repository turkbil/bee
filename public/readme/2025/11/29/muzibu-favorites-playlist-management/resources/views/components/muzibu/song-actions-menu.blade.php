@props(['song' => null])

@if($song)
<div x-data="{ open: false, showPlaylistModal: false, showCreateModal: false }" @click.away="open = false" class="relative inline-block">
    <button @click="open = !open" class="text-spotify-text-gray hover:text-white transition-colors p-2">
        <i class="fas fa-ellipsis-v"></i>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" 
         x-transition
         class="absolute right-0 mt-2 w-56 bg-spotify-gray rounded-lg shadow-2xl border border-spotify-gray-light z-50"
         style="display: none;">
        
        <!-- Playlist'e Ekle -->
        <button @click="showPlaylistModal = true; open = false" 
                class="w-full text-left px-4 py-3 hover:bg-spotify-gray-light transition-colors flex items-center gap-3 text-white">
            <i class="fas fa-plus-circle text-spotify-green"></i>
            <span>Playlist'e Ekle</span>
        </button>

        <!-- Yeni Playlist Oluştur -->
        <button @click="showCreateModal = true; open = false"
                class="w-full text-left px-4 py-3 hover:bg-spotify-gray-light transition-colors flex items-center gap-3 text-white border-t border-spotify-gray-light">
            <i class="fas fa-star text-yellow-400"></i>
            <span>Yeni Playlist Oluştur</span>
        </button>

        <!-- Favorilere Ekle/Çıkar -->
        <button @click="toggleFavorite({{ $song->song_id }}); open = false"
                class="w-full text-left px-4 py-3 hover:bg-spotify-gray-light transition-colors flex items-center gap-3 text-white border-t border-spotify-gray-light">
            <i :class="isLiked({{ $song->song_id }}) ? 'fas fa-heart text-red-500' : 'far fa-heart text-spotify-text-gray'"></i>
            <span x-text="isLiked({{ $song->song_id }}) ? 'Favorilerden Çıkar' : 'Favorilere Ekle'"></span>
        </button>

        <!-- Kuyruk'a Ekle -->
        <button @click="addToQueue({{ $song->song_id }}); open = false"
                class="w-full text-left px-4 py-3 hover:bg-spotify-gray-light transition-colors flex items-center gap-3 text-white border-t border-spotify-gray-light rounded-b-lg">
            <i class="fas fa-list text-blue-400"></i>
            <span>Kuyruk'a Ekle</span>
        </button>
    </div>

    <!-- Playlist Seçim Modali -->
    <x-muzibu.playlist-select-modal :songId="$song->song_id" x-show="showPlaylistModal" @close="showPlaylistModal = false" />

    <!-- Playlist Oluşturma Modali -->
    <x-muzibu.create-playlist-modal :songId="$song->song_id" x-show="showCreateModal" @close="showCreateModal = false" />
</div>
@endif
