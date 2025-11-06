# 📋 AI Chat V3 - TODO & Action Plan
**Date:** 2025-11-05
**Priority:** CRITICAL
**System:** ixtif.com AI Chat Bot

---

## 🚨 IMMEDIATE FIXES (Do First!)

### ✅ Task 1: Enable Debug Logging
**Priority:** CRITICAL
**Time:** 2 minutes
**Why:** Can't fix what we can't see

```php
// FILE: Modules/AI/App/Http/Controllers/Api/PublicAIController.php
// LINE: 556 (After config check)

\Log::emergency('🚨🚨🚨 WORKFLOW ENGINE DECISION', [
    'config_value' => $useNewSystem,
    'type_of_config' => gettype($useNewSystem),
    'will_use_v2' => $useNewSystem === true,
    'timestamp' => now()->toIso8601String(),
]);
```

### ✅ Task 2: Force Config Refresh
**Priority:** CRITICAL
**Time:** 1 minute
**Why:** Config cached, not reading .env

```bash
# Nuclear cache clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
rm -rf bootstrap/cache/*.php
php artisan config:cache
php artisan route:cache

# OPcache reset
curl -s -k https://ixtif.com/opcache-reset.php

# Restart PHP
brew services restart php
```

### ✅ Task 3: Test V2 Activation
**Priority:** HIGH
**Time:** 2 minutes

```bash
# Test API call
curl -X POST https://ixtif.com/api/ai/v1/shop-assistant/chat \
  -H "Content-Type: application/json" \
  -d '{"message":"test v2 system","session_id":"debug_v2_test"}'

# Check logs immediately
tail -100 storage/logs/laravel.log | grep -E "WORKFLOW|shopAssistantChat"
```

---

## 🔧 V2 SYSTEM FIXES

### ✅ Task 4: Fix Null Response in V2
**Priority:** HIGH
**File:** `app/Services/ConversationFlowEngine.php`
**Line:** 177-186

```php
// BEFORE (returns null):
return [
    'success' => true,
    'response' => $aiResponse,  // Often NULL!
    // ...
];

// AFTER (with fallback):
return [
    'success' => true,
    'response' => $aiResponse ?: $this->generateFallbackResponse($conversation),
    // ...
];

// Add this method:
protected function generateFallbackResponse($conversation): string
{
    \Log::warning('⚠️ No AI response generated, using fallback');
    return 'Merhaba! Size nasıl yardımcı olabilirim? Ürünlerimiz hakkında bilgi almak ister misiniz?';
}
```

### ✅ Task 5: Check Flow Configuration
**Priority:** HIGH
**Time:** 5 minutes

```sql
-- 1. Find active flow for tenant 2
SELECT id, flow_name, is_active, start_node_id,
       JSON_LENGTH(flow_data->'$.nodes') as node_count
FROM tenant_ixtif.tenant_conversation_flows
WHERE tenant_id = 2 AND is_active = 1;

-- 2. Check if flow has AI Response node
SELECT
    id,
    flow_name,
    JSON_CONTAINS(
        JSON_EXTRACT(flow_data, '$.nodes[*].type'),
        '"ai_response"'
    ) as has_ai_response_node
FROM tenant_ixtif.tenant_conversation_flows
WHERE tenant_id = 2 AND is_active = 1;

-- 3. If no AI Response node, check what nodes exist
SELECT
    JSON_UNQUOTE(JSON_EXTRACT(node.value, '$.type')) as node_type,
    JSON_UNQUOTE(JSON_EXTRACT(node.value, '$.id')) as node_id,
    JSON_UNQUOTE(JSON_EXTRACT(node.value, '$.name')) as node_name
FROM tenant_ixtif.tenant_conversation_flows,
     JSON_TABLE(flow_data->'$.nodes', '$[*]' COLUMNS (value JSON PATH '$')) as node
WHERE tenant_id = 2 AND is_active = 1;
```

### ✅ Task 6: Add AI Response Node to Flow
**Priority:** MEDIUM
**If:** No AI Response node found

```php
// Use tinker to add node
php artisan tinker

$flow = \App\Models\TenantConversationFlow::where('tenant_id', 2)
    ->where('is_active', true)->first();

$flowData = $flow->flow_data;

// Add AI Response node
$aiNode = [
    'id' => 'node_ai_response',
    'type' => 'ai_response',
    'name' => 'AI Yanıt',
    'config' => [
        'use_streaming' => false,
        'max_tokens' => 500,
    ],
    'pos_x' => 400,
    'pos_y' => 300,
];

$flowData['nodes'][] = $aiNode;

// Connect it in the flow
$flowData['edges'][] = [
    'source' => 'node_3', // sentiment_detection
    'target' => 'node_ai_response',
];

$flow->flow_data = $flowData;
$flow->save();
```

---

## 🐛 V1 SYSTEM FIXES (If V2 Can't Be Fixed)

### ✅ Task 7: Fix Repetitive Response in V1
**Priority:** MEDIUM
**File:** `Modules/AI/App/Http/Controllers/Api/PublicAIController.php`
**Line:** 750-850 (Orchestrator section)

Check these:
1. Is smart search returning products?
2. Is orchestrator getting correct context?
3. Is AI prompt being generated properly?

```php
// Add debug logs
\Log::info('🔍 DEBUG: Smart Search Results', [
    'products_found' => count($smartSearchResults['products'] ?? []),
    'first_product' => $smartSearchResults['products'][0] ?? null,
]);

\Log::info('🔍 DEBUG: Orchestrator Input', [
    'has_products' => !empty($contextOptions['smart_search_results']),
    'user_message' => $contextOptions['user_message'],
]);
```

---

## 📊 MONITORING & VALIDATION

### ✅ Task 8: Set Up Monitoring
**Priority:** LOW
**Time:** 10 minutes

```bash
# Create monitoring script
cat > /tmp/monitor_ai.sh << 'EOF'
#!/bin/bash
while true; do
    clear
    echo "=== AI CHAT MONITOR ==="
    echo "Time: $(date)"
    echo ""
    echo "=== LAST 10 WORKFLOW LOGS ==="
    tail -100 /Users/nurullah/Desktop/cms/laravel/storage/logs/laravel.log | \
        grep -E "WORKFLOW|shopAssistant" | tail -10
    echo ""
    echo "=== V2 CALLS (Last Hour) ==="
    grep "shopAssistantChatV2 STARTED" /Users/nurullah/Desktop/cms/laravel/storage/logs/laravel.log | \
        grep "$(date +'%Y-%m-%d %H')" | wc -l
    echo ""
    echo "=== V1 CALLS (Last Hour) ==="
    grep "shopAssistantChat STARTED (OLD SYSTEM)" /Users/nurullah/Desktop/cms/laravel/storage/logs/laravel.log | \
        grep "$(date +'%Y-%m-%d %H')" | wc -l
    sleep 5
done
EOF

chmod +x /tmp/monitor_ai.sh
# Run: /tmp/monitor_ai.sh
```

---

## 📝 CHECKLIST

### Phase 1: Debug (5 min)
- [ ] Add emergency log to controller
- [ ] Clear all caches
- [ ] Test API call
- [ ] Check logs for WORKFLOW entries

### Phase 2: Fix V2 (15 min)
- [ ] Add fallback response to ConversationFlowEngine
- [ ] Check flow has AI Response node
- [ ] Add AI Response node if missing
- [ ] Test V2 system works

### Phase 3: Fix V1 (10 min) - Only if V2 fails
- [ ] Add debug logs to V1 orchestrator
- [ ] Check smart search results
- [ ] Verify AI prompt generation
- [ ] Fix prompt template

### Phase 4: Validate (5 min)
- [ ] Test "Merhaba" - should get different response
- [ ] Test "transpalet fiyatı" - should get products
- [ ] Test "nasılsın" - should get contextual response
- [ ] Check logs show correct system (V1 or V2)

---

## 🎯 SUCCESS CRITERIA

✅ **FIXED** when:
1. Different responses for different inputs (no more repetitive "Merhaba!")
2. Logs show which system is running (V1 or V2)
3. Product searches return actual products
4. Conversation context is maintained

❌ **NOT FIXED** if:
1. Still getting same "Merhaba!" for all inputs
2. No logs appearing
3. Config changes not taking effect
4. V2 still returning null

---

## 💡 QUICK WIN

**If nothing else works:**

```php
// NUCLEAR OPTION - Force V1 system
// FILE: PublicAIController.php, Line 554

// $useNewSystem = config('ai.use_workflow_engine', false);
$useNewSystem = false; // FORCE V1 SYSTEM

\Log::emergency('🚨 FORCED V1 SYSTEM DUE TO V2 ISSUES');
```

This at least gets you back to a working (if repetitive) system.

---

## 📞 ESCALATION

If after 30 minutes nothing works:
1. Check if production server is different from local
2. Check nginx/Apache response caching
3. Check CloudFlare or CDN caching
4. Consider rolling back recent deployments

---

**Remember:** The code is good, this is a configuration/deployment issue!