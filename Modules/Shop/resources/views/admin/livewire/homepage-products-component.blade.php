@php
    View::share('pretitle', 'Anasayfa Ürünleri');
@endphp

<div class="homepage-products-component-wrapper">
    <div class="card">
        @include('shop::admin.helper')

        <div class="card-body p-0">
            <!-- Header Bölümü -->
            <div class="row mx-2 my-3">
                <div class="col-md-8">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Bilgi:</strong> Sadece "Anasayfada Gösterilsin" işaretli ürünler bu listede görünür.
                        <strong>Sürükle-bırak</strong> yaparak sıralayın. Yukarıda olanlar önce gösterilir.
                    </div>
                </div>

                <!-- Ortadaki Loading Indicator -->
                <div class="col-md-2 position-relative">
                    <div wire:loading
                        wire:target="render, updateSortOrder"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">Güncelleniyor...</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>

                <!-- Ürün Sayısı -->
                <div class="col-md-2">
                    <div class="d-flex align-items-center h-100 justify-content-end">
                        <span class="text-muted">
                            <i class="fas fa-home me-2"></i>
                            <strong>{{ $products->count() }}</strong> Ürün
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tablo Bölümü -->
            <div id="table-default" class="table-responsive">
                @if($products->count() === 0)
                    <div class="empty p-5">
                        <div class="empty-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <p class="empty-title">Anasayfa ürünü yok</p>
                        <p class="empty-subtitle text-muted">
                            Ürün listesinden "Anasayfada Gösterilsin" seçeneğini aktif edin.
                        </p>
                        <div class="empty-action">
                            <a href="{{ route('admin.shop.products.index') }}" class="btn btn-primary">
                                <i class="fas fa-box me-2"></i>
                                Ürünlere Git
                            </a>
                        </div>
                    </div>
                @else
                    <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                        <thead>
                            <tr>
                                <th style="width: 50px">
                                    <i class="fas fa-grip-vertical text-muted" data-bs-toggle="tooltip" title="Sürükle-bırak ile sırala"></i>
                                </th>
                                <th style="width: 50px">ID</th>
                                <th>Ürün Adı</th>
                                <th>Kategori</th>
                                <th>Marka</th>
                                <th class="text-end">Fiyat</th>
                                <th class="text-center" style="width: 100px">Sıra</th>
                            </tr>
                        </thead>
                        <tbody class="table-tbody sortable-list" id="sortable-homepage-products">
                            @foreach($products as $product)
                                <tr class="hover-trigger sortable-item"
                                    wire:key="homepage-product-{{ $product->product_id }}"
                                    data-product-id="{{ $product->product_id }}">
                                    <td class="text-center">
                                        <i class="fas fa-grip-vertical text-muted sortable-handle" style="cursor: grab;"></i>
                                    </td>
                                    <td class="sort-id small">
                                        {{ $product->product_id }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="editable-title pr-4">
                                                    {{ $product->getTranslated('title', $currentSiteLocale) ?? $product->getTranslated('title', 'tr') }}
                                                </span>
                                            </div>
                                            @if($product->sku)
                                                <span class="badge bg-secondary-lt ms-2">{{ $product->sku }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($product->category)
                                            {{ $product->category->getTranslated('title', $currentSiteLocale) ?? '—' }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->brand)
                                            {{ $product->brand->getTranslated('title', $currentSiteLocale) ?? '—' }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($product->price_on_request)
                                            <span class="badge bg-secondary">Fiyat Talep Edilir</span>
                                        @elseif ($product->base_price)
                                            <div class="d-flex flex-column align-items-end">
                                                <span class="fw-semibold">{{ number_format((float) $product->base_price, 2) }} {{ $product->currency ?? 'TRY' }}</span>
                                                @if ($product->compare_at_price && $product->compare_at_price > $product->base_price)
                                                    <small class="text-muted text-decoration-line-through">
                                                        {{ number_format((float) $product->compare_at_price, 2) }} {{ $product->currency ?? 'TRY' }}
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($product->homepage_sort_order)
                                            <span class="badge bg-primary">{{ $product->homepage_sort_order }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <p class="small text-muted mb-0">
                    Toplam <span class="fw-semibold">{{ $products->count() }}</span> anasayfa ürünü
                </p>
                <p class="small text-muted mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Değişiklikler otomatik kaydedilir
                </p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Sortable Styles */
.sortable-handle {
    cursor: grab !important;
}

.sortable-handle:active {
    cursor: grabbing !important;
}

.sortable-ghost {
    opacity: 0.4;
    background-color: var(--tblr-primary-lt, #e6f3ff) !important;
}

.sortable-chosen {
    background-color: var(--tblr-primary-lt, #e6f3ff) !important;
}

.sortable-drag {
    opacity: 0.8;
    cursor: grabbing !important;
}

:root[data-bs-theme="dark"] .sortable-ghost,
:root[data-bs-theme="dark"] .sortable-chosen {
    background-color: rgba(var(--tblr-primary-rgb), 0.2) !important;
}

/* Hover Effects */
.hover-trigger:hover .hover-hide {
    display: none;
}

.hover-trigger .hover-show {
    display: none;
}

.hover-trigger:hover .hover-show {
    display: inline-block;
}

/* Badge Improvements */
.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

:root[data-bs-theme="dark"] .badge.bg-secondary-lt {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: rgba(255, 255, 255, 0.8) !important;
}

:root[data-bs-theme="light"] .badge.bg-secondary-lt {
    background-color: #e9ecef !important;
    color: #495057 !important;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
<script>
let homepageSortableInstance = null;

function initHomepageSortable() {
    const sortableList = document.getElementById('sortable-homepage-products');

    if (!sortableList) {
        if (homepageSortableInstance) {
            homepageSortableInstance.destroy();
            homepageSortableInstance = null;
        }
        return;
    }

    // Eski instance'ı temizle
    if (homepageSortableInstance) {
        homepageSortableInstance.destroy();
        homepageSortableInstance = null;
    }

    homepageSortableInstance = new Sortable(sortableList, {
        animation: 150,
        handle: '.sortable-handle',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        onEnd: function(evt) {
            const productIds = [];
            const rows = sortableList.querySelectorAll('.sortable-item');

            rows.forEach(function(row) {
                const productId = row.getAttribute('data-product-id');
                if (productId) {
                    productIds.push(parseInt(productId));
                }
            });

            console.log('Homepage Sortable onEnd - Product IDs:', productIds);

            // Livewire component'e sıralama bilgisini gönder
            @this.call('updateSortOrder', productIds);
        }
    });

    console.log('Homepage sortable initialized');
}

// İlk yükleme
document.addEventListener('DOMContentLoaded', function() {
    initHomepageSortable();
});

// Livewire güncellemelerinden sonra yeniden initialize
document.addEventListener('livewire:navigated', function() {
    initHomepageSortable();
});

document.addEventListener('livewire:update', function() {
    setTimeout(initHomepageSortable, 100);
});

// Wire events ile de dinle
Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
    succeed(({ snapshot, effect }) => {
        queueMicrotask(() => {
            initHomepageSortable();
        });
    });
});
</script>
@endpush
