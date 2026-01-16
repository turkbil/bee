@php
    $order = $payment->payable;
    $user = $order?->user; // Ödemeyi yapan kişi
    $orderMetadata = $order?->metadata ?? [];
    $transferNote = $orderMetadata['transfer_note'] ?? null;
    $billingAddress = $order?->billing_address ?? [];
    $orderItems = $order?->items ?? collect();

    // Kurumsal toplu ödeme kontrolü
    $isCorporateBulk = ($orderMetadata['type'] ?? null) === 'corporate_bulk';
    $selectedUserIds = $orderMetadata['selected_user_ids'] ?? [];
    $corporateTargetUsers = collect();
    if ($isCorporateBulk && !empty($selectedUserIds)) {
        $corporateTargetUsers = \App\Models\User::whereIn('id', $selectedUserIds)->get();
    }

    // Durum konfigürasyonu
    $statusConfig = [
        'pending' => ['color' => 'warning', 'label' => 'Bekliyor', 'icon' => 'clock'],
        'processing' => ['color' => 'info', 'label' => 'İşleniyor', 'icon' => 'spinner fa-spin'],
        'completed' => ['color' => 'success', 'label' => 'Tamamlandı', 'icon' => 'check-circle'],
        'failed' => ['color' => 'danger', 'label' => 'Başarısız', 'icon' => 'times-circle'],
        'cancelled' => ['color' => 'secondary', 'label' => 'İptal', 'icon' => 'ban'],
        'refunded' => ['color' => 'orange', 'label' => 'İade', 'icon' => 'undo'],
    ];
    $sc = $statusConfig[$payment->status] ?? ['color' => 'secondary', 'label' => $payment->status, 'icon' => 'question'];

    // Gateway konfigürasyonu
    $gatewayConfig = [
        'paytr' => ['icon' => 'credit-card', 'label' => 'Kredi Kartı'],
        'manual' => ['icon' => 'building-columns', 'label' => 'Havale/EFT'],
        'bank_transfer' => ['icon' => 'building-columns', 'label' => 'Havale/EFT'],
    ];
    $gc = $gatewayConfig[$payment->gateway] ?? ['icon' => 'wallet', 'label' => $payment->gateway ?? 'Bilinmiyor'];

    // Kurumsal mı bireysel mi
    $isCorporate = !empty($order->customer_company);
    $taxNumber = $order->customer_tax_number ?? '';
    $taxNumberLength = strlen(preg_replace('/[^0-9]/', '', $taxNumber));
    if (!$isCorporate && $taxNumberLength == 10) $isCorporate = true;

    $customerType = $isCorporate ? 'Kurumsal' : 'Bireysel';
    $taxLabel = $isCorporate ? 'VKN' : 'TCKN';

    // Telefon formatlama
    $formatPhone = function($phone) {
        if (empty($phone)) return null;
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($digits, '90') && strlen($digits) == 12) $digits = substr($digits, 2);
        if (str_starts_with($digits, '0')) $digits = substr($digits, 1);
        if (strlen($digits) == 10) {
            return '+90 ' . substr($digits, 0, 3) . ' ' . substr($digits, 3, 3) . ' ' . substr($digits, 6, 2) . ' ' . substr($digits, 8, 2);
        }
        return $phone;
    };
    $phoneRaw = function($phone) {
        if (empty($phone)) return null;
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($digits, '0')) $digits = substr($digits, 1);
        return strlen($digits) == 10 ? '+90' . $digits : $phone;
    };
@endphp

{{-- BAŞLIK: Üye Bilgileri + Tutar --}}
<div class="card bg-primary text-white mb-4">
    <div class="card-body py-4">
        <div class="row align-items-center">
            {{-- Sol: Üye Bilgileri --}}
            <div class="col-lg-6">
                @if($user)
                {{-- Kurumsal toplu ödeme ise başlık göster --}}
                @if($isCorporateBulk)
                <div class="text-white-50 small mb-2"><i class="fas fa-credit-card me-1"></i>Ödemeyi Yapan</div>
                @endif
                <div class="d-flex align-items-center">
                    @if($user->getFirstMediaUrl('avatar'))
                        <img src="{{ $user->getFirstMediaUrl('avatar') }}" class="avatar avatar-lg rounded-circle me-3 border border-2 border-white" alt="">
                    @else
                        <span class="avatar avatar-lg rounded-circle bg-white text-primary me-3 fs-3">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <strong class="fs-4">{{ $user->name }} {{ $user->surname }}</strong>
                            <span class="badge bg-white text-primary ms-2">#{{ $user->id }}</span>
                            <button type="button" class="btn btn-sm text-white ms-2 opacity-75" onclick="copyText('{{ e($user->name . ' ' . $user->surname) }}')"><i class="fas fa-copy"></i></button>
                        </div>
                        <div class="d-flex align-items-center text-white-50 small mb-1">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:{{ $user->email }}" class="text-white-50">{{ $user->email }}</a>
                            <button type="button" class="btn btn-sm text-white ms-1 opacity-50" onclick="copyText('{{ $user->email }}')"><i class="fas fa-copy fa-xs"></i></button>
                        </div>
                        @if($user->phone)
                        <div class="d-flex align-items-center text-white-50 small">
                            <i class="fas fa-phone me-2"></i>
                            <a href="tel:{{ $phoneRaw($user->phone) }}" class="text-white-50">{{ $formatPhone($user->phone) }}</a>
                            <button type="button" class="btn btn-sm text-white ms-1 opacity-50" onclick="copyText('{{ $phoneRaw($user->phone) }}')"><i class="fas fa-copy fa-xs"></i></button>
                        </div>
                        @endif
                    </div>
                </div>
                @if($user->corporate_account_id || ($user->subscription_expires_at && $user->subscription_expires_at->isFuture()))
                <div class="mt-2">
                    @if($user->corporate_account_id)
                        <span class="badge bg-purple"><i class="fas fa-building me-1"></i>Kurumsal</span>
                    @endif
                    @if($user->subscription_expires_at && $user->subscription_expires_at->isFuture())
                        <span class="badge bg-success"><i class="fas fa-crown me-1"></i>Premium</span>
                    @endif
                </div>
                @endif
                @else
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-lg rounded-circle bg-white-50 text-white me-3 fs-3"><i class="fas fa-user-slash"></i></span>
                    <div>
                        <strong class="fs-4">Misafir</strong>
                        <div class="text-white-50 small">Kayıtlı üye değil</div>
                    </div>
                </div>
                @endif
            </div>
            {{-- Sağ: Tutar ve Durum --}}
            <div class="col-lg-6 text-lg-end text-center mt-3 mt-lg-0">
                <div class="mb-2">
                    <span class="badge bg-white text-primary me-1">
                        <i class="fas fa-{{ $gc['icon'] }} me-1"></i>{{ $gc['label'] }}
                    </span>
                    <span class="badge bg-{{ $sc['color'] }}">
                        <i class="fas fa-{{ $sc['icon'] }} me-1"></i>{{ $sc['label'] }}
                    </span>
                </div>
                <div class="display-5 fw-bold">{{ number_format($payment->amount, 2, ',', '.') }} ₺</div>
                @if($payment->paid_at)
                <div class="mt-1 text-white-50 small">
                    <i class="fas fa-check-circle me-1"></i>{{ $payment->paid_at->format('d M Y, H:i') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- KURUMSAL TOPLU ÖDEME: Abonelik Verilen Kişiler --}}
@if($isCorporateBulk && $corporateTargetUsers->count() > 0)
<div class="card bg-purple-lt mb-4">
    <div class="card-header">
        <h4 class="card-title mb-0">
            <i class="fas fa-users me-2 text-purple"></i>Abonelik Verilen Kişiler
            <span class="badge bg-purple ms-2">{{ $corporateTargetUsers->count() }} kişi</span>
        </h4>
    </div>
    <div class="card-body">
        <div class="row g-2">
            @foreach($corporateTargetUsers as $targetUser)
            <div class="col-md-6">
                <div class="d-flex align-items-center p-2 rounded bg-white">
                    @if($targetUser->getFirstMediaUrl('avatar'))
                        <img src="{{ $targetUser->getFirstMediaUrl('avatar') }}" class="avatar avatar-sm rounded-circle me-2" alt="">
                    @else
                        <span class="avatar avatar-sm rounded-circle bg-purple-lt me-2">{{ strtoupper(substr($targetUser->name, 0, 1)) }}</span>
                    @endif
                    <div class="flex-fill">
                        <div class="fw-bold small">{{ $targetUser->name }} {{ $targetUser->surname }} <span class="text-muted fw-normal">#{{ $targetUser->id }}</span></div>
                        <div class="text-muted small">{{ $targetUser->email }}</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-ghost-primary" onclick="copyText('{{ $targetUser->email }}')"><i class="fas fa-copy"></i></button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="row">
    {{-- SOL KOLON --}}
    <div class="col-lg-6">
        {{-- FATURA İLETİŞİM (Checkout'tan gelen) --}}
        @if($order)
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-address-card me-2 text-cyan"></i>Fatura İletişim
                </h4>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width:100px">Ad Soyad</td>
                            <td>
                                <strong>{{ $order->customer_name ?: '-' }}</strong>
                                @if($order->customer_name)
                                <button type="button" class="btn btn-sm btn-ghost-primary ms-1" onclick="copyText('{{ e($order->customer_name) }}')"><i class="fas fa-copy"></i></button>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">E-posta</td>
                            <td>
                                @if($order->customer_email)
                                <a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a>
                                <button type="button" class="btn btn-sm btn-ghost-primary ms-1" onclick="copyText('{{ $order->customer_email }}')"><i class="fas fa-copy"></i></button>
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Telefon</td>
                            <td>
                                @if($order->customer_phone)
                                <a href="tel:{{ $phoneRaw($order->customer_phone) }}">{{ $formatPhone($order->customer_phone) }}</a>
                                <button type="button" class="btn btn-sm btn-ghost-primary ms-1" onclick="copyText('{{ $phoneRaw($order->customer_phone) }}')"><i class="fas fa-copy"></i></button>
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-muted small mt-2">
                    <i class="fas fa-info-circle me-1"></i>Checkout sayfasında girilen bilgiler
                </div>
            </div>
        </div>
        @endif

        {{-- FATURA BİLGİLERİ --}}
        @if($order)
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <h4 class="card-title mb-0">
                    <i class="fas fa-file-invoice me-2 text-primary"></i>Fatura Bilgileri
                </h4>
                <span class="badge bg-{{ $isCorporate ? 'purple' : 'cyan' }} ms-auto">
                    <i class="fas fa-{{ $isCorporate ? 'building' : 'user' }} me-1"></i>{{ $customerType }}
                </span>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tbody>
                        @if($isCorporate && $order->customer_company)
                        <tr>
                            <td class="text-muted" style="width:140px">Firma</td>
                            <td>
                                <strong>{{ $order->customer_company }}</strong>
                                <button type="button" class="btn btn-sm btn-ghost-primary ms-2" onclick="copyText('{{ e($order->customer_company) }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
                        </tr>
                        @endif

                        @if($order->customer_tax_office)
                        <tr>
                            <td class="text-muted">Vergi Dairesi</td>
                            <td>
                                {{ $order->customer_tax_office }}
                                <button type="button" class="btn btn-sm btn-ghost-primary ms-2" onclick="copyText('{{ e($order->customer_tax_office) }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
                        </tr>
                        @endif

                        @if($order->customer_tax_number)
                        <tr>
                            <td class="text-muted">{{ $taxLabel }}</td>
                            <td>
                                <code class="fs-5">{{ $order->customer_tax_number }}</code>
                                <button type="button" class="btn btn-sm btn-ghost-primary ms-2" onclick="copyText('{{ $order->customer_tax_number }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>

                @if(!$order->customer_company && !$order->customer_tax_office && !$order->customer_tax_number)
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Vergi bilgisi girilmemiş
                </div>
                @endif

                {{-- Fatura Adresi --}}
                <hr>
                <h5 class="text-muted mb-3"><i class="fas fa-map-marker-alt me-2"></i>Fatura Adresi</h5>
                @if(!empty($billingAddress) && !empty($billingAddress['address_line_1']))
                @php
                    $fullAddress = $billingAddress['address_line_1'];
                    if (!empty($billingAddress['district'])) $fullAddress .= ', ' . $billingAddress['district'];
                    if (!empty($billingAddress['city'])) $fullAddress .= ', ' . $billingAddress['city'];
                    if (!empty($billingAddress['postal_code'])) $fullAddress .= ' ' . $billingAddress['postal_code'];
                @endphp
                <div class="d-flex align-items-start">
                    <div class="flex-fill">
                        {{ $billingAddress['address_line_1'] }}<br>
                        @if(!empty($billingAddress['district'])){{ $billingAddress['district'] }}, @endif
                        {{ $billingAddress['city'] ?? '' }}
                        @if(!empty($billingAddress['postal_code'])) {{ $billingAddress['postal_code'] }}@endif
                    </div>
                    <button type="button" class="btn btn-sm btn-ghost-primary ms-2" onclick="copyText('{{ e($fullAddress) }}')">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                @else
                <div class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Fatura adresi girilmemiş</div>
                @endif
            </div>
        </div>
        @endif

        {{-- NOTLAR --}}
        @if($payment->notes || $transferNote)
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-sticky-note me-2 text-warning"></i>Notlar
                </h4>
            </div>
            <div class="card-body">
                @if($payment->notes)
                <div style="white-space: pre-line;">{{ $payment->notes }}</div>
                @endif
                @if($transferNote)
                <div class="alert alert-info mt-3 mb-0">
                    <strong><i class="fas fa-comment me-2"></i>Müşteri Notu:</strong><br>
                    {{ $transferNote }}
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- SAĞ KOLON --}}
    <div class="col-lg-6">
        {{-- SİPARİŞ --}}
        @if($order)
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title mb-0">
                    <i class="fas fa-shopping-bag me-2 text-success"></i>Sipariş
                </h4>
                @if($order->customer_email)
                <a href="{{ route('admin.orders.index') }}?search={{ urlencode($order->customer_email) }}"
                   target="_blank"
                   class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-external-link-alt me-1"></i>Tüm Siparişleri Gör
                </a>
                @endif
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width:140px">Sipariş No</td>
                            <td>
                                <code>{{ $order->order_number }}</code>
                                <button type="button" class="btn btn-sm btn-ghost-primary ms-2" onclick="copyText('{{ $order->order_number }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tarih</td>
                            <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Ürünler --}}
                @if($orderItems->count() > 0)
                <hr>
                <h5 class="text-muted mb-3">ÜRÜNLER</h5>
                @foreach($orderItems as $item)
                <div class="d-flex justify-content-between py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <span>{{ $item->product_name ?? $item->name ?? 'Ürün' }} <small class="text-muted">x{{ $item->quantity ?? 1 }}</small></span>
                    <strong>{{ number_format($item->total ?? $item->price ?? 0, 2, ',', '.') }} ₺</strong>
                </div>
                @endforeach
                @endif

                {{-- Toplam --}}
                <hr>
                @if($order->subtotal && $order->subtotal != $order->total_amount)
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Ara Toplam</span>
                    <span>{{ number_format($order->subtotal, 2, ',', '.') }} ₺</span>
                </div>
                @endif
                @if($order->tax_amount && $order->tax_amount > 0)
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">KDV (%20)</span>
                    <span>{{ number_format($order->tax_amount, 2, ',', '.') }} ₺</span>
                </div>
                @endif
                @if($order->discount_amount && $order->discount_amount > 0)
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted text-success">İndirim</span>
                    <span class="text-success">-{{ number_format($order->discount_amount, 2, ',', '.') }} ₺</span>
                </div>
                @endif
                <div class="d-flex justify-content-between py-2">
                    <strong class="fs-4">Toplam</strong>
                    <strong class="fs-4 text-primary">{{ number_format($order->total_amount, 2, ',', '.') }} ₺</strong>
                </div>
            </div>
        </div>
        @endif

        {{-- ÖDEME --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-credit-card me-2 text-purple"></i>Ödeme
                </h4>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width:140px">Ödeme No</td>
                            <td>
                                <code>{{ $payment->payment_number }}</code>
                                <button type="button" class="btn btn-sm btn-ghost-primary ms-2" onclick="copyText('{{ $payment->payment_number }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
                        </tr>

                        @if($payment->gateway_transaction_id)
                        <tr>
                            <td class="text-muted">İşlem ID</td>
                            <td>
                                <code class="small">{{ $payment->gateway_transaction_id }}</code>
                                <button type="button" class="btn btn-sm btn-ghost-primary ms-2" onclick="copyText('{{ $payment->gateway_transaction_id }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
                        </tr>
                        @endif

                        <tr>
                            <td class="text-muted">Yöntem</td>
                            <td><i class="fas fa-{{ $gc['icon'] }} me-2 text-muted"></i>{{ $gc['label'] }}</td>
                        </tr>

                        @if($payment->card_last_four)
                        <tr>
                            <td class="text-muted">Kart</td>
                            <td>**** {{ $payment->card_last_four }}</td>
                        </tr>
                        @endif

                        @if($payment->installment_count && $payment->installment_count > 1)
                        <tr>
                            <td class="text-muted">Taksit</td>
                            <td><span class="badge bg-warning">{{ $payment->installment_count }} Taksit</span></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- DEKONT --}}
        @if($payment->receipt_path)
        <div class="card mb-4">
            <div class="card-header bg-info-lt">
                <h4 class="card-title mb-0 text-info">
                    <i class="fas fa-receipt me-2"></i>Müşteri Dekontu
                </h4>
            </div>
            <div class="card-body text-center">
                @php $isReceiptImage = Str::endsWith(strtolower($payment->receipt_path), ['.jpg', '.jpeg', '.png', '.webp', '.gif']); @endphp
                @if($isReceiptImage)
                    <img src="{{ asset('storage/' . $payment->receipt_path) }}" alt="Dekont" class="img-fluid rounded mb-3" style="max-height: 200px;">
                @else
                    <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                @endif
                <div class="btn-list justify-content-center">
                    <a href="{{ asset('storage/' . $payment->receipt_path) }}" target="_blank" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i>Görüntüle
                    </a>
                    <a href="{{ asset('storage/' . $payment->receipt_path) }}" download class="btn btn-outline-info">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- FATURA YÜKLE --}}
        <div class="card">
            <div class="card-header bg-success-lt d-flex align-items-center">
                <h4 class="card-title mb-0 text-success">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Fatura Dosyası
                </h4>
                @if($payment->invoice_path)
                    <span class="badge bg-success ms-auto"><i class="fas fa-check me-1"></i>Yüklendi</span>
                @else
                    <span class="badge bg-warning ms-auto">Bekliyor</span>
                @endif
            </div>
            <div class="card-body">
                @if($payment->invoice_path)
                    <div class="text-center" id="invoicePreview-{{ $payment->payment_id }}">
                        @php $isInvoiceImage = Str::endsWith(strtolower($payment->invoice_path), ['.jpg', '.jpeg', '.png', '.webp', '.gif']); @endphp
                        @if($isInvoiceImage)
                            <img src="{{ asset('storage/' . $payment->invoice_path) }}" alt="Fatura" class="img-fluid rounded mb-3" style="max-height: 200px;">
                        @else
                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                            <div class="mb-2 text-muted">{{ basename($payment->invoice_path) }}</div>
                        @endif
                        @if($payment->invoice_uploaded_at)
                            <div class="text-muted small mb-3">{{ $payment->invoice_uploaded_at->format('d.m.Y H:i') }}</div>
                        @endif
                        <div class="btn-list justify-content-center mb-3">
                            <a href="{{ asset('storage/' . $payment->invoice_path) }}" target="_blank" class="btn btn-success">
                                <i class="fas fa-eye me-1"></i>Görüntüle
                            </a>
                            <a href="{{ asset('storage/' . $payment->invoice_path) }}" download class="btn btn-outline-success">
                                <i class="fas fa-download"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger" onclick="deleteInvoice({{ $payment->payment_id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <button type="button" class="btn btn-ghost-secondary btn-sm" onclick="showUploadArea({{ $payment->payment_id }})">
                            <i class="fas fa-sync me-1"></i>Değiştir
                        </button>
                    </div>
                @endif

                <div id="invoiceUploadArea-{{ $payment->payment_id }}" class="{{ $payment->invoice_path ? 'd-none' : '' }}">
                    <div class="card card-body text-center p-4"
                         id="dropzone-{{ $payment->payment_id }}"
                         ondrop="handleDrop(event, {{ $payment->payment_id }})"
                         ondragover="handleDragOver(event)"
                         ondragleave="handleDragLeave(event)"
                         style="border: 2px dashed var(--tblr-success); cursor: pointer;">
                        <div id="dropzoneContent-{{ $payment->payment_id }}">
                            <i class="fas fa-cloud-upload-alt fa-3x text-success mb-3"></i>
                            <div class="mb-2"><strong>Sürükle & Bırak</strong></div>
                            <div class="text-muted mb-3">veya</div>
                            <label for="invoiceInput-{{ $payment->payment_id }}" class="btn btn-success">
                                <i class="fas fa-folder-open me-1"></i>Dosya Seç
                            </label>
                            <input type="file" id="invoiceInput-{{ $payment->payment_id }}" accept=".pdf,.jpg,.jpeg,.png,.webp" class="d-none" onchange="handleSelect(event, {{ $payment->payment_id }})">
                            <div class="text-muted small mt-3">PDF, JPG, PNG (Max 10MB)</div>
                        </div>
                        <div id="dropzoneLoading-{{ $payment->payment_id }}" class="d-none py-3">
                            <div class="spinner-border text-success mb-2"></div>
                            <div>Yükleniyor...</div>
                            <div class="progress mt-3 mx-auto" style="height: 6px; max-width: 200px;">
                                <div id="uploadProgress-{{ $payment->payment_id }}" class="progress-bar bg-success" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
