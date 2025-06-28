<div>
@include('languagemanagement::admin.helper')

<div class="card">
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Arama Kutusu -->
            <div class="col">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live="search" class="form-control"
                        placeholder="Sistem dili ara...">
                </div>
            </div>
            <!-- Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, delete, toggleActive, updateOrder"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px; z-index: 10;">
                    <div class="small text-muted mb-2">Güncelleniyor...</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Dil Kartları -->
        @if($languages->count() > 0)
            <div class="row" id="sortable-list">
                @foreach($languages as $language)
                    <div class="col-md-6 col-lg-4 mb-3" data-id="{{ $language->id }}">
                        <div class="card h-100 {{ !$language->is_active ? 'opacity-50' : '' }}" style="cursor: move;">
                            <div class="card-body d-flex flex-column">
                                <!-- Başlık -->
                                <div class="d-flex align-items-center mb-2">
                                    <span class="me-2" style="font-size: 1.5rem;">{{ $language->flag_icon ?? '🌐' }}</span>
                                    <div>
                                        <h5 class="card-title mb-0">{{ $language->native_name }}</h5>
                                        <small class="text-muted">{{ $language->name }}</small>
                                    </div>
                                </div>

                                <!-- Bilgiler -->
                                <div class="mb-3 flex-grow-1">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <small class="text-muted">Kod:</small>
                                            <div><code>{{ $language->code }}</code></div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Yön:</small>
                                            <div>{{ $language->direction === 'rtl' ? 'Sağdan Sola' : 'Soldan Sağa' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Durum Badge'leri -->
                                <div class="mb-3">
                                    @if($language->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                    
                                    @if(in_array(strtolower($language->code), ['tr', 'en']))
                                        <span class="badge bg-warning-lt ms-1">Korumalı</span>
                                    @endif
                                </div>

                                <!-- Butonlar -->
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.languagemanagement.system.manage', $language->id) }}" 
                                           class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="fas fa-edit me-1"></i> Düzenle
                                        </a>
                                        
                                        @if(!in_array(strtolower($language->code), ['tr', 'en']))
                                            <button wire:click="toggleActive({{ $language->id }})" 
                                                    wire:confirm="{{ __('admin.confirm_status_change') }}"
                                                    class="btn btn-outline-{{ $language->is_active ? 'warning' : 'success' }} btn-sm">
                                                <i class="fas fa-{{ $language->is_active ? 'pause' : 'play' }} me-1"></i>
                                                {{ $language->is_active ? __('admin.deactivate') : __('admin.activate') }}
                                            </button>
                                        @endif
                                    </div>
                                    
                                    @if(!in_array(strtolower($language->code), ['tr', 'en']))
                                        <button wire:click="delete({{ $language->id }})" 
                                                wire:confirm="{{ __('admin.confirm_delete_message') }}"
                                                class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash me-1"></i> {{ __('admin.delete') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-globe fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">{{ __('admin.no_language_found') }}</h5>
                <p class="text-muted">
                    @if($search)
                        "{{ $search }}" araması için sonuç bulunamadı.
                    @else
                        Henüz sistem dili eklenmemiş.
                    @endif
                </p>
                <a href="{{ route('admin.languagemanagement.system.manage') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> İlk Sistem Dilini Ekle
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortableList = document.getElementById('sortable-list');
    if (sortableList) {
        new Sortable(sortableList, {
            animation: 150,
            ghostClass: 'opacity-25',
            chosenClass: 'shadow',
            onEnd: function(evt) {
                const itemIds = Array.from(sortableList.children).map(item => 
                    item.getAttribute('data-id')
                );
                @this.call('updateOrder', itemIds);
            }
        });
    }
});
</script>
@endpush
</div>