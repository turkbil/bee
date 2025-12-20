@extends('themes.muzibu.layouts.app')

@section('content')
<section class="relative h-80 mb-8 bg-gradient-to-b from-blue-900 via-blue-800 to-transparent">
    <div class="container mx-auto px-8 h-full flex items-end pb-8">
        @if($album->coverMedia)
            <img src="{{ thumb($album->coverMedia, 232, 232) }}" alt="{{ $album->getTranslation('title', app()->getLocale()) }}" class="w-58 h-58 rounded-lg shadow-2xl mr-6">
        @else
            <div class="w-58 h-58 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center shadow-2xl mr-6">
                <span class="text-6xl">💿</span>
            </div>
        @endif
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
    <div class="space-y-1">
        @foreach($songs as $index => $song)
            <x-muzibu.song-row :song="$song" :index="$index" :show-album="false" />
        @endforeach
    </div>
</section>
@endsection
