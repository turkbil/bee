@include('page::admin.helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <!-- Dil Seçici Butonları -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="btn-group" role="group">
                        @foreach($availableLanguages as $lang)
                            <button type="button" 
                                    wire:click="switchLanguage('{{ $lang }}')"
                                    class="btn {{ $currentLanguage === $lang ? 'btn-primary' : 'btn-outline-primary' }}">
                                @if($lang === 'tr')
                                    🇹🇷 Türkçe
                                @elseif($lang === 'en') 
                                    🇺🇸 English
                                @elseif($lang === 'ar')
                                    🇸🇦 العربية
                                @endif
                            </button>
                        @endforeach
                    </div>
                    <small class="text-muted">
                        {{ $currentLanguage === 'tr' ? 'Türkçe içerik düzenleniyor' : 
                           ($currentLanguage === 'en' ? 'English content editing' : 'تحرير المحتوى العربي') }}
                    </small>
                </div>
                
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">{{ __('admin.basic_info') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-2" class="nav-link" data-bs-toggle="tab">{{ __('page::admin.seo') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-3" class="nav-link" data-bs-toggle="tab">{{ __('admin.code_area') }}</a>
                    </li>
                </ul>
                
                @if($studioEnabled && $pageId)
                <div class="card-actions">
                    <a href="{{ route('admin.studio.editor', ['module' => 'page', 'id' => $pageId]) }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-wand-magic-sparkles me-2"></i> {{ __('studio.edit_with_studio', ['default' => 'Edit with Studio']) }}
                    </a>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- {{ __('admin.basic_info') }} -->
                    <div class="tab-pane fade active show" id="tabs-1">
                        <!-- Başlık alanı - Dil bazlı -->
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="multiLangInputs.{{ $currentLanguage }}.title"
                                class="form-control @error('multiLangInputs.' . $currentLanguage . '.title') is-invalid @enderror"
                                placeholder="{{ __('page::admin.title_field') }} ({{ strtoupper($currentLanguage) }})">
                            <label>
                                {{ __('page::admin.title_field') }} 
                                @if($currentLanguage === 'tr')
                                    (Türkçe) *
                                @elseif($currentLanguage === 'en')
                                    (English)
                                @elseif($currentLanguage === 'ar')
                                    (العربية)
                                @endif
                            </label>
                            @error('multiLangInputs.' . $currentLanguage . '.title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- İçerik editörü - Dil bazlı -->
                        <div class="mb-3" wire:ignore>
                            <label class="form-label">
                                {{ __('page::admin.content') }} 
                                @if($currentLanguage === 'tr')
                                    (Türkçe)
                                @elseif($currentLanguage === 'en')
                                    (English)
                                @elseif($currentLanguage === 'ar')
                                    (العربية)
                                @endif
                            </label>
                            <textarea id="editor_{{ $currentLanguage }}" 
                                      wire:model.defer="multiLangInputs.{{ $currentLanguage }}.body"
                                      class="form-control">{{ $multiLangInputs[$currentLanguage]['body'] ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                    value="1" {{ (!isset($inputs['is_active']) || $inputs['is_active']) ? 'checked' : '' }} />

                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('page::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('page::admin.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- {{ __('page::admin.seo') }} -->
                    <div class="tab-pane fade" id="tabs-2">
                        <!-- Slug alanı - Dil bazlı -->
                        <div class="form-floating mb-3">
                            <input type="text" 
                                   wire:model="multiLangInputs.{{ $currentLanguage }}.slug" 
                                   class="form-control @error('multiLangInputs.' . $currentLanguage . '.slug') is-invalid @enderror"
                                   placeholder="{{ __('page::admin.slug_field') }} ({{ strtoupper($currentLanguage) }})">
                            <label>
                                {{ __('page::admin.slug_field') }}
                                @if($currentLanguage === 'tr')
                                    (Türkçe)
                                @elseif($currentLanguage === 'en')
                                    (English)  
                                @elseif($currentLanguage === 'ar')
                                    (العربية)
                                @endif
                            </label>
                            @error('multiLangInputs.' . $currentLanguage . '.slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">Boş bırakılırsa başlıktan otomatik oluşturulur</small>
                        </div>
                        
                        <!-- Meta Keywords - Dil bazlı -->
                        <div class="form-floating mb-3">
                            <input type="text" 
                                wire:model.defer="multiLangInputs.{{ $currentLanguage }}.metakey"
                                class="form-control"
                                placeholder="{{ __('page::admin.meta_keywords') }} ({{ strtoupper($currentLanguage) }})...">
                            <label>
                                {{ __('page::admin.meta_keywords') }}
                                @if($currentLanguage === 'tr')
                                    (Türkçe)
                                @elseif($currentLanguage === 'en')
                                    (English)
                                @elseif($currentLanguage === 'ar')
                                    (العربية)
                                @endif
                            </label>
                            <small class="form-hint">Virgülle ayrılmış anahtar kelimeler</small>
                        </div>

                        <!-- Meta Description - Dil bazlı -->
                        <div class="form-floating mb-3">
                            <textarea wire:model="multiLangInputs.{{ $currentLanguage }}.metadesc" 
                                      class="form-control" 
                                      data-bs-toggle="autosize"
                                      placeholder="{{ __('page::admin.meta_description') }} ({{ strtoupper($currentLanguage) }})"></textarea>
                            <label>
                                {{ __('page::admin.meta_description') }}
                                @if($currentLanguage === 'tr')
                                    (Türkçe)
                                @elseif($currentLanguage === 'en')
                                    (English)
                                @elseif($currentLanguage === 'ar')
                                    (العربية)
                                @endif
                            </label>
                            <small class="form-hint">Boş bırakılırsa içerikten otomatik oluşturulur</small>
                        </div>
                    </div>

                    <!-- {{ __('admin.code_area') }} -->
                    <div class="tab-pane fade" id="tabs-3">
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentLanguage = '{{ $currentLanguage }}';
    
    // Dil değişikliği olayını dinle  
    Livewire.on('language-switched', function(data) {
        console.log('Page component dil değişikliği:', data);
        currentLanguage = data.language;
        
        // TinyMCE editörünü senkronize et
        setTimeout(() => {
            const editorId = `editor_${data.language}`;
            const editor = tinymce.get(editorId);
            if (editor && data.content) {
                editor.setContent(data.content);
            }
        }, 300);
    });
    
    // TinyMCE içeriğini kaydetmeden önce senkronize et
    Livewire.on('sync-tinymce-content', function() {
        const editorId = `editor_${currentLanguage}`;
        const editor = tinymce.get(editorId);
        if (editor) {
            const content = editor.getContent();
            @this.set(`multiLangInputs.${currentLanguage}.body`, content);
            console.log('TinyMCE içeriği senkronize edildi:', content.length, 'karakter');
        }
    });
});
</script>