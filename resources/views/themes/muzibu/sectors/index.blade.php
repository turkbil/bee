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
        const cards = document.querySelectorAll('[data-sector-id]');
        const visibleCount = Math.min(cards.length, 6);
        cards.forEach((card, i) => {
            if (i >= visibleCount) return;
            const id = card.dataset.sectorId;
            if (id && window.Alpine?.store('sidebar')?.prefetch) {
                setTimeout(() => {
                    window.Alpine.store('sidebar').prefetch('sector', parseInt(id));
                }, i * 150);
            }
        });
    }, 300);
});
</script>

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
                <a href="{{ route('muzibu.sectors.show', $sector->getTranslation('slug', app()->getLocale())) }}"
                   @mouseenter="$store.sidebar.prefetch('sector', {{ $sector->sector_id }})"
                   class="sector-card group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-4 transition-all duration-300"
                   data-sector-id="{{ $sector->sector_id }}"
                   data-sector-title="{{ $sector->getTranslation('title', app()->getLocale()) }}"
                   data-is-favorite="{{ auth()->check() && $sector->isFavoritedBy(auth()->user()) ? '1' : '0' }}">
                    <div class="relative mb-4">
                        @if($sector->media_id && $sector->iconMedia)
                            <img src="{{ thumb($sector->iconMedia, 300, 300, ['scale' => 1]) }}"
                                 alt="{{ $sector->getTranslation('title', app()->getLocale()) }}"
                                 class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                 loading="lazy">
                        @else
                            <div class="w-full aspect-square bg-gradient-to-br from-pink-500 to-rose-600 rounded-lg flex items-center justify-center text-4xl shadow-lg">
                                🎭
                            </div>
                        @endif

                        {{-- Play Button - Spotify Style Bottom Right --}}
                        <button @click.stop.prevent="
                            $store.player.setPlayContext({
                                type: 'sector',
                                id: {{ $sector->sector_id }},
                                name: '{{ addslashes($sector->getTranslation('title', app()->getLocale())) }}'
                            });
                            playSector({{ $sector->sector_id }});
                        " class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-xl hover:scale-110 hover:bg-green-500">
                            <i class="fas fa-play ml-1"></i>
                        </button>

                        {{-- 3-Dot Menu Button (Cover Sağ Üst) - HOVER'DA GÖRÜNÜR --}}
                        <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop.prevent>
                            <button @click="Alpine.store('contextMenu').openContextMenu($event, 'sector', {
                                id: {{ $sector->sector_id }},
                                title: '{{ addslashes($sector->getTranslation('title', app()->getLocale())) }}',
                                is_favorite: {{ auth()->check() && $sector->isFavoritedBy(auth()->user()) ? 'true' : 'false' }}
                            })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <h3 class="font-semibold text-white mb-1 truncate">
                        {{ $sector->getTranslation('title', app()->getLocale()) }}
                    </h3>

                    @if($sector->description)
                        <p class="text-sm text-gray-400 truncate">
                            {{ $sector->getTranslation('description', app()->getLocale()) }}
                        </p>
                    @endif
                </a>
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
@endsection
