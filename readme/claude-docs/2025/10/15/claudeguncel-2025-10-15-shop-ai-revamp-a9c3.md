# 🤖 SHOP AI CHAT ASSISTANT - KAPSAMLI REVİZYON

**Tarih:** 2025-10-15
**ID:** a9c3
**Kapsam:** AI Chat Widget'ın tam yeniden yapılandırılması

---

## 📋 GEREKSİNİMLER (Kullanıcı Talepleri)

### 🎯 ÖNCELİK SIRASI (Önem Sırasına Göre)

#### 1️⃣ **EN YÜKSEK ÖNCELİK: Ürün Bilgisi & Satış**
- ✅ Ürünleri DOĞRU bilmek (tüm tablolardan veri çek)
- ✅ Ürünler hakkında GÜzel anlatım (biraz överek, satış odaklı)
- ✅ Olumsuz ifadelerden KAÇIN
- ✅ Link vermekten ÇEKİNME
- ✅ Eksik veriler varsa geniş düşün

#### 2️⃣ **Akıllı Müşteri Anlama**
- ✅ Önce müşteriyi ANLA (gerekirse soru sor)
- ✅ Tıklanabilir seçenekler sun (yazarak yormamak için)
- ✅ Kullanıcı yazarsa tıklama linkleri pasif olur

#### 3️⃣ **Bilgi Bankası Entegrasyonu**
- ✅ Knowledge Base (ai_knowledge_base) tablosundaki soru-cevapları BİL
- ✅ Önemli SSS'leri AI'ya dahil et

#### 4️⃣ **Telefon Numarası Toplama**
- ✅ Müşteri ilgilendikten SONRA telefon iste
- ✅ Numara alamazsa bizim numarayı + WhatsApp'ı VER
- ✅ Numaraları parantez içinde de yaz (örn: "(0555 123 4567)")

#### 5️⃣ **Telegram Bildirimi**
- ✅ Numara alınınca Telegram'a BİLDİR
- ✅ Konuşma özeti + admin linki + telefon numarası gönder

---

## 🏗️ MEVCUT SİSTEM ANALİZİ

### ✅ Var Olanlar
1. **Knowledge Base Sistemi:**
   - Model: `AIKnowledgeBase` (SettingManagement modülü)
   - Tablo: `ai_knowledge_base`
   - Tenant-bazlı soru-cevap yönetimi
   - Admin panel: Livewire component var

2. **Telefon Tespiti:**
   - Service: `PhoneNumberDetectionService`
   - Regex pattern'ler (Türk numaraları)
   - Telescope logging mevcut

3. **Telegram Entegrasyonu:**
   - Package: `laravel-notification-channels/telegram` YÜKLENMİŞ
   - Henüz kullanılmıyor (aktif değil)

4. **Konuşma Sistemi:**
   - `AIConversation` + `AIMessage` models
   - Session tracking (IP-based)
   - Conversation history (son 20 mesaj)

### ❌ Eksikler
1. Knowledge Base AI'ya entegre DEĞİL
2. Telegram bildirimi AKTIF DEĞİL
3. Tıklanabilir buton sistemi YOK
4. Ürün bilgisi yeterince DETAYLI DEĞİL
5. Satış odaklı "övme" TON YOK

---

## 🛠️ YAPILACAK İŞLEMLER

### 1️⃣ Knowledge Base Entegrasyonu
**Dosya:** `PublicAIController.php` (buildEnhancedSystemPrompt method)

**Yapılacak:**
```php
// Knowledge Base bilgilerini prompt'a ekle
$knowledgeBase = AIKnowledgeBase::active()->ordered()->get();

if ($knowledgeBase->isNotEmpty()) {
    $prompts[] = "\n## 📚 BİLGİ BANKASI (SSS)\n";
    $prompts[] = "Aşağıdaki soru-cevapları BİL ve müşteri sorarsa kullan:\n";

    foreach ($knowledgeBase as $kb) {
        $prompts[] = "**S: {$kb->question}**";
        $prompts[] = "C: {$kb->answer}\n";
    }
}
```

---

### 2️⃣ Telegram Bildirim Servisi
**Yeni Dosya:** `Modules/AI/app/Services/TelegramNotificationService.php`

**İçerik:**
```php
<?php

namespace Modules\AI\App\Services;

use NotificationChannels\Telegram\TelegramMessage;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    public function sendPhoneNumberAlert(AIConversation $conversation, array $phoneNumbers)
    {
        try {
            // Conversation summary
            $summaryService = new ConversationSummaryService();
            $summary = $summaryService->generateSummary($conversation);
            $adminLink = $summaryService->generateAdminLink($conversation);

            // Format phones
            $phoneService = new PhoneNumberDetectionService();
            $formattedPhones = array_map(
                fn($p) => $phoneService->formatPhoneNumber($p),
                $phoneNumbers
            );

            // Build Telegram message
            $message = "📞 **YENİ TELEFON NUMARASI TOPLANDI!**\n\n";
            $message .= "**Telefon:** " . implode(', ', $formattedPhones) . "\n";
            $message .= "**Konuşma ID:** {$conversation->id}\n";
            $message .= "**Mesaj Sayısı:** {$conversation->message_count}\n\n";
            $message .= "**Admin Link:** {$adminLink}\n\n";
            $message .= "---\n{$summary}";

            // Send to Telegram
            TelegramMessage::create()
                ->to(config('services.telegram.chat_id'))
                ->content($message)
                ->send();

            Log::info('✅ Telegram notification sent', [
                'conversation_id' => $conversation->id,
                'phones' => $formattedPhones,
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Telegram notification failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
```

**Config:** `config/services.php` eklenecek:
```php
'telegram' => [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'chat_id' => env('TELEGRAM_CHAT_ID'),
],
```

---

### 3️⃣ Prompt Revampi (IxtifPromptService)

**Dosya:** `Modules/AI/app/Services/Tenant/IxtifPromptService.php`

**Eklenecek Kurallar:**

```php
// SATIŞ TONU (Yeni bölüm - en üste ekle)
$prompts[] = "**🎯 SATIŞ TONU VE YAKLAŞIM:**";
$prompts[] = "- Ürünleri ÖVEREK tanıt (kalite, dayanıklılık, avantajlar)";
$prompts[] = "- Olumlu dil kullan (❌ 'olmaz' yerine ✅ 'şöyle yapabiliriz')";
$prompts[] = "- Müşteriye güven ver ('En çok tercih edilen', 'Garantili')";
$prompts[] = "- Fayda odaklı konuş ('Bu sayede...', 'Böylece...')";
$prompts[] = "";

// MÜŞTERİYİ ANLAMA (Revize)
$prompts[] = "**🤔 MÜŞTERİYİ ANLAMA SÜRECİ:**";
$prompts[] = "1. İlk mesajda müşteri ne istediğini NET söylerse → Direkt ürün öner";
$prompts[] = "2. Belirsiz istek varsa → Seçenekler sun (tıklanabilir):";
$prompts[] = "   Örnek: 'Hangi tip makine arıyorsunuz?'";
$prompts[] = "   → Transpalet | Forklift | Reach Truck | İstif Makinesi";
$prompts[] = "3. Kullanıcı YAZAR mı TIKLAR mı → Sistem halleder";
$prompts[] = "";

// KNOWLEDGE BASE KULLANIMI (Yeni)
$prompts[] = "**📚 BİLGİ BANKASI KULLANIMI:**";
$prompts[] = "- SSS'leri BİL (sistem otomatik ekler)";
$prompts[] = "- Müşteri garanti, teslimat, servis sorarsa → Bilgi bankasından cevapla";
$prompts[] = "- Bilmediğin şey varsa → 'Detaylı bilgi için ekibimizle görüşelim' de";
$prompts[] = "";

// WHATSAPP & TELEFON (Güncelleme)
$prompts[] = "**📞 İLETİŞİM BİLGİLERİ PAYLAŞIMI:**";
$prompts[] = "- Önce müşterinin numarasını AL (ilgilendikten sonra)";
$prompts[] = "- Alamazsan → Bizim numarayı VER:";
$prompts[] = "  'Bize ulaşabilirsiniz: **0555 123 4567** (WhatsApp mevcut)'";
$prompts[] = "  WhatsApp linki: [WhatsApp ile İletişim](https://wa.me/905551234567)";
$prompts[] = "";
```

---

### 4️⃣ Tıklanabilir Buton Sistemi (Frontend)

**Dosya:** `resources/views/components/ai/chat-widget.blade.php` (veya inline)

**Yapılacak:**
- AI yanıtta `[BUTTON:Transpalet|Forklift|Reach Truck]` gibi özel syntax
- Frontend bu syntax'ı algılar ve buton render eder
- Kullanıcı butona tıklarsa → Mesaj otomatik gönderilir
- Kullanıcı yazarsa → Butonlar disabled olur

**JavaScript:**
```javascript
// AI yanıtını parse et
function parseAIResponse(content) {
    // [BUTTON:Option1|Option2|Option3] pattern'ini bul
    const buttonPattern = /\[BUTTON:(.*?)\]/g;
    let match;

    while ((match = buttonPattern.exec(content)) !== null) {
        const options = match[1].split('|');
        const buttons = options.map(opt =>
            `<button class="ai-quick-reply" data-message="${opt}">${opt}</button>`
        ).join('');

        content = content.replace(match[0], `<div class="ai-buttons">${buttons}</div>`);
    }

    return content;
}

// Butona tıklama
$(document).on('click', '.ai-quick-reply', function() {
    const message = $(this).data('message');
    sendMessage(message);

    // Butonları disable et
    $('.ai-quick-reply').prop('disabled', true).addClass('disabled');
});

// Kullanıcı yazarsa butonları kapat
$('#chat-input').on('input', function() {
    if ($(this).val().length > 0) {
        $('.ai-quick-reply').prop('disabled', true).addClass('disabled');
    }
});
```

---

### 5️⃣ Ürün Bilgisi Genişletme

**Dosya:** `PublicAIController.php` → `formatShopContext()`

**Eklenecek:**
```php
// Ürün tablosundan DAHA FAZLA bilgi
if (!empty($product)) {
    // Mevcut bilgilere ek:
    $formatted[] = "\n**🌟 ÜRÜN AVANTAJLARI (Satış Noktaları):**";
    $formatted[] = "- Yüksek kalite malzeme";
    $formatted[] = "- Uzun ömürlü kullanım";
    $formatted[] = "- Kapsamlı garanti";
    $formatted[] = "- Hızlı teslimat";
    $formatted[] = "- Profesyonel servis desteği";

    // Varyantlar daha detaylı
    // Brand bilgisi
    // Stock durumu (varsa)
}

// Kategorilerde de benzer detay ekle
```

---

### 6️⃣ Telegram Entegrasyonu (detectPhoneNumberAndLogToTelescope)

**Dosya:** `PublicAIController.php` (satır 502-566)

**Mevcut kod:**
```php
private function detectPhoneNumberAndLogToTelescope(AIConversation $conversation): void
{
    // ... mevcut kod ...

    if ($hasPhoneNumber && !empty($detectedPhones)) {
        // ... Telescope logging ...

        // 🆕 TELEGRAM BİLDİRİMİ EKLE:
        $telegramService = app(\Modules\AI\App\Services\TelegramNotificationService::class);
        $telegramService->sendPhoneNumberAlert($conversation, $detectedPhones);
    }
}
```

---

## 📁 DOSYA YAPISI (Değişiklik Yapılacak)

```
Modules/AI/
├── app/
│   ├── Http/Controllers/Api/
│   │   └── PublicAIController.php              ✏️ GÜNCELLE (Knowledge Base + Telegram)
│   ├── Services/
│   │   ├── TelegramNotificationService.php     ✨ YENİ OLUŞTUR
│   │   ├── PhoneNumberDetectionService.php     ✅ MEVCUT
│   │   ├── ConversationSummaryService.php      ✅ MEVCUT
│   │   └── Tenant/
│   │       └── IxtifPromptService.php           ✏️ GÜNCELLE (Satış tonu + Buton)

Modules/SettingManagement/
├── app/Models/
│   └── AIKnowledgeBase.php                      ✅ MEVCUT

config/
└── services.php                                  ✏️ GÜNCELLE (Telegram config)

.env
└── TELEGRAM_BOT_TOKEN & TELEGRAM_CHAT_ID         ✏️ EKLE

resources/views/components/ai/
└── chat-widget.blade.php (veya inline JS)        ✏️ GÜNCELLE (Buton parsing)
```

---

## ✅ TEST SENARYOLARı

### Senaryo 1: Belirsiz İstek
```
Müşteri: "Merhaba, bir şey arıyorum"
AI: "Merhaba! Size yardımcı olmaktan mutluluk duyarım. 😊
     Hangi tip makine arıyorsunuz?

     [BUTTON:Transpalet|Forklift|Reach Truck|İstif Makinesi]
```

### Senaryo 2: Net İstek
```
Müşteri: "2 ton elektrikli transpalet arıyorum"
AI: "Harika! Size 2 ton elektrikli transpaletlerimizi göstereyim:

     ⭐ [Litef EPT20 Elektrikli Transpalet](url)
     - 2000 kg taşıma kapasitesi
     - Lityum batarya (8 saat kullanım)
     - Kompakt tasarım ile dar alanlarda çalışma
     - **Sektörün en çok tercih edilen modeli!**

     Bu modeller özellikle dayanıklılığı ve uzun ömrü ile öne çıkıyor.
     Daha fazla detay için telefon numaranızı alabilir miyim? 📞"
```

### Senaryo 3: Knowledge Base Sorusu
```
Müşteri: "Garanti süresi ne kadar?"
AI: "Tüm ürünlerimizde 2 yıl garantimiz bulunmaktadır.
     Ayrıca isteğe bağlı 3. yıl uzatılmış garanti de sunuyoruz.

     Daha detaylı bilgi için: 0555 123 4567 (WhatsApp)"
```

### Senaryo 4: Numara Alınca
```
Müşteri: "0555 987 6543"
AI: "Teşekkür ederim! Ekibimiz en kısa sürede sizi arayacak. 🙏"

→ TELEGRAM'A GÖNDERİLİR:
📞 YENİ TELEFON NUMARASI TOPLANDI!
Telefon: 0555 987 6543
Konuşma ID: 123
Admin Link: https://ixtif.com/admin/ai/conversations/123
```

---

## 🚀 UYGULAMA SIRASI

1. ✅ Knowledge Base entegrasyonu (PublicAIController)
2. ✅ TelegramNotificationService oluştur
3. ✅ .env + config/services.php Telegram ayarları
4. ✅ IxtifPromptService revampi (satış tonu + buton syntax)
5. ✅ Frontend buton parsing (chat-widget.blade.php)
6. ✅ Ürün bilgisi genişletme (formatShopContext)
7. ✅ Telegram bildirimini aktif et (detectPhoneNumber)
8. ✅ Test senaryolarını çalıştır

---

## 📊 BEKLENTİ

- ✅ AI daha satış odaklı ve övücü
- ✅ Müşteri daha kolay anlaşılıyor (butonlar)
- ✅ Knowledge Base bilgileri entegre
- ✅ Numara alınca Telegram'a bildirim
- ✅ Ürünler daha detaylı anlatılıyor
- ✅ Olumsuz ifadelerden kaçınılıyor

---

**NOT:** Bu revizyon GLOBAL (PublicAIController) + TENANT-SPECIFIC (IxtifPromptService) dosyalarda yapılacak.
