@extends('themes.muzibu.layouts.app')

@section('content')
<div class="px-4 sm:px-6 py-6 sm:py-8">
    {{-- Playlist Header - Responsive --}}
    <div class="flex flex-col sm:flex-row items-center sm:items-end gap-4 sm:gap-6 mb-6 sm:mb-8">
        @if($playlist->media_id && $playlist->coverMedia)
            <img src="{{ thumb($playlist->coverMedia, 300, 300, ['scale' => 1]) }}"
                 alt="{{ $playlist->getTranslation('title', app()->getLocale()) }}"
                 class="w-40 h-40 sm:w-48 sm:h-48 md:w-56 md:h-56 object-cover rounded-lg shadow-2xl flex-shrink-0">
        @else
            <div class="w-40 h-40 sm:w-48 sm:h-48 md:w-56 md:h-56 bg-gradient-to-br from-muzibu-coral to-purple-600 rounded-lg flex items-center justify-center text-4xl sm:text-5xl md:text-6xl shadow-2xl flex-shrink-0">
                🎵
            </div>
        @endif

        <div class="flex-1 w-full sm:min-w-0 text-center sm:text-left pb-0 sm:pb-4">
            <p class="text-xs sm:text-sm font-semibold text-gray-400 uppercase tracking-wide mb-2">Playlist</p>
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-3 sm:mb-4 truncate">
                {{ $playlist->getTranslation('title', app()->getLocale()) }}
            </h1>

            @if($playlist->description)
                <p class="text-sm sm:text-base md:text-lg text-gray-300 mb-2 line-clamp-2">
                    {{ $playlist->getTranslation('description', app()->getLocale()) }}
                </p>
            @endif

            <p class="text-sm text-gray-400">
                {{ $songs->count() }} şarkı
            </p>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-center sm:justify-start gap-4 mb-6 sm:mb-8">
        <button class="w-12 h-12 sm:w-14 sm:h-14 bg-muzibu-coral hover:bg-opacity-90 rounded-full flex items-center justify-center shadow-lg hover:scale-105 transition-all">
            <i class="fas fa-play text-white text-lg sm:text-xl ml-1"></i>
        </button>

        <div @click.stop>
            <x-common.favorite-button :model="$playlist" />
        </div>
    </div>

    {{-- Songs List - Responsive --}}
    @if($songs && $songs->count() > 0)
        <div class="space-y-1">
            @foreach($songs as $index => $song)
                <div class="group flex items-center gap-2 sm:gap-4 px-2 sm:px-4 py-2 sm:py-3 rounded-lg hover:bg-white/5 transition-all cursor-pointer">
                    <span class="text-gray-400 w-6 sm:w-8 text-center text-xs sm:text-sm flex-shrink-0">{{ $index + 1 }}</span>

                    <div class="flex-1 min-w-0">
                        <h3 class="text-white font-medium text-sm sm:text-base truncate">
                            {{ $song->getTranslation('title', app()->getLocale()) }}
                        </h3>
                        @if($song->artist)
                            <p class="text-xs sm:text-sm text-gray-400 truncate">
                                {{ $song->artist->getTranslation('title', app()->getLocale()) }}
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
    @else
        <div class="text-center py-12">
            <p class="text-gray-400">Bu playlist'te henüz şarkı yok</p>
        </div>
    @endif
</div>
@endsection
