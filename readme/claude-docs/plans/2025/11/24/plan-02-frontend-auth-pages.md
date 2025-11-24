# Frontend Auth Sayfalar1 - Plan

**Tarih:** 2025-11-24 19:53
**Durum:** Plan
**Öncelik:** Orta

---

## Hedef Sayfalar

1. **Login** - Giri_ sayfas1
2. **Register** - Kay1t sayfas1
3. **Profile** - Profil sayfas1

---

## 1. Login Sayfas1

### Mevcut Durum
- Auth tema tasar1m1 haz1r (40 sayfa)
- Statik blade template

### Eklenecek Özellikler

#### A. Livewire Entegrasyonu
- Email/^ifre giri_i
- "Beni hat1rla" checkbox
- ^ifremi unuttum linki

#### B. Cihaz Limiti Kontrolü
- Login sonras1 cihaz say1s1 kontrolü
- Limit dolmu_sa ’ Cihaz seçme ekran1
- Mevcut cihazlardan birini ç1kar veya devam et

#### C. Hesap Kilidi Kontrolü
- `failed_login_attempts` sayac1
- 5 ba_ar1s1z deneme ’ hesap kilitle
- `locked_until` zaman1 kontrol et

#### D. 2FA Yönlendirme
- `two_factor_enabled` kontrolü
- 2FA aktifse ’ SMS kodu gönder ’ dorulama sayfas1

#### E. Onay Kontrolü
- `is_approved` kontrolü
- Onays1z kullan1c1 ’ "Hesab1n1z onay bekliyor" mesaj1

### Dosyalar
- `resources/views/themes/muzibu/auth/login.blade.php`
- Yeni: `app/Http/Livewire/Auth/LoginComponent.php`

### Servisler
- `app/Services/Auth/DeviceService.php`
- `app/Services/Auth/LoginLogService.php`
- `app/Services/Auth/TwoFactorService.php`

---

## 2. Register Sayfas1

### Mevcut Durum
- Auth tema tasar1m1 haz1r
- Statik blade template

### Eklenecek Özellikler

#### A. Livewire Form
- Ad Soyad
- Email
- ^ifre (tekrar ile)
- Telefon (2FA için)
- Kurumsal kod (opsiyonel)

#### B. Kurumsal Kod ile Kay1t
- Kurumsal kod input alan1
- Kod girilirse ’ `CorporateService::registerWithCode()`
- Kullan1c1 otomatik kuruma balan1r
- parent_id set edilir

#### C. Onay Bekleme Sistemi
- Kay1t sonras1 ’ `is_approved = false`
- Kullan1c1 ’ "Hesab1n1z olu_turuldu, admin onay1 bekleniyor" sayfas1
- Mail gönder ’ Admin'e & Kullan1c1ya

#### D. Trial Abonelik
- setting('auth_registration_auto_trial') kontrolü
- True ise ’ Otomatik trial abonelik olu_tur

### Dosyalar
- `resources/views/themes/muzibu/auth/register.blade.php`
- Yeni: `app/Http/Livewire/Auth/RegisterComponent.php`

### Servisler
- `app/Services/Auth/CorporateService.php`
- `Modules/Subscription/app/Services/SubscriptionService.php`

---

## 3. Profile Sayfas1

### Mevcut Durum
- Muhtemelen mevcut bir profil sayfas1 var

### Eklenecek Özellikler

#### A. Sekmeler
1. **Hesap Bilgileri**
   - Ad Soyad
   - Email
   - Telefon
   - Avatar upload

2. **Güvenlik**
   - ^ifre dei_tir
   - 2FA aç/kapa
   - Telefon numaras1 güncelle

3. **Cihazlar1m**
   - Aktif cihazlar listesi
   - Cihaz ç1karma butonu
   - Son aktivite zaman1

4. **Aboneliim**
   - Mevcut plan
   - Biti_ tarihi
   - Yenileme butonu
   - Plan dei_tir linki

### Dosyalar
- `resources/views/themes/muzibu/profile/index.blade.php`
- Yeni: `app/Http/Livewire/Profile/ProfileComponent.php`
- Yeni: `app/Http/Livewire/Profile/SecurityComponent.php`
- Yeni: `app/Http/Livewire/Profile/DevicesComponent.php`
- Yeni: `app/Http/Livewire/Profile/SubscriptionComponent.php`

---

## Yakla_1m

### Ad1m 1: Livewire Component'leri Olu_tur
- LoginComponent
- RegisterComponent
- ProfileComponent (sekmeli)

### Ad1m 2: View'lar1 Güncelle
- Statik template'leri Livewire'a çevir
- Alpine.js kullan (client-side validasyon)

### Ad1m 3: Servis Entegrasyonu
- DeviceService kullan
- CorporateService kullan
- SubscriptionService kullan
- LoginLogService kullan

### Ad1m 4: Middleware Ekle
- CheckDeviceLimit
- CheckSubscription
- CheckApproval

---

## Teknik Notlar

### Alpine.js Kullan1m1
```blade
<div x-data="{ showPassword: false }">
    <input :type="showPassword ? 'text' : 'password'">
    <button @click="showPassword = !showPassword">
        <i :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
    </button>
</div>
```

### Livewire Form Validation
```php
protected $rules = [
    'email' => 'required|email',
    'password' => 'required|min:8',
];

public function login()
{
    $this->validate();
    // Login logic
}
```

### Session Management
```php
// Cihaz limiti kontrolü
$activeDevices = DeviceService::getActiveDevices(auth()->user());
if ($activeDevices >= $deviceLimit) {
    // Cihaz seçme ekran1
}
```

---

## Beklenen Sonuç

-  Login sayfas1 Livewire entegrasyonu
-  Cihaz limiti kontrolü çal1_1yor
-  2FA ak1_1 çal1_1yor
-  Register sayfas1 Livewire entegrasyonu
-  Kurumsal kodla kay1t çal1_1yor
-  Profile sayfas1 4 sekmeli çal1_1yor

---

**NOT:** Her sayfa için önce HTML taslak haz1rlanacak!
