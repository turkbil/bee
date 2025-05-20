// Form Builder Şablonları
document.addEventListener("DOMContentLoaded", function() {
    // Element şablonları
    window.elementTemplates = {
      text: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <input type="text" class="form-control" placeholder="{placeholder}" value="{default_value}">
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      textarea: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <textarea class="form-control" rows="4" placeholder="{placeholder}">{default_value}</textarea>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      number: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <input type="number" class="form-control" placeholder="{placeholder}" value="{default_value}">
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      email: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <input type="email" class="form-control" placeholder="{placeholder}" value="{default_value}">
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      select: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <select class="form-select">
                      <option value="" disabled>{placeholder}</option>
                      <!-- Seçenekler JavaScript tarafından eklenecek -->
                  </select>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      checkbox: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-check">
                      <input class="form-check-input" type="checkbox" {default_value}>
                      <span class="form-check-label">{label}</span>
                  </label>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      radio: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <div class="radio-options">
                      <!-- Seçenekler JavaScript tarafından eklenecek -->
                  </div>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      switch: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" {default_value}>
                      <span class="form-check-label">{label}</span>
                  </label>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      color: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <input type="color" class="form-control form-control-color" value="{default_value}" title="Renk seçin">
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      date: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <div class="input-icon">
                      <span class="input-icon-addon">
                          <i class="fas fa-calendar"></i>
                      </span>
                      <input type="date" class="form-control" value="{default_value}">
                  </div>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      time: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <div class="input-icon">
                      <span class="input-icon-addon">
                          <i class="fas fa-clock"></i>
                      </span>
                      <input type="time" class="form-control" value="{default_value}">
                  </div>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      file: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <div class="row align-items-center g-3">
                      <div class="col-12 col-md-9">
                          <div class="card">
                              <div class="card-body">
                                  <div class="dropzone">
                                      <div class="d-flex flex-column align-items-center justify-content-center p-4">
                                          <i class="fa-solid fa-cloud-arrow-up fa-2x mb-2 text-muted"></i>
                                          <div class="text-muted">Dosyayı sürükleyip bırakın veya tıklayın</div>
                                      </div>
                                      <input type="file" class="d-none" />
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-3">
                          <div class="card">
                              <div class="card-body p-3">
                                  <div class="d-flex align-items-center justify-content-center text-muted" style="height: 100px;">
                                      <i class="fa-solid fa-file-circle-xmark fa-2x"></i>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      image: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <div class="row align-items-center g-3">
                      <div class="col-12 col-md-9">
                          <div class="card">
                              <div class="card-body">
                                  <div class="dropzone">
                                      <div class="d-flex flex-column align-items-center justify-content-center p-4">
                                          <i class="fa-solid fa-cloud-arrow-up fa-2x mb-2 text-muted"></i>
                                          <div class="text-muted">Görseli sürükleyip bırakın veya tıklayın</div>
                                      </div>
                                      <input type="file" class="d-none" accept="image/*" />
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-3">
                          <div class="card">
                              <div class="card-body p-3">
                                  <div class="d-flex align-items-center justify-content-center text-muted" style="height: 156px;">
                                      <i class="fa-solid fa-image-slash fa-2x"></i>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      image_multiple: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <div class="card">
                      <div class="card-body p-3">
                          <div class="dropzone p-4">
                              <div class="text-center">
                                  <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                  <h4 class="text-muted">Görselleri sürükleyip bırakın veya tıklayın</h4>
                                  <p class="text-muted small">PNG, JPG, WEBP, GIF - Maks 2MB - <strong>Toplu seçim yapabilirsiniz</strong></p>
                              </div>
                              <input type="file" class="d-none" accept="image/*" multiple />
                          </div>
                      </div>
                  </div>
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      row: `
              <div class="row row-element d-flex g-3">
                  <div class="col-12 col-md-6 column-element" data-width="6">
                      <div class="column-placeholder">
                          <i class="fas fa-plus me-2"></i> Buraya element sürükleyin
                      </div>
                  </div>
                  <div class="col-12 col-md-6 column-element" data-width="6">
                      <div class="column-placeholder">
                          <i class="fas fa-plus me-2"></i> Buraya element sürükleyin
                      </div>
                  </div>
              </div>
          `,
      heading: `
              <div class="mb-3 col-12 col-md-{width}">
                  <{size} class="text-{align}">{content}</{size}>
              </div>
          `,
      paragraph: `
              <div class="mb-3 col-12 col-md-{width}">
                  <p class="text-{align}">{content}</p>
              </div>
          `,
      divider: `
              <div class="mb-3 col-12 col-md-{width}">
                  <div class="dropdown-divider my-3" style="border-top: {thickness} {style} {color}; height: 0; opacity: 1;"></div>
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
              <div class="mb-3 col-12 col-md-{width}">
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
      password: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <input type="password" class="form-control" placeholder="{placeholder}" value="{default_value}">
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      tel: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <input type="tel" class="form-control" placeholder="{placeholder}" value="{default_value}">
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      url: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <input type="url" class="form-control" placeholder="{placeholder}" value="{default_value}">
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      range: `
              <div class="mb-3 col-12 col-md-{width}">
                  <label class="form-label">{label}</label>
                  <input type="range" class="form-range" min="{min}" max="{max}" step="{step}" value="{default_value}">
                  <div class="form-text text-muted">{help_text}</div>
              </div>
          `,
      button: `
              <div class="mb-3 col-12 col-md-{width}">
                  <button type="button" class="btn btn-{button_style}">{label}</button>
              </div>
          `,
    };
  
    // Özellik paneli şablonları
    window.propertyTemplates = {
      text: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-font me-2"></i>Metin Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Placeholder</label>
                                  <input type="text" class="form-control" name="placeholder" value="{placeholder}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Değer</label>
                                  <input type="text" class="form-control" name="default_value" value="{default_value}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      textarea: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-align-left me-2"></i>Uzun Metin Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Placeholder</label>
                                  <input type="text" class="form-control" name="placeholder" value="{placeholder}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Değer</label>
                                  <textarea class="form-control" name="default_value" rows="3">{default_value}</textarea>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      select: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-caret-square-down me-2"></i>Açılır Liste Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Placeholder</label>
                                  <input type="text" class="form-control" name="placeholder" value="{placeholder}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Seçenekler</label>
                                  <div id="options-container">
                                      <!-- Seçenekler JavaScript ile doldurulacak -->
                                  </div>
                                  <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-option">
                                      <i class="fas fa-plus me-1"></i> Seçenek Ekle
                                  </button>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Değer</label>
                                  <select class="form-select" name="default_value">
                                      <option value="">Varsayılan değer seçin</option>
                                      <!-- Seçenekler JavaScript ile doldurulacak -->
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      number: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-hashtag me-2"></i>Sayı Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Placeholder</label>
                                  <input type="text" class="form-control" name="placeholder" value="{placeholder}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Değer</label>
                                  <input type="number" class="form-control" name="default_value" value="{default_value}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      email: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-at me-2"></i>E-posta Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Placeholder</label>
                                  <input type="text" class="form-control" name="placeholder" value="{placeholder}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Değer</label>
                                  <input type="email" class="form-control" name="default_value" value="{default_value}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      checkbox: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-check-square me-2"></i>Onay Kutusu Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Durum</label>
                                  <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="default_value" id="default_value_true" value="true" {default_value}>
                                      <label class="form-check-label" for="default_value_true">İşaretli</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="default_value" id="default_value_false" value="false" {!default_value}>
                                      <label class="form-check-label" for="default_value_false">İşaretsiz</label>
                                  </div>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      radio: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-circle me-2"></i>Seçim Düğmesi Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Seçenekler</label>
                                  <div id="options-container">
                                      <!-- Seçenekler JavaScript ile doldurulacak -->
                                  </div>
                                  <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-option">
                                      <i class="fas fa-plus me-1"></i> Seçenek Ekle
                                  </button>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Değer</label>
                                  <select class="form-select" name="default_value">
                                      <option value="">Varsayılan değer seçin</option>
                                      <!-- Seçenekler JavaScript ile doldurulacak -->
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      switch: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-toggle-on me-2"></i>Anahtar Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Durum</label>
                                  <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="default_value" id="default_value_true" value="true" {default_value}>
                                      <label class="form-check-label" for="default_value_true">Açık</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="default_value" id="default_value_false" value="false" {!default_value}>
                                      <label class="form-check-label" for="default_value_false">Kapalı</label>
                                  </div>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      color: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-palette me-2"></i>Renk Seçici Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Değer</label>
                                  <input type="color" class="form-control form-control-color" name="default_value" value="{default_value}" title="Renk seçin">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      date: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-calendar me-2"></i>Tarih Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Değer</label>
                                  <input type="date" class="form-control" name="default_value" value="{default_value}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      time: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-clock me-2"></i>Saat Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Değer</label>
                                  <input type="time" class="form-control" name="default_value" value="{default_value}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      file: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-file me-2"></i>Dosya Yükleme Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      image: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-image me-2"></i>Resim Yükleme Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      image_multiple: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-images me-2"></i>Çoklu Resim Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      row: `
            <div class="property-panel">
                <div class="property-header">
                    <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-grip-lines-vertical me-2"></i>Satır Elementini Düzenle</h4>
                </div>
                
                <div class="p-0">
                    <!-- Satır Ayarları -->
                    <div class="property-section">
                        <div class="section-title">Satır Ayarları</div>
                        <div class="section-content p-3">
                            <div class="mb-3 col-12 col-md-{width}">
                                <label class="form-label">Sütun Sayısı</label>
                                <select class="form-select" name="column-count">
                                    <option value="2" {columns2}>2 Sütun</option>
                                    <option value="3" {columns3}>3 Sütun</option>
                                    <option value="4" {columns4}>4 Sütun</option>
                                </select>
                            </div>
                            <div class="mb-3 col-12 col-md-{width}">
                                <label class="form-label">Sütun Genişlikleri</label>
                                <div id="column-widths-container">
                                    <!-- Sütun genişlikleri JavaScript ile doldurulacak -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          `,
      heading: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-heading me-2"></i>Başlık Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- İçerik -->
                      <div class="property-section">
                          <div class="section-title">İçerik</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Başlık Metni</label>
                                  <input type="text" class="form-control" name="content" value="{content}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
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
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Hizalama</label>
                                  <select class="form-select" name="align">
                                      <option value="left" {alignleft}>Sola</option>
                                      <option value="center" {aligncenter}>Ortaya</option>
                                      <option value="right" {alignright}>Sağa</option>
                                  </select>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      paragraph: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-paragraph me-2"></i>Paragraf Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- İçerik -->
                      <div class="property-section">
                          <div class="section-title">İçerik</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Paragraf Metni</label>
                                  <textarea class="form-control" name="content" rows="4">{content}</textarea>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Hizalama</label>
                                  <select class="form-select" name="align">
                                      <option value="left" {alignleft}>Sola</option>
                                      <option value="center" {aligncenter}>Ortaya</option>
                                      <option value="right" {alignright}>Sağa</option>
                                      <option value="justify" {alignjustify}>İki Yana</option>
                                  </select>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      divider: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-minus me-2"></i>Ayırıcı Çizgi Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Stil</label>
                                  <select class="form-select" name="style">
                                      <option value="solid" {stylesolid}>Düz Çizgi</option>
                                      <option value="dashed" {styledashed}>Kesik Çizgi</option>
                                      <option value="dotted" {styledotted}>Noktalı</option>
                                      <option value="double" {styledouble}>Çift Çizgi</option>
                                  </select>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Kalınlık</label>
                                  <select class="form-select" name="thickness">
                                      <option value="1px" {thickness1px}>İnce (1px)</option>
                                      <option value="2px" {thickness2px}>Orta (2px)</option>
                                      <option value="3px" {thickness3px}>Kalın (3px)</option>
                                      <option value="5px" {thickness5px}>Çok Kalın (5px)</option>
                                  </select>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Renk</label>
                                  <input type="color" class="form-control form-control-color" name="color" value="{color}" title="Renk seçin">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      spacer: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-arrows-alt-v me-2"></i>Boşluk Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yükseklik</label>
                                  <select class="form-select" name="height">
                                      <option value="0.5rem" {height0_5}>Çok Küçük (0.5rem)</option>
                                      <option value="1rem" {height1}>Küçük (1rem)</option>
                                      <option value="2rem" {height2}>Orta (2rem)</option>
                                      <option value="3rem" {height3}>Büyük (3rem)</option>
                                      <option value="4rem" {height4}>Çok Büyük (4rem)</option>
                                  </select>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
<option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      card: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-credit-card me-2"></i>Kart Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- İçerik -->
                      <div class="property-section">
                          <div class="section-title">İçerik</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Başlık</label>
                                  <input type="text" class="form-control" name="title" value="{title}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">İçerik</label>
                                  <textarea class="form-control" name="content" rows="4">{content}</textarea>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="has_header" {has_header}>
                                      <span class="form-check-label">Başlık Göster</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="has_footer" {has_footer}>
                                      <span class="form-check-label">Alt Bilgi Göster</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      tab_group: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-folder me-2"></i>Sekme Grubu Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Sekmeler -->
                      <div class="property-section">
                          <div class="section-title">Sekmeler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                              <div id="tabs-container">
                                      <!-- Sekmeler JavaScript ile doldurulacak -->
                                  </div>
                                  <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-tab">
                                      <i class="fas fa-plus me-1"></i> Sekme Ekle
                                  </button>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      password: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-key me-2"></i>Şifre Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Placeholder</label>
                                  <input type="text" class="form-control" name="placeholder" value="{placeholder}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Değer</label>
                                  <input type="password" class="form-control" name="default_value" value="{default_value}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      tel: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-phone me-2"></i>Telefon Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Placeholder</label>
                                  <input type="text" class="form-control" name="placeholder" value="{placeholder}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Değer</label>
                                  <input type="tel" class="form-control" name="default_value" value="{default_value}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      url: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-globe me-2"></i>URL Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Placeholder</label>
                                  <input type="text" class="form-control" name="placeholder" value="{placeholder}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Değer</label>
                                  <input type="url" class="form-control" name="default_value" value="{default_value}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      range: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-sliders-h me-2"></i>Aralık Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Alan Adı (System Key)</label>
                                  <input type="text" class="form-control" name="name" value="{name}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Veri Özellikleri -->
                      <div class="property-section">
                          <div class="section-title">Veri Özellikleri</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Minimum Değer</label>
                                  <input type="number" class="form-control" name="min" value="{min}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Maksimum Değer</label>
                                  <input type="number" class="form-control" name="max" value="{max}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Adım</label>
                                  <input type="number" class="form-control" name="step" value="{step}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Yardım Metni</label>
                                  <input type="text" class="form-control" name="help_text" value="{help_text}">
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Varsayılan Değer</label>
                                  <input type="range" class="form-range" name="default_value" min="{min}" max="{max}" step="{step}" value="{default_value}">
                                  <div class="mt-2 text-center">
                                      <span class="badge bg-secondary">{default_value}</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                      <!-- Ayarlar -->
                      <div class="property-section">
                          <div class="section-title">Ayarlar</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="required" {required}>
                                      <span class="form-check-label">Zorunlu Alan</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_active" {is_active}>
                                      <span class="form-check-label">Aktif</span>
                                  </label>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-check form-switch">
                                      <input class="form-check-input" type="checkbox" name="is_system" {is_system}>
                                      <span class="form-check-label">Sistem Ayarı</span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
      button: `
              <div class="property-panel">
                  <div class="property-header">
                      <h4 class="fw-bold p-3 border-bottom"><i class="fas fa-square me-2"></i>Buton Elementini Düzenle</h4>
                  </div>
                  
                  <div class="p-0">
                      <!-- Temel Bilgiler -->
                      <div class="property-section">
                          <div class="section-title">Temel Bilgiler</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Etiket</label>
                                  <input type="text" class="form-control" name="label" value="{label}">
                              </div>
                          </div>
                      </div>
                      
                      <!-- Görünüm Ayarları -->
                      <div class="property-section">
                          <div class="section-title">Görünüm Ayarları</div>
                          <div class="section-content p-3">
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Stil</label>
                                  <select class="form-select" name="button_style">
                                      <option value="primary" {button_styleprimary}>Primary</option>
                                      <option value="secondary" {button_stylesecondary}>Secondary</option>
                                      <option value="success" {button_stylesuccess}>Success</option>
                                      <option value="danger" {button_styledanger}>Danger</option>
                                      <option value="warning" {button_stylewarning}>Warning</option>
                                      <option value="info" {button_styleinfo}>Info</option>
                                      <option value="light" {button_stylelight}>Light</option>
                                      <option value="dark" {button_styledark}>Dark</option>
                                  </select>
                              </div>
                              <div class="mb-3 col-12 col-md-{width}">
                                  <label class="form-label">Genişlik</label>
                                  <select class="form-select" name="width">
                                      <option value="12" {width12}>Tam Genişlik (12/12)</option>
                                      <option value="6" {width6}>Yarım Genişlik (6/12)</option>
                                      <option value="4" {width4}>Üçte Bir (4/12)</option>
                                      <option value="3" {width3}>Çeyrek (3/12)</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `,
    };
  });