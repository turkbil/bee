@extends('themes.muzibu.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Favorilerim</h1>
        <p class="text-gray-400">Beğendiğin şarkılar, albümler ve playlistler</p>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-8 border-b border-gray-800">
        <nav class="flex space-x-8" x-data="{ activeTab: '{{ $type }}' }">
            <a href="{{ route('muzibu.favorites', ['type' => 'all']) }}"
               class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors"
               :class="activeTab === 'all' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                Tümü
            </a>
            <a href="{{ route('muzibu.favorites', ['type' => 'songs']) }}"
               class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors"
               :class="activeTab === 'songs' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                Şarkılar
            </a>
            <a href="{{ route('muzibu.favorites', ['type' => 'albums']) }}"
               class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors"
               :class="activeTab === 'albums' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                Albümler
            </a>
            <a href="{{ route('muzibu.favorites', ['type' => 'playlists']) }}"
               class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors"
               :class="activeTab === 'playlists' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                Playlistler
            </a>
        </nav>
    </div>

    @if($favorites->count() > 0)
        <!-- Favorites Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 mb-8">
            @foreach($favorites as $favorite)
                @php
                    $item = $favorite->favoritable;
                @endphp

                @if($item)
                    @if($item instanceof \Modules\Muzibu\App\Models\Song)
                        <x-muzibu.song-card :song="$item" />
                    @elseif($item instanceof \Modules\Muzibu\App\Models\Album)
                        <x-muzibu.album-card :album="$item" />
                    @elseif($item instanceof \Modules\Muzibu\App\Models\Playlist)
                        <x-muzibu.playlist-card :playlist="$item" />
                    @endif
                @endif
            @endforeach
        </div>

        <!-- Pagination -->
        @if($favorites->hasPages())
            <div class="mt-8">
                {{ $favorites->links() }}
            </div>
        @endif

    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="mb-6">
                <i class="fas fa-heart text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">
                @if($type === 'all')
                    Henüz favori eklemedin
                @elseif($type === 'songs')
                    Henüz favori şarkın yok
                @elseif($type === 'albums')
                    Henüz favori albümün yok
                @else
                    Henüz favori playlistin yok
                @endif
            </h3>
            <p class="text-gray-400 mb-6">Beğendiğin içerikleri favorilere ekleyerek kolayca ulaşabilirsin</p>
            <a href="{{ route('muzibu.home') }}" class="inline-flex items-center px-6 py-3 bg-muzibu-coral text-white font-semibold rounded-full hover:bg-opacity-90 transition-all">
                <i class="fas fa-home mr-2"></i>
                Ana Sayfaya Dön
            </a>
        </div>
    @endif
</div>
@endsection
