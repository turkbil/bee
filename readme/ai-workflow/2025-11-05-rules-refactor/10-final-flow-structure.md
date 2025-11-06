# FLOW YAPISI - İXTİF AI ASSISTANT (ID: 6)

**Tarih:** 2025-11-06
**Flow ID:** 6
**Durum:** ✅ AKTİF (TEK AKTİF FLOW!)
**Database:** `tenant_ixtif.tenant_conversation_flows`

---

## 📊 GENEL BİLGİLER

**Flow Adı:** İxtif AI Assistant
**Tenant ID:** 2 (İxtif)
**Priority:** 10 (En yüksek)
**Start Node:** node_1
**Toplam Node:** 14
**Toplam Edge:** 13

---

## 🎯 FLOW AKIŞ DİYAGRAMI

```
START
  ↓
node_1 (Karşılama) → "Merhaba! Size nasıl yardımcı olabilirim?"
  ↓
node_2 (Geçmiş Yükle) → Son 10 mesajı yükle
  ↓
node_3 (Niyet Analizi) → Müşteri ne istiyor?
  ├─→ purchase_intent/comparison → node_4 (Kategori Tespit)
  ├─→ question/browsing → node_9 (Context Hazırla)
  └─→ support_request → node_11 (İletişim Ver)

[SATIN ALMA YOLU]
node_4 (Kategori Tespit) → Transpalet mi? Forklift mu?
  ↓
node_5 (Fiyat Sorgusu mu?) → "Fiyat", "ne kadar" içeriyor mu?
  ├─→ TRUE → node_6 (Fiyat Sorgusu)
  └─→ FALSE → node_7 (Ürün Ara)

node_6 (Fiyat Sorgusu) → Fiyata göre sırala (5 ürün)
  ↓
node_7 (Ürün Ara) → Meilisearch (3 ürün)
  ↓
node_8 (Stok Sırala) → Yüksek stok öne
  ↓

[ORTAK YOL]
node_9 (Context Hazırla) → Contact, AI Settings, Products
  ↓
node_10 (AI Cevap Üret) → [PROMPT BURADA! 4176 karakter]
  ↓
node_12 (Linkleri Render Et) → [LINK:shop:slug] → https://ixtif.com/...
  ↓
node_13 (Mesajları Kaydet) → DB'ye kaydet
  ↓
node_14 (Bitir) → END

[DESTEK YOLU]
node_11 (İletişim Bilgisi Ver) → WhatsApp, Phone, Email
  ↓
node_10 (AI Cevap Üret)
```

---

## 🔢 NODE DETAYLARI (14 NODE)

### 1️⃣ node_1: Karşılama (welcome)

**Amaç:** Kullanıcıyı karşıla, önerileri göster

**Config:**
```json
{
  "next_node": "node_2",
  "welcome_message": "Merhaba! Size nasıl yardımcı olabilirim?",
  "show_suggestions": true,
  "suggestions": [
    "Ürün ara",
    "Fiyat bilgisi",
    "İletişim"
  ]
}
```

**Pozisyon:** (100, 100)

---

### 2️⃣ node_2: Geçmiş Yükle (history_loader)

**Amaç:** Konuşma geçmişini yükle (context için)

**Config:**
```json
{
  "limit": 10,
  "order": "asc",
  "next_node": "node_3",
  "include_system_messages": false
}
```

**Pozisyon:** (100, 200)

**Çıktı:** Son 10 mesaj context'e eklenir

---

### 3️⃣ node_3: Niyet Analizi (sentiment_detection)

**Amaç:** Müşteri niyetini tespit et (satın alma, soru, destek)

**Config:**
```json
{
  "next_node": "node_4",
  "default_next_node": "node_9",
  "sentiment_routes": {
    "purchase_intent": "node_4",
    "comparison": "node_4",
    "question": "node_9",
    "browsing": "node_9",
    "support_request": "node_11"
  }
}
```

**Pozisyon:** (100, 300)

**Edge'ler:**
- `edge_3_purchase` → node_4 (Satın alma)
- `edge_3_comparison` → node_4 (Karşılaştırma)
- `edge_3_question` → node_9 (Soru)
- `edge_3_browsing` → node_9 (Gezinme)
- `edge_3_support` → node_11 (Destek)

---

### 4️⃣ node_4: Kategori Tespit (category_detection)

**Amaç:** Hangi kategori? (Transpalet, Forklift, vb.)

**Config:**
```json
{
  "next_node": "node_5",
  "no_category_next_node": "node_6",
  "category_questions": {
    "transpalet": [
      {
        "key": "capacity",
        "question": "Hangi kapasite transpalet arıyorsunuz?",
        "options": ["1.5 ton", "2 ton", "2.5 ton", "3 ton"]
      },
      {
        "key": "type",
        "question": "Manuel mi elektrikli mi?",
        "options": ["Manuel", "Elektrikli"]
      }
    ],
    "forklift": [
      {
        "key": "capacity",
        "question": "Hangi kapasite forklift arıyorsunuz?",
        "options": ["2 ton", "3 ton", "5 ton"]
      },
      {
        "key": "fuel",
        "question": "Yakıt tipi?",
        "options": ["Dizel", "Elektrikli", "LPG"]
      }
    ]
  }
}
```

**Pozisyon:** (300, 400)

**Çıktı:** `detected_category` → "transpalet" veya "forklift" vb.

---

### 5️⃣ node_5: Fiyat Sorgusu mu? (condition)

**Amaç:** Kullanıcı fiyat soruyor mu kontrol et

**Config:**
```json
{
  "condition_type": "contains_keywords",
  "keywords": [
    "fiyat",
    "kaç para",
    "ne kadar",
    "en ucuz",
    "en pahalı"
  ],
  "true_node": "node_6",
  "false_node": "node_7"
}
```

**Pozisyon:** (300, 500)

**Edge'ler:**
- `edge_5_true` → node_6 (Fiyat sorgusu var)
- `edge_5_false` → node_7 (Normal ürün arama)

---

### 6️⃣ node_6: Fiyat Sorgusu (price_query)

**Amaç:** Fiyata göre ürün getir (ucuzdan pahalıya)

**Config:**
```json
{
  "limit": 5,
  "show_vat": false,
  "vat_rate": 20,
  "next_node": "node_8",
  "no_products_next_node": "node_11",
  "exclude_categories": [44]
}
```

**Pozisyon:** (500, 500)

**Çıktı:** En fazla 5 ürün (fiyata göre sıralı)

**Not:** Category 44 (Yedek Parça?) exclude edilmiş

---

### 7️⃣ node_7: Ürün Ara (product_search)

**Amaç:** Meilisearch ile ürün ara

**Config:**
```json
{
  "next_node": "node_8",
  "search_limit": 3,
  "sort_by_stock": true,
  "use_meilisearch": true,
  "no_products_next_node": "node_11"
}
```

**Pozisyon:** (500, 600)

**Çıktı:** En fazla 3 ürün (stok durumuna göre)

---

### 8️⃣ node_8: Stok Sırala (stock_sorter)

**Amaç:** Yüksek stoklu ürünleri öne çıkar

**Config:**
```json
{
  "next_node": "node_9",
  "exclude_out_of_stock": false,
  "high_stock_threshold": 10
}
```

**Pozisyon:** (700, 550)

**Çıktı:** Ürünler stok sırasına göre düzenlenmiş

---

### 9️⃣ node_9: Context Hazırla (context_builder)

**Amaç:** AI'ya gönderilecek context'i hazırla

**Config:**
```json
{
  "next_node": "node_10",
  "history_limit": 10,
  "include_tenant_directives": true,
  "include_conversation_context": true,
  "include_conversation_history": true
}
```

**Pozisyon:** (900, 400)

**Çıktı (Context Data):**
```json
{
  "contact": {
    "whatsapp": "...",
    "whatsapp_link": "https://wa.me/...",
    "phone": "...",
    "email": "..."
  },
  "ai_settings": {
    "assistant_name": "AI Asistan",
    "response_tone": "friendly",
    "use_emojis": "moderate",
    "response_length": "medium",
    "sales_approach": "consultative"
  },
  "conversation_context": {
    "products": [
      {
        "id": 123,
        "title": "...",
        "slug": "...",
        "base_price": 15000,
        "currency": "TRY",
        "formatted_price": "15.000 ₺",
        "description": "...",
        "category": "...",
        "stock": 10,
        "image": "...",
        "url": "/shop/product/..."
      }
    ]
  },
  "conversation_history": [
    {"role": "user", "content": "..."},
    {"role": "assistant", "content": "..."}
  ]
}
```

**Detaylı Context Yapısı:** `11-context-data-reference.md` dosyasında

---

### 🔟 node_10: AI Cevap Üret (ai_response)

**Amaç:** Claude API ile cevap üret

**Config:**
```json
{
  "next_node": "node_12",
  "max_tokens": 500,
  "temperature": 0.7,
  "system_prompt": "[4176 karakter - Aşağıda özet]"
}
```

**Pozisyon:** (900, 500)

**System Prompt Boyutu:** 4.176 karakter

**Prompt Özeti (Ana Bölümler):**
1. 🎯 ANA İŞİMİZ: TAM ÜRÜN SATIŞI
2. 🚨 GÜVENLİK KURALLARI (Uydurma yasağı)
3. 🔗 ÜRÜN LİNK FORMATI
4. 📝 FORMATLAMA KURALLARI
5. 🌟 SATIŞ TONU (Coşkulu!)
6. 🗣️ SAMİMİ KONUŞMA (Nasılsın? → Arkadaşça yanıt)
7. 🚨 ÖNCE ÜRÜN GÖSTER
8. 🎯 KATEGORİLER (7 kategori, yedek parça en son)
9. 💰 FİYAT GÖSTERME
10. 💱 CURRENCY KURALLARI
11. 📞 TELEFON TOPLAMA
12. 📦 ÜRÜN BULUNAMADI (Pozitif yanıt)
13. 📝 MARKDOWN FORMAT
14. 📋 YANIT KURALLARI
15. ❌ YASAKLAR

**Detaylı Prompt:** `09-prompt-correction.md` dosyasında tam hali mevcut

---

### 1️⃣1️⃣ node_11: İletişim Bilgisi Ver (contact_request)

**Amaç:** Ürün bulunamadıysa veya destek isteniyorsa iletişim bilgisi ver

**Config:**
```json
{
  "next_node": "node_10",
  "callback_form_url": "/contact/callback"
}
```

**Pozisyon:** (500, 700)

**Çıktı:** Context'e contact bilgileri eklenir, AI bu bilgileri kullanır

---

### 1️⃣2️⃣ node_12: Linkleri Render Et (link_generator)

**Amaç:** `[LINK:shop:slug]` formatını gerçek URL'e çevir

**Config:**
```json
{
  "base_url": "https://ixtif.com",
  "next_node": "node_13"
}
```

**Pozisyon:** (1100, 500)

**Dönüşüm:**
```
ÖNCE: **BT LWE 160** [LINK:shop:bt-lwe-160]
SONRA: **BT LWE 160** <a href="https://ixtif.com/shop/product/bt-lwe-160">...</a>
```

---

### 1️⃣3️⃣ node_13: Mesajları Kaydet (message_saver)

**Amaç:** Kullanıcı ve AI mesajlarını database'e kaydet

**Config:**
```json
{
  "next_node": "node_14",
  "save_metadata": true,
  "save_user_message": true,
  "save_assistant_message": true
}
```

**Pozisyon:** (1100, 600)

**Çıktı:** `tenant_conversation_messages` tablosuna kayıt

---

### 1️⃣4️⃣ node_14: Bitir (end)

**Amaç:** Flow'u sonlandır

**Config:** []

**Pozisyon:** (1100, 700)

---

## 🔗 EDGE DETAYLARI (13 EDGE)

| Edge ID | Source | Target | Açıklama |
|---------|--------|--------|----------|
| edge_1 | node_1 | node_2 | Karşılama → Geçmiş yükle |
| edge_2 | node_2 | node_3 | Geçmiş → Niyet analizi |
| edge_3_purchase | node_3 | node_4 | Satın alma niyeti → Kategori tespit |
| edge_3_comparison | node_3 | node_4 | Karşılaştırma → Kategori tespit |
| edge_3_question | node_3 | node_9 | Soru → Context hazırla |
| edge_3_support | node_3 | node_11 | Destek → İletişim ver |
| edge_3_browsing | node_3 | node_9 | Gezinme → Context hazırla |
| edge_4 | node_4 | node_5 | Kategori → Fiyat sorgusu kontrolü |
| edge_5_true | node_5 | node_6 | Fiyat sorgusu var → Fiyat sorgusu |
| edge_5_false | node_5 | node_7 | Fiyat sorgusu yok → Ürün ara |
| edge_6 | node_6 | node_8 | Fiyat sorgusu → Stok sırala |
| edge_7 | node_7 | node_8 | Ürün ara → Stok sırala |
| edge_8 | node_8 | node_9 | Stok sırala → Context hazırla |
| edge_9 | node_9 | node_10 | Context → AI cevap |
| edge_10 | node_10 | node_12 | AI cevap → Link render |
| edge_11 | node_11 | node_10 | İletişim → AI cevap |
| edge_12 | node_12 | node_13 | Link render → Mesaj kaydet |
| edge_13 | node_13 | node_14 | Mesaj kaydet → Bitir |

---

## 📋 SENARYO BAZLI AKIŞLAR

### Senaryo 1: "transpalet istiyorum"

```
1. node_1: Karşılama ✅
2. node_2: Geçmiş yükle ✅
3. node_3: Niyet analizi → purchase_intent ✅
4. node_4: Kategori tespit → "transpalet" ✅
5. node_5: Fiyat sorgusu? → FALSE ✅
6. node_7: Ürün ara → 3 transpalet ürünü ✅
7. node_8: Stok sırala → Yüksek stok öne ✅
8. node_9: Context hazırla → Products + Contact + Settings ✅
9. node_10: AI cevap → "Harika! Size transpalet göstereyim..." ✅
10. node_12: Link render → [LINK:shop:...] → <a href="..."> ✅
11. node_13: Mesaj kaydet → DB'ye kaydet ✅
12. node_14: Bitir ✅
```

### Senaryo 2: "en ucuz forklift"

```
1-3. [Aynı]
4. node_4: Kategori tespit → "forklift" ✅
5. node_5: Fiyat sorgusu? → TRUE ("en ucuz") ✅
6. node_6: Fiyat sorgusu → 5 forklift (ucuzdan pahalıya) ✅
7. node_8: Stok sırala ✅
8-12. [Aynı]
```

### Senaryo 3: "nasılsın?"

```
1-2. [Aynı]
3. node_3: Niyet analizi → question ✅
9. node_9: Context hazırla (ürün yok) ✅
10. node_10: AI cevap → "İyiyim teşekkürler! 😊" ✅
12-14. [Aynı]
```

### Senaryo 4: "iletişim bilgisi"

```
1-2. [Aynı]
3. node_3: Niyet analizi → support_request ✅
11. node_11: İletişim bilgisi ver → WhatsApp, Phone, Email ✅
10. node_10: AI cevap → "Tabii! İletişim bilgilerimiz..." ✅
12-14. [Aynı]
```

---

## 🎯 KRİTİK NOKTALAR

### 1. PROMPT KONUMU
- **Node 10** (ai_response) → `config.system_prompt`
- Boyut: 4.176 karakter
- Son güncelleme: 2025-11-06 03:30
- Güncelleme nedeni: ANA İŞ TANIMI düzeltmesi

### 2. CONTEXT BUILDER (Node 9)
- Contact bilgisi: `settings()` helper'dan
- AI Settings: `settings()` helper'dan
- Product price formatting: `ShopCurrency` model
- N+1 optimization: Batch currency query

### 3. PRODUCT SEARCH
- **Node 7:** Meilisearch (3 ürün)
- **Node 6:** Price query (5 ürün)
- Exclude category: 44 (Yedek Parça?)

### 4. LINK RENDERING
- **Node 12:** `[LINK:shop:slug]` → `<a href="https://ixtif.com/shop/product/slug">`
- Base URL: `https://ixtif.com`

---

## 🔄 GÜNCELLEME GEÇMİŞİ

### 2025-11-06 03:30
- **Değişiklik:** Prompt güncelleme (node_10)
- **Sebep:** ANA İŞ TANIMI düzeltmesi
- **Eski:** Yedek parça odaklı
- **Yeni:** TAM ÜRÜN odaklı
- **Detay:** `09-prompt-correction.md`

### 2025-11-06
- **Değişiklik:** Flow cleanup
- **Sebep:** Admin panel'de 3 flow karışıklık yaratıyordu
- **Silinen:** ID 2, ID 5
- **Kalan:** ID 6 (TEK AKTİF!)

### 2025-11-05
- **Değişiklik:** Backend implementation
- **Eklenen:** ContextBuilderNode (5 method)
- **Eklenen:** ProductSearchNode (currency field)
- **Detay:** `08-backend-implementation.md`

---

## 📊 PERFORMANS NOTLARI

**Ortalama İşlem Süresi (Tahmini):**
1. Karşılama + Geçmiş: ~50ms
2. Niyet + Kategori: ~100ms
3. Ürün Arama (Meilisearch): ~200ms
4. Stok Sırala + Context: ~100ms
5. AI Cevap (Claude API): ~2000ms
6. Link Render + Kaydet: ~100ms

**Toplam:** ~2550ms (2.5 saniye)

**Darboğazlar:**
- Claude API çağrısı (2s)
- Meilisearch (200ms)

**Optimizasyon:**
- N+1 query prevention (ContextBuilderNode)
- Batch currency query
- Meilisearch cache

---

## 🔗 İLGİLİ DOSYALAR

- **Prompt Detayı:** `09-prompt-correction.md`
- **Context Referansı:** `11-context-data-reference.md`
- **Backend Implementation:** `08-backend-implementation.md`
- **Flow Kurulum:** `07-flow-implementation.md`
- **Master Kurallar:** `01-ai-rules-complete.md`

---

**Son Güncelleme:** 2025-11-06
**Durum:** ✅ AKTİF - PRODUCTION READY
