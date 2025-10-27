# 📚 İXTİF SAYFALARI İÇİN KÜTÜPHANELER

> **Onaylandı:** Dışardan kütüphane çekebilirim!
> **Tarih:** 2025-10-23

---

## ✅ KULLANILACAK KÜTÜPHANELER

### 🎨 ANİMASYON & EFEKTLER

#### 1. **AOS (Animate On Scroll)**
```html
<!-- CSS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<!-- JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    once: true,
    offset: 100
  });
</script>
```

**Kullanım:**
```html
<div data-aos="fade-up">Animasyonlu içerik</div>
<div data-aos="zoom-in" data-aos-delay="200">Gecikmeli animasyon</div>
```

**Nerede:** Hakkımızda, Hizmetler, Referanslar

---

#### 2. **CountUp.js (Sayı Animasyonu)**
```html
<script src="https://cdn.jsdelivr.net/npm/countup@1.8.2/dist/countUp.min.js"></script>
```

**Kullanım:**
```javascript
// Sayılarla iXtif (1,020 ürün animasyonu)
const productCount = new CountUp('product-count', 0, 1020, 0, 2.5);
productCount.start();
```

**Nerede:** Hakkımızda (Sayılarla iXtif bölümü)

---

#### 3. **Typed.js (Yazma Animasyonu)**
```html
<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
```

**Kullanım:**
```javascript
new Typed('#typed', {
  strings: ['Forklift', 'Transpalet', 'İstif Makinesi', 'Reach Truck'],
  typeSpeed: 50,
  backSpeed: 30,
  loop: true
});
```

**Nerede:** Anasayfa hero (isteğe bağlı)

---

### 🗺️ HARİTA & LOKASYONs

#### 4. **Google Maps Embed API**
```html
<iframe
  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3014.123!2d29.123!3d40.123!..."
  width="100%"
  height="450"
  style="border:0;"
  allowfullscreen=""
  loading="lazy"
  referrerpolicy="no-referrer-when-downgrade">
</iframe>
```

**Kullanım:** İletişim sayfası (Kartal/Küçükyalı konum)

**Alternatif: Leaflet.js (Açık kaynak)**
```html
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
```

---

### 📋 FORM & VALİDASYON

#### 5. **jQuery Validation (İsteğe Bağlı)**
```html
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
```

**Alpine.js ile yaparız, jQuery'ye gerek yok!**

---

#### 6. **Dropzone.js (Dosya Upload)**
```html
<link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet">
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
```

**Kullanım:** Kariyer başvuru formu (CV upload)

**Alternatif: FilePond (Modern)**
```html
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
```

**Nerede:** Kariyer, KVKK Başvuru

---

### 🎠 SLIDER & CAROUSEL

#### 7. **Swiper.js**
```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
```

**Kullanım:**
```javascript
const swiper = new Swiper('.swiper', {
  slidesPerView: 4,
  spaceBetween: 30,
  loop: true,
  autoplay: {
    delay: 3000,
  },
  breakpoints: {
    640: { slidesPerView: 2 },
    1024: { slidesPerView: 4 }
  }
});
```

**Nerede:** Referanslar (Logo slider)

---

#### 8. **Splide.js (Alternatif - Daha hafif)**
```html
<link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
```

---

### 🍪 ÇEREZ YÖNETİMİ

#### 9. **CookieConsent.js**
```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css">
<script src="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.js"></script>
```

**Kullanım:**
```javascript
window.cookieconsent.initialise({
  palette: {
    popup: { background: "#1e293b" },
    button: { background: "#2563eb" }
  },
  content: {
    message: "Bu site çerez kullanmaktadır.",
    dismiss: "Anladım",
    link: "Detaylı Bilgi",
    href: "/cerez-politikasi"
  }
});
```

**Nerede:** Tüm sayfalarda (footer)

---

### 🎯 ACCORDION & TABS

#### 10. **Alpine.js Native (Kütüphane gereksiz!)**
```html
<!-- Accordion (SSS) -->
<div x-data="{ open: null }">
  <div @click="open = open === 1 ? null : 1">
    <h3>Soru 1</h3>
    <div x-show="open === 1" x-collapse>Cevap 1</div>
  </div>
</div>

<!-- Tabs (Kategoriler) -->
<div x-data="{ tab: 'urunler' }">
  <button @click="tab = 'urunler'">Ürünler</button>
  <button @click="tab = 'fiyat'">Fiyat</button>
  <div x-show="tab === 'urunler'">İçerik 1</div>
</div>
```

**Alpine yeterli, kütüphane çekmeye gerek yok!**

---

### 📊 CHART & GRAFİK

#### 11. **Chart.js (İstatistikler için)**
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
```

**Kullanım:**
```javascript
const ctx = document.getElementById('myChart');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['2020', '2021', '2022', '2023', '2024'],
    datasets: [{
      label: 'Satış Grafiği',
      data: [12, 19, 3, 5, 2]
    }]
  }
});
```

**Nerede:** Hakkımızda (opsiyonel), Basın Odası

---

### 🔍 SEARCH & FİLTER

#### 12. **List.js (Filtreleme)**
```html
<script src="https://cdn.jsdelivr.net/npm/list.js@2.3.1/dist/list.min.js"></script>
```

**Kullanım:**
```javascript
const options = {
  valueNames: ['title', 'category', 'price']
};
const ssList = new List('sss-list', options);
```

**Nerede:** SSS (arama), Referanslar (sektör filtresi)

**Alternatif: Alpine.js ile yapabiliriz!**

---

### 🎨 ICON PACKS

#### 13. **Font Awesome Pro 6.x (Mevcut)**
```html
<!-- Zaten sistemde var -->
<i class="fa-light fa-forklift"></i>
<i class="fa-brands fa-whatsapp"></i>
```

#### 14. **Lucide Icons (Modern Alternatif)**
```html
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();
</script>
```

**Kullanım:**
```html
<i data-lucide="truck"></i>
<i data-lucide="phone"></i>
```

---

### 📱 MODAL & POPUP

#### 15. **Alpine.js Native Modal**
```html
<div x-data="{ open: false }">
  <button @click="open = true">Aç</button>

  <div x-show="open"
       x-transition
       @click.away="open = false"
       class="fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg">
      Modal içerik
    </div>
  </div>
</div>
```

**Alpine yeterli!**

---

### 🎥 VİDEO PLAYER

#### 16. **Plyr.js (Modern Video Player)**
```html
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
<script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
```

**Kullanım:**
```html
<video class="plyr-video">
  <source src="/video/forklift-tanitim.mp4" type="video/mp4">
</video>
<script>
  const player = new Plyr('.plyr-video');
</script>
```

**Nerede:** Hizmetler, Hakkımızda (tanıtım videosu)

---

### 📸 LIGHTBOX & GALLERY

#### 17. **GLightbox (Modern Lightbox)**
```html
<link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
```

**Kullanım:**
```html
<a href="image-large.jpg" class="glightbox">
  <img src="image-thumb.jpg" alt="Forklift">
</a>
<script>
  const lightbox = GLightbox();
</script>
```

**Nerede:** Referanslar (case study görselleri)

---

### 🔔 NOTİFİCATİON & TOAST

#### 18. **Notyf (Modern Toast)**
```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
```

**Kullanım:**
```javascript
const notyf = new Notyf();
notyf.success('Form başarıyla gönderildi!');
notyf.error('Bir hata oluştu!');
```

**Nerede:** Form gönderimi (İletişim, Kariyer, KVKK)

---

### 📅 DATE PICKER

#### 19. **Flatpickr (Tarih Seçici)**
```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
```

**Kullanım:**
```javascript
flatpickr("#date-input", {
  dateFormat: "d.m.Y",
  locale: "tr"
});
```

**Nerede:** Servis randevu (gelecekte)

---

### 🛡️ GÜVENLİK & SPAMspection

#### 20. **Google reCAPTCHA v3**
```html
<script src="https://www.google.com/recaptcha/api.js"></script>
```

**Kullanım:**
```html
<div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY"></div>
```

**Nerede:** Tüm formlar (İletişim, Kariyer, KVKK)

---

## 📦 KULLANIM STRATEJİSİ

### ✅ MUTLAKA KULLANALIM

1. **AOS** → Scroll animasyonları (tüm sayfalarda)
2. **CountUp.js** → Sayı animasyonları (Hakkımızda)
3. **Google Maps** → Harita (İletişim)
4. **Swiper.js** → Logo slider (Referanslar)
5. **CookieConsent.js** → Çerez banner (tüm sayfalarda)
6. **FilePond** → Dosya upload (Kariyer, KVKK)
7. **Notyf** → Toast notifications (formlar)

### 🟡 İSTEĞE BAĞLI

8. **Chart.js** → İstatistik grafikleri (gelecekte)
9. **Plyr.js** → Video player (tanıtım videosu eklenirse)
10. **GLightbox** → Image gallery (case study)
11. **reCAPTCHA** → Spam koruması (formlar)

### ❌ KULLANMAYALIM

- jQuery → Alpine.js yeterli
- Bootstrap JS → Tailwind kullanıyoruz
- Lodash → Vanilla JS yeterli
- Moment.js → Date-fns veya native

---

## 🎯 SAYFA BAZINDA KÜTÜPHANE PLANI

### 1. HAKKIMIZDA
```html
✅ AOS (scroll animations)
✅ CountUp.js (1,020 ürün, 106 kategori animasyonu)
```

### 2. İLETİŞİM
```html
✅ Google Maps Embed
✅ Notyf (form başarı/hata)
🟡 reCAPTCHA (spam koruması)
```

### 3. HİZMETLER
```html
✅ AOS (card animations)
🟡 Plyr.js (hizmet tanıtım videosu)
```

### 4. SSS
```html
✅ Alpine.js (accordion - native)
🟡 List.js (arama/filtreleme) VEYA Alpine ile yap
```

### 5. REFERANSLAR
```html
✅ Swiper.js (logo slider)
✅ AOS (fade-in animations)
🟡 GLightbox (case study görselleri)
```

### 6. KARİYER
```html
✅ FilePond (CV upload)
✅ Notyf (başvuru bildirimi)
🟡 reCAPTCHA (spam koruması)
```

### 7. TÜM HUKUKİ SAYFALAR
```html
✅ Alpine.js (sidebar navigation)
✅ AOS (fade-in)
```

### 8. GENEL (Footer - Tüm sayfalarda)
```html
✅ CookieConsent.js (çerez banner)
```

---

## 📝 ÖRNEK CDN TEMPLATE

**Her sayfada kullanılacak base template:**

```html
<!DOCTYPE html>
<html lang="tr" x-data="{ darkMode: localStorage.getItem('darkMode') || 'light' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS (Mevcut) -->
    <link href="/css/app.css" rel="stylesheet">

    <!-- Font Awesome Pro (Mevcut) -->

    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- CookieConsent CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css">

    <!-- Sayfa özel CSS -->
    <style>{{ $page->css }}</style>
</head>
<body>
    <!-- İçerik -->
    {!! $page->body !!}

    <!-- Alpine.js (Mevcut) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ duration: 800, once: true });</script>

    <!-- CookieConsent JS -->
    <script src="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.js"></script>

    <!-- Sayfa özel JS -->
    <script>{{ $page->js }}</script>
</body>
</html>
```

---

## 🚀 HAZIR!

**Kütüphaneler belirlendi, istediğin zaman BAŞLAYALIM!** 🎨

Hangi sayfadan başlamak istersin?
