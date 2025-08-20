console.log('🔧 Simple Translation Modal loading...');

// Translation modal açma fonksiyonu
function openTranslationModal(entityType, entityId) {
    console.log('🔧 DEBUGGING: Opening translation modal for:', entityType, entityId);
    
    // DEBUG: Tüm modal'ları bul
    const allModals = document.querySelectorAll('[id*="modal"], [class*="modal"]');
    console.log('🔍 DEBUG: Found all modals:', Array.from(allModals).map(m => ({ id: m.id, classes: m.className })));
    
    // Modal'ı aç
    const modal = document.getElementById('aiTranslationModal');
    console.log('🔍 DEBUG: Modal element:', modal);
    console.log('🔍 DEBUG: Modal exists:', !!modal);
    
    if (modal) {
        console.log('🔍 DEBUG: Modal current style:', modal.style.cssText);
        console.log('🔍 DEBUG: Modal current classes:', modal.className);
        console.log('🔍 DEBUG: Modal current display:', getComputedStyle(modal).display);
        console.log('🔍 DEBUG: Modal visibility:', getComputedStyle(modal).visibility);
        console.log('🔍 DEBUG: Modal opacity:', getComputedStyle(modal).opacity);
        
        // Entity type ve ID'yi sakla
        modal.setAttribute('data-entity-type', entityType);
        modal.setAttribute('data-entity-id', entityId);
        
        // DEBUG: Library kontrolleri
        console.log('🔍 DEBUG: jQuery available:', typeof $ !== 'undefined');
        console.log('🔍 DEBUG: Bootstrap available:', typeof bootstrap !== 'undefined');
        console.log('🔍 DEBUG: Window.bootstrap:', window.bootstrap);
        
        // Bootstrap ile aç - BACKDROP STATIC (Siyah alana tıklayınca kapanmasın)
        console.log('📦 DEBUG: jQuery modal çalışmıyor, direkt manual modal açıyorum');
        manualModalOpen(modal);
        
        // Modal açıldıktan sonra dilleri yükle
        setTimeout(() => {
            loadAvailableLanguages();
        }, 100);
    } else {
        console.error('❌ DEBUG: Translation modal not found!');
        console.error('❌ DEBUG: Document body:', document.body);
        console.error('❌ DEBUG: All elements with "modal":', document.querySelectorAll('*[id*="modal"], *[class*="modal"]'));
    }
}

// Manuel modal açma fonksiyonu - DEBUG
function manualModalOpen(modal) {
    console.log('📦 DEBUG: Manual modal opening initiated');
    console.log('🔍 DEBUG: Modal before manual open:', modal);
    
    // Body'den modal-open class'ını kaldır
    document.body.classList.remove('modal-open');
    
    // Eski backdrop'ları temizle
    const oldBackdrops = document.querySelectorAll('.modal-backdrop');
    oldBackdrops.forEach(backdrop => backdrop.remove());
    
    // Backdrop ekle
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    backdrop.id = 'aiTranslationModalBackdrop';
    backdrop.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1040;';
    document.body.appendChild(backdrop);
    console.log('✅ DEBUG: Backdrop added');
    
    // Modal'ı göster
    modal.style.display = 'block';
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.zIndex = '1050';
    modal.classList.add('show');
    modal.setAttribute('aria-hidden', 'false');
    modal.setAttribute('aria-modal', 'true');
    
    // Body'ye modal açık class ekle
    document.body.classList.add('modal-open');
    document.body.style.overflow = 'hidden';
    
    console.log('✅ DEBUG: Manual modal opened');
    console.log('🔍 DEBUG: After manual open - Modal display:', modal.style.display);
    console.log('🔍 DEBUG: After manual open - Modal classes:', modal.className);
    console.log('🔍 DEBUG: After manual open - Body classes:', document.body.className);
}

// Dilleri yükleme fonksiyonu
function loadAvailableLanguages() {
    console.log('🌍 Loading available languages...');
    
    // AJAX ile tenant'ın aktif dillerini al
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    };
    
    // CSRF token varsa ekle
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }
    
    fetch('/admin/api/tenant-languages', {
        method: 'GET',
        headers: headers,
        credentials: 'same-origin'
    })
        .then(response => response.json())
        .then(data => {
            console.log('📦 API Response:', data);
            if (data.success && data.languages) {
                populateLanguageSelectors(data.languages);
            } else {
                console.error('❌ Failed to load tenant languages:', data.message || 'No languages data');
                // Fallback diller
                const fallbackLanguages = [
                    { code: 'tr', name: 'Türkçe', native_name: 'Türkçe', flag: '🇹🇷' },
                    { code: 'en', name: 'English', native_name: 'English', flag: '🇬🇧' }
                ];
                populateLanguageSelectors(fallbackLanguages);
            }
        })
        .catch(error => {
            console.error('❌ Network error loading tenant languages:', error);
            // Fallback diller
            const fallbackLanguages = [
                { code: 'tr', name: 'Türkçe', native_name: 'Türkçe', flag: '🇹🇷' },
                { code: 'en', name: 'English', native_name: 'English', flag: '🇬🇧' }
            ];
            populateLanguageSelectors(fallbackLanguages);
        });
}

// Dil selector'larını doldurma fonksiyonu
function populateLanguageSelectors(languages) {
    console.log('📝 Populating language selectors with:', languages);

    // Kaynak dil dropdown'unu doldur
    const sourceSelect = document.getElementById('sourceLanguage');
    if (sourceSelect) {
        sourceSelect.innerHTML = '<option value="">Kaynak dil seçiniz...</option>';
        languages.forEach(lang => {
            sourceSelect.innerHTML += `<option value="${lang.code}">${lang.flag} ${lang.name}</option>`;
        });
        // Varsayılan olarak TR seç
        sourceSelect.value = 'tr';
        
        // Kaynak dil değişikliği event listener'ı ekle
        sourceSelect.addEventListener('change', handleSourceLanguageChange);
    }

    // Hedef diller listesini doldur
    const targetContainer = document.getElementById('targetLanguagesContainer');
    if (targetContainer) {
        targetContainer.innerHTML = '';
        languages.forEach(lang => {
            const div = document.createElement('div');
            div.className = 'col-6';
            div.setAttribute('data-lang-code', lang.code);
            div.innerHTML = `
                <div class="pretty p-default p-curve p-thick p-smooth">
                    <input class="target-lang-checkbox" type="checkbox" name="targetLanguages[]" value="${lang.code}" id="target_${lang.code}" ${lang.code !== 'tr' ? 'checked' : ''}>
                    <div class="state p-primary-o">
                        <label style="margin-left: 8px;">${lang.flag}<span style="margin-left: 6px;">${lang.name}</span></label>
                    </div>
                </div>
            `;
            targetContainer.appendChild(div);
        });
    }

    // İlk yükleme sonrası source language'e göre target'ları güncelle
    handleSourceLanguageChange();

    // Hedef dil checkbox'ları için change event listener
    const targetCheckboxes = document.querySelectorAll('.target-lang-checkbox');
    targetCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateStartButtonState);
    });

    // Butonu aktif et
    const startBtn = document.getElementById('startTranslation');
    if (startBtn) {
        startBtn.onclick = startTranslation;
        updateStartButtonState(); // İlk durumu kontrol et
    }
    
    console.log('✅ Languages loaded successfully');
}

// Kaynak dil değişikliği handler'ı
function handleSourceLanguageChange() {
    const sourceSelect = document.getElementById('sourceLanguage');
    const selectedSourceLang = sourceSelect ? sourceSelect.value : '';
    
    console.log('🔄 Source language changed to:', selectedSourceLang);
    
    // Tüm hedef dil container'larını al
    const targetContainer = document.getElementById('targetLanguagesContainer');
    if (!targetContainer) return;
    
    const languageContainers = targetContainer.querySelectorAll('[data-lang-code]');
    
    languageContainers.forEach(container => {
        const langCode = container.getAttribute('data-lang-code');
        const checkbox = container.querySelector('input[type="checkbox"]');
        const prettyDiv = container.querySelector('.pretty');
        
        if (langCode === selectedSourceLang && selectedSourceLang !== '') {
            // Kaynak dille aynı olan dili disable et
            if (prettyDiv) {
                prettyDiv.style.opacity = '0.3';
                prettyDiv.style.pointerEvents = 'none';
            }
            if (checkbox) {
                checkbox.disabled = true;
                checkbox.checked = false;
            }
        } else {
            // Diğer dilleri enable et
            if (prettyDiv) {
                prettyDiv.style.opacity = '1';
                prettyDiv.style.pointerEvents = 'auto';
            }
            if (checkbox) {
                checkbox.disabled = false;
                if (langCode !== 'tr' || selectedSourceLang !== 'tr') {
                    // TR değilse veya kaynak TR değilse default olarak check et
                    checkbox.checked = true;
                }
            }
        }
    });
    
    // Start button durumunu güncelle
    updateStartButtonState();
}

// Start button durumunu güncelle
function updateStartButtonState() {
    const sourceSelect = document.getElementById('sourceLanguage');
    const sourceLanguage = sourceSelect ? sourceSelect.value : '';
    
    const checkedTargets = document.querySelectorAll('.target-lang-checkbox:checked:not(:disabled)');
    const hasTargets = checkedTargets.length > 0;
    
    const startBtn = document.getElementById('startTranslation');
    if (startBtn) {
        startBtn.disabled = !sourceLanguage || !hasTargets;
        
        if (sourceLanguage && hasTargets) {
            startBtn.classList.remove('btn-secondary');
            startBtn.classList.add('btn-primary');
        } else {
            startBtn.classList.remove('btn-primary');
            startBtn.classList.add('btn-secondary');
        }
    }
    
    console.log('🎯 Button state updated:', { sourceLanguage, targetCount: checkedTargets.length, enabled: sourceLanguage && hasTargets });
}

// Translation başlatma fonksiyonu - ÇOK ÖNEMLİ DÖNGÜ ÖNLEMİ
function startTranslation() {
    console.log('🚀 Starting translation process...');
    
    const modal = document.getElementById('aiTranslationModal');
    if (!modal) {
        console.error('❌ Modal not found!');
        return;
    }

    // ÇOK ÖNEMLİ: Butonu hemen disable et (döngü önlemi)
    const startBtn = document.getElementById('startTranslation');
    if (startBtn) {
        if (startBtn.disabled) {
            console.log('⚠️ Button already disabled - translation in progress, ignoring click');
            return; // Zaten çeviri devam ediyor, çık
        }
        
        startBtn.disabled = true;
        startBtn.classList.add('disabled');
        startBtn.style.pointerEvents = 'none';
        startBtn.innerHTML = '<span>Çeviriliyor...</span> <span class="spinner-border spinner-border-sm ms-1" role="status"></span>';
        console.log('🔒 Translation button disabled to prevent loops');
    }

    // MODAL'I KEYBOARD VE BACKDROP'A KAPATMAYA KARŞI KORU
    modal.setAttribute('data-bs-keyboard', 'false');
    modal.setAttribute('data-bs-backdrop', 'static');
    console.log('🛡️ Modal protected from closing during translation');
    
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
        // Hata durumunda butonu yeniden aktif et
        resetTranslationButton();
        return;
    }
    
    // Full screen gradient overlay göster
    showFullScreenOverlay();
    
    // QUEUE SİSTEMİ - Çeviri job'ını başlat
    startQueueTranslation(entityType, entityId, sourceLanguage, targetLanguages);
}

// Buton reset fonksiyonu
function resetTranslationButton() {
    const startBtn = document.getElementById('startTranslation');
    if (startBtn) {
        startBtn.disabled = false;
        startBtn.classList.remove('disabled');
        startBtn.style.pointerEvents = 'auto';
        startBtn.innerHTML = '<span id="buttonText">Çevir</span>';
        console.log('🔓 Translation button reset');
    }
}

// QUEUE TRANSLATION - Yeni sistem
function startQueueTranslation(entityType, entityId, sourceLanguage, targetLanguages) {
    console.log('🚀 Queue Translation başlatılıyor...', {
        entityType, entityId, sourceLanguage, targetLanguages
    });
    
    // Progress başlat
    updateProgress('🚀 Yapay zeka sistemi devreye giriyor...', 10);
    
    // Livewire component'i bul ve queue'ya job gönder
    findAndCallQueueTranslation(entityId, sourceLanguage, targetLanguages)
        .then((sessionId) => {
            console.log('✅ Queue job başlatıldı, session ID:', sessionId);
            updateProgress('💪 Güçlü AI motorları çalışmaya başladı...', 20);
            
            // WebSocket veya polling ile progress takibi başlat
            startProgressTracking(sessionId);
        })
        .catch(error => {
            console.error('❌ Queue job başlatılamadı:', error);
            updateProgress('🔥 Sistemde geçici bir problem var, tekrar deneyin!', 0);
            // Hata durumunda butonu reset et
            setTimeout(() => {
                resetTranslationButton();
                hideFullScreenOverlay();
            }, 3000);
        });
}

// Queue translation job başlatma
function findAndCallQueueTranslation(entityId, sourceLanguage, targetLanguages) {
    return new Promise((resolve, reject) => {
        console.log('🔍 Finding Livewire component for queue translation...');
        
        // Modal'dan entity type'ı al
        const modal = document.getElementById('aiTranslationModal');
        const entityType = modal ? modal.getAttribute('data-entity-type') : 'page';
        
        // Entity type'a göre component ismi belirle
        const expectedComponentNames = [
            `${entityType}-component`,
            `${entityType}Component`,
            `${entityType}ManageComponent`,
            `${entityType}-manage-component`
        ];
        
        // Livewire component bul
        let targetComponent = null;
        const wireElements = document.querySelectorAll('[wire\\:id]');
        
        for (let element of wireElements) {
            const wireId = element.getAttribute('wire:id');
            try {
                const component = Livewire.find(wireId);
                if (component && component.__instance) {
                    const componentName = component.__instance.fingerprint?.name || component.__instance.name || 'unknown';
                    
                    if (componentName && expectedComponentNames.some(name => componentName === name || componentName.includes(name))) {
                        targetComponent = component;
                        console.log('🎯 Found target component for queue!', componentName);
                        break;
                    }
                }
            } catch (error) {
                console.log('❌ Error accessing component:', wireId, error);
            }
        }
        
        if (targetComponent) {
            try {
                // Session ID event listener ekle  
                let listenerRemoved = false;
                const sessionListener = (data) => {
                    console.log('📨 Received queued event:', data);
                    
                    // Listener'ı sadece bir kez çalıştır
                    if (listenerRemoved) return;
                    listenerRemoved = true;
                    
                    // Livewire event data'sı array şeklinde gelir, ilk element'i al
                    const eventData = Array.isArray(data) ? data[0] : data;
                    
                    if (eventData && eventData.sessionId) {
                        console.log('✅ SessionId received:', eventData.sessionId);
                        resolve(eventData.sessionId);
                    } else {
                        console.error('❌ No sessionId in event data:', data);
                        console.error('❌ Parsed eventData:', eventData);
                        reject(new Error('No sessionId received'));
                    }
                };
                
                // Livewire event listener ekle
                Livewire.on('translationQueued', sessionListener);
                
                // Queue translation çağır
                targetComponent.call('translateFromModal', entityId, sourceLanguage, targetLanguages);
                console.log('📞 Queue translation call sent...');
                
                // Timeout ekle - AI çeviri için daha uzun süre
                setTimeout(() => {
                    if (!listenerRemoved) {
                        listenerRemoved = true;
                        console.log('⚠️ Translation timeout - checking if translation completed anyway...');
                        
                        // Sayfayı yenile ve başarı kontrolü yap
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                        
                        reject(new Error('Translation timeout - page will refresh to check results'));
                    }
                }, 60000); // 60 saniye timeout
                
            } catch (error) {
                console.error('❌ Error calling queue translation:', error);
                reject(error);
            }
        } else {
            reject(new Error('Component not found for queue translation'));
        }
    });
}

// REAL-TIME PROGRESS TRACKING - API POLLING SİSTEMİ 
function startProgressTracking(sessionId) {
    console.log('📡 NURU: Real-time progress tracking başlatıldı - session:', sessionId);
    
    let progress = 20;
    let isCompleted = false;
    let pollCount = 0;
    
    // API'den gerçek progress alacağız
    const pollInterval = setInterval(() => {
        pollCount++;
        console.log(`🔍 NURU: Polling attempt ${pollCount} for session: ${sessionId}`);
        
        // Şimdilik Livewire event'ini bekle, API sonra ekleriz
        if (progress < 75) {
            progress = Math.min(75, progress + Math.random() * 3 + 1); // Yavaş artış
            updateProgress(`🔥 Elite AI sistemi çalışıyor... (${Math.floor(progress)}%)`, Math.floor(progress));
        } else {
            updateProgress(`🔥 Elite AI sistemi %${Math.floor(progress)} - completion bekleniyor...`, Math.floor(progress));
        }
        
        // 100 polling'den sonra timeout (100 x 4 = 6.5 dakika)
        if (pollCount >= 100 && !isCompleted) {
            console.log('⏰ NURU: 6.5 dakika timeout, final check yapıyorum');
            clearInterval(pollInterval);
            finalCompletionCheck(sessionId);
        }
        
    }, 4000); // 4 saniye interval
    
    // Global completion event listener
    window.translationProgressInterval = pollInterval;
    window.translationSessionId = sessionId;
    
    // Ana timeout - 15 dakika
    setTimeout(() => {
        if (!isCompleted) {
            console.log('⏰ NURU: 15 dakika ana timeout');
            clearInterval(pollInterval);
            finalCompletionCheck(sessionId);
        }
    }, 900000); // 15 dakika
}

// Son completion kontrolü
function finalCompletionCheck(sessionId) {
    console.log('🔍 NURU: Final completion check başlatıldı');
    
    updateProgress('⏳ Çeviri tamamlanma durumu kontrol ediliyor...', 90);
    
    // 5 saniye bekle sonra sayfa yenile
    setTimeout(() => {
        console.log('⚠️ NURU: Final timeout, sayfa yenileniyor');
        updateProgress('⚠️ Çeviri arka planda devam ediyor. Sayfa yenileniyor...', 95);
        
        setTimeout(() => {
            closeTranslationModal();
            window.location.reload();
        }, 3000);
    }, 5000);
}

// Progress güncelleme fonksiyonu
function updateProgress(message, percentage) {
    console.log(`📊 Progress: ${percentage}% - ${message}`);
    
    // Progress area'yı göster
    const progressArea = document.getElementById('translationProgress');
    const progressBar = document.getElementById('progressBar');
    const progressMessage = document.getElementById('progressMessage');
    
    if (progressArea) {
        progressArea.style.display = 'block';
    }
    
    if (progressBar) {
        progressBar.style.width = percentage + '%';
        progressBar.setAttribute('aria-valuenow', percentage);
    }
    
    if (progressMessage) {
        progressMessage.textContent = message;
    }
    
    // Overlay progress'i de güncelle
    updateOverlayProgress(message, percentage);
    
    // Buton durumunu güncelle
    updateButtonState(percentage);
    
    // Console'da da göster - MODAL KAPATMAYI SADECE LİVEWİRE EVENT'LERİNDE YAP
    if (percentage >= 100) {
        console.log('🎉 NURU: Progress %100 but modal close only via Livewire events!');
        // MODAL KAPATMA KALDIRILDI - Sadece Livewire event'lerinde kapanacak
    } else if (percentage === 0 && message.includes('hata')) {
        console.log('❌ Translation process failed or reset');
        // Hata durumunda progress'i gizle
        if (progressArea) {
            progressArea.style.display = 'none';
        }
        updateButtonState(-1); // Reset button
    } else {
        console.log(`⏳ Translation in progress: ${percentage}%`);
    }
}

// Buton durumunu güncelle
function updateButtonState(percentage) {
    const startButton = document.getElementById('startTranslation');
    const buttonText = document.getElementById('buttonText');
    const buttonSpinner = document.getElementById('buttonSpinner');
    const cancelButton = document.getElementById('cancelButton');
    
    if (percentage > 0 && percentage < 100) {
        // Loading durumu
        if (startButton) startButton.disabled = true;
        if (buttonText) buttonText.textContent = 'Çevriliyor...';
        if (buttonSpinner) buttonSpinner.style.display = 'inline-block';
        if (cancelButton) cancelButton.disabled = true;
    } else if (percentage >= 100) {
        // Tamamlandı durumu
        if (buttonText) buttonText.textContent = 'Tamamlandı';
        if (buttonSpinner) buttonSpinner.style.display = 'none';
    } else {
        // Normal durum (başlangıç veya hata)
        if (startButton) {
            startButton.disabled = false;
            startButton.classList.remove('disabled');
            startButton.style.pointerEvents = 'auto';
        }
        if (buttonText) buttonText.textContent = 'Çevir';
        if (buttonSpinner) buttonSpinner.style.display = 'none';
        if (cancelButton) cancelButton.disabled = false;
    }
}

// Modal-content overlay gösterme fonksiyonu
function showFullScreenOverlay() {
    console.log('🎨 Showing modal-content AI overlay...');
    
    // Mevcut overlay varsa kaldır
    const existingOverlay = document.getElementById('aiTranslationOverlay');
    if (existingOverlay) {
        existingOverlay.remove();
    }
    
    // Modal-content'i bul
    const modal = document.getElementById('aiTranslationModal');
    const modalContent = modal ? modal.querySelector('.modal-content') : null;
    
    if (!modalContent) {
        console.error('❌ Modal content not found for overlay');
        return;
    }
    
    // Modal-content'e relative position ekle
    modalContent.style.position = 'relative';
    
    // Yeni overlay oluştur
    const overlay = document.createElement('div');
    overlay.id = 'aiTranslationOverlay';
    
    overlay.style.cssText = `
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, 
            #667eea 0%, 
            #764ba2 20%, 
            #f093fb 40%, 
            #f5576c 60%, 
            #4facfe 80%,
            #00d4ff 100%);
        background-size: 600% 600%;
        animation: gradientSlide 8s ease-in-out infinite;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
        border-radius: 0.25rem;
    `;
    
    // CSS animasyonunu ekle
    if (!document.getElementById('aiTranslationStyles')) {
        const style = document.createElement('style');
        style.id = 'aiTranslationStyles';
        style.textContent = `
            @keyframes gradientSlide {
                0% { 
                    background-position: 0% 50%; 
                    transform: scale(1);
                }
                25% { 
                    background-position: 100% 25%; 
                    transform: scale(1.02);
                }
                50% { 
                    background-position: 200% 75%; 
                    transform: scale(1);
                }
                75% { 
                    background-position: 300% 25%; 
                    transform: scale(1.02);
                }
                100% { 
                    background-position: 400% 50%; 
                    transform: scale(1);
                }
            }
            
            @keyframes aiPulse {
                0%, 100% { transform: scale(1); opacity: 0.8; }
                50% { transform: scale(1.1); opacity: 1; }
            }
            
            @keyframes aiSpin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            @keyframes aiFloat {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
            
            .ai-loading-text {
                color: white;
                font-size: 28px;
                font-weight: bold;
                text-align: center;
                margin-bottom: 30px;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                animation: aiFloat 3s ease-in-out infinite;
            }
            
            .ai-loading-subtitle {
                color: rgba(255,255,255,0.9);
                font-size: 18px;
                text-align: center;
                margin-bottom: 40px;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            }
            
            .ai-spinner {
                width: 80px;
                height: 80px;
                border: 4px solid rgba(255,255,255,0.3);
                border-top: 4px solid white;
                border-radius: 50%;
                animation: aiSpin 1s linear infinite;
                margin-bottom: 20px;
            }
            
            .ai-progress-bar {
                width: 300px;
                height: 6px;
                background: rgba(255,255,255,0.3);
                border-radius: 3px;
                overflow: hidden;
                margin-bottom: 15px;
            }
            
            .ai-progress-fill {
                height: 100%;
                background: linear-gradient(90deg, #fff, #f0f0f0, #fff);
                background-size: 200% 100%;
                animation: aiProgressShine 2s ease-in-out infinite;
                width: 0%;
                transition: width 0.3s ease;
            }
            
            @keyframes aiProgressShine {
                0% { background-position: -200% 0; }
                100% { background-position: 200% 0; }
            }
            
            .ai-status-text {
                color: rgba(255,255,255,0.8);
                font-size: 14px;
                text-align: center;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            }
        `;
        document.head.appendChild(style);
    }
    
    // Loading içeriği - Sadeleştirilmiş
    overlay.innerHTML = `
        <div class="ai-loading-text">🤖 Yapay Zeka İş Başında</div>
        <div class="ai-loading-subtitle">Lütfen bekleyin.</div>
        <div class="ai-progress-bar">
            <div class="ai-progress-fill" id="aiProgressFill"></div>
        </div>
        <div class="ai-status-text" id="aiStatusText">Çeviri işlemi başlatılıyor...</div>
    `;
    
    // Modal-content'e ekle
    modalContent.appendChild(overlay);
    
    // Overlay'i görünür yap
    setTimeout(() => {
        overlay.style.opacity = '1';
    }, 50);
    
    console.log('✨ AI Translation overlay displayed');
}

// Full screen overlay gizleme fonksiyonu
function hideFullScreenOverlay() {
    console.log('🎨 Hiding full screen AI overlay...');
    
    const overlay = document.getElementById('aiTranslationOverlay');
    if (overlay) {
        overlay.style.opacity = '0';
        setTimeout(() => {
            overlay.remove();
        }, 500);
    }
}

// Overlay progress güncelleme fonksiyonu
function updateOverlayProgress(message, percentage) {
    const progressFill = document.getElementById('aiProgressFill');
    const statusText = document.getElementById('aiStatusText');
    
    if (progressFill) {
        progressFill.style.width = percentage + '%';
    }
    
    if (statusText) {
        statusText.textContent = message;
    }
}

// Modal kapatma fonksiyonu
function closeTranslationModal() {
    console.log('🔒 NURU: Modal kapatılıyor - backdrop temizleniyor');
    
    // BUTONU YENİDEN AKTİF ET
    resetTranslationButton();
    
    // Overlay'i gizle
    hideFullScreenOverlay();
    
    // MANUEL MODAL KAPAT
    const modal = document.getElementById('aiTranslationModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        modal.removeAttribute('aria-modal');
        modal.removeAttribute('data-bs-keyboard');
        modal.removeAttribute('data-bs-backdrop');
        console.log('✅ NURU: Modal element gizlendi');
    }
    
    // BACKDROP TEMİZLE - TÜM BACKDROP'LARI BUL VE SİL
    const backdrops = document.querySelectorAll('.modal-backdrop, #aiTranslationModalBackdrop, #translation-modal-backdrop');
    backdrops.forEach((backdrop, index) => {
        console.log(`🗑️ NURU: Backdrop ${index + 1} siliniyor:`, backdrop.id || backdrop.className);
        backdrop.remove();
    });
    
    // BODY CLASS TEMİZLE
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    console.log('✅ NURU: Body classes temizlendi');
    
    // GLOBAL INTERVALS TEMİZLE
    if (window.translationProgressInterval) {
        clearInterval(window.translationProgressInterval);
        window.translationProgressInterval = null;
        console.log('✅ NURU: Progress interval temizlendi');
    }
    
    console.log('🎉 NURU: Modal tamamen kapatıldı ve temizlendi');
}

// Livewire event listener'ları - ONCE ONLY
// Global unique check to prevent duplicate loading
if (!window.simpleTranslationModalLoaded) {
    window.simpleTranslationModalLoaded = true;

    document.addEventListener('livewire:initialized', () => {
        if (window.simpleTranslationListenersAdded) {
            console.log('⚠️ Livewire listeners already added, skipping...');
            return;
        }
        
        console.log('⚡ Livewire initialized - setting up event listeners');
        window.simpleTranslationListenersAdded = true;
    
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
    
    // Çeviri tamamlandı event'i - Livewire'dan gelir, DOM event'ine çevrilir
    Livewire.on('translation-complete', (data) => {
        console.log('🎉 NURU: Livewire translation-complete event received:', data);
        
        // Global progress interval'ı durdur
        if (window.translationProgressInterval) {
            clearInterval(window.translationProgressInterval);
            console.log('✅ NURU: Progress interval stopped by completion event');
        }
        
        // Progress'i %100'e getir ve modal'ı kapat
        updateProgress('🎉 Çeviri başarıyla tamamlandı! İçerik kaydediliyor...', 100);
        
        // 5 saniye bekle (DB işlemlerinin tamamlanması için)
        setTimeout(() => {
            updateProgress('✅ Tüm içerikler kaydedildi! Sayfa yenileniyor...', 100);
            
            setTimeout(() => {
                closeTranslationModal();
                window.location.reload();
            }, 2000);
        }, 5000);
        
        // DOM event olarak da fırlat (Promise'lerin beklemesi için)
        const customEvent = new CustomEvent('translation-complete', {
            detail: data
        });
        document.dispatchEvent(customEvent);
        
        console.log('🔥 NURU: DOM translation-complete event dispatched ve modal kapatıldı');
    });

    // Çeviri hatası event'i
    Livewire.on('translation-error', (data) => {
        console.log('❌ NURU: Livewire translation-error event received:', data);
        
        updateProgress('❌ Çeviri işleminde hata oluştu. Tekrar deneyin.', 0);
        
        setTimeout(() => {
            hideFullScreenOverlay();
            resetTranslationButton();
        }, 3000);
    });
    });
}

console.log('✅ Simple Translation Modal loaded successfully');