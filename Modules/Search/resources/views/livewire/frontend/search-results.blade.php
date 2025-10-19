<div class="search-results-container container mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Arama Barı --}}
    <div class="sticky top-24 sm:top-28 lg:top-32 z-20">
        <div class="bg-white/90 dark:bg-slate-900/85 backdrop-blur border border-slate-200/70 dark:border-slate-700 rounded-2xl shadow-sm px-6 py-5">
            <input type="search"
                   wire:model.live.debounce.300ms="query"
                   placeholder="Ürün, kategori ara..."
                   class="w-full px-5 py-3.5 text-base sm:text-lg border border-slate-200 dark:border-slate-700 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-100 dark:focus:border-blue-400 dark:focus:ring-blue-900/40 focus:outline-none transition bg-white/90 dark:bg-slate-900 text-slate-900 dark:text-slate-100">

            @if($totalCount > 0)
                <div class="mt-2 flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                    <span><strong>{{ number_format($totalCount) }}</strong> sonuç bulundu</span>
                    <span class="text-xs text-slate-400 dark:text-slate-500">({{ $responseTime }}ms)</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Sonuçlar --}}
    @if($totalCount > 0)
        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($results as $index => $item)
                @php
                    $typeBadge = $item['type_label'] ?? null;
                    $productBadge = $item['product_badge'] ?? null;
                @endphp
                <div class="group bg-white dark:bg-slate-900 border border-slate-200/70 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-lg transition duration-200"
                     wire:click="trackClick({{ $item['id'] }}, '{{ $item['type'] }}', {{ $index }})">
                    <a href="{{ $item['url'] }}" class="flex flex-row gap-5 p-5 sm:p-6">
                        @if(!empty($item['image']))
                            <div class="w-24 sm:w-32 md:w-36 flex-shrink-0">
                                <img src="{{ $item['image'] }}"
                                     alt="{{ strip_tags($item['title'] ?? '') }}"
                                     class="w-full aspect-square object-cover rounded-xl bg-slate-100 dark:bg-slate-800">
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-lg text-slate-900 dark:text-slate-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                {!! $item['highlighted_title'] !!}
                            </h3>

                            @if(!empty($item['highlighted_description']))
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 leading-relaxed line-clamp-3">
                                    {!! $item['highlighted_description'] !!}
                                </p>
                            @endif

                            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs font-medium">
                                @if($typeBadge)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                        {{ $typeBadge }}
                                    </span>
                                @endif

                                @if($productBadge)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                        {{ $productBadge }}
                                    </span>
                                @endif
                            </div>

                            @if(!empty($item['price']))
                                <div class="mt-4 text-base font-semibold text-green-600 dark:text-green-400">
                                    {{ $item['price'] }}
                                </div>
                            @endif
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        @if($totalCount > $perPage)
            <nav class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4" aria-label="Arama sayfalama">
                <div class="flex items-center gap-2">
                    <button type="button"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:click="goToPreviousPage"
                            @disabled($currentPage <= 1)
                    >
                        <i class="fa-solid fa-arrow-left"></i> Önceki
                    </button>
                    <button type="button"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:click="goToNextPage"
                            @disabled($currentPage >= $lastPage)
                    >
                        Sonraki <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                    <span>Sayfa {{ $currentPage }} / {{ $lastPage }}</span>
                    <div class="hidden sm:flex items-center gap-1">
                        @php
                            $window = 3;
                            $start = max(1, $currentPage - $window);
                            $end = min($lastPage, $currentPage + $window);
                        @endphp
                        @for($i = $start; $i <= $end; $i++)
                            <button type="button"
                                    class="min-w-[2.5rem] h-10 rounded-lg border text-sm font-semibold transition
                                           {{ $currentPage === $i ? 'bg-blue-600 text-white border-blue-600' : 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}"
                                    wire:click="goToPage({{ $i }})">
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>
            </nav>
        @endif

    @elseif(strlen($query) >= 2)
        <div class="text-center py-12">
            <i class="fas fa-search text-6xl text-slate-300 dark:text-slate-700 mb-4"></i>
            <p class="text-xl text-slate-600 dark:text-slate-300">Sonuç bulunamadı</p>
        </div>
    @endif
</div>
