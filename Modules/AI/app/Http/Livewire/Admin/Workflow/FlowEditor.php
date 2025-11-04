<?php

namespace Modules\AI\App\Http\Livewire\Admin\Workflow;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\TenantConversationFlow;
use App\Models\AIWorkflowNode;
use App\Services\ConversationFlowEngine;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class FlowEditor extends Component
{
    public $flowId = null;
    public $flowName = '';
    public $flowDescription = '';
    public $flowData = [];
    public $availableNodes = [];
    public $nodesByCategory = [];
    public $currentTenantId = null;
    public $isActive = true;
    public $priority = 0;

    // Test Flow properties
    public $testMessage = '';
    public $testMessages = [];
    public $testSessionId = null;
    public $debugInfo = [];

    protected $rules = [
        'flowName' => 'required|string|max:100',
        'flowDescription' => 'nullable|string|max:500',
        'isActive' => 'boolean',
        'priority' => 'integer|min:0|max:999',
    ];

    public function mount($flowId = null)
    {
        $this->currentTenantId = tenant('id');

        if ($flowId) {
            $flow = TenantConversationFlow::where('tenant_id', $this->currentTenantId)
                ->findOrFail($flowId);

            $this->flowId = $flow->id;
            $this->flowName = $flow->flow_name;
            $this->flowDescription = $flow->flow_description;
            $this->flowData = $flow->flow_data;
            $this->isActive = $flow->is_active;
            $this->priority = $flow->priority;

            // Debug log
            \Log::info('FlowEditor mounted', [
                'flowId' => $this->flowId,
                'flowData type' => gettype($this->flowData),
                'flowData empty' => empty($this->flowData),
                'nodes count' => isset($this->flowData['nodes']) ? count($this->flowData['nodes']) : 0,
            ]);
        }

        // Node kütüphanesini DB'den al (kategori bazlı, tenant'a özel filtrelenmiş)
        $this->nodesByCategory = AIWorkflowNode::getByCategory($this->currentTenantId);
        $this->availableNodes = AIWorkflowNode::getForTenant($this->currentTenantId);
    }

    public function saveFlow()
    {
        $this->validate();

        // JavaScript'ten gelen drawflow data frontend'de yakalanacak
        // Bu method Livewire event ile tetiklenecek

        $this->dispatchBrowserEvent('save-flow-request');
    }

    public function saveFlowData($drawflowData)
    {
        $this->validate();

        // Drawflow JSON'unu Laravel formatına çevir
        $flowData = $this->convertDrawflowToFlowData($drawflowData);

        // Validate flow structure
        $validationErrors = $this->validateFlowStructure($flowData);
        if (!empty($validationErrors)) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => 'Flow validation failed: ' . implode(', ', $validationErrors),
            ]);
            return;
        }

        $flow = TenantConversationFlow::updateOrCreate(
            ['id' => $this->flowId],
            [
                'tenant_id' => tenant('id'),
                'flow_name' => $this->flowName,
                'flow_description' => $this->flowDescription,
                'flow_data' => $flowData,
                'start_node_id' => $this->findStartNodeId($flowData),
                'is_active' => $this->isActive,
                'priority' => $this->priority,
            ]
        );

        $this->flowId = $flow->id;

        // Clear flow cache
        \App\Services\ConversationFlowEngine::clearFlowCache(tenant('id'));

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => 'Flow saved successfully!',
        ]);

        // Redirect to flow list after save
        return redirect()->route('admin.ai.workflow.flows.index');
    }

    /**
     * Validate flow structure
     */
    protected function validateFlowStructure(array $flowData): array
    {
        $errors = [];

        // Check if flow has nodes
        if (empty($flowData['nodes'])) {
            $errors[] = 'Flow must have at least one node';
            return $errors;
        }

        // Check for start node (first node is considered start)
        $startNodeId = $this->findStartNodeId($flowData);
        if (!$startNodeId) {
            $errors[] = 'Flow must have a start node';
        }

        // Check for end node
        $hasEndNode = false;
        foreach ($flowData['nodes'] as $node) {
            if ($node['type'] === 'end') {
                $hasEndNode = true;
                break;
            }
        }
        if (!$hasEndNode) {
            $errors[] = 'Flow must have at least one end node';
        }

        // Check for orphan nodes (nodes with no incoming or outgoing connections)
        $connectedNodes = [];
        foreach ($flowData['edges'] as $edge) {
            $connectedNodes[$edge['source']] = true;
            $connectedNodes[$edge['target']] = true;
        }

        $orphanNodes = [];
        foreach ($flowData['nodes'] as $node) {
            // Start node doesn't need incoming connection
            // End node doesn't need outgoing connection
            if ($node['type'] !== 'end' && !isset($connectedNodes[$node['id']])) {
                $orphanNodes[] = $node['name'] ?? $node['id'];
            }
        }

        if (!empty($orphanNodes)) {
            $errors[] = 'Disconnected nodes found: ' . implode(', ', $orphanNodes);
        }

        // Check for circular dependencies (basic check)
        if ($this->hasCircularDependency($flowData)) {
            $errors[] = 'Flow contains circular dependencies';
        }

        // Validate required fields for each node
        foreach ($flowData['nodes'] as $node) {
            if (empty($node['type'])) {
                $errors[] = 'Node missing type: ' . ($node['name'] ?? $node['id']);
            }
            if (empty($node['name'])) {
                $errors[] = 'Node missing name: ' . $node['id'];
            }
        }

        return $errors;
    }

    /**
     * Check for circular dependencies
     */
    protected function hasCircularDependency(array $flowData): bool
    {
        // Build adjacency list
        $graph = [];
        foreach ($flowData['nodes'] as $node) {
            $graph[$node['id']] = [];
        }
        foreach ($flowData['edges'] as $edge) {
            $graph[$edge['source']][] = $edge['target'];
        }

        // DFS to detect cycle
        $visited = [];
        $recursionStack = [];

        foreach (array_keys($graph) as $nodeId) {
            if ($this->hasCycleDFS($nodeId, $graph, $visited, $recursionStack)) {
                return true;
            }
        }

        return false;
    }

    /**
     * DFS helper for cycle detection
     */
    protected function hasCycleDFS(string $nodeId, array $graph, array &$visited, array &$recursionStack): bool
    {
        if (isset($recursionStack[$nodeId])) {
            return true; // Cycle detected
        }

        if (isset($visited[$nodeId])) {
            return false; // Already visited, no cycle
        }

        $visited[$nodeId] = true;
        $recursionStack[$nodeId] = true;

        foreach ($graph[$nodeId] ?? [] as $neighbor) {
            if ($this->hasCycleDFS($neighbor, $graph, $visited, $recursionStack)) {
                return true;
            }
        }

        unset($recursionStack[$nodeId]);
        return false;
    }

    private function convertDrawflowToFlowData($drawflowData)
    {
        $nodes = [];
        $edges = [];

        if (!isset($drawflowData['drawflow']['Home']['data'])) {
            return ['nodes' => [], 'edges' => []];
        }

        $drawflowNodes = $drawflowData['drawflow']['Home']['data'];

        foreach ($drawflowNodes as $nodeId => $nodeData) {
            $nodes[] = [
                'id' => 'node_' . $nodeId,
                'type' => $nodeData['name'] ?? 'unknown',
                'name' => $nodeData['data']['label'] ?? $nodeData['name'] ?? 'Node',
                'class' => $nodeData['data']['class'] ?? '',
                'config' => $nodeData['data']['config'] ?? [],
                'position' => [
                    'x' => $nodeData['pos_x'] ?? 0,
                    'y' => $nodeData['pos_y'] ?? 0,
                ]
            ];

            // Extract connections as edges
            if (isset($nodeData['outputs'])) {
                foreach ($nodeData['outputs'] as $outputKey => $output) {
                    if (isset($output['connections'])) {
                        foreach ($output['connections'] as $connection) {
                            $edges[] = [
                                'id' => 'edge_' . $nodeId . '_' . $connection['node'],
                                'source' => 'node_' . $nodeId,
                                'target' => 'node_' . $connection['node'],
                            ];
                        }
                    }
                }
            }
        }

        return compact('nodes', 'edges');
    }

    private function findStartNodeId($flowData)
    {
        // İlk node'u start node olarak kabul et
        if (!empty($flowData['nodes'])) {
            return $flowData['nodes'][0]['id'];
        }
        return null;
    }

    /**
     * Send test message through the flow
     */
    public function sendTestMessage()
    {
        if (empty(trim($this->testMessage))) {
            return;
        }

        // Generate session ID if not exists
        if (!$this->testSessionId) {
            $this->testSessionId = 'test_' . Str::uuid();
        }

        // Add user message to chat
        $this->testMessages[] = [
            'role' => 'user',
            'content' => $this->testMessage,
            'timestamp' => now()->toISOString(),
        ];

        try {
            // Ensure flow is saved first
            if (!$this->flowId) {
                throw new \Exception('Flow must be saved before testing. Please save the flow first.');
            }

            // Use ConversationFlowEngine to process message
            $engine = app(ConversationFlowEngine::class);
            $result = $engine->processMessage(
                $this->testSessionId,
                $this->currentTenantId,
                $this->testMessage,
                null // No user ID for test
            );

            // Add assistant response to chat
            if ($result['success'] && !empty($result['response'])) {
                $this->testMessages[] = [
                    'role' => 'assistant',
                    'content' => $result['response'],
                    'timestamp' => now()->toISOString(),
                ];
            } elseif (!$result['success']) {
                $this->testMessages[] = [
                    'role' => 'system',
                    'content' => 'Error: ' . ($result['error'] ?? 'Unknown error'),
                    'timestamp' => now()->toISOString(),
                ];
            } else {
                $this->testMessages[] = [
                    'role' => 'assistant',
                    'content' => 'No response generated from flow.',
                    'timestamp' => now()->toISOString(),
                ];
            }

            // Update debug info
            $this->debugInfo = [
                'session_id' => $this->testSessionId,
                'flow_id' => $this->flowId,
                'tenant_id' => $this->currentTenantId,
                'current_node' => $result['current_node'] ?? null,
                'next_node' => $result['next_node'] ?? null,
                'context' => $result['context'] ?? [],
                'conversation_id' => $result['conversation_id'] ?? null,
                'execution_result' => $result,
            ];

        } catch (\Exception $e) {
            $this->testMessages[] = [
                'role' => 'system',
                'content' => 'Error: ' . $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];

            $this->debugInfo = [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];
        }

        // Clear input
        $this->testMessage = '';
    }

    /**
     * Reset test session
     */
    public function resetTestSession()
    {
        $this->testMessages = [];
        $this->testSessionId = null;
        $this->testMessage = '';
        $this->debugInfo = [];

        // Clear cache for this session
        if ($this->testSessionId) {
            \Cache::forget("flow_context:{$this->testSessionId}");
        }
    }

    public function render()
    {
        return view('ai::livewire.admin.workflow.flow-editor');
    }
}
