@php
    View::share('pretitle', 'Test 1: Modern Tabler.io Premium');
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
                        <button type="button" class="btn btn-primary" onclick="openModernTranslationModal()">
                            <i class="fa-solid fa-language me-1"></i>
                            Modern Premium Modal
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
                                            <input type="text" class="form-control" placeholder="Test başlığı" value="Test Sayfa Başlığı">
                                            <label>Başlık ★</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" placeholder="test-sayfa-url" value="test-sayfa">
                                            <label>URL Slug</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Content Editor Area --}}
                                <div class="mb-3">
                                    <label class="form-label">İçerik ★</label>
                                    <div style="border: 1px solid #dee2e6; border-radius: 0.375rem; min-height: 300px; padding: 15px; background: #f8f9fa;">
                                        <div style="color: #6c757d; text-align: center; margin-top: 120px;">
                                            <i class="fa-solid fa-file-text" style="font-size: 48px; opacity: 0.3;"></i>
                                            <p class="mt-3">Bu test sayfasında TinyMCE editör simüle edilmiştir</p>
                                            <p>Gerçek sayfalarda burası editör alanıdır</p>
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
                                    <input type="text" class="form-control" placeholder="Meta başlık" value="Test SEO Başlığı">
                                    <label>Meta Başlık</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" style="height: 100px;" placeholder="Meta açıklama">Test SEO açıklaması</textarea>
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

{{-- MODERN TABLER.IO PREMIUM MODAL --}}
<div class="modal modal-blur fade" id="modernTranslationModal" tabindex="-1" role="dialog" aria-labelledby="modernTranslationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 12px; box-shadow: 0 25px 50px rgba(0,0,0,0.2);">
            
            {{-- Premium Header --}}
            <div class="modal-header border-0" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 12px 12px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="me-3" style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-brain" style="color: white; font-size: 24px; animation: pulse 2s infinite;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white mb-0" id="modernTranslationModalLabel">
                            Modern AI Translation v3.0
                        </h5>
                        <small class="text-white-50">Premium Tabler.io Design</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
            </div>
            
            <div class="modal-body" style="color: white;">
                {{-- Language Selection Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-white">
                            <i class="fa-solid fa-flag me-2"></i>Kaynak Dil
                        </label>
                        <div class="card bg-white bg-opacity-10 border-0" style="backdrop-filter: blur(5px);">
                            <div class="card-body p-3">
                                <select class="form-select bg-transparent border-0 text-white" style="color: white !important;">
                                    <option value="tr" style="color: black;">🇹🇷 Türkçe</option>
                                    <option value="en" style="color: black;">🇬🇧 English</option>
                                    <option value="ar" style="color: black;">🇸🇦 العربية</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">
                            <i class="fa-solid fa-bullseye me-2"></i>Hedef Diller
                        </label>
                        <div class="card bg-white bg-opacity-10 border-0" style="backdrop-filter: blur(5px);">
                            <div class="card-body p-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" checked>
                                    <label class="form-check-label text-white">🇬🇧 English</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox">
                                    <label class="form-check-label text-white">🇸🇦 العربية</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Premium Options --}}
                <div class="card bg-white bg-opacity-10 border-0 mb-4" style="backdrop-filter: blur(5px);">
                    <div class="card-body p-3">
                        <h6 class="text-white mb-3">
                            <i class="fa-solid fa-magic-wand-sparkles me-2"></i>Premium Özellikler
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" checked>
                                    <label class="form-check-label text-white">Smart HTML Koruması</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked>
                                    <label class="form-check-label text-white">Kalite Optimizasyonu</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox">
                                    <label class="form-check-label text-white">Mevcut Üzerine Yaz</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked>
                                    <label class="form-check-label text-white">Real-time Preview</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Progress Area (Hidden initially) --}}
                <div id="modernProgressArea" class="d-none">
                    <div class="card bg-white bg-opacity-20 border-0" style="backdrop-filter: blur(10px);">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <div class="spinner-border text-white" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <h6 class="text-white mb-2">Premium AI İşleniyor...</h6>
                            <div class="progress mb-3" style="height: 8px; background: rgba(255,255,255,0.2);">
                                <div class="progress-bar bg-white" role="progressbar" style="width: 45%"></div>
                            </div>
                            <small class="text-white-75">Neural network optimization in progress...</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-0" style="background: rgba(255,255,255,0.05);">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times me-1"></i>İptal
                </button>
                <button type="button" class="btn btn-light" onclick="startModernTranslation()">
                    <i class="fa-solid fa-rocket me-1"></i>Premium Çeviri Başlat
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Modern Modal Overrides */
#modernTranslationModal .modal-content {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

#modernTranslationModal .form-select option {
    color: #000 !important;
    background: white !important;
}

#modernTranslationModal .progress-bar {
    background: linear-gradient(90deg, rgba(255,255,255,0.8), rgba(255,255,255,1), rgba(255,255,255,0.8)) !important;
    animation: progressShimmer 2s infinite;
}

@keyframes progressShimmer {
    0% { background-position: -200px 0; }
    100% { background-position: calc(200px + 100%) 0; }
}
</style>
@endpush

@push('scripts')
<script>
function openModernTranslationModal() {
    console.log('🎨 Modern Premium Modal açılıyor...');
    
    // Reset modal
    document.getElementById('modernProgressArea').classList.add('d-none');
    
    // Modal'ı aç
    const modal = new bootstrap.Modal(document.getElementById('modernTranslationModal'));
    modal.show();
}

function startModernTranslation() {
    console.log('🚀 Premium translation başlatılıyor...');
    
    // Progress area'yı göster
    document.getElementById('modernProgressArea').classList.remove('d-none');
    
    // Simulate progress
    let progress = 0;
    const progressBar = document.querySelector('#modernProgressArea .progress-bar');
    
    const interval = setInterval(() => {
        progress += Math.random() * 10;
        progressBar.style.width = Math.min(progress, 100) + '%';
        
        if (progress >= 100) {
            clearInterval(interval);
            setTimeout(() => {
                document.querySelector('#modernTranslationModal .btn-close').click();
                alert('🎉 Premium çeviri tamamlandı!');
            }, 1000);
        }
    }, 200);
}

// Dark/Light mode compatibility
if (document.body.getAttribute('data-bs-theme') === 'dark') {
    document.documentElement.style.setProperty('--modal-gradient', 'linear-gradient(135deg, #2c3e50 0%, #34495e 100%)');
} else {
    document.documentElement.style.setProperty('--modal-gradient', 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)');
}
</script>
@endpush