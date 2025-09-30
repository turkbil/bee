// Page Management System - Consolidated JavaScript
// Combines all manage-related JavaScript code from component, layout and main.js

// Global variables
window.currentPageId = null;
window.currentLanguage = window.tenantDefaultLanguage || 'tr';
window.allLanguagesSeoData = {};

// ===== MODAL BACKDROP TEMİZLEME SİSTEMİ =====
window.cleanModalBackdrop = function() {
    console.log('🔒 NURU: Modal backdrop temizleme işlemi başlıyor...');
    
    // TÜM BACKDROP'LARI BUL VE SİL - ULTRA AGGRESSIVE
    const backdrops = document.querySelectorAll(
        '.modal-backdrop, ' +
        '#aiTranslationModalBackdrop, ' +
        '#translation-modal-backdrop, ' +
        '[id*="backdrop"], ' +
        '[class*="backdrop"], ' +
        'div[style*="background-color: rgba"], ' +
        'div[style*="position: fixed"][style*="z-index"]'
    );

    backdrops.forEach((backdrop, index) => {
        console.log(`🗑️ NURU: Backdrop ${index + 1} siliniyor:`, backdrop.id || backdrop.className);
        backdrop.remove();
    });

    // BODY CLASS VE STYLE TEMİZLE - ULTRA RESET
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    document.body.style.marginRight = '';
    document.body.style.pointerEvents = '';
    
    // MODAL CONTAINER'LARI TEMİZLE
    const modalContainers = document.querySelectorAll('.modal[style*="display: block"]');
    modalContainers.forEach(modal => {
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
    });
    
    console.log('✅ NURU: Modal backdrop temizleme tamamlandı');
};

// EMERGENCY GLOBAL CLEANUP - Console'dan çağrılabilir
window.emergencyCleanupBackdrop = function() {
    console.log('🚨 EMERGENCY: Manual backdrop cleanup çağrıldı!');
    window.cleanModalBackdrop();
    // Extra cleanup
    setTimeout(() => {
        window.cleanModalBackdrop();
    }, 500);
};

// BOOTSTRAP MODAL EVENT'LERİNE HOOK ET
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap modal hide event'inde backdrop temizle
    $(document).on('hide.bs.modal', function(e) {
        console.log('🎭 NURU: Bootstrap modal hide event tetiklendi');
        setTimeout(() => {
            window.cleanModalBackdrop();
        }, 100);
    });
    
    // Modal kapatma butonlarına hook et
    $(document).on('click', '[data-bs-dismiss="modal"], .btn-close', function() {
        console.log('🔘 NURU: Modal kapatma butonu tıklandı');
        setTimeout(() => {
            window.cleanModalBackdrop();
        }, 300);
    });
});

// ===== SYSTEM INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function() {
    // Dinamik sayfa tespiti
    const currentPath = window.location.pathname;
    let pageName = 'Manage';
    
    if (currentPath.includes('/page/manage')) {
        pageName = 'Page Manage';
    } else if (currentPath.includes('/menumanagement')) {
        pageName = 'Menu Management';
    } else if (currentPath.includes('/portfolio/manage')) {
        pageName = 'Portfolio Manage';
    } else if (currentPath.includes('/announcement/manage')) {
        pageName = 'Announcement Manage';
    }
    
    // console.log(`🚀 ${pageName} sayfası başlatılıyor...`);
    
    // Initialize core systems
    setupLanguageSwitching();
    setupSaveAndContinueSystem();
    setupSeoCharacterCounters();
    setupSeoEnterPrevention();
    initializeTabSystem();
    setupSlugNormalization();

    // İlk yüklemede dil içeriğini ayarla
    if (window.currentLanguage && window.switchLanguageContent) {
        setTimeout(() => {
            window.switchLanguageContent(window.currentLanguage);
            console.log('🎯 İlk yükleme - dil içeriği ayarlandı:', window.currentLanguage);
        }, 100);
    }
    
    // console.log(`✅ ${pageName} sayfası hazır!`);
});

// ===== LANGUAGE SWITCHING SYSTEM =====
function setupLanguageSwitching() {
    console.log('🔧 Language switching sistemi kuruluyor...');
    
    const langButtons = $('.language-switch-btn');
    console.log('🔍 Bulunan language button sayısı:', langButtons.length);
    
    langButtons.each(function(index) {
        console.log(`  ${index}: ${$(this).data('language')} - class: ${this.className}`);
    });
    
    // 🚨 KRİTİK FİX: Event delegation kullan - DOM yenilense bile çalışır
    $(document).off('click', '.language-switch-btn').on('click', '.language-switch-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('🚨🚨 LANGUAGE BUTTON CLICKED! Event captured!');
        console.log('🚨 Event target:', e.target);
        console.log('🚨 Current target:', e.currentTarget);
        const language = $(this).data('language');
        const nativeName = $(this).data('native-name');
        
        // Language switching triggered
        console.log('📍 Element data-language:', $(this).data('language'));
        console.log('📍 Element data-native-name:', $(this).data('native-name'));
        
        // 🚨 Acil kontrol: Element gerçekten language switch button mı?
        if (!$(this).hasClass('language-switch-btn')) {
            console.log('❌ HATA: Bu element language-switch-btn değil!');
            console.log('🔍 Element class:', this.className);
            return false;
        }
        
        // Element validation successful
        
        // *** ELEMENT TİPİ TESPİTİ ***
        console.log('🔍 Element tag name:', this.tagName);
        console.log('🔍 Element class list:', this.className);
        console.log('🔍 Element wire:click attribute:', $(this).attr('wire:click'));
        console.log('🔍 Element onclick attribute:', $(this).attr('onclick'));
        
        // *** LIVEWIRE VARLIQ KONTROL ***
        const livewireExists = typeof Livewire !== 'undefined';
        console.log('🔍 Livewire tanımlı mı?', livewireExists);
        if (livewireExists) {
            console.log('🔍 Livewire versiyonu:', Livewire.version || 'Versiyon bulunamadı');
        }
        
        // *** WIRE DİRECTİVE KONTROL ***
        const hasWireClick = $(this).attr('wire:click');
        console.log('🔍 wire:click directive var mı?', !!hasWireClick);
        if (hasWireClick) {
            console.log('🔍 wire:click değeri:', hasWireClick);
        }
        
        // Update button states
        $('.language-switch-btn').removeClass('text-primary').addClass('text-muted')
            .css('border-bottom', '2px solid transparent')
            .prop('disabled', false); // KRİTİK: Tüm buttonları enable et
        
        $(this).removeClass('text-muted').addClass('text-primary')
            .css('border-bottom', '2px solid var(--primary-color)')
            .prop('disabled', true); // Mevcut dil button'u disable
        
        console.log('✅ Button states güncellendi');
        
        // Update language badge
        const languageBadge = document.getElementById('languageBadge');
        if (languageBadge && nativeName) {
            const badgeContent = languageBadge.querySelector('.nav-link');
            if (badgeContent) {
                badgeContent.innerHTML = `<i class="fas fa-language me-2"></i>${nativeName}<i class="fas fa-chevron-down ms-2"></i>`;
                console.log('✅ Language badge güncellendi:', nativeName);
            }
        }
        
        // Update dropdown current language
        const dropdownCurrentLang = document.getElementById('dropdown-current-lang');
        if (dropdownCurrentLang && nativeName) {
            dropdownCurrentLang.textContent = nativeName;
            console.log('✅ Dropdown current language güncellendi:', nativeName);
        }
        
        // Update dropdown items state (active/inactive styling and check icons)
        const dropdownItems = document.querySelectorAll('.dropdown-item.language-switch-btn');
        dropdownItems.forEach(item => {
            const itemLanguage = item.getAttribute('data-language');
            const checkIcon = item.querySelector('.fas.fa-check');
            
            if (itemLanguage === language) {
                // Active item - Tabler.io standart active bg renkleri
                item.classList.add('active');
                item.style.backgroundColor = 'var(--tblr-active-bg, #e9ecef)';
                item.style.color = 'var(--tblr-body-color, #1a1a1a)';
                item.setAttribute('disabled', 'true');
                
                // Add check icon if not exists
                if (!checkIcon) {
                    const newCheckIcon = document.createElement('i');
                    newCheckIcon.className = 'fas fa-check ms-2';
                    newCheckIcon.style.color = 'var(--tblr-body-color, #1a1a1a)';
                    item.appendChild(newCheckIcon);
                } else {
                    // Update existing check icon color
                    checkIcon.style.color = 'var(--tblr-body-color, #1a1a1a)';
                }
            } else {
                // Inactive item
                item.classList.remove('active');
                item.style.backgroundColor = '';
                item.style.color = '';
                item.removeAttribute('disabled');
                
                // Remove check icon if exists
                if (checkIcon) {
                    checkIcon.remove();
                }
            }
        });
        
        // Aktif tab'ı logla
        const activeTabElement = $('.nav-tabs .nav-link.active');
        const activeTab = activeTabElement.attr('href');
        console.log('📑 Aktif tab:', activeTab);
        
        // *** LANGUAGE CONTENT ELEMANLARI TESPİTİ ***
        console.log('🔍 Language content elementleri aranıyor...');
        
        // MenuManagement için SEO content'leri hariç tut
        const currentPath = window.location.pathname;
        const isMenuManagement = currentPath.includes('/menumanagement');
        
        let allLanguageContents, targetLanguageContent;
        
        if (isMenuManagement) {
            // Sadece basic content'ler
            allLanguageContents = $('.language-content');
            targetLanguageContent = $(`.language-content[data-language="${language}"]`);
            console.log('🚫 MenuManagement - SEO contentler atlandı');
        } else {
            // Hem basic hem SEO content'ler
            allLanguageContents = $('.language-content, .seo-language-content');
            targetLanguageContent = $(`.language-content[data-language="${language}"], .seo-language-content[data-language="${language}"]`);
        }
        
        console.log('📊 Toplam language-content sayısı:', allLanguageContents.length);
        console.log('🎯 Hedef dil content sayısı:', targetLanguageContent.length);
        
        // Language content detayları
        console.log('🔍 Tüm language-content elementleri:');
        allLanguageContents.each(function(index) {
            console.log(`  📦 Element ${index}:`);
            console.log(`     - data-language: "${$(this).data('language')}"`);
            console.log(`     - görünür mü: ${$(this).is(':visible')}`);
            console.log(`     - display style: ${$(this).css('display')}`);
            console.log(`     - class list: ${this.className}`);
        });
        
        console.log('🔍 Hedef dil elementleri:');
        targetLanguageContent.each(function(index) {
            console.log(`  🎯 Hedef element ${index}:`);
            console.log(`     - data-language: "${$(this).data('language')}"`);
            console.log(`     - görünür mü: ${$(this).is(':visible')}`);
            console.log(`     - display style: ${$(this).css('display')}`);
        });
        
        // *** KARAR VERME - LIVEWIRE MI JQUERY MI? ***
        if (livewireExists && hasWireClick) {
            console.log('🚨 LIVEWIRE TETİKLENECEK - wire:click mevcut');
            console.log('⚠️ jQuery işlemi iptal ediliyor, Livewire devralıyor');

            // 📋 TAB STATE'İ GÜVENCE ALTINA AL
            const storageKey = 'page_active_tab';
            const currentActiveTab = $('.nav-tabs .nav-link.active').attr('href');
            if (currentActiveTab) {
                localStorage.setItem(storageKey, currentActiveTab);
                console.log('💾 Livewire dil değişimi öncesi tab kaydedildi:', currentActiveTab);
            }

            return; // Let Livewire handle this
        }
        
        if (allLanguageContents.length === 0) {
            console.log('🚨 HATA: LANGUAGE-CONTENT ELEMANLARI BULUNAMADI!');
            console.log('🔍 DOM yapısı kontrol ediliyor...');
            const anyDataLanguageElements = $('[data-language]');
            console.log('📦 DOM içinde data-language attribute olan elementler:', anyDataLanguageElements.length);
            anyDataLanguageElements.each(function(index) {
                console.log(`  📍 Element ${index}: tag=${this.tagName}, class="${this.className}", data-language="${$(this).data('language')}"`);
            });
            return;
        }
        
        // *** JQUERY İLE LANGUAGE CONTENT DEĞİŞİMİ ***

        // Önce tüm elementleri gizle
        allLanguageContents.each(function(index) {
            // KRİTİK: Hide için de force et
            $(this).hide().css('display', 'none');
        });

        // KRİTİK FİX: Aktif tab kontrolü
        const currentActiveTab = $('.nav-tabs .nav-link.active').attr('href');
        const isSeoTabActive = currentActiveTab === '#1';
        const isCodeTabActive = currentActiveTab === '#2';
        targetLanguageContent.each(function(index) {
            const beforeShow = $(this).is(':visible');
            const isBasicContent = $(this).hasClass('language-content');
            const isSeoContent = $(this).hasClass('seo-language-content');

            // KRİTİK KARAR: Tab durumuna göre gösterme mantığı
            let shouldShow = false;

            if (isSeoTabActive) {
                // SEO tab aktifse: Hem basic hem SEO content'leri göster
                shouldShow = true;
            } else if (isCodeTabActive) {
                // Code tab aktifse: Hiçbir language content gösterme (Code ortak)
                shouldShow = false;
            } else {
                // Diğer tab'lar aktifse: Sadece basic content'leri göster
                shouldShow = isBasicContent;
            }

            if (shouldShow) {
                // KRİTİK FİX: TÜM elementler için CSS display force et
                $(this).css({
                    'display': 'block',
                    'visibility': 'visible'
                }).removeClass('d-none').show();

                // ULTRA FİX: Multiple DOM manipulation methods
                if (!$(this).is(':visible')) {
                    // Method 1: setProperty with important
                    $(this)[0].style.setProperty('display', 'block', 'important');
                    $(this)[0].style.setProperty('visibility', 'visible', 'important');

                    // Method 2: CSS class override
                    $(this).addClass('force-visible');

                    // Method 3: Inline style override
                    $(this).attr('style', 'display: block !important; visibility: visible !important;');

                    console.log(`  🔨 ULTRA FİX: Multiple override methods - ${isBasicContent ? 'Basic' : 'SEO'}`);
                }

                // SEO content için özel fix
                if (!isMenuManagement && isSeoContent) {
                    // Parent container check
                    const parentContainer = $(this).closest('.tab-pane');
                    if (parentContainer.length) {
                        parentContainer.addClass('show active');
                        console.log(`  🔧 SEO parent container aktivasyon edildi`);
                    }
                    console.log(`  🔧 SEO element için display:block force edildi`);
                }
            }

            const afterShow = $(this).is(':visible');
            // Element visibility updated
        });
        
        // Final durum kontrolü
        // Update global variables
        window.currentLanguage = language;
        
        // *** LIVEWIRE ÇAĞRISI KALDIRILDI - PURE JQUERY SİSTEM ***
        // Livewire morph'ları tab içeriğini bozuyordu, sadece client-side yapıyoruz
        // AdminLanguageSwitcher ve PageManageComponent çağrıları devre dışı
        console.log('✅ Pure jQuery dil değişimi - Livewire render YOK');
        
        // Language switching completed
    });
}

// ===== SAVE AND CONTINUE SYSTEM =====
function setupSaveAndContinueSystem() {
    // ✅ Debounce flag - çift tıklama önleme
    let isSaving = false;

    document.addEventListener('click', function(e) {
        const saveButton = e.target.closest('.save-button');

        if (saveButton) {
            // ✅ Eğer kaydetme işlemi devam ediyorsa engelle
            if (isSaving) {
                console.log('⏸️ Kaydetme zaten devam ediyor - atlandı');
                e.preventDefault();
                e.stopPropagation();
                return;
            }

            // 🔥 MONACO SYNC - Save öncesi editör değerlerini textarea'lara aktar
            if (window.cssEditor) {
                const cssTextarea = document.getElementById('css-textarea');
                if (cssTextarea) {
                    const currentValue = window.cssEditor.getValue();
                    cssTextarea.value = currentValue;
                    cssTextarea.dispatchEvent(new Event('input', { bubbles: true }));
                    console.log('🔄 CSS Editor sync edildi:', currentValue.length, 'karakter');
                }
            }

            if (window.jsEditor) {
                const jsTextarea = document.getElementById('js-textarea');
                if (jsTextarea) {
                    const currentValue = window.jsEditor.getValue();
                    jsTextarea.value = currentValue;
                    jsTextarea.dispatchEvent(new Event('input', { bubbles: true }));
                    console.log('🔄 JS Editor sync edildi:', currentValue.length, 'karakter');
                }
            }

            // ✅ Kaydetme flag'ini set et
            isSaving = true;
            console.log('💾 Save button tıklandı');

            // ✅ 2 saniye sonra flag'i sıfırla
            setTimeout(() => {
                isSaving = false;
                console.log('✅ Save debounce reset edildi');
            }, 2000);
            
            // Get active language - DETAYLI DEBUG
            const activeLanguageBtn = document.querySelector('.language-switch-btn.text-primary');
            const currentLang = activeLanguageBtn ? activeLanguageBtn.dataset.language : window.currentLanguage;
            
            console.log('🔍🔍 AKTİF DİL TESPİTİ:');
            console.log('  - .text-primary button:', activeLanguageBtn);
            console.log('  - Bulunan dil:', currentLang);
            console.log('  - window.currentLanguage:', window.currentLanguage);
            
            // TÜM language button'ları kontrol et
            const allLangButtons = document.querySelectorAll('.language-switch-btn');
            console.log('🔍 Tüm dil button\'ları:');
            allLangButtons.forEach((btn, index) => {
                const lang = btn.dataset.language;
                const isActive = btn.classList.contains('text-primary');
                const isDisabled = btn.disabled;
                console.log(`  ${index}: ${lang} - aktif:${isActive}, disabled:${isDisabled}`);
            });
            
            // Detect "Save and Continue" button
            const wireClick = saveButton.getAttribute('wire:click');
            const isContinueButton = wireClick && wireClick.includes('save(false, false)');
            
            // Get active tab with extensive debugging
            const activeTabElement = document.querySelector('.nav-tabs .nav-link.active');
            const activeTab = activeTabElement ? activeTabElement.getAttribute('href') : null;
            
            if (activeTabElement) {
                const tabText = activeTabElement.textContent.trim();
                console.log('🔍🔍 Aktif tab detayları:');
                console.log('  - Tab ID:', activeTab);
                console.log('  - Tab metni:', tabText);
                console.log('  - SEO tab mı?:', tabText.includes('SEO'));
                console.log('  - Element:', activeTabElement);
            }
            
            if (isContinueButton) {
                // 🎯 NURULLAH'IN YENİ KURALI: Geçici state koruma - sadece sayfa yenilenmediği sürece
                console.log('🎯 Kaydet ve Devam Et - GEÇİCİ state korunacak');
                console.log('  - Aktif dil:', currentLang);
                console.log('  - Aktif tab:', activeTab);
                
                // sessionStorage yerine window object kullan (sadece aynı pencerede geçerli)
                window.tempSavedLanguage = currentLang;
                window.tempSavedTab = activeTab;
                
                console.log('✅ Geçici state window object\'e kaydedildi');
                console.log('📋 KURAL: Sayfa tamamen yenilenirse bu veriler kaybolacak');
            } else {
                // Normal Kaydet - geçici state'leri temizle
                if (window.tempSavedLanguage) {
                    delete window.tempSavedLanguage;
                    console.log('🧹 Geçici dil state temizlendi');
                }
                if (window.tempSavedTab) {
                    delete window.tempSavedTab;
                    console.log('🧹 Geçici tab state temizlendi');
                }
                console.log('📤 Normal Kaydet - geçici state\'ler temizlendi');
            }
        }
    });
    
    // 🎯 NURULLAH'IN YENİ KURALI: İlk açılışta DAIMA varsayılan state (TR + Temel Bilgiler)
    console.log('🔎 RESTORE KONTROL - sayfa başladı');
    console.log('📋 KURAL: İlk açılışta her zaman TR dili + Temel Bilgiler tab');
    
    // İlk açılışta localStorage'daki eski state'leri temizle
    const savedLanguage = localStorage.getItem('page_active_language');
    const savedTab = localStorage.getItem('page_active_tab_persist');
    
    if (savedLanguage || savedTab) {
        console.log('🧹 SAYFA AÇILIŞI - Eski state\'ler temizleniyor...');
        console.log('🧹 Temizlenen dil:', savedLanguage || 'yok');
        console.log('🧹 Temizlenen tab:', savedTab || 'yok');
        
        localStorage.removeItem('page_active_language');
        localStorage.removeItem('page_active_tab_persist');
        console.log('✅ State storage temizlendi - varsayılan açılış hazır');
    }
    
    // Sayfa her açıldığında varsayılan state: TR dili + ilk tab aktif
    console.log('🎯 Varsayılan state uygulanıyor: TR dili + Temel Bilgiler tab');
    
    // Artık restore işlemi yok - her zaman temiz açılış
    if (false) {
        console.log('🔄 Kaydedilen state\'ler restore ediliyor...');
        console.log('🌍 Dil:', savedLanguage || 'yok');
        console.log('📑 Tab:', savedTab || 'yok');
        
        setTimeout(function() {
            // Restore language first
            if (savedLanguage) {
                const targetLangBtn = $(`.language-switch-btn[data-language="${savedLanguage}"]`);
                if (targetLangBtn.length) {
                    targetLangBtn.click();
                    console.log('✅ Dil restore tamamlandı:', savedLanguage);
                }
            }
            
            // Then restore tab
            if (savedTab) {
                console.log('🔍 Tab restore deneniyor:', savedTab);
                
                // İLKİ: Mevcut tab'ları listele
                const allTabs = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
                console.log('🔍 Mevcut tüm tab\'lar:');
                allTabs.forEach((tab, index) => {
                    const href = tab.getAttribute('href');
                    const text = tab.textContent.trim();
                    console.log(`  Tab ${index}: href="${href}", text="${text}"`);
                });
                
                const targetTabElement = document.querySelector(`[href="${savedTab}"]`);
                console.log('🔍 Bulunan tab element:', targetTabElement);
                
                if (targetTabElement) {
                    const tabText = targetTabElement.textContent.trim();
                    console.log('🔍 Restore edilecek tab metni:', tabText);
                    console.log('🔍 Bootstrap var mı?', typeof bootstrap !== 'undefined');
                    
                    // KRİTİK FİX: Tab pane'lerini de manuel aktif et
                    const tabId = savedTab.replace('#', '');
                    const targetPane = document.getElementById(tabId);
                    
                    console.log('🔧 Tab pane kontrolü:', {
                        tabId: tabId,
                        targetPane: targetPane,
                        savedTab: savedTab
                    });
                    
                    if (typeof bootstrap !== 'undefined') {
                        const tab = new bootstrap.Tab(targetTabElement);
                        tab.show();
                        console.log('✅ Tab restore tamamlandı (Bootstrap):', savedTab, '-', tabText);
                    } else {
                        // Manuel tab aktivasyon
                        console.log('🔧 Manuel tab aktivasyon başlıyor...');
                        
                        // 1. Tüm tab link'leri deaktif et
                        document.querySelectorAll('.nav-link').forEach(tab => {
                            tab.classList.remove('active');
                            tab.setAttribute('aria-selected', 'false');
                        });
                        
                        // 2. Tüm tab pane'leri deaktif et
                        document.querySelectorAll('.tab-pane').forEach(pane => {
                            pane.classList.remove('show', 'active');
                        });
                        
                        // 3. Hedef tab'ı aktif et
                        targetTabElement.classList.add('active');
                        targetTabElement.setAttribute('aria-selected', 'true');
                        
                        // 4. Hedef pane'i aktif et
                        if (targetPane) {
                            targetPane.classList.add('show', 'active');
                            console.log('✅ Tab pane aktif edildi:', tabId);
                        } else {
                            console.log('❌ Tab pane bulunamadı:', tabId);
                        }
                        
                        console.log('✅ Tab restore tamamlandı (Manuel):', savedTab, '-', tabText);
                    }
                } else {
                    console.log('❌ Tab element bulunamadı:', savedTab);
                    // Alternative selectors try
                    const altTab1 = document.querySelector(`a[href="${savedTab}"]`);
                    const altTab2 = document.querySelector(`.nav-link[href="${savedTab}"]`);
                    console.log('🔍 Alternatif tab selectors:');
                    console.log('  - a[href]:', altTab1);
                    console.log('  - .nav-link[href]:', altTab2);
                    
                    if (altTab1) {
                        altTab1.click();
                        console.log('✅ Tab restore (alt1):', savedTab);
                    } else if (altTab2) {
                        altTab2.click();
                        console.log('✅ Tab restore (alt2):', savedTab);
                    }
                }
            }
            
            // Clean up storage
            localStorage.removeItem('page_active_language');
            localStorage.removeItem('page_active_tab_persist');
            console.log('🧹 State storage temizlendi');
        }, 500); // Tab restore için biraz daha bekle
    }
}

// ===== SEO CHARACTER COUNTERS =====
function setupSeoCharacterCounters() {
    // Sadece content tipindeki modüllerde SEO çalıştır
    const currentPath = window.location.pathname;
    const isMenuManagement = currentPath.includes('/menumanagement');
    
    if (isMenuManagement) {
        // console.log('🚫 MenuManagement - SEO sistemi atlandı');
        return; // MenuManagement için SEO sistemini çalıştırma
    }
    
    setTimeout(function() {
        const languages = ['tr', 'en', 'ar'];
        
        languages.forEach(function(lang) {
            // Title counter
            const titleInput = document.querySelector(`[wire\\:model="seoDataCache.${lang}.seo_title"]`);
            const titleCounter = document.querySelector(`.char-count-${lang}-title`);
            const titleProgress = document.querySelector(`.progress-${lang}-title`);
            
            if (titleInput && titleCounter && titleProgress) {
                function updateTitleCounter() {
                    const length = titleInput.value.length;
                    titleCounter.textContent = length;
                    titleProgress.style.width = (length / 60 * 100) + '%';
                    
                    if (length > 54) titleProgress.className = 'progress-bar bg-danger';
                    else if (length > 48) titleProgress.className = 'progress-bar bg-warning';
                    else titleProgress.className = 'progress-bar bg-success';
                }
                
                titleInput.addEventListener('input', updateTitleCounter);
                updateTitleCounter();
            }
            
            // Description counter
            const descInput = document.querySelector(`[wire\\:model="seoDataCache.${lang}.seo_description"]`);
            const descCounter = document.querySelector(`.char-count-${lang}-desc`);
            const descProgress = document.querySelector(`.progress-${lang}-desc`);
            
            if (descInput && descCounter && descProgress) {
                function updateDescCounter() {
                    const length = descInput.value.length;
                    descCounter.textContent = length;
                    descProgress.style.width = (length / 160 * 100) + '%';
                    
                    if (length > 144) descProgress.className = 'progress-bar bg-danger';
                    else if (length > 128) descProgress.className = 'progress-bar bg-warning';
                    else descProgress.className = 'progress-bar bg-success';
                }
                
                descInput.addEventListener('input', updateDescCounter);
                updateDescCounter();
            }
        });
    }, 800); // Wait for SEO tab to load
}

// ===== TAB SYSTEM =====
function initializeTabSystem() {
    const storageKey = 'page_active_tab';
    
    // Restore active tab
    const savedTab = localStorage.getItem(storageKey);
    if (savedTab) {
        const tabElement = document.querySelector(`[href="${savedTab}"]`);
        if (tabElement && typeof bootstrap !== 'undefined') {
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    }
    
    // Bind tab events
    const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabLinks.forEach(link => {
        link.addEventListener('shown.bs.tab', (e) => {
            localStorage.setItem(storageKey, e.target.getAttribute('href'));
        });
    });
}

// ===== MULTI-LANGUAGE FORM SWITCHER =====
const MultiLangFormSwitcher = {
    init() {
        const languageButtons = document.querySelectorAll('.language-switch-btn');
        const languageContents = document.querySelectorAll('.language-content');
        
        if (languageButtons.length === 0) {
            console.log('ℹ️ Language switch buttons bulunamadı - tek dil sistemi aktif');
            // Tek dil durumunda da devam et - form işlevselliği etkilenmesin
            this.setupSingleLanguageMode();
            return;
        }
        
        // Language button click events
        languageButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const targetLang = button.getAttribute('data-language');
                
                if (targetLang) {
                    this.switchLanguage(targetLang).catch(console.error);
                }
            });
        });
    },

    setupSingleLanguageMode() {
        console.log('🔧 Tek dil modu aktif - tüm language-content elementleri görünür yapılıyor');
        
        // Tek dil durumunda tüm content'leri görünür yap
        const languageContents = document.querySelectorAll('.language-content, .seo-language-content');
        languageContents.forEach(content => {
            content.style.display = 'block';
            content.style.visibility = '';
            content.classList.remove('d-none', 'force-visible');
        });
        
        // Tek dil durumu için global language ayarla
        const firstLanguageContent = document.querySelector('.language-content[data-language]');
        if (firstLanguageContent) {
            const defaultLang = firstLanguageContent.getAttribute('data-language');
            window.currentLanguage = defaultLang;
            console.log('🌍 Tek dil modu - varsayılan dil:', defaultLang);
        }
        
        console.log('✅ Tek dil modu kurulumu tamamlandı');
    },
    
    async switchLanguage(language) {
        // Update global currentLanguage
        window.currentLanguage = language;
        console.log('🌍 Global currentLanguage güncellendi:', language);
        
        // Update active button
        const languageButtons = document.querySelectorAll('.language-switch-btn');
        languageButtons.forEach(btn => {
            if (btn.getAttribute('data-language') === language) {
                btn.classList.add('text-primary');
                btn.classList.remove('text-muted');
                btn.style.borderBottom = '2px solid var(--primary-color) !important';
            } else {
                btn.classList.remove('text-primary');
                btn.classList.add('text-muted');
                btn.style.borderBottom = '2px solid transparent';
            }
        });
        
        // Update content visibility - TEMEL BİLGİLER TAB
        const languageContents = document.querySelectorAll('.language-content');
        languageContents.forEach(content => {
            if (content.getAttribute('data-language') === language) {
                content.style.display = 'block';
                content.style.visibility = '';
                content.classList.remove('d-none', 'force-visible');
            } else {
                content.style.display = 'none';
                content.style.visibility = '';
                content.classList.add('d-none');
                content.classList.remove('force-visible');
            }
        });

        // Update content visibility - SEO TAB
        const seoLanguageContents = document.querySelectorAll('.seo-language-content');
        seoLanguageContents.forEach(content => {
            if (content.getAttribute('data-language') === language) {
                content.style.display = 'block';
                content.style.visibility = '';
                content.classList.remove('d-none', 'force-visible');
            } else {
                content.style.display = 'none';
                content.style.visibility = '';
                content.classList.add('d-none');
                content.classList.remove('force-visible');
            }
        });
        
        // Update language badge
        const nativeName = document.querySelector(`[data-language="${language}"]`)?.getAttribute('data-native-name');
        const languageBadge = document.getElementById('languageBadge');
        if (languageBadge && nativeName) {
            const badgeContent = languageBadge.querySelector('.nav-link');
            if (badgeContent) {
                badgeContent.innerHTML = `<i class="fas fa-language me-2"></i>${nativeName}<i class="fas fa-chevron-down ms-2"></i>`;
            }
        }
    }
};

// ===== GLOBAL LOADING BAR SYSTEM =====
document.addEventListener('DOMContentLoaded', function() {
    const loadingBar = document.getElementById('global-loading-bar');
    const progressBar = document.getElementById('loading-progress');
    
    if (!loadingBar || !progressBar) return;
    
    // Loading bar controller
    const loader = {
        show: function() {
            loadingBar.style.opacity = '1';
            progressBar.style.width = '10%';
        },
        
        setValue: function(value) {
            progressBar.style.width = (value * 100) + '%';
        },
        
        hide: function() {
            this.setValue(1);
            setTimeout(() => {
                loadingBar.style.opacity = '0';
                setTimeout(() => {
                    progressBar.style.width = '0%';
                }, 300);
            }, 200);
        }
    };
    
    // Show loading bar - MODAL SAFE
    function showLoadingBar() {
        // 🚫 MODAL AÇIKKEN LOADING BAR GÖSTERME
        const activeModals = document.querySelectorAll('.modal.show, .modal[style*="display: block"]');
        if (activeModals.length > 0) {
            console.log('🚫 MANAGE.JS MODAL AÇIK: Loading bar iptal edildi');
            return;
        }

        loader.show();
        setTimeout(() => loader.setValue(0.9), 200);
        
        // AUTO-HIDE: 2 saniye sonra otomatik gizle (modal safe)
        setTimeout(() => {
            console.log('🚨 MANAGE.JS AUTO-HIDE: Loading bar otomatik gizleniyor (2 saniye)');
            hideLoadingBar();
        }, 2000);
    }
    
    // Hide loading bar
    function hideLoadingBar() {
        loader.hide();
    }
    
    // Attach loading to links
    function attachLoadingToLinks() {
        const links = document.querySelectorAll('a[href]:not([href^="#"]):not([href^="javascript:"]):not([href^="mailto:"]):not([href^="tel:"]):not([data-no-loading])');
        
        links.forEach(link => {
            if (link.dataset.loadingAttached) return;
            link.dataset.loadingAttached = 'true';
            
            link.addEventListener('click', function(e) {
                if (this.hostname !== window.location.hostname) return;
                if (this.getAttribute('data-bs-toggle')) return;
                if (this.getAttribute('data-bs-target')) return;
                if (this.getAttribute('data-bs-dismiss')) return;
                
                // Modal içindeki linkler için loading bar gösterme
                if (this.closest('.modal')) {
                    console.log('🚫 MANAGE.JS MODAL İÇİ LINK: Loading bar iptal edildi');
                    return;
                }
                
                showLoadingBar();
            });
        });
        
        // Wire:click elements
        const wireElements = document.querySelectorAll('[wire\\:click]');
        wireElements.forEach(element => {
            if (element.dataset.wireLoadingAttached) return;
            element.dataset.wireLoadingAttached = 'true';

            element.addEventListener('click', function(e) {
                // Modal içindeki wire:click için loading bar gösterme
                if (this.closest('.modal')) {
                    console.log('🚫 MANAGE.JS MODAL İÇİ WIRE:CLICK: Loading bar iptal edildi');
                    return;
                }

                // Language switcher için loading bar gösterme
                if (this.classList.contains('language-switch-btn')) {
                    console.log('🚫 MANAGE.JS LANGUAGE SWITCH: Loading bar iptal edildi');
                    return;
                }

                // AI SEO butonları için loading bar gösterme (kendi loading sistemleri var)
                if (this.classList.contains('ai-seo-comprehensive-btn') ||
                    this.classList.contains('ai-seo-recommendations-btn')) {
                    console.log('🚫 MANAGE.JS AI SEO BUTTON: Loading bar iptal edildi');
                    return;
                }

                showLoadingBar();
            });
        });
    }
    
    // Initialize
    window.addEventListener('load', hideLoadingBar);
    
    // ULTRA AGGRESSIVE FIX: 2 saniye sonra zorla gizle
    setTimeout(() => {
        console.log('🚨 MANAGE.JS ULTRA AGGRESSIVE: Loading bar zorla gizleniyor');
        if (loadingBar) {
            loadingBar.style.display = 'none';
            loadingBar.style.opacity = '0';
            progressBar.style.width = '0%';
            console.log('✅ MANAGE.JS: Loading bar zorla gizlendi');
        }
    }, 2000);
    
    // BACKUP FIX: 1 saniye sonra da kontrol et
    setTimeout(() => {
        if (loadingBar && (loadingBar.style.display !== 'none' || loadingBar.style.opacity !== '0')) {
            console.log('🚨 MANAGE.JS BACKUP FIX: Loading bar hala görünür, zorla gizleniyor');
            loadingBar.style.display = 'none';
            loadingBar.style.opacity = '0';
            progressBar.style.width = '0%';
        }
    }, 1000);
    
    attachLoadingToLinks();
    
    // Livewire integration with selective loading bar
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('message.sent', (message) => {
            // Language switching ve AI işlemleri için loading bar gösterme
            const payload = message?.payload || {};
            const method = payload?.method;

            // Language switching işlemlerini tespit et
            if (method === 'switchLanguage') {
                console.log('🚫 LIVEWIRE LANGUAGE SWITCH: Loading bar iptal edildi');
                return;
            }

            // AI işlemleri için loading bar gösterme (kendi overlayları var)
            if (method && (method.includes('ai') || method.includes('seo'))) {
                console.log('🚫 LIVEWIRE AI/SEO: Loading bar iptal edildi');
                return;
            }

            showLoadingBar();
        });

        Livewire.hook('message.processed', (message) => {
            const payload = message?.message?.payload || {};
            const method = payload?.method;

            // Aynı kontroller message.processed için de
            if (method === 'switchLanguage' ||
                (method && (method.includes('ai') || method.includes('seo')))) {
                return; // Loading bar zaten gösterilmedi, gizlemeye gerek yok
            }

            hideLoadingBar();
        });
    }
    
    // Form submissions
    document.addEventListener('submit', function(e) {
        if (e.target.tagName === 'FORM') {
            showLoadingBar();
        }
    });
    
    // Browser navigation
    window.addEventListener('beforeunload', function() {
        showLoadingBar();
    });
    
    // Reattach on DOM changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length > 0) {
                setTimeout(attachLoadingToLinks, 100);
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// ===== MANAGE PAGE SPECIFIC INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('/manage')) {
        MultiLangFormSwitcher.init();
    }
});

// ===== LIVEWIRE UPDATE HANDLERS =====
document.addEventListener('livewire:updated', function() {
    if (window.location.pathname.includes('/manage')) {
        console.log('🔄 LIVEWIRE UPDATE TETİKLENDİ');

        // 🚨 KRITIK: Mevcut tab ve dil durumunu kaydet
        const currentActiveTab = $('.nav-tabs .nav-link.active').attr('href');
        const currentActiveLanguage = $('.language-switch-btn.text-primary').data('language');

        console.log('💾 Livewire update öncesi durum:', {
            tab: currentActiveTab,
            language: currentActiveLanguage
        });

        // 📋 TAB STATE'İ KAYDET
        const storageKey = 'page_active_tab';
        if (currentActiveTab) {
            localStorage.setItem(storageKey, currentActiveTab);
            console.log('💾 Tab state localStorage\'a kaydedildi:', currentActiveTab);
        }

        // 🔥 KRİTİK: Aktif dili de window.tempSavedLanguage'e kaydet
        if (currentActiveLanguage) {
            window.tempSavedLanguage = currentActiveLanguage;
            console.log('💾 Aktif dil window.tempSavedLanguage\'e kaydedildi:', currentActiveLanguage);
        }

        // INSTANT: Core systems restore - NO setTimeout for rapid saves
        // 🔄 CORE SYSTEMS RESTORE
        setupSeoCharacterCounters();
        setupSeoEnterPrevention();
        MultiLangFormSwitcher.init();

        // 🔄 Language switching sistemini yeniden kur
        setupLanguageSwitching();

        // 🎯 UNIFIED TAB RESTORE SYSTEM
        console.log('🔄 Tab restore sistemi başlatılıyor...');

        // Method 1: Global tab restore function
        if (typeof window.forceTabRestore === 'function') {
            console.log('✅ Global forceTabRestore çağrılıyor');
            window.forceTabRestore();
        } else {
                // Method 2: Manual tab restore
                console.log('🔄 Manual tab restore başlatılıyor');
                const savedTab = localStorage.getItem(storageKey);
                if (savedTab) {
                    console.log('📋 Kaydedilmiş tab bulundu:', savedTab);

                    const targetTab = document.querySelector(`[href="${savedTab}"]`);
                    const targetPane = document.getElementById(savedTab.replace('#', ''));

                    if (targetTab && targetPane) {
                        // Tüm tab'ları deaktif et
                        document.querySelectorAll('.nav-link').forEach(tab => {
                            tab.classList.remove('active');
                            tab.setAttribute('aria-selected', 'false');
                        });
                        document.querySelectorAll('.tab-pane').forEach(pane => {
                            pane.classList.remove('show', 'active');
                        });

                        // Hedef tab'ı aktif et
                        targetTab.classList.add('active');
                        targetTab.setAttribute('aria-selected', 'true');
                        targetPane.classList.add('show', 'active');

                        console.log('✅ Manual tab restore tamamlandı:', savedTab);
                    }
            }
        }

        // 🎯 NURULLAH'IN YENİ KURALI: Livewire update sonrası geçici state restore
        console.log('🔍 RESTORE CHECK:', {
            hasTempLanguage: !!window.tempSavedLanguage,
            hasTempTab: !!window.tempSavedTab,
            tempLanguage: window.tempSavedLanguage,
            tempTab: window.tempSavedTab,
            currentLanguage: window.currentLanguage
        });

        if (window.tempSavedLanguage || window.tempSavedTab) {
                console.log('✅ GEÇİCİ state bulundu - restore başlıyor...');
                console.log('  - Kaydedilen dil:', window.tempSavedLanguage || 'yok');
                console.log('  - Kaydedilen tab:', window.tempSavedTab || 'yok');
                
                // Önce dil restore et (eğer varsa)
                if (window.tempSavedLanguage) {
                    console.log('🔍 DİL RESTORE BAŞLIYOR:', window.tempSavedLanguage);
                    
                    const targetLangBtn = $(`.language-switch-btn[data-language="${window.tempSavedLanguage}"]`);
                    console.log('🔍 Hedef dil button bulundu mu?', targetLangBtn.length > 0);
                    console.log('🔍 Hedef button element:', targetLangBtn[0]);
                    
                    if (targetLangBtn.length) {
                        console.log('🚨 DİL BUTTON CLICK TETİKLENİYOR:', window.tempSavedLanguage);
                        targetLangBtn.click();
                        console.log('✅ Geçici dil restore edildi:', window.tempSavedLanguage);

                        // INSTANT: Force dil içerik görünürlüğünü güncelle - NO setTimeout
                        if (window.switchLanguageContent) {
                            console.log('🔥 FORCE switchLanguageContent çağrılıyor:', window.tempSavedLanguage);
                            window.switchLanguageContent(window.tempSavedLanguage);
                        }
                    } else {
                        console.log('❌ Hedef dil button bulunamadı:', window.tempSavedLanguage);
                        // Tüm mevcut button'ları listele
                        const allLangButtons = $('.language-switch-btn');
                        console.log('🔍 Mevcut tüm dil button\'ları:');
                        allLangButtons.each(function(index) {
                            console.log(`  ${index}: ${$(this).data('language')} - class: ${this.className}`);
                        });
                    }
                }
                
                // Sonra tab restore et (eğer varsa)
                if (window.tempSavedTab) {
                    console.log('🔍 TAB RESTORE BAŞLIYOR:', window.tempSavedTab);
                    
                    setTimeout(function() {
                        const targetTabElement = document.querySelector(`[href="${window.tempSavedTab}"]`);
                        console.log('🔍 Hedef tab element bulundu mu?', !!targetTabElement);
                        console.log('🔍 Hedef tab element:', targetTabElement);
                        
                        if (targetTabElement) {
                            const tabText = targetTabElement.textContent.trim();
                            console.log('🔍 Tab metni:', tabText);
                            
                            // Mevcut durum kontrolü
                            const currentActiveTab = document.querySelector('.nav-tabs .nav-link.active');
                            console.log('🔍 Şu anki aktif tab:', currentActiveTab);
                            console.log('🔍 Şu anki aktif tab href:', currentActiveTab?.getAttribute('href'));
                            
                            console.log('🚨 TAB MANUEL AKTİVASYON BAŞLIYOR');
                            
                            // Manuel tab aktivasyon
                            document.querySelectorAll('.nav-link').forEach(tab => {
                                tab.classList.remove('active');
                                tab.setAttribute('aria-selected', 'false');
                                console.log('🔧 Tab deaktif edildi:', tab.getAttribute('href'));
                            });
                            
                            document.querySelectorAll('.tab-pane').forEach(pane => {
                                pane.classList.remove('show', 'active');
                                console.log('🔧 Pane deaktif edildi:', pane.id);
                            });
                            
                            targetTabElement.classList.add('active');
                            targetTabElement.setAttribute('aria-selected', 'true');
                            console.log('🔧 Hedef tab aktif edildi:', window.tempSavedTab);
                            
                            const tabId = window.tempSavedTab.replace('#', '');
                            const targetPane = document.getElementById(tabId);
                            console.log('🔍 Hedef pane bulundu mu?', !!targetPane);
                            console.log('🔍 Hedef pane ID:', tabId);
                            
                            if (targetPane) {
                                targetPane.classList.add('show', 'active');
                                console.log('🔧 Hedef pane aktif edildi:', tabId);
                            } else {
                                console.log('❌ Hedef pane bulunamadı:', tabId);
                                // Tüm mevcut pane'leri listele
                                const allPanes = document.querySelectorAll('.tab-pane');
                                console.log('🔍 Mevcut tüm pane\'ler:');
                                allPanes.forEach((pane, index) => {
                                    console.log(`  ${index}: ID="${pane.id}" - class="${pane.className}"`);
                                });
                            }
                            
                            // Son kontrol
                            setTimeout(function() {
                                const finalActiveTab = document.querySelector('.nav-tabs .nav-link.active');
                                const finalActivePane = document.querySelector('.tab-pane.active');
                                console.log('🔍 Final aktif tab:', finalActiveTab?.getAttribute('href'));
                                console.log('🔍 Final aktif pane:', finalActivePane?.id);
                                console.log('🔍 Tab restore başarılı mı?', 
                                    finalActiveTab?.getAttribute('href') === window.tempSavedTab ? '✅' : '❌');
                            }, 50);
                            
                            console.log('✅ Geçici tab restore edildi:', window.tempSavedTab, '-', tabText);
                        } else {
                            console.log('❌ Hedef tab element bulunamadı:', window.tempSavedTab);
                            // Tüm mevcut tab'ları listele
                            const allTabs = document.querySelectorAll('.nav-link');
                            console.log('🔍 Mevcut tüm tab\'lar:');
                            allTabs.forEach((tab, index) => {
                                console.log(`  ${index}: href="${tab.getAttribute('href')}" - text="${tab.textContent.trim()}"`);
                            });
                        }
                    }, 200); // Tab için ek bekle
                }
                
                console.log('🎯 GEÇİCİ state restore tamamlandı - veriler korunacak');
                
                // 🚨 IMPORTANT: window.tempSavedLanguage ve window.tempSavedTab'ı SİLME
                // Kaydet ve Devam Et ile çalışmaya devam ediyoruz
            } else {
                // State yoksa da mevcut dili force restore et
                console.log('⚠️ Temp state YOK - fallback restore başlıyor');
                console.log('  - window.currentLanguage:', window.currentLanguage);
                console.log('  - window.switchLanguageContent var mı:', !!window.switchLanguageContent);

                // Eğer hiç dil yoksa, button'dan al
                if (!window.currentLanguage) {
                    const activeLangBtn = $('.language-switch-btn.text-primary');
                    if (activeLangBtn.length) {
                        window.currentLanguage = activeLangBtn.data('language');
                        console.log('  - Button\'dan dil alındı:', window.currentLanguage);
                    }
                }

                // INSTANT: Fallback restore - NO setTimeout
                if (window.currentLanguage && window.switchLanguageContent) {
                    console.log('🔥 FALLBACK RESTORE ÇALIŞIYOR:', window.currentLanguage);
                    window.switchLanguageContent(window.currentLanguage);
                    console.log('✅ FALLBACK RESTORE TAMAMLANDI');
                } else {
                    console.error('❌ FALLBACK RESTORE BAŞARISIZ - Dil veya fonksiyon eksik!');
                }
            }
    }
});

// ===== GLOBAL TAB MANAGER =====
// TabManager is defined in main.js - using that one instead

// ===== SLUG NORMALIZATION SYSTEM =====
function setupSlugNormalization() {
    // JavaScript slug normalization function (matches PHP SlugHelper)
    function normalizeSlug(slug) {
        slug = slug.toLowerCase().trim();
        
        // Multi-language character mapping - extensible for any language
        const characterMaps = {
            // Turkish characters
            'ç': 'c', 'ğ': 'g', 'ı': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u',
            'Ç': 'c', 'Ğ': 'g', 'I': 'i', 'İ': 'i', 'Ö': 'o', 'Ş': 's', 'Ü': 'u',
            
            // Arabic characters
            'ا': 'a', 'ب': 'b', 'ت': 't', 'ث': 'th', 'ج': 'j', 'ح': 'h', 'خ': 'kh',
            'د': 'd', 'ذ': 'dh', 'ر': 'r', 'ز': 'z', 'س': 's', 'ش': 'sh', 'ص': 's',
            'ض': 'd', 'ط': 't', 'ظ': 'z', 'ع': 'a', 'غ': 'gh', 'ف': 'f', 'ق': 'q',
            'ك': 'k', 'ل': 'l', 'م': 'm', 'ن': 'n', 'ه': 'h', 'و': 'w', 'ي': 'y',
            'ى': 'a', 'ة': 'h', 'ء': 'a', 'أ': 'a', 'إ': 'i', 'آ': 'a', 'ؤ': 'w',
            'ئ': 'y', 'ً': '', 'ٌ': '', 'ٍ': '', 'َ': '', 'ُ': '', 'ِ': '', 'ّ': '', 'ْ': '',
            
            // French characters
            'à': 'a', 'á': 'a', 'â': 'a', 'ã': 'a', 'ä': 'a', 'å': 'a',
            'è': 'e', 'é': 'e', 'ê': 'e', 'ë': 'e',
            'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i',
            'ò': 'o', 'ó': 'o', 'ô': 'o', 'õ': 'o', 'ø': 'o',
            'ù': 'u', 'ú': 'u', 'û': 'u',
            'ý': 'y', 'ÿ': 'y',
            'ñ': 'n', 'ç': 'c',
            
            // German characters
            'ä': 'ae', 'ö': 'oe', 'ü': 'ue', 'ß': 'ss',
            'Ä': 'ae', 'Ö': 'oe', 'Ü': 'ue',
            
            // Spanish characters
            'ñ': 'n', 'Ñ': 'n',
            
            // Portuguese characters
            'ã': 'a', 'õ': 'o', 'ç': 'c',
            
            // Italian characters - mostly covered by French
            
            // Russian (Cyrillic) characters
            'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo',
            'ж': 'zh', 'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm',
            'н': 'n', 'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
            'ф': 'f', 'х': 'h', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'sch',
            'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya',
            
            // Greek characters
            'α': 'a', 'β': 'b', 'γ': 'g', 'δ': 'd', 'ε': 'e', 'ζ': 'z', 'η': 'h',
            'θ': 'th', 'ι': 'i', 'κ': 'k', 'λ': 'l', 'μ': 'm', 'ν': 'n', 'ξ': 'x',
            'ο': 'o', 'π': 'p', 'ρ': 'r', 'σ': 's', 'τ': 't', 'υ': 'y', 'φ': 'f',
            'χ': 'ch', 'ψ': 'ps', 'ω': 'w',
            
            // Polish characters
            'ą': 'a', 'ć': 'c', 'ę': 'e', 'ł': 'l', 'ń': 'n', 'ó': 'o', 'ś': 's',
            'ź': 'z', 'ż': 'z',
            
            // Czech characters
            'á': 'a', 'č': 'c', 'ď': 'd', 'é': 'e', 'ě': 'e', 'í': 'i', 'ň': 'n',
            'ó': 'o', 'ř': 'r', 'š': 's', 'ť': 't', 'ú': 'u', 'ů': 'u', 'ý': 'y',
            'ž': 'z',
            
            // Hungarian characters
            'á': 'a', 'é': 'e', 'í': 'i', 'ó': 'o', 'ö': 'o', 'ő': 'o', 'ú': 'u',
            'ü': 'u', 'ű': 'u',
            
            // Japanese romanization (basic hiragana/katakana)
            'あ': 'a', 'い': 'i', 'う': 'u', 'え': 'e', 'お': 'o',
            'か': 'ka', 'き': 'ki', 'く': 'ku', 'け': 'ke', 'こ': 'ko',
            'さ': 'sa', 'し': 'shi', 'す': 'su', 'せ': 'se', 'そ': 'so',
            'た': 'ta', 'ち': 'chi', 'つ': 'tsu', 'て': 'te', 'と': 'to',
            'な': 'na', 'に': 'ni', 'ぬ': 'nu', 'ね': 'ne', 'の': 'no',
            'は': 'ha', 'ひ': 'hi', 'ふ': 'fu', 'へ': 'he', 'ほ': 'ho',
            'ま': 'ma', 'み': 'mi', 'む': 'mu', 'め': 'me', 'も': 'mo',
            'や': 'ya', 'ゆ': 'yu', 'よ': 'yo',
            'ら': 'ra', 'り': 'ri', 'る': 'ru', 'れ': 're', 'ろ': 'ro',
            'わ': 'wa', 'を': 'wo', 'ん': 'n'
        };
        
        // Apply all character mappings
        for (let [search, replace] of Object.entries(characterMaps)) {
            slug = slug.replaceAll(search, replace);
        }
        
        // Replace spaces with hyphens and clean up
        slug = slug.replace(/\s+/g, '-');
        slug = slug.replace(/[^a-z0-9\-]/g, '');
        slug = slug.replace(/\-+/g, '-');
        slug = slug.replace(/^-+|-+$/g, '');
        
        return slug;
    }
    
    // Find all slug inputs and attach normalization
    setTimeout(function() {
        const languages = ['tr', 'en', 'ar'];
        
        languages.forEach(function(lang) {
            // Find slug input by wire:model attribute
            const allInputs = document.querySelectorAll('input');
            let slugInput = null;
            for (let input of allInputs) {
                if (input.getAttribute('wire:model') === `multiLangInputs.${lang}.slug`) {
                    slugInput = input;
                    break;
                }
            }
            
            if (slugInput && !slugInput.dataset.slugNormalized) {
                slugInput.dataset.slugNormalized = 'true';
                
                slugInput.addEventListener('input', function(e) {
                    const originalValue = e.target.value;
                    let normalizedValue = normalizeSlug(originalValue);
                    
                    // OTOMATIK DÜZELTME: Eğer normalize edilmiş slug boşsa title'dan oluştur
                    if (!normalizedValue || normalizedValue.trim() === '') {
                        const titleInput = document.querySelector(`input[wire\\:model="multiLangInputs.${lang}.title"]`);
                        if (titleInput && titleInput.value.trim()) {
                            normalizedValue = normalizeSlug(titleInput.value);
                            console.log(`🔄 ${lang} slug boş - title'dan oluşturuldu: "${titleInput.value}" → "${normalizedValue}"`);
                        }
                    }
                    
                    if (originalValue !== normalizedValue) {
                        e.target.value = normalizedValue;
                        
                        // Trigger Livewire update
                        e.target.dispatchEvent(new Event('input', { bubbles: true }));
                        
                        console.log(`🔧 ${lang} slug normalized: "${originalValue}" → "${normalizedValue}"`);
                    }
                });
                
                // console.log(`✅ Slug normalization enabled for ${lang}`);
            }
        });
    }, 500); // Wait for DOM to be ready
}

// ===== SEO ENTER KEY PREVENTION SYSTEM =====
function setupSeoEnterPrevention() {
    console.log('🚫 SEO Enter tuşu engelleme sistemi kuruluyor...');
    
    // Enter tuşunu engelleyecek CSS sınıfını hedef al
    const seoInputs = document.querySelectorAll('.seo-no-enter');
    
    console.log('🔍 Bulunan seo-no-enter input sayısı:', seoInputs.length);

    seoInputs.forEach((input, index) => {
        const wireModel = input.getAttribute('wire:model') || input.getAttribute('wire:model.live') || input.getAttribute('wire:model.defer');
        const currentValue = input.value || input.textContent || '';

        console.log(`  Input ${index}:`, {
            tag: input.tagName,
            name: input.name || 'yok',
            placeholder: input.placeholder || 'yok',
            wireModel: wireModel || '❌ YOK',
            value: currentValue ? currentValue.substring(0, 50) + '...' : '🔴 BOŞ',
            hasValue: !!currentValue,
            id: input.id || 'no-id'
        });

        // Enter tuşu event listener'ı ekle
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('🚫 Enter tuşu engellendi - SEO alanında:', input.name || input.placeholder);
                
                // Kullanıcıya görsel geri bildirim
                const originalBorder = input.style.border;
                input.style.border = '2px solid #dc3545';
                input.style.backgroundColor = '#ffeaea';
                
                // Tooltip veya uyarı göster
                showSeoEnterWarning(input);
                
                // 1 saniye sonra görsel efekti kaldır
                setTimeout(() => {
                    input.style.border = originalBorder;
                    input.style.backgroundColor = '';
                }, 1000);
                
                return false;
            }
        });
        
        // Paste event'i için de temizlik
        input.addEventListener('paste', function(e) {
            setTimeout(() => {
                const value = input.value;
                if (value.includes('\n') || value.includes('\r')) {
                    const cleanValue = value.replace(/[\r\n]/g, ' ').replace(/\s+/g, ' ').trim();
                    input.value = cleanValue;
                    
                    console.log('🧹 Yapıştırılan metindeki Enter karakterleri temizlendi');
                    showSeoEnterWarning(input, 'Yapıştırılan metindeki satır sonları kaldırıldı');
                    
                    // Livewire'a güncellemeyi bildir
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }, 10);
        });
    });
    
    console.log(`✅ ${seoInputs.length} SEO input için Enter tuşu engelleme aktif`);
}

// SEO Enter uyarısı göster (sessiz)
function showSeoEnterWarning(input, customMessage = null) {
    // Sadece konsola log - kullanıcıya görsel uyarı yok
    console.log('🚫 Enter tuşu engellendi - SEO alanında:', input.name || input.placeholder);
}

// ===== GLOBAL EXPORTS =====
// window.TabManager = TabManager; // Already exported from main.js
window.MultiLangFormSwitcher = MultiLangFormSwitcher;
window.setupLanguageSwitching = setupLanguageSwitching;
window.setupSaveAndContinueSystem = setupSaveAndContinueSystem;
window.setupSeoCharacterCounters = setupSeoCharacterCounters;
window.setupSlugNormalization = setupSlugNormalization;
window.setupSeoEnterPrevention = setupSeoEnterPrevention;

// Helper functions for loading data
function loadDataForLanguage(language) {
    console.log('📊 Tüm veri yükleniyor:', language);
    // This function is handled by Livewire system
    // Language contents are already managed by show/hide
}

function loadDataForLanguageExceptCode(language) {
    console.log('📊 Veri yükleniyor (Code HARİÇ):', language);
    // SEO tab preserves Code tab
    // Language contents are managed by show/hide, Code tab is not affected
}

// DİL İÇERİK GÖRÜNÜRLÜĞÜ FONKSİYONU - Global olarak expose ediyoruz
window.switchLanguageContent = function(language) {
    console.log('🎯 DİL İÇERİK DEĞİŞTİRME:', language);

    // TEMEL BİLGİLER TAB - language-content divleri
    document.querySelectorAll('.language-content').forEach(content => {
        const contentLang = content.getAttribute('data-language');
        if (contentLang === language) {
            // GÖRÜNÜR YAP - Tüm blokları temizle
            content.style.display = 'block';
            content.style.visibility = 'visible';
            content.style.opacity = '1';
            content.style.height = 'auto';
            content.classList.remove('d-none');
            console.log('👁️ TEMEL BİLGİLER görünür:', contentLang);
        } else {
            // GİZLE - Agresif gizleme
            content.style.display = 'none';
            content.style.visibility = 'hidden';
            content.style.opacity = '0';
            content.style.height = '0';
            content.classList.add('d-none');
            console.log('👻 TEMEL BİLGİLER gizli:', contentLang);
        }
    });

    // SEO TAB - seo-language-content divleri
    document.querySelectorAll('.seo-language-content').forEach(content => {
        const contentLang = content.getAttribute('data-language');
        if (contentLang === language) {
            // GÖRÜNÜR YAP - Tüm blokları temizle
            content.style.display = 'block';
            content.style.visibility = 'visible';
            content.style.opacity = '1';
            content.style.height = 'auto';
            content.classList.remove('d-none');
            console.log('👁️ SEO CONTENT görünür:', contentLang);
        } else {
            // GİZLE - Agresif gizleme
            content.style.display = 'none';
            content.style.visibility = 'hidden';
            content.style.opacity = '0';
            content.style.height = '0';
            content.classList.add('d-none');
            console.log('👻 SEO CONTENT gizli:', contentLang);
        }
    });

    // DİL BUTONU GÜNCELLEMESİ
    document.querySelectorAll('.language-switch-btn').forEach(btn => {
        const btnLang = btn.getAttribute('data-language');
        if (btnLang === language) {
            btn.classList.add('text-primary');
            btn.classList.remove('text-muted');
            btn.style.borderBottom = '2px solid var(--primary-color) !important';
        } else {
            btn.classList.remove('text-primary');
            btn.classList.add('text-muted');
            btn.style.borderBottom = '2px solid transparent';
        }
    });

    console.log('✅ Dil içerik görünürlük güncellendi:', language);
};

// 🚀 PURE CLIENT-SIDE LANGUAGE SWITCHING (NO LIVEWIRE)
window.clientSideLanguageSwitch = function(language) {
    console.log('🎯 PURE CLIENT-SIDE Language Switch:', language);

    // Dil içerik görünürlüğünü güncelle
    switchLanguageContent(language);

    // Global language variable'ı güncelle
    window.currentLanguage = language;

    // Session'a da kaydet (AJAX ile - background)
    fetch('/admin/page/manage/update-language-session', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ language: language })
    }).catch(e => console.warn('⚠️ Session update failed:', e));

    console.log('✅ Pure client-side dil değiştirme tamamlandı');
};

// 🚀 CLIENT-SIDE LANGUAGE SWITCHING EVENT LISTENER (Fallback)
document.addEventListener('DOMContentLoaded', function() {
    // Wait for Livewire to be ready
    document.addEventListener('livewire:initialized', () => {
        console.log('🎯 Livewire initialized - Client-side language switch listener hazır');

        Livewire.on('client-language-switched', (event) => {
            console.log('🎯 Client-side language switch event alındı:', event);

            const language = event.language || event[0]?.language;
            const oldLanguage = event.oldLanguage || event[0]?.oldLanguage;

            if (language) {
                console.log('🔄 Client-side dil değiştirme başlıyor:', oldLanguage, '→', language);

                // Mevcut jQuery switchLanguage fonksiyonunu kullan (render yok)
                switchLanguageContent(language);

                // Global language variable'ı güncelle
                window.currentLanguage = language;

                console.log('✅ Client-side dil değiştirme tamamlandı');
            } else {
                console.warn('⚠️ Language parametresi bulunamadı:', event);
            }
        });
    });
});