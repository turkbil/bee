@include('settingmanagement::helper')
<div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="input-icon flex-grow-1">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="{{ __('settingmanagement::admin.search_group_placeholder') }}">
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row row-cards">
                @forelse($groups as $group)
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card bg-muted-lt">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-primary-lt me-2">
                                        <i class="{{ $group->icon ?? 'fas fa-folder' }} {{ !$group->is_active ? 'text-danger' : '' }}"></i>
                                    </div>
                                    <div>
                                        <h3 class="card-title mb-0 d-flex align-items-center">
                                            {{ $group->name }}
                                            @if(!$group->is_active)
                                            <span class="badge bg-danger text-white ms-2">{{ __('settingmanagement::admin.passive_badge') }}</span>
                                            @endif
                                        </h3>
                                        @if($group->description)
                                        <small class="text-muted">{{ Str::limit($group->description, 50) }}</small>
                                        @endif
                                    </div>
                                    <div class="ms-auto">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-icon" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @if(auth()->user()->hasRole('root'))
                                                <a href="{{ route('admin.settingmanagement.group.manage', $group->id) }}"
                                                    class="dropdown-item">
                                                    <i class="fas fa-edit me-2"></i> {{ __('settingmanagement::admin.edit_action') }}
                                                </a>
                                                @endif
                                                @if(auth()->user()->hasRole('root'))
                                                <a href="{{ route('admin.settingmanagement.group.manage', ['parent_id' => $group->id]) }}"
                                                    class="dropdown-item">
                                                    <i class="fas fa-plus me-2"></i> {{ __('settingmanagement::admin.add_subgroup_action') }}
                                                </a>
                                                @endif
                                                @if(auth()->user()->hasRole('root'))
                                                <button wire:click="toggleActive({{ $group->id }})"
                                                    class="dropdown-item">
                                                    <i class="fas fa-{{ $group->is_active ? 'ban' : 'check' }} me-2"></i>
                                                    {{ $group->is_active ? __('settingmanagement::admin.deactivate_action') : __('settingmanagement::admin.activate_action') }}
                                                </button>
                                                @endif
                                                @if($group->children->isEmpty() && auth()->user()->hasRole('root'))
                                                <button wire:click="delete({{ $group->id }})"
                                                    wire:confirm="{{ __('settingmanagement::admin.delete_subgroup_confirm') }}"
                                                    class="dropdown-item text-danger">
                                                    <i class="fas fa-trash me-2"></i> {{ __('settingmanagement::admin.delete_action') }}
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($group->children->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach($group->children as $child)
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar avatar-sm bg-primary-lt">
                                            <i class="{{ $child->icon ?? 'fas fa-circle' }} {{ !$child->is_active ? 'text-danger' : '' }}"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-fill">
                                                <div class="font-weight-medium d-flex align-items-center"> 
                                                    <a href="{{ route('admin.settingmanagement.values', $child->id) }}"
                                                        class="text-reset">
                                                    {{ $child->name }}
                                                    </a>
                                                    @if(!$child->is_active)
                                                    <span class="badge bg-danger text-white ms-2">{{ __('settingmanagement::admin.passive_badge') }}</span>
                                                    @endif
                                                </div>
                                                @if($child->description)
                                                <div class="text-muted small">{{ Str::limit($child->description, 40) }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center justify-content-end">
                                                <span class="badge bg-primary text-white text-center align-middle d-flex align-items-center justify-content-center" style="min-width: 2.5rem; padding: 0.35rem 0.5rem;">
                                                    {{ $child->settings->count() }}
                                                </span>
                                                <div class="dropdown ms-2">
                                                    <a href="#" class="btn btn-icon" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="{{ route('admin.settingmanagement.values', $child->id) }}" class="dropdown-item">
                                                            <i class="fas fa-edit me-2"></i> {{ __('settingmanagement::admin.configure_settings') }}
                                                        </a>
                                                        @if(auth()->user()->hasRole('root'))
                                                        <a href="{{ route('admin.settingmanagement.group.manage', $child->id) }}" class="dropdown-item">
                                                            <i class="fas fa-edit me-2"></i> {{ __('settingmanagement::admin.edit_action') }}
                                                        </a>
                                                        <a href="{{ route('admin.settingmanagement.form-builder.edit', $child->id) }}" class="dropdown-item">
                                                            <i class="fas fa-magic me-2"></i> {{ __('settingmanagement::admin.form_builder_action') }}
                                                        </a>
                                                        @endif
                                                        @if(auth()->user()->hasRole('root'))
                                                        <button wire:click="toggleActive({{ $child->id }})" class="dropdown-item">
                                                            <i class="fas fa-{{ $child->is_active ? 'ban' : 'check' }} me-2"></i>
                                                            {{ $child->is_active ? __('settingmanagement::admin.deactivate_action') : __('settingmanagement::admin.activate_action') }}
                                                        </button>
                                                        @endif
                                                        @if($child->children->isEmpty() && auth()->user()->hasRole('root'))
                                                        <button wire:click="delete({{ $child->id }})" wire:confirm="{{ __('settingmanagement::admin.delete_subgroup_confirm') }}" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash me-2"></i> {{ __('settingmanagement::admin.delete_action') }}
                                                        </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="card-footer">
                            <div class="d-flex align-items-center">
                                <div>
                                    <div class="text-muted">{{ $group->children->count() }} {{ __('settingmanagement::admin.subgroup_count') }}</div>
                                </div>
                                <div class="ms-auto">
                                    @if(auth()->user()->hasRole('root'))
                                    <a href="{{ route('admin.settingmanagement.group.manage', ['parent_id' => $group->id]) }}"
                                        class="btn btn-link btn-sm">
                                        {{ __('settingmanagement::admin.add_subgroup_button') }}
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="empty">
                        <div class="empty-icon">
                            <i class="fas fa-layer-group fa-3x text-muted"></i>
                        </div>
                        <p class="empty-title">{{ __('settingmanagement::admin.empty_group_title') }}</p>
                        <p class="empty-subtitle text-muted">
                            {{ __('settingmanagement::admin.empty_group_subtitle') }}
                        </p>
                        @if(auth()->user()->hasRole('root'))
                        <div class="empty-action">
                            <a href="{{ route('admin.settingmanagement.group.manage') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                {{ __('settingmanagement::admin.add_new_group_button') }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>