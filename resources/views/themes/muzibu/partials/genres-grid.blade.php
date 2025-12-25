<div class="px-6 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Müzik Türleri</h1>
        <p class="text-gray-400">Favori müzik türlerini keşfet</p>
    </div>

    {{-- Genres Grid --}}
    @if($genres && $genres->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            @foreach($genres as $genre)
                <x-muzibu.genre-card :genre="$genre" :preview="true" :compact="false" />
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
                <i class="fas fa-guitar text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Henüz müzik türü yok</h3>
            <p class="text-gray-400">Yakında yeni müzik türleri eklenecek</p>
        </div>
    @endif
</div>
