@extends('themes.muzibu.layouts.app')

@section('content')
{{-- 🎯 Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
</script>

<div class="px-6 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Favorilerim</h1>
        <p class="text-gray-400">Beğendiğin şarkılar, albümler ve playlistler</p>
    </div>

    {{-- Filter Tabs --}}
    <div class="mb-8 border-b border-gray-800">
        <nav class="flex space-x-8" x-data="{ activeTab: '{{ $type }}' }">
            <a href="{{ route('muzibu.favorites', ['type' => 'all']) }}"
               wire:navigate
               class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors"
               :class="activeTab === 'all' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                Tümü
            </a>
            <a href="{{ route('muzibu.favorites', ['type' => 'songs']) }}"
               wire:navigate
               class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors"
               :class="activeTab === 'songs' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                Şarkılar
            </a>
            <a href="{{ route('muzibu.favorites', ['type' => 'albums']) }}"
               wire:navigate
               class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors"
               :class="activeTab === 'albums' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                Albümler
            </a>
            <a href="{{ route('muzibu.favorites', ['type' => 'playlists']) }}"
               wire:navigate
               class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors"
               :class="activeTab === 'playlists' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                Playlistler
            </a>
        </nav>
    </div>

    @if($favorites->count() > 0)
        {{-- Favorites Grid - Modern Layout --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7 gap-4 mb-8">
            @foreach($favorites as $favorite)
                @php
                    $item = $favorite->favoritable;
                @endphp

                @if($item)
                    @if($item instanceof \Modules\Muzibu\App\Models\Song)
                        {{-- Song Card --}}
                        <div class="group relative">
                            <a href="{{ $item->getUrl() }}"
                               wire:navigate
                               class="block p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-white/10">
                                <div class="relative mb-3">
                                    <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl">
                                        @if($item->album && $item->album->getFirstMedia('album_cover'))
                                            <img src="{{ thumb($item->album->getFirstMedia('album_cover'), 200, 200, ['scale' => 1]) }}"
                                                 alt="{{ $item->getTranslation('title', app()->getLocale()) }}"
                                                 class="w-full h-full object-cover"
                                                 loading="lazy">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-muzibu-coral to-purple-600 flex items-center justify-center text-4xl">
                                                🎵
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <h3 class="font-semibold text-white truncate mb-1 text-sm">
                                    {{ $item->getTranslation('title', app()->getLocale()) }}
                                </h3>
                                @if($item->album && $item->album->artist)
                                    <p class="text-xs text-muzibu-text-gray truncate">
                                        {{ $item->album->artist->getTranslation('title', app()->getLocale()) }}
                                    </p>
                                @endif
                            </a>
                            {{-- Play button OUTSIDE <a> tag --}}
                            <button class="absolute w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-20 pointer-events-auto"
                                    style="bottom: calc(3rem + 0.75rem + 0.5rem); right: calc(0.75rem + 0.5rem);"
                                    @click="playSong({{ $item->song_id }})">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>

                    @elseif($item instanceof \Modules\Muzibu\App\Models\Album)
                        {{-- Album Card --}}
                        <div class="group relative">
                            <a href="{{ route('muzibu.albums.show', $item->getTranslation('slug', app()->getLocale())) }}"
                               wire:navigate
                               class="block p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-white/10">
                                <div class="relative mb-3">
                                    <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl">
                                        @if($item->getFirstMedia('album_cover'))
                                            <img src="{{ thumb($item->getFirstMedia('album_cover'), 200, 200, ['scale' => 1]) }}"
                                                 alt="{{ $item->getTranslation('title', app()->getLocale()) }}"
                                                 class="w-full h-full object-cover"
                                                 loading="lazy">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-4xl">
                                                🎸
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <h3 class="font-semibold text-white truncate mb-1 text-sm">
                                    {{ $item->getTranslation('title', app()->getLocale()) }}
                                </h3>
                                @if($item->artist)
                                    <p class="text-xs text-muzibu-text-gray truncate">
                                        {{ $item->artist->getTranslation('title', app()->getLocale()) }}
                                    </p>
                                @endif
                            </a>
                            {{-- Play button OUTSIDE <a> tag --}}
                            <button class="absolute w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-20 pointer-events-auto"
                                    style="bottom: calc(3rem + 0.75rem + 0.5rem); right: calc(0.75rem + 0.5rem);"
                                    @click="playAlbum({{ $item->album_id }})">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>

                    @elseif($item instanceof \Modules\Muzibu\App\Models\Playlist)
                        {{-- Playlist Card --}}
                        <div class="group relative">
                            <a href="{{ route('muzibu.playlist.show', $item->getTranslation('slug', app()->getLocale())) }}"
                               wire:navigate
                               class="block p-3 rounded-lg transition-all duration-300 cursor-pointer bg-transparent hover:bg-white/10">
                                <div class="relative mb-3">
                                    <div class="w-full aspect-square rounded-md overflow-hidden shadow-xl">
                                        @if($item->getFirstMedia('cover'))
                                            <img src="{{ thumb($item->getFirstMedia('cover'), 200, 200, ['scale' => 1]) }}"
                                                 alt="{{ $item->getTranslation('title', app()->getLocale()) }}"
                                                 class="w-full h-full object-cover"
                                                 loading="lazy">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-green-500 to-blue-600 flex items-center justify-center text-4xl">
                                                📋
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <h3 class="font-semibold text-white truncate mb-1 text-sm">
                                    {{ $item->getTranslation('title', app()->getLocale()) }}
                                </h3>
                                @if($item->description)
                                    <p class="text-xs text-muzibu-text-gray truncate">
                                        {{ Str::limit($item->getTranslation('description', app()->getLocale()), 40) }}
                                    </p>
                                @endif
                            </a>
                            {{-- Play button OUTSIDE <a> tag --}}
                            <button class="absolute w-12 h-12 bg-muzibu-coral rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-20 pointer-events-auto"
                                    style="bottom: calc(3rem + 0.75rem + 0.5rem); right: calc(0.75rem + 0.5rem);"
                                    @click="playPlaylist({{ $item->playlist_id }})">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>
                    @endif
                @endif
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($favorites->hasPages())
            <div class="mt-8">
                {{ $favorites->links() }}
            </div>
        @endif

    @else
        {{-- Empty State --}}
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
            <a href="{{ route('muzibu.home') }}"
               wire:navigate
               class="inline-flex items-center px-6 py-3 bg-muzibu-coral text-white font-semibold rounded-full hover:bg-opacity-90 transition-all">
                <i class="fas fa-home mr-2"></i>
                Ana Sayfaya Dön
            </a>
        </div>
    @endif
</div>
@endsection
