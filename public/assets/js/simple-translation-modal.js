console.log('🔧 Simple Translation Modal loading...');

// Translation modal açma fonksiyonu
function openTranslationModal(entityType, entityId) {
    console.log('🔧 Opening translation modal for:', entityType, entityId);
    
    // Modal'ı aç
    const modal = document.getElementById('aiTranslationModal');
    if (modal) {
        // Entity type ve ID'yi sakla
        modal.setAttribute('data-entity-type', entityType);
        modal.setAttribute('data-entity-id', entityId);
        
        // Bootstrap ile aç
        if (typeof $ !== 'undefined') {
            console.log('📦 Using jQuery to open modal');
            $(modal).modal('show');
        } else if (typeof bootstrap !== 'undefined') {
            console.log('📦 Using Bootstrap to open modal');
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        } else {
            console.log('📦 Using manual modal opening');
            // Manuel modal açma
            modal.style.display = 'block';
            modal.classList.add('show');
            modal.setAttribute('aria-hidden', 'false');
            modal.setAttribute('aria-modal', 'true');
        }
        
        // Modal açıldıktan sonra dilleri yükle
        setTimeout(() => {
            loadAvailableLanguages();
        }, 100);
    } else {
        console.error('❌ Translation modal not found!');
    }
}

// Dilleri yükleme fonksiyonu
function loadAvailableLanguages() {
    console.log('🌍 Loading available languages...');
    
    // Basit dil listesi - Laravel'den gelen diller
    const languages = [
        { code: 'tr', name: 'Türkçe', flag: '🇹🇷' },
        { code: 'en', name: 'English', flag: '🇬🇧' },
        { code: 'ar', name: 'العربية', flag: '🇸🇦' },
        { code: 'da', name: 'Dansk', flag: '🇩🇰' },
        { code: 'sq', name: 'Shqip', flag: '🇦🇱' }
    ];

    // Kaynak dil dropdown'unu doldur
    const sourceSelect = document.getElementById('sourceLanguage');
    if (sourceSelect) {
        sourceSelect.innerHTML = '<option value="">Kaynak dil seçiniz...</option>';
        languages.forEach(lang => {
            sourceSelect.innerHTML += `<option value="${lang.code}">${lang.flag} ${lang.name}</option>`;
        });
        // Varsayılan olarak TR seç
        sourceSelect.value = 'tr';
    }

    // Hedef diller listesini doldur
    const targetContainer = document.getElementById('targetLanguages');
    if (targetContainer) {
        targetContainer.innerHTML = '';
        languages.forEach(lang => {
            if (lang.code !== 'tr') { // TR hariç tüm dilleri ekle
                const div = document.createElement('div');
                div.className = 'col-6 mb-2';
                div.innerHTML = `
                    <div class="form-check">
                        <input class="form-check-input target-lang-checkbox" 
                               type="checkbox" 
                               name="targetLanguages[]"
                               value="${lang.code}" 
                               id="target_${lang.code}" 
                               checked>
                        <label class="form-check-label" for="target_${lang.code}">
                            ${lang.flag} ${lang.name}
                        </label>
                    </div>
                `;
                targetContainer.appendChild(div);
            }
        });
    }

    // Butonu aktif et
    const startBtn = document.getElementById('startTranslationBtn');
    if (startBtn) {
        startBtn.disabled = false;
        startBtn.onclick = startTranslation;
    }
    
    console.log('✅ Languages loaded successfully');
}

// Translation başlatma fonksiyonu
function startTranslation() {
    console.log('🚀 Starting translation process...');
    
    const modal = document.getElementById('aiTranslationModal');
    if (!modal) {
        console.error('❌ Modal not found!');
        return;
    }
    
    const entityType = modal.getAttribute('data-entity-type');
    const entityId = parseInt(modal.getAttribute('data-entity-id'));
    
    console.log('🔍 Entity:', entityType, entityId);
    
    // Source language
    const sourceLanguageSelect = document.getElementById('sourceLanguage');
    const sourceLanguage = sourceLanguageSelect ? sourceLanguageSelect.value : 'tr';
    
    // Target languages
    const targetLanguages = [];
    const targetCheckboxes = document.querySelectorAll('input[name="targetLanguages[]"]:checked');
    targetCheckboxes.forEach(checkbox => {
        targetLanguages.push(checkbox.value);
    });
    
    console.log('🎯 Translation config:', {
        sourceLanguage,
        targetLanguages,
        entityType,
        entityId
    });
    
    // NURULLAH İÇİN: Hangi sayfa çeviriliyor
    console.log(`🔍 NURULLAH: Page ID ${entityId} çeviriliyor (${sourceLanguage} → ${targetLanguages.join(', ')})`);
    
    if (targetLanguages.length === 0) {
        alert('Lütfen en az bir hedef dil seçin!');
        return;
    }
    
    // Progress göster
    updateProgress('Çeviri başlatılıyor...', 0);
    
    // Timeout warnings
    let timeoutWarning30, timeoutWarning60;
    
    // 30 saniye uyarısı
    timeoutWarning30 = setTimeout(() => {
        console.log('⏰ 30 second timeout warning');
        updateProgress('Çeviri devam ediyor... (Bu işlem biraz zaman alabilir)', 50);
    }, 30000);
    
    // 60 saniye uyarısı
    timeoutWarning60 = setTimeout(() => {
        console.log('⏰ 60 second timeout warning');
        updateProgress('Çeviri hâlâ devam ediyor... İçerik büyükse normal süredir.', 70);
    }, 60000);
    
    // Livewire component'i bul ve çeviri başlat
    findAndCallLivewireTranslation(entityId, sourceLanguage, targetLanguages)
        .then(() => {
            console.log('✅ Translation completed successfully');
            console.log(`🔍 NURULLAH: Page ID ${entityId} çevirisi tamamlandı - veritabanını kontrol et!`);
            clearTimeout(timeoutWarning30);
            clearTimeout(timeoutWarning60);
            updateProgress('Çeviri tamamlandı!', 100);
        })
        .catch(error => {
            console.error('❌ Translation failed:', error);
            clearTimeout(timeoutWarning30);
            clearTimeout(timeoutWarning60);
            updateProgress('Çeviri sırasında hata oluştu: ' + error.message, 0);
        });
}

// Livewire component bulma ve çağırma
function findAndCallLivewireTranslation(entityId, sourceLanguage, targetLanguages) {
    return new Promise((resolve, reject) => {
        console.log('🔍 Finding Livewire component via DOM...');
        
        // METHOD 1: DOM'daki wire:id elementlerini bul
        const wireElements = document.querySelectorAll('[wire\\:id]');
        console.log('🔎 Found wire:id elements:', wireElements.length);
        
        let pageComponent = null;
        
        for (let element of wireElements) {
            const wireId = element.getAttribute('wire:id');
            console.log('🔄 Checking element with wire:id:', wireId, element);
            
            // Element'in Livewire component'ini al
            try {
                const component = Livewire.find(wireId);
                if (component && component.__instance) {
                    console.log('✅ Found component:', {
                        wireId: wireId,
                        name: component.__instance.fingerprint?.name,
                        hasTranslateMethod: typeof component.call === 'function'
                    });
                    
                    // PageComponent'ı bul
                    if (component.__instance.fingerprint?.name === 'page-component' ||
                        component.__instance.fingerprint?.name.includes('page')) {
                        pageComponent = component;
                        console.log('🎯 Found PageComponent via DOM!');
                        break;
                    }
                } else {
                    console.log('❌ Component not found for wireId:', wireId);
                }
            } catch (error) {
                console.log('❌ Error accessing component:', wireId, error);
            }
        }
        
        // METHOD 2: Eğer DOM'da bulamazsa, Livewire.all() dene
        if (!pageComponent) {
            console.log('🔄 Fallback to Livewire.all()...');
            const allComponents = Livewire.all();
            
            allComponents.forEach((comp, index) => {
                if (comp && comp.__instance && comp.__instance.fingerprint) {
                    console.log(`Component ${index}:`, {
                        name: comp.__instance.fingerprint.name,
                        hasCall: typeof comp.call === 'function'
                    });
                    
                    if (comp.__instance.fingerprint.name === 'page-component' ||
                        comp.__instance.fingerprint.name.includes('page')) {
                        pageComponent = comp;
                        console.log('🎯 Found PageComponent via Livewire.all()!');
                    }
                }
            });
        }
        
        // METHOD 3: Manuel element arama
        if (!pageComponent) {
            console.log('🔄 Manual element search...');
            const pageElements = document.querySelectorAll('[class*="page"], [id*="page"], [data-component*="page"]');
            console.log('🔎 Found potential page elements:', pageElements.length);
            
            for (let element of pageElements) {
                if (element.hasAttribute('wire:id')) {
                    const wireId = element.getAttribute('wire:id');
                    try {
                        const component = Livewire.find(wireId);
                        if (component && typeof component.call === 'function') {
                            pageComponent = component;
                            console.log('🎯 Found component via manual search!');
                            break;
                        }
                    } catch (error) {
                        console.log('❌ Manual search error:', error);
                    }
                }
            }
        }
        
        if (pageComponent) {
            console.log('✅ Successfully found PageComponent!');
            console.log('📞 Calling translateFromModal with:', {
                entityId,
                sourceLanguage,
                targetLanguages
            });
            
            try {
                // Livewire.call kullan
                pageComponent.call('translateFromModal', entityId, sourceLanguage, targetLanguages);
                resolve();
            } catch (error) {
                console.error('❌ Error calling translateFromModal:', error);
                reject(error);
            }
        } else {
            console.error('❌ PageComponent not found via any method!');
            reject(new Error('PageComponent not found via any method'));
        }
    });
}

// Progress güncelleme fonksiyonu
function updateProgress(message, percentage) {
    console.log(`📊 Progress: ${percentage}% - ${message}`);
    
    const progressBar = document.querySelector('#aiTranslationModal .progress-bar');
    const progressText = document.querySelector('#aiTranslationModal .progress-text');
    
    if (progressBar) {
        progressBar.style.width = percentage + '%';
        progressBar.setAttribute('aria-valuenow', percentage);
    }
    
    if (progressText) {
        progressText.textContent = message;
    }
    
    // Console'da da göster
    if (percentage >= 100) {
        console.log('🎉 Translation process completed!');
        // 2 saniye sonra modal'ı kapat
        setTimeout(() => {
            closeTranslationModal();
        }, 2000);
    } else if (percentage === 0) {
        console.log('❌ Translation process failed or reset');
    } else {
        console.log(`⏳ Translation in progress: ${percentage}%`);
    }
}

// Modal kapatma fonksiyonu
function closeTranslationModal() {
    console.log('🔒 Closing translation modal');
    
    const modal = document.getElementById('aiTranslationModal');
    if (modal) {
        // jQuery ile kapat
        if (typeof $ !== 'undefined') {
            $(modal).modal('hide');
        } else if (typeof bootstrap !== 'undefined') {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        } else {
            // Manuel kapat
            modal.style.display = 'none';
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
            modal.removeAttribute('aria-modal');
            
            // Backdrop'u kaldır
            const backdrop = document.getElementById('translation-modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        }
    }
}

// Livewire event listener'ları
document.addEventListener('livewire:initialized', () => {
    console.log('⚡ Livewire initialized - setting up event listeners');
    
    // Modal kapatma event'i
    Livewire.on('closeTranslationModal', () => {
        console.log('📢 Received closeTranslationModal event');
        closeTranslationModal();
    });
    
    // Component refresh event'i
    Livewire.on('refreshComponent', () => {
        console.log('📢 Received refreshComponent event');
        // Sayfa yenilenmesi otomatik olacak (Livewire'ın kendi mekanizması)
    });
});

console.log('✅ Simple Translation Modal loaded successfully');