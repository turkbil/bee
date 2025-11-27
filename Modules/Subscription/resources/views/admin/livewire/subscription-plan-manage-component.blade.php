<div>
    @include('subscription::admin.helper')
    @include('admin.partials.error_message')

    <form wire:submit="save">
        <div class="row">
            {{-- Sol Kolon: Plan Bilgileri --}}
            <div class="col-lg-8">

                {{-- CARD 1: Plan Adı ve Açıklama (Çoklu Dil) --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-globe text-primary me-2"></i>
                            {{ __('subscription::admin.plan_information') }}
                        </h3>
                        <div class="card-actions">
                            <div class="btn-group btn-group-sm" role="group">
                                @foreach($availableLanguages as $lang)
                                    <button type="button"
                                            class="btn {{ $currentLanguage === $lang ? 'btn-primary' : 'btn-outline-primary' }}"
                                            wire:click="$set('currentLanguage', '{{ $lang }}')">
                                        {{ strtoupper($lang) }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach($availableLanguages as $lang)
                            <div class="language-content" style="{{ $currentLanguage === $lang ? '' : 'display: none;' }}">
                                {{-- Başlık --}}
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-tag text-primary me-1"></i>
                                        {{ __('subscription::admin.plan_name') }} ({{ strtoupper($lang) }})
                                    </label>
                                    <input type="text"
                                           class="form-control @error('multiLangInputs.'.$lang.'.title') is-invalid @enderror"
                                           wire:model="multiLangInputs.{{ $lang }}.title"
                                           placeholder="Örn: Premium, Starter, Pro">
                                    @error('multiLangInputs.'.$lang.'.title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Açıklama --}}
                                <div class="mb-0">
                                    <label class="form-label">
                                        <i class="fas fa-align-left text-info me-1"></i>
                                        {{ __('admin.description') }} ({{ strtoupper($lang) }})
                                    </label>
                                    <textarea class="form-control"
                                              rows="3"
                                              wire:model="multiLangInputs.{{ $lang }}.description"
                                              placeholder="Planın kısa açıklaması..."></textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- CARD 2: Slug --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-link text-secondary me-2"></i>
                            URL Slug
                        </h3>
                    </div>
                    <div class="card-body">
                        <input type="text"
                               class="form-control @error('inputs.slug') is-invalid @enderror"
                               wire:model="inputs.slug"
                               placeholder="premium-plan">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            URL'de kullanılacak benzersiz tanımlayıcı
                        </small>
                        @error('inputs.slug')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- CARD 3: Fiyatlandırma --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                            {{ __('subscription::admin.pricing') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Aylık --}}
                            <div class="col-md-6 mb-3">
                                <div class="card bg-blue-lt">
                                    <div class="card-body">
                                        <h4 class="mb-3">
                                            <i class="fas fa-calendar-alt text-blue me-2"></i>
                                            Aylık Fiyat
                                        </h4>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('subscription::admin.price_monthly') }}</label>
                                            <div class="input-group">
                                                <input type="number"
                                                       class="form-control"
                                                       step="0.01"
                                                       wire:model="inputs.price_monthly"
                                                       placeholder="99">
                                                <span class="input-group-text">₺</span>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label">
                                                {{ __('subscription::admin.compare_price') }}
                                                <small class="text-muted">(İndirim öncesi)</small>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                       class="form-control"
                                                       step="0.01"
                                                       wire:model="inputs.compare_price_monthly"
                                                       placeholder="149">
                                                <span class="input-group-text">₺</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Yıllık --}}
                            <div class="col-md-6 mb-3">
                                <div class="card bg-green-lt">
                                    <div class="card-body">
                                        <h4 class="mb-3">
                                            <i class="fas fa-calendar-check text-green me-2"></i>
                                            Yıllık Fiyat
                                        </h4>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('subscription::admin.price_yearly') }}</label>
                                            <div class="input-group">
                                                <input type="number"
                                                       class="form-control"
                                                       step="0.01"
                                                       wire:model="inputs.price_yearly"
                                                       placeholder="999">
                                                <span class="input-group-text">₺</span>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label">
                                                {{ __('subscription::admin.compare_price') }}
                                                <small class="text-muted">(İndirim öncesi)</small>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                       class="form-control"
                                                       step="0.01"
                                                       wire:model="inputs.compare_price_yearly"
                                                       placeholder="1788">
                                                <span class="input-group-text">₺</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 4: Özellikler --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list-check text-warning me-2"></i>
                            {{ __('subscription::admin.features') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('subscription::admin.add_feature') }}</label>
                            <div class="input-group">
                                <input type="text"
                                       class="form-control"
                                       wire:model="newFeature"
                                       wire:keydown.enter.prevent="addFeature"
                                       placeholder="Örn: Sınırsız şarkı dinleme">
                                <button type="button" class="btn btn-primary" wire:click="addFeature">
                                    <i class="fas fa-plus"></i> Ekle
                                </button>
                            </div>
                        </div>

                        @if(is_array($features) && count($features) > 0)
                            <ul class="list-group">
                                @foreach($features as $index => $feature)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-check text-success me-2"></i>
                                            {{ $feature }}
                                        </span>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="removeFeature({{ $index }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Henüz özellik eklenmedi
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Sağ Kolon: Ayarlar --}}
            <div class="col-lg-4">

                {{-- CARD 5: Deneme ve Limitler --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cog text-primary me-2"></i>
                            {{ __('admin.settings') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-hourglass-half text-warning me-1"></i>
                                {{ __('subscription::admin.trial_days') }}
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control"
                                       wire:model="inputs.trial_days"
                                       min="0"
                                       placeholder="7">
                                <span class="input-group-text">gün</span>
                            </div>
                            <small class="text-muted">0 = Deneme süresi yok</small>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">
                                <i class="fas fa-mobile-alt text-info me-1"></i>
                                {{ __('subscription::admin.device_limit') }}
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control"
                                       wire:model="inputs.device_limit"
                                       min="1"
                                       placeholder="3">
                                <span class="input-group-text">cihaz</span>
                            </div>
                            <small class="text-muted">Aynı anda kaç cihazda kullanılabilir</small>
                        </div>
                    </div>
                </div>

                {{-- CARD 6: Görünürlük --}}
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
                                <input type="checkbox" class="form-check-input" wire:model="inputs.is_featured">
                                <span class="form-check-label">{{ __('subscription::admin.is_featured') }}</span>
                            </label>
                            <small class="text-muted d-block">Öne çıkan plan olarak işaretle</small>
                        </div>
                        <div class="mb-0">
                            <label class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" wire:model="inputs.is_active">
                                <span class="form-check-label">{{ __('admin.active') }}</span>
                            </label>
                            <small class="text-muted d-block">Pasif planlar kullanıcılara gösterilmez</small>
                        </div>
                    </div>
                </div>

                {{-- CARD 7: Kaydet --}}
                <div class="card bg-primary-lt">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-save me-2"></i>{{ __('admin.save') }}
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
