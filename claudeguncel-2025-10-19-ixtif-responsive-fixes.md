# iXtif.com Responsive Tasarım Düzenlemeleri

**Tarih:** 2025-10-19
**Site:** https://ixtif.com/
**Durum:** Plan Aşaması - Onay Bekleniyor

---

## 📋 YAPILACAK DÜZENLEMELER

### 1. Container Padding Sorunu (1280px & 1024px)
**Sorun:** Ekran 1280px ve 1024px altına inince soldan sağdan boşluklar artıyor, container daralıyor.

**Çözüm:**
- Container'ların responsive padding değerlerini kontrol et
- 1024px-1280px arası için özel breakpoint ekle
- `px-4 sm:px-6 lg:px-8` yapısını gözden geçir
- Dar ekranlarda daha geniş görünmesi için padding azalt

**Dosyalar:**
- `Modules/Page/resources/views/themes/ixtif/show.blade.php` (tüm section'lar)
- `resources/views/themes/ixtif/layouts/header.blade.php` (container)

---

### 2. İstif Makinesi Linki - 1280px Altında Gizle
**Sorun:** 1280px altında desktop navigasyonda "İstif Makinesi" linki görünüyor olmamalı.

**Çözüm:**
- Header'da "İstif Makinesi" butonuna `hidden xl:flex` ekle (1280px üstünde görünsün)
- Mobile navigasyonda kalacak (zaten ayrı bir bölüm)

**Dosya:**
- `resources/views/themes/ixtif/layouts/header.blade.php` (line 577-584)

---

### 3. Hero Section Yükseklik Düzeltmesi
**Sorun:** `min-h-screen` olduğu için içerik dar olsa bile ekranı kaplıyor, üstte çok boşluk kalıyor.

**Çözüm:** `min-h-screen` kaldır, içeriğe göre yükseklik alsın.

**Dosya:**
- `Modules/Page/resources/views/themes/ixtif/show.blade.php` (line 65)

---

### 4. Hero Bölümü Özellik Kutuları - Mobilde 2x2 Grid + 1 Özellik Ekle
**Mevcut Durum:** 3 kutu var (Güçlü Stok, Garantili Ürün, Profesyonel Ekip)

**Hedef:** 4 kutu olacak, mobilde 2x2 grid

**Eklenecek 1 Yeni Özellik:**
1. **Hızlı Teslimat** - fa-truck-fast - "Aynı gün kargo"

**Grid Yapısı:**
- Desktop: `sm:grid-cols-4` (1 satır x 4 sütun)
- Mobile: `grid-cols-2` (2 satır x 2 sütun)

**Dosya:**
- `Modules/Page/resources/views/themes/ixtif/show.blade.php` (line 104-132)

---

### 5. Ürün Kartları - Glass Efekt Ekle
**Hedef:** Ürün kartları contact cards gibi glass efekt alacak

**Glass Efekt Classları:**
```blade
bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10
```

**Dosya:**
- `Modules/Page/resources/views/themes/ixtif/show.blade.php` (line 254 - product cards)

---

### 6. Ürün Grup Kartları - Border Ekle (Light Mode Fix)
**Sorun:** Light mode'da ürün grup kartlarının border'ı görünmüyor

**Çözüm:** Güçlü border ekle
```blade
border-2 border-gray-200 dark:border-white/10
```

**Dosya:**
- `Modules/Page/resources/views/themes/ixtif/show.blade.php` (line 170 - category cards)

---

### 7. Light Mode Kontrast Güçlendirme
**Sorun:** Light mode'da kontrastlar zayıf, dark mode kadar güçlü olmalı

**Çözüm:**
- Ürün kartları: Daha belirgin border ve shadow
- Grup kartları: Güçlü border ve hover efektleri
- Genel: bg-gray-50/80 gibi yarı saydam değil, solid renkler

**Tüm kartlar gözden geçirilecek**

---

---

### 8. Ürün Kategorileri - Mobilde 2x2 Grid
**Mevcut:** 4 kutu (Forklift, Transpalet, İstif Makinesi, Reach Truck) - desktop 4'lü, mobile 1'li

**Hedef:** Mobile'de 2x2 grid

**Değişiklik:**
```blade
<!-- Eski -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

<!-- Yeni -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
```

**Dosya:**
- `Modules/Page/resources/views/themes/ixtif/show.blade.php` (line 168)

---

### 5. Hizmet Kategorileri - V2 Sil, V3 Mobilde 3x2 Yap
**Durum:** 2 ayrı section var:
- **V2 (line 408-484):** 5 kutu - SİLİNECEK
- **V3 (line 486-587):** 6 kutu - KULLANILACAK

**V3 Düzenleme:**
- Desktop: Mevcut yapı kalacak (flex row)
- Mobile: `grid grid-cols-3 gap-4` (2 satır x 3 sütun)
- Separatorları mobilde gizle

**Dosyalar:**
- `Modules/Page/resources/views/themes/ixtif/show.blade.php`
  - V2 Section (407-484): **SİL**
  - V3 Section (486-587): **Responsive düzenle**

---

### 6. İletişim Butonları - Mobilde 2x2 Grid
**Mevcut:** `grid-cols-1 md:grid-cols-2 lg:grid-cols-4`

**Hedef:** Mobile'de 2x2

**Değişiklik:**
```blade
<!-- Eski -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

<!-- Yeni -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
```

**Dosya:**
- `Modules/Page/resources/views/themes/ixtif/show.blade.php` (line 593)

---

### 7. Canlı Destek Butonu - Chat Açma Fonksiyonu
**Durum:** Zaten çalışıyor (line 625)

**Kontrol:**
```javascript
onclick="if(window.$store && window.$store.aiChat) { window.$store.aiChat.openFloating(); } else if(window.openAIChat) { window.openAIChat(); }"
```

**Aksiyon:** Test edilecek, sorun yoksa dokunulmayacak.

---

### 8. Footer Copyright Tasarımı
**Mevcut Durum:**
```
2025 iXtif. Tüm hakları saklıdır. | Gizlilik Politikası • Kullanım Koşulları • KVKK
```

**Hedef:**
```
2025 [Kurum Adı]. Tüm hakları saklıdır.
Gizlilik Politikası | Kullanım Koşulları | KVKK
```

**Düzenleme:**
- "iXtif" yerine `$companyName` veya `$siteTitle` kullan
- Gizlilik linkleri ayrı satıra al
- Mobilde de düzgün görünsün

**Dosya:**
- `resources/views/themes/ixtif/layouts/footer.blade.php` (line 220-227)

---

### 9. Schema/Sitemap Linkleri - Mobilde Göster
**Mevcut:** `hidden lg:block` (sadece desktop'ta görünüyor)

**Hedef:** Mobilde de görünsün

**Değişiklik:**
```blade
<!-- Eski -->
<div class="hidden lg:block ...">

<!-- Yeni -->
<div class="block ...">
```

**Not:** Mobile'de küçük font ve kompakt tasarım kullanılacak

**Dosya:**
- `resources/views/themes/ixtif/layouts/footer.blade.php` (line 231)

---

## 🎯 DEĞİŞTİRİLECEK DOSYALAR

1. ✅ `Modules/Page/resources/views/themes/ixtif/show.blade.php`
   - Container padding
   - Hero özellikler (3→6 kutu, 2x3 grid)
   - Kategori kartları (mobil 2x2)
   - Hizmet V2 section SİL
   - Hizmet V3 mobil 3x2
   - İletişim kartları (mobil 2x2)

2. ✅ `resources/views/themes/ixtif/layouts/header.blade.php`
   - Container padding
   - İstif Makinesi link gizle (1280px altı)

3. ✅ `resources/views/themes/ixtif/layouts/footer.blade.php`
   - Copyright tasarım (kurum adı + linkler ayrı satır)
   - Schema/sitemap mobilde göster

---

## ⚠️ DİKKAT EDİLECEKLER

1. **Dark Mode:** Tüm değişiklikler dark mode ile uyumlu olacak
2. **Tailwind Breakpoints:**
   - `sm:` 640px+
   - `md:` 768px+
   - `lg:` 1024px+
   - `xl:` 1280px+
3. **Gap/Padding:** Mobilde daha kompakt, desktop'ta ferah
4. **Icon & Text:** Mobilde küçültülecek yerler belirtilecek

---

## ✅ ONAY SONRASI ADIMLAR

1. ✅ Her dosyayı teker teker düzenle
2. ✅ Her değişiklikten sonra todo'yu güncelle
3. ✅ Tüm değişiklikler bitince özet sun
4. ✅ Kullanıcı test etsin

---

**Hazırlayan:** Claude
**Onay Durumu:** ⏳ Bekliyor
