<div wire:key="page-manage-component">
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
                    <li class="nav-item ms-2">
                    @else
                    <li class="nav-item ms-auto">
                    @endif
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
                            @include('page::admin.includes.content-editor', [
                                'lang' => $lang, 
                                'langName' => $langName, 
                                'langData' => $langData
                            ])
                        </div>
                        @endforeach
                        
                        {{-- 🤖 AI ANALIZ SONUÇLARI VE KONTROL PANELİ - FORM İÇİNDE --}}
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-robot me-2 text-primary"></i>AI Asistan & Analiz Merkezi
                                </h5>
                            </div>
                            <div class="card-body">
                                {{-- AI Kontrol Butonları --}}
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <button type="button" wire:click="runQuickAnalysis" class="btn btn-primary w-100" @if($aiProgress) disabled @endif>
                                            @if($aiProgress)
                                                <i class="fas fa-spinner fa-spin me-2"></i>Analiz Ediliyor...
                                            @else
                                                <i class="fas fa-tachometer-alt me-2"></i>🚀 Hızlı SEO Analizi
                                            @endif
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" wire:click="generateAISuggestions" class="btn btn-success w-100" @if($aiProgress) disabled @endif>
                                            @if($aiProgress)
                                                <i class="fas fa-spinner fa-spin me-2"></i>Üretiliyor...
                                            @else
                                                <i class="fas fa-lightbulb me-2"></i>🎯 AI Önerileri
                                            @endif
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" wire:click="testAI" class="btn btn-info w-100">
                                            <i class="fas fa-vial me-2"></i>🧪 AI Test
                                        </button>
                                    </div>
                                </div>

                                {{-- AI ANALIZ SONUÇLARI --}}
                                @if($aiProgress)
                                <div class="card" wire:key="ai-progress-card">
                                    <div class="card-body text-center py-5">
                                        <div class="spinner-border text-primary mb-3" role="status">
                                            <span class="visually-hidden">Yükleniyor...</span>
                                        </div>
                                        <h5 class="text-muted">AI analizi devam ediyor...</h5>
                                        <p class="text-muted small">Lütfen bekleyin, yapay zeka sonuçlarınızı hazırlıyor.</p>
                                    </div>
                                </div>
                                @elseif(!empty($aiAnalysis))
                                <div class="card" wire:key="ai-analysis-card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <div class="card-title">📊 AI Analiz Sonuçları</div>
                                            <div class="ms-auto">
                                                <span class="badge bg-primary">
                                                    {{ $aiAnalysis['stats']['ai_used'] ? '🤖 AI' : '⚡ Hızlı' }} 
                                                    {{ $aiAnalysis['stats']['timestamp'] ?? now()->format('H:i:s') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 col-md-3">
                                                <div class="card text-center">
                                                    <div class="card-body p-3">
                                                        <div class="display-6 fw-bold text-primary">{{ $aiAnalysis['overall_score'] ?? 0 }}/100</div>
                                                        <div class="text-muted small">Genel Skor</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="card text-center">
                                                    <div class="card-body p-3">
                                                        <div class="h4 fw-bold text-info">{{ $aiAnalysis['title_score'] ?? 0 }}/100</div>
                                                        <div class="text-muted small">📝 Başlık</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="card text-center">
                                                    <div class="card-body p-3">
                                                        <div class="h4 fw-bold text-success">{{ $aiAnalysis['content_score'] ?? 0 }}/100</div>
                                                        <div class="text-muted small">📄 İçerik</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="card text-center">
                                                    <div class="card-body p-3">
                                                        <div class="h4 fw-bold text-warning">{{ $aiAnalysis['seo_score'] ?? 0 }}/100</div>
                                                        <div class="text-muted small">🔍 SEO</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    
                                    {{-- AI DETAYLI YANIT --}}
                                    @if(!empty($aiAnalysis['ai_formatted_response']))
                                    <div class="card card-sm mt-3">
                                        <div class="card-header py-2">
                                            <div class="card-title m-0">
                                                <i class="fas fa-robot me-2 text-success"></i>🤖 Detaylı AI Analizi
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="ai-response-content" style="line-height: 1.6;">
                                                {!! $aiAnalysis['ai_formatted_response'] !!}
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    {{-- AI KISA ÖNERİLER (VARSA) --}}
                                    @if(!empty($aiAnalysis['suggestions']))
                                    <div class="card card-sm mt-3">
                                        <div class="card-header py-2">
                                            <div class="card-title m-0">
                                                <i class="fas fa-lightbulb me-2 text-warning"></i>💡 Hızlı Öneriler
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            @foreach(array_slice($aiAnalysis['suggestions'], 0, 5) as $suggestion)
                                            <div class="d-flex align-items-start mb-2">
                                                <i class="fas fa-arrow-right me-2 text-primary mt-1"></i>
                                                <div class="text-muted small">{{ is_array($suggestion) ? implode(' ', array_filter((array)$suggestion)) : $suggestion }}</div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @else
                                <div class="card border-dashed" wire:key="ai-analysis-placeholder">
                                    <div class="card-body text-center py-5">
                                        <div class="text-muted mb-3">
                                            <i class="fas fa-robot fa-3x"></i>
                                        </div>
                                        <h5 class="text-muted">🤖 AI analiz sonuçları burada görünecek...</h5>
                                        <p class="text-muted small mb-3">Hızlı Analiz butonuna tıklayın</p>
                                        <small class="text-muted">
                                            Debug: Property = {{ empty($aiAnalysis) ? 'BOŞ' : count($aiAnalysis) . ' adet' }} | 
                                            Session = {{ session('ai_last_analysis') ? 'DOLU' : 'BOŞ' }} | 
                                            Final = {{ !empty($aiAnalysis) ? count($aiAnalysis) . ' adet' : 'BOŞ' }} | 
                                            Zaman: {{ now()->format('H:i:s') }}
                                        </small>
                                    </div>
                                </div>
                                @endif

                                {{-- AI ÖNERİLERİ --}}
                                @if(!empty($aiSuggestions))
                                <div class="card mt-3" wire:key="ai-suggestions-card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <div class="card-title">
                                                <i class="fas fa-lightbulb me-2 text-warning"></i>🎯 AI İyileştirme Önerileri
                                            </div>
                                            <div class="ms-auto">
                                                <span class="badge bg-warning text-dark">
                                                    {{ is_array($aiSuggestions) ? count($aiSuggestions) : '1' }} Öneri
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        {{-- Eğer $aiSuggestions string ise (AI full response) --}}
                                        @if(is_string($aiSuggestions))
                                            <div class="ai-suggestions-content" style="line-height: 1.6;">
                                                {!! nl2br(e($aiSuggestions)) !!}
                                            </div>
                                        @else
                                            {{-- Array format (önceki format) --}}
                                            <div class="row">
                                                @foreach(array_slice($aiSuggestions, 0, 8) as $index => $suggestion)
                                                <div class="col-md-6 mb-3">
                                                    <div class="card card-sm">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-start">
                                                                <span class="badge bg-warning text-dark me-3 mt-1">{{ $index + 1 }}</span>
                                                                <div class="text-muted">{{ is_array($suggestion) ? implode(' ', array_filter((array)$suggestion)) : $suggestion }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @else
                                <div class="card mt-3 border-dashed" wire:key="ai-suggestions-placeholder">
                                    <div class="card-body text-center py-4">
                                        <div class="text-warning mb-3">
                                            <i class="fas fa-lightbulb fa-2x"></i>
                                        </div>
                                        <h5 class="text-muted">🎯 AI önerileri burada görünecek...</h5>
                                        <p class="text-muted small mb-2">AI Önerileri butonuna tıklayın</p>
                                        <small class="text-muted">
                                            Suggestions Debug: Property = {{ empty($aiSuggestions) ? 'BOŞ' : count($aiSuggestions) . ' adet' }} | 
                                            Session = {{ session('ai_last_suggestions') ? count(session('ai_last_suggestions')) . ' adet' : 'BOŞ' }} | 
                                            Final = {{ !empty($aiSuggestions) ? count($aiSuggestions) . ' adet' : 'BOŞ' }} | 
                                            Zaman: {{ now()->format('H:i:s') }}
                                        </small>
                                    </div>
                                </div>
                                @endif

                                {{-- AI Progress --}}
                                @if($aiProgress)
                                <div class="alert alert-info border-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-spinner fa-spin me-2"></i>
                                        <span>AI işlemi devam ediyor, lütfen bekleyin...</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

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
                        <!-- Global SEO Form Component -->
                        @if($pageId)
                            @php
                                $page = \Modules\Page\App\Models\Page::find($pageId);
                            @endphp
                            @if($page)
                                @livewire('seo-form-component', ['model' => $page])
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Sayfa bulunamadı (ID: {{ $pageId }}). SEO ayarları görüntülenemiyor.
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                SEO ayarları sayfayı kaydettikten sonra mevcut olacaktır.
                            </div>
                        @endif
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
    
    {{-- 🚀 FLOATING AI PANELİ + FORM İÇİ = DUAL AI SİSTEMİ --}}
    @include('page::admin.includes.ai-assistant-panel')
    
    {{-- Helper dosyası --}}
    @include('page::admin.helper')
    
</div>