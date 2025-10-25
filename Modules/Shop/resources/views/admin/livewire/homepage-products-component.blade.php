@php
    View::share('pretitle', 'Anasayfa Ürünleri');
@endphp

<div class="homepage-products-component">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-home me-2"></i>
                Anasayfa Ürünleri Sıralama
            </h3>
            <div class="card-actions">
                <button wire:click="saveSortOrders" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    Sıralamayı Kaydet
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Bilgi:</strong> Sadece "Anasayfada Gösterilsin" işaretli ürünler bu listede görünür.
                Küçük numara = üstte gösterilir. Boş bırakılan ürünler en sona gider.
            </div>

            @if(count($products) === 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Henüz anasayfada gösterilecek ürün seçilmemiş. Ürün yönetiminden "Anasayfada Gösterilsin" seçeneğini aktif edin.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Sıra</th>
                                <th style="width: 100px;">SKU</th>
                                <th>Ürün Adı</th>
                                <th style="width: 200px;">Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <input
                                            type="number"
                                            wire:model="sortOrders.{{ $product['product_id'] }}"
                                            class="form-control form-control-sm"
                                            style="width: 70px;"
                                            placeholder="Sıra"
                                            min="0"
                                        >
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $product['sku'] ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <strong>{{ $product['title'] }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $product['category_name'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button wire:click="saveSortOrders" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Sıralamayı Kaydet
                    </button>
                </div>
            @endif
        </div>

        <!-- Loading Indicator -->
        <div wire:loading wire:target="saveSortOrders, loadProducts"
            class="position-fixed top-50 start-50 translate-middle"
            style="z-index: 9999;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Yükleniyor...</span>
            </div>
        </div>
    </div>
</div>
