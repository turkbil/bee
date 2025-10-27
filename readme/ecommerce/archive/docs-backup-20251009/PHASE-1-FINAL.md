# 🚀 FAZ 1: ÜRÜN KATALOĞU + ÜYELİK SATIŞI

**Karar:** Hem ürün kataloğu hem üyelik satışı 1. fazda!
**Hedef:** Kullanıcılar ürünleri görebilmeli ve üyelik satın alabilmeli
**Süre:** 1-1.5 ay
**Tablo Sayısı:** 24

---

## 🎯 FAZ 1 HEDEFLERİ

### 1️⃣ Ürün Kataloğu
```
✅ Ürünleri listele (kategoriler, markalar)
✅ Ürün detay sayfası
✅ Varyant sistemi (mast yüksekliği, batarya)
✅ Özellikler (kapasite, menzil, vb)
✅ Sepete ekle
✅ Stok kontrolü
```

### 2️⃣ Üyelik Satışı
```
✅ Üyelik paketleri göster (Aylık, Yıllık)
✅ Paket satın alma
✅ Ödeme entegrasyonu (iyzico)
✅ Otomatik yenileme
✅ Üyelik yönetimi (iptal, duraklatma)
```

### 3️⃣ Temel Özellikler
```
✅ Kupon sistemi
✅ Basit vergi hesaplama
✅ Fatura adresi yönetimi
```

---

## 📊 FAZ 1 TABLOLARI (24 Tablo)

### A. KATALOG SİSTEMİ (6 Tablo)

```
001_create_shop_categories_table.php              // Kategori ağacı
002_create_shop_brands_table.php                  // Markalar
003_create_shop_products_table.php                // Ana ürünler
004_create_shop_product_variants_table.php        // Varyantlar
005_create_shop_attributes_table.php              // Özellikler
006_create_shop_product_attributes_table.php      // Ürün-özellik ilişkisi
```

**Özellikler:**
- ✅ Hiyerarşik kategori yapısı
- ✅ JSON çoklu dil desteği
- ✅ Varyant yönetimi (beden, renk, özellik)
- ✅ Dinamik özellik sistemi

---

### B. ÜYELİK SİSTEMİ (3 Tablo)

```
007_create_shop_subscription_plans_table.php      // Üyelik paketleri
008_create_shop_subscriptions_table.php           // Aktif üyelikler
009_create_shop_membership_tiers_table.php        // Seviyeler (Bronze, Silver, Gold)
```

**Özellikler:**
- ✅ Aylık/Yıllık paketler
- ✅ Deneme sürümü (trial)
- ✅ Otomatik yenileme
- ✅ Üyelik seviye sistemi (opsiyonel)

**NOT:** `shop_membership_tiers` opsiyonel - Sadece otomatik seviye sistemi istersen kullan

---

### C. SİPARİŞ SİSTEMİ (5 Tablo)

```
010_create_shop_orders_table.php                  // Siparişler
011_create_shop_order_items_table.php             // Sipariş kalemleri
012_create_shop_order_addresses_table.php         // Sipariş adresleri (snapshot)
013_create_shop_payment_methods_table.php         // Ödeme yöntemleri
014_create_shop_payments_table.php                // Ödeme kayıtları
```

**Özellikler:**
- ✅ Hem ürün hem üyelik siparişi
- ✅ Sipariş durumu (pending, paid, cancelled)
- ✅ Adres snapshot (değişiklik etkilemez)

---

### D. STOK YÖNETİMİ (4 Tablo)

```
015_create_shop_warehouses_table.php              // Depolar
016_create_shop_inventory_table.php               // Stok kayıtları
017_create_shop_stock_movements_table.php         // Stok hareketleri
018_create_shop_price_lists_table.php             // Fiyat listeleri (B2B)
```

**Özellikler:**
- ✅ Çoklu depo desteği
- ✅ Stok takibi (giriş/çıkış)
- ✅ B2B fiyat listeleri

**NOT:** B2B yoksa `shop_price_lists` çıkarılabilir (23 tablo)

---

### E. SEPET SİSTEMİ (2 Tablo)

```
019_create_shop_carts_table.php                   // Sepetler
020_create_shop_cart_items_table.php              // Sepet ürünleri
```

**Özellikler:**
- ✅ Misafir sepet
- ✅ Kullanıcı sepeti
- ✅ Sepet kaydetme (7 gün)

---

### F. VERGİ SİSTEMİ (2 Tablo)

```
021_create_shop_taxes_table.php                   // Vergi tanımları
022_create_shop_tax_rates_table.php               // Vergi oranları
```

**Özellikler:**
- ✅ KDV hesaplama
- ✅ İl bazlı vergi
- ✅ Vergi muafiyeti

---

### G. KUPON & ADRES (2 Tablo)

```
023_create_shop_coupons_table.php                 // Kuponlar
024_create_shop_customer_addresses_table.php      // Müşteri adresleri
```

**Özellikler:**
- ✅ İndirim kuponları
- ✅ Fatura/teslimat adresi

**NOT:** `shop_coupon_usages` Faz 2'ye ertelendi (basit sistemde log gerekmiyor)

---

### H. SİSTEM (1 Tablo)

```
025_create_shop_settings_table.php                // Sistem ayarları
```

---

## 📋 TABLO LİSTESİ ÖZET

```
KATALOG (6)
───────────────────────────────────────────
001 ✅ shop_categories
002 ✅ shop_brands
003 ✅ shop_products
004 ✅ shop_product_variants
005 ✅ shop_attributes
006 ✅ shop_product_attributes

ÜYELİK (3)
───────────────────────────────────────────
007 ✅ shop_subscription_plans
008 ✅ shop_subscriptions
009 🟡 shop_membership_tiers            (opsiyonel)

SİPARİŞ (5)
───────────────────────────────────────────
010 ✅ shop_orders
011 ✅ shop_order_items
012 ✅ shop_order_addresses
013 ✅ shop_payment_methods
014 ✅ shop_payments

STOK (4)
───────────────────────────────────────────
015 ✅ shop_warehouses
016 ✅ shop_inventory
017 ✅ shop_stock_movements
018 🟡 shop_price_lists                 (B2B için)

SEPET (2)
───────────────────────────────────────────
019 ✅ shop_carts
020 ✅ shop_cart_items

VERGİ (2)
───────────────────────────────────────────
021 ✅ shop_taxes
022 ✅ shop_tax_rates

DİĞER (2)
───────────────────────────────────────────
023 ✅ shop_coupons
024 ✅ shop_customer_addresses

SİSTEM (1)
───────────────────────────────────────────
025 ✅ shop_settings

───────────────────────────────────────────
TOPLAM: 25 Tablo
(24 kesin + 1 opsiyonel)
```

---

## ❌ ÇIKARILAN TABLOLAR (41 Tablo)

### Faz 2'ye Ertelenenler (10)
```
→ shop_shipping_methods         // Kargo şimdilik manuel
→ shop_shipments                // Kargo takibi sonra
→ shop_coupon_usages            // Basit sistemde log gerekmiyor
→ shop_product_prices           // price_lists varsa gerekir
→ shop_reviews                  // Yorum sistemi sonra
→ shop_wishlists                // Favori sistemi sonra
→ shop_comparisons              // Karşılaştırma sonra
→ shop_quotes                   // B2B teklif sistemi sonra
→ shop_quote_items              // B2B teklif kalemleri sonra
→ shop_vendors                  // Marketplace sonra
```

### Universal Sistemler (10)
```
❌ shop_notifications           → Universal Notification
❌ shop_email_templates         → Universal Email Template
❌ shop_activity_logs           → ActivityLoggable Trait
❌ shop_seo_redirects           → SeoManagement Modülü
❌ shop_analytics               → Google Analytics
❌ shop_product_views           → HasViewCounter Trait
❌ shop_search_logs             → Universal Search Log
❌ shop_banners                 → WidgetManagement
❌ shop_newsletters             → Universal Newsletter
❌ shop_tags + shop_product_tags → Tags (zaten var)
```

### JSON'da Tutulacaklar (6)
```
❌ shop_product_images          → media_gallery (JSON)
❌ shop_product_videos          → video_url (string)
❌ shop_product_documents       → manual_pdf_url (string)
❌ shop_product_bundles         → bundle_products (JSON)
❌ shop_product_cross_sells     → related_products (JSON)
❌ shop_campaigns               → shop_coupons'la birleştir
```

### Gereksiz/İleri Seviye (15)
```
❌ shop_product_questions       → Universal Comment
❌ shop_product_answers         → Universal Comment
❌ shop_vendor_products         → Marketplace değil
❌ shop_returns                 → İade Faz 3'te
❌ shop_return_items            → İade Faz 3'te
❌ shop_refunds                 → İade Faz 3'te
❌ shop_loyalty_points          → Sadakat Faz 3'te
❌ shop_loyalty_transactions    → Sadakat Faz 3'te
❌ shop_gift_cards              → Hediye kartı sonra
❌ shop_gift_card_transactions  → Hediye kartı sonra
❌ shop_service_requests        → Ticket sistemi ayrı
❌ shop_rental_contracts        → Kiralama özel sektör
❌ shop_customer_groups         → Segmentasyon sonra
❌ shop_customers               → users tablosu var
```

---

## 🔧 OPSİYONEL TABLO KARARLARI

### 1. shop_membership_tiers (Seviye Sistemi)

**Ne İşe Yarar?**
```
Kullanıcı harcamasına göre otomatik seviye kazanır:
- ₺50,000 harcadı → Silver (5% indirim)
- ₺200,000 harcadı → Gold (10% indirim)
```

**Karar:**
- ✅ Otomatik seviye sistemi istiyorsan → TUT
- ❌ Sadece ücretli üyelik varsa → ÇIKAR

**Sonuç:** 25 tablo → 24 tablo

---

### 2. shop_price_lists (B2B Fiyat Listeleri)

**Ne İşe Yarar?**
```
Farklı müşteri grupları için farklı fiyatlar:
- Perakende: ₺100,000
- Bayi: ₺85,000
- VIP: ₺75,000
```

**Karar:**
- ✅ B2B sistem varsa → TUT
- ❌ Tek fiyat varsa → ÇIKAR

**Sonuç:** 25 tablo → 24 tablo (veya 23)

---

## 📂 DOSYA YAPISISI

```
readme/ecommerce/
├── migrations/
│   ├── phase-1/                    ← FAZ 1 (25 dosya)
│   │   ├── 001_create_shop_categories_table.php
│   │   ├── 002_create_shop_brands_table.php
│   │   ├── ...
│   │   └── 025_create_shop_settings_table.php
│   │
│   └── archive/                    ← ARŞİV (41 dosya)
│       ├── phase-2/                // Faz 2'ye ertelenenler
│       ├── universal-systems/      // Universal sistemler
│       └── not-needed/             // Gereksiz olanlar
│
├── PHASE-1-FINAL.md               ← Bu dosya
├── PHASE-PRIORITY.md              ← Faz planı
├── TABLE-ANALYSIS.md              ← 66 tablo analizi
└── FAQ-TABLOLAR.md                ← Soru-cevaplar
```

---

## 🎯 SONRAKI ADIMLAR

### Adım 1: Migration Organizasyonu
```bash
# Phase 1 klasörü oluştur
mkdir -p migrations/phase-1

# 25 migration'ı taşı
mv 001-025 → migrations/phase-1/

# Geri kalanları arşivle
mv 026-066 → migrations/archive/
```

### Adım 2: Shop Modülünü Oluştur
```bash
# Portfolio'yu klonla
php artisan module:make Shop

# Migration'ları kopyala
cp readme/ecommerce/migrations/phase-1/*
   Modules/Shop/Database/migrations/
```

### Adım 3: Model'leri Oluştur
```php
// 5 Universal Trait kullan
use HasReviews, HasViewCounter, HasRatings, HasTags, ActivityLoggable;
```

### Adım 4: Repository Pattern Uygula
```php
// Portfolio pattern'i takip et
CategoryRepository
ProductRepository
SubscriptionRepository
```

### Adım 5: Livewire Component'leri
```php
// Admin sayfaları
CategoryIndex, CategoryForm
ProductIndex, ProductForm
SubscriptionPlanIndex, SubscriptionPlanForm

// Frontend sayfaları
ProductList, ProductDetail
CartPage, CheckoutPage
SubscriptionPage
```

---

## 📊 FAZ KARŞILAŞTIRMA

| Özellik | Eski Plan | Yeni Plan (Faz 1) |
|---------|-----------|-------------------|
| Tablo Sayısı | 66 | 25 |
| Üyelik Satışı | ✅ Faz 1 (9) | ✅ Faz 1 |
| Ürün Kataloğu | ❌ Faz 2 (15) | ✅ Faz 1 |
| Sepet | ❌ Faz 2 | ✅ Faz 1 |
| Stok | ❌ Faz 2 | ✅ Faz 1 |
| İnceleme | ❌ Faz 3 | ❌ Faz 2 |
| B2B Teklif | ❌ Faz 3 | ❌ Faz 2 |
| Kargo | ❌ Faz 4 | ❌ Faz 2 |

---

## ✅ FAZ 1 ÖZELLİKLERİ

### Kullanıcı Ne Yapabilir?

#### 1. Ürün Tarafı
```
✅ Ürünleri listele (kategori, marka filtresi)
✅ Ürün detayına bak
✅ Varyant seç (mast yüksekliği, batarya)
✅ Sepete ekle
✅ Sepet yönetimi
✅ Stok kontrolü
```

#### 2. Üyelik Tarafı
```
✅ Üyelik paketlerini gör (Aylık, Yıllık)
✅ Paket özellikleri karşılaştır
✅ Paket satın al
✅ Kredi kartı ile öde (iyzico)
✅ Deneme sürümü başlat
✅ Üyelik yönet (iptal, duraklatma)
✅ Otomatik yenileme ayarla
```

#### 3. Diğer
```
✅ Kupon kodu kullan
✅ Fatura adresi ekle/düzenle
✅ Sipariş geçmişi gör
✅ KDV dahil/hariç fiyat görüntüle
```

---

## 💰 MALIYET/SÜRE TAHMİNİ

### Geliştirme Süresi (Tam Zamanlı)
```
Migration'lar:          2 gün
Model'ler + Trait:      3 gün
Repository'ler:         3 gün
Admin Livewire:         7 gün
Frontend Livewire:      7 gün
İyzico Entegrasyonu:    3 gün
Test & Debug:           5 gün
────────────────────────────
TOPLAM:                 30 gün (1 ay)
```

### Yarı Zamanlı (Günde 4 saat)
```
TOPLAM:                 60 gün (2 ay)
```

---

## 🎯 BAŞARILI FAZ 1 KRİTERLERİ

### Minimum Viable Product (MVP)
```
✅ Ürünler listelenebiliyor
✅ Sepete eklenebiliyor
✅ Üyelik satın alınabiliyor
✅ Ödeme alınabiliyor
✅ Admin panel çalışıyor
```

### Bonus Özellikler (Zamanında Biterse)
```
🎁 Kupon sistemi aktif
🎁 Stok takibi çalışıyor
🎁 Email bildirimleri
🎁 Fatura oluşturma
```

---

## 📞 ŞİMDİ NE YAPALIM?

**Seçenekler:**

1️⃣ **Migration Organizasyonu Yap**
   → 25 dosyayı phase-1'e taşı
   → 41 dosyayı arşivle

2️⃣ **Shop Modülünü Oluştur**
   → Portfolio'yu klonla
   → Migration'ları taşı
   → Model'leri oluştur

3️⃣ **Daha Detaylı Planlama**
   → Her tablonun seed verilerini hazırla
   → Model relation'ları belirle
   → Repository method'ları listele

**Hangisini yapalım?** 😊
