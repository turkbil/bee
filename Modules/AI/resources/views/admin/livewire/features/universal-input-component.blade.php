<div>
    <div class="mb-4">
        <!-- Ana Giriş Alanı -->
        <div class="mb-4">
            <label for="blog-topic" class="form-label fw-bold text-start d-block">Blog Konusu *</label>
            <textarea wire:model.defer="blogTopic" class="form-control @error('blogTopic') is-invalid @enderror" 
                      id="blog-topic" placeholder="Hangi konu hakkında blog yazısı yazmak istiyorsunuz?" rows="4" required>
            </textarea>
            @error('blogTopic')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text text-start">Yapay zeka ile yazılacak konuyu belirtin. Açık ve detaylı konu tanımlaması daha iyi sonuç verir.</div>
        </div>

        <!-- İleri Düzey Ayarlar Accordion -->
        <div class="accordion" id="advancedSettingsAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#advancedSettings" aria-expanded="false" aria-controls="advancedSettings">
                        <i class="fas fa-cogs text-primary me-2"></i>İleri Düzey Ayarlar
                    </button>
                </h2>
                <div id="advancedSettings" class="accordion-collapse collapse" data-bs-parent="#advancedSettingsAccordion">
                    <div class="accordion-body pt-4 pb-4">
                        <!-- İlk Satır: Yazım Tonu & İçerik Uzunluğu -->
                        <div class="row">
                            <!-- Yazım Tonu -->
                            <div class="col-md-6 text-start">
                                <div class="mb-3">
                                    <label for="writing-tone" class="form-label fw-bold text-start d-block">Yazım Tonu</label>
                                    <select wire:model="writingTone" class="form-select @error('writingTone') is-invalid @enderror" id="writing-tone">
                                        @foreach($writingToneOptions as $option)
                                            <option value="{{ $option['prompt_id'] }}">{{ $option['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('writingTone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- İçerik Uzunluğu -->
                            <div class="col-md-6 text-start">
                                <div class="mb-3">
                                    <label for="content-length" class="form-label fw-bold text-start d-block">İçerik Uzunluğu</label>
                                    <div class="range-container">
                                        <input wire:model="contentLength" type="range" class="form-range" id="content-length"
                                               min="1" max="5" value="{{ $contentLength }}" step="1">
                                        <div class="d-flex justify-content-between mt-2">
                                            <small class="text-muted">Kısa</small>
                                            <small class="text-muted">Normal</small>
                                            <small class="text-muted">Uzun</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- İkinci Satır: Hedef Kitle -->
                        <div class="row mb-3">
                            <div class="col-md-6 text-start">
                                <div class="mb-3">
                                    <label for="target-audience" class="form-label fw-bold text-start d-block">
                                        Hedef Kitle
                                    </label>
                                    <input wire:model.defer="targetAudience" type="text" class="form-control @error('targetAudience') is-invalid @enderror" 
                                           id="target-audience">
                                    @error('targetAudience')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-start mt-1">
                                        Yaş grubu, meslek, deneyim seviyesi, ilgi alanları
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Şirket Profili - Pretty Checkbox -->
                        @if($hasCompanyProfile)
                        <div class="mb-3 text-start">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="use_company_profile" wire:model="useCompanyProfile" value="1" />
                                <div class="state p-primary p-on ms-2">
                                    <label>{{ __('admin.use_company_profile') }}</label>
                                </div>
                                <div class="state p-primary p-off ms-2">
                                    <label>{{ __('admin.dont_use_company_profile') }}</label>
                                </div>
                            </div>
                            <div class="form-text text-start mt-1">
                                AI, şirket bilgilerinizi kullanarak daha kişiselleştirilmiş içerik üretir
                            </div>
                        </div>
                        @else
                        <!-- Şirket Profili Kurulum Önerisi -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="alert alert-info d-flex align-items-center mb-0" role="alert">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        <div>
                                            <strong>AI Profil Sistemi:</strong> Daha kişiselleştirilmiş AI yanıtları için AI profil sisteminizi tamamlayın.
                                            <a href="{{ route('admin.ai.profile.edit') }}" class="alert-link ms-1">AI Profili Düzenle</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Button -->
    <button wire:click="testFeature" class="test-btn w-100 mb-3" 
            wire:loading.attr="disabled" wire:target="testFeature">
        <span class="btn-text" wire:loading.remove wire:target="testFeature">
            <i class="fas fa-magic me-2"></i>{{ __('ai::admin.prowess.experience_magic') }}
        </span>
        <span wire:loading wire:target="testFeature">
            <i class="fas fa-cogs me-2"></i>{{ __('ai::admin.prowess.ai_working') }}
            <span class="loading-spinner spinner-border spinner-border-sm ms-2" role="status"></span>
        </span>
    </button>


    <!-- Result Showcase -->
    @if($showResult)
    <div class="result-showcase">
        <div class="result-header">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-sparkles"></i>
                </div>
                <div>
                    <div class="fw-bold">{{ __('ai::admin.prowess.ai_result') }}</div>
                    <small class="opacity-75">{{ $resultMeta ?: __('ai::admin.prowess.processing_complete') }}</small>
                </div>
            </div>
        </div>
        <div class="result-content">
            {!! $result !!}
        </div>
    </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- CSS Styles -->
    <style>
        .result-showcase {
            background: var(--tblr-card-bg);
            border: 1px solid var(--tblr-border-color);
            border-radius: 1rem;
            margin-top: 1.5rem;
            overflow: hidden;
        }

        .result-header {
            background: linear-gradient(90deg, var(--tblr-primary), var(--tblr-success));
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
        }

        .result-content {
            padding: 1.5rem;
            line-height: 1.7;
            font-family: inherit;
            text-align: left;
        }

        .form-range::-webkit-slider-thumb {
            background: var(--tblr-primary);
        }

        .form-range::-moz-range-thumb {
            background: var(--tblr-primary);
        }

        .range-label {
            font-size: 0.75rem;
        }
    </style>

</div>