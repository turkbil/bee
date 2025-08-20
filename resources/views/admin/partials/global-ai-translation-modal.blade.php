<!-- Minimal AI Translation Modal -->
<div class="modal modal-blur fade" id="aiTranslationModal" tabindex="-1" role="dialog" aria-labelledby="aiTranslationModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aiTranslationModalLabel">Yapay Zeka Çeviri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeTranslationModal()"></button>
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
                <button type="button" class="btn" data-bs-dismiss="modal" id="cancelButton" onclick="closeTranslationModal()">İptal</button>
                <button type="button" id="startTranslation" class="btn btn-primary">
                    <span id="buttonText">Çevir</span>
                    <span id="buttonSpinner" class="spinner-border spinner-border-sm ms-1" style="display: none;" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>