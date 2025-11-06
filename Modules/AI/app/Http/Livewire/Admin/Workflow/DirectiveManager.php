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

        $query = AITenantDirective::where('tenant_id', $tenantId)
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('directive_key', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('directive_value', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterCategory !== 'all', function($query) {
                $query->where('category', $this->filterCategory);
            });

        // Get all directives for grouping
        $allDirectives = $query->orderBy('category', 'asc')
            ->orderBy('directive_key', 'asc')
            ->get();

        // Group by category
        $groupedDirectives = $allDirectives->groupBy('category');

        // Category counts
        $categories = AITenantDirective::where('tenant_id', $tenantId)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get()
            ->pluck('count', 'category');

        // Category meta info
        $categoryMeta = [
            'ai_config' => [
                'title' => 'AI Configuration',
                'icon' => 'fa-brain',
                'color' => 'azure',
                'description' => 'Core AI behavior and model settings'
            ],
            'chat' => [
                'title' => 'Chat Settings',
                'icon' => 'fa-comments',
                'color' => 'blue',
                'description' => 'Chat interface and messaging configuration'
            ],
            'general' => [
                'title' => 'General',
                'icon' => 'fa-cog',
                'color' => 'secondary',
                'description' => 'General system directives'
            ],
            'behavior' => [
                'title' => 'Behavior',
                'icon' => 'fa-lightbulb',
                'color' => 'yellow',
                'description' => 'AI behavior patterns'
            ],
            'display' => [
                'title' => 'Display',
                'icon' => 'fa-eye',
                'color' => 'purple',
                'description' => 'Visual display settings'
            ],
            'pricing' => [
                'title' => 'Pricing',
                'icon' => 'fa-dollar-sign',
                'color' => 'green',
                'description' => 'Price display and negotiation'
            ],
            'lead' => [
                'title' => 'Lead Collection',
                'icon' => 'fa-user-plus',
                'color' => 'orange',
                'description' => 'Lead capture directives'
            ],
            'contact' => [
                'title' => 'Contact',
                'icon' => 'fa-phone',
                'color' => 'cyan',
                'description' => 'Contact information display'
            ],
        ];

        return view('ai::livewire.admin.workflow.directive-manager', [
            'groupedDirectives' => $groupedDirectives,
            'categories' => $categories,
            'categoryMeta' => $categoryMeta,
            'totalCount' => $allDirectives->count(),
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
