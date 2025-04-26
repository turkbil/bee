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
        });
    });
    
    // Karanlık mod switch için tema geçiş fonksiyonu
    initThemeSwitch();

    // Ana renk değiştirme
    const colorRadios = document.querySelectorAll('input[name="theme-primary"]');
    colorRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const color = this.value;
            document.cookie = `siteColor=${color};path=/;max-age=31536000`;
            
            // Sayfa yenilemeden rengi değiştir
            document.documentElement.style.setProperty('--primary-color', color);
            updateTextColor(color);
            
            // Radius örneklerini güncelle (aktif renk değişimi için)
            updateRadiusExamples();
        });
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
            document.body.classList.remove('theme-base-slate', 'theme-base-cool', 'theme-base-neutral', 'theme-base-warm', 'theme-base-indigo', 'theme-base-azure');
            document.body.classList.add(`theme-base-${baseTheme}`);
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
            document.cookie = 'themeBase=cool;path=/;max-age=31536000';
            document.cookie = 'themeFont=Inter, system-ui, -apple-system, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, \'Noto Sans\', sans-serif;path=/;max-age=31536000';
            document.cookie = 'themeRadius=0.5rem;path=/;max-age=31536000';
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
            }
        });
    }

    // Yardımcı Fonksiyonlar
    
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
                example.style.backgroundColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color');
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
        // Mevcut tema rengi için metin rengini güncelle
        const currentColor = getCookie('siteColor') || '#066fd1';
        updateTextColor(currentColor);
        
        // Tablo görünümünü ayarla
        const tableCompact = getCookie('tableCompact') || '0';
        if (tableCompact === '1') {
            document.body.classList.add('table-compact');
        } else {
            document.body.classList.remove('table-compact');
        }
        
        // Gri tonu ayarla
        const baseTheme = getCookie('themeBase') || 'cool';
        document.body.classList.remove('theme-base-slate', 'theme-base-cool', 'theme-base-neutral', 'theme-base-warm', 'theme-base-indigo', 'theme-base-azure');
        document.body.classList.add(`theme-base-${baseTheme}`);
        
        // Font boyutunu ayarla
        const fontSize = getCookie('themeFontSize') || 'small';
        document.body.classList.remove('font-size-small', 'font-size-normal', 'font-size-large');
        document.body.classList.add(`font-size-${fontSize}`);
        
        // Tüm font boyutlarını güncelle
        updateFontSizes(fontSize);
        
        // Card Body radius değerlerini ayarla
        const themeRadius = getCookie('themeRadius') || '0.5rem';
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
    });
});