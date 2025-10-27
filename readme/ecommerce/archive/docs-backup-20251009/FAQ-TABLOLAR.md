# ❓ TABLO SORULARI - CEVAPLAR

## 1️⃣ shop_analytics vs shop_product_views Farkı Nedir?

### shop_product_views (Detaylı Loglama)
**Amaç:** Her görüntülemeyi tek tek kaydet
```php
Schema::create('shop_product_views', function (Blueprint $table) {
    $table->id('view_id');
    $table->foreignId('product_id');
    $table->foreignId('user_id')->nullable();      // Kim baktı?
    $table->string('ip_address');                   // IP
    $table->string('user_agent');                   // Tarayıcı
    $table->string('referrer')->nullable();         // Nereden geldi?
    $table->timestamp('viewed_at');                 // Ne zaman?
});

// Örnek veri:
| view_id | product_id | user_id | ip_address    | viewed_at           |
|---------|------------|---------|---------------|---------------------|
| 1       | 100        | 25      | 192.168.1.1   | 2025-01-10 14:30:00 |
| 2       | 100        | 25      | 192.168.1.1   | 2025-01-10 14:35:00 |
| 3       | 100        | NULL    | 45.67.89.10   | 2025-01-10 15:00:00 |
```

**Avantajları:**
- ✅ Kim, ne zaman, nereden baktı bilgisi
- ✅ Kullanıcı davranışı analizi
- ✅ A/B test yapabilme
- ✅ Detaylı raporlar

**Dezavantajları:**
- ❌ Çok büyür (1 milyon ürün görüntüleme = 1 milyon satır)
- ❌ Performans sorunu (her sayfa yüklemesinde INSERT)
- ❌ Disk alanı tüketir

---

### HasViewCounter Trait (Sayaç)
**Amaç:** Sadece toplam görüntüleme sayısını tut
```php
// shop_products tablosunda
$table->integer('view_count')->default(0);

// Kullanım
$product->incrementViewCount(); // +1 artar

// Örnek veri:
| product_id | title         | view_count |
|------------|---------------|------------|
| 100        | Ürün A        | 15847      |
| 101        | Ürün B        | 8542       |
```

**Avantajları:**
- ✅ Hızlı (sadece UPDATE)
- ✅ Minimal alan (her ürün için 1 integer)
- ✅ Performanslı

**Dezavantajları:**
- ❌ Detay yok (kim, ne zaman, nereden?)
- ❌ Analiz yapılamaz

---

### shop_analytics (Toplu Analitik)
**Amaç:** Günlük/haftalık/aylık özet veriler
```php
Schema::create('shop_analytics', function (Blueprint $table) {
    $table->id('analytics_id');
    $table->string('entity_type');              // Product, Category, Brand
    $table->unsignedBigInteger('entity_id');
    $table->date('date');                       // 2025-01-10
    $table->string('metric_type');              // views, sales, revenue
    $table->integer('metric_value');            // 1500
    $table->json('metadata')->nullable();       // {country, device, source}
});

// Örnek veri:
| analytics_id | entity_type | entity_id | date       | metric_type | metric_value |
|--------------|-------------|-----------|------------|-------------|--------------|
| 1            | Product     | 100       | 2025-01-10 | views       | 1547         |
| 2            | Product     | 100       | 2025-01-10 | sales       | 15           |
| 3            | Product     | 100       | 2025-01-10 | revenue     | 25000        |
| 4            | Category    | 5         | 2025-01-10 | views       | 8542         |
```

**Avantajları:**
- ✅ Çok detaylı (metrik bazlı)
- ✅ Tarih bazlı analiz (trendler)
- ✅ Tüm entity'ler için (Product, Category, Brand)
- ✅ Makul boyut (günlük özet)

---

### 💡 TAVSİYE

**Senin projen için:**

```
❌ shop_product_views      → Çıkar (detay gerekmez)
❌ shop_analytics          → Çıkar (şimdilik gerekmez)
✅ HasViewCounter Trait    → Kullan (basit, hızlı)

// Gelecekte ihtiyaç olursa
→ Google Analytics kullan (ücretsiz, profesyonel)
→ Ya da shop_analytics ekle (custom dashboard için)
```

**Sonuç:** İkisi de aynı değil, farklı amaçlar için:
- **shop_product_views:** Her tıklamayı kaydet (overkill)
- **HasViewCounter:** Sadece sayaç (yeterli)
- **shop_analytics:** Günlük özet + metrikler (ileri seviye)

---

## 2️⃣ shop_campaigns Nedir?

### Kampanya Sistemi
**Amaç:** Promosyon kampanyaları (indirim, hediye, vb.)

```php
Schema::create('shop_campaigns', function (Blueprint $table) {
    $table->id('campaign_id');
    $table->json('title');                      // Kampanya adı
    $table->enum('campaign_type', [
        'discount',      // %20 İndirim
        'bogo',          // Al 1 Öde 1
        'bundle',        // Paket İndirim
        'gift',          // Hediye
        'flash_sale',    // Flaş İndirim
    ]);
    $table->decimal('discount_percentage');     // %20
    $table->timestamp('start_date');
    $table->timestamp('end_date');
    $table->boolean('is_active');
});

// Örnek:
| campaign_id | title                  | type        | discount | start_date | end_date   |
|-------------|------------------------|-------------|----------|------------|------------|
| 1           | Ramazan Kampanyası     | discount    | 15.00    | 2025-03-01 | 2025-03-31 |
| 2           | Black Friday           | flash_sale  | 50.00    | 2025-11-29 | 2025-11-29 |
| 3           | Yılbaşı Hediyesi       | gift        | 0.00     | 2025-12-15 | 2025-12-31 |
```

---

### shop_coupons ile Farkı

**shop_coupons (Kupon Kodu):**
```
Kod: YENI2025
İndirim: %10
Kullanım: Müşteri kodu girer → İndirim uygulanır
```

**shop_campaigns (Otomatik Kampanya):**
```
Kampanya: "Ramazan Kampanyası"
İndirim: %15
Uygulama: Otomatik (belli ürünlere, kategorilere)
Kod gerekmez
```

---

### Birleştirilebilir mi?

**EVET!** shop_coupons tablosunu genişleterek:

```php
Schema::create('shop_coupons', function (Blueprint $table) {
    $table->id('coupon_id');

    // Kupon TİPİ
    $table->enum('type', ['code', 'campaign', 'auto'])
          ->default('code');

    // Kupon kodu (type=code için)
    $table->string('code')->nullable()->unique();

    // Kampanya bilgileri (type=campaign için)
    $table->json('title')->nullable();
    $table->enum('campaign_type', ['discount', 'bogo', 'gift'])->nullable();

    // Ortak alanlar
    $table->decimal('discount_percentage');
    $table->timestamp('start_date');
    $table->timestamp('end_date');
});

// Örnek kullanım:
| coupon_id | type     | code      | title              | discount |
|-----------|----------|-----------|--------------------|---------:|
| 1         | code     | YENI2025  | NULL               | 10.00    |
| 2         | campaign | NULL      | Ramazan Kampanyası | 15.00    |
| 3         | auto     | NULL      | Sepet İndirimi     | 5.00     |
```

**💡 TAVSİYE:** `shop_campaigns` tablosunu çıkar, `shop_coupons` içinde yönet!

---

## 3️⃣ shop_product_bundles Nedir?

### Paket Ürün (Bundle)
**Amaç:** Birden fazla ürünü paket halinde indirimli sat

```
Örnek 1: FORKLIFT PAKETİ
┌─────────────────────────────────────┐
│ 🎁 PAKET: "Başlangıç Seti"         │
│ ─────────────────────────────────── │
│ ✅ CPD15TVL Forklift                │
│ ✅ Yedek Batarya                    │
│ ✅ Şarj İstasyonu                   │
│ ─────────────────────────────────── │
│ Normal Fiyat:  ₺850,000             │
│ Paket Fiyat:   ₺750,000 (%12 İndirim)│
└─────────────────────────────────────┘

Örnek 2: ÜYELİK + ÜRÜN PAKETİ
┌─────────────────────────────────────┐
│ 🎁 PAKET: "Premium Paket"           │
│ ─────────────────────────────────── │
│ ✅ 1 Yıllık Premium Üyelik          │
│ ✅ Forklift Kiralama (3 ay)         │
│ ─────────────────────────────────── │
│ Normal Fiyat:  ₺120,000             │
│ Paket Fiyat:   ₺99,000 (%17 İndirim)│
└─────────────────────────────────────┘
```

---

### Tablo Yapısı

```php
Schema::create('shop_product_bundles', function (Blueprint $table) {
    $table->id('bundle_id');
    $table->json('title');                      // Paket adı
    $table->json('description');
    $table->decimal('bundle_price', 12, 2);     // Paket fiyatı
    $table->decimal('original_price', 12, 2);   // Toplam fiyat
    $table->json('product_ids');                // [100, 105, 120]
    $table->boolean('is_active');
});

// Örnek:
| bundle_id | title              | bundle_price | original_price | product_ids     |
|-----------|--------------------|--------------|----------------|-----------------|
| 1         | Başlangıç Seti     | 750000       | 850000         | [100, 105, 120] |
| 2         | Premium Paket      | 99000        | 120000         | [200, 250]      |
```

---

### JSON'da Tutulabilir mi?

**EVET!** shop_products tablosunda:

```php
// shop_products tablosunda
$table->json('bundle_products')->nullable();

// Örnek veri:
{
  "is_bundle": true,
  "bundle_discount_percentage": 12,
  "products": [
    {"product_id": 100, "quantity": 1, "price": 650000},
    {"product_id": 105, "quantity": 1, "price": 120000},
    {"product_id": 120, "quantity": 1, "price": 80000}
  ],
  "total_price": 850000,
  "bundle_price": 750000
}
```

**💡 TAVSİYE:** Basit paket sistemi için JSON yeterli. Karmaşık paket yönetimi için ayrı tablo.

---

## 4️⃣ ÜYELİK SİSTEMİ (MUTLAKA GEREKLI)

### Senin Projen İçin Gerekli Tablolar

```
✅ shop_subscription_plans      // Paketler (Aylık, Yıllık)
✅ shop_subscriptions           // Aktif üyelikler
🟡 shop_membership_tiers        // Seviyeler (Bronze, Silver, Gold)
```

---

### 1. shop_subscription_plans (Paketler)

```php
Schema::create('shop_subscription_plans', function (Blueprint $table) {
    $table->id('plan_id');
    $table->json('title');                      // Plan adı
    $table->json('description');

    // Fiyat
    $table->decimal('price', 10, 2);            // ₺99
    $table->enum('billing_cycle', ['monthly', 'yearly', 'lifetime']);
    $table->string('currency', 3)->default('TRY');

    // Özellikler
    $table->json('features');                   // ["Sınırsız Teklif", "Öncelikli Destek"]
    $table->integer('max_quotes')->nullable();  // Aylık maksimum teklif sayısı
    $table->boolean('priority_support')->default(false);

    // Deneme
    $table->integer('trial_days')->default(0);  // 7 gün ücretsiz

    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});

// Örnek:
| plan_id | title              | price  | billing_cycle | trial_days | features                          |
|---------|--------------------|--------|---------------|------------|-----------------------------------|
| 1       | {"tr":"Temel"}     | 99.00  | monthly       | 7          | ["10 Teklif/Ay"]                  |
| 2       | {"tr":"Pro"}       | 299.00 | monthly       | 14         | ["Sınırsız Teklif", "Destek"]     |
| 3       | {"tr":"Yıllık"}    | 990.00 | yearly        | 30         | ["Sınırsız", "Öncelik", "%20"]    |
```

---

### 2. shop_subscriptions (Aktif Üyelikler)

```php
Schema::create('shop_subscriptions', function (Blueprint $table) {
    $table->id('subscription_id');
    $table->foreignId('user_id');               // Kullanıcı
    $table->foreignId('plan_id');               // Hangi plan

    // Durum
    $table->enum('status', [
        'trial',        // Deneme sürümü
        'active',       // Aktif
        'cancelled',    // İptal edildi (dönem sonuna kadar aktif)
        'expired',      // Süresi doldu
        'paused'        // Donduruldu
    ])->default('trial');

    // Tarihler
    $table->timestamp('trial_ends_at')->nullable();
    $table->timestamp('current_period_start');
    $table->timestamp('current_period_end');
    $table->timestamp('cancelled_at')->nullable();

    // Ödeme
    $table->boolean('auto_renew')->default(true);
    $table->string('payment_method')->nullable(); // iyzico, stripe
    $table->string('payment_token')->nullable();  // Kart token

    $table->timestamps();
});

// Örnek:
| subscription_id | user_id | plan_id | status | current_period_start | current_period_end | auto_renew |
|-----------------|---------|---------|--------|----------------------|--------------------|------------|
| 1               | 100     | 2       | active | 2025-01-01           | 2025-02-01         | true       |
| 2               | 105     | 3       | trial  | 2025-01-10           | 2025-02-10         | true       |
```

---

### 3. shop_membership_tiers (Opsiyonel - Seviyeler)

**Bu gereklimi?** Sadece farklı seviyeler varsa

```php
Schema::create('shop_membership_tiers', function (Blueprint $table) {
    $table->id('tier_id');
    $table->json('title');                      // Bronze, Silver, Gold

    // Koşullar
    $table->decimal('min_annual_spending')->nullable();  // Yıllık ₺50,000 harcama
    $table->integer('min_orders')->nullable();            // 10 sipariş

    // Avantajlar
    $table->decimal('discount_percentage')->default(0);   // %5 indirim
    $table->integer('loyalty_points_multiplier')->default(1); // 2x puan
    $table->boolean('free_shipping')->default(false);

    $table->integer('sort_order')->default(0);
    $table->timestamps();
});

// Örnek:
| tier_id | title              | min_spending | discount | loyalty_multiplier |
|---------|--------------------|--------------|----------|-------------------|
| 1       | {"tr":"Bronze"}    | 0            | 0.00     | 1                 |
| 2       | {"tr":"Silver"}    | 50000        | 5.00     | 1.5               |
| 3       | {"tr":"Gold"}      | 200000       | 10.00    | 2                 |
| 4       | {"tr":"Platinum"}  | 500000       | 15.00    | 3                 |
```

**Fark:**
- **subscription_plans:** Kullanıcı satın alır (aylık ₺99)
- **membership_tiers:** Otomatik kazanılır (₺50,000 harcadı → Silver)

---

## 💡 SONUÇ

### Senin Projen İçin TAVSİYE

```
✅ MUTLAKA GEREKLI (ÜYELİK)
- shop_subscription_plans
- shop_subscriptions

🟡 OPSİYONEL
- shop_membership_tiers (sadece otomatik seviye sistemi varsa)

❌ ÇIKARILMALI
- shop_product_views (HasViewCounter yeterli)
- shop_analytics (Google Analytics kullan)
- shop_campaigns (shop_coupons'a entegre et)
- shop_product_bundles (JSON'da tut veya basit paket için)
```

**İlk fazda odaklanılacaklar:**
1. ✅ Üyelik planları oluşturma
2. ✅ Üyelik satın alma (iyzico entegrasyonu)
3. ✅ Otomatik yenileme
4. ✅ İptal/dondurma
5. ✅ Deneme sürümü

Başka sorun var mı?
