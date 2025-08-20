@extends('admin.layout')

@section('title', $featureModel->name . ' - AI Feature')

@section('breadcrumb')
    <ol class="breadcrumb m-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.ai.prowess') }}">AI Prowess</a></li>
        <li class="breadcrumb-item active">{{ $featureModel->name }}</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Feature Header - Same as Prowess Category Header -->
    <div class="feature-header mb-4">
        <div class="skill-icon">{{ $featureModel->emoji ?? '🤖' }}</div>
        <h2 class="mb-0 position-relative">
            <i class="fas fa-magic me-3"></i>
            {{ $featureModel->name }}
            <span class="badge badge-secondary ms-3">Tek Feature</span>
        </h2>
        <p class="mb-0 mt-2 opacity-75">{{ $featureModel->description }}</p>
        <div class="mt-3">
            <a href="{{ route('admin.ai.prowess') }}" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-1"></i>
                Geri Dön
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row justify-content-center">
                <!-- Feature Test Panel - Full Width -->
                <div class="col-lg-10 col-xl-8">
                    <div class="card prowess-card">
                        <div class="card-body">
                            <!-- Universal Feature Form -->
                            <form id="featureTestForm">
                                <!-- Ana Giriş Alanı -->
                                <div class="mb-4">
                                    <label for="mainInput" class="form-label fw-bold text-start d-block">
                                        @if($featureModel->id == 201)
                                            Blog Konusu *
                                        @else
                                            {{ $featureModel->name }} İçeriği *
                                        @endif
                                    </label>
                                    <textarea class="form-control" id="mainInput" name="main_input"
                                              placeholder="@if($featureModel->id == 201)Hangi konu hakkında blog yazısı yazmak istiyorsunuz?@else{{ $featureModel->input_placeholder ?? 'İçeriğinizi buraya yazın...' }}@endif" 
                                              rows="4" required></textarea>
                                    @if($featureModel->id == 201)
                                        <div class="form-text text-start">Yapay zeka ile yazılacak konuyu belirtin. Açık ve detaylı konu tanımlaması daha iyi sonuç verir.</div>
                                    @endif
                                </div>

                                <!-- İleri Düzey Ayarlar Accordion -->
                                <div class="accordion" id="advancedSettingsAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                    data-bs-target="#advancedSettings" aria-expanded="false" aria-controls="advancedSettings">
                                                <i class="fas fa-cogs text-primary me-2"></i>İleri Düzey Ayarlar
                                            </button>
                                        </h2>
                                        <div id="advancedSettings" class="accordion-collapse collapse" data-bs-parent="#advancedSettingsAccordion">
                                            <div class="accordion-body pt-4 pb-4">
                                                <div class="row">
                                                    <!-- Sol Kolon -->
                                                    <div class="col-md-6 text-start">
                                                        <!-- Yazım Tonu -->
                                                        <div class="mb-3">
                                                            <label for="writingTone" class="form-label fw-bold text-start d-block">Yazım Tonu</label>
                                                            <select class="form-select" id="writingTone" name="writing_tone" data-choices>
                                                                @foreach(\Modules\AI\App\Models\Prompt::where('prompt_type', 'writing_tone')->where('is_active', true)->orderBy('priority', 'desc')->orderBy('name')->get() as $toneOption)
                                                                    <option value="{{ $toneOption->prompt_id }}">{{ $toneOption->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <!-- Hedef Kitle -->
                                                        <div class="mb-3">
                                                            <label for="targetAudience" class="form-label fw-bold text-start d-block">Hedef Kitle</label>
                                                            <input type="text" class="form-control" id="targetAudience" name="target_audience" 
                                                                   placeholder="Örn: 25-35 yaş teknoloji meraklıları, işletme sahipleri...">
                                                            <div class="form-text text-start mt-1">
                                                                <strong>Yaş grubu, meslek, deneyim seviyesi, ilgi alanları</strong> gibi detayları ekleyebilirsiniz.
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Sağ Kolon -->
                                                    <div class="col-md-6 text-start">
                                                        <!-- İçerik Uzunluğu -->
                                                        <div class="mb-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <label for="contentLength" class="form-label fw-bold text-start mb-0">İçerik Uzunluğu</label>
                                                                <span class="badge badge-primary" id="contentLengthDisplay">Normal</span>
                                                            </div>
                                                            <div class="range-container">
                                                                <input type="range" class="form-range" id="contentLength" name="content_length" 
                                                                       min="1" max="5" value="3" step="1">
                                                                <div class="d-flex justify-content-between mt-2">
                                                                    @if(isset($contentLengthOptions) && $contentLengthOptions->count() > 0)
                                                                        @foreach($contentLengthOptions as $index => $option)
                                                                            <small class="text-muted range-label" data-value="{{ $index + 1 }}">{{ $option['label'] }}</small>
                                                                        @endforeach
                                                                    @else
                                                                        <small class="text-muted">Çok Kısa</small>
                                                                        <small class="text-muted">Kısa</small>
                                                                        <small class="text-muted">Normal</small>
                                                                        <small class="text-muted">Uzun</small>
                                                                        <small class="text-muted">Detaylı</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <!-- Şirket Profili Pretty Checkbox -->
                                                <div class="row" id="companyProfileSection" style="display: none;">
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-start d-block">Şirket Profili</label>
                                                            <div class="pretty p-switch p-fill">
                                                                <input type="checkbox" id="useCompanyProfile" name="use_company_profile" checked>
                                                                <div class="state p-success">
                                                                    <label class="text-start">
                                                                        <i class="fa-solid fa-building me-2"></i> Şirket Profilimi Kullan
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="form-text text-start mt-1">AI, şirket bilgilerinizi kullanarak daha kişiselleştirilmiş içerik üretir</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Test Butonu -->
                                <div class="mt-4 d-flex justify-content-center">
                                    <button type="button" class="test-btn px-5" onclick="testFeatureForm()" id="mainTestBtn">
                                        <span class="btn-text">
                                            <i class="fas fa-magic me-2"></i>
                                            @if($featureModel->id == 201)
                                                Yapay Zeka ile Üret
                                            @else
                                                {{ $featureModel->name }} Çalıştır
                                            @endif
                                        </span>
                                        <span class="loading-spinner spinner-border spinner-border-sm ms-2" role="status" style="display: none;"></span>
                                    </button>
                                </div>
                            </form>

                            <!-- Sonuç Alanı -->
                            <div id="testResults" class="mt-4" style="display: none;">
                                <div class="alert alert-info">
                                    <div class="d-flex">
                                        <div class="spinner-border spinner-border-sm me-2" role="status" id="loadingSpinner">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div id="resultContent">İşleniyor...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* Prowess Card Styling - Same as Prowess Page */
    .prowess-card {
        transition: all 0.3s ease;
        border: 1px solid var(--tblr-border-color);
        background: var(--tblr-card-bg);
        overflow: hidden;
        position: relative;
    }

    .prowess-card:hover {
        box-shadow: var(--tblr-box-shadow-lg);
        border-color: var(--tblr-primary);
    }

    .prowess-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--tblr-primary), var(--tblr-success));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .prowess-card:hover::before {
        opacity: 1;
    }

    /* Feature Header - Same as Prowess Category Header */
    .feature-header {
        background: linear-gradient(135deg, var(--tblr-primary), var(--tblr-blue));
        color: white;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .skill-icon {
        font-size: 3rem;
        line-height: 1;
        margin-bottom: 1rem;
    }

    /* Hide unwanted elements */
    .hidden-field {
        display: none !important;
    }

    .skill-badge {
        background: var(--tblr-success);
        color: white;
        border: none;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
    }

    .test-btn {
        background: linear-gradient(45deg, var(--tblr-primary), var(--tblr-purple));
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 2rem;
        transition: all 0.3s ease;
    }

    .test-btn:hover {
        box-shadow: var(--tblr-box-shadow);
        color: white;
    }

    .accordion-button:not(.collapsed) {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .accordion-button {
        cursor: pointer;
    }

    .accordion-button:hover {
        background-color: var(--tblr-accordion-button-hover-bg, rgba(0, 0, 0, 0.05));
        color: var(--tblr-primary);
    }

    /* Choices.js styling fixes */
    .choices__inner {
        text-align: left !important;
    }

    .choices__list--single .choices__item {
        text-align: left !important;
    }

    .choices__list--dropdown .choices__item {
        text-align: left !important;
    }

    .result-showcase {
        background: var(--tblr-card-bg);
        border: 1px solid var(--tblr-border-color);
        border-radius: 1rem;
        margin-top: 1.5rem;
        overflow: hidden;
    }
</style>
@endpush

@push('js')
<script>
// Choices.js'i başlat
document.addEventListener('DOMContentLoaded', function() {
    // Choices.js dropdown'larını başlat
    const choicesElements = document.querySelectorAll('[data-choices]');
    choicesElements.forEach(element => {
        new Choices(element, {
            searchEnabled: false,
            itemSelectText: '',
            shouldSort: false
        });
    });

    // AI Profil kontrol sistemi başlat
    checkAIProfileAvailability();
    
    // Hedef kitle artık direkt input - ekstra JavaScript gerekmez

    // İçerik uzunluğu range slider
    const contentLengthRange = document.getElementById('contentLength');
    const contentLengthDisplay = document.getElementById('contentLengthDisplay');
    if (contentLengthRange && contentLengthDisplay) {
        // Sabit label'lar - 5 seviyeli sistem
        const labels = ['Çok Kısa', 'Kısa', 'Normal', 'Uzun', 'Detaylı'];

        contentLengthRange.addEventListener('input', function() {
            const value = parseInt(this.value);
            const labelText = labels[value - 1] || 'Normal';
            contentLengthDisplay.textContent = labelText;
            console.log('Content length changed to:', value, '-', labelText);
        });

        // İlk değeri ayarla
        const initialValue = parseInt(contentLengthRange.value) || 3;
        const initialLabel = labels[initialValue - 1] || 'Normal';
        contentLengthDisplay.textContent = initialLabel;
        console.log('Initial content length set to:', initialValue, '-', initialLabel);
    }
});

// Universal feature test fonksiyonu
function testFeatureForm() {
    const mainInput = document.getElementById('mainInput').value;
    
    if (!mainInput.trim()) {
        alert('Lütfen ana içeriği giriniz.');
        return;
    }

    // Form verilerini topla
    const formData = {
        main_input: mainInput,
        writing_tone: document.getElementById('writingTone').value,
        target_audience: document.getElementById('targetAudience').value,
        content_length: document.getElementById('contentLength').value,
        use_company_profile: document.getElementById('useCompanyProfile').checked
    };

    testFeature({{ $featureModel->id }}, mainInput, formData);
}

// AI Feature test fonksiyonu
function testFeature(featureId, input, extraData = {}) {
    const resultsDiv = document.getElementById('testResults');
    const spinner = document.getElementById('loadingSpinner');
    const content = document.getElementById('resultContent');
    
    // Sonuç alanını göster
    resultsDiv.style.display = 'block';
    spinner.style.display = 'inline-block';
    content.innerHTML = 'AI işleniyor...';
    
    // AJAX request
    fetch('{{ route("admin.ai.test-feature") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            feature_id: featureId,
            input_text: input,
            extra_data: extraData
        })
    })
    .then(response => response.json())
    .then(data => {
        spinner.style.display = 'none';
        
        if (data.success) {
            content.innerHTML = `
                <div class="alert alert-success">
                    <h4>✅ İşlem Başarılı!</h4>
                    <div class="mt-3">${data.response}</div>
                    ${data.tokens_used ? `<div class="mt-2"><small class="text-muted">Token Kullanımı: ${data.tokens_used}</small></div>` : ''}
                </div>
            `;
        } else {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <h4>❌ Hata Oluştu</h4>
                    <div class="mt-2">${data.message}</div>
                </div>
            `;
        }
    })
    .catch(error => {
        spinner.style.display = 'none';
        content.innerHTML = `
            <div class="alert alert-danger">
                <h4>❌ Bağlantı Hatası</h4>
                <div class="mt-2">İstek işlenirken hata oluştu: ${error.message}</div>
            </div>
        `;
    });
}

// AI Profil kontrolü fonksiyonu
async function checkAIProfileAvailability() {
    try {
        const response = await fetch('/admin/ai/api/profiles/company-info', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.available) {
            // Şirket profili mevcut - checkbox'ı göster
            const profileSection = document.getElementById('companyProfileSection');
            if (profileSection) {
                profileSection.style.display = 'block';
            }
            console.log('AI Profile available - checkbox shown');
        } else {
            // Şirket profili yok - checkbox gizli kalır
            console.log('AI Profile not available - checkbox hidden');
        }
        
    } catch (error) {
        console.warn('AI Profile check failed:', error);
        // Hata durumunda checkbox gizli kalır
    }
}
</script>
@endpush