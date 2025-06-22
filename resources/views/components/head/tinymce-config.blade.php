<script src="{{ asset('admin-assets/libs/tinymce/tinymce.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // TinyMCE yapılandırması
        let options = {
          selector: '#editor',
          plugins: 'code table lists link image media searchreplace fullscreen preview wordcount visualblocks visualchars nonbreaking importcss charmap emoticons pagebreak accordion autoresize codesample quickbars',
          toolbar: 'code preview | undo redo | blocks | bold italic underline | link image media codesample | alignleft aligncenter alignright alignjustify | bullist numlist | table | accordion | visualblocks visualchars | pagebreak | emoticons | searchreplace | fullscreen | wordcount',
          // Menü özelleştirmesi
          menubar: 'edit view insert format tools table',
          menu: {
            edit: { title: 'Düzenle', items: 'undo redo | cut copy | selectall | searchreplace' },
            view: { title: 'Görüntüle', items: 'code preview | visualaid visualchars visualblocks | spellchecker | fullscreen' },
            insert: { title: 'Ekle', items: 'image link media template codesample inserttable | charmap emoticons | accordion | pagebreak nonbreaking' },
            format: { title: 'Biçim', items: 'bold italic underline strikethrough superscript subscript codeformat | formats blockformats fontformats fontsizes align | forecolor backcolor | removeformat' },
            tools: { title: 'Araçlar', items: 'spellchecker spellcheckerlanguage | wordcount' },
            table: { title: 'Tablo', items: 'inserttable | cell row column | tableprops deletetable' }
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
              document.getElementById('editor').dispatchEvent(new Event('input'));
            });
            
            // min-height için veri özniteliğini kontrol et
            const editorElement = document.getElementById('editor');
            if (editorElement && editorElement.dataset.minHeight) {
              options.min_height = parseInt(editorElement.dataset.minHeight);
              options.content_style = 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; min-height: ' + options.min_height + 'px; }';
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
            if (!document.getElementById('editor')) return;
            
            if (!tinymce.get('editor')) {
              setTimeout(function() {
                tinymce.init(options);
              }, 100);
            }
          });
        }
    });
</script>