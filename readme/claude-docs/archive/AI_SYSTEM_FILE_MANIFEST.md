# AI Chat System - Complete File Manifest

**Generated:** 2025-11-05

---

## 📁 Controller Layer

### API Controllers
| File | Class | Endpoints |
|------|-------|-----------|
| `Modules/AI/app/Http/Controllers/Api/PublicAIController.php` | `PublicAIController` | POST `/ai/v1/shop-assistant/chat` |
| | | POST `/ai/v1/chat` (public) |
| | | POST `/ai/v1/feature/{slug}` |
| | | GET `/ai/v1/shop-assistant/history` |
| | | POST `/ai/v1/shop-assistant/chat-stream` |

**Key Methods:**
- `shopAssistantChat()` - Entry point (line 551)
- `shopAssistantChatV2()` - New V2 system (line 2532)
- `publicChat()` - Public API chat
- `getConversationHistory()` - Load message history

---

## 🔧 Service Layer

### Core Orchestration Services
| Service | File | Purpose |
|---------|------|---------|
| `ConversationFlowEngine` | `app/Services/ConversationFlowEngine.php` | Main flow orchestrator |
| `NodeExecutor` | `app/Services/ConversationNodes/NodeExecutor.php` | Node execution engine |
| `CentralAIService` | `app/Services/AI/CentralAIService.php` | AI provider management |
| `ModuleContextOrchestrator` | `app/Services/AI/Context/ModuleContextOrchestrator.php` | Context building |

### Chat Services
| Service | File | Purpose |
|---------|------|---------|
| `ChatServiceV2` | `Modules/AI/app/Services/Chat/ChatServiceV2.php` | Real-time chat (not used in flow) |
| `WidgetRenderer` | `Modules/AI/app/Services/Chat/WidgetRenderer.php` | Widget HTML generation |

### AI Services
| Service | File | Purpose |
|---------|------|---------|
| `ProductSearchService` | `app/Services/AI/ProductSearchService.php` | Search products for chat |
| `AIResponseValidator` | `app/Services/AI/AIResponseValidator.php` | Validate AI responses |
| `MarkdownPostProcessor` | `app/Services/AI/MarkdownPostProcessor.php` | Format AI responses |

---

## 🔌 Node Handlers

### Common Nodes
Located: `app/Services/ConversationNodes/Common/`

| Node Type | File | Class |
|-----------|------|-------|
| `welcome` | `WelcomeNode.php` | `WelcomeNode` |
| `ai_response` | `AIResponseNode.php` | `AIResponseNode` |
| `context_builder` | `ContextBuilderNode.php` | `ContextBuilderNode` |
| `history_loader` | `HistoryLoaderNode.php` | `HistoryLoaderNode` |
| `message_saver` | `MessageSaverNode.php` | `MessageSaverNode` |
| `end` | `EndNode.php` | `EndNode` |
| `condition` | `ConditionNode.php` | `ConditionNode` |
| `sentiment` | `SentimentDetectionNode.php` | `SentimentDetectionNode` |
| `link_generator` | `LinkGeneratorNode.php` | `LinkGeneratorNode` |
| `webhook` | `WebhookNode.php` | `WebhookNode` |
| `share_contact` | `ShareContactNode.php` | `ShareContactNode` |
| `collect_data` | `CollectDataNode.php` | `CollectDataNode` |

### Shop-Specific Nodes
Located: `app/Services/ConversationNodes/Shop/`

| Node Type | File | Class |
|-----------|------|-------|
| `category_detection` | `CategoryDetectionNode.php` | `CategoryDetectionNode` |
| `product_search` | `ProductSearchNode.php` | `ProductSearchNode` |
| `price_query` | `PriceQueryNode.php` | `PriceQueryNode` |
| `product_comparison` | `ProductComparisonNode.php` | `ProductComparisonNode` |
| `stock_sorter` | `StockSorterNode.php` | `StockSorterNode` |
| `contact_request` | `ContactRequestNode.php` | `ContactRequestNode` |
| `currency_converter` | `CurrencyConverterNode.php` | `CurrencyConverterNode` |

### Base Classes
| File | Class | Purpose |
|------|-------|---------|
| `AbstractNode.php` | `AbstractNode` | Base for all nodes |
| `NodeExecutor.php` | `NodeExecutor` | Executes nodes |

---

## 💾 Models & Database

### Core Models (Central DB)
| Model | Table | File |
|-------|-------|------|
| `AIConversation` | `ai_conversations` | `app/Models/AIConversation.php` |
| `AIConversationMessage` | `ai_conversation_messages` | `app/Models/AIConversationMessage.php` |
| `TenantConversationFlow` | `tenant_conversation_flows` | `app/Models/TenantConversationFlow.php` |
| `AIWorkflowNode` | `ai_workflow_nodes` | `app/Models/AIWorkflowNode.php` |
| `AITenantDirective` | `ai_tenant_directives` | `app/Models/AITenantDirective.php` |

### Modules/AI Models
| Model | Table | File |
|-------|-------|------|
| `AIConversation` | `ai_conversations` | `Modules/AI/app/Models/AIConversation.php` |
| `AICreditUsage` | `ai_credit_usage` | `Modules/AI/app/Models/AICreditUsage.php` |
| `AIFeature` | `ai_features` | `Modules/AI/app/Models/AIFeature.php` |

### Database Migrations
Located: `database/migrations/` and `database/migrations/tenant/`

| Migration | Purpose |
|-----------|---------|
| `*create_ai_conversations_table` | Conversation state tracking |
| `*create_ai_conversation_messages_table` | Message history |
| `*create_ai_workflow_nodes_table` | Node registry |
| `*create_tenant_conversation_flows_table` | Flow definitions |
| `*create_ai_tenant_directives_table` | Tenant AI rules |

---

## 🔐 Configuration Files

### Main Config
| File | Purpose |
|------|---------|
| `config/ai.php` | AI system configuration |
| `.env` | Environment variables |

### Module Config
| File | Purpose |
|------|---------|
| `Modules/AI/config/config.php` | AI module settings |
| `Modules/AI/config/universal-input-system.php` | Input validation |

---

## 🛣️ Routes

### API Routes
File: `Modules/AI/routes/api.php`

```
POST /api/ai/v1/shop-assistant/chat           → shopAssistantChat()
POST /api/ai/v1/shop-assistant/chat-stream    → shopAssistantChatStream()
GET  /api/ai/v1/shop-assistant/history        → getConversationHistory()
DELETE /api/ai/v1/conversation/{id}           → deleteConversation()
```

---

## 📊 Data Flow Files

### Context Building
| File | Purpose |
|------|---------|
| `app/Services/AI/Context/PageContextBuilder.php` | Build page context |
| `app/Services/AI/Context/ShopContextBuilder.php` | Build shop context |
| `app/Services/AI/Context/ModuleContextOrchestrator.php` | Orchestrate all context |
| `app/Services/AI/Context/ContextAwareEngine.php` | Apply context to prompts |

### Search Services
| File | Purpose |
|------|---------|
| `app/Services/AI/ProductSearchService.php` | Product search in chat |
| `app/Services/AI/HybridSearchService.php` | Hybrid search (Vector + Full-text) |
| `app/Services/AI/VectorSearchService.php` | Vector embeddings |
| `app/Services/AI/EmbeddingService.php` | Generate embeddings |

---

## 🎯 Views & Templates

### Chat Widget Views
| File | Purpose |
|------|---------|
| `Modules/AI/resources/views/widgets/chat-widget.blade.php` | Chat widget HTML |
| `resources/views/components/ai/floating-widget.blade.php` | Floating widget component |

### Admin Views
| File | Purpose |
|------|---------|
| `Modules/AI/resources/views/admin/chat/chat-panel.blade.php` | Admin chat panel |
| `Modules/AI/resources/views/admin/universal/context-dashboard.blade.php` | Context dashboard |

---

## 🔍 Key Execution Paths

### REQUEST → RESPONSE Flow
```
User Message
  ↓
[Route: POST /api/ai/v1/shop-assistant/chat]
  ↓
[PublicAIController::shopAssistantChat()]
  ├─ Validate request
  ├─ Check config('ai.use_workflow_engine')
  └─ Route to shopAssistantChatV2() if enabled
       ↓
[shopAssistantChatV2()]
  ├─ Validate input
  ├─ Generate session_id
  └─ Call ConversationFlowEngine::processMessage()
       ↓
[ConversationFlowEngine::processMessage()]
  ├─ Get or create AIConversation
  ├─ Load TenantConversationFlow from cache
  ├─ Loop: Execute nodes (max 20 iterations)
  │   ├─ Get current node from flow_data
  │   ├─ Call NodeExecutor::execute()
  │   │   ├─ Initialize node registry
  │   │   ├─ Resolve handler class
  │   │   ├─ Validate config
  │   │   └─ Call handler->execute()
  │   ├─ Update AIConversation state
  │   ├─ If AI Response node: Call generateAIResponse()
  │   └─ Move to next node
  └─ Return result with $aiResponse
       ↓
[API Response]
  ├─ If $aiResponse exists: Return message
  └─ Else: Return fallback "Merhaba! Size..."
```

---

## 📚 Support Resources

### Documentation
- Full Analysis: `AI_CHAT_ARCHITECTURE_ANALYSIS_2025-11-05.md`
- Quick Reference: `AI_CHAT_QUICK_REFERENCE.md`
- This File: `AI_SYSTEM_FILE_MANIFEST.md`

### Code Comments
Look for comments in:
- `ConversationFlowEngine.php` - Flow logic
- `NodeExecutor.php` - Registry system
- `AbstractNode.php` - Node interface
- Individual node files - Implementation details

### Logs
Check `storage/logs/laravel.log` for:
- `shopAssistantChat` entries
- `ConversationFlow` entries
- `Node execution` entries
- `AI response` entries

---

## 🔧 Important Constants

### ConversationFlowEngine
```php
MAX_ITERATIONS = 20  // Max nodes to execute
```

### NodeExecutor
```php
// Node registry is initialized per execute() call
// Ensures fresh tenant context
```

### ChatServiceV2 (Not used in workflow)
```php
SESSION_TTL = 3600  // 1 hour
MAX_MESSAGES_PER_SESSION = 100
MAX_CONCURRENT_SESSIONS = 10
```

---

## 🚨 Critical Files to Monitor

For debugging, always check these files first:
1. `Modules/AI/app/Http/Controllers/Api/PublicAIController.php` - Entry point
2. `app/Services/ConversationFlowEngine.php` - Main logic
3. `app/Services/ConversationNodes/NodeExecutor.php` - Node execution
4. Database: `ai_conversations`, `tenant_conversation_flows`, `ai_workflow_nodes`
5. Config: `config/ai.php`, `.env`

---

## 📞 Finding Code Examples

### Node Implementation Example
```
File: app/Services/ConversationNodes/Common/WelcomeNode.php
Shows: How to extend AbstractNode and implement execute()
```

### Context Building Example
```
File: app/Services/ConversationNodes/Common/ContextBuilderNode.php
Shows: How to prepare context for AI
```

### Product Integration Example
```
File: app/Services/ConversationNodes/Shop/ProductSearchNode.php
Shows: How to integrate with shop module
```

