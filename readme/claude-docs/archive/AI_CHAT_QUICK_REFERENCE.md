# AI Chat System - Quick Reference Guide

**Last Updated:** 2025-11-05  
**Full Analysis:** `AI_CHAT_ARCHITECTURE_ANALYSIS_2025-11-05.md`

---

## 🚀 System Entry Point

```
POST /api/ai/v1/shop-assistant/chat
↓
PublicAIController::shopAssistantChat()
↓
shopAssistantChatV2()  (if AI_USE_WORKFLOW_ENGINE=true)
↓
ConversationFlowEngine::processMessage()
```

---

## 🔑 Key Classes & Files

| Purpose | File Path | Key Methods |
|---------|-----------|-------------|
| **API Controller** | `Modules/AI/app/Http/Controllers/Api/PublicAIController.php` | `shopAssistantChat()`, `shopAssistantChatV2()` |
| **Flow Orchestrator** | `app/Services/ConversationFlowEngine.php` | `processMessage()` |
| **Node Executor** | `app/Services/ConversationNodes/NodeExecutor.php` | `execute()` |
| **Node Registry** | `ai_workflow_nodes` table | Node type → Class mapping |
| **Flow Definition** | `tenant_conversation_flows` table | Flow structure (JSON) |
| **Conversation State** | `ai_conversations` table | Session tracking |

---

## 🔴 PROBLEM: Repetitive "Merhaba! Size nasıl yardımcı olabilirim?" Message

### Quick Diagnosis

1. **Check Logs:**
   ```bash
   tail -f storage/logs/laravel.log | grep "shopAssistantChat\|ConversationFlow"
   ```

2. **Check Configuration:**
   ```bash
   grep "AI_USE_WORKFLOW_ENGINE" .env
   # Should be: AI_USE_WORKFLOW_ENGINE=true
   ```

3. **Check Flow Exists:**
   ```bash
   php artisan tinker
   >>> DB::table('tenant_conversation_flows')->where('tenant_id', 2)->where('is_active', 1)->first();
   ```

4. **Check Flow Has AI Node:**
   ```bash
   >>> $flow = DB::table('tenant_conversation_flows')->where('tenant_id', 2)->first();
   >>> collect(json_decode($flow->flow_data)->nodes)->pluck('type');
   # Should include: 'ai_response'
   ```

### Root Causes & Fixes

| Symptom | Root Cause | Fix |
|---------|-----------|-----|
| Always returns fallback message | No AI Response node in flow | Add AI Response node to flow |
| Old flow still used | Flow cache not cleared | `ConversationFlowEngine::clearFlowCache(2)` |
| Config not updating | Config cached | `php artisan config:clear` |
| Node class not found | Registry mapping wrong | Check `ai_workflow_nodes` table |

---

## 💾 Critical Database Tables

### 1. tenant_conversation_flows (Central DB)
```php
// Query active flow for tenant
DB::table('tenant_conversation_flows')
    ->where('tenant_id', 2)
    ->where('is_active', 1)
    ->first()
```

**Structure:**
- `id` - Flow ID
- `tenant_id` - Which tenant
- `flow_name` - Display name
- `flow_data` - JSON: Full flow definition with nodes
- `start_node_id` - Entry point
- `is_active` - Currently used

### 2. ai_conversations (Central DB)
```php
// Query active conversations
DB::table('ai_conversations')
    ->where('tenant_id', 2)
    ->where('session_id', $sessionId)
    ->first()
```

**Tracks:**
- `current_node_id` - Where in flow execution
- `context_data` - Accumulated context (JSON)
- `state_history` - Node traversal log

### 3. ai_workflow_nodes (Central DB - Node Registry)
```php
// Check node mappings
DB::table('ai_workflow_nodes')
    ->where('is_active', 1)
    ->select('node_key', 'node_class')
    ->get()
```

**Maps:**
- `node_key` (e.g., 'welcome') → `node_class` (e.g., 'App\Services\...\WelcomeNode')

### 4. ai_conversation_messages (Central DB)
```php
// Query conversation history
DB::table('ai_conversation_messages')
    ->where('conversation_id', $conversationId)
    ->orderBy('created_at', 'asc')
    ->get()
```

**Stores:**
- `role` - 'user' or 'assistant'
- `content` - Message text

---

## 🔧 Debugging Commands

### Clear All Caches
```bash
php artisan cache:clear && \
php artisan config:clear && \
php artisan route:clear && \
php artisan view:clear && \
echo "✅ All caches cleared"
```

### Clear Flow Cache Only
```bash
php artisan tinker
>>> App\Services\ConversationFlowEngine::clearFlowCache(2);
>>> "✅ Flow cache cleared for tenant 2"
```

### Check Active Flow
```bash
php artisan tinker
>>> $flow = DB::table('tenant_conversation_flows')->where('tenant_id', 2)->where('is_active', 1)->first();
>>> json_decode($flow->flow_data, true)
```

### Test Chat Directly
```bash
php artisan tinker
>>> $engine = app(App\Services\ConversationFlowEngine::class);
>>> $result = $engine->processMessage('test-session', 2, 'Merhaba');
>>> dd($result);
```

### Monitor Logs in Real-Time
```bash
tail -f storage/logs/laravel.log | grep -i "conversation\|node\|workflow"
```

---

## 🔌 Node Types Available

### Common Nodes
- `welcome` - Initial greeting
- `ai_response` - Generate AI response
- `context_builder` - Prepare context
- `history_loader` - Load conversation history
- `message_saver` - Save to database
- `condition` - Branch logic
- `end` - Terminate flow

### Shop Nodes (ixtif.com)
- `category_detection` - Detect product category
- `product_search` - Search products
- `price_query` - Handle price questions
- `product_comparison` - Compare products
- `stock_sorter` - Sort by availability
- `contact_request` - Contact form

---

## 📊 Flow Data Structure

```json
{
  "nodes": [
    {
      "id": "node_1",
      "type": "welcome",
      "name": "Greeting",
      "config": {
        "welcome_message": "Merhaba!",
        "next_node": "node_2"
      }
    },
    {
      "id": "node_2",
      "type": "ai_response",
      "name": "AI Cevap",
      "config": {
        "prompt": "Sen yardımcı bir asistan...",
        "next_node": "node_3"
      }
    },
    {
      "id": "node_3",
      "type": "end",
      "name": "Bitir",
      "config": {}
    }
  ]
}
```

---

## 🎯 Execution Flow (Simplified)

```
1. User sends message
   ↓
2. Webhook checks: AI_USE_WORKFLOW_ENGINE?
   ├─ true → V2 (ConversationFlowEngine)
   └─ false → V1 (Legacy)
   ↓
3. Load conversation (or create new)
   ↓
4. Load flow from cache/DB
   ↓
5. Loop: Execute nodes (max 20)
   ├─ Get current node
   ├─ Execute node handler
   ├─ Update conversation state
   ├─ If type='ai_response' → Generate AI response
   └─ Move to next node
   ↓
6. Return response (or fallback if null)
```

---

## ⚠️ Common Issues

### "Always returns 'Merhaba! Size nasıl yardımcı olabilirim?'"
- **Cause:** AI Response node missing or returns null
- **Fix:** Add AI Response node to flow with valid prompt

### "Old flow still being used"
- **Cause:** Cache not cleared
- **Fix:** 
  ```bash
  php artisan cache:clear
  ConversationFlowEngine::clearFlowCache(2)
  ```

### "Unknown node type: X"
- **Cause:** Node class not in registry
- **Fix:** Check `ai_workflow_nodes` table for correct mapping

### "No active flow configured"
- **Cause:** No flow in `tenant_conversation_flows` for tenant
- **Fix:** Create active flow for tenant

---

## 🔐 Configuration

**File:** `.env`
```
AI_USE_WORKFLOW_ENGINE=true    # Use V2 workflow engine
```

**File:** `config/ai.php` (line 34)
```php
'use_workflow_engine' => env('AI_USE_WORKFLOW_ENGINE', true),
```

---

## 📚 Related Documentation

- **Full Analysis:** `AI_CHAT_ARCHITECTURE_ANALYSIS_2025-11-05.md`
- **Node Development:** `/Modules/AI/app/Services/ConversationNodes/AbstractNode.php`
- **AI Service:** `/app/Services/AI/CentralAIService.php`
- **Database Migrations:** `/database/migrations/tenant/`

---

## 🚨 Emergency Debug

If everything is broken, check in this order:

1. **Logs:** `tail -100 storage/logs/laravel.log`
2. **Config:** `php artisan config:cache` exists?
3. **Flow:** Active row in `tenant_conversation_flows`?
4. **Nodes:** Rows in `ai_workflow_nodes`?
5. **Cache:** `php artisan cache:clear && php artisan config:clear`
6. **OPcache:** Clear via `opcache-reset.php`

---

## 📞 Support

If issues persist:
1. Check full analysis document
2. Review logs carefully (timestamps matter!)
3. Check database consistency
4. Run: `php artisan tinker` to debug live
5. Clear all caches and retry

