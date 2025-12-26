@extends('themes.muzibu.layouts.app')

@section('content')
{{-- Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
</script>

<div class="px-6 py-8">
    {{-- Header - Alternatif 2: Icon + Text --}}
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-folder-plus text-3xl text-white"></i>
            </div>
            <div>
                <h1 class="text-5xl font-extrabold text-white mb-1">Playlistlerim</h1>
                <p class="text-gray-400 text-lg">Oluşturduğun tüm playlistler</p>
            </div>
        </div>

        {{-- Create Playlist Button --}}
        <button @click="$dispatch('open-create-playlist-modal')"
                class="inline-flex items-center gap-2 px-6 py-3 bg-muzibu-coral hover:bg-muzibu-coral/90 text-white font-semibold rounded-full transition-all transform hover:scale-105">
            <i class="fas fa-plus"></i>
            <span class="hidden sm:inline">Yeni Playlist</span>
        </button>
    </div>

    {{-- Playlists Grid --}}
    @if($playlists->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            @foreach($playlists as $playlist)
                <x-muzibu.my-playlist-card :playlist="$playlist" :preview="true" />
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($playlists->hasPages())
            <div class="mt-8">
                @include('themes.muzibu.partials.pagination', ['paginator' => $playlists])
            </div>
        @endif

    @else
        {{-- Empty State --}}
        <div class="text-center py-20 px-4">
            <div class="mb-8 inline-flex items-center justify-center w-32 h-32 rounded-full bg-gradient-to-br from-muzibu-coral/20 to-orange-600/20 border-2 border-muzibu-coral/40">
                <svg class="w-16 h-16 text-muzibu-coral" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                </svg>
            </div>

            <h3 class="text-2xl sm:text-3xl font-bold text-white mb-3">
                Henüz playlist oluşturmadın
            </h3>

            <p class="text-gray-400 text-lg mb-10 max-w-lg mx-auto">
                Favori şarkılarını bir araya getirerek kendi playlistlerini oluştur
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <button @click="$dispatch('open-create-playlist-modal')"
                        class="inline-flex items-center gap-3 px-8 py-4 bg-muzibu-coral hover:bg-muzibu-coral/90 text-white font-bold rounded-full transition-all transform hover:scale-105 shadow-lg">
                    <i class="fas fa-plus text-xl"></i>
                    <span>İlk Playlist'ini Oluştur</span>
                </button>

                <a href="{{ route('muzibu.home') }}"
                   data-spa
                   class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-full transition-all">
                    <i class="fas fa-home"></i>
                    <span>Ana Sayfaya Dön</span>
                </a>
            </div>
        </div>
    @endif
</div>

{{-- Delete Confirmation Modal --}}
<div x-data="{
        open: false,
        playlistId: null,
        playlistTitle: '',
        deleting: false,

        init() {
            this.$watch('open', value => {
                document.body.style.overflow = value ? 'hidden' : '';
            });

            window.addEventListener('confirm-delete-playlist', (e) => {
                this.playlistId = e.detail.id;
                this.playlistTitle = e.detail.title;
                this.open = true;
            });
        },

        async deletePlaylist() {
            this.deleting = true;

            try {
                const response = await fetch(`/api/muzibu/playlists/${this.playlistId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                    }
                });

                const data = await response.json();

                if (data.success) {
                    if (window.Alpine?.store('toast')) {
                        window.Alpine.store('toast').show(data.message || 'Playlist silindi', 'success');
                    }
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    if (window.Alpine?.store('toast')) {
                        window.Alpine.store('toast').show(data.message || 'Hata oluştu', 'error');
                    } else {
                        alert(data.message || 'Hata oluştu');
                    }
                    this.deleting = false;
                }
            } catch (error) {
                console.error('Playlist delete error:', error);
                if (window.Alpine?.store('toast')) {
                    window.Alpine.store('toast').show('Bağlantı hatası', 'error');
                } else {
                    alert('Bağlantı hatası');
                }
                this.deleting = false;
            }
        },

        close() {
            if (!this.deleting) {
                this.open = false;
            }
        }
     }"
     x-show="open"
     x-cloak
     @keydown.escape.window="close()"
     @confirm-delete-playlist.window="playlistId = $event.detail.id; playlistTitle = $event.detail.title; open = true"
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
     style="display: none;">

    {{-- Backdrop --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()"
         class="absolute inset-0 bg-black/90 backdrop-blur-sm"></div>

    {{-- Modal --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop
         class="relative w-full max-w-md bg-gradient-to-br from-zinc-900 to-black rounded-2xl shadow-2xl border border-red-900/30 p-6">

        {{-- Close Button --}}
        <button @click="close()"
                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center text-white/60 hover:text-white hover:bg-white/10 rounded-full transition-all">
            <i class="fas fa-times"></i>
        </button>

        {{-- Icon --}}
        <div class="mb-4 flex justify-center">
            <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center">
                <i class="fas fa-trash-alt text-red-500 text-2xl"></i>
            </div>
        </div>

        {{-- Title --}}
        <h3 class="text-2xl font-bold text-white text-center mb-2">Playlist'i Sil</h3>

        {{-- Message --}}
        <p class="text-gray-400 text-center mb-6">
            <span class="font-semibold text-white" x-text="playlistTitle"></span> adlı playlist'i silmek istediğinize emin misiniz?
            <span class="block mt-2 text-red-400 text-sm">Bu işlem geri alınamaz!</span>
        </p>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="button"
                    @click="close()"
                    :disabled="deleting"
                    :class="deleting ? 'opacity-50 cursor-not-allowed' : 'hover:bg-white/20'"
                    class="flex-1 px-6 py-3 bg-white/10 text-white font-semibold rounded-full transition-all">
                İptal
            </button>
            <button type="button"
                    @click="deletePlaylist()"
                    :disabled="deleting"
                    :class="deleting ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-600'"
                    class="flex-1 px-6 py-3 bg-red-500 text-white font-semibold rounded-full transition-all flex items-center justify-center gap-2">
                <i class="fas fa-spinner fa-spin" x-show="deleting" x-cloak></i>
                <i class="fas fa-trash-alt" x-show="!deleting"></i>
                <span x-text="deleting ? 'Siliniyor...' : 'Sil'"></span>
            </button>
        </div>
    </div>
</div>

@endsection
