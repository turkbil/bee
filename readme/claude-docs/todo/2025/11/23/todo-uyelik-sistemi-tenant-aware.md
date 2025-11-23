# Üyelik Sistemi - Tenant-Aware TODO

## Tarih: 2025-11-23
## Versiyon: 14 (Final Plan - Tinker Yaklaşımı)
## HTML Rapor: https://ixtif.com/readme/2025/11/23/uyelik-sistemi-analiz/

---

## 🎉 TAMAMLANMA DURUMU: Backend %100 | UI/UX %30

| Aşama | Durum |
|-------|-------|
| 1. Tablo Rename Migration | ✅ Tamamlandı |
| 2. Users Tablosu Güncelleme | ✅ Tamamlandı |
| 3. Universal Modeller | ✅ Tamamlandı |
| 4. Servisler | ✅ Tamamlandı |
| 5. Settings (DB) | ✅ Tamamlandı |
| 6. Middleware | ✅ Tamamlandı |
| 7. Mail Module | ✅ Tamamlandı |
| 8. Cron Jobs | ✅ Tamamlandı |
| 9. Auth Theme Designs | ✅ 40/40 Tamamlandı |
| 10. Frontend UI/UX | ⏳ 4/12 Devam Ediyor |
| 11. Admin UI/UX | ⏳ 3/4 Devam Ediyor |

**Son Güncelleme:** 2025-11-23
**UI/UX Plan:** https://ixtif.com/readme/2025/11/23/uyelik-ui-plan/

---

## ÖZET

### Değişiklik Yapısı
- **5 tablo RENAME** (shop_ prefix kaldırılacak)
- **0 yeni tablo** (sessions + activity_log kullanılacak)
- **9 yeni kolon** (users tablosuna)
- **1 yeni modül** (Mail - nwidart)

### Kesinleşen Kararlar
| Özellik | Karar |
|---------|-------|
| Ödeme Sistemi | PayTR |
| Oturum Süresi | 1 yıl |
| Fiyat (Muzibu) | Aylık 299 TL / Yıllık 2.999 TL |
| Cihaz Limiti | Kullanıcı bazlı (varsayılan: 1) |
| Deneme Hakkı | Üyelik süresine EKLENİR |
| Cihaz Takibi | sessions tablosu |
| Giriş Logları | activity_log tablosu |
| 2FA | İsteğe bağlı, SMS ile |
| Kupon Sistemi | Universal (mevcut tablo rename) |
| Kurumsal Üyelik | Sınırsız alt hesap (Sadece Muzibu) |

---

## AŞAMA 1: TABLO RENAME MİGRATION ✅

### Migration Dosyası
```
database/migrations/2025_11_23_000001_rename_shop_tables_to_universal.php
database/migrations/tenant/2025_11_23_000001_rename_shop_tables_to_universal.php
```

### Rename İşlemleri
- [x] `shop_subscription_plans` → `subscription_plans`
- [x] `shop_subscriptions` → `subscriptions`
- [x] `shop_coupons` → `coupons`
- [x] `shop_coupon_usages` → `coupon_usages`
- [x] `shop_customer_addresses` → `customer_addresses`

**✅ Migration çalıştırıldı - Tablolar mevcut**

```php
public function up(): void
{
    Schema::rename('shop_subscription_plans', 'subscription_plans');
    Schema::rename('shop_subscriptions', 'subscriptions');
    Schema::rename('shop_coupons', 'coupons');
    Schema::rename('shop_coupon_usages', 'coupon_usages');
    Schema::rename('shop_customer_addresses', 'customer_addresses');
}
```

---

## AŞAMA 2: USERS TABLOSU GÜNCELLEME ✅

### Migration Dosyası
```
database/migrations/2025_11_23_000002_add_membership_fields_to_users_table.php
database/migrations/tenant/2025_11_23_000002_add_membership_fields_to_users_table.php
```

**✅ Migration çalıştırıldı - Tüm kolonlar mevcut**

### Yeni Kolonlar (9 adet)

| Kolon | Tip | Varsayılan | Açıklama |
|-------|-----|------------|----------|
| `device_limit` | integer nullable | null | Kullanıcıya özel cihaz limiti. null ise settings'den default alınır |
| `is_approved` | boolean | true | Manuel onay gerektiren üyelikler için. false ise giriş yapamaz |
| `failed_login_attempts` | integer | 0 | Başarısız giriş sayacı. Belirli sayıdan sonra hesap kilitlenir |
| `locked_until` | timestamp nullable | null | Hesap kilit bitiş zamanı. Bu tarihten önce giriş engelli |
| `two_factor_enabled` | boolean | false | 2FA aktif mi? true ise girişte SMS kodu istenir |
| `two_factor_phone` | string nullable | null | 2FA telefon numarası. Farklı numara kullanılabilir |
| `is_corporate` | boolean | false | Kurumsal ana hesap mı? true ise alt hesap oluşturabilir |
| `corporate_code` | string nullable unique | null | Kurumsal davet kodu: FIRMA-ABC123 |
| `parent_user_id` | foreignId nullable | null | Alt hesaplar için ana hesabın ID'si |

```php
Schema::table('users', function (Blueprint $table) {
    $table->integer('device_limit')->nullable();
    $table->boolean('is_approved')->default(true);
    $table->integer('failed_login_attempts')->default(0);
    $table->timestamp('locked_until')->nullable();
    $table->boolean('two_factor_enabled')->default(false);
    $table->string('two_factor_phone')->nullable();
    $table->boolean('is_corporate')->default(false);
    $table->string('corporate_code')->nullable()->unique();
    $table->foreignId('parent_user_id')->nullable()->constrained('users')->onDelete('set null');
});
```

---

## AŞAMA 3: UNIVERSAL MODELLER ✅

### Model Dosyaları (app/Models/)
- [x] `SubscriptionPlan.php`
- [x] `Subscription.php` (implements Payable)
- [x] `Coupon.php`
- [x] `CouponUsage.php`
- [x] `CustomerAddress.php`

### Subscription Model (Payable Interface)
```php
use Modules\Payment\App\Contracts\Payable;

class Subscription extends Model implements Payable
{
    public function getPayableAmount(): float
    {
        return (float) $this->price_per_cycle;
    }

    public function getPayableDescription(): string
    {
        return "Abonelik #{$this->subscription_number}";
    }

    public function getPayableCustomer(): array
    {
        return [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone ?? '',
            'address' => 'Türkiye'
        ];
    }

    public function getPayableDetails(): ?array
    {
        return [
            'items' => [[
                'name' => $this->plan->title,
                'price' => $this->price_per_cycle,
                'quantity' => 1
            ]]
        ];
    }
}
```

---

## AŞAMA 4: SERVİSLER ✅

### Servis Dosyaları (app/Services/Auth/)
- [x] `DeviceService.php` - sessions tablosu ile cihaz yönetimi
- [x] `LoginLogService.php` - activity_log ile giriş kaydı
- [x] `TwoFactorService.php` - SMS kod gönderme/doğrulama
- [x] `SubscriptionService.php` - Abonelik işlemleri
- [x] `CouponService.php` - Kupon doğrulama/uygulama
- [x] `CorporateService.php` - Kurumsal hesap yönetimi

---

## AŞAMA 5: SETTINGMANAGEMENT (Tinker ile DB'ye) ✅

**✅ 5 grup oluşturuldu, 17 ayar key'i eklendi**

### Veritabanı Yapısı
- `settings_groups` (CENTRAL) → Grup + prefix tanımı
- `settings` (CENTRAL) → Ayar tanımları (key, type, default)
- `settings_values` (TENANT) → Her tenant'ın değerleri

### Grup Hiyerarşisi
Tüm gruplar "Kullanıcı" grubu (ID=3) altında alt grup olarak eklenecek:
```
Kullanıcı (ID=3)
├── Kayıt Ayarları (ID=20, prefix: auth_registration)
├── Oturum Ayarları (ID=21, prefix: auth_session)
├── Güvenlik Ayarları (ID=22, prefix: auth_security)
├── Abonelik Ayarları (ID=23, prefix: auth_subscription)
└── Kurumsal Ayarlar (ID=24, prefix: corporate)
```

### Oluşturulacak Gruplar (5 adet)

| ID | Grup Adı | Parent ID | Prefix | Icon |
|----|----------|-----------|--------|------|
| 20 | Kayıt Ayarları | 3 | auth_registration | fas fa-user-plus |
| 21 | Oturum Ayarları | 3 | auth_session | fas fa-clock |
| 22 | Güvenlik Ayarları | 3 | auth_security | fas fa-shield-alt |
| 23 | Abonelik Ayarları | 3 | auth_subscription | fas fa-credit-card |
| 24 | Kurumsal Ayarlar | 3 | corporate | fas fa-building |

### Ayarlar (Key = prefix_name formatında)

#### auth_registration
- [x] `auth_registration_enabled` (select, 1) - Kayıt Aktif
- [x] `auth_registration_email_verify` (select, 1) - E-posta Doğrulama
- [x] `auth_registration_approval` (select, 0) - Admin Onayı
- [x] `auth_registration_trial_days` (text, 7) - Deneme Süresi (gün)

#### auth_session
- [x] `auth_session_lifetime` (text, 525600) - Oturum Süresi (dk) - 1 yıl
- [x] `auth_session_device_limit` (text, 1) - Cihaz Limiti

#### auth_security
- [x] `auth_security_max_attempts` (text, 5) - Max Giriş Denemesi
- [x] `auth_security_lockout` (text, 30) - Kilitleme Süresi (dk)
- [x] `auth_security_2fa_enabled` (select, 1) - 2FA Aktif
- [x] `auth_security_2fa_expiry` (text, 5) - 2FA Kod Süresi (dk)

#### auth_subscription
- [x] `auth_subscription_paid_enabled` (select, 0) - Ücretli Üyelik
- [x] `auth_subscription_auto_renewal` (select, 1) - Otomatik Yenileme
- [x] `auth_subscription_reminder_days` (text, 7) - Hatırlatma (gün önce)
- [x] `auth_subscription_grace_days` (text, 3) - Tolerans Süresi (gün)

#### corporate
- [x] `corporate_enabled` (select, 0) - Kurumsal Üyelik
- [x] `corporate_max_users` (text, 0) - Max Alt Kullanıcı (0=sınırsız)

### Tinker ile Grup Ekleme
```bash
php artisan tinker
```

```php
// Kayıt Ayarları grubu
DB::table('settings_groups')->insert([
    'id' => 20,
    'name' => 'Kayıt Ayarları',
    'slug' => 'kayit-ayarlari',
    'parent_id' => 3, // Kullanıcı
    'prefix' => 'auth_registration',
    'icon' => 'fas fa-user-plus',
    'created_at' => now(),
    'updated_at' => now()
]);

// Diğer gruplar da aynı şekilde eklenir (ID: 21, 22, 23, 24)
```

### Tinker ile Ayar Ekleme (Örnek)
```php
// Kayıt Aktif ayarı
DB::table('settings')->insert([
    'group_id' => 20,
    'label' => 'Kayıt Aktif',
    'key' => 'auth_registration_enabled',
    'type' => 'select',
    'options' => json_encode(['0' => 'Kapalı', '1' => 'Açık']),
    'default_value' => '1',
    'help' => 'Yeni üye kaydı açık mı?',
    'created_at' => now(),
    'updated_at' => now()
]);
```

### Kodda Kullanım
```php
// Helper ile
$trialDays = setting('auth_registration_trial_days', 7);
$deviceLimit = setting('auth_session_device_limit', 1);
$isPaidEnabled = setting('auth_subscription_paid_enabled', false);

// Her tenant kendi settings_values tablosundan okur
// Değer yoksa default_value kullanılır
```

---

## AŞAMA 6: MIDDLEWARE ✅

### Middleware Dosyaları (app/Http/Middleware/)
- [x] `CheckDeviceLimit.php` (device.limit) - Cihaz limitini kontrol eder
- [x] `CheckSubscription.php` (subscription) - Aktif abonelik kontrolü
- [x] `CheckApproval.php` (approved) - Kullanıcı onaylı mı kontrol eder

---

## AŞAMA 7: MAIL MODULE (nwidart) ✅

### Modül Oluşturma
```bash
php artisan module:make Mail
```

**✅ Modül oluşturuldu, 8 mail class ve template mevcut**

### Modül Yapısı
```
Modules/Mail/
├── app/
│   ├── Mail/
│   │   ├── WelcomeMail.php
│   │   ├── TrialEndingMail.php
│   │   ├── SubscriptionRenewalMail.php
│   │   ├── PaymentSuccessMail.php
│   │   ├── PaymentFailedMail.php
│   │   ├── NewDeviceLoginMail.php
│   │   ├── TwoFactorCodeMail.php
│   │   └── CorporateInviteMail.php
│   ├── Services/
│   │   └── MailService.php
│   └── Providers/
│       └── MailServiceProvider.php
├── resources/
│   └── views/
│       └── emails/
│           ├── welcome.blade.php
│           ├── trial-ending.blade.php
│           ├── subscription-renewal.blade.php
│           ├── payment-success.blade.php
│           ├── payment-failed.blade.php
│           ├── new-device-login.blade.php
│           ├── two-factor-code.blade.php
│           └── corporate-invite.blade.php
├── config/
│   └── config.php
└── module.json
```

### Mail Class'ları
- [x] `WelcomeMail.php` - Kayıt sonrası
- [x] `TrialEndingMail.php` - Deneme bitmeden 2 gün önce
- [x] `SubscriptionRenewalMail.php` - Yenileme öncesi 7 gün
- [x] `PaymentSuccessMail.php` - Ödeme başarılı
- [x] `PaymentFailedMail.php` - Ödeme başarısız
- [x] `NewDeviceLoginMail.php` - Yeni cihazdan giriş
- [x] `TwoFactorCodeMail.php` - 2FA SMS yedeği
- [x] `CorporateInviteMail.php` - Kurumsal davet

---

## AŞAMA 8: CRON JOBS ✅

### Command Dosyaları (app/Console/Commands/)
- [x] `CheckTrialExpiryCommand.php` - Günlük 09:00
- [x] `SendRenewalRemindersCommand.php` - Günlük 10:00
- [x] `ProcessRecurringPaymentsCommand.php` - Günlük 06:00
- [x] `CleanupExpiredSessionsCommand.php` - Haftalık Pazar 03:00

---

## KOMUTLAR

```bash
# Migration çalıştır
php artisan migrate
php artisan tenants:migrate

# Mail modülü oluştur
php artisan module:make Mail

# Cache temizle
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## TENANT AYARLARI

### İxtif (Tenant 2)
- paid_membership_enabled = false
- corporate_enabled = false
- trial_days = 0

### Muzibu (Tenant 1001)
- paid_membership_enabled = true
- corporate_enabled = true
- trial_days = 7
- Fiyat: 299 TL / 2.999 TL

---

## AŞAMA 9: AUTH THEME DESIGNS

### Tasarım Kütüphanesi
8 kategori × 5 tema = 40 sayfa

**Özellikler:**
- Dark/Light mode toggle (tamamında)
- Self-contained CSS/JS (CDN)
- Tailwind CSS + Alpine.js
- FontAwesome icons
- Sosyal giriş butonu YOK

### Klasör Yapısı
```
public/design/auth-themes/
├── login/
├── register/
├── forgot-password/
├── reset-password/
├── email-verification/
├── 2fa-code/
├── profile/
└── devices/
```

### Tema Stilleri (Her kategoride 5 adet)
1. **Minimal** - Temiz, sade, modern
2. **Corporate** - Kurumsal, profesyonel
3. **Creative** - Yaratıcı, renkli, animasyonlu
4. **Dark Pro** - Koyu, glow efektli
5. **Classic** - Klasik, zarif, serif font

### Progress

#### ✅ Tamamlanan
- [x] Login - design-1-minimal.html
- [x] Login - design-2-corporate.html
- [x] Login - design-3-creative.html
- [x] Login - design-4-dark-pro.html
- [x] Login - design-5-classic.html
- [x] Register - design-1-minimal.html
- [x] Register - design-2-corporate.html
- [x] Register - design-3-creative.html
- [x] Register - design-4-dark-pro.html
- [x] Register - design-5-classic.html
- [x] Forgot-password - design-1-minimal.html
- [x] Forgot-password - design-2-corporate.html
- [x] Forgot-password - design-3-creative.html
- [x] Forgot-password - design-4-dark-pro.html
- [x] Forgot-password - design-5-classic.html
- [x] Reset-password - design-1-minimal.html
- [x] Reset-password - design-2-corporate.html
- [x] Reset-password - design-3-creative.html
- [x] Reset-password - design-4-dark-pro.html
- [x] Reset-password - design-5-classic.html

- [x] Email-verification - design-1-minimal.html
- [x] Email-verification - design-2-corporate.html
- [x] Email-verification - design-3-creative.html
- [x] Email-verification - design-4-dark-pro.html
- [x] Email-verification - design-5-classic.html
- [x] 2fa-code - design-1-minimal.html
- [x] 2fa-code - design-2-corporate.html
- [x] 2fa-code - design-3-creative.html
- [x] 2fa-code - design-4-dark-pro.html
- [x] 2fa-code - design-5-classic.html
- [x] Profile - design-1-minimal.html
- [x] Profile - design-2-corporate.html
- [x] Profile - design-3-creative.html
- [x] Profile - design-4-dark-pro.html
- [x] Profile - design-5-classic.html
- [x] Devices - design-1-minimal.html
- [x] Devices - design-2-corporate.html
- [x] Devices - design-3-creative.html
- [x] Devices - design-4-dark-pro.html
- [x] Devices - design-5-classic.html

### Tamamlanma Durumu
**40/40 tema tamamlandi!** (8 kategori x 5 tema)

### URL
Tasarım Kataloğu: https://ixtif.com/design/
