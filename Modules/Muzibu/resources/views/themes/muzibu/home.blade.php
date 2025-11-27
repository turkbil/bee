@extends('themes.muzibu.layouts.app')

@section('title', 'Muzibu - Modern Music Platform')

@section('content')
    {{-- SKELETON LOADING STATE --}}
    <div x-show="isLoading" x-cloak class="px-6 py-8 space-y-8">
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
                <div class="space-y-3" x-data="{ delay: {{ $i * 50 }} }" :style="`animation-delay: ${delay}ms`">
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
            {{-- Top Pills with enhanced effects --}}
            <div class="px-6 py-4 flex gap-3 overflow-x-auto scrollbar-hide sticky top-0 bg-gradient-to-b from-spotify-dark to-transparent backdrop-blur-sm z-10">
                <button class="px-4 py-2 bg-white text-black rounded-full text-sm font-semibold whitespace-nowrap transition-all shadow-lg hover:shadow-white/30">
                    Tümü
                </button>
                <button class="px-4 py-2 bg-spotify-gray-light text-white rounded-full text-sm font-semibold whitespace-nowrap hover:bg-white hover:text-black transition-all">
                    Müzik
                </button>
                <button class="px-4 py-2 bg-spotify-gray-light text-white rounded-full text-sm font-semibold whitespace-nowrap hover:bg-white hover:text-black transition-all">
                    Podcast'ler
                </button>
            </div>

            {{-- Horizontal Scroll Cards with parallax --}}
            @if(isset($featuredPlaylists) && $featuredPlaylists->count() > 0)
            <div class="px-6 py-4 animate-slide-up">
                <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-thin">
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
                        class="min-w-[180px] bg-spotify-gray p-4 rounded-lg hover:bg-spotify-gray-light cursor-pointer group transition-all shadow-xl hover:shadow-2xl card-shine animate-scale-in"
                        @click="playPlaylist({{ $playlist->playlist_id }})"
                        style="animation-delay: {{ $index * 50 }}ms"
                    >
                        <div class="relative mb-4">
                            <div class="w-full aspect-square rounded-lg bg-gradient-to-br {{ $gradients[$index % count($gradients)] }} flex items-center justify-center text-5xl animate-gradient shadow-lg group-hover:shadow-2xl transition-shadow">
                                🎵
                            </div>
                            <button class="absolute bottom-2 right-2 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 shadow-2xl transition-all glow-on-hover">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>
                        <h3 class="font-bold text-white mb-2 truncate group-hover:text-spotify-green transition-colors">{{ $playlistTitle }}</h3>
                        <p class="text-sm text-spotify-text-gray truncate">Playlist</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Albums Section with stagger animation --}}
            @if(isset($newReleases) && $newReleases->count() > 0)
            <div class="px-6 py-8 animate-slide-up" style="animation-delay: 100ms">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-white to-spotify-text-gray bg-clip-text text-transparent">Yeni Albümler</h2>
                    <a href="/albums" class="text-sm font-bold text-spotify-text-gray hover:text-white hover:underline transition-all">Tümünü göster →</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach($newReleases->take(10) as $index => $album)
                    @php
                        $albumTitleJson = @json_decode($album->title);
                        $albumTitle = $albumTitleJson && isset($albumTitleJson->tr) ? $albumTitleJson->tr : $album->title;
                        $artistTitleJson = @json_decode($album->artist_title);
                        $artistTitle = $artistTitleJson && isset($artistTitleJson->tr) ? $artistTitleJson->tr : $album->artist_title;
                    @endphp
                    <div
                        class="bg-spotify-gray p-4 rounded-lg hover:bg-spotify-gray-light cursor-pointer group transition-all shadow-xl hover:shadow-2xl card-shine animate-scale-in"
                        @click="playAlbum({{ $album->album_id }})"
                        style="animation-delay: {{ $index * 50 }}ms"
                    >
                        <div class="relative mb-4">
                            <div class="w-full aspect-square rounded-lg bg-gradient-to-br from-blue-500 via-purple-600 to-pink-600 flex items-center justify-center text-5xl animate-gradient shadow-lg group-hover:shadow-2xl transition-shadow">
                                🎸
                            </div>
                            <button class="absolute bottom-2 right-2 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 shadow-2xl transition-all glow-on-hover">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>
                        <h3 class="font-bold text-white mb-2 truncate group-hover:text-spotify-green transition-colors">{{ $albumTitle }}</h3>
                        <p class="text-sm text-spotify-text-gray truncate">{{ $artistTitle }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Popular Songs with row hover --}}
            @if(isset($popularSongs) && $popularSongs->count() > 0)
            <div class="px-6 py-8 animate-slide-up" style="animation-delay: 200ms">
                <h2 class="text-2xl font-bold mb-4 bg-gradient-to-r from-white to-spotify-text-gray bg-clip-text text-transparent">Popüler Şarkılar</h2>
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
                        class="grid grid-cols-[40px_1fr_60px_60px] md:grid-cols-[40px_50px_1fr_200px_60px_60px] gap-4 items-center px-4 py-3 hover:bg-spotify-green/10 cursor-pointer group border-l-2 border-transparent hover:border-spotify-green transition-all"
                    >
                        <div class="text-center text-sm text-spotify-text-gray group-hover:text-spotify-green transition-colors" @click="playSong({ id: {{ $song->song_id }}, title: '{{ addslashes($songTitle) }}' })">
                            <span class="group-hover:hidden">{{ $index + 1 }}</span>
                            <i class="fas fa-play text-spotify-green hidden group-hover:inline animate-pulse"></i>
                        </div>
                        <div class="hidden md:block w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded flex items-center justify-center text-xl shadow-lg group-hover:shadow-spotify-green/50 transition-shadow" @click="playSong({ id: {{ $song->song_id }}, title: '{{ addslashes($songTitle) }}' })">🎵</div>
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
                            <i class="fas fa-heart" :class="{ 'animate-pulse': isLiked({{ $song->song_id }}) }"></i>
                        </button>
                        <div class="text-sm text-spotify-text-gray text-right group-hover:text-white transition-colors">{{ gmdate('i:s', $song->duration ?? 0) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Genres with 3D effect --}}
            @if(isset($genres) && $genres->count() > 0)
            <div class="px-6 py-8 pb-20 animate-slide-up" style="animation-delay: 300ms">
                <h2 class="text-2xl font-bold mb-4 bg-gradient-to-r from-white to-spotify-text-gray bg-clip-text text-transparent">Türlere Göre Keşfet</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    @php
                        $genreColors = [
                            'from-pink-500 to-pink-700',
                            'from-purple-500 to-purple-700',
                            'from-blue-500 to-blue-700',
                            'from-cyan-500 to-cyan-700',
                            'from-teal-500 to-teal-700',
                            'from-green-500 to-green-700',
                            'from-orange-500 to-orange-700',
                            'from-red-500 to-red-700',
                        ];
                    @endphp
                    @foreach($genres as $index => $genre)
                    @php
                        $slugJson = @json_decode($genre->slug);
                        $slug = $slugJson && isset($slugJson->tr) ? $slugJson->tr : $genre->slug;
                        $titleJson = @json_decode($genre->title);
                        $title = $titleJson && isset($titleJson->tr) ? $titleJson->tr : $genre->title;
                    @endphp
                    <a
                        href="/genres/{{ $slug }}"
                        class="h-32 bg-gradient-to-br {{ $genreColors[$index % count($genreColors)] }} rounded-lg p-4 flex items-end cursor-pointer shadow-2xl hover:shadow-spotify-green/30 transition-all relative overflow-hidden group card-shine animate-scale-in"
                        style="animation-delay: {{ $index * 30 }}ms"
                    >
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition-colors"></div>
                        <h3 class="text-xl font-bold text-white relative z-10">{{ $title }}</h3>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
    </div>
    {{-- END ACTUAL CONTENT --}}
@endsection
