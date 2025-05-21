@include('settingmanagement::helper')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-cogs me-2"></i>
            Tenant Ayarları
        </h3>
    </div>
    <div class="card-body">
        <!-- Filtreleme Araçları -->
        <div class="mb-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                            placeholder="Aramak için yazın...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="selectedGroup" class="form-select">
                        <option value="">Tüm Gruplar</option>
                        @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Ayarlar Listesi -->
        @forelse($settings->groupBy('group_id') as $groupId => $groupSettings)
        @php $group = $groups->firstWhere('id', $groupId); @endphp
        <div class="card mb-3">
            <div class="card-header d-flex">
                <div class="flex-grow-1">
                    <h3 class="card-title">{{ $group->name }}</h3>
                </div>
                <div>
                    <a href="{{ route('admin.settingmanagement.values', $groupId) }}" class="btn btn-sm btn-ghost-secondary">
                        Toplu Düzenle 
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead>
                        <tr>
                            <th style="width: 25%">BAŞLIK</th>
                            <th style="width: 5%">ÖZEL</th>
                            <th style="width: 35%">DEĞER</th>
                            <th style="width: 20%">ANAHTAR</th>
                            <th style="width: 10%">TİP</th>
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
                                        <i class="fas fa-file text-primary me-2"></i>
                                        <a href="{{ cdn($setting->current_value) }}" target="_blank" class="text-truncate">
                                            {{ basename($setting->current_value) }}
                                        </a>
                                        <!-- Değer Düzenle bağlantısı kaldırıldı -->
                                    </div>
                                @elseif($setting->type === 'image' && $setting->current_value)
                                    <div class="d-flex align-items-center">
                                        <img src="{{ cdn($setting->current_value) }}" class="img-thumbnail me-2" style="max-width: 40px; max-height: 40px;">
                                        <!-- Değer Düzenle bağlantısı kaldırıldı -->
                                    </div>
                                @elseif($setting->type === 'checkbox')
                                    {{ $setting->current_value == '1' ? 'Evet' : 'Hayır' }}
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
                                <!-- Değer Düzenle bağlantısı kaldırıldı -->
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
            <p class="empty-title">Ayar bulunamadı</p>
            <p class="empty-subtitle text-muted">
                Arama kriterlerinize uygun ayar bulunmamaktadır.
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