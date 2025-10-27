# 🚀 İXTİF SAYFA ÜRETİM PLANI

> **Hazırlanma:** 2025-10-23
> **Durum:** Onay Bekliyor
> **Kullanıcı Talimatları:** Dikkate alındı

---

## ⚠️ ÖNEMLİ NOTLAR

### ❌ DOKUNMA
- **Anasayfa** → Mevcut, karışma!
- **Blog** → Ayrı modül var, karışma!
- **Ürünler (Liste)** → Shop modülü mevcut

### ✅ KURALLAR
- **Tailwind CSS + Alpine.js** kullan
- **Pages modülü** kullan (title, slug, body, css, js kolonları)
- **SEO Settings** tablosundan SEO bilgileri çek
- **Gerçek bilgi kullan**, uydurmaca yapma!
- **Türkiye hukuku** standartlarına uy
- **KVKK uyumlu** içerikler
- **Sidebar** kullan (ilgili sayfalar için)

### 📍 RESMİ BİLGİLER
```
Şirket: İXTİF İÇ VE DIŞ TİCARET ANONİM ŞİRKETİ
Adres: Küçükyalı Mahallesi, Çamlık Sokak,
       Manzara Adalar Sitesi, B Blok No: 1/B,
       İç Kapı No: 89, Kartal / İSTANBUL
Telefon: 0216 755 3 555
Vergi Dairesi: Kartal V.D.
Vergi No: 4831552951
WhatsApp: 0532 216 07 54
E-posta: info@ixtif.com
```

---

## 📋 OLUŞTURULACAK SAYFALAR

### 🏢 1. HAKKIMIZDA (/hakkimizda)

**Öncelik:** 🔴 ACİL (1. Öncelik)

**İçerik Yapısı:**
```
✅ Hero Section (Slogan + Görsel)
✅ Şirket Tanıtımı (Gerçek bilgiler)
✅ Sayılarla iXtif (Veritabanından: 1,020 ürün, 106 kategori)
✅ Vizyon & Misyon (Marka kimlik dokümanından)
✅ Marka Değerleri (4 değer)
✅ Depo Bilgileri (Tuzla Ana Depo - gerçek)
✅ İletişim CTA
```

**SEO Settings:**
```
Title: "İXTİF Hakkında | Türkiye'nin İstif Pazarı"
Meta Description: "25+ yıllık tecrübe ile forklift, transpalet ve istif ekipmanlarında lider. 5,000 m² depo, 1,020+ ürün, 7/24 destek."
Keywords: ixtif hakkında, forklift şirketi, istif ekipmanları, depo ekipmanları istanbul
```

**Tailwind + Alpine:**
- AOS animations (scroll effects)
- Counter animations (sayılar)
- Timeline component (isteğe bağlı)

---

### 📞 2. İLETİŞİM (/iletisim)

**Öncelik:** 🔴 ACİL (1. Öncelik)

**İçerik Yapısı:**
```
✅ İletişim Bilgileri (Settings'ten dinamik)
   - Telefon: {{ settings('contact_phone_1') }}
   - WhatsApp: {{ settings('contact_whatsapp_1') }}
   - E-posta: {{ settings('contact_email_1') }}
✅ Resmi Adres (Yukarıdaki gerçek adres)
✅ İletişim Formu (Livewire veya Ajax)
✅ Google Maps (Kartal/Küçükyalı konumu)
✅ WhatsApp Quick Buttons (Satış, Kiralama, Servis, Yedek Parça)
✅ Çalışma Saatleri
✅ Departmanlar
```

**SEO Settings:**
```
Title: "İletişim | iXtif - 7/24 Destek Hattı"
Meta Description: "iXtif ile iletişime geçin. Telefon: 0216 755 3 555, WhatsApp: 0532 216 07 54. Tuzla/İstanbul merkez ofis."
Schema: ContactPage, LocalBusiness
```

**Tailwind + Alpine:**
- Form validation (Alpine)
- WhatsApp click tracking
- Map embed

---

### 🛠️ 3. HİZMETLER (/hizmetler)

**Öncelik:** 🔴 ACİL (1. Öncelik)

**İçerik Yapısı:**
```
✅ 6 Hizmet Kartı:
   1. Satın Alma (Sıfır ürünler)
   2. Kiralama (Günlük/Aylık/Yıllık)
   3. İkinci El (Garantili)
   4. Teknik Servis (7/24)
   5. Bakım Anlaşması (Yıllık)
   6. Operatör Eğitimi (MEB onaylı)

Her kart:
- Icon (Font Awesome)
- Başlık
- Açıklama
- Fiyat (başlangıç - genel bilgi, kesin değil!)
- CTA butonu
```

**SEO Settings:**
```
Title: "Hizmetlerimiz | Satış, Kiralama, Servis - iXtif"
Meta Description: "Forklift satış, kiralama, teknik servis ve bakım hizmetleri. Günlük 150₺'den başlayan fiyatlarla esnek çözümler."
Keywords: forklift kiralama, forklift servisi, forklift bakım, operatör eğitimi
```

**Tailwind + Alpine:**
- Card hover effects
- Price toggle (günlük/aylık)
- Modal (detay göster)

---

### ❓ 4. SSS (/sss)

**Öncelik:** 🟡 YÜKSEK (2. Öncelik)

**İçerik Yapısı:**
```
✅ Kategoriler:
   1. Ürünler Hakkında (10-15 soru)
   2. Fiyat & Ödeme (8-10 soru)
   3. Kiralama (10-12 soru)
   4. Servis & Bakım (10-12 soru)
   5. Yasal & Güvenlik (6-8 soru)
   6. Teslimat (5-6 soru)

Format: Accordion (Alpine x-data)
Schema.org: FAQPage
```

**SEO Settings:**
```
Title: "Sıkça Sorulan Sorular | Forklift SSS - iXtif"
Meta Description: "Forklift, transpalet ve istif ekipmanları hakkında tüm sorularınızın cevapları. 50+ SSS."
Schema: FAQPage (her soru için Question/Answer)
```

**Tailwind + Alpine:**
- Accordion component
- Search filter (Alpine)
- Category tabs

---

### 🏆 5. REFERANSLAR (/referanslar)

**Öncelik:** 🟡 YÜKSEK (2. Öncelik)

**İçerik Yapısı:**
```
✅ Sektör Bazlı Logo Grid:
   - Lojistik (Aras, MNG, DHL, UPS)
   - Perakende (Migros, BİM, A101)
   - Üretim (Arçelik, Vestel, Bosch)
   - E-Ticaret (Trendyol, Hepsiburada)
   - Gıda (Ülker, Eti, Coca-Cola)

✅ Başarı Hikayeleri (2-3 adet - genel, uydurmaca değil!)
✅ Müşteri Yorumları (Genel, gerçek varsa ekle)
```

**SEO Settings:**
```
Title: "Referanslarımız | 500+ Kurumsal Müşteri - iXtif"
Meta Description: "Türkiye'nin lider firmalarının tercihi. Aras Kargo, Migros, Trendyol, Arçelik ve 500+ kurumsal referans."
Keywords: forklift referanslar, ixtif müşterileri, kurumsal referanslar
```

**Tailwind + Alpine:**
- Logo slider (Alpine carousel)
- Filter by sector
- Lazy load images

---

### 💼 6. KARİYER (/kariyer)

**Öncelik:** 🟡 YÜKSEK (2. Öncelik)

**İçerik Yapısı:**
```
✅ İnsan Kaynakları Politikası
✅ Çalışan Avantajları (8 kart)
✅ Açık Pozisyonlar (Genel kategoriler - spesifik değil!)
   - Satış Danışmanı
   - Forklift Teknisyeni
   - Operatör
   - (Detay vermeden genel)
✅ Başvuru Formu (CV upload)
✅ KVKK onay checkbox
```

**SEO Settings:**
```
Title: "Kariyer | İXTİF'te Çalış, Geleceğini İnşa Et"
Meta Description: "iXtif ailesine katılın! Satış, teknik servis, operasyon pozisyonları. Başvuru için formu doldurun."
Keywords: ixtif kariyer, ixtif iş ilanları, forklift teknisyen iş
```

**Tailwind + Alpine:**
- File upload preview
- Form validation
- Position filter

---

### 🔒 7. HUKUKİ SAYFALAR (ZORUNLU!)

#### 7.1. GİZLİLİK POLİTİKASI (/gizlilik-politikasi)

**İçerik:**
- Veri Sorumlusu (İXTİF resmi bilgileri)
- Toplanan Veriler
- Veri Kullanım Amaçları
- Veri Paylaşımı
- Veri Saklama Süresi
- Kullanıcı Hakları (KVKK md. 11)
- İletişim (kvkk@ixtif.com)

**Sidebar:**
- KVKK Aydınlatma Metni
- KVKK Başvuru
- Çerez Politikası
- Kullanım Koşulları

---

#### 7.2. KULLANIM KOŞULLARI (/kullanim-kosullari)

**İçerik:**
- Taraflar (İXTİF bilgileri)
- Hizmet Kapsamı
- Kullanıcı Yükümlülükleri
- Fikri Mülkiyet
- Sorumluluk Sınırlaması
- Uyuşmazlık Çözümü (İstanbul Mahkemeleri)

---

#### 7.3. KVKK AYDINLATMA METNİ (/kvkk-aydinlatma-metni)

**İçerik:**
- Veri Sorumlusu (İXTİF A.Ş. - Vergi No: 4831552951)
- Kişisel Verilerin Toplanma Yöntemi
- İşlenme Amaçları
- Aktarılan Taraflar
- Veri Sahibinin Hakları (KVKK md. 11)
- Başvuru Yöntemi

---

#### 7.4. KVKK BAŞVURU FORMU (/kvkk-basvuru)

**İçerik:**
- Form (TC No, İsim, Talep Türü, Açıklama)
- Kimlik Belgesi Upload
- KVKK onay checkbox
- Gönder butonu

**Tailwind + Alpine:**
- Form validation
- File upload
- Success message

---

#### 7.5. ÇEREZ POLİTİKASI (/cerez-politikasi)

**İçerik:**
- Çerez Nedir?
- Çerez Türleri (Zorunlu, Analitik, Pazarlama)
- Saklama Süreleri
- Çerez Yönetimi
- Çerez Reddi

**Çerez Banner:**
- Alpine.js ile cookie banner
- Kabul/Reddet butonları
- localStorage kaydı

---

#### 7.6. İPTAL & İADE (/iptal-iade)

**İçerik:**
- Cayma Hakkı (14 gün - 6502 TKHK)
- Cayma Hakkı Kullanılamayan Durumlar
- İade Koşulları
- İade Süreci
- Kargo Ücreti
- İletişim (iade@ixtif.com)

---

#### 7.7. CAYMA HAKKI FORMU (/cayma-hakki)

**İçerik:**
- Müşteri Bilgileri
- Sipariş Bilgileri
- İade Sebebi
- İade Yöntemi
- Ödeme İadesi
- İmza/Onay

---

#### 7.8. MESAFELİ SATIŞ SÖZLEŞMESİ (/mesafeli-satis-sozlesmesi)

**İçerik:**
- Taraflar (SATICI: İXTİF A.Ş. - Tam bilgiler)
- Sözleşme Konusu Ürün
- Teslimat
- Ödeme
- Cayma Hakkı
- Uyuşmazlık Çözümü (İstanbul Anadolu Mahkemeleri)

---

#### 7.9. TESLİMAT & KARGO (/teslimat)

**İçerik:**
- Teslimat Süreleri
- Kargo Firmaları (MNG, Aras, Yurtiçi)
- Kargo Ücreti (500₺ üzeri ücretsiz)
- Teslimat Süreci
- Teslimatta Dikkat Edilecekler

---

#### 7.10. ÖDEME YÖNTEMLERİ (/odeme-yontemleri)

**İçerik:**
- Kredi Kartı (Visa, Mastercard, Troy)
- Banka Havalesi/EFT (İXTİF hesap bilgileri - GEREKLİ!)
- Taksit Seçenekleri
- Güvenlik (3D Secure, SSL)
- Kurumsal Ödeme

**NOT:** Banka hesap bilgileri eklenecek (şimdilik placeholder)

---

#### 7.11. GÜVENLİ ALIŞVERİŞ (/guvenli-alisveris)

**İçerik:**
- SSL Sertifikası
- Güvenlik Önlemleri
- Kredi Kartı Güvenliği
- KVKK Uyumluluk
- Sertifika Badgeleri

---

## 🎨 TASARIM STANDARTLARI

### Tailwind Classes
```css
/* Container */
.container mx-auto px-4 sm:px-4 md:px-0

/* Hero Section */
.bg-gradient-to-br from-blue-50 via-white to-purple-50
.dark:from-slate-900 dark:via-slate-800

/* Cards */
.bg-white dark:bg-slate-800
.rounded-3xl
.shadow-2xl shadow-blue-500/10
.border border-gray-100 dark:border-slate-700

/* Buttons */
.bg-blue-600 hover:bg-blue-700
.text-white
.py-3 px-6 rounded-lg
.transition-all duration-300

/* Typography */
.text-3xl md:text-4xl lg:text-5xl font-bold
.text-gray-900 dark:text-white
.text-gray-600 dark:text-gray-300
```

### Alpine.js Components
```javascript
// Accordion (SSS)
x-data="{ open: null }"
@click="open = open === 1 ? null : 1"

// Form Validation
x-data="{ name: '', email: '', valid: false }"
@submit.prevent="submitForm()"

// Modal
x-data="{ show: false }"
x-show="show"
x-transition

// Counter (Hakkımızda - sayılar)
x-data="{ count: 0 }"
x-init="animateCount()"
```

---

## 📊 SEO SETTINGS TABLOSU

**Her sayfa için:**

```php
// seo_settings tablosu
[
    'seoable_type' => 'Modules\Page\App\Models\Page',
    'seoable_id' => [page_id],
    'titles' => json(['tr' => 'Başlık']),
    'descriptions' => json(['tr' => 'Açıklama']),
    'keywords' => 'keyword1, keyword2',
    'og_titles' => json(['tr' => 'OG Başlık']),
    'og_descriptions' => json(['tr' => 'OG Açıklama']),
    'og_type' => 'website',
    'twitter_card' => 'summary_large_image',
    'schema_markup' => json([...]),
    'robots_meta' => 'index, follow',
    'canonical_url' => 'https://ixtif.com/...',
]
```

---

## 📂 PAGES MODÜLÜ YAPISI

**Her sayfa için:**

```php
[
    'title' => json(['tr' => 'Sayfa Başlığı']),
    'slug' => json(['tr' => 'sayfa-slug']),
    'body' => '<div class="container">...</div>',
    'css' => '/* Sayfaya özel CSS */',
    'js' => '/* Sayfaya özel JS */',
    'is_active' => 1,
    'is_homepage' => 0,
]
```

---

## 🎯 ÜRETİM SIRASI

### Faz 1: Temel Sayfalar (1. Gün)
1. ✅ Hakkımızda
2. ✅ İletişim
3. ✅ Hizmetler

### Faz 2: Destek Sayfaları (2. Gün)
4. ✅ SSS
5. ✅ Referanslar
6. ✅ Kariyer

### Faz 3: Hukuki Sayfalar (3. Gün)
7. ✅ Gizlilik Politikası
8. ✅ Kullanım Koşulları
9. ✅ KVKK Aydınlatma Metni
10. ✅ KVKK Başvuru Formu
11. ✅ Çerez Politikası
12. ✅ İptal & İade
13. ✅ Cayma Hakkı Formu
14. ✅ Mesafeli Satış Sözleşmesi
15. ✅ Teslimat & Kargo
16. ✅ Ödeme Yöntemleri
17. ✅ Güvenli Alışveriş

---

## ✅ KONTROL LİSTESİ

Her sayfa için:

- [ ] Pages tablosuna eklendi
- [ ] SEO Settings oluşturuldu
- [ ] Tailwind + Alpine kullanıldı
- [ ] Responsive tasarım
- [ ] Dark mode desteği
- [ ] Settings'ten dinamik veri çekimi
- [ ] Gerçek bilgiler kullanıldı
- [ ] KVKK uyumlu
- [ ] Schema.org markup
- [ ] Internal linking (sidebar)
- [ ] Test edildi

---

## 🚨 ÖNEMLİ HATIRLATMALAR

❌ **YAPMA:**
- Anasayfa'ya dokunma
- Blog modülüne karışma
- Uydurma bilgi ekleme
- Kesin fiyat verme
- Yanlış hukuki bilgi

✅ **YAP:**
- Gerçek bilgileri kullan
- Settings'ten çek
- KVKK'ya uy
- Türkiye hukukuna uy
- Responsive + Dark mode
- Schema.org ekle

---

**HAZIR!** "BAŞLA" dediğinde sayfaları oluşturmaya başlayacağım! 🚀
