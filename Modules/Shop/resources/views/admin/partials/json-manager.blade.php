{{-- JSON İçerik Yönetimi Komponenti --}}
<div class="card mt-4" x-data="jsonManager()">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
            <i class="fas fa-code me-2"></i>
            {{ __('shop::admin.json_content_management') }}
        </h3>
        <button type="button" class="btn btn-sm btn-outline-secondary" @click="toggleAllSections()">
            <i class="fas fa-expand-alt me-1"></i>
            <span x-text="allExpanded ? '{{ __('shop::admin.collapse_all') }}' : '{{ __('shop::admin.expand_all') }}'"></span>
        </button>
    </div>

    <div class="card-body">

        {{-- DİNAMİK KATEGORİLER (TENANT-DEFINED) --}}
        <div class="custom-categories-section mt-4" x-data="customCategoryManager()">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-layer-group me-2 text-primary"></i>
                    {{ __('shop::admin.json.custom_categories') }}
                </h5>
                <button type="button"
                    @click="showCategoryModal = true"
                    class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    {{ __('shop::admin.json.add_custom_category') }}
                </button>
            </div>

            {{-- Template Seçici --}}
            <div class="alert alert-primary d-flex align-items-center mb-3">
                <i class="fas fa-magic me-2"></i>
                <div class="flex-fill">
                    <strong>Hızlı Başlangıç:</strong> Hazır şablonlardan birini seçerek tüm alanları otomatik ekleyin
                </div>
                <select class="form-select form-select-sm w-auto ms-3"
                        @change="loadTemplate($event.target.value); $event.target.value = ''">
                    <option value="">Şablon Seç...</option>
                    @foreach(\Modules\Shop\App\Models\ShopProductFieldTemplate::active()->get() as $template)
                    <option value="{{ $template->template_id }}">
                        {{ $template->name }} ({{ count($template->fields) }} alan)
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Mevcut Özel Kategoriler --}}
            <div class="custom-categories-list">
                <template x-if="!$wire.customJsonFields || Object.keys($wire.customJsonFields).length === 0">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                        <p>{{ __('shop::admin.json.no_custom_categories') }}</p>
                        <small>{{ __('shop::admin.json.custom_categories_hint') }}</small>
                    </div>
                </template>

                <template x-for="(categoryData, categoryName) in $wire.customJsonFields" :key="categoryName">
                    <div class="json-section mb-3" x-data="{ open: false }">
                        <div class="section-header" @click="open = !open" style="cursor: pointer;">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border-start border-4 border-primary">
                                <div class="d-flex align-items-center">
                                    <i class="fas" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" style="width: 20px;"></i>
                                    <i class="fas fa-cube me-2 text-primary"></i>
                                    <strong x-text="categoryName"></strong>
                                    <span class="badge bg-primary ms-2" x-text="categoryData.items?.length || 0"></span>
                                    <span class="badge bg-secondary ms-2" x-text="(categoryData.fields?.length || 0) + ' alan'"></span>
                                </div>
                                <button type="button"
                                    @click.stop="deleteCustomCategory(categoryName)"
                                    class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div x-show="open" x-collapse class="section-content mt-2 p-3 border rounded">
                            {{-- Dinamik item render - field tanımına göre --}}
                            <div x-html="renderCategoryItems(categoryName, categoryData)"></div>

                            <button type="button"
                                @click="addItemToCustomCategory(categoryName)"
                                class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-plus me-1"></i>
                                {{ __('shop::admin.json.add_item') }}
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Kategori Ekleme Modal --}}
            <div class="modal fade" :class="{'show d-block': showCategoryModal}" x-show="showCategoryModal" tabindex="-1" style="background: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-layer-group me-2"></i>
                                {{ __('shop::admin.json.new_custom_category') }}
                            </h5>
                            <button type="button" class="btn-close" @click="showCategoryModal = false"></button>
                        </div>
                        <div class="modal-body">
                            {{-- Kategori Adı --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('shop::admin.json.category_name') }}</label>
                                <input type="text"
                                    x-model="newCategory.name"
                                    class="form-control"
                                    placeholder="{{ __('shop::admin.json.category_name_placeholder') }}">
                            </div>

                            {{-- Alanlar Listesi --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Alanlar</label>
                                <div class="alert alert-info py-2 px-3 small mb-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Alan isimleri girin: title, text, icon, url, description vs.
                                </div>

                                {{-- Mevcut Alanlar --}}
                                <div class="fields-list mb-2">
                                    <template x-for="(fieldName, index) in newCategory.fields" :key="'field-' + index">
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <div class="row align-items-center">
                                                    <div class="col-11">
                                                        <input type="text"
                                                            x-model="newCategory.fields[index]"
                                                            class="form-control form-control-sm"
                                                            placeholder="Alan adı (örn: title, description, icon, url)">
                                                    </div>
                                                    <div class="col-1">
                                                        <button type="button"
                                                            @click="newCategory.fields.splice(index, 1)"
                                                            class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Boş Durum --}}
                                    <template x-if="newCategory.fields.length === 0">
                                        <div class="text-center text-muted py-3 border rounded">
                                            <i class="fas fa-cube fa-2x mb-2 opacity-25"></i>
                                            <p class="mb-0 small">Henüz alan eklenmedi</p>
                                        </div>
                                    </template>
                                </div>

                                {{-- Alan Ekle Butonu --}}
                                <button type="button"
                                    @click="newCategory.fields.push('')"
                                    class="btn btn-sm btn-outline-primary w-100">
                                    <i class="fas fa-plus me-1"></i>
                                    Yeni Alan Ekle
                                </button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="showCategoryModal = false">
                                {{ __('admin.cancel') }}
                            </button>
                            <button type="button"
                                class="btn btn-primary"
                                @click="createCustomCategory()"
                                :disabled="!newCategory.name || newCategory.fields.length === 0">
                                <i class="fas fa-check me-1"></i>
                                {{ __('admin.create') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function jsonManager() {
    return {
        allExpanded: false,

        toggleAllSections() {
            this.allExpanded = !this.allExpanded;
            // Tüm section'ları aç/kapat
            Alpine.store('sections', this.allExpanded);
        },

    }
}

function customCategoryManager() {
    return {
        showCategoryModal: false,
        newCategory: {
            name: '',
            fields: []  // ['title', 'description', 'icon']
        },
        templates: @json(\Modules\Shop\App\Models\ShopProductFieldTemplate::active()->get()),

        init() {
            console.log('🎯 customCategoryManager INIT');
            console.log('📦 $wire:', this.$wire);
            console.log('📦 customJsonFields:', this.$wire.customJsonFields);
            console.log('📋 templates:', this.templates);
        },

        // Template'ten field'ları yükle
        loadTemplate(templateId) {
            if (!templateId) return;

            const template = this.templates.find(t => t.template_id == templateId);
            if (!template) {
                console.error('Template bulunamadı:', templateId);
                return;
            }

            console.log('🎯 Template yükleniyor:', template.name);

            // customJsonFields'ı object'e çevir
            if (Array.isArray(this.$wire.customJsonFields)) {
                this.$wire.customJsonFields = {};
            }

            // Template'teki her field definition için kategori oluştur
            const categoryName = template.name;

            // Field definitions'ı sakla (name + type bilgisiyle)
            const fields = template.fields.map(f => ({
                name: f.name,
                type: f.type  // input, textarea, checkbox
            }));

            // Kategoriyi ekle
            this.$wire.customJsonFields[categoryName] = {
                fields: fields,  // [{ name: 'author', type: 'input' }, ...]
                items: []
            };

            console.log('✅ Template yüklendi:', categoryName, 'Fields:', fields);

            // Toast göster
            if (window.showToast) {
                showToast(`"${template.name}" şablonu eklendi! ${fields.length} alan hazır.`, 'success');
            }
        },

        createCustomCategory() {
            console.log('🚀 createCustomCategory BAŞLADI');
            console.log('📝 newCategory:', this.newCategory);

            if (!this.newCategory.name || this.newCategory.fields.length === 0) {
                console.log('❌ Validation fail - name veya fields boş');
                return;
            }

            // Boş field isimleri filtrele
            const validFields = this.newCategory.fields.filter(f => f && f.trim());
            console.log('✅ Valid fields:', validFields);

            if (validFields.length === 0) {
                console.log('❌ Valid fields boş');
                return;
            }

            // Custom JSON fields'ın tipini kontrol et
            console.log('🔍 customJsonFields tipi:', Array.isArray(this.$wire.customJsonFields) ? 'ARRAY' : 'OBJECT');

            // ARRAY ise OBJE'ye çevir
            if (Array.isArray(this.$wire.customJsonFields)) {
                console.log('⚠️ customJsonFields bir ARRAY, OBJE\'ye çeviriliyor...');
                this.$wire.customJsonFields = {};
            }

            // Boş veya null ise obje yap
            if (!this.$wire.customJsonFields || typeof this.$wire.customJsonFields !== 'object') {
                console.log('⚠️ customJsonFields yok, oluşturuluyor');
                this.$wire.customJsonFields = {};
            }

            console.log('📦 ÖNCE customJsonFields:', JSON.parse(JSON.stringify(this.$wire.customJsonFields)));

            // Yeni kategori ekle
            this.$wire.customJsonFields[this.newCategory.name] = {
                fields: validFields,  // ['title', 'description', 'icon']
                items: []  // Boş başlar
            };

            console.log('📦 SONRA customJsonFields:', JSON.parse(JSON.stringify(this.$wire.customJsonFields)));
            console.log('✅ Kategori eklendi:', this.newCategory.name);

            // Modal'ı kapat ve formu temizle
            this.showCategoryModal = false;
            this.newCategory = { name: '', fields: [] };

            console.log('🎯 Modal kapatıldı');
        },

        deleteCustomCategory(categoryName) {
            if (confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')) {
                const fields = { ...this.$wire.customJsonFields };
                delete fields[categoryName];
                this.$wire.customJsonFields = fields;
            }
        },

        addItemToCustomCategory(categoryName) {
            const category = this.$wire.customJsonFields[categoryName];

            // Yeni item oluştur - her field için boş değer
            const newItem = {};
            category.fields.forEach(fieldName => {
                newItem[fieldName] = '';
            });

            category.items.push(newItem);
        },

        renderCategoryItems(categoryName, categoryData) {
            const fields = categoryData.fields;  // [{ name: 'author', type: 'input' }, ...]
            const items = categoryData.items;
            let html = '';

            if (!items || items.length === 0) {
                return '<div class="text-center text-muted py-3">Henüz item eklenmemiş</div>';
            }

            // Her item için card oluştur
            items.forEach((item, itemIndex) => {
                html += `
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center py-2 px-3">
                            <strong class="small">Item ${itemIndex + 1}</strong>
                            <button type="button"
                                @click="$wire.customJsonFields['${categoryName}'].items.splice(${itemIndex}, 1)"
                                class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="card-body p-3">
                `;

                // Her field için type'a göre input oluştur - Grid: 6'lı yan yana
                html += '<div class="row">';
                fields.forEach(field => {
                    // String mi object mi kontrol et (eski format vs yeni format)
                    const fieldName = typeof field === 'object' ? field.name : field;
                    const fieldType = typeof field === 'object' ? field.type : 'input';
                    const escapedFieldName = fieldName.replace(/'/g, "\\'");

                    // Field Type'a göre render
                    if (fieldType === 'checkbox') {
                        // Checkbox - Yarım genişlik (col-md-6)
                        html += `
                            <div class="col-md-6 mb-2">
                                <label class="form-check form-switch">
                                    <input type="checkbox"
                                        class="form-check-input"
                                        x-model="$wire.customJsonFields['${categoryName}'].items[${itemIndex}]['${escapedFieldName}']">
                                    <span class="form-check-label">${fieldName}</span>
                                </label>
                            </div>
                        `;
                    } else if (fieldType === 'textarea') {
                        // Textarea - Tam genişlik (col-12)
                        html += `
                            <div class="col-12 mb-2">
                                <label class="form-label small mb-1">${fieldName}</label>
                                <textarea
                                    x-model="$wire.customJsonFields['${categoryName}'].items[${itemIndex}]['${escapedFieldName}']"
                                    class="form-control form-control-sm"
                                    rows="3"
                                    placeholder="${fieldName}"></textarea>
                            </div>
                        `;
                    } else {
                        // Input - Yarım genişlik (col-md-6)
                        html += `
                            <div class="col-md-6 mb-2">
                                <label class="form-label small mb-1">${fieldName}</label>
                                <input type="text"
                                    x-model="$wire.customJsonFields['${categoryName}'].items[${itemIndex}]['${escapedFieldName}']"
                                    class="form-control form-control-sm"
                                    placeholder="${fieldName}">
                            </div>
                        `;
                    }
                });
                html += '</div>';

                html += `
                        </div>
                    </div>
                `;
            });

            return html;
        },

        // ESKİ KOD - ÇALIŞMAZ, SİLİNECEK
        _oldRenderCode() {
            const type = 'UNUSED';
            const items = [];
            let html = '';

            if (type === 'simple_text') {
                // Basit metin listesi
                if (Array.isArray(items)) {
                    items.forEach((item, index) => {
                        html += `
                            <div class="card mb-2">
                                <div class="card-body p-2">
                                    <div class="row align-items-center">
                                        <div class="col-11">
                                            <input type="text"
                                                x-model="$wire.customJsonFields['${categoryName}'].items[${index}]"
                                                class="form-control form-control-sm"
                                                placeholder="Metin girin">
                                        </div>
                                        <div class="col-1">
                                            <button type="button"
                                                @click="$wire.customJsonFields['${categoryName}'].items.splice(${index}, 1)"
                                                class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
            } else if (type === 'icon_text') {
                // İkonlu liste
                if (Array.isArray(items)) {
                    items.forEach((item, index) => {
                        html += `
                            <div class="card mb-2">
                                <div class="card-body p-2">
                                    <div class="row align-items-center">
                                        <div class="col-3">
                                            <input type="text"
                                                x-model="$wire.customJsonFields['${categoryName}'].items[${index}].icon"
                                                class="form-control form-control-sm"
                                                placeholder="İkon">
                                        </div>
                                        <div class="col-8">
                                            <input type="text"
                                                x-model="$wire.customJsonFields['${categoryName}'].items[${index}].text"
                                                class="form-control form-control-sm"
                                                placeholder="Metin">
                                        </div>
                                        <div class="col-1">
                                            <button type="button"
                                                @click="$wire.customJsonFields['${categoryName}'].items.splice(${index}, 1)"
                                                class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
            } else if (type === 'url_list') {
                // URL listesi
                if (Array.isArray(items)) {
                    items.forEach((item, index) => {
                        html += `
                            <div class="card mb-2">
                                <div class="card-body p-2">
                                    <div class="row align-items-center">
                                        <div class="col-5">
                                            <input type="text"
                                                x-model="$wire.customJsonFields['${categoryName}'].items[${index}].title"
                                                class="form-control form-control-sm"
                                                placeholder="Başlık">
                                        </div>
                                        <div class="col-6">
                                            <input type="url"
                                                x-model="$wire.customJsonFields['${categoryName}'].items[${index}].url"
                                                class="form-control form-control-sm"
                                                placeholder="https://...">
                                        </div>
                                        <div class="col-1">
                                            <button type="button"
                                                @click="$wire.customJsonFields['${categoryName}'].items.splice(${index}, 1)"
                                                class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
            } else if (type === 'key_value') {
                // Key-Value
                if (typeof items === 'object' && !Array.isArray(items)) {
                    Object.keys(items).forEach((key) => {
                        const escapedKey = key.replace(/'/g, "\\'");
                        html += `
                            <div class="card mb-2">
                                <div class="card-body p-2">
                                    <div class="row align-items-center">
                                        <div class="col-5">
                                            <input type="text"
                                                value="${key}"
                                                @change="updateCustomKey('${categoryName}', '${escapedKey}', $event.target.value)"
                                                class="form-control form-control-sm"
                                                placeholder="Özellik Adı">
                                        </div>
                                        <div class="col-6">
                                            <input type="text"
                                                x-model="$wire.customJsonFields['${categoryName}'].items['${escapedKey}']"
                                                class="form-control form-control-sm"
                                                placeholder="Değer">
                                        </div>
                                        <div class="col-1">
                                            <button type="button"
                                                @click="deleteCustomKey('${categoryName}', '${escapedKey}')"
                                                class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
            } else if (type === 'detailed') {
                // Detaylı içerik
                if (Array.isArray(items)) {
                    items.forEach((item, index) => {
                        html += `
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-11">
                                            <input type="text"
                                                x-model="$wire.customJsonFields['${categoryName}'].items[${index}].title"
                                                class="form-control form-control-sm"
                                                placeholder="Başlık">
                                        </div>
                                        <div class="col-1">
                                            <button type="button"
                                                @click="$wire.customJsonFields['${categoryName}'].items.splice(${index}, 1)"
                                                class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <textarea
                                                x-model="$wire.customJsonFields['${categoryName}'].items[${index}].description"
                                                class="form-control form-control-sm"
                                                rows="3"
                                                placeholder="Açıklama"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
            }

            return html || '<div class="text-center text-muted py-3">Henüz item eklenmemiş</div>';
        },

        updateCustomKey(categoryName, oldKey, newKey) {
            if (oldKey === newKey || !newKey) return;

            const items = { ...this.$wire.customJsonFields[categoryName].items };
            items[newKey] = items[oldKey];
            delete items[oldKey];
            this.$wire.customJsonFields[categoryName].items = items;
        },

        deleteCustomKey(categoryName, key) {
            const items = { ...this.$wire.customJsonFields[categoryName].items };
            delete items[key];
            this.$wire.customJsonFields[categoryName].items = items;
        }
    }
}
</script>
@endpush
