{{-- helper.blade.php'den section'lar gelecek --}}
@include('payment::admin.helper')

<div>
    {{-- Page Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <span class="avatar avatar-lg bg-primary-lt">
                        <i class="fas fa-credit-card fa-lg"></i>
                    </span>
                </div>
                <div class="col">
                    <h2 class="page-title mb-1">Ödeme Yönetimi</h2>
                    <div class="text-muted">Tüm ödemeleri görüntüleyin ve yönetin</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Ara</label>
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="fas fa-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Ödeme no, işlem ID...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Durum</label>
                    <select wire:model.live="status" class="form-select">
                        <option value="">Tümü</option>
                        <option value="pending">Bekliyor</option>
                        <option value="processing">İşleniyor</option>
                        <option value="completed">Tamamlandı</option>
                        <option value="failed">Başarısız</option>
                        <option value="cancelled">İptal</option>
                        <option value="refunded">İade</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Ödeme Yöntemi</label>
                    <select wire:model.live="gateway" class="form-select">
                        <option value="">Tümü</option>
                        <option value="paytr">Kredi Kartı</option>
                        <option value="manual">Havale/EFT</option>
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    @if($search || $status || $gateway)
                        <button wire:click="$set('search', ''); $set('status', ''); $set('gateway', '')" class="btn btn-ghost-secondary">
                            <i class="fas fa-times me-1"></i> Temizle
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Payment List --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;"></th>
                        <th>Sipariş</th>
                        <th>Müşteri</th>
                        <th class="text-end">Tutar</th>
                        <th class="text-center">Yöntem</th>
                        <th class="text-center">Durum</th>
                        <th>Tarih</th>
                        <th style="width: 120px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    @php
                        $statusConfig = [
                            'pending' => ['color' => 'yellow', 'icon' => 'clock', 'label' => 'Bekliyor'],
                            'processing' => ['color' => 'blue', 'icon' => 'spinner fa-spin', 'label' => 'İşleniyor'],
                            'completed' => ['color' => 'green', 'icon' => 'check-circle', 'label' => 'Tamamlandı'],
                            'failed' => ['color' => 'red', 'icon' => 'times-circle', 'label' => 'Başarısız'],
                            'cancelled' => ['color' => 'secondary', 'icon' => 'ban', 'label' => 'İptal'],
                            'refunded' => ['color' => 'orange', 'icon' => 'undo', 'label' => 'İade'],
                        ];
                        $gatewayConfig = [
                            'paytr' => ['color' => 'purple', 'icon' => 'credit-card', 'label' => 'Kredi Kartı'],
                            'manual' => ['color' => 'cyan', 'icon' => 'building-columns', 'label' => 'Havale/EFT'],
                            'bank_transfer' => ['color' => 'cyan', 'icon' => 'building-columns', 'label' => 'Havale/EFT'],
                        ];
                        $sc = $statusConfig[$payment->status] ?? ['color' => 'secondary', 'icon' => 'question', 'label' => $payment->status];
                        $gc = $gatewayConfig[$payment->gateway] ?? ['color' => 'secondary', 'icon' => 'wallet', 'label' => $payment->gateway];
                    @endphp
                    <tr class="cursor-pointer" wire:click="viewPayment({{ $payment->payment_id }})">
                        <td>
                            <span class="avatar avatar-sm bg-{{ $sc['color'] }}-lt">
                                <i class="fas fa-{{ $sc['icon'] }} text-{{ $sc['color'] }}"></i>
                            </span>
                        </td>
                        <td>
                            <div class="font-weight-medium">{{ $payment->payable?->order_number ?? '#' . $payment->payable_id }}</div>
                            <div class="text-muted small">{{ $payment->payment_number }}</div>
                        </td>
                        <td>
                            @if($payment->payable?->customer_name)
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-xs me-2 bg-secondary-lt">
                                        {{ strtoupper(substr($payment->payable->customer_name, 0, 1)) }}
                                    </span>
                                    <span>{{ Str::limit($payment->payable->customer_name, 20) }}</span>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <span class="h4 mb-0">{{ number_format($payment->amount, 0, ',', '.') }}</span>
                            <span class="text-muted">₺</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $gc['color'] }}">
                                <i class="fas fa-{{ $gc['icon'] }} me-1"></i>{{ $gc['label'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $sc['color'] }}">{{ $sc['label'] }}</span>
                        </td>
                        <td>
                            <div class="small">{{ $payment->created_at->format('d.m.Y') }}</div>
                            <div class="text-muted small">{{ $payment->created_at->format('H:i') }}</div>
                        </td>
                        <td class="text-end" onclick="event.stopPropagation();">
                            @if($payment->status === 'pending')
                                <div class="btn-group">
                                    <button wire:click="markAsCompleted({{ $payment->payment_id }})"
                                            wire:confirm="Ödemeyi onaylamak istediğinizden emin misiniz?"
                                            class="btn btn-sm btn-success" title="Onayla">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button wire:click="markAsFailed({{ $payment->payment_id }})"
                                            wire:confirm="Ödemeyi reddetmek istediğinizden emin misiniz?"
                                            class="btn btn-sm btn-outline-danger" title="Reddet">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @else
                                <button wire:click="viewPayment({{ $payment->payment_id }})" class="btn btn-sm btn-ghost-primary">
                                    <i class="fas fa-eye me-1"></i> Detay
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="empty">
                                <div class="empty-icon">
                                    <i class="fas fa-inbox fa-3x text-muted"></i>
                                </div>
                                <p class="empty-title">Ödeme kaydı bulunamadı</p>
                                <p class="empty-subtitle text-muted">Henüz herhangi bir ödeme işlemi yapılmamış.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted">Toplam <strong>{{ $payments->total() }}</strong> ödeme</p>
            <div class="ms-auto">
                {{ $payments->links() }}
            </div>
        </div>
        @endif
    </div>

    {{-- ==================== PAYMENT DETAIL MODAL ==================== --}}
    @if($showModal && $selectedPayment)
    @php
        $order = $selectedPayment->payable;
        $user = $order?->user;
        $orderMetadata = $order?->metadata ?? [];
        $transferNote = $orderMetadata['transfer_note'] ?? null;
        $bankTransferConfirmedAt = $orderMetadata['bank_transfer_confirmed_at'] ?? null;

        $statusConfig = [
            'pending' => ['color' => 'yellow', 'icon' => 'clock', 'label' => 'Bekliyor', 'bg' => 'bg-yellow-lt'],
            'processing' => ['color' => 'blue', 'icon' => 'spinner', 'label' => 'İşleniyor', 'bg' => 'bg-blue-lt'],
            'completed' => ['color' => 'green', 'icon' => 'check-circle', 'label' => 'Tamamlandı', 'bg' => 'bg-green-lt'],
            'failed' => ['color' => 'red', 'icon' => 'times-circle', 'label' => 'Başarısız', 'bg' => 'bg-red-lt'],
            'cancelled' => ['color' => 'secondary', 'icon' => 'ban', 'label' => 'İptal', 'bg' => 'bg-secondary-lt'],
            'refunded' => ['color' => 'orange', 'icon' => 'undo', 'label' => 'İade', 'bg' => 'bg-orange-lt'],
        ];
        $sc = $statusConfig[$selectedPayment->status] ?? ['color' => 'secondary', 'icon' => 'question', 'label' => $selectedPayment->status, 'bg' => 'bg-secondary-lt'];
    @endphp
    <div class="modal modal-blur fade show" style="display: block; z-index: 10000;" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg">
                {{-- Modal Header --}}
                <div class="modal-header {{ $sc['bg'] }} border-0">
                    <div class="d-flex align-items-center gap-3">
                        <span class="avatar avatar-lg bg-white shadow-sm">
                            <i class="fas fa-{{ $sc['icon'] }} text-{{ $sc['color'] }} fa-lg"></i>
                        </span>
                        <div>
                            <h4 class="modal-title mb-0">{{ $selectedPayment->payment_number }}</h4>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge bg-{{ $sc['color'] }}">{{ $sc['label'] }}</span>
                                <span class="text-muted">{{ $selectedPayment->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <div class="modal-body p-0">
                    <div class="row g-0">
                        {{-- Sol Kolon: Ana Bilgiler --}}
                        <div class="col-lg-8 border-end">
                            {{-- Tutar Banner --}}
                            <div class="p-4 bg-dark text-white">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="text-white-50 small mb-1">Ödeme Tutarı</div>
                                        <div class="d-flex align-items-baseline gap-1">
                                            <span class="display-5 fw-bold">{{ number_format($selectedPayment->amount, 2, ',', '.') }}</span>
                                            <span class="fs-4 text-white-50">₺</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        @php
                                            $gw = $selectedPayment->gateway;
                                            $gwIcon = match($gw) {
                                                'paytr' => 'credit-card',
                                                'manual', 'bank_transfer' => 'building-columns',
                                                default => 'wallet'
                                            };
                                            $gwLabel = match($gw) {
                                                'paytr' => 'Kredi Kartı',
                                                'manual', 'bank_transfer' => 'Havale/EFT',
                                                default => ucfirst($gw)
                                            };
                                        @endphp
                                        <div class="text-center">
                                            <div class="avatar avatar-xl bg-white-lt mb-2">
                                                <i class="fas fa-{{ $gwIcon }} fa-lg"></i>
                                            </div>
                                            <div class="small">{{ $gwLabel }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Müşteri & Sipariş --}}
                            @if($order)
                            <div class="p-4">
                                {{-- Müşteri Kartı --}}
                                <div class="d-flex align-items-start gap-3 mb-4 pb-4 border-bottom">
                                    <span class="avatar avatar-lg bg-primary-lt">
                                        {{ strtoupper(substr($order->customer_name ?? 'M', 0, 1)) }}
                                    </span>
                                    <div class="flex-fill">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <h4 class="mb-0">{{ $order->customer_name }}</h4>
                                            @if($user)
                                                <span class="badge bg-green-lt text-green">Kayıtlı Üye</span>
                                            @else
                                                <span class="badge bg-secondary-lt text-secondary">Misafir</span>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-wrap gap-3 text-muted">
                                            @if($order->customer_email)
                                                <a href="mailto:{{ $order->customer_email }}" class="text-reset">
                                                    <i class="fas fa-envelope me-1"></i>{{ $order->customer_email }}
                                                </a>
                                            @endif
                                            @if($order->customer_phone)
                                                <a href="tel:{{ $order->customer_phone }}" class="text-reset">
                                                    <i class="fas fa-phone me-1"></i>{{ $order->customer_phone }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted small">Sipariş No</div>
                                        <div class="badge bg-primary fs-6">{{ $order->order_number }}</div>
                                    </div>
                                </div>

                                {{-- Adresler --}}
                                @if($order->shipping_address || $order->billing_address)
                                <div class="row g-3 mb-4">
                                    @if($order->shipping_address)
                                    @php $addr = is_array($order->shipping_address) ? $order->shipping_address : json_decode($order->shipping_address, true); @endphp
                                    <div class="col-md-6">
                                        <div class="card card-sm h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <i class="fas fa-truck text-blue"></i>
                                                    <span class="fw-medium">Teslimat Bilgileri</span>
                                                </div>
                                                <div class="text-muted small">
                                                    @if(!empty($addr['full_name']) || (!empty($addr['first_name']) && !empty($addr['last_name'])))
                                                        <div class="mb-1 fw-medium text-dark">{{ $addr['full_name'] ?? ($addr['first_name'] . ' ' . $addr['last_name']) }}</div>
                                                    @endif
                                                    @if(!empty($addr['phone']))
                                                        <div class="mb-1"><i class="fas fa-phone me-1"></i>{{ $addr['phone'] }}</div>
                                                    @endif
                                                    @if(!empty($addr['email']))
                                                        <div class="mb-1"><i class="fas fa-envelope me-1"></i>{{ $addr['email'] }}</div>
                                                    @endif
                                                    @if(!empty($addr['address_line_1']) || !empty($addr['city']) || !empty($addr['district']))
                                                        <div class="mt-2 pt-2 border-top">
                                                            @if(!empty($addr['address_line_1']))
                                                                {{ $addr['address_line_1'] }}<br>
                                                            @endif
                                                            @if(!empty($addr['district']) || !empty($addr['city']))
                                                                {{ $addr['district'] }}{{ !empty($addr['district']) && !empty($addr['city']) ? ', ' : '' }}{{ $addr['city'] }}
                                                            @endif
                                                            @if(!empty($addr['postal_code']))
                                                                <br>{{ $addr['postal_code'] }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($order->billing_address)
                                    @php $baddr = is_array($order->billing_address) ? $order->billing_address : json_decode($order->billing_address, true); @endphp
                                    <div class="col-md-6">
                                        <div class="card card-sm h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <i class="fas fa-file-invoice text-orange"></i>
                                                    <span class="fw-medium">Fatura Bilgileri</span>
                                                </div>
                                                <div class="text-muted small">
                                                    @if(!empty($baddr['full_name']) || (!empty($baddr['first_name']) && !empty($baddr['last_name'])))
                                                        <div class="mb-1 fw-medium text-dark">{{ $baddr['full_name'] ?? ($baddr['first_name'] . ' ' . $baddr['last_name']) }}</div>
                                                    @endif
                                                    @if(!empty($baddr['company_name']))
                                                        <div class="mb-1">{{ $baddr['company_name'] }}</div>
                                                    @endif
                                                    @if(!empty($baddr['phone']))
                                                        <div class="mb-1"><i class="fas fa-phone me-1"></i>{{ $baddr['phone'] }}</div>
                                                    @endif
                                                    @if(!empty($baddr['email']))
                                                        <div class="mb-1"><i class="fas fa-envelope me-1"></i>{{ $baddr['email'] }}</div>
                                                    @endif
                                                    @if(!empty($baddr['tax_office']) || !empty($baddr['tax_number']) || !empty($order->customer_tax_office) || !empty($order->customer_tax_number))
                                                        <div class="mt-2 pt-2 border-top">
                                                            @if(!empty($baddr['tax_office']) || !empty($order->customer_tax_office))
                                                                <strong>V.D:</strong> {{ $baddr['tax_office'] ?? $order->customer_tax_office }}
                                                            @endif
                                                            @if(!empty($baddr['tax_number']) || !empty($order->customer_tax_number))
                                                                @if(!empty($baddr['tax_office']) || !empty($order->customer_tax_office))<br>@endif
                                                                <strong>V.N:</strong> {{ $baddr['tax_number'] ?? $order->customer_tax_number }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @if(!empty($baddr['address_line_1']) || !empty($baddr['city']) || !empty($baddr['district']))
                                                        <div class="mt-2 pt-2 border-top">
                                                            @if(!empty($baddr['address_line_1']))
                                                                {{ $baddr['address_line_1'] }}<br>
                                                            @endif
                                                            @if(!empty($baddr['district']) || !empty($baddr['city']))
                                                                {{ $baddr['district'] }}{{ !empty($baddr['district']) && !empty($baddr['city']) ? ', ' : '' }}{{ $baddr['city'] }}
                                                            @endif
                                                            @if(!empty($baddr['postal_code']))
                                                                <br>{{ $baddr['postal_code'] }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                {{-- Sipariş Kalemleri --}}
                                @if($order->items && $order->items->count() > 0)
                                <div class="mb-3">
                                    <h5 class="mb-3">
                                        <i class="fas fa-shopping-bag me-2 text-muted"></i>
                                        Sipariş Kalemleri
                                        <span class="badge bg-secondary-lt ms-2">{{ $order->items->count() }}</span>
                                    </h5>
                                    <div class="list-group list-group-flush">
                                        @foreach($order->items as $item)
                                        @php
                                            // Ürün linki oluştur
                                            $productUrl = null;
                                            if ($item->orderable) {
                                                $orderable = $item->orderable;
                                                // ShopProduct için
                                                if (method_exists($orderable, 'getSlug') || isset($orderable->slug)) {
                                                    $slug = $orderable->slug ?? ($orderable->getSlug() ?? null);
                                                    // Slug array ise ilk değeri al
                                                    if (is_array($slug)) {
                                                        $slug = $slug[app()->getLocale()] ?? $slug['tr'] ?? reset($slug) ?? null;
                                                    }
                                                    if ($slug) {
                                                        $productUrl = url('/shop/' . $slug);
                                                    }
                                                }
                                                // SubscriptionPlan için admin link
                                                if (!$productUrl && str_contains(get_class($orderable), 'SubscriptionPlan')) {
                                                    $productUrl = route('admin.subscription.plans.manage', $orderable->plan_id ?? $orderable->id);
                                                }
                                            }
                                            // Image URL - array ise ilk değeri al
                                            $imageUrl = $item->item_image;
                                            if (is_array($imageUrl)) {
                                                $imageUrl = reset($imageUrl) ?: null;
                                            }
                                        @endphp
                                        <div class="list-group-item px-0">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    @if($imageUrl)
                                                        <span class="avatar" style="background-image: url({{ $imageUrl }})"></span>
                                                    @else
                                                        <span class="avatar bg-secondary-lt">
                                                            <i class="fas fa-box text-secondary"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col">
                                                    @if($productUrl)
                                                        <a href="{{ $productUrl }}" target="_blank" class="text-reset fw-medium d-flex align-items-center gap-1">
                                                            {{ $item->product_name }}
                                                            <i class="fas fa-external-link-alt text-muted small"></i>
                                                        </a>
                                                    @else
                                                        <span class="fw-medium">{{ $item->product_name }}</span>
                                                    @endif
                                                    <div class="text-muted small">
                                                        {{ number_format($item->unit_price, 2, ',', '.') }} ₺ x {{ $item->quantity }}
                                                        @if($item->is_digital)
                                                            <span class="badge bg-purple-lt text-purple ms-1">Dijital</span>
                                                        @endif
                                                    </div>
                                                    {{-- Kurumsal Üye Bilgisi --}}
                                                    @if(($item->metadata['type'] ?? null) === 'corporate_bulk' && !empty($item->metadata['target_user_ids']))
                                                        @php
                                                            $targetUserIds = $item->metadata['target_user_ids'];
                                                            $memberNames = \App\Models\User::whereIn('id', $targetUserIds)->pluck('name', 'id')->toArray();
                                                        @endphp
                                                        <div class="mt-2 p-2 bg-purple-lt rounded">
                                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                                <i class="fas fa-building text-purple"></i>
                                                                <span class="text-purple fw-medium small">Kurumsal Üyeler:</span>
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach($memberNames as $memberId => $memberName)
                                                                    <span class="badge bg-white text-dark shadow-sm">
                                                                        <i class="fas fa-user me-1"></i>{{ $memberName }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-auto">
                                                    <span class="fw-bold">{{ number_format($item->total, 2, ',', '.') }} ₺</span>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    {{-- Toplam --}}
                                    <div class="d-flex justify-content-end pt-3 border-top mt-3">
                                        <div class="text-end">
                                            @if($order->discount_amount > 0)
                                            <div class="text-muted small">
                                                İndirim: -{{ number_format($order->discount_amount, 2, ',', '.') }} ₺
                                            </div>
                                            @endif
                                            @if($order->tax_amount > 0)
                                            <div class="text-muted small">
                                                KDV: {{ number_format($order->tax_amount, 2, ',', '.') }} ₺
                                            </div>
                                            @endif
                                            <div class="h3 mb-0">
                                                Toplam: {{ number_format($order->total_amount, 2, ',', '.') }} ₺
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @else
                                <div class="p-4">
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Sipariş bulunamadı (ID: {{ $selectedPayment->payable_id }})
                                    </div>
                                </div>
                            @endif

                            {{-- Gateway Response (Collapsible) --}}
                            @if($selectedPayment->gateway_response)
                            <div class="border-top">
                                <div class="accordion" id="gatewayAccordion">
                                    <div class="accordion-item border-0">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed py-3 bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#gatewayCollapse">
                                                <i class="fas fa-code me-2"></i> Gateway Response (Teknik Detay)
                                            </button>
                                        </h2>
                                        <div id="gatewayCollapse" class="accordion-collapse collapse" data-bs-parent="#gatewayAccordion">
                                            <div class="accordion-body p-0">
                                                <pre class="bg-dark text-white p-3 m-0 small" style="max-height: 200px; overflow-y: auto;">{{ json_encode($selectedPayment->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Sağ Kolon: Sidebar --}}
                        <div class="col-lg-4 bg-light">
                            <div class="p-4">
                                {{-- Havale Bilgileri --}}
                                @if(in_array($selectedPayment->gateway, ['manual', 'bank_transfer']))
                                <div class="mb-4">
                                    <h5 class="d-flex align-items-center gap-2 mb-3">
                                        <i class="fas fa-building-columns text-cyan"></i>
                                        Havale/EFT Bilgileri
                                    </h5>
                                    <div class="card">
                                        <div class="card-body">
                                            @if($bankTransferConfirmedAt)
                                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                                <span class="text-muted">Bildirim:</span>
                                                <span class="badge bg-cyan">{{ \Carbon\Carbon::parse($bankTransferConfirmedAt)->format('d.m.Y H:i') }}</span>
                                            </div>
                                            @endif

                                            @if($transferNote)
                                            <div class="mb-3">
                                                <div class="text-muted small mb-1">Müşteri Notu:</div>
                                                <div class="p-2 bg-white rounded border-start border-3 border-cyan">
                                                    <i class="fas fa-quote-left text-muted me-1 small"></i>
                                                    {{ $transferNote }}
                                                </div>
                                            </div>
                                            @else
                                            <div class="text-muted small mb-3">
                                                <i class="fas fa-info-circle me-1"></i> Müşteri not bırakmamış
                                            </div>
                                            @endif

                                            <div class="text-muted small mb-2">Banka Hesapları:</div>
                                            @php
                                                $banks = [];
                                                for ($i = 1; $i <= 3; $i++) {
                                                    if (setting("payment_bank_{$i}_active") && setting("payment_bank_{$i}_iban")) {
                                                        $banks[] = ['name' => setting("payment_bank_{$i}_name"), 'iban' => setting("payment_bank_{$i}_iban")];
                                                    }
                                                }
                                            @endphp
                                            @forelse($banks as $bank)
                                            <div class="d-flex justify-content-between align-items-center py-1 small">
                                                <span>{{ $bank['name'] }}</span>
                                                <code class="text-muted">{{ Str::limit($bank['iban'], 12) }}</code>
                                            </div>
                                            @empty
                                            <div class="text-muted small">Kayıtlı banka yok</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- Dekont --}}
                                <div class="mb-4">
                                    <h5 class="d-flex align-items-center gap-2 mb-3">
                                        <i class="fas fa-file-image text-orange"></i>
                                        Dekont
                                    </h5>
                                    <div class="card">
                                        <div class="card-body">
                                            @if($selectedPayment->receipt_path)
                                                @php
                                                    $ext = strtolower(pathinfo($selectedPayment->receipt_path, PATHINFO_EXTENSION));
                                                    $isPdf = $ext === 'pdf';
                                                @endphp
                                                <div class="text-center mb-3">
                                                    @if($isPdf)
                                                        <a href="{{ Storage::url($selectedPayment->receipt_path) }}" target="_blank" class="d-block">
                                                            <div class="py-4 bg-white rounded border">
                                                                <i class="fas fa-file-pdf text-danger fa-3x"></i>
                                                                <div class="small text-muted mt-2">PDF Dekont</div>
                                                            </div>
                                                        </a>
                                                    @else
                                                        <a href="{{ Storage::url($selectedPayment->receipt_path) }}" target="_blank">
                                                            <img src="{{ Storage::url($selectedPayment->receipt_path) }}" alt="Dekont" class="img-fluid rounded border" style="max-height: 180px;">
                                                        </a>
                                                    @endif
                                                </div>
                                                @if($selectedPayment->receipt_uploaded_at)
                                                <div class="text-center text-muted small mb-3">
                                                    <i class="fas fa-clock me-1"></i>{{ $selectedPayment->receipt_uploaded_at->format('d.m.Y H:i') }}
                                                </div>
                                                @endif
                                                <div class="d-grid gap-2">
                                                    <a href="{{ Storage::url($selectedPayment->receipt_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-external-link me-1"></i> Görüntüle
                                                    </a>
                                                    <button wire:click="deleteReceipt" wire:confirm="Dekontu silmek istediğinizden emin misiniz?" class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-trash me-1"></i> Sil
                                                    </button>
                                                </div>
                                            @else
                                                <div class="text-center py-3 mb-3">
                                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                                    <div class="small text-muted">Dekont yüklenmemiş</div>
                                                </div>
                                                <input type="file" wire:model="receiptFile" class="form-control form-control-sm mb-2" accept=".jpg,.jpeg,.png,.pdf,.webp">
                                                @error('receiptFile') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                                                <div wire:loading wire:target="receiptFile" class="text-center py-2 small">
                                                    <span class="spinner-border spinner-border-sm me-1"></span> Yükleniyor...
                                                </div>
                                                @if($receiptFile)
                                                <button wire:click="uploadReceipt" wire:loading.attr="disabled" class="btn btn-success btn-sm w-100">
                                                    <i class="fas fa-upload me-1"></i> Kaydet
                                                </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Tarihler --}}
                                <div class="mb-4">
                                    <h5 class="d-flex align-items-center gap-2 mb-3">
                                        <i class="fas fa-history text-blue"></i>
                                        Tarihler
                                    </h5>
                                    <div class="card">
                                        <div class="list-group list-group-flush">
                                            <div class="list-group-item d-flex justify-content-between small">
                                                <span class="text-muted">Oluşturuldu</span>
                                                <span>{{ $selectedPayment->created_at->format('d.m.Y H:i') }}</span>
                                            </div>
                                            @if($selectedPayment->paid_at)
                                            <div class="list-group-item d-flex justify-content-between small">
                                                <span class="text-success"><i class="fas fa-check-circle me-1"></i>Ödendi</span>
                                                <span>{{ $selectedPayment->paid_at->format('d.m.Y H:i') }}</span>
                                            </div>
                                            @endif
                                            @if($selectedPayment->failed_at)
                                            <div class="list-group-item d-flex justify-content-between small">
                                                <span class="text-danger"><i class="fas fa-times-circle me-1"></i>Başarısız</span>
                                                <span>{{ $selectedPayment->failed_at->format('d.m.Y H:i') }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Notlar --}}
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h5 class="d-flex align-items-center gap-2 mb-0">
                                            <i class="fas fa-sticky-note text-yellow"></i>
                                            Notlar
                                        </h5>
                                        @if(!$editingNotes)
                                        <button wire:click="toggleEditNotes" class="btn btn-ghost-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                    <div class="card">
                                        <div class="card-body">
                                            @if($editingNotes)
                                                <textarea wire:model="notes" class="form-control form-control-sm mb-2" rows="4" placeholder="Not ekleyin..."></textarea>
                                                <div class="d-flex gap-2">
                                                    <button wire:click="saveNotes" class="btn btn-success btn-sm flex-fill">
                                                        <i class="fas fa-save me-1"></i> Kaydet
                                                    </button>
                                                    <button wire:click="cancelEditNotes" class="btn btn-secondary btn-sm">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            @else
                                                @if($selectedPayment->notes)
                                                    <div class="small" style="white-space: pre-line;">{{ $selectedPayment->notes }}</div>
                                                @else
                                                    <div class="text-muted small text-center py-2">Not eklenmemiş</div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer">
                    <div class="d-flex align-items-center gap-2 me-auto">
                        <button wire:click="previousPayment" @if(!$this->canGoPrevious()) disabled @endif class="btn btn-ghost-secondary btn-sm">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button wire:click="nextPayment" @if(!$this->canGoNext()) disabled @endif class="btn btn-ghost-secondary btn-sm">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <span class="text-muted small">← → ile gezin</span>
                    </div>

                    @if($selectedPayment->status === 'pending')
                        <button wire:click="markAsCompleted({{ $selectedPayment->payment_id }})" wire:confirm="Ödemeyi onaylamak istediğinizden emin misiniz?" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> Onayla
                        </button>
                        <button wire:click="markAsFailed({{ $selectedPayment->payment_id }})" wire:confirm="Ödemeyi reddetmek istediğinizden emin misiniz?" class="btn btn-outline-danger">
                            <i class="fas fa-times me-1"></i> Reddet
                        </button>
                    @endif
                    <button wire:click="closeModal" class="btn btn-secondary">Kapat</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
    @endif

    <style>
    .cursor-pointer { cursor: pointer; }
    .cursor-pointer:hover { background-color: rgba(var(--tblr-primary-rgb), 0.02); }
    </style>
</div>
