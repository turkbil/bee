@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center py-6">
        <div class="flex items-center gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="w-9 h-9 flex items-center justify-center text-gray-500 bg-white/5 rounded-lg cursor-not-allowed">
                    <i class="fas fa-chevron-left text-xs"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="w-9 h-9 flex items-center justify-center text-white bg-white/10 rounded-lg hover:bg-muzibu-coral transition">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="w-9 h-9 flex items-center justify-center text-gray-500">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-9 h-9 flex items-center justify-center text-white bg-muzibu-coral rounded-lg font-medium text-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="w-9 h-9 flex items-center justify-center text-gray-300 bg-white/10 rounded-lg hover:bg-white/20 hover:text-white transition text-sm">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="w-9 h-9 flex items-center justify-center text-white bg-white/10 rounded-lg hover:bg-muzibu-coral transition">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            @else
                <span class="w-9 h-9 flex items-center justify-center text-gray-500 bg-white/5 rounded-lg cursor-not-allowed">
                    <i class="fas fa-chevron-right text-xs"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
