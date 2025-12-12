@include('subscription::admin.helper')

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
                                <select class="form-select @error('user_id') is-invalid @enderror"
                                        wire:model="user_id"
                                        {{ $subscriptionId ? 'disabled' : '' }}>
                                    <option value="">Kullanıcı seçin...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Plan Seçimi --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">
                                    <i class="fas fa-box text-success me-1"></i>
                                    Abonelik Planı
                                </label>
                                <select class="form-select @error('subscription_plan_id') is-invalid @enderror"
                                        wire:model.live="subscription_plan_id">
                                    <option value="">Plan seçin...</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->subscription_plan_id }}">
                                            {{ $plan->getTranslated('title', 'tr') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subscription_plan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Önce plan seçin, sonra süre seçeneği görünecek</small>
                            </div>
                        </div>

                        {{-- Süre Seçimi (Dynamic Cycles) --}}
                        @if($subscription_plan_id && !empty($available_cycles))
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required">
                                    <i class="fas fa-clock text-warning me-1"></i>
                                    Süre Seçeneği
                                </label>
                                <select class="form-select @error('cycle_key') is-invalid @enderror"
                                        wire:model.live="cycle_key">
                                    <option value="">Süre seçin...</option>
                                    @foreach($available_cycles as $key => $cycle)
                                        <option value="{{ $key }}">
                                            {{ $cycle['label']['tr'] ?? $cycle['label']['en'] ?? $key }}
                                            ({{ $cycle['duration_days'] }} gün •
                                            @if(!empty($cycle['compare_price']) && $cycle['compare_price'] > $cycle['price'])
                                                <s>{{ $currency === 'USD' ? '$' : ($currency === 'EUR' ? '€' : '₺') }}{{ number_format($cycle['compare_price'], 2) }}</s>
                                            @endif
                                            {{ $currency === 'USD' ? '$' : ($currency === 'EUR' ? '€' : '₺') }}{{ number_format($cycle['price'], 2) }})
                                            @if(!empty($cycle['trial_days']))
                                                🎁 {{ $cycle['trial_days'] }} gün deneme
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('cycle_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Cycle Detayları --}}
                            @if($cycle_key && !empty($available_cycles[$cycle_key]))
                                @php
                                    $selectedCycle = $available_cycles[$cycle_key];
                                @endphp
                                <div class="col-12">
                                    <div class="alert alert-info mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-info-circle me-2 fs-3"></i>
                                            <div>
                                                <strong>{{ $selectedCycle['label']['tr'] ?? $selectedCycle['label']['en'] }}</strong>
                                                @if(!empty($selectedCycle['badge']['text']))
                                                    <span class="badge bg-{{ $selectedCycle['badge']['color'] ?? 'info' }} ms-2">
                                                        {{ $selectedCycle['badge']['text'] }}
                                                    </span>
                                                @endif
                                                <div class="small mt-1">
                                                    <span class="me-3">
                                                        <i class="fas fa-calendar-days me-1"></i>
                                                        {{ $selectedCycle['duration_days'] }} gün
                                                    </span>
                                                    <span class="me-3">
                                                        <i class="fas fa-money-bill-wave me-1"></i>
                                                        @if(!empty($selectedCycle['compare_price']) && $selectedCycle['compare_price'] > $selectedCycle['price'])
                                                            <s>{{ $currency === 'USD' ? '$' : ($currency === 'EUR' ? '€' : '₺') }}{{ number_format($selectedCycle['compare_price'], 2) }}</s>
                                                        @endif
                                                        {{ $currency === 'USD' ? '$' : ($currency === 'EUR' ? '€' : '₺') }}{{ number_format($selectedCycle['price'], 2) }}
                                                    </span>
                                                    @if(!empty($selectedCycle['trial_days']))
                                                        <span class="me-3">
                                                            <i class="fas fa-gift me-1"></i>
                                                            {{ $selectedCycle['trial_days'] }} gün deneme
                                                        </span>
                                                    @endif
                                                </div>
                                                @if(!empty($selectedCycle['promo_text']['tr']))
                                                    <div class="mt-2">
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="fas fa-tag me-1"></i>
                                                            {{ $selectedCycle['promo_text']['tr'] }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @elseif($subscription_plan_id)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Bu plan için henüz süre seçeneği tanımlanmamış.
                            </div>
                        @endif
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
                                <label class="form-label required">
                                    <i class="fas fa-calendar-plus text-primary me-1"></i>
                                    Başlangıç Tarihi ve Saati
                                </label>
                                <input type="datetime-local"
                                       class="form-control @error('started_at') is-invalid @enderror"
                                       wire:model.live="started_at">
                                @error('started_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tarih ve saat seçin (ör: 12 Ara 2025, 14:30)</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">
                                    <i class="fas fa-calendar-check text-success me-1"></i>
                                    Bitiş Tarihi ve Saati
                                </label>
                                <input type="datetime-local"
                                       class="form-control @error('current_period_end') is-invalid @enderror"
                                       wire:model="current_period_end">
                                @error('current_period_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Otomatik hesaplanır, manuel değiştirilebilir</small>
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
