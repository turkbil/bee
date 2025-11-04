# 🎯 NODE EXECUTOR IMPLEMENTATION GUIDE

## TEMEL YAPISI

```
User Message → Flow Seçimi → Node Executor → Node Handler → Response
                                    ↓
                              Context Store
```

---

## 1. BASE CLASSES

### NodeExecutor.php
```php
<?php
namespace App\Services\ConversationNodes;

use App\Models\TenantConversationFlow;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NodeExecutor
{
    protected array $context = [];
    protected array $handlers = [];
    protected ?string $sessionId = null;
    protected ?int $flowId = null;

    public function __construct()
    {
        $this->registerHandlers();
    }

    /**
     * Register all available node handlers
     */
    protected function registerHandlers(): void
    {
        $this->handlers = [
            'ai_response' => Handlers\AIResponseNode::class,
            'category_detection' => Handlers\CategoryDetectionNode::class,
            'product_recommendation' => Handlers\ProductRecommendationNode::class,
            'condition' => Handlers\ConditionNode::class,
            'price_filter' => Handlers\PriceFilterNode::class,
            'collect_data' => Handlers\CollectDataNode::class,
            'quotation' => Handlers\QuotationNode::class,
            'share_contact' => Handlers\ShareContactNode::class,
            'end' => Handlers\EndNode::class,
        ];
    }

    /**
     * Execute a flow from start
     */
    public function executeFlow(int $flowId, string $message, string $sessionId): array
    {
        $this->flowId = $flowId;
        $this->sessionId = $sessionId;

        // Load flow
        $flow = TenantConversationFlow::find($flowId);
        if (!$flow || !$flow->is_active) {
            throw new \Exception('Flow not found or inactive');
        }

        // Load context from cache
        $this->loadContext();

        // Add message to context
        $this->context['current_message'] = $message;
        $this->context['history'][] = ['role' => 'user', 'content' => $message];

        // Find current node or start from beginning
        $currentNodeId = $this->context['current_node'] ?? $flow->start_node_id;

        // Execute nodes until END or WAIT
        $response = $this->processNodes($flow->flow_data, $currentNodeId);

        // Save context
        $this->saveContext();

        return $response;
    }

    /**
     * Process nodes recursively
     */
    protected function processNodes(array $flowData, string $nodeId): array
    {
        $maxIterations = 20; // Prevent infinite loops
        $iterations = 0;
        $responses = [];

        while ($nodeId && $iterations < $maxIterations) {
            $iterations++;

            // Find node in flow data
            $node = $this->findNode($flowData, $nodeId);
            if (!$node) {
                Log::error("Node not found: {$nodeId}");
                break;
            }

            // Execute node
            $result = $this->executeNode($node);

            // Collect response
            if (!empty($result['response'])) {
                $responses[] = $result['response'];
            }

            // Check for flow control
            if ($result['action'] === 'end') {
                break;
            } elseif ($result['action'] === 'wait') {
                $this->context['current_node'] = $nodeId;
                break;
            } elseif ($result['action'] === 'continue') {
                // Find next node
                $nodeId = $result['next_node'] ?? $this->findNextNode($flowData, $nodeId);
            }
        }

        return [
            'success' => true,
            'responses' => $responses,
            'context' => $this->context,
        ];
    }

    /**
     * Execute single node
     */
    protected function executeNode(array $nodeData): array
    {
        $type = $nodeData['type'] ?? '';
        $handlerClass = $this->handlers[$type] ?? null;

        if (!$handlerClass || !class_exists($handlerClass)) {
            Log::error("Handler not found for node type: {$type}");
            return ['action' => 'continue'];
        }

        $handler = new $handlerClass();
        return $handler->execute($nodeData, $this->context);
    }

    /**
     * Find node by ID
     */
    protected function findNode(array $flowData, string $nodeId): ?array
    {
        foreach ($flowData['nodes'] ?? [] as $node) {
            if ($node['id'] === $nodeId) {
                return $node;
            }
        }
        return null;
    }

    /**
     * Find next node from edges
     */
    protected function findNextNode(array $flowData, string $currentNodeId): ?string
    {
        foreach ($flowData['edges'] ?? [] as $edge) {
            if ($edge['source'] === $currentNodeId) {
                return $edge['target'];
            }
        }
        return null;
    }

    /**
     * Load context from cache
     */
    protected function loadContext(): void
    {
        $cacheKey = "flow_context:{$this->sessionId}";
        $this->context = Cache::get($cacheKey, [
            'session_id' => $this->sessionId,
            'flow_id' => $this->flowId,
            'history' => [],
            'variables' => [],
            'current_node' => null,
        ]);
    }

    /**
     * Save context to cache
     */
    protected function saveContext(): void
    {
        $cacheKey = "flow_context:{$this->sessionId}";
        Cache::put($cacheKey, $this->context, now()->addHours(24));
    }
}
```

---

## 2. NODE HANDLER INTERFACE

### NodeHandlerInterface.php
```php
<?php
namespace App\Services\ConversationNodes\Contracts;

interface NodeHandlerInterface
{
    /**
     * Execute node logic
     *
     * @param array $nodeData Node configuration
     * @param array &$context Shared context (by reference)
     * @return array ['action' => 'continue|wait|end', 'response' => '', 'next_node' => '']
     */
    public function execute(array $nodeData, array &$context): array;
}
```

---

## 3. SAMPLE NODE HANDLERS

### AIResponseNode.php
```php
<?php
namespace App\Services\ConversationNodes\Handlers;

use App\Services\ConversationNodes\Contracts\NodeHandlerInterface;
use App\Services\OpenAIService;

class AIResponseNode implements NodeHandlerInterface
{
    public function execute(array $nodeData, array &$context): array
    {
        $config = $nodeData['config'] ?? [];
        $prompt = $config['prompt'] ?? 'Respond to the user message';

        // Build full prompt with context
        $fullPrompt = $this->buildPrompt($prompt, $context);

        // Call OpenAI
        $openai = app(OpenAIService::class);
        $response = $openai->generateResponse($fullPrompt, $context['history']);

        // Add to history
        $context['history'][] = ['role' => 'assistant', 'content' => $response];

        return [
            'action' => 'continue',
            'response' => $response,
        ];
    }

    protected function buildPrompt(string $basePrompt, array $context): string
    {
        $prompt = $basePrompt;

        // Add context variables
        if (!empty($context['variables']['category'])) {
            $prompt .= "\nDetected category: " . $context['variables']['category'];
        }

        if (!empty($context['variables']['products'])) {
            $prompt .= "\nRecommended products: " . implode(', ', $context['variables']['products']);
        }

        return $prompt;
    }
}
```

### CategoryDetectionNode.php
```php
<?php
namespace App\Services\ConversationNodes\Handlers;

use App\Services\ConversationNodes\Contracts\NodeHandlerInterface;

class CategoryDetectionNode implements NodeHandlerInterface
{
    protected array $categories = [
        'transpalet' => ['transpalet', 'palet', 'transpaletler'],
        'forklift' => ['forklift', 'fork lift', 'forkliftler'],
        'istif' => ['istif', 'istif makinesi', 'stacker'],
        'aks' => ['aks', 'aks araba', 'platform'],
    ];

    public function execute(array $nodeData, array &$context): array
    {
        $message = strtolower($context['current_message'] ?? '');
        $detectedCategory = null;

        // Simple keyword matching
        foreach ($this->categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($message, $keyword)) {
                    $detectedCategory = $category;
                    break 2;
                }
            }
        }

        // Store in context
        if ($detectedCategory) {
            $context['variables']['category'] = $detectedCategory;
            $context['variables']['category_confidence'] = 0.9;
        }

        return [
            'action' => 'continue',
            'response' => null, // Silent node
        ];
    }
}
```

### ConditionNode.php
```php
<?php
namespace App\Services\ConversationNodes\Handlers;

use App\Services\ConversationNodes\Contracts\NodeHandlerInterface;

class ConditionNode implements NodeHandlerInterface
{
    public function execute(array $nodeData, array &$context): array
    {
        $config = $nodeData['config'] ?? [];
        $conditionType = $config['condition_type'] ?? 'contains_keyword';

        $result = false;

        switch ($conditionType) {
            case 'contains_keyword':
                $keywords = $config['keywords'] ?? [];
                $message = strtolower($context['current_message'] ?? '');
                foreach ($keywords as $keyword) {
                    if (str_contains($message, strtolower($keyword))) {
                        $result = true;
                        break;
                    }
                }
                break;

            case 'has_variable':
                $varName = $config['variable_name'] ?? '';
                $result = !empty($context['variables'][$varName]);
                break;

            case 'expression':
                // Evaluate simple expressions
                $expression = $config['expression'] ?? '';
                $result = $this->evaluateExpression($expression, $context);
                break;
        }

        // Determine next node
        $nextNode = $result
            ? ($config['true_branch'] ?? null)
            : ($config['false_branch'] ?? null);

        return [
            'action' => 'continue',
            'response' => null,
            'next_node' => $nextNode,
        ];
    }

    protected function evaluateExpression(string $expression, array $context): bool
    {
        // Simple expression evaluator
        // Example: "category == 'transpalet'"
        // This is simplified - in production use a proper expression parser
        return false;
    }
}
```

### EndNode.php
```php
<?php
namespace App\Services\ConversationNodes\Handlers;

use App\Services\ConversationNodes\Contracts\NodeHandlerInterface;

class EndNode implements NodeHandlerInterface
{
    public function execute(array $nodeData, array &$context): array
    {
        $config = $nodeData['config'] ?? [];
        $message = $config['message'] ?? 'İyi günler dileriz!';

        // Clear context for next conversation
        $context['current_node'] = null;
        $context['variables'] = [];

        return [
            'action' => 'end',
            'response' => $message,
        ];
    }
}
```

---

## 4. CHAT CONTROLLER INTEGRATION

### ChatController.php
```php
// In your existing ChatController

use App\Services\ConversationNodes\NodeExecutor;

public function sendMessage(Request $request)
{
    $message = $request->input('message');
    $sessionId = session()->getId();

    // Check if workflow is enabled for this tenant
    $tenant = tenant();
    $flow = TenantConversationFlow::where('tenant_id', $tenant->id)
        ->where('is_active', true)
        ->orderBy('priority')
        ->first();

    if ($flow) {
        // Use workflow system
        $executor = new NodeExecutor();
        $result = $executor->executeFlow($flow->id, $message, $sessionId);

        $response = implode("\n\n", $result['responses']);
    } else {
        // Fallback to regular chat
        $response = $this->regularChatResponse($message);
    }

    return response()->json([
        'success' => true,
        'response' => $response,
    ]);
}
```

---

## 5. TEST IMPLEMENTATION

### Test Command
```bash
php artisan make:command TestWorkflow
```

```php
<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ConversationNodes\NodeExecutor;

class TestWorkflow extends Command
{
    protected $signature = 'workflow:test {flowId} {message}';
    protected $description = 'Test workflow execution';

    public function handle()
    {
        $flowId = $this->argument('flowId');
        $message = $this->argument('message');
        $sessionId = 'test-' . uniqid();

        $executor = new NodeExecutor();

        try {
            $result = $executor->executeFlow($flowId, $message, $sessionId);

            $this->info('=== RESPONSES ===');
            foreach ($result['responses'] as $response) {
                $this->line($response);
                $this->line('---');
            }

            $this->info('=== CONTEXT ===');
            $this->line(json_encode($result['context'], JSON_PRETTY_PRINT));

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
```

---

## KULLANIM

```bash
# Test workflow
php artisan workflow:test 1 "Merhaba, transpalet arıyorum"

# Expected output:
=== RESPONSES ===
Hoş geldiniz! Size transpalet konusunda yardımcı olabilirim.
---
İşte en çok tercih edilen transpaletlerimiz...
---
=== CONTEXT ===
{
    "session_id": "test-xxx",
    "flow_id": 1,
    "variables": {
        "category": "transpalet",
        "category_confidence": 0.9
    },
    "current_node": "node_3"
}
```