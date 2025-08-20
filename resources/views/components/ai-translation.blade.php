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
<!-- AI Translation Modal - Sadece bir kez dahil edilir -->
<div class="modal modal-blur fade" id="aiTranslationModal" tabindex="-1" role="dialog" aria-labelledby="aiTranslationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aiTranslationModalLabel">Yapay Zeka Çeviri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Kaynak dil</label>
                        <select id="sourceLanguage" class="form-select">
                            <option value="">Seçin...</option>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Hedef diller</label>
                        <div id="targetLanguagesContainer" class="row g-2">
                            <!-- Languages will be loaded here -->
                        </div>
                    </div>
                    
                    <div class="col-12 mt-3">
                        <div class="pretty p-default p-curve p-thick p-smooth">
                            <input type="checkbox" id="overwriteExisting" checked>
                            <div class="state p-success-o">
                                <label style="margin-left: 8px;">Mevcut çevirilerin üzerine yaz</label>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Progress Area -->
                    <div class="col-12 mt-3" id="translationProgress" style="display: none;">
                        <div class="d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span id="progressMessage" class="text-muted">Çeviri başlatılıyor...</span>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-primary" id="progressBar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal" id="cancelButton">İptal</button>
                <button type="button" id="startTranslation" class="btn btn-primary">
                    <span id="buttonText">Çevir</span>
                    <span id="buttonSpinner" class="spinner-border spinner-border-sm ms-1" style="display: none;" role="status" aria-hidden="true"></span>
                </button>
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