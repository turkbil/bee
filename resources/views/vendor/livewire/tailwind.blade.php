@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

@if ($paginator->hasPages())
    <div class="card-footer d-flex align-items-center">
        <!-- Sol Taraf: Kayıt Bilgisi -->
        <p class="small text-muted m-0">
            Gösterilen <span class="fw-semibold">{{ $paginator->firstItem() }}</span> -
            <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
            arası, toplam <span class="fw-semibold">{{ $paginator->total() }}</span> sonuç
        </p>

        <!-- Sağ Taraf: Pagination -->
        <ul class="pagination m-0 ms-auto">
            {{-- Önceki Sayfa --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link" aria-disabled="true">
                        <!-- SVG - Geri Butonu -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M15 6l-6 6l6 6"></path>
                        </svg>
                        Önceki
                    </span>
                </li>
            @else
                <li class="page-item">
                    <button wire:click="previousPage('{{ $paginator->getPageName() }}')" class="page-link" x-on:click="{{ $scrollIntoViewJsSnippet }}">
                        <!-- SVG - Geri Butonu -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M15 6l-6 6l6 6"></path>
                        </svg>
                        Önceki
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
                    <button wire:click="gotoPage(1)" class="page-link">1</button>
                </li>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            @endif

            {{-- Orta Sayfa Numaraları --}}
            @for ($page = $startPage; $page <= $endPage; $page++)
                @if ($page == $currentPage)
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="page-link">{{ $page }}</button>
                    </li>
                @endif
            @endfor

            {{-- ... + Son Sayfa --}}
            @if ($endPage < $lastPage)
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
                <li class="page-item">
                    <button wire:click="gotoPage({{ $lastPage }})" class="page-link">{{ $lastPage }}</button>
                </li>
            @endif

            {{-- Sonraki Sayfa --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <button wire:click="nextPage('{{ $paginator->getPageName() }}')" class="page-link" x-on:click="{{ $scrollIntoViewJsSnippet }}">
                        Sonraki
                        <!-- SVG - İleri Butonu -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M9 6l6 6l-6 6"></path>
                        </svg>
                    </button>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">
                        Sonraki
                        <!-- SVG - İleri Butonu -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M9 6l6 6l-6 6"></path>
                        </svg>
                    </span>
                </li>
            @endif
        </ul>
    </div>
@endif