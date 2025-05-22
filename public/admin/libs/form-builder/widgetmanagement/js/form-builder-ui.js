// Widget Management Form Builder UI İşlemleri

// Global değişkenler - sayfa yüklenmeden önce tanımla
window.widgetFormBuilderUIInitialized = window.widgetFormBuilderUIInitialized || false;

document.addEventListener("DOMContentLoaded", function() {
  // Eğer zaten başlatıldıysa, tekrar başlatma
  if (window.widgetFormBuilderUIInitialized) {
    return;
  }
  window.widgetFormBuilderUIInitialized = true;
  // Sayfa yüklendiğinde SortableJS'yi başlat
  if (window.initializeSortable) {
    setTimeout(() => {
      window.initializeSortable();
    }, 500);
  }
  
  // İlk durumu kaydet - undo/redo için
  setTimeout(() => {
    if (window.saveState) {
      window.saveState();
    }
  }, 1000);
  
  // Canvas tıklama ile seçili elementi temizle
  if (window.formCanvas) {
    window.formCanvas.addEventListener("click", function (e) {
      if (
        e.target === window.formCanvas ||
        e.target === window.emptyCanvas ||
        e.target.closest(".empty-canvas")
      ) {
        if (window.clearSelectedElement) {
          window.clearSelectedElement();
        }
      }
    });
  }
  
  // Undo butonu
  const undoBtn = document.getElementById("cmd-undo");
  if (undoBtn) {
    undoBtn.addEventListener("click", function () {
      if (!window.undoStack || !window.formCanvas) return;
      
      if (window.undoStack.length > 1) {
        // Son durumu redoStack'e ekle
        window.redoStack.push(window.undoStack.pop());

        // Önceki durumu yükle
        window.formCanvas.innerHTML = window.undoStack[window.undoStack.length - 1];

        // Butonların durumunu güncelle
        this.disabled = window.undoStack.length <= 1;
        document.getElementById("cmd-redo").disabled = false;

        // SortableJS'yi yeniden başlat ve diğer dinleyicileri ekle
        window.initializeSortable();
        window.checkEmptyCanvas();
      }
    });
  }

  // Redo butonu
  const redoBtn = document.getElementById("cmd-redo");
  if (redoBtn) {
    redoBtn.addEventListener("click", function () {
      if (!window.redoStack || !window.formCanvas) return;
      
      if (window.redoStack.length > 0) {
        // Son redo durumunu al
        const state = window.redoStack.pop();

        // Mevcut durumu undoStack'e ekle
        window.undoStack.push(state);

        // Durumu yükle
        window.formCanvas.innerHTML = state;

        // Butonların durumunu güncelle
        this.disabled = window.redoStack.length === 0;
        document.getElementById("cmd-undo").disabled = false;

        // SortableJS'yi yeniden başlat ve diğer dinleyicileri ekle
        window.initializeSortable();
        window.checkEmptyCanvas();
      }
    });
  }
  
  // Temizle butonu
  const clearBtn = document.getElementById("cmd-clear");
  if (clearBtn) {
    clearBtn.addEventListener("click", function() {
      if (!window.formCanvas) return;
      
      if (window.formCanvas.querySelectorAll(".form-element").length > 0) {
        if (confirm("Form içeriğini tamamen temizlemek istediğinize emin misiniz?")) {
          window.formCanvas.innerHTML = "";
          window.checkEmptyCanvas();
          window.saveState();
          window.clearSelectedElement();
        }
      }
    });
  }
  
  // Bileşen sınırları görünürlüğü
  const visibilityBtn = document.getElementById("sw-visibility");
  if (visibilityBtn) {
    visibilityBtn.addEventListener("click", function() {
      this.classList.toggle("active");
      document.body.classList.toggle("hide-borders");
      
      const elements = document.querySelectorAll(".form-element");
      elements.forEach(el => {
        el.classList.toggle("no-border");
      });
    });
  }
  
  // Kaydet butonu - global değişken olarak tanımla
  if (!window.widgetUiSaveBtn) {
    window.widgetUiSaveBtn = document.getElementById("save-btn");
  }
  
  if (window.widgetUiSaveBtn) {
    window.widgetUiSaveBtn.addEventListener("click", function() {
      if (!window.getFormJSON) return;
      
      const formData = window.getFormJSON();
      console.log("Widget Form Verisi:", formData);
      
      // Form verisi hazır, şimdi kaydet
      const widgetId = document.getElementById('widget-id').value;
      const schemaType = document.getElementById('schema-type').value;
      if (widgetId && schemaType && typeof Livewire !== 'undefined') {
        Livewire.dispatch('save-widget-form-layout', {
          widgetId: widgetId, 
          schemaType: schemaType,
          formData: formData
        });
      } else {
        // Klasik form gönderimi için
        const layoutDataInput = document.getElementById('layout-data');
        if (layoutDataInput) {
          layoutDataInput.value = JSON.stringify(formData);
          const saveForm = document.getElementById('save-form');
          if (saveForm) {
            saveForm.submit();
          }
        }
      }
    });
  }
  
  // Önizleme butonu
  const previewBtn = document.getElementById("preview-btn");
  if (previewBtn) {
    previewBtn.addEventListener("click", function() {
      if (!window.getFormJSON || !window.renderTemplate) return;
      
      const formData = window.getFormJSON();

      // Yeni pencerede göstermek için HTML oluştur
      let previewHtml = `
        <!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Widget Form Önizleme</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
            <style>
                body {
                    background-color: #f9fafb;
                    padding: 20px;
                }
                .preview-container {
                    max-width: 800px;
                    margin: 0 auto;
                    background-color: white;
                    border-radius: 8px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                    padding: 20px;
                }
                .preview-header {
                    margin-bottom: 20px;
                    padding-bottom: 15px;
                    border-bottom: 1px solid #e5e7eb;
                }
            </style>
        </head>
        <body>
            <div class="preview-container">
                <div class="preview-header">
                    <h2>Widget Form Önizleme</h2>
                    <p class="text-muted">Bu bir widget form önizlemesidir.</p>
                </div>
                <form>
        `;

      // Öğelerin HTML içeriğini önizlemeye ekle
      formData.elements.forEach((element) => {
        if (element.type === "row") {
          previewHtml += '<div class="row">';

          element.columns.forEach((column) => {
            previewHtml += `<div class="col-md-${column.width}">`;

            column.elements.forEach((item) => {
              const properties = item.properties;
              previewHtml += window.renderTemplate(
                window.elementTemplates[item.type],
                properties
              );
            });

            previewHtml += "</div>";
          });

          previewHtml += "</div>";
        } else {
          const properties = element.properties;
          previewHtml += window.renderTemplate(
            window.elementTemplates[element.type],
            properties
          );
        }
      });

      previewHtml += `
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Gönder</button>
                        <button type="reset" class="btn btn-outline-secondary ms-2">Sıfırla</button>
                    </div>
                </form>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
        </body>
        </html>
        `;

      // Yeni pencerede aç
      const previewWindow = window.open("", "_blank");
      previewWindow.document.write(previewHtml);
      previewWindow.document.close();
    });
  }
  
  // Form eleman arama işlevi
  const searchInput = document.getElementById('elements-search');
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const searchText = this.value.toLowerCase();
      const elements = document.querySelectorAll('.element-palette-item');
      
      elements.forEach(element => {
        const elementText = element.textContent.toLowerCase();
        if (elementText.includes(searchText) || searchText === '') {
          element.style.display = 'flex';
        } else {
          element.style.display = 'none';
        }
      });
      
      // Eğer bir kategori tüm elemanları gizlenmişse, kategoriyi de gizle
      const categories = document.querySelectorAll('.block-category');
      categories.forEach(category => {
        const visibleElements = category.querySelectorAll('.element-palette-item[style="display: flex;"]');
        if (visibleElements.length === 0) {
          category.style.display = 'none';
        } else {
          category.style.display = 'block';
        }
      });
    });
  }
  
  // Panel ve sekme durumlarını localStorage'dan yükle
  const loadSavedStates = function() {
    // Panel durumları
    const leftPanelCollapsed = localStorage.getItem("widget_form_builder_left_collapsed") === "true";
    const rightPanelCollapsed = localStorage.getItem("widget_form_builder_right_collapsed") === "true";

    const leftPanel = document.querySelector(".panel__left");
    const rightPanel = document.querySelector(".panel__right");

    if (leftPanelCollapsed && leftPanel) {
      leftPanel.classList.add("collapsed");
    }

    if (rightPanelCollapsed && rightPanel) {
      rightPanel.classList.add("collapsed");
    }

    // Sekme durumları
    const leftTab = localStorage.getItem("widget_form_builder_left_tab");
    const rightTab = localStorage.getItem("widget_form_builder_right_tab");

    if (leftTab) {
      const tabEl = document.querySelector(`.panel__left .panel-tab[data-tab="${leftTab}"]`);
      if (tabEl) {
        tabEl.click();
      }
    }

    if (rightTab) {
      const tabEl = document.querySelector(`.panel__right .panel-tab[data-tab="${rightTab}"]`);
      if (tabEl) {
        tabEl.click();
      }
    }
  };
  
  // Durumları yükle
  loadSavedStates();
});