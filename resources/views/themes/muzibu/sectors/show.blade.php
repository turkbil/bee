@extends('themes.muzibu.layouts.app')

@section('content')
<div class="px-6 py-8">
    {{-- Sector Header --}}
    <div class="flex items-end gap-6 mb-8 animate-slide-up">
        @if($sector->media_id && $sector->iconMedia)
            <img src="{{ thumb($sector->iconMedia, 300, 300, ['scale' => 1]) }}"
                 alt="{{ $sector->getTranslation('title', app()->getLocale()) }}"
                 class="w-56 h-56 object-cover rounded-lg shadow-2xl flex-shrink-0">
        @else
            <div class="w-56 h-56 bg-gradient-to-br from-pink-500 to-rose-600 rounded-lg flex items-center justify-center text-6xl shadow-2xl flex-shrink-0">
                🎭
            </div>
        @endif

        <div class="flex-1 min-w-0 pb-4">
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-2">Kategori</p>
            <h1 class="text-5xl font-bold text-white mb-4 truncate">
                {{ $sector->getTranslation('title', app()->getLocale()) }}
            </h1>

            @if($sector->description)
                <p class="text-lg text-gray-300 mb-2">
                    {{ $sector->getTranslation('description', app()->getLocale()) }}
                </p>
            @endif

            <p class="text-sm text-gray-400">
                {{ $playlists->count() }} playlist
            </p>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-4 mb-8">
        <button @click="
            $store.player.setPlayContext({
                type: 'sector',
                id: {{ $sector->sector_id }},
                name: '{{ addslashes($sector->getTranslation('title', app()->getLocale())) }}'
            });
            playSector({{ $sector->sector_id }});
        " class="w-14 h-14 bg-muzibu-coral hover:bg-opacity-90 rounded-full flex items-center justify-center shadow-lg hover:scale-105 transition-all">
            <i class="fas fa-play text-white text-xl ml-1"></i>
        </button>

        <div @click.stop>
            <x-common.favorite-button :model="$sector" />
        </div>
    </div>

    {{-- RADYOLAR BÖLÜMÜ (Üstte) --}}
    @if(isset($radios) && $radios->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <i class="fas fa-radio text-red-500"></i>
                Canlı Radyolar
                <span class="bg-red-600 text-white text-xs px-2 py-1 rounded-full animate-pulse">CANLI</span>
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                @foreach($radios as $radio)
                    <div class="group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-4 transition-all duration-300 cursor-pointer"
                         @click="
                            $store.player.setPlayContext({
                                type: 'radio',
                                id: {{ $radio->radio_id }},
                                name: '{{ addslashes($radio->getTranslation('title', app()->getLocale())) }}',
                                streamUrl: '{{ $radio->stream_url }}'
                            });
                            $dispatch('play-radio', {
                                radioId: {{ $radio->radio_id }},
                                title: '{{ addslashes($radio->getTranslation('title', app()->getLocale())) }}',
                                streamUrl: '{{ $radio->stream_url }}'
                            });
                         ">
                        <div class="relative mb-4">
                            @if($radio->media_id && $radio->logoMedia)
                                <div class="w-full aspect-square bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg flex items-center justify-center p-4 shadow-lg overflow-hidden">
                                    <img src="{{ thumb($radio->logoMedia, 200, 200, ['scale' => 1]) }}"
                                         alt="{{ $radio->getTranslation('title', app()->getLocale()) }}"
                                         class="w-full h-full object-contain"
                                         loading="lazy">
                                </div>
                            @else
                                <div class="w-full aspect-square bg-gradient-to-br from-red-500 via-pink-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                                    <i class="fas fa-radio text-white text-4xl opacity-90"></i>
                                </div>
                            @endif

                            {{-- Play Button Overlay --}}
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                                <button class="opacity-0 group-hover:opacity-100 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg">
                                    <i class="fas fa-play ml-1"></i>
                                </button>
                            </div>

                            {{-- Live Badge --}}
                            <div class="absolute top-2 left-2">
                                <div class="bg-red-600 text-white px-2 py-0.5 rounded-full text-[10px] font-bold flex items-center gap-1 shadow-lg animate-pulse">
                                    <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                                    CANLI
                                </div>
                            </div>
                        </div>

                        <h3 class="font-semibold text-white mb-1 truncate text-center">
                            {{ $radio->getTranslation('title', app()->getLocale()) }}
                        </h3>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- PLAYLİSTLER BÖLÜMÜ (Altta) --}}
    @if($playlists && $playlists->count() > 0)
        <div>
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <i class="fas fa-list text-muzibu-coral"></i>
                Playlistler
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                @foreach($playlists as $playlist)
                    <a href="{{ route('muzibu.playlists.show', $playlist->getTranslation('slug', app()->getLocale())) }}"
                       wire:navigate
                       class="playlist-card group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-4 transition-all duration-300"
                       data-playlist-id="{{ $playlist->playlist_id }}"
                       data-playlist-title="{{ $playlist->getTranslation('title', app()->getLocale()) }}"
                       data-is-favorite="{{ auth()->check() && $playlist->isFavoritedBy(auth()->user()) ? '1' : '0' }}"
                       data-is-mine="{{ auth()->check() && $playlist->user_id === auth()->id() ? '1' : '0' }}">
                        <div class="relative mb-4">
                            @if($playlist->media_id && $playlist->coverMedia)
                                <img src="{{ thumb($playlist->coverMedia, 300, 300, ['scale' => 1]) }}"
                                     alt="{{ $playlist->getTranslation('title', app()->getLocale()) }}"
                                     class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                     loading="lazy">
                            @else
                                <div class="w-full aspect-square bg-gradient-to-br from-muzibu-coral to-purple-600 rounded-lg flex items-center justify-center text-4xl shadow-lg">
                                    🎵
                                </div>
                            @endif

                            {{-- Play Button - Spotify Style Bottom Right --}}
                            <button @click.stop.prevent="
                                $store.player.setPlayContext({
                                    type: 'playlist',
                                    id: {{ $playlist->playlist_id }},
                                    name: '{{ addslashes($playlist->getTranslation('title', app()->getLocale())) }}'
                                });
                                playPlaylist({{ $playlist->playlist_id }});
                            " class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-xl hover:scale-110 hover:bg-green-500">
                                <i class="fas fa-play ml-1"></i>
                            </button>

                            {{-- Favorite Button --}}
                            <div class="absolute top-2 right-2" @click.stop>
                                <x-common.favorite-button :model="$playlist" size="sm" iconOnly="true" />
                            </div>

                            {{-- Menu Button (3 nokta) --}}
                            <div class="absolute top-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop.prevent>
                                <button @click="Alpine.store('contextMenu').openContextMenu($event, 'playlist', {
                                    id: {{ $playlist->playlist_id }},
                                    title: '{{ addslashes($playlist->getTranslation('title', app()->getLocale())) }}',
                                    is_favorite: {{ auth()->check() && $playlist->isFavoritedBy(auth()->user()) ? 'true' : 'false' }},
                                    is_mine: {{ auth()->check() && $playlist->user_id === auth()->id() ? 'true' : 'false' }}
                                })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                                    <i class="fas fa-ellipsis-v text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <h3 class="font-semibold text-white mb-1 truncate">
                            {{ $playlist->getTranslation('title', app()->getLocale()) }}
                        </h3>

                        @if($playlist->description)
                            <p class="text-sm text-gray-400 truncate">
                                {{ $playlist->getTranslation('description', app()->getLocale()) }}
                            </p>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-400">Bu kategoride henüz playlist yok</p>
        </div>
    @endif
</div>
@endsection
