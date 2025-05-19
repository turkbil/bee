// Form Builder İşlemleri

document.addEventListener("DOMContentLoaded", function() {
  // Ayarları yükle ve dropdown'ları doldur
  window.populateSettingDropdowns = function(groupId) {
    fetch(`/admin/settingmanagement/api/settings?group=${groupId}`)
      .then(response => response.json())
      .then(settings => {
        const settingDropdowns = document.querySelectorAll('select[name="setting_id"]');
        
        settingDropdowns.forEach(dropdown => {
          // Mevcut seçili değeri al
          const currentValue = dropdown.value;
          
          // Dropdown'ı temizle, sadece ilk opsiyonu koru
          const firstOption = dropdown.querySelector('option:first-child');
          dropdown.innerHTML = '';
          dropdown.appendChild(firstOption);
          
          // Yeni seçenekleri ekle
          settings.forEach(setting => {
            const option = document.createElement('option');
            option.value = setting.id;
            option.textContent = `${setting.label} (${setting.key})`;
            
            // Eğer önceden seçili değer vardıysa, onu tekrar seç
            if (currentValue && currentValue == setting.id) {
              option.selected = true;
            }
            
            dropdown.appendChild(option);
          });
        });
      })
      .catch(error => {
        console.error('Ayarlar alınırken hata oluştu:', error);
      });
  };

  // Özellik panelini güncelle
  window.updatePropertiesPanel = function() {
    if (!window.selectedElement) return;

    const type = window.selectedElement.dataset.type;

    // Özellik şablonunu al
    const propTemplate = window.propertyTemplates[type];

    if (!propTemplate) {
      window.propertiesPanel.innerHTML = `
                <div class="alert alert-warning">
                    Bu element tipi için özellik paneli henüz eklenmemiş.
                </div>
            `;
      return;
    }

    // Element özelliklerini al
    let properties = window.selectedElement.properties;

    // Özellikler tanımlı değilse, varsayılan özellikleri kullan
    if (!properties) {
      properties = window.defaultProperties[type]
        ? JSON.parse(JSON.stringify(window.defaultProperties[type]))
        : {};
      window.selectedElement.properties = properties;
    }

    // Genişlik değerlerini kontrol et
    let templateData = Object.assign({}, properties);
    templateData["width" + properties.width] = true;

    // Row için sütun sayısını kontrol et
    if (type === "row" && properties.columns) {
      templateData["columns" + properties.columns.length] = true;
    }
    
    // Heading size için kontrol et
    if (type === "heading" && properties.size) {
      templateData["size" + properties.size] = true;
    }
    
    // Hizalama için kontrol et
    if (properties.align) {
      templateData["align" + properties.align] = true;
    }
    
    // Stil için kontrol et
    if (properties.style) {
      templateData["style" + properties.style] = true;
    }
    
    // Kalınlık için kontrol et
    if (properties.thickness) {
      templateData["thickness" + properties.thickness.replace(".", "_")] = true;
    }
    
    // Yükseklik için kontrol et
    if (properties.height) {
      const heightMap = {
        "0.5rem": "0_5",
        "1rem": "1",
        "2rem": "2",
        "3rem": "3",
        "4rem": "4"
      };
      
      if (heightMap[properties.height]) {
        templateData["height" + heightMap[properties.height]] = true;
      }
    }

    // Şablonu işle
    window.propertiesPanel.innerHTML = window.renderTemplate(propTemplate, templateData);
  
    // Özellik değişikliklerini dinle
    const inputs = window.propertiesPanel.querySelectorAll("input, select, textarea");
    inputs.forEach((input) => {
      input.addEventListener("change", function () {
        window.updateElementProperty(input);
      });

      input.addEventListener("keyup", function () {
        window.updateElementProperty(input);
      });
    });
  
    // Select için özel yönetim
    if (type === "select" || type === "radio") {
      // Seçenekleri ekle
      const optionsContainer = document.getElementById("options-container");
      if (optionsContainer) {
        optionsContainer.innerHTML = "";
  
        if (properties.options && properties.options.length) {
          properties.options.forEach((option, index) => {
            const optionRow = document.createElement("div");
            optionRow.className = "input-group mb-2 option-row";
            const isChecked = option.is_default || properties.default_value === option.value;
            
            optionRow.innerHTML = `
                <div class="input-group-text">
                    <input class="form-check-input" type="checkbox" name="option-default-${index}" ${isChecked ? 'checked' : ''}>
                </div>
                <input type="text" class="form-control" name="option-label-${index}" placeholder="Etiket" value="${option.label || ""}">
                <input type="text" class="form-control" name="option-value-${index}" placeholder="Değer" value="${option.value || ""}">
                <button type="button" class="btn btn-outline-danger remove-option" data-index="${index}">
                    <i class="fas fa-times"></i>
                </button>
            `;
  
            optionsContainer.appendChild(optionRow);
  
            // Değer değişikliklerini dinle
            const inputs = optionRow.querySelectorAll("input");
            inputs.forEach((input) => {
              input.addEventListener("change", function () {
                window.updateOptionValue(index, input);
              });
  
              input.addEventListener("keyup", function () {
                window.updateOptionValue(index, input);
              });
            });
          });
        }
  
        // Var olan seçenek silme butonlarını etkinleştir
        const removeOptionBtns = optionsContainer.querySelectorAll(".remove-option");
        removeOptionBtns.forEach((btn) => {
          btn.addEventListener("click", function () {
            const index = parseInt(this.dataset.index);
            properties.options.splice(index, 1);
            window.updateElementContent();
            window.updatePropertiesPanel();
          });
        });
      }
  
      // Seçenek Ekle butonu
      const addOptionBtn = document.getElementById("add-option");
      if (addOptionBtn) {
        addOptionBtn.addEventListener("click", function () {
          if (!Array.isArray(properties.options)) {
            properties.options = [];
          }
          
          // Eğer başka seçenek yoksa ilk seçenek varsayılan olsun
          const isDefault = properties.options.length === 0;
          
          properties.options.push({
            value: "option" + (properties.options.length + 1),
            label: "Seçenek " + (properties.options.length + 1),
            is_default: isDefault
          });
          
          // Yeni seçenek varsayılansa default_value'yu güncelle
          if (isDefault) {
            properties.default_value = "option" + properties.options.length;
          }
          
          window.updateElementContent();
          window.updatePropertiesPanel();
        });
      }
      
      // Varsayılan değer dropdown'ı
      const defaultValueSelect = window.propertiesPanel.querySelector('select[name="default_value"]');
      if (defaultValueSelect) {
        // Seçenekleri ekle
        defaultValueSelect.innerHTML = '<option value="">Varsayılan değer seçin</option>';
        
        if (properties.options && properties.options.length) {
          properties.options.forEach((option) => {
            const optionElement = document.createElement('option');
            optionElement.value = option.value;
            optionElement.textContent = option.label;
            
            // Varsayılan değeri seç
            if (option.is_default || properties.default_value === option.value) {
              optionElement.selected = true;
            }
            
            defaultValueSelect.appendChild(optionElement);
          });
        }
        
        // Değişikliği dinle
        defaultValueSelect.addEventListener('change', function() {
          const selectedValue = this.value;
          
          // Tüm seçeneklerin varsayılan değerini sıfırla
          properties.options.forEach(option => {
            option.is_default = option.value === selectedValue;
          });
          
          // Varsayılan değeri güncelle
          properties.default_value = selectedValue;
          
          window.updateElementContent();
        });
      }
    }
      
    // Tab Group için özel yönetim
    if (type === "tab_group") {
      // Tab'ları ekle
      const tabsContainer = document.getElementById("tabs-container");
      if (tabsContainer) {
        tabsContainer.innerHTML = "";
  
        if (properties.tabs && properties.tabs.length) {
          properties.tabs.forEach((tab, index) => {
            const tabRow = document.createElement("div");
            tabRow.className = "card mb-2";
            tabRow.innerHTML = `
                <div class="card-body p-2">
                  <div class="input-group mb-2">
                    <span class="input-group-text">Başlık</span>
                    <input type="text" class="form-control" name="tab-title-${index}" value="${tab.title || ''}">
                  </div>
                  <div class="input-group">
                    <span class="input-group-text">İçerik</span>
                    <textarea class="form-control" name="tab-content-${index}" rows="2">${tab.content || ''}</textarea>
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-danger mt-2 remove-tab" data-index="${index}">
                    <i class="fas fa-times me-1"></i> Sekme Sil
                  </button>
                </div>
              `;
              
            tabsContainer.appendChild(tabRow);
              
            // Değer değişikliklerini dinle
            const inputs = tabRow.querySelectorAll("input, textarea");
            inputs.forEach((input) => {
              input.addEventListener("change", function() {
                window.updateTabValue(index, input);
              });
                
              input.addEventListener("keyup", function() {
                window.updateTabValue(index, input);
              });
            });
          });
        }
      }
  
      // Sekme silme butonlarını etkinleştir
      const removeTabBtns = document.querySelectorAll(".remove-tab");
      removeTabBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
          const index = parseInt(this.dataset.index);
          properties.tabs.splice(index, 1);
          window.updateElementContent();
          window.updatePropertiesPanel();
        });
      });
  
      // Sekme Ekle butonu
      const addTabBtn = document.getElementById("add-tab");
      if (addTabBtn) {
        addTabBtn.addEventListener("click", function () {
          if (!Array.isArray(properties.tabs)) {
            properties.tabs = [];
          }
          properties.tabs.push({
            title: "Sekme " + (properties.tabs.length + 1),
            content: "İçerik " + (properties.tabs.length + 1)
          });
          window.updateElementContent();
          window.updatePropertiesPanel();
        });
      }
    }
  
    // Row için özel yönetim
    if (type === "row") {
      // Sütun sayısını değiştirme
      const columnCountSelect = window.propertiesPanel.querySelector(
        '[name="column-count"]'
      );
      if (columnCountSelect) {
        columnCountSelect.addEventListener("change", function () {
          window.updateRowColumns(parseInt(this.value));
        });
      }
  
      // Sütun genişliklerini göster
      const columnWidthsContainer = document.getElementById(
        "column-widths-container"
      );
      if (columnWidthsContainer && properties.columns) {
        columnWidthsContainer.innerHTML = "";
  
        properties.columns.forEach((column, index) => {
          const rowElement = document.createElement("div");
          rowElement.className = "input-group mb-2 column-width-row";
          rowElement.innerHTML = `
                    <span class="input-group-text">Sütun ${
                      column.index
                    }</span>
                    <select class="form-select" name="column-width-${index}">
                        <option value="2" ${
                          column.width == 2 ? "selected" : ""
                        }>2/12</option>
                        <option value="3" ${
                          column.width == 3 ? "selected" : ""
                        }>3/12</option>
                        <option value="4" ${
                          column.width == 4 ? "selected" : ""
                        }>4/12</option>
                        <option value="6" ${
                          column.width == 6 ? "selected" : ""
                        }>6/12</option>
                        <option value="8" ${
                          column.width == 8 ? "selected" : ""
                        }>8/12</option>
                        <option value="10" ${
                          column.width == 10 ? "selected" : ""
                        }>10/12</option>
                    </select>
                `;
  
          columnWidthsContainer.appendChild(rowElement);
  
          // Sütun genişliği değişikliğini dinle
          const select = rowElement.querySelector("select");
          select.addEventListener("change", function () {
            window.updateColumnWidth(index, parseInt(this.value));
          });
        });
      }
    }
      
    // Eğer grup ID'si belirtilmişse ve form element'i bir ayarla ilişkilendirilebilecek 
    // türdeyse (düzen elementleri hariç), ayarları getir
    const formGroupId = document.getElementById('group-id')?.value;
    if (formGroupId && ['text', 'textarea', 'select', 'checkbox', 'radio', 'switch', 
                        'number', 'email', 'date', 'time', 'color', 'file', 'image', 
                        'image_multiple'].includes(type)) {
      window.populateSettingDropdowns(formGroupId);
    }
  };
  
  // Seçenek değerini güncelle
  window.updateOptionValue = function(index, input) {
    if (
      !window.selectedElement ||
      (window.selectedElement.dataset.type !== "select" &&
        window.selectedElement.dataset.type !== "radio")
    )
      return;

    const properties = window.selectedElement.properties;

    if (!properties || !Array.isArray(properties.options)) {
      return;
    }

    const option = properties.options[index];
    if (!option) return;

    if (input.name.startsWith("option-value-")) {
      option.value = input.value;
    } else if (input.name.startsWith("option-label-")) {
      option.label = input.value;
    } else if (input.name.startsWith("option-default-")) {
      // Varsayılan seçimi güncelle
      const isDefault = input.checked;
      
      if (isDefault) {
        // Diğer tüm seçeneklerin varsayılan özelliğini kaldır
        properties.options.forEach((opt, idx) => {
          if (idx !== index) {
            opt.is_default = false;
          }
        });
        
        option.is_default = true;
        properties.default_value = option.value;
      } else {
        option.is_default = false;
        
        // En az bir varsayılan seçenek olduğundan emin ol
        const hasDefault = properties.options.some(opt => opt.is_default);
        if (!hasDefault && properties.options.length > 0) {
          properties.options[0].is_default = true;
          properties.default_value = properties.options[0].value;
        }
      }
    }

    window.updateElementContent();
    
    // Varsayılan değer dropdown'ını güncelle
    const defaultValueSelect = window.propertiesPanel.querySelector('select[name="default_value"]');
    if (defaultValueSelect) {
      // Mevcut varsayılan değeri sakla
      const currentValue = defaultValueSelect.value;
      
      // Dropdown'ı temizle
      defaultValueSelect.innerHTML = '<option value="">Varsayılan değer seçin</option>';
      
      // Seçenekleri ekle
      properties.options.forEach(option => {
        const optionElement = document.createElement("option");
        optionElement.value = option.value;
        optionElement.textContent = option.label;
        
        // Mevcut seçili değer varsa veya option.is_default true ise seç
        if (option.value === properties.default_value || option.is_default) {
          optionElement.selected = true;
        }
        
        defaultValueSelect.appendChild(optionElement);
      });
    }
  };

  // Sekme değerini güncelle
  window.updateTabValue = function(index, input) {
    if (!window.selectedElement || window.selectedElement.dataset.type !== "tab_group") {
      return;
    }

    const properties = window.selectedElement.properties;

    if (!properties || !Array.isArray(properties.tabs)) {
      return;
    }

    const tab = properties.tabs[index];
    if (!tab) return;

    if (input.name.startsWith("tab-title-")) {
      tab.title = input.value;
    } else if (input.name.startsWith("tab-content-")) {
      tab.content = input.value;
    }

    window.updateElementContent();
  };

  // Element özelliklerini güncelle
  window.updateElementProperty = function(input) {
    if (!window.selectedElement) return;

    const name = input.name;
    const value = input.type === "checkbox" ? input.checked : input.value;

    // Özel özellik güncellemeleri
    if (name === "label") {
      window.selectedElement.querySelector(".element-title").textContent = value;
      window.selectedElement.properties.label = value;

      // Form içindeki etiketi güncelle
      const labelElement = window.selectedElement.querySelector(".form-label");
      if (labelElement) {
        labelElement.textContent = value;
      }
    } else if (name === "width") {
      window.selectedElement.dataset.width = value;
      window.selectedElement.properties.width = parseInt(value);

      // Genişlik değişikliğini anında göster
      const width = parseInt(value);
      window.selectedElement.style.width = `${(width * 100) / 12}%`;

      // Element sütun içinde ise sütun genişliğini güncelle
      const columnElement = window.selectedElement.closest(".column-element");
      if (columnElement) {
        columnElement.className = columnElement.className.replace(
          /col-md-\d+/,
          `col-md-${width}`
        );
        columnElement.dataset.width = width;
      }
    } else if (name === "content" && window.selectedElement.dataset.type === "heading") {
      window.selectedElement.querySelector(".element-title").textContent = "Başlık: " + value;
      window.selectedElement.properties.content = value;
      
      // Başlık içeriğini güncelle
      const headingElement = window.selectedElement.querySelector(window.selectedElement.properties.size);
      if (headingElement) {
        headingElement.textContent = value;
      }
    } else if (name === "title" && window.selectedElement.dataset.type === "card") {
      window.selectedElement.querySelector(".element-title").textContent = "Kart: " + value;
      window.selectedElement.properties.title = value;
      
      // Kart başlığını güncelle
      const titleElement = window.selectedElement.querySelector(".card-title");
      if (titleElement) {
        titleElement.textContent = value;
      }
    } else if (name === "default_value_text" && window.selectedElement.dataset.type === "color") {
      // Renk metin kutusundan değişiklik geldiğinde renk seçiciyi de güncelle
      window.selectedElement.properties.default_value = value;
      const colorPicker = window.propertiesPanel.querySelector('input[type="color"][name="default_value"]');
      if (colorPicker) {
        colorPicker.value = value;
      }
    } else if (name === "color_text" && window.selectedElement.dataset.type === "divider") {
      // Ayırıcı çizgi renk metin kutusundan değişiklik geldiğinde renk seçiciyi de güncelle
      window.selectedElement.properties.color = value;
      const colorPicker = window.propertiesPanel.querySelector('input[type="color"][name="color"]');
      if (colorPicker) {
        colorPicker.value = value;
      }
    } else if (name === "setting_id") {
      window.selectedElement.properties.setting_id = value;
    } else {
      // Genel özellik güncelleme
      window.selectedElement.properties[name] = value;
    }

    // Element içeriğini güncelle
    window.updateElementContent();
  };

  // Row sütunlarını güncelle
  window.updateRowColumns = function(columnCount) {
    if (!window.selectedElement || window.selectedElement.dataset.type !== "row") return;

    // Mevcut sütunları al
    let currentColumns = window.selectedElement.properties.columns || [];
    const rowElement = window.selectedElement.querySelector(".row-element");
    const columnElements = rowElement
      ? rowElement.querySelectorAll(".column-element")
      : [];

    // Eğer sütun sayısı azalıyorsa ve dolu sütunlar varsa uyarı ver
    if (columnCount < currentColumns.length) {
      // Mevcut içerik olup olmadığını kontrol et
      let hasContent = false;

      // Silinecek sütunları kontrol et
      for (let i = columnCount; i < columnElements.length; i++) {
        if (
          columnElements[i] &&
          columnElements[i].querySelectorAll(".form-element").length > 0
        ) {
          hasContent = true;
          break;
        }
      }

      if (hasContent) {
        const proceed = confirm(
          "Sütun sayısını azaltırsanız, fazla olan sütunlardaki içerikler kaybolacak. Devam etmek istiyor musunuz?"
        );
        if (!proceed) return;
      }
    }

    // Yeni sütun dizisi oluştur, mevcut sütunların içeriğini koru
    const newColumns = [];

    // Varsayılan genişlik
    const defaultWidth = Math.floor(12 / columnCount);

    // Yeni sütun dizisi oluştur
    for (let i = 0; i < columnCount; i++) {
      if (i < currentColumns.length) {
        // Mevcut sütunu koru
        newColumns.push(currentColumns[i]);
      } else {
        // Yeni sütun ekle
        newColumns.push({
          index: i + 1,
          width: defaultWidth,
        });
      }
    }

    // Row properties'i güncelle
    window.selectedElement.properties.columns = newColumns;

    // Sütun genişliklerini kontrol et ve güncelle
    const rowWidth = newColumns.reduce(
      (sum, col) => sum + parseInt(col.width || 0),
      0
    );
    if (rowWidth > 12) {
      // Genişlikleri orantılı olarak azalt
      const ratio = 12 / rowWidth;
      newColumns.forEach((col) => {
        col.width = Math.max(1, Math.floor(col.width * ratio));
      });
    }

    // Element içeriğini güncelle - mevcut içeriği koru
    window.updateRowContent();

    // Özellik panelini güncelle (sütun genişlikleri için)
    window.updatePropertiesPanel();
    
    // Durum kaydet
    window.saveState();
  };

  // Sütun genişliğini güncelle
  window.updateColumnWidth = function(columnIndex, width) {
    if (!window.selectedElement || window.selectedElement.dataset.type !== "row") return;

    if (
      !window.selectedElement.properties ||
      !Array.isArray(window.selectedElement.properties.columns)
    ) {
      return;
    }

    window.selectedElement.properties.columns[columnIndex].width = width;

    // Sütun genişlikleri toplamını kontrol et
    const totalWidth = window.selectedElement.properties.columns.reduce(
      (sum, col) => sum + (parseInt(col.width) || 0),
      0
    );

    // Toplam genişlik 12'yi aşıyorsa uyarı ver
    if (totalWidth > 12) {
      alert(
        "Toplam sütun genişliği 12 birimden fazla olamaz. Diğer sütunların genişliklerini düşürün."
      );
      // Önceki değeri geri yükle
      window.selectedElement.properties.columns[columnIndex].width =
        12 - (totalWidth - width);
    }

    // Row içeriğini güncelle
    window.updateRowContent();
    
    // Durum kaydet
    window.saveState();
  };

  // Element içeriğini güncelle
  window.updateElementContent = function() {
    if (!window.selectedElement) return;

    const type = window.selectedElement.dataset.type;
    const properties = window.selectedElement.properties;

    if (!properties) {
      console.error("Element özellikleri tanımlanmamış:", window.selectedElement);
      return;
    }

    // Özel durum: Row
    if (type === "row") {
      window.updateRowContent();
    } else {
      // Normal elementler için içeriği güncelle
      const content = window.selectedElement.querySelector(".element-content");
      content.innerHTML = window.renderTemplate(window.elementTemplates[type], properties);

      // Select ve radio için seçenekleri ekle
      if (type === "select" && properties.options) {
        const selectElement = content.querySelector("select");
        if (selectElement) {
          properties.options.forEach((option) => {
            const optionElement = document.createElement("option");
            optionElement.value = option.value || "";
            optionElement.textContent = option.label || "";
            if (option.is_default || properties.default_value === option.value) {
                optionElement.selected = true;
            }
            selectElement.appendChild(optionElement);
          });
        }
      } else if (type === "radio" && properties.options) {
        const radioContainer = content.querySelector(".radio-options");
        if (radioContainer) {
          properties.options.forEach((option) => {
            const radioElement = document.createElement("div");
            radioElement.className = "form-check";
            const isDefault = option.is_default || properties.default_value === option.value;
            radioElement.innerHTML = `
                          <input class="form-check-input" type="radio" name="${
                            properties.name || ""
                          }" ${isDefault ? 'checked' : ''}>
                          <span class="form-check-label">${
                            option.label || ""
                          }</span>
                      `;
            radioContainer.appendChild(radioElement);
          });
        }
      } else if (type === "tab_group" && properties.tabs) {
        // Tab Group için sekmeler ekle
        const tabList = content.querySelector(".nav-tabs");
        const tabContent = content.querySelector(".tab-content");
        
        if (tabList && tabContent) {
          tabList.innerHTML = '';
          tabContent.innerHTML = '';
          
          properties.tabs.forEach((tab, index) => {
            // Tab başlığı ekleme
            const tabItem = document.createElement("li");
            tabItem.className = "nav-item";
            tabItem.innerHTML = `
              <a href="#tab-${window.selectedElement.dataset.id}-${index}" class="nav-link ${index === 0 ? 'active' : ''}" data-bs-toggle="tab">
                ${tab.title}
              </a>
            `;
            tabList.appendChild(tabItem);
            
            // Tab içeriği ekleme
            const tabPane = document.createElement("div");
            tabPane.className = `tab-pane ${index === 0 ? 'active' : ''}`;
            tabPane.id = `tab-${window.selectedElement.dataset.id}-${index}`;
            tabPane.innerHTML = `<p>${tab.content}</p>`;
            tabContent.appendChild(tabPane);
          });
        }
      }
    }
    
    // Durum kaydet
    window.saveState();
  };

  // Row içeriğini güncelle
  window.updateRowContent = function() {
    if (!window.selectedElement || window.selectedElement.dataset.type !== "row") return;

    const rowElement = window.selectedElement.querySelector(".row-element");
    if (!rowElement) return;

    // Properties'deki sütun sayısını ve mevcut DOM'daki sütun sayısını kontrol et
    const columnsInProps = window.selectedElement.properties.columns || [];
    const existingColumns = rowElement.querySelectorAll(".column-element");
    
    // Varolan sütunları kontrol et ve içeriği sakla
    const savedContent = [];
    for (let i = 0; i < Math.min(existingColumns.length, columnsInProps.length); i++) {
      const columnElements = existingColumns[i].querySelectorAll(".form-element");
      if (columnElements.length > 0) {
        savedContent.push([...columnElements]);
      } else {
        savedContent.push([]);
      }
    }
    
    // DOM'u temizle
    rowElement.innerHTML = '';
    
    // Sütunları oluştur
    columnsInProps.forEach((column, index) => {
      // Sütun genişliği
      const width = column.width || Math.floor(12 / columnsInProps.length);
      
      // Sütun elementi oluştur
      const columnDiv = document.createElement('div');
      columnDiv.className = `col-md-${width} column-element`;
      columnDiv.dataset.width = width;
      
      // Eğer saklanmış içerik varsa, onu geri ekle
      if (savedContent[index] && savedContent[index].length > 0) {
        savedContent[index].forEach(element => {
          columnDiv.appendChild(element);
        });
      } else {
        // Boş sütuna placeholder ekle
        const placeholder = document.createElement('div');
        placeholder.className = 'column-placeholder';
        placeholder.innerHTML = '<i class="fas fa-plus me-2"></i> Buraya element sürükleyin';
        columnDiv.appendChild(placeholder);
      }
      
      // Sütunu satıra ekle
      rowElement.appendChild(columnDiv);
    });
    
    // SortableJS'yi yeniden başlat
    window.initializeColumnSortables();
  };
});