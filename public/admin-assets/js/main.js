// Turkbil Bee - Admin Main JavaScript
// Minimal version - syntax fix

// SOLID Management System - Global JavaScript
class PageManagementSystem {
    constructor() {
        this.tabManager = null;
        this.seoManager = null;
        this.init();
    }

    init() {
        this.waitForDependencies(() => {
            this.initializeTabSystem();
            // this.initializeSeoSystem(); // Moved to seo-tabs.js
            this.bindGlobalEvents();
        });
    }

    waitForDependencies(callback) {
        if (typeof $ !== 'undefined') {
            $(document).ready(callback);
        } else {
            document.addEventListener('DOMContentLoaded', callback);
        }
    }

    initializeTabSystem() {
        this.tabManager = {
            storageKey: 'adminFormActiveTab',
            
            init(customKey = null) {
                if (customKey) {
                    this.storageKey = customKey;
                }
                this.restoreActiveTab();
                this.bindTabEvents();
            },
            
            restoreActiveTab() {
                const savedTab = localStorage.getItem(this.storageKey);
                if (savedTab) {
                    const tabElement = document.querySelector(`[href="${savedTab}"]`);
                    if (tabElement && typeof bootstrap !== 'undefined') {
                        const tab = new bootstrap.Tab(tabElement);
                        tab.show();
                    }
                }
            },
            
            bindTabEvents() {
                const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
                tabLinks.forEach(link => {
                    link.addEventListener('shown.bs.tab', (e) => {
                        localStorage.setItem(this.storageKey, e.target.getAttribute('href'));
                    });
                });
            }
        };
        
        this.tabManager.init();
    }

    // initializeSeoSystem moved to seo-tabs.js

    bindGlobalEvents() {
        // Tab değişikliklerini dinle
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-bs-toggle="tab"]')) {
                setTimeout(() => {
                    // SEO manager now in seo-tabs.js
                }, 100);
            }
        });
    }
}

// Tab Manager - Global Export
const TabManager = {
    storageKey: 'adminFormActiveTab',
    
    init(customKey = null) {
        if (customKey) {
            this.storageKey = customKey;
        }
        this.restoreActiveTab();
        this.bindTabEvents();
    },
    
    restoreActiveTab() {
        const savedTab = localStorage.getItem(this.storageKey);
        if (savedTab) {
            const tabElement = document.querySelector(`[href="${savedTab}"]`);
            if (tabElement && typeof bootstrap !== 'undefined') {
                const tab = new bootstrap.Tab(tabElement);
                tab.show();
            }
        }
    },
    
    bindTabEvents() {
        const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
        tabLinks.forEach(link => {
            link.addEventListener('shown.bs.tab', (e) => {
                localStorage.setItem(this.storageKey, e.target.getAttribute('href'));
            });
        });
    }
};

// Character Counting System - SEO counters moved to seo-tabs.js
function setupCharacterCounting() {
    
    const counters = document.querySelectorAll('.character-counter:not(.seo-character-counter)');
    
    counters.forEach(counter => {
        const fieldName = counter.getAttribute('data-counter-for');
        const limit = parseInt(counter.getAttribute('data-limit'));
        
        if (!fieldName || !limit) return;
        
        const input = document.getElementById(fieldName) || 
                     document.querySelector(`[name="${fieldName}"]`) ||
                     document.querySelector(`[wire\\:model*="${fieldName}"]`);
        
        if (!input) {
            console.warn(`⚠️ Input bulunamadı: ${fieldName}`);
            return;
        }
        
        // Progress bar'ı bul (sadece non-SEO için)
        const progressBar = counter.closest('label')?.querySelector('.progress-bar:not(.seo-progress-bar)') || 
                           counter.closest('.form-text')?.querySelector('.progress-bar:not(.seo-progress-bar)');
        
        // Counter fonksiyonu
        function updateCounter() {
            const currentLength = input.value.length;
            const percentage = (currentLength / limit) * 100;
            
            // Mini loader göster
            counter.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Sayılıyor...';
            
            // 100ms sonra sonucu göster
            setTimeout(() => {
                let colorClass = '';
                let progressColorClass = '';
                
                if (percentage > 90) {
                    colorClass = 'text-danger';
                    progressColorClass = 'bg-danger';
                } else if (percentage > 75) {
                    colorClass = 'text-warning';
                    progressColorClass = 'bg-warning';
                } else {
                    colorClass = 'text-success';
                    progressColorClass = 'bg-success';
                }
                
                counter.className = `character-counter ${colorClass}`;
                counter.innerHTML = `${currentLength}/${limit}`;
                
                // Progress bar güncelle
                if (progressBar) {
                    progressBar.style.width = `${Math.min(100, percentage)}%`;
                    progressBar.className = `progress-bar ${progressColorClass}`;
                }
            }, 100);
        }
        
        // Event listeners
        input.addEventListener('input', updateCounter);
        input.addEventListener('keyup', updateCounter);
        input.addEventListener('change', updateCounter);
        
        // İlk yükleme
        updateCounter();
        
    });
}

// Keyword System moved to seo-tabs.js

// Initialize system
document.addEventListener('DOMContentLoaded', function() {
    window.pageManagement = new PageManagementSystem();
});

// Initialize manage page functions - moved to manage.js
// document.addEventListener('DOMContentLoaded', function() {
//     if (window.location.pathname.includes('/manage')) {
//         setupCharacterCounting();
//         MultiLangFormSwitcher.init();
//     }
// });

// Livewire update handler - moved to manage.js  
// document.addEventListener('livewire:updated', function() {
//     if (window.location.pathname.includes('/manage')) {
//         setTimeout(function() {
//             setupCharacterCounting();
//             MultiLangFormSwitcher.init();
//         }, 100);
//     }
// });

// Multi-Language Form Switcher - moved to manage.js for manage pages
// For non-manage pages, a simplified version will be added here if needed

// ===== GLOBAL SEO WIDGET SYSTEM =====
class GlobalSeoWidget {
    constructor() {
        this.baseUrl = '/admin/seo';
        this.currentModel = null;
        this.currentLanguage = 'tr';
        // SEO cache artık seo-tabs.js'te yönetiliyor
        this.init();
    }

    async init() {
        if (this.isManagePage()) {
            // GlobalSeoWidget context setup (SEO-specific kod seo-tabs.js'te)
            if (window.currentPageId) {
                this.setContext('Page', window.currentPageId);
            }
            
            this.setupEventListeners();
            this.setupKeywordSystem();
            this.setupSlugSystem();
        }
    }

    isManagePage() {
        return window.location.pathname.includes('/manage');
    }

    // Model ve language bilgilerini set et
    setContext(modelType, modelId, language = 'tr') {
        this.currentModel = { type: modelType, id: modelId };
        this.currentLanguage = language;
        window.currentModelType = modelType;
        window.currentModelId = modelId;
    }

    // Bu fonksiyonlar seo-tabs.js'e taşındı - sadece backward compatibility için tutuluyor

    // Form'a SEO verilerini uygula
    applySeoDataToForm(seoData, language) {
        const elements = {
            'seo-title': seoData.seoData?.seo_title || '',
            'seo-description': seoData.seoData?.seo_description || '',
            'seo-keywords-hidden': seoData.seoData?.seo_keywords || '',
            'canonical-url': seoData.seoData?.canonical_url || ''
        };

        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.value = value;
                element.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });

        // Keyword'leri güncelle
        if (seoData.seoData?.seo_keywords) {
            this.updateKeywordDisplay(seoData.seoData.seo_keywords);
        }

        // Livewire component'i güncelle
        if (window.Livewire) {
            window.Livewire.dispatch('seo-field-updated', {
                language: language,
                seoData: seoData.seoData
            });
        }
    }

    // Dil değiştiğinde SEO verilerini güncelle
    async updateSeoDataForLanguage(language) {
        console.log('🔍 updateSeoDataForLanguage çağrıldı - Model:', this.currentModel, 'Language:', language);
        
        if (!this.currentModel) {
            console.error('❌ currentModel yok! SEO güncelleme durdu:', this.currentModel);
            return;
        }
        
        console.log('🎯 SEO verileri güncelleniyor:', language);
        
        const seoData = await this.fetchSeoData(language);
        if (seoData) {
            this.applySeoDataToForm(seoData, language);
        }
    }

    // Keyword sistemi kurulumu
    setupKeywordSystem() {
        const keywordInput = document.getElementById('keyword-input');
        const addKeywordBtn = document.getElementById('add-keyword');
        
        if (!keywordInput || !addKeywordBtn) return;

        // Enter tuşu ile keyword ekleme
        keywordInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const keyword = keywordInput.value.trim();
                if (keyword) {
                    this.addKeyword(keyword);
                    keywordInput.value = '';
                }
            }
        });

        // Button ile keyword ekleme
        addKeywordBtn.addEventListener('click', () => {
            const keyword = keywordInput.value.trim();
            if (keyword) {
                this.addKeyword(keyword);
                keywordInput.value = '';
            }
        });

        // Mevcut keyword'leri göster
        const hiddenInput = document.getElementById('seo-keywords-hidden');
        if (hiddenInput && hiddenInput.value) {
            this.updateKeywordDisplay(hiddenInput.value);
        }
    }

    // Keyword ekle
    addKeyword(keyword) {
        if (!keyword || keyword.trim() === '') return;
        
        const hiddenInput = document.getElementById('seo-keywords-hidden');
        if (!hiddenInput) return;
        
        const currentKeywords = hiddenInput.value.split(',').map(k => k.trim()).filter(k => k !== '');
        
        // Duplicate kontrolü
        if (currentKeywords.includes(keyword.trim())) return;
        
        // Limit kontrolü (maksimum 10)
        if (currentKeywords.length >= 10) {
            alert('En fazla 10 anahtar kelime ekleyebilirsiniz.');
            return;
        }
        
        currentKeywords.push(keyword.trim());
        hiddenInput.value = currentKeywords.join(', ');
        hiddenInput.dispatchEvent(new Event('input'));
        
        this.updateKeywordDisplay(hiddenInput.value);
    }

    // Keyword kaldır
    removeKeyword(keywordToRemove) {
        const hiddenInput = document.getElementById('seo-keywords-hidden');
        if (!hiddenInput) return;
        
        const currentKeywords = hiddenInput.value.split(',').map(k => k.trim()).filter(k => k !== '');
        const updatedKeywords = currentKeywords.filter(k => k !== keywordToRemove);
        
        hiddenInput.value = updatedKeywords.join(', ');
        hiddenInput.dispatchEvent(new Event('input'));
        
        this.updateKeywordDisplay(hiddenInput.value);
    }

    // Keyword display'i güncelle
    updateKeywordDisplay(keywordsString) {
        const keywordDisplay = document.getElementById('keyword-display');
        if (!keywordDisplay) return;
        
        keywordDisplay.innerHTML = '';
        
        if (!keywordsString || keywordsString.trim() === '') return;
        
        const keywords = keywordsString.split(',').map(k => k.trim()).filter(k => k !== '');
        
        keywords.forEach(keyword => {
            const badge = document.createElement('span');
            badge.className = 'badge badge-secondary me-1 mb-1';
            badge.style.padding = '6px 8px';
            
            badge.innerHTML = `
                <span class="keyword-text">${keyword}</span>
                <span class="keyword-remove" style="cursor: pointer; padding: 2px 4px; border-radius: 2px; transition: background-color 0.2s;" 
                      onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'" 
                      onmouseout="this.style.backgroundColor='transparent'">&times;</span>
            `;
            
            badge.querySelector('.keyword-remove').addEventListener('click', () => {
                this.removeKeyword(keyword);
            });
            
            keywordDisplay.appendChild(badge);
        });
    }

    // Slug sistemi kurulumu
    setupSlugSystem() {
        const slugInput = document.getElementById('page-slug');
        const slugStatus = document.getElementById('slug-status');
        const titleInput = document.querySelector('[wire\\:model*=".title"]');
        
        if (!slugInput) return;

        // Title'dan otomatik slug oluşturma
        if (titleInput) {
            titleInput.addEventListener('input', () => {
                if (slugInput.value.trim() === '') {
                    const slug = this.generateSlug(titleInput.value);
                    slugInput.value = slug;
                    slugInput.dispatchEvent(new Event('input', { bubbles: true }));
                    this.checkSlugUniqueness(slug);
                }
            });
        }

        // Slug değişikliklerini dinle
        let slugTimeout;
        slugInput.addEventListener('input', () => {
            clearTimeout(slugTimeout);
            const slug = slugInput.value.trim();
            
            if (slug === '') {
                if (slugStatus) slugStatus.innerHTML = '';
                slugInput.classList.remove('is-valid', 'is-invalid');
                return;
            }
            
            // Slug format kontrolü
            const cleanSlug = this.generateSlug(slug);
            if (slug !== cleanSlug) {
                slugInput.value = cleanSlug;
                slugInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
            
            slugTimeout = setTimeout(() => {
                this.checkSlugUniqueness(cleanSlug);
            }, 500);
        });
    }

    // Slug benzersizlik kontrolü
    async checkSlugUniqueness(slug) {
        if (!slug || !this.currentModel) return;
        
        const slugStatus = document.getElementById('slug-status');
        const slugInput = document.getElementById('page-slug');
        
        if (slugStatus) {
            slugStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Kontrol ediliyor...';
        }
        if (slugInput) {
            slugInput.classList.remove('is-valid', 'is-invalid');
        }
        
        try {
            const response = await fetch(`${this.baseUrl}/check-slug`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    slug: slug,
                    module: this.currentModel.type,
                    exclude_id: this.currentModel.id
                })
            });

            const data = await response.json();
            
            if (slugStatus && slugInput) {
                if (data.unique) {
                    slugStatus.innerHTML = '<i class="fas fa-check"></i> Kullanılabilir';
                    slugInput.classList.remove('is-invalid');
                    slugInput.classList.add('is-valid');
                } else {
                    slugStatus.innerHTML = '<i class="fas fa-times"></i> Bu slug zaten kullanılıyor';
                    slugInput.classList.remove('is-valid');
                    slugInput.classList.add('is-invalid');
                }
            }
        } catch (error) {
            console.error('Slug kontrolü hatası:', error);
            if (slugStatus) {
                slugStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Kontrol edilemiyor';
            }
        }
    }

    // Türkçe slug üretimi
    generateSlug(text) {
        if (!text) return '';
        
        const turkishChars = {
            'Ç': 'C', 'ç': 'c', 'Ğ': 'G', 'ğ': 'g', 
            'I': 'I', 'ı': 'i', 'İ': 'I', 'i': 'i',
            'Ö': 'O', 'ö': 'o', 'Ş': 'S', 'ş': 's', 
            'Ü': 'U', 'ü': 'u'
        };
        
        return text
            .replace(/[ÇçĞğIıİiÖöŞşÜü]/g, match => turkishChars[match] || match)
            .toLowerCase()
            .replace(/[^a-z0-9\s\-]/g, '')
            .replace(/[\s\-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    // Event listener'ları kur
    setupEventListeners() {
        // SEO event listeners seo-tabs.js'te yönetiliyor
    }
}

// Language Animation System
class LanguageAnimationSystem {
    constructor() {
        this.container = null;
        this.buttons = null;
        this.badge = null;
        this.isHovering = false;
        this.hasInitialAnimationRun = false;
        this.init();
    }

    init() {
        // DOM hazır olduğunda sistemi başlat
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    setup() {
        this.container = document.querySelector('.language-animation-container');
        if (!this.container) return;

        this.buttons = this.container.querySelector('.language-buttons');
        this.badge = this.container.querySelector('.language-badge');
        
        if (!this.buttons || !this.badge) return;

        this.setupHoverEvents();
        this.startInitialAnimation();
    }

    setupHoverEvents() {
        this.container.addEventListener('mouseenter', () => {
            this.isHovering = true;
            this.showButtons();
        });

        this.container.addEventListener('mouseleave', () => {
            this.isHovering = false;
            this.showBadge();
        });
    }

    showButtons() {
        // TR EN AR butonları zaten sabit - sadece badge'i gizle
        this.badge.style.opacity = '0';
        this.badge.style.visibility = 'hidden';
        this.badge.style.transform = 'translateX(-50%) translateY(10px)';
    }

    showBadge() {
        // Badge'i göster - TR EN AR butonları sabit kalır
        this.badge.style.opacity = '1';
        this.badge.style.visibility = 'visible';
        this.badge.style.transform = 'translateX(-50%) translateY(0)';
    }

    startInitialAnimation() {
        // Sadece sayfa açıldığında bir kez çalışır
        if (this.hasInitialAnimationRun) return;
        
        // İlk yüklemede TR EN AR butonları göster (2 saniye)
        this.showButtons();
        
        // 3 saniye sonra badge'i göster ve hover'a geç
        setTimeout(() => {
            if (!this.isHovering) {
                this.showBadge();
            }
            this.hasInitialAnimationRun = true;
        }, 3000);
    }
}

// Global SEO Widget instance'ı oluştur
window.GlobalSeoWidget = new GlobalSeoWidget();

// Language Animation System'i başlat
window.LanguageAnimationSystem = new LanguageAnimationSystem();

// Global exports
window.TabManager = TabManager;
window.setupCharacterCounting = setupCharacterCounting;
// window.MultiLangFormSwitcher = MultiLangFormSwitcher; // Moved to manage.js