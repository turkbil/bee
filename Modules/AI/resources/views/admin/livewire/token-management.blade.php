<div>
    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Başarılı!</strong> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Hata!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-vcenter card-table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Site</th>
                    <th class="text-center" style="width: 80px">AI Durumu</th>
                    <th>Token Bakiyesi</th>
                    <th>Aylık Limit</th>
                    <th>Bu Ay Kullanım</th>
                    <th>Son Kullanım</th>
                    <th class="text-center" style="width: 160px">İşlemler</th>
                </tr>
            </thead>
            <tbody class="table-tbody">
                @forelse($tenants as $tenant)
                <tr class="hover-trigger">
                    <td>
                        <div class="d-flex align-items-center">
                            <div>
                                <a href="{{ route('admin.ai.tokens.show', $tenant) }}" class="fw-bold text-decoration-none">
                                    {{ $tenant->title ?: 'Varsayılan' }}
                                </a>
                                <div class="text-muted small">ID: {{ $tenant->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center align-middle">
                        <button wire:click="toggleAI({{ $tenant->id }}, {{ $tenant->ai_enabled ? 'false' : 'true' }})"
                            class="btn btn-icon btn-sm {{ $tenant->ai_enabled ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                            @if($tenant->ai_enabled)
                            <i class="fas fa-check"></i>
                            @else
                            <i class="fas fa-times"></i>
                            @endif
                        </button>
                    </td>
                    <td>
                        <div class="fw-bold">{{ \App\Helpers\TokenHelper::format($tenant->real_token_balance) }}</div>
                        <div class="text-muted small">gerçek bakiye</div>
                        <div class="text-xs text-muted">Satın alınan: {{ \App\Helpers\TokenHelper::format($tenant->total_purchased) }} | Kullanılan: {{ \App\Helpers\TokenHelper::format($tenant->total_used) }}</div>
                    </td>
                    <td>
                        @if($tenant->ai_monthly_token_limit > 0)
                            <div class="fw-bold">{{ \App\Helpers\TokenHelper::format($tenant->ai_monthly_token_limit) }}</div>
                            <div class="text-muted small">token/ay</div>
                        @else
                            <span class="text-muted">Sınırsız</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.ai.tokens.usage-stats', ['tenant_id' => $tenant->id]) }}" class="text-decoration-none">
                            <div class="fw-bold">{{ \App\Helpers\TokenHelper::format($tenant->ai_tokens_used_this_month) }}</div>
                        </a>
                        @if($tenant->ai_monthly_token_limit > 0)
                            <div class="progress progress-sm mt-1">
                                <div class="progress-bar" 
                                     style="width: {{ min(100, ($tenant->ai_tokens_used_this_month / $tenant->ai_monthly_token_limit) * 100) }}%">
                                </div>
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($tenant->ai_last_used_at)
                            <div class="fw-bold">{{ $tenant->ai_last_used_at->format('d.m.Y H:i') }}</div>
                            <div class="text-muted small">{{ $tenant->ai_last_used_at->diffForHumans() }}</div>
                        @else
                            <span class="text-muted">Hiç kullanılmamış</span>
                        @endif
                    </td>
                    <td class="text-center align-middle">
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <a href="{{ route('admin.ai.tokens.show', $tenant) }}"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Detayları Görüntüle">
                                        <i class="fa-solid fa-eye link-secondary fa-lg"></i>
                                    </a>
                                </div>
                                <div class="col lh-1">
                                    <div class="dropdown mt-1">
                                        <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" 
                                               wire:click="toggleAI({{ $tenant->id }}, {{ $tenant->ai_enabled ? 'false' : 'true' }})" 
                                               class="dropdown-item {{ $tenant->ai_enabled ? 'link-danger' : 'link-success' }}">
                                                <i class="fas fa-{{ $tenant->ai_enabled ? 'times' : 'check' }} me-2"></i>
                                                AI'yi {{ $tenant->ai_enabled ? 'Devre Dışı Bırak' : 'Aktif Et' }}
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a href="javascript:void(0);" 
                                               wire:click="openTokenModal({{ $tenant->id }})" 
                                               class="dropdown-item">
                                                <i class="fas fa-coins me-2"></i>Token Ekle/Çıkar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">Henüz kiracı bulunmuyor.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($tenants->hasPages())
        <div class="mt-3">
            {{ $tenants->links() }}
        </div>
    @endif

    <!-- Token Yönetimi Modal -->
    @if($showModal)
    <div class="modal modal-blur fade show" style="display: block;" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Token Yönetimi</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    @if($selectedTenant)
                    <div class="mb-3">
                        <label class="form-label">Kiracı</label>
                        <div class="form-control-plaintext">{{ $selectedTenant->title ?: 'Varsayılan' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gerçek Bakiye</label>
                        <div class="form-control-plaintext">{{ \App\Helpers\TokenHelper::format($currentBalance) }} token</div>
                        <small class="text-muted">
                            Satın alınan: {{ \App\Helpers\TokenHelper::format($selectedTenant->total_purchased ?? 0) }} | 
                            Kullanılan: {{ \App\Helpers\TokenHelper::format($selectedTenant->total_used ?? 0) }}
                        </small>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="tokenAmount" class="form-label">Token Miktarı</label>
                        <input type="number" class="form-control @error('tokenAmount') is-invalid @enderror" 
                               wire:model="tokenAmount" 
                               placeholder="Eklemek için pozitif, çıkarmak için negatif">
                        @error('tokenAmount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Pozitif sayı token ekler, negatif sayı token çıkarır.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="adjustmentReason" class="form-label">Açıklama</label>
                        <textarea class="form-control @error('adjustmentReason') is-invalid @enderror" 
                                  wire:model="adjustmentReason" 
                                  rows="3" 
                                  placeholder="Token düzenleme sebebini açıklayın..."></textarea>
                        @error('adjustmentReason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @error('general')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">İptal</button>
                    <button type="button" class="btn btn-primary" wire:click="adjustTokens">
                        <span wire:loading.remove>Uygula</span>
                        <span wire:loading>İşleniyor...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>

@script
<script>
    // Auto refresh after token adjustment
    $wire.on('tokenAdjusted', () => {
        setTimeout(() => {
            location.reload();
        }, 2000);
    });

    // Auto refresh after AI toggle
    $wire.on('aiToggled', () => {
        setTimeout(() => {
            location.reload();
        }, 1500);
    });
</script>
@endscript