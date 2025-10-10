# 🛒 E-COMMERCE MODULE - PHASE 1

## 📋 GENEL BAKIŞ

Bu döküman E-Commerce modülünün **Phase 1** (Faz 1) yapısını detaylı olarak açıklar.

**Phase 1 Hedefi:**
- ✅ Ürün Kataloğu (Kategori, Marka, Ürün, Varyant, Özellikler)
- ✅ Üyelik Satışı (Subscription-based monetization)
- ✅ Kupon ve Promosyon Sistemi
- ✅ Sipariş Yönetimi (Order, Payment, Cart)
- ✅ Stok Yönetimi (Warehouse, Inventory, Stock Movements)
- ✅ Universal Sistemler (Favorites, Search Logs)

---

## 🗄️ VERİTABANI MİMARİSİ

### Toplam Tablo Sayısı: 28

#### 🌐 Universal Tables (2)
Tüm modüller için ortak tablolar (Shop, Blog, Portfolio, etc.)

| # | Tablo | Açıklama | Trait |
|---|-------|----------|-------|
| 1 | `favorites` | Polymorphic favoriler (ürün, blog, portfolyo) | `HasFavorites`, `Favoritable` |
| 2 | `search_logs` | Tüm modüller için arama analitikleri | `Searchable` |

#### 🛍️ Shop Module Tables (26)

##### **KATALOG (6 tablo)**
| # | Tablo | Açıklama | İlişkiler |
|---|-------|----------|-----------|
| 1 | `shop_categories` | Kategoriler (nested, unlimited depth) | parent, children, products |
| 2 | `shop_brands` | Markalar | products |
| 3 | `shop_products` | Ürünler (catalog products) | category, brand, variants, reviews, carts, favorites |
| 4 | `shop_product_variants` | Ürün varyantları (renk, beden vs.) | product, attribute_values, cart_items, order_items |
| 5 | `shop_attributes` | Özellikler (Renk, Beden, Materyal) | values |
| 6 | `shop_attribute_values` | Özellik değerleri (Kırmızı, XL, Pamuk) | attribute, product_variants |

##### **ÜYELİK (2 tablo)**
| # | Tablo | Açıklama | Model |
|---|-------|----------|-------|
| 7 | `shop_subscription_plans` | Üyelik paketleri (Bronze, Premium) | SubscriptionPlan |
| 8 | `shop_subscriptions` | Aktif üyelikler (user → plan) | Subscription |

##### **SİPARİŞ (5 tablo)**
| # | Tablo | Açıklama | İlişkiler |
|---|-------|----------|-----------|
| 9 | `shop_orders` | Siparişler | user, items, addresses, payments |
| 10 | `shop_order_items` | Sipariş kalemleri | order, product_variant |
| 11 | `shop_order_addresses` | Sipariş adresleri (billing, shipping) | order |
| 12 | `shop_payment_methods` | Ödeme yöntemleri (Kredi Kartı, PayPal) | payments |
| 13 | `shop_payments` | Ödeme kayıtları | order, payment_method |

##### **STOK (3 tablo)**
| # | Tablo | Açıklama | İlişkiler |
|---|-------|----------|-----------|
| 14 | `shop_warehouses` | Depolar | inventories |
| 15 | `shop_inventory` | Stok miktarları | warehouse, product_variant |
| 16 | `shop_stock_movements` | Stok hareketleri (giriş/çıkış) | warehouse, product_variant |

##### **SEPET (2 tablo)**
| # | Tablo | Açıklama | İlişkiler |
|---|-------|----------|-----------|
| 17 | `shop_carts` | Sepetler | user, items |
| 18 | `shop_cart_items` | Sepet kalemleri | cart, product_variant |

##### **VERGİ (2 tablo)**
| # | Tablo | Açıklama | İlişkiler |
|---|-------|----------|-----------|
| 19 | `shop_taxes` | Vergi tanımları (KDV, ÖTV) | rates |
| 20 | `shop_tax_rates` | Vergi oranları (country/region based) | tax |

##### **PROMOSYON (3 tablo)**
| # | Tablo | Açıklama | İlişkiler |
|---|-------|----------|-----------|
| 21 | `shop_coupons` | Kupon kodları (SUMMER2025) | usages |
| 22 | `shop_coupon_usages` | Kupon kullanım kayıtları | coupon, user, order |
| 23 | `shop_campaigns` | Kampanyalar (Yılbaşı İndirimi) | products (JSON) |

##### **DİĞER (3 tablo)**
| # | Tablo | Açıklama | İlişkiler |
|---|-------|----------|-----------|
| 24 | `shop_reviews` | Ürün değerlendirmeleri | product, user |
| 25 | `shop_customer_addresses` | Müşteri adresleri (saved addresses) | user |
| 26 | `shop_settings` | Modül ayarları (JSON key-value) | - |

---

## 🎯 ÖZELLİKLER

### ✨ Phase 1'de Gelen Özellikler

#### 📦 Katalog Yönetimi
- ✅ Sınırsız derinlikte kategori ağacı
- ✅ Marka yönetimi
- ✅ Ürün varyantları (renk, beden, vs.)
- ✅ Dinamik özellikler sistemi (attributes)
- ✅ Çoklu dil desteği (JSON fields: `title`, `slug`, `description`)

#### 💳 Üyelik Sistemi (Subscription-based)
- ✅ Paket bazlı üyelik (Bronze, Premium)
- ✅ Aylık/yıllık ödeme seçenekleri
- ✅ Otomatik yenileme (cron jobs)
- ✅ İçerik kilitleme (middleware)
- ✅ E-posta bildirimleri (activated, renewed, expiring)

#### 🛒 Sepet & Sipariş
- ✅ Misafir ve üye sepeti
- ✅ Çoklu ödeme yöntemi
- ✅ Sipariş durumu takibi
- ✅ Fatura/kargo adresleri
- ✅ Ödeme entegrasyonu (hazır)

#### 📊 Stok Yönetimi
- ✅ Çoklu depo desteği
- ✅ Stok hareketleri (giriş/çıkış/transfer)
- ✅ Düşük stok uyarıları
- ✅ Varyant bazlı stok takibi

#### 🎁 Promosyon & Kupon
- ✅ Kupon kodları (miktar/yüzde indirim)
- ✅ Minimum sepet tutarı
- ✅ Kullanım limitleri (toplam/kullanıcı başı)
- ✅ Kampanya yönetimi
- ✅ Ürün bazlı indirimler

#### 🌐 Universal Sistemler
- ✅ **Favorites**: Ürün, blog, portfolyo favorileme (polymorphic)
- ✅ **Search Logs**: Tüm modüller için arama analitikleri
- ✅ **SEO Management**: Universal SEO modülü entegrasyonu
- ✅ **Tags**: Evrensel etiket sistemi

#### ⭐ Değerlendirme & Yorum
- ✅ 5 yıldız puanlama
- ✅ Yorum onay sistemi
- ✅ Admin yanıtlama

---

## 📁 DOSYA YAPISI

```
readme/ecommerce/
├── migrations/
│   ├── universal/                    # 2 dosya
│   │   ├── 2025_01_10_000001_create_favorites_table.php
│   │   └── 2025_01_10_000002_create_search_logs_table.php
│   └── phase-1/                      # 26 dosya
│       ├── 001_create_shop_categories_table.php
│       ├── 002_create_shop_brands_table.php
│       ├── 003_create_shop_products_table.php
│       ├── ... (23 more files)
│       └── 026_create_shop_settings_table.php
│
├── archive/
│   ├── migrations-backup-20251009/   # Orijinal 66 migration
│   └── docs-backup-20251009/         # Eski dökümanlar
│
├── TODO.md                           # Detaylı görev listesi
├── README.md                         # Bu dosya
└── [diğer dökümanlar]
```

---

## 🚀 KURULUM

### 1️⃣ Migration Deployment

```bash
# Universal migration'ları taşı
cp migrations/universal/* ../../database/migrations/

# Shop migration'larını taşı
mkdir -p ../../Modules/Shop/Database/Migrations
cp migrations/phase-1/* ../../Modules/Shop/Database/Migrations/

# Migration'ları çalıştır
php artisan migrate

# Doğrulama
php artisan migrate:status
```

### 2️⃣ Model & Repository Oluşturma

```bash
# Model oluşturma (örnek)
php artisan make:model Shop/Product -m

# Repository oluşturma (manual - Page pattern'i kopyala)
# Modules/Page/app/Repositories/PageRepository.php
```

### 3️⃣ Trait Implementation

**Universal Traits:**
- `app/Traits/HasFavorites.php` → User model
- `app/Traits/Favoritable.php` → Product, Post, Portfolio models
- `app/Traits/Searchable.php` → All searchable models

**Shop Traits:**
- `Modules/Shop/app/Traits/HasViewCounter.php`
- `Modules/Shop/app/Traits/Reviewable.php`

---

## 💡 KULLANIM ÖRNEKLERİ

### Kategori Ağacı

```php
// Kök kategorileri al
$rootCategories = Category::whereNull('parent_id')
    ->where('is_active', true)
    ->orderBy('sort_order')
    ->get();

// Alt kategorileri al (eager loading)
$categories = Category::with('children')->get();
```

### Ürün Varyantları

```php
// Ürün ve varyantları
$product = Product::with('variants.attributeValues')->find($id);

// Varyant stok kontrolü
$variant = ProductVariant::find($variantId);
if ($variant->hasStock(5)) {
    // Sepete ekle
}
```

### Favoriler (Universal)

```php
// User model
use HasFavorites;

// Favorilere ekle
$user->addFavorite($product);
$user->addFavorite($blogPost);
$user->addFavorite($portfolioItem);

// Favori mi?
if ($user->hasFavorited($product)) {
    // Kalp ikonu dolu göster
}

// Tüm favorileri al
$favorites = $user->favorites; // Polymorphic collection
```

### Arama Logu (Universal)

```php
use App\Models\SearchLog;

// Arama kaydı oluştur
SearchLog::create([
    'query' => 'laptop',
    'module' => 'shop',
    'filters' => ['category' => 'electronics', 'price_range' => [1000, 5000]],
    'results_count' => 42,
    'user_id' => auth()->id(),
    'session_id' => session()->getId(),
    'ip_address' => request()->ip(),
]);

// Tıklama kaydı
$searchLog->update([
    'clicked_result_type' => 'Product',
    'clicked_result_id' => $product->id,
    'clicked_position' => 3,
]);
```

### Üyelik Kontrolü

```php
// User model
if ($user->hasActiveSubscription()) {
    // Premium içerik göster
}

// Middleware
Route::middleware(['subscription:premium'])->group(function () {
    // Premium rotalar
});
```

### Kupon Kullanımı

```php
$coupon = Coupon::where('code', 'SUMMER2025')
    ->where('is_active', true)
    ->first();

if ($coupon->isValid() && $coupon->canUseBy($user)) {
    $discount = $coupon->calculateDiscount($cartTotal);
}
```

---

## 🏗️ MİMARİ KARARLAR

### Portfolio Pattern Kullanımı

Phase 1 migration'ları **Portfolio modülü pattern'i** baz alınarak oluşturuldu:

**Standartlar:**
- ✅ Meaningful primary keys: `id('category_id')` not `id()`
- ✅ JSON multilingual fields: `title`, `slug`, `description`
- ✅ Foreign keys reference meaningful IDs: `->references('category_id')`
- ✅ JSON slug indexes for performance (MySQL 8.0+)
- ✅ Timestamp indexes: `$table->index('created_at')`
- ✅ Schema existence checks: `if (Schema::hasTable('...'))`

### Universal vs Module-Specific

**Universal Sistemler (Tüm modüller için ortak):**
- Favorites (polymorphic)
- Search Logs
- SEO Management (SeoManagement module)
- Tags (polymorphic)
- Activity Logs

**Module-Specific Sistemler (Sadece Shop için):**
- Categories (shop_categories)
- Products (shop_products)
- Orders (shop_orders)
- Subscriptions (shop_subscriptions)
- Reviews (shop_reviews - product-specific)

### Neden 28 Tablo?

Orijinal planda 66 tablo vardı, Phase 1'de **28 tablo'ya indirgendi**:

**Kaldırılanlar (38 tablo):**
- 10 tablo → Universal sistemlere taşındı (favorites, search_logs, tags, seo)
- 6 tablo → JSON field'lara taşındı (translations, metas)
- 10 tablo → Phase 2'ye ertelendi (analytics, affiliates, etc.)
- 12 tablo → Gereksiz/duplicate (product_images, product_views, etc.)

**Kalanlar (28 tablo):**
- 26 Shop tablosu (core functionality)
- 2 Universal tablo (favorites, search_logs)

---

## 📊 SONRAKI FAZLAR

### Phase 2 (Planlama Aşamasında)
- Multi-vendor (çoklu satıcı)
- Affiliate sistemi
- Advanced analytics
- Product comparison
- Wishlist sharing
- Gift cards

### Phase 3 (İleri Düzey)
- B2B features
- Multi-currency
- Multi-warehouse advanced
- Subscription management dashboard
- Advanced reporting

---

## 🔗 İLGİLİ DÖKÜMANTASYON

- **TODO.md** - Detaylı görev listesi ve ilerleme takibi
- **STANDARDIZATION-PATTERN.md** - Migration standardizasyon kuralları
- **PHASE-1-ABSOLUTE-FINAL.md** - Phase 1 tablo detayları
- **UNIVERSAL-SYSTEMS-REVIEW.md** - Universal sistem kararları
- **TABLE-ANALYSIS.md** - 66 tablodan 28'e indirgeme analizi

---

## 📝 NOTLAR

### Önemli Hatırlatmalar

1. **SEO Yönetimi**: Tablo bazlı değil, `SeoManagement` modülü üzerinden
2. **Media Yönetimi**: `media` tablosu ile (Spatie Media Library)
3. **Multilingual**: JSON fields kullanılıyor (`title`, `slug`, `description`)
4. **Primary Keys**: Anlamlı isimler (`category_id`, `product_id`)
5. **Foreign Keys**: Anlamlı primary key'lere referans veriyor

### JSON Field Syntax

```php
// Migration
$table->json('title')->comment('Başlık: {"tr": "Elektronik", "en": "Electronics", "vs.": "..."}');
$table->json('slug')->comment('Slug: {"tr": "elektronik", "en": "electronics", "vs.": "..."}');

// Model cast
protected $casts = [
    'title' => 'array',
    'slug' => 'array',
];

// Accessor
public function getTitleAttribute($value)
{
    $titles = json_decode($value, true);
    return $titles[app()->getLocale()] ?? $titles['tr'] ?? '';
}
```

### JSON Slug Indexing (MySQL 8.0+ / MariaDB 10.5+)

```php
// Migration'da index ekleme
DB::statement("ALTER TABLE shop_categories ADD INDEX idx_slug_tr ((CAST(slug->'$.tr' AS CHAR(255)) COLLATE utf8mb4_bin))");
```

---

## ✅ DURUM

**Migration Aşaması**: ✅ %100 Tamamlandı (28/28)
**Model Aşaması**: ⏳ Bekliyor (0/28)
**Repository Aşaması**: ⏳ Bekliyor (0/20)
**Controller Aşaması**: ⏳ Bekliyor (0/15)

**Sonraki Adım**: Model oluşturma (TODO.md'de detaylı liste)

---

**Hazırlayan**: Claude AI
**Tarih**: 2025-01-10
**Versiyon**: Phase 1 - v1.0
