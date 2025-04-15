/**
 * Studio Editor - UI Modülü
 * Modern arayüz işlevleri
 */
window.StudioUI = (function() {
    // Editor örneğini global olarak sakla
    let editorInstance = null;
    
    /**
     * Arayüz olaylarını kaydeder
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupUI(editor) {
        editorInstance = editor;
        
        setupTabs();
        setupDeviceToggle(editor);
        initializeBlockCategories();
    }
    
    /**
     * Tab panellerini yapılandırır
     */
    function setupTabs() {
        const tabs = document.querySelectorAll(".panel-tab");
        const tabContents = document.querySelectorAll(".panel-tab-content");

        tabs.forEach((tab) => {
            // Eski event listener'ları temizle
            const newTab = tab.cloneNode(true);
            if (tab.parentNode) {
                tab.parentNode.replaceChild(newTab, tab);
            }
            
            newTab.addEventListener("click", function () {
                const tabName = this.getAttribute("data-tab");

                // Aktif tab değiştir
                tabs.forEach((t) => t.classList.remove("active"));
                this.classList.add("active");

                // İçeriği değiştir
                tabContents.forEach((content) => {
                    if (content.getAttribute("data-tab-content") === tabName) {
                        content.classList.add("active");
                    } else {
                        content.classList.remove("active");
                    }
                });
            });
        });
    }
    
    /**
     * Blok kategorilerini başlat
     */
    function initializeBlockCategories() {
        const categories = document.querySelectorAll('.block-category-header');

        categories.forEach(category => {
            // Mevcut listener'ı kaldır (varsa)
            const newCategory = category.cloneNode(true);
            if (category.parentNode) {
                category.parentNode.replaceChild(newCategory, category);
            }
            
            // Tıklama olayını ekle
            newCategory.addEventListener('click', function() {
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
            });

            // İlk başta tüm kategoriler açık olsun
            const parent = newCategory.closest('.block-category');
            if (parent) {
                parent.classList.remove('collapsed');
                const content = parent.querySelector('.block-items');
                if (content) {
                    content.style.display = 'grid';
                }
            }
        });
    }
    
    /**
     * Cihaz görünümü değiştirme butonlarını yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupDeviceToggle(editor) {
        const deviceDesktop = document.getElementById("device-desktop");
        const deviceTablet = document.getElementById("device-tablet");
        const deviceMobile = document.getElementById("device-mobile");

        // Tüm butonları temizle ve yeniden oluştur
        function recreateButton(button) {
            if (!button) return null;
            
            const newButton = button.cloneNode(true);
            if (button.parentNode) {
                button.parentNode.replaceChild(newButton, button);
            }
            return newButton;
        }
        
        const newDesktopBtn = recreateButton(deviceDesktop);
        const newTabletBtn = recreateButton(deviceTablet);
        const newMobileBtn = recreateButton(deviceMobile);

        function toggleDeviceButtons(activeBtn) {
            const deviceBtns = document.querySelectorAll(".device-btns button");
            if (deviceBtns) {
                deviceBtns.forEach((btn) => {
                    btn.classList.remove("active");
                });
                if (activeBtn) {
                    activeBtn.classList.add("active");
                }
            }
        }

        if (newDesktopBtn) {
            newDesktopBtn.addEventListener("click", function () {
                editor.setDevice("Desktop");
                toggleDeviceButtons(this);
            });
        }

        if (newTabletBtn) {
            newTabletBtn.addEventListener("click", function () {
                editor.setDevice("Tablet");
                toggleDeviceButtons(this);
            });
        }

        if (newMobileBtn) {
            newMobileBtn.addEventListener("click", function () {
                editor.setDevice("Mobile");
                toggleDeviceButtons(this);
            });
        }
    }
    
    return {
        setupUI: setupUI
    };
})();