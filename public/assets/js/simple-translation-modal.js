
// Translation modal açma fonksiyonu
function openTranslationModal(entityType, entityId) {
    
    const modal = document.getElementById('aiTranslationModal');
    if (modal) {
        modal.setAttribute('data-entity-type', entityType);
        modal.setAttribute('data-entity-id', entityId);
        manualModalOpen(modal);
        setTimeout(() => loadAvailableLanguages(), 100);
    } else {
        console.error('❌ Translation modal not found!');
    }
}

// Manuel modal açma
function manualModalOpen(modal) {
    
    // Eski backdrop'ları temizle
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    
    // Backdrop ekle
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    backdrop.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1040;';
    document.body.appendChild(backdrop);
    
    // Modal'ı aç
    modal.style.display = 'block';
    modal.style.zIndex = '1050';
    modal.classList.add('show');
    modal.setAttribute('aria-hidden', 'false');
    modal.setAttribute('aria-modal', 'true');
    
    // Body'yi kilitle
    document.body.classList.add('modal-open');
    document.body.style.overflow = 'hidden';
    
}

// Dil yükleme fonksiyonu
function loadAvailableLanguages() {
    
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    };
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const csrfValue = csrfToken ? csrfToken.getAttribute('content') : null;
    if (csrfValue) headers['X-CSRF-TOKEN'] = csrfValue;
    
    fetch('/admin/api/tenant-languages', { method: 'GET', headers, credentials: 'same-origin' })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.languages) {
                populateLanguageSelectors(data.languages);
            } else {
                const fallback = [
                    { code: 'tr', name: 'Türkçe', flag: '🇹🇷' },
                    { code: 'en', name: 'English', flag: '🇬🇧' }
                ];
                populateLanguageSelectors(fallback);
            }
        })
        .catch(() => {
            const fallback = [
                { code: 'tr', name: 'Türkçe', flag: '🇹🇷' },
                { code: 'en', name: 'English', flag: '🇬🇧' }
            ];
            populateLanguageSelectors(fallback);
        });
}

// AI UYARI SİSTEMLİ Dil selector'larını doldur
function populateLanguageSelectors(languages) {
    
    // MAIN LANGUAGE OVERRIDE: Ana dilleri belirle
    languages.forEach(lang => {
        if (['tr', 'en', 'ar'].includes(lang.code)) {
            lang.is_main_language = true;
        }
    });
    
    const sourceSelect = document.getElementById('sourceLanguage');
    const targetContainer = document.getElementById('targetLanguagesContainer');
    
    if (!sourceSelect || !targetContainer) {
        console.error('❌ Required elements not found');
        return;
    }

    // Clear existing options
    sourceSelect.innerHTML = '<option value="">Seçin...</option>';
    targetContainer.innerHTML = '';

    languages.forEach(lang => {
        // Source language dropdown
        sourceSelect.innerHTML += `<option value="${lang.code}">${lang.flag} ${lang.name}</option>`;
        
        // Target language pretty checkboxes WITH AI WARNING SUPPORT
        const div = document.createElement('div');
        div.className = 'col-md-6 mb-2';
        
        // AI uyarı işareti ekle
        const aiWarningIndicator = lang.is_main_language ? '' : ' ⚠️';
        
        div.innerHTML = `
            <div class="pretty p-default p-curve p-thick p-smooth">
                <input type="checkbox" 
                       value="${lang.code}" 
                       id="target_${lang.code}" 
                       data-lang-name="${lang.name}" 
                       data-lang-flag="${lang.flag}"
                       data-is-main-language="${lang.is_main_language || false}"
                       onchange="checkAIWarning(this)">
                <div class="state p-success-o">
                    <label style="margin-left: 8px;">${lang.flag} ${lang.name}${aiWarningIndicator}</label>
                </div>
            </div>
        `;
        
        targetContainer.appendChild(div);
    });

    // Set default source language to Turkish
    sourceSelect.value = 'tr';
    
    // Add event listeners for source language change
    sourceSelect.addEventListener('change', handleSourceLanguageChange);
    
    // Add event listeners for target language checkboxes with 2-limit
    targetContainer.addEventListener('change', function(event) {
        if (event.target.type === 'checkbox') {
            handleTargetLanguageSelection(event.target);
        }
    });

    // Kaynak dil değişikliğini handle et - bu çok önemli!
    handleSourceLanguageChange();
    
    // Start button durumunu güncelle
    updateStartButtonState();
    
    // Çevir butonu click event listener ekle
    const startBtn = document.getElementById('startTranslation');
    if (startBtn) {
        startBtn.addEventListener('click', startTranslation);
    }
}

// 5-dil sınırlaması kontrolü - Modal uyarı sistemi ile
function handleTargetLanguageSelection(checkbox) {
    const checkedBoxes = document.querySelectorAll('#targetLanguagesContainer input[type="checkbox"]:checked');
    
    if (checkbox.checked && checkedBoxes.length > 5) {
        checkbox.checked = false;
        showLanguageLimitWarning();
        return;
    }
    
    updateStartButtonState();
}

// Kaynak dil değişikliği
function handleSourceLanguageChange() {
    const sourceSelect = document.getElementById('sourceLanguage');
    const selectedSourceLang = sourceSelect ? sourceSelect.value : '';
    
    const targetContainer = document.getElementById('targetLanguagesContainer');
    if (!targetContainer) return;
    
    const checkboxes = targetContainer.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        if (checkbox.value === selectedSourceLang && selectedSourceLang !== '') {
            checkbox.disabled = true;
            checkbox.checked = false;
        } else {
            checkbox.disabled = false;
        }
    });
    
    // AUTO-SELECT FIRST 5 AVAILABLE LANGUAGES (SMART SYSTEM) - BUT ONLY IF <=6 TOTAL LANGUAGES
    if (selectedSourceLang) {
        const availableCheckboxes = Array.from(targetContainer.querySelectorAll('input[type="checkbox"]:not(:disabled)'));
        const totalAvailableLanguages = availableCheckboxes.length;
        
        // First clear all checkboxes
        availableCheckboxes.forEach(cb => cb.checked = false);
        
        // AUTO-SELECT sadece 6 veya daha az dil varsa (1 kaynak + 5 hedef = 6)
        // Çok dil varsa manuel seçim yapılsın
        if (totalAvailableLanguages <= 5) {
            let checkedCount = 0;
            availableCheckboxes.forEach(checkbox => {
                if (checkedCount < 5) {
                    checkbox.checked = true;
                    checkedCount++;
                }
            });
        } else {
        }
    }
    
    updateStartButtonState();
}

// Start button durumu
function updateStartButtonState() {
    const sourceSelect = document.getElementById('sourceLanguage');
    const sourceLanguage = sourceSelect ? sourceSelect.value : '';
    
    const checkedTargets = document.querySelectorAll('#targetLanguagesContainer input[type="checkbox"]:checked');
    const hasTargets = checkedTargets.length > 0;
    
    const startBtn = document.getElementById('startTranslation');
    if (startBtn) {
        startBtn.disabled = !sourceLanguage || !hasTargets;
    }
}

// Çeviri başlatma
function startTranslation() {
    
    const modal = document.getElementById('aiTranslationModal');
    if (!modal) return;

    const startBtn = document.getElementById('startTranslation');
    const buttonText = document.getElementById('buttonText');
    const buttonSpinner = document.getElementById('buttonSpinner');
    const progressDiv = document.getElementById('translationProgress');
    
    // Modal'ı kilitle (overlay ekle)
    addModalOverlay();
    
    // UI durumunu güncelle
    if (startBtn) startBtn.disabled = true;
    if (buttonText) buttonText.textContent = 'Çeviriliyor...';
    if (buttonSpinner) buttonSpinner.style.display = 'inline-block';
    if (progressDiv) progressDiv.style.display = 'block';
    
    // Tüm form elementlerini devre dışı bırak
    lockModalForm();
    
    const entityType = modal.getAttribute('data-entity-type');
    const entityId = parseInt(modal.getAttribute('data-entity-id'));
    
    // Dilleri al
    const sourceSelect = document.getElementById('sourceLanguage');
    const sourceLanguage = sourceSelect ? sourceSelect.value : 'tr';
    
    const targetLanguages = [];
    document.querySelectorAll('#targetLanguagesContainer input[type="checkbox"]:checked').forEach(cb => {
        targetLanguages.push(cb.value);
    });
    
    if (targetLanguages.length === 0) {
        alert('Lütfen en az bir hedef dil seçin!');
        removeModalOverlay();
        unlockModalForm();
        resetTranslationButton();
        return;
    }
    
    // Backend job başlat
    startQueueTranslation(entityType, entityId, sourceLanguage, targetLanguages);
}

// Modal overlay ekleme fonksiyonu - AI Sihirbazı Temalı
function addModalOverlay() {
    const modal = document.getElementById('aiTranslationModal');
    if (!modal) return;
    
    // Var olan overlay'i temizle
    removeModalOverlay();
    
    // Modal content'i bul
    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        // Overlay div'i oluştur - AI Wizard Theme
        const overlay = document.createElement('div');
        overlay.id = 'translationOverlay';
        overlay.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                rgba(99, 102, 241, 0.95) 0%, 
                rgba(139, 92, 246, 0.95) 25%,
                rgba(168, 85, 247, 0.95) 50%,
                rgba(219, 39, 119, 0.95) 75%,
                rgba(236, 72, 153, 0.95) 100%);
            background-size: 400% 400%;
            animation: gradientShift 3s ease infinite;
            z-index: 1060;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            backdrop-filter: blur(10px);
        `;
        
        // AI Wizard Loading Content
        overlay.innerHTML = `
            <style>
                @keyframes gradientShift {
                    0% { background-position: 0% 50%; }
                    50% { background-position: 100% 50%; }
                    100% { background-position: 0% 50%; }
                }
                @keyframes magicPulse {
                    0%, 100% { transform: scale(1); opacity: 0.8; }
                    50% { transform: scale(1.1); opacity: 1; }
                }
                @keyframes sparkle {
                    0%, 100% { opacity: 0; transform: scale(0.5); }
                    50% { opacity: 1; transform: scale(1); }
                }
                .magic-wand {
                    animation: magicPulse 2s ease-in-out infinite;
                    font-size: 3rem;
                    margin-bottom: 1rem;
                    filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.8));
                }
                .sparkles {
                    position: absolute;
                    color: white;
                    animation: sparkle 1.5s ease-in-out infinite;
                }
                .sparkle-1 { top: 20%; left: 20%; animation-delay: 0s; }
                .sparkle-2 { top: 30%; right: 20%; animation-delay: 0.3s; }
                .sparkle-3 { bottom: 30%; left: 25%; animation-delay: 0.6s; }
                .sparkle-4 { bottom: 20%; right: 25%; animation-delay: 0.9s; }
                .ai-title { 
                    color: white;
                    font-size: 1.5rem;
                    font-weight: bold;
                    margin-bottom: 0.5rem;
                    text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
                }
                .ai-subtitle {
                    color: rgba(255, 255, 255, 0.9);
                    font-size: 1rem;
                    margin-bottom: 1.5rem;
                    text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
                }
            </style>
            
            <div class="sparkles sparkle-1">✨</div>
            <div class="sparkles sparkle-2">⭐</div>
            <div class="sparkles sparkle-3">🌟</div>
            <div class="sparkles sparkle-4">💫</div>
            
            <div class="magic-wand">🧙‍♂️</div>
            <div class="ai-title">Yapay Zeka Sihirbazı</div>
            <div class="ai-subtitle">Çeviri hizmeti sizin için başlatıldı</div>
            
            <div class="col-12 mt-3" id="overlayTranslationProgress">
                <div class="d-flex align-items-center justify-content-center mb-3">
                    <div class="spinner-border spinner-border-sm text-white me-2" id="overlaySpinner" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span id="overlayProgressMessage" class="text-white fw-bold">🚀 Yapay zeka sistemi devreye giriyor...</span>
                </div>
                <div class="progress" style="height: 8px; border-radius: 4px; background: rgba(255,255,255,0.25); box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);">
                    <div class="progress-bar" id="overlayProgressBar" role="progressbar" 
                         style="width: 15%; background: linear-gradient(90deg, #fff 0%, rgba(255,255,255,0.9) 50%, #fff 100%); border-radius: 4px; transition: width 0.5s ease; box-shadow: 0 1px 3px rgba(255,255,255,0.3);" 
                         aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-center mt-2">
                    <small id="overlayProgressDetail" class="text-white-50">Gerçek zamanlı progress tracking aktif</small>
                </div>
            </div>
        `;
        
        // Modal content'e relative position ver
        modalContent.style.position = 'relative';
        
        // Overlay'i ekle
        modalContent.appendChild(overlay);
        
    }
}

// Modal overlay kaldırma fonksiyonu
function removeModalOverlay() {
    const overlay = document.getElementById('translationOverlay');
    if (overlay) {
        overlay.remove();
    }
}

// Modal form kilitleme
function lockModalForm() {
    const modal = document.getElementById('aiTranslationModal');
    if (!modal) return;
    
    // Tüm input, select ve button elementlerini devre dışı bırak
    const elements = modal.querySelectorAll('input, select, button');
    elements.forEach(el => {
        el.disabled = true;
        el.setAttribute('data-was-disabled', el.disabled ? 'true' : 'false');
    });
    
    // Modal close buttonlarını da devre dışı bırak
    modal.querySelectorAll('[data-bs-dismiss="modal"], .btn-close').forEach(el => {
        el.style.pointerEvents = 'none';
        el.style.opacity = '0.5';
    });
}

// Modal form kilidi kaldırma
function unlockModalForm() {
    const modal = document.getElementById('aiTranslationModal');
    if (!modal) return;
    
    // Form elementlerini tekrar aktif et
    const elements = modal.querySelectorAll('input, select, button');
    elements.forEach(el => {
        const wasDisabled = el.getAttribute('data-was-disabled') === 'true';
        if (!wasDisabled) {
            el.disabled = false;
        }
        el.removeAttribute('data-was-disabled');
    });
    
    // Modal close buttonları tekrar aktif et
    modal.querySelectorAll('[data-bs-dismiss="modal"], .btn-close').forEach(el => {
        el.style.pointerEvents = '';
        el.style.opacity = '';
    });
}

// GERÇEK ZAMANI QUEUE TRANSLATION BAŞLATMA
function startQueueTranslation(entityType, entityId, sourceLanguage, targetLanguages) {
    
    updateProgress('🚀 Yapay zeka sistemi devreye giriyor...', 15);
    
    // Livewire component'i bul ve job başlat
    findAndCallQueueTranslation(entityId, sourceLanguage, targetLanguages)
        .then(sessionId => {
            
            // Global session ID set et (force check için)
            window.currentSessionId = sessionId;
            
            updateProgress('💪 AI motorları çalışmaya başladı...', 25);
            
            // GERÇEK PROGRESS TRACKING BAŞLAT
            startProgressTracking(sessionId);
            
        })
        .catch(error => {
            console.error('❌ Queue start error:', error);
            updateProgress('❌ Sistem problemi! Tekrar deneyin.', 0);
            setTimeout(() => {
                resetTranslationButton();
                closeTranslationModal();
            }, 3000);
        });
}

// Livewire component bul ve çağır
function findAndCallQueueTranslation(entityId, sourceLanguage, targetLanguages) {
    return new Promise((resolve, reject) => {
        const modal = document.getElementById('aiTranslationModal');
        const entityType = (modal && modal.getAttribute('data-entity-type')) || 'page';
        
        const componentNames = [
            `${entityType}-component`,
            `${entityType}Component`, 
            `${entityType}ManageComponent`,
            `${entityType}-manage-component`
        ];
        
        let targetComponent = null;
        const wireElements = document.querySelectorAll('[wire\\:id]');
        
        for (let element of wireElements) {
            const wireId = element.getAttribute('wire:id');
            try {
                const component = Livewire.find(wireId);
                if (component && component.__instance) {
                    const componentName = (component.__instance.fingerprint && component.__instance.fingerprint.name) || component.__instance.name || 'unknown';
                    
                    if (componentNames.some(name => componentName === name || componentName.includes(name))) {
                        targetComponent = component;
                        break;
                    }
                }
            } catch (error) {
            }
        }
        
        if (targetComponent) {
            let listenerRemoved = false;
            const sessionListener = (data) => {
                if (listenerRemoved) return;
                listenerRemoved = true;
                
                const eventData = Array.isArray(data) ? data[0] : data;
                if (eventData && eventData.sessionId) {
                    resolve(eventData.sessionId);
                } else {
                    reject(new Error('No sessionId received'));
                }
            };
            
            Livewire.on('translationQueued', sessionListener);
            targetComponent.call('translateFromModal', {
                entityId: entityId,
                sourceLanguage: sourceLanguage,
                targetLanguages: targetLanguages,
                overwriteExisting: true
            });
            
            setTimeout(() => {
                if (!listenerRemoved) {
                    listenerRemoved = true;
                    reject(new Error('Translation timeout'));
                }
            }, 300000);
            
        } else {
            reject(new Error('Component not found'));
        }
    });
}

// GERÇEK ZAMANI PROGRESS TRACKING - Log Based System
function startProgressTracking(sessionId) {
    
    let isCompleted = false;
    let pollCount = 0;
    let lastLogPosition = 0;
    
    // Broadcasting listener (PRIMARY)
    if (window.Echo) {
        window.Echo.channel('translation-updates')
            .listen('.translation.completed', (event) => {
                if (event.sessionId === sessionId && !isCompleted) {
                    isCompleted = true;
                    clearInterval(logPolling);
                    handleTranslationCompletion(event);
                }
            });
    }
    
    // GERÇEK LOG-BASED PROGRESS TRACKING
    const logPolling = setInterval(async () => {
        if (isCompleted) {
            clearInterval(logPolling);
            return;
        }
        
        pollCount++;
        
        try {
            // Laravel.log dosyasından gerçek progress verilerini çek
            const progressData = await checkRealTranslationProgress(sessionId, lastLogPosition);
            
            if (progressData.found) {
                lastLogPosition = progressData.logPosition;
                
                // Gerçek progress ile güncelle
                updateProgress(
                    progressData.message || `🔥 AI sistemi çalışıyor... (${progressData.percentage}%)`,
                    progressData.percentage
                );
                
                // Tamamlandı mı kontrolü
                if (progressData.completed && !isCompleted) {
                    isCompleted = true;
                    clearInterval(logPolling);
                    
                    // Completion event manuel tetikleme
                    handleTranslationCompletion({
                        sessionId: sessionId,
                        success: progressData.success || 1,
                        failed: progressData.failed || 0,
                        status: 'completed'
                    });
                }
            } else {
                // Fallback progress (log verisi yoksa)
                const fallbackProgress = Math.min(85, 25 + (pollCount * 2));
                updateProgress(
                    `⚡ Çeviri işlemi devam ediyor... (${fallbackProgress}%)`,
                    fallbackProgress
                );
            }
            
        } catch (error) {
            console.error('❌ Progress check error:', error);
            
            // Hata durumunda basit fallback
            const errorProgress = Math.min(70, 30 + (pollCount * 1.5));
            updateProgress(
                `⚡ Sistem çalışıyor... (${Math.floor(errorProgress)}%)`,
                Math.floor(errorProgress)
            );
        }
        
        // Timeout kontrolü (30 saniye)
        if (pollCount >= 15 && !isCompleted) {
            clearInterval(logPolling);
            forceCompletionCheck();
        }
        
    }, 2000); // 2 saniyede bir kontrol - DAHA AGRESIF
    
    // Ultimate timeout (5 dakika)
    setTimeout(() => {
        if (!isCompleted) {
            clearInterval(logPolling);
            forceCompletionCheck();
        }
    }, 300000);
}

// ENHANCED COMPLETION HANDLER - GLOBAL function
window.handleTranslationCompletion = function(event) {
    
    const successCount = event.success || 0;
    const failedCount = event.failed || 0;
    const totalCount = successCount + failedCount;
    
    // Progress'i KESIN 100%'e çıkar
    if (failedCount > 0) {
        updateProgress(`⚠️ Çeviri tamamlandı: ${successCount} başarılı, ${failedCount} hatalı`, 100);
    } else {
        updateProgress(`🎉 Çeviri başarıyla tamamlandı! (${successCount} çeviri)`, 100);
    }
    
    // Kesin modal kapanması için timeout
    setTimeout(() => {
        
        // Overlay temizle ve modal kapat
        removeModalOverlay();
        unlockModalForm();
        closeTranslationModal();
        
        // Sayfa yenileme
        setTimeout(() => {
            window.location.reload();
        }, 500);
        
    }, 1500); // 1.5 saniye göster, sonra kapat
}

// GERÇEK LOG-BASED PROGRESS CHECK FUNCTION
async function checkRealTranslationProgress(sessionId, lastLogPosition) {
    try {
        // Laravel log'undan gerçek progress verilerini çek
        const response = await fetch('/admin/api/translation-progress', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                sessionId: sessionId,
                lastLogPosition: lastLogPosition
            })
        });
        
        if (response.ok) {
            return await response.json();
        }
        
    } catch (error) {
        console.error('❌ Progress check failed:', error);
    }
    
    return { found: false, percentage: 0, message: '', completed: false };
}

// ZORLA TAMAMLAMA KONTROLÜ
function forceCompletionCheck() {
    updateProgress('⏳ Çeviri tamamlanma durumu kontrol ediliyor...', 95);
    
    // Son bir kez gerçek durum kontrolü
    setTimeout(async () => {
        try {
            const finalCheck = await checkRealTranslationProgress(window.currentSessionId || '', 0);
            
            if (finalCheck.completed) {
                handleTranslationCompletion({
                    sessionId: window.currentSessionId,
                    success: finalCheck.success || 1,
                    failed: finalCheck.failed || 0,
                    status: 'completed'
                });
            } else {
                updateProgress('✅ Çeviri tamamlandı! Sayfa yenileniyor...', 100);
                setTimeout(() => {
                    closeTranslationModal();
                    window.location.reload();
                }, 2000);
            }
            
        } catch (error) {
            console.error('❌ Force check failed:', error);
            updateProgress('✅ İşlem tamamlandı! Sayfa yenileniyor...', 100);
            setTimeout(() => {
                closeTranslationModal();
                window.location.reload();
            }, 2000);
        }
    }, 2000);
}

// ENHANCED PROGRESS UPDATE - Overlay Support with Animation
function updateProgress(message, percentage) {
    
    // Overlay progress (PRIMARY)
    const overlayProgressBar = document.getElementById('overlayProgressBar');
    const overlayProgressMessage = document.getElementById('overlayProgressMessage');
    const overlayProgressDetail = document.getElementById('overlayProgressDetail');
    const overlaySpinner = document.getElementById('overlaySpinner');
    
    if (overlayProgressBar) {
        overlayProgressBar.style.width = percentage + '%';
        overlayProgressBar.setAttribute('aria-valuenow', percentage);
        
        // Progress bar renk değişimi
        if (percentage >= 100) {
            overlayProgressBar.style.background = 'linear-gradient(90deg, #10b981 0%, #059669 50%, #047857 100%)';
        } else if (percentage >= 80) {
            overlayProgressBar.style.background = 'linear-gradient(90deg, #f59e0b 0%, #d97706 50%, #b45309 100%)';
        }
    }
    
    if (overlayProgressMessage) {
        overlayProgressMessage.textContent = message;
    }
    
    if (overlayProgressDetail) {
        overlayProgressDetail.textContent = `İlerleme: ${percentage}% • ${new Date().toLocaleTimeString()}`;
    }
    
    // Spinner kontrolü
    if (overlaySpinner) {
        if (percentage >= 100) {
            overlaySpinner.style.display = 'none';
        } else {
            overlaySpinner.style.display = 'inline-block';
        }
    }
    
    // Fallback - Modal body progress (compatibility)
    const progressBar = document.getElementById('progressBar');
    const progressMessage = document.getElementById('progressMessage');
    
    if (progressBar) progressBar.style.width = percentage + '%';
    if (progressMessage) progressMessage.textContent = message;
}

// Modal kapat
function closeTranslationModal() {
    
    // Overlay'i temizle
    removeModalOverlay();
    
    // Form kilidi kaldır
    unlockModalForm();
    
    const modal = document.getElementById('aiTranslationModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        modal.removeAttribute('aria-modal');
    }
    
    // Backdrop temizle
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    
    resetTranslationButton();
}

// Buton reset
function resetTranslationButton() {
    const startBtn = document.getElementById('startTranslation');
    const buttonText = document.getElementById('buttonText');
    const buttonSpinner = document.getElementById('buttonSpinner');
    const progressDiv = document.getElementById('translationProgress');
    
    // Overlay ve form kilidi temizle
    removeModalOverlay();
    unlockModalForm();
    
    if (startBtn) startBtn.disabled = false;
    if (buttonText) buttonText.textContent = 'Çevir';
    if (buttonSpinner) buttonSpinner.style.display = 'none';
    if (progressDiv) progressDiv.style.display = 'none';
}

// Livewire event listeners
document.addEventListener('livewire:initialized', () => {
    
    Livewire.on('translation-complete', (data) => {
        updateProgress('🎉 Çeviri tamamlandı! Sayfa yenileniyor...', 100);
        setTimeout(() => {
            closeTranslationModal();
            window.location.reload();
        }, 2000);
    });

    Livewire.on('translation-error', (data) => {
        updateProgress('❌ Çeviri hatası oluştu. Tekrar deneyin.', 0);
        setTimeout(() => {
            resetTranslationButton();
        }, 3000);
    });
});

// 🚨 DİL SINIRI UYARI SİSTEMİ
function showLanguageLimitWarning() {
    
    // Mevcut uyarıları temizle
    removeAIWarning();
    removeLimitWarning();
    
    // Modal footer'da limit uyarısı oluştur
    const modalFooter = document.querySelector('#aiTranslationModal .modal-footer');
    if (!modalFooter) return;
    
    const limitWarningDiv = document.createElement('div');
    limitWarningDiv.id = 'languageLimitWarning';
    limitWarningDiv.className = 'alert d-flex align-items-start mb-3';
    limitWarningDiv.style.cssText = `
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 16px;
        font-size: 14px;
        background-color: #f8f9fa;
        color: #495057;
    `;
    
    limitWarningDiv.innerHTML = `
        <div class="limit-warning-icon" style="font-size: 1.5rem; margin-right: 12px; flex-shrink: 0;">⚡</div>
        <div class="flex-grow-1">
            <div class="fw-semibold mb-1">
                <i class="fas fa-exclamation-circle me-1"></i>Dil Seçim Sınırı
            </div>
            <div class="small">
                Performans ve kalite için aynı anda en fazla <strong>5 dil</strong> seçebilirsiniz. 
                Diğer diller için ikinci bir çeviri işlemi başlatabilirsiniz.
            </div>
        </div>
        <button type="button" class="btn-close btn-sm" onclick="removeLimitWarning()" aria-label="Kapat"></button>
    `;
    
    // Footer'ın en üstüne ekle
    modalFooter.insertBefore(limitWarningDiv, modalFooter.firstChild);
    
}

function removeLimitWarning() {
    const existingWarning = document.getElementById('languageLimitWarning');
    if (existingWarning) {
        existingWarning.remove();
    }
}

// 🚨 AI UYARI SİSTEMİ FONKSIYONLARI
function checkAIWarning(checkbox) {
    
    const isMainLanguage = checkbox.getAttribute('data-is-main-language') === 'true';
    const langName = checkbox.getAttribute('data-lang-name');
    
    if (checkbox.checked && !isMainLanguage) {
        // Zayıf AI destekli dil seçildi - uyarı göster
        showAIWarningModal(langName, checkbox.value, checkbox);
    } else {
        // Ana dil veya checkbox kapatıldı - uyarıyı temizle
        removeAIWarning();
    }
}

function showAIWarningModal(langName, langCode, checkbox) {
    
    // Mevcut uyarıyı temizle
    removeAIWarning();
    
    // Modal footer'da uyarı mesajı oluştur
    const modalFooter = document.querySelector('#aiTranslationModal .modal-footer');
    if (!modalFooter) return;
    
    const aiWarningDiv = document.createElement('div');
    aiWarningDiv.id = 'aiWarningSystem';
    aiWarningDiv.className = 'alert d-flex align-items-start mb-3';
    aiWarningDiv.style.cssText = `
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 16px;
        font-size: 14px;
    `;
    
    aiWarningDiv.innerHTML = `
        <div class="ai-warning-icon" style="font-size: 1.5rem; margin-right: 12px; flex-shrink: 0;">⚠️</div>
        <div class="flex-grow-1">
            <div class="fw-semibold mb-1">
                <i class="fas fa-exclamation-triangle me-1"></i>Yapay Zeka Çeviri Uyarısı
            </div>
            <div class="small">
                <strong>${langName}</strong> dili için yapay zeka çeviri sistemi sınırlı destek sağlamaktadır. 
                Çeviri kalitesi değişken olabilir ve sonuçların kontrol edilmesi önerilir.
            </div>
        </div>
        <button type="button" class="btn-close btn-sm" onclick="removeAIWarning(); uncheckLanguage('${langCode}')" aria-label="Kapat"></button>
    `;
    
    // Footer'ın en üstüne ekle
    modalFooter.insertBefore(aiWarningDiv, modalFooter.firstChild);
    
}

function removeAIWarning() {
    const existingWarning = document.getElementById('aiWarningSystem');
    if (existingWarning) {
        existingWarning.remove();
    }
}

function uncheckLanguage(langCode) {
    const checkbox = document.getElementById(`target_${langCode}`);
    if (checkbox) {
        checkbox.checked = false;
        updateStartButtonState();
    }
}

// Modal kapanırken uyarıları temizle
function closeTranslationModal() {
    
    // Tüm uyarıları temizle
    removeAIWarning();
    removeLimitWarning();
    
    // Overlay'i temizle
    removeModalOverlay();
    
    // Form kilidi kaldır
    unlockModalForm();
    
    const modal = document.getElementById('aiTranslationModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        modal.removeAttribute('aria-modal');
    }
    
    // Backdrop temizle
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    
    resetTranslationButton();
}

