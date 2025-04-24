document.addEventListener("DOMContentLoaded", function () {
    var themeConfig = {
        theme: "light",
        "theme-font": "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif",
        "theme-primary": "#066fd1",
        "theme-radius": "0.5rem",
        "table-compact": "1",
        "theme-base": "gray"
    };
    var form = document.getElementById("offcanvasSettings");
    var resetButton = document.getElementById("reset-changes");
    
    // Tema ayarlarını form elementlerine uygula
    function applySettings() {
        // Renk modu
        var theme = getCookie('dark') === '1' ? 'dark' : 'light';
        var themeRadios = form.querySelectorAll('input[name="theme"]');
        themeRadios.forEach(function(radio) {
            radio.checked = radio.value === theme;
        });
        
        // Renk şeması
        var primaryColor = getCookie('siteColor') || themeConfig["theme-primary"];
        var colorRadios = form.querySelectorAll('input[name="theme-primary"]');
        colorRadios.forEach(function(radio) {
            radio.checked = radio.value === primaryColor;
        });
        
        // Yazı tipi
        var fontFamily = getCookie('themeFont') || themeConfig["theme-font"];
        var fontRadios = form.querySelectorAll('input[name="theme-font"]');
        fontRadios.forEach(function(radio) {
            radio.checked = radio.value === fontFamily;
        });
        
        // Köşe yuvarlaklığı
        var borderRadius = getCookie('themeRadius') || themeConfig["theme-radius"];
        var radiusRadios = form.querySelectorAll('input[name="theme-radius"]');
        radiusRadios.forEach(function(radio) {
            radio.checked = radio.value === borderRadius;
        });
        
        // Tablo kompakt
        var tableCompact = getCookie('tableCompact') || themeConfig["table-compact"];
        var tableRadios = form.querySelectorAll('input[name="table-compact"]');
        tableRadios.forEach(function(radio) {
            radio.checked = radio.value === tableCompact;
        });
        
        // Tema taban rengi
        var themeBase = getCookie('themeBase') || themeConfig["theme-base"];
        var baseRadios = form.querySelectorAll('input[name="theme-base"]');
        baseRadios.forEach(function(radio) {
            radio.checked = radio.value === themeBase;
        });
    }
    
    // Tema ayarlarını cookie'lere kaydet ve anında uygula
    function applyThemeChanges(name, value) {
        switch(name) {
            case 'theme':
                var darkValue = value === 'dark' ? '1' : '0';
                setCookie('dark', darkValue, 365);
                document.body.className = darkValue === '1' ? 
                    'dark' + (getCookie('tableCompact') === '0' ? '' : ' table-compact') : 
                    'light' + (getCookie('tableCompact') === '0' ? '' : ' table-compact');
                document.body.setAttribute('data-bs-theme', value);
                
                // Dark switch'i güncelle
                const darkSwitches = document.querySelectorAll(".dark-switch");
                darkSwitches.forEach(function (switchEl) {
                    switchEl.checked = darkValue === '1';
                });
                break;
                
            case 'theme-primary':
                changeColor(value);
                
                // Header'daki renk seçiciyi güncelle
                const selectedColor = document.getElementById("selectedColor");
                if (selectedColor) {
                    selectedColor.style.backgroundColor = value;
                }
                break;
                
            case 'theme-font':
                setCookie('themeFont', value, 365);
                document.documentElement.style.setProperty('--tblr-font-family', value);
                document.body.style.fontFamily = value;
                break;
                
            case 'theme-radius':
                setCookie('themeRadius', value, 365);
                document.documentElement.style.setProperty('--tblr-border-radius', value);
                
                // Tüm köşeli elementleri güncelleme
                const elements = document.querySelectorAll('.btn, .card, .form-control, .form-select, .dropdown-menu, .alert, .badge, .nav-tabs, .nav-pills .nav-link, .input-group-text, .modal-content, .toast, .pagination .page-item .page-link');
                elements.forEach(el => {
                    el.style.borderRadius = value;
                });
                break;
                
            case 'table-compact':
                setCookie('tableCompact', value, 365);
                toggleTableMode(value === '1');
                break;
                
            case 'theme-base':
                setCookie('themeBase', value, 365);
                
                // Gri tonunu uygula
                let baseColorClass = '';
                switch(value) {
                    case 'slate':
                        baseColorClass = 'theme-base-slate';
                        break;
                    case 'gray':
                        baseColorClass = 'theme-base-gray';
                        break;
                    case 'zinc':
                        baseColorClass = 'theme-base-zinc';
                        break;
                    case 'neutral':
                        baseColorClass = 'theme-base-neutral';
                        break;
                    case 'stone':
                        baseColorClass = 'theme-base-stone';
                        break;
                }
                
                // Önce tüm tema taban sınıflarını kaldır
                document.body.classList.remove(
                    'theme-base-slate',
                    'theme-base-gray',
                    'theme-base-zinc',
                    'theme-base-neutral',
                    'theme-base-stone'
                );
                
                // Seçilen tema taban sınıfını ekle
                if (baseColorClass) {
                    document.body.classList.add(baseColorClass);
                }
                
                // CSS değişkenlerini güncelle
                updateThemeBaseColors(value);
                break;
        }
    }
    
    // Tema taban renklerini güncelle
    function updateThemeBaseColors(base) {
        let grayColors = {};
        
        switch(base) {
            case 'slate':
                grayColors = {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a'
                };
                break;
            case 'gray': // Varsayılan
                grayColors = {
                    50: '#fafafa',
                    100: '#f4f4f5',
                    200: '#e4e4e7',
                    300: '#d4d4d8',
                    400: '#a1a1aa',
                    500: '#71717a',
                    600: '#52525b',
                    700: '#3f3f46',
                    800: '#27272a',
                    900: '#18181b'
                };
                break;
            case 'zinc':
                grayColors = {
                    50: '#fafafa',
                    100: '#f4f4f5',
                    200: '#e4e4e7',
                    300: '#d4d4d8',
                    400: '#a1a1aa',
                    500: '#71717a',
                    600: '#52525b',
                    700: '#3f3f46',
                    800: '#27272a',
                    900: '#18181b'
                };
                break;
            case 'neutral':
                grayColors = {
                    50: '#fafafa',
                    100: '#f5f5f5',
                    200: '#e5e5e5',
                    300: '#d4d4d4',
                    400: '#a3a3a3',
                    500: '#737373',
                    600: '#525252',
                    700: '#404040',
                    800: '#262626',
                    900: '#171717'
                };
                break;
            case 'stone':
                grayColors = {
                    50: '#fafaf9',
                    100: '#f5f5f4',
                    200: '#e7e5e4',
                    300: '#d6d3d1',
                    400: '#a8a29e',
                    500: '#78716c',
                    600: '#57534e',
                    700: '#44403c',
                    800: '#292524',
                    900: '#1c1917'
                };
                break;
        }
        
        // CSS değişkenlerini güncelle
        Object.keys(grayColors).forEach(key => {
            document.documentElement.style.setProperty(`--tblr-gray-${key}`, grayColors[key]);
        });
        
        // Temaya göre arkaplan ve yazı renklerini güncelle
        if (document.body.getAttribute('data-bs-theme') === 'dark') {
            document.documentElement.style.setProperty('--tblr-body-bg', grayColors[900]);
            document.documentElement.style.setProperty('--tblr-bg-surface', grayColors[800]);
            document.documentElement.style.setProperty('--tblr-bg-surface-secondary', grayColors[700]);
            document.documentElement.style.setProperty('--tblr-body-color', grayColors[100]);
        } else {
            document.documentElement.style.setProperty('--tblr-body-bg', grayColors[50]);
            document.documentElement.style.setProperty('--tblr-bg-surface', '#ffffff');
            document.documentElement.style.setProperty('--tblr-bg-surface-secondary', grayColors[100]);
            document.documentElement.style.setProperty('--tblr-body-color', grayColors[900]);
        }
    }
    
    // Form değişikliklerini dinle
    form.addEventListener("change", function (event) {
        if (!event.target.name || !event.target.value) return;
        
        // Değişikliği anında uygula
        applyThemeChanges(event.target.name, event.target.value);
    });
    
    // Değişiklikleri sıfırla
    resetButton.addEventListener("click", function () {
        // Cookie'leri sıfırla
        setCookie('dark', '0', 365);
        setCookie('siteColor', themeConfig["theme-primary"], 365);
        setCookie('themeFont', themeConfig["theme-font"], 365);
        setCookie('themeRadius', themeConfig["theme-radius"], 365);
        setCookie('tableCompact', themeConfig["table-compact"], 365);
        setCookie('themeBase', themeConfig["theme-base"], 365);
        
        // Varsayılan değerleri uygula
        document.body.classList.remove('dark');
        document.body.classList.add('light');
        document.body.setAttribute('data-bs-theme', 'light');
        
        // Tema taban sınıflarını kaldır
        document.body.classList.remove(
            'theme-base-slate',
            'theme-base-gray',
            'theme-base-zinc',
            'theme-base-neutral',
            'theme-base-stone'
        );
        
        // Varsayılan tema taban sınıfını ekle
        document.body.classList.add('theme-base-gray');
        
        changeColor(themeConfig["theme-primary"]);
        document.documentElement.style.setProperty('--tblr-font-family', themeConfig["theme-font"]);
        document.body.style.fontFamily = themeConfig["theme-font"];
        document.documentElement.style.setProperty('--tblr-border-radius', themeConfig["theme-radius"]);
        
        // Köşeli elementleri varsayılana geri döndür
        const elements = document.querySelectorAll('.btn, .card, .form-control, .form-select, .dropdown-menu, .alert, .badge, .nav-tabs, .nav-pills .nav-link, .input-group-text, .modal-content, .toast, .pagination .page-item .page-link');
        elements.forEach(el => {
            el.style.borderRadius = themeConfig["theme-radius"];
        });
        
        toggleTableMode(themeConfig["table-compact"] === '1');
        updateThemeBaseColors(themeConfig["theme-base"]);
        
        // Dark switch'i güncelle
        const darkSwitches = document.querySelectorAll(".dark-switch");
        darkSwitches.forEach(function (switchEl) {
            switchEl.checked = false;
        });
        
        // Form değerlerini güncelle
        applySettings();
    });
    
    // Cookie yardımcı fonksiyonları
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }
    
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    
    // Sayfa yüklendiğinde tema ayarlarını uygula
    applySettings();
    
    // Sayfa yüklendiğinde CSS değişkenlerini uygula
    document.documentElement.style.setProperty('--tblr-font-family', getCookie('themeFont') || themeConfig["theme-font"]);
    document.body.style.fontFamily = getCookie('themeFont') || themeConfig["theme-font"];
    document.documentElement.style.setProperty('--tblr-border-radius', getCookie('themeRadius') || themeConfig["theme-radius"]);
    
    // Köşeli elementleri başlangıçta güncelle
    const borderRadius = getCookie('themeRadius') || themeConfig["theme-radius"];
    const elements = document.querySelectorAll('.btn, .card, .form-control, .form-select, .dropdown-menu, .alert, .badge, .nav-tabs, .nav-pills .nav-link, .input-group-text, .modal-content, .toast, .pagination .page-item .page-link');
    elements.forEach(el => {
        el.style.borderRadius = borderRadius;
    });
    
    // Tema taban rengini uygula
    const themeBase = getCookie('themeBase') || themeConfig["theme-base"];
    updateThemeBaseColors(themeBase);
    
    // Tema taban sınıfları
    const baseClass = `theme-base-${themeBase}`;
    document.body.classList.add(baseClass);
    
    // Dark switch ile tema değişimi yapıldığında tema ayarlarını güncelle
    const darkSwitches = document.querySelectorAll(".dark-switch");
    darkSwitches.forEach(function (switchEl) {
        switchEl.addEventListener("change", function () {
            var themeRadios = form.querySelectorAll('input[name="theme"]');
            themeRadios.forEach(function(radio) {
                if (radio.value === (switchEl.checked ? 'dark' : 'light')) {
                    radio.checked = true;
                    applyThemeChanges('theme', radio.value);
                }
            });
        });
    });
});

// Renk değiştirme fonksiyonu (main.js'den alındı ve güncellendi)
function changeColor(newColor) {
    const { r, g, b } = hexToRgbObj(newColor);
    const brightness = (r * 299 + g * 587 + b * 114) / 1000;
    const textColor = brightness > 170 ? "rgb(24, 36, 51)" : "#ffffff";
    const rgbValue = `${r},${g},${b}`;

    // CSS kurallarını oluştur
    let styleSheet = document.getElementById("dynamic-colors");
    if (!styleSheet) {
        styleSheet = document.createElement("style");
        styleSheet.id = "dynamic-colors";
        document.head.appendChild(styleSheet);
    }

    // CSS kurallarını güncelle
    styleSheet.textContent = `
        :root {
            --primary-color: ${newColor};
            --primary-text-color: ${textColor};
            --primary-rgb: ${rgbValue};
            --tblr-primary: ${newColor};
            --bs-primary: ${newColor};
            --bs-primary-rgb: ${rgbValue};
        }
        
        .btn-primary {
            color: ${textColor} !important;
            background-color: ${newColor} !important;
            border-color: ${newColor} !important;
        }
        
        .bg-primary {
            color: ${textColor} !important;
            background-color: ${newColor} !important;
        }
    `;

    // Cookie'leri güncelle
    document.cookie = `siteColor=${newColor}; max-age=${60 * 60 * 24 * 365}; path=/`;
    document.cookie = `siteTextColor=${textColor}; max-age=${60 * 60 * 24 * 365}; path=/`;

    // Eğer selectedColor varsa (navigation.blade.php'de) onu da güncelle
    const selectedColor = document.getElementById("selectedColor");
    if (selectedColor) {
        selectedColor.style.backgroundColor = newColor;
        selectedColor.style.color = textColor;
    }
}

function hexToRgbObj(hex) {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    return { r, g, b };
}

// Tablo düzeni kodu
function toggleTableMode(isCompact) {
    document.cookie = `tableCompact=${isCompact ? '1' : '0'}; max-age=${60*60*24*30}; path=/`;
    const body = document.body;
    const darkClass = body.classList.contains('dark') ? 'dark' : 'light';
    body.className = `${darkClass}${isCompact ? ' table-compact' : ''}`;
    
    // Tema taban sınıfını koru
    const themeBase = getCookie('themeBase') || 'gray';
    body.classList.add(`theme-base-${themeBase}`);
}