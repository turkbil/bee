# 📞 İletişim ve Bildirim Ayarları Sistemi

**Tarih:** 2025-10-18
**Tenant:** ixtif.com (ID: 2)
**Durum:** ✅ Tamamlandı

---

## 📋 ÖZET

İxtif.com için kapsamlı **İletişim Bilgileri** ve **Bildirim Ayarları** sistemleri oluşturuldu.

### ✨ Yapılanlar

1. ✅ Central DB'de 2 yeni Setting Grubu oluşturuldu
2. ✅ Toplam 34 yeni setting tanımı eklendi
3. ✅ Tenant 2 (ixtif.com) için değerler dolduruldu
4. ✅ 4 adet Seeder dosyası oluşturuldu
5. ✅ Footer kodu güncellendi (telefon/whatsapp)

---

## 🏗️ YENİ SETTING GRUPLARI

### 1️⃣ İletişim Bilgileri Grubu
**Slug:** `iletisim-bilgileri`
**Parent:** Genel Sistem (ID: 1)
**Toplam Ayar:** 24

#### 📞 Telefonlar (3)
- `contact_phone_1` - Ana Telefon → **0216 755 3 555**
- `contact_phone_2` - Alternatif Telefon 1
- `contact_phone_3` - Alternatif Telefon 2

#### 💬 WhatsApp (3)
- `contact_whatsapp_1` - Ana WhatsApp → **0501 005 67 58**
- `contact_whatsapp_2` - Destek WhatsApp
- `contact_whatsapp_3` - Satış WhatsApp

#### 📧 E-postalar (3)
- `contact_email_1` - Genel E-posta → **info@ixtif.com**
- `contact_email_2` - Destek E-posta
- `contact_email_3` - Satış E-posta

#### 🌐 Sosyal Medya (7)
- `social_instagram` → **https://instagram.com/ixtifcom**
- `social_facebook` → **https://facebook.com/ixtif**
- `social_twitter`
- `social_linkedin`
- `social_tiktok`
- `social_youtube`
- `social_pinterest`

#### 📍 Adres Bilgileri (6)
- `contact_address_line_1`
- `contact_address_line_2`
- `contact_city` → **İstanbul**
- `contact_state` → **Tuzla**
- `contact_postal_code`
- `contact_country` → **Türkiye**

#### ⏰ Çalışma Saatleri (2)
- `contact_working_hours` → **08:00 - 20:00 (Hafta içi ve Cumartesi)**
- `contact_working_days` → **Pazartesi - Cumartesi**

---

### 2️⃣ Bildirim Ayarları Grubu
**Slug:** `bildirim-ayarlari`
**Parent:** Genel Sistem (ID: 1)
**Toplam Ayar:** 10

#### 📱 Telegram Bildirimleri (3)
- `telegram_enabled` (checkbox) → **1** (Aktif)
- `telegram_bot_token` → **8344881512:AAGJQn3Z167ebNx67pwvGuKf1RbzTHazbt0**
- `telegram_chat_id` → **-1002943373765**

#### 💬 WhatsApp Bildirimleri - Twilio (5)
- `whatsapp_enabled` (checkbox) → **1** (Aktif)
- `twilio_account_sid` → **AC1b50075754770609cb4a69be42112e3f**
- `twilio_auth_token` (password) → **b2b99ddd9ebd4d771bb96c08ece5d97c**
- `twilio_whatsapp_from` → **whatsapp:+14155238886**
- `twilio_whatsapp_to` → **whatsapp:+905322160754**

#### 📧 Email Bildirimleri (2)
- `email_enabled` (checkbox) → **1** (Aktif)
- `notification_email` → **info@ixtif.com**

---

## 📁 OLUŞTURULAN DOSYALAR

### 1. Central DB Seeder'ları
```
/Modules/SettingManagement/database/seeders/ContactSettingsSeeder.php
/Modules/SettingManagement/database/seeders/NotificationSettingsSeeder.php
```

**Çalıştırma:**
```bash
php artisan db:seed --class="Modules\\SettingManagement\\Database\\Seeders\\ContactSettingsSeeder"
php artisan db:seed --class="Modules\\SettingManagement\\Database\\Seeders\\NotificationSettingsSeeder"
```

### 2. Tenant DB Seeder'ları
```
/Modules/SettingManagement/database/seeders/ContactSettingsValuesSeeder.php
/Modules/SettingManagement/database/seeders/NotificationSettingsValuesSeeder.php
```

**Çalıştırma (Tenant context gerekli):**
```php
use App\Models\Tenant;
use Modules\SettingManagement\Database\Seeders\ContactSettingsValuesSeeder;
use Modules\SettingManagement\Database\Seeders\NotificationSettingsValuesSeeder;

$tenant = Tenant::find(2); // ixtif.com
$tenant->run(function () {
    (new ContactSettingsValuesSeeder)->run();
    (new NotificationSettingsValuesSeeder)->run();
});
```

---

## 🔧 FOOTER GÜNCELLEMELERİ

### Değiştirilen Dosya
```
/resources/views/themes/ixtif/layouts/footer.blade.php
```

### Değişiklikler
```php
// ❌ ESKİ
$contactPhone = setting('contact_phone', '0850 123 45 67');
$contactWhatsapp = setting('contact_whatsapp', '905010056758');
$contactEmail = setting('contact_email', 'info@ixtif.com');

// ✅ YENİ
$contactPhone = setting('contact_phone_1', '0216 755 3 555');
$contactWhatsapp = setting('contact_whatsapp_1', '0501 005 67 58');
$contactEmail = setting('contact_email_1', 'info@ixtif.com');

// Sosyal Medya WhatsApp linki de güncellendi
['icon' => 'whatsapp', 'url' => 'https://wa.me/' . preg_replace('/[^0-9]/', '', setting('contact_whatsapp_1', '905010056758')), ...]
```

---

## ✅ TEST SONUÇLARI

### Central DB Settings
```bash
✅ İletişim Bilgileri grubu ve 24 ayar oluşturuldu!
✅ Bildirim Ayarları grubu ve 10 ayar oluşturuldu!
```

### Tenant 2 (ixtif.com) Values
```bash
✅ İletişim Bilgileri değerleri oluşturuldu!
   ├─ Oluşturulan: 10
   └─ Atlanan (boş): 14

✅ Bildirim Ayarları değerleri oluşturuldu!
   ├─ Oluşturulan: 10
   └─ Atlanan (boş): 0
```

### Frontend Test (ixtif.com)
```bash
# Footer'da doğru numaralar görünüyor:
✅ 0216 755 3 555 (Telefon)
✅ 0501 005 67 58 (WhatsApp)
✅ info@ixtif.com (Email)
```

---

## 📚 KULLANIM ÖRNEKLERİ

### Blade Template'lerde
```php
{{ setting('contact_phone_1') }}          // 0216 755 3 555
{{ setting('contact_whatsapp_1') }}       // 0501 005 67 58
{{ setting('contact_email_1') }}          // info@ixtif.com
{{ setting('social_instagram') }}         // https://instagram.com/ixtifcom
{{ setting('contact_working_hours') }}    // 08:00 - 20:00 (Hafta içi ve Cumartesi)
{{ setting('telegram_enabled') }}         // 1
{{ setting('notification_email') }}       // info@ixtif.com
```

### PHP'de Değer Güncelleme (Tenant Context'te)
```php
// Tenant context içinde olmalısınız!
setting_update('contact_phone_1', '0216 123 45 67');
setting_update('social_facebook', 'https://facebook.com/yenisayfa');

// Cache temizle (otomatik yapılır, ama manuel de yapabilirsiniz)
setting_clear_cache();
```

---

## 🎯 YENİ TENANT OLUŞTURURKEN

Yeni bir tenant oluşturduğunuzda bu seeder'ları çalıştırmanız gerekir:

```php
use App\Models\Tenant;
use Modules\SettingManagement\Database\Seeders\ContactSettingsValuesSeeder;
use Modules\SettingManagement\Database\Seeders\NotificationSettingsValuesSeeder;

// Önce Central DB'de settings tanımları oluşturulmuş olmalı!

$newTenant = Tenant::find(3); // Yeni tenant ID
$newTenant->run(function () {
    (new ContactSettingsValuesSeeder)->run();
    (new NotificationSettingsValuesSeeder)->run();
});
```

**NOT:** Değerleri tenant'a özel olarak düzenlemeniz gerekir!

---

## 🔍 VERİTABANI YAPISI

### Central Database: `settings` tablosu
```sql
SELECT * FROM settings WHERE `key` LIKE 'contact_%' OR `key` LIKE 'social_%';
SELECT * FROM settings WHERE `key` LIKE 'telegram_%' OR `key` LIKE 'twilio_%';
```

### Tenant Database: `settings_values` tablosu
```sql
SELECT sv.*, s.key, s.label
FROM settings_values sv
JOIN central.settings s ON sv.setting_id = s.id
WHERE s.key LIKE 'contact_%' OR s.key LIKE 'social_%';
```

---

## 🎨 ADMİN PANELİ

Ayarları düzenlemek için:
```
https://ixtif.com/admin/settingmanagement/values/{group_id}

İletişim Bilgileri Group ID: (otomatik oluşturulan ID)
Bildirim Ayarları Group ID: (otomatik oluşturulan ID)
```

Group ID'leri bulmak için:
```sql
SELECT id, name, slug FROM settings_groups WHERE slug IN ('iletisim-bilgileri', 'bildirim-ayarlari');
```

---

## 📝 NOTLAR

1. ✅ **Setting sistemi 2-katmanlı çalışır:**
   - **Central DB (`settings`)**: Ayar tanımları
   - **Tenant DB (`settings_values`)**: Ayar değerleri

2. ✅ **Cache kullanılıyor:**
   - Ayar değerleri 3600 saniye (1 saat) cache'lenir
   - `setting_clear_cache()` ile temizlenebilir
   - SettingValue model'inde otomatik cache temizleme var

3. ✅ **Fallback sistem:**
   - Önce tenant value bakılır
   - Yoksa central default_value kullanılır
   - O da yoksa fonksiyona verilen default parametre kullanılır

4. ✅ **Tüm ayarlar opsiyonel:**
   - Boş değerler de geçerli (özellikle checkbox'lar için '0' geçerli)
   - Frontend'de fallback değerler tanımlı

---

## 🚀 SONRAKI ADIMLAR

1. [ ] Diğer tenant'lar için de value seeder'ları çalıştır
2. [ ] Admin panelinde setting gruplarını düzenle/iyileştir
3. [ ] Email template'lerinde bu ayarları kullan
4. [ ] API endpoint'leri için bu ayarları kullan
5. [ ] Dökümantasyon sayfası oluştur

---

**Oluşturan:** Claude
**Tarih:** 2025-10-18
**İşlem Süresi:** ~30 dakika
**Durum:** ✅ Production'da aktif
