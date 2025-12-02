// Form Builder İşlemleri

document.addEventListener("DOMContentLoaded", function() {
  // Debounce fonksiyonu - API çağrılarını optimize etmek için
  window.debounce = function(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  };

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

  // Benzersiz alan adı oluşturmak için yardımcı fonksiyon
  window.makeNameUnique = function(baseName) {
    // Canvas'taki tüm elementleri al
    const allElements = window.formCanvas.querySelectorAll('.form-element');
    
    // Şu anki seçili element dışındaki tüm elementleri filtrele
    const otherElements = Array.from(allElements).filter(el => el !== window.selectedElement);
    
    // Aynı isimde başka bir element var mı kontrol et
    let counter = 1;
    let finalName = baseName;
    
    // Aynı isimde element varsa sonuna numara ekle
    while (otherElements.some(el => el.properties && el.properties.name === finalName)) {
      counter++;
      // Son kısım zaten sayı içeriyorsa, o sayıyı güncelle
      if (finalName.match(/-\d+$/)) {
        finalName = finalName.replace(/-\d+$/, `-${counter}`);
      } else {
        finalName = `${baseName}-${counter}`;
      }
    }
    
    return finalName;
  };

  // Name güncelleme için debounced API çağrısı (global seviyede)
  window.debouncedUpdateNameFromLabel = window.debounce(function(labelValue, nameInput) {
    const groupId = document.getElementById('group-id')?.value;
    if (!groupId || !labelValue || !nameInput) return;

    fetch(`/admin/settingmanagement/api/groups/${groupId}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(response => response.json())
    .then(groupData => {
      if (groupData && groupData.prefix) {
        let groupPrefix = window.slugifyTurkish(groupData.prefix.toLowerCase());

        if (groupPrefix) {
          const labelSlug = window.slugifyTurkish(labelValue);
          const currentName = window.selectedElement.properties.name || '';
          const isDefaultName = !currentName || currentName.endsWith('_field') || currentName.startsWith('form_');

          let newBaseName;
          if (isDefaultName || !currentName.startsWith(groupPrefix + '_')) {
            newBaseName = groupPrefix + '_' + labelSlug;
          } else {
            const nameParts = currentName.split('_');
            if (nameParts.length > 1) {
              nameParts.splice(1);
              nameParts.push(labelSlug);
              newBaseName = nameParts.join('_');
            } else {
              newBaseName = groupPrefix + '_' + labelSlug;
            }
          }

          const uniqueName = window.makeNameUnique(newBaseName);
          window.selectedElement.properties.name = uniqueName;
          nameInput.value = uniqueName;
        }
      }
    })
    .catch(error => {
      console.error('Grup verisi alınamadı:', error);
    });
  }, 500);

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

    // Boolean özelliklerini kontrol et - checkbox'ların doğru gösterilmesi için
    if (properties.required === undefined) properties.required = false;
    if (properties.is_active === undefined) properties.is_active = true;
    if (properties.is_system === undefined) properties.is_system = false;
    
    // Checkbox özelliklerinin string'den boolean'a dönüşümünü kontrol et
    if (typeof properties.required === 'string') properties.required = properties.required === 'true';
    if (typeof properties.is_active === 'string') properties.is_active = properties.is_active === 'true';
    if (typeof properties.is_system === 'string') properties.is_system = properties.is_system === 'true';

    // Genişlik değerlerini kontrol et
    let templateData = Object.assign({}, properties);
    templateData["width" + properties.width] = true;

    // Row için sütun sayısını kontrol et
    if (type === "row" && properties.columns) {
      // Bu kısım değişecek
      // Var olan sütun sayısına göre doğru seçeneği işaretleyelim
      const columnsCount = properties.columns.length;
      templateData["columns2"] = (columnsCount === 2);
      templateData["columns3"] = (columnsCount === 3);
      templateData["columns4"] = (columnsCount === 4);
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

    // Alan adı (System Key) alanını disabled olarak ayarla ve çift tıklama olayı ekle
    const nameInput = window.propertiesPanel.querySelector('input[name="name"]');
    console.log('🔍 Name input bulundu mu?', nameInput);

    if (nameInput) {
      // DISABLED YERINE READONLY + CSS ile disabled görünümü
      nameInput.setAttribute('readonly', 'readonly');
      nameInput.style.cursor = 'pointer';
      nameInput.style.backgroundColor = '#e9ecef'; // Bootstrap disabled input rengi
      nameInput.style.opacity = '1'; // Readonly olunca opacity düşmesin
      nameInput.title = 'Düzenlemek için çift tıklayın';
      nameInput.classList.add('name-input-locked'); // Tanımlama için class ekle

      console.log('✅ Name input readonly yapıldı, cursor:', nameInput.style.cursor);

      // Çift tıklama event'i direkt input'a ekle (artık disabled değil, readonly)
      nameInput.addEventListener('dblclick', function(e) {
        console.log('🖱️ Çift tıklama algılandı!');
        e.preventDefault();
        e.stopPropagation();

        // Readonly'i kaldır ve düzenlemeye aç
        this.removeAttribute('readonly');
        this.style.cursor = 'text';
        this.style.backgroundColor = '#fff3cd'; // Sarı arka plan - düzenleniyor göstergesi
        this.classList.remove('name-input-locked');
        this.classList.add('name-input-editing');
        this.focus();
        this.select(); // Tüm metni seç

        console.log('📝 Input düzenleme moduna alındı');
      });

      // Blur olayında tekrar readonly yap (kullanıcı dışarı tıklarsa)
      nameInput.addEventListener('blur', function() {
        console.log('👋 Blur event - input readonly yapılıyor');

        // Eğer değer değiştiyse güncelle
        if (window.selectedElement && this.value !== window.selectedElement.properties.name) {
          window.selectedElement.properties.name = this.value;
          console.log('💾 Yeni değer kaydedildi:', this.value);
        }

        // Tekrar readonly yap
        this.setAttribute('readonly', 'readonly');
        this.style.cursor = 'pointer';
        this.style.backgroundColor = '#e9ecef'; // Bootstrap disabled input rengi
        this.classList.remove('name-input-editing');
        this.classList.add('name-input-locked');
      });
    } else {
      console.warn('⚠️ Name input bulunamadı!');
    }

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

    // Row için özel yönetim - DROPDOWN SEÇİLİ GÖSTERİMİ İÇİN EK KOD
    if (type === "row") {
      // Sütun sayısını değiştirme
      const columnCountSelect = window.propertiesPanel.querySelector('[name="column-count"]');
      if (columnCountSelect) {
        // Mevcut sütun sayısına göre dropdown'daki doğru değeri seç
        if (properties.columns) {
          const columnsCount = properties.columns.length;
          // Dropdown'daki uygun seçeneği seç
          Array.from(columnCountSelect.options).forEach(option => {
            option.selected = parseInt(option.value) === columnsCount;
          });
        }
        
        columnCountSelect.addEventListener("change", function () {
          window.updateRowColumns(parseInt(this.value));
        });
      }

      // Sütun genişliklerini göster
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

          // Sütun genişliği değişikliğini dinle
          const select = rowElement.querySelector("select");
          select.addEventListener("change", function () {
            window.updateColumnWidth(index, parseInt(this.value));
          });
        });
      }
    }
    
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
            const optValue = window.getOptionValue ? window.getOptionValue(option) : option.value;
            const optLabel = window.getOptionLabel ? window.getOptionLabel(option) : option.label;
            const isChecked = option.is_default || properties.default_value === optValue;

            optionRow.innerHTML = `
                <div class="input-group-text">
                    <input class="form-check-input" type="radio" name="option-default" value="${index}" ${isChecked ? 'checked' : ''}>
                </div>
                <input type="text" class="form-control" name="option-label-${index}" placeholder="Etiket" value="${optLabel || ""}">
                <input type="text" class="form-control" name="option-value-${index}" placeholder="Değer" value="${optValue || ""}">
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
      
      // Varsayılan değer radio butonları
      const defaultValueContainer = window.propertiesPanel.querySelector('.default-value-radio-container');
      if (defaultValueContainer) {
        // Mevcut içeriği temizle
        defaultValueContainer.innerHTML = '';

        if (properties.options && properties.options.length) {
          properties.options.forEach((option, index) => {
            // Radio butonu oluştur
            const radioDiv = document.createElement('div');
            radioDiv.className = 'form-check';

            const optValue = window.getOptionValue ? window.getOptionValue(option) : option.value;
            const optLabel = window.getOptionLabel ? window.getOptionLabel(option) : option.label;

            const radioInput = document.createElement('input');
            radioInput.className = 'form-check-input';
            radioInput.type = 'radio';
            radioInput.name = 'default_value_radio';
            radioInput.id = `default_value_${optValue}`;
            radioInput.value = optValue;

            // Varsayılan değeri seç
            if (option.is_default || properties.default_value === optValue) {
              radioInput.checked = true;
            }

            const radioLabel = document.createElement('label');
            radioLabel.className = 'form-check-label';
            radioLabel.htmlFor = `default_value_${optValue}`;
            radioLabel.textContent = optLabel;
            
            radioDiv.appendChild(radioInput);
            radioDiv.appendChild(radioLabel);
            defaultValueContainer.appendChild(radioDiv);
            
            // Değişikliği dinle
            radioInput.addEventListener('change', function() {
              if (this.checked) {
                const selectedValue = this.value;
                
                // Tüm seçeneklerin varsayılan değerini sıfırla
                properties.options.forEach(option => {
                  option.is_default = option.value === selectedValue;
                });
                
                // Varsayılan değeri güncelle
                properties.default_value = selectedValue;
                
                window.updateElementContent();
              }
            });
          });
        }
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
    
    // Otomatik Alan Adı (System Key) oluşturma
    const labelInput = window.propertiesPanel.querySelector('input[name="label"]');
    if (labelInput && nameInput) {
      // Debounced API çağrısı fonksiyonu (500ms gecikme ile)
      const updateNameFromLabel = window.debounce(function(labelValue) {
        // Grup ID'sini al
        const groupId = document.getElementById('group-id')?.value;

        if (groupId && labelValue) {
          // Grup bilgilerini getir - prefix değeri için API'ye sor
          fetch(`/admin/settingmanagement/api/groups/${groupId}`, {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
          })
          .then(response => response.json())
          .then(groupData => {
            if (groupData && groupData.prefix) {
              // Grup prefix'ini al
              let groupPrefix = groupData.prefix;

              // Prefix'i slug formatına çevir
              groupPrefix = window.slugifyTurkish(groupPrefix.toLowerCase());

              if (groupPrefix) {
                // Label'i slug formatına çevir
                const labelSlug = window.slugifyTurkish(labelValue);

                // Alan adını oluştur
                let newName = groupPrefix + '_' + labelSlug;

                // Eğer properties.name zaten varsa ve prefix ile başlıyorsa
                if (window.selectedElement.properties.name &&
                    window.selectedElement.properties.name.startsWith(groupPrefix + '_')) {
                  // Sadece label kısmını değiştir
                  const nameParts = window.selectedElement.properties.name.split('_');
                  if (nameParts.length > 1) {
                    nameParts.splice(1); // İlk parçayı (prefix) dışındakileri sil
                    nameParts.push(labelSlug); // Yeni label'i ekle
                    newName = nameParts.join('_');
                  }
                }

                // Benzersiz bir isim oluştur
                const uniqueName = window.makeNameUnique(newName);

                // Alan adını güncelle
                window.selectedElement.properties.name = uniqueName;
                nameInput.value = uniqueName;
              }
            }
          })
          .catch(error => {
            console.error('Grup verisi alınamadı:', error);
          });
        }
      }, 500); // 500ms gecikme

      // Label değiştiğinde debounced fonksiyonu çağır
      labelInput.addEventListener('input', function() {
        updateNameFromLabel(this.value);
      });
      
      // İlk yüklemede label değerine göre name alanını güncelle
      if (labelInput.value) {
        // Eğer name değeri form_ ile başlıyorsa veya _field ile bitiyorsa
        const nameValue = nameInput.value || '';
        const isDefaultName = nameValue.endsWith('_field') || nameValue.startsWith('form_');
        
        if (isDefaultName) {
          // Yapay bir input event tetikle
          const inputEvent = new Event('input', { bubbles: true });
          labelInput.dispatchEvent(inputEvent);
        }
      }
    }
    
    // Checkbox durumlarını manuel olarak ayarla (is_active, is_system, required)
    const checkboxes = window.propertiesPanel.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach((checkbox) => {
      const name = checkbox.name;
      
      if (name === 'is_active' || name === 'is_system' || name === 'required') {
        // Özelliği kontrol et ve checkbox'ı doğru şekilde ayarla
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
      // Radio butonları için varsayılan seçimi güncelle
      const selectedIndex = parseInt(input.value);
      
      // Tüm seçeneklerin varsayılan özelliğini sıfırla
      properties.options.forEach((opt, idx) => {
        opt.is_default = idx === selectedIndex;
      });
      
      // Seçilen seçeneğin varsayılan değerini ayarla
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
    
    // Input tipine göre değer al
    if (input.type === "checkbox") {
      value = input.checked;
    } else {
      value = input.value;
    }

    // Özel özellik güncellemeleri
    if (name === "label") {
      window.selectedElement.querySelector(".element-title").textContent = value;
      window.selectedElement.properties.label = value;

      // Form içindeki etiketi güncelle
      const labelElement = window.selectedElement.querySelector(".form-label");
      if (labelElement) {
        labelElement.textContent = value;
      }
      
      // Alan adını otomatik güncelle (label değiştiğinde) - Debounced API çağrısı ile
      const nameInput = window.propertiesPanel.querySelector('input[name="name"]');
      if (nameInput && nameInput.disabled && value) {
        // Global debounced fonksiyonu kullan
        window.debouncedUpdateNameFromLabel(value, nameInput);
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
      // Alan adı manuel değiştirildiğinde
      window.selectedElement.properties.name = value;
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
    } else if (name === "is_active" || name === "is_system" || name === "required") {
      // Boolean özellikleri doğrudan güncelle
      window.selectedElement.properties[name] = value;
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
    
    const rowElement = window.selectedElement.querySelector('.row-element');
    if (!rowElement) return;
    
    if (!rowElement.classList.contains('g-3')) {
      rowElement.classList.add('g-3');
    }

    const currentColumns = window.selectedElement.properties.columns || [];
    const columnElements = rowElement.querySelectorAll(".column-element") || [];

    // Sütun sayısı azalıyorsa
    if (columnCount < currentColumns.length) {
      let hasContent = false;
      let elementsToSave = [];

      // Silinecek sütunlardaki içerikleri kontrol et
      for (let i = columnCount; i < columnElements.length; i++) {
        const elements = columnElements[i].querySelectorAll(".form-element");
        if (elements.length > 0) {
          hasContent = true;
          elements.forEach(el => {
            // Element özelliklerini ve DOM yapısını kaydet
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

    // Yeni sütun dizisi oluştur
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

    // Yeni sütun dizisi oluştur
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

    // Row properties'i güncelle
    window.selectedElement.properties.columns = newColumns;

    // Satır içeriğini güncelle
    window.updateRowContent();

    // Panel güncelle ve durumu kaydet
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
    
    // Sütun sayısını al
    const columnCount = window.selectedElement.properties.columns.length;
    
    // Sütun sayısına göre standart genişlik hesapla
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
    
    // Eğer 2 sütun varsa, her sütunun genişliği 6 olmalı
    if (columnCount === 2) {
      window.selectedElement.properties.columns.forEach((col, i) => {
        col.width = 6;
      });
    } else {
      // Diğer durumlar için normal genişlik güncellemesi yap
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
      // Şablon özellikleri oluştur
      const templateProps = {...properties};
      
      // Checkbox için özel özellikler
      if (type === "checkbox") {
        templateProps.checkbox_label = properties.checkbox_label || "Onay";
        templateProps.default_value_text = properties.default_value_text || "";
        
        // Varsayılan durum için HTML özelliğini ayarla (düz mantık)
        if (properties.default_value === true || properties.default_value === "true") {
          templateProps.default_value = "checked";
        } else {
          templateProps.default_value = "";
        }
      }
      
      // Switch (anahtar) için özel özellikler
      if (type === "switch") {
        templateProps.switch_label = properties.switch_label || "Anahtar";
        templateProps.default_value_text = properties.default_value_text || "";
        
        // Varsayılan durum için HTML özelliğini ayarla
        if (properties.default_value === true || properties.default_value === "true") {
          templateProps.default_value = "checked";
        } else {
          templateProps.default_value = "";
        }
      }
      
      // Select (açılır liste) için özel işlemler
      if (type === "select" && properties.options && properties.options.length > 0) {
        // Seçenek listesini oluştur
        const content = window.selectedElement.querySelector(".element-content");
        const select = content.querySelector("select");
        
        if (select) {
          // İlk seçeneği koru (placeholder)
          const placeholder = select.querySelector('option[disabled]');
          select.innerHTML = '';
          
          if (placeholder) {
            select.appendChild(placeholder);
          }
          
          // Seçenekleri ekle
          properties.options.forEach(option => {
            const optionElement = document.createElement('option');
            const optValue = window.getOptionValue ? window.getOptionValue(option) : option.value;
            const optLabel = window.getOptionLabel ? window.getOptionLabel(option) : option.label;
            optionElement.value = optValue;
            optionElement.textContent = optLabel;

            // Varsayılan seçeneği seç
            if (option.is_default || properties.default_value === optValue) {
              optionElement.selected = true;
            }

            select.appendChild(optionElement);
          });
        }
      }
      
      // Radio (seçim düğmeleri) için özel işlemler
      if (type === "radio" && properties.options && properties.options.length > 0) {
        // Seçenek listesini oluştur
        const content = window.selectedElement.querySelector(".element-content");
        const radioOptions = content.querySelector(".radio-options");
        
        if (radioOptions) {
          radioOptions.innerHTML = '';
          
          // Seçenekleri ekle
          properties.options.forEach((option, index) => {
            const radioDiv = document.createElement('div');
            radioDiv.className = 'form-check';

            const optValue = window.getOptionValue ? window.getOptionValue(option) : option.value;
            const optLabel = window.getOptionLabel ? window.getOptionLabel(option) : option.label;

            const radioInput = document.createElement('input');
            radioInput.className = 'form-check-input';
            radioInput.type = 'radio';
            radioInput.name = properties.name;
            radioInput.id = `${properties.name}_${index}`;
            radioInput.value = optValue;
            radioInput.setAttribute('onchange', 'window.updateRadioState(this)');

            // Varsayılan seçeneği seç
            if (option.is_default || properties.default_value === optValue) {
              radioInput.checked = true;
            }

            const radioLabel = document.createElement('label');
            radioLabel.className = 'form-check-label';
            radioLabel.htmlFor = `${properties.name}_${index}`;
            radioLabel.textContent = optLabel;

            radioDiv.appendChild(radioInput);
            radioDiv.appendChild(radioLabel);
            radioOptions.appendChild(radioDiv);
          });
        }
      }
      
      // Normal elementler için içeriği güncelle
      const content = window.selectedElement.querySelector(".element-content");
      content.innerHTML = window.renderTemplate(window.elementTemplates[type], templateProps);

      // Select ve radio için seçenekleri ekle
      if (type === "select" && properties.options) {
        const selectElement = content.querySelector("select");
        if (selectElement) {
          properties.options.forEach((option) => {
            const optionElement = document.createElement("option");
            const optValue = window.getOptionValue ? window.getOptionValue(option) : (option.value || "");
            const optLabel = window.getOptionLabel ? window.getOptionLabel(option) : (option.label || "");
            optionElement.value = optValue;
            optionElement.textContent = optLabel;
            if (option.is_default || properties.default_value === optValue) {
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
            const optValue = window.getOptionValue ? window.getOptionValue(option) : (option.value || "");
            const optLabel = window.getOptionLabel ? window.getOptionLabel(option) : (option.label || "");
            const isDefault = option.is_default || properties.default_value === optValue;
            radioElement.innerHTML = `
                          <input class="form-check-input" type="radio" name="${
                            properties.name || ""
                          }" ${isDefault ? 'checked' : ''}>
                          <span class="form-check-label">${optLabel}</span>
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

    const columnsInProps = window.selectedElement.properties.columns || [];
    if (columnsInProps.length === 0) {
      console.warn("Satır için sütun tanımlanmamış");
      return;
    }
    
    // Mevcut sütunları al ve element içeriğini yedekle
    const existingColumns = rowElement.querySelectorAll(".column-element");
    const columnContents = [];
    
    // Her sütundaki elementleri yedekle
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
    
    // Satırı temizle ve yeni sütunları oluştur
    rowElement.innerHTML = '';
    
    // Sütun genişliklerinin toplamını kontrol et
    const totalWidth = columnsInProps.reduce((sum, col) => sum + parseInt(col.width || 0), 0);
    if (totalWidth !== 12) {
      console.warn(`Sütun genişliklerinin toplamı 12 olmalı, şu an: ${totalWidth}`);
      
      // Genişlikleri yeniden hesapla
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
      
      // Genişlikleri düzelt
      columnsInProps.forEach((col, i) => {
        col.width = defaultWidth + (i < extraWidth ? 1 : 0);
      });
    }
    
    // Sütunları oluştur
    columnsInProps.forEach((column, index) => {
      const columnDiv = document.createElement('div');
      columnDiv.className = `col-md-${column.width} column-element`;
      columnDiv.dataset.width = column.width;
      columnDiv.dataset.index = index;
      
      // Sütunu satıra ekle
      rowElement.appendChild(columnDiv);
    });
    
    // Yeni oluşturulan sütunları al
    const newColumns = rowElement.querySelectorAll(".column-element");
    
    // Yedeklenen içeriği yeni sütunlara ekle
    for (let i = 0; i < Math.min(columnContents.length, newColumns.length); i++) {
      const columnElements = columnContents[i];
      
      // Sütundaki her elementi yeni sütuna ekle
      columnElements.forEach(elementData => {
        const newElement = window.createFormElement(elementData.type, elementData.properties);
        if (newElement) {
          newColumns[i].appendChild(newElement);
        }
      });
    }
    
    // Eğer eski sütun sayısı yeni sütun sayısından fazlaysa, fazla elementleri ilk sütuna ekle
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
    
    // Boş sütunlara placeholder ekle
    newColumns.forEach(column => {
      if (column.children.length === 0) {
        const placeholder = document.createElement('div');
        placeholder.className = 'column-placeholder';
        placeholder.innerHTML = '<i class="fas fa-plus me-2"></i> Buraya element sürükleyin';
        column.appendChild(placeholder);
      }
    });
    
    // SortableJS'yi yeniden başlat
    window.initializeColumnSortables();
  };

  
  // Canvas'taki onay kutusunun durumu değiştiğinde özellikler panelini güncelle
  window.updateCheckboxState = function(checkbox) {
    // Onay kutusunun bulunduğu form elementini bul
    const formElement = checkbox.closest('.form-element');
    if (!formElement) return;
    
    // Eğer bu element seçili değilse, önce seç
    if (window.selectedElement !== formElement) {
      // Mevcut seçili elementi temizle
      if (window.selectedElement) {
        window.selectedElement.classList.remove('selected');
      }
      
      // Yeni elementi seç
      window.selectedElement = formElement;
      window.selectedElement.classList.add('selected');
      
      // Özellikler panelini güncelle
      window.updatePropertiesPanel();
    }
    
    // Onay kutusunun durumunu al
    const isChecked = checkbox.checked;
    
    // Özellikler panelindeki radio butonlarını güncelle
    const defaultValueTrue = window.propertiesPanel.querySelector('#default_value_true');
    const defaultValueFalse = window.propertiesPanel.querySelector('#default_value_false');
    
    if (defaultValueTrue && defaultValueFalse) {
      // Düz mantık: Onay kutusu işaretli ise, "İşaretli" seçeneği seçili olmalı
      if (isChecked) {
        defaultValueTrue.checked = true;
        defaultValueFalse.checked = false;
        window.selectedElement.properties.default_value = true;
      } else {
        defaultValueFalse.checked = true;
        defaultValueTrue.checked = false;
        window.selectedElement.properties.default_value = false;
      }
      
      // Durum kaydet
      window.saveState();
    }
    
    // Olayın yayılmasını durdur (tıklama olayının form elementine ulaşmasını engelle)
    event.stopPropagation();
  };
  
  // Canvas'taki anahtarın durumu değiştiğinde özellikler panelini güncelle
  window.updateSwitchState = function(switchElement) {
    // Anahtarın bulunduğu form elementini bul
    const formElement = switchElement.closest('.form-element');
    if (!formElement) return;
    
    // Eğer bu element seçili değilse, önce seç
    if (window.selectedElement !== formElement) {
      // Mevcut seçili elementi temizle
      if (window.selectedElement) {
        window.selectedElement.classList.remove('selected');
      }
      
      // Yeni elementi seç
      window.selectedElement = formElement;
      window.selectedElement.classList.add('selected');
      
      // Özellikler panelini güncelle
      window.updatePropertiesPanel();
    }
    
    // Anahtarın durumunu al
    const isChecked = switchElement.checked;
    
    // Özellikler panelindeki radio butonlarını güncelle
    const defaultValueTrue = window.propertiesPanel.querySelector('#default_value_true');
    const defaultValueFalse = window.propertiesPanel.querySelector('#default_value_false');
    
    if (defaultValueTrue && defaultValueFalse) {
      // Düz mantık: Anahtar açık ise, "Açık" seçeneği seçili olmalı
      if (isChecked) {
        defaultValueTrue.checked = true;
        defaultValueFalse.checked = false;
        window.selectedElement.properties.default_value = true;
      } else {
        defaultValueFalse.checked = true;
        defaultValueTrue.checked = false;
        window.selectedElement.properties.default_value = false;
      }
      
      // Durum kaydet
      window.saveState();
    }
    
    // Olayın yayılmasını durdur (tıklama olayının form elementine ulaşmasını engelle)
    event.stopPropagation();
  };
  
  // Canvas'taki açılır listenin durumu değiştiğinde özellikler panelini güncelle
  window.updateSelectState = function(selectElement) {
    // Açılır listenin bulunduğu form elementini bul
    const formElement = selectElement.closest('.form-element');
    if (!formElement) return;
    
    // Eğer bu element seçili değilse, önce seç
    if (window.selectedElement !== formElement) {
      // Mevcut seçili elementi temizle
      if (window.selectedElement) {
        window.selectedElement.classList.remove('selected');
      }
      
      // Yeni elementi seç
      window.selectedElement = formElement;
      window.selectedElement.classList.add('selected');
      
      // Özellikler panelini güncelle
      window.updatePropertiesPanel();
    }
    
    // Seçilen değeri al
    const selectedValue = selectElement.value;
    
    // Özellikler panelindeki seçenekleri güncelle
    if (window.selectedElement.properties.options && window.selectedElement.properties.options.length > 0) {
      // Tüm seçeneklerin varsayılan değerini sıfırla
      window.selectedElement.properties.options.forEach(option => {
        option.is_default = option.value === selectedValue;
      });
      
      // Varsayılan değeri güncelle
      window.selectedElement.properties.default_value = selectedValue;
      
      // Özellikler panelindeki seçenekleri güncelle
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
      
      // Durum kaydet
      window.saveState();
    }
    
    // Olayın yayılmasını durdur
    event.stopPropagation();
  };
  
  // Canvas'taki seçim düğmelerinin durumu değiştiğinde özellikler panelini güncelle
  window.updateRadioState = function(radioElement) {
    // Seçim düğmesinin bulunduğu form elementini bul
    const formElement = radioElement.closest('.form-element');
    if (!formElement) return;
    
    // Eğer bu element seçili değilse, önce seç
    if (window.selectedElement !== formElement) {
      // Mevcut seçili elementi temizle
      if (window.selectedElement) {
        window.selectedElement.classList.remove('selected');
      }
      
      // Yeni elementi seç
      window.selectedElement = formElement;
      window.selectedElement.classList.add('selected');
      
      // Özellikler panelini güncelle
      window.updatePropertiesPanel();
    }
    
    // Seçilen değeri al
    const selectedValue = radioElement.value;
    
    // Özellikler panelindeki seçenekleri güncelle
    if (window.selectedElement.properties.options && window.selectedElement.properties.options.length > 0) {
      // Tüm seçeneklerin varsayılan değerini sıfırla
      window.selectedElement.properties.options.forEach(option => {
        option.is_default = option.value === selectedValue;
      });
      
      // Varsayılan değeri güncelle
      window.selectedElement.properties.default_value = selectedValue;
      
      // Özellikler panelindeki seçenekleri güncelle
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
      
      // Durum kaydet
      window.saveState();
    }
    
    // Olayın yayılmasını durdur
    event.stopPropagation();
  };
  
  // Türkçe karakterleri İngilizce karakterlere dönüştürme
  window.slugifyTurkish = function(text) {
    if (!text) return '';
    
    // Türkçe karakter çevrimi
    const turkishChars = { 'ç': 'c', 'ğ': 'g', 'ı': 'i', 'i': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u', 
                          'Ç': 'C', 'Ğ': 'G', 'I': 'I', 'İ': 'I', 'Ö': 'O', 'Ş': 'S', 'Ü': 'U' };
    
    // Türkçe karakterleri değiştir
    let slug = text.replace(/[çğıiöşüÇĞIİÖŞÜ]/g, function(char) {
      return turkishChars[char] || char;
    });
    
    // Diğer özel karakterleri ve boşlukları alt çizgi ile değiştir
    slug = slug.toLowerCase()
              .replace(/[^a-z0-9_]+/g, '_')  // Harfler, rakamlar ve alt çizgi hariç tüm karakterleri alt çizgiye çevir
              .replace(/^_+|_+$/g, '')       // Baştaki ve sondaki alt çizgileri temizle
              .replace(/_+/g, '_');          // Ardışık alt çizgileri tek alt çizgiye indir
    
    // Rakamla başlayamaz, kontrolü
    if (/^[0-9]/.test(slug)) {
      slug = 'a_' + slug;  // Rakamla başlıyorsa başına 'a_' ekle
    }
    
    return slug;
  };
});