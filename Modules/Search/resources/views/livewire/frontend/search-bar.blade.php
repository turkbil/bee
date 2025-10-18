<div class="relative" x-data="{ open: @entangle('isOpen') }" @click.away="$wire.closeDropdown()">
    <input type="search"
           wire:model.live.debounce.300ms="query"
           placeholder="Ara..."
           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">

    @if($isOpen && count($results) > 0)
        <div class="absolute top-full left-0 right-0 mt-2 bg-white shadow-xl rounded-lg z-50 max-h-96 overflow-y-auto">
            @foreach($results as $index => $item)
                <a href="{{ $item['url'] }}"
                   wire:click="selectResult({{ $index }})"
                   class="block p-3 hover:bg-gray-100 border-b">
                    <div class="font-medium">{!! $item['highlighted_title'] !!}</div>
                    <div class="text-sm text-gray-500">{{ $item['type_label'] }}</div>
                </a>
            @endforeach
        </div>
    @endif
</div>
