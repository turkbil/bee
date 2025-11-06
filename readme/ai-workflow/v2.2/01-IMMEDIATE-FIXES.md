# 🚀 IMMEDIATE FIXES - AI System Improvements
**Priority:** CRITICAL
**Time Required:** 15 minutes
**Impact:** HIGH

---

## ⚡ QUICK FIXES (5 DAKİKA)

### 1. System Prompt Düzeltme

```sql
-- ÇALIŞTIR (tenant_ixtif database)
UPDATE ai_flows
SET flow_data = JSON_SET(
    flow_data,
    '$.nodes[5].config.system_prompt',
    'Kullanıcıyla doğal ve samimi konuş. Kısa cevaplar ver (2-3 cümle). Ürün bulursan özellik ve fiyat göster. Kendini tanıtma, AI olduğunu söyleme.'
)
WHERE id = 6;

-- Verify
SELECT JSON_EXTRACT(flow_data, '$.nodes[5].config.system_prompt') FROM ai_flows WHERE id = 6;
```

### 2. System Prompt Directive Ekle

```sql
-- System prompt'u directive olarak ekle
INSERT INTO ai_tenant_directives (tenant_id, directive_key, directive_value, directive_type, category, is_active) VALUES
(2, 'system_prompt', 'Doğal konuş. Kendini tanıtma. Ürün varsa kısa liste yap, fiyat göster.', 'string', 'ai_config', 1);

-- Anti-robotic rules
INSERT INTO ai_tenant_directives (tenant_id, directive_key, directive_value, directive_type, category, is_active) VALUES
(2, 'forbidden_phrases', '["ben bir AI", "yapay zeka asistanı", "e-ticaret", "asistan olarak"]', 'json', 'ai_config', 1),
(2, 'response_style', 'casual_friendly', 'string', 'ai_config', 1),
(2, 'max_response_sentences', '3', 'integer', 'ai_config', 1);
```

### 3. Welcome Message Variations

```sql
-- Multiple welcome messages
INSERT INTO ai_tenant_directives (tenant_id, directive_key, directive_value, directive_type, category, is_active) VALUES
(2, 'welcome_messages', '["Merhaba! Nasıl yardımcı olabilirim?", "Hoş geldiniz! Ne arıyorsunuz?", "Merhaba! Size nasıl yardımcı olabilirim?", "İyi günler! Hangi ürünle ilgileniyorsunuz?"]', 'json', 'chat', 1);

-- Context-specific greetings
INSERT INTO ai_tenant_directives (tenant_id, directive_key, directive_value, directive_type, category, is_active) VALUES
(2, 'morning_greetings', '["Günaydın! Nasıl yardımcı olabilirim?", "Günaydın, hoş geldiniz!"]', 'json', 'chat', 1),
(2, 'evening_greetings', '["İyi akşamlar! Ne arıyorsunuz?", "İyi akşamlar, size nasıl yardımcı olabilirim?"]', 'json', 'chat', 1);
```

---

## 🔧 CODE UPDATES (10 DAKİKA)

### 1. AIResponseNode.php Güncelleme

```php
// FILE: Modules/AI/app/Services/Workflow/Nodes/AIResponseNode.php

// ADD: After line 20 (getDirectiveValue method'undan sonra)
protected function getRandomWelcomeMessage(): string
{
    // Try to get welcome messages array
    $welcomeMessages = $this->getDirectiveValue('welcome_messages', 'json', null);

    if ($welcomeMessages && is_array($welcomeMessages)) {
        return $welcomeMessages[array_rand($welcomeMessages)];
    }

    // Check time-based greetings
    $hour = (int) date('H');
    if ($hour >= 5 && $hour < 12) {
        $greetings = $this->getDirectiveValue('morning_greetings', 'json', null);
        if ($greetings && is_array($greetings)) {
            return $greetings[array_rand($greetings)];
        }
    } elseif ($hour >= 18) {
        $greetings = $this->getDirectiveValue('evening_greetings', 'json', null);
        if ($greetings && is_array($greetings)) {
            return $greetings[array_rand($greetings)];
        }
    }

    // Fallback
    return $this->getDirectiveValue('welcome_message', 'string', 'Merhaba! Nasıl yardımcı olabilirim?');
}

// UPDATE: Line 187-206 (prepareMessages method içinde)
// ESKİ KOD:
// $welcomeMessage = null;
// try {
//     $directive = \App\Models\AITenantDirective::where...
// }

// YENİ KOD:
$welcomeMessage = $this->getRandomWelcomeMessage();

// UPDATE: Line 48 (execute method içinde)
// System prompt için directive desteği ekle
$systemPrompt = $this->getDirectiveValue('system_prompt', 'string',
    $this->getConfig('system_prompt', '')
);

// Anti-robotic filter
$forbiddenPhrases = $this->getDirectiveValue('forbidden_phrases', 'json', []);
if (!empty($forbiddenPhrases) && is_array($forbiddenPhrases)) {
    foreach ($forbiddenPhrases as $phrase) {
        if (stripos($systemPrompt, $phrase) !== false) {
            $systemPrompt = str_ireplace($phrase, '', $systemPrompt);
        }
    }
}
```

### 2. ProductSearchNode.php İyileştirme

```php
// FILE: Modules/AI/app/Services/Workflow/Nodes/ProductSearchNode.php

// UPDATE: Line 81 (extractKeywords method)
protected function extractKeywords(string $message): array
{
    $keywords = [];
    $message = mb_strtolower($message);

    // Genişletilmiş keyword listesi
    $productTypes = [
        // Mevcut
        'transpalet', 'forklift', 'istif', 'istif makinesi',
        'akülü', 'elektrikli', 'manuel', 'palet', 'platform',

        // Yeni eklemeler
        'kaldırıcı', 'yük', 'depo', 'lojistik', 'taşıyıcı',
        'reach truck', 'stacker', 'çekici', 'transpaletler',
        'forkliftler', 'makinası', 'makina', 'ekipman'
    ];

    // Fiyat keywords
    $priceKeywords = [
        'fiyat', 'kaç para', 'ne kadar', 'ücret', 'tutar',
        'en ucuz', 'en uygun', 'en pahalı', 'bütçe'
    ];

    // Check product types
    foreach ($productTypes as $type) {
        if (str_contains($message, $type)) {
            $keywords[] = $type;
        }
    }

    // Check if price query
    foreach ($priceKeywords as $price) {
        if (str_contains($message, $price)) {
            $keywords[] = 'price_query'; // Special keyword
            break;
        }
    }

    return array_unique($keywords);
}
```

---

## ⚙️ CACHE CLEAR (2 DAKİKA)

```bash
# 1. Cache temizle
php artisan view:clear
php artisan responsecache:clear
php artisan cache:clear

# 2. OPcache reset
curl -s -k https://ixtif.com/opcache-reset.php
curl -s -k https://a.test/opcache-reset.php

# 3. Restart services (if needed)
# brew services restart php
# valet restart
```

---

## ✅ TEST CHECKLIST

### Test 1: Karşılama Çeşitliliği
```bash
# Farklı session'larla test et
for i in {1..5}; do
  curl -X POST https://a.test/api/ai/v1/shop-assistant/chat \
    -H "Content-Type: application/json" \
    -d "{\"message\":\"merhaba\",\"session_id\":\"test_$i\"}" \
    | jq '.data.message'
  sleep 1
done

# Farklı karşılama mesajları görmeli
```

### Test 2: Ürün Arama
```bash
curl -X POST https://a.test/api/ai/v1/shop-assistant/chat \
  -H "Content-Type: application/json" \
  -d '{"message":"transpalet","session_id":"test_product"}'

# Ürün listesi görmeli, "e-ticaret asistanı" görmemeli
```

### Test 3: Doğal Konuşma
```bash
curl -X POST https://a.test/api/ai/v1/shop-assistant/chat \
  -H "Content-Type: application/json" \
  -d '{"message":"en ucuz transpalet hangisi","session_id":"test_price"}'

# Direkt fiyat görmeli, uzun açıklama olmamalı
```

---

## 🎯 BAŞARI KRİTERLERİ

### ✅ BAŞARILI:
```
User: merhaba
AI: Hoş geldiniz! Ne arıyorsunuz?

User: transpalet
AI: İşte transpalet modellerimiz:
• Manuel 2.5 ton - 8,500 TL
• Elektrikli 2 ton - 45,000 TL
Detay isterseniz söyleyin.
```

### ❌ BAŞARISIZ:
```
User: merhaba
AI: Merhaba! Ben bir e-ticaret asistanıyım. Size nasıl yardımcı olabilirim?

User: transpalet
AI: E-ticaret sitemizde transpalet kategorisinde ürünlerimiz mevcuttur.
```

---

## 📊 MONITORING

```bash
# Log izleme
tail -f storage/logs/laravel.log | grep -E "AI Response|Welcome|Product"

# Response time check
time curl -X POST https://a.test/api/ai/v1/shop-assistant/chat \
  -H "Content-Type: application/json" \
  -d '{"message":"test","session_id":"perf_test"}'
```

---

## 🔄 ROLLBACK PLAN

Eğer sorun çıkarsa:

```sql
-- Eski system prompt'a dön
UPDATE ai_flows
SET flow_data = JSON_SET(
    flow_data,
    '$.nodes[5].config.system_prompt',
    'Sen bir e-ticaret asistanısın. Ürünleri markdown formatında öner.'
)
WHERE id = 6;

-- Directive'leri deaktif et
UPDATE ai_tenant_directives
SET is_active = 0
WHERE tenant_id = 2 AND directive_key IN ('system_prompt', 'forbidden_phrases', 'welcome_messages');
```

---

**⏱️ Toplam Süre:** 15 dakika
**📈 Beklenen İyileşme:** %80 daha doğal konuşma
**🎯 Risk:** Düşük (fallback var)