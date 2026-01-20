# ECRİN TURİZM - WEB SİTESİ DOKÜMANTASYONU

> **Kapsamlı Web Sitesi İçerik ve Teknik Kılavuzu**
> Son güncellemeler: Telefon/WhatsApp odaklı iletişim, temiz hero tasarımı, doğrulanmış bilgiler

**Son Güncelleme:** 10 Ocak 2026
**Versiyon:** 2.0

---

## 📋 İÇİNDEKİLER

1. [Proje Bilgileri](#proje-bilgileri)
2. [Kurumsal Kimlik](#kurumsal-kimlik)
3. [Teknoloji Stack](#teknoloji-stack)
4. [Renk Paleti](#renk-paleti)
5. [Tipografi](#tipografi)
6. [İkon Kullanımı](#ikon-kullanimi)
7. [Sayfa Yapısı](#sayfa-yapisi)
8. [Ana Sayfa İçeriği](#ana-sayfa-icerigi)
9. [İletişim Stratejisi](#iletisim-stratejisi)
10. [Tasarım Prensipleri](#tasarim-prensipleri)

---

## 📋 PROJE BİLGİLERİ

### Firma Bilgileri (Doğrulanmış)

**Resmi Firma Adı:** Ecrin Turizm Sanayi ve Ticaret Limited Şirketi
**Marka Adı:** Olçun Travel
**Kuruluş Tarihi:** 17.09.2008
**Lisans:** A Grubu Seyahat Acentası İşletme Belgesi (No: 9817)
**Lokasyon:** Güngören / İstanbul

### İletişim Bilgileri (Onaylı)

**Telefon:** 0546 810 17 17
**E-posta:** info@ecrinturizm.org
**Web:** www.ecrinturizm.org

**ÖNEMLİ NOT:**
- Sosyal medya hesapları bulunmamaktadır
- İletişim sadece telefon ve WhatsApp üzerinden sağlanır
- Form tabanlı iletişim kullanılmaz

### Hizmetler

1. **Turizm Taşımacılığı** - Yurt içi ve yurt dışı tur organizasyonları
2. **Personel Taşımacılığı** - Kurumsal servis çözümleri
3. **Öğrenci Taşımacılığı** - Güvenli okul servisi
4. **Otel Rezervasyonları** - Anlaşmalı oteller
5. **Yat Kiralama** - Mavi yolculuk deneyimleri

---

## 🎨 KURUMSAL KİMLİK

### Marka Değerleri

**Ana Motto:** "Güvenle Yolculuk, Huzurla Varış"

**Temel Değerler:**
- Güven ve güvenlik
- Profesyonellik
- Müşteri odaklılık
- Kalite standartları

### Logo Kullanımı

**Format:**
- Birincil: SVG
- Yedek: PNG

**Varyasyonlar:**
- Mavi logo (açık zemin için)
- Beyaz logo (koyu zemin için)

**Minimum Boyut:** 120px genişlik

---

## 💻 TEKNOLOJİ STACK

### Frontend Teknolojileri

```html
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Google Fonts - Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
```

### Teknolojiler

- **HTML5** - Semantik yapı
- **Tailwind CSS v3.4+** - Utility-first CSS
- **Alpine.js 3.x** - Hafif JavaScript framework
- **Font Awesome 6** - İkonlar
- **Google Fonts (Inter)** - Tipografi

---

## 🎨 RENK PALETİ

### Birincil Renkler

**Mavi Tonları (Marka Rengi):**
```css
--blue-50:  #EFF6FF   /* Çok açık mavi - arkaplanlar */
--blue-100: #DBEAFE   /* Açık mavi - kartlar */
--blue-500: #3B82F6   /* Orta mavi - CTA butonları */
--blue-600: #2563EB   /* Koyu mavi - hover efektleri */
--blue-700: #1D4ED8   /* Daha koyu - buton hover */
--blue-800: #1E40AF   /* Ana marka rengi */
--blue-900: #1E3A8A   /* En koyu - header */
```

**Tailwind Sınıfları:**
```html
bg-blue-800      <!-- Ana marka rengi -->
bg-blue-600      <!-- CTA butonları -->
text-blue-600    <!-- Linkler -->
hover:bg-blue-700 <!-- Hover efekti -->
```

### Gri Tonları (Nötr)

```css
--gray-50:  #F9FAFB   /* Sayfa arkaplanı -->
--gray-100: #F3F4F6   /* Bölüm arkaplanları -->
--gray-500: #6B7280   /* Alt metinler -->
--gray-600: #4B5563   /* Gövde metinleri -->
--gray-800: #1F2937   /* Ana başlıklar -->
--gray-900: #111827   /* Hero başlıklar -->
```

### Aksan Renkler

```css
--green-500: #22C55E   /* WhatsApp butonu -->
--amber-500: #F59E0B   /* Özel vurgular -->
```

### Gradient Kombinasyonları

**Hero Gradient:**
```css
background: linear-gradient(135deg, #1E3A8A 0%, #1E40AF 50%, #2563EB 100%);
/* Tailwind: from-blue-900 via-blue-800 to-blue-700 */
```

**CTA Gradient:**
```css
background: linear-gradient(to right, #2563EB, #1D4ED8);
/* Tailwind: from-blue-600 to-blue-700 */
```

---

## ✍️ TİPOGRAFİ

### Font Ailesi

**Ana Font:** Inter (Google Fonts)

```css
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI',
             Roboto, 'Helvetica Neue', Arial, sans-serif;
```

### Başlık Stilleri

**H1 - Hero Başlıkları:**
```html
<!-- Desktop: 60px, Tablet: 48px, Mobile: 36px -->
<h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight text-white">
    Ana Başlık
</h1>
```

**H2 - Bölüm Başlıkları:**
```html
<!-- Desktop: 48px, Tablet: 40px, Mobile: 32px -->
<h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900">
    Bölüm Başlığı
</h2>
```

**H3 - Alt Başlıklar:**
```html
<!-- Desktop: 36px, Tablet: 30px, Mobile: 24px -->
<h3 class="text-2xl md:text-3xl font-semibold text-gray-800">
    Alt Başlık
</h3>
```

**H4 - Kart Başlıkları:**
```html
<!-- 24px sabit -->
<h4 class="text-2xl font-semibold text-gray-800">
    Kart Başlığı
</h4>
```

### Gövde Metni

**Büyük Paragraf (Lead):**
```html
<p class="text-lg md:text-xl leading-relaxed text-gray-600">
    Giriş paragrafı veya önemli metin
</p>
```

**Normal Paragraf:**
```html
<p class="text-base md:text-lg leading-relaxed text-gray-600">
    Standart gövde metni
</p>
```

**Küçük Metin:**
```html
<p class="text-sm text-gray-500">
    Alt metin veya ek bilgiler
</p>
```

### Özel Metin Stilleri

**Üst Etiket (Eyebrow):**
```html
<span class="text-sm font-semibold uppercase tracking-wider text-blue-500">
    Profesyonel Hizmet
</span>
```

---

## 🎯 İKON KULLANIMI

### Font Awesome İkonları

#### İletişim İkonları
```html
<!-- Telefon -->
<i class="fa-solid fa-phone"></i>

<!-- WhatsApp -->
<i class="fa-brands fa-whatsapp"></i>

<!-- E-posta -->
<i class="fa-solid fa-envelope"></i>

<!-- Konum -->
<i class="fa-solid fa-location-dot"></i>
```

#### Hizmet İkonları
```html
<!-- Turizm Taşımacılığı -->
<i class="fa-solid fa-bus"></i>

<!-- Personel Taşımacılığı -->
<i class="fa-solid fa-briefcase"></i>

<!-- Öğrenci Taşımacılığı -->
<i class="fa-solid fa-graduation-cap"></i>

<!-- Otel Rezervasyonları -->
<i class="fa-solid fa-hotel"></i>

<!-- Yat Kiralama -->
<i class="fa-solid fa-ship"></i>
```

#### Özellik İkonları
```html
<!-- A Grubu Lisans -->
<i class="fa-solid fa-award"></i>

<!-- Geniş Filo -->
<i class="fa-solid fa-truck-fast"></i>

<!-- Müşteri Memnuniyeti -->
<i class="fa-solid fa-user-group"></i>

<!-- 7/24 Destek -->
<i class="fa-solid fa-headset"></i>

<!-- Güvenlik -->
<i class="fa-solid fa-shield-halved"></i>

<!-- GPS Takip -->
<i class="fa-solid fa-location-crosshairs"></i>
```

### İkon Boyutları

```html
<!-- Küçük (16px) -->
<i class="fa-solid fa-phone text-base"></i>

<!-- Normal (24px) -->
<i class="fa-solid fa-phone text-2xl"></i>

<!-- Büyük (48px) -->
<i class="fa-solid fa-phone text-5xl"></i>

<!-- Extra Büyük (64px) -->
<i class="fa-solid fa-phone text-6xl"></i>
```

---

## 📄 SAYFA YAPISI

### Site Haritası

Website 4 ana HTML sayfasından oluşur:

1. **index.html** - Ana Sayfa
2. **hizmetlerimiz.html** - Hizmetlerimiz
3. **hakkimizda.html** - Hakkımızda
4. **iletisim.html** - İletişim

### Header Yapısı (Tüm Sayfalar)

**Üst Bar:**
```html
<!-- Responsive: Mobile'da da görünür -->
<div class="bg-blue-900 text-white">
    <div class="flex items-center justify-center md:justify-between py-2 text-xs md:text-sm">
        <!-- Telefon - Her zaman görünür -->
        <a href="tel:+905468101717">
            <i class="fa-solid fa-phone"></i>
            <span>0546 810 17 17</span>
        </a>
        <!-- E-posta - Tablet+ -->
        <a href="mailto:info@ecrinturizm.org" class="hidden sm:flex">
            <i class="fa-solid fa-envelope"></i>
            <span>info@ecrinturizm.org</span>
        </a>
    </div>
</div>
```

**Ana Navigasyon:**
```html
<nav class="bg-white shadow-md sticky top-0 z-50">
    <!-- Logo -->
    <div class="logo">Ecrin Turizm | Olçun Travel</div>

    <!-- Desktop Menü -->
    <div class="hidden lg:flex">
        <a href="index.html">Ana Sayfa</a>
        <a href="hizmetlerimiz.html">Hizmetlerimiz</a>
        <a href="hakkimizda.html">Hakkımızda</a>
        <a href="iletisim.html">İletişim</a>
    </div>

    <!-- CTA Butonlar (Desktop) -->
    <div class="hidden lg:flex gap-3">
        <!-- Telefon Butonu -->
        <a href="tel:+905468101717" class="px-5 py-3 bg-blue-600 text-white">
            <i class="fa-solid fa-phone"></i>
            <span>0546 810 17 17</span>
        </a>
        <!-- WhatsApp Butonu -->
        <a href="https://wa.me/905468101717" class="px-5 py-3 bg-green-500 text-white">
            <i class="fa-brands fa-whatsapp"></i>
            <span>WhatsApp</span>
        </a>
    </div>

    <!-- Hamburger (Mobile) -->
    <button class="lg:hidden" @click="mobileMenuOpen = true">
        <i class="fa-solid fa-bars"></i>
    </button>
</nav>
```

**Mobil Menü:**
```html
<!-- Alpine.js ile açılır kapanır -->
<div x-show="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden">
    <!-- Overlay -->
    <div class="bg-gray-900/50" @click="mobileMenuOpen = false"></div>

    <!-- Menü İçeriği -->
    <div class="w-80 bg-white h-full">
        <!-- Menü Linkleri -->
        <a href="index.html">Ana Sayfa</a>
        <a href="hizmetlerimiz.html">Hizmetlerimiz</a>
        <a href="hakkimizda.html">Hakkımızda</a>
        <a href="iletisim.html">İletişim</a>

        <!-- Alt Kısım CTA Butonları -->
        <div class="p-4 border-t space-y-2">
            <a href="tel:+905468101717" class="btn-phone">
                <i class="fa-solid fa-phone"></i>
                0546 810 17 17
            </a>
            <a href="https://wa.me/905468101717" class="btn-whatsapp">
                <i class="fa-brands fa-whatsapp"></i>
                WhatsApp
            </a>
        </div>
    </div>
</div>
```

### Footer Yapısı (Tüm Sayfalar)

```html
<footer class="bg-gray-900 text-white pt-16 pb-8">
    <div class="container mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">

        <!-- Kolon 1: Hakkımızda -->
        <div>
            <h3>Ecrin Turizm</h3>
            <p class="text-gray-400">
                2008'den beri güvenle hizmet veren A Grubu Seyahat Acentası.
                Profesyonel taşımacılık çözümleri.
            </p>
            <!-- NOT: Sosyal medya YOKTUR -->
        </div>

        <!-- Kolon 2: Hızlı Linkler -->
        <div>
            <h4>Hızlı Erişim</h4>
            <ul>
                <li><a href="index.html">Ana Sayfa</a></li>
                <li><a href="hizmetlerimiz.html">Hizmetlerimiz</a></li>
                <li><a href="hakkimizda.html">Hakkımızda</a></li>
                <li><a href="iletisim.html">İletişim</a></li>
            </ul>
        </div>

        <!-- Kolon 3: Hizmetler -->
        <div>
            <h4>Hizmetlerimiz</h4>
            <ul>
                <li>Turizm Taşımacılığı</li>
                <li>Personel Taşımacılığı</li>
                <li>Öğrenci Taşımacılığı</li>
                <li>Otel Rezervasyonları</li>
                <li>Yat Kiralama</li>
            </ul>
        </div>

        <!-- Kolon 4: İletişim -->
        <div>
            <h4>İletişim</h4>
            <div class="space-y-2 text-gray-400">
                <p>
                    <i class="fa-solid fa-phone"></i>
                    0546 810 17 17
                </p>
                <p>
                    <i class="fa-solid fa-envelope"></i>
                    info@ecrinturizm.org
                </p>
                <p>
                    <i class="fa-solid fa-location-dot"></i>
                    Güngören / İstanbul
                </p>
            </div>
        </div>
    </div>

    <!-- Alt Bar -->
    <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
        <p>© 2026 Ecrin Turizm. Tüm hakları saklıdır.</p>
        <p class="text-sm mt-2">
            A Grubu Seyahat Acentası İşletme Belgesi No: 9817
        </p>
    </div>
</footer>
```

---

## 🏠 ANA SAYFA İÇERİĞİ

### 1. Hero Bölümü

**ÖNEMLİ:** Hero bölümü temiz ve minimal tasarlanmalıdır. İstatistikler ayrı bölümde gösterilir.

```html
<section class="relative bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white py-20 md:py-32">
    <div class="container mx-auto px-4 text-center max-w-5xl">

        <!-- Üst Etiket -->
        <div class="animate-fadeIn mb-6">
            <span class="inline-block px-4 py-2 bg-blue-600/30 rounded-full text-sm font-semibold backdrop-blur-sm">
                Profesyonel Turizm Çözümleri
            </span>
        </div>

        <!-- Ana Başlık -->
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 animate-fadeIn">
            Güvenle Yolculuk,<br>Huzurla Varış
        </h1>

        <!-- Alt Başlık -->
        <p class="text-lg md:text-xl text-blue-100 mb-8 max-w-3xl mx-auto animate-fadeIn">
            2008'den beri A Grubu Seyahat Acentası olarak profesyonel
            taşımacılık hizmetleri sunuyoruz
        </p>

        <!-- CTA Butonlar -->
        <div class="flex flex-wrap gap-4 justify-center animate-fadeIn">
            <a href="hizmetlerimiz.html" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-all">
                <span>Hizmetlerimiz</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
            <a href="iletisim.html" class="inline-flex items-center gap-2 px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all border-2 border-blue-500">
                <span>İletişim</span>
                <i class="fa-solid fa-phone"></i>
            </a>
        </div>
    </div>
</section>
```

### 2. İstatistikler Bölümü

**ÖNEMLİ:** Tüm istatistikler simetrik ve tutarlı boyutlarda olmalıdır.

```html
<section class="bg-gradient-to-r from-blue-600 to-blue-700 py-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">

            <!-- İstatistik 1 -->
            <div>
                <div class="flex justify-center mb-4">
                    <i class="fa-solid fa-award text-5xl md:text-6xl"></i>
                </div>
                <div class="mb-2">
                    <div class="text-2xl md:text-3xl font-bold">A Grubu</div>
                </div>
                <div class="text-sm md:text-base text-blue-100">Seyahat Acentası</div>
            </div>

            <!-- İstatistik 2 -->
            <div>
                <div class="flex justify-center mb-4">
                    <i class="fa-solid fa-truck-fast text-5xl md:text-6xl"></i>
                </div>
                <div class="mb-2">
                    <div class="text-2xl md:text-3xl font-bold">Geniş</div>
                </div>
                <div class="text-sm md:text-base text-blue-100">Araç Filosu</div>
            </div>

            <!-- İstatistik 3 -->
            <div>
                <div class="flex justify-center mb-4">
                    <i class="fa-solid fa-user-group text-5xl md:text-6xl"></i>
                </div>
                <div class="mb-2">
                    <div class="text-2xl md:text-3xl font-bold">Binlerce</div>
                </div>
                <div class="text-sm md:text-base text-blue-100">Mutlu Müşteri</div>
            </div>

            <!-- İstatistik 4 -->
            <div>
                <div class="flex justify-center mb-4">
                    <i class="fa-solid fa-headset text-5xl md:text-6xl"></i>
                </div>
                <div class="mb-2">
                    <div class="text-2xl md:text-3xl font-bold">7/24</div>
                </div>
                <div class="text-sm md:text-base text-blue-100">Destek</div>
            </div>

        </div>
    </div>
</section>
```

### 3. Hizmetlerimiz Bölümü

```html
<section class="py-16 md:py-24">
    <div class="container mx-auto px-4">

        <!-- Başlık -->
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                Hizmetlerimiz
            </h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Profesyonel ve güvenilir taşımacılık çözümleri
            </p>
        </div>

        <!-- Hizmet Kartları Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- Kart 1: Turizm Taşımacılığı -->
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-solid fa-bus text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">
                    Turizm Taşımacılığı
                </h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Yurt içi ve yurt dışı turlarda konforlu, modern ve güvenli
                    araçlarımızla unutulmaz yolculuklar sunuyoruz.
                </p>
                <a href="tel:+905468101717" class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:gap-3 transition-all">
                    <span>Hemen Arayın</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Kart 2: Personel Taşımacılığı -->
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-solid fa-briefcase text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">
                    Personel Taşımacılığı
                </h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Kurumunuza özel personel servis çözümleri. Düzenli güzergah
                    planlaması ve zamanında varış garantisi.
                </p>
                <a href="tel:+905468101717" class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:gap-3 transition-all">
                    <span>Hemen Arayın</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Kart 3: Öğrenci Taşımacılığı -->
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-solid fa-graduation-cap text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">
                    Öğrenci Taşımacılığı
                </h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Güvenlik standartlarına uygun araçlarımızla çocuklarınızın
                    okul yolculuklarını güvenle tamamlıyoruz.
                </p>
                <a href="tel:+905468101717" class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:gap-3 transition-all">
                    <span>Hemen Arayın</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Kart 4: Otel Rezervasyonları -->
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-solid fa-hotel text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">
                    Otel Rezervasyonları
                </h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Anlaşmalı otellerimizle uygun fiyat garantisi. Tatil paketleri
                    ve grup rezervasyonları.
                </p>
                <a href="tel:+905468101717" class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:gap-3 transition-all">
                    <span>Hemen Arayın</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Kart 5: Yat Kiralama -->
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-solid fa-ship text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">
                    Yat Kiralama
                </h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Mavi yolculuğun keyfini premium yat kiralamaları ile çıkarın.
                    Kişiye özel rotalar ve organizasyonlar.
                </p>
                <a href="tel:+905468101717" class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:gap-3 transition-all">
                    <span>Hemen Arayın</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

        </div>
    </div>
</section>
```

### 4. Neden Ecrin Turizm?

```html
<section class="py-16 md:py-24 bg-gray-50">
    <div class="container mx-auto px-4">

        <!-- Başlık -->
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                Neden Ecrin Turizm?
            </h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Yıllardır güvenle hizmet veriyoruz
            </p>
        </div>

        <!-- Özellikler Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- Özellik 1 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-award text-4xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">
                    A Grubu Lisans
                </h3>
                <p class="text-gray-600">
                    A Grubu Seyahat Acentası İşletme Belgesi (No: 9817) ile
                    resmi güvence
                </p>
            </div>

            <!-- Özellik 2 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-truck-fast text-4xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">
                    Modern Araç Filosu
                </h3>
                <p class="text-gray-600">
                    Geniş ve modern araç filomuzla her ihtiyaca uygun çözümler
                </p>
            </div>

            <!-- Özellik 3 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-users text-4xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">
                    Profesyonel Kadro
                </h3>
                <p class="text-gray-600">
                    Deneyimli sürücü ve rehber kadromuzla güvenli yolculuklar
                </p>
            </div>

            <!-- Özellik 4 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-headset text-4xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">
                    7/24 Destek
                </h3>
                <p class="text-gray-600">
                    Kesintisiz destek hattımızla her an yanınızdayız
                </p>
            </div>

            <!-- Özellik 5 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-shield-halved text-4xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">
                    Tam Güvenlik
                </h3>
                <p class="text-gray-600">
                    Sigorta güvencesi ve güvenlik standartları ile hizmet
                </p>
            </div>

            <!-- Özellik 6 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-location-crosshairs text-4xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">
                    GPS Takip Sistemi
                </h3>
                <p class="text-gray-600">
                    Araçlarımız GPS takip sistemi ile donatılmıştır
                </p>
            </div>

        </div>
    </div>
</section>
```

### 5. Nasıl Çalışır?

```html
<section class="py-16 md:py-24">
    <div class="container mx-auto px-4">

        <!-- Başlık -->
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                Nasıl Çalışır?
            </h2>
            <p class="text-lg text-gray-600">
                3 basit adımda hizmetimizden yararlanın
            </p>
        </div>

        <!-- Adımlar -->
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <!-- Adım 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                        1
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">
                        Hizmet Seçin
                    </h3>
                    <p class="text-gray-600">
                        İhtiyacınıza uygun hizmet ve aracı belirleyin
                    </p>
                </div>

                <!-- Adım 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                        2
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">
                        Hizmet Alın
                    </h3>
                    <p class="text-gray-600">
                        Bizi arayın veya WhatsApp'tan yazın
                    </p>
                </div>

                <!-- Adım 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                        3
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">
                        Keyfinize Bakın
                    </h3>
                    <p class="text-gray-600">
                        Gerisini bize bırakın, güvenle yolculuk yapın
                    </p>
                </div>

            </div>
        </div>
    </div>
</section>
```

### 6. İletişim CTA Bölümü

**ÖNEMLİ:** Form kullanılmaz, sadece telefon ve WhatsApp odaklı iletişim.

```html
<section class="py-16 md:py-24 bg-gradient-to-r from-blue-600 to-blue-700">
    <div class="container max-w-4xl mx-auto px-4 text-center">

        <!-- Başlık -->
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6">
            Hemen Arayın veya WhatsApp'tan Yazın
        </h2>

        <p class="text-lg md:text-xl text-blue-100 mb-10">
            Profesyonel ekibimiz size yardımcı olmak için hazır
        </p>

        <!-- İletişim Butonları -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">

            <!-- Telefon Butonu -->
            <a href="tel:+905468101717" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-5 bg-white text-gray-900 rounded-xl hover:bg-gray-50 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1">
                <i class="fa-solid fa-phone text-2xl text-blue-600"></i>
                <div class="text-left">
                    <div class="text-xs text-gray-500">Hemen Arayın</div>
                    <div class="text-xl font-bold">0546 810 17 17</div>
                </div>
            </a>

            <!-- WhatsApp Butonu -->
            <a href="https://wa.me/905468101717" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-5 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1">
                <i class="fa-brands fa-whatsapp text-3xl"></i>
                <div class="text-left">
                    <div class="text-xs text-green-100">WhatsApp</div>
                    <div class="text-xl font-bold">Mesaj Gönderin</div>
                </div>
            </a>

        </div>
    </div>
</section>
```

---

## 📞 İLETİŞİM STRATEJİSİ

### Ana İlkeler

1. **Form Yok:** Teklif formu veya iletişim formu kullanılmaz
2. **Telefon Odaklı:** Birincil iletişim kanalı telefon
3. **WhatsApp Desteği:** İkincil iletişim kanalı WhatsApp
4. **Sosyal Medya Yok:** Hiçbir sosyal medya hesabı bulunmamaktadır

### İletişim Buton Stilleri

**Telefon Butonu (Primary):**
```html
<a href="tel:+905468101717" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg">
    <i class="fa-solid fa-phone"></i>
    <span>0546 810 17 17</span>
</a>
```

**WhatsApp Butonu (Secondary):**
```html
<a href="https://wa.me/905468101717" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition-all shadow-lg">
    <i class="fa-brands fa-whatsapp"></i>
    <span>WhatsApp</span>
</a>
```

### WhatsApp Mesaj Şablonları

**Ana Sayfadan:**
```
https://wa.me/905468101717?text=Merhaba%2C%20Ecrin%20Turizm%20hakkında%20bilgi%20almak%20istiyorum.
```

**Hizmetler Sayfasından:**
```
https://wa.me/905468101717?text=Merhaba%2C%20hizmetleriniz%20hakkında%20detaylı%20bilgi%20almak%20istiyorum.
```

---

## 🎨 TASARIM PRENSİPLERİ

### Genel Kurallar

**Spacing (Boşluklar):**
```html
<!-- Bölüm içi padding -->
py-16 md:py-24

<!-- Container -->
container mx-auto px-4 sm:px-6 lg:px-8

<!-- Kartlar arası gap -->
gap-6 md:gap-8
```

**Shadow (Gölgeler):**
```html
<!-- Kartlar -->
shadow-lg hover:shadow-xl

<!-- Butonlar -->
shadow-md hover:shadow-lg
```

**Transition (Geçişler):**
```html
<!-- Standart transition -->
transition-all duration-300 ease-in-out

<!-- Hover efektleri -->
hover:scale-105 hover:-translate-y-2
```

**Border Radius:**
```html
<!-- Küçük -->
rounded-lg        /* 8px */

<!-- Orta -->
rounded-xl        /* 12px */

<!-- Büyük -->
rounded-2xl       /* 16px */

<!-- Tam -->
rounded-full      /* 50% */
```

### Responsive Breakpoints

```css
/* Tailwind Breakpoints */
sm:   640px   /* Küçük tablet */
md:   768px   /* Tablet */
lg:   1024px  /* Küçük masaüstü */
xl:   1280px  /* Masaüstü */
2xl:  1536px  /* Büyük ekran */
```

**Kullanım Örneği:**
```html
<!-- Mobile: 1 kolon, Tablet: 2 kolon, Desktop: 3 kolon -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <!-- içerik -->
</div>
```

### Animasyonlar

**CSS Animasyonları:**
```html
<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.6s ease-out forwards;
}
</style>
```

**Kullanım:**
```html
<div class="animate-fadeIn" style="animation-delay: 0.2s;">
    <!-- İçerik -->
</div>
```

---

## ✅ KALİTE KONTROL LİSTESİ

### Her Sayfada Olması Gerekenler

- [ ] Responsive üst bar (telefon numarası mobilde görünür)
- [ ] Sticky header navigasyon
- [ ] Mobil hamburger menü
- [ ] Telefon + WhatsApp CTA butonları (header)
- [ ] Temiz hero bölümü
- [ ] Footer (sosyal medya linkı YOK)
- [ ] Copyright bilgisi
- [ ] Lisans numarası (9817)

### İçerik Kuralları

- [ ] Sadece doğrulanmış bilgiler kullanılmış
- [ ] Spesifik sayılar kullanılmamış (yerine genel terimler)
- [ ] "Teklif Al" formu YOK
- [ ] Sosyal medya linkleri YOK
- [ ] Telefon: 0546 810 17 17 (doğru)
- [ ] Email: info@ecrinturizm.org (doğru)
- [ ] Lisans No: 9817 (doğru)
- [ ] Kuruluş: 2008 (doğru)

### Tasarım Kontrolleri

- [ ] Hero bölümü sade ve temiz
- [ ] İstatistikler simetrik (text-2xl md:text-3xl)
- [ ] İkonlar tutarlı boyutlarda
- [ ] Renkler marka paletine uygun
- [ ] Buton stilleri tutarlı
- [ ] Hover efektleri çalışıyor
- [ ] Mobile responsive
- [ ] Animasyonlar yumuşak

---

## 🚀 TEKNİK NOTLAR

### Alpine.js Kullanımı

**Mobil Menü:**
```html
<div x-data="{ mobileMenuOpen: false }">
    <!-- Hamburger butonu -->
    <button @click="mobileMenuOpen = true">
        <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Mobil menü -->
    <div x-show="mobileMenuOpen"
         x-transition
         @click.away="mobileMenuOpen = false">
        <!-- Menü içeriği -->
    </div>
</div>
```

### Performans Optimizasyonu

**Lazy Loading:**
```html
<img src="image.jpg" loading="lazy" alt="Açıklama">
```

**Font Loading:**
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
```

---

## 📝 SONUÇ

Bu dokümantasyon, Ecrin Turizm web sitesi için güncel ve kapsamlı kılavuzdur.

**Önemli Hatırlatmalar:**
- Sosyal medya hesapları YOKTUR
- İletişim sadece telefon ve WhatsApp üzerinden
- Teklif formu KULLANILMAZ
- Sadece doğrulanmış bilgiler kullanılır
- Hero bölümleri temiz ve minimal
- İstatistikler simetrik

**Müşteri:** Ecrin Turizm San. ve Tic. Ltd. Şti.
**Versiyon:** 2.0
**Tarih:** 10 Ocak 2026
