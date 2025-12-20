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
        <div class="space-y-1">
            @foreach($songs as $index => $song)
                <x-muzibu.song-row :song="$song" :index="$index" :show-album="true" />
            @endforeach
        </div>

        @if($songs->hasPages())
            <div class="mt-8 flex justify-center">{{ $songs->links() }}</div>
        @endif
    @else
        <div class="text-center py-20">
            <div class="mb-6">
                <i class="fas fa-music text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Henüz şarkı yok</h3>
            <p class="text-gray-400">Bu türde şarkı bulunmuyor</p>
        </div>
    @endif
</section>
@endsection
