<div x-data="{
    showPreview: false,
    showEdit: false,
    previewData: null,
    editData: null,
    testEmail: '{{ $testEmail }}',
    activeCategory: 'all',

    openPreview(template) {
        this.previewData = template;
        this.showPreview = true;
    },

    openEdit(template) {
        this.editData = {
            key: template.key,
            name: template.name,
            category: template.category,
            is_active: template.is_active,
            subject_tr: template.subject?.tr || '',
            subject_en: template.subject?.en || '',
            content_tr: template.content?.tr || '',
            content_en: template.content?.en || ''
        };
        this.showEdit = true;
    },

    saveTemplate() {
        $wire.saveTemplateFromAlpine(this.editData).then(() => {
            this.showEdit = false;
        });
    },

    sendTest(key) {
        $wire.sendTest(key, this.testEmail);
    },

    filterByCategory(category) {
        return this.activeCategory === 'all' || category === this.activeCategory;
    }
}">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-envelope me-2"></i>
                Mail Şablonları
            </h3>
            <div class="card-actions">
                <div class="input-icon" style="width: 250px;">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Şablon ara...">
                </div>
            </div>
        </div>

        <!-- Kategori Sekmeleri -->
        <div class="card-body border-bottom py-3">
            <div class="d-flex flex-wrap gap-2">
                <button type="button"
                    class="btn"
                    :class="activeCategory === 'all' ? 'btn-primary' : 'btn-outline-primary'"
                    @click="activeCategory = 'all'">
                    <i class="fas fa-th-large me-1"></i> Tümü
                </button>
                <button type="button"
                    class="btn"
                    :class="activeCategory === 'auth' ? 'btn-primary' : 'btn-outline-primary'"
                    @click="activeCategory = 'auth'">
                    <i class="fas fa-shield-halved me-1"></i> Kimlik Doğrulama
                </button>
                <button type="button"
                    class="btn"
                    :class="activeCategory === 'payment' ? 'btn-primary' : 'btn-outline-primary'"
                    @click="activeCategory = 'payment'">
                    <i class="fas fa-credit-card me-1"></i> Ödeme
                </button>
                <button type="button"
                    class="btn"
                    :class="activeCategory === 'subscription' ? 'btn-primary' : 'btn-outline-primary'"
                    @click="activeCategory = 'subscription'">
                    <i class="fas fa-rotate me-1"></i> Abonelik
                </button>
                <button type="button"
                    class="btn"
                    :class="activeCategory === 'corporate' ? 'btn-primary' : 'btn-outline-primary'"
                    @click="activeCategory = 'corporate'">
                    <i class="fas fa-building me-1"></i> Kurumsal
                </button>
            </div>
        </div>

        <div class="card-body">
            @php
                $groupedTemplates = collect($this->templates)->groupBy('category');
                $categoryOrder = ['auth', 'payment', 'subscription', 'corporate'];
                $categoryNames = [
                    'auth' => 'Kimlik Doğrulama',
                    'payment' => 'Ödeme',
                    'subscription' => 'Abonelik',
                    'corporate' => 'Kurumsal',
                ];
                $icons = [
                    'auth' => 'fa-shield-halved',
                    'payment' => 'fa-credit-card',
                    'subscription' => 'fa-rotate',
                    'corporate' => 'fa-building',
                ];
                $colors = [
                    'auth' => 'blue',
                    'payment' => 'green',
                    'subscription' => 'purple',
                    'corporate' => 'orange',
                ];
            @endphp

            <div class="row g-3">
                @foreach($categoryOrder as $cat)
                    @if($groupedTemplates->has($cat))
                        <!-- Kategori Başlığı -->
                        <div class="col-12" x-show="activeCategory === 'all' || activeCategory === '{{ $cat }}'" x-transition>
                            <div class="d-flex align-items-center p-2 bg-{{ $colors[$cat] }}-lt rounded" x-show="activeCategory === 'all'">
                                <i class="fas {{ $icons[$cat] }} me-2 text-{{ $colors[$cat] }}"></i>
                                <h3 class="mb-0 h4 text-{{ $colors[$cat] }}">{{ $categoryNames[$cat] }}</h3>
                                <div class="ms-auto">
                                    <span class="badge bg-{{ $colors[$cat] }}">
                                        {{ $groupedTemplates[$cat]->count() }} şablon
                                    </span>
                                </div>
                            </div>
                        </div>

                        @foreach($groupedTemplates[$cat] as $template)
                            @php
                                $category = $template['category'] ?? 'system';
                                $categoryIcon = $icons[$category] ?? 'fa-envelope';
                                $categoryColor = $colors[$category] ?? 'secondary';
                                $isOverridden = $template['is_overridden'] ?? false;
                            @endphp
                            <div class="col-md-6 col-lg-4" x-show="activeCategory === 'all' || activeCategory === '{{ $category }}'" x-transition>
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar bg-{{ $categoryColor }}-lt me-3">
                                        <i class="fas {{ $categoryIcon }} text-{{ $categoryColor }}"></i>
                                    </span>
                                    <div class="flex-fill">
                                        <div class="font-weight-medium">{{ $template['name'] ?? 'İsimsiz' }}</div>
                                        <div class="text-muted small">{{ $template['key'] ?? '' }}</div>
                                    </div>
                                    @if($isOverridden)
                                        <span class="badge bg-warning-lt">Özel</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer d-flex gap-2">
                                <button @click="openPreview(@js($template))" class="btn btn-primary flex-fill">
                                    <i class="fas fa-eye me-1"></i> Önizle
                                </button>
                                <button @click="openEdit(@js($template))" class="btn btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click="sendTest('{{ $template['key'] }}')" class="btn btn-outline-success">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                                @if($isOverridden)
                                    <button wire:click="resetToDefault('{{ $template['key'] }}')"
                                            wire:confirm="Varsayılana döndürülsün mü?"
                                            class="btn btn-outline-warning">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                        @endforeach
                    @endif
                @endforeach

                @if($groupedTemplates->isEmpty())
                    <div class="col-12">
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="fas fa-envelope fa-3x text-muted"></i>
                            </div>
                            <p class="empty-title">Şablon bulunamadı</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Test Email -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <i class="fas fa-flask fa-2x text-muted"></i>
                </div>
                <div class="col">
                    <label class="form-label mb-1">Test E-posta Adresi</label>
                    <input type="email" x-model="testEmail" class="form-control" placeholder="test@example.com">
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal (Alpine) -->
    <template x-if="showPreview && previewData">
        <div>
            <div class="modal modal-blur fade show" style="display: block;" tabindex="-1" @click.self="showPreview = false">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" x-text="previewData.name"></h5>
                            <button type="button" class="btn-close" @click="showPreview = false"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="bg-light p-4">
                                <div class="bg-white rounded shadow-sm p-4" x-html="previewData.content?.tr || ''"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn" @click="showPreview = false">Kapat</button>
                            <button type="button" class="btn btn-success" @click="sendTest(previewData.key)">
                                <i class="fas fa-paper-plane me-1"></i> Test Gönder
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show" @click="showPreview = false"></div>
        </div>
    </template>

    <!-- Edit Modal (Alpine) -->
    <template x-if="showEdit && editData">
        <div>
            <div class="modal modal-blur fade show" style="display: block;" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Şablon Düzenle: <span x-text="editData.name"></span></h5>
                            <button type="button" class="btn-close" @click="showEdit = false"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Şablon Adı</label>
                                    <input type="text" x-model="editData.name" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kategori</label>
                                    <select x-model="editData.category" class="form-select">
                                        <option value="system">Sistem</option>
                                        <option value="auth">Kimlik Doğrulama</option>
                                        <option value="payment">Ödeme</option>
                                        <option value="subscription">Abonelik</option>
                                        <option value="corporate">Kurumsal</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Durum</label>
                                    <label class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" x-model="editData.is_active">
                                        <span class="form-check-label">Aktif</span>
                                    </label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="mb-3">Türkçe</h4>
                                    <div class="mb-3">
                                        <label class="form-label">Konu</label>
                                        <input type="text" x-model="editData.subject_tr" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">İçerik</label>
                                        <textarea x-model="editData.content_tr" class="form-control" rows="10" style="font-family: monospace; font-size: 12px;"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h4 class="mb-3">English</h4>
                                    <div class="mb-3">
                                        <label class="form-label">Subject</label>
                                        <input type="text" x-model="editData.subject_en" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Content</label>
                                        <textarea x-model="editData.content_en" class="form-control" rows="10" style="font-family: monospace; font-size: 12px;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn" @click="showEdit = false">İptal</button>
                            <button type="button" class="btn btn-primary" @click="saveTemplate()">
                                <i class="fas fa-save me-1"></i> Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>
    </template>
</div>
