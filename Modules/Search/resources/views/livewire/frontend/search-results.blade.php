<div class="search-results-container">
    {{-- Arama Bar --}}
    <div class="sticky top-0 bg-white z-10 py-6 mb-8 shadow-sm">
        <input type="search"
               wire:model.live.debounce.300ms="query"
               placeholder="Ürün, kategori ara..."
               class="w-full px-6 py-4 text-lg border-2 rounded-lg focus:border-blue-500 focus:outline-none">

        @if($totalCount > 0)
            <div class="mt-2 text-sm text-gray-600">
                <strong>{{ number_format($totalCount) }}</strong> sonuç bulundu
                <span class="text-xs text-gray-400">({{ $responseTime }}ms)</span>
            </div>
        @endif
    </div>

    {{-- Sonuçlar --}}
    @if($totalCount > 0)
        <div class="grid gap-4">
            @foreach($results as $index => $item)
                <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition"
                     wire:click="trackClick({{ $item['id'] }}, '{{ $item['type'] }}', {{ $index }})">
                    <a href="{{ $item['url'] }}" class="flex gap-4">
                        @if($item['image'])
                            <img src="{{ $item['image'] }}" class="w-20 h-20 object-cover rounded">
                        @endif
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg">{!! $item['highlighted_title'] !!}</h3>
                            <p class="text-sm text-gray-600 mt-1">{!! $item['highlighted_description'] !!}</p>
                            <div class="mt-2 text-xs text-gray-500">
                                <span class="bg-gray-100 px-2 py-1 rounded">{{ $item['type_label'] }}</span>
                                @if($item['price'])
                                    <span class="ml-2 font-semibold text-green-600">{{ $item['price'] }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @elseif(strlen($query) >= 2)
        <div class="text-center py-12">
            <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
            <p class="text-xl text-gray-600">Sonuç bulunamadı</p>
        </div>
    @endif
</div>
