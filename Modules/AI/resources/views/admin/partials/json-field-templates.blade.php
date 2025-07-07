{{-- Example Inputs Template --}}
<template id="example-inputs-template">
    <div class="card mb-3 json-item" data-index="__INDEX__">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="drag-handle me-3" style="cursor: move;">
                    <i class="fas fa-grip-vertical text-muted"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="row g-2">
                        <div class="col-md-8">
                            <textarea class="form-control form-control-sm" 
                                      name="example_inputs[__INDEX__][text]" 
                                      rows="3" 
                                      placeholder="Örnek metin girin..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <input type="text" 
                                   class="form-control form-control-sm mb-2" 
                                   name="example_inputs[__INDEX__][label]" 
                                   placeholder="Etiket (örn: E-ticaret)">
                            <select class="form-select form-select-sm" 
                                    name="example_inputs[__INDEX__][type]">
                                <option value="business">İş/Ticaret</option>
                                <option value="personal">Kişisel</option>
                                <option value="academic">Akademik</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-json-item">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

{{-- Helper Examples Template --}}
<template id="helper-examples-template">
    <div class="card mb-3 json-item" data-index="__INDEX__">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div class="drag-handle me-3" style="cursor: move;">
                    <i class="fas fa-grip-vertical text-muted"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="row g-2">
                        <div class="col-12">
                            <input type="text" 
                                   class="form-control form-control-sm mb-2" 
                                   name="helper_examples[__INDEX__][code]" 
                                   placeholder="Örnek kod: ai_translate('Hello', 'tr')">
                        </div>
                        <div class="col-md-8">
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="helper_examples[__INDEX__][description]" 
                                   placeholder="Açıklama">
                        </div>
                        <div class="col-md-4">
                            <input type="number" 
                                   class="form-control form-control-sm" 
                                   name="helper_examples[__INDEX__][estimated_tokens]" 
                                   placeholder="Tahmini token">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-json-item">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

{{-- Helper Parameters Template --}}
<template id="helper-parameters-template">
    <div class="card mb-3 json-item" data-index="__INDEX__">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div class="drag-handle me-3" style="cursor: move;">
                    <i class="fas fa-grip-vertical text-muted"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="helper_parameters[__INDEX__][name]" 
                                   placeholder="Parametre adı">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" 
                                    name="helper_parameters[__INDEX__][type]">
                                <option value="string">String</option>
                                <option value="array">Array</option>
                                <option value="boolean">Boolean</option>
                                <option value="integer">Integer</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" 
                                    name="helper_parameters[__INDEX__][required]">
                                <option value="1">Zorunlu</option>
                                <option value="0">Opsiyonel</option>
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="helper_parameters[__INDEX__][description]" 
                                   placeholder="Parametre açıklaması">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-json-item">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

{{-- Response Template Sections --}}
<template id="response-template-sections">
    <div class="card mb-3 json-item" data-index="__INDEX__">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div class="drag-handle me-3" style="cursor: move;">
                    <i class="fas fa-grip-vertical text-muted"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="response_template[sections][__INDEX__][title]" 
                                   placeholder="Bölüm başlığı (örn: SEO Başlık)">
                        </div>
                        <div class="col-md-6">
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="response_template[sections][__INDEX__][format]" 
                                   placeholder="Format (örn: 50-60 karakter)">
                        </div>
                        <div class="col-12 mt-2">
                            <textarea class="form-control form-control-sm" 
                                      name="response_template[sections][__INDEX__][description]" 
                                      rows="2"
                                      placeholder="Bölüm açıklaması veya kuralları"></textarea>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-json-item">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

{{-- Settings Template --}}
<template id="settings-template">
    <div class="card mb-3 json-item" data-index="__INDEX__">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div class="drag-handle me-3" style="cursor: move;">
                    <i class="fas fa-grip-vertical text-muted"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="settings[__INDEX__][key]" 
                                   placeholder="Ayar anahtarı">
                        </div>
                        <div class="col-md-4">
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="settings[__INDEX__][value]" 
                                   placeholder="Varsayılan değer">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" 
                                    name="settings[__INDEX__][type]">
                                <option value="text">Metin</option>
                                <option value="number">Sayı</option>
                                <option value="boolean">Evet/Hayır</option>
                                <option value="select">Seçim</option>
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="settings[__INDEX__][description]" 
                                   placeholder="Ayar açıklaması">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-json-item">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

{{-- Error/Success Messages Template --}}
<template id="messages-template">
    <div class="card mb-3 json-item" data-index="__INDEX__">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div class="drag-handle me-3" style="cursor: move;">
                    <i class="fas fa-grip-vertical text-muted"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="__FIELD__[__INDEX__][code]" 
                                   placeholder="Mesaj kodu">
                        </div>
                        <div class="col-md-8">
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="__FIELD__[__INDEX__][message]" 
                                   placeholder="Mesaj metni">
                        </div>
                        <div class="col-md-6 mt-2">
                            <select class="form-select form-select-sm" 
                                    name="__FIELD__[__INDEX__][type]">
                                <option value="info">Bilgi</option>
                                <option value="warning">Uyarı</option>
                                <option value="error">Hata</option>
                                <option value="success">Başarı</option>
                            </select>
                        </div>
                        <div class="col-md-6 mt-2">
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="__FIELD__[__INDEX__][context]" 
                                   placeholder="Bağlam (opsiyonel)">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-json-item">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

{{-- Usage Examples Template --}}
<template id="usage-examples-template">
    <div class="card mb-3 json-item" data-index="__INDEX__">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div class="drag-handle me-3" style="cursor: move;">
                    <i class="fas fa-grip-vertical text-muted"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="row g-2">
                        <div class="col-12">
                            <input type="text" 
                                   class="form-control form-control-sm mb-2" 
                                   name="usage_examples[__INDEX__][title]" 
                                   placeholder="Kullanım örneği başlığı">
                        </div>
                        <div class="col-12">
                            <textarea class="form-control form-control-sm mb-2" 
                                      name="usage_examples[__INDEX__][input]" 
                                      rows="2"
                                      placeholder="Örnek girdi"></textarea>
                        </div>
                        <div class="col-12">
                            <textarea class="form-control form-control-sm" 
                                      name="usage_examples[__INDEX__][output]" 
                                      rows="3"
                                      placeholder="Beklenen çıktı"></textarea>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-json-item">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<style>
.json-item {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.json-item:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.sortable-ghost {
    opacity: 0.4;
    background: #f8f9fa;
}

.drag-handle {
    cursor: move;
}

.drag-handle:hover {
    color: #495057 !important;
}
</style>