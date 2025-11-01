# 🚀 Shop System V4 - Sistem Kuralları

## 📋 GENEL BAKIŞ

Shop System V4, üretici PDF kataloglarından **tam otomatik, zengin içerikli ürün sayfaları** üretir.

**Temel Felsefe:**
- ❌ Hard-coded mapping yok
- ❌ CTA/Kampanya/Sahte sosyal kanıt yok
- ✅ Dinamik sistem (kategori/ürün bazlı)
- ✅ 8 farklı içerik varyasyonu
- ✅ SEO odaklı FAQ yapısı
- ✅ AI asistan için zengin anahtar kelime sistemi

---

## 🎯 1. BAŞLIK STANDARDİZASYONU

### Format:
```
[Model] [Kapasite] [Enerji Tipi] [Kategori] [- Özel Özellik (opsiyonel)]
```

### Örnekler:

**✅ DOĞRU:**
- `F4 1.5 Ton Lityum Akülü Transpalet`
- `CPD20 2 Ton Elektrikli Forklift`
- `CBD15 1.5 Ton Elektrikli İstif Makinesi - 3300mm`
- `CQD20 2 Ton Elektrikli Reach Truck`
- `QDD15 1.5 Ton Elektrikli Order Picker - 3000mm`

**❌ YANLIŞ:**
- `F4 Transpalet` (kapasite/enerji eksik)
- `1.5 Tonluk Li-Ion F4 Transpalet` (sıralama yanlış)
- `F4 1500kg Lithium Pallet Truck` (İngilizce, birim yanlış)

### Kurallar:
1. **Model**: Üretici model kodu (F4, CPD20, CBD15)
2. **Kapasite**: `X Ton` formatında (1.5 Ton, 2 Ton, 3.5 Ton)
3. **Enerji**: `Elektrikli`, `Lityum Akülü`, `Dizel`, `LPG`, `Hibrit`
4. **Kategori**: 7 ana kategoriden biri (tam isim):
   - Forklift
   - Transpalet
   - İstif Makinesi
   - Reach Truck
   - Order Picker
   - Tow Truck
   - Otonom
5. **Özel Özellik**: Opsiyonel (Kaldırma yüksekliği, özel donanım)

---

## 🎨 2. 8 İÇERİK VARYASYONU SİSTEMİ

**Her özellik/avantaj için 8 farklı anlatım stili üretilir:**

| # | Tip | Amaç | Uzunluk | İkon Örneği |
|---|-----|------|---------|-------------|
| 1 | **Teknik** | Mühendislere/uzmanlara hitap | 1-2 cümle | fa-cog, fa-tools |
| 2 | **Fayda** | Müşteri için ne kazancı var? | 1 cümle | fa-check-circle, fa-thumbs-up |
| 3 | **Slogan** | Dikkat çekici, akılda kalıcı | 3-6 kelime | fa-star, fa-award |
| 4 | **Motto** | Marka mesajı, değer vurgusu | 4-8 kelime | fa-crown, fa-gem |
| 5 | **Kısa Bullet** | Hızlı tarama için | 3-6 kelime | fa-check, fa-bolt |
| 6 | **Uzun Açıklama** | Detaylı anlatım | 3-5 cümle | fa-info-circle, fa-book |
| 7 | **Karşılaştırma** | Rakiplerle/eski teknoloji ile kıyas | 1-2 cümle | fa-chart-line, fa-balance-scale |
| 8 | **Anahtar Kelime** | AI/Arama için tetikleyiciler | 5-10 kelime | fa-key, fa-tag |

### Örnek: Li-Ion Batarya Özelliği

```json
{
  "feature_id": "li-ion-battery",
  "variations": {
    "technical": "24V 20Ah Li-Ion batarya, 4-6 saat operasyon, BMS korumalı",
    "benefit": "Tam gün çalış, şarj bekleme",
    "slogan": "Bir Şarj, Tam Gün İş!",
    "motto": "Li-Ion teknoloji, sınırsız verimlilik",
    "short_bullet": "4-6 saat kesintisiz, sıfır bakım",
    "long_description": "24V/20Ah Li-Ion batarya sistemi, tek şarjda 4-6 saat kesintisiz operasyon sağlar. Geleneksel kurşun asit bataryalara göre 3 kat daha uzun ömürlü, %50 daha hafif ve tamamen bakım gerektirmez. Entegre BMS (Battery Management System) aşırı şarj, derin deşarj ve kısa devre koruması sağlar.",
    "comparison": "Kurşun aside göre 3x uzun ömür, %50 daha hafif, sıfır bakım",
    "keywords": "lityum, li-ion, akü, batarya, şarj, 24 volt, enerji, pil, battery, lithium"
  },
  "icon": "fa-battery-full",
  "icon_color": "success"
}
```

---

## 🔑 3. ANAHTAR KELİME SİSTEMİ (AI ASISTAN İÇİN)

**3 kategori anahtar kelime:**

### 3.1 Ana Kelimeler (5-8 adet)
- **Amaç**: Ürünün ana tanımlayıcıları
- **Örnek**: `transpalet`, `1.5 ton`, `li-ion`, `kompakt`, `hafif`

### 3.2 Eş Anlamlılar (10-15 adet)
- **Amaç**: Müşterilerin farklı ifadeleri
- **Örnek**: `palet taşıyıcı`, `palet kaldırıcı`, `el transpaleti`, `akülü palet`, `lityum transpalet`, `lithium pallet truck`, `elektrikli palet`, `şarjlı transpalet`

### 3.3 Kullanım/Jargon (10-15 adet)
- **Amaç**: Müşterilerin kullanım senaryoları, sektör jargonu
- **Örnek**: `soğuk hava deposu`, `frigo`, `dar koridor`, `market`, `depo`, `lojistik`, `kargo`, `e-ticaret`, `portif`, `hafif yük`, `kısa mesafe`, `iç mekan`

### JSON Formatı:
```json
{
  "keywords": {
    "primary": ["transpalet", "1.5 ton", "li-ion", "kompakt", "hafif"],
    "synonyms": ["palet taşıyıcı", "palet kaldırıcı", "el transpaleti", "akülü palet", "lityum transpalet", "lithium pallet truck", "elektrikli palet", "şarjlı transpalet", "elektrikli transpalet", "bataryalı palet"],
    "usage_jargon": ["soğuk hava deposu", "frigo", "dar koridor", "market", "depo", "lojistik", "kargo", "e-ticaret", "portif", "hafif yük", "kısa mesafe", "iç mekan", "gıda deposu", "soğuk zincir"]
  }
}
```

### ⚠️ KRİTİK UYARI:
**7 ana kategori BİRBİRİNİN EŞ ANLAMLISI DEĞİLDİR!**

❌ YANLIŞ:
```json
"synonyms": ["forklift", "istif makinesi", "transpalet"]
```

✅ DOĞRU:
```json
// Transpalet için
"synonyms": ["palet taşıyıcı", "palet kaldırıcı", "el transpaleti"]

// Forklift için (ayrı ürün!)
"synonyms": ["forklift truck", "lift truck", "counterbalance"]
```

**7 Ana Kategori (Tamamen Bağımsız):**
1. Forklift
2. Transpalet
3. İstif Makinesi
4. Reach Truck
5. Order Picker
6. Tow Truck
7. Otonom

---

## ❓ 4. FAQ (SSS) SİSTEMİ

### Minimum Gereksinim:
- **En Az**: 10 soru (SEO için kritik)
- **Maksimum**: Sınırsız (ama değerli olmalı)

### Kategori Dağılımı:

| Kategori | Oran | Örnek Sayı (10 min) |
|----------|------|---------------------|
| Kullanım | 30% | 3 soru |
| Teknik | 25% | 2-3 soru |
| Seçenekler/Opsiyonlar | 20% | 2 soru |
| Bakım/Servis | 15% | 1-2 soru |
| Satın Alma | 10% | 1 soru |

### Soru Tipleri ve Örnekler:

**KULLANIM (30%):**
1. `F4 transpalet hangi sektörlerde kullanılır?`
2. `Dar koridorlarda kullanılabilir mi?`
3. `Soğuk hava depolarında çalışır mı?`

**TEKNİK (25%):**
4. `Li-Ion batarya ne kadar dayanır?`
5. `Maksimum kaldırma kapasitesi nedir?`
6. `Şarj süresi ne kadar?`

**SEÇENEKLER (20%):**
7. `Hangi fork uzunlukları mevcut?`
8. `Ekstra batarya alınabilir mi?`

**BAKIM (15%):**
9. `Bakım gereksinimleri nelerdir?`
10. `Garanti süresi kaç yıl?`

**SATIN ALMA (10%):**
11. `Fiyat teklifi nasıl alınır?`

### JSON Formatı:
```json
{
  "faq": [
    {
      "category": "usage",
      "question": "F4 transpalet hangi sektörlerde kullanılır?",
      "answer": "F4, kompakt yapısı sayesinde market, e-ticaret deposu, soğuk hava deposu, eczane, küçük üretim tesisleri gibi dar alan gerektiren sektörlerde idealdir. Özellikle dar koridorlu depolarda ve iç mekan operasyonlarında yüksek verimlilik sağlar.",
      "icon": "fa-industry"
    }
  ]
}
```

### SEO Önemi:
- **Google Featured Snippets**: İyi yapılandırılmış FAQ direkt arama sonuçlarında gösterilebilir
- **Schema.org FAQPage**: Yapılandırılmış veri ile arama motorları için optimize
- **Long-tail Keywords**: Her soru farklı arama sorgusu yakalar

---

## 🏭 5. SEKTÖR/ENDÜSTRİ LİSTESİ

### Kurallar:
- **Ürün Bazlı**: Her ürün kendi sektör listesine sahip
- **Özellik Bazlı**: Ürünün özelliklerine göre belirlenir
- **Miktar**: 15-30 sektör

### F4 Transpalet Örneği (Kompakt, Hafif, İç Mekan):
```json
{
  "industries": [
    {"name": "Market/Süpermarket", "icon": "fa-shopping-cart", "relevance": "high"},
    {"name": "E-ticaret Deposu", "icon": "fa-box", "relevance": "high"},
    {"name": "Soğuk Hava Deposu", "icon": "fa-snowflake", "relevance": "high"},
    {"name": "Gıda Lojistiği", "icon": "fa-apple-alt", "relevance": "high"},
    {"name": "Eczane/İlaç Deposu", "icon": "fa-pills", "relevance": "medium"},
    {"name": "Tekstil Deposu", "icon": "fa-tshirt", "relevance": "medium"},
    {"name": "Elektronik Depo", "icon": "fa-microchip", "relevance": "medium"},
    {"name": "Küçük Üretim", "icon": "fa-cogs", "relevance": "medium"},
    {"name": "Mobilya Mağazası", "icon": "fa-couch", "relevance": "low"},
    {"name": "Yedek Parça Deposu", "icon": "fa-wrench", "relevance": "low"}
  ]
}
```

### CPD20 Forklift Örneği (Yüksek Kapasite, Dış Mekan):
```json
{
  "industries": [
    {"name": "İnşaat", "icon": "fa-hard-hat", "relevance": "high"},
    {"name": "Liman/Terminal", "icon": "fa-ship", "relevance": "high"},
    {"name": "Çelik/Metal", "icon": "fa-industry", "relevance": "high"},
    {"name": "Otomotiv Üretim", "icon": "fa-car", "relevance": "high"}
  ]
}
```

### İkon Seçimi:
- **Sertifikalar**: SADECE sertifikalar bölümünde kullanılır (CE, ISO vs.)
- **Sektör İkonları**: FontAwesome 6 - Sektörü temsil eden ikon

---

## 📝 6. ÜRÜN AÇIKLAMASI YAPISI

### 3 Katmanlı Anlatım:

| Katman | Ton | Uzunluk | Amaç |
|--------|-----|---------|------|
| **1. Hikayeci Giriş** | Samimi, dikkat çeken, öven | 100-150 kelime | İlk izlenim, merak uyandır |
| **2. Profesyonel Teknik** | Ciddi, uzmanlara hitap | 200-300 kelime | Detaylı özellikler, avantajlar |
| **3. Detay/Nüans** | Bilgilendirici, ek bilgiler | 100-150 kelime | Kullanım senaryoları, ipuçları |

**Toplam**: 400-600 kelime

### Örnek Yapı:

```markdown
## Hikayeci Giriş (100-150 kelime)
Deponuzda yer daraldı mı? Dar koridorlarda manevra yaparken zorlanıyor musunuz? İşte F4, tam da bu sorunlar için tasarlandı. Sadece 400mm'lik çatal mesafesi ile şimdiye kadar erişemediğiniz alanlara kolayca ulaşın. 120 kg ağırlığıyla piyasadaki en hafif transpalet olmasına rağmen 1.5 ton yükü güvenle taşır. Li-Ion batarya teknolojisi sayesinde sabah şarj edin, akşama kadar çalışın. Artık batarya değiştirme, bakım yapma veya kurşun asit'in ağırlığıyla uğraşma yok. F4, küçük işletmelerin büyük dostu!

## Profesyonel Teknik (200-300 kelime)
F4, EP Equipment'ın modüler platform teknolojisi ile geliştirilmiş, endüstriyel sınıf bir elektrikli transpalettir. 24V/20Ah Li-Ion batarya sistemi, tek şarjda 4-6 saat kesintisiz operasyon kapasitesi sunar. Çıkarılabilir batarya tasarımı sayesinde ikinci bir batarya ile 7/24 çalışma mümkündür...

[300 kelimeye kadar devam eder - Teknik özellikler, BMS sistemi, güvenlik, ergonomi, dayanıklılık]

## Detay/Nüans (100-150 kelime)
F4'ü özellikle benzersiz kılan opsiyonel stabilizasyon tekerlekleridir. Bu sistem sayesinde yük taşırken ekstra denge sağlar ve operatör güvenliğini artırır. 6 farklı çatal uzunluğu (900-1500mm) ve 2 farklı genişlik seçeneği (560/685mm) ile ihtiyacınıza özel konfigürasyon yapabilirsiniz. Soğuk hava depolarında test edilmiş, -25°C'ye kadar çalışma garantisi vardır...

[150 kelimeye kadar - Kullanım senaryoları, ipuçları, özel durumlar]
```

### Ton Özellikleri:

**Hikayeci:**
- ✅ Soru ile başla (müşteriyle bağ kur)
- ✅ Sorunu tanımla, çözümü sun
- ✅ Ürünü öv, heyecan yarat
- ❌ Teknik jargon kullanma

**Profesyonel:**
- ✅ Teknik terimler kullan
- ✅ Sayısal verilerle destekle
- ✅ Standartları belirt (CE, ISO)
- ❌ Abartılı pazarlama dili

**Detay/Nüans:**
- ✅ Pratik bilgiler ver
- ✅ Özel kullanım senaryoları
- ✅ Müşteri soruları yanıtla
- ❌ Tekrar etme

---

## 🎨 7. İKON SİSTEMİ

### Kurallar:
- **FontAwesome 6**: Tüm ikonlar FA6'dan seçilir
- **Her İçerik İkon Alır**: Kullanım opsiyonel ama atama zorunlu
- **Renk Kodları**: Bootstrap renk sistemi (primary, success, warning, danger, info, secondary)

### Kategori Bazlı İkon Önerileri:

**Teknik Özellikler:**
- fa-cog (ayarlar)
- fa-microchip (teknoloji)
- fa-tools (ekipman)
- fa-ruler-combined (ölçüler)

**Avantajlar:**
- fa-check-circle (onay)
- fa-thumbs-up (beğeni)
- fa-star (kalite)
- fa-award (ödül)

**Güvenlik:**
- fa-shield-alt (koruma)
- fa-lock (güvenlik)
- fa-user-shield (operatör güvenliği)

**Performans:**
- fa-tachometer-alt (hız)
- fa-bolt (güç)
- fa-rocket (performans)
- fa-chart-line (verimlilik)

**Batarya/Enerji:**
- fa-battery-full (tam şarj)
- fa-plug (şarj)
- fa-bolt (enerji)
- fa-leaf (çevre dostu)

### JSON Formatı:
```json
{
  "icon": "fa-battery-full",
  "icon_color": "success",
  "icon_style": "solid"  // solid, regular, light, duotone
}
```

---

## 📊 8. TEKNİK ÖZELLİKLER YAPISII (ACCORDION)

### Kategori Sistemi (Dinamik):

**Transpalet İçin Örnek:**
1. Genel Özellikler (Model, kapasite, enerji)
2. Batarya Sistemi (Tip, kapasite, şarj süresi)
3. Boyutlar (Uzunluk, genişlik, yükseklik)
4. Çatal Özellikleri (Uzunluk, genişlik, yükseklik)
5. Performans (Hız, kaldırma hızı, menzil)
6. Tekerlekler (Tip, malzeme, çap)
7. Fren Sistemi (Tip, özellikler)
8. Güvenlik (Koruma sistemleri)
9. Ergonomi (Kumanda, tutamaç)
10. Çevresel (Sıcaklık aralığı, gürültü)
11. Sertifikalar (CE, ISO)
12. Opsiyonlar (Ekstra donanımlar)

### JSON Formatı:
```json
{
  "technical_specs": {
    "general": {
      "category_name": "Genel Özellikler",
      "icon": "fa-info-circle",
      "properties": [
        {"key": "Model", "value": "F4", "unit": ""},
        {"key": "Kapasite", "value": "1500", "unit": "kg"},
        {"key": "Enerji Tipi", "value": "Li-Ion Batarya", "unit": ""}
      ]
    },
    "battery": {
      "category_name": "Batarya Sistemi",
      "icon": "fa-battery-full",
      "properties": [
        {"key": "Tip", "value": "Li-Ion", "unit": ""},
        {"key": "Voltaj", "value": "24", "unit": "V"},
        {"key": "Kapasite", "value": "20", "unit": "Ah"},
        {"key": "Operasyon Süresi", "value": "4-6", "unit": "saat"},
        {"key": "Şarj Süresi", "value": "2-3", "unit": "saat"},
        {"key": "Çıkarılabilir", "value": "Evet", "unit": ""}
      ]
    }
  }
}
```

---

## 🏆 9. REKABET AVANTAJLARI

### Kurallar:
- **Karşılaştırmalı Veri**: Rakip/eski teknoloji ile kıyaslama
- **Sayısal Kanıt**: Mümkün olduğunca rakam ver
- **İkon Kullan**: Her avantaja ikon ata

### Örnek:
```json
{
  "competitive_advantages": [
    {
      "title": "3x Daha Uzun Batarya Ömrü",
      "description": "Kurşun asit bataryaya göre 3 kat daha uzun kullanım ömrü (1500 vs 500 şarj döngüsü)",
      "icon": "fa-battery-full",
      "icon_color": "success",
      "comparison_value": "3x",
      "comparison_baseline": "Kurşun asit"
    },
    {
      "title": "%50 Daha Hafif",
      "description": "Aynı kapasitedeki kurşun asit bataryalı modellere göre %50 daha hafif (120kg vs 240kg)",
      "icon": "fa-weight",
      "icon_color": "primary",
      "comparison_value": "50%",
      "comparison_baseline": "Kurşun asit model"
    }
  ]
}
```

---

## 🎬 10. KULLANIM SENARYOLARI (USE CASES)

### Kurallar:
- **Spesifik Durumlar**: Genel değil, somut senaryolar
- **Sektör/Ortam Odaklı**: Müşterinin işyerine uyarla
- **İkon + Kısa Açıklama**: Her senaryo 1-2 cümle

### F4 İçin Örnekler:
```json
{
  "use_cases": [
    {
      "title": "Soğuk Hava Deposu",
      "description": "Market zincirleri için soğuk hava depolarında (-25°C) sebze-meyve paletlerinin transferi. Kompakt yapısı dar koridorlarda yüksek verimlilik sağlar.",
      "icon": "fa-snowflake",
      "industry": "Gıda Lojistiği",
      "environment": "İç Mekan - Soğuk"
    },
    {
      "title": "E-ticaret Deposu",
      "description": "Hızlı kargo hazırlama için raf aralarında sürekli hareket. Li-Ion batarya ile 8 saatlik vardiya boyunca kesintisiz operasyon.",
      "icon": "fa-box",
      "industry": "E-ticaret",
      "environment": "İç Mekan"
    }
  ]
}
```

---

## 📦 11. OPSİYONLAR/AKSESUARLAR

### Kurallar:
- **Opsiyonel Ekipmanlar**: Standart modelde olmayan ekstralar
- **Gruplandırma**: Kategori bazlı (çatal, batarya, güvenlik)
- **Fiyat Bilgisi**: "Fiyat için teklif alın" (fiyat yazma!)

### Örnek:
```json
{
  "options": {
    "forks": {
      "category_name": "Çatal Seçenekleri",
      "icon": "fa-grip-horizontal",
      "items": [
        {
          "name": "900mm Çatal",
          "description": "Standart palet boyutları için",
          "sku": "F4-FORK-900"
        },
        {
          "name": "1150mm Çatal",
          "description": "Euro palet için",
          "sku": "F4-FORK-1150"
        }
      ]
    },
    "battery": {
      "category_name": "Batarya Seçenekleri",
      "icon": "fa-battery-half",
      "items": [
        {
          "name": "İkinci Li-Ion Batarya",
          "description": "7/24 operasyon için yedek batarya",
          "sku": "F4-BAT-EXTRA"
        }
      ]
    }
  }
}
```

---

## ❌ 12. YASAK İÇERİKLER

### Kesinlikle Kullanılmayacak:

**CTA (Call to Action):**
- ❌ "Hemen Sipariş Verin!"
- ❌ "Bugün Kaçırmayın!"
- ❌ "Şimdi Satın Alın!"

**Kampanya/Aciliyet:**
- ❌ Geri sayım sayacı
- ❌ "Son 3 stok!"
- ❌ "%50 İNDİRİM!"
- ❌ "Bugün 10 Kişi Baktı!"

**Sahte Sosyal Kanıt:**
- ❌ "5000+ Mutlu Müşteri!"
- ❌ Fake testimonialler
- ❌ "Türkiye'nin 1 Numarası!" (kanıt olmadan)

### Kullanıcı Geri Bildirimi:
> "kampanya offer sözüne ve cta ya gerek yok. Bu sahtekarlık gibi"
> "cta yı unut artık sürekli yazıp durma"

---

## 🗂️ 13. VERİTABANI YAPISII (ÖNERİ)

### ShopProduct Model - Yeni Alanlar:

```php
// JSON fields
protected $casts = [
    'content_variations' => 'array',  // 8 varyasyon
    'keywords' => 'array',             // 3 kategori
    'faq' => 'array',                  // 10+ soru
    'industries' => 'array',           // 15-30 sektör
    'technical_specs' => 'array',      // Accordion
    'competitive_advantages' => 'array',
    'use_cases' => 'array',
    'options' => 'array'
];
```

### Örnek Kayıt:
```json
{
  "id": 123,
  "sku": "F4-201",
  "title": "F4 1.5 Ton Lityum Akülü Transpalet",
  "category_id": 2,  // Transpalet
  "content_variations": {
    "li-ion-battery": {
      "technical": "...",
      "benefit": "...",
      ...
    }
  },
  "keywords": {
    "primary": [...],
    "synonyms": [...],
    "usage_jargon": [...]
  },
  "faq": [
    {"category": "usage", "question": "...", "answer": "..."}
  ],
  "industries": [
    {"name": "Market", "icon": "fa-shopping-cart", "relevance": "high"}
  ]
}
```

---

## 🤖 14. PDF PARSER - YAPAY ZEKA KURALLARI

### Girdi:
- PDF dosyası (üretici broşürü)
- Kategori bilgisi (Transpalet, Forklift vs.)

### Çıktı:
- JSON formatında tam ürün verisi
- 8 varyasyonlu içerik
- 10+ FAQ
- Sektör listesi
- Teknik özellikler accordion

### Parser Adımları:

1. **OCR/Text Extraction**: PDF'den text çıkar
2. **Kategori Tespiti**: Hangi kategori olduğunu belirle
3. **Teknik Özellik Çıkarımı**: Tabloları parse et
4. **Feature Extraction**: Özellik bullet'larını bul
5. **8 Varyasyon Üret**: Her özellik için 8 stilde yaz
6. **FAQ Üret**: En az 10 soru-cevap oluştur
7. **Sektör Match**: Özelliklere göre sektör öner
8. **Anahtar Kelime Üret**: 3 kategoride keywords
9. **İkon Ata**: Her içeriğe uygun ikon seç
10. **JSON Export**: Tek JSON dosyası oluştur

### Örnek Prompt (AI için):
```
readme/shop-system-v4/V4-SYSTEM-RULES.md dosyasını oku.
EP PDF/2-Transpalet/F4 201/ klasöründeki PDF'i analiz et.
Kategori: Transpalet
Modül:

 Seeder

Çıktı formatı: JSON (seeder hazır)
8 varyasyon: Zorunlu
FAQ minimum: 10 soru
İkon sistemi: FontAwesome 6
```

---

## 📋 15. SEEDER TEMPLATE YAPISI

### Dosya Adı:
```
Modules/Shop/database/seeders/[Category]_[Model]_Seeder.php
```

Örnek: `Transpalet_F4_201_Seeder.php`

### Template (Kısaltılmış):
```php
<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Shop\Models\ShopProduct;
use Modules\Shop\Models\ShopCategory;

class Transpalet_F4_201_Seeder extends Seeder
{
    public function run()
    {
        $category = ShopCategory::where('slug', 'transpalet')->first();

        $product = ShopProduct::create([
            'sku' => 'F4-201',
            'category_id' => $category->id,
            'brand_id' => 1, // EP Equipment

            // Başlık standardizasyonu
            'title' => json_encode([
                'tr' => 'F4 1.5 Ton Lityum Akülü Transpalet',
                'en' => 'F4 1.5 Ton Li-Ion Pallet Truck'
            ]),

            // 8 Varyasyonlu içerik
            'content_variations' => json_encode([
                'li-ion-battery' => [
                    'technical' => '24V 20Ah Li-Ion batarya, 4-6 saat operasyon',
                    'benefit' => 'Tam gün çalış, şarj bekleme',
                    'slogan' => 'Bir Şarj, Tam Gün İş!',
                    'motto' => 'Li-Ion teknoloji, sınırsız verimlilik',
                    'short_bullet' => '4-6 saat kesintisiz, sıfır bakım',
                    'long_description' => '...',
                    'comparison' => 'Kurşun aside göre 3x uzun ömür',
                    'keywords' => 'lityum, li-ion, akü, batarya',
                    'icon' => 'fa-battery-full',
                    'icon_color' => 'success'
                ]
            ]),

            // Anahtar kelimeler (AI için)
            'keywords' => json_encode([
                'primary' => ['transpalet', '1.5 ton', 'li-ion', 'kompakt'],
                'synonyms' => ['palet taşıyıcı', 'el transpaleti'],
                'usage_jargon' => ['soğuk hava', 'frigo', 'dar koridor']
            ]),

            // FAQ (Min 10)
            'faq' => json_encode([
                [
                    'category' => 'usage',
                    'question' => 'Hangi sektörlerde kullanılır?',
                    'answer' => '...',
                    'icon' => 'fa-industry'
                ]
            ]),

            // Sektörler (15-30)
            'industries' => json_encode([
                ['name' => 'Market', 'icon' => 'fa-shopping-cart', 'relevance' => 'high']
            ]),

            // Teknik özellikler (Accordion)
            'technical_specs' => json_encode([
                'general' => [
                    'category_name' => 'Genel Özellikler',
                    'icon' => 'fa-info-circle',
                    'properties' => [
                        ['key' => 'Model', 'value' => 'F4', 'unit' => '']
                    ]
                ]
            ])
        ]);
    }
}
```

---

## 🎯 16. ÖZET KONTROL LİSTESİ

### Her Ürün İçin Zorunlu:

- [ ] **Başlık**: Standardizasyona uygun (Model Kapasite Enerji Kategori)
- [ ] **8 Varyasyon**: Her özellik için 8 stilde anlatım
- [ ] **FAQ**: Minimum 10 soru (5 kategoride dağılmış)
- [ ] **Anahtar Kelimeler**: 3 kategori (Ana, Eş anlamlı, Kullanım)
- [ ] **Sektörler**: 15-30 sektör (ürün özelliklerine göre)
- [ ] **Teknik Özellikler**: Accordion yapısında (8-15 kategori)
- [ ] **İkonlar**: Her içerik için FontAwesome 6 ikonu
- [ ] **Açıklama**: 3 katmanlı (Hikaye + Profesyonel + Detay = 400-600 kelime)
- [ ] **Rekabet Avantajları**: Karşılaştırmalı veri ile
- [ ] **Kullanım Senaryoları**: Spesifik sektör/ortam örnekleri
- [ ] **Opsiyonlar**: Kategorilendirilmiş ekstra donanımlar

### Yasak İçerikler:
- [ ] ❌ CTA yok
- [ ] ❌ Kampanya/indirim mesajı yok
- [ ] ❌ Sahte sosyal kanıt yok
- [ ] ❌ Aciliyet mesajı yok

---

## 📞 İLETİŞİM & DESTEK

Bu döküman Shop System V4'ün temel kurallarını içerir.

**Dosya Konumu**: `/var/www/vhosts/tuufi.com/httpdocs/readme/shop-system-v4/V4-SYSTEM-RULES.md`

**İlgili Dosyalar**:
- `F4-karsilastirma.html` - Mevcut vs Yeni sistem karşılaştırması
- (İlerleyen aşamalarda eklenecek: Parser algoritması, seeder örnekleri)

**Versiyon**: V4.0
**Son Güncelleme**: 2025-01-01
