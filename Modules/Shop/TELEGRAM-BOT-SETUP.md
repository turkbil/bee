# 🤖 Telegram Bot Kurulum Kılavuzu

Shop modülü iletişim formundan gelen bildirimleri hem mail hem de Telegram'a otomatik olarak göndermek için Telegram bot kurulumu.

---

## 📋 İçindekiler

1. [Telegram Bot Oluşturma](#1-telegram-bot-oluşturma)
2. [Chat ID Bulma](#2-chat-id-bulma)
3. [Ortam Değişkenlerini Ayarlama](#3-ortam-değişkenlerini-ayarlama)
4. [Test Etme](#4-test-etme)
5. [Sorun Giderme](#5-sorun-giderme)

---

## 1. Telegram Bot Oluşturma

### Adım 1.1: BotFather'a Git
1. Telegram'da [@BotFather](https://t.me/botfather) kullanıcısını ara
2. Sohbeti başlat
3. `/newbot` komutunu gönder

### Adım 1.2: Bot Bilgilerini Gir
1. Bot için bir isim belirle (örn: "İXTİF Shop Bot")
2. Bot için benzersiz bir kullanıcı adı belirle (örn: "ixtif_shop_bot")
   - Kullanıcı adı `bot` ile bitmelidir
   - Benzersiz olmalıdır

### Adım 1.3: Bot Token'ı Al
BotFather size bir **token** verecek. Bu token'ı güvenli bir yere kaydedin.

Örnek token:
```
123456789:ABCdefGHIjklMNOpqrsTUVwxyz123456789
```

---

## 2. Chat ID Bulma

Bildirimlerin gönderileceği chat ID'yi bulmalısınız.

### Seçenek A: Kişisel Hesaba Bildirim Göndermek

1. Bot'unuzu Telegram'da arayın (örn: @ixtif_shop_bot)
2. `/start` komutunu gönderin
3. Tarayıcınızda şu URL'yi açın:
   ```
   https://api.telegram.org/bot<BOT_TOKEN>/getUpdates
   ```
   `<BOT_TOKEN>` yerine kendi bot token'ınızı yazın

4. Response'da `chat` -> `id` değerini bulun:
   ```json
   {
     "ok": true,
     "result": [{
       "message": {
         "chat": {
           "id": 123456789,
           "first_name": "John",
           "type": "private"
         }
       }
     }]
   }
   ```

5. Bu `id` değeri sizin **Chat ID**'niz (örn: `123456789`)

### Seçenek B: Grup/Kanal'a Bildirim Göndermek

1. Telegram'da bir grup oluşturun
2. Bot'u gruba admin olarak ekleyin
3. Gruba herhangi bir mesaj gönderin
4. Aşağıdaki URL'yi tarayıcıda açın:
   ```
   https://api.telegram.org/bot<BOT_TOKEN>/getUpdates
   ```

5. Response'da grup chat ID'sini bulun (negatif bir sayı olacaktır):
   ```json
   {
     "chat": {
       "id": -987654321,
       "title": "İXTİF Shop Bildirimleri",
       "type": "group"
     }
   }
   ```

6. Bu negatif sayı sizin **Group Chat ID**'niz (örn: `-987654321`)

---

## 3. Ortam Değişkenlerini Ayarlama

### Adım 3.1: .env Dosyasını Düzenle

`.env` dosyanızı açın ve aşağıdaki değişkenleri bulun:

```env
# ===========================================
# TELEGRAM BOT AYARLARI
# ===========================================

# Telegram Bot Token (BotFather'dan alınır)
TELEGRAM_BOT_TOKEN=

# Telegram Chat ID (Bildirim gönderilecek grup veya kullanıcı ID'si)
TELEGRAM_CHAT_ID=
```

### Adım 3.2: Değerleri Girin

```env
# ===========================================
# TELEGRAM BOT AYARLARI
# ===========================================

# Telegram Bot Token (BotFather'dan alınır)
TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrsTUVwxyz123456789

# Telegram Chat ID (Bildirim gönderilecek grup veya kullanıcı ID'si)
TELEGRAM_CHAT_ID=123456789
```

### Adım 3.3: Cache'i Temizle

Değişikliklerin aktif olması için cache'i temizleyin:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 4. Test Etme

### Adım 4.1: Test İçin Form Doldur

1. Web sitenizde bir ürün sayfasına gidin (örn: http://laravel.test/shop/urun-slug)
2. "Teklif Al" formunu doldurun
3. Formu gönderin

### Adım 4.2: Bildirimleri Kontrol Et

**Mail**: Admin e-posta adresinizi kontrol edin
**Telegram**: Telegram hesabınızı veya grubunuzu kontrol edin

Telegram mesajı şu formatta gelecek:

```
🔔 YENİ TEKLİF TALEBİ

📦 Ürün: İXTİF RSL161 16 Ton Li-ion İstif Makinesi

👤 Müşteri Bilgileri:
• Ad Soyad: Ahmet Yılmaz
• E-posta: ahmet@example.com
• Telefon: 0555 555 55 55

💬 Mesaj:
Bu ürün hakkında detaylı bilgi almak istiyorum.

🔗 [Ürünü Görüntüle]
```

---

## 5. Sorun Giderme

### Telegram bildirimi gelmiyor ama mail geliyor

**Çözüm 1: Token ve Chat ID'yi kontrol edin**
```bash
# .env dosyanızı kontrol edin
cat .env | grep TELEGRAM
```

**Çözüm 2: Bot'un mesaj gönderme iznini kontrol edin**
- Kişisel hesap kullanıyorsanız, bota `/start` yazın
- Grup kullanıyorsanız, bot'un admin olduğundan emin olun

**Çözüm 3: Log dosyalarını kontrol edin**
```bash
tail -f storage/logs/laravel.log
```

### Token veya Chat ID hatalı

**Test URL'si ile kontrol edin:**

```bash
curl "https://api.telegram.org/bot<BOT_TOKEN>/sendMessage?chat_id=<CHAT_ID>&text=Test"
```

Başarılı olursa şu cevabı alırsınız:
```json
{
  "ok": true,
  "result": {
    "message_id": 123,
    "date": 1234567890,
    "text": "Test"
  }
}
```

### Genel Hata Kontrolü

```bash
# Queue çalıştırın (bildirimler queue'ya gidiyorsa)
php artisan queue:work

# Log dosyasını izleyin
tail -f storage/logs/laravel.log
```

---

## 🎯 Özet

✅ BotFather'dan bot oluşturdunuz
✅ Bot token'ı aldınız
✅ Chat ID'yi buldunuz
✅ .env dosyasını güncellediniz
✅ Sistemi test ettiniz

**Artık her teklif talebi hem mail hem Telegram'a düşecek!** 🎉

---

## 📞 Destek

Sorun yaşıyorsanız:
1. Log dosyalarını kontrol edin
2. Token ve Chat ID'nin doğru olduğundan emin olun
3. Bot'un mesaj gönderme iznine sahip olduğunu kontrol edin

**İyi kullanımlar!** 🚀
