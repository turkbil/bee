@include('portfolio::admin.helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">
                            <i class="fas fa-edit me-2"></i>{{ __('portfolio::admin.basic_info') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-2" class="nav-link" data-bs-toggle="tab">
                            <i class="fas fa-search me-2"></i>{{ __('portfolio::admin.seo') }}
                        </a>
                    </li>
                    @if($studioEnabled && $portfolioId)
                    <li class="nav-item ms-auto">
                        <a href="{{ route('admin.studio.editor', ['module' => 'portfolio', 'id' => $portfolioId]) }}" 
                           target="_blank" 
                           class="nav-link px-3 py-2 bg-primary text-white rounded">
                            <i class="fas fa-wand-magic-sparkles me-2"></i>{{ __('portfolio::admin.edit_with_studio') }}
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
                    <!-- Temel Bilgiler -->
                    <div class="tab-pane fade active show" id="tabs-1">
                        <!-- Başlık - Çok dilli -->
                        @foreach($availableLanguages as $lang)
                        @php
                            $langData = $multiLangInputs[$lang] ?? [];
                            // Tenant languages'den dil ismini al
                            $langName = $tenantLanguages->where('code', $lang)->first()?->native_name ?? strtoupper($lang);
                        @endphp
                        
                        <div class="language-content" data-language="{{ $lang }}" style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">
                            <div class="form-floating mb-3">
                                <input type="text" wire:model="multiLangInputs.{{ $lang }}.title"
                                    class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror"
                                    placeholder="{{ __('portfolio::admin.portfolio_title_placeholder') }} ({{ strtoupper($lang) }})">
                                <label>
                                    {{ __('portfolio::admin.title') }} ({{ $langName }})
                                    @if($lang === session('site_default_language', 'tr')) * @endif
                                </label>
                                @error('multiLangInputs.' . $lang . '.title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endforeach
                        
                        <!-- Kategori seçimi - sadece bir kere -->
                        <div class="form-floating mb-3">
                            <select wire:model.defer="inputs.portfolio_category_id"
                                class="form-control @error('inputs.portfolio_category_id') is-invalid @enderror"
                                data-choices 
                                data-choices-search="{{ count($categories) > 6 ? 'true' : 'false' }}"
                                data-choices-placeholder="{{ __('portfolio::admin.select_category') }}">
                                <option value="">{{ __('portfolio::admin.select_category') }}</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->portfolio_category_id }}" {{ $category->
                                    portfolio_category_id == $inputs['portfolio_category_id'] ? 'selected' : '' }}>
                                    {{ $category->title[app()->getLocale()] ?? $category->title['tr'] ?? '' }}
                                </option>
                                @endforeach
                            </select>
                            <label>{{ __('portfolio::admin.category') }}</label>
                            @error('inputs.portfolio_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fotoğraf Yükleme -->
                        @include('portfolio::admin.partials.image-upload', [
                        'imageKey' => 'image',
                        'label' => __('admin.drag_drop_image')
                        ])

                        <!-- İçerik editörü - Çok dilli -->
                        @foreach($availableLanguages as $lang)
                        @php
                            $langData = $multiLangInputs[$lang] ?? [];
                            // Tenant languages'den dil ismini al
                            $langName = $tenantLanguages->where('code', $lang)->first()?->native_name ?? strtoupper($lang);
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
                                    value="1" {{ (!isset($inputs['is_active']) || $inputs['is_active']) ? 'checked' : '' }} />

                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('portfolio::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('portfolio::admin.not_active') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- SEO -->
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
                                    data-choices-placeholder="{{ __('portfolio::admin.enter_keywords') }}"
                                    placeholder="{{ __('portfolio::admin.enter_keywords') }}">
                                <label>{{ __('portfolio::admin.meta_keywords') }} ({{ $langName }})</label>
                            </div>

                            <!-- Meta Description -->
                            <div class="form-floating mb-3">
                                <textarea wire:model="multiLangInputs.{{ $lang }}.metadesc" class="form-control" data-bs-toggle="autosize"
                                    placeholder="{{ __('portfolio::admin.meta_description_placeholder') }}"></textarea>
                                <label>{{ __('portfolio::admin.meta_description') }} ({{ $langName }})</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <x-form-footer route="admin.portfolio" :model-id="$portfolioId" />

        </div>
    </form>
</div>