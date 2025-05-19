// Form Builder Drag-Drop İşlemleri
document.addEventListener("DOMContentLoaded", function() {
    // SortableJS Initialize
    window.initializeSortable = function() {
      if (!window.formCanvas) {
        console.error('Form canvas bulunamadı');
        return;
      }
      
      // Element Paleti için Sortable - her bir palette öğesi için ayrı ayrı Sortable tanımlıyoruz
      document.querySelectorAll(".element-palette-item").forEach((item) => {
        new Sortable(item.parentElement, {
          group: {
            name: "palette",
            pull: "clone",
            put: false,
          },
          sort: false,
          animation: 150,
          filter: ".element-category",
          draggable: ".element-palette-item",
          onStart: function (evt) {
            // Sürükleme başladığında sadece bir öğenin sürüklenmesini sağla
            evt.from.classList.add("dragging");
          },
          onEnd: function (evt) {
            evt.from.classList.remove("dragging");
          },
        });
      });
  
      // Form canvas için Sortable
      new Sortable(window.formCanvas, {
        group: {
          name: "form",
          put: true,
        },
        animation: 150,
        filter: ".empty-canvas",
        onAdd: function (evt) {
          if (evt.item.classList.contains("element-palette-item")) {
            const type = evt.item.dataset.type;
  
            if (type) {
              // Paletten gelen elemanı kalıcı bir form elemanına dönüştür
              const properties = window.defaultProperties[type]
                ? JSON.parse(JSON.stringify(window.defaultProperties[type]))
                : {};
              const newElement = window.createFormElement(type, properties);
  
              if (newElement) {
                // Placeholder öğeyi değiştir
                evt.item.parentNode.replaceChild(newElement, evt.item);
  
                // Boş canvas uyarısını her zaman gizle
                window.emptyCanvas.style.display = "none";
  
                // Yeni elementi seç
                window.selectElement(newElement);
  
                // Row elementi ise sürüklenebilir sütunlar oluştur
                if (type === "row") {
                  window.initializeColumnSortables();
                }
  
                // Durum kaydetme
                window.saveState();
              }
            } else {
              // Geçersiz element, kaldırılır
              evt.item.remove();
            }
          }
  
          // Canvas boş mu kontrol et
          window.checkEmptyCanvas();
        },
        onChange: function () {
          // Boş canvas kontrolü
          window.checkEmptyCanvas();
  
          // Durum kaydetme
          window.saveState();
        },
        onRemove: function () {
          // Boş canvas kontrolü
          window.checkEmptyCanvas();
  
          // Durum kaydetme
          window.saveState();
        },
      });
  
      // İlk çağrı
      window.initializeColumnSortables();
    };
  
    // Sütunlar için SortableJS Initialize
    window.initializeColumnSortables = function() {
      const columns = document.querySelectorAll(".column-element");
      columns.forEach((column) => {
        new Sortable(column, {
          group: {
            name: "form",
            put: function (to, from, dragEl) {
              // Row elementi sütun içine eklenemez
              if (dragEl.dataset && dragEl.dataset.type === "row") {
                return false;
              }
              return true;
            },
          },
          animation: 150,
          filter: ".column-placeholder",
          onAdd: function (evt) {
            // Placeholder'ı kaldır
            const placeholder = column.querySelector(".column-placeholder");
            if (placeholder) {
              placeholder.remove();
            }
  
            // Element palette'den ekleniyorsa
            if (evt.item.classList.contains("element-palette-item")) {
              const type = evt.item.dataset.type;
  
              if (type) {
                // Paletten gelen elemanı kalıcı bir form elemanına dönüştür
                const properties = window.defaultProperties[type]
                  ? JSON.parse(JSON.stringify(window.defaultProperties[type]))
                  : {};
                const newElement = window.createFormElement(type, properties);
  
                if (newElement) {
                  // Placeholder öğeyi değiştir
                  evt.item.parentNode.replaceChild(newElement, evt.item);
  
                  // Yeni elementi seç
                  window.selectElement(newElement);
  
                  // Durum kaydetme
                  window.saveState();
                }
              } else {
                // Geçersiz element, kaldırılır
                evt.item.remove();
              }
            }
          },
          onRemove: function (evt) {
            // Sütundan element çıkarıldığında
            if (evt.from.children.length === 0) {
              // Sütun boşsa placeholder ekle
              const placeholder = document.createElement("div");
              placeholder.className = "column-placeholder";
              placeholder.innerHTML =
                '<i class="fas fa-plus me-2"></i> Buraya element sürükleyin';
              evt.from.appendChild(placeholder);
            }
  
            // Durum kaydetme
            window.saveState();
          },
          onChange: function () {
            // Durum kaydetme
            window.saveState();
          },
        });
      });
    };
  });