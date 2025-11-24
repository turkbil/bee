@extends('themes.muzibu.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="relative h-64 mb-8 bg-gradient-to-b from-purple-900 via-purple-800 to-transparent">
    <div class="container mx-auto px-8 h-full flex flex-col justify-end pb-12">
        <h1 class="text-5xl font-black mb-2 text-white drop-shadow-2xl">Playlistler</h1>
        <p class="text-lg text-white/90">İşletmeniz için özel olarak hazırlanmış müzik listeleri</p>
    </div>
</section>

<!-- Playlists Grid -->
<section class="px-8 pb-12">
    @if($playlists->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach($playlists as $playlist)
                <div class="group cursor-pointer" @click="playPlaylist({{ $playlist->playlist_id }})">
                    <div class="relative bg-spotify-gray rounded-lg p-4 hover:bg-spotify-gray/80 transition-all mb-4">
                        <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=200&h=200&fit=crop"
                             class="w-full aspect-square object-cover rounded-md mb-4 shadow-lg">

                        <!-- Play Button Overlay -->
                        <div class="absolute bottom-6 right-6 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all shadow-2xl">
                            <i class="fas fa-play text-black ml-0.5"></i>
                        </div>

                        <h3 class="text-white font-bold text-base mb-2 truncate">
                            {{ $playlist->title['tr'] ?? $playlist->title['en'] ?? 'Playlist' }}
                        </h3>
                        <p class="text-sm text-gray-400">{{ $playlist->song_count }} şarkı</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($playlists->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $playlists->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-20">
            <div class="w-20 h-20 bg-spotify-gray rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-list text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-white mb-3">Henüz playlist yok</h3>
            <p class="text-gray-400">Yakında yeni playlistler eklenecek</p>
        </div>
    @endif
</section>
@endsection
