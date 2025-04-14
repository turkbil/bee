/**
 * Studio Editor - Olay Modülü
 * Olay yönetimi için merkezi sistem
 */
window.StudioEvents = (function() {
    // Olay dinleyicileri için saklama alanı
    const listeners = {};
    
    /**
     * Olay dinleyicisi ekle
     * @param {string} event - Olay adı
     * @param {Function} callback - Çağrılacak fonksiyon
     */
    function on(event, callback) {
        if (!listeners[event]) {
            listeners[event] = [];
        }
        
        listeners[event].push(callback);
        console.log(`'${event}' olayı için dinleyici eklendi.`);
    }
    
    /**
     * Tek seferlik olay dinleyicisi ekle
     * @param {string} event - Olay adı
     * @param {Function} callback - Çağrılacak fonksiyon
     */
    function once(event, callback) {
        function onceCallback(...args) {
            off(event, onceCallback);
            callback.apply(this, args);
        }
        
        on(event, onceCallback);
    }
    
    /**
     * Olay dinleyicisini kaldır
     * @param {string} event - Olay adı
     * @param {Function} callback - Kaldırılacak callback
     */
    function off(event, callback) {
        if (!listeners[event]) {
            return;
        }
        
        if (!callback) {
            delete listeners[event];
            return;
        }
        
        const index = listeners[event].indexOf(callback);
        if (index !== -1) {
            listeners[event].splice(index, 1);
            console.log(`'${event}' olayı için bir dinleyici kaldırıldı.`);
        }
    }
    
    /**
     * Olayı tetikle
     * @param {string} event - Olay adı
     * @param {...any} args - Dinleyicilere geçirilecek argümanlar
     */
    function trigger(event, ...args) {
        if (!listeners[event]) {
            return;
        }
        
        console.log(`'${event}' olayı tetikleniyor. Dinleyici sayısı: ${listeners[event].length}`);
        
        // Orijinal diziyi kopyala (tetikleme sırasında değişebilir)
        const callbacks = [...listeners[event]];
        
        callbacks.forEach(callback => {
            try {
                callback(...args);
            } catch (error) {
                console.error(`'${event}' olayı işlenirken hata:`, error);
            }
        });
    }
    
    /**
     * Örnek olayları başlat
     */
    function setupDefaultEvents() {
        // Editor yüklendi olayı
        on('editor:loaded', (editor) => {
            console.log('Editor yüklendi olayı işleniyor...');
        });
        
        // İçerik değişti olayı
        on('editor:content:changed', () => {
            console.log('İçerik değişikliği algılandı.');
            // Kaydetme düğmesini aktifleştir
            const saveBtn = document.getElementById('save-btn');
            if (saveBtn) {
                saveBtn.classList.add('btn-pulse');
            }
        });
        
        // İçerik kaydedildi olayı
        on('editor:content:saved', (data) => {
            console.log('İçerik başarıyla kaydedildi:', data);
            // Kaydetme düğmesinin vurgusunu kaldır
            const saveBtn = document.getElementById('save-btn');
            if (saveBtn) {
                saveBtn.classList.remove('btn-pulse');
            }
        });
    }
    
    // Örnek olayları ayarla
    setupDefaultEvents();
    
    return {
        on: on,
        once: once,
        off: off,
        trigger: trigger
    };
})();