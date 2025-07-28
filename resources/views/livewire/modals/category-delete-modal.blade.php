<div>
    @if($showModal)
    <div class="modal modal-blur fade show" id="category-delete-modal" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Kategori Sil</h4>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p><strong>"{{ $title }}"</strong> kategorisini silmek istediğinizden emin misiniz?</p>
                    
                    @if($contentCount > 0)
                        <div class="alert alert-warning">
                            <h4 class="alert-heading">Dikkat!</h4>
                            <p class="mb-0">Bu kategoride <strong>{{ $contentCount }}</strong> adet içerik bulunmaktadır.</p>
                        </div>
                        
                        <p><strong>Ne yapmak istiyorsunuz?</strong></p>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">İçerikleri hangi kategoriye taşıyalım?</label>
                            <select class="form-select" wire:model="selectedCategory">
                                <option value="">Seçiniz...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->portfolio_category_id ?? $category->id }}">
                                        {{ $category->display_title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">İptal</button>
                    
                    @if($contentCount > 0)
                        <button type="button" class="btn btn-warning" wire:click="move" wire:loading.attr="disabled" @if(empty($selectedCategory)) disabled @endif>
                            <span wire:loading.remove>İçerikleri Taşı ve Kategoriyi Sil</span>
                            <span wire:loading>İşleniyor...</span>
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="deleteCategory" wire:loading.attr="disabled">
                            <span wire:loading.remove>HEPSİNİ SİL</span>
                            <span wire:loading>İşleniyor...</span>
                        </button>
                    @else
                        <button type="button" class="btn btn-danger" wire:click="deleteCategory" wire:loading.attr="disabled">
                            <span wire:loading.remove>Kategoriyi Sil</span>
                            <span wire:loading>İşleniyor...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>