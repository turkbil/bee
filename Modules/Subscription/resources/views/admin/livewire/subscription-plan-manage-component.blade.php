@php
    View::share('pretitle', $planId ? __('subscription::admin.edit_plan') : __('subscription::admin.new_plan'));
@endphp

<div wire:key="subscription-plan-manage-component">
    @include('subscription::admin.helper')
    @include('admin.partials.error_message')

    <form wire:submit="save">
                <div class="row">
                    <div class="col-lg-8">
                        {{-- Step 1: Plan Bilgileri --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <span class="badge bg-primary me-2">1</span>
                                    {{ __('subscription::admin.plan_name') }}
                                </h3>
                                <div class="card-actions">
                                    <ul class="nav nav-pills">
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
                            </div>
                            <div class="card-body">
                                @foreach($availableLanguages as $lang)
                                <div class="{{ $currentLanguage === $lang ? '' : 'd-none' }}">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-tag text-primary me-1"></i>
                                            {{ __('subscription::admin.plan_name') }} ({{ strtoupper($lang) }})
                                        </label>
                                        <input type="text" class="form-control form-control-lg"
                                               wire:model="multiLangInputs.{{ $lang }}.title"
                                               wire:blur="generateSlug"
                                               placeholder="Örn: Premium, Starter, Pro">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Kullanıcıların göreceği plan adı
                                        </small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-align-left text-info me-1"></i>
                                            {{ __('admin.description') }} ({{ strtoupper($lang) }})
                                        </label>
                                        <textarea class="form-control" rows="3"
                                                  wire:model="multiLangInputs.{{ $lang }}.description"
                                                  placeholder="Planın kısa açıklaması..."></textarea>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Plan kartında görünecek açıklama
                                        </small>
                                    </div>
                                </div>
                                @endforeach

                                <div class="mb-0">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-link text-secondary me-1"></i>
                                        {{ __('admin.slug') }}
                                    </label>
                                    <input type="text" class="form-control" wire:model="slug" placeholder="premium-plan">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        URL'de kullanılacak benzersiz tanımlayıcı (otomatik oluşturulur)
                                    </small>
                                    @error('slug') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: Fiyatlandırma --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <span class="badge bg-primary me-2">2</span>
                                    {{ __('subscription::admin.pricing') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Aylık --}}
                                    <div class="col-md-6">
                                        <div class="card bg-blue-lt mb-3">
                                            <div class="card-body">
                                                <h4 class="mb-3">
                                                    <i class="fas fa-calendar-alt text-blue me-2"></i>
                                                    Aylık Fiyat
                                                </h4>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('subscription::admin.price_monthly') }}</label>
                                                    <div class="input-group input-group-lg">
                                                        <input type="number" class="form-control" step="0.01" wire:model="price_monthly" placeholder="99">
                                                        <span class="input-group-text fw-bold">₺</span>
                                                    </div>
                                                    @error('price_monthly') <span class="text-danger small">{{ $message }}</span> @enderror
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label">
                                                        {{ __('subscription::admin.compare_price') }}
                                                        <small class="text-muted">({{ __('subscription::admin.strikethrough') }})</small>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" step="0.01" wire:model="compare_price_monthly" placeholder="149">
                                                        <span class="input-group-text">₺</span>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Eski fiyat olarak üstü çizili gösterilir
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Yıllık --}}
                                    <div class="col-md-6">
                                        <div class="card bg-green-lt mb-3">
                                            <div class="card-body">
                                                <h4 class="mb-3">
                                                    <i class="fas fa-calendar-check text-green me-2"></i>
                                                    Yıllık Fiyat
                                                </h4>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('subscription::admin.price_yearly') }}</label>
                                                    <div class="input-group input-group-lg">
                                                        <input type="number" class="form-control" step="0.01" wire:model="price_yearly" placeholder="999">
                                                        <span class="input-group-text fw-bold">₺</span>
                                                    </div>
                                                    @error('price_yearly') <span class="text-danger small">{{ $message }}</span> @enderror
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label">
                                                        {{ __('subscription::admin.compare_price') }}
                                                        <small class="text-muted">({{ __('subscription::admin.strikethrough') }})</small>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" step="0.01" wire:model="compare_price_yearly" placeholder="1788">
                                                        <span class="input-group-text">₺</span>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Yıllık alımda ne kadar tasarruf edildiğini gösterir
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3: Özellikler --}}
                        <div class="card mb-3" x-data="{
                            features: @entangle('features'),
                            newIcon: 'check',
                            newFeature: '',
                            addFeature() {
                                if (this.newFeature.trim()) {
                                    this.features.push({
                                        icon: this.newIcon.trim() || 'check',
                                        text: this.newFeature.trim()
                                    });
                                    this.newFeature = '';
                                    this.newIcon = 'check';
                                }
                            },
                            removeFeature(index) {
                                this.features.splice(index, 1);
                            },
                            getFeatureText(feature) {
                                return typeof feature === 'string' ? feature : feature.text;
                            },
                            getFeatureIcon(feature) {
                                return typeof feature === 'string' ? 'check' : (feature.icon || 'check');
                            }
                        }">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <span class="badge bg-primary me-2">3</span>
                                    {{ __('subscription::admin.features') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-plus-circle text-success me-1"></i>
                                        {{ __('subscription::admin.add_feature') }}
                                    </label>
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i :class="'fas fa-' + newIcon"></i>
                                                </span>
                                                <input type="text" class="form-control" x-model="newIcon" placeholder="check">
                                            </div>
                                            <small class="text-muted">FontAwesome ikon</small>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                       placeholder="Örn: Sınırsız dinleme, HD kalite..."
                                                       x-model="newFeature"
                                                       @keydown.enter.prevent="addFeature()">
                                                <button type="button" class="btn btn-primary" @click="addFeature()">
                                                    <i class="fas fa-plus me-1"></i> Ekle
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        İkon örnekleri: check, star, music, infinity, bolt, shield-alt, ban
                                    </small>
                                </div>

                                <template x-if="features.length > 0">
                                    <ul class="list-group">
                                        <template x-for="(feature, index) in features" :key="index">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>
                                                    <i :class="'fas fa-' + getFeatureIcon(feature) + ' text-success me-2'"></i>
                                                    <span x-text="getFeatureText(feature)"></span>
                                                </span>
                                                <button type="button" class="btn btn-sm btn-outline-danger" @click="removeFeature(index)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </li>
                                        </template>
                                    </ul>
                                </template>
                                <template x-if="features.length === 0">
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        {{ __('subscription::admin.no_features') }} - En az bir özellik eklemeniz önerilir
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        {{-- Deneme & Limitler --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-cog text-primary me-2"></i>
                                    {{ __('admin.settings') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-hourglass-half text-warning me-1"></i>
                                        {{ __('subscription::admin.trial_days') }}
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" wire:model="trial_days" min="0" placeholder="7">
                                        <span class="input-group-text">gün</span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        0 = Deneme süresi yok
                                    </small>
                                    @error('trial_days') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-mobile-alt text-info me-1"></i>
                                        {{ __('subscription::admin.device_limit') }}
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" wire:model="device_limit" min="1" placeholder="3">
                                        <span class="input-group-text">cihaz</span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Aynı anda kaç cihazda kullanılabilir
                                    </small>
                                    @error('device_limit') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Öne Çıkarma --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-star text-warning me-2"></i>
                                    Görünürlük
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model="is_featured">
                                        <span class="form-check-label fw-bold">{{ __('subscription::admin.is_featured') }}</span>
                                    </label>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Öne çıkan plan "Popüler" etiketi ile vurgulanır
                                    </small>
                                </div>
                                <div class="mb-0">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model="is_active">
                                        <span class="form-check-label fw-bold">{{ __('admin.active') }}</span>
                                    </label>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Pasif planlar kullanıcılara gösterilmez
                                    </small>
                                </div>
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
