@include('usermanagement::helper')
<div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-history text-azure me-2"></i>
                    <h3 class="card-title m-0">İşlem Kayıtları</h3>
                </div>
                
                @if($isRoot)
                <a href="javascript:void(0);" wire:click="clearLogs" class="btn btn-outline-danger">
                    <i class="fas fa-trash-alt me-2"></i>Tüm Kayıtları Temizle
                </a>
                @endif
            </div>
        </div>
        
        <div class="card-body border-bottom pb-3">
            <!-- Filtreleme Araçları -->
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
                
                <!-- Sayfa Başına Kayıt -->
                <div class="col-md-1">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                
                <!-- Temizle Butonu -->
                <div class="col-md-2 d-flex justify-content-end">
                    @if($search || $userFilter || $moduleFilter || $eventFilter || $dateFrom || $dateTo)
                    <button type="button" class="btn btn-outline-secondary" wire:click="clearFilters">
                        <i class="fas fa-times me-1"></i>Filtreleri Temizle
                    </button>
                    @endif
                </div>
            </div>
            
            <!-- Tarih Aralığı -->
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
                
                <!-- Aktif Filtreler -->
                <div class="col-md-6">
                    @if($search || $userFilter || $moduleFilter || $eventFilter || $dateFrom || $dateTo)
                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                        @if($search)
                            <span class="badge bg-azure-lt">{{ $search }}</span>
                        @endif
                        @if($userFilter)
                            <span class="badge bg-blue-lt">{{ $users->firstWhere('id', $userFilter)->name ?? 'Kullanıcı' }}</span>
                        @endif
                        @if($moduleFilter)
                            <span class="badge bg-indigo-lt">{{ $moduleFilter }}</span>
                        @endif
                        @if($eventFilter)
                            <span class="badge bg-purple-lt">{{ $eventFilter }}</span>
                        @endif
                        @if($dateFrom)
                            <span class="badge bg-teal-lt">{{ $dateFrom }}</span>
                        @endif
                        @if($dateTo)
                            <span class="badge bg-cyan-lt">{{ $dateTo }}</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Yükleniyor İndikatörü -->
        <div wire:loading wire:target="search, userFilter, moduleFilter, eventFilter, dateFrom, dateTo, perPage, sortBy, gotoPage, previousPage, nextPage, confirmDelete, confirmBulkDelete, clearLogs, clearUserLogs">
            <div class="progress progress-sm">
                <div class="progress-bar progress-bar-indeterminate"></div>
            </div>
        </div>
        
        <!-- Tablo -->
        <div class="table-responsive">
            <table class="table card-table table-vcenter">
                <thead>
                    <tr class="text-nowrap">
                        <th style="width: 3rem">
                            <div class="d-flex align-items-center gap-2">
                                <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                                <button class="table-sort {{ $sortField === 'id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" 
                                    wire:click="sortBy('id')">ID</button>
                            </div>
                        </th>
                        <th>
                            <button class="table-sort {{ $sortField === 'log_name' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" 
                                wire:click="sortBy('log_name')">MODÜL</button>
                        </th>
                        <th>
                            <button class="table-sort {{ $sortField === 'description' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" 
                                wire:click="sortBy('description')">AÇIKLAMA</button>
                        </th>
                        <th>
                            <button class="table-sort {{ $sortField === 'subject_type' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" 
                                wire:click="sortBy('subject_type')">NESNE</button>
                        </th>
                        <th>
                            <button class="table-sort {{ $sortField === 'event' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" 
                                wire:click="sortBy('event')">EYLEM</button>
                        </th>
                        <th>
                            <button class="table-sort {{ $sortField === 'causer_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" 
                                wire:click="sortBy('causer_id')">KULLANICI</button>
                        </th>
                        <th>
                            <button class="table-sort {{ $sortField === 'created_at' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" 
                                wire:click="sortBy('created_at')">TARİH</button>
                        </th>
                        <th style="width: 5rem">İŞLEMLER</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr wire:key="log-{{ $log->id }}">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $log->id }}" 
                                    class="form-check-input" @if(in_array($log->id, $selectedItems)) checked @endif>
                                <span class="text-muted">{{ $log->id }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-azure-lt">{{ $log->log_name }}</span>
                        </td>
                        <td class="text-wrap">
                            <div>{{ $log->description }}</div>
                            @if(isset($log->properties['baslik']) && $log->properties['baslik'])
                                <div class="text-muted small">{{ $log->properties['baslik'] }}</div>
                            @endif
                        </td>
                        <td>
                            @if($log->subject_type)
                                <div>{{ class_basename($log->subject_type) }}</div>
                                @if($log->subject_id)
                                    <div class="text-muted small">ID: {{ $log->subject_id }}</div>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($log->event == 'created')
                                <span class="badge bg-green">{{ $log->event }}</span>
                            @elseif($log->event == 'updated')
                                <span class="badge bg-blue">{{ $log->event }}</span>
                            @elseif($log->event == 'deleted')
                                <span class="badge bg-pink">{{ $log->event }}</span>
                            @else
                                <span class="badge bg-purple">{{ $log->event }}</span>
                            @endif
                        </td>
                        <td>
                            @if($log->causer)
                                @if($isRoot || !$log->causer->isRoot())
                                    <a href="{{ route('admin.usermanagement.user.activity.logs', $log->causer->id) }}" class="text-reset">
                                        {{ $log->causer->name }}
                                    </a>
                                @else
                                    <span>{{ $log->causer->name }}</span>
                                @endif
                                <div class="text-muted small">ID: {{ $log->causer->id }}</div>
                            @else
                                <span class="text-secondary">Sistem</span>
                            @endif
                        </td>
                        <td>
                            {{ $log->created_at->format('d.m.Y') }}
                            <div class="text-muted small">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#details-modal-{{ $log->id }}" class="btn btn-icon btn-sm" title="Detayları Görüntüle">
                                    <i class="fas fa-eye text-muted"></i>
                                </a>
                                
                                @if($isRoot)
                                <div class="dropdown">
                                    <a href="#" class="btn btn-icon btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v text-muted"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="javascript:void(0);" wire:click="confirmDelete({{ $log->id }})" class="dropdown-item text-danger">
                                            <i class="fas fa-trash me-1"></i> Sil
                                        </a>
                                        @if($log->causer)
                                        <a href="javascript:void(0);" wire:click="clearUserLogs({{ $log->causer->id }})" class="dropdown-item text-danger">
                                            <i class="fas fa-user-times me-1"></i> Kullanıcı Kayıtlarını Sil
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Detay Modal -->
                            <div class="modal modal-blur fade" id="details-modal-{{ $log->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-info-circle text-info me-2"></i>
                                                İşlem #{{ $log->id }} Detayı
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
                                                            <h3 class="card-title">Genel Bilgiler</h3>
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
                                                                    <div class="col-4">Açıklama</div>
                                                                    <div class="col-8 text-muted">{{ $log->description }}</div>
                                                                </div>
                                                            </div>
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">Eylem</div>
                                                                    <div class="col-8">
                                                                        @if($log->event == 'created')
                                                                            <span class="badge bg-green">{{ $log->event }}</span>
                                                                        @elseif($log->event == 'updated')
                                                                            <span class="badge bg-blue">{{ $log->event }}</span>
                                                                        @elseif($log->event == 'deleted')
                                                                            <span class="badge bg-pink">{{ $log->event }}</span>
                                                                        @else
                                                                            <span class="badge bg-purple">{{ $log->event }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">Kullanıcı</div>
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
                                                                            <span class="text-muted">Sistem</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">Tarih</div>
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
                                                            <h3 class="card-title">Nesne Bilgileri</h3>
                                                        </div>
                                                        <div class="list-group list-group-flush">
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">Tür</div>
                                                                    <div class="col-8 text-muted">
                                                                        {{ $log->subject_type ? class_basename($log->subject_type) : 'Belirtilmemiş' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">ID</div>
                                                                    <div class="col-8 text-muted">
                                                                        {{ $log->subject_id ?? 'Belirtilmemiş' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-group-item">
                                                                <div class="row">
                                                                    <div class="col-4">Başlık</div>
                                                                    <div class="col-8 text-muted">
                                                                        {{ $log->properties['baslik'] ?? 'Belirtilmemiş' }}
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
                                                            <h3 class="card-title">Değişen Alanlar</h3>
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
                                                                        <td class="fw-medium">{{ $key }}</td>
                                                                        <td>
                                                                            @if(is_array($value) && isset($value['old']))
                                                                                @if(is_array($value['old']))
                                                                                    <pre class="p-2 bg-dark text-white rounded">{{ json_encode($value['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
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
                                                                                    <pre class="p-2 bg-dark text-white rounded">{{ json_encode($value['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
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
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                <!-- JSON Verisi (Gelişmiş) -->
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-status-top bg-yellow"></div>
                                                        <div class="card-header d-flex justify-content-between align-items-center">
                                                            <h3 class="card-title">Detaylı JSON Verisi</h3>
                                                            <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('log-json-{{ $log->id }}')">
                                                                <i class="fas fa-copy me-1"></i> Kopyala
                                                            </button>
                                                        </div>
                                                        <div class="card-body">
                                                            <pre id="log-json-{{ $log->id }}" class="p-3 bg-dark text-white rounded-3 overflow-auto" style="max-height: 300px;">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn" data-bs-dismiss="modal">Kapat</button>
                                            @if($isRoot)
                                            <button type="button" class="btn btn-danger ms-auto" wire:click="confirmDelete({{ $log->id }})" data-bs-dismiss="modal">
                                                <i class="fas fa-trash me-1"></i> Bu Kaydı Sil
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="empty">
                                <div class="empty-img">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-database-off" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12.983 8.978c3.955 -.182 7.017 -1.446 7.017 -2.978c0 -1.657 -3.582 -3 -8 -3c-1.661 0 -3.204 .19 -4.483 .515m-3.139 1.126c-.238 .418 -.378 .871 -.378 1.359c0 1.657 3.582 3 8 3c.986 0 1.93 -.067 2.802 -.19"></path>
                                        <path d="M4 6v6c0 1.657 3.582 3 8 3c3.217 0 5.991 -.712 7.261 -1.74m.739 -3.26v-4"></path>
                                        <path d="M4 12v6c0 1.657 3.582 3 8 3c4.418 0 8 -1.343 8 -3v-2"></path>
                                        <path d="M3 3l18 18"></path>
                                    </svg>
                                </div>
                                <p class="empty-title">Kayıt bulunamadı</p>
                                <p class="empty-subtitle text-secondary">Arama kriterlerinize uygun kayıt bulunmamaktadır.</p>
                                @if($search || $userFilter || $moduleFilter || $eventFilter || $dateFrom || $dateTo)
                                <div class="empty-action">
                                    <button class="btn btn-primary" wire:click="clearFilters">
                                        <i class="fas fa-filter me-1"></i> Filtreleri Temizle
                                    </button>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-secondary">Toplam <span class="fw-medium">{{ $logs->total() }}</span> kayıt</p>
            <div class="ms-auto">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
    
    <!-- Bulk Actions -->
    @if($bulkActionsEnabled)
    <div class="position-fixed bottom-0 start-50 translate-middle-x mb-3" style="z-index: 1000;">
        <div class="card shadow-lg border-0 rounded">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary">{{ count($selectedItems) }} öğe seçildi</span>
                    @if($isRoot)
                    <button type="button" class="btn btn-danger btn-sm" wire:click="confirmBulkDelete">
                        <i class="fas fa-trash me-1"></i>Seçilenleri Sil
                    </button>
                    @endif
                    <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="refreshSelectedItems">
                        <i class="fas fa-times me-1"></i>Seçimi İptal Et
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

@push('scripts')
    <script>
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const textToCopy = element.textContent;
            
            navigator.clipboard.writeText(textToCopy)
                .then(() => {
                    // Kopyalama başarılı olduğunda bildirim göster
                    const notification = document.createElement('div');
                    notification.className = 'position-fixed top-0 end-0 p-3';
                    notification.style.zIndex = '1080';
                    notification.innerHTML = `
                        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header bg-success text-white">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong class="me-auto">Başarılı</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                JSON verisi panoya kopyalandı.
                            </div>
                        </div>
                    `;
                    document.body.appendChild(notification);
                    
                    // 3 saniye sonra bildirimi kaldır
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                })
                .catch(err => {
                    console.error('Kopyalama başarısız:', err);
                });
        }
    </script>
@endpush