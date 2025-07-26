// Page Management System - Consolidated JavaScript
// Combines all manage-related JavaScript code from component, layout and main.js

// Global variables
window.currentPageId = null;
window.currentLanguage = 'tr';
window.allLanguagesSeoData = {};

// ===== SYSTEM INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Page Manage sayfası başlatılıyor...');
    
    // Initialize core systems
    setupLanguageSwitching();
    setupSaveAndContinueSystem();
    setupSeoCharacterCounters();
    initializeTabSystem();
    setupSlugNormalization();
    
    console.log('✅ Page Manage sayfası hazır!');
});

// ===== LANGUAGE SWITCHING SYSTEM =====
function setupLanguageSwitching() {
    $('.language-switch-btn').on('click', function() {
        const language = $(this).data('language');
        const nativeName = $(this).data('native-name');
        
        console.log('🌍 Dil değiştirildi:', language);
        
        // Update button states
        $('.language-switch-btn').removeClass('text-primary').addClass('text-muted')
            .css('border-bottom', '2px solid transparent');
        
        $(this).removeClass('text-muted').addClass('text-primary')
            .css('border-bottom', '2px solid var(--primary-color)');
        
        // Update language badge
        const languageBadge = document.getElementById('languageBadge');
        if (languageBadge && nativeName) {
            const badgeContent = languageBadge.querySelector('.nav-link');
            if (badgeContent) {
                badgeContent.innerHTML = `<i class="fas fa-language me-2"></i>${nativeName}<i class="fas fa-chevron-down ms-2"></i>`;
            }
        }
        
        // Check active tab - Code tab is preserved
        const activeTabElement = $('.nav-tabs .nav-link.active');
        const activeTab = activeTabElement.attr('href');
        
        if (activeTab && activeTab === '#2') {
            console.log('🎯 Code tab aktif - dil değişikliği engellendi');
        } else {
            console.log('🎯 Normal dil değişikliği yapılıyor');
        }
        
        // Update language content visibility
        $('.language-content').hide();
        $(`.language-content[data-language="${language}"]`).show();
        
        // Update global variables
        window.currentLanguage = language;
    });
}

// ===== SAVE AND CONTINUE SYSTEM =====
function setupSaveAndContinueSystem() {
    document.addEventListener('click', function(e) {
        const saveButton = e.target.closest('.save-button');
        
        if (saveButton) {
            console.log('💾 Save button tıklandı');
            
            // Get active language
            const activeLanguageBtn = document.querySelector('.language-switch-btn.text-primary');
            const currentLang = activeLanguageBtn ? activeLanguageBtn.dataset.language : window.currentLanguage;
            
            // Detect "Save and Continue" button
            const wireClick = saveButton.getAttribute('wire:click');
            const isContinueButton = wireClick && wireClick.includes('save(false, false)');
            
            if (isContinueButton && currentLang) {
                localStorage.setItem('page_active_language', currentLang);
                console.log('🎯 Kaydet ve Devam Et - dil korunacak:', currentLang);
            } else {
                localStorage.removeItem('page_active_language');
                console.log('📤 Normal Kaydet - dil state temizlendi');
            }
        }
    });
    
    // Restore language on page load
    const savedLanguage = localStorage.getItem('page_active_language');
    if (savedLanguage) {
        console.log('🔄 Kaydedilen dil restore ediliyor:', savedLanguage);
        
        setTimeout(function() {
            const targetLangBtn = $(`.language-switch-btn[data-language="${savedLanguage}"]`);
            if (targetLangBtn.length) {
                targetLangBtn.click();
                console.log('✅ Dil restore tamamlandı:', savedLanguage);
            }
            localStorage.removeItem('page_active_language');
        }, 300);
    }
}

// ===== SEO CHARACTER COUNTERS =====
function setupSeoCharacterCounters() {
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
            console.warn('⚠️ Language switch buttons bulunamadı');
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
        
        // Update content visibility
        const languageContents = document.querySelectorAll('.language-content');
        languageContents.forEach(content => {
            if (content.getAttribute('data-language') === language) {
                content.style.display = 'block';
            } else {
                content.style.display = 'none';
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
                progressBar.style.width = '0%';
            }, 100);
        }
    };
    
    // Show loading bar
    function showLoadingBar() {
        loader.show();
        setTimeout(() => loader.setValue(0.9), 200);
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
                
                showLoadingBar();
            });
        });
        
        // Wire:click elements
        const wireElements = document.querySelectorAll('[wire\\:click]');
        wireElements.forEach(element => {
            if (element.dataset.wireLoadingAttached) return;
            element.dataset.wireLoadingAttached = 'true';
            
            element.addEventListener('click', function(e) {
                showLoadingBar();
            });
        });
    }
    
    // Initialize
    window.addEventListener('load', hideLoadingBar);
    attachLoadingToLinks();
    
    // Livewire integration
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('message.sent', () => {
            showLoadingBar();
        });
        
        Livewire.hook('message.processed', () => {
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
        setTimeout(function() {
            setupSeoCharacterCounters();
            MultiLangFormSwitcher.init();
        }, 100);
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
                
                console.log(`✅ Slug normalization enabled for ${lang}`);
            }
        });
    }, 500); // Wait for DOM to be ready
}

// ===== GLOBAL EXPORTS =====
// window.TabManager = TabManager; // Already exported from main.js
window.MultiLangFormSwitcher = MultiLangFormSwitcher;
window.setupLanguageSwitching = setupLanguageSwitching;
window.setupSaveAndContinueSystem = setupSaveAndContinueSystem;
window.setupSeoCharacterCounters = setupSeoCharacterCounters;
window.setupSlugNormalization = setupSlugNormalization;

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