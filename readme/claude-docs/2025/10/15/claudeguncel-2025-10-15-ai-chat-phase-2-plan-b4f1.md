# 🚀 AI CHAT ASSISTANT - PHASE 2 PLANLAMA

**Tarih:** 2025-10-15
**ID:** b4f1
**Sprint:** Phase 2 - Frontend & Advanced Features
**Önceki Sprint:** Phase 1 (a9c3) - Backend & Prompt Revamp ✅

---

## 📊 PHASE 1 ÖZET (TAMAMLANDI)

### ✅ Tamamlananlar
1. Knowledge Base entegrasyonu
2. Telegram notification service
3. IxtifPromptService satış tonu revampi
4. WhatsApp iletişim stratejisi
5. Olumsuz ifadelerden kaçınma kuralı

### ⏸️ Ertelenenler (Phase 2'ye aktarıldı)
1. Tıklanabilir buton sistemi (Frontend)
2. Ürün bilgisi genişletme (Opsiyonel)
3. Frontend chat widget iyileştirmeleri

---

## 🎯 PHASE 2 HEDEFLER

### 1️⃣ **Tıklanabilir Quick Reply Butonları**
**Öncelik:** 🔥 YÜKSEK
**Tahmini Süre:** 4-6 saat

**Problem:**
- Müşteri yazmak zorunda (mobilde zor)
- AI soru sorarken kullanıcı deneyimi kötü

**Çözüm:**
- AI yanıtlarında otomatik buton render et
- Kullanıcı tıklarsa mesaj otomatik gönderilsin
- Kullanıcı yazmaya başlarsa butonlar disabled olsun

**Teknik Detay:**
```
AI Yanıtı: "Hangi tip makine arıyorsunuz? [BUTTON:Transpalet|Forklift|Reach Truck]"

Frontend Parse Eder:
- "Hangi tip makine arıyorsunuz?"
- [Transpalet] [Forklift] [Reach Truck] (tıklanabilir)

Kullanıcı "Transpalet" Tıklarsa:
- Input'a "Transpalet" yazılır
- Otomatik gönderilir
- Butonlar disabled/gizlenir
```

---

### 2️⃣ **Ürün Görselli Kartlar**
**Öncelik:** 🟡 ORTA
**Tahmini Süre:** 3-4 saat

**Problem:**
- Sadece text link kuru görünüyor
- Müşteri ürünü görmek istiyor

**Çözüm:**
- Ürün önerirken görsel kartlar göster
- Ürün adı, görsel, fiyat, kısa açıklama

**Teknik Detay:**
```
AI Yanıtı:
"[PRODUCT:123]" (özel syntax)

Frontend Render:
┌─────────────────────────┐
│ [Ürün Görseli]         │
│ Litef EPT20            │
│ 2000 kg                │
│ Fiyat Sorunuz          │
│ [Detay Gör]            │
└─────────────────────────┘
```

---

### 3️⃣ **Typing Indicator (Yazıyor Animasyonu)**
**Öncelik:** 🟢 DÜŞÜK
**Tahmini Süre:** 1-2 saat

**Problem:**
- AI yanıt verirken sessizlik var
- Kullanıcı bekliyor mu bilmiyor

**Çözüm:**
```
Mesaj gönderilince:
"● ● ●" (animasyonlu)
"AI yanıt hazırlıyor..."
```

---

### 4️⃣ **Konuşma Geçmişi Yükleme**
**Öncelik:** 🟡 ORTA
**Tahmini Süre:** 2-3 saat

**Problem:**
- Sayfa yenilenince konuşma kayboluyor
- Müşteri tekrar baştan anlatıyor

**Çözüm:**
- Session ID ile konuşma geçmişi yükle
- LocalStorage veya Cookie'de session_id sakla
- Sayfa açılınca `/api/ai/v1/conversation-history` çağır

---

### 5️⃣ **Ürün Bilgisi Genişletme**
**Öncelik:** 🟢 DÜŞÜK (Opsiyonel)
**Tahmini Süre:** 2-3 saat

**Eklenecekler:**
- Marka bilgisi (ShopBrand)
- Stok durumu (varsa)
- Varyant fiyat karşılaştırması
- Benzer ürünler önerisi

---

## 📁 DOSYA YAPISI (Phase 2)

```
resources/
├── views/
│   └── components/
│       └── ai/
│           ├── chat-widget.blade.php               ✏️ GÜNCELLE (buton + kart)
│           ├── quick-reply-buttons.blade.php       ✨ YENİ
│           └── product-card.blade.php              ✨ YENİ

public/
├── js/
│   └── ai/
│       ├── chat-widget.js                          ✏️ GÜNCELLE (parsing + events)
│       ├── quick-reply.js                          ✨ YENİ
│       └── product-card-renderer.js                ✨ YENİ

Modules/AI/
└── app/
    └── Services/
        └── Tenant/
            └── IxtifPromptService.php               ✏️ GÜNCELLE (buton syntax kuralı)
```

---

## 🔨 TEKNIK UYGULAMA DETAYLARI

### 1️⃣ TIKLANA BİLİR BUTONLAR

#### A) Backend (AI Prompt Güncelleme)
**Dosya:** `IxtifPromptService.php`

```php
// Eklenecek kural:
$prompts[] = "**BUTON SYNTAX (Seçenekler sunmak için):**";
$prompts[] = "- Müşteriye seçenek sunduğunda şu formatı kullan:";
$prompts[] = "  [BUTTON:Seçenek1|Seçenek2|Seçenek3]";
$prompts[] = "- Örnek: 'Hangi kategori? [BUTTON:Transpalet|Forklift|Reach Truck]'";
$prompts[] = "- UYARI: En fazla 4 seçenek (mobil uyumluluk)";
$prompts[] = "";
```

#### B) Frontend (JavaScript Parsing)
**Dosya:** `public/js/ai/chat-widget.js`

```javascript
/**
 * Parse AI response for special syntax
 */
function parseAIResponse(content) {
    // [BUTTON:Option1|Option2|Option3] pattern'ini bul
    const buttonPattern = /\[BUTTON:(.*?)\]/g;
    let match;

    while ((match = buttonPattern.exec(content)) !== null) {
        const options = match[1].split('|');

        // HTML butonları oluştur
        const buttons = options.map(opt =>
            `<button class="ai-quick-reply-btn" data-message="${opt.trim()}">${opt.trim()}</button>`
        ).join('');

        // Pattern'i butonlarla değiştir
        content = content.replace(match[0], `<div class="ai-quick-reply-container">${buttons}</div>`);
    }

    return content;
}

/**
 * Handle quick reply button click
 */
$(document).on('click', '.ai-quick-reply-btn', function() {
    const message = $(this).data('message');

    // Input'a yaz
    $('#chat-input').val(message);

    // Otomatik gönder
    sendMessage(message);

    // Tüm butonları disable et
    $('.ai-quick-reply-btn').prop('disabled', true).addClass('disabled');
});

/**
 * Kullanıcı yazmaya başlarsa butonları kapat
 */
$('#chat-input').on('input', function() {
    if ($(this).val().length > 0) {
        $('.ai-quick-reply-btn').prop('disabled', true).addClass('disabled');
    }
});
```

#### C) CSS Styling
**Dosya:** `public/css/ai-chat-widget.css` (veya inline)

```css
/* Quick Reply Butonları */
.ai-quick-reply-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 10px 0;
}

.ai-quick-reply-btn {
    padding: 10px 16px;
    background: #f0f0f0;
    border: 1px solid #ddd;
    border-radius: 20px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

.ai-quick-reply-btn:hover {
    background: #e0e0e0;
    border-color: #ccc;
}

.ai-quick-reply-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

/* Mobil responsive */
@media (max-width: 576px) {
    .ai-quick-reply-btn {
        flex: 1 1 45%; /* 2 buton yan yana */
        text-align: center;
    }
}
```

---

### 2️⃣ ÜRÜN KARTLARı

#### A) Backend (AI Prompt Güncelleme)
**Dosya:** `IxtifPromptService.php`

```php
$prompts[] = "**ÜRÜN KARTI SYNTAX (Görsel göstermek için):**";
$prompts[] = "- Ürün önerirken linkle birlikte kart göster:";
$prompts[] = "  [PRODUCTCARD:product_id]";
$prompts[] = "- Örnek: 'Size şunu önerebilirim: [PRODUCTCARD:123]'";
$prompts[] = "- NOT: En fazla 3 ürün kartı (görsel kalabalık olmasın)";
$prompts[] = "";
```

#### B) Frontend API (Ürün Detayı Çekme)
**Yeni Endpoint:** `PublicAIController.php`

```php
/**
 * Get product card data for AI widget
 */
public function getProductCard(Request $request, int $productId): JsonResponse
{
    try {
        $product = ShopProduct::with(['media', 'brand', 'category'])
            ->where('id', $productId)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json(['success' => false], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'title' => $product->getTranslated('title'),
                'image' => $product->getFirstMediaUrl('images', 'thumb'),
                'price' => $product->price_formatted ?? 'Fiyat Sorunuz',
                'brand' => $product->brand?->name,
                'url' => route('shop.product.show', $product->slug),
                'short_description' => $product->getTranslated('short_description'),
            ],
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false], 500);
    }
}
```

**Route:**
```php
Route::get('/api/ai/v1/product-card/{id}', [PublicAIController::class, 'getProductCard']);
```

#### C) Frontend Rendering
**Dosya:** `public/js/ai/product-card-renderer.js`

```javascript
/**
 * Parse and render product cards
 */
async function parseProductCards(content) {
    const cardPattern = /\[PRODUCTCARD:(\d+)\]/g;
    let match;
    const promises = [];

    while ((match = cardPattern.exec(content)) !== null) {
        const productId = match[1];
        const placeholder = match[0];

        promises.push(
            fetch(`/api/ai/v1/product-card/${productId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const card = renderProductCard(data.data);
                        content = content.replace(placeholder, card);
                    }
                })
        );
    }

    await Promise.all(promises);
    return content;
}

/**
 * Render product card HTML
 */
function renderProductCard(product) {
    return `
        <div class="ai-product-card">
            <div class="ai-product-image">
                <img src="${product.image}" alt="${product.title}" />
            </div>
            <div class="ai-product-info">
                <h4>${product.title}</h4>
                <p class="brand">${product.brand || ''}</p>
                <p class="price">${product.price}</p>
                <a href="${product.url}" class="btn btn-sm btn-primary" target="_blank">
                    Detay Gör
                </a>
            </div>
        </div>
    `;
}
```

---

### 3️⃣ TYPING INDICATOR

#### Frontend Implementation
**Dosya:** `chat-widget.js`

```javascript
/**
 * Show typing indicator
 */
function showTypingIndicator() {
    const typingHtml = `
        <div class="message ai-message typing-indicator" id="typing-indicator">
            <div class="typing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <p class="typing-text">AI yanıt hazırlıyor...</p>
        </div>
    `;

    $('#chat-messages').append(typingHtml);
    scrollToBottom();
}

/**
 * Hide typing indicator
 */
function hideTypingIndicator() {
    $('#typing-indicator').remove();
}

/**
 * Updated sendMessage function
 */
async function sendMessage(message) {
    // ... existing code ...

    // Show typing
    showTypingIndicator();

    try {
        const response = await fetch('/api/ai/v1/shop-assistant-chat', {
            method: 'POST',
            body: JSON.stringify({ message }),
            headers: { 'Content-Type': 'application/json' }
        });

        const data = await response.json();

        // Hide typing
        hideTypingIndicator();

        // Show AI response
        displayAIMessage(data.data.message);
    } catch (error) {
        hideTypingIndicator();
        // error handling...
    }
}
```

**CSS:**
```css
.typing-indicator {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    background: #f5f5f5;
    border-radius: 8px;
}

.typing-dots {
    display: flex;
    gap: 4px;
}

.typing-dots span {
    width: 8px;
    height: 8px;
    background: #999;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.typing-dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
    30% { transform: translateY(-10px); opacity: 1; }
}
```

---

### 4️⃣ KONUŞMA GEÇMİŞİ YÜKLEME

#### Frontend Implementation
**Dosya:** `chat-widget.js`

```javascript
/**
 * Load conversation history on widget open
 */
async function loadConversationHistory() {
    const sessionId = getOrCreateSessionId();

    try {
        const response = await fetch(`/api/ai/v1/conversation-history?session_id=${sessionId}`);
        const data = await response.json();

        if (data.success && data.data.messages.length > 0) {
            // Clear existing messages
            $('#chat-messages').empty();

            // Render history
            data.data.messages.forEach(msg => {
                if (msg.role === 'user') {
                    displayUserMessage(msg.content);
                } else {
                    displayAIMessage(msg.content);
                }
            });

            scrollToBottom();
        }
    } catch (error) {
        console.error('Failed to load conversation history:', error);
    }
}

/**
 * Get or create session ID (localStorage)
 */
function getOrCreateSessionId() {
    let sessionId = localStorage.getItem('ai_chat_session_id');

    if (!sessionId) {
        // Generate new session ID (MD5 of IP + timestamp + random)
        sessionId = generateSessionId();
        localStorage.setItem('ai_chat_session_id', sessionId);
    }

    return sessionId;
}

/**
 * Initialize widget
 */
$(document).ready(function() {
    // Widget açıldığında geçmişi yükle
    $('#chat-widget-toggle').on('click', function() {
        if ($('#chat-widget').is(':visible')) {
            loadConversationHistory();
        }
    });
});
```

---

## 📅 UYGULAMA ZAMANLAMA (Önerilen)

### Sprint 1: Temel Özellikler (1-2 gün)
- [x] Tıklanabilir butonlar (Backend prompt)
- [x] Tıklanabilir butonlar (Frontend parsing)
- [x] Typing indicator

### Sprint 2: Gelişmiş Özellikler (1-2 gün)
- [ ] Ürün kartları (Backend API)
- [ ] Ürün kartları (Frontend rendering)
- [ ] Konuşma geçmişi yükleme

### Sprint 3: İyileştirmeler (1 gün)
- [ ] Mobil responsive testing
- [ ] Performance optimization
- [ ] Analytics integration

---

## 🧪 TEST SENARYOLARı (Phase 2)

### Test 1: Tıklanabilir Butonlar
```
Müşteri: "Bir şey arıyorum"

AI: "Hangi tip makine arıyorsunuz? [BUTTON:Transpalet|Forklift|Reach Truck|İstif Makinesi]"

Frontend Render:
┌──────────────────────────┐
│ Hangi tip makine         │
│ arıyorsunuz?            │
│                          │
│ [Transpalet] [Forklift] │
│ [Reach Truck] [İstif]   │
└──────────────────────────┘

Kullanıcı "Transpalet" Tıklar:
→ Input'a "Transpalet" yazılır
→ Otomatik gönderilir
→ Butonlar disabled olur
```

### Test 2: Ürün Kartları
```
Müşteri: "2 ton transpalet"

AI: "İşte size en uygun modeller: [PRODUCTCARD:123] [PRODUCTCARD:124]"

Frontend Render:
┌─────────────────────────┐
│ [Ürün Görseli]         │
│ Litef EPT20            │
│ Litef Marka            │
│ Fiyat Sorunuz          │
│ [Detay Gör →]          │
└─────────────────────────┘

┌─────────────────────────┐
│ [Ürün Görseli]         │
│ Litef EPT20-Li         │
│ Litef Marka            │
│ ₺125,000               │
│ [Detay Gör →]          │
└─────────────────────────┘
```

### Test 3: Konuşma Geçmişi
```
Sayfa Yenileme:
→ localStorage'dan session_id oku
→ /api/ai/v1/conversation-history çağır
→ Tüm geçmiş mesajları render et

Sonuç:
- Müşteri kaldığı yerden devam eder
- Tekrar baştan anlatmaz
```

---

## 📊 BAŞARI KRİTERLERİ (KPI)

### Kullanıcı Deneyimi
- [ ] Buton tıklama oranı: %60+ (yazmak yerine tıklama)
- [ ] Ortalama yanıt süresi: <2 saniye
- [ ] Mobil kullanım oranı: %40+ (responsive)

### Teknik
- [ ] Sayfa yükleme: <1 saniye
- [ ] API yanıt süresi: <500ms
- [ ] JavaScript hata oranı: <0.1%

### İş Hedefleri
- [ ] Telefon toplama oranı: +20% artış
- [ ] Konuşma tamamlama oranı: +15% artış
- [ ] WhatsApp tıklama oranı: %25+

---

## 🚨 RİSK ANALİZİ

### Yüksek Risk
❌ **Frontend JavaScript uyumsuzluk**
- Mevcut chat widget kodu bilinmiyor
- Çözüm: Önce mevcut kodu incele, test et

❌ **Mobil responsive sorunları**
- Butonlar küçük ekranda taşabilir
- Çözüm: Max 2-3 buton, responsive CSS

### Orta Risk
⚠️ **AI buton syntax'ını unutabilir**
- Prompt uzun olunca atlayabilir
- Çözüm: Prompt'ta en üstte vurgula

⚠️ **Ürün görselleri yavaş yüklenebilir**
- Çok ürün kartı performansı düşürür
- Çözüm: Max 3 kart, lazy loading

### Düşük Risk
🟢 **Session ID conflict**
- Birden fazla cihazda aynı session
- Çözüm: IP + User Agent hash

---

## 📝 SON NOTLAR

1. **Phase 1 Bağımlılıklar:** Telegram config tamamlanmalı (.env)
2. **Frontend Kod:** Mevcut chat widget kodunu incelemek gerekiyor
3. **Test Ortamı:** Önce staging/local'de test, sonra prod
4. **Geri Bildirim:** Her sprint sonrası kullanıcı testi yap

---

**PLANLAMA HAZIR! Şimdi uygulama aşamasına geçilebilir.** 🚀

Hangi sprint'ten başlamak istersiniz?
1. Sprint 1: Temel Özellikler (Buton + Typing)
2. Sprint 2: Gelişmiş Özellikler (Ürün Kartları)
3. Mevcut chat widget kodunu incele
