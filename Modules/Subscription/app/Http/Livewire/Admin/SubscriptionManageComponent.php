<?php

declare(strict_types=1);

namespace Modules\Subscription\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Subscription\App\Models\Subscription;
use Modules\Subscription\App\Models\SubscriptionPlan;
use App\Models\User;

#[Layout('admin.layout')]
class SubscriptionManageComponent extends Component
{
    public $subscriptionId;
    public $customer_id;
    public $plan_id;
    public $billing_cycle = 'monthly';
    public $status = 'active';
    public $has_trial = false;
    public $trial_days = 0;
    public $auto_renew = true;
    public $started_at;
    public $current_period_end;

    // Computed
    public $price_per_cycle = 0;

    public function boot()
    {
        view()->share('pretitle', $this->subscriptionId ? 'Abonelik Düzenle' : 'Yeni Abonelik');
    }

    public function mount($id = null)
    {
        $this->boot();
        $this->started_at = now()->format('Y-m-d');
        $this->current_period_end = now()->addMonth()->format('Y-m-d');

        if ($id) {
            $this->subscriptionId = (int) $id;
            $this->loadSubscription();
        }
    }

    protected function loadSubscription()
    {
        $subscription = Subscription::findOrFail($this->subscriptionId);

        $this->customer_id = $subscription->customer_id;
        $this->plan_id = $subscription->plan_id;
        $this->billing_cycle = $subscription->billing_cycle;
        $this->status = $subscription->status;
        $this->has_trial = (bool) $subscription->has_trial;
        $this->trial_days = (int) $subscription->trial_days;
        $this->auto_renew = (bool) $subscription->auto_renew;
        $this->price_per_cycle = (float) $subscription->price_per_cycle;
        $this->started_at = $subscription->started_at?->format('Y-m-d');
        $this->current_period_end = $subscription->current_period_end?->format('Y-m-d');
    }

    public function updatedPlanId()
    {
        if ($this->plan_id) {
            $plan = SubscriptionPlan::find($this->plan_id);
            if ($plan) {
                $this->price_per_cycle = $this->billing_cycle === 'monthly'
                    ? (float) $plan->price_monthly
                    : (float) $plan->price_yearly;
            }
        }
    }

    public function updatedBillingCycle()
    {
        $this->updatedPlanId();

        // Update period end based on cycle
        if ($this->started_at) {
            $start = \Carbon\Carbon::parse($this->started_at);
            $this->current_period_end = $this->billing_cycle === 'monthly'
                ? $start->addMonth()->format('Y-m-d')
                : $start->addYear()->format('Y-m-d');
        }
    }

    protected function rules()
    {
        return [
            'customer_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:subscription_plans,subscription_plan_id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'status' => 'required|in:active,trial,paused,cancelled,expired,pending_payment',
            'has_trial' => 'boolean',
            'trial_days' => 'nullable|integer|min:0',
            'auto_renew' => 'boolean',
            'started_at' => 'required|date',
            'current_period_end' => 'required|date|after:started_at',
        ];
    }

    public function save()
    {
        $this->validate();

        $plan = SubscriptionPlan::findOrFail($this->plan_id);

        $data = [
            'customer_id' => $this->customer_id,
            'plan_id' => $plan->subscription_plan_id,
            'status' => $this->status,
            'billing_cycle' => $this->billing_cycle,
            'price_per_cycle' => $this->price_per_cycle,
            'currency' => '₺',
            'has_trial' => $this->has_trial,
            'trial_days' => $this->has_trial ? $this->trial_days : 0,
            'started_at' => $this->started_at,
            'current_period_start' => $this->started_at,
            'current_period_end' => $this->current_period_end,
            'next_billing_date' => $this->current_period_end,
            'auto_renew' => $this->auto_renew,
            'billing_cycles_completed' => 0,
            'total_paid' => 0,
        ];

        try {
            if ($this->subscriptionId) {
                $subscription = Subscription::findOrFail($this->subscriptionId);
                $subscription->update($data);
                $message = 'Abonelik başarıyla güncellendi';
            } else {
                Subscription::create($data);
                $message = 'Abonelik başarıyla oluşturuldu';
            }

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => $message,
                'type' => 'success'
            ]);

            return redirect()->route('admin.subscription.index');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'İşlem başarısız: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $plans = SubscriptionPlan::active()->ordered()->get();

        return view('subscription::admin.livewire.subscription-manage-component', [
            'users' => $users,
            'plans' => $plans,
        ]);
    }
}
