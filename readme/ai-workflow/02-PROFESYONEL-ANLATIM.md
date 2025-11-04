# 🏗️ AI CONVERSATION WORKFLOW ENGINE - PROFESYONEL MİMARİ

## SİSTEM MİMARİSİ

### Genel Bakış

```
┌─────────────────────────────────────────────────────────────┐
│                     ARCHITECTURE LAYERS                      │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌─────────────────────────────────────────────────────┐   │
│  │         PRESENTATION LAYER (Admin Panel)            │   │
│  │  - Drawflow Visual Editor                           │   │
│  │  - Livewire Components (Flow Manager)              │   │
│  │  - Node Configuration UI                            │   │
│  └──────────────────┬──────────────────────────────────┘   │
│                     │                                        │
│                     ↓                                        │
│  ┌─────────────────────────────────────────────────────┐   │
│  │         APPLICATION LAYER (Business Logic)          │   │
│  │  - NodeExecutor (Orchestration)                     │   │
│  │  - ConversationFlowEngine                           │   │
│  │  - StateManager                                      │   │
│  └──────────────────┬──────────────────────────────────┘   │
│                     │                                        │
│                     ↓                                        │
│  ┌─────────────────────────────────────────────────────┐   │
│  │         DOMAIN LAYER (Node Handlers)                │   │
│  │  - AIResponseNode                                    │   │
│  │  - ShowProductsNode                                  │   │
│  │  - ConditionNode                                     │   │
│  │  - CustomActionNode                                  │   │
│  └──────────────────┬──────────────────────────────────┘   │
│                     │                                        │
│                     ↓                                        │
│  ┌─────────────────────────────────────────────────────┐   │
│  │         DATA LAYER (Persistence)                     │   │
│  │  - TenantConversationFlow (Eloquent)                │   │
│  │  - AITenantDirective (Eloquent)                     │   │
│  │  - Conversation (State tracking)                     │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

---

## DATABASE SCHEMA

### 1. tenant_conversation_flows

```sql
CREATE TABLE tenant_conversation_flows (
    -- Birincil anahtar
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
        COMMENT 'Akış ID - Benzersiz tanımlayıcı',

    -- Tenant ilişkisi
    tenant_id INT UNSIGNED NOT NULL
        COMMENT 'Hangi tenant (örn: 2=ixtif.com, 3=diğer)',

    -- Akış bilgileri
    flow_name VARCHAR(255) NOT NULL
        COMMENT 'Akış adı - Admin panelde görünen isim (örn: "E-Ticaret Satış Akışı")',

    flow_description TEXT
        COMMENT 'Akış açıklaması - Admin için bilgi notu, kullanıcı görmez',

    flow_data JSON NOT NULL
        COMMENT 'Tüm akış yapısı: nodes (kutucuklar), edges (bağlantılar), positions - Drawflow JSON',

    start_node_id VARCHAR(50) NOT NULL
        COMMENT 'İlk çalışacak node ID - Akış buradan başlar (örn: "node_greeting_1")',

    -- Durum kontrol
    is_active BOOLEAN DEFAULT TRUE
        COMMENT 'Aktif mi? 1=kullanımda, 0=devre dışı (sadece aktif olanlar çalışır)',

    priority INT DEFAULT 0
        COMMENT 'Öncelik - Birden fazla aktif flow varsa en düşük sayı çalışır (0 en yüksek öncelik)',

    -- Audit bilgileri
    created_by BIGINT UNSIGNED
        COMMENT 'Akışı oluşturan admin user ID - users tablosundan',

    updated_by BIGINT UNSIGNED
        COMMENT 'Son güncelleyen admin user ID - users tablosundan',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Oluşturulma tarihi',

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        COMMENT 'Son güncellenme tarihi - Otomatik güncellenir',

    -- İndeksler (performans)
    INDEX idx_tenant_active (tenant_id, is_active)
        COMMENT 'Tenant aktif akış sorgusunu hızlandırır',

    INDEX idx_priority (tenant_id, priority DESC)
        COMMENT 'Öncelik sırasına göre seçim için - En düşük sayı önce',

    -- Foreign key
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        COMMENT 'Tenant silinirse akışları da sil'

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    COMMENT='Tenant AI sohbet akışları - Admin panelden çizilen akışlar burada saklanır';
```

**flow_data JSON yapısı:**
```json
{
  "nodes": [
    {
      "id": "node_uuid_1",
      "type": "ai_response",
      "name": "Greeting Step",
      "class": "App\\Services\\ConversationNodes\\AIResponseNode",
      "config": {
        "system_prompt": "Greet the customer warmly",
        "temperature": 0.7,
        "max_tokens": 150
      },
      "position": {"x": 100, "y": 100},
      "inputs": [],
      "outputs": ["output_1"]
    },
    {
      "id": "node_uuid_2",
      "type": "show_products",
      "name": "Product Recommendation",
      "class": "App\\Services\\ConversationNodes\\ShowProductsNode",
      "config": {
        "filters": ["homepage", "high_stock"],
        "sort_by": "priority",
        "limit": 5,
        "include_price": true
      },
      "position": {"x": 100, "y": 300},
      "inputs": ["input_1"],
      "outputs": ["output_1", "output_2"]
    }
  ],
  "edges": [
    {
      "id": "edge_uuid_1",
      "source": "node_uuid_1",
      "target": "node_uuid_2",
      "sourceOutput": "output_1",
      "targetInput": "input_1",
      "condition": null
    }
  ],
  "variables": {
    "greeting_style": "{{tenant.directive.greeting_style}}",
    "max_products": "{{tenant.directive.max_products}}"
  }
}
```

### 2. ai_tenant_directives (Central Config)

```sql
CREATE TABLE ai_tenant_directives (
    -- Birincil anahtar
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
        COMMENT 'Directive ID - Benzersiz tanımlayıcı',

    -- Tenant ilişkisi
    tenant_id INT UNSIGNED NOT NULL
        COMMENT 'Hangi tenant (örn: 2=ixtif.com)',

    -- Directive bilgileri
    directive_key VARCHAR(100) NOT NULL
        COMMENT 'Ayar anahtarı - Kod içinde kullanılan isim (örn: "greeting_style", "max_products")',

    directive_value TEXT NOT NULL
        COMMENT 'Ayar değeri - String, sayı, JSON olabilir (örn: "friendly", "5", "true")',

    directive_type ENUM('string', 'integer', 'boolean', 'json', 'array') DEFAULT 'string'
        COMMENT 'Değer tipi - Kod tarafında nasıl parse edileceğini belirler',

    -- Kategorileme
    category VARCHAR(50) DEFAULT 'general'
        COMMENT 'Kategori - Ayarları gruplamak için (general, behavior, pricing, contact, display, lead)',

    description VARCHAR(255)
        COMMENT 'Açıklama - Admin için bilgi, bu ayar ne işe yarar',

    -- Durum
    is_active BOOLEAN DEFAULT TRUE
        COMMENT 'Aktif mi? 1=kullanımda, 0=devre dışı (sadece aktif olanlar okunur)',

    -- Zaman damgaları
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Oluşturulma tarihi',

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        COMMENT 'Son güncellenme tarihi - Otomatik güncellenir',

    -- Kısıtlamalar
    UNIQUE KEY uk_tenant_key (tenant_id, directive_key)
        COMMENT 'Aynı tenant içinde aynı key tekrar edemez - Her ayar unique',

    INDEX idx_tenant_category (tenant_id, category)
        COMMENT 'Kategoriye göre hızlı filtreleme',

    -- Foreign key
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        COMMENT 'Tenant silinirse ayarları da sil'

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    COMMENT='Tenant AI davranış ayarları - Tenant admin bu ayarları yönetir';
```

**Örnek directive kayıtları:**
```sql
INSERT INTO ai_tenant_directives (tenant_id, directive_key, directive_value, directive_type, category) VALUES
(2, 'greeting_style', 'formal', 'string', 'behavior'),
(2, 'show_price', 'true', 'boolean', 'pricing'),
(2, 'max_products_per_response', '5', 'integer', 'general'),
(2, 'emoji_usage', 'moderate', 'string', 'behavior'),
(2, 'contact_priority', '["whatsapp", "phone", "email"]', 'json', 'contact');
```

### 3. ai_conversations (State Tracking)

```sql
CREATE TABLE ai_conversations (
    -- Birincil anahtar
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
        COMMENT 'Sohbet ID - Her sohbet oturumu için benzersiz',

    -- İlişkiler
    tenant_id INT UNSIGNED NOT NULL
        COMMENT 'Hangi tenant (örn: 2=ixtif.com)',

    flow_id BIGINT UNSIGNED NOT NULL
        COMMENT 'Hangi akış kullanılıyor - tenant_conversation_flows tablosundan',

    -- Durum takibi
    current_node_id VARCHAR(50)
        COMMENT 'Şu anda hangi node''da - Akış içinde konum (örn: "node_greeting_1")',

    session_id VARCHAR(100) UNIQUE NOT NULL
        COMMENT 'Browser session ID - Her ziyaretçi için unique (cookie/localStorage)',

    user_id BIGINT UNSIGNED NULL
        COMMENT 'Kayıtlı kullanıcı ID - Varsa users tablosundan, yoksa NULL (guest)',

    -- Sohbet verisi
    context_data JSON
        COMMENT 'Sohbet sırasında toplanan veriler - Telefon, email, tercihler vb. JSON formatında',

    state_history JSON
        COMMENT 'Node geçiş geçmişi - Hangi node''lardan geçti, ne zaman, JSON array [{node_id, timestamp, success}]',

    -- Zaman damgaları
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Sohbet başlangıç zamanı',

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        COMMENT 'Son mesaj zamanı - Her mesajda güncellenir',

    -- İndeksler
    INDEX idx_session (session_id)
        COMMENT 'Session ile hızlı erişim - Her mesajda kullanılır',

    INDEX idx_tenant_flow (tenant_id, flow_id)
        COMMENT 'Tenant akış istatistikleri için',

    -- Foreign keys
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        COMMENT 'Tenant silinirse sohbetleri de sil',

    FOREIGN KEY (flow_id) REFERENCES tenant_conversation_flows(id) ON DELETE CASCADE
        COMMENT 'Akış silinirse o akışın sohbetlerini sil'

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    COMMENT='AI sohbet oturumları - Her kullanıcı oturumu burada takip edilir';
```

---

## CORE COMPONENTS

### 1. Node Base Class (Abstract)

```php
// app/Services/ConversationNodes/AbstractNode.php

namespace App\Services\ConversationNodes;

use App\Models\AIConversation;

abstract class AbstractNode
{
    /**
     * Node configuration
     */
    protected array $config;

    /**
     * Node execution result
     */
    protected array $result = [
        'success' => false,
        'prompt' => null,
        'data' => [],
        'next_node' => null,
        'error' => null
    ];

    /**
     * Constructor
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Execute node logic
     *
     * @param AIConversation $conversation
     * @param string $userMessage
     * @return array
     */
    abstract public function execute(AIConversation $conversation, string $userMessage): array;

    /**
     * Validate node configuration
     */
    abstract public function validate(): bool;

    /**
     * Get node metadata
     */
    public function getMetadata(): array
    {
        return [
            'type' => static::getType(),
            'name' => static::getName(),
            'description' => static::getDescription(),
            'config_schema' => static::getConfigSchema(),
            'inputs' => static::getInputs(),
            'outputs' => static::getOutputs()
        ];
    }

    /**
     * Get node type identifier
     */
    abstract public static function getType(): string;

    /**
     * Get node display name
     */
    abstract public static function getName(): string;

    /**
     * Get node description
     */
    abstract public static function getDescription(): string;

    /**
     * Get configuration schema (for admin UI)
     */
    abstract public static function getConfigSchema(): array;

    /**
     * Get input definitions
     */
    abstract public static function getInputs(): array;

    /**
     * Get output definitions
     */
    abstract public static function getOutputs(): array;
}
```

### 2. Node Executor (Orchestrator)

```php
// app/Services/ConversationNodes/NodeExecutor.php

namespace App\Services\ConversationNodes;

use App\Models\AIConversation;
use Illuminate\Support\Facades\Log;

class NodeExecutor
{
    /**
     * Registered node handlers
     */
    protected static array $nodeRegistry = [
        'ai_response' => Nodes\AIResponseNode::class,
        'show_products' => Nodes\ShowProductsNode::class,
        'show_price' => Nodes\ShowPriceNode::class,
        'get_phone' => Nodes\GetPhoneNode::class,
        'condition' => Nodes\ConditionNode::class,
        'custom_action' => Nodes\CustomActionNode::class,
    ];

    /**
     * Execute a node
     */
    public function execute(array $nodeData, AIConversation $conversation, string $userMessage): array
    {
        try {
            // Get node handler class
            $handlerClass = $this->resolveNodeHandler($nodeData['type']);

            // Instantiate handler with config
            $handler = new $handlerClass($nodeData['config'] ?? []);

            // Validate configuration
            if (!$handler->validate()) {
                throw new \Exception("Invalid node configuration for {$nodeData['type']}");
            }

            // Execute node
            $result = $handler->execute($conversation, $userMessage);

            // Log execution
            Log::info('Node executed', [
                'node_id' => $nodeData['id'],
                'type' => $nodeData['type'],
                'conversation_id' => $conversation->id,
                'success' => $result['success']
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Node execution failed', [
                'node_id' => $nodeData['id'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'next_node' => null
            ];
        }
    }

    /**
     * Resolve node handler class
     */
    protected function resolveNodeHandler(string $nodeType): string
    {
        if (!isset(self::$nodeRegistry[$nodeType])) {
            throw new \Exception("Unknown node type: {$nodeType}");
        }

        return self::$nodeRegistry[$nodeType];
    }

    /**
     * Register a custom node handler
     */
    public static function registerNode(string $type, string $handlerClass): void
    {
        self::$nodeRegistry[$type] = $handlerClass;
    }

    /**
     * Get all registered nodes metadata
     */
    public static function getAvailableNodes(): array
    {
        return collect(self::$nodeRegistry)
            ->map(fn($class) => (new $class())->getMetadata())
            ->values()
            ->toArray();
    }
}
```

### 3. Conversation Flow Engine

```php
// app/Services/ConversationFlowEngine.php

namespace App\Services;

use App\Models\{AIConversation, TenantConversationFlow};
use App\Services\ConversationNodes\NodeExecutor;
use Illuminate\Support\Facades\{Cache, Log};

class ConversationFlowEngine
{
    protected NodeExecutor $executor;

    public function __construct(NodeExecutor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * Process user message through conversation flow
     */
    public function processMessage(AIConversation $conversation, string $userMessage): array
    {
        // Get active flow
        $flow = $this->getFlow($conversation);

        // Get current node
        $currentNode = $this->getCurrentNode($conversation, $flow);

        // Execute node
        $result = $this->executor->execute($currentNode, $conversation, $userMessage);

        if (!$result['success']) {
            return $this->handleError($conversation, $result);
        }

        // Update conversation state
        $this->updateConversationState($conversation, $currentNode, $result);

        // Prepare AI context
        $aiContext = $this->buildAIContext($conversation, $result);

        // Generate AI response
        $aiResponse = $this->generateAIResponse($result['prompt'], $aiContext);

        return [
            'success' => true,
            'response' => $aiResponse,
            'current_node' => $currentNode['name'],
            'next_node' => $result['next_node'],
            'context' => $result['data'] ?? []
        ];
    }

    /**
     * Get active flow for conversation
     */
    protected function getFlow(AIConversation $conversation): TenantConversationFlow
    {
        return Cache::remember(
            "conversation_flow_{$conversation->tenant_id}_{$conversation->flow_id}",
            3600,
            fn() => TenantConversationFlow::findOrFail($conversation->flow_id)
        );
    }

    /**
     * Get current node from flow
     */
    protected function getCurrentNode(AIConversation $conversation, TenantConversationFlow $flow): array
    {
        $flowData = $flow->flow_data;
        $currentNodeId = $conversation->current_node_id ?? $flow->start_node_id;

        $node = collect($flowData['nodes'])->firstWhere('id', $currentNodeId);

        if (!$node) {
            throw new \Exception("Node not found: {$currentNodeId}");
        }

        return $node;
    }

    /**
     * Update conversation state after node execution
     */
    protected function updateConversationState(AIConversation $conversation, array $currentNode, array $result): void
    {
        $stateHistory = $conversation->state_history ?? [];
        $stateHistory[] = [
            'node_id' => $currentNode['id'],
            'node_type' => $currentNode['type'],
            'timestamp' => now()->toISOString(),
            'result' => $result['success']
        ];

        $conversation->update([
            'current_node_id' => $result['next_node'],
            'state_history' => $stateHistory,
            'context_data' => array_merge(
                $conversation->context_data ?? [],
                $result['data'] ?? []
            )
        ]);
    }

    /**
     * Build AI context from conversation and node result
     */
    protected function buildAIContext(AIConversation $conversation, array $result): array
    {
        return [
            'tenant_id' => $conversation->tenant_id,
            'conversation_context' => $conversation->context_data ?? [],
            'node_data' => $result['data'] ?? [],
            'directives' => $this->getTenantDirectives($conversation->tenant_id),
            'message_history' => $this->getMessageHistory($conversation)
        ];
    }

    /**
     * Get tenant directives
     */
    protected function getTenantDirectives(int $tenantId): array
    {
        return Cache::remember(
            "tenant_directives_{$tenantId}",
            3600,
            fn() => \App\Models\AITenantDirective::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->pluck('directive_value', 'directive_key')
                ->toArray()
        );
    }

    /**
     * Get conversation message history
     */
    protected function getMessageHistory(AIConversation $conversation): array
    {
        return $conversation->messages()
            ->latest()
            ->limit(10)
            ->get()
            ->reverse()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->content
            ])
            ->toArray();
    }

    /**
     * Generate AI response
     */
    protected function generateAIResponse(string $prompt, array $context): string
    {
        return app(\App\Services\AIService::class)->ask($prompt, $context);
    }

    /**
     * Handle node execution error
     */
    protected function handleError(AIConversation $conversation, array $result): array
    {
        Log::error('Flow execution error', [
            'conversation_id' => $conversation->id,
            'error' => $result['error']
        ]);

        return [
            'success' => false,
            'response' => 'Üzgünüm, bir hata oluştu. Lütfen tekrar deneyin.',
            'error' => $result['error']
        ];
    }
}
```

---

## NODE IMPLEMENTATIONS

### Example 1: CategoryDetectionNode (İxtif.com Özel)

```php
// app/Services/ConversationNodes/Nodes/CategoryDetectionNode.php

namespace App\Services\ConversationNodes\Nodes;

use App\Services\ConversationNodes\AbstractNode;
use App\Models\{AIConversation, ShopCategory};

class CategoryDetectionNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        // Kategori tespiti için anahtar kelimeler
        $categoryKeywords = [
            'transpalet' => ['transpalet', 'transpaleti', 'trans palet', 'palet taşıma'],
            'forklift' => ['forklift', 'fork lift', 'istif makinesi', 'yükleyici'],
            'istif' => ['istif', 'istifleme', 'reach truck'],
            'yedek_parca' => ['yedek parça', 'parça', 'aksesuar']
        ];

        // Mesajdan kategori tespit et
        $detectedCategory = null;
        $userMessageLower = mb_strtolower($userMessage, 'UTF-8');

        foreach ($categoryKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($userMessageLower, $keyword)) {
                    $detectedCategory = $category;
                    break 2;
                }
            }
        }

        // Kategori sınırlaması uygula
        $conversation->update([
            'context_data' => array_merge(
                $conversation->context_data ?? [],
                [
                    'detected_category' => $detectedCategory,
                    'category_locked' => true,  // Kategori dışına çıkma
                    'allow_cross_category' => false
                ]
            )
        ]);

        // AI'a kategori context'i ver
        $prompt = $detectedCategory
            ? "Müşteri {$detectedCategory} kategorisinde arama yapıyor. Bu kategoriden ürün öner."
            : "Müşteri hangi kategoride ürün arıyor, netleştirmeye çalış.";

        return [
            'success' => true,
            'prompt' => $prompt,
            'data' => [
                'category' => $detectedCategory,
                'category_locked' => true
            ],
            'next_node' => $detectedCategory ? 'product_recommendation' : 'ask_category'
        ];
    }

    public function validate(): bool
    {
        return true;
    }

    public static function getType(): string
    {
        return 'category_detection';
    }

    public static function getName(): string
    {
        return 'Kategori Tespit';
    }

    public static function getDescription(): string
    {
        return 'Kullanıcı mesajından ürün kategorisini tespit eder ve kategori sınırlaması uygular';
    }
}
```

### Example 2: ProductRecommendationNode (İxtif.com Özel)

```php
// app/Services/ConversationNodes/Nodes/ProductRecommendationNode.php

namespace App\Services\ConversationNodes\Nodes;

use App\Services\ConversationNodes\AbstractNode;
use App\Models\{AIConversation, Product};

class ProductRecommendationNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        // Kategori context'ini al
        $contextData = $conversation->context_data ?? [];
        $detectedCategory = $contextData['detected_category'] ?? null;
        $categoryLocked = $contextData['category_locked'] ?? false;

        // İXTİF.COM İÇİN ÖNCELİK SIRASI
        // 1. show_on_homepage = 1 olanlar
        // 2. Yüksek stoklu ürünler
        // 3. Kategori filtresi (eğer tespit edildiyse)

        $query = \Modules\Shop\Models\Product::where('tenant_id', $conversation->tenant_id)
            ->where('is_active', true)
            ->where('status', 1);

        // Kategori sınırlaması
        if ($categoryLocked && $detectedCategory) {
            $query->whereHas('category', function($q) use ($detectedCategory) {
                $q->where('slug', 'like', "%{$detectedCategory}%");
            });
        }

        // İXTİF ÖZEL: Öncelik sıralaması
        $query->orderByRaw('CASE WHEN show_on_homepage = 1 THEN 0 ELSE 1 END')  // Anasayfa öncelik
              ->orderBy('stock_quantity', 'DESC')  // Stok miktarı sıralama
              ->orderBy('sort_order', 'ASC');      // Sıralama numarası

        // Get products
        $products = $query->limit($limit)->get();

        // Build prompt
        $productList = $products->map(function($product) use ($includePrice) {
            $info = [
                'name' => $product->getTranslated('title'),
                'features' => $product->getTranslated('short_description')
            ];

            if ($includePrice && $product->base_price > 0) {
                $info['price'] = $product->base_price . ' ' . $product->currency;
            }

            return $info;
        })->toArray();

        $prompt = "Kullanıcıya aşağıdaki ürünleri öner:\n" .
                  json_encode($productList, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return [
            'success' => true,
            'prompt' => $prompt,
            'data' => [
                'products' => $products->toArray(),
                'product_count' => $products->count()
            ],
            'next_node' => $this->config['next_node'] ?? null
        ];
    }

    public function validate(): bool
    {
        // Validate required config
        if (!isset($this->config['limit']) || $this->config['limit'] < 1) {
            return false;
        }

        return true;
    }

    public static function getType(): string
    {
        return 'show_products';
    }

    public static function getName(): string
    {
        return 'Ürün Göster';
    }

    public static function getDescription(): string
    {
        return 'Filtrelere göre ürün listesi gösterir ve AI\'a context sağlar';
    }

    public static function getConfigSchema(): array
    {
        return [
            'filters' => [
                'type' => 'array',
                'label' => 'Filtreler',
                'options' => [
                    'homepage' => 'Anasayfa Ürünleri',
                    'high_stock' => 'Yüksek Stok',
                    'featured' => 'Öne Çıkan'
                ],
                'default' => ['homepage']
            ],
            'sort_by' => [
                'type' => 'select',
                'label' => 'Sıralama',
                'options' => [
                    'priority' => 'Öncelik',
                    'stock' => 'Stok (Yüksek → Düşük)',
                    'price' => 'Fiyat (Düşük → Yüksek)'
                ],
                'default' => 'priority'
            ],
            'limit' => [
                'type' => 'number',
                'label' => 'Maksimum Ürün Sayısı',
                'min' => 1,
                'max' => 20,
                'default' => 5
            ],
            'include_price' => [
                'type' => 'boolean',
                'label' => 'Fiyat Göster',
                'default' => true
            ]
        ];
    }

    public static function getInputs(): array
    {
        return [
            ['id' => 'input_1', 'label' => 'Tetikleyici']
        ];
    }

    public static function getOutputs(): array
    {
        return [
            ['id' => 'output_1', 'label' => 'Ürünler bulundu'],
            ['id' => 'output_2', 'label' => 'Ürün bulunamadı']
        ];
    }
}
```

---

## ADMIN PANEL INTEGRATION

### Livewire Component: Flow Manager

```php
// app/Http/Livewire/Admin/AI/FlowManager.php

namespace App\Http\Livewire\Admin\AI;

use Livewire\Component;
use App\Models\TenantConversationFlow;
use App\Services\ConversationNodes\NodeExecutor;

class FlowManager extends Component
{
    public $flowId;
    public $flowName;
    public $flowData;
    public $availableNodes;

    public function mount($flowId = null)
    {
        $this->flowId = $flowId;

        if ($flowId) {
            $flow = TenantConversationFlow::findOrFail($flowId);
            $this->flowName = $flow->flow_name;
            $this->flowData = $flow->flow_data;
        }

        // Get available node types
        $this->availableNodes = NodeExecutor::getAvailableNodes();
    }

    public function saveFlow($flowDataJson)
    {
        $this->validate([
            'flowName' => 'required|string|max:255'
        ]);

        $flowData = json_decode($flowDataJson, true);

        TenantConversationFlow::updateOrCreate(
            ['id' => $this->flowId],
            [
                'tenant_id' => tenant('id'),
                'flow_name' => $this->flowName,
                'flow_data' => $flowData,
                'start_node_id' => $flowData['nodes'][0]['id'] ?? null,
                'is_active' => true
            ]
        );

        session()->flash('message', 'Flow başarıyla kaydedildi!');

        return redirect()->route('admin.ai-flows.index');
    }

    public function render()
    {
        return view('livewire.admin.ai.flow-manager');
    }
}
```

### Blade View: Drawflow Integration

```blade
<!-- resources/views/livewire/admin/ai/flow-manager.blade.php -->

<div class="flow-manager-container">
    <div class="flow-header">
        <input type="text"
               wire:model="flowName"
               placeholder="Flow Adı"
               class="form-control">
        <button wire:click="saveFlow" class="btn btn-primary">Kaydet</button>
    </div>

    <div class="flow-editor-wrapper">
        <!-- Node Library (Left Sidebar) -->
        <div class="node-library">
            <h5>Node Kütüphanesi</h5>
            @foreach($availableNodes as $node)
                <div class="node-item"
                     draggable="true"
                     data-node-type="{{ $node['type'] }}"
                     data-node-config="{{ json_encode($node) }}">
                    <i class="icon-{{ $node['type'] }}"></i>
                    {{ $node['name'] }}
                </div>
            @endforeach
        </div>

        <!-- Drawflow Canvas -->
        <div id="drawflow" class="drawflow-canvas"></div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/drawflow/drawflow.min.js') }}"></script>
<script>
    // Initialize Drawflow
    const editor = new Drawflow(document.getElementById('drawflow'));
    editor.start();

    // Load existing flow data
    @if($flowData)
        editor.import(@json($flowData));
    @endif

    // Drag & Drop Node Library
    document.querySelectorAll('.node-item').forEach(item => {
        item.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('node', e.target.dataset.nodeConfig);
        });
    });

    document.getElementById('drawflow').addEventListener('drop', (e) => {
        e.preventDefault();
        const nodeData = JSON.parse(e.dataTransfer.getData('node'));

        // Add node to canvas
        editor.addNode(
            nodeData.type,
            nodeData.inputs.length,
            nodeData.outputs.length,
            e.clientX,
            e.clientY,
            nodeData.type,
            nodeData.config,
            nodeData.name
        );
    });

    // Save button
    document.querySelector('.btn-primary').addEventListener('click', () => {
        const flowData = editor.export();
        @this.call('saveFlow', JSON.stringify(flowData));
    });
</script>
@endpush
```

---

## DEPLOYMENT & PERFORMANCE

### Caching Strategy

```php
// Cache tenant flows
Cache::remember("flow_{$tenantId}", 3600, fn() => TenantConversationFlow::active()->first());

// Cache tenant directives
Cache::remember("directives_{$tenantId}", 3600, fn() => AITenantDirective::getAll());

// Cache node registry
Cache::rememberForever('node_registry', fn() => NodeExecutor::getAvailableNodes());
```

### Monitoring

```php
// Log node execution metrics
Log::info('Node execution', [
    'node_type' => $nodeType,
    'execution_time' => $executionTime,
    'memory_usage' => memory_get_usage(true)
]);
```

---

## İXTİF.COM ÖZEL AKIŞ DETAYLARI

### 10 Adımlık E-Ticaret Satış Akışı

```yaml
flow_name: "İxtif.com E-Ticaret Satış Akışı"
tenant_id: 2
priority: 1
nodes:
  - id: node_1
    type: greeting
    name: "Karşılama"

  - id: node_2
    type: category_detection
    name: "Kategori Tespit"
    config:
      lock_category: true  # Kategori dışına çıkma

  - id: node_3
    type: product_recommendation
    name: "Ürün Önerme"
    config:
      priority_order:
        1: "show_on_homepage = 1"
        2: "stock_quantity DESC"
        3: "sort_order ASC"

  - id: node_4
    type: price_filter
    name: "Fiyat Filtreleme"
    config:
      triggers: ["ucuz", "ekonomik", "pahalı", "kaliteli"]

  - id: node_5
    type: currency_display
    name: "Para Birimi"

  - id: node_6
    type: currency_conversion
    name: "Kur Dönüşümü"
    config:
      source: "exchange_rates"

  - id: node_7
    type: product_detail
    name: "Ürün Detay"

  - id: node_8
    type: collect_phone
    name: "Telefon Al"
    config:
      regex: "^(\+90|0)?[0-9]{10}$"
      save_to: "leads"

  - id: node_9
    type: share_contact
    name: "İletişim Paylaş"
    config:
      source: "settings_values"
      show: ["whatsapp", "phone"]

  - id: node_10
    type: mail_address
    name: "Mail/Adres"
    config:
      source: "settings_values"
```

### Tenant Directives (İxtif.com Özel)

```sql
-- İxtif.com için kritik ayarlar
INSERT INTO ai_tenant_directives (tenant_id, directive_key, directive_value, category) VALUES
-- Kategori Ayarları
(2, 'category_boundary_strict', 'true', 'behavior'),
(2, 'allow_cross_category', 'false', 'behavior'),
(2, 'auto_detect_category', 'true', 'behavior'),

-- Ürün Gösterim
(2, 'priority_homepage_products', 'true', 'display'),
(2, 'sort_by_stock', 'true', 'display'),
(2, 'show_stock_status', 'false', 'display'),  -- Exact stok gösterme
(2, 'max_products_per_response', '5', 'display'),

-- Fiyat Politikası
(2, 'show_price_without_asking', 'true', 'pricing'),
(2, 'currency_conversion_enabled', 'true', 'pricing'),
(2, 'default_currency', 'USD', 'pricing'),
(2, 'show_tax_included', 'false', 'pricing'),

-- Lead Toplama
(2, 'collect_phone_required', 'true', 'lead'),
(2, 'phone_regex_tr', '^(\+90|0)?[0-9]{10}$', 'lead'),
(2, 'auto_save_leads', 'true', 'lead'),

-- Teknik Özellikler
(2, 'enable_comparison', 'true', 'features'),
(2, 'enable_quotation', 'true', 'features'),
(2, 'technical_support_redirect', 'true', 'features');
```

## SUMMARY

**Architecture Pattern:** Event-Driven Workflow Engine
**Visual Editor:** Drawflow (MIT License)
**State Management:** Database + Cache
**Extensibility:** Plugin-based node system
**Multi-tenancy:** Tenant-isolated flows + Central directives
**Performance:** Cached flows, lazy node loading

**İxtif.com Özellikleri:**
- ✅ Kategori odaklı satış (transpalet/forklift)
- ✅ Anasayfa + stok öncelikli sıralama
- ✅ Kur dönüşümü (exchange_rates)
- ✅ Ürün karşılaştırma (F4 vs F6)
- ✅ Lead toplama ve scoring
- ✅ WhatsApp/telefon entegrasyonu

**Key Benefits:**
- ✅ Visual flow designer (no-code for admins)
- ✅ Code-based nodes (extensible for developers)
- ✅ Multi-tenant isolated
- ✅ Database-driven (dynamic configuration)
- ✅ Production-ready (caching, logging, error handling)
- ✅ İxtif.com'a özel e-ticaret optimizasyonu
