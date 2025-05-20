/**
 * Studio Editor - Blok Kategorileri Modülü
 * Blok kategorilerini yönetme
 */

window.StudioBlockCategories = (function() {
    // Kategorilerin başlatılıp başlatılmadığını takip et
    let categoriesInitialized = false;
    let categoriesStatesLoaded = false;
    
    /**
     * Blok kategorilerini başlat
     */
    function initializeBlockCategories() {
        // Bu fonksiyon sadece bir kez çalışmalı
        if (categoriesInitialized) {
            console.log("Blok kategorileri zaten başlatılmış, tekrar başlatma atlanıyor.");
            return;
        }
        categoriesInitialized = true;
        
        const categories = document.querySelectorAll('.block-category-header');
        
        categories.forEach(category => {
            // Tıklama olayını ekle - event izleyerek çakışmayı önle
            category.addEventListener('click', function(event) {
                // Eğer olay zaten işlendiyse, tekrar işleme
                if (event._categoryHandled) return;
                event._categoryHandled = true;
                
                const parent = this.closest('.block-category');
                if (!parent) return;
                
                parent.classList.toggle('collapsed');

                const content = parent.querySelector('.block-items');
                if (content) {
                    if (parent.classList.contains('collapsed')) {
                        content.style.display = 'none';
                    } else {
                        content.style.display = 'grid';
                    }
                }
                
                // Kategori durumlarını kaydet
                saveBlockCategoryStates();
            });
        });
        
        // Kategori durumlarını yükle (ilk kez yüklenmediyse)
        if (!categoriesStatesLoaded) {
            categoriesStatesLoaded = true;
            loadBlockCategoryStates();
        }
    }
    
    /**
     * DOM'da blok kategorileri oluştur
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Object} categories - Kategori bilgileri
     */
    function createBlockCategories(editor, categories) {
        // Bu metod yalnızca bir kez çalışmalı
        if (window._blockCategoriesCreated) {
            return;
        }
        window._blockCategoriesCreated = true;
        
        const blocksContainer = document.getElementById('blocks-container');
        
        if (!blocksContainer) {
            console.error("Blok container bulunamadı!");
            return;
        }
        
        // Önce içeriği temizle
        blocksContainer.innerHTML = '';
        
        // Her kategori için bir div oluştur
        Object.keys(categories).forEach(categoryId => {
            const categoryName = categories[categoryId];
            const categoryIcon = getCategoryIcon(categoryId);
            
            const categoryDiv = document.createElement('div');
            categoryDiv.className = 'block-category';
            categoryDiv.setAttribute('data-category', categoryId);
            
            categoryDiv.innerHTML = `
                <div class="block-category-header">
                    <i class="${categoryIcon} text-primary"></i>
                    <span>${categoryName}</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <div class="block-items"></div>
            `;
            
            blocksContainer.appendChild(categoryDiv);
            
            // Kategori başlığına tıklama olayı ekle - ancak sadece bir kez
            const header = categoryDiv.querySelector('.block-category-header');
            header.addEventListener('click', function(e) {
                // Zaten bu olaya yanıt veriliyorsa işleme devam etme
                if (e._categoryClickHandled) return;
                e._categoryClickHandled = true;
                
                const parent = this.closest('.block-category');
                if (!parent) return;

                parent.classList.toggle('collapsed');
                const itemsContainer = parent.querySelector('.block-items');
                if (itemsContainer) {
                    itemsContainer.style.display = parent.classList.contains('collapsed') ? 'none' : 'grid';
                }

                // Kategori durumlarını localStorage'a kaydet
                saveBlockCategoryStates();
            });
        });
    }
    
    /**
     * Kategori açılıp kapanma durumlarını localStorage'a kaydet
     */
    function saveBlockCategoryStates() {
        // Eğer bir zamanlayıcı zaten aktifse onu temizle ve yeni bir tane oluştur
        if (window._saveBlockCategoriesTimeout) {
            clearTimeout(window._saveBlockCategoriesTimeout);
        }
        
        window._saveBlockCategoriesTimeout = setTimeout(() => {
            const categories = document.querySelectorAll('.block-category');
            const states = {};
            
            categories.forEach(category => {
                const categoryId = category.getAttribute('data-category');
                if (categoryId) {
                    states[categoryId] = category.classList.contains('collapsed');
                }
            });
            
            localStorage.setItem('studio_block_categories', JSON.stringify(states));
        }, 300);
    }
    
    /**
     * Kategori açılıp kapanma durumlarını localStorage'dan yükle
     */
    function loadBlockCategoryStates() {
        // Bu metod yalnızca bir kez çalışmalı
        if (categoriesStatesLoaded) {
            return;
        }
        categoriesStatesLoaded = true;
        
        const savedStates = localStorage.getItem('studio_block_categories');
        if (!savedStates) return;
        
        try {
            const states = JSON.parse(savedStates);
            const categories = document.querySelectorAll('.block-category');
            
            categories.forEach(category => {
                const categoryId = category.getAttribute('data-category');
                if (categoryId && states[categoryId] !== undefined) {
                    if (states[categoryId]) {
                        category.classList.add('collapsed');
                        const content = category.querySelector('.block-items');
                        if (content) {
                            content.style.display = 'none';
                        }
                    } else {
                        category.classList.remove('collapsed');
                        const content = category.querySelector('.block-items');
                        if (content) {
                            content.style.display = 'grid';
                        }
                    }
                }
            });
        } catch (e) {
            console.error('Block category states could not be loaded:', e);
        }
    }
    
    /**
     * Kategori için ikon sınıfını al
     * @param {string} categoryId - Kategori ID'si
     * @return {string} - İkon sınıfı
     */
    function getCategoryIcon(categoryId) {
        // Eğer kategoriye özel ikon bilgisi varsa
        const categoryData = window.studioCategories && window.studioCategories[categoryId];
        if (categoryData && categoryData.icon) {
            return categoryData.icon;
        }
        
        // Varsayılan ikonlar (sadece fallback için)
        const fallbackIcons = {
            'active-widgets': 'fa fa-star'
        };
        
        return fallbackIcons[categoryId] || 'fa fa-cube';
    }
    
    return {
        initializeBlockCategories: initializeBlockCategories,
        createBlockCategories: createBlockCategories,
        saveBlockCategoryStates: saveBlockCategoryStates,
        loadBlockCategoryStates: loadBlockCategoryStates,
        getCategoryIcon: getCategoryIcon
    };
})();