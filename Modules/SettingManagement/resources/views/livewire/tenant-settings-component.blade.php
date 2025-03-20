@include('settingmanagement::helper')

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">
                <i class="fas fa-cogs me-2"></i>
                Tenant Ayarları
            </h3>
        </div>
    </div>
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Sol Taraf (Arama ve Filtreleme) -->
            <div class="col-md-8">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                placeholder="Aramak için yazmaya başlayın...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select wire:model.live="selectedGroup" class="form-select">
                            <option value="">Tüm Gruplar</option>
                            @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Ortadaki Loading -->
            <div class="col-md-1 position-relative">
                <div wire:loading
                    wire:target="render, search, selectedGroup"
                    class="position-absolute top-50 start-50 translate-middle text-center">
                    <div class="progress" style="height: 2px;">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ayarlar Listesi -->
        <div class="row row-cards">
            @forelse($settings->groupBy('group_id') as $groupId => $groupSettings)
            @php $group = $groups->firstWhere('id', $groupId); @endphp
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $group->name }}</h3>
                        <div class="card-actions">
                            <a href="{{ route('admin.settingmanagement.values', $groupId) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-pencil-alt me-1"></i> Toplu Düzenle
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Başlık</th>
                                    <th>Değer</th>
                                    <th>Kullanım</th>
                                    <th class="w-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupSettings as $setting)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="text-truncate" style="max-width: 200px;">{{ $setting->label }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted text-truncate" style="max-width: 200px;">
                                            @if($setting->type === 'file' && $setting->current_value)
                                                <i class="fas fa-file me-1"></i> Dosya
                                            @elseif($setting->type === 'checkbox')
                                                {{ $setting->current_value ? 'Evet' : 'Hayır' }}
                                            @else
                                                {{ $setting->current_value ?: '-' }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($setting->is_custom)
                                        <span class="badge bg-green">Özel</span>
                                        @else
                                        <span class="badge bg-muted">Varsayılan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.settingmanagement.value', $setting->id) }}" class="btn btn-icon btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty" style="min-height: 200px; display: flex; flex-direction: column; justify-content: center;">
                    <div class="empty-icon">
                        <i class="fas fa-cogs fa-3x text-muted"></i>
                    </div>
                    <p class="empty-title">Ayar bulunamadı</p>
                    <p class="empty-subtitle text-muted">
                        Arama kriterlerinize uygun ayar bulunmamaktadır.
                    </p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>