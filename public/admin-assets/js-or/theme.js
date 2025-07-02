// Tema Ayarları JS
document.addEventListener('DOMContentLoaded', function() {
    // Tema modu değiştirme (açık/koyu/sistem)
    const themeRadios = document.querySelectorAll('input[name="theme"]');
    themeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const themeMode = this.value; // light, dark veya auto
            document.cookie = `dark=${themeMode === 'dark' ? '1' : (themeMode === 'auto' ? 'auto' : '0')};path=/;max-age=31536000`;
            
            // Sayfa yenilemeden temayı değiştir
            const body = document.body;
            
            if (themeMode === 'dark') {
                body.setAttribute('data-bs-theme', 'dark');
                body.classList.remove('light');
                body.classList.add('dark');
                // Navbar tema düğmesini güncelle
                const themeContainer = document.querySelector('.theme-mode');
                if (themeContainer) {
                    themeContainer.setAttribute('data-theme', 'dark');
                }
                const themeSwitch = document.getElementById('switch');
                if (themeSwitch) {
                    themeSwitch.checked = true;
                }
            } else if (themeMode === 'light') {
                body.setAttribute('data-bs-theme', 'light');
                body.classList.remove('dark');
                body.classList.add('light');
                const themeContainer = document.querySelector('.theme-mode');
                if (themeContainer) {
                    themeContainer.setAttribute('data-theme', 'light');
                }
                const themeSwitch = document.getElementById('switch');
                if (themeSwitch) {
                    themeSwitch.checked = false;
                }
            } else if (themeMode === 'auto') {
                // Sistem ayarını kontrol et
                const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                body.setAttribute('data-bs-theme', prefersDarkMode ? 'dark' : 'light');
                body.classList.remove(prefersDarkMode ? 'light' : 'dark');
                body.classList.add(prefersDarkMode ? 'dark' : 'light');
                const themeContainer = document.querySelector('.theme-mode');
                if (themeContainer) {
                    themeContainer.setAttribute('data-theme', 'auto');
                }
                const themeSwitch = document.getElementById('switch');
                if (themeSwitch) {
                    themeSwitch.checked = prefersDarkMode;
                }
            }
            
            // Mevcut primary rengi koru - tema değişiminde tekrar uygula
            const currentColor = getCookie('siteColor') || '#066fd1';
            const primaryPalette = generatePrimaryPalette(currentColor);
            applyPrimaryPalette(primaryPalette);
            
            // CSS değişkenlerini zorla güncelle
            forceUpdateThemeVariables();
            
            // Smooth transition için kısa delay
            setTimeout(() => {
                forceUpdateAllElements();
            }, 50);
        });
    });
    
    // Karanlık mod switch için tema geçiş fonksiyonu
    initThemeSwitch();

    // Ana renk değiştirme - Tabler.io uyumlu
    function initColorPickers() {
        const colorRadios = document.querySelectorAll('input[name="theme-primary"]');
        colorRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const color = this.value;
                document.cookie = `siteColor=${color};path=/;max-age=31536000`;
                
                // Primary renk paletini hesapla ve uygula
                const primaryPalette = generatePrimaryPalette(color);
                applyPrimaryPalette(primaryPalette);
                updateTextColor(color);
                
                // Radius örneklerini güncelle
                updateRadiusExamples();
                
                // Primary renk değiştirildi
            });
        });
    }
    
    // Sayfa yüklendiğinde ve dinamik içerik yüklendiğinde çalıştır
    initColorPickers();
    
    // MutationObserver ile yeni eklenen color picker'ları yakalayalım
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        const newColorRadios = node.querySelectorAll ? node.querySelectorAll('input[name="theme-primary"]') : [];
                        if (newColorRadios.length > 0) {
                            initColorPickers();
                        }
                    }
                });
            }
        });
    });
    
    // DOM değişikliklerini izle
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Köşe yuvarlaklığı değiştirme - Range slider
    const radiusSlider = document.getElementById('radius-slider');
    const radiusValue = document.getElementById('radius-value');
    const radiusExamples = document.querySelectorAll('.radius-example');
    
    if (radiusSlider && radiusValue) {
        const radiusMap = ['0', '0.25rem', '0.5rem', '0.75rem', '1rem'];
        
        radiusSlider.addEventListener('input', function() {
            const selectedIndex = parseInt(this.value);
            const selectedRadius = radiusMap[selectedIndex];
            radiusValue.value = selectedRadius;
            
            // Cookie güncelleme ve CSS değişkeni ayarlama
            document.cookie = `themeRadius=${selectedRadius};path=/;max-age=31536000`;
            document.documentElement.style.setProperty('--tblr-border-radius', selectedRadius);
            
            // Card body elementlerinin radius değerlerini güncelle
            document.documentElement.style.setProperty('--card-border-radius', selectedRadius);
            updateCardBodyRadiuses(selectedRadius);
            
            // Örnekleri güncelle
            radiusExamples.forEach((example, index) => {
                if (index === selectedIndex) {
                    example.classList.add('active');
                } else {
                    example.classList.remove('active');
                }
            });
        });
        
        // Köşe yuvarlaklığı örneklerine tıklama olayı ekleme
        radiusExamples.forEach((example) => {
            example.addEventListener('click', function() {
                const selectedIndex = this.getAttribute('data-radius');
                radiusSlider.value = selectedIndex;
                
                // Slider değişimi olayını tetikle
                const event = new Event('input');
                radiusSlider.dispatchEvent(event);
            });
        });
    }

    // Font Boyutu değiştirme
    const fontSizeRadios = document.querySelectorAll('input[name="theme-font-size"]');
    fontSizeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const fontSize = this.value;
            document.cookie = `themeFontSize=${fontSize};path=/;max-age=31536000`;
            
            // Sayfa yenilemeden font boyutunu değiştir
            document.body.classList.remove('font-size-small', 'font-size-normal', 'font-size-large');
            document.body.classList.add(`font-size-${fontSize}`);
            
            // Tüm font boyutlarını güncelle
            updateFontSizes(fontSize);
        });
    });
    

    // Yazı tipi değiştirme
    const fontRadios = document.querySelectorAll('input[name="theme-font"]');
    fontRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const font = this.value;
            document.cookie = `themeFont=${encodeURIComponent(font)};path=/;max-age=31536000`;
            
            // Sayfa yenilemeden yazı tipini değiştir
            document.documentElement.style.setProperty('--tblr-font-family', font);
            document.body.style.fontFamily = font;
            
            // Roboto ve Poppins için Google Fonts yükleme
            if (font.includes('Roboto')) {
                ensureGoogleFont('roboto-font', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
            } else if (font.includes('Poppins')) {
                ensureGoogleFont('poppins-font', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap');
            }
        });
    });

    // Gri ton değiştirme
    const baseRadios = document.querySelectorAll('input[name="theme-base"]');
    baseRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const baseTheme = this.value;
            document.cookie = `themeBase=${baseTheme};path=/;max-age=31536000`;
            
            // Gri tonu güncelle
            document.body.classList.remove('theme-base-slate', 'theme-base-cool', 'theme-base-neutral', 'theme-base-warm', 'theme-base-indigo', 'theme-base-azure', 'theme-base-primary', 'theme-base-secondary', 'theme-base-tertiary', 'theme-base-error', 'theme-base-neutral-variant', 'theme-base-mavi-gri', 'theme-base-cinko-gri', 'theme-base-tas-rengi');
            
            // Özel isimler için sınıfları doğru şekilde ekle
            if(baseTheme === 'slate') {
                document.body.classList.add('theme-base-mavi-gri');
            } else if(baseTheme === 'zinc') {
                document.body.classList.add('theme-base-cinko-gri');
            } else if(baseTheme === 'stone') {
                document.body.classList.add('theme-base-tas-rengi');
            } else {
                document.body.classList.add(`theme-base-${baseTheme}`);
            }
        });
    });

    // Tablo kompakt görünüm değiştirme
    const tableCompactRadios = document.querySelectorAll('input[name="table-compact"]');
    tableCompactRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const isCompact = this.value === '1';
            document.cookie = `tableCompact=${isCompact ? '1' : '0'};path=/;max-age=31536000`;
            
            // Sayfa yenilemeden tablo görünümünü değiştir
            if (isCompact) {
                document.body.classList.add('table-compact');
            } else {
                document.body.classList.remove('table-compact');
            }
        });
    });

    // Tema sıfırlama butonu
    const resetButton = document.getElementById('reset-changes');
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            // Varsayılan değerleri ayarla
            document.cookie = 'dark=auto;path=/;max-age=31536000';
            document.cookie = 'siteColor=#066fd1;path=/;max-age=31536000';
            document.cookie = 'themeBase=neutral;path=/;max-age=31536000';
            document.cookie = 'themeFont=Inter, system-ui, -apple-system, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, \'Noto Sans\', sans-serif;path=/;max-age=31536000';
            document.cookie = 'themeRadius=0.25rem;path=/;max-age=31536000';
            document.cookie = 'tableCompact=0;path=/;max-age=31536000';
            document.cookie = 'themeFontSize=small;path=/;max-age=31536000';
            
            // Sayfayı yenile
            window.location.reload();
        });
    }

    // Navbar'daki tema geçiş düğmesi - Sırayla Açık -> Karanlık -> Sistem modları arasında geçiş yapar
    function initThemeSwitch() {
        // Tema düğmesini bul
        const themeSwitch = document.getElementById('switch');
        
        if (!themeSwitch) return;
        
        // Cookie'den mevcut temayı al
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }
        
        // Mevcut tema ayarını al
        let currentTheme = getCookie('dark');
        
        // Eğer tema ayarı yoksa, varsayılan olarak sistem teması kullan
        if (!currentTheme || (currentTheme !== '0' && currentTheme !== '1' && currentTheme !== 'auto')) {
            currentTheme = 'auto'; // Sistem teması
        }
        
        // Tema durumunu güncelle
        function updateThemeState() {
            const themeContainer = document.querySelector('.theme-mode');
            if (!themeContainer) return;
            
            // Önce mevcut tema durumunu temizle
            themeContainer.removeAttribute('data-theme');
            
            // Mevcut temaya göre data-theme özniteliğini ayarla
            if (currentTheme === 'auto') {
                // Sistem teması için
                themeContainer.setAttribute('data-theme', 'auto');
                
                // Sistem teması için, sistem ayarına göre switch'i ayarla
                const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                themeSwitch.checked = prefersDarkMode;
            } else if (currentTheme === '1') {
                // Karanlık mod
                themeContainer.setAttribute('data-theme', 'dark');
                themeSwitch.checked = true;
            } else {
                // Açık mod
                themeContainer.setAttribute('data-theme', 'light');
                themeSwitch.checked = false;
            }
        }
        
        // Sistem temasını kontrol et
        function checkSystemTheme() {
            if (currentTheme === 'auto') {
                const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const body = document.body;
                
                body.setAttribute('data-bs-theme', prefersDarkMode ? 'dark' : 'light');
                body.classList.remove(prefersDarkMode ? 'light' : 'dark');
                body.classList.add(prefersDarkMode ? 'dark' : 'light');
                
                // Sistem teması değiştiğinde tema durumunu güncelle
                updateThemeState();
            }
        }
        
        // Başlangıçta tema durumunu ayarla ve sistem temasını kontrol et
        updateThemeState();
        checkSystemTheme();
        
        // Tema düğmesine tıklama olayı ekle
        themeSwitch.addEventListener('change', function() {
            // Sırayla geçiş: Açık -> Karanlık -> Sistem -> Açık ...
            if (currentTheme === '0') {
                // Açık moddan karanlık moda geç
                currentTheme = '1';
            } else if (currentTheme === '1') {
                // Karanlık moddan sistem moduna geç
                currentTheme = 'auto';
            } else {
                // Sistem modundan açık moda geç
                currentTheme = '0';
            }
            
            // Cookie'yi ayarla
            document.cookie = `dark=${currentTheme};path=/;max-age=31536000`;
            
            // Tema sınıflarını güncelle
            const body = document.body;
            
            if (currentTheme === '1') {
                body.setAttribute('data-bs-theme', 'dark');
                body.classList.remove('light');
                body.classList.add('dark');
            } else if (currentTheme === '0') {
                body.setAttribute('data-bs-theme', 'light');
                body.classList.remove('dark');
                body.classList.add('light');
            } else if (currentTheme === 'auto') {
                const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                body.setAttribute('data-bs-theme', prefersDarkMode ? 'dark' : 'light');
                body.classList.remove(prefersDarkMode ? 'light' : 'dark');
                body.classList.add(prefersDarkMode ? 'dark' : 'light');
            }
            
            // Tema durumunu güncelle
            updateThemeState();
            
            // Mevcut primary rengi koru - tema değişiminde tekrar uygula
            const currentColor = getCookie('siteColor') || '#066fd1';
            const primaryPalette = generatePrimaryPalette(currentColor);
            applyPrimaryPalette(primaryPalette);
            
            // CSS değişkenlerini zorla güncelle
            forceUpdateThemeVariables();
            
            // Smooth transition için kısa delay
            setTimeout(() => {
                forceUpdateAllElements();
            }, 50);
        });
        
        // Sistem teması değiştiğinde otomatik güncelleme
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            if (currentTheme === 'auto') {
                const prefersDarkMode = e.matches;
                const body = document.body;
                
                body.setAttribute('data-bs-theme', prefersDarkMode ? 'dark' : 'light');
                body.classList.remove(prefersDarkMode ? 'light' : 'dark');
                body.classList.add(prefersDarkMode ? 'dark' : 'light');
                
                // Sistem teması değiştiğinde tema durumunu güncelle
                updateThemeState();
                
                // Sistem tema değişiminde de primary rengi koru
                const currentColor = getCookie('siteColor') || '#066fd1';
                const primaryPalette = generatePrimaryPalette(currentColor);
                applyPrimaryPalette(primaryPalette);
            }
        });
    }

    // Yardımcı Fonksiyonlar
    
    // Primary renk paleti oluşturma - Tabler.io uyumlu
    function generatePrimaryPalette(primaryColor) {
        // HEX rengini RGB'ye dönüştür
        const hex = primaryColor.replace('#', '');
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);
        
        // HSL'e dönüştür
        const [h, s, l] = rgbToHsl(r, g, b);
        
        // Tabler.io primary palet tonları
        const palette = {
            50: hslToHex(h, Math.min(s, 0.3), Math.min(l + 0.45, 0.95)),
            100: hslToHex(h, Math.min(s, 0.4), Math.min(l + 0.35, 0.9)),
            200: hslToHex(h, Math.min(s + 0.1, 0.6), Math.min(l + 0.25, 0.85)),
            300: hslToHex(h, Math.min(s + 0.15, 0.7), Math.min(l + 0.15, 0.75)),
            400: hslToHex(h, Math.min(s + 0.2, 0.8), Math.min(l + 0.05, 0.65)),
            500: primaryColor, // Ana renk
            600: hslToHex(h, Math.min(s + 0.1, 0.9), Math.max(l - 0.1, 0.2)),
            700: hslToHex(h, Math.min(s + 0.15, 0.95), Math.max(l - 0.2, 0.15)),
            800: hslToHex(h, Math.min(s + 0.2, 1), Math.max(l - 0.3, 0.1)),
            900: hslToHex(h, Math.min(s + 0.25, 1), Math.max(l - 0.4, 0.05)),
            950: hslToHex(h, Math.min(s + 0.3, 1), Math.max(l - 0.45, 0.02))
        };
        
        return palette;
    }
    
    // Primary paleti uygula
    function applyPrimaryPalette(palette) {
        const root = document.documentElement;
        
        // Tabler.io primary CSS değişkenlerini ayarla - !important ile zorla
        root.style.setProperty('--tblr-primary', palette[500], 'important');
        root.style.setProperty('--tblr-primary-rgb', hexToRgb(palette[500]), 'important');
        root.style.setProperty('--tblr-primary-50', palette[50], 'important');
        root.style.setProperty('--tblr-primary-100', palette[100], 'important');
        root.style.setProperty('--tblr-primary-200', palette[200], 'important');
        root.style.setProperty('--tblr-primary-300', palette[300], 'important');
        root.style.setProperty('--tblr-primary-400', palette[400], 'important');
        root.style.setProperty('--tblr-primary-500', palette[500], 'important');
        root.style.setProperty('--tblr-primary-600', palette[600], 'important');
        root.style.setProperty('--tblr-primary-700', palette[700], 'important');
        root.style.setProperty('--tblr-primary-800', palette[800], 'important');
        root.style.setProperty('--tblr-primary-900', palette[900], 'important');
        root.style.setProperty('--tblr-primary-950', palette[950], 'important');
        
        // Primary text renkleri hesapla ve uygula
        const primaryTextColor = calculateContrastColor(palette[500]);
        const primaryLightTextColor = calculateContrastColor(palette[100]);
        
        // Hover renkleri hesapla
        const primaryHoverBg = primaryTextColor === '#ffffff' ? 'rgba(255, 255, 255, 0.15)' : 'rgba(0, 0, 0, 0.15)';
        const primaryLtHoverBg = primaryLightTextColor === '#ffffff' ? 'rgba(255, 255, 255, 0.15)' : 'rgba(0, 0, 0, 0.15)';
        
        root.style.setProperty('--tblr-primary-text', primaryTextColor, 'important');
        root.style.setProperty('--tblr-primary-lt-text', primaryLightTextColor, 'important');
        
        // Hover renkleri uygula
        root.style.setProperty('--tblr-primary-hover-bg', primaryHoverBg, 'important');
        root.style.setProperty('--tblr-primary-hover-text', primaryTextColor, 'important');
        root.style.setProperty('--tblr-primary-lt-hover-bg', primaryLtHoverBg, 'important');
        root.style.setProperty('--tblr-primary-lt-hover-text', primaryLightTextColor, 'important');
        
        // Eski değişken uyumluluğu için
        root.style.setProperty('--primary-color', palette[500], 'important');
        root.style.setProperty('--primary-color-rgb', hexToRgb(palette[500]), 'important');
        root.style.setProperty('--primary-text-color', primaryTextColor, 'important');
        
        // Light tema tonları
        root.style.setProperty('--tblr-primary-lt', palette[100], 'important');
        root.style.setProperty('--tblr-primary-lt-rgb', hexToRgb(palette[100]), 'important');
        
        // Body'de de değişkenleri zorla - hem light hem dark tema için
        const body = document.body;
        body.style.setProperty('--tblr-primary', palette[500], 'important');
        body.style.setProperty('--tblr-primary-rgb', hexToRgb(palette[500]), 'important');
        body.style.setProperty('--tblr-primary-text', primaryTextColor, 'important');
        body.style.setProperty('--tblr-primary-50', palette[50], 'important');
        body.style.setProperty('--tblr-primary-100', palette[100], 'important');
        body.style.setProperty('--tblr-primary-200', palette[200], 'important');
        body.style.setProperty('--tblr-primary-300', palette[300], 'important');
        body.style.setProperty('--tblr-primary-400', palette[400], 'important');
        body.style.setProperty('--tblr-primary-500', palette[500], 'important');
        body.style.setProperty('--tblr-primary-600', palette[600], 'important');
        body.style.setProperty('--tblr-primary-700', palette[700], 'important');
        body.style.setProperty('--tblr-primary-800', palette[800], 'important');
        body.style.setProperty('--tblr-primary-900', palette[900], 'important');
        body.style.setProperty('--tblr-primary-950', palette[950], 'important');
        
        // Önemli elementleri de güncelle
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.style.setProperty('--tblr-primary', palette[500], 'important');
        });
        
        // Tüm primary sınıflarını kullanan elementleri güncelle
        const primaryElements = document.querySelectorAll('.btn-primary, .bg-primary, .text-primary, .border-primary');
        primaryElements.forEach(element => {
            element.style.setProperty('--tblr-primary', palette[500], 'important');
            element.style.setProperty('--tblr-primary-rgb', hexToRgb(palette[500]), 'important');
        });
    }
    
    // Kontrast rengini hesapla (beyaz ya da siyah)
    function calculateContrastColor(backgroundColor) {
        // HEX rengini RGB'ye dönüştür
        const hex = backgroundColor.replace('#', '');
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);
        
        // Luminance hesapla (W3C formülü)
        const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
        
        // Koyu renkler için beyaz, açık renkler için siyah metin
        return luminance > 0.5 ? '#000000' : '#ffffff';
    }
    
    // RGB'den HSL'e dönüştür
    function rgbToHsl(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        const max = Math.max(r, g, b), min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;
        
        if (max === min) {
            h = s = 0;
        } else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }
        return [h, s, l];
    }
    
    // HSL'den HEX'e dönüştür
    function hslToHex(h, s, l) {
        const hue2rgb = (p, q, t) => {
            if (t < 0) t += 1;
            if (t > 1) t -= 1;
            if (t < 1/6) return p + (q - p) * 6 * t;
            if (t < 1/2) return q;
            if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
            return p;
        };
        
        if (s === 0) {
            const gray = Math.round(l * 255);
            return `#${gray.toString(16).padStart(2, '0').repeat(3)}`;
        }
        
        const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        const p = 2 * l - q;
        const r = Math.round(hue2rgb(p, q, h + 1/3) * 255);
        const g = Math.round(hue2rgb(p, q, h) * 255);
        const b = Math.round(hue2rgb(p, q, h - 1/3) * 255);
        
        return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
    }
    
    // HEX'den RGB string'e dönüştür
    function hexToRgb(hex) {
        const r = parseInt(hex.substring(1, 3), 16);
        const g = parseInt(hex.substring(3, 5), 16);
        const b = parseInt(hex.substring(5, 7), 16);
        return `${r}, ${g}, ${b}`;
    }
    
    // Metin rengini hesaplama
    function updateTextColor(backgroundColor) {
        // Rengi parçalara ayır
        let r, g, b;
        
        // HEX renk formatını kontrol et
        if (backgroundColor.startsWith('#')) {
            const hex = backgroundColor.substring(1);
            r = parseInt(hex.substring(0, 2), 16);
            g = parseInt(hex.substring(2, 4), 16);
            b = parseInt(hex.substring(4, 6), 16);
        } 
        // RGB formatını kontrol et
        else if (backgroundColor.startsWith('rgb')) {
            const rgbMatch = backgroundColor.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
            if (rgbMatch) {
                r = parseInt(rgbMatch[1]);
                g = parseInt(rgbMatch[2]);
                b = parseInt(rgbMatch[3]);
            }
        }
        
        // Luminance (parlaklık) hesapla - W3C formülü
        const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
        
        // Koyu renkler için beyaz, açık renkler için siyah metin
        const textColor = luminance > 0.5 ? '#000000' : '#ffffff';
        document.documentElement.style.setProperty('--primary-text-color', textColor);
        document.cookie = `siteTextColor=${textColor};path=/;max-age=31536000`;
    }
    
    // Radius örneklerini güncelle
    function updateRadiusExamples() {
        const examples = document.querySelectorAll('.radius-example');
        examples.forEach(example => {
            if (example.classList.contains('active')) {
                example.style.backgroundColor = getComputedStyle(document.documentElement).getPropertyValue('--tblr-primary');
            }
        });
    }
    
    // Card body elementlerinin radius değerlerini güncelle
    function updateCardBodyRadiuses(radius) {
        const cardBodies = document.querySelectorAll('.card-body');
        cardBodies.forEach(cardBody => {
            cardBody.style.borderRadius = radius;
        });
        
        // Buton radius değerlerini de güncelle
        const buttons = document.querySelectorAll('.btn:not(.btn-pill):not(.btn-square)');
        buttons.forEach(button => {
            button.style.borderRadius = radius;
        });
    }
    
    // Google Fonts yükleme
    function ensureGoogleFont(id, href) {
        if (!document.getElementById(id)) {
            const link = document.createElement('link');
            link.id = id;
            link.rel = 'stylesheet';
            link.href = href;
            document.head.appendChild(link);
        }
    }

    // Cookie okuma
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    // Font boyutlarını güncelleme fonksiyonu
    function updateFontSizes(sizeCategory) {
        // Varsayılan boyutlar (normal boyut için)
        const defaultSizes = {
            'font-size': '0.875rem',
            'body-font-size': '0.875rem',
            'dropdown-font-size': '0.875rem',
            'code-font-size': '0.875rem',
            'h1-font-size': '1.5rem',
            'h2-font-size': '1.25rem',
            'h3-font-size': '1.125rem',
            'h4-font-size': '1rem',
            'h5-font-size': '0.875rem',
            'h6-font-size': '0.75rem',
            'small-font-size': '0.75rem',
            'btn-font-size': '0.875rem',
            'btn-sm-font-size': '0.75rem',
            'btn-lg-font-size': '1rem',
            'input-font-size': '0.875rem',
            'input-sm-font-size': '0.75rem',
            'input-lg-font-size': '1rem',
            'table-font-size': '0.875rem',
            'table-sm-font-size': '0.75rem',
            'table-lg-font-size': '1rem',
            'blockquote-font-size': '1rem',
            'nav-link-font-size': '0.875rem'
        };
        
        // Boyut çarpanları
        let factor = 1;
        if (sizeCategory === 'small') factor = 0.857;
        if (sizeCategory === 'large') factor = 1.143;
        
        // Tüm CSS değişkenlerini güncelle
        for (const [key, value] of Object.entries(defaultSizes)) {
            // Rem değerini alıp sayısal değere dönüştür
            const remValue = parseFloat(value);
            // Yeni boyutu hesapla ve rem olarak ayarla
            const newSize = (remValue * factor).toFixed(3) + 'rem';
            // CSS değişkenini güncelle
            document.documentElement.style.setProperty(`--tblr-${key}`, newSize);
        }
    }

    // Başlangıç durumunu ayarla
    function initializeThemeSettings() {
        // Mevcut tema rengi için primary paleti ve metin rengini güncelle
        const currentColor = getCookie('siteColor') || '#066fd1';
        const primaryPalette = generatePrimaryPalette(currentColor);
        applyPrimaryPalette(primaryPalette);
        updateTextColor(currentColor);
        
        // Tablo görünümünü ayarla
        const tableCompact = getCookie('tableCompact') || '0';
        if (tableCompact === '1') {
            document.body.classList.add('table-compact');
        } else {
            document.body.classList.remove('table-compact');
        }
        
        // Gri tonu ayarla
        const baseTheme = getCookie('themeBase') || 'neutral';
        document.body.classList.remove(
            'theme-base-slate', 
            'theme-base-cool', 
            'theme-base-neutral', 
            'theme-base-warm', 
            'theme-base-indigo', 
            'theme-base-azure', 
            'theme-base-primary', 
            'theme-base-secondary', 
            'theme-base-tertiary', 
            'theme-base-error', 
            'theme-base-neutral-variant'
        );
        document.body.classList.add(`theme-base-${baseTheme}`);
        
        // Font boyutunu ayarla
        const fontSize = getCookie('themeFontSize') || 'small';
        document.body.classList.remove('font-size-small', 'font-size-normal', 'font-size-large');
        document.body.classList.add(`font-size-${fontSize}`);
        
        // Tüm font boyutlarını güncelle
        updateFontSizes(fontSize);
        
        // Card Body radius değerlerini ayarla
        const themeRadius = getCookie('themeRadius') || '0.25rem';
        updateCardBodyRadiuses(themeRadius);
        
        // Gerekli Google fontlarını yükle
        const currentFont = getCookie('themeFont');
        if (currentFont) {
            if (currentFont.includes('Roboto')) {
                ensureGoogleFont('roboto-font', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
            } else if (currentFont.includes('Poppins')) {
                ensureGoogleFont('poppins-font', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap');
            }
        }
        
        // Radius örneklerini başlangıçta ayarla
        updateRadiusExamples();
        
        // Sistem teması değişikliğini dinle
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (getCookie('dark') === 'auto') {
                const prefersDarkMode = e.matches;
                document.body.setAttribute('data-bs-theme', prefersDarkMode ? 'dark' : 'light');
                document.body.classList.remove(prefersDarkMode ? 'light' : 'dark');
                document.body.classList.add(prefersDarkMode ? 'dark' : 'light');
                
                if (document.getElementById('switch')) {
                    document.getElementById('switch').checked = prefersDarkMode;
                }
                
                // Primary rengi yeniden uygula
                const currentColor = getCookie('siteColor') || '#066fd1';
                const primaryPalette = generatePrimaryPalette(currentColor);
                applyPrimaryPalette(primaryPalette);
                
                // CSS değişkenlerini zorla güncelle
                forceUpdateThemeVariables();
                
                setTimeout(() => {
                    forceUpdateAllElements();
                }, 50);
            }
        });
    }
    
    
    // Tema ayarlarını başlat
    initializeThemeSettings();
    
    // Koyu tema değiştiğinde radius örneklerini güncelle
    document.addEventListener('darkModeChange', updateRadiusExamples);
        
    // Sayfa tamamen yüklendikten sonra bir kez daha tema düğmesini kontrol et
    window.addEventListener('load', function() {
        initThemeSwitch();
        initColorPickers(); // Color picker'ları da yeniden başlat
        
        // Mevcut seçili rengi zorla uygula
        const selectedColorRadio = document.querySelector('input[name="theme-primary"]:checked');
        if (selectedColorRadio) {
            const color = selectedColorRadio.value;
            const primaryPalette = generatePrimaryPalette(color);
            applyPrimaryPalette(primaryPalette);
            updateTextColor(color);
        }
    });
    
    // Tema değişkenlerini zorla güncelleme fonksiyonu
    function forceUpdateThemeVariables() {
        const root = document.documentElement;
        const body = document.body;
        const isDark = body.getAttribute('data-bs-theme') === 'dark';
        
        // Tema değişkenlerini zorla yenile
        if (isDark) {
            root.classList.add('dark-theme');
            root.classList.remove('light-theme');
        } else {
            root.classList.add('light-theme');
            root.classList.remove('dark-theme');
        }
        
        // Body'de CSS değişkenlerini zorla güncelle
        body.style.setProperty('--theme-transition', 'none');
        setTimeout(() => {
            body.style.removeProperty('--theme-transition');
        }, 100);
    }
    
    // Tüm elementleri zorla güncelleme fonksiyonu
    function forceUpdateAllElements() {
        // Tüm card'ları güncelle
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.style.display = 'none';
            card.offsetHeight; // reflow trigger
            card.style.display = '';
        });
        
        // Badge'leri güncelle
        const badges = document.querySelectorAll('.badge');
        badges.forEach(badge => {
            // Önce inline style'ları temizle
            badge.style.removeProperty('background-color');
            badge.style.removeProperty('color');
            badge.style.removeProperty('border-color');
            
            // Reflow tetikle
            badge.style.display = 'none';
            badge.offsetHeight;
            badge.style.display = '';
            
            // CSS class'ları yeniden uygula
            const isDark = document.body.getAttribute('data-bs-theme') === 'dark';
            
            if (badge.classList.contains('bg-blue-lt')) {
                if (isDark) {
                    badge.style.setProperty('background-color', 'var(--tblr-primary-800)', 'important');
                    badge.style.setProperty('color', 'var(--tblr-primary-200)', 'important');
                } else {
                    badge.style.setProperty('background-color', 'var(--tblr-primary-100)', 'important');
                    badge.style.setProperty('color', 'var(--tblr-primary-700)', 'important');
                }
            }
        });
        
        // Avatar'ları güncelle
        const avatars = document.querySelectorAll('.avatar');
        avatars.forEach(avatar => {
            const computedStyle = window.getComputedStyle(avatar);
            avatar.style.backgroundColor = computedStyle.backgroundColor;
            avatar.style.color = computedStyle.color;
        });
        
        // List group item'ları güncelle
        const listItems = document.querySelectorAll('.list-group-item');
        listItems.forEach(item => {
            const computedStyle = window.getComputedStyle(item);
            item.style.backgroundColor = computedStyle.backgroundColor;
            item.style.color = computedStyle.color;
        });
        
        // Button'ları güncelle
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.style.display = 'none';
            button.offsetHeight; // reflow trigger
            button.style.display = '';
        });
        
        // Text renkleri güncelle
        const textElements = document.querySelectorAll('.text-muted, .text-body');
        textElements.forEach(element => {
            const computedStyle = window.getComputedStyle(element);
            element.style.color = computedStyle.color;
        });
        
        // Form elementleri güncelle
        const formElements = document.querySelectorAll('.form-control, .form-select');
        formElements.forEach(element => {
            const computedStyle = window.getComputedStyle(element);
            element.style.backgroundColor = computedStyle.backgroundColor;
            element.style.color = computedStyle.color;
            element.style.borderColor = computedStyle.borderColor;
        });
        
        // Navbar dropdown'ları güncelle
        const dropdownItems = document.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(item => {
            item.style.display = 'none';
            item.offsetHeight; // reflow trigger
            item.style.display = '';
        });
        
        // Inline style'ları temizle
        clearInlineStyles();
        
        // Boş style attribute'ları olan elementleri özel olarak güncelle
        updateEmptyStyleElements();
        
        // Tema elementleri güncellendi
    }
    
    // Inline style'ları temizleyerek CSS'in kontrolü ele almasını sağla
    function clearInlineStyles() {
        // Problemli RGB renkleri olan elementleri bul ve style'larını temizle
        const problematicColors = [
            'rgb(248, 250, 252)',
            'rgb(255, 255, 255)', 
            'rgb(220, 225, 231)',
            'rgb(71, 85, 105)',
            'rgb(46, 60, 81)',
            'rgb(33, 14, 90)',
            'rgb(174, 155, 230)'
        ];
        
        problematicColors.forEach(color => {
            // Background color'u temizle
            const bgElements = document.querySelectorAll(`[style*="background-color: ${color}"]`);
            bgElements.forEach(element => {
                element.style.removeProperty('background-color');
            });
            
            // Text color'u temizle
            const textElements = document.querySelectorAll(`[style*="color: ${color}"]`);
            textElements.forEach(element => {
                element.style.removeProperty('color');
            });
            
            // Border color'u temizle
            const borderElements = document.querySelectorAll(`[style*="border-color: ${color}"]`);
            borderElements.forEach(element => {
                element.style.removeProperty('border-color');
            });
        });
        
        // Form elementlerini özel olarak temizle
        const formElements = document.querySelectorAll('.form-control, .form-select');
        formElements.forEach(element => {
            if (element.style.backgroundColor || element.style.color || element.style.borderColor) {
                element.style.removeProperty('background-color');
                element.style.removeProperty('color');
                element.style.removeProperty('border-color');
            }
        });
        
        // Badge'leri temizle
        const badges = document.querySelectorAll('.badge');
        badges.forEach(badge => {
            if (badge.style.backgroundColor || badge.style.color) {
                badge.style.removeProperty('background-color');
                badge.style.removeProperty('color');
                badge.style.removeProperty('border-color');
            }
            
            // Badge sınıflarını zorla güncelle
            badge.classList.remove('theme-updating');
            badge.offsetHeight; // reflow trigger
            badge.classList.add('theme-updating');
            
            setTimeout(() => {
                badge.classList.remove('theme-updating');
            }, 200);
        });
        
        // List group item'ları temizle
        const listItems = document.querySelectorAll('.list-group-item, .bg-muted-lt');
        listItems.forEach(item => {
            if (item.style.backgroundColor || item.style.color) {
                item.style.removeProperty('background-color');
                item.style.removeProperty('color');
            }
        });
        
        // Icon'ları temizle
        const icons = document.querySelectorAll('.fas, .far, .fab, .text-muted');
        icons.forEach(icon => {
            if (icon.style.color) {
                icon.style.removeProperty('color');
            }
        });
        
        // Inline style'lar temizlendi
    }
    
    // Boş style attribute'ları olan elementleri güncelle
    function updateEmptyStyleElements() {
        // Boş style attribute'ları olan elementleri bul
        const emptyStyleElements = document.querySelectorAll('[style=""]');
        
        emptyStyleElements.forEach(element => {
            // Element tipine göre uygun sınıfları tetikle
            if (element.classList.contains('list-group-item') || element.classList.contains('bg-muted-lt')) {
                element.classList.add('theme-updating');
                setTimeout(() => {
                    element.classList.remove('theme-updating');
                }, 100);
            }
            
            if (element.classList.contains('form-control') || element.classList.contains('form-select')) {
                element.classList.add('theme-updating');
                setTimeout(() => {
                    element.classList.remove('theme-updating');
                }, 100);
            }
            
            if (element.classList.contains('fas') || element.classList.contains('text-muted')) {
                element.classList.add('theme-updating');
                setTimeout(() => {
                    element.classList.remove('theme-updating');
                }, 100);
            }
            
            if (element.classList.contains('badge')) {
                element.classList.add('theme-updating');
                setTimeout(() => {
                    element.classList.remove('theme-updating');
                }, 100);
            }
        });
        
        // Özel problemli sınıf kombinasyonlarını bul
        const problematicElements = [
            '.list-group-item.py-3.px-2.mx-1',
            '.d-flex.align-items-center.p-2.bg-muted-lt.rounded',
            '.col-12.mb-2 .bg-muted-lt'
        ];
        
        problematicElements.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(element => {
                element.style.display = 'none';
                element.offsetHeight; // reflow trigger
                element.style.display = '';
                
                element.classList.add('theme-updating');
                setTimeout(() => {
                    element.classList.remove('theme-updating');
                }, 150);
            });
        });
        
        // Tüm bg-muted-lt elementlerini zorla güncelle
        const bgMutedElements = document.querySelectorAll('.bg-muted-lt');
        bgMutedElements.forEach(element => {
            element.style.display = 'none';
            element.offsetHeight;
            element.style.display = '';
        });
        
        // Bu spesifik problemli element için ekstra güncelleme
        const specificElements = document.querySelectorAll('div.d-flex.align-items-center.p-2.bg-muted-lt.rounded[style=""]');
        specificElements.forEach(element => {
            // Inline style'ları tamamen kaldır
            element.removeAttribute('style');
            
            // Reflow tetikle
            element.style.display = 'none';
            element.offsetHeight;
            element.style.display = '';
            
            // CSS sınıflarını zorla yeniden uygula
            const className = element.className;
            element.className = '';
            element.offsetHeight;
            element.className = className;
            
            // Theme-updating sınıfı ekle
            element.classList.add('theme-updating');
            setTimeout(() => {
                element.classList.remove('theme-updating');
            }, 200);
        });
        
        // Boş style elementleri güncellendi
    }
});