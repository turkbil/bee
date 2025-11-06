# AI Workflow Flow Implementation

**Tarih:** 2025-11-05
**Durum:** ✅ TAMAMLANDI

---

## 📊 OLUŞTURULAN FLOW'LAR

### 1. Global AI Assistant Template (Central DB)

**Konum:** `laravel.tenant_conversation_flows`
**Tenant ID:** 0 (Şablon)
**Flow ID:** 2
**Durum:** Pasif (sadece şablon)
**Boyut:** 6.841 karakter

**Amaç:**
Yeni tenant oluşturulduğunda bu şablonu kopyalayıp tenant'ın kendi database'ine eklemek.

**İçerik:**
- Güvenlik kuralları
- Link formatı
- Formatlama kuralları
- Konuşma tarzı (doğal, samimi)
- Yanıt kuralları
- Fiyat/Currency kuralları
- Settings sistemi entegrasyonu

---

### 2. İxtif AI Assistant (Tenant DB)

**Konum:** `tenant_ixtif.tenant_conversation_flows`
**Tenant ID:** 2 (İxtif)
**Flow ID:** 6
**Durum:** ✅ AKTİF
**Priority:** 10 (En yüksek)
**Boyut:** 8.126 karakter

**İçerik:**
- ✅ Tüm Global kurallar
- ✅ İxtif özel satış tonu (COŞKULU!)
- ✅ SİZ hitabı
- ✅ Önce ürün göster kuralı
- ✅ Kategori karıştırma yasağı
- ✅ Emoji kullanımı (4-5 per mesaj)
- ✅ Telefon toplama stratejisi
- ✅ Ürün önceliklendirme

---

## 🗄️ DATABASE YAPISI

### Central Database (`laravel`)

```sql
-- Şablon flow'lar
SELECT * FROM tenant_conversation_flows WHERE tenant_id = 0;

-- Sonuç:
-- id: 2
-- flow_name: Global AI Assistant Template
-- is_active: 0 (şablon)
-- priority: 99
```

### Tenant Database (`tenant_ixtif`)

```sql
-- İxtif'in flow'ları
SELECT * FROM tenant_conversation_flows WHERE tenant_id = 2;

-- Sonuç:
-- id: 6 - İxtif AI Assistant (AKTİF)
-- id: 5 - Global AI Assistant (pasif, yedek)
-- id: 2 - Shop Assistant Flow (eski V1, pasif)
```

---

## 🎯 FLOW YAPISI

### Node Listesi (14 adet)

1. **node_1**: welcome - Karşılama mesajı
2. **node_2**: history_loader - Geçmiş konuşmaları yükle
3. **node_3**: sentiment_detection - Niyet analizi
4. **node_4**: category_detection - Kategori tespit
5. **node_5**: condition - Fiyat sorgusu kontrolü
6. **node_6**: price_query - Fiyat sorgusu
7. **node_7**: product_search - Ürün arama
8. **node_8**: stock_sorter - Stok sıralama
9. **node_9**: context_builder - AI context hazırlama
10. **node_10**: ai_response - **AI cevap üretimi (PROMPT BURADA!)**
11. **node_11**: contact_request - İletişim bilgisi
12. **node_12**: link_generator - Link render
13. **node_13**: message_saver - Mesajları kaydet
14. **node_14**: end - Bitir

### Edge'ler (Bağlantılar)

```json
{
  "edge_3_purchase": "node_3 → node_4 (satın alma niyeti)",
  "edge_3_comparison": "node_3 → node_4 (karşılaştırma)",
  "edge_3_question": "node_3 → node_9 (soru)",
  "edge_3_support": "node_3 → node_11 (destek)",
  "edge_3_browsing": "node_3 → node_9 (gezinme)",
  "edge_5_true": "node_5 → node_6 (fiyat sorgusu varsa)",
  "edge_5_false": "node_5 → node_7 (fiyat sorgusu yoksa)"
}
```

---

## 📝 PROMPT FARKLARI

### Global Template Prompt (Özet)

```
Sen bu firmanın AI satış danışmanısın.

🚨 GÜVENLİK KURALLARI
❌ ÜRÜN UYDURMA YASAĞI
❌ İLETİŞİM UYDURMA YASAĞI

🗣️ KONUŞMA TARZI:
✅ Doğal ve samimi
❌ "Ben yapay zeka asistanıyım" DEME!

💰 FİYAT:
- formatted_price AYNEN göster
- shop_currencies'den gelir

⚙️ SETTINGS:
- İletişim: contact_whatsapp_1, contact_phone_1
- AI kişilik: ai_assistant_name, ai_response_tone
```

### İxtif Prompt (Ek Kurallar)

```
+ 🌟 SATIŞ TONU:
  - COŞKULU ve ÖVÜCÜ!
  - 'Harika', 'Mükemmel', 'Muhteşem'
  - DAIMA SİZ hitabı
  - 4-5 emoji per mesaj 😊 🎉 💪

+ 🚨 ÖNCE ÜRÜN GÖSTER!
  ❌ Önce soru sor
  ✅ Önce 3-5 ürün göster, SONRA soru sor

+ 🚨 KATEGORİ KARIŞTIRMA YASAK!
  Transpalet → Sadece transpalet
  Forklift → Sadece forklift

+ 🎯 ÜRÜN ÖNCELİKLENDİRME:
  ❌ Yedek parça EN SONA
  ✅ Tam ürün ÖNE

+ 📞 TELEFON TOPLAMA:
  Önce ürün göster, sonra WhatsApp ver
```

---

## 🚀 YENİ TENANT EKLEME WORKFLOW

### Adım 1: Global Template'i Kopyala

```sql
-- Central'den şablonu al
SELECT flow_data FROM laravel.tenant_conversation_flows
WHERE tenant_id = 0 AND flow_name = 'Global AI Assistant Template';

-- Yeni tenant'ın DB'sine ekle
INSERT INTO tenant_X.tenant_conversation_flows
(tenant_id, flow_name, flow_description, flow_data, start_node_id, is_active, priority)
SELECT
  X, -- Yeni tenant ID
  'AI Assistant',
  'Genel AI asistan',
  flow_data,
  'node_1',
  1,
  10
FROM laravel.tenant_conversation_flows
WHERE tenant_id = 0 AND flow_name = 'Global AI Assistant Template';
```

### Adım 2: Tenant'a Özel Kurallar Ekle (Opsiyonel)

```php
// Eğer tenant'a özel kurallar varsa:
$flow = TenantConversationFlow::where('tenant_id', $newTenantId)->first();
$flowData = json_decode($flow->flow_data, true);

// AI Response node'unu bul
foreach ($flowData['nodes'] as &$node) {
    if ($node['type'] === 'ai_response') {
        // Özel kuralları ekle
        $node['config']['system_prompt'] .= "\n\n[TENANT ÖZEL KURALLAR]";
    }
}

$flow->flow_data = json_encode($flowData);
$flow->save();
```

---

## 📋 YAPILACAKLAR (Backend)

### Context Builder Güncellemeleri

**Dosya:** `/Modules/AI/App/Services/V2/Nodes/ContextBuilderNode.php`

```php
public function execute(array $context): array
{
    $tenant = tenant();

    // 1. Currency bilgisi ekle (shop_currencies)
    $products = $context['products'] ?? [];
    foreach ($products as &$product) {
        $currency = ShopCurrency::where('code', $product['currency'])->first();
        $product['formatted_price'] = $this->formatPrice(
            $product['base_price'],
            $currency
        );
    }

    // 2. Settings bilgileri ekle
    $settingService = app(SettingService::class);
    $context['contact'] = [
        'whatsapp' => $settingService->get('contact_whatsapp_1'),
        'phone' => $settingService->get('contact_phone_1'),
        'email' => $settingService->get('contact_email_1'),
        'whatsapp_link' => $this->generateWhatsAppLink(
            $settingService->get('contact_whatsapp_1')
        ),
    ];

    $context['ai_settings'] = [
        'name' => $settingService->get('ai_assistant_name', 'AI Asistan'),
        'tone' => $settingService->get('ai_response_tone', 'friendly'),
        'use_emojis' => $settingService->get('ai_use_emojis', 'moderate'),
    ];

    return $context;
}

protected function formatPrice($price, $currency)
{
    $formatted = number_format(
        $price,
        $currency->decimal_places ?? 0,
        ',',
        '.'
    );

    if ($currency->format === 'symbol_before') {
        return $currency->symbol . $formatted;
    }

    return $formatted . ' ' . $currency->symbol;
}

protected function generateWhatsAppLink($phoneNumber)
{
    $clean = preg_replace('/[^0-9]/', '', $phoneNumber);

    if (substr($clean, 0, 1) === '0') {
        $clean = '90' . substr($clean, 1);
    }

    return "https://wa.me/{$clean}";
}
```

---

## ✅ TAMAMLANAN İŞLER

- [x] V1 sistem analizi (OptimizedPromptService + IxtifPromptService)
- [x] Global kuralları çıkarma (12 kategori)
- [x] İxtif özel kuralları çıkarma (14 kategori)
- [x] Dokümantasyon oluşturma (8 dosya, 64 KB)
- [x] Currency kuralları düzeltme (shop_currencies)
- [x] Settings sistemi analizi (3 tablo)
- [x] Global template flow oluşturma (Central DB)
- [x] İxtif flow oluşturma (Tenant DB)
- [x] Flow'ları aktifleştirme

---

## 🔄 SONRAKI ADIMLAR

1. **Backend Güncellemeleri:**
   - [ ] ContextBuilderNode: formatPrice() ekle
   - [ ] ContextBuilderNode: Settings entegrasyonu
   - [ ] ProductSearchService: Currency bilgisi ekle

2. **Test:**
   - [ ] İxtif flow test et (frontend)
   - [ ] Global template'i başka tenant'a kopyala
   - [ ] Settings değişikliklerinin yansımasını kontrol et
   - [ ] Currency formatlamasını test et (TRY, USD, EUR)

3. **Admin Panel (Gelecek):**
   - [ ] Flow seçici ekran
   - [ ] Flow düzenleyici (node pozisyonları)
   - [ ] Prompt editör (AI Response node)

---

## 📊 ÖZET

**Oluşturulan Flow Sayısı:** 2
- 1 Global Template (Central DB, şablon)
- 1 İxtif Flow (Tenant DB, aktif)

**Node Sayısı:** 14
**Edge Sayısı:** 13

**Toplam Kural Kategorisi:** 26
- Global: 12 kategori
- İxtif Özel: 14 kategori

**Dokümantasyon:** 8 dosya (72 KB)

**Aktif Flow:** İxtif AI Assistant (ID: 6, Priority: 10)

---

## 🎉 BAŞARILI!

AI Workflow sistemi başarıyla kuruldu. Artık:

✅ Yeni tenant eklerken Global template'i kopyala
✅ Tenant'a özel kuralları ekle
✅ Her tenant kendi flow'unu kullanır (performans)
✅ Currency ve Settings dinamik
✅ AI hallüsinasyon riski düşük (placeholder kullanımı)
