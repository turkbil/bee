@extends('themes.muzibu.layouts.app')

@section('content')
<!-- Hero Carousel -->
<section class="relative h-96 mb-8 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-blue-900 via-purple-900 to-transparent"></div>
    <div class="relative z-10 container mx-auto px-8 h-full flex flex-col justify-end pb-12">
        <h1 class="text-6xl font-black mb-4 text-white drop-shadow-2xl">İşletmenize Telifsiz Müzik</h1>
        <p class="text-xl text-white/90 mb-6 max-w-2xl">Restoranınız, kafeniz, mağazanız için yasal ve profesyonel müzik çözümü. 7 gün ücretsiz deneyin.</p>
        <div class="flex gap-4">
            <button @click="playPlaylist(1)" class="px-8 py-4 bg-spotify-green hover:bg-spotify-green-light rounded-full font-bold text-black transition-all hover:scale-105 shadow-2xl">
                <i class="fas fa-play mr-2"></i>Hemen Dinle
            </button>
            <button @click="showAuthModal = 'register'" class="px-8 py-4 bg-white/10 backdrop-blur-md hover:bg-white/20 rounded-full font-bold text-white transition-all border-2 border-white/50">
                7 Gün Ücretsiz Başla
            </button>
        </div>
    </div>
</section>

<!-- Featured Playlists -->
<section class="px-8 mb-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-white">Öne Çıkan Playlistler</h2>
        <a href="/playlists" class="text-sm text-gray-400 hover:text-white transition-all font-semibold">Tümünü Gör</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        @foreach($featuredPlaylists as $playlist)
            @php
                $title = json_decode($playlist->title, true);
                $slug = json_decode($playlist->slug, true);
            @endphp
            <div class="group cursor-pointer" @click="playPlaylist({{ $playlist->playlist_id }})">
                <div class="relative bg-spotify-gray rounded-lg p-4 hover:bg-spotify-gray/80 transition-all mb-4">
                    <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=200&h=200&fit=crop"
                         class="w-full aspect-square object-cover rounded-md mb-4 shadow-lg">
                    <div class="absolute bottom-6 right-6 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all shadow-2xl">
                        <i class="fas fa-play text-black ml-0.5"></i>
                    </div>
                    <h3 class="text-white font-bold text-base mb-2 truncate">{{ $title['tr'] ?? $title['en'] ?? 'Playlist' }}</h3>
                    <p class="text-sm text-gray-400 line-clamp-2">{{ $playlist->song_count ?? 0 }} şarkı</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- New Releases -->
<section class="px-8 mb-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-white">Yeni Albümler</h2>
        <a href="/albums" class="text-sm text-gray-400 hover:text-white transition-all font-semibold">Tümünü Gör</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        @foreach($newReleases as $album)
            @php
                $title = json_decode($album->title, true);
                $artistTitle = json_decode($album->artist_title, true);
            @endphp
            <div class="group cursor-pointer" @click="playAlbum({{ $album->album_id }})">
                <div class="relative bg-spotify-gray rounded-lg p-4 hover:bg-spotify-gray/80 transition-all mb-4">
                    <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=200&h=200&fit=crop"
                         class="w-full aspect-square object-cover rounded-md mb-4 shadow-lg">
                    <div class="absolute bottom-6 right-6 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all shadow-2xl">
                        <i class="fas fa-play text-black ml-0.5"></i>
                    </div>
                    <h3 class="text-white font-bold text-base mb-2 truncate">{{ $title['tr'] ?? $title['en'] ?? 'Album' }}</h3>
                    <p class="text-sm text-gray-400 truncate">{{ $artistTitle['tr'] ?? $artistTitle['en'] ?? '' }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- Popular Songs -->
<section class="px-8 mb-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-white">Popüler Şarkılar</h2>
    </div>

    <div class="bg-spotify-gray/30 rounded-lg overflow-hidden">
        @foreach($popularSongs->take(10) as $index => $song)
            @php
                $songTitle = json_decode($song->song_title, true);
                $albumTitle = json_decode($song->album_title, true);
                $artistTitle = json_decode($song->artist_title, true);
            @endphp
            <div class="flex items-center gap-4 p-4 hover:bg-white/5 transition-all group cursor-pointer"
                 @click="playSong({{ $song->song_id }})">
                <div class="w-8 text-center">
                    <span class="text-gray-400 group-hover:hidden text-sm">{{ $index + 1 }}</span>
                    <i class="fas fa-play text-white hidden group-hover:inline-block"></i>
                </div>
                <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=48&h=48&fit=crop"
                     class="w-12 h-12 rounded shadow-md">
                <div class="flex-1 min-w-0">
                    <div class="text-white font-medium truncate">{{ $songTitle['tr'] ?? $songTitle['en'] ?? 'Song' }}</div>
                    <div class="text-sm text-gray-400 truncate">{{ $artistTitle['tr'] ?? $artistTitle['en'] ?? '' }}</div>
                </div>
                <div class="text-sm text-gray-400 hidden md:block truncate max-w-xs">
                    {{ $albumTitle['tr'] ?? $albumTitle['en'] ?? '' }}
                </div>
                <div class="text-sm text-gray-400">
                    {{ floor($song->duration / 60) }}:{{ str_pad($song->duration % 60, 2, '0', STR_PAD_LEFT) }}
                </div>
                <button @click.stop="toggleFavorite('song', {{ $song->song_id }})"
                        class="opacity-0 group-hover:opacity-100 transition-opacity text-gray-400 hover:text-white">
                    <i :class="isFavorite('song', {{ $song->song_id }}) ? 'fas fa-heart text-spotify-green' : 'far fa-heart'"></i>
                </button>
            </div>
        @endforeach
    </div>
</section>

<!-- Genres -->
<section class="px-8 mb-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-white">Türlere Göre Keşfet</h2>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($genres as $genre)
            @php
                $title = json_decode($genre->title, true);
                $colors = ['bg-blue-600', 'bg-purple-600', 'bg-pink-600', 'bg-orange-600', 'bg-green-600', 'bg-red-600'];
                $color = $colors[array_rand($colors)];
            @endphp
            <a href="/genres/{{ $genre->genre_id }}"
               class="relative h-32 rounded-lg {{ $color }} overflow-hidden group hover:scale-105 transition-all shadow-lg">
                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-all"></div>
                <div class="relative z-10 p-4 h-full flex items-end">
                    <h3 class="text-white font-bold text-xl">{{ $title['tr'] ?? $title['en'] ?? 'Genre' }}</h3>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endsection
