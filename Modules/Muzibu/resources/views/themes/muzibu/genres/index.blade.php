@extends('themes.muzibu.layouts.app')

@section('content')
<section class="relative h-64 mb-8 bg-gradient-to-b from-pink-900 via-pink-800 to-transparent">
    <div class="container mx-auto px-8 h-full flex flex-col justify-end pb-12">
        <h1 class="text-5xl font-black mb-2 text-white drop-shadow-2xl">Müzik Türleri</h1>
        <p class="text-lg text-white/90">Tarzına göre müzik keşfet</p>
    </div>
</section>

<section class="px-8 pb-12">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($genres as $genre)
            @php $colors = ['bg-blue-600', 'bg-purple-600', 'bg-pink-600', 'bg-orange-600', 'bg-green-600', 'bg-red-600']; $color = $colors[array_rand($colors)]; @endphp
            <a href="/genres/{{ $genre->genre_id }}" class="relative h-32 rounded-lg {{ $color }} overflow-hidden group hover:scale-105 transition-all shadow-lg">
                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-all"></div>
                <div class="relative z-10 p-4 h-full flex flex-col justify-between">
                    <h3 class="text-white font-bold text-xl">{{ $genre->title['tr'] ?? $genre->title['en'] ?? 'Genre' }}</h3>
                    <p class="text-white/80 text-sm">{{ $genre->song_count }} şarkı</p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endsection
