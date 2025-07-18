@include('widgetmanagement::helper')
<div>
    <div class="row g-3">
        
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $editCategoryId ? __('widgetmanagement::admin.edit_category') : __('widgetmanagement::admin.new_category') }}</h3>
                </div>
                <div class="card-body position-relative">
                    
                    <div wire:loading 
                        wire:target="saveEdit, quickAdd, cancelEdit"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px; z-index: 10;">
                        <div class="small text-muted mb-2">{{ __('widgetmanagement::admin.updating') }}</div>
                        <div class="progress mb-1">
                            <div class="category-progress-bar-indeterminate"></div>
                        </div>
                    </div>
                    
                    <form wire:submit.prevent="{{ $editCategoryId ? 'saveEdit' : 'quickAdd' }}">
                        <div class="form-floating mb-3">
                            @if($editCategoryId)
                                <input type="text" 
                                    wire:model="editData.title" 
                                    class="form-control @error('editData.title') is-invalid @enderror" 
                                    placeholder="{{ __('widgetmanagement::admin.category_title_placeholder') }}">
                                <label>{{ __('widgetmanagement::admin.category_title') }}</label>
                                @error('editData.title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @else
                                <input type="text" 
                                    wire:model="title" 
                                    class="form-control @error('title') is-invalid @enderror" 
                                    placeholder="{{ __('widgetmanagement::admin.category_title_placeholder') }}">
                                <label>{{ __('widgetmanagement::admin.category_title') }}</label>
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                        
                        <div class="form-floating mb-3">
                            @if($editCategoryId)
                                <input type="text" 
                                    wire:model="editData.slug" 
                                    class="form-control @error('editData.slug') is-invalid @enderror"
                                    placeholder="{{ __('widgetmanagement::admin.category_slug_placeholder') }}">
                                <label>{{ __('widgetmanagement::admin.slug') }}</label>
                                @error('editData.slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @else
                                <input type="text" 
                                    wire:model="slug" 
                                    class="form-control @error('slug') is-invalid @enderror"
                                    placeholder="{{ __('widgetmanagement::admin.category_slug_placeholder') }}">
                                <label>{{ __('widgetmanagement::admin.slug') }}</label>
                                @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                            <small class="form-hint">{{ __('widgetmanagement::admin.auto_slug_note') }}</small>
                        </div>
                        
                        <div class="form-floating mb-3">
                            @if($editCategoryId)
                                <select 
                                    wire:model="editData.parent_id" 
                                    class="form-control"
                                    data-choices
                                    data-choices-search="{{ count($parentCategories) > 6 ? 'true' : 'false' }}"
                                    data-choices-placeholder="{{ __('widgetmanagement::admin.select_parent_category') }}">
                                    <option value="">{{ __('widgetmanagement::admin.add_as_main_category') }}</option>
                                    @foreach($parentCategories as $parent)
                                        @if($parent->widget_category_id != $editCategoryId)
                                        <option value="{{ $parent->widget_category_id }}">{{ $parent->title }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <label>{{ __('widgetmanagement::admin.parent_category') }}</label>
                            @else
                                <select 
                                    wire:model="parentId" 
                                    class="form-control"
                                    data-choices
                                    data-choices-search="{{ count($parentCategories) > 6 ? 'true' : 'false' }}"
                                    data-choices-placeholder="{{ __('widgetmanagement::admin.select_parent_category') }}">
                                    <option value="">{{ __('widgetmanagement::admin.add_as_main_category') }}</option>
                                    @foreach($parentCategories as $parent)
                                        <option value="{{ $parent->widget_category_id }}">{{ $parent->title }}</option>
                                    @endforeach
                                </select>
                                <label>{{ __('widgetmanagement::admin.parent_category') }}</label>
                            @endif
                        </div>
                        
                        <div class="form-floating mb-3">
                            @if($editCategoryId)
                                <textarea 
                                    wire:model="editData.description" 
                                    class="form-control" 
                                    rows="3"
                                    placeholder="{{ __('widgetmanagement::admin.category_description_placeholder') }}"></textarea>
                                <label>{{ __('widgetmanagement::admin.description') }}</label>
                            @else
                                <textarea 
                                    wire:model="description" 
                                    class="form-control" 
                                    rows="3"
                                    placeholder="{{ __('widgetmanagement::admin.category_description_placeholder') }}"></textarea>
                                <label>{{ __('widgetmanagement::admin.description') }}</label>
                            @endif
                        </div>
                        
                        <div class="form-floating mb-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="{{ $editCategoryId ? $editData['icon'] : ($icon ?? 'fa-solid fa-folder') }}"></i>
                                </span>
                                @if($editCategoryId)
                                    <input type="text" 
                                        wire:model="editData.icon" 
                                        class="form-control"
                                        placeholder="fa-folder">
                                @else
                                    <input type="text" 
                                        wire:model="icon" 
                                        class="form-control"
                                        placeholder="fa-folder">
                                @endif
                            </div>
                            <small class="form-hint">{{ __('widgetmanagement::admin.icon_placeholder') }}</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                @if($editCategoryId)
                                    <input type="checkbox" wire:model="editData.is_active" value="1" />
                                @else
                                    <input type="checkbox" wire:model="is_active" value="1" />
                                @endif
                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('widgetmanagement::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('widgetmanagement::admin.not_active') }}</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            @if($editCategoryId)
                            <button type="button" class="btn btn-outline-secondary" wire:click="cancelEdit">
                                <i class="fas fa-times me-1"></i> {{ __('widgetmanagement::admin.cancel') }}
                            </button>
                            @endif
                            <button type="submit" class="btn btn-primary ms-auto" wire:loading.attr="disabled">
                                <i class="fas fa-save me-1"></i> {{ $editCategoryId ? __('widgetmanagement::admin.update') : __('widgetmanagement::admin.add') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title">{{ __('widgetmanagement::admin.categories') }}</h3>
                        </div>
                        <div class="col-auto">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                    placeholder="{{ __('widgetmanagement::admin.search_placeholder') }}">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0 position-relative">
                    
                    <div wire:loading
                        wire:target="loadCategories, toggleActive, delete, updatedSearch, updateOrder"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px; z-index: 10;">
                        <div class="small text-muted mb-2">{{ __('widgetmanagement::admin.updating') }}</div>
                        <div class="progress mb-1">
                            <div class="category-progress-bar-indeterminate"></div>
                        </div>
                    </div>
                    
                    <div wire:loading.class="opacity-50" wire:target="loadCategories, toggleActive, delete, updatedSearch, updateOrder">
                        <div class="list-group list-group-flush" id="category-sortable-list">
                            @forelse($categories as $category)
                                
                                <div class="category-item list-group-item p-2" 
                                    wire:key="category-{{ $category->widget_category_id }}" 
                                    id="item-{{ $category->widget_category_id }}" 
                                    data-id="{{ $category->widget_category_id }}"
                                    data-is-parent="1">
                                    <div class="d-flex align-items-center">
                                        
                                        <div class="category-drag-handle me-2">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </div>
                                        
                                        
                                        <div class="bg-primary-lt rounded-2 d-flex align-items-center justify-content-center me-2" 
                                            style="width: 2.5rem; height: 2.5rem;">
                                            <i class="{{ $category->icon ?? 'fas fa-folder' }}"></i>
                                        </div>
                                        
                                        
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <div class="h4 mb-0">{{ $category->title }}</div>
                                                </div>
                                                
                                                
                                                <div class="d-flex align-items-center">
                                                    <div class="container">
                                                        <div class="row">
                                                            <div class="col">
                                                                <button wire:click="toggleActive({{ $category->widget_category_id }})"
                                                                    class="btn btn-icon btn-sm {{ $category->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top" 
                                                                    title="{{ $category->is_active ? __('widgetmanagement::admin.deactivate') : __('widgetmanagement::admin.activate') }}">
                                                                    
                                                                    <div wire:loading wire:target="toggleActive({{ $category->widget_category_id }})"
                                                                        class="spinner-border spinner-border-sm">
                                                                    </div>
                                                                    
                                                                    <div wire:loading.remove wire:target="toggleActive({{ $category->widget_category_id }})">
                                                                        @if($category->is_active)
                                                                        <i class="fas fa-check fa-lg"></i>
                                                                        @else
                                                                        <i class="fas fa-times fa-lg"></i>
                                                                        @endif
                                                                    </div>
                                                                </button>
                                                            </div>
                                                            <div class="col">
                                                                <a href="javascript:void(0);" wire:click="startEdit({{ $category->widget_category_id }})" 
                                                                   data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('widgetmanagement::admin.edit') }}">
                                                                    <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                                                </a>
                                                            </div>
                                                            <div class="col lh-1">
                                                                <div class="dropdown mt-1">
                                                                    <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                                        aria-haspopup="true" aria-expanded="false">
                                                                        <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                                                    </a>
                                                                    <div class="dropdown-menu dropdown-menu-end">
                                                                        <a href="javascript:void(0);" wire:click="delete({{ $category->widget_category_id }})" 
                                                                           class="dropdown-item link-danger">
                                                                            <i class="fas fa-trash me-2"></i> {{ __('widgetmanagement::admin.delete') }}
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                @if($category->children && $category->children->count() > 0)
                                    @foreach($category->children as $child)
                                        <div class="category-item list-group-item p-2 ps-5" 
                                            wire:key="child-{{ $child->widget_category_id }}" 
                                            id="item-{{ $child->widget_category_id }}" 
                                            data-id="{{ $child->widget_category_id }}"
                                            data-parent-id="{{ $category->widget_category_id }}"
                                            data-is-parent="0">
                                            <div class="d-flex align-items-center">
                                                
                                                <div class="category-drag-handle me-2">
                                                    <i class="fas fa-grip-vertical text-muted"></i>
                                                </div>
                                                
                                                
                                                <div class="bg-secondary-lt rounded-2 d-flex align-items-center justify-content-center me-2" 
                                                    style="width: 2.5rem; height: 2.5rem;">
                                                    <i class="{{ $child->icon ?? 'fas fa-folder' }}"></i>
                                                </div>
                                                
                                                
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <div class="h4 mb-0">{{ $child->title }}</div>
                                                        </div>
                                                        
                                                        
                                                        <div class="d-flex align-items-center">
                                                            <div class="container">
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <button wire:click="toggleActive({{ $child->widget_category_id }})"
                                                                            class="btn btn-icon btn-sm {{ $child->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}"
                                                                            data-bs-toggle="tooltip" data-bs-placement="top" 
                                                                            title="{{ $child->is_active ? __('widgetmanagement::admin.deactivate') : __('widgetmanagement::admin.activate') }}">
                                                                            
                                                                            <div wire:loading wire:target="toggleActive({{ $child->widget_category_id }})"
                                                                                class="spinner-border spinner-border-sm">
                                                                            </div>
                                                                            
                                                                            <div wire:loading.remove wire:target="toggleActive({{ $child->widget_category_id }})">
                                                                                @if($child->is_active)
                                                                                <i class="fas fa-check fa-lg"></i>
                                                                                @else
                                                                                <i class="fas fa-times fa-lg"></i>
                                                                                @endif
                                                                            </div>
                                                                        </button>
                                                                    </div>
                                                                    <div class="col">
                                                                        <a href="javascript:void(0);" wire:click="startEdit({{ $child->widget_category_id }})" 
                                                                           data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('widgetmanagement::admin.edit') }}">
                                                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                                                        </a>
                                                                    </div>
                                                                    <div class="col lh-1">
                                                                        <div class="dropdown mt-1">
                                                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                                                aria-haspopup="true" aria-expanded="false">
                                                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                                                            </a>
                                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                                <a href="javascript:void(0);" wire:click="delete({{ $child->widget_category_id }})" 
                                                                                   class="dropdown-item link-danger">
                                                                                    <i class="fas fa-trash me-2"></i> {{ __('widgetmanagement::admin.delete') }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @empty
                                <div class="list-group-item py-4">
                                    <div class="empty">
                                        <div class="empty-img">
                                            <i class="fas fa-folder-open fa-4x text-muted"></i>
                                        </div>
                                        <p class="empty-title mt-2">{{ __('widgetmanagement::admin.category_not_found') }}</p>
                                        <p class="empty-subtitle text-muted">
                                            @if(!empty($search))
                                                {{ __('widgetmanagement::admin.no_category_match') }}
                                            @else
                                                {{ __('widgetmanagement::admin.no_category_yet') }}
                                            @endif
                                        </p>
                                        @if(!empty($search))
                                            <div class="empty-action">
                                                <button wire:click="$set('search', '')" class="btn btn-primary">
                                                    <i class="fas fa-times me-1"></i> {{ __('widgetmanagement::admin.clear_search') }}
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
<script src="{{ asset('admin-assets/libs/sortable/sortable-settings.js') }}"></script>
@endpush