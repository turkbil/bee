// Widget Management Form Builder Temel Değişkenler ve Fonksiyonlar
// Global değişkenler - sayfa yüklenmeden önce tanımla
window.widgetFormBuilderCoreInitialized = window.widgetFormBuilderCoreInitialized || false;

document.addEventListener("DOMContentLoaded", function() {
    // Eğer zaten başlatıldıysa, tekrar başlatma
    if (window.widgetFormBuilderCoreInitialized) {
        return;
    }
    window.widgetFormBuilderCoreInitialized = true;
    
    // Global değişkenler
    window.elementCounter = window.elementCounter || 0;
    window.selectedElement = window.selectedElement || null;
    window.formCanvas = window.formCanvas || document.getElementById("form-canvas");
    window.emptyCanvas = window.emptyCanvas || document.getElementById("empty-canvas");
    window.elementPalette = window.elementPalette || document.getElementById("element-palette");
    window.propertiesPanel = window.propertiesPanel || document.getElementById("properties-panel");
    window.undoStack = window.undoStack || [];
    window.redoStack = window.redoStack || [];
    
    // Element seçimi
    window.selectElement = function(element) {
      // Önceki seçimi temizle
      if (window.selectedElement) {
        window.selectedElement.classList.remove("selected");
      }
  
      // Yeni elementi seç
      window.selectedElement = element;
      window.selectedElement.classList.add("selected");
  
      // Özellik panelini güncelle
      window.updatePropertiesPanel();
    };
    
    // Seçili elementi temizle
    window.clearSelectedElement = function() {
      if (window.selectedElement) {
        window.selectedElement.classList.remove("selected");
      }
      window.selectedElement = null;
  
      // Özellik panelini sıfırla
      window.propertiesPanel.innerHTML = `
              <div class="text-center p-4">
                  <div class="h1 text-muted mb-3">
                      <i class="fas fa-mouse-pointer"></i>
                  </div>
                  <h3 class="text-muted">Element Seçilmedi</h3>
                  <p class="text-muted">Özelliklerini düzenlemek için bir form elementi seçin.</p>
              </div>
          `;
    };
    
    // Boş canvas kontrolü
    window.checkEmptyCanvas = function() {
      if (window.formCanvas.querySelectorAll(".form-element").length === 0) {
        window.emptyCanvas.style.display = "flex";
      } else {
        window.emptyCanvas.style.display = "none";
      }
    };
    
    // Durum kaydetme
    window.saveState = function() {
      // Formun mevcut içeriğini state olarak kaydet
      if (!window.formCanvas) return;
      
      const state = window.formCanvas.innerHTML;
      window.undoStack.push(state);
      window.redoStack = []; // Yeni bir durum kaydedildiğinde redo stack'i temizle
  
      // Butonların durumunu güncelle
      const undoBtn = document.getElementById("cmd-undo");
      const redoBtn = document.getElementById("cmd-redo");
  
      if (undoBtn) undoBtn.disabled = window.undoStack.length <= 1;
      if (redoBtn) redoBtn.disabled = window.redoStack.length === 0;
    };
    
    // Şablon işleme (Mustache benzeri basit bir işleyici)
    window.renderTemplate = function(template, data) {
      let result = template;

      // Değişken yerleştirme {variable}
      Object.keys(data).forEach((key) => {
        const value = data[key];
        if (typeof value === "string" || typeof value === "number") {
          const regex = new RegExp("{" + key + "}", "g");
          result = result.replace(regex, value);
        }
      });

      // Koşullu özellikler {selected}, {checked}, vb.
      Object.keys(data).forEach((key) => {
        const value = data[key];
        if (typeof value === "boolean" && value === true) {
          const regex = new RegExp("{" + key + "}", "g");
          result = result.replace(regex, "selected");
        } else if (typeof value === "boolean") {
          const regex = new RegExp("{" + key + "}", "g");
          result = result.replace(regex, "");
        }
      });

      // Width değerleri için özel koşullar
      if (data.width) {
        for (let i = 1; i <= 12; i++) {
          const regex = new RegExp("{width" + i + "}", "g");
          result = result.replace(regex, data.width == i ? "selected" : "");
        }
      }

      // Heading size için özel koşullar
      if (data.size) {
        const sizes = ["h1", "h2", "h3", "h4", "h5", "h6"];
        sizes.forEach((size) => {
          const regex = new RegExp("{size" + size + "}", "g");
          result = result.replace(regex, data.size === size ? "selected" : "");
        });
      }
      
      // Hizalama için özel koşullar
      if (data.align) {
        const aligns = ["left", "center", "right", "justify"];
        aligns.forEach((align) => {
          const regex = new RegExp("{align" + align + "}", "g");
          result = result.replace(regex, data.align === align ? "selected" : "");
        });
      }
      
      // Stil için özel koşullar
      if (data.style) {
        const styles = ["solid", "dashed", "dotted", "double"];
        styles.forEach((style) => {
          const regex = new RegExp("{style" + style + "}", "g");
          result = result.replace(regex, data.style === style ? "selected" : "");
        });
      }
      
      // Yükseklik için özel koşullar
      if (data.height) {
        const heights = { "0.5rem": "0_5", "1rem": "1", "2rem": "2", "3rem": "3", "4rem": "4" };
        Object.keys(heights).forEach((height) => {
          const regex = new RegExp("{height" + heights[height] + "}", "g");
          result = result.replace(regex, data.height === height ? "selected" : "");
        });
      }

      // Card display özellikleri için koşullar
      if (data.has_header !== undefined) {
        result = result.replace(/{header_display}/g, data.has_header ? 'block' : 'none');
      }
      
      if (data.has_footer !== undefined) {
        result = result.replace(/{footer_display}/g, data.has_footer ? 'block' : 'none');
      }

      // Row sütun sayısı için özel koşullar - BURADA DİKKAT EDİLMESİ GEREKEN KISIM
      if (data.columns2 !== undefined) {
        const regex = new RegExp("{columns2}", "g");
        result = result.replace(regex, data.columns2 === true ? "selected" : "");
      }
      
      if (data.columns3 !== undefined) {
        const regex = new RegExp("{columns3}", "g");
        result = result.replace(regex, data.columns3 === true ? "selected" : "");
      }
      
      if (data.columns4 !== undefined) {
        const regex = new RegExp("{columns4}", "g");
        result = result.replace(regex, data.columns4 === true ? "selected" : "");
      }

      // Kalan yer tutucuları temizle
      result = result.replace(/{[^{}]+}/g, "");

      return result;
    };
    
    // RGB'den HEX'e dönüştürme yardımcı fonksiyonu
    window.rgbToHex = function(r, g, b) {
      return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    };
    
    // Form Verilerini JSON Olarak Al
    window.getFormJSON = function() {
      const formElements = [];
      const elements = window.formCanvas.querySelectorAll(":scope > .form-element"); // Sadece ilk seviye elementler
  
      elements.forEach((element) => {
        const type = element.dataset.type;
        const properties = element.properties || {};
  
        const elementData = {
          type: type,
          properties: JSON.parse(JSON.stringify(properties)),
        };
  
        // Row elementi içindeki elementleri dahil et
        if (type === "row") {
          const columns = element.querySelectorAll(".column-element");
          elementData.columns = [];
  
          columns.forEach((column) => {
            const columnElements = [];
            const columnItems = column.querySelectorAll(".form-element");
  
            columnItems.forEach((item) => {
              const itemType = item.dataset.type;
              const itemProps = item.properties || {};
  
              columnElements.push({
                type: itemType,
                properties: JSON.parse(JSON.stringify(itemProps)),
              });
            });
  
            elementData.columns.push({
              width: parseInt(column.dataset.width || 6),
              elements: columnElements,
            });
          });
        }
  
        formElements.push(elementData);
      });
  
      return {
        title: "Widget Form Builder",
        elements: formElements,
      };
    };
    
    // JSON'dan form yükle
    window.loadFormFromJSON = function(json) {
      console.log('loadFormFromJSON çağrıldı. Gelen JSON:', json);
      
      if (!window.formCanvas) {
        console.error('Form canvas bulunamadı');
        return;
      }
  
      // Canvas'ı temizle ve önceki elemanları sil
      const existingElements = window.formCanvas.querySelectorAll('.form-element');
      existingElements.forEach(el => el.remove());
      
      // Boş canvas gösterimini kontrol et
      window.checkEmptyCanvas();
      
      // JSON doğru formatta değilse çık
      if (!json || !json.elements || !Array.isArray(json.elements)) {
        console.error('Geçersiz JSON formatı veya elements dizisi bulunamadı');
        window.emptyCanvas.style.display = 'flex';
        return;
      }
      
      // Form elemanlarını yükle
      let elementCount = 0;
      
      json.elements.forEach(element => {
        if (!element.type) {
          console.error('Element tipi bulunamadı:', element);
          return;
        }
        
        // Element properties'lerini kopyala
        const properties = element.properties 
          ? JSON.parse(JSON.stringify(element.properties)) 
          : {};
          
        // Element oluştur
        const formElement = window.createFormElement(element.type, properties);
        
        if (!formElement) {
          console.error('Element oluşturulamadı:', element.type);
          return;
        }
        
        // Row elementi ise sütunları ve içindeki elementleri ekle
        if (element.type === 'row' && element.columns && Array.isArray(element.columns)) {
          const rowElement = formElement.querySelector('.row-element');
          
          if (rowElement) {
            // Eski sütunları temizle
            rowElement.innerHTML = '';
            
            // Yeni sütunları ekle
            element.columns.forEach(column => {
              const columnWidth = column.width || 6;
              const columnDiv = document.createElement('div');
              columnDiv.className = `col-md-${columnWidth} column-element`;
              columnDiv.dataset.width = columnWidth;
              
              // Sütun içindeki elementleri ekle
              if (column.elements && Array.isArray(column.elements)) {
                column.elements.forEach(colElement => {
                  if (!colElement.type) return;
                  
                  const colElementProps = colElement.properties 
                    ? JSON.parse(JSON.stringify(colElement.properties)) 
                    : {};
                    
                  const colFormElement = window.createFormElement(colElement.type, colElementProps);
                  
                  if (colFormElement) {
                    columnDiv.appendChild(colFormElement);
                    elementCount++;
                  }
                });
              } 
              
              // Eğer sütun boşsa placeholder ekle
              if (columnDiv.children.length === 0) {
                const placeholder = document.createElement('div');
                placeholder.className = 'column-placeholder';
                placeholder.innerHTML = '<i class="fas fa-plus me-2"></i> Buraya element sürükleyin';
                columnDiv.appendChild(placeholder);
              }
              
              rowElement.appendChild(columnDiv);
            });
          }
        }
        
        // Elementi canvas'a ekle
        window.formCanvas.appendChild(formElement);
        elementCount++;
      });
      
      // Canvas'da element var mı kontrol et
      if (elementCount > 0) {
        window.emptyCanvas.style.display = 'none';
      } else {
        window.emptyCanvas.style.display = 'flex';
      }
      
      // Sütunlar için sortable'ı yeniden başlat
      window.initializeColumnSortables();
      
      // Durum kaydet (undo/redo için başlangıç durumu)
      window.saveState();
      
      console.log('Form başarıyla yüklendi. Toplam element sayısı:', elementCount);
    };
  
    // Kategori açma/kapama için olay dinleyiciler
    const categoryHeaders = document.querySelectorAll(".block-category-header");
    categoryHeaders.forEach((header) => {
      header.addEventListener("click", function () {
        const category = this.closest(".block-category");
        category.classList.toggle("collapsed");
  
        // Kategori durumunu localStorage'a kaydet
        const categoryName = this.querySelector("span").textContent.trim();
        const categories = JSON.parse(
          localStorage.getItem("widget_form_builder_categories") || "{}"
        );
        categories[categoryName] = category.classList.contains("collapsed");
        localStorage.setItem(
          "widget_form_builder_categories",
          JSON.stringify(categories)
        );
      });
    });
    
    // Kategori durumlarını yükle
    const categories = JSON.parse(localStorage.getItem("widget_form_builder_categories") || "{}");
    Object.keys(categories).forEach((categoryName) => {
      const isCollapsed = categories[categoryName];
      const headers = document.querySelectorAll(".block-category-header");
  
      headers.forEach((header) => {
        const headerText = header.querySelector("span")?.textContent.trim();
        if (headerText === categoryName && isCollapsed) {
          const category = header.closest(".block-category");
          if (category) {
            category.classList.add("collapsed");
            const blockItems = category.querySelector(".block-items");
            if (blockItems) {
              blockItems.style.display = "none";
            }
          }
        }
      });
    });
  });