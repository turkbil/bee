// Form Builder Şablonları
document.addEventListener("DOMContentLoaded", function() {
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
                      <input type="text" class="form-control" name="default_value" value="{default_value}" placeholder="#ffffff">
                      <small class="text-muted">Hex formatında girin: #RRGGBB</small>
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
  });