<script src="/admin-assets/libs/tinymce/tinymce.min.js?v={{ time() }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // TinyMCE yapılandırması
        let options = {
          selector: '#editor, [id^="editor_"]',
          plugins: 'code table lists link image media searchreplace fullscreen preview wordcount visualblocks visualchars nonbreaking importcss charmap emoticons pagebreak accordion autoresize codesample quickbars',
          toolbar: 'code preview | undo redo | blocks | bold italic underline | link image media codesample | alignleft aligncenter alignright alignjustify | bullist numlist | table | accordion | visualblocks visualchars | pagebreak | emoticons | searchreplace | fullscreen | wordcount',
          // Menü özelleştirmesi
          menubar: 'edit view insert format tools table',
          menu: {
            edit: { title: '{{ __('admin.tinymce_edit') }}', items: 'undo redo | cut copy | selectall | searchreplace' },
            view: { title: '{{ __('admin.tinymce_view') }}', items: 'code preview | visualaid visualchars visualblocks | spellchecker | fullscreen' },
            insert: { title: '{{ __('admin.tinymce_insert') }}', items: 'image link media template codesample inserttable | charmap emoticons | accordion | pagebreak nonbreaking' },
            format: { title: '{{ __('admin.tinymce_format') }}', items: 'bold italic underline strikethrough superscript subscript codeformat | formats blockformats fontformats fontsizes align | forecolor backcolor | removeformat' },
            tools: { title: '{{ __('admin.tinymce_tools') }}', items: 'spellchecker spellcheckerlanguage | wordcount' },
            table: { title: '{{ __('admin.tinymce_table') }}', items: 'inserttable | cell row column | tableprops deletetable' }
          },
          // TinyMCE API anahtarı
          api_key: '967fwgtb91olxert4wao3pajp6scq5x58dkxngfta8c9xi88',
          // Ek eklenti ayarları
          quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
          quickbars_insert_toolbar: 'quickimage quicktable',
          autoresize_bottom_margin: 20,
          min_height: 400, // minimum yükseklik 400px olarak ayarlandı
          height: 400, // yükseklik de 400px olarak ayarlandı
          // Upgrade uyarısını ve markalaşmayı kaldır
          promotion: false,
          branding: false,
          // Türkçe dil desteği
          language: 'tr',
          // İçerik stili
          content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; min-height: 400px; }',
          setup: function(editor) {
            editor.on('change', function() {
              editor.save();
              
              // Hem #editor hem de #editor_xx için event dispatch et
              const editorElement = document.getElementById(editor.id);
              if (editorElement) {
                editorElement.dispatchEvent(new Event('input'));
              }
            });
            
            // min-height için veri özniteliğini kontrol et
            const editorElement = document.getElementById(editor.id);
            if (editorElement && editorElement.dataset.minHeight) {
              options.min_height = parseInt(editorElement.dataset.minHeight);
              options.content_style = 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; min-height: ' + options.min_height + 'px; }';
            }
            
            // Dil desteği - editör ID'sinden dil tespiti
            if (editor.id.includes('_')) {
              const language = editor.id.split('_')[1];
              if (language === 'ar') {
                editor.settings.directionality = 'rtl';
              } else {
                editor.settings.directionality = 'ltr';
              }
            }
          }
        };
        
        // Sadeleştirilmiş karanlık mod ayarları
        const isDarkMode = document.querySelector('body').classList.contains('dark') ||
                          document.querySelector('body').getAttribute('data-bs-theme') === 'dark' ||
                          localStorage.getItem("tablerTheme") === "dark";
        
        if (isDarkMode) {
          options.skin = "oxide-dark";
          options.content_css = "dark";
        }

        tinymce.init(options);
        
        // Bootstrap dialog içinde TinyMCE kullanımı için focusin sorununu çöz
        document.addEventListener('focusin', (e) => {
          if (e.target.closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !== null) {
            e.stopImmediatePropagation();
          }
        });
        
        // Livewire yeniden yükleme olayını dinle
        if (typeof window.Livewire !== 'undefined') {
          window.Livewire.hook('message.processed', function() {
            // Tüm editor'ları kontrol et
            const editors = document.querySelectorAll('#editor, [id^="editor_"]');
            
            editors.forEach(function(editorElement) {
              if (!tinymce.get(editorElement.id)) {
                setTimeout(function() {
                  tinymce.init(options);
                }, 100);
              }
            });
          });
        }
    });
</script>