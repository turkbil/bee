/**
 * Studio Editor - Bildirim Modülü
 * Bildirim ve uyarı gösterme işlevleri
 */

window.StudioNotification = (function() {
    /**
     * Bildirim göster
     * @param {string} title - Bildirim başlığı
     * @param {string} message - Bildirim mesajı
     * @param {string} type - Bildirim tipi (success, error, warning, info)
     */
    function show(title, message, type = "success") {
        const notif = document.createElement("div");
        notif.className = `toast align-items-center text-white bg-${
            type === "success" ? "success" : 
            type === "error" ? "danger" : 
            type === "warning" ? "warning" : 
            "info"
        } border-0`;
        notif.setAttribute("role", "alert");
        notif.setAttribute("aria-live", "assertive");
        notif.setAttribute("aria-atomic", "true");

        notif.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas ${
                    type === "success" ? "fa-check-circle" : 
                    type === "error" ? "fa-times-circle" : 
                    type === "warning" ? "fa-exclamation-triangle" : 
                    "fa-info-circle"
                } me-2"></i>
                <strong>${title}</strong>: ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
        </div>
        `;

        // Toast container
        let container = document.querySelector(".toast-container");
        if (!container) {
            container = document.createElement("div");
            container.className =
                "toast-container position-fixed bottom-0 end-0 p-3";
            container.style.zIndex = "9999";
            document.body.appendChild(container);
        }

        container.appendChild(notif);

        // Bootstrap Toast API mevcut mu kontrol et
        if (typeof bootstrap !== "undefined" && bootstrap.Toast) {
            const toast = new bootstrap.Toast(notif, {
                autohide: true,
                delay: 3000,
            });
            toast.show();
        } else {
            // Fallback - basit toast gösterimi
            notif.style.display = "block";
            setTimeout(() => {
                notif.style.opacity = "0";
                setTimeout(() => {
                    if (container.contains(notif)) {
                        container.removeChild(notif);
                    }
                }, 300);
            }, 3000);
        }

        // Belli bir süre sonra kaldır
        setTimeout(() => {
            if (container.contains(notif)) {
                container.removeChild(notif);
            }
        }, 3300);
    }
    
    /**
     * Başarı bildirimi göster
     * @param {string} message - Bildirim mesajı
     */
    function success(message) {
        show("Başarılı", message, "success");
    }
    
    /**
     * Hata bildirimi göster
     * @param {string} message - Bildirim mesajı
     */
    function error(message) {
        show("Hata", message, "error");
    }
    
    /**
     * Uyarı bildirimi göster
     * @param {string} message - Bildirim mesajı
     */
    function warning(message) {
        show("Uyarı", message, "warning");
    }
    
    /**
     * Bilgi bildirimi göster
     * @param {string} message - Bildirim mesajı
     */
    function info(message) {
        show("Bilgi", message, "info");
    }
    
    return {
        show: show,
        success: success,
        error: error,
        warning: warning,
        info: info
    };
})();