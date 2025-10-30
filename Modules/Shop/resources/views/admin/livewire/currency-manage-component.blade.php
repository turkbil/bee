@include('shop::admin.helper')

@php
    $title = $currencyId ? 'Edit Currency' : 'New Currency';
    View::share('pretitle', $title);
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title mb-0">{{ $title }}</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.shop.currencies.index') }}" class="btn btn-outline-secondary">
                Back
            </a>
            <button class="btn btn-primary" wire:click="save">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-lg-8">
                <h3 class="mb-3">Basic Information</h3>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Currency Code</label>
                        <input type="text"
                               class="form-control @error('code') is-invalid @enderror"
                               wire:model.lazy="code"
                               placeholder="USD"
                               maxlength="3"
                               style="text-transform: uppercase;">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">3-letter ISO code (e.g., USD, EUR, TRY)</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Symbol</label>
                        <input type="text"
                               class="form-control @error('symbol') is-invalid @enderror"
                               wire:model.lazy="symbol"
                               placeholder="$"
                               maxlength="10">
                        @error('symbol')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Currency symbol (e.g., $, €, ₺)</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Name (English)</label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           wire:model.lazy="name"
                           placeholder="US Dollar">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name (Turkish)</label>
                        <input type="text"
                               class="form-control"
                               wire:model.lazy="nameTranslations.tr"
                               placeholder="Amerikan Doları">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name (English - Translation)</label>
                        <input type="text"
                               class="form-control"
                               wire:model.lazy="nameTranslations.en"
                               placeholder="US Dollar">
                    </div>
                </div>

                <h3 class="mt-4 mb-3">Settings</h3>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">
                            Exchange Rate (to TRY)
                            <span class="form-help" data-bs-toggle="popover" data-bs-placement="top"
                                  data-bs-content="Manuel kur girişi yapabilir veya ana sayfadan TCMB'den otomatik çekebilirsiniz.">
                                ?
                            </span>
                        </label>
                        <input type="number"
                               class="form-control @error('exchangeRate') is-invalid @enderror"
                               wire:model.lazy="exchangeRate"
                               step="0.0001"
                               min="0.0001"
                               placeholder="42.0000">
                        @error('exchangeRate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint text-muted">
                            <i class="fas fa-info-circle"></i>
                            1 {{ $code ?: 'XXX' }} = <span class="fw-bold text-primary">{{ $exchangeRate }}</span> TRY
                            <br>
                            <span class="text-success">💡 İpucu:</span> Ana sayfadan <strong>"TCMB'den Güncelle"</strong> butonuyla otomatik çekebilirsiniz.
                        </small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Decimal Places</label>
                        <input type="number"
                               class="form-control @error('decimalPlaces') is-invalid @enderror"
                               wire:model.lazy="decimalPlaces"
                               min="0"
                               max="4"
                               placeholder="2">
                        @error('decimalPlaces')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Number of decimal places (0-4)</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Symbol Position</label>
                    <select class="form-select @error('format') is-invalid @enderror"
                            wire:model.lazy="format">
                        <option value="symbol_before">Before amount ($ 1,000.00)</option>
                        <option value="symbol_after">After amount (1.000,00 ₺)</option>
                    </select>
                    @error('format')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="isActive">
                            <span class="form-check-label">Active</span>
                        </label>
                        <small class="form-hint d-block">Active currencies can be used in the system</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="isDefault">
                            <span class="form-check-label">Set as Default Currency</span>
                        </label>
                        <small class="form-hint d-block">Default currency for new products and carts</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="isAutoUpdate">
                            <span class="form-check-label">
                                <i class="fas fa-sync-alt text-success"></i> TCMB Auto Update
                            </span>
                        </label>
                        <small class="form-hint d-block text-success">
                            <strong>Otomatik güncelleme:</strong> Bu para birimi TCMB'den günlük kurları çekecek
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card bg-light">
                    <div class="card-header">
                        <h3 class="card-title">Preview</h3>
                    </div>
                    <div class="card-body">
                        <h4 class="mb-2">{{ $code ?: 'XXX' }} - {{ $symbol ?: '?' }}</h4>
                        <p class="text-muted mb-3">{{ $name ?: 'Currency Name' }}</p>

                        <div class="mb-3">
                            <strong>Sample Price:</strong>
                            <div class="fs-3 fw-bold text-primary">
                                @if($format === 'symbol_before')
                                    {{ $symbol }} 1{{ $decimalPlaces > 0 ? ',' . str_repeat('0', $decimalPlaces) : '' }}
                                @else
                                    1{{ $decimalPlaces > 0 ? ',' . str_repeat('0', $decimalPlaces) : '' }} {{ $symbol }}
                                @endif
                            </div>
                        </div>

                        <div class="mb-2">
                            <span class="badge {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                                {{ $isActive ? 'Active' : 'Inactive' }}
                            </span>
                            @if($isDefault)
                                <span class="badge bg-primary">
                                    <i class="fas fa-star"></i> Default
                                </span>
                            @endif
                            @if($isAutoUpdate)
                                <span class="badge bg-success">
                                    <i class="fas fa-sync-alt"></i> Auto Update
                                </span>
                            @endif
                        </div>

                        <hr>

                        <div class="small text-muted">
                            <div class="mb-1"><strong>Exchange Rate:</strong> {{ $exchangeRate }} TRY</div>
                            <div class="mb-1"><strong>Decimal Places:</strong> {{ $decimalPlaces }}</div>
                            <div><strong>Format:</strong> {{ ucfirst(str_replace('_', ' ', $format)) }}</div>
                        </div>
                    </div>
                </div>

                <div class="card bg-azure-lt mt-3">
                    <div class="card-body">
                        <h4>Common Currencies</h4>
                        <ul class="list-unstyled mb-0">
                            <li><strong>USD ($)</strong> - US Dollar</li>
                            <li><strong>EUR (€)</strong> - Euro</li>
                            <li><strong>GBP (£)</strong> - British Pound</li>
                            <li><strong>TRY (₺)</strong> - Turkish Lira</li>
                            <li><strong>JPY (¥)</strong> - Japanese Yen</li>
                        </ul>
                    </div>
                </div>

                {{-- TCMB Info Card --}}
                <div class="card bg-success-lt mt-3">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="fas fa-sync-alt"></i> TCMB Otomatik Güncelleme
                        </h4>
                        <p class="mb-2 small">
                            <strong>Türkiye Cumhuriyet Merkez Bankası</strong> (TCMB) güncel döviz kurlarını otomatik çekebilirsiniz.
                        </p>
                        <hr class="my-2">
                        <div class="small">
                            <div class="mb-1">
                                <i class="fas fa-check text-success"></i> Güncel piyasa kurları
                            </div>
                            <div class="mb-1">
                                <i class="fas fa-check text-success"></i> Tek tıkla otomatik güncelleme
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-check text-success"></i> Manuel düzenleme yine mümkün
                            </div>
                        </div>
                        <a href="{{ route('admin.shop.currencies.index') }}" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-arrow-right"></i> Kur Listesine Git
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
