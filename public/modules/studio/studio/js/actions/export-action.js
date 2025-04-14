/**
 * Studio Export Action
 * İçeriği dışa aktarma işlemlerini yöneten modül
 */
const StudioExportAction = (function() {
    let editor = null;
    let config = {};
    let exportButton = null;

    /**
     * Dışa aktarma eylemlerini ayarla
     * @param {Object} editorInstance GrapesJS editor örneği
     * @param {Object} options Yapılandırma seçenekleri
     */
    function init(editorInstance, options = {}) {
        editor = editorInstance;
        config = {
            exportButtonId: 'export-btn',
            modalId: 'export-modal',
            formats: ['html', 'css', 'js', 'zip'],
            defaultFormat: 'html',
            ...options
        };

        // Dışa aktarma butonunu ayarla
        setupExportButton();

        // Komut ekle
        editor.Commands.add('export-content', {
            run: () => exportContent()
        });

        console.log('Export Action başlatıldı');
    }

    /**
     * Dışa aktarma butonunu ayarla
     */
    function setupExportButton() {
        exportButton = document.getElementById(config.exportButtonId);
        
        if (exportButton) {
            exportButton.addEventListener('click', function(e) {
                e.preventDefault();
                exportContent();
            });
            
            console.log('Dışa aktarma butonu hazırlandı');
        } else {
            console.warn('Dışa aktarma butonu bulunamadı:', config.exportButtonId);
        }
    }

    /**
     * İçeriği dışa aktar
     */
    function exportContent() {
        // Modal daha önce oluşturulduysa kaldır
        const existingModal = document.getElementById(config.modalId);
        if (existingModal) {
            existingModal.remove();
        }
        
        // Dışa aktarma modalını oluştur
        setupExportModal();
    }

    /**
     * Dışa aktarma modalını oluştur
     */
    function setupExportModal() {
        // HTML içeriğini al
        const htmlContent = generateExportableHTML();
        const cssContent = editor.getCss();
        const jsContent = document.getElementById('js-content')?.value || '';
        
        // Modal HTML yapısı
        const modalHTML = `
        <div class="modal fade" id="${config.modalId}" tabindex="-1" aria-labelledby="${config.modalId}-label" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="${config.modalId}-label">İçeriği Dışa Aktar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="export-format" id="export-html" value="html" checked>
                                <label class="btn btn-outline-primary" for="export-html">HTML</label>
                                
                                <input type="radio" class="btn-check" name="export-format" id="export-css" value="css">
                                <label class="btn btn-outline-primary" for="export-css">CSS</label>
                                
                                <input type="radio" class="btn-check" name="export-format" id="export-js" value="js">
                                <label class="btn btn-outline-primary" for="export-js">JavaScript</label>
                                
                                <input type="radio" class="btn-check" name="export-format" id="export-zip" value="zip">
                                <label class="btn btn-outline-primary" for="export-zip">Tam Sayfa</label>
                            </div>
                        </div>
                        
                        <div class="export-content-wrapper" id="export-content-wrapper">
                            <div class="export-content active" data-format="html">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="m-0">HTML İçerik</h6>
                                    <button type="button" class="btn btn-sm btn-primary copy-btn" data-content="html">
                                        <i class="fas fa-copy"></i> Kopyala
                                    </button>
                                </div>
                                <pre class="bg-light p-3 border rounded" style="max-height: 400px; overflow: auto;"><code class="html">${escapeHtml(htmlContent)}</code></pre>
                            </div>
                            
                            <div class="export-content" data-format="css">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="m-0">CSS İçerik</h6>
                                    <button type="button" class="btn btn-sm btn-primary copy-btn" data-content="css">
                                        <i class="fas fa-copy"></i> Kopyala
                                    </button>
                                </div>
                                <pre class="bg-light p-3 border rounded" style="max-height: 400px; overflow: auto;"><code class="css">${escapeHtml(cssContent)}</code></pre>
                            </div>
                            
                            <div class="export-content" data-format="js">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="m-0">JavaScript İçerik</h6>
                                    <button type="button" class="btn btn-sm btn-primary copy-btn" data-content="js">
                                        <i class="fas fa-copy"></i> Kopyala
                                    </button>
                                </div>
                                <pre class="bg-light p-3 border rounded" style="max-height: 400px; overflow: auto;"><code class="js">${escapeHtml(jsContent)}</code></pre>
                            </div>
                            
                            <div class="export-content" data-format="zip">
                                <div class="text-center py-4">
                                    <p>Tam HTML sayfasını ZIP olarak indirebilirsiniz.</p>
                                    <button type="button" class="btn btn-primary" id="download-zip-btn">
                                        <i class="fas fa-download me-2"></i> ZIP İndir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="button" class="btn btn-primary" id="download-btn">
                            <i class="fas fa-download me-2"></i> İndir
                        </button>
                    </div>
                </div>
            </div>
        </div>
        `;
        
        // Modal'ı body'ye ekle
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Modal örneğini al
        const modalElement = document.getElementById(config.modalId);
        const modal = new bootstrap.Modal(modalElement);
        
        // Modal göster
        modal.show();
        
        // Format seçimi
        const formatInputs = document.querySelectorAll('input[name="export-format"]');
        formatInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Tüm içerikleri gizle
                document.querySelectorAll('.export-content').forEach(el => {
                    el.classList.remove('active');
                });
                
                // Seçilen içeriği göster
                const selectedFormat = this.value;
                document.querySelector(`.export-content[data-format="${selectedFormat}"]`).classList.add('active');
            });
        });
        
        // Kopyalama butonları
        const copyButtons = document.querySelectorAll('.copy-btn');
        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const contentType = this.getAttribute('data-content');
                let contentToCopy = '';
                
                switch (contentType) {
                    case 'html':
                        contentToCopy = htmlContent;
                        break;
                    case 'css':
                        contentToCopy = cssContent;
                        break;
                    case 'js':
                        contentToCopy = jsContent;
                        break;
                }
                
                // Panoya kopyala
                navigator.clipboard.writeText(contentToCopy)
                    .then(() => {
                        // Kopyalandı mesajı
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-check"></i> Kopyalandı';
                        
                        setTimeout(() => {
                            this.innerHTML = originalText;
                        }, 2000);
                    })
                    .catch(err => {
                        console.error('Kopyalama hatası:', err);
                    });
            });
        });
        
        // İndirme butonu
        const downloadBtn = document.getElementById('download-btn');
        downloadBtn.addEventListener('click', function() {
            const selectedFormat = document.querySelector('input[name="export-format"]:checked').value;
            
            switch (selectedFormat) {
                case 'html':
                    downloadFile('index.html', htmlContent, 'text/html');
                    break;
                case 'css':
                    downloadFile('styles.css', cssContent, 'text/css');
                    break;
                case 'js':
                    downloadFile('scripts.js', jsContent, 'text/javascript');
                    break;
                case 'zip':
                    downloadZip();
                    break;
            }
        });
        
        // ZIP indirme butonu
        const downloadZipBtn = document.getElementById('download-zip-btn');
        downloadZipBtn.addEventListener('click', downloadZip);
    }
    
    /**
     * HTML içeriğinden tam sayfa HTML kodu oluştur
     * @returns {string} Tam sayfa HTML
     */
    function generateExportableHTML() {
        // HTML içeriğini al
        const htmlContent = editor.getHtml();
        
        // CSS içeriğini al
        const cssContent = editor.getCss();
        
        // JS içeriğini al
        const jsContent = document.getElementById('js-content')?.value || '';
        
        // Tam sayfa HTML
        return `<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dışa Aktarılmış Sayfa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
${cssContent}
    </style>
</head>
<body>
${htmlContent}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
${jsContent}
    </script>
</body>
</html>`;
    }
    
    /**
     * Dosya indir
     * @param {string} filename Dosya adı
     * @param {string} content Dosya içeriği
     * @param {string} contentType Dosya tipi
     */
    function downloadFile(filename, content, contentType) {
        const blob = new Blob([content], { type: contentType });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
        URL.revokeObjectURL(url);
    }
    
    /**
     * ZIP indir
     */
    function downloadZip() {
        // ZIP kütüphanesi yüklü değilse yükle
        if (typeof JSZip === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js';
            script.onload = createAndDownloadZip;
            document.head.appendChild(script);
        } else {
            createAndDownloadZip();
        }
    }
    
    /**
     * ZIP oluştur ve indir
     */
    function createAndDownloadZip() {
        // HTML içeriğini al
        const htmlContent = generateExportableHTML();
        
        // CSS içeriğini al
        const cssContent = editor.getCss();
        
        // JS içeriğini al
        const jsContent = document.getElementById('js-content')?.value || '';
        
        // Yeni ZIP oluştur
        const zip = new JSZip();
        
        // Dosyaları ekle
        zip.file("index.html", htmlContent);
        zip.file("assets/css/styles.css", cssContent);
        zip.file("assets/js/scripts.js", jsContent);
        
        // ZIP'i oluştur ve indir
        zip.generateAsync({ type: "blob" })
            .then(function(content) {
                const url = URL.createObjectURL(content);
                const a = document.createElement('a');
                a.href = url;
                a.download = "webpage.zip";
                a.click();
                URL.revokeObjectURL(url);
            });
    }
    
    /**
     * HTML karakterlerini kaçış
     * @param {string} html HTML içeriği
     * @returns {string} Kaçış karakterli HTML
     */
    function escapeHtml(html) {
        return html
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        init: init,
        exportContent: exportContent
    };
})();

// Global olarak kullanılabilir yap
window.StudioExportAction = StudioExportAction;