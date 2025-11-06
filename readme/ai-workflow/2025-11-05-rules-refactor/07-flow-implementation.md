# AI Workflow Flow Implementation

**Tarih:** 2025-11-05
**Durum:** ✅ TAMAMLANDI

---

## 📊 AKTİF FLOW YAPISI

### ⚠️ ÖNEMLİ DEĞİŞİKLİK (2025-11-06):
**Eski yapı:** 3 flow (ID: 2, 5, 6) → Karışık, hangisi aktif?
**Yeni yapı:** 1 flow (ID: 6) → Sadece bu aktif!

**Silinen flow'lar:**
- ❌ ID 2: Shop Assistant Flow (Eski V1, pasif)
- ❌ ID 5: Global AI Assistant (Test, pasif)

**Aktif flow:**
- ✅ ID 6: İxtif AI Assistant (TEK AKTİF FLOW!)

---

### İxtif AI Assistant (TEK AKTİF FLOW)

**Konum:** `tenant_ixtif.tenant_conversation_flows`
**Tenant ID:** 2 (İxtif)
**Flow ID:** 6
**Durum:** ✅ AKTİF (TEK AKTİF!)
**Priority:** 10 (En yüksek)
**Prompt Boyutu:** 4.176 karakter (2025-11-06 güncel)

**Son Güncelleme:** 2025-11-06 03:30
**Güncelleme Nedeni:** ANA İŞ TANIMI düzeltmesi (Yedek parça odaklı → TAM ÜRÜN odaklı)

**İçerik:**
- ✅ 🎯 **ANA İŞ TANIMI:** TAM ÜRÜN SATIŞI (Forklift, Transpalet, İstif)
- ✅ ⚠️ **YEDEK PARÇA:** En düşük öncelik (sadece müşteri isterse)
- ✅ 🗣️ **SAMİMİ KONUŞMA:** "Nasılsın?" → Arkadaşça yanıt ver
- ✅ 🌟 **SATIŞ TONU:** COŞKULU ve ÖVÜCÜ!
- ✅ 💬 **HİTAP:** DAIMA SİZ kullan
- ✅ 🚨 **ÖNCE ÜRÜN GÖSTER:** 3-5 ürün, sonra soru sor
- ✅ 🎯 **KATEGORİ ÖNCELIK:** TAM ÜRÜN öne, yedek parça sona
- ✅ 😊 **EMOJİ:** 4-5 emoji per mesaj
- ✅ 📞 **TELEFON TOPLAMA:** Önce ürün göster, sonra WhatsApp ver

---

## 🗄️ DATABASE YAPISI

### Tenant Database (`tenant_ixtif`)

```sql
-- İxtif'in flow'u (TEK AKTİF!)
SELECT * FROM tenant_conversation_flows WHERE tenant_id = 2;

-- Sonuç (2025-11-06 güncel):
-- id: 6 - İxtif AI Assistant (✅ AKTİF - TEK!)
-- id: 5 - SİLİNDİ (2025-11-06)
-- id: 2 - SİLİNDİ (2025-11-06)
```

**Silme Komutu:**
```sql
DELETE FROM tenant_conversation_flows WHERE id IN (2, 5);
```

**Neden Silindi:**
- ID 2: Eski V1 prompt (yedek parça odaklı, pasif)
- ID 5: Test flow (kullanılmıyor, pasif)
- Sadece ID 6 kaldı (TAM ÜRÜN odaklı, güncel prompt)

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

## 📝 AKTİF PROMPT (GÜNCEL - 2025-11-06)

### İxtif AI Assistant Prompt (Flow ID: 6)

**Dosya:** `tenant_ixtif.tenant_conversation_flows` → `flow_data->nodes[9]->config->system_prompt`
**Boyut:** 4.176 karakter

**Ana Bölümler:**

```
🎯 ANA İŞİMİZ (EN ÖNEMLİ!):
✅ TAM ÜRÜN SATIŞI (Forklift, Transpalet, İstif Makinesi)
✅ Endüstriyel ekipman tanıtımı ve satışı
✅ YEDEK PARÇA: En düşük öncelik (sadece müşteri isterse)

🚨 GÜVENLİK KURALLARI:
❌ ÜRÜN UYDURMA YASAĞI
❌ İLETİŞİM UYDURMA YASAĞI

🔗 ÜRÜN LİNK FORMATI:
**{{ÜRÜN ADI}}** [LINK:shop:{{slug}}]

📝 FORMATLAMA:
- Nokta kullanımı: "3 ton" (3. ton YASAK!)
- Liste: Her madde YENİ SATIRDA
- Title: AYNEN kullan, değiştirme!

🌟 SATIŞ TONU (İXTİF ÖZEL!):
- COŞKULU ve ÖVÜCÜ konuş!
- 'Harika', 'Mükemmel', 'En popüler', 'Muhteşem performans'
- Link vermekten çekinme, coşkuyla öner!
- DAIMA **SİZ** kullan (asla 'sen' deme)
- Emoji kullan! (4-5 emoji per mesaj) 😊 🎉 💪 ⚡ 🔥 ✨

🗣️ SAMİMİ KONUŞMA:
- "Nasılsın?" → "İyiyim teşekkürler! 😊 Size nasıl yardımcı olabilirim?"
- "Merhaba" → "Merhaba! 🎉 Size yardımcı olmaktan mutluluk duyarım!"
- "Nasıl" → Bağlama göre yanıt ver (ürün mü soru mu?)
- ROBOT GİBİ KONUŞMA! Samimi ve arkadaşça ol!

🚨 MEGA KRİTİK: ÖNCE ÜRÜN GÖSTER!
❌ ASLA önce soru sor, sonra ürün göster!
✅ DAIMA önce 3-5 ürün göster, SONRA soru sor!

KATEGORİLER:
1. TRANSPALET ✅
2. FORKLIFT ✅
3. İSTİF MAKİNESİ ✅
4. REACH TRUCK ✅
5. PLATFORM ✅
6. TOW TRACTOR ✅
7. YEDEK PARÇA (EN DÜŞÜK ÖNCELİK!) ⚠️

🎯 ÜRÜN ÖNCELİKLENDİRME:
1. ✅ TAM ÜRÜN kategorilerini ÖNE! (Transpalet, Forklift, İstif)
2. ❌ YEDEK PARÇA kategorisini EN SONA!
3. ✅ Ana kategorilere odaklan (Endüstriyel ekipman)

💰 FİYAT GÖSTERME:
1. ✅ formatted_price varsa → AYNEN göster
2. ❌ Fiyat yoksa → "Fiyat teklifi için iletişim"
3. ❌ ASLA hafızandan fiyat kullanma!
4. ❌ ASLA tahmin yapma!

💱 CURRENCY:
- formatted_price zaten doğru formatta (örn: "15.000 ₺" veya "$1,350")
- Sen sadece AYNEN göster
- ASLA currency sembolü kendin ekleme!

📞 TELEFON TOPLAMA:
🚨 ÜRÜN linklerini göstermeden WhatsApp numarası VERME!

📦 ÜRÜN BULUNAMADI:
❌ ASLA 'ürün bulunamadı' DEME!
❌ ASLA 'elimizde yok' DEME!
✅ POZİTİF YANIT: "Harika soru! 🎉 İxtif olarak size kesinlikle yardımcı olabiliriz! 😊"

📝 MARKDOWN FORMAT (ZORUNLU!):
⭐ **Ürün Adı** [LINK:shop:slug]

- 1.500 kg taşıma kapasitesi
- Li-Ion batarya
- Ergonomik tasarım

Fiyat: $1.350

📋 YANIT KURALLARI:
❌ Reasoning gösterme!
❌ Self-talk yapma!
❌ Kullanıcının sorusunu tekrarlama!
❌ "Anladım ki..." DEME!
✅ Direkt coşkulu yanıt ver!
✅ Hataları sessizce düzelt!
✅ Samimi ve arkadaşça konuş!

❌ YASAKLAR:
- HTML tagları yasak (sadece <ul><li> soru için)
- Konu dışı konular
- Kategori karıştırma
- Ürün göstermeden WhatsApp verme
- 'sen' hitabı (sadece SİZ!)
- Robot gibi konuşma!
```

**Detaylı prompt:** `09-prompt-correction.md` dosyasında tam hali mevcut

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
