<div class="relative" x-data="{ open: @entangle('isOpen') }" @click.away="$wire.closeDropdown()">
    <div class="relative">
        <input type="search"
               wire:model.live.debounce.300ms="query"
               placeholder="Ürün, kategori veya marka arayın..."
               class="w-full bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 rounded-full px-6 py-3 pl-12 pr-24 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 dark:text-blue-400"></i>
        <a href="{{ route('search.show', ['query' => $query]) }}"
           class="absolute right-2 top-1/2 -translate-y-1/2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-full hover:from-blue-700 hover:to-purple-700 transition">
            Ara
        </a>
    </div>

    @if($isOpen && count($this->results) > 0)
        <div class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 shadow-xl rounded-lg z-50 max-h-96 overflow-y-auto border border-gray-200 dark:border-gray-700">
            @foreach($this->results as $index => $item)
                <a href="{{ $item['url'] }}"
                   @click="$wire.trackClick({{ $item['id'] }}, '{{ $item['type'] }}', {{ $index }})"
                   class="block p-3 hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700 last:border-b-0 transition">
                    <div class="font-medium text-gray-900 dark:text-white">{!! $item['highlighted_title'] !!}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $item['type_label'] }}</div>
                </a>
            @endforeach

            @if(strlen($query) >= 2)
                <a href="{{ route('search.show', ['query' => $query]) }}"
                   class="block p-3 text-center text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium transition">
                    Tüm sonuçları gör →
                </a>
            @endif
        </div>
    @endif
</div>
