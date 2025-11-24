@extends('themes.muzibu.layouts.app')

@section('content')
<section class="relative h-80 mb-8 bg-gradient-to-b from-blue-900 via-blue-800 to-transparent">
    <div class="container mx-auto px-8 h-full flex items-end pb-8">
        <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=232&h=232&fit=crop" class="w-58 h-58 rounded-lg shadow-2xl mr-6">
        <div class="flex-1">
            <p class="text-sm font-semibold text-white mb-2">ALBÜM</p>
            <h1 class="text-6xl font-black mb-4 text-white drop-shadow-2xl">{{ $album->title['tr'] ?? $album->title['en'] ?? 'Album' }}</h1>
            <p class="text-lg text-white/90">{{ $album->artist_title['tr'] ?? $album->artist_title['en'] ?? '' }}</p>
            <p class="text-sm text-white/70 mt-2">{{ $songs->count() }} şarkı</p>
        </div>
    </div>
</section>

<section class="px-8 mb-8">
    <button @click="playAlbum({{ $album->album_id }})" class="w-14 h-14 bg-spotify-green hover:bg-spotify-green-light rounded-full flex items-center justify-center hover:scale-105 transition-all shadow-lg">
        <i class="fas fa-play text-black text-xl ml-0.5"></i>
    </button>
</section>

<section class="px-8 pb-12">
    <div class="space-y-2">
        @foreach($songs as $index => $song)
            <div class="flex items-center gap-4 px-4 py-3 rounded hover:bg-white/5 transition-all group cursor-pointer" @click="playSong({{ $song->song_id }})">
                <span class="text-gray-400 w-8 text-center">{{ $index + 1 }}</span>
                <div class="flex-1">
                    <div class="text-white">{{ $song->title['tr'] ?? $song->title['en'] ?? 'Song' }}</div>
                </div>
                <span class="text-sm text-gray-400">{{ floor($song->duration / 60) }}:{{ str_pad($song->duration % 60, 2, '0', STR_PAD_LEFT) }}</span>
            </div>
        @endforeach
    </div>
</section>
@endsection
