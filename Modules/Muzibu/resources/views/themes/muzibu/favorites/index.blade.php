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
                    <div class="group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-4 transition-all duration-300 cursor-pointer">
                        @if($item instanceof \Modules\Muzibu\App\Models\Song)
                            <!-- Song Card -->
                            <div @click="$dispatch('play-song', { songId: {{ $item->song_id }} })">
                                <div class="relative mb-4">
                                    @if($item->album && $item->album->getFirstMedia('album_cover'))
                                        <img src="{{ thumb($item->album->getFirstMedia('album_cover'), 300, 300) }}"
                                             alt="{{ $item->getTranslation('title', app()->getLocale()) }}"
                                             class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                             loading="lazy">
                                    @else
                                        <div class="w-full aspect-square bg-gradient-to-br from-muzibu-coral to-purple-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-music text-white text-4xl opacity-50"></i>
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                                        <button class="opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:scale-110">
                                            <i class="fas fa-play ml-1"></i>
                                        </button>
                                    </div>

                                    <div class="absolute top-2 right-2" @click.stop>
                                        <x-common.favorite-button :model="$item" size="sm" iconOnly="true" />
                                    </div>
                                </div>

                                <h3 class="font-semibold text-white mb-1 truncate">
                                    {{ $item->getTranslation('title', app()->getLocale()) }}
                                </h3>

                                @if($item->album && $item->album->artist)
                                    <p class="text-sm text-gray-400 truncate">
                                        {{ $item->album->artist->getTranslation('title', app()->getLocale()) }}
                                    </p>
                                @endif
                            </div>

                        @elseif($item instanceof \Modules\Muzibu\App\Models\Album)
                            <!-- Album Card -->
                            <a href="{{ route('muzibu.album.show', $item->getTranslation('slug', app()->getLocale())) }}">
                                <div class="relative mb-4">
                                    @if($item->getFirstMedia('album_cover'))
                                        <img src="{{ thumb($item->getFirstMedia('album_cover'), 300, 300) }}"
                                             alt="{{ $item->getTranslation('title', app()->getLocale()) }}"
                                             class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                             loading="lazy">
                                    @else
                                        <div class="w-full aspect-square bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-compact-disc text-white text-4xl opacity-50"></i>
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                                        <button class="opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:scale-110">
                                            <i class="fas fa-play ml-1"></i>
                                        </button>
                                    </div>

                                    <div class="absolute top-2 right-2" @click.stop>
                                        <x-common.favorite-button :model="$item" size="sm" iconOnly="true" />
                                    </div>
                                </div>

                                <h3 class="font-semibold text-white mb-1 truncate">
                                    {{ $item->getTranslation('title', app()->getLocale()) }}
                                </h3>

                                @if($item->artist)
                                    <p class="text-sm text-gray-400 truncate">
                                        {{ $item->artist->getTranslation('title', app()->getLocale()) }}
                                    </p>
                                @endif
                            </a>

                        @elseif($item instanceof \Modules\Muzibu\App\Models\Playlist)
                            <!-- Playlist Card -->
                            <a href="{{ route('muzibu.playlist.show', $item->getTranslation('slug', app()->getLocale())) }}">
                                <div class="relative mb-4">
                                    @if($item->getFirstMedia('cover'))
                                        <img src="{{ thumb($item->getFirstMedia('cover'), 300, 300) }}"
                                             alt="{{ $item->getTranslation('title', app()->getLocale()) }}"
                                             class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                             loading="lazy">
                                    @else
                                        <div class="w-full aspect-square bg-gradient-to-br from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-list-music text-white text-4xl opacity-50"></i>
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                                        <button class="opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:scale-110">
                                            <i class="fas fa-play ml-1"></i>
                                        </button>
                                    </div>

                                    <div class="absolute top-2 right-2" @click.stop>
                                        <x-common.favorite-button :model="$item" size="sm" iconOnly="true" />
                                    </div>
                                </div>

                                <h3 class="font-semibold text-white mb-1 truncate">
                                    {{ $item->getTranslation('title', app()->getLocale()) }}
                                </h3>

                                @if($item->description)
                                    <p class="text-sm text-gray-400 truncate">
                                        {{ $item->getTranslation('description', app()->getLocale()) }}
                                    </p>
                                @endif
                            </a>
                        @endif
                    </div>
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
