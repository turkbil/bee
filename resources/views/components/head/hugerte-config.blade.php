{{-- HugeRTE Configuration - Simple & Dark Mode Compatible --}}
<script src="/admin-assets/libs/hugerte/hugerte.min.js?v={{ time() }}"></script>
<script>
    // ENHANCED: Sayfa yüklenmeden önce dark mode kontrolü - multiple fallbacks
    function getInitialDarkMode() {
        try {
            // 1. localStorage kontrolü (primary)
            const tablerTheme = localStorage.getItem('tablerTheme');
            if (tablerTheme === 'dark') return true;
            if (tablerTheme === 'light') return false;
            
            // 2. DOM element kontrolü (secondary)
            if (document.body) {
                const bodyTheme = document.body.getAttribute('data-bs-theme');
                if (bodyTheme === 'dark') return true;
                if (bodyTheme === 'light') return false;
            }
            
            // 3. HTML element kontrolü (tertiary)  
            if (document.documentElement) {
                const htmlTheme = document.documentElement.getAttribute('data-bs-theme');
                if (htmlTheme === 'dark') return true;
                if (htmlTheme === 'light') return false;
            }
            
            // 4. CSS class kontrolü (fallback)
            if (document.body?.classList.contains('theme-dark')) return true;
            if (document.documentElement?.classList.contains('theme-dark')) return true;
            
            // 5. System preference (ultimate fallback)
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return true;
            }
            
            return false;
        } catch (e) {
            console.warn('Dark mode detection failed:', e);
            return false;
        }
    }

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
          // İçerik stili - base styles
          content_style: "body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }",
          
          // Livewire sync için setup callback
          setup: function(editor) {
            // Editor ready olduğunda Livewire sync kur
            editor.on('init', function() {
              
              // Real-time sync - content değiştiğinde
              editor.on('input change keyup', function() {
                const lang = editor.id.replace('editor_', '');
                const hiddenInput = document.getElementById('hidden_body_' + lang);
                
                if (hiddenInput) {
                  hiddenInput.value = editor.getContent();
                  hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
              });
            });
          },
        };
        
        // FIXED: Immediate dark mode detection for page load
        const isDarkMode = getInitialDarkMode();
        
        // Apply dark mode styles immediately
        function applyDarkModeStyles(opts, isDark) {
            if (isDark) {
                opts.skin = "oxide-dark";
                opts.content_css = "dark";
                // Dark mode için özel CSS override
                opts.content_style = "body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }" + `
                  body { 
                    background-color: #1a1a1a !important; 
                    color: #e3e3e3 !important; 
                  }
                  p { color: #e3e3e3 !important; }
                  h1, h2, h3, h4, h5, h6 { color: #ffffff !important; }
                  a { color: #4dabf7 !important; }
                  blockquote { 
                    border-left: 4px solid #495057 !important; 
                    background-color: #212529 !important; 
                    color: #e3e3e3 !important; 
                  }
                  pre, code { 
                    background-color: #212529 !important; 
                    color: #f8f9fa !important; 
                    border: 1px solid #495057 !important; 
                  }
                  table { border-color: #495057 !important; }
                  td, th { 
                    border-color: #495057 !important; 
                    color: #e3e3e3 !important; 
                  }
                  th { background-color: #343a40 !important; }
                  hr { border-color: #495057 !important; }
                `;
            } else {
                opts.skin = "oxide";
                opts.content_css = "default";
            }
            return opts;
        }
        
        // Apply initial theme
        options = applyDarkModeStyles(options, isDarkMode);

        // DOM standards mode kontrolü
        if (document.compatMode !== 'CSS1Compat') {
            console.error('HugeRTE: Document not in standards mode. Current mode:', document.compatMode);
            return;
        }

        hugerte.init(options);
        
        // Tema değişimini dinle ve editörü güncelle
        function updateEditorTheme() {
            const isDark = getInitialDarkMode();
            
            // Tüm editör instance'larını tamamen temizle
            try {
                if (typeof hugerte !== 'undefined') {
                    // Önce tüm editörleri remove et
                    hugerte.remove();
                    
                    // DOM'dan kalan TinyMCE elementlerini temizle
                    document.querySelectorAll('.tox-tinymce, .tox-tinymce-inline, .tox-tinymce-aux').forEach(el => {
                        el.remove();
                    });
                }
            } catch (error) {
                console.warn('HugeRTE editor cleanup failed:', error);
            }
            
            // Use the same styling function
            options = applyDarkModeStyles(options, isDark);
            
            // Editörü yeniden başlat - daha uzun timeout
            setTimeout(() => {
                hugerte.init(options);
            }, 500);
        }
        
        // Tema değişimi için debounced function - çoklu çağrıları önler
        let themeUpdateTimeout;
        function debouncedThemeUpdate() {
            clearTimeout(themeUpdateTimeout);
            themeUpdateTimeout = setTimeout(() => {
                updateEditorTheme();
            }, 500); // 500ms debounce
        }
        
        // Body attribute değişimini izle (Tabler tema değişimi)
        const observer = new MutationObserver((mutations) => {
            let shouldUpdate = false;
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && 
                    (mutation.attributeName === 'data-bs-theme' || 
                     mutation.attributeName === 'class')) {
                    shouldUpdate = true;
                }
            });
            
            if (shouldUpdate) {
                debouncedThemeUpdate();
            }
        });
        
        // Body elementini izlemeye başla
        observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['data-bs-theme', 'class']
        });
        
        // Bootstrap dialog içinde HugeRTE kullanımı için focusin sorununu çöz
        document.addEventListener('focusin', (e) => {
          if (e.target.closest(".tox-hugerte, .tox-hugerte-aux, .moxman-window, .tam-assetmanager-root") !== null) {
            e.stopImmediatePropagation();
          }
        });
        
    });
</script>