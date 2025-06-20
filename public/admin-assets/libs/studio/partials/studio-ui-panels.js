/**
 * Studio Editor - Panel Modülü
 * Panelleri yönetme ve açma/kapama
 */

window.StudioPanels = (function() {
    /**
     * Panel açma/kapama butonlarını ekle ve yapılandır
     */
    function initializePanelToggles() {
        // Sol panel açma/kapama butonu
        createPanelToggle('panel__left', 'fa-chevron-left');
        
        // Sağ panel açma/kapama butonu
        createPanelToggle('panel__right', 'fa-chevron-right');
        
        // Önceki panel durumlarını yükle
        loadPanelStates();
    }
    
    /**
     * Panel toggle butonunu oluştur
     * @param {string} panelClass - Panel sınıfı
     * @param {string} iconClass - İkon sınıfı
     */
    function createPanelToggle(panelClass, iconClass) {
        const panel = document.querySelector(`.${panelClass}`);
        if (!panel) return;
        
        // Zaten bir toggle butonu varsa kaldır
        const existingToggle = panel.querySelector('.panel-toggle');
        if (existingToggle) {
            existingToggle.remove();
        }
        
        // Toggle butonunu oluştur
        const toggleBtn = document.createElement('div');
        toggleBtn.className = 'panel-toggle';
        toggleBtn.innerHTML = `<i class="fas ${iconClass}"></i>`;
        
        // Toggle butonuna tıklama olayı ekle
        toggleBtn.addEventListener('click', function() {
            // Panel durumunu değiştir
            panel.classList.toggle('collapsed');
            
            // Panel durumunu localStorage'a kaydet
            savePanelStates();
        });
        
        // Panele toggle butonunu ekle
        panel.appendChild(toggleBtn);
    }
    
    /**
     * Panel açık/kapalı durumlarını localStorage'a kaydet
     */
    function savePanelStates() {
        // Sol panel durumu
        const leftPanel = document.querySelector('.panel__left');
        const leftCollapsed = leftPanel && leftPanel.classList.contains('collapsed');
        
        // Sağ panel durumu
        const rightPanel = document.querySelector('.panel__right');
        const rightCollapsed = rightPanel && rightPanel.classList.contains('collapsed');
        
        // Durumları localStorage'a kaydet
        localStorage.setItem('studio_left_panel_collapsed', leftCollapsed ? 'true' : 'false');
        localStorage.setItem('studio_right_panel_collapsed', rightCollapsed ? 'true' : 'false');
    }
    
    /**
     * Panel açık/kapalı durumlarını localStorage'dan yükle
     */
    function loadPanelStates() {
        // Sol panel durumu
        const leftPanel = document.querySelector('.panel__left');
        const leftSavedState = localStorage.getItem('studio_left_panel_collapsed');
        
        if (leftPanel && leftSavedState === 'true') {
            leftPanel.classList.add('collapsed');
        } else if (leftPanel) {
            leftPanel.classList.remove('collapsed');
        }
        
        // Sağ panel durumu
        const rightPanel = document.querySelector('.panel__right');
        const rightSavedState = localStorage.getItem('studio_right_panel_collapsed');
        
        if (rightPanel && rightSavedState === 'true') {
            rightPanel.classList.add('collapsed');
        } else if (rightPanel) {
            rightPanel.classList.remove('collapsed');
        }
    }
    
    return {
        initializePanelToggles: initializePanelToggles,
        createPanelToggle: createPanelToggle,
        savePanelStates: savePanelStates,
        loadPanelStates: loadPanelStates
    };
})();