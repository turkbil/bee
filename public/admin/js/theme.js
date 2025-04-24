// public/admin/js/theme.js
document.addEventListener('DOMContentLoaded', function() {
    // Tema modunu değiştirme (açık/koyu/sistem)
    const themeRadios = document.querySelectorAll('input[name="theme"]');
    themeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const themeMode = this.value; // light, dark veya auto
            document.cookie = `dark=${themeMode};path=/;max-age=31536000`;
            
            // Sayfa yenilemeden temayı değiştir
            const body = document.body;
            
            if (themeMode === 'dark') {
                body.setAttribute('data-bs-theme', 'dark');
                body.classList.remove('light');
                body.classList.add('dark');
                document.getElementById('switch').checked = true;
            } else if (themeMode === 'light') {
                body.setAttribute('data-bs-theme', 'light');
                body.classList.remove('dark');
                body.classList.add('light');
                document.getElementById('switch').checked = false;
            } else if (themeMode === 'auto') {
                // Sistem ayarını kontrol et
                const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                body.setAttribute('data-bs-theme', prefersDarkMode ? 'dark' : 'light');
                body.classList.remove(prefersDarkMode ? 'light' : 'dark');
                body.classList.add(prefersDarkMode ? 'dark' : 'light');
                document.getElementById('switch').checked = prefersDarkMode;
                
                // Tüm tik işaretlerini gizle ve sadece auto için olanı göster
                document.querySelectorAll('.theme-check').forEach(check => {
                    check.classList.add('d-none');
                });
                this.parentElement.querySelector('.theme-check').classList.remove('d-none');
            }
            
            // Radyo butonlarının tik işaretlerini güncelle
            themeRadios.forEach(r => {
                const checkIcon = r.parentElement.querySelector('.theme-check');
                if (checkIcon) {
                    if (r.checked) {
                        checkIcon.classList.remove('d-none');
                    } else {
                        checkIcon.classList.add('d-none');
                    }
                }
            });
        });
    });

    // Renk şeması değiştirme
    const colorRadios = document.querySelectorAll('input[name="theme-primary"]');
    colorRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const color = this.value;
            document.cookie = `siteColor=${color};path=/;max-age=31536000`;
            
            // Sayfa yenilemeden rengi değiştir
            document.documentElement.style.setProperty('--primary-color', color);
            updateTextColor(color);
            
            // Seçilen rengin tik işaretini göster, diğerlerini gizle
            document.querySelectorAll('.color-check').forEach(check => {
                check.classList.add('d-none');
            });
            this.parentElement.querySelector('.color-check').classList.remove('d-none');
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

    // Yazı tipi değiştirme
    const fontRadios = document.querySelectorAll('input[name="theme-font"]');
    fontRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const font = this.value;
            document.cookie = `themeFont=${encodeURIComponent(font)};path=/;max-age=31536000`;
            
            // Sayfa yenilemeden yazı tipini değiştir
            document.documentElement.style.setProperty('--tblr-font-family', font);
            document.body.style.fontFamily = font;
        });
    });

    // Köşe yuvarlaklığı değiştirme
    const radiusRadios = document.querySelectorAll('input[name="theme-radius"]');
    radiusRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const radius = this.value;
            document.cookie = `themeRadius=${radius};path=/;max-age=31536000`;
            
            // Sayfa yenilemeden köşe yuvarlaklığını değiştir
            document.documentElement.style.setProperty('--tblr-border-radius', radius);
            
            // Seçilen köşe yuvarlaklığının tik işaretini göster, diğerlerini gizle
            radiusRadios.forEach(r => {
                const checkIcon = r.parentElement.querySelector('div:last-child');
                if (checkIcon) {
                    if (r.checked) {
                        checkIcon.classList.remove('d-none');
                    } else {
                        checkIcon.classList.add('d-none');
                    }
                }
            });
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
                
                // Tik işaretlerini güncelle
                document.querySelectorAll('.theme-check').forEach(check => {
                    check.classList.add('d-none');
                });
                if (darkRadio && darkRadio.parentElement.querySelector('.theme-check')) {
                    darkRadio.parentElement.querySelector('.theme-check').classList.remove('d-none');
                }
            } else {
                body.setAttribute('data-bs-theme', 'light');
                body.classList.remove('dark');
                body.classList.add('light');
                
                // Radio butonları güncelle
                const lightRadio = document.querySelector('input[name="theme"][value="light"]');
                if (lightRadio) lightRadio.checked = true;
                
                // Tik işaretlerini güncelle
                document.querySelectorAll('.theme-check').forEach(check => {
                    check.classList.add('d-none');
                });
                if (lightRadio && lightRadio.parentElement.querySelector('.theme-check')) {
                    lightRadio.parentElement.querySelector('.theme-check').classList.remove('d-none');
                }
            }
        });
    }

    // Metin rengini hesaplama fonksiyonu
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

    // Cookie okuma yardımcı fonksiyonu
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    // Sayfa yüklendiğinde mevcut tema ayarlarını uygula
    function applyBaseTheme() {
        const baseTheme = getCookie('themeBase') || 'gray';
        document.body.classList.remove('theme-base-slate', 'theme-base-gray', 'theme-base-zinc', 'theme-base-neutral', 'theme-base-stone');
        document.body.classList.add(`theme-base-${baseTheme}`);
    }

    // Sistem teması kontrolü ve uygulama
    function applySystemTheme() {
        if (getCookie('dark') === 'auto') {
            const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.body.setAttribute('data-bs-theme', prefersDarkMode ? 'dark' : 'light');
            document.body.classList.remove(prefersDarkMode ? 'light' : 'dark');
            document.body.classList.add(prefersDarkMode ? 'dark' : 'light');
            
            if (document.getElementById('switch')) {
                document.getElementById('switch').checked = prefersDarkMode;
            }
        }
    }

    // Sayfa yüklendiğinde tema ayarlarını uygula
    applyBaseTheme();
    applySystemTheme();
    
    // Mevcut tema rengi için metin rengini güncelle
    const currentColor = getCookie('siteColor') || '#066fd1';
    updateTextColor(currentColor);
    
    // Sistem temasını dinle
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        applySystemTheme();
    });
});