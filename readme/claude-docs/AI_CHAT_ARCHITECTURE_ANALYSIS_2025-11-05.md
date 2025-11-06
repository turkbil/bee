# AI Chat System Architecture - Complete Deep Dive Analysis

**Analysis Date:** 2025-11-05  
**Codebase:** Laravel CMS with Multi-tenant AI System  
**Tenant Context:** ixtif.com (Tenant ID: 2)  
**Current System Status:** NEW V2 WORKFLOW ENGINE ACTIVE (AI_USE_WORKFLOW_ENGINE=true)

---

## 📊 EXECUTIVE SUMMARY

The system uses a **NEW node-based ConversationFlowEngine** (V2) instead of the legacy direct AI response system. The repetitive "Merhaba! Size nasıl yardımcı olabilirim?" message is returned when:

1. **Workflow Engine enabled** (AI_USE_WORKFLOW_ENGINE=true in .env)
2. **Welcome Node executes** at the start of each conversation
3. **AI Response Node fails/returns null** causing fallback to default message
4. **Configuration cache stale** - workflow engine doesn't see latest flow changes
5. **Welcome Node config missing** - defaults to hardcoded message

---

## 🏗️ ARCHITECTURE OVERVIEW

### System Layers (Top-Down)

```
┌─────────────────────────────────────────────────────────────┐
│ PUBLIC API ENDPOINT                                         │
│ POST /api/ai/v1/shop-assistant/chat                        │
│ (PublicAIController::shopAssistantChat)                    │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│ CONFIGURATION CHECK                                         │
│ config('ai.use_workflow_engine') [DEFAULT: true]           │
│ Routes to V2 if enabled, V1 if disabled                    │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│ CONVERSATION FLOW ENGINE (V2) - NEW SYSTEM                 │
│ App\Services\ConversationFlowEngine::processMessage()      │
│ - Multi-node execution loop (max 20 iterations)            │
│ - Node orchestration and state management                  │
│ - Context aggregation across nodes                         │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│ NODE EXECUTOR SERVICE                                       │
│ App\Services\ConversationNodes\NodeExecutor::execute()     │
│ - Registry-based node resolution                           │
│ - Node handler instantiation                               │
│ - Configuration validation                                 │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│ NODE HANDLERS (Pluggable Architecture)                      │
│ Common:                    Shop-Specific:                   │
│ - WelcomeNode              - CategoryDetectionNode          │
│ - AIResponseNode           - ProductSearchNode             │
│ - ContextBuilderNode       - PriceQueryNode                │
│ - HistoryLoaderNode        - ProductComparisonNode         │
│ - MessageSaverNode         - StockSorterNode               │
│ - EndNode                  - ContactRequestNode            │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│ AI SERVICE (Response Generation)                            │
│ App\Services\AI\CentralAIService::executeRequest()         │
│ - Provider selection (OpenAI, DeepSeek, Anthropic)         │
│ - Prompt engineering with context                          │
│ - Response parsing and formatting                          │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│ DATA PERSISTENCE LAYER                                      │
│ - AIConversation (central DB - conversation state)         │
│ - AIConversationMessage (central DB - message history)     │
│ - TenantConversationFlow (central DB - flow definition)    │
│ - AIWorkflowNode (central DB - node registry)              │
│ - Redis Cache (session state, flow cache)                  │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔄 REQUEST FLOW - DETAILED BREAKDOWN

### 1. ENDPOINT: PublicAIController::shopAssistantChat()
**File:** `Modules/AI/app/Http/Controllers/Api/PublicAIController.php` (line 551)

```php
public function shopAssistantChat(Request $request): JsonResponse
{
    // Check if new V2 workflow engine should be used
    $useNewSystem = config('ai.use_workflow_engine', false);
    
    // KEY DECISION POINT
    if ($useNewSystem) {
        return $this->shopAssistantChatV2($request);  // ← NEW SYSTEM
    }
    
    // Otherwise use old system...
}
```

**Key Logic:**
- Reads `AI_USE_WORKFLOW_ENGINE=true` from `.env`
- Routes to NEW `shopAssistantChatV2()` method
- Currently ALWAYS routes to V2 system

### 2. ROUTER: shopAssistantChatV2()
**File:** `Modules/AI/app/Http/Controllers/Api/PublicAIController.php` (line 2532)

```php
protected function shopAssistantChatV2(Request $request): JsonResponse
{
    // Validate request
    $validated = $request->validate([
        'message' => 'required|string|min:1|max:1000',
        'product_id' => 'nullable|integer',
        'category_id' => 'nullable|integer',
        'page_slug' => 'nullable|string|max:255',
        'session_id' => 'nullable|string|max:64',
    ]);
    
    // Generate or reuse session_id
    $sessionId = $validated['session_id'] ?? $this->generateSessionId($request);
    
    // 🔑 CRITICAL: Route to ConversationFlowEngine
    $engine = app(\App\Services\ConversationFlowEngine::class);
    
    $result = $engine->processMessage(
        $sessionId,
        tenant('id'),           // Tenant ID (usually 2 for ixtif.com)
        $validated['message'],
        auth()->id()            // User ID (null for guests)
    );
    
    // Map flow result to API response
    if ($result['success']) {
        return response()->json([
            'success' => true,
            'data' => [
                'message' => $result['response'] ?? 'Merhaba! Size nasıl yardımcı olabilirim?',
                'session_id' => $sessionId,
                'conversation_id' => $result['conversation_id'] ?? null,
                // ... metadata
            ],
        ]);
    }
    
    // Fallback to error
    return response()->json([...], 500);
}
```

**Critical Issue:** 
- **Line 2568**: Fallback message if `$result['response']` is null!
- This is where "Merhaba! Size nasıl yardımcı olabilirim?" comes from
- If flow execution returns null, this hardcoded message is used

### 3. ORCHESTRATOR: ConversationFlowEngine::processMessage()
**File:** `app/Services/ConversationFlowEngine.php` (line 35)

```php
public function processMessage(
    string $sessionId,
    int $tenantId,
    string $userMessage,
    ?int $userId = null
): array {
    // 1. Get or create conversation
    $conversation = AIConversation::getOrCreateForSession($sessionId, $tenantId, $userId);
    
    // 2. Load active flow for tenant
    $flow = $this->getFlow($conversation);  // Cached for 1 hour
    
    if (!$flow) {
        return $this->fallbackResponse('No active flow configured');  // ← ISSUE #1
    }
    
    // 3. MULTI-NODE EXECUTION LOOP (max 20 iterations)
    $maxIterations = 20;
    $iteration = 0;
    $executedNodes = [];
    $finalResult = null;
    $aiResponse = null;  // ← STARTS AS NULL
    
    while ($iteration < $maxIterations) {
        $iteration++;
        
        // Get current node
        $currentNode = $this->getCurrentNode($conversation, $flow);
        
        if (!$currentNode) {
            break;  // ← ISSUE #2: Early exit without AI response
        }
        
        // Execute node
        $result = $this->executor->execute($currentNode, $conversation, $userMessage);
        
        if (!$result['success']) {
            return $this->handleError($conversation, $result);  // ← ISSUE #3
        }
        
        // Update conversation state
        $this->updateConversationState($conversation, $currentNode, $result);
        
        // If AI Response node - generate response
        if ($currentNode['type'] === 'ai_response' && !empty($result['prompt'])) {
            $aiContext = $this->buildAIContext($conversation, $result);
            $aiContext['user_message'] = $userMessage;
            
            // Generate AI response
            $aiResponse = $this->generateAIResponse($result['prompt'], $aiContext);
            
            // Store in context
            $conversation->addToContext('last_ai_response', $aiResponse);
        }
        
        // If end node, stop
        if ($currentNode['type'] === 'end') {
            break;
        }
        
        // Store final result
        $finalResult = $result;
        
        // If no next_node, stop
        if (empty($result['next_node'])) {
            break;
        }
    }
    
    // Return result with response (or null if never set)
    return [
        'success' => true,
        'response' => $aiResponse,  // ← CAN BE NULL HERE
        'nodes_executed' => $executedNodes,
        'context' => [
            'flow_name' => $flow->flow_name,
            'nodes_executed' => array_column($executedNodes, 'type'),
        ],
        'conversation_id' => $conversation->id,
    ];
}
```

**Critical Paths Where Response is NULL:**

| Issue | Condition | Result |
|-------|-----------|--------|
| #1 | No active flow for tenant | Returns fallback error |
| #2 | No current node found | Loop breaks, aiResponse stays NULL |
| #3 | Node execution fails | Returns error, aiResponse stays NULL |
| #4 | No AI Response node executed | aiResponse never set, stays NULL |
| #5 | AI Response node returns null | aiResponse becomes null |
| #6 | Empty prompt in AI node | AI node skipped, aiResponse stays NULL |

When any of these happen → `$result['response']` is NULL → Fallback message used!

### 4. NODE EXECUTOR: NodeExecutor::execute()
**File:** `app/Services/ConversationNodes/NodeExecutor.php` (line 129)

```php
public function execute(array $nodeData, AIConversation $conversation, string $userMessage): array
{
    try {
        // 🚨 CRITICAL: ALWAYS reinitialize registry on execute()
        // This ensures fresh tenant context
        self::$initialized = false;
        self::$nodeRegistry = [];
        $this->initializeRegistry($conversation->tenant_id);  // Force tenant context
        self::$initialized = true;
        
        // Resolve node handler class
        $handlerClass = $this->resolveNodeHandler($nodeData['type']);
        
        // Instantiate and execute
        $handler = new $handlerClass($nodeData['config'] ?? []);
        
        if (!$handler->validate()) {
            throw new \Exception("Invalid node configuration for {$nodeData['type']}");
        }
        
        // EXECUTE NODE
        $result = $handler->execute($conversation, $userMessage);
        
        return $result;
        
    } catch (\Exception $e) {
        Log::error('Node execution failed', [
            'node_id' => $nodeData['id'],
            'error' => $e->getMessage(),
        ]);
        
        return [  // ← RETURNS FAILED RESULT
            'success' => false,
            'error' => $e->getMessage(),
            'next_node' => null,
            'prompt' => null,
            'data' => [],
        ];
    }
}
```

**Node Registry Issue:**
- Registry loads from `ai_workflow_nodes` table
- Maps node type → handler class (e.g., `'welcome'` → `'App\Services\ConversationNodes\Common\WelcomeNode'`)
- If class not found → Node execution fails
- If registry not properly initialized → Node handler resolution fails

### 5. WELCOME NODE: WelcomeNode::execute()
**File:** `app/Services/ConversationNodes/Common/WelcomeNode.php` (line 16)

```php
public function execute(AIConversation $conversation, string $userMessage): array
{
    // Get welcome message from config
    $welcomeMessage = $this->getConfig('welcome_message', 'Merhaba! Size nasıl yardımcı olabilirim?');
    //                                                      ↑ DEFAULT FALLBACK
    
    $showSuggestions = $this->getConfig('show_suggestions', false);
    $suggestions = $this->getConfig('suggestions', []);
    
    $data = [
        'node_type' => 'welcome',
        'welcome_message' => $welcomeMessage,
    ];
    
    if ($showSuggestions && !empty($suggestions)) {
        $data['suggestions'] = $suggestions;
    }
    
    $nextNode = $this->getConfig('next_node');
    
    return $this->success(
        null,      // ← No AI prompt
        $data,     // ← Returns welcome message in data
        $nextNode
    );
}
```

**Why Welcome Message is Returned:**
- Welcome node returns `$data['welcome_message']` 
- But this is **stored in data**, not in the final response
- The response comes from **AI Response node**, not Welcome node
- If AI Response node fails → null is returned
- Fallback to hardcoded message

---

## 🔴 ROOT CAUSES OF REPETITIVE MESSAGES

### ROOT CAUSE #1: No Active Flow Configured
**Condition:** `TenantConversationFlow::getActiveFlowForTenant($tenantId)` returns NULL

**Flow:**
1. `ConversationFlowEngine::processMessage()` line 46-48
2. Returns `fallbackResponse()` → "No active flow configured"
3. API returns error

**Fix:** Ensure active conversation flow exists in `tenant_conversation_flows` table for tenant

### ROOT CAUSE #2: Welcome Node at Flow Start
**Condition:** Flow starts with Welcome node

**Flow:**
1. Conversation created with `current_node_id` = start_node_id
2. Start node is Welcome node
3. Welcome node executes, returns welcome message
4. Next node determined by config
5. If no next node → flow ends
6. No AI Response node executed → `$aiResponse` stays NULL
7. Fallback message used

**Why This Happens:**
- Welcome node is designed to run first
- But it doesn't generate AI response, just shows greeting
- If flow has no subsequent nodes → no AI happens

**Fix:** Ensure flow routes from Welcome node to AI Response node

### ROOT CAUSE #3: AI Response Node Missing or Invalid
**Condition:** AI Response node not in flow OR has invalid config

**Flow:**
1. Flow executes multiple nodes
2. No node with type `'ai_response'` is executed
3. Line 123 in ConversationFlowEngine never triggers
4. `$aiResponse` stays NULL
5. Fallback message used

**Fix:** Add AI Response node to flow with valid prompt

### ROOT CAUSE #4: Configuration Cache Stale
**Condition:** Flow cache not cleared after flow update

**Flow:**
1. Flow updated in database
2. But `Cache::remember("conversation_flow_{$tenantId}_{$flowId}", 3600, ...)` still holds old data
3. Old flow (with wrong nodes) used
4. Nodes fail or missing AI response
5. Fallback message used

**Fix:** Clear flow cache: `ConversationFlowEngine::clearFlowCache($tenantId)`

### ROOT CAUSE #5: Node Handler Class Not Found
**Condition:** Node registry initialization fails

**Flow:**
1. `NodeExecutor::initializeRegistry()` loads from `ai_workflow_nodes`
2. Maps node type to handler class
3. If class doesn't exist or mapping wrong → exception thrown
4. Node execution fails
5. ConversationFlowEngine handles error
6. No AI response generated
7. Fallback message used

**Fix:** Verify `ai_workflow_nodes` table has correct class mappings

### ROOT CAUSE #6: Flow Database Query Fails
**Condition:** Conversation flow not found or corrupted

**Flow:**
1. `AIConversation::getOrCreateForSession()` line 84
2. `TenantConversationFlow::getActiveFlowForTenant($tenantId)` returns NULL
3. Throws exception: "No active flow found for tenant X"
4. `ConversationFlowEngine::processMessage()` catches exception
5. Returns fallback: "An error occurred..."
6. API returns error response

**Fix:** Check `tenant_conversation_flows` table for active flows

---

## 💾 DATA MODELS & DATABASE SCHEMA

### AIConversation (Central DB)
```
┌────────────────────────────────────┐
│ ai_conversations                   │
├────────────────────────────────────┤
│ id (PK)                            │
│ tenant_id                          │  → Links to tenant
│ flow_id                            │  → Current flow
│ current_node_id                    │  → Flow execution position
│ session_id                         │  → Guest session identifier
│ user_id (nullable)                 │  → Authenticated user
│ context_data (JSON)                │  → State & context
│ state_history (JSON)               │  → Node traversal log
│ created_at, updated_at             │
└────────────────────────────────────┘
```

**Usage in Flow:**
- `getOrCreateForSession()` → Get or create with default flow
- `moveToNode()` → Update `current_node_id` after node execution
- `addToContext()` → Store AI response in `context_data`
- `mergeContext()` → Bulk update context

### AIConversationMessage (Central DB)
```
┌────────────────────────────────────┐
│ ai_conversation_messages           │
├────────────────────────────────────┤
│ id (PK)                            │
│ conversation_id (FK)               │  → AIConversation.id
│ role ('user' or 'assistant')       │
│ content                            │  → Message text
│ metadata (JSON, nullable)          │
│ created_at, updated_at             │
└────────────────────────────────────┘
```

**Usage in Flow:**
- Not directly used in ConversationFlowEngine
- Used in message history loading
- `getMessageHistory()` at line 279-298

### TenantConversationFlow (Central DB)
```
┌────────────────────────────────────┐
│ tenant_conversation_flows          │
├────────────────────────────────────┤
│ id (PK)                            │
│ tenant_id                          │  → Which tenant
│ flow_name                          │  → Display name
│ flow_data (JSON)                   │  → Full flow definition
│ start_node_id                      │  → Entry point node
│ is_active                          │  → Currently active
│ created_at, updated_at             │
└────────────────────────────────────┘
```

**flow_data Structure:**
```json
{
  "nodes": [
    {
      "id": "node_1",
      "type": "welcome",
      "name": "Welcome",
      "config": {
        "welcome_message": "Hello!",
        "show_suggestions": false,
        "next_node": "node_2"
      }
    },
    {
      "id": "node_2",
      "type": "ai_response",
      "name": "AI Response",
      "config": {
        "prompt": "You are a helpful assistant...",
        "next_node": "node_3"
      }
    },
    {
      "id": "node_3",
      "type": "end",
      "name": "End",
      "config": {}
    }
  ]
}
```

### AIWorkflowNode (Central DB - Node Registry)
```
┌────────────────────────────────────┐
│ ai_workflow_nodes                  │
├────────────────────────────────────┤
│ id (PK)                            │
│ node_key (e.g., 'welcome')         │
│ node_class                         │  → Handler class
│ category (e.g., 'flow')            │
│ is_active                          │
│ is_global                          │
│ order                              │
├────────────────────────────────────┤
│ Example rows:                      │
│ key: 'welcome'                     │
│ class: 'App\Services\...\Welcome   │
│ key: 'ai_response'                 │
│ class: 'App\Services\...\AIResponse│
│ key: 'product_search'              │
│ class: 'App\Services\...\ProductSearch
└────────────────────────────────────┘
```

---

## 🔌 NODE TYPES & HANDLERS

### Common Nodes (All Tenants)
| Node Type | Handler Class | Purpose |
|-----------|---------------|---------|
| `welcome` | `WelcomeNode` | Initial greeting |
| `ai_response` | `AIResponseNode` | Generate AI response |
| `context_builder` | `ContextBuilderNode` | Prepare context data |
| `history_loader` | `HistoryLoaderNode` | Load conversation history |
| `message_saver` | `MessageSaverNode` | Save messages to DB |
| `condition` | `ConditionNode` | Branch flow logic |
| `end` | `EndNode` | Terminate flow |

### Shop-Specific Nodes (ixtif.com)
| Node Type | Handler Class | Purpose |
|-----------|---------------|---------|
| `category_detection` | `CategoryDetectionNode` | Detect product category |
| `product_search` | `ProductSearchNode` | Search products |
| `price_query` | `PriceQueryNode` | Handle price queries |
| `product_comparison` | `ProductComparisonNode` | Compare products |
| `stock_sorter` | `StockSorterNode` | Sort by availability |
| `contact_request` | `ContactRequestNode` | Handle contact form |

### Node Result Structure
Every node returns:
```php
[
    'success' => true|false,        // Execution succeeded?
    'prompt' => '...' or null,      // System prompt for AI (if needed)
    'data' => [                     // Context data to merge
        'category' => '...',
        'products' => [...],
        ...
    ],
    'next_node' => 'node_id',       // Where to go next
    'error' => 'Error message...'   // If success=false
]
```

---

## 🎯 CACHING STRATEGY

### Layer 1: Flow Cache (1 hour)
**Location:** `ConversationFlowEngine::getFlow()` line 209-213
```php
Cache::remember(
    "conversation_flow_{$conversation->tenant_id}_{$conversation->flow_id}",
    3600,  // 1 hour
    fn() => TenantConversationFlow::find($conversation->flow_id)
);
```
**Issue:** Old flow definition cached if not cleared after update

### Layer 2: Session Cache (Temporary)
**Location:** `ChatServiceV2` (not used in workflow engine)
```php
// In ChatServiceV2 - different system
Cache::put("chat_session:{$sessionId}", $session, self::SESSION_TTL);
```

### Layer 3: Response Cache (Optional)
**From .env:**
```
RESPONSE_CACHE_ENABLED=true
RESPONSE_CACHE_DRIVER=redis
RESPONSE_CACHE_LIFETIME=3600
```
**Issue:** API responses cached globally - old responses served

### Layer 4: Configuration Cache (PHP)
**Command:** `php artisan config:cache`
**Effect:** Config values frozen in `bootstrap/cache/config.php`
**Issue:** If config cached, `.env` changes ignored until `config:clear`

---

## 🚨 COMMON ISSUES & SYMPTOMS

| Symptom | Root Cause | Solution |
|---------|-----------|----------|
| Always "Merhaba! Size..." | No AI Response node in flow | Add AI Response node to flow |
| Same welcome message every time | Conversation not loading history | Check `AIConversationMessage` table |
| Config changes not applied | Config cached | Run `php artisan config:clear` |
| Old flow used | Flow cache not cleared | Run `ConversationFlowEngine::clearFlowCache($tenantId)` |
| "Unknown node type" error | Node handler class not mapped | Check `ai_workflow_nodes` registry |
| Sudden change in behavior | OPcache stale bytecode | Clear OPcache via opcache-reset.php |
| Response cache stale | Response cache not cleared | Clear response cache manually |

---

## 🔧 CRITICAL CONFIGURATION

**File:** `config/ai.php` (line 34)
```php
'use_workflow_engine' => env('AI_USE_WORKFLOW_ENGINE', true),
```

**Current Status:**
- `.env`: `AI_USE_WORKFLOW_ENGINE=true`
- Default: `true`
- Effect: ALWAYS uses new V2 workflow engine

**To Use Old System:**
```
AI_USE_WORKFLOW_ENGINE=false
```
Then clear config: `php artisan config:clear`

---

## 📈 EXECUTION FLOW DIAGRAM

```
USER MESSAGE
    ↓
PublicAIController::shopAssistantChat()
    ↓
CHECK: config('ai.use_workflow_engine')
    ↓
shopAssistantChatV2()
    ↓
ConversationFlowEngine::processMessage()
    ├─→ [1] Get conversation (or create)
    ├─→ [2] Load flow from cache/DB
    ├─→ [3] Loop: Execute nodes (max 20)
    │   ├─→ [3.1] Get current node
    │   ├─→ [3.2] NodeExecutor::execute()
    │   │   ├─→ [3.2.1] Initialize registry
    │   │   ├─→ [3.2.2] Resolve handler class
    │   │   ├─→ [3.2.3] Validate config
    │   │   └─→ [3.2.4] Call handler->execute()
    │   ├─→ [3.3] Update conversation state
    │   ├─→ [3.4] If AI Response node → generateAIResponse()
    │   └─→ [3.5] Move to next node
    ├─→ [4] Return result with $aiResponse
    ↓
shopAssistantChatV2() continued
    ├─→ if result['success'] && result['response']:
    │   └─→ Return API response with AI message
    └─→ else:
        └─→ Return error response
```

---

## 🔍 DEBUG LOGGING POINTS

All critical points have detailed logging:

**ConversationFlowEngine.php:**
- Line 71: Node execution started
- Line 82: Node executed result
- Line 117: AI response condition check
- Line 124: AI Response generation
- Line 139: AI response generated
- Line 157: Max iterations exceeded
- Line 169: Message processed successfully

**NodeExecutor.php:**
- Line 60: Registry initialization
- Line 137: Force reinitialize registry
- Line 171: Node executed successfully
- Line 191: Node execution failed

**WelcomeNode.php:**
- Line 37: Welcome node executed

---

## 💡 KEY INSIGHTS

1. **Multi-node execution model** - Not single-shot AI, but orchestrated workflow
2. **State persistence** - Conversation state maintained in `ai_conversations` table
3. **Pluggable nodes** - Easy to add new node types (just extend `AbstractNode`)
4. **Tenant-aware** - Each tenant can have different flows
5. **Caching layers** - Multiple cache points can cause stale data issues
6. **Fallback behavior** - Multiple fallback messages at different layers

---

## ✅ VERIFICATION CHECKLIST

Before assuming "system broken":

- [ ] Is `AI_USE_WORKFLOW_ENGINE=true` in .env?
- [ ] Does `tenant_conversation_flows` have active row for tenant?
- [ ] Does flow have AI Response node configured?
- [ ] Is flow config cache cleared after updates?
- [ ] Are node handler classes in `ai_workflow_nodes` registry?
- [ ] Is response cache cleared if using Redis?
- [ ] Is OPcache cleared via opcache-reset.php?
- [ ] Does `ai_conversation_messages` have message history?
- [ ] Are AI provider API keys valid (OPENAI_API_KEY, etc)?

