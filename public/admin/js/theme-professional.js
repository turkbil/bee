// Tabler.io Profesyonel Tema Sistemi
document.addEventListener('DOMContentLoaded', function() {
    
    // Tema konfigürasyonu
    const themeConfig = {
        'theme': 'light',
        'theme-primary': 'blue'
    };
    
    // URL parametrelerini al
    const urlParams = new URLSearchParams(window.location.search);
    
    // Tema ayarlarını başlat
    initializeTheme();
    
    // Form event listener'ları ekle
    initializeThemeForm();
    
    function initializeTheme() {
        // Her tema ayarı için kontrol et
        for (const key in themeConfig) {
            let selectedValue;
            
            // 1. URL parametresini kontrol et
            const paramValue = urlParams.get(key);
            if (paramValue) {
                localStorage.setItem('tabler-' + key, paramValue);
                selectedValue = paramValue;
            } else {
                // 2. LocalStorage'ı kontrol et
                const storedValue = localStorage.getItem('tabler-' + key);
                selectedValue = storedValue || themeConfig[key];
            }
            
            // Data attribute'u uygula
            if (selectedValue !== themeConfig[key]) {
                document.documentElement.setAttribute('data-bs-' + key, selectedValue);
            } else {
                document.documentElement.removeAttribute('data-bs-' + key);
            }
            
            // Form elemanlarını güncelle
            updateFormElement(key, selectedValue);
        }
        
        // Eski cookie sisteminden değer oku (backward compatibility)
        const oldColor = getCookie('siteColor');
        if (oldColor && !localStorage.getItem('tabler-theme-primary')) {
            const colorName = hexToColorName(oldColor);
            if (colorName) {
                setThemeValue('theme-primary', colorName);
            }
        }
    }
    
    function initializeThemeForm() {
        // Form değişikliklerini dinle
        const form = document.querySelector('.theme-settings-form') || document.body;
        
        form.addEventListener('change', function(event) {
            const target = event.target;
            const name = target.name;
            const value = target.value;
            
            // Tema ayarlarını kontrol et
            if (name && themeConfig.hasOwnProperty(name)) {
                setThemeValue(name, value);
            }
        });
        
        // Karanlık mod switch için özel handler
        const themeRadios = document.querySelectorAll('input[name="theme"]');
        themeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                setThemeValue('theme', this.value);
                
                // Navbar tema switch'ini güncelle
                updateNavbarThemeSwitch(this.value);
            });
        });
        
        // Ana renk seçimi için özel handler
        const colorRadios = document.querySelectorAll('input[name="theme-primary"]');
        colorRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const value = this.value;
                
                // Eğer hex color ise, CSS variable'ı doğrudan set et
                if (value.startsWith('#')) {
                    setHexColor(value);
                } else {
                    // Tabler.io standard color name ise normal işlem
                    setThemeValue('theme-primary', value);
                }
                
                console.log('Primary color changed to:', value);
            });
        });
        
        // Navbar'daki tatlı tema switch'i için
        initNavbarThemeSwitch();
    }
    
    function setThemeValue(key, value) {
        // Data attribute'u ayarla
        document.documentElement.setAttribute('data-bs-' + key, value);
        
        // LocalStorage'a kaydet
        localStorage.setItem('tabler-' + key, value);
        
        // Form elemanını güncelle
        updateFormElement(key, value);
        
        // Özel işlemler
        if (key === 'theme') {
            handleThemeChange(value);
        }
        
        console.log(`Theme ${key} changed to:`, value);
    }
    
    function updateFormElement(key, value) {
        const formElement = document.querySelector(`input[name="${key}"][value="${value}"]`);
        if (formElement) {
            formElement.checked = true;
        }
    }
    
    function handleThemeChange(themeValue) {
        const body = document.body;
        
        if (themeValue === 'dark') {
            body.setAttribute('data-bs-theme', 'dark');
            body.classList.remove('light');
            body.classList.add('dark');
        } else if (themeValue === 'light') {
            body.setAttribute('data-bs-theme', 'light');
            body.classList.remove('dark');
            body.classList.add('light');
        } else if (themeValue === 'auto') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            body.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
            body.classList.remove(prefersDark ? 'light' : 'dark');
            body.classList.add(prefersDark ? 'dark' : 'light');
        }
        
        // Tema container'ı güncelle
        const themeContainer = document.querySelector('.theme-mode');
        if (themeContainer) {
            themeContainer.setAttribute('data-theme', themeValue);
        }
    }
    
    function initNavbarThemeSwitch() {
        // Eski tatlı tema switch'i entegrasyonu
        const themeSwitch = document.getElementById('switch');
        if (themeSwitch) {
            themeSwitch.addEventListener('change', function() {
                const isChecked = this.checked;
                const newTheme = isChecked ? 'dark' : 'light';
                setThemeValue('theme', newTheme);
                
                console.log('Navbar theme switch:', newTheme);
            });
            
            // Başlangıç durumunu ayarla
            const currentTheme = localStorage.getItem('tabler-theme') || 'light';
            themeSwitch.checked = (currentTheme === 'dark');
        }
    }
    
    function updateNavbarThemeSwitch(themeValue) {
        const themeSwitch = document.getElementById('switch');
        if (themeSwitch) {
            themeSwitch.checked = (themeValue === 'dark');
        }
        
        // Tema container'ını güncelle
        const themeContainer = document.querySelector('.theme-mode');
        if (themeContainer) {
            themeContainer.setAttribute('data-theme', themeValue);
        }
    }
    
    // Utility fonksiyonlar
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
    
    function setHexColor(hex) {
        // Hex rengi doğrudan CSS variable'ına set et
        const root = document.documentElement;
        
        // RGB'ye dönüştür
        const rgb = hexToRgb(hex);
        const rgbArray = rgb.split(', ').map(n => parseInt(n));
        
        // Rengin koyuluğuna göre text rengi hesapla (WCAG contrast)
        const brightness = calculateBrightness(rgbArray[0], rgbArray[1], rgbArray[2]);
        const textColor = brightness > 128 ? '#000000' : '#ffffff';
        const textColorRgb = brightness > 128 ? '0, 0, 0' : '255, 255, 255';
        
        // Light version için daha açık ton oluştur
        const lightHex = lightenColor(hex, 0.85);
        const lightRgb = hexToRgb(lightHex);
        const lightTextColor = '#000000'; // Light version her zaman koyu text
        
        // Primary color ve tüm varyantlarını set et
        root.style.setProperty('--tblr-primary', hex);
        root.style.setProperty('--tblr-primary-rgb', rgb);
        root.style.setProperty('--tblr-primary-fg', textColor);
        root.style.setProperty('--tblr-primary-lt', lightHex);
        root.style.setProperty('--tblr-primary-lt-rgb', lightRgb);
        
        // Eski uyumluluk için
        root.style.setProperty('--primary-color', hex);
        root.style.setProperty('--primary-color-rgb', rgb);
        root.style.setProperty('--primary-text-color', textColor);
        
        // ANINDA tüm primary elementleri güncelle - TAK diye
        instantUpdateAllElements(hex, textColor);
        
        // LocalStorage'a hex olarak kaydet
        localStorage.setItem('tabler-theme-primary', hex);
        
        // Eski sistem uyumluluğu için cookie'ye de yaz
        document.cookie = `siteColor=${hex};path=/;max-age=31536000`;
        
        console.log(`INSTANT color applied: ${hex}, Text color: ${textColor}`);
    }
    
    function calculateBrightness(r, g, b) {
        // WCAG brightness hesaplama
        return (r * 299 + g * 587 + b * 114) / 1000;
    }
    
    function lightenColor(hex, percent) {
        const num = parseInt(hex.replace("#", ""), 16);
        const amt = Math.round(2.55 * percent * 100);
        const R = (num >> 16) + amt;
        const G = (num >> 8 & 0x00FF) + amt;
        const B = (num & 0x0000FF) + amt;
        return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
            (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
            (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
    }
    
    function instantUpdateAllElements(primaryColor, textColor) {
        // TÜM primary elementlerini ANINDA bul ve güncelle
        const elements = document.querySelectorAll(`
            .btn-primary,
            .bg-primary,
            .text-primary,
            .border-primary,
            .badge.bg-primary,
            .alert-primary,
            .progress-bar.bg-primary,
            .nav-pills .nav-link.active,
            .pagination .page-link.active,
            .card-header.bg-primary,
            .module-card[style*="--tblr-primary"]
        `);
        
        // Her elementi ANINDA güncelle - transition yok
        elements.forEach(element => {
            if (element.classList.contains('bg-primary') || 
                element.classList.contains('btn-primary') ||
                element.classList.contains('alert-primary')) {
                element.style.backgroundColor = primaryColor;
                element.style.color = textColor;
            }
            
            if (element.classList.contains('text-primary')) {
                element.style.color = primaryColor;
            }
            
            if (element.classList.contains('border-primary')) {
                element.style.borderColor = primaryColor;
            }
            
            // Module kartları
            if (element.style.getPropertyValue('--tblr-primary')) {
                element.style.setProperty('--tblr-primary', primaryColor);
            }
        });
    }

    function applyColorToAllElements(primaryColor, textColor) {
        // TÜM primary elementlerini bul ve renklerini güncelle
        const primaryElements = document.querySelectorAll(`
            .btn-primary,
            .bg-primary,
            .text-primary,
            .border-primary,
            .badge.bg-primary,
            .alert-primary,
            .progress-bar.bg-primary,
            .nav-pills .nav-link.active,
            .pagination .page-link.active,
            .card-header.bg-primary,
            .navbar-brand,
            .theme-mode .toggle,
            [style*="primary"],
            .module-card[style*="--tblr-primary"]
        `);
        
        primaryElements.forEach(element => {
            // Background primary olan elementler
            if (element.classList.contains('bg-primary') || 
                element.classList.contains('btn-primary') ||
                element.classList.contains('alert-primary') ||
                element.style.backgroundColor) {
                element.style.setProperty('background-color', primaryColor, 'important');
                element.style.setProperty('color', textColor, 'important');
                
                // Link'ler ve child elementler için de
                const links = element.querySelectorAll('a, .text-primary, span, p, h1, h2, h3, h4, h5, h6');
                links.forEach(link => {
                    link.style.setProperty('color', textColor, 'important');
                });
            }
            
            // Text primary olan elementler
            if (element.classList.contains('text-primary')) {
                element.style.setProperty('color', primaryColor, 'important');
            }
            
            // Border primary olan elementler
            if (element.classList.contains('border-primary')) {
                element.style.setProperty('border-color', primaryColor, 'important');
            }
        });
        
        // Module kartlarındaki inline primary style'ları güncelle
        const moduleCards = document.querySelectorAll('.module-card[style*="--tblr-primary"]');
        moduleCards.forEach(card => {
            card.style.setProperty('--tblr-primary', primaryColor, 'important');
            card.style.setProperty('--tblr-primary-rgb', hexToRgb(primaryColor), 'important');
            
            // Card içindeki tüm primary renkli elementler
            const cardPrimaryElements = card.querySelectorAll('.btn-primary, .bg-primary, .text-primary, a[href]');
            cardPrimaryElements.forEach(el => {
                if (el.style.backgroundColor || el.classList.contains('bg-primary') || el.classList.contains('btn-primary')) {
                    el.style.setProperty('color', textColor, 'important');
                }
            });
        });
        
        // Progress bar'lar
        const progressBars = document.querySelectorAll('.progress-bar.bg-primary');
        progressBars.forEach(bar => {
            bar.style.setProperty('background-color', primaryColor, 'important');
        });
        
        // Hiçbir repaint yok - TAK diye değişsin
    }
    
    
    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        if (result) {
            const r = parseInt(result[1], 16);
            const g = parseInt(result[2], 16);  
            const b = parseInt(result[3], 16);
            return `${r}, ${g}, ${b}`;
        }
        return '0, 0, 0';
    }
    
    function hexToColorName(hex) {
        const colorMap = {
            '#066fd1': 'blue',
            '#4299e1': 'azure', 
            '#6366f1': 'indigo',
            '#8b5cf6': 'purple',
            '#ec4899': 'pink',
            '#dc2626': 'red',
            '#ea580c': 'orange',
            '#eab308': 'yellow',
            '#65a30d': 'lime',
            '#16a34a': 'green',
            '#0d9488': 'teal',
            '#0891b2': 'cyan'
        };
        return colorMap[hex.toLowerCase()] || null;
    }
    
    // Reset fonksiyonu
    window.resetTheme = function() {
        for (const key in themeConfig) {
            localStorage.removeItem('tabler-' + key);
            document.documentElement.removeAttribute('data-bs-' + key);
            updateFormElement(key, themeConfig[key]);
        }
        console.log('Theme reset to defaults');
        location.reload();
    };
    
    // Debug fonksiyonu
    window.debugTheme = function() {
        console.log('Current theme state:', {
            documentAttributes: Array.from(document.documentElement.attributes)
                .filter(attr => attr.name.startsWith('data-bs-'))
                .reduce((acc, attr) => {
                    acc[attr.name] = attr.value;
                    return acc;
                }, {}),
            localStorage: Object.keys(localStorage)
                .filter(key => key.startsWith('tabler-'))
                .reduce((acc, key) => {
                    acc[key] = localStorage.getItem(key);
                    return acc;
                }, {}),
            computedPrimary: getComputedStyle(document.documentElement)
                .getPropertyValue('--tblr-primary').trim()
        });
    };
    
    console.log('🎨 Professional Theme System Initialized');
});