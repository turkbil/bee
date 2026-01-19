@include('cart::admin.helper')

<div>
    <div class="card">
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <!-- Arama Kutusu -->
                <div class="col-md-4">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="fas fa-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                            placeholder="{{ __('cart::admin.order_search_placeholder') }}">
                    </div>
                </div>

                <!-- Ortadaki Loading -->
                <div class="col-md-4 position-relative">
                    <div wire:loading class="position-absolute top-50 start-50 translate-middle text-center" style="width: 100%; max-width: 250px;">
                        <div class="small mb-2 text-muted">{{ __('cart::admin.updating') }}</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Taraf -->
                <div class="col-md-4">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <!-- Filtre Butonu -->
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse"
                            data-bs-target="#filterCollapse" aria-expanded="false">
                            <i class="fas fa-filter me-1"></i>{{ __('cart::admin.filters') }}
                            @if($this->hasActiveFilters())
                            <span class="badge bg-primary ms-1">!</span>
                            @endif
                        </button>

                        <!-- Excel Export -->
                        <button wire:click="exportOrders" class="btn btn-sm btn-success">
                            <i class="fas fa-download me-1"></i>{{ __('cart::admin.export_excel') }}
                        </button>

                        <!-- Sayfa Adeti -->
                        <select wire:model.live="perPage" class="form-select form-select-sm" style="width: 70px;">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body border-bottom py-3">
            <!-- Filtre Bölümü - Açılır Kapanır -->
            <div class="collapse mb-3" id="filterCollapse">
                <div class="card card-body bg-light">
                    <!-- Row 1: Durum Filtreleri -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label small text-muted">{{ __('cart::admin.payment_status') }}</label>
                            <select wire:model.live="paymentStatus" class="form-select form-select-sm">
                                <option value="">{{ __('cart::admin.all') }}</option>
                                <option value="paid">{{ __('cart::admin.payment_status_paid') }}</option>
                                <option value="pending">{{ __('cart::admin.payment_status_pending') }}</option>
                                <option value="failed">{{ __('cart::admin.payment_status_failed') }}</option>
                                <option value="refunded">{{ __('cart::admin.payment_status_refunded') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">{{ __('cart::admin.order_status') }}</label>
                            <select wire:model.live="status" class="form-select form-select-sm">
                                <option value="">{{ __('cart::admin.all') }}</option>
                                <option value="pending">{{ __('cart::admin.order_status_pending') }}</option>
                                <option value="completed">{{ __('cart::admin.order_status_completed') }}</option>
                                <option value="cancelled">{{ __('cart::admin.order_status_cancelled') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">{{ __('cart::admin.payment_method') }}</label>
                            <select wire:model.live="paymentMethod" class="form-select form-select-sm">
                                <option value="">{{ __('cart::admin.all') }}</option>
                                <option value="paytr">{{ __('cart::admin.payment_method_card') }}</option>
                                <option value="manual">{{ __('cart::admin.payment_method_transfer') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">{{ __('cart::admin.start_date') }}</label>
                            <input type="date" wire:model.live="dateFrom" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">{{ __('cart::admin.end_date') }}</label>
                            <input type="date" wire:model.live="dateTo" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            @if($this->hasActiveFilters())
                            <button type="button" class="btn btn-sm btn-outline-danger w-100" wire:click="clearFilters">
                                <i class="fas fa-times me-1"></i>{{ __('cart::admin.clear_filters') }}
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Row 2: Tutar ve Switch'ler -->
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label small text-muted">{{ __('cart::admin.min_amount') }} (TL)</label>
                            <input type="number" wire:model.live.debounce.500ms="amountMin" class="form-control form-control-sm" placeholder="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">{{ __('cart::admin.max_amount') }} (TL)</label>
                            <input type="number" wire:model.live.debounce.500ms="amountMax" class="form-control form-control-sm" placeholder="~">
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2">
                            <label class="form-check form-switch mb-0">
                                <input type="checkbox" wire:model.live="showPaid" class="form-check-input">
                                <span class="form-check-label small text-success"><i class="fas fa-check-circle me-1"></i>Ödenenler</span>
                            </label>
                        </div>
                        <div class="col-md-2">
                            <label class="form-check form-switch mb-0">
                                <input type="checkbox" wire:model.live="showPending" class="form-check-input">
                                <span class="form-check-label small text-warning"><i class="fas fa-clock me-1"></i>Bekleyenler</span>
                            </label>
                        </div>
                        <div class="col-md-2">
                            <label class="form-check form-switch mb-0">
                                <input type="checkbox" wire:model.live="showFailed" class="form-check-input">
                                <span class="form-check-label small text-danger"><i class="fas fa-times-circle me-1"></i>Başarısızlar</span>
                            </label>
                        </div>
                    </div>

                    <!-- Aktif Filtreler Badge'leri -->
                    @if($this->hasActiveFilters())
                    <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
                        @if($search)
                            <span class="badge bg-azure-lt text-azure">{{ __('cart::admin.search') }}: {{ $search }}</span>
                        @endif
                        @if($paymentStatus)
                            <span class="badge bg-green-lt text-green">{{ __('cart::admin.payment') }}: {{ __('cart::admin.payment_status_' . $paymentStatus) }}</span>
                        @endif
                        @if($status)
                            <span class="badge bg-blue-lt text-blue">{{ __('cart::admin.status') }}: {{ __('cart::admin.order_status_' . $status) }}</span>
                        @endif
                        @if($paymentMethod)
                            <span class="badge bg-purple-lt text-purple">{{ __('cart::admin.payment_method') }}: {{ $paymentMethod === 'paytr' ? __('cart::admin.payment_method_card') : __('cart::admin.payment_method_transfer') }}</span>
                        @endif
                        @if($dateFrom)
                            <span class="badge bg-teal-lt text-teal">{{ __('cart::admin.start_date') }}: {{ $dateFrom }}</span>
                        @endif
                        @if($dateTo)
                            <span class="badge bg-cyan-lt text-cyan">{{ __('cart::admin.end_date') }}: {{ $dateTo }}</span>
                        @endif
                        @if($amountMin)
                            <span class="badge bg-orange-lt text-orange">Min: {{ $amountMin }} TL</span>
                        @endif
                        @if($amountMax)
                            <span class="badge bg-red-lt text-red">Max: {{ $amountMax }} TL</span>
                        @endif
                        @if(!$showPaid)
                            <span class="badge bg-secondary-lt text-secondary">Ödenenler Hariç</span>
                        @endif
                        @if($showPending)
                            <span class="badge bg-yellow-lt text-yellow">Bekleyenler Dahil</span>
                        @endif
                        @if($showFailed)
                            <span class="badge bg-red-lt text-red">Başarısızlar Dahil</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Özet İstatistikler -->
            <div class="d-flex gap-4 small">
                <span><i class="fas fa-receipt me-1"></i>{{ __('cart::admin.total_orders') }}: <strong>{{ $stats['total_count'] }}</strong> ({{ number_format($stats['total_amount'], 0, ',', '.') }} TL)</span>
                <span class="text-success"><i class="fas fa-check-circle me-1"></i>{{ __('cart::admin.paid_orders') }}: <strong>{{ $stats['paid_count'] }}</strong></span>
                <span class="text-warning"><i class="fas fa-clock me-1"></i>{{ __('cart::admin.pending_orders') }}: <strong>{{ $stats['pending_count'] }}</strong></span>
            </div>
        </div>

        <!-- Tablo -->
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                        </th>
                        <th>{{ __('cart::admin.order_no') }}</th>
                        <th>{{ __('cart::admin.customer') }}</th>
                        <th class="text-center">{{ __('cart::admin.payment_method') }}</th>
                        <th class="text-center">{{ __('cart::admin.product') }}</th>
                        <th class="text-end">{{ __('cart::admin.amount') }}</th>
                        <th class="text-center">{{ __('cart::admin.order_status') }}</th>
                        <th class="text-center">{{ __('cart::admin.payment_status') }}</th>
                        <th>{{ __('cart::admin.date') }}</th>
                        <th class="text-center">{{ __('cart::admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $paymentGateway = $order->payments->first()?->gateway;
                            $gc = match($paymentGateway) {
                                'paytr' => ['color' => 'purple', 'icon' => 'credit-card', 'label' => __('cart::admin.payment_method_card')],
                                'manual', 'bank_transfer' => ['color' => 'cyan', 'icon' => 'building-columns', 'label' => __('cart::admin.payment_method_transfer')],
                                default => ['color' => 'azure', 'icon' => 'wallet', 'label' => '-'],
                            };

                            $statusColors = [
                                'pending' => 'bg-yellow', 'processing' => 'bg-blue', 'shipped' => 'bg-purple',
                                'delivered' => 'bg-green', 'completed' => 'bg-green', 'cancelled' => 'bg-red',
                            ];

                            $paymentColors = [
                                'pending' => 'bg-yellow-lt text-yellow', 'paid' => 'bg-green-lt text-green',
                                'failed' => 'bg-red-lt text-red', 'refunded' => 'bg-orange-lt text-orange',
                            ];
                        @endphp
                        <tr wire:key="order-{{ $order->order_id }}">
                            <td class="text-center" onclick="event.stopPropagation();">
                                <input type="checkbox" wire:model.live="selectedOrders" value="{{ $order->order_id }}" class="form-check-input">
                            </td>
                            <td>
                                <strong class="font-monospace">{{ $order->order_number }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ Str::limit($order->customer_name ?: __('cart::admin.guest'), 20) }}</strong>
                                    @if($order->customer_email)
                                        <div class="small text-muted">{{ Str::limit($order->customer_email, 25) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $gc['color'] }}">
                                    <i class="fas fa-{{ $gc['icon'] }} me-1"></i>{{ $gc['label'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-azure">{{ $order->items->count() }}</span>
                            </td>
                            <td class="text-end">
                                <strong>{{ number_format($order->total_amount, 0, ',', '.') }} TL</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $statusColors[$order->status] ?? 'bg-dark' }}">
                                    {{ __('cart::admin.order_status_' . $order->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $paymentColors[$order->payment_status] ?? 'bg-dark' }}">
                                    {{ __('cart::admin.payment_status_' . $order->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $order->created_at->format('d.m.Y') }}</div>
                                <div class="small text-muted">{{ $order->created_at->format('H:i') }}</div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal" data-bs-target="#orderDetailModal"
                                        onclick="loadOrderDetail({{ $order->order_id }})" title="Detay">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4">
                            <div class="empty">
                                <div class="empty-icon"><i class="fas fa-receipt fa-3x text-muted"></i></div>
                                <p class="empty-title">{{ __('cart::admin.no_orders_found') }}</p>
                                <p class="empty-subtitle text-muted">{{ __('cart::admin.no_orders_description') }}</p>
                                @if($this->hasActiveFilters())
                                <div class="empty-action">
                                    <button wire:click="clearFilters" class="btn btn-primary">
                                        <i class="fas fa-times me-1"></i>{{ __('cart::admin.clear_filters') }}
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
        @if($orders->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

    <!-- Bulk Actions -->
    @if(count($selectedOrders) > 0)
    <div class="position-fixed bottom-0 start-50 translate-middle-x mb-4" style="z-index: 1050;">
        <div class="card shadow-lg border-0">
            <div class="card-body p-3">
                <div class="d-flex gap-3 align-items-center">
                    <span class="small"><strong>{{ count($selectedOrders) }}</strong> {{ __('cart::admin.selected_orders') }}</span>
                    <button wire:click="bulkMarkAsPaid" wire:confirm="Seçili siparişleri ödendi olarak işaretlemek istediğinizden emin misiniz?" class="btn btn-sm btn-success">
                        <i class="fas fa-check me-1"></i>{{ __('cart::admin.mark_as_paid') }}
                    </button>
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                            {{ __('cart::admin.change_status') }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" wire:click.prevent="bulkChangeStatus('completed')">{{ __('cart::admin.order_status_completed') }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" wire:click.prevent="bulkChangeStatus('cancelled')">{{ __('cart::admin.order_status_cancelled') }}</a></li>
                        </ul>
                    </div>
                    <button wire:click="$set('selectedOrders', [])" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Order Detail Modal (Bootstrap) -->
    <div class="modal modal-blur fade" id="orderDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-receipt me-2"></i>
                        <span id="modalOrderTitle">{{ __('cart::admin.order_detail') }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderModalBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <div class="mt-2 text-muted">{{ __('cart::admin.loading') }}</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">{{ __('cart::admin.close') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadOrderDetail(orderId) {
    document.getElementById('orderModalBody').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><div class="mt-2 text-muted">{{ __('cart::admin.loading') }}</div></div>';
    document.getElementById('modalOrderTitle').textContent = '{{ __('cart::admin.order_detail') }}';

    fetch(`/admin/orders/${orderId}/detail`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalOrderTitle').textContent = `{{ __('cart::admin.order_detail') }} #${data.order.order_number}`;
                document.getElementById('orderModalBody').innerHTML = data.html;
            } else {
                document.getElementById('orderModalBody').innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>{{ __('cart::admin.no_orders_found') }}</div>';
            }
        })
        .catch(error => {
            console.error('Order detail error:', error);
            document.getElementById('orderModalBody').innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Bir hata oluştu</div>';
        });
}
</script>
@endpush
