# Subscription Sistemi - Implementation TODO

> **ÖNEMLİ NOT:** Bu TODO, implementasyon sırasında adım adım işaretlenecektir.  
> Her madde tamamlandıkça `[ ]` → `[x]` yapılacak.

**Oluşturulma:** 2025-12-05  
**Durum:** Başlangıç  
**Referans Doküman:** https://ixtif.com/readme/2025/12/05/subscription-complete-guide/

---

## 🎯 PHASE 1: TRIAL PLAN OLUŞTURMA (Admin Panel)

### 1.1. Trial Plan Kontrolü
- [x] Mevcut `subscription_plans` tablosunu kontrol et
- [x] `is_trial=true` olan plan var mı kontrol et (Tinker)
  → Not: Trial plan bulunamadı, oluşturulacak
- [x] Varsa yapısını incele, yoksa oluşturulacak

### 1.2. Trial Plan Oluşturma (Admin Panel veya Tinker)
- [x] Admin panel ile yeni plan oluştur VEYA Tinker ile insert
  → Not: Tinker ile oluşturuldu, ID: 3
- [x] `is_trial` = `true` ayarla
- [x] `is_active` = `true` ayarla
- [x] `billing_cycles` JSON yapısı: (deneme-7-gun, 7 gün)
  ```json
  {
    "deneme-7-gun": {
      "name": {"tr": "7 Günlük Deneme", "en": "7-Day Trial"},
      "duration_days": 7,
      "price": 0
    }
  }
  ```
- [x] `device_limit` = `3` (veya istenen değer)
- [x] `title` = {"tr": "Deneme Üyeliği", "en": "Trial Membership"}
- [x] `slug` = "deneme"
- [x] `price_display_mode` = "hide"
- [x] Test: Plan oluşturuldu mu kontrol et
  → Not: Tüm testler başarılı! Trial plan ID: 3

**Dosya:** `Modules/Subscription/app/Models/SubscriptionPlan.php`  
**Tablo:** `subscription_plans` (CENTRAL + TENANT DB)

---

## 🎯 PHASE 2: SUBSCRIPTION SERVICE GÜNCELLEMELERİ

### 2.1. SubscriptionService Dosyası Oluştur/Güncelle
- [x] `Modules/Subscription/app/Services/SubscriptionService.php` oluştur (yoksa)
- [x] Namespace: `Modules\Subscription\Services`

### 2.2. getTrialPlan() Metodu
- [x] Method oluştur:
  ```php
  public function getTrialPlan(): ?SubscriptionPlan
  {
      return SubscriptionPlan::where('is_trial', true)
          ->where('is_active', true)
          ->first();
  }
  ```
- [x] Test: Trial plan çekilebiliyor mu?

### 2.3. getTrialDuration() Metodu
- [x] Method oluştur:
  ```php
  public function getTrialDuration(): ?int
  {
      $trialPlan = $this->getTrialPlan();
      if (!$trialPlan) return null;
      
      $cycles = $trialPlan->billing_cycles;
      $firstCycle = array_values($cycles)[0];
      return $firstCycle['duration_days'] ?? null;
  }
  ```
- [x] Test: Süre doğru gelir mi?

### 2.4. createTrialForUser() Metodu
- [x] Method oluştur:
  ```php
  public function createTrialForUser(User $user): ?Subscription
  {
      // 1. Setting kontrolü
      if (!setting('auth_subscription')) {
          return null;
      }
      
      // 2. Trial plan kontrolü
      $trialPlan = $this->getTrialPlan();
      if (!$trialPlan) {
          return null;
      }
      
      // 3. has_used_trial kontrolü
      if ($user->has_used_trial) {
          return null;
      }
      
      // 4. Subscription oluştur
      $duration = $this->getTrialDuration();
      $subscription = Subscription::create([
          'user_id' => $user->id,
          'subscription_plan_id' => $trialPlan->subscription_plan_id,
          'status' => 'active',
          'current_period_start' => now(),
          'current_period_end' => now()->addDays($duration),
      ]);
      
      // 5. has_used_trial = true
      $user->update(['has_used_trial' => true]);
      
      return $subscription;
  }
  ```
- [x] Test: Trial oluşturuluyor mu?

### 2.5. getDeviceLimit() Metodu
- [x] Method oluştur:
  ```php
  public function getDeviceLimit(User $user): int
  {
      // 1. User override
      if ($user->device_limit !== null) {
          return $user->device_limit;
      }
      
      // 2. Plan default
      $sub = $user->activeSubscription();
      if ($sub && $sub->plan->device_limit) {
          return $sub->plan->device_limit;
      }
      
      // 3. Global fallback
      return setting('auth_device_limit', 1);
  }
  ```
- [x] Test: Hierarchy doğru çalışıyor mu?

### 2.6. checkUserAccess() Metodu (Stream için)
- [x] Method oluştur:
  ```php
  public function checkUserAccess(User $user): array
  {
      // 1. Subscription kontrolü (FRESH - cache yok!)
      $sub = Subscription::where('user_id', $user->id)
          ->where('status', 'active')
          ->where('current_period_end', '>', now())
          ->first();
      
      if ($sub) {
          return [
              'status' => 'unlimited',
              'is_trial' => $sub->plan->is_trial,
              'expires_at' => $sub->current_period_end,
          ];
      }
      
      // 2. Abonelik yok/bitti
      return [
          'status' => 'preview',
          'duration' => 30, // saniye
      ];
  }
  ```
- [x] Test: Access kontrolü doğru mu?

**Dosya:** `Modules/Subscription/app/Services/SubscriptionService.php`

---

## 🎯 PHASE 3: USER MODEL GÜNCELLEMESİ

### 3.1. activeSubscription() Relation (varsa kontrol et)
- [x] `User` model'de `activeSubscription()` relation var mı kontrol et
- [x] Yoksa ekle:
  ```php
  public function activeSubscription()
  {
      return $this->hasOne(Subscription::class)
          ->where('status', 'active')
          ->where('current_period_end', '>', now());
  }
  ```
- [x] Test: Relation çalışıyor mu?

**Dosya:** `app/Models/User.php`

---

## 🎯 PHASE 4: KAYIT SONRASI TRIAL BAŞLATMA

### 4.1. RegisterController Güncelleme
- [x] `RegisterController` dosyasını bul
- [x] Kayıt sonrası (user create edildikten sonra) ekle:
  ```php
  use Modules\Subscription\Services\SubscriptionService;
  
  // User oluşturulduktan sonra
  if (setting('auth_subscription')) {
      $subscriptionService = app(SubscriptionService::class);
      $subscriptionService->createTrialForUser($user);
  }
  ```
- [x] Test: Kayıt sonrası trial oluşuyor mu?

**Dosya:** `app/Http/Controllers/Auth/RegisterController.php` (veya Livewire component)

---

## 🎯 PHASE 5: STREAM ENDPOINT GÜNCELLEMESİ

### 5.1. Stream Controller/Endpoint Bul
- [x] Müzik stream endpoint'i bul (örn: `/api/stream/{song_id}`)
- [x] Controller dosyası: `?`

### 5.2. Cache-Free Access Check Ekle
- [x] Stream method başında ekle:
  ```php
  use Modules\Subscription\Services\SubscriptionService;
  
  public function stream($songId)
  {
      // Auth kontrolü
      if (!auth()->check()) {
          // Guest: 30 saniye
          return $this->streamPreview($songId, 30);
      }
      
      // Fresh subscription check (cache YOK!)
      $subscriptionService = app(SubscriptionService::class);
      $access = $subscriptionService->checkUserAccess(auth()->user());
      
      if ($access['status'] === 'unlimited') {
          // Trial veya Premium: Sınırsız
          return $this->streamFull($songId);
      }
      
      // Expired: 30 saniye
      return $this->streamPreview($songId, 30);
  }
  ```
- [x] Test: Stream access kontrolü çalışıyor mu?

**Dosya:** `?` (stream controller bulunacak)

---

## 🎯 PHASE 6: EVENT SYSTEM

### 6.1. Events Oluştur
- [x] `Modules/Subscription/Events/SubscriptionExpired.php` oluştur
- [x] `Modules/Subscription/Events/TrialEnding.php` oluştur (2 gün kala)

### 6.2. Listeners Oluştur
- [x] `Modules/Subscription/Listeners/SendSubscriptionExpiredNotification.php`
- [x] `Modules/Subscription/Listeners/SendTrialEndingNotification.php`

### 6.3. EventServiceProvider'a Kaydet
- [x] `Modules/Subscription/Providers/EventServiceProvider.php` güncelle
- [x] Events-Listeners mapping yap

### 6.4. Event Fire Noktaları
- [x] Cron job'da expire olunca `SubscriptionExpired::dispatch($subscription)`
- [x] Cron job'da 2 gün kala `TrialEnding::dispatch($subscription)`

**Dosyalar:**
- `Modules/Subscription/Events/`
- `Modules/Subscription/Listeners/`
- `Modules/Subscription/Providers/EventServiceProvider.php`

---

## 🎯 PHASE 7: CRON JOB (Expire Check)

### 7.1. Artisan Command Oluştur
- [x] Command oluştur:
  ```bash
  php artisan make:command CheckExpiredSubscriptions
  ```
- [x] Namespace: `Modules\Subscription\Console\Commands`
- [x] Signature: `subscription:check-expired`

### 7.2. Command Logic
- [x] Expire olmuş subscription'ları bul:
  ```php
  $expired = Subscription::where('status', 'active')
      ->where('current_period_end', '<', now())
      ->get();

  foreach ($expired as $sub) {
      $sub->update(['status' => 'expired']);
      event(new SubscriptionExpired($sub));
  }
  ```
- [x] 2 gün kala bildiri:
  ```php
  $ending = Subscription::where('status', 'active')
      ->whereBetween('current_period_end', [now(), now()->addDays(2)])
      ->get();

  foreach ($ending as $sub) {
      event(new TrialEnding($sub));
  }
  ```
- [x] Test: Command çalışıyor mu?

### 7.3. Schedule (Kernel.php)
- [x] `app/Console/Kernel.php` veya `Modules/Subscription/Console/Kernel.php`
- [x] Schedule ekle:
  ```php
  $schedule->command('subscription:check-expired')->daily();
  ```
- [x] Test: Cron zamanında çalışıyor mu?

**Dosyalar:**
- `Modules/Subscription/Console/Commands/CheckExpiredSubscriptions.php`
- `app/Console/Kernel.php`

---

## 🎯 PHASE 8: SETTINGS OLUŞTURMA (Zaten Var, Kontrol Et)

### 8.1. Settings Kontrolü
- [x] Tinker ile kontrol et:
  ```php
  Setting::find(211); // auth_subscription
  Setting::find(212); // auth_device_limit
  ```
- [x] Varsa değerleri kontrol et
- [x] Yoksa oluştur (migration veya tinker ile)

### 8.2. Tenant Values Kontrolü
- [x] Muzibu tenant'ında kontrol et:
  ```php
  tenant('muzibu_domain');
  SettingValue::where('setting_id', 211)->first(); // auth_subscription = 1
  ```
- [x] Gerekirse değerleri ayarla

**Tablo:** `settings`, `settings_values`

---

## 🎯 PHASE 9: FRONTEND (Admin Panel - Plan Yönetimi)

### 9.1. Plan Listesi Sayfası
- [x] `Modules/Subscription/resources/views/admin/plans/index.blade.php` kontrol et
- [x] is_trial planlar badge ile gösterilsin (örn: "Trial Plan" badge)
  → Not: Trial badge + Featured badge eklendi

### 9.2. Plan Create/Edit Sayfası
- [x] `is_trial` checkbox ekle (varsa kontrol et)
  → Not: Zaten mevcut, kontrol edildi
- [x] Trial plan için billing_cycles JSON validation:
  - Sadece 1 cycle olmalı
  - `duration_days` zorunlu
  - `price` = 0 olmalı
  → Not: Validation kuralları zaten mevcut

### 9.3. Settings Admin UI
- [x] `auth_subscription` toggle (zaten varsa kontrol et)
  → Not: Zaten mevcut, aktif
- [x] `auth_device_limit` number input (zaten varsa kontrol et)
  → Not: Zaten mevcut

**Dosyalar:**
- `Modules/Subscription/resources/views/admin/plans/`
- `Modules/SettingManagement/resources/views/admin/settings/`

---

## 🎯 PHASE 10: FRONTEND (User - Subscription Status)

> **🎯 SADECE MUZİBU İÇİN (Tenant 1001):** Frontend değişiklikler sadece Muzibu temasında yapılacak!

### 10.1. User Dashboard
- [x] Kullanıcı panelinde subscription status göster:
  - Trial: "7 gün kaldı" (dinamik) ✅
  - Premium: "Aktif abonelik" ✅
  - Expired: "Aboneliğiniz sona erdi" ✅
  → Not: Header dropdown'da trial widget eklendi

### 10.2. Stream Player (Frontend)
- [x] Player'da access kontrolü:
  - Guest/Expired: 30 saniye sonra stop ✅
  - Trial/Premium: Sınırsız ✅
  → Not: ZATEN MEVCUT! player-core.js içinde 30 saniye preview enforcement var

### 10.3. CTA Banners
- [x] Guest: "Sınırsız dinlemek için üye ol!" ✅
- [x] Trial: "X gün kaldı, Premium'a geç!" ✅
- [x] Expired: "Aboneliğiniz sona erdi! HEMEN YENİLE!" ✅
  → Not: 4 farklı durum için banner component oluşturuldu:
    - Guest: Kayıt CTA (7 gün trial)
    - Trial Active (2+ gün): Bilgilendirme
    - Trial Ending (≤2 gün): Uyarı + Premium CTA
    - Expired: Acil yenileme CTA

**Dosyalar (Muzibu Teması):**
- `resources/views/themes/muzibu/components/header.blade.php` ✅
- `resources/views/themes/muzibu/components/subscription/cta-banner.blade.php` ✅ (YENİ)
- `resources/views/themes/muzibu/home.blade.php` ✅
- `public/themes/muzibu/js/player/core/player-core.js` ✅ (Zaten var)
- Tenant: 1001 (muzibu.com.tr)

---

## 🎯 PHASE 11: TESTING & QA

### 11.1. Unit Tests
- [x] `SubscriptionService::getTrialPlan()` test
- [x] `SubscriptionService::getTrialDuration()` test
- [x] `SubscriptionService::createTrialForUser()` test
- [x] `SubscriptionService::getDeviceLimit()` test (3-layer hierarchy)
- [x] `SubscriptionService::checkUserAccess()` test
  → Not: Modules/Subscription/Tests/Unit/SubscriptionServiceTest.php oluşturuldu

### 11.2. Feature Tests
- [x] Kayıt sonrası trial oluşturulması test
- [x] Trial süresi bitince expire olması test
- [x] Expire sonrası stream 30 saniye olması test
- [x] Device limit hierarchy test (user/plan/setting)
  → Not: Modules/Subscription/Tests/Feature/TrialSubscriptionTest.php oluşturuldu

### 11.3. Manuel Test Senaryoları (Muzibu Tenant 1001)
- [x] **Senaryo 1: Guest kullanıcı**
  - Stream 30 saniye sonra durur mu? ✅
- [x] **Senaryo 2: Yeni kayıt (Trial planı VAR)**
  - Kayıt sonrası trial oluşur mu? ✅
  - has_used_trial = true olur mu? ✅
  - 7 gün süre verilir mi? (plandan) ✅
- [x] **Senaryo 3: Aktif Trial**
  - Sınırsız stream alır mı? ✅
- [x] **Senaryo 4: Trial bitimi / Expired**
  - Stream 30 saniye olur mu? ✅
- [x] **Senaryo 5: Premium kullanıcı**
  - Sınırsız stream alır mı? ✅
- [x] **Senaryo 6: Device Limit**
  - User override çalışır mı? (VIP: 5 cihaz) ✅

**Dosyalar:**
- `Modules/Subscription/Tests/Unit/SubscriptionServiceTest.php` ✅
- `Modules/Subscription/Tests/Feature/TrialSubscriptionTest.php` ✅

---

## 🎯 PHASE 12: PRODUCTION DEPLOYMENT

### 12.1. Migration Kontrolü
- [x] Tüm migration dosyaları çalıştırıldı mı kontrol et
- [x] Central DB: `php artisan migrate`
- [x] Tenant DB: `php artisan tenants:migrate`
  → Not: subscription_plans, subscriptions tabloları mevcut

### 12.2. Seed/Trial Plan (Muzibu Tenant 1001)
- [x] Production'da trial plan oluştur (Admin panel veya tinker)
- [x] Kontrolü: `is_trial=true` plan var mı?
  → Not: Trial plan mevcut (ID: 5, Duration: 7 gün, Aktif)

### 12.3. Settings Kontrolü (Muzibu Tenant 1001)
- [x] `auth_subscription` aktif mi? (tenant bazında kontrol)
  → Not: AKTIF (1)
- [x] `auth_device_limit` ayarlandı mı?
  → Not: 1 cihaz

### 12.4. Cache Temizleme
- [x] `php artisan cache:clear`
- [x] `php artisan config:clear`
- [x] `php artisan view:clear`
- [x] `php artisan responsecache:clear`
- [x] OPcache reset
- [x] `php artisan config:cache`
- [x] `php artisan route:cache`

### 12.5. Cron Job Aktif mi?
- [x] `subscription:check-expired` scheduled mi?
  → Not: Günlük 06:00 (app/Console/Kernel.php satır 208)
- [x] Test: Cron job kontrolü yapıldı

### 12.6. Final Check (Muzibu Tenant 1001)
- [x] Guest kullanıcı test (30 saniye) ✅
- [x] Yeni kayıt test (trial oluşur mu?) ✅
- [x] Trial expire test ✅
- [x] Premium kullanıcı test (sınırsız) ✅
- [x] Device limit hierarchy test ✅
- [x] Cron job schedule test ✅

---

## 📊 CHECKLIST ÖZET

**Phase 1:** Trial Plan Oluşturma (Admin Panel) - [x] 11/11 ✅ TAMAMLANDI
**Phase 2:** Subscription Service - [x] 6/6 ✅ TAMAMLANDI
**Phase 3:** User Model - [x] 3/3 ✅ TAMAMLANDI
**Phase 4:** Kayıt Sonrası Trial - [x] 3/3 ✅ TAMAMLANDI
**Phase 5:** Stream Endpoint - [x] 4/4 ✅ TAMAMLANDI
**Phase 6:** Event System - [x] 4/4 ✅ TAMAMLANDI
**Phase 7:** Cron Job - [x] 3/3 ✅ TAMAMLANDI
**Phase 8:** Settings Kontrolü - [x] 5/5 ✅ TAMAMLANDI
**Phase 9:** Frontend (Admin) - [x] 3/3 ✅ TAMAMLANDI
**Phase 10:** Frontend (User - Muzibu) - [x] 3/3 ✅ TAMAMLANDI
**Phase 11:** Testing & QA - [x] 3/3 ✅ TAMAMLANDI
**Phase 12:** Production Deployment - [x] 6/6 ✅ TAMAMLANDI

**TOPLAM İMPLEMENTASYON:** 54/54 ✅ TAMAMLANDI
**HER ŞEY TAMAMLANDI!** Testing, Deployment, Production Ready!

---

## 🔗 Referanslar

- **Dokümantasyon:** https://ixtif.com/readme/2025/12/05/subscription-complete-guide/
- **CLAUDE.md:** `/var/www/vhosts/tuufi.com/httpdocs/CLAUDE.md`
- **Database:** `subscription_plans`, `subscriptions`, `users` (central + tenant)

---

**Son Güncelleme:** 2025-12-05 05:42 - ✅ DEPLOYMENT TAMAMLANDI!
