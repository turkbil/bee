@php
if (!isset($scrollTo)) {
$scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
? "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()"
: '';
@endphp

<div class="card-footer d-flex flex-column flex-md-row align-items-center">
    <!-- Sol Taraf: Kayıt Bilgisi -->
    <p class="small text-muted m-0 text-center text-md-start">
        {{ t('admin.pagination_showing') }} <span class="fw-semibold">{{ $paginator->firstItem() }}</span> {{ t('admin.pagination_to') }}
        <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
        {{ t('admin.pagination_between') }}, {{ t('admin.pagination_of') }} <span class="fw-semibold">{{ $paginator->total() }}</span> {{ t('admin.pagination_results') }}
    </p>

    @if ($paginator->hasPages())
    <!-- Sağ Taraf: Pagination -->
    <ul class="pagination m-0 mt-3 mt-md-0 ms-md-auto justify-content-center justify-content-md-start">
        {{-- Önceki Sayfa --}}
        @if ($paginator->onFirstPage())
        <li class="page-item disabled">
            <span class="page-link link-secondary" aria-disabled="true">
                <!-- SVG - Geri Butonu -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M15 6l-6 6l6 6"></path>
                </svg>
                {{ t('admin.pagination_previous') }}
            </span>
        </li>
        @else
        <li class="page-item">
            <button wire:click="previousPage('{{ $paginator->getPageName() }}')" class="page-link link-secondary"
                x-on:click="{{ $scrollIntoViewJsSnippet }}">
                <!-- SVG - Geri Butonu -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M15 6l-6 6l6 6"></path>
                </svg>
                {{ t('admin.pagination_previous') }}
            </button>
        </li>
        @endif

        {{-- Sayfa Numaraları --}}
        @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $startPage = max($currentPage - 2, 1);
        $endPage = min($currentPage + 2, $lastPage);
        @endphp

        {{-- İlk Sayfa + ... --}}
        @if ($startPage > 1)
        <li class="page-item">
            <button wire:click="gotoPage(1)" class="page-link link-secondary">1</button>
        </li>
        <li class="page-item disabled">
            <span class="page-link link-secondary">...</span>
        </li>
        @endif

        {{-- Orta Sayfa Numaraları --}}
        @for ($page = $startPage; $page <= $endPage; $page++) @if ($page==$currentPage) <li class="page-item active">
            <span class="page-link">{{ $page }}</span>
            </li>
            @else
            <li class="page-item">
                <button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                    class="page-link link-secondary">
                    {{ $page }}
                </button>
            </li>
            @endif
            @endfor

            {{-- ... + Son Sayfa --}}
            @if ($endPage < $lastPage) <li class="page-item disabled">
                <span class="page-link link-secondary">...</span>
                </li>
                <li class="page-item">
                    <button wire:click="gotoPage({{ $lastPage }})" class="page-link link-secondary">{{ $lastPage
                        }}</button>
                </li>
                @endif

                {{-- Sonraki Sayfa --}}
                @if ($paginator->hasMorePages())
                <li class="page-item">
                    <button wire:click="nextPage('{{ $paginator->getPageName() }}')" class="page-link link-secondary"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}">
                        {{ t('admin.pagination_next') }}
                        <!-- SVG - İleri Butonu -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M9 6l6 6l-6 6"></path>
                        </svg>
                    </button>
                </li>
                @else
                <li class="page-item disabled">
                    <span class="page-link link-secondary">
                        {{ t('admin.pagination_next') }}
                        <!-- SVG - İleri Butonu -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M9 6l6 6l-6 6"></path>
                        </svg>
                    </span>
                </li>
                @endif
    </ul>
    @endif
</div>