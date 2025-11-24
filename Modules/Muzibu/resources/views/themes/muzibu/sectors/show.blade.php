@extends('themes.muzibu.layouts.app')

@section('content')
<section class="relative h-64 mb-8 bg-gradient-to-b from-green-900 via-green-800 to-transparent">
    <div class="container mx-auto px-8 h-full flex flex-col justify-end pb-12">
        <p class="text-sm font-semibold text-white mb-2">SEKTÖR</p>
        <h1 class="text-6xl font-black mb-2 text-white drop-shadow-2xl">{{ $sector->title['tr'] ?? $sector->title['en'] ?? 'Sector' }}</h1>
    </div>
</section>

<section class="px-8 pb-12">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        @foreach($playlists as $playlist)
            <div class="group cursor-pointer" @click="playPlaylist({{ $playlist->playlist_id }})">
                <div class="relative bg-spotify-gray rounded-lg p-4 hover:bg-spotify-gray/80 transition-all mb-4">
                    <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=200&h=200&fit=crop" class="w-full aspect-square object-cover rounded-md mb-4 shadow-lg">
                    <div class="absolute bottom-6 right-6 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all shadow-2xl">
                        <i class="fas fa-play text-black ml-0.5"></i>
                    </div>
                    <h3 class="text-white font-bold text-base mb-2 truncate">{{ $playlist->title['tr'] ?? $playlist->title['en'] ?? 'Playlist' }}</h3>
                    <p class="text-sm text-gray-400">{{ $playlist->song_count }} şarkı</p>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection
