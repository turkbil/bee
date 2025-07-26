@include('announcement::admin.helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">
                            <i class="fas fa-edit me-2"></i>{{ __('announcement::admin.basic_info') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-2" class="nav-link" data-bs-toggle="tab">
                            <i class="fas fa-search me-2"></i>{{ __('announcement::admin.seo') }}
                        </a>
                    </li>
                    <li class="nav-item ms-auto">
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
                    <!-- {{ __('announcement::admin.basic_info') }} -->
                    <div class="tab-pane fade active show" id="tabs-1">
                        @foreach($availableLanguages as $lang)
                        @php
                            $langData = $multiLangInputs[$lang] ?? [];
                            // Tenant languages'den dil ismini al
                            $langName = $tenantLanguages->where('code', $lang)->first()?->native_name ?? strtoupper($lang);
                        @endphp
                        
                        <div class="language-content" data-language="{{ $lang }}" style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">
                            <!-- Başlık alanı -->
                            <div class="form-floating mb-3">
                                <input type="text" wire:model="multiLangInputs.{{ $lang }}.title"
                                    class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror"
                                    placeholder="{{ __('announcement::admin.announcement_title_placeholder') }} ({{ strtoupper($lang) }})">
                                <label>
                                    {{ __('announcement::admin.title') }} ({{ $langName }})
                                    @if($lang === session('site_default_language', 'tr')) * @endif
                                </label>
                                @error('multiLangInputs.' . $lang . '.title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- İçerik editörü -->
                            <div class="mb-3" wire:ignore>
                                <label class="form-label">
                                    {{ __('announcement::admin.content') }} ({{ $langName }})
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
                                    <label>{{ __('announcement::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('announcement::admin.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- {{ __('announcement::admin.seo') }} -->
                    <div class="tab-pane fade" id="tabs-2">
                        @foreach($availableLanguages as $lang)
                        @php
                            // Tenant languages'den dil ismini al
                            $langName = $tenantLanguages->where('code', $lang)->first()?->native_name ?? strtoupper($lang);
                        @endphp
                        
                        <div class="language-content" data-language="{{ $lang }}" style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">
                            <!-- Slug alanı -->
                            <div class="form-floating mb-3">
                                <input type="text" wire:model="multiLangInputs.{{ $lang }}.slug" 
                                    class="form-control @error('multiLangInputs.' . $lang . '.slug') is-invalid @enderror"
                                    placeholder="Slug ({{ strtoupper($lang) }})">
                                <label>Slug ({{ $langName }})</label>
                                @error('multiLangInputs.' . $lang . '.slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Boş bırakılırsa başlıktan otomatik oluşturulur</small>
                            </div>
                            
                            <!-- Meta Keywords -->
                            <div class="form-floating mb-3">
                                <input type="text" 
                                    wire:model.defer="multiLangInputs.{{ $lang }}.metakey"
                                    class="form-control"
                                    data-choices
                                    data-choices-multiple="true"
                                    data-choices-search="false"
                                    data-choices-filter="true"
                                    data-choices-placeholder="{{ __('announcement::admin.meta_keywords_placeholder') }}"
                                    placeholder="{{ __('announcement::admin.meta_keywords_placeholder') }}">
                                <label>{{ __('announcement::admin.meta_keywords') }} ({{ $langName }})</label>
                            </div>

                            <!-- Meta Description -->
                            <div class="form-floating mb-3">
                                <textarea wire:model="multiLangInputs.{{ $lang }}.metadesc" class="form-control" data-bs-toggle="autosize"
                                    placeholder="{{ __('announcement::admin.meta_description_placeholder') }}"></textarea>
                                <label>{{ __('announcement::admin.meta_description') }} ({{ $langName }})</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <x-form-footer route="admin.announcement" :model-id="$announcementId" />

        </div>
    </form>
</div>