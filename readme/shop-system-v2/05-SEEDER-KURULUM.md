# 🌱 SEEDER KURULUM VE ÇALIŞTIRMA

## 🎯 KURULUM SIRASI

```bash
# 1. Kategorileri oluştur (spec template'leriyle birlikte)
php artisan db:seed --class=ShopCategoryWithSpecsSeeder

# 2. Attribute'ları oluştur
php artisan db:seed --class=ShopAttributeSeeder

# 3. Tüm ürünleri yükle (JSON'lardan)
php artisan db:seed --class=ShopProductMasterSeeder

# 4. Cache temizle
php artisan app:clear-all
```

---

## 📊 SEEDER DOSYALARI

### 1️⃣ **ShopCategoryWithSpecsSeeder**

**Konum:** `/database/seeders/ShopCategoryWithSpecsSeeder.php`

**Ne Yapar?**
- 6 ana kategori oluşturur
- Her kategori için `primary_specs_template` tanımlar
- SEO ayarlarını ekler

**Kategoriler:**
1. Forklift
2. Transpalet
3. İstif Makinesi
4. Sipariş Toplama Makinesi
5. Otonom Sistemler
6. Reach Truck

---

### 2️⃣ **ShopAttributeSeeder**

**Konum:** `/database/seeders/ShopAttributeSeeder.php`

**Ne Yapar?**
- 7 ortak attribute tanımlar
- Filtreleme için gerekli alanları doldurur

**Attribute'lar:**
1. Yük Kapasitesi (kg) - range
2. Voltaj (V) - select
3. Batarya Tipi - select
4. Asansör Yüksekliği (mm) - range
5. Servis Ağırlığı (kg) - range
6. Çatal Uzunluğu (mm) - select
7. Ürün Durumu - select

---

### 3️⃣ **ShopProductMasterSeeder**

**Konum:** `/database/seeders/ShopProductMasterSeeder.php`

**Ne Yapar?**
- `readme/shop-system-v2/json-extracts/` klasöründeki TÜM JSON'ları tarar
- Her ürün için:
  - Ana ürün kaydı (`shop_products`)
  - Varyantlar (`shop_product_variants`)
  - Attribute bağlantıları (`shop_product_attributes`)
- Kategori ID'lerini dinamik çöz ümler
- Mevcut ürünleri günceller (SKU bazlı)

**İstatistik Gösterir:**
```
📊 İSTATİSTİKLER:
   📄 Toplam JSON dosyası: 25
   ✅ Oluşturulan ürün: 25
   🔧 Oluşturulan varyant: 48
   🏷️  Bağlanan attribute: 125
```

---

## 🔄 YENİDEN YÜKLEME

### **Tüm Shop Verilerini Sıfırla:**

```bash
# Migration'ları geri al
php artisan migrate:rollback --path=Modules/Shop/database/migrations

# Tekrar oluştur
php artisan migrate --path=Modules/Shop/database/migrations

# Seeder'ları çalıştır
php artisan db:seed --class=ShopCategoryWithSpecsSeeder
php artisan db:seed --class=ShopAttributeSeeder
php artisan db:seed --class=ShopProductMasterSeeder
```

### **Sadece Ürünleri Yeniden Yükle:**

```bash
# Ürün tablolarını temizle
php artisan db:seed --class=ShopProductMasterSeeder --force

# veya Manuel:
# TRUNCATE shop_products, shop_product_variants, shop_product_attributes;
# php artisan db:seed --class=ShopProductMasterSeeder
```

---

## 🧪 TEST

```bash
# Tinker ile kontrol
php artisan tinker

>>> \DB::table('shop_products')->count()
=> 25

>>> \DB::table('shop_product_variants')->count()
=> 48

>>> \DB::table('shop_product_attributes')->count()
=> 125

>>> \DB::table('shop_categories')->count()
=> 6

>>> \DB::table('shop_attributes')->count()
=> 7

# Bir ürünü çek ve kontrol et
>>> $product = \DB::table('shop_products')->where('sku', 'F4-201')->first();
>>> json_decode($product->faq_data, true);
=> [ ... 10 FAQ ... ]

>>> json_decode($product->primary_specs, true);
=> [ ... 4 kart ... ]
```

---

## 📁 JSON EXTRACTS KLASÖRÜBaşarısız

**Konum:** `readme/shop-system-v2/json-extracts/`

**Yapı:**
```
json-extracts/
├── f4-201-transpalet.json
├── f4-202-transpalet.json
├── es12-istif-makinesi.json
├── jx1-order-picker.json
└── ...
```

**Dosya İsimlendirme:**
```
{sku}-{slug}.json

Örnek:
F4-201 → f4-201-transpalet.json
ES12 → es12-istif-makinesi.json
```

---

## ⚠️ SORUN GİDERME

### **Kategori Bulunamadı Hatası**

```
❌ Hata: Kategori bulunamadı
```

**Çözüm:**
```bash
# 1. ShopCategoryMapper cache'ini temizle
php artisan tinker
>>> App\Helpers\ShopCategoryMapper::clearCache();

# 2. Kategori seeder'ını tekrar çalıştır
php artisan db:seed --class=ShopCategoryWithSpecsSeeder
```

---

### **Duplicate SKU Hatası**

```
❌ Integrity constraint violation: Duplicate entry 'F4-201' for key 'sku'
```

**Çözüm:**
SKU zaten var. Seeder otomatik UPDATE yapmalı. Kod kontrol et:
```php
$existingProduct = DB::table('shop_products')->where('sku', $sku)->first();
if ($existingProduct) {
    // UPDATE
} else {
    // INSERT
}
```

---

### **JSON Parse Hatası**

```
❌ JSON parse hatası: f4-201-transpalet.json
```

**Çözüm:**
```bash
# JSON dosyasını validate et
cat readme/shop-system-v2/json-extracts/f4-201-transpalet.json | jq .

# Syntax hatası varsa AI'ya tekrar gönder
```

---

## 🚀 OTOMATİK BATCH İŞLEME (İsteğe Bağlı)

```bash
# Tüm JSON'ları toplu işle
php artisan shop:batch-import --path=readme/shop-system-v2/json-extracts

# Sadece yeni eklenen JSON'ları işle
php artisan shop:batch-import --only-new

# Dry run (test modu)
php artisan shop:batch-import --dry-run
```

---

**LANDING PAGE YAPISINI HAZIRLIYORUM...**
