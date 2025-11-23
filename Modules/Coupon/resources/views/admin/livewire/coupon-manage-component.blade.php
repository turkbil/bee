@php
    View::share('pretitle', $couponId ? __('coupon::admin.edit') : __('coupon::admin.create'));
@endphp

<div wire:key="coupon-manage-component">
    @include('coupon::admin.helper')
    @include('admin.partials.error_message')

    <form wire:submit="save">
                <div class="row">
                    <div class="col-lg-8">
                        {{-- Step 1: Kupon Kodu --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <span class="badge bg-primary me-2">1</span>
                                    {{ __('coupon::admin.step_code') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3" x-data="{
                                    generateCode() {
                                        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                                        let code = '';
                                        for (let i = 0; i < 8; i++) {
                                            code += chars.charAt(Math.floor(Math.random() * chars.length));
                                        }
                                        $wire.set('code', code);
                                    }
                                }">
                                    <label class="form-label fw-bold">{{ __('coupon::admin.code') }}</label>
                                    <div class="input-group input-group-lg">
                                        <input type="text" class="form-control" wire:model="code" style="text-transform: uppercase; letter-spacing: 2px; font-weight: bold;" placeholder="YILSONU20">
                                        <button type="button" class="btn btn-primary" @click="generateCode()">
                                            <i class="fas fa-magic me-1"></i>{{ __('coupon::admin.generate') }}
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        {{ __('coupon::admin.code_hint') }}
                                    </small>
                                    @error('code') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: İndirim Türü --}}
                        <div class="card mb-3" x-data="{
                            selectedType: @entangle('coupon_type'),
                            discountPercentage: @entangle('discount_percentage'),
                            discountAmount: @entangle('discount_amount')
                        }">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <span class="badge bg-primary me-2">2</span>
                                    {{ __('coupon::admin.step_discount') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">{{ __('coupon::admin.discount_type') }}</label>
                                    <div class="row g-2">
                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="coupon_type" value="percentage" class="form-selectgroup-input" x-model="selectedType">
                                                <span class="form-selectgroup-label d-flex flex-column align-items-center p-3">
                                                    <i class="fas fa-percent fa-2x mb-2 text-primary"></i>
                                                    <span class="fw-bold">{{ __('coupon::admin.percentage') }}</span>
                                                    <small class="text-muted">{{ __('coupon::admin.hint_percentage') }}</small>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="coupon_type" value="fixed_amount" class="form-selectgroup-input" x-model="selectedType">
                                                <span class="form-selectgroup-label d-flex flex-column align-items-center p-3">
                                                    <i class="fas fa-lira-sign fa-2x mb-2 text-success"></i>
                                                    <span class="fw-bold">{{ __('coupon::admin.fixed_amount') }}</span>
                                                    <small class="text-muted">{{ __('coupon::admin.hint_fixed') }}</small>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="coupon_type" value="free_shipping" class="form-selectgroup-input" x-model="selectedType">
                                                <span class="form-selectgroup-label d-flex flex-column align-items-center p-3">
                                                    <i class="fas fa-truck fa-2x mb-2 text-info"></i>
                                                    <span class="fw-bold">{{ __('coupon::admin.free_shipping') }}</span>
                                                    <small class="text-muted">{{ __('coupon::admin.hint_shipping') }}</small>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="coupon_type" value="buy_x_get_y" class="form-selectgroup-input" x-model="selectedType">
                                                <span class="form-selectgroup-label d-flex flex-column align-items-center p-3">
                                                    <i class="fas fa-gift fa-2x mb-2 text-warning"></i>
                                                    <span class="fw-bold">{{ __('coupon::admin.buy_x_get_y') }}</span>
                                                    <small class="text-muted">{{ __('coupon::admin.hint_bogo') }}</small>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <template x-if="selectedType === 'percentage'">
                                    <div class="alert alert-light border mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold mb-1">{{ __('coupon::admin.discount_value') }}</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control form-control-lg" step="1" min="1" max="100" x-model="discountPercentage" placeholder="20">
                                                    <span class="input-group-text fw-bold">%</span>
                                                </div>
                                                @error('discount_percentage') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <div class="text-muted">
                                                    <i class="fas fa-calculator me-1"></i>
                                                    <strong>{{ __('coupon::admin.example') }}:</strong> %20 indirim = 100₺'lik üründe 20₺ indirim
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="selectedType === 'fixed_amount' || selectedType === 'buy_x_get_y'">
                                    <div class="alert alert-light border mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold mb-1">{{ __('coupon::admin.discount_value') }}</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control form-control-lg" step="0.01" x-model="discountAmount" placeholder="50">
                                                    <span class="input-group-text fw-bold">₺</span>
                                                </div>
                                                @error('discount_amount') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <div class="text-muted">
                                                    <i class="fas fa-calculator me-1"></i>
                                                    <strong>{{ __('coupon::admin.example') }}:</strong> 50₺ sabit indirim uygulanır
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Step 3: Kullanım Alanı + Step 4: Limitler (Alpine.js ile birleşik) --}}
                        <div x-data="{ scope: @entangle('applies_to') }">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <span class="badge bg-primary me-2">3</span>
                                        {{ __('coupon::admin.step_scope') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <label class="form-label fw-bold">{{ __('coupon::admin.applies_to') }}</label>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="applies_to" value="all" class="form-selectgroup-input" x-model="scope">
                                                <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                    <i class="fas fa-globe fa-lg me-2 text-primary"></i>
                                                    <span>
                                                        <span class="fw-bold d-block">{{ __('coupon::admin.scope_all') }}</span>
                                                        <small class="text-muted">{{ __('coupon::admin.hint_scope_all') }}</small>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="applies_to" value="shop" class="form-selectgroup-input" x-model="scope">
                                                <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                    <i class="fas fa-shopping-cart fa-lg me-2 text-success"></i>
                                                    <span>
                                                        <span class="fw-bold d-block">{{ __('coupon::admin.scope_shop') }}</span>
                                                        <small class="text-muted">{{ __('coupon::admin.hint_scope_shop') }}</small>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="applies_to" value="subscription" class="form-selectgroup-input" x-model="scope">
                                                <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                    <i class="fas fa-sync-alt fa-lg me-2 text-info"></i>
                                                    <span>
                                                        <span class="fw-bold d-block">{{ __('coupon::admin.scope_subscription') }}</span>
                                                        <small class="text-muted">{{ __('coupon::admin.hint_scope_sub') }}</small>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 4: Sepet Limitleri (sadece shop veya all seçiliyse) --}}
                            <template x-if="scope !== 'subscription'">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <span class="badge bg-primary me-2">4</span>
                                            {{ __('coupon::admin.limits') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">
                                                        <i class="fas fa-shopping-cart text-primary me-1"></i>
                                                        {{ __('coupon::admin.min_amount') }}
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" step="0.01" wire:model="min_order_amount" placeholder="100">
                                                        <span class="input-group-text">₺</span>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        {{ __('coupon::admin.hint_min_amount') }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">
                                                        <i class="fas fa-hand-holding-usd text-success me-1"></i>
                                                        {{ __('coupon::admin.max_discount') }}
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" step="0.01" wire:model="max_discount_amount" placeholder="500">
                                                        <span class="input-group-text">₺</span>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        {{ __('coupon::admin.hint_max_discount') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Step 4/5: Kullanım Limitleri (her zaman göster) --}}
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <span class="badge bg-primary me-2" x-text="scope === 'subscription' ? '4' : '5'"></span>
                                        {{ __('coupon::admin.usage_limit') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="fas fa-users text-info me-1"></i>
                                                    {{ __('coupon::admin.usage_limit') }}
                                                </label>
                                                <input type="number" class="form-control" wire:model="usage_limit_total" placeholder="{{ __('coupon::admin.unlimited') }}">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    {{ __('coupon::admin.hint_usage_limit') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="fas fa-user text-warning me-1"></i>
                                                    {{ __('coupon::admin.usage_per_user') }}
                                                </label>
                                                <input type="number" class="form-control" wire:model="usage_limit_per_user" min="1" placeholder="1">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    {{ __('coupon::admin.hint_usage_per_user') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <ul class="nav nav-tabs card-header-tabs">
                                    @foreach($availableLanguages as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $currentLanguage === $lang ? 'active' : '' }}"
                                           href="#" wire:click.prevent="switchLanguage('{{ $lang }}')">
                                            {{ strtoupper($lang) }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="card-body">
                                @foreach($availableLanguages as $lang)
                                <div class="{{ $currentLanguage === $lang ? '' : 'd-none' }}">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.description') }} ({{ strtoupper($lang) }})</label>
                                        <textarea class="form-control" rows="3"
                                                  wire:model="multiLangInputs.{{ $lang }}.description"></textarea>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        {{-- Geçerlilik Tarihleri --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                                    {{ __('coupon::admin.validity') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-play text-success me-1"></i>
                                        {{ __('coupon::admin.starts_at') }}
                                    </label>
                                    <input type="datetime-local" class="form-control" wire:model="valid_from">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        {{ __('coupon::admin.hint_starts_at') }}
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-stop text-danger me-1"></i>
                                        {{ __('coupon::admin.expires_at') }}
                                    </label>
                                    <input type="datetime-local" class="form-control" wire:model="valid_until">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        {{ __('coupon::admin.hint_expires_at') }}
                                    </small>
                                    @error('valid_until') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-toggle-on text-primary me-2"></i>
                                    {{ __('admin.status') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <label class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" wire:model="is_active">
                                    <span class="form-check-label fw-bold">{{ __('admin.active') }}</span>
                                </label>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Aktif olmayan kuponlar kullanılamaz
                                </small>
                            </div>
                        </div>

                        {{-- Save Button --}}
                        <div class="card bg-primary-lt">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-save me-2"></i>{{ __('admin.save') }}
                                </button>
                                <small class="text-muted d-block text-center mt-2">
                                    Tüm bilgileri kontrol ettikten sonra kaydedin
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
</div>
