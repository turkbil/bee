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
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach($playlists as $playlist)
                <x-muzibu.playlist-card :playlist="$playlist" :preview="true" />
            @endforeach
        </div>

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
