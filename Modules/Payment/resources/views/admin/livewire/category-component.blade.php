@php
    View::share('pretitle', __('payment::admin.category_management'));
@endphp

<div wire:key="payment-category-component" wire:id="payment-category-component">
    {{-- Helper dosyası --}}
    @include('payment::admin.helper-category')
    @include('admin.partials.error_message')

    <!-- İki Sütunlu Layout -->
    <div class="row">
        <!-- Sol Sütun: Form -->
        <div class="col-lg-5 col-md-12 mb-3">
            <form method="post" wire:submit.prevent="addCategory">
                <div class="card">
                    <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="category_active_tab">
                        <x-manage.language.switcher :current-language="$currentLanguage" />
                    </x-tab-system>
                    <div class="card-body">
                        <div class="tab-content" id="contentTabContent">
                            <!-- Kategori Ekleme Tab -->
                            <div class="tab-pane fade show active" id="0" role="tabpanel">

                                @foreach ($availableLanguages as $lang)
                                    @php
                                        $langData = $multiLangInputs[$lang] ?? [];
                                        $tenantLanguages = \Modules\LanguageManagement\app\Models\TenantLanguage::where('is_active', true)->get();
                                        $langName = $tenantLanguages->where('code', $lang)->first()?->native_name ?? strtoupper($lang);
                                    @endphp

                                    <div class="language-content" data-language="{{ $lang }}"
                                        style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">

                                        <!-- Kategori Adı - MenuManagement Pattern -->
                                        <div class="form-floating mb-3">
                                            <input type="text"
                                                class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror"
                                                wire:model="multiLangInputs.{{ $lang }}.title"
                                                placeholder="{{ __('payment::admin.category_title') }}">
                                            <label>
                                                {{ __('payment::admin.category_title') }}
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

                                <!-- Üst Kategori (Parent) -->
                                <div class="form-floating mb-3">
                                    <select class="form-select" wire:model.defer="parent_id">
                                        <option value="">{{ __('payment::admin.main_category') }}</option>
                                        @foreach($this->hierarchicalCategories as $cat)
                                            <option value="{{ $cat['id'] }}">
                                                {{ $cat['title'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>{{ __('payment::admin.parent_category') }}</label>
                                </div>

                                <!-- Aktif Durumu - MenuManagement Pattern -->
                                <div class="mb-3">
                                    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                        <input type="checkbox" id="is_active" name="is_active" wire:model="is_active"
                                            value="1"
                                            {{ !isset($is_active) || $is_active ? 'checked' : '' }} />

                                        <div class="state p-success p-on ms-2">
                                            <label>{{ __('payment::admin.active') }}</label>
                                        </div>
                                        <div class="state p-danger p-off ms-2">
                                            <label>{{ __('payment::admin.inactive') }}</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kaydet Butonu -->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        {{ __('payment::admin.new_category') }}
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sağ Sütun: Kategori Listesi -->
        <div class="col-lg-7 col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- Header Bölümü - MenuManagement Pattern -->
                    <div class="row mb-3">
                        <!-- Dinamik Başlık - Sol -->
                        <div class="col">
                            <h3 class="card-title mb-0">{{ __('payment::admin.categories') }}</h3>
                            <p class="text-muted small mb-0">{{ __('payment::admin.category_management') }}</p>
                        </div>
                        <!-- Ortadaki Loading -->
                        <div class="col position-relative">
                            <div wire:loading
                                wire:target="toggleCategoryStatus, updateOrder, addCategory, search"
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
                                        placeholder="{{ __('payment::admin.search_categories') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tablo Bölümü -->
                <div class="card-body p-0">
                    <!-- Category Items List -->
                    <div wire:loading.class="opacity-50" wire:target="toggleCategoryStatus, updateOrder, addCategory, search">
                        <div class="list-group list-group-flush" id="category-sortable-list">
                            @forelse($categories as $item)

                                <!-- Kategori Öğesi - MenuManagement Pattern -->
                                @php
                                    $depthLevel = $item->depth_level ?? 0;
                                    $indentPx = $depthLevel * 30; // 30px per level
                                @endphp
                                <div class="category-item list-group-item p-2"
                                    style="padding-left: {{ 8 + $indentPx }}px !important;"
                                    wire:key="category-{{ $item->category_id }}"
                                    data-id="{{ $item->category_id }}"
                                    data-depth="{{ $depthLevel }}"
                                    @if($item->parent_id)
                                        data-parent-id="{{ $item->parent_id }}"
                                    @endif>
                                    <div class="d-flex align-items-center">

                                        <!-- Drag Handle - MenuManagement Pattern -->
                                        <div class="category-drag-handle me-2">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </div>

                                        <!-- Icon - MenuManagement Pattern -->
                                        <div class="{{ $depthLevel > 0 ? 'bg-secondary-lt' : 'bg-primary-lt' }} rounded-2 d-flex align-items-center justify-content-center me-2"
                                            style="width: 2.5rem; height: 2.5rem;">
                                            <i class="{{ $depthLevel > 0 ? 'fas fa-folder-open' : 'fas fa-folder' }}"></i>
                                        </div>

                                        <!-- Content - MenuManagement Pattern -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <div class="h4 mb-0">{{ $item->getTranslated('title', app()->getLocale()) }}</div>
                                                </div>

                                                <!-- Actions - MenuManagement Pattern -->
                                                <div class="d-flex align-items-center gap-3">
                                                    <!-- Payment Count Badge -->
                                                    @if($item->payments_count > 0)
                                                    <div>
                                                        <span class="badge bg-blue-lt">{{ $item->payments_count }}</span>
                                                    </div>
                                                    @endif
                                                    <!-- Active/Inactive Toggle -->
                                                    <div>
                                                        <button wire:click="toggleCategoryStatus({{ $item->category_id }})"
                                                            class="btn btn-icon btn-sm {{ $item->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ $item->is_active ? __('admin.deactivate') : __('admin.activate') }}">

                                                            <div wire:loading wire:target="toggleCategoryStatus({{ $item->category_id }})"
                                                                class="spinner-border spinner-border-sm">
                                                            </div>

                                                            <div wire:loading.remove wire:target="toggleCategoryStatus({{ $item->category_id }})">
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
                                                        <a href="{{ route('admin.payment.category.manage', $item->category_id) }}"
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
                                                                   onclick="Livewire.find('{{ $_instance->getId() }}').call('openDeleteModal', {{ $item->category_id }}, '{{ addslashes($item->getTranslated('title', app()->getLocale())) }}')"
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
                                            <i class="fas fa-folder-open fa-4x text-muted"></i>
                                        </div>
                                        <p class="empty-title mt-2">{{ __('payment::admin.no_categories_found') }}</p>
                                        <p class="empty-subtitle text-muted">{{ __('payment::admin.no_results') }}</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Category Delete Modal -->
    <livewire:modals.category-delete-modal />
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('admin-assets/css/category-sortable.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
<script src="{{ asset('admin-assets/js/category-sortable.js') }}"></script>

{{-- Category JavaScript Variables --}}
<script>
    window.currentCategoryId = 1;
    window.currentLanguage = '{{ $currentLanguage }}';
</script>
@endpush
