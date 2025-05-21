// Form Builder Drag-Drop İşlemleri
document.addEventListener("DOMContentLoaded", function() {
  // SortableJS Initialize
  window.initializeSortable = function() {
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
          // Sürükleme başladığında sadece bir öğenin sürüklenmesini sağla
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
        put: ["palette", "form"]
      },
      animation: 150,
      filter: ".empty-canvas",
      onAdd: function (evt) {
        console.log("Canvas'a element eklendi:", evt.item);
        
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
        
        // Debounce uygula
        clearTimeout(window.saveStateTimeout);
        window.saveStateTimeout = setTimeout(function() {
          window.saveState();
        }, 300);
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
    
    // Mevcut sortable örneklerini temizle
    columns.forEach((column) => {
      if (column._sortable) {
        column._sortable.destroy();
      }
    });
    
    // Tüm sütunlara yeni Sortable örnekleri oluştur
    columns.forEach((column) => {
      column._sortable = new Sortable(column, {
        group: {
          name: "form",
          pull: true,
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
        ghostClass: "sortable-ghost",
        chosenClass: "sortable-chosen",
        dragClass: "sortable-drag",
        fallbackTolerance: 5,
        onStart: function (evt) {
          console.log("Sütundan element sürükleme başladı");
          column.classList.add("column-active");
          
          // Element başlangıç pozisyonunu tut
          evt.item._originalParent = column;
        },
        onEnd: function (evt) {
          console.log("Sütundan element sürükleme bitti");
          column.classList.remove("column-active");
          
          // Element kontrol et - DOM'da yoksa kurtarma işlemi yap
          const isElementInDom = document.body.contains(evt.item);
          console.log("Sürükleme sonrası element DOM'da mı:", isElementInDom);
          
          if (!isElementInDom && evt.item._originalParent) {
            console.warn("Element DOM'dan kayboldu, kurtarılıyor...");
            // Yeniden ekleyelim, burası row değişmeyeceği için sorun çıkarmaz
            setTimeout(function() {
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
              // Paletten gelen elemanı kalıcı bir form elemanına dönüştür
              const properties = window.defaultProperties[type]
                ? JSON.parse(JSON.stringify(window.defaultProperties[type]))
                : {};
                
              try {
                const newElement = window.createFormElement(type, properties);

                if (newElement) {
                  // Placeholder öğeyi değiştir
                  evt.item.parentNode.replaceChild(newElement, evt.item);

                  // Yeni elementi seç
                  window.selectElement(newElement);

                  // Durum kaydetme
                  window.saveState();
                }
              } catch (error) {
                console.error("Element oluşturma hatası:", error);
              }
            } else {
              // Geçersiz element, kaldırılır
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

          // Durum kaydetme
          window.saveState();
        },
        onChange: function () {
          // Debounce uygula
          clearTimeout(column._changeTimeout);
          column._changeTimeout = setTimeout(function() {
            // Durum kaydetme
            window.saveState();
          }, 100);
        },
      });
    });
    
    console.log("Toplam " + columns.length + " sütun için Sortable başlatıldı");
  };
});