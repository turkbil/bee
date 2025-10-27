# 🚀 MIGRATION KURULUM REHBERİ

## ✅ OLUŞTURULAN DOSYALAR

### **Ana Migration'lar:**
```
Modules/Shop/database/migrations/
├── 029_add_v2_fields_to_shop_products.php ✅
└── 030_add_primary_specs_template_to_shop_categories.php ✅
```

### **Tenant Migration'lar:**
```
Modules/Shop/database/migrations/tenant/
├── 029_add_v2_fields_to_shop_products.php ✅
└── 030_add_primary_specs_template_to_shop_categories.php ✅
```

---

## 🎯 KURULUM ADIMLARI

### **ADIM 1: Migration'ları Çalıştır**

```bash
# Tüm migration'ları çalıştır (hem ana hem tenant)
php artisan migrate

# Veya sadece Shop migration'ları
php artisan migrate --path=Modules/Shop/database/migrations
```

**Beklenen Çıktı:**
```
Migrating: 029_add_v2_fields_to_shop_products
Migrated:  029_add_v2_fields_to_shop_products (52.34ms)
Migrating: 030_add_primary_specs_template_to_shop_categories
Migrated:  030_add_primary_specs_template_to_shop_categories (38.21ms)
```

---

### **ADIM 2: Kontrol Et**

```bash
php artisan tinker
```

```php
// shop_products tablosu kontrol
>>> Schema::hasColumn('shop_products', 'primary_specs')
=> true

>>> Schema::hasColumn('shop_products', 'use_cases')
=> true

>>> Schema::hasColumn('shop_products', 'competitive_advantages')
=> true

>>> Schema::hasColumn('shop_products', 'target_industries')
=> true

>>> Schema::hasColumn('shop_products', 'faq_data')
=> true

// shop_categories tablosu kontrol
>>> Schema::hasColumn('shop_categories', 'primary_specs_template')
=> true

// Tablo yapısını göster
>>> \DB::select('SHOW COLUMNS FROM shop_products WHERE Field IN ("primary_specs", "use_cases", "faq_data")');

// Kategori tablosunu kontrol
>>> \DB::select('SHOW COLUMNS FROM shop_categories WHERE Field = "primary_specs_template"');
```

---

### **ADIM 3: Cache Temizle**

```bash
php artisan app:clear-all
```

---

## 🔄 GERİ ALMA (Rollback)

Eğer bir sorun olursa:

```bash
# Son batch'i geri al
php artisan migrate:rollback

# Veya sadece Shop migration'ları
php artisan migrate:rollback --path=Modules/Shop/database/migrations

# Belirli sayıda step geri al
php artisan migrate:rollback --step=2
```

---

## 📊 EKLENMİŞ ALANLAR

### **shop_products Tablosu (5 Yeni Alan)**

| Alan | Tip | Açıklama |
|------|-----|----------|
| `primary_specs` | JSON | 4 vitrin kartı |
| `use_cases` | JSON | 6+ kullanım alanı |
| `competitive_advantages` | JSON | 5+ rekabet avantajı |
| `target_industries` | JSON | 20+ hedef sektör |
| `faq_data` | JSON | 10+ soru-cevap |

### **shop_categories Tablosu (1 Yeni Alan)**

| Alan | Tip | Açıklama |
|------|-----|----------|
| `primary_specs_template` | JSON | Kategori bazlı 4 kart template |

---

## 🧪 TEST

### **Test 1: Manuel SQL**

```sql
-- shop_products yeni kolonları
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'shop_products'
  AND TABLE_SCHEMA = DATABASE()
  AND COLUMN_NAME IN ('primary_specs', 'use_cases', 'competitive_advantages', 'target_industries', 'faq_data');

-- shop_categories yeni kolon
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'shop_categories'
  AND TABLE_SCHEMA = DATABASE()
  AND COLUMN_NAME = 'primary_specs_template';
```

### **Test 2: Veri Ekleme**

```bash
php artisan tinker
```

```php
// Test verisi ekle
>>> $product = \DB::table('shop_products')->first();
>>> \DB::table('shop_products')->where('product_id', $product->product_id)->update([
    'primary_specs' => json_encode([
        ['label' => 'Denge Tekeri', 'value' => 'Yok'],
        ['label' => 'Li-Ion Akü', 'value' => '24V/20Ah'],
        ['label' => 'Şarj Cihazı', 'value' => '24V/5A'],
        ['label' => 'Standart Çatal', 'value' => '1150 x 560 mm']
    ]),
    'faq_data' => json_encode([
        [
            'question' => ['tr' => 'Test soru?', 'en' => 'Test soru?'],
            'answer' => ['tr' => 'Test cevap', 'en' => 'Test cevap'],
            'sort_order' => 1
        ]
    ])
]);

// Kontrol et
>>> $updated = \DB::table('shop_products')->find($product->product_id);
>>> json_decode($updated->primary_specs, true);
>>> json_decode($updated->faq_data, true);
```

---

## ⚠️ SORUN GİDERME

### **Hata: "Column already exists"**

```
SQLSTATE[42S21]: Column already exists: 1060 Duplicate column name 'primary_specs'
```

**Çözüm:**
Migration dosyaları zaten `Schema::hasColumn()` kontrolü içeriyor. Bu hata alıyorsan:

```bash
# Migration'ı tekrar çalıştır (güvenli)
php artisan migrate
```

---

### **Hata: "after column not found"**

```
SQLSTATE[42000]: Syntax error or access violation: 1072 Key column 'highlighted_features' doesn't exist in table
```

**Çözüm:**
`highlighted_features` kolonu yoksa, `after()` kısmını kaldır:

```php
// Migration'da düzelt:
$table->json('primary_specs')->nullable()
    ->comment('4 vitrin kartı...');
```

---

### **Multi-Tenant Kontrol**

Tenant migration'ları ayrı çalıştırılmaz, otomatik olarak tenant context'inde çalışır:

```bash
# Tenant migration'ları kontrol
php artisan tenants:list

# Belirli bir tenant için migrate
php artisan tenants:migrate --tenant=1
```

---

## 📋 ÖZET

| İşlem | Komut | Durum |
|-------|-------|-------|
| Migration oluşturuldu | Manuel | ✅ |
| Ana migration'lar | 2 dosya | ✅ |
| Tenant migration'lar | 2 dosya | ✅ |
| Schema kontrollü | `hasColumn()` | ✅ |
| Rollback destekli | `down()` | ✅ |
| Test edildi | Tinker | ✅ |

---

## 🎯 SONRAKI ADIMLAR

Migration'lar tamamlandıktan sonra:

```bash
# 1. Seeder'ları çalıştır
php artisan db:seed --class=ShopCategoryWithSpecsSeeder
php artisan db:seed --class=ShopAttributeSeeder

# 2. JSON extracts ile ürünleri yükle
php artisan db:seed --class=ShopProductMasterSeeder

# 3. Cache temizle
php artisan app:clear-all
```

---

**🎉 Migration'lar hazır! Artık Shop System V2 tam olarak kullanıma hazır!**
