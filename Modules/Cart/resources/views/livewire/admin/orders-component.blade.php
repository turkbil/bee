@include('cart::admin.helper')

<div class="orders-component-wrapper">
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
                            placeholder="Sipariş no, müşteri, e-posta, telefon...">
                    </div>
                </div>
                <!-- Sipariş Durumu -->
                <div class="col-md-2">
                    <select wire:model.live="status" class="form-select">
                        <option value="">Tüm Durumlar</option>
                        @foreach($statuses as $statusOption)
                            @php
                                $statusLabels = [
                                    'pending' => 'Beklemede',
                                    'processing' => 'Hazırlanıyor',
                                    'shipped' => 'Kargoda',
                                    'delivered' => 'Teslim Edildi',
                                    'completed' => 'Tamamlandı',
                                    'cancelled' => 'İptal',
                                    'payment_failed' => 'Ödeme Başarısız',
                                ];
                            @endphp
                            <option value="{{ $statusOption }}">{{ $statusLabels[$statusOption] ?? $statusOption }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Ödeme Durumu -->
                <div class="col-md-2">
                    <select wire:model.live="paymentStatus" class="form-select">
                        <option value="">Tüm Ödemeler</option>
                        @foreach($paymentStatuses as $ps)
                            @php
                                $paymentLabels = [
                                    'pending' => 'Bekliyor',
                                    'paid' => 'Ödendi',
                                    'failed' => 'Başarısız',
                                    'refunded' => 'İade Edildi',
                                ];
                            @endphp
                            <option value="{{ $ps }}">{{ $paymentLabels[$ps] ?? $ps }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Loading -->
                <div class="col position-relative">
                    <div wire:loading wire:target="render, search, status, paymentStatus, perPage"
                        class="position-absolute top-50 start-50 translate-middle text-center" style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">Yükleniyor...</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                <!-- Per Page -->
                <div class="col-md-1">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <!-- Tablo -->
            @if($orders->isEmpty())
                <div class="empty py-5">
                    <div class="empty-icon">
                        <i class="fas fa-receipt fa-3x text-muted"></i>
                    </div>
                    <p class="empty-title">Sipariş bulunamadı</p>
                    <p class="empty-subtitle text-muted">
                        Henüz sipariş kaydı bulunmuyor veya filtrelerinize uygun sipariş yok.
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                        <thead>
                            <tr>
                                <th style="width: 140px">Sipariş No</th>
                                <th>Müşteri</th>
                                <th class="text-center" style="width: 80px">Ürün</th>
                                <th class="text-end" style="width: 120px">Tutar</th>
                                <th class="text-center" style="width: 120px">Sipariş</th>
                                <th class="text-center" style="width: 100px">Ödeme</th>
                                <th style="width: 140px">Tarih</th>
                                <th class="text-center" style="width: 80px">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr wire:key="order-{{ $order->order_id }}">
                                    <td>
                                        <strong class="font-monospace">{{ $order->order_number }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary-lt me-2">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $order->customer_name ?: 'Misafir' }}</strong>
                                                @if($order->customer_email)
                                                    <br><small class="text-muted">{{ $order->customer_email }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-azure">{{ $order->items->count() }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ number_format($order->total_amount, 0, ',', '.') }} <i class="fa-solid fa-turkish-lira-sign text-muted"></i></strong>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow',
                                                'processing' => 'bg-blue',
                                                'shipped' => 'bg-purple',
                                                'delivered' => 'bg-green',
                                                'completed' => 'bg-green',
                                                'cancelled' => 'bg-red',
                                                'payment_failed' => 'bg-red',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Beklemede',
                                                'processing' => 'Hazırlanıyor',
                                                'shipped' => 'Kargoda',
                                                'delivered' => 'Teslim',
                                                'completed' => 'Tamamlandı',
                                                'cancelled' => 'İptal',
                                                'payment_failed' => 'Öd. Başarısız',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusColors[$order->status] ?? 'bg-secondary' }}">
                                            {{ $statusLabels[$order->status] ?? $order->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $paymentColors = [
                                                'pending' => 'bg-yellow-lt text-yellow',
                                                'paid' => 'bg-green-lt text-green',
                                                'failed' => 'bg-red-lt text-red',
                                                'refunded' => 'bg-orange-lt text-orange',
                                            ];
                                            $paymentLabels = [
                                                'pending' => 'Bekliyor',
                                                'paid' => 'Ödendi',
                                                'failed' => 'Başarısız',
                                                'refunded' => 'İade',
                                            ];
                                        @endphp
                                        <span class="badge {{ $paymentColors[$order->payment_status] ?? 'bg-secondary' }}">
                                            {{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-muted">{{ $order->created_at->diffForHumans() }}</div>
                                        <small class="text-muted">{{ $order->created_at->format('d.m.Y H:i') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="viewOrder({{ $order->order_id }})" class="btn btn-sm btn-primary" title="Detay">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                    <div class="card-footer d-flex align-items-center">
                        <p class="m-0 text-muted">
                            Gösterilen: <span>{{ $orders->firstItem() }}</span> - <span>{{ $orders->lastItem() }}</span>
                            / Toplam: <span>{{ $orders->total() }}</span>
                        </p>
                        <ul class="pagination m-0 ms-auto">
                            {{ $orders->onEachSide(1)->links() }}
                        </ul>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Order Detail Modal -->
    @if($showModal && $selectedOrder)
        <div class="modal modal-blur fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-receipt me-2"></i>
                            Sipariş #{{ $selectedOrder->order_number }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Sol: Sipariş Bilgileri -->
                            <div class="col-md-8">
                                <!-- Müşteri Bilgileri -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-user me-2"></i>Müşteri Bilgileri</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <strong>Ad Soyad:</strong> {{ $selectedOrder->customer_name ?: '-' }}
                                                </div>
                                                <div class="mb-2">
                                                    <strong>E-posta:</strong> {{ $selectedOrder->customer_email ?: '-' }}
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Telefon:</strong> {{ $selectedOrder->customer_phone ?: '-' }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                @if($selectedOrder->customer_company)
                                                    <div class="mb-2">
                                                        <strong>Firma:</strong> {{ $selectedOrder->customer_company }}
                                                    </div>
                                                @endif
                                                @if($selectedOrder->customer_tax_office)
                                                    <div class="mb-2">
                                                        <strong>Vergi Dairesi:</strong> {{ $selectedOrder->customer_tax_office }}
                                                    </div>
                                                @endif
                                                @if($selectedOrder->customer_tax_number)
                                                    <div class="mb-2">
                                                        <strong>VKN/TCKN:</strong> {{ $selectedOrder->customer_tax_number }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Teslimat Adresi -->
                                @if($selectedOrder->shipping_address)
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-truck me-2"></i>Teslimat Adresi</h3>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $addr = $selectedOrder->shipping_address;
                                            @endphp
                                            @if(is_array($addr))
                                                <div>{{ $addr['address_line_1'] ?? $addr['address'] ?? '' }}</div>
                                                <div>{{ ($addr['district'] ?? '') . ' / ' . ($addr['city'] ?? '') }}</div>
                                                @if(isset($addr['postal_code']))
                                                    <div class="text-muted">{{ $addr['postal_code'] }}</div>
                                                @endif
                                            @else
                                                <div>{{ $addr }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Sipariş Ürünleri -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-box me-2"></i>Sipariş Ürünleri ({{ $selectedOrder->items->count() }})</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Ürün</th>
                                                        <th class="text-end">Birim</th>
                                                        <th class="text-center">Adet</th>
                                                        <th class="text-end">Toplam</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($selectedOrder->items as $item)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    @if($item->item_image)
                                                                        <img src="{{ $item->item_image }}" alt="" class="avatar avatar-sm me-2">
                                                                    @else
                                                                        <div class="avatar avatar-sm me-2 bg-secondary-lt">
                                                                            <i class="fas fa-box"></i>
                                                                        </div>
                                                                    @endif
                                                                    <div>
                                                                        <strong>{{ $item->product_name ?: $item->item_title }}</strong>
                                                                        @if($item->item_sku)
                                                                            <br><small class="text-muted">SKU: {{ $item->item_sku }}</small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-end">{{ number_format($item->unit_price, 0, ',', '.') }} TL</td>
                                                            <td class="text-center">
                                                                <span class="badge bg-primary">{{ $item->quantity }}</span>
                                                            </td>
                                                            <td class="text-end"><strong>{{ number_format($item->total_price, 0, ',', '.') }} TL</strong></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3" class="text-end">Ara Toplam:</td>
                                                        <td class="text-end">{{ number_format($selectedOrder->subtotal, 0, ',', '.') }} TL</td>
                                                    </tr>
                                                    @if($selectedOrder->tax_amount > 0)
                                                        <tr>
                                                            <td colspan="3" class="text-end">KDV:</td>
                                                            <td class="text-end">{{ number_format($selectedOrder->tax_amount, 0, ',', '.') }} TL</td>
                                                        </tr>
                                                    @endif
                                                    @if($selectedOrder->shipping_cost > 0)
                                                        <tr>
                                                            <td colspan="3" class="text-end">Kargo:</td>
                                                            <td class="text-end">{{ number_format($selectedOrder->shipping_cost, 0, ',', '.') }} TL</td>
                                                        </tr>
                                                    @endif
                                                    @if($selectedOrder->discount_amount > 0)
                                                        <tr class="text-success">
                                                            <td colspan="3" class="text-end">İndirim:</td>
                                                            <td class="text-end">-{{ number_format($selectedOrder->discount_amount, 0, ',', '.') }} TL</td>
                                                        </tr>
                                                    @endif
                                                    <tr class="table-active">
                                                        <td colspan="3" class="text-end"><h4 class="mb-0">Genel Toplam:</h4></td>
                                                        <td class="text-end"><h4 class="mb-0">{{ number_format($selectedOrder->total_amount, 0, ',', '.') }} TL</h4></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ödeme Geçmişi -->
                                @if($selectedOrder->payments && $selectedOrder->payments->count() > 0)
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-credit-card me-2"></i>Ödeme Geçmişi</h3>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Ödeme No</th>
                                                            <th>Yöntem</th>
                                                            <th class="text-end">Tutar</th>
                                                            <th class="text-center">Durum</th>
                                                            <th>Tarih</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($selectedOrder->payments as $payment)
                                                            <tr>
                                                                <td><code>{{ $payment->payment_number }}</code></td>
                                                                <td>{{ ucfirst($payment->gateway) }}</td>
                                                                <td class="text-end">{{ number_format($payment->amount, 0, ',', '.') }} TL</td>
                                                                <td class="text-center">
                                                                    @php
                                                                        $pColors = ['pending' => 'bg-yellow', 'completed' => 'bg-green', 'failed' => 'bg-red'];
                                                                        $pLabels = ['pending' => 'Bekliyor', 'completed' => 'Tamamlandı', 'failed' => 'Başarısız'];
                                                                    @endphp
                                                                    <span class="badge {{ $pColors[$payment->status] ?? 'bg-secondary' }}">
                                                                        {{ $pLabels[$payment->status] ?? $payment->status }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Sağ: Durum & İşlemler -->
                            <div class="col-md-4">
                                <!-- Durum Kartı -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-info-circle me-2"></i>Sipariş Durumu</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Sipariş Durumu</label>
                                            <select wire:model="newStatus" class="form-select">
                                                <option value="pending">Beklemede</option>
                                                <option value="processing">Hazırlanıyor</option>
                                                <option value="shipped">Kargoya Verildi</option>
                                                <option value="delivered">Teslim Edildi</option>
                                                <option value="completed">Tamamlandı</option>
                                                <option value="cancelled">İptal Edildi</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Ödeme Durumu</label>
                                            <div class="d-flex align-items-center gap-2">
                                                @php
                                                    $ps = $selectedOrder->payment_status;
                                                    $psColors = ['pending' => 'bg-yellow', 'paid' => 'bg-green', 'failed' => 'bg-red', 'refunded' => 'bg-orange'];
                                                    $psLabels = ['pending' => 'Ödeme Bekliyor', 'paid' => 'Ödendi', 'failed' => 'Başarısız', 'refunded' => 'İade'];
                                                @endphp
                                                <span class="badge {{ $psColors[$ps] ?? 'bg-secondary' }} fs-6">
                                                    {{ $psLabels[$ps] ?? $ps }}
                                                </span>
                                                @if($ps === 'pending')
                                                    <button wire:click="markAsPaid" class="btn btn-sm btn-success" onclick="return confirm('Ödendi olarak işaretlensin mi?')">
                                                        <i class="fas fa-check me-1"></i>Ödendi İşaretle
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Kargo Takip No</label>
                                            <input type="text" wire:model="trackingNumber" class="form-control" placeholder="Takip numarası girin">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Admin Notları</label>
                                            <textarea wire:model="adminNotes" class="form-control" rows="3" placeholder="Dahili notlar..."></textarea>
                                        </div>

                                        <button wire:click="updateOrderStatus" class="btn btn-primary w-100">
                                            <i class="fas fa-save me-1"></i>Değişiklikleri Kaydet
                                        </button>
                                    </div>
                                </div>

                                <!-- Tarihler -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-clock me-2"></i>Tarihler</h3>
                                    </div>
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex justify-content-between">
                                            <span>Oluşturulma:</span>
                                            <span>{{ $selectedOrder->created_at->format('d.m.Y H:i') }}</span>
                                        </div>
                                        @if($selectedOrder->confirmed_at)
                                            <div class="list-group-item d-flex justify-content-between">
                                                <span>Onaylandı:</span>
                                                <span>{{ $selectedOrder->confirmed_at->format('d.m.Y H:i') }}</span>
                                            </div>
                                        @endif
                                        @if($selectedOrder->shipped_at)
                                            <div class="list-group-item d-flex justify-content-between">
                                                <span>Kargoya Verildi:</span>
                                                <span>{{ $selectedOrder->shipped_at->format('d.m.Y H:i') }}</span>
                                            </div>
                                        @endif
                                        @if($selectedOrder->delivered_at)
                                            <div class="list-group-item d-flex justify-content-between">
                                                <span>Teslim Edildi:</span>
                                                <span>{{ $selectedOrder->delivered_at->format('d.m.Y H:i') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Müşteri Notları -->
                                @if($selectedOrder->customer_notes)
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-comment me-2"></i>Müşteri Notu</h3>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-0">{{ $selectedOrder->customer_notes }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- Navigation -->
                        @if($this->canGoPrevious())
                            <button type="button" class="btn btn-secondary me-auto" wire:click="previousOrder">
                                <i class="fas fa-arrow-left me-1"></i> Önceki
                            </button>
                        @endif

                        @if($this->canGoNext())
                            <button type="button" class="btn btn-secondary" wire:click="nextOrder">
                                Sonraki <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        @endif

                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
