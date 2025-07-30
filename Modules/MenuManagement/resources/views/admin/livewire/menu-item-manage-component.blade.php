<div wire:key="menu-item-manage-component" wire:id="menu-item-manage-component">
    {{-- Helper dosyası --}}
    @include('menumanagement::admin.helper')
    @include('admin.partials.error_message')

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                
                <!-- Sol Panel - Menü Öğesi Ekleme -->
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('menumanagement::admin.add_menu_item') }}</h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="addMenuItem">
                                
                                <!-- Dil Sekmeleri -->
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    @foreach($this->availableSiteLanguages as $language)
                                        @php
                                            $languageName = \Modules\LanguageManagement\App\Models\TenantLanguage::where('code', $language)->value('name');
                                        @endphp
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link @if($loop->first) active @endif" 
                                                id="language-{{ $language }}-tab" 
                                                data-bs-toggle="tab" 
                                                data-bs-target="#language-{{ $language }}" 
                                                type="button" 
                                                role="tab">
                                                {{ $languageName }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content">
                                    @foreach($this->availableSiteLanguages as $language)
                                        <div class="tab-pane fade @if($loop->first) show active @endif" 
                                            id="language-{{ $language }}" 
                                            role="tabpanel">
                                            
                                            <!-- Başlık -->
                                            <div class="form-floating mb-3">
                                                <input type="text" 
                                                    class="form-control @error('multiLangInputs.' . $language . '.title') is-invalid @enderror" 
                                                    wire:model="multiLangInputs.{{ $language }}.title"
                                                    placeholder="{{ __('menumanagement::admin.title') }}">
                                                <label>{{ __('menumanagement::admin.title') }} ({{ strtoupper($language) }})</label>
                                                @error('multiLangInputs.' . $language . '.title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- URL/Link -->
                                            <div class="form-floating mb-3">
                                                <input type="text" 
                                                    class="form-control @error('multiLangInputs.' . $language . '.url_value') is-invalid @enderror" 
                                                    wire:model="multiLangInputs.{{ $language }}.url_value"
                                                    placeholder="{{ __('menumanagement::admin.url_value') }}">
                                                <label>{{ __('menumanagement::admin.url_value') }} ({{ strtoupper($language) }})</label>
                                                @error('multiLangInputs.' . $language . '.url_value')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                    @endforeach
                                </div>

                                <!-- URL Tipi -->
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('url_type') is-invalid @enderror" wire:model="url_type">
                                        <option value="">{{ __('menumanagement::admin.select_url_type') }}</option>
                                        <option value="custom">{{ __('menumanagement::admin.custom_url') }}</option>
                                        <option value="page">{{ __('menumanagement::admin.page_url') }}</option>
                                        <option value="module">{{ __('menumanagement::admin.module_url') }}</option>
                                        <option value="external">{{ __('menumanagement::admin.external_url') }}</option>
                                    </select>
                                    <label>{{ __('menumanagement::admin.url_type') }}</label>
                                    @error('url_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Hedef -->
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('target') is-invalid @enderror" wire:model="target">
                                        <option value="_self">{{ __('menumanagement::admin.same_window') }}</option>
                                        <option value="_blank">{{ __('menumanagement::admin.new_window') }}</option>
                                        <option value="_parent">{{ __('menumanagement::admin.parent_window') }}</option>
                                        <option value="_top">{{ __('menumanagement::admin.top_window') }}</option>
                                    </select>
                                    <label>{{ __('menumanagement::admin.target') }}</label>
                                    @error('target')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Sıralama -->
                                <div class="form-floating mb-3">
                                    <input type="number" 
                                        class="form-control @error('sort_order') is-invalid @enderror" 
                                        wire:model="sort_order"
                                        min="0"
                                        placeholder="{{ __('menumanagement::admin.sort_order') }}">
                                    <label>{{ __('menumanagement::admin.sort_order') }}</label>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Üst Menü -->
                                <div class="form-floating mb-3">
                                    <select class="form-select" wire:model="parent_id">
                                        <option value="">{{ __('menumanagement::admin.no_parent') }}</option>
                                        @foreach($headerMenuItems as $item)
                                            <option value="{{ $item->menu_item_id }}">
                                                {{ $item->getTranslated('title', app()->getLocale()) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>{{ __('menumanagement::admin.parent_item') }}</label>
                                </div>

                                <!-- Gelişmiş Seçenekler (Accordion) -->
                                <div class="accordion mb-3">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#advanced-options">
                                                {{ __('menumanagement::admin.advanced_options') }}
                                            </button>
                                        </h2>
                                        <div id="advanced-options" class="accordion-collapse collapse">
                                            <div class="accordion-body">
                                                
                                                <!-- CSS Sınıfı -->
                                                <div class="form-floating mb-3">
                                                    <input type="text" 
                                                        class="form-control" 
                                                        wire:model="css_class"
                                                        placeholder="{{ __('menumanagement::admin.css_class') }}">
                                                    <label>{{ __('menumanagement::admin.css_class') }}</label>
                                                </div>

                                                <!-- İkon -->
                                                <div class="form-floating mb-3">
                                                    <input type="text" 
                                                        class="form-control" 
                                                        wire:model="icon"
                                                        placeholder="{{ __('menumanagement::admin.icon') }}">
                                                    <label>{{ __('menumanagement::admin.icon') }}</label>
                                                </div>

                                                <!-- Aktif Durumu -->
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" 
                                                        type="checkbox" 
                                                        wire:model="is_active"
                                                        id="is_active">
                                                    <label class="form-check-label" for="is_active">
                                                        {{ __('menumanagement::admin.is_active') }}
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kaydet Butonu -->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        {{ __('menumanagement::admin.add_menu_item') }}
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sağ Panel - Menü Öğeleri Listesi -->
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                {{ __('menumanagement::admin.header_menu_items') }}
                                @if($headerMenu)
                                    <span class="badge badge-outline ms-2">{{ $headerMenu->getTranslated('name', app()->getLocale()) }}</span>
                                @endif
                            </h3>
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary btn-sm" wire:click="refreshMenuItems">
                                    <i class="fas fa-refresh me-1"></i>{{ __('admin.refresh') }}
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            
                            @if($headerMenuItems && count($headerMenuItems) > 0)
                                <div class="list-group list-group-flush" id="sortable-menu-items">
                                    @foreach($headerMenuItems as $item)
                                        <div class="list-group-item d-flex justify-content-between align-items-center sortable-item" 
                                            data-id="{{ $item->menu_item_id }}"
                                            style="padding-left: {{ ($item->depth_level * 20) + 15 }}px;">
                                            
                                            <div class="d-flex align-items-center flex-grow-1">
                                                <div class="drag-handle me-3" style="cursor: move;">
                                                    <i class="fas fa-grip-vertical text-muted"></i>
                                                </div>
                                                
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold">
                                                        @if($item->depth_level > 0)
                                                            <span class="text-muted me-2">└─</span>
                                                        @endif
                                                        @if($item->icon)
                                                            <i class="{{ $item->icon }} me-2"></i>
                                                        @endif
                                                        {{ $item->getTranslated('title', app()->getLocale()) }}
                                                    </div>
                                                    <div class="text-muted small">
                                                        {{ $item->getTranslated('url_value', app()->getLocale()) }}
                                                        <span class="badge badge-outline ms-2">{{ ucfirst($item->url_type) }}</span>
                                                        @if($item->target !== '_self')
                                                            <span class="badge badge-outline ms-1">{{ $item->target }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center">
                                                <!-- Aktif/Pasif Durumu -->
                                                <div class="form-check form-switch me-3">
                                                    <input class="form-check-input" 
                                                        type="checkbox" 
                                                        @if($item->is_active) checked @endif
                                                        wire:click="toggleMenuItemStatus({{ $item->menu_item_id }})"
                                                        id="status-{{ $item->menu_item_id }}">
                                                </div>

                                                <!-- Sıralama Numarası -->
                                                <span class="badge badge-outline me-3">{{ $item->sort_order }}</span>

                                                <!-- İşlemler -->
                                                <div class="dropdown">
                                                    <button type="button" 
                                                        class="btn btn-ghost-secondary btn-sm dropdown-toggle" 
                                                        data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <button class="dropdown-item" 
                                                            wire:click="editMenuItem({{ $item->menu_item_id }})">
                                                            <i class="fas fa-edit me-2"></i>{{ __('admin.edit') }}
                                                        </button>
                                                        <button class="dropdown-item text-danger" 
                                                            wire:click="deleteMenuItem({{ $item->menu_item_id }})"
                                                            onclick="return confirm('{{ __('admin.are_you_sure') }}')">
                                                            <i class="fas fa-trash me-2"></i>{{ __('admin.delete') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-list-ul fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">{{ __('menumanagement::admin.no_menu_items_found') }}</p>
                                    <p class="text-muted small">{{ __('menumanagement::admin.add_first_menu_item') }}</p>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.sortable-item {
    transition: all 0.3s ease;
}

.sortable-item:hover {
    background-color: rgba(var(--tblr-primary-rgb), 0.05);
}

.drag-handle:hover {
    color: var(--tblr-primary) !important;
}

.list-group-item {
    border-left: 3px solid transparent;
}

.list-group-item:hover {
    border-left-color: var(--tblr-primary);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sortable işlevselliği (SortableJS gerektirir)
    if (typeof Sortable !== 'undefined') {
        const sortableList = document.getElementById('sortable-menu-items');
        if (sortableList) {
            new Sortable(sortableList, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    const itemIds = Array.from(sortableList.children).map(item => 
                        item.getAttribute('data-id')
                    );
                    @this.call('updateMenuItemOrder', itemIds);
                }
            });
        }
    }
});
</script>
@endpush