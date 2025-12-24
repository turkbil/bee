@props([
    'genre'
])

@php
    $cover = $genre->coverMedia ?? null;
    $coverUrl = $cover ? thumb($cover, 150, 150) : null;
    $genreUrl = '/genres/' . ($genre->slug ?? $genre->genre_id ?? $genre->id);

    // Rastgele gradient renkleri
    $gradients = [
        'from-purple-500 to-pink-500',
        'from-blue-500 to-cyan-500',
        'from-green-500 to-emerald-500',
        'from-orange-500 to-red-500',
        'from-indigo-500 to-purple-500',
        'from-pink-500 to-rose-500',
        'from-teal-500 to-green-500',
        'from-yellow-500 to-orange-500',
    ];
    $gradient = $gradients[($genre->id ?? 0) % count($gradients)];
@endphp

<a href="{{ $genreUrl }}"
   class="group relative flex items-center gap-3 p-3 rounded-xl bg-gradient-to-r {{ $gradient }} hover:scale-105 transition-transform"
   data-spa>

    {{-- Cover/Icon --}}
    <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-black/20">
        @if($cover)
            <img src="{{ $coverUrl }}" alt="{{ $genre->title }}" class="w-full h-full object-cover" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-music text-white/50 text-xl"></i>
            </div>
        @endif
    </div>

    {{-- Title --}}
    <span class="text-white font-bold text-sm truncate flex-1">{{ $genre->title }}</span>

    {{-- Arrow --}}
    <i class="fas fa-chevron-right text-white/50 group-hover:text-white transition"></i>
</a>
