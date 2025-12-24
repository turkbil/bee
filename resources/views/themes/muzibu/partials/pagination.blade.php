@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        {{-- Mobile view --}}
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white/5 border border-white/10 cursor-default rounded-md">
                    Önceki
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-white/10 border border-white/10 rounded-md hover:bg-white/20 transition">
                    Önceki
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-white bg-white/10 border border-white/10 rounded-md hover:bg-white/20 transition">
                    Sonraki
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white/5 border border-white/10 cursor-default rounded-md">
                    Sonraki
                </span>
            @endif
        </div>

        {{-- Desktop view --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-400">
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    -
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    /
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    kayıt
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rounded-md shadow-sm gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Önceki">
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white/5 border border-white/10 cursor-default rounded-l-md" aria-hidden="true">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-white/10 border border-white/10 rounded-l-md hover:bg-green-500 transition" aria-label="Önceki">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white/5 border border-white/10 cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-500 border border-green-500 cursor-default">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-300 bg-white/10 border border-white/10 hover:bg-white/20 hover:text-white transition" aria-label="Sayfa {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-white/10 border border-white/10 rounded-r-md hover:bg-green-500 transition" aria-label="Sonraki">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Sonraki">
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white/5 border border-white/10 cursor-default rounded-r-md" aria-hidden="true">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
