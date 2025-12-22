<div class="px-6 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Sektörler</h1>
        <p class="text-gray-400">Müzik sektörlerini keşfet</p>
    </div>

    {{-- Sectors Grid --}}
    @if($sectors && $sectors->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            @foreach($sectors as $sector)
                <x-muzibu.sector-card :sector="$sector" />
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($sectors->hasPages())
            <div class="mt-8">
                {{ $sectors->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-20">
            <div class="mb-6">
                <i class="fas fa-th-large text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Henüz sektör yok</h3>
            <p class="text-gray-400">Yakında yeni sektörler eklenecek</p>
        </div>
    @endif
</div>
