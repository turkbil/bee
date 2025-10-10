# 🎯 SHOP MODULE - MASTER PLAN

## 📍 MEVCUT DURUM (OCAK 2025)

✅ **TAMAMLANDI:**
- 66 Migration dosyası oluşturuldu (`readme/ecommerce/migrations/`)
- Portfolio pattern seçildi (89 dosya, kategori hiyerarşisi, repository, Livewire)
- Üyelik sistemi **ödeme bazlı** güncellendi (Spotify/Netflix modeli)
- 5 Ürün JSON dosyası hazır (CPD15TVL, CPD18TVL, CPD20TVL, EST122, F4)
- SQL INSERT dosyaları hazır (kategoriler + markalar)

✅ **TAMAMLANDI (10 Ocak 2025):**
- 66 Migration dosyası standardize edildi (Portfolio pattern'e göre)
- name → title, string slug → json slug dönüşümü yapıldı
- SEO kolonları kaldırıldı (Universal SEO'ya geçiş)
- Primary key'ler anlamlı isimler aldı
- JSON slug indexes eklendi (MySQL 8.0+ / MariaDB 10.5+)
- Sektörel comment'ler genel örneklere çevrildi
- "vs." ile dinamik dil desteği eklendi

⏳ **ŞİMDİ:**
- Portfolio → Shop modülü klonlama
- Migration'ları Modules/Shop'a taşıma

🎯 **HEDEF:**
- Forklift satışı (B2B, teklif sistemi, kapora)
- Üyelik satışı (aylık/yıllık, otomatik yenileme)
- Bayilik sistemi (komisyon, ödeme/hesaplaşma)

---

## 📊 66 MİGRATION TABLOSU

### **1. KATALOG** (6 Tablo)
```
001 - shop_categories           // Kategoriler (hiyerarşik, JSON multilang)
002 - shop_brands               // Markalar (iXtif, Toyota, Linde)
003 - shop_products             // Ürünler (CPD15TVL, vb)
004 - shop_product_variants     // Varyantlar (mast yüksekliği, batarya)
005 - shop_attributes           // Özellikler (kapasite, menzil)
006 - shop_product_attributes   // Ürün özellik değerleri
```

### **2. MÜŞTERİ** (3 Tablo)
```
007 - shop_customers            // Müşteri bilgileri
008 - shop_customer_addresses   // Adresler (fatura, kargo)
009 - shop_customer_groups      // Gruplar (VIP, Toptan)
```

### **3. SİPARİŞ** (5 Tablo)
```
010 - shop_orders               // Siparişler
011 - shop_order_items          // Sipariş kalemleri
012 - shop_order_addresses      // Sipariş adresleri (snapshot)
013 - shop_payment_methods      // Ödeme yöntemleri
014 - shop_shipping_methods     // Kargo yöntemleri
```

### **4. ÖDEME & KARGO** (4 Tablo)
```
015 - shop_payments             // Ödeme işlemleri
016 - shop_shipments            // Kargo gönderimleri
017 - shop_warehouses           // Depolar/Lokasyonlar
018 - shop_inventory            // Stok kayıtları
```

### **5. STOK** (3 Tablo)
```
019 - shop_stock_movements      // Stok hareketleri
020 - shop_price_lists          // Fiyat listeleri
021 - shop_product_prices       // Ürün fiyatları (çoklu liste)
```

### **6. PROMOSYON** (5 Tablo)
```
022 - shop_coupons              // Kuponlar
023 - shop_coupon_usages        // Kupon kullanımları
024 - shop_reviews              // Ürün yorumları
025 - shop_wishlists            // Favori listeleri
026 - shop_comparisons          // Karşılaştırma listeleri
```

### **7. SEPET** (3 Tablo)
```
027 - shop_carts                // Sepetler
028 - shop_cart_items           // Sepet ürünleri
029 - shop_taxes                // Vergiler
```

### **8. VERGİ & GÖRSEL** (8 Tablo)
```
030 - shop_tax_rates            // Vergi oranları
031 - shop_product_images       // Ürün görselleri
032 - shop_product_videos       // Ürün videoları
033 - shop_product_documents    // Ürün dokümanları (PDF)
034 - shop_tags                 // Etiketler (universal)
035 - shop_product_tags         // Ürün etiketleri
036 - shop_product_questions    // Ürün soruları
037 - shop_product_answers      // Ürün cevapları
```

### **9. SİSTEM** (3 Tablo)
```
038 - shop_notifications        // Bildirimler
039 - shop_email_templates      // E-posta şablonları
040 - shop_activity_logs        // İşlem kayıtları
```

### **10. BAYİLİK** (5 Tablo)
```
041 - shop_vendors              // Bayiler
042 - shop_vendor_products      // Bayi ürünleri
043 - shop_subscription_plans   // Üyelik paketleri (Bronze, Premium)
044 - shop_subscriptions        // Üyelikler (ÖDEME BAZLI)
045 - shop_membership_tiers     // Seviyeler (bonus özellikler)
```

### **11. SADAKAT** (2 Tablo)
```
046 - shop_loyalty_points       // Sadakat puanları
047 - shop_loyalty_transactions // Puan hareketleri
```

### **12. SERVİS & KİRALAMA** (3 Tablo)
```
048 - shop_service_requests     // Servis talepleri
049 - shop_rental_contracts     // Kiralama sözleşmeleri
050 - shop_quotes               // Teklifler
```

### **13. TEKLİF & İADE** (6 Tablo)
```
051 - shop_quote_items          // Teklif kalemleri
052 - shop_returns              // İadeler
053 - shop_return_items         // İade kalemleri
054 - shop_refunds              // Geri ödemeler
055 - shop_product_bundles      // Ürün paketleri
056 - shop_product_cross_sells  // Çapraz satış önerileri
```

### **14. SEO & KAMPANYA** (5 Tablo)
```
057 - shop_seo_redirects        // SEO yönlendirmeleri
058 - shop_banners              // Banner'lar
059 - shop_campaigns            // Kampanyalar
060 - shop_analytics            // Analitik verileri
061 - shop_product_views        // Ürün görüntüleme sayaçları
```

### **15. ARAMA & HEDİYE** (5 Tablo)
```
062 - shop_search_logs          // Arama kayıtları
063 - shop_gift_cards           // Hediye kartları
064 - shop_gift_card_transactions // Hediye kartı hareketleri
065 - shop_newsletters          // Newsletter kayıtları
066 - shop_settings             // Modül ayarları
```

---

## 💳 ÜYELİK SİSTEMİ (ÖDEME BAZLI)

### **Kavram:**
Netflix/Spotify gibi → Para ver, içerik aç. Para verme, içerik kilitle.

### **Tablolar:**
- `shop_subscription_plans` → Paketler (Bronze: ₺99/ay, Premium: ₺299/ay)
- `shop_subscriptions` → Aktif üyelikler (starts_at, ends_at, auto_renew)
- `shop_membership_tiers` → Bonus özellikler (VIP destek, indirim kuponu)

### **Model Methods:**

**Customer.php:**
```php
public function hasActiveSubscription() {
    return $this->subscriptions()
        ->where('starts_at', '<=', now())
        ->where('ends_at', '>=', now())
        ->exists();
}

public function isPremium() {
    return $this->activeSubscription?->plan->slug === 'premium-uyelik';
}
```

**Subscription.php:**
```php
public function isActive() {
    return $this->starts_at <= now() && $this->ends_at >= now();
}

public function isExpired() {
    return $this->ends_at < now();
}

public function renew() {
    if ($this->billing_cycle === 'monthly') {
        $this->ends_at = $this->ends_at->addMonth();
    } else {
        $this->ends_at = $this->ends_at->addYear();
    }
    $this->save();
}
```

### **Middleware:**

**SubscriptionMiddleware.php:**
```php
public function handle(Request $request, Closure $next, $requiredPlan = null)
{
    if (!auth()->check()) {
        return redirect()->route('login')
            ->with('error', 'Bu içeriği görmek için giriş yapmalısınız.');
    }

    $subscription = auth()->user()->customer->activeSubscription();

    if (!$subscription) {
        return redirect()->route('shop.subscriptions.plans')
            ->with('error', 'Bu içeriği görmek için üyelik gereklidir.');
    }

    if ($subscription->isExpired()) {
        return redirect()->route('shop.subscriptions.renew')
            ->with('error', 'Üyeliğinizin süresi dolmuş.');
    }

    if ($requiredPlan && !$subscription->hasPlan($requiredPlan)) {
        return redirect()->route('shop.subscriptions.upgrade')
            ->with('error', 'Bu içerik için Premium üyelik gereklidir.');
    }

    return $next($request);
}
```

### **Route Örneği:**
```php
// Temel üyelik gereken içerik
Route::middleware(['auth', 'subscription'])->group(function () {
    Route::get('/music/play/{id}', [MusicController::class, 'play']);
});

// Premium üyelik gereken içerik
Route::middleware(['auth', 'subscription:premium'])->group(function () {
    Route::get('/music/download/{id}', [MusicController::class, 'download']);
});
```

### **Cron Jobs:**
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Her gün 02:00'de üyelikleri kontrol et ve yenile
    $schedule->command('subscriptions:renew')->dailyAt('02:00');

    // Her gün 10:00'da bitecek üyelikler için hatırlatma
    $schedule->command('subscriptions:remind')->dailyAt('10:00');
}
```

---

## 🎨 UNIVERSAL TRAITS (5 ADET)

### **1. HasReviews** (Yorum Sistemi)
```php
trait HasReviews
{
    public function reviews() {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function approvedReviews() {
        return $this->reviews()->where('is_approved', true);
    }

    public function averageRating() {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    public function ratingCount() {
        return $this->approvedReviews()->count();
    }

    public function updateRatingCache() {
        $this->rating_average = $this->averageRating();
        $this->rating_count = $this->ratingCount();
        $this->save();
    }
}
```

### **2. HasViewCounter** (Sayaç Sistemi)
```php
trait HasViewCounter
{
    public function incrementViews() {
        $this->increment('view_count');

        ProductView::create([
            'product_id' => $this->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function incrementCartAdds() {
        $this->increment('cart_add_count');
    }

    public function incrementSales($quantity = 1) {
        $this->increment('sales_count', $quantity);
    }
}
```

### **3. HasRatings** (Detaylı Puanlama)
```php
trait HasRatings
{
    public function overallRating() {
        return $this->rating_average;
    }

    public function qualityRating() {
        return $this->reviews()->avg('quality_rating');
    }

    public function deliveryRating() {
        return $this->reviews()->avg('delivery_rating');
    }

    public function valueRating() {
        return $this->reviews()->avg('value_rating');
    }
}
```

### **4. HasTags** (Etiket Sistemi)
```php
trait HasTags
{
    public function tags() {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function syncTags(array $tags) {
        $tagIds = [];

        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $tagIds[] = $tag->id;
        }

        $this->tags()->sync($tagIds);
    }

    public function hasTag($tagName) {
        return $this->tags()->where('name', $tagName)->exists();
    }
}
```

### **5. ActivityLoggable** (Observer Pattern)
```php
// app/Observers/ActivityLoggableObserver.php
class ActivityLoggableObserver
{
    public function created($model) {
        ActivityLog::create([
            'loggable_type' => get_class($model),
            'loggable_id' => $model->id,
            'action' => 'created',
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
        ]);
    }

    public function updated($model) {
        ActivityLog::create([
            'loggable_type' => get_class($model),
            'loggable_id' => $model->id,
            'action' => 'updated',
            'user_id' => auth()->id(),
            'changes' => $model->getChanges(),
        ]);
    }
}
```

**NOT: SEO Yönetimi**
- SEO ayarları tablo bazlı değil, Universal SEO sistemi ile yönetilir
- SeoManagement modülü üzerinden tüm entity'lere (Product, Category, Brand, vb.) SEO desteği sağlanır
- Model'larda SEO kolonları bulunmaz (seo_title, seo_description, seo_keywords)

---

## 📋 11 FAZLI GELİŞTİRME PLANI

### **PHASE 1: Modül Oluşturma** (30 dk) ⏳ ŞİMDİ
- Portfolio → Shop klonlama
- Migration'ları taşıma
- Laravel formatına çevirme
- İlk migrate

### **PHASE 2: Temel Modeller** (3-4 saat)
- 9 Model: Category, Brand, Tag, Attribute, Warehouse, Product, ProductVariant, ProductImage, Customer
- Relations, casts, scopes, methods
- Traits ekleme (HasReviews, HasViewCounter, HasTags, HasRatings, ActivityLoggable)

### **PHASE 3: Repository Pattern** (4-5 saat)
- Interface + Implementation
- ProductRepository, CategoryRepository, CustomerRepository, OrderRepository
- Service Provider binding'leri

### **PHASE 4: Traits** (2-3 saat)
- 5 Universal trait implementasyonu (HasReviews, HasViewCounter, HasRatings, HasTags, ActivityLoggable)
- ActivityLoggableObserver

### **PHASE 5: Routes** (2-3 saat)
- Admin routes (category, brand, product, customer, order)
- Site routes (listing, detail, cart, subscription)
- SubscriptionMiddleware

### **PHASE 6: Üyelik Sistemi** (3-4 saat)
- Subscription/SubscriptionPlan model methods
- SubscriptionController
- Payment Gateway (iyzico/PayTR/Stripe)
- Cron Jobs
- Email templates

### **PHASE 7: Livewire Admin** (1 hafta)
- ProductList, ProductCreate, ProductEdit
- VariantManager, ImageManager
- CategoryTree, CategoryForm
- OrderList, OrderDetail

### **PHASE 8: Frontend** (2 hafta)
- Product Listing, Product Detail
- Cart & Checkout
- My Account (orders, addresses, wishlist, subscription)
- Search, Subscription Pages

### **PHASE 9: Test & Optimizasyon** (3-4 gün)
- Unit tests (model, repository, trait)
- Feature tests (route, controller, livewire)
- Browser tests (Dusk)
- Performance (eager loading, cache, index)
- Security audit

### **PHASE 10: Dokümantasyon** (1-2 gün)
- README.md
- API Documentation
- User guide

### **PHASE 11: Deployment** (1 gün)
- Production deployment
- Final testing
- Security check

---

## 🚀 SONRAKI ADIMLAR

**ŞİMDİ:**
```bash
cd /Users/nurullah/Desktop/cms/laravel
./module.sh
# Seçim: 1 (Portfolio)
# Modül adı: Shop
```

**SONRA:**
1. Migration dosyalarını taşı
2. Laravel formatına çevir (`2025_01_10_000001_...`)
3. `php artisan migrate`
4. İlk commit

**TAHMINI SÜRE:** 4-5 hafta toplam

---

## 📊 ÖZET

✅ **66 Migration** hazır ve standardize edildi
✅ **Portfolio Pattern** seçildi (kategori + repository + Livewire)
✅ **Üyelik Sistemi** ödeme bazlı güncellendi
✅ **5 Universal Trait** planlandı (HasSEO kaldırıldı, Universal SEO kullanılacak)
✅ **11 Fazlı Plan** oluşturuldu
⏳ **Şimdi:** Module.sh ile klonlama

**HEDEF:** Forklift + Üyelik + Bayilik için eksiksiz e-ticaret modülü
