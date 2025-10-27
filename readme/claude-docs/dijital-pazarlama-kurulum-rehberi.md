# 🎯 DİJİTAL PAZARLAMA PLATFORMLARI - KURULUM REHBERİ

**Tarih:** 2025-10-26
**Proje:** ixtif.com (Tenant 2)
**Durum:** Admin panel hazır, platform ID'leri bekleniyor

---

## 📋 TAMAMLANAN İŞLEMLER

### ✅ 1. Database Yapısı (TAMAMLANDI)
- [x] Settings tablosuna 9 yeni alan eklendi
- [x] GTM ID kaydedildi: `GTM-P8HKHCG9`
- [x] Clarity ID kaydedildi: `tvzxzyip9e`
- [x] Admin panel layout güncellendi

### ✅ 2. Admin Panel (TAMAMLANDI)
- [x] Form builder layout düzenlendi
- [x] "Dijital Pazarlama Platformları" bölümü eklendi
- [x] Tüm alanlar admin panelde görünür: https://ixtif.com/admin/settingmanagement/values/8

---

## 🚀 YAPMAMIZ GEREKEN İŞLER (SIRASIZ DEĞIL!)

### 📍 AŞAMA 1: PLATFORM HESAPLARI OLUŞTUR (KULLANICI)

#### 1.1. Google Tag Manager (ZORUNLU - 5 dakika)
**Ne yapacaksın:**
1. https://tagmanager.google.com adresine git
2. Sağ üstte "Create Account" tıkla
3. **Account Setup:**
   - Account Name: `iXtif`
   - Country: `Turkey`
   - Continue tıkla
4. **Container Setup:**
   - Container name: `ixtif.com`
   - Target platform: `Web` seç
   - Create tıkla
5. **GTM Container ID'yi kopyala:**
   - Örnek: `GTM-P8HKHCG9` (SENİNKİ ZATEN VAR!)
6. **Popup'ı KAPAT** (snippet kodunu şimdi ekleme, Claude ekleyecek)

**Ne elde edeceksin:**
- ✅ GTM Container ID (zaten var: GTM-P8HKHCG9)

---

#### 1.2. Microsoft Clarity (ÖNERİLİR - 3 dakika)
**Ne yapacaksın:**
1. https://clarity.microsoft.com adresine git
2. Microsoft hesabınla giriş yap (varsa) veya oluştur
3. "Add new project" tıkla
4. **Project Setup:**
   - Name: `ixtif.com`
   - Website URL: `https://ixtif.com`
   - Category: `E-commerce` veya `Business`
   - Create tıkla
5. **Clarity Project ID'yi kopyala:**
   - Örnek: `tvzxzyip9e` (SENİNKİ ZATEN VAR!)
6. **Setup instructions** popup'ını KAPAT

**Ne elde edeceksin:**
- ✅ Clarity Project ID (zaten var: tvzxzyip9e)
- ✅ ÜCRETSIZ heatmap + session replay

---

#### 1.3. Google Ads Conversion Tracking (REKLAM BAŞLATINCA - 10 dakika)
**Ne yapacaksın:**
1. https://ads.google.com adresine git
2. Reklam hesabına giriş yap
3. **Tools & Settings** (sağ üst) → **Measurement** → **Conversions** tıkla
4. **+ New conversion action** tıkla
5. **Website** seç
6. **Conversion action setup:**
   - Category: `Lead` veya `Submit lead form`
   - Conversion name: `Form Submission`
   - Value: Her dönüşüm için aynı değer (örn: 50 TL)
   - Count: `One` (her form için 1 kez)
   - Create and continue tıkla
7. **Tag setup:**
   - "Use Google Tag Manager" SEÇ
   - **Conversion ID** ve **Conversion Label**'ı kopyala
8. **Tekrar et:** Telefon tıklama için de ayrı conversion oluştur
   - Conversion name: `Phone Click`
   - Category: `Lead`

**Ne elde edeceksin:**
- ✅ Conversion ID: `AW-XXXXXXXXXX` (örnek)
- ✅ Form Conversion Label: `AbC-123xyz` (örnek)
- ✅ Phone Conversion Label: `XyZ-456abc` (örnek)

---

#### 1.4. Facebook Pixel (REKLAM BAŞLATINCA - 5 dakika)
**Ne yapacaksın:**
1. https://business.facebook.com/events_manager adresine git
2. **Data Sources** → **Add** tıkla
3. **Web** seç
4. **Connect Data Sources:**
   - Pixel name: `ixtif.com Pixel`
   - Website URL: `https://ixtif.com`
   - Create tıkla
5. **Set Up Pixel:**
   - "Manually Install Pixel Code Yourself" SEÇ
   - **Pixel ID'yi kopyala** (15 haneli numara)
6. Popup'ı KAPAT

**Ne elde edeceksin:**
- ✅ Facebook Pixel ID: `123456789012345` (örnek)

---

#### 1.5. LinkedIn Insight Tag (B2B REKLAM İÇİN - 5 dakika)
**Ne yapacaksın:**
1. https://www.linkedin.com/campaignmanager adresine git
2. Hesap seç (yoksa oluştur)
3. **Account Assets** → **Insight Tag** tıkla
4. **Install My Insight Tag** tıkla
5. **Partner ID'yi kopyala**

**Ne elde edeceksin:**
- ✅ LinkedIn Partner ID: `123456` (örnek)

---

### 📍 AŞAMA 2: ID'LERİ ADMIN PANELE GİR (KULLANICI - 2 dakika)

**Ne yapacaksın:**
1. https://ixtif.com/admin/settingmanagement/values/8 adresine git
2. Aldığın ID'leri ilgili alanlara gir:

| Alan | Değer (Örnek) | Zorunlu mu? |
|------|---------------|-------------|
| Google Tag Manager Container ID | `GTM-P8HKHCG9` | ✅ ZORUNLU |
| Microsoft Clarity Project ID | `tvzxzyip9e` | ✅ ÖNERİLİR |
| Google Ads Conversion ID | `AW-XXXXXXXXXX` | ⏳ Reklam başlatınca |
| Google Ads Form Label | `AbC-123xyz` | ⏳ Reklam başlatınca |
| Google Ads Phone Label | `XyZ-456abc` | ⏳ Reklam başlatınca |
| Facebook Pixel ID | `123456789012345` | ⏳ Reklam başlatınca |
| LinkedIn Partner ID | `123456` | ⚠️ B2B reklam yaparsan |
| Twitter Pixel ID | - | ❌ Opsiyonel |
| TikTok Pixel ID | - | ❌ Opsiyonel |

3. **Kaydet** butonuna bas

---

### 📍 AŞAMA 3: HEADER KODLARINI GÜNCELLE (CLAUDE - 10 dakika)

**Claude yapacak:**
- [x] `header.blade.php` dosyasını düzenle
- [x] GTM snippet ekle (head kısmı)
- [x] GTM noscript ekle (body başlangıcı)
- [x] Eski GA4 kodunu KORU ama COMMENT'e al (GTM'e taşınacak)
- [x] Eski Yandex kodunu KORU ama COMMENT'e al (GTM'e taşınacak)
- [x] Settings'ten dinamik ID çekme sistemi kur

**Kullanıcı yapacak:**
- Hiçbir şey! Bekle :)

---

### 📍 AŞAMA 4: GTM CONTAINER JSON HAZIRLA (CLAUDE - 20 dakika)

**Claude yapacak:**
- [x] GTM Container JSON dosyası oluştur
- [x] GA4 Tag ekle (measurement ID: setting'ten)
- [x] Yandex Metrica Tag ekle (ID: setting'ten)
- [x] Microsoft Clarity Tag ekle (ID: setting'ten)
- [x] Google Ads Conversion Tag ekle (ID + Labels: setting'ten)
- [x] Facebook Pixel Tag ekle (ID: setting'ten)
- [x] LinkedIn Insight Tag ekle (ID: setting'ten)
- [x] Event triggers ekle (form_submit, phone_click, whatsapp_click, file_download)
- [x] dataLayer variables tanımla

**Kullanıcı yapacak:**
- JSON dosyasını indir
- GTM panele import et (sonraki aşama)

---

### 📍 AŞAMA 5: DATALAYER PUSH KODLARI EKLE (CLAUDE - 15 dakika)

**Claude yapacak:**
- [x] `public/js/ga-events.js` dosyası oluştur
- [x] Form submit event
- [x] Telefon tıklama event (4 farklı lokasyon)
- [x] WhatsApp tıklama event (4 farklı lokasyon)
- [x] PDF download event
- [x] Scroll depth tracking (25%, 50%, 75%, 100%)
- [x] Product view event (shop sayfası)
- [x] Footer'a script ekle

**Kullanıcı yapacak:**
- Hiçbir şey! Bekle :)

---

### 📍 AŞAMA 6: SCHEMA.ORG STRUCTURED DATA EKLE (CLAUDE - 10 dakika)

**Claude yapacak:**
- [x] `Organization` schema (header'da)
- [x] `Product` schema (shop show sayfasında)
- [x] `BreadcrumbList` schema
- [x] JSON-LD formatında ekle

**Kullanıcı yapacak:**
- Hiçbir şey! Bekle :)

---

### 📍 AŞAMA 7: GTM CONTAINER'I IMPORT ET (KULLANICI - 2 dakika)

**Ne yapacaksın:**
1. GTM paneline git: https://tagmanager.google.com
2. Container'ı seç (GTM-P8HKHCG9)
3. **Admin** (sağ üst) → **Import Container** tıkla
4. Claude'un hazırladığı JSON dosyasını seç
5. **Choose workspace:** `Default Workspace`
6. **Choose import option:** `Merge - Rename conflicting tags, triggers, and variables`
7. **Import** tıkla
8. **Continue** tıkla

**Ne elde edeceksin:**
- ✅ Tüm taglar hazır (GA4, Yandex, Clarity, Google Ads, Facebook vb.)
- ✅ Tüm triggers hazır (form submit, phone click vb.)
- ✅ Tüm variables hazır (dataLayer values)

---

### 📍 AŞAMA 8: CACHE + BUILD (CLAUDE - 2 dakika)

**Claude yapacak:**
```bash
php artisan view:clear
php artisan cache:clear
php artisan responsecache:clear
npm run prod
```

**Kullanıcı yapacak:**
- Hiçbir şey! Bekle :)

---

### 📍 AŞAMA 9: GTM PREVIEW MODE İLE TEST (BİRLİKTE - 10 dakika)

**Kullanıcı yapacak:**
1. GTM panelinde **Preview** butonuna bas
2. URL gir: `https://ixtif.com`
3. **Connect** tıkla
4. Yeni sekmede site açılacak

**Claude + Kullanıcı birlikte:**
- [ ] GTM açılıyor mu? ✅
- [ ] GA4 tag tetikleniyor mu? ✅
- [ ] Yandex tag tetikleniyor mu? ✅
- [ ] Clarity tag tetikleniyor mu? ✅
- [ ] Form gönder → `form_submit` event tetikleniyor mu? ✅
- [ ] Telefon tıkla → `phone_click` event tetikleniyor mu? ✅
- [ ] WhatsApp tıkla → `whatsapp_click` event tetikleniyor mu? ✅
- [ ] PDF indir → `file_download` event tetikleniyor mu? ✅

---

### 📍 AŞAMA 10: GTM CONTAINER'I PUBLISH ET (KULLANICI - 1 dakika)

**Ne yapacaksın:**
1. GTM Preview'dan çık
2. **Submit** butonuna bas (sağ üst)
3. **Version name:** `v1.0 - Full Marketing Setup`
4. **Version description:** `GA4 + Yandex + Clarity + Google Ads + Facebook + LinkedIn + Events`
5. **Publish** tıkla

**Ne elde edeceksin:**
- ✅ Canlı sistemde tüm platformlar aktif
- ✅ Reklam dönüşümleri ölçülüyor
- ✅ Heatmap çalışıyor
- ✅ Event tracking aktif

---

## 📊 ÖZET: KİM NE YAPACAK?

### 🙋 SEN (KULLANICI)
1. ✅ GTM Container oluştur (YAPILDI: GTM-P8HKHCG9)
2. ✅ Clarity hesabı aç (YAPILDI: tvzxzyip9e)
3. ⏳ Google Ads Conversion ID al (reklam başlatınca)
4. ⏳ Facebook Pixel al (reklam başlatınca)
5. ⏳ LinkedIn Insight Tag al (B2B reklam yaparsan)
6. ⏳ Admin panelden ID'leri gir
7. ⏳ GTM JSON import et
8. ⏳ GTM Preview ile test et
9. ⏳ GTM Publish et

### 🤖 CLAUDE
1. ⏳ Header'a GTM snippet ekle
2. ⏳ dataLayer push kodları yaz
3. ⏳ GTM Container JSON hazırla
4. ⏳ Schema.org ekle
5. ⏳ Cache+Build yap
6. ⏳ Test desteği ver

---

## 🎯 ŞİMDİ NE YAPMALIYIZ?

### SEÇENEK 1: HEMEN DEVAM (ÖNERİLİR)
**Claude şunları yapabilir (kullanıcı hazır olmasa bile):**
- ✅ Header'a GTM snippet ekle (dinamik, settings'ten ID çeker)
- ✅ dataLayer kodları ekle
- ✅ Schema.org ekle
- ✅ Cache+Build yap

**Sonra sen:**
- Google Ads/Facebook ID'lerini alırsın
- Admin panelden girersin
- GTM JSON import edersin
- Test edip publish edersin

### SEÇENEK 2: ÖNCE PLATFORMLARI HAZIRLA
**Sen önce:**
- Google Ads Conversion oluştur
- Facebook Pixel oluştur
- LinkedIn Insight al
- Admin panelden gir

**Sonra Claude:**
- Tüm kodları ekler
- GTM JSON hazırlar
- Test edersiniz

---

## 💡 BENİM ÖNERİM

**SEÇENEK 1'İ YAPALIM!**

**Neden?**
- ✅ Claude kodları şimdi eklesin (GTM snippet, dataLayer, Schema.org)
- ✅ Sen rahatça platform hesaplarını oluştur (acele yok)
- ✅ ID'leri admin panelden girdiğinde OTOMATIK çalışacak
- ✅ GTM JSON hazır beklesin, import ettiğinde 2 dakikada bitir

**Yani:** Claude şimdi altyapıyı kursun, sen boş zamanında ID'leri toplayıp gir!

---

## 🚀 BAŞLAYALIM MI?

**"DEVAM ET"** dersen:
1. Header'a GTM snippet ekleyeceğim
2. dataLayer kodlarını ekleyeceğim
3. Schema.org ekleyeceğim
4. GTM Container JSON hazırlayacağım
5. Cache+Build yapacağım

**Sen boş zamanında:**
- Google Ads/Facebook/LinkedIn hesaplarını oluştur
- ID'leri admin panelden gir
- GTM JSON import et
- Test et, publish et

**TAMAM MI?** 🎯
