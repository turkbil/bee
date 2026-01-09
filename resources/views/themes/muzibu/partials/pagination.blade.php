@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="py-8">
        @php
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();
        @endphp

        <div class="flex items-center justify-center gap-2">
            {{-- Previous Page Icon --}}
            @if ($paginator->onFirstPage())
                <span class="w-10 h-10 flex items-center justify-center text-gray-600 bg-white/5 rounded-lg cursor-not-allowed opacity-50">
                    <i class="fas fa-chevron-left text-sm"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="w-10 h-10 flex items-center justify-center text-white bg-white/10 rounded-lg hover:bg-muzibu-coral transition-all duration-200 hover:scale-105 shadow-lg hover:shadow-muzibu-coral/50 group">
                    <i class="fas fa-chevron-left text-sm group-hover:-translate-x-0.5 transition-transform"></i>
                </a>
            @endif

            {{-- Previous Page Number --}}
            @if ($currentPage > 1)
                <a href="{{ $paginator->url($currentPage - 1) }}"
                   class="w-10 h-10 flex items-center justify-center text-gray-300 bg-white/10 rounded-lg hover:bg-white/20 hover:text-white hover:shadow-lg transition-all duration-200 font-medium text-sm hover:scale-105 backdrop-blur-sm">
                    {{ $currentPage - 1 }}
                </a>
            @endif

            {{-- Current Page Number --}}
            <span class="relative w-10 h-10 flex items-center justify-center text-white bg-gradient-to-br from-muzibu-coral to-pink-500 rounded-lg font-bold text-sm shadow-lg shadow-muzibu-coral/50 ring-2 ring-muzibu-coral/30 ring-offset-2 ring-offset-slate-900">
                {{ $currentPage }}
                <span class="absolute -top-1 -right-1 w-2 h-2 bg-pink-400 rounded-full animate-ping"></span>
            </span>

            {{-- Next Page Number --}}
            @if ($currentPage < $lastPage)
                <a href="{{ $paginator->url($currentPage + 1) }}"
                   class="w-10 h-10 flex items-center justify-center text-gray-300 bg-white/10 rounded-lg hover:bg-white/20 hover:text-white hover:shadow-lg transition-all duration-200 font-medium text-sm hover:scale-105 backdrop-blur-sm">
                    {{ $currentPage + 1 }}
                </a>
            @endif

            {{-- Next Page Icon --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="w-10 h-10 flex items-center justify-center text-white bg-white/10 rounded-lg hover:bg-muzibu-coral transition-all duration-200 hover:scale-105 shadow-lg hover:shadow-muzibu-coral/50 group">
                    <i class="fas fa-chevron-right text-sm group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            @else
                <span class="w-10 h-10 flex items-center justify-center text-gray-600 bg-white/5 rounded-lg cursor-not-allowed opacity-50">
                    <i class="fas fa-chevron-right text-sm"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
