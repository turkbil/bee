<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">
                            <i class="fas fa-info-circle me-2"></i>{{ __('admin.basic_info') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-2" class="nav-link" data-bs-toggle="tab">
                            <i class="fas fa-search me-2"></i>{{ __('page::admin.seo') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-3" class="nav-link" data-bs-toggle="tab">
                            <i class="fas fa-code me-2"></i>{{ __('admin.code_area') }}
                        </a>
                    </li>
                    @if($studioEnabled && $pageId)
                    <li class="nav-item ms-auto">
                        <a href="{{ route('admin.studio.editor', ['module' => 'page', 'id' => $pageId]) }}" 
                           target="_blank" 
                           class="nav-link px-3 py-2 bg-primary text-white rounded">
                            <i class="fas fa-wand-magic-sparkles me-2"></i>Studio ile Düzenle
                        </a>
                    </li>
                    @endif
                    <li class="nav-item ms-2">
                        @php
                            $tenantLanguages = \Modules\LanguageManagement\app\Models\TenantLanguage::orderBy('is_active', 'desc')
                                ->orderBy('sort_order', 'asc')
                                ->orderBy('id', 'asc')
                                ->get();
                        @endphp
                        <div class="d-flex gap-3">
                            @foreach($tenantLanguages->where('is_active', true) as $lang)
                                <button class="btn btn-link p-2 language-switch-btn {{ $currentLanguage === $lang->code ? 'text-primary' : 'text-muted' }}" 
                                        style="border: none; border-radius: 0; {{ $currentLanguage === $lang->code ? 'border-bottom: 2px solid var(--primary-color) !important;' : 'border-bottom: 2px solid transparent;' }}"
                                        data-language="{{ $lang->code }}">
                                    {{ strtoupper($lang->code) }}
                                </button>
                            @endforeach
                        </div>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Tab 1: Basic Info -->
                    <div class="tab-pane fade active show" id="tabs-1">
                        @foreach($availableLanguages as $lang)
                        @php
                            $langData = $multiLangInputs[$lang] ?? [];
                            $langName = $lang === 'tr' ? 'Türkçe' : ($lang === 'en' ? 'English' : 'العربية');
                        @endphp
                        
                        <div class="language-content" data-language="{{ $lang }}" style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">
                            <!-- Başlık alanı -->
                            <div class="form-floating mb-3">
                                <input type="text" wire:model="multiLangInputs.{{ $lang }}.title"
                                    class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror"
                                    placeholder="{{ __('page::admin.title_field') }} ({{ strtoupper($lang) }})">
                                <label>
                                    {{ __('page::admin.title_field') }} ({{ $langName }})
                                    @if($lang === session('site_default_language', 'tr')) * @endif
                                </label>
                                @error('multiLangInputs.' . $lang . '.title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- İçerik editörü -->
                            <div class="mb-3" wire:ignore>
                                <label class="form-label">
                                    {{ __('page::admin.content') }} ({{ $langName }})
                                </label>
                                <textarea id="editor_{{ $lang }}" 
                                          wire:model.defer="multiLangInputs.{{ $lang }}.body"
                                          class="form-control">{{ $langData['body'] ?? '' }}</textarea>
                            </div>
                        </div>
                        @endforeach
                        
                        <!-- Aktif/Pasif - sadece bir kere -->
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
                    
                    <!-- Tab 2: SEO -->
                    <div class="tab-pane fade" id="tabs-2">
                        @foreach($availableLanguages as $lang)
                        @php
                            $langName = $lang === 'tr' ? 'Türkçe' : ($lang === 'en' ? 'English' : 'العربية');
                        @endphp
                        
                        <div class="language-content" data-language="{{ $lang }}" style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">
                            <!-- Slug alanı -->
                            <div class="form-floating mb-3">
                                <input type="text" wire:model="multiLangInputs.{{ $lang }}.slug" 
                                    class="form-control @error('multiLangInputs.' . $lang . '.slug') is-invalid @enderror"
                                    placeholder="{{ __('page::admin.slug_field') }} ({{ strtoupper($lang) }})">
                                <label>{{ __('page::admin.slug_field') }} ({{ $langName }})</label>
                                @error('multiLangInputs.' . $lang . '.slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Boş bırakılırsa başlıktan otomatik oluşturulur</small>
                            </div>
                            
                            <!-- Meta Keywords -->
                            <div class="form-floating mb-3">
                                <input type="text" wire:model.defer="multiLangInputs.{{ $lang }}.metakey"
                                    class="form-control"
                                    placeholder="{{ __('page::admin.meta_keywords') }} ({{ strtoupper($lang) }})...">
                                <label>{{ __('page::admin.meta_keywords') }} ({{ $langName }})</label>
                                <small class="form-hint">Virgülle ayrılmış anahtar kelimeler</small>
                            </div>

                            <!-- Meta Description -->
                            <div class="form-floating mb-3">
                                <textarea wire:model="multiLangInputs.{{ $lang }}.metadesc" 
                                          class="form-control" 
                                          data-bs-toggle="autosize"
                                          placeholder="{{ __('page::admin.meta_description') }} ({{ strtoupper($lang) }})"></textarea>
                                <label>{{ __('page::admin.meta_description') }} ({{ $langName }})</label>
                                <small class="form-hint">Boş bırakılırsa içerikten otomatik oluşturulur</small>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Tab 3: Code Area -->
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

@include('page::admin.helper')