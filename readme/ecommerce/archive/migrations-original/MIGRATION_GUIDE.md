# 🗄️ SHOP MODULE - MIGRATION GUIDE

## ✅ OLUŞTURULAN MIGRATION DOSYALARI

### 1. Katalog Tabloları
- ✅ `001_create_shop_categories_table.php` - Ürün kategorileri (hiyerarşik)
- ✅ `002_create_shop_brands_table.php` - Markalar
- ✅ `003_create_shop_products_table.php` - Ana ürün tablosu
- ✅ `004_create_shop_product_variants_table.php` - Ürün varyantları
- ✅ `005_create_shop_attributes_table.php` - Özellik tanımları
- ✅ `006_create_shop_product_attributes_table.php` - Ürün özellik değerleri

### 2. Müşteri Tabloları
- ✅ `007_create_shop_customers_table.php` - Müşteri bilgileri
- ⏳ `008_create_shop_customer_addresses_table.php` - Müşteri adresleri
- ⏳ `009_create_shop_customer_groups_table.php` - Müşteri grupları

### 3. Sipariş Tabloları
- ✅ `010_create_shop_orders_table.php` - Siparişler
- ⏳ `011_create_shop_order_items_table.php` - Sipariş kalemleri
- ⏳ `012_create_shop_order_addresses_table.php` - Sipariş adresleri (snapshot)
- ⏳ `013_create_shop_order_status_history_table.php` - Sipariş durum geçmişi
- ⏳ `014_create_shop_order_notes_table.php` - Sipariş notları

---

## 📋 KALAN MIGRATION ŞABLONLARI

### 📌 Sepet Tabloları (2 Tablo)

#### `015_create_shop_carts_table.php`
```php
Schema::create('shop_carts', function (Blueprint $table) {
    $table->id()->comment('Sepet benzersiz ID');
    $table->foreignId('customer_id')->nullable()->comment('Müşteri ID (kayıtlı kullanıcı)');
    $table->string('session_id')->nullable()->comment('Oturum ID (misafir kullanıcı)');
    $table->string('token')->unique()->comment('Sepet token (benzersiz)');
    $table->decimal('subtotal', 12, 2)->default(0)->comment('Ara toplam');
    $table->decimal('discount_amount', 10, 2)->default(0)->comment('İndirim tutarı');
    $table->decimal('total_amount', 12, 2)->default(0)->comment('Toplam tutar');
    $table->string('coupon_code')->nullable()->comment('Uygulanan kupon kodu');
    $table->timestamp('expires_at')->nullable()->comment('Sepet son kullanma tarihi');
    $table->timestamps();

    $table->index('customer_id');
    $table->index('session_id');
    $table->index('token');
    $table->index('expires_at');
});
```

#### `016_create_shop_cart_items_table.php`
```php
Schema::create('shop_cart_items', function (Blueprint $table) {
    $table->id()->comment('Sepet ürün ID');
    $table->foreignId('cart_id')->comment('Sepet ID');
    $table->foreignId('product_id')->comment('Ürün ID');
    $table->foreignId('variant_id')->nullable()->comment('Varyant ID');
    $table->integer('quantity')->default(1)->comment('Miktar');
    $table->decimal('unit_price', 10, 2)->comment('Birim fiyat');
    $table->decimal('total_price', 12, 2)->comment('Toplam fiyat');
    $table->json('options')->nullable()->comment('Seçilen opsiyonlar (kabin, koltuk, vb)');
    $table->timestamps();

    $table->index('cart_id');
    $table->index('product_id');
    $table->unique(['cart_id', 'product_id', 'variant_id']);
});
```

---

### 📌 Fiyatlandırma Tabloları (3 Tablo)

#### `017_create_shop_pricing_rules_table.php`
```php
Schema::create('shop_pricing_rules', function (Blueprint $table) {
    $table->id()->comment('Fiyat kuralı ID');
    $table->json('name')->comment('Kural adı ({"tr":"VIP İndirim","en":"VIP Discount"})');
    $table->enum('type', ['percentage', 'fixed', 'special_price'])->comment('Kural tipi');
    $table->decimal('value', 10, 2)->comment('İndirim değeri (% veya ₺)');
    $table->foreignId('customer_group_id')->nullable()->comment('Müşteri grubu ID');
    $table->foreignId('category_id')->nullable()->comment('Kategori ID (null=tüm kategoriler)');
    $table->integer('min_quantity')->default(1)->comment('Minimum miktar');
    $table->date('start_date')->nullable()->comment('Başlangıç tarihi');
    $table->date('end_date')->nullable()->comment('Bitiş tarihi');
    $table->boolean('is_active')->default(true)->comment('Aktif/Pasif');
    $table->integer('priority')->default(0)->comment('Öncelik (yüksek önce uygulanır)');
    $table->timestamps();

    $table->index('customer_group_id');
    $table->index('category_id');
    $table->index('is_active');
});
```

#### `018_create_shop_price_tiers_table.php`
```php
Schema::create('shop_price_tiers', function (Blueprint $table) {
    $table->id()->comment('Kademeli fiyat ID');
    $table->foreignId('product_id')->comment('Ürün ID');
    $table->integer('min_quantity')->comment('Minimum miktar');
    $table->integer('max_quantity')->nullable()->comment('Maksimum miktar (null=sınırsız)');
    $table->decimal('price', 10, 2)->comment('Fiyat (₺)');
    $table->decimal('discount_percentage', 5, 2)->nullable()->comment('İndirim yüzdesi');
    $table->timestamps();

    $table->index('product_id');
    $table->index(['product_id', 'min_quantity']);
});
```

#### `019_create_shop_product_prices_table.php`
```php
Schema::create('shop_product_prices', function (Blueprint $table) {
    $table->id()->comment('Çoklu fiyat ID');
    $table->foreignId('product_id')->comment('Ürün ID');
    $table->foreignId('customer_group_id')->nullable()->comment('Müşteri grubu ID (null=genel)');
    $table->string('price_list_name')->nullable()->comment('Fiyat listesi adı');
    $table->decimal('price', 10, 2)->comment('Fiyat (₺)');
    $table->date('start_date')->nullable()->comment('Başlangıç tarihi');
    $table->date('end_date')->nullable()->comment('Bitiş tarihi');
    $table->boolean('is_active')->default(true)->comment('Aktif/Pasif');
    $table->timestamps();

    $table->index('product_id');
    $table->index('customer_group_id');
    $table->index('is_active');
});
```

---

### 📌 Promosyon Tabloları (4 Tablo)

#### `020_create_shop_promotions_table.php`
#### `021_create_shop_promotion_rules_table.php`
#### `022_create_shop_coupons_table.php`
#### `023_create_shop_coupon_usages_table.php`

---

### 📌 Kargo Tabloları (3 Tablo)

#### `024_create_shop_shipping_zones_table.php`
#### `025_create_shop_shipping_methods_table.php`
#### `026_create_shop_shipping_rates_table.php`

---

### 📌 Ödeme Tabloları (3 Tablo)

#### `027_create_shop_payment_methods_table.php`
#### `028_create_shop_payment_transactions_table.php`
#### `029_create_shop_payment_installments_table.php`

---

### 📌 Stok Tabloları (3 Tablo)

#### `030_create_shop_inventory_logs_table.php`
```php
Schema::create('shop_inventory_logs', function (Blueprint $table) {
    $table->id()->comment('Stok hareket ID');
    $table->foreignId('product_id')->comment('Ürün ID');
    $table->foreignId('variant_id')->nullable()->comment('Varyant ID');
    $table->foreignId('location_id')->nullable()->comment('Depo/Lokasyon ID');
    $table->enum('type', ['purchase', 'sale', 'return', 'adjustment', 'transfer', 'damage'])
          ->comment('Hareket tipi: purchase=Alış, sale=Satış, return=İade, adjustment=Düzeltme, transfer=Transfer, damage=Fire');
    $table->integer('quantity_before')->comment('Önceki miktar');
    $table->integer('quantity_change')->comment('Değişim miktarı (+/-)');
    $table->integer('quantity_after')->comment('Sonraki miktar');
    $table->string('reference_type')->nullable()->comment('Referans tipi (Order, Return, vb)');
    $table->unsignedBigInteger('reference_id')->nullable()->comment('Referans ID');
    $table->text('notes')->nullable()->comment('Notlar');
    $table->foreignId('user_id')->nullable()->comment('İşlemi yapan kullanıcı');
    $table->timestamps();

    $table->index('product_id');
    $table->index(['reference_type', 'reference_id']);
    $table->index('created_at');
});
```

#### `031_create_shop_inventory_locations_table.php`
#### `032_create_shop_stock_reservations_table.php`

---

### 📌 Muhasebe Tabloları (5 Tablo)

#### `033_create_shop_invoices_table.php`
#### `034_create_shop_invoice_items_table.php`
#### `035_create_shop_expenses_table.php`
#### `036_create_shop_financial_reports_table.php`
#### `037_create_shop_tax_rates_table.php`

---

### 📌 Teklif Tabloları (3 Tablo - B2B)

#### `038_create_shop_quotes_table.php`
```php
Schema::create('shop_quotes', function (Blueprint $table) {
    $table->id()->comment('Teklif ID');
    $table->string('quote_number')->unique()->comment('Teklif numarası (QUO-2024-00001)');
    $table->foreignId('customer_id')->comment('Müşteri ID');
    $table->enum('status', ['draft', 'sent', 'viewed', 'accepted', 'rejected', 'expired'])
          ->default('draft')->comment('Teklif durumu');
    $table->decimal('subtotal', 14, 2)->comment('Ara toplam');
    $table->decimal('discount_amount', 12, 2)->default(0)->comment('İndirim');
    $table->decimal('tax_amount', 12, 2)->default(0)->comment('Vergi');
    $table->decimal('total_amount', 14, 2)->comment('Toplam');
    $table->date('valid_until')->nullable()->comment('Geçerlilik tarihi');
    $table->text('terms_conditions')->nullable()->comment('Şartlar ve koşullar');
    $table->text('notes')->nullable()->comment('Notlar');
    $table->foreignId('converted_order_id')->nullable()->comment('Dönüştürülen sipariş ID');
    $table->timestamp('sent_at')->nullable()->comment('Gönderilme tarihi');
    $table->timestamp('viewed_at')->nullable()->comment('Görüntülenme tarihi');
    $table->timestamp('accepted_at')->nullable()->comment('Kabul edilme tarihi');
    $table->timestamps();
    $table->softDeletes();

    $table->index('quote_number');
    $table->index('customer_id');
    $table->index('status');
});
```

#### `039_create_shop_quote_items_table.php`
#### `040_create_shop_quote_status_history_table.php`

---

### 📌 Yorum & Soru Tabloları (4 Tablo)

#### `041_create_shop_product_reviews_table.php`
#### `042_create_shop_review_images_table.php`
#### `043_create_shop_product_questions_table.php`
#### `044_create_shop_product_answers_table.php`

---

### 📌 Sadakat Tabloları (3 Tablo)

#### `045_create_shop_loyalty_programs_table.php`
#### `046_create_shop_customer_points_table.php`
#### `047_create_shop_point_transactions_table.php`

---

### 📌 İade Tabloları (2 Tablo)

#### `048_create_shop_returns_table.php`
#### `049_create_shop_return_items_table.php`

---

### 📌 Kampanya Tabloları (4 Tablo)

#### `050_create_shop_flash_sales_table.php`
#### `051_create_shop_flash_sale_products_table.php`
#### `052_create_shop_product_bundles_table.php`
#### `053_create_shop_bundle_items_table.php`

---

### 📌 Hediye Tabloları (4 Tablo)

#### `054_create_shop_gift_cards_table.php`
#### `055_create_shop_gift_card_transactions_table.php`
#### `056_create_shop_gift_wrapping_options_table.php`
#### `057_create_shop_order_gift_wrapping_table.php`

---

### 📌 Bayilik Tabloları - HİBRİT (5 Tablo)

#### `058_create_shop_vendors_table.php`
```php
Schema::create('shop_vendors', function (Blueprint $table) {
    $table->id()->comment('Bayi ID');
    $table->foreignId('user_id')->comment('Kullanıcı ID - users ilişkisi');
    $table->string('vendor_code')->unique()->comment('Bayi kodu (VENDOR-001)');
    $table->enum('vendor_type', ['individual', 'company'])->comment('Bayi tipi');
    $table->json('company_info')->nullable()->comment('Şirket bilgileri (JSON)');
    $table->enum('status', ['pending', 'active', 'suspended', 'rejected'])
          ->default('pending')->comment('Bayi durumu');
    $table->decimal('commission_rate', 5, 2)->default(10)->comment('Komisyon oranı (%)');
    $table->decimal('total_sales', 14, 2)->default(0)->comment('Toplam satış (₺)');
    $table->decimal('total_commission', 14, 2)->default(0)->comment('Toplam komisyon (₺)');
    $table->decimal('pending_payout', 12, 2)->default(0)->comment('Bekleyen ödeme (₺)');
    $table->json('documents')->nullable()->comment('Belgeler (vergi levhası, vb)');
    $table->text('notes')->nullable()->comment('Notlar');
    $table->timestamp('approved_at')->nullable()->comment('Onaylanma tarihi');
    $table->timestamps();
    $table->softDeletes();

    $table->index('user_id');
    $table->index('vendor_code');
    $table->index('status');
});
```

#### `059_create_shop_vendor_products_table.php`
#### `060_create_shop_vendor_commissions_table.php`
#### `061_create_shop_vendor_payouts_table.php`
#### `062_create_shop_vendor_applications_table.php`

---

### 📌 Diğer Tabloları (3 Tablo)

#### `063_create_shop_wishlists_table.php`
#### `064_create_shop_wishlist_items_table.php`
#### `065_create_shop_stock_notifications_table.php`

---

### 📌 Ayarlar (1 Tablo)

#### `066_create_shop_module_settings_table.php`
```php
Schema::create('shop_module_settings', function (Blueprint $table) {
    $table->id()->comment('Ayar ID');
    $table->string('key')->comment('Ayar anahtarı (payment_methods_enabled, tax_rate, vb)');
    $table->text('value')->nullable()->comment('Ayar değeri');
    $table->enum('type', ['string', 'integer', 'boolean', 'json', 'text'])
          ->default('string')->comment('Değer tipi');
    $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');
    $table->string('group')->default('general')->comment('Ayar grubu (general, payment, shipping, vb)');
    $table->boolean('is_public')->default(false)->comment('Frontend\'de kullanılabilir mi?');
    $table->timestamps();

    $table->unique('key');
    $table->index('group');
});
```

---

## 🔧 MIGRATION KULLANIMI

### Migration Çalıştırma
```bash
# Tüm migration'ları çalıştır
php artisan migrate --path=Modules/Shop/Database/Migrations

# Belirli bir migration'ı çalıştır
php artisan migrate --path=Modules/Shop/Database/Migrations/001_create_shop_categories_table.php

# Migration'ları geri al
php artisan migrate:rollback --path=Modules/Shop/Database/Migrations

# Tüm migration'ları sıfırla ve tekrar çalıştır
php artisan migrate:fresh --path=Modules/Shop/Database/Migrations
```

### Seeder Çalıştırma
```bash
# Tüm seeder'ları çalıştır
php artisan db:seed --class=ShopDatabaseSeeder

# Belirli seeder çalıştır
php artisan db:seed --class=ShopCategorySeeder
```

---

## 📝 ÖNEMLİ NOTLAR

### 1. JSON Kolonları
- Tüm çoklu dil alanları JSON formatında: `{"tr":"...","en":"..."}`
- MySQL 5.7+ veya MariaDB 10.2.7+ gerektirir
- JSON kolonlarda arama için `->` operatörü kullanılır

### 2. Foreign Key Constraints
- `onDelete('cascade')`: Parent silinince child da silinir
- `onDelete('set null')`: Parent silinince child'daki FK null olur
- `onDelete('restrict')`: Parent silinmez eğer child varsa

### 3. Index Stratejisi
- WHERE kullanılan kolonlara index ekle
- JOIN yapılan kolonlara index ekle
- Composite index'ler sık kullanılan kombinasyonlar için

### 4. Soft Delete
- Önemli tablolarda soft delete kullan
- `deleted_at` kolonu otomatik eklenir
- `->whereNull('deleted_at')` ile aktif kayıtları filtrele

### 5. Comment Standartları
- Her kolon için Türkçe comment ekle
- Enum değerlerini açıkla
- JSON yapısını örnek ile göster

---

## ✅ SONRAKI ADIMLAR

1. ✅ Kalan migration dosyalarını oluştur
2. ⏳ Seeder dosyalarını hazırla
3. ⏳ Model dosyalarını oluştur (Eloquent)
4. ⏳ Repository pattern uygula
5. ⏳ Livewire component'leri oluştur
6. ⏳ Admin panel sayfalarını yap
7. ⏳ Frontend sayfalarını yap

---

**Son Güncelleme:** 2025-10-09
**Toplam Tablo:** 66
**Oluşturulan:** 8
**Kalan:** 58
