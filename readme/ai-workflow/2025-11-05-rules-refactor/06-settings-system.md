# Settings Sistemi - İletişim & AI Ayarları

## 📊 TABLO YAPISI

### Central Database: `laravel`

```
settings (Central - Tanımlar)
├── id
├── group_id → settings_groups.id
├── label (Görünen ad)
├── key (Benzersiz anahtar)
├── type (text, email, select, textarea, json)
├── options (JSON - select için seçenekler)
├── default_value
├── sort_order
├── is_active
├── is_system
└── is_required

settings_groups (Central - Gruplar)
├── id
├── parent_id (Alt grup için)
├── prefix
├── name
├── slug
├── description
├── icon
└── is_active
```

### Tenant Database: `tenant_*`

```
settings_values (Tenant - Değerler)
├── id
├── setting_id → laravel.settings.id
├── value (Tenant'a özel değer)
├── created_at
└── updated_at
```

---

## 📞 İLETİŞİM AYARLARI

### Central Tanımlar

| ID | Label | Key | Type |
|----|-------|-----|------|
| 4 | Ana E-posta Adresi | `site_email` | email |
| 56 | Ana Telefon | `contact_phone_1` | text |
| 57 | Alternatif Telefon 1 | `contact_phone_2` | text |
| 58 | Alternatif Telefon 2 | `contact_phone_3` | text |
| 59 | Ana WhatsApp | `contact_whatsapp_1` | text |
| 60 | Destek WhatsApp | `contact_whatsapp_2` | text |
| 61 | Satış WhatsApp | `contact_whatsapp_3` | text |
| 62 | Genel E-posta | `contact_email_1` | email |
| 63 | Destek E-posta | `contact_email_2` | email |
| 64 | Satış E-posta | `contact_email_3` | email |

### İxtif Tenant Değerleri (Örnek)

| Key | Value |
|-----|-------|
| `contact_phone_1` | 0216 755 3 555 |
| `contact_whatsapp_1` | 0501 005 67 58 |
| `whatsapp_enabled` | 1 |

---

## 🤖 AI AYARLARI

### Temel AI Ayarları

| ID | Label | Key | Type |
|----|-------|-----|------|
| 18 | AI Asistan İsmi | `ai_assistant_name` | text |
| 19 | AI Kişiliği / Rol | `ai_personality_role` | select |
| 20 | Firma Sektörü | `ai_company_sector` | text |
| 21 | Firma Kuruluş Yılı | `ai_company_founded_year` | text |
| 22 | Firma Ana Hizmetleri | `ai_company_main_services` | textarea |
| 23 | Firma Uzmanlaştığı Alanlar | `ai_company_expertise` | textarea |
| 24 | Hedef Müşteri Profili | `ai_target_customer_profile` | select |
| 25 | Hedef Sektörler | `ai_target_industries` | textarea |

### AI Davranış Ayarları

| ID | Label | Key | Type |
|----|-------|-----|------|
| 36 | Yanıt Tarzı | `ai_response_tone` | select |
| 37 | Emoji Kullanımı | `ai_use_emojis` | select |
| 38 | Yanıt Uzunluğu | `ai_response_length` | select |
| 39 | Satış Yaklaşımı | `ai_sales_approach` | select |
| 40 | CTA Sıklığı | `ai_cta_frequency` | select |
| 41 | Fiyat Gösterme Politikası | `ai_price_policy` | select |

### AI Özel Talimatlar

| ID | Label | Key | Type |
|----|-------|-----|------|
| 42 | Özel Talimatlar | `ai_custom_instructions` | textarea |
| 43 | Yasaklı Konular | `ai_forbidden_topics` | textarea |
| 44 | Firma Sertifikaları | `ai_company_certifications` | textarea |
| 45 | Referans Sayısı | `ai_company_reference_count` | text |
| 47 | Bilgi Bankası (FAQ) | `ai_knowledge_base` | json |

---

## 🔧 KULLANIM

### Laravel'de Settings Çekme

```php
use Modules\Settings\App\Services\SettingService;

// Settings service
$settingService = app(SettingService::class);

// Tek ayar
$whatsapp = $settingService->get('contact_whatsapp_1');
// "0501 005 67 58"

$aiName = $settingService->get('ai_assistant_name');
// "İxtif Asistan"

// Varsayılan değer ile
$phone = $settingService->get('contact_phone_1', '0800 000 00 00');

// Grup bazında tüm ayarlar
$contactSettings = $settingService->group('contact');
/*
[
    'contact_phone_1' => '0216 755 3 555',
    'contact_whatsapp_1' => '0501 005 67 58',
    'contact_email_1' => 'info@ixtif.com',
    ...
]
*/

$aiSettings = $settingService->group('ai');
/*
[
    'ai_assistant_name' => 'İxtif Asistan',
    'ai_personality_role' => 'professional_salesperson',
    'ai_response_tone' => 'friendly',
    'ai_use_emojis' => 'moderate',
    ...
]
*/
```

### Helper Fonksiyon (Eğer varsa)

```php
// Global helper
$whatsapp = settings('contact_whatsapp_1');

// Grup helper
$contactInfo = settings()->group('contact');
```

---

## 💬 AI PROMPT İÇİN KULLANIM

### İletişim Bilgilerini Çekme

```php
// AI Context Builder içinde
$contactInfo = [
    'phone' => settings('contact_phone_1'),
    'whatsapp' => settings('contact_whatsapp_1'),
    'whatsapp_link' => $this->generateWhatsAppLink(
        settings('contact_whatsapp_1')
    ),
    'email' => settings('contact_email_1'),
];

// AI Context'e ekle
$aiContext['contact'] = $contactInfo;
```

### AI Prompt'a Ekleme

```markdown
**📞 İLETİŞİM BİLGİLERİ (Sadece gerektiğinde kullan):**

💬 **WhatsApp:** [{{contact.whatsapp}}]({{contact.whatsapp_link}})
📞 **Telefon:** {{contact.phone}}
📧 **E-posta:** [{{contact.email}}](mailto:{{contact.email}})

**KURALLAR:**
- ÜRÜN linklerini göstermeden WhatsApp numarası VERME!
- Önce ürünleri göster, sonra iletişim bilgisi ver
- Telefon numarası AYNEN kullan, değiştirme!
```

### AI Kişilik Ayarlarını Kullanma

```php
// AI davranışını settings'ten çek
$aiPersonality = [
    'name' => settings('ai_assistant_name', 'AI Asistan'),
    'role' => settings('ai_personality_role', 'professional_salesperson'),
    'tone' => settings('ai_response_tone', 'friendly'),
    'use_emojis' => settings('ai_use_emojis', 'moderate'),
    'response_length' => settings('ai_response_length', 'medium'),
    'sales_approach' => settings('ai_sales_approach', 'consultative'),
    'price_policy' => settings('ai_price_policy', 'show_always'),
];

// Prompt'a ekle
$systemPrompt = "Sen {$aiPersonality['name']} adında {$aiPersonality['role']} rolünde bir asistansın.";
```

---

## 🎯 İXTİF ÖZEL AYARLAR (Öneri)

### Tenant-Specific Settings

```sql
-- İxtif için özel ayarlar
INSERT INTO tenant_ixtif.settings_values (setting_id, value) VALUES
(19, 'enthusiastic_salesperson'),  -- ai_personality_role
(36, 'enthusiastic'),               -- ai_response_tone
(37, 'high'),                       -- ai_use_emojis (4-5 per mesaj)
(38, 'short'),                      -- ai_response_length (max 2 cümle)
(39, 'aggressive'),                 -- ai_sales_approach (coşkulu!)
(40, 'high'),                       -- ai_cta_frequency
(41, 'show_always'),                -- ai_price_policy
(42, 'DAIMA SİZ hitabı kullan. Önce ürün göster, sonra soru sor!'), -- ai_custom_instructions
(43, 'Sivas kangal, hava durumu, siyaset, din'); -- ai_forbidden_topics
```

---

## 📋 YAPILACAKLAR

### AI Context Builder Güncellemeleri

1. [ ] `contact_whatsapp_1` çek
2. [ ] `contact_phone_1` çek
3. [ ] `contact_email_1` çek
4. [ ] WhatsApp linki oluştur (wa.me format)
5. [ ] AI Context'e `contact` objesi ekle

### AI Prompt Güncellemeleri

1. [ ] İletişim bilgileri placeholder'larını ekle
2. [ ] {{contact.whatsapp}} kullan (hardcode değil!)
3. [ ] {{contact.whatsapp_link}} kullan
4. [ ] AI Kişilik ayarlarını kontrol et
5. [ ] Tenant'a göre ayarları uygula

### Admin Panel (Gelecek)

1. [ ] Settings sayfası zaten var (muhtemelen)
2. [ ] AI Ayarları sekmesi kontrol et
3. [ ] İletişim Ayarları sekmesi kontrol et
4. [ ] Tenant'a özel değer kaydetme kontrol et

---

## ⚠️ KRİTİK NOTLAR

### 1. Hardcode Yasak!

**❌ YANLIŞ:**
```php
$whatsapp = '0534 515 2626';  // Hardcode!
```

**✅ DOĞRU:**
```php
$whatsapp = settings('contact_whatsapp_1');  // Settings'ten çek!
```

### 2. Fallback Değer

```php
// Eğer setting yoksa fallback kullan
$whatsapp = settings('contact_whatsapp_1', '0800 000 00 00');

// Veya hata ver
if (!settings('contact_whatsapp_1')) {
    throw new \Exception('WhatsApp numarası ayarlanmamış!');
}
```

### 3. WhatsApp Link Formatı

```php
protected function generateWhatsAppLink($phoneNumber)
{
    // Format: 0534 515 26 26 → 905345152626
    $clean = preg_replace('/[^0-9]/', '', $phoneNumber);

    // Başında 0 varsa 90 ile değiştir
    if (substr($clean, 0, 1) === '0') {
        $clean = '90' . substr($clean, 1);
    }

    return "https://wa.me/{$clean}";
}
```

### 4. Tenant Context

```php
// DAIMA tenant context'te çalış
$whatsapp = settings('contact_whatsapp_1');  // Otomatik tenant-aware

// Veya manuel tenant belirt
$whatsapp = settings('contact_whatsapp_1', null, tenant('id'));
```

---

## ✅ SONUÇ

**Settings Sistemi:**
- ✅ Central: Tanımlar (`laravel.settings`)
- ✅ Central: Gruplar (`laravel.settings_groups`)
- ✅ Tenant: Değerler (`tenant_*.settings_values`)

**İletişim Bilgileri:**
- ✅ `contact_whatsapp_1` → Ana WhatsApp
- ✅ `contact_phone_1` → Ana Telefon
- ✅ `contact_email_1` → Ana E-posta

**AI Ayarları:**
- ✅ `ai_assistant_name` → AI ismi
- ✅ `ai_personality_role` → Kişilik/Rol
- ✅ `ai_response_tone` → Yanıt tarzı
- ✅ `ai_use_emojis` → Emoji kullanımı
- ✅ `ai_custom_instructions` → Özel talimatlar

**Kullanım:**
- ✅ `settings('key')` ile çek
- ✅ `settings()->group('contact')` ile grup çek
- ✅ AI Context'e ekle
- ✅ Prompt'ta placeholder kullan ({{contact.whatsapp}})
- ❌ Hardcode YASAK!
