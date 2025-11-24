@extends('themes.muzibu.layouts.app')

@section('content')
<!-- Playlist Header -->
<section class="relative h-80 mb-8 bg-gradient-to-b from-purple-900 via-purple-800 to-transparent">
    <div class="container mx-auto px-8 h-full flex items-end pb-8">
        <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=232&h=232&fit=crop"
             class="w-58 h-58 rounded-lg shadow-2xl mr-6">
        <div class="flex-1">
            <p class="text-sm font-semibold text-white mb-2">PLAYLIST</p>
            <h1 class="text-6xl font-black mb-4 text-white drop-shadow-2xl">
                {{ $playlist->title['tr'] ?? $playlist->title['en'] ?? 'Playlist' }}
            </h1>
            @if($playlist->description)
                <p class="text-lg text-white/90 mb-4">
                    {{ $playlist->description['tr'] ?? $playlist->description['en'] ?? '' }}
                </p>
            @endif
            <p class="text-sm text-white/70">{{ $songs->count() }} şarkı</p>
        </div>
    </div>
</section>

<!-- Player Controls -->
<section class="px-8 mb-8">
    <div class="flex items-center gap-6">
        <button @click="playPlaylist({{ $playlist->playlist_id }})"
                class="w-14 h-14 bg-spotify-green hover:bg-spotify-green-light rounded-full flex items-center justify-center hover:scale-105 transition-all shadow-lg">
            <i class="fas fa-play text-black text-xl ml-0.5"></i>
        </button>
        <button @click="toggleFavorite('playlist', {{ $playlist->playlist_id }})"
                class="text-gray-400 hover:text-white transition-all text-3xl">
            <i :class="isFavorite('playlist', {{ $playlist->playlist_id }}) ? 'fas fa-heart text-spotify-green' : 'far fa-heart'"></i>
        </button>
    </div>
</section>

<!-- Songs List -->
<section class="px-8 pb-12">
    <div class="bg-black/20 rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="grid grid-cols-[3rem_minmax(200px,4fr)_minmax(200px,2fr)_100px] gap-4 px-4 py-2 border-b border-white/10 text-sm text-gray-400">
            <div class="text-center">#</div>
            <div>BAŞLIK</div>
            <div>ALBÜM</div>
            <div class="text-right"><i class="far fa-clock"></i></div>
        </div>

        <!-- Song Rows -->
        @foreach($songs as $index => $song)
            <div class="grid grid-cols-[3rem_minmax(200px,4fr)_minmax(200px,2fr)_100px] gap-4 px-4 py-3 hover:bg-white/5 transition-all group cursor-pointer"
                 @click="playSong({{ $song->song_id }})">
                <!-- Position -->
                <div class="flex items-center justify-center">
                    <span class="text-gray-400 group-hover:hidden text-sm">{{ $index + 1 }}</span>
                    <i class="fas fa-play text-white hidden group-hover:inline-block"></i>
                </div>

                <!-- Title & Artist -->
                <div class="flex items-center gap-3 min-w-0">
                    <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=40&h=40&fit=crop"
                         class="w-10 h-10 rounded shadow-md">
                    <div class="min-w-0 flex-1">
                        <div class="text-white font-medium truncate">
                            {{ $song->song_title['tr'] ?? $song->song_title['en'] ?? 'Song' }}
                        </div>
                        <div class="text-sm text-gray-400 truncate">
                            {{ $song->artist_title['tr'] ?? $song->artist_title['en'] ?? '' }}
                        </div>
                    </div>
                </div>

                <!-- Album -->
                <div class="flex items-center text-sm text-gray-400 truncate">
                    {{ $song->album_title['tr'] ?? $song->album_title['en'] ?? '' }}
                </div>

                <!-- Duration & Actions -->
                <div class="flex items-center justify-end gap-4">
                    <button @click.stop="toggleFavorite('song', {{ $song->song_id }})"
                            class="opacity-0 group-hover:opacity-100 transition-opacity text-gray-400 hover:text-white">
                        <i :class="isFavorite('song', {{ $song->song_id }}) ? 'fas fa-heart text-spotify-green' : 'far fa-heart'"></i>
                    </button>
                    <span class="text-sm text-gray-400">
                        {{ floor($song->duration / 60) }}:{{ str_pad($song->duration % 60, 2, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection
