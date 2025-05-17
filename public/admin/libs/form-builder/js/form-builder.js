// Form Builder JavaScript Kodu
document.addEventListener("DOMContentLoaded", function () {
    // Değişkenler
    let elementCounter = 0;
    let selectedElement = null;
    const formCanvas = document.getElementById("form-canvas");
    const emptyCanvas = document.getElementById("empty-canvas");
    const elementPalette = document.getElementById("element-palette");
    const propertiesPanel = document.getElementById("properties-panel");
  
    // Panel sekmeleri için olay dinleyiciler
    const tabs = document.querySelectorAll(".panel-tab");
    tabs.forEach((tab) => {
      tab.addEventListener("click", function () {
        const tabName = this.getAttribute("data-tab");
        const tabContainer = this.closest(".panel-tabs");
        const panelSide = this.closest(".panel__left") ? "left" : "right";
  
        // Tüm sekmeleri pasif yap
        tabContainer.querySelectorAll(".panel-tab").forEach((t) => {
          t.classList.remove("active");
        });
  
        // Seçilen sekmeyi aktif yap
        this.classList.add("active");
  
        // İçerikleri gizle
        const panelContent = this.closest(".panel__left, .panel__right");
        panelContent.querySelectorAll(".panel-tab-content").forEach((content) => {
          content.classList.remove("active");
        });
  
        // İlgili içeriği göster
        const contentSelector = `.panel-tab-content[data-tab-content="${tabName}"]`;
        const content = panelContent.querySelector(contentSelector);
        if (content) {
          content.classList.add("active");
        }
  
        // LocalStorage'e kaydet
        localStorage.setItem(`form_builder_${panelSide}_tab`, tabName);
      });
    });
  
    // Panel açma/kapama butonları için olay dinleyiciler
    const toggleButtons = document.querySelectorAll(".panel-toggle");
    toggleButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const panel = this.closest(".panel__left, .panel__right");
        panel.classList.toggle("collapsed");
  
        // Panel durumunu localStorage'a kaydet
        const panelSide = panel.classList.contains("panel__left")
          ? "left"
          : "right";
        localStorage.setItem(
          `form_builder_${panelSide}_collapsed`,
          panel.classList.contains("collapsed")
        );
      });
    });
  
    // Kategori açma/kapama için olay dinleyiciler
    const categoryHeaders = document.querySelectorAll(".block-category-header");
    categoryHeaders.forEach((header) => {
      header.addEventListener("click", function () {
        const category = this.closest(".block-category");
        category.classList.toggle("collapsed");
  
        // Kategori durumunu localStorage'a kaydet
        const categoryName = this.querySelector("span").textContent.trim();
        const categories = JSON.parse(
          localStorage.getItem("form_builder_categories") || "{}"
        );
        categories[categoryName] = category.classList.contains("collapsed");
        localStorage.setItem(
          "form_builder_categories",
          JSON.stringify(categories)
        );
      });
    });
  
    // Form elementlerinin varsayılan özellikleri
    const defaultProperties = {
      text: {
        label: "Metin Alanı",
        name: "text_field",
        placeholder: "Metin giriniz",
        help_text: "",
        width: 12,
        required: false,
        default_value: "",
      },
      textarea: {
        label: "Uzun Metin",
        name: "textarea_field",
        placeholder: "Uzun metin giriniz",
        help_text: "",
        width: 12,
        required: false,
        default_value: "",
      },
      number: {
        label: "Sayı Alanı",
        name: "number_field",
        placeholder: "Sayı giriniz",
        help_text: "",
        width: 12,
        required: false,
        default_value: "",
      },
      email: {
        label: "E-posta Adresi",
        name: "email_field",
        placeholder: "E-posta adresinizi giriniz",
        help_text: "",
        width: 12,
        required: false,
        default_value: "",
      },
      select: {
        label: "Açılır Liste",
        name: "select_field",
        placeholder: "Seçiniz",
        help_text: "",
        width: 12,
        required: false,
        options: [
          { value: "option1", label: "Seçenek 1" },
          { value: "option2", label: "Seçenek 2" },
          { value: "option3", label: "Seçenek 3" },
        ],
      },
      checkbox: {
        label: "Onay Kutusu",
        name: "checkbox_field",
        help_text: "",
        width: 12,
        required: false,
        default_checked: false,
      },
      radio: {
        label: "Seçim Düğmeleri",
        name: "radio_field",
        help_text: "",
        width: 12,
        required: false,
        options: [
          { value: "option1", label: "Seçenek 1" },
          { value: "option2", label: "Seçenek 2" },
          { value: "option3", label: "Seçenek 3" },
        ],
      },
      switch: {
        label: "Anahtar",
        name: "switch_field",
        help_text: "",
        width: 12,
        required: false,
        default_checked: false,
      },
      row: {
        columns: [
          { index: 1, width: 6 },
          { index: 2, width: 6 },
        ],
      },
    };
  
    // Element şablonları
    const elementTemplates = {
      text: `
              <div class="mb-3">
                  <label class="form-label">{label}</label>
                  <input type="text" class="form-control" placeholder="{placeholder}">
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      textarea: `
              <div class="mb-3">
                  <label class="form-label">{label}</label>
                  <textarea class="form-control" rows="4" placeholder="{placeholder}"></textarea>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      number: `
              <div class="mb-3">
                  <label class="form-label">{label}</label>
                  <input type="number" class="form-control" placeholder="{placeholder}">
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      email: `
              <div class="mb-3">
                  <label class="form-label">{label}</label>
                  <input type="email" class="form-control" placeholder="{placeholder}">
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      select: `
              <div class="mb-3">
                  <label class="form-label">{label}</label>
                  <select class="form-select">
                      <option value="" selected disabled>{placeholder}</option>
                      <!-- Seçenekler JavaScript tarafından eklenecek -->
                  </select>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      checkbox: `
              <div class="mb-3">
                  <label class="form-check">
                      <input class="form-check-input" type="checkbox">
                      <span class="form-check-label">{label}</span>
                  </label>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      radio: `
              <div class="mb-3">
                  <label class="form-label">{label}</label>
                  <div class="radio-options">
                      <!-- Seçenekler JavaScript tarafından eklenecek -->
                  </div>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      switch: `
              <div class="mb-3">
                  <label class="form-check form-switch">
                      <input class="form-check-input" type="checkbox">
                      <span class="form-check-label">{label}</span>
                  </label>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      row: `
              <div class="row row-element d-flex">
                  <div class="col-md-6 column-element" data-width="6">
                      <div class="column-placeholder">
                          <i class="fas fa-plus me-2"></i> Buraya element sürükleyin
                      </div>
                  </div>
                  <div class="col-md-6 column-element" data-width="6">
                      <div class="column-placeholder">
                          <i class="fas fa-plus me-2"></i> Buraya element sürükleyin
                      </div>
                  </div>
              </div>
          `,
    };
  
    // Özellik paneli şablonları
    const propertyTemplates = {
      text: `
              <h4 class="fw-bold p-3 border-bottom">Metin Elementini Düzenle</h4>
              <div class="p-3">
                  <div class="mb-3">
                      <label class="form-label">Etiket</label>
                      <input type="text" class="form-control" name="label" value="{label}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Alan Adı</label>
                      <input type="text" class="form-control" name="name" value="{name}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Placeholder</label>
                      <input type="text" class="form-control" name="placeholder" value="{placeholder}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Yardım Metni</label>
                      <input type="text" class="form-control" name="help_text" value="{help_text}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Genişlik</label>
                      <select class="form-select" name="width">
                          <option value="12" {width12}>Tam Genişlik (12/12)</option>
                          <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                          <option value="4" {width4}>Üçte Bir (4/12)</option>
                          <option value="3" {width3}>Çeyrek (3/12)</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="required" {required}>
                          <span class="form-check-label">Zorunlu Alan</span>
                      </label>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Varsayılan Değer</label>
                      <input type="text" class="form-control" name="default_value" value="{default_value}">
                  </div>
              </div>
          `,
      textarea: `
              <h4 class="fw-bold p-3 border-bottom">Uzun Metin Elementini Düzenle</h4>
              <div class="p-3">
                  <div class="mb-3">
                      <label class="form-label">Etiket</label>
                      <input type="text" class="form-control" name="label" value="{label}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Alan Adı</label>
                      <input type="text" class="form-control" name="name" value="{name}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Placeholder</label>
                      <input type="text" class="form-control" name="placeholder" value="{placeholder}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Yardım Metni</label>
                      <input type="text" class="form-control" name="help_text" value="{help_text}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Genişlik</label>
                      <select class="form-select" name="width">
                          <option value="12" {width12}>Tam Genişlik (12/12)</option>
                          <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                          <option value="4" {width4}>Üçte Bir (4/12)</option>
                          <option value="3" {width3}>Çeyrek (3/12)</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="required" {required}>
                          <span class="form-check-label">Zorunlu Alan</span>
                      </label>
                  </div>
              </div>
          `,
      select: `
              <h4 class="fw-bold p-3 border-bottom">Açılır Liste Elementini Düzenle</h4>
              <div class="p-3">
                  <div class="mb-3">
                      <label class="form-label">Etiket</label>
                      <input type="text" class="form-control" name="label" value="{label}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Alan Adı</label>
                      <input type="text" class="form-control" name="name" value="{name}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Placeholder</label>
                      <input type="text" class="form-control" name="placeholder" value="{placeholder}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Genişlik</label>
                      <select class="form-select" name="width">
                          <option value="12" {width12}>Tam Genişlik (12/12)</option>
                          <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                          <option value="4" {width4}>Üçte Bir (4/12)</option>
                          <option value="3" {width3}>Çeyrek (3/12)</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="required" {required}>
                          <span class="form-check-label">Zorunlu Alan</span>
                      </label>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Seçenekler</label>
                      <div id="options-container">
                          <!-- Seçenekler JavaScript ile doldurulacak -->
                      </div>
                      <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-option">
                          <i class="fas fa-plus me-1"></i> Seçenek Ekle
                      </button>
                  </div>
              </div>
          `,
      number: `
              <h4 class="fw-bold p-3 border-bottom">Sayı Elementini Düzenle</h4>
              <div class="p-3">
                  <div class="mb-3">
                      <label class="form-label">Etiket</label>
                      <input type="text" class="form-control" name="label" value="{label}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Alan Adı</label>
                      <input type="text" class="form-control" name="name" value="{name}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Placeholder</label>
                      <input type="text" class="form-control" name="placeholder" value="{placeholder}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Yardım Metni</label>
                      <input type="text" class="form-control" name="help_text" value="{help_text}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Genişlik</label>
                      <select class="form-select" name="width">
                          <option value="12" {width12}>Tam Genişlik (12/12)</option>
                          <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                          <option value="4" {width4}>Üçte Bir (4/12)</option>
                          <option value="3" {width3}>Çeyrek (3/12)</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="required" {required}>
                          <span class="form-check-label">Zorunlu Alan</span>
                      </label>
                  </div>
              </div>
          `,
      email: `
              <h4 class="fw-bold p-3 border-bottom">E-posta Elementini Düzenle</h4>
              <div class="p-3">
                  <div class="mb-3">
                      <label class="form-label">Etiket</label>
                      <input type="text" class="form-control" name="label" value="{label}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Alan Adı</label>
                      <input type="text" class="form-control" name="name" value="{name}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Placeholder</label>
                      <input type="text" class="form-control" name="placeholder" value="{placeholder}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Yardım Metni</label>
                      <input type="text" class="form-control" name="help_text" value="{help_text}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Genişlik</label>
                      <select class="form-select" name="width">
                          <option value="12" {width12}>Tam Genişlik (12/12)</option>
                          <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                          <option value="4" {width4}>Üçte Bir (4/12)</option>
                          <option value="3" {width3}>Çeyrek (3/12)</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="required" {required}>
                          <span class="form-check-label">Zorunlu Alan</span>
                      </label>
                  </div>
              </div>
          `,
      checkbox: `
              <h4 class="fw-bold p-3 border-bottom">Onay Kutusu Elementini Düzenle</h4>
              <div class="p-3">
                  <div class="mb-3">
                      <label class="form-label">Etiket</label>
                      <input type="text" class="form-control" name="label" value="{label}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Alan Adı</label>
                      <input type="text" class="form-control" name="name" value="{name}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Yardım Metni</label>
                      <input type="text" class="form-control" name="help_text" value="{help_text}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Genişlik</label>
                      <select class="form-select" name="width">
                          <option value="12" {width12}>Tam Genişlik (12/12)</option>
                          <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                          <option value="4" {width4}>Üçte Bir (4/12)</option>
                          <option value="3" {width3}>Çeyrek (3/12)</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="required" {required}>
                          <span class="form-check-label">Zorunlu Alan</span>
                      </label>
                  </div>
                  <div class="mb-3">
                      <label class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="default_checked" {default_checked}>
                          <span class="form-check-label">Varsayılan İşaretli</span>
                      </label>
                  </div>
              </div>
          `,
      radio: `
              <h4 class="fw-bold p-3 border-bottom">Seçim Düğmesi Elementini Düzenle</h4>
              <div class="p-3">
                  <div class="mb-3">
                      <label class="form-label">Etiket</label>
                      <input type="text" class="form-control" name="label" value="{label}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Alan Adı</label>
                      <input type="text" class="form-control" name="name" value="{name}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Yardım Metni</label>
                      <input type="text" class="form-control" name="help_text" value="{help_text}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Genişlik</label>
                      <select class="form-select" name="width">
                          <option value="12" {width12}>Tam Genişlik (12/12)</option>
                          <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                          <option value="4" {width4}>Üçte Bir (4/12)</option>
                          <option value="3" {width3}>Çeyrek (3/12)</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="required" {required}>
                          <span class="form-check-label">Zorunlu Alan</span>
                      </label>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Seçenekler</label>
                      <div id="options-container">
                          <!-- Seçenekler JavaScript ile doldurulacak -->
                      </div>
                      <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-option">
                          <i class="fas fa-plus me-1"></i> Seçenek Ekle
                      </button>
                  </div>
              </div>
          `,
      switch: `
              <h4 class="fw-bold p-3 border-bottom">Anahtar Elementini Düzenle</h4>
              <div class="p-3">
                  <div class="mb-3">
                      <label class="form-label">Etiket</label>
                      <input type="text" class="form-control" name="label" value="{label}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Alan Adı</label>
                      <input type="text" class="form-control" name="name" value="{name}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Yardım Metni</label>
                      <input type="text" class="form-control" name="help_text" value="{help_text}">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Genişlik</label>
                      <select class="form-select" name="width">
                          <option value="12" {width12}>Tam Genişlik (12/12)</option>
                          <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                          <option value="4" {width4}>Üçte Bir (4/12)</option>
                          <option value="3" {width3}>Çeyrek (3/12)</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="required" {required}>
                          <span class="form-check-label">Zorunlu Alan</span>
                      </label>
                  </div>
                  <div class="mb-3">
                      <label class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="default_checked" {default_checked}>
                          <span class="form-check-label">Varsayılan İşaretli</span>
                      </label>
                  </div>
              </div>
          `,
      row: `
              <h4 class="fw-bold p-3 border-bottom">Satır Elementini Düzenle</h4>
              <div class="p-3">
                  <div class="mb-3">
                      <label class="form-label">Sütun Sayısı</label>
                      <select class="form-select" name="column-count">
                          <option value="2" {columns2}>2 Sütun</option>
                          <option value="3" {columns3}>3 Sütun</option>
                          <option value="4" {columns4}>4 Sütun</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Sütun Genişlikleri</label>
                      <div id="column-widths-container">
                          <!-- Sütun genişlikleri JavaScript ile doldurulacak -->
                      </div>
                  </div>
              </div>
          `,
    };
  
    // Şablon işleme (Mustache benzeri basit bir işleyici)
    function renderTemplate(template, data) {
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
          result = result.replace(regex, "checked");
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
  
      // Row sütun sayısı için özel koşullar
      if (data.columns) {
        for (let i = 2; i <= 4; i++) {
          const regex = new RegExp("{columns" + i + "}", "g");
          result = result.replace(
            regex,
            data.columns.length == i ? "selected" : ""
          );
        }
      }
  
      // Kalan yer tutucuları temizle
      result = result.replace(/{[^{}]+}/g, "");
  
      return result;
    }
  
    // Form elementlerini oluştur
    function createFormElement(type, properties) {
      if (!type || typeof type !== "string") {
        console.error("Geçersiz element tipi:", type);
        return null;
      }
  
      if (!properties) {
        properties = defaultProperties[type]
          ? JSON.parse(JSON.stringify(defaultProperties[type]))
          : {};
      }
  
      const elementId = "element-" + ++elementCounter;
      const formElement = document.createElement("div");
      formElement.className = "form-element";
      formElement.dataset.id = elementId;
      formElement.dataset.type = type;
  
      // Element header'ı oluştur
      const header = document.createElement("div");
      header.className = "element-header";
  
      const elementTitle =
        properties.label || type.charAt(0).toUpperCase() + type.slice(1);
  
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
  
      // Element içeriğini oluştur
      const content = document.createElement("div");
      content.className = "element-content";
  
      // Element şablonunu al ve işle
      const template = elementTemplates[type];
      if (template) {
        content.innerHTML = renderTemplate(template, properties);
  
        // Select ve radio için seçenekleri ekle
        if (type === "select" && properties.options) {
          const selectElement = content.querySelector("select");
          if (selectElement) {
            properties.options.forEach((option) => {
              const optionElement = document.createElement("option");
              optionElement.value = option.value;
              optionElement.textContent = option.label;
              selectElement.appendChild(optionElement);
            });
          }
        } else if (type === "radio" && properties.options) {
          const radioContainer = content.querySelector(".radio-options");
          if (radioContainer) {
            properties.options.forEach((option) => {
              const radioElement = document.createElement("div");
              radioElement.className = "form-check";
              radioElement.innerHTML = `
                              <input class="form-check-input" type="radio" name="${properties.name}">
                              <span class="form-check-label">${option.label}</span>
                          `;
              radioContainer.appendChild(radioElement);
            });
          }
        }
      } else {
        content.innerHTML = `<div class="alert alert-warning">Şablon bulunamadı: ${type}</div>`;
      }
  
      // Varsayılan genişlik ayarını ekle
      if (type !== "row") {
        formElement.dataset.width = properties.width || 12;
        formElement.style.width = `${(properties.width * 100) / 12}%`;
      }
  
      // Eleman özelliklerini sakla
      formElement.properties = Object.assign({}, properties);
  
      // Elementi birleştir
      formElement.appendChild(header);
      formElement.appendChild(content);
  
      // Event listener'ları ekle
      formElement.addEventListener("click", function (e) {
        e.stopPropagation();
        selectElement(formElement);
      });
  
      // Element işlemlerini ekle (silme, kopyalama)
      const buttons = formElement.querySelectorAll("[data-action]");
      buttons.forEach((button) => {
        button.addEventListener("click", function (e) {
          e.stopPropagation();
          const action = this.dataset.action;
  
          if (action === "remove") {
            formElement.remove();
            if (selectedElement === formElement) {
              clearSelectedElement();
            }
            checkEmptyCanvas();
          } else if (action === "duplicate") {
            // Kopya oluştururken özellikleri derin kopyalama
            const elementProps = formElement.properties
              ? JSON.parse(JSON.stringify(formElement.properties))
              : JSON.parse(JSON.stringify(defaultProperties[type] || {}));
            const duplicate = createFormElement(type, elementProps);
  
            if (duplicate) {
              formElement.parentNode.insertBefore(
                duplicate,
                formElement.nextSibling
              );
            }
          }
        });
      });
  
      return formElement;
    }
  
    // Element seçimi
    function selectElement(element) {
      // Önceki seçimi temizle
      if (selectedElement) {
        selectedElement.classList.remove("selected");
      }
  
      // Yeni elementi seç
      selectedElement = element;
      selectedElement.classList.add("selected");
  
      // Özellik panelini güncelle
      updatePropertiesPanel();
    }
  
    // Seçili elementi temizle
    function clearSelectedElement() {
      if (selectedElement) {
        selectedElement.classList.remove("selected");
      }
      selectedElement = null;
  
      // Özellik panelini sıfırla
      propertiesPanel.innerHTML = `
              <div class="text-center p-4">
                  <div class="h1 text-muted mb-3">
                      <i class="fas fa-mouse-pointer"></i>
                  </div>
                  <h3 class="text-muted">Element Seçilmedi</h3>
                  <p class="text-muted">Özelliklerini düzenlemek için bir form elementi seçin.</p>
              </div>
          `;
    }
  
    // Özellik panelini güncelle
    function updatePropertiesPanel() {
      if (!selectedElement) return;
  
      const type = selectedElement.dataset.type;
  
      // Özellik şablonunu al
      const propTemplate = propertyTemplates[type];
  
      if (!propTemplate) {
        propertiesPanel.innerHTML = `
                  <div class="alert alert-warning">
                      Bu element tipi için özellik paneli henüz eklenmemiş.
                  </div>
              `;
        return;
      }
  
      // Element özelliklerini al
      let properties = selectedElement.properties;
  
      // Özellikler tanımlı değilse, varsayılan özellikleri kullan
      if (!properties) {
        properties = defaultProperties[type]
          ? JSON.parse(JSON.stringify(defaultProperties[type]))
          : {};
        selectedElement.properties = properties;
      }
  
      // Genişlik değerlerini kontrol et
      let templateData = Object.assign({}, properties);
      templateData["width" + properties.width] = true;
  
      // Row için sütun sayısını kontrol et
      if (type === "row" && properties.columns) {
        templateData["columns" + properties.columns.length] = true;
      }
  
      // Şablonu işle
      propertiesPanel.innerHTML = renderTemplate(propTemplate, templateData);
  
      // Özellik değişikliklerini dinle
      const inputs = propertiesPanel.querySelectorAll("input, select");
      inputs.forEach((input) => {
        input.addEventListener("change", function () {
          updateElementProperty(input);
        });
  
        input.addEventListener("keyup", function () {
          updateElementProperty(input);
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
              optionRow.innerHTML = `
                              <input type="text" class="form-control" name="option-value-${index}" placeholder="Değer" value="${
                option.value || ""
              }">
                              <input type="text" class="form-control" name="option-label-${index}" placeholder="Etiket" value="${
                option.label || ""
              }">
                              <button type="button" class="btn btn-outline-danger remove-option" data-index="${index}">
                                  <i class="fas fa-times"></i>
                              </button>
                          `;
  
              optionsContainer.appendChild(optionRow);
  
              // Değer değişikliklerini dinle
              const inputs = optionRow.querySelectorAll("input");
              inputs.forEach((input) => {
                input.addEventListener("change", function () {
                  updateOptionValue(index, input);
                });
  
                input.addEventListener("keyup", function () {
                  updateOptionValue(index, input);
                });
              });
            });
          }
  
          // Var olan seçenek silme butonlarını etkinleştir
          const removeOptionBtns =
            optionsContainer.querySelectorAll(".remove-option");
          removeOptionBtns.forEach((btn) => {
            btn.addEventListener("click", function () {
              const index = parseInt(this.dataset.index);
              properties.options.splice(index, 1);
              updateElementContent();
              updatePropertiesPanel();
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
            properties.options.push({
              value: "option" + (properties.options.length + 1),
              label: "Seçenek " + (properties.options.length + 1),
            });
            updateElementContent();
            updatePropertiesPanel();
          });
        }
      }
  
      // Row için özel yönetim
      if (type === "row") {
        // Sütun sayısını değiştirme
        const columnCountSelect = propertiesPanel.querySelector(
          '[name="column-count"]'
        );
        if (columnCountSelect) {
          columnCountSelect.addEventListener("change", function () {
            updateRowColumns(parseInt(this.value));
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
              updateColumnWidth(index, parseInt(this.value));
            });
          });
        }
      }
    }
  
    // Seçenek değerini güncelle
    function updateOptionValue(index, input) {
      if (
        !selectedElement ||
        (selectedElement.dataset.type !== "select" &&
          selectedElement.dataset.type !== "radio")
      )
        return;
  
      const properties = selectedElement.properties;
  
      if (!properties || !Array.isArray(properties.options)) {
        return;
      }
  
      const option = properties.options[index];
      if (!option) return;
  
      if (input.name.startsWith("option-value-")) {
        option.value = input.value;
      } else if (input.name.startsWith("option-label-")) {
        option.label = input.value;
      }
  
      updateElementContent();
    }
  
    // Element özelliklerini güncelle
    function updateElementProperty(input) {
      if (!selectedElement) return;
  
      const name = input.name;
      const value = input.type === "checkbox" ? input.checked : input.value;
  
      // Özel özellik güncellemeleri
      if (name === "label") {
        selectedElement.querySelector(".element-title").textContent = value;
        selectedElement.properties.label = value;
  
        // Form içindeki etiketi güncelle
        const labelElement = selectedElement.querySelector(".form-label");
        if (labelElement) {
          labelElement.textContent = value;
        }
      } else if (name === "width") {
        selectedElement.dataset.width = value;
        selectedElement.properties.width = parseInt(value);
  
        // Genişlik değişikliğini anında göster
        const width = parseInt(value);
        selectedElement.style.width = `${(width * 100) / 12}%`;
  
        // Element sütun içinde ise sütun genişliğini güncelle
        const columnElement = selectedElement.closest(".column-element");
        if (columnElement) {
          columnElement.className = columnElement.className.replace(
            /col-md-\d+/,
            `col-md-${width}`
          );
          columnElement.dataset.width = width;
        }
      } else {
        // Genel özellik güncelleme
        selectedElement.properties[name] = value;
      }
  
      // Element içeriğini güncelle
      updateElementContent();
    }
  
    // Row sütunlarını güncelle
    function updateRowColumns(columnCount) {
      if (!selectedElement || selectedElement.dataset.type !== "row") return;
  
      // Mevcut sütunları al
      let currentColumns = selectedElement.properties.columns || [];
      const rowElement = selectedElement.querySelector(".row-element");
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
      selectedElement.properties.columns = newColumns;
  
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
      updateRowContent(columnElements);
  
      // Özellik panelini güncelle (sütun genişlikleri için)
      updatePropertiesPanel();
    }
  
    // Row içeriğini güncelle - mevcut içeriği koru
    function updateRowContent(oldColumns) {
      if (!selectedElement || selectedElement.dataset.type !== "row") return;
  
      const properties = selectedElement.properties;
      const content = selectedElement.querySelector(".element-content");
  
      // Yeni row şablonunu oluştur
      content.innerHTML = `<div class="row row-element d-flex flex-row"></div>`;
  
      const rowElement = content.querySelector(".row-element");
      if (!rowElement) return;
  
      // Yeni sütunları oluştur
      properties.columns.forEach((column, index) => {
        const columnElement = document.createElement("div");
        columnElement.className = `col-md-${column.width} column-element`;
        columnElement.dataset.width = column.width;
        columnElement.style.display = "block";
        columnElement.style.minHeight = "80px";
  
        // Eğer eski bir sütun varsa içeriğini taşı
        if (oldColumns && index < oldColumns.length) {
          const formerColumn = oldColumns[index];
          const formerElements = formerColumn
            ? formerColumn.querySelectorAll(".form-element")
            : [];
  
          if (formerElements.length > 0) {
            // Mevcut elemanları kopyala
            formerElements.forEach((elem) => {
              columnElement.appendChild(elem.cloneNode(true));
            });
          } else {
            // Placeholder göster
            columnElement.innerHTML = `
                          <div class="column-placeholder">
                              <i class="fas fa-plus me-2"></i> Buraya element sürükleyin
                          </div>
                      `;
          }
        } else {
          // Yeni sütun, placeholder göster
          columnElement.innerHTML = `
                      <div class="column-placeholder">
                          <i class="fas fa-plus me-2"></i> Buraya element sürükleyin
                      </div>
                  `;
        }
  
        rowElement.appendChild(columnElement);
      });
  
      // Event listener'ları yeniden ekle
      const newElements = rowElement.querySelectorAll(".form-element");
      newElements.forEach((elem) => {
        elem.addEventListener("click", function (e) {
          e.stopPropagation();
          selectElement(elem);
        });
  
        // Butonlara event listener ekle
        const buttons = elem.querySelectorAll("[data-action]");
        buttons.forEach((button) => {
          button.addEventListener("click", function (e) {
            e.stopPropagation();
            const action = this.dataset.action;
  
            if (action === "remove") {
              elem.remove();
              if (selectedElement === elem) {
                clearSelectedElement();
              }
            } else if (action === "duplicate") {
              const type = elem.dataset.type;
              const properties = JSON.parse(
                JSON.stringify(elem.properties || {})
              );
              const duplicate = createFormElement(type, properties);
              elem.parentNode.insertBefore(duplicate, elem.nextSibling);
            }
          });
        });
      });
  
      // Sütunları sürüklenebilir yap
      initializeColumnSortables();
    }
  
    // Sütun genişliğini güncelle
    function updateColumnWidth(columnIndex, width) {
      if (!selectedElement || selectedElement.dataset.type !== "row") return;
  
      if (
        !selectedElement.properties ||
        !Array.isArray(selectedElement.properties.columns)
      ) {
        return;
      }
  
      selectedElement.properties.columns[columnIndex].width = width;
  
      // Sütun genişlikleri toplamını kontrol et
      const totalWidth = selectedElement.properties.columns.reduce(
        (sum, col) => sum + (parseInt(col.width) || 0),
        0
      );
  
      // Toplam genişlik 12'yi aşıyorsa uyarı ver
      if (totalWidth > 12) {
        alert(
          "Toplam sütun genişliği 12 birimden fazla olamaz. Diğer sütunların genişliklerini düşürün."
        );
        // Önceki değeri geri yükle
        selectedElement.properties.columns[columnIndex].width =
          12 - (totalWidth - width);
      }
  
      // Row içeriğini güncelle
      const columnElements = selectedElement.querySelectorAll(".column-element");
      updateRowContent(columnElements);
    }
  
    // Element içeriğini güncelle
    function updateElementContent() {
      if (!selectedElement) return;
  
      const type = selectedElement.dataset.type;
      const properties = selectedElement.properties;
  
      if (!properties) {
        console.error("Element özellikleri tanımlanmamış:", selectedElement);
        return;
      }
  
      // Özel durum: Row
      if (type === "row") {
        const columnElements =
          selectedElement.querySelectorAll(".column-element");
        updateRowContent(columnElements);
      } else {
        // Normal elementler için içeriği güncelle
        const content = selectedElement.querySelector(".element-content");
        content.innerHTML = renderTemplate(elementTemplates[type], properties);
  
        // Select ve radio için seçenekleri ekle
        if (type === "select" && properties.options) {
          const selectElement = content.querySelector("select");
          if (selectElement) {
            properties.options.forEach((option) => {
              const optionElement = document.createElement("option");
              optionElement.value = option.value || "";
              optionElement.textContent = option.label || "";
              selectElement.appendChild(optionElement);
            });
          }
        } else if (type === "radio" && properties.options) {
          const radioContainer = content.querySelector(".radio-options");
          if (radioContainer) {
            properties.options.forEach((option) => {
              const radioElement = document.createElement("div");
              radioElement.className = "form-check";
              radioElement.innerHTML = `
                              <input class="form-check-input" type="radio" name="${
                                properties.name || ""
                              }">
                              <span class="form-check-label">${
                                option.label || ""
                              }</span>
                          `;
              radioContainer.appendChild(radioElement);
            });
          }
        }
      }
    }
  
    // Boş canvas kontrolü
    function checkEmptyCanvas() {
      if (formCanvas.querySelectorAll(".form-element").length === 0) {
        emptyCanvas.style.display = "flex";
      } else {
        emptyCanvas.style.display = "none";
      }
    }
  
    // SortableJS Initialize
    function initializeSortable() {
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
      new Sortable(formCanvas, {
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
              const properties = defaultProperties[type]
                ? JSON.parse(JSON.stringify(defaultProperties[type]))
                : {};
              const newElement = createFormElement(type, properties);
  
              if (newElement) {
                // Placeholder öğeyi değiştir
                evt.item.parentNode.replaceChild(newElement, evt.item);
  
                // Boş canvas uyarısını her zaman gizle
                emptyCanvas.style.display = "none";
  
                // Yeni elementi seç
                selectElement(newElement);
  
                // Row elementi ise sürüklenebilir sütunlar oluştur
                if (type === "row") {
                  initializeColumnSortables();
                }
  
                // Durum kaydetme
                saveState();
              }
            } else {
              // Geçersiz element, kaldırılır
              evt.item.remove();
            }
          }
  
          // Canvas boş mu kontrol et
          checkEmptyCanvas();
        },
        onChange: function () {
          // Boş canvas kontrolü
          checkEmptyCanvas();
  
          // Durum kaydetme
          saveState();
        },
        onRemove: function () {
          // Boş canvas kontrolü
          checkEmptyCanvas();
  
          // Durum kaydetme
          saveState();
        },
      });
  
      // İlk çağrı
      initializeColumnSortables();
    }
  
    // Sütunlar için SortableJS Initialize
    function initializeColumnSortables() {
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
                const properties = defaultProperties[type]
                  ? JSON.parse(JSON.stringify(defaultProperties[type]))
                  : {};
                const newElement = createFormElement(type, properties);
  
                if (newElement) {
                  // Placeholder öğeyi değiştir
                  evt.item.parentNode.replaceChild(newElement, evt.item);
  
                  // Yeni elementi seç
                  selectElement(newElement);
  
                  // Durum kaydetme
                  saveState();
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
            saveState();
          },
          onChange: function () {
            // Durum kaydetme
            saveState();
          },
        });
      });
    }
  
    // Formu JSON olarak al
    function getFormJSON() {
      const formElements = [];
      const elements = formCanvas.querySelectorAll(":scope > .form-element"); // Sadece ilk seviye elementler
  
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
        title: "Form Builder",
        elements: formElements,
      };
    }
  
    // Formu kaydet
    document.getElementById("save-btn").addEventListener("click", function () {
      const formData = getFormJSON();
      console.log("Form Verisi:", formData);
      console.log("JSON:", JSON.stringify(formData, null, 2));
  
      // Basit bildirim göster
      const toast = document.createElement("div");
      toast.className =
        "toast position-fixed bottom-0 end-0 m-3 bg-success text-white show";
      toast.setAttribute("role", "alert");
      toast.innerHTML = `
              <div class="toast-header bg-success text-white">
                  <i class="fas fa-check-circle me-2"></i>
                  <strong class="me-auto">Başarılı</strong>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
              </div>
              <div class="toast-body">
                  Form başarıyla kaydedildi.
              </div>
          `;
      document.body.appendChild(toast);
  
      // 3 saniye sonra toast'ı otomatik kaldır
      setTimeout(() => {
        toast.remove();
      }, 3000);
    });
  
    // Formu önizle
    document.getElementById("preview-btn").addEventListener("click", function () {
      const formData = getFormJSON();
  
      // Yeni pencerede göstermek için HTML oluştur
      let previewHtml = `
          <!DOCTYPE html>
          <html lang="tr">
          <head>
              <meta charset="UTF-8">
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <title>Form Önizleme</title>
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
                      <h2>Form Önizleme</h2>
                      <p class="text-muted">Bu bir form önizlemesidir.</p>
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
              previewHtml += renderTemplate(
                elementTemplates[item.type],
                properties
              );
            });
  
            previewHtml += "</div>";
          });
  
          previewHtml += "</div>";
        } else {
          const properties = element.properties;
          previewHtml += renderTemplate(
            elementTemplates[element.type],
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
  
    // Canvas boş alan tıklama
    formCanvas.addEventListener("click", function (e) {
      if (
        e.target === formCanvas ||
        e.target === emptyCanvas ||
        e.target.closest(".empty-canvas")
      ) {
        clearSelectedElement();
      }
    });
  
    // Geri ve ileri butonları
    let undoStack = [];
    let redoStack = [];
  
    function saveState() {
      // Formun mevcut içeriğini state olarak kaydet
      const state = formCanvas.innerHTML;
      undoStack.push(state);
      redoStack = []; // Yeni bir durum kaydedildiğinde redo stack'i temizle
  
      // Butonların durumunu güncelle
      const undoBtn = document.getElementById("cmd-undo");
      const redoBtn = document.getElementById("cmd-redo");
  
      if (undoBtn) undoBtn.disabled = undoStack.length <= 1;
      if (redoBtn) redoBtn.disabled = redoStack.length === 0;
    }
  
    const undoBtn = document.getElementById("cmd-undo");
    if (undoBtn) {
      undoBtn.addEventListener("click", function () {
        if (undoStack.length > 1) {
          // Son durumu redoStack'e ekle
          redoStack.push(undoStack.pop());
  
          // Önceki durumu yükle
          formCanvas.innerHTML = undoStack[undoStack.length - 1];
  
          // Butonların durumunu güncelle
          this.disabled = undoStack.length <= 1;
          document.getElementById("cmd-redo").disabled = false;
  
          // SortableJS'yi yeniden başlat ve diğer dinleyicileri ekle
          initializeSortable();
          checkEmptyCanvas();
        }
      });
    }
  
    const redoBtn = document.getElementById("cmd-redo");
    if (redoBtn) {
      redoBtn.addEventListener("click", function () {
        if (redoStack.length > 0) {
          // Son redo durumunu al
          const state = redoStack.pop();
  
          // Mevcut durumu undoStack'e ekle
          undoStack.push(state);
  
          // Durumu yükle
          formCanvas.innerHTML = state;
  
          // Butonların durumunu güncelle
          this.disabled = redoStack.length === 0;
          document.getElementById("cmd-undo").disabled = false;
  
          // SortableJS'yi yeniden başlat ve diğer dinleyicileri ekle
          initializeSortable();
          checkEmptyCanvas();
        }
      });
    }
  
    // Panel ve sekme durumlarını localStorage'dan yükle
    function loadSavedStates() {
      // Panel durumları
      const leftPanelCollapsed =
        localStorage.getItem("form_builder_left_collapsed") === "true";
      const rightPanelCollapsed =
        localStorage.getItem("form_builder_right_collapsed") === "true";
  
      const leftPanel = document.querySelector(".panel__left");
      const rightPanel = document.querySelector(".panel__right");
  
      if (leftPanelCollapsed && leftPanel) {
        leftPanel.classList.add("collapsed");
      }
  
      if (rightPanelCollapsed && rightPanel) {
        rightPanel.classList.add("collapsed");
      }
  
      // Sekme durumları
      const leftTab = localStorage.getItem("form_builder_left_tab");
      const rightTab = localStorage.getItem("form_builder_right_tab");
  
      if (leftTab) {
        const tabEl = document.querySelector(
          `.panel__left .panel-tab[data-tab="${leftTab}"]`
        );
        if (tabEl) {
          tabEl.click();
        }
      }
  
      if (rightTab) {
        const tabEl = document.querySelector(
          `.panel__right .panel-tab[data-tab="${rightTab}"]`
        );
        if (tabEl) {
          tabEl.click();
        }
      }
  
      // Kategori durumları
      const categories = JSON.parse(
        localStorage.getItem("form_builder_categories") || "{}"
      );
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
    }
  
    // İlk durumu kaydet
    saveState();
  
    // SortableJS'yi başlat
    initializeSortable();
  
    // Kaydedilmiş durumları yükle
    loadSavedStates();
  });
  