// Widget Management Form Builder Drag-Drop İşlemleri
// Global değişkenler - sayfa yüklenmeden önce tanımla
window.widgetFormBuilderDragDropInitialized = window.widgetFormBuilderDragDropInitialized || false;

window.initializeSortable = function() {
  // Eğer zaten başlatıldıysa, tekrar başlatma
  if (window.widgetSortableInitialized) {
    return;
  }
  window.widgetSortableInitialized = true;
  if (!window.formCanvas) {
    console.error('Form canvas bulunamadı');
    return;
  }
  
  // Element Paleti için Sortable - her bir palete kategorisi için Sortable tanımlıyoruz
  document.querySelectorAll(".block-items").forEach((blockItem) => {
    // Eğer zaten bir Sortable örneği varsa onu yok et
    if (blockItem._sortable) {
      blockItem._sortable.destroy();
    }
    
    blockItem._sortable = new Sortable(blockItem, {
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
        evt.from.classList.add("dragging");
        console.log("Palette'den sürükleme başladı:", evt.item.dataset.type);
      },
      onEnd: function (evt) {
        evt.from.classList.remove("dragging");
        console.log("Palette'den sürükleme bitti");
      },
    });
  });

  // Form canvas için Sortable
  if (window.formCanvas._sortable) {
    window.formCanvas._sortable.destroy();
  }
  
  window.formCanvas._sortable = new Sortable(window.formCanvas, {
    group: {
      name: "form",
      put: function(to, from, dragEl) {
        // Tüm elementlerin canvas içinde sürüklenmesine izin ver
        return true;
      }
    },
    animation: 150,
    delay: 50,
    delayOnTouchOnly: true,
    filter: ".empty-canvas",
    onAdd: function (evt) {
      console.log("Canvas'a element eklendi:", evt.item);
      
      if (evt.item.classList.contains("element-palette-item")) {
        const type = evt.item.dataset.type;

        if (type) {
          const properties = window.defaultProperties[type]
            ? JSON.parse(JSON.stringify(window.defaultProperties[type]))
            : {};
          const newElement = window.createFormElement(type, properties);

          if (newElement) {
            evt.item.parentNode.replaceChild(newElement, evt.item);
            window.emptyCanvas.style.display = "none";
            window.selectElement(newElement);

            if (type === "row") {
              setTimeout(function() {
                window.initializeColumnSortables();
              }, 50);
            }

            window.saveState();
          }
        } else {
          evt.item.remove();
        }
      }

      window.checkEmptyCanvas();
    },
    onChange: function () {
      window.checkEmptyCanvas();
      
      clearTimeout(window.saveStateTimeout);
      window.saveStateTimeout = setTimeout(function() {
        window.saveState();
      }, 300);
    },
    onRemove: function () {
      window.checkEmptyCanvas();
      window.saveState();
    },
  });

  window.initializeColumnSortables();
};

// Sütunlar için SortableJS Initialize
window.initializeColumnSortables = function() {
  // Önceki tüm Sortable örneklerini temizle
  const previousColumns = document.querySelectorAll('.column-element[data-sortable-initialized="true"]');
  previousColumns.forEach(column => {
    if (column._sortable) {
      column._sortable.destroy();
      column.removeAttribute('data-sortable-initialized');
    }
  });
  
  // Yeni sütunlar için Sortable başlat
  const columns = document.querySelectorAll(".column-element:not([data-sortable-initialized])");
  
  // Sadece sütun varsa log göster
  if (columns.length > 0) {
    console.log("Toplam " + columns.length + " yeni sütun için Sortable başlatılıyor");
  }
  
  // Sütun yoksa işlem yapma
  if (columns.length === 0) {
    return;
  }
  
  columns.forEach((column) => {
    // Daha güvenli kontrol - _sortable özelliği var mı ve destroy metodu var mı kontrol et
    if (column._sortable && typeof column._sortable.destroy === 'function') {
      try {
        column._sortable.destroy();
      } catch (error) {
        console.warn('Sortable destroy hatası:', error);
      }
    }
    
    // Sütunu başlatıldı olarak işaretle
    column.setAttribute('data-sortable-initialized', 'true');
    
    column._sortable = new Sortable(column, {
      group: {
        name: "column",
        pull: true,
        put: function (to, from, dragEl) {
          // Tüm elementlerin sütun içine sürüklenmesine izin ver
          // Palette'den, diğer sütunlardan veya canvas'tan gelen elementleri kabul et
          return from.el.classList.contains('block-items') || 
                 from.el.classList.contains('column-element') ||
                 from.el.classList.contains('row-element') ||
                 from.el === window.formCanvas;
        }
      },
      animation: 150,
      delay: 50,
      delayOnTouchOnly: true,
      filter: ".column-placeholder",
      fallbackOnBody: true,
      fallbackTolerance: 3,
      ghostClass: "sortable-ghost",
      chosenClass: "sortable-chosen",
      dragClass: "sortable-drag",
      onStart: function (evt) {
        console.log("Sütundan element sürükleme başladı");
        column.classList.add("column-active");
        
        // Element başlangıç pozisyonunu tut
        evt.item._originalParent = column;
        evt.item._originalIndex = evt.oldIndex;
      },
      onEnd: function (evt) {
        console.log("Sütundan element sürükleme bitti");
        column.classList.remove("column-active");
        
        // Element kontrol et - DOM'da yoksa kurtarma işlemi yap
        const isElementInDom = document.body.contains(evt.item);
        console.log("Sürükleme sonrası element DOM'da mı:", isElementInDom);
        
        if (!isElementInDom && evt.item._originalParent) {
          console.warn("Element DOM'dan kayboldu, kurtarılıyor...");
          
          // Sütundaki placeholder'ı kaldır
          const placeholder = column.querySelector(".column-placeholder");
          if (placeholder) {
            placeholder.remove();
          }
          
          // Row içeriğini tamamen yeniden yükle
          setTimeout(function() {
            const rowElement = column.closest(".form-element[data-type='row']");
            if (rowElement && typeof window.updateRowContent === 'function') {
              window.updateRowContent.call(rowElement);
            }
            window.initializeColumnSortables();
          }, 100);
        }
        
        // Durum kaydetme
        window.saveState();
      },
      onAdd: function (evt) {
        console.log("Sütuna element eklendi");
        
        // Placeholder'ı kaldır
        const placeholder = column.querySelector(".column-placeholder");
        if (placeholder) {
          placeholder.remove();
        }

        // Element palette'den ekleniyorsa
        if (evt.item.classList.contains("element-palette-item")) {
          const type = evt.item.dataset.type;

          if (type) {
            try {
              const properties = window.defaultProperties[type]
                ? JSON.parse(JSON.stringify(window.defaultProperties[type]))
                : {};
                
              const newElement = window.createFormElement(type, properties);

              if (newElement) {
                evt.item.parentNode.replaceChild(newElement, evt.item);
                window.selectElement(newElement);
                window.saveState();
              }
            } catch (error) {
              console.error("Element oluşturma hatası:", error);
              evt.item.remove();
            }
          } else {
            evt.item.remove();
          }
        }
      },
      onRemove: function (evt) {
        console.log("Sütundan element çıkarıldı");
        
        // Sütundan element çıkarıldığında
        if (column.children.length === 0) {
          // Sütun boşsa placeholder ekle
          const placeholder = document.createElement("div");
          placeholder.className = "column-placeholder";
          placeholder.innerHTML =
            '<i class="fas fa-plus me-2"></i> Buraya element sürükleyin';
          column.appendChild(placeholder);
        }
      },
      onChange: function () {
        // Debounce uygula
        clearTimeout(column._changeTimeout);
        column._changeTimeout = setTimeout(function() {
          window.saveState();
        }, 100);
      },
    });
  });
};

// DOMContentLoaded olayı için event listener ekle
document.addEventListener("DOMContentLoaded", function() {
  // İlk yükleme 
  setTimeout(() => {
    if (typeof window.initializeSortable === 'function') {
      window.initializeSortable();
    } else {
      console.error("initializeSortable fonksiyonu bulunamadı");
    }
  }, 300);
});