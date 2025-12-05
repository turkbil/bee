@include('subscription::admin.helper')

<div x-data="cycleManager(@entangle('cycles'), @entangle('features'))">
    <div class="row g-4">
        <div class="col-lg-8">
            {{-- PLAN BİLGİLERİ --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                        <i class="fas fa-info-circle text-azure"></i>
                        <span>{{ __('subscription::admin.plan_information') }}</span>
                    </h3>
                </div>
                <div class="card-body p-4">
                    @php
                        $defaultLang = $availableLanguages[0] ?? 'tr';
                    @endphp

                    {{-- Plan Adı (TR) --}}
                    <div class="mb-4">
                        <label class="form-label required fw-semibold">
                            <i class="fas fa-tag me-2 text-muted"></i>
                            {{ __('subscription::admin.plan_name') }}
                        </label>
                        <input type="text"
                               class="form-control @error('multiLangInputs.'.$defaultLang.'.title') is-invalid @enderror"
                               wire:model="multiLangInputs.{{ $defaultLang }}.title"
                               placeholder="Örn: Premium, Gold, Silver">
                        @error('multiLangInputs.'.$defaultLang.'.title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Açıklama (TR) --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-align-left me-2 text-muted"></i>
                            {{ __('admin.description') }}
                        </label>
                        <textarea class="form-control"
                                  wire:model="multiLangInputs.{{ $defaultLang }}.description"
                                  rows="4"
                                  placeholder="Plan hakkında kısa açıklama..."></textarea>
                    </div>

                    {{-- Slug --}}
                    <div class="mb-4">
                        <label class="form-label required fw-semibold">
                            <i class="fas fa-link me-2 text-muted"></i>
                            Slug
                        </label>
                        <input type="text"
                               class="form-control @error('inputs.slug') is-invalid @enderror"
                               wire:model="inputs.slug"
                               @if(!$planId) readonly @endif
                               placeholder="premium-plan">
                        <small class="form-hint mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            @if(!$planId)
                                Plan adından otomatik oluşturulur
                            @else
                                URL'de kullanılacak benzersiz tanımlayıcı
                            @endif
                        </small>
                        @error('inputs.slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    {{-- Para Birimi & KDV --}}
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="mb-0">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-money-bill-wave me-2 text-muted"></i>
                                    Para Birimi
                                </label>
                                <select class="form-select @error('inputs.currency') is-invalid @enderror"
                                        wire:model="inputs.currency">
                                    <option value="TRY">₺ TRY (Türk Lirası)</option>
                                    <option value="USD">$ USD (Amerikan Doları)</option>
                                    <option value="EUR">€ EUR (Euro)</option>
                                </select>
                                @error('inputs.currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-0">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-percentage me-2 text-muted"></i>
                                    KDV Oranı (%)
                                </label>
                                <input type="number"
                                       class="form-control @error('inputs.tax_rate') is-invalid @enderror"
                                       wire:model="inputs.tax_rate"
                                       placeholder="20"
                                       min="0"
                                       max="100"
                                       step="0.01">
                                <small class="form-hint mt-1">Türkiye için varsayılan: %20</small>
                                @error('inputs.tax_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-0">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-eye me-2 text-muted"></i>
                                    Fiyat Gösterim
                                </label>
                                <select class="form-select @error('inputs.price_display_mode') is-invalid @enderror"
                                        wire:model="inputs.price_display_mode">
                                    <option value="show">Göster</option>
                                    <option value="hide">Gizle</option>
                                    <option value="request">Fiyat Sorunuz</option>
                                </select>
                                @error('inputs.price_display_mode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BILLING CYCLES --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                        <i class="fas fa-clock text-warning"></i>
                        <span>Süre & Fiyat Seçenekleri</span>
                    </h3>
                </div>
                <div class="card-body p-4">
                    {{-- Mevcut Cycles --}}
                    <template x-if="Object.keys(cycles).length > 0">
                        <div class="mb-4" id="cycles-sortable-list">
                            <template x-for="(cycle, key, index) in cycles" :key="key">
                                <div class="card mb-3 shadow-sm"
                                     style="border-left: 4px solid var(--tblr-primary);"
                                     :data-cycle-key="key"
                                     x-data="{ editMode: false, editData: {} }">
                                    <div class="card-body p-3">
                                        {{-- VIEW MODE --}}
                                        <template x-if="!editMode">
                                            <div>
                                                <div class="row align-items-start g-3">
                                                    <div class="col-auto d-flex align-items-center">
                                                        <i class="fas fa-grip-vertical text-muted cycle-drag-handle me-3"
                                                           style="cursor: grab; font-size: 1.3rem;"></i>
                                                        <div class="badge bg-primary d-flex align-items-center justify-content-center"
                                                             style="font-size: 0.95rem; width: 38px; height: 38px; border-radius: 8px; font-weight: 700;"
                                                             x-text="'#' + (cycle.sort_order || (index + 1))"></div>
                                                    </div>
                                                    <div class="col">
                                                        {{-- Başlık & Badge --}}
                                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                                                            <h4 class="mb-0 fw-bold" x-text="cycle.label?.tr || key"></h4>
                                                            <template x-if="cycle.badge?.text">
                                                                <span class="badge fs-6"
                                                                      :class="'bg-' + (cycle.badge.color || 'info')"
                                                                      x-text="cycle.badge.text"></span>
                                                            </template>
                                                        </div>

                                                        {{-- Info Badges --}}
                                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                                            {{-- Gün Sayısı --}}
                                                            <div class="badge badge-outline text-azure d-flex align-items-center gap-2 px-3 py-2">
                                                                <i class="fas fa-clock"></i>
                                                                <span><span x-text="cycle.duration_days"></span> gün</span>
                                                            </div>

                                                            {{-- Fiyat --}}
                                                            <div class="badge badge-outline text-green d-flex align-items-center gap-2 px-3 py-2">
                                                                <i class="fas fa-tag"></i>
                                                                <span>₺<span x-text="Number(cycle.price).toFixed(2)"></span></span>
                                                            </div>

                                                            {{-- Eski Fiyat --}}
                                                            <template x-if="cycle.compare_price">
                                                                <div class="badge badge-outline text-orange d-flex align-items-center gap-2 px-3 py-2">
                                                                    <i class="fas fa-percent"></i>
                                                                    <span>Eski: ₺<span x-text="Number(cycle.compare_price).toFixed(2)"></span></span>
                                                                </div>
                                                            </template>

                                                            {{-- Deneme Süresi --}}
                                                            <template x-if="cycle.trial_days">
                                                                <div class="badge badge-outline text-purple d-flex align-items-center gap-2 px-3 py-2">
                                                                    <i class="fas fa-gift"></i>
                                                                    <span><span x-text="cycle.trial_days"></span> gün deneme</span>
                                                                </div>
                                                            </template>
                                                        </div>

                                                        {{-- Promosyon Yazısı --}}
                                                        <template x-if="cycle.promo_text?.tr">
                                                            <div class="mt-2">
                                                                <div class="badge bg-warning-lt d-inline-flex align-items-center gap-2 px-3 py-2" style="font-size: 0.9rem;">
                                                                    <i class="fas fa-bullhorn"></i>
                                                                    <span x-text="cycle.promo_text.tr"></span>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>

                                                    {{-- Action Buttons --}}
                                                    <div class="col-auto">
                                                        <div class="btn-group">
                                                            <button type="button"
                                                                    class="btn btn-icon btn-primary"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Düzenle"
                                                                    @click="editMode = true; editData = JSON.parse(JSON.stringify({
                                                                        label_tr: cycle.label?.tr || '',
                                                                        label_en: cycle.label?.en || '',
                                                                        price: cycle.price || '',
                                                                        compare_price: cycle.compare_price || '',
                                                                        duration_days: cycle.duration_days || 30,
                                                                        trial_days: cycle.trial_days || '',
                                                                        badge_text: cycle.badge?.text || '',
                                                                        badge_color: cycle.badge?.color || '',
                                                                        promo_text_tr: cycle.promo_text?.tr || '',
                                                                        sort_order: cycle.sort_order || (index + 1)
                                                                    }))">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button"
                                                                    class="btn btn-icon btn-danger"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Sil"
                                                                    @click="if(confirm('Bu cycle\'ı silmek istediğinize emin misiniz?')) removeCycle(key)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- EDIT MODE --}}
                                        <template x-if="editMode">
                                            <div class="p-4 bg-body-tertiary rounded">
                                                <div class="d-flex align-items-center mb-4">
                                                    <i class="fas fa-edit text-primary fs-3 me-3"></i>
                                                    <h5 class="mb-0 fw-bold">Cycle Düzenle</h5>
                                                </div>

                                                <div class="row g-3">
                                                    {{-- Temel Bilgiler --}}
                                                    <div class="col-12">
                                                        <div class="card bg-azure-lt mb-3">
                                                            <div class="card-body p-3">
                                                                <h6 class="text-azure mb-3 fw-semibold">
                                                                    <i class="fas fa-info-circle me-2"></i>
                                                                    Temel Bilgiler
                                                                </h6>
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label required">Süre Adı (TR)</label>
                                                                        <input type="text" class="form-control" x-model="editData.label_tr" placeholder="Örn: Aylık, Yıllık">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label required">Gün Sayısı</label>
                                                                        <input type="number" class="form-control" x-model="editData.duration_days" min="1" placeholder="30">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Fiyatlandırma --}}
                                                    <div class="col-12">
                                                        <div class="card bg-green-lt mb-3">
                                                            <div class="card-body p-3">
                                                                <h6 class="text-green mb-3 fw-semibold">
                                                                    <i class="fas fa-tag me-2"></i>
                                                                    Fiyatlandırma
                                                                </h6>
                                                                <div class="row g-3">
                                                                    <div :class="@js($inputs['is_trial']) ? 'col-md-4' : 'col-md-6'">
                                                                        <label class="form-label required">Fiyat (₺)</label>
                                                                        <input type="number" class="form-control" x-model="editData.price" step="0.01" placeholder="99.90">
                                                                    </div>
                                                                    <div :class="@js($inputs['is_trial']) ? 'col-md-4' : 'col-md-6'">
                                                                        <label class="form-label">Üstü Çizili Fiyat (₺)</label>
                                                                        <input type="number" class="form-control" x-model="editData.compare_price" step="0.01" placeholder="120.00">
                                                                    </div>
                                                                    <template x-if="@js($inputs['is_trial'])">
                                                                        <div class="col-md-4">
                                                                            <label class="form-label">Deneme Süresi (gün)</label>
                                                                            <input type="number" class="form-control" x-model="editData.trial_days" min="0" placeholder="7">
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Ekstra Özellikler --}}
                                                    <div class="col-12">
                                                        <div class="card bg-purple-lt mb-3">
                                                            <div class="card-body p-3">
                                                                <h6 class="text-purple mb-3 fw-semibold">
                                                                    <i class="fas fa-star me-2"></i>
                                                                    Ekstra Özellikler
                                                                </h6>
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Badge Yazısı</label>
                                                                        <input type="text" class="form-control" x-model="editData.badge_text" placeholder="Popüler, En Avantajlı">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Badge Rengi</label>
                                                                        <select class="form-select" x-model="editData.badge_color">
                                                                            <option value="">Seç</option>
                                                                            <option value="primary">🔵 Mavi</option>
                                                                            <option value="success">🟢 Yeşil</option>
                                                                            <option value="warning">🟡 Sarı</option>
                                                                            <option value="danger">🔴 Kırmızı</option>
                                                                            <option value="info">🔷 Açık Mavi</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="form-label">Promosyon Yazısı</label>
                                                                        <input type="text" class="form-control" x-model="editData.promo_text_tr" placeholder="2 ay bedava!, İlk ay %20 indirim">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Action Buttons --}}
                                                <div class="mt-4 d-flex gap-2">
                                                    <button type="button"
                                                            class="btn btn-primary px-4"
                                                            @click="@this.call('updateCycle', key, editData); editMode = false">
                                                        <i class="fas fa-save me-2"></i>
                                                        Kaydet
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-secondary px-4"
                                                            @click="editMode = false">
                                                        <i class="fas fa-times me-2"></i>
                                                        İptal
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Deneme Üyeliği Uyarısı --}}
                    <template x-if="@js($inputs['is_trial']) && Object.keys(cycles).length >= 1">
                        <div class="alert alert-warning d-flex align-items-center gap-3 mb-0">
                            <i class="fas fa-info-circle fs-3"></i>
                            <div>
                                <h4 class="alert-title">Deneme Üyeliği Limiti</h4>
                                <div class="text-muted">Deneme üyeliği için sadece 1 süre eklenebilir. Daha fazla ekleme yapılamaz.</div>
                            </div>
                        </div>
                    </template>

                    {{-- Yeni Cycle Ekleme Formu --}}
                    <div class="card border-dashed shadow-sm" x-show="!@js($inputs['is_trial']) || Object.keys(cycles).length < 1">
                        <div class="card-body p-4">
                            <h4 class="mb-4 d-flex align-items-center gap-2">
                                <i class="fas fa-plus-circle text-primary"></i>
                                <span>Yeni Süre Ekle</span>
                                <template x-if="@js($inputs['is_trial'])">
                                    <span class="badge bg-info">Deneme Üyeliği</span>
                                </template>
                            </h4>

                            {{-- Bölüm 1: Temel Bilgiler --}}
                            <div class="card bg-primary-lt mb-3">
                                <div class="card-body p-3">
                                    <h6 class="text-primary mb-3 fw-semibold d-flex align-items-center gap-2">
                                        <span class="badge bg-primary">1</span>
                                        <span>Temel Bilgiler</span>
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label required">Süre Adı (TR)</label>
                                            <input type="text"
                                                   class="form-control"
                                                   x-model="newCycle.label_tr"
                                                   placeholder="Örn: Aylık, 15 Günlük, 6 Aylık">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required">Gün Sayısı</label>
                                            <input type="number"
                                                   class="form-control"
                                                   x-model="newCycle.duration_days"
                                                   placeholder="30"
                                                   min="1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Bölüm 2: Fiyatlandırma --}}
                            <div class="card bg-success-lt mb-3">
                                <div class="card-body p-3">
                                    <h6 class="text-success mb-3 fw-semibold d-flex align-items-center gap-2">
                                        <span class="badge bg-success">2</span>
                                        <span>Fiyatlandırma</span>
                                    </h6>
                                    <div class="row g-3">
                                        <div :class="@js($inputs['is_trial']) ? 'col-md-4' : 'col-md-6'">
                                            <label class="form-label required">Fiyat (₺)</label>
                                            <input type="number"
                                                   class="form-control"
                                                   x-model="newCycle.price"
                                                   placeholder="99.90"
                                                   min="0"
                                                   step="0.01">
                                        </div>
                                        <div :class="@js($inputs['is_trial']) ? 'col-md-4' : 'col-md-6'">
                                            <label class="form-label">
                                                Üstü Çizili Fiyat (₺)
                                                <span class="badge bg-secondary ms-1 badge-sm">Opsiyonel</span>
                                            </label>
                                            <input type="number"
                                                   class="form-control"
                                                   x-model="newCycle.compare_price"
                                                   placeholder="120.00"
                                                   min="0"
                                                   step="0.01">
                                        </div>
                                        <template x-if="@js($inputs['is_trial'])">
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    Deneme Süresi (gün)
                                                    <span class="badge bg-info ms-1 badge-sm">Deneme Üyeliği</span>
                                                </label>
                                                <input type="number"
                                                       class="form-control"
                                                       x-model="newCycle.trial_days"
                                                       placeholder="7"
                                                       min="0">
                                            </div>
                                        </template>
                                    </div>
                                    <small class="form-hint d-flex align-items-center gap-2 mt-2">
                                        <i class="fas fa-lightbulb"></i>
                                        <span>Üstü çizili fiyat, kullanıcıya tasarruf miktarını gösterir</span>
                                    </small>
                                </div>
                            </div>

                            {{-- Bölüm 3: Ekstra Özellikler --}}
                            <div class="card bg-warning-lt mb-3">
                                <div class="card-body p-3">
                                    <h6 class="text-warning mb-3 fw-semibold d-flex align-items-center gap-2">
                                        <span class="badge bg-warning">3</span>
                                        <span>Ekstra Özellikler</span>
                                        <span class="badge bg-secondary badge-sm">Tümü Opsiyonel</span>
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Badge Yazısı</label>
                                            <input type="text"
                                                   class="form-control"
                                                   x-model="newCycle.badge_text"
                                                   placeholder="Popüler, En Avantajlı, %20 İndirim">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Badge Rengi</label>
                                            <select class="form-select" x-model="newCycle.badge_color">
                                                <option value="">Seç</option>
                                                <option value="primary">🔵 Mavi</option>
                                                <option value="success">🟢 Yeşil</option>
                                                <option value="warning">🟡 Sarı</option>
                                                <option value="danger">🔴 Kırmızı</option>
                                                <option value="info">🔷 Açık Mavi</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Promosyon Yazısı</label>
                                            <input type="text"
                                                   class="form-control"
                                                   x-model="newCycle.promo_text_tr"
                                                   placeholder="2 ay bedava!, İlk ay %20 indirim, Yıllık pakette 2 ay hediye">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <button type="button"
                                    class="btn btn-primary w-100 py-3"
                                    @click="addCycle()"
                                    :disabled="!newCycle.label_tr || !newCycle.price || !newCycle.duration_days">
                                <i class="fas fa-check me-2"></i>
                                <span class="fw-semibold">Cycle Ekle</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ÖZELLİKLER (FEATURES) --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                        <i class="fas fa-list-check text-success"></i>
                        <span>{{ __('subscription::admin.features') }}</span>
                    </h3>
                </div>
                <div class="card-body p-4">
                    {{-- Mevcut Features --}}
                    <template x-if="features.length > 0">
                        <ul class="list-group mb-4" id="features-sortable-list" x-ref="featureList">
                            <template x-for="(feature, index) in features" :key="index">
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3" :data-index="index">
                                    <div class="d-flex align-items-center gap-3 flex-grow-1">
                                        <i class="fas fa-grip-vertical text-muted feature-drag-handle" style="cursor: grab; font-size: 1.2rem;"></i>
                                        <i :class="getFeatureIcon(feature)" class="text-success" style="font-size: 1.3rem;"></i>
                                        <span class="fw-medium" x-text="getFeatureText(feature)"></span>
                                    </div>
                                    <button type="button"
                                            class="btn btn-icon btn-danger"
                                            data-bs-toggle="tooltip"
                                            title="Sil"
                                            @click="removeFeature(index)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </template>

                    {{-- Yeni Feature Ekleme --}}
                    <div class="card bg-body-tertiary">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-icons me-2 text-muted"></i>
                                        İkon Class
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           x-model="newFeature.icon"
                                           placeholder="fas fa-check">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-text me-2 text-muted"></i>
                                        Özellik Metni
                                    </label>
                                    <div class="input-group">
                                        <input type="text"
                                               class="form-control"
                                               x-model="newFeature.text"
                                               @keydown.enter.prevent="addFeature"
                                               placeholder="Sınırsız ürün, 7/24 destek, vb.">
                                        <button type="button"
                                                class="btn btn-primary px-4"
                                                @click="addFeature"
                                                :disabled="!newFeature.text">
                                            <i class="fas fa-plus me-1"></i>
                                            Ekle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- AYARLAR --}}
            <div class="card mb-4 shadow-sm sticky-top" style="top: 1rem;">
                <div class="card-header">
                    <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                        <i class="fas fa-cog text-secondary"></i>
                        <span>{{ __('admin.settings') }}</span>
                    </h3>
                </div>
                <div class="card-body p-4">
                    {{-- Cihaz Limiti --}}
                    <div class="mb-4">
                        <label class="form-label required fw-semibold">
                            <i class="fas fa-mobile-alt me-2 text-muted"></i>
                            {{ __('subscription::admin.device_limit') }}
                        </label>
                        <input type="number"
                               class="form-control"
                               wire:model="inputs.device_limit"
                               min="1"
                               placeholder="1">
                        <small class="form-hint mt-1">Kullanıcı kaç cihazda aynı anda kullanabilir</small>
                    </div>

                    <hr class="my-4">

                    {{-- Deneme Üyeliği --}}
                    <div class="mb-3">
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                            <label class="form-selectgroup-item flex-fill">
                                <input type="checkbox"
                                       class="form-selectgroup-input"
                                       wire:model.live="inputs.is_trial">
                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                    <div class="me-3">
                                        <span class="form-selectgroup-check"></span>
                                    </div>
                                    <div class="form-selectgroup-label-content d-flex align-items-center">
                                        <span class="avatar avatar-sm me-3 bg-info-lt">
                                            <i class="fas fa-gift text-info"></i>
                                        </span>
                                        <div>
                                            <div class="font-weight-medium">Deneme Üyeliği</div>
                                            <div class="text-muted small">Bu plan deneme üyeliği için (max 1 süre)</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Öne Çıkan --}}
                    <div class="mb-3">
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                            <label class="form-selectgroup-item flex-fill">
                                <input type="checkbox"
                                       class="form-selectgroup-input"
                                       wire:model.live="inputs.is_featured">
                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                    <div class="me-3">
                                        <span class="form-selectgroup-check"></span>
                                    </div>
                                    <div class="form-selectgroup-label-content d-flex align-items-center">
                                        <span class="avatar avatar-sm me-3 bg-warning-lt">
                                            <i class="fas fa-star text-warning"></i>
                                        </span>
                                        <div>
                                            <div class="font-weight-medium">{{ __('subscription::admin.is_featured') }}</div>
                                            <div class="text-muted small">Bu plan öne çıkan olarak işaretlenecek</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Aktif --}}
                    <div class="mb-0">
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                            <label class="form-selectgroup-item flex-fill">
                                <input type="checkbox"
                                       class="form-selectgroup-input"
                                       wire:model.live="inputs.is_active">
                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                    <div class="me-3">
                                        <span class="form-selectgroup-check"></span>
                                    </div>
                                    <div class="form-selectgroup-label-content d-flex align-items-center">
                                        <span class="avatar avatar-sm me-3 bg-success-lt">
                                            <i class="fas fa-toggle-on text-success"></i>
                                        </span>
                                        <div>
                                            <div class="font-weight-medium">{{ __('admin.active') }}</div>
                                            <div class="text-muted small">Plan aktif ve satın alınabilir durumda</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="card-footer bg-body-tertiary p-3">
                    <button type="button"
                            class="btn btn-primary w-100 py-3"
                            wire:click="save">
                        <i class="fas fa-save me-2"></i>
                        <span class="fw-semibold">Değişiklikleri Kaydet</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('cycleManager', (initialCycles, initialFeatures) => ({
            cycles: initialCycles || {},
            features: initialFeatures || [],

            newCycle: {
                label_tr: '',
                price: '',
                duration_days: 30,
                compare_price: '',
                trial_days: '',
                badge_text: '',
                badge_color: '',
                promo_text_tr: '',
            },

            newFeature: {
                icon: 'fas fa-check',
                text: ''
            },

            init() {
                this.$nextTick(() => {
                    this.initFeaturesSortable();
                    this.initCyclesSortable();
                });

                this.$watch('features', () => {
                    this.$nextTick(() => {
                        this.initFeaturesSortable();
                    });
                });

                this.$watch('cycles', () => {
                    this.$nextTick(() => {
                        this.initCyclesSortable();
                    });
                });
            },

            initFeaturesSortable() {
                const listEl = document.getElementById('features-sortable-list');
                if (listEl && this.features.length > 0) {
                    if (listEl.sortableInstance) {
                        listEl.sortableInstance.destroy();
                    }

                    listEl.sortableInstance = new Sortable(listEl, {
                        handle: '.feature-drag-handle',
                        animation: 150,
                        onEnd: (evt) => {
                            const oldIndex = evt.oldIndex;
                            const newIndex = evt.newIndex;

                            if (oldIndex !== newIndex) {
                                const item = this.features.splice(oldIndex, 1)[0];
                                this.features.splice(newIndex, 0, item);
                            }
                        }
                    });
                }
            },

            initCyclesSortable() {
                const listEl = document.getElementById('cycles-sortable-list');
                if (listEl && Object.keys(this.cycles).length > 0) {
                    if (listEl.sortableInstance) {
                        listEl.sortableInstance.destroy();
                    }

                    listEl.sortableInstance = new Sortable(listEl, {
                        handle: '.cycle-drag-handle',
                        animation: 150,
                        onEnd: (evt) => {
                            const oldIndex = evt.oldIndex;
                            const newIndex = evt.newIndex;

                            if (oldIndex !== newIndex) {
                                const entries = Object.entries(this.cycles);
                                const [movedEntry] = entries.splice(oldIndex, 1);
                                entries.splice(newIndex, 0, movedEntry);

                                const newCycles = {};
                                entries.forEach(([key, value], index) => {
                                    newCycles[key] = {...value, sort_order: index + 1};
                                });

                                this.cycles = newCycles;
                            }
                        }
                    });
                }
            },

            addCycle() {
                if (!this.newCycle.label_tr || !this.newCycle.price || !this.newCycle.duration_days) {
                    return;
                }

                @this.call('addCycle', this.newCycle);

                this.newCycle = {
                    label_tr: '',
                    price: '',
                    duration_days: 30,
                    compare_price: '',
                    trial_days: '',
                    badge_text: '',
                    badge_color: '',
                    promo_text_tr: '',
                };
            },

            removeCycle(key) {
                @this.call('removeCycle', key);
            },

            addFeature() {
                const text = this.newFeature.text.trim();
                const icon = this.newFeature.icon.trim() || 'fas fa-check';

                if (text) {
                    this.features.push(`${icon}|${text}`);
                    this.newFeature.text = '';
                    this.newFeature.icon = 'fas fa-check';
                }
            },

            removeFeature(index) {
                this.features.splice(index, 1);
            },

            getFeatureIcon(feature) {
                if (typeof feature === 'string' && feature.includes('|')) {
                    return feature.split('|')[0];
                }
                return 'fas fa-check';
            },

            getFeatureText(feature) {
                if (typeof feature === 'string' && feature.includes('|')) {
                    return feature.split('|')[1];
                }
                return feature;
            }
        }));
    });
</script>
@endpush
