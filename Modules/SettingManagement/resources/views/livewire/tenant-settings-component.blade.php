@include('settingmanagement::helper')

<div class="card">
    <div class="card-body">
        
        <div class="row mb-3">
            
            <div class="col">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="{{ t('settingmanagement::general.search_placeholder') }}">
                </div>
            </div>
            
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, selectedGroup"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">{{ t('settingmanagement::general.updating') }}</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-2">
                    <select wire:model.live="selectedGroup" class="form-select" style="min-width: 150px;">
                        <option value="">{{ t('settingmanagement::general.all_groups') }}</option>
                        @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">

        
        @forelse($settings->groupBy('group_id') as $groupId => $groupSettings)
        @php $group = $groups->firstWhere('id', $groupId); @endphp
        <div class="card mb-3">
            <div class="card-header d-flex">
                <div class="flex-grow-1">
                    <h3 class="card-title">{{ $group->name }}</h3>
                </div>
                <div>
                    <a href="{{ route('admin.settingmanagement.values', $groupId) }}" class="btn btn-sm btn-ghost-secondary">
                        {{ t('settingmanagement::general.bulk_edit') }} 
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead>
                        <tr>
                            <th style="width: 25%">{{ t('settingmanagement::general.table_header_title') }}</th>
                            <th style="width: 5%">{{ t('settingmanagement::general.table_header_custom') }}</th>
                            <th style="width: 35%">{{ t('settingmanagement::general.table_header_value') }}</th>
                            <th style="width: 20%">{{ t('settingmanagement::general.table_header_key') }}</th>
                            <th style="width: 10%">{{ t('settingmanagement::general.table_header_type') }}</th>
                            <th style="width: 5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupSettings as $setting)
                        <tr>
                            <td>{{ $setting->label }}</td>
                            <td>
                                @if($setting->is_custom)
                                <span class="badge bg-azure-lt">
                                    <i class="fas fa-check text-azure"></i>
                                </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;" title="{{ $setting->current_value }}">
                                @if($setting->type === 'file' && $setting->current_value)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file me-2" aria-label="{{ t('settingmanagement::general.file_icon_description') }}"></i>
                                        <a href="{{ cdn($setting->current_value) }}" target="_blank" class="text-truncate">
                                            {{ basename($setting->current_value) }}
                                        </a>
                                        
                                    </div>
                                @elseif($setting->type === 'image' && $setting->current_value)
                                    <div class="d-flex align-items-center">
                                        <img src="{{ cdn($setting->current_value) }}" class="img-thumbnail me-2" style="max-width: 40px; max-height: 40px;">
                                        
                                    </div>
                                @elseif($setting->type === 'checkbox')
                                    {{ $setting->current_value == '1' ? t('settingmanagement::general.yes') : t('settingmanagement::general.no') }}
                                @elseif($setting->type === 'textarea' || $setting->type === 'html')
                                    <span class="text-muted">{{ Str::limit(strip_tags($setting->current_value), 50) ?: '-' }}</span>
                                @else
                                    {{ $setting->current_value ?: '-' }}
                                @endif
                                </div>
                            </td>
                            <td><code>{{ $setting->key }}</code></td>
                            <td>
                                <span class="badge bg-blue-lt">{{ $setting->type }}</span>
                            </td>
                            <td>
                                
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @empty
        <div class="empty">
            <div class="empty-icon">
                <i class="fas fa-cogs"></i>
            </div>
            <p class="empty-title">{{ t('settingmanagement::general.empty_title') }}</p>
            <p class="empty-subtitle text-muted">
                {{ t('settingmanagement::general.empty_subtitle') }}
            </p>
        </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
    .table th {
        text-transform: uppercase;
        color: #6c7a91;
        font-size: 0.625rem;
        letter-spacing: 0.04em;
        font-weight: 600;
    }
    
    .card-header.d-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .flex-grow-1 {
        flex-grow: 1;
    }
</style>
@endpush