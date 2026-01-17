# Payment & Order Tablo Yapıları

## 📊 KULLANILAN TABLOLAR

### 1️⃣ PAYMENTS (Ödemeler)
**Tablo:** `payments`
**Primary Key:** `payment_id`
**Soft Delete:** ✅ Var (`deleted_at`)

#### Önemli Kolonlar:
- `payment_id` - Primary key
- `payment_number` - Unique (PAY-20260116172554-2318F0)
- `payable_type` - Polymorphic (hangi model?) → Order, Subscription, vs.
- `payable_id` - Polymorphic ID
- `payment_method_id` - Foreign key → payment_methods tablosu
- `amount` - Ödeme tutarı
- `currency` - Para birimi (TRY, USD, vs.)
- `status` - pending, processing, completed, failed, cancelled, refunded
- `gateway` - paytr, stripe, iyzico, paypal, manual
- `gateway_transaction_id` - PayTR merchant_oid
- `paid_at` - Ödeme tarihi
- `created_at` - Oluşturulma tarihi
- `deleted_at` - Soft delete (NULL = aktif, dolu = silinmiş)

#### İlişkiler:
```php
// Polymorphic - Herhangi bir modele bağlanabilir
payable_type + payable_id

Örnekler:
- payable_type: "Modules\Cart\App\Models\Order"
- payable_type: "Modules\Subscription\App\Models\Subscription"
```

---

### 2️⃣ CART_ORDERS (Siparişler)
**Tablo:** `cart_orders`
**Primary Key:** `order_id`
**Soft Delete:** ✅ Var (`deleted_at`)

#### Önemli Kolonlar:
- `order_id` - Primary key
- `order_number` - Unique (ORD2026011622E266)
- `user_id` - Foreign key → users tablosu (müşteri)
- `order_type` - sale, subscription, service, digital
- `status` - pending, confirmed, processing, ready, shipped, delivered, completed, cancelled, refunded
- `payment_status` - pending, partially_paid, paid, refunded, failed
- `subtotal` - Ara toplam
- `tax_amount` - KDV tutarı
- `total_amount` - Toplam tutar
- `paid_amount` - Ödenen tutar
- `currency` - Para birimi
- `customer_name` - Müşteri adı (snapshot)
- `customer_email` - Müşteri email (snapshot)
- `created_at` - Oluşturulma tarihi
- `deleted_at` - Soft delete

#### İlişkiler:
```php
// Payment'lara gelen bağlantı
payments.payable_type = "Modules\Cart\App\Models\Order"
payments.payable_id = cart_orders.order_id

// User'a bağlantı
cart_orders.user_id → users.id

// Order items
cart_orders.order_id ← cart_order_items.order_id
```

---

### 3️⃣ CART_ORDER_ITEMS (Sipariş Kalemleri)
**Tablo:** `cart_order_items`
**Primary Key:** `order_item_id`
**Soft Delete:** ✅ Var

#### Önemli Kolonlar:
- `order_item_id` - Primary key
- `order_id` - Foreign key → cart_orders.order_id
- `orderable_type` - Polymorphic (ürün tipi)
- `orderable_id` - Polymorphic ID
- `item_title` - Ürün başlığı (snapshot)
- `quantity` - Miktar
- `unit_price` - Birim fiyat
- `total_price` - Toplam fiyat
- `metadata` - JSON (cycle_key, vs.)

#### İlişkiler:
```php
// Order'a bağlı
cart_order_items.order_id → cart_orders.order_id

// Polymorphic - Herhangi bir ürüne bağlanabilir
orderable_type + orderable_id

Örnekler:
- orderable_type: "Modules\Subscription\App\Models\SubscriptionPlan"
- orderable_type: "Modules\Shop\App\Models\ShopProduct"
```

---

### 4️⃣ CARTS (Sepetler)
**Tablo:** `carts`
**Primary Key:** `cart_id`
**Soft Delete:** ✅ Var (`deleted_at`)

#### Önemli Kolonlar:
- `cart_id` - Primary key
- `customer_id` - User ID (misafir ise NULL)
- `session_id` - Session ID (misafir için)
- `status` - active, abandoned, converted, merged
- `items_count` - Ürün sayısı
- `total` - Toplam tutar
- `converted_to_order_id` - Hangi order'a dönüştü?
- `converted_at` - Dönüştürülme tarihi

#### İlişkiler:
```php
// User'a bağlantı
carts.customer_id → users.id (nullable)

// Order'a bağlantı (dönüştürme sonrası)
carts.converted_to_order_id → cart_orders.order_id

// Cart items
carts.cart_id ← cart_items.cart_id
```

---

### 5️⃣ CART_ITEMS (Sepet Kalemleri)
**Tablo:** `cart_items`
**Primary Key:** `cart_item_id`
**Soft Delete:** ❌ Yok

#### Önemli Kolonlar:
- `cart_item_id` - Primary key
- `cart_id` - Foreign key → carts.cart_id
- `cartable_type` - Polymorphic (ürün tipi)
- `cartable_id` - Polymorphic ID
- `quantity` - Miktar
- `unit_price` - Birim fiyat
- `total` - Toplam fiyat

---

## 🔗 İLİŞKİ DİYAGRAMI

```
users (Central DB)
  └─> carts (Tenant DB) [customer_id]
       └─> cart_items [cart_id]
            └─> cartable (SubscriptionPlan, Product, vs.) [polymorphic]

  └─> cart_orders (Tenant DB) [user_id]
       ├─> cart_order_items [order_id]
       │    └─> orderable (SubscriptionPlan, Product, vs.) [polymorphic]
       │
       └─> payments [payable_type + payable_id] ← POLYMORPHIC!
            └─> payment_methods [payment_method_id]
```

---

## 📍 14 OCAK VE ÖNCESİ NASIL BULABİLİRİZ?

### Payment Kayıtları:
```sql
SELECT * FROM payments
WHERE DATE(created_at) <= '2026-01-14';
```

### İlişkili Order'lar:
```sql
SELECT o.* FROM cart_orders o
INNER JOIN payments p ON p.payable_type = 'Modules\\Cart\\App\\Models\\Order'
                     AND p.payable_id = o.order_id
WHERE DATE(p.created_at) <= '2026-01-14';
```

VEYA:

```sql
SELECT * FROM cart_orders
WHERE DATE(created_at) <= '2026-01-14';
```

### İlişkili Order Items:
```sql
SELECT oi.* FROM cart_order_items oi
INNER JOIN cart_orders o ON oi.order_id = o.order_id
WHERE DATE(o.created_at) <= '2026-01-14';
```

---

## ⚠️ SİLME SIRASI (Foreign Key Constraints)

**DOĞRU SIRA:**
1. `cart_order_items` (en içteki child)
2. `cart_orders` (parent)
3. `payments` (polymorphic - en sona)

**YANLIŞ SIRA:**
Eğer önce `payments` silinirse → `cart_orders` yetim kalır (orphaned)
Eğer önce `cart_orders` silinirse → Foreign key hatası alabilirsin

---

## 💾 YEDEK ALMA

```bash
# Sadece 14 Ocak ve öncesi
mysqldump tenant_muzibu_1528d0 \
  payments \
  cart_orders \
  cart_order_items \
  --where="DATE(created_at) <= '2026-01-14'" \
  > backup.sql
```

---

## 🗑️ SİLME SORGUSU (HARD DELETE)

```sql
-- 1. Order Items
DELETE FROM cart_order_items
WHERE order_id IN (
    SELECT order_id FROM cart_orders
    WHERE DATE(created_at) <= '2026-01-14'
);

-- 2. Orders
DELETE FROM cart_orders
WHERE DATE(created_at) <= '2026-01-14';

-- 3. Payments
DELETE FROM payments
WHERE DATE(created_at) <= '2026-01-14';
```

---

## ✅ SOFT DELETE (Önerilen - Geri Getirilebilir)

```sql
-- Soft delete (deleted_at set edilir)
UPDATE payments
SET deleted_at = NOW()
WHERE DATE(created_at) <= '2026-01-14'
AND deleted_at IS NULL;

UPDATE cart_orders
SET deleted_at = NOW()
WHERE DATE(created_at) <= '2026-01-14'
AND deleted_at IS NULL;
```

---

Oluşturulma: 16 Ocak 2026
