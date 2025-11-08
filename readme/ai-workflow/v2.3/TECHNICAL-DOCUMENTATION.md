# AI Shop Chatbot - Technical Documentation v2.3

## 📋 Table of Contents
1. [System Overview](#system-overview)
2. [Architecture](#architecture)
3. [Frontend Layer](#frontend-layer)
4. [Backend Layer](#backend-layer)
5. [Workflow Engine](#workflow-engine)
6. [Data Flow](#data-flow)
7. [Database Schema](#database-schema)
8. [API Specification](#api-specification)
9. [Configuration](#configuration)
10. [Deployment](#deployment)

---

## 1. System Overview

### Purpose
AI-powered e-commerce chatbot system that assists customers with product inquiries, recommendations, and general shopping support.

### Key Features
- **Floating Widget**: Persistent chat interface on all pages
- **Context-Aware Responses**: Product/category-specific intelligence
- **Conversation History**: Persistent sessions across page navigation
- **Multi-Tenant Support**: Isolated data per tenant
- **Workflow Engine**: Modular, configurable AI processing pipeline
- **Real-time Interaction**: Non-blocking async responses
- **Rate Limiting**: Protection against abuse
- **Markdown Rendering**: Rich text formatting in responses

### Tech Stack
- **Frontend**: Alpine.js, Tailwind CSS, JavaScript ES6+
- **Backend**: Laravel 10, PHP 8.2+
- **AI Integration**: OpenAI API (GPT-4)
- **Database**: MySQL 8.0+ (Multi-tenant with Stancl)
- **Cache**: Redis (session & conversation caching)
- **Queue**: Laravel Queue (background jobs)

---

## 2. Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────┐
│                     FRONTEND LAYER                       │
├─────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │ Floating     │  │ Alpine.js    │  │ Markdown     │  │
│  │ Widget UI    │◄─┤ Store        │◄─┤ Renderer     │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
│         │                  │                  │          │
│         └──────────────────┴──────────────────┘          │
│                           │                               │
│                           ▼                               │
│                  ┌──────────────────┐                    │
│                  │  WebSocket/AJAX  │                    │
│                  └──────────────────┘                    │
└─────────────────────────────────────────────────────────┘
                            │
                            │ HTTPS/JSON
                            ▼
┌─────────────────────────────────────────────────────────┐
│                     API GATEWAY                          │
├─────────────────────────────────────────────────────────┤
│  Route: /api/ai/v1/shop-assistant/chat                  │
│  Controller: PublicAIController@shopAssistantChatV2     │
│  Middleware: RateLimit, CORS, CSRF                      │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│                    BACKEND LAYER                         │
├─────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │ Validation   │  │ Session Mgmt │  │ Conversation │  │
│  │ Layer        │─▶│ (Redis)      │─▶│ Manager      │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
│         │                                      │          │
│         └──────────────────┬───────────────────┘          │
│                            ▼                               │
│                  ┌──────────────────┐                    │
│                  │  Flow Executor   │                    │
│                  └──────────────────┘                    │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│                   WORKFLOW ENGINE                        │
├─────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │ Context      │  │ Category     │  │ Product      │  │
│  │ Builder      │─▶│ Detection    │─▶│ Search       │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
│         │                                      │          │
│         └──────────────────┬───────────────────┘          │
│                            ▼                               │
│                  ┌──────────────────┐                    │
│                  │  AI Response     │                    │
│                  │  Generator       │                    │
│                  └──────────────────┘                    │
│                            │                               │
│                            ▼                               │
│                  ┌──────────────────┐                    │
│                  │  OpenAI API      │                    │
│                  └──────────────────┘                    │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│                   DATA LAYER                             │
├─────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │ MySQL        │  │ Redis Cache  │  │ File Storage │  │
│  │ (Tenant DB)  │  │ (Sessions)   │  │ (Logs)       │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
```

### Design Patterns
- **MVC Architecture**: Controllers → Services → Models
- **Repository Pattern**: Data access abstraction
- **Factory Pattern**: Node creation in workflow engine
- **Observer Pattern**: Event listeners for conversation lifecycle
- **Strategy Pattern**: Different AI response strategies per node type

---

## 3. Frontend Layer

### 3.1 File Structure

```
public/assets/
├── js/
│   └── ai-chat.js              # Alpine.js store & utilities
└── css/
    └── ai-chat.css             # Widget styling

resources/views/components/ai/
└── floating-widget.blade.php   # Main widget component
```

### 3.2 Alpine.js Store

**Location**: `/public/assets/js/ai-chat.js`

**Purpose**: Global state management for chat widget

**Key Properties**:
```javascript
{
  sessionId: null,              // Session identifier (localStorage)
  conversationId: null,         // Database conversation ID
  messages: [],                 // Chat message history
  isLoading: false,             // API request in progress
  isTyping: false,              // AI typing indicator
  error: null,                  // Error messages
  floatingOpen: false,          // Widget open/closed state
  context: {                    // Page context
    product_id: null,
    category_id: null,
    page_slug: null
  }
}
```

**Key Methods**:

#### `init()`
- Loads session ID from localStorage
- Restores previous conversation history
- Sets up event listeners for context changes

#### `sendMessage(messageText, contextOverride)`
- Validates input
- Adds user message to UI immediately
- Sends POST request to `/api/ai/v1/shop-assistant/chat`
- Handles response and updates message list
- Error handling with user-friendly messages

```javascript
async sendMessage(messageText, contextOverride = {}) {
    // Add user message immediately (optimistic UI)
    this.addMessage({
        role: 'user',
        content: messageText.trim(),
        created_at: new Date().toISOString(),
    });

    // Set loading state
    this.isLoading = true;
    this.isTyping = true;

    try {
        const response = await fetch('/api/ai/v1/shop-assistant/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                message: messageText.trim(),
                session_id: this.sessionId,
                product_id: this.context.product_id,
                category_id: this.context.category_id,
                page_slug: this.context.page_slug
            })
        });

        const data = await response.json();

        if (data.success) {
            // Add AI response
            this.addMessage({
                role: 'assistant',
                content: data.data.message,
                created_at: new Date().toISOString()
            });

            // Update session
            this.sessionId = data.data.session_id;
            localStorage.setItem('ai_chat_session_id', this.sessionId);
        }
    } catch (error) {
        // Error handling
        this.addMessage({
            role: 'system',
            content: '⚠️ Hata: ' + error.message,
            isError: true
        });
    } finally {
        this.isLoading = false;
        this.isTyping = false;
    }
}
```

#### `loadHistory()`
- Fetches conversation history from backend
- Restores message list on page reload

#### `scrollToBottom()`
- Auto-scrolls chat container to latest message

### 3.3 Floating Widget Component

**Location**: `/resources/views/components/ai/floating-widget.blade.php`

**Features**:
- **Auto-open Behavior**: Opens after 10 seconds on desktop (not mobile)
- **Persistent State**: Saves open/closed state in localStorage
- **Unread Indicator**: Shows badge when new AI messages arrive
- **Animated Bubble**: Rotating promotional messages
- **Responsive Design**: Adapts to mobile/tablet/desktop
- **Dark Mode Support**: Full dark mode compatibility
- **Disclaimer Modal**: AI usage disclaimer popup

**Key UI States**:

1. **Closed State**: Floating button with animated bubble messages
2. **Welcome State**: First-time user greeting with suggestions
3. **Conversation State**: Message history with typing indicators
4. **Error State**: User-friendly error messages

### 3.4 Markdown Rendering

**Function**: `window.aiChatRenderMarkdown(content)`

**Backend Processing**: Since v2.3, markdown is rendered on backend using `league/commonmark` library

**Security**: XSS protection with HTML sanitization

**Custom Link Format**:
- `[LINK:shop:product-slug]` → Product page link
- `[LINK:shop:category:category-slug]` → Category page link

**Example**:
```markdown
Transpalet modelleri için [LINK:shop:category:transpalet] sayfamızı ziyaret edebilirsiniz.
→ Renders as clickable link with Tailwind styles
```

---

## 4. Backend Layer

### 4.1 API Controller

**Location**: `/Modules/AI/app/Http/Controllers/Api/PublicAIController.php`

**Method**: `shopAssistantChatV2(Request $request): JsonResponse`

**Responsibilities**:
1. Input validation
2. Session management
3. Rate limiting
4. Workflow execution
5. Conversation persistence
6. Response formatting

**Request Validation**:
```php
$validated = $request->validate([
    'message' => 'required|string|min:1|max:1000',
    'product_id' => 'nullable|integer',
    'category_id' => 'nullable|integer',
    'page_slug' => 'nullable|string|max:255',
    'session_id' => 'nullable|string|max:64',
]);
```

**Session Management**:
```php
// Generate or reuse session ID
$sessionId = $validated['session_id'] ?? $this->generateSessionId($request);

// Load existing conversation
$conversation = AIConversation::where('session_id', $sessionId)
    ->where('tenant_id', tenant('id'))
    ->first();
```

**Workflow Execution**:
```php
// Get active flow configuration
$flow = Flow::getActiveFlow();

$flowExecutor = app(FlowExecutor::class);

// Execute workflow with conversation history
$result = $flowExecutor->execute($flow->flow_data, [
    'user_message' => $validated['message'],
    'session_id' => $sessionId,
    'tenant_id' => tenant('id'),
    'conversation_history' => $conversationHistory
]);
```

**Response Format**:
```json
{
  "success": true,
  "data": {
    "message": "AI yanıtı burada (HTML formatted)",
    "session_id": "abc123xyz",
    "conversation_id": null,
    "metadata": {
      "system": "workflow_engine_v2",
      "flow_name": "E-commerce Chat Flow",
      "nodes_executed": ["context_builder", "ai_response", "end"]
    }
  }
}
```

### 4.2 Rate Limiting

**Implementation**: Laravel RateLimiter

**Limits**:
- Guest users: 10 requests per hour per IP
- Authenticated users: Higher limits (credit-based)

**Response on Limit Exceeded**:
```json
{
  "success": false,
  "error": "Rate limit exceeded. Please try again later.",
  "retry_after": 3600
}
```

---

## 5. Workflow Engine

### 5.1 Flow Executor

**Location**: `/Modules/AI/app/Services/Workflow/FlowExecutor.php`

**Purpose**: Orchestrates node execution in defined flow

**Flow Structure**:
```php
[
    'id' => 'flow-123',
    'name' => 'E-commerce Chat Flow',
    'start_node' => 'context_builder',
    'nodes' => [
        ['id' => 'context_builder', 'type' => 'context_builder', 'config' => [...]],
        ['id' => 'ai_response', 'type' => 'ai_response', 'config' => [...]],
        ['id' => 'end', 'type' => 'end']
    ],
    'edges' => [
        ['from' => 'context_builder', 'to' => 'ai_response'],
        ['from' => 'ai_response', 'to' => 'end']
    ]
]
```

**Execution Flow**:
1. **Initialize Context**: Merge initial context with flow data
2. **Discover Parallel Groups**: Identify nodes that can run concurrently
3. **Execute Nodes**: Sequential or parallel execution
4. **Merge Results**: Accumulate context from each node
5. **Return Final Response**: Extract AI response from context

### 5.2 Node Types

#### **BaseNode** (Abstract)
All nodes extend this base class.

**Interface**:
```php
abstract public function execute(array $context): array;
abstract public function getType(): string;
```

#### **ContextBuilderNode**
**Purpose**: Enriches context with product/category/page data

**Output**:
```php
[
    'context_type' => 'product', // or 'category', 'general'
    'product_data' => [...],
    'category_data' => [...],
    'related_products' => [...]
]
```

#### **CategoryDetectionNode**
**Purpose**: Detects user intent category from message

**AI Prompt**:
```
Analyze the user message and determine the category:
- product_inquiry
- price_question
- stock_check
- recommendation_request
- general_question
```

**Output**:
```php
[
    'detected_category' => 'product_inquiry',
    'confidence' => 0.92
]
```

#### **ProductSearchNode**
**Purpose**: Searches products based on user query

**Features**:
- Keyword extraction
- Elasticsearch/Database query
- Relevance scoring

**Output**:
```php
[
    'search_results' => [
        ['id' => 1, 'title' => 'Transpalet 2.5 Ton', 'score' => 0.95],
        ['id' => 2, 'title' => 'Transpalet 3 Ton', 'score' => 0.87]
    ]
]
```

#### **StockSorterNode**
**Purpose**: Prioritizes in-stock products

**Logic**:
```php
usort($products, function($a, $b) {
    if ($a['stock'] > 0 && $b['stock'] === 0) return -1;
    if ($a['stock'] === 0 && $b['stock'] > 0) return 1;
    return $b['score'] <=> $a['score']; // Fallback to relevance
});
```

#### **AIResponseNode**
**Purpose**: Generates final AI response using OpenAI

**System Prompt**:
```
You are an e-commerce assistant for {tenant_name}.
Product catalog: {product_data}
Conversation history: {conversation_history}
User message: {user_message}

Generate a helpful, concise response in Turkish.
Use markdown for formatting.
Include product links using [LINK:shop:slug] format.
```

**OpenAI Configuration**:
- Model: `gpt-4-turbo-preview`
- Temperature: `0.7`
- Max Tokens: `500`

**Output**:
```php
[
    'ai_response' => '2.5 ton kapasiteli transpalet modellerimiz...',
    'tokens_used' => 234
]
```

#### **MessageSaverNode**
**Purpose**: Persists conversation to database

**Operations**:
1. Create/update `ai_conversations` record
2. Insert user message → `ai_messages` table
3. Insert AI response → `ai_messages` table

#### **WelcomeNode**
**Purpose**: Generates first-time user greeting

**Output**:
```php
[
    'ai_response' => 'Merhaba! Size nasıl yardımcı olabilirim? 👋'
]
```

#### **EndNode**
**Purpose**: Marks flow completion

**Output**:
```php
[
    'flow_completed' => true
]
```

### 5.3 Node Factory

**Location**: `/Modules/AI/app/Services/Workflow/Nodes/NodeFactory.php`

**Purpose**: Creates node instances based on type

```php
public static function create(string $type, array $config): BaseNode
{
    return match($type) {
        'context_builder' => new ContextBuilderNode($config),
        'category_detection' => new CategoryDetectionNode($config),
        'product_search' => new ProductSearchNode($config),
        'ai_response' => new AIResponseNode($config),
        'end' => new EndNode($config),
        default => throw new \Exception("Unknown node type: {$type}")
    };
}
```

---

## 6. Data Flow

### Complete Request-Response Cycle

```
USER ACTION: User types "Transpalet fiyatları nedir?"
     │
     ▼
[1] FRONTEND: Alpine.js captures input
     │
     ├─ Adds user message to UI (optimistic)
     ├─ Sets loading state
     └─ Sends POST /api/ai/v1/shop-assistant/chat
            │
            ▼
[2] API GATEWAY: Laravel route middleware
     │
     ├─ CSRF validation
     ├─ Rate limit check
     └─ Request validation
            │
            ▼
[3] CONTROLLER: PublicAIController@shopAssistantChatV2
     │
     ├─ Validate input schema
     ├─ Generate/restore session_id
     ├─ Load conversation history from DB
     └─ Get active workflow configuration
            │
            ▼
[4] WORKFLOW ENGINE: FlowExecutor->execute()
     │
     ├─ [Node 1] ContextBuilderNode
     │    └─ Output: { context_type: 'general' }
     │
     ├─ [Node 2] CategoryDetectionNode
     │    └─ AI Call: Detect intent
     │    └─ Output: { detected_category: 'price_question' }
     │
     ├─ [Node 3] ProductSearchNode
     │    └─ Database: Search 'transpalet'
     │    └─ Output: { search_results: [...] }
     │
     ├─ [Node 4] StockSorterNode
     │    └─ Sort by stock availability
     │    └─ Output: { sorted_products: [...] }
     │
     └─ [Node 5] AIResponseNode
          └─ OpenAI API Call
          ├─ System prompt with context
          ├─ Conversation history
          └─ User message
          └─ Output: { ai_response: 'Transpalet fiyatlarımız...' }
            │
            ▼
[5] PERSISTENCE: MessageSaverNode
     │
     ├─ Create/update ai_conversations
     ├─ Insert user message (role: 'user')
     └─ Insert AI response (role: 'assistant')
            │
            ▼
[6] RESPONSE FORMATTING: Controller
     │
     └─ JSON response with markdown-rendered HTML
            │
            ▼
[7] FRONTEND: Alpine.js receives response
     │
     ├─ Parse JSON
     ├─ Add AI message to UI
     ├─ Update session_id in localStorage
     ├─ Clear loading state
     └─ Scroll to bottom
            │
            ▼
[8] USER SEES: AI response in chat window
```

---

## 7. Database Schema

### 7.1 ai_conversations

Stores conversation sessions.

```sql
CREATE TABLE ai_conversations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(64) NOT NULL,
    tenant_id INT NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    feature_slug VARCHAR(100) DEFAULT 'shop-assistant',
    status ENUM('active', 'closed', 'archived') DEFAULT 'active',
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    INDEX idx_session_tenant (session_id, tenant_id),
    INDEX idx_user_id (user_id),
    INDEX idx_tenant_status (tenant_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 7.2 ai_messages

Stores individual messages in conversations.

```sql
CREATE TABLE ai_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED NOT NULL,
    role ENUM('user', 'assistant', 'system') NOT NULL,
    content LONGTEXT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (conversation_id) REFERENCES ai_conversations(id) ON DELETE CASCADE,
    INDEX idx_conversation_created (conversation_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 7.3 Eloquent Models

**AIConversation.php**:
```php
class AIConversation extends Model
{
    protected $fillable = [
        'session_id', 'tenant_id', 'user_id', 'feature_slug',
        'status', 'ip_address', 'user_agent'
    ];

    public function messages()
    {
        return $this->hasMany(AIMessage::class, 'conversation_id');
    }
}
```

**AIMessage.php**:
```php
class AIMessage extends Model
{
    protected $fillable = ['conversation_id', 'role', 'content'];

    public function conversation()
    {
        return $this->belongsTo(AIConversation::class, 'conversation_id');
    }
}
```

---

## 8. API Specification

### Endpoint: POST /api/ai/v1/shop-assistant/chat

**Request**:
```json
{
  "message": "Transpalet fiyatları nedir?",
  "session_id": "abc123xyz",
  "product_id": null,
  "category_id": null,
  "page_slug": null
}
```

**Response (Success)**:
```json
{
  "success": true,
  "data": {
    "message": "<p>Transpalet modellerimizin fiyatları <a href='/shop/category/transpalet'>buradan</a> görebilirsiniz...</p>",
    "session_id": "abc123xyz",
    "conversation_id": null,
    "metadata": {
      "system": "workflow_engine_v2",
      "flow_name": "E-commerce Chat Flow",
      "nodes_executed": ["context_builder", "ai_response", "end"]
    }
  }
}
```

**Response (Error)**:
```json
{
  "success": false,
  "message": "Geçersiz veri",
  "errors": {
    "message": ["The message field is required."]
  }
}
```

### Endpoint: GET /api/ai/v1/shop-assistant/history

**Query Parameters**:
- `session_id` (required): Session identifier

**Response**:
```json
{
  "success": true,
  "data": {
    "conversation_id": 123,
    "messages": [
      {
        "role": "user",
        "content": "Merhaba",
        "created_at": "2025-01-06T10:30:00Z"
      },
      {
        "role": "assistant",
        "content": "Merhaba! Nasıl yardımcı olabilirim?",
        "created_at": "2025-01-06T10:30:02Z"
      }
    ]
  }
}
```

---

## 9. Configuration

### 9.1 Environment Variables

```env
# OpenAI Configuration
OPENAI_API_KEY=sk-xxxxxxxxxxxxxxxxxxxxxxxx
OPENAI_MODEL=gpt-4-turbo-preview
OPENAI_MAX_TOKENS=500
OPENAI_TEMPERATURE=0.7

# Rate Limiting
AI_RATE_LIMIT_GUEST=10
AI_RATE_LIMIT_WINDOW=3600

# Session
AI_SESSION_LIFETIME=7200
```

### 9.2 Flow Configuration

Active flow stored in `ai_flows` table as JSON:

```json
{
  "id": "ecommerce-chat-v1",
  "name": "E-commerce Chat Flow",
  "start_node": "context_builder",
  "nodes": [
    {
      "id": "context_builder",
      "type": "context_builder",
      "config": {
        "load_product": true,
        "load_category": true
      }
    },
    {
      "id": "ai_response",
      "type": "ai_response",
      "config": {
        "model": "gpt-4-turbo-preview",
        "temperature": 0.7,
        "max_tokens": 500
      }
    },
    {
      "id": "end",
      "type": "end"
    }
  ],
  "edges": [
    {"from": "context_builder", "to": "ai_response"},
    {"from": "ai_response", "to": "end"}
  ]
}
```

---

## 10. Deployment

### 10.1 Requirements

- PHP 8.2+
- MySQL 8.0+
- Redis 6.0+
- Node.js 18+ (for asset compilation)
- Composer 2.x
- OpenAI API access

### 10.2 Installation Steps

```bash
# 1. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 2. Install Node dependencies
npm install

# 3. Compile frontend assets
npm run prod

# 4. Run migrations
php artisan migrate --force

# 5. Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache

# 7. Start queue worker
php artisan queue:work --daemon
```

### 10.3 Performance Optimization

**OPcache Configuration** (`php.ini`):
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

**Redis Cache**:
```bash
# Flush cache on deployment
php artisan cache:clear
php artisan config:cache
```

**Asset Optimization**:
```bash
# Minify CSS/JS
npm run prod

# Gzip compression (nginx)
gzip on;
gzip_types text/css application/javascript application/json;
```

### 10.4 Monitoring

**Logs**:
- Application: `storage/logs/laravel.log`
- Workflow: Filterable with `grep "🚀 FlowExecutor"`
- Errors: `grep "❌"`

**Metrics**:
- Response time: Average node execution time
- Token usage: OpenAI API consumption tracking
- Conversation count: Active sessions per tenant

**Health Check**:
```bash
# Test API
curl -X POST https://domain.com/api/ai/v1/shop-assistant/chat \
  -H "Content-Type: application/json" \
  -d '{"message":"test"}'
```

---

## Appendix

### A. Error Codes

| Code | Message | Cause |
|------|---------|-------|
| 422 | Validation Error | Invalid input format |
| 429 | Rate Limit Exceeded | Too many requests |
| 500 | Internal Server Error | Workflow/OpenAI failure |

### B. Troubleshooting

**Issue**: Chat widget not appearing
- Check asset compilation: `npm run prod`
- Verify component inclusion in layout
- Check browser console for JS errors

**Issue**: AI responses empty
- Verify OpenAI API key in `.env`
- Check workflow configuration in database
- Review logs: `tail -f storage/logs/laravel.log`

**Issue**: Session not persisting
- Clear Redis cache: `redis-cli FLUSHDB`
- Check localStorage in browser DevTools
- Verify `session_id` in API responses

---

**Version**: 2.3
**Last Updated**: 2025-01-06
**Author**: iXtif Development Team
