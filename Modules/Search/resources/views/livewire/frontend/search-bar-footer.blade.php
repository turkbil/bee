<div class="relative mb-6" x-data="{ open: @entangle('isOpen') }" @click.away="$wire.closeDropdown()">
    <div class="flex gap-3">
        <div class="flex-1 relative">
            <i class="fa-solid fa-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 text-xl z-10"></i>
            <input type="search"
                   wire:model.live.debounce.300ms="query"
                   placeholder="Ürün, kategori veya marka arayın..."
                   class="w-full bg-white border-0 rounded-2xl pl-16 pr-6 py-5 text-gray-900 text-lg focus:outline-none focus:ring-4 focus:ring-white/30 transition-shadow">
        </div>
        <a href="{{ route('search.show', ['query' => $query]) }}"
           class="bg-white text-indigo-600 px-8 md:px-10 py-5 rounded-2xl font-bold text-lg hover:shadow-2xl transition-all flex items-center gap-2">
            <i class="fa-solid fa-search"></i>
            <span class="hidden md:inline">Ara</span>
        </a>
    </div>

    @if($isOpen && count($this->results) > 0)
        <div class="absolute top-full left-0 right-0 mt-2 bg-white shadow-xl rounded-lg z-50 max-h-96 overflow-y-auto border border-gray-200">
            @foreach($this->results as $index => $item)
                <a href="{{ $item['url'] }}"
                   @click="$wire.trackClick({{ $item['id'] }}, '{{ $item['type'] }}', {{ $index }})"
                   class="block p-3 hover:bg-gray-100 border-b border-gray-200 last:border-b-0 transition">
                    <div class="font-medium text-gray-900">{!! $item['highlighted_title'] !!}</div>
                    <div class="text-sm text-gray-500">{{ $item['type_label'] }}</div>
                </a>
            @endforeach

            @if(strlen($query) >= 2)
                <a href="{{ route('search.show', ['query' => $query]) }}"
                   class="block p-3 text-center text-indigo-600 hover:bg-gray-100 font-medium transition">
                    Tüm sonuçları gör →
                </a>
            @endif
        </div>
    @endif
</div>
