
// Theme Builder CSS Specificity Fix
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap Offcanvas manual initialization for theme builder
    const offcanvasElement = document.getElementById('offcanvasTheme');
    if (offcanvasElement) {
        // Bootstrap Offcanvas'ı manuel olarak başlat
        if (typeof bootstrap !== 'undefined' && bootstrap.Offcanvas) {
            const themeOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
            
            // Theme builder açıldığında form update
            offcanvasElement.addEventListener('shown.bs.offcanvas', function() {
                // Form state güncelleme
                if (typeof updateThemeBuilderForm === 'function') {
                    updateThemeBuilderForm();
                }
                
                // Real-time listener'ları attach et
                if (typeof attachRealTimeListeners === 'function') {
                    attachRealTimeListeners();
                }
                
                // CSS değişkenlerini zorla güncelle
                setTimeout(() => {
                    forceUpdateThemeState();
                }, 100);
            });
        }
    }
    
    // Theme mode switch için fallback event handling
    const themeSwitch = document.getElementById('switch');
    if (themeSwitch) {
        // Eğer tema sistemi yüklenmemişse fallback
        setTimeout(() => {
            if (typeof initThemeSwitch !== 'function') {
                initFallbackThemeSwitch();
            }
        }, 1000);
    }
});

// Fallback theme switch sistemi
function initFallbackThemeSwitch() {
    const themeSwitch = document.getElementById('switch');
    if (!themeSwitch) return;
    
    themeSwitch.addEventListener('change', function() {
        const currentTheme = getCookieValue('dark') || 'auto';
        let newTheme = 'auto';
        
        if (currentTheme === '0') newTheme = '1';
        else if (currentTheme === '1') newTheme = 'auto';
        else newTheme = '0';
        
        document.cookie = `dark=${newTheme};path=/;max-age=31536000`;
        
        // Tema uygula
        applyThemeModeFallback(newTheme);
        
        // Switch ve container state güncelle
        updateThemeSwitchState(newTheme);
    });
}

// Fallback tema modu uygulama
function applyThemeModeFallback(themeMode) {
    const body = document.body;
    
    if (themeMode === '1') {
        body.setAttribute('data-bs-theme', 'dark');
        body.classList.remove('light');
        body.classList.add('dark');
    } else if (themeMode === '0') {
        body.setAttribute('data-bs-theme', 'light');
        body.classList.remove('dark');
        body.classList.add('light');
    } else {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        body.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
        body.classList.remove(prefersDark ? 'light' : 'dark');
        body.classList.add(prefersDark ? 'dark' : 'light');
    }
}

// Theme switch state güncelleme
function updateThemeSwitchState(themeMode) {
    const themeContainer = document.querySelector('.theme-mode');
    const themeSwitch = document.getElementById('switch');
    
    if (themeContainer && themeSwitch) {
        if (themeMode === 'auto') {
            themeContainer.setAttribute('data-theme', 'auto');
            themeSwitch.checked = false;
        } else if (themeMode === '1') {
            themeContainer.setAttribute('data-theme', 'dark');
            themeSwitch.checked = true;
        } else {
            themeContainer.setAttribute('data-theme', 'light');
            themeSwitch.checked = false;
        }
    }
}

// Theme state'i zorla güncelleme
function forceUpdateThemeState() {
    // CSS Custom Properties zorla yenile
    const root = document.documentElement;
    const currentTheme = getCookieValue('dark') || 'auto';
    const currentColor = getCookieValue('siteColor') || '#066fd1';
    
    // Primary color CSS değişkenlerini zorla uygula
    root.style.setProperty('--tblr-primary', currentColor, 'important');
    root.style.setProperty('--tblr-primary-rgb', hexToRgbString(currentColor), 'important');
    
    // Body class'larını zorla güncelle
    const body = document.body;
    if (currentTheme === '1') {
        body.classList.add('dark');
        body.classList.remove('light');
    } else if (currentTheme === '0') {
        body.classList.add('light');
        body.classList.remove('dark');
    } else {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        body.classList.add(prefersDark ? 'dark' : 'light');
        body.classList.remove(prefersDark ? 'light' : 'dark');
    }
    
    // Theme container state güncelle
    updateThemeSwitchState(currentTheme);
}

// Cookie okuma helper
function getCookieValue(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

// HEX to RGB string helper
function hexToRgbString(hex) {
    if (!hex || !hex.startsWith('#')) return '6, 111, 209'; // Fallback
    
    // Hex uzunluk kontrolü
    if (hex.length !== 7) {
        console.warn('Geçersiz hex uzunluğu:', hex, '- varsayılan değer kullanılıyor');
        return '6, 111, 209'; // Fallback
    }
    
    const r = parseInt(hex.substring(1, 3), 16);
    const g = parseInt(hex.substring(3, 5), 16);
    const b = parseInt(hex.substring(5, 7), 16);
    
    // NaN kontrolü
    if (isNaN(r) || isNaN(g) || isNaN(b)) {
        console.warn('RGB dönüşümünde NaN değeri main.js\'te:', {hex, r, g, b}, '- varsayılan değer kullanılıyor');
        return '6, 111, 209'; // Fallback
    }
    
    return `${r}, ${g}, ${b}`;
}

// Translation function
function t(key, params = {}) {
    if (typeof window.jsTranslations === 'undefined') {
        console.warn('jsTranslations not loaded, using fallback');
        return key;
    }
    
    const locale = document.documentElement.lang || 'tr';
    const translations = window.jsTranslations[locale] || window.jsTranslations['tr'] || {};
    
    let text = translations[key] || key;
    
    // Parameter replacement
    if (params && Object.keys(params).length > 0) {
        Object.keys(params).forEach(param => {
            text = text.replace(`{${param}}`, params[param]);
        });
    }
    
    return text;
}

// Bootstrap kontrolü ve fallback
function ensureBootstrap() {
    // Tabler.min.js Bootstrap'ı içerir, ancak yüklenmesi biraz zaman alabilir
    if (typeof bootstrap === 'undefined' && typeof window.bootstrap === 'undefined') {
        // Bootstrap yüklenmemişse basit fallback
        window.bootstrap = {
            Offcanvas: function(element) {
                return {
                    show: function() { 
                        element.classList.add('show'); 
                        element.style.visibility = 'visible';
                    },
                    hide: function() { 
                        element.classList.remove('show'); 
                        element.style.visibility = 'hidden';
                    },
                    toggle: function() {
                        if (element.classList.contains('show')) {
                            this.hide();
                        } else {
                            this.show();
                        }
                    }
                };
            },
            Modal: function(element) {
                return {
                    show: function() { element.style.display = 'block'; },
                    hide: function() { element.style.display = 'none'; }
                };
            }
        };
        // Bootstrap fallback aktif
    }
}

// Bootstrap'ı hemen kontrol et
ensureBootstrap();

// Sayfa yüklendikten sonra da kontrol et
document.addEventListener('DOMContentLoaded', function() {
    ensureBootstrap();
});

// Choices.js Sistemi
function initializeChoices() {
    const choicesElements = document.querySelectorAll('[data-choices]');
    
    choicesElements.forEach(function(element) {
        if (element.dataset.choicesInitialized) {
            return;
        }
        
        // Listeleme sayfalarındaki TÜM filtreleme selectbox'larını atla - normal select olarak kalsın
        const wireModel = element.getAttribute('wire:model.live') || element.getAttribute('wire:model');
        const listingFilters = [
            'perPage', 'selectedCategory', 'roleFilter', 'statusFilter', 'viewType',
            'typeFilter', 'parentCategoryFilter', 'categoryFilter', 'theme_id'
        ];
        
        if (listingFilters.includes(wireModel)) {
            return;
        }
        
        element.dataset.choicesInitialized = 'true';
        
        const options = {
            searchEnabled: true,
            searchPlaceholderValue: t('search_placeholder'),
            noResultsText: t('no_results'),
            noChoicesText: t('no_choices'),
            itemSelectText: t('item_select'),
            removeItemButton: false,
            duplicateItemsAllowed: false,
            placeholder: true,
            placeholderValue: t('select_placeholder'),
            searchResultLimit: 10,
            shouldSort: true,
            position: 'bottom',
            loadingText: t('loading'),
            addItemText: (value) => `"${value}" ${t('add_item')}`,
            maxItemText: (maxItemCount) => t('max_items', {count: maxItemCount}),
            uniqueItemText: t('duplicate_item'),
            customAddItemText: t('invalid_comma'),
        };
        
        // Data attribute'lardan özel ayarları al
        if (element.dataset.choicesMultiple === 'true') {
            options.removeItemButton = true;
            options.addItems = true;
            // Virgül karakterini engelle
            options.addItemFilter = (value) => {
                return !value.includes(',');
            };
        }
        
        // Filter selectbox'ları için özel ayarlar
        if (element.dataset.choicesFilter === 'true') {
            options.itemSelectText = '';
            options.searchEnabled = false;
            options.placeholderValue = null;
            options.allowHTML = true;
        }
        
        if (element.dataset.choicesSearch === 'false') {
            options.searchEnabled = false;
        }
        
        if (element.dataset.choicesPlaceholder) {
            options.placeholderValue = element.dataset.choicesPlaceholder;
        }
        
        if (element.dataset.choicesMaxItems) {
            options.maxItemCount = parseInt(element.dataset.choicesMaxItems);
        }
        
        // Choices.js'i başlat
        const choices = new Choices(element, options);
        
        // Filter selectbox'ları için genişlik ayarla
        if (element.dataset.choicesFilter === 'true') {
            const container = choices.containerOuter.element;
            container.style.width = '100%';
            container.style.minWidth = '100%';
        }
        
        // Element'a choices instance'ını ekle
        element.choicesInstance = choices;
        
        
        // Choices.js'in name attribute'unu düzelt
        if (element.getAttribute('wire:model.live') === 'selectedCategory') {
            element.name = 'selectedCategory';
            element.setAttribute('name', 'selectedCategory');
            
            // Choices'ın passedElement'ini de kontrol et
            if (choices.passedElement && choices.passedElement.element) {
                choices.passedElement.element.name = 'selectedCategory';
                choices.passedElement.element.setAttribute('name', 'selectedCategory');
            }
            
        }
        
        // Choices.js getValue metodunu override et - Livewire için
        const originalGetValue = choices.getValue.bind(choices);
        choices.getValue = function(valueOnly = false) {
            const result = originalGetValue(valueOnly);
            
            // Eğer sonuç object ise ve value property'si varsa, sadece value'yu döndür
            if (typeof result === 'object' && result !== null && result.hasOwnProperty('value')) {
                return result.value;
            }
            
            return result;
        };
        
        // Livewire değeri varsa hemen set et
        if (element.hasAttribute('wire:model') || element.hasAttribute('wire:model.live')) {
            setTimeout(() => {
                const currentValue = element.value;
                const finalValue = currentValue; // Normal choices için sadece mevcut değer
                
                
                if (finalValue && choices.getValue(true) !== finalValue) {
                    choices.setChoiceByValue(finalValue);
                }
            }, 50);
        }
        
        // Livewire entegrasyonu
        if (element.hasAttribute('wire:model')) {
            // Değişiklik dinleme - Livewire için özelleştirilmiş
            choices.passedElement.element.addEventListener('change', function(e) {
                // Override edilmiş getValue metodunu kullan (artık her zaman string döner)
                const selectedValue = choices.getValue(true);
                this.value = selectedValue;
                
                // Livewire'a temiz input event'i gönder
                this.dispatchEvent(new Event('input', { bubbles: true }));
                
                // URL'deki [value] eklerini temizle
                setTimeout(() => {
                    const url = new URL(window.location);
                    const params = new URLSearchParams(url.search);
                    
                    // [value] içeren parametreleri bul ve temizle
                    for (const [key, value] of params.entries()) {
                        if (key.includes('[value]')) {
                            const cleanKey = key.replace('[value]', '');
                            params.delete(key);
                            if (value) {
                                params.set(cleanKey, value);
                            }
                        }
                    }
                    
                    // URL'yi güncelle
                    url.search = params.toString();
                    window.history.replaceState({}, '', url);
                }, 100);
            });
            
            // Livewire'dan gelen değer değişikliklerini dinle
            const wireName = element.getAttribute('wire:model') || element.getAttribute('wire:model.live') || element.getAttribute('wire:model.defer');
            if (wireName) {
                // Element'in değerini kontrol et ve choices'ı güncelle
                const updateChoicesValue = () => {
                    const currentValue = element.value;
                    if (currentValue && choices.getValue(true) !== currentValue) {
                        choices.setChoiceByValue(currentValue);
                    }
                };
                
                // İlk yüklemede değeri kontrol et
                setTimeout(updateChoicesValue, 100);
                
                // Livewire güncellemelerini dinle
                document.addEventListener('livewire:updated', updateChoicesValue);
                document.addEventListener('livewire:morph.updated', updateChoicesValue);
            }
        }
    });
}

// Tags Input Sistemi
function initializeTagsInput() {
    const tagInputs = document.querySelectorAll('.tags-input');
    
    tagInputs.forEach(function(container) {
        if (container.dataset.initialized) return;
        container.dataset.initialized = 'true';
        
        const hiddenInput = container.querySelector('input[type="hidden"]');
        const tagsContainer = container.querySelector('.tags-container');
        const tagInput = container.querySelector('.tag-input');
        
        if (!hiddenInput || !tagsContainer || !tagInput) return;
        
        // Mevcut değerleri yükle
        loadExistingTags();
        
        // Enter veya virgül ile tag ekleme
        tagInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                addTag(this.value.trim());
                this.value = '';
            }
        });
        
        // Focus kaybında tag ekleme
        tagInput.addEventListener('blur', function() {
            if (this.value.trim()) {
                addTag(this.value.trim());
                this.value = '';
            }
        });
        
        // Container'a tıklayınca input'a focus
        tagsContainer.addEventListener('click', function() {
            tagInput.focus();
        });
        
        function addTag(value) {
            if (!value || getCurrentTags().includes(value)) return;
            
            const tagElement = document.createElement('span');
            tagElement.className = 'tag-item';
            tagElement.innerHTML = `
                ${value}
                <button type="button" class="tag-remove" onclick="removeTag(this)">×</button>
            `;
            
            tagsContainer.insertBefore(tagElement, tagInput);
            updateHiddenInput();
        }
        
        function getCurrentTags() {
            return Array.from(tagsContainer.querySelectorAll('.tag-item'))
                .map(tag => tag.textContent.replace('×', '').trim());
        }
        
        function updateHiddenInput() {
            const tags = getCurrentTags();
            hiddenInput.value = tags.join(',');
            
            // Livewire entegrasyonu
            if (hiddenInput.hasAttribute('wire:model')) {
                hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
        
        function loadExistingTags() {
            const existingValue = hiddenInput.value;
            if (existingValue) {
                const tags = existingValue.split(',').map(tag => tag.trim()).filter(tag => tag);
                tags.forEach(tag => addTag(tag));
            }
        }
        
        // Global removeTag fonksiyonu
        window.removeTag = function(button) {
            button.parentElement.remove();
            updateHiddenInput();
        };
    });
}

// Tooltip Sistemi
function initializeTooltips() {
    // Mevcut tooltip'leri dispose et
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(element) {
        if (element._tooltip) {
            element._tooltip.dispose();
        }
    });
    
    // Yeni tooltip'leri başlat
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"], [title]');
    
    tooltips.forEach(function (tooltipElement) {
        // Sadece title attribute'u olan elementler için tooltip oluştur
        if (tooltipElement.getAttribute('title') && 
            !tooltipElement.hasAttribute('data-bs-toggle')) {
            tooltipElement.setAttribute('data-bs-toggle', 'tooltip');
        }
        
        if (tooltipElement.hasAttribute('data-bs-toggle') && 
            tooltipElement.getAttribute('data-bs-toggle') === 'tooltip') {
            
            // Bootstrap 5 kullanımı
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltip = new bootstrap.Tooltip(tooltipElement, {
                    trigger: 'hover focus',
                    placement: 'bottom'
                });
                tooltipElement._tooltip = tooltip;
            }
        }
    });
}

// Document Ready
document.addEventListener("DOMContentLoaded", function () {
    // Choices.js'i başlat
    initializeChoices();
    
    // Tags Input'ları başlat
    initializeTagsInput();

    // Livewire entegrasyonu
    document.addEventListener("livewire:navigated", function () {
        setTimeout(function() {
            initializeChoices();
            initializeTagsInput();
            initializeTooltips();
        }, 100);
    });

    document.addEventListener("livewire:load", function () {
        setTimeout(function() {
            initializeChoices();
            initializeTagsInput();
            initializeTooltips();
        }, 100);
    });

    // DOM değişikliklerini gözlemle
    const observer = new MutationObserver(function(mutations) {
        let shouldReinit = false;
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        if (node.matches && (node.matches('.tags-input') || node.matches('[data-choices]') || node.matches('[title]') || node.matches('[data-bs-toggle="tooltip"]'))) {
                            shouldReinit = true;
                        }
                        if (node.querySelectorAll && (node.querySelectorAll('.tags-input').length > 0 || node.querySelectorAll('[data-choices]').length > 0 || node.querySelectorAll('[title]').length > 0 || node.querySelectorAll('[data-bs-toggle="tooltip"]').length > 0)) {
                            shouldReinit = true;
                        }
                    }
                });
            }
        });
        if (shouldReinit) {
            setTimeout(function() {
                initializeChoices();
                initializeTagsInput();
                initializeTooltips();
            }, 50);
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Tooltip başlat
    initializeTooltips();

    // Module menu
    const dropdowns = document.querySelectorAll(".module-menu .dropdown");
    dropdowns.forEach(function (dropdown) {
        dropdown.addEventListener("click", function (event) {
            dropdown.classList.add("open");
            event.stopPropagation();
        });
    });

    document.addEventListener("click", function (e) {
        if (!e.target.closest(".module-menu .dropdown")) {
            dropdowns.forEach(function (dropdown) {
                dropdown.classList.remove("open");
            });
        }
    });

    const moduleItems = document.querySelectorAll(
        ".module-menu .dropdown-module-item"
    );
    moduleItems.forEach(function (item) {
        item.addEventListener("click", function (e) {
            e.stopPropagation();
        });
    });

    // Datepicker
    const litepickerLocale = {
        months: t('months'),
        weekdaysShort: t('weekdays_short'),
    };

    const datepickers = document.querySelectorAll(".datepicker");
    datepickers.forEach(function (datepicker) {
        const picker = new Litepicker({
            element: datepicker,
            format: "YYYY-MM-DD",
            singleMode: true,
            dropdowns: {
                months: true,
                years: true,
            },
            numberOfMonths: 1,
            numberOfColumns: 1,
            resetButton: true,
            lang: "tr-TR",
            locale: litepickerLocale,
            setup: function (picker) {
                picker.on("selected", function (date) {
                    if (datepicker.classList.contains("datepicker-start")) {
                        // Livewire.emit("setFilter", "date_start", date.format("YYYY-MM-DD"));
                    } else if (
                        datepicker.classList.contains("datepicker-end")
                    ) {
                        // Livewire.emit("setFilter", "date_end", date.format("YYYY-MM-DD"));
                    }
                });
            },
        });
    });
});

// CSRF Token ayarla
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content"),
    },
});

// Modal kapatıldığında formu sıfırla
document.addEventListener("hidden.bs.modal", function (event) {
    const modal = event.target;
    if (!modal) return;

    const forms = modal.querySelectorAll("form");
    forms.forEach(function (form) {
        form.reset();
    });

    const inputs = modal.querySelectorAll(
        'input[type="text"], input[type="email"], input[type="number"], textarea'
    );
    inputs.forEach(function (input) {
        input.value = "";
    });

    const checkboxes = modal.querySelectorAll(
        'input[type="checkbox"], input[type="radio"]'
    );
    checkboxes.forEach(function (input) {
        input.checked = false;
    });

    const selects = modal.querySelectorAll("select");
    selects.forEach(function (select) {
        select.selectedIndex = 0;
    });
});

// Sayfa yüklendiğinde başlat
document.addEventListener('DOMContentLoaded', function() {
    initializeChoices();
    initializeTagsInput();
    initializeTooltips();
});

// Livewire güncellemelerinden sonra yeniden başlat
document.addEventListener('livewire:updated', function() {
    initializeChoices();
    initializeTagsInput();
    initializeTooltips();
});

// Livewire component'ler mount olduğunda kontrol et
document.addEventListener('livewire:init', function() {
    // URL parametrelerinden perPage değerini al
    const urlParams = new URLSearchParams(window.location.search);
    const perPageFromUrl = urlParams.get('perPage');
    
    if (perPageFromUrl) {
        // perPage select'ini bul ve değerini güncelle
        setTimeout(() => {
            const perPageSelects = document.querySelectorAll('select[wire\\:model\\.live="perPage"], select[wire\\:model="perPage"]');
            perPageSelects.forEach(select => {
                if (select.choicesInstance) {
                    select.choicesInstance.setChoiceByValue(perPageFromUrl);
                } else {
                    // Choices henüz init olmamışsa değeri direkt set et
                    select.value = perPageFromUrl;
                }
            });
        }, 200);
    }
});

// Livewire navigated event'ini dinle (daha güvenli)
document.addEventListener('livewire:navigated', function() {
    setTimeout(() => {
        // Tüm select elementlerini kontrol et
        const allSelects = document.querySelectorAll('select[wire\\:model\\.live="perPage"], select[wire\\:model="perPage"]');
        allSelects.forEach(select => {
            const currentValue = select.value;
            if (select.choicesInstance && currentValue) {
                // Choices instance varsa ve değer varsa güncelle
                select.choicesInstance.setChoiceByValue(currentValue);
            }
        });
    }, 100);
});

// Cache Clear İşlevselliği
document.addEventListener('DOMContentLoaded', function() {
    // Cache clear butonları için event listener
    const cacheClearBtns = document.querySelectorAll('.cache-clear-btn, .cache-clear-all-btn');
    
    cacheClearBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const action = this.dataset.action;
            const isAllClear = action === 'clear-all';
            
            // Dropdown menüdeki hızlı işlemler grid iconunu bul ve loading state yap
            const quickActionsDropdown = document.querySelector('[data-bs-toggle="dropdown"] .fa-grid-2');
            let originalGridIcon = '';
            if (quickActionsDropdown) {
                originalGridIcon = quickActionsDropdown.className;
                quickActionsDropdown.className = 'fa-solid fa-spinner fa-spin';
            }
            
            // Cache clear butonundaki icon'u da loading state yap
            const iconElement = this.querySelector('i');
            const originalIcon = iconElement.className;
            iconElement.className = 'fa-solid fa-spinner fa-spin';
            this.style.pointerEvents = 'none';
            
            // AJAX isteği
            const baseUrl = window.location.origin;
            const url = isAllClear ? `${baseUrl}/admin/cache/clear-all` : `${baseUrl}/admin/cache/clear`;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Başarı toast'ı göster
                    if (typeof showToast === 'function') {
                        showToast(t('success'), data.message, 'success');
                    }
                    
                } else {
                    // Hata toast'ı göster
                    if (typeof showToast === 'function') {
                        showToast(t('error'), data.message, 'error');
                    }
                }
            })
            .catch(error => {
                if (typeof showToast === 'function') {
                    showToast(t('error'), t('cache_error'), 'error');
                }
            })
            .finally(() => {
                // Grid icon'u tekrar normale döndür
                if (quickActionsDropdown && originalGridIcon) {
                    quickActionsDropdown.className = originalGridIcon;
                }
                
                // Buton icon'u tekrar normale döndür
                iconElement.className = originalIcon;
                this.style.pointerEvents = 'auto';
            });
        });
    });
});

// Tab State Management for Admin Forms
const TabManager = {
    storageKey: 'adminFormActiveTab',
    
    init(customKey = null) {
        if (customKey) {
            this.storageKey = customKey;
        }
        this.restoreActiveTab();
        this.bindTabEvents();
        this.bindLivewireEvents();
    },
    
    getActiveTab() {
        return localStorage.getItem(this.storageKey) || 'tabs-1';
    },
    
    setActiveTab(tabId) {
        localStorage.setItem(this.storageKey, tabId);
    },
    
    restoreActiveTab() {
        const activeTab = this.getActiveTab();
        if (activeTab) {
            // Bootstrap tab navigation'ı güncelle
            $('.nav-link[data-bs-toggle="tab"]').removeClass('active');
            $(`.nav-link[href="#${activeTab}"]`).addClass('active');
            
            // Tab content'leri güncelle
            $('.tab-pane').removeClass('active show');
            $(`#${activeTab}`).addClass('active show');
        }
    },
    
    bindTabEvents() {
        const self = this;
        $('.nav-link[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            const activeTab = $(e.target).attr('href').substring(1);
            self.setActiveTab(activeTab);
        });
    },
    
    bindLivewireEvents() {
        const self = this;
        window.addEventListener('livewire:updated', function () {
            setTimeout(() => self.restoreActiveTab(), 50);
        });
    }
};

// Language Switcher for Multi-Language Forms
const MultiLangFormSwitcher = {
    init() {
        this.bindLanguageSwitch();
    },
    
    bindLanguageSwitch() {
        $(document).on('click', '.language-switch-item', function(e) {
            e.preventDefault();
            
            const $item = $(this);
            const selectedLanguage = $item.data('language');
            const selectedFlag = $item.data('flag');
            const selectedName = $item.data('name');
            
            // UI güncelle
            $('#currentLanguageFlag').text(selectedFlag);
            $('#currentLanguageName').text(selectedName);
            
            // Dropdown active state
            $('.language-switch-item').removeClass('active');
            $('.language-switch-item .fa-check').remove();
            $item.addClass('active');
            $item.append('<i class="fas fa-check ms-auto text-success"></i>');
            
            // Tüm dil içeriklerini gizle ve seçili olanı göster
            $('.language-content').hide();
            $(`.language-content[data-language="${selectedLanguage}"]`).show();
            
            // Session'a kaydet
            const currentPath = window.location.pathname;
            const module = currentPath.split('/')[2]; // /admin/{module}/...
            
            $.post(`/admin/${module}/set-editing-language`, {
                language: selectedLanguage,
                _token: $('meta[name="csrf-token"]').attr('content')
            });
        });
    },
    
    switchLanguage(selectedLanguage) {
        // Tüm dil içeriklerini gizle ve seçili olanı göster
        $('.language-content').hide();
        $(`.language-content[data-language="${selectedLanguage}"]`).show();
        
        // Session'a kaydet
        const currentPath = window.location.pathname;
        const module = currentPath.split('/')[2]; // /admin/{module}/...
        
        $.post(`/admin/${module}/set-editing-language`, {
            language: selectedLanguage,
            _token: $('meta[name="csrf-token"]').attr('content')
        });
    }
};

// TinyMCE Config for Multi-Language Forms
const TinyMCEMultiLang = {
    configs: {},
    
    getConfig(selector, options = {}) {
        const defaultConfig = {
            selector: selector,
            height: 400,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px }',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        };
        
        return {...defaultConfig, ...options};
    },
    
    initAll(languages = ['tr', 'en', 'ar']) {
        languages.forEach(lang => {
            if (document.getElementById(`editor_${lang}`)) {
                tinymce.init(this.getConfig(`#editor_${lang}`));
            }
        });
    },
    
    destroy() {
        tinymce.remove();
    }
};

// Theme Builder Offcanvas Manuel Açma Fonksiyonu
document.addEventListener('DOMContentLoaded', function() {
    console.log('🏗️ Theme Builder DOMContentLoaded başladı');
    
    // Theme builder button'ları bul ve manuel event ekle
    const themeBuilderButtons = document.querySelectorAll('[data-bs-target="#offcanvasTheme"]');
    console.log('🏗️ Theme builder button sayısı:', themeBuilderButtons.length);
    
    themeBuilderButtons.forEach(function(button, index) {
        console.log(`🏗️ Theme builder button ${index + 1} event listener ekleniyor`);
        
        button.addEventListener('click', function(e) {
            console.log('🏗️ THEME BUILDER BUTTON TIKLANDI!');
            e.preventDefault();
            
            // Offcanvas elementini bul
            const offcanvasElement = document.getElementById('offcanvasTheme');
            console.log('🏗️ OffcanvasTheme elementi:', offcanvasElement ? 'bulundu' : 'bulunamadı');
            
            if (offcanvasElement) {
                // Bootstrap yüklenmişse Offcanvas instance'ını oluştur veya al
                console.log('🏗️ Bootstrap kontrolü yapılıyor...');
                console.log('🏗️ Bootstrap tanımlı mı:', typeof bootstrap !== 'undefined');
                console.log('🏗️ Bootstrap.Offcanvas var mı:', typeof bootstrap !== 'undefined' && !!bootstrap.Offcanvas);
                console.log('🏗️ getInstance fonksiyonu var mı:', typeof bootstrap !== 'undefined' && bootstrap.Offcanvas && typeof bootstrap.Offcanvas.getInstance === 'function');
                
                if (typeof bootstrap !== 'undefined' && bootstrap.Offcanvas && typeof bootstrap.Offcanvas.getInstance === 'function') {
                    console.log('✅ Bootstrap Offcanvas kullanılıyor');
                    
                    let offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    console.log('🏗️ Mevcut instance:', offcanvasInstance ? 'var' : 'yok');
                    
                    if (!offcanvasInstance) {
                        console.log('🏗️ Yeni Offcanvas instance oluşturuluyor...');
                        offcanvasInstance = new bootstrap.Offcanvas(offcanvasElement);
                    }
                    
                    // Offcanvas'ı aç
                    console.log('🏗️ Offcanvas açılıyor...');
                    offcanvasInstance.show();
                    console.log('✅ Offcanvas açıldı!');
                } else {
                    console.warn('⚠️ Bootstrap Offcanvas bulunamadı, fallback kullanılıyor');
                    // Fallback olarak direkt show/hide class'ları
                    if (offcanvasElement.classList.contains('show')) {
                        console.log('🏗️ Fallback: Offcanvas kapatılıyor');
                        offcanvasElement.classList.remove('show');
                    } else {
                        console.log('🏗️ Fallback: Offcanvas açılıyor');
                        offcanvasElement.classList.add('show');
                        
                        // Manuel olarak shown.bs.offcanvas eventini tetikle
                        console.log('🏗️ Manuel shown.bs.offcanvas eventi tetikleniyor...');
                        const shownEvent = new CustomEvent('shown.bs.offcanvas');
                        offcanvasElement.dispatchEvent(shownEvent);
                    }
                    return;
                }
            } else {
                console.error('❌ OffcanvasTheme elementi bulunamadı!');
            }
        });
    });
    
    // Theme Builder açıldıkında mevcut ayarları yükle
    const offcanvasTheme = document.getElementById('offcanvasTheme');
    if (offcanvasTheme) {
        offcanvasTheme.addEventListener('shown.bs.offcanvas', function() {
            // Mevcut tema ayarlarını form'a yansıt
            updateThemeBuilderForm();
        });
    }
});

// Theme Builder form güncellemesi
function updateThemeBuilderForm() {
    // Mevcut tema cookie'lerini oku ve form'u güncelle
    const cookies = document.cookie.split(';').reduce((acc, cookie) => {
        const [key, value] = cookie.split('=').map(c => c.trim());
        acc[key] = value;
        return acc;
    }, {});
    
    // Dark mode radio'larını güncelle
    const darkCookie = cookies.dark || 'auto';
    const themeRadios = document.querySelectorAll('input[name="theme"]');
    themeRadios.forEach(radio => {
        if (darkCookie === '1' && radio.value === 'dark') {
            radio.checked = true;
        } else if (darkCookie === '0' && radio.value === 'light') {
            radio.checked = true;
        } else if (darkCookie === 'auto' && radio.value === 'auto') {
            radio.checked = true;
        }
    });
    
    // Primary color'u güncelle
    const primaryColor = cookies.siteColor || '#066fd1';
    const colorRadios = document.querySelectorAll('input[name="theme-primary"]');
    colorRadios.forEach(radio => {
        if (radio.value === primaryColor) {
            radio.checked = true;
        }
    });
    
    // Font seçimini güncelle
    const themeFont = cookies.themeFont;
    if (themeFont) {
        const fontRadios = document.querySelectorAll('input[name="theme-font"]');
        fontRadios.forEach(radio => {
            if (decodeURIComponent(themeFont) === radio.value) {
                radio.checked = true;
            }
        });
    }
    
    // Font size güncelle
    const fontSize = cookies.themeFontSize || 'small';
    const fontSizeRadios = document.querySelectorAll('input[name="theme-font-size"]');
    fontSizeRadios.forEach(radio => {
        if (radio.value === fontSize) {
            radio.checked = true;
        }
    });
    
    // Radius slider güncelle
    const radiusValue = cookies.themeRadius || '0.375rem';
    const radiusSlider = document.getElementById('radius-slider');
    if (radiusSlider) {
        const radiusMap = ['0', '0.25rem', '0.375rem', '0.5rem', '0.75rem', '1rem'];
        const radiusIndex = radiusMap.indexOf(radiusValue);
        if (radiusIndex !== -1) {
            radiusSlider.value = radiusIndex;
            
            // Radius examples'ı güncelle
            const radiusExamples = document.querySelectorAll('.radius-example');
            radiusExamples.forEach((example, index) => {
                if (index === radiusIndex) {
                    example.classList.add('active');
                } else {
                    example.classList.remove('active');
                }
            });
        }
    }
    
    // Table compact güncelle
    const tableCompact = cookies.tableCompact || '0';
    const tableRadios = document.querySelectorAll('input[name="table-compact"]');
    tableRadios.forEach(radio => {
        if (radio.value === tableCompact) {
            radio.checked = true;
        }
    });
    
    // Base theme güncelle
    const themeBase = cookies.themeBase || 'neutral';
    const baseRadios = document.querySelectorAll('input[name="theme-base"]');
    baseRadios.forEach(radio => {
        if (radio.value === themeBase) {
            radio.checked = true;
        }
    });
};

// Bootstrap event entegrasyonu - Theme switcher için
document.addEventListener('DOMContentLoaded', function() {
    // Dark mode toggle için click event listener
    const darkModeSwitch = document.getElementById('switch');
    if (darkModeSwitch) {
        // Theme switch'e manuel event ekle
        darkModeSwitch.addEventListener('click', function(e) {
            // Eğer tema sistemi yüklenmişse, onun change event'ini kullan
            if (typeof initThemeSwitch === 'function') {
                // Theme.js'deki change event'i tetiklenir
                return;
            }
            
            // Fallback: Basit toggle
            const isChecked = this.checked;
            const newTheme = isChecked ? '1' : '0';
            document.cookie = `dark=${newTheme};path=/;max-age=31536000`;
            
            // Body theme'ini güncelle
            if (isChecked) {
                document.body.setAttribute('data-bs-theme', 'dark');
                document.body.classList.add('dark');
                document.body.classList.remove('light');
            } else {
                document.body.setAttribute('data-bs-theme', 'light');
                document.body.classList.add('light');
                document.body.classList.remove('dark');
            }
        });
    }
    
    // Offcanvas events için theme builder entegrasyonu
    const offcanvasTheme = document.getElementById('offcanvasTheme');
    if (offcanvasTheme) {
        // Offcanvas açıldığında
        offcanvasTheme.addEventListener('shown.bs.offcanvas', function() {
            // Form güncellemesini tetikle
            if (typeof updateThemeBuilderForm === 'function') {
                updateThemeBuilderForm();
            }
            
            // Focus management
            const firstInput = this.querySelector('input[type="radio"]:checked, input[type="radio"]');
            if (firstInput) {
                firstInput.focus();
            }
        });
        
        // Offcanvas kapandığında
        offcanvasTheme.addEventListener('hidden.bs.offcanvas', function() {
            // Theme değişikliklerini kaydet veya uygula
            if (typeof forceUpdateThemeVariables === 'function') {
                forceUpdateThemeVariables();
            }
        });
    }
    
    // Theme değişikliklerini dinle ve toast mesajı göster
    document.addEventListener('change', function(e) {
        if (e.target.matches('input[name="theme"], input[name="theme-primary"], input[name="theme-font"], input[name="theme-font-size"], input[name="theme-base"], input[name="table-compact"]')) {
            // Theme değişikliği tespit edildi
            if (typeof showToast === 'function') {
                showToast(t('success'), t('theme_updated'), 'success');
            }
        }
    });
});