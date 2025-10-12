# 🤖 YAPAY ZEKA İÇİN SHOP SYSTEM V2 KILAVUZU

## 🇹🇷 KRİTİK: %100 TÜRKÇE KURALI

**🚨 EN ÖNEMLİ KURAL:**

### **TÜM JSON KEY'LER VE VALUE'LER TÜRKÇE OLACAK!**

```json
❌ YANLIŞ:
{
  "fork_length": 1150,
  "battery_type": "Li-Ion",
  "load_capacity": 2000
}

✅ DOĞRU:
{
  "catal_uzunlugu": 1150,
  "aku_tipi": "Li-Ion",
  "yuk_kapasitesi": 2000
}
```

**NEDEN?**
- Frontend dinamik render yapacak
- `catal_uzunlugu` → "Çatal Uzunluğu" (otomatik güzelleştirilecek)
- Çeviri katmanı gereksiz
- PDF'den gelen her alan direkt Türkçe key ile kaydedilecek

**ÖZEL DURUMLAR:**
- `ş, ı, ç, ü, ö, ğ` → `s, i, c, u, o, g` (slug-friendly key'ler için)
- Örnek: `yerden_yükseklik` → `yerden_yukseklik` (key için)
- Örnek: `Yerden Yükseklik` → Düz metin (value için - orijinal Türkçe kalır)

---

## 📄 LANDING PAGE ZORUNLU SECTIONS

**KULLANICI İSTEĞİ:** Her ürün sayfasında **MUTLAKA** şu sections olmalıdır:

1. 🔀 **Varyantlar** (Variants) - parent/child product listesi
2. ⚡ **Özellikler** (Features) - features.list + features.branding (slogan, motto, technical_summary)
3. 🌟 **Öne Çıkanlar** (Highlighted Features) - highlighted_features (icon, priority, title, description min 3)
4. 📋 **Avantajlar** (Advantages) - features.branding extra vurgu
5. 🏆 **Rekabet** (Competitive Advantages) - competitive_advantages (min 5)
6. 🏢 **Sektörler** (Target Industries) - target_industries (min 20 - DİNAMİK: varyanta göre değişir)
7. 🔧 **Teknik** (Technical Specs) - technical_specs (PDF'den gelen TÜM alanlar, SINIR YOK, tablo olarak geliyorsa aynen çevir)
8. 🎯 **Kullanım** (Use Cases) - use_cases (min 6 - DİNAMİK: varyanta göre değişir)
9. ❓ **S.S.S** (FAQ) - faq_data (min 10 soru-cevap: question, answer, sort_order)
10. 🛠️ **Opsiyonlar** (Accessories) - accessories (PDF'den gelen opsiyonel ekipmanlar)
11. 📜 **Sertifikalar** (Certifications) - certifications (CE, ISO vs.)
12. 🖼️ **Medya Galerisi** (Media Gallery) - media_gallery (ALAN OLACAK AMA MANUEL EKLENİR)
13. ✉️ **Teklif Al** (Quote Form) - contact bilgileri (0216 755 3 555, info@ixtif.com)

**🚨 ZORUNLU:** Her section DOLU olmalı! Boş section ASLA olmamalı! Her ürün ve her varyant bu sections'lara sahip olmalı!

**📸 MEDYA NOT:** `media_gallery` alanı sistemde olacak ama fotoğraflar MANUEL eklenecek. Placeholder URL koy veya boş array bırak.

---

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
  - ✅ `/readme/shop-system-v2/07-DYNAMIC-LANGUAGE-SYSTEM.md` → DİNAMİK DİL SİSTEMİ!

### 🚨 KRİTİK UYARILAR:
1. **🇹🇷 SADECE TÜRKÇE!**
   - **Tüm içerik %100 Türkçe yazılacak**
   - **Çoklu dil YOK - sadece Türkçe**
   - **Tüm JSON alanları direkt Türkçe string olacak**
   - **Çeviri YAPMA - her şey direkt Türkçe üretilecek**
2. **İletişim Sabit:** `0216 755 3 555` ve `info@ixtif.com` değişmez
3. **Minimum Sayılar:** Altına düşme (use_cases≥6, faq≥10, vs.)
4. **Template Uyumu:** Kategori template'ini tam uygula
5. **Varyant İçerikleri:** Her varyant TAMAMEN farklı içerik gerektirir (aynı metinleri kopyalama!)

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

## 📂 EP PDF KLASÖRLERİ VE KATEGORİLER

### PDF Kaynak Klasörü
```
/Users/nurullah/Desktop/cms/EP PDF/
├── 1-Forklift/                              → Kategori: forklift
├── 2-Transpalet/                            → Kategori: transpalet
├── 3-İstif Makineleri/                      → Kategori: istif-makinesi
├── 4-Order Picker - Dikey Sipariş/          → Kategori: order-picker
├── 5-Otonom/                                → Kategori: otonom
└── 6-Reach Truck/                           → Kategori: reach-truck
```

### Her Kategori İçin ZORUNLU 4 Ana Özellik (primary_specs)

Her kategoride **AYNI 4 KART** kullanılır. Landing page'de vitrin kartları olarak gösterilir.

#### 1. **TRANSPALET** (2-Transpalet/)

```json
"primary_specs": [
  {"label": "Yük Kapasitesi", "value": "[X] Ton"},
  {"label": "Akü Sistemi", "value": "Li-Ion [X]V"},
  {"label": "Çatal Uzunluğu", "value": "[X] mm"},
  {"label": "Denge Tekeri", "value": "Var/Yok"}
]
```

#### 2. **FORKLIFT** (1-Forklift/)

```json
"primary_specs": [
  {"label": "Yük Kapasitesi", "value": "[X] Ton"},
  {"label": "Mast Yüksekliği", "value": "[X] mm"},
  {"label": "Yakıt Tipi", "value": "Elektrik/Dizel/LPG"},
  {"label": "Kabin Tipi", "value": "Kapalı/Açık"}
]
```

#### 3. **İSTİF MAKİNESİ** (3-İstif Makineleri/)

```json
"primary_specs": [
  {"label": "Yük Kapasitesi", "value": "[X] Ton"},
  {"label": "Kaldırma Yüksekliği", "value": "[X] mm"},
  {"label": "Kullanım Tipi", "value": "Yürüyüşlü/Sürücülü"},
  {"label": "Akü Kapasitesi", "value": "[X]V/[X]Ah"}
]
```

#### 4. **ORDER PICKER** (4-Order Picker/)

```json
"primary_specs": [
  {"label": "Çalışma Yüksekliği", "value": "[X] mm"},
  {"label": "Yük Kapasitesi", "value": "[X] kg"},
  {"label": "Platform Tipi", "value": "Sabit/Hareketli"},
  {"label": "Akü Voltajı", "value": "[X]V"}
]
```

#### 5. **OTONOM** (5-Otonom/)

```json
"primary_specs": [
  {"label": "Otomasyon Seviyesi", "value": "Tam/Yarı Otonom"},
  {"label": "Yük Kapasitesi", "value": "[X] Ton"},
  {"label": "Navigasyon", "value": "Lazer/Kamera/QR"},
  {"label": "Güvenlik Sistemi", "value": "Lidar/3D Kamera"}
]
```

#### 6. **REACH TRUCK** (6-Reach Truck/)

```json
"primary_specs": [
  {"label": "Erişim Yüksekliği", "value": "[X] mm"},
  {"label": "Yük Kapasitesi", "value": "[X] Ton"},
  {"label": "Çatal Uzunluğu", "value": "[X] mm"},
  {"label": "Akü Kapasitesi", "value": "[X]V/[X]Ah"}
]
```

---

## 🔀 ÜRÜN VARYANTLARI SİSTEMİ (PRODUCT-BASED VARIANTS)

### Varyant Yapısı Nedir?

Shop System V2, **her varyantın ayrı bir ürün olduğu** bir varyant sistemi kullanır.

**Eski Sistem (KULLANMA!):**
```
❌ 1 Ana Ürün → shop_products
   └── N Varyant → shop_product_variants (başka tablo)
```

**Yeni Sistem (KULLAN!):**
```
✅ Her varyant = Ayrı Product (shop_products tablosu)
   └── parent_product_id ile birbirine bağlı
```

### Varyant Veri Yapısı

**shop_products tablosuna eklenen kolonlar:**
```sql
parent_product_id    → Ana ürünün product_id'si (NULL = bağımsız ürün)
is_master_product    → Ana ürün mü? (opsiyonel overview page için)
variant_type         → Varyant tipi slug (örn: '1-5-ton', 'denge-tekerlekli')
```

### Kategori → primary_specs Eşleştirmesi

**ÖNEMLİ:** PDF'yi okuduktan sonra kategoriyi belirle ve o kategorinin 4 kartını kullan!

**Örnek:**
```
PDF: "/Users/nurullah/Desktop/cms/EP PDF/2-Transpalet/F4 201/..."
  ↓
Kategori: "transpalet"
  ↓
primary_specs: [
  {"label": "Yük Kapasitesi", "value": "2 Ton"},
  {"label": "Akü Sistemi", "value": "Li-Ion 48V"},
  {"label": "Çatal Uzunluğu", "value": "1150 mm"},
  {"label": "Denge Tekeri", "value": "Yok"}
]
```

### Varyant Örnek Yapısı

**Gerçek Örnek: F4 201 Transpalet Serisi (PDF'den)**

F4 201 için şu varyantlar mevcut:
- **Denge Tekeri:** Var/Yok (castor wheels)
- **Çatal Uzunluğu:** 900mm, 1000mm, 1150mm (standart), 1220mm, 1350mm, 1500mm
- **Çatal Genişliği:** 560mm (standart) veya 685mm (geniş)
- **Batarya Kapasitesi:** 24V/20Ah×2 (standart) veya 24V/20Ah×4 (yüksek kapasite)

**Önerilen Seeder Stratejisi:**

Her **ana özellik farklılığı** için ayrı seeder:

```
Seeders/
├── F4_201_Standart_Seeder.php           → Standart versiyon (2 ton, 1150×560mm çatal, denge tekersiz)
├── F4_201_Denge_Tekerlekli_Seeder.php   → Denge tekerlekli versiyon (dengesiz zeminler için)
├── F4_201_Genis_Catal_Seeder.php        → Geniş çatal (685mm) versiyon (büyük paletler için)
├── F4_201_Uzun_Catal_Seeder.php         → Uzun çatal (1500mm) versiyon (uzun yükler için)
└── F4_201_Yuksek_Kapasite_Seeder.php    → Yüksek batarya kapasiteli versiyon (uzun vardiya)
```

**Seeder Yapısı Örneği:**

```
Product 5: F4 201 Transpalet (Standart)
├── product_id: 5
├── parent_product_id: NULL
├── is_master_product: false  (direkt satılan ürün, master değil)
├── variant_type: 'standart'
├── title: "F4 201 Li-Ion Akülü Transpalet"
├── slug: "f4-201-transpalet"
├── long_description: Standart kullanım senaryoları
├── technical_specs:
│   ├── capacity: 2000 kg
│   ├── fork_dimensions: "1150×560 mm"
│   ├── castor_wheels: false (yok)
│   └── battery: "24V/20Ah×2"
├── use_cases:
│   - "Standart palet taşıma (1000×1200 mm)"
│   - "Dar koridorlu depolarda kullanım"
│   - "E-ticaret fulfilment merkezleri"
│   - "Perakende mağaza arka depoları"
└── faq_data:
    - "Standart çatal uzunluğu yeterli mi?"
    - "Denge tekeri olmadan güvenli mi?"
    - "Hangi palet tiplerine uygun?"

Product 6: F4 201 Transpalet (Denge Tekerlekli)
├── product_id: 6
├── parent_product_id: 5  (standart versiyona bağlı)
├── is_master_product: false
├── variant_type: 'denge-tekerlekli'
├── title: "F4 201 Li-Ion Akülü Transpalet - Denge Tekerlekli"
├── slug: "f4-201-transpalet-denge-tekerlekli"
├── long_description: Denge tekerinin avantajları, dengesiz zeminlerde kullanım
├── technical_specs:
│   ├── capacity: 2000 kg
│   ├── fork_dimensions: "1150×560 mm"
│   ├── castor_wheels: true (VAR!) 👈 FARKLI
│   └── battery: "24V/20Ah×2"
├── use_cases: 👈 TAMAMEN FARKLI
│   - "Dengesiz zeminlerde güvenli taşıma"
│   - "Ağır yüklerde stabilite sağlama"
│   - "Rampalı alanlarda kullanım"
│   - "İnşaat sahalarında malzeme taşıma"
│   - "Bozuk zeminde depo operasyonları"
└── faq_data: 👈 TAMAMEN FARKLI
    - "Denge tekeri ne işe yarar?"
    - "Hangi zeminlerde denge tekeri gereklidir?"
    - "Denge tekerli versiyon daha ağır mı?"
    - "Dar koridorlarda dönüş yapabilir mi?"

Product 7: F4 201 Transpalet (Geniş Çatal)
├── product_id: 7
├── parent_product_id: 5
├── variant_type: 'genis-catal'
├── title: "F4 201 Li-Ion Akülü Transpalet - Geniş Çatal (685mm)"
├── slug: "f4-201-transpalet-genis-catal"
├── long_description: Geniş çatalın avantajları, büyük paletler için
├── technical_specs:
│   ├── fork_dimensions: "1150×685 mm" 👈 FARKLI
├── use_cases: 👈 TAMAMEN FARKLI
│   - "Büyük boyutlu palet taşıma (1200×1400 mm)"
│   - "Geniş taban yüzey alanı gereken yükler"
│   - "Mobilya ve beyaz eşya depoları"
│   - "İnşaat malzemesi lojistiği"
└── faq_data: 👈 TAMAMEN FARKLI
    - "Geniş çatal hangi palet tiplerine uygun?"
    - "Standart çataldan farkı nedir?"
    - "Dar koridorlarda kullanılabilir mi?"

Product 8: F4 201 Transpalet (Uzun Çatal)
├── product_id: 8
├── parent_product_id: 5
├── variant_type: 'uzun-catal-1500mm'
├── title: "F4 201 Li-Ion Akülü Transpalet - Uzun Çatal (1500mm)"
├── slug: "f4-201-transpalet-uzun-catal-1500mm"
├── technical_specs:
│   ├── fork_dimensions: "1500×560 mm" 👈 FARKLI
├── use_cases: 👈 TAMAMEN FARKLI
│   - "Uzun malzeme taşıma (boru, profil, kereste)"
│   - "İki palet yan yana taşıma"
│   - "Tekstil rulolarının taşınması"
│   - "Halı ve zemin kaplama endüstrisi"
└── faq_data: 👈 TAMAMEN FARKLI
    - "1500mm çatal ne kadar yük alabilir?"
    - "Uzun çatalla manevra kabiliyeti nasıldır?"
    - "Hangi malzemeler için idealdir?"

Product 9: F4 201 Transpalet (Yüksek Batarya Kapasiteli)
├── product_id: 9
├── parent_product_id: 5
├── variant_type: 'yuksek-batarya'
├── title: "F4 201 Li-Ion Akülü Transpalet - Yüksek Kapasite (4×20Ah)"
├── slug: "f4-201-transpalet-yuksek-batarya"
├── technical_specs:
│   ├── battery: "24V/20Ah×4" 👈 FARKLI (2 kat daha fazla)
├── use_cases: 👈 TAMAMEN FARKLI
│   - "Uzun vardiya operasyonları (12-16 saat)"
│   - "Yoğun kullanım gerektiren depolar"
│   - "Çok sayıda yükleme-boşaltma işlemi"
│   - "24/7 operasyon süren tesisler"
│   - "Şarj istasyonuna erişimin zor olduğu alanlar"
└── faq_data: 👈 TAMAMEN FARKLI
    - "4 bataryalı versiyon kaç saat çalışır?"
    - "Batarya şarj süresi ne kadar?"
    - "Ağırlık farkı ne kadar?"
    - "Maliyet farkı ne kadar?"
```

### Varyant Oluşturma Kuralları

#### 🚨 ÖNEMLİ: HER DETAY VARYANTLARDA DA OLMALI AMA VARYANTA ÖZEL OLMALI!

**Kullanıcı İsteği:** "her şey değişmeli demiştim diğerlerinde de. bağımsız bir sayfa olmalılar yeni özellikler eklenerek."

#### 1. Her Varyant = TAM BİR ÜRÜN (Master Product ile AYNI Detay Seviyesinde)

Her varyant şunlara sahip olmalı:
- ✅ **Benzersiz başlık** (title) - Varyant tipini belirten
- ✅ **Benzersiz slug** - SEO dostu URL
- ✅ **Kendi açıklaması** (short_description, long_description) - FARKLI içerik!
- ✅ **Kendi teknik özellikleri** (technical_specs) - Varyanta göre değişen değerler
- ✅ **Kendi primary_specs** - Varyanta özel 4 kart
- ✅ **Kendi features** - Varyanta özel özellik listesi
- ✅ **Kendi highlighted_features** - Varyanta özel öne çıkan özellikler
- ✅ **Kendi use_cases** - Varyanta özel kullanım senaryoları (min 6)
- ✅ **Kendi competitive_advantages** - Varyanta özel rekabet avantajları (min 5)
- ✅ **Kendi target_industries** - Varyanta özel hedef sektörler (min 20)
- ✅ **Kendi FAQ'leri** (faq_data) - Varyanta özel sorular (min 10)
- ✅ **Kendi görseli** (featured_image, gallery)
- ✅ **Kendi SEO meta bilgileri**

**❌ YANLIŞ:** Master product'tan kopyala-yapıştır yaparak aynı içeriği kullanmak
**✅ DOĞRU:** Her varyant için yeni, o varyanta özel içerik üretmek

#### 2. Varyant Başlıkları

**❌ Yanlış:**
```json
{
  "title": "F4 201 Transpalet"
}
```

**✅ Doğru:**
```json
{
  "title": "F4 201 - 1.5 Ton Transpalet"
}
```

#### 3. Varyant Slug'ları

**Slug Formatı:** `[model]-[varyant-tipi]-[kategori]`

**Örnekler:**
```
f4-201-1-5-ton-transpalet
f4-201-2-ton-transpalet
f4-201-denge-tekerlekli-transpalet
f4-201-denge-tekerleksiz-transpalet
f5-301-elektrikli-forklift
f5-301-dizel-forklift
```

#### 4. variant_type Değerleri

**Kurallar:**
- Slug-friendly format (küçük harf, tire ile ayrılmış)
- Türkçe karakterler İngilizce karşılıklarına çevrilir (ş→s, ı→i, ç→c, ü→u, ö→o, ğ→g)
- Boşluklar tire (-) ile değiştirilir

**Örnekler:**
```json
"variant_type": "1-5-ton"
"variant_type": "2-ton"
"variant_type": "denge-tekerlekli"
"variant_type": "denge-tekerleksiz"
"variant_type": "elektrikli"
"variant_type": "dizel"
"variant_type": "lpg"
```

#### 5. Varyant İçerik Farklılıkları

**Her varyant için farklı olmalı:**

**long_description:**
```markdown
❌ Aynı metin: "F4 201 transpalet yüksek performanslıdır..."
✅ Farklı metin:
  - 1.5 ton: "1.5 ton kapasiteli F4 201, dar koridorlarda..."
  - 2 ton: "2 ton kapasiteli F4 201, daha yüksek yüklerde..."
  - Denge tekerlekli: "Denge tekeri sayesinde F4 201..."
```

**technical_specs:**
```json
❌ Aynı kapasite: {"capacity": {"value": 2000, "unit": "kg"}}
✅ Farklı kapasite:
  - 1.5 ton: {"capacity": {"value": 1500, "unit": "kg"}}
  - 2 ton: {"capacity": {"value": 2000, "unit": "kg"}}
```

**faq_data:**
```json
❌ Genel sorular: "Transpalet nasıl kullanılır?"
✅ Varyanta özel sorular:
  - 1.5 ton: "1.5 ton kapasite hangi işler için yeterlidir?"
  - Denge tekerlekli: "Denge tekeri ne işe yarar?"
  - Denge tekerleksiz: "Denge tekerleksiz kullanım avantajları nelerdir?"
```

### Varyant JSON Şablonu

**🇹🇷 SADECE TÜRKÇE - Varyant JSON Şablonu:**

**✅ DOĞRU YÖNTEM (KULLAN - SADECE TÜRKÇE):**
```json
{
  "title": "F4 201 Transpalet"
}
```

**❌ YANLIŞ YÖNTEM (KULLANMA):**
- Çoklu dil objesi kullanma
- İngilizce alan ekleme
- Dil kodu kullanma

---

**Standart Varyant Ürün (Ana varyant - parent_product_id: NULL):**
```json
{
  "parent_product_id": null,
  "is_master_product": false,
  "variant_type": "standart",
  "category_slug": "transpalet",
  "model_code": "F4-201",
  "sku": "F4-201-STD",
  "title": "F4 201 Li-Ion Akülü Transpalet",
  "slug": "f4-201-transpalet",
  "short_description": "2 ton kapasiteli, Li-Ion bataryalı, kompakt transpalet. Dar koridorlar ve standart palet taşıma için ideal.",
  "long_description": "<p>F4 201 Li-Ion Akülü Transpalet, 2 ton yük kapasitesi ile standart palet taşıma işlemleriniz için mükemmel bir çözümdür...</p>",
  "technical_specs": {
    "capacity": {"value": 2000, "unit": "kg"},
    "fork_dimensions": {
      "length": {"value": 1150, "unit": "mm"},
      "width": {"value": 560, "unit": "mm"}
    },
    "castor_wheels": false,
    "battery": {
      "type": "Li-Ion",
      "voltage": 48,
      "capacity": "24V/20Ah×2"
    }
  },
  "primary_specs": [
    {"label": "Yük Kapasitesi", "value": "2 Ton"},
    {"label": "Çatal Boyutu", "value": "1150×560 mm"},
    {"label": "Akü Sistemi", "value": "Li-Ion 48V"},
    {"label": "Denge Tekeri", "value": "Yok"}
  ],
  "faq_data": [
    {
      "question": "F4 201 standart versiyonu hangi işler için uygundur?",
      "answer": "Standart palet taşıma (1000×1200 mm), dar koridorlu depo operasyonları, e-ticaret fulfilment merkezleri için idealdir...",
      "sort_order": 1
    },
    {
      "question": "Denge tekeri olmadan güvenli midir?",
      "answer": "Evet, düz ve düzenli zeminlerde denge tekeri gerekmez. Standart versiyon 2 ton yükü güvenle taşır...",
      "sort_order": 2
    }
  ],
  "use_cases": [
    "Standart palet taşıma işlemleri (1000×1200 mm Euro palet)",
    "Dar koridorlu depo ve mağaza operasyonları",
    "E-ticaret fulfilment merkezlerinde yükleme-boşaltma",
    "Perakende mağaza arka depo stok yönetimi",
    "Hafif-orta tonajlı ürün transferi",
    "Günlük rutin palet hareketleri"
  ],
  "competitive_advantages": [
    "Li-Ion batarya sistemi - hızlı şarj, uzun ömür",
    "Kompakt boyut (400mm gövde uzunluğu) - dar alanlarda kullanım",
    "140 kg hafif ağırlık - kolay manevra",
    "48V güçlü sistem - yüksek performans",
    "Çıkarılabilir batarya - esneklik"
  ],
  "target_industries": [
    "E-ticaret ve Fulfilment Merkezleri",
    "Perakende Zincir Mağazalar",
    "Soğuk Hava Depoları",
    "Gıda ve İçecek Endüstrisi",
    "İlaç ve Medikal Lojistik",
    "Elektronik Ürün Depoları",
    "Tekstil ve Giyim Sektörü",
    "Mobilya Depoları",
    "Otomotiv Yan Sanayi",
    "Kimyasal Madde Depolama",
    "FMCG (Hızlı Tüketim Ürünleri)",
    "Lojistik ve Dağıtım Merkezleri",
    "Beyaz Eşya Depoları",
    "Tarım Ürünleri Depolama",
    "İnşaat Malzemesi Depoları",
    "Belediye Hizmetleri",
    "Enerji ve Altyapı Projeleri",
    "Liman ve Kargo Terminalleri",
    "Havaalanı Kargo Operasyonları",
    "Tüketim Ürünleri Perakendesi"
  ]
}
```

**Denge Tekerlekli Varyant (Child - parent_product_id: [Standart ID]):**
```json
{
  "parent_product_id": "[STANDART_PRODUCT_ID]",
  "is_master_product": false,
  "variant_type": "denge-tekerlekli",
  "category_slug": "transpalet",
  "model_code": "F4-201-DT",
  "sku": "F4-201-CASTOR",
  "title": "F4 201 Li-Ion Akülü Transpalet - Denge Tekerlekli",
  "slug": "f4-201-transpalet-denge-tekerlekli",
  "short_description": "Denge tekeri ile donatılmış 2 ton transpalet. Dengesiz zeminler ve ağır yükler için stabilite sağlar.",
  "long_description": "<p>Denge tekerlekli F4 201 Transpalet, dengesiz zeminlerde ve ağır yük taşımada stabilite sağlayan özel tasarımıyla öne çıkar...</p>",
  "technical_specs": {
    "capacity": {"value": 2000, "unit": "kg"},
    "fork_dimensions": {
      "length": {"value": 1150, "unit": "mm"},
      "width": {"value": 560, "unit": "mm"}
    },
    "castor_wheels": true,  ← ✅ FARKLI!
    "battery": {
      "type": "Li-Ion",
      "voltage": 48,
      "capacity": "24V/20Ah×2"
    }
  },
  "primary_specs": [
    {"label": "Yük Kapasitesi", "value": "2 Ton"},
    {"label": "Çatal Boyutu", "value": "1150×560 mm"},
    {"label": "Akü Sistemi", "value": "Li-Ion 48V"},
    {"label": "Denge Tekeri", "value": "VAR"}  ← ✅ FARKLI!
  ],
  "faq_data": [  ← ✅ TAMAMEN FARKLI SORULAR!
    {
      "question": "Denge tekeri ne işe yarar?",
      "answer": "Denge tekerleri, ağır yüklerde ve dengesiz zeminlerde transpaletin dengede kalmasını sağlar. Yükün düşme riskini azaltır ve operatör güvenliğini artırır...",
      "sort_order": 1
    },
    {
      "question": "Hangi zeminlerde denge tekeri gereklidir?",
      "answer": "Bozuk asfalt, rampalı alanlar, eğimli yüzeyler, inşaat sahaları gibi dengesiz zeminlerde denge tekeri kullanımı önerilir...",
      "sort_order": 2
    },
    {
      "question": "Denge tekerli versiyon daha ağır mı?",
      "answer": "Evet, yaklaşık 5-8 kg ağırlık farkı vardır. Ancak bu ağırlık stabilite için gereklidir...",
      "sort_order": 3
    }
  ],
  "use_cases": [  ← ✅ TAMAMEN FARKLI!
    "Dengesiz ve bozuk zeminlerde güvenli palet taşıma",
    "Rampalı alanlarda yükleme-boşaltma operasyonları",
    "İnşaat sahalarında malzeme transferi",
    "Açık alan depo operasyonları",
    "Ağır yük taşımada extra stabilite gereken işler",
    "Eğimli yüzeylerde palet hareketleri"
  ],
  "target_industries": [  ← ✅ SEKTÖRLER FARKLI!
    "İnşaat ve Altyapı Projeleri",
    "Açık Alan Depoları",
    "Liman ve Kargo Terminalleri",
    "İnşaat Malzemesi Tedarikçileri",
    "Ağır Sanayi Tesisleri",
    "Madencilik Lojistiği",
    "Taş Ocağı ve Kum Tesisleri",
    "Çimento ve Beton Üretimi",
    "Demir-Çelik Endüstrisi",
    "Büyük Ölçekli Üretim Tesisleri"
  ]
}
```

### Varyant Oluşturma Adımları (AI İçin)

#### Adım 1: Ana Ürün (Master) Gerekli Mi?

**Master Product OPSİYONEL.**

**Master oluştur eğer:**
- Tüm varyantları anlatan genel bir "overview" sayfası istiyorsanız
- Kullanıcıların önce ürün ailesini görmesini istiyorsanız

**Master oluşturma eğer:**
- Direkt varyantları göstermek istiyorsanız
- Varyantlar birbirinden çok farklıysa

#### Adım 2: Varyantları Belirle

**PDF'yi okuyup varyantları tespit et:**
```
F4-201 Transpalet PDF'i → Varyantlar:
  - 1.5 ton kapasite
  - 2 ton kapasite
  - Denge tekerlekli versiyon
  - Denge tekerleksiz versiyon
```

#### Adım 3: Her Varyant İçin Ayrı JSON Oluştur

**Dosya yapısı:**
```
json-extracts/
├── f4-201-master-transpalet.json        (opsiyonel)
├── f4-201-1-5-ton-transpalet.json       (zorunlu)
├── f4-201-2-ton-transpalet.json         (zorunlu)
├── f4-201-denge-tekerlekli.json         (zorunlu)
└── f4-201-denge-tekerleksiz.json        (zorunlu)
```

#### Adım 4: parent_product_id Bağlantısını Kur

**Seeder'da yapılır (JSON'da product_id belirtme):**
```php
// F4_201_Transpalet_Seeder.php

// 1. Master oluştur (opsiyonel)
$master = ShopProduct::create([
    'parent_product_id' => null,
    'is_master_product' => true,
    'variant_type' => null,
    'title' => ['tr' => 'F4 201 Transpalet Serisi', 'en' => '...'],
    // ...
]);

// 2. Varyantları oluştur
ShopProduct::create([
    'parent_product_id' => $master->product_id,  // Ana ürüne bağla
    'is_master_product' => false,
    'variant_type' => '1-5-ton',
    'title' => ['tr' => 'F4 201 - 1.5 Ton Transpalet', 'en' => '...'],
    // ...
]);

ShopProduct::create([
    'parent_product_id' => $master->product_id,  // Aynı ana ürüne bağla
    'is_master_product' => false,
    'variant_type' => '2-ton',
    'title' => ['tr' => 'F4 201 - 2 Ton Transpalet', 'en' => '...'],
    // ...
]);
```

### Varyant SEO Stratejisi

#### Her Varyant Ayrı SEO'ya Sahip

**1.5 Ton Varyant:**
```
Title: F4 201 - 1.5 Ton Transpalet | İXTİF
Meta Description: 1.5 ton kapasiteli F4 201 transpalet. Dar koridorlar için ideal...
Canonical URL: https://site.com/shop/f4-201-1-5-ton-transpalet
Schema.org: Product (capacity: 1500kg)
```

**2 Ton Varyant:**
```
Title: F4 201 - 2 Ton Transpalet | İXTİF
Meta Description: 2 ton kapasiteli F4 201 transpalet. Ağır yükler için güçlü...
Canonical URL: https://site.com/shop/f4-201-2-ton-transpalet
Schema.org: Product (capacity: 2000kg)
```

#### Schema.org Structured Data

**ProductGroup (Ana Ürün):**
```json
{
  "@context": "https://schema.org",
  "@type": "ProductGroup",
  "name": "F4 201 Transpalet Serisi",
  "hasVariant": [
    {
      "@type": "Product",
      "name": "F4 201 - 1.5 Ton Transpalet",
      "url": "https://site.com/shop/f4-201-1-5-ton-transpalet"
    },
    {
      "@type": "Product",
      "name": "F4 201 - 2 Ton Transpalet",
      "url": "https://site.com/shop/f4-201-2-ton-transpalet"
    }
  ]
}
```

### Varyant Landing Page Tasarımı

**Varyant sayfasında görünen bölümler:**

1. **Hero Section** → Varyanta özel başlık ve açıklama
2. **Varyant Switcher** → Diğer varyantlara geçiş kartları (resimli, tıklanabilir)
3. **Technical Specs** → Varyanta özel teknik özellikler
4. **FAQ** → Varyanta özel sorular
5. **Ana Ürüne Dön Linki** → (eğer master varsa)

**Varyant Switcher Örneği:**
```html
<section id="variants">
  <h2>Diğer Varyantlar</h2>
  <div class="variant-cards">
    <!-- 1.5 Ton Varyant Card -->
    <a href="/shop/f4-201-1-5-ton-transpalet">
      <img src="1.5-ton.jpg">
      <h3>F4 201 - 1.5 Ton</h3>
      <p>Dar koridorlar için ideal</p>
      <span class="variant-tag">1-5-ton</span>
    </a>

    <!-- 2 Ton Varyant Card -->
    <a href="/shop/f4-201-2-ton-transpalet">
      <img src="2-ton.jpg">
      <h3>F4 201 - 2 Ton</h3>
      <p>Ağır yükler için güçlü</p>
      <span class="variant-tag">2-ton</span>
    </a>
  </div>
</section>
```

### Varyant Kullanım Senaryoları

#### Senaryo 1: Kapasite Varyantları (Transpalet, Forklift)

**Varyantlar:**
- 1.5 ton
- 2 ton
- 2.5 ton

**Farklılıklar:**
- Kapasite değerleri (technical_specs)
- Kullanım senaryoları (use_cases)
- SSS soruları ("1.5 ton yeterli mi?", "2 ton ile 2.5 ton farkı?")

#### Senaryo 2: Özellik Varyantları (Denge Tekeri, Kabin)

**Varyantlar:**
- Denge tekerlekli
- Denge tekerleksiz
- Kapalı kabin
- Açık kabin

**Farklılıklar:**
- Özellik açıklamaları (long_description)
- Teknik detaylar (technical_specs → "wheels" section)
- SSS ("Denge tekeri nedir?", "Kapalı kabin avantajları?")

#### Senaryo 3: Yakıt Tipi Varyantları (Forklift)

**Varyantlar:**
- Elektrikli
- Dizel
- LPG

**Farklılıklar:**
- Motor özellikleri (technical_specs → "engine" section)
- Kullanım alanları (use_cases → kapalı/açık alan)
- Çevre bilgisi (competitive_advantages → emisyon, gürültü)

### Varyant TODO Listesi Eklentisi

**Her varyant için TODO'ya ekle:**

```markdown
### VARYANT BİLGİLERİ
- [ ] Bu ürün bir varyant mı? (Evet/Hayır)
- [ ] **EVET ise:**
  - [ ] Ana ürün (master) oluşturuldu mu? (Evet/Hayır/Opsiyonel)
  - [ ] Tüm varyantlar belirlendi (kaç adet: ____)
  - [ ] Her varyant için ayrı JSON dosyası oluşturuldu
  - [ ] Varyant başlıkları FARKLI ve açıklayıcı
  - [ ] Varyant slug'ları benzersiz
  - [ ] variant_type değerleri slug-friendly
  - [ ] parent_product_id ilişkisi kurulacak (seeder'da)
  - [ ] Her varyantın içeriği FARKLI:
    - [ ] long_description farklı
    - [ ] technical_specs farklı (varyanta özel değerler)
    - [ ] faq_data farklı (varyanta özel sorular)
    - [ ] use_cases varyanta göre uyarlandı
  - [ ] Varyant görselleri hazırlandı (her varyant için featured_image)

### VARYANT DETAYLARI
- **Ana Ürün (Master):** [Adı] → [JSON dosya adı]
- **Varyant 1:** [Adı] → [JSON dosya adı] → variant_type: [slug]
- **Varyant 2:** [Adı] → [JSON dosya adı] → variant_type: [slug]
- **Varyant 3:** [Adı] → [JSON dosya adı] → variant_type: [slug]
```

### Varyant Hızlı Kontrol Listesi

```markdown
✅ Varyant Oluşturma Kontrol:
- [ ] Her varyantın ayrı JSON dosyası var
- [ ] Başlıklar varyant tipini içeriyor (örn: "F4 201 - 1.5 Ton")
- [ ] Slug'lar benzersiz (örn: f4-201-1-5-ton-transpalet)
- [ ] variant_type slug-friendly (örn: 1-5-ton)
- [ ] İçerikler FARKLI (aynı metin değil!)
- [ ] Teknik özellikler varyanta göre farklı
- [ ] SSS'ler varyanta özel sorular içeriyor
- [ ] Her varyantın kendi görseli var
- [ ] parent_product_id ilişkisi planlandı (seeder için)
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
  "use_cases": [
    "Dar koridorlarda palet taşıma",
    "Depo içi yükleme-boşaltma",
    "... (toplam 6+ senaryo)"
  ],
  "faq_data": [
    {
      "question": "Li-Ion akü avantajları nelerdir?",
      "answer": "Detaylı Türkçe cevap...",
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
  "use_cases": ["Senaryo 1", "Senaryo 2"], // ❌ 6'dan az!
  "faq_data": [ // ❌ 5 soru var, 10 olmalı!
    {"question": "...", "answer": "..."}
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

### 1️⃣2️⃣ WARRANTY INFO (Garanti Bilgisi) - PDF KLASÖRÜne GÖRE
- [ ] Garanti bilgisi eklendi
- [ ] **Forklift klasörü** (`/EP PDF/1-Forklift/`): `{"tr": "2 Yıl Ürün Garantisi | 5 Yıl Akü Garantisi"}`
- [ ] **Tüm Diğer Klasörler** (Transpalet, İstif, vs.): `{"tr": "1 Yıl Ürün Garantisi | 2 Yıl Akü Garantisi"}`
- [ ] **NOT:** Garanti PDF klasör adına göre otomatik belirlenecek!

### 1️⃣3️⃣ ATTRIBUTES (Filtrelenebilir Özellikler)
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

---

## 📦 SEEDER STRATEJİSİ (HER VARYANT İÇİN AYRI SEEDER)

### ✅ Önerilen Yaklaşım: Her Ana Varyant İçin Ayrı Seeder

**Neden?**
- Her varyantın içeriği TAMAMEN farklı (use_cases, target_industries, faq_data)
- Kolay yönetim ve güncelleme
- Bağımsız test edilebilir
- Git history takibi kolay

### F4 201 İçin Seeder Yapısı

```
Modules/Shop/database/seeders/
├── F4_201/
│   ├── F4_201_Standart_Seeder.php          ← Ana varyant (parent_product_id: NULL)
│   ├── F4_201_Denge_Tekerlekli_Seeder.php  ← Child (parent: Standart)
│   ├── F4_201_Genis_Catal_Seeder.php       ← Child (parent: Standart)
│   ├── F4_201_Uzun_Catal_Seeder.php        ← Child (parent: Standart)
│   └── F4_201_Yuksek_Batarya_Seeder.php    ← Child (parent: Standart)
└── ShopSeeder.php                           ← Master seeder (hepsini çağırır)
```

### Seeder İçeriği Örneği

**F4_201_Standart_Seeder.php:**
```php
<?php

namespace Modules\Shop\Database\Seeders\F4_201;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;

class F4_201_Standart_Seeder extends Seeder
{
    public function run(): void
    {
        $category = ShopCategory::where('slug', 'transpalet')->first();

        ShopProduct::create([
            'category_id' => $category->category_id,
            'parent_product_id' => null,  // ANA VARYANT
            'is_master_product' => false,
            'variant_type' => 'standart',
            'sku' => 'F4-201-STD',
            'title' => 'F4 201 Li-Ion Akülü Transpalet',
            'slug' => 'f4-201-transpalet',
            'short_description' => '2 ton kapasiteli, Li-Ion bataryalı...',
            'long_description' => '<p>Standart kullanım için...</p>',
            'technical_specs' => [
                'capacity' => ['value' => 2000, 'unit' => 'kg'],
                'fork_dimensions' => [
                    'length' => ['value' => 1150, 'unit' => 'mm'],
                    'width' => ['value' => 560, 'unit' => 'mm'],
                ],
                'castor_wheels' => false,
                'battery' => [
                    'type' => 'Li-Ion',
                    'voltage' => 48,
                    'capacity' => '24V/20Ah×2',
                ],
            ],
            'primary_specs' => [
                ['label' => 'Yük Kapasitesi', 'value' => '2 Ton'],
                ['label' => 'Çatal Boyutu', 'value' => '1150×560 mm'],
                ['label' => 'Akü Sistemi', 'value' => 'Li-Ion 48V'],
                ['label' => 'Denge Tekeri', 'value' => 'Yok'],
            ],
            'use_cases' => [
                'Standart palet taşıma işlemleri',
                'Dar koridorlu depo operasyonları',
                'E-ticaret fulfilment merkezleri',
                'Perakende mağaza arka depoları',
                'Hafif-orta tonajlı ürün transferi',
                'Günlük rutin palet hareketleri',
            ],
            'competitive_advantages' => [
                'Li-Ion batarya - hızlı şarj',
                'Kompakt boyut - 400mm gövde',
                '140 kg hafif',
                '48V güçlü sistem',
                'Çıkarılabilir batarya',
            ],
            'target_industries' => [
                'E-ticaret ve Fulfilment',
                'Perakende Mağazalar',
                'Soğuk Hava Depoları',
                'Gıda Endüstrisi',
                // ... 20+ sektör
            ],
            'faq_data' => [
                [
                    'question' => 'Standart versiyon hangi işler için uygundur?',
                    'answer' => 'Standart palet taşıma, dar koridor...',
                    'sort_order' => 1,
                ],
                // ... 10+ soru
            ],
            'is_active' => true,
            'published_at' => now(),
        ]);
    }
}
```

**F4_201_Denge_Tekerlekli_Seeder.php:**
```php
<?php

namespace Modules\Shop\Database\Seeders\F4_201;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;

class F4_201_Denge_Tekerlekli_Seeder extends Seeder
{
    public function run(): void
    {
        $category = ShopCategory::where('slug', 'transpalet')->first();
        
        // Ana varyantı bul (standart)
        $parentProduct = ShopProduct::where('sku', 'F4-201-STD')->first();

        ShopProduct::create([
            'category_id' => $category->category_id,
            'parent_product_id' => $parentProduct->product_id,  // 👈 Standart'a bağlı
            'is_master_product' => false,
            'variant_type' => 'denge-tekerlekli',
            'sku' => 'F4-201-CASTOR',
            'title' => 'F4 201 Li-Ion Akülü Transpalet - Denge Tekerlekli',
            'slug' => 'f4-201-transpalet-denge-tekerlekli',
            'short_description' => 'Denge tekeri ile donatılmış 2 ton...',
            'long_description' => '<p>Dengesiz zeminler için...</p>',
            'technical_specs' => [
                'capacity' => ['value' => 2000, 'unit' => 'kg'],
                'fork_dimensions' => [
                    'length' => ['value' => 1150, 'unit' => 'mm'],
                    'width' => ['value' => 560, 'unit' => 'mm'],
                ],
                'castor_wheels' => true,  // 👈 FARKLI!
                'battery' => [
                    'type' => 'Li-Ion',
                    'voltage' => 48,
                    'capacity' => '24V/20Ah×2',
                ],
            ],
            'primary_specs' => [
                ['label' => 'Yük Kapasitesi', 'value' => '2 Ton'],
                ['label' => 'Çatal Boyutu', 'value' => '1150×560 mm'],
                ['label' => 'Akü Sistemi', 'value' => 'Li-Ion 48V'],
                ['label' => 'Denge Tekeri', 'value' => 'VAR'],  // 👈 FARKLI!
            ],
            'use_cases' => [  // 👈 TAMAMEN FARKLI!
                'Dengesiz zeminlerde güvenli taşıma',
                'Rampalı alanlarda operasyonlar',
                'İnşaat sahalarında malzeme transferi',
                'Açık alan depo operasyonları',
                'Ağır yük stabilite',
                'Eğimli yüzeylerde kullanım',
            ],
            'target_industries' => [  // 👈 SEKTÖRLER FARKLI!
                'İnşaat ve Altyapı',
                'Açık Alan Depoları',
                'Liman ve Kargo',
                'İnşaat Malzemesi',
                'Ağır Sanayi',
                // ... 20+ sektör
            ],
            'faq_data' => [  // 👈 SORULAR FARKLI!
                [
                    'question' => 'Denge tekeri ne işe yarar?',
                    'answer' => 'Dengesiz zeminlerde stabilite...',
                    'sort_order' => 1,
                ],
                [
                    'question' => 'Hangi zeminlerde gereklidir?',
                    'answer' => 'Bozuk asfalt, rampalı...',
                    'sort_order' => 2,
                ],
                // ... 10+ soru
            ],
            'is_active' => true,
            'published_at' => now(),
        ]);
    }
}
```

### ShopSeeder.php (Master Seeder)

```php
<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ShopCategorySeeder::class,
            ShopAttributeSeeder::class,
            ShopBrandSeeder::class,
            
            // F4 201 Transpalet Serisi
            \Modules\Shop\Database\Seeders\F4_201\F4_201_Standart_Seeder::class,
            \Modules\Shop\Database\Seeders\F4_201\F4_201_Denge_Tekerlekli_Seeder::class,
            \Modules\Shop\Database\Seeders\F4_201\F4_201_Genis_Catal_Seeder::class,
            \Modules\Shop\Database\Seeders\F4_201\F4_201_Uzun_Catal_Seeder::class,
            \Modules\Shop\Database\Seeders\F4_201\F4_201_Yuksek_Batarya_Seeder::class,
            
            // F4 202 Serisi (gelecekte)
            // ...
        ]);
    }
}
```

### Test Komutları

```bash
# Tüm shop seeder'ları
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\ShopSeeder

# Sadece F4 201 standart
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\F4_201\\F4_201_Standart_Seeder

# Sadece F4 201 denge tekerlekli
php artisan db:seed --class=Modules\\Shop\\Database\\Seeders\\F4_201\\F4_201_Denge_Tekerlekli_Seeder
```

---

## 🎯 ÖZET: VARYANT SİSTEMİ KURALLARI

### 🇹🇷 SADECE TÜRKÇE
- ❌ Çoklu dil objesi kullanma
- ✅ Direkt Türkçe string kullan
- ✅ Çeviri yapma, her şey Türkçe

### 📦 HER VARYANT = AYRI ÜRÜN
- ✅ Ayrı title, slug, URL
- ✅ Tamamen farklı içerik (use_cases, faq, industries)
- ✅ Ayrı technical_specs
- ✅ Ayrı SEO meta bilgileri

### 🗂️ HER VARYANT = AYRI SEEDER
- ✅ F4_201_Standart_Seeder.php → parent_product_id: NULL
- ✅ F4_201_Denge_Tekerlekli_Seeder.php → parent_product_id: [Standart ID]
- ✅ Her seeder bağımsız çalışabilir

### 🔗 PARENT-CHILD İLİŞKİSİ
- ✅ Ana varyant: parent_product_id = NULL
- ✅ Diğer varyantlar: parent_product_id = [Ana varyant ID]
- ✅ is_master_product = false (hepsi direkt satılan ürün)

### 📝 İÇERİK FARKLILIKLARI
- ✅ Use cases: Her varyant için farklı senaryolar
- ✅ Target industries: Her varyant için farklı sektörler
- ✅ FAQ: Her varyant için özel sorular
- ✅ Technical specs: Varyanta özel değerler

### 🎨 LANDING PAGE
- ✅ Her varyantın kendi sayfası
- ✅ Varyant switcher: Diğer varyantlara tıklanabilir kartlar
- ✅ Parent product link: Ana ürüne dönüş (varsa)

**🎉 Artık hazırsın! PDF'leri işle ve varyant seeder'ları oluştur!**

