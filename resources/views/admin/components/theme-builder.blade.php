<div class="offcanvas offcanvas-start theme-builder" tabindex="-1" id="offcanvasTheme" aria-labelledby="offcanvasThemeLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasThemeLabel">
            <i class="fa-solid fa-palette me-2"></i>Tema Ayarları
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="theme-sections">
            <!-- Görünüm Modu -->
            <div class="theme-section">
                <div class="theme-section-header">
                    <i class="fa-solid fa-circle-half-stroke"></i>
                    <span>Görünüm Modu</span>
                </div>
                <div class="theme-section-body pt-4">
                    <div class="appearance-options mb-2">
                        <label class="appearance-option" for="theme-light">
                            <input type="radio" id="theme-light" name="theme" value="light" {{ isset($_COOKIE['dark']) && $_COOKIE['dark'] == '0' ? 'checked' : '' }}>
                            <div class="appearance-preview light-preview"></div>
                            <span>Açık</span>
                        </label>
                        <label class="appearance-option" for="theme-dark">
                            <input type="radio" id="theme-dark" name="theme" value="dark" {{ isset($_COOKIE['dark']) && $_COOKIE['dark'] == '1' ? 'checked' : '' }}>
                            <div class="appearance-preview dark-preview"></div>
                            <span>Koyu</span>
                        </label>
                        <label class="appearance-option" for="theme-auto">
                            <input type="radio" id="theme-auto" name="theme" value="auto" {{ !isset($_COOKIE['dark']) || $_COOKIE['dark'] == 'auto' ? 'checked' : '' }}>
                            <div class="appearance-preview auto-preview"></div>
                            <span>Sistem</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Ana Renk -->
            <div class="theme-section">
                <div class="theme-section-header">
                    <i class="fa-solid fa-droplet"></i>
                    <span>Ana Renk</span>
                </div>
                <div class="theme-section-body pt-4">
                    <div class="row g-2 mb-2">
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#066fd1" class="form-colorinput-input" {{ (!isset($_COOKIE['siteColor']) || $_COOKIE['siteColor'] == '#066fd1') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #066fd1"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#1E40AF" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#1E40AF') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #1E40AF"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#5F3DC4" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#5F3DC4') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #5F3DC4"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#F03E3E" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#F03E3E') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #F03E3E"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#AE3EC9" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#AE3EC9') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #AE3EC9"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#7209B7" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#7209B7') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #7209B7"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#BE4BDB" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#BE4BDB') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #BE4BDB"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#D6336C" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#D6336C') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #D6336C"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#7950F2" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#7950F2') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #7950F2"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#4338CA" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#4338CA') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #4338CA"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#0EA5E9" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#0EA5E9') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #0EA5E9"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#06B6D4" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#06B6D4') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #06B6D4"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#0891B2" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#0891B2') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #0891B2"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#059669" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#059669') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #059669"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#16A34A" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#16A34A') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #16A34A"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#65A30D" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#65A30D') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #65A30D"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#CA8A04" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#CA8A04') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #CA8A04"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#D97706" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#D97706') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #D97706"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#EA580C" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#EA580C') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #EA580C"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#DC2626" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#DC2626') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #DC2626"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#475569" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#475569') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #475569"></span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="form-colorinput">
                                <input name="theme-primary" type="radio" value="#0F172A" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#0F172A') ? 'checked' : '' }}>
                                <span class="form-colorinput-color" style="background-color: #0F172A"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yazı Tipi -->
            <div class="theme-section">
                <div class="theme-section-header">
                    <i class="fa-solid fa-font"></i>
                    <span>Yazı Tipi</span>
                </div>
                <div class="theme-section-body py-4">
                    <div class="font-menu">
                        <label class="dropdown-item font-option">
                            <input class="form-check-input m-0 me-2" type="radio" id="font-inter" name="theme-font" value="Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif" {{ (!isset($_COOKIE['themeFont']) || $_COOKIE['themeFont'] == "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif") ? 'checked' : '' }}>
                            <span style="font-family: Inter, system-ui;">Inter System-ui</span>
                        </label>
                        <label class="dropdown-item font-option">
                            <input class="form-check-input m-0 me-2" type="radio" id="font-roboto" name="theme-font" value="'Roboto', sans-serif" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Roboto', sans-serif") ? 'checked' : '' }}>
                            <span style="font-family: 'Roboto', sans-serif;">Roboto Sans-serif</span>
                        </label>
                        <label class="dropdown-item font-option">
                            <input class="form-check-input m-0 me-2" type="radio" id="font-poppins" name="theme-font" value="'Poppins', sans-serif" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Poppins', sans-serif") ? 'checked' : '' }}>
                            <span style="font-family: 'Poppins', sans-serif;">Poppins Sans-serif</span>
                        </label>
                        <label class="dropdown-item font-option">
                            <input class="form-check-input m-0 me-2" type="radio" id="font-georgia" name="theme-font" value="Georgia, 'Times New Roman', Times, serif" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "Georgia, 'Times New Roman', Times, serif") ? 'checked' : '' }}>
                            <span style="font-family: Georgia, 'Times New Roman', Times, serif;">Georgia Serif</span>
                        </label>
                        <label class="dropdown-item font-option">
                            <input class="form-check-input m-0 me-2" type="radio" id="font-courier" name="theme-font" value="'Courier New', Courier, monospace" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Courier New', Courier, monospace") ? 'checked' : '' }}>
                            <span style="font-family: 'Courier New', Courier, monospace;">Courier Monospace</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Font Boyutu -->
            <div class="theme-section">
                <div class="theme-section-header">
                    <i class="fa-solid fa-text-size"></i>
                    <span>Font Boyutu</span>
                </div>
                <div class="theme-section-body pt-4">
                    <div class="appearance-options mb-2">
                        <label class="appearance-option" for="font-size-small">
                            <input type="radio" id="font-size-small" name="theme-font-size" value="small" {{ (!isset($_COOKIE['themeFontSize']) || $_COOKIE['themeFontSize'] == 'small') ? 'checked' : '' }}>
                            <div class="appearance-preview">
                                <span style="font-size: 0.75rem;">Aa<br/>Bb<br/>Cc</span>
                            </div>
                            <span>Küçük</span>
                        </label>
                        <label class="appearance-option" for="font-size-normal">
                            <input type="radio" id="font-size-normal" name="theme-font-size" value="normal" {{ (isset($_COOKIE['themeFontSize']) && $_COOKIE['themeFontSize'] == 'normal') ? 'checked' : '' }}>
                            <div class="appearance-preview">
                                <span style="font-size: 0.875rem;">Aa<br/>Bb<br/>Cc</span>
                            </div>
                            <span>Normal</span>
                        </label>
                        <label class="appearance-option" for="font-size-large">
                            <input type="radio" id="font-size-large" name="theme-font-size" value="large" {{ (isset($_COOKIE['themeFontSize']) && $_COOKIE['themeFontSize'] == 'large') ? 'checked' : '' }}>
                            <div class="appearance-preview">
                                <span style="font-size: 1rem;">Aa<br/>Bb<br/>Cc</span>
                            </div>
                            <span>Büyük</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Köşe Yuvarlaklığı -->
            <div class="theme-section">
                <div class="theme-section-header">
                    <i class="fa-solid fa-square"></i>
                    <span>Köşe Yuvarlaklığı</span>
                </div>
                <div class="theme-section-body py-4">
                    <div class="radius-slider-container pt-2 px-3">
                        <input type="range" class="radius-slider" id="radius-slider" min="0" max="4" step="1" value="{{ $radiusValue = isset($_COOKIE['themeRadius']) ? (($_COOKIE['themeRadius'] == '0') ? 0 : (($_COOKIE['themeRadius'] == '0.25rem') ? 1 : (($_COOKIE['themeRadius'] == '0.5rem') ? 2 : (($_COOKIE['themeRadius'] == '0.75rem') ? 3 : 4)))) : 2 }}">
                        <div class="radius-preview pt-3">
                            <div class="radius-example radius-0 {{ $radiusValue == 0 ? 'active' : '' }}" data-radius="0"></div>
                            <div class="radius-example radius-1 {{ $radiusValue == 1 ? 'active' : '' }}" data-radius="1"></div>
                            <div class="radius-example radius-2 {{ $radiusValue == 2 ? 'active' : '' }}" data-radius="2"></div>
                            <div class="radius-example radius-3 {{ $radiusValue == 3 ? 'active' : '' }}" data-radius="3"></div>
                            <div class="radius-example radius-4 {{ $radiusValue == 4 ? 'active' : '' }}" data-radius="4"></div>
                        </div>
                        <input type="hidden" id="radius-value" name="theme-radius" value="{{ isset($_COOKIE['themeRadius']) ? $_COOKIE['themeRadius'] : '0.25rem' }}">
                    </div>
                </div>
            </div>

            <!-- Tablo Görünümü -->
            <div class="theme-section">
                <div class="theme-section-header">
                    <i class="fa-solid fa-table-list"></i>
                    <span>Tablo Görünümü</span>
                </div>
                <div class="theme-section-body py-4">
                    <div class="appearance-options">
                        <label class="appearance-option" for="table-compact">
                            <input type="radio" id="table-compact" name="table-compact" value="1" {{ (isset($_COOKIE['tableCompact']) && $_COOKIE['tableCompact'] == '1') ? 'checked' : '' }}>
                            <div class="appearance-preview compact-preview"></div>
                            <span>Kompakt</span>
                        </label>
                        <label class="appearance-option" for="table-normal">
                            <input type="radio" id="table-normal" name="table-compact" value="0" {{ (!isset($_COOKIE['tableCompact']) || $_COOKIE['tableCompact'] == '0') ? 'checked' : '' }}>
                            <div class="appearance-preview normal-preview"></div>
                            <span>Normal</span>
                        </label>
                        <div class="appearance-option invisible">
                            <div class="appearance-preview"></div>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gri Tonlar -->
            <div class="theme-section">
                <div class="theme-section-header">
                    <i class="fa-solid fa-swatchbook"></i>
                    <span>Renk Teması</span>
                </div>
                <div class="theme-section-body py-4">
                    <div class="gray-tones-grid">
                        <label class="tone-option" for="tone-mavi-gri">
                            <input type="radio" id="tone-mavi-gri" name="theme-base" value="slate" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'slate') ? 'checked' : '' }}>
                            <div class="tone-preview mavi-gri-preview"></div>
                            <span>Mavi Gri</span>
                        </label>
                        <label class="tone-option" for="tone-neutral">
                            <input type="radio" id="tone-neutral" name="theme-base" value="neutral" {{ (!isset($_COOKIE['themeBase']) || $_COOKIE['themeBase'] == 'neutral' || $_COOKIE['themeBase'] == 'cool') ? 'checked' : '' }}>
                            <div class="tone-preview neutral-preview"></div>
                            <span>Nötr</span>
                        </label>
                        <label class="tone-option" for="tone-tas-rengi">
                            <input type="radio" id="tone-tas-rengi" name="theme-base" value="stone" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'stone') ? 'checked' : '' }}>
                            <div class="tone-preview tas-rengi-preview"></div>
                            <span>Taş Rengi</span>
                        </label>
                        <label class="tone-option" for="tone-error">
                            <input type="radio" id="tone-error" name="theme-base" value="error" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'error') ? 'checked' : '' }}>
                            <div class="tone-preview error-preview"></div>
                            <span>Secondary</span>
                        </label>
                        <label class="tone-option" for="tone-cinko-gri">
                            <input type="radio" id="tone-cinko-gri" name="theme-base" value="zinc" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'zinc') ? 'checked' : '' }}>
                            <div class="tone-preview cinko-gri-preview"></div>
                            <span>Çinko Gri</span>
                        </label>
                        <label class="tone-option" for="tone-neutral-variant">
                            <input type="radio" id="tone-neutral-variant" name="theme-base" value="neutral-variant" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'neutral-variant') ? 'checked' : '' }}>
                            <div class="tone-preview neutral-variant-preview"></div>
                            <span>Koyu Gri</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="theme-actions">
                <button type="button" class="btn btn-reset btn-primary" id="reset-changes">
                    <i class="fa-solid fa-rotate-left me-2"></i>Varsayılana Dön
                </button>
            </div>

        </div>
    </div>
</div>