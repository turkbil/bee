<div>
    @php
        View::share('pretitle', $pageId ? 'Sayfa Düzenleme' : 'Yeni Sayfa Ekleme');
    @endphp

    @include('page::admin.helper')

    <form method="post" wire:submit.prevent="save">
        @include('admin.partials.error_message')
        <div class="card">
            
            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="page_active_tab">
                {{-- Studio Edit Button --}}
                @if ($studioEnabled && $pageId)
                    <li class="nav-item ms-3">
                        <a href="{{ route('admin.studio.editor', ['module' => 'page', 'id' => $pageId]) }}"
                            target="_blank" class="btn btn-outline-primary" style="padding: 0.20rem 0.75rem; margin-top: 5px;">
                            <i class="fa-solid fa-wand-magic-sparkles fa-lg me-1"></i>{{ __('page::admin.studio.editor') }}
                        </a>
                    </li>
                @endif

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

                                {{-- İçerik editörü - AI button artık global component'te --}}
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
                            :seo-data-cache="$seoDataCache" :page-id="$this->pageId"
                            :static-ai-analysis="$staticAiAnalysis" :dynamic-ai-analysis="$dynamicAiAnalysis"
                            :static-ai-recommendations="$staticAiRecommendations" :dynamic-ai-recommendations="$dynamicAiRecommendations"
                            :analysis-loaders="$analysisLoaders" :recommendation-loaders="$recommendationLoaders"
                            :analysis-errors="$analysisErrors" :recommendation-errors="$recommendationErrors" />
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


@push('scripts')
    <script>
        window.currentPageId = {{ $jsVariables['currentPageId'] ?? 'null' }};
        window.currentLanguage = '{{ $jsVariables['currentLanguage'] ?? 'tr' }}';

        // TinyMCE Content Update Helper Function
        window.updateTinyMCEContent = function(content, targetField = 'body') {
            try {
                const currentLang = window.currentLanguage || 'tr';
                const editorId = `multiLangInputs.${currentLang}.${targetField}`;

                console.log('🎯 updateTinyMCEContent çağırıldı:', {
                    editorId,
                    currentLang,
                    targetField,
                    contentLength: content ? content.length : 0
                });

                // 🔍 DEBUG: DOM yapısını analiz et
                console.log('🔍 DOM DEBUG:', {
                    hugerte_exists: typeof hugerte !== 'undefined',
                    tinyMCE_exists: typeof tinyMCE !== 'undefined',
                    current_language: currentLang,
                    target_field: targetField
                });

                // HugeRTE/TinyMCE editor'ları tara
                if (typeof hugerte !== 'undefined') {
                    console.log('🔍 HugeRTE Debug:', {
                        hugerte: hugerte,
                        hugerte_editors: hugerte.editors || 'editors property not found',
                        hugerte_activeEditor: hugerte.activeEditor || 'activeEditor not found'
                    });

                    // HugeRTE editör bulma (multiple approach)
                    let targetEditor = null;

                    // Method 1: hugerte.editors array
                    if (hugerte.editors && Array.isArray(hugerte.editors)) {
                        targetEditor = hugerte.editors.find(ed =>
                            ed.id && (ed.id.includes(targetField) || ed.id.includes(currentLang))
                        );
                    }

                    // Method 2: hugerte.activeEditor
                    if (!targetEditor && hugerte.activeEditor) {
                        targetEditor = hugerte.activeEditor;
                    }

                    // Method 3: hugerte.get() method
                    if (!targetEditor && typeof hugerte.get === 'function') {
                        const allEditors = hugerte.get();
                        if (allEditors && allEditors.length > 0) {
                            targetEditor = allEditors.find(ed =>
                                ed.id && (ed.id.includes(targetField) || ed.id.includes(currentLang))
                            ) || allEditors[0]; // Son çare olarak ilk editörü al
                        }
                    }

                    if (targetEditor && targetEditor.setContent) {
                        console.log('✅ HugeRTE editor bulundu:', targetEditor.id);
                        targetEditor.setContent(content);

                        // Livewire sync
                        const textareaElement = document.getElementById(targetEditor.id);
                        if (textareaElement) {
                            textareaElement.value = content;
                            textareaElement.dispatchEvent(new Event('input', { bubbles: true }));
                        }

                        // Hidden input sync
                        const hiddenInput = document.getElementById(`hidden_${targetField}_${currentLang}`);
                        if (hiddenInput) {
                            hiddenInput.value = content;
                            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }

                        console.log('✅ HugeRTE content güncellendi!');
                        return true;
                    }
                }

                // TinyMCE fallback
                if (typeof tinyMCE !== 'undefined' && tinyMCE.editors) {
                    console.log('🔍 TinyMCE Fallback:', Object.keys(tinyMCE.editors));
                    const editorKeys = Object.keys(tinyMCE.editors);
                    const matchingKey = editorKeys.find(key =>
                        key.includes(targetField) || key.includes(currentLang)
                    );

                    if (matchingKey) {
                        const editor = tinyMCE.editors[matchingKey];
                        if (editor && editor.setContent) {
                            editor.setContent(content);
                            console.log('✅ TinyMCE content güncellendi!');
                            return true;
                        }
                    }
                }

                // Son çare: Direkt textarea selector'ları dene
                console.log('🔍 Manual textarea search başlatılıyor...');

                // Multiple textarea selector attempts
                const textareaSelectors = [
                    `textarea[wire\\:model*="${targetField}"]`,
                    `textarea[wire\\:model*="${currentLang}.${targetField}"]`,
                    `textarea[wire\\:model*="multiLangInputs.${currentLang}.${targetField}"]`,
                    `textarea.hugerte-editor`,
                    `textarea[id*="${targetField}"]`,
                    `textarea[id*="${currentLang}"]`,
                    `textarea[name*="${targetField}"]`
                ];

                let textarea = null;
                for (const selector of textareaSelectors) {
                    textarea = document.querySelector(selector);
                    if (textarea) {
                        console.log('✅ Textarea bulundu:', selector);
                        break;
                    }
                }

                if (textarea) {
                    textarea.value = content;
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    textarea.dispatchEvent(new Event('change', { bubbles: true }));

                    // Hidden input'u da güncelle
                    const hiddenInput = document.getElementById(`hidden_${targetField}_${currentLang}`);
                    if (hiddenInput) {
                        hiddenInput.value = content;
                        hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }

                    console.log('✅ Textarea direkt güncellendi');
                    return true;
                }

                // Ultra debug: Tüm textarea'ları listele
                const allTextareas = document.querySelectorAll('textarea');
                console.log('🔍 Mevcut tüm textarea\'lar:', Array.from(allTextareas).map(ta => ({
                    id: ta.id,
                    name: ta.name,
                    wireModel: ta.getAttribute('wire:model'),
                    classes: ta.className
                })));

                console.error('❌ Hiçbir editor/textarea bulunamadı');
                return false;
            } catch (e) {
                console.error('❌ updateTinyMCEContent error:', e);
                return false;
            }
        };

        // GLOBAL receiveGeneratedContent function - Conflict önlemek için null check
        if (typeof window.receiveGeneratedContent === 'undefined') {
            window.receiveGeneratedContent = function(content, targetField = 'body') {
                try {
                    console.log('🎯 AI Content received:', {
                        content: content ? content.substring(0, 100) + '...' : 'empty',
                        targetField
                    });

                    // ÖNCE TinyMCE editörünü direkt güncelle (anında görünüm için)
                    window.updateTinyMCEContent(content, targetField);

                    // SONRA Livewire component'i güncelle (database save için)
                    if (window.Livewire) {
                        // İlk yöntem: Livewire 3.x
                        if (window.Livewire.getByName) {
                            try {
                                const pageComponent = window.Livewire.getByName('page-manage-component')[0];
                                if (pageComponent && pageComponent.call) {
                                    console.log('✅ PageManageComponent bulundu (v3), receiveGeneratedContent çağırılıyor...');
                                    pageComponent.call('receiveGeneratedContent', content, targetField);
                                    return;
                                }
                            } catch (e) {
                                console.warn('⚠️ Livewire v3 method failed:', e);
                            }
                        }

                        // İkinci yöntem: Livewire 2.x
                        if (window.Livewire.all) {
                            try {
                                const pageComponent = window.Livewire.all().find(component => {
                                    return component &&
                                           component.__instance &&
                                           component.__instance.fingerprint &&
                                           component.__instance.fingerprint.name === 'page-manage-component';
                                });

                                if (pageComponent && pageComponent.call) {
                                    console.log('✅ PageManageComponent bulundu (v2), receiveGeneratedContent çağırılıyor...');
                                    pageComponent.call('receiveGeneratedContent', content, targetField);
                                    return;
                                }
                            } catch (e) {
                                console.warn('⚠️ Livewire v2 method failed:', e);
                            }
                        }

                        // Üçüncü yöntem: Direct wire:id kullanma
                        const wireElement = document.querySelector('[wire\\:id]');
                        if (wireElement && wireElement.__livewire) {
                            try {
                                console.log('✅ Wire element bulundu, receiveGeneratedContent çağırılıyor...');
                                wireElement.__livewire.call('receiveGeneratedContent', content, targetField);
                                return;
                            } catch (e) {
                                console.warn('⚠️ Wire element method failed:', e);
                            }
                        }

                        console.error('❌ PageManageComponent hiçbir yöntemle bulunamadı');
                    } else {
                        console.error('❌ Livewire henüz yüklenmemiş');
                    }
                } catch (e) {
                    console.error('❌ receiveGeneratedContent error:', e);
                }
            };
            console.log('✅ Global receiveGeneratedContent function tanımlandı');
        } else {
            console.warn('⚠️ receiveGeneratedContent zaten tanımlı, duplicate önlendi');
        }

        // Debug: currentPageId değerini logla
        console.log('🔍 Page ID Debug:', {
            currentPageId: window.currentPageId,
            pageIdFromJsVars: {{ $jsVariables['currentPageId'] ?? 'null' }},
            pageIdFromLivewire: {{ $pageId ?? 'null' }}
        });
        
        

        // 🔥 ÇEVİRİ SONRASI REFRESH EVENT LİSTENER
        document.addEventListener('livewire:initialized', () => {
            // Component refresh event'ini dinle - SADECE ÇEVİRİ İÇİN
            Livewire.on('refreshComponent', (data) => {
                // Eğer SEO işlemi değilse sadece o zaman refresh yap
                if (!data || !data.source || data.source !== 'seo-analysis') {
                    console.log('🔄 Çeviri tamamlandı - component yenileniyor...', data);
                    Livewire.components.getByName('page-manage-component')[0].$refresh();
                } else {
                    console.log('⚠️ SEO analizi - component refresh atlandı');
                }
            });
            
            // ✅ TinyMCE editör refresh event'i artık gerekli değil
            // AI content direkt olarak TinyMCE'ye yazılıyor
        });
    </script>
@endpush

@push('modals')
    @include('admin.partials.global-ai-content-modal')
@endpush