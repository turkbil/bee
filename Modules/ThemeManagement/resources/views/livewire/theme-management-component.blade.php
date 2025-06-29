@include('thememanagement::helper')
<div class="card">
    <div class="card-body">
        <!-- {{ __('thememanagement::admin.header_section') }} -->
        <div class="row mb-3">
            <!-- {{ __('thememanagement::admin.search_box') }} -->
            <div class="col">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live="search" class="form-control"
                        placeholder="{{ __('thememanagement::admin.search_placeholder') }}">
                </div>
            </div>
            <!-- {{ __('thememanagement::admin.middle_loading') }} -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">{{ __('admin.loading') }}</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- {{ __('thememanagement::admin.right_side') }} -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- {{ __('thememanagement::admin.page_count_selection') }} -->
                    <div style="min-width: 60px">
                        <select wire:model.live="perPage" class="form-select">
                            <option value="10">10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                            <option value="1000">1000</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- {{ __('thememanagement::admin.theme_cards') }} -->
        <div class="row row-cards">
            @forelse($themes as $theme)
            <div class="col-md-4 col-xl-3" wire:key="theme-{{ $theme->theme_id }}">
                <div class="card {{ $theme->is_default ? 'card-active' : '' }}">
                    <!-- {{ __('thememanagement::admin.theme_preview_image') }} -->
                    <div class="card-img-top img-responsive img-responsive-16x9" style="background-image: url({{ $theme->getFirstMedia('images') ? url($theme->getFirstMedia('images')->getUrl()) : asset('assets/static/illustrations/undraw_design_components_9vy6.svg') }}); background-size: cover; background-position: center; height: 160px;"></div>
                    <div class="card-body">
                        <h3 class="card-title">
                            @if($editingTitleId === $theme->theme_id)
                            <div class="d-flex align-items-center gap-2" x-data
                                @click.outside="$wire.updateTitleInline()">
                                <div class="flexible-input-wrapper">
                                    <input type="text" wire:model.defer="newTitle"
                                        class="form-control form-control-sm flexible-input"
                                        placeholder="{{ __('thememanagement::admin.new_title') }}" wire:keydown.enter="updateTitleInline"
                                        wire:keydown.escape="$set('editingTitleId', null)" x-init="$nextTick(() => {
                                                $el.focus();
                                                $el.style.width = '20px';
                                                $el.style.width = ($el.scrollWidth + 2) + 'px';
                                            })" x-on:input="
                                                $el.style.width = '20px';
                                                $el.style.width = ($el.scrollWidth + 2) + 'px'
                                            " style="min-width: 60px; max-width: 100%;">
                                </div>
                                <button class="btn px-2 py-1 btn-outline-success" wire:click="updateTitleInline">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn px-2 py-1 btn-outline-danger"
                                    wire:click="$set('editingTitleId', null)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @else
                            <div class="d-flex align-items-center justify-content-between">
                                <span>{{ $theme->title }}</span>
                                <button class="btn btn-sm px-2 py-1 edit-icon ms-2"
                                    wire:click="startEditingTitle({{ $theme->theme_id }}, '{{ $theme->title }}')">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                            @endif
                        </h3>
                        
                        <div class="mb-2">
                            <span class="badge bg-blue-lt">{{ $theme->name }}</span>
                            <span class="badge bg-purple-lt">{{ $theme->folder_name }}</span>
                        </div>
                        
                        <div class="text-muted">
                            {{ Str::limit($theme->description, 80) }}
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-between align-items-center">
                            <div>
                                @if($theme->is_default)
                                <span class="badge bg-green">{{ __('admin.default') }}</span>
                                @endif
                                <span class="badge {{ $theme->is_active ? 'bg-blue' : 'bg-red' }}">
                                    {{ $theme->is_active ? __('admin.active') : __('admin.inactive') }}
                                </span>
                            </div>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="{{ route('admin.thememanagement.manage', $theme->theme_id) }}" class="dropdown-item">
                                        <i class="fas fa-edit me-2"></i> {{ __('admin.edit') }}
                                    </a>
                                    @if(!$theme->is_default)
                                    <a href="javascript:void(0);" wire:click="setDefault({{ $theme->theme_id }})" class="dropdown-item">
                                        <i class="fas fa-check-circle me-2"></i> {{ __('thememanagement::admin.set_as_default') }}
                                    </a>
                                    @endif
                                    <a href="javascript:void(0);" wire:click="toggleActive({{ $theme->theme_id }})" class="dropdown-item">
                                        <i class="fas {{ $theme->is_active ? 'fa-ban' : 'fa-check' }} me-2"></i> 
                                        {{ $theme->is_active ? __('thememanagement::admin.deactivate') : __('thememanagement::admin.activate') }}
                                    </a>
                                    @if(!$theme->is_default)
                                    <a href="javascript:void(0);" wire:click="$dispatch('showDeleteModal', {
                                        module: 'thememanagement',
                                        id: {{ $theme->theme_id }},
                                        title: '{{ $theme->title }}'
                                    })" class="dropdown-item text-danger">
                                        <i class="fas fa-trash me-2"></i> {{ __('admin.delete') }}
                                    </a>
                                    @else
                                    <a href="javascript:void(0);" class="dropdown-item text-muted" onclick="event.preventDefault();">
                                        <i class="fas fa-lock me-2"></i> {{ __('thememanagement::admin.default_theme_cannot_be_deleted') }}
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty">
                    <div class="empty-img">
                        <img src="{{ asset('assets/static/illustrations/undraw_no_data_re_kwbl.svg') }}" height="128" alt="">
                    </div>
                    <p class="empty-title">{{ __('thememanagement::admin.no_theme_found') }}</p>
                    <p class="empty-subtitle text-muted">
                        {{ __('thememanagement::admin.no_theme_description') }}
                    </p>
                    <div class="empty-action">
                        <a href="{{ route('admin.thememanagement.manage') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> {{ __('thememanagement::admin.add_new_theme') }}
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        
        <!-- {{ __('thememanagement::admin.pagination') }} -->
        <div class="mt-4">
            {{ $themes->links() }}
        </div>
    </div>
    
    <livewire:modals.delete-modal />
</div>