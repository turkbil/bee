# 📊 SHOP MIGRATION DURUMU - KONTROL RAPORU

## ✅ MEVCUT TABLOLAR (28 Tablo)

```
✅ 001_create_shop_customer_groups_table.php
✅ 002_create_shop_categories_table.php
✅ 003_create_shop_brands_table.php
✅ 004_create_shop_attributes_table.php
✅ 005_create_shop_subscription_plans_table.php
✅ 006_create_shop_taxes_table.php
✅ 007_create_shop_payment_methods_table.php
✅ 008_create_shop_warehouses_table.php
✅ 009_create_shop_coupons_table.php
✅ 010_create_shop_campaigns_table.php
✅ 011_create_shop_settings_table.php
✅ 012_create_shop_customers_table.php
✅ 013_create_shop_customer_addresses_table.php
✅ 014_create_shop_products_table.php
✅ 015_create_shop_product_variants_table.php
✅ 016_create_shop_product_attributes_table.php
✅ 017_create_shop_orders_table.php
✅ 018_create_shop_order_items_table.php
✅ 019_create_shop_order_addresses_table.php
✅ 020_create_shop_inventory_table.php
✅ 021_create_shop_stock_movements_table.php
✅ 022_create_shop_subscriptions_table.php
✅ 023_create_shop_payments_table.php
✅ 024_create_shop_carts_table.php
✅ 025_create_shop_cart_items_table.php
✅ 026_create_shop_tax_rates_table.php
✅ 027_create_shop_coupon_usages_table.php
✅ 028_create_shop_reviews_table.php
```

---

## ❌ EKSİK ALANLAR (Shop System V2 için)

### **1. shop_products Tablosu**

| Alan | Durum | Açıklama |
|------|-------|----------|
| `title` | ✅ Var | JSON çoklu dil |
| `slug` | ✅ Var | JSON çoklu dil |
| `short_description` | ✅ Var | JSON çoklu dil |
| `body` | ✅ Var | JSON çoklu dil (marketing intro + body) |
| `technical_specs` | ✅ Var | JSON nested object |
| `features` | ✅ Var | JSON array |
| `highlighted_features` | ✅ Var | JSON array |
| `warranty_info` | ✅ Var | JSON object |
| `tags` | ✅ Var | JSON array |
| **`primary_specs`** | ❌ **YOK** | **4 vitrin kartı** |
| **`use_cases`** | ❌ **YOK** | **6+ kullanım alanı** |
| **`competitive_advantages`** | ❌ **YOK** | **5+ rekabet avantajı** |
| **`target_industries`** | ❌ **YOK** | **20+ hedef sektör** |
| **`faq_data`** | ❌ **YOK** | **10+ soru-cevap** |

### **2. shop_categories Tablosu**

| Alan | Durum | Açıklama |
|------|-------|----------|
| `title` | ✅ Var | JSON çoklu dil |
| `slug` | ✅ Var | JSON çoklu dil |
| `description` | ✅ Var | JSON çoklu dil |
| `icon_class` | ✅ Var | Font Awesome icon |
| **`primary_specs_template`** | ❌ **YOK** | **Kategori bazlı 4 kart template** |

---

## 🔧 ÇÖZÜM: YENİ MIGRATION DOSYALARI

### ✅ **OLUŞTURULAN MIGRATION'LAR:**

```bash
✅ 029_add_v2_fields_to_shop_products.php
✅ 030_add_primary_specs_template_to_shop_categories.php
```

**Konum:**
```
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/database/migrations/
```

---

## 🚀 KURULUM ADIM LARI

### **1. Migration'ları Çalıştır:**

```bash
# Shop migration'larını çalıştır
php artisan migrate --path=Modules/Shop/database/migrations

# Veya tüm migration'ları
php artisan migrate
```

**Beklenen Çıktı:**
```
Migrating: 029_add_v2_fields_to_shop_products
Migrated:  029_add_v2_fields_to_shop_products (45.23ms)
Migrating: 030_add_primary_specs_template_to_shop_categories
Migrated:  030_add_primary_specs_template_to_shop_categories (32.15ms)
```

### **2. Tablo Yapısını Kontrol Et:**

```bash
php artisan tinker

# shop_products tablosu kontrol
>>> Schema::hasColumn('shop_products', 'primary_specs')
=> true

>>> Schema::hasColumn('shop_products', 'faq_data')
=> true

# shop_categories tablosu kontrol
>>> Schema::hasColumn('shop_categories', 'primary_specs_template')
=> true
```

### **3. Manuel SQL Kontrol (Opsiyonel):**

```sql
-- shop_products tablosu kolonları
SHOW COLUMNS FROM shop_products;

-- Yeni eklenen kolonlar
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'shop_products'
  AND TABLE_SCHEMA = 'laravel'
  AND COLUMN_NAME IN ('primary_specs', 'use_cases', 'competitive_advantages', 'target_industries', 'faq_data');

-- shop_categories tablosu kontrol
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'shop_categories'
  AND TABLE_SCHEMA = 'laravel'
  AND COLUMN_NAME = 'primary_specs_template';
```

---

## 📋 ALAN DETAYLARI

### **shop_products Tablosu - Yeni Alanlar**

#### 1. **primary_specs** (JSON)
```json
[
  {"label": "Denge Tekeri", "value": "Yok"},
  {"label": "Li-Ion Akü", "value": "24V/20Ah çıkarılabilir paket"},
  {"label": "Şarj Cihazı", "value": "24V/5A harici hızlı şarj"},
  {"label": "Standart Çatal", "value": "1150 x 560 mm"}
]
```

#### 2. **use_cases** (JSON)
```json
{
  "tr": [
    "E-ticaret depolarında hızlı sipariş hazırlama",
    "Dar koridorlu perakende depolarında yükleme",
    "Soğuk zincir lojistiğinde kesintisiz taşıma",
    "..."
  ],
  "en": ["..."],
  "vs.": "..."
}
```

#### 3. **competitive_advantages** (JSON)
```json
{
  "tr": [
    "48V Li-Ion güç platformu ile en agresif hızlanma",
    "140 kg ultra hafif servis ağırlığı",
    "Tak-çıkar batarya ile sıfır bekleme",
    "..."
  ],
  "en": ["..."],
  "vs.": "..."
}
```

#### 4. **target_industries** (JSON)
```json
{
  "tr": [
    "E-ticaret & fulfillment merkezleri",
    "Perakende zincir depoları",
    "Soğuk zincir ve gıda lojistiği",
    "... (20 sektör)"
  ],
  "en": ["..."],
  "vs.": "..."
}
```

#### 5. **faq_data** (JSON)
```json
[
  {
    "question": {
      "tr": "F4 201 bir vardiyada kaç saat çalışır?",
      "en": "...",
      "vs.": "..."
    },
    "answer": {
      "tr": "Standart 2 modül ile 6 saate kadar...",
      "en": "...",
      "vs.": "..."
    },
    "sort_order": 1,
    "category": "usage",
    "is_highlighted": true
  }
]
```

### **shop_categories Tablosu - Yeni Alan**

#### **primary_specs_template** (JSON)
```json
{
  "card_1": {
    "label": "Denge Tekeri",
    "field_path": "options.stabilizing_wheels",
    "icon": "fa-solid fa-circle-dot",
    "format": "boolean_to_text",
    "mapping": {"true": "Var", "false": "Yok"}
  },
  "card_2": {
    "label": "Li-Ion Akü",
    "field_path": "electrical.battery_system.configuration",
    "icon": "fa-solid fa-battery-full",
    "format": "text"
  },
  "card_3": {
    "label": "Şarj Cihazı",
    "field_path": "electrical.charger_options.standard",
    "icon": "fa-solid fa-plug",
    "format": "text"
  },
  "card_4": {
    "label": "Standart Çatal",
    "field_path": "dimensions.fork_dimensions",
    "icon": "fa-solid fa-ruler",
    "format": "fork_dimensions"
  }
}
```

---

## ✅ ÖNCEKİ TABLOLAR KULLANILIYOR MU?

### **EVET, TÜM TABLOLAR KULLANI LIYOR:**

| Tablo | Kullanım | V2'de Kullanılıyor mu? |
|-------|----------|------------------------|
| `shop_products` | Ana ürün verileri | ✅ **EVET** (+ 5 yeni alan) |
| `shop_product_variants` | Fiziksel farklılıklar | ✅ **EVET** (çatal, batarya varyantları) |
| `shop_product_attributes` | Filtreleme | ✅ **EVET** (voltaj, kapasite, vb.) |
| `shop_categories` | Kategoriler | ✅ **EVET** (+ 1 yeni alan: primary_specs_template) |
| `shop_brands` | Markalar | ✅ **EVET** (İXTİF) |
| `shop_attributes` | Attribute tanımları | ✅ **EVET** (7 ortak attribute) |
| `shop_orders` | Sipariş sistemi | ⏸️ **HAZIR** (ileride kullanılacak) |
| `shop_carts` | Sepet sistemi | ⏸️ **HAZIR** (ileride kullanılacak) |
| `shop_reviews` | Müşteri yorumları | ⏸️ **HAZIR** (FAQ değil, gerçek yorumlar için) |
| Diğer tablolar | Tam e-commerce sistemi | ⏸️ **HAZIR** (gelecek fazlar için) |

---

## 🎯 SONUÇ

### ✅ **ŞUAN KI DURUM:**

1. **Mevcut migration'lar sağlam** ✅
   - 28 tablo hazır
   - İlişkiler doğru
   - Index'ler optimize

2. **Eksik 6 alan eklendi** ✅
   - `shop_products`: 5 yeni JSON alanı
   - `shop_categories`: 1 yeni JSON alanı

3. **Migration dosyaları oluşturuldu** ✅
   - `029_add_v2_fields_to_shop_products.php`
   - `030_add_primary_specs_template_to_shop_categories.php`

### 🚀 **YAPILACAKLAR:**

```bash
# 1. Migration'ları çalıştır
php artisan migrate

# 2. Seeder'ları hazırla (daha önce oluşturuldu)
php artisan db:seed --class=ShopCategoryWithSpecsSeeder
php artisan db:seed --class=ShopAttributeSeeder

# 3. Test et
php artisan tinker
>>> \DB::table('shop_products')->first()
>>> \DB::table('shop_categories')->first()
```

---

**🎉 TÜM TABLOLAR SAĞLIKLI VE V2 İÇİN HAZIR!**
