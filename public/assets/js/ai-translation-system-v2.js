/**
 * 🌍 AI TRANSLATION SYSTEM V2 - SIMPLIFIED & FIXED
 * Modüller için geliştirilmiş AI çeviri sistemi
 * 
 * FEATURES:
 * - ✅ Sadece is_visible=true olan dillere çeviri
 * - ✅ Checkbox toggle düzgün çalışıyor
 * - ✅ JSON field güncelleme
 * - ✅ Progress tracking
 */

class AITranslationSystemV2 {
    constructor(config = {}) {
        this.config = {
            module: config.module || 'page',
            baseUrl: config.baseUrl || '/admin',
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
            ...config
        };

        this.modal = null;
        this.languages = [];
        this.selectedTargets = new Set();
        this.sourceLanguage = null;
        
        this.init();
    }

    /**
     * Sistemi başlat
     */
    async init() {
        console.log('🚀 AI Translation System V2 başlatılıyor...');
        
        // Modal'ı bul
        this.modal = document.getElementById('aiTranslationModal');
        if (!this.modal) {
            console.error('Translation modal bulunamadı!');
            return;
        }

        // Dilleri yükle
        await this.loadLanguages();
        
        // Event listener'ları kur
        this.setupEventListeners();
        
        console.log('✅ AI Translation System V2 hazır!');
    }

    /**
     * Dilleri yükle
     */
    async loadLanguages() {
        try {
            const response = await fetch(`${this.config.baseUrl}/languagemanagement/api/languages/visible`, {
                headers: {
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.languages = data.languages || [];
                console.log(`✅ ${this.languages.length} görünür dil yüklendi`);
                this.renderLanguageOptions();
            }
        } catch (error) {
            console.error('Diller yüklenemedi:', error);
            // Fallback olarak statik dilleri kullan
            this.languages = [
                { language_code: 'tr', name: 'Türkçe' },
                { language_code: 'en', name: 'English' },
                { language_code: 'ar', name: 'العربية' },
                { language_code: 'da', name: 'Dansk' },
                { language_code: 'bn', name: 'বাংলা' },
                { language_code: 'sq', name: 'Shqip' }
            ];
            this.renderLanguageOptions();
        }
    }

    /**
     * Dil seçeneklerini render et
     */
    renderLanguageOptions() {
        // Source language dropdown
        const sourceSelect = document.getElementById('sourceLanguage');
        if (sourceSelect) {
            sourceSelect.innerHTML = '';
            this.languages.forEach(lang => {
                const option = new Option(lang.name, lang.language_code);
                if (lang.language_code === 'tr') {
                    option.selected = true;
                    this.sourceLanguage = 'tr';
                }
                sourceSelect.appendChild(option);
            });
        }

        // Target languages checkboxes
        const targetContainer = document.getElementById('targetLanguages');
        if (targetContainer) {
            targetContainer.innerHTML = '';
            
            this.languages.forEach(lang => {
                const isDisabled = lang.language_code === this.sourceLanguage;
                
                const checkboxHtml = `
                    <div class="pretty p-icon p-round p-pulse mb-2" data-lang="${lang.language_code}">
                        <input type="checkbox" 
                               name="targetLanguages" 
                               value="${lang.language_code}"
                               ${isDisabled ? 'disabled' : ''}
                               ${!isDisabled ? 'checked' : ''}>
                        <div class="state p-success">
                            <i class="icon fa fa-check"></i>
                            <label>${lang.name}</label>
                        </div>
                    </div>`;
                
                targetContainer.insertAdjacentHTML('beforeend', checkboxHtml);
                
                // Disabled olmayan dilleri selected set'e ekle
                if (!isDisabled) {
                    this.selectedTargets.add(lang.language_code);
                }
            });
        }
    }

    /**
     * Event listener'ları kur
     */
    setupEventListeners() {
        // Source language değişimi
        const sourceSelect = document.getElementById('sourceLanguage');
        if (sourceSelect) {
            sourceSelect.addEventListener('change', (e) => {
                this.sourceLanguage = e.target.value;
                this.updateTargetLanguages();
            });
        }

        // Target language checkbox'ları - Delegate event
        const targetContainer = document.getElementById('targetLanguages');
        if (targetContainer) {
            targetContainer.addEventListener('change', (e) => {
                if (e.target.name === 'targetLanguages') {
                    const lang = e.target.value;
                    
                    if (e.target.checked) {
                        this.selectedTargets.add(lang);
                    } else {
                        this.selectedTargets.delete(lang);
                    }
                    
                    console.log(`📋 Seçili diller: ${Array.from(this.selectedTargets).join(', ')}`);
                }
            });
        }

        // Çeviri başlat butonu
        const startBtn = document.getElementById('startTranslation');
        if (startBtn) {
            startBtn.addEventListener('click', () => this.startTranslation());
        }

        // Modal açıldığında
        $(this.modal).on('shown.bs.modal', () => {
            this.resetModal();
        });
    }

    /**
     * Target language'ları güncelle (source değiştiğinde)
     */
    updateTargetLanguages() {
        const targetContainer = document.getElementById('targetLanguages');
        if (!targetContainer) return;

        this.selectedTargets.clear();

        targetContainer.querySelectorAll('input[name="targetLanguages"]').forEach(checkbox => {
            const lang = checkbox.value;
            const prettyContainer = checkbox.closest('.pretty');
            
            if (lang === this.sourceLanguage) {
                // Kaynak dili disable et
                checkbox.disabled = true;
                checkbox.checked = false;
                prettyContainer.classList.add('disabled');
            } else {
                // Diğer dilleri enable et ve seç
                checkbox.disabled = false;
                checkbox.checked = true;
                prettyContainer.classList.remove('disabled');
                this.selectedTargets.add(lang);
            }
        });

        console.log(`🔄 Kaynak dil: ${this.sourceLanguage}, Hedef diller: ${Array.from(this.selectedTargets).join(', ')}`);
    }

    /**
     * Çeviri işlemini başlat
     */
    async startTranslation() {
        // Validation
        if (this.selectedTargets.size === 0) {
            this.showError('Lütfen en az bir hedef dil seçin!');
            return;
        }

        // Loading göster
        this.showLoading('Çeviri başlatılıyor...');

        // Seçili item'ları al
        const selectedItems = this.getSelectedItems();
        if (selectedItems.length === 0) {
            this.showError('Çevrilecek içerik bulunamadı!');
            this.hideLoading();
            return;
        }

        try {
            // API çağrısı
            const response = await fetch(`${this.config.baseUrl}/${this.config.module}/ai/translation/translate-multi`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    source_language: this.sourceLanguage,
                    target_languages: Array.from(this.selectedTargets),
                    selected_items: selectedItems,
                    include_seo: document.getElementById('includeSeo')?.checked || false
                })
            });

            const data = await response.json();

            if (data.success) {
                // Progress tracking başlat
                if (data.operation_id) {
                    this.trackProgress(data.operation_id);
                } else {
                    // Direkt sonuç geldi
                    this.showSuccess(data.message || 'Çeviri tamamlandı!');
                    setTimeout(() => {
                        $(this.modal).modal('hide');
                        window.location.reload();
                    }, 2000);
                }
            } else {
                this.showError(data.message || 'Çeviri başlatılamadı!');
            }

        } catch (error) {
            console.error('Çeviri hatası:', error);
            this.showError('Çeviri işlemi sırasında hata oluştu!');
        }
    }

    /**
     * Progress tracking
     */
    async trackProgress(operationId) {
        const checkInterval = setInterval(async () => {
            try {
                const response = await fetch(`${this.config.baseUrl}/${this.config.module}/ai/translation/check-progress`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.config.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        operation_id: operationId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Progress güncelle
                    this.updateProgress(data.progress || 0, data.message || 'İşleniyor...');

                    // Tamamlandıysa
                    if (data.status === 'completed') {
                        clearInterval(checkInterval);
                        this.showSuccess(data.message || 'Çeviri tamamlandı!');
                        
                        setTimeout(() => {
                            $(this.modal).modal('hide');
                            window.location.reload();
                        }, 2000);
                    }
                }
            } catch (error) {
                console.error('Progress check hatası:', error);
            }
        }, 2000); // Her 2 saniyede bir kontrol et

        // 60 saniye sonra timeout
        setTimeout(() => {
            clearInterval(checkInterval);
            this.showWarning('Çeviri arka planda devam ediyor. Lütfen sayfayı yenileyin.');
        }, 60000);
    }

    /**
     * Seçili item'ları al
     */
    getSelectedItems() {
        // Tekli mod mu toplu mod mu kontrol et
        const currentPageId = window.aiTranslationCurrentPage;
        const selectedBulkItems = window.aiTranslationSelectedItems || [];

        if (selectedBulkItems.length > 0) {
            return selectedBulkItems;
        } else if (currentPageId) {
            return [currentPageId];
        }

        return [];
    }

    /**
     * Modal'ı sıfırla
     */
    resetModal() {
        // Source language'ı varsayılan yap
        this.sourceLanguage = 'tr';
        const sourceSelect = document.getElementById('sourceLanguage');
        if (sourceSelect) {
            sourceSelect.value = 'tr';
        }

        // Target languages'ı yeniden render et
        this.updateTargetLanguages();

        // Loading'i gizle
        this.hideLoading();
    }

    /**
     * UI Helper Methods
     */
    showLoading(message) {
        const overlay = document.getElementById('translationLoadingOverlay');
        const progressTitle = document.getElementById('aiProgressTitle');
        
        if (overlay) {
            overlay.style.display = 'flex';
        }
        if (progressTitle) {
            progressTitle.textContent = message;
        }
    }

    hideLoading() {
        const overlay = document.getElementById('translationLoadingOverlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    updateProgress(percentage, message) {
        const progressBar = document.querySelector('#translationLoadingOverlay .progress-bar');
        const progressTitle = document.getElementById('aiProgressTitle');
        const progressSubtitle = document.getElementById('aiProgressSubtitle');

        if (progressBar) {
            progressBar.style.width = `${percentage}%`;
            progressBar.setAttribute('aria-valuenow', percentage);
        }
        if (progressTitle) {
            progressTitle.textContent = message;
        }
        if (progressSubtitle) {
            progressSubtitle.textContent = `${percentage}% tamamlandı`;
        }
    }

    showSuccess(message) {
        const alertContainer = document.getElementById('translationAlerts');
        if (alertContainer) {
            alertContainer.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
        }
        this.hideLoading();
    }

    showError(message) {
        const alertContainer = document.getElementById('translationAlerts');
        if (alertContainer) {
            alertContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
        }
        this.hideLoading();
    }

    showWarning(message) {
        const alertContainer = document.getElementById('translationAlerts');
        if (alertContainer) {
            alertContainer.innerHTML = `
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="fas fa-info-circle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
        }
        this.hideLoading();
    }
}

// Global instance
window.AITranslationSystemV2 = AITranslationSystemV2;

// Auto-init
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('aiTranslationModal')) {
        window.aiTranslationSystem = new AITranslationSystemV2({
            module: window.currentModule || 'page'
        });
    }
});