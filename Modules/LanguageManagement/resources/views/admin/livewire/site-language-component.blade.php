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
                        placeholder="{{ __('admin.search_tenant_language') }}...">
                </div>
            </div>
            <!-- Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, delete, toggleActive, setAsDefault, updateOrder"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px; z-index: 10;">
                    <div class="small text-muted mb-2">{{ __('admin.updating') }}...</div>
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
                        <div class="card h-100 {{ !$language->is_active ? 'opacity-50' : '' }} {{ $language->is_default ? 'border-primary' : '' }}" style="cursor: move;">
                            @if($language->is_default)
                                <div class="card-header bg-primary text-white py-2">
                                    <small><i class="fas fa-star me-1"></i> {{ __('admin.default_tenant_language') }}</small>
                                </div>
                            @endif
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
                                            <small class="text-muted">{{ __('admin.code') }}:</small>
                                            <div><code>{{ $language->code }}</code></div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">{{ __('admin.direction') }}:</small>
                                            <div>{{ $language->direction === 'rtl' ? __('admin.rtl') : __('admin.ltr') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Durum Badge'leri -->
                                <div class="mb-3">
                                    @if($language->is_active)
                                        <span class="badge bg-success">{{ __('admin.active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('admin.inactive') }}</span>
                                    @endif
                                    
                                    @if($language->is_default)
                                        <span class="badge bg-primary ms-1">{{ __('admin.default') }}</span>
                                    @endif
                                </div>

                                <!-- Butonlar -->
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.languagemanagement.site.manage', $language->id) }}" 
                                           class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="fas fa-edit me-1"></i> {{ __('admin.edit') }}
                                        </a>
                                        
                                        <button wire:click="toggleActive({{ $language->id }})" 
                                                wire:confirm="{{ __('admin.confirm_status_change') }}?"
                                                class="btn btn-outline-{{ $language->is_active ? 'warning' : 'success' }} btn-sm">
                                            <i class="fas fa-{{ $language->is_active ? 'pause' : 'play' }} me-1"></i>
                                            {{ $language->is_active ? __('admin.deactivate') : __('admin.activate') }}
                                        </button>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        @if(!$language->is_default)
                                            <button wire:click="setAsDefault({{ $language->id }})" 
                                                    wire:confirm="{{ __('admin.confirm_set_default') }}?"
                                                    class="btn btn-outline-info btn-sm flex-fill">
                                                <i class="fas fa-star me-1"></i> {{ __('admin.set_default') }}
                                            </button>
                                        @endif
                                        
                                        <button wire:click="delete({{ $language->id }})" 
                                                wire:confirm="{{ __('admin.confirm_delete_tenant_language') }}?"
                                                class="btn btn-outline-danger btn-sm {{ $language->is_default ? 'flex-fill' : '' }}">
                                            <i class="fas fa-trash me-1"></i>
                                            {{ $language->is_default ? __('admin.delete') : '' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-globe fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">{{ __('admin.no_tenant_languages_found') }}</h5>
                <p class="text-muted">
                    @if($search)
                        {{ __('admin.no_search_results', ['search' => $search]) }}
                    @else
                        {{ __('admin.no_tenant_languages_yet') }}
                    @endif
                </p>
                <a href="{{ route('admin.languagemanagement.site.manage') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> {{ __('admin.add_first_tenant_language') }}
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