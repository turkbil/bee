@extends('themes.muzibu.layouts.app')

@section('content')
{{-- 🎯 Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}

// 🚀 Auto-prefetch visible items on page load (staggered to avoid server overload)
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        const cards = document.querySelectorAll('[data-genre-id]');
        const visibleCount = Math.min(cards.length, 6);
        cards.forEach((card, i) => {
            if (i >= visibleCount) return;
            const id = card.dataset.genreId;
            if (id && window.Alpine?.store('sidebar')?.prefetch) {
                setTimeout(() => {
                    window.Alpine.store('sidebar').prefetch('genre', parseInt(id));
                }, i * 150);
            }
        });
    }, 300);
});
</script>

<div class="px-6 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Türler</h1>
        <p class="text-gray-400">Müzik türlerini keşfet</p>
    </div>

    {{-- Genres Grid --}}
    @if($genres && $genres->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            @foreach($genres as $genre)
                <a href="{{ route('muzibu.genres.show', $genre->getTranslation('slug', app()->getLocale())) }}"
                   @mouseenter="$store.sidebar.prefetch('genre', {{ $genre->genre_id }})"
                   class="genre-card group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-4 transition-all duration-300"
                   data-genre-id="{{ $genre->genre_id }}"
                   data-genre-title="{{ $genre->getTranslation('title', app()->getLocale()) }}"
                   data-is-favorite="{{ auth()->check() && $genre->isFavoritedBy(auth()->user()) ? '1' : '0' }}">
                    <div class="relative mb-4">
                        @if($genre->media_id && $genre->iconMedia)
                            <img src="{{ thumb($genre->iconMedia, 300, 300, ['scale' => 1]) }}"
                                 alt="{{ $genre->getTranslation('title', app()->getLocale()) }}"
                                 class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                 loading="lazy">
                        @else
                            <div class="w-full aspect-square bg-gradient-to-br from-green-500 to-teal-600 rounded-lg flex items-center justify-center text-4xl shadow-lg">
                                🎸
                            </div>
                        @endif

                        {{-- Play Button - Spotify Style Bottom Right --}}
                        <button @click.stop.prevent="
                            $store.player.setPlayContext({
                                type: 'genre',
                                id: {{ $genre->genre_id }},
                                name: '{{ addslashes($genre->getTranslation('title', app()->getLocale())) }}'
                            });
                            playGenres({{ $genre->genre_id }});
                        " class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-xl hover:scale-110 hover:bg-green-500">
                            <i class="fas fa-play ml-1"></i>
                        </button>

                        {{-- 3-Dot Menu Button (Cover Sağ Üst) - HOVER'DA GÖRÜNÜR --}}
                        <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop.prevent>
                            <button @click="Alpine.store('contextMenu').openContextMenu($event, 'genre', {
                                id: {{ $genre->genre_id }},
                                title: '{{ addslashes($genre->getTranslation('title', app()->getLocale())) }}',
                                is_favorite: {{ auth()->check() && $genre->isFavoritedBy(auth()->user()) ? 'true' : 'false' }}
                            })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <h3 class="font-semibold text-white mb-1 truncate">
                        {{ $genre->getTranslation('title', app()->getLocale()) }}
                    </h3>

                    @if($genre->description)
                        <p class="text-sm text-gray-400 truncate">
                            {{ $genre->getTranslation('description', app()->getLocale()) }}
                        </p>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($genres->hasPages())
            <div class="mt-8">
                {{ $genres->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-20">
            <div class="mb-6">
                <i class="fas fa-music text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Henüz tür yok</h3>
            <p class="text-gray-400">Yakında yeni türler eklenecek</p>
        </div>
    @endif
</div>
@endsection
