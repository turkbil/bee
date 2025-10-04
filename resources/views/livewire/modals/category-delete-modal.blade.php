<div>
    @if($showModal)
    <div class="modal modal-blur fade show" id="category-delete-modal" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        @if($contentCount > 0 || $childCategoryCount > 0)
                            Kategori ve İçerikleri Yönet
                        @else
                            Kategori Sil
                        @endif
                    </h3>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <div class="modal-body">
                    <!-- Kategori Başlığı -->
                    <div class="text-center mb-4">
                        <p class="mb-2 fs-3">
                            <strong>"{{ $title }}"</strong>
                        </p>
                        <p class="mb-0">
                            @if($contentCount > 0 && $childCategoryCount > 0)
                                Bu kategoride <strong>{{ $contentCount }}</strong> adet içerik ve <strong>{{ $childCategoryCount }}</strong> adet alt kategori var.
                            @elseif($contentCount > 0)
                                Bu kategoride <strong>{{ $contentCount }}</strong> adet içerik var.
                            @elseif($childCategoryCount > 0)
                                Bu kategorinin <strong>{{ $childCategoryCount }}</strong> adet alt kategorisi var.
                            @else
                                Kategoriyi silmek istediğinizden emin misiniz?
                            @endif
                        </p>
                    </div>

                    @if($contentCount > 0 || $childCategoryCount > 0)
                        <!-- İki Seçenek: Taşı veya Sil -->
                        <div class="row g-4">
                            <!-- Sol: Taşıma Seçeneği -->
                            <div class="col-6">
                                <div class="card h-100 border-warning">
                                    <div class="card-body d-flex flex-column p-4" x-data="{ selectedCat: @entangle('selectedCategory') }">
                                        <!-- İkon ve Başlık -->
                                        <div class="text-center mb-4">
                                            <div class="mb-3 d-flex align-items-center justify-content-center" style="height: 40px;">
                                                <i class="fas fa-exchange-alt" style="font-size: 32px;"></i>
                                            </div>
                                            <h3 class="mb-3">Taşı ve Sil</h3>
                                            <p class="mb-0" style="min-height: 48px;">
                                                @if($contentCount > 0 && $childCategoryCount > 0)
                                                    İçerikler ve alt kategoriler seçilen kategoriye taşınacak, ardından kategori silinecek.
                                                @elseif($contentCount > 0)
                                                    İçerikler seçilen kategoriye taşınacak, ardından kategori silinecek.
                                                @else
                                                    Alt kategoriler seçilen kategoriye taşınacak, ardından kategori silinecek.
                                                @endif
                                            </p>
                                        </div>

                                        <!-- Form Alanı -->
                                        <div class="mb-3 flex-grow-1">
                                            <label class="form-label required">Hedef Kategori</label>
                                            <select class="form-select" wire:model="selectedCategory" x-on:change="$wire.selectedCategory = $event.target.value">
                                                <option value="">Kategori seçiniz...</option>
                                                @foreach($categories as $category)
                                                    @php
                                                        $depth = $category->depth_level ?? 0;
                                                        $prefix = $depth > 0 ? str_repeat('—', min($depth, 3)) . ' ' : '';
                                                    @endphp
                                                    <option value="{{ $category->id }}">
                                                        {{ $prefix }}{{ $category->display_title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Aksiyon Butonu -->
                                        <div class="mt-auto">
                                            <button type="button" class="btn btn-warning w-100" style="height: 42px;" wire:click="move" wire:loading.attr="disabled" :disabled="!selectedCat || selectedCat === ''">
                                                <span wire:loading.remove wire:target="move">
                                                    <i class="fas fa-exchange-alt me-1"></i>
                                                    Taşı ve Sil
                                                </span>
                                                <span wire:loading wire:target="move">
                                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                                    Taşınıyor...
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sağ: Silme Seçeneği -->
                            <div class="col-6">
                                <div class="card h-100 border-danger">
                                    <div class="card-body d-flex flex-column p-4">
                                        <!-- İkon ve Başlık -->
                                        <div class="text-center mb-4">
                                            <div class="mb-3 d-flex align-items-center justify-content-center" style="height: 40px;">
                                                <i class="fas fa-trash-alt" style="font-size: 32px;"></i>
                                            </div>
                                            <h3 class="mb-3">Hepsini Sil</h3>
                                            <p class="mb-0" style="min-height: 48px;">
                                                @if($contentCount > 0 && $childCategoryCount > 0)
                                                    Kategori, tüm içerikler ({{ $contentCount }} adet) ve alt kategoriler ({{ $childCategoryCount }} adet) kalıcı olarak silinecek.
                                                @elseif($contentCount > 0)
                                                    Kategori ve tüm içerikler ({{ $contentCount }} adet) kalıcı olarak silinecek.
                                                @else
                                                    Kategori ve tüm alt kategoriler ({{ $childCategoryCount }} adet) kalıcı olarak silinecek.
                                                @endif
                                            </p>
                                        </div>

                                        <!-- Uyarı Mesajı -->
                                        <div class="mb-3 flex-grow-1 d-flex flex-column justify-content-center">
                                            <div class="text-center p-3 border border-danger rounded">
                                                <strong>Dikkat!</strong> Bu işlem geri alınamaz.
                                            </div>
                                        </div>

                                        <!-- Aksiyon Butonu -->
                                        <div class="mt-auto">
                                            <button type="button" class="btn btn-danger w-100" style="height: 42px;" wire:click="deleteCategory" wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="deleteCategory">
                                                    <i class="fas fa-trash-alt me-1"></i>
                                                    Hepsini Sil
                                                </span>
                                                <span wire:loading wire:target="deleteCategory">
                                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                                    Siliniyor...
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    @if(!($contentCount > 0 || $childCategoryCount > 0))
                        <button type="button" class="btn" wire:click="$set('showModal', false)">İptal</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteCategory" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="deleteCategory">
                                <i class="fas fa-trash-alt me-1"></i>
                                Kategoriyi Sil
                            </span>
                            <span wire:loading wire:target="deleteCategory">
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Siliniyor...
                            </span>
                        </button>
                    @else
                        <button type="button" class="btn w-100" wire:click="$set('showModal', false)">İptal</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>
