@extends('themes.muzibu.layouts.app')

@section('content')
<div class="px-4 py-6 sm:px-6 sm:py-8">
    {{-- Sector Header - Responsive --}}
    <div class="flex flex-col sm:flex-row items-center sm:items-end gap-4 sm:gap-6 mb-6 sm:mb-8">
        @if($sector->media_id && $sector->iconMedia)
            <img src="{{ thumb($sector->iconMedia, 300, 300, ['scale' => 1]) }}"
                 alt="{{ $sector->getTranslation('title', app()->getLocale()) }}"
                 class="w-40 h-40 sm:w-48 sm:h-48 md:w-56 md:h-56 object-cover rounded-lg shadow-2xl flex-shrink-0">
        @else
            <div class="w-40 h-40 sm:w-48 sm:h-48 md:w-56 md:h-56 bg-gradient-to-br from-pink-500 to-rose-600 rounded-lg flex items-center justify-center text-4xl sm:text-5xl md:text-6xl shadow-2xl flex-shrink-0">
                🎭
            </div>
        @endif

        <div class="flex-1 w-full sm:min-w-0 text-center sm:text-left pb-0 sm:pb-4">
            <p class="text-xs sm:text-sm font-semibold text-gray-400 uppercase tracking-wide mb-2">Kategori</p>
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-3 sm:mb-4 truncate">
                {{ $sector->getTranslation('title', app()->getLocale()) }}
            </h1>

            @if($sector->description)
                <p class="text-sm sm:text-base md:text-lg text-gray-300 mb-2 line-clamp-2">
                    {{ $sector->getTranslation('description', app()->getLocale()) }}
                </p>
            @endif

            <p class="text-sm text-gray-400">
                {{ $playlists->count() }} playlist
            </p>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-center sm:justify-start gap-4 mb-6 sm:mb-8">
        <button @click="
            $store.player.setPlayContext({
                type: 'sector',
                id: {{ $sector->sector_id }},
                name: '{{ addslashes($sector->getTranslation('title', app()->getLocale())) }}'
            });
            playSector({{ $sector->sector_id }});
        " class="w-12 h-12 sm:w-14 sm:h-14 bg-muzibu-coral hover:bg-opacity-90 rounded-full flex items-center justify-center shadow-lg hover:scale-105 transition-all">
            <i class="fas fa-play text-white text-lg sm:text-xl ml-1"></i>
        </button>

        <div @click.stop>
            <x-common.favorite-button :model="$sector" />
        </div>
    </div>

    {{-- RADYOLAR BÖLÜMÜ (Üstte) --}}
    @if(isset($radios) && $radios->count() > 0)
        <div class="mb-8 sm:mb-12">
            <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6 flex items-center gap-2 sm:gap-3">
                <i class="fas fa-radio text-red-500"></i>
                Canlı Radyolar
                <span class="bg-red-600 text-white text-xs px-2 py-1 rounded-full animate-pulse">CANLI</span>
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
                @foreach($radios as $radio)
                    <x-muzibu.radio-card :radio="$radio" />
                @endforeach
            </div>
        </div>
    @endif

    {{-- PLAYLİSTLER BÖLÜMÜ (Altta) --}}
    @if($playlists && $playlists->count() > 0)
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6 flex items-center gap-2 sm:gap-3">
                <i class="fas fa-list text-muzibu-coral"></i>
                Playlistler
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
                @foreach($playlists as $playlist)
                    <x-muzibu.playlist-card :playlist="$playlist" :preview="true" />
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
