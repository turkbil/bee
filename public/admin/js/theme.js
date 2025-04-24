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
                if (document.getElementById('switch')) {
                    document.getElementById('switch').checked = true;
                }
            } else if (themeMode === 'light') {
                body.setAttribute('data-bs-theme', 'light');
                body.classList.remove('dark');
                body.classList.add('light');
                if (document.getElementById('switch')) {
                    document.getElementById('switch').checked = false;
                }
            } else if (themeMode === 'auto') {
                // Sistem ayarını kontrol et
                const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                body.setAttribute('data-bs-theme', prefersDarkMode ? 'dark' : 'light');
                body.classList.remove(prefersDarkMode ? 'light' : 'dark');
                body.classList.add(prefersDarkMode ? 'dark' : 'light');
                if (document.getElementById('switch')) {
                    document.getElementById('switch').checked = prefersDarkMode;
                }
            }
        });
    });

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
            
            // Örnekleri güncelle
            radiusExamples.forEach((example, index) => {
                if (index === selectedIndex) {
                    example.classList.add('active');
                } else {
                    example.classList.remove('active');
                }
            });
        });
    }

    // Yazı tipi değiştirme
    const fontRadios = document.querySelectorAll('input[name="theme-font"]');
    fontRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const font = this.value;
            document.cookie = `themeFont=${encodeURIComponent(font)};path=/;max-age=31536000`;
            
            // Sayfa yenilemeden yazı tipini değiştir
            document.documentElement.style.setProperty('--tblr-font-family', font);
            document.body.style.fontFamily = font;
            
            // Roboto ve Poppins için Google Fonts
            if (font === "'Roboto', sans-serif") {
                ensureGoogleFont('roboto-font', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
            } else if (font === "'Poppins', sans-serif") {
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
            document.body.classList.remove('theme-base-slate', 'theme-base-gray', 'theme-base-zinc', 'theme-base-neutral', 'theme-base-stone');
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
            document.cookie = 'dark=0;path=/;max-age=31536000';
            document.cookie = 'siteColor=#066fd1;path=/;max-age=31536000';
            document.cookie = 'themeBase=gray;path=/;max-age=31536000';
            document.cookie = 'themeFont=Inter, system-ui, -apple-system, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, \'Noto Sans\', sans-serif;path=/;max-age=31536000';
            document.cookie = 'themeRadius=0.5rem;path=/;max-age=31536000';
            document.cookie = 'tableCompact=1;path=/;max-age=31536000';
            
            // Sayfayı yenile
            window.location.reload();
        });
    }

    // Navbar'daki tema geçiş düğmesi
    const themeSwitch = document.getElementById('switch');
    if (themeSwitch) {
        themeSwitch.addEventListener('change', function() {
            const isDark = this.checked;
            
            // Auto modunda ise değiştirme
            if (getCookie('dark') === 'auto') {
                return;
            }
            
            document.cookie = `dark=${isDark ? '1' : '0'};path=/;max-age=31536000`;
            
            // Sayfa yenilemeden tema modunu değiştir
            const body = document.body;
            if (isDark) {
                body.setAttribute('data-bs-theme', 'dark');
                body.classList.remove('light');
                body.classList.add('dark');
                
                // Radio butonları güncelle
                const darkRadio = document.querySelector('input[name="theme"][value="dark"]');
                if (darkRadio) darkRadio.checked = true;
            } else {
                body.setAttribute('data-bs-theme', 'light');
                body.classList.remove('dark');
                body.classList.add('light');
                
                // Radio butonları güncelle
                const lightRadio = document.querySelector('input[name="theme"][value="light"]');
                if (lightRadio) lightRadio.checked = true;
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

    // Başlangıç durumunu ayarla
    function initializeThemeSettings() {
        // Mevcut tema rengi için metin rengini güncelle
        const currentColor = getCookie('siteColor') || '#066fd1';
        updateTextColor(currentColor);
        
        // Tablo görünümünü ayarla
        const tableCompact = getCookie('tableCompact') || '1';
        if (tableCompact === '1') {
            document.body.classList.add('table-compact');
        } else {
            document.body.classList.remove('table-compact');
        }
        
        // Gri tonu ayarla
        const baseTheme = getCookie('themeBase') || 'gray';
        document.body.classList.remove('theme-base-slate', 'theme-base-gray', 'theme-base-zinc', 'theme-base-neutral', 'theme-base-stone');
        document.body.classList.add(`theme-base-${baseTheme}`);
        
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
});