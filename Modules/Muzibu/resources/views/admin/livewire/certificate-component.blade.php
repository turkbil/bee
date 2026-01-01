<div class="certificate-wrapper">
    <div class="card">
        <div class="card-body p-0">
            <!-- Header -->
            <div class="row mx-2 my-3">
                <!-- Arama -->
                <div class="col-md-3">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control"
                            placeholder="Sertifika ara...">
                    </div>
                </div>

                <!-- Gecerlilik Filtre -->
                <div class="col-md-2">
                    <select wire:model.live="validFilter" class="form-select">
                        <option value="">Tum Sertifikalar</option>
                        <option value="1">Gecerli</option>
                        <option value="0">Iptal Edilmis</option>
                    </select>
                </div>

                <!-- Sayfa Basina -->
                <div class="col-md-2">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <!-- Loading -->
                <div class="col-md-3 position-relative">
                    <div wire:loading class="position-absolute top-50 start-50 translate-middle">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Yukleniyor...</span>
                        </div>
                    </div>
                </div>

                <!-- Yeni Ekle -->
                <div class="col-md-2 text-end">
                    <a href="{{ route('admin.muzibu.certificate.manage') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Yeni Sertifika
                    </a>
                </div>
            </div>

            <!-- Toplu Islem -->
            @if(count($selectedIds) > 0)
            <div class="alert alert-warning mx-3">
                <div class="d-flex align-items-center justify-content-between">
                    <span>
                        <strong>{{ count($selectedIds) }}</strong> sertifika secildi
                    </span>
                    <button type="button" wire:click="bulkDelete" wire:confirm="Secili sertifikalari silmek istediginize emin misiniz?"
                        class="btn btn-danger btn-sm">
                        <i class="fas fa-trash me-1"></i>
                        Secilenleri Sil
                    </button>
                </div>
            </div>
            @endif

            <!-- Tablo -->
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 40px">
                                <input type="checkbox" class="form-check-input"
                                    wire:model.live="selectAll">
                            </th>
                            <th wire:click="sortBy('id')" style="cursor: pointer; width: 60px">
                                #
                                @if($sortField === 'id')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('certificate_code')" style="cursor: pointer">
                                Sertifika Kodu
                                @if($sortField === 'certificate_code')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('member_name')" style="cursor: pointer">
                                Uye Adi
                                @if($sortField === 'member_name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Kullanici</th>
                            <th style="width: 120px">Uyelik Bas.</th>
                            <th style="width: 120px">Gecerlilik</th>
                            <th style="width: 80px">Gorunum</th>
                            <th style="width: 100px">Durum</th>
                            <th class="text-center" style="width: 140px">Islemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($certificates as $cert)
                        <tr wire:key="cert-{{ $cert->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input"
                                    wire:model.live="selectedIds" value="{{ $cert->id }}">
                            </td>
                            <td class="text-muted">{{ $cert->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-certificate text-warning me-2"></i>
                                    <div>
                                        <div class="fw-bold">{{ $cert->certificate_code }}</div>
                                        <div class="small text-muted">
                                            @if($cert->verification_hash)
                                                <a href="{{ route('muzibu.certificate.verify', $cert->verification_hash) }}" target="_blank" class="text-decoration-none">
                                                    <i class="fas fa-external-link-alt me-1"></i>Dogrula
                                                </a>
                                            @else
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Hash yok</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $cert->member_name }}</div>
                                @if($cert->tax_number)
                                    <div class="small text-muted">VKN: {{ $cert->tax_number }}</div>
                                @endif
                            </td>
                            <td>
                                @if($cert->user)
                                    <span class="badge bg-azure-lt">
                                        {{ $cert->user->name }}
                                    </span>
                                    <div class="small text-muted">{{ $cert->user->email }}</div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($cert->membership_start)
                                    <span class="small">{{ $cert->membership_start->format('d.m.Y') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($cert->valid_until)
                                    <span class="small {{ $cert->valid_until->isPast() ? 'text-danger' : '' }}">
                                        {{ $cert->valid_until->format('d.m.Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary" title="QR tarama sayisi">
                                    <i class="fas fa-qrcode me-1"></i>{{ $cert->view_count ?? 0 }}
                                </span>
                            </td>
                            <td>
                                @if($cert->is_valid)
                                    <span class="badge bg-success">Gecerli</span>
                                @else
                                    <span class="badge bg-danger">Iptal</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.muzibu.certificate.manage', $cert->id) }}"
                                        class="btn btn-sm btn-ghost-primary" title="Duzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" wire:click="toggleValid({{ $cert->id }})"
                                        class="btn btn-sm btn-ghost-{{ $cert->is_valid ? 'success' : 'danger' }}"
                                        title="{{ $cert->is_valid ? 'Iptal Et' : 'Gecerli Yap' }}">
                                        <i class="fas fa-{{ $cert->is_valid ? 'toggle-on' : 'toggle-off' }}"></i>
                                    </button>
                                    <button type="button" wire:click="deleteCertificate({{ $cert->id }})"
                                        wire:confirm="Bu sertifikayi silmek istediginize emin misiniz?"
                                        class="btn btn-sm btn-ghost-danger" title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-img">
                                        <i class="fas fa-certificate fa-3x text-muted"></i>
                                    </div>
                                    <p class="empty-title">Sertifika bulunamadi</p>
                                    <p class="empty-subtitle text-muted">
                                        Henuz hic sertifika olusturulmamis veya arama kriterlerinize uygun sertifika yok.
                                    </p>
                                    <div class="empty-action">
                                        <a href="{{ route('admin.muzibu.certificate.manage') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>
                                            Yeni Sertifika Olustur
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($certificates->hasPages())
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">
                    Toplam <strong>{{ $certificates->total() }}</strong> sertifika
                </p>
                <div class="ms-auto">
                    {{ $certificates->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
