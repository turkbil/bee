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
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, confirmDelete, moduleFilter, eventFilter, dateFrom, dateTo, clearFilters, clearUserLogs"
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
                        @if($search || $moduleFilter || $eventFilter || $dateFrom || $dateTo)
                        <span class="badge bg-primary ms-1">{{ t('usermanagement::general.filters_active') }}</span>
                        @endif
                    </button>
                    
                    <!-- Sayfa Adeti Seçimi -->
                    <div style="min-width: 70px">
                        <select wire:model.live="perPage" class="form-select">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtre Bölümü - Açılır Kapanır -->
        <div class="collapse mb-3" id="filterCollapse">
            <div class="card card-body">
                <div class="row g-3">
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
                    
                    <!-- Sayfa Başına Kayıt -->
                    <div class="col-md-4">
                        <label class="form-label">{{ t('usermanagement::general.per_page') }}</label>
                        <select wire:model.live="perPage" class="form-select">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
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
                        @if($search || $moduleFilter || $eventFilter || $dateFrom || $dateTo)
                        <button type="button" class="btn btn-outline-secondary" wire:click="clearFilters">
                            <i class="fas fa-times me-1"></i>{{ t('usermanagement::general.clear_filters') }}
                        </button>
                        @endif
                    </div>
                </div>
                
                <!-- Aktif Filtreler -->
                @if($search || $moduleFilter || $eventFilter || $dateFrom || $dateTo)
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @if($search)
                        <span class="badge bg-azure-lt">{{ t('usermanagement::general.search') }}: {{ $search }}</span>
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
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="card-title m-0">
                <i class="fas fa-history me-2"></i>{{ t('usermanagement::general.user_activity_logs', ['name' => $userName]) }}
            </h3>
            <div class="btn-list">
                <a href="{{ route('admin.usermanagement.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>{{ t('usermanagement::general.back_to_user_list') }}
                </a>
                
                @if($isRoot)
                <button type="button" class="btn btn-outline-danger" wire:click="clearUserLogs">
                    <i class="fas fa-trash-alt me-2"></i>{{ t('usermanagement::general.clear_all_logs') }}
                </button>
                @endif
            </div>
        </div>
        
        <!-- Aktivite Listesi -->
        <div class="divide-y">
            @forelse($logs as $log)
            <div class="p-3" wire:key="log-{{ $log->id }}">
                <div class="row">
                    <div class="col-auto">
                        <!-- Modül tipine göre avatar -->
                        @if($log->log_name == 'User')
                            <span class="avatar bg-blue-lt">
                                <i class="fas fa-user"></i>
                            </span>
                        @elseif($log->log_name == 'Page')
                            <span class="avatar bg-purple-lt">
                                <i class="fas fa-file-alt"></i>
                            </span>
                        @elseif($log->log_name == 'Portfolio')
                            <span class="avatar bg-green-lt">
                                <i class="fas fa-briefcase"></i>
                            </span>
                        @elseif($log->log_name == 'UserModulePermission')
                            <span class="avatar bg-orange-lt">
                                <i class="fas fa-key"></i>
                            </span>
                        @else
                            <span class="avatar bg-secondary-lt">
                                <i class="fas fa-cube"></i>
                            </span>
                        @endif
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center mb-1">
                            <span class="text-reset">{{ $log->description }}</span>
                        </div>
                        <div class="text-muted small"> 
                            <span class="badge bg-blue-lt me-2">{{ $log->log_name }}</span> 
                            {{ $log->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="col-auto align-self-center">
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
                                                        <div class="col-4">Modül</div>
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
                                                        <div class="col-4">Modül</div>
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
            </div>
            @empty
            <div class="empty py-5">
                <div class="empty-img">
                    <i class="fas fa-search fa-3x text-muted"></i>
                </div>
                <p class="empty-title">{{ t('usermanagement::general.no_records') }}</p>
                <p class="empty-subtitle text-muted">
                    {{ t('usermanagement::general.no_records_text') }}
                </p>
                <div class="empty-action">
                    <button class="btn btn-primary" wire:click="clearFilters">
                        <i class="fas fa-times me-2"></i>Filtreleri Temizle
                    </button>
                </div>
            </div>
            @endforelse
        </div>
    </div>
    

    
    <!-- Pagination -->
    {{ $logs->links() }}

    <livewire:modals.delete-modal />
    <livewire:usermanagement.confirm-action-modal />
</div>