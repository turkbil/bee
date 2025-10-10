# 🎯 SHOP SİSTEMİ V2 - KOMPLE DÖKÜMANTAS YON

## 📚 DOSYALAR

| # | Dosya | Açıklama |
|---|-------|----------|
| 0 | **00-GENEL-BAKIS.md** | Sistemin genel yapısı, mimari yaklaşım, veri akışı |
| 1 | **01-KATEGORI-SPECS.md** | Her kategorinin sabit 4 kart özellikleri (Transpalet, Forklift, vs.) |
| 2 | **02-FAQ-SISTEMI.md** | FAQ'ların yapısı, neden ayrı tablo değil, zorunlu konular |
| 3 | **03-AI-KURALLARI.md** | **ESKİ KURALLARIN** - PDF→JSON dönüşüm kuralları, Türkçe %100 |
| 4 | **04-JSON-SABLONU.md** | Standart JSON şablonu (AI'ya verilecek) |
| 5 | **05-SEEDER-KURULUM.md** | Database kurulum adımları, seeder sırası, troubleshooting |
| 6 | **06-LANDING-PAGE-YAPISI.md** | Frontend Blade şablonu, CSS, route, controller |
| 🤖 | **AI-PROMPT.md** | AI'ya verilecek tam kılavuz (path'ler, görevler, senaryolar) |
| 📋 | **PRODUCT-TODO-TEMPLATE.md** | Her ürün için TODO listesi şablonu (kopyala-kullan) |

---

## 🚀 HIZLI BAŞLANGIÇ

```bash
# 1. Dokümantasyonu oku
cat readme/shop-system-v2/00-GENEL-BAKIS.md

# 2. Kategorileri oluştur
php artisan db:seed --class=ShopCategoryWithSpecsSeeder

# 3. Attribute'ları oluştur
php artisan db:seed --class=ShopAttributeSeeder

# 4. Bir PDF'i AI ile JSON'a çevir
# → AI-PROMPT.md dosyasını AI'ya ver (tüm talimatlar orada)
# → PRODUCT-TODO-TEMPLATE.md şablonunu her ürün için kopyala
# → JSON'u readme/shop-system-v2/json-extracts/ klasörüne kaydet

# 5. Tüm ürünleri yükle
php artisan db:seed --class=ShopProductMasterSeeder

# 6. Cache temizle
php artisan app:clear-all

# 7. Landing page'i test et
# http://laravel.test/shop/urun/f4-201-2-ton-48v-li-ion-transpalet
```

---

## 📊 SİSTEM MİMARİSİ ÖZET

### ✅ **HYBRID YAKLAŞIM**

| Veri Tipi | Konum | Amaç |
|-----------|-------|------|
| **Rich Content** | `shop_products` (JSON) | Landing page, marketing içerik |
| **FAQ** | `shop_products.faq_data` (JSON) | Ürüne özel sorular, performans |
| **Primary Specs** | `shop_products.primary_specs` (JSON) | 4 vitrin kartı (kategori bazlı) |
| **Variants** | `shop_product_variants` (tablo) | Fiziksel farklılıklar (çatal, batarya) |
| **Attributes** | `shop_product_attributes` (tablo) | Filtreleme (voltaj, kapasite, vb.) |

### ✅ **2 TEMEL İHTİYAÇ KARŞILANIYOR**

1. **PRODUCTS SAYFASI**
   - Filtreleme: `shop_product_attributes` tablosu
   - Listeleme: `shop_products` + `shop_categories`
   - Detay: `shop_products` (tek query)

2. **LANDING PAGE**
   - Marketing: `long_description` (intro + body)
   - Vitrin Kartları: `primary_specs` (4 kart)
   - Branding: `features.branding` (slogan, motto, technical_summary)
   - Kullanım Alanları: `use_cases` (6+)
   - Avantajlar: `competitive_advantages` (5+)
   - Sektörler: `target_industries` (20+)
   - FAQ: `faq_data` (10+)

---

## ✅ ESKİ KURALLARIN KORUNDU

### 📝 **İçerik Kuralları**
- ✅ Tüm metinler %100 Türkçe (`en` alanı Türkçe kopya)
- ✅ `marketing-intro` + `marketing-body` yapısı
- ✅ `features.branding`: slogan, motto, technical_summary
- ✅ `use_cases` ≥ 6, `competitive_advantages` ≥ 5
- ✅ `target_industries` ≥ 20, `faq_data` ≥ 10
- ✅ `primary_specs`: 4 kart (kategori bazlı)

### 📞 **İletişim & Hizmetler**
- ✅ Telefon: `0216 755 3 555`
- ✅ E-posta: `info@ixtif.com`
- ✅ İXTİF hizmetleri: ikinci el, kiralık, yedek parça, teknik servis

### 🔑 **SEO & Pazarlama**
- ✅ Anahtar kelimeler `marketing-body`'de liste olarak
- ✅ Duygusal tetikleyiciler (prestij, şampiyon, vitrin, vs.)
- ✅ Son kullanıcı odaklı (B2B detaylardan kaçın)

---

## 📁 KLASÖR YAPISI

```
readme/shop-system-v2/
├── README.md                           ← (Bu dosya)
├── 00-GENEL-BAKIS.md
├── 01-KATEGORI-SPECS.md
├── 02-FAQ-SISTEMI.md
├── 03-AI-KURALLARI.md                  ← ESKİ KURALLARIN
├── 04-JSON-SABLONU.md
├── 05-SEEDER-KURULUM.md
├── 06-LANDING-PAGE-YAPISI.md
├── AI-PROMPT.md                        ← 🤖 AI için tam kılavuz
├── PRODUCT-TODO-TEMPLATE.md            ← 📋 TODO şablonu (kopyala-kullan)
├── MIGRATION-KURULUM.md
├── MIGRATION-DURUMU.md
└── json-extracts/                      ← AI'dan dönen JSON'lar buraya
    ├── F4-201-transpalet.json
    ├── F4-202-transpalet.json
    └── ...
```

---

## 🎯 İŞ AKIŞI

```
┌─────────────────┐
│   EP PDF'LER    │
│  (Klasörlerde)  │
└────────┬────────┘
         │
         │ (1) AI'ya gönder (03-AI-KURALLARI.md + 04-JSON-SABLONU.md)
         ▼
┌─────────────────┐
│  JSON EXTRACTS  │
│  (Ürün başına)  │
└────────┬────────┘
         │
         │ (2) ShopProductMasterSeeder
         ▼
┌──────────────────────────────────┐
│        DATABASE                   │
│  ┌────────────────────────────┐  │
│  │  shop_products (Rich)      │  │
│  │  - faq_data (JSON)         │  │
│  │  - primary_specs (JSON)    │  │
│  └───┬─────────────┬──────────┘  │
│      │             │              │
│  ┌───▼─────┐  ┌───▼───────────┐ │
│  │VARIANTS │  │  ATTRIBUTES   │ │
│  └─────────┘  └───────────────┘ │
└──────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────┐
│         FRONTEND                  │
│  ┌───────────┐  ┌──────────────┐│
│  │ PRODUCTS  │  │ LANDING PAGE ││
│  │ PAGE      │  │ (Ürün bazlı) ││
│  └───────────┘  └──────────────┘│
└──────────────────────────────────┘
```

---

## 🧪 TEST SENARYOLARI

### **Test 1: Kategori Specs**
```bash
php artisan tinker
>>> $category = \DB::table('shop_categories')->where('slug->tr', 'transpalet')->first();
>>> json_decode($category->primary_specs_template, true);
# Beklenen: 4 kart (Denge Tekeri, Li-Ion Akü, Şarj Cihazı, Standart Çatal)
```

### **Test 2: FAQ Sistemi**
```bash
>>> $product = \DB::table('shop_products')->where('sku', 'F4-201')->first();
>>> $faqs = json_decode($product->faq_data, true);
>>> count($faqs);  # ≥ 10 olmalı
>>> $faqs[0]['question']['tr'];  # Türkçe soru
>>> $faqs[0]['answer']['tr'];    # Detaylı Türkçe cevap
```

### **Test 3: Attribute Filtreleme**
```bash
>>> \DB::table('shop_product_attributes')
    ->where('attribute_id', 2)  # Voltaj
    ->where('value_numeric', 48)
    ->count();
# 48V ürün sayısı
```

### **Test 4: Landing Page**
```bash
# Browser'da aç:
http://laravel.test/shop/urun/f4-201-2-ton-48v-li-ion-transpalet

# Kontrol et:
- Hero section (title, short_description)
- Primary Specs (4 kart)
- Marketing content (intro + body)
- Branding (slogan, motto)
- FAQ (10+ soru)
- Contact form
```

---

## 📞 DESTEK

Sorularınız için:
- Dokümantasyon: `readme/shop-system-v2/` klasöründe 6 detaylı dosya
- Eski kurallar: `03-AI-KURALLARI.md`
- JSON şablonu: `04-JSON-SABLONU.md`

---

## 🎉 ÖZELLİKLER

✅ Eski kurallarına %100 uyumlu
✅ FAQ'lar ürün içinde (performanslı)
✅ Kategori bazlı sabit specs
✅ Hybrid mimari (monolithic + normalized)
✅ Landing page hazır (Blade şablonu)
✅ AI entegrasyonu kolay
✅ Çoklu dil desteği (JSON based)
✅ Filtreleme sistemi (attributes)
✅ Varyant yönetimi (fiziksel farklar)
✅ Seeder'lar modüler

---

**TEBRİKLER! Shop Sistemi V2 hazır ve kullanıma açık! 🚀**
