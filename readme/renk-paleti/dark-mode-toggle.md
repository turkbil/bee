# 🌓 Dark Mode Toggle Sistemi

Alpine.js ile localStorage destekli dark/light mode toggle

---

## 🎯 Sistem Özellikleri

- ✅ Alpine.js ile reactive toggle
- ✅ localStorage ile tercih kaydı
- ✅ Sistem tercihi (prefers-color-scheme) desteği
- ✅ Smooth geçiş animasyonları
- ✅ Icon değişimi (sun/moon)

---

## 📦 1. Tailwind Config Güncelle

`tailwind.config.js` dosyasına ekle:

```javascript
module.exports = {
  darkMode: 'class', // ⚠️ Kritik: class-based dark mode
  theme: {
    extend: {
      // ... (renk paleti config'leri)
    },
  },
  plugins: [],
}
```

---

## 🎨 2. Global CSS Ekle

`resources/css/app.css` veya ilgili global CSS dosyasına:

```css
/* Gold gradient animation */
@keyframes gold-shimmer {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.gold-gradient {
    background: linear-gradient(90deg, #d4af37, #f4e5a1, #d4af37, #f4e5a1);
    background-size: 200% auto;
    animation: gold-shimmer 3s ease infinite;
}

/* Smooth dark mode transition */
html {
    transition: background-color 0.3s ease, color 0.3s ease;
}

body {
    transition: background-color 0.3s ease, color 0.3s ease;
}
```

---

## 🧩 3. Alpine.js Dark Mode Component

`resources/views/themes/ixtif/layout.blade.php` veya master layout'a ekle:

```html
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
      x-data="darkMode()"
      x-bind:class="{ 'dark': isDark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'iXtif - Premium İstif Ekipmanları')</title>

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Dark Mode Alpine.js Component -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('darkMode', () => ({
                isDark: false,

                init() {
                    // 1. localStorage kontrol
                    const theme = localStorage.getItem('theme');

                    if (theme) {
                        this.isDark = theme === 'dark';
                    } else {
                        // 2. Sistem tercihi kontrol (prefers-color-scheme)
                        this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    }

                    // 3. Sistem tercihi değişikliklerini dinle
                    window.matchMedia('(prefers-color-scheme: dark)')
                        .addEventListener('change', (e) => {
                            if (!localStorage.getItem('theme')) {
                                this.isDark = e.matches;
                            }
                        });
                },

                toggle() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                }
            }));
        });
    </script>
</head>
<body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-white">

    <!-- Dark Mode Toggle Button -->
    @include('themes.ixtif.partials.dark-mode-toggle')

    <!-- Content -->
    @yield('content')

</body>
</html>
```

---

## 🔘 4. Toggle Button Component

`resources/views/themes/ixtif/partials/dark-mode-toggle.blade.php`:

### Option 1: Floating Button (Navbar dışında)

```html
<!-- Sağ üst köşe floating button -->
<div class="fixed top-6 right-6 z-50">
    <button @click="toggle()"
            class="w-12 h-12
                   bg-white dark:bg-gray-800
                   border-2 border-gray-200 dark:border-gray-700
                   rounded-full
                   shadow-lg
                   hover:scale-110
                   hover:shadow-xl
                   transition-all
                   flex items-center justify-center">
        <!-- Sun icon (light mode) -->
        <svg x-show="!isDark"
             xmlns="http://www.w3.org/2000/svg"
             class="h-6 w-6 text-yellow-600"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>

        <!-- Moon icon (dark mode) -->
        <svg x-show="isDark"
             xmlns="http://www.w3.org/2000/svg"
             class="h-6 w-6 text-yellow-500"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>
</div>
```

### Option 2: Navbar İçinde

```html
<!-- Navbar içinde toggle -->
<nav class="fixed top-0 left-0 right-0 z-50
            bg-white/80 dark:bg-black/80
            backdrop-blur-xl
            border-b border-gray-200 dark:border-gray-800">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">

        <!-- Logo -->
        <div class="text-2xl font-black bg-gold-gradient bg-clip-text text-transparent">
            iXtif
        </div>

        <!-- Menu + Dark Mode Toggle -->
        <div class="flex items-center gap-6">
            <a href="/" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                Anasayfa
            </a>
            <a href="/urunler" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                Ürünler
            </a>

            <!-- Dark Mode Toggle -->
            <button @click="toggle()"
                    class="w-10 h-10
                           bg-gray-100 dark:bg-gray-800
                           border border-gray-200 dark:border-gray-700
                           rounded-lg
                           hover:bg-gray-200 dark:hover:bg-gray-700
                           transition-all
                           flex items-center justify-center">
                <svg x-show="!isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg x-show="isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            <!-- CTA Button -->
            <a href="/iletisim"
               class="px-6 py-2
                      bg-gradient-to-r from-yellow-600 to-yellow-500
                      text-white
                      rounded-full
                      hover:shadow-yellow
                      transition-all">
                İletişim
            </a>
        </div>

    </div>
</nav>
```

### Option 3: Premium Toggle (with Animation)

```html
<!-- Premium animated toggle -->
<button @click="toggle()"
        class="relative w-16 h-8
               bg-gray-200 dark:bg-gray-700
               rounded-full
               transition-all
               hover:shadow-lg">
    <!-- Slider -->
    <div class="absolute top-1 left-1
                w-6 h-6
                bg-gradient-to-r from-yellow-600 to-yellow-500
                rounded-full
                transition-transform
                flex items-center justify-center"
         :class="{ 'translate-x-8': isDark }">
        <!-- Icon inside slider -->
        <svg x-show="!isDark" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg x-show="isDark" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </div>
</button>
```

---

## 🧪 5. Test Senaryoları

### Manuel Test

```bash
# 1. Tarayıcı açılışında default theme kontrol
# 2. Toggle butonuna tıkla → Dark/Light geçiş
# 3. Sayfayı yenile → Tercih korunmalı (localStorage)
# 4. localStorage temizle → Sistem tercihi (prefers-color-scheme) kullanılmalı
```

### Browser Console Test

```javascript
// localStorage kontrol
localStorage.getItem('theme') // "dark" veya "light"

// Dark mode aç
localStorage.setItem('theme', 'dark')
location.reload()

// Light mode aç
localStorage.setItem('theme', 'light')
location.reload()

// Sistem tercihi kullan (localStorage temizle)
localStorage.removeItem('theme')
location.reload()
```

---

## 📱 6. Responsive Behavior

```html
<!-- Mobile: Hamburger menü içinde -->
<div class="md:hidden" x-data="{ open: false }">
    <!-- Hamburger button -->
    <button @click="open = !open" class="...">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Mobile menu -->
    <div x-show="open" class="...">
        <!-- Menu items -->
        <a href="/">Anasayfa</a>
        <a href="/urunler">Ürünler</a>

        <!-- Dark mode toggle -->
        <button @click="toggle()" class="...">
            <span x-text="isDark ? 'Light Mode' : 'Dark Mode'"></span>
        </button>
    </div>
</div>
```

---

## ✅ Kurulum Checklist

- [ ] `tailwind.config.js` → `darkMode: 'class'` ekle
- [ ] Global CSS → `.gold-gradient` animasyonu ekle
- [ ] Layout → Alpine.js `darkMode()` component ekle
- [ ] Layout → `x-data="darkMode()"` ve `:class="{ 'dark': isDark }"` ekle
- [ ] Toggle button → Navbar veya floating position'da ekle
- [ ] Test → Dark/Light toggle çalışıyor mu?
- [ ] Test → localStorage tercihi kaydediliyor mu?
- [ ] Test → Sayfa yenilenince tercih korunuyor mu?
- [ ] Build → `npm run prod`
- [ ] Cache → `php artisan view:clear && php artisan cache:clear`

---

## 🔧 Troubleshooting

### Problem: Dark mode toggle çalışmıyor

```bash
# 1. Alpine.js yüklü mü?
# Browser console: Alpine

# 2. Tailwind darkMode config doğru mu?
# tailwind.config.js: darkMode: 'class'

# 3. Build yapıldı mı?
npm run prod
```

### Problem: Tercih kaydedilmiyor

```javascript
// Browser console: localStorage kontrol
localStorage.getItem('theme')

// Manuel set et
localStorage.setItem('theme', 'dark')
```

### Problem: Geçiş animasyonları yavaş

```css
/* Transition süresini azalt */
html, body {
    transition: background-color 0.15s ease, color 0.15s ease;
}
```

---

**Hazırlayan:** Claude
**Tarih:** 2025-10-26
**Framework:** Alpine.js + Tailwind CSS + localStorage
