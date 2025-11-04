# AI CONVERSATION WORKFLOW ENGINE - IMPLEMENTATION PROMPT

## SYSTEM OVERVIEW

Build a visual conversation workflow system for multi-tenant Laravel application. This system allows tenant admins to design conversation flows using drag-and-drop interface, with nodes executing PHP functions that control AI behavior.

---

## CORE REQUIREMENTS

### 1. DATABASE SCHEMA

Create 3 main tables:

**tenant_conversation_flows:**
- Stores visual workflow configurations per tenant
- JSON structure with nodes and edges
- Track active/inactive flows
- Support multiple flows with priority

**ai_tenant_directives:**
- Central configuration table for tenant-specific AI behavior
- Key-value pairs (e.g., "greeting_style" => "formal")
- Categorized (general, behavior, pricing, contact)
- Easy to query and validate

**ai_conversations:**
- Track conversation state (which node user is currently on)
- Store conversation history
- Persist user context data
- Session-based tracking

### 2. NODE SYSTEM ARCHITECTURE

**Base Abstract Class (AbstractNode):**
- All nodes extend this base class
- Required methods:
  - execute(): Process node logic
  - validate(): Validate configuration
  - getType(): Return node type identifier
  - getName(): Display name for admin UI
  - getConfigSchema(): Define configuration fields for admin UI
  - getInputs()/getOutputs(): Connection points

**6 Core Node Types:**

1. **AIResponseNode** - Send system prompt to AI
   - Config: prompt text, temperature, max_tokens
   - Output: AI response

2. **ShowProductsNode** - Fetch and filter products
   - Config: filters (homepage, high_stock, featured), sort_by, limit
   - Output: product list + AI context

3. **ShowPriceNode** - Calculate and display pricing
   - Config: currency, include_tax, format
   - Output: formatted price

4. **GetPhoneNode** - Extract phone number from user message
   - Config: validation regex, success/retry nodes
   - Output: phone number or retry

5. **ConditionNode** - If/else branching
   - Config: condition (contains keyword, regex, etc.), true/false branches
   - Output: route to different node based on condition

6. **CustomActionNode** - Execute custom PHP code
   - Config: PHP class/method to call
   - Output: custom result

### 3. EXECUTION ENGINE

**NodeExecutor:**
- Registry of all node types
- Execute nodes with validation
- Error handling and logging
- Return standardized result format

**ConversationFlowEngine:**
- Main orchestrator
- Load tenant's active flow
- Track current node position
- Execute node sequence
- Build AI context from multiple nodes
- Update conversation state
- Cache tenant flows and directives

### 4. ADMIN INTERFACE

**Requirements:**
- List all flows (create/edit/delete)
- Visual flow designer using Drawflow library
- Drag-and-drop node placement
- Connect nodes with edges
- Configure each node (modal/sidebar)
- Export to JSON and save to database
- Tenant directives configuration page

**Drawflow Integration:**
- JavaScript library for visual editor
- Left sidebar: Node library (draggable)
- Canvas: Flow designer
- Export JSON structure
- Import existing flows

### 5. RUNTIME EXECUTION FLOW

```
1. User sends message
2. Controller receives message
3. Load tenant's active flow from database/cache
4. Get conversation's current node
5. Execute node via NodeExecutor
6. Node returns: prompt + data + next_node
7. Build AI context (merge node data + directives + history)
8. Call AI service
9. Save AI response
10. Update conversation state (move to next node)
11. Return response to user
```

### 6. DATA STRUCTURES

**Flow JSON Format:**
```json
{
  "nodes": [
    {
      "id": "unique_uuid",
      "type": "show_products",
      "name": "Product Recommendation",
      "class": "App\\Services\\ConversationNodes\\ShowProductsNode",
      "config": {
        "filters": ["homepage", "high_stock"],
        "limit": 5
      },
      "position": {"x": 100, "y": 100},
      "inputs": ["input_1"],
      "outputs": ["output_1", "output_2"]
    }
  ],
  "edges": [
    {
      "id": "edge_uuid",
      "source": "node_uuid_1",
      "target": "node_uuid_2",
      "condition": null
    }
  ]
}
```

**Node Result Format:**
```php
[
  'success' => true/false,
  'prompt' => 'System prompt for AI',
  'data' => ['products' => [...], ...],
  'next_node' => 'node_uuid',
  'error' => null
]
```

---

## IMPLEMENTATION DELIVERABLES

Generate complete production-ready code:

1. **Migrations:**
   - tenant_conversation_flows
   - ai_tenant_directives
   - ai_conversations

2. **Models:**
   - TenantConversationFlow (with flow_data cast to array)
   - AITenantDirective
   - AIConversation

3. **Node System:**
   - AbstractNode base class
   - 6 node implementations (AIResponseNode, ShowProductsNode, etc.)
   - NodeExecutor service

4. **Flow Engine:**
   - ConversationFlowEngine service
   - State management
   - Context building
   - Caching strategy

5. **Admin Panel:**
   - Livewire FlowManager component
   - Blade views with Drawflow integration
   - Node configuration UI
   - Directives management page

6. **Controller Integration:**
   - Update AIChatController to use flow engine
   - API endpoints for flow management

7. **Routes:**
   - admin.ai-flows.index (list flows)
   - admin.ai-flows.create
   - admin.ai-flows.edit
   - admin.ai-flows.store
   - admin.ai-directives.index

---

## TECHNICAL SPECIFICATIONS

**Laravel Version:** 10+
**PHP Version:** 8.2+ (use readonly properties, match expressions)
**Multi-tenancy:** Stancl Tenancy package
**Frontend:** Livewire 3 + Drawflow JS
**Caching:** Redis (cache flows and directives)

**Code Standards:**
- Type hints everywhere
- PHPDoc comments
- Repository pattern for complex queries
- Service layer for business logic
- Comprehensive error handling
- Logging (node executions, errors)
- Validation (node configs, user input)

**Security:**
- Tenant isolation (all queries filter by tenant_id)
- Input validation
- XSS prevention
- CSRF protection

**Performance:**
- Cache tenant flows (1 hour TTL)
- Cache directives (1 hour TTL)
- Lazy load nodes
- Queue long-running operations

---

## SPECIAL REQUIREMENTS

1. **Extensibility:** Easy to add new node types (just create new class extending AbstractNode)
2. **Multi-tenant:** Complete isolation between tenants
3. **State Persistence:** Conversation state survives server restarts
4. **Graceful Degradation:** If node fails, fallback to simple AI response
5. **Admin UX:** Non-technical admins should be able to create flows
6. **Debuggability:** Log all node executions with timing and results

---

## OUTPUT FORMAT

Provide complete, production-ready code with:
- Full file paths
- Complete class implementations
- Migration files
- Blade templates
- JavaScript integration
- Error handling
- Logging
- Comments explaining complex logic

Do not provide partial code or pseudocode. Generate deployable Laravel code.
