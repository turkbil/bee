@extends('themes.muzibu.layouts.app')

@section('content')
<div class="px-4 sm:px-6 py-6 sm:py-8">
    {{-- Artist Header - Responsive --}}
    <div class="flex flex-col sm:flex-row items-center sm:items-end gap-4 sm:gap-6 mb-6 sm:mb-8">
        @if($artist->media_id && $artist->photoMedia)
            <img src="{{ thumb($artist->photoMedia, 300, 300, ['scale' => 1]) }}"
                 alt="{{ $artist->getTranslation('title', app()->getLocale()) }}"
                 class="w-40 h-40 sm:w-48 sm:h-48 md:w-56 md:h-56 object-cover rounded-full shadow-2xl flex-shrink-0">
        @else
            <div class="w-40 h-40 sm:w-48 sm:h-48 md:w-56 md:h-56 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-4xl sm:text-5xl md:text-6xl shadow-2xl flex-shrink-0">
                🎤
            </div>
        @endif

        <div class="flex-1 w-full sm:min-w-0 text-center sm:text-left pb-0 sm:pb-4">
            <p class="text-xs sm:text-sm font-semibold text-gray-400 uppercase tracking-wide mb-2">Sanatçı</p>
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-3 sm:mb-4 truncate">
                {{ $artist->getTranslation('title', app()->getLocale()) }}
            </h1>

            <p class="text-sm text-gray-400">
                {{ $albums->count() }} albüm • {{ $songs->count() }} şarkı
            </p>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-center sm:justify-start gap-4 mb-6 sm:mb-8">
        <button @click="playArtist({{ $artist->artist_id }})"
                class="w-12 h-12 sm:w-14 sm:h-14 bg-muzibu-coral hover:bg-opacity-90 rounded-full flex items-center justify-center shadow-lg hover:scale-105 transition-all">
            <i class="fas fa-play text-white text-lg sm:text-xl ml-1"></i>
        </button>

        <div @click.stop>
            <x-common.favorite-button :model="$artist" />
        </div>
    </div>

    {{-- Bio (if exists) --}}
    @if($artist->getTranslation('bio', app()->getLocale()))
        <div class="mb-8 p-4 bg-white/5 rounded-lg">
            <h2 class="text-xl font-bold text-white mb-3">Hakkında</h2>
            <div class="text-gray-300 text-sm leading-relaxed">
                {!! nl2br(e($artist->getTranslation('bio', app()->getLocale()))) !!}
            </div>
        </div>
    @endif

    {{-- Albums Section --}}
    @if($albums && $albums->count() > 0)
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white mb-4">Albümler</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($albums as $album)
                    <a href="{{ route('muzibu.albums.show', $album->getTranslation('slug', app()->getLocale())) }}"
                       class="album-card group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-3 transition-all duration-300">
                        <div class="relative mb-3">
                            @if($album->media_id && $album->coverMedia)
                                <img src="{{ thumb($album->coverMedia, 200, 200, ['scale' => 1]) }}"
                                     alt="{{ $album->getTranslation('title', app()->getLocale()) }}"
                                     class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                     loading="lazy">
                            @else
                                <div class="w-full aspect-square bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-3xl shadow-lg">
                                    💿
                                </div>
                            @endif

                            {{-- Play Button --}}
                            <button @click.stop.prevent="playAlbum({{ $album->album_id }})"
                                    class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-10 h-10 flex items-center justify-center shadow-xl hover:scale-110 hover:bg-green-500">
                                <i class="fas fa-play ml-0.5 text-sm"></i>
                            </button>
                        </div>

                        <h3 class="font-semibold text-white text-sm truncate">
                            {{ $album->getTranslation('title', app()->getLocale()) }}
                        </h3>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Songs Section --}}
    @if($songs && $songs->count() > 0)
        <div>
            <h2 class="text-2xl font-bold text-white mb-4">Popüler Şarkılar</h2>
            <div class="space-y-1">
                @foreach($songs->take(20) as $index => $song)
                    <div class="group flex items-center gap-2 sm:gap-4 px-2 sm:px-4 py-2 sm:py-3 rounded-lg hover:bg-white/5 transition-all cursor-pointer"
                         @click="playSong({{ $song->song_id }})">
                        <span class="text-gray-400 w-6 sm:w-8 text-center text-xs sm:text-sm flex-shrink-0">{{ $index + 1 }}</span>

                        {{-- Song Cover (Mobile hidden, Desktop visible) --}}
                        <div class="hidden sm:block w-10 h-10 flex-shrink-0">
                            @if($song->album && $song->album->media_id && $song->album->coverMedia)
                                <img src="{{ thumb($song->album->coverMedia, 100, 100, ['scale' => 1]) }}"
                                     alt="{{ $song->getTranslation('title', app()->getLocale()) }}"
                                     class="w-full h-full object-cover rounded"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 rounded flex items-center justify-center text-lg">
                                    🎵
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <h3 class="text-white font-medium text-sm sm:text-base truncate">
                                {{ $song->getTranslation('title', app()->getLocale()) }}
                            </h3>
                            @if($song->album)
                                <p class="text-xs sm:text-sm text-gray-400 truncate">
                                    {{ $song->album->getTranslation('title', app()->getLocale()) }}
                                </p>
                            @endif
                        </div>

                        <span class="text-xs sm:text-sm text-gray-400 hidden sm:block flex-shrink-0">
                            {{ gmdate('i:s', $song->duration ?? 0) }}
                        </span>

                        <div @click.stop class="flex-shrink-0">
                            <x-muzibu.song-actions-menu :song="$song" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Empty State --}}
    @if((!$albums || $albums->count() === 0) && (!$songs || $songs->count() === 0))
        <div class="text-center py-12">
            <div class="mb-6">
                <i class="fas fa-music text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Henüz içerik yok</h3>
            <p class="text-gray-400">Bu sanatçıya ait albüm veya şarkı bulunamadı</p>
        </div>
    @endif
</div>

{{-- Play Functions --}}
<script>
function playArtist(artistId) {
    if (window.Alpine && window.Alpine.store('player')) {
        window.Alpine.store('player').setPlayContext({
            type: 'artist',
            id: artistId,
            name: '{{ addslashes($artist->getTranslation('title', app()->getLocale())) }}'
        });
    }
    // TODO: Implement play artist functionality
    console.log('Play artist:', artistId);
}

function playAlbum(albumId) {
    if (window.Alpine && window.Alpine.store('player')) {
        window.Alpine.store('player').setPlayContext({
            type: 'album',
            id: albumId
        });
    }
    // TODO: Implement play album functionality
    console.log('Play album:', albumId);
}

function playSong(songId) {
    if (window.Alpine && window.Alpine.store('player')) {
        window.Alpine.store('player').setPlayContext({
            type: 'song',
            id: songId
        });
    }
    // TODO: Implement play song functionality
    console.log('Play song:', songId);
}
</script>
@endsection
