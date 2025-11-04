<?php

namespace Modules\AI\App\Http\Livewire\Admin\Workflow;

use Livewire\Component;
use App\Models\TenantConversationFlow;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class FlowList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'all'; // all, active, inactive

    protected $queryString = ['search', 'filterStatus'];

    public function render()
    {
        $tenantId = tenant('id');

        $flows = TenantConversationFlow::where('tenant_id', $tenantId)
            ->when($this->search, function($query) {
                $query->where('flow_name', 'like', '%' . $this->search . '%')
                    ->orWhere('flow_description', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus !== 'all', function($query) {
                $query->where('is_active', $this->filterStatus === 'active');
            })
            ->orderBy('priority', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('ai::livewire.admin.workflow.flow-list', [
            'flows' => $flows,
        ]);
    }

    public function toggleStatus($flowId)
    {
        $flow = TenantConversationFlow::find($flowId);

        if ($flow && $flow->tenant_id == tenant('id')) {
            $flow->update([
                'is_active' => !$flow->is_active,
            ]);

            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => 'Flow status updated!',
            ]);
        }
    }

    public function deleteFlow($flowId)
    {
        $flow = TenantConversationFlow::find($flowId);

        if ($flow && $flow->tenant_id == tenant('id')) {
            $flow->delete();

            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => 'Flow deleted successfully!',
            ]);
        }
    }

    public function duplicateFlow($flowId)
    {
        $flow = TenantConversationFlow::find($flowId);

        if ($flow && $flow->tenant_id == tenant('id')) {
            $newFlow = $flow->replicate();
            $newFlow->flow_name = $flow->flow_name . ' (Copy)';
            $newFlow->is_active = false;
            $newFlow->priority = 99;
            $newFlow->save();

            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => 'Flow duplicated successfully!',
            ]);
        }
    }
}
