# 🎨 iXtif Renk Paleti - Dark & Light Mode

**Kaynak Tasarım:** https://ixtif.com/design/hakkimizda-alternatifler/design-hakkimizda-10.html

Premium, lüks ve kurumsal bir görünüm için optimize edilmiş dark & light mode renk sistemi.

**⚠️ ÖZEL KURAL:** Siyah (black) YOK - Sadece lacivert (navy) kullan!

---

## 🌑 DARK MODE (Default)

### Background Renkler

| Kullanım | Tailwind Class | Hex | Açıklama |
|----------|---------------|-----|----------|
| **Ana Arkaplan** | `bg-navy-950` | `#0a0e27` | En koyu lacivert (body) - SİYAH YERİNE! |
| **İkincil Arkaplan** | `bg-navy-900` | `#0f1629` | Section/container arkaplanı |
| **Card Arkaplanı** | `bg-navy-800` | `#1a1f3a` | Card, element arkaplanları |
| **Orta Ton** | `bg-gray-800` | `#1f2937` | Alternatif card arkaplanı |

**Gradient Arkaplanlar:**
```css
/* Section gradient (Navy ile!) */
bg-gradient-to-b from-navy-950 via-navy-900 to-navy-800

/* Card gradient */
bg-gradient-to-br from-navy-900 to-navy-800
bg-gradient-to-br from-navy-900 via-navy-800 to-gray-800
```

### Text Renkler

| Kullanım | Tailwind Class | Hex | Açıklama |
|----------|---------------|-----|----------|
| **Ana Text** | `text-white` | `#ffffff` | Başlıklar, önemli text |
| **İkincil Text** | `text-gray-400` | `#9ca3af` | Açıklamalar, paragraph |
| **Soluk Text** | `text-gray-500` | `#6b7280` | Footer, caption |
| `text-gray-600` | `#4b5563` | Çok soluk text |

### Gold/Yellow (Premium Accent) ⭐

**Ana renk:** Gold gradient (altın parlaklığı efekti)

| Kullanım | Tailwind Class | Hex | Açıklama |
|----------|---------------|-----|----------|
| **CTA Button** | `bg-yellow-600` | `#ca8a04` | Ana buton arkaplanı |
| **Hover/Gradient** | `bg-yellow-500` | `#eab308` | Hover state |
| **Accent Text** | `text-yellow-500` | `#eab308` | Link, icon, vurgu |
| **Border** | `border-yellow-600` | `#ca8a04` | Accent border |

**Custom Gold Gradient:**
```css
.gold-gradient {
    background: linear-gradient(90deg, #d4af37, #f4e5a1, #d4af37, #f4e5a1);
    background-size: 200% auto;
    animation: gold-shimmer 3s ease infinite;
}

@keyframes gold-shimmer {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
```

**Kullanım:**
```html
<!-- Text gradient -->
<span class="gold-gradient bg-clip-text text-transparent">PRESTİJ</span>

<!-- Button gradient -->
<button class="gold-gradient text-gray-950">SATIN AL</button>
```

### Border Renkler

| Kullanım | Tailwind Class | Hex |
|----------|---------------|-----|
| **Ana Border** | `border-gray-800` | `#1f2937` |
| **Açık Border** | `border-gray-700` | `#374151` |
| **Accent Border** | `border-yellow-600` | `#ca8a04` |

### Overlay/Glassmorphism

```css
/* Navbar backdrop (Navy ile!) */
bg-navy-900/80 backdrop-blur-xl border-b border-gray-800

/* Glass cards */
bg-white/10 backdrop-blur-md border border-white/20

/* Hover state */
hover:bg-white/20
```

### Shadow Effects

```css
/* Gold glow */
hover:shadow-[0_0_20px_rgba(212,175,55,0.5)]
hover:shadow-[0_0_40px_rgba(212,175,55,0.5)]

/* Yellow glow */
hover:shadow-[0_0_20px_rgba(234,179,8,0.5)]
```

---

## ☀️ LIGHT MODE

### Background Renkler

| Kullanım | Tailwind Class | Hex | Açıklama |
|----------|---------------|-----|----------|
| **Ana Arkaplan** | `bg-white` | `#ffffff` | En açık arkaplan (body) |
| **İkincil Arkaplan** | `bg-gray-50` | `#f9fafb` | Section/container arkaplanı |
| **Card Arkaplanı** | `bg-gray-100` | `#f3f4f6` | Card, element arkaplanları |
| **Gölge Arkaplan** | `bg-gray-200` | `#e5e7eb` | Hover state |

**Gradient Arkaplanlar:**
```css
/* Section gradient */
bg-gradient-to-b from-white via-gray-50 to-gray-100

/* Card gradient */
bg-gradient-to-br from-white to-gray-50
bg-gradient-to-br from-white via-gray-50 to-gray-100
```

### Text Renkler

| Kullanım | Tailwind Class | Hex | Açıklama |
|----------|---------------|-----|----------|
| **Ana Text** | `text-gray-900` | `#111827` | Başlıklar, önemli text |
| **İkincil Text** | `text-gray-600` | `#4b5563` | Açıklamalar, paragraph |
| **Soluk Text** | `text-gray-500` | `#6b7280` | Caption, metadata |
| **Çok Soluk** | `text-gray-400` | `#9ca3af` | Disabled text |

### Gold/Yellow (Premium Accent) ⭐

**Aynı gold gradient kullanılır** (dark mode ile aynı)

| Kullanım | Tailwind Class | Hex | Açıklama |
|----------|---------------|-----|----------|
| **CTA Button** | `bg-yellow-600` | `#ca8a04` | Ana buton arkaplanı |
| **Hover/Gradient** | `bg-yellow-500` | `#eab308` | Hover state |
| **Accent Text** | `text-yellow-700` | `#a16207` | Link (daha koyu) |
| **Border** | `border-yellow-600` | `#ca8a04` | Accent border |

### Border Renkler

| Kullanım | Tailwind Class | Hex |
|----------|---------------|-----|
| **Ana Border** | `border-gray-200` | `#e5e7eb` |
| **Açık Border** | `border-gray-300` | `#d1d5db` |
| **Accent Border** | `border-yellow-600` | `#ca8a04` |

### Overlay/Glassmorphism

```css
/* Navbar backdrop */
bg-white/80 backdrop-blur-xl border-b border-gray-200

/* Glass cards */
bg-gray-900/10 backdrop-blur-md border border-gray-900/20

/* Hover state */
hover:bg-gray-900/20
```

### Shadow Effects

```css
/* Light mode shadow */
shadow-lg shadow-gray-200/50

/* Gold glow (aynı) */
hover:shadow-[0_0_20px_rgba(212,175,55,0.5)]
```

---

## 🔄 Dark/Light Mode Toggle

### HTML Class Yapısı

```html
<!-- Dark mode -->
<html lang="tr" class="dark">

<!-- Light mode -->
<html lang="tr" class="">
```

### Tailwind Dark Mode Syntax

```html
<!-- Background -->
<div class="bg-white dark:bg-gray-950">

<!-- Text -->
<p class="text-gray-900 dark:text-white">

<!-- Border -->
<div class="border-gray-200 dark:border-gray-800">

<!-- Card örneği -->
<div class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white border-gray-200 dark:border-gray-800">
```

---

## 📦 Tailwind Config Entegrasyonu

`tailwind.config.js` dosyasına eklenecek:

```javascript
module.exports = {
  darkMode: 'class', // class-based dark mode
  theme: {
    extend: {
      colors: {
        // Navy - En koyu lacivert (siyah yerine!)
        navy: {
          950: '#0a0e27', // En koyu (body background) - BLACK YERİNE!
          900: '#0f1629', // Section background
          800: '#1a1f3a', // Card background
          700: '#252b4a', // Hover state
        },
        // Gold gradient için custom renk
        gold: {
          50: '#fefce8',
          100: '#fef9c3',
          200: '#fef08a',
          300: '#fde047',
          400: '#facc15',
          500: '#f4e5a1', // Light gold
          600: '#d4af37', // Main gold
          700: '#b8941f',
          800: '#92740f',
          900: '#78600a',
        },
      },
      backgroundImage: {
        'gold-gradient': 'linear-gradient(90deg, #d4af37, #f4e5a1, #d4af37, #f4e5a1)',
        'gold-gradient-r': 'linear-gradient(to right, #ca8a04, #eab308)',
      },
      animation: {
        'gold-shimmer': 'gold-shimmer 3s ease infinite',
      },
      keyframes: {
        'gold-shimmer': {
          '0%': { backgroundPosition: '0% 50%' },
          '50%': { backgroundPosition: '100% 50%' },
          '100%': { backgroundPosition: '0% 50%' },
        },
      },
      boxShadow: {
        'gold-sm': '0 0 20px rgba(212, 175, 55, 0.3)',
        'gold': '0 0 20px rgba(212, 175, 55, 0.5)',
        'gold-lg': '0 0 40px rgba(212, 175, 55, 0.5)',
        'yellow-sm': '0 0 20px rgba(234, 179, 8, 0.3)',
        'yellow': '0 0 20px rgba(234, 179, 8, 0.5)',
      },
    },
  },
  plugins: [],
}
```

**⚠️ NOT:** Siyah (black) kullanma! Navy renkleri kullan.

---

## 🎯 Component Örnekleri

Detaylı component örnekleri için: `component-ornekleri.md` dosyasına bakın.

---

## ✅ Checklist: Renk Paletini Uygulamak

- [ ] `tailwind.config.js` güncelle (navy renkleri, gold gradient, dark mode)
- [ ] Gold gradient CSS'ini global styles'a ekle
- [ ] Dark mode toggle sistemi kur (Alpine.js)
- [ ] Tüm modüllerde dark/light class'ları ekle:
  - [ ] Navbar (bg-navy-900/80, glassmorphism)
  - [ ] Footer (bg-navy-950 - siyah YOK!)
  - [ ] Hero sections (gold gradient başlık)
  - [ ] Card components (bg-navy-800, gradient arkaplan)
  - [ ] Form elements
  - [ ] Buttons (gold gradient CTA)
  - [ ] Modals
  - [ ] Tables
- [ ] localStorage dark mode preference kaydet
- [ ] Test et: Dark ↔ Light geçişleri
- [ ] Cache temizle + npm run prod

**⚠️ DİKKAT:**
- **Siyah (bg-black) kullanma!** → bg-navy-950 kullan!
- **Gold gradient kullan!** Premium görünüm için
- **Glassmorphism kullan!** Navbar ve card'larda

---

**Hazırlayan:** Claude
**Tarih:** 2025-10-26
**Kaynak:** design-hakkimizda-10.html
