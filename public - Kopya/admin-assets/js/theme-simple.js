// Tema Ayarları JS - Orjinal Yapıya Dayalı Basit Sistem
document.addEventListener("DOMContentLoaded", function () {
    // Tema modu değiştirme (açık/koyu/sistem)
    const themeRadios = document.querySelectorAll('input[name="theme"]');
    themeRadios.forEach((radio) => {
        radio.addEventListener("change", function () {
            const themeMode = this.value; // light, dark veya auto
            document.cookie = `dark=${
                themeMode === "dark" ? "1" : themeMode === "auto" ? "auto" : "0"
            };path=/;max-age=31536000`;

            // Sayfa yenilemeden temayı değiştir
            applyThemeChanges(themeMode);
        });
    });

    // Karanlık mod switch için tema geçiş fonksiyonu
    initThemeSwitch();

    // Ana renk değiştirme
    const colorRadios = document.querySelectorAll(
        'input[name="theme-primary"]'
    );
    colorRadios.forEach((radio) => {
        radio.addEventListener("change", function () {
            let color = this.value;

            // Tabler renk isimlerini hex kodlara çevir
            const tablerColors = {
                blue: "#066fd1",
                azure: "#4299e1",
                indigo: "#6366f1",
                purple: "#8b5cf6",
                pink: "#ec4899",
                red: "#ef4444",
                orange: "#f97316",
                yellow: "#fbbf24",
                lime: "#84cc16",
                green: "#10b981",
                teal: "#06b6d4",
                cyan: "#0891b2",
            };

            // Eğer renk isimlendirme şeklindeyse hex'e çevir
            if (tablerColors[color]) {
                color = tablerColors[color];
            }

            document.cookie = `siteColor=${color};path=/;max-age=31536000`;

            // Sayfa yenilemeden rengi değiştir
            document.documentElement.style.setProperty(
                "--primary-color",
                color
            );
            
            // RGB değerini de güncelle
            const rgbValue = hexToRgb(color);
            if (rgbValue) {
                document.documentElement.style.setProperty(
                    "--primary-color-rgb",
                    `${rgbValue.r}, ${rgbValue.g}, ${rgbValue.b}`
                );
            }
            
            updateTextColor(color);

            // Radius örneklerini güncelle (aktif renk değişimi için)
            updateRadiusExamples();
        });
    });

    // Köşe yuvarlaklığı değiştirme - Range slider
    const radiusSlider = document.getElementById("radius-slider");
    const radiusValue = document.getElementById("radius-value");
    const radiusExamples = document.querySelectorAll(".radius-example");

    if (radiusSlider && radiusValue) {
        const radiusMap = ["0", "0.25rem", "0.375rem", "0.5rem", "0.75rem", "1rem"];

        radiusSlider.addEventListener("input", function () {
            const selectedIndex = parseInt(this.value);
            const selectedRadius = radiusMap[selectedIndex];
            radiusValue.value = selectedRadius;

            // Cookie güncelleme ve CSS değişkeni ayarlama
            document.cookie = `themeRadius=${selectedRadius};path=/;max-age=31536000`;
            document.documentElement.style.setProperty(
                "--tblr-border-radius",
                selectedRadius
            );

            // Tüm UI elementlerinin border-radius değerlerini güncelle
            updateAllElementRadiuses(selectedRadius);

            // Örnekleri güncelle
            radiusExamples.forEach((example, index) => {
                if (index === selectedIndex) {
                    example.classList.add("active");
                } else {
                    example.classList.remove("active");
                }
            });
        });

        // Köşe yuvarlaklığı örneklerine tıklama olayı ekleme
        radiusExamples.forEach((example) => {
            example.addEventListener("click", function () {
                const selectedIndex = this.getAttribute("data-radius");
                radiusSlider.value = selectedIndex;

                // Slider değişimi olayını tetikle
                const event = new Event("input");
                radiusSlider.dispatchEvent(event);
            });
        });
    }

    // Font Boyutu değiştirme
    const fontSizeRadios = document.querySelectorAll(
        'input[name="theme-font-size"]'
    );
    fontSizeRadios.forEach((radio) => {
        radio.addEventListener("change", function () {
            const fontSize = this.value;
            document.cookie = `themeFontSize=${fontSize};path=/;max-age=31536000`;

            // Sayfa yenilemeden font boyutunu değiştir
            document.body.classList.remove(
                "font-size-small",
                "font-size-normal",
                "font-size-large"
            );
            document.body.classList.add(`font-size-${fontSize}`);

            // Tüm font boyutlarını güncelle
            updateFontSizes(fontSize);
        });
    });

    // Yazı tipi değiştirme
    const fontRadios = document.querySelectorAll('input[name="theme-font"]');
    fontRadios.forEach((radio) => {
        radio.addEventListener("change", function () {
            const font = this.value;
            document.cookie = `themeFont=${encodeURIComponent(
                font
            )};path=/;max-age=31536000`;

            // Sayfa yenilemeden yazı tipini değiştir
            document.documentElement.style.setProperty(
                "--tblr-font-family",
                font
            );
            document.body.style.fontFamily = font;

            // Roboto ve Poppins için Google Fonts yükleme
            if (font.includes("Roboto")) {
                ensureGoogleFont(
                    "roboto-font",
                    "https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap"
                );
            } else if (font.includes("Poppins")) {
                ensureGoogleFont(
                    "poppins-font",
                    "https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap"
                );
            }
        });
    });

    // Gri ton değiştirme
    const baseRadios = document.querySelectorAll('input[name="theme-base"]');
    baseRadios.forEach((radio) => {
        radio.addEventListener("change", function () {
            const baseTheme = this.value;
            document.cookie = `themeBase=${baseTheme};path=/;max-age=31536000`;

            // Gri tonu güncelle
            document.body.classList.remove(
                "theme-base-slate",
                "theme-base-cool",
                "theme-base-neutral",
                "theme-base-warm",
                "theme-base-indigo",
                "theme-base-azure",
                "theme-base-primary",
                "theme-base-secondary",
                "theme-base-tertiary",
                "theme-base-error",
                "theme-base-neutral-variant",
                "theme-base-mavi-gri",
                "theme-base-cinko-gri",
                "theme-base-tas-rengi"
            );

            // Sınıfları doğru şekilde ekle
            document.body.classList.add(`theme-base-${baseTheme}`);
        });
    });

    // Tablo kompakt görünüm değiştirme
    const tableCompactRadios = document.querySelectorAll(
        'input[name="table-compact"]'
    );
    tableCompactRadios.forEach((radio) => {
        radio.addEventListener("change", function () {
            const isCompact = this.value === "1";
            document.cookie = `tableCompact=${
                isCompact ? "1" : "0"
            };path=/;max-age=31536000`;

            // Sayfa yenilemeden tablo görünümünü değiştir
            if (isCompact) {
                document.body.classList.add("table-compact");
            } else {
                document.body.classList.remove("table-compact");
            }
        });
    });

    // Tema sıfırlama butonu
    const resetButton = document.getElementById("reset-changes");
    if (resetButton) {
        resetButton.addEventListener("click", function () {
            if (
                confirm(
                    "Tüm tema ayarları varsayılana döndürülecek. Emin misiniz?"
                )
            ) {
                // Varsayılan değerleri ayarla
                document.cookie = "dark=auto;path=/;max-age=31536000";
                document.cookie = "siteColor=#066fd1;path=/;max-age=31536000";
                document.cookie = "themeBase=neutral;path=/;max-age=31536000";
                document.cookie =
                    "themeFont=Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif;path=/;max-age=31536000";
                document.cookie = "themeRadius=0.25rem;path=/;max-age=31536000";
                document.cookie = "tableCompact=0;path=/;max-age=31536000";
                document.cookie = "themeFontSize=small;path=/;max-age=31536000";

                // Sayfayı yenile
                window.location.reload();
            }
        });
    }

    // Navbar'daki tema geçiş düğmesi - Sırayla Açık -> Karanlık -> Sistem modları arasında geçiş yapar
    function initThemeSwitch() {
        // Tema düğmesini bul
        const themeSwitch = document.getElementById("switch");

        if (!themeSwitch) return;

        // Cookie'den mevcut temayı al
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(";").shift();
            return null;
        }

        // Mevcut tema ayarını al
        let currentTheme = getCookie("dark");

        // Eğer tema ayarı yoksa, varsayılan olarak sistem teması kullan
        if (
            !currentTheme ||
            (currentTheme !== "0" &&
                currentTheme !== "1" &&
                currentTheme !== "auto")
        ) {
            currentTheme = "auto"; // Sistem teması
        }

        // Tema durumunu güncelle
        function updateThemeState() {
            const themeContainer = document.querySelector(".theme-mode");
            if (!themeContainer) return;

            // Önce mevcut tema durumunu temizle
            themeContainer.removeAttribute("data-theme");

            // Mevcut temaya göre data-theme özniteliğini ayarla
            if (currentTheme === "auto") {
                // Sistem teması için
                themeContainer.setAttribute("data-theme", "auto");

                // Sistem teması için, sistem ayarına göre switch'i ayarla
                const prefersDarkMode = window.matchMedia(
                    "(prefers-color-scheme: dark)"
                ).matches;
                themeSwitch.checked = prefersDarkMode;
            } else if (currentTheme === "1") {
                // Karanlık mod
                themeContainer.setAttribute("data-theme", "dark");
                themeSwitch.checked = true;
            } else {
                // Açık mod
                themeContainer.setAttribute("data-theme", "light");
                themeSwitch.checked = false;
            }
        }

        // Sistem temasını kontrol et
        function checkSystemTheme() {
            if (currentTheme === "auto") {
                applyThemeChanges("auto");
                updateThemeState();
            }
        }

        // Başlangıçta tema durumunu ayarla ve sistem temasını kontrol et
        updateThemeState();
        checkSystemTheme();

        // Tema düğmesine tıklama olayı ekle
        themeSwitch.addEventListener("change", function () {
            // Sırayla geçiş: Açık -> Karanlık -> Sistem -> Açık ...
            if (currentTheme === "0") {
                // Açık moddan karanlık moda geç
                currentTheme = "1";
            } else if (currentTheme === "1") {
                // Karanlık moddan sistem moduna geç
                currentTheme = "auto";
            } else {
                // Sistem modundan açık moda geç
                currentTheme = "0";
            }

            // Cookie'yi ayarla
            document.cookie = `dark=${currentTheme};path=/;max-age=31536000`;

            // Tema sınıflarını güncelle
            if (currentTheme === "1") {
                applyThemeChanges("dark");
            } else if (currentTheme === "0") {
                applyThemeChanges("light");
            } else if (currentTheme === "auto") {
                applyThemeChanges("auto");
            }

            // Tema durumunu güncelle
            updateThemeState();
        });

        // Sistem teması değiştiğinde otomatik güncelleme
        window
            .matchMedia("(prefers-color-scheme: dark)")
            .addEventListener("change", function (e) {
                if (currentTheme === "auto") {
                    applyThemeChanges("auto");
                    updateThemeState();
                }
            });
    }

    // Yardımcı Fonksiyonlar

    // Merkezi tema uygulama fonksiyonu
    function applyThemeChanges(themeMode) {
        const body = document.body;
        const html = document.documentElement;

        // Önce tüm tema sınıflarını temizle
        body.classList.remove("light", "dark");
        html.classList.remove("light", "dark");

        if (themeMode === "dark") {
            body.setAttribute("data-bs-theme", "dark");
            html.setAttribute("data-bs-theme", "dark");
            body.classList.add("dark");
            html.classList.add("dark");

            // Navbar tema düğmesini güncelle
            const themeContainer = document.querySelector(".theme-mode");
            if (themeContainer) {
                themeContainer.setAttribute("data-theme", "dark");
            }
            const themeSwitch = document.getElementById("switch");
            if (themeSwitch) {
                themeSwitch.checked = true;
            }
        } else if (themeMode === "light") {
            body.setAttribute("data-bs-theme", "light");
            html.setAttribute("data-bs-theme", "light");
            body.classList.add("light");
            html.classList.add("light");

            const themeContainer = document.querySelector(".theme-mode");
            if (themeContainer) {
                themeContainer.setAttribute("data-theme", "light");
            }
            const themeSwitch = document.getElementById("switch");
            if (themeSwitch) {
                themeSwitch.checked = false;
            }
        } else if (themeMode === "auto") {
            // Sistem ayarını kontrol et
            const prefersDarkMode = window.matchMedia(
                "(prefers-color-scheme: dark)"
            ).matches;
            const actualTheme = prefersDarkMode ? "dark" : "light";

            body.setAttribute("data-bs-theme", actualTheme);
            html.setAttribute("data-bs-theme", actualTheme);
            body.classList.add(actualTheme);
            html.classList.add(actualTheme);

            const themeContainer = document.querySelector(".theme-mode");
            if (themeContainer) {
                themeContainer.setAttribute("data-theme", "auto");
            }
            const themeSwitch = document.getElementById("switch");
            if (themeSwitch) {
                themeSwitch.checked = prefersDarkMode;
            }
        }

        // Tema değişikliği sonrası ayarları yeniden uygula
        const currentColor = getCookie("siteColor") || "#066fd1";
        document.documentElement.style.setProperty(
            "--primary-color",
            currentColor
        );
        
        // RGB değerini de güncelle
        const rgbValue = hexToRgb(currentColor);
        if (rgbValue) {
            document.documentElement.style.setProperty(
                "--primary-color-rgb",
                `${rgbValue.r}, ${rgbValue.g}, ${rgbValue.b}`
            );
        }
        
        updateTextColor(currentColor);
        updateRadiusExamples();

        // CSS değişkenlerini zorla güncelle
        setTimeout(() => {
            const baseTheme = getCookie("themeBase") || "neutral";
            body.classList.remove(
                "theme-base-slate",
                "theme-base-cool",
                "theme-base-neutral",
                "theme-base-warm",
                "theme-base-indigo",
                "theme-base-azure",
                "theme-base-primary",
                "theme-base-secondary",
                "theme-base-tertiary",
                "theme-base-error",
                "theme-base-neutral-variant",
                "theme-base-mavi-gri",
                "theme-base-cinko-gri",
                "theme-base-tas-rengi"
            );
            body.classList.add(`theme-base-${baseTheme}`);

            const fontSize = getCookie("themeFontSize") || "small";
            body.classList.remove(
                "font-size-small",
                "font-size-normal",
                "font-size-large"
            );
            body.classList.add(`font-size-${fontSize}`);
            updateFontSizes(fontSize);

            const themeRadius = getCookie("themeRadius") || "0.25rem";
            updateCardBodyRadiuses(themeRadius);
        }, 50);
    }

    // Metin rengini hesaplama
    function updateTextColor(backgroundColor) {
        // Önce renk isimlerini hex'e çevir
        const tablerColors = {
            blue: "#066fd1",
            azure: "#4299e1",
            indigo: "#6366f1",
            purple: "#8b5cf6",
            pink: "#ec4899",
            red: "#ef4444",
            orange: "#f97316",
            yellow: "#fbbf24",
            lime: "#84cc16",
            green: "#10b981",
            teal: "#06b6d4",
            cyan: "#0891b2",
        };

        // Eğer renk isimlendirme şeklindeyse hex'e çevir
        if (tablerColors[backgroundColor]) {
            backgroundColor = tablerColors[backgroundColor];
        }

        // Rengi parçalara ayır
        let r, g, b;

        // HEX renk formatını kontrol et
        if (backgroundColor.startsWith("#")) {
            const hex = backgroundColor.substring(1);
            r = parseInt(hex.substring(0, 2), 16);
            g = parseInt(hex.substring(2, 4), 16);
            b = parseInt(hex.substring(4, 6), 16);
        }
        // RGB formatını kontrol et
        else if (backgroundColor.startsWith("rgb")) {
            const rgbMatch = backgroundColor.match(
                /rgba?\((\d+),\s*(\d+),\s*(\d+)/i
            );
            if (rgbMatch) {
                r = parseInt(rgbMatch[1]);
                g = parseInt(rgbMatch[2]);
                b = parseInt(rgbMatch[3]);
            }
        }

        // Eğer renk ayrıştırılamadıysa varsayılan değerler
        if (isNaN(r) || isNaN(g) || isNaN(b)) {
            console.error("Renk ayrıştırılamadı:", backgroundColor);
            return;
        }

        // Luminance (parlaklık) hesapla - W3C formülü
        const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

        // Eski sistemdeki gibi 0.5 eşiği - daha doğru çalışır
        const textColor = luminance > 0.6 ? "#000000" : "#ffffff";

        document.documentElement.style.setProperty(
            "--primary-text-color",
            textColor
        );
        document.cookie = `siteTextColor=${textColor};path=/;max-age=31536000`;
    }

    // Radius örneklerini güncelle
    function updateRadiusExamples() {
        const examples = document.querySelectorAll(".radius-example");
        examples.forEach((example) => {
            if (example.classList.contains("active")) {
                example.style.backgroundColor = getComputedStyle(
                    document.documentElement
                ).getPropertyValue("--primary-color");
            }
        });
    }

    // Card body elementlerinin radius değerlerini güncelle
    function updateCardBodyRadiuses(radius) {
        const cardBodies = document.querySelectorAll(".card-body");
        cardBodies.forEach((cardBody) => {
            cardBody.style.borderRadius = radius;
        });

        // Buton radius değerlerini de güncelle
        const buttons = document.querySelectorAll(
            ".btn:not(.btn-pill):not(.btn-square)"
        );
        buttons.forEach((button) => {
            button.style.borderRadius = radius;
        });
    }

    // Google Fonts yükleme
    function ensureGoogleFont(id, href) {
        if (!document.getElementById(id)) {
            const link = document.createElement("link");
            link.id = id;
            link.rel = "stylesheet";
            link.href = href;
            document.head.appendChild(link);
        }
    }

    // Cookie okuma
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(";").shift();
    }

    // Hex rengi RGB'ye çevirme
    function hexToRgb(hex) {
        // # işaretini kaldır
        hex = hex.replace('#', '');
        
        // Eğer 3 karakterli hex ise, 6 karaktere genişlet
        if (hex.length === 3) {
            hex = hex.split('').map(char => char + char).join('');
        }
        
        if (hex.length !== 6) {
            return null;
        }
        
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);
        
        return { r, g, b };
    }

    // Font boyutlarını güncelleme fonksiyonu
    function updateFontSizes(sizeCategory) {
        // Varsayılan boyutlar (normal boyut için)
        const defaultSizes = {
            "font-size": "0.875rem",
            "body-font-size": "0.875rem",
            "dropdown-font-size": "0.875rem",
            "code-font-size": "0.875rem",
            "h1-font-size": "1.5rem",
            "h2-font-size": "1.25rem",
            "h3-font-size": "1.125rem",
            "h4-font-size": "1rem",
            "h5-font-size": "0.875rem",
            "h6-font-size": "0.75rem",
            "small-font-size": "0.75rem",
            "btn-font-size": "0.875rem",
            "btn-sm-font-size": "0.75rem",
            "btn-lg-font-size": "1rem",
            "input-font-size": "0.875rem",
            "input-sm-font-size": "0.75rem",
            "input-lg-font-size": "1rem",
            "table-font-size": "0.875rem",
            "table-sm-font-size": "0.75rem",
            "table-lg-font-size": "1rem",
            "blockquote-font-size": "1rem",
            "nav-link-font-size": "0.875rem",
        };

        // Boyut çarpanları
        let factor = 1;
        if (sizeCategory === "small") factor = 0.857;
        if (sizeCategory === "large") factor = 1.143;

        // Tüm CSS değişkenlerini güncelle
        for (const [key, value] of Object.entries(defaultSizes)) {
            // Rem değerini alıp sayısal değere dönüştür
            const remValue = parseFloat(value);
            // Yeni boyutu hesapla ve rem olarak ayarla
            const newSize = (remValue * factor).toFixed(3) + "rem";
            // CSS değişkenini güncelle
            document.documentElement.style.setProperty(
                `--tblr-${key}`,
                newSize
            );
        }
    }

    // Başlangıç durumunu ayarla
    function initializeThemeSettings() {
        // Mevcut tema rengi için metin rengini güncelle
        const currentColor = getCookie("siteColor") || "#066fd1";
        document.documentElement.style.setProperty(
            "--primary-color",
            currentColor
        );
        
        // RGB değerini de güncelle
        const rgbValue = hexToRgb(currentColor);
        if (rgbValue) {
            document.documentElement.style.setProperty(
                "--primary-color-rgb",
                `${rgbValue.r}, ${rgbValue.g}, ${rgbValue.b}`
            );
        }
        
        updateTextColor(currentColor);

        // Tablo görünümünü ayarla
        const tableCompact = getCookie("tableCompact") || "0";
        if (tableCompact === "1") {
            document.body.classList.add("table-compact");
        } else {
            document.body.classList.remove("table-compact");
        }

        // Gri tonu ayarla
        const baseTheme = getCookie("themeBase") || "neutral";
        document.body.classList.remove(
            "theme-base-slate",
            "theme-base-cool",
            "theme-base-neutral",
            "theme-base-warm",
            "theme-base-indigo",
            "theme-base-azure",
            "theme-base-primary",
            "theme-base-secondary",
            "theme-base-tertiary",
            "theme-base-error",
            "theme-base-neutral-variant",
            "theme-base-mavi-gri",
            "theme-base-cinko-gri",
            "theme-base-tas-rengi"
        );
        document.body.classList.add(`theme-base-${baseTheme}`);

        // Font boyutunu ayarla
        const fontSize = getCookie("themeFontSize") || "small";
        document.body.classList.remove(
            "font-size-small",
            "font-size-normal",
            "font-size-large"
        );
        document.body.classList.add(`font-size-${fontSize}`);

        // Tüm font boyutlarını güncelle
        updateFontSizes(fontSize);

        // Border radius değerlerini ayarla
        const themeRadius = getCookie("themeRadius") || "0.375rem";
        document.documentElement.style.setProperty("--tblr-border-radius", themeRadius);
        updateAllElementRadiuses(themeRadius);

        // Gerekli Google fontlarını yükle
        const currentFont = getCookie("themeFont");
        if (currentFont) {
            if (currentFont.includes("Roboto")) {
                ensureGoogleFont(
                    "roboto-font",
                    "https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap"
                );
            } else if (currentFont.includes("Poppins")) {
                ensureGoogleFont(
                    "poppins-font",
                    "https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap"
                );
            }
        }

        // Radius örneklerini başlangıçta ayarla
        updateRadiusExamples();

        // Sistem teması değişikliğini dinle
        window
            .matchMedia("(prefers-color-scheme: dark)")
            .addEventListener("change", (e) => {
                if (getCookie("dark") === "auto") {
                    applyThemeChanges("auto");
                }
            });
    }

    // Tema ayarlarını başlat
    initializeThemeSettings();

    // Koyu tema değiştiğinde radius örneklerini güncelle
    document.addEventListener("darkModeChange", updateRadiusExamples);

    // Basit border-radius güncelleme fonksiyonu
    function updateAllElementRadiuses(radiusValue) {
        // Ana CSS değişkenini güncelle
        document.documentElement.style.setProperty("--tblr-border-radius", radiusValue);
        
        // Basit element listesi
        const simpleElements = [
            '.btn', '.card', '.badge', '.form-control', '.form-select', 
            '.dropdown-menu', '.dropdown-item', '.alert', '.avatar', '.nav-link'
        ];
        
        // Her element için radius uygula
        simpleElements.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(element => {
                element.style.borderRadius = radiusValue;
            });
        });
        
        // Group elementleri için özel mantık - başlangıç/bitiş yuvarlak, orta düz
        
        // Button Group
        const buttonGroups = document.querySelectorAll('.btn-group');
        buttonGroups.forEach(group => {
            const buttons = group.querySelectorAll('.btn');
            buttons.forEach((button, index) => {
                button.style.borderRadius = '0';
                
                if (index === 0) {
                    // İlk buton - sol köşeler yuvarlak
                    button.style.borderTopLeftRadius = radiusValue;
                    button.style.borderBottomLeftRadius = radiusValue;
                }
                if (index === buttons.length - 1) {
                    // Son buton - sağ köşeler yuvarlak
                    button.style.borderTopRightRadius = radiusValue;
                    button.style.borderBottomRightRadius = radiusValue;
                }
            });
        });
        
        // Input Group
        const inputGroups = document.querySelectorAll('.input-group');
        inputGroups.forEach(group => {
            const elements = group.querySelectorAll('.form-control, .input-group-text, .btn');
            elements.forEach((element, index) => {
                element.style.borderRadius = '0';
                
                if (index === 0) {
                    element.style.borderTopLeftRadius = radiusValue;
                    element.style.borderBottomLeftRadius = radiusValue;
                }
                if (index === elements.length - 1) {
                    element.style.borderTopRightRadius = radiusValue;
                    element.style.borderBottomRightRadius = radiusValue;
                }
            });
        });
        
        // Pagination
        const paginationGroups = document.querySelectorAll('.pagination');
        paginationGroups.forEach(pagination => {
            const pageItems = pagination.querySelectorAll('.page-item .page-link');
            pageItems.forEach((link, index) => {
                link.style.borderRadius = '0';
                
                if (index === 0) {
                    link.style.borderTopLeftRadius = radiusValue;
                    link.style.borderBottomLeftRadius = radiusValue;
                }
                if (index === pageItems.length - 1) {
                    link.style.borderTopRightRadius = radiusValue;
                    link.style.borderBottomRightRadius = radiusValue;
                }
            });
        });
    }

    // Sayfa tamamen yüklendikten sonra bir kez daha tema düğmesini kontrol et
    window.addEventListener("load", function () {
        initThemeSwitch();
    });
});
