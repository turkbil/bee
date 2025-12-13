@extends('themes.muzibu.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Playlistlerim</h1>
            <p class="text-gray-400">Oluşturduğun tüm playlistler</p>
        </div>

        <!-- Create Playlist Button -->
        <button @click="$dispatch('open-create-playlist-modal')"
                class="inline-flex items-center px-6 py-3 bg-muzibu-coral text-white font-semibold rounded-full hover:bg-opacity-90 transition-all">
            <i class="fas fa-plus mr-2"></i>
            Yeni Playlist
        </button>
    </div>

    @if($playlists->count() > 0)
        <!-- Playlists Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 mb-8">
            @foreach($playlists as $playlist)
                <div class="group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-4 transition-all duration-300 cursor-pointer">
                    <a href="{{ route('muzibu.playlist.show', $playlist->getTranslation('slug', app()->getLocale())) }}">
                        <div class="relative mb-4">
                            @if($playlist->getFirstMedia('cover'))
                                <img src="{{ thumb($playlist->getFirstMedia('cover'), 300, 300, ['scale' => 1]) }}"
                                     alt="{{ $playlist->getTranslation('title', app()->getLocale()) }}"
                                     class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                     loading="lazy">
                            @else
                                <div class="w-full aspect-square bg-gradient-to-br from-muzibu-coral via-purple-600 to-blue-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-list-music text-white text-4xl opacity-50"></i>
                                </div>
                            @endif

                            <!-- Play Button Overlay -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                                <button class="opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:scale-110"
                                        @click.prevent="
                                            $store.player.setPlayContext({
                                                type: 'user_playlist',
                                                id: {{ $playlist->playlist_id }},
                                                name: '{{ addslashes($playlist->getTranslation('title', app()->getLocale())) }}'
                                            });
                                            $dispatch('play-playlist', { playlistId: {{ $playlist->playlist_id }} });
                                        ">
                                    <i class="fas fa-play ml-1"></i>
                                </button>
                            </div>

                            <!-- Favorite Button -->
                            <div class="absolute top-2 right-2" x-on:click.stop>
                                <x-common.favorite-button :model="$playlist" size="sm" iconOnly="true" />
                            </div>

                            <!-- Public/Private Badge -->
                            <div class="absolute bottom-2 left-2">
                                @if($playlist->is_public)
                                    <span class="inline-flex items-center px-2 py-1 bg-green-600 bg-opacity-80 text-white text-xs font-medium rounded-full">
                                        <i class="fas fa-globe-americas mr-1"></i>
                                        Herkese Açık
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 bg-gray-800 bg-opacity-80 text-white text-xs font-medium rounded-full">
                                        <i class="fas fa-lock mr-1"></i>
                                        Gizli
                                    </span>
                                @endif
                            </div>
                        </div>

                        <h3 class="font-semibold text-white mb-1 truncate">
                            {{ $playlist->getTranslation('title', app()->getLocale()) }}
                        </h3>

                        <p class="text-sm text-gray-400 truncate">
                            {{ $playlist->songs_count }} şarkı
                        </p>

                        @if($playlist->description)
                            <p class="text-xs text-gray-500 mt-1 truncate">
                                {{ $playlist->getTranslation('description', app()->getLocale()) }}
                            </p>
                        @endif
                    </a>

                    <!-- Playlist Actions Menu -->
                    <div class="mt-3 pt-3 border-t border-gray-700 flex items-center justify-between" x-on:click.stop>
                        <div class="flex items-center space-x-2">
                            <!-- Edit Button -->
                            <a href="{{ route('muzibu.playlist.edit', $playlist->playlist_id) }}"
                               class="text-gray-400 hover:text-muzibu-coral transition-colors"
                               title="Düzenle">
                                <i class="fas fa-edit"></i>
                            </a>

                            <!-- Delete Button -->
                            <button @click="if(confirm('Bu playlist\'i silmek istediğinize emin misiniz?')) {
                                        try {
                                            const token = localStorage.getItem('auth_token');
                                            fetch('/api/muzibu/playlists/{{ $playlist->playlist_id }}', {
                                                method: 'DELETE',
                                                headers: {
                                                    'Authorization': 'Bearer ' + (token || ''),
                                                    'Accept': 'application/json'
                                                }
                                            }).then(response => response.json())
                                              .then(data => {
                                                  if(data.success) {
                                                      window.location.reload();
                                                  } else {
                                                      alert(data.message || 'Bir hata oluştu');
                                                  }
                                              });
                                        } catch (e) {
                                            alert('Storage erişim hatası');
                                        }
                                    }"
                                    class="text-gray-400 hover:text-red-500 transition-colors"
                                    title="Sil">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>

                        <!-- Share Button (if public) -->
                        @if($playlist->is_public)
                            <button @click="navigator.clipboard.writeText('{{ route('muzibu.playlist.show', $playlist->getTranslation('slug', app()->getLocale())) }}');
                                           alert('Link kopyalandı!');"
                                    class="text-gray-400 hover:text-muzibu-coral transition-colors"
                                    title="Paylaş">
                                <i class="fas fa-share-alt"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($playlists->hasPages())
            <div class="mt-8">
                {{ $playlists->links() }}
            </div>
        @endif

    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="mb-6">
                <i class="fas fa-list-music text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Henüz playlist oluşturmadın</h3>
            <p class="text-gray-400 mb-6">Favori şarkılarını bir araya getirerek kendi playlistlerini oluşturabilirsin</p>

            <div class="flex items-center justify-center space-x-4">
                <button @click="$dispatch('open-create-playlist-modal')"
                        class="inline-flex items-center px-6 py-3 bg-muzibu-coral text-white font-semibold rounded-full hover:bg-opacity-90 transition-all">
                    <i class="fas fa-plus mr-2"></i>
                    İlk Playlist'ini Oluştur
                </button>

                <a href="{{ route('muzibu.home') }}"
                   class="inline-flex items-center px-6 py-3 bg-gray-700 text-white font-semibold rounded-full hover:bg-gray-600 transition-all">
                    <i class="fas fa-home mr-2"></i>
                    Ana Sayfaya Dön
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Include Create Playlist Modal -->
<x-muzibu.create-playlist-modal />
@endsection
