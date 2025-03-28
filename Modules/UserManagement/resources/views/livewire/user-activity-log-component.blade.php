@include('usermanagement::helper')
<div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-history text-primary me-2"></i>{{ $userName }} - İşlem Kayıtları
                </h3>
                <div class="btn-list">
                    <a href="{{ route('admin.usermanagement.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kullanıcı Listesine Dön
                    </a>
                    
                    @if($isRoot)
                    <button type="button" class="btn btn-outline-danger" wire:click="clearUserLogs">
                        <i class="fas fa-trash-alt me-2"></i>Tüm Kayıtları Temizle
                    </button>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card-body border-bottom">
            <!-- Filtreler - Açılır Panel -->
            <div class="accordion" id="filterAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="filterHeading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                            <i class="fas fa-filter me-2"></i> Filtreleme Seçenekleri
                            @if($search || $moduleFilter || $eventFilter || $dateFrom || $dateTo)
                            <span class="badge bg-primary ms-2">Aktif</span>
                            @endif
                        </button>
                    </h2>
                    <div id="filterCollapse" class="accordion-collapse collapse" aria-labelledby="filterHeading" data-bs-parent="#filterAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Arama</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" 
                                            placeholder="Kayıtlarda ara...">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Modül</label>
                                    <select wire:model.live="moduleFilter" class="form-select">
                                        <option value="">Tüm Modüller</option>
                                        @foreach($modules as $module)
                                            <option value="{{ $module }}">{{ $module }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Eylem</label>
                                    <select wire:model.live="eventFilter" class="form-select">
                                        <option value="">Tüm Eylemler</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event }}">{{ $event }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tarih Aralığı</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="input-icon">
                                                <span class="input-icon-addon">
                                                    <i class="fas fa-calendar"></i>
                                                </span>
                                                <input type="date" wire:model.live="dateFrom" class="form-control" placeholder="Başlangıç">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-icon">
                                                <span class="input-icon-addon">
                                                    <i class="fas fa-calendar"></i>
                                                </span>
                                                <input type="date" wire:model.live="dateTo" class="form-control" placeholder="Bitiş">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="form-label">Kayıt sayısı</label>
                                    <select wire:model.live="perPage" class="form-select">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3 d-flex align-items-end justify-content-end">
                                    @if($search || $moduleFilter || $eventFilter || $dateFrom || $dateTo)
                                    <button class="btn btn-outline-secondary" wire:click="clearFilters">
                                        <i class="fas fa-times me-1"></i>Filtreleri Temizle
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Loading Spinner -->
        <div wire:loading wire:target="search, moduleFilter, eventFilter, dateFrom, dateTo, perPage, nextPage, previousPage, gotoPage, sortBy">
            <div class="progress progress-sm">
                <div class="progress-bar progress-bar-indeterminate"></div>
            </div>
        </div>
        
        <!-- Aktivite Listesi -->
        <div class="card-body p-0">
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
                            <div class="text-truncate">
                                <!-- Eylem açıklaması -->
                                @if($log->event == 'created')
                                    <strong>{{ $log->log_name }}</strong> kaydı oluşturuldu.
                                @elseif($log->event == 'updated')
                                    <strong>{{ $log->log_name }}</strong> kaydı güncellendi.
                                @elseif($log->event == 'deleted')
                                    <strong>{{ $log->log_name }}</strong> kaydı silindi.
                                @else
                                    {{ $log->description }}
                                @endif
                                
                                <!-- Nesne başlığı -->
                                @if(isset($log->properties['baslik']))
                                <strong>"{{ $log->properties['baslik'] }}"</strong>
                                @endif
                            </div>
                            <div class="text-secondary">{{ $log->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="col-auto align-self-center">
                            <div class="btn-list">
                                <a href="#" class="btn btn-icon btn-sm" data-bs-toggle="modal" data-bs-target="#details-modal-{{ $log->id }}" title="Detayları Görüntüle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($isRoot)
                                <a href="#" class="btn btn-icon btn-sm" wire:click="$dispatch('showDeleteModal', {
                                    module: 'activitylog',
                                    id: {{ $log->id }},
                                    title: '{{ $log->id }} numaralı kayıt'
                                })" title="Kaydı Sil">
                                    <i class="fas fa-trash text-danger"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detay Modal -->
                    <div class="modal modal-blur fade" id="details-modal-{{ $log->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">İşlem Detayları</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
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
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
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
                @endforelse
            </div>
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