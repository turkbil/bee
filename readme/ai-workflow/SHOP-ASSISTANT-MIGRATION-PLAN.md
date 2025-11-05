# 🛍️ Shop Assistant Migration Plan

**Tarih**: 2025-11-05
**Amaç**: Mevcut shop assistant sistemini yeni workflow sistemine migrate etmek

---

## 📋 SİSTEM MİMARİSİ

### 🌍 GLOBAL NODES (Tüm E-Ticaret Siteleri Kullanabilir)

**Location:** `app/Services/ConversationNodes/Common/`

| # | Node Key | Açıklama | Kullanım |
|---|----------|----------|----------|
| 1 | `welcome` | Karşılama mesajı | İlk mesaj |
| 2 | `ai_response` | AI cevap üretme | Her mesajda |
| 3 | `context_builder` | Context hazırlama | AI'ya gönderilecek bilgiler |
| 4 | `history_loader` | Konuşma geçmişi | Son 10 mesaj |
| 5 | `message_saver` | Mesaj kaydetme | Her mesaj sonrası |
| 6 | `sentiment_detection` | Kullanıcı niyeti | purchase_intent, question, etc. |
| 7 | `link_generator` | Link oluşturma | [LINK:shop:product:slug] → URL |
| 8 | `condition` | Şart kontrolü | IF/ELSE mantığı |
| 9 | `collect_data` | Veri toplama | Form alanları |
| 10 | `end` | Sohbet bitişi | Son node |

---

### 🛒 SHOP MODULE NODES (Shop Modülü Olan Siteler)

**Location:** `app/Services/ConversationNodes/Shop/`

| # | Node Key | Açıklama | Kullanım |
|---|----------|----------|----------|
| 1 | `product_search` | Ürün arama | Meilisearch + DB fallback |
| 2 | `price_query` | Fiyat sorgusu | DB'den fiyat çekme |
| 3 | `category_detection` | Kategori algılama | "transpalet" → Kategori ID |
| 4 | `currency_converter` | Döviz çevirici | TL/USD/EUR (site güncel kur) |
| 5 | `product_comparison` | Ürün karşılaştırma | İki ürün farklarını listele |
| 6 | `contact_request` | İletişim isteği | "Sizi arayalım" formu |
| 7 | `stock_sorter` | Stok sıralama | Homepage → Çok stoklu → Normal |

---

## 🎯 TENANT ÖZELLEŞTİRMELERİ (iXtif Tenant ID: 2)

### 1. Kategori Önceliklendirme

**Yüksek Öncelik (İlk öner):**
- Transpalet
- Forklift
- İstif Makinaları

**Düşük Öncelik (Sadece açıkça söylenirse):**
- Yedek Parça (ID: 44)
- Aksesuar

**Config:**
```json
{
    "category_priority": {
        "high": ["transpalet", "forklift", "istif-makinasi"],
        "low": ["yedek-parca"],
        "exclude_unless_explicit": [44]
    }
}
```

---

### 2. Kategori Bazlı Özel Sorular

**Transpalet Kategorisi:**
```
Sorular:
- Hangi kapasite? (1.5 ton, 2 ton, 2.5 ton, 3 ton)
- Manuel mi elektrikli mi?
- Lift yüksekliği ne kadar olmalı?
- Çatal boyu tercihiniz?
```

**Forklift Kategorisi:**
```
Sorular:
- Hangi kapasite? (2 ton, 3 ton, 5 ton, 7 ton)
- Dizel mi, elektrikli mi, LPG mi?
- İç mekan mı, dış mekan mı?
- Kaldırma yüksekliği?
```

**Config:**
```json
{
    "category_questions": {
        "transpalet": [
            {"key": "capacity", "question": "Hangi kapasite transpalet arıyorsunuz?", "options": ["1.5 ton", "2 ton", "2.5 ton", "3 ton"]},
            {"key": "type", "question": "Manuel mi, elektrikli mi?", "options": ["Manuel", "Elektrikli", "Yarı elektrikli"]},
            {"key": "lift_height", "question": "Lift yüksekliği ne kadar olmalı?", "options": ["Standart (800mm)", "Yüksek kaldırma (1600mm)", "Platformlu"]}
        ],
        "forklift": [
            {"key": "capacity", "question": "Hangi kapasite forklift arıyorsunuz?", "options": ["2 ton", "3 ton", "5 ton", "7 ton"]},
            {"key": "fuel_type", "question": "Yakıt tipi tercihiniz?", "options": ["Dizel", "Elektrikli", "LPG"]},
            {"key": "usage_area", "question": "İç mekan mı, dış mekan mı kullanacaksınız?", "options": ["İç mekan", "Dış mekan", "Her ikisi"]}
        ]
    }
}
```

---

### 3. Stok Sıralama Mantığı

**Sıralama:**
1. ✅ Homepage'de gösterilen ürünler (featured)
2. ✅ Yüksek stoklu ürünler (stock > 10)
3. ✅ Normal stoklu ürünler (stock > 0)
4. ❌ Stok 0 olan ürünler dahil ETME

**Stok 0 Ürün İçin Cevap:**
```
"Bu ürün şu anda stokta bulunmamaktadır.
Müşteri temsilcilerimizin bu konuda sizinle iletişime geçmesi için
numaranızı paylaşabilir misiniz?

Paylaşmak istemezseniz, bizi şu numaradan arayabilirsiniz:
📞 [PHONE_NUMBER]
💬 WhatsApp: [WHATSAPP_LINK]"
```

**Config:**
```json
{
    "stock_sorting": {
        "priority_order": ["featured", "high_stock", "normal_stock"],
        "high_stock_threshold": 10,
        "exclude_out_of_stock": false,
        "out_of_stock_response": "contact_request"
    }
}
```

---

### 4. Fiyat Gösterimi

**Mevcut Durum:**
- KDV HARİÇ fiyat göster
- "KDV sonradan eklenir" notu ekle

**Gelecek:**
- Müşteriye göre seçmeli (B2B: KDV hariç, B2C: KDV dahil)
- Şimdilik KDV hariç

**Config:**
```json
{
    "price_display": {
        "show_vat": false,
        "vat_rate": 20,
        "vat_note": "Fiyatlarımız KDV hariçtir. KDV sonradan eklenir.",
        "future_b2b_b2c_toggle": true
    }
}
```

**Örnek Cevap:**
```
"İxtif F4 Forklift 2 Ton fiyatı: 450,000 TL (KDV hariç)
📌 Not: Fiyatlarımız KDV hariçtir. KDV sonradan eklenir.

[Link: Ürün Detayı]"
```

---

### 5. Ürün Karşılaştırma

**Soru:** "F4 ile CPD18TVL'yi karşılaştır"

**Cevap Formatı:**
```
İxtif F4 Forklift ile Lonking CPD18TVL Forklift karşılaştırması:

FARKLAR:
✅ F4 Avantajları:
   - Daha düşük fiyat (450,000 TL vs 520,000 TL)
   - Daha kompakt boyut (iç mekan için uygun)

✅ CPD18TVL Avantajları:
   - Daha yüksek kaldırma kapasitesi (4500mm vs 3000mm)
   - Dizel motor (elektriğe göre daha uzun kullanım)

ORTAK ÖZELLİKLER:
- Her ikisi de 1.8 ton kapasite
- Side shift özelliği
- Otomatik vites

[Link: F4 Detayı] | [Link: CPD18TVL Detayı]
```

**Config:**
```json
{
    "comparison_format": "differences_only",
    "show_advantages": true,
    "show_common_features": true
}
```

---

### 6. Teklif/Sipariş İsteği

**Mevcut:**
- "Sizi Arayalım" linki var

**Gelecek:**
- Teklif formu eklenecek

**Soru:** "Teklif istiyorum"

**Cevap:**
```
Elbette! Size detaylı teklif hazırlayalım.

Aşağıdaki linkten telefon numaranızı bırakabilirsiniz,
en kısa sürede sizi arayalım:

🔗 [Sizi Arayalım Formu]

Ya da direkt olarak bize ulaşabilirsiniz:
📞 [PHONE_NUMBER]
💬 WhatsApp: [WHATSAPP_LINK]
```

**Config:**
```json
{
    "quotation_form_enabled": false,
    "callback_form_url": "/contact/callback",
    "telegram_notification": true,
    "show_contact_info": true
}
```

---

### 7. İletişim Bilgileri

**Kaynak:** Settings Values → `contact_info` group

**Veriler:**
```php
settings()->get('contact_info.phone')       // Telefon
settings()->get('contact_info.whatsapp')    // WhatsApp link
settings()->get('contact_info.email')       // Email
settings()->get('contact_info.address')     // Adres
```

**Soru:** "Nasıl ulaşabilirim?"

**Cevap:**
```
Bize aşağıdaki kanallardan ulaşabilirsiniz:

📞 Telefon: [PHONE_NUMBER]
💬 WhatsApp: [WHATSAPP_LINK]
✉️ Email: [EMAIL]
📍 Adres: [ADDRESS]

Çalışma Saatlerimiz: Pazartesi-Cuma 09:00-18:00
```

---

### 8. Çalışma Saatleri

**Durum:** Mesai dışı saat fark etmez

**Not:** Gelecekte "Mesai saatleri dışındasınız, size dönelim mi?" özelliği eklenebilir

---

### 🚨 9. KRİTİK KURAL: HALÜSİNASYON YASAK!

**SORUN:** AI dünyadan örnek ürünler veriyor (kendi database'imizde olmayan)

**KURAL:**
```
❌ ASLA dünyadan örnek verme (Toyota, Nissan, vb.)
❌ ASLA hayali ürün önerme
❌ ASLA "genelde şu özelliklere sahiptir" deme

✅ SADECE veritabanındaki ürünlerden bahset
✅ Yoksa: "Bu özellikte ürünümüz şu anda bulunmamaktadır"
✅ Yönlendir: "Müşteri temsilcilerimiz size yardımcı olabilir"
```

**Örnek Cevap (Ürün Yoksa):**
```
"2 ton elektrikli forklift kategorisinde şu anda stoklarımızda uygun ürün bulunmamaktadır.

Ancak müşteri temsilcilerimiz bu konuda size özel çözüm önerileri sunabilir.

Sizinle iletişime geçmemiz için telefon numaranızı paylaşabilir misiniz?
Alternatif olarak bizi şu numaradan arayabilirsiniz:
📞 [PHONE_NUMBER]
💬 WhatsApp: [WHATSAPP_LINK]"
```

**System Prompt'a Eklenecek Direktif:**
```
KRITIK KURAL:
- SADECE veritabanında bulunan ürünlerden bahset
- ASLA dünyadan örnek verme (Toyota, Mitsubishi, vb. marka adları yasak)
- ASLA hayali teknik özellikler uydurmayın
- Ürün yoksa: Müşteri temsilcisine yönlendir
```

---

## 🔄 DEFAULT FLOW YAPISI

### Flow Diyagramı:

```
START
  ↓
[1. welcome] → Karşılama mesajı
  ↓
[2. history_loader] → Geçmiş yükle (son 10 mesaj)
  ↓
[3. sentiment_detection] → Kullanıcı niyeti?
  ↓
┌─────────────────────────────────────┐
│ purchase_intent OR comparison?      │
└─────────────────────────────────────┘
         ↓ YES                ↓ NO
    [4. category_detection]  [9. context_builder] → Direkt AI'ya git
         ↓
    [5. price_query] → Fiyat sorgusu mu?
         ↓ YES              ↓ NO
    [6. product_search]   [7. product_search]
    (DB - Price sort)     (Meilisearch)
         ↓                    ↓
    [8. stock_sorter] ← ─────┘
         ↓
    [9. context_builder] → AI context hazırla
         ↓
    [10. ai_response] → AI cevap üret
         ↓
    [11. link_generator] → Linkleri render et
         ↓
    [12. message_saver] → Mesajları kaydet
         ↓
    [13. end] → Bitti
```

---

## 📊 DATABASE SEED

### 1. Global Nodes (Central DB)

```sql
INSERT INTO ai_workflow_nodes (node_key, node_class, node_name, category, icon, is_global, is_active, `order`) VALUES
('welcome', 'App\\Services\\ConversationNodes\\Common\\WelcomeNode', '{"tr":"Karşılama","en":"Welcome"}', 'flow', 'fa-hand-wave', 1, 1, 1),
('ai_response', 'App\\Services\\ConversationNodes\\Common\\AIResponseNode', '{"tr":"AI Cevap","en":"AI Response"}', 'ai', 'fa-robot', 1, 1, 2),
('context_builder', 'App\\Services\\ConversationNodes\\Common\\ContextBuilderNode', '{"tr":"Context Hazırla","en":"Build Context"}', 'data', 'fa-layer-group', 1, 1, 3),
('history_loader', 'App\\Services\\ConversationNodes\\Common\\HistoryLoaderNode', '{"tr":"Geçmiş Yükle","en":"Load History"}', 'data', 'fa-history', 1, 1, 4),
('message_saver', 'App\\Services\\ConversationNodes\\Common\\MessageSaverNode', '{"tr":"Mesaj Kaydet","en":"Save Message"}', 'data', 'fa-save', 1, 1, 5),
('sentiment_detection', 'App\\Services\\ConversationNodes\\Common\\SentimentDetectionNode', '{"tr":"Niyet Analizi","en":"Sentiment Detection"}', 'analysis', 'fa-brain', 1, 1, 6),
('link_generator', 'App\\Services\\ConversationNodes\\Common\\LinkGeneratorNode', '{"tr":"Link Oluştur","en":"Generate Links"}', 'output', 'fa-link', 1, 1, 7),
('condition', 'App\\Services\\ConversationNodes\\Common\\ConditionNode', '{"tr":"Şart Kontrolü","en":"Condition"}', 'flow', 'fa-code-branch', 1, 1, 8),
('collect_data', 'App\\Services\\ConversationNodes\\Common\\CollectDataNode', '{"tr":"Veri Topla","en":"Collect Data"}', 'input', 'fa-wpforms', 1, 1, 9),
('end', 'App\\Services\\ConversationNodes\\Common\\EndNode', '{"tr":"Bitir","en":"End"}', 'flow', 'fa-flag-checkered', 1, 1, 10);
```

### 2. Shop Module Nodes (Central DB - Whitelist)

```sql
INSERT INTO ai_workflow_nodes (node_key, node_class, node_name, category, icon, is_global, tenant_whitelist, is_active, `order`) VALUES
('product_search', 'App\\Services\\ConversationNodes\\Shop\\ProductSearchNode', '{"tr":"Ürün Ara","en":"Product Search"}', 'shop', 'fa-search', 0, '[2,3]', 1, 1),
('price_query', 'App\\Services\\ConversationNodes\\Shop\\PriceQueryNode', '{"tr":"Fiyat Sorgusu","en":"Price Query"}', 'shop', 'fa-dollar-sign', 0, '[2,3]', 1, 2),
('category_detection', 'App\\Services\\ConversationNodes\\Shop\\CategoryDetectionNode', '{"tr":"Kategori Tespit","en":"Category Detection"}', 'shop', 'fa-tags', 0, '[2,3]', 1, 3),
('currency_converter', 'App\\Services\\ConversationNodes\\Shop\\CurrencyConverterNode', '{"tr":"Döviz Çevirici","en":"Currency Converter"}', 'shop', 'fa-exchange-alt', 0, '[2,3]', 1, 4),
('product_comparison', 'App\\Services\\ConversationNodes\\Shop\\ProductComparisonNode', '{"tr":"Ürün Karşılaştır","en":"Product Comparison"}', 'shop', 'fa-balance-scale', 0, '[2,3]', 1, 5),
('contact_request', 'App\\Services\\ConversationNodes\\Shop\\ContactRequestNode', '{"tr":"İletişim İsteği","en":"Contact Request"}', 'shop', 'fa-phone', 0, '[2,3]', 1, 6),
('stock_sorter', 'App\\Services\\ConversationNodes\\Shop\\StockSorterNode', '{"tr":"Stok Sırala","en":"Stock Sorter"}', 'shop', 'fa-sort-amount-down', 0, '[2,3]', 1, 7);
```

### 3. Tenant Directives (Central DB)

```sql
INSERT INTO ai_tenant_directives (tenant_id, directive, priority, is_active) VALUES
(2, 'Fiyat belirtirken KDV hariç fiyat ver ve "KDV sonradan eklenir" notunu ekle', 1, 1),
(2, 'Ürün önerirken teknik özellikleri ve avantajlarını vurgula', 2, 1),
(2, 'Her zaman profesyonel ve yardımsever bir dille konuş', 3, 1),
(2, 'Link verirken [LINK:shop:product:slug] formatını kullan', 4, 1),
(2, 'Yedek parça kategorisini sadece kullanıcı açıkça isterse öner', 5, 1),
(2, 'Stok durumu sorulursa müşteri temsilcisine yönlendir', 6, 1),
(2, 'Transpalet ve Forklift kategorilerini öncelikli öner', 7, 1),
(2, 'Kategori belirlendikten sonra özellik sorularını sor (kapasite, tip, vb.)', 8, 1);
```

### 4. Default Flow (Tenant DB - iXtif)

**Flow JSON:** (Ayrı dosyada: `shop-assistant-default-flow.json`)

---

## ✅ IMPLEMENTATION CHECKLIST

- [ ] 10 Global Node class'ı oluştur
- [ ] 7 Shop Module Node class'ı oluştur
- [ ] ProductSearchService entegrasyonu (Meilisearch)
- [ ] StockSorter mantığı (featured → high stock → normal)
- [ ] CategoryDetection ile özel sorular
- [ ] PriceQuery (KDV hariç, stok kontrolü)
- [ ] CurrencyConverter (settings'den güncel kur)
- [ ] ContactRequest (settings'den iletişim bilgileri)
- [ ] ProductComparison (farklar + avantajlar)
- [ ] Seed command: `php artisan ai:seed-shop-assistant`
- [ ] Default flow JSON oluştur
- [ ] Test: iXtif'te chat widget ile test et
- [ ] Migration guide hazırla

---

## 🚀 NEXT STEPS

1. ✅ Plan onaylandı
2. ⏳ Node class'larını oluştur
3. ⏳ Seed command'ı yaz
4. ⏳ Test et
5. ⏳ Production'a deploy

**Estimated Time:** 4-6 saat
