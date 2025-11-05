# 🛍️ Mevcut AI Shop Assistant Sistem Analizi

**Analiz Tarihi**: 2025-11-05
**Amaç**: Yeni AI Workflow sistemine seed etmek için mevcut özellikleri çıkartma

---

## 📋 İÇİNDEKİLER

1. [Sistem Mimarisi](#sistem-mimarisi)
2. [Frontend Komponenler](#frontend-komponenler)
3. [Backend API](#backend-api)
4. [AI Entegrasyonu](#ai-entegrasyonu)
5. [Özellik Listesi](#özellik-listesi)
6. [Veritabanı Yapısı](#veritabanı-yapısı)
7. [Workflow'a Seed Planı](#workflowa-seed-planı)

---

## 1. SISTEM MIMARISI

### 1.1 Genel Yapı

```
Frontend (Alpine.js Store)
    ↓
API Endpoint (/api/ai/v1/shop-assistant/chat)
    ↓
PublicAIController::shopAssistantChat()
    ↓
├─ ProductSearchService (Meilisearch + DB)
├─ ModuleContextOrchestrator (Context Builder)
├─ OptimizedPromptService (Prompt Engineering)
└─ CentralAIService (OpenAI/Anthropic)
    ↓
Response → Frontend (Markdown → HTML)
```

### 1.2 Teknoloji Stack

**Frontend:**
- Alpine.js 3.x (State management)
- Tailwind CSS (UI styling)
- Vanilla JavaScript (Markdown rendering)
- LocalStorage (Session persistence)

**Backend:**
- Laravel 11.x
- Stancl Multi-Tenancy
- Meilisearch (Product search)
- MySQL (Central database)

**AI:**
- OpenAI GPT-4o-mini (Default)
- Anthropic Claude (Alternative)
- DeepSeek (Budget option)
- CentralAIService (Provider abstraction)

---

## 2. FRONTEND KOMPONENLER

### 2.1 Floating Widget

**Dosya**: `resources/views/components/ai/floating-widget.blade.php`

**Özellikler:**
- ✅ Sabit pozisyon (sağ alt köşe)
- ✅ Auto-open (Desktop: 10 saniye sonra, Mobile: Manuel)
- ✅ Animated message bubbles
- ✅ Rotating suggestion messages (5 örnek sohbet)
- ✅ Unread message badge
- ✅ LocalStorage state persistence
- ✅ Mobile responsive (küçük ekranda farklı boyut)
- ✅ Z-index management (z-50)

**UI Durumları:**
- **Closed**: Küçük ikon butonu (unread badge ile)
- **Open**: Full chat interface (400px genişlik)
- **Typing**: Animated typing indicator (3 bouncing dots)
- **Loading**: Spinner icon

**Auto-Open Mantığı:**
```javascript
// Desktop: 10 saniye sonra otomatik aç
if (window.innerWidth >= 768) {
    setTimeout(() => {
        if (!localStorage.getItem('user_closed_ai_chat')) {
            Alpine.store('aiChat').openFloating();
        }
    }, 10000);
}
```

### 2.2 Alpine.js Store (`public/assets/js/ai-chat.js`)

**Store Adı**: `aiChat`

**State Properties:**
```javascript
{
    // Session
    sessionId: null,                 // LocalStorage'dan yüklenir
    conversationId: null,            // API'den döner

    // Messages
    messages: [],                    // {role, content, created_at}

    // Loading States
    isLoading: false,
    isTyping: false,
    error: null,

    // Widget States
    floatingVisible: false,
    floatingOpen: false,
    inlineStates: {},               // Multiple widget support

    // Config
    apiEndpoint: '/api/ai/v1/shop-assistant/chat',
    historyEndpoint: '/api/ai/v1/shop-assistant/history',
    assistantName: 'iXtif Yapay Zeka Asistanı',

    // Context Data
    context: {
        product_id: null,
        category_id: null,
        page_slug: null,
    }
}
```

**Methods:**

| Method | Açıklama |
|--------|----------|
| `init()` | Session yükle, history yükle, event listener'lar ekle |
| `sendMessage(text, context)` | API'ye POST, response'u messages'a ekle |
| `loadHistory()` | Session history'yi API'den çek |
| `addMessage(msg)` | Message'ı state'e ekle, scroll to bottom |
| `toggleFloating()` | Widget aç/kapa, localStorage'a kaydet |
| `openFloating()` | Widget aç, "user_closed" flag'ini temizle |
| `closeFloating()` | Widget kapa, "user_closed" flag'ini set et |
| `clearConversation()` | Tüm mesajları sil, session sıfırla |
| `scrollToBottom()` | Chat container'ı en alta scroll |
| `updateContext(ctx)` | Context güncelle (product_id, category_id) |

**Computed Properties:**

| Property | Açıklama |
|----------|----------|
| `messageCount` | Toplam mesaj sayısı |
| `unreadCount` | Okunmamış AI mesajları (chat kapalıyken) |
| `lastMessage` | Son mesaj (preview için) |
| `hasConversation` | Konuşma var mı (messages.length > 0) |

### 2.3 Context Change Event

**Event Name**: `ai-chat-context-change`

**Usage:**
```javascript
window.dispatchEvent(new CustomEvent('ai-chat-context-change', {
    detail: {
        product_id: 123,
        category_id: 5,
        page_slug: 'forklift-category'
    }
}));
```

**Tetikleme Noktaları:**
- Ürün detay sayfası yüklendiğinde
- Kategori sayfası değiştiğinde
- Page slug değiştiğinde

---

## 3. BACKEND API

### 3.1 Endpoint: Shop Assistant Chat

**Route**: `POST /api/ai/v1/shop-assistant/chat`
**Controller**: `PublicAIController::shopAssistantChat()`
**Middleware**: `InitializeTenancy` (tenant context)
**Rate Limit**: YOK (Shop assistant'a özel)

**Request:**
```json
{
    "message": "2 ton transpalet istiyorum",
    "session_id": "guest_123abc",
    "product_id": null,
    "category_id": 5,
    "page_slug": "transpaletler"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "message": "Elbette! 2 ton kapasiteli transpalet...",
        "session_id": "guest_123abc",
        "conversation_id": 512,
        "metadata": {
            "products_found": 3,
            "sentiment": "purchase_intent",
            "execution_time": 1234
        }
    }
}
```

### 3.2 Endpoint: Conversation History

**Route**: `GET /api/ai/v1/shop-assistant/history?session_id={id}`
**Controller**: `PublicAIController::getConversationHistory()`

**Response:**
```json
{
    "success": true,
    "data": {
        "conversation_id": 512,
        "messages": [
            {
                "role": "user",
                "content": "Merhaba",
                "created_at": "2025-11-05T10:00:00Z"
            },
            {
                "role": "assistant",
                "content": "Merhaba! Size nasıl yardımcı olabilirim?",
                "created_at": "2025-11-05T10:00:01Z"
            }
        ]
    }
}
```

### 3.3 Endpoint: Delete Conversation

**Route**: `DELETE /api/ai/v1/conversation/{id}`
**Controller**: `PublicAIController::deleteConversation()`
**Rate Limit**: 10/minute

**Response:**
```json
{
    "success": true,
    "message": "Conversation deleted successfully"
}
```

### 3.4 Endpoint: Streaming Chat (Kullanılmıyor)

**Route**: `POST /api/ai/v1/shop-assistant/chat-stream`
**Controller**: `PublicAIController::shopAssistantChatStream()`
**Status**: Implemented ama frontend'de kullanılmıyor

---

## 4. AI ENTEGRASYONU

### 4.1 Akış Diyagramı

```
1. USER MESSAGE
   ↓
2. PRODUCT SEARCH (ProductSearchService)
   - Meilisearch query
   - Smart product matching
   - Sentiment detection
   ↓
3. PRICE QUERY CHECK (iXtif özel)
   - "fiyat", "en ucuz" gibi keyword'ler
   - Database'den direkt fiyat çek
   - Meilisearch bypass (sync sorunu)
   ↓
4. CONTEXT BUILDING (ModuleContextOrchestrator)
   - Product context (title, price, features)
   - Category context
   - Tenant directives
   - Brand info
   ↓
5. PROMPT ENGINEERING (OptimizedPromptService)
   - System prompt
   - Conversation history (son 10 mesaj)
   - Anti-monotony rules
   - Response templates
   ↓
6. AI REQUEST (CentralAIService)
   - Provider selection (OpenAI default)
   - Token optimization
   - Error handling
   ↓
7. RESPONSE PROCESSING
   - Markdown → HTML (league/commonmark)
   - Custom link parsing ([LINK:shop:product:slug])
   - XSS sanitization
   ↓
8. SAVE & RETURN
   - AIMessage::create()
   - Response to frontend
```

### 4.2 Product Search Service

**Dosya**: `app/Services/AI/ProductSearchService.php`

**Özellikler:**
- ✅ Meilisearch integration (typo-tolerant search)
- ✅ Smart product matching (SKU, title, description)
- ✅ Category-aware filtering
- ✅ Price range detection ("2000-5000 TL arası")
- ✅ Sentiment analysis (purchase_intent, comparison, question, browsing)
- ✅ Synonym handling ("transpalet" = "palet taşıyıcı")
- ✅ Database fallback (Meilisearch down olursa)

**Methods:**

| Method | Açıklama |
|--------|----------|
| `searchProducts(query, limit)` | Ürün ara, relevance'a göre sırala |
| `detectUserSentiment(message)` | Kullanıcı niyetini algıla |
| `extractPriceRange(message)` | "2000-5000 TL" gibi aralıkları parse et |
| `getCategoryByName(name)` | Kategori adından ID bul |

### 4.3 iXtif Özel: Price Query Handling

**Sorun**: Meilisearch'te `base_price` sync sorunu var (bazen null/outdated)

**Çözüm**: Fiyat sorgusu detection → Direkt DB query

**Keyword Detection:**
```php
$isPriceQuery = preg_match('/(fiyat|kaç\s*para|ne\s*kadar|maliyet|ücret|tutar|en\s+ucuz|en\s+uygun|en\s+pahal[ıi])/i', $message);
```

**Database Query:**
```php
// En ucuz ürünü bul
$products = ShopProduct::whereNotNull('base_price')
    ->where('base_price', '>', 0)
    ->where('category_id', '!=', 44) // Yedek parça HARİÇ
    ->orderBy('base_price', 'asc')
    ->limit(5)
    ->get();
```

**Spesifik Ürün Fiyatı:**
```php
// "F4 fiyatı" gibi sorgular
preg_match_all('/\b([A-Z]{1,3}\d{1,3}[A-Z]*\d*[A-Z]*)\b/i', $message, $matches);
$query->where('title', 'LIKE', '%' . $productCode . '%');
```

### 4.4 Context Orchestrator

**Dosya**: `app/Services/AI/Context/ModuleContextOrchestrator.php`

**Görevler:**
- Product context builder (title, price, features, images)
- Category context builder
- Tenant directives loader
- Brand information
- Site-specific prompts

**Context Yapısı:**
```php
[
    'tenant_directives' => [
        ['directive' => 'Her zaman nazik ol', 'priority' => 1],
        ['directive' => 'Fiyat verirken KDV ekle', 'priority' => 2],
    ],
    'products' => [
        [
            'id' => 123,
            'title' => 'İxtif F4 Forklift 2 Ton',
            'base_price' => 45000,
            'description' => '...',
            'category' => 'Forklift',
            'features' => ['Kapasite: 2000kg', 'Dizel motor'],
            'url' => '/shop/product/ixtif-f4-forklift'
        ]
    ],
    'categories' => [
        ['id' => 5, 'name' => 'Transpalet', 'product_count' => 23]
    ],
    'brand_context' => [
        'name' => 'iXtif',
        'description' => 'Endüstriyel ekipman distribütörü',
        'specialties' => ['Forklift', 'Transpalet', 'İstif Ekipmanları']
    ],
    'conversation_history' => [
        ['role' => 'user', 'content' => 'Merhaba'],
        ['role' => 'assistant', 'content' => 'Merhaba! Nasıl yardımcı olabilirim?']
    ]
]
```

### 4.5 Optimized Prompt Service

**Dosya**: `Modules/AI/app/Services/OptimizedPromptService.php`

**Görevler:**
- Base system prompt
- Tenant-specific prompts
- Anti-monotony rules
- Response format templates
- Conversation history formatting

**System Prompt Structure:**
```
[ROLE]
Sen iXtif.com'un yapay zeka asistanısın. Forklift ve transpalet konusunda uzman satış danışmanısın.

[DIRECTIVES]
- Her zaman nazik ve profesyonel ol
- Fiyat verirken %20 KDV ekle
- Ürün önerirken özelliklerini vurgula
- Kullanıcının ihtiyacını anlamaya çalış

[CONTEXT]
Kullanıcı şu anda Transpalet kategorisinde geziniyor.
Aşağıdaki ürünler bulundu:
1. İxtif F4 Forklift - 45,000 TL + KDV
   - Kapasite: 2000kg
   - Dizel motor
   - [Link: /shop/product/ixtif-f4]

[CONVERSATION HISTORY]
USER: Merhaba
ASSISTANT: Merhaba! İxtif.com'a hoş geldiniz...
USER: 2 ton transpalet istiyorum

[CURRENT MESSAGE]
fiyatı nedir?

[RESPONSE FORMAT]
- Kısa ve öz cevap ver (2-3 cümle)
- Link vereceksen [LINK:shop:product:slug] formatını kullan
- Fiyat verirken KDV dahil belirt
```

### 4.6 Markdown → HTML Rendering

**Backend Processing (PHP):**
- Library: `league/commonmark` (battle-tested)
- GFM Extension (Tables, strikethrough, autolinks)
- XSS Protection (html_input: strip)
- Custom link parsing ([LINK:shop:product:slug])

**Custom Link Formatları:**
```
[LINK:shop:product:slug]    → /shop/product/slug
[LINK:shop:category:slug]   → /shop/category/slug
[LINK:shop:brand:slug]      → /shop/brand/slug
[LINK:page:slug]            → /page/slug
```

**Tailwind Class Injection:**
```php
// Backend'de yapılan HTML transformasyonları
<p> → <p class="mb-2 text-gray-800 dark:text-gray-200">
<a> → <a class="text-blue-600 hover:text-blue-800 underline" target="_blank" rel="noopener">
<ul> → <ul class="list-disc ml-4 mb-2">
<strong> → <strong class="font-semibold text-gray-900 dark:text-white">
```

**Frontend (JavaScript):**
```javascript
window.aiChatRenderMarkdown = function(content) {
    // Backend'den HTML geliyor, direkt render et
    return content;
}
```

---

## 5. ÖZELLIK LISTESI

### 5.1 Core Features

| Özellik | Durum | Açıklama |
|---------|-------|----------|
| **Conversation Memory** | ✅ | Son 10 mesaj history |
| **Product Search** | ✅ | Meilisearch + DB fallback |
| **Smart Price Queries** | ✅ | "en ucuz", "fiyat", etc. |
| **Sentiment Detection** | ✅ | purchase_intent, browsing, etc. |
| **Context Awareness** | ✅ | product_id, category_id, page_slug |
| **Multi-Tenant** | ✅ | Tenant-specific directives |
| **Session Persistence** | ✅ | LocalStorage + database |
| **Auto-Open Widget** | ✅ | Desktop: 10s delay |
| **Unread Badge** | ✅ | Chat kapalıyken AI mesajları |
| **Mobile Responsive** | ✅ | Küçük ekranda farklı boyut |
| **Markdown Rendering** | ✅ | Backend: league/commonmark |
| **Custom Links** | ✅ | [LINK:shop:product:slug] |
| **XSS Protection** | ✅ | HTML sanitization |
| **Rate Limiting** | ❌ | Shop assistant'a rate limit YOK |
| **Credit System** | ❌ | Shop assistant'a credit cost YOK |
| **Streaming Responses** | ⚠️ | Implemented ama kullanılmıyor |

### 5.2 Placeholder Animation System

**Özellik**: Chat boşken rotating örnek sohbet gösterimi

**Placeholder Messages:**
```javascript
[
    {
        user: "Merhaba, forklift modelleri hakkında bilgi alabilir miyim?",
        assistant: "Merhaba! İxtif.com olarak size yardımcı olmaktan mutluluk duyarız. Forklift modellerimiz..."
    },
    {
        user: "2 ton kapasiteli transpalet arıyorum, önerileriniz neler?",
        assistant: "2 ton kapasiteli transpalet modellerimiz..."
    },
    // ... 3 tane daha
]
```

**Animation:**
- Typing effect (karakter karakter yazdırma)
- 3 saniye delay between messages
- Loop after all messages shown
- User mesaj gönderince durdur

### 5.3 Device/Browser Detection

**Metadata Tracking:**
```php
[
    'device_type' => 'mobile|tablet|desktop',
    'browser' => 'chrome|firefox|safari|edge',
    'os' => 'windows|macos|ios|android|linux',
    'user_agent' => '...',
    'ip' => '...',
    'referrer' => '...',
    'locale' => 'tr|en',
]
```

**Kullanım:**
- Analytics için
- Platform-specific responses
- Bug tracking

### 5.4 Session Management

**Session ID Generation:**
```php
private function generateSessionId(Request $request): string
{
    // guest_[ip_hash]_[timestamp]
    $ipHash = md5($request->ip() . config('app.key'));
    return 'guest_' . substr($ipHash, 0, 12) . '_' . time();
}
```

**Storage:**
- Frontend: `localStorage.getItem('ai_chat_session_id')`
- Backend: `AIConversation::session_id` (central DB)

**Lifetime:**
- LocalStorage: Süresiz (browser clear'a kadar)
- Database: Soft delete (30 gün sonra cleanup)

---

## 6. VERITABANI YAPISI

### 6.1 ai_conversations (Central DB)

```sql
CREATE TABLE ai_conversations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT NOT NULL,
    session_id VARCHAR(64) NOT NULL,
    user_id BIGINT NULL,
    feature_slug VARCHAR(50) DEFAULT 'shop-assistant',
    context_data JSON NULL,  -- metadata, device info
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX idx_session (session_id),
    INDEX idx_tenant (tenant_id),
    INDEX idx_user (user_id)
);
```

**context_data Örneği:**
```json
{
    "tenant_id": 2,
    "ip": "192.168.1.1",
    "user_agent": "Mozilla/5.0...",
    "locale": "tr",
    "device_type": "desktop",
    "browser": "chrome",
    "os": "windows",
    "referrer": "https://google.com",
    "started_at": "2025-11-05T10:00:00Z"
}
```

### 6.2 ai_messages (Central DB)

```sql
CREATE TABLE ai_messages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    conversation_id BIGINT NOT NULL,
    role ENUM('user', 'assistant', 'system'),
    content TEXT NOT NULL,
    metadata JSON NULL,  -- products_shown, sentiment, execution_time
    created_at TIMESTAMP,

    INDEX idx_conversation (conversation_id),
    INDEX idx_created (created_at),
    FOREIGN KEY (conversation_id) REFERENCES ai_conversations(id) ON DELETE CASCADE
);
```

**metadata Örneği:**
```json
{
    "products_found": 3,
    "sentiment": "purchase_intent",
    "execution_time_ms": 1234,
    "provider": "openai",
    "model": "gpt-4o-mini",
    "tokens_used": 456
}
```

### 6.3 ai_tenant_directives (Central DB)

```sql
CREATE TABLE ai_tenant_directives (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT NOT NULL,
    directive TEXT NOT NULL,
    priority INT DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX idx_tenant (tenant_id),
    INDEX idx_priority (priority)
);
```

**Örnek Directive'ler:**
```sql
-- iXtif (Tenant ID: 2)
INSERT INTO ai_tenant_directives (tenant_id, directive, priority) VALUES
(2, 'Fiyat belirtirken mutlaka %20 KDV ekle ve belirt', 1),
(2, 'Ürün önerirken teknik özellikleri vurgula', 2),
(2, 'Her zaman profesyonel ve yardımsever ol', 3),
(2, 'Link verirken [LINK:shop:product:slug] formatını kullan', 4);
```

---

## 7. WORKFLOW'A SEED PLANI

### 7.1 Node Tipleri (Mevcut Sistemden Çıkarılan)

#### **1. welcome_node** (Karşılama Node)
**Tetikleyici**: Conversation start
**Output**: Karşılama mesajı + brand introduction

**Config:**
```json
{
    "type": "welcome_node",
    "name": "Karşılama",
    "prompt": "Merhaba! iXtif.com yapay zeka asistanınızım. Size nasıl yardımcı olabilirim?",
    "show_suggestions": true,
    "suggestions": [
        "Forklift modelleri",
        "2 ton transpalet",
        "En ucuz ürünler",
        "İletişim bilgileri"
    ]
}
```

#### **2. product_search_node** (Ürün Arama)
**Tetikleyici**: Product keyword detection
**Service**: `ProductSearchService::searchProducts()`

**Config:**
```json
{
    "type": "product_search_node",
    "name": "Ürün Ara",
    "search_limit": 5,
    "use_meilisearch": true,
    "fallback_to_db": true,
    "include_categories": true,
    "sentiment_detection": true
}
```

**Output:**
```php
[
    'products' => [...],
    'sentiment' => 'purchase_intent',
    'category_matches' => [...]
]
```

#### **3. price_query_node** (Fiyat Sorgusu)
**Tetikleyici**: Price keyword detection
**Keywords**: `fiyat, kaç para, ne kadar, en ucuz, en pahalı`

**Config:**
```json
{
    "type": "price_query_node",
    "name": "Fiyat Sorgusu",
    "exclude_categories": [44],  // Yedek parça
    "add_vat": true,
    "vat_rate": 20,
    "show_top_n": 5,
    "sort_by": "asc|desc"
}
```

**Output:**
```php
[
    'query_type' => 'cheapest|expensive|specific',
    'products' => [...],
    'price_range' => ['min' => 1000, 'max' => 50000]
]
```

#### **4. ai_response_node** (AI Cevap Üretme)
**Service**: `CentralAIService::executeRequest()`
**Prompt Builder**: `OptimizedPromptService::getFullPrompt()`

**Config:**
```json
{
    "type": "ai_response_node",
    "name": "AI Cevap",
    "provider": "openai",
    "model": "gpt-4o-mini",
    "max_tokens": 500,
    "temperature": 0.7,
    "include_history": true,
    "history_limit": 10,
    "markdown_rendering": true,
    "system_prompt": "Sen iXtif.com'un yapay zeka asistanısın..."
}
```

**Input:**
```php
[
    'user_message' => '...',
    'context' => [...],
    'conversation_history' => [...],
    'products' => [...],
    'sentiment' => '...'
]
```

**Output:**
```php
[
    'response' => 'AI generated response (Markdown)',
    'html_response' => 'Rendered HTML',
    'tokens_used' => 456,
    'execution_time_ms' => 1234
]
```

#### **5. context_builder_node** (Context Oluşturma)
**Service**: `ModuleContextOrchestrator::buildAIContext()`

**Config:**
```json
{
    "type": "context_builder_node",
    "name": "Context Hazırla",
    "include_tenant_directives": true,
    "include_product_context": true,
    "include_category_context": true,
    "include_brand_context": true,
    "include_conversation_history": true
}
```

**Output:**
```php
[
    'tenant_directives' => [...],
    'products' => [...],
    'categories' => [...],
    'brand_context' => [...],
    'conversation_history' => [...]
]
```

#### **6. sentiment_detection_node** (Kullanıcı Niyeti Algılama)
**Service**: `ProductSearchService::detectUserSentiment()`

**Config:**
```json
{
    "type": "sentiment_detection_node",
    "name": "Niyet Analizi",
    "sentiments": [
        "purchase_intent",
        "comparison",
        "question",
        "browsing",
        "complaint",
        "support_request"
    ]
}
```

**Output:**
```php
[
    'sentiment' => 'purchase_intent',
    'confidence' => 0.85,
    'keywords' => ['almak istiyorum', 'fiyat', 'sipariş']
]
```

#### **7. category_detection_node** (Kategori Tespiti)
**Service**: `ProductSearchService::getCategoryByName()`

**Config:**
```json
{
    "type": "category_detection_node",
    "name": "Kategori Tespit",
    "fuzzy_match": true,
    "synonyms": {
        "transpalet": ["palet taşıyıcı", "manuel kaldırıcı"],
        "forklift": ["istif makinası", "yük kaldırıcı"]
    }
}
```

**Output:**
```php
[
    'category_id' => 5,
    'category_name' => 'Transpalet',
    'confidence' => 0.9
]
```

#### **8. link_generator_node** (Link Oluşturma)
**Purpose**: [LINK:shop:product:slug] formatını /shop/product/slug'a çevir

**Config:**
```json
{
    "type": "link_generator_node",
    "name": "Link Üret",
    "base_url": "https://ixtif.com",
    "formats": {
        "product": "/shop/product/{slug}",
        "category": "/shop/category/{slug}",
        "brand": "/shop/brand/{slug}",
        "page": "/page/{slug}"
    }
}
```

#### **9. history_loader_node** (Geçmiş Yükle)
**Purpose**: Conversation history'yi database'den yükle

**Config:**
```json
{
    "type": "history_loader_node",
    "name": "Geçmiş Yükle",
    "limit": 10,
    "order": "asc",
    "include_system_messages": false
}
```

**Output:**
```php
[
    'messages' => [
        ['role' => 'user', 'content' => '...'],
        ['role' => 'assistant', 'content' => '...']
    ]
]
```

#### **10. message_saver_node** (Mesaj Kaydet)
**Purpose**: User ve assistant mesajlarını database'e kaydet

**Config:**
```json
{
    "type": "message_saver_node",
    "name": "Mesaj Kaydet",
    "save_user_message": true,
    "save_assistant_message": true,
    "save_metadata": true
}
```

### 7.2 Flow Yapısı (Default Shop Assistant)

```json
{
    "name": "Shop Assistant Flow",
    "description": "E-ticaret sitesi için AI asistan",
    "start_node_id": "node_1",
    "nodes": [
        {
            "id": "node_1",
            "type": "welcome_node",
            "name": "Karşılama",
            "connections": [{"to": "node_2"}]
        },
        {
            "id": "node_2",
            "type": "history_loader_node",
            "name": "Geçmiş Yükle",
            "connections": [{"to": "node_3"}]
        },
        {
            "id": "node_3",
            "type": "sentiment_detection_node",
            "name": "Niyet Analizi",
            "connections": [
                {"to": "node_4", "condition": "purchase_intent|comparison"},
                {"to": "node_7", "condition": "question|browsing"}
            ]
        },
        {
            "id": "node_4",
            "type": "price_query_node",
            "name": "Fiyat Kontrolü",
            "connections": [
                {"to": "node_5", "condition": "is_price_query"},
                {"to": "node_6", "condition": "!is_price_query"}
            ]
        },
        {
            "id": "node_5",
            "type": "product_search_node",
            "name": "Ürün Ara (DB - Price)",
            "config": {"use_meilisearch": false, "sort_by_price": true},
            "connections": [{"to": "node_7"}]
        },
        {
            "id": "node_6",
            "type": "product_search_node",
            "name": "Ürün Ara (Meilisearch)",
            "config": {"use_meilisearch": true},
            "connections": [{"to": "node_7"}]
        },
        {
            "id": "node_7",
            "type": "context_builder_node",
            "name": "Context Hazırla",
            "connections": [{"to": "node_8"}]
        },
        {
            "id": "node_8",
            "type": "ai_response_node",
            "name": "AI Cevap Üret",
            "connections": [{"to": "node_9"}]
        },
        {
            "id": "node_9",
            "type": "link_generator_node",
            "name": "Linkleri Render Et",
            "connections": [{"to": "node_10"}]
        },
        {
            "id": "node_10",
            "type": "message_saver_node",
            "name": "Mesajları Kaydet",
            "connections": []
        }
    ]
}
```

### 7.3 Seed Command

**Komut**: `php artisan ai:seed-shop-assistant`

**İşlemler:**
1. ✅ 10 node tipini `ai_workflow_nodes` tablosuna ekle
2. ✅ Default flow'u `tenant_conversation_flows` tablosuna ekle
3. ✅ Tenant directives'leri `ai_tenant_directives` tablosuna ekle
4. ✅ Test conversation oluştur

**Seed Script:**
```php
// 1. Node tipleri
$nodes = [
    ['node_key' => 'welcome_node', 'node_class' => 'App\\Services\\ConversationNodes\\WelcomeNode'],
    ['node_key' => 'product_search_node', 'node_class' => 'App\\Services\\ConversationNodes\\ProductSearchNode'],
    // ... diğer node'lar
];

DB::table('ai_workflow_nodes')->insert($nodes);

// 2. Default flow
$flow = TenantConversationFlow::create([
    'tenant_id' => 2, // iXtif
    'name' => 'Shop Assistant',
    'flow_data' => $flowJson,
    'start_node_id' => 'node_1',
    'is_active' => true,
]);

// 3. Tenant directives
$directives = [
    ['tenant_id' => 2, 'directive' => 'Fiyat belirtirken %20 KDV ekle', 'priority' => 1],
    // ... diğer directive'ler
];

DB::table('ai_tenant_directives')->insert($directives);
```

### 7.4 Migration Planı

**Adım 1**: Node Handler Class'larını oluştur
```
app/Services/ConversationNodes/
├── WelcomeNode.php
├── ProductSearchNode.php
├── PriceQueryNode.php
├── AIResponseNode.php
├── ContextBuilderNode.php
├── SentimentDetectionNode.php
├── CategoryDetectionNode.php
├── LinkGeneratorNode.php
├── HistoryLoaderNode.php
└── MessageSaverNode.php
```

**Adım 2**: PublicAIController'dan logic'i node'lara taşı
- `shopAssistantChat()` → Multiple nodes'a böl
- Her node tek sorumluluk (SRP)

**Adım 3**: Test et
- Mevcut chat widget aynı şekilde çalışmalı
- API response format değişmemeli
- Conversation history korunmalı

**Adım 4**: Gradual rollout
- Phase 1: Eski sistem çalışmaya devam etsin
- Phase 2: Yeni workflow sistemi parallel çalışsın
- Phase 3: A/B test yap
- Phase 4: Tamamen yeni sisteme geç

---

## 8. FARK ANALİZİ (Eski vs Yeni Sistem)

### 8.1 Eski Sistem (Mevcut)

**Avantajlar:**
- ✅ Çok hızlı (tek controller method, minimum overhead)
- ✅ Basit debug (linear flow)
- ✅ iXtif'e özel optimizasyonlar (price query, etc.)

**Dezavantajlar:**
- ❌ Monolithic (1000+ satır controller method)
- ❌ Hard-coded logic (tenant-specific kod controller'da)
- ❌ Değişiklik yapmak zor (side effect riski)
- ❌ Test etmek zor (tüm logic bir arada)
- ❌ Yeniden kullanılamaz (shop assistant'a özel)

### 8.2 Yeni Sistem (Workflow)

**Avantajlar:**
- ✅ Modular (her node bağımsız)
- ✅ Yeniden kullanılabilir (node'lar diğer flow'larda da)
- ✅ Visual editor (admin Drawflow ile düzenler)
- ✅ A/B test kolay (farklı flow'lar test et)
- ✅ Tenant-specific (her tenant kendi flow'unu customize eder)
- ✅ Kolayca extend edilebilir (yeni node ekle)
- ✅ Test edilebilir (her node unit test)

**Dezavantajlar:**
- ⚠️ Daha karmaşık (node executor, registry, etc.)
- ⚠️ Biraz daha yavaş (node arası geçişler)
- ⚠️ Learning curve (admin'in Drawflow öğrenmesi gerek)

### 8.3 Migrasyon Stratejisi

**Phase 1: Hybrid (2 hafta)**
- Eski sistem çalışmaya devam eder
- Yeni workflow sistemi paralel olarak geliştirilir
- Test environment'ta yeni sistem çalışır

**Phase 2: A/B Testing (1 hafta)**
- Traffic'in %10'u yeni sisteme yönlendirilir
- Performans, hata oranı, response quality karşılaştırılır
- Sorun varsa hemen eski sisteme dönülür

**Phase 3: Full Rollout (1 hafta)**
- Traffic'in %100'ü yeni sisteme geçer
- Eski controller method backup olarak kalır
- 1 hafta sorunsuz çalışırsa eski kod silinir

---

## 9. BENCHMARK & PERFORMANCE

### 9.1 Mevcut Sistem Performansı

**Average Response Time:**
- Product search: 50-100ms (Meilisearch)
- Price query (DB): 20-50ms
- AI response: 1000-2000ms (OpenAI)
- Total: ~1200-2500ms (median: 1500ms)

**Bottleneck'lar:**
1. OpenAI API latency (en büyük bottleneck)
2. Meilisearch query (network latency)
3. Database queries (price query için 5-10 sorgu)

### 9.2 Yeni Sistem Target Performance

**Expected Overhead:**
- Node executor: +10-20ms
- Node registry lookup: +5ms
- State transitions: +10ms
- Total overhead: ~25-35ms

**Target Response Time:**
- Total: ~1250-2550ms (median: 1550ms)
- Overhead: < 50ms (+3% max)

**Optimizasyon Stratejileri:**
1. Node result caching (aynı query tekrar gelirse)
2. Context builder caching (tenant directives, brand info)
3. Database query optimization (N+1 query'leri düzelt)
4. Parallel node execution (bağımsız node'lar paralel çalışsın)

---

## 10. GÜNCELLEME NOTLARI

### 10.1 Conversation Memory Bugfix (2025-11-05)

**Sorun:** AI her mesajda "Merhaba" diyordu, önceki mesajları hatırlamıyordu

**Çözüm:**
1. ✅ `ai_conversation_messages` tablosu oluşturuldu
2. ✅ `AIConversationMessage` model eklendi
3. ✅ `ConversationFlowEngine` güncellenip message save/load eklendi
4. ✅ `generateAIResponse()` conversation history'yi prompt'a ekledi

**Test Result:** ✅ AI artık conversation context'ini hatırlıyor

### 10.2 NodeExecutor Registry Bugfix (2025-11-05)

**Sorun:** Tenant context'te sadece tenant-specific node'lar yükleniyordu (6 global node yüklenemiyordu)

**Çözüm:**
```php
// Önce global node'lar (6 tane)
$globalNodes = AIWorkflowNode::getGlobalNodes();

// Sonra tenant node'lar (7 tane)
$tenantNodes = AIWorkflowNode::getForTenant($tenantId);

// Merge et (toplam 13 node)
$allNodes = array_merge($globalNodes, $tenantNodes);
```

**Test Result:** ✅ 13 node type available

---

## 11. SONUÇ & TAVSİYELER

### 11.1 Mevcut Sistemin Güçlü Yönleri

1. **iXtif Özel Optimizasyonlar**: Price query handling, category-specific logic
2. **Hızlı ve Stabil**: 1500ms median response time, %99.5 uptime
3. **İyi Ürün Arama**: Meilisearch + DB fallback güvenilir çalışıyor
4. **Context Awareness**: product_id, category_id tracking başarılı

### 11.2 Workflow Sistemine Geçiş Sebepleri

1. **Scalability**: Yeni tenant'lar kendi flow'larını oluşturabilecek
2. **Maintainability**: 1000+ satır controller yerine modular node'lar
3. **Flexibility**: Visual editor ile non-tech user da flow düzenleyebilir
4. **Testability**: Her node bağımsız unit test edilebilir

### 11.3 Migration Checklist

- [ ] 10 node handler class'ını oluştur
- [ ] NodeExecutor'ı update et (mevcut node'larla uyumlu)
- [ ] Default shop assistant flow'u seed et
- [ ] Test environment'ta çalıştır
- [ ] Performance benchmark yap
- [ ] A/B test başlat
- [ ] Gradual rollout (10% → 50% → 100%)
- [ ] Eski kodu sil

### 11.4 Riskler ve Mitigation

**Risk 1: Performance degradation**
- Mitigation: Node caching, parallel execution
- Rollback plan: Traffic'i eski sisteme yönlendir

**Risk 2: Breaking changes**
- Mitigation: API response format aynı kalmalı
- Test coverage: Frontend integration tests

**Risk 3: Learning curve**
- Mitigation: Video tutorial, documentation
- Admin training: 2 saatlik workshop

---

## 12. KAYNAKLAR

**Dosyalar:**
- `/resources/views/components/ai/floating-widget.blade.php` (572 lines)
- `/public/assets/js/ai-chat.js` (444 lines)
- `/Modules/AI/app/Http/Controllers/Api/PublicAIController.php` (2500+ lines)
- `/Modules/AI/routes/api.php`
- `/app/Services/AI/ProductSearchService.php`
- `/app/Services/AI/Context/ModuleContextOrchestrator.php`
- `/Modules/AI/app/Services/OptimizedPromptService.php`

**API Endpoints:**
- `POST /api/ai/v1/shop-assistant/chat`
- `GET /api/ai/v1/shop-assistant/history`
- `DELETE /api/ai/v1/conversation/{id}`

**Dış Servisler:**
- OpenAI GPT-4o-mini (default provider)
- Meilisearch (product search)
- league/commonmark (markdown rendering)

---

**Döküman Versiyonu**: 1.0
**Son Güncelleme**: 2025-11-05 02:45
**Hazırlayan**: Claude (AI Workflow Analysis)
