<?php

namespace Modules\AI\App\Http\Livewire\Admin\Workflow;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\TenantConversationFlow;
use App\Services\ConversationNodes\NodeExecutor;
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
        }

        // Node kütüphanesini kategoriye göre gruplu al
        $this->nodesByCategory = NodeExecutor::getNodesByCategory();
        $this->availableNodes = NodeExecutor::getAvailableNodes();

        // Tenant-specific node'ları filtrele (sadece bu tenant'a ait olanlar)
        $this->filterNodesByTenant();
    }

    private function filterNodesByTenant()
    {
        // Global nodes (Common) - Her tenant görebilir
        // Tenant-specific nodes - Sadece ilgili tenant görebilir

        $filteredCategories = [];

        foreach ($this->nodesByCategory as $category => $nodes) {
            $filteredNodes = array_filter($nodes, function($node) {
                // Common node'lar herkese açık
                if (str_contains($node['class'], '\\Common\\')) {
                    return true;
                }

                // Tenant-specific node kontrolü
                if (str_contains($node['class'], '\\TenantSpecific\\')) {
                    // Tenant_2 için node mu? (İxtif.com)
                    if (str_contains($node['class'], 'Tenant_2') && $this->currentTenantId == 2) {
                        return true;
                    }
                    // Tenant_1 için node mu? (tenant_a)
                    if (str_contains($node['class'], 'Tenant_1') && $this->currentTenantId == 1) {
                        return true;
                    }
                    return false;
                }

                return true;
            });

            if (!empty($filteredNodes)) {
                $filteredCategories[$category] = array_values($filteredNodes);
            }
        }

        $this->nodesByCategory = $filteredCategories;
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

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => 'Flow saved successfully!',
        ]);

        // Redirect to flow list after save
        return redirect()->route('admin.ai.workflow.flows.index');
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

    public function render()
    {
        return view('ai::livewire.admin.workflow.flow-editor');
    }
}
