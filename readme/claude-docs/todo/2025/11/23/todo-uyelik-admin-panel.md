# Üyelik Sistemi - Admin Panel TODO

## Tarih: 2025-11-23
## Versiyon: 1
## HTML Taslak: https://ixtif.com/readme/2025/11/23/uyelik-sistemi-analiz/

---

## ÖZET

### Yapı Kararı
- **Ana Modül:** UserManagement (mevcut modüle entegre)
- **Dashboard:** Ana admin dashboard (routes/admin)

### Oluşturulacak Dosyalar
- **6 yeni Livewire component**
- **6 yeni blade view**
- **2 güncelleme (mevcut component)**
- **1 dashboard widget**
- **Route tanımlamaları**

---

## AŞAMA 1: ABONELİK PLAN YÖNETİMİ

### Livewire Components
- [ ] `Modules/UserManagement/app/Http/Livewire/SubscriptionPlanComponent.php`
  - Liste görünümü
  - Arama, filtreleme
  - Toplu işlemler
  - Sıralama

- [ ] `Modules/UserManagement/app/Http/Livewire/SubscriptionPlanManageComponent.php`
  - Create/Edit form
  - JSON özellikler editörü
  - Validasyon

### Blade Views
- [ ] `Modules/UserManagement/resources/views/livewire/subscription-plan-component.blade.php`
- [ ] `Modules/UserManagement/resources/views/livewire/subscription-plan-manage-component.blade.php`

### Routes
```php
// Modules/UserManagement/routes/admin.php
Route::prefix('subscription-plans')->name('subscription-plans.')->group(function () {
    Route::get('/', SubscriptionPlanComponent::class)->name('index');
    Route::get('/create', SubscriptionPlanManageComponent::class)->name('create');
    Route::get('/{id}/edit', SubscriptionPlanManageComponent::class)->name('edit');
});
```

### Model İlişkileri (app/Models/SubscriptionPlan.php)
- [ ] `subscriptions()` - hasMany
- [ ] `scopeActive()` - query scope
- [ ] `scopeFeatured()` - query scope

---

## AŞAMA 2: KUPON YÖNETİMİ

### Livewire Components
- [ ] `Modules/UserManagement/app/Http/Livewire/CouponComponent.php`
  - Liste görünümü
  - Kullanım istatistikleri
  - Durum filtreleme (aktif/süresi dolmuş/limit dolmuş)

- [ ] `Modules/UserManagement/app/Http/Livewire/CouponManageComponent.php`
  - Create/Edit form
  - İndirim tipi seçimi (yüzde/sabit)
  - Tarih aralığı
  - Plan seçimi

### Blade Views
- [ ] `Modules/UserManagement/resources/views/livewire/coupon-component.blade.php`
- [ ] `Modules/UserManagement/resources/views/livewire/coupon-manage-component.blade.php`

### Routes
```php
Route::prefix('coupons')->name('coupons.')->group(function () {
    Route::get('/', CouponComponent::class)->name('index');
    Route::get('/create', CouponManageComponent::class)->name('create');
    Route::get('/{id}/edit', CouponManageComponent::class)->name('edit');
});
```

### Model İlişkileri (app/Models/Coupon.php)
- [ ] `usages()` - hasMany
- [ ] `plans()` - belongsToMany (pivot: coupon_plan)
- [ ] `scopeValid()` - aktif + tarih geçerli + limit dolmamış
- [ ] `isValid()` - tek kupon kontrolü
- [ ] `apply($amount)` - indirim hesaplama

---

## AŞAMA 3: ABONELİK LİSTESİ

### Livewire Component
- [ ] `Modules/UserManagement/app/Http/Livewire/SubscriptionComponent.php`
  - Kullanıcı aboneliklerini listeleme
  - Durum filtreleme (aktif/deneme/iptal/süresi dolmuş)
  - Plan filtreleme
  - Tarih aralığı filtreleme
  - Detay modal

### Blade View
- [ ] `Modules/UserManagement/resources/views/livewire/subscription-component.blade.php`

### Routes
```php
Route::get('/subscriptions', SubscriptionComponent::class)->name('subscriptions.index');
```

### Model İlişkileri (app/Models/Subscription.php)
- [ ] `user()` - belongsTo
- [ ] `plan()` - belongsTo
- [ ] `payments()` - hasMany
- [ ] `scopeActive()` - aktif abonelikler
- [ ] `scopeTrial()` - deneme süresinde
- [ ] `scopeExpiringSoon($days)` - yakında bitecek
- [ ] `isActive()` - bool
- [ ] `daysRemaining()` - kalan gün
- [ ] `cancel()` - iptal işlemi

---

## AŞAMA 4: KULLANICI LİSTESİ GÜNCELLEMESİ

### Mevcut Component Güncelleme
- [ ] `Modules/UserManagement/app/Http/Livewire/UserComponent.php`

  **Yeni Kolonlar:**
  - Abonelik durumu (plan adı + kalan gün)
  - Cihaz kullanımı (aktif/limit)
  - Onay durumu

  **Yeni Filtreler:**
  - Abonelik durumu (aktif/deneme/yok)
  - Onay durumu (onaylı/bekliyor)
  - Kurumsal/bireysel

  **Toplu İşlemler:**
  - Toplu onay
  - Toplu abonelik atama
  - Toplu cihaz limiti değiştirme

### Blade View Güncelleme
- [ ] `Modules/UserManagement/resources/views/livewire/user-component.blade.php`

---

## AŞAMA 5: KULLANICI DETAY SEKMELERİ

### Mevcut Component Güncelleme
- [ ] `Modules/UserManagement/app/Http/Livewire/UserManageComponent.php`

  **Yeni Sekmeler:**

  1. **Cihazlar Sekmesi**
     - Aktif oturumlar listesi (sessions tablosundan)
     - Cihaz bilgisi (user agent parse)
     - IP adresi + konum
     - Son aktivite
     - Tekli/toplu çıkış yapma

  2. **Abonelik Sekmesi**
     - Mevcut abonelik bilgisi
     - Plan değiştirme
     - İptal etme
     - Abonelik geçmişi
     - Ödeme geçmişi

  3. **Giriş Logları Sekmesi**
     - activity_log tablosundan
     - Tarih, IP, cihaz, durum (başarılı/başarısız)
     - Filtreleme

  **Yeni Alanlar (Genel Sekme):**
  - device_limit (null = settings'den)
  - is_approved (checkbox)
  - is_corporate (checkbox)
  - corporate_code (readonly, auto-generate)
  - two_factor_enabled (checkbox)
  - two_factor_phone

### Blade View Güncelleme
- [ ] `Modules/UserManagement/resources/views/livewire/user-manage-component.blade.php`

---

## AŞAMA 6: DASHBOARD WIDGET

### Livewire Component
- [ ] `app/Http/Livewire/Admin/SubscriptionStatsWidget.php`

  **İstatistikler:**
  - Aktif abonelik sayısı
  - Deneme süresindeki kullanıcı sayısı
  - Bu ay gelir
  - Yenileme bekleyen abonelikler
  - Son 5 abonelik
  - Plan dağılımı (pie chart data)

### Blade View
- [ ] `resources/views/livewire/admin/subscription-stats-widget.blade.php`

### Dashboard Entegrasyonu
- [ ] `resources/views/admin/dashboard.blade.php`
  - Widget'ı include et
  - Koşullu gösterim (setting: auth_subscription_paid_enabled)

---

## AŞAMA 7: MENU ENTEGRASYONU

### Admin Menü Güncelleme
- [ ] `Modules/UserManagement/resources/views/admin/partials/sidebar.blade.php` veya MenuManagement

**Yeni Menü Öğeleri:**
```
Kullanıcı Yönetimi
├── Kullanıcılar (mevcut)
├── Roller (mevcut)
├── İzinler (mevcut)
├── Abonelik Planları (YENİ)
├── Abonelikler (YENİ)
└── Kuponlar (YENİ)
```

---

## AŞAMA 8: LANG DOSYALARI

### Türkçe Dil Dosyası
- [ ] `Modules/UserManagement/lang/tr/subscription.php`

```php
return [
    'plans' => [
        'title' => 'Abonelik Planları',
        'create' => 'Yeni Plan',
        'edit' => 'Plan Düzenle',
        // ...
    ],
    'subscriptions' => [
        'title' => 'Abonelikler',
        'status' => [
            'active' => 'Aktif',
            'trial' => 'Deneme',
            'expired' => 'Süresi Doldu',
            'cancelled' => 'İptal',
        ],
        // ...
    ],
    'coupons' => [
        'title' => 'Kuponlar',
        'code' => 'Kupon Kodu',
        'discount_type' => 'İndirim Tipi',
        'percent' => 'Yüzde',
        'fixed' => 'Sabit Tutar',
        // ...
    ],
];
```

---

## KOMUTLAR

```bash
# View cache temizle
php artisan view:clear

# Config cache temizle
php artisan config:clear

# Route cache temizle
php artisan route:clear

# OPcache reset
curl -s -k https://ixtif.com/opcache-reset.php
```

---

## TEST ADIMLARI

### Abonelik Plan Yönetimi
- [ ] Plan oluşturma
- [ ] Plan düzenleme
- [ ] Plan silme (abonesi varsa uyarı)
- [ ] Plan aktivasyon toggle

### Kupon Yönetimi
- [ ] Kupon oluşturma (yüzde)
- [ ] Kupon oluşturma (sabit)
- [ ] Kupon kullanım limiti kontrolü
- [ ] Kupon tarih kontrolü
- [ ] Kupon plan kısıtlaması

### Abonelik Listesi
- [ ] Filtreleme çalışıyor mu
- [ ] Detay modal açılıyor mu
- [ ] İptal işlemi

### Kullanıcı Listesi
- [ ] Yeni kolonlar görünüyor mu
- [ ] Yeni filtreler çalışıyor mu
- [ ] Toplu işlemler

### Kullanıcı Detay
- [ ] Cihazlar sekmesi
- [ ] Abonelik sekmesi
- [ ] Giriş logları sekmesi
- [ ] Yeni alanlar kaydediliyor mu

### Dashboard
- [ ] Widget görünüyor mu
- [ ] İstatistikler doğru mu

---

## NOTLAR

### Önemli Noktalar
1. Tüm component'ler mevcut UserManagement pattern'ini takip etmeli
2. Bulk actions için mevcut trait'ler kullanılmalı: `WithBulkActions`, `WithBulkActionsQueue`
3. Form validasyonu için Laravel form request veya Livewire rules
4. Tüm text'ler lang dosyasından çekilmeli
5. Permission kontrolü: `@can('manage-subscriptions')`

### Bağımlılıklar
- Backend modeller hazır (SubscriptionPlan, Subscription, Coupon, vb.)
- Servisler hazır (SubscriptionService, CouponService, vb.)
- Settings ayarları hazır (auth_subscription_*, corporate_*)

### Tenant-Aware
- Tüm sorgular otomatik tenant-aware (global scope)
- Settings tenant'a özel değerler döner

---

## ÖNCELIK SIRASI

1. **Abonelik Plan Yönetimi** - Önce planlar tanımlanmalı
2. **Kupon Yönetimi** - Kampanyalar için
3. **Kullanıcı Listesi Güncelleme** - Mevcut listenin zenginleştirilmesi
4. **Kullanıcı Detay Sekmeleri** - Detaylı yönetim
5. **Abonelik Listesi** - Genel bakış
6. **Dashboard Widget** - İstatistikler
7. **Menu Entegrasyonu** - Erişim
8. **Lang Dosyaları** - Çoklu dil

---

## TASLAK ONAY

**HTML Taslak:** https://ixtif.com/readme/2025/11/23/uyelik-sistemi-analiz/

**Onay Bekliyor** - "UYGUNDUR" komutu ile geliştirmeye başlanacak.
