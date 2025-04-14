/**
 * Studio Editor - Export Action
 * İçerik dışa aktarma işlemlerini yönetir
 */
const StudioExportAction = (function() {
    /**
     * Dışa aktarma düğmesini yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupExportButton(editor) {
        const exportBtn = document.getElementById('export-btn');
        if (exportBtn) {
            // Eski listener'ları temizle
            const newExportBtn = exportBtn.cloneNode(true);
            if (exportBtn.parentNode) {
                exportBtn.parentNode.replaceChild(newExportBtn, exportBtn);
            }
            
            newExportBtn.addEventListener('click', function() {
                showExportModal(editor);
            });
        }
    }
    
    /**
     * Dışa aktarma modalını gösterir
     * @param {Object} editor - GrapesJS editor örneği
     */
    function showExportModal(editor) {
        // Daha önce oluşturulmuş bir modal varsa kaldır
        const existingModal = document.getElementById('exportModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // HTML, CSS ve JS içeriğini al
        const html = StudioCore.prepareContentForSave(editor);
        const css = StudioCore.prepareCssForSave(editor);
        const jsContentEl = document.getElementById('js-content');
        const js = jsContentEl ? jsContentEl.value : '';
        
        // Tam HTML sayfası oluştur
        const exportContent = generateExportableHTML(html, css, js);
        
        // Modalı oluştur
        setupExportModal(exportContent);
    }
    
    /**
     * Dışa aktarma içeriğini oluşturur
     * @param {string} html - HTML içeriği
     * @param {string} css - CSS içeriği
     * @param {string} js - JavaScript içeriği
     * @returns {string} - Dışa aktarılabilir HTML
     */
    function generateExportableHTML(html, css, js) {
        return `<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dışa Aktarılan Sayfa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
${css}
    </style>
</head>
<body>
${html}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
${js}
    </script>
</body>
</html>`;
    }
    
    /**
     * Dışa aktarma modalını oluşturur
     * @param {string} exportContent - Dışa aktarılacak içerik
     */
    function setupExportModal(exportContent) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'exportModal';
        modal.setAttribute('tabindex', '-1');
        modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">HTML Dışa Aktar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export-content" class="form-label">HTML Kodu</label>
                        <textarea id="export-content" class="form-control font-monospace" rows="20">${exportContent}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-primary" id="copyExportBtn">Kopyala</button>
                    <button type="button" class="btn btn-success" id="downloadExportBtn">İndir</button>
                </div>
            </div>
        </div>`;

        document.body.appendChild(modal);

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();

            // Kopyala butonu işlevi
            document.getElementById('copyExportBtn').addEventListener('click', function() {
                const exportContent = document.getElementById('export-content');
                exportContent.select();
                document.execCommand('copy');
                StudioUtils.showNotification('Başarılı', 'İçerik panoya kopyalandı.', 'success');
            });

            // İndir butonu işlevi
            document.getElementById('downloadExportBtn').addEventListener('click', function() {
                const content = document.getElementById('export-content').value;
                const blob = new Blob([content], { type: 'text/html' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'sayfa_export.html';
                a.click();
                URL.revokeObjectURL(url);
            });

            modal.addEventListener('hidden.bs.modal', function() {
                modal.remove();
            });
        } else {
            // Bootstrap yoksa basit modalı göster
            modal.style.display = 'block';
            
            // Kapat butonları
            const closeButtons = modal.querySelectorAll('.btn-close, .btn-secondary');
            closeButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    document.body.removeChild(modal);
                });
            });
            
            // Kopyala butonu
            document.getElementById('copyExportBtn').addEventListener('click', function() {
                const exportContent = document.getElementById('export-content');
                exportContent.select();
                document.execCommand('copy');
                alert('İçerik panoya kopyalandı.');
            });
            
            // İndir butonu
            document.getElementById('downloadExportBtn').addEventListener('click', function() {
                const content = document.getElementById('export-content').value;
                const blob = new Blob([content], { type: 'text/html' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'sayfa_export.html';
                a.click();
                URL.revokeObjectURL(url);
            });
        }
    }
    
    // Dışa aktarılan API
    return {
        setupExportButton,
        showExportModal,
        generateExportableHTML
    };
})();

// Global olarak kullanılabilir yap
window.StudioExportAction = StudioExportAction;