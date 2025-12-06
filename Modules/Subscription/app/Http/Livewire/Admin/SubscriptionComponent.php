<?php

declare(strict_types=1);

namespace Modules\Subscription\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Computed};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Subscription\App\Models\Subscription;
use Modules\Subscription\App\Models\SubscriptionPlan;

#[Layout('admin.layout')]
class SubscriptionComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterPlan = '';
    public $filterCycle = '';
    public $perPage = 25;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterPlan' => ['except' => ''],
        'filterCycle' => ['except' => ''],
    ];

    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function subscriptions()
    {
        $query = Subscription::query()
            ->with(['customer', 'plan']);

        // Search
        if (!empty($this->search)) {
            $search = strtolower($this->search);
            $query->where(function($q) use ($search) {
                $q->where('subscription_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        // Filter by plan
        if (!empty($this->filterPlan)) {
            $query->where('subscription_plan_id', $this->filterPlan);
        }

        // Filter by billing cycle
        if (!empty($this->filterCycle)) {
            $query->where('billing_cycle', $this->filterCycle);
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    #[Computed]
    public function plans()
    {
        return SubscriptionPlan::orderBy('sort_order')->get();
    }

    #[Computed]
    public function stats()
    {
        // Trial: has_trial=true VE trial_ends_at gelecekte (status ne olursa olsun)
        $trialCount = Subscription::where('has_trial', true)
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now())
            ->count();

        // Active: Premium (trial olmayan aktif) subscriptions
        $activeCount = Subscription::where('status', 'active')
            ->where(function($q) {
                $q->whereNull('current_period_end')
                  ->orWhere('current_period_end', '>', now());
            })
            ->where(function($q) {
                $q->where('has_trial', false)
                  ->orWhereNull('has_trial')
                  ->orWhere('trial_ends_at', '<=', now())
                  ->orWhereNull('trial_ends_at');
            })
            ->count();

        // Expired: Süresi dolmuş (status expired veya period_end geçmiş)
        $expiredCount = Subscription::where(function($q) {
            $q->where('status', 'expired')
              ->orWhere(function($q2) {
                  $q2->whereNotNull('current_period_end')
                     ->where('current_period_end', '<=', now())
                     ->where('status', '!=', 'cancelled');
              });
        })->count();

        // Cancelled: İptal edilmiş
        $cancelledCount = Subscription::cancelled()->count();

        return [
            'active' => $activeCount,
            'trial' => $trialCount,
            'expired' => $expiredCount,
            'cancelled' => $cancelledCount,
        ];
    }

    public function cancel(int $id)
    {
        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->cancel();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('subscription::admin.subscriptions.cancelled'),
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

    public function render()
    {
        return view('subscription::admin.livewire.subscription-component', [
            'subscriptions' => $this->subscriptions,
            'plans' => $this->plans,
            'stats' => $this->stats,
        ]);
    }
}
