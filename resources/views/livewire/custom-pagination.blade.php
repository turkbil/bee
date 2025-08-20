@if ($paginator->hasPages())
    <style>
        .pagination-debug { background: yellow !important; color: black !important; font-weight: bold !important; }
    </style>
    <!-- CUSTOM PAGINATION VIEW LOADED - TESTING -->
    <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
        <div>
            <p class="small text-muted pagination-debug">
                {{ __('admin.pagination_showing') }}
                <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                {{ __('admin.pagination_to') }}
                <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                {{ __('admin.pagination_of') }}
                <span class="fw-semibold">{{ $paginator->total() }}</span>
                {{ __('admin.pagination_results') }}
            </p>
        </div>

        <div>
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="{{ __('admin.pagination_previous') }}">
                        <span class="page-link" aria-hidden="true">‹</span>
                    </li>
                @else
                    <li class="page-item">
                        <button type="button" dusk="previousPage" class="page-link" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="$el.closest('body').scrollIntoView()" wire:loading.attr="disabled" aria-label="{{ __('admin.pagination_previous') }}">‹</button>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" wire:key="paginator-page-{{ $paginator->getPageName() }}-{{ $page }}" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item" wire:key="paginator-page-{{ $paginator->getPageName() }}-{{ $page }}"><button type="button" class="page-link" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="$el.closest('body').scrollIntoView()">{{ $page }}</button></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <button type="button" dusk="nextPage" class="page-link" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="$el.closest('body').scrollIntoView()" wire:loading.attr="disabled" aria-label="{{ __('admin.pagination_next') }}">›</button>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="{{ __('admin.pagination_next') }}">
                        <span class="page-link" aria-hidden="true">›</span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
@endif