/**
 * Studio Editor - Yardımcı İşlevler Modülü
 * Yardımcı fonksiyonlar ve genel araçlar
 */
window.StudioUtils = (function() {
    /**
     * Bildirim göster
     * @param {string} title - Bildirim başlığı
     * @param {string} message - Bildirim mesajı
     * @param {string} type - Bildirim tipi (success, error, warning, info)
     */
    function showNotification(title, message, type = "success") {
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
                "toast-container position-fixed top-0 end-0 p-3";
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
     * Kod düzenleme modalı göster
     * @param {string} title - Modal başlığı
     * @param {string} content - Düzenlenecek içerik
     * @param {Function} callback - Değişiklik kaydedildiğinde çağrılacak fonksiyon
     */
    function showEditModal(title, content, callback) {
        // Mevcut modalı temizle
        const existingModal = document.getElementById("codeEditModal");
        if (existingModal) {
            existingModal.remove();
        }
        
        const modal = document.createElement("div");
        modal.className = "modal fade";
        modal.id = "codeEditModal";
        modal.setAttribute("tabindex", "-1");
        modal.setAttribute("aria-modal", "true");
        modal.setAttribute("role", "dialog");
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <textarea id="code-editor" class="form-control font-monospace" rows="20">${content}</textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="button" class="btn btn-primary" id="saveCodeBtn">Uygula</button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Bootstrap.Modal nesnesi mevcut mu kontrol et
        if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
            // Önceki modal instance'ları temizle
            const oldModals = document.querySelectorAll('.modal-backdrop');
            oldModals.forEach(oldModal => {
                if (oldModal.parentNode) {
                    oldModal.parentNode.removeChild(oldModal);
                }
            });
            
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();

            document
                .getElementById("saveCodeBtn")
                .addEventListener("click", function () {
                    const newCode = document.getElementById("code-editor").value;
                    callback(newCode);
                    modalInstance.hide();
                });

            modal.addEventListener("hidden.bs.modal", function () {
                modal.remove();
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => {
                    if (backdrop.parentNode) {
                        backdrop.parentNode.removeChild(backdrop);
                    }
                });
            });
        } else {
            // Fallback - basit modal gösterimi
            modal.style.display = "block";
            modal.style.backgroundColor = "rgba(0,0,0,0.5)";

            const saveBtn = modal.querySelector("#saveCodeBtn");
            if (saveBtn) {
                saveBtn.addEventListener("click", function () {
                    const newCode = document.getElementById("code-editor").value;
                    callback(newCode);
                    document.body.removeChild(modal);
                });
            }

            const closeBtn = modal.querySelector(".btn-close");
            if (closeBtn) {
                closeBtn.addEventListener("click", function () {
                    document.body.removeChild(modal);
                });
            }

            const cancelBtn = modal.querySelector(".btn-secondary");
            if (cancelBtn) {
                cancelBtn.addEventListener("click", function () {
                    document.body.removeChild(modal);
                });
            }
        }
    }
    
    /**
     * Rastgele benzersiz ID oluştur
     * @returns {string} - Rastgele oluşturulmuş ID
     */
    function generateUniqueId() {
        return 'studio-' + Math.random().toString(36).substring(2, 11);
    }
    
    /**
     * Dizeyi güvenli bir şekilde HTML'e dönüştür
     * @param {string} str - Dönüştürülecek dize
     * @returns {string} - Güvenli HTML dize
     */
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
    
    /**
     * Widget içeriklerini temizle
     * @param {string} html - Temizlenecek HTML
     * @returns {string} - Temizlenmiş HTML
     */
    function cleanWidgetHtml(html) {
        // Basit temizleme - script etiketlerini kaldır
        return html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
    }
    
    /**
     * AJAX isteği gönder
     * @param {string} url - İstek URL'i
     * @param {Object} data - Gönderilecek veri
     * @param {Function} successCallback - Başarılı olduğunda çağrılacak fonksiyon
     * @param {Function} errorCallback - Hata olduğunda çağrılacak fonksiyon
     */
    function sendRequest(url, data, successCallback, errorCallback) {
        // CSRF token'ı al
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                successCallback?.(data);
                showNotification('Başarılı', data.message || 'İşlem başarıyla tamamlandı.');
            } else {
                throw new Error(data.message || 'Bir hata oluştu.');
            }
        })
        .catch(error => {
            errorCallback?.(error);
            showNotification('Hata', error.message || 'Bir hata oluştu.', 'error');
        });
    }

    return {
        showNotification: showNotification,
        showEditModal: showEditModal,
        generateUniqueId: generateUniqueId,
        escapeHtml: escapeHtml,
        cleanWidgetHtml: cleanWidgetHtml,
        sendRequest: sendRequest
    };
})();