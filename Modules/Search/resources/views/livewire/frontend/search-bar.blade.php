@php
    $searchShowRoute = route('search.show', ['query' => '_PLACEHOLDER_']);
@endphp

<div class="relative"
     x-data="{
         query: @entangle('query').live,
         open: @entangle('isOpen').live,
         keywords: [],
         products: [],
         total: 0,
         loading: false,
         debounceTimer: null,
         searchShowUrl: '{{ $searchShowRoute }}',

         showEmptyState() {
             return this.open && !this.loading && (this.keywords.length === 0 && this.products.length === 0) && this.query?.trim().length >= 2;
         }
     }"
     @click.away="open = false">

    <div class="relative">
        <input type="search"
               x-model="query"
               @keydown.enter.prevent="if(query?.trim()) window.location.href=searchShowUrl.replace('_PLACEHOLDER_', encodeURIComponent(query))"
               placeholder="Ürün, kategori veya marka arayın..."
               class="w-full bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 rounded-full px-6 py-3 pl-12 pr-24 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 dark:text-blue-400"></i>
        <a :href="searchShowUrl.replace('_PLACEHOLDER_', encodeURIComponent(query || ''))"
           class="absolute right-2 top-1/2 -translate-y-1/2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-full hover:from-blue-700 hover:to-purple-700 transition">
            Ara
        </a>
    </div>

    @if($isOpen && count($this->results) > 0)
        <div class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 shadow-xl rounded-xl z-50 max-h-[32rem] overflow-y-auto border border-gray-200 dark:border-gray-700"
             style="z-index:50;">
            @foreach($this->results as $index => $item)
                <a href="{{ $item['url'] }}"
                   @click="$wire.trackClick({{ $item['id'] }}, '{{ $item['type'] }}', {{ $index }})"
                   class="flex items-start gap-3 md:gap-4 p-3 md:p-4 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition group">

                    {{-- Image --}}
                    @if(!empty($item['image']))
                        <div class="w-12 h-12 md:w-16 md:h-16 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                            <img src="{{ $item['image'] }}"
                                 alt="{{ $item['title'] }}"
                                 class="w-full h-full object-cover"
                                 loading="lazy">
                        </div>
                    @else
                        <div class="w-12 h-12 md:w-16 md:h-16 flex-shrink-0 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 dark:text-gray-500 text-lg md:text-2xl"></i>
                        </div>
                    @endif

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm md:text-base text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition mb-1 line-clamp-1">
                            {!! $item['highlighted_title'] !!}
                        </div>

                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                {{ $item['type_label'] }}
                            </span>
                            @if(!empty($item['price']))
                                <span class="text-xs md:text-sm font-bold text-blue-600 dark:text-blue-400">{{ $item['price'] }}</span>
                            @endif
                        </div>

                        @if(!empty($item['highlighted_description']))
                            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 line-clamp-1">
                                {!! $item['highlighted_description'] !!}
                            </p>
                        @endif
                    </div>
                </a>
            @endforeach

            @if(strlen($query) >= 2)
                <a :href="searchShowUrl.replace('_PLACEHOLDER_', encodeURIComponent(query || ''))"
                   class="block p-3 md:p-4 text-center text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-semibold transition text-sm md:text-base">
                    Tüm sonuçları gör ({{ count($this->results) }}+) →
                </a>
            @endif
        </div>
    @endif
</div>
