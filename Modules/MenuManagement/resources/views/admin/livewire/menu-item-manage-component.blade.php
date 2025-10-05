@php
    View::share('pretitle', $editingMenuItemId ? 'Menü Öğesi Düzenleme' : 'Yeni Menü Öğesi Ekleme');
@endphp

<div wire:key="menu-item-manage-component" wire:id="menu-item-manage-component">
    {{-- Helper dosyası --}}
    @include('menumanagement::admin.helper')
    @include('admin.partials.error_message')

    <!-- İki Sütunlu Layout -->
    <div class="row">
        <!-- Sol Sütun: Form -->
        <div class="col-lg-5 col-md-12 mb-3">
            <form method="post" wire:submit.prevent="saveMenuItem">
                <div class="card">
                    <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="menu_active_tab">

                        <x-manage.language.switcher :current-language="$currentLanguage" />

                    </x-tab-system>
                    <div class="card-body">
                        <div class="tab-content" id="contentTabContent">
                            <!-- Menü Öğesi Ekleme Tab -->
                            <div class="tab-pane fade show active" id="0" role="tabpanel">

                                @foreach ($availableLanguages as $lang)
                                    @php
                                        $langData = $multiLangInputs[$lang] ?? [];
                                        $tenantLanguages = \Modules\LanguageManagement\app\Models\TenantLanguage::where('is_active', true)->get();
                                        $langName = $tenantLanguages->where('code', $lang)->first()?->native_name ?? strtoupper($lang);
                                    @endphp

                                    <div class="language-content" data-language="{{ $lang }}"
                                        style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">

                                        <!-- Başlık - Page Pattern -->
                                        <div class="form-floating mb-3">
                                            <input type="text" 
                                                class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror" 
                                                wire:model="multiLangInputs.{{ $lang }}.title"
                                                placeholder="{{ __('menumanagement::admin.title') }}">
                                            <label>
                                                {{ __('menumanagement::admin.title') }}
                                                @if ($lang === session('site_default_language', 'tr'))
                                                    <span class="required-star">★</span>
                                                @endif
                                            </label>
                                            @error('multiLangInputs.' . $lang . '.title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                @endforeach

                                <!-- URL Tipi -->
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('url_type') is-invalid @enderror" wire:model="url_type" wire:change="urlTypeChanged">
                                        <option value="">{{ __('menumanagement::admin.select_url_type') }}</option>
                                        <option value="url">{{ __('menumanagement::admin.url_link') }}</option>
                                        <option value="module">{{ __('menumanagement::admin.module_content') }}</option>
                                    </select>
                                    <label>{{ __('menumanagement::admin.url_type') }}</label>
                                    @error('url_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- URL Tipi Info Mesajları -->
                                @if($url_type === 'url')
                                    <div class="alert alert-info alert-important mb-3" role="alert">
                                        <div class="d-flex">
                                            <div>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <circle cx="12" cy="12" r="9"></circle>
                                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                    <polyline points="11,12 12,12 12,16 13,16"></polyline>
                                                </svg>
                                            </div>
                                            <div>{{ __('menumanagement::admin.url_info') }}</div>
                                        </div>
                                    </div>
                                @endif

                                <!-- URL Formu -->
                                @if($url_type)
                                    <!-- Manuel URL Girişi -->
                                    @if($url_type === 'url')
                                        <div class="form-floating mb-3">
                                            <input type="text" 
                                                class="form-control @error('url_data.url') is-invalid @enderror" 
                                                wire:model.lazy="url_data.url"
                                                wire:change="updateUrlData($event.target.value)"
                                                placeholder="{{ __('menumanagement::admin.url_value') }}">
                                            <label>{{ __('menumanagement::admin.url_value') }}</label>
                                            @error('url_data.url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif
                                    
                                    <!-- Modül Seçimi -->
                                    @if($url_type === 'module')
                                        <div class="form-floating mb-3">
                                            <select class="form-select @error('url_data.module') is-invalid @enderror"
                                                wire:model.live="selectedModule">
                                                <option value="">{{ __('menumanagement::admin.select_module') }}</option>
                                                @foreach($availableModules as $module)
                                                    <option value="{{ $module['slug'] }}">{{ $module['label'] }}</option>
                                                @endforeach
                                            </select>
                                            <label>{{ __('menumanagement::admin.select_module') }}</label>
                                            @error('url_data.module')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- URL Tipi Seçimi -->
                                        @if($selectedModule && count($moduleUrlTypes) > 0)
                                            <div class="form-floating mb-3">
                                                <select class="form-select @error('url_data.type') is-invalid @enderror"
                                                    wire:model.live="selectedUrlType">
                                                    <option value="">{{ __('menumanagement::admin.select_url_type') }}</option>
                                                    @foreach($moduleUrlTypes as $type)
                                                        <option value="{{ $type['type'] }}">{{ $type['label'] }}</option>
                                                    @endforeach
                                                </select>
                                                <label>{{ __('menumanagement::admin.select_url_type') }}</label>
                                                @error('url_data.type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif
                                        
                                        <!-- İçerik Seçimi -->
                                        @if($selectedUrlType && count($moduleContent) > 0)
                                            <div class="form-floating mb-3">
                                                <select class="form-select @error('url_data.id') is-invalid @enderror" 
                                                    wire:model="url_data.id" 
                                                    wire:change="contentSelected($event.target.value)">
                                                    <option value="">{{ __('menumanagement::admin.select_content') }}</option>
                                                    @foreach($moduleContent as $content)
                                                        <option value="{{ $content['id'] }}">{{ $content['label'] }}</option>
                                                    @endforeach
                                                </select>
                                                <label>{{ __('menumanagement::admin.select_content') }}</label>
                                                @error('url_data.id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif
                                    @endif
                                @endif

                                <!-- Gelişmiş Seçenekler (Accordion) -->
                                <div class="mb-3">
                                    <button class="btn btn-outline-primary w-100 d-flex justify-content-between align-items-center" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#advanced-options"
                                        aria-expanded="false"
                                        style="border-radius: 0.5rem; transition: all 0.15s ease;">
                                        <span><i class="fas fa-cog me-2"></i>{{ __('menumanagement::admin.advanced_options') }}</span>
                                        <i class="fas fa-chevron-down" id="advanced-toggle-icon"></i>
                                    </button>
                                    <div id="advanced-options" class="collapse mt-3">
                                        <div class="pt-3">

                                                <!-- Üst Menü -->
                                                <div class="form-floating mb-3">
                                                    <select class="form-select" wire:model.defer="parent_id">
                                                        <option value="">{{ __('menumanagement::admin.no_parent') }}</option>
                                                        @foreach($this->hierarchicalMenuItems as $item)
                                                            <option value="{{ $item['id'] }}">
                                                                {{ $item['title'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label>{{ __('menumanagement::admin.parent_item') }}</label>
                                                </div>

                                                <!-- Hedef -->
                                                <div class="form-floating mb-3">
                                                    <select class="form-select @error('target') is-invalid @enderror" wire:model.defer="target">
                                                        <option value="_self" @if($target === '_self') selected @endif>{{ __('menumanagement::admin.same_window') }}</option>
                                                        <option value="_blank" @if($target === '_blank') selected @endif>{{ __('menumanagement::admin.new_window') }}</option>
                                                    </select>
                                                    <label>{{ __('menumanagement::admin.target') }}</label>
                                                    @error('target')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- İkon -->
                                                <div class="form-floating mb-3">
                                                    <input type="text" 
                                                        class="form-control" 
                                                        wire:model.defer="icon"
                                                        placeholder="{{ __('menumanagement::admin.icon') }}">
                                                    <label>{{ __('menumanagement::admin.icon') }}</label>
                                                </div>

                                                <!-- Aktif Durumu - Page Pattern -->
                                                <div class="mb-3">
                                                    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                                        <input type="checkbox" id="is_active" name="is_active" wire:model="is_active"
                                                            value="1"
                                                            {{ !isset($is_active) || $is_active ? 'checked' : '' }} />

                                                        <div class="state p-success p-on ms-2">
                                                            <label>{{ __('menumanagement::admin.active') }}</label>
                                                        </div>
                                                        <div class="state p-danger p-off ms-2">
                                                            <label>{{ __('menumanagement::admin.inactive') }}</label>
                                                        </div>
                                                    </div>
                                                </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- Kaydet/Güncelle Butonları -->
                                <div class="d-flex justify-content-end gap-2">
                                    @if($editingMenuItemId)
                                        <button type="button" wire:click="cancelEdit" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>
                                            {{ __('admin.cancel') }}
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            {{ __('admin.update') }}
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            {{ __('menumanagement::admin.add_menu_item') }}
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Sağ Sütun: Menü Öğeleri Listesi -->
        <div class="col-lg-7 col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- Header Bölümü - Portfolio Pattern -->
                    <div class="row mb-3">
                        <!-- Dinamik Başlık - Sol -->
                        <div class="col">
                            <h3 class="card-title mb-0">
                                @if($headerMenu && $headerMenu->menu_id != 1)
                                    {{ $headerMenu->getTranslated('name', app()->getLocale()) }} {{ __('menumanagement::admin.menu_items') }}
                                @else
                                    {{ __('menumanagement::admin.header_menu_items') }}
                                @endif
                            </h3>
                            <p class="text-muted small mb-0">
                                @if($headerMenu && $headerMenu->menu_id != 1)
                                    {{ $headerMenu->getTranslated('name', app()->getLocale()) }} menüsünün öğelerini yönetin
                                @else
                                    {{ __('menumanagement::admin.header_menu_description') }}
                                @endif
                            </p>
                        </div>
                        <!-- Ortadaki Loading -->
                        <div class="col position-relative">
                            <div wire:loading
                                wire:target="toggleMenuItemStatus, updateMenuItemOrder, refreshMenuItems, addMenuItem, editMenuItem, confirmDelete, search"
                                class="position-absolute top-50 start-50 translate-middle text-center"
                                style="width: 100%; max-width: 250px; z-index: 10;">
                                <div class="small text-muted mb-2">{{ __('admin.updating') }}</div>
                                <div class="progress mb-1">
                                    <div class="progress-bar progress-bar-indeterminate"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Arama Kutusu - Sağ -->
                        <div class="col">
                            <div class="d-flex align-items-center justify-content-end">
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                        placeholder="{{ __('menumanagement::admin.search_menu_items') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tablo Bölümü -->
                <div class="card-body p-0">
                    <!-- Menu Items List -->
                    <div wire:loading.class="opacity-50" wire:target="toggleMenuItemStatus, updateMenuItemOrder, refreshMenuItems, addMenuItem, editMenuItem, confirmDelete, search">
                        <div class="list-group list-group-flush" id="menu-sortable-list">
                            @forelse($headerMenuItems as $item)
                                
                                <!-- Menü Öğesi - Widget Pattern -->
                                @php
                                    $depthLevel = $item->depth_level ?? 0;
                                    $indentPx = $depthLevel * 30; // 30px per level
                                @endphp
                                <div class="menu-item list-group-item p-2"
                                    style="padding-left: {{ 8 + $indentPx }}px !important;"
                                    wire:key="menu-{{ $item->item_id }}"
                                    data-id="{{ $item->item_id }}"
                                    data-depth="{{ $depthLevel }}"
                                    @if($item->parent_id)
                                        data-parent-id="{{ $item->parent_id }}"
                                    @endif>
                                    <div class="d-flex align-items-center">
                                        
                                        <!-- Drag Handle - Widget Pattern -->
                                        <div class="menu-drag-handle me-2">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </div>
                                        
                                        <!-- Icon - Widget Pattern -->
                                        <div class="{{ $depthLevel > 0 ? 'bg-secondary-lt' : 'bg-primary-lt' }} rounded-2 d-flex align-items-center justify-content-center me-2" 
                                            style="width: 2.5rem; height: 2.5rem;">
                                            <i class="{{ $item->icon ?: 'fas fa-link' }}"></i>
                                        </div>
                                        
                                        <!-- Content - Widget Pattern -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <div class="h4 mb-0">{{ $item->getTranslated('title', app()->getLocale()) }}</div>
                                                </div>
                                                
                                                <!-- Actions - Widget Pattern -->
                                                <div class="d-flex align-items-center gap-3">
                                                    <!-- Active/Inactive Toggle -->
                                                    <div>
                                                        <button wire:click="toggleMenuItemStatus({{ $item->item_id }})"
                                                            class="btn btn-icon btn-sm {{ $item->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" 
                                                            title="{{ $item->is_active ? __('admin.deactivate') : __('admin.activate') }}">
                                                            
                                                            <div wire:loading wire:target="toggleMenuItemStatus({{ $item->item_id }})"
                                                                class="spinner-border spinner-border-sm">
                                                            </div>
                                                            
                                                            <div wire:loading.remove wire:target="toggleMenuItemStatus({{ $item->item_id }})">
                                                                @if($item->is_active)
                                                                <i class="fas fa-check fa-lg"></i>
                                                                @else
                                                                <i class="fas fa-times fa-lg"></i>
                                                                @endif
                                                            </div>
                                                        </button>
                                                    </div>
                                                    <!-- Edit Button -->
                                                    <div>
                                                        <a href="javascript:void(0);" wire:click="editMenuItem({{ $item->item_id }})" 
                                                           data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('admin.edit') }}">
                                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                                        </a>
                                                    </div>
                                                    <!-- Menu Dropdown -->
                                                    <div class="lh-1">
                                                        <div class="dropdown mt-1">
                                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="false">
                                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a href="javascript:void(0);" 
                                                                   onclick="Livewire.find('{{ $_instance->getId() }}').call('openDeleteModal', {{ $item->item_id }}, '{{ addslashes($item->getTranslated('title', app()->getLocale())) }}')"
                                                                   class="dropdown-item link-danger">
                                                                    <i class="fas fa-trash me-2"></i> {{ __('admin.delete') }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            @empty
                                <div class="list-group-item py-4">
                                    <div class="empty">
                                        <div class="empty-img">
                                            <i class="fas fa-list-ul fa-4x text-muted"></i>
                                        </div>
                                        <p class="empty-title mt-2">{{ __('menumanagement::admin.no_menu_items_found') }}</p>
                                        <p class="empty-subtitle text-muted">{{ __('menumanagement::admin.add_first_menu_item') }}</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MenuManagement Delete Modal -->
    @if($showDeleteModal)
    <div class="modal modal-blur fade show" id="menuitem-delete-modal" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" wire:click="closeDeleteModal"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <svg class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 9v2m0 4v.01"/>
                        <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/>
                    </svg>
                    <h3>{{ __('admin.delete_confirmation') }}</h3>
                    <div class="text-muted">
                        <strong>"{{ $deleteItemTitle }}"</strong> menü öğesini silmek istediğinize emin misiniz?<br>
                        Bu işlem geri alınamaz!
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button class="btn w-100" wire:click="closeDeleteModal">
                                    {{ __('admin.cancel') }}
                                </button>
                            </div>
                            <div class="col">
                                <button class="btn btn-danger w-100" wire:click="confirmDelete" wire:loading.attr="disabled">
                                    <span wire:loading.remove>{{ __('admin.delete') }}</span>
                                    <span wire:loading>{{ __('admin.deleting') }}...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('admin-assets/css/category-sortable.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
<script src="{{ asset('admin-assets/js/menu-sortable.js') }}"></script>
@endpush
