@extends('themes.muzibu.layouts.app')

@section('content')
{{-- 🎯 Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
</script>

<div class="px-6 py-8">
    {{-- Header --}}
    <div class="mb-8 animate-slide-up">
        <h1 class="text-4xl font-bold text-white mb-2">Türler</h1>
        <p class="text-gray-400">Müzik türlerini keşfet</p>
    </div>

    {{-- Genres Grid --}}
    @if($genres && $genres->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 animate-slide-up" style="animation-delay: 100ms">
            @foreach($genres as $genre)
                <a href="{{ route('muzibu.genres.show', $genre->getTranslation('slug', app()->getLocale())) }}"
                   class="group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-4 transition-all duration-300">
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

                        {{-- Play Button Overlay --}}
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                            <button @click.stop="
                                $store.player.setPlayContext({
                                    type: 'genre',
                                    id: {{ $genre->genre_id }},
                                    name: '{{ addslashes($genre->getTranslation('title', app()->getLocale())) }}'
                                });
                                playGenres({{ $genre->genre_id }});
                            " class="opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:scale-110">
                                <i class="fas fa-play ml-1"></i>
                            </button>
                        </div>

                        {{-- Favorite Button --}}
                        <div class="absolute top-2 right-2" x-on:click.stop>
                            <x-common.favorite-button :model="$genre" size="sm" iconOnly="true" />
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
