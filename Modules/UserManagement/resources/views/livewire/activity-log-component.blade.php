@include('usermanagement::helper')
<div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-history text-primary me-2"></i>İşlem Kayıtları
                </h3>
                <div class="btn-list">
                    @if($isRoot)
                    <button type="button" class="btn btn-outline-danger" wire:click="clearLogs">
                        <i class="fas fa-trash-alt me-2"></i>Tüm Kayıtları Temizle
                    </button>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card-body border-bottom pb-3">
            <!-- Filtre Araçları -->
            <div class="row mb-3">
                <!-- Arama -->
                <div class="col-md-3">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                            placeholder="Aramak için yazmaya başlayın...">
                    </div>
                </div>
                
                <!-- Kullanıcı Filtresi -->
                <div class="col-md-2">
                    <select wire:model.live="userFilter" class="form-select">
                        <option value="">Tüm Kullanıcılar</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Modül Filtresi -->
                <div class="col-md-2">
                    <select wire:model.live="moduleFilter" class="form-select">
                        <option value="">Tüm Modüller</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}">{{ $module }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Eylem Filtresi -->
                <div class="col-md-2">
                    <select wire:model.live="eventFilter" class="form-select">
                        <option value="">Tüm Eylemler</option>
                        @foreach($events as $event)
                            <option value="{{ $event }}">{{ $event }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Loading ve Perpage-->
                <div class="col-md-3">
                    <div class="d-flex justify-content-end align-items-center gap-3">
                        <!-- Loading -->
                        <div class="position-relative" style="width: 24px; height: 24px;">
                            <div wire:loading wire:target="search, userFilter, moduleFilter, eventFilter, dateFrom, dateTo, perPage, nextPage, previousPage, gotoPage, sortBy" class="position-absolute" style="top: 0; left: 0">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            </div>
                        </div>
                        
                        <!-- Per Page -->
                        <select wire:model.live="perPage" class="form-select form-select-sm" style="width: 70px;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Tarih Aralığı ve Filtre Yönetimi -->
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2">
                        <div class="input-icon flex-grow-1">
                            <span class="input-icon-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" wire:model.live="dateFrom" class="form-control" placeholder="Başlangıç">
                        </div>
                        <div class="input-icon flex-grow-1">
                            <span class="input-icon-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" wire:model.live="dateTo" class="form-control" placeholder="Bitiş">
                        </div>
                    </div>
                </div>
                
                <!-- Filtre Rozet ve Temizleme -->
                <div class="col-md-6">
                    <div class="d-flex justify-content-end align-items-center">
                        @if($search || $userFilter || $moduleFilter || $eventFilter || $dateFrom || $dateTo)
                        <div class="d-flex align-items-center me-3">
                            <span class="badge bg-azure-lt me-2">Filtre:</span>
                            @if($search) <span class="badge bg-blue-lt me-1">Ara: {{ $search }}</span> @endif
                            @if($userFilter) <span class="badge bg-green-lt me-1">Kullanıcı: {{ $users->firstWhere('id', $userFilter)->name ?? 'Bilinmeyen' }}</span> @endif
                            @if($moduleFilter) <span class="badge bg-purple-lt me-1">Modül: {{ $moduleFilter }}</span> @endif
                            @if($eventFilter) <span class="badge bg-yellow-lt me-1">Eylem: {{ $eventFilter }}</span> @endif
                            @if($dateFrom) <span class="badge bg-teal-lt me-1">Başlangıç: {{ $dateFrom }}</span> @endif
                            @if($dateTo) <span class="badge bg-cyan-lt me-1">Bitiş: {{ $dateTo }}</span> @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="clearFilters">
                            <i class="fas fa-times me-1"></i>Temizle
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tablo -->
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="w-1">
                            <button class="table-sort {{ $sortField === 'id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" 
                                wire:click="sortBy('id')">
                                ID
                            </button>
                        </th>
                        <th>
                            <button class="table-sort {{ $sortField === 'log_name' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" 
                                wire:click="sortBy('log_name')">
                                Modül
                            </button>
                        </th>
                        <th>
                            <button class="table-sort {{ $sortField === 'description' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" 
                                wire:click="sortBy('description')">
                                Açıklama
                            </button>
                        </th>
                        <th>
                            <button class="table-sort {{ $sortField === 'event' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" 
                                wire:click="sortBy('event')">
                                Eylem
                            </button>
                        </th>
                        <th>
                            <button class="table-sort {{ $sortField === 'causer_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" 
                                wire:click="sortBy('causer_id')">
                                Kullanıcı
                            </button>
                        </th>
                        <th>
                            <button class="table-sort {{ $sortField === 'created_at' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" 
                                wire:click="sortBy('created_at')">
                                Tarih
                            </button>
                        </th>
                        <th class="w-1">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="hover-trigger" wire:key="log-{{ $log->id }}">
                        <td><span class="text-muted">{{ $log->id }}</span></td>
                        <td>
                            <span class="badge bg-{{ 
                                $log->log_name == 'User' ? 'blue' : 
                                ($log->log_name == 'Page' ? 'indigo' : 
                                ($log->log_name == 'Portfolio' ? 'green' : 
                                ($log->log_name == 'UserModulePermission' ? 'orange' : 'secondary'))) 
                            }}-lt">
                                {{ $log->log_name }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span>{{ $log->description }}</span>
                                @if(isset($log->properties['baslik']) && $log->properties['baslik'])
                                <span class="text-muted small">{{ $log->properties['baslik'] }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($log->event == 'created')
                                <span class="badge bg-green-lt">oluşturuldu</span>
                            @elseif($log->event == 'updated')
                                <span class="badge bg-blue-lt">güncellendi</span>
                            @elseif($log->event == 'deleted')
                                <span class="badge bg-red-lt">silindi</span>
                            @else
                                <span class="badge bg-purple-lt">{{ $log->event }}</span>
                            @endif
                        </td>
                        <td>
                            @if($log->causer)
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs me-2 bg-blue-lt">
                                    {{ strtoupper(substr($log->causer->name ?? 'U', 0, 1)) }}
                                </span>
                                @if($isRoot || !$log->causer->isRoot())
                                <a href="{{ route('admin.usermanagement.user.activity.logs', $log->causer->id) }}" class="text-reset">
                                    {{ $log->causer->name }}
                                </a>
                                @else
                                <span>{{ $log->causer->name }}</span>
                                @endif
                            </div>
                            @else
                            <span class="text-muted">Sistem</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span>{{ $log->created_at->format('d.m.Y') }}</span>
                                <span class="text-muted small">{{ $log->created_at->format('H:i:s') }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex">
                                <button type="button" class="btn btn-icon btn-sm" data-bs-toggle="modal" data-bs-target="#details-modal-{{ $log->id }}" title="Detayları Görüntüle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                @if($isRoot)
                                <button type="button" class="btn btn-icon btn-sm text-danger" wire:click="$dispatch('showDeleteModal', {
                                    module: 'activitylog',
                                    id: {{ $log->id }},
                                    title: '{{ $log->id }} numaralı kayıt'
                                })" title="Kaydı Sil">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                            
                            <!-- Detay Modal -->
                            <div class="modal modal-blur fade" id="details-modal-{{ $log->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">İşlem Detayları #{{ $log->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h4 class="card-title">Genel Bilgiler</h4>
                                                        </div>
                                                        <div class="table-responsive">
                                                            <table class="table card-table table-vcenter">
                                                                <tr>
                                                                    <td class="fw-bold">ID</td>
                                                                    <td>{{ $log->id }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="fw-bold">Modül</td>
                                                                    <td>{{ $log->log_name }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="fw-bold">Açıklama</td>
                                                                    <td>{{ $log->description }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="fw-bold">Eylem</td>
                                                                    <td>
                                                                        @if($log->event == 'created')
                                                                            <span class="badge bg-green-lt">oluşturuldu</span>
                                                                        @elseif($log->event == 'updated')
                                                                            <span class="badge bg-blue-lt">güncellendi</span>
                                                                        @elseif($log->event == 'deleted')
                                                                            <span class="badge bg-red-lt">silindi</span>
                                                                        @else
                                                                            <span class="badge bg-purple-lt">{{ $log->event }}</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="fw-bold">Tarih</td>
                                                                    <td>{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="fw-bold">Kullanıcı</td>
                                                                    <td>{{ $log->causer->name ?? 'Sistem' }}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h4 class="card-title">Nesne Bilgileri</h4>
                                                        </div>
                                                        <div class="table-responsive">
                                                            <table class="table card-table table-vcenter">
                                                                <tr>
                                                                    <td class="fw-bold">Tür</td>
                                                                    <td>{{ $log->subject_type ? class_basename($log->subject_type) : 'N/A' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="fw-bold">ID</td>
                                                                    <td>{{ $log->subject_id ?? 'N/A' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="fw-bold">Başlık</td>
                                                                    <td>{{ $log->properties['baslik'] ?? 'N/A' }}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if(isset($log->properties['degisenler']) && !empty($log->properties['degisenler']))
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Değişen Alanlar</h4>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table card-table table-vcenter">
                                                        <thead>
                                                            <tr>
                                                                <th>Alan</th>
                                                                <th>Eski Değer</th>
                                                                <th>Yeni Değer</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($log->properties['degisenler'] as $key => $value)
                                                            <tr>
                                                                <td class="fw-bold">{{ $key }}</td>
                                                                <td>
                                                                    @if(is_array($value))
                                                                        @if(isset($value['old']))
                                                                            @if(is_array($value['old']))
                                                                                <div class="text-dark bg-light p-2 rounded">
                                                                                    {{ json_encode($value['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                                                                </div>
                                                                            @else
                                                                                {{ $value['old'] }}
                                                                            @endif
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(is_array($value))
                                                                        @if(isset($value['new']))
                                                                            @if(is_array($value['new']))
                                                                                <div class="text-dark bg-light p-2 rounded">
                                                                                    {{ json_encode($value['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                                                                </div>
                                                                            @else
                                                                                {{ $value['new'] }}
                                                                            @endif
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    @else
                                                                        @if(is_array($value))
                                                                            <div class="text-dark bg-light p-2 rounded">
                                                                                {{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                                                            </div>
                                                                        @else
                                                                            {{ $value }}
                                                                        @endif
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            @else
                                            <div class="alert alert-info">
                                                Bu işlemde değişen alan bilgisi bulunmuyor.
                                            </div>
                                            @endif
                                            
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h4 class="card-title">JSON Verisi</h4>
                                                </div>
                                                <div class="card-body">
                                                    <pre class="language-json p-3 bg-light rounded"><code>{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-3">
                            <div class="empty">
                                <div class="empty-img">
                                    <i class="fas fa-search fa-3x text-muted"></i>
                                </div>
                                <p class="empty-title">Kayıt bulunamadı</p>
                                <p class="empty-subtitle text-muted">
                                    Arama kriterlerinize uygun kayıt bulunmamaktadır.
                                </p>
                                <div class="empty-action">
                                    <button class="btn btn-primary" wire:click="clearFilters">
                                        <i class="fas fa-times me-2"></i>Filtreleri Temizle
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <p class="m-0 text-muted">Toplam {{ $logs->total() }} kayıt</p>
            {{ $logs->links() }}
        </div>
    </div>
    
    <livewire:modals.delete-modal />
    <livewire:usermanagement.confirm-action-modal />
</div>