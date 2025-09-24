@php
    View::share('pretitle', 'Test 2: Interactive Component System');
@endphp

<div>
    {{-- Page Helper - birebir aynı --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ $testName }}
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        {{-- Test modal butonları --}}
                        <button type="button" class="btn btn-primary" onclick="openInteractiveTranslationModal()">
                            <i class="fa-solid fa-cogs me-1"></i>
                            Interactive System
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content area - Page manage benzeri --}}
    <div class="page-body">
        <div class="container-xl">
            <form method="post">
                <div class="card">
                    
                    {{-- Tab System - Page ile aynı --}}
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#tab-content" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                                    <i class="fa-solid fa-file-text me-1"></i>
                                    İçerik
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tab-seo" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                    <i class="fa-solid fa-search me-1"></i>
                                    SEO
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            {{-- Content Tab --}}
                            <div class="tab-pane fade show active" id="tab-content" role="tabpanel">
                                {{-- Title Field --}}
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" placeholder="Test başlığı" value="Interactive Component Test">
                                            <label>Başlık ★</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" placeholder="test-sayfa-url" value="interactive-test">
                                            <label>URL Slug</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Content Editor Area --}}
                                <div class="mb-3">
                                    <label class="form-label">İçerik ★</label>
                                    <div style="border: 1px solid #dee2e6; border-radius: 0.375rem; min-height: 300px; padding: 15px; background: #f8f9fa;">
                                        <div style="color: #6c757d; text-align: center; margin-top: 120px;">
                                            <i class="fa-solid fa-cogs" style="font-size: 48px; opacity: 0.3;"></i>
                                            <p class="mt-3">Interactive Component System Test</p>
                                            <p>Smart UI bileşenleri burada test edilir</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Active Checkbox --}}
                                <div class="mb-3">
                                    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                        <input type="checkbox" id="is_active" checked />
                                        <div class="state p-success p-on ms-2">
                                            <label>Aktif</label>
                                        </div>
                                        <div class="state p-danger p-off ms-2">
                                            <label>Pasif</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SEO Tab --}}
                            <div class="tab-pane fade" id="tab-seo" role="tabpanel">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" placeholder="Meta başlık" value="Interactive System SEO">
                                    <label>Meta Başlık</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" style="height: 100px;" placeholder="Meta açıklama">Interactive component system ile gelişmiş çeviri deneyimi</textarea>
                                    <label>Meta Açıklama</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Footer --}}
                    <div class="card-footer text-end">
                        <div class="d-flex">
                            <button type="button" class="btn btn-link">İptal</button>
                            <button type="button" class="btn btn-primary ms-auto">
                                <i class="fa-solid fa-save me-1"></i>
                                Kaydet
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

{{-- INTERACTIVE COMPONENT SYSTEM MODAL --}}
<div class="modal modal-blur fade" id="interactiveTranslationModal" tabindex="-1" role="dialog" aria-labelledby="interactiveTranslationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            
            {{-- Interactive Header with Live Stats --}}
            <div class="modal-header bg-primary text-white">
                <div class="d-flex align-items-center w-100">
                    <div class="me-3">
                        <i class="fa-solid fa-microchip fa-2x" id="interactiveIcon" style="animation: rotate 4s linear infinite;"></i>
                    </div>
                    <div class="flex-fill">
                        <h5 class="modal-title mb-0" id="interactiveTranslationModalLabel">
                            Interactive AI Translation System v2.0
                        </h5>
                        <small class="text-white-75">Smart Component Architecture</small>
                    </div>
                    <div class="text-end">
                        <div class="row text-center" style="min-width: 200px;">
                            <div class="col-4">
                                <div style="font-size: 18px; font-weight: bold;" id="liveTokenCount">0</div>
                                <small style="font-size: 10px;">Tokens</small>
                            </div>
                            <div class="col-4">
                                <div style="font-size: 18px; font-weight: bold;" id="liveQuality">--</div>
                                <small style="font-size: 10px;">Quality</small>
                            </div>
                            <div class="col-4">
                                <div style="font-size: 18px; font-weight: bold;" id="liveSpeed">--</div>
                                <small style="font-size: 10px;">Speed</small>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-0">
                {{-- Multi-Step Wizard Interface --}}
                <div class="row g-0">
                    {{-- Left Panel - Interactive Controls --}}
                    <div class="col-md-4 border-end bg-light">
                        <div class="p-4">
                            {{-- Step Indicator --}}
                            <div class="steps mb-4">
                                <div class="step-item active" data-step="1">
                                    <div class="step-counter">1</div>
                                    <div class="step-display">
                                        <div class="step-title">Dil Seçimi</div>
                                        <div class="step-subtitle">Smart Detection</div>
                                    </div>
                                </div>
                                <div class="step-item" data-step="2">
                                    <div class="step-counter">2</div>
                                    <div class="step-display">
                                        <div class="step-title">Kalite Ayarı</div>
                                        <div class="step-subtitle">AI Optimization</div>
                                    </div>
                                </div>
                                <div class="step-item" data-step="3">
                                    <div class="step-counter">3</div>
                                    <div class="step-display">
                                        <div class="step-title">Önizleme</div>
                                        <div class="step-subtitle">Live Preview</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Interactive Language Selector --}}
                            <div class="step-content" id="step-1">
                                <h6 class="mb-3">🎯 Akıllı Dil Seçimi</h6>
                                
                                {{-- Source Language with Auto-detection --}}
                                <div class="mb-3">
                                    <label class="form-label">Kaynak Dil</label>
                                    <div class="input-group">
                                        <select class="form-select" id="sourceLanguageInteractive">
                                            <option value="auto">🔍 Otomatik Algıla</option>
                                            <option value="tr" selected>🇹🇷 Türkçe</option>
                                            <option value="en">🇬🇧 English</option>
                                            <option value="ar">🇸🇦 العربية</option>
                                        </select>
                                        <button class="btn btn-outline-secondary" type="button" onclick="detectLanguage()">
                                            <i class="fa-solid fa-search"></i>
                                        </button>
                                    </div>
                                    <div class="mt-1">
                                        <small class="text-success" id="detectionResult">✓ Türkçe algılandı (95% güven)</small>
                                    </div>
                                </div>

                                {{-- Target Languages with Smart Suggestions --}}
                                <div class="mb-3">
                                    <label class="form-label">Hedef Diller</label>
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <div class="row g-1">
                                                <div class="col-6">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input target-lang" type="checkbox" value="en" checked>
                                                        <label class="form-check-label">🇬🇧</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input target-lang" type="checkbox" value="ar">
                                                        <label class="form-check-label">🇸🇦</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <small class="text-info">💡 <span id="smartSuggestion">English önerilir (popüler seçim)</span></small>
                                    </div>
                                </div>

                                <button class="btn btn-primary w-100" onclick="goToStep(2)">
                                    Kalite Ayarına Geç <i class="fa-solid fa-arrow-right ms-1"></i>
                                </button>
                            </div>

                            {{-- Quality Settings --}}
                            <div class="step-content d-none" id="step-2">
                                <h6 class="mb-3">⚙️ AI Optimizasyon</h6>

                                {{-- Quality Slider --}}
                                <div class="mb-3">
                                    <label class="form-label">Çeviri Kalitesi</label>
                                    <input type="range" class="form-range" id="qualitySlider" min="1" max="5" value="3">
                                    <div class="d-flex justify-content-between">
                                        <small>Hızlı</small>
                                        <small>Dengeli</small>
                                        <small>Yüksek</small>
                                    </div>
                                    <div class="text-center mt-1">
                                        <span class="badge bg-primary" id="qualityBadge">Dengeli Kalite</span>
                                    </div>
                                </div>

                                {{-- Processing Method --}}
                                <div class="mb-3">
                                    <label class="form-label">İşlem Yöntemi</label>
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <div class="form-check mb-1">
                                                <input class="form-check-input" type="radio" name="processMethod" value="smart" checked>
                                                <label class="form-check-label">🧠 Smart Processing</label>
                                            </div>
                                            <div class="form-check mb-1">
                                                <input class="form-check-input" type="radio" name="processMethod" value="chunk">
                                                <label class="form-check-label">🔄 Chunk Processing</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="processMethod" value="stream">
                                                <label class="form-check-label">⚡ Stream Processing</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <button class="btn btn-outline-secondary w-100" onclick="goToStep(1)">
                                            <i class="fa-solid fa-arrow-left me-1"></i> Geri
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-primary w-100" onclick="goToStep(3)">
                                            Önizleme <i class="fa-solid fa-eye ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Preview Settings --}}
                            <div class="step-content d-none" id="step-3">
                                <h6 class="mb-3">👀 Önizleme Ayarları</h6>

                                <div class="mb-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">Canlı Önizleme</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Yan Yana Karşılaştırma</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">Confidence Score</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <button class="btn btn-outline-secondary w-100" onclick="goToStep(2)">
                                            <i class="fa-solid fa-arrow-left me-1"></i> Geri
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-success w-100" onclick="startInteractiveTranslation()">
                                            Başlat <i class="fa-solid fa-play ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Panel - Live Preview & Analytics --}}
                    <div class="col-md-8">
                        <div class="p-4">
                            {{-- Real-time Preview Area --}}
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fa-solid fa-eye me-2"></i>
                                        Canlı Önizleme
                                        <span class="badge bg-success ms-2" id="previewStatus">Hazır</span>
                                    </h6>
                                </div>
                                <div class="card-body" style="min-height: 200px;">
                                    <div id="previewContent" class="text-muted text-center" style="margin-top: 60px;">
                                        <i class="fa-solid fa-magic fa-2x mb-2"></i>
                                        <p>Çeviri başladığında burada canlı önizleme görünecek</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Real-time Analytics --}}
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <div class="text-muted mb-1">Token Kullanımı</div>
                                            <div class="h3 mb-1" id="tokenUsage">0 / 1000</div>
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-primary" id="tokenProgress" style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <div class="text-muted mb-1">Confidence Score</div>
                                            <div class="h3 mb-1" id="confidenceScore">--</div>
                                            <div class="text-success">
                                                <small>Yüksek kalite bekleniyor</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <div class="w-100">
                    <div class="row align-items-center">
                        <div class="col">
                            <small class="text-muted">
                                <span id="processingInfo">Interactive sistem hazır</span>
                            </small>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fa-solid fa-times me-1"></i>Kapat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Step Indicator Styles */
.steps .step-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.75rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.steps .step-item:hover {
    background: rgba(13, 110, 253, 0.1);
}

.steps .step-item.active {
    background: rgba(13, 110, 253, 0.1);
    border-left: 3px solid #0d6efd;
}

.steps .step-counter {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 0.75rem;
    font-size: 0.875rem;
}

.steps .step-item.active .step-counter {
    background: #0d6efd;
    color: white;
}

.steps .step-title {
    font-weight: 600;
    font-size: 0.9rem;
}

.steps .step-subtitle {
    font-size: 0.75rem;
    color: #6c757d;
}

/* Interactive Form Elements */
.form-range::-webkit-slider-thumb {
    background: #0d6efd;
    box-shadow: 0 0 0 1px #fff, 0 0 0 3px rgba(13, 110, 253, 0.3);
}

.form-check-input:checked[type=checkbox] {
    background-color: #0d6efd;
    border-color: #0d6efd;
    animation: checkPulse 0.3s ease;
}

@keyframes checkPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Live Stats Animation */
#liveTokenCount, #liveQuality, #liveSpeed {
    animation: counterUpdate 0.3s ease;
}

@keyframes counterUpdate {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); color: #0d6efd; }
    100% { transform: scale(1); }
}
</style>
@endpush

@push('scripts')
<script>
let currentStep = 1;

function openInteractiveTranslationModal() {
    console.log('🔧 Interactive Modal açılıyor...');
    
    // Reset to step 1
    goToStep(1);
    
    // Start live stats simulation
    startLiveStats();
    
    // Modal'ı aç
    const modal = new bootstrap.Modal(document.getElementById('interactiveTranslationModal'));
    modal.show();
}

function goToStep(stepNum) {
    // Hide all steps
    for (let i = 1; i <= 3; i++) {
        document.getElementById(`step-${i}`).classList.add('d-none');
        document.querySelector(`[data-step="${i}"]`).classList.remove('active');
    }
    
    // Show current step
    document.getElementById(`step-${stepNum}`).classList.remove('d-none');
    document.querySelector(`[data-step="${stepNum}"]`).classList.add('active');
    
    currentStep = stepNum;
    
    // Update preview based on step
    updatePreviewContent(stepNum);
}

function updatePreviewContent(step) {
    const previewContent = document.getElementById('previewContent');
    const previewStatus = document.getElementById('previewStatus');
    
    switch(step) {
        case 1:
            previewContent.innerHTML = `
                <div class="text-center text-muted">
                    <i class="fa-solid fa-language fa-2x mb-2"></i>
                    <p>Dil seçimini tamamlayın</p>
                    <small>Otomatik algılama aktif</small>
                </div>`;
            previewStatus.textContent = 'Dil Seçimi';
            previewStatus.className = 'badge bg-warning ms-2';
            break;
        case 2:
            previewContent.innerHTML = `
                <div class="text-center text-info">
                    <i class="fa-solid fa-cogs fa-2x mb-2"></i>
                    <p>Kalite ayarları yapılandırılıyor</p>
                    <small>Optimum performans için ayarlanıyor</small>
                </div>`;
            previewStatus.textContent = 'Konfigürasyon';
            previewStatus.className = 'badge bg-info ms-2';
            break;
        case 3:
            previewContent.innerHTML = `
                <div class="text-center text-success">
                    <i class="fa-solid fa-check-circle fa-2x mb-2"></i>
                    <p>Sistem hazır, çeviri başlatılabilir</p>
                    <small>Tüm parametreler optimize edildi</small>
                </div>`;
            previewStatus.textContent = 'Hazır';
            previewStatus.className = 'badge bg-success ms-2';
            break;
    }
}

function detectLanguage() {
    const result = document.getElementById('detectionResult');
    result.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Algılanıyor...';
    result.className = 'text-warning';
    
    setTimeout(() => {
        result.innerHTML = '✓ Türkçe algılandı (98% güven)';
        result.className = 'text-success';
        
        // Update smart suggestion
        document.getElementById('smartSuggestion').textContent = 'English ve Arabic önerilir (yüksek başarı oranı)';
    }, 1500);
}

function startLiveStats() {
    let tokenCount = 0;
    let quality = 85;
    let speed = 'Fast';
    
    const interval = setInterval(() => {
        // Simulate live token counting
        if (tokenCount < 1000) {
            tokenCount += Math.floor(Math.random() * 10);
            document.getElementById('liveTokenCount').textContent = tokenCount;
            document.getElementById('tokenUsage').textContent = `${tokenCount} / 1000`;
            document.getElementById('tokenProgress').style.width = `${(tokenCount / 1000) * 100}%`;
        }
        
        // Update quality
        quality = Math.min(100, quality + Math.floor(Math.random() * 3));
        document.getElementById('liveQuality').textContent = quality + '%';
        document.getElementById('confidenceScore').textContent = quality + '%';
        
        // Update speed
        const speeds = ['Fast', 'Med', 'High'];
        speed = speeds[Math.floor(Math.random() * speeds.length)];
        document.getElementById('liveSpeed').textContent = speed;
        
    }, 2000);
    
    // Stop after modal closes
    document.getElementById('interactiveTranslationModal').addEventListener('hidden.bs.modal', () => {
        clearInterval(interval);
    });
}

function startInteractiveTranslation() {
    console.log('🚀 Interactive translation başlatılıyor...');
    
    // Update preview to show processing
    const previewContent = document.getElementById('previewContent');
    const processingInfo = document.getElementById('processingInfo');
    
    previewContent.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h6 class="text-primary">Interactive AI İşleniyor...</h6>
            <div class="progress mb-3" style="height: 10px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" id="mainProgress" style="width: 0%"></div>
            </div>
            <small class="text-muted">Smart component analysis in progress...</small>
        </div>`;
    
    processingInfo.textContent = 'Interactive processing started...';
    
    // Simulate interactive progress
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 15;
        document.getElementById('mainProgress').style.width = Math.min(progress, 100) + '%';
        
        if (progress >= 100) {
            clearInterval(progressInterval);
            previewContent.innerHTML = `
                <div class="text-center text-success">
                    <i class="fa-solid fa-check-circle fa-3x mb-3"></i>
                    <h5>Interactive Çeviri Tamamlandı!</h5>
                    <p>Smart component sistem başarıyla işledi</p>
                    <div class="badge bg-success">100% Success Rate</div>
                </div>`;
            
            setTimeout(() => {
                document.querySelector('#interactiveTranslationModal .btn-close').click();
                alert('🎉 Interactive component system çevirisi tamamlandı!');
            }, 2000);
        }
    }, 300);
}

// Quality slider interaction
document.addEventListener('DOMContentLoaded', function() {
    const qualitySlider = document.getElementById('qualitySlider');
    const qualityBadge = document.getElementById('qualityBadge');
    
    if (qualitySlider) {
        qualitySlider.addEventListener('input', function() {
            const value = parseInt(this.value);
            const qualities = ['Hızlı', 'İyi', 'Dengeli', 'Yüksek', 'Premium'];
            const colors = ['secondary', 'info', 'primary', 'success', 'warning'];
            
            qualityBadge.textContent = qualities[value - 1] + ' Kalite';
            qualityBadge.className = `badge bg-${colors[value - 1]}`;
        });
    }
});
</script>
@endpush