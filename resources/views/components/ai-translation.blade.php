@props(['entityType' => 'page', 'entityId' => null, 'buttonClass' => 'link-secondary', 'iconClass' => 'fas fa-language', 'size' => 'fa-lg', 'tooltip' => 'Yapay Zeka ile Çeviri'])

<!-- AI Translation Component - Herhangi bir yerde kullanılabilir -->
<a href="javascript:void(0);" 
   onclick="openTranslationModal('{{ $entityType }}', {{ $entityId }})" 
   data-bs-toggle="tooltip" 
   data-bs-placement="top" 
   style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;" 
   aria-label="{{ $tooltip }}" 
   data-bs-original-title="{{ $tooltip }}"
   class="{{ $buttonClass }}">
    <i class="{{ $iconClass }} {{ $size }}"></i>
</a>

@pushonce('modals')
<!-- AI Translation Modal - Modern Design -->
<div class="modal modal-blur fade" id="aiTranslationModal" tabindex="-1" role="dialog" aria-labelledby="aiTranslationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- Modern Header with Gradient -->
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 0.375rem 0.375rem 0 0;">
                <div class="d-flex align-items-center">
                    <div class="me-3" style="font-size: 1.5rem;">🤖</div>
                    <div>
                        <h4 class="modal-title mb-0" id="aiTranslationModalLabel">AI Çeviri Sistemi</h4>
                        <small style="opacity: 0.9;">Güçlü yapay zeka ile anında çeviri</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <!-- Source Language Selection -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark mb-3">
                        <i class="fas fa-globe-americas me-2 text-primary"></i>Kaynak Dil
                    </label>
                    <div class="source-language-wrapper">
                        <select id="sourceLanguage" class="form-select form-select-lg shadow-sm" style="border: 2px solid #e9ecef; border-radius: 12px; padding: 12px 16px;">
                            <option value="">🌍 Kaynak dili seçin...</option>
                        </select>
                    </div>
                </div>

                <!-- Target Languages -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark mb-3">
                        <i class="fas fa-language me-2 text-success"></i>Hedef Diller
                    </label>
                    <div id="targetLanguagesContainer" class="row g-3">
                        <!-- Modern language cards will be loaded here -->
                    </div>
                </div>

                <!-- Advanced Options -->
                <div class="mb-4">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(45deg, #f8f9fe 0%, #f1f3ff 100%);">
                        <div class="card-body py-3">
                            <div class="form-check form-switch form-check-lg">
                                <input class="form-check-input" type="checkbox" id="overwriteExisting" checked style="transform: scale(1.2);">
                                <label class="form-check-label fw-medium" for="overwriteExisting">
                                    <i class="fas fa-sync-alt me-2 text-warning"></i>Mevcut çevirilerin üzerine yaz
                                    <div class="small text-muted mt-1">Varolan içerikler güncellenecek</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Area - Hidden by default -->
                <div id="translationProgress" class="d-none">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(45deg, #fff5f5 0%, #fff1f1 100%);">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="spinner-border spinner-border-sm text-primary me-3" role="status">
                                    <span class="visually-hidden">İşlem devam ediyor...</span>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark" id="progressMessage">Çeviri başlatılıyor...</div>
                                    <div class="small text-muted">Lütfen bekleyin, işlem devam ediyor</div>
                                </div>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 10px;">
                                <div class="progress-bar bg-gradient progress-bar-striped progress-bar-animated" 
                                     id="progressBar" 
                                     role="progressbar" 
                                     style="width: 0%; border-radius: 10px; background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);" 
                                     aria-valuenow="0" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <div class="row mt-3 text-center">
                                <div class="col-4">
                                    <div class="small text-muted">Hız</div>
                                    <div class="fw-bold text-success">⚡ Süper Hızlı</div>
                                </div>
                                <div class="col-4">
                                    <div class="small text-muted">Kalite</div>
                                    <div class="fw-bold text-primary">💎 Premium</div>
                                </div>
                                <div class="col-4">
                                    <div class="small text-muted">AI Motor</div>
                                    <div class="fw-bold text-warning">🤖 GPT-4</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modern Footer -->
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <div class="d-flex w-100 gap-3">
                    <button type="button" class="btn btn-lg flex-fill" data-bs-dismiss="modal" id="cancelButton" style="border: 2px solid #e9ecef; border-radius: 12px; font-weight: 500;">
                        <i class="fas fa-times me-2"></i>İptal
                    </button>
                    <button type="button" id="startTranslation" class="btn btn-lg btn-primary flex-fill" style="border-radius: 12px; font-weight: 600; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                        <span id="buttonText">
                            <i class="fas fa-magic me-2"></i>Çeviriyi Başlat
                        </span>
                        <span id="buttonSpinner" class="spinner-border spinner-border-sm ms-2" style="display: none;" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endpushonce

@pushonce('scripts')
<!-- AI Translation JavaScript - Sadece bir kez dahil edilir -->
<script src="{{ asset('assets/js/simple-translation-modal.js') }}?v={{ time() }}"></script>

<script>
console.log('🚀 AI Translation Component Script loaded');

// Debug: Console'a Livewire component'leri listele
document.addEventListener('livewire:initialized', () => {
    console.log('🔍 DEBUG: Listing all Livewire components:');
    
    setTimeout(() => {
        const allComponents = Livewire.all();
        allComponents.forEach((comp, index) => {
            if (comp && comp.__instance) {
                const name = comp.__instance.fingerprint?.name || comp.__instance.name || 'unknown';
                const wireId = comp.__instance.id || 'unknown-id';
                console.log(`🔧 Component ${index}: ${name} (${wireId})`);
            }
        });
        
        // Wire ID'leri de listele
        const wireElements = document.querySelectorAll('[wire\\:id]');
        console.log(`🔧 Wire ID elements found: ${wireElements.length}`);
        wireElements.forEach((element, index) => {
            const wireId = element.getAttribute('wire:id');
            try {
                const component = Livewire.find(wireId);
                const name = component?.__instance?.fingerprint?.name || component?.__instance?.name || 'unknown';
                console.log(`🔧 Wire Element ${index}: ${name} (${wireId})`);
            } catch (e) {
                console.log(`🔧 Wire Element ${index}: Error accessing (${wireId})`);
            }
        });
    }, 1000);
});
</script>
@endpushonce