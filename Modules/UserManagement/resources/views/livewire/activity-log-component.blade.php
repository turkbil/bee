@include('usermanagement::helper')
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
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="{{ t('usermanagement::general.search_placeholder') }}">
                </div>
            </div>
            
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, confirmDelete, userFilter, moduleFilter, eventFilter, dateFrom, dateTo, clearFilters, clearLogs, clearUserLogs"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">{{ t('usermanagement::general.updating') }}</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            
            <!-- Sağ Taraf (Filtre ve Sayfa Seçimi) -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Filtre Butonu -->
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" 
                        data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                        <i class="fas fa-filter me-1"></i>
                        {{ t('usermanagement::general.filters') }}
                        @if($search || $userFilter || $moduleFilter || $eventFilter || $dateFrom || $dateTo)
                        <span class="badge bg-primary ms-1">{{ t('usermanagement::general.filters_active') }}</span>
                        @endif
                    </button>
                    
                    
                    <!-- Sayfa Adeti Seçimi -->
                    <div style="min-width: 70px">
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
        
        <!-- Filtre Bölümü - Açılır Kapanır -->
        <div class="collapse mb-3" id="filterCollapse">
            <div class="card card-body">
                <div class="row g-3">
                    <!-- Kullanıcı Filtresi -->
                    <div class="col-md-4">
                        <label class="form-label">{{ t('usermanagement::general.user') }}</label>
                        <select wire:model.live="userFilter" class="form-select">
                            <option value="">{{ t('usermanagement::general.all_users') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Modül Filtresi -->
                    <div class="col-md-4">
                        <label class="form-label">{{ t('usermanagement::general.module') }}</label>
                        <select wire:model.live="moduleFilter" class="form-select">
                            <option value="">{{ t('usermanagement::general.all_modules') }}</option>
                            @foreach($modules as $module)
                                <option value="{{ $module }}">{{ $module }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Eylem Filtresi -->
                    <div class="col-md-4">
                        <label class="form-label">{{ t('usermanagement::general.action') }}</label>
                        <select wire:model.live="eventFilter" class="form-select">
                            <option value="">{{ t('usermanagement::general.all_events') }}</option>
                            @foreach($events as $event)
                                <option value="{{ $event }}">{{ $event }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Tarih Aralığı -->
                    <div class="col-md-4">
                        <label class="form-label">{{ t('usermanagement::general.start_date') }}</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" wire:model.live="dateFrom" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">{{ t('usermanagement::general.end_date') }}</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" wire:model.live="dateTo" class="form-control">
                        </div>
                    </div>
                    
                    <!-- Filtreleri Temizle -->
                    <div class="col-md-4 d-flex align-items-end">
                        @if($search || $userFilter || $moduleFilter || $eventFilter || $dateFrom || $dateTo)
                        <button type="button" class="btn btn-outline-secondary" wire:click="clearFilters">
                            <i class="fas fa-times me-1"></i>{{ t('usermanagement::general.clear_filters') }}
                        </button>
                        @endif
                    </div>
                </div>
                
                <!-- Aktif Filtreler -->
                @if($search || $userFilter || $moduleFilter || $eventFilter || $dateFrom || $dateTo)
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @if($search)
                        <span class="badge bg-azure-lt">{{ t('usermanagement::general.search') }}: {{ $search }}</span>
                    @endif
                    @if($userFilter)
                        <span class="badge bg-blue-lt">{{ t('usermanagement::general.user') }}: {{ $users->firstWhere('id', $userFilter)->name ?? t('usermanagement::general.user') }}</span>
                    @endif
                    @if($moduleFilter)
                        <span class="badge bg-indigo-lt">{{ t('usermanagement::general.module') }}: {{ $moduleFilter }}</span>
                    @endif
                    @if($eventFilter)
                        <span class="badge bg-purple-lt">{{ t('usermanagement::general.action') }}: {{ $eventFilter }}</span>
                    @endif
                    @if($dateFrom)
                        <span class="badge bg-teal-lt">{{ t('usermanagement::general.start_date') }}: {{ $dateFrom }}</span>
                    @endif
                    @if($dateTo)
                        <span class="badge bg-cyan-lt">{{ t('usermanagement::general.end_date') }}: {{ $dateTo }}</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
        
        <!-- Tablo Bölümü -->
        <div id="table-default" class="table-responsive">
            <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                <thead>
                    <tr>
                        <th style="width: 50px">
                            <div class="d-flex align-items-center gap-2">
                                <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                                <button
                                    class="table-sort {{ $sortField === 'id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('id')">
                                </button>
                            </div>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'log_name' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('log_name')">
                                {{ t('usermanagement::general.module') }}
                            </button>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'description' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('description')">
                                {{ t('usermanagement::general.description') }}
                            </button>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'event' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('event')">
                                {{ t('usermanagement::general.action') }}
                            </button>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'causer_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('causer_id')">
                                {{ t('usermanagement::general.user') }}
                            </button>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'created_at' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('created_at')">
                                {{ t('usermanagement::general.date') }}
                            </button>
                        </th>
                        <th class="text-center" style="width: 120px">{{ t('usermanagement::general.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="table-tbody">
                    @forelse($logs as $log)
                    <tr class="hover-trigger" wire:key="row-{{ $log->id }}">
                        <td class="sort-id small">
                            <div class="hover-toggle">
                                <span class="hover-hide">{{ $log->id }}</span>
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $log->id }}" 
                                    class="form-check-input hover-show" @if(in_array($log->id, $selectedItems)) checked @endif>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-blue-lt">{{ $log->log_name }}</span>
                        </td>
                        <td class="text-wrap">
                            <div>{{ $log->description }}</div>
                            @if(isset($log->properties['baslik']) && $log->properties['baslik'] && $log->properties['baslik'] != $log->description)
                            @endif
                        </td>
                        <td><span class="badge bg-blue-lt text-muted small">{{ $log->event }}</span></td>
                        <td>
                            @if($log->causer)
                                @if($isRoot || !$log->causer->isRoot())
                                    <a href="{{ route('admin.usermanagement.user.activity.logs', $log->causer->id) }}" class="text-reset">
                                        {{ $log->causer->name }}
                                    </a>
                                    @if($log->causer->email)
                                        <div class="text-muted small">{{ $log->causer->email }}</div>
                                    @endif
                                @else
                                    <div>{{ $log->causer->name }}</div>
                                    @if($log->causer->email)
                                        <div class="text-muted small">{{ $log->causer->email }}</div>
                                    @endif
                                @endif
                            @else
                                <span class="text-secondary">{{ t('usermanagement::general.system') }}</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $log->created_at->format('d.m.Y') }}</div>
                            <div class="text-muted small">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="text-center align-middle">
                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#details-modal-{{ $log->id }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ t('usermanagement::general.details') }}">
                                            <i class="fa-solid fa-eye link-secondary fa-lg"></i>
                                        </a>
                                    </div>
                                    
                                    @if($isRoot)
                                    <div class="col lh-1">
                                        <div class="dropdown mt-1">
                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);" wire:click="confirmDelete({{ $log->id }})" 
                                                    class="dropdown-item link-danger">
                                                    {{ t('usermanagement::general.delete') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Detay Modal -->
                            <div class="modal modal-blur fade" id="details-modal-{{ $log->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-info-circle text-info me-2"></i>
                                                {{ t('usermanagement::general.operation_detail', ['id' => $log->id]) }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <!-- Genel Bilgiler -->
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-status-top bg-primary"></div>
                                                        <div class="card-header">
                                                            <h3 class="card-title">{{ t('usermanagement::general.general_info') }}</h3>
                                                        </div>
                                                        <div class="list-group list-group-flush">
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">{{ t('usermanagement::general.module') }}</div>
                                                                    <div class="col-8 text-muted">{{ $log->log_name }}</div>
                                                                </div>
                                                            </div>
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">{{ t('usermanagement::general.description') }}</div>
                                                                    <div class="col-8 text-muted">{{ $log->description }}</div>
                                                                </div>
                                                            </div>
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">{{ t('usermanagement::general.action') }}</div>
                                                                    <div class="col-8 text-muted">{{ $log->event }}</div>
                                                                </div>
                                                            </div>
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">{{ t('usermanagement::general.user') }}</div>
                                                                    <div class="col-8">
                                                                        @if($log->causer)
                                                                            @if($isRoot || !$log->causer->isRoot())
                                                                                <a href="{{ route('admin.usermanagement.user.activity.logs', $log->causer->id) }}">
                                                                                    {{ $log->causer->name }}
                                                                                </a>
                                                                            @else
                                                                                {{ $log->causer->name }}
                                                                            @endif
                                                                        @else
                                                                            <span class="text-muted">{{ t('usermanagement::general.system') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">{{ t('usermanagement::general.date') }}</div>
                                                                    <div class="col-8 text-muted">{{ $log->created_at->format('d.m.Y H:i:s') }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Nesne Bilgileri -->
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-status-top bg-indigo"></div>
                                                        <div class="card-header">
                                                            <h3 class="card-title">{{ t('usermanagement::general.object_info') }}</h3>
                                                        </div>
                                                        <div class="list-group list-group-flush">
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">{{ t('usermanagement::general.type') }}</div>
                                                                    <div class="col-8 text-muted">
                                                                        {{ $log->subject_type ? class_basename($log->subject_type) : '-' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">ID</div>
                                                                    <div class="col-8 text-muted">
                                                                        {{ $log->subject_id ?? '-' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">{{ t('usermanagement::general.title') }}</div>
                                                                    <div class="col-8 text-muted">
                                                                        {{ $log->properties['baslik'] ?? '-' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">{{ t('usermanagement::general.module') }}</div>
                                                                    <div class="col-8 text-muted">
                                                                        {{ $log->properties['modul'] ?? $log->log_name }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Değişen Alanlar -->
                                                @if(isset($log->properties['degisenler']) && !empty($log->properties['degisenler']))
                                                <div class="col-12 mt-3">
                                                    <div class="card">
                                                        <div class="card-status-top bg-purple"></div>
                                                        <div class="card-header">
                                                            <h3 class="card-title">{{ t('usermanagement::general.changed_fields') }}</h3>
                                                        </div>
                                                        <div class="table-responsive">
                                                            @if(!is_array($log->properties['degisenler']))
                                                            <table class="table card-table table-vcenter">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="fw-medium">{{ t('usermanagement::general.change') }}</td>
                                                                        <td>{{ $log->properties['degisenler'] }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            @else
                                                            <table class="table card-table table-vcenter">
                                                                <thead>
                                                                    <tr>
                                                                        <th>{{ t('usermanagement::general.field') }}</th>
                                                                        <th>{{ t('usermanagement::general.old_value') }}</th>
                                                                        <th>{{ t('usermanagement::general.new_value') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($log->properties['degisenler'] as $key => $value)
                                                                    <tr>
                                                                        <td class="fw-medium">{{ ucfirst($key) }}</td>
                                                                        <td>
                                                                            @if(is_array($value) && isset($value['old']))
                                                                                @if(is_array($value['old']))
                                                                                    @foreach($value['old'] as $oldKey => $oldValue)
                                                                                        <div>{{ ucfirst($oldKey) }}: {{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}</div>
                                                                                    @endforeach
                                                                                @else
                                                                                    {{ $value['old'] }}
                                                                                @endif
                                                                            @else
                                                                                <span class="text-muted">-</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if(is_array($value) && isset($value['new']))
                                                                                @if(is_array($value['new']))
                                                                                    @foreach($value['new'] as $newKey => $newValue)
                                                                                        <div>{{ ucfirst($newKey) }}: {{ is_array($newValue) ? json_encode($newValue) : $newValue }}</div>
                                                                                    @endforeach
                                                                                @else
                                                                                    {{ $value['new'] }}
                                                                                @endif
                                                                            @elseif(!is_array($value))
                                                                                {{ $value }}
                                                                            @else
                                                                                <span class="text-muted">-</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                <!-- JSON Verisi Özet -->
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-status-top bg-yellow"></div>
                                                        <div class="card-header">
                                                            <h3 class="card-title">{{ t('usermanagement::general.json_data_summary') }}</h3>
                                                        </div>
                                                        <div class="table-responsive">
                                                            <table class="table card-table table-vcenter">
                                                                <tbody>
                                                                    @foreach($log->properties as $key => $value)
                                                                        @if($key != 'degisenler')
                                                                        <tr>
                                                                            <td class="fw-medium" style="width:150px">{{ ucfirst($key) }}</td>
                                                                            <td>
                                                                                @if(is_array($value))
                                                                                    @if(count($value) == 0)
                                                                                        <span class="text-muted">-</span>
                                                                                    @else
                                                                                        @foreach($value as $subKey => $subValue)
                                                                                            <div>{{ ucfirst($subKey) }}: {{ is_array($subValue) ? json_encode($subValue) : $subValue }}</div>
                                                                                        @endforeach
                                                                                    @endif
                                                                                @else
                                                                                    {{ $value }}
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                        @endif
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn" data-bs-dismiss="modal">{{ t('usermanagement::general.close') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="empty">
                                <p class="empty-title">{{ t('usermanagement::general.no_records') }}</p>
                                <p class="empty-subtitle text-muted">
                                    {{ t('usermanagement::general.no_records_text') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    {{ $logs->links() }}
    
    <!-- Bulk Actions -->
    @if($bulkActionsEnabled)
    <div class="position-fixed bottom-0 start-50 translate-middle-x mb-4" style="z-index: 1000;">
        <div class="card shadow-lg border-0 rounded-lg " style="backdrop-filter: blur(12px); background: var(--tblr-bg-surface);"><span class="badge bg-red badge-notification badge-blink"></span>
            <div class="card-body p-3">
                <div class="d-flex flex-wrap gap-3 align-items-center justify-content-center">
                    <span class="text-muted small">{{ t('usermanagement::general.selected_items', ['count' => count($selectedItems)]) }}</span>
                    @if($isRoot)
                    <button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 hover-btn" wire:click="confirmBulkDelete">
                        <i class="fas fa-trash me-2"></i>
                        <span>{{ t('usermanagement::general.delete') }}</span>
                    </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 hover-btn" wire:click="refreshSelectedItems">
                        <i class="fas fa-times me-2"></i>
                        <span>{{ t('usermanagement::general.cancel_selection') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <livewire:modals.bulk-delete-modal />
    <livewire:modals.delete-modal />
    <livewire:usermanagement.confirm-action-modal />
</div>