/**
 * Global AI Translation System
 * Universal translation system for all modules
 * Compatible with Page, Portfolio, Announcement, Blog, etc.
 */

class AITranslationSystem {
    constructor() {
        this.modal = null;
        this.isOpen = false;
        this.currentModule = null;
        this.currentItemId = null;
        this.selectedItems = [];
        this.mode = 'single'; // 'single' or 'bulk'
        this.isTranslating = false; // Prevent multiple simultaneous translations
        this.currentOperationId = null; // Track current operation
        this.pollCount = 0; // Smart polling counter
        this.config = {
            baseUrl: '/admin',
            endpoints: {
                languages: '/admin/ai/translation/languages',
                estimate: '/admin/ai/translation/estimate-tokens',
                start: '/admin/ai/translation/start',
                progress: '/admin/ai/translation/progress'
            }
        };
    }

    /**
     * Initialize the translation system
     * @param {Object} options Configuration options
     */
    init(options = {}) {
        this.config = { ...this.config, ...options };
        
        const modalElement = document.getElementById('aiTranslationModal');
        if (modalElement) {
            this.setupModal(modalElement);
            console.log('Modal element found and initialized');
        } else {
            console.warn('AI Translation Modal element not found in DOM');
        }
        
        this.loadLanguages();
        this.setupEventListeners();
        
        console.log('AI Translation System initialized for all modules');
    }

    /**
     * Setup modal with Bootstrap or fallback
     */
    setupModal(modalElement) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            this.modal = new bootstrap.Modal(modalElement);
        } else {
            // Tabler/native fallback
            this.modal = {
                show: () => {
                    modalElement.classList.add('show');
                    modalElement.style.display = 'block';
                    document.body.classList.add('modal-open');
                    modalElement.setAttribute('aria-hidden', 'false');
                    
                    // Add backdrop
                    this.addModalBackdrop();
                    this.isOpen = true;
                },
                hide: () => {
                    modalElement.classList.remove('show');
                    modalElement.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    modalElement.setAttribute('aria-hidden', 'true');
                    
                    // Remove backdrop
                    this.removeModalBackdrop();
                    this.isOpen = false;
                }
            };
        }
    }

    /**
     * Add modal backdrop manually
     */
    addModalBackdrop() {
        const existing = document.getElementById('ai-modal-backdrop');
        if (existing) existing.remove();
        
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'ai-modal-backdrop';
        document.body.appendChild(backdrop);
    }

    /**
     * Remove modal backdrop
     */
    removeModalBackdrop() {
        const backdrop = document.getElementById('ai-modal-backdrop');
        if (backdrop) backdrop.remove();
    }

    /**
     * Load available languages from API
     */
    async loadLanguages() {
        try {
            const response = await fetch(this.config.endpoints.languages);
            if (response.ok) {
                const responseData = await response.json();
                // Handle controller response format: {success: true, data: {languages: [...]}}
                let languages = [];
                if (responseData.success && responseData.data && responseData.data.languages) {
                    languages = responseData.data.languages;
                } else if (Array.isArray(responseData)) {
                    // Fallback for direct array response
                    languages = responseData;
                } else {
                    console.warn('AI Translation: Unexpected response format:', responseData);
                    return [];
                }
                
                console.log('AI Translation: Languages loaded successfully:', languages.length);
                this.populateLanguageOptions(languages);
                return languages;
            } else {
                console.error('AI Translation: Languages API returned error:', response.status);
            }
        } catch (error) {
            console.error('AI Translation: Languages loading error:', error);
        }
        return [];
    }

    /**
     * Populate language select options and checkboxes
     */
    populateLanguageOptions(languages) {
        const sourceSelect = document.getElementById('sourceLanguage');
        const targetSelect = document.getElementById('targetLanguages');
        
        if (sourceSelect) {
            sourceSelect.innerHTML = '<option value="">-- Kaynak Dil Seçin --</option>';
            languages.forEach(lang => {
                const option = document.createElement('option');
                option.value = lang.code;
                option.textContent = `${lang.name} (${lang.code.toUpperCase()})`;
                if (lang.code === 'tr') option.selected = true; // Default Turkish
                sourceSelect.appendChild(option);
            });
            
            // 🚨 OTOMATIK EVENT KALDIRILDI: Manuel seçim için source change'de target'ları otomatik değiştirme
            // sourceSelect.addEventListener('change', () => {
            //     this.updateTargetLanguageOptions(languages);
            // });
        }
        
        if (targetSelect) {
            this.renderTargetLanguages(languages);
            // 🚨 MANUEL SEÇİM: updateTargetLanguageOptions kaldırıldı - kullanıcı manuel seçim yapacak
            // this.updateTargetLanguageOptions(languages);
        }
    }

    /**
     * Render target language checkboxes
     */
    renderTargetLanguages(languages) {
        const targetSelect = document.getElementById('targetLanguages');
        if (!targetSelect) return;
        
        targetSelect.innerHTML = '';
        languages.forEach(lang => {
            const div = document.createElement('div');
            div.className = 'col-6 mb-2';
            div.innerHTML = `
                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                    <input type="checkbox" value="${lang.code}" 
                           id="target_${lang.code}" name="targetLanguages">
                    <div class="state p-success ms-2">
                        <label for="target_${lang.code}">${lang.name}</label>
                    </div>
                </div>
            `;
            targetSelect.appendChild(div);
        });
    }

    /**
     * Update target language options based on source selection
     */
    updateTargetLanguageOptions(languages) {
        const sourceLanguage = this.getSourceLanguage();
        const targetCheckboxes = document.querySelectorAll('input[name="targetLanguages"]');
        
        targetCheckboxes.forEach(checkbox => {
            const isSourceLanguage = checkbox.value === sourceLanguage;
            checkbox.disabled = isSourceLanguage;
            
            // Uncheck if it's the source language
            if (isSourceLanguage && checkbox.checked) {
                checkbox.checked = false;
                
                // 🚨 KRİTİK FİX: Pretty checkbox'ın görsel durumunu da güncelle
                const changeEvent = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(changeEvent);
            }
            
            // Update pretty checkbox container styling for disabled items
            const prettyContainer = checkbox.closest('.pretty');
            if (prettyContainer) {
                if (isSourceLanguage) {
                    prettyContainer.style.opacity = '0.5';
                    prettyContainer.style.filter = 'grayscale(100%)';
                } else {
                    prettyContainer.style.opacity = '';
                    prettyContainer.style.filter = '';
                }
            }
        });
        
        // NOT AUTO-SELECT: Let user choose languages manually
        // Removed auto-selection to allow user choice
    }

    /**
     * Auto-select all target languages except source
     */
    autoSelectTargetLanguages(excludeLanguage) {
        const targetCheckboxes = document.querySelectorAll('input[name="targetLanguages"]');
        targetCheckboxes.forEach(checkbox => {
            if (checkbox.value !== excludeLanguage && !checkbox.disabled) {
                checkbox.checked = true;
                
                // 🚨 KRİTİK FİX: Pretty checkbox görselini de güncelle
                const prettyContainer = checkbox.closest('.pretty');
                if (prettyContainer) {
                    // Checked state class'ını ekle
                    prettyContainer.classList.add('state');
                    // Change event'ini tetikle ki pretty checkbox güncelle
                    const changeEvent = new Event('change', { bubbles: true });
                    checkbox.dispatchEvent(changeEvent);
                }
            }
        });
    }

    /**
     * Setup all event listeners
     */
    setupEventListeners() {
        const modalElement = document.getElementById('aiTranslationModal');
        if (!modalElement) return;

        // Close button events
        const closeButtons = modalElement.querySelectorAll('[data-bs-dismiss="modal"], .btn-close');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => this.close());
        });

        // 🚨 KAYNAK DİL DEĞİŞİMİ EVENT'İ - HEDEFLERİ OTOMATIK DISABLE ET
        const sourceSelect = document.getElementById('sourceLanguage');
        if (sourceSelect) {
            sourceSelect.addEventListener('change', (e) => {
                console.log('🌍 Kaynak dil değişti:', e.target.value);
                this.handleSourceLanguageChange(e.target.value);
                this.debounce(() => this.updateTokenEstimation(), 500);
            });
        }

        // 🔥 YENİ CLEAN CHECKBOX SİSTEMİ - Pretty Checkbox problemini çöz
        setTimeout(() => {
            this.setupCleanCheckboxSystem();
        }, 300);

        // Translation start button with loading overlay
        const startButton = modalElement.querySelector('#startTranslationBtn');
        if (startButton) {
            startButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.showLoadingOverlay();
                this.startTranslation();
            });
        }

        // Select all languages button
        const selectAllBtn = modalElement.querySelector('#selectAllLanguages');
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', () => this.selectAllLanguages());
        }

        // Clear all languages button
        const clearAllBtn = modalElement.querySelector('#clearAllLanguages');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', () => this.clearAllLanguages());
        }

        // Cancel translation button
        const cancelBtn = modalElement.querySelector('#cancelTranslationBtn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.cancelTranslation());
        }

        // Listen for Livewire bulk translation event
        window.addEventListener('openBulkTranslationModal', (event) => {
            const data = event.detail[0] || event.detail;
            this.openBulkTranslation(data.selectedItems, data.module);
        });
    }

    /**
     * Open translation modal for specific module and item
     * @param {string} module Module name (page, portfolio, announcement, etc.)
     * @param {number} itemId Item ID to translate
     * @param {string} mode Translation mode ('single' or 'bulk')
     * @param {Array} selectedItems Array of selected item IDs for bulk mode
     */
    open(module, itemId, mode = 'single', selectedItems = []) {
        // Validate itemId - don't open modal for invalid items
        if (!itemId || itemId === 'null' || itemId === null || itemId === undefined) {
            this.showAlert('Bu kayıt henüz kaydedilmediği için çeviri yapılamaz. Önce kaydı yapın.', 'warning');
            return;
        }
        
        this.currentModule = module;
        this.currentItemId = itemId;
        this.mode = mode;
        this.selectedItems = selectedItems;
        
        // Update modal title and info
        this.updateModalTitle();
        this.updateModalInfo();
        
        // Update endpoint URLs for current module
        this.updateEndpointsForModule(module);
        
        // Show/hide bulk options based on mode
        this.toggleBulkOptions();
        
        // Show modal
        if (this.modal) {
            this.modal.show();
        }
        
        // 🚨 İLK AÇILIŞTA: Kaynak dil hariç hepsini seç, sonra manuel kontrol edilebilir
        setTimeout(() => {
            this.autoSelectAllExceptSource();
            this.updateTokenEstimation();
        }, 300); // Event listener'dan ÖNCE çalışması için timing azaltıldı
    }

    /**
     * Open bulk translation modal
     * @param {Array} selectedItems Array of selected items with their data
     * @param {string} module Module name
     */
    openBulkTranslation(selectedItems, module) {
        console.log('openBulkTranslation called', {selectedItems, module});
        
        if (!selectedItems || selectedItems.length === 0) {
            this.showAlert('Lütfen çevirmek istediğiniz öğeleri seçin.', 'warning');
            return;
        }
        
        // Wait a bit for DOM to be ready
        setTimeout(() => {
            // Check if modal element exists
            let modalElement = document.getElementById('aiTranslationModal');
            
            if (!modalElement) {
                console.error('Modal element not found! Checking alternative methods...');
                
                // Try to find modal by class
                modalElement = document.querySelector('.modal#aiTranslationModal');
                
                if (!modalElement) {
                    // Log all modals to debug
                    const allModals = document.querySelectorAll('.modal');
                    console.log('All modals found:', allModals.length);
                    allModals.forEach((modal, index) => {
                        console.log(`Modal ${index}:`, modal.id, modal.className);
                    });
                    
                    this.showAlert('Çeviri modülü yüklenemedi. Sayfayı yenileyin.', 'error');
                    return;
                }
            }
            
            console.log('Modal element found:', modalElement);
            console.log('Modal display style:', window.getComputedStyle(modalElement).display);
            console.log('Modal visibility:', window.getComputedStyle(modalElement).visibility);
            
            // Force dispose existing modal if any
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const existingModal = bootstrap.Modal.getInstance(modalElement);
                if (existingModal) {
                    console.log('Disposing existing modal instance...');
                    existingModal.dispose();
                }
                
                // Create fresh modal instance
                this.modal = new bootstrap.Modal(modalElement, {
                    keyboard: false,
                    backdrop: 'static'
                });
                console.log('New Bootstrap modal created');
            } else {
                // Fallback to manual modal
                this.setupModal(modalElement);
                console.log('Manual modal setup completed');
            }
            
            // Store selected items with their full data
            this.selectedItems = selectedItems;
            this.currentModule = module;
            this.mode = 'bulk';
            
            // Use the first item's ID as current item for compatibility
            this.currentItemId = selectedItems[0].id;
            
            console.log('Modal state before opening:', {
                modal: this.modal,
                modalElement: modalElement,
                selectedItems: this.selectedItems,
                currentModule: this.currentModule,
                mode: this.mode
            });
            
            // Update modal title and info
            this.updateModalTitle();
            this.updateModalInfo();
            
            // Show selected items list for bulk mode
            if (this.mode === 'bulk') {
                this.displaySelectedItems();
            }
            
            // Update endpoint URLs for current module
            this.updateEndpointsForModule(module);
            
            // Show/hide bulk options based on mode
            this.toggleBulkOptions();
            
            // Show modal
            if (this.modal) {
                console.log('Showing modal with Bootstrap...');
                
                // For manual modal, use our show method
                if (this.modal.show) {
                    this.modal.show();
                }
                
                // Ensure modal is visible
                setTimeout(() => {
                    modalElement.classList.add('show');
                    modalElement.style.display = 'block';
                    modalElement.setAttribute('aria-hidden', 'false');
                    modalElement.setAttribute('aria-modal', 'true');
                    modalElement.setAttribute('role', 'dialog');
                    document.body.classList.add('modal-open');
                    
                    // Ensure backdrop exists
                    if (!document.getElementById('ai-modal-backdrop')) {
                        this.addModalBackdrop();
                    }
                    
                    console.log('Modal forced to show, display:', modalElement.style.display);
                }, 100);
            } else {
                console.error('Modal not initialized!');
            }
            
            // Load initial estimation after modal is shown
            setTimeout(() => {
                // Reload languages in case they didn't load initially
                this.loadLanguages().then(() => {
                    // For bulk mode, enable button immediately if we have items
                    if (this.mode === 'bulk' && this.selectedItems.length > 0) {
                        this.enableTranslationStart(true);
                    }
                    // Update token estimation
                    this.updateTokenEstimation();
                });
            }, 800);
        }, 100); // Small delay to ensure DOM is ready
    }

    /**
     * Update modal title based on module and mode
     */
    updateModalTitle() {
        const titleElement = document.getElementById('aiTranslationModalLabel');
        const modeElement = document.getElementById('translationModeText');
        
        const moduleNames = {
            'page': 'Sayfa',
            'portfolio': 'Portfolio',
            'announcement': 'Duyuru',
            'blog': 'Blog',
            'product': 'Ürün'
        };
        
        const moduleName = moduleNames[this.currentModule] || (this.currentModule ? this.currentModule.charAt(0).toUpperCase() + this.currentModule.slice(1) : 'Modül');
        
        if (titleElement) {
            titleElement.textContent = `Yapay Zeka ${moduleName} Çevirisi`;
        }
        
        if (modeElement) {
            if (this.mode === 'bulk') {
                modeElement.textContent = `Toplu çeviri (${this.selectedItems.length} ${moduleName.toLowerCase()})`;
            } else {
                modeElement.textContent = `Tekil ${moduleName.toLowerCase()} çevirisi`;
            }
        }
    }

    /**
     * Update API endpoints for current module
     */
    updateEndpointsForModule(module) {
        // AI translation endpoints are centralized in AI module
        this.config.currentEndpoints = {
            estimate: this.config.endpoints.estimate,
            start: this.config.endpoints.start,
            progress: this.config.endpoints.progress
        };
    }

    /**
     * Close translation modal and reset form
     */
    close() {
        // Hide loading overlay first
        this.hideLoadingOverlay();
        
        if (this.modal) {
            this.modal.hide();
        }
        
        this.resetForm();
        this.currentModule = null;
        this.currentItemId = null;
        this.selectedItems = [];
    }

    /**
     * Reset form to initial state
     */
    resetForm() {
        const form = document.getElementById('aiTranslationForm');
        if (form) {
            // Don't completely reset, keep language selections
            // form.reset();
        }
        
        // Hide progress
        const progressContainer = document.getElementById('translationProgress');
        if (progressContainer) {
            progressContainer.style.display = 'none';
        }
        
        // Clear estimation
        const estimationElement = document.getElementById('tokenEstimation');
        if (estimationElement) {
            estimationElement.innerHTML = '<div class="text-muted small">Dil seçimi yapın...</div>';
        }
        
        // Enable start button
        const startButton = document.getElementById('startTranslationBtn');
        if (startButton) {
            startButton.disabled = false;
            startButton.innerHTML = '<i class="fas fa-play me-2"></i>Çeviriyi Başlat';
        }
    }

    /**
     * Update token estimation based on current selections
     */
    async updateTokenEstimation() {
        const selectedTargets = this.getSelectedTargetLanguages();
        const sourceLanguage = this.getSourceLanguage();
        const quality = this.getTranslationQuality();

        // For bulk mode, be more lenient - enable if we have items even without language selection
        if (this.mode === 'bulk' && this.selectedItems.length > 0) {
            if (!selectedTargets.length || !sourceLanguage) {
                this.enableTranslationStart(true); // Enable button even without complete language selection
                this.displayEmptyEstimation();
                return;
            }
        } else if (!selectedTargets.length || !sourceLanguage || 
                   (this.mode === 'single' && !this.currentItemId)) {
            this.displayEmptyEstimation();
            return;
        }

        try {
            // For bulk mode, extract IDs from selectedItems objects
            let items;
            if (this.mode === 'single') {
                items = [this.currentItemId];
            } else {
                // If selectedItems contains objects, extract IDs
                items = this.selectedItems.map(item => {
                    return typeof item === 'object' ? item.id : item;
                });
            }
            
            const response = await fetch(this.config.currentEndpoints.estimate, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: JSON.stringify({
                    items: items,
                    source_language: sourceLanguage,
                    target_languages: selectedTargets,
                    quality: quality || 'balanced'
                })
            });

            if (response.ok) {
                const data = await response.json();
                this.displayTokenEstimation(data);
                this.enableTranslationStart(true);
            } else {
                this.displayEstimationError();
                this.enableTranslationStart(false);
            }
        } catch (error) {
            console.error('AI Translation: Token estimation error:', error);
            this.displayEstimationError();
            this.enableTranslationStart(false);
        }
    }

    /**
     * Display token estimation results
     */
    displayTokenEstimation(data) {
        const estimationElement = document.getElementById('tokenEstimation');
        if (!estimationElement || !data) return;
        
        const costClass = data.total_tokens > 5000 ? 'alert-warning' : 'alert-info';
        
        estimationElement.innerHTML = `
            <div class="alert ${costClass} mb-0">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-calculator me-2"></i>
                    <h6 class="mb-0">Tahmini Maliyet</h6>
                </div>
                <div class="row small">
                    <div class="col-6">
                        <strong>Toplam Token:</strong> ${(data.total_tokens || 0).toLocaleString()}
                    </div>
                    <div class="col-6">
                        <strong>Tahmini Süre:</strong> ${data.estimated_time || '1-2 dakika'}
                    </div>
                    <div class="col-6 mt-1">
                        <strong>İçerik Sayısı:</strong> ${data.item_count || 1}
                    </div>
                    <div class="col-6 mt-1">
                        <strong>Hedef Dil:</strong> ${data.language_count || 1}
                    </div>
                </div>
                ${data.total_tokens > 5000 ? '<div class="mt-2 small text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Yüksek token kullanımı</div>' : ''}
            </div>
        `;
    }

    /**
     * Display empty estimation message
     */
    displayEmptyEstimation() {
        const estimationElement = document.getElementById('tokenEstimation');
        if (estimationElement) {
            estimationElement.innerHTML = '<div class="text-muted small">Kaynak dil ve hedef dil seçin...</div>';
        }
        this.enableTranslationStart(false);
    }

    /**
     * Display estimation error
     */
    displayEstimationError() {
        const estimationElement = document.getElementById('tokenEstimation');
        if (estimationElement) {
            estimationElement.innerHTML = '<div class="alert alert-danger small mb-0">Maliyet hesaplanırken hata oluştu.</div>';
        }
    }

    /**
     * Enable or disable translation start button
     */
    enableTranslationStart(enabled) {
        const startButton = document.getElementById('startTranslationBtn');
        if (startButton) {
            startButton.disabled = !enabled;
        }
    }

    /**
     * Show loading overlay
     */
    showLoadingOverlay() {
        const overlay = document.getElementById('translationLoadingOverlay');
        if (overlay) {
            overlay.style.display = 'flex';
        }
        
        // Disable cancel button and other modal interactions
        const cancelBtn = document.getElementById('cancelBtn');
        const startBtn = document.getElementById('startTranslationBtn');
        if (cancelBtn) cancelBtn.disabled = true;
        if (startBtn) startBtn.disabled = true;
    }

    /**
     * Hide loading overlay
     */
    hideLoadingOverlay() {
        const overlay = document.getElementById('translationLoadingOverlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
        
        // Re-enable buttons
        const cancelBtn = document.getElementById('cancelBtn');
        const startBtn = document.getElementById('startTranslationBtn');
        if (cancelBtn) cancelBtn.disabled = false;
        if (startBtn) startBtn.disabled = false;
    }

    /**
     * Start translation process
     */
    async startTranslation() {
        // Prevent multiple simultaneous translations
        if (this.isTranslating) {
            console.warn('Translation already in progress, ignoring new request');
            return;
        }

        const selectedTargets = this.getSelectedTargetLanguages();
        const sourceLanguage = this.getSourceLanguage();
        const quality = this.getTranslationQuality();

        // Debug log
        console.log('Translation Debug:', {
            selectedTargets,
            sourceLanguage,
            quality,
            currentItemId: this.currentItemId,
            targetCount: selectedTargets.length
        });

        if (!this.currentItemId || this.currentItemId === 'null') {
            this.showAlert('Bu sayfa henüz kaydedilmediği için çeviri yapılamaz. Önce sayfayı kaydedin.', 'warning');
            return;
        }
        
        if (!selectedTargets.length || !sourceLanguage) {
            this.showAlert('Lütfen kaynak dil ve hedef dilleri seçin.', 'warning');
            return;
        }

        try {
            // Set translation state
            this.isTranslating = true;
            this.pollCount = 0; // Reset poll counter for new translation
            
            // Show progress and disable button
            this.showProgress('Çeviri başlatılıyor...', 0);
            this.enableTranslationStart(false);

            // For bulk mode, extract IDs from selectedItems objects
            let items;
            if (this.mode === 'single') {
                items = [this.currentItemId];
            } else {
                // If selectedItems contains objects, extract IDs
                items = this.selectedItems.map(item => {
                    return typeof item === 'object' ? item.id : item;
                });
            }
            
            const response = await fetch(this.config.currentEndpoints.start, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: JSON.stringify({
                    items: items,
                    source_language: sourceLanguage,
                    target_languages: selectedTargets,
                    quality: quality || 'balanced'
                })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.operation_id) {
                    this.trackProgress(data.operation_id);
                } else {
                    this.hideLoadingOverlay();
                    this.showAlert('Çeviri başlatılamadı.', 'error');
                    this.enableTranslationStart(true);
                }
            } else {
                this.hideLoadingOverlay();
                const errorData = await response.json();
                this.showAlert(errorData.message || 'Çeviri başlatılırken hata oluştu.', 'error');
                this.enableTranslationStart(true);
            }
        } catch (error) {
            console.error('AI Translation: Start error:', error);
            this.hideLoadingOverlay();
            this.showAlert('Çeviri başlatılırken sistem hatası oluştu.', 'error');
            this.enableTranslationStart(true);
        }
    }

    /**
     * Track translation progress
     */
    async trackProgress(operationId) {
        const checkProgress = async () => {
            try {
                const response = await fetch(`${this.config.currentEndpoints.progress}/${operationId}`);
                if (response.ok) {
                    const data = await response.json();
                    this.updateProgressDisplay(data);

                    if (data.status === 'completed') {
                        this.isTranslating = false;
                        this.currentOperationId = null;
                        this.showProgress('Çeviri tamamlandı!', 100);
                        setTimeout(() => {
                            this.hideLoadingOverlay();
                            this.close();
                            this.refreshPage();
                        }, 3000);
                    } else if (data.status === 'failed') {
                        this.isTranslating = false;
                        this.currentOperationId = null;
                        this.hideLoadingOverlay();
                        this.showAlert('Çeviri işlemi başarısız oldu.', 'error');
                        this.enableTranslationStart(true);
                    } else {
                        // AKILLI POLLING - Maksimum 3 kez kontrol et
                        this.pollCount = (this.pollCount || 0) + 1;
                        if (this.pollCount <= 3) {
                            console.log(`Smart polling: attempt ${this.pollCount}/3`);
                            setTimeout(() => this.checkProgressOnce(operationId), 10000); // 10 saniye bekle
                        } else {
                            console.log('Max polling reached - stopping. User can refresh manually.');
                            this.showProgress('⏳ Çeviri arka planda devam ediyor... (Birkaç dakika sonra sayfayı yenileyin)', 50);
                        }
                    }
                }
            } catch (error) {
                console.error('AI Translation: Progress tracking error:', error);
                this.showAlert('İlerleme takip edilirken hata oluştu.', 'error');
            }
        };

        checkProgress();
    }

    /**
     * Check progress once (for smart polling)
     */
    async checkProgressOnce(operationId) {
        try {
            const response = await fetch(`/admin/ai/translation/progress/${operationId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateProgressDisplay(data);
                
                // Eğer tamamlandı veya başarısız olduysa polling'i durdur
                if (data.status === 'completed' || data.status === 'failed') {
                    this.enableTranslationStart(true);
                    this.currentOperationId = null;
                    this.isTranslating = false;
                    
                    if (data.status === 'completed') {
                        setTimeout(() => {
                            this.hideProgress();
                            location.reload(); // Sayfayı yenile
                        }, 2000);
                    }
                } else {
                    // Devam eden işlem için bir sonraki polling'i planla
                    this.pollCount = (this.pollCount || 0) + 1;
                    if (this.pollCount <= 3) {
                        console.log(`Smart polling continues: attempt ${this.pollCount}/3`);
                        setTimeout(() => this.checkProgressOnce(operationId), 10000);
                    } else {
                        console.log('Max polling reached - stopping check.');
                        this.showProgress('⏳ Çeviri arka planda devam ediyor... (Birkaç dakika sonra sayfayı yenileyin)', 50);
                    }
                }
            }
        } catch (error) {
            console.error('Progress check error:', error);
        }
    }

    /**
     * Update progress display
     */
    updateProgressDisplay(data) {
        const percentage = Math.min(data.progress || 0, 100);
        let message = data.message || 'İşleniyor...';
        
        // Status'a göre detaylı mesaj göster
        if (data.status === 'initializing') {
            message = '⚡ Çeviri motoru başlatılıyor, lütfen bekleyin...';
        } else if (data.status === 'starting') {
            message = '🚀 Çeviri sistemi hazırlanıyor, yakında başlayacak...';
        } else if (data.status === 'queued') {
            message = '📋 Çeviri işlemi sıraya alındı, hazırlanıyor...';
        } else if (data.status === 'processing') {
            message = `🔄 İşleniyor: ${data.current_language || 'Tüm diller'}`;
        } else if (data.status === 'completed') {
            message = '✅ Çeviri tamamlandı!';
        } else if (data.status === 'failed') {
            message = '❌ Çeviri işlemi başarısız oldu';
        }
        
        // Progress detayını console'a log et
        console.log('AI Translation Progress:', {
            status: data.status,
            progress: percentage,
            message: message,
            operation_id: data.operation_id || 'unknown'
        });
        
        this.showProgress(message, percentage);
    }

    /**
     * Show progress with modern AI overlay
     */
    showProgress(message, percentage) {
        const overlay = document.getElementById('translationLoadingOverlay');
        const titleElement = document.getElementById('aiProgressTitle');
        const subtitleElement = document.getElementById('aiProgressSubtitle');
        const progressBar = document.querySelector('.translation-progress-bar');

        if (overlay) {
            overlay.style.display = 'flex';
        }

        // Progress bar'ı güncelle
        if (progressBar) {
            progressBar.style.width = percentage + '%';
            progressBar.setAttribute('aria-valuenow', percentage);
            progressBar.textContent = Math.round(percentage) + '%';
        }

        if (titleElement) {
            // Get localized title messages from global admin translations
            const titleMessages = {
                100: window.adminTranslations?.ai_translation_wizard_working || 'Yapay Zeka Çeviri Sihirbazı İş Başında',
                80: window.adminTranslations?.ai_translation_almost_done || 'Çeviriler Neredeyse Tamam',
                50: window.adminTranslations?.ai_translation_language_analysis || 'Dil Analizi Devam Ediyor',
                0: window.adminTranslations?.ai_translation_preparing || 'Yapay Zeka Çevirilerinizi Hazırlıyor'
            };
            
            if (percentage >= 100) {
                titleElement.innerHTML = '<nobr>' + titleMessages[100] + '</nobr>';
            } else if (percentage >= 80) {
                titleElement.innerHTML = '<nobr>' + titleMessages[80] + '</nobr>';
            } else if (percentage >= 50) {
                titleElement.innerHTML = '<nobr>' + titleMessages[50] + '</nobr>';
            } else {
                titleElement.innerHTML = '<nobr>' + titleMessages[0] + '</nobr>';
            }
        }

        if (subtitleElement) {
            subtitleElement.style.display = 'block'; // Show subtitle again
            if (percentage >= 100) {
                const completedMessage = window.adminTranslations?.ai_translation_global_reach || 'İfadeleriniz dünyanın her yerinde karşılık bulacak';
                subtitleElement.innerHTML = '<nobr>' + completedMessage + '</nobr>';
            } else {
                const processingMessage = window.adminTranslations?.ai_translation_processing || 'Çoklu dil çevirileri profesyonel kalitede işleniyor...';
                subtitleElement.innerHTML = '<nobr>' + (message || processingMessage) + '</nobr>';
            }
        }
        
        // Console'a debug bilgisi yaz
        console.log('Progress updated:', {
            percentage: percentage,
            message: message,
            progressBarFound: !!progressBar,
            titleFound: !!titleElement,
            subtitleFound: !!subtitleElement
        });
    }

    /**
     * Show alert message
     */
    showAlert(message, type = 'info') {
        // You can integrate with your existing toast/alert system
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: type === 'error' ? 'Hata' : 'Bilgi',
                text: message,
                icon: type === 'error' ? 'error' : type === 'warning' ? 'warning' : 'info',
                timer: 4000
            });
        } else {
            alert(message);
        }
    }

    /**
     * Refresh page to show changes
     */
    refreshPage() {
        if (typeof Livewire !== 'undefined' && Livewire.first()) {
            // Livewire page refresh
            Livewire.first().call('$refresh');
        } else {
            // Standard page reload
            window.location.reload();
        }
    }

    // Helper methods
    getSelectedTargetLanguages() {
        return Array.from(document.querySelectorAll('input[name="targetLanguages"]:checked')).map(cb => cb.value);
    }

    getSourceLanguage() {
        const sourceSelect = document.getElementById('sourceLanguage');
        return sourceSelect?.value || '';
    }

    getTranslationQuality() {
        const qualitySelect = document.getElementById('translationQuality');
        return qualitySelect?.value || 'balanced';
    }

    getCSRFToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        const token = metaTag?.getAttribute('content') || '';
        console.log('CSRF Token Debug:', {
            metaTag: !!metaTag,
            token: token ? token.substring(0, 10) + '...' : 'EMPTY',
            tokenLength: token.length
        });
        return token;
    }

    /**
     * Update modal info section
     */
    updateModalInfo() {
        const currentModuleElement = document.getElementById('currentModule');
        const currentModeElement = document.getElementById('currentMode');
        const selectedItemsElement = document.getElementById('selectedItemsInfo');
        
        const moduleNames = {
            'page': 'Sayfa',
            'portfolio': 'Portfolio', 
            'announcement': 'Duyuru',
            'blog': 'Blog',
            'product': 'Ürün'
        };
        
        const moduleName = moduleNames[this.currentModule] || this.currentModule?.charAt(0).toUpperCase() + this.currentModule?.slice(1);
        
        if (currentModuleElement) {
            currentModuleElement.textContent = moduleName || '-';
        }
        
        if (currentModeElement) {
            currentModeElement.textContent = this.mode === 'bulk' ? 'Toplu Çeviri' : 'Tekil Çeviri';
        }
        
        if (selectedItemsElement) {
            if (this.mode === 'bulk' && this.selectedItems.length > 0) {
                selectedItemsElement.textContent = `${this.selectedItems.length} ${moduleName?.toLowerCase() || 'öğe'} seçili`;
            } else if (this.mode === 'single' && this.currentItemId) {
                selectedItemsElement.textContent = `1 ${moduleName?.toLowerCase() || 'öğe'}`;
            } else {
                selectedItemsElement.textContent = '-';
            }
        }
    }

    /**
     * Toggle bulk options visibility
     */
    toggleBulkOptions() {
        const bulkCard = document.getElementById('bulkOptionsCard');
        if (bulkCard) {
            bulkCard.style.display = this.mode === 'bulk' ? 'block' : 'none';
        }
    }

    /**
     * Display selected items list for bulk translation
     */
    displaySelectedItems() {
        const detailsElement = document.getElementById('selectedItemsDetails');
        if (!detailsElement) return;
        
        if (this.selectedItems && this.selectedItems.length > 0) {
            let html = '<div class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">';
            
            this.selectedItems.forEach((item, index) => {
                const title = item.title ? 
                    (typeof item.title === 'object' ? (item.title.tr || item.title.en || 'Başlıksız') : item.title) 
                    : `İçerik #${item.id}`;
                
                html += `
                    <div class="list-group-item py-2 px-3">
                        <div class="d-flex align-items-center">
                            <span class="badge me-2">${index + 1}</span>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">${this.escapeHtml(title)}</div>
                            </div>
                            <i class="fas fa-check-circle opacity-50"></i>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            html += `<div class="mt-2 text-center small">
                        <i class="fas fa-info-circle me-1"></i>
                        Toplam ${this.selectedItems.length} içerik çevrilecek
                     </div>`;
            
            detailsElement.innerHTML = html;
        } else {
            detailsElement.innerHTML = '<div class="text-center py-3">Seçili içerik bulunamadı</div>';
        }
    }

    /**
     * Escape HTML special characters
     */
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    /**
     * Select all target languages (except source and disabled)
     */
    selectAllLanguages() {
        const checkboxes = document.querySelectorAll('input[name="targetLanguages"]');
        checkboxes.forEach(checkbox => {
            if (!checkbox.checked && !checkbox.disabled) {
                checkbox.checked = true;
                // Trigger change event for token estimation
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    }

    /**
     * Clear all target languages
     */
    clearAllLanguages() {
        const checkboxes = document.querySelectorAll('input[name="targetLanguages"]');
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                checkbox.checked = false;
                // Trigger change event for token estimation
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    }

    /**
     * İLK AÇILIŞTA: Kaynak dil hariç hepsini seç, sonra manuel kontrol edilebilir
     */
    autoSelectAllExceptSource() {
        console.log('🚨 INITIAL SELECT: İlk açılışta dilleri seç - event listener conflictı olmadan');
        
        const sourceLanguage = this.getSourceLanguage() || 'tr';
        const checkboxes = document.querySelectorAll('input[name="targetLanguages"]');
        
        checkboxes.forEach((checkbox) => {
            const isSourceLanguage = checkbox.value === sourceLanguage;
            
            if (!isSourceLanguage) {
                // Source dil değilse seç
                checkbox.checked = true;
                
                // Pretty Checkbox görselini güncelle
                const prettyContainer = checkbox.closest('.pretty');
                if (prettyContainer) {
                    prettyContainer.classList.add('state');
                    const stateDiv = prettyContainer.querySelector('.state');
                    if (stateDiv) {
                        stateDiv.classList.add('p-is-checked');
                    }
                }
                
                console.log(`✅ Initial select: ${checkbox.value} seçildi`);
            } else {
                // Source dili disable et
                checkbox.checked = false;
                checkbox.disabled = true;
                
                const prettyContainer = checkbox.closest('.pretty');
                if (prettyContainer) {
                    prettyContainer.style.opacity = '0.5';
                    prettyContainer.style.pointerEvents = 'none';
                    prettyContainer.classList.remove('state');
                    
                    const stateDiv = prettyContainer.querySelector('.state');
                    if (stateDiv) {
                        stateDiv.classList.remove('p-is-checked');
                    }
                }
                console.log(`❌ Source dil ${checkbox.value} disable edildi`);
            }
        });
        
        console.log('🎯 Initial selection tamamlandı - artık manuel toggle çalışabilir');
    }

    /**
     * Clear all language selections when modal opens (Manuel seçim için)
     */
    clearAllLanguageSelections() {
        console.log('🚨 MANUEL SEÇİM: Tüm dil seçimlerini temizle - kullanıcı manuel seçecek');
        
        const checkboxes = document.querySelectorAll('input[name="targetLanguages"]');
        checkboxes.forEach(checkbox => {
            if (checkbox.checked && !checkbox.disabled) {
                checkbox.checked = false;
                
                // 🚨 KRİTİK: Pretty checkbox görselini de sıfırla
                const prettyContainer = checkbox.closest('.pretty');
                if (prettyContainer) {
                    prettyContainer.classList.remove('state');
                    
                    // Change event tetikle ki pretty checkbox state'i güncellensin
                    const changeEvent = new Event('change', { bubbles: true });
                    checkbox.dispatchEvent(changeEvent);
                }
            }
        });
        
        console.log('Dil seçimleri temizlendi - kullanıcı manuel seçim yapabilir');
    }

    /**
     * Cancel translation operation
     */
    cancelTranslation() {
        // You can implement cancellation logic here
        this.showAlert('Çeviri işlemi iptal edildi.', 'info');
        this.close();
    }

    /**
     * Update progress with statistics
     */
    updateProgressStatistics(data) {
        const processedElement = document.getElementById('processedCount');
        const totalElement = document.getElementById('totalCount');
        const successElement = document.getElementById('successCount');
        const errorElement = document.getElementById('errorCount');
        
        if (processedElement) processedElement.textContent = data.processed || 0;
        if (totalElement) totalElement.textContent = data.total || 0;
        if (successElement) successElement.textContent = data.success || 0;
        if (errorElement) errorElement.textContent = data.errors || 0;
    }

    /**
     * Get advanced options from form
     */
    getAdvancedOptions() {
        return {
            preserve_formatting: document.getElementById('preserveFormatting')?.checked || false,
            overwrite_existing: document.getElementById('overwriteExisting')?.checked || false,
            translate_seo: document.getElementById('translateSEO')?.checked || false,
            batch_size: document.getElementById('bulkBatchSize')?.value || 5,
            parallel_processing: document.getElementById('parallelProcessing')?.checked || false
        };
    }

    /**
     * 🔥 YENİ KAYNAK DİL DEĞİŞİM HANDLERİ
     */
    handleSourceLanguageChange(sourceLanguage) {
        console.log('🌍 Kaynak dil değişikliği işleniyor:', sourceLanguage);
        
        if (!sourceLanguage) {
            // Kaynak dil seçilmemişse tüm hedef dilleri etkinleştir
            this.enableAllTargetLanguages();
            return;
        }
        
        // Hedef dillerde kaynak dili disable et, diğerlerini etkinleştir
        this.updateTargetLanguagesBasedOnSource(sourceLanguage);
    }
    
    /**
     * Kaynak dile göre hedef dilleri güncelle
     */
    updateTargetLanguagesBasedOnSource(sourceLanguage) {
        const targetCheckboxes = document.querySelectorAll('input[name="targetLanguages"]');
        
        targetCheckboxes.forEach(checkbox => {
            const isSourceLanguage = checkbox.value === sourceLanguage;
            const prettyContainer = checkbox.closest('.pretty');
            
            if (isSourceLanguage) {
                // Kaynak dil: Disable ve uncheck
                checkbox.disabled = true;
                checkbox.checked = false;
                
                if (prettyContainer) {
                    prettyContainer.style.opacity = '0.3';
                    prettyContainer.style.pointerEvents = 'none';
                    prettyContainer.classList.remove('state');
                    
                    // Disable durumu için class ekle
                    prettyContainer.classList.add('disabled-language');
                    
                    const stateDiv = prettyContainer.querySelector('.state');
                    if (stateDiv) {
                        stateDiv.classList.remove('p-is-checked');
                    }
                }
                
                console.log(`❌ Source language ${checkbox.value} disabled`);
            } else {
                // Diğer diller: Enable
                checkbox.disabled = false;
                
                if (prettyContainer) {
                    prettyContainer.style.opacity = '';
                    prettyContainer.style.pointerEvents = '';
                    prettyContainer.classList.remove('disabled-language');
                }
                
                console.log(`✅ Target language ${checkbox.value} enabled`);
            }
        });
    }
    
    /**
     * Tüm hedef dilleri etkinleştir
     */
    enableAllTargetLanguages() {
        const targetCheckboxes = document.querySelectorAll('input[name="targetLanguages"]');
        
        targetCheckboxes.forEach(checkbox => {
            checkbox.disabled = false;
            const prettyContainer = checkbox.closest('.pretty');
            
            if (prettyContainer) {
                prettyContainer.style.opacity = '';
                prettyContainer.style.pointerEvents = '';
                prettyContainer.classList.remove('disabled-language');
            }
        });
        
        console.log('🌐 Tüm hedef diller etkinleştirildi');
    }
    
    /**
     * 🔥 YENİ CLEAN CHECKBOX SİSTEMİ KURULUMU
     */
    setupCleanCheckboxSystem() {
        const targetContainer = document.getElementById('targetLanguages');
        if (!targetContainer) return;
        
        console.log('🔧 Clean checkbox sistemi kuruluyor...');
        
        // Mevcut event listener'ları temizle
        const prettyContainers = targetContainer.querySelectorAll('.pretty');
        prettyContainers.forEach(container => {
            const newContainer = container.cloneNode(true);
            container.parentNode.replaceChild(newContainer, container);
        });
        
        // Yeni event system kur
        const self = this;
        targetContainer.addEventListener('click', function(e) {
            let targetInput = null;
            
            // Input'u bul
            if (e.target.tagName === 'INPUT' && e.target.name === 'targetLanguages') {
                targetInput = e.target;
            } else {
                const prettyContainer = e.target.closest('.pretty');
                if (prettyContainer) {
                    targetInput = prettyContainer.querySelector('input[name="targetLanguages"]');
                }
            }
            
            // Eğer valid input bulunduysa ve disabled değilse
            if (targetInput && !targetInput.disabled) {
                console.log(`🎯 Manual checkbox toggle: ${targetInput.value}`);
                
                // Tüm pretty checkbox event'lerini engelle
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                // State değiştir
                const newState = !targetInput.checked;
                targetInput.checked = newState;
                
                // Pretty checkbox görselini güncelle
                self.updatePrettyCheckboxVisual(targetInput, newState);
                
                // Token estimation güncelle
                self.debounce(() => self.updateTokenEstimation(), 300);
                
                console.log(`✅ Checkbox ${targetInput.value} = ${newState ? 'SEÇİLDİ' : 'KALDIRILDI'}`);
                
                return false;
            }
        }, { capture: true });
        
        console.log('✅ Clean checkbox sistemi kuruldu');
    }
    
    /**
     * Pretty checkbox görselini manuel güncelle
     */
    updatePrettyCheckboxVisual(checkbox, isChecked) {
        const prettyContainer = checkbox.closest('.pretty');
        if (!prettyContainer) return;
        
        console.log(`🎨 Pretty checkbox görsel güncelleniyor: ${checkbox.value} = ${isChecked ? 'CHECKED' : 'UNCHECKED'}`);
        
        // Pretty checkbox'ın doğru class'larını kullan
        if (isChecked) {
            // Seçili hale getir - pretty checkbox'ın kendi state sistemini kullan
            prettyContainer.classList.add('state');
            checkbox.checked = true;
            
            // Pretty checkbox'ın internal state'ini de güncelle
            const changeEvent = new Event('change', { bubbles: true });
            checkbox.dispatchEvent(changeEvent);
        } else {
            // Seçimi kaldır
            prettyContainer.classList.remove('state');
            checkbox.checked = false;
            
            // Pretty checkbox'ın internal state'ini de güncelle  
            const changeEvent = new Event('change', { bubbles: true });
            checkbox.dispatchEvent(changeEvent);
        }
        
        console.log(`✅ Pretty checkbox görsel güncelleme tamamlandı: ${checkbox.value}`);
    }
    
    /**
     * Loading overlay'i gelişmiş detaylarla göster
     */
    showAdvancedLoadingOverlay(initialMessage = '🚀 Çeviri sistemi başlatılıyor...') {
        const overlay = document.getElementById('translationLoadingOverlay');
        const titleElement = document.getElementById('aiProgressTitle');
        const subtitleElement = document.getElementById('aiProgressSubtitle');
        const progressBar = document.querySelector('.translation-progress-bar');
        const languageStatusList = document.getElementById('languageStatusList');
        
        if (overlay) {
            overlay.style.display = 'flex';
        }
        
        // İlk mesajı göster
        if (titleElement) {
            titleElement.textContent = 'Yapay Zeka Çeviri Hazırlığı';
        }
        
        if (subtitleElement) {
            subtitleElement.textContent = initialMessage;
        }
        
        // İlk progress
        if (progressBar) {
            progressBar.style.width = '5%';
            progressBar.textContent = '5%';
        }
        
        // Dil durumları listesini hazırla
        if (languageStatusList) {
            const selectedLanguages = this.getSelectedTargetLanguages();
            this.initializeLanguageStatusList(selectedLanguages);
        }
        
        console.log('📺 Gelişmiş loading overlay başlatıldı');
    }
    
    /**
     * Dil durumları listesini başlat
     */
    initializeLanguageStatusList(targetLanguages) {
        const statusList = document.getElementById('languageStatusList');
        if (!statusList) return;
        
        let html = '<div class="language-status-container">';
        
        targetLanguages.forEach(langCode => {
            const langName = this.getLanguageNameByCode(langCode);
            html += `
                <div class="language-status-item" data-lang="${langCode}">
                    <div class="d-flex align-items-center">
                        <div class="status-indicator me-2" id="status-${langCode}">
                            <i class="fas fa-clock text-muted"></i>
                        </div>
                        <span class="language-name">${langName} (${langCode.toUpperCase()})</span>
                        <div class="ms-auto">
                            <span class="status-text" id="status-text-${langCode}">Bekliyor...</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        statusList.innerHTML = html;
        
        console.log('📋 Dil durumları listesi oluşturuldu:', targetLanguages.length);
    }
    
    /**
     * Dil kodundan dil adını al
     */
    getLanguageNameByCode(code) {
        const languageNames = {
            'tr': 'Türkçe',
            'en': 'İngilizce', 
            'ar': 'Arapça',
            'da': 'Danca',
            'bn': 'Bengalce',
            'sq': 'Arnavutça',
            'fr': 'Fransızca',
            'de': 'Almanca',
            'es': 'İspanyolca',
            'it': 'İtalyanca',
            'pt': 'Portekizce',
            'ru': 'Rusça',
            'zh': 'Çince',
            'ja': 'Japonca',
            'ko': 'Korece'
        };
        
        return languageNames[code] || code.toUpperCase();
    }
    
    /**
     * Dil durumunu güncelle
     */
    updateLanguageStatus(langCode, status, message = '') {
        const statusIndicator = document.getElementById(`status-${langCode}`);
        const statusText = document.getElementById(`status-text-${langCode}`);
        
        if (statusIndicator && statusText) {
            let icon, color, text;
            
            switch (status) {
                case 'processing':
                    icon = 'fas fa-spinner fa-spin';
                    color = 'text-primary';
                    text = 'İşleniyor...';
                    break;
                case 'completed':
                    icon = 'fas fa-check-circle';
                    color = 'text-success';
                    text = 'Tamamlandı';
                    break;
                case 'failed':
                    icon = 'fas fa-times-circle';
                    color = 'text-danger';
                    text = 'Hata';
                    break;
                case 'waiting':
                default:
                    icon = 'fas fa-clock';
                    color = 'text-muted';
                    text = 'Bekliyor...';
                    break;
            }
            
            statusIndicator.innerHTML = `<i class="${icon} ${color}"></i>`;
            statusText.textContent = message || text;
            
            console.log(`🏷️ Dil durumu güncellendi: ${langCode} = ${status}`);
        }
    }

    /**
     * Debounce function to limit API calls
     */
    debounce(func, wait) {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(func, wait);
    }
}

// Create global instance
window.AITranslation = new AITranslationSystem();

// Global helper functions for easy access
window.openTranslationModal = function(module, itemId, mode = 'single', selectedItems = []) {
    if (window.AITranslation) {
        window.AITranslation.open(module, itemId, mode, selectedItems);
    }
};

window.startAITranslation = function(module, itemId, mode = 'single', selectedItems = []) {
    if (window.AITranslation) {
        window.AITranslation.open(module, itemId, mode, selectedItems);
    }
};

window.initAITranslation = function(options = {}) {
    if (window.AITranslation) {
        window.AITranslation.init(options);
    }
};

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (window.AITranslation) {
        window.AITranslation.init();
    }
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AITranslationSystem;
}