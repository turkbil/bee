# 🧩 Component Örnekleri - Dark & Light Mode

**⚠️ SADE TASARIM:** Gradient YOK, Animasyon YOK, Siyah YOK (Navy kullan!)

---

## 🎯 NAVBAR (Sticky, Solid)

```html
<nav class="fixed top-0 left-0 right-0 z-50
            bg-white dark:bg-navy-900
            border-b border-gray-200 dark:border-gray-800">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">

        <!-- Logo (Solid Yellow) -->
        <div class="text-2xl font-black text-yellow-600">
            iXtif
        </div>

        <!-- Menu -->
        <div class="flex gap-6 text-sm">
            <a href="/"
               class="text-gray-600 dark:text-gray-400
                      hover:text-gray-900 dark:hover:text-white
                      transition-colors">
                Anasayfa
            </a>
            <a href="/urunler"
               class="text-gray-600 dark:text-gray-400
                      hover:text-gray-900 dark:hover:text-white
                      transition-colors">
                Ürünler
            </a>

            <!-- CTA Button (Solid Yellow) -->
            <a href="/iletisim"
               class="px-6 py-2
                      bg-yellow-600
                      hover:bg-yellow-500
                      text-white
                      rounded-full
                      transition-colors">
                İletişim
            </a>
        </div>

    </div>
</nav>
```

**⚠️ Değişiklikler:**
- ~~bg-black/80~~ → `bg-white dark:bg-navy-900` (solid!)
- ~~backdrop-blur-xl~~ → Kaldırıldı
- ~~bg-gradient-to-r from-yellow-600 to-yellow-500~~ → `bg-yellow-600` (solid!)
- ~~hover:shadow-yellow~~ → `hover:bg-yellow-500` (solid hover!)

---

## 🎨 HERO SECTION

```html
<section class="pt-32 pb-20 px-4
                bg-white dark:bg-navy-950">
    <div class="container mx-auto max-w-6xl text-center">

        <!-- Badge -->
        <div class="inline-block mb-6 px-6 py-2
                    bg-yellow-600/20
                    border border-yellow-600/30
                    rounded-full">
            <span class="text-yellow-700 dark:text-yellow-500 font-bold text-sm">
                <i class="fas fa-crown mr-2"></i>
                PREMIUM İSTİF EKİPMANLARI
            </span>
        </div>

        <!-- Başlık (Solid Yellow) -->
        <h1 class="text-7xl md:text-9xl font-black mb-6 leading-none">
            <span class="text-yellow-600">
                PRESTİJİN
            </span>
            <span class="block text-gray-900 dark:text-white mt-2">
                ADRESİ
            </span>
        </h1>

        <!-- Açıklama -->
        <p class="text-2xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto mb-12">
            25 yıldır Türkiye'nin en seçkin kurumsal firmalarına premium forklift ve depo ekipmanları hizmeti sunuyoruz
        </p>

        <!-- CTA Buttons -->
        <div class="flex justify-center gap-6">
            <!-- Primary (Solid) -->
            <a href="#iletisim"
               class="px-10 py-5
                      bg-yellow-600
                      hover:bg-yellow-500
                      text-white
                      rounded-full
                      font-black
                      transition-colors">
                ÖZEL TEKLİF ALIN
            </a>

            <!-- Secondary -->
            <a href="#kesfet"
               class="px-10 py-5
                      border-2 border-yellow-600
                      text-yellow-700 dark:text-yellow-500
                      rounded-full
                      font-bold
                      hover:bg-yellow-600/10
                      transition-all">
                Keşfedin
            </a>
        </div>

    </div>
</section>
```

**⚠️ Değişiklikler:**
- ~~bg-gradient-to-b from-white via-gray-50 to-gray-100~~ → `bg-white dark:bg-navy-950` (solid!)
- ~~bg-gold-gradient bg-clip-text text-transparent~~ → `text-yellow-600` (solid!)
- ~~bg-gold-gradient~~ → `bg-yellow-600` (solid!)
- ~~hover:shadow-gold-lg~~ → `hover:bg-yellow-500` (solid hover!)

---

## 📊 STATS GRID

```html
<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-20">

    <!-- Stat Card (Solid) -->
    <div class="text-center p-8
                bg-gray-100 dark:bg-navy-800
                rounded-2xl
                border border-gray-200 dark:border-gray-700
                hover:border-yellow-600
                transition-all">
        <div class="text-6xl font-black text-yellow-600 mb-2">
            25+
        </div>
        <div class="text-gray-600 dark:text-gray-400 text-sm uppercase tracking-wider">
            Yıl Liderlik
        </div>
    </div>

    <!-- Diğer stat cardlar aynı yapı -->

</div>
```

**⚠️ Değişiklikler:**
- ~~bg-gradient-to-br from-white to-gray-50~~ → `bg-gray-100 dark:bg-navy-800` (solid!)
- ~~bg-gold-gradient bg-clip-text text-transparent~~ → `text-yellow-600` (solid!)

---

## 🃏 FEATURE CARD

```html
<div class="group
            bg-white dark:bg-navy-900
            p-10
            rounded-2xl
            border border-gray-200 dark:border-gray-700
            hover:border-yellow-600
            transition-all">

    <!-- Icon (Solid Yellow Background) -->
    <div class="w-16 h-16
                bg-yellow-600
                rounded-xl
                flex items-center justify-center
                mb-6
                group-hover:bg-yellow-500
                transition-colors">
        <i class="fas fa-shopping-cart text-white text-2xl"></i>
    </div>

    <!-- Başlık -->
    <h3 class="text-2xl font-bold mb-4
               text-gray-900 dark:text-white">
        Premium Satın Alma
    </h3>

    <!-- Açıklama -->
    <p class="text-gray-600 dark:text-gray-400 mb-6">
        Sıfır ekipmanlar, 2 yıl garanti, VIP montaj ve eğitim hizmetleri
    </p>

    <!-- Link -->
    <a href="/satin-alma"
       class="text-yellow-700 dark:text-yellow-500
              font-bold
              group-hover:translate-x-2
              inline-block
              transition-transform">
        Detaylar <i class="fas fa-arrow-right ml-2"></i>
    </a>

</div>
```

**⚠️ Değişiklikler:**
- ~~bg-gradient-to-br from-white to-gray-50~~ → `bg-white dark:bg-navy-900` (solid!)
- ~~bg-gold-gradient~~ → `bg-yellow-600` (solid!)
- ~~group-hover:scale-110~~ → `group-hover:bg-yellow-500` (solid hover!)

---

## 📈 INFO CARD (İstatistik)

```html
<div class="bg-white dark:bg-navy-800
            p-8
            rounded-2xl
            border border-gray-200 dark:border-gray-700">

    <!-- Icon -->
    <i class="fas fa-forklift
              text-5xl text-yellow-600
              mb-4"></i>

    <!-- Number -->
    <div class="text-4xl font-black text-gray-900 dark:text-white mb-2">
        1,020
    </div>

    <!-- Label -->
    <div class="text-gray-600 dark:text-gray-400">
        Premium Ürün
    </div>

</div>
```

**⚠️ Değişiklikler:**
- ~~bg-gradient-to-br from-white via-gray-50 to-gray-100~~ → `bg-white dark:bg-navy-800` (solid!)

---

## 🔘 BUTTONS

### Primary CTA (Solid Yellow)

```html
<button class="px-10 py-5
               bg-yellow-600
               hover:bg-yellow-500
               text-white
               rounded-full
               font-black
               transition-colors">
    SATIN AL
</button>
```

**⚠️ Değişiklik:**
- ~~bg-gold-gradient~~ → `bg-yellow-600` (solid!)
- ~~hover:shadow-gold-lg~~ → `hover:bg-yellow-500` (solid hover!)

### Secondary Outline

```html
<button class="px-10 py-5
               border-2 border-yellow-600
               text-yellow-700 dark:text-yellow-500
               rounded-full
               font-bold
               hover:bg-yellow-600/10
               transition-all">
    DETAYLAR
</button>
```

### Tertiary (Ghost)

```html
<button class="px-8 py-4
               bg-gray-100 dark:bg-navy-800
               border border-gray-200 dark:border-gray-700
               text-gray-900 dark:text-white
               rounded-xl
               font-bold
               hover:border-yellow-600
               transition-all">
    <i class="fas fa-download mr-2"></i>
    Katalog İndir
</button>
```

---

## 🗂️ CONTACT CARD

```html
<a href="tel:02167553555"
   class="bg-gray-100 dark:bg-navy-800
          p-8
          rounded-2xl
          border border-gray-200 dark:border-gray-700
          hover:border-yellow-600
          hover:shadow-lg
          transition-all">

    <!-- Icon -->
    <i class="fas fa-phone
              text-5xl text-yellow-600
              mb-4"></i>

    <!-- Label -->
    <div class="font-bold text-lg text-gray-900 dark:text-white">
        VIP Hat
    </div>

    <!-- Value -->
    <div class="text-gray-600 dark:text-gray-400">
        0216 755 3 555
    </div>

</a>
```

**⚠️ Değişiklikler:**
- ~~bg-white/10 backdrop-blur-md~~ → `bg-gray-100 dark:bg-navy-800` (solid!)
- ~~hover:bg-white/20~~ → `hover:border-yellow-600 hover:shadow-lg` (solid!)
- ~~hover:scale-105~~ → Kaldırıldı (sade tasarım!)

---

## 🎯 BADGE/PILL

```html
<!-- Premium badge (Solid) -->
<div class="inline-block px-6 py-2
            bg-yellow-600/20
            border border-yellow-600/30
            rounded-full">
    <span class="text-yellow-700 dark:text-yellow-500 font-bold text-sm">
        <i class="fas fa-crown mr-2"></i>
        PREMIUM
    </span>
</div>
```

---

## 📄 SECTION CONTAINERS

### Default Section (Solid)

```html
<section class="py-20 px-4
                bg-white dark:bg-navy-950">
    <div class="container mx-auto max-w-7xl">
        <!-- Content -->
    </div>
</section>
```

**⚠️ Değişiklik:**
- ~~bg-black~~ → `bg-navy-950` (siyah YOK!)

### Alternate Section (Solid)

```html
<section class="py-20 px-4
                bg-gray-50 dark:bg-navy-900">
    <div class="container mx-auto max-w-6xl">
        <!-- Content -->
    </div>
</section>
```

**⚠️ Değişiklik:**
- ~~bg-gradient-to-b from-white via-gray-50 to-gray-100~~ → `bg-gray-50 dark:bg-navy-900` (solid!)

---

## 🔲 FOOTER

```html
<footer class="bg-gray-50 dark:bg-navy-950
               py-12
               border-t border-gray-200 dark:border-gray-800">
    <div class="container mx-auto text-center">

        <!-- Logo (Solid Yellow) -->
        <div class="text-4xl font-black text-yellow-600 mb-4">
            iXtif
        </div>

        <!-- Slogan -->
        <div class="text-gray-500 mb-6">
            Türkiye'nin Premium İstif Pazarı
        </div>

        <!-- Social Icons -->
        <div class="flex justify-center gap-6 mb-6">
            <a href="#"
               class="text-gray-400 dark:text-gray-600
                      hover:text-yellow-600
                      transition-colors">
                <i class="fab fa-instagram text-2xl"></i>
            </a>
            <!-- Diğer iconlar aynı -->
        </div>

        <!-- Copyright -->
        <div class="text-sm text-gray-500 dark:text-gray-600">
            © 2025 İXTİF İç ve Dış Ticaret A.Ş. | Tüm hakları saklıdır.
        </div>

    </div>
</footer>
```

**⚠️ Değişiklikler:**
- ~~bg-black~~ → `bg-navy-950` (siyah YOK!)
- ~~bg-gold-gradient bg-clip-text text-transparent~~ → `text-yellow-600` (solid!)

---

## 🎨 TRANSITIONS & EFFECTS

### Hover Effects (Sade!)

```html
<!-- Renk geçişi -->
<button class="bg-yellow-600 hover:bg-yellow-500 transition-colors">

<!-- Border geçişi -->
<div class="border border-gray-200 hover:border-yellow-600 transition-all">

<!-- Shadow geçişi -->
<div class="shadow-md hover:shadow-lg transition-shadow">

<!-- Transform (minimal) -->
<a class="group">
    <span class="group-hover:translate-x-2 inline-block transition-transform">
        Detaylar <i class="fas fa-arrow-right ml-2"></i>
    </span>
</a>
```

**⚠️ YAPMA:**
- ~~animate-*~~ kullanma!
- ~~hover:scale-110~~ gibi büyük scale kullanma!
- ~~hover:shadow-gold-lg~~ gibi custom glow kullanma!

---

## ✨ BEST PRACTICES

### Typography Hierarchy

```html
<!-- H1 - Hero -->
<h1 class="text-7xl md:text-9xl font-black text-gray-900 dark:text-white">

<!-- H1 Accent -->
<h1 class="text-7xl font-black text-yellow-600">

<!-- H2 - Section -->
<h2 class="text-5xl font-black text-gray-900 dark:text-white">

<!-- H3 - Card -->
<h3 class="text-2xl font-bold text-gray-900 dark:text-white">

<!-- Body -->
<p class="text-xl text-gray-600 dark:text-gray-400">
```

### Spacing System

```html
<!-- Section padding -->
py-20 px-4

<!-- Card padding -->
p-8 | p-10

<!-- Container max-width -->
max-w-6xl | max-w-7xl

<!-- Gap -->
gap-4 | gap-6 | gap-8 | gap-12
```

### Border Radius

```html
<!-- Pills/Badges -->
rounded-full

<!-- Cards -->
rounded-2xl

<!-- Icons/Small elements -->
rounded-xl
```

---

## ⚠️ KURAL ÖZETİ

### YAPILACAKLAR ✅
- Solid renkler kullan
- Navy (lacivert) kullan (siyah yerine)
- Standart Tailwind transition'ları
- Minimal hover effects

### YAPILMAYACAKLAR ❌
- ~~bg-gradient-to-*~~ kullanma!
- ~~bg-black~~ kullanma → bg-navy-950 kullan!
- ~~animate-*~~ kullanma!
- ~~.gold-gradient~~ kullanma → text-yellow-600 kullan!
- ~~backdrop-blur-*~~ minimal kullan (solid tercih!)
- ~~hover:scale-*~~ büyük scale kullanma!
- ~~hover:shadow-[custom-glow]~~ kullanma!

---

**Hazırlayan:** Claude
**Tarih:** 2025-10-26
**Not:** Tüm componentler sade, solid renk tasarımı ile dark/light mode desteklidir.
