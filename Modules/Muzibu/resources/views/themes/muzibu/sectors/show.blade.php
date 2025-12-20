@extends('themes.muzibu.layouts.app')

@section('content')
<section class="relative h-64 mb-8 bg-gradient-to-b from-green-900 via-green-800 to-transparent">
    <div class="container mx-auto px-8 h-full flex flex-col justify-end pb-12">
        <p class="text-sm font-semibold text-white mb-2">SEKTÖR</p>
        <h1 class="text-6xl font-black mb-2 text-white drop-shadow-2xl">{{ $sector->title['tr'] ?? $sector->title['en'] ?? 'Sector' }}</h1>
    </div>
</section>

<section class="px-8 pb-12">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @foreach($playlists as $playlist)
            <x-muzibu.playlist-card :playlist="$playlist" />
        @endforeach
    </div>
</section>
@endsection
