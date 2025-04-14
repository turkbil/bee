/**
 * Studio Editor - Toolbar Manager
 * Araç çubuğu işlevselliğini yönetir
 */
const StudioToolbarManager = (function() {
    /**
     * Araç çubuğunu kurar
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupToolbar(editor) {
        if (!editor) {
            console.error('Editor örneği geçersiz');
            return;
        }
        
        console.log('Araç çubuğu kuruluyor...');
        
        initButtons(editor);
        setupDeviceSwitcher(editor);
    }
    
    /**
     * Araç çubuğu butonlarını başlatır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function initButtons(editor) {
        // Butonları yeniden tanımlayarak eski dinleyicileri temizle
        function resetButton(id) {
            const btn = document.getElementById(id);
            if (btn) {
                const newBtn = btn.cloneNode(true);
                if (btn.parentNode) {
                    btn.parentNode.replaceChild(newBtn, btn);
                }
                return newBtn;
            }
            return null;
        }
        
        // Bileşen sınırlarını göster/gizle
        const swVisibility = resetButton('sw-visibility');
        if (swVisibility) {
            swVisibility.addEventListener('click', function() {
                editor.runCommand('sw-visibility');
                this.classList.toggle('active');
            });
        }
        
        // İçeriği temizle
        const cmdClear = resetButton('cmd-clear');
        if (cmdClear) {
            cmdClear.addEventListener('click', function() {
                if (confirm('İçeriği temizlemek istediğinize emin misiniz? Bu işlem geri alınamaz.')) {
                    editor.runCommand('core:canvas-clear');
                }
            });
        }
        
        // Geri al
        const cmdUndo = resetButton('cmd-undo');
        if (cmdUndo) {
            cmdUndo.addEventListener('click', function() {
                editor.runCommand('core:undo');
            });
        }
        
        // Yinele
        const cmdRedo = resetButton('cmd-redo');
        if (cmdRedo) {
            cmdRedo.addEventListener('click', function() {
                editor.runCommand('core:redo');
            });
        }
        
        // HTML kodu düzenle
        const cmdCodeEdit = resetButton('cmd-code-edit');
        if (cmdCodeEdit) {
            cmdCodeEdit.addEventListener('click', function() {
                const htmlContent = editor.getHtml();
                StudioUtils.showEditModal('HTML Düzenle', htmlContent, (newHtml) => {
                    editor.setComponents(newHtml);
                });
            });
        }
        
        // CSS kodu düzenle
        const cmdCssEdit = resetButton('cmd-css-edit');
        if (cmdCssEdit) {
            cmdCssEdit.addEventListener('click', function() {
                const cssContent = editor.getCss();
                StudioUtils.showEditModal('CSS Düzenle', cssContent, (newCss) => {
                    editor.setStyle(newCss);
                });
            });
        }
        
        // JavaScript kodu düzenle
        const cmdJsEdit = resetButton('cmd-js-edit');
        if (cmdJsEdit) {
            cmdJsEdit.addEventListener('click', function() {
                const jsContentEl = document.getElementById('js-content');
                const jsContent = jsContentEl ? jsContentEl.value : '';
                StudioUtils.showEditModal('JavaScript Düzenle', jsContent, (newJs) => {
                    if (jsContentEl) {
                        jsContentEl.value = newJs;
                    }
                });
            });
        }
        
        // Dışa aktar butonu
        if (window.StudioExportAction && typeof window.StudioExportAction.setupExportButton === 'function') {
            window.StudioExportAction.setupExportButton(editor);
        }
        
        // Önizleme butonu
        if (window.StudioPreviewAction && typeof window.StudioPreviewAction.setupPreviewButton === 'function') {
            window.StudioPreviewAction.setupPreviewButton(editor);
        }
        
        // Kaydet butonu
        if (window.StudioSaveAction && typeof window.StudioSaveAction.setupSaveButton === 'function') {
            window.StudioSaveAction.setupSaveButton(editor, {
                moduleType: editor.Canvas.getBody().closest('#gjs').getAttribute('data-module-type'),
                moduleId: parseInt(editor.Canvas.getBody().closest('#gjs').getAttribute('data-module-id'))
            });
        }
        
        console.log('Araç çubuğu butonları başlatıldı.');
    }
    
    /**
     * Cihaz görünümü değiştiriciyi kurar
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupDeviceSwitcher(editor) {
        // Butonları yeniden tanımlayarak eski dinleyicileri temizle
        function resetButton(id) {
            const btn = document.getElementById(id);
            if (btn) {
                const newBtn = btn.cloneNode(true);
                if (btn.parentNode) {
                    btn.parentNode.replaceChild(newBtn, btn);
                }
                return newBtn;
            }
            return null;
        }
        
        // Masaüstü görünümü
        const deviceDesktop = resetButton('device-desktop');
        if (deviceDesktop) {
            deviceDesktop.addEventListener('click', function() {
                handleDeviceSwitch(editor, 'Masaüstü', this);
            });
        }
        
        // Tablet görünümü
        const deviceTablet = resetButton('device-tablet');
        if (deviceTablet) {
            deviceTablet.addEventListener('click', function() {
                handleDeviceSwitch(editor, 'Tablet', this);
            });
        }
        
        // Mobil görünümü
        const deviceMobile = resetButton('device-mobile');
        if (deviceMobile) {
            deviceMobile.addEventListener('click', function() {
                handleDeviceSwitch(editor, 'Mobil', this);
            });
        }
        
        console.log('Cihaz görünümü değiştirici kuruldu.');
    }
    
    /**
     * Cihaz görünümü değişikliğini işler
     * @param {Object} editor - GrapesJS editor örneği
     * @param {string} deviceName - Cihaz adı
     * @param {HTMLElement} clickedButton - Tıklanan buton
     */
    function handleDeviceSwitch(editor, deviceName, clickedButton) {
        // Cihaz görünümünü değiştir
        editor.setDevice(deviceName);
        
        // Buton durumunu güncelle
        const deviceButtons = document.querySelectorAll('.device-btns button');
        deviceButtons.forEach(btn => {
            btn.classList.remove('active');
        });
        
        if (clickedButton) {
            clickedButton.classList.add('active');
        }
        
        console.log(`"${deviceName}" görünümüne geçildi.`);
    }
    
    // Dışa aktarılan API
    return {
        setupToolbar,
        initButtons,
        setupDeviceSwitcher,
        handleDeviceSwitch
    };
})();

// Global olarak kullanılabilir yap
window.StudioToolbarManager = StudioToolbarManager;