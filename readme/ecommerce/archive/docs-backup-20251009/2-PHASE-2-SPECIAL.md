# 🌸 PHASE 2: SPECIAL SECTORS (Gelecek - 1 Sene Sonra)

## 🎯 AMAÇ

PHASE 1 sonrası **isteğe bağlı** özel sektör eklentileri:
- 🍕 Restaurant (Yemek siparişi)
- 🌸 Florist (Çiçek satışı)

**Not:** Bu extension'lar **opsiyonel**. Sadece ihtiyacı olan tenantlar aktif eder.

---

## 📊 TOPLAM: 8 TABLO

### **1. RESTAURANT EXTENSION** (5 Tablo)

#### `shop_preparation_logs`
**Ne işe yarar:** Sipariş hazırlık süreç takibi

**Kolonlar:**
```php
id, order_id
stage, // received, preparing, ready, picked_up
stage_name (JSON) // {"tr": "Hazırlanıyor", "en": "Preparing"}
started_at, completed_at, estimated_completion
notes, updated_by_user_id
created_at
```

---

#### `shop_restaurant_settings`
**Ne işe yarar:** Restoran özel ayarları

**Kolonlar:**
```php
id
restaurant_name, restaurant_type // fast_food, fine_dining, cafe
working_hours (JSON) // {"monday": "09:00-22:00"}
minimum_order_amount
average_preparation_time
max_concurrent_orders
enable_table_reservation
enable_takeaway, enable_dine_in, enable_delivery
kitchen_printer_enabled
metadata (JSON)
created_at, updated_at
```

---

#### `shop_menu_categories`
**Ne işe yarar:** Menü kategorileri (Ana Yemek, Tatlı, İçecek)

**Kolonlar:**
```php
id, parent_id
name (JSON), description (JSON), slug
icon_url, sort_order
is_active
available_times (JSON) // {"breakfast": true, "lunch": true}
created_at, updated_at
```

---

#### `shop_menu_modifiers`
**Ne işe yarar:** Ekstra malzeme, sos seçenekleri

**Kolonlar:**
```php
id, product_id
modifier_type // extra, sauce, topping, size, spice_level
name (JSON) // {"tr": "Acılı Sos", "en": "Spicy Sauce"}
price_modifier // +5 TL
is_required
options (JSON) // ["Az Acı", "Orta Acı", "Çok Acı"]
sort_order, is_active
created_at, updated_at
```

**Örnek:**
```
Lahmacun →
  - Acılık: Az/Orta/Çok (zorunlu, ücretsiz)
  - Soğan: Var/Yok (ücretsiz)
  - Ekstra Limon: +2 TL (opsiyonel)
```

---

#### `shop_table_reservations`
**Ne işe yarar:** Masa rezervasyonları

**Kolonlar:**
```php
id, customer_id
reservation_date, reservation_time
table_number, party_size // Kaç kişi
special_requests
status // pending, confirmed, cancelled, completed
confirmed_at, cancelled_at
created_at, updated_at
```

---

### **2. FLORIST EXTENSION** (3 Tablo)

#### `shop_special_days`
**Ne işe yarar:** Özel günler yönetimi (14 Şubat, Anneler Günü)

**Kolonlar:**
```php
id
name (JSON) // {"tr": "Sevgililer Günü", "en": "Valentine's Day"}
date, is_recurring, recurring_pattern
requires_time_slot
default_delivery_fee_multiplier // 1.5x ücret
min_order_amount, max_orders_per_slot
message_templates (JSON) // Hazır mesajlar
banner_image_url, icon_url
featured_categories (JSON), featured_products (JSON)
is_active
created_at, updated_at
```

---

#### `shop_florist_settings`
**Ne işe yarar:** Çiçekçi özel ayarları

**Kolonlar:**
```php
id
shop_name
enable_message_cards // Mesaj kartı
enable_photo_proof // Teslimat fotoğrafı
enable_care_instructions // Bakım talimatları
default_message_card_price
working_hours (JSON)
delivery_zones (JSON)
metadata (JSON)
created_at, updated_at
```

---

#### `shop_message_card_templates`
**Ne işe yarar:** Hazır mesaj şablonları

**Kolonlar:**
```php
id, occasion_type // birthday, anniversary, sympathy, congratulations
message_text (JSON)
is_active, sort_order
created_at, updated_at
```

---

## 🎯 KULLANIM SENARYOLARI

### 🍕 Restaurant Tenant
**PHASE 1 (55) + Restaurant Extension (5) = 60 Tablo**

**Kurulum:**
```bash
php artisan module:enable ShopRestaurant
php artisan migrate
```

**Ayarlar:**
```json
{
  "enable_restaurant_extension": true,
  "allow_physical_products": true,
  "product_type": "food",
  "enable_preparation_tracking": true
}
```

---

### 🌸 Florist Tenant
**PHASE 1 (55) + Florist Extension (3) = 58 Tablo**

**Kurulum:**
```bash
php artisan module:enable ShopFlorist
php artisan migrate
```

**Ayarlar:**
```json
{
  "enable_florist_extension": true,
  "enable_message_cards": true,
  "enable_special_days": true
}
```

---

## 📁 DİZİN YAPISI

```
Modules/
├── Shop/ (PHASE 1 - Core)
│   └── database/migrations/tenant/
│       ├── 001_create_shop_categories.php
│       └── ... (55 migration)
│
├── ShopRestaurant/ (Extension)
│   └── database/migrations/tenant/
│       ├── 001_create_shop_preparation_logs.php
│       └── ... (5 migration)
│
└── ShopFlorist/ (Extension)
    └── database/migrations/tenant/
        ├── 001_create_shop_special_days.php
        └── ... (3 migration)
```

---

## 📊 TABLO KARŞILAŞTIRMASI

| Tenant Tipi | PHASE 1 | Restaurant | Florist | TOPLAM |
|-------------|---------|------------|---------|--------|
| **Forklift** | 55 | - | - | 55 |
| **Üyelik** | 55 | - | - | 55 |
| **Bayilik** | 55 | - | - | 55 |
| **Restaurant** | 55 | 5 | - | 60 |
| **Florist** | 55 | - | 3 | 58 |
| **Restaurant+Florist** | 55 | 5 | 3 | 63 |

---

## 💡 ÖNEMLİ NOTLAR

### **PHASE 2 Opsiyonel Çünkü:**
- Forklift satanlar restaurant özelliklerine ihtiyaç duymaz
- Üyelik satanlar kurye sistemine ihtiyaç duymaz
- Her tenant sadece ihtiyacı olan extension'ı aktif eder

### **Modüler Yaklaşım:**
```
Shop (Core) → Herkes kullanır (zorunlu)
  ├── ShopRestaurant → İsteyen aktif eder
  └── ShopFlorist → İsteyen aktif eder
```

---

## 🚀 SONUÇ

**PHASE 2 gelecek için hazır!**

**ŞİMDİ:** PHASE 1 (55 tablo) öncelik ⭐
**GELECEK:** PHASE 2 (8 tablo) isteğe bağlı
