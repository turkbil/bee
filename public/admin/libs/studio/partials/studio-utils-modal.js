/**
 * Studio Editor - Modal Modülü
 * Modal dialog gösterme işlevleri
 */

window.StudioModal = (function() {
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
        
        // Mevcut backdrop'ları temizle
        const backdropElements = document.querySelectorAll('.modal-backdrop');
        backdropElements.forEach(element => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
        });
        
        const modal = document.createElement("div");
        modal.className = "modal fade";
        modal.id = "codeEditModal";
        modal.setAttribute("tabindex", "-1");
        modal.setAttribute("aria-modal", "true");
        modal.setAttribute("role", "dialog");
        modal.innerHTML = `
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center">
                            <i class="fas fa-code text-primary me-2"></i>${title}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="p-2 bg-light border-bottom d-flex justify-content-end">
                            <span class="badge me-2">
                                <i class="fas fa-info-circle me-1"></i>
                                <span id="line-count">0</span> satır
                            </span>
                            <span class="badge">
                                <i class="fas fa-text-width me-1"></i>
                                <span id="char-count">0</span> karakter
                            </span>
                        </div>
                        <textarea id="code-editor" class="form-control font-monospace" style="min-height: 70vh" rows="25">${content}</textarea>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>İptal
                        </button>
                        <button type="button" class="btn btn-primary" id="saveCodeBtn">
                            <i class="fas fa-check me-1"></i>Uygula
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Satır ve karakter sayacı
        const updateCounts = () => {
            const codeEditor = document.getElementById("code-editor");
            if (codeEditor) {
                const text = codeEditor.value;
                const lines = text.split('\n').length;
                const chars = text.length;
                
                document.getElementById("line-count").textContent = lines;
                document.getElementById("char-count").textContent = chars;
            }
        };

        // Bootstrap.Modal nesnesi mevcut mu kontrol et
        if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
            const modalInstance = new bootstrap.Modal(modal, {
                backdrop: 'static',
                keyboard: false
            });
            modalInstance.show();

            const codeEditor = document.getElementById("code-editor");
            if (codeEditor) {
                codeEditor.addEventListener('input', updateCounts);
                // İlk sayımı yap
                updateCounts();
            }

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

            const codeEditor = document.getElementById("code-editor");
            if (codeEditor) {
                codeEditor.addEventListener('input', updateCounts);
                // İlk sayımı yap
                updateCounts();
            }

            const saveBtn = modal.querySelector("#saveCodeBtn");
            if (saveBtn) {
                saveBtn.addEventListener("click", function () {
                    const newCode =
                        document.getElementById("code-editor").value;
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
     * Onay modalı göster
     * @param {string} title - Modal başlığı
     * @param {string} message - Modal mesajı
     * @param {Function} confirmCallback - Onay butonuna tıklandığında çağrılacak fonksiyon
     * @param {Function} cancelCallback - İptal butonuna tıklandığında çağrılacak fonksiyon
     */
    function showConfirmModal(title, message, confirmCallback, cancelCallback) {
        // Mevcut modalı temizle
        const existingModal = document.getElementById("confirmModal");
        if (existingModal) {
            existingModal.remove();
        }
        
        // Mevcut backdrop'ları temizle
        const backdropElements = document.querySelectorAll('.modal-backdrop');
        backdropElements.forEach(element => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
        });
        
        const modal = document.createElement("div");
        modal.className = "modal fade";
        modal.id = "confirmModal";
        modal.setAttribute("tabindex", "-1");
        modal.setAttribute("aria-modal", "true");
        modal.setAttribute("role", "dialog");
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="button" class="btn btn-primary" id="confirmBtn">Onayla</button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Bootstrap.Modal nesnesi mevcut mu kontrol et
        if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();

            document.getElementById("confirmBtn").addEventListener("click", function () {
                if (typeof confirmCallback === 'function') {
                    confirmCallback();
                }
                modalInstance.hide();
            });

            const cancelBtn = modal.querySelector(".btn-secondary");
            if (cancelBtn) {
                cancelBtn.addEventListener("click", function () {
                    if (typeof cancelCallback === 'function') {
                        cancelCallback();
                    }
                });
            }

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

            const confirmBtn = modal.querySelector("#confirmBtn");
            if (confirmBtn) {
                confirmBtn.addEventListener("click", function () {
                    if (typeof confirmCallback === 'function') {
                        confirmCallback();
                    }
                    document.body.removeChild(modal);
                });
            }

            const cancelBtn = modal.querySelector(".btn-secondary");
            if (cancelBtn) {
                cancelBtn.addEventListener("click", function () {
                    if (typeof cancelCallback === 'function') {
                        cancelCallback();
                    }
                    document.body.removeChild(modal);
                });
            }

            const closeBtn = modal.querySelector(".btn-close");
            if (closeBtn) {
                closeBtn.addEventListener("click", function () {
                    if (typeof cancelCallback === 'function') {
                        cancelCallback();
                    }
                    document.body.removeChild(modal);
                });
            }
        }
    }
    
    return {
        showEditModal: showEditModal,
        showConfirmModal: showConfirmModal
    };
})();