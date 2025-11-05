# 🛒 Google Merchant Center Kurulum Rehberi

**Tarih:** 2025-11-05
**Tenant:** ixtif.com (Tenant ID: 2)
**Feed URL:** `https://ixtif.com/productfeed`

---

## 📋 ÖN HAZIRLIK

### ✅ Sistem Kontrolü (Tamamlandı)
- ✅ Google Shopping Feed sistemi mevcut
- ✅ Feed URL: `https://ixtif.com/productfeed`
- ✅ Tenant-aware feed (otomatik domain algılama)
- ✅ 500 ürün limiti (gerekirse artırılabilir)
- ✅ SSL sertifikası aktif
- ✅ JSON çoklu dil desteği (tr/en)

### 📝 İhtiyaç Duyulan Bilgiler

**İşletme Bilgileri:**
- Şirket/Marka Adı: _(site_name ayarından alınıyor)_
- Vergi Numarası
- Adres (fiziksel işletme adresi)
- Telefon
- E-posta (iletişim)

**Gerekli Sayfa Bağlantıları:**
- İade/Değişim Politikası: `https://ixtif.com/...` _(oluşturulmalı)_
- Gizlilik Politikası: `https://ixtif.com/...` _(oluşturulmalı)_
- Kullanım Koşulları: `https://ixtif.com/...` _(oluşturulmalı)_

**Kargo/Teslimat Bilgileri:**
- Kargo şirketi
- Teslimat süreleri (gün)
- Kargo ücretleri (bölgesel)
- Ücretsiz kargo limiti (varsa)

---

## 🚀 ADIM ADIM KURULUM

### 1️⃣ Google Merchant Center Hesabı Oluşturma

**URL:** https://merchants.google.com

1. **Google hesabı ile giriş yap**
   - İşletme e-postası önerilir
   - Google Ads ile aynı hesap kullanılabilir

2. **İşletme bilgilerini gir:**
   - İşletme adı (örn: "IXTIF Endüstriyel Ekipman")
   - Ülke: Türkiye
   - Saat dilimi: Europe/Istanbul (GMT+3)

3. **İşletme türünü seç:**
   - Seçenek 1: "B2B ve B2C" (önerilir)
   - Seçenek 2: "Sadece B2B"

4. **Web sitesi adresini doğrula:**
   - Domain: `ixtif.com`
   - Doğrulama yöntemi: **HTML tag** (önerilir)

---

### 2️⃣ Web Sitesi Doğrulama (HTML Tag Yöntemi)

Google, meta tag doğrulaması isteyecek:

```html
<meta name="google-site-verification" content="XXXXXXXXXXXXXXXXXXXXXXXX" />
```

**Ekleme Konumu:**
1. Layout dosyasına eklenecek: `Modules/Shop/resources/views/layouts/app.blade.php`
2. `<head>` tagı içine ekle
3. Cache temizle, build yap
4. Google Merchant Center'da "Doğrula" butonuna tıkla

**Örnek:**
```blade
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Merchant Center Verification -->
    <meta name="google-site-verification" content="MERCHANT-CENTER-TAG-BURAYA" />

    <title>{{ $title ?? config('app.name') }}</title>
    ...
</head>
```

---

### 3️⃣ Ürün Feed'i Ekleme

**Merchant Center → Ürünler → Feedler → Feed Ekle**

1. **Feed türü:** "Planlı getirme" (Scheduled fetch)
2. **Ülke:** Türkiye
3. **Dil:** Türkçe
4. **Hedefler:** Google Shopping (Free listings + Ads)

**Feed Ayarları:**
- **Feed adı:** "ixtif.com Google Shopping Feed"
- **Dosya adı veya URL:** `https://ixtif.com/productfeed`
- **Fetch sıklığı:** Günlük (önerilir: her gün 03:00)
- **Zaman dilimi:** Europe/Istanbul

**Gelişmiş Ayarlar:**
- Format: RSS/XML (otomatik algılanır)
- Karakter kümesi: UTF-8
- Sıkıştırma: Yok

**İlk Fetch:**
- "Şimdi getir" butonuna tıkla
- 5-10 dakika içinde ürünler yüklenecek

---

### 4️⃣ Politika Sayfalarını Ekleme

**⚠️ KRİTİK: Bu sayfalar olmadan onay alınamaz!**

**Gerekli Sayfalar:**
1. İade ve Değişim Politikası
2. Gizlilik Politikası
3. Kullanım Koşulları (Hizmet Şartları)

**Ekleme:**
- Page modülünde oluştur
- Footer'a link ekle
- Merchant Center → Ayarlar → Web sitesi → Politikalar → Linkler ekle

**Örnek URL'ler:**
```
https://ixtif.com/iade-ve-degisim-politikasi
https://ixtif.com/gizlilik-politikasi
https://ixtif.com/kullanim-kosullari
```

---

### 5️⃣ Teslimat/Kargo Ayarları

**Merchant Center → Ayarlar → Gönderim ve İadeler**

**Teslimat Ayarları:**
1. **Teslimat hizmeti adı:** "Standart Kargo"
2. **Teslimat süresi:** 3-7 iş günü
3. **Teslimat bölgeleri:** Türkiye (tüm iller)
4. **Kargo ücreti:**
   - Sabit ücret: 50 TL (örnek)
   - Ücretsiz kargo eşiği: 1000 TL (örnek)

**İade Ayarları:**
1. **İade kabul süresi:** 14 gün
2. **İade kargo ücreti:** Müşteri öder / Satıcı öder
3. **İade politikası URL:** `https://ixtif.com/iade-politikasi`

---

### 6️⃣ Vergi Ayarları

**Merchant Center → Ayarlar → Vergi**

**Türkiye için:**
- Vergi oranı: %20 KDV (varsayılan)
- Vergi politikası: "Fiyatlara dahil" (önerilir)

---

### 7️⃣ Google Ads Hesabına Bağlama

**⚠️ Merchant Center ve Google Ads bağlantısı gerekli!**

**Adımlar:**
1. **Merchant Center → Ayarlar → Bağlantılı hesaplar**
2. **Google Ads → Bağlantı ekle**
3. **Google Ads Müşteri Kimliği gir** (örn: 123-456-7890)
4. **İzin düzeyi:** "Standart" (önerilir)
5. **İstek gönder**

**Google Ads tarafında onaylama:**
1. Google Ads → Araçlar → Bağlantılı hesaplar
2. Google Merchant Center → Bekleyen istek
3. **Onayla**

**Bağlantı Testi:**
- Merchant Center'da bağlantı durumu "Aktif" görünmeli
- Google Ads'te Merchant Center simgesi yeşil olmalı

---

### 8️⃣ Maksimum Performans Kampanyası Oluşturma

**Google Ads → Kampanyalar → Yeni Kampanya**

**Kampanya Türü:**
- **Hedef:** Satış / Olası müşteriler
- **Kampanya türü:** Maksimum Performans (Performance Max)

**Kampanya Ayarları:**
1. **Kampanya adı:** "ixtif.com - Maksimum Performans"
2. **Bütçe:** Günlük bütçe belirle (örn: 100 TL/gün)
3. **Hedef ROAS:** (İsteğe bağlı - başlangıçta kullanma)

**Varlık Grubu (Asset Group):**
1. **İşletme adı:** "IXTIF Endüstriyel Ekipman"
2. **Görseller:** Logo, banner, ürün görselleri ekle
3. **Başlıklar:** 5-15 başlık yaz (örn: "Kaliteli Transpalet", "Endüstriyel Ekipman")
4. **Uzun başlıklar:** 1-5 uzun başlık (90 karakter)
5. **Açıklamalar:** 4-5 açıklama (90 karakter)

**Merchant Center Bağlantısı:**
- **Ürün feed'i:** "ixtif.com Google Shopping Feed" seç
- **Tüm ürünler** veya **belirli kategoriler** seç

**Hedef Kitle:**
- Coğrafi konum: Türkiye (tüm iller)
- Dil: Türkçe
- Demografi: Tümü (özelleştirilebilir)

**Son Adımlar:**
1. **Kampanya URL parametreleri:** (isteğe bağlı)
   ```
   utm_source=google&utm_medium=cpc&utm_campaign=performance_max
   ```
2. **Dönüşüm takibi:** Google Analytics veya Google Ads dönüşüm takibi ekle
3. **Kampanyayı yayınla**

---

## 🔍 SORUN GİDERME

### Feed Hataları

**"Ürünler yüklenemiyor"**
- Feed URL'ini kontrol et: `https://ixtif.com/productfeed`
- Browser'da aç, XML görünmeli
- SSL sertifikası geçerli mi kontrol et

**"Fiyat bilgisi eksik"**
- `base_price` NULL veya 0 olan ürünler feed'e eklenmez
- `price_on_request = true` olan ürünler fiyat göstermez (bazı kategorilerde kabul edilir)

**"Ürün açıklaması çok kısa"**
- `body` veya `short_description` alanlarını doldur
- En az 50 karakter önerilir
- Feed otomatik HTML tag'lerini temizler

### Doğrulama Sorunları

**"İade politikası bulunamadı"**
- Footer'da link olmalı
- Sayfa erişilebilir olmalı (404 olmamalı)
- Link Merchant Center'a eklenmeli

**"Web sitesi doğrulanmadı"**
- Meta tag doğru eklendi mi kontrol et
- Cache temizle, build yap
- Browser'da kaynak kodunu görüntüle, tag görünmeli

### Google Ads Bağlantı Sorunları

**"Bağlantı beklemede"**
- Google Ads hesabında onayla
- Merchant Center ve Google Ads aynı Google hesabına bağlı olmalı

**"Merchant Center ürünleri kampanyada görünmüyor"**
- Feed onay aldı mı kontrol et
- Ürünler "Aktif" durumda mı kontrol et
- Kampanya hedef ülkesi ile feed ülkesi aynı olmalı

---

## 📊 PERFORMANS TAKİBİ

### Merchant Center Metrikleri
- **Gösterim sayısı:** Ürünlerin kaç kez gösterildiği
- **Tıklama sayısı:** Feed'den gelen tıklamalar
- **Tıklama oranı (CTR):** Gösterim/Tıklama oranı

### Google Ads Metrikleri
- **Dönüşüm sayısı:** Satış/Lead
- **Dönüşüm değeri:** Toplam gelir
- **ROAS:** Return on Ad Spend (Harcama başına gelir)
- **Maliyet:** Harcanan reklam bütçesi

### Optimizasyon Önerileri
1. **Ürün görselleri:** Yüksek çözünürlüklü (800x800px+)
2. **Ürün başlıkları:** Net ve açıklayıcı (örn: "1.5 Ton Transpalet - IXTIF")
3. **Fiyatlandırma:** Rekabetçi fiyatlar
4. **Stok durumu:** Güncel stok bilgisi (şu an her ürün "in stock")
5. **Feed güncelleme:** Günlük fetch önerilir

---

## ✅ KONTROL LİSTESİ

### Merchant Center Onboarding
- [ ] Google Merchant Center hesabı oluşturuldu
- [ ] Web sitesi doğrulandı (HTML tag)
- [ ] İşletme bilgileri tamamlandı
- [ ] Feed eklendi (`https://ixtif.com/productfeed`)
- [ ] İlk feed fetch tamamlandı
- [ ] Ürünler "Aktif" durumda
- [ ] İade politikası sayfası oluşturuldu ve eklendi
- [ ] Gizlilik politikası sayfası oluşturuldu ve eklendi
- [ ] Kullanım koşulları sayfası oluşturuldu ve eklendi
- [ ] Teslimat/kargo ayarları yapıldı
- [ ] Vergi ayarları yapıldı

### Google Ads Entegrasyonu
- [ ] Google Ads hesabı mevcut
- [ ] Merchant Center - Google Ads bağlantısı kuruldu
- [ ] Bağlantı onaylandı (her iki tarafta)
- [ ] Maksimum Performans kampanyası oluşturuldu
- [ ] Varlık grubu (Asset Group) tamamlandı
- [ ] Merchant Center feed'i kampanyaya eklendi
- [ ] Hedef kitle ayarları yapıldı
- [ ] Dönüşüm takibi kuruldu
- [ ] Kampanya yayına alındı

### Post-Launch
- [ ] Feed günlük güncelleniyor
- [ ] Ürünler onay aldı (Merchant Center)
- [ ] Kampanya aktif ve gösterim alıyor
- [ ] Tıklamalar geliyor
- [ ] Dönüşümler takip ediliyor

---

## 📞 DESTEK

**Google Merchant Center Yardım:**
- https://support.google.com/merchants

**Google Ads Yardım:**
- https://support.google.com/google-ads

**Feed URL Test:**
```bash
curl -s https://ixtif.com/productfeed | head -100
```

**Feed Ürün Sayısı Kontrolü:**
```bash
curl -s https://ixtif.com/productfeed | grep -o '<item>' | wc -l
```

---

**Hazırlayan:** Claude AI
**Son Güncelleme:** 2025-11-05
