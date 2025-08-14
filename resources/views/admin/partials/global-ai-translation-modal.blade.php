<!-- Global AI Translation Modal - Universal for all modules -->
<div class="modal fade" id="aiTranslationModal" tabindex="-1" aria-labelledby="aiTranslationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="position: relative; overflow: hidden;">
            <div class="modal-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-language fa-2x me-3"></i>
                    <div>
                        <h1 class="modal-title fs-5 mb-0" id="aiTranslationModalLabel">Yapay Zeka Çeviri Sistemi</h1>
                        <small class="opacity-75" id="translationModeText">Universal Translation System</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Translation Form -->
                <form id="aiTranslationForm">
                    <div class="row">
                        <!-- Left Column: Translation Settings -->
                        <div class="col-md-6">

                            <!-- Source Language -->
                            <div class="form-floating mb-4">
                                <select class="form-select" id="sourceLanguage" required>
                                    <option value="">Kaynak dil seçiniz...</option>
                                </select>
                                <label for="sourceLanguage">
                                    <i class="fas fa-globe me-2"></i>Kaynak Dil
                                </label>
                            </div>

                            <!-- Target Languages -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-flag me-2"></i>
                                        Hedef Diller
                                    </h5>
                                </div>
                                <div class="card-body" style="max-height: 180px; overflow-y: auto;">
                                    <div id="targetLanguages" class="row g-2">
                                        <!-- Languages will be loaded here -->
                                        <div class="col-12 text-center text-muted py-3">
                                            <div class="spinner-border spinner-border-sm me-2"></div>
                                            Diller yükleniyor...
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Right Column: Options & Settings -->
                        <div class="col-md-6">
                            
                            <!-- Advanced Options -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-cog me-2"></i>
                                        Çeviri Seçenekleri
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                                <input type="checkbox" id="overwriteExisting" checked />
                                                <div class="state p-success p-on ms-2">
                                                    <label>Mevcut çevirilerin üzerine yaz</label>
                                                </div>
                                                <div class="state p-danger p-off ms-2">
                                                    <label>Mevcut çevirilerin üzerine yaz</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Not:</strong> Tüm içerik alanları (başlık, metin, SEO vb.) otomatik olarak çevrilecektir.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Bulk Operations (when applicable) -->
                            <div class="card mb-4" id="bulkOptionsCard" style="display: none;">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-layer-group me-2"></i>
                                        Toplu İşlem Detayları
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Seçilen İçerikler Listesi -->
                                    <div class="mt-3" id="selectedItemsList">
                                        <h6 class="text-muted mb-2">
                                            <i class="fas fa-list me-2"></i>Çevrilecek İçerikler
                                        </h6>
                                        <div class="small text-muted" id="selectedItemsDetails">
                                            <!-- JavaScript ile doldurulacak -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Translation Progress - Hidden (will be replaced by overlay) -->
                <div id="translationProgress" class="mt-4" style="display: none;">
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-between w-100">
                    <div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" id="cancelBtn">
                            <i class="fas fa-times me-2"></i>İptal
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" id="startTranslationBtn" disabled>
                            <i class="fas fa-magic me-2"></i>Çeviriyi Başlat
                        </button>
                        
                        <!-- Parçalı Çeviri Butonları (gizli başlangıçta) -->
                        <div id="partialTranslationButtons" style="display: none;">
                            <!-- JavaScript ile doldurulacak -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modern AI Translation Overlay -->
            <div class="ai-translation-overlay" id="translationLoadingOverlay" style="display: none;">
                <div class="ai-overlay-backdrop"></div>
                <div class="ai-translation-content">
                    <div class="ai-brain-animation">
                        <div class="ai-robot">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="ai-pulse-rings">
                            <div class="pulse-ring"></div>
                            <div class="pulse-ring"></div>
                            <div class="pulse-ring"></div>
                        </div>
                    </div>
                    <div class="ai-text-content">
                        <h4 class="ai-title" id="aiProgressTitle">🧠 Yapay Zeka Çevirilerinizi Hazırlıyor</h4>
                        <p class="ai-subtitle" id="aiProgressSubtitle">Çoklu dil çevirileri profesyonel kalitede işleniyor...</p>
                        
                        <!-- Progress Bar -->
                        <div class="progress mt-3 mb-3" style="height: 25px; background: rgba(255,255,255,0.2);">
                            <div class="progress-bar progress-bar-striped progress-bar-animated translation-progress-bar" 
                                 role="progressbar" 
                                 style="width: 0%; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);"
                                 aria-valuenow="0" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">0%</div>
                        </div>
                        
                        <!-- Language Status List -->
                        <div id="languageStatusList" class="mt-3" style="max-height: 200px; overflow-y: auto;">
                            <!-- Dil durumları buraya eklenecek -->
                        </div>
                        
                        <!-- Info Badge -->
                        <div id="translationInfoBadge" class="mt-3" style="display: none;">
                            <span class="badge bg-info text-white"></span>
                        </div>
                        
                        <div class="ai-status-dots mt-3">
                            <span class="status-dot"></span>
                            <span class="status-dot"></span>
                            <span class="status-dot"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Custom styles for AI Translation Modal */
#aiTranslationModal .modal-xl {
    max-width: 1200px;
}

#aiTranslationModal .bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

#aiTranslationModal .progress-bar {
    transition: width 0.3s ease;
}

#aiTranslationModal .card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid rgba(0, 0, 0, 0.05);
    border-radius: 0.5rem;
}

#aiTranslationModal .card-header {
    background: rgba(79, 70, 229, 0.05);
    border-bottom: 1px solid rgba(79, 70, 229, 0.1);
    font-weight: 600;
}

#aiTranslationModal .form-check-input:checked {
    background-color: #4f46e5;
    border-color: #4f46e5;
}

/* 🔥 YENİ: Disabled Language Styling */
#aiTranslationModal .disabled-language {
    opacity: 0.3 !important;
    pointer-events: none !important;
    filter: grayscale(70%);
}

#aiTranslationModal .disabled-language label {
    color: #6c757d !important;
    text-decoration: line-through;
}

/* Language Status List Styling */
.language-status-container {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 0.375rem;
    padding: 0.5rem;
    background: rgba(255, 255, 255, 0.05);
}

.language-status-item {
    padding: 0.5rem;
    margin-bottom: 0.25rem;
    border-radius: 0.25rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.language-status-item:last-child {
    margin-bottom: 0;
}

.status-indicator {
    min-width: 20px;
}

.language-name {
    font-size: 0.875rem;
    font-weight: 500;
}

.status-text {
    font-size: 0.75rem;
    opacity: 0.8;
}


/* Button Enhancements */
#aiTranslationModal .btn {
    border-radius: 0.375rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

#aiTranslationModal .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

#aiTranslationModal .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

#aiTranslationModal .btn-primary:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b3b9e 100%);
}

/* Modal Footer Enhancement */
#aiTranslationModal .modal-footer.bg-light {
    background: linear-gradient(180deg, rgba(248, 249, 250, 0.8) 0%, rgba(248, 249, 250, 1) 100%) !important;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.pulse-animation {
    animation: pulse 2s infinite;
}

/* Modern AI Translation Overlay - Modal Only */
.ai-translation-overlay {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 1060;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
}

.ai-overlay-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, 
        rgba(99, 102, 241, 0.92) 0%, 
        rgba(219, 39, 119, 0.92) 100%);
    background-size: 200% 200%;
    backdrop-filter: blur(15px);
    z-index: 1061;
    border-radius: 0.5rem;
    animation: gradientShift 6s ease-in-out infinite;
}

.ai-translation-content {
    position: relative;
    z-index: 1062;
    text-align: center;
    color: white;
    max-width: 500px;
    padding: 2rem;
}

/* AI Brain Animation */
.ai-brain-animation {
    position: relative;
    margin-bottom: 2rem;
}

.ai-robot {
    font-size: 4rem;
    color: #ffffff;
    position: relative;
    z-index: 3;
    animation: robotPulse 2s ease-in-out infinite;
}

.ai-pulse-rings {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.pulse-ring {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 120px;
    height: 120px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    animation: pulseRing 2s ease-out infinite;
}

.pulse-ring:nth-child(2) {
    animation-delay: 0.5s;
}

.pulse-ring:nth-child(3) {
    animation-delay: 1s;
}

/* Text Content */
.ai-text-content {
    animation: fadeInUp 1s ease-out;
}

.ai-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.ai-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

/* Status Dots Animation */
.ai-status-dots {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}

.status-dot {
    width: 8px;
    height: 8px;
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    animation: statusDot 1.5s ease-in-out infinite;
}

.status-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.status-dot:nth-child(3) {
    animation-delay: 0.4s;
}

/* Keyframe Animations */
@keyframes robotPulse {
    0%, 100% { 
        transform: scale(1);
        filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.5));
    }
    50% { 
        transform: scale(1.1);
        filter: drop-shadow(0 0 30px rgba(255, 255, 255, 0.8));
    }
}

@keyframes pulseRing {
    0% {
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(2);
        opacity: 0;
    }
}

@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes statusDot {
    0%, 100% { 
        opacity: 0.3;
        transform: scale(1);
    }
    50% { 
        opacity: 1;
        transform: scale(1.2);
    }
}

@keyframes gradientShift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

/* Partial Translation Buttons */
#partialTranslationButtons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

.partial-translate-btn {
    transition: all 0.2s ease;
}

.partial-translate-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.partial-translate-btn.btn-success {
    pointer-events: none;
    opacity: 0.8;
}
</style>
@endpush

{{-- JavaScript yönetimi: ai-translation-system.js (layout.blade.php'de yükleniyor) --}}