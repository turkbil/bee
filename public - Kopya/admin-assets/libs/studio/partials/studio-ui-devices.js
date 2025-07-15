/**
 * Studio Editor - Cihaz Modülü
 * Duyarlı tasarım cihazlarını yönetme
 */

window.StudioDevices = (function() {
    // Cihaz butonları
    let deviceDesktopBtn = null;
    let deviceTabletBtn = null;
    let deviceMobileBtn = null;
    
    /**
     * Cihaz görünümü değiştirme butonlarını yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupDeviceToggle(editor) {
        deviceDesktopBtn = document.getElementById("device-desktop");
        deviceTabletBtn = document.getElementById("device-tablet");
        deviceMobileBtn = document.getElementById("device-mobile");

        // Tüm butonları temizle ve yeniden oluştur
        function recreateButton(button) {
            if (!button) return null;
            
            const newButton = button.cloneNode(true);
            if (button.parentNode) {
                button.parentNode.replaceChild(newButton, button);
            }
            return newButton;
        }
        
        const newDesktopBtn = recreateButton(deviceDesktopBtn);
        const newTabletBtn = recreateButton(deviceTabletBtn);
        const newMobileBtn = recreateButton(deviceMobileBtn);
        
        deviceDesktopBtn = newDesktopBtn;
        deviceTabletBtn = newTabletBtn;
        deviceMobileBtn = newMobileBtn;

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
        
        // Önceki aktif cihazı yükle
        const savedDevice = localStorage.getItem('studio_active_device');
        if (savedDevice) {
            const activeDeviceBtn = document.getElementById(`device-${savedDevice}`);
            if (activeDeviceBtn) {
                toggleDeviceButtons(activeDeviceBtn);
                editor.setDevice(savedDevice.charAt(0).toUpperCase() + savedDevice.slice(1));
            } else {
                // Varsayılan olarak masaüstünü aktif yap
                if (newDesktopBtn) {
                    toggleDeviceButtons(newDesktopBtn);
                    editor.setDevice("Desktop");
                }
            }
        } else {
            // Varsayılan olarak masaüstünü aktif yap
            if (newDesktopBtn) {
                toggleDeviceButtons(newDesktopBtn);
                editor.setDevice("Desktop");
            }
        }
    }

    /**
     * Cihaz butonlarının aktif durumunu değiştirir
     * @param {HTMLElement} activeBtn - Aktif buton
     */
    function toggleDeviceButtons(activeBtn) {
        // Tüm butonlardan active sınıfını kaldır
        if (deviceDesktopBtn) deviceDesktopBtn.classList.remove("active");
        if (deviceTabletBtn) deviceTabletBtn.classList.remove("active");
        if (deviceMobileBtn) deviceMobileBtn.classList.remove("active");
        
        // Sadece aktif butona active sınıfı ekle
        if (activeBtn) {
            activeBtn.classList.add("active");
        }
        
        // Aktif cihazı localStorage'a kaydet
        if (activeBtn) {
            const deviceId = activeBtn.id.replace('device-', '');
            localStorage.setItem('studio_active_device', deviceId);
        }
    }
    
    return {
        setupDeviceToggle: setupDeviceToggle,
        toggleDeviceButtons: toggleDeviceButtons
    };
})();