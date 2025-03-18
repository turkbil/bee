<div>
    @if($showModal)
    <!-- Modal -->
    <div class="modal fade show" id="category-delete-modal" tabindex="-1" role="dialog" aria-modal="true"
        style="display: block; padding-right: 16px;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Kategori Sil</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                        <h3 class="h4">Dikkat!</h3>
                        <p class="text-muted">"{{ $title }}" isimli kategoriyi silmek üzeresiniz.</p>
                    </div>

                    @if($contentCount > 0)
                    <div class="mb-4">
                        <p class="text-muted mb-3 text-center">Bu kategoride <strong>{{ $contentCount }}</strong> içerik
                            bulunuyor. İçeriklere ne yapılacağını seçmelisiniz.</p>

                        <!-- Silme ve Taşıma Seçenekleri -->
                        <div class="row g-3">
                            <!-- Silme Seçeneği -->
                            <div class="col-12 col-md-6">
                                <div class="card card-sm h-100">
                                    <div class="card-body text-center d-flex flex-column">
                                        <i class="fas fa-trash-alt fa-2x text-danger mb-3"></i>
                                        <h4 class="h5 mb-2">İçerikleri Sil</h4>
                                        <p class="text-muted mb-3">Bu seçenek ile kategorideki tüm içerikler kalıcı
                                            olarak silinecek ve kategori kaldırılacaktır.</p>
                                        <div class="mt-auto">
                                            <button class="btn btn-outline-danger w-100" wire:click="delete">
                                                <i class="fas fa-trash-alt me-2"></i> Sil
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Taşıma Seçeneği -->
                            <div class="col-12 col-md-6">
                                <div class="card card-sm h-100">
                                    <div class="card-body text-center d-flex flex-column">
                                        <i class="fas fa-share fa-2x text-info mb-3"></i>
                                        <h4 class="h5 mb-2">İçerikleri Taşı</h4>
                                        <p class="text-muted mb-3">Bu seçenek ile kategorideki tüm içerikler başka bir
                                            kategoriye taşınacak ve bu kategori kaldırılacaktır.</p>
                                        <div class="mb-3">
                                            <select class="form-select tomselect" wire:model="selectedCategory">
                                                <option value="">Bir kategori seçin</option>
                                                @foreach($categories as $category)
                                                <option value="{{ $category->{$module.'_category_id'} }}">{{
                                                    $category->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mt-auto">
                                            <button class="btn btn-outline-info w-100" wire:click="move"
                                                wire:loading.attr="disabled">
                                                <span wire:loading.remove>
                                                    <i class="fas fa-share me-2"></i> Taşı ve Sil
                                                </span>
                                                <span wire:loading>
                                                    <i class="fas fa-spinner fa-spin me-2"></i> İşlem Yapılıyor...
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <p class="text-muted text-center">Bu kategoride herhangi bir içerik bulunmuyor.</p>
                    @endif
                </div>

            </div>
        </div>
        <!-- Modal Backdrop -->
        <div class="modal-backdrop fade show" style="z-index: 1040; pointer-events: none;"></div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            // ESC tuşu ile modalı kapat
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && $('#category-delete-modal').is(':visible')) {
                    Livewire.dispatch('closeModal');
                }
            });

            // TomSelect'ı başlat
            new TomSelect('.tomselect', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        });
    </script>
</div>