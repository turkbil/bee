<?php

namespace Modules\AI\App\Http\Livewire\Admin\Workflow;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\AIWorkflowNode;

#[Layout('admin.layout')]
class NodeLibrary extends Component
{
    public $nodes = [];
    public $nodesByCategory = [];
    public $search = '';
    public $filterCategory = 'all';
    public $filterGlobal = 'all';

    public function mount()
    {
        $this->loadNodes();
    }

    public function updatedSearch()
    {
        $this->loadNodes();
    }

    public function updatedFilterCategory()
    {
        $this->loadNodes();
    }

    public function updatedFilterGlobal()
    {
        $this->loadNodes();
    }

    private function loadNodes()
    {
        $nodes = collect();

        // 1. Get global nodes from CENTRAL DB
        try {
            $centralQuery = \DB::connection('mysql')->table('ai_workflow_nodes')
                ->where('is_active', true)
                ->where('is_global', true);

            if ($this->search) {
                $centralQuery->where(function ($q) {
                    $q->where('node_key', 'like', '%' . $this->search . '%')
                      ->orWhere('node_class', 'like', '%' . $this->search . '%')
                      ->orWhereRaw("JSON_EXTRACT(node_name, '$.en') LIKE ?", ['%' . $this->search . '%'])
                      ->orWhereRaw("JSON_EXTRACT(node_name, '$.tr') LIKE ?", ['%' . $this->search . '%']);
                });
            }

            if ($this->filterCategory !== 'all') {
                $centralQuery->where('category', $this->filterCategory);
            }

            if ($this->filterGlobal !== 'tenant') {
                $centralNodes = $centralQuery->orderBy('category')->orderBy('order')->get();

                foreach ($centralNodes as $node) {
                    $nodes->push((object)[
                        'id' => $node->id,
                        'node_key' => $node->node_key,
                        'node_class' => $node->node_class,
                        'node_name' => json_decode($node->node_name, true),
                        'node_description' => json_decode($node->node_description, true),
                        'category' => $node->category,
                        'icon' => $node->icon,
                        'order' => $node->order,
                        'is_global' => true,
                        'is_active' => $node->is_active,
                        'tenant_whitelist' => json_decode($node->tenant_whitelist ?? 'null', true),
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to fetch global nodes', ['error' => $e->getMessage()]);
        }

        // 2. Get tenant-specific nodes from TENANT DB
        if ($this->filterGlobal !== 'global' && tenancy()->initialized) {
            try {
                $tenantQuery = AIWorkflowNode::query()
                    ->where('is_active', true)
                    ->where('is_global', false);

                if ($this->search) {
                    $tenantQuery->where(function ($q) {
                        $q->where('node_key', 'like', '%' . $this->search . '%')
                          ->orWhere('node_class', 'like', '%' . $this->search . '%')
                          ->orWhereRaw("JSON_EXTRACT(node_name, '$.en') LIKE ?", ['%' . $this->search . '%'])
                          ->orWhereRaw("JSON_EXTRACT(node_name, '$.tr') LIKE ?", ['%' . $this->search . '%']);
                    });
                }

                if ($this->filterCategory !== 'all') {
                    $tenantQuery->where('category', $this->filterCategory);
                }

                $tenantNodes = $tenantQuery->orderBy('category')->orderBy('order')->get();
                $nodes = $nodes->merge($tenantNodes);
            } catch (\Exception $e) {
                \Log::error('Failed to fetch tenant nodes', ['error' => $e->getMessage()]);
            }
        }

        $this->nodes = $nodes;
        $this->nodesByCategory = $nodes->groupBy('category');
    }

    public function toggleStatus($nodeId)
    {
        $node = AIWorkflowNode::find($nodeId);
        if ($node) {
            $node->is_active = !$node->is_active;
            $node->save();

            $this->loadNodes();

            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => 'Node status updated!',
            ]);
        }
    }

    public function render()
    {
        return view('ai::livewire.admin.workflow.node-library');
    }
}
