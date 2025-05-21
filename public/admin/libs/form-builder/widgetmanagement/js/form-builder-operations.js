// Widget Management Form Builder İşlemleri

document.addEventListener("DOMContentLoaded", function() {
  // Benzersiz alan adı oluşturmak için yardımcı fonksiyon
  window.makeNameUnique = function(baseName) {
    const allElements = window.formCanvas.querySelectorAll('.form-element');
    const otherElements = Array.from(allElements).filter(el => el !== window.selectedElement);
    
    let counter = 1;
    let finalName = baseName;
    
    while (otherElements.some(el => el.properties && el.properties.name === finalName)) {
      counter++;
      if (finalName.match(/-\d+$/)) {
        finalName = finalName.replace(/-\d+$/, `-${counter}`);
      } else {
        finalName = `${baseName}-${counter}`;
      }
    }
    
    return finalName;
  };

  // Özellik panelini güncelle
  window.updatePropertiesPanel = function() {
    if (!window.selectedElement) return;

    const type = window.selectedElement.dataset.type;
    const propTemplate = window.propertyTemplates[type];

    if (!propTemplate) {
      window.propertiesPanel.innerHTML = `
              <div class="alert alert-warning">
                  Bu element tipi için özellik paneli henüz eklenmemiş.
              </div>
          `;
      return;
    }

    let properties = window.selectedElement.properties;

    if (!properties) {
      properties = window.defaultProperties[type]
        ? JSON.parse(JSON.stringify(window.defaultProperties[type]))
        : {};
      window.selectedElement.properties = properties;
    }

    if (properties.required === undefined) properties.required = false;
    if (properties.is_active === undefined) properties.is_active = true;
    if (properties.is_system === undefined) properties.is_system = false;
    
    if (typeof properties.required === 'string') properties.required = properties.required === 'true';
    if (typeof properties.is_active === 'string') properties.is_active = properties.is_active === 'true';
    if (typeof properties.is_system === 'string') properties.is_system = properties.is_system === 'true';

    let templateData = Object.assign({}, properties);
    templateData["width" + properties.width] = true;

    if (type === "row" && properties.columns) {
      const columnsCount = properties.columns.length;
      templateData["columns2"] = (columnsCount === 2);
      templateData["columns3"] = (columnsCount === 3);
      templateData["columns4"] = (columnsCount === 4);
    }
    
    if (type === "heading" && properties.size) {
      templateData["size" + properties.size] = true;
    }
    
    if (properties.align) {
      templateData["align" + properties.align] = true;
    }
    
    if (properties.style) {
      templateData["style" + properties.style] = true;
    }
    
    if (properties.thickness) {
      templateData["thickness" + properties.thickness.replace(".", "_")] = true;
    }
    
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

    window.propertiesPanel.innerHTML = window.renderTemplate(propTemplate, templateData);

    const nameInput = window.propertiesPanel.querySelector('input[name="name"]');
    if (nameInput) {
      nameInput.disabled = true;
      nameInput.style.cursor = 'pointer';
      nameInput.title = 'Düzenlemek için çift tıklayın';
      
      nameInput.addEventListener('dblclick', function() {
        this.disabled = false;
        this.style.cursor = 'text';
        this.focus();
      });
    }

    const inputs = window.propertiesPanel.querySelectorAll("input, select, textarea");
    inputs.forEach((input) => {
      input.addEventListener("change", function () {
        window.updateElementProperty(input);
      });

      input.addEventListener("keyup", function () {
        window.updateElementProperty(input);
      });
    });

    if (type === "row") {
      const columnCountSelect = window.propertiesPanel.querySelector('[name="column-count"]');
      if (columnCountSelect) {
        if (properties.columns) {
          const columnsCount = properties.columns.length;
          Array.from(columnCountSelect.options).forEach(option => {
            option.selected = parseInt(option.value) === columnsCount;
          });
        }
        
        columnCountSelect.addEventListener("change", function () {
          window.updateRowColumns(parseInt(this.value));
        });
      }

      const columnWidthsContainer = document.getElementById("column-widths-container");
      if (columnWidthsContainer && properties.columns) {
        columnWidthsContainer.innerHTML = "";

        properties.columns.forEach((column, index) => {
          const rowElement = document.createElement("div");
          rowElement.className = "input-group mb-2 column-width-row";
          rowElement.innerHTML = `
                    <span class="input-group-text">Sütun ${index + 1}</span>
                    <select class="form-select" name="column-width-${index}">
                        <option value="2" ${column.width == 2 ? "selected" : ""}>2/12</option>
                        <option value="3" ${column.width == 3 ? "selected" : ""}>3/12</option>
                        <option value="4" ${column.width == 4 ? "selected" : ""}>4/12</option>
                        <option value="6" ${column.width == 6 ? "selected" : ""}>6/12</option>
                        <option value="8" ${column.width == 8 ? "selected" : ""}>8/12</option>
                        <option value="10" ${column.width == 10 ? "selected" : ""}>10/12</option>
                    </select>
                `;

          columnWidthsContainer.appendChild(rowElement);

          const select = rowElement.querySelector("select");
          select.addEventListener("change", function () {
            window.updateColumnWidth(index, parseInt(this.value));
          });
        });
      }
    }
    
    if (type === "select" || type === "radio") {
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
                    <input class="form-check-input" type="radio" name="option-default" value="${index}" ${isChecked ? 'checked' : ''}>
                </div>
                <input type="text" class="form-control" name="option-label-${index}" placeholder="Etiket" value="${option.label || ""}">
                <input type="text" class="form-control" name="option-value-${index}" placeholder="Değer" value="${option.value || ""}">
                <button type="button" class="btn btn-outline-danger remove-option" data-index="${index}">
                    <i class="fas fa-times"></i>
                </button>
            `;

            optionsContainer.appendChild(optionRow);

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

      const addOptionBtn = document.getElementById("add-option");
      if (addOptionBtn) {
        addOptionBtn.addEventListener("click", function () {
          if (!Array.isArray(properties.options)) {
            properties.options = [];
          }
          
          const isDefault = properties.options.length === 0;
          
          properties.options.push({
            value: "option" + (properties.options.length + 1),
            label: "Seçenek " + (properties.options.length + 1),
            is_default: isDefault
          });
          
          if (isDefault) {
            properties.default_value = "option" + properties.options.length;
          }
          
          window.updateElementContent();
          window.updatePropertiesPanel();
        });
      }
      
      const defaultValueContainer = window.propertiesPanel.querySelector('.default-value-radio-container');
      if (defaultValueContainer) {
        defaultValueContainer.innerHTML = '';
        
        if (properties.options && properties.options.length) {
          properties.options.forEach((option, index) => {
            const radioDiv = document.createElement('div');
            radioDiv.className = 'form-check';
            
            const radioInput = document.createElement('input');
            radioInput.className = 'form-check-input';
            radioInput.type = 'radio';
            radioInput.name = 'default_value_radio';
            radioInput.id = `default_value_${option.value}`;
            radioInput.value = option.value;
            
            if (option.is_default || properties.default_value === option.value) {
              radioInput.checked = true;
            }
            
            const radioLabel = document.createElement('label');
            radioLabel.className = 'form-check-label';
            radioLabel.htmlFor = `default_value_${option.value}`;
            radioLabel.textContent = option.label;
            
            radioDiv.appendChild(radioInput);
            radioDiv.appendChild(radioLabel);
            defaultValueContainer.appendChild(radioDiv);
            
            radioInput.addEventListener('change', function() {
              if (this.checked) {
                const selectedValue = this.value;
                
                properties.options.forEach(option => {
                  option.is_default = option.value === selectedValue;
                });
                
                properties.default_value = selectedValue;
                
                window.updateElementContent();
              }
            });
          });
        }
      }
    }
      
    if (type === "tab_group") {
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

      const removeTabBtns = document.querySelectorAll(".remove-tab");
      removeTabBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
          const index = parseInt(this.dataset.index);
          properties.tabs.splice(index, 1);
          window.updateElementContent();
          window.updatePropertiesPanel();
        });
      });

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
    
    const labelInput = window.propertiesPanel.querySelector('input[name="label"]');
    if (labelInput && nameInput) {
      labelInput.addEventListener('input', function() {
        const labelSlug = window.slugifyTurkish(this.value);
        let newName = 'widget_' + labelSlug;
        
        const uniqueName = window.makeNameUnique(newName);
        
        window.selectedElement.properties.name = uniqueName;
        nameInput.value = uniqueName;
      });
      
      if (labelInput.value) {
        const nameValue = nameInput.value || '';
        const isDefaultName = nameValue.endsWith('_field') || nameValue.startsWith('widget_');
        
        if (isDefaultName) {
          const inputEvent = new Event('input', { bubbles: true });
          labelInput.dispatchEvent(inputEvent);
        }
      }
    }
    
    const checkboxes = window.propertiesPanel.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach((checkbox) => {
      const name = checkbox.name;
      
      if (name === 'is_active' || name === 'is_system' || name === 'required') {
        const value = properties[name];
        checkbox.checked = value === true;
      }
    });
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
    } else if (input.name === "option-default") {
      const selectedIndex = parseInt(input.value);
      
      properties.options.forEach((opt, idx) => {
        opt.is_default = idx === selectedIndex;
      });
      
      properties.default_value = option.value;
    }

    window.updateElementContent();
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
    let value;
    
    if (input.type === "checkbox") {
      value = input.checked;
    } else {
      value = input.value;
    }

    if (name === "label") {
      window.selectedElement.querySelector(".element-title").textContent = value;
      window.selectedElement.properties.label = value;

      const labelElement = window.selectedElement.querySelector(".form-label");
      if (labelElement) {
        labelElement.textContent = value;
      }
      
      const nameInput = window.propertiesPanel.querySelector('input[name="name"]');
      if (nameInput && nameInput.disabled) {
        if (value) {
          const labelSlug = window.slugifyTurkish(value);
          const currentName = window.selectedElement.properties.name || '';
          const isDefaultName = !currentName || currentName.endsWith('_field') || currentName.startsWith('widget_');
          
          if (isDefaultName || !currentName.startsWith('widget_')) {
            const newBaseName = 'widget_' + labelSlug;
            const uniqueName = window.makeNameUnique(newBaseName);
            
            window.selectedElement.properties.name = uniqueName;
            nameInput.value = uniqueName;
          }
        }
      }
    } else if (name === "checkbox_label" && window.selectedElement.dataset.type === "checkbox") {
      window.selectedElement.properties.checkbox_label = value;
      window.updateElementContent();
    } else if (name === "default_value_text" && window.selectedElement.dataset.type === "checkbox") {
      window.selectedElement.properties.default_value_text = value;
      window.updateElementContent();
    } else if (name === "switch_label" && window.selectedElement.dataset.type === "switch") {
      window.selectedElement.properties.switch_label = value;
      window.updateElementContent();
    } else if (name === "default_value_text" && window.selectedElement.dataset.type === "switch") {
      window.selectedElement.properties.default_value_text = value;
      window.updateElementContent();
    } else if (name === "name") {
      window.selectedElement.properties.name = value;
    } else if (name === "width") {
      window.selectedElement.dataset.width = value;
      window.selectedElement.properties.width = parseInt(value);

      const width = parseInt(value);
      window.selectedElement.style.width = `${(width * 100) / 12}%`;

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
      
      const headingElement = window.selectedElement.querySelector(window.selectedElement.properties.size);
      if (headingElement) {
        headingElement.textContent = value;
      }
    } else if (name === "title" && window.selectedElement.dataset.type === "card") {
      window.selectedElement.querySelector(".element-title").textContent = "Kart: " + value;
      window.selectedElement.properties.title = value;
      
      const titleElement = window.selectedElement.querySelector(".card-title");
      if (titleElement) {
        titleElement.textContent = value;
      }
    } else if (name === "default_value_text" && window.selectedElement.dataset.type === "color") {
      window.selectedElement.properties.default_value = value;
      const colorPicker = window.propertiesPanel.querySelector('input[type="color"][name="default_value"]');
      if (colorPicker) {
        colorPicker.value = value;
      }
    } else if (name === "color_text" && window.selectedElement.dataset.type === "divider") {
      window.selectedElement.properties.color = value;
      const colorPicker = window.propertiesPanel.querySelector('input[type="color"][name="color"]');
      if (colorPicker) {
        colorPicker.value = value;
      }
    } else if (name === "is_active" || name === "is_system" || name === "required") {
      window.selectedElement.properties[name] = value;
    } else {
      window.selectedElement.properties[name] = value;
    }

    window.updateElementContent();
  };

  // Row sütunlarını güncelle
  window.updateRowColumns = function(columnCount) {
    if (!window.selectedElement || window.selectedElement.dataset.type !== "row") return;
    
    const rowElement = window.selectedElement.querySelector('.row-element');
    if (!rowElement) return;
    
    if (!rowElement.classList.contains('g-3')) {
      rowElement.classList.add('g-3');
    }

    const currentColumns = window.selectedElement.properties.columns || [];
    const columnElements = rowElement.querySelectorAll(".column-element") || [];

    if (columnCount < currentColumns.length) {
      let hasContent = false;
      let elementsToSave = [];

      for (let i = columnCount; i < columnElements.length; i++) {
        const elements = columnElements[i].querySelectorAll(".form-element");
        if (elements.length > 0) {
          hasContent = true;
          elements.forEach(el => {
            elementsToSave.push({
              element: el,
              properties: el.properties ? JSON.parse(JSON.stringify(el.properties)) : {},
              type: el.dataset.type
            });
          });
        }
      }

      if (hasContent) {
        const proceed = confirm(
          "Sütun sayısını azaltırsanız, fazla olan sütunlardaki içerikler ilk sütuna taşınacak. Devam etmek istiyor musunuz?"
        );
        if (!proceed) return;
      }
    }

    const newColumns = [];
    let defaultWidth;
    
    switch(columnCount) {
      case 1: defaultWidth = 12; break;
      case 2: defaultWidth = 6; break;
      case 3: defaultWidth = 4; break;
      case 4: defaultWidth = 3; break;
      case 6: defaultWidth = 2; break;
      default: defaultWidth = Math.floor(12 / columnCount);
    }
    
    let extraWidth = 12 - (defaultWidth * columnCount);

    for (let i = 0; i < columnCount; i++) {
      let columnWidth = defaultWidth;
      if (extraWidth > 0) {
        columnWidth++;
        extraWidth--;
      }
      
      if (i < currentColumns.length) {
        newColumns.push({
          ...currentColumns[i],
          width: columnWidth
        });
      } else {
        newColumns.push({
          index: i + 1,
          width: columnWidth
        });
      }
    }

    window.selectedElement.properties.columns = newColumns;

    window.updateRowContent();

    window.updatePropertiesPanel();
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
    
    const columnCount = window.selectedElement.properties.columns.length;
    
    let standardWidth;
    switch(columnCount) {
      case 1:
        standardWidth = 12;
        break;
      case 2:
        standardWidth = 6;
        break;
      case 3:
        standardWidth = 4;
        break;
      case 4:
        standardWidth = 3;
        break;
      case 6:
        standardWidth = 2;
        break;
      case 12:
        standardWidth = 1;
        break;
      default:
        standardWidth = Math.floor(12 / columnCount);
    }
    
    if (columnCount === 2) {
      window.selectedElement.properties.columns.forEach((col, i) => {
        col.width = 6;
      });
    } else {
      window.selectedElement.properties.columns[columnIndex].width = width;

      const totalWidth = window.selectedElement.properties.columns.reduce(
        (sum, col) => sum + (parseInt(col.width) || 0),
        0
      );

      if (totalWidth > 12) {
        alert(
          "Toplam sütun genişliği 12 birimden fazla olamaz. Diğer sütunların genişliklerini düşürün."
        );
        window.selectedElement.properties.columns[columnIndex].width =
          12 - (totalWidth - width);
      }
    }

    window.updateRowContent();
    
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

    if (type === "row") {
      window.updateRowContent();
    } else {
      const templateProps = {...properties};
      
      if (type === "checkbox") {
        templateProps.checkbox_label = properties.checkbox_label || "Onay";
        templateProps.default_value_text = properties.default_value_text || "";
        
        if (properties.default_value === true || properties.default_value === "true") {
          templateProps.default_value = "checked";
        } else {
          templateProps.default_value = "";
        }
      }
      
      if (type === "switch") {
        templateProps.switch_label = properties.switch_label || "Anahtar";
        templateProps.default_value_text = properties.default_value_text || "";
        
        if (properties.default_value === true || properties.default_value === "true") {
          templateProps.default_value = "checked";
        } else {
          templateProps.default_value = "";
        }
      }
      
      if (type === "select" && properties.options && properties.options.length > 0) {
        const content = window.selectedElement.querySelector(".element-content");
        const select = content.querySelector("select");
        
        if (select) {
          const placeholder = select.querySelector('option[disabled]');
          select.innerHTML = '';
          
          if (placeholder) {
            select.appendChild(placeholder);
          }
          
          properties.options.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option.value;
            optionElement.textContent = option.label;
            
            if (option.is_default || properties.default_value === option.value) {
              optionElement.selected = true;
            }
            
            select.appendChild(optionElement);
          });
        }
      }
      
      if (type === "radio" && properties.options && properties.options.length > 0) {
        const content = window.selectedElement.querySelector(".element-content");
        const radioOptions = content.querySelector(".radio-options");
        
        if (radioOptions) {
          radioOptions.innerHTML = '';
          
          properties.options.forEach((option, index) => {
            const radioDiv = document.createElement('div');
            radioDiv.className = 'form-check';
            
            const radioInput = document.createElement('input');
            radioInput.className = 'form-check-input';
            radioInput.type = 'radio';
            radioInput.name = properties.name;
            radioInput.id = `${properties.name}_${index}`;
            radioInput.value = option.value;
            radioInput.setAttribute('onchange', 'window.updateRadioState(this)');
            
            if (option.is_default || properties.default_value === option.value) {
              radioInput.checked = true;
            }
            
            const radioLabel = document.createElement('label');
            radioLabel.className = 'form-check-label';
            radioLabel.htmlFor = `${properties.name}_${index}`;
            radioLabel.textContent = option.label;
            
            radioDiv.appendChild(radioInput);
            radioDiv.appendChild(radioLabel);
            radioOptions.appendChild(radioDiv);
          });
        }
      }
      
      const content = window.selectedElement.querySelector(".element-content");
      content.innerHTML = window.renderTemplate(window.elementTemplates[type], templateProps);

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
        const tabList = content.querySelector(".nav-tabs");
        const tabContent = content.querySelector(".tab-content");
        
        if (tabList && tabContent) {
          tabList.innerHTML = '';
          tabContent.innerHTML = '';
          
          properties.tabs.forEach((tab, index) => {
            const tabItem = document.createElement("li");
            tabItem.className = "nav-item";
            tabItem.innerHTML = `
              <a href="#tab-${window.selectedElement.dataset.id}-${index}" class="nav-link ${index === 0 ? 'active' : ''}" data-bs-toggle="tab">
                ${tab.title}
              </a>
            `;
            tabList.appendChild(tabItem);
            
            const tabPane = document.createElement("div");
            tabPane.className = `tab-pane ${index === 0 ? 'active' : ''}`;
            tabPane.id = `tab-${window.selectedElement.dataset.id}-${index}`;
            tabPane.innerHTML = `<p>${tab.content}</p>`;
            tabContent.appendChild(tabPane);
          });
        }
      }
    }
    
    window.saveState();
  };

  // Row içeriğini güncelle
  window.updateRowContent = function() {
    if (!window.selectedElement || window.selectedElement.dataset.type !== "row") return;

    const rowElement = window.selectedElement.querySelector(".row-element");
    if (!rowElement) return;

    const columnsInProps = window.selectedElement.properties.columns || [];
    if (columnsInProps.length === 0) {
      console.warn("Satır için sütun tanımlanmamış");
      return;
    }
    
    const existingColumns = rowElement.querySelectorAll(".column-element");
    const columnContents = [];
    
    for (let i = 0; i < existingColumns.length; i++) {
      const elements = existingColumns[i].querySelectorAll(".form-element");
      const columnElements = [];
      
      elements.forEach(element => {
        columnElements.push({
          type: element.dataset.type,
          properties: element.properties ? JSON.parse(JSON.stringify(element.properties)) : {}
        });
      });
      
      columnContents.push(columnElements);
    }
    
    rowElement.innerHTML = '';
    
    const totalWidth = columnsInProps.reduce((sum, col) => sum + parseInt(col.width || 0), 0);
    if (totalWidth !== 12) {
      console.warn(`Sütun genişliklerinin toplamı 12 olmalı, şu an: ${totalWidth}`);
      
      const columnCount = columnsInProps.length;
      let defaultWidth;
      
      switch(columnCount) {
        case 1: defaultWidth = 12; break;
        case 2: defaultWidth = 6; break;
        case 3: defaultWidth = 4; break;
        case 4: defaultWidth = 3; break;
        case 6: defaultWidth = 2; break;
        default: defaultWidth = Math.floor(12 / columnCount);
      }
      
      let extraWidth = 12 - (defaultWidth * columnCount);
      
      columnsInProps.forEach((col, i) => {
        col.width = defaultWidth + (i < extraWidth ? 1 : 0);
      });
    }
    
    columnsInProps.forEach((column, index) => {
      const columnDiv = document.createElement('div');
      columnDiv.className = `col-md-${column.width} column-element`;
      columnDiv.dataset.width = column.width;
      columnDiv.dataset.index = index;
      
      rowElement.appendChild(columnDiv);
    });
    
    const newColumns = rowElement.querySelectorAll(".column-element");
    
    for (let i = 0; i < Math.min(columnContents.length, newColumns.length); i++) {
      const columnElements = columnContents[i];
      
      columnElements.forEach(elementData => {
        const newElement = window.createFormElement(elementData.type, elementData.properties);
        if (newElement) {
          newColumns[i].appendChild(newElement);
        }
      });
    }
    
    if (columnContents.length > newColumns.length && newColumns.length > 0) {
      for (let i = newColumns.length; i < columnContents.length; i++) {
        columnContents[i].forEach(elementData => {
          const newElement = window.createFormElement(elementData.type, elementData.properties);
          if (newElement) {
            newColumns[0].appendChild(newElement);
          }
        });
      }
    }
    
    newColumns.forEach(column => {
      if (column.children.length === 0) {
        const placeholder = document.createElement('div');
        placeholder.className = 'column-placeholder';
        placeholder.innerHTML = '<i class="fas fa-plus me-2"></i> Buraya element sürükleyin';
        column.appendChild(placeholder);
      }
    });
    
    window.initializeColumnSortables();
  };

  window.updateCheckboxState = function(checkbox) {
    const formElement = checkbox.closest('.form-element');
    if (!formElement) return;
    
    if (window.selectedElement !== formElement) {
      if (window.selectedElement) {
        window.selectedElement.classList.remove('selected');
      }
      
      window.selectedElement = formElement;
      window.selectedElement.classList.add('selected');
      
      window.updatePropertiesPanel();
    }
    
    const isChecked = checkbox.checked;
    
    const defaultValueTrue = window.propertiesPanel.querySelector('#default_value_true');
    const defaultValueFalse = window.propertiesPanel.querySelector('#default_value_false');
    
    if (defaultValueTrue && defaultValueFalse) {
      if (isChecked) {
        defaultValueTrue.checked = true;
        defaultValueFalse.checked = false;
        window.selectedElement.properties.default_value = true;
      } else {
        defaultValueFalse.checked = true;
        defaultValueTrue.checked = false;
        window.selectedElement.properties.default_value = false;
      }
      
      window.saveState();
    }
    
    event.stopPropagation();
  };
  
  window.updateSwitchState = function(switchElement) {
    const formElement = switchElement.closest('.form-element');
    if (!formElement) return;
    
    if (window.selectedElement !== formElement) {
      if (window.selectedElement) {
        window.selectedElement.classList.remove('selected');
      }
      
      window.selectedElement = formElement;
      window.selectedElement.classList.add('selected');
      
      window.updatePropertiesPanel();
    }
    
    const isChecked = switchElement.checked;
    
    const defaultValueTrue = window.propertiesPanel.querySelector('#default_value_true');
    const defaultValueFalse = window.propertiesPanel.querySelector('#default_value_false');
    
    if (defaultValueTrue && defaultValueFalse) {
      if (isChecked) {
        defaultValueTrue.checked = true;
        defaultValueFalse.checked = false;
        window.selectedElement.properties.default_value = true;
      } else {
        defaultValueFalse.checked = true;
        defaultValueTrue.checked = false;
        window.selectedElement.properties.default_value = false;
      }
      
      window.saveState();
    }
    
    event.stopPropagation();
  };
  
  window.updateSelectState = function(selectElement) {
    const formElement = selectElement.closest('.form-element');
    if (!formElement) return;
    
    if (window.selectedElement !== formElement) {
      if (window.selectedElement) {
        window.selectedElement.classList.remove('selected');
      }
      
      window.selectedElement = formElement;
      window.selectedElement.classList.add('selected');
      
      window.updatePropertiesPanel();
    }
    
    const selectedValue = selectElement.value;
    
    if (window.selectedElement.properties.options && window.selectedElement.properties.options.length > 0) {
      window.selectedElement.properties.options.forEach(option => {
        option.is_default = option.value === selectedValue;
      });
      
      window.selectedElement.properties.default_value = selectedValue;
      
      const optionsContainer = window.propertiesPanel.querySelector('.options-container');
      if (optionsContainer) {
        const optionItems = optionsContainer.querySelectorAll('.option-item');
        optionItems.forEach(item => {
          const optionValue = item.querySelector('input[name="option_value"]').value;
          const isDefaultCheckbox = item.querySelector('input[type="checkbox"]');
          
          if (isDefaultCheckbox) {
            isDefaultCheckbox.checked = optionValue === selectedValue;
          }
        });
      }
      
      window.saveState();
    }
    
    event.stopPropagation();
  };
  
  window.updateRadioState = function(radioElement) {
    const formElement = radioElement.closest('.form-element');
    if (!formElement) return;
    
    if (window.selectedElement !== formElement) {
      if (window.selectedElement) {
        window.selectedElement.classList.remove('selected');
      }
      
      window.selectedElement = formElement;
      window.selectedElement.classList.add('selected');
      
      window.updatePropertiesPanel();
    }
    
    const selectedValue = radioElement.value;
    
    if (window.selectedElement.properties.options && window.selectedElement.properties.options.length > 0) {
      window.selectedElement.properties.options.forEach(option => {
        option.is_default = option.value === selectedValue;
      });
      
      window.selectedElement.properties.default_value = selectedValue;
      
      const optionsContainer = window.propertiesPanel.querySelector('.options-container');
      if (optionsContainer) {
        const optionItems = optionsContainer.querySelectorAll('.option-item');
        optionItems.forEach(item => {
          const optionValue = item.querySelector('input[name="option_value"]').value;
          const isDefaultCheckbox = item.querySelector('input[type="checkbox"]');
          
          if (isDefaultCheckbox) {
            isDefaultCheckbox.checked = optionValue === selectedValue;
          }
        });
      }
      
      window.saveState();
    }
    
    event.stopPropagation();
  };
  
  window.slugifyTurkish = function(text) {
    if (!text) return '';
    
    const turkishChars = { 'ç': 'c', 'ğ': 'g', 'ı': 'i', 'i': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u', 
                          'Ç': 'C', 'Ğ': 'G', 'I': 'I', 'İ': 'I', 'Ö': 'O', 'Ş': 'S', 'Ü': 'U' };
    
    let slug = text.replace(/[çğıiöşüÇĞIİÖŞÜ]/g, function(char) {
      return turkishChars[char] || char;
    });
    
    slug = slug.toLowerCase()
              .replace(/[^a-z0-9_]+/g, '_')
              .replace(/^_+|_+$/g, '')
              .replace(/_+/g, '_');
    
    if (/^[0-9]/.test(slug)) {
      slug = 'a_' + slug;
    }
    
    return slug;
  };
});