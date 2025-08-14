<div>
    @include('admin.partials.error_message')

    <form method="post" wire:submit.prevent="save">
        <div class="card">
            {{-- Action Buttons Row --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    {{-- Studio Edit Button --}}
                    @if ($studioEnabled && $pageId)
                        <a href="{{ route('admin.studio.editor', ['module' => 'page', 'id' => $pageId]) }}"
                            target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fa-solid fa-wand-magic-sparkles me-1"></i>{{ __('page::admin.studio.editor') }}
                        </a>
                    @endif

                    {{-- Livewire Based Translation Button --}}
                    <button type="button" class="btn btn-outline-info btn-sm"
                        onclick="openPageTranslationModal()">
                        <i class="fas fa-sync-alt me-1"></i>Hızlı Çeviri
                    </button>
                </div>
            </div>
            
            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="page_active_tab">
                <x-manage.language.switcher :current-language="$currentLanguage" />
            </x-tab-system>
            <div class="card-body">
                <div class="tab-content" id="contentTabContent">
                    <!-- Temel Bilgiler Tab -->
                    <div class="tab-pane fade show active" id="0" role="tabpanel">
                        @foreach ($availableLanguages as $lang)
                            @php
                                $langData = $multiLangInputs[$lang] ?? [];
                                // Tenant languages'den dil ismini al
$tenantLanguages = \Modules\LanguageManagement\app\Models\TenantLanguage::where(
    'is_active',
    true,
)->get();
$langName =
    $tenantLanguages->where('code', $lang)->first()?->native_name ?? strtoupper($lang);
                            @endphp

                            <div class="language-content" data-language="{{ $lang }}"
                                style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">

                                <!-- Başlık ve Slug alanları -->
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <div class="form-floating">
                                            <input type="text" wire:model="multiLangInputs.{{ $lang }}.title"
                                                class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror"
                                                placeholder="{{ __('page::admin.title_field') }}">
                                            <label>
                                                {{ __('page::admin.title_field') }}
                                                @if ($lang === session('site_default_language', 'tr'))
                                                    <span class="required-star">★</span>
                                                @endif
                                            </label>
                                            @error('multiLangInputs.' . $lang . '.title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control"
                                                wire:model="multiLangInputs.{{ $lang }}.slug" maxlength="255"
                                                placeholder="sayfa-url-slug">
                                            <label>
                                                {{ __('admin.page_url_slug') }}
                                                <small class="text-muted ms-2">-
                                                    {{ __('admin.slug_auto_generated') }}</small>
                                            </label>
                                            <div class="form-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>{{ __('admin.slug_help') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- İçerik editörü -->
                                @include('admin.components.content-editor', [
                                    'lang' => $lang,
                                    'langName' => $langName,
                                    'langData' => $langData,
                                    'fieldName' => 'body',
                                    'label' => __('page::admin.content'),
                                    'placeholder' => __('page::admin.content_placeholder'),
                                ])
                            </div>
                        @endforeach

                        {{-- SEO Character Counter - manage.js'te tanımlı --}}

                        <!-- Aktif/Pasif - sadece bir kere -->
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                    value="1"
                                    {{ !isset($inputs['is_active']) || $inputs['is_active'] ? 'checked' : '' }} />

                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('page::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('page::admin.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Tab -->
                    <div class="tab-pane fade" id="1" role="tabpanel">
                        <x-seomanagement::universal-seo-tab :model="$this->currentPage" :available-languages="$availableLanguages" :current-language="$currentLanguage"
                            :seo-data-cache="$seoDataCache" />
                    </div>

                    <!-- Code Tab -->
                    <div class="tab-pane fade" id="2" role="tabpanel">
                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.css" class="form-control" data-bs-toggle="autosize"
                                placeholder="{{ __('admin.css_code') }}"></textarea>
                            <label>{{ __('admin.css') }}</label>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.js" class="form-control" data-bs-toggle="autosize"
                                placeholder="{{ __('admin.js_code') }}"></textarea>
                            <label>{{ __('admin.javascript') }}</label>
                        </div>
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.page" :model-id="$pageId" />

        </div>
    </form>

    {{-- Page Translation Modal --}}
    <div class="modal fade" id="pageTranslationModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-language me-2"></i>Sayfa İçeriği Çeviri
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Kaynak Dil</label>
                            <select id="translationSourceLang" class="form-select mb-3">
                                @foreach($availableLanguages as $lang)
                                    <option value="{{ $lang }}" {{ $lang === $currentLanguage ? 'selected' : '' }}>
                                        {{ strtoupper($lang) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hedef Diller</label>
                            <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                @foreach($availableLanguages as $lang)
                                    <div class="form-check">
                                        <input class="form-check-input translation-target-lang" 
                                               type="checkbox" 
                                               value="{{ $lang }}" 
                                               id="target_{{ $lang }}">
                                        <label class="form-check-label" for="target_{{ $lang }}">
                                            {{ strtoupper($lang) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label class="form-label">Çevrilecek Alanlar</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="translateTitle" checked>
                                    <label class="form-check-label" for="translateTitle">Başlık</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="translateBody" checked>
                                    <label class="form-check-label" for="translateBody">İçerik</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="overwriteExisting">
                                    <label class="form-check-label" for="overwriteExisting">
                                        Mevcut çevirilerin üzerine yaz
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="startPageTranslation()">
                        <i class="fas fa-sync-alt me-2"></i>Çeviriyi Başlat
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.currentPageId = {{ $jsVariables['currentPageId'] ?? 'null' }};
        window.currentLanguage = '{{ $jsVariables['currentLanguage'] ?? 'tr' }}';
        
        // Page Translation Functions
        function openPageTranslationModal() {
            // Wait for DOM and Tabler to be ready
            if (document.readyState !== 'complete') {
                window.addEventListener('load', openPageTranslationModal);
                return;
            }
            
            const modalElement = document.getElementById('pageTranslationModal');
            if (!modalElement) {
                console.error('Translation modal element not found');
                return;
            }
            
            // Try multiple Bootstrap/Tabler modal approaches
            let modal;
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                modal = new bootstrap.Modal(modalElement);
            } else if (typeof window.bootstrap !== 'undefined' && window.bootstrap.Modal) {
                modal = new window.bootstrap.Modal(modalElement);
            } else {
                // Direct modal show as last resort
                modalElement.classList.add('show');
                modalElement.style.display = 'block';
                document.body.classList.add('modal-open');
                
                // Add backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
                
                console.warn('Using manual modal display');
                return;
            }
            
            // Kaynak dili değiştirdiğinde hedef dilleri güncelle
            document.getElementById('translationSourceLang').addEventListener('change', function() {
                const sourceLang = this.value;
                document.querySelectorAll('.translation-target-lang').forEach(checkbox => {
                    if (checkbox.value === sourceLang) {
                        checkbox.checked = false;
                        checkbox.disabled = true;
                        checkbox.closest('.form-check').style.opacity = '0.5';
                    } else {
                        checkbox.disabled = false;
                        checkbox.closest('.form-check').style.opacity = '1';
                    }
                });
            });
            
            // İlk yüklemede kaynak dili tetikle
            document.getElementById('translationSourceLang').dispatchEvent(new Event('change'));
            
            modal.show();
        }
        
        function startPageTranslation() {
            const sourceLang = document.getElementById('translationSourceLang').value;
            const targetLangs = [];
            const fields = [];
            
            // Hedef dilleri topla
            document.querySelectorAll('.translation-target-lang:checked').forEach(checkbox => {
                targetLangs.push(checkbox.value);
            });
            
            // Çevrilecek alanları topla
            if (document.getElementById('translateTitle').checked) fields.push('title');
            if (document.getElementById('translateBody').checked) fields.push('body');
            
            const overwriteExisting = document.getElementById('overwriteExisting').checked;
            
            if (targetLangs.length === 0) {
                alert('Lütfen en az bir hedef dil seçin');
                return;
            }
            
            if (fields.length === 0) {
                alert('Lütfen en az bir alan seçin');
                return;
            }
            
            // Loading göster
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Çevriliyor...';
            
            // Livewire metodunu çağır
            @this.call('translateContent', {
                sourceLanguage: sourceLang,
                targetLanguages: targetLangs,
                fields: fields,
                overwriteExisting: overwriteExisting
            }).then(() => {
                // Modal'ı kapat
                bootstrap.Modal.getInstance(document.getElementById('pageTranslationModal')).hide();
                
                // Butonu eski haline getir
                btn.disabled = false;
                btn.innerHTML = originalText;
            }).catch(error => {
                console.error('Çeviri hatası:', error);
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
    </script>
@endpush