# CONTEXT DATA REFERENCE - AI'YA GİDEN VERİ YAPISI

**Tarih:** 2025-11-06
**Node:** node_9 (Context Builder)
**Hedef:** node_10 (AI Response)

---

## 📋 GENEL BAKIŞ

**Context Builder (node_9)** AI'ya gönderilecek tüm verileri hazırlar:
1. İletişim bilgileri (`contact`)
2. AI kişilik ayarları (`ai_settings`)
3. Konuşma context'i (`conversation_context`)
4. Konuşma geçmişi (`conversation_history`)

Bu veriler **Claude API**'ye gönderilir ve AI yanıt üretir.

---

## 🎯 TAM CONTEXT YAPISI

```json
{
  "contact": {
    "whatsapp": "+905551234567",
    "whatsapp_link": "https://wa.me/905551234567",
    "phone": "+90 555 123 45 67",
    "email": "info@ixtif.com"
  },

  "ai_settings": {
    "assistant_name": "İxtif AI Asistan",
    "response_tone": "friendly",
    "use_emojis": "moderate",
    "response_length": "medium",
    "sales_approach": "consultative"
  },

  "conversation_context": {
    "detected_category": "transpalet",
    "user_preferences": {
      "capacity": "2 ton",
      "type": "Elektrikli"
    },
    "products": [
      {
        "id": 123,
        "title": "BT LWE 160 - 1.6 Ton Elektrikli Transpalet",
        "slug": "bt-lwe-160",
        "base_price": 45000,
        "currency": "TRY",
        "formatted_price": "45.000 ₺",
        "currency_symbol": "₺",
        "currency_format": "symbol_after",
        "decimal_places": 0,
        "description": "1.6 ton taşıma kapasitesi, Li-Ion batarya, ergonomik tasarım",
        "category": "Transpalet",
        "category_id": 12,
        "stock": 15,
        "is_featured": true,
        "image": "https://ixtif.com/storage/media/bt-lwe-160.jpg",
        "url": "/shop/product/bt-lwe-160"
      },
      {
        "id": 124,
        "title": "LINDE T20 - 2 Ton Manuel Transpalet",
        "slug": "linde-t20",
        "base_price": 32000,
        "currency": "TRY",
        "formatted_price": "32.000 ₺",
        "currency_symbol": "₺",
        "currency_format": "symbol_after",
        "decimal_places": 0,
        "description": "2 ton kapasite, manuel sistem, dayanıklı yapı",
        "category": "Transpalet",
        "category_id": 12,
        "stock": 8,
        "is_featured": false,
        "image": "https://ixtif.com/storage/media/linde-t20.jpg",
        "url": "/shop/product/linde-t20"
      }
    ]
  },

  "conversation_history": [
    {
      "role": "user",
      "content": "merhaba"
    },
    {
      "role": "assistant",
      "content": "Merhaba! Size nasıl yardımcı olabilirim?"
    },
    {
      "role": "user",
      "content": "transpalet istiyorum"
    }
  ]
}
```

---

## 📞 CONTACT DATA (İletişim Bilgileri)

### Kaynak: `settings()` Helper

**Database:** `tenant_settings` tablosu

**Alan Adları:**
- `contact_whatsapp_1` → WhatsApp numarası
- `contact_phone_1` → Telefon numarası
- `contact_email_1` → Email adresi

**Örnek:**
```json
{
  "contact": {
    "whatsapp": "+905551234567",
    "whatsapp_link": "https://wa.me/905551234567",
    "phone": "+90 555 123 45 67",
    "email": "info@ixtif.com"
  }
}
```

**WhatsApp Link Formatı:**
```php
// Otomatik link oluşturma
// Input: "+90 555 123 45 67" veya "0555 123 45 67"
// Output: "https://wa.me/905551234567"

// Kod: ContextBuilderNode::generateWhatsAppLink()
$clean = preg_replace('/[^0-9]/', '', $phoneNumber); // "905551234567"
if (substr($clean, 0, 1) === '0') {
    $clean = '90' . substr($clean, 1); // "0555..." → "90555..."
}
return "https://wa.me/{$clean}";
```

**AI Kullanımı:**
```markdown
💬 **WhatsApp:** [+90 555 123 45 67](https://wa.me/905551234567)
📞 **Telefon:** +90 555 123 45 67
✉️ **Email:** info@ixtif.com
```

---

## 🤖 AI_SETTINGS (AI Kişilik Ayarları)

### Kaynak: `settings()` Helper

**Database:** `tenant_settings` tablosu

**Alan Adları:**
- `ai_assistant_name` → AI asistan adı
- `ai_response_tone` → Yanıt tonu
- `ai_use_emojis` → Emoji kullanımı
- `ai_response_length` → Yanıt uzunluğu
- `ai_sales_approach` → Satış yaklaşımı

**Örnek:**
```json
{
  "ai_settings": {
    "assistant_name": "İxtif AI Asistan",
    "response_tone": "friendly",
    "use_emojis": "moderate",
    "response_length": "medium",
    "sales_approach": "consultative"
  }
}
```

**Varsayılan Değerler:**
```php
// ContextBuilderNode::getAISettings()
'assistant_name' => settings('ai_assistant_name', 'AI Asistan'),
'response_tone' => settings('ai_response_tone', 'friendly'),
'use_emojis' => settings('ai_use_emojis', 'moderate'),
'response_length' => settings('ai_response_length', 'medium'),
'sales_approach' => settings('ai_sales_approach', 'consultative'),
```

**Değer Seçenekleri:**

| Setting | Değerler | Açıklama |
|---------|----------|----------|
| response_tone | `friendly`, `professional`, `casual`, `formal` | Yanıt tonu |
| use_emojis | `none`, `minimal`, `moderate`, `frequent` | Emoji sıklığı |
| response_length | `short`, `medium`, `long` | Yanıt uzunluğu |
| sales_approach | `consultative`, `aggressive`, `educational` | Satış yaklaşımı |

**NOT:** Şu anda `ai_settings` prompt'a dahil edilmiyor, gelecekte kullanılabilir.

---

## 🛒 CONVERSATION_CONTEXT (Konuşma Context'i)

### Kaynak: Birden Fazla Node

**İçerik:**
1. `detected_category` → node_4 (category_detection)
2. `user_preferences` → node_4 (category_detection questions)
3. `products` → node_6/node_7 (price_query/product_search)

**Örnek:**
```json
{
  "conversation_context": {
    "detected_category": "transpalet",
    "user_preferences": {
      "capacity": "2 ton",
      "type": "Elektrikli"
    },
    "products": [...]
  }
}
```

---

## 📦 PRODUCTS DATA (Ürün Verisi)

### Kaynak: node_6 (price_query) veya node_7 (product_search)

**Format:** Array of Objects

**Her Ürün İçin Alanlar:**

| Alan | Tip | Kaynak | Açıklama |
|------|-----|--------|----------|
| `id` | int | Database | Ürün ID |
| `title` | string | Database | Ürün başlığı (çoklu dil) |
| `slug` | string | Database | URL slug |
| `base_price` | float | Database | Temel fiyat (sayısal) |
| `currency` | string | Database | Para birimi kodu (TRY, USD, EUR) |
| `formatted_price` | string | **ContextBuilderNode** | Formatlanmış fiyat ("45.000 ₺") |
| `currency_symbol` | string | **ContextBuilderNode** | Para birimi sembolü ("₺") |
| `currency_format` | string | **ContextBuilderNode** | Format tipi ("symbol_before" / "symbol_after") |
| `decimal_places` | int | **ContextBuilderNode** | Ondalık basamak (0, 2) |
| `description` | string | Database | Ürün açıklaması (çoklu dil) |
| `category` | string | Database | Kategori adı (çoklu dil) |
| `category_id` | int | Database | Kategori ID |
| `stock` | int | Database | Stok miktarı |
| `is_featured` | bool | Database | Öne çıkan mı? |
| `image` | string/null | Database | Ürün görseli URL |
| `url` | string | Computed | Ürün detay sayfası ("/shop/product/slug") |

**Örnek Ürün:**
```json
{
  "id": 123,
  "title": "BT LWE 160 - 1.6 Ton Elektrikli Transpalet",
  "slug": "bt-lwe-160",
  "base_price": 45000,
  "currency": "TRY",
  "formatted_price": "45.000 ₺",
  "currency_symbol": "₺",
  "currency_format": "symbol_after",
  "decimal_places": 0,
  "description": "1.6 ton taşıma kapasitesi, Li-Ion batarya, ergonomik tasarım",
  "category": "Transpalet",
  "category_id": 12,
  "stock": 15,
  "is_featured": true,
  "image": "https://ixtif.com/storage/media/bt-lwe-160.jpg",
  "url": "/shop/product/bt-lwe-160"
}
```

---

## 💰 PRICE FORMATTING (Fiyat Formatlama)

### Kaynak: `ContextBuilderNode::formatProductPrices()`

**Amaç:** `base_price` + `currency` → `formatted_price`

**Örnek Dönüşüm:**

| base_price | currency | formatted_price | Açıklama |
|------------|----------|-----------------|----------|
| 45000 | TRY | "45.000 ₺" | Türk Lirası |
| 1350 | USD | "$1,350" | Amerikan Doları |
| 1200 | EUR | "€1.200" | Euro |
| 32500.50 | TRY | "32.500,50 ₺" | Ondalıklı |

**Currency Database (shop_currencies):**

```sql
SELECT * FROM shop_currencies;

-- Örnek kayıtlar:
-- code | symbol | format         | decimal_places
-- TRY  | ₺      | symbol_after   | 0
-- USD  | $      | symbol_before  | 0
-- EUR  | €      | symbol_before  | 2
```

**Format Kuralları:**

1. **symbol_before:**
   ```php
   // Örnek: USD → "$1,350"
   return $currency->symbol . number_format($price, $decimal, ',', '.');
   ```

2. **symbol_after:**
   ```php
   // Örnek: TRY → "45.000 ₺"
   return number_format($price, $decimal, ',', '.') . ' ' . $currency->symbol;
   ```

**N+1 Query Prevention:**
```php
// ❌ YANLIŞ (Her ürün için 1 query)
foreach ($products as $product) {
    $currency = ShopCurrency::where('code', $product['currency'])->first();
    // ...
}

// ✅ DOĞRU (Tek query ile tüm currency'ler)
$currencyCodes = array_unique(array_column($products, 'currency'));
$currencies = ShopCurrency::whereIn('code', $currencyCodes)->get()->keyBy('code');
foreach ($products as $product) {
    $currency = $currencies[$product['currency']];
    // ...
}
```

**AI Kullanımı:**
```markdown
Fiyat: 45.000 ₺
```

**⚠️ KRİTİK KURAL:**
AI'ya gönderilen prompt'ta:
```
💱 CURRENCY:
- formatted_price zaten doğru formatta (örn: "15.000 ₺" veya "$1,350")
- Sen sadece AYNEN göster
- ASLA currency sembolü kendin ekleme!
```

---

## 💬 CONVERSATION_HISTORY (Konuşma Geçmişi)

### Kaynak: node_2 (history_loader)

**Format:** Array of Messages

**Her Mesaj İçin Alanlar:**

| Alan | Tip | Değerler | Açıklama |
|------|-----|----------|----------|
| `role` | string | `user`, `assistant`, `system` | Mesaj sahibi |
| `content` | string | Mesaj içeriği | Kullanıcı/AI mesajı |

**Örnek:**
```json
{
  "conversation_history": [
    {
      "role": "user",
      "content": "merhaba"
    },
    {
      "role": "assistant",
      "content": "Merhaba! 🎉 Size nasıl yardımcı olabilirim?"
    },
    {
      "role": "user",
      "content": "transpalet istiyorum"
    },
    {
      "role": "assistant",
      "content": "Harika! 🎉 Size en popüler transpalet modellerimizi göstereyim..."
    },
    {
      "role": "user",
      "content": "2 ton elektrikli olsun"
    }
  ]
}
```

**Config (node_2):**
```json
{
  "limit": 10,
  "order": "asc",
  "include_system_messages": false
}
```

**Limit:** Son 10 mesaj (5 kullanıcı + 5 AI)
**Order:** Eskiden yeniye (asc)
**System Messages:** Dahil değil (sadece user/assistant)

**Database:** `tenant_conversation_messages` tablosu

**Query Örneği:**
```sql
SELECT role, content
FROM tenant_conversation_messages
WHERE conversation_id = 123
AND role IN ('user', 'assistant')
ORDER BY created_at ASC
LIMIT 10;
```

**AI Kullanımı:**
Claude API'ye `messages` array olarak gönderilir:
```json
{
  "model": "claude-sonnet-4-5",
  "max_tokens": 500,
  "temperature": 0.7,
  "system": "[system_prompt]",
  "messages": [
    {"role": "user", "content": "merhaba"},
    {"role": "assistant", "content": "Merhaba! 🎉"},
    {"role": "user", "content": "transpalet istiyorum"}
  ]
}
```

---

## 🔄 CONTEXT FLOW (Node'lar Arası Veri Akışı)

### 1. Başlangıç (node_1)
```json
{
  "conversation_id": 123,
  "user_message": "transpalet istiyorum"
}
```

### 2. Geçmiş Yükle (node_2)
```json
{
  "conversation_id": 123,
  "user_message": "transpalet istiyorum",
  "conversation_history": [...]  // +EKLENEN
}
```

### 3. Niyet Analizi (node_3)
```json
{
  "conversation_id": 123,
  "user_message": "transpalet istiyorum",
  "conversation_history": [...],
  "detected_intent": "purchase_intent"  // +EKLENEN
}
```

### 4. Kategori Tespit (node_4)
```json
{
  "conversation_id": 123,
  "user_message": "transpalet istiyorum",
  "conversation_history": [...],
  "detected_intent": "purchase_intent",
  "detected_category": "transpalet",  // +EKLENEN
  "user_preferences": {}  // +EKLENEN (şimdilik boş)
}
```

### 5. Ürün Ara (node_7)
```json
{
  "conversation_id": 123,
  "user_message": "transpalet istiyorum",
  "conversation_history": [...],
  "detected_intent": "purchase_intent",
  "detected_category": "transpalet",
  "user_preferences": {},
  "products": [...]  // +EKLENEN (currency YOK henüz)
}
```

### 6. Stok Sırala (node_8)
```json
{
  // Aynı + products sıralanmış
}
```

### 7. Context Hazırla (node_9)
```json
{
  "conversation_id": 123,
  "user_message": "transpalet istiyorum",
  "contact": {...},  // +EKLENEN
  "ai_settings": {...},  // +EKLENEN
  "conversation_context": {
    "detected_category": "transpalet",
    "user_preferences": {},
    "products": [...]  // +formatted_price EKLENEN
  },
  "conversation_history": [...]
}
```

### 8. AI Cevap (node_10)
- Claude API'ye gönderilir
- `system_prompt` + `context` + `conversation_history`
- Yanıt: Markdown formatında AI mesajı

---

## 📝 ÖRNEK AI PROMPT OLUŞTURMA

### Claude API'ye Gönderilen Data

**System Prompt:**
```
Sen İxtif.com satış danışmanısın. Forklift, transpalet ve istif makineleri satıyorsun.

🎯 ANA İŞİMİZ (EN ÖNEMLİ!):
✅ TAM ÜRÜN SATIŞI (Forklift, Transpalet, İstif Makinesi)
...
[4176 karakter prompt]
```

**Context (AI'ya ek bilgi olarak gönderilir):**
```json
{
  "contact": {
    "whatsapp": "+905551234567",
    "whatsapp_link": "https://wa.me/905551234567",
    "phone": "+90 555 123 45 67",
    "email": "info@ixtif.com"
  },
  "conversation_context": {
    "detected_category": "transpalet",
    "products": [
      {
        "id": 123,
        "title": "BT LWE 160 - 1.6 Ton Elektrikli Transpalet",
        "slug": "bt-lwe-160",
        "formatted_price": "45.000 ₺",
        "description": "...",
        "stock": 15
      }
    ]
  }
}
```

**Messages:**
```json
[
  {"role": "user", "content": "merhaba"},
  {"role": "assistant", "content": "Merhaba! 🎉"},
  {"role": "user", "content": "transpalet istiyorum"}
]
```

**AI Response Örneği:**
```markdown
Harika! 🎉 Size en popüler transpalet modellerimizi göstereyim! 😊

⭐ **BT LWE 160 - 1.6 Ton Elektrikli Transpalet** [LINK:shop:bt-lwe-160]

Favorilerimden biri! 🔥

- 1.6 ton taşıma kapasitesi (süper güçlü! 💪)
- Li-Ion batarya (uzun ömürlü! 🔋)
- Ergonomik tasarım (çok pratik! 👍)

Fiyat: 45.000 ₺

Hangi kapasite arıyorsunuz? 🤔
```

---

## 🛡️ GÜVENLİK KONTROLLARI

### 1. Settings Hataları

```php
// ContextBuilderNode::getContactInformation()
try {
    $whatsapp = settings('contact_whatsapp_1');
    // ...
} catch (\Exception $e) {
    $this->log('warning', 'Failed to load contact information', [
        'error' => $e->getMessage(),
    ]);
    return []; // Boş array döndür, crash etme!
}
```

**Sonuç:** Setting yoksa veya hata varsa → Boş array, AI "İletişim bilgisi yok" der

### 2. Currency Bulunamadı

```php
// ContextBuilderNode::formatProductPrices()
if (isset($product['formatted_price'])) {
    return $product; // Zaten formatlanmış, tekrar yapma
}

if (isset($product['base_price']) && isset($product['currency']) && isset($currencies[$product['currency']])) {
    // Format yap
} else {
    // Currency yok → formatted_price ekleme, base_price olduğu gibi kalır
}
```

**Sonuç:** Currency yoksa → `formatted_price` eklenmez, AI "Fiyat talep üzerine" der

### 3. Ürün Yoksa

```php
// Config: no_products_next_node: "node_11"
if (empty($products)) {
    return 'node_11'; // İletişim bilgisi ver
}
```

**Sonuç:** Ürün bulunamadıysa → node_11'e git, iletişim bilgisi ver

---

## 📊 CONTEXT BOYUTU

**Tahmini Boyutlar:**

| Alan | Boyut (Karakter) | Açıklama |
|------|------------------|----------|
| contact | ~200 | 4 alan (whatsapp, phone, email, link) |
| ai_settings | ~200 | 5 alan (name, tone, emojis, length, approach) |
| detected_category | ~20 | Tek string |
| user_preferences | ~100 | 2-3 soru cevabı |
| products (3 ürün) | ~2000 | Her ürün ~700 karakter |
| conversation_history (10 mesaj) | ~2000 | Her mesaj ~200 karakter |

**Toplam Context:** ~4500 karakter

**Claude API Token Limiti:**
- Input: 200.000 token (~800.000 karakter)
- Output: 8.192 token (~32.000 karakter)

**Güvenlik Marjı:** Context 4.5KB, limit 800KB → %0.5 kullanım (Çok güvenli!)

---

## 🔗 İLGİLİ DOSYALAR

- **Flow Yapısı:** `10-final-flow-structure.md`
- **Backend Implementation:** `08-backend-implementation.md`
- **Prompt Detayı:** `09-prompt-correction.md`
- **Master Kurallar:** `01-ai-rules-complete.md`

---

**Son Güncelleme:** 2025-11-06
**Durum:** ✅ AKTİF - PRODUCTION READY
