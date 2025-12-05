<?php

declare(strict_types=1);

namespace Modules\Subscription\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Computed};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Subscription\App\Models\SubscriptionPlan;
use Modules\LanguageManagement\App\Models\TenantLanguage;

#[Layout('admin.layout')]
class SubscriptionPlanComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterFeatured = '';
    public $perPage = 25;

    // Bulk actions
    public $selectedItems = [];
    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterFeatured' => ['except' => ''],
    ];

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'updateOrder' => 'updateOrder',
    ];

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = $this->plans->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function plans()
    {
        $query = SubscriptionPlan::query()
            ->withCount('subscriptions');

        // Search
        if (!empty($this->search)) {
            $search = strtolower($this->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr'))) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.en'))) LIKE ?", ["%{$search}%"])
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus === 'active');
        }

        // Filter by featured
        if ($this->filterFeatured !== '') {
            $query->where('is_featured', $this->filterFeatured === 'yes');
        }

        return $query->orderBy('sort_order', 'asc')->get();
    }

    public function updateOrder($list)
    {
        try {
            if (!is_array($list) || empty($list)) {
                return;
            }

            foreach ($list as $item) {
                if (isset($item['id']) && isset($item['order'])) {
                    SubscriptionPlan::where('subscription_plan_id', $item['id'])
                        ->update(['sort_order' => $item['order']]);
                }
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('subscription::admin.plans.order_updated'),
                'type' => 'success'
            ]);

            $this->dispatch('refresh-sortable');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ]);
        }
    }

    public function toggleStatus(int $id)
    {
        try {
            $plan = SubscriptionPlan::findOrFail($id);
            $plan->update(['is_active' => !$plan->is_active]);

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('admin.status_updated'),
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ]);
        }
    }

    public function toggleFeatured(int $id)
    {
        try {
            $plan = SubscriptionPlan::findOrFail($id);
            $plan->update(['is_featured' => !$plan->is_featured]);

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('admin.status_updated'),
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ]);
        }
    }

    public function delete(int $id)
    {
        try {
            $plan = SubscriptionPlan::findOrFail($id);

            if ($plan->subscriptions()->exists()) {
                $this->dispatch('toast', [
                    'title' => __('admin.error'),
                    'message' => __('subscription::admin.plans.has_subscriptions'),
                    'type' => 'error'
                ]);
                return;
            }

            $plan->delete();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('admin.deleted_successfully'),
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ]);
        }
    }

    // Bulk Actions
    public function bulkActivate()
    {
        SubscriptionPlan::whereIn('id', $this->selectedItems)
            ->update(['is_active' => true]);

        $this->selectedItems = [];
        $this->selectAll = false;

        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => __('admin.bulk_activated'),
            'type' => 'success'
        ]);
    }

    public function bulkDeactivate()
    {
        SubscriptionPlan::whereIn('id', $this->selectedItems)
            ->update(['is_active' => false]);

        $this->selectedItems = [];
        $this->selectAll = false;

        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => __('admin.bulk_deactivated'),
            'type' => 'success'
        ]);
    }

    public function bulkDelete()
    {
        $plans = SubscriptionPlan::whereIn('id', $this->selectedItems)
            ->withCount('subscriptions')
            ->get();

        $deleted = 0;
        $failed = 0;

        foreach ($plans as $plan) {
            if ($plan->subscriptions_count == 0) {
                $plan->delete();
                $deleted++;
            } else {
                $failed++;
            }
        }

        $this->selectedItems = [];
        $this->selectAll = false;

        if ($failed > 0) {
            $this->dispatch('toast', [
                'title' => __('admin.warning'),
                'message' => __('subscription::admin.plans.bulk_delete_partial', ['deleted' => $deleted, 'failed' => $failed]),
                'type' => 'warning'
            ]);
        } else {
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('admin.bulk_deleted', ['count' => $deleted]),
                'type' => 'success'
            ]);
        }
    }

    public function render()
    {
        return view('subscription::admin.livewire.subscription-plan-component', [
            'plans' => $this->plans
        ]);
    }
}
