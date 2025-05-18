// Form Builder JavaScript Kodu
document.addEventListener("DOMContentLoaded", function () {
  // Global değişkenler
  window.elementCounter = 0;
  window.selectedElement = null;
  window.formCanvas = document.getElementById("form-canvas");
  window.emptyCanvas = document.getElementById("empty-canvas");
  window.elementPalette = document.getElementById("element-palette");
  window.propertiesPanel = document.getElementById("properties-panel");
  window.undoStack = [];
  window.redoStack = [];

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
  window.defaultProperties = {
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
    color: {
      label: "Renk Seçici",
      name: "color_field",
      help_text: "",
      width: 12,
      required: false,
      default_value: "#ffffff",
    },
    date: {
      label: "Tarih",
      name: "date_field",
      help_text: "",
      width: 12,
      required: false,
      default_value: "",
    },
    time: {
      label: "Saat",
      name: "time_field",
      help_text: "",
      width: 12,
      required: false,
      default_value: "",
    },
    file: {
      label: "Dosya Yükleme",
      name: "file_field",
      help_text: "",
      width: 12,
      required: false,
      default_value: "",
    },
    image: {
      label: "Resim Yükleme",
      name: "image_field",
      help_text: "",
      width: 12,
      required: false,
      default_value: "",
    },
    row: {
      columns: [
        { index: 1, width: 6 },
        { index: 2, width: 6 },
      ],
    },
    // Yeni düzen elemanları
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
      label: "Ayırıcı",
      style: "solid",
      width: 12,
      color: "#e5e7eb",
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

  // Element şablonları
  window.elementTemplates = {
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
    color: `
            <div class="mb-3">
                <label class="form-label">{label}</label>
                <input type="color" class="form-control form-control-color" value="{default_value}">
                <div class="form-text text-muted">{help_text}</div>
            </div>
        `,
    date: `
            <div class="mb-3">
                <label class="form-label">{label}</label>
                <input type="date" class="form-control">
                <div class="form-text text-muted">{help_text}</div>
            </div>
        `,
    time: `
            <div class="mb-3">
                <label class="form-label">{label}</label>
                <input type="time" class="form-control">
                <div class="form-text text-muted">{help_text}</div>
            </div>
        `,
    file: `
            <div class="mb-3">
                <label class="form-label">{label}</label>
                <input type="file" class="form-control">
                <div class="form-text text-muted">{help_text}</div>
            </div>
        `,
    image: `
            <div class="mb-3">
                <label class="form-label">{label}</label>
                <input type="file" class="form-control" accept="image/*">
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
    // Yeni düzen elemanları için şablonlar
    heading: `
            <div class="mb-3">
                <{size} class="text-{align}">{content}</{size}>
            </div>
        `,
    paragraph: `
            <div class="mb-3">
                <p class="text-{align}">{content}</p>
            </div>
        `,
    divider: `
            <div class="mb-3">
                <hr style="border-top: 1px {style} {color};">
            </div>
        `,
    spacer: `
            <div style="height: {height};" class="mb-3"></div>
        `,
    card: `
            <div class="card mb-3">
                <div class="card-header" style="display: {header_display};">
                    <h3 class="card-title">{title}</h3>
                </div>
                <div class="card-body">
                    <p>{content}</p>
                </div>
                <div class="card-footer" style="display: {footer_display};">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary">Tamam</button>
                    </div>
                </div>
            </div>
        `,
    tab_group: `
            <div class="mb-3">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <!-- Sekme başlıkları JavaScript tarafından eklenecek -->
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Sekme içerikleri JavaScript tarafından eklenecek -->
                        </div>
                    </div>
                </div>
            </div>
        `,
  };

  // Özellik paneli şablonları
  window.propertyTemplates = {
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
                <div class="mb-3">
                    <label class="form-label">Ayar ID</label>
                    <select class="form-select" name="setting_id">
                        <option value="">Ayar Seçiniz</option>
                        <!-- Ayarlar AJAX ile yüklenecek -->
                    </select>
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
                <div class="mb-3">
                    <label class="form-label">Ayar ID</label>
                    <select class="form-select" name="setting_id">
                        <option value="">Ayar Seçiniz</option>
                        <!-- Ayarlar AJAX ile yüklenecek -->
                    </select>
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
                <div class="mb-3">
                    <label class="form-label">Ayar ID</label>
                    <select class="form-select" name="setting_id">
                        <option value="">Ayar Seçiniz</option>
                        <!-- Ayarlar AJAX ile yüklenecek -->
                    </select>
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
                <div class="mb-3">
                    <label class="form-label">Ayar ID</label>
                    <select class="form-select" name="setting_id">
                        <option value="">Ayar Seçiniz</option>
                        <!-- Ayarlar AJAX ile yüklenecek -->
                    </select>
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
                <div class="mb-3">
                    <label class="form-label">Ayar ID</label>
                    <select class="form-select" name="setting_id">
                        <option value="">Ayar Seçiniz</option>
                        <!-- Ayarlar AJAX ile yüklenecek -->
                    </select>
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
                <div class="mb-3">
                    <label class="form-label">Ayar ID</label>
                    <select class="form-select" name="setting_id">
                        <option value="">Ayar Seçiniz</option>
                        <!-- Ayarlar AJAX ile yüklenecek -->
                    </select>
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
                <div class="mb-3">
                    <label class="form-label">Ayar ID</label>
                    <select class="form-select" name="setting_id">
                        <option value="">Ayar Seçiniz</option>
                        <!-- Ayarlar AJAX ile yüklenecek -->
                    </select>
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
                <div class="mb-3">
                    <label class="form-label">Ayar ID</label>
                    <select class="form-select" name="setting_id">
                        <option value="">Ayar Seçiniz</option>
                        <!-- Ayarlar AJAX ile yüklenecek -->
                    </select>
                </div>
            </div>
        `,
    color: `
            <h4 class="fw-bold p-3 border-bottom">Renk Seçici Elementini Düzenle</h4>
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
                    <label class="form-label">Varsayılan Değer</label>
                    <input type="color" class="form-control form-control-color" name="default_value" value="{default_value}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Ayar ID</label>
                    <select class="form-select" name="setting_id">
                        <option value="">Ayar Seçiniz</option>
                        <!-- Ayarlar AJAX ile yüklenecek -->
                    </select>
                </div>
            </div>
        `,
    date: `
            <h4 class="fw-bold p-3 border-bottom">Tarih Elementini Düzenle</h4>
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
                    <label class="form-label">Ayar ID</label>
                    <select class="form-select" name="setting_id">
                        <option value="">Ayar Seçiniz</option>
                        <!-- Ayarlar AJAX ile yüklenecek -->
                    </select>
                </div>
            </div>
        `,
    time: `
            <h4 class="fw-bold p-3 border-bottom">Saat Elementini Düzenle</h4>
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
                    <label class="form-label">Ayar ID</label>
                    <select class="form-select" name="setting_id">
                        <option value="">Ayar Seçiniz</option>
                        <!-- Ayarlar AJAX ile yüklenecek -->
                    </select>
                </div>
            </div>
        `,
    file: `
            <div class="mb-3">
                <label class="form-label">{label}</label>
                <input type="file" class="form-control">
                <div class="form-text text-muted">{help_text}</div>
            </div>
        `,
    image: `
            <div class="mb-3">
                <label class="form-label">{label}</label>
                <input type="file" class="form-control" accept="image/*">
                <div class="form-text text-muted">{help_text}</div>
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
    // Yeni düzen elemanları için özellik paneli şablonları
    heading: `
            <h4 class="fw-bold p-3 border-bottom">Başlık Elementini Düzenle</h4>
            <div class="p-3">
                <div class="mb-3">
                    <label class="form-label">İçerik</label>
                    <input type="text" class="form-control" name="content" value="{content}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Boyut</label>
                    <select class="form-select" name="size">
                        <option value="h1" {sizeh1}>Başlık 1 (H1)</option>
                        <option value="h2" {sizeh2}>Başlık 2 (H2)</option>
                        <option value="h3" {sizeh3}>Başlık 3 (H3)</option>
                        <option value="h4" {sizeh4}>Başlık 4 (H4)</option>
                        <option value="h5" {sizeh5}>Başlık 5 (H5)</option>
                        <option value="h6" {sizeh6}>Başlık 6 (H6)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Hizalama</label>
                    <select class="form-select" name="align">
                        <option value="left" {alignleft}>Sola</option>
                        <option value="center" {aligncenter}>Ortaya</option>
                        <option value="right" {alignright}>Sağa</option>
                    </select>
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
            </div>
        `,
    paragraph: `
            <h4 class="fw-bold p-3 border-bottom">Paragraf Elementini Düzenle</h4>
            <div class="p-3">
                <div class="mb-3">
                    <label class="form-label">İçerik</label>
                    <textarea class="form-control" name="content" rows="4">{content}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Hizalama</label>
                    <select class="form-select" name="align">
                        <option value="left" {alignleft}>Sola</option>
                        <option value="center" {aligncenter}>Ortaya</option>
                        <option value="right" {alignright}>Sağa</option>
                        <option value="justify" {alignjustify}>İki Yana</option>
                    </select>
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
            </div>
        `,
    divider: `
            <h4 class="fw-bold p-3 border-bottom">Ayırıcı Elementini Düzenle</h4>
            <div class="p-3">
                <div class="mb-3">
                    <label class="form-label">Stil</label>
                    <select class="form-select" name="style">
                        <option value="solid" {stylesolid}>Düz Çizgi</option>
                        <option value="dashed" {styledashed}>Kesik Çizgi</option>
                        <option value="dotted" {styledotted}>Noktalı</option>
                        <option value="double" {styledouble}>Çift Çizgi</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Renk</label>
                    <input type="color" class="form-control form-control-color" name="color" value="{color}">
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
            </div>
        `,
    spacer: `
            <h4 class="fw-bold p-3 border-bottom">Boşluk Elementini Düzenle</h4>
            <div class="p-3">
                <div class="mb-3">
                    <label class="form-label">Yükseklik</label>
                    <select class="form-select" name="height">
                        <option value="0.5rem" {height0_5}>Çok Küçük (0.5rem)</option>
                        <option value="1rem" {height1}>Küçük (1rem)</option>
                        <option value="2rem" {height2}>Orta (2rem)</option>
                        <option value="3rem" {height3}>Büyük (3rem)</option>
                        <option value="4rem" {height4}>Çok Büyük (4rem)</option>
                    </select>
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
            </div>
        `,
    card: `
            <h4 class="fw-bold p-3 border-bottom">Kart Elementini Düzenle</h4>
            <div class="p-3">
                <div class="mb-3">
                    <label class="form-label">Başlık</label>
                    <input type="text" class="form-control" name="title" value="{title}">
                </div>
                <div class="mb-3">
                    <label class="form-label">İçerik</label>
                    <textarea class="form-control" name="content" rows="4">{content}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="has_header" {has_header}>
                        <span class="form-check-label">Başlık Göster</span>
                    </label>
                </div>
                <div class="mb-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="has_footer" {has_footer}>
                        <span class="form-check-label">Alt Bilgi Göster</span>
                    </label>
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
            </div>
        `,
    tab_group: `
            <h4 class="fw-bold p-3 border-bottom">Sekme Grubu Elementini Düzenle</h4>
            <div class="p-3">
                <div class="mb-3">
                    <label class="form-label">Sekmeler</label>
                    <div id="tabs-container">
                        <!-- Sekmeler JavaScript ile doldurulacak -->
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-tab">
                        <i class="fas fa-plus me-1"></i> Sekme Ekle
                    </button>
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
            </div>
        `,
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

    const elementId = "element-" + ++window.elementCounter;
    const formElement = document.createElement("div");
    formElement.className = "form-element";
    formElement.dataset.id = elementId;
    formElement.dataset.type = type;

    // Element header'ı oluştur
    const header = document.createElement("div");
    header.className = "element-header";

    // Element başlığını belirle
    let elementTitle = '';
    
    if (type === 'row') {
      elementTitle = 'Satır Düzeni';
    } else if (type === 'heading') {
      elementTitle = 'Başlık: ' + (properties.content || '');
    } else if (type === 'paragraph') {
      elementTitle = 'Paragraf';
    } else if (type === 'divider') {
      elementTitle = 'Ayırıcı';
    } else if (type === 'spacer') {
      elementTitle = 'Boşluk';
    } else if (type === 'card') {
      elementTitle = 'Kart: ' + (properties.title || '');
    } else if (type === 'tab_group') {
      elementTitle = 'Sekme Grubu';
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

    // Element içeriğini oluştur
    const content = document.createElement("div");
    content.className = "element-content";

    // Element şablonunu al ve işle
    const template = window.elementTemplates[type];
    if (template) {
      content.innerHTML = window.renderTemplate(template, properties);

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
      } else if (type === "tab_group" && properties.tabs) {
        // Tab Group için sekmeler ekle
        const tabList = content.querySelector(".nav-tabs");
        const tabContent = content.querySelector(".tab-content");
        
        if (tabList && tabContent) {
          properties.tabs.forEach((tab, index) => {
            // Tab başlığı ekleme
            const tabItem = document.createElement("li");
            tabItem.className = "nav-item";
            tabItem.innerHTML = `
              <a href="#tab-${elementId}-${index}" class="nav-link ${index === 0 ? 'active' : ''}" data-bs-toggle="tab">
                ${tab.title}
              </a>
            `;
            tabList.appendChild(tabItem);
            
            // Tab içeriği ekleme
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
      window.selectElement(formElement);
    });

    // Element işlemlerini ekle (silme, kopyalama)
    const buttons = formElement.querySelectorAll("[data-action]");
    buttons.forEach((button) => {
      button.addEventListener("click", function (e) {
        e.stopPropagation();
        const action = this.dataset.action;

        if (action === "remove") {
          formElement.remove();
          if (window.selectedElement === formElement) {
            window.clearSelectedElement();
          }
          window.checkEmptyCanvas();
          window.saveState();
        } else if (action === "duplicate") {
          // Kopya oluştururken özellikleri derin kopyalama
          const elementProps = formElement.properties
            ? JSON.parse(JSON.stringify(formElement.properties))
            : JSON.parse(JSON.stringify(window.defaultProperties[type] || {}));
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
  
  // Formu JSON olarak al
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
      title: "Form Builder",
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
});