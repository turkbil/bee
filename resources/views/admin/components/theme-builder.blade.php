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
                <div class="theme-section-body">
                    <div class="appearance-options">
                        <label class="appearance-option" for="theme-light">
                            <input type="radio" id="theme-light" name="theme" value="light" {{ !isset($_COOKIE['dark']) || $_COOKIE['dark'] != '1' ? 'checked' : '' }}>
                            <div class="appearance-preview light-preview"></div>
                            <span>Açık</span>
                        </label>
                        <label class="appearance-option" for="theme-dark">
                            <input type="radio" id="theme-dark" name="theme" value="dark" {{ isset($_COOKIE['dark']) && $_COOKIE['dark'] == '1' ? 'checked' : '' }}>
                            <div class="appearance-preview dark-preview"></div>
                            <span>Koyu</span>
                        </label>
                        <label class="appearance-option" for="theme-auto">
                            <input type="radio" id="theme-auto" name="theme" value="auto" {{ isset($_COOKIE['dark']) && $_COOKIE['dark'] == 'auto' ? 'checked' : '' }}>
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
                <div class="theme-section-body">
                    <div class="color-options">
                        <div class="color-grid">
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#066fd1" {{ (!isset($_COOKIE['siteColor']) || $_COOKIE['siteColor'] == '#066fd1') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #066fd1"></span>
                            </label>
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#F03E3E" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#F03E3E') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #F03E3E"></span>
                            </label>
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#E64980" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#E64980') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #E64980"></span>
                            </label>
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#BE4BDB" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#BE4BDB') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #BE4BDB"></span>
                            </label>
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#7950F2" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#7950F2') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #7950F2"></span>
                            </label>
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#4C6EF5" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#4C6EF5') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #4C6EF5"></span>
                            </label>
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#228BE6" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#228BE6') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #228BE6"></span>
                            </label>
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#15AABF" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#15AABF') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #15AABF"></span>
                            </label>
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#12B886" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#12B886') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #12B886"></span>
                            </label>
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#40C057" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#40C057') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #40C057"></span>
                            </label>
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#82C91E" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#82C91E') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #82C91E"></span>
                            </label>
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#FAB005" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FAB005') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #FAB005"></span>
                            </label>
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#FD7E14" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FD7E14') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #FD7E14"></span>
                            </label>
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#FF922B" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FF922B') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #FF922B"></span>
                            </label>
                            <label class="color-option">
                                <input name="theme-primary" type="radio" value="#FCC419" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FCC419') ? 'checked' : '' }}>
                                <span class="color-swatch" style="background-color: #FCC419"></span>
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
                <div class="theme-section-body">
                    <div class="font-options">
                        <label class="font-option" for="font-inter">
                            <input type="radio" id="font-inter" name="theme-font" value="Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif" {{ (!isset($_COOKIE['themeFont']) || $_COOKIE['themeFont'] == "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif") ? 'checked' : '' }}>
                            <span style="font-family: Inter, system-ui;">Inter</span>
                        </label>
                        <label class="font-option" for="font-roboto">
                            <input type="radio" id="font-roboto" name="theme-font" value="'Roboto', sans-serif" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Roboto', sans-serif") ? 'checked' : '' }}>
                            <span style="font-family: 'Roboto', sans-serif;">Roboto</span>
                        </label>
                        <label class="font-option" for="font-poppins">
                            <input type="radio" id="font-poppins" name="theme-font" value="'Poppins', sans-serif" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Poppins', sans-serif") ? 'checked' : '' }}>
                            <span style="font-family: 'Poppins', sans-serif;">Poppins</span>
                        </label>
                        <label class="font-option" for="font-georgia">
                            <input type="radio" id="font-georgia" name="theme-font" value="Georgia, 'Times New Roman', Times, serif" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "Georgia, 'Times New Roman', Times, serif") ? 'checked' : '' }}>
                            <span style="font-family: Georgia, 'Times New Roman', Times, serif;">Georgia</span>
                        </label>
                        <label class="font-option" for="font-courier">
                            <input type="radio" id="font-courier" name="theme-font" value="'Courier New', Courier, monospace" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Courier New', Courier, monospace") ? 'checked' : '' }}>
                            <span style="font-family: 'Courier New', Courier, monospace;">Courier</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Köşe Yuvarlaklığı -->
            <div class="theme-section">
                <div class="theme-section-header">
                    <i class="fa-solid fa-square-corners"></i>
                    <span>Köşe Yuvarlaklığı</span>
                </div>
                <div class="theme-section-body">
                    <div class="radius-slider-container">
                        <input type="range" class="radius-slider" id="radius-slider" min="0" max="4" step="1" value="{{ $radiusValue = isset($_COOKIE['themeRadius']) ? (($_COOKIE['themeRadius'] == '0') ? 0 : (($_COOKIE['themeRadius'] == '0.25rem') ? 1 : (($_COOKIE['themeRadius'] == '0.5rem') ? 2 : (($_COOKIE['themeRadius'] == '0.75rem') ? 3 : 4)))) : 2 }}">
                        <div class="radius-preview">
                            <div class="radius-example radius-0 {{ $radiusValue == 0 ? 'active' : '' }}"></div>
                            <div class="radius-example radius-1 {{ $radiusValue == 1 ? 'active' : '' }}"></div>
                            <div class="radius-example radius-2 {{ $radiusValue == 2 ? 'active' : '' }}"></div>
                            <div class="radius-example radius-3 {{ $radiusValue == 3 ? 'active' : '' }}"></div>
                            <div class="radius-example radius-4 {{ $radiusValue == 4 ? 'active' : '' }}"></div>
                        </div>
                        <input type="hidden" id="radius-value" name="theme-radius" value="{{ isset($_COOKIE['themeRadius']) ? $_COOKIE['themeRadius'] : '0.5rem' }}">
                    </div>
                </div>
            </div>

            <!-- Gri Tonlar -->
            <div class="theme-section">
                <div class="theme-section-header">
                    <i class="fa-solid fa-swatchbook"></i>
                    <span>Gri Tonları</span>
                </div>
                <div class="theme-section-body">
                    <div class="gray-options">
                        <label class="gray-option" for="gray-slate">
                            <input type="radio" id="gray-slate" name="theme-base" value="slate" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'slate') ? 'checked' : '' }}>
                            <span class="gray-name">Slate</span>
                            <span class="gray-preview" style="background: linear-gradient(to right, #0f172a, #f8fafc);"></span>
                        </label>
                        <label class="gray-option" for="gray-gray">
                            <input type="radio" id="gray-gray" name="theme-base" value="gray" {{ (!isset($_COOKIE['themeBase']) || $_COOKIE['themeBase'] == 'gray') ? 'checked' : '' }}>
                            <span class="gray-name">Gray</span>
                            <span class="gray-preview" style="background: linear-gradient(to right, #111827, #f9fafb);"></span>
                        </label>
                        <label class="gray-option" for="gray-zinc">
                            <input type="radio" id="gray-zinc" name="theme-base" value="zinc" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'zinc') ? 'checked' : '' }}>
                            <span class="gray-name">Zinc</span>
                            <span class="gray-preview" style="background: linear-gradient(to right, #18181b, #fafafa);"></span>
                        </label>
                        <label class="gray-option" for="gray-neutral">
                            <input type="radio" id="gray-neutral" name="theme-base" value="neutral" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'neutral') ? 'checked' : '' }}>
                            <span class="gray-name">Neutral</span>
                            <span class="gray-preview" style="background: linear-gradient(to right, #171717, #fafafa);"></span>
                        </label>
                        <label class="gray-option" for="gray-stone">
                            <input type="radio" id="gray-stone" name="theme-base" value="stone" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'stone') ? 'checked' : '' }}>
                            <span class="gray-name">Stone</span>
                            <span class="gray-preview" style="background: linear-gradient(to right, #1c1917, #fafaf9);"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Tablo Görünümü -->
            <div class="theme-section">
                <div class="theme-section-header">
                    <i class="fa-solid fa-table-list"></i>
                    <span>Tablo Görünümü</span>
                </div>
                <div class="theme-section-body">
                    <div class="table-options">
                        <label class="table-option" for="table-compact">
                            <input type="radio" id="table-compact" name="table-compact" value="1" {{ (!isset($_COOKIE['tableCompact']) || $_COOKIE['tableCompact'] == '1') ? 'checked' : '' }}>
                            <div class="table-preview compact-preview"></div>
                            <span>Kompakt</span>
                        </label>
                        <label class="table-option" for="table-normal">
                            <input type="radio" id="table-normal" name="table-compact" value="0" {{ (isset($_COOKIE['tableCompact']) && $_COOKIE['tableCompact'] == '0') ? 'checked' : '' }}>
                            <div class="table-preview normal-preview"></div>
                            <span>Normal</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="theme-actions">
            <button type="button" class="btn btn-reset" id="reset-changes">
                <i class="fa-solid fa-rotate-left me-2"></i>Varsayılana Dön
            </button>
        </div>
    </div>
</div>