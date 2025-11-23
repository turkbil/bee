# 📞 WhatsApp ve Telefon: Settings'ten Dinamik Alma

**Tarih:** 2025-10-19
**Sorun:** Chatbot, WhatsApp ve telefon numaralarını sabit değer kullanıyordu ve WhatsApp linkini yanlış oluşturuyordu
**Çözüm:** Settings'ten dinamik alma + Post-processing ile link düzeltme

---

## 🔴 SORUN

### 1. Sabit Numara Sorunu
```php
// ❌ ÖNCE (IxtifPromptService.php - Satır 155-156)
$prompts[] = "💬 **WhatsApp:** [0534 515 2626](https://wa.me/905345152626)";
$prompts[] = "📞 **Telefon:** 0534 515 2626";
```

**Sorun:**
- Numara sabit kodlanmış (her tenant için farklı olmalı)
- Settings'teki değerler kullanılmıyordu
- Tenant-specific iletişim bilgileri gösterilmiyordu

### 2. WhatsApp Link Hatası
```json
// ❌ AI Yanıtı (YANLIŞ)
"WhatsApp:** [0501 005 67 58](https://ixtif.com/shop/ixtif-efx5-301-45-m-direk)"
```

**Sorun:**
- AI, WhatsApp linkini ÜRÜN LİNKİ ile karıştırıyordu
- `[LINK:shop:xxx]` pattern'i WhatsApp linkine de uygulanıyordu
- `https://wa.me/...` yerine `https://ixtif.com/shop/...` linki oluşturuyordu

---

## ✅ ÇÖZÜM

### **Aşama 1: Settings'ten Dinamik Alma**

**Dosya:** `Modules/AI/app/Services/Tenant/IxtifPromptService.php`

```php
public function buildPrompt(): array
{
    $prompts = [];

    // İletişim bilgilerini settings'ten al
    $contactInfo = \App\Helpers\AISettingsHelper::getContactInfo();

    // WhatsApp ve Telefon için fallback (settings'te yoksa)
    $whatsapp = $contactInfo['whatsapp'] ?? '0534 515 2626';
    $phone = $contactInfo['phone'] ?? '0534 515 2626';

    // WhatsApp clean format (0534 -> 905345152626)
    $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
    if (substr($cleanWhatsapp, 0, 1) === '0') {
        $cleanWhatsapp = '90' . substr($cleanWhatsapp, 1);
    }
    $whatsappLink = "https://wa.me/{$cleanWhatsapp}";

    // ...
}
```

**Değişiklikler:**
- ✅ `AISettingsHelper::getContactInfo()` kullanıldı
- ✅ `contact_whatsapp_1` ve `contact_phone_1` settings'ten okunuyor
- ✅ Fallback mekanizması var (settings boşsa eski numara)
- ✅ WhatsApp numarası otomatik temizleniyor (0501 → 905010056758)

### **Aşama 2: Prompt'larda Dinamik Değerler**

```php
// ÜRÜN BULUNAMADI bölümünde (Satır 170-171)
$prompts[] = "💬 **WhatsApp:** [{$whatsapp}]({$whatsappLink})";
$prompts[] = "📞 **Telefon:** {$phone}";

// TELEFON TOPLAMA bölümünde (Satır 121-126)
$prompts[] = "3. Telefon alamazsan → O ZAMAN bizim numarayı ver: **{$whatsapp}**";
$prompts[] = "";
$prompts[] = "**WhatsApp Bilgisi (Sadece telefon alamazsan):**";
$prompts[] = "- Numara: **{$whatsapp}**";
$prompts[] = "- Link: {$whatsappLink}";
$prompts[] = "- Format: `[{$whatsapp}]({$whatsappLink})`";
```

### **Aşama 3: Mega Kritik Uyarı Eklendi**

```php
$prompts[] = "";
$prompts[] = "🚨🚨🚨 **MEGA KRİTİK: WhatsApp LİNK HATASI YAPMA!** 🚨🚨🚨";
$prompts[] = "";
$prompts[] = "❌ **BU HATALAR YAPILDI (TEKRAR YAPMA!):**";
$prompts[] = "- `[{$whatsapp}](https://ixtif.com/shop/ixtif-efx3-251-1220-mm-catal)` ← YANLIŞ!";
$prompts[] = "- `[{$whatsapp}](https://ixtif.com/shop/...)` ← YANLIŞ!";
$prompts[] = "- WhatsApp numarasına ASLA ürün sayfası linki koyma!";
$prompts[] = "";
$prompts[] = "✅ **TEK DOĞRU FORMAT:**";
$prompts[] = "- `[{$whatsapp}]({$whatsappLink})` ← SADECE BU!";
$prompts[] = "- Link MUTLAKA `{$whatsappLink}` olmalı!";
$prompts[] = "- `wa.me/` ile başlamalı, `/shop/` ile ASLA başlamamali!";
```

### **Aşama 4: Post-Processing Eklendi** 🔧

**Dosya:** `Modules/AI/app/Http/Controllers/Api/PublicAIController.php`

**Metod:** `fixWhatsAppLinks()` (Satır 2154-2182)

```php
/**
 * 🔧 Fix WhatsApp Links - AI bazen ürün linki koyuyor, düzeltelim
 *
 * AI yanıtında WhatsApp linkini ürün linki ile karıştırıyorsa, doğru wa.me linkini oluştur
 */
private function fixWhatsAppLinks(string $message): string
{
    // WhatsApp numarası settings'ten al
    $contactInfo = \App\Helpers\AISettingsHelper::getContactInfo();
    $whatsapp = $contactInfo['whatsapp'] ?? '0534 515 2626';

    // Clean WhatsApp number (0534 -> 905345152626)
    $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
    if (substr($cleanWhatsapp, 0, 1) === '0') {
        $cleanWhatsapp = '90' . substr($cleanWhatsapp, 1);
    }
    $correctWhatsAppLink = "https://wa.me/{$cleanWhatsapp}";

    // Pattern 1: [WHATSAPP_NUMBER](WRONG_SHOP_LINK)
    $pattern = '/\[([0-9\s]+)\]\(https?:\/\/[^\)]+\/shop\/[^\)]+\)/i';
    $replacement = "[$1]({$correctWhatsAppLink})";
    $fixed = preg_replace($pattern, $replacement, $message);

    // Pattern 2: WhatsApp: [NUMBER](NON_WA_ME_LINK)
    $pattern2 = '/(WhatsApp:\s*)\[([0-9\s]+)\]\(https?:\/\/(?!wa\.me)[^\)]+\)/i';
    $replacement2 = "$1[$2]({$correctWhatsAppLink})";
    $fixed = preg_replace($pattern2, $replacement2, $fixed);

    return $fixed;
}
```

**Kullanım:** (Satır 867-870)
```php
// 📞 PHONE NUMBER DETECTION & TELESCOPE LOGGING
$this->detectPhoneNumberAndLogToTelescope($conversation);

// 🔧 WhatsApp Link Post-Processing Fix
$finalMessage = $aiResponse['content'] ?? '';
$finalMessage = $this->fixWhatsAppLinks($finalMessage);

return response()->json([
    'success' => true,
    'data' => [
        'message' => $finalMessage,
        // ...
    ]
]);
```

---

## 🎯 SONUÇ

### ✅ ÖNCE (YANLIŞ)
```json
{
  "message": "İxtif olarak, terazili transpalet... WhatsApp: [0501 005 67 58](https://ixtif.com/shop/ixtif-efx5-301-45-m-direk)"
}
```

### ✅ SONRA (DOĞRU)
```json
{
  "message": "İxtif olarak, terazili transpalet... WhatsApp: [0501 005 67 58](https://wa.me/905010056758) Telefon: 0216 755 3 555"
}
```

---

## 📊 KAZANIMLAR

1. **✅ Dinamik İletişim:** Settings'ten `contact_whatsapp_1` ve `contact_phone_1` çekiliyor
2. **✅ Tenant-Specific:** Her tenant kendi numarasını gösteriyor
3. **✅ Doğru WhatsApp Link:** `https://wa.me/905010056758` formatında
4. **✅ Otomatik Düzeltme:** AI yanlış link üretse bile post-processing düzeltiyor
5. **✅ Pozitif Yanıt:** "Size yardımcı olabiliriz!" tonu korunuyor

---

## 🔧 TEKNİK DETAYLAR

### Settings Yapısı
```
settings_groups (id: 10) - "İletişim Bilgileri"
├── settings
│   ├── contact_phone_1 (Ana Telefon)
│   ├── contact_whatsapp_1 (Ana WhatsApp)
│   ├── contact_email_1 (Genel E-posta)
│   └── ...
└── settings_values
    └── value (tenant-specific değerler)
```

### AISettingsHelper::getContactInfo()
```php
[
    'phone' => setting('contact_phone_1', null),        // 0216 755 3 555
    'whatsapp' => setting('contact_whatsapp_1', null),  // 0501 005 67 58
    'email' => setting('contact_email_1', null),
    // ...
]
```

### WhatsApp Link Formatı
```
Input:  0501 005 67 58
Clean:  05010056758
Format: 905010056758 (başına 90 eklendi)
Link:   https://wa.me/905010056758
```

---

## 🚀 TEST SONUÇLARI

**Test Komutu:**
```bash
curl -k -X POST 'https://ixtif.com/api/ai/v1/shop-assistant/chat' \
  -H 'Content-Type: application/json' \
  -d '{"message":"terazili transpalet var mı"}'
```

**Sonuç:**
```json
{
  "success": true,
  "data": {
    "message": "İxtif olarak, terazili transpalet konusunda size yardımcı olabiliriz! 😊 Bu konuda detaylı bilgi almak ve size özel çözümler sunabilmek için müşteri temsilcimizle görüşmenizi öneriyoruz. **Hemen iletişime geçin:** 💬 **WhatsApp:** [0501 005 67 58](https://wa.me/905010056758) 📞 **Telefon:** 0216 755 3 555 Size özel çözümler ve fiyat teklifleri hazırlayabiliriz! Hangi özellikleri arıyorsunuz?"
  }
}
```

**Doğrulama:**
- ✅ WhatsApp Link: `https://wa.me/905010056758` ← DOĞRU!
- ✅ Telefon: 0216 755 3 555 ← DOĞRU (Settings'ten!)
- ✅ WhatsApp Numara: 0501 005 67 58 ← DOĞRU (Settings'ten!)
- ✅ Pozitif Ton: "size yardımcı olabiliriz!" ← DOĞRU!

---

## 📝 DEĞİŞTİRİLEN DOSYALAR

1. **Modules/AI/app/Services/OptimizedPromptService.php**
   - Tenant kontrolü eklendi (satır 893-898)

2. **Modules/AI/app/Services/Tenant/IxtifPromptService.php**
   - Dinamik settings entegrasyonu (satır 30-41)
   - Sabit numaralar kaldırıldı
   - Mega kritik uyarı eklendi (satır 177-189)

3. **Modules/AI/app/Http/Controllers/Api/PublicAIController.php**
   - fixWhatsAppLinks() metodu (satır 2154-2182)
   - Post-processing uygulandı (satır 867-870)

---

## ⚙️ SETTINGS PANEL

**URL:** https://ixtif.com/admin/settingmanagement/values/10

**Ayarlanacak Değerler:**
- `contact_phone_1`: Ana telefon numarası (örn: 0216 755 3 555)
- `contact_whatsapp_1`: Ana WhatsApp numarası (örn: 0501 005 67 58)

**Not:** Değerler boş bırakılırsa fallback olarak `0534 515 2626` kullanılır.

---

**🎉 SORUN ÇÖZÜLDÜ!**

- Chatbot artık settings'ten dinamik olarak WhatsApp ve telefon numarası alıyor
- WhatsApp linki doğru formatda (`https://wa.me/...`)
- AI yanlış link üretse bile post-processing düzeltiyor
- Pozitif yanıt tonu korunuyor
