<?php

namespace Modules\AI\App\Http\Livewire\Admin\Workflow;

use Livewire\Component;
use App\Models\AITenantDirective;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class DirectiveManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCategory = 'all';

    // Edit modal
    public $editingDirective = null;
    public $directiveKey = '';
    public $directiveValue = '';
    public $directiveType = 'string';
    public $directiveCategory = 'general';
    public $directiveDescription = '';
    public $isActive = true;

    // New directive modal
    public $showNewModal = false;

    protected $queryString = ['search', 'filterCategory'];

    protected $rules = [
        'directiveKey' => 'required|string|max:100',
        'directiveValue' => 'required',
        'directiveType' => 'required|in:string,integer,boolean,json,array',
        'directiveCategory' => 'required|string|max:50',
        'directiveDescription' => 'nullable|string|max:500',
        'isActive' => 'boolean',
    ];

    public function render()
    {
        $tenantId = tenant('id');

        $directives = AITenantDirective::where('tenant_id', $tenantId)
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('directive_key', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('directive_value', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterCategory !== 'all', function($query) {
                $query->where('category', $this->filterCategory);
            })
            ->orderBy('category', 'asc')
            ->orderBy('directive_key', 'asc')
            ->paginate(20);

        // Category counts
        $categories = AITenantDirective::where('tenant_id', $tenantId)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get()
            ->pluck('count', 'category');

        return view('ai::livewire.admin.workflow.directive-manager', [
            'directives' => $directives,
            'categories' => $categories,
        ]);
    }

    public function openNewModal()
    {
        $this->resetForm();
        $this->showNewModal = true;
    }

    public function closeNewModal()
    {
        $this->showNewModal = false;
        $this->resetForm();
    }

    public function editDirective($directiveId)
    {
        $directive = AITenantDirective::find($directiveId);

        if ($directive && $directive->tenant_id == tenant('id')) {
            $this->editingDirective = $directive->id;
            $this->directiveKey = $directive->directive_key;
            $this->directiveValue = $directive->directive_value;
            $this->directiveType = $directive->directive_type;
            $this->directiveCategory = $directive->category;
            $this->directiveDescription = $directive->description ?? '';
            $this->isActive = $directive->is_active;
        }
    }

    public function cancelEdit()
    {
        $this->editingDirective = null;
        $this->resetForm();
    }

    public function saveDirective()
    {
        $this->validate();

        $tenantId = tenant('id');

        if ($this->editingDirective) {
            // Update existing
            $directive = AITenantDirective::find($this->editingDirective);

            if ($directive && $directive->tenant_id == $tenantId) {
                $directive->update([
                    'directive_value' => $this->directiveValue,
                    'directive_type' => $this->directiveType,
                    'category' => $this->directiveCategory,
                    'description' => $this->directiveDescription,
                    'is_active' => $this->isActive,
                ]);

                $this->dispatch('alert', [
                    'type' => 'success',
                    'message' => 'Directive updated successfully!',
                ]);

                $this->cancelEdit();
            }
        } else {
            // Create new
            AITenantDirective::create([
                'tenant_id' => $tenantId,
                'directive_key' => $this->directiveKey,
                'directive_value' => $this->directiveValue,
                'directive_type' => $this->directiveType,
                'category' => $this->directiveCategory,
                'description' => $this->directiveDescription,
                'is_active' => $this->isActive,
            ]);

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Directive created successfully!',
            ]);

            $this->closeNewModal();
        }

        // Clear cache
        AITenantDirective::clearCache($tenantId);
    }

    public function toggleStatus($directiveId)
    {
        $directive = AITenantDirective::find($directiveId);

        if ($directive && $directive->tenant_id == tenant('id')) {
            $directive->update([
                'is_active' => !$directive->is_active,
            ]);

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Directive status updated!',
            ]);

            // Clear cache
            AITenantDirective::clearCache(tenant('id'));
        }
    }

    public function deleteDirective($directiveId)
    {
        $directive = AITenantDirective::find($directiveId);

        if ($directive && $directive->tenant_id == tenant('id')) {
            $directive->delete();

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Directive deleted successfully!',
            ]);

            // Clear cache
            AITenantDirective::clearCache(tenant('id'));
        }
    }

    protected function resetForm()
    {
        $this->editingDirective = null;
        $this->directiveKey = '';
        $this->directiveValue = '';
        $this->directiveType = 'string';
        $this->directiveCategory = 'general';
        $this->directiveDescription = '';
        $this->isActive = true;
        $this->resetErrorBag();
    }
}
