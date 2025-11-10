@php
    View::share('pretitle', $reviewId ? 'Yorum Düzenle' : 'Manuel Yorum Ekle');
@endphp

<div class="review-manage-wrapper">
    @include('reviewsystem::admin.helper')

    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="row g-3">
                    <!-- Model Tipi -->
                    <div class="col-md-6">
                        <label class="form-label required">Model Tipi</label>
                        <select wire:model="modelType" class="form-select @error('modelType') is-invalid @enderror">
                            <option value="">Seçiniz...</option>
                            @foreach($availableModels as $class => $label)
                                <option value="{{ $class }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @if(isset($validationErrors['modelType']))
                            <div class="invalid-feedback d-block">{{ $validationErrors['modelType'] }}</div>
                        @endif
                        <div class="form-hint">Yorum yapılacak model tipini seçin</div>
                    </div>

                    <!-- Model ID -->
                    <div class="col-md-6">
                        <label class="form-label required">Model ID</label>
                        <input type="number" wire:model="modelId" class="form-control @error('modelId') is-invalid @enderror"
                               placeholder="Örn: 123">
                        @if(isset($validationErrors['modelId']))
                            <div class="invalid-feedback d-block">{{ $validationErrors['modelId'] }}</div>
                        @endif
                        <div class="form-hint">Ürün/sayfa/içerik ID'sini girin</div>
                    </div>

                    <!-- Kullanıcı -->
                    <div class="col-md-6">
                        <label class="form-label">Kullanıcı</label>
                        <select wire:model="userId" class="form-select">
                            <option value="">Seçiniz (veya yazar adı girin)</option>
                            @foreach($users as $user)
                                <option value="{{ $user['id'] }}">{{ $user['label'] }}</option>
                            @endforeach
                        </select>
                        <div class="form-hint">Kayıtlı kullanıcı seçin</div>
                    </div>

                    <!-- Yazar Adı (Guest) -->
                    <div class="col-md-6">
                        <label class="form-label">Yazar Adı (Guest)</label>
                        <input type="text" wire:model="authorName" class="form-control"
                               placeholder="Örn: Ahmet Yılmaz"
                               @if($userId) disabled @endif>
                        @if(isset($validationErrors['author']))
                            <div class="invalid-feedback d-block">{{ $validationErrors['author'] }}</div>
                        @endif
                        <div class="form-hint">
                            @if($userId)
                                <span class="text-muted">
                                    <i class="fas fa-info-circle"></i> Kullanıcı seçildiğinde bu alan devre dışı
                                </span>
                            @else
                                Kullanıcı seçilmediyse zorunlu
                            @endif
                        </div>
                    </div>

                    <!-- Puan (Yıldız) -->
                    <div class="col-12">
                        <label class="form-label">Puan (İsteğe Bağlı)</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="btn-group" role="group">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                            wire:click="$set('ratingValue', {{ $i }})"
                                            class="btn btn-sm {{ $ratingValue == $i ? 'btn-warning' : 'btn-outline-warning' }}">
                                        <i class="fas fa-star"></i> {{ $i }}
                                    </button>
                                @endfor
                                <button type="button"
                                        wire:click="$set('ratingValue', null)"
                                        class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-times"></i> Temizle
                                </button>
                            </div>
                            @if($ratingValue)
                                <span class="text-muted">
                                    <i class="fas fa-star text-warning"></i> {{ $ratingValue }}/5
                                </span>
                            @endif
                        </div>
                        @if(isset($validationErrors['ratingValue']))
                            <div class="invalid-feedback d-block">{{ $validationErrors['ratingValue'] }}</div>
                        @endif
                    </div>

                    <!-- Yorum Metni -->
                    <div class="col-12">
                        <label class="form-label required">Yorum</label>
                        <textarea wire:model="reviewBody"
                                  rows="5"
                                  class="form-control @error('reviewBody') is-invalid @enderror"
                                  placeholder="Yorum metnini buraya yazın..."></textarea>
                        @if(isset($validationErrors['reviewBody']))
                            <div class="invalid-feedback d-block">{{ $validationErrors['reviewBody'] }}</div>
                        @endif
                        <div class="form-hint">Minimum 10 karakter önerilir</div>
                    </div>

                    <!-- Onay Durumu -->
                    <div class="col-12">
                        <label class="form-check form-switch">
                            <input type="checkbox" wire:model="isApproved" class="form-check-input">
                            <span class="form-check-label">Onaylı Olarak Ekle</span>
                        </label>
                        <div class="form-hint">
                            @if($isApproved)
                                <span class="text-success"><i class="fas fa-check-circle"></i> Yorum hemen yayınlanacak</span>
                            @else
                                <span class="text-warning"><i class="fas fa-clock"></i> Yorum onay bekleyecek</span>
                            @endif
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fas fa-save"></i>
                                    {{ $reviewId ? 'Güncelle' : 'Kaydet' }}
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin"></i>
                                    Kaydediliyor...
                                </span>
                            </button>

                            <a href="{{ route('admin.reviewsystem.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Listeye Dön
                            </a>

                            @if($reviewId)
                                <button type="button"
                                        wire:click="$set('reviewId', null); $refresh"
                                        class="btn btn-info">
                                    <i class="fas fa-plus"></i> Yeni Yorum
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-info-circle"></i> Bilgi</h3>
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li><strong>Model Tipi:</strong> Yorumun ekleneceği model sınıfı (ürün, sayfa, vs.)</li>
                <li><strong>Model ID:</strong> O model'in veritabanı ID'si</li>
                <li><strong>Kullanıcı:</strong> Kayıtlı kullanıcı adına yorum eklenir</li>
                <li><strong>Yazar Adı:</strong> Guest yorum için (kullanıcı seçilmediyse zorunlu)</li>
                <li><strong>Puan:</strong> 1-5 arası yıldız puanı (opsiyonel)</li>
                <li><strong>Onaylı:</strong> Açıksa yorum hemen yayınlanır, kapalıysa onay bekler</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('show-toast', (event) => {
        const type = event.type || 'info';
        const message = event.message || 'İşlem tamamlandı';

        // Tabler.io toast notification
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            document.body.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl);
            toast.show();

            toastEl.addEventListener('hidden.bs.toast', () => {
                toastEl.remove();
            });
        } else {
            // Fallback: alert
            alert(message);
        }
    });
});
</script>
@endpush
