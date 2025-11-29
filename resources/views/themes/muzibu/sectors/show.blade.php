@extends('themes.muzibu.layouts.app')

@section('content')
<div class="px-6 py-8">
    {{-- Sector Header --}}
    <div class="flex items-end gap-6 mb-8 animate-slide-up">
        @if($sector->getFirstMedia('cover'))
            <img src="{{ thumb($sector->getFirstMedia('cover'), 300, 300, ['scale' => 1]) }}"
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
        <button class="w-14 h-14 bg-muzibu-coral hover:bg-opacity-90 rounded-full flex items-center justify-center shadow-lg hover:scale-105 transition-all">
            <i class="fas fa-play text-white text-xl ml-1"></i>
        </button>

        <div @click.stop>
            <x-common.favorite-button :model="$sector" />
        </div>
    </div>

    {{-- Playlists Grid --}}
    @if($playlists && $playlists->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach($playlists as $playlist)
                <a href="{{ route('muzibu.playlists.show', $playlist->getTranslation('slug', app()->getLocale())) }}"
                   class="group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-4 transition-all duration-300">
                    <div class="relative mb-4">
                        @if($playlist->getFirstMedia('cover'))
                            <img src="{{ thumb($playlist->getFirstMedia('cover'), 300, 300, ['scale' => 1]) }}"
                                 alt="{{ $playlist->getTranslation('title', app()->getLocale()) }}"
                                 class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                 loading="lazy">
                        @else
                            <div class="w-full aspect-square bg-gradient-to-br from-muzibu-coral to-purple-600 rounded-lg flex items-center justify-center text-4xl shadow-lg">
                                🎵
                            </div>
                        @endif

                        {{-- Play Button Overlay --}}
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                            <button class="opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:scale-110">
                                <i class="fas fa-play ml-1"></i>
                            </button>
                        </div>

                        {{-- Favorite Button --}}
                        <div class="absolute top-2 right-2" @click.stop>
                            <x-common.favorite-button :model="$playlist" size="sm" iconOnly="true" />
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
    @else
        <div class="text-center py-12">
            <p class="text-gray-400">Bu kategoride henüz playlist yok</p>
        </div>
    @endif
</div>
@endsection
