// Widget Management Form Builder Elementleri ve Varsayılan Özellikleri
document.addEventListener("DOMContentLoaded", function() {
  // Form elementlerinin varsayılan özellikleri
  window.defaultProperties = {
    text: {
      label: "Metin Alanı",
      name: "text_field",
      placeholder: "Metin giriniz",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      default_value: "",
    },
    textarea: {
      label: "Uzun Metin",
      name: "textarea_field",
      placeholder: "Uzun metin giriniz",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      default_value: "",
    },
    number: {
      label: "Sayı Alanı",
      name: "number_field",
      placeholder: "Sayı giriniz",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      default_value: "",
    },
    email: {
      label: "E-posta Adresi",
      name: "email_field",
      placeholder: "E-posta adresinizi giriniz",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      default_value: "",
    },
    select: {
      label: "Açılır Liste",
      name: "select_field",
      placeholder: "Seçiniz",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      options: [
        { value: "option1", label: "Seçenek 1", is_default: true },
        { value: "option2", label: "Seçenek 2", is_default: false },
        { value: "option3", label: "Seçenek 3", is_default: false },
      ],
      default_value: "option1"
    },
    checkbox: {
      label: "Onay Kutusu",
      name: "checkbox_field",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      default_value: false,
      checkbox_label: "Onay",
      default_value_text: "",
    },
    radio: {
      label: "Seçim Düğmeleri",
      name: "radio_field",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      options: [
        { value: "option1", label: "Seçenek 1", is_default: true },
        { value: "option2", label: "Seçenek 2", is_default: false },
        { value: "option3", label: "Seçenek 3", is_default: false },
      ],
      default_value: "option1"
    },
    switch: {
      label: "Anahtar",
      name: "switch_field",
      help_text: "",
      active_label: "Evet",
      inactive_label: "Hayır",
      default_value: false,
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
    },
    color: {
      label: "Renk Seçici",
      name: "color_field",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      default_value: "#206bc4",
    },
    date: {
      label: "Tarih",
      name: "date_field",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      default_value: new Date().toISOString().split('T')[0],
    },
    time: {
      label: "Saat",
      name: "time_field",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      default_value: new Date().toTimeString().split(' ')[0].slice(0, 5),
    },
    file: {
      label: "Dosya Yükleme",
      name: "file_field",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      default_value: "",
    },
    image: {
      label: "Resim Yükleme",
      name: "image_field",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      default_value: "",
    },
    image_multiple: {
      label: "Çoklu Resim",
      name: "image_multiple_field",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      default_value: "",
    },
    password: {
      label: "Şifre Alanı",
      name: "password_field",
      placeholder: "Şifre giriniz",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      default_value: "",
    },
    tel: {
      label: "Telefon Numarası",
      name: "tel_field",
      placeholder: "Telefon numarası giriniz",
      help_text: "", 
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      default_value: "",
    },
    url: {
      label: "Web Adresi",
      name: "url_field",
      placeholder: "Web adresi giriniz",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      default_value: "",
    },
    range: {
      label: "Değer Aralığı",
      name: "range_field",
      help_text: "",
      width: 12,
      required: false,
      is_active: true,
      is_system: false,
      min: 0,
      max: 100,
      step: 1,
      default_value: 50,
    },
    button: {
      label: "Buton",
      width: 12,
      button_style: "primary",
    },
    row: {
      columns: [
        { index: 1, width: 6 },
        { index: 2, width: 6 },
      ],
    },
    heading: {
      label: "Başlık",
      content: "Başlık Metni",
      size: "h3",
      width: 12,
      align: "left",
    },
    paragraph: {
      label: "Paragraf",
      content: "Paragraf metni burada yer alacak.",
      width: 12,
      align: "left",
    },
    divider: {
      label: "Ayırıcı Çizgi",
      style: "solid",
      width: 12,
      color: "#e5e7eb",
      thickness: "1px"
    },
    spacer: {
      label: "Boşluk",
      height: "2rem",
      width: 12,
    },
    card: {
      label: "Kart",
      title: "Kart Başlığı",
      content: "Kart içeriği burada yer alacak.",
      width: 12,
      has_header: true,
      has_footer: false,
    },
    tab_group: {
      label: "Sekme Grubu",
      tabs: [
        { title: "Sekme 1", content: "İçerik 1" },
        { title: "Sekme 2", content: "İçerik 2" },
      ],
      width: 12,
    },
  };
  
  // Element oluşturma fonksiyonu
  window.createFormElement = function(type, properties) {
    console.log('createFormElement çağrıldı. Element tipi:', type);
    if (!type || typeof type !== "string") {
      console.error("Geçersiz element tipi:", type);
      return null;
    }

    if (!properties) {
      properties = window.defaultProperties[type]
        ? JSON.parse(JSON.stringify(window.defaultProperties[type]))
        : {};
    }

    if (type === "color" && properties.default_value && properties.default_value.startsWith("rgba")) {
      try {
        const rgba = properties.default_value.match(/rgba\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*([\d.]+)\s*\)/);
        if (rgba) {
          const r = parseInt(rgba[1]);
          const g = parseInt(rgba[2]);
          const b = parseInt(rgba[3]);
          properties.default_value = window.rgbToHex(r, g, b);
        } else {
          properties.default_value = "#206bc4";
        }
      } catch (e) {
        properties.default_value = "#206bc4";
      }
    }

    const elementId = "element-" + ++window.elementCounter;
    const formElement = document.createElement("div");
    formElement.className = "form-element";
    formElement.dataset.id = elementId;
    formElement.dataset.type = type;

    const header = document.createElement("div");
    header.className = "element-header";

    let elementTitle = '';
    
    if (type === 'row') {
      elementTitle = 'Satır Düzeni';
    } else if (type === 'heading') {
      elementTitle = 'Başlık: ' + (properties.content || '');
    } else if (type === 'paragraph') {
      elementTitle = 'Paragraf';
    } else if (type === 'divider') {
      elementTitle = 'Ayırıcı Çizgi';
    } else if (type === 'spacer') {
      elementTitle = 'Boşluk';
    } else if (type === 'card') {
      elementTitle = 'Kart: ' + (properties.title || '');
    } else if (type === 'tab_group') {
      elementTitle = 'Sekme Grubu';
    } else if (type === 'image_multiple') {
      elementTitle = 'Çoklu Resim: ' + (properties.label || '');
    } else {
      elementTitle = properties.label || type.charAt(0).toUpperCase() + type.slice(1);
    }

    header.innerHTML = `
            <div class="element-handle">
                <i class="fas fa-grip-lines"></i>
            </div>
            <div class="element-title">
                ${elementTitle}
            </div>
            <div class="element-actions">
                <button type="button" class="btn btn-sm" data-action="duplicate">
                    <i class="fas fa-clone"></i>
                </button>
                <button type="button" class="btn btn-sm text-danger" data-action="remove">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

    const content = document.createElement("div");
    content.className = "element-content";

    const template = window.elementTemplates[type];
    if (template) {
      content.innerHTML = window.renderTemplate(template, properties);

      if (type === "select" && properties.options) {
        const selectElement = content.querySelector("select");
        if (selectElement) {
          properties.options.forEach((option) => {
            const optionElement = document.createElement("option");
            optionElement.value = option.value;
            optionElement.textContent = option.label;
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
                            <input class="form-check-input" type="radio" name="${properties.name}" ${isDefault ? 'checked' : ''}>
                            <span class="form-check-label">${option.label}</span>
                        `;
            radioContainer.appendChild(radioElement);
          });
        }
      } else if (type === "tab_group" && properties.tabs) {
        const tabList = content.querySelector(".nav-tabs");
        const tabContent = content.querySelector(".tab-content");
        
        if (tabList && tabContent) {
          properties.tabs.forEach((tab, index) => {
            const tabItem = document.createElement("li");
            tabItem.className = "nav-item";
            tabItem.innerHTML = `
              <a href="#tab-${elementId}-${index}" class="nav-link ${index === 0 ? 'active' : ''}" data-bs-toggle="tab">
                ${tab.title}
              </a>
            `;
            tabList.appendChild(tabItem);
            
            const tabPane = document.createElement("div");
            tabPane.className = `tab-pane ${index === 0 ? 'active' : ''}`;
            tabPane.id = `tab-${elementId}-${index}`;
            tabPane.innerHTML = `<p>${tab.content}</p>`;
            tabContent.appendChild(tabPane);
          });
        }
      }
    } else {
      content.innerHTML = `<div class="alert alert-warning">Şablon bulunamadı: ${type}</div>`;
    }

    if (type !== "row") {
      formElement.dataset.width = properties.width || 12;
      formElement.style.width = `${(properties.width * 100) / 12}%`;
    }

    formElement.properties = Object.assign({}, properties);

    formElement.appendChild(header);
    formElement.appendChild(content);

    formElement.addEventListener("click", function (e) {
      e.stopPropagation();
      window.selectElement(formElement);
    });

    const buttons = formElement.querySelectorAll("[data-action]");
    buttons.forEach((button) => {
      button.addEventListener("click", function (e) {
        e.stopPropagation();
        const action = this.dataset.action;

        if (action === "remove") {
          if (formElement.properties.is_system === true) {
            alert("Bu element bir sistem ayarıdır ve silinemez.");
            return;
          }
          
          formElement.remove();
          if (window.selectedElement === formElement) {
            window.clearSelectedElement();
          }
          window.checkEmptyCanvas();
          window.saveState();
        } else if (action === "duplicate") {
          if (formElement.properties.is_system === true) {
            alert("Sistem ayarları kopyalanamaz.");
            return;
          }
          
          const elementProps = formElement.properties
            ? JSON.parse(JSON.stringify(formElement.properties))
            : JSON.parse(JSON.stringify(window.defaultProperties[type] || {}));
          
          elementProps.is_system = false;
          
          if (elementProps.name) {
            elementProps.name = elementProps.name + "_copy";
          }
          
          const duplicate = window.createFormElement(type, elementProps);

          if (duplicate) {
            formElement.parentNode.insertBefore(
              duplicate,
              formElement.nextSibling
            );
            window.saveState();
          }
        }
      });
    });

    return formElement;
  };
});