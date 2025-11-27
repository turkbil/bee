<div>
    <form wire:submit="save">
        <div class="row">
            {{-- Sol Kolon --}}
            <div class="col-lg-8">

                {{-- CARD 1: Kullanıcı & Plan Seçimi --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-circle text-primary me-2"></i>
                            Abonelik Bilgileri
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Kullanıcı Seçimi --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user text-primary me-1"></i>
                                    Kullanıcı *
                                </label>
                                <select class="form-select @error('customer_id') is-invalid @enderror"
                                        wire:model="customer_id"
                                        {{ $subscriptionId ? 'disabled' : '' }}>
                                    <option value="">Kullanıcı seçin...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Plan Seçimi --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-box text-success me-1"></i>
                                    Abonelik Planı *
                                </label>
                                <select class="form-select @error('plan_id') is-invalid @enderror"
                                        wire:model.live="plan_id">
                                    <option value="">Plan seçin...</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->subscription_plan_id }}">
                                            {{ $plan->getTranslated('title', 'tr') }}
                                            ({{ number_format($plan->price_monthly, 2) }} ₺/ay)
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- Faturalama Döngüsü --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-sync text-info me-1"></i>
                                    Faturalama Döngüsü *
                                </label>
                                <select class="form-select" wire:model.live="billing_cycle">
                                    <option value="monthly">Aylık</option>
                                    <option value="yearly">Yıllık</option>
                                </select>
                            </div>

                            {{-- Fiyat (Otomatik) --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-money-bill-wave text-success me-1"></i>
                                    Dönem Fiyatı
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           class="form-control"
                                           wire:model="price_per_cycle"
                                           step="0.01"
                                           readonly>
                                    <span class="input-group-text">₺</span>
                                </div>
                                <small class="text-muted">Plan ve döngüye göre otomatik hesaplanır</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: Tarihler --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt text-warning me-2"></i>
                            Abonelik Tarihleri
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Başlangıç Tarihi *</label>
                                <input type="date"
                                       class="form-control @error('started_at') is-invalid @enderror"
                                       wire:model="started_at">
                                @error('started_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bitiş Tarihi *</label>
                                <input type="date"
                                       class="form-control @error('current_period_end') is-invalid @enderror"
                                       wire:model="current_period_end">
                                @error('current_period_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Sağ Kolon --}}
            <div class="col-lg-4">

                {{-- CARD 3: Durum --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-toggle-on text-primary me-2"></i>
                            Durum
                        </h3>
                    </div>
                    <div class="card-body">
                        <label class="form-label">Abonelik Durumu</label>
                        <select class="form-select" wire:model="status">
                            <option value="active">Aktif</option>
                            <option value="trial">Deneme</option>
                            <option value="paused">Duraklatıldı</option>
                            <option value="cancelled">İptal Edildi</option>
                            <option value="expired">Süresi Doldu</option>
                            <option value="pending_payment">Ödeme Bekliyor</option>
                        </select>
                    </div>
                </div>

                {{-- CARD 4: Deneme Süresi --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-hourglass-half text-info me-2"></i>
                            Deneme Süresi
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" wire:model.live="has_trial">
                                <span class="form-check-label">Deneme süresi var</span>
                            </label>
                        </div>

                        @if($has_trial)
                            <div>
                                <label class="form-label">Deneme Süresi (Gün)</label>
                                <div class="input-group">
                                    <input type="number"
                                           class="form-control"
                                           wire:model="trial_days"
                                           min="0"
                                           placeholder="7">
                                    <span class="input-group-text">gün</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- CARD 5: Otomatik Yenileme --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-sync-alt text-success me-2"></i>
                            Yenileme
                        </h3>
                    </div>
                    <div class="card-body">
                        <label class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" wire:model="auto_renew">
                            <span class="form-check-label">Otomatik yenileme</span>
                        </label>
                        <small class="text-muted d-block mt-2">
                            Dönem sonunda otomatik olarak yenilensin
                        </small>
                    </div>
                </div>

                {{-- CARD 6: Kaydet --}}
                <div class="card bg-primary-lt">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-save me-2"></i>Kaydet
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
