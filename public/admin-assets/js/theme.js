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
    
    // Karanlık mod switch için tema geçiş fonksiyonu - window.load'da çağrılacak

    // Ana renk değiştirme - Real-time system kullanıyor, bu fonksiyon devre dışı
    function initColorPickers() {
        console.log('🎨 initColorPickers ATLANACAK - Real-time system aktif');
        // Bu fonksiyon artık real-time system tarafından handle ediliyor
        // Duplicate event listener'ları önlemek için devre dışı bırakıldı
    }
    
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
        const radiusMap = ['0', '0.25rem', '0.375rem', '0.5rem', '0.75rem', '1rem'];
        
        radiusSlider.addEventListener('input', function() {
            const selectedIndex = parseInt(this.value);
            const selectedRadius = radiusMap[selectedIndex];
            radiusValue.value = selectedRadius;
            
            // Cookie güncelleme
            document.cookie = `themeRadius=${selectedRadius};path=/;max-age=31536000`;
            
            // Anında radius değişimi için güçlü CSS güncellemesi
            applyRadiusInstantly(selectedRadius);
            
            // Örnekleri güncelle
            radiusExamples.forEach((example, index) => {
                if (index === selectedIndex) {
                    example.classList.add('active');
                } else {
                    example.classList.remove('active');
                }
            });
            
            // Toast bildirimi devre dışı - Livewire handle ediyor
            // showThemeToast('Köşe yuvarlaklığı güncellendi');
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

    // Navbar'daki tema geçiş düğmesi - Basitleştirilmiş ve düzgün çalışan sistem
    function initThemeSwitch() {
        console.log('🎨 initThemeSwitch BAŞLADI');
        
        // Çoklu çağrı kontrolü
        if (window.themeSwitch_initialized) {
            console.log('⚠️ initThemeSwitch zaten çalıştırılmış, atlanıyor...');
            return;
        }
        
        // Tema düğmesini bul
        const themeSwitch = document.getElementById('switch');
        
        if (!themeSwitch) {
            console.log('❌ Theme switch bulunamadı!');
            return;
        }
        
        console.log('✅ Theme switch bulundu:', themeSwitch);
        
        // Cookie'den mevcut temayı al
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }
        
        // Mevcut tema ayarını al
        let currentTheme = getCookie('dark');
        console.log('🍪 Cookie\'den alınan tema:', currentTheme);
        
        // Eğer tema ayarı yoksa, varsayılan olarak sistem teması kullan
        if (!currentTheme || (currentTheme !== '0' && currentTheme !== '1' && currentTheme !== 'auto')) {
            currentTheme = 'auto'; // Sistem teması
            console.log('🔄 Varsayılan tema ayarlandı:', currentTheme);
        }
        
        // Tema durumunu güncelle
        function updateThemeState() {
            console.log('🔄 updateThemeState çağrıldı, currentTheme:', currentTheme);
            
            const themeContainer = document.querySelector('.theme-mode');
            if (!themeContainer) {
                console.log('❌ .theme-mode container bulunamadı!');
                return;
            }
            
            console.log('✅ Theme container bulundu:', themeContainer);
            
            // Önce mevcut tema durumunu temizle
            const oldDataTheme = themeContainer.getAttribute('data-theme');
            console.log('🗑️ Eski data-theme temizleniyor:', oldDataTheme);
            themeContainer.removeAttribute('data-theme');
            
            // Mevcut temaya göre data-theme özniteliğini ayarla
            if (currentTheme === 'auto') {
                // Sistem teması için
                console.log('🔮 AUTO tema modu ayarlanıyor...');
                themeContainer.setAttribute('data-theme', 'auto');
                
                // Sistem teması için, sistem ayarına göre switch'i ayarla
                const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                console.log('🖥️ Sistem dark mode tercihi:', prefersDarkMode);
                themeSwitch.checked = prefersDarkMode;
                console.log('✅ AUTO - checkbox checked:', prefersDarkMode, 'data-theme: auto');
            } else if (currentTheme === '1') {
                // Karanlık mod
                console.log('🌙 DARK tema modu ayarlanıyor...');
                themeContainer.setAttribute('data-theme', 'dark');
                themeSwitch.checked = true;
                console.log('✅ DARK - checkbox checked: true, data-theme: dark');
            } else {
                // Açık mod
                console.log('☀️ LIGHT tema modu ayarlanıyor...');
                themeContainer.setAttribute('data-theme', 'light');
                themeSwitch.checked = false;
                console.log('✅ LIGHT - checkbox checked: false, data-theme: light');
            }
            
            console.log('📋 Final updateThemeState sonucu:', {
                currentTheme: currentTheme,
                dataTheme: themeContainer.getAttribute('data-theme'),
                checkboxChecked: themeSwitch.checked
            });
        }
        
        // Sistem temasını kontrol et
        function checkSystemTheme() {
            console.log('🔍 checkSystemTheme çağrıldı, currentTheme:', currentTheme);
            
            if (currentTheme === 'auto') {
                console.log('🔮 AUTO modunda sistem teması kontrol ediliyor...');
                const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                console.log('🖥️ Sistem dark mode:', prefersDarkMode);
                
                const body = document.body;
                const oldBodyTheme = body.getAttribute('data-bs-theme');
                const oldBodyClasses = body.classList.toString();
                
                console.log('📄 Body - eski data-bs-theme:', oldBodyTheme);
                console.log('📄 Body - eski classList:', oldBodyClasses);
                
                body.setAttribute('data-bs-theme', prefersDarkMode ? 'dark' : 'light');
                body.classList.remove(prefersDarkMode ? 'light' : 'dark');
                body.classList.add(prefersDarkMode ? 'dark' : 'light');
                
                console.log('📄 Body - yeni data-bs-theme:', body.getAttribute('data-bs-theme'));
                console.log('📄 Body - yeni classList:', body.classList.toString());
                
                // Sistem teması değiştiğinde tema durumunu güncelle
                console.log('🔄 AUTO modunda updateThemeState çağrılıyor...');
                updateThemeState();
            } else {
                console.log('⏭️ AUTO mod değil, sistem teması kontrolü atlandı');
            }
        }
        
        // Başlangıçta tema durumunu ayarla ve sistem temasını kontrol et
        updateThemeState();
        checkSystemTheme();
        
        // Debounce için değişken
        let debounceTimer = null;
        
        // Theme switch'e manuel click handler ekle (change yerine)
        themeSwitch.addEventListener('click', function(event) {
            // Checkbox'ın otomatik değişimini engelle
            event.preventDefault();
            
            console.log('🖱️ Theme switch click olayı!');
            console.log('📊 Click event details:', {
                currentTheme: currentTheme,
                eventType: event.type,
                isTrusted: event.isTrusted,
                timeStamp: event.timeStamp,
                checkbox_checked_before: this.checked
            });
            
            // Debounce - 300ms içinde tekrar tetiklenirse öncekini iptal et
            if (debounceTimer) {
                console.log('⏱️ Debounce: Önceki click iptal edildi');
                clearTimeout(debounceTimer);
            }
            
            debounceTimer = setTimeout(() => {
                console.log('⚡ Debounce: Click işleniyor...');
            
            // Sırayla geçiş: Açık -> Karanlık -> Sistem -> Açık ...
            const oldTheme = currentTheme;
            console.log('🎯 Tema değişim mantığı başlıyor, mevcut tema:', oldTheme);
            
            if (currentTheme === '0') {
                // Açık moddan karanlık moda geç
                console.log('☀️ → 🌙 Light → Dark geçiş');
                currentTheme = '1';
            } else if (currentTheme === '1') {
                // Karanlık moddan sistem moduna geç
                console.log('🌙 → 🔮 Dark → Auto geçiş');
                currentTheme = 'auto';
            } else {
                // Sistem modundan açık moda geç
                console.log('🔮 → ☀️ Auto → Light geçiş');
                currentTheme = '0';
            }
            
            console.log('🔄 Tema değişimi tamamlandı:', {
                from: oldTheme,
                to: currentTheme,
                sequence: oldTheme === '0' ? 'Light→Dark' : 
                         oldTheme === '1' ? 'Dark→Auto' : 'Auto→Light'
            });
            
            // Cookie'yi ayarla
            document.cookie = `dark=${currentTheme};path=/;max-age=31536000`;
            console.log('🍪 Cookie ayarlandı:', `dark=${currentTheme}`);
            
            // Tema sınıflarını güncelle
            console.log('📄 Body tema sınıfları güncelleniyor...');
            const body = document.body;
            const oldBodyTheme = body.getAttribute('data-bs-theme');
            const oldBodyClasses = body.classList.toString();
            
            console.log('📄 Body - önceki durumu:', {
                dataTheme: oldBodyTheme,
                classList: oldBodyClasses
            });
            
            if (currentTheme === '1') {
                console.log('🌙 Body DARK tema uygulanıyor...');
                body.setAttribute('data-bs-theme', 'dark');
                body.classList.remove('light');
                body.classList.add('dark');
            } else if (currentTheme === '0') {
                console.log('☀️ Body LIGHT tema uygulanıyor...');
                body.setAttribute('data-bs-theme', 'light');
                body.classList.remove('dark');
                body.classList.add('light');
            } else if (currentTheme === 'auto') {
                console.log('🔮 Body AUTO tema uygulanıyor...');
                const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                console.log('🖥️ Sistem dark mode tercihi:', prefersDarkMode);
                body.setAttribute('data-bs-theme', prefersDarkMode ? 'dark' : 'light');
                body.classList.remove(prefersDarkMode ? 'light' : 'dark');
                body.classList.add(prefersDarkMode ? 'dark' : 'light');
            }
            
            console.log('📄 Body - yeni durumu:', {
                dataTheme: body.getAttribute('data-bs-theme'),
                classList: body.classList.toString()
            });
            
            // Tema durumunu güncelle
            console.log('🔄 Click sonrası updateThemeState çağrılıyor...');
            updateThemeState();
            
            // Primary rengi koru
            const currentColor = getCookie('siteColor') || '#066fd1';
            const primaryPalette = generatePrimaryPalette(currentColor);
            applyPrimaryPalette(primaryPalette);
            
            // CSS değişkenlerini güncelle
            forceUpdateThemeVariables();
            
            // Toast bildirimi devre dışı - Livewire handle ediyor
            // const themeName = currentTheme === '1' ? 'Karanlık' : 
            //                 currentTheme === '0' ? 'Açık' : 'Sistem';
            // showThemeToast(`Tema: ${themeName} modu`);
            
            }, 300); // 300ms debounce süresi
        });
        
        // Sistem teması değiştiğinde otomatik güncelleme
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
            checkSystemTheme();
            
            // Primary rengi koru
            const currentColor = getCookie('siteColor') || '#066fd1';
            const primaryPalette = generatePrimaryPalette(currentColor);
            applyPrimaryPalette(primaryPalette);
        });
        
        // İnitialize flag'i ayarla
        window.themeSwitch_initialized = true;
        console.log('✅ initThemeSwitch tamamlandı ve flag ayarlandı');
    }

    // Yardımcı Fonksiyonlar
    
    // Color name'leri HEX'e dönüştürme
    function colorNameToHex(colorName) {
        // Null/undefined kontrolü
        if (!colorName) {
            console.warn('Renk adı boş, varsayılan blue kullanılıyor');
            return '#066fd1';
        }
        
        // Eğer zaten hex formatında ise direkt döndür
        if (typeof colorName === 'string' && colorName.startsWith('#')) {
            return colorName;
        }
        
        const colorMap = {
            'blue': '#066fd1',
            'azure': '#4299e1', 
            'indigo': '#4263eb',
            'purple': '#8951e0',
            'pink': '#d14986',
            'red': '#dc3545',
            'orange': '#fd7e14',
            'yellow': '#ffc107',
            'lime': '#32cd32',
            'green': '#28a745',
            'teal': '#20c997',
            'cyan': '#17a2b8'
        };
        
        const result = colorMap[colorName.toLowerCase()];
        if (!result) {
            console.warn('Bilinmeyen renk adı:', colorName, '- orijinal değer döndürülüyor');
            // Eğer geçerli hex değeri ise direkt döndür, değilse varsayılan renk
            return (colorName.startsWith('#') && colorName.length === 7) ? colorName : '#066fd1';
        }
        
        return result;
    }

    // Primary renk paleti oluşturma - Tabler.io uyumlu
    function generatePrimaryPalette(primaryColor) {
        console.log('🎨 generatePrimaryPalette - gelen renk:', primaryColor);
        
        // Color name'i hex'e dönüştür
        const hexColor = colorNameToHex(primaryColor);
        console.log('🎨 generatePrimaryPalette - dönüştürülen hex:', hexColor);
        
        // Geçerli hex kontrolü
        if (!hexColor || !hexColor.startsWith('#') || hexColor.length !== 7) {
            console.warn('Geçersiz hex rengi generatePrimaryPalette\'te:', hexColor, '- varsayılan renk kullanılıyor');
            const fallbackColor = '#066fd1';
            const hex = fallbackColor.replace('#', '');
            const r = parseInt(hex.substring(0, 2), 16);
            const g = parseInt(hex.substring(2, 4), 16);
            const b = parseInt(hex.substring(4, 6), 16);
            console.log('🎨 generatePrimaryPalette - Fallback RGB:', {r, g, b});
            const [h, s, l] = rgbToHsl(r, g, b);
            return generatePaletteFromHSL(h, s, l, fallbackColor);
        }
        
        // HEX rengini RGB'ye dönüştür
        const hex = hexColor.replace('#', '');
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);
        
        // NaN kontrolü
        if (isNaN(r) || isNaN(g) || isNaN(b)) {
            console.warn('RGB dönüşümünde NaN değeri generatePrimaryPalette\'te:', {hex, r, g, b}, '- varsayılan renk kullanılıyor');
            const fallbackColor = '#066fd1';
            const hexFallback = fallbackColor.replace('#', '');
            const rFallback = parseInt(hexFallback.substring(0, 2), 16);
            const gFallback = parseInt(hexFallback.substring(2, 4), 16);
            const bFallback = parseInt(hexFallback.substring(4, 6), 16);
            const [h, s, l] = rgbToHsl(rFallback, gFallback, bFallback);
            return generatePaletteFromHSL(h, s, l, fallbackColor);
        }
        
        console.log('🎨 generatePrimaryPalette - RGB:', {r, g, b});
        
        // HSL'e dönüştür
        const [h, s, l] = rgbToHsl(r, g, b);
        
        return generatePaletteFromHSL(h, s, l, hexColor);
    }
    
    // Palet oluşturma yardımcı fonksiyonu
    function generatePaletteFromHSL(h, s, l, primaryColor) {
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
        
        console.log('🎨 generatePrimaryPalette - final palette:', palette);
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
        // Eğer hex renk adı string'i ise önce hex'e dönüştür
        if (!hex.startsWith('#')) {
            hex = colorNameToHex(hex);
        }
        
        // Geçersiz hex değerini kontrol et
        if (!hex || hex.length !== 7 || !hex.startsWith('#')) {
            console.warn('Geçersiz hex değeri:', hex, '- varsayılan blue rengi kullanılıyor');
            hex = '#066fd1'; // Varsayılan blue rengi
        }
        
        const r = parseInt(hex.substring(1, 3), 16);
        const g = parseInt(hex.substring(3, 5), 16);
        const b = parseInt(hex.substring(5, 7), 16);
        
        // NaN kontrolü ekle
        if (isNaN(r) || isNaN(g) || isNaN(b)) {
            console.warn('RGB dönüşümünde NaN değeri:', {hex, r, g, b}, '- varsayılan değerler kullanılıyor');
            return '6, 111, 209'; // Varsayılan blue RGB değeri
        }
        
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
    
    // Radius'u anında uygulama fonksiyonu (güçlü ve kapsamlı)
    function applyRadiusInstantly(radius) {
        const root = document.documentElement;
        const body = document.body;
        
        // Ana CSS değişkenlerini güncelle - !important ile zorla
        root.style.setProperty('--tblr-border-radius', radius, 'important');
        root.style.setProperty('--tblr-border-radius-default', radius, 'important');
        root.style.setProperty('--tblr-card-border-radius', radius, 'important');
        root.style.setProperty('--tblr-btn-border-radius', radius, 'important');
        root.style.setProperty('--card-border-radius', radius, 'important');
        root.style.setProperty('--border-radius', radius, 'important');
        root.style.setProperty('--btn-border-radius', radius, 'important');
        
        // Body'de de değişkenleri ayarla
        body.style.setProperty('--tblr-border-radius', radius, 'important');
        body.style.setProperty('--tblr-card-border-radius', radius, 'important');
        
        // Tüm elementleri anında güncelle
        updateAllRadiusElements(radius);
        
        // Örnekleri güncelle
        updateRadiusExamples();
    }
    
    // Tüm radius elementlerini güncelleme fonksiyonu
    function updateAllRadiusElements(radius) {
        // Card'lar - özel radius hesaplama
        const cardContainers = document.querySelectorAll('.card');
        cardContainers.forEach(card => {
            card.style.setProperty('border-radius', radius, 'important');
        });
        
        const cardHeaders = document.querySelectorAll('.card-header');
        cardHeaders.forEach(header => {
            // Card header - sadece üst köşeler yuvarlanacak
            header.style.setProperty('border-top-left-radius', radius, 'important');
            header.style.setProperty('border-top-right-radius', radius, 'important');
            header.style.setProperty('border-bottom-left-radius', '0', 'important');
            header.style.setProperty('border-bottom-right-radius', '0', 'important');
        });
        
        const cardBodies = document.querySelectorAll('.card-body');
        cardBodies.forEach(body => {
            // Card body - tabanlı header varsa sadece alt köşeler, yoksa üst köşeler de
            const hasHeader = body.previousElementSibling && body.previousElementSibling.classList.contains('card-header');
            const hasFooter = body.nextElementSibling && body.nextElementSibling.classList.contains('card-footer');
            
            if (hasHeader && hasFooter) {
                // Ortada - hiç radius yok
                body.style.setProperty('border-radius', '0', 'important');
            } else if (hasHeader) {
                // Header var - sadece alt köşeler
                body.style.setProperty('border-top-left-radius', '0', 'important');
                body.style.setProperty('border-top-right-radius', '0', 'important');
                body.style.setProperty('border-bottom-left-radius', radius, 'important');
                body.style.setProperty('border-bottom-right-radius', radius, 'important');
            } else if (hasFooter) {
                // Footer var - sadece üst köşeler
                body.style.setProperty('border-top-left-radius', radius, 'important');
                body.style.setProperty('border-top-right-radius', radius, 'important');
                body.style.setProperty('border-bottom-left-radius', '0', 'important');
                body.style.setProperty('border-bottom-right-radius', '0', 'important');
            } else {
                // Tek başına - tüm köşeler
                body.style.setProperty('border-radius', radius, 'important');
            }
        });
        
        const cardFooters = document.querySelectorAll('.card-footer');
        cardFooters.forEach(footer => {
            // Card footer - sadece alt köşeler yuvarlanacak
            footer.style.setProperty('border-top-left-radius', '0', 'important');
            footer.style.setProperty('border-top-right-radius', '0', 'important');
            footer.style.setProperty('border-bottom-left-radius', radius, 'important');
            footer.style.setProperty('border-bottom-right-radius', radius, 'important');
        });
        
        // Butonlar
        const buttons = document.querySelectorAll('.btn:not(.btn-pill):not(.btn-square)');
        buttons.forEach(button => {
            button.style.setProperty('border-radius', radius, 'important');
        });
        
        // Form elementleri - card içindeki form'lar için özel radius hesaplama
        const formElements = document.querySelectorAll('.form-control, .form-select, .form-check-input');
        formElements.forEach(element => {
            // Card body içindeyse daha küçük radius kullan
            const isInCardBody = element.closest('.card-body');
            const isInModal = element.closest('.modal-body');
            
            let elementRadius = radius;
            
            // İç içe elementler için radius'u azalt
            if (isInCardBody || isInModal) {
                // Ana radius'tan daha küçük bir değer hesapla
                const radiusValue = parseFloat(radius);
                if (radius.includes('rem')) {
                    elementRadius = Math.max(0.25, radiusValue * 0.6) + 'rem';
                } else if (radius === '0') {
                    elementRadius = '0';
                } else {
                    elementRadius = Math.max(4, parseInt(radius) * 0.6) + 'px';
                }
            }
            
            element.style.setProperty('border-radius', elementRadius, 'important');
        });
        
        // Modal'lar
        const modals = document.querySelectorAll('.modal-content, .modal-header, .modal-body, .modal-footer');
        modals.forEach(modal => {
            modal.style.setProperty('border-radius', radius, 'important');
        });
        
        // Dropdown'lar
        const dropdowns = document.querySelectorAll('.dropdown-menu, .dropdown-item');
        dropdowns.forEach(dropdown => {
            dropdown.style.setProperty('border-radius', radius, 'important');
        });
        
        // Offcanvas
        const offcanvas = document.querySelectorAll('.offcanvas, .offcanvas-header, .offcanvas-body');
        offcanvas.forEach(element => {
            element.style.setProperty('border-radius', radius, 'important');
        });
        
        // Alert'ler
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.setProperty('border-radius', radius, 'important');
        });
        
        // Badge'ler
        const badges = document.querySelectorAll('.badge:not(.badge-pill)');
        badges.forEach(badge => {
            badge.style.setProperty('border-radius', radius, 'important');
        });
        
        // Table'lar
        const tables = document.querySelectorAll('.table, .table-responsive');
        tables.forEach(table => {
            table.style.setProperty('border-radius', radius, 'important');
        });
        
        // Progress bar'lar
        const progressBars = document.querySelectorAll('.progress, .progress-bar');
        progressBars.forEach(progressBar => {
            progressBar.style.setProperty('border-radius', radius, 'important');
        });
        
        // List group'lar
        const listGroups = document.querySelectorAll('.list-group, .list-group-item');
        listGroups.forEach(listGroup => {
            listGroup.style.setProperty('border-radius', radius, 'important');
        });
        
        // Navigation'lar - özel radius kuralları
        const navPills = document.querySelectorAll('.nav-pills .nav-link');
        navPills.forEach(nav => {
            nav.style.setProperty('border-radius', radius, 'important');
        });
        
        // Nav tabs container (ul elementi) - sadece üst köşeler yuvarlanacak
        const navTabsContainers = document.querySelectorAll('.nav-tabs, .card-header-tabs, ul.nav-tabs, ul.card-header-tabs');
        navTabsContainers.forEach(container => {
            container.style.setProperty('border-top-left-radius', radius, 'important');
            container.style.setProperty('border-top-right-radius', radius, 'important');
            container.style.setProperty('border-bottom-left-radius', '0', 'important');
            container.style.setProperty('border-bottom-right-radius', '0', 'important');
            container.style.setProperty('overflow', 'hidden', 'important');
        });
        
        // Nav tabs - sadece üst köşeler yuvarlanacak
        const navTabs = document.querySelectorAll('.nav-tabs .nav-link, .card-header-tabs .nav-link');
        navTabs.forEach(nav => {
            nav.style.setProperty('border-top-left-radius', radius, 'important');
            nav.style.setProperty('border-top-right-radius', radius, 'important');
            nav.style.setProperty('border-bottom-left-radius', '0', 'important');
            nav.style.setProperty('border-bottom-right-radius', '0', 'important');
        });
        
        // Language switch butonları da sadece üst köşeler
        const languageSwitchBtns = document.querySelectorAll('.language-switch-btn');
        languageSwitchBtns.forEach(btn => {
            btn.style.setProperty('border-top-left-radius', radius, 'important');
            btn.style.setProperty('border-top-right-radius', radius, 'important');
            btn.style.setProperty('border-bottom-left-radius', '0', 'important');
            btn.style.setProperty('border-bottom-right-radius', '0', 'important');
        });
        
        // Studio edit butonu için özel tam radius (bg-primary olan nav-link'ler)
        const studioBtns = document.querySelectorAll('.nav-tabs .nav-link.bg-primary, .card-header-tabs .nav-link.bg-primary');
        studioBtns.forEach(btn => {
            btn.style.setProperty('border-radius', radius, 'important');
            btn.style.setProperty('border-top-left-radius', radius, 'important');
            btn.style.setProperty('border-top-right-radius', radius, 'important');
            btn.style.setProperty('border-bottom-left-radius', radius, 'important');
            btn.style.setProperty('border-bottom-right-radius', radius, 'important');
        });
        
        // Input group'lar
        const inputGroups = document.querySelectorAll('.input-group, .input-group-text');
        inputGroups.forEach(inputGroup => {
            inputGroup.style.setProperty('border-radius', radius, 'important');
        });
        
        // Avatar'lar (square olmayan)
        const avatars = document.querySelectorAll('.avatar:not(.avatar-rounded)');
        avatars.forEach(avatar => {
            avatar.style.setProperty('border-radius', radius, 'important');
        });
        
        // Custom elements
        const customElements = document.querySelectorAll('.border-radius, .rounded, .rounded-1, .rounded-2, .rounded-3');
        customElements.forEach(element => {
            element.style.setProperty('border-radius', radius, 'important');
        });
        
        // TinyMCE ve editör alanları için özel radius
        const tinyMCEElements = document.querySelectorAll('.tox-tinymce, .tox-editor-container, .tox-editor-header, .mce-tinymce, .mce-container, .editor-container');
        tinyMCEElements.forEach(element => {
            const radiusValue = parseFloat(radius);
            let editorRadius = radius;
            
            if (radius.includes('rem')) {
                editorRadius = Math.max(0.25, radiusValue * 0.5) + 'rem';
            } else if (radius === '0') {
                editorRadius = '0';
            } else {
                editorRadius = Math.max(4, parseInt(radius) * 0.5) + 'px';
            }
            
            element.style.setProperty('border-radius', editorRadius, 'important');
            element.style.setProperty('overflow', 'hidden', 'important');
        });
        
        // Form floating container'ları için özel radius
        const formFloatingElements = document.querySelectorAll('.form-floating');
        formFloatingElements.forEach(element => {
            const radiusValue = parseFloat(radius);
            let floatingRadius = radius;
            
            if (radius.includes('rem')) {
                floatingRadius = Math.max(0.25, radiusValue * 0.6) + 'rem';
            } else if (radius === '0') {
                floatingRadius = '0';
            } else {
                floatingRadius = Math.max(4, parseInt(radius) * 0.6) + 'px';
            }
            
            element.style.setProperty('border-radius', floatingRadius, 'important');
            element.style.setProperty('overflow', 'hidden', 'important');
        });
        
        // Language content ve tab pane'ler için özel radius
        const languageContents = document.querySelectorAll('.language-content, .tab-pane');
        languageContents.forEach(element => {
            element.style.setProperty('border-radius', '0', 'important');
            element.style.setProperty('overflow', 'hidden', 'important');
        });
        
        // Language content içindeki specific form floating alanları
        const languageFormFloating = document.querySelectorAll('.language-content .form-floating, .language-content .form-floating.mb-3, .tab-pane .language-content .form-floating');
        languageFormFloating.forEach(element => {
            const radiusValue = parseFloat(radius);
            let formRadius = radius;
            
            if (radius.includes('rem')) {
                formRadius = Math.max(0.25, radiusValue * 0.6) + 'rem';
            } else if (radius === '0') {
                formRadius = '0';
            } else {
                formRadius = Math.max(4, parseInt(radius) * 0.6) + 'px';
            }
            
            element.style.setProperty('border-radius', formRadius, 'important');
            element.style.setProperty('overflow', 'hidden', 'important');
        });
        
        // Language content içindeki form control'ler
        const languageFormControls = document.querySelectorAll('.language-content .form-control, .language-content .form-select, .language-content textarea.form-control');
        languageFormControls.forEach(element => {
            const radiusValue = parseFloat(radius);
            let controlRadius = radius;
            
            if (radius.includes('rem')) {
                controlRadius = Math.max(0.25, radiusValue * 0.6) + 'rem';
            } else if (radius === '0') {
                controlRadius = '0';
            } else {
                controlRadius = Math.max(4, parseInt(radius) * 0.6) + 'px';
            }
            
            element.style.setProperty('border-radius', controlRadius, 'important');
        });
        
        // Theme builder örnekleri
        const radiusExamples = document.querySelectorAll('.radius-example');
        radiusExamples.forEach(example => {
            if (example.classList.contains('active')) {
                example.style.setProperty('border-radius', radius, 'important');
                example.style.setProperty('background-color', 'var(--tblr-primary)', 'important');
            }
        });
        
        // Force reflow tüm elementler için
        document.body.offsetHeight;
        
        // CSS animasyonları için kısa delay
        setTimeout(() => {
            // Transition'ları geri aç
            const transitionCards = document.querySelectorAll('.card');
            const transitionButtons = document.querySelectorAll('.btn');
            
            transitionCards.forEach(card => {
                card.style.transition = 'border-radius 0.15s ease';
            });
            transitionButtons.forEach(button => {
                button.style.transition = 'border-radius 0.15s ease';
            });
        }, 50);
    }
    
    // Card body elementlerinin radius değerlerini güncelle (eski fonksiyon - geriye uyumluluk)
    function updateCardBodyRadiuses(radius) {
        // Yeni fonksiyonu çağır
        updateAllRadiusElements(radius);
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

    // Debug fonksiyonu - renk dönüşümü hatalarını yakala
    function debugColorConversion(colorName, step, data) {
        console.group(`🔍 Debug: ${colorName} rengi - ${step}`);
        console.log('Input:', colorName);
        console.log('Data:', data);
        console.groupEnd();
    }

    // Başlangıç durumunu ayarla
    function initializeThemeSettings() {
        // Mevcut tema rengi için primary paleti ve metin rengini güncelle
        const currentColor = getCookie('siteColor') || '#066fd1';
        debugColorConversion(currentColor, 'Başlangıç', {currentColor});
        
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
        // initColorPickers() kaldırıldı - real-time system kullanıyor
        
        // Mevcut seçili rengi zorla uygula
        const selectedColorRadio = document.querySelector('input[name="theme-primary"]:checked');
        if (selectedColorRadio) {
            const color = selectedColorRadio.value;
            const primaryPalette = generatePrimaryPalette(color);
            applyPrimaryPalette(primaryPalette);
            updateTextColor(color);
        }
        
        // Theme builder için ekstra event listener'lar
        initThemeBuilderEvents();
    });
    
    // Theme Builder için anında yansıma event'leri
    function initThemeBuilderEvents() {
        console.log('🏗️ initThemeBuilderEvents BAŞLADI');
        
        // Theme builder açıldığında tüm form elementlerini dinle
        const offcanvasTheme = document.getElementById('offcanvasTheme');
        console.log('🏗️ OffcanvasTheme elementi:', offcanvasTheme ? 'bulundu' : 'bulunamadı');
        
        if (offcanvasTheme) {
            console.log('🏗️ Offcanvas event listener ekleniyor...');
            
            offcanvasTheme.addEventListener('shown.bs.offcanvas', function() {
                console.log('🏗️ OFFCANVAS AÇILDI! Theme Builder aktif');
                
                // Offcanvas açıldığında tüm form elementlerini tekrar dinle
                console.log('🏗️ Real-time listeners attach ediliyor...');
                attachRealTimeListeners();
                
                // Mevcut değerleri form'a yansıt
                console.log('🏗️ Theme Builder form güncelleniyor...');
                updateThemeBuilderForm();
                
                console.log('✅ Theme Builder hazır!');
            });
        } else {
            console.warn('⚠️ OffcanvasTheme elementi bulunamadı!');
        }
        
        console.log('✅ initThemeBuilderEvents tamamlandı');
    }
    
    // Real-time listener'ları attach et
    function attachRealTimeListeners() {
        console.log('🔗 attachRealTimeListeners BAŞLADI');
        
        // Tüm tema değişikliklerini real-time dinle
        
        // Ana renk değişiklikleri
        const primaryInputs = document.querySelectorAll('input[name="theme-primary"]');
        console.log('🔗 Primary color inputs bulundu:', primaryInputs.length);
        
        primaryInputs.forEach((input, index) => {
            if (!input.hasAttribute('data-realtime-attached')) {
                console.log(`🔗 Real-time listener ekleniyor primary ${index + 1}:`, input.value);
                input.setAttribute('data-realtime-attached', 'true');
                
                input.addEventListener('change', function() {
                    console.log('🔗 REAL-TIME PRIMARY COLOR CHANGE!', this.value);
                    
                    const color = this.value;
                    debugColorConversion(color, 'Real-time değişim', {
                        inputValue: this.value,
                        colorType: typeof color,
                        isHex: color.startsWith('#'),
                        length: color.length
                    });
                    
                    document.cookie = `siteColor=${color};path=/;max-age=31536000`;
                    console.log('🍪 Real-time color cookie:', `siteColor=${color}`);
                    
                    const primaryPalette = generatePrimaryPalette(color);
                    console.log('🔗 Real-time palette generated:', primaryPalette);
                    
                    applyPrimaryPalette(primaryPalette);
                    updateTextColor(color);
                    updateRadiusExamples();
                    
                    // Toast bildirimi devre dışı - Livewire handle ediyor
                    // showThemeToast('Ana renk güncellendi');
                    console.log('✅ Real-time primary color change tamamlandı');
                });
            } else {
                console.log(`⏭️ Primary ${index + 1} zaten real-time listener'a sahip, atlanıyor`);
            }
        });
        
        // Tema modu değişiklikleri
        document.querySelectorAll('input[name="theme"]').forEach(input => {
            if (!input.hasAttribute('data-realtime-attached')) {
                input.setAttribute('data-realtime-attached', 'true');
                input.addEventListener('change', function() {
                    const themeMode = this.value;
                    document.cookie = `dark=${themeMode === 'dark' ? '1' : (themeMode === 'auto' ? 'auto' : '0')};path=/;max-age=31536000`;
                    
                    applyThemeMode(themeMode);
                    updateNavbarThemeSwitch(themeMode);
                    
                    // showThemeToast('Tema modu güncellendi');
                });
            }
        });
        
        // Font boyutu değişiklikleri
        document.querySelectorAll('input[name="theme-font-size"]').forEach(input => {
            if (!input.hasAttribute('data-realtime-attached')) {
                input.setAttribute('data-realtime-attached', 'true');
                input.addEventListener('change', function() {
                    const fontSize = this.value;
                    document.cookie = `themeFontSize=${fontSize};path=/;max-age=31536000`;
                    
                    document.body.classList.remove('font-size-small', 'font-size-normal', 'font-size-large');
                    document.body.classList.add(`font-size-${fontSize}`);
                    updateFontSizes(fontSize);
                    
                    // showThemeToast('Font boyutu güncellendi');
                });
            }
        });
        
        // Font ailesi değişiklikleri
        document.querySelectorAll('input[name="theme-font"]').forEach(input => {
            if (!input.hasAttribute('data-realtime-attached')) {
                input.setAttribute('data-realtime-attached', 'true');
                input.addEventListener('change', function() {
                    const font = this.value;
                    document.cookie = `themeFont=${encodeURIComponent(font)};path=/;max-age=31536000`;
                    
                    document.documentElement.style.setProperty('--tblr-font-family', font);
                    document.body.style.fontFamily = font;
                    
                    if (font.includes('Roboto')) {
                        ensureGoogleFont('roboto-font', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
                    } else if (font.includes('Poppins')) {
                        ensureGoogleFont('poppins-font', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap');
                    }
                    
                    // showThemeToast('Font ailesi güncellendi');
                });
            }
        });
        
        // Gri ton değişiklikleri
        document.querySelectorAll('input[name="theme-base"]').forEach(input => {
            if (!input.hasAttribute('data-realtime-attached')) {
                input.setAttribute('data-realtime-attached', 'true');
                input.addEventListener('change', function() {
                    const baseTheme = this.value;
                    document.cookie = `themeBase=${baseTheme};path=/;max-age=31536000`;
                    
                    applyBaseTheme(baseTheme);
                    
                    // showThemeToast('Renk teması güncellendi');
                });
            }
        });
        
        // Tablo görünümü değişiklikleri
        document.querySelectorAll('input[name="table-compact"]').forEach(input => {
            if (!input.hasAttribute('data-realtime-attached')) {
                input.setAttribute('data-realtime-attached', 'true');
                input.addEventListener('change', function() {
                    const isCompact = this.value === '1';
                    document.cookie = `tableCompact=${isCompact ? '1' : '0'};path=/;max-age=31536000`;
                    
                    if (isCompact) {
                        document.body.classList.add('table-compact');
                    } else {
                        document.body.classList.remove('table-compact');
                    }
                    
                    // showThemeToast('Tablo görünümü güncellendi');
                });
            }
        });
        
        // Radius slider real-time değişimi
        const radiusSlider = document.getElementById('radius-slider');
        if (radiusSlider && !radiusSlider.hasAttribute('data-realtime-attached')) {
            radiusSlider.setAttribute('data-realtime-attached', 'true');
            
            radiusSlider.addEventListener('input', function() {
                const radiusMap = ['0', '0.25rem', '0.375rem', '0.5rem', '0.75rem', '1rem'];
                const selectedIndex = parseInt(this.value);
                const selectedRadius = radiusMap[selectedIndex];
                
                document.cookie = `themeRadius=${selectedRadius};path=/;max-age=31536000`;
                applyRadiusInstantly(selectedRadius);
                
                // Radius örneklerini güncelle
                document.querySelectorAll('.radius-example').forEach((example, index) => {
                    if (index === selectedIndex) {
                        example.classList.add('active');
                    } else {
                        example.classList.remove('active');
                    }
                });
                
                // showThemeToast('Köşe yuvarlaklığı güncellendi');
            });
        }
        
        // Radius örneklerine tıklama
        document.querySelectorAll('.radius-example').forEach((example) => {
            if (!example.hasAttribute('data-realtime-attached')) {
                example.setAttribute('data-realtime-attached', 'true');
                
                example.addEventListener('click', function() {
                    const selectedIndex = this.getAttribute('data-radius');
                    const radiusSlider = document.getElementById('radius-slider');
                    
                    if (radiusSlider) {
                        radiusSlider.value = selectedIndex;
                        const event = new Event('input');
                        radiusSlider.dispatchEvent(event);
                    }
                });
            }
        });
        
        // Specific form alanları için ek real-time güncellemeler
        const specificFormElements = document.querySelectorAll('.language-content .form-floating, .language-content .form-control');
        specificFormElements.forEach((element) => {
            if (!element.hasAttribute('data-radius-attached')) {
                element.setAttribute('data-radius-attached', 'true');
                
                // Element'in mevcut radius değerini gözlemle
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                            // Theme builder değişikliği tespit edilirse anında güncelle
                            const currentRadius = getCookie('themeRadius') || '0.375rem';
                            if (currentRadius) {
                                applyRadiusInstantly(currentRadius);
                            }
                        }
                    });
                });
                
                observer.observe(element, {
                    attributes: true,
                    attributeFilter: ['style']
                });
            }
        });
    }
    
    // Tema modunu uygula
    function applyThemeMode(themeMode) {
        const body = document.body;
        
        if (themeMode === 'dark') {
            body.setAttribute('data-bs-theme', 'dark');
            body.classList.remove('light');
            body.classList.add('dark');
        } else if (themeMode === 'light') {
            body.setAttribute('data-bs-theme', 'light');
            body.classList.remove('dark');
            body.classList.add('light');
        } else if (themeMode === 'auto') {
            const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
            body.setAttribute('data-bs-theme', prefersDarkMode ? 'dark' : 'light');
            body.classList.remove(prefersDarkMode ? 'light' : 'dark');
            body.classList.add(prefersDarkMode ? 'dark' : 'light');
        }
        
        // Primary rengi koru
        const currentColor = getCookie('siteColor') || '#066fd1';
        const primaryPalette = generatePrimaryPalette(currentColor);
        applyPrimaryPalette(primaryPalette);
        
        forceUpdateThemeVariables();
        setTimeout(() => forceUpdateAllElements(), 50);
    }
    
    // Navbar tema switch'ini güncelle
    function updateNavbarThemeSwitch(themeMode) {
        const themeContainer = document.querySelector('.theme-mode');
        const themeSwitch = document.getElementById('switch');
        
        if (themeContainer && themeSwitch) {
            if (themeMode === 'auto') {
                themeContainer.setAttribute('data-theme', 'auto');
                themeSwitch.checked = false;
            } else if (themeMode === 'dark') {
                themeContainer.setAttribute('data-theme', 'dark');
                themeSwitch.checked = true;
            } else {
                themeContainer.setAttribute('data-theme', 'light');
                themeSwitch.checked = false;
            }
        }
    }
    
    // Base theme uygula
    function applyBaseTheme(baseTheme) {
        document.body.classList.remove(
            'theme-base-slate', 'theme-base-cool', 'theme-base-neutral', 
            'theme-base-warm', 'theme-base-indigo', 'theme-base-azure', 
            'theme-base-primary', 'theme-base-secondary', 'theme-base-tertiary', 
            'theme-base-error', 'theme-base-neutral-variant', 'theme-base-mavi-gri', 
            'theme-base-cinko-gri', 'theme-base-tas-rengi', 'theme-base-stone', 'theme-base-zinc'
        );
        
        if (baseTheme === 'slate') {
            document.body.classList.add('theme-base-mavi-gri');
        } else if (baseTheme === 'zinc') {
            document.body.classList.add('theme-base-cinko-gri');
        } else if (baseTheme === 'stone') {
            document.body.classList.add('theme-base-tas-rengi');
        } else {
            document.body.classList.add(`theme-base-${baseTheme}`);
        }
        
        forceUpdateThemeVariables();
        setTimeout(() => forceUpdateAllElements(), 50);
    }
    
    // Theme builder form'unu güncelle
    function updateThemeBuilderForm() {
        // Mevcut tema ayarlarını form'a yansıt
        const currentTheme = getCookie('dark') || 'auto';
        const currentColor = getCookie('siteColor') || '#066fd1';
        const currentFont = getCookie('themeFont') || "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif";
        const currentFontSize = getCookie('themeFontSize') || 'small';
        const currentBase = getCookie('themeBase') || 'neutral';
        const currentRadius = getCookie('themeRadius') || '0.375rem';
        const currentTableCompact = getCookie('tableCompact') || '0';
        
        // Tema modu radio'larını güncelle
        let themeValue = 'auto';
        if (currentTheme === '1') themeValue = 'dark';
        else if (currentTheme === '0') themeValue = 'light';
        
        const themeRadio = document.querySelector(`input[name="theme"][value="${themeValue}"]`);
        if (themeRadio) themeRadio.checked = true;
        
        // Ana renk radio'larını güncelle
        const colorRadio = document.querySelector(`input[name="theme-primary"][value="${currentColor}"]`);
        if (colorRadio) colorRadio.checked = true;
        
        // Font radio'larını güncelle
        const fontRadio = document.querySelector(`input[name="theme-font"][value="${currentFont}"]`);
        if (fontRadio) fontRadio.checked = true;
        
        // Font boyutu radio'larını güncelle
        const fontSizeRadio = document.querySelector(`input[name="theme-font-size"][value="${currentFontSize}"]`);
        if (fontSizeRadio) fontSizeRadio.checked = true;
        
        // Base theme radio'larını güncelle
        const baseRadio = document.querySelector(`input[name="theme-base"][value="${currentBase}"]`);
        if (baseRadio) baseRadio.checked = true;
        
        // Tablo görünümü radio'larını güncelle
        const tableRadio = document.querySelector(`input[name="table-compact"][value="${currentTableCompact}"]`);
        if (tableRadio) tableRadio.checked = true;
        
        // Radius slider'ını güncelle
        const radiusSlider = document.getElementById('radius-slider');
        if (radiusSlider) {
            const radiusMap = ['0', '0.25rem', '0.375rem', '0.5rem', '0.75rem', '1rem'];
            const radiusIndex = radiusMap.indexOf(currentRadius);
            if (radiusIndex !== -1) {
                radiusSlider.value = radiusIndex;
                
                // Radius örneklerini güncelle
                document.querySelectorAll('.radius-example').forEach((example, index) => {
                    if (index === radiusIndex) {
                        example.classList.add('active');
                    } else {
                        example.classList.remove('active');
                    }
                });
            }
        }
    }
    
    // Theme toast bildirimi - sistem toast'ını kullan
    function showThemeToast(message) {
        // Sistem toast.js dosyasındaki showToast fonksiyonunu kullan
        if (typeof showToast === 'function') {
            showToast('Tema Ayarları', message, 'info');
        } else {
            // Fallback için hafif bir bildirim (DOM'da kalmasın)
            console.log('Tema değişti:', message);
        }
    }
    
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