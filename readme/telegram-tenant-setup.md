# 📱 Tenant Bazlı Telegram Bildirimleri - Kurulum Rehberi

## 🎯 Sistem Mimarisi

Her tenant'ın **kendi Telegram bot'u ve chat ID'si** olmalı. Ayarlar tenant database'de saklanır.

---

## 📋 Yeni Tenant İçin Telegram Kurulumu

### 1️⃣ Telegram Bot Oluştur

1. Telegram'da **@BotFather** ile konuşun
2. `/newbot` komutunu gönderin
3. Bot adını verin (örn: "Muzibu Assistant")
4. Bot username verin (örn: "MuzibuBot")
5. **Bot Token'ı** kopyalayın: `1234567890:ABCdefGHIjklMNOpqrsTUVwxyz`

### 2️⃣ Chat ID Bul

**Seçenek A: Grup Chat ID (Önerilen)**
1. Telegram'da yeni grup oluşturun (örn: "Muzibu Bildirimler")
2. Bot'u gruba ekleyin (/start yazın)
3. **@userinfobot**'u gruba ekleyin
4. Chat ID'yi kopyalayın (örn: `-1002943373765`)
5. @userinfobot'u gruptan çıkarın

**Seçenek B: Kişisel Chat ID**
1. Bot'a `/start` mesajı gönderin
2. Tarayıcıda: `https://api.telegram.org/bot[BOT_TOKEN]/getUpdates`
3. `"chat":{"id":123456789}` değerini bulun

### 3️⃣ Admin Panelden Ayarları Gir

```
URL: https://[tenant-domain]/admin/settingmanagement/values/11

Telegram Ayarları:
- [✓] Telegram Bildirimlerini Aktifleştir
- Bot Token: 1234567890:ABCdefGHIjklMNOpqrsTUVwxyz
- Chat ID: -1002943373765
```

**KAYDET** butonuna basın.

### 4️⃣ Test Et

**Tinker ile test:**
```bash
# Tenant context'e gir
php artisan tenants:run --tenant=muzibu.com.tr "
    \$service = new \Modules\AI\App\Services\TelegramNotificationService();
    print_r(\$service->testConnection());
"
```

**Frontend'den test:**
1. AI chat widget'ını aç
2. Telefon numarası gönder: `0555 123 4567`
3. Telegram'dan bildirim geldiğini kontrol et

---

## 🔧 Manuel Kurulum (Tinker)

```bash
# 1. Tenant context'e gir
php artisan tinker

# 2. Tenant seç (örn: Tenant 1001 - muzibu.com.tr)
tenancy()->initialize(1001);

# 3. Ayarları oluştur
$values = [
    80 => '1',  // telegram_enabled
    81 => 'BOT_TOKEN_BURAYA',  // telegram_bot_token
    82 => 'CHAT_ID_BURAYA',  // telegram_chat_id
];

foreach ($values as $settingId => $value) {
    \Modules\SettingManagement\App\Models\SettingValue::updateOrCreate(
        ['setting_id' => $settingId],
        ['value' => $value]
    );
}

echo "✅ Telegram ayarları kaydedildi!\n";

# 4. Test et
$service = new \Modules\AI\App\Services\TelegramNotificationService();
print_r($service->testConnection());
```

---

## 📊 Setting ID Referansı

| Setting Key | Setting ID | Açıklama |
|-------------|-----------|----------|
| `telegram_enabled` | 80 | Telegram bildirimleri aktif mi? (1/0) |
| `telegram_bot_token` | 81 | Bot Token (@BotFather'dan) |
| `telegram_chat_id` | 82 | Chat/Grup ID (@userinfobot'tan) |
| `whatsapp_enabled` | 83 | WhatsApp bildirimleri aktif mi? (1/0) |

---

## 🚨 Sorun Giderme

### Bildirim Gelmiyor

1. **Setting kontrolü:**
```bash
php artisan tinker
>>> setting('telegram_enabled')
>>> setting('telegram_bot_token')
>>> setting('telegram_chat_id')
```

2. **Test connection:**
```bash
php artisan tinker
>>> $service = new \Modules\AI\App\Services\TelegramNotificationService();
>>> $service->testConnection();
```

3. **Log kontrolü:**
```bash
tail -n 100 storage/logs/laravel.log | grep Telegram
```

### Bot Mesaj Gönderemiyor

- Bot'u gruba admin yapın
- Chat ID doğru mu kontrol edin (- işareti önemli!)
- Bot token geçerli mi test edin

### Ayarlar Boş Geliyor

```bash
# Tenant context'te misiniz?
php artisan tinker
>>> tenant()  # null dönerse tenant context yok!
>>> tenancy()->initialize(2);  # Manuel initialize
```

---

## 📚 İlgili Dosyalar

- **Service:** `Modules/AI/app/Services/TelegramNotificationService.php`
- **Controller:** `Modules/AI/app/Http/Controllers/Api/PublicAIController.php`
- **Phone Detection:** `Modules/AI/app/Services/PhoneNumberDetectionService.php`
- **Setting Seeder:** `Modules/SettingManagement/database/seeders/NotificationSettingsValuesSeeder.php`

---

## ✅ Checklist

- [ ] Telegram bot oluşturuldu (@BotFather)
- [ ] Chat/Grup ID bulundu (@userinfobot)
- [ ] Admin panelden ayarlar girildi
- [ ] Test mesajı başarıyla gönderildi
- [ ] Frontend'den telefon testi yapıldı
- [ ] Telegram'dan bildirim alındı

**Kurulum tamamlandı! 🎉**
