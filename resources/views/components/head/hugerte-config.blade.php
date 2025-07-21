{{-- HugeRTE Configuration - Simple & Dark Mode Compatible --}}
<script src="/admin-assets/libs/tinymce/tinymce.min.js?v={{ time() }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // HugeRTE temel yapılandırması
        let options = {
          selector: '#editor, [id^="editor_"]',
          plugins: [
            "advlist",
            "autolink",
            "lists",
            "link",
            "image",
            "charmap",
            "preview",
            "anchor",
            "searchreplace",
            "visualblocks",
            "code",
            "fullscreen",
            "insertdatetime",
            "media",
            "table",
            "help",
            "wordcount",
          ],
          toolbar: "undo redo | formatselect | " +
            "bold italic backcolor | alignleft aligncenter " +
            "alignright alignjustify | bullist numlist outdent indent | " +
            "removeformat | link image media | code preview | fullscreen",
          height: 300,
          menubar: false,
          statusbar: false,
          // License key for HugeRTE
          license_key: "gpl",
          // İçerik stili
          content_style: "body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }",
        };
        
        // Tabler dark mode detection - comprehensive
        function detectDarkMode() {
            // Multiple detection methods
            const tablerTheme = localStorage.getItem("tablerTheme");
            const bodyDataTheme = document.body.getAttribute('data-bs-theme');
            const bodyHasDark = document.body.classList.contains('theme-dark');
            const bodyIsDark = document.body.classList.contains('dark');
            const htmlDataTheme = document.documentElement.getAttribute('data-bs-theme');
            const htmlIsDark = document.documentElement.getAttribute('data-theme');
            
            // Background color check (Tabler dark mode has dark background)
            const bodyBgColor = window.getComputedStyle(document.body).backgroundColor;
            const isBackgroundDark = bodyBgColor && (
                bodyBgColor.includes('rgb(26') || // dark background rgb values
                bodyBgColor.includes('rgb(33') ||
                bodyBgColor.includes('rgb(40') ||
                bodyBgColor === 'rgb(26, 32, 44)' // common dark bg
            );
            
            // Dark mode detection checks
            
            // Comprehensive dark mode detection
            return tablerTheme === "dark" || 
                   bodyDataTheme === "dark" || 
                   htmlDataTheme === "dark" ||
                   htmlIsDark === "dark" ||
                   bodyHasDark || 
                   bodyIsDark ||
                   isBackgroundDark;
        }
        
        const isDarkMode = detectDarkMode();
        
        if (isDarkMode) {
          options.skin = "oxide-dark";
          options.content_css = "dark";
          // Dark mode için ek ayarlar
          options.toolbar_mode = "sliding";
          // Dark theme applied
        } else {
          // Light theme applied
        }
        
        tinymce.init(options);
        
        // Tema değişimini dinle ve editörü güncelle
        function updateEditorTheme() {
            const isDark = detectDarkMode();
            
            // Tüm editör instance'larını güncelle
            tinymce.editors.forEach(editor => {
                if (editor && editor.initialized) {
                    // Editörü kaldır ve yeniden başlat
                    editor.remove();
                }
            });
            
            // Yeni tema ile editörü yeniden başlat
            if (isDark) {
                options.skin = "oxide-dark";
                options.content_css = "dark";
            } else {
                options.skin = "oxide";
                options.content_css = "default";
            }
            
            // Editörü yeniden başlat
            setTimeout(() => {
                tinymce.init(options);
            }, 100);
        }
        
        // Body attribute değişimini izle (Tabler tema değişimi)
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && 
                    (mutation.attributeName === 'data-bs-theme' || 
                     mutation.attributeName === 'class')) {
                    updateEditorTheme();
                }
            });
        });
        
        // Body elementini izlemeye başla
        observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['data-bs-theme', 'class']
        });
        
        // Bootstrap dialog içinde TinyMCE kullanımı için focusin sorununu çöz
        document.addEventListener('focusin', (e) => {
          if (e.target.closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !== null) {
            e.stopImmediatePropagation();
          }
        });
        
    });
</script>