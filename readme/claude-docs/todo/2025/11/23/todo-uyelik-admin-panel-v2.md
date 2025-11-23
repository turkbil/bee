# Üyelik Admin Panel - TODO v2

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
- [ ] `php artisan module:make Subscription`

### 1.2 Migrations (tenant)
- [ ] `Modules/Subscription/database/migrations/tenant/create_subscription_plans_table.php`
- [ ] `Modules/Subscription/database/migrations/tenant/create_subscriptions_table.php`

### 1.3 Modeller
- [ ] `Modules/Subscription/app/Models/SubscriptionPlan.php`
- [ ] `Modules/Subscription/app/Models/Subscription.php`

### 1.4 Servisler
- [ ] `Modules/Subscription/app/Services/SubscriptionService.php`

### 1.4 Livewire Components
- [ ] `Modules/Subscription/app/Http/Livewire/Admin/SubscriptionPlanComponent.php`
- [ ] `Modules/Subscription/app/Http/Livewire/Admin/SubscriptionPlanManageComponent.php`
- [ ] `Modules/Subscription/app/Http/Livewire/Admin/SubscriptionComponent.php`

### 1.5 Blade Views
- [ ] `Modules/Subscription/resources/views/admin/livewire/subscription-plan-component.blade.php`
- [ ] `Modules/Subscription/resources/views/admin/livewire/subscription-plan-manage-component.blade.php`
- [ ] `Modules/Subscription/resources/views/admin/livewire/subscription-component.blade.php`

### 1.6 Routes
- [ ] `Modules/Subscription/routes/admin.php`

### 1.7 Lang
- [ ] `Modules/Subscription/lang/tr/admin.php`

---

## AŞAMA 2: COUPON MODÜLÜ

### 2.1 Modül Oluştur
- [ ] `php artisan module:make Coupon`

### 2.2 Migrations (tenant)
- [ ] `Modules/Coupon/database/migrations/tenant/create_coupons_table.php`
- [ ] `Modules/Coupon/database/migrations/tenant/create_coupon_usages_table.php`

### 2.3 Modeller
- [ ] `Modules/Coupon/app/Models/Coupon.php`
- [ ] `Modules/Coupon/app/Models/CouponUsage.php`

### 2.4 Servisler
- [ ] `Modules/Coupon/app/Services/CouponService.php`

### 2.4 Livewire Components
- [ ] `Modules/Coupon/app/Http/Livewire/Admin/CouponComponent.php`
- [ ] `Modules/Coupon/app/Http/Livewire/Admin/CouponManageComponent.php`

### 2.5 Blade Views
- [ ] `Modules/Coupon/resources/views/admin/livewire/coupon-component.blade.php`
- [ ] `Modules/Coupon/resources/views/admin/livewire/coupon-manage-component.blade.php`

### 2.6 Routes
- [ ] `Modules/Coupon/routes/admin.php`

### 2.7 Lang
- [ ] `Modules/Coupon/lang/tr/admin.php`

---

## AŞAMA 3: USERMANAGEMENT GÜNCELLEMESİ

### 3.1 UserComponent Güncelle
- [ ] Yeni kolonlar: Abonelik durumu, Cihaz kullanımı
- [ ] Yeni filtreler: Abonelik, Onay durumu

### 3.2 UserManageComponent Yeni Sekmeler
- [ ] Cihazlar sekmesi (sessions tablosu)
- [ ] Abonelik sekmesi (abonelik bilgisi + geçmiş)
- [ ] Giriş logları sekmesi (activity_log)

### 3.3 Yeni Alanlar
- [ ] device_limit
- [ ] is_approved
- [ ] is_corporate
- [ ] two_factor_enabled

---

## AŞAMA 4: DASHBOARD WIDGET

- [ ] `app/Http/Livewire/Admin/SubscriptionStatsWidget.php`
- [ ] `resources/views/livewire/admin/subscription-stats-widget.blade.php`

---

## AŞAMA 5: MENU

- [ ] Abonelik menu grubu (Planlar, Abonelikler)
- [ ] Pazarlama menu grubu (Kuponlar)

---

## AŞAMA 6: ESKİ DOSYALARI TEMİZLE

- [ ] `app/Models/SubscriptionPlan.php` sil (modüle taşındı)
- [ ] `app/Models/Subscription.php` sil (modüle taşındı)
- [ ] `app/Models/Coupon.php` sil (modüle taşındı)
- [ ] `app/Services/Auth/*Service.php` sil (modüllere taşındı)

---

## KOMUTLAR

```bash
# Modül oluştur
php artisan module:make Subscription
php artisan module:make Coupon

# Cache temizle
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

---

## ONAY BEKLİYOR

"UYGUNDUR" ile başla.
