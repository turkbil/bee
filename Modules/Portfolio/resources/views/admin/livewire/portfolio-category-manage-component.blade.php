@php
    View::share('pretitle', $categoryId ? 'Portföy Kategori Düzenleme' : 'Yeni Portföy Kategori Ekleme');
@endphp

@include('portfolio::admin.helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">{{ __('portfolio::admin.basic_information') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-2" class="nav-link" data-bs-toggle="tab">{{ __('portfolio::admin.seo') }}</a>
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
                    <!-- Temel Bilgiler -->
                    <div class="tab-pane fade active show" id="tabs-1">
                        <!-- Başlık - Çok dilli -->
                        @foreach($availableLanguages as $lang)
                        @php
                            $langData = $multiLangInputs[$lang] ?? [];
                            $langName = $lang === 'tr' ? 'Türkçe' : ($lang === 'en' ? 'English' : 'العربية');
                        @endphp
                        
                        <div class="language-content" data-language="{{ $lang }}" style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">
                            <div class="form-floating mb-3">
                                <input type="text" wire:model="multiLangInputs.{{ $lang }}.title"
                                    class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror"
                                    placeholder="{{ __('portfolio::admin.category_title') }} ({{ strtoupper($lang) }})">
                                <label>
                                    {{ __('portfolio::admin.category_title_label') }} ({{ $langName }})
                                    @if($lang === session('site_default_language', 'tr')) * @endif
                                </label>
                                @error('multiLangInputs.' . $lang . '.title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endforeach

                        <!-- İçerik editörü - Çok dilli -->
                        @foreach($availableLanguages as $lang)
                        @php
                            $langData = $multiLangInputs[$lang] ?? [];
                            $langName = $lang === 'tr' ? 'Türkçe' : ($lang === 'en' ? 'English' : 'العربية');
                        @endphp
                        
                        <div class="language-content" data-language="{{ $lang }}" style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">
                            <div class="mb-3" wire:ignore>
                                <label class="form-label">
                                    {{ __('portfolio::admin.content') }} ({{ $langName }})
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
                                    value="1" {{ $inputs['is_active'] ? 'checked' : '' }} />
                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('portfolio::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('portfolio::admin.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="tab-pane fade" id="tabs-2">
                        <x-seomanagement::universal-seo-tab 
                            :model="$this->currentCategory()" 
                            :available-languages="$availableLanguages" 
                            :current-language="$currentLanguage" 
                            :seo-data-cache="$seoDataCache" />
                    </div>
                </div>
            </div>

            <!-- Form Footer -->
            <x-form-footer route="admin.portfolio.category" :model-id="$categoryId" />
        </div>
    </form>
</div>