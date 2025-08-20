@if ($paginator->hasPages())
    <nav class="mb-0">
        <ul class="pagination mb-0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled" aria-disabled="true"><span>{{ __('admin.pagination_previous') }}</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">{{ __('admin.pagination_previous') }}</a></li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('admin.pagination_next') }}</a></li>
            @else
                <li class="disabled" aria-disabled="true"><span>{{ __('admin.pagination_next') }}</span></li>
            @endif
        </ul>
    </nav>
@endif
