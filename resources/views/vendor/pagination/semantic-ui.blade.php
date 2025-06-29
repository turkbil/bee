@if ($paginator->hasPages())
    <div class="ui pagination menu" role="navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <a class="icon item disabled" aria-disabled="true" aria-label="{{ __('admin.pagination_previous') }}"> <i class="left chevron icon"></i> </a>
        @else
            <a class="icon item" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('admin.pagination_previous') }}"> <i class="left chevron icon"></i> </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <a class="icon item disabled" aria-disabled="true">{{ $element }}</a>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <a class="item active" href="{{ $url }}" aria-current="page">{{ $page }}</a>
                    @else
                        <a class="item" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a class="icon item" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('admin.pagination_next') }}"> <i class="right chevron icon"></i> </a>
        @else
            <a class="icon item disabled" aria-disabled="true" aria-label="{{ __('admin.pagination_next') }}"> <i class="right chevron icon"></i> </a>
        @endif
    </div>
@endif
