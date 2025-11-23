# Üyelik Sistemi - Tenant-Aware TODO

## Tarih: 2025-11-23
## Versiyon: 13 (Final Plan)
## HTML Rapor: https://ixtif.com/readme/2025/11/23/uyelik-sistemi-analiz/

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

## AŞAMA 1: TABLO RENAME MİGRATION

### Migration Dosyası
```
database/migrations/2025_11_23_001_rename_shop_tables_to_universal.php
database/migrations/tenant/2025_11_23_001_rename_shop_tables_to_universal.php
```

### Rename İşlemleri
- [ ] `shop_subscription_plans` → `subscription_plans`
- [ ] `shop_subscriptions` → `subscriptions`
- [ ] `shop_coupons` → `coupons`
- [ ] `shop_coupon_usages` → `coupon_usages`
- [ ] `shop_customer_addresses` → `customer_addresses`

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

## AŞAMA 2: USERS TABLOSU GÜNCELLEME

### Migration Dosyası
```
database/migrations/2025_11_23_002_add_membership_fields_to_users_table.php
database/migrations/tenant/2025_11_23_002_add_membership_fields_to_users_table.php
```

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

## AŞAMA 3: UNIVERSAL MODELLER

### Model Dosyaları (app/Models/)
- [ ] `SubscriptionPlan.php`
- [ ] `Subscription.php` (implements Payable)
- [ ] `Coupon.php`
- [ ] `CouponUsage.php`
- [ ] `CustomerAddress.php`

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

## AŞAMA 4: SERVİSLER

### Servis Dosyaları (app/Services/Auth/)
- [ ] `DeviceService.php` - sessions tablosu ile cihaz yönetimi
- [ ] `LoginLogService.php` - activity_log ile giriş kaydı
- [ ] `TwoFactorService.php` - SMS kod gönderme/doğrulama
- [ ] `SubscriptionService.php` - Abonelik işlemleri
- [ ] `CouponService.php` - Kupon doğrulama/uygulama
- [ ] `CorporateService.php` - Kurumsal hesap yönetimi

---

## AŞAMA 5: SETTINGMANAGEMENT (Seeder ile DB'ye)

### Veritabanı Yapısı
- `settings_groups` (CENTRAL) → Grup + prefix tanımı
- `settings` (CENTRAL) → Ayar tanımları (key, type, default)
- `settings_values` (TENANT) → Her tenant'ın değerleri

### Seeder Dosyası
```
Modules/SettingManagement/database/seeders/AuthSettingsSeeder.php
```

### Oluşturulacak Gruplar (5 adet)

| ID | Grup Adı | Prefix | Icon |
|----|----------|--------|------|
| 20 | Kayıt Ayarları | auth_registration | fas fa-user-plus |
| 21 | Oturum Ayarları | auth_session | fas fa-clock |
| 22 | Güvenlik Ayarları | auth_security | fas fa-shield-alt |
| 23 | Abonelik Ayarları | auth_subscription | fas fa-credit-card |
| 24 | Kurumsal Ayarlar | corporate | fas fa-building |

### Ayarlar (Key = prefix_name formatında)

#### auth_registration
- [ ] `auth_registration_enabled` (select, 1) - Kayıt Aktif
- [ ] `auth_registration_email_verify` (select, 1) - E-posta Doğrulama
- [ ] `auth_registration_approval` (select, 0) - Admin Onayı
- [ ] `auth_registration_trial_days` (text, 7) - Deneme Süresi (gün)

#### auth_session
- [ ] `auth_session_lifetime` (text, 525600) - Oturum Süresi (dk) - 1 yıl
- [ ] `auth_session_device_limit` (text, 1) - Cihaz Limiti

#### auth_security
- [ ] `auth_security_max_attempts` (text, 5) - Max Giriş Denemesi
- [ ] `auth_security_lockout` (text, 30) - Kilitleme Süresi (dk)
- [ ] `auth_security_2fa_enabled` (select, 1) - 2FA Aktif
- [ ] `auth_security_2fa_expiry` (text, 5) - 2FA Kod Süresi (dk)

#### auth_subscription
- [ ] `auth_subscription_paid_enabled` (select, 0) - Ücretli Üyelik
- [ ] `auth_subscription_auto_renewal` (select, 1) - Otomatik Yenileme
- [ ] `auth_subscription_reminder_days` (text, 7) - Hatırlatma (gün önce)
- [ ] `auth_subscription_grace_days` (text, 3) - Tolerans Süresi (gün)

#### corporate
- [ ] `corporate_enabled` (select, 0) - Kurumsal Üyelik
- [ ] `corporate_max_users` (text, 0) - Max Alt Kullanıcı (0=sınırsız)

### Seeder Çalıştırma
```bash
php artisan db:seed --class="Modules\\SettingManagement\\Database\\Seeders\\AuthSettingsSeeder"
```

### Kodda Kullanım
```php
// Helper ile
$trialDays = setting('auth_registration_trial_days', 7);
$deviceLimit = setting('auth_session_device_limit', 1);
$isPaidEnabled = setting('auth_subscription_paid_enabled', false);
```

---

## AŞAMA 6: MIDDLEWARE

### Middleware Dosyaları (app/Http/Middleware/)
- [ ] `CheckDeviceLimit.php` (device.limit) - Cihaz limitini kontrol eder
- [ ] `CheckSubscription.php` (subscription) - Aktif abonelik kontrolü
- [ ] `CheckApproval.php` (approved) - Kullanıcı onaylı mı kontrol eder

---

## AŞAMA 7: MAIL MODULE (nwidart)

### Modül Oluşturma
```bash
php artisan module:make Mail
```

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
- [ ] `WelcomeMail.php` - Kayıt sonrası
- [ ] `TrialEndingMail.php` - Deneme bitmeden 2 gün önce
- [ ] `SubscriptionRenewalMail.php` - Yenileme öncesi 7 gün
- [ ] `PaymentSuccessMail.php` - Ödeme başarılı
- [ ] `PaymentFailedMail.php` - Ödeme başarısız
- [ ] `NewDeviceLoginMail.php` - Yeni cihazdan giriş
- [ ] `TwoFactorCodeMail.php` - 2FA SMS yedeği
- [ ] `CorporateInviteMail.php` - Kurumsal davet

---

## AŞAMA 8: CRON JOBS

### Command Dosyaları (app/Console/Commands/)
- [ ] `CheckTrialExpiryCommand.php` - Günlük 09:00
- [ ] `SendRenewalRemindersCommand.php` - Günlük 10:00
- [ ] `ProcessRecurringPaymentsCommand.php` - Günlük 06:00
- [ ] `CleanupExpiredSessionsCommand.php` - Haftalık Pazar 03:00

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
