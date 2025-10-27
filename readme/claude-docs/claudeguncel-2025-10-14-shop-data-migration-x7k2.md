# Shop Verileri Taşıma Planı - tuufi_4ekim → tenant_ixtif

**Tarih:** 2025-10-14
**ID:** x7k2
**İşlem:** Shop tablolarındaki tüm verileri kaynak veritabanından hedef veritabanına taşıma

---

## 📊 MEVCUT DURUM ANALİZİ

### Kaynak Veritabanı: `tuufi_4ekim`
```
shop_attributes         →    7 kayıt
shop_brands             →    1 kayıt
shop_categories         →  106 kayıt
shop_products           → 1020 kayıt ⚠️ ÖNEMLİ
shop_settings           →   22 kayıt
shop_taxes              →    1 kayıt
shop_tax_rates          →    1 kayıt
shop_warehouses         →    1 kayıt
```

### Hedef Veritabanı: `tenant_ixtif`
```
shop_attributes         →    8 kayıt (1 fazla)
shop_brands             →    1 kayıt
shop_categories         →  107 kayıt (1 fazla)
shop_products           →    0 kayıt ⚠️ BOŞ
shop_settings           →   23 kayıt (1 fazla)
shop_taxes              →    1 kayıt
shop_tax_rates          →    0 kayıt ⚠️ BOŞ
shop_warehouses         →    1 kayıt
```

---

## 🎯 TAŞINACAK VERİLER

### 🔴 KRİTİK ÖNCELİK (Veri Kaybı Riski)
1. **shop_products** → 1020 ürün (tenant_ixtif'de 0 kayıt var)
2. **shop_product_variants** → Varyantlar
3. **shop_product_attributes** → Ürün özellikleri
4. **shop_tax_rates** → Vergi oranları (tenant_ixtif'de boş)

### 🟡 ORTA ÖNCELİK (Mevcut Veriler Var)
5. **shop_categories** → Kategoriler (hedefte 107, kaynakta 106)
6. **shop_attributes** → Özellikler (hedefte 8, kaynakta 7)
7. **shop_brands** → Markalar
8. **shop_settings** → Ayarlar (hedefte 23, kaynakta 22)

### 🟢 DÜŞÜK ÖNCELİK (Zaten Boş)
- shop_campaigns
- shop_cart_items, shop_carts
- shop_coupons, shop_coupon_usages
- shop_customers, shop_customer_addresses, shop_customer_groups
- shop_inventory, shop_stock_movements
- shop_orders, shop_order_addresses, shop_order_items
- shop_payments, shop_payment_methods
- shop_reviews
- shop_subscriptions, shop_subscription_plans

---

## ⚙️ TAŞIMA STRATEJİSİ

### Metod 1: TRUNCATE + INSERT (Önerilen)
```sql
-- Mevcut verileri temizle ve yeni verileri ekle
TRUNCATE TABLE tenant_ixtif.shop_products;
INSERT INTO tenant_ixtif.shop_products SELECT * FROM tuufi_4ekim.shop_products;
```

**Avantajlar:**
- ✅ Temiz başlangıç
- ✅ ID çakışması riski yok
- ✅ Hızlı ve garantili

**Dezavantajlar:**
- ⚠️ Mevcut veriler silinir

### Metod 2: DELETE + INSERT
```sql
-- Sadece çakışan ID'leri sil, sonra ekle
DELETE FROM tenant_ixtif.shop_products WHERE id IN (SELECT id FROM tuufi_4ekim.shop_products);
INSERT INTO tenant_ixtif.shop_products SELECT * FROM tuufi_4ekim.shop_products;
```

---

## 📋 TAŞIMA SIRASI (Foreign Key Bağımlılıkları)

```
1. shop_brands (bağımlılık yok)
2. shop_categories (bağımlılık yok)
3. shop_attributes (bağımlılık yok)
4. shop_warehouses (bağımlılık yok)
5. shop_taxes (bağımlılık yok)
6. shop_tax_rates (taxes'e bağlı)
7. shop_settings (bağımlılık yok)
8. shop_products (categories, brands'e bağlı) ⚠️
9. shop_product_variants (products'a bağlı) ⚠️
10. shop_product_attributes (products + attributes'a bağlı) ⚠️
```

---

## ✅ TAŞIMA PLANI

### Adım 1: Foreign Key Kontrolünü Geçici Kapat
```sql
SET FOREIGN_KEY_CHECKS = 0;
```

### Adım 2: Ana Tabloları Taşı
```sql
TRUNCATE TABLE tenant_ixtif.shop_brands;
INSERT INTO tenant_ixtif.shop_brands SELECT * FROM tuufi_4ekim.shop_brands;

TRUNCATE TABLE tenant_ixtif.shop_categories;
INSERT INTO tenant_ixtif.shop_categories SELECT * FROM tuufi_4ekim.shop_categories;

TRUNCATE TABLE tenant_ixtif.shop_attributes;
INSERT INTO tenant_ixtif.shop_attributes SELECT * FROM tuufi_4ekim.shop_attributes;

TRUNCATE TABLE tenant_ixtif.shop_warehouses;
INSERT INTO tenant_ixtif.shop_warehouses SELECT * FROM tuufi_4ekim.shop_warehouses;

TRUNCATE TABLE tenant_ixtif.shop_taxes;
INSERT INTO tenant_ixtif.shop_taxes SELECT * FROM tuufi_4ekim.shop_taxes;

TRUNCATE TABLE tenant_ixtif.shop_tax_rates;
INSERT INTO tenant_ixtif.shop_tax_rates SELECT * FROM tuufi_4ekim.shop_tax_rates;
```

### Adım 3: Ürün Verilerini Taşı (KRİTİK)
```sql
TRUNCATE TABLE tenant_ixtif.shop_products;
INSERT INTO tenant_ixtif.shop_products SELECT * FROM tuufi_4ekim.shop_products;

TRUNCATE TABLE tenant_ixtif.shop_product_variants;
INSERT INTO tenant_ixtif.shop_product_variants SELECT * FROM tuufi_4ekim.shop_product_variants;

TRUNCATE TABLE tenant_ixtif.shop_product_attributes;
INSERT INTO tenant_ixtif.shop_product_attributes SELECT * FROM tuufi_4ekim.shop_product_attributes;

TRUNCATE TABLE tenant_ixtif.shop_product_field_templates;
INSERT INTO tenant_ixtif.shop_product_field_templates SELECT * FROM tuufi_4ekim.shop_product_field_templates;
```

### Adım 4: Ayarları Taşı
```sql
TRUNCATE TABLE tenant_ixtif.shop_settings;
INSERT INTO tenant_ixtif.shop_settings SELECT * FROM tuufi_4ekim.shop_settings;
```

### Adım 5: Foreign Key Kontrolünü Aç
```sql
SET FOREIGN_KEY_CHECKS = 1;
```

### Adım 6: Doğrulama
```sql
SELECT 'shop_products' as tablo, COUNT(*) as kayit_sayisi FROM tenant_ixtif.shop_products
UNION ALL
SELECT 'shop_categories', COUNT(*) FROM tenant_ixtif.shop_categories
UNION ALL
SELECT 'shop_brands', COUNT(*) FROM tenant_ixtif.shop_brands
UNION ALL
SELECT 'shop_attributes', COUNT(*) FROM tenant_ixtif.shop_attributes;
```

---

## ⚠️ RİSK DEĞERLENDİRMESİ

### Yüksek Risk
- ❌ **Veri Kaybı**: tenant_ixtif'deki mevcut veriler silinecek
- ❌ **Foreign Key Hatası**: Bağımlılık sırası yanlış olursa hata alınabilir

### Orta Risk
- ⚠️ **ID Çakışması**: Mevcut ID'ler yeni verilerle çakışabilir

### Düşük Risk
- ✅ Kaynak veri bozulmaz (sadece SELECT)
- ✅ Geri dönüş için yedek alma mümkün

---

## 🔒 GÜVENLİK ÖNLEMLERİ

1. ✅ İşlem öncesi tenant_ixtif yedeği al
2. ✅ Foreign key kontrollerini yönet
3. ✅ Transaction kullan (mümkünse)
4. ✅ İşlem sonrası doğrulama yap

---

## 📝 TODO LİSTESİ

- [ ] Plan onayı al
- [ ] tenant_ixtif veritabanı yedeği al
- [ ] Foreign key kontrolünü kapat
- [ ] Ana tabloları taşı (brands, categories, attributes, warehouses, taxes, tax_rates)
- [ ] Ürün tablolarını taşı (products, variants, attributes, templates)
- [ ] Ayarları taşı (settings)
- [ ] Foreign key kontrolünü aç
- [ ] Kayıt sayılarını doğrula
- [ ] Test sorguları çalıştır

---

## 🎬 İŞLEME HAZIR

**ONAY BEKLENİYOR:** Bu planla devam edilsin mi?

**Alternatifler:**
1. Tüm shop_ tablolarını taşı (boş olanlar dahil)
2. Sadece kritik tabloları taşı (products, variants, attributes)
3. Manuel seçim (hangi tabloların taşınacağını belirt)
