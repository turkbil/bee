<?php

declare(strict_types=1);

namespace Modules\Coupon\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Computed};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Coupon\App\Models\Coupon;

#[Layout('admin.layout')]
class CouponComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterType = '';
    public $perPage = 25;

    // Bulk actions
    public $selectedItems = [];
    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterType' => ['except' => ''],
    ];

    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = $this->coupons->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function coupons()
    {
        $query = Coupon::query();

        // Search
        if (!empty($this->search)) {
            $query->where('code', 'like', "%{$this->search}%");
        }

        // Filter by status
        if (!empty($this->filterStatus)) {
            switch ($this->filterStatus) {
                case 'active':
                    $query->active()
                        ->where(function ($q) {
                            $q->whereNull('valid_until')
                              ->orWhere('valid_until', '>', now());
                        })
                        ->where(function ($q) {
                            $q->whereNull('usage_limit_total')
                              ->orWhereRaw('used_count < usage_limit_total');
                        });
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'expired':
                    $query->where('valid_until', '<=', now());
                    break;
                case 'limit_reached':
                    $query->whereNotNull('usage_limit_total')
                        ->whereRaw('used_count >= usage_limit_total');
                    break;
            }
        }

        // Filter by type
        if (!empty($this->filterType)) {
            $query->where('coupon_type', $this->filterType);
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    #[Computed]
    public function stats()
    {
        return [
            'total' => Coupon::count(),
            'active' => Coupon::valid()->count(),
            'total_usage' => Coupon::sum('used_count'),
        ];
    }

    public function toggleStatus(int $id)
    {
        try {
            $coupon = Coupon::findOrFail($id);
            $coupon->update(['is_active' => !$coupon->is_active]);

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
            $coupon = Coupon::findOrFail($id);
            $coupon->delete();

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
        Coupon::whereIn('id', $this->selectedItems)
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
        Coupon::whereIn('id', $this->selectedItems)
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
        Coupon::whereIn('id', $this->selectedItems)->delete();

        $count = count($this->selectedItems);
        $this->selectedItems = [];
        $this->selectAll = false;

        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => __('admin.bulk_deleted', ['count' => $count]),
            'type' => 'success'
        ]);
    }

    public function render()
    {
        return view('coupon::admin.livewire.coupon-component', [
            'coupons' => $this->coupons,
            'stats' => $this->stats,
        ]);
    }
}
