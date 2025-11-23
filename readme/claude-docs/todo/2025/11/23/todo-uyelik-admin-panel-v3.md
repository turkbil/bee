# Üyelik Admin Panel - TODO v3

## Tarih: 2025-11-23
## Yapı: Ayrı Modüller
## Pattern: Modules/Portfolio (sorting, bulk actions, vb.)

---

## ÖZET

| Modül | İçerik |
|-------|--------|
| Subscription | Planlar + Abonelikler + Migrations |
| Coupon | Kuponlar + Migrations |
| UserManagement | Kullanıcı sekmeleri (güncelleme) |

---

## AŞAMA 1: SUBSCRIPTION MODÜLÜ

### 1.1 Modül Oluştur
```bash
php artisan module:make Subscription
```

### 1.2 Migrations (tenant)

**subscription_plans tablosu:**
```php
Schema::create('subscription_plans', function (Blueprint $table) {
    $table->id();
    $table->json('title');
    $table->json('description')->nullable();
    $table->string('slug')->unique();
    $table->decimal('price_monthly', 10, 2)->default(0);
    $table->decimal('price_yearly', 10, 2)->default(0);
    $table->integer('trial_days')->default(0);
    $table->integer('device_limit')->default(1);
    $table->json('features')->nullable();
    $table->boolean('is_featured')->default(false);
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

**subscriptions tablosu:**
```php
Schema::create('subscriptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
    $table->string('subscription_number')->unique();
    $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
    $table->decimal('price_per_cycle', 10, 2);
    $table->timestamp('starts_at');
    $table->timestamp('ends_at')->nullable();
    $table->timestamp('trial_ends_at')->nullable();
    $table->timestamp('cancelled_at')->nullable();
    $table->enum('status', ['active', 'trial', 'expired', 'cancelled'])->default('trial');
    $table->boolean('auto_renew')->default(true);
    $table->timestamps();
});
```

### 1.3 Modeller

**SubscriptionPlan.php:**
- [ ] `subscriptions()` - hasMany
- [ ] `scopeActive($query)`
- [ ] `scopeFeatured($query)`
- [ ] `getTitleAttribute()` - JSON accessor
- [ ] `getDescriptionAttribute()` - JSON accessor

**Subscription.php:**
- [ ] `user()` - belongsTo
- [ ] `plan()` - belongsTo
- [ ] `payments()` - hasMany (Payment modülü)
- [ ] `scopeActive($query)`
- [ ] `scopeTrial($query)`
- [ ] `scopeExpiringSoon($query, $days)`
- [ ] `isActive()` - bool
- [ ] `isTrial()` - bool
- [ ] `daysRemaining()` - int
- [ ] `cancel()` - method
- [ ] Payable interface implement

### 1.4 Servisler

**SubscriptionService.php:**
- [ ] `create($user, $plan, $cycle)`
- [ ] `renew($subscription)`
- [ ] `cancel($subscription)`
- [ ] `changePlan($subscription, $newPlan)`
- [ ] `checkExpiring($days)` - Collection

### 1.5 Livewire Components

**SubscriptionPlanComponent.php:**
- [ ] Liste görünümü
- [ ] Arama (title)
- [ ] Filtreler: Durum (aktif/pasif), Featured
- [ ] Sıralama (drag & drop)
- [ ] Toplu işlemler: Aktif/Pasif yap, Sil
- [ ] WithBulkActions trait

**SubscriptionPlanManageComponent.php:**
- [ ] Create/Edit form
- [ ] JSON title/description (çoklu dil)
- [ ] Features editörü
- [ ] Fiyat alanları
- [ ] Validasyon

**SubscriptionComponent.php:**
- [ ] Liste görünümü
- [ ] Arama (kullanıcı adı, email, subscription_number)
- [ ] Filtreler: Durum, Plan, Tarih aralığı
- [ ] Detay modal
- [ ] İptal işlemi

### 1.6 Blade Views
- [ ] `subscription-plan-component.blade.php`
- [ ] `subscription-plan-manage-component.blade.php`
- [ ] `subscription-component.blade.php`

### 1.7 Routes
```php
// Modules/Subscription/routes/admin.php
Route::prefix('subscription')->name('admin.subscription.')->middleware(['web', 'auth', 'admin'])->group(function () {
    // Plans
    Route::get('/plans', SubscriptionPlanComponent::class)->name('plans.index');
    Route::get('/plans/create', SubscriptionPlanManageComponent::class)->name('plans.create');
    Route::get('/plans/{id}/edit', SubscriptionPlanManageComponent::class)->name('plans.edit');

    // Subscriptions
    Route::get('/', SubscriptionComponent::class)->name('index');
});
```

### 1.8 Lang
```php
// Modules/Subscription/lang/tr/admin.php
return [
    'plans' => [
        'title' => 'Abonelik Planları',
        'create' => 'Yeni Plan',
        'edit' => 'Plan Düzenle',
        'name' => 'Plan Adı',
        'price_monthly' => 'Aylık Fiyat',
        'price_yearly' => 'Yıllık Fiyat',
        'trial_days' => 'Deneme Süresi (gün)',
        'device_limit' => 'Cihaz Limiti',
        'features' => 'Özellikler',
        'is_featured' => 'Öne Çıkan',
        'is_active' => 'Aktif',
    ],
    'subscriptions' => [
        'title' => 'Abonelikler',
        'subscription_number' => 'Abonelik No',
        'user' => 'Kullanıcı',
        'plan' => 'Plan',
        'status' => 'Durum',
        'starts_at' => 'Başlangıç',
        'ends_at' => 'Bitiş',
        'statuses' => [
            'active' => 'Aktif',
            'trial' => 'Deneme',
            'expired' => 'Süresi Doldu',
            'cancelled' => 'İptal',
        ],
    ],
];
```

---

## AŞAMA 2: COUPON MODÜLÜ

### 2.1 Modül Oluştur
```bash
php artisan module:make Coupon
```

### 2.2 Migrations (tenant)

**coupons tablosu:**
```php
Schema::create('coupons', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->json('description')->nullable();
    $table->enum('discount_type', ['percent', 'fixed'])->default('percent');
    $table->decimal('discount_value', 10, 2);
    $table->decimal('min_amount', 10, 2)->nullable();
    $table->decimal('max_discount', 10, 2)->nullable();
    $table->integer('usage_limit')->nullable();
    $table->integer('usage_per_user')->default(1);
    $table->integer('used_count')->default(0);
    $table->timestamp('starts_at')->nullable();
    $table->timestamp('expires_at')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**coupon_usages tablosu:**
```php
Schema::create('coupon_usages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->morphs('usable'); // subscription_id veya order_id
    $table->decimal('discount_amount', 10, 2);
    $table->timestamps();
});
```

### 2.3 Modeller

**Coupon.php:**
- [ ] `usages()` - hasMany
- [ ] `scopeActive($query)`
- [ ] `scopeValid($query)` - aktif + tarih geçerli + limit dolmamış
- [ ] `isValid()` - bool
- [ ] `isUsableBy($user)` - bool
- [ ] `apply($amount)` - decimal (indirim hesapla)
- [ ] `incrementUsage()`

**CouponUsage.php:**
- [ ] `coupon()` - belongsTo
- [ ] `user()` - belongsTo
- [ ] `usable()` - morphTo

### 2.4 Servisler

**CouponService.php:**
- [ ] `validate($code, $user, $amount)`
- [ ] `apply($coupon, $user, $usable, $amount)`
- [ ] `getDiscount($coupon, $amount)`

### 2.5 Livewire Components

**CouponComponent.php:**
- [ ] Liste görünümü
- [ ] Arama (code)
- [ ] Filtreler: Durum (aktif/pasif/süresi dolmuş/limit dolmuş)
- [ ] Kullanım istatistikleri (used_count / usage_limit)
- [ ] Toplu işlemler: Aktif/Pasif yap, Sil
- [ ] WithBulkActions trait

**CouponManageComponent.php:**
- [ ] Create/Edit form
- [ ] İndirim tipi seçimi (yüzde/sabit)
- [ ] Tarih aralığı picker
- [ ] Limit ayarları
- [ ] Validasyon

### 2.6 Blade Views
- [ ] `coupon-component.blade.php`
- [ ] `coupon-manage-component.blade.php`

### 2.7 Routes
```php
// Modules/Coupon/routes/admin.php
Route::prefix('coupon')->name('admin.coupon.')->middleware(['web', 'auth', 'admin'])->group(function () {
    Route::get('/', CouponComponent::class)->name('index');
    Route::get('/create', CouponManageComponent::class)->name('create');
    Route::get('/{id}/edit', CouponManageComponent::class)->name('edit');
});
```

### 2.8 Lang
```php
// Modules/Coupon/lang/tr/admin.php
return [
    'title' => 'Kuponlar',
    'create' => 'Yeni Kupon',
    'edit' => 'Kupon Düzenle',
    'code' => 'Kupon Kodu',
    'discount_type' => 'İndirim Tipi',
    'discount_value' => 'İndirim Değeri',
    'types' => [
        'percent' => 'Yüzde (%)',
        'fixed' => 'Sabit Tutar (₺)',
    ],
    'usage_limit' => 'Kullanım Limiti',
    'usage_per_user' => 'Kullanıcı Başı Limit',
    'used_count' => 'Kullanım Sayısı',
    'starts_at' => 'Başlangıç Tarihi',
    'expires_at' => 'Bitiş Tarihi',
    'min_amount' => 'Min. Tutar',
    'max_discount' => 'Max. İndirim',
    'is_active' => 'Aktif',
];
```

---

## AŞAMA 3: USERMANAGEMENT GÜNCELLEMESİ

### 3.1 UserComponent Güncelle

**Yeni Kolonlar:**
- [ ] Abonelik durumu (plan adı + kalan gün)
- [ ] Cihaz kullanımı (aktif / limit)
- [ ] Onay durumu badge

**Yeni Filtreler:**
- [ ] Abonelik durumu (aktif/deneme/yok)
- [ ] Onay durumu (onaylı/bekliyor)
- [ ] Kurumsal/bireysel

**Toplu İşlemler:**
- [ ] Toplu onay
- [ ] Toplu abonelik atama

### 3.2 UserManageComponent Yeni Sekmeler

**Cihazlar Sekmesi:**
- [ ] sessions tablosundan aktif oturumlar
- [ ] Cihaz bilgisi (user agent parse)
- [ ] IP adresi
- [ ] Son aktivite
- [ ] Tekli/toplu çıkış yapma

**Abonelik Sekmesi:**
- [ ] Mevcut abonelik bilgisi
- [ ] Plan değiştirme
- [ ] İptal etme
- [ ] Abonelik geçmişi

**Giriş Logları Sekmesi:**
- [ ] activity_log tablosundan
- [ ] Tarih, IP, cihaz, durum
- [ ] Filtreleme

**Yeni Alanlar (Genel Sekme):**
- [ ] device_limit input
- [ ] is_approved checkbox
- [ ] is_corporate checkbox
- [ ] corporate_code (readonly)
- [ ] two_factor_enabled checkbox
- [ ] two_factor_phone input

---

## AŞAMA 4: DASHBOARD WIDGET

**SubscriptionStatsWidget.php:**
- [ ] Aktif abonelik sayısı
- [ ] Deneme süresindeki kullanıcı sayısı
- [ ] Bu ay gelir
- [ ] Yenileme bekleyen (7 gün içinde)
- [ ] Son 5 abonelik

**subscription-stats-widget.blade.php:**
- [ ] Card layout
- [ ] İstatistik gösterimi
- [ ] Koşullu gösterim: `setting('auth_subscription_paid_enabled')`

---

## AŞAMA 5: MENU

**MenuManagement'a ekle:**

```
Abonelik (yeni grup)
├── Planlar → admin.subscription.plans.index
└── Abonelikler → admin.subscription.index

Pazarlama (yeni grup)
└── Kuponlar → admin.coupon.index
```

---

## AŞAMA 6: TEMİZLİK

### Silinecek Dosyalar
- [ ] `app/Models/SubscriptionPlan.php`
- [ ] `app/Models/Subscription.php`
- [ ] `app/Models/Coupon.php`
- [ ] `app/Models/CouponUsage.php`
- [ ] `app/Services/Auth/SubscriptionService.php`
- [ ] `app/Services/Auth/CouponService.php`
- [ ] `database/migrations/*subscription*`
- [ ] `database/migrations/*coupon*`
- [ ] `database/migrations/tenant/*subscription*`
- [ ] `database/migrations/tenant/*coupon*`

---

## KOMUTLAR

```bash
# Modül oluştur
php artisan module:make Subscription
php artisan module:make Coupon

# Migration çalıştır
php artisan tenants:migrate

# Cache temizle
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# OPcache reset
curl -s -k https://ixtif.com/opcache-reset.php
```

---

## TEST ADIMLARI

### Subscription Modülü
- [ ] Plan oluşturma
- [ ] Plan düzenleme
- [ ] Plan sıralama (drag & drop)
- [ ] Plan silme
- [ ] Abonelik listeleme
- [ ] Abonelik filtreleme
- [ ] Abonelik iptal

### Coupon Modülü
- [ ] Kupon oluşturma (yüzde)
- [ ] Kupon oluşturma (sabit)
- [ ] Kupon düzenleme
- [ ] Kullanım limiti kontrolü
- [ ] Tarih kontrolü

### UserManagement
- [ ] Yeni kolonlar görünüyor mu
- [ ] Yeni filtreler çalışıyor mu
- [ ] Cihazlar sekmesi
- [ ] Abonelik sekmesi
- [ ] Loglar sekmesi

---

## ONAY BEKLİYOR

"UYGUNDUR" ile başla.
