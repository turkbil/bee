
// Choices.js Sistemi
function initializeChoices() {
    const choicesElements = document.querySelectorAll('[data-choices]');
    console.log('🎯 INITIALIZE CHOICES - Found elements:', choicesElements.length);
    
    choicesElements.forEach(function(element) {
        if (element.dataset.choicesInitialized) {
            console.log('⏭️ Skipping already initialized element');
            return;
        }
        
        // Listeleme sayfalarındaki TÜM filtreleme selectbox'larını atla - normal select olarak kalsın
        const wireModel = element.getAttribute('wire:model.live') || element.getAttribute('wire:model');
        const listingFilters = [
            'perPage', 'selectedCategory', 'roleFilter', 'statusFilter', 'viewType',
            'typeFilter', 'parentCategoryFilter', 'categoryFilter', 'theme_id'
        ];
        
        if (listingFilters.includes(wireModel)) {
            console.log('⏭️ Skipping listing filter element - will be normal select:', wireModel);
            return;
        }
        
        element.dataset.choicesInitialized = 'true';
        console.log('🎯 Initializing choices for:', element);
        
        const options = {
            searchEnabled: true,
            searchPlaceholderValue: 'Arama yapın...',
            noResultsText: 'Sonuç bulunamadı',
            noChoicesText: 'Seçenek yok',
            itemSelectText: 'Seçmek için tıklayın',
            removeItemButton: false,
            duplicateItemsAllowed: false,
            placeholder: true,
            placeholderValue: 'Seçiniz...',
            searchResultLimit: 10,
            shouldSort: true,
            position: 'bottom',
            // Türkçe ayarları
            loadingText: 'Yükleniyor...',
            addItemText: (value) => `"${value}" eklemek için Enter'a basın`,
            maxItemText: (maxItemCount) => `Sadece ${maxItemCount} değer ekleyebilirsiniz`,
            uniqueItemText: 'Bu değer zaten eklendi',
            customAddItemText: 'Virgül karakteri kullanılamaz',
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
        
        // DEBUG: Element'in name attribute'unu kontrol et
        console.log('🔍 DEBUG Element:', {
            id: element.id,
            name: element.name,
            wireModel: element.getAttribute('wire:model.live'),
            hasName: element.hasAttribute('name'),
            nameValue: element.getAttribute('name')
        });
        
        // Choices.js'in name attribute'unu düzelt
        if (element.getAttribute('wire:model.live') === 'selectedCategory') {
            element.name = 'selectedCategory';
            element.setAttribute('name', 'selectedCategory');
            
            // Choices'ın passedElement'ini de kontrol et
            if (choices.passedElement && choices.passedElement.element) {
                choices.passedElement.element.name = 'selectedCategory';
                choices.passedElement.element.setAttribute('name', 'selectedCategory');
            }
            
            console.log('🔧 Fixed name attribute to:', element.name);
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
                
                // Normal choices debug
                console.log('Choices set value:', {
                    wireModel: element.getAttribute('wire:model.live'),
                    finalValue: finalValue,
                    choicesValue: choices.getValue(true)
                });
                
                if (finalValue && choices.getValue(true) !== finalValue) {
                    choices.setChoiceByValue(finalValue);
                    console.log('Set choices value to:', finalValue);
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
                    console.log('🔄 UPDATE CHOICES VALUE:', {
                        element: element,
                        currentValue: currentValue,
                        choicesValue: choices.getValue(true),
                        wireModel: element.getAttribute('wire:model.live')
                    });
                    if (currentValue && choices.getValue(true) !== currentValue) {
                        console.log('🔄 Updating choices to:', currentValue);
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
        months: [
            "Ocak",
            "Şubat",
            "Mart",
            "Nisan",
            "Mayıs",
            "Haziran",
            "Temmuz",
            "Ağustos",
            "Eylül",
            "Ekim",
            "Kasım",
            "Aralık",
        ],
        weekdaysShort: ["Paz", "Pzt", "Sal", "Çar", "Per", "Cum", "Cmt"],
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
    console.log('🚀 LIVEWIRE UPDATED EVENT');
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
            
            // Icon'u disable et ve loading state
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
                        showToast('Başarılı', data.message, 'success');
                    } else {
                        console.log('✅ ' + data.message);
                    }
                    
                    // Console'a detayları yazdır
                    console.log('Cache temizlendi:', data.cleared);
                } else {
                    // Hata toast'ı göster
                    if (typeof showToast === 'function') {
                        showToast('Hata', data.message, 'error');
                    } else {
                        console.error('❌ ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Cache clear error:', error);
                if (typeof showToast === 'function') {
                    showToast('Hata', 'Cache temizleme sırasında bir hata oluştu', 'error');
                } else {
                    console.error('❌ Cache temizleme sırasında bir hata oluştu');
                }
            })
            .finally(() => {
                // Icon'u tekrar normale döndür
                iconElement.className = originalIcon;
                this.style.pointerEvents = 'auto';
            });
        });
    });
});