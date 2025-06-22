@include('widgetmanagement::helper')
<div>
    <div class="row g-4">
        <div class="col-md-3">
            <form class="sticky-top" style="top: 20px;">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                    placeholder="{{ t('widgetmanagement::general.search_component') }}">
                            </div>
                        </div>
                        
                        
                        <div class="mb-4">
                            <div class="form-label">{{ t('widgetmanagement::general.component_type') }}</div>
                            <select wire:model.live="typeFilter" class="form-select">
                                <option value="">{{ t('widgetmanagement::general.all_types') }}</option>
                                @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        
                        <div class="mb-4">
                            <div class="list-group list-group-transparent mb-3">
                                <a class="list-group-item list-group-item-action py-2 d-flex align-items-center {{ $parentCategoryFilter == '' ? 'active' : '' }}" 
                                wire:click.prevent="$set('parentCategoryFilter', '')" href="#">
                                 {{ t('widgetmanagement::general.all_categories') }}
                                 <small class="text-secondary ms-auto">{{ $entities->total() }}</small>
                                </a>
                                
                                @foreach($parentCategories as $category)
                                <a class="list-group-item list-group-item-action py-2 d-flex align-items-center {{ $parentCategoryFilter == $category->widget_category_id ? 'active' : '' }}" 
                                    wire:click.prevent="$set('parentCategoryFilter', '{{ $category->widget_category_id }}')" href="#">
                                    {{ $category->title }}
                                    <small class="text-secondary ms-auto">{{ $category->total_widgets_count }}</small>
                                </a>
                                                             
                                @if($category->children_count > 0)
                                    @foreach($category->children as $childCategory)
                                    <a class="list-group-item list-group-item-action py-2 d-flex align-items-center ps-5 {{ $categoryFilter == $childCategory->widget_category_id ? 'active' : '' }}" 
                                       wire:click.prevent="$set('categoryFilter', '{{ $childCategory->widget_category_id }}')" href="#">
                                        <i class="fas fa-angle-right me-2"></i> {{ $childCategory->title }}
                                        <small class="text-secondary ms-auto">{{ $childCategory->widgets_count }}</small>
                                    </a>
                                    @endforeach
                                @endif
                                @endforeach
                            </div>
                        </div>
                        
                        
                        @if($childCategories->count() > 0 && $parentCategoryFilter && !$categoryFilter)
                        <div class="form-label">{{ t('widgetmanagement::general.subcategories') }}</div>
                        <div class="mb-4">
                            <div class="list-group list-group-transparent mb-3">
                                <a class="list-group-item list-group-item-action py-2 d-flex align-items-center {{ $categoryFilter == '' ? 'active' : '' }}" 
                                   wire:click.prevent="$set('categoryFilter', '')" href="#">
                                    {{ t('widgetmanagement.misc.show_all') }}
                                </a>
                                
                                @foreach($childCategories as $childCategory)
                                <a class="list-group-item list-group-item-action py-2 d-flex align-items-center {{ $categoryFilter == $childCategory->widget_category_id ? 'active' : '' }}" 
                                   wire:click.prevent="$set('categoryFilter', '{{ $childCategory->widget_category_id }}')" href="#">
                                    {{ $childCategory->title }}
                                    <small class="text-secondary ms-auto">{{ $childCategory->widgets_count }}</small>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        
                        <div class="form-label">{{ t('widgetmanagement::general.per_page_items') }}</div>
                        <div class="mb-4">
                            <select wire:model.live="perPage" class="form-select">
                                <option value="100">{{ t('widgetmanagement::general.components_count', ['count' => 100]) }}</option>
                                <option value="200">{{ t('widgetmanagement::general.components_count', ['count' => 200]) }}</option>
                                <option value="300">{{ t('widgetmanagement::general.components_count', ['count' => 300]) }}</option>
                                <option value="500">{{ t('widgetmanagement::general.components_count', ['count' => 500]) }}</option>
                                <option value="1000">{{ t('widgetmanagement::general.components_count', ['count' => 1000]) }}</option>
                            </select>
                        </div>
                        
                    </div>
                </div>
            </form>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <h3 class="card-title">{{ t('widgetmanagement::general.active_components') }}</h3>
                            <p class="text-muted">{{ t('widgetmanagement::general.manage_active_components') }}</p>
                        </div>
                        
                        
                        <div class="position-relative d-flex justify-content-center align-items-center">
                            <div wire:loading
                                wire:target="render, search, perPage, gotoPage, previousPage, nextPage, createInstance, deleteInstance, toggleActive, categoryFilter, parentCategoryFilter"
                                class="text-center">
                                <div class="small text-muted me-2">{{ t('widgetmanagement::general.updating') }}</div>
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                    
                    
                    @php
                        $groupedEntities = $entities->groupBy(function($item) use ($widgets) {
                            $widget = $widgets->get($item->widget_id);
                            return $widget && $widget->category ? $widget->category->title : t('widgetmanagement::general.no_category_assigned');
                        });
                    @endphp
                    
                    @forelse($groupedEntities as $categoryName => $categoryEntities)
                    <div class="mb-4">
                        <h4 class="border-bottom pb-2 mb-3">{{ $categoryName }}</h4>
                        <div class="row row-cards">
                            @foreach($categoryEntities as $instance)
                            @php
                                $widget = $widgets->get($instance->widget_id);
                            @endphp
                            <div class="col-12 col-sm-6 col-lg-6">
                                <div class="card h-100">
                                    <div class="card-status-top {{ $widget && $widget->is_active ? 'bg-primary' : 'bg-danger' }}"></div>
                                    
                                    
                                    <div class="card-header d-flex align-items-center">
                                        <div class="me-auto">
                                            <h3 class="card-title mb-0">
                                                <a href="{{ $widget && $widget->has_items ? route('admin.widgetmanagement.items', $instance->id) : route('admin.widgetmanagement.settings', $instance->id) }}">
                                                    {{ $instance->display_title }}
                                                </a>
                                            </h3>
                                            @if($widget && $widget->category)
                                            <div class="text-muted small">
                                                @if($widget->category->parent)
                                                <span>{{ $widget->category->parent->title }} / {{ $widget->category->title }}</span>
                                                @else
                                                <span>{{ $widget->category->title }}</span>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ route('admin.widgetmanagement.settings', $instance->id) }}" class="dropdown-item">
                                                    <i class="fas fa-sliders-h me-2"></i> {{ $widget && $widget->has_items ? t('widgetmanagement.fields.settings') : t('widgetmanagement.misc.customize') }}
                                                </a>

                                                @if($widget && $widget->has_items)
                                                    @if($widget->type === 'static')
                                                        @php
                                                            $staticItem = $instance->items->first();
                                                            $itemId = $staticItem ? $staticItem->id : 0;
                                                        @endphp
                                                        <a href="{{ route('admin.widgetmanagement.item.manage', [$instance->id, $itemId]) }}" class="dropdown-item">
                                                            <i class="fas fa-layer-group me-2"></i> {{ t('widgetmanagement.fields.content') }}
                                                        </a>
                                                    @else
                                                        <a href="{{ route('admin.widgetmanagement.items', $instance->id) }}" class="dropdown-item">
                                                            <i class="fas fa-layer-group me-2"></i> {{ t('widgetmanagement.fields.content') }}
                                                        </a>
                                                    @endif
                                                @endif
                                                
                                                <a href="{{ route('admin.widgetmanagement.preview.instance', $instance->id) }}" class="dropdown-item" target="_blank">
                                                    <i class="fas fa-eye me-2"></i> {{ t('widgetmanagement.fields.preview') }}
                                                </a>
                                                
                                                <div class="dropdown-divider"></div>
                                                @if($hasRootPermission && $widget)
                                                <a href="{{ route('admin.widgetmanagement.manage', $widget->id) }}" class="dropdown-item">
                                                    <i class="fas fa-tools me-2"></i> {{ t('widgetmanagement::general.configure') }}
                                                </a>
                                                @endif
                                                <button class="dropdown-item text-danger" wire:click="deleteInstance({{ $instance->id }})" 
                                                onclick="return confirm('{{ t('widgetmanagement::general.confirm_delete') }}')">
                                                    <i class="fas fa-trash me-2"></i> {{ t('widgetmanagement::general.delete') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div class="card-footer">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex gap-2">

                                            @if($widget && $widget->has_items)
                                                @if($widget->type === 'static')
                                                    @php
                                                        $staticItem = $instance->items->first();
                                                        $itemId = $staticItem ? $staticItem->id : 0;
                                                    @endphp
                                                    <a href="{{ route('admin.widgetmanagement.item.manage', [$instance->id, $itemId]) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-info-circle me-2 text-muted"></i>
                                                        {{ t('widgetmanagement::general.how_to_use') }}
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.widgetmanagement.items', $instance->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-info-circle me-2 text-muted"></i>
                                                        {{ t('widgetmanagement::general.how_to_use') }}
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('admin.widgetmanagement.settings', $instance->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-rocket me-2"></i>
                                                    {{ t('widgetmanagement::general.quick_start') }}
                                                </a>
                                            @endif
                                            
                                            <a href="{{ route('admin.widgetmanagement.preview.instance', $instance->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="fas fa-eye me-1"></i>
                                                {{ t('widgetmanagement::general.preview') }}
                                            </a>

                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                                    <input type="checkbox" wire:click="toggleActive({{ $instance->id }})"
                                                        {{ $instance->is_active ? 'checked' : '' }} value="1" />
                                                    <div class="state p-success p-on ms-2">
                                                        <label>{{ t('widgetmanagement::general.active') }}</label>
                                                    </div>
                                                    <div class="state p-danger p-off ms-2">
                                                        <label>{{ t('widgetmanagement::general.inactive') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="empty">
                        <div class="empty-img">
                            <img src="{{ asset('images/empty.svg') }}" height="128" alt="">
                        </div>
                        <p class="empty-title">{{ t('widgetmanagement::general.no_components_found') }}</p>
                        <p class="empty-subtitle text-muted">
                            {{ t('widgetmanagement::general.no_components_description') }}
                        </p>
                        <div class="empty-action">
                            <a href="{{ route('admin.widgetmanagement.gallery') }}" class="btn btn-primary">
                                <i class="fas fa-th-large me-2"></i> {{ t('widgetmanagement::general.go_to_component_gallery') }}
                            </a>
                        </div>
                    </div>
                    @endforelse
                </div>
                
                
                @if($entities->hasPages())
                <div class="card-footer d-flex align-items-center justify-content-end">
                    {{ $entities->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>