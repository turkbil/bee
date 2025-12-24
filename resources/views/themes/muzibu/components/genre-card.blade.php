@props([
    'genre',
    'preview' => false,
    'size' => 'normal'
])

@php
    $cover = $genre->coverMedia ?? null;
    $coverUrl = $cover ? thumb($cover, 300, 300) : '/images/default-genre.png';
    $genreUrl = '/genres/' . ($genre->slug ?? $genre->genre_id ?? $genre->id);
    $songsCount = $genre->songs_count ?? 0;

    // Rastgele gradient renkleri
    $gradients = [
        'from-purple-500 to-pink-500',
        'from-blue-500 to-cyan-500',
        'from-green-500 to-emerald-500',
        'from-orange-500 to-red-500',
        'from-indigo-500 to-purple-500',
        'from-pink-500 to-rose-500',
    ];
    $gradient = $gradients[($genre->id ?? 0) % count($gradients)];

    $sizeClasses = [
        'small' => 'w-32 sm:w-36',
        'normal' => 'w-40 sm:w-44',
        'large' => 'w-48 sm:w-56'
    ];
    $cardSize = $sizeClasses[$size] ?? $sizeClasses['normal'];
@endphp

<div class="group flex-shrink-0 {{ $cardSize }} snap-start">
    <a href="{{ $genreUrl }}" class="block" data-spa>
        {{-- Cover --}}
        <div class="relative aspect-square rounded-xl overflow-hidden mb-3">
            @if($cover)
                <img src="{{ $coverUrl }}" alt="{{ $genre->title }}"
                     class="w-full h-full object-cover transition group-hover:scale-105"
                     loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
            @else
                <div class="w-full h-full bg-gradient-to-br {{ $gradient }} flex items-center justify-center">
                    <i class="fas fa-music text-white/30 text-4xl"></i>
                </div>
            @endif

            {{-- Play Button Overlay --}}
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                <button @click.prevent="playGenre({{ $genre->genre_id ?? $genre->id }})"
                        class="w-12 h-12 bg-green-500 hover:bg-green-400 rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition">
                    <i class="fas fa-play text-black text-lg ml-0.5"></i>
                </button>
            </div>

            {{-- Title Overlay --}}
            <div class="absolute bottom-0 left-0 right-0 p-3">
                <h3 class="text-white font-bold text-lg drop-shadow-lg">{{ $genre->title }}</h3>
            </div>
        </div>
    </a>
</div>
