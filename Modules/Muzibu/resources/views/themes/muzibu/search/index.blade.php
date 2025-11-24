@extends('themes.muzibu.layouts.app')

@section('title', 'Ara - Muzibu')

@section('content')
    <div class="px-8 py-6">
        <!-- Hero Search -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-6">Arama Sonuçları</h1>
            <form method="GET" action="/search" class="relative max-w-2xl">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="search" name="q" value="{{ $query }}" placeholder="Şarkı, albüm, sanatçı ara..." class="w-full pl-12 pr-4 py-4 bg-spotify-gray text-white rounded-full focus:outline-none focus:ring-2 focus:ring-spotify-green transition-all font-medium">
            </form>
        </div>

        @if($query)
            <!-- Songs -->
            @if(count($results['songs']) > 0)
                <section class="mb-12">
                    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-music text-spotify-green"></i> Şarkılar
                    </h2>
                    <div class="space-y-2">
                        @foreach($results['songs'] as $song)
                            <div class="flex items-center gap-4 p-4 rounded-lg hover:bg-spotify-gray transition-all group">
                                <button @click="playSong({{ $song->song_id }})" class="w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center hover:scale-105 transition-all shadow-lg opacity-0 group-hover:opacity-100">
                                    <i class="fas fa-play text-black ml-0.5"></i>
                                </button>
                                <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=56&h=56&fit=crop" class="w-14 h-14 rounded shadow-md">
                                <div class="flex-1">
                                    <div class="font-semibold text-white">{{ $song->song_title['tr'] ?? 'Untitled' }}</div>
                                    <div class="text-sm text-gray-400">{{ $song->artist_title['tr'] ?? 'Unknown' }} • {{ $song->album_title['tr'] ?? 'Unknown' }}</div>
                                </div>
                                <div class="text-sm text-gray-400">{{ gmdate('i:s', $song->duration ?? 0) }}</div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Albums -->
            @if(count($results['albums']) > 0)
                <section class="mb-12">
                    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-record-vinyl text-spotify-green"></i> Albümler
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                        @foreach($results['albums'] as $album)
                            <a href="/albums/{{ $album->album_id }}" class="block group">
                                <div class="relative mb-3">
                                    <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=200&h=200&fit=crop" class="w-full aspect-square object-cover rounded-lg shadow-lg">
                                    <button @click.prevent="playAlbum({{ $album->album_id }})" class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-all rounded-lg">
                                        <div class="w-14 h-14 bg-spotify-green rounded-full flex items-center justify-center hover:scale-110 transition-all shadow-2xl">
                                            <i class="fas fa-play text-black text-xl ml-1"></i>
                                        </div>
                                    </button>
                                </div>
                                <h3 class="font-semibold text-white truncate">{{ $album->album_title['tr'] ?? 'Untitled' }}</h3>
                                <p class="text-sm text-gray-400 truncate">{{ $album->artist_title['tr'] ?? 'Unknown' }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Playlists -->
            @if(count($results['playlists']) > 0)
                <section class="mb-12">
                    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-list text-spotify-green"></i> Playlistler
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                        @foreach($results['playlists'] as $playlist)
                            <a href="/playlists/{{ $playlist->playlist_id }}" class="block group">
                                <div class="relative mb-3">
                                    <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=200&h=200&fit=crop" class="w-full aspect-square object-cover rounded-lg shadow-lg">
                                    <button @click.prevent="playPlaylist({{ $playlist->playlist_id }})" class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-all rounded-lg">
                                        <div class="w-14 h-14 bg-spotify-green rounded-full flex items-center justify-center hover:scale-110 transition-all shadow-2xl">
                                            <i class="fas fa-play text-black text-xl ml-1"></i>
                                        </div>
                                    </button>
                                </div>
                                <h3 class="font-semibold text-white truncate">{{ $playlist->title['tr'] ?? 'Untitled' }}</h3>
                                <p class="text-sm text-gray-400 truncate">{{ count($playlist->description ?? []) }} şarkı</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Artists -->
            @if(count($results['artists']) > 0)
                <section class="mb-12">
                    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-user-circle text-spotify-green"></i> Sanatçılar
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                        @foreach($results['artists'] as $artist)
                            <a href="/artists/{{ $artist->artist_id }}" class="block group text-center">
                                <div class="relative mb-3">
                                    <img src="https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=200&h=200&fit=crop" class="w-full aspect-square object-cover rounded-full shadow-lg mx-auto">
                                </div>
                                <h3 class="font-semibold text-white truncate">{{ $artist->title['tr'] ?? 'Unknown' }}</h3>
                                <p class="text-sm text-gray-400">Sanatçı</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if(count($results['songs']) == 0 && count($results['albums']) == 0 && count($results['playlists']) == 0 && count($results['artists']) == 0)
                <div class="text-center py-20">
                    <i class="fas fa-search text-6xl text-gray-600 mb-4"></i>
                    <h2 class="text-2xl font-bold text-gray-400 mb-2">Sonuç Bulunamadı</h2>
                    <p class="text-gray-500">"{{ $query }}" için herhangi bir sonuç bulunamadı.</p>
                </div>
            @endif
        @else
            <div class="text-center py-20">
                <i class="fas fa-search text-6xl text-gray-600 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-400 mb-2">Arama Yapın</h2>
                <p class="text-gray-500">Şarkı, albüm, playlist veya sanatçı arayın.</p>
            </div>
        @endif
    </div>
@endsection
