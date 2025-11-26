@extends('themes.muzibu.layouts.app')

@section('content')
<section class="relative h-64 mb-8 bg-gradient-to-b from-pink-900 via-pink-800 to-transparent">
    <div class="container mx-auto px-8 h-full flex flex-col justify-end pb-12">
        <p class="text-sm font-semibold text-white mb-2">TÜRE GÖRE</p>
        <h1 class="text-6xl font-black mb-2 text-white drop-shadow-2xl">{{ $genre->title['tr'] ?? $genre->title['en'] ?? 'Genre' }}</h1>
    </div>
</section>

<section class="px-8 pb-12">
    @if($songs->count() > 0)
        <div class="space-y-2">
            @foreach($songs as $index => $song)
                <div class="flex items-center gap-4 px-4 py-3 rounded hover:bg-white/5 transition-all group cursor-pointer" @click="playSong({{ $song->song_id }})">
                    <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=48&h=48&fit=crop" class="w-12 h-12 rounded shadow-md">
                    <div class="flex-1 min-w-0">
                        <div class="text-white font-medium truncate">{{ $song->song_title['tr'] ?? $song->song_title['en'] ?? 'Song' }}</div>
                        <div class="text-sm text-gray-400 truncate">{{ $song->artist_title['tr'] ?? $song->artist_title['en'] ?? '' }}</div>
                    </div>
                    <span class="text-sm text-gray-400">{{ floor($song->duration / 60) }}:{{ str_pad($song->duration % 60, 2, '0', STR_PAD_LEFT) }}</span>
                </div>
            @endforeach
        </div>

        @if($songs->hasPages())
            <div class="mt-8 flex justify-center">{{ $songs->links() }}</div>
        @endif
    @endif
</section>
@endsection
