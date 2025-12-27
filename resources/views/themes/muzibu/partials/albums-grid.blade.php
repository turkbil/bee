<div class="px-4 py-6 sm:px-6 sm:py-8">
    {{-- Header --}}
    <div class="mb-4 sm:mb-6">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-0.5">Albümler</h1>
        <p class="text-gray-400 text-sm sm:text-base">En yeni ve popüler albümler</p>
    </div>

    {{-- Albums Grid --}}
    @if($albums && $albums->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
            @foreach($albums as $album)
                <x-muzibu.album-card :album="$album" :preview="true" />
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($albums->hasPages())
            <div class="mt-8">
                {{ $albums->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-20">
            <div class="mb-6">
                <i class="fas fa-record-vinyl text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Henüz albüm yok</h3>
            <p class="text-gray-400">Yakında yeni albümler eklenecek</p>
        </div>
    @endif
</div>
