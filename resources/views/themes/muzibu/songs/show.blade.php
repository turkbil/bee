@extends('themes.muzibu.layouts.app')

@section('content')
<div class="px-4 sm:px-6 py-6 sm:py-8">
    {{-- Song Header - Hero Section --}}
    <div class="flex flex-col sm:flex-row items-center sm:items-end gap-4 sm:gap-6 mb-6 sm:mb-8">
        @if($song->getCoverUrl())
            <img src="{{ thumb_url($song->getCoverUrl(), 300, 300) }}"
                 alt="{{ $song->getTranslation('title', app()->getLocale()) }}"
                 class="w-48 h-48 sm:w-56 sm:h-56 md:w-64 md:h-64 object-cover rounded-xl shadow-2xl flex-shrink-0">
        @else
            <div class="w-48 h-48 sm:w-56 sm:h-56 md:w-64 md:h-64 bg-gradient-to-br from-muzibu-coral via-purple-600 to-pink-600 rounded-xl flex items-center justify-center text-5xl sm:text-6xl md:text-7xl shadow-2xl flex-shrink-0">
                🎵
            </div>
        @endif

        <div class="flex-1 w-full sm:min-w-0 text-center sm:text-left pb-0 sm:pb-4">
            <p class="text-xs sm:text-sm font-semibold text-gray-400 uppercase tracking-wide mb-2">Şarkı</p>
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-3 sm:mb-4">
                {{ $song->getTranslation('title', app()->getLocale()) }}
            </h1>

            @if($song->album && $song->album->artist)
                <a href="{{ $song->album->artist->getUrl() }}"
                   class="text-lg sm:text-xl text-white hover:text-muzibu-coral transition-colors font-semibold inline-block mb-2">
                    {{ $song->album->artist->getTranslation('title', app()->getLocale()) }}
                </a>
            @endif

            @if($song->album)
                <p class="text-sm sm:text-base text-gray-400">
                    Albüm:
                    <a href="{{ $song->album->getUrl() }}" class="text-gray-300 hover:text-white transition-colors">
                        {{ $song->album->getTranslation('title', app()->getLocale()) }}
                    </a>
                </p>
            @endif

            @if($song->duration)
                <p class="text-sm text-gray-500 mt-1">
                    <i class="far fa-clock mr-1"></i>
                    {{ $song->getFormattedDuration() }}
                </p>
            @endif
        </div>
    </div>

    {{-- Actions - Play, Favorite, Add to Playlist --}}
    <div class="flex items-center justify-center sm:justify-start gap-4 mb-8 sm:mb-10">
        <button
            @click="playSong({{ $song->song_id }})"
            class="w-14 h-14 sm:w-16 sm:h-16 bg-muzibu-coral hover:bg-muzibu-coral-light rounded-full flex items-center justify-center shadow-xl hover:scale-110 transition-all duration-200">
            <i class="fas fa-play text-white text-xl sm:text-2xl ml-1"></i>
        </button>

        <div @click.stop>
            <x-common.favorite-button :model="$song" />
        </div>

        <button
            @click="$store.playlistModal.showForSong({{ $song->song_id }}, {
                title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
                artist: '{{ $song->album && $song->album->artist ? addslashes($song->album->artist->getTranslation('title', app()->getLocale())) : '' }}',
                cover_url: '{{ $song->getCoverUrl() ? thumb_url($song->getCoverUrl(), 300, 300) : '' }}'
            })"
            class="group/add w-12 h-12 sm:w-14 sm:h-14 bg-zinc-800 hover:bg-muzibu-coral rounded-full flex items-center justify-center transition-all relative"
            title="Playlist'e Ekle">
            <i class="fas fa-plus text-white text-lg"></i>
            <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 px-3 py-1 bg-zinc-900 text-white text-xs rounded-lg whitespace-nowrap opacity-0 group-hover/add:opacity-100 transition-opacity pointer-events-none">
                Playlist'e Ekle
            </span>
        </button>
    </div>

    {{-- Lyrics Section (if available) --}}
    @if($song->lyrics)
        <div class="mb-8 bg-zinc-900/50 rounded-xl p-6">
            <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-align-left text-muzibu-coral"></i>
                Şarkı Sözleri
            </h2>
            <div class="text-gray-300 leading-relaxed whitespace-pre-line">
                {{ $song->getTranslation('lyrics', app()->getLocale()) }}
            </div>
        </div>
    @endif

    {{-- Related Songs from Same Album --}}
    @if($relatedSongs && $relatedSongs->count() > 0)
        <div class="mt-10">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-record-vinyl text-muzibu-coral"></i>
                Aynı Albümden
            </h2>

            <div class="space-y-1">
                @foreach($relatedSongs as $index => $relatedSong)
                    <div
                        @click="playSong({{ $relatedSong->song_id }})"
                        class="group flex items-center gap-3 sm:gap-4 px-3 sm:px-4 py-3 rounded-lg hover:bg-white/5 transition-all cursor-pointer">

                        {{-- Play/Number --}}
                        <div class="w-10 text-center text-gray-400 group-hover:text-white transition-colors">
                            <span class="group-hover:hidden">{{ $index + 1 }}</span>
                            <i class="fas fa-play hidden group-hover:inline text-muzibu-coral"></i>
                        </div>

                        {{-- Cover --}}
                        @if($relatedSong->getCoverUrl())
                            <img src="{{ thumb_url($relatedSong->getCoverUrl(), 80, 80) }}"
                                 alt="{{ $relatedSong->getTranslation('title', app()->getLocale()) }}"
                                 class="w-12 h-12 rounded-md object-cover flex-shrink-0">
                        @else
                            <div class="w-12 h-12 bg-gradient-to-br from-zinc-700 to-zinc-800 rounded-md flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-music text-gray-500 text-sm"></i>
                            </div>
                        @endif

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="text-white font-medium truncate group-hover:text-muzibu-coral transition-colors">
                                {{ $relatedSong->getTranslation('title', app()->getLocale()) }}
                            </h3>
                            @if($relatedSong->album && $relatedSong->album->artist)
                                <p class="text-sm text-gray-400 truncate">
                                    {{ $relatedSong->album->artist->getTranslation('title', app()->getLocale()) }}
                                </p>
                            @endif
                        </div>

                        {{-- Duration --}}
                        @if($relatedSong->duration)
                            <div class="text-sm text-gray-400 hidden sm:block">
                                {{ $relatedSong->getFormattedDuration() }}
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <div @click.stop>
                                <x-common.favorite-button :model="$relatedSong" size="sm" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
