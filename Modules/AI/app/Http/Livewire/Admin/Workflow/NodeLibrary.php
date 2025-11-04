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
        $query = AIWorkflowNode::query()->where('is_active', true);

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('node_key', 'like', '%' . $this->search . '%')
                  ->orWhere('node_class', 'like', '%' . $this->search . '%')
                  ->orWhereRaw("JSON_EXTRACT(node_name, '$.en') LIKE ?", ['%' . $this->search . '%'])
                  ->orWhereRaw("JSON_EXTRACT(node_name, '$.tr') LIKE ?", ['%' . $this->search . '%']);
            });
        }

        // Category filter
        if ($this->filterCategory !== 'all') {
            $query->where('category', $this->filterCategory);
        }

        // Global filter
        if ($this->filterGlobal === 'global') {
            $query->where('is_global', true);
        } elseif ($this->filterGlobal === 'tenant') {
            $query->where('is_global', false);
        }

        $this->nodes = $query->orderBy('category')->orderBy('order')->get();

        // Group by category
        $this->nodesByCategory = $this->nodes->groupBy('category');
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
