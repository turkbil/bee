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
            this.initializeSeoSystem();
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

    initializeSeoSystem() {
        this.seoManager = {
            updateAllCounters() {
                const counters = document.querySelectorAll('.seo-character-counter');
                counters.forEach(counter => {
                    const fieldName = counter.getAttribute('data-counter-for');
                    const limit = parseInt(counter.getAttribute('data-limit'));
                    const input = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);
                    
                    if (input) {
                        this.updateCounter(input, counter, limit);
                    }
                });
            },
            
            updateCounter(input, counter, limit) {
                const currentLength = input.value.length;
                const percentage = (currentLength / limit) * 100;
                
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
                
                counter.className = `seo-character-counter ${colorClass}`;
                counter.innerHTML = `${currentLength}/${limit}`;
                
                // Progress bar güncelle
                const progressBar = counter.closest('label')?.querySelector('.seo-progress-bar') || 
                                   counter.closest('.form-text')?.querySelector('.seo-progress-bar');
                if (progressBar) {
                    progressBar.style.width = `${Math.min(100, percentage)}%`;
                    progressBar.className = `progress-bar seo-progress-bar ${progressColorClass}`;
                }
            }
        };
    }

    bindGlobalEvents() {
        // Tab değişikliklerini dinle
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-bs-toggle="tab"]')) {
                setTimeout(() => {
                    if (this.seoManager) {
                        this.seoManager.updateAllCounters();
                    }
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

// Character Counting System
function setupCharacterCounting() {
    console.log('🔄 Character counting sistemi başlatılıyor...');
    
    const counters = document.querySelectorAll('.seo-character-counter');
    
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
        
        // Progress bar'ı bul (label içinde veya form-text içinde)
        const progressBar = counter.closest('label')?.querySelector('.seo-progress-bar') || 
                           counter.closest('.form-text')?.querySelector('.seo-progress-bar');
        
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
                
                counter.className = `seo-character-counter ${colorClass}`;
                counter.innerHTML = `${currentLength}/${limit}`;
                
                // Progress bar güncelle
                if (progressBar) {
                    progressBar.style.width = `${Math.min(100, percentage)}%`;
                    progressBar.className = `progress-bar seo-progress-bar ${progressColorClass}`;
                }
            }, 100);
        }
        
        // Event listeners
        input.addEventListener('input', updateCounter);
        input.addEventListener('keyup', updateCounter);
        input.addEventListener('change', updateCounter);
        
        // İlk yükleme
        updateCounter();
        
        console.log(`✅ Character counter kuruldu: ${fieldName}`);
    });
    
    console.log('✅ Character counting sistemi hazır!');
}

// Keyword System
function setupKeywordSystem() {
    console.log('🔄 Keyword sistemi başlatılıyor...');
    
    const keywordInput = document.getElementById('keyword-input');
    const addKeywordBtn = document.getElementById('add-keyword');
    const keywordDisplay = document.getElementById('keyword-display');
    const hiddenInput = document.getElementById('seo-keywords-hidden');
    
    if (!keywordInput || !keywordDisplay || !hiddenInput) {
        console.warn('⚠️ Keyword elemanları bulunamadı');
        return;
    }
    
    // Add keyword function
    function addKeyword() {
        const keyword = keywordInput.value.trim();
        if (!keyword) return;
        
        // Duplicate check
        const existingKeywords = Array.from(keywordDisplay.querySelectorAll('.keyword-text'))
            .map(elem => elem.textContent.trim());
        
        if (existingKeywords.includes(keyword)) {
            alert('Bu anahtar kelime zaten ekli!');
            return;
        }
        
        // Create badge
        const badge = document.createElement('span');
        badge.className = 'badge badge-secondary me-1 mb-1';
        badge.style.cssText = 'padding: 6px 8px;';
        badge.innerHTML = `
            <span class="keyword-text">${keyword}</span>
            <span class="keyword-remove" style="cursor: pointer; padding: 2px 4px; border-radius: 2px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'" onmouseout="this.style.backgroundColor='transparent'">&times;</span>
        `;
        
        // Remove click event
        badge.querySelector('.keyword-remove').addEventListener('click', function() {
            badge.remove();
            updateHiddenInput();
        });
        
        keywordDisplay.appendChild(badge);
        keywordInput.value = '';
        updateHiddenInput();
    }
    
    // Update hidden input
    function updateHiddenInput() {
        const keywords = Array.from(keywordDisplay.querySelectorAll('.keyword-text'))
            .map(elem => elem.textContent.trim())
            .filter(text => text.length > 0);
        
        hiddenInput.value = keywords.join(', ');
        hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
        
        console.log('✅ Keywords updated:', keywords);
    }
    
    // Event listeners
    if (addKeywordBtn) {
        addKeywordBtn.addEventListener('click', addKeyword);
    }
    
    keywordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addKeyword();
        }
    });
    
    // Existing remove buttons
    keywordDisplay.querySelectorAll('.keyword-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            btn.closest('.badge').remove();
            updateHiddenInput();
        });
    });
    
    console.log('✅ Keyword sistemi hazır!');
}

// Initialize system
document.addEventListener('DOMContentLoaded', function() {
    window.pageManagement = new PageManagementSystem();
});

// Initialize manage page functions
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('/manage')) {
        console.log('🔄 Manage sayfası yüklendi:', window.location.pathname);
        
        setupCharacterCounting();
        setupKeywordSystem();
        MultiLangFormSwitcher.init();
    }
});

// Livewire update handler
document.addEventListener('livewire:updated', function() {
    if (window.location.pathname.includes('/manage')) {
        setTimeout(function() {
            setupCharacterCounting();
            setupKeywordSystem();
            MultiLangFormSwitcher.init();
        }, 100);
    }
});

// Multi-Language Form Switcher
const MultiLangFormSwitcher = {
    init() {
        console.log('🌐 MultiLangFormSwitcher başlatılıyor...');
        
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
                    this.switchLanguage(targetLang);
                }
            });
        });
        
        console.log('✅ MultiLangFormSwitcher hazır!');
    },
    
    switchLanguage(language) {
        console.log('🔄 Dil geçişi:', language);
        
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
        
        // Trigger Livewire language switch if available
        if (window.Livewire) {
            Livewire.dispatch('switchLanguage', { language: language });
        }
        
        console.log('✅ Dil geçişi tamamlandı:', language);
    }
};

// Global exports
window.TabManager = TabManager;
window.setupKeywordSystem = setupKeywordSystem;
window.setupCharacterCounting = setupCharacterCounting;
window.MultiLangFormSwitcher = MultiLangFormSwitcher;