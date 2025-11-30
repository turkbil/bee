@extends('themes.muzibu.layouts.app')

@section('title', 'Muzibu - Modern Music Platform')

@section('content')
    {{-- SKELETON LOADING STATE --}}
    <div x-show="isLoading" x-cloak class="px-2 py-4 space-y-8">
        {{-- Skeleton Pills --}}
        <div class="flex gap-3">
            <div class="skeleton w-20 h-10 rounded-full"></div>
            <div class="skeleton w-24 h-10 rounded-full"></div>
            <div class="skeleton w-28 h-10 rounded-full"></div>
        </div>

        {{-- Skeleton Cards Grid --}}
        <div class="space-y-6">
            <div class="skeleton w-48 h-8 rounded"></div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @for($i = 0; $i < 5; $i++)
                <div class="space-y-3" class="skeleton-stagger-{{ $i }}">
                    <div class="skeleton w-full aspect-square rounded-lg"></div>
                    <div class="skeleton w-3/4 h-4 rounded"></div>
                    <div class="skeleton w-1/2 h-3 rounded"></div>
                </div>
                @endfor
            </div>
        </div>

        {{-- Skeleton Song List --}}
        <div class="space-y-4">
            <div class="skeleton w-40 h-8 rounded"></div>
            <div class="space-y-2">
                @for($i = 0; $i < 3; $i++)
                <div class="flex gap-4 items-center">
                    <div class="skeleton w-10 h-10 rounded"></div>
                    <div class="flex-1 skeleton h-4 rounded"></div>
                    <div class="skeleton w-20 h-4 rounded"></div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- ACTUAL CONTENT --}}
    <div x-show="contentLoaded" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

            {{-- CTA Actions - Responsive --}}
            <div class="px-2 sm:px-8 pt-2 pb-4">
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <button @click="$dispatch('open-create-playlist-modal')" class="flex-1 sm:flex-none px-6 py-3 bg-gradient-to-r from-muzibu-coral to-pink-600 hover:from-muzibu-coral-light hover:to-pink-700 text-white font-bold rounded-full transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 flex items-center justify-center gap-2">
                        <i class="fas fa-plus-circle"></i>
                        <span>Playlist Oluştur</span>
                    </button>
                    <a href="{{ route('muzibu.my-playlists') }}" class="flex-1 sm:flex-none px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-full transition-all duration-300 border-2 border-white/20 hover:border-white/40 flex items-center justify-center gap-2">
                        <i class="fas fa-list"></i>
                        <span>Playlistlerim</span>
                    </a>
                    <a href="{{ route('muzibu.favorites') }}" class="flex-1 sm:flex-none px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-full transition-all duration-300 border-2 border-white/20 hover:border-white/40 flex items-center justify-center gap-2">
                        <i class="fas fa-heart"></i>
                        <span>Favorilerim</span>
                    </a>
                </div>
            </div>

            {{-- Horizontal Scroll Cards with parallax --}}
            @if(isset($featuredPlaylists) && $featuredPlaylists->count() > 0)
            <div class="px-2 pt-1 pb-8 animate-slide-up">
                <div class="flex gap-6 overflow-x-auto pb-2 scrollbar-thin">
                    @foreach($featuredPlaylists as $index => $playlist)
                    @php
                        $titleJson = @json_decode($playlist->title);
                        $playlistTitle = $titleJson && isset($titleJson->tr) ? $titleJson->tr : $playlist->title;
                        $gradients = [
                            'from-purple-500 via-pink-500 to-red-500',
                            'from-blue-500 via-cyan-500 to-teal-500',
                            'from-orange-500 via-red-500 to-pink-500',
                            'from-green-500 via-emerald-500 to-teal-500',
                            'from-indigo-500 via-purple-500 to-pink-500',
                            'from-yellow-500 via-orange-500 to-red-500',
                        ];
                    @endphp
                    <div
                        class="min-w-[180px] bg-spotify-gray p-5 rounded-lg hover:bg-spotify-gray-light cursor-pointer group transition-all duration-300 shadow-xl hover:shadow-2xl card-shine animate-scale-in"
                        class="item-stagger-{{ $index % 10 }}"
                    >
                        <div class="relative mb-4" @click="playPlaylist({{ $playlist->playlist_id }})">
                            <div class="w-full aspect-square rounded-lg bg-gradient-to-br {{ $gradients[$index % count($gradients)] }} flex items-center justify-center text-5xl animate-gradient shadow-lg group-hover:shadow-2xl transition-all duration-300">
                                🎵
                            </div>
                            <button class="absolute bottom-2 right-2 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 shadow-2xl transition-all duration-500 ease-out glow-on-hover">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>
                        <div @click="playPlaylist({{ $playlist->playlist_id }})">
                            <h3 class="font-bold text-white text-lg mb-2 truncate group-hover:text-spotify-green transition-colors duration-300">{{ $playlistTitle }}</h3>
                            <p class="text-sm text-spotify-text-gray truncate">Playlist</p>
                        </div>
                        <div class="mt-2 flex items-center justify-between" @click.stop>
                            <x-common.favorite-button :model="$playlist" size="sm" iconOnly="true" />
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Albums Section with stagger animation --}}
            @if(isset($newReleases) && $newReleases->count() > 0)
            <div class="px-2 py-4 animate-slide-up" class="section-delay-1">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-white via-zinc-100 to-spotify-text-gray bg-clip-text text-transparent">Yeni Albümler</h2>
                    <a href="/albums" class="text-sm font-bold text-spotify-text-gray hover:text-white hover:underline transition-colors duration-300 group">
                        Tümünü göster →
                    </a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-8">
                    @foreach($newReleases->take(10) as $index => $album)
                    @php
                        $albumTitleJson = @json_decode($album->title);
                        $albumTitle = $albumTitleJson && isset($albumTitleJson->tr) ? $albumTitleJson->tr : $album->title;
                        $artistTitleJson = @json_decode($album->artist_title);
                        $artistTitle = $artistTitleJson && isset($artistTitleJson->tr) ? $artistTitleJson->tr : $album->artist_title;
                    @endphp
                    <div
                        class="bg-spotify-gray p-5 rounded-lg hover:bg-spotify-gray-light cursor-pointer group transition-all duration-300 shadow-xl hover:shadow-2xl card-shine animate-scale-in"
                        class="item-stagger-{{ $index % 10 }}"
                    >
                        <div class="relative mb-4" @click="playAlbum({{ $album->album_id }})">
                            <div class="w-full aspect-square rounded-lg bg-gradient-to-br from-blue-500 via-purple-600 to-pink-600 flex items-center justify-center text-5xl animate-gradient shadow-lg group-hover:shadow-2xl transition-all duration-300">
                                🎸
                            </div>
                            <button class="absolute bottom-2 right-2 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 shadow-2xl transition-all duration-500 ease-out glow-on-hover">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>
                        <div @click="playAlbum({{ $album->album_id }})">
                            <h3 class="font-bold text-white text-lg mb-2 truncate group-hover:text-spotify-green transition-colors duration-300">{{ $albumTitle }}</h3>
                            <p class="text-base text-spotify-text-gray/80 truncate">{{ $artistTitle }}</p>
                        </div>
                        <div class="mt-2 flex items-center justify-between" @click.stop>
                            <x-common.favorite-button :model="$album" size="sm" iconOnly="true" />
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Popular Songs with row hover --}}
            @if(isset($popularSongs) && $popularSongs->count() > 0)
            <div class="px-2 py-4 animate-slide-up" class="section-delay-2">
                <h2 class="text-3xl font-bold mb-6 tracking-tight bg-gradient-to-r from-white via-zinc-100 to-spotify-text-gray bg-clip-text text-transparent">Popüler Şarkılar</h2>
                <div class="bg-spotify-gray/30 rounded-lg overflow-hidden backdrop-blur-sm">
                    @foreach($popularSongs->take(10) as $index => $song)
                    @php
                        $songTitleJson = @json_decode($song->song_title);
                        $songTitle = $songTitleJson && isset($songTitleJson->tr) ? $songTitleJson->tr : $song->song_title;
                        $artistTitleJson = @json_decode($song->artist_title);
                        $songArtist = $artistTitleJson && isset($artistTitleJson->tr) ? $artistTitleJson->tr : $song->artist_title;
                        $albumTitleJson = @json_decode($song->album_title);
                        $songAlbum = $albumTitleJson && isset($albumTitleJson->tr) ? $albumTitleJson->tr : $song->album_title;
                    @endphp
                    <div
                        class="grid grid-cols-[40px_1fr_60px_60px_40px] md:grid-cols-[40px_50px_1fr_200px_60px_60px_40px] gap-4 items-center px-4 py-4 hover:bg-spotify-green/15 cursor-pointer group border-l-4 border-transparent hover:border-spotify-green transition-all duration-300 ease-out"
                    >
                        <div class="text-center text-base text-spotify-text-gray group-hover:text-spotify-green transition-colors duration-300" @click="playSong({ id: {{ $song->song_id }}, title: '{{ addslashes($songTitle) }}' })">
                            <span class="group-hover:hidden">{{ $index + 1 }}</span>
                            <i class="fas fa-play text-spotify-green hidden group-hover:inline animate-pulse"></i>
                        </div>
                        <div class="hidden md:grid place-items-center w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded text-xl leading-none shadow-lg group-hover:shadow-spotify-green/50 transition-all duration-300" @click="playSong({ id: {{ $song->song_id }}, title: '{{ addslashes($songTitle) }}' })">🎵</div>
                        <div class="min-w-0" @click="playSong({ id: {{ $song->song_id }}, title: '{{ addslashes($songTitle) }}' })">
                            <h4 class="text-white font-medium truncate group-hover:text-spotify-green transition-colors">{{ $songTitle }}</h4>
                            <p class="text-sm text-spotify-text-gray truncate">{{ $songArtist }}</p>
                        </div>
                        <div class="text-sm text-spotify-text-gray truncate hidden lg:block" @click="playSong({ id: {{ $song->song_id }}, title: '{{ addslashes($songTitle) }}' })">{{ $songAlbum }}</div>
                        <button
                            @click.stop="toggleLike({{ $song->song_id }})"
                            class="text-spotify-text-gray hover:text-spotify-green transition-colors"
                            :class="{ 'text-spotify-green': isLiked({{ $song->song_id }}) }"
                        >
                            <i :class="isLiked({{ $song->song_id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
                        </button>
                        <div class="text-sm text-spotify-text-gray text-right group-hover:text-white transition-colors">{{ gmdate('i:s', $song->duration ?? 0) }}</div>
                        <div @click.stop>
                            <x-muzibu.song-actions-menu :song="$song" />
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Genres with Images --}}
            @if(isset($genres) && $genres->count() > 0)
            <div class="px-2 py-4 pb-20 animate-slide-up" class="section-delay-3">
                <h2 class="text-3xl font-bold mb-6 tracking-tight bg-gradient-to-r from-white via-zinc-100 to-spotify-text-gray bg-clip-text text-transparent">Türler</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
                    @foreach($genres as $index => $genre)
                    @php
                        $slugJson = @json_decode($genre->slug);
                        $slug = $slugJson && isset($slugJson->tr) ? $slugJson->tr : $genre->slug;
                        $titleJson = @json_decode($genre->title);
                        $title = $titleJson && isset($titleJson->tr) ? $titleJson->tr : $genre->title;
                    @endphp
                    <a
                        href="/genres/{{ $slug }}"
                        class="h-40 rounded-lg overflow-hidden cursor-pointer shadow-2xl hover:shadow-spotify-green/30 transition-all duration-300 ease-out relative group card-shine animate-scale-in"
                        class="card-stagger-{{ $index % 10 }}"
                    >
                        @if($genre->media_id && $genre->iconMedia)
                            {{-- Genre Image Background --}}
                            <div class="absolute inset-0">
                                <img
                                    src="{{ thumb($genre->iconMedia, 300, 300, ['scale' => 1]) }}"
                                    alt="{{ $title }}"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                >
                            </div>
                            {{-- Gradient Overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent group-hover:from-black/60"></div>
                        @else
                            {{-- Fallback Gradient --}}
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-pink-600"></div>
                            <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors duration-300"></div>
                        @endif

                        {{-- Genre Title --}}
                        <div class="absolute bottom-0 left-0 right-0 p-5">
                            <h3 class="text-2xl font-bold text-white relative z-10 group-hover:text-spotify-green transition-colors duration-300">{{ $title }}</h3>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
    </div>
    {{-- END ACTUAL CONTENT --}}
@endsection
