# 🤖 YAPAY ZEKA İÇİN SHOP SYSTEM V2 KILAVUZU

## 📍 PROJE YAPISI VE PATHLER

```
/Users/nurullah/Desktop/cms/laravel/
├── Modules/Shop/
│   ├── app/
│   │   ├── Models/
│   │   │   ├── ShopProduct.php              # Ana ürün modeli
│   │   │   ├── ShopCategory.php             # Kategori modeli
│   │   │   ├── ShopProductVariant.php       # Varyant modeli
│   │   │   ├── ShopAttribute.php            # Attribute modeli
│   │   │   └── ShopProductAttribute.php     # Ürün-Attribute ilişki modeli
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   └── Livewire/
│   │   └── Services/
│   │       └── ShopProductService.php
│   ├── database/
│   │   ├── migrations/                      # Ana migration'lar
│   │   │   ├── 029_add_v2_fields_to_shop_products.php
│   │   │   └── 030_add_primary_specs_template_to_shop_categories.php
│   │   └── migrations/tenant/               # Tenant migration'lar
│   │       ├── 029_add_v2_fields_to_shop_products.php
│   │       └── 030_add_primary_specs_template_to_shop_categories.php
│   └── resources/
│       └── views/
│           └── themes/blank/
│               ├── index.blade.php          # Ürün listesi
│               └── show.blade.php           # Landing page
├── Modules/Shop/database/
│   └── seeders/                              # ✨ TÜM SHOP SEEDER'LAR BURADA
│       ├── F4_201_Transpalet_Seeder.php     # F4-201 ürün seeder
│       ├── ShopCategorySeeder.php            # Kategori seeder
│       ├── ShopAttributeSeeder.php           # Attribute seeder
│       ├── ShopProductMasterSeeder.php       # Ana ürün seeder (JSON okur)
│       └── ShopSeeder.php                    # Master seeder
├── app/
│   ├── Helpers/
│   │   └── ShopCategoryMapper.php           # Kategori mapping helper
│   └── Console/Commands/
│       └── ProcessPdfToJson.php             # PDF işleme komutu
└── readme/
    ├── shop-system-v2/                       # ✨ YENİ SISTEM DÖKÜMANLARI
    │   ├── README.md                         # Ana indeks
    │   ├── 00-GENEL-BAKIS.md                # Sistem genel bakış
    │   ├── 01-KATEGORI-SPECS.md             # Kategori template'leri
    │   ├── 02-FAQ-SISTEMI.md                # FAQ yapısı
    │   ├── 03-AI-KURALLARI.md               # AI üretim kuralları
    │   ├── 04-JSON-SABLONU.md               # Standart JSON template
    │   ├── 05-SEEDER-KURULUM.md             # Seeder kurulum
    │   ├── 06-LANDING-PAGE-YAPISI.md        # Frontend template
    │   ├── AI-PROMPT.md                      # 👈 BU DOSYA
    │   ├── MIGRATION-KURULUM.md             # Migration kurulum
    │   ├── MIGRATION-DURUMU.md              # Migration durum raporu
    │   └── json-extracts/                    # JSON çıktı klasörü
    │       ├── F4-201-transpalet.json
    │       └── ...
    └── ecommerce/                            # ❌ ESKİ SİSTEM (KULLANMA!)
        └── ai-prompts/
            ├── prompt.md
            ├── 01-pdf-to-product-json.md
            └── 02-json-to-sql-insert.md
```

---

## 🎯 SİSTEMİN AMACI

### Ana Hedefler:
1. **Ürün Listesi Sayfası** → Filtrelenebilir, aranabilir ürün kataloğu
2. **Landing Page** → Her ürüne özel pazarlama odaklı detay sayfası
3. **Kategori Bazlı Özellikler** → Her kategorinin sabit 4 ana özellik kartı

### Veri Akışı:
```
PDF Katalog
  ↓
JSON Extract (AI ile)
  ↓
Database (Seeder ile)
  ↓
Landing Page (Blade ile)
```

---

## 🤖 YAPAY ZEKA GÖREV TANIMI

### Sen Ne Yapacaksın?

#### 1️⃣ PDF'den JSON Çıkarma
**Görev:** PDF katalogları okuyup yapılandırılmış JSON verisi üret.

**Kullanacağın Dosyalar:**
- **Oku:** `/Users/nurullah/Desktop/cms/EP PDF/[kategori]/[dosya].pdf`
- **Template:** `/Users/nurullah/Desktop/cms/laravel/readme/shop-system-v2/04-JSON-SABLONU.md`
- **Kurallar:** `/Users/nurullah/Desktop/cms/laravel/readme/shop-system-v2/03-AI-KURALLARI.md`
- **Yaz:** `/Users/nurullah/Desktop/cms/laravel/readme/shop-system-v2/json-extracts/[kategori-slug]-[model].json`

**Örnek Komut:**
```bash
# Claude'a şunu söyle:
"Bu PDF'i oku ve Shop System V2 formatında JSON üret:
/Users/nurullah/Desktop/cms/EP PDF/F4 TRANSPALET/F4-201 Seri Li-Ion Akülü Transpalet.pdf

Kuralları buradan al: readme/shop-system-v2/03-AI-KURALLARI.md
Template'i buradan al: readme/shop-system-v2/04-JSON-SABLONU.md
Çıktıyı buraya yaz: readme/shop-system-v2/json-extracts/F4-201-transpalet.json"
```

#### 2️⃣ JSON Verilerini Doğrulama
**Görev:** Üretilen JSON'ların kurallara uygun olup olmadığını kontrol et.

**Kontrol Listesi:**
- ✅ Tüm içerik %100 Türkçe mi?
- ✅ `en` alanı Türkçe kopyası mı? (çeviri değil!)
- ✅ İletişim: `0216 755 3 555` ve `info@ixtif.com` var mı?
- ✅ İXTİF servisleri belirtilmiş mi? (ikinci el, kiralık, yedek parça, teknik servis)
- ✅ Minimum sayılar:
  - `use_cases` ≥ 6 senaryo
  - `competitive_advantages` ≥ 5 avantaj
  - `target_industries` ≥ 20 sektör
  - `faq_data` ≥ 10 soru-cevap
- ✅ `primary_specs` → Kategori template'ine uygun 4 kart
- ✅ Marketing içeriği: `<section class="marketing-intro">` + `<section class="marketing-body">`

#### 3️⃣ Kategori Template'lerini Uygulama
**Görev:** Her kategoriye özel sabit 4 kartlık yapıyı JSON'a ekle.

**Kategori Mapping:**
```php
// readme/shop-system-v2/01-KATEGORI-SPECS.md dosyasına bak

"F4 TRANSPALET" → primary_specs_template (Denge Tekeri, Li-Ion Akü, Şarj Cihazı, Standart Çatal)
"F5 FORKLIFT" → primary_specs_template (Mast Tipi, Motor Gücü, Yük Merkezi, Kabin)
"F6 İSTİF MAKİNESİ" → primary_specs_template (Yürüyüşlü/Sürücülü, Akü Kapasitesi, Mast Yüksekliği, Çatal Genişliği)
// vs...
```

**Örnek Kullanım:**
```bash
# Claude'a şunu söyle:
"readme/shop-system-v2/01-KATEGORI-SPECS.md dosyasını oku.
F4-201 Transpalet için primary_specs JSON'unu üret.
Template'deki 4 kartı doldur."
```

---

## 📋 KULLANIM SENARYOLARI

### Senaryo 1: Yeni PDF İşleme (Tek Ürün)
```bash
# 1. PDF'i oku
Dosya: /Users/nurullah/Desktop/cms/EP PDF/F4 TRANSPALET/F4-201 Seri Li-Ion Akülü Transpalet.pdf

# 2. AI'ya komut ver
"Bu PDF'ten readme/shop-system-v2/04-JSON-SABLONU.md formatında JSON üret.
Kurallar: readme/shop-system-v2/03-AI-KURALLARI.md
Kategori template: readme/shop-system-v2/01-KATEGORI-SPECS.md → Transpalet
Çıktı: readme/shop-system-v2/json-extracts/F4-201-transpalet.json"

# 3. JSON doğrula
php artisan tinker
>>> $json = json_decode(file_get_contents('readme/shop-system-v2/json-extracts/F4-201-transpalet.json'), true);
>>> count($json['use_cases']['tr']); // ≥ 6 olmalı
>>> count($json['faq_data']); // ≥ 10 olmalı

# 4. Database'e yükle
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\ShopProductMasterSeeder
# VEYA kısa yol:
php artisan db:seed --class=F4_201_Transpalet_Seeder
```

### Senaryo 2: Toplu PDF İşleme (Klasör)
```bash
# 1. AI'ya komut ver
"readme/shop-system-v2/04-JSON-SABLONU.md ve 03-AI-KURALLARI.md kullan.
/Users/nurullah/Desktop/cms/EP PDF/F4 TRANSPALET/ klasöründeki TÜM PDF'leri işle.
Her biri için ayrı JSON üret: readme/shop-system-v2/json-extracts/
Kategori template: Transpalet (01-KATEGORI-SPECS.md'den al)"

# 2. Toplu yükleme
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\ShopProductMasterSeeder
```

### Senaryo 3: Mevcut JSON Güncelleme
```bash
# 1. AI'ya komut ver
"readme/shop-system-v2/json-extracts/F4-201-transpalet.json dosyasını oku.
FAQ sayısını 15'e çıkar (şu an 10).
Yeni sorular ekle, JSON'u güncelle."

# 2. Değişiklikleri database'e al
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\ShopProductMasterSeeder --force
```

### Senaryo 4: Kategori Template Değiştirme
```bash
# 1. AI'ya komut ver
"readme/shop-system-v2/01-KATEGORI-SPECS.md dosyasını düzenle.
Transpalet kategorisi için 4. kartı değiştir:
Eski: Standart Çatal
Yeni: Garanti Süresi

Template'i güncelle ve tüm transpalet JSON'larını yeniden üret."

# 2. Database template'ini güncelle
php artisan tinker
>>> $category = \Modules\Shop\app\Models\ShopCategory::where('slug', 'transpalet')->first();
>>> $category->primary_specs_template = [...yeni template...];
>>> $category->save();
```

---

## 🔧 ÖNEMLİ KURALLAR

### ❌ YAPMA:
- **ESKİ DÖKÜMANLARDAKİ KURALLARI KULLANMA!**
  - ❌ `/readme/ecommerce/ai-prompts/prompt.md` → ESKİ, KULLANMA!
  - ❌ `/readme/ecommerce/ai-prompts/01-pdf-to-product-json.md` → ESKİ, KULLANMA!

### ✅ YAP:
- **YENİ SİSTEM DÖKÜMANLARINDAKİ KURALLARI KULLAN!**
  - ✅ `/readme/shop-system-v2/03-AI-KURALLARI.md` → YENİ, BU KURALLARI KULLAN!
  - ✅ `/readme/shop-system-v2/04-JSON-SABLONU.md` → YENİ, BU TEMPLATE'İ KULLAN!

### 🚨 KRİTİK UYARILAR:
1. **Türkçe Zorunlu:** Her alan %100 Türkçe olmalı
2. **"en" = Türkçe Kopyası:** İngilizce çeviri yapma, Türkçe'yi kopyala
3. **İletişim Sabit:** `0216 755 3 555` ve `info@ixtif.com` değişmez
4. **Minimum Sayılar:** Altına düşme (use_cases≥6, faq≥10, vs.)
5. **Template Uyumu:** Kategori template'ini tam uygula

---

## 📊 VERI YAPISI KAVRAMI

### Hibrit Yaklaşım (Çok Önemli!)

**shop_products Tablosu (Monolitik JSON):**
```sql
-- Zengin içerik burada (tek query ile tüm data)
primary_specs          JSON   -- 4 vitrin kartı
use_cases              JSON   -- 6+ kullanım alanı
competitive_advantages JSON   -- 5+ rekabet avantajı
target_industries      JSON   -- 20+ hedef sektör
faq_data              JSON   -- 10+ soru-cevap
marketing_content     JSON   -- Pazarlama metinleri
highlighted_features  JSON   -- Öne çıkan özellikler
```

**shop_product_variants Tablosu (Normalize):**
```sql
-- Fiziksel farklılıklar burada (fiyat, stok, SKU)
- Fork uzunluğu değişiklikleri
- Batarya konfigürasyonları
- Renk seçenekleri
```

**shop_attributes + shop_product_attributes (Normalize):**
```sql
-- Filtrelenebilir özellikler burada
- Yük Kapasitesi: 1000kg, 1500kg, 2000kg
- Voltaj: 24V, 48V
- Batarya Tipi: Li-Ion, Asit
```

### Neden Bu Yapı?

**Landing Page:** Tek query → Hızlı
```php
$product = ShopProduct::find(1101);
// Tüm data hazır: specs, faq, marketing, features
```

**Ürün Listesi:** Join ile filtrele
```php
ShopProduct::whereHas('attributes', function($q) {
    $q->where('slug', 'yuk-kapasitesi')->wherePivot('value', '2000kg');
})->get();
```

---

## 🎯 AI ÇIKTI KALİTE KONTROL

### Başarılı JSON Örneği:
```json
{
  "category_slug": "transpalet",
  "model_code": "F4-201",
  "primary_specs": [
    {"label": "Denge Tekeri", "value": "Yok"},
    {"label": "Li-Ion Akü", "value": "24V/20Ah"},
    {"label": "Şarj Cihazı", "value": "24V/5A"},
    {"label": "Standart Çatal", "value": "1150 x 560 mm"}
  ],
  "use_cases": {
    "tr": [
      "Dar koridorlarda palet taşıma",
      "Depo içi yükleme-boşaltma",
      "... (toplam 6+ senaryo)"
    ],
    "en": "[Türkçe kopya]"
  },
  "faq_data": [
    {
      "question": {"tr": "Li-Ion akü avantajları nelerdir?", "en": "[aynı]"},
      "answer": {"tr": "Detaylı Türkçe cevap...", "en": "[aynı]"},
      "sort_order": 1
    }
    // ... (toplam 10+ soru)
  ],
  "contact": {
    "phone": "0216 755 3 555",
    "email": "info@ixtif.com"
  }
}
```

### Hatalı JSON Örneği (Düzelt!):
```json
{
  "use_cases": {
    "tr": ["Senaryo 1", "Senaryo 2"], // ❌ 6'dan az!
    "en": ["Scenario 1", "Scenario 2"] // ❌ İngilizce çeviri yapılmış!
  },
  "faq_data": [ // ❌ 5 soru var, 10 olmalı!
    {"question": {"tr": "..."}, "answer": {"tr": "..."}}
  ],
  "contact": {
    "phone": "0555 123 4567" // ❌ Yanlış numara!
  }
}
```

---

## ✅ HER ÜRÜN İÇİN TODO LİSTESİ

### 📋 Ürün İşleme Kontrol Listesi

Her ürün için bu listeyi kullan. Tamamlanan maddeleri `[x]` ile işaretle:

```markdown
## ÜRÜN: [Model Kodu - Ürün Adı]
**PDF Dosyası:** `/Users/nurullah/Desktop/cms/EP PDF/[Kategori]/[dosya-adı].pdf`
**JSON Çıktısı:** `readme/shop-system-v2/json-extracts/[kategori-slug]-[model].json`

### 1️⃣ PDF OKUMA VE HAZIRLIK
- [ ] PDF dosyası okundu
- [ ] Kategori belirlendi (Transpalet/Forklift/İstif/vb.)
- [ ] Model kodu tespit edildi
- [ ] readme/shop-system-v2/01-KATEGORI-SPECS.md → Kategori template'i okundu
- [ ] readme/shop-system-v2/03-AI-KURALLARI.md → AI kuralları okundu
- [ ] readme/shop-system-v2/04-JSON-SABLONU.md → JSON template okundu

### 2️⃣ TEMEL BİLGİLER
- [ ] `category_slug` doğru belirlendi
- [ ] `model_code` doğru girildi (örn: F4-201)
- [ ] `name.tr` Türkçe ürün adı yazıldı
- [ ] `name.en` Türkçe kopyası yapıldı (çeviri DEĞİL!)
- [ ] `slug` oluşturuldu (örn: f4-201-transpalet)
- [ ] `sku` benzersiz kod verildi

### 3️⃣ PRIMARY SPECS (4 KART)
- [ ] Kategori template'inden 4 kart alındı
- [ ] Kart 1: Label + Value dolduruldu
- [ ] Kart 2: Label + Value dolduruldu
- [ ] Kart 3: Label + Value dolduruldu
- [ ] Kart 4: Label + Value dolduruldu
- [ ] Tüm kartlar kategori yapısına uygun

### 4️⃣ MARKETING CONTENT
- [ ] `marketing_content.intro.tr` yazıldı (giriş paragrafı)
- [ ] `marketing_content.intro.en` Türkçe kopyası yapıldı
- [ ] `marketing_content.body.tr` yazıldı (detaylı içerik)
- [ ] `marketing_content.body.en` Türkçe kopyası yapıldı
- [ ] HTML formatı: `<section class="marketing-intro">` kullanıldı
- [ ] HTML formatı: `<section class="marketing-body">` kullanıldı
- [ ] İXTİF servisleri belirtildi (ikinci el, kiralık, yedek parça, teknik servis)
- [ ] İletişim bilgileri eklendi: 0216 755 3 555, info@ixtif.com

### 5️⃣ HIGHLIGHTED FEATURES
- [ ] Minimum 3 öne çıkan özellik yazıldı
- [ ] Her özellik Türkçe
- [ ] `en` alanları Türkçe kopyası

### 6️⃣ USE CASES (Kullanım Alanları)
- [ ] Minimum 6 kullanım senaryosu yazıldı ✅ (6+ zorunlu)
- [ ] Tüm senaryolar Türkçe
- [ ] `use_cases.tr` array dolduruldu
- [ ] `use_cases.en` Türkçe kopyası yapıldı
- [ ] Gerçekçi kullanım örnekleri verildi

### 7️⃣ COMPETITIVE ADVANTAGES (Rekabet Avantajları)
- [ ] Minimum 5 rekabet avantajı yazıldı ✅ (5+ zorunlu)
- [ ] Tüm avantajlar Türkçe
- [ ] `competitive_advantages.tr` array dolduruldu
- [ ] `competitive_advantages.en` Türkçe kopyası yapıldı
- [ ] Somut avantajlar belirtildi

### 8️⃣ TARGET INDUSTRIES (Hedef Sektörler)
- [ ] Minimum 20 hedef sektör yazıldı ✅ (20+ zorunlu)
- [ ] Tüm sektörler Türkçe
- [ ] `target_industries.tr` array dolduruldu
- [ ] `target_industries.en` Türkçe kopyası yapıldı
- [ ] Çeşitli sektörler kapsandı (lojistik, gıda, otomotiv, vb.)

### 9️⃣ FAQ DATA (Sık Sorulan Sorular)
- [ ] Minimum 10 soru-cevap yazıldı ✅ (10+ zorunlu)
- [ ] Her FAQ için `question.tr` dolduruldu
- [ ] Her FAQ için `question.en` Türkçe kopyası yapıldı
- [ ] Her FAQ için `answer.tr` detaylı cevap yazıldı
- [ ] Her FAQ için `answer.en` Türkçe kopyası yapıldı
- [ ] Her FAQ için `sort_order` verildi (1'den başla)
- [ ] Sorular gerçekçi ve faydalı

### 🔟 TEKNİK ÖZELLİKLER (Specifications)
- [ ] `specifications.dimensions` dolduruldu (ölçüler)
- [ ] `specifications.capacity` dolduruldu (kapasite)
- [ ] `specifications.performance` dolduruldu (performans)
- [ ] `specifications.electrical` dolduruldu (elektrik - varsa)
- [ ] `specifications.safety` dolduruldu (güvenlik)
- [ ] `specifications.options` dolduruldu (opsiyonlar)
- [ ] Tüm teknik veriler PDF'den alındı
- [ ] Birimler doğru girildi (mm, kg, V, A, vb.)

### 1️⃣1️⃣ VARIANTS (Varyantlar)
- [ ] Tüm varyantlar tespit edildi
- [ ] Her varyant için `variant_name` verildi
- [ ] Her varyant için `sku` oluşturuldu
- [ ] Varyant farklılıkları `specifications` içinde belirtildi

### 1️⃣2️⃣ ATTRIBUTES (Filtrelenebilir Özellikler)
- [ ] `attributes.yuk_kapasitesi` belirlendi
- [ ] `attributes.voltaj` belirlendi (varsa)
- [ ] `attributes.batarya_tipi` belirlendi (varsa)
- [ ] `attributes.mast_yuksekligi` belirlendi (varsa)
- [ ] Diğer filtrelenebilir özellikler eklendi

### 1️⃣3️⃣ MEDYA
- [ ] `images` array oluşturuldu
- [ ] Ana görsel path'i verildi
- [ ] Galeri görselleri listeye eklendi (varsa)
- [ ] `videos` array oluşturuldu (varsa)
- [ ] Video URL'leri eklendi (varsa)

### 1️⃣4️⃣ SON KONTROLLER
- [ ] JSON syntax hatası yok (valid JSON)
- [ ] Tüm zorunlu alanlar dolu
- [ ] Hiç İngilizce çeviri yok (hepsi Türkçe kopyası)
- [ ] İletişim bilgileri doğru: 0216 755 3 555, info@ixtif.com
- [ ] Minimum sayı kontrolleri geçti:
  - [ ] use_cases ≥ 6 ✅
  - [ ] competitive_advantages ≥ 5 ✅
  - [ ] target_industries ≥ 20 ✅
  - [ ] faq_data ≥ 10 ✅
- [ ] JSON dosyası kaydedildi: `readme/shop-system-v2/json-extracts/[isim].json`

### 1️⃣5️⃣ RAPORLAMA
- [ ] Özet rapor hazırlandı
- [ ] Eksik alanlar belirtildi (varsa)
- [ ] Uyarılar listeye eklendi (varsa)
- [ ] TODO listesi tamamlandı olarak işaretlendi

---

## ✅ TAMAMLANAN ÜRÜNLER
Buraya işlenen ürünleri ekle:

- [x] **F4-201 Transpalet** → `json-extracts/F4-201-transpalet.json` ✅
- [ ] **F4-202 Transpalet** → `json-extracts/F4-202-transpalet.json`
- [ ] **F5-301 Forklift** → `json-extracts/F5-301-forklift.json`
```

### 🎯 AI İçin Kullanım:

Her yeni ürün işlenirken **PRODUCT-TODO-TEMPLATE.md** dosyasını kullan:

```bash
# Claude'a şunu söyle:
"readme/shop-system-v2/PRODUCT-TODO-TEMPLATE.md dosyasını kopyala.
Yeni dosya adı: PRODUCT-TODO-F4-201-transpalet.md

Bu template'i kullanarak F4-201 Transpalet ürününü işle:
1. PDF'i oku: /Users/nurullah/Desktop/cms/EP PDF/F4 TRANSPALET/F4-201...
2. Her adımı tamamladıkça [x] ile işaretle
3. Boş alanları (_____) doldur
4. İstatistik tablosunu güncelle
5. JSON'u oluştur ve kaydet

Sonunda:
- Tamamlanmış TODO dosyasını kaydet
- Özet raporu göster
- JSON dosya yolunu ver"
```

**Önemli:**
- Her ürün için **ayrı TODO dosyası** oluştur
- Dosya adı formatı: `PRODUCT-TODO-[kategori-slug]-[model].md`
- Örnek: `PRODUCT-TODO-F4-201-transpalet.md`

---

## 🚀 HIZLI BAŞLANGIÇ

### AI'ya İlk Komut (Örnek: F4-201 Transpalet):
```
"readme/shop-system-v2/ klasöründeki dökümanları oku:
1. 03-AI-KURALLARI.md → Kuralları öğren
2. 04-JSON-SABLONU.md → Template'i öğren
3. 01-KATEGORI-SPECS.md → Kategori yapısını öğren
4. PRODUCT-TODO-TEMPLATE.md → TODO şablonunu öğren

Sonra şu işlemleri yap:

ADIM 1: TODO Dosyası Oluştur
- PRODUCT-TODO-TEMPLATE.md'yi kopyala
- Yeni dosya: readme/shop-system-v2/PRODUCT-TODO-F4-201-transpalet.md

ADIM 2: PDF İşle
- PDF oku: /Users/nurullah/Desktop/cms/EP PDF/F4 TRANSPALET/F4-201 Seri Li-Ion Akülü Transpalet.pdf
- TODO listesini takip et, her adımı [x] ile işaretle
- Boş alanları doldur (_____)

ADIM 3: JSON Üret
- JSON oluştur: readme/shop-system-v2/json-extracts/F4-201-transpalet.json
- Tüm kuralları uygula (Türkçe, min sayılar, vb.)

ADIM 4: Kontrol ve Rapor
- TODO dosyasındaki istatistik tablosunu doldur
- Özet rapor hazırla
- Eksikleri belirt (varsa)

Tamamlandığında bana şunları göster:
✅ Tamamlanmış TODO dosyası
✅ Özet rapor
✅ JSON dosya yolu
✅ İstatistikler (use_cases: X, faq: Y, vb.)"
```

---

## 📞 DESTEK VE SORUN GİDERME

### Sık Karşılaşılan Hatalar:

**1. "Kategori template bulunamadı"**
```bash
# Çözüm:
readme/shop-system-v2/01-KATEGORI-SPECS.md dosyasını kontrol et.
PDF klasör adı ile kategori slug'ı eşleştir:
"F4 TRANSPALET" → "transpalet"
```

**2. "Minimum sayı kontrolü başarısız"**
```bash
# Çözüm:
use_cases → min 6
competitive_advantages → min 5
target_industries → min 20
faq_data → min 10
```

**3. "İngilizce içerik tespit edildi"**
```bash
# Çözüm:
"en" alanlarına Türkçe'nin kopyasını yaz, çeviri yapma!
```

---

**🎉 Artık Shop System V2 için AI üretim sürecine hazırsın!**

Tüm kurallar, template'ler ve path'ler bu dosyada. Başarılar! 🚀
