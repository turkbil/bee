@include('subscription::admin.helper')

<div x-data="cycleManager(@entangle('cycles'), @entangle('features'))">
    <div class="row">
        <div class="col-lg-8">
            {{-- PLAN BİLGİLERİ --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle text-blue me-2"></i>
                        {{ __('subscription::admin.plan_information') }}
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $defaultLang = $availableLanguages[0] ?? 'tr';
                    @endphp

                    {{-- Plan Adı (TR) --}}
                    <div class="mb-3">
                        <label class="form-label required">{{ __('subscription::admin.plan_name') }}</label>
                        <input type="text"
                               class="form-control @error('multiLangInputs.'.$defaultLang.'.title') is-invalid @enderror"
                               wire:model="multiLangInputs.{{ $defaultLang }}.title"
                               placeholder="Örn: Premium, Gold, Silver">
                        @error('multiLangInputs.'.$defaultLang.'.title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Açıklama (TR) --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.description') }}</label>
                        <textarea class="form-control"
                                  wire:model="multiLangInputs.{{ $defaultLang }}.description"
                                  rows="3"></textarea>
                    </div>

                    {{-- Slug --}}
                    <div class="mb-3">
                        <label class="form-label required">Slug</label>
                        <input type="text"
                               class="form-control @error('inputs.slug') is-invalid @enderror"
                               wire:model="inputs.slug"
                               @if(!$planId) readonly @endif
                               placeholder="premium-plan">
                        <small class="form-hint">
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
                </div>
            </div>

            {{-- BILLING CYCLES --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock text-warning me-2"></i>
                        Süre & Fiyat Seçenekleri
                    </h3>
                </div>
                <div class="card-body">
                    {{-- Mevcut Cycles --}}
                    <template x-if="Object.keys(cycles).length > 0">
                        <div class="mb-3" id="cycles-sortable-list">
                            <template x-for="(cycle, key) in Object.keys(cycles)" :key="key">
                                <div class="card mb-2" style="border-left: 3px solid #0d6efd; cursor: move;" :data-cycle-key="key">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <i class="fas fa-grip-vertical text-muted cycle-drag-handle" style="cursor: grab; font-size: 1.2rem;"></i>
                                            </div>
                                            <div class="col">
                                                <h4 class="mb-1" x-text="cycles[key].label?.tr || key"></h4>
                                                <div class="text-muted small">
                                                    <span x-text="cycles[key].duration_days"></span> gün •
                                                    ₺<span x-text="cycles[key].price"></span>
                                                    <template x-if="cycles[key].compare_price">
                                                        <span class="text-muted ms-2">
                                                            (Üstü çizili: ₺<span x-text="cycles[key].compare_price"></span>)
                                                        </span>
                                                    </template>
                                                    <template x-if="cycles[key].badge?.text">
                                                        <span class="badge ms-2" :class="'bg-' + (cycles[key].badge.color || 'info')" x-text="cycles[key].badge.text"></span>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        @click="removeCycle(key)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Yeni Cycle Ekleme Formu - Organize Edilmiş Versiyon --}}
                    <div class="card" style="background: #f8f9fa; border: 2px dashed #dee2e6;">
                        <div class="card-body">
                            <h4 class="mb-4">
                                <i class="fas fa-plus-circle text-primary me-2"></i>
                                Yeni Süre Ekle
                            </h4>

                            {{-- Bölüm 1: Temel Bilgiler --}}
                            <div class="p-3 mb-3 rounded" style="background: rgba(13, 110, 253, 0.05); border-left: 3px solid #0d6efd;">
                                <h5 class="text-primary mb-3" style="font-size: 1rem; font-weight: 600;">
                                    <span class="badge bg-primary me-2">1</span>
                                    Temel Bilgiler
                                </h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Süre Adı (TR)</label>
                                        <input type="text"
                                               class="form-control"
                                               x-model="newCycle.label_tr"
                                               placeholder="Örn: Aylık, 15 Günlük, 6 Aylık">
                                    </div>
                                    <div class="col-md-6 mb-0">
                                        <label class="form-label required">Gün Sayısı</label>
                                        <input type="number"
                                               class="form-control"
                                               x-model="newCycle.duration_days"
                                               placeholder="30"
                                               min="1">
                                    </div>
                                </div>
                            </div>

                            {{-- Bölüm 2: Fiyatlandırma --}}
                            <div class="p-3 mb-3 rounded" style="background: rgba(13, 110, 253, 0.05); border-left: 3px solid #0d6efd;">
                                <h5 class="text-primary mb-3" style="font-size: 1rem; font-weight: 600;">
                                    <span class="badge bg-primary me-2">2</span>
                                    Fiyatlandırma
                                </h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Fiyat (₺)</label>
                                        <input type="number"
                                               class="form-control"
                                               x-model="newCycle.price"
                                               placeholder="99"
                                               min="0"
                                               step="0.01">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            Üstü Çizili Fiyat (₺)
                                            <span class="badge bg-secondary ms-1" style="font-size: 0.7rem;">Opsiyonel</span>
                                        </label>
                                        <input type="number"
                                               class="form-control"
                                               x-model="newCycle.compare_price"
                                               placeholder="120"
                                               min="0"
                                               step="0.01">
                                    </div>
                                    <div class="col-md-4 mb-0">
                                        <label class="form-label">
                                            Deneme Süresi (gün)
                                            <span class="badge bg-secondary ms-1" style="font-size: 0.7rem;">Opsiyonel</span>
                                        </label>
                                        <input type="number"
                                               class="form-control"
                                               x-model="newCycle.trial_days"
                                               placeholder="7"
                                               min="0">
                                    </div>
                                </div>
                                <small class="text-muted d-block">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Üstü çizili fiyat, kullanıcıya tasarruf miktarını gösterir
                                </small>
                            </div>

                            {{-- Bölüm 3: Ekstra Özellikler --}}
                            <div class="p-3 mb-3 rounded" style="background: rgba(13, 110, 253, 0.05); border-left: 3px solid #0d6efd;">
                                <h5 class="text-primary mb-3" style="font-size: 1rem; font-weight: 600;">
                                    <span class="badge bg-primary me-2">3</span>
                                    Ekstra Özellikler
                                    <span class="badge bg-secondary ms-2" style="font-size: 0.75rem;">Tümü Opsiyonel</span>
                                </h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Badge Yazısı</label>
                                        <input type="text"
                                               class="form-control"
                                               x-model="newCycle.badge_text"
                                               placeholder="Popüler, En Avantajlı, %20 İndirim">
                                    </div>
                                    <div class="col-md-6 mb-3">
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
                                    <div class="col-12 mb-0">
                                        <label class="form-label">Promosyon Yazısı</label>
                                        <input type="text"
                                               class="form-control"
                                               x-model="newCycle.promo_text_tr"
                                               placeholder="2 ay bedava!, İlk ay %20 indirim, Yıllık pakette 2 ay hediye">
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <button type="button"
                                    class="btn btn-primary w-100"
                                    style="padding: 12px;"
                                    @click="addCycle()"
                                    :disabled="!newCycle.label_tr || !newCycle.price || !newCycle.duration_days">
                                <i class="fas fa-check me-2"></i>
                                Cycle Ekle
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ÖZELLİKLER (FEATURES) --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list-check text-success me-2"></i>
                        {{ __('subscription::admin.features') }}
                    </h3>
                </div>
                <div class="card-body">
                    {{-- Mevcut Features --}}
                    <template x-if="features.length > 0">
                        <ul class="list-group mb-3" id="features-sortable-list" x-ref="featureList">
                            <template x-for="(feature, index) in features" :key="index">
                                <li class="list-group-item d-flex justify-content-between align-items-center" :data-index="index">
                                    <div class="d-flex align-items-center gap-2 flex-grow-1">
                                        <i class="fas fa-grip-vertical text-muted feature-drag-handle" style="cursor: grab;"></i>
                                        <i :class="getFeatureIcon(feature)" class="text-success fs-4"></i>
                                        <span x-text="getFeatureText(feature)"></span>
                                    </div>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            @click="removeFeature(index)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </template>

                    {{-- Yeni Feature Ekleme --}}
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">İkon Class</label>
                            <input type="text"
                                   class="form-control"
                                   x-model="newFeature.icon"
                                   placeholder="fas fa-check">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Özellik Metni</label>
                            <div class="input-group">
                                <input type="text"
                                       class="form-control"
                                       x-model="newFeature.text"
                                       @keydown.enter.prevent="addFeature"
                                       placeholder="Sınırsız ürün">
                                <button type="button"
                                        class="btn btn-primary"
                                        @click="addFeature"
                                        :disabled="!newFeature.text">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- AYARLAR --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog text-secondary me-2"></i>
                        {{ __('admin.settings') }}
                    </h3>
                </div>
                <div class="card-body">
                    {{-- Cihaz Limiti --}}
                    <div class="mb-3">
                        <label class="form-label required">{{ __('subscription::admin.device_limit') }}</label>
                        <input type="number"
                               class="form-control"
                               wire:model="inputs.device_limit"
                               min="1">
                    </div>

                    {{-- Öne Çıkan --}}
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input type="checkbox"
                                   class="form-check-input"
                                   wire:model="inputs.is_featured">
                            <span class="form-check-label">
                                {{ __('subscription::admin.is_featured') }}
                            </span>
                        </label>
                    </div>

                    {{-- Aktif --}}
                    <div class="mb-0">
                        <label class="form-check form-switch">
                            <input type="checkbox"
                                   class="form-check-input"
                                   wire:model="inputs.is_active">
                            <span class="form-check-label">
                                {{ __('admin.active') }}
                            </span>
                        </label>
                    </div>
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
                // Features sortable initialize et
                this.$nextTick(() => {
                    this.initFeaturesSortable();
                    this.initCyclesSortable();
                });

                // Features değiştiğinde sortable'ı yeniden init et
                this.$watch('features', () => {
                    this.$nextTick(() => {
                        this.initFeaturesSortable();
                    });
                });

                // Cycles değiştiğinde sortable'ı yeniden init et
                this.$watch('cycles', () => {
                    this.$nextTick(() => {
                        this.initCyclesSortable();
                    });
                });
            },

            initFeaturesSortable() {
                const listEl = document.getElementById('features-sortable-list');
                if (listEl && this.features.length > 0) {
                    // Eski instance'ı destroy et
                    if (listEl.sortableInstance) {
                        listEl.sortableInstance.destroy();
                    }

                    // Yeni Sortable instance oluştur
                    listEl.sortableInstance = new Sortable(listEl, {
                        handle: '.feature-drag-handle',
                        animation: 150,
                        onEnd: (evt) => {
                            // Yeni sıralamayı al
                            const oldIndex = evt.oldIndex;
                            const newIndex = evt.newIndex;

                            if (oldIndex !== newIndex) {
                                // Array'i yeniden sırala
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
                    // Eski instance'ı destroy et
                    if (listEl.sortableInstance) {
                        listEl.sortableInstance.destroy();
                    }

                    // Yeni Sortable instance oluştur
                    listEl.sortableInstance = new Sortable(listEl, {
                        handle: '.cycle-drag-handle',
                        animation: 150,
                        onEnd: (evt) => {
                            // Yeni sıralamayı al
                            const oldIndex = evt.oldIndex;
                            const newIndex = evt.newIndex;

                            if (oldIndex !== newIndex) {
                                // Object'i array'e çevir, sırala, tekrar object'e çevir
                                const entries = Object.entries(this.cycles);
                                const [movedEntry] = entries.splice(oldIndex, 1);
                                entries.splice(newIndex, 0, movedEntry);

                                // Yeni object oluştur
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

                // Reset form
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
