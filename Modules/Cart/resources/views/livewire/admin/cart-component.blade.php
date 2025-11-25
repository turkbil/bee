@include('cart::admin.helper')

<div class="cart-component-wrapper">
    <div class="card">
        <div class="card-body p-0">
            <!-- Header Bölümü -->
            <div class="row mx-2 my-3">
                <!-- Arama Kutusu -->
                <div class="col-md-4">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control"
                            placeholder="{{ __('cart::admin.search_placeholder') }}">
                    </div>
                </div>
                <!-- Status Filter -->
                <div class="col-md-3">
                    <select wire:model.live="status" class="form-select">
                        <option value="">{{ __('cart::admin.all_statuses') }}</option>
                        @foreach($statuses as $statusOption)
                            <option value="{{ $statusOption }}">{{ __('cart::admin.status_' . $statusOption) }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="render, search, status, perPage, gotoPage, previousPage, nextPage"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">{{ __('admin.updating') }}</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                <!-- Sağ Taraf (Per Page Select) -->
                <div class="col-md-2">
                    <div style="width: 80px; min-width: 80px">
                        <select wire:model.live="perPage" class="form-control listing-filter-select" data-choices
                            data-choices-search="false" data-choices-filter="true">
                            <option value="25"><nobr>25</nobr></option>
                            <option value="50"><nobr>50</nobr></option>
                            <option value="100"><nobr>100</nobr></option>
                            <option value="500"><nobr>500</nobr></option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tablo Bölümü -->
            @if($carts->isEmpty())
                <div class="empty">
                    <div class="empty-icon">
                        <i class="fas fa-shopping-cart fa-3x text-muted"></i>
                    </div>
                    <p class="empty-title">{{ __('cart::admin.no_carts_found') }}</p>
                    <p class="empty-subtitle text-muted">
                        {{ __('cart::admin.no_carts_description') }}
                    </p>
                </div>
            @else
                <div id="table-default" class="table-responsive">
                    <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                        <thead>
                            <tr>
                                <th style="width: 100px">{{ __('cart::admin.cart_id') }}</th>
                                <th>{{ __('cart::admin.customer_session') }}</th>
                                <th class="text-center" style="width: 120px">{{ __('cart::admin.items_count') }}</th>
                                <th class="text-end" style="width: 140px">{{ __('cart::admin.total') }}</th>
                                <th class="text-center" style="width: 120px">{{ __('cart::admin.status') }}</th>
                                <th style="width: 160px">{{ __('cart::admin.last_activity') }}</th>
                                <th class="text-center" style="width: 100px">{{ __('admin.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="table-tbody">
                            @forelse($carts as $cart)
                                <tr class="hover-trigger" wire:key="cart-{{ $cart->cart_id }}">
                                    <td class="small">
                                        <strong>#{{ $cart->cart_id }}</strong>
                                    </td>
                                    <td>
                                        @if($cart->customer_id)
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user text-blue me-2"></i>
                                                <span>{{ __('cart::admin.customer') }} #{{ $cart->customer_id }}</span>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-secret text-muted me-2"></i>
                                                <div>
                                                    <span class="text-muted">{{ __('cart::admin.guest') }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($cart->session_id, 20) }}</small>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-azure">{{ $cart->items_count }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ number_format($cart->total, 2) }} {{ $cart->currency_code }}</strong>
                                        @if($cart->discount_amount > 0)
                                            <br>
                                            <small class="text-success">-{{ number_format($cart->discount_amount, 2) }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @switch($cart->status)
                                            @case('active')
                                                <span class="badge bg-success">{{ __('cart::admin.status_active') }}</span>
                                                @break
                                            @case('abandoned')
                                                <span class="badge bg-warning">{{ __('cart::admin.status_abandoned') }}</span>
                                                @break
                                            @case('converted')
                                                <span class="badge bg-info">{{ __('cart::admin.status_converted') }}</span>
                                                @break
                                            @case('merged')
                                                <span class="badge bg-secondary">{{ __('cart::admin.status_merged') }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($cart->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($cart->last_activity_at)
                                            <div class="text-muted">{{ $cart->last_activity_at->diffForHumans() }}</div>
                                            <small class="text-muted">{{ $cart->last_activity_at->format('d.m.Y H:i') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="viewCart({{ $cart->cart_id }})" class="btn btn-sm btn-primary" title="{{ __('cart::admin.view_cart') }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        {{ __('cart::admin.no_carts_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($carts->hasPages())
                        <div class="card-footer d-flex align-items-center">
                            <p class="m-0 text-muted">
                                {{ __('admin.showing') }}
                                <span>{{ $carts->firstItem() }}</span>
                                {{ __('admin.to') }}
                                <span>{{ $carts->lastItem() }}</span>
                                {{ __('admin.of') }}
                                <span>{{ $carts->total() }}</span>
                                {{ __('admin.entries') }}
                            </p>
                            <ul class="pagination m-0 ms-auto">
                                {{ $carts->onEachSide(1)->links() }}
                            </ul>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Cart Detail Modal -->
    @if($showModal && $selectedCart)
        <div class="modal modal-blur fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Sepet Detayları #{{ $selectedCart->cart_id }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Cart Info -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Müşteri:</strong>
                                @if($selectedCart->customer_id)
                                    <i class="fas fa-user text-blue me-1"></i> Müşteri #{{ $selectedCart->customer_id }}
                                @else
                                    <i class="fas fa-user-secret text-muted me-1"></i> Misafir
                                @endif
                            </div>
                            <div class="col-md-6">
                                <strong>Durum:</strong>
                                @switch($selectedCart->status)
                                    @case('active')
                                        <span class="badge bg-success">Aktif</span>
                                        @break
                                    @case('abandoned')
                                        <span class="badge bg-warning">Terk Edildi</span>
                                        @break
                                    @case('converted')
                                        <span class="badge bg-info">Siparişe Dönüştü</span>
                                        @break
                                    @case('merged')
                                        <span class="badge bg-secondary">Birleştirildi</span>
                                        @break
                                @endswitch
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Oluşturulma:</strong> {{ $selectedCart->created_at->format('d.m.Y H:i') }}
                            </div>
                            <div class="col-md-6">
                                <strong>Son Aktivite:</strong> {{ $selectedCart->last_activity_at ? $selectedCart->last_activity_at->format('d.m.Y H:i') : '-' }}
                            </div>
                        </div>

                        @if($selectedCart->session_id)
                            <div class="mb-3">
                                <strong>Session ID:</strong> <code>{{ $selectedCart->session_id }}</code>
                            </div>
                        @endif

                        @if($selectedCart->ip_address)
                            <div class="mb-3">
                                <strong>IP Adresi:</strong> {{ $selectedCart->ip_address }}
                            </div>
                        @endif

                        <hr>

                        <!-- Cart Items -->
                        <h5 class="mb-3">Sepetteki Ürünler ({{ $selectedCart->items->count() }})</h5>

                        @if($selectedCart->items->isEmpty())
                            <div class="alert alert-warning">
                                Bu sepet boş.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Ürün</th>
                                            <th class="text-end">Birim Fiyat</th>
                                            <th class="text-center">Adet</th>
                                            <th class="text-end">Toplam</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($selectedCart->items as $item)
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
                                                            <strong>{{ $item->item_name }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ class_basename($item->cartable_type) }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    {{ number_format($item->unit_price, 2) }} {{ $selectedCart->currency_code }}
                                                    @if($item->discount_amount > 0)
                                                        <br>
                                                        <small class="text-success">-{{ number_format($item->discount_amount, 2) }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary">{{ $item->quantity }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <strong>{{ number_format($item->total, 2) }} {{ $selectedCart->currency_code }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Ara Toplam:</strong></td>
                                            <td class="text-end"><strong>{{ number_format($selectedCart->subtotal, 2) }} {{ $selectedCart->currency_code }}</strong></td>
                                        </tr>
                                        @if($selectedCart->discount_amount > 0)
                                            <tr>
                                                <td colspan="3" class="text-end text-success"><strong>İndirim:</strong></td>
                                                <td class="text-end text-success"><strong>-{{ number_format($selectedCart->discount_amount, 2) }} {{ $selectedCart->currency_code }}</strong></td>
                                            </tr>
                                        @endif
                                        @if($selectedCart->tax_amount > 0)
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>KDV:</strong></td>
                                                <td class="text-end"><strong>{{ number_format($selectedCart->tax_amount, 2) }} {{ $selectedCart->currency_code }}</strong></td>
                                            </tr>
                                        @endif
                                        <tr class="table-active">
                                            <td colspan="3" class="text-end"><h4>Genel Toplam:</h4></td>
                                            <td class="text-end"><h4>{{ number_format($selectedCart->total, 2) }} {{ $selectedCart->currency_code }}</h4></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <!-- Navigation Buttons -->
                        @if($this->canGoPrevious())
                            <button type="button" class="btn btn-secondary me-auto" wire:click="previousCart">
                                <i class="fas fa-arrow-left me-1"></i> Önceki
                            </button>
                        @endif

                        @if($this->canGoNext())
                            <button type="button" class="btn btn-secondary" wire:click="nextCart">
                                Sonraki <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        @endif

                        <!-- Action Buttons -->
                        @if($selectedCart->status === 'active' && $selectedCart->items->isNotEmpty())
                            <button type="button" class="btn btn-warning" wire:click="markAsAbandoned({{ $selectedCart->cart_id }})"
                                    onclick="return confirm('Bu sepeti terk edildi olarak işaretlemek istediğinizden emin misiniz?')">
                                <i class="fas fa-exclamation-triangle me-1"></i> Terk Edildi İşaretle
                            </button>
                        @endif

                        @if($selectedCart->items->isNotEmpty())
                            <button type="button" class="btn btn-danger" wire:click="clearCart({{ $selectedCart->cart_id }})"
                                    onclick="return confirm('Bu sepeti temizlemek istediğinizden emin misiniz? Bu işlem geri alınamaz!')">
                                <i class="fas fa-trash me-1"></i> Sepeti Temizle
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
